/**
 * WpPopPop Element: Payment Checkout Button
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    var payDefinition = {
        label: 'Payment Button',
        icon: 'dashicons-cart',

        getDefaults: function() {
            return {
                width: 220,
                height: 48,
                content: 'Pay Now ($19.99)',
                btnText: 'Pay Now ($19.99)',
                gateway: 'stripe',
                amount: '19.99',
                currency: 'USD',
                item_desc: 'Product Order',
                bgColor: '#6366f1',
                color: '#ffffff',
                hoverBg: '#4f46e5',
                hoverColor: '#ffffff',
                borderRadius: 6,
                borderWidth: 0,
                borderColor: '#4338ca',
                fontSize: 15,
                fontWeight: '600',
                textAlign: 'center'
            };
        },

        renderStage: function(el) {
            var label = el.btnText || el.content || 'Pay Now';
            var align = el.textAlign || 'center';

            return '<button type="button" disabled style="width:100%;height:100%;border:inherit;border-radius:inherit;' +
                'background:inherit;color:inherit;font-size:inherit;font-weight:inherit;text-align:' + align + ';' +
                'display:flex;align-items:center;justify-content:center;gap:6px;padding:0 16px;box-sizing:border-box;pointer-events:none;' +
                'cursor:default;outline:none;-webkit-appearance:none;appearance:none;">' +
                '<span>💳</span> <span>' + label + '</span>' +
                '</button>';
        },

        renderSettings: function(el, $container) {
            var label = el.btnText || el.content || 'Pay Now ($19.99)';
            var gateway = el.gateway || 'stripe';
            var currency = el.currency || 'USD';

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Payment Button Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Button Label Text</label>' +
                    '<input type="text" id="insp-pay-text" value="' + label + '" class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Payment Gateway</label>' +
                        '<select id="insp-pay-gateway" class="wppoppop-insp-input">' +
                            '<option value="stripe"' + (gateway === 'stripe' ? ' selected' : '') + '>Stripe</option>' +
                            '<option value="paypal"' + (gateway === 'paypal' ? ' selected' : '') + '>PayPal</option>' +
                        '</select>' +
                    '</div>' +
                    '<div>' +
                        '<label>Currency</label>' +
                        '<select id="insp-pay-currency" class="wppoppop-insp-input">' +
                            '<option value="USD"' + (currency === 'USD' ? ' selected' : '') + '>USD ($)</option>' +
                            '<option value="EUR"' + (currency === 'EUR' ? ' selected' : '') + '>EUR (€)</option>' +
                            '<option value="GBP"' + (currency === 'GBP' ? ' selected' : '') + '>GBP (£)</option>' +
                            '<option value="CAD"' + (currency === 'CAD' ? ' selected' : '') + '>CAD ($)</option>' +
                            '<option value="AUD"' + (currency === 'AUD' ? ' selected' : '') + '>AUD ($)</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Price / Amount</label>' +
                        '<input type="number" step="0.01" id="insp-pay-amount" value="' + (el.amount || '19.99') + '" class="wppoppop-insp-input">' +
                    '</div>' +
                    '<div>' +
                        '<label>Item Description</label>' +
                        '<input type="text" id="insp-pay-desc" value="' + (el.item_desc || 'Product Order') + '" class="wppoppop-insp-input">' +
                    '</div>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px;padding-top:10px;border-top:1px dashed #e2e8f0;">' +
                    '<div>' +
                        '<label>Hover Background</label>' +
                        '<input type="color" id="insp-pay-hover-bg" value="' + (el.hoverBg || '#4f46e5') + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                    '<div>' +
                        '<label>Hover Text Color</label>' +
                        '<input type="color" id="insp-pay-hover-color" value="' + (el.hoverColor || '#ffffff') + '" class="wppoppop-insp-input" style="height:32px;">' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-pay-text').on('input change', function() {
                el.content = $(this).val();
                el.btnText = el.content;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-pay-gateway').on('change', function() {
                el.gateway = $(this).val();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-pay-currency').on('change', function() {
                el.currency = $(this).val();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-pay-amount').on('input change', function() {
                el.amount = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-pay-desc').on('change', function() {
                el.item_desc = $(this).val();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-pay-hover-bg').on('input change', function() {
                el.hoverBg = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-pay-hover-color').on('input change', function() {
                el.hoverColor = $(this).val();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    };

    window.WpPopPopElements.register('pay', payDefinition);
    window.WpPopPopElements.register('pay_btn', payDefinition);
})(jQuery);
