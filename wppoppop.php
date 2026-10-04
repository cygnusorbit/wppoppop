<?php
/**
 * Plugin Name:       WP Pop Pop
 * Plugin URI:        https://github.com/cygnusorbit/wppoppop
 * Description:       High-performance, modular layered popup plugin modernizing Green Popups.
 * Version:           1.0.0
 * Author:            Cygnus Orbit
 * License:           GPL-2.0-or-later
 * Text Domain:       wppoppop
 */

defined('ABSPATH') || exit;

define('WPPOPPOP_VERSION', '1.0.0');
define('WPPOPPOP_PATH', plugin_dir_path(__FILE__));
define('WPPOPPOP_URL', plugin_dir_url(__FILE__));

spl_autoload_register(function (string $class) {
    $prefix = 'WPPopPop\\';
    $base_dir = WPPOPPOP_PATH . 'includes/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

add_action('plugins_loaded', function () {
    if (class_exists(\WPPopPop\Core\RemoteEmbedHandler::class)) {
        \WPPopPop\Core\RemoteEmbedHandler::init();
    }
    if (class_exists(\WPPopPop\Core\CountdownTimer::class)) {
        \WPPopPop\Core\CountdownTimer::init();
    }
    if (class_exists(\WPPopPop\Core\WheelManager::class)) {
        \WPPopPop\Core\WheelManager::init();
    }
    if (class_exists(\WPPopPop\Core\ContentLocker::class)) {
        \WPPopPop\Core\ContentLocker::init();
    }
    if (class_exists(\WPPopPop\Core\FloatingLauncher::class)) {
        \WPPopPop\Core\FloatingLauncher::init();
    }
    if (class_exists(\WPPopPop\Core\Plugin::class)) {
        \WPPopPop\Core\Plugin::get_instance()->init();
    }
    if (class_exists(\WPPopPop\Admin\PopupManager::class)) {
        \WPPopPop\Admin\PopupManager::init();
    }
    if (class_exists(\WPPopPop\Admin\MenuManager::class)) {
        \WPPopPop\Admin\MenuManager::init();
    }
    if (class_exists(\WPPopPop\Admin\Builder::class)) {
        \WPPopPop\Admin\Builder::init();
    }
    if (class_exists(\WPPopPop\API\SubmissionHandler::class)) {
        \WPPopPop\API\SubmissionHandler::init();
    }
    if (class_exists(\WPPopPop\Integrations\CRMManager::class)) {
        \WPPopPop\Integrations\CRMManager::init();
    }
    if (class_exists(\WPPopPop\Integrations\PaymentGatewayManager::class)) {
        \WPPopPop\Integrations\PaymentGatewayManager::init();
    }
    if (class_exists(\WPPopPop\Core\FieldProcessor::class)) {
        \WPPopPop\Core\FieldProcessor::init();
    }
    if (class_exists(\WPPopPop\Integrations\WooCommerceManager::class)) {
        \WPPopPop\Integrations\WooCommerceManager::init();
    }
    if (class_exists(\WPPopPop\Core\ConfirmationManager::class)) {
        \WPPopPop\Core\ConfirmationManager::init();
    }
    if (class_exists(\WPPopPop\Core\AutoresponderService::class)) {
        \WPPopPop\Core\AutoresponderService::init();
    }
    if (class_exists(\WPPopPop\Core\GutenbergBlocks::class)) {
        \WPPopPop\Core\GutenbergBlocks::init();
    }
    if (class_exists(\WPPopPop\Core\CelebrationManager::class)) {
        \WPPopPop\Core\CelebrationManager::init();
    }
    if (class_exists(\WPPopPop\Core\WebFonts::class)) {
        \WPPopPop\Core\WebFonts::init();
    }
    if (class_exists(\WPPopPop\Core\Widget::class)) {
        add_action('widgets_init', [\WPPopPop\Core\Widget::class, 'register']);
    }
    if (class_exists(\WPPopPop\Admin\SettingsManager::class)) {
        \WPPopPop\Admin\SettingsManager::init();
    }
    if (class_exists(\WPPopPop\Export\LeadExporter::class)) {
        \WPPopPop\Export\LeadExporter::init();
    }
    if (class_exists(\WPPopPop\Targeting\ABTestingManager::class)) {
        \WPPopPop\Targeting\ABTestingManager::init();
    }
    if (class_exists(\WPPopPop\Targeting\TargetingManager::class)) {
        \WPPopPop\Targeting\TargetingManager::init();
    }
    if (class_exists(\WPPopPop\Admin\StatsManager::class)) {
        \WPPopPop\Admin\StatsManager::init();
    }
    if (class_exists(\WPPopPop\Admin\FieldAnalyticsManager::class)) {
        \WPPopPop\Admin\FieldAnalyticsManager::init();
    }
});
