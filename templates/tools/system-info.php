<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$php_version    = phpversion();
$mysql_version  = $wpdb->db_version();
$wp_version     = get_bloginfo('version');
$memory_limit   = ini_get('memory_limit');
$max_exec_time  = ini_get('max_execution_time') . 's';
$upload_max     = size_format(wp_max_upload_size());
$post_max       = ini_get('post_max_size');
$curl_installed = function_exists('curl_version');
$curl_info      = $curl_installed ? curl_version() : ['version' => 'N/A'];
$openssl_status = extension_loaded('openssl') ? 'Enabled' : 'Disabled';
$json_status    = extension_loaded('json') ? 'Enabled' : 'Disabled';
?>
<div class="wppoppop-card-box" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;border-bottom:1px solid #f1f5f9;padding-bottom:10px;">
        <span class="dashicons dashicons-dashboard" style="font-size:20px;width:20px;height:20px;color:#2563eb;"></span>
        <h3 style="margin:0;font-size:16px;font-weight:700;color:#1e293b;">System Environment & Health Status</h3>
    </div>
    
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:12px;font-size:13px;">
        <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <span style="color:#64748b;font-size:11px;font-weight:600;display:block;text-transform:uppercase;">PHP Version</span>
            <strong style="color:#1e293b;"><?php echo esc_html($php_version); ?></strong>
            <?php if (version_compare($php_version, '7.4', '>=')) : ?>
                <span style="color:#10b981;font-size:11px;font-weight:600;margin-left:6px;">&#10004; Compatible</span>
            <?php else : ?>
                <span style="color:#ef4444;font-size:11px;font-weight:600;margin-left:6px;">&#9888; Outdated</span>
            <?php endif; ?>
        </div>

        <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <span style="color:#64748b;font-size:11px;font-weight:600;display:block;text-transform:uppercase;">MySQL Database</span>
            <strong style="color:#1e293b;"><?php echo esc_html($mysql_version); ?></strong>
        </div>

        <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <span style="color:#64748b;font-size:11px;font-weight:600;display:block;text-transform:uppercase;">WordPress Core</span>
            <strong style="color:#1e293b;"><?php echo esc_html($wp_version); ?></strong>
        </div>

        <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <span style="color:#64748b;font-size:11px;font-weight:600;display:block;text-transform:uppercase;">Memory Limit</span>
            <strong style="color:#1e293b;"><?php echo esc_html($memory_limit); ?></strong>
        </div>

        <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <span style="color:#64748b;font-size:11px;font-weight:600;display:block;text-transform:uppercase;">Max Execution Time</span>
            <strong style="color:#1e293b;"><?php echo esc_html($max_exec_time); ?></strong>
        </div>

        <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <span style="color:#64748b;font-size:11px;font-weight:600;display:block;text-transform:uppercase;">Upload Limits</span>
            <strong style="color:#1e293b;">Max <?php echo esc_html($upload_max); ?> (Post: <?php echo esc_html($post_max); ?>)</strong>
        </div>

        <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <span style="color:#64748b;font-size:11px;font-weight:600;display:block;text-transform:uppercase;">cURL Extension</span>
            <strong style="color:#1e293b;"><?php echo esc_html($curl_info['version'] ?? 'Active'); ?></strong>
        </div>

        <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
            <span style="color:#64748b;font-size:11px;font-weight:600;display:block;text-transform:uppercase;">SSL & Cryptography</span>
            <strong style="color:#1e293b;"><?php echo esc_html($openssl_status); ?> (OpenSSL)</strong>
        </div>
    </div>
</div>
