/**
 * WpPopPop Builder: Stage Mirror
 * Edit mode shows the SAME markup + inline styles as live preview by asking the real frontend
 * renderer (PHP) for each layer and mirroring it onto the stage node. Geometry stays editable.
 * Cross-browser: Chrome, Safari, Firefox, Edge (no optional chaining, no arrow functions).
 */
(function(window, $) {
    'use strict';
    if (window.__wppoppopStageMirror) return;
    window.__wppoppopStageMirror = true;

    var cache = {}, timer = null, busy = false, again = false, fails = 0;
    var KEEP = ['position', 'left', 'top', 'width', 'height', 'zIndex', 'display'];
    var SIG_SKIP = ['top', 'left', 'x', 'y', 'zIndex'];

    function vars() { return window.wppoppop_vars || {}; }
    function ajaxUrl() {
        var v = vars();
        return v.ajax_url || (typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : '/wp-admin/admin-ajax.php');
    }
    function nonce() { var v = vars(); return v.nonce || $('#wppoppop-builder-nonce').val() || ''; }
    function canvas() { return window.WpPopPopBuilderCanvas; }
    function elements() {
        var C = canvas();
        var l = (C && typeof C.getActiveElements === 'function') ? C.getActiveElements() : [];
        return Array.isArray(l) ? l : [];
    }
    function blocked() {
        var C = canvas();
        return fails >= 3 || window.WPPOPPOP_STAGE_MIRROR === false || !!(C && C.isPreview);
    }
    function sigOf(el) {
        var c = {}, k;
        for (k in el) {
            if (Object.prototype.hasOwnProperty.call(el, k) && SIG_SKIP.indexOf(k) === -1) { c[k] = el[k]; }
        }
        try { return JSON.stringify(c); } catch (e) { return String(Math.random()); }
    }

    function apply(el, html, sig, force) {
        var node = document.getElementById('el-' + el.id);
        if (!node) return;
        if (!force && node.getAttribute('data-wpp-sig') === sig) return;

        var doc;
        try { doc = new DOMParser().parseFromString('<body>' + html + '</body>', 'text/html'); } catch (e) { return; }
        var scripts = doc.querySelectorAll('script');
        for (var s = 0; s < scripts.length; s++) { scripts[s].parentNode.removeChild(scripts[s]); }

        var wrap = doc.body.querySelector('.wppoppop-layer-item') || doc.body.firstElementChild;
        if (!wrap) return;
        var extras = '', i, kids = doc.body.children;
        for (i = 0; i < kids.length; i++) { if (kids[i] !== wrap) { extras += kids[i].outerHTML; } }
        var inner = extras + wrap.innerHTML;

        var keep = {};
        for (i = 0; i < KEEP.length; i++) { keep[KEEP[i]] = node.style[KEEP[i]]; }
        var $handles = $(node).children('.ui-resizable-handle, .wppoppop-resize-handle').detach();

        node.style.cssText = wrap.getAttribute('style') || '';
        for (i = 0; i < KEEP.length; i++) { if (keep[KEEP[i]]) { node.style[KEEP[i]] = keep[KEEP[i]]; } }

        node.innerHTML = inner;
        if ((wrap.getAttribute('class') || '').indexOf('wppoppop-el-shape') !== -1) { node.style.borderRadius = '0'; }
        $(node).append($handles);

        var cls = (wrap.getAttribute('class') || '').split(/\s+/);
        for (i = 0; i < cls.length; i++) {
            if (/^wppoppop-el-/.test(cls[i])) { node.classList.add(cls[i]); }
        }
        node.setAttribute('data-wpp-sig', sig);
    }

    function syncOne(el, force) {
        if (blocked() || !el || el.id === undefined || el.hidden) return;
        var sig = sigOf(el), hit = cache[el.id];
        if (hit && hit.sig === sig) { apply(el, hit.html, sig, force); return; }
        schedule();
    }

    function schedule(ms) {
        if (blocked()) return;
        if (timer) clearTimeout(timer);
        timer = setTimeout(flush, ms || 90);
    }

    function flush() {
        timer = null;
        if (blocked()) return;
        if (busy) { again = true; return; }
        var send = [], sigs = {};
        elements().forEach(function(el) {
            if (!el || el.id === undefined || el.hidden) return;
            var s = sigOf(el), hit = cache[el.id];
            if (hit && hit.sig === s) { apply(el, hit.html, s, false); return; }
            send.push(el); sigs[el.id] = s;
        });
        if (!send.length) return;
        busy = true;
        $.ajax({
            url: ajaxUrl(), type: 'POST', dataType: 'json',
            data: { action: 'wppoppop_render_stage', nonce: nonce(), elements: JSON.stringify(send) }
        }).done(function(res) {
            if (res && res.success && res.data && res.data.html) {
                fails = 0;
                if (Object.keys(cache).length > 400) { cache = {}; }
                var map = res.data.html;
                elements().forEach(function(el) {
                    if (!el || map[el.id] === undefined) return;
                    cache[el.id] = { sig: sigs[el.id], html: map[el.id] };
                    if (sigOf(el) === sigs[el.id]) { apply(el, map[el.id], sigs[el.id], true); }
                });
            } else { fails++; }
        }).fail(function() { fails++; }).always(function() {
            busy = false;
            if (again) { again = false; schedule(30); }
        });
    }

    function wrapFn(obj, name, afterEl) {
        var f = obj && obj[name];
        if (typeof f !== 'function') return false;
        if (f.__wppMirror) return true;
        var w = function() {
            var r = f.apply(this, arguments);
            try { afterEl.apply(this, arguments); } catch (e) {}
            return r;
        };
        w.__wppMirror = true;
        obj[name] = w;
        return true;
    }

    function afterStage(el) {
        if (!el || typeof el !== 'object' || el.id === undefined) return;
        var n = document.getElementById('el-' + el.id);
        if (n) n.removeAttribute('data-wpp-sig');
        syncOne(el, true);
    }

    function hook() {
        var R = window.WpPopPopElementsRegistry;
        var C = canvas();
        var ok = true;
        if (R) {
            ok = wrapFn(R, 'renderMediaElementStage', afterStage) && ok;
            ok = wrapFn(R, 'renderFormElementStage', afterStage) && ok;
            ok = wrapFn(R, 'applyStyleToStageNode', afterStage) && ok;
        } else { ok = false; }
        if (C) {
            ok = wrapFn(C, 'renderCanvas', function() {
                elements().forEach(function(el) { syncOne(el, true); });
                schedule();
            }) && ok;
        } else { ok = false; }
        return ok;
    }

    $(document).off('input.wppMirror change.wppMirror')
        .on('input.wppMirror change.wppMirror',
            '#wppoppop-inspector-drawer input, #wppoppop-inspector-drawer select, #wppoppop-inspector-drawer textarea',
            function() { setTimeout(function() { schedule(); }, 30); });

    $(function() {
        var tries = 0;
        var t = setInterval(function() {
            tries++;
            if (hook() && tries > 4) { /* keep re-checking slowly below */ }
            if (tries > 60) { clearInterval(t); }
        }, 250);
        setInterval(hook, 2000);
        setTimeout(function() { schedule(10); }, 400);
    });
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
