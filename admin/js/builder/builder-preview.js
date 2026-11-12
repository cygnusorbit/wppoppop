/**
 * WpPopPop Builder: Live Preview (real frontend iframe) + "Preview on Homepage"
 * Cross-browser: Chrome, Safari, Firefox, Edge (no CSS inset, no optional chaining)
 */
(function (window, $) {
    'use strict';

    var MODAL = '#wppoppop-live-preview-modal';
    var FRAME = '#wppoppop-live-preview-frame';

    function ajaxUrl() {
        var v = window.wppoppop_vars || {};
        return v.ajax_url || (typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : '/wp-admin/admin-ajax.php');
    }
    function nonce() {
        var v = window.wppoppop_vars || {};
        return v.nonce || $('#wppoppop-builder-nonce').val() || '';
    }

    var Preview = {
        isOpen: false,
        frameUrl: '',
        wasPreview: false,

        getUid: function () {
            var io = window.WpPopPopBuilderIO;
            var uid = (io && typeof io.getUid === 'function') ? io.getUid() : '';
            return uid || 'draft';
        },

        flush: function () {
            try {
                var R = window.WpPopPopElementsRegistry, I = window.WpPopPopBuilderInspector, IO = window.WpPopPopBuilderIO;
                if (R && typeof R.flushElementFromDOM === 'function') R.flushElementFromDOM();
                if (I && typeof I.flushSanitizedLayerSettings === 'function') I.flushSanitizedLayerSettings();
                if (IO && typeof IO.flushActiveElement === 'function') IO.flushActiveElement();
            } catch (e) {}
            var core = window.WpPopPopBuilderCore;
            if (core && core.state) {
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta) core.state.canvasMeta = {};
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                var m = core.state.canvasMeta[cur];
                var a = $('#set-canvas-anim-appearance, #set-anim-appearance').val();
                if (a) m.anim_appearance = a;
                var d = $('#set-canvas-anim-duration, #set-anim-duration').val();
                if (d !== undefined && d !== '' && !isNaN(d)) m.anim_duration = parseInt(d, 10);
                var l = $('#set-canvas-anim-delay, #set-anim-delay').val();
                if (l !== undefined && l !== '' && !isNaN(l)) m.anim_delay = parseInt(l, 10);
                var o = $('#set-canvas-anim-disappearance, #set-anim-disappearance').val();
                if (o) m.anim_disappearance = o;
                var $exP = $('#set-canvas-anim-exit-enable');
                if ($exP.length) m.anim_exit_enabled = $exP.is(':checked');
                var $radP = $('#set-canvas-radius');
                if ($radP.length) m.canvas_radius = Math.max(0, Math.min(500, parseInt($radP.val(), 10) || 0));

                /* WPPOPPOP_BTN_GEOM_SYNC: button-family size, radius and font follow the visible stage node */
                var btnTypes = { submit: 1, button: 1, step_btn: 1, link_btn: 1, pay: 1 };
                var btnList = (core.state.canvases && core.state.canvases[cur]) || [];
                for (var bi = 0; bi < btnList.length; bi++) {
                    var bEl = btnList[bi];
                    if (!bEl || bEl.id === undefined || !btnTypes[bEl.type]) continue;
                    var bNode = document.getElementById('el-' + bEl.id) || document.getElementById('wppoppop-el-' + bEl.id);
                    if (!bNode || !bNode.offsetWidth || !bNode.offsetHeight) continue;
                    bEl.width = bNode.offsetWidth; bEl.w = bEl.width;
                    bEl.height = bNode.offsetHeight; bEl.h = bEl.height;
                    var bCs = window.getComputedStyle ? window.getComputedStyle(bNode) : null;
                    if (!bCs) continue;
                    var bRadTxt = bCs.borderTopLeftRadius || '';
                    if (bRadTxt && bRadTxt.indexOf('%') === -1 && !isNaN(parseFloat(bRadTxt))) { bEl.borderRadius = Math.round(parseFloat(bRadTxt)); }
                    var bFs = parseFloat(bCs.fontSize);
                    if (!isNaN(bFs) && bFs > 0) { bEl.fontSize = Math.round(bFs); }
                    var bFw = parseInt(bCs.fontWeight, 10);
                    if (!isNaN(bFw) && bFw > 0) { bEl.fontWeight = String(bFw); }
                }

                /* WPPOPPOP_PREVIEW_GEOM_SYNC: the stage DOM is the visual truth for the current canvas */
                var geoList = (core.state.canvases && core.state.canvases[cur]) || [];
                for (var gi = 0; gi < geoList.length; gi++) {
                    var gEl = geoList[gi];
                    if (!gEl || gEl.id === undefined) continue;
                    var gNode = document.getElementById('wppoppop-el-' + gEl.id);
                    if (!gNode || !gNode.offsetWidth || !gNode.offsetHeight) continue;
                    var gL = parseInt(gNode.style.left, 10), gT = parseInt(gNode.style.top, 10);
                    if (isNaN(gL)) gL = parseInt($(gNode).css('left'), 10);
                    if (isNaN(gT)) gT = parseInt($(gNode).css('top'), 10);
                    if (!isNaN(gL)) { gEl.left = gL; gEl.x = gL; }
                    if (!isNaN(gT)) { gEl.top = gT; gEl.y = gT; }
                }
            }
        },

        buildPayload: function () {
            this.flush();
            var core = window.WpPopPopBuilderCore;
            var st = (core && core.state) ? core.state : {};
            var settings = window.WpPopPopBuilderSettings && typeof window.WpPopPopBuilderSettings.getSettings === 'function'
                ? window.WpPopPopBuilderSettings.getSettings() : {};
            var canv = st.canvases || { 1: [] };
            var meta = st.canvasMeta || {};
            return {
                canvases: canv,
                screens: canv,
                canvasMeta: meta,
                canvas_meta: meta,
                settings: settings,
                title: $('#wppoppop-builder-title').val() || 'Untitled Popup'
            };
        },

        stage: function () {
            var payload = JSON.stringify(this.buildPayload());
            return $.ajax({
                url: ajaxUrl(),
                type: 'POST',
                dataType: 'json',
                data: { action: 'wppoppop_preview_stage', nonce: nonce(), uid: this.getUid(), data: payload }
            });
        },

        // ---------- Live preview modal (real frontend in iframe) ----------
        buildModal: function () {
            if ($(MODAL).length) return;
            var btn = 'background:#1e293b;border:1px solid #334155;color:#f8fafc;border-radius:0px;height:30px;padding:0 12px;font-size:12px;font-weight:600;cursor:pointer;';
            var html =
                '<div id="wppoppop-live-preview-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:1000000;background:rgba(2,6,23,.94);flex-direction:column;">' +
                    '<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:8px 14px;background:#0f172a;border-bottom:1px solid #1e293b;color:#f8fafc;font:600 12px/1.4 -apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif;">' +
                        '<span style="color:#10b981;">&#9679; LIVE PREVIEW (frontend, editing disabled)</span>' +
                        '<span style="display:flex;gap:6px;">' +
                            '<button type="button" data-wpp-dev="100%" style="' + btn + '">Desktop</button>' +
                            '<button type="button" data-wpp-dev="768px" style="' + btn + '">Tablet</button>' +
                            '<button type="button" data-wpp-dev="390px" style="' + btn + '">Mobile</button>' +
                            '<button type="button" data-wpp-act="reload" style="' + btn + '">Replay</button>' +
                            '<button type="button" data-wpp-act="exit" style="' + btn.replace('#1e293b', '#dc2626').replace('#334155', '#dc2626') + '">Exit Preview</button>' +
                        '</span>' +
                    '</div>' +
                    '<div style="flex:1;display:flex;justify-content:center;align-items:stretch;padding:12px;overflow:auto;">' +
                        '<iframe id="wppoppop-live-preview-frame" title="Live Preview" allow="autoplay; fullscreen; encrypted-media; picture-in-picture" style="width:100%;max-width:100%;height:100%;border:0;background:transparent;border-radius:0px;"></iframe>' +
                    '</div>' +
                '</div>';
            $('body').append(html);
        },

        enter: function (url) {
            this.buildModal();
            var C = window.WpPopPopBuilderCanvas;
            this.wasPreview = !!(C && C.isPreview);
            if (C) C.isPreview = true;                      // disables canvas selection/drag handlers
            $('body').addClass('wppoppop-preview-locked').css('overflow', 'hidden');
            try { if (window.WpPopPopBuilderInspector && window.WpPopPopBuilderInspector.close) window.WpPopPopBuilderInspector.close(true); } catch (e) {}
            try { if (window.WpPopPopBuilderSettings && window.WpPopPopBuilderSettings.closeDrawer) window.WpPopPopBuilderSettings.closeDrawer(); } catch (e) {}
            this.frameUrl = url;
            $(FRAME).css('width', '100%').attr('src', url);
            $(MODAL).css('display', 'flex');
            this.isOpen = true;
        },

        exit: function () {
            $(MODAL).hide();
            $(FRAME).attr('src', 'about:blank');
            var C = window.WpPopPopBuilderCanvas;
            if (C) C.isPreview = this.wasPreview;
            $('body').removeClass('wppoppop-preview-locked').css('overflow', '');
            this.isOpen = false;
        },

        setBusy: function (sel, busy, label) {
            var $b = $(sel);
            if (!$b.length) return;
            if (busy) { $b.data('wppOrig', $b.html()).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin" aria-hidden="true" title="' + label + '"></i>'); }
            else { $b.prop('disabled', false).html($b.data('wppOrig') || $b.html()); }
        },

        openLive: function () {
            var self = this;
            self.setBusy('#wppoppop-btn-preview', true, 'Loading...');
            self.stage().done(function (res) {
                if (res && res.success && res.data) { self.enter(res.data.frame_url); }
                else { window.alert((res && res.data && res.data.message) || wp.i18n.__('Preview failed.', 'wppoppop')); }
            }).fail(function () {
                window.alert(wp.i18n.__('Server error while preparing preview.', 'wppoppop'));
            }).always(function () { self.setBusy('#wppoppop-btn-preview', false); });
        },

        openHomepage: function () {
            var self = this;
            // Open synchronously (avoids popup blockers), then point at the staged URL.
            var win = null;
            try { win = window.open('', '_blank'); } catch (e) { win = null; }
            self.setBusy('#wppoppop-btn-preview-home', true, 'Loading...');
            self.stage().done(function (res) {
                if (res && res.success && res.data) {
                    if (win && !win.closed) { win.location.href = res.data.url; }
                    else { window.location.href = res.data.url; }   // popup blocked: redirect this tab
                } else {
                    if (win) win.close();
                    window.alert((res && res.data && res.data.message) || wp.i18n.__('Preview failed.', 'wppoppop'));
                }
            }).fail(function () {
                if (win) win.close();
                window.alert(wp.i18n.__('Server error while preparing preview.', 'wppoppop'));
            }).always(function () { self.setBusy('#wppoppop-btn-preview-home', false); });
        },

        ensureHomeButton: function () {
            if ($('#wppoppop-btn-preview-home').length) return;
            var $src = $('#wppoppop-btn-preview').first();
            if (!$src.length) return;
            var $b = $src.clone(false).attr({ id: 'wppoppop-btn-preview-home', title: 'Preview on Homepage', 'aria-label': 'Preview on Homepage' }).html('<i class="fa-solid fa-desktop" aria-hidden="true"></i>');
            $src.after($b);
        }
    };

    window.WpPopPopBuilderPreview = Preview;

    // Capture phase: wins over every other Preview click handler regardless of load order.
    document.addEventListener('click', function (e) {
        var t = e.target;
        if (!t || !t.closest) return;
        var b = t.closest('#wppoppop-btn-preview, #wppoppop-btn-preview-home');
        if (b) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (b.disabled) return;
            if (b.id === 'wppoppop-btn-preview-home') Preview.openHomepage(); else Preview.openLive();
            return;
        }
        var m = t.closest('#wppoppop-live-preview-modal [data-wpp-act], #wppoppop-live-preview-modal [data-wpp-dev]');
        if (m) {
            e.preventDefault();
            var act = m.getAttribute('data-wpp-act'), dev = m.getAttribute('data-wpp-dev');
            if (act === 'exit') Preview.exit();
            else if (act === 'reload') { $(FRAME).attr('src', Preview.frameUrl); }
            else if (dev) { $(FRAME).css('width', dev); }
        }
    }, true);

    // Lock the editor while previewing: only ESC is allowed.
    document.addEventListener('keydown', function (e) {
        if (!Preview.isOpen) return;
        if (e.key === 'Escape' || e.keyCode === 27) { e.preventDefault(); Preview.exit(); return; }
        e.stopImmediatePropagation();
    }, true);

    // Inject extra button + matching style (works with either header template)
    $(function () {
        if (!$('#wppoppop-preview-home-css').length) {
            $('head').append('<style id="wppoppop-preview-home-css">#wppoppop-btn-preview-home{display:inline-flex!important;align-items:center!important;justify-content:center!important;height:32px!important;padding:0 12px!important;background:#1e293b!important;border:1px solid #334155!important;border-radius:0px!important;color:#f8fafc!important;font-size:12px!important;font-weight:500!important;cursor:pointer!important;box-sizing:border-box!important;margin:0!important;white-space:nowrap;}#wppoppop-btn-preview-home:hover{background:#334155!important;color:#38bdf8!important;border-color:#38bdf8!important;}#wppoppop-btn-preview-home:disabled,#wppoppop-btn-preview:disabled{opacity:.6;cursor:wait!important;}</style>');
        }
        Preview.ensureHomeButton();
        setTimeout(function () { Preview.ensureHomeButton(); }, 500);
        setTimeout(function () { Preview.ensureHomeButton(); }, 1500);
    });
/* WPPOPPOP_PREVIEW_IN_STAGE_V1 */
    /* WPPOPPOP_PREVIEW_IN_STAGE_V1: render preview inside the checker workspace area */
    (function () {
        var P = window.WpPopPopBuilderPreview;
        var WS_SEL = '.wppoppop-builder-workspace, #wppoppop-canvas-workspace';

        function placeOverWorkspace() {
            var $c = $('#wppoppop-live-preview-modal');
            if (!$c.length) return;
            var $ws = $(WS_SEL).filter(':visible').first();
            var top, left, width, height;
            if ($ws.length) {
                var r = $ws[0].getBoundingClientRect();
                top = r.top; left = r.left; width = r.width; height = r.height;
            } else {
                var hb = $('.wppoppop-builder-header').first();
                top = hb.length ? hb[0].getBoundingClientRect().bottom : 60;
                left = 0; width = window.innerWidth; height = window.innerHeight - top;
            }
            $c.css({ position: 'fixed', top: top + 'px', left: left + 'px', width: width + 'px', height: height + 'px' });
        }

        P.buildModal = function () {
            if ($('#wppoppop-live-preview-modal').length) return;
            var btn = 'background:#1e293b;border:1px solid #334155;color:#f8fafc;border-radius:0px;height:26px;padding:0 10px;font-size:11px;font-weight:600;cursor:pointer;';
            var checker = 'background-color:#e2e8f0;background-image:linear-gradient(45deg,#cbd5e1 25%,transparent 25%,transparent 75%,#cbd5e1 75%),linear-gradient(45deg,#cbd5e1 25%,transparent 25%,transparent 75%,#cbd5e1 75%);background-size:20px 20px;background-position:0 0,10px 10px;';
            var html =
                '<div id="wppoppop-live-preview-modal" style="display:none;z-index:99980;flex-direction:column;' + checker + 'box-sizing:border-box;">' +
                    '<div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:5px 10px;background:#0f172a;border-bottom:1px solid #1e293b;color:#f8fafc;font:600 11px/1.4 -apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif;flex:0 0 auto;">' +
                        '<span style="color:#10b981;">&#9679; LIVE PREVIEW &mdash; editing disabled</span>' +
                        '<span style="display:flex;gap:6px;">' +
                            '<button type="button" data-wpp-dev="100%" style="' + btn + '">Desktop</button>' +
                            '<button type="button" data-wpp-dev="768px" style="' + btn + '">Tablet</button>' +
                            '<button type="button" data-wpp-dev="390px" style="' + btn + '">Mobile</button>' +
                            '<button type="button" data-wpp-act="reload" style="' + btn + '">Replay</button>' +
                            '<button type="button" data-wpp-act="exit" style="' + btn.replace('#1e293b', '#dc2626').replace('#334155', '#dc2626') + '">Exit Preview</button>' +
                        '</span>' +
                    '</div>' +
                    '<div style="flex:1 1 auto;min-height:0;display:flex;justify-content:center;align-items:stretch;overflow:auto;">' +
                        '<iframe id="wppoppop-live-preview-frame" title="Live Preview" allow="autoplay; fullscreen; encrypted-media; picture-in-picture" style="width:100%;max-width:100%;height:100%;border:0;background:transparent;"></iframe>' +
                    '</div>' +
                '</div>';
            $('body').append(html);
            if (!$('#wppoppop-preview-lock-css').length) {
                $('head').append('<style id="wppoppop-preview-lock-css">' +
                    'body.wppoppop-preview-locked .wppoppop-ribbon-bar,' +
                    'body.wppoppop-preview-locked .wppoppop-align-tools,' +
                    'body.wppoppop-preview-locked .wppoppop-history-tools,' +
                    'body.wppoppop-preview-locked .wppoppop-header-history-tools,' +
                    'body.wppoppop-preview-locked .wppoppop-viewport-switch,' +
                    'body.wppoppop-preview-locked .wppoppop-viewport-toggles,' +
                    'body.wppoppop-preview-locked .wppoppop-screens-nav,' +
                    'body.wppoppop-preview-locked .wppoppop-canvas-tab-wrapper,' +
                    'body.wppoppop-preview-locked #wppoppop-add-canvas-btn,' +
                    'body.wppoppop-preview-locked .wppoppop-quick-dimensions-wrap,' +
                    'body.wppoppop-preview-locked #wppoppop-zoom-select,' +
                    'body.wppoppop-preview-locked #wppoppop-btn-settings,' +
                    'body.wppoppop-preview-locked #wppoppop-btn-embed,' +
                    'body.wppoppop-preview-locked #wppoppop-btn-save{pointer-events:none!important;opacity:.45!important;}' +
                    'body.wppoppop-preview-locked #wppoppop-inspector-drawer,' +
                    'body.wppoppop-preview-locked #wppoppop-floating-layers-panel,' +
                    'body.wppoppop-preview-locked #wppoppop-settings-drawer{display:none!important;}' +
                    '</style>');
            }
            $(window).off('resize.wppPrev').on('resize.wppPrev', function () { if (P.isOpen) placeOverWorkspace(); });
        };

        P.enter = function (url) {
            this.buildModal();
            var C = window.WpPopPopBuilderCanvas;
            this.wasPreview = !!(C && C.isPreview);
            if (C) C.isPreview = true;
            $('body').addClass('wppoppop-preview-locked');
            try { if (window.WpPopPopBuilderInspector && window.WpPopPopBuilderInspector.close) window.WpPopPopBuilderInspector.close(true); } catch (e) {}
            try { if (window.WpPopPopBuilderSettings && window.WpPopPopBuilderSettings.closeDrawer) window.WpPopPopBuilderSettings.closeDrawer(); } catch (e) {}
            this.frameUrl = url;
            $('#wppoppop-live-preview-frame').css('width', '100%').attr('src', url);
            $('#wppoppop-live-preview-modal').css('display', 'flex');
            placeOverWorkspace();
            this.isOpen = true;
            $('#wppoppop-btn-preview').html('<i class="fa-solid fa-eye-slash" aria-hidden="true"></i>').attr({title:'Exit Preview','aria-label':'Exit Preview'}).addClass('active');
        };

        P.exit = function () {
            $('#wppoppop-live-preview-modal').hide();
            $('#wppoppop-live-preview-frame').attr('src', 'about:blank');
            var C = window.WpPopPopBuilderCanvas;
            if (C) C.isPreview = this.wasPreview;
            $('body').removeClass('wppoppop-preview-locked').css('overflow', '');
            this.isOpen = false;
            $('#wppoppop-btn-preview').html('<i class="fa-solid fa-eye" aria-hidden="true"></i>').attr({title:'Preview','aria-label':'Preview'}).removeClass('active');
        };

        // Preview button toggles: open -> Exit
        var origOpenLive = P.openLive;
        P.openLive = function () {
            if (this.isOpen) { this.exit(); return; }
            return origOpenLive.apply(this, arguments);
        };
    })();
})(window, jQuery);

/* === WPPOPPOP_SHAPE_INTERACTION_ENGINE_START === */
(function() {
    'use strict';

    // 1. Toggle-Slide Click Handler & Sync
    document.addEventListener('click', function(e) {
        var toggle = e.target.closest('.wppoppop-toggle-switch, .wppoppop-switch, .wppoppop-toggle-slide');
        if (!toggle) return;

        var checkbox = toggle.querySelector('input[type="checkbox"]');
        if (!checkbox) return;

        // If click was directly on the input, allow natural browser state change
        if (e.target !== checkbox) {
            checkbox.checked = !checkbox.checked;
            var changeEvt = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvt);
        }

        if (checkbox.checked) {
            toggle.classList.add('is-active', 'active');
        } else {
            toggle.classList.remove('is-active', 'active');
        }
    }, true);

    // 2. Shape Background Color Field Suppression on Layer Selection
    function updateShapeInspectorView() {
        var inspector = document.querySelector('.wppoppop-inspector, #wppoppop-inspector, .wppoppop-layer-settings');
        if (!inspector) return;

        var activeLayer = document.querySelector('.wppoppop-layer.is-selected, .wppoppop-layer.active, [data-layer-id].active');
        var layerType = activeLayer ? (activeLayer.getAttribute('data-type') || activeLayer.getAttribute('data-element-type') || '') : '';

        if (!layerType) {
            var typeInp = inspector.querySelector('input[name="type"], input[data-prop="type"]');
            if (typeInp) layerType = typeInp.value;
        }

        if (layerType === 'shape') {
            inspector.setAttribute('data-element', 'shape');
            inspector.setAttribute('data-element-type', 'shape');

            // Hide background color controls dynamically
            var bgControls = inspector.querySelectorAll(
                '.wppoppop-control-background, .wppoppop-control-bg-color, [data-prop="background"], [data-prop="background_color"], [data-prop="backgroundColor"]'
            );
            bgControls.forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
            });

            // Ensure Stroke default shows 0 if empty
            var strokeInp = inspector.querySelector('input[name="stroke_width"], input[data-prop="stroke_width"], input[data-prop="stroke"]');
            if (strokeInp && (strokeInp.value === '' || strokeInp.value === undefined)) {
                strokeInp.value = '0';
            }

            // Ensure Border & Corner Radius default show 0 if empty
            var borderInp = inspector.querySelector('input[name="border_width"], input[data-prop="border_width"], input[data-prop="borderRadius"]');
            if (borderInp && (borderInp.value === '' || borderInp.value === undefined)) {
                borderInp.value = '0';
            }
        } else {
            if (inspector.getAttribute('data-element') === 'shape') {
                inspector.removeAttribute('data-element');
                inspector.removeAttribute('data-element-type');
            }
        }
    }

    document.addEventListener('click', function() {
        setTimeout(updateShapeInspectorView, 50);
    });
    window.addEventListener('load', function() {
        setTimeout(updateShapeInspectorView, 100);
    });
})();
/* === WPPOPPOP_SHAPE_INTERACTION_ENGINE_END === */

/* === WPPOPPOP_SHAPE_DEFAULTS_ENFORCER_START === */
(function() {
    'use strict';

    var ZERO_PROPS = [
        'stroke', 'stroke_width', 'strokeWidth', 'stroke-width',
        'border_width', 'borderWidth', 'border-width',
        'border_radius', 'borderRadius', 'border-radius', 'corner_radius', 'cornerRadius', 'radius',
        'shadow_x', 'shadow_y', 'shadow_blur', 'shadow_spread',
        'shadowX', 'shadowY', 'shadowBlur', 'shadowSpread',
        'box_shadow_x', 'box_shadow_y', 'box_shadow_blur', 'box_shadow_spread'
    ];

    function sanitizeShapeModel(element) {
        if (!element) return;
        var type = element.type || element.element_type || element.elementType || '';
        if (type !== 'shape') return;

        // Force stroke to 0 if undefined, null, empty string, or non-explicit
        if (element.stroke_width === undefined || element.stroke_width === null || element.stroke_width === '' || element.stroke_width === 1 || element.stroke_width === '1' || element.stroke_width === '1px') {
            element.stroke_width = 0;
        }
        if (element.stroke === undefined || element.stroke === null || element.stroke === '' || element.stroke === 1 || element.stroke === '1') {
            element.stroke = 0;
        }

        // Force border and corner radius to 0
        if (element.border_width === undefined || element.border_width === null || element.border_width === '') {
            element.border_width = 0;
        }
        if (element.border_radius === undefined || element.border_radius === null || element.border_radius === '') {
            element.border_radius = 0;
        }
        if (element.borderRadius === undefined || element.borderRadius === null || element.borderRadius === '') {
            element.borderRadius = 0;
        }

        // Force all box shadow fields to 0
        ['shadow_x', 'shadow_y', 'shadow_blur', 'shadow_spread'].forEach(function(sp) {
            if (element[sp] === undefined || element[sp] === null || element[sp] === '') {
                element[sp] = 0;
            }
        });
    }

    function syncInspectorInputs() {
        var inspector = document.querySelector('.wppoppop-inspector, #wppoppop-inspector, .wppoppop-sidebar-settings, .wppoppop-layer-settings');
        if (!inspector) return;

        var activeLayer = document.querySelector('.wppoppop-layer.is-selected, .wppoppop-layer.active, [data-layer-id].active, .wppoppop-layer-item.active');
        var layerType = activeLayer ? (activeLayer.getAttribute('data-type') || activeLayer.getAttribute('data-element-type') || activeLayer.getAttribute('data-element') || '') : '';

        if (!layerType) {
            var typeInp = inspector.querySelector('input[name="type"], input[data-prop="type"], [data-setting="type"]');
            if (typeInp) layerType = typeInp.value;
        }

        if (layerType === 'shape') {
            inspector.setAttribute('data-element', 'shape');
            inspector.setAttribute('data-element-type', 'shape');

            // Hide background color controls
            var bgControls = inspector.querySelectorAll(
                '.wppoppop-control-background, .wppoppop-control-bg-color, [data-prop="background"], [data-prop="background_color"], [data-prop="backgroundColor"], .control-background-color'
            );
            bgControls.forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
            });

            // Enforce "0" on empty or uninitialized target fields in inspector DOM
            var inputs = inspector.querySelectorAll('input, select');
            inputs.forEach(function(inp) {
                var prop = (inp.getAttribute('data-prop') || inp.getAttribute('data-setting') || inp.getAttribute('data-key') || inp.name || inp.id || '').toLowerCase();
                for (var i = 0; i < ZERO_PROPS.length; i++) {
                    var zp = ZERO_PROPS[i].toLowerCase();
                    if (prop === zp || prop.endsWith('_' + zp) || prop.endsWith('-' + zp)) {
                        if (inp.value === '' || inp.value === undefined || inp.value === null || inp.value === '1' && (prop.indexOf('stroke') !== -1 || prop.indexOf('border') !== -1)) {
                            // If it defaulted to 1 or empty on a brand new shape, reset to 0
                            if (!activeLayer.getAttribute('data-has-custom-style')) {
                                inp.value = '0';
                                var evt = new Event('input', { bubbles: true });
                                inp.dispatchEvent(evt);
                            }
                        }
                        break;
                    }
                }
            });
        }
    }

    // Intercept builder element addition
    if (typeof window.WpPopPop !== 'undefined' && window.WpPopPop.registerElement) {
        var origReg = window.WpPopPop.registerElement;
        window.WpPopPop.registerElement = function(type, config) {
            if (type === 'shape' && config && config.defaults) {
                config.defaults.stroke = 0;
                config.defaults.stroke_width = 0;
                config.defaults.strokeWidth = 0;
                config.defaults.border_width = 0;
                config.defaults.border_radius = 0;
                config.defaults.borderRadius = 0;
                config.defaults.shadow_x = 0;
                config.defaults.shadow_y = 0;
                config.defaults.shadow_blur = 0;
                config.defaults.shadow_spread = 0;
            }
            return origReg.apply(this, arguments);
        };
    }

    // Bind DOM events
    document.addEventListener('click', function() {
        setTimeout(syncInspectorInputs, 40);
    }, true);

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(syncInspectorInputs, 80);
    });

    window.addEventListener('load', function() {
        setTimeout(syncInspectorInputs, 150);
    });
})();
/* === WPPOPPOP_SHAPE_DEFAULTS_ENFORCER_END === */

/* === WPPOPPOP_SHAPE_STROKE_ZERO_ENGINE_START === */
(function() {
    'use strict';

    function enforceShapeStrokeZero() {
        var inspector = document.querySelector('.wppoppop-inspector, #wppoppop-inspector, .wppoppop-layer-settings');
        if (!inspector) return;

        var activeLayer = document.querySelector('.wppoppop-layer.is-selected, .wppoppop-layer.active, [data-layer-id].active, .wppoppop-layer-item.active');
        var layerType = activeLayer ? (activeLayer.getAttribute('data-type') || activeLayer.getAttribute('data-element-type') || activeLayer.getAttribute('data-element') || '') : '';

        if (!layerType) {
            var typeInp = inspector.querySelector('input[name="type"], input[data-prop="type"], [data-setting="type"]');
            if (typeInp) layerType = typeInp.value;
        }

        if (layerType === 'shape') {
            // Find STROKE WIDTH (PX) inputs in inspector
            var strokeInputs = inspector.querySelectorAll(
                'input[name="stroke_width"], input[name="strokeWidth"], input[name="stroke"], ' +
                'input[data-prop="stroke_width"], input[data-prop="strokeWidth"], input[data-prop="stroke"], ' +
                'input[data-setting="stroke_width"], input[data-key="stroke_width"], #stroke_width'
            );

            strokeInputs.forEach(function(inp) {
                // If input is empty, undefined, or defaulted to 1 on initial load, lock it to 0
                if (inp.value === '' || inp.value === undefined || inp.value === null || (inp.value === '1' && !activeLayer.hasAttribute('data-stroke-user-set'))) {
                    inp.value = '0';
                    inp.setAttribute('value', '0');
                    var evt = new Event('input', { bubbles: true });
                    inp.dispatchEvent(evt);
                }
            });

            // Mark when user explicitly changes stroke
            strokeInputs.forEach(function(inp) {
                if (!inp._strokeTrackerAttached) {
                    inp._strokeTrackerAttached = true;
                    inp.addEventListener('change', function() {
                        if (activeLayer) activeLayer.setAttribute('data-stroke-user-set', 'true');
                    });
                }
            });
        }
    }

    // Intercept builder element registration to force default 0
    if (typeof window.WpPopPop !== 'undefined' && window.WpPopPop.registerElement) {
        var origRegister = window.WpPopPop.registerElement;
        window.WpPopPop.registerElement = function(type, config) {
            if (type === 'shape' && config && config.defaults) {
                config.defaults.stroke_width = 0;
                config.defaults.strokeWidth = 0;
                config.defaults.stroke = 0;
            }
            return origRegister.apply(this, arguments);
        };
    }

    document.addEventListener('click', function() {
        setTimeout(enforceShapeStrokeZero, 40);
    }, true);

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(enforceShapeStrokeZero, 80);
    });

    window.addEventListener('load', function() {
        setTimeout(enforceShapeStrokeZero, 120);
    });
})();
/* === WPPOPPOP_SHAPE_STROKE_ZERO_ENGINE_END === */
