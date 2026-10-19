<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('WpPopPop_Installer')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-installer.php';
}

class WpPopPop_List_Table extends WP_List_Table {
    protected $entry_counts = null;

    public function __construct() {
        parent::__construct([
            'singular' => __('Campaign', 'wppoppop'),
            'plural'   => __('Campaigns', 'wppoppop'),
            'ajax'     => false,
        ]);
    }

    public function get_columns() {
        return [
            'cb'        => '<input type="checkbox" />',
            'title'     => __('Name', 'wppoppop'),
            'shortcode' => '',
            'entries'   => __('Entries', 'wppoppop'),
            'active'    => __('Active', 'wppoppop'),
            'modified'  => __('Last modified', 'wppoppop'),
        ];
    }

    protected function get_sortable_columns() {
        return [
            'title'     => ['title', false],
            'shortcode' => ['uid', false],
            'entries'   => ['submissions', false],
            'active'    => ['status', false],
            'modified'  => ['updated_at', true],
        ];
    }

    public function get_bulk_actions() {
        $actions = [];
        $status = isset($_REQUEST['status']) ? sanitize_key($_REQUEST['status']) : 'all';

        if ($status === 'trash') {
            if (current_user_can('edit_wppoppop_campaigns')) {
                $actions['untrash'] = __('Restore', 'wppoppop');
            }
            if (current_user_can('purge_wppoppop_campaigns')) {
                $actions['delete'] = __('Delete Permanently', 'wppoppop');
            }
            return $actions;
        }

        if (current_user_can('delete_wppoppop_campaigns')) {
            $actions['trash'] = __('Move to Trash', 'wppoppop');
        }
        if (current_user_can('publish_wppoppop_campaigns')) {
            $actions['publish'] = __('Set Status: Published', 'wppoppop');
            $actions['draft']   = __('Set Status: Draft', 'wppoppop');
        }
        if (current_user_can('edit_wppoppop_campaigns')) {
            $actions['duplicate'] = __('Duplicate Selected', 'wppoppop');
            $actions['export']    = __('Export Selected (JSON)', 'wppoppop');
        }

        return $actions;
    }

    protected function get_views() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $rows = $wpdb->get_results("SELECT status FROM {$table_name}");

        $all = 0; $active = 0; $inactive = 0; $trash = 0;
        foreach ((array) $rows as $row) {
            if ($row->status === 'trash') {
                $trash++;
                continue;
            }
            $all++;
            if ($row->status === 'publish' || $row->status === 'published') {
                $active++;
            } else {
                $inactive++;
            }
        }

        $current = isset($_GET['status']) ? sanitize_key($_GET['status']) : 'all';
        if ($current === 'publish' || $current === 'published') { $current = 'active'; }
        if ($current === 'draft') { $current = 'inactive'; }

        $make = function ($key, $label, $count) use ($current) {
            $url = ($key === 'all')
                ? remove_query_arg(['status', 'paged'])
                : add_query_arg(['status' => $key, 'paged' => 1]);
            return sprintf(
                '<a href="%s" class="wppoppop-view-btn%s"%s>%s <span class="count">(%d)</span></a>',
                esc_url($url),
                $current === $key ? ' current' : '',
                $current === $key ? ' aria-current="page"' : '',
                esc_html($label),
                (int) $count
            );
        };

        return [
            'all'      => $make('all', __('All', 'wppoppop'), $all),
            'active'   => $make('active', __('Active', 'wppoppop'), $active),
            'inactive' => $make('inactive', __('Inactive', 'wppoppop'), $inactive),
            'trash'    => $make('trash', __('Trash', 'wppoppop'), $trash),
        ];
    }

    protected function get_views_legacy() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $all_rows   = $wpdb->get_results("SELECT id, status, impressions, submissions FROM {$table_name}");

        $all_count     = 0;
        $pub_count     = 0;
        $draft_count   = 0;
        $high_cr_count = 0;
        $trash_count   = 0;

        if (!empty($all_rows)) {
            foreach ($all_rows as $row) {
                if ($row->status === 'trash') {
                    $trash_count++;
                    continue;
                }

                $all_count++;
                if ($row->status === 'publish' || $row->status === 'published') {
                    $pub_count++;
                } else {
                    $draft_count++;
                }

                $imp = intval($row->impressions);
                $sub = intval($row->submissions);
                $cr  = ($imp > 0) ? round(($sub / $imp) * 100, 1) : 0;
                if ($cr >= 10.0) {
                    $high_cr_count++;
                }
            }
        }

        $current = isset($_GET['status']) ? sanitize_key($_GET['status']) : 'all';

        $views = [
            'all'     => sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-all">%d</span>)</span></a>',
                esc_url(remove_query_arg(['status', 'paged'])),
                $current === 'all' ? 'current' : '',
                __('All', 'wppoppop'),
                $all_count
            ),
            'publish' => sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-publish">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'publish', 'paged' => 1])),
                $current === 'publish' ? 'current' : '',
                __('Published', 'wppoppop'),
                $pub_count
            ),
            'draft'   => sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-draft">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'draft', 'paged' => 1])),
                $current === 'draft' ? 'current' : '',
                __('Drafts', 'wppoppop'),
                $draft_count
            ),
            'high-cr' => sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-high-cr">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'high-cr', 'paged' => 1])),
                $current === 'high-cr' ? 'current' : '',
                __('High Converting (&ge;10%)', 'wppoppop'),
                $high_cr_count
            ),
        ];

        if ($trash_count > 0 || $current === 'trash') {
            $views['trash'] = sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-trash">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'trash', 'paged' => 1])),
                $current === 'trash' ? 'current' : '',
                __('Trash', 'wppoppop'),
                $trash_count
            );
        }

        return $views;
    }

    protected function extra_tablenav($which) {
        $status = isset($_REQUEST['status']) ? sanitize_key($_REQUEST['status']) : 'all';
        if ($which === 'top' && $status === 'trash' && current_user_can('purge_wppoppop_campaigns')) {
            $empty_trash_url = wp_nonce_url(
                admin_url('admin.php?page=wppoppop&action=empty_trash'),
                'wppoppop_empty_trash'
            );
            $empty_prompt = esc_attr(wp_json_encode(__('Are you sure you want to permanently empty the trash? This cannot be undone.', 'wppoppop')));
            ?>
            <div class="alignleft actions">
                <a href="<?php echo esc_url($empty_trash_url); ?>" class="button button-secondary apply" onclick="return confirm(<?php echo $empty_prompt; ?>);">
                    <?php esc_html_e('Empty Trash', 'wppoppop'); ?>
                </a>
            </div>
            <?php
        }
    }

    protected function column_cb($item) {
        $uid = is_array($item) ? $item['uid'] : $item->uid;
        return sprintf(
            '<input type="checkbox" name="bulk_ids[]" value="%s" class="wppoppop-select-row" />',
            esc_attr($uid)
        );
    }

    public function column_title($item) {
        $uid      = is_array($item) ? $item['uid'] : $item->uid;
        $title    = is_array($item) ? $item['title'] : $item->title;
        $status   = is_array($item) ? $item['status'] : $item->status;
        $is_trash = ($status === 'trash');

        $can_edit  = current_user_can('edit_wppoppop_campaign', $uid);
        $can_trash = current_user_can('delete_wppoppop_campaign', $uid);
        $can_purge = current_user_can('purge_wppoppop_campaign', $uid);

        $edit_url = admin_url('admin.php?page=wppoppop-builder&uid=' . esc_attr($uid));

        if ($is_trash) {
            $actions = [];
            if ($can_edit) {
                $untrash_url = wp_nonce_url(
                    admin_url('admin.php?page=wppoppop&action=untrash&uid=' . rawurlencode($uid)),
                    'wppoppop_untrash_' . $uid
                );
                $actions['untrash'] = sprintf('<a href="%s">%s</a>', esc_url($untrash_url), __('Restore', 'wppoppop'));
            }

            if ($can_purge) {
                $delete_url = wp_nonce_url(
                    admin_url('admin.php?page=wppoppop&action=delete&uid=' . rawurlencode($uid)),
                    'wppoppop_delete_' . $uid
                );
                $confirm_prompt = sprintf(__('Are you sure you want to permanently delete "%s"? This action cannot be undone.', 'wppoppop'), $title);
                $actions['delete'] = sprintf(
                    '<a href="%s" class="submitdelete" onclick="return confirm(%s);">%s</a>',
                    esc_url($delete_url),
                    esc_attr(wp_json_encode($confirm_prompt)),
                    __('Delete Permanently', 'wppoppop')
                );
            }

            return sprintf('<strong>%s</strong>%s', esc_html($title), $this->row_actions($actions));
        }

        $actions = [];

        if ($can_edit) {
            $actions['edit'] = sprintf('<a href="%s">%s</a>', esc_url($edit_url), __('Edit', 'wppoppop'));
            $actions['inline hide-if-no-js'] = sprintf(
                '<button type="button" class="button-link editinline wppoppop-quick-edit-btn" data-uid="%s" data-title="%s" data-status="%s">%s</button>',
                esc_attr($uid),
                esc_attr($title),
                esc_attr($status),
                __('Quick Edit', 'wppoppop')
            );
            $actions['duplicate'] = sprintf(
                '<a href="javascript:void(0);" role="button" class="wppoppop-duplicate-btn wppoppop-action-duplicate" data-uid="%s">%s</a>',
                esc_attr($uid),
                __('Duplicate', 'wppoppop')
            );
            $actions['export'] = sprintf(
                '<a href="javascript:void(0);" role="button" class="wppoppop-export-btn wppoppop-action-export" data-uid="%s">%s</a>',
                esc_attr($uid),
                __('Export JSON', 'wppoppop')
            );
        }

        $actions['embed'] = sprintf(
            '<a href="javascript:void(0);" role="button" class="wppoppop-action-embed" data-uid="%s" data-status="%s">%s</a>',
            esc_attr($uid),
            esc_attr($status),
            __('Embed', 'wppoppop')
        );

        if ($can_trash) {
            $trash_url = wp_nonce_url(
                admin_url('admin.php?page=wppoppop&action=trash&uid=' . rawurlencode($uid)),
                'wppoppop_trash_' . $uid
            );
            $actions['trash'] = sprintf('<a href="%s" class="submitdelete">%s</a>', esc_url($trash_url), __('Trash', 'wppoppop'));
        }

        $post_state = in_array($status, ['publish', 'published'], true)
            ? ' <span class="wppoppop-active-badge">' . esc_html__('ACTIVE', 'wppoppop') . '</span>'
            : ' <span class="wppoppop-inactive-badge">' . esc_html__('INACTIVE', 'wppoppop') . '</span>';

        $title_link = $can_edit
            ? sprintf('<a class="row-title" href="%s">%s</a>', esc_url($edit_url), esc_html($title))
            : esc_html($title);

        return sprintf('<strong>%s%s</strong>%s', $title_link, $post_state, $this->row_actions($actions));
    }

    public function column_status($item) {
        $status = is_array($item) ? $item['status'] : $item->status;
        if ($status === 'trash') {
            return '<span class="wppoppop-badge" style="background:#fce8e6;color:#c92a2a;font-weight:600;padding:2px 8px;border-radius:3px;">' . esc_html__('Trash', 'wppoppop') . '</span>';
        }

        $is_published = ($status === 'publish' || $status === 'published');
        $badge_class  = $is_published ? 'wppoppop-badge-success' : 'wppoppop-badge-draft';
        $label        = $is_published ? __('Published', 'wppoppop') : __('Draft', 'wppoppop');

        return sprintf('<span class="wppoppop-badge %s">%s</span>', esc_attr($badge_class), esc_html($label));
    }

    public function column_impressions($item) {
        $val = is_array($item) ? $item['impressions'] : $item->impressions;
        return number_format_i18n(intval($val));
    }

    public function column_submissions($item) {
        $val = is_array($item) ? $item['submissions'] : $item->submissions;
        return number_format_i18n(intval($val));
    }

    public function column_conversion($item) {
        $imp  = intval(is_array($item) ? $item['impressions'] : $item->impressions);
        $sub  = intval(is_array($item) ? $item['submissions'] : $item->submissions);
        $rate = ($imp > 0) ? round(($sub / $imp) * 100, 1) : 0;
        return $rate . '%';
    }

    public function column_created_at($item) {
        $val = is_array($item) ? $item['created_at'] : $item->created_at;
        return esc_html(date_i18n(get_option('date_format'), strtotime($val)));
    }

    protected function get_entry_counts() {
        if ($this->entry_counts !== null) {
            return $this->entry_counts;
        }
        global $wpdb;
        WpPopPop_Installer::ensure_schema();
        $subs = $wpdb->prefix . 'wppoppop_submissions';
        $rows = $wpdb->get_results(
            "SELECT popup_uid, COUNT(*) AS total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread FROM {$subs} GROUP BY popup_uid",
            ARRAY_A
        );
        $this->entry_counts = [];
        foreach ((array) $rows as $r) {
            $this->entry_counts[$r['popup_uid']] = [
                'total'  => (int) $r['total'],
                'unread' => (int) $r['unread'],
            ];
        }
        return $this->entry_counts;
    }

    public function column_shortcode($item) {
        $uid = is_array($item) ? $item['uid'] : $item->uid;
        $sc  = '[wppoppop uid="' . $uid . '"]';
        $preview_url = add_query_arg('wppoppop_preview', rawurlencode($uid), home_url('/'));
        $chip = '<code class="wppoppop-shortcode-chip" title="' . esc_attr__('Click to copy', 'wppoppop') . '">' . esc_html($sc) . '</code>';
        $icon = '<a href="' . esc_url($preview_url) . '" class="wppoppop-preview-screen" target="_blank" rel="noopener noreferrer" title="' . esc_attr__('Live preview on homepage', 'wppoppop') . '" aria-label="' . esc_attr__('Live preview on homepage', 'wppoppop') . '"><span class="dashicons dashicons-desktop" aria-hidden="true"></span></a>';
        return '<div class="wppoppop-shortcode-wrap">' . $chip . $icon . '</div>';
    }

    public function column_entries($item) {
        $uid    = is_array($item) ? $item['uid'] : $item->uid;
        $counts = $this->get_entry_counts();
        $total  = isset($counts[$uid]) ? $counts[$uid]['total'] : 0;
        $unread = isset($counts[$uid]) ? $counts[$uid]['unread'] : 0;
        $url    = admin_url('admin.php?page=wppoppop-submissions&popup_uid=' . rawurlencode($uid));
        $label  = sprintf(
            __('%1$s unread (%2$s entries)', 'wppoppop'),
            number_format_i18n($unread),
            number_format_i18n($total)
        );
        return '<a href="' . esc_url($url) . '"><strong>' . esc_html($label) . '</strong></a>';
    }

    public function column_active($item) {
        $uid    = is_array($item) ? $item['uid'] : $item->uid;
        $status = is_array($item) ? $item['status'] : $item->status;
        if ($status === 'trash') {
            return '<span aria-hidden="true">&mdash;</span>';
        }
        $is_on    = ($status === 'publish' || $status === 'published');
        $can      = current_user_can('manage_options') || current_user_can('publish_wppoppop_campaigns');
        $label    = $is_on ? __('Active', 'wppoppop') : __('Inactive', 'wppoppop');
        return sprintf(
            '<label class="wppoppop-switch" title="%1$s"><input type="checkbox" class="wppoppop-status-toggle-input" data-uid="%2$s" role="switch" aria-label="%1$s"%3$s%4$s /><span class="wppoppop-switch-slider" aria-hidden="true"></span></label>',
            esc_attr($label),
            esc_attr($uid),
            $is_on ? ' checked="checked"' : '',
            $can ? '' : ' disabled="disabled"'
        );
    }

    public function column_modified($item) {
        $val = is_array($item) ? ($item['updated_at'] ?? '') : ($item->updated_at ?? '');
        if (empty($val) || $val === '0000-00-00 00:00:00') {
            $val = is_array($item) ? ($item['created_at'] ?? '') : ($item->created_at ?? '');
        }
        if (empty($val)) {
            return '—';
        }
        return esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $val));
    }

    public function column_default($item, $column_name) {
        if (is_array($item)) {
            return isset($item[$column_name]) ? esc_html($item[$column_name]) : '';
        }
        return isset($item->$column_name) ? esc_html($item->$column_name) : '';
    }

    public function single_row($item) {
        $uid    = is_array($item) ? $item['uid'] : $item->uid;
        $title  = is_array($item) ? $item['title'] : $item->title;
        $status = is_array($item) ? $item['status'] : $item->status;

        echo '<tr id="wppoppop-row-' . esc_attr($uid) . '" class="wppoppop-table-row" data-uid="' . esc_attr($uid) . '" data-raw-title="' . esc_attr($title) . '" data-status="' . esc_attr($status) . '">';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    /**
     * Header-safe redirect mechanism to handle actions whether executed
     * before admin-header.php or inside template rendering.
     *
     * @param string $url The destination URL.
     */
    protected function safe_redirect($url) {
        if (!headers_sent()) {
            wp_safe_redirect($url);
            exit;
        }

        echo '<script type="text/javascript">window.location.replace(' . wp_json_encode($url) . ');</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . esc_url($url) . '"></noscript>';
        exit;
    }

    public function process_actions() {
        global $wpdb;
        $items_table = $wpdb->prefix . 'wppoppop_items';
        $subs_table  = $wpdb->prefix . 'wppoppop_submissions';

        // 1. Move to Trash
        if (isset($_GET['action']) && $_GET['action'] === 'trash' && !empty($_GET['uid'])) {
            $uid = sanitize_text_field(wp_unslash($_GET['uid']));
            check_admin_referer('wppoppop_trash_' . $uid);

            if (!current_user_can('delete_wppoppop_campaign', $uid)) {
                wp_die(__('You do not have permission to trash this campaign.', 'wppoppop'));
            }

            $wpdb->update($items_table, ['status' => 'trash'], ['uid' => $uid]);

            $redirect = remove_query_arg(['action', 'uid', '_wpnonce']);
            $redirect = add_query_arg(['trashed' => 1, 'trashed_uid' => $uid], $redirect);
            $this->safe_redirect($redirect);
        }

        // 2. Restore from Trash
        if (isset($_GET['action']) && $_GET['action'] === 'untrash' && !empty($_GET['uid'])) {
            $uid = sanitize_text_field(wp_unslash($_GET['uid']));
            check_admin_referer('wppoppop_untrash_' . $uid);

            if (!current_user_can('edit_wppoppop_campaign', $uid)) {
                wp_die(__('You do not have permission to restore this campaign.', 'wppoppop'));
            }

            $wpdb->update($items_table, ['status' => 'draft'], ['uid' => $uid]);

            $redirect = remove_query_arg(['action', 'uid', '_wpnonce']);
            $redirect = add_query_arg('untrashed', 1, $redirect);
            $this->safe_redirect($redirect);
        }

        // 3. Permanent Purge
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['uid'])) {
            $uid = sanitize_text_field(wp_unslash($_GET['uid']));
            check_admin_referer('wppoppop_delete_' . $uid);

            if (!current_user_can('purge_wppoppop_campaign', $uid)) {
                wp_die(__('You do not have permission to permanently delete this campaign.', 'wppoppop'));
            }

            $wpdb->delete($items_table, ['uid' => $uid]);
            if ($wpdb->get_var("SHOW TABLES LIKE '{$subs_table}'") === $subs_table) {
                $wpdb->delete($subs_table, ['popup_uid' => $uid]);
            }

            $redirect = remove_query_arg(['action', 'uid', '_wpnonce']);
            $redirect = add_query_arg('deleted', 1, $redirect);
            $this->safe_redirect($redirect);
        }

        // 4. Empty Trash
        if (isset($_GET['action']) && $_GET['action'] === 'empty_trash') {
            check_admin_referer('wppoppop_empty_trash');

            if (!current_user_can('purge_wppoppop_campaigns')) {
                wp_die(__('You do not have permission to empty the trash.', 'wppoppop'));
            }

            $trashed_uids = $wpdb->get_col("SELECT uid FROM {$items_table} WHERE status = 'trash'");
            if (!empty($trashed_uids)) {
                $wpdb->query("DELETE FROM {$items_table} WHERE status = 'trash'");
                if ($wpdb->get_var("SHOW TABLES LIKE '{$subs_table}'") === $subs_table) {
                    $placeholders = implode(',', array_fill(0, count($trashed_uids), '%s'));
                    $wpdb->query($wpdb->prepare("DELETE FROM {$subs_table} WHERE popup_uid IN ($placeholders)", $trashed_uids));
                }
            }

            $redirect = remove_query_arg(['action', '_wpnonce']);
            $redirect = add_query_arg('deleted', count($trashed_uids), $redirect);
            $this->safe_redirect($redirect);
        }

        // 5. Bulk Actions Handler
        $action = $this->current_action();
        if ($action && in_array($action, ['trash', 'untrash', 'delete', 'publish', 'draft', 'duplicate'], true)) {
            $nonce = !empty($_REQUEST['_wpnonce']) ? sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])) : '';
            $valid = wp_verify_nonce($nonce, 'bulk-' . $this->_args['plural']) || 
                     wp_verify_nonce($nonce, 'bulk-popups') || 
                     wp_verify_nonce($nonce, 'wppoppop_admin_nonce');

            if (!$valid) {
                wp_die(__('Security verification failed. Please try again.', 'wppoppop'));
            }

            $uids = [];
            if (!empty($_REQUEST['bulk_ids']) && is_array($_REQUEST['bulk_ids'])) {
                $uids = array_map('sanitize_text_field', wp_unslash($_REQUEST['bulk_ids']));
            }

            if (!empty($uids)) {
                $count = count($uids);
                $placeholders = implode(',', array_fill(0, count($uids), '%s'));

                if ($action === 'trash') {
                    if (!current_user_can('delete_wppoppop_campaigns')) {
                        wp_die(__('You do not have permission to move campaigns to the trash.', 'wppoppop'));
                    }
                    $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET status = 'trash' WHERE uid IN ($placeholders)", $uids));
                    $arg = ['trashed' => $count];
                } elseif ($action === 'untrash') {
                    if (!current_user_can('edit_wppoppop_campaigns')) {
                        wp_die(__('You do not have permission to restore campaigns.', 'wppoppop'));
                    }
                    $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET status = 'draft' WHERE uid IN ($placeholders)", $uids));
                    $arg = ['untrashed' => $count];
                } elseif ($action === 'delete') {
                    if (!current_user_can('purge_wppoppop_campaigns')) {
                        wp_die(__('You do not have permission to permanently delete campaigns.', 'wppoppop'));
                    }
                    $wpdb->query($wpdb->prepare("DELETE FROM {$items_table} WHERE uid IN ($placeholders)", $uids));
                    if ($wpdb->get_var("SHOW TABLES LIKE '{$subs_table}'") === $subs_table) {
                        $wpdb->query($wpdb->prepare("DELETE FROM {$subs_table} WHERE popup_uid IN ($placeholders)", $uids));
                    }
                    $arg = ['deleted' => $count];
                } elseif ($action === 'duplicate') {
                    if (!current_user_can('edit_wppoppop_campaigns')) {
                        wp_die(__('You do not have permission to duplicate campaigns.', 'wppoppop'));
                    }
                    $src = $wpdb->get_results($wpdb->prepare("SELECT title, data FROM {$items_table} WHERE uid IN ($placeholders) AND status != 'trash'", $uids), ARRAY_A);
                    $made = 0;
                    foreach ((array) $src as $r) {
                        $ok = $wpdb->insert($items_table, [
                            'uid'           => wp_generate_uuid4(),
                            'title'         => $r['title'] . ' (Copy)',
                            'data'          => $r['data'],
                            'status'        => 'draft',
                            'impressions'   => 0,
                            'submissions'   => 0,
                            'confirmations' => 0,
                        ], ['%s', '%s', '%s', '%s', '%d', '%d', '%d']);
                        if ($ok) { $made++; }
                    }
                    $arg = ['duplicated' => $made];
                } elseif ($action === 'publish' || $action === 'draft') {
                    if (!current_user_can('publish_wppoppop_campaigns')) {
                        wp_die(__('You do not have permission to modify campaign statuses.', 'wppoppop'));
                    }
                    $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET status = %s WHERE uid IN ($placeholders)", array_merge([$action], $uids)));
                    $arg = ['updated' => $count];
                }

                $redirect = remove_query_arg(['action', 'action2', 'bulk_ids', '_wpnonce']);
                $redirect = add_query_arg($arg, $redirect);
                $this->safe_redirect($redirect);
            }
        }
    }

    public function prepare_items() {
        $this->process_actions();

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];

        $status = isset($_GET['status']) ? sanitize_key($_GET['status']) : 'all';
        $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

        $query  = "SELECT * FROM {$table_name} WHERE 1=1";
        $params = [];

        if ($status === 'trash') {
            $query .= " AND status = 'trash'";
        } elseif ($status === 'published' || $status === 'publish' || $status === 'active') {
            $query .= " AND status IN ('publish', 'published')";
        } elseif ($status === 'draft' || $status === 'inactive') {
            $query .= " AND status NOT IN ('publish', 'published', 'trash')";
        } else {
            $query .= " AND status != 'trash'";
        }

        if (!empty($search)) {
            $query .= " AND (title LIKE %s OR uid LIKE %s)";
            $wildcard = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $orderby = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'updated_at';
        $order   = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

        $allowed_orderby = ['title', 'uid', 'status', 'impressions', 'submissions', 'created_at', 'updated_at'];
        if (!in_array($orderby, $allowed_orderby, true)) {
            $orderby = 'updated_at';
        }

        $query .= " ORDER BY {$orderby} {$order}";

        if (!empty($params)) {
            $results = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        } else {
            $results = $wpdb->get_results($query, ARRAY_A);
        }

        $data = $results ?: [];

        if ($status === 'high-cr') {
            $data = array_filter($data, function($item) {
                $imp = intval($item['impressions']);
                $sub = intval($item['submissions']);
                $cr  = ($imp > 0) ? round(($sub / $imp) * 100, 1) : 0;
                return $cr >= 10.0;
            });
        }

        $total_items   = count($data);
        $user_per_page = (int) get_user_meta(get_current_user_id(), 'wppoppop_campaigns_per_page', true);
        $per_page      = ($user_per_page > 0) ? $user_per_page : 25;
        $current_page  = $this->get_pagenum();

        $this->items = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page) ?: 1,
        ]);
    }
}
