<?php
namespace WPPopPop\Admin;

use WPPopPop\Targeting\PopupPostType;
use WPPopPop\Targeting\TargetingManager;
use WPPopPop\Targeting\ABTestingManager;

class AdminPages {
    public static function render_header(string $title, array $actions = []): void {
        ?>
        <div class="wppoppop-header-row">
            <h1 class="wppoppop-main-title"><?php echo esc_html($title); ?></h1>
            <div class="wppoppop-header-actions">
                <?php foreach ($actions as $action): ?>
                    <a href="<?php echo esc_url($action['url']); ?>" class="wppoppop-outline-btn <?php echo esc_attr($action['class'] ?? ''); ?>" <?php echo !empty($action['target']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                        <?php echo esc_html($action['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /* 1. Popups Overview */
    public static function render_popups_page(): void {
        $popups = get_posts([
            'post_type'      => PopupPostType::POST_TYPE,
            'post_status'    => ['publish', 'draft'],
            'posts_per_page' => 50,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        ?>
        <div class="wrap wppoppop-admin-wrap">
            <?php self::render_header('Green Popups - Popups', [
                ['label' => 'Create New Popup', 'url' => admin_url('admin.php?page=wppoppop-add')],
                ['label' => 'Online Documentation', 'url' => 'https://greenpopups.com/documentation/', 'target' => true]
            ]); ?>

            <div class="wppoppop-filter-bar">
                <div class="wppoppop-filter-left">
                    <label>Search: <input type="text" class="wppoppop-search-input" id="wppoppop-search" placeholder="Search popups..." /></label>
                    <button type="button" class="wppoppop-outline-btn">Search</button>
                </div>
                <div class="wppoppop-filter-right">
                    <label>Sort: 
                        <select class="wppoppop-select" id="wppoppop-sort-popups">
                            <option value="created_desc">Created &#9660;</option>
                            <option value="title_asc">Title</option>
                            <option value="entries_desc">Entries</option>
                        </select>
                    </label>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-add')); ?>" class="wppoppop-btn-pink">+ Create New Popup</a>
                </div>
            </div>

            <div class="wppoppop-table-container">
                <table class="wppoppop-table" id="wppoppop-main-popups-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Name</th>
                            <th style="width: 35%;">Slug</th>
                            <th style="width: 15%; text-align: center;">Entries</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($popups)): ?>
                            <tr><td colspan="4" class="wppoppop-empty">List is empty.</td></tr>
                        <?php else: ?>
                            <?php foreach ($popups as $p): 
                                $slug = get_post_meta($p->ID, '_wppoppop_slug', true) ?: sanitize_title($p->post_title);
                                $status = $p->post_status === 'publish' ? 'ACTIVE' : 'INACTIVE';
                                $badge_class = $p->post_status === 'publish' ? 'wppoppop-badge-active' : 'wppoppop-badge-inactive';
                                $entries_count = (int) get_post_meta($p->ID, '_wppoppop_submissions_count', true);
                                $export_url = wp_nonce_url(admin_url('admin-post.php?action=wppoppop_export_popup_json&popup_id=' . $p->ID), 'wppoppop_export_popup');
                            ?>
                                <tr data-popup-id="<?php echo esc_attr($p->ID); ?>">
                                    <td>
                                        <div class="wppoppop-item-title">
                                            <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-add&id=' . $p->ID)); ?>" class="wppoppop-title-link"><?php echo esc_html($p->post_title); ?></a>
                                            <span class="wppoppop-badge <?php echo esc_attr($badge_class); ?>"><?php echo esc_html($status); ?></span>
                                        </div>
                                        <div class="wppoppop-item-meta">Created: <?php echo esc_html(get_the_date('Y-m-d H:i', $p)); ?></div>
                                    </td>
                                    <td>
                                        <div class="wppoppop-slug-box">
                                            <span class="dashicons dashicons-visibility wppoppop-icon-eye" data-id="<?php echo esc_attr($p->ID); ?>" title="Live Preview"></span>
                                            <input type="text" class="wppoppop-input-slug" value="<?php echo esc_attr($slug); ?>" readonly />
                                            <span class="dashicons dashicons-editor-code wppoppop-icon-code" data-shortcode="[wppoppop id=&quot;<?php echo esc_attr($p->ID); ?>&quot;]" title="Click to copy shortcode"></span>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&popup_id=' . $p->ID)); ?>" class="wppoppop-entries-count"><?php echo esc_html($entries_count); ?></a>
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="wppoppop-kebab-menu">
                                            <span class="dashicons dashicons-ellipsis wppoppop-kebab-trigger"></span>
                                            <div class="wppoppop-dropdown-menu">
                                                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-add&id=' . $p->ID)); ?>">Edit Popup</a>
                                                <a href="#" class="wppoppop-action-toggle-status" data-id="<?php echo esc_attr($p->ID); ?>">Toggle Status</a>
                                                <a href="#" class="wppoppop-action-duplicate" data-id="<?php echo esc_attr($p->ID); ?>">Duplicate</a>
                                                <a href="<?php echo esc_url($export_url); ?>">Export (JSON)</a>
                                                <a href="#" class="wppoppop-action-delete wppoppop-text-danger" data-id="<?php echo esc_attr($p->ID); ?>">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="wppoppop-footer-bar">
                <button type="button" class="wppoppop-btn-pink" id="wppoppop-import-btn"><span class="dashicons dashicons-upload"></span> Import Popup</button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-add')); ?>" class="wppoppop-btn-pink">+ Create New Popup</a>
            </div>

            <!-- Live Sandbox Preview Modal -->
            <div id="wppoppop-preview-modal" class="wppoppop-modal-backdrop" style="display:none;">
                <div class="wppoppop-modal-window" style="width:720px; max-width:95%;">
                    <div class="wppoppop-modal-header">
                        <h3 id="wppoppop-preview-title">Popup Live Sandbox Preview</h3>
                        <span class="wppoppop-modal-close" id="wppoppop-preview-modal-close">&times;</span>
                    </div>
                    <div class="wppoppop-modal-body" id="wppoppop-preview-content" style="background:#e2e8f0; min-height:440px; display:flex; align-items:center; justify-content:center; padding:24px;"></div>
                </div>
            </div>

            <!-- Import Popup JSON Modal -->
            <div id="wppoppop-import-modal" class="wppoppop-modal-backdrop" style="display:none;">
                <div class="wppoppop-modal-window">
                    <div class="wppoppop-modal-header">
                        <h3><?php echo esc_html__('Import Popup from JSON', 'wppoppop'); ?></h3>
                        <span class="wppoppop-modal-close" id="wppoppop-import-modal-close">&times;</span>
                    </div>
                    <form id="wppoppop-import-form">
                        <div class="wppoppop-modal-body">
                            <p style="font-size:13px; color:#475569; margin:0 0 10px 0;">
                                Select a previously exported <code>.json</code> popup configuration to restore all layers, targeting rules, and visual settings.
                            </p>
                            <input type="file" id="wppoppop-import-file" accept=".json" required class="wppoppop-input" style="padding:10px; background:#f8fafc;" />
                        </div>
                        <div class="wppoppop-modal-footer">
                            <button type="button" class="wppoppop-outline-btn" id="wppoppop-import-cancel">Cancel</button>
                            <button type="submit" class="wppoppop-btn-pink" id="wppoppop-import-submit-btn">Upload &amp; Import</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="wppoppop-admin-toast" class="wppoppop-admin-toast" style="display:none;"></div>
        </div>
        <?php
    }

    /* 2. Visual Builder Page */
    public static function render_create_page(): void {
        Builder::render_builder_page();
    }

    /* 3. A/B Campaigns Page */
    public static function render_campaigns_page(): void {
        $campaigns = get_posts([
            'post_type'      => ABTestingManager::CAMPAIGN_CPT,
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        $available_popups = get_posts([
            'post_type'      => PopupPostType::POST_TYPE,
            'post_status'    => ['publish', 'draft'],
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);
        ?>
        <div class="wrap wppoppop-admin-wrap">
            <?php self::render_header('Green Popups - A/B Campaigns', [
                ['label' => 'Create New Campaign', 'url' => '#create-campaign', 'class' => 'wppoppop-open-campaign-modal'],
                ['label' => 'Online Documentation', 'url' => 'https://greenpopups.com/documentation/', 'target' => true]
            ]); ?>

            <div class="wppoppop-filter-bar">
                <div class="wppoppop-filter-left">
                    <label>Search: <input type="text" class="wppoppop-search-input" id="wppoppop-campaign-search" /></label>
                    <button type="button" class="wppoppop-outline-btn">Search</button>
                </div>
                <div class="wppoppop-filter-right">
                    <label>Sort: <select class="wppoppop-select"><option>Created &#9660;</option></select></label>
                    <button type="button" class="wppoppop-btn-pink wppoppop-open-campaign-modal">+ Create New Campaign</button>
                </div>
            </div>

            <div class="wppoppop-table-container">
                <table class="wppoppop-table" id="wppoppop-campaigns-table">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Name</th>
                            <th style="width: 30%;">Slug</th>
                            <th style="width: 25%;">Popups</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($campaigns)): ?>
                            <tr><td colspan="4" class="wppoppop-empty">List is empty.</td></tr>
                        <?php else: ?>
                            <?php foreach ($campaigns as $c): 
                                $slug = get_post_meta($c->ID, '_wppoppop_campaign_slug', true) ?: $c->post_name;
                                $popup_ids = get_post_meta($c->ID, '_wppoppop_campaign_popups', true) ?: [];
                            ?>
                                <tr data-campaign-id="<?php echo esc_attr($c->ID); ?>">
                                    <td>
                                        <strong><?php echo esc_html($c->post_title); ?></strong>
                                        <div class="wppoppop-item-meta">Shortcode: <code>[wppoppop_campaign id="<?php echo esc_attr($c->ID); ?>"]</code></div>
                                    </td>
                                    <td>
                                        <div class="wppoppop-slug-box">
                                            <input type="text" class="wppoppop-input-slug" value="<?php echo esc_attr($slug); ?>" readonly />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="wppoppop-campaign-popups-list">
                                            <?php if (empty($popup_ids)): ?>
                                                <span style="color:#94a3b8;">None assigned</span>
                                            <?php else: ?>
                                                <?php foreach ($popup_ids as $pid): 
                                                    $p = get_post($pid);
                                                    if (!$p) continue;
                                                    $impressions = (int) get_post_meta($pid, '_wppoppop_impressions', true);
                                                    $submits     = (int) get_post_meta($pid, '_wppoppop_submissions', true);
                                                    $rate        = $impressions > 0 ? round(($submits / $impressions) * 100, 1) : 0;
                                                ?>
                                                    <div class="wppoppop-variant-badge">
                                                        <span class="wppoppop-variant-title"><?php echo esc_html($p->post_title); ?> (#<?php echo esc_html($pid); ?>)</span>
                                                        <span class="wppoppop-variant-stats"><?php echo esc_html($rate); ?>% conv (<?php echo esc_html($submits); ?>/<?php echo esc_html($impressions); ?>)</span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="dashicons dashicons-trash wppoppop-btn-del-campaign" data-id="<?php echo esc_attr($c->ID); ?>" title="Delete Campaign" style="cursor:pointer;color:#94a3b8;"></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Create Campaign Modal -->
            <div id="wppoppop-campaign-modal" class="wppoppop-modal-backdrop" style="display:none;">
                <div class="wppoppop-modal-window">
                    <div class="wppoppop-modal-header">
                        <h3><?php echo esc_html__('Create New A/B Campaign', 'wppoppop'); ?></h3>
                        <span class="wppoppop-modal-close" id="wppoppop-campaign-modal-close">&times;</span>
                    </div>
                    <form id="wppoppop-campaign-form">
                        <div class="wppoppop-modal-body">
                            <div class="wppoppop-form-group">
                                <label for="camp-title"><?php echo esc_html__('Campaign Name', 'wppoppop'); ?></label>
                                <input type="text" id="camp-title" name="title" class="wppoppop-input" required placeholder="e.g. Autumn Sale Split Test" />
                            </div>
                            <div class="wppoppop-form-group">
                                <label for="camp-slug"><?php echo esc_html__('Campaign Slug', 'wppoppop'); ?></label>
                                <input type="text" id="camp-slug" name="slug" class="wppoppop-input" placeholder="e.g. autumn-split" />
                            </div>
                            <div class="wppoppop-form-group">
                                <label><?php echo esc_html__('Select Variants to Include', 'wppoppop'); ?></label>
                                <div class="wppoppop-campaign-popups-picker">
                                    <?php foreach ($available_popups as $p): ?>
                                        <label class="wppoppop-checkbox-row">
                                            <input type="checkbox" name="popups[]" value="<?php echo esc_attr($p->ID); ?>" />
                                            <span><?php echo esc_html($p->post_title); ?> (ID: <?php echo esc_attr($p->ID); ?>)</span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="wppoppop-modal-footer">
                            <button type="button" class="wppoppop-outline-btn" id="wppoppop-campaign-cancel">Cancel</button>
                            <button type="submit" class="wppoppop-btn-pink" id="wppoppop-campaign-submit-btn">Save Campaign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    /* 4. Targeting Matrix Page */
    public static function render_targeting_page(): void {
        $matrix = TargetingManager::get_matrix();
        $all_popups = get_posts([
            'post_type'      => PopupPostType::POST_TYPE,
            'post_status'    => ['publish', 'draft'],
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC'
        ]);

        $events = [
            'onload'       => ['label' => 'OnLoad', 'icon' => '&#9673;'],
            'onscroll'     => ['label' => 'OnScroll', 'icon' => '&#9675;'],
            'onexit'       => ['label' => 'OnExit', 'icon' => '&#9675;'],
            'oninactivity' => ['label' => 'OnInactivity', 'icon' => '&#9675;'],
            'contentstart' => ['label' => 'ContentStart (inline)', 'icon' => '&#9675;'],
            'contentend'   => ['label' => 'ContentEnd (inline)', 'icon' => '&#9675;'],
        ];
        ?>
        <div class="wrap wppoppop-admin-wrap">
            <?php self::render_header('Green Popups - Targeting', [
                ['label' => 'Create New Target', 'url' => admin_url('admin.php?page=wppoppop-add')],
                ['label' => 'Online Documentation', 'url' => 'https://greenpopups.com/documentation/', 'target' => true]
            ]); ?>

            <div class="wppoppop-pill-nav" id="wppoppop-targeting-pills">
                <?php $first = true; foreach ($events as $slug => $data): ?>
                    <button type="button" class="wppoppop-pill-btn <?php echo $first ? 'active' : ''; ?>" data-event="<?php echo esc_attr($slug); ?>">
                        <span class="wppoppop-pill-dot"><?php echo $first ? '&#9673;' : '&#9675;'; ?></span> <?php echo esc_html($data['label']); ?>
                    </button>
                <?php $first = false; endforeach; ?>
            </div>

            <div class="wppoppop-target-section">
                <h3><?php echo esc_html__('Active Targets', 'wppoppop'); ?></h3>
                <div class="wppoppop-dropzone wppoppop-target-dropzone" id="wppoppop-active-zone" data-zone="active">
                    <div class="wppoppop-zone-empty" <?php echo !empty($matrix['onload']['active']) ? 'style="display:none;"' : ''; ?>>
                        Drop existing target here or <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-add')); ?>">create</a> new one.
                    </div>
                    <div class="wppoppop-targets-grid" id="wppoppop-active-grid"></div>
                </div>
            </div>

            <div class="wppoppop-target-section">
                <h3><?php echo esc_html__('Passive Targets', 'wppoppop'); ?></h3>
                <div class="wppoppop-dropzone wppoppop-target-dropzone" id="wppoppop-passive-zone" data-zone="passive">
                    <div class="wppoppop-zone-empty" <?php echo !empty($matrix['onload']['passive']) ? 'style="display:none;"' : ''; ?>>
                        Drop existing target here to disable it.
                    </div>
                    <div class="wppoppop-targets-grid" id="wppoppop-passive-grid"></div>
                </div>
            </div>
        </div>

        <script>
            window.wppoppopTargetingMatrix = <?php echo wp_json_encode($matrix); ?>;
            window.wppoppopAvailablePopups = <?php 
                $popups_data = [];
                foreach ($all_popups as $p) {
                    $popups_data[] = ['id' => $p->ID, 'title' => $p->post_title, 'status' => $p->post_status];
                }
                echo wp_json_encode($popups_data);
            ?>;
        </script>
        <?php
    }

    /* 5. Log Submissions Page matching Menu-Log.png */
    public static function render_log_page(): void {
        $filter_popup = isset($_GET['popup_id']) ? absint($_GET['popup_id']) : 0;
        $search = sanitize_text_field($_GET['s'] ?? '');

        $args = [
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if ($filter_popup > 0) {
            $args['meta_key']   = '_wppoppop_lead_popup_id';
            $args['meta_value'] = $filter_popup;
        }

        if (!empty($search)) {
            $args['s'] = $search;
        }

        $leads = get_posts($args);
        $all_popups = get_posts(['post_type' => PopupPostType::POST_TYPE, 'posts_per_page' => 100]);
        ?>
        <div class="wrap wppoppop-admin-wrap">
            <?php self::render_header('Green Popups - Log', [
                ['label' => 'Online Documentation', 'url' => 'https://greenpopups.com/documentation/', 'target' => true]
            ]); ?>

            <div class="wppoppop-filter-bar">
                <form method="get" class="wppoppop-filter-left">
                    <input type="hidden" name="page" value="wppoppop-log" />
                    <?php if ($filter_popup > 0): ?>
                        <input type="hidden" name="popup_id" value="<?php echo esc_attr($filter_popup); ?>" />
                    <?php endif; ?>
                    <label>Search: <input type="text" name="s" class="wppoppop-search-input" value="<?php echo esc_attr($search); ?>" placeholder="Search email/name..." /></label>
                    <button type="submit" class="wppoppop-outline-btn">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log' . ($filter_popup ? '&popup_id=' . $filter_popup : ''))); ?>" class="wppoppop-outline-btn" style="color:#64748b;border-color:#cbd5e1;">Clear</a>
                    <?php endif; ?>
                </form>
                <div class="wppoppop-filter-right">
                    <label>Filter: 
                        <select class="wppoppop-select" id="wppoppop-log-popup-filter" onchange="location = this.value;">
                            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log')); ?>">All Popups</option>
                            <?php foreach ($all_popups as $ap): ?>
                                <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&popup_id=' . $ap->ID)); ?>" <?php selected($filter_popup, $ap->ID); ?>>
                                    <?php echo esc_html($ap->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <span class="dashicons dashicons-admin-generic wppoppop-settings-icon" id="wppoppop-log-config-trigger" title="Log Configuration" style="cursor:pointer;color:#64748b;"></span>
                </div>
            </div>

            <div class="wppoppop-table-container">
                <table class="wppoppop-table" id="wppoppop-leads-table">
                    <thead>
                        <tr>
                            <th style="width: 4%;"><input type="checkbox" id="wppoppop-select-all-leads" /></th>
                            <th style="width: 8%;">ID</th>
                            <th style="width: 25%;">Primary Field</th>
                            <th style="width: 23%;">Secondray Field</th>
                            <th style="width: 20%;">Popup</th>
                            <th style="width: 10%;">Amount</th>
                            <th style="width: 10%;">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leads)): ?>
                            <tr><td colspan="7" class="wppoppop-empty">List is empty.</td></tr>
                        <?php else: ?>
                            <?php foreach ($leads as $lead): 
                                $pid    = (int) get_post_meta($lead->ID, '_wppoppop_lead_popup_id', true);
                                $name   = get_post_meta($lead->ID, '_wppoppop_lead_name', true) ?: '—';
                                $amount = get_post_meta($lead->ID, '_wppoppop_amount', true) ?: '0.00';
                            ?>
                                <tr data-lead-id="<?php echo esc_attr($lead->ID); ?>">
                                    <td><input type="checkbox" class="wppoppop-lead-chk" value="<?php echo esc_attr($lead->ID); ?>" /></td>
                                    <td>#<?php echo esc_html($lead->ID); ?></td>
                                    <td>
                                        <a href="#" class="wppoppop-open-lead-detail" data-lead-id="<?php echo esc_attr($lead->ID); ?>" style="color:#2271b1;font-weight:600;text-decoration:none;">
                                            <?php echo esc_html($lead->post_title); ?>
                                        </a>
                                    </td>
                                    <td><?php echo esc_html($name); ?></td>
                                    <td><?php echo esc_html($pid ? get_the_title($pid) : '—'); ?></td>
                                    <td>$<?php echo esc_html(number_format((float)$amount, 2)); ?></td>
                                    <td><?php echo esc_html(get_the_date('Y-m-d H:i', $lead)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="wppoppop-footer-bar" style="justify-content: flex-end;">
                <button type="button" class="wppoppop-btn-pink" id="wppoppop-delete-leads-btn"><span class="dashicons dashicons-trash"></span> Delete Selected</button>
            </div>

            <!-- Lead Submission Inspector Modal -->
            <div id="wppoppop-lead-modal" class="wppoppop-modal-backdrop" style="display:none;">
                <div class="wppoppop-modal-window">
                    <div class="wppoppop-modal-header">
                        <h3><?php echo esc_html__('Submission Record Telemetry', 'wppoppop'); ?></h3>
                        <span class="wppoppop-modal-close" id="wppoppop-lead-modal-close">&times;</span>
                    </div>
                    <div class="wppoppop-modal-body" id="wppoppop-lead-modal-body">
                        <p style="color:#64748b;">Loading record details...</p>
                    </div>
                    <div class="wppoppop-modal-footer">
                        <button type="button" class="wppoppop-outline-btn" id="wppoppop-lead-modal-dismiss">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /* 6. Stats Page matching Menu-Stats.png */
    public static function render_stats_page(): void {
        $popup_id   = isset($_GET['popup_id']) ? absint($_GET['popup_id']) : 0;
        $start_date = sanitize_text_field($_GET['start_date'] ?? gmdate('Y-m-01'));
        $end_date   = sanitize_text_field($_GET['end_date'] ?? gmdate('Y-m-t'));

        $timeline = StatsManager::get_timeline_data($popup_id, $start_date, $end_date);
        $popups   = get_posts(['post_type' => PopupPostType::POST_TYPE, 'posts_per_page' => 100]);

        $total_impressions = 0;
        $total_submits     = 0;
        $total_confirmed   = 0;
        $total_payments    = 0;

        foreach ($timeline as $day) {
            $total_impressions += $day['impressions'];
            $total_submits     += $day['submits'];
            $total_confirmed   += $day['confirmed'];
            $total_payments    += $day['payments'];
        }

        $points_impressions = StatsManager::build_svg_points($timeline, 'impressions', 940, 200, 200);
        $points_submits     = StatsManager::build_svg_points($timeline, 'submits', 940, 200, 200);
        $points_confirmed   = StatsManager::build_svg_points($timeline, 'confirmed', 940, 200, 200);
        $points_payments    = StatsManager::build_svg_points($timeline, 'payments', 940, 200, 200);

        $dates = array_keys($timeline);
        $date_count = count($dates);
        $step_x = $date_count > 1 ? (940 / ($date_count - 1)) : 940;
        ?>
        <div class="wrap wppoppop-admin-wrap">
            <?php self::render_header('Green Popups - Stats', [
                ['label' => 'Online Documentation', 'url' => 'https://greenpopups.com/documentation/', 'target' => true]
            ]); ?>

            <form method="get" class="wppoppop-filter-bar wppoppop-stats-controls">
                <input type="hidden" name="page" value="wppoppop-stats" />
                <div class="wppoppop-filter-left" style="gap: 15px;">
                    <div>
                        <select name="popup_id" class="wppoppop-select" style="min-width: 200px;">
                            <option value="0">All Popups</option>
                            <?php foreach ($popups as $p): ?>
                                <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($popup_id, $p->ID); ?>><?php echo esc_html($p->post_title); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="wppoppop-sublabel">Popup</div>
                    </div>
                    <div>
                        <input type="date" name="start_date" class="wppoppop-date-input" value="<?php echo esc_attr($start_date); ?>" />
                        <div class="wppoppop-sublabel">Start date</div>
                    </div>
                    <div>
                        <input type="date" name="end_date" class="wppoppop-date-input" value="<?php echo esc_attr($end_date); ?>" />
                        <div class="wppoppop-sublabel">End date</div>
                    </div>
                    <button type="submit" class="wppoppop-btn-apply">&#10003; Apply</button>
                </div>
            </form>

            <div class="wppoppop-chart-wrapper">
                <div class="wppoppop-chart-legend">
                    <span class="wppoppop-legend-item"><span class="legend-color" style="background:#ff5b77;"></span> Impressions: <strong><?php echo esc_html($total_impressions); ?></strong></span>
                    <span class="wppoppop-legend-item"><span class="legend-color" style="background:#f59e0b;"></span> Submits: <strong><?php echo esc_html($total_submits); ?></strong></span>
                    <span class="wppoppop-legend-item"><span class="legend-color" style="background:#14b8a6;"></span> Confirmed: <strong><?php echo esc_html($total_confirmed); ?></strong></span>
                    <span class="wppoppop-legend-item"><span class="legend-color" style="background:#a855f7;"></span> Payments: <strong><?php echo esc_html($total_payments); ?></strong></span>
                </div>
                <div class="wppoppop-chart-canvas-box" id="wppoppop-chart-box">
                    <svg class="wppoppop-chart-svg" viewBox="0 0 1020 320" preserveAspectRatio="none">
                        <!-- Horizontal Grid Lines -->
                        <line x1="40" y1="20" x2="980" y2="20" stroke="#f0f0f1" stroke-width="1" />
                        <text x="32" y="24" fill="#94a3b8" font-size="11" text-anchor="end">1.0</text>
                        <line x1="40" y1="65" x2="980" y2="65" stroke="#f0f0f1" stroke-width="1" />
                        <text x="32" y="69" fill="#94a3b8" font-size="11" text-anchor="end">0.8</text>
                        <line x1="40" y1="110" x2="980" y2="110" stroke="#f0f0f1" stroke-width="1" />
                        <text x="32" y="114" fill="#94a3b8" font-size="11" text-anchor="end">0.6</text>
                        <line x1="40" y1="155" x2="980" y2="155" stroke="#f0f0f1" stroke-width="1" />
                        <text x="32" y="159" fill="#94a3b8" font-size="11" text-anchor="end">0.2</text>
                        <line x1="40" y1="200" x2="980" y2="200" stroke="#e2e8f0" stroke-width="1.5" />
                        <text x="32" y="204" fill="#94a3b8" font-size="11" text-anchor="end">0</text>
                        <line x1="40" y1="245" x2="980" y2="245" stroke="#f0f0f1" stroke-width="1" />
                        <text x="32" y="249" fill="#94a3b8" font-size="11" text-anchor="end">-0.4</text>
                        <line x1="40" y1="290" x2="980" y2="290" stroke="#f0f0f1" stroke-width="1" />
                        <text x="32" y="294" fill="#94a3b8" font-size="11" text-anchor="end">-1.0</text>

                        <!-- Multi-Series Data Polylines -->
                        <polyline fill="none" stroke="#ff5b77" stroke-width="2" points="<?php echo esc_attr($points_impressions); ?>" />
                        <polyline fill="none" stroke="#f59e0b" stroke-width="2" stroke-dasharray="4" points="<?php echo esc_attr($points_submits); ?>" />
                        <polyline fill="none" stroke="#14b8a6" stroke-width="2" stroke-dasharray="2" points="<?php echo esc_attr($points_confirmed); ?>" />
                        <polyline fill="none" stroke="#a855f7" stroke-width="2" points="<?php echo esc_attr($points_payments); ?>" />

                        <!-- Tilted Date Labels along X-Axis matching Menu-Stats.png -->
                        <?php foreach ($dates as $idx => $date_val): 
                            $x_pos = round(40 + ($idx * $step_x));
                        ?>
                            <circle cx="<?php echo esc_attr($x_pos); ?>" cy="200" r="3" fill="#ff5b77" />
                            <text x="<?php echo esc_attr($x_pos); ?>" y="220" font-size="9" fill="#64748b" transform="rotate(45 <?php echo esc_attr($x_pos); ?>,220)" text-anchor="start">
                                <?php echo esc_html($date_val); ?>
                            </text>
                        <?php endforeach; ?>
                    </svg>
                </div>
            </div>
        </div>
        <?php
    }

    /* 7. Field Analytics Page matching Menu-Field Analytics.png */
    public static function render_analytics_page(): void {
        $popups = get_posts(['post_type' => PopupPostType::POST_TYPE, 'posts_per_page' => 100]);
        $selected_id = isset($_GET['popup_id']) ? absint($_GET['popup_id']) : 0;
        $stats = $selected_id ? FieldAnalyticsManager::get_field_stats($selected_id) : [];
        ?>
        <div class="wrap wppoppop-admin-wrap">
            <?php self::render_header('Green Popups - Field Analytics', [
                ['label' => 'Online Documentation', 'url' => 'https://greenpopups.com/documentation/', 'target' => true]
            ]); ?>

            <div class="wppoppop-filter-bar">
                <form method="get" class="wppoppop-filter-left" style="align-items: center; gap: 15px;">
                    <input type="hidden" name="page" value="wppoppop-analytics" />
                    <div>
                        <select name="popup_id" class="wppoppop-select" style="min-width: 220px;" onchange="this.form.submit();">
                            <option value="">Select the form</option>
                            <?php foreach ($popups as $p): ?>
                                <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($selected_id, $p->ID); ?>>
                                    <?php echo esc_html($p->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="wppoppop-sublabel">Popup</div>
                    </div>
                    <div style="text-align: center;">
                        <label class="wppoppop-switch"><input type="checkbox" name="period_toggle" value="1" /><span class="slider"></span></label>
                        <div class="wppoppop-sublabel">Period</div>
                    </div>
                    <button type="submit" class="wppoppop-btn-apply">&#10003; Apply</button>
                </form>
            </div>

            <?php if (!$selected_id || empty($stats['fields'])): ?>
                <div class="wppoppop-empty-view">No form selected.</div>
            <?php else: ?>
                <!-- Form Summary Header -->
                <div class="wppoppop-analytics-summary-cards">
                    <div class="wppoppop-summary-card">
                        <span class="wppoppop-card-metric"><?php echo esc_html($stats['impressions']); ?></span>
                        <span class="wppoppop-card-label">Total Impressions</span>
                    </div>
                    <div class="wppoppop-summary-card">
                        <span class="wppoppop-card-metric"><?php echo esc_html($stats['submissions']); ?></span>
                        <span class="wppoppop-card-label">Form Submissions</span>
                    </div>
                    <div class="wppoppop-summary-card">
                        <span class="wppoppop-card-metric" style="color:#059669;"><?php echo esc_html($stats['conversion_rate']); ?>%</span>
                        <span class="wppoppop-card-label">Conversion Rate</span>
                    </div>
                </div>

                <div class="wppoppop-table-container" style="margin-top: 20px;">
                    <table class="wppoppop-table">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Field Label</th>
                                <th style="width: 20%;">Type</th>
                                <th style="width: 15%; text-align:center;">Entries</th>
                                <th style="width: 30%;">Fill Rate Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['fields'] as $key => $f): ?>
                                <tr>
                                    <td><strong><?php echo esc_html($f['label']); ?></strong></td>
                                    <td><code><?php echo esc_html($f['type']); ?></code></td>
                                    <td style="text-align:center;"><?php echo esc_html($f['entries']); ?></td>
                                    <td>
                                        <div class="wppoppop-fill-track">
                                            <div class="wppoppop-fill-bar" style="width: <?php echo esc_attr($f['fill_rate']); ?>%;"></div>
                                            <span class="wppoppop-fill-text"><?php echo esc_html($f['fill_rate']); ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /* 8. Transactions Page matching Menu-Transactions .png */
    public static function render_transactions_page(): void {
        $search = sanitize_text_field($_GET['s'] ?? '');
        $args = [
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => [
                [
                    'key'     => '_wppoppop_amount',
                    'value'   => '0.00',
                    'compare' => '!=',
                ],
            ],
        ];

        if (!empty($search)) {
            $args['s'] = $search;
        }

        $transactions = get_posts($args);
        ?>
        <div class="wrap wppoppop-admin-wrap">
            <?php self::render_header('Green Popups - Transactions', [
                ['label' => 'Online Documentation', 'url' => 'https://greenpopups.com/documentation/', 'target' => true]
            ]); ?>

            <div class="wppoppop-filter-bar">
                <form method="get" class="wppoppop-filter-left">
                    <input type="hidden" name="page" value="wppoppop-transactions" />
                    <label>Search: <input type="text" name="s" class="wppoppop-search-input" value="<?php echo esc_attr($search); ?>" placeholder="Search payer..." /></label>
                    <button type="submit" class="wppoppop-outline-btn">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-transactions')); ?>" class="wppoppop-outline-btn" style="color:#64748b;border-color:#cbd5e1;">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="wppoppop-table-container">
                <table class="wppoppop-table" id="wppoppop-transactions-table">
                    <thead>
                        <tr>
                            <th style="width: 4%;"><input type="checkbox" id="wppoppop-select-all-tx" /></th>
                            <th style="width: 36%;">Payer</th>
                            <th style="width: 25%;">Status</th>
                            <th style="width: 20%;">Amount</th>
                            <th style="width: 15%;">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr><td colspan="5" class="wppoppop-empty">List is empty.</td></tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $tx): 
                                $status = get_post_meta($tx->ID, '_wppoppop_status', true) ?: 'Completed';
                                $amount = get_post_meta($tx->ID, '_wppoppop_amount', true) ?: '0.00';
                                $badge_class = ($status === 'Completed' || $status === 'Paid') ? 'wppoppop-badge-active' : 'wppoppop-badge-inactive';
                            ?>
                                <tr data-tx-id="<?php echo esc_attr($tx->ID); ?>">
                                    <td><input type="checkbox" class="wppoppop-tx-chk" value="<?php echo esc_attr($tx->ID); ?>" /></td>
                                    <td>
                                        <strong><?php echo esc_html($tx->post_title); ?></strong>
                                        <div class="wppoppop-item-meta">Tx ID: #<?php echo esc_html($tx->ID); ?></div>
                                    </td>
                                    <td><span class="wppoppop-badge <?php echo esc_attr($badge_class); ?>"><?php echo esc_html($status); ?></span></td>
                                    <td><strong>$<?php echo esc_html(number_format((float)$amount, 2)); ?></strong></td>
                                    <td><?php echo esc_html(get_the_date('Y-m-d H:i', $tx)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="wppoppop-footer-bar" style="justify-content: flex-end;">
                <button type="button" class="wppoppop-btn-pink" id="wppoppop-delete-tx-btn"><span class="dashicons dashicons-trash"></span> Delete Selected</button>
            </div>
        </div>
        <?php
    }

    /* 9. Template Library Page matching Menu-Library.jpg */
    public static function render_library_page(): void {
        $templates = [
            ['id' => '27124', 'name' => 'Fashawn Style Minimal', 'color' => '#f1f1f1'],
            ['id' => '27118', 'name' => 'Fresh Taters Organic', 'color' => '#fffbeb'],
            ['id' => '27100', 'name' => 'Tech Hardware Showcase', 'color' => '#18181b'],
            ['id' => '27091', 'name' => 'Interior Architecture', 'color' => '#fafafa'],
            ['id' => '27082', 'name' => 'Purple Gradient Form', 'color' => '#6366f1'],
            ['id' => '27079', 'name' => 'Contact Us Clean Dialog', 'color' => '#ffffff'],
            ['id' => '27063', 'name' => 'Sign In Compact Vector', 'color' => '#f8fafc'],
            ['id' => '27057', 'name' => 'Editorial Access Gate', 'color' => '#f5f5f4'],
        ];
        ?>
        <div class="wrap wppoppop-admin-wrap">
            <?php self::render_header('Green Popups - Library', [
                ['label' => 'Clear Library Cache', 'url' => '#clear-cache'],
                ['label' => 'Online Documentation', 'url' => 'https://greenpopups.com/documentation/', 'target' => true]
            ]); ?>

            <div class="wppoppop-library-grid">
                <?php foreach ($templates as $t): ?>
                    <div class="wppoppop-template-card">
                        <div class="wppoppop-template-badge">#<?php echo esc_html($t['id']); ?></div>
                        <div class="wppoppop-template-preview" style="background-color:<?php echo esc_attr($t['color']); ?>;">
                            <strong><?php echo esc_html($t['name']); ?></strong>
                        </div>
                        <div class="wppoppop-template-hover">
                            <button type="button" class="wppoppop-btn-pink wppoppop-btn-import-tpl" data-id="<?php echo esc_attr($t['id']); ?>">Use Template</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /* 10. General & Advanced Settings Page matching Menu-Settings-General.png */
    public static function render_settings_page(): void {
        $settings = SettingsManager::get_all();
        $export_url = wp_nonce_url(admin_url('admin-post.php?action=wppoppop_export_leads_csv'), 'wppoppop_export_leads');
        ?>
        <div class="wrap wppoppop-admin-wrap">
            <?php self::render_header('Green Popups - General Settings', [
                ['label' => 'Online Documentation', 'url' => 'https://greenpopups.com/documentation/', 'target' => true]
            ]); ?>

            <div class="wppoppop-settings-tabs" id="wppoppop-main-settings-tabs">
                <button type="button" class="wppoppop-tab-link active" data-tab="general">General</button>
                <button type="button" class="wppoppop-tab-link" data-tab="advanced">Advanced</button>
                <a href="<?php echo esc_url($export_url); ?>" class="wppoppop-outline-btn" style="margin-left:auto; display:flex; align-items:center; gap:6px;">
                    <span class="dashicons dashicons-download" style="font-size:16px;"></span> Export Leads (CSV)
                </a>
            </div>

            <form id="wppoppop-master-settings-form">
                <div class="wppoppop-settings-tab-pane active" id="pane-general">
                    <div class="wppoppop-section-header">Mailing Settings</div>
                    <table class="wppoppop-form-table">
                        <tr>
                            <th style="width: 220px;">Sender name:</th>
                            <td>
                                <input type="text" name="settings[sender_name]" class="wppoppop-input-full" value="<?php echo esc_attr($settings['sender_name']); ?>" />
                                <div class="wppoppop-field-desc">Please enter sender name. All messages from plugin are sent using this name as "FROM:" header value..</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Sender email:</th>
                            <td>
                                <input type="email" name="settings[sender_email]" class="wppoppop-input-full" value="<?php echo esc_attr($settings['sender_email']); ?>" />
                                <div class="wppoppop-field-desc">Please enter sender e-mail. All messages from plugin are sent using this e-mail as "FROM:" header value..</div>
                            </td>
                        </tr>
                    </table>

                    <div class="wppoppop-section-header">Miscellaneous</div>
                    <table class="wppoppop-form-table">
                        <tr>
                            <th style="width: 220px;">Pre-load popups:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[preload]" value="1" <?php checked($settings['preload'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Pre-load popups</span>
                                <div class="wppoppop-field-desc">Tick checkbox to pre-load popups (not recommended). If disabled, popups are pulled on demand using AJAX.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Pre-load event popups:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[preload_events]" value="1" <?php checked($settings['preload_events'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Pre-load event popups</span>
                                <div class="wppoppop-field-desc">If enabled, only event popups (OnLoad, OnExit, etc.) are loaded together with website. All other popups are pulled on demand using AJAX.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>GA tracking:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[ga_tracking]" value="1" <?php checked($settings['ga_tracking'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Enable Google Analytics tracking</span>
                                <div class="wppoppop-field-desc">Send form submission event to Google Analytics. GA must be installed on your website.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Google Fonts:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[google_fonts]" value="1" <?php checked($settings['google_fonts'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Enable Google Fonts</span>
                                <div class="wppoppop-field-desc">Turn this feature on if you want to use full set of Google Fonts loaded from Google servers.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Font Awesome:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[font_awesome]" value="1" <?php checked($settings['font_awesome'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Enable Font Awesome icons</span>
                                <div class="wppoppop-field-desc">Turn this feature on if you want to use set of Font Awesome icons.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Air Datepicker plugin:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[air_datepicker]" value="1" <?php checked($settings['air_datepicker'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Enable Air Datepicker plugin</span>
                                <div class="wppoppop-field-desc">Turn this feature on if you want to use nice datepicker with forms.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>jQuery Mask plugin:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[mask_plugin]" value="1" <?php checked($settings['mask_plugin'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Enable jQuery Mask plugin</span>
                                <div class="wppoppop-field-desc">Turn this feature on if you want to specify input masks for text fields.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>JavaScript Expression Parser:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[expression_parser]" value="1" <?php checked($settings['expression_parser'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Enable JavaScript Expression Parser plugin</span>
                                <div class="wppoppop-field-desc">Turn this feature on if you want to use math expressions on front-end side.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Signature Pad plugin:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[signature_pad]" value="1" <?php checked($settings['signature_pad'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Enable Signature Pad plugin</span>
                                <div class="wppoppop-field-desc">Turn this feature on if you want to use signature pad with forms.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Ion.RangeSlider plugin:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[rangeslider]" value="1" <?php checked($settings['rangeslider'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Enable Ion.RangeSlider plugin</span>
                                <div class="wppoppop-field-desc">Turn this feature on if you want to use range slider with forms.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>AdBlock detector:</th>
                            <td>
                                <label class="wppoppop-switch"><input type="checkbox" name="settings[adblock_detector]" value="1" <?php checked($settings['adblock_detector'], 1); ?> /><span class="slider"></span></label>
                                <span class="wppoppop-switch-label">Enable AdBlock detector</span>
                                <div class="wppoppop-field-desc">Turn this feature on if you want to use OnAdBlockDetected popups.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>CSV column separator:</th>
                            <td>
                                <select name="settings[csv_separator]" class="wppoppop-select" style="min-width: 140px;">
                                    <option value="," <?php selected($settings['csv_separator'], ','); ?>>Comma - ","</option>
                                    <option value=";" <?php selected($settings['csv_separator'], ';'); ?>>Semicolon - ";"</option>
                                    <option value="tab" <?php selected($settings['csv_separator'], 'tab'); ?>>Tab</option>
                                </select>
                                <div class="wppoppop-field-desc">Please select CSV column separator.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Custom local fonts:</th>
                            <td>
                                <textarea name="settings[custom_fonts]" class="wppoppop-textarea" rows="4"><?php echo esc_textarea($settings['custom_fonts']); ?></textarea>
                                <div class="wppoppop-field-desc">Set custom local font names (one name per line).</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Reset cookie:</th>
                            <td>
                                <button type="button" class="wppoppop-btn-pink" id="wppoppop-reset-cookies-btn">&#10006; Reset Cookies</button>
                                <div class="wppoppop-field-desc">Click the button to reset cookie. Popup will appear for all returning visitors.</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="wppoppop-settings-tab-pane" id="pane-advanced" style="display:none;">
                                                            <div class="wppoppop-section-header">Email Marketing & CRM Integrations (Mailchimp, ActiveCampaign, Brevo)</div>
                    <table class="wppoppop-form-table">
                        <tr>
                            <th style="width: 220px;">Active Marketing Service:</th>
                            <td>
                                <select name="settings[crm_provider]" class="wppoppop-select" id="wppoppop-crm-provider-select" style="min-width: 220px;">
                                    <option value="none" <?php selected($settings['crm_provider'] ?? 'none', 'none'); ?>>None (Store Leads Locally Only)</option>
                                    <option value="mailchimp" <?php selected($settings['crm_provider'] ?? '', 'mailchimp'); ?>>Mailchimp</option>
                                    <option value="activecampaign" <?php selected($settings['crm_provider'] ?? '', 'activecampaign'); ?>>ActiveCampaign</option>
                                    <option value="brevo" <?php selected($settings['crm_provider'] ?? '', 'brevo'); ?>>Brevo (Sendinblue)</option>
                                </select>
                                <div class="wppoppop-field-desc">Automatically pushes captured contacts to your designated subscriber list.</div>
                            </td>
                        </tr>
                        <!-- Mailchimp Fields -->
                        <tr>
                            <th>Mailchimp Settings:</th>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:8px;">
                                    <input type="password" name="settings[mailchimp_api_key]" class="wppoppop-input-full" placeholder="API Key (e.g. abcd1234efgh5678-us1)" value="<?php echo esc_attr($settings['mailchimp_api_key'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">Mailchimp API Key</div>
                                    <input type="text" name="settings[mailchimp_list_id]" class="wppoppop-input-full" placeholder="Audience / List ID" value="<?php echo esc_attr($settings['mailchimp_list_id'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">Audience ID</div>
                                </div>
                            </td>
                        </tr>
                        <!-- ActiveCampaign Fields -->
                        <tr>
                            <th>ActiveCampaign Settings:</th>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:8px;">
                                    <input type="url" name="settings[activecampaign_api_url]" class="wppoppop-input-full" placeholder="API URL (e.g. https://youraccount.api-us1.com)" value="<?php echo esc_url($settings['activecampaign_api_url'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">ActiveCampaign API URL</div>
                                    <input type="password" name="settings[activecampaign_api_key]" class="wppoppop-input-full" placeholder="API Key" value="<?php echo esc_attr($settings['activecampaign_api_key'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">ActiveCampaign API Key</div>
                                    <input type="text" name="settings[activecampaign_list_id]" class="wppoppop-input-full" placeholder="List ID (e.g. 1)" value="<?php echo esc_attr($settings['activecampaign_list_id'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">List ID</div>
                                </div>
                            </td>
                        </tr>
                        <!-- Brevo Fields -->
                        <tr>
                            <th>Brevo Settings:</th>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:8px;">
                                    <input type="password" name="settings[brevo_api_key]" class="wppoppop-input-full" placeholder="xkeysib-..." value="<?php echo esc_attr($settings['brevo_api_key'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">Brevo v3 API Key</div>
                                    <input type="text" name="settings[brevo_list_id]" class="wppoppop-input-full" placeholder="List ID (e.g. 2)" value="<?php echo esc_attr($settings['brevo_list_id'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">List ID</div>
                                </div>
                            </td>
                        </tr>
                    </table>

                                                                                                    <div class="wppoppop-section-header">Live Activity Social Proof Stream (FOMO Notifications)</div>
                    <table class="wppoppop-form-table">
                        <tr>
                            <th style="width: 220px;">Social Proof Stream:</th>
                            <td>
                                <label class="wppoppop-switch">
                                    <input type="checkbox" name="settings[social_proof_enabled]" value="1" <?php checked(!empty($settings['social_proof_enabled'])); ?> />
                                    <span class="slider"></span>
                                </label>
                                <span class="wppoppop-switch-label">Enable Live Activity Conversion Toasts</span>
                                <div class="wppoppop-field-desc">Displays subtle notification cards in the corner showing recent subscriber conversions to build trust and increase urgency.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Display Position:</th>
                            <td>
                                <select name="settings[social_proof_position]" class="wppoppop-select" style="min-width: 180px;">
                                    <option value="bottom-left" <?php selected($settings['social_proof_position'] ?? 'bottom-left', 'bottom-left'); ?>>Bottom Left Corner</option>
                                    <option value="bottom-right" <?php selected($settings['social_proof_position'] ?? '', 'bottom-right'); ?>>Bottom Right Corner</option>
                                    <option value="top-left" <?php selected($settings['social_proof_position'] ?? '', 'top-left'); ?>>Top Left Corner</option>
                                    <option value="top-right" <?php selected($settings['social_proof_position'] ?? '', 'top-right'); ?>>Top Right Corner</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Toast Duration (Seconds):</th>
                            <td>
                                <input type="number" name="settings[social_proof_duration]" min="2" max="30" value="<?php echo esc_attr($settings['social_proof_duration'] ?? 5); ?>" class="wppoppop-input" style="width: 100px;" />
                                <div class="wppoppop-field-desc">How long each notification card remains visible on screen.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Interval Delay (Seconds):</th>
                            <td>
                                <input type="number" name="settings[social_proof_interval]" min="4" max="120" value="<?php echo esc_attr($settings['social_proof_interval'] ?? 10); ?>" class="wppoppop-input" style="width: 100px;" />
                                <div class="wppoppop-field-desc">Cooldown pause between successive notifications.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Fallback Activities (Name|Action|PopupID):</th>
                            <td>
                                <textarea name="settings[social_proof_fallbacks]" class="wppoppop-textarea" rows="4" placeholder="Sarah M.|claimed a 20% discount|0"><?php echo esc_textarea($settings['social_proof_fallbacks'] ?? ''); ?></textarea>
                                <div class="wppoppop-field-desc">One entry per line: <code>Name|Action|PopupID</code> (used when recent database submissions are sparse).</div>
                            </td>
                        </tr>
                    </table>

                    <div class="wppoppop-section-header">WooCommerce Cart Abandonment Recovery & Offers</div>
                    <table class="wppoppop-form-table">
                        <tr>
                            <th style="width: 220px;">Cart Abandonment Detection:</th>
                            <td>
                                <label class="wppoppop-switch">
                                    <input type="checkbox" name="settings[woo_abandonment_enabled]" value="1" <?php checked(!empty($settings['woo_abandonment_enabled'])); ?> />
                                    <span class="slider"></span>
                                </label>
                                <span class="wppoppop-switch-label">Enable Exit-Intent Recovery for Cart & Checkout</span>
                                <div class="wppoppop-field-desc">Automatically intercepts shoppers attempting to leave with active items in their shopping cart.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Minimum Cart Total Threshold ($):</th>
                            <td>
                                <input type="number" name="settings[woo_cart_threshold]" min="0" step="1" class="wppoppop-input" style="width: 140px;" value="<?php echo esc_attr($settings['woo_cart_threshold'] ?? 0); ?>" />
                                <div class="wppoppop-field-desc">Only trigger abandonment popups if cart value equals or exceeds this amount (0 to trigger on all carts).</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Assigned Recovery Popup:</th>
                            <td>
                                <?php
                                $woo_popups = get_posts([
                                    'post_type'      => \WPPopPop\Targeting\PopupPostType::POST_TYPE,
                                    'post_status'    => 'publish',
                                    'posts_per_page' => 100,
                                    'orderby'        => 'title',
                                    'order'          => 'ASC',
                                ]);
                                $selected_abandon_id = absint($settings['woo_abandonment_popup'] ?? 0);
                                ?>
                                <select name="settings[woo_abandonment_popup]" class="wppoppop-select" style="min-width: 240px;">
                                    <option value="0"><?php echo esc_html__('-- Select Abandonment Popup --', 'wppoppop'); ?></option>
                                    <?php foreach ($woo_popups as $wp): ?>
                                        <option value="<?php echo esc_attr($wp->ID); ?>" <?php selected($selected_abandon_id, $wp->ID); ?>>
                                            <?php echo esc_html($wp->post_title); ?> (#<?php echo esc_html($wp->ID); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="wppoppop-field-desc">Popup displayed when a shopper attempts to abandon their checkout.</div>
                            </td>
                        </tr>
                    </table>

                    <div class="wppoppop-section-header">Automated Welcome Autoresponder</div>
                    <table class="wppoppop-form-table">
                        <tr>
                            <th style="width: 220px;">Welcome Autoresponder:</th>
                            <td>
                                <label class="wppoppop-switch">
                                    <input type="checkbox" name="settings[autoresponder_enabled]" value="1" <?php checked(!empty($settings['autoresponder_enabled'])); ?> />
                                    <span class="slider"></span>
                                </label>
                                <span class="wppoppop-switch-label">Send Welcome Email Upon Subscription / Verification</span>
                                <div class="wppoppop-field-desc">Automatically dispatches an HTML welcome email delivering vouchers or download assets immediately after confirmation.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Email Subject:</th>
                            <td>
                                <input type="text" name="settings[autoresponder_subject]" class="wppoppop-input-full" value="<?php echo esc_attr($settings['autoresponder_subject'] ?? 'Welcome to {site_name}! Here is your gift'); ?>" />
                                <div class="wppoppop-field-desc">Available merge tags: <code>{name}</code>, <code>{email}</code>, <code>{site_name}</code>, <code>{popup_title}</code>, <code>{coupon_code}</code></div>
                            </td>
                        </tr>
                        <tr>
                            <th>Email Body Template:</th>
                            <td>
                                <textarea name="settings[autoresponder_body]" class="wppoppop-textarea" rows="6" placeholder="Hi {name},&#10;&#10;Thank you for subscribing to {popup_title}!&#10;Your voucher code: {coupon_code}&#10;&#10;Best regards,&#10;{site_name}"><?php echo esc_textarea($settings['autoresponder_body'] ?? ''); ?></textarea>
                                <div class="wppoppop-field-desc">HTML and plain text supported. Merge tags are replaced dynamically before dispatch.</div>
                            </td>
                        </tr>
                    </table>

                    <div class="wppoppop-section-header">Audio Sound Effects & Confetti Celebration</div>
                    <table class="wppoppop-form-table">
                        <tr>
                            <th style="width: 220px;">Sound Synthesizer:</th>
                            <td>
                                <label class="wppoppop-switch">
                                    <input type="checkbox" name="settings[sound_enabled]" value="1" <?php checked(!empty($settings['sound_enabled'])); ?> />
                                    <span class="slider"></span>
                                </label>
                                <span class="wppoppop-switch-label">Enable Web Audio Synthesizer</span>
                                <div class="wppoppop-field-desc">Plays lightweight acoustic audio feedback using the browser Web Audio API without loading external media files.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Confetti Celebration:</th>
                            <td>
                                <label class="wppoppop-switch">
                                    <input type="checkbox" name="settings[confetti_enabled]" value="1" <?php checked(!empty($settings['confetti_enabled'])); ?> />
                                    <span class="slider"></span>
                                </label>
                                <span class="wppoppop-switch-label">Enable Confetti Cannon on Conversion</span>
                                <div class="wppoppop-field-desc">Displays a full-screen physics-based particle burst upon successful subscription or checkout completion.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Modal Open Sound:</th>
                            <td>
                                <select name="settings[sound_open_preset]" class="wppoppop-select" style="min-width: 180px;">
                                    <option value="pop" <?php selected($settings['sound_open_preset'] ?? 'pop', 'pop'); ?>>Gentle Pop</option>
                                    <option value="chime" <?php selected($settings['sound_open_preset'] ?? '', 'chime'); ?>>Bell Chime</option>
                                    <option value="none" <?php selected($settings['sound_open_preset'] ?? '', 'none'); ?>>None (Silent)</option>
                                </select>
                                <div class="wppoppop-field-desc">Acoustic cue triggered when a popup deploys.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Success Sound:</th>
                            <td>
                                <select name="settings[sound_success_preset]" class="wppoppop-select" style="min-width: 180px;">
                                    <option value="fanfare" <?php selected($settings['sound_success_preset'] ?? 'fanfare', 'fanfare'); ?>>Victory Fanfare</option>
                                    <option value="cash_register" <?php selected($settings['sound_success_preset'] ?? '', 'cash_register'); ?>>Cash Register Ding</option>
                                    <option value="chime" <?php selected($settings['sound_success_preset'] ?? '', 'chime'); ?>>Harmonic Chime</option>
                                    <option value="none" <?php selected($settings['sound_success_preset'] ?? '', 'none'); ?>>None (Silent)</option>
                                </select>
                                <div class="wppoppop-field-desc">Audio feedback triggered when a visitor opts in or completes payment.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Master Sound Volume:</th>
                            <td>
                                <input type="range" name="settings[sound_volume]" min="1" max="100" value="<?php echo esc_attr($settings['sound_volume'] ?? 50); ?>" class="wppoppop-range-slider" style="width: 220px;" oninput="this.nextElementSibling.value = this.value + '%'" />
                                <output style="font-weight:600; font-size:13px; margin-left:8px;"><?php echo esc_html($settings['sound_volume'] ?? 50); ?>%</output>
                                <div class="wppoppop-field-desc">Adjust synthesizer gain output.</div>
                            </td>
                        </tr>
                    </table>

                    <div class="wppoppop-section-header">Payment Gateways & Monetization (Stripe & PayPal)</div>
                    <table class="wppoppop-form-table">
                        <tr>
                            <th style="width: 220px;">Currency Code:</th>
                            <td>
                                <select name="settings[payment_currency]" class="wppoppop-select" style="min-width: 140px;">
                                    <option value="USD" <?php selected($settings['payment_currency'] ?? 'USD', 'USD'); ?>>USD ($)</option>
                                    <option value="EUR" <?php selected($settings['payment_currency'] ?? '', 'EUR'); ?>>EUR (€)</option>
                                    <option value="GBP" <?php selected($settings['payment_currency'] ?? '', 'GBP'); ?>>GBP (£)</option>
                                    <option value="CAD" <?php selected($settings['payment_currency'] ?? '', 'CAD'); ?>>CAD ($)</option>
                                    <option value="AUD" <?php selected($settings['payment_currency'] ?? '', 'AUD'); ?>>AUD ($)</option>
                                </select>
                                <div class="wppoppop-field-desc">Select ISO-4217 currency code for popup checkouts.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Environment Mode:</th>
                            <td>
                                <select name="settings[payment_mode]" class="wppoppop-select" style="min-width: 140px;">
                                    <option value="sandbox" <?php selected($settings['payment_mode'] ?? 'sandbox', 'sandbox'); ?>>Sandbox / Test</option>
                                    <option value="live" <?php selected($settings['payment_mode'] ?? '', 'live'); ?>>Live Production</option>
                                </select>
                                <div class="wppoppop-field-desc">Toggle between test and production processing.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Stripe Gateway:</th>
                            <td>
                                <label class="wppoppop-switch">
                                    <input type="checkbox" name="settings[stripe_enabled]" value="1" <?php checked(!empty($settings['stripe_enabled'])); ?> />
                                    <span class="slider"></span>
                                </label>
                                <span class="wppoppop-switch-label">Enable Stripe Payments</span>
                                <div style="margin-top:10px;display:flex;flex-direction:column;gap:8px;">
                                    <input type="text" name="settings[stripe_pub_key]" class="wppoppop-input-full" placeholder="pk_test_..." value="<?php echo esc_attr($settings['stripe_pub_key'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">Stripe Publishable Key</div>
                                    <input type="password" name="settings[stripe_secret_key]" class="wppoppop-input-full" placeholder="sk_test_..." value="<?php echo esc_attr($settings['stripe_secret_key'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">Stripe Secret Key</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>PayPal Gateway:</th>
                            <td>
                                <label class="wppoppop-switch">
                                    <input type="checkbox" name="settings[paypal_enabled]" value="1" <?php checked(!empty($settings['paypal_enabled'])); ?> />
                                    <span class="slider"></span>
                                </label>
                                <span class="wppoppop-switch-label">Enable PayPal Checkout</span>
                                <div style="margin-top:10px;display:flex;flex-direction:column;gap:8px;">
                                    <input type="text" name="settings[paypal_client_id]" class="wppoppop-input-full" placeholder="PayPal Client ID" value="<?php echo esc_attr($settings['paypal_client_id'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">PayPal Client ID</div>
                                    <input type="password" name="settings[paypal_secret]" class="wppoppop-input-full" placeholder="PayPal Secret" value="<?php echo esc_attr($settings['paypal_secret'] ?? ''); ?>" />
                                    <div class="wppoppop-sublabel">PayPal Secret</div>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="wppoppop-section-header">Developer & Integration Settings</div>
                    <table class="wppoppop-form-table">
                        <tr>
                            <th style="width: 220px;">Global Webhook URL:</th>
                            <td>
                                <input type="url" name="settings[webhook_url]" class="wppoppop-input-full" value="<?php echo esc_url($settings['webhook_url']); ?>" placeholder="https://hooks.zapier.com/..." />
                                <div class="wppoppop-field-desc">Dispatches real-time JSON payloads for every lead captured.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Admin Notification Email:</th>
                            <td>
                                <input type="email" name="settings[admin_notify_email]" class="wppoppop-input-full" value="<?php echo esc_attr($settings['admin_notify_email']); ?>" />
                                <div class="wppoppop-field-desc">Receive instant email alerts whenever new submissions occur.</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Global Custom CSS:</th>
                            <td>
                                <textarea name="settings[custom_css]" class="wppoppop-textarea" rows="6"><?php echo esc_textarea($settings['custom_css']); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>Global Custom JavaScript:</th>
                            <td>
                                <textarea name="settings[custom_js]" class="wppoppop-textarea" rows="6"><?php echo esc_textarea($settings['custom_js']); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="wppoppop-save-bar">
                    <button type="submit" class="wppoppop-btn-pink" id="wppoppop-master-save-btn">&#10003; Save Settings</button>
                </div>
            </form>
        </div>
        <?php
    }
}
