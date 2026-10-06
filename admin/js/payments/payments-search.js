(function(window, $) {
    'use strict';
    window.WpPopPopPayments = window.WpPopPopPayments || {};

    var Search = {
        init: function() {
            var $input = $('#wppoppop-payments-search');
            if (!$input.length) return;

            $input.on('input', function() {
                var term = $(this).val().toLowerCase().trim();
                $('.wppoppop-tx-row').each(function() {
                    var email = $(this).data('email') || '';
                    var txid  = $(this).data('txid') || '';
                    if (email.indexOf(term) !== -1 || txid.indexOf(term) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        }
    };

    window.WpPopPopPayments.Search = Search;
})(window, jQuery);
