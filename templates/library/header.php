<?php
if (!defined('ABSPATH')) {
    exit;
}
$bar_title = __('Popups Library', 'wppoppop');
$bar_icon  = 'dashicons-category';
$bar_actions = WPPOPPOP_PATH . 'templates/library/bar-actions.php';
if (!class_exists('WpPopPop_Ajax_Templates')) { require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-templates.php'; }
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
        <?php foreach (WpPopPop_Ajax_Templates::get_categories() as $wpp_cat) : ?>
        <button type="button" class="wppoppop-cat-btn" data-cat="<?php echo esc_attr($wpp_cat['slug']); ?>"><?php echo esc_html($wpp_cat['label']); ?></button>
        <?php endforeach; ?>
    </div>
</div>
