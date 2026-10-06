<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-card-table" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.05);overflow:hidden;">
    <table class="wp-list-table widefat fixed striped table-view-list" style="border:none;">
        <thead>
            <tr>
                <th style="width:50px;text-align:center;">#</th>
                <th style="font-weight:600;">Title & UID</th>
                <th style="width:110px;text-align:center;font-weight:600;">Status</th>
                <th style="width:120px;text-align:right;font-weight:600;">Impressions</th>
                <th style="width:120px;text-align:right;font-weight:600;">Leads</th>
                <th style="width:110px;text-align:right;font-weight:600;">Conversion</th>
                <th style="width:170px;text-align:center;font-weight:600;">Shortcode</th>
                <th style="width:190px;text-align:right;font-weight:600;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($popups)) : ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:30px 10px;color:#64748b;">
                        <span class="dashicons dashicons-external" style="font-size:32px;width:32px;height:32px;margin-bottom:8px;display:block;margin:0 auto 8px auto;color:#94a3b8;"></span>
                        No popup campaigns found. Click <strong>Create Popup</strong> above to launch your first high-converting campaign!
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($popups as $idx => $p) : 
                    $impr = intval($p->impressions ?? 0);
                    $subs = intval($p->submissions ?? 0);
                    $cr   = $impr > 0 ? round(($subs / $impr) * 100, 1) : 0;
                    $edit_url = admin_url('admin.php?page=wppoppop-builder&uid=' . $p->uid);
                    $preview_url = home_url('/?wppoppop_preview=' . $p->uid);
                    ?>
                    <tr>
                        <td style="text-align:center;color:#94a3b8;"><?php echo $idx + 1; ?></td>
                        <td>
                            <strong><a href="<?php echo esc_url($edit_url); ?>" style="color:#1e293b;text-decoration:none;font-size:14px;"><?php echo esc_html($p->title); ?></a></strong>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;"><code><?php echo esc_html($p->uid); ?></code></div>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge" style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#dcfce7;color:#166534;">
                                <?php echo esc_html(ucfirst($p->status)); ?>
                            </span>
                        </td>
                        <td style="text-align:right;font-weight:600;color:#1e293b;"><?php echo number_format($impr); ?></td>
                        <td style="text-align:right;font-weight:600;color:#10b981;"><?php echo number_format($subs); ?></td>
                        <td style="text-align:right;font-weight:600;color:#f59e0b;"><?php echo esc_html($cr); ?>%</td>
                        <td style="text-align:center;">
                            <button type="button" class="button button-small wppoppop-copy-sc-btn" data-shortcode="[wppoppop uid=&quot;<?php echo esc_attr($p->uid); ?>&quot;]" title="Click to copy shortcode" style="font-size:11px;font-family:monospace;">
                                [wppoppop]
                            </button>
                        </td>
                        <td style="text-align:right;white-space:nowrap;">
                            <a href="<?php echo esc_url($edit_url); ?>" class="button button-small" title="Edit Builder">Edit</a>
                            <a href="<?php echo esc_url($preview_url); ?>" target="_blank" class="button button-small" title="Live Preview">Preview</a>
                            <button type="button" class="button button-small wppoppop-duplicate-btn" data-uid="<?php echo esc_attr($p->uid); ?>" title="Duplicate">Copy</button>
                            <button type="button" class="button button-small wppoppop-export-btn" data-uid="<?php echo esc_attr($p->uid); ?>" title="Export JSON">Export</button>
                            <button type="button" class="button button-small wppoppop-delete-btn" data-uid="<?php echo esc_attr($p->uid); ?>" title="Delete" style="color:#ef4444;border-color:#fca5a5;">&times;</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
