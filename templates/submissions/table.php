<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-card-table" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.05);overflow:hidden;">
    <table class="wp-list-table widefat fixed striped table-view-list" id="wppoppop-submissions-table" style="border:none;">
        <thead>
            <tr>
                <th style="width:50px;text-align:center;">#</th>
                <th style="width:200px;font-weight:600;">Email & Country</th>
                <th style="font-weight:600;">Popup Campaign</th>
                <th style="width:110px;text-align:center;font-weight:600;">Opt-In Status</th>
                <th style="width:100px;text-align:center;font-weight:600;">Lead Score</th>
                <th style="font-weight:600;">Captured Fields & Attribution</th>
                <th style="width:140px;font-weight:600;">Date</th>
                <th style="width:180px;text-align:right;font-weight:600;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($submissions)) : ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:30px 10px;color:#64748b;">
                        <span class="dashicons dashicons-email-alt" style="font-size:32px;width:32px;height:32px;margin:0 auto 8px auto;display:block;color:#94a3b8;"></span>
                        No leads captured yet. Published popups with form elements will log submission records here.
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($submissions as $idx => $s) : 
                    $fields = json_decode($s->fields_data, true) ?: [];
                    $quiz_score = isset($fields['quiz_score']) ? intval($fields['quiz_score']) : null;
                    $is_confirmed = ($s->status === 'confirmed');
                    $country = !empty($s->country_code) ? esc_html($s->country_code) : '—';
                    ?>
                    <tr class="wppoppop-sub-row" data-id="<?php echo esc_attr($s->id); ?>" data-email="<?php echo esc_attr(strtolower($s->email)); ?>" data-title="<?php echo esc_attr(strtolower($s->popup_title ?? '')); ?>">
                        <td style="text-align:center;color:#94a3b8;"><?php echo esc_html($s->id); ?></td>
                        <td>
                            <strong><?php echo esc_html($s->email); ?></strong>
                            <div style="font-size:11px;color:#64748b;margin-top:2px;">Country: <code><?php echo $country; ?></code></div>
                        </td>
                        <td>
                            <strong><?php echo esc_html($s->popup_title ?: 'Untitled Popup'); ?></strong>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;"><code><?php echo esc_html($s->popup_uid); ?></code></div>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge" style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;<?php echo $is_confirmed ? 'background:#dcfce7;color:#166534;' : 'background:#fef3c7;color:#92400e;'; ?>">
                                <?php echo esc_html(ucfirst($s->status)); ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <?php if ($quiz_score !== null) : ?>
                                <span style="background:#e0e7ff;color:#3730a3;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">
                                    <?php echo esc_html($quiz_score); ?> pts
                                </span>
                            <?php else : ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size:12px;color:#334155;">
                                <?php 
                                $preview_parts = [];
                                foreach ($fields as $k => $v) {
                                    if ($k === 'signature' || $k === 'quiz_score') continue;
                                    if (strpos($k, 'utm_') === 0) continue;
                                    $val_text = is_array($v) ? implode(', ', $v) : $v;
                                    $preview_parts[] = esc_html($k) . ': <strong>' . esc_html($val_text) . '</strong>';
                                }
                                echo !empty($preview_parts) ? implode(' &bull; ', array_slice($preview_parts, 0, 3)) : '<span style="color:#94a3b8;">No custom fields</span>';
                                ?>
                            </div>
                            <?php if (!empty($fields['utm_source']) || !empty($fields['utm_campaign'])) : ?>
                                <div style="margin-top:4px;display:flex;gap:4px;flex-wrap:wrap;">
                                    <?php if (!empty($fields['utm_source'])) : ?>
                                        <span style="background:#f1f5f9;color:#475569;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:600;">src: <?php echo esc_html($fields['utm_source']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($fields['utm_campaign'])) : ?>
                                        <span style="background:#f1f5f9;color:#475569;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:600;">camp: <?php echo esc_html($fields['utm_campaign']); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:#64748b;"><?php echo esc_html($s->created_at); ?></td>
                        <td style="text-align:right;white-space:nowrap;">
                            <button type="button" class="button button-small wppoppop-view-sub-btn" 
                                    data-id="<?php echo esc_attr($s->id); ?>"
                                    data-email="<?php echo esc_attr($s->email); ?>"
                                    data-popup="<?php echo esc_attr($s->popup_title ?: 'Untitled'); ?>"
                                    data-date="<?php echo esc_attr($s->created_at); ?>"
                                    data-country="<?php echo esc_attr($country); ?>"
                                    data-fields='<?php echo esc_attr(wp_json_encode($fields)); ?>'>
                                View/Edit
                            </button>
                            <button type="button" class="button button-small wppoppop-anon-sub-btn" data-id="<?php echo esc_attr($s->id); ?>" title="Anonymize PII (GDPR)">GDPR</button>
                            <button type="button" class="button button-small wppoppop-del-sub-btn" data-id="<?php echo esc_attr($s->id); ?>" title="Delete Record" style="color:#ef4444;border-color:#fca5a5;">&times;</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
