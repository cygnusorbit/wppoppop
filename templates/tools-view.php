<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}

global $wpdb;
$notice_msg  = '';
$notice_type = 'success';

// Handle Tools Actions (single shared service)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wppoppop_tools_nonce'])) {
    list($notice_msg, $notice_type) = WpPopPop_Ajax_Tools::handle_post();
}
?>
<div class="wrap wppoppop-dash wppoppop-tools-wrap">
    <?php include WPPOPPOP_PATH . 'templates/tools/header.php'; ?>

    <?php if (!empty($notice_msg)) : ?>
        <div class="notice notice-<?php echo esc_attr($notice_type); ?> is-dismissible" style="margin-bottom:20px;">
            <p><?php echo esc_html($notice_msg); ?></p>
        </div>
    <?php endif; ?>

    <!-- System Environment & Health Diagnostics -->
    <?php include WPPOPPOP_PATH . 'templates/tools/system-info.php'; ?>

    <!-- Database Schema Table Status & Repair -->
    <?php include WPPOPPOP_PATH . 'templates/tools/database-maintenance.php'; ?>

    <!-- Bulk JSON Backup Export & Import -->
    <?php include WPPOPPOP_PATH . 'templates/tools/bulk-portability.php'; ?>

    <!-- Restore options + maintenance -->
    <?php include WPPOPPOP_PATH . 'templates/tools/maintenance.php'; ?>
</div>
