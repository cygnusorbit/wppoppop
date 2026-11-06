<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'wppoppop'));
}
$ext_can_upload = WpPopPop_Modules_Installer::can_upload() && class_exists('ZipArchive');
if (!class_exists('ZipArchive')) {
    $ext_block = __('The PHP zip extension is required to upload modules. Ask your host to enable it.', 'wppoppop');
} elseif (!WpPopPop_Modules_Installer::file_mods_allowed()) {
    $ext_block = __('File changes are disabled on this site (DISALLOW_FILE_MODS), so modules cannot be uploaded.', 'wppoppop');
} elseif (is_multisite() && !is_super_admin()) {
    $ext_block = __('Only network administrators can upload modules.', 'wppoppop');
} else {
    $ext_block = '';
}
?>
<div class="wrap wppoppop-dash wppoppop-extend-wrap">
    <?php include WPPOPPOP_PATH . 'templates/extend/header.php'; ?>

    <div id="wpp-ext-notice" class="wpp-mod-notice" role="status" aria-live="polite" hidden></div>
    <noscript><div class="notice notice-warning"><p><?php esc_html_e('JavaScript is required to manage modules.', 'wppoppop'); ?></p></div></noscript>

    <div class="wpp-ext-card wpp-ext-upload" id="wpp-ext-upload-card">
        <div class="wpp-ext-card-head">
            <span class="dashicons dashicons-upload"></span>
            <h3><?php esc_html_e('Upload Module', 'wppoppop'); ?></h3>
        </div>
        <?php if ($ext_can_upload) : ?>
            <p class="wpp-ext-help"><?php
                /* translators: %s: maximum zip size */
                echo esc_html(sprintf(__('Upload a module .zip (module.json plus HTML, CSS and JS files, up to %s). An uploaded module stays Available until you click Install.', 'wppoppop'), size_format(WpPopPop_Modules_Installer::MAX_ZIP)));
            ?></p>
            <div class="wpp-ext-upload-row">
                <div class="wpp-ext-drop" id="wpp-ext-drop">
                    <span class="dashicons dashicons-media-archive"></span>
                    <span class="wpp-ext-drop-text"><?php esc_html_e('Drag a module .zip here, or', 'wppoppop'); ?>
                        <label for="wpp-ext-file" class="wpp-ext-browse"><?php esc_html_e('browse', 'wppoppop'); ?></label></span>
                    <input type="file" id="wpp-ext-file" class="wpp-ext-file" accept=".zip,application/zip,application/x-zip-compressed">
                    <span class="wpp-ext-file-name" id="wpp-ext-file-name"></span>
                </div>
                <button type="button" class="button button-primary" id="wpp-ext-upload-btn" disabled><?php esc_html_e('Upload Module', 'wppoppop'); ?></button>
            </div>
        <?php else : ?>
            <p class="wpp-ext-help"><?php echo esc_html($ext_block); ?></p>
        <?php endif; ?>
    </div>

    <div class="wpp-mod-toolbar">
        <ul class="wpp-mod-filters" id="wpp-ext-filters">
            <li><button type="button" class="is-active" data-filter="all"><?php esc_html_e('All', 'wppoppop'); ?> <span class="count">(0)</span></button></li>
            <li><button type="button" data-filter="available"><?php esc_html_e('Available', 'wppoppop'); ?> <span class="count">(0)</span></button></li>
            <li><button type="button" data-filter="installed"><?php esc_html_e('Installed', 'wppoppop'); ?> <span class="count">(0)</span></button></li>
            <li><button type="button" data-filter="builtin"><?php esc_html_e('Built-in', 'wppoppop'); ?> <span class="count">(0)</span></button></li>
            <li><button type="button" data-filter="uploaded"><?php esc_html_e('Uploaded', 'wppoppop'); ?> <span class="count">(0)</span></button></li>
        </ul>
        <div class="wpp-mod-search">
            <label class="screen-reader-text" for="wpp-ext-search"><?php esc_html_e('Search modules', 'wppoppop'); ?></label>
            <input type="search" id="wpp-ext-search" placeholder="<?php echo esc_attr__('Search modules…', 'wppoppop'); ?>" autocomplete="off">
        </div>
    </div>

    <div class="wpp-ext-grid" id="wpp-ext-grid"></div>
</div>
