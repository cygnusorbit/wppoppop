<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$table_subs  = $wpdb->prefix . 'wppoppop_submissions';
$table_items = $wpdb->prefix . 'wppoppop_items';

$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$where_sql = '';
if (!empty($search_query)) {
    $where_sql = $wpdb->prepare("WHERE s.email LIKE %s OR i.title LIKE %s", '%' . $wpdb->esc_like($search_query) . '%', '%' . $wpdb->esc_like($search_query) . '%');
}

$submissions = $wpdb->get_results("SELECT s.*, i.title as popup_title 
    FROM {$table_subs} s LEFT JOIN {$table_items} i ON s.popup_uid = i.uid {$where_sql} ORDER BY s.id DESC LIMIT 100");

$total_subs      = $wpdb->get_var("SELECT COUNT(*) FROM {$table_subs}");
$total_confirmed = $wpdb->get_var("SELECT COUNT(*) FROM {$table_subs} WHERE status = 'confirmed'");
$total_views     = $wpdb->get_var("SELECT SUM(impressions) FROM {$table_items}");
$global_rate     = ($total_views > 0) ? round(($total_confirmed / $total_views) * 100, 2) : 0;
?>
<div class="wrap wppoppop-admin-page">
    <h1 class="wp-heading-inline">Submissions & Lead Editor</h1>
    <a href="<?php echo admin_url('admin-ajax.php?action=wppoppop_export_submissions_csv&nonce=' . wp_create_nonce('wppoppop_builder_nonce')); ?>" class="page-title-action">Export to CSV</a>
    <hr class="wp-header-end">

    <div style="display: flex; gap: 20px; margin: 20px 0;">
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Total Impressions</div>
            <div style="font-size: 32px; font-weight: 700; color: #2271b1; margin-top: 5px;"><?php echo number_format((int)$total_views); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Captured Leads</div>
            <div style="font-size: 32px; font-weight: 700; color: #8c8f94; margin-top: 5px;"><?php echo number_format((int)$total_subs); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Confirmed Leads</div>
            <div style="font-size: 32px; font-weight: 700; color: #00a32a; margin-top: 5px;"><?php echo number_format((int)$total_confirmed); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Conversion Rate</div>
            <div style="font-size: 32px; font-weight: 700; color: #d63638; margin-top: 5px;"><?php echo $global_rate; ?>%</div>
        </div>
    </div>

    <!-- Search Form -->
    <form method="get" style="margin-bottom: 15px; display: flex; gap: 8px;">
        <input type="hidden" name="page" value="wppoppop-submissions">
        <input type="search" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="Search lead by email..." style="width: 320px;">
        <button type="submit" class="button">Search Leads</button>
    </form>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 160px;">Popup</th>
                <th>Lead Email</th>
                <th style="width: 140px;">Campaign / UTM</th>
                <th style="width: 70px;">Country</th>
                <th style="width: 90px;">Status</th>
                <th>Submitted Data</th>
                <th style="width: 140px;">Date</th>
                <th style="width: 180px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($submissions)) : ?>
                <tr><td colspan="9">No lead records found.</td></tr>
            <?php else : ?>
                <?php foreach ($submissions as $sub) : 
                    $fields = json_decode($sub->fields_data, true) ?: [];
                    $campaign = !empty($fields['utm_campaign']) ? esc_html($fields['utm_campaign']) : 'organic';
                    $source   = !empty($fields['utm_source']) ? esc_html($fields['utm_source']) : 'direct';
                    ?>
                    <tr id="sub-row-<?php echo esc_attr($sub->id); ?>">
                        <td>#<?php echo esc_html($sub->id); ?></td>
                        <td><strong><?php echo esc_html($sub->popup_title ?: 'Deleted'); ?></strong></td>
                        <td class="sub-email-cell"><a href="mailto:<?php echo esc_attr($sub->email); ?>"><?php echo esc_html($sub->email); ?></a></td>
                        <td>
                            <span class="wppoppop-badge wppoppop-badge-utm"><?php echo $campaign; ?></span>
                            <span class="wppoppop-badge wppoppop-badge-source"><?php echo $source; ?></span>
                        </td>
                        <td><code><?php echo esc_html($sub->country_code ?: 'GL'); ?></code></td>
                        <td><span style="font-size:11px;font-weight:700;color:#00a32a;"><?php echo ucfirst(esc_html($sub->status)); ?></span></td>
                        <td>
                            <?php 
                            foreach ($fields as $k => $v) {
                                if (in_array($k, ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'], true)) continue;
                                if ($k === 'signature') {
                                    echo "<span style='color:#0284c7;font-weight:700;'>[Signature]</span> ";
                                } else {
                                    $v_str = is_array($v) ? implode(', ', $v) : $v;
                                    echo "<code>" . esc_html($k) . "</code>: " . esc_html($v_str) . " ";
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html($sub->created_at); ?></td>
                        <td>
                            <button type="button" class="button button-small btn-view-lead" data-id="<?php echo esc_attr($sub->id); ?>">View/Edit</button>
                            <button type="button" class="button button-small button-link-delete btn-gdpr-delete" data-id="<?php echo esc_attr($sub->id); ?>">Purge</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
