/**
 * WpPopPop Element: CTA Link Button
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    var linkDefinition = {
        label: 'Link Button',
        icon: 'dashicons-admin-links',

        getDefaults: function() {
            return {
                width: 180,
                height: 42,
                content: 'Learn More →',
                btnText: 'Learn More →',
                link_url: 'https://example.com',
                link_target: '_blank',
                nofollow: false,
                bgColor: '#10b981',
                color: '#ffffff',
                hoverBg: '#059669',
                hoverColor: '#ffffff',
                borderRadius: 6,
                borderWidth: 0,
                borderColor: '#047857',
                fontSize: 14,
                fontWeight: '600',
                textAlign: 'center'
            };
        },

        renderStage: function(el) {
            var label = el.btnText || el.content || 'Learn More →';
            var align = el.textAlign || 'center';

            return '<a href="javascript:void(0);" style="width:100%;height:100%;border:inherit;border-radius:inherit;' +
                'background:inherit;color:inherit;font-size:inherit;font-weight:inherit;text-align:' + align + ';' +
                'text-decoration:none;display:flex;align-items:center;justify-content:center;padding:0 16px;' +
                'box-sizing:border-box;pointer-events:none;cursor:default;">' +
                label +
                '</a>';
        },

        renderSettings: function(el, $container) {
            var label = el.btnText || el.content || 'Learn More →';
            var url = el.link_url || el.url || 'https://example.com';
            var target = el.link_target || el.target || '_blank';

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Link Button Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Button Label Text</label>' +
                    '<input type="text" id="insp-link-text" value="' + label + '" class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Destination URL</label>' +
                    '<input type="url" id="insp-link-url" value="' + url + '" placeholder="https://..." class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:6px;margin-top:6px;">' +
                    '<div style="display:flex;align-items:center;gap:6px;">' +
                        '<input type="checkbox" id="insp-link-target"' + (target === '_blank' ? ' checked' : '') + '>' +
                        '<label for="insp-link-target" style="margin:0;cursor:pointer;">Open in New Tab (_blank)</label>' +
                    '</div>' +
                    '<div style="display:flex;align-items:center;gap:6px;">' +
                        '<input type="checkbox" id="insp-link-nofollow"' + (el.nofollow ? ' checked' : '') + '>' +
                        '<label for="insp-link-nofollow" style="margin:0;cursor:pointer;">Add rel="nofollow"</label>' +
                    '</div>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px;padding-top:10px;border-top:1px dashed #e2e8f0;">' +
                    '<div>' +
                        '<label>Hover Background</label>' +
                        '<input type="color" id="insp-link-hover-bg" value="' + (el.hoverBg || '#059669') + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                    '<div>' +
                        '<label>Hover Text Color</label>' +
                        '<input type="color" id="insp-link-hover-color" value="' + (el.hoverColor || '#ffffff') + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-link-text').on('input change', function() {
                el.content = $(this).val();
                el.btnText = el.content;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-link-url').on('change', function() {
                el.link_url = $(this).val().trim();
                el.url = el.link_url;
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-link-target').on('change', function() {
                el.link_target = $(this).is(':checked') ? '_blank' : '_self';
                el.target = el.link_target;
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-link-nofollow').on('change', function() {
                el.nofollow = $(this).is(':checked');
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-link-hover-bg').on('input change', function() {
                el.hoverBg = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-link-hover-color').on('input change', function() {
                el.hoverColor = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    };

    window.WpPopPopElements.register('link', linkDefinition);
    window.WpPopPopElements.register('link_btn', linkDefinition);
})(jQuery);
