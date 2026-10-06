(function(window, $) {
    'use strict';
    window.WpPopPopPayments = window.WpPopPopPayments || {};

    var Modal = {
        init: function() {
            this.bindInspector();
            this.bindDismissal();
            this.bindPrint();
        },

        bindInspector: function() {
            $('.wppoppop-view-receipt-btn').on('click', function() {
                var $btn = $(this);
                $('#wppoppop-rcpt-txid').text($btn.data('txid'));
                $('#wppoppop-rcpt-email').text($btn.data('email'));
                $('#wppoppop-rcpt-popup').text($btn.data('popup'));
                $('#wppoppop-rcpt-date').text($btn.data('date'));
                $('#wppoppop-rcpt-gateway').text($btn.data('gateway'));
                $('#wppoppop-rcpt-status').text($btn.data('status'));
                
                var formatted = '$' + $btn.data('amount') + ' ' + $btn.data('currency');
                $('#wppoppop-rcpt-amount').text(formatted);
                $('#wppoppop-rcpt-total').text(formatted);

                $('#wppoppop-receipt-modal').css('display', 'flex');
            });
        },

        bindDismissal: function() {
            $('#wppoppop-receipt-close, #wppoppop-rcpt-done').on('click', function() {
                $('#wppoppop-receipt-modal').hide();
            });

            $('#wppoppop-receipt-modal').on('click', function(e) {
                if (e.target === this) {
                    $(this).hide();
                }
            });
        },

        bindPrint: function() {
            $('#wppoppop-rcpt-print').on('click', function() {
                window.print();
            });
        }
    };

    window.WpPopPopPayments.Modal = Modal;
})(window, jQuery);
