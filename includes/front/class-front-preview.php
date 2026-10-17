<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Front Preview: stages unsaved builder state and renders it on the real frontend
 * (admins only). Used by the builder "Preview" iframe and "Preview on Homepage" button.
 */
class WpPopPop_Front_Preview {
    protected $renderer;
    protected $uid = '';
    protected $config = null;

    public function __construct(WpPopPop_Front_Renderer $renderer) {
        $this->renderer = $renderer;
        add_action('wp_ajax_wppoppop_preview_stage', [$this, 'ajax_stage']);
        add_action('wp', [$this, 'maybe_boot'], 1);
        add_action('template_redirect', [$this, 'maybe_bare_template'], 1); // WPPOPPOP_PREVIEW_BARE_V1
    }

    protected function transient_key($uid) {
        return 'wppoppop_prev_' . get_current_user_id() . '_' . md5($uid);
    }

    public function ajax_stage() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_builder_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.']);
        }

        $uid = isset($_POST['uid']) ? sanitize_key(wp_unslash($_POST['uid'])) : '';
        if ($uid === '') {
            $uid = 'draft';
        }
        $raw     = isset($_POST['data']) ? wp_unslash($_POST['data']) : '';
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            wp_send_json_error(['message' => 'Malformed preview payload.']);
        }

        set_transient($this->transient_key($uid), wp_json_encode($decoded), HOUR_IN_SECONDS);

        $url = add_query_arg([
            'wppoppop_preview' => $uid,
            'wppoppop_draft'   => 1,
            'wppoppop_t'       => time(),
        ], home_url('/'));

        wp_send_json_success([
            'uid'       => $uid,
            'url'       => $url,
            'frame_url' => add_query_arg('wppoppop_frame', 1, $url),
        ]);
    }

    public function maybe_boot() {
        if (is_admin() || empty($_GET['wppoppop_preview'])) {
            return;
        }
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return;
        }

        $uid    = sanitize_key(wp_unslash($_GET['wppoppop_preview']));
        $config = null;

        if (!empty($_GET['wppoppop_draft'])) {
            $raw = get_transient($this->transient_key($uid));
            if (is_string($raw) && $raw !== '') {
                $config = json_decode($raw, true);
            }
        }
        if (!is_array($config)) {
            global $wpdb;
            $table = $wpdb->prefix . 'wppoppop_items';
            $row   = $wpdb->get_row($wpdb->prepare("SELECT data FROM {$table} WHERE uid = %s", $uid), ARRAY_A);
            if ($row && !empty($row['data'])) {
                $config = json_decode($row['data'], true);
            }
        }
        if (!is_array($config)) {
            return;
        }

        $this->uid    = $uid;
        $this->config = $config;

        if (!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
        nocache_headers();
        if (!empty($_GET['wppoppop_frame'])) {
            show_admin_bar(false);
            add_filter('show_admin_bar', '__return_false');
        }

        // Preview shows ONLY the previewed popup.
        add_filter('wppoppop_suppress_front_popups', '__return_true');
        add_action('wp_footer', [$this, 'render'], 60);
    }

    /**
     * Frame mode: replace the theme template with a bare document so only the popup loads.
     */
    public function maybe_bare_template() {
        if (!$this->uid || !is_array($this->config) || empty($_GET['wppoppop_frame'])) {
            return;
        }
        status_header(200);
        header('Content-Type: text/html; charset=' . get_option('blog_charset'));
        ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Popup Preview</title>
<style>html,body{margin:0;padding:0;min-height:100%;background:#f1f5f9;}</style>
<?php wp_head(); ?>
</head>
<body class="wppoppop-preview-frame">
<?php wp_footer(); ?>
</body>
</html>
        <?php
        exit;
    }

    public function render() {
        if (!$this->uid || !is_array($this->config)) {
            return;
        }
        $uid = $this->uid;

        try {
            ob_start();
            echo $this->renderer->render_popup_markup($uid, $this->config, false, false);
            echo ob_get_clean();
        } catch (\Throwable $e) {
            if (ob_get_level() > 0) { ob_end_clean(); }
            echo '<div style="position:fixed;top:12px;left:50%;transform:translateX(-50%);z-index:1000002;background:#7f1d1d;color:#fff;padding:8px 14px;border-radius:8px;font:600 12px sans-serif;">Preview render error: ' . esc_html($e->getMessage()) . '</div>';
        }

        $back = admin_url('admin.php?page=wppoppop-builder' . ($uid !== 'draft' ? '&uid=' . rawurlencode($uid) : ''));
        ?>
        <div id="wppoppop-preview-banner" style="position:fixed;left:12px;bottom:12px;z-index:1000001;background:#0f172a;color:#f8fafc;border:1px solid #334155;border-radius:20px;padding:6px 14px;font:600 12px/1.4 -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;display:flex;gap:10px;align-items:center;box-shadow:0 8px 20px rgba(0,0,0,.35);">
            <span style="color:#10b981;">&#9679; PREVIEW MODE</span>
            <a href="<?php echo esc_url($back); ?>" style="color:#38bdf8;text-decoration:none;">Back to Builder</a>
            <a href="#" id="wppoppop-preview-replay" style="color:#e2e8f0;text-decoration:none;">Replay</a>
        </div>
        <script>
        (function () {
            var uid = <?php echo wp_json_encode($uid); ?>;
            try { if (window.self !== window.top) { var b = document.getElementById('wppoppop-preview-banner'); if (b) b.style.display = 'none'; } } catch (e) {}
            try { localStorage.removeItem('wppoppop_dismissed_' + uid); } catch (e) {}
            function show(force) {
                var D = window.WpPopPopFrontDisplay, $ = window.jQuery;
                if (!D || !$) { return setTimeout(function () { show(force); }, 150); }
                var $w = $('#wppoppop-popup-' + uid);
                if (force || !$w.is(':visible')) {
                    try { localStorage.removeItem('wppoppop_dismissed_' + uid); } catch (e) {}
                    $w.data('wppoppopClosing', false);
                    D.show(uid);
                }
            }
            if (document.readyState === 'complete') { setTimeout(function () { show(false); }, 350); }
            else { window.addEventListener('load', function () { setTimeout(function () { show(false); }, 350); }); }
            document.addEventListener('click', function (e) {
                var t = e.target && e.target.closest ? e.target.closest('#wppoppop-preview-replay') : null;
                if (t) { e.preventDefault(); show(true); }
            });
        })();
        </script>
        <?php
    }
}
