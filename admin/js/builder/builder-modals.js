/**
 * WpPopPop Visual Builder: Embed Modal Controller ("How to Embed & Display Popup")
 * Single source of truth for snippets, tabs, copy (with Safari/HTTP fallback) and close.
 */
(function($) {
    'use strict';

    var MODAL = '#wppoppop-modal-embed';
    var PLACEHOLDER = 'YOUR_POPUP_UID';
    var ENS = '.wppoppopEmbed';

    window.WpPopPopBuilderModals = {
        _bound: false,
        _tab: 'shortcode',

        init: function() {
            if (this._bound) return;
            this._bound = true;
            this.bindEmbedModal();
        },

        getUid: function() {
            var uid = '';
            try {
                if (window.WpPopPopBuilderIO && typeof window.WpPopPopBuilderIO.getUid === 'function') {
                    uid = window.WpPopPopBuilderIO.getUid();
                }
            } catch (e) {}
            if (!uid) uid = $('#wppoppop-builder-uid').val() || '';
            return $.trim(String(uid || ''));
        },

        /* RULE: the server is the only source of uids. The UI only displays the saved value. */

        refresh: function() {
            var uid = this.getUid();
            var shown = uid || PLACEHOLDER; /* WPPOPPOP_EMBED_LIVE_NAME */
            $('#wppoppop-embed-shortcode-val').val('[wppoppop uid="' + shown + '"]');
            $('#wppoppop-embed-onclick-val').val('<a href="#" class="wppoppop-trigger" data-wppoppop-id="' + shown + '">Open Popup</a>');
            $('#wppoppop-embed-js-val').val("window.WpPopPop && window.WpPopPop.show('" + shown + "');");
            $('.wppoppop-embed-uid').text(shown);
            $('#wppoppop-embed-unsaved').toggle(!uid);
            var st = $.trim(String($('#wppoppop-builder-status').val() || ''));
            $('#wppoppop-embed-draft').toggle(!!uid && st !== '' && st !== 'publish' && st !== 'published');
        },

        switchTab: function(tab) {
            this._tab = tab || 'shortcode';
            $('.wppoppop-embed-tab').removeClass('active')
                .filter('[data-tab="' + this._tab + '"]').addClass('active');
            $('.wppoppop-embed-tab-content').removeClass('active').hide();
            $('#embed-tab-' + this._tab).addClass('active').show();
        },

        openEmbed: function() {
            this.refresh();
            this.switchTab(this._tab);
            $(MODAL).stop(true, true).css({
                display: 'flex', alignItems: 'center', justifyContent: 'center', opacity: 0
            }).animate({ opacity: 1 }, 150);
        },

        closeEmbed: function() {
            var $m = $(MODAL);
            if (!$m.is(':visible')) return;
            $m.stop(true, true).animate({ opacity: 0 }, 120, function() {
                $m.css({ display: 'none', opacity: 1 });
            });
        },

        copyText: function(text, $btn) {
            if (!$btn.data('origHtml')) $btn.data('origHtml', $btn.html());
            var orig = $btn.data('origHtml');

            function ok() {
                $btn.addClass('copied').html('<span class="dashicons dashicons-yes"></span> Copied!');
                setTimeout(function() { $btn.removeClass('copied').html(orig); }, 1600);
            }
            function fallback() {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.cssText = 'position:fixed;top:0;left:-9999px;opacity:0;';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try { ta.setSelectionRange(0, 99999); } catch (e) {}
                var done = false;
                try { done = document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(ta);
                if (done) { ok(); } else { window.prompt('Copy to clipboard (Cmd/Ctrl+C, Enter):', text); }
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(ok, fallback);
            } else {
                fallback();
            }
        },

        bindEmbedModal: function() {
            var self = this;

            $(document).off('click' + ENS, '#wppoppop-btn-embed, #wppoppop-btn-shortcode')
                .on('click' + ENS, '#wppoppop-btn-embed, #wppoppop-btn-shortcode', function(e) {
                    e.preventDefault();
                    self.openEmbed();
                });

            $(document).off('click' + ENS, '.wppoppop-embed-tab')
                .on('click' + ENS, '.wppoppop-embed-tab', function(e) {
                    e.preventDefault();
                    self.switchTab($(this).attr('data-tab'));
                });

            $(document).off('click' + ENS, '.wppoppop-btn-copy')
                .on('click' + ENS, '.wppoppop-btn-copy', function(e) {
                    e.preventDefault();
                    var $btn = $(this);
                    var $input = $('#' + $btn.attr('data-target'));
                    if (!$input.length) return;
                    $input[0].select();
                    self.copyText($input.val(), $btn);
                });

            $(document).off('focus' + ENS, MODAL + ' .wppoppop-code-box input')
                .on('focus' + ENS, MODAL + ' .wppoppop-code-box input', function() { this.select(); });

            $(document).off('click' + ENS, MODAL + ' .wppoppop-modal-close')
                .on('click' + ENS, MODAL + ' .wppoppop-modal-close', function(e) {
                    e.preventDefault();
                    self.closeEmbed();
                });

            $(document).off('mousedown' + ENS, MODAL)
                .on('mousedown' + ENS, MODAL, function(e) {
                    if (e.target === this) self.closeEmbed();
                });

            $(document).off('input' + ENS + ' change' + ENS, '#wppoppop-builder-title')
                .on('input' + ENS + ' change' + ENS, '#wppoppop-builder-title', function() {
                    if ($(MODAL).is(':visible')) self.refresh();
                });

            $(document).off('keydown' + ENS)
                .on('keydown' + ENS, function(e) {
                    if (e.key === 'Escape' || e.keyCode === 27) self.closeEmbed();
                });
        }
    };

    $(function() { window.WpPopPopBuilderModals.init(); });
})(jQuery);

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
