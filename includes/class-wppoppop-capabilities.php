<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Capabilities & Meta-Capability Mapping Engine
 * Integrates WordPress core role permissions and dynamic capability evaluation.
 */
class WpPopPop_Capabilities {

    public function __construct() {
        add_action('init', [$this, 'register_role_capabilities']);
        add_filter('map_meta_cap', [$this, 'map_meta_caps'], 10, 4);
    }

    /**
     * Map meta capabilities to primitive capabilities dynamically.
     *
     * @param array  $caps    Primitive capabilities required.
     * @param string $cap     Meta capability being checked.
     * @param int    $user_id Current user ID.
     * @param array  $args    Additional contextual arguments (e.g., campaign UID).
     * @return array
     */
    public function map_meta_caps($caps, $cap, $user_id, $args) {
        // 1. Strict Guard Clause: Ignore any capability not managed by WpPopPop
        $handled_meta_caps = [
            'read_wppoppop_campaign',
            'edit_wppoppop_campaign',
            'publish_wppoppop_campaign',
            'delete_wppoppop_campaign',
            'purge_wppoppop_campaign',
        ];

        if (!in_array($cap, $handled_meta_caps, true)) {
            return $caps;
        }

        // 2. Direct Pre-Computed Capability Check (Prevents Infinite Recursion)
        // Accessing $user->allcaps directly does not trigger map_meta_cap filters.
        $user = get_userdata($user_id);
        if ($user && !empty($user->allcaps['manage_options'])) {
            return ['exist'];
        }

        // 3. Resolve Meta Capabilities to Primitive Capabilities
        switch ($cap) {
            case 'read_wppoppop_campaign':
            case 'edit_wppoppop_campaign':
                $caps = ['edit_wppoppop_campaigns'];
                $uid  = !empty($args[0]) ? $args[0] : '';
                if ($uid) {
                    $campaign = $this->get_campaign($uid);
                    if ($campaign && isset($campaign->author_id) && (int) $campaign->author_id !== (int) $user_id) {
                        $caps[] = 'edit_others_wppoppop_campaigns';
                    }
                }
                break;

            case 'publish_wppoppop_campaign':
                $caps = ['publish_wppoppop_campaigns'];
                break;

            case 'delete_wppoppop_campaign':
                $caps = ['delete_wppoppop_campaigns'];
                $uid  = !empty($args[0]) ? $args[0] : '';
                if ($uid) {
                    $campaign = $this->get_campaign($uid);
                    if ($campaign && isset($campaign->author_id) && (int) $campaign->author_id !== (int) $user_id) {
                        $caps[] = 'delete_others_wppoppop_campaigns';
                    }
                    if ($campaign && in_array($campaign->status, ['publish', 'published'], true)) {
                        $caps[] = 'delete_published_wppoppop_campaigns';
                    }
                }
                break;

            case 'purge_wppoppop_campaign':
                $caps = ['purge_wppoppop_campaigns'];
                break;
        }

        return $caps;
    }

    /**
     * Assign primitive capabilities to standard WordPress roles.
     */
    public function register_role_capabilities() {
        // 1. Administrator: Unrestricted operational and administrative authority
        $admin = get_role('administrator');
        if ($admin) {
            $admin_caps = [
                'manage_wppoppop',
                'edit_wppoppop_campaigns',
                'edit_others_wppoppop_campaigns',
                'publish_wppoppop_campaigns',
                'delete_wppoppop_campaigns',
                'delete_others_wppoppop_campaigns',
                'delete_published_wppoppop_campaigns',
                'purge_wppoppop_campaigns',
                'read_wppoppop_submissions',
            ];
            foreach ($admin_caps as $cap) {
                if (!$admin->has_cap($cap)) {
                    $admin->add_cap($cap);
                }
            }
        }

        // 2. Editor: Full campaign lifecycle management and lead inspection
        $editor = get_role('editor');
        if ($editor) {
            $editor_caps = [
                'edit_wppoppop_campaigns',
                'edit_others_wppoppop_campaigns',
                'publish_wppoppop_campaigns',
                'delete_wppoppop_campaigns',
                'delete_others_wppoppop_campaigns',
                'delete_published_wppoppop_campaigns',
                'read_wppoppop_submissions',
            ];
            foreach ($editor_caps as $cap) {
                if (!$editor->has_cap($cap)) {
                    $editor->add_cap($cap);
                }
            }
        }

        // 3. Author: Campaign creation, editing own campaigns, and draft deletion
        $author = get_role('author');
        if ($author) {
            $author_caps = [
                'edit_wppoppop_campaigns',
                'publish_wppoppop_campaigns',
                'delete_wppoppop_campaigns',
            ];
            foreach ($author_caps as $cap) {
                if (!$author->has_cap($cap)) {
                    $author->add_cap($cap);
                }
            }
        }
    }

    /**
     * Fetch campaign record from custom items table.
     */
    protected function get_campaign($uid_or_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        if (is_numeric($uid_or_id)) {
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", (int) $uid_or_id));
        }
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE uid = %s", sanitize_text_field($uid_or_id)));
    }
}
