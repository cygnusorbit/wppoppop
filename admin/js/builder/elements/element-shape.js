/**
 * WpPopPop Element: Vector SVG Shape
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    var PRESET_SVGS = {
        circle: '<circle cx="50" cy="50" r="44"/>',
        square: '<rect x="6" y="6" width="88" height="88"/>',
        rounded_rect: '<rect x="6" y="6" width="88" height="88" rx="16" ry="16"/>',
        star: '<polygon points="50,6 63,36 96,36 69,56 80,88 50,68 20,88 31,56 4,36 37,36"/>',
        triangle: '<polygon points="50,8 94,88 6,88"/>',
        diamond: '<polygon points="50,6 94,50 50,94 6,50"/>',
        heart: '<path d="M50 84 C20 60 6 42 6 26 A 18 18 0 0 1 44 16 L 50 22 L 56 16 A 18 18 0 0 1 94 26 C 94 42 80 60 50 84 Z"/>',
        hexagon: '<polygon points="50,6 90,28 90,72 50,94 10,72 10,28"/>',
        octagon: '<polygon points="32,6 68,6 94,32 94,68 68,94 32,94 6,68 6,32"/>',
        shield: '<path d="M50 6 L90 20 L90 54 C90 74 50 94 50 94 C50 94 10 74 10 54 L10 20 Z"/>',
        cross: '<polygon points="36,6 64,6 64,36 94,36 94,64 64,64 64,94 36,94 36,64 6,64 6,36 36,36"/>',
        rectangle: '<rect x="0" y="0" width="100" height="100"/>',
        rect: '<rect x="0" y="0" width="100" height="100"/>',
        rounded: '<rect x="0" y="0" width="100" height="100" rx="16" ry="16"/>',
        pill: '<rect x="2" y="15" width="96" height="70" rx="35" ry="35"/>',
        pentagon: '<polygon points="50,3 97,38 79,94 21,94 3,38"/>',
        badge: '<polygon points="50,2 62,13 78,10 83,25 97,33 93,49 99,64 86,74 83,90 67,91 56,99 44,91 28,90 25,74 12,64 18,49 14,33 28,25 33,10 48,13"/>'
    };

    window.WpPopPopElements.register('shape', {
        label: 'Vector Shape',
        icon: 'dashicons-marker',

        getDefaults: function() {
            return {
                width: 120,
                height: 120,
                shapePreset: 'square',
                shapeFill: '#3b82f6',
                shapeStroke: '#1d4ed8',
                shapeStrokeWidth: 2,
                shapeRotate: 0
            };
        },

        renderStage: function(el) {
            var preset = el.shapePreset || el.shape_preset || 'square';
            var geom = PRESET_SVGS[preset] || PRESET_SVGS.square || PRESET_SVGS.rectangle;
            var fill = el.shapeNoFill ? 'none' : (el.shapeFill || el.shape_fill || '#3b82f6');
            var stroke = el.strokeColor || el.shapeStroke || el.shape_stroke || '#1d4ed8';
            var strokeW = (el.strokeWidth !== undefined) ? el.strokeWidth : ((el.shapeStrokeWidth !== undefined) ? el.shapeStrokeWidth : 0);
            var rot = parseInt(el.shapeRotate || el.shape_rotate || 0, 10);

            var rotStyle = ''; // rotation applied once on the stage node

            return '<svg viewBox="0 0 100 100" style="width:100%;height:100%;display:block;overflow:visible;' + rotStyle + '" ' +
                'fill="' + fill + '" stroke="' + stroke + '" stroke-width="' + strokeW + '" vector-effect="non-scaling-stroke">' +
                geom +
                '</svg>';
        },

        renderSettings: function(el, $container) {
            var preset = el.shapePreset || el.shape_preset || 'square';
            var fill = el.shapeFill || el.shape_fill || '#3b82f6';
            var stroke = el.shapeStroke || el.shape_stroke || '#1d4ed8';
            var strokeW = (el.shapeStrokeWidth !== undefined) ? el.shapeStrokeWidth : 2;
            var rot = parseInt(el.shapeRotate || el.shape_rotate || 0, 10);

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Vector Shape Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Shape Preset</label>' +
                    '<select id="insp-shape-preset" class="wppoppop-insp-input">' +
                        '<option value="circle"' + (preset === 'circle' ? ' selected' : '') + '>Circle</option>' +
                        '<option value="square"' + (preset === 'square' ? ' selected' : '') + '>Square</option>' +
                        '<option value="rounded_rect"' + (preset === 'rounded_rect' ? ' selected' : '') + '>Rounded Rectangle</option>' +
                        '<option value="star"' + (preset === 'star' ? ' selected' : '') + '>Star</option>' +
                        '<option value="triangle"' + (preset === 'triangle' ? ' selected' : '') + '>Triangle</option>' +
                        '<option value="diamond"' + (preset === 'diamond' ? ' selected' : '') + '>Diamond</option>' +
                        '<option value="heart"' + (preset === 'heart' ? ' selected' : '') + '>Heart</option>' +
                        '<option value="hexagon"' + (preset === 'hexagon' ? ' selected' : '') + '>Hexagon</option>' +
                        '<option value="octagon"' + (preset === 'octagon' ? ' selected' : '') + '>Octagon</option>' +
                        '<option value="shield"' + (preset === 'shield' ? ' selected' : '') + '>Shield</option>' +
                        '<option value="cross"' + (preset === 'cross' ? ' selected' : '') + '>Cross</option>' +
                    '</select>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Fill Color</label>' +
                        '<input type="color" id="insp-shape-fill" value="' + fill + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                    '<div>' +
                        '<label>Stroke Color</label>' +
                        '<input type="color" id="insp-shape-stroke" value="' + stroke + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Stroke Width (px)</label>' +
                        '<input type="number" id="insp-shape-strokew" min="0" max="30" value="' + strokeW + '" class="wppoppop-insp-input">' +
                    '</div>' +
                    '<div>' +
                        '<label>Rotation (°)</label>' +
                        '<input type="number" id="insp-shape-rotate" min="0" max="360" value="' + rot + '" class="wppoppop-insp-input">' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-shape-preset').on('change', function() {
                el.shapePreset = $(this).val();
                el.shape_preset = el.shapePreset;
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-shape-fill').on('input change', function() {
                el.shapeFill = $(this).val();
                el.shape_fill = el.shapeFill;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-shape-stroke').on('input change', function() {
                el.shapeStroke = $(this).val();
                el.shape_stroke = el.shapeStroke;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-shape-strokew').on('input change', function() {
                el.shapeStrokeWidth = parseInt($(this).val(), 10) || 0;
                el.shape_stroke_width = el.shapeStrokeWidth;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-shape-rotate').on('input change', function() {
                el.shapeRotate = parseInt($(this).val(), 10) || 0;
                el.shape_rotate = el.shapeRotate;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    });
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
