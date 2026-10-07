<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('wppoppop_settings', []);
?>
<div class="wrap wppoppop-settings-wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('WpPopPop Settings', 'wppoppop'); ?></h1>
    <hr class="wp-header-end">

    <?php settings_errors('wppoppop_settings'); ?>
    <div id="wppoppop-settings-notice" style="display:none;" class="notice is-dismissible"></div>

    <div class="wppoppop-settings-tabs-nav">
        <button type="button" class="wppoppop-tab-btn active" data-tab="general"><?php esc_html_e('General & Mailing', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn" data-tab="performance"><?php esc_html_e('Performance & Preload', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn" data-tab="libraries"><?php esc_html_e('Typography & Libraries', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn" data-tab="security"><?php esc_html_e('Security & Geolocation', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn" data-tab="custom-code"><?php esc_html_e('Custom CSS & JS', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-tab-btn" data-tab="tools"><?php esc_html_e('System Tools', 'wppoppop'); ?></button>
    </div>

    <form id="wppoppop-settings-form" method="post" action="options.php">
        <?php
        settings_fields('wppoppop_settings_group');
        ?>
        <div class="wppoppop-tab-content active" id="tab-general">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-general.php'; ?>
        </div>
        <div class="wppoppop-tab-content" id="tab-performance" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-performance.php'; ?>
        </div>
        <div class="wppoppop-tab-content" id="tab-libraries" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-libraries.php'; ?>
        </div>
        <div class="wppoppop-tab-content" id="tab-security" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-security.php'; ?>
        </div>
        <div class="wppoppop-tab-content" id="tab-custom-code" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-custom-code.php'; ?>
        </div>
        <div class="wppoppop-tab-content" id="tab-tools" style="display:none;">
            <?php include WPPOPPOP_PATH . 'templates/settings/tab-tools.php'; ?>
        </div>

        <p class="submit">
            <?php submit_button(__('Save Changes', 'wppoppop'), 'primary', 'submit', false, ['id' => 'wppoppop-settings-save-btn']); ?>
        </p>
    </form>
</div>
