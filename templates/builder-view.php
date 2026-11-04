<?php
if (!defined('ABSPATH')) {
    exit;
}

$current_uid = isset($_GET['uid']) ? sanitize_text_field(wp_unslash($_GET['uid'])) : ($popup ? $popup->uid : '');
?>
<style id="wppoppop-fouc-guard">.wppoppop-builder-wrap{opacity:0 !important;}</style>
<style id="wppoppop-boot-style">
#wppoppop-boot-loader{position:fixed;top:32px;left:160px;right:0;bottom:0;z-index:99990;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;background:#0f172a;opacity:1;-webkit-transition:opacity .25s ease;transition:opacity .25s ease;}
body.folded #wppoppop-boot-loader{left:36px;}
@media (max-width:960px){#wppoppop-boot-loader{left:36px;}}
@media (max-width:782px){#wppoppop-boot-loader{top:46px;left:0;}}
#wppoppop-boot-loader.is-done{opacity:0;pointer-events:none;}
#wppoppop-boot-loader .wppoppop-boot-spin{width:38px;height:38px;border-radius:50%;border:3px solid rgba(255,255,255,.14);border-top-color:#38bdf8;-webkit-box-shadow:0 0 24px rgba(56,189,248,.25);box-shadow:0 0 24px rgba(56,189,248,.25);-webkit-animation:wppoppopBootSpin .8s linear infinite;animation:wppoppopBootSpin .8s linear infinite;}
@-webkit-keyframes wppoppopBootSpin{to{-webkit-transform:rotate(360deg);transform:rotate(360deg);}}
@keyframes wppoppopBootSpin{to{-webkit-transform:rotate(360deg);transform:rotate(360deg);}}
</style>
<div id="wppoppop-boot-loader" role="status" aria-live="polite" aria-label="<?php esc_attr_e('Loading builder', 'wppoppop'); ?>"><span class="wppoppop-boot-spin"></span></div>
<div class="wppoppop-builder-wrap">
    <!-- Hidden Security Nonce & Popup Identifier State -->
    <?php wp_nonce_field('wppoppop_builder_nonce', 'wppoppop-builder-nonce'); ?>
    <input type="hidden" id="wppoppop-builder-uid" value="<?php echo esc_attr($current_uid); ?>">
    <input type="hidden" id="wppoppop-builder-status" value="<?php echo esc_attr(($popup && isset($popup->status)) ? $popup->status : ''); ?>">

    <!-- 1. Top Application Toolbar (Canvas Sequence & Navigation) -->
    <?php include WPPOPPOP_PATH . 'templates/builder/header.php'; ?>

    <!-- 2. Horizontal Elements Ribbon Toolbar (19 Elements) -->
    <?php include WPPOPPOP_PATH . 'templates/builder/ribbon.php'; ?>

    <!-- Main Workspace Frame (Supports Pushing-Frame Inspector Docking) -->
    <div class="wppoppop-main-frame" style="display:flex;flex:1;overflow:hidden;position:relative;">
        <!-- 3. Visual Stage, Layers Panel & Canvas Workspace -->
        <?php include WPPOPPOP_PATH . 'templates/builder/canvas.php'; ?>

        <!-- 4. Pushing Frame Layer Inspector Drawer -->
        <?php include WPPOPPOP_PATH . 'templates/builder/drawer-inspector.php'; ?>

        <!-- 5. Popup Settings Drawer (slides in within the checker area, below top bar & ribbon) -->
        <?php include WPPOPPOP_PATH . 'templates/builder/drawer-settings.php'; ?>
    </div>

    <!-- 6. Embed Codes & Live Sandbox Preview Modals -->
    <?php include WPPOPPOP_PATH . 'templates/builder/modals.php'; ?>

    <script>
    /* WPPOPPOP_FOUC_GUARD: reveal once stylesheets and icon fonts have loaded (hard cap 3s) */
    (function () {
        var done = false;
        function reveal() {
            if (done) { return; }
            done = true;
            var s = document.getElementById('wppoppop-fouc-guard');
            if (s && s.parentNode) { s.parentNode.removeChild(s); }
            var l = document.getElementById('wppoppop-boot-loader');
            if (l) {
                l.className += ' is-done';
                setTimeout(function () {
                    var st = document.getElementById('wppoppop-boot-style');
                    if (l.parentNode) { l.parentNode.removeChild(l); }
                    if (st && st.parentNode) { st.parentNode.removeChild(st); }
                }, 300);
            }
        }
        function go() {
            if (window.requestAnimationFrame) {
                window.requestAnimationFrame(function () { window.requestAnimationFrame(reveal); });
            } else {
                setTimeout(reveal, 50);
            }
        }
        function ready() {
            if (document.fonts && document.fonts.ready && typeof document.fonts.ready.then === 'function') {
                document.fonts.ready.then(go, go);
            } else {
                go();
            }
        }
        if (document.readyState === 'complete') { ready(); } else { window.addEventListener('load', ready); }
        setTimeout(reveal, 3000);
    })();
    </script>
</div>
