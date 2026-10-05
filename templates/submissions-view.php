<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$table_subs  = $wpdb->prefix . 'wppoppop_submissions';
$table_items = $wpdb->prefix . 'wppoppop_items';

$submissions = $wpdb->get_results("SELECT s.*, i.title as popup_title 
    FROM {$table_subs} s LEFT JOIN {$table_items} i ON s.popup_uid = i.uid ORDER BY s.id DESC LIMIT 100");

$total_subs      = $wpdb->get_var("SELECT COUNT(*) FROM {$table_subs}");
$total_confirmed = $wpdb->get_var("SELECT COUNT(*) FROM {$table_subs} WHERE status = 'confirmed'");
$total_views     = $wpdb->get_var("SELECT SUM(impressions) FROM {$table_items}");
$global_rate     = ($total_views > 0) ? round(($total_confirmed / $total_views) * 100, 2) : 0;
?>
<div class="wrap wppoppop-admin-page">
    <h1 class="wp-heading-inline">Submissions & Field Analytics</h1>
    <a href="<?php echo admin_url('admin-ajax.php?action=wppoppop_export_submissions_csv&nonce=' . wp_create_nonce('wppoppop_builder_nonce')); ?>" class="page-title-action">Export to CSV</a>
    <hr class="wp-header-end">

    <div style="display: flex; gap: 20px; margin: 20px 0;">
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Total Impressions</div>
            <div style="font-size: 32px; font-weight: 700; color: #2271b1; margin-top: 5px;"><?php echo number_format((int)$total_views); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Total Submissions</div>
            <div style="font-size: 32px; font-weight: 700; color: #8c8f94; margin-top: 5px;"><?php echo number_format((int)$total_subs); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Confirmed Leads</div>
            <div style="font-size: 32px; font-weight: 700; color: #00a32a; margin-top: 5px;"><?php echo number_format((int)$total_confirmed); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Confirmed Conv. Rate</div>
            <div style="font-size: 32px; font-weight: 700; color: #d63638; margin-top: 5px;"><?php echo $global_rate; ?>%</div>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th style="width: 200px;">Popup Campaign</th>
                <th>Lead Email</th>
                <th style="width: 120px;">Status</th>
                <th>Submitted Attributes</th>
                <th style="width: 160px;">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($submissions)) : ?>
                <tr><td colspan="6">No submissions recorded yet.</td></tr>
            <?php else : ?>
                <?php foreach ($submissions as $sub) : 
                    $fields = json_decode($sub->fields_data, true);
                    $badge_color = ($sub->status === 'confirmed') ? '#00a32a' : '#dba617';
                    ?>
                    <tr>
                        <td>#<?php echo esc_html($sub->id); ?></td>
                        <td><strong><?php echo esc_html($sub->popup_title ? $sub->popup_title : 'Deleted Popup'); ?></strong></td>
                        <td><a href="mailto:<?php echo esc_attr($sub->email); ?>"><?php echo esc_html($sub->email); ?></a></td>
                        <td>
                            <span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;color:#fff;background:<?php echo $badge_color; ?>;">
                                <?php echo ucfirst(esc_html($sub->status)); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if (!empty($fields)) {
                                foreach ($fields as $k => $v) {
                                    echo "<code>" . esc_html($k) . "</code>: " . esc_html($v) . " ";
                                }
                            } else {
                                echo "<span style='color:#888;'>None</span>";
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html($sub->created_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
