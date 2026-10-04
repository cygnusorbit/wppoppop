<?php
namespace WPPopPop\Admin;

class Builder {
    public static function init(): void {
        add_action('admin_menu', [__CLASS__, 'register_builder_menu']);
        add_action('admin_init', [__CLASS__, 'redirect_standard_add_new']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_builder_assets']);
        add_action('wp_ajax_wppoppop_save_builder_popup', [__CLASS__, 'ajax_save_popup']);
    }

    public static function register_builder_menu(): void {
        // Registers /wp-admin/admin.php?page=lepopup-add
        add_submenu_page(
            'edit.php?post_type=wppoppop',
            __('Create Popup', 'wppoppop'),
            __('Create Popup', 'wppoppop'),
            'manage_options',
            'lepopup-add',
            [__CLASS__, 'render_builder_page']
        );
    }

    public static function redirect_standard_add_new(): void {
        global $pagenow;
        if ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'wppoppop') {
            wp_safe_redirect(admin_url('admin.php?page=lepopup-add'));
            exit;
        }
    }

    public static function enqueue_builder_assets(string $hook): void {
        if (strpos($hook, 'lepopup-add') === false && (!isset($_GET['page']) || $_GET['page'] !== 'lepopup-add')) {
            return;
        }

        wp_enqueue_style('dashicons');
        wp_enqueue_style(
            'wppoppop-builder',
            WPPOPPOP_URL . 'assets/css/builder.css',
            ['dashicons'],
            WPPOPPOP_VERSION
        );

        wp_enqueue_script(
            'wppoppop-builder-js',
            WPPOPPOP_URL . 'assets/js/builder.js',
            ['jquery'],
            WPPOPPOP_VERSION,
            true
        );

        wp_localize_script('wppoppop-builder-js', 'WPPopPopBuilder', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('wppoppop_builder_save_nonce'),
        ]);
    }

    public static function render_builder_page(): void {
        $popup_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        $title    = '';
        $layers   = '[]';

        if ($popup_id > 0) {
            $post = get_post($popup_id);
            if ($post && $post->post_type === 'wppoppop') {
                $title  = $post->post_title;
                $layers = get_post_meta($popup_id, '_wppoppop_builder_layers', true) ?: '[]';
            }
        }

        if (empty($title)) {
            $title = 'popup-' . gmdate('Y-m-d-h-i-s');
        }
        ?>
        <div class="wrap lepopup-builder-wrap">
            <div class="lepopup-header-row">
                <h1 class="lepopup-main-title"><?php echo esc_html__('Green Popups - Edit Popup', 'wppoppop'); ?></h1>
                <div class="lepopup-header-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=lepopup-add')); ?>" class="lepopup-outline-btn"><?php echo esc_html__('Create New Popup', 'wppoppop'); ?></a>
                    <a href="https://greenpopups.com/documentation/" target="_blank" rel="noopener noreferrer" class="lepopup-outline-btn"><?php echo esc_html__('Online Documentation', 'wppoppop'); ?></a>
                </div>
            </div>

            <div class="lepopup-editor-workspace">
                <!-- Dark Control Top Bar -->
                <div class="lepopup-dark-bar">
                    <div class="lepopup-bar-left">
                        <button type="button" class="lepopup-icon-btn" id="lepopup-settings-btn" title="Popup Settings">
                            <span class="dashicons dashicons-admin-generic"></span>
                        </button>
                        <input type="text" id="lepopup-popup-title" class="lepopup-title-input" value="<?php echo esc_attr($title); ?>" placeholder="Enter Popup Title..." />
                        <input type="hidden" id="lepopup-popup-id" value="<?php echo esc_attr($popup_id); ?>" />
                    </div>
                    <div class="lepopup-bar-right">
                        <button type="button" class="lepopup-btn-save" id="lepopup-save-btn">
                            <span class="dashicons dashicons-media-default"></span>
                            <span class="lepopup-save-text"><?php echo esc_html__('Save', 'wppoppop'); ?></span>
                        </button>
                    </div>
                </div>

                <!-- Tab Row -->
                <div class="lepopup-tabs-bar">
                    <button type="button" class="lepopup-tab active" data-tab="page"><?php echo esc_html__('Page', 'wppoppop'); ?></button>
                    <button type="button" class="lepopup-tab" data-tab="confirmation"><?php echo esc_html__('Confirmation', 'wppoppop'); ?></button>
                    <button type="button" class="lepopup-tab-add" id="lepopup-add-page-btn">+ <?php echo esc_html__('Add Page', 'wppoppop'); ?></button>
                </div>

                <!-- Elements Toolbar -->
                <div class="lepopup-elements-toolbar" id="lepopup-toolbar">
                    <button type="button" class="lepopup-tool-btn" data-type="rectangle" title="Rectangle / Container"><span class="dashicons dashicons-format-image"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="image" title="Image"><span class="dashicons dashicons-camera"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="video" title="Video"><span class="dashicons dashicons-video-alt3"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="text" title="Text / Heading"><span class="lepopup-txt-icon">A</span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="html" title="Custom HTML"><span class="dashicons dashicons-editor-code"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="close" title="Close Button"><span class="dashicons dashicons-no-alt"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="ribbon" title="Ribbon / Badge"><span class="dashicons dashicons-tag"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="divider" title="Divider"><span class="dashicons dashicons-minus"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="link" title="Link Button"><span class="dashicons dashicons-admin-links"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="input" title="Text Input"><span class="dashicons dashicons-edit"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="email" title="Email Input"><span class="dashicons dashicons-email"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="number" title="Number Field"><span class="lepopup-txt-icon">42</span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="arrows" title="Sort / Arrows"><span class="dashicons dashicons-sort"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="align" title="Paragraph"><span class="dashicons dashicons-editor-alignleft"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="dropdown" title="Dropdown Select"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="checkbox" title="Checkbox"><span class="dashicons dashicons-yes"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="radio" title="Radio Button"><span class="dashicons dashicons-marker"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="list" title="Bullet List"><span class="dashicons dashicons-editor-ul"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="gallery" title="Gallery"><span class="dashicons dashicons-images-alt2"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="tile" title="Tile / Grid"><span class="lepopup-txt-icon">tile</span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="calendar" title="Datepicker"><span class="dashicons dashicons-calendar-alt"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="clock" title="Timepicker"><span class="dashicons dashicons-clock"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="upload" title="File Upload"><span class="dashicons dashicons-upload"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="lock" title="Locker"><span class="dashicons dashicons-lock"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="star" title="Rating"><span class="dashicons dashicons-star-filled"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="hidden" title="Hidden Field"><span class="dashicons dashicons-hidden"></span></button>
                    <button type="button" class="lepopup-tool-btn" data-type="submit" title="Submit Button"><span class="dashicons dashicons-share-alt2"></span></button>
                </div>

                <!-- Checkered Canvas Area -->
                <div class="lepopup-canvas-container" id="lepopup-canvas-container">
                    <div class="lepopup-canvas-box" id="lepopup-canvas-box">
                        <div class="lepopup-canvas-layers" id="lepopup-canvas-layers"></div>
                        <div class="lepopup-resize-handle" id="lepopup-resize-handle" title="Resize canvas"></div>
                    </div>

                    <!-- Floating Layers Panel -->
                    <div class="lepopup-layers-panel" id="lepopup-layers-panel">
                        <div class="lepopup-layers-header">
                            <span class="lepopup-layers-title"><?php echo esc_html__('LAYERS', 'wppoppop'); ?></span>
                            <span class="dashicons dashicons-move lepopup-layers-drag-icon"></span>
                        </div>
                        <div class="lepopup-layers-body">
                            <div class="lepopup-layers-hint" id="lepopup-layers-hint">
                                1. Click any button on elements toolbar to add new layer.<br>
                                2. Sort layers to change z-index.
                            </div>
                            <ul class="lepopup-layers-list" id="lepopup-layers-list"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div id="lepopup-notification" class="lepopup-toast"></div>
        </div>
        <script>
            window.lepopupInitialLayers = <?php echo !empty($layers) ? $layers : '[]'; ?>;
        </script>
        <?php
    }

    public static function ajax_save_popup(): void {
        check_ajax_referer('wppoppop_builder_save_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        $popup_id = isset($_POST['popup_id']) ? absint($_POST['popup_id']) : 0;
        $title    = sanitize_text_field($_POST['title'] ?? '');
        $layers   = wp_unslash($_POST['layers'] ?? '[]');

        if (empty($title)) {
            $title = 'popup-' . gmdate('Y-m-d-h-i-s');
        }

        if ($popup_id > 0) {
            $updated = wp_update_post([
                'ID'         => $popup_id,
                'post_title' => $title,
                'post_type'  => 'wppoppop',
            ]);
            $saved_id = $popup_id;
        } else {
            $saved_id = wp_insert_post([
                'post_title'  => $title,
                'post_status' => 'publish',
                'post_type'   => 'wppoppop',
            ]);
        }

        if (is_wp_error($saved_id) || !$saved_id) {
            wp_send_json_error(['message' => 'Failed to save popup.']);
        }

        update_post_meta($saved_id, '_wppoppop_builder_layers', $layers);

        wp_send_json_success([
            'popup_id' => $saved_id,
            'message'  => __('Popup saved successfully!', 'wppoppop'),
        ]);
    }
}
