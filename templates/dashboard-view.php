<?php
if (!defined('ABSPATH')) {
    exit;
}

// Defensive Hydration: Fallback to database if $popups was not injected by controller
if (!isset($popups) || !is_array($popups)) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wppoppop_items';
    $popups = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");
    if (!is_array($popups)) {
        $popups = [];
    }
}

$total_popups = count($popups);
$total_impressions = 0;
$total_submissions = 0;

foreach ($popups as $p) {
    $total_impressions += intval($p->impressions);
    $total_submissions += intval($p->submissions);
}

$overall_cr = ($total_impressions > 0) ? round(($total_submissions / $total_impressions) * 100, 1) : 0;
?>
<div class="wrap wppoppop-dashboard-wrap">
    <!-- Top Bar -->
    <div class="wppoppop-dash-header">
        <div class="dash-header-title">
            <span class="dashicons dashicons-external header-icon"></span>
            <div>
                <h1>WpPopPop</h1>
                <p class="dash-subtitle">High-Converting Popups & Interactive Lead Generation</p>
            </div>
        </div>
        <div class="dash-header-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder')); ?>" class="button button-primary button-hero">
                <span class="dashicons dashicons-plus-alt2" style="vertical-align:middle;margin-top:-2px;"></span> Create Popup
            </a>
        </div>
    </div>

    <!-- Quick Stats KPI Cards -->
    <div class="wppoppop-kpi-grid">
        <div class="kpi-card">
            <span class="kpi-label">Active Campaigns</span>
            <span class="kpi-value"><?php echo esc_html($total_popups); ?></span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Total Impressions</span>
            <span class="kpi-value"><?php echo esc_html(number_format($total_impressions)); ?></span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Total Submissions</span>
            <span class="kpi-value"><?php echo esc_html(number_format($total_submissions)); ?></span>
        </div>
        <div class="kpi-card highlight">
            <span class="kpi-label">Average Conversion Rate</span>
            <span class="kpi-value"><?php echo esc_html($overall_cr); ?>%</span>
        </div>
    </div>

    <!-- Table Controls Bar -->
    <div class="wppoppop-table-toolbar">
        <div class="toolbar-left">
            <input type="text" id="wppoppop-search-input" placeholder="Search popups by title or UID..." class="widefat">
        </div>
        <div class="toolbar-right">
            <span class="dashicons dashicons-info" style="color:#64748b;vertical-align:middle;"></span>
            <span style="font-size:12px;color:#64748b;">Click <strong>Live Site Preview</strong> to test any popup directly on your live homepage.</span>
        </div>
    </div>

    <!-- Popups Listing Table -->
    <div class="wppoppop-table-container">
        <table class="wp-list-table widefat fixed striped table-view-list wppoppop-table" id="wppoppop-popups-table">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-title" style="width: 28%;">Popup Title & Shortcode</th>
                    <th scope="col" class="manage-column column-preview" style="width: 17%;">Live Site Preview</th>
                    <th scope="col" class="manage-column column-status" style="width: 10%;">Status</th>
                    <th scope="col" class="manage-column column-impressions" style="width: 11%;">Impressions</th>
                    <th scope="col" class="manage-column column-submissions" style="width: 11%;">Submissions</th>
                    <th scope="col" class="manage-column column-cr" style="width: 9%;">CR%</th>
                    <th scope="col" class="manage-column column-date" style="width: 10%;">Created</th>
                    <th scope="col" class="manage-column column-actions" style="width: 4%; text-align: center;"></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($popups)) : ?>
                    <tr class="no-items">
                        <td colspan="8" style="text-align: center; padding: 40px 20px;">
                            <span class="dashicons dashicons-admin-page" style="font-size: 42px; width:42px; height:42px; color: #94a3b8; margin-bottom: 10px;"></span>
                            <h3 style="margin: 0 0 8px 0; color:#334155;">No popups created yet</h3>
                            <p style="color:#64748b; margin: 0 0 15px 0;">Create your first converting popup campaign with the visual drag-and-drop builder.</p>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder')); ?>" class="button button-primary">Create Your First Popup</a>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($popups as $p) : 
                        $cr = ($p->impressions > 0) ? round(($p->submissions / $p->impressions) * 100, 1) : 0;
                        $is_active = ($p->status === 'publish');
                        $live_preview_url = add_query_arg(['wppoppop_preview' => $p->uid], home_url('/'));
                        $formatted_date = (!empty($p->created_at) && strtotime($p->created_at)) 
                            ? date_i18n(get_option('date_format'), strtotime($p->created_at)) 
                            : '&mdash;';
                    ?>
                        <tr id="popup-row-<?php echo esc_attr($p->uid); ?>" data-uid="<?php echo esc_attr($p->uid); ?>" data-title="<?php echo esc_attr(strtolower($p->title)); ?>">
                            <!-- Title & Shortcode -->
                            <td class="column-title">
                                <strong class="popup-item-title">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . $p->uid)); ?>">
                                        <?php echo esc_html($p->title); ?>
                                    </a>
                                </strong>
                                <div class="popup-shortcode-badge" title="Click to copy shortcode" data-shortcode='[wppoppop uid="<?php echo esc_attr($p->uid); ?>"]'>
                                    <code>[wppoppop uid="<?php echo esc_attr($p->uid); ?>"]</code>
                                    <span class="dashicons dashicons-clipboard copy-icon"></span>
                                </div>
                            </td>

                            <!-- Live Site Preview on Homepage -->
                            <td class="column-preview">
                                <a href="<?php echo esc_url($live_preview_url); ?>" target="_blank" class="wppoppop-live-site-btn" title="Open live homepage with this popup active">
                                    <span class="dashicons dashicons-external"></span> Live Site Preview
                                </a>
                            </td>

                            <!-- Status Badge -->
                            <td class="column-status">
                                <span class="wppoppop-status-badge <?php echo $is_active ? 'status-active' : 'status-draft'; ?>" id="badge-status-<?php echo esc_attr($p->uid); ?>">
                                    <?php echo $is_active ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>

                            <!-- Impressions -->
                            <td class="column-impressions" id="metric-impressions-<?php echo esc_attr($p->uid); ?>">
                                <?php echo esc_html(number_format($p->impressions)); ?>
                            </td>

                            <!-- Submissions -->
                            <td class="column-submissions" id="metric-submissions-<?php echo esc_attr($p->uid); ?>">
                                <?php echo esc_html(number_format($p->submissions)); ?>
                            </td>

                            <!-- Conversion Rate -->
                            <td class="column-cr" id="metric-cr-<?php echo esc_attr($p->uid); ?>">
                                <strong><?php echo esc_html($cr); ?>%</strong>
                            </td>

                            <!-- Created Date -->
                            <td class="column-date" style="color: #64748b; font-size: 12px;">
                                <?php echo esc_html($formatted_date); ?>
                            </td>

                            <!-- Context Action Dropdown Trigger -->
                            <td class="column-actions" style="position:relative; text-align:right;">
                                <div class="wppoppop-action-wrap">
                                    <button type="button" class="wppoppop-action-btn" title="Options">
                                        <span class="dashicons dashicons-menu"></span>
                                    </button>

                                    <!-- Floating Popover Menu -->
                                    <div class="wppoppop-action-menu">
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . $p->uid)); ?>" class="action-item">
                                            Edit
                                        </a>
                                        <button type="button" class="action-item action-btn-toggle" data-uid="<?php echo esc_attr($p->uid); ?>">
                                            <?php echo $is_active ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                        <button type="button" class="action-item action-btn-duplicate" data-uid="<?php echo esc_attr($p->uid); ?>">
                                            Duplicate
                                        </button>
                                        <a href="<?php echo esc_url(admin_url('admin-post.php?action=wppoppop_export_definition&uid=' . $p->uid . '&nonce=' . wp_create_nonce('wppoppop_export_' . $p->uid))); ?>" class="action-item">
                                            Export popup definition
                                        </a>
                                        <a href="<?php echo esc_url(admin_url('admin-post.php?action=wppoppop_export_csv&uid=' . $p->uid . '&nonce=' . wp_create_nonce('wppoppop_csv_' . $p->uid))); ?>" class="action-item">
                                            Export all records as CSV
                                        </a>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-stats&uid=' . $p->uid)); ?>" class="action-item">
                                            Statistics
                                        </a>
                                        <button type="button" class="action-item action-btn-reset-stats" data-uid="<?php echo esc_attr($p->uid); ?>">
                                            Reset Statistics
                                        </button>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-field-analytics&uid=' . $p->uid)); ?>" class="action-item">
                                            Field Analytics
                                        </a>
                                        <div class="action-menu-sep"></div>
                                        <button type="button" class="action-item action-btn-delete" data-uid="<?php echo esc_attr($p->uid); ?>">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
