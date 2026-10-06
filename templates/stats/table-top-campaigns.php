<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-card-table" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.05);overflow:hidden;">
    <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;">
        <h3 style="margin:0;font-size:15px;font-weight:700;color:#1e293b;">Top Performing Campaigns</h3>
    </div>
    <table class="wp-list-table widefat fixed striped table-view-list" style="border:none;">
        <thead>
            <tr>
                <th style="width:50px;text-align:center;">Rank</th>
                <th style="font-weight:600;">Popup Campaign & Identifier</th>
                <th style="width:130px;text-align:right;font-weight:600;">Impressions</th>
                <th style="width:130px;text-align:right;font-weight:600;">Captured Leads</th>
                <th style="width:140px;text-align:right;font-weight:600;">Conversion Rate</th>
                <th style="width:110px;text-align:center;font-weight:600;">Status</th>
                <th style="width:140px;text-align:right;font-weight:600;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ranked_popups)) : ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:30px 10px;color:#64748b;">
                        No campaign records available to rank.
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($ranked_popups as $rank => $p) : 
                    $p_impr = intval($p->impressions ?? 0);
                    $p_subs = intval($p->submissions ?? 0);
                    $p_cr   = $p_impr > 0 ? round(($p_subs / $p_impr) * 100, 1) : 0;
                    $edit_url = admin_url('admin.php?page=wppoppop-builder&uid=' . $p->uid);
                    ?>
                    <tr>
                        <td style="text-align:center;font-weight:700;color:#64748b;">#<?php echo $rank + 1; ?></td>
                        <td>
                            <strong><a href="<?php echo esc_url($edit_url); ?>" style="color:#1e293b;text-decoration:none;font-size:14px;"><?php echo esc_html($p->title); ?></a></strong>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;"><code><?php echo esc_html($p->uid); ?></code></div>
                        </td>
                        <td style="text-align:right;font-weight:600;color:#1e293b;"><?php echo number_format($p_impr); ?></td>
                        <td style="text-align:right;font-weight:600;color:#10b981;"><?php echo number_format($p_subs); ?></td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex;align-items:center;gap:6px;">
                                <strong style="color:#f59e0b;"><?php echo esc_html($p_cr); ?>%</strong>
                                <div style="width:50px;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                                    <div style="width:<?php echo min(100, $p_cr * 2); ?>%;height:100%;background:#f59e0b;"></div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge" style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#dcfce7;color:#166534;">
                                <?php echo esc_html(ucfirst($p->status)); ?>
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <a href="<?php echo esc_url($edit_url); ?>" class="button button-small">Edit Builder</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
