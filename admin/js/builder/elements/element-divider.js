/**
 * WpPopPop Element: Divider Separator
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    window.WpPopPopElements.register('divider', {
        label: 'Divider Line',
        icon: 'dashicons-minus',

        getDefaults: function() {
            return {
                width: 240,
                height: 16,
                dividerStyle: 'solid',
                dividerColor: '#cbd5e1',
                dividerThickness: 2,
                dividerOrientation: 'horizontal'
            };
        },

        renderStage: function(el) {
            var style = el.dividerStyle || 'solid';
            var color = el.dividerColor || '#cbd5e1';
            var thick = (el.dividerThickness !== undefined) ? el.dividerThickness : 2;
            var isVert = (el.dividerOrientation === 'vertical');

            if (isVert) {
                return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;box-sizing:border-box;">' +
                    '<div style="height:100%;border-left:' + thick + 'px ' + style + ' ' + color + ';"></div>' +
                    '</div>';
            }

            return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;box-sizing:border-box;">' +
                '<div style="width:100%;border-top:' + thick + 'px ' + style + ' ' + color + ';"></div>' +
                '</div>';
        },

        renderSettings: function(el, $container) {
            var style = el.dividerStyle || 'solid';
            var color = el.dividerColor || '#cbd5e1';
            var thick = (el.dividerThickness !== undefined) ? el.dividerThickness : 2;
            var orient = el.dividerOrientation || 'horizontal';

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Divider Settings</h4>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Orientation</label>' +
                        '<select id="insp-div-orient" class="wppoppop-insp-input">' +
                            '<option value="horizontal"' + (orient === 'horizontal' ? ' selected' : '') + '>Horizontal</option>' +
                            '<option value="vertical"' + (orient === 'vertical' ? ' selected' : '') + '>Vertical</option>' +
                        '</select>' +
                    '</div>' +
                    '<div>' +
                        '<label>Line Style</label>' +
                        '<select id="insp-div-style" class="wppoppop-insp-input">' +
                            '<option value="solid"' + (style === 'solid' ? ' selected' : '') + '>Solid</option>' +
                            '<option value="dashed"' + (style === 'dashed' ? ' selected' : '') + '>Dashed</option>' +
                            '<option value="dotted"' + (style === 'dotted' ? ' selected' : '') + '>Dotted</option>' +
                            '<option value="double"' + (style === 'double' ? ' selected' : '') + '>Double</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Thickness (px)</label>' +
                        '<input type="number" id="insp-div-thick" min="1" max="24" value="' + thick + '" class="wppoppop-insp-input">' +
                    '</div>' +
                    '<div>' +
                        '<label>Line Color</label>' +
                        '<input type="color" id="insp-div-color" value="' + color + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-div-orient').on('change', function() {
                el.dividerOrientation = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-div-style').on('change', function() {
                el.dividerStyle = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-div-thick').on('input change', function() {
                el.dividerThickness = parseInt($(this).val(), 10) || 1;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-div-color').on('input change', function() {
                el.dividerColor = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    });
})(jQuery);
