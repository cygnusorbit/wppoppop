/**
 * WpPopPop Visual Builder: I/O Persistence Engine
 * Handles Save/Load Routines, Per-Canvas Metadata (Name, Size, Color, Animation, Logic) & URL Syncing
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderIO = {
        flushActiveElement: function() {
            if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.flushActiveElement === 'function') {
                try { window.WpPopPopBuilderInspector.flushActiveElement(); } catch(e) {}
            }
            if (typeof this.flushActiveInputs === 'function') {
                try { this.flushActiveInputs(); } catch(e) {}
            }
            if (window.jQuery) {
                window.jQuery('input:focus, textarea:focus, select:focus').trigger('change').trigger('blur');
            }
        },

        exportConfig: function() {
            if (window.WpPopPopElementsRegistry && typeof window.WpPopPopElementsRegistry.flushElementFromDOM === 'function') {
                window.WpPopPopElementsRegistry.flushElementFromDOM();
            }
            if (window.WpPopPopBuilderInspector) {
                if (typeof window.WpPopPopBuilderInspector.flushSanitizedLayerSettings === "function") {
                    window.WpPopPopBuilderInspector.flushSanitizedLayerSettings();
                }
                if (typeof window.WpPopPopBuilderInspector.flushActiveElement === "function") {
                    window.WpPopPopBuilderInspector.flushActiveElement();
                }
            }
            if (typeof this.flushActiveElement === 'function') {
                this.flushActiveElement();
            }
            if (typeof this.exportPopup === 'function') {
                return this.exportPopup();
            }
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state) return {};
            return {
                id: core.state.id || (window.jQuery ? window.jQuery('#wppoppop-builder-uid').val() : 0) || 0,
                title: core.state.title || (window.jQuery ? window.jQuery('#wppoppop-builder-title').val() : '') || 'Untitled Popup',
                canvases: JSON.parse(JSON.stringify(core.state.canvases || { 1: [] })),
                canvasMeta: JSON.parse(JSON.stringify(core.state.canvasMeta || {})),
                settings: JSON.parse(JSON.stringify(core.state.settings || {}))
            };
        },

        init: function() {
            this.bindSave();
            this.loadInitialData();
        },

        getUid: function() {
            var urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('uid') || $('#wppoppop-builder-uid').val() || (window.wppoppop_vars ? window.wppoppop_vars.current_uid : '') || '';
        },

        syncUid: function(uid) {
            if (!uid) return;
            if ($('#wppoppop-builder-uid').length === 0) {
                $('<input>').attr({ type: 'hidden', id: 'wppoppop-builder-uid', value: uid }).appendTo('body');
            } else {
                $('#wppoppop-builder-uid').val(uid);
            }

            if (window.wppoppop_vars) {
                window.wppoppop_vars.current_uid = uid;
            }

            var currentUrl = new URL(window.location.href);
            if (currentUrl.searchParams.get('uid') !== uid) {
                currentUrl.searchParams.set('uid', uid);
                window.history.replaceState({ path: currentUrl.toString() }, '', currentUrl.toString());
            }
        },

        bindSave: function() {
            var self = this;
            $('#wppoppop-btn-save').off('click.wppoppop_save').on('click.wppoppop_save', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var origText = $btn.html();

                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Saving...');

                var core = window.WpPopPopBuilderCore;
                try {
                    if (window.WpPopPopElementsRegistry && typeof window.WpPopPopElementsRegistry.flushElementFromDOM === 'function') { window.WpPopPopElementsRegistry.flushElementFromDOM(); }
                    if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.flushSanitizedLayerSettings === 'function') { window.WpPopPopBuilderInspector.flushSanitizedLayerSettings(); }
                    self.flushActiveElement();
                } catch (flushErr) {}
                var settings = window.WpPopPopBuilderSettings ? window.WpPopPopBuilderSettings.getSettings() : {};

                
        // Pre-export active canvas animation synchronization
        if (core && core.state) {
            var curCanvas = core.state.currentCanvas || 1;
            if (!core.state.canvasMeta) core.state.canvasMeta = {};
            if (!core.state.canvasMeta[curCanvas]) core.state.canvasMeta[curCanvas] = {};
            var curCanvasMeta = core.state.canvasMeta[curCanvas];

            var liveAnimApp = $('#set-canvas-anim-appearance, #set-anim-appearance').val();
            if (liveAnimApp) curCanvasMeta.anim_appearance = liveAnimApp;

            var liveAnimDur = $('#set-canvas-anim-duration, #set-anim-duration').val();
            if (liveAnimDur !== undefined && liveAnimDur !== '' && !isNaN(liveAnimDur)) {
                curCanvasMeta.anim_duration = parseInt(liveAnimDur, 10);
            }

            var liveAnimDel = $('#set-canvas-anim-delay, #set-anim-delay').val();
            if (liveAnimDel !== undefined && liveAnimDel !== '' && !isNaN(liveAnimDel)) {
                curCanvasMeta.anim_delay = parseInt(liveAnimDel, 10);
            }

            var liveAnimDis = $('#set-canvas-anim-disappearance, #set-anim-disappearance').val();
            if (liveAnimDis) curCanvasMeta.anim_disappearance = liveAnimDis;
            var $liveExit = $('#set-canvas-anim-exit-enable');
            if ($liveExit.length) { curCanvasMeta.anim_exit_enabled = $liveExit.is(':checked'); }

            var $liveRadius = $('#set-canvas-radius');
            if ($liveRadius.length) { curCanvasMeta.canvas_radius = Math.max(0, Math.min(500, parseInt($liveRadius.val(), 10) || 0)); }

            /* WPPOPPOP_DEFAULT_CLOSE: pre-save flush */
            var $dcBtn = $('#set-canvas-close-btn');
            if ($dcBtn.length) { curCanvasMeta.close_btn_enabled = $dcBtn.is(':checked'); }
            /* WPPOPPOP_AUTOCLOSE_FLUSH: pre-save flush of the auto-close countdown */
            var $acBtn = $('#set-canvas-autoclose');
            if ($acBtn.length) {
                var acFlush = Math.max(0, Math.min(300, parseInt($acBtn.val(), 10) || 0));
                curCanvasMeta.autoclose = acFlush;
                curCanvasMeta.autoclose_delay = acFlush;
            }
        }


            var payload = {
                    canvases: (core && core.state && core.state.canvases) ? core.state.canvases : { 1: [], 2: [] },
                    screens: (core && core.state && core.state.canvases) ? core.state.canvases : { 1: [], 2: [] },
                    canvasMeta: (core && core.state && core.state.canvasMeta) ? core.state.canvasMeta : {},
                    canvas_meta: (core && core.state && core.state.canvasMeta) ? core.state.canvasMeta : {},
                    settings: settings,
                    title: $('#wppoppop-builder-title').val() || 'Untitled Popup'
                };

                var serialized = JSON.stringify(payload);
                var postData = {
                    action: 'wppoppop_save_popup',
                    nonce: (window.wppoppop_vars ? window.wppoppop_vars.nonce : '') || $('#wppoppop-builder-nonce').val(),
                    uid: self.getUid(),
                    title: payload.title,
                    data: serialized,
                    config: serialized
                };

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: postData,
                    success: function(response) {
                        if (response && response.success && response.data) {
                            var savedUid = response.data.uid;
                            self.syncUid(savedUid);
                            if (response.data.status) {
                                if ($('#wppoppop-builder-status').length === 0) {
                                    $('<input>').attr({ type: 'hidden', id: 'wppoppop-builder-status' }).appendTo('body');
                                }
                                $('#wppoppop-builder-status').val(response.data.status);
                            }
                            try {
                                var wppM = window.WpPopPopBuilderModals;
                                if (wppM && $('#wppoppop-modal-embed').is(':visible')) { wppM.refresh(); }
                            } catch (wppE) {}
                            $btn.html('<span class="dashicons dashicons-yes"></span> Saved!');
                            setTimeout(function() {
                                $btn.prop('disabled', false).html(origText);
                            }, 1500);
                        } else {
                            var msg = (response && response.data && response.data.message) ? response.data.message : 'Save failed.';
                            alert(msg);
                            $btn.prop('disabled', false).html(origText);
                        }
                    },
                    error: function() {
                        alert(wp.i18n.__('Server error while saving popup.', 'wppoppop'));
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });
        },

        loadInitialData: function() {
            var core = window.WpPopPopBuilderCore;
            var uid = this.getUid();
            if (!uid) {
                if (core) {
                    core.state.canvases = { 1: [], 2: [] };
                    core.state.canvasMeta = {
                        1: { name: 'Canvas 1', width: 300, height: 400, bg_mode: 'solid', bg_color: '#ffffff', grad_color1: '#3b82f6', grad_color2: '#1d4ed8', grad_angle: 135, anim_appearance: 'fadeIn', anim_duration: 1000, anim_delay: 0, anim_disappearance: 'none', anim_exit_enabled: false, logic_enabled: false },
                        2: { name: 'Confirmation', width: 300, height: 400, bg_mode: 'solid', bg_color: '#ffffff', grad_color1: '#3b82f6', grad_color2: '#1d4ed8', grad_angle: 135, anim_appearance: 'fadeIn', anim_duration: 1000, anim_delay: 0, anim_disappearance: 'none', anim_exit_enabled: false, logic_enabled: false }
                    };
                    if (window.WpPopPopBuilderDefaults) { window.WpPopPopBuilderDefaults.applyMeta(core.state.canvasMeta); }
                    core.renderCanvasTabs();
                    core.switchCanvas(1);
                    if (window.WpPopPopBuilderDefaults) { window.WpPopPopBuilderDefaults.applySettings(); }
                    if (window.WpPopPopBuilderSettings) {
                        window.WpPopPopBuilderSettings.renderCanvasBullets();
                    }
                }
                return;
            }

            var self = this;
            var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
            var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : $('#wppoppop-builder-nonce').val();

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'wppoppop_load_popup',
                    nonce: nonce,
                    uid: uid
                },
                success: function(response) {
                    if (response && response.success && response.data) {
                        var row = response.data;
                        var rawData = row.data || row.config || '{}';
                        var cfg = {};

                        if (typeof rawData === 'string') {
                            try { cfg = JSON.parse(rawData); } catch(e) { cfg = {}; }
                        } else if (typeof rawData === 'object' && rawData !== null) {
                            cfg = rawData;
                        }

                        if (core) {
                            var sourceCanvases = cfg.canvases || cfg.screens;
                            if (sourceCanvases && typeof sourceCanvases === 'object') {
                                core.state.canvases = sourceCanvases;
                            } else {
                                core.state.canvases = { 1: [], 2: [] };
                            }

                            core.state.canvasMeta = cfg.canvasMeta || cfg.canvas_meta || {};
                            /* WPPOPPOP_CANVAS2_NAME: previous default name on saved popups becomes the new default */
                            if (core.state.canvasMeta[2] && core.state.canvasMeta[2].name === 'Canvas 2') { core.state.canvasMeta[2].name = 'Confirmation'; }

                            if (!core.state.canvases[1]) core.state.canvases[1] = [];
                            if (!core.state.canvases[2]) core.state.canvases[2] = [];

                            Object.keys(core.state.canvases).forEach(function(k) {
                                if (!core.state.canvasMeta[k]) {
                                    core.state.canvasMeta[k] = {
                                        name: (String(k) === '2' ? 'Confirmation' : 'Canvas ' + k),
                                        width: (cfg.settings && cfg.settings.box && cfg.settings.box.width) || 300,
                                        height: (cfg.settings && cfg.settings.box && cfg.settings.box.height) || 400,
                                        bg_mode: (cfg.settings && cfg.settings.box && cfg.settings.box.bg_mode) || 'solid',
                                        bg_color: (cfg.settings && cfg.settings.box && cfg.settings.box.bg_color) || '#ffffff',
                                        grad_color1: (cfg.settings && cfg.settings.box && cfg.settings.box.grad_color1) || '#3b82f6',
                                        grad_color2: (cfg.settings && cfg.settings.box && cfg.settings.box.grad_color2) || '#1d4ed8',
                                        grad_angle: (cfg.settings && cfg.settings.box && cfg.settings.box.grad_angle) || 135,
                                        anim_appearance: 'fadeIn',
                                        anim_duration: 1000,
                                        anim_delay: 0,
                                        anim_disappearance: 'none', anim_exit_enabled: false,
                                        logic_enabled: false
                                    };
                                } else {
                                    if (!core.state.canvasMeta[k].anim_appearance || core.state.canvasMeta[k].anim_appearance === 'fade') core.state.canvasMeta[k].anim_appearance = 'fadeIn';
                                    if (core.state.canvasMeta[k].anim_duration === undefined) core.state.canvasMeta[k].anim_duration = 1000;
                                    if (core.state.canvasMeta[k].anim_delay === undefined) core.state.canvasMeta[k].anim_delay = 0;
                                    if (!core.state.canvasMeta[k].anim_disappearance) core.state.canvasMeta[k].anim_disappearance = 'none'; else if (core.state.canvasMeta[k].anim_disappearance === 'fade') core.state.canvasMeta[k].anim_disappearance = 'fadeOut';
                                    if (core.state.canvasMeta[k].anim_exit_enabled === undefined) core.state.canvasMeta[k].anim_exit_enabled = false;
                                }
                            });

                            core.renderCanvasTabs();
                        }

                        if (window.WpPopPopBuilderSettings && cfg.settings) {
                            window.WpPopPopBuilderSettings.setSettings(cfg.settings);
                        }

                        var title = row.title || cfg.title || '';
                        if (title) {
                            $('#wppoppop-builder-title').val(title);
                        }

                        if (core) {
                            core.switchCanvas(1);
                        }

                        if (window.WpPopPopBuilderSettings) {
                            window.WpPopPopBuilderSettings.renderCanvasBullets();
                        }
                    }
                }
            });
        }
    };
})(jQuery);

// Delegated click handler for Save Popup button
jQuery(document).off('click.wppoppop_global_save_click', '#wppoppop-btn-save, .wppoppop-btn-save')
    .on('click.wppoppop_global_save_click', '#wppoppop-btn-save, .wppoppop-btn-save', function(e) {
        e.preventDefault();
        if (window.WpPopPopBuilderIO && typeof window.WpPopPopBuilderIO.savePopup === 'function') {
            window.WpPopPopBuilderIO.savePopup();
        }
    });

/* === WPPOPPOP_PHASE5_IO_PRESAVE_FLUSH_START === */
(function(window, $) {
    'use strict';

    function flushActiveDecomposedElement() {
        var $drawer = $('#wppoppop-inspector-drawer');
        if (!$drawer.length || (!$drawer.hasClass('is-open') && !$drawer.hasClass('open'))) {
            return;
        }
        // Trigger change on any focused input inside the inspector drawer so two-way binders run
        var $focused = $drawer.find('input:focus, textarea:focus, select:focus');
        if ($focused.length) {
            $focused.trigger('change');
        }
        // Explicitly flush visible .wppoppop-gp-bind controls into the active element
        var insp = window.WpPopPopBuilderInspector;
        var activeId = (insp && insp.activeElementId) ? String(insp.activeElementId) : (
            window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? String(window.WpPopPopBuilderCore.state.activeId || '') : ''
        );
        var cleanId = activeId.replace(/^wppoppop-el-/, '').replace(/^el-/, '');
        if (!cleanId || !window.WpPopPopBuilderCanvas || typeof window.WpPopPopBuilderCanvas.getActiveElements !== 'function') {
            return;
        }
        var els = window.WpPopPopBuilderCanvas.getActiveElements() || [];
        var el = Array.isArray(els) ? els.find(function(item) {
            return item && (String(item.id) === cleanId || String(item.id) === activeId);
        }) : null;
        if (!el) return;

        $drawer.find('.element-panel:visible .wppoppop-gp-bind').each(function() {
            var $inp = $(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            el[prop] = $inp.is(':checkbox') ? $inp.is(':checked') : $inp.val();
        });
    }

    $(function() {
        var io = window.WpPopPopBuilderIO;
        if (io && !io._phase5FlushWrapped && typeof io.exportConfig === 'function') {
            io._phase5FlushWrapped = true;
            var origExport = io.exportConfig;
            io.exportConfig = function() {
                try { flushActiveDecomposedElement(); } catch (e) {}
                return origExport.apply(this, arguments);
            };
        }
    });
})(window, jQuery);
/* === WPPOPPOP_PHASE5_IO_PRESAVE_FLUSH_END === */

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
