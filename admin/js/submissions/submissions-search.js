(function(window, $) {
    'use strict';
    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};

    var Search = {
        init: function() {
            var $input = $('#wppoppop-subs-search');
            if (!$input.length) return;

            $input.on('input', function() {
                var term = $(this).val().toLowerCase().trim();
                $('.wppoppop-sub-row').each(function() {
                    var email = $(this).data('email') || '';
                    var title = $(this).data('title') || '';
                    if (email.indexOf(term) !== -1 || title.indexOf(term) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        }
    };

    window.WpPopPopSubmissions.Search = Search;
})(window, jQuery);
