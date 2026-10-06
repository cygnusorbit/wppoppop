<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Gather Server & Environment Diagnostics
$php_version        = phpversion();
$wp_version         = get_bloginfo('version');
$server_software    = isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field($_SERVER['SERVER_SOFTWARE']) : 'Unknown';
$mysql_version      = $wpdb->db_version();
$php_memory_limit   = ini_get('memory_limit') ?: 'N/A';
$wp_memory_limit    = defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : '40M';
$max_exec_time      = ini_get('max_execution_time') ? ini_get('max_execution_time') . 's' : 'N/A';
$upload_max_filesize= ini_get('upload_max_filesize') ?: 'N/A';
$post_max_size      = ini_get('post_max_size') ?: 'N/A';
$curl_status        = function_exists('curl_version') ? 'Enabled (' . curl_version()['version'] . ')' : 'Disabled';
$openssl_status     = defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'Disabled';
$gd_status          = extension_loaded('gd') ? 'Enabled' : 'Disabled';
$json_status        = extension_loaded('json') ? 'Enabled' : 'Disabled';
$multisite_status   = is_multisite() ? 'Yes' : 'No';
$wp_debug_status    = defined('WP_DEBUG') && WP_DEBUG ? 'Enabled' : 'Disabled';
$plugin_version     = defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0.0';

// Database Entity Counts
$popups_count       = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wppoppop_items");
$leads_count        = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wppoppop_submissions");
$campaigns_count    = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wppoppop_campaigns");

// Health Validations
$php_is_optimal     = version_compare($php_version, '7.4', '>=');
$mem_val            = (int)preg_replace('/[^0-9]/', '', $php_memory_limit);
$memory_is_optimal  = ($mem_val >= 128 || strpos($php_memory_limit, '-1') !== false);

// Build Formatted Markdown Diagnostic Text
$report_buffer  = "### WpPopPop System & Diagnostics Audit\n";
$report_buffer .= "- Generated: " . current_time('mysql') . "\n";
$report_buffer .= "- Site URL: " . get_site_url() . "\n";
$report_buffer .= "- WpPopPop Version: " . $plugin_version . "\n";
$report_buffer .= "- WordPress Version: " . $wp_version . "\n";
$report_buffer .= "- Multisite: " . $multisite_status . "\n";
$report_buffer .= "- WP Debug: " . $wp_debug_status . "\n";
$report_buffer .= "- Web Server: " . $server_software . "\n";
$report_buffer .= "- PHP Version: " . $php_version . "\n";
$report_buffer .= "- PHP Memory Limit: " . $php_memory_limit . "\n";
$report_buffer .= "- WP Memory Limit: " . $wp_memory_limit . "\n";
$report_buffer .= "- PHP Max Execution Time: " . $max_exec_time . "\n";
$report_buffer .= "- Upload Max Filesize: " . $upload_max_filesize . "\n";
$report_buffer .= "- Post Max Size: " . $post_max_size . "\n";
$report_buffer .= "- MySQL Server Version: " . $mysql_version . "\n";
$report_buffer .= "- cURL: " . $curl_status . "\n";
$report_buffer .= "- OpenSSL: " . $openssl_status . "\n";
$report_buffer .= "- GD Library: " . $gd_status . "\n";
$report_buffer .= "- JSON Extension: " . $json_status . "\n";
$report_buffer .= "- Total Active Popups: " . $popups_count . "\n";
$report_buffer .= "- Captured Submissions: " . $leads_count . "\n";
$report_buffer .= "- A/B Split Campaigns: " . $campaigns_count . "\n";
?>
<div class="wppoppop-card-box" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;border-bottom:1px solid #f1f5f9;padding-bottom:10px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span class="dashicons dashicons-dashboard" style="font-size:20px;width:20px;height:20px;color:#10b981;"></span>
            <h3 style="margin:0;font-size:16px;font-weight:700;color:#1e293b;">System Environment & Health Diagnostics</h3>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <button type="button" id="wppoppop-copy-system-info-btn" class="button button-secondary" style="display:inline-flex;align-items:center;gap:6px;font-size:12px;">
                <span class="dashicons dashicons-clipboard" style="font-size:15px;width:15px;height:15px;"></span> Copy Diagnostic Report
            </button>
            <button type="button" id="wppoppop-download-system-info-btn" class="button button-secondary" style="display:inline-flex;align-items:center;gap:6px;font-size:12px;" title="Download diagnostic report as text file">
                <span class="dashicons dashicons-download" style="font-size:15px;width:15px;height:15px;"></span> Download .txt
            </button>
        </div>
    </div>

    <!-- Hidden Pre-Formatted Diagnostic Textarea for Copy Pipeline -->
    <textarea id="wppoppop-system-report-text" style="display:none;" readonly><?php echo esc_textarea($report_buffer); ?></textarea>

    <!-- Diagnostics Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:16px;">
        <!-- WordPress Core Environment -->
        <div style="background:#f8fafc;padding:14px;border-radius:6px;border:1px solid #e2e8f0;">
            <h4 style="margin:0 0 10px 0;font-size:13px;font-weight:700;color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:6px;">WordPress Core</h4>
            <div style="font-size:12px;display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">WordPress Version:</span><strong style="color:#1e293b;"><?php echo esc_html($wp_version); ?></strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">WpPopPop Version:</span><strong style="color:#1e293b;"><?php echo esc_html($plugin_version); ?></strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">Multisite Network:</span><strong style="color:#1e293b;"><?php echo esc_html($multisite_status); ?></strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">WP Debug Mode:</span><strong style="color:#1e293b;"><?php echo esc_html($wp_debug_status); ?></strong></div>
            </div>
        </div>

        <!-- PHP & Web Server Runtime -->
        <div style="background:#f8fafc;padding:14px;border-radius:6px;border:1px solid #e2e8f0;">
            <h4 style="margin:0 0 10px 0;font-size:13px;font-weight:700;color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:6px;">PHP & Web Server</h4>
            <div style="font-size:12px;display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:#64748b;">PHP Version:</span>
                    <span style="display:flex;align-items:center;gap:6px;">
                        <strong style="color:#1e293b;"><?php echo esc_html($php_version); ?></strong>
                        <span style="padding:1px 6px;border-radius:8px;font-size:10px;font-weight:600;background:<?php echo $php_is_optimal ? '#dcfce7' : '#fef3c7'; ?>;color:<?php echo $php_is_optimal ? '#15803d' : '#92400e'; ?>;">
                            <?php echo $php_is_optimal ? 'Optimal' : 'Attention'; ?>
                        </span>
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:#64748b;">PHP Memory Limit:</span>
                    <span style="display:flex;align-items:center;gap:6px;">
                        <strong style="color:#1e293b;"><?php echo esc_html($php_memory_limit); ?></strong>
                        <span style="padding:1px 6px;border-radius:8px;font-size:10px;font-weight:600;background:<?php echo $memory_is_optimal ? '#dcfce7' : '#fef3c7'; ?>;color:<?php echo $memory_is_optimal ? '#15803d' : '#92400e'; ?>;">
                            <?php echo $memory_is_optimal ? 'Optimal' : 'Low'; ?>
                        </span>
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">WP Memory Limit:</span><strong style="color:#1e293b;"><?php echo esc_html($wp_memory_limit); ?></strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">Max Execution Time:</span><strong style="color:#1e293b;"><?php echo esc_html($max_exec_time); ?></strong></div>
            </div>
        </div>

        <!-- Limits, DB & Libraries -->
        <div style="background:#f8fafc;padding:14px;border-radius:6px;border:1px solid #e2e8f0;">
            <h4 style="margin:0 0 10px 0;font-size:13px;font-weight:700;color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:6px;">Uploads & Extensions</h4>
            <div style="font-size:12px;display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">Upload Max Filesize:</span><strong style="color:#1e293b;"><?php echo esc_html($upload_max_filesize); ?></strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">Post Max Size:</span><strong style="color:#1e293b;"><?php echo esc_html($post_max_size); ?></strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">MySQL Version:</span><strong style="color:#1e293b;"><?php echo esc_html($mysql_version); ?></strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#64748b;">cURL Extension:</span><strong style="color:#1e293b;"><?php echo esc_html(strpos($curl_status, 'Enabled') !== false ? 'Enabled' : 'Disabled'); ?></strong></div>
            </div>
        </div>
    </div>
</div>
