/**
 * WpPopPop Dashboard: Search Filter
 * Synchronizes real-time text input with WpPopPopDashboardTable.
 */
(function($) {
    'use strict';

    window.WpPopPopDashboardSearch = {
        init: function() {
            this.bindSearch();
        },

        bindSearch: function() {
            var debounceTimer;
            $('#wppoppop-search-input').on('keyup input', function() {
                var query = $(this).val();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    if (window.WpPopPopDashboardTable) {
                        window.WpPopPopDashboardTable.filter(query);
                    }
                }, 150);
            });
        }
    };
})(jQuery);
