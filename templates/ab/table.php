<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$items_table = $wpdb->prefix . 'wppoppop_items';
$all_popups_raw = $wpdb->get_results("SELECT uid, title, impressions, submissions FROM {$items_table}");
$popups_by_uid = [];
foreach ($all_popups_raw as $p) {
    $popups_by_uid[$p->uid] = $p;
}
?>
<div class="wppoppop-card-table" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.05);overflow:hidden;">
    <table class="wp-list-table widefat fixed striped table-view-list" id="wppoppop-ab-table" style="border:none;">
        <thead>
            <tr>
                <th style="width:50px;text-align:center;">#</th>
                <th style="width:220px;font-weight:600;">Campaign Title & UID</th>
                <th style="font-weight:600;">Variations & Real-Time Performance</th>
                <th style="width:110px;text-align:center;font-weight:600;">Status</th>
                <th style="width:180px;text-align:center;font-weight:600;">A/B Shortcode</th>
                <th style="width:100px;text-align:right;font-weight:600;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($campaigns)) : ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:36px 10px;color:#64748b;">
                        <span class="dashicons dashicons-randomize" style="font-size:32px;width:32px;height:32px;margin:0 auto 8px auto;display:block;color:#94a3b8;"></span>
                        No A/B testing campaigns found. Click <strong>New A/B Campaign</strong> above to launch your first split-test experiment!
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($campaigns as $idx => $c) : 
                    $uids = json_decode($c->popup_uids, true) ?: [];
                    $shortcode = '[wppoppop_ab uid="' . esc_attr($c->uid) . '"]';
                    ?>
                    <tr class="wppoppop-ab-row" data-uid="<?php echo esc_attr($c->uid); ?>">
                        <td style="text-align:center;color:#94a3b8;"><?php echo $idx + 1; ?></td>
                        <td>
                            <strong style="font-size:14px;color:#1e293b;"><?php echo esc_html($c->title); ?></strong>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;"><code><?php echo esc_html($c->uid); ?></code></div>
                            <div style="font-size:11px;color:#64748b;margin-top:4px;">Created: <?php echo esc_html($c->created_at); ?></div>
                        </td>
                        <td>
                            <?php if (empty($uids)) : ?>
                                <span style="color:#ef4444;font-size:12px;">No variations attached.</span>
                            <?php else : ?>
                                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:8px;">
                                    <?php foreach ($uids as $var_idx => $v_uid) : 
                                        $pop = $popups_by_uid[$v_uid] ?? null;
                                        $v_title = $pop ? $pop->title : 'Unknown (' . $v_uid . ')';
                                        $v_impr  = $pop ? intval($pop->impressions) : 0;
                                        $v_subs  = $pop ? intval($pop->submissions) : 0;
                                        $v_cr    = $v_impr > 0 ? round(($v_subs / $v_impr) * 100, 1) : 0;
                                        $var_letter = chr(65 + ($var_idx % 26)); // A, B, C...
                                        ?>
                                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:8px 10px;font-size:12px;">
                                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                                                <span style="font-weight:700;color:#2563eb;">Variant <?php echo $var_letter; ?></span>
                                                <span style="font-weight:700;color:#f59e0b;"><?php echo esc_html($v_cr); ?>% CR</span>
                                            </div>
                                            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#1e293b;font-weight:600;" title="<?php echo esc_attr($v_title); ?>">
                                                <?php echo esc_html($v_title); ?>
                                            </div>
                                            <div style="color:#64748b;font-size:11px;margin-top:2px;">
                                                Impr: <strong><?php echo number_format($v_impr); ?></strong> &bull; Leads: <strong><?php echo number_format($v_subs); ?></strong>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge" style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#dcfce7;color:#166534;">
                                <?php echo esc_html(ucfirst($c->status)); ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="button button-small wppoppop-copy-ab-sc-btn" data-shortcode="<?php echo esc_attr($shortcode); ?>" title="Click to copy shortcode" style="font-size:11px;font-family:monospace;">
                                [wppoppop_ab]
                            </button>
                        </td>
                        <td style="text-align:right;">
                            <button type="button" class="button button-small wppoppop-del-ab-btn" data-uid="<?php echo esc_attr($c->uid); ?>" title="Delete Campaign" style="color:#ef4444;border-color:#fca5a5;">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
