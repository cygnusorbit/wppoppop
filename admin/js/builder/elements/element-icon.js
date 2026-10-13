/**
 * WpPopPop Element: Vector Icon
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    window.WpPopPopElements.register('icon', {
        label: 'Icon Glyph',
        icon: 'dashicons-star-filled',

        getDefaults: function() {
            return {
                width: 48,
                height: 48,
                iconGlyph: 'dashicons-star-filled',
                iconSize: 32,
                iconColor: '#3b82f6',
                bgColor: 'transparent',
                borderRadius: 24
            };
        },

        renderStage: function(el) {
            var glyph = el.iconGlyph || 'dashicons-star-filled';
            var size = el.iconSize || 32;
            var color = el.iconColor || '#3b82f6';

            return '<div style="width:100%;height:100%;border:inherit;border-radius:inherit;background:inherit;' +
                'display:flex;align-items:center;justify-content:center;box-sizing:border-box;pointer-events:none;user-select:none;">' +
                '<span class="dashicons ' + glyph + '" style="font-size:' + size + 'px;width:' + size + 'px;height:' + size + 'px;' +
                'line-height:1;display:flex;align-items:center;justify-content:center;color:' + color + ';"></span>' +
                '</div>';
        },

        renderSettings: function(el, $container) {
            var glyph = el.iconGlyph || 'dashicons-star-filled';
            var size = el.iconSize || 32;
            var color = el.iconColor || '#3b82f6';

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Icon Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Icon Glyph</label>' +
                    '<select id="insp-icon-glyph" class="wppoppop-insp-input">' +
                        '<option value="dashicons-star-filled"' + (glyph === 'dashicons-star-filled' ? ' selected' : '') + '>★ Star Filled</option>' +
                        '<option value="dashicons-heart"' + (glyph === 'dashicons-heart' ? ' selected' : '') + '>♥ Heart</option>' +
                        '<option value="dashicons-yes"' + (glyph === 'dashicons-yes' ? ' selected' : '') + '>✓ Checkmark</option>' +
                        '<option value="dashicons-bell"' + (glyph === 'dashicons-bell' ? ' selected' : '') + '>🔔 Notification Bell</option>' +
                        '<option value="dashicons-awards"' + (glyph === 'dashicons-awards' ? ' selected' : '') + '>🏆 Trophy Award</option>' +
                        '<option value="dashicons-cart"' + (glyph === 'dashicons-cart' ? ' selected' : '') + '>🛒 Shopping Cart</option>' +
                        '<option value="dashicons-megaphone"' + (glyph === 'dashicons-megaphone' ? ' selected' : '') + '>📢 Megaphone</option>' +
                        '<option value="dashicons-lock"' + (glyph === 'dashicons-lock' ? ' selected' : '') + '>🔒 Security Lock</option>' +
                        '<option value="dashicons-lightbulb"' + (glyph === 'dashicons-lightbulb' ? ' selected' : '') + '>💡 Lightbulb Idea</option>' +
                        '<option value="dashicons-email-alt"' + (glyph === 'dashicons-email-alt' ? ' selected' : '') + '>✉ Envelope</option>' +
                    '</select>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Icon Size (px)</label>' +
                        '<input type="number" id="insp-icon-size" min="12" max="120" value="' + size + '" class="wppoppop-insp-input">' +
                    '</div>' +
                    '<div>' +
                        '<label>Icon Color</label>' +
                        '<input type="color" id="insp-icon-color" value="' + color + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-icon-glyph').on('change', function() {
                el.iconGlyph = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-icon-size').on('input change', function() {
                el.iconSize = parseInt($(this).val(), 10) || 32;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-icon-color').on('input change', function() {
                el.iconColor = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    });
})(jQuery);
