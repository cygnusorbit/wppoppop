<?php
if (!defined('ABSPATH')) {
    exit;
}
$bar_title = __('Popups Library', 'wppoppop');
$bar_icon  = 'dashicons-category';
?>
<h1 class="screen-reader-text"><?php esc_html_e('Popups & Templates Library', 'wppoppop'); ?></h1>
<?php include WPPOPPOP_PATH . 'templates/partials/dash-bar.php'; ?>

<div class="wppoppop-library-header">
    <div class="wppoppop-library-toolbar">
        <div class="wppoppop-library-intro">
            <h2><?php esc_html_e('Popups & Templates Library', 'wppoppop'); ?></h2>
            <p><?php esc_html_e('Browse pre-configured, high-converting templates and import them directly into the visual builder.', 'wppoppop'); ?></p>
        </div>
        <div class="wppoppop-library-search-wrap">
            <span class="dashicons dashicons-search"></span>
            <input type="text" id="wppoppop-lib-search" placeholder="<?php echo esc_attr__('Search templates by keyword...', 'wppoppop'); ?>" autocomplete="off">
        </div>
    </div>

    <div class="wppoppop-lib-categories wppoppop-library-categories">
        <button type="button" class="wppoppop-cat-btn active" data-cat="all"><?php esc_html_e('All Templates', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-cat-btn" data-cat="lead-gen"><?php esc_html_e('Lead Generation', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-cat-btn" data-cat="ecommerce"><?php esc_html_e('E-Commerce & Coupons', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-cat-btn" data-cat="gamified"><?php esc_html_e('Gamified & Interactive', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-cat-btn" data-cat="feedback"><?php esc_html_e('Surveys & Feedback', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-cat-btn" data-cat="announcement"><?php esc_html_e('Announcements', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-cat-btn" data-cat="custom"><?php esc_html_e('My Templates', 'wppoppop'); ?></button>
    </div>
</div>
