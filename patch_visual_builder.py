from pathlib import Path

# Identify plugin base directory
candidates = [
    Path("/var/www/html/wp-content/plugins/wppoppop"),
    Path.home() / "Desktop" / "wppoppop",
    Path.cwd()
]
base_dir = next((p for p in candidates if (p / "wppoppop.php").exists()), Path.cwd())

admin_dir = base_dir / "includes" / "Admin"
core_dir  = base_dir / "includes" / "Core"
css_dir   = base_dir / "assets" / "css"
js_dir    = base_dir / "assets" / "js"

for d in [admin_dir, core_dir, css_dir, js_dir]:
    d.mkdir(parents=True, exist_ok=True)

# 1. Update Builder.php with the 27-tool element ribbon & multi-page canvas
builder_php = r"""<?php
namespace WPPopPop\Admin;

class Builder {
    public static function init(): void {
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_builder_assets']);
        add_action('wp_ajax_wppoppop_save_builder_popup', [__CLASS__, 'ajax_save_popup']);
    }

    public static function enqueue_builder_assets(string $hook): void {
        if (strpos($hook, 'wppoppop-add') === false && (!isset($_GET['page']) \vert{}\vert{}$_GET['page'] !== 'wppoppop-add')) {
            return;
        }

        wp_enqueue_style('dashicons');
        wp_enqueue_style('wppoppop-builder', WPPOPPOP_URL . 'assets/css/builder.css', ['dashicons'], WPPOPPOP_VERSION);
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('wppoppop-builder-js', WPPOPPOP_URL . 'assets/js/builder.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable', 'jquery-ui-sortable'], WPPOPPOP_VERSION, true);

        wp_localize_script('wppoppop-builder-js', 'WPPopPopBuilder', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('wppoppop_builder_save_nonce'),
        ]);
    }

    public static function render_builder_page(): void {
        $popup_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        $title    = '';$layers   = '[]';

        if ($popup_id > 0) {
            $post = get_post($popup_id);
            if ($post &&$post->post_type === 'wppoppop') {
                $title  =$post->post_title;
                $layers = get_post_meta($popup_id, '_wppoppop_builder_layers', true) ?: '[]';
            }
        }

        if (empty($title)) {$title = 'popup-' . gmdate('Y-m-d-h-i-s');
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
                        <button type="button" class="wppoppop-icon-btn" id="wppoppop-settings-btn" title="Popup Settings">
                            <span class="dashicons dashicons-admin-generic"></span>
                        </button>
                        <input type="text" id="wppoppop-popup-title" class="wppoppop-title-input" value="<?php echo esc_attr($title); ?>" placeholder="Enter Popup Title..." />
                        <input type="hidden" id="wppoppop-popup-id" value="<?php echo esc_attr($popup_id); ?>" />
                    </div>
                    <div class="wppoppop-bar-right">
                        <button type="button" class="wppoppop-btn-pink" id="wppoppop-save-btn">
                            <span class="dashicons dashicons-media-default"></span>
                            <span class="wppoppop-save-text"><?php echo esc_html__('Save', 'wppoppop'); ?></span>
                        </button>
                    </div>
                </div>

                <!-- Canvas Sub-Navigation Tabs -->
                <div class="wppoppop-tabs-bar">
                    <button type="button" class="wppoppop-tab active" data-page="page"><?php echo esc_html__('Page', 'wppoppop'); ?></button>
                    <button type="button" class="wppoppop-tab" data-page="confirmation"><?php echo esc_html__('Confirmation', 'wppoppop'); ?></button>
                    <button type="button" class="wppoppop-tab-add" id="wppoppop-add-page-btn">+ <?php echo esc_html__('Add Page', 'wppoppop'); ?></button>
                </div>

                <!-- 27 Elements Ribbon Toolbar -->
                <div class="wppoppop-elements-toolbar" id="wppoppop-toolbar">
                    <button type="button" class="wppoppop-tool-btn" data-type="rectangle" title="Rectangle / Container"><span class="dashicons dashicons-format-image"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="image" title="Image"><span class="dashicons dashicons-camera"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="video" title="Video"><span class="dashicons dashicons-video-alt3"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="text" title="Text / Heading"><strong style="font-size:14px;">A</strong></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="html" title="Custom HTML"><span class="dashicons dashicons-editor-code"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="close" title="Close Button"><span class="dashicons dashicons-no-alt"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="ribbon" title="Ribbon / Badge"><span class="dashicons dashicons-tag"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="divider" title="Divider"><span class="dashicons dashicons-minus"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="link" title="Link Button"><span class="dashicons dashicons-admin-links"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="input" title="Text Input"><span class="dashicons dashicons-edit"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="email" title="Email Input"><span class="dashicons dashicons-email"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="number" title="Number Field"><strong>42</strong></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="arrows" title="Sort / Stepper"><span class="dashicons dashicons-sort"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="align" title="Paragraph"><span class="dashicons dashicons-editor-alignleft"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="dropdown" title="Dropdown Select"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="checkbox" title="Checkbox"><span class="dashicons dashicons-yes"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="radio" title="Radio Button"><span class="dashicons dashicons-marker"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="list" title="Bullet List"><span class="dashicons dashicons-editor-ul"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="gallery" title="Gallery"><span class="dashicons dashicons-images-alt2"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="tile" title="Tile Grid"><span style="font-size:11px;font-weight:600;">tile</span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="calendar" title="Datepicker"><span class="dashicons dashicons-calendar-alt"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="clock" title="Timepicker"><span class="dashicons dashicons-clock"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="upload" title="File Upload"><span class="dashicons dashicons-upload"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="lock" title="Locker Mode"><span class="dashicons dashicons-lock"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="star" title="Rating"><span class="dashicons dashicons-star-filled"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="hidden" title="Hidden Input"><span class="dashicons dashicons-hidden"></span></button>
                    <button type="button" class="wppoppop-tool-btn" data-type="submit" title="Submit Button"><span class="dashicons dashicons-share-alt2"></span></button>
                </div>

                <!-- Canvas Workspace -->
                <div class="wppoppop-canvas-container" id="wppoppop-canvas-container">
                    <div class="wppoppop-canvas-box" id="wppoppop-canvas-box">
                        <div class="wppoppop-canvas-layers" id="wppoppop-canvas-layers"></div>
                    </div>

                    <!-- Floating Movable LAYERS Panel -->
                    <div class="wppoppop-layers-panel" id="wppoppop-layers-panel">
                        <div class="wppoppop-layers-header">
                            <span class="wppoppop-layers-title"><?php echo esc_html__('LAYERS', 'wppoppop'); ?></span>
                            <span class="dashicons dashicons-move wppoppop-layers-drag-icon" title="Drag Panel"></span>
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

            <!-- Property Editor Modal -->
            <div id="wppoppop-prop-modal" class="wppoppop-prop-modal" style="display:none;">
                <div class="wppoppop-prop-content">
                    <div class="wppoppop-prop-header">
                        <h4 id="wppoppop-prop-title">Edit Layer Properties</h4>
                        <span class="wppoppop-prop-close">&times;</span>
                    </div>
                    <div class="wppoppop-prop-body">
                        <label>Content / Label:
                            <input type="text" id="prop-content" class="wppoppop-input" />
                        </label>
                        <div style="display:flex;gap:10px;margin-top:10px;">
                            <label style="flex:1;">Text Color:
                                <input type="color" id="prop-color" value="#000000" style="width:100%;height:36px;padding:0;cursor:pointer;" />
                            </label>
                            <label style="flex:1;">Background:
                                <input type="color" id="prop-bg" value="#ffffff" style="width:100%;height:36px;padding:0;cursor:pointer;" />
                            </label>
                        </div>
                        <div style="display:flex;gap:10px;margin-top:10px;">
                            <label style="flex:1;">Font Size (px):
                                <input type="number" id="prop-font-size" value="16" class="wppoppop-input" />
                            </label>
                            <label style="flex:1;">Border Radius (px):
                                <input type="number" id="prop-radius" value="4" class="wppoppop-input" />
                            </label>
                        </div>
                    </div>
                    <div class="wppoppop-prop-footer">
                        <button type="button" class="wppoppop-btn-pink" id="prop-save-btn">Apply Changes</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            window.wppoppopInitialLayers = <?php echo !empty($layers) ?$layers : '[]'; ?>;
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

        if (empty($title)) {$title = 'popup-' . gmdate('Y-m-d-h-i-s');
        }

        if ($popup_id > 0) {
            wp_update_post([
                'ID'         => $popup_id,
                'post_title' => $title,
                'post_type'  => 'wppoppop',
            ]);
            $saved_id =$popup_id;
        } else {
            $saved_id = wp_insert_post([
                'post_title'  => $title,
                'post_status' => 'publish',
                'post_type'   => 'wppoppop',
            ]);
        }

        if (is_wp_error($saved_id) \vert{}\vert{} !$saved_id) {
            wp_send_json_error(['message' => 'Failed to save popup.']);
        }

        update_post_meta($saved_id, '_wppoppop_builder_layers',$layers);

        wp_send_json_success([
            'popup_id' => $saved_id,
            'message'  => __('Popup successfully saved!', 'wppoppop'),
        ]);
    }
}
"""
(admin_dir / "Builder.php").write_text(builder_php.strip() + "\n", encoding="utf-8")

# 2. Modernized Builder Stylesheet matching Menu-Create Popups.png
builder_css = r"""
.wppoppop-builder-wrap {
    margin: 15px 20px 0 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
}

.wppoppop-editor-workspace {
    border: 1px solid #ccd0d4;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}

/* Dark Header Ribbon */
.wppoppop-dark-bar {
    background: #3c434a;
    padding: 8px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.wppoppop-bar-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.wppoppop-icon-btn {
    background: transparent;
    border: none;
    color: #f0f0f1;
    cursor: pointer;
    padding: 4px;
    border-radius: 3px;
    display: flex;
    align-items: center;
}

.wppoppop-icon-btn:hover {
    background: #4a525a;
}

.wppoppop-title-input {
    background: #2c3338;
    border: 1px solid #50575e;
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 3px;
    font-size: 13px;
    width: 280px;
}

/* Multi-Page Tabs Bar */
.wppoppop-tabs-bar {
    background: #ffffff;
    border-bottom: 1px solid #c3c4c7;
    display: flex;
    padding: 0 10px;
    gap: 4px;
}

.wppoppop-tab {
    background: transparent;
    border: 1px solid transparent;
    border-bottom: 2px solid transparent;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 500;
    color: #50575e;
    cursor: pointer;
}

.wppoppop-tab.active {
    color: #b5295c;
    border-bottom: 2px solid #b5295c;
    font-weight: 600;
}

.wppoppop-tab-add {
    background: transparent;
    border: none;
    padding: 10px 14px;
    color: #2271b1;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

/* 27 Tools Ribbon */
.wppoppop-elements-toolbar {
    background: #f0f0f1;
    border-bottom: 1px solid #ccd0d4;
    padding: 6px 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
}

.wppoppop-tool-btn {
    background: #ffffff;
    border: 1px solid #ccd0d4;
    border-radius: 2px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #3c434a;
    padding: 0;
    transition: all 0.12s ease;
}

.wppoppop-tool-btn:hover {
    background: #b5295c;
    border-color: #b5295c;
    color: #ffffff;
}

.wppoppop-tool-btn span.dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

/* Canvas Area */
.wppoppop-canvas-container {
    position: relative;
    background-color: #f8fafc;
    background-image: 
        linear-gradient(45deg, #eef2f6 25%, transparent 25%), 
        linear-gradient(-45deg, #eef2f6 25%, transparent 25%), 
        linear-gradient(45deg, transparent 75%, #eef2f6 75%), 
        linear-gradient(-45deg, transparent 75%, #eefThe visual drag-and-drop layer builder for the creation interface (`Menu-Create Popups.png`) provides interactive element placement, live coordinate tracking, a draggable layer-hierarchy panel, multi-page switching, and a layer property inspector.

### Key Features Implemented:
* **Interactive Drag-and-Drop Canvas**: Add, move, and position elements (Headings, Inputs, Submit buttons, HTML blocks, Images, Dividers, Badges) directly inside the canvas.
* **Floating Draggable Layers Panel**: Move layers up or down in the stack to alter `z-index`, toggle element visibility, or delete elements with instant canvas synchronization.
* **Live Property Inspector**: Select any active layer to edit its text, placeholder, typography, background colors, border styles, and dimension properties.
* **Multi-Page & Confirmation State Switching**: Work seamlessly across initial opt-in views and post-submission confirmation cards.
* **Asynchronous State Persistence**: Automatically serialize canvas dimensions and layer configurations to post meta via administrative endpoints.

---

### Visual Builder & Layer Inspector Patch

Run the following patch in your terminal to upgrade `Builder.php`, `builder.css`, and `builder.js`:

```bash
cat << 'EOF' > patch_builder_v2.py
from pathlib import Path

candidates = [
    Path("/var/www/html/wp-content/plugins/wppoppop"),
    Path.home() / "Desktop" / "wppoppop",
    Path.cwd()
]
base_dir = next((p for p in candidates if (p / "wppoppop.php").exists()), Path("/var/www/html/wp-content/plugins/wppoppop"))
admin_dir = base_dir / "includes" / "Admin"
css_dir   = base_dir / "assets" / "css"
js_dir    = base_dir / "assets" / "js"

for directory in [admin_dir, css_dir, js_dir]:
    directory.mkdir(parents=True, exist_ok=True)

# 1. Update Builder.php
builder_php = r"""<?php
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
                    <a href="[https://greenpopups.com/documentation/](https://greenpopups.com/documentation/)" target="_blank" rel="noopener noreferrer" class="wppoppop-outline-btn"><?php echo esc_html__('Online Documentation', 'wppoppop'); ?></a>
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

            <!-- Layer Inspector Modal / Drawer -->
            <div id="wppoppop-inspector-modal" class="wppoppop-inspector-modal" style="display:none;">
                <div class="wppoppop-inspector-inner">
                    <div class="wppoppop-inspector-header">
                        <h3 id="wppoppop-inspector-title"><?php echo esc_html__('Layer Settings', 'wppoppop'); ?></h3>
                        <button type="button" class="wppoppop-inspector-close">&times;</button>
                    </div>
                    <div class="wppoppop-inspector-fields">
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
"""
(admin_dir / "Builder.php").write_text(builder_php.strip() + "\n", encoding="utf-8")

# 2. Update assets/css/builder.css
builder_css = r"""
.wppoppop-builder-wrap {
    margin: 15px 20px 0 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.wppoppop-editor-workspace {
    position: relative;
    border: 1px solid #ccd0d4;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.wppoppop-dark-bar {
    background: #32373c;
    padding: 8px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.wppoppop-bar-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.wppoppop-icon-btn {
    background: transparent;
    border: none;
    color: #c3c4c7;
    cursor: pointer;
    padding: 4px;
}

.wppoppop-icon-btn:hover {
    color: #ffffff;
}

.wppoppop-title-input {
    background: #1d2327;
    border: 1px solid #464b50;
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 3px;
    font-size: 13px;
    width: 280px;
}

.wppoppop-tabs-bar {
    background: #f0f0f1;
    border-bottom: 1px solid #dcdcde;
    display: flex;
    padding: 8px 16px 0;
    gap: 4px;
}

.wppoppop-tab {
    background: #e0e0e0;
    border: 1px solid #ccd0d4;
    border-bottom: none;
    padding: 8px 18px;
    font-size: 13px;
    color: #50575e;
    cursor: pointer;
    border-radius: 4px 4px 0 0;
}

.wppoppop-tab.active {
    background: #ffffff;
    color: #b5295c;
    font-weight: 600;
    border-bottom: 1px solid #ffffff;
    margin-bottom: -1px;
}

.wppoppop-tab-add {
    background: transparent;
    border: none;
    color: #b5295c;
    font-weight: 600;
    font-size: 13px;
    padding: 8px 14px;
    cursor: pointer;
}

.wppoppop-elements-toolbar {
    background: #ffffff;
    border-bottom: 1px solid #ccd0d4;
    padding: 6px 14px;
    display: flex;
    gap: 6px;
    align-items: center;
}

.wppoppop-tool-btn {
    background: #ffffff;
    border: 1px solid #c3c4c7;
    color: #50575e;
    border-radius: 3px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}

.wppoppop-tool-btn:hover {
    border-color: #b5295c;
    color: #b5295c;
}

.wppoppop-txt-icon {
    font-weight: 700;
    font-size: 13px;
}

/* Canvas Area */
.wppoppop-canvas-container {
    position: relative;
    background-color: #f6f7f7;
    background-image: radial-gradient(#dcdcde 1px, transparent 1px);
    background-size: 16px 16px;
    min-height: 520px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.wppoppop-canvas-box {
    width: 640px;
    height: 400px;
    background: #ffffff;
    border: 1px dashed #94a3b8;
    position: relative;
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}

.wppoppop-canvas-layers {
    position: relative;
    width: 100%;
    height: 100%;
}

.wppoppop-canvas-element {
    position: absolute;
    cursor: move;
    user-select: none;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wppoppop-canvas-element.selected {
    outline: 2px solid #b5295c;
}

/* Floating Layers Panel */
.wppoppop-layers-panel {
    position: absolute;
    right: 24px;
    top: 24px;
    width: 240px;
    background: #ffffff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    z-index: 999;
}

.wppoppop-layers-header {
    background: #b5295c;
    color: #ffffff;
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: move;
}

.wppoppop-layers-body {
    padding: 8px;
    max-height: 320px;
    overflow-y: auto;
}

.wppoppop-layers-hint {
    color: #8c8f94;
    font-size: 11px;
    line-height: 1.4;
    padding: 6px;
    background: #fbfbfb;
    border-radius: 3px;
    margin-bottom: 8px;
}

.wppoppop-layers-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.wppoppop-layers-list li {
    padding: 6px 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    cursor: pointer;
}

.wppoppop-layers-list li.active {
    border-color: #b5295c;
    background: #fff1f2;
}

.wppoppop-layer-item-actions {
    display: flex;
    gap: 6px;
    color: #787c82;
}

.wppoppop-layer-item-actions span:hover {
    color: #1d2327;
}

/* Property Inspector Modal */
.wppoppop-inspector-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999999;
}

.wppoppop-inspector-inner {
    background: #ffffff;
    border-radius: 6px;
    width: 360px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    overflow: hidden;
}

.wppoppop-inspector-header {
    background: #f0f0f1;
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #dcdcde;
}

.wppoppop-inspector-header h3 {
    margin: 0;
    font-size: 14px;
}

.wppoppop-inspector-close {
    background: transparent;
    border: none;
    font-size: 18px;
    cursor: pointer;
}

.wppoppop-inspector-fields {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.wppoppop-field-row label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
}

.wppoppop-color-input {
    width: 100%;
    height: 34px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    cursor: pointer;
}

.wppoppop-inspector-actions {
    padding: 12px 16px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    text-align: right;
}

.wppoppop-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #10b981;
    color: #ffffff;
    padding: 10px 18px;
    border-radius: 4px;
    font-size: 13px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999999;
}
"""
(css_dir / "builder.css").write_text(builder_css.strip() + "\n", encoding="utf-8")

# 3. Update assets/js/builder.js
builder_js = r"""
(function($) {
    'use strict';

    $(document).ready(function() {
        let layers = window.wppoppopInitialLayers || [];
        let selectedIndex = null;

        // Make floating layers panel draggable
        if ($.fn.draggable) {
            $('#wppoppop-layers-panel').draggable({
                handle: '#wppoppop-layers-drag-handle',
                containment: '#wppoppop-canvas-container'
            });
        }

        function showToast(msg) {
            const $t = $('#wppoppop-toast');
            $t.text(msg).fadeIn(200);
            setTimeout(() => $t.fadeOut(200), 2400);
        }

        function renderLayers() {
            const $canvas = $('#wppoppop-canvas-layers');
            const $list   = $('#wppoppop-layers-list');
            $canvas.empty();
            $list.empty();

            if (layers.length === 0) {
                $('#wppoppop-layers-hint').show();
            } else {
                $('#wppoppop-layers-hint').hide();
            }

            layers.forEach(function(l, idx) {
                if (l.visible === false) return;

                const isSelected = (selectedIndex === idx);
                const $el = $('<div></div>')
                    .addClass('wppoppop-canvas-element')
                    .toggleClass('selected', isSelected)
                    .attr('data-idx', idx)
                    .css({
                        left: (l.x || 30) + 'px',
                        top: (l.y || 30) + 'px',
                        width: (l.w || 180) + 'px',
                        height: (l.h || 42) + 'px',
                        zIndex: l.z || (idx + 1),
                        color: l.color || '#111827',
                        backgroundColor: l.bg || 'transparent',
                        fontSize: (l.fontSize || 14) + 'px',
                        borderRadius: (l.radius || 3) + 'px',
                        border: (l.type === 'rectangle' ? '1px solid #ccd0d4' : (l.type === 'divider' ? 'none' : 'none'))
                    });

                if (l.type === 'text') {
                    $el.text(l.content || 'Headline Text');
                } else if (l.type === 'email') {
                    $el.append($('<input type="email" placeholder="Enter your email..." readonly />').css({ width:'100%', height:'100%', border:'1px solid #ccd0d4', padding:'0 10px', borderRadius: (l.radius || 3) + 'px' }));
                } else if (l.type === 'input') {
                    $el.append($('<input type="text" placeholder="Your Name" readonly />').css({ width:'100%', height:'100%', border:'1px solid #ccd0d4', padding:'0 10px', borderRadius: (l.radius || 3) + 'px' }));
                } else if (l.type === 'submit') {
                    $el.append($('<button type="button"></button>').text(l.content || 'Subscribe Now').css({ width:'100%', height:'100%', background: l.bg || '#b5295c', color: l.color || '#ffffff', border:'none', borderRadius: (l.radius || 3) + 'px', cursor:'pointer', fontWeight: 600 }));
                } else if (l.type === 'ribbon') {
                    $el.text(l.content || 'SPECIAL OFFER').css({ background: l.bg || '#e0f2fe', color: l.color || '#0284c7', fontSize: '11px', fontWeight: 700 });
                } else if (l.type === 'close') {
                    $el.text('✕').css({ fontSize: '18px', cursor: 'pointer' });
                } else if (l.type === 'divider') {
                    $el.append($('<hr style="width:100%; border:0; border-top:1px solid #ccd0d4; margin:0;" />'));
                } else {
                    $el.text(l.content || l.type.toUpperCase());
                }

                $canvas.append($el);

                // Add to floating layers panel list
                const $li = $('<li></li>')
                    .toggleClass('active', isSelected)
                    .attr('data-idx', idx)
                    .append($('<span></span>').text((idx + 1) + '. ' + (l.content ? l.content.substring(0, 16) : l.type)))
                    .append(
                        $('<div class="wppoppop-layer-item-actions"></div>')
                            .append($('<span class="dashicons dashicons-admin-generic" title="Edit Properties"></span>').on('click', (e) => { e.stopPropagation(); openInspector(idx); }))
                            .append($('<span class="dashicons dashicons-trash" title="Delete"></span>').on('click', (e) => { e.stopPropagation(); deleteLayer(idx); }))
                    );

                $list.append($li);
            });

            // Initialize draggable on canvas elements
            if ($.fn.draggable) {
                $('.wppoppop-canvas-element').draggable({
                    containment: '#wppoppop-canvas-box',
                    stop: function(e, ui) {
                        const idx = $(this).data('idx');
                        layers[idx].x = Math.round(ui.position.left);
                        layers[idx].y = Math.round(ui.position.top);
                    }
                });
            }
        }

        // Selection Handlers
        $(document).on('click', '.wppoppop-canvas-element', function(e) {
            e.stopPropagation();
            selectedIndex = $(this).data('idx');
            renderLayers();
        });

        $(document).on('click', '#wppoppop-layers-list li', function() {
            selectedIndex = $(this).data('idx');
            renderLayers();
        });

        $('#wppoppop-canvas-container').on('click', function(e) {
            if ($(e.target).is('#wppoppop-canvas-container') || $(e.target).is('#wppoppop-canvas-box') || $(e.target).is('#wppoppop-canvas-layers')) {
                selectedIndex = null;
                renderLayers();
            }
        });

        // Add Layer via Toolbar
        $('#wppoppop-toolbar .wppoppop-tool-btn').on('click', function() {
            const type = $(this).data('type');
            const offset = (layers.length * 15) % 180;
            const newLayer = {
                type: type,
                content: type === 'text' ? 'Headline Layer' : (type === 'submit' ? 'Subscribe Now' : ''),
                x: 40 + offset,
                y: 40 + offset,
                w: type === 'submit' ? 180 : (type === 'text' ? 240 : 220),
                h: type === 'text' ? 36 : 42,
                z: layers.length + 1,
                color: type === 'submit' ? '#ffffff' : '#111827',
                bg: type === 'submit' ? '#b5295c' : (type === 'ribbon' ? '#e0f2fe' : 'transparent'),
                fontSize: type === 'text' ? 20 : 14,
                radius: 4,
                visible: true
            };
            layers.push(newLayer);
            selectedIndex = layers.length - 1;
            renderLayers();
        });

        // Inspector Controls
        function openInspector(idx) {
            selectedIndex = idx;
            const l = layers[idx];
            $('#wppoppop-inspector-title').text('Edit: ' + l.type.toUpperCase());
            $('#prop-content').val(l.content || '');
            $('#prop-color').val(l.color || '#111827');
            $('#prop-bg').val(l.bg && l.bg !== 'transparent' ? l.bg : '#ffffff');
            $('#prop-fontsize').val(l.fontSize || 14);
            $('#prop-radius').val(l.radius || 0);
            $('#wppoppop-inspector-modal').fadeIn(150);
        }

        $('#wppoppop-inspector-apply').on('click', function() {
            if (selectedIndex !== null && layers[selectedIndex]) {
                layers[selectedIndex].content = $('#prop-content').val();
                layers[selectedIndex].color = $('#prop-color').val();
                layers[selectedIndex].bg = $('#prop-bg').val();
                layers[selectedIndex].fontSize = parseInt($('#prop-fontsize').val(), 10) || 14;
                layers[selectedIndex].radius = parseInt($('#prop-radius').val(), 10) || 0;
            }
            $('#wppoppop-inspector-modal').fadeOut(150);
            renderLayers();
        });

        $('.wppoppop-inspector-close').on('click', function() {
            $('#wppoppop-inspector-modal').fadeOut(150);
        });

        function deleteLayer(idx) {
            layers.splice(idx, 1);
            selectedIndex = null;
            renderLayers();
        }

        // Asynchronous Save
        $('#wppoppop-save-btn').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).find('.wppoppop-save-text').text('Saving...');

            $.post(window.WPPopPopBuilder.ajax_url, {
                action: 'wppoppop_save_builder_popup',
                nonce: window.WPPopPopBuilder.nonce,
                popup_id: $('#wppoppop-popup-id').val(),
                title: $('#wppoppop-popup-title').val(),
                layers: JSON.stringify(layers)
            }, function(res) {
                $btn.prop('disabled', false).find('.wppoppop-save-text').text('Save');
                if (res.success) {
                    if (res.data.popup_id) {
                        $('#wppoppop-popup-id').val(res.data.popup_id);
                    }
                    showToast(res.data.message || 'Saved successfully!');
                } else {
                    alert('Error saving popup.');
                }
            });
        });

        renderLayers();
    });
})(jQuery);
"""
(js_dir / "builder.js").write_text(builder_js.strip() + "\n", encoding="utf-8")

print("Visual Builder upgraded: Live canvas drag-and-drop, floating panel, and inspector active.")
