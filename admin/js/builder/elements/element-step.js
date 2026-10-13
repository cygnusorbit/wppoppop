/**
 * WpPopPop Element: Next Step / Canvas Button
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    var stepDefinition = {
        label: 'Next Step Button',
        icon: 'dashicons-arrow-right-alt',

        getDefaults: function() {
            return {
                width: 200,
                height: 46,
                content: 'Next Step →',
                btnText: 'Next Step →',
                goto_canvas: 2,
                goto_screen: 2,
                bgColor: '#0f172a',
                color: '#ffffff',
                hoverBg: '#1e293b',
                hoverColor: '#ffffff',
                borderRadius: 6,
                borderWidth: 0,
                borderColor: '#334155',
                fontSize: 15,
                fontWeight: '600',
                textAlign: 'center'
            };
        },

        renderStage: function(el) {
            var label = el.btnText || el.content || 'Next Step →';
            var target = el.goto_canvas || el.goto_screen || 2;
            var align = el.textAlign || 'center';

            return '<button type="button" disabled style="width:100%;height:100%;border:inherit;border-radius:inherit;' +
                'background:inherit;color:inherit;font-size:inherit;font-weight:inherit;text-align:' + align + ';' +
                'display:flex;align-items:center;justify-content:center;gap:6px;padding:0 16px;box-sizing:border-box;pointer-events:none;' +
                'cursor:default;outline:none;-webkit-appearance:none;appearance:none;">' +
                '<span>' + label + '</span>' +
                '<small style="opacity:0.65;font-size:10px;">(Step ' + target + ')</small>' +
                '</button>';
        },

        renderSettings: function(el, $container) {
            var label = el.btnText || el.content || 'Next Step →';
            var target = parseInt(el.goto_canvas || el.goto_screen || 2, 10);

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Step Navigation Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Button Label Text</label>' +
                    '<input type="text" id="insp-step-text" value="' + label + '" class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Destination Canvas Step</label>' +
                    '<select id="insp-step-goto" class="wppoppop-insp-input">' +
                        '<option value="1"' + (target === 1 ? ' selected' : '') + '>Canvas 1</option>' +
                        '<option value="2"' + (target === 2 ? ' selected' : '') + '>Canvas 2</option>' +
                        '<option value="3"' + (target === 3 ? ' selected' : '') + '>Canvas 3</option>' +
                        '<option value="4"' + (target === 4 ? ' selected' : '') + '>Canvas 4</option>' +
                    '</select>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px;padding-top:10px;border-top:1px dashed #e2e8f0;">' +
                    '<div>' +
                        '<label>Hover Background</label>' +
                        '<input type="color" id="insp-step-hover-bg" value="' + (el.hoverBg || '#1e293b') + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                    '<div>' +
                        '<label>Hover Text Color</label>' +
                        '<input type="color" id="insp-step-hover-color" value="' + (el.hoverColor || '#ffffff') + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-step-text').on('input change', function() {
                el.content = $(this).val();
                el.btnText = el.content;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-step-goto').on('change', function() {
                el.goto_canvas = parseInt($(this).val(), 10) || 2;
                el.goto_screen = el.goto_canvas;
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-step-hover-bg').on('input change', function() {
                el.hoverBg = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-step-hover-color').on('input change', function() {
                el.hoverColor = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    };

    window.WpPopPopElements.register('step', stepDefinition);
    window.WpPopPopElements.register('step_btn', stepDefinition);
    window.WpPopPopElements.register('next_step', stepDefinition);
})(jQuery);
