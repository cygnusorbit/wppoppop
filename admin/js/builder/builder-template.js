/**
 * WpPopPop Builder: Save As Template
 * Inspector DOM -> JS model -> pre-save flush -> AJAX (server whitelist) -> Library
 * ES5 / cross-browser (Chrome, Safari, Firefox, Edge).
 */
(function (window, $) {
    'use strict';

    var T = {
        frame: null,

        vars: function () {
            return window.wppoppop_vars || { ajax_url: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), nonce: $('#wppoppop-builder-nonce').val() || '' };
        },

        flush: function () {
            try {
                if (window.WpPopPopElementsRegistry && typeof window.WpPopPopElementsRegistry.flushElementFromDOM === 'function') { window.WpPopPopElementsRegistry.flushElementFromDOM(); }
                var insp = window.WpPopPopBuilderInspector;
                if (insp && typeof insp.flushSanitizedLayerSettings === 'function') { insp.flushSanitizedLayerSettings(); }
                if (window.WpPopPopBuilderIO && typeof window.WpPopPopBuilderIO.flushActiveElement === 'function') { window.WpPopPopBuilderIO.flushActiveElement(); }
            } catch (e) {}
        },

        collect: function () {
            this.flush();
            var core = window.WpPopPopBuilderCore;
            var st = (core && core.state) ? core.state : {};
            var settings = window.WpPopPopBuilderSettings ? window.WpPopPopBuilderSettings.getSettings() : {};
            var canv = st.canvases || { 1: [] };
            var meta = st.canvasMeta || {};
            /* WPPOPPOP_TPL_ANIM_SYNC: same live-field sync the Save button performs */
            var cur = st.currentCanvas || 1;
            if (!st.canvasMeta) { st.canvasMeta = {}; }
            if (!st.canvasMeta[cur]) { st.canvasMeta[cur] = {}; }
            meta = st.canvasMeta;
            var cm = meta[cur];
            var vApp = $('#set-canvas-anim-appearance, #set-anim-appearance').val();
            if (vApp) { cm.anim_appearance = vApp; }
            var vDur = $('#set-canvas-anim-duration, #set-anim-duration').val();
            if (vDur !== undefined && vDur !== '' && !isNaN(vDur)) { cm.anim_duration = parseInt(vDur, 10); }
            var vDel = $('#set-canvas-anim-delay, #set-anim-delay').val();
            if (vDel !== undefined && vDel !== '' && !isNaN(vDel)) { cm.anim_delay = parseInt(vDel, 10); }
            var vDis = $('#set-canvas-anim-disappearance, #set-anim-disappearance').val();
            if (vDis) { cm.anim_disappearance = vDis; }
            var $exT = $('#set-canvas-anim-exit-enable'); if ($exT.length) { cm.anim_exit_enabled = $exT.is(':checked'); }
            var $radT = $('#set-canvas-radius'); if ($radT.length) { cm.canvas_radius = Math.max(0, Math.min(500, parseInt($radT.val(), 10) || 0)); }
            var $dcT = $('#set-canvas-close-btn'); if ($dcT.length) { cm.close_btn_enabled = $dcT.is(':checked'); } /* WPPOPPOP_DEFAULT_CLOSE */
            var $acT = $('#set-canvas-autoclose'); if ($acT.length) { var acT = Math.max(0, Math.min(300, parseInt($acT.val(), 10) || 0)); cm.autoclose = acT; cm.autoclose_delay = acT; } /* WPPOPPOP_AUTOCLOSE_FLUSH */
            return {
                title: $('#wppoppop-builder-title').val() || 'Untitled Popup',
                canvases: canv,
                screens: canv,
                canvasMeta: meta,
                canvas_meta: meta,
                settings: settings
            };
        },

        stats: function (payload) {
            var counts = {}, total = 0, k, i, list, t;
            for (k in payload.canvases) {
                if (!Object.prototype.hasOwnProperty.call(payload.canvases, k)) { continue; }
                list = payload.canvases[k] || [];
                for (i = 0; i < list.length; i++) {
                    t = (list[i] && list[i].type) ? String(list[i].type) : 'element';
                    counts[t] = (counts[t] || 0) + 1;
                    total++;
                }
            }
            var cur = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.currentCanvas) || 1;
            var m = payload.canvasMeta[cur] || payload.canvasMeta[1] || {};
            return { counts: counts, total: total, width: parseInt(m.width, 10) || 640, height: parseInt(m.height, 10) || 400 };
        },

        esc: function (s) { return $('<div>').text(String(s)).html(); },

        open: function () {
            var payload = this.collect();
            var s = this.stats(payload), html = '', t;
            for (t in s.counts) {
                if (Object.prototype.hasOwnProperty.call(s.counts, t)) {
                    html += '<div>' + this.esc(t) + ' x ' + s.counts[t] + '</div>';
                }
            }
            $('#wppoppop-tpl-elements').html(html || '<em>No elements</em>');
            $('#wppoppop-tpl-total').text('(' + s.total + ')');
            $('#wppoppop-tpl-size').text(s.width + ' \u00d7 ' + s.height + ' px');
            $('#wppoppop-tpl-name').val(payload.title);
            $('#wppoppop-tpl-desc').val('');
            this.setImage('');
            if ($('#wppoppop-tpl-category option[value="custom"]').length) { $('#wppoppop-tpl-category').val('custom'); }
            $('#wppoppop-tpl-msg').hide().text('');
            $('#wppoppop-tpl-submit').prop('disabled', false);
            $('#wppoppop-modal-save-template').css('display', 'flex');
            $('#wppoppop-tpl-name').focus();
        },

        close: function () { $('#wppoppop-modal-save-template').hide(); },

        setImage: function (url) {
            $('#wppoppop-tpl-image').val(url || '');
            $('#wppoppop-tpl-prev').toggleClass('has-img', !!url);
            $('#wppoppop-tpl-prev').css('background-image', url ? 'url("' + url.replace(/"/g, '%22') + '")' : 'none');
        },

        pickImage: function () {
            var self = this;
            if (!window.wp || !window.wp.media) { var u = window.prompt(wp.i18n.__('Image URL', 'wppoppop')); if (u) { self.setImage(u); } return; }
            if (!this.frame) {
                this.frame = window.wp.media({ title: 'Popup Image', button: { text: 'Use image' }, library: { type: 'image' }, multiple: false });
                this.frame.on('select', function () {
                    var a = self.frame.state().get('selection').first().toJSON();
                    self.setImage(a.url || '');
                });
            }
            this.frame.open();
        },

        msg: function (text, ok) {
            $('#wppoppop-tpl-msg').css('color', ok ? '#008a20' : '#b32d2e').text(text).show();
        },

        submit: function () {
            var self = this, v = this.vars();
            var name = $.trim($('#wppoppop-tpl-name').val());
            if (!name) { this.msg('Please enter a popup name.', false); return; }
            var $b = $('#wppoppop-tpl-submit').prop('disabled', true);
            $.ajax({
                url: v.ajax_url, type: 'POST', dataType: 'json',
                data: {
                    action: 'wppoppop_template_save',
                    nonce: v.nonce,
                    name: name,
                    description: $('#wppoppop-tpl-desc').val(),
                    image: $('#wppoppop-tpl-image').val(),
                    category: $('#wppoppop-tpl-category').val() || 'custom',
                    data: JSON.stringify(this.collect())
                },
                success: function (res) {
                    if (res && res.success) {
                        self.msg('Saved to Popups Library.', true);
                        setTimeout(function () { self.close(); }, 900);
                    } else {
                        self.msg((res && res.data && res.data.message) || 'Save failed.', false);
                        $b.prop('disabled', false);
                    }
                },
                error: function () { self.msg('Server error while saving template.', false); $b.prop('disabled', false); }
            });
        },

        init: function () {
            var self = this;
            $(document)
                .off('click.wppoppopTpl')
                .on('click.wppoppopTpl', '#wppoppop-btn-save-template', function (e) { e.preventDefault(); self.open(); })
                .on('click.wppoppopTpl', '#wppoppop-tpl-close, #wppoppop-tpl-cancel', function (e) { e.preventDefault(); self.close(); })
                .on('click.wppoppopTpl', '#wppoppop-tpl-upload, #wppoppop-tpl-prev', function (e) { e.preventDefault(); self.pickImage(); })
                .on('click.wppoppopTpl', '#wppoppop-tpl-remove-img', function (e) { e.preventDefault(); self.setImage(''); })
                .on('click.wppoppopTpl', '#wppoppop-tpl-submit', function (e) { e.preventDefault(); self.submit(); })
                .on('click.wppoppopTpl', '#wppoppop-modal-save-template', function (e) { if (e.target === this) { self.close(); } })
                .on('keydown.wppoppopTpl', function (e) { if (e.keyCode === 27) { self.close(); } });
        }
    };

    window.WpPopPopBuilderTemplate = T;
    $(function () { T.init(); });
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
