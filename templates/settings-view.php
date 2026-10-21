<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!isset($settings) || !is_array($settings)) {
    $settings = get_option('wppoppop_settings', []);
}
?>
<div class="wrap wppoppop-dash wppoppop-settings-wrap">
    <?php include WPPOPPOP_PATH . 'templates/settings/header.php'; ?>

    <!-- Native Server-Side Notice Banner -->
    <?php if (isset($notice) && 'saved' === $notice) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Settings saved successfully!', 'wppoppop'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Real-time AJAX Notification Container -->
    <div id="wppoppop-settings-alert" class="notice is-dismissible" style="display:none;">
        <p></p>
    </div>

    <!-- Navigation Tab Buttons (Clean Text - No Icons) -->
    <nav class="wppoppop-settings-tabs-nav wpp-theme-tabs" aria-label="<?php esc_attr_e('Settings Sections', 'wppoppop'); ?>">
        <button type="button" class="wppoppop-tab-btn wpp-theme-tab active" data-tab="general"><?php esc_html_e('General & Mailing', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn wpp-theme-tab" data-tab="performance"><?php esc_html_e('Performance & Preload', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn wpp-theme-tab" data-tab="libraries"><?php esc_html_e('Typography & Libraries', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn wpp-theme-tab" data-tab="security"><?php esc_html_e('Security & Geolocation', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn wpp-theme-tab" data-tab="custom-code"><?php esc_html_e('Custom CSS & JS', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn wpp-theme-tab" data-tab="tools"><?php esc_html_e('Cookie Cache & DB', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn wpp-theme-tab" data-tab="module"><?php esc_html_e('Module', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn wpp-theme-tab" data-tab="builder"><?php esc_html_e('Builder', 'wppoppop'); ?></button>
       
    </nav>

    <!-- Master Settings Form -->
    <form id="wppoppop-settings-form" method="post" action="">
        <?php wp_nonce_field('wppoppop_save_settings_action', 'wppoppop_settings_nonce'); ?>

        <!-- Tab 1 Container: General & Mailing -->
        <div id="tab-general" class="wppoppop-tab-content active" data-tab-content="general">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-general.php'; ?>
        </div>

        <!-- Tab 2 Container: Performance & Preload -->
        <div id="tab-performance" class="wppoppop-tab-content" data-tab-content="performance" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-performance.php'; ?>
        </div>

        <!-- Tab 3 Container: Typography & Libraries -->
        <div id="tab-libraries" class="wppoppop-tab-content" data-tab-content="libraries" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-libraries.php'; ?>
        </div>

        <!-- Tab 4 Container: Security & Geolocation -->
        <div id="tab-security" class="wppoppop-tab-content" data-tab-content="security" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-security.php'; ?>
        </div>

        <!-- Tab 5 Container: Custom Code & Scripts -->
        <div id="tab-custom-code" class="wppoppop-tab-content" data-tab-content="custom-code" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-custom-code.php'; ?>
        </div>

        <!-- Tab 6 Container: System Tools & Diagnostics -->
        <div id="tab-tools" class="wppoppop-tab-content" data-tab-content="tools" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-tools.php'; ?>
        </div>

        <!-- Tab 7 Container: Module (Campaign Settings on/off) -->
        <div id="tab-module" class="wppoppop-tab-content" data-tab-content="module" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-module.php'; ?>
        </div>

        <!-- Tab 8 Container: Builder tools -->
        <div id="tab-builder" class="wppoppop-tab-content" data-tab-content="builder" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-builder.php'; ?>
        </div>

        <!-- Bottom Action Persistence Bar (Clean Text Button - No Misaligned Icons) -->
        <div class="wppoppop-settings-submit-bar">
            <button type="submit" id="wppoppop-save-settings-btn" class="button button-primary">
                <?php esc_html_e('Save Settings', 'wppoppop'); ?>
            </button>
            <span id="wppoppop-save-settings-spinner" class="spinner" style="float:none;margin:0;"></span>
            <span id="wppoppop-save-settings-msg" style="font-weight:600;font-size:13px;"></span>
        </div>
    </form>
</div>
