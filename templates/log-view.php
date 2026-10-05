<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_logs = $wpdb->prefix . 'wppoppop_logs';
$logs = $wpdb->get_results("SELECT * FROM {$table_logs} ORDER BY id DESC LIMIT 100");
?>
<div class="wrap wppoppop-log-wrap">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h1 style="margin:0;">System & Integration Event Log</h1>
        <button type="button" class="button button-secondary" id="btn-clear-logs">Clear Event Log</button>
    </div>
    <p class="description">Audit trail of webhook events, SMS dispatches, user confirmation tokens, and delivery statuses.</p>

    <table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
        <thead>
            <tr>
                <th style="width: 70px;">ID</th>
                <th style="width: 140px;">Event</th>
                <th>Message</th>
                <th>Payload Context</th>
                <th style="width: 170px;">Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)) : ?>
                <tr><td colspan="5">No system events logged yet.</td></tr>
            <?php else : ?>
                <?php foreach ($logs as $log) : 
                    $badge_color = ($log->event_type === 'confirmation') ? '#0284c7' : '#10b981';
                    ?>
                    <tr>
                        <td>#<?php echo esc_html($log->id); ?></td>
                        <td>
                            <span style="display:inline-block; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600; color:#fff; background:<?php echo $badge_color; ?>;">
                                <?php echo esc_html(strtoupper($log->event_type)); ?>
                            </span>
                        </td>
                        <td><strong><?php echo esc_html($log->message); ?></strong></td>
                        <td><code><?php echo esc_html($log->context); ?></code></td>
                        <td><?php echo esc_html($log->created_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('#btn-clear-logs').on('click', function() {
        if (!confirm('Clear all system activity logs?')) return;
        $.post(wppoppop_lib_vars ? wppoppop_lib_vars.ajax_url : ajaxurl, {
            action: 'wppoppop_clear_logs',
            nonce: '<?php echo wp_create_nonce('wppoppop_builder_nonce'); ?>'
        }, function(res) {
            location.reload();
        });
    });
});
</script>
