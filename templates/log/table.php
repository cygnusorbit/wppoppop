<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-card-table" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.05);overflow:hidden;">
    <table class="wp-list-table widefat fixed striped table-view-list" id="wppoppop-logs-table" style="border:none;">
        <thead>
            <tr>
                <th style="width:60px;text-align:center;">#</th>
                <th style="width:170px;font-weight:600;">Event Type</th>
                <th style="font-weight:600;">Message / Description</th>
                <th style="width:260px;font-weight:600;">Payload Metadata</th>
                <th style="width:160px;font-weight:600;">Timestamp</th>
                <th style="width:90px;text-align:right;font-weight:600;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)) : ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:36px 10px;color:#64748b;">
                        <span class="dashicons dashicons-clipboard" style="font-size:32px;width:32px;height:32px;margin:0 auto 8px auto;display:block;color:#94a3b8;"></span>
                        No activity log entries found matching the filter criteria.
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($logs as $l) : 
                    $type = strtolower($l->event_type);
                    $is_error = (strpos($type, 'error') !== false || strpos($type, 'failed') !== false);
                    $is_success = (strpos($type, 'confirmed') !== false || strpos($type, 'dispatched') !== false || strpos($type, 'created') !== false);
                    
                    if ($is_error) {
                        $badge_style = 'background:#fee2e2;color:#991b1b;';
                    } elseif ($is_success) {
                        $badge_style = 'background:#dcfce7;color:#166534;';
                    } else {
                        $badge_style = 'background:#e0e7ff;color:#3730a3;';
                    }

                    $raw_payload = $l->payload ?: '{}';
                    $decoded_payload = json_decode($raw_payload, true);
                    $payload_preview = (!empty($decoded_payload) && is_array($decoded_payload)) 
                        ? wp_json_encode(array_slice($decoded_payload, 0, 3)) 
                        : (strlen($raw_payload) > 60 ? substr($raw_payload, 0, 57) . '...' : $raw_payload);
                    ?>
                    <tr class="wppoppop-log-row" data-id="<?php echo esc_attr($l->id); ?>" data-type="<?php echo esc_attr($type); ?>" data-msg="<?php echo esc_attr(strtolower($l->message)); ?>">
                        <td style="text-align:center;color:#94a3b8;"><?php echo esc_html($l->id); ?></td>
                        <td>
                            <span class="badge" style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700;<?php echo $badge_style; ?>">
                                <?php echo esc_html($l->event_type); ?>
                            </span>
                        </td>
                        <td>
                            <strong style="color:#1e293b;font-size:13px;"><?php echo esc_html($l->message); ?></strong>
                        </td>
                        <td>
                            <code style="font-size:11px;color:#475569;background:#f8fafc;padding:2px 6px;border-radius:4px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?php echo esc_html($payload_preview); ?>
                            </code>
                        </td>
                        <td style="font-size:12px;color:#64748b;">
                            <?php echo esc_html($l->created_at); ?>
                        </td>
                        <td style="text-align:right;">
                            <button type="button" class="button button-small wppoppop-view-payload-btn"
                                    data-id="<?php echo esc_attr($l->id); ?>"
                                    data-type="<?php echo esc_attr($l->event_type); ?>"
                                    data-msg="<?php echo esc_attr($l->message); ?>"
                                    data-date="<?php echo esc_attr($l->created_at); ?>"
                                    data-payload='<?php echo esc_attr($raw_payload); ?>'>
                                Inspect
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
