<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wppoppop-settings-wrap">
    <h1 class="wp-heading-inline">WpPopPop Settings</h1>
    <hr class="wp-header-end">

    <div id="wppoppop-settings-notice" style="display:none;" class="notice is-dismissible"></div>

    <div class="wppoppop-settings-tabs-nav">
        <button type="button" class="wppoppop-tab-btn active" data-tab="general">General & Mailing</button>
        <button type="button" class="wppoppop-tab-btn" data-tab="performance">Performance & Preload</button>
        <button type="button" class="wppoppop-tab-btn" data-tab="libraries">Typography & Libraries</button>
        <button type="button" class="wppoppop-tab-btn" data-tab="security">Security & Geolocation</button>
        <button type="button" class="wppoppop-tab-btn" data-tab="custom-code">Custom CSS & JS</button>
        <button type="button" class="wppoppop-tab-btn" data-tab="tools">System Tools</button>
    </div>

    <form id="wppoppop-settings-form" method="post">
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
            <button type="submit" class="button button-primary" id="wppoppop-settings-save-btn">Save Changes</button>
        </p>
    </form>
</div>
