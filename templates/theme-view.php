<?php
if (!defined('ABSPATH')) {
    exit;
}
$theme_schema = WpPopPop_Admin_Theme::schema();
$theme_values = WpPopPop_Admin_Theme::get_values();
$theme_status = isset($_GET['wpp_status']) ? sanitize_key(wp_unslash($_GET['wpp_status'])) : '';
?>
<div class="wrap wppoppop-dash wppoppop-theme-wrap">
    <?php include WPPOPPOP_PATH . 'templates/theme/header.php'; ?>

    <form method="post" id="wpp-theme-form" action="<?php echo esc_url(admin_url('admin.php?page=' . WpPopPop_Admin_Theme::PAGE)); ?>">
        <?php wp_nonce_field(WpPopPop_Admin_Theme::NONCE_ACTION, 'wpp_theme_nonce'); ?>
        <input type="hidden" name="wpp_theme_action" id="wpp-theme-action" value="save">

        <div class="wpp-theme-layout">
            <div class="wpp-theme-main">
                <?php include WPPOPPOP_PATH . 'templates/theme/sections.php'; ?>
            </div>
            <aside class="wpp-theme-side">
                <?php include WPPOPPOP_PATH . 'templates/theme/preview.php'; ?>
                <?php include WPPOPPOP_PATH . 'templates/theme/tools.php'; ?>
            </aside>
        </div>
    </form>
</div>
