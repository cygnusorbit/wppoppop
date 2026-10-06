/**
 * WpPopPop Dashboard: Master Coordinator
 */
(function($) {
    'use strict';

    window.WpPopPopDashboard = {
        init: function() {
            if (window.WpPopPopDashboardTable) {
                window.WpPopPopDashboardTable.init();
            }
            if (window.WpPopPopDashboardActions) {
                window.WpPopPopDashboardActions.init();
            }
            if (window.WpPopPopDashboardSearch) {
                window.WpPopPopDashboardSearch.init();
            }
            if (window.WpPopPopDashboardImport) {
                window.WpPopPopDashboardImport.init();
            }
            if (window.WpPopPopDashboardEmbed) {
                window.WpPopPopDashboardEmbed.init();
            }
        }
    };

    $(document).ready(function() {
        window.WpPopPopDashboard.init();
    });
})(jQuery);
