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
        cross: '<polygon points="36,6 64,6 64,36 94,36 94,64 64,64 64,94 36,94 36,64 6,64 6,36 36,36"/>'
    };

    window.WpPopPopElements.register('shape', {
        label: 'Vector Shape',
        icon: 'dashicons-marker',

        getDefaults: function() {
            return {
                width: 120,
                height: 120,
                shapePreset: 'circle',
                shapeFill: '#3b82f6',
                shapeStroke: '#1d4ed8',
                shapeStrokeWidth: 2,
                shapeRotate: 0
            };
        },

        renderStage: function(el) {
            var preset = el.shapePreset || el.shape_preset || 'circle';
            var geom = PRESET_SVGS[preset] || PRESET_SVGS.circle;
            var fill = el.shapeFill || el.shape_fill || '#3b82f6';
            var stroke = el.shapeStroke || el.shape_stroke || '#1d4ed8';
            var strokeW = (el.shapeStrokeWidth !== undefined) ? el.shapeStrokeWidth : 2;
            var rot = parseInt(el.shapeRotate || el.shape_rotate || 0, 10);

            var rotStyle = rot ? 'transform:rotate(' + rot + 'deg);transform-origin:center;' : '';

            return '<svg viewBox="0 0 100 100" style="width:100%;height:100%;display:block;overflow:visible;' + rotStyle + '" ' +
                'fill="' + fill + '" stroke="' + stroke + '" stroke-width="' + strokeW + '" vector-effect="non-scaling-stroke">' +
                geom +
                '</svg>';
        },

        renderSettings: function(el, $container) {
            var preset = el.shapePreset || el.shape_preset || 'circle';
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
