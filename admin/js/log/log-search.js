(function(window, $) {
    'use strict';
    window.WpPopPopLog = window.WpPopPopLog || {};

    var Search = {
        init: function() {
            var $input = $('#wppoppop-log-search');
            if (!$input.length) return;

            $input.on('input', function() {
                var term = $(this).val().toLowerCase().trim();
                $('.wppoppop-log-row').each(function() {
                    var type = $(this).data('type') || '';
                    var msg  = $(this).data('msg') || '';
                    if (type.indexOf(term) !== -1 || msg.indexOf(term) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        }
    };

    window.WpPopPopLog.Search = Search;
})(window, jQuery);
