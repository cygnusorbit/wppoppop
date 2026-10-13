/**
 * WpPopPop Element: On-Canvas Close Icon
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    var closeDefinition = {
        label: 'Close Icon',
        icon: 'dashicons-no-alt',

        getDefaults: function() {
            return {
                width: 36,
                height: 36,
                glyph_style: 'times',
                icon_size: 22,
                close_action: 'close',
                cookie_days: 30,
                color: '#64748b',
                hoverColor: '#0f172a',
                bgColor: 'transparent',
                borderRadius: 18,
                borderWidth: 0,
                textAlign: 'center'
            };
        },

        renderStage: function(el) {
            var style = el.glyph_style || 'times';
            var glyph = '×';
            if (style === 'cross') glyph = '✕';
            if (style === 'circle_x') glyph = 'ⓧ';
            if (style === 'custom_text') glyph = el.content || 'CLOSE';

            var size = el.icon_size || 22;

            return '<button type="button" disabled style="width:100%;height:100%;border:inherit;border-radius:inherit;' +
                'background:inherit;color:inherit;font-size:' + size + 'px;line-height:1;display:flex;align-items:center;' +
                'justify-content:center;box-sizing:border-box;pointer-events:none;cursor:default;outline:none;' +
                '-webkit-appearance:none;appearance:none;padding:0;">' +
                glyph +
                '</button>';
        },

        renderSettings: function(el, $container) {
            var style = el.glyph_style || 'times';
            var action = el.close_action || el.closeAction || 'close';

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Close Trigger Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Glyph Style</label>' +
                    '<select id="insp-close-style" class="wppoppop-insp-input">' +
                        '<option value="times"' + (style === 'times' ? ' selected' : '') + '>Standard Multiply (×)</option>' +
                        '<option value="cross"' + (style === 'cross' ? ' selected' : '') + '>Clean Cross (✕)</option>' +
                        '<option value="circle_x"' + (style === 'circle_x' ? ' selected' : '') + '>Circled Cross (ⓧ)</option>' +
                        '<option value="custom_text"' + (style === 'custom_text' ? ' selected' : '') + '>Text Label</option>' +
                    '</select>' +
                '</div>' +
                '<div id="insp-close-text-wrap" style="margin-bottom:8px;' + (style === 'custom_text' ? '' : 'display:none;') + '">' +
                    '<label>Close Label Text</label>' +
                    '<input type="text" id="insp-close-text" value="' + (el.content || 'CLOSE') + '" class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Glyph Size (px)</label>' +
                    '<input type="number" id="insp-close-size" value="' + (el.icon_size || 22) + '" min="10" max="64" class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Dismissal Behavior</label>' +
                    '<select id="insp-close-action" class="wppoppop-insp-input">' +
                        '<option value="close"' + (action === 'close' ? ' selected' : '') + '>Close Popup (Normal)</option>' +
                        '<option value="close_period"' + (action === 'close_period' ? ' selected' : '') + '>Hide for Browser Session</option>' +
                        '<option value="close_forever"' + (action === 'close_forever' ? ' selected' : '') + '>Set Suppression Cookie (Days)</option>' +
                    '</select>' +
                '</div>' +
                '<div id="insp-close-days-wrap" style="margin-bottom:8px;' + (action === 'close_forever' ? '' : 'display:none;') + '">' +
                    '<label>Suppression Period (Days)</label>' +
                    '<input type="number" id="insp-close-days" value="' + (el.cookie_days || 30) + '" min="1" max="365" class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="margin-top:10px;padding-top:10px;border-top:1px dashed #e2e8f0;">' +
                    '<label>Hover Color</label>' +
                    '<input type="color" id="insp-close-hover-color" value="' + (el.hoverColor || '#0f172a') + '" class="wppoppop-insp-input" style="height:32px;">' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-close-style').on('change', function() {
                el.glyph_style = $(this).val();
                if (el.glyph_style === 'custom_text') {
                    $('#insp-close-text-wrap').show();
                } else {
                    $('#insp-close-text-wrap').hide();
                }
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-close-text').on('input change', function() {
                el.content = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-close-size').on('input change', function() {
                el.icon_size = parseInt($(this).val(), 10) || 22;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-close-action').on('change', function() {
                el.close_action = $(this).val();
                el.closeAction = el.close_action;
                if (el.close_action === 'close_forever') {
                    $('#insp-close-days-wrap').show();
                } else {
                    $('#insp-close-days-wrap').hide();
                }
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-close-days').on('change', function() {
                el.cookie_days = parseInt($(this).val(), 10) || 30;
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-close-hover-color').on('input change', function() {
                el.hoverColor = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    };

    window.WpPopPopElements.register('close', closeDefinition);
    window.WpPopPopElements.register('close_icon', closeDefinition);
})(jQuery);
