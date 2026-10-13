/**
 * WpPopPop Element: Submit Button
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    var submitDefinition = {
        label: 'Submit Button',
        icon: 'dashicons-yes-alt',

        getDefaults: function() {
            return {
                width: 200,
                height: 46,
                content: 'Submit Now',
                btnText: 'Submit Now',
                submit_action: 'close',
                redirect_url: '',
                bgColor: '#3b82f6',
                color: '#ffffff',
                hoverBg: '#2563eb',
                hoverColor: '#ffffff',
                borderRadius: 6,
                borderWidth: 0,
                borderColor: '#1d4ed8',
                fontSize: 15,
                fontWeight: '600',
                textAlign: 'center'
            };
        },

        renderStage: function(el) {
            var label = el.btnText || el.content || 'Submit Now';
            var align = el.textAlign || 'center';

            return '<button type="button" disabled style="width:100%;height:100%;border:inherit;border-radius:inherit;' +
                'background:inherit;color:inherit;font-size:inherit;font-weight:inherit;text-align:' + align + ';' +
                'display:flex;align-items:center;justify-content:center;padding:0 16px;box-sizing:border-box;pointer-events:none;' +
                'cursor:default;outline:none;-webkit-appearance:none;appearance:none;">' +
                label +
                '</button>';
        },

        renderSettings: function(el, $container) {
            var action = el.submit_action || el.action || 'close';
            var label = el.btnText || el.content || 'Submit Now';

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Submit Button Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Button Label Text</label>' +
                    '<input type="text" id="insp-submit-text" value="' + label + '" class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Submission Behavior</label>' +
                    '<select id="insp-submit-action" class="wppoppop-insp-input">' +
                        '<option value="close"' + (action === 'close' ? ' selected' : '') + '>Show Success & Close Popup</option>' +
                        '<option value="next_canvas"' + (action === 'next_canvas' ? ' selected' : '') + '>Advance to Next Canvas</option>' +
                        '<option value="redirect"' + (action === 'redirect' ? ' selected' : '') + '>Redirect to External URL</option>' +
                    '</select>' +
                '</div>' +
                '<div id="insp-submit-redir-wrap" style="margin-bottom:8px;' + (action === 'redirect' ? '' : 'display:none;') + '">' +
                    '<label>Destination Redirect URL</label>' +
                    '<input type="url" id="insp-submit-redir" value="' + (el.redirect_url || el.redirectUrl || '') + '" placeholder="https://example.com/thank-you" class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px;padding-top:10px;border-top:1px dashed #e2e8f0;">' +
                    '<div>' +
                        '<label>Hover Background</label>' +
                        '<input type="color" id="insp-submit-hover-bg" value="' + (el.hoverBg || '#2563eb') + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                    '<div>' +
                        '<label>Hover Text Color</label>' +
                        '<input type="color" id="insp-submit-hover-color" value="' + (el.hoverColor || '#ffffff') + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-submit-text').on('input change', function() {
                el.content = $(this).val();
                el.btnText = el.content;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-submit-action').on('change', function() {
                el.submit_action = $(this).val();
                el.action = el.submit_action;
                if (el.submit_action === 'redirect') {
                    $('#insp-submit-redir-wrap').show();
                } else {
                    $('#insp-submit-redir-wrap').hide();
                }
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-submit-redir').on('change', function() {
                el.redirect_url = $(this).val().trim();
                el.redirectUrl = el.redirect_url;
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-submit-hover-bg').on('input change', function() {
                el.hoverBg = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-submit-hover-color').on('input change', function() {
                el.hoverColor = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    };

    window.WpPopPopElements.register('submit', submitDefinition);
    window.WpPopPopElements.register('submit_btn', submitDefinition);
})(jQuery);
