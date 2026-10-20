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
$mem_bytes         = ((string) $php_memory_limit === '-1') ? -1 : (int) wp_convert_hr_to_bytes($php_memory_limit);
$memory_is_optimal = ($mem_bytes === -1 || $mem_bytes >= 128 * MB_IN_BYTES);

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
$report_buffer .= "- Total Popups: " . $popups_count . "\n";
$report_buffer .= "- Captured Submissions: " . $leads_count . "\n";
$report_buffer .= "- A/B Split Campaigns: " . $campaigns_count . "\n";
?>
<?php
$sys_groups = [
    __('WordPress Core', 'wppoppop') => [
        [__('WordPress Version', 'wppoppop'), $wp_version, null],
        [__('WpPopPop Version', 'wppoppop'), $plugin_version, null],
        [__('Multisite Network', 'wppoppop'), $multisite_status, null],
        [__('WP Debug Mode', 'wppoppop'), $wp_debug_status, null],
    ],
    __('PHP & Web Server', 'wppoppop') => [
        [__('PHP Version', 'wppoppop'), $php_version, $php_is_optimal ? ['ok', 'Optimal'] : ['warn', 'Attention']],
        [__('PHP Memory Limit', 'wppoppop'), $php_memory_limit, $memory_is_optimal ? ['ok', 'Optimal'] : ['warn', 'Low']],
        [__('WP Memory Limit', 'wppoppop'), $wp_memory_limit, null],
        [__('Max Execution Time', 'wppoppop'), $max_exec_time, null],
    ],
    __('Uploads & Extensions', 'wppoppop') => [
        [__('Upload Max Filesize', 'wppoppop'), $upload_max_filesize, null],
        [__('Post Max Size', 'wppoppop'), $post_max_size, null],
        [__('MySQL Version', 'wppoppop'), $mysql_version, null],
        [__('cURL Extension', 'wppoppop'), $curl_status, null],
        [__('GD Library', 'wppoppop'), $gd_status, null],
    ],
];
?>
<div class="wppoppop-card-box wpp-tools-card">
    <div class="wpp-tools-card-head">
        <span class="dashicons dashicons-dashboard"></span>
        <h3><?php esc_html_e('System Environment & Health Diagnostics', 'wppoppop'); ?></h3>
        <div class="wpp-tools-head-actions">
            <button type="button" id="wppoppop-copy-system-info-btn" class="button wpp-tools-btn"><span class="dashicons dashicons-clipboard"></span><?php esc_html_e('Copy Diagnostic Report', 'wppoppop'); ?></button>
            <button type="button" id="wppoppop-download-system-info-btn" class="button wpp-tools-btn" title="<?php esc_attr_e('Download diagnostic report as a .txt file', 'wppoppop'); ?>"><span class="dashicons dashicons-download"></span><?php esc_html_e('Download .txt', 'wppoppop'); ?></button>
        </div>
    </div>

    <textarea id="wppoppop-system-report-text" class="wpp-tools-hidden" readonly aria-hidden="true" tabindex="-1"><?php echo esc_textarea($report_buffer); ?></textarea>

    <div class="wpp-tools-grid wpp-tools-grid-3">
        <?php foreach ($sys_groups as $group_title => $rows) : ?>
            <div class="wpp-tools-panel">
                <h4><?php echo esc_html($group_title); ?></h4>
                <div class="wpp-kv">
                    <?php foreach ($rows as $row) : ?>
                        <div class="wpp-kv-row">
                            <span class="wpp-kv-label"><?php echo esc_html($row[0]); ?>:</span>
                            <span class="wpp-kv-value">
                                <strong><?php echo esc_html($row[1]); ?></strong>
                                <?php if ($row[2]) : ?>
                                    <span class="wpp-pill is-<?php echo esc_attr($row[2][0]); ?>"><?php echo esc_html($row[2][1]); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
