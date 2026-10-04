<?php
namespace WPPopPop\Admin;

class Builder {
    public static function init(): void {
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_builder_assets']);
        add_action('wp_ajax_wppoppop_save_builder_popup', [__CLASS__, 'ajax_save_popup']);
    }

    public static function enqueue_builder_assets(string $hook): void {
        if (strpos($hook, 'wppoppop-add') === false && (!isset($_GET['page']) || $_GET['page'] !== 'wppoppop-add')) {
            return;
        }

        wp_enqueue_style('dashicons');
        wp_enqueue_style('wppoppop-builder', WPPOPPOP_URL . 'assets/css/builder.css', ['dashicons'], WPPOPPOP_VERSION);
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_script('wppoppop-builder-js', WPPOPPOP_URL . 'assets/js/builder.js', ['jquery', 'jquery-ui-draggable'], WPPOPPOP_VERSION, true);

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
        <div class="wrap wppoppop-builder-wrap">
            <div class="wppoppop-header-row">
                <h1 class="wppoppop-main-title"><?php echo esc_html__('Green Popups - Edit Popup', 'wppoppop'); ?></h1>
                <div class="wppoppop-header-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-add')); ?>" class="wppoppop-outline-btn"><?php echo esc_html__('Create New Popup', 'wppoppop'); ?></a>
                    <a href="https://greenpopups.com/documentation/" target="_blank" rel="noopener noreferrer" class="wppoppop-outline-btn"><?php echo esc_html__('Online Documentation', 'wppoppop'); ?></a>
                </div>
            </div>

            <div class="wppoppop-editor-workspace">
                <!-- Top Dark Control Bar -->
                <div class="wppoppop-dark-bar">
                    <div class="wppoppop-bar-left">
                        <button type="button" class="wppoppop-icon-btn" id="wppoppop-modal-settings-btn" title="Popup Settings">
                            <span class="dashicons dashicons-admin-generic"></span>
                        </button>
                        <input type="text" id="wppoppop-popup-title" class="wppoppop-title-input" value="<?php echo esc_attr($title); ?>" placeholder="Enter Popup Title..." />
                        <input type="hidden" id="wppoppop-popup-id" value="<?php echo esc_attr($popup_id); ?>" />
                    </div>
                    <div class="wppoppop-bar-right">
                        <button type="button" class="wppoppop-btn-pink wppoppop-btn-save" id="wppoppop-save-btn">
                            <span class="dashicons dashicons-saved"></span>
                            <span class="wppoppop-save-text"><?php echo esc_html__('Save', 'wppoppop'); ?></span>
                        </button>
                    </div>
                </div>

                <!-- Page / Confirmation Tabs -->
                <div class="wppoppop-tabs-bar">
                    <button type="button" class="wppoppop-tab active" data-tab="page"><?php echo esc_html__('Page', 'wppoppop'); ?></button>
                    <button type="button" class="wppoppop-tab" data-tab="confirmation"><?php echo esc_html__('Confirmation', 'wppoppop'); ?></button>
                    <button type="button" class="wppoppop-tab-add" id="wppoppop-add-page-btn">+ <?php echo esc_html__('Add Page', 'wppoppop'); ?></button>
                </div>

                <!-- Elements Toolbar -->
                <div class="wppoppop-elements-toolbar" id="wppoppop-toolbar">
                    <button type="button" class="wppoppop-tool-btn" data-type="rectangle" title="Container Box"><span class="dashicons dashicons-format-image"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="text" title="Heading / Text"><span class="wppoppop-txt-icon">A</span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="email" title="Email Input"><span class="dashicons dashicons-email"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="input" title="Text Input"><span class="dashicons dashicons-edit"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="submit" title="Action Button"><span class="dashicons dashicons-share-alt2"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="close" title="Close Button"><span class="dashicons dashicons-no-alt"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="ribbon" title="Ribbon Badge"><span class="dashicons dashicons-tag"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="divider" title="Divider Line"><span class="dashicons dashicons-minus"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="html" title="Custom HTML"><span class="dashicons dashicons-editor-code"></span></button>
                </div>

                <!-- Canvas Workspace -->
                <div class="wppoppop-canvas-container" id="wppoppop-canvas-container">
                    <div class="wppoppop-canvas-box" id="wppoppop-canvas-box">
                        <div class="wppoppop-canvas-layers" id="wppoppop-canvas-layers"></div>
                    </div>

                    <!-- Floating Movable Layers Panel -->
                    <div class="wppoppop-layers-panel" id="wppoppop-layers-panel">
                        <div class="wppoppop-layers-header" id="wppoppop-layers-drag-handle">
                            <span class="wppoppop-layers-title"><?php echo esc_html__('LAYERS', 'wppoppop'); ?></span>
                            <span class="dashicons dashicons-move wppoppop-layers-drag-icon"></span>
                        </div>
                        <div class="wppoppop-layers-body">
                            <div class="wppoppop-layers-hint" id="wppoppop-layers-hint">
                                1. Click any button on elements toolbar to add new layer.<br>
                                2. Sort layers to change z-index.
                            </div>
                            <ul class="wppoppop-layers-list" id="wppoppop-layers-list"></ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layer Inspector Modal -->
            <div id="wppoppop-inspector-modal" class="wppoppop-inspector-modal" style="display:none;">
                <div class="wppoppop-inspector-inner">
                    <div class="wppoppop-inspector-header">
                        <h3 id="wppoppop-inspector-title"><?php echo esc_html__('Layer Settings', 'wppoppop'); ?></h3>
                        <button type="button" class="wppoppop-inspector-close">&times;</button>
                    </div>
                    <div class="wppoppop-inspector-fields">
                                                <div class="wppoppop-field-row">
                            <label><?php echo esc_html__('Font Family', 'wppoppop'); ?></label>
                            <select id="prop-fontfamily" class="wppoppop-select" style="width:100%;">
                                <option value="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">System Default</option>
                                <option value="Roboto, sans-serif">Roboto</option>
                                <option value="'Open Sans', sans-serif">Open Sans</option>
                                <option value="Montserrat, sans-serif">Montserrat</option>
                                <option value="Poppins, sans-serif">Poppins</option>
                                <option value="Inter, sans-serif">Inter</option>
                                <option value="'Playfair Display', serif">Playfair Display</option>
                                <option value="Merriweather, serif">Merriweather</option>
                                <option value="Oswald, sans-serif">Oswald</option>
                            </select>
                        </div>
                        <div class="wppoppop-field-row">
                            <label><?php echo esc_html__('Content / Label', 'wppoppop'); ?></label>
                            <input type="text" id="prop-content" class="wppoppop-input" />
                        </div>
                        <div class="wppoppop-field-row">
                            <label><?php echo esc_html__('Text Color', 'wppoppop'); ?></label>
                            <input type="color" id="prop-color" class="wppoppop-color-input" />
                        </div>
                        <div class="wppoppop-field-row">
                            <label><?php echo esc_html__('Background Color', 'wppoppop'); ?></label>
                            <input type="color" id="prop-bg" class="wppoppop-color-input" />
                        </div>
                        <div class="wppoppop-field-row">
                            <label><?php echo esc_html__('Font Size (px)', 'wppoppop'); ?></label>
                            <input type="number" id="prop-fontsize" min="10" max="72" class="wppoppop-input" />
                        </div>
                        <div class="wppoppop-field-row">
                            <label><?php echo esc_html__('Border Radius (px)', 'wppoppop'); ?></label>
                            <input type="number" id="prop-radius" min="0" max="50" class="wppoppop-input" />
                        </div>
                    </div>
                    <div class="wppoppop-inspector-actions">
                        <button type="button" class="wppoppop-btn-pink" id="wppoppop-inspector-apply"><?php echo esc_html__('Apply', 'wppoppop'); ?></button>
                    </div>
                </div>
            </div>

            <div id="wppoppop-toast" class="wppoppop-toast" style="display:none;"></div>
        </div>
        <script>
            window.wppoppopInitialLayers = <?php echo !empty($layers) ? $layers : '[]'; ?>;
        </script>
        <?php
    }

    public static function ajax_save_popup(): void {
        check_ajax_referer('wppoppop_builder_save_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $popup_id = isset($_POST['popup_id']) ? absint($_POST['popup_id']) : 0;
        $title    = sanitize_text_field($_POST['title'] ?? '');
        $layers   = wp_unslash($_POST['layers'] ?? '[]');

        if (empty($title)) {
            $title = 'popup-' . gmdate('Y-m-d-h-i-s');
        }

        if ($popup_id > 0) {
            wp_update_post([
                'ID'         => $popup_id,
                'post_title' => $title,
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
            wp_send_json_error(['message' => __('Failed to save popup.', 'wppoppop')]);
        }

        update_post_meta($saved_id, '_wppoppop_builder_layers', $layers);

        wp_send_json_success([
            'popup_id' => $saved_id,
            'message'  => __('Popup saved successfully!', 'wppoppop'),
        ]);
    }
}
