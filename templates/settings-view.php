<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!isset($settings) || !is_array($settings)) {
    $settings = get_option('wppoppop_settings', []);
}
?>
<div class="wrap wppoppop-settings-wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e('WpPopPop Settings', 'wppoppop'); ?>
    </h1>
    <p class="description" style="margin-top:4px;color:#64748b;">
        <?php esc_html_e('Configure domain delivery rules, performance preloads, component extensions, bot security, and cache epochs.', 'wppoppop'); ?>
    </p>

    <!-- Native Server-Side Notice Banner -->
    <?php if (isset($notice) && 'saved' === $notice) : ?>
        <div class="notice notice-success is-dismissible" style="margin-top:16px;">
            <p><?php esc_html_e('Settings saved successfully!', 'wppoppop'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Real-time AJAX Notification Container -->
    <div id="wppoppop-settings-alert" class="notice is-dismissible" style="display:none;margin-top:16px;">
        <p></p>
    </div>

    <!-- Navigation Tab Buttons (Clean Text - No Icons) -->
    <nav class="wppoppop-settings-tabs-nav" aria-label="<?php esc_attr_e('Settings Sections', 'wppoppop'); ?>">
        <button type="button" class="wppoppop-tab-btn active" data-tab="general"><?php esc_html_e('General & Mailing', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn" data-tab="performance"><?php esc_html_e('Performance & Preload', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn" data-tab="libraries"><?php esc_html_e('Typography & Libraries', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn" data-tab="security"><?php esc_html_e('Security & Geolocation', 'wppoppop'); ?></button>
         <button type="button" class="wppoppop-tab-btn" data-tab="tools"><?php esc_html_e('Cookie Cache & DB', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn" data-tab="custom-code"><?php esc_html_e('Custom CSS & JS', 'wppoppop'); ?></button>
       
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

        <!-- Bottom Action Persistence Bar (Clean Text Button - No Misaligned Icons) -->
        <div class="wppoppop-settings-submit-bar" style="margin-top:24px;display:flex;align-items:center;gap:12px;">
            <button type="submit" id="wppoppop-save-settings-btn" class="button button-primary button-hero" style="min-height:42px;line-height:40px;padding:0 24px;font-size:14px;cursor:pointer;">
                <?php esc_html_e('Save Settings', 'wppoppop'); ?>
            </button>
            <span id="wppoppop-save-settings-spinner" class="spinner" style="float:none;margin:0;"></span>
            <span id="wppoppop-save-settings-msg" style="font-weight:600;font-size:13px;"></span>
        </div>
    </form>
</div>
