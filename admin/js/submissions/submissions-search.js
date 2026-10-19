(function(window, $) {
    'use strict';
    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};

    var Search = {
        init: function() {
            var $input = $('#wppoppop-subs-search');
            if (!$input.length) return;

            var fire = function() {
                $(document).trigger('wppoppop:subs-search', [$input.val().toLowerCase().trim()]);
            };

            $input.on('input keyup', fire);
            $input.on('keydown', function(e) {
                if (e.which === 13) { e.preventDefault(); fire(); }
            });
            $('#wppoppop-subs-search-btn').on('click', function(e) {
                e.preventDefault();
                fire();
            });
        }
    };

    window.WpPopPopSubmissions.Search = Search;
})(window, jQuery);
