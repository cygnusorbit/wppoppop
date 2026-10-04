<?php
namespace WPPopPop\Targeting;

class PopupPostType {
    public const POST_TYPE = 'wppoppop';
    public const LEAD_POST_TYPE = 'wppoppop_lead';

    public function init(): void {
        add_action('init', [$this, 'register_post_types']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save_meta']);
        
        add_filter('manage_' . self::LEAD_POST_TYPE . '_posts_columns', [$this, 'set_lead_columns']);
        add_action('manage_' . self::LEAD_POST_TYPE . '_posts_custom_column', [$this, 'render_lead_columns'], 10, 2);

        add_filter('manage_' . self::POST_TYPE . '_posts_columns', [$this, 'set_popup_columns']);
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', [$this, 'render_popup_columns'], 10, 2);

        add_shortcode('wppoppop', [$this, 'render_shortcode']);
    }

    public function register_post_types(): void {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name'               => __('Popups', 'wppoppop'),
                'singular_name'      => __('Popup', 'wppoppop'),
                'add_new'            => __('Add New Popup', 'wppoppop'),
                'add_new_item'       => __('Add New Popup', 'wppoppop'),
                'edit_item'          => __('Edit Popup', 'wppoppop'),
                'all_items'          => __('All Popups', 'wppoppop'),
            ],
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon'           => 'dashicons-format-gallery',
            'supports'            => ['title', 'editor'],
            'has_archive'         => false,
            'exclude_from_search' => true,
        ]);

        register_post_type(self::LEAD_POST_TYPE, [
            'labels' => [
                'name'               => __('Captured Leads', 'wppoppop'),
                'singular_name'      => __('Lead', 'wppoppop'),
                'all_items'          => __('All Leads', 'wppoppop'),
            ],
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=' . self::POST_TYPE,
            'supports'            => ['title'],
            'capabilities'        => [
                'create_posts' => 'do_not_allow',
            ],
            'map_meta_cap'        => true,
        ]);
    }

    public function set_popup_columns(array $columns): array {
        $new_columns = [];
        foreach ($columns as $key => $title) {
            $new_columns[$key] = $title;
            if ($key === 'title') {
                $new_columns['social_proof']    = __('FOMO Alerts', 'wppoppop');
                $new_columns['display_mode']    = __('Display Mode', 'wppoppop');
                $new_columns['wheel_stat']      = __('Gamified Wheel', 'wppoppop');
                $new_columns['schedule_stat']   = __('Schedule', 'wppoppop');
                $new_columns['autoresponder']   = __('Autoresponder', 'wppoppop');
                $new_columns['sms_stat']        = __('SMS Alert', 'wppoppop');
                $new_columns['payments_stat']   = __('Payments / Revenue', 'wppoppop');
                $new_columns['mailchimp']       = __('Mailchimp', 'wppoppop');
                $new_columns['wc_rule']         = __('E-Commerce Mode', 'wppoppop');
                $new_columns['multistep']       = __('Funnel Mode', 'wppoppop');
                $new_columns['ab_mode']         = __('A/B Campaign', 'wppoppop');
                $new_columns['position']        = __('Position', 'wppoppop');
                $new_columns['impressions']     = __('Impressions', 'wppoppop');
                $new_columns['submissions']     = __('Submissions', 'wppoppop');
                $new_columns['conversion_rate'] = __('Conversion Rate', 'wppoppop');
                $new_columns['shortcode']       = __('Shortcode', 'wppoppop');
            }
        }
        return $new_columns;
    }

    public function render_popup_columns(string $column, int $post_id): void {
        $impressions  = (int) get_post_meta($post_id, '_wppoppop_impressions', true);
        $submissions  = (int) get_post_meta($post_id, '_wppoppop_submissions', true);
        $position     = get_post_meta($post_id, '_wppoppop_position', true) ?: 'center';
        $ab_enabled   = get_post_meta($post_id, '_wppoppop_ab_enabled', true) === '1';
        $multistep    = get_post_meta($post_id, '_wppoppop_multistep_enabled', true) === '1';
        $wc_rule      = get_post_meta($post_id, '_wppoppop_wc_rule', true) ?: 'all';
        $mc_enabled   = get_post_meta($post_id, '_wppoppop_mailchimp_enabled', true) === '1';
        $sms_enabled  = get_post_meta($post_id, '_wppoppop_sms_enabled', true) === '1';
        $ar_enabled   = get_post_meta($post_id, '_wppoppop_autoresponder_enabled', true) === '1';
        $sch_enabled  = get_post_meta($post_id, '_wppoppop_schedule_enabled', true) === '1';
        $wheel_on     = get_post_meta($post_id, '_wppoppop_wheel_enabled', true) === '1';
        $display_mode = get_post_meta($post_id, '_wppoppop_display_mode', true) ?: 'modal';
        $sp_enabled   = get_post_meta($post_id, '_wppoppop_sp_enabled', true) === '1';

        $pay_enabled  = get_post_meta($post_id, '_wppoppop_payment_enabled', true) === '1';
        $pay_count    = (int) get_post_meta($post_id, '_wppoppop_payments_count', true);
        $pay_rev      = (float) get_post_meta($post_id, '_wppoppop_revenue_total', true);

        if ($column === 'social_proof') {
            echo $sp_enabled ? '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#dbeafe;color:#1e40af;">FOMO ON</span>' : '<span style="color:#94a3b8;">—</span>';
        } elseif ($column === 'display_mode') {
            $mode_tags = [
                'modal'      => ['label' => 'MODAL', 'bg' => '#f1f5f9', 'color' => '#334155'],
                'fullscreen' => ['label' => 'FULLSCREEN', 'bg' => '#fef3c7', 'color' => '#92400e'],
                'bar_top'    => ['label' => 'TOP BAR', 'bg' => '#dcfce7', 'color' => '#166534'],
                'bar_bottom' => ['label' => 'BOTTOM BAR', 'bg' => '#e0e7ff', 'color' => '#3730a3'],
                'slide_in'   => ['label' => 'SLIDE-IN', 'bg' => '#ede9fe', 'color' => '#5b21b6'],
            ];
            $t = $mode_tags[$display_mode] ?? $mode_tags['modal'];
            echo '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:' . esc_attr($t['bg']) . ';color:' . esc_attr($t['color']) . ';">' . esc_html($t['label']) . '</span>';
        } elseif ($column === 'wheel_stat') {
            echo $wheel_on ? '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#fbcfe8;color:#9d174d;">SPIN WHEEL</span>' : '<span style="color:#94a3b8;">—</span>';
        } elseif ($column === 'schedule_stat') {
            if ($sch_enabled) {
                $is_live = ScheduleManager::matches_schedule(get_post($post_id));
                echo $is_live 
                    ? '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;">LIVE</span>'
                    : '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#fee2e2;color:#991b1b;">OFF-SCHEDULE</span>';
            } else {
                echo '<span style="color:#94a3b8;">Always On</span>';
            }
        } elseif ($column === 'autoresponder') {
            echo $ar_enabled ? '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#dbeafe;color:#1e40af;">ACTIVE</span>' : '<span style="color:#94a3b8;">—</span>';
        } elseif ($column === 'sms_stat') {
            echo $sms_enabled ? '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;">ACTIVE</span>' : '<span style="color:#94a3b8;">—</span>';
        } elseif ($column === 'payments_stat') {
            if ($pay_enabled) {
                echo '<strong>' . esc_html($pay_count) . ' orders</strong><br><span style="color:#16a34a;font-weight:600;">$' . esc_html(number_format($pay_rev, 2)) . '</span>';
            } else {
                echo '<span style="color:#94a3b8;">Disabled</span>';
            }
        } elseif ($column === 'mailchimp') {
            echo $mc_enabled ? '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#fef08a;color:#854d0e;">SYNC ON</span>' : '<span style="color:#94a3b8;">—</span>';
        } elseif ($column === 'wc_rule') {
            if ($wc_rule === 'cart_abandonment') {
                echo '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#fef08a;color:#854d0e;">CART ABANDON</span>';
            } elseif ($wc_rule === 'products_only') {
                echo '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#e0e7ff;color:#3730a3;">PRODUCTS</span>';
            } else {
                echo '<span style="color:#94a3b8;">Standard</span>';
            }
        } elseif ($column === 'multistep') {
            echo $multistep ? '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#dbeafe;color:#1e40af;">MULTI-STEP</span>' : '<span style="color:#94a3b8;">Single</span>';
        } elseif ($column === 'ab_mode') {
            if ($ab_enabled) {
                $var_b = get_post_meta($post_id, '_wppoppop_ab_variant_id', true);
                echo '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#ede9fe;color:#6b21a8;">ACTIVE (vs #' . esc_html($var_b) . ')</span>';
            } else {
                echo '<span style="color:#94a3b8;">—</span>';
            }
        } elseif ($column === 'position') {
            echo esc_html(ucwords(str_replace('-', ' ', $position)));
        } elseif ($column === 'impressions') {
            echo esc_html(number_format_i18n($impressions));
        } elseif ($column === 'submissions') {
            echo esc_html(number_format_i18n($submissions));
        } elseif ($column === 'conversion_rate') {
            $rate = $impressions > 0 ? round(($submissions / $impressions) * 100, 1) : 0;
            echo esc_html($rate . '%');
        } elseif ($column === 'shortcode') {
            echo '<code>[wppoppop id="' . esc_attr($post_id) . '"]</code>';
        }
    }

    public function render_shortcode(array $atts): string {
        $atts = shortcode_atts([
            'id'   => 0,
            'text' => __('Open Popup', 'wppoppop'),
        ], $atts, 'wppoppop');

        $popup_id = absint($atts['id']);
        if (!$popup_id) {
            return '';
        }

        return sprintf(
            '<button type="button" class="wppoppop-trigger wppoppop-btn-inline" data-popup-id="%d">%s</button>',
            $popup_id,
            esc_html($atts['text'])
        );
    }

    public function set_lead_columns(array $columns): array {
        return [
            'cb'         => $columns['cb'],
            'title'      => __('Email Address', 'wppoppop'),
            'name'       => __('Name', 'wppoppop'),
            'popup_id'   => __('Source Popup', 'wppoppop'),
            'attachment' => __('Attachment', 'wppoppop'),
            'downloads'  => __('Downloads', 'wppoppop'),
            'status'     => __('Status', 'wppoppop'),
            'date'       => __('Captured At', 'wppoppop'),
        ];
    }

    public function render_lead_columns(string $column, int $post_id): void {
        if ($column === 'name') {
            echo esc_html(get_post_meta($post_id, '_wppoppop_lead_name', true) ?: '—');
        } elseif ($column === 'popup_id') {
            $pid = get_post_meta($post_id, '_wppoppop_lead_popup_id', true);
            echo esc_html($pid ? get_the_title($pid) . " (#$pid)" : '—');
        } elseif ($column === 'attachment') {
            $file_url = get_post_meta($post_id, '_wppoppop_lead_attachment', true);
            if (!empty($file_url)) {
                echo '<a href="' . esc_url($file_url) . '" target="_blank" class="button button-small">' . esc_html__('View File', 'wppoppop') . '</a>';
            } else {
                echo '<span style="color:#94a3b8;">—</span>';
            }
        } elseif ($column === 'downloads') {
            $d_count = (int) get_post_meta($post_id, '_wppoppop_download_count', true);
            echo '<strong>' . esc_html($d_count) . '</strong>';
        } elseif ($column === 'status') {
            $confirmed = get_post_meta($post_id, '_wppoppop_confirmed', true);
            if ($confirmed === '1') {
                echo '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:12px;font-weight:600;background:#dcfce7;color:#166534;">' . esc_html__('Confirmed', 'wppoppop') . '</span>';
            } else {
                echo '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:12px;font-weight:600;background:#fef3c7;color:#92400e;">' . esc_html__('Pending', 'wppoppop') . '</span>';
            }
        }
    }

    public function add_meta_boxes(): void {
        add_meta_box(
            'wppoppop_targeting_settings',
            __('Targeting & Trigger Rules', 'wppoppop'),
            [$this, 'render_targeting_metabox'],
            self::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'wppoppop_behavior_settings',
            __('Tab Switch, Back Button & Sound Effects Engine', 'wppoppop'),
            [$this, 'render_behavior_metabox'],
            self::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'wppoppop_social_proof_settings',
            __('Social Proof & Live FOMO Notifications', 'wppoppop'),
            [$this, 'render_social_proof_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_custom_fields_settings',
            __('Form Field Builder & Spam Defense (Turnstile / Honeypot)', 'wppoppop'),
            [$this, 'render_custom_fields_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_layout_settings',
            __('Display Mode & Layout Style', 'wppoppop'),
            [$this, 'render_layout_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_wheel_settings',
            __('Gamified Spin-to-Win Fortune Wheel', 'wppoppop'),
            [$this, 'render_wheel_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_schedule_settings',
            __('Date / Time Schedule & Working Hours', 'wppoppop'),
            [$this, 'render_schedule_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_countdown_settings',
            __('Urgency Countdown Timer Engine', 'wppoppop'),
            [$this, 'render_countdown_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_autoresponder_settings',
            __('Autoresponder & Digital Asset Delivery', 'wppoppop'),
            [$this, 'render_autoresponder_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_sms_settings',
            __('SMS Notifications (Twilio Gateway)', 'wppoppop'),
            [$this, 'render_sms_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_email_verify_settings',
            __('Email Verification API (Deliverability Check)', 'wppoppop'),
            [$this, 'render_email_verify_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_payment_settings',
            __('Stripe Payment Gateway & Monetization', 'wppoppop'),
            [$this, 'render_payment_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_mailchimp_settings',
            __('Mailchimp CRM Integration', 'wppoppop'),
            [$this, 'render_mailchimp_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_privacy_settings',
            __('GDPR & Sensitive Data Storage', 'wppoppop'),
            [$this, 'render_privacy_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_math_settings',
            __('Math Expressions & Conditional Logic', 'wppoppop'),
            [$this, 'render_math_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_wc_settings',
            __('WooCommerce & Store Targeting', 'wppoppop'),
            [$this, 'render_wc_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_param_settings',
            __('URL Query & Frequency Capping', 'wppoppop'),
            [$this, 'render_param_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_remote_embed_settings',
            __('Remote Use / Cross-Domain Embed', 'wppoppop'),
            [$this, 'render_remote_embed_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_multistep_settings',
            __('Multi-Step Funnel Settings', 'wppoppop'),
            [$this, 'render_multistep_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_tracking_settings',
            __('Event Tracking & Custom JS Handlers', 'wppoppop'),
            [$this, 'render_tracking_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_geo_settings',
            __('Geolocation Filter', 'wppoppop'),
            [$this, 'render_geo_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_ab_settings',
            __('A/B Split Campaign', 'wppoppop'),
            [$this, 'render_ab_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_layer_settings',
            __('Layer Customizer', 'wppoppop'),
            [$this, 'render_layer_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'wppoppop_optin_settings',
            __('Double Opt-In Verification', 'wppoppop'),
            [$this, 'render_optin_metabox'],
            self::POST_TYPE,
            'normal',
            'default'
        );
    }

    public function render_behavior_metabox(\WP_Post $post): void {
        $tab_switch  = get_post_meta($post->ID, '_wppoppop_tab_switch_enabled', true);
        $tab_flash   = get_post_meta($post->ID, '_wppoppop_tab_title_flash', true) ?: '⚠️ Wait, don\'t leave your discount!';
        $back_button = get_post_meta($post->ID, '_wppoppop_back_button_enabled', true);
        $sound_fx    = get_post_meta($post->ID, '_wppoppop_sound_fx_enabled', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_tab_switch_enabled"><?php esc_html_e('OnPageSwitch (Tab Switch)', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_tab_switch_enabled" id="wppoppop_tab_switch_enabled" value="1" <?php checked($tab_switch, '1'); ?>>
                        <?php esc_html_e('Trigger popup immediately when visitor returns to this browser tab', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_tab_title_flash"><?php esc_html_e('Inactive Tab Flashing Title', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_tab_title_flash" id="wppoppop_tab_title_flash" value="<?php echo esc_attr($tab_flash); ?>" class="large-text">
                    <p class="description"><?php esc_html_e('Alternates the browser tab title with this message when visitors switch to another tab.', 'wppoppop'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_back_button_enabled"><?php esc_html_e('Browser Back-Button Hijack', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_back_button_enabled" id="wppoppop_back_button_enabled" value="1" <?php checked($back_button, '1'); ?>>
                        <?php esc_html_e('Intercept browser back button to display retention popup instead of leaving site', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_sound_fx_enabled"><?php esc_html_e('Web Audio Sound Effects', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_sound_fx_enabled" id="wppoppop_sound_fx_enabled" value="1" <?php checked($sound_fx, '1'); ?>>
                        <?php esc_html_e('Play synthesized chimes on popup open and victory chords on successful submission', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_social_proof_metabox(\WP_Post $post): void {
        $sp_enabled   = get_post_meta($post->ID, '_wppoppop_sp_enabled', true);
        $sp_use_real  = get_post_meta($post->ID, '_wppoppop_sp_use_real', true);
        $sp_use_real  = ($sp_use_real === '') ? '1' : $sp_use_real;
        $sp_interval  = get_post_meta($post->ID, '_wppoppop_sp_interval', true) ?: '8';
        $sp_duration  = get_post_meta($post->ID, '_wppoppop_sp_duration', true) ?: '5';
        $sp_position  = get_post_meta($post->ID, '_wppoppop_sp_position', true) ?: 'bottom-left';
        $sp_fallbacks = get_post_meta($post->ID, '_wppoppop_sp_fallbacks', true) ?: "Alex from London just subscribed!|4 minutes ago|🎉\nMaria from New York claimed a 20% discount|12 minutes ago|⚡\nDavid from Berlin unlocked the voucher!|25 minutes ago|🔥";
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_sp_enabled"><?php esc_html_e('Enable FOMO Toast Stream', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_sp_enabled" id="wppoppop_sp_enabled" value="1" <?php checked($sp_enabled, '1'); ?>>
                        <?php esc_html_e('Display non-intrusive floating social proof cards showing recent subscriber activity', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_sp_use_real"><?php esc_html_e('Include Real Lead Submissions', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_sp_use_real" id="wppoppop_sp_use_real" value="1" <?php checked($sp_use_real, '1'); ?>>
                        <?php esc_html_e('Automatically pull recent leads captured by this popup and mask subscriber identities', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_sp_position"><?php esc_html_e('Toast Screen Position', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_sp_position" id="wppoppop_sp_position">
                        <option value="bottom-left" <?php selected($sp_position, 'bottom-left'); ?>><?php esc_html_e('Bottom Left (Recommended)', 'wppoppop'); ?></option>
                        <option value="bottom-right" <?php selected($sp_position, 'bottom-right'); ?>><?php esc_html_e('Bottom Right', 'wppoppop'); ?></option>
                        <option value="top-left" <?php selected($sp_position, 'top-left'); ?>><?php esc_html_e('Top Left', 'wppoppop'); ?></option>
                        <option value="top-right" <?php selected($sp_position, 'top-right'); ?>><?php esc_html_e('Top Right', 'wppoppop'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_sp_interval"><?php esc_html_e('Delay Between Notifications (Sec)', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" name="_wppoppop_sp_interval" id="wppoppop_sp_interval" value="<?php echo esc_attr($sp_interval); ?>" min="2" max="60" class="small-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_sp_duration"><?php esc_html_e('Display Duration per Toast (Sec)', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" name="_wppoppop_sp_duration" id="wppoppop_sp_duration" value="<?php echo esc_attr($sp_duration); ?>" min="2" max="30" class="small-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_sp_fallbacks"><?php esc_html_e('Fallback / Custom Notifications', 'wppoppop'); ?></label></th>
                <td>
                    <textarea name="_wppoppop_sp_fallbacks" id="wppoppop_sp_fallbacks" rows="5" class="large-text" style="font-family:monospace;"><?php echo esc_textarea($sp_fallbacks); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_custom_fields_metabox(\WP_Post $post): void {
        $enable_dropdown   = get_post_meta($post->ID, '_wppoppop_enable_dropdown', true);
        $dropdown_label    = get_post_meta($post->ID, '_wppoppop_dropdown_label', true) ?: 'Select Inquired Department';
        $dropdown_options  = get_post_meta($post->ID, '_wppoppop_dropdown_options', true) ?: "Sales Support\nTechnical Inquiries\nPartnership\nGeneral Feedback";

        $enable_datepicker = get_post_meta($post->ID, '_wppoppop_enable_datepicker', true);
        $datepicker_label  = get_post_meta($post->ID, '_wppoppop_datepicker_label', true) ?: 'Preferred Appointment / Meeting Date';

        $enable_upload     = get_post_meta($post->ID, '_wppoppop_enable_file_upload', true);
        $upload_label      = get_post_meta($post->ID, '_wppoppop_file_upload_label', true) ?: 'Attach Resume / Document (PDF, PNG, JPG, DOCX)';

        $turnstile_on       = get_post_meta($post->ID, '_wppoppop_turnstile_enabled', true);
        $turnstile_site_key = get_post_meta($post->ID, '_wppoppop_turnstile_site_key', true) ?: '';
        $turnstile_sec_key  = get_post_meta($post->ID, '_wppoppop_turnstile_secret_key', true) ?: '';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Honeypot Bot Defense', 'wppoppop'); ?></th>
                <td>
                    <span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;">AUTOMATICALLY ACTIVE</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_enable_dropdown"><?php esc_html_e('Enable Dropdown Select Field', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_enable_dropdown" id="wppoppop_enable_dropdown" value="1" <?php checked($enable_dropdown, '1'); ?>>
                        <?php esc_html_e('Add a custom select dropdown menu to the popup form', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_dropdown_label"><?php esc_html_e('Dropdown Label', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_dropdown_label" id="wppoppop_dropdown_label" value="<?php echo esc_attr($dropdown_label); ?>" class="large-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_dropdown_options"><?php esc_html_e('Dropdown Options (One per line)', 'wppoppop'); ?></label></th>
                <td>
                    <textarea name="_wppoppop_dropdown_options" id="wppoppop_dropdown_options" rows="4" class="large-text" style="font-family:monospace;"><?php echo esc_textarea($dropdown_options); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_enable_datepicker"><?php esc_html_e('Enable Datepicker Field', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_enable_datepicker" id="wppoppop_enable_datepicker" value="1" <?php checked($enable_datepicker, '1'); ?>>
                        <?php esc_html_e('Add a date selector field for scheduling or appointments', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_datepicker_label"><?php esc_html_e('Datepicker Label', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_datepicker_label" id="wppoppop_datepicker_label" value="<?php echo esc_attr($datepicker_label); ?>" class="large-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_enable_file_upload"><?php esc_html_e('Enable File Upload Field', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_enable_file_upload" id="wppoppop_enable_file_upload" value="1" <?php checked($enable_upload, '1'); ?>>
                        <?php esc_html_e('Allow visitors to attach documents/images (PDF, ZIP, PNG, JPG, DOCX, TXT)', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_file_upload_label"><?php esc_html_e('File Upload Label', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_file_upload_label" id="wppoppop_file_upload_label" value="<?php echo esc_attr($upload_label); ?>" class="large-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_turnstile_enabled"><?php esc_html_e('Enable Cloudflare Turnstile Bot Protection', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_turnstile_enabled" id="wppoppop_turnstile_enabled" value="1" <?php checked($turnstile_on, '1'); ?>>
                        <?php esc_html_e('Authenticate form submissions using frictionless Cloudflare Turnstile CAPTCHA', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_turnstile_site_key"><?php esc_html_e('Turnstile Site Key', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_turnstile_site_key" id="wppoppop_turnstile_site_key" value="<?php echo esc_attr($turnstile_site_key); ?>" class="large-text" placeholder="0x4AAAAAA...">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_turnstile_secret_key"><?php esc_html_e('Turnstile Secret Key', 'wppoppop'); ?></label></th>
                <td>
                    <input type="password" name="_wppoppop_turnstile_secret_key" id="wppoppop_turnstile_secret_key" value="<?php echo esc_attr($turnstile_sec_key); ?>" class="large-text">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_layout_metabox(\WP_Post $post): void {
        $display_mode = get_post_meta($post->ID, '_wppoppop_display_mode', true) ?: 'modal';
        $position     = get_post_meta($post->ID, '_wppoppop_position', true) ?: 'center';
        $inline_mode  = get_post_meta($post->ID, '_wppoppop_inline_mode', true) ?: 'none';
        $webhook_url  = get_post_meta($post->ID, '_wppoppop_webhook_url', true) ?: '';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_display_mode"><?php esc_html_e('Display Presentation Mode', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_display_mode" id="wppoppop_display_mode">
                        <option value="modal" <?php selected($display_mode, 'modal'); ?>><?php esc_html_e('Classic Modal (Lightbox Popup)', 'wppoppop'); ?></option>
                        <option value="fullscreen" <?php selected($display_mode, 'fullscreen'); ?>><?php esc_html_e('Fullscreen Takeover (Welcome Mat)', 'wppoppop'); ?></option>
                        <option value="bar_top" <?php selected($display_mode, 'bar_top'); ?>><?php esc_html_e('Sticky Top Notification Bar', 'wppoppop'); ?></option>
                        <option value="bar_bottom" <?php selected($display_mode, 'bar_bottom'); ?>><?php esc_html_e('Sticky Bottom Ribbon Bar', 'wppoppop'); ?></option>
                        <option value="slide_in" <?php selected($display_mode, 'slide_in'); ?>><?php esc_html_e('Corner Slide-In Flyout', 'wppoppop'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_position"><?php esc_html_e('Viewport Alignment (Modal Mode Only)', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_position" id="wppoppop_position">
                        <option value="center" <?php selected($position, 'center'); ?>>Center (Default)</option>
                        <option value="top-left" <?php selected($position, 'top-left'); ?>>Top Left</option>
                        <option value="top-center" <?php selected($position, 'top-center'); ?>>Top Center</option>
                        <option value="top-right" <?php selected($position, 'top-right'); ?>>Top Right</option>
                        <option value="middle-left" <?php selected($position, 'middle-left'); ?>>Middle Left</option>
                        <option value="middle-right" <?php selected($position, 'middle-right'); ?>>Middle Right</option>
                        <option value="bottom-left" <?php selected($position, 'bottom-left'); ?>>Bottom Left</option>
                        <option value="bottom-center" <?php selected($position, 'bottom-center'); ?>>Bottom Center</option>
                        <option value="bottom-right" <?php selected($position, 'bottom-right'); ?>>Bottom Right</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_inline_mode"><?php esc_html_e('Inline Content Injection', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_inline_mode" id="wppoppop_inline_mode">
                        <option value="none" <?php selected($inline_mode, 'none'); ?>>None (Overlay / Bar Mode)</option>
                        <option value="content_start" <?php selected($inline_mode, 'content_start'); ?>>ContentStart (Beginning of post/page)</option>
                        <option value="content_end" <?php selected($inline_mode, 'content_end'); ?>>ContentEnd (End of post/page)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_webhook_url"><?php esc_html_e('Webhook / CRM URL', 'wppoppop'); ?></label></th>
                <td>
                    <input type="url" name="_wppoppop_webhook_url" id="wppoppop_webhook_url" value="<?php echo esc_url($webhook_url); ?>" class="large-text" placeholder="https://hooks.zapier.com/hooks/catch/...">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_wheel_metabox(\WP_Post $post): void {
        $enabled = get_post_meta($post->ID, '_wppoppop_wheel_enabled', true);
        $slices  = get_post_meta($post->ID, '_wppoppop_wheel_slices', true) ?: "10% OFF|SAVE10\nFree Gift|FREEGIFT\nTry Again|NONE\n25% OFF|SAVE25\n5% OFF|SAVE05\nJackpot 50%|JACKPOT50\nFree Shipping|FREESHIP\nLucky Voucher|LUCKY";
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_wheel_enabled"><?php esc_html_e('Enable Spin-to-Win Wheel', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_wheel_enabled" id="wppoppop_wheel_enabled" value="1" <?php checked($enabled, '1'); ?>>
                        <?php esc_html_e('Display an interactive gamified fortune wheel above the lead capture form', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_wheel_slices"><?php esc_html_e('Wheel Slices & Coupons', 'wppoppop'); ?></label></th>
                <td>
                    <textarea name="_wppoppop_wheel_slices" id="wppoppop_wheel_slices" rows="8" class="large-text" style="font-family:monospace;"><?php echo esc_textarea($slices); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_schedule_metabox(\WP_Post $post): void {
        $enabled   = get_post_meta($post->ID, '_wppoppop_schedule_enabled', true);
        $start     = get_post_meta($post->ID, '_wppoppop_schedule_start', true) ?: '';
        $end       = get_post_meta($post->ID, '_wppoppop_schedule_end', true) ?: '';
        $days      = get_post_meta($post->ID, '_wppoppop_schedule_days', true) ?: [];
        $time_from = get_post_meta($post->ID, '_wppoppop_schedule_time_from', true) ?: '';
        $time_to   = get_post_meta($post->ID, '_wppoppop_schedule_time_to', true) ?: '';

        $weekdays = [
            '1' => __('Monday', 'wppoppop'),
            '2' => __('Tuesday', 'wppoppop'),
            '3' => __('Wednesday', 'wppoppop'),
            '4' => __('Thursday', 'wppoppop'),
            '5' => __('Friday', 'wppoppop'),
            '6' => __('Saturday', 'wppoppop'),
            '7' => __('Sunday', 'wppoppop'),
        ];
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_schedule_enabled"><?php esc_html_e('Enable Campaign Scheduling', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_schedule_enabled" id="wppoppop_schedule_enabled" value="1" <?php checked($enabled, '1'); ?>>
                        <?php esc_html_e('Restrict popup display to a designated date range and hours', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_schedule_start"><?php esc_html_e('Campaign Start Date & Time', 'wppoppop'); ?></label></th>
                <td>
                    <input type="datetime-local" name="_wppoppop_schedule_start" id="wppoppop_schedule_start" value="<?php echo esc_attr($start); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_schedule_end"><?php esc_html_e('Campaign End Date & Time', 'wppoppop'); ?></label></th>
                <td>
                    <input type="datetime-local" name="_wppoppop_schedule_end" id="wppoppop_schedule_end" value="<?php echo esc_attr($end); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Active Days of the Week', 'wppoppop'); ?></th>
                <td>
                    <?php foreach ($weekdays as $d_num => $d_label): ?>
                        <label style="margin-right:12px; display:inline-block;">
                            <input type="checkbox" name="_wppoppop_schedule_days[]" value="<?php echo esc_attr($d_num); ?>" <?php checked(in_array((string)$d_num, (array)$days, true)); ?>>
                            <?php echo esc_html($d_label); ?>
                        </label>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Active Daily Hours Window', 'wppoppop'); ?></th>
                <td>
                    <label for="wppoppop_schedule_time_from"><?php esc_html_e('From:', 'wppoppop'); ?></label>
                    <input type="time" name="_wppoppop_schedule_time_from" id="wppoppop_schedule_time_from" value="<?php echo esc_attr($time_from); ?>">
                    &nbsp;&nbsp;
                    <label for="wppoppop_schedule_time_to"><?php esc_html_e('To:', 'wppoppop'); ?></label>
                    <input type="time" name="_wppoppop_schedule_time_to" id="wppoppop_schedule_time_to" value="<?php echo esc_attr($time_to); ?>">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_countdown_metabox(\WP_Post $post): void {
        $enabled     = get_post_meta($post->ID, '_wppoppop_countdown_enabled', true);
        $type        = get_post_meta($post->ID, '_wppoppop_countdown_type', true) ?: 'evergreen';
        $target_date = get_post_meta($post->ID, '_wppoppop_countdown_date', true) ?: '';
        $mins        = get_post_meta($post->ID, '_wppoppop_countdown_mins', true) ?: '15';
        $action      = get_post_meta($post->ID, '_wppoppop_countdown_action', true) ?: 'hide';
        $redir       = get_post_meta($post->ID, '_wppoppop_countdown_redir', true) ?: '';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_countdown_enabled"><?php esc_html_e('Enable Countdown Timer', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_countdown_enabled" id="wppoppop_countdown_enabled" value="1" <?php checked($enabled, '1'); ?>>
                        <?php esc_html_e('Display an animated urgency timer bar inside the popup', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_countdown_type"><?php esc_html_e('Timer Mode', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_countdown_type" id="wppoppop_countdown_type">
                        <option value="evergreen" <?php selected($type, 'evergreen'); ?>><?php esc_html_e('Evergreen (Personalized per visitor session)', 'wppoppop'); ?></option>
                        <option value="fixed" <?php selected($type, 'fixed'); ?>><?php esc_html_e('Fixed Date & Time (Global deadline)', 'wppoppop'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_countdown_mins"><?php esc_html_e('Evergreen Duration (Minutes)', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" name="_wppoppop_countdown_mins" id="wppoppop_countdown_mins" value="<?php echo esc_attr($mins); ?>" min="1" max="1440" class="small-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_countdown_date"><?php esc_html_e('Fixed Target Date & Time', 'wppoppop'); ?></label></th>
                <td>
                    <input type="datetime-local" name="_wppoppop_countdown_date" id="wppoppop_countdown_date" value="<?php echo esc_attr($target_date); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_countdown_action"><?php esc_html_e('When Timer Reaches Zero', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_countdown_action" id="wppoppop_countdown_action">
                        <option value="hide" <?php selected($action, 'hide'); ?>><?php esc_html_e('Dismiss / Hide Popup Immediately', 'wppoppop'); ?></option>
                        <option value="text" <?php selected($action, 'text'); ?>><?php esc_html_e('Keep Open & Display "Offer Expired"', 'wppoppop'); ?></option>
                        <option value="redirect" <?php selected($action, 'redirect'); ?>><?php esc_html_e('Redirect Visitor to URL', 'wppoppop'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_countdown_redir"><?php esc_html_e('Expiration Redirect URL', 'wppoppop'); ?></label></th>
                <td>
                    <input type="url" name="_wppoppop_countdown_redir" id="wppoppop_countdown_redir" value="<?php echo esc_url($redir); ?>" class="large-text" placeholder="https://example.com/offer-closed">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_autoresponder_metabox(\WP_Post $post): void {
        $enabled      = get_post_meta($post->ID, '_wppoppop_autoresponder_enabled', true);
        $subject      = get_post_meta($post->ID, '_wppoppop_autoresponder_subject', true) ?: 'Thank you for your request!';
        $body         = get_post_meta($post->ID, '_wppoppop_autoresponder_body', true) ?: "Hi {name},\n\nThank you for getting in touch! Here are your requested materials:\n{download_link}\n\nBest regards,\nThe Team";
        $asset_url    = get_post_meta($post->ID, '_wppoppop_asset_file_url', true) ?: '';
        $expiry_hours = get_post_meta($post->ID, '_wppoppop_token_expiry_hours', true) ?: '24';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_autoresponder_enabled"><?php esc_html_e('Enable Autoresponder', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_autoresponder_enabled" id="wppoppop_autoresponder_enabled" value="1" <?php checked($enabled, '1'); ?>>
                        <?php esc_html_e('Automatically dispatch a welcome email with download links to subscribers', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_autoresponder_subject"><?php esc_html_e('Email Subject', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_autoresponder_subject" id="wppoppop_autoresponder_subject" value="<?php echo esc_attr($subject); ?>" class="large-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_autoresponder_body"><?php esc_html_e('Email Content Body', 'wppoppop'); ?></label></th>
                <td>
                    <textarea name="_wppoppop_autoresponder_body" id="wppoppop_autoresponder_body" rows="6" class="large-text"><?php echo esc_textarea($body); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_asset_file_url"><?php esc_html_e('Digital Asset File URL', 'wppoppop'); ?></label></th>
                <td>
                    <input type="url" name="_wppoppop_asset_file_url" id="wppoppop_asset_file_url" value="<?php echo esc_url($asset_url); ?>" class="large-text" placeholder="https://example.com/wp-content/uploads/guide.pdf">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_token_expiry_hours"><?php esc_html_e('Token Expiration Window (Hours)', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" name="_wppoppop_token_expiry_hours" id="wppoppop_token_expiry_hours" value="<?php echo esc_attr($expiry_hours); ?>" min="1" max="720" class="small-text">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_sms_metabox(\WP_Post $post): void {
        $sms_enabled = get_post_meta($post->ID, '_wppoppop_sms_enabled', true);
        $sid         = get_post_meta($post->ID, '_wppoppop_twilio_sid', true) ?: '';
        $token       = get_post_meta($post->ID, '_wppoppop_twilio_token', true) ?: '';
        $from_num    = get_post_meta($post->ID, '_wppoppop_twilio_from', true) ?: '';
        $to_num      = get_post_meta($post->ID, '_wppoppop_twilio_to', true) ?: '';
        $template    = get_post_meta($post->ID, '_wppoppop_sms_template', true) ?: "New Lead: {name} ({email}) on {popup_title}";
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_sms_enabled"><?php esc_html_e('Enable SMS Dispatch', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_sms_enabled" id="wppoppop_sms_enabled" value="1" <?php checked($sms_enabled, '1'); ?>>
                        <?php esc_html_e('Send an automated text message notification upon lead submission', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_twilio_sid"><?php esc_html_e('Twilio Account SID', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_twilio_sid" id="wppoppop_twilio_sid" value="<?php echo esc_attr($sid); ?>" class="large-text" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_twilio_token"><?php esc_html_e('Twilio Auth Token', 'wppoppop'); ?></label></th>
                <td>
                    <input type="password" name="_wppoppop_twilio_token" id="wppoppop_twilio_token" value="<?php echo esc_attr($token); ?>" class="large-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_twilio_from"><?php esc_html_e('From Number / Sender ID', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_twilio_from" id="wppoppop_twilio_from" value="<?php echo esc_attr($from_num); ?>" class="regular-text" placeholder="+1234567890">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_twilio_to"><?php esc_html_e('Recipient Alert Number', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_twilio_to" id="wppoppop_twilio_to" value="<?php echo esc_attr($to_num); ?>" class="regular-text" placeholder="+1987654321">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_sms_template"><?php esc_html_e('SMS Message Body', 'wppoppop'); ?></label></th>
                <td>
                    <textarea name="_wppoppop_sms_template" id="wppoppop_sms_template" rows="3" class="large-text"><?php echo esc_textarea($template); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_email_verify_metabox(\WP_Post $post): void {
        $verify_enabled = get_post_meta($post->ID, '_wppoppop_email_verify_enabled', true);
        $api_key        = get_post_meta($post->ID, '_wppoppop_email_verify_api_key', true) ?: '';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_email_verify_enabled"><?php esc_html_e('Enable Deliverability Verification', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_email_verify_enabled" id="wppoppop_email_verify_enabled" value="1" <?php checked($verify_enabled, '1'); ?>>
                        <?php esc_html_e('Verify email deliverability and block disposable/temporary mailboxes via Kickbox API', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_email_verify_api_key"><?php esc_html_e('Verification API Key', 'wppoppop'); ?></label></th>
                <td>
                    <input type="password" name="_wppoppop_email_verify_api_key" id="wppoppop_email_verify_api_key" value="<?php echo esc_attr($api_key); ?>" class="large-text" placeholder="live_... or test_...">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_payment_metabox(\WP_Post $post): void {
        $enabled     = get_post_meta($post->ID, '_wppoppop_payment_enabled', true);
        $secret_key  = get_post_meta($post->ID, '_wppoppop_stripe_secret_key', true) ?: '';
        $pub_key     = get_post_meta($post->ID, '_wppoppop_stripe_pub_key', true) ?: '';
        $amount      = get_post_meta($post->ID, '_wppoppop_payment_amount', true) ?: '19.99';
        $currency    = get_post_meta($post->ID, '_wppoppop_payment_currency', true) ?: 'usd';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_payment_enabled"><?php esc_html_e('Enable Stripe Payments', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_payment_enabled" id="wppoppop_payment_enabled" value="1" <?php checked($enabled, '1'); ?>>
                        <?php esc_html_e('Require credit card payment authorization upon popup form submission', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_payment_amount"><?php esc_html_e('Fixed Order Amount', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" step="0.01" name="_wppoppop_payment_amount" id="wppoppop_payment_amount" value="<?php echo esc_attr($amount); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_payment_currency"><?php esc_html_e('Currency Code', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_payment_currency" id="wppoppop_payment_currency" value="<?php echo esc_attr($currency); ?>" class="small-text" placeholder="usd">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_stripe_secret_key"><?php esc_html_e('Stripe Secret Key', 'wppoppop'); ?></label></th>
                <td>
                    <input type="password" name="_wppoppop_stripe_secret_key" id="wppoppop_stripe_secret_key" value="<?php echo esc_attr($secret_key); ?>" class="large-text" placeholder="sk_test_... or sk_live_...">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_stripe_pub_key"><?php esc_html_e('Stripe Publishable Key', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_stripe_pub_key" id="wppoppop_stripe_pub_key" value="<?php echo esc_attr($pub_key); ?>" class="large-text" placeholder="pk_test_... or pk_live_...">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_mailchimp_metabox(\WP_Post $post): void {
        $mc_enabled = get_post_meta($post->ID, '_wppoppop_mailchimp_enabled', true);
        $api_key    = get_post_meta($post->ID, '_wppoppop_mailchimp_api_key', true) ?: '';
        $list_id    = get_post_meta($post->ID, '_wppoppop_mailchimp_list_id', true) ?: '';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_mailchimp_enabled"><?php esc_html_e('Enable Mailchimp Sync', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_mailchimp_enabled" id="wppoppop_mailchimp_enabled" value="1" <?php checked($mc_enabled, '1'); ?>>
                        <?php esc_html_e('Automatically dispatch captured subscribers directly to Mailchimp API v3', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_mailchimp_api_key"><?php esc_html_e('Mailchimp API Key', 'wppoppop'); ?></label></th>
                <td>
                    <input type="password" name="_wppoppop_mailchimp_api_key" id="wppoppop_mailchimp_api_key" value="<?php echo esc_attr($api_key); ?>" class="large-text" placeholder="e.g. 1234567890abcdef1234567890abcdef-us6">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_mailchimp_list_id"><?php esc_html_e('Audience / List ID', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_mailchimp_list_id" id="wppoppop_mailchimp_list_id" value="<?php echo esc_attr($list_id); ?>" class="regular-text" placeholder="e.g. a1b2c3d4e5">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_privacy_metabox(\WP_Post $post): void {
        $anonymize_ip = get_post_meta($post->ID, '_wppoppop_privacy_anonymize_ip', true);
        $disable_db   = get_post_meta($post->ID, '_wppoppop_privacy_disable_db', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_privacy_anonymize_ip"><?php esc_html_e('Anonymize IP Addresses', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_privacy_anonymize_ip" id="wppoppop_privacy_anonymize_ip" value="1" <?php checked($anonymize_ip, '1'); ?>>
                        <?php esc_html_e('Mask visitor IP addresses (e.g. 192.168.1.0) before writing to the database and dispatches', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_privacy_disable_db"><?php esc_html_e('Do Not Store in Local Database', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_privacy_disable_db" id="wppoppop_privacy_disable_db" value="1" <?php checked($disable_db, '1'); ?>>
                        <?php esc_html_e('Forward submissions directly to Webhooks & Mailchimp without storing personal lead data in WordPress', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_math_metabox(\WP_Post $post): void {
        $math_enabled   = get_post_meta($post->ID, '_wppoppop_math_enabled', true);
        $unit_price     = get_post_meta($post->ID, '_wppoppop_math_unit_price', true) ?: '25.00';
        $currency       = get_post_meta($post->ID, '_wppoppop_math_currency', true) ?: '$';
        $cond_enabled   = get_post_meta($post->ID, '_wppoppop_cond_enabled', true);
        $cond_threshold = get_post_meta($post->ID, '_wppoppop_cond_threshold', true) ?: '3';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_math_enabled"><?php esc_html_e('Enable Dynamic Math Calculations', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_math_enabled" id="wppoppop_math_enabled" value="1" <?php checked($math_enabled, '1'); ?>>
                        <?php esc_html_e('Calculate live pricing/quotes in real-time based on quantity selector', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_math_unit_price"><?php esc_html_e('Base Unit Price / Multiplier', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" step="0.01" name="_wppoppop_math_unit_price" id="wppoppop_math_unit_price" value="<?php echo esc_attr($unit_price); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_math_currency"><?php esc_html_e('Currency Symbol', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_math_currency" id="wppoppop_math_currency" value="<?php echo esc_attr($currency); ?>" class="small-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_cond_enabled"><?php esc_html_e('Enable Conditional Logic', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_cond_enabled" id="wppoppop_cond_enabled" value="1" <?php checked($cond_enabled, '1'); ?>>
                        <?php esc_html_e('Dynamically display enterprise company field when quantity reaches threshold', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_cond_threshold"><?php esc_html_e('Quantity Threshold for Conditional Field', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" name="_wppoppop_cond_threshold" id="wppoppop_cond_threshold" value="<?php echo esc_attr($cond_threshold); ?>" min="1" max="100" class="small-text">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_wc_metabox(\WP_Post $post): void {
        $wc_rule     = get_post_meta($post->ID, '_wppoppop_wc_rule', true) ?: 'all';
        $min_cart    = get_post_meta($post->ID, '_wppoppop_wc_min_cart', true) ?: '';
        $is_wc       = WooCommerceManager::is_wc_active();
        ?>
        <table class="form-table">
            <?php if (!$is_wc): ?>
                <tr>
                    <td colspan="2">
                        <div class="notice notice-warning inline" style="margin:0 0 12px;">
                            <p><?php esc_html_e('WooCommerce is not currently active on this site. Rules configured here will automatically apply once WooCommerce is enabled.', 'wppoppop'); ?></p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <th scope="row"><label for="wppoppop_wc_rule"><?php esc_html_e('WooCommerce Target Context', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_wc_rule" id="wppoppop_wc_rule">
                        <option value="all" <?php selected($wc_rule, 'all'); ?>><?php esc_html_e('Standard (Ignore WooCommerce status)', 'wppoppop'); ?></option>
                        <option value="cart_abandonment" <?php selected($wc_rule, 'cart_abandonment'); ?>><?php esc_html_e('Cart Abandonment (Cart/Checkout with active items)', 'wppoppop'); ?></option>
                        <option value="products_only" <?php selected($wc_rule, 'products_only'); ?>><?php esc_html_e('Single Product Pages Only', 'wppoppop'); ?></option>
                        <option value="cart_checkout_only" <?php selected($wc_rule, 'cart_checkout_only'); ?>><?php esc_html_e('Cart & Checkout Pages Only', 'wppoppop'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_wc_min_cart"><?php esc_html_e('Minimum Cart Total ($)', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" step="0.01" name="_wppoppop_wc_min_cart" id="wppoppop_wc_min_cart" value="<?php echo esc_attr($min_cart); ?>" class="regular-text" placeholder="0.00">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_param_metabox(\WP_Post $post): void {
        $param_key   = get_post_meta($post->ID, '_wppoppop_url_param_key', true) ?: '';
        $param_val   = get_post_meta($post->ID, '_wppoppop_url_param_val', true) ?: '';
        $freq_limit  = get_post_meta($post->ID, '_wppoppop_freq_limit', true) ?: '0';
        $prepopulate = get_post_meta($post->ID, '_wppoppop_prepopulate', true);
        $prepopulate = ($prepopulate === '') ? '1' : $prepopulate;
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_prepopulate"><?php esc_html_e('Dynamic URL Pre-population', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_prepopulate" id="wppoppop_prepopulate" value="1" <?php checked($prepopulate, '1'); ?>>
                        <?php esc_html_e('Automatically pre-fill name and email input fields from URL parameters (?name=...&email=...)', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_freq_limit"><?php esc_html_e('Frequency Capping (Max Impressions)', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" name="_wppoppop_freq_limit" id="wppoppop_freq_limit" value="<?php echo esc_attr($freq_limit); ?>" min="0" max="100">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_url_param_key"><?php esc_html_e('URL Query Key Requirement', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_url_param_key" id="wppoppop_url_param_key" value="<?php echo esc_attr($param_key); ?>" class="regular-text" placeholder="e.g. utm_campaign">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_url_param_val"><?php esc_html_e('URL Query Value Requirement', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_url_param_val" id="wppoppop_url_param_val" value="<?php echo esc_attr($param_val); ?>" class="regular-text" placeholder="e.g. holiday_sale">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_remote_embed_metabox(\WP_Post $post): void {
        $embed_script_url = add_query_arg(['wppoppop_embed' => $post->ID], home_url('/'));
        $embed_tag = '<script async src="' . esc_url($embed_script_url) . '"></script>';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label><?php esc_html_e('Remote Embed Snippet', 'wppoppop'); ?></label></th>
                <td>
                    <textarea readonly class="large-text" rows="2" style="font-family:monospace;" onclick="this.select();"><?php echo esc_textarea($embed_tag); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_tracking_metabox(\WP_Post $post): void {
        $ga_tracking  = get_post_meta($post->ID, '_wppoppop_ga_tracking', true);
        $js_on_open   = get_post_meta($post->ID, '_wppoppop_js_on_open', true) ?: '';
        $js_on_submit = get_post_meta($post->ID, '_wppoppop_js_on_submit', true) ?: '';
        $js_on_close  = get_post_meta($post->ID, '_wppoppop_js_on_close', true) ?: '';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_ga_tracking"><?php esc_html_e('Analytics Integration', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_ga_tracking" id="wppoppop_ga_tracking" value="1" <?php checked($ga_tracking, '1'); ?>>
                        <?php esc_html_e('Automatically dispatch events to GA4 (gtag), Google Tag Manager (dataLayer), and Meta Pixel (fbq)', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_js_on_open"><?php esc_html_e('Custom JS: OnOpen', 'wppoppop'); ?></label></th>
                <td>
                    <textarea name="_wppoppop_js_on_open" id="wppoppop_js_on_open" rows="3" class="large-text" style="font-family:monospace;"><?php echo esc_textarea($js_on_open); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_js_on_submit"><?php esc_html_e('Custom JS: OnSubmit', 'wppoppop'); ?></label></th>
                <td>
                    <textarea name="_wppoppop_js_on_submit" id="wppoppop_js_on_submit" rows="3" class="large-text" style="font-family:monospace;"><?php echo esc_textarea($js_on_submit); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_js_on_close"><?php esc_html_e('Custom JS: OnClose', 'wppoppop'); ?></label></th>
                <td>
                    <textarea name="_wppoppop_js_on_close" id="wppoppop_js_on_close" rows="3" class="large-text" style="font-family:monospace;"><?php echo esc_textarea($js_on_close); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_multistep_metabox(\WP_Post $post): void {
        $multistep      = get_post_meta($post->ID, '_wppoppop_multistep_enabled', true);
        $step1_question = get_post_meta($post->ID, '_wppoppop_step1_question', true) ?: 'Would you like an exclusive 20% discount today?';
        $step1_opt1     = get_post_meta($post->ID, '_wppoppop_step1_opt1', true) ?: 'Yes, I want the discount!';
        $step1_opt2     = get_post_meta($post->ID, '_wppoppop_step1_opt2', true) ?: 'No thanks, I prefer paying full price';
        $step3_coupon   = get_post_meta($post->ID, '_wppoppop_step3_coupon', true) ?: 'SAVE20NOW';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_multistep_enabled"><?php esc_html_e('Enable Multi-Step Funnel', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_multistep_enabled" id="wppoppop_multistep_enabled" value="1" <?php checked($multistep, '1'); ?>>
                        <?php esc_html_e('Convert popup into a 3-step progressive commitment flow', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_step1_question"><?php esc_html_e('Step 1: Hook Question', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_step1_question" id="wppoppop_step1_question" value="<?php echo esc_attr($step1_question); ?>" class="large-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_step1_opt1"><?php esc_html_e('Step 1: Affirmative Button', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_step1_opt1" id="wppoppop_step1_opt1" value="<?php echo esc_attr($step1_opt1); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_step1_opt2"><?php esc_html_e('Step 1: Negative/Decline Link', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_step1_opt2" id="wppoppop_step1_opt2" value="<?php echo esc_attr($step1_opt2); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_step3_coupon"><?php esc_html_e('Step 3: Reward Voucher Code', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_step3_coupon" id="wppoppop_step3_coupon" value="<?php echo esc_attr($step3_coupon); ?>" class="regular-text">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_geo_metabox(\WP_Post $post): void {
        $geo_mode      = get_post_meta($post->ID, '_wppoppop_geo_mode', true) ?: 'all';
        $geo_countries = get_post_meta($post->ID, '_wppoppop_geo_countries', true) ?: '';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_geo_mode"><?php esc_html_e('Geolocation Rule', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_geo_mode" id="wppoppop_geo_mode">
                        <option value="all" <?php selected($geo_mode, 'all'); ?>><?php esc_html_e('All Countries (Worldwide)', 'wppoppop'); ?></option>
                        <option value="allow" <?php selected($geo_mode, 'allow'); ?>><?php esc_html_e('Show Only in Selected Countries (Whitelist)', 'wppoppop'); ?></option>
                        <option value="block" <?php selected($geo_mode, 'block'); ?>><?php esc_html_e('Hide in Selected Countries (Blacklist)', 'wppoppop'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_geo_countries"><?php esc_html_e('Target Country Codes', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_geo_countries" id="wppoppop_geo_countries" value="<?php echo esc_attr($geo_countries); ?>" class="large-text" placeholder="US, GB, CA, TH, DE">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_ab_metabox(\WP_Post $post): void {
        $ab_enabled  = get_post_meta($post->ID, '_wppoppop_ab_enabled', true) === '1';
        $variant_id  = absint(get_post_meta($post->ID, '_wppoppop_ab_variant_id', true));
        $split_ratio = (int)(get_post_meta($post->ID, '_wppoppop_ab_split_ratio', true) ?: 50);

        $other_popups = get_posts([
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'exclude'        => [$post->ID],
        ]);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_ab_enabled"><?php esc_html_e('Enable A/B Campaign', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_ab_enabled" id="wppoppop_ab_enabled" value="1" <?php checked($ab_enabled, true); ?>>
                        <?php esc_html_e('Split incoming visitor traffic with an alternative variant', 'wppoppop'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_ab_variant_id"><?php esc_html_e('Select Variant B', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_ab_variant_id" id="wppoppop_ab_variant_id">
                        <option value=""><?php esc_html_e('— None —', 'wppoppop'); ?></option>
                        <?php foreach ($other_popups as $p): ?>
                            <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($variant_id, $p->ID); ?>>
                                <?php echo esc_html($p->post_title . ' (#' . $p->ID . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_ab_split_ratio"><?php esc_html_e('Variant B Split Ratio (%)', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" name="_wppoppop_ab_split_ratio" id="wppoppop_ab_split_ratio" value="<?php echo esc_attr($split_ratio); ?>" min="1" max="99">
                </td>
            </tr>
        </table>

        <?php if ($ab_enabled && $variant_id): 
            $stats = ABTestingManager::get_variant_stats($post->ID, $variant_id);
            $var_b_title = get_the_title($variant_id);
        ?>
            <hr style="margin:20px 0; border:0; border-top:1px solid #e2e8f0;">
            <h4 style="margin:0 0 12px; font-size:14px; font-weight:600;"><?php esc_html_e('Live Campaign Performance', 'wppoppop'); ?></h4>
            <table class="widefat striped" style="max-width:680px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Variant', 'wppoppop'); ?></th>
                        <th><?php esc_html_e('Popup Name', 'wppoppop'); ?></th>
                        <th><?php esc_html_e('Impressions', 'wppoppop'); ?></th>
                        <th><?php esc_html_e('Submissions', 'wppoppop'); ?></th>
                        <th><?php esc_html_e('Conversion Rate', 'wppoppop'); ?></th>
                        <th><?php esc_html_e('Status', 'wppoppop'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>A (Control)</strong></td>
                        <td><?php echo esc_html($post->post_title); ?></td>
                        <td><?php echo esc_html(number_format_i18n($stats['A']['impressions'])); ?></td>
                        <td><?php echo esc_html(number_format_i18n($stats['A']['submissions'])); ?></td>
                        <td><strong><?php echo esc_html($stats['A']['conversion_rate']); ?>%</strong></td>
                        <td>
                            <?php if ($stats['winner'] === 'A'): ?>
                                <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:4px;font-weight:700;font-size:11px;">LEADING</span>
                            <?php else: ?>
                                <span style="color:#64748b;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>B (Challenger)</strong></td>
                        <td><?php echo esc_html($var_b_title); ?></td>
                        <td><?php echo esc_html(number_format_i18n($stats['B']['impressions'])); ?></td>
                        <td><?php echo esc_html(number_format_i18n($stats['B']['submissions'])); ?></td>
                        <td><strong><?php echo esc_html($stats['B']['conversion_rate']); ?>%</strong></td>
                        <td>
                            <?php if ($stats['winner'] === 'B'): ?>
                                <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:4px;font-weight:700;font-size:11px;">LEADING</span>
                            <?php else: ?>
                                <span style="color:#64748b;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
        <?php
    }

    public function render_targeting_metabox(\WP_Post $post): void {
        wp_nonce_field('wppoppop_targeting_save', 'wppoppop_targeting_nonce');

        $target_rule  = get_post_meta($post->ID, '_wppoppop_target_rule', true) ?: 'all';
        $user_status  = get_post_meta($post->ID, '_wppoppop_user_status', true) ?: 'all';
        $exit_intent  = get_post_meta($post->ID, '_wppoppop_exit_intent', true);
        $exit_intent  = ($exit_intent === '') ? '1' : $exit_intent;
        $scroll_depth = get_post_meta($post->ID, '_wppoppop_scroll_depth', true) ?: '50';
        $inactivity   = get_post_meta($post->ID, '_wppoppop_inactivity', true) ?: '10';
        $adblock      = get_post_meta($post->ID, '_wppoppop_adblock', true);
        $optin_locker = get_post_meta($post->ID, '_wppoppop_optin_locker', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_target_rule"><?php esc_html_e('Display Location', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_target_rule" id="wppoppop_target_rule">
                        <option value="all" <?php selected($target_rule, 'all'); ?>>Entire Website</option>
                        <option value="posts" <?php selected($target_rule, 'posts'); ?>>All Single Posts</option>
                        <option value="pages" <?php selected($target_rule, 'pages'); ?>>All Pages</option>
                        <option value="frontpage" <?php selected($target_rule, 'frontpage'); ?>>Homepage / Front Page Only</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_user_status"><?php esc_html_e('Target Audience', 'wppoppop'); ?></label></th>
                <td>
                    <select name="_wppoppop_user_status" id="wppoppop_user_status">
                        <option value="all" <?php selected($user_status, 'all'); ?>>All Visitors</option>
                        <option value="logged_in" <?php selected($user_status, 'logged_in'); ?>>Logged-in Users Only</option>
                        <option value="logged_out" <?php selected($user_status, 'logged_out'); ?>>Guests (Logged-out) Only</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_exit_intent"><?php esc_html_e('Exit-Intent Trigger', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_exit_intent" id="wppoppop_exit_intent" value="1" <?php checked($exit_intent, '1'); ?>>
                        Trigger when cursor moves toward top boundary
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_adblock"><?php esc_html_e('OnAdBlockDetected Trigger', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_adblock" id="wppoppop_adblock" value="1" <?php checked($adblock, '1'); ?>>
                        Trigger immediately if any AdBlock extension is detected
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_optin_locker"><?php esc_html_e('Opt-In Locker Mode', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_optin_locker" id="wppoppop_optin_locker" value="1" <?php checked($optin_locker, '1'); ?>>
                        Disable close button and backdrop click (subscription required to dismiss)
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_scroll_depth"><?php esc_html_e('Scroll Depth (%)', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" name="_wppoppop_scroll_depth" id="wppoppop_scroll_depth" value="<?php echo esc_attr($scroll_depth); ?>" min="0" max="100">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_inactivity"><?php esc_html_e('Inactivity Seconds', 'wppoppop'); ?></label></th>
                <td>
                    <input type="number" name="_wppoppop_inactivity" id="wppoppop_inactivity" value="<?php echo esc_attr($inactivity); ?>" min="0" max="600">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_layer_metabox(\WP_Post $post): void {
        $badge = get_post_meta($post->ID, '_wppoppop_layer_badge', true) ?: 'SPECIAL OFFER';
        $cta   = get_post_meta($post->ID, '_wppoppop_layer_cta_text', true) ?: 'Subscribe Now';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_layer_badge"><?php esc_html_e('Top Badge Layer', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_layer_badge" id="wppoppop_layer_badge" value="<?php echo esc_attr($badge); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_layer_cta_text"><?php esc_html_e('Button CTA Label', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_layer_cta_text" id="wppoppop_layer_cta_text" value="<?php echo esc_attr($cta); ?>" class="regular-text">
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_optin_metabox(\WP_Post $post): void {
        $double_optin = get_post_meta($post->ID, '_wppoppop_double_optin', true);
        $subject      = get_post_meta($post->ID, '_wppoppop_double_optin_subject', true) ?: 'Please confirm your subscription';
        $body         = get_post_meta($post->ID, '_wppoppop_double_optin_body', true) ?: "Hi {name},\n\nPlease click the link below to confirm your subscription:\n{confirm_link}\n\nThank you!";
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wppoppop_double_optin"><?php esc_html_e('Enable Double Opt-In', 'wppoppop'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" name="_wppoppop_double_optin" id="wppoppop_double_optin" value="1" <?php checked($double_optin, '1'); ?>>
                        Send verification email and require subscriber confirmation
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_double_optin_subject"><?php esc_html_e('Confirmation Subject', 'wppoppop'); ?></label></th>
                <td>
                    <input type="text" name="_wppoppop_double_optin_subject" id="wppoppop_double_optin_subject" value="<?php echo esc_attr($subject); ?>" class="large-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wppoppop_double_optin_body"><?php esc_html_e('Confirmation Body', 'wppoppop'); ?></label></th>
                <td>
                    <textarea name="_wppoppop_double_optin_body" id="wppoppop_double_optin_body" rows="5" class="large-text"><?php echo esc_textarea($body); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    public function save_meta(int $post_id): void {
        if (!isset($_POST['wppoppop_targeting_nonce']) || !wp_verify_nonce($_POST['wppoppop_targeting_nonce'], 'wppoppop_targeting_save')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $target_rule  = sanitize_text_field($_POST['_wppoppop_target_rule'] ?? 'all');
        $user_status  = sanitize_text_field($_POST['_wppoppop_user_status'] ?? 'all');
        $exit_intent  = isset($_POST['_wppoppop_exit_intent']) ? '1' : '0';
        $scroll_depth = absint($_POST['_wppoppop_scroll_depth'] ?? 0);
        $inactivity   = absint($_POST['_wppoppop_inactivity'] ?? 0);
        $adblock      = isset($_POST['_wppoppop_adblock']) ? '1' : '0';
        $optin_locker = isset($_POST['_wppoppop_optin_locker']) ? '1' : '0';

        // Phase 26: Behavior & Sound Effects Settings
        $tab_switch   = isset($_POST['_wppoppop_tab_switch_enabled']) ? '1' : '0';
        $tab_flash    = sanitize_text_field($_POST['_wppoppop_tab_title_flash'] ?? '');
        $back_button  = isset($_POST['_wppoppop_back_button_enabled']) ? '1' : '0';
        $sound_fx     = isset($_POST['_wppoppop_sound_fx_enabled']) ? '1' : '0';

        // Phase 25: Social Proof Settings
        $sp_enabled   = isset($_POST['_wppoppop_sp_enabled']) ? '1' : '0';
        $sp_use_real  = isset($_POST['_wppoppop_sp_use_real']) ? '1' : '0';
        $sp_interval  = absint($_POST['_wppoppop_sp_interval'] ?? 8);
        $sp_duration  = absint($_POST['_wppoppop_sp_duration'] ?? 5);
        $sp_position  = sanitize_text_field($_POST['_wppoppop_sp_position'] ?? 'bottom-left');
        $sp_fallbacks = sanitize_textarea_field($_POST['_wppoppop_sp_fallbacks'] ?? '');

        // Phase 23: Custom Fields & Spam Settings
        $enable_dropdown   = isset($_POST['_wppoppop_enable_dropdown']) ? '1' : '0';
        $dropdown_label    = sanitize_text_field($_POST['_wppoppop_dropdown_label'] ?? '');
        $dropdown_options  = sanitize_textarea_field($_POST['_wppoppop_dropdown_options'] ?? '');

        $enable_datepicker = isset($_POST['_wppoppop_enable_datepicker']) ? '1' : '0';
        $datepicker_label  = sanitize_text_field($_POST['_wppoppop_datepicker_label'] ?? '');

        $enable_upload     = isset($_POST['_wppoppop_enable_file_upload']) ? '1' : '0';
        $upload_label      = sanitize_text_field($_POST['_wppoppop_file_upload_label'] ?? '');

        $turnstile_on       = isset($_POST['_wppoppop_turnstile_enabled']) ? '1' : '0';
        $turnstile_site_key = sanitize_text_field($_POST['_wppoppop_turnstile_site_key'] ?? '');
        $turnstile_sec_key  = sanitize_text_field($_POST['_wppoppop_turnstile_secret_key'] ?? '');

        $display_mode = sanitize_text_field($_POST['_wppoppop_display_mode'] ?? 'modal');
        $position     = sanitize_text_field($_POST['_wppoppop_position'] ?? 'center');
        $inline_mode  = sanitize_text_field($_POST['_wppoppop_inline_mode'] ?? 'none');
        $webhook_url  = esc_url_raw($_POST['_wppoppop_webhook_url'] ?? '');

        $wheel_enabled = isset($_POST['_wppoppop_wheel_enabled']) ? '1' : '0';
        $wheel_slices  = sanitize_textarea_field($_POST['_wppoppop_wheel_slices'] ?? '');

        $sch_enabled   = isset($_POST['_wppoppop_schedule_enabled']) ? '1' : '0';
        $sch_start     = sanitize_text_field($_POST['_wppoppop_schedule_start'] ?? '');
        $sch_end       = sanitize_text_field($_POST['_wppoppop_schedule_end'] ?? '');
        $sch_days      = array_map('sanitize_text_field', $_POST['_wppoppop_schedule_days'] ?? []);
        $sch_time_from = sanitize_text_field($_POST['_wppoppop_schedule_time_from'] ?? '');
        $sch_time_to   = sanitize_text_field($_POST['_wppoppop_schedule_time_to'] ?? '');

        $cd_enabled = isset($_POST['_wppoppop_countdown_enabled']) ? '1' : '0';
        $cd_type    = sanitize_text_field($_POST['_wppoppop_countdown_type'] ?? 'evergreen');
        $cd_mins    = absint($_POST['_wppoppop_countdown_mins'] ?? 15);
        $cd_date    = sanitize_text_field($_POST['_wppoppop_countdown_date'] ?? '');
        $cd_action  = sanitize_text_field($_POST['_wppoppop_countdown_action'] ?? 'hide');
        $cd_redir   = esc_url_raw($_POST['_wppoppop_countdown_redir'] ?? '');

        $ar_enabled   = isset($_POST['_wppoppop_autoresponder_enabled']) ? '1' : '0';
        $ar_subject   = sanitize_text_field($_POST['_wppoppop_autoresponder_subject'] ?? '');
        $ar_body      = sanitize_textarea_field($_POST['_wppoppop_autoresponder_body'] ?? '');
        $asset_url    = esc_url_raw($_POST['_wppoppop_asset_file_url'] ?? '');
        $expiry_hours = absint($_POST['_wppoppop_token_expiry_hours'] ?? 24);

        $sms_enabled  = isset($_POST['_wppoppop_sms_enabled']) ? '1' : '0';
        $sid          = sanitize_text_field($_POST['_wppoppop_twilio_sid'] ?? '');
        $token        = sanitize_text_field($_POST['_wppoppop_twilio_token'] ?? '');
        $from_num     = sanitize_text_field($_POST['_wppoppop_twilio_from'] ?? '');
        $to_num       = sanitize_text_field($_POST['_wppoppop_twilio_to'] ?? '');
        $template     = sanitize_textarea_field($_POST['_wppoppop_sms_template'] ?? '');

        $verify_enabled = isset($_POST['_wppoppop_email_verify_enabled']) ? '1' : '0';
        $api_key_verify = sanitize_text_field($_POST['_wppoppop_email_verify_api_key'] ?? '');

        $pay_enabled  = isset($_POST['_wppoppop_payment_enabled']) ? '1' : '0';
        $pay_amount   = sanitize_text_field($_POST['_wppoppop_payment_amount'] ?? '19.99');
        $pay_curr     = sanitize_text_field($_POST['_wppoppop_payment_currency'] ?? 'usd');
        $pay_sec_key  = sanitize_text_field($_POST['_wppoppop_stripe_secret_key'] ?? '');
        $pay_pub_key  = sanitize_text_field($_POST['_wppoppop_stripe_pub_key'] ?? '');

        $mc_enabled = isset($_POST['_wppoppop_mailchimp_enabled']) ? '1' : '0';
        $mc_api_key = sanitize_text_field($_POST['_wppoppop_mailchimp_api_key'] ?? '');
        $mc_list_id = sanitize_text_field($_POST['_wppoppop_mailchimp_list_id'] ?? '');

        $anonymize_ip = isset($_POST['_wppoppop_privacy_anonymize_ip']) ? '1' : '0';
        $disable_db   = isset($_POST['_wppoppop_privacy_disable_db']) ? '1' : '0';

        $wc_rule     = sanitize_text_field($_POST['_wppoppop_wc_rule'] ?? 'all');
        $wc_min_cart = sanitize_text_field($_POST['_wppoppop_wc_min_cart'] ?? '');

        $param_key   = sanitize_text_field($_POST['_wppoppop_url_param_key'] ?? '');
        $param_val   = sanitize_text_field($_POST['_wppoppop_url_param_val'] ?? '');
        $freq_limit  = absint($_POST['_wppoppop_freq_limit'] ?? 0);
        $prepopulate = isset($_POST['_wppoppop_prepopulate']) ? '1' : '0';

        $math_enabled   = isset($_POST['_wppoppop_math_enabled']) ? '1' : '0';
        $unit_price     = sanitize_text_field($_POST['_wppoppop_math_unit_price'] ?? '25.00');
        $currency       = sanitize_text_field($_POST['_wppoppop_math_currency'] ?? '$');
        $cond_enabled   = isset($_POST['_wppoppop_cond_enabled']) ? '1' : '0';
        $cond_threshold = absint($_POST['_wppoppop_cond_threshold'] ?? 3);

        $geo_mode      = sanitize_text_field($_POST['_wppoppop_geo_mode'] ?? 'all');
        $geo_countries = sanitize_text_field($_POST['_wppoppop_geo_countries'] ?? '');

        $ab_enabled   = isset($_POST['_wppoppop_ab_enabled']) ? '1' : '0';
        $variant_id   = absint($_POST['_wppoppop_ab_variant_id'] ?? 0);
        $split_ratio  = absint($_POST['_wppoppop_ab_split_ratio'] ?? 50);

        $layer_badge  = sanitize_text_field($_POST['_wppoppop_layer_badge'] ?? '');
        $layer_cta    = sanitize_text_field($_POST['_wppoppop_layer_cta_text'] ?? 'Subscribe Now');

        $double_optin = isset($_POST['_wppoppop_double_optin']) ? '1' : '0';
        $optin_sub    = sanitize_text_field($_POST['_wppoppop_double_optin_subject'] ?? '');
        $optin_body   = sanitize_textarea_field($_POST['_wppoppop_double_optin_body'] ?? '');

        $multistep      = isset($_POST['_wppoppop_multistep_enabled']) ? '1' : '0';
        $step1_question = sanitize_text_field($_POST['_wppoppop_step1_question'] ?? '');
        $step1_opt1     = sanitize_text_field($_POST['_wppoppop_step1_opt1'] ?? '');
        $step1_opt2     = sanitize_text_field($_POST['_wppoppop_step1_opt2'] ?? '');
        $step3_coupon   = sanitize_text_field($_POST['_wppoppop_step3_coupon'] ?? '');

        $ga_tracking  = isset($_POST['_wppoppop_ga_tracking']) ? '1' : '0';
        $js_on_open   = current_user_can('unfiltered_html') ? ($_POST['_wppoppop_js_on_open'] ?? '') : sanitize_textarea_field($_POST['_wppoppop_js_on_open'] ?? '');
        $js_on_submit = current_user_can('unfiltered_html') ? ($_POST['_wppoppop_js_on_submit'] ?? '') : sanitize_textarea_field($_POST['_wppoppop_js_on_submit'] ?? '');
        $js_on_close  = current_user_can('unfiltered_html') ? ($_POST['_wppoppop_js_on_close'] ?? '') : sanitize_textarea_field($_POST['_wppoppop_js_on_close'] ?? '');

        update_post_meta($post_id, '_wppoppop_target_rule', $target_rule);
        update_post_meta($post_id, '_wppoppop_user_status', $user_status);
        update_post_meta($post_id, '_wppoppop_exit_intent', $exit_intent);
        update_post_meta($post_id, '_wppoppop_scroll_depth', $scroll_depth);
        update_post_meta($post_id, '_wppoppop_inactivity', $inactivity);
        update_post_meta($post_id, '_wppoppop_adblock', $adblock);
        update_post_meta($post_id, '_wppoppop_optin_locker', $optin_locker);

        update_post_meta($post_id, '_wppoppop_tab_switch_enabled', $tab_switch);
        update_post_meta($post_id, '_wppoppop_tab_title_flash', $tab_flash);
        update_post_meta($post_id, '_wppoppop_back_button_enabled', $back_button);
        update_post_meta($post_id, '_wppoppop_sound_fx_enabled', $sound_fx);

        update_post_meta($post_id, '_wppoppop_sp_enabled', $sp_enabled);
        update_post_meta($post_id, '_wppoppop_sp_use_real', $sp_use_real);
        update_post_meta($post_id, '_wppoppop_sp_interval', $sp_interval);
        update_post_meta($post_id, '_wppoppop_sp_duration', $sp_duration);
        update_post_meta($post_id, '_wppoppop_sp_position', $sp_position);
        update_post_meta($post_id, '_wppoppop_sp_fallbacks', $sp_fallbacks);

        update_post_meta($post_id, '_wppoppop_enable_dropdown', $enable_dropdown);
        update_post_meta($post_id, '_wppoppop_dropdown_label', $dropdown_label);
        update_post_meta($post_id, '_wppoppop_dropdown_options', $dropdown_options);
        update_post_meta($post_id, '_wppoppop_enable_datepicker', $enable_datepicker);
        update_post_meta($post_id, '_wppoppop_datepicker_label', $datepicker_label);
        update_post_meta($post_id, '_wppoppop_enable_file_upload', $enable_upload);
        update_post_meta($post_id, '_wppoppop_file_upload_label', $upload_label);

        update_post_meta($post_id, '_wppoppop_turnstile_enabled', $turnstile_on);
        update_post_meta($post_id, '_wppoppop_turnstile_site_key', $turnstile_site_key);
        update_post_meta($post_id, '_wppoppop_turnstile_secret_key', $turnstile_sec_key);

        update_post_meta($post_id, '_wppoppop_display_mode', $display_mode);
        update_post_meta($post_id, '_wppoppop_position', $position);
        update_post_meta($post_id, '_wppoppop_inline_mode', $inline_mode);
        update_post_meta($post_id, '_wppoppop_webhook_url', $webhook_url);

        update_post_meta($post_id, '_wppoppop_wheel_enabled', $wheel_enabled);
        update_post_meta($post_id, '_wppoppop_wheel_slices', $wheel_slices);

        update_post_meta($post_id, '_wppoppop_schedule_enabled', $sch_enabled);
        update_post_meta($post_id, '_wppoppop_schedule_start', $sch_start);
        update_post_meta($post_id, '_wppoppop_schedule_end', $sch_end);
        update_post_meta($post_id, '_wppoppop_schedule_days', $sch_days);
        update_post_meta($post_id, '_wppoppop_schedule_time_from', $sch_time_from);
        update_post_meta($post_id, '_wppoppop_schedule_time_to', $sch_time_to);

        update_post_meta($post_id, '_wppoppop_countdown_enabled', $cd_enabled);
        update_post_meta($post_id, '_wppoppop_countdown_type', $cd_type);
        update_post_meta($post_id, '_wppoppop_countdown_mins', $cd_mins);
        update_post_meta($post_id, '_wppoppop_countdown_date', $cd_date);
        update_post_meta($post_id, '_wppoppop_countdown_action', $cd_action);
        update_post_meta($post_id, '_wppoppop_countdown_redir', $cd_redir);

        update_post_meta($post_id, '_wppoppop_autoresponder_enabled', $ar_enabled);
        update_post_meta($post_id, '_wppoppop_autoresponder_subject', $ar_subject);
        update_post_meta($post_id, '_wppoppop_autoresponder_body', $ar_body);
        update_post_meta($post_id, '_wppoppop_asset_file_url', $asset_url);
        update_post_meta($post_id, '_wppoppop_token_expiry_hours', $expiry_hours);

        update_post_meta($post_id, '_wppoppop_sms_enabled', $sms_enabled);
        update_post_meta($post_id, '_wppoppop_twilio_sid', $sid);
        update_post_meta($post_id, '_wppoppop_twilio_token', $token);
        update_post_meta($post_id, '_wppoppop_twilio_from', $from_num);
        update_post_meta($post_id, '_wppoppop_twilio_to', $to_num);
        update_post_meta($post_id, '_wppoppop_sms_template', $template);

        update_post_meta($post_id, '_wppoppop_email_verify_enabled', $verify_enabled);
        update_post_meta($post_id, '_wppoppop_email_verify_api_key', $api_key_verify);

        update_post_meta($post_id, '_wppoppop_payment_enabled', $pay_enabled);
        update_post_meta($post_id, '_wppoppop_payment_amount', $pay_amount);
        update_post_meta($post_id, '_wppoppop_payment_currency', $pay_curr);
        update_post_meta($post_id, '_wppoppop_stripe_secret_key', $pay_sec_key);
        update_post_meta($post_id, '_wppoppop_stripe_pub_key', $pay_pub_key);

        update_post_meta($post_id, '_wppoppop_mailchimp_enabled', $mc_enabled);
        update_post_meta($post_id, '_wppoppop_mailchimp_api_key', $mc_api_key);
        update_post_meta($post_id, '_wppoppop_mailchimp_list_id', $mc_list_id);

        update_post_meta($post_id, '_wppoppop_privacy_anonymize_ip', $anonymize_ip);
        update_post_meta($post_id, '_wppoppop_privacy_disable_db', $disable_db);

        update_post_meta($post_id, '_wppoppop_wc_rule', $wc_rule);
        update_post_meta($post_id, '_wppoppop_wc_min_cart', $wc_min_cart);

        update_post_meta($post_id, '_wppoppop_url_param_key', $param_key);
        update_post_meta($post_id, '_wppoppop_url_param_val', $param_val);
        update_post_meta($post_id, '_wppoppop_freq_limit', $freq_limit);
        update_post_meta($post_id, '_wppoppop_prepopulate', $prepopulate);

        update_post_meta($post_id, '_wppoppop_math_enabled', $math_enabled);
        update_post_meta($post_id, '_wppoppop_math_unit_price', $unit_price);
        update_post_meta($post_id, '_wppoppop_math_currency', $currency);
        update_post_meta($post_id, '_wppoppop_cond_enabled', $cond_enabled);
        update_post_meta($post_id, '_wppoppop_cond_threshold', $cond_threshold);

        update_post_meta($post_id, '_wppoppop_geo_mode', $geo_mode);
        update_post_meta($post_id, '_wppoppop_geo_countries', $geo_countries);

        update_post_meta($post_id, '_wppoppop_ab_enabled', $ab_enabled);
        update_post_meta($post_id, '_wppoppop_ab_variant_id', $variant_id);
        update_post_meta($post_id, '_wppoppop_ab_split_ratio', $split_ratio);

        update_post_meta($post_id, '_wppoppop_layer_badge', $layer_badge);
        update_post_meta($post_id, '_wppoppop_layer_cta_text', $layer_cta);

        update_post_meta($post_id, '_wppoppop_double_optin', $double_optin);
        update_post_meta($post_id, '_wppoppop_double_optin_subject', $optin_sub);
        update_post_meta($post_id, '_wppoppop_double_optin_body', $optin_body);

        update_post_meta($post_id, '_wppoppop_multistep_enabled', $multistep);
        update_post_meta($post_id, '_wppoppop_step1_question', $step1_question);
        update_post_meta($post_id, '_wppoppop_step1_opt1', $step1_opt1);
        update_post_meta($post_id, '_wppoppop_step1_opt2', $step1_opt2);
        update_post_meta($post_id, '_wppoppop_step3_coupon', $step3_coupon);

        update_post_meta($post_id, '_wppoppop_ga_tracking', $ga_tracking);
        update_post_meta($post_id, '_wppoppop_js_on_open', $js_on_open);
        update_post_meta($post_id, '_wppoppop_js_on_submit', $js_on_submit);
        update_post_meta($post_id, '_wppoppop_js_on_close', $js_on_close);
    }
}
