(function(window, $) {
    'use strict';
    window.WpPopPopDashboard = window.WpPopPopDashboard || {};

    var Search = {
        init: function() {
            var $input = $('#wppoppop-dash-search');
            if (!$input.length) return;

            $input.on('input', function() {
                var term = $(this).val().toLowerCase().trim();
                $('.wp-list-table tbody tr').each(function() {
                    var text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(term) !== -1);
                });
            });
        }
    };

    window.WpPopPopDashboard.Search = Search;
})(window, jQuery);
