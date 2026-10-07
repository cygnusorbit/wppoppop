/**
 * WpPopPop Dashboard: Master Coordinator
 */
(function($) {
    'use strict';

    window.WpPopPopDashboard = window.WpPopPopDashboard || {};

    window.WpPopPopDashboard.init = function() {
        if (window.WpPopPopDashboardTable && typeof window.WpPopPopDashboardTable.init === 'function') {
            window.WpPopPopDashboardTable.init();
        }

        // Support both direct module and nested namespace
        var actionsModule = window.WpPopPopDashboardActions || (window.WpPopPopDashboard && window.WpPopPopDashboard.Actions);
        if (actionsModule && typeof actionsModule.init === 'function') {
            actionsModule.init();
        }

        if (window.WpPopPopDashboardSearch && typeof window.WpPopPopDashboardSearch.init === 'function') {
            window.WpPopPopDashboardSearch.init();
        }
        if (window.WpPopPopDashboardImport && typeof window.WpPopPopDashboardImport.init === 'function') {
            window.WpPopPopDashboardImport.init();
        }
        if (window.WpPopPopDashboardEmbed && typeof window.WpPopPopDashboardEmbed.init === 'function') {
            window.WpPopPopDashboardEmbed.init();
        }
    };

    $(document).ready(function() {
        window.WpPopPopDashboard.init();
    });
})(jQuery);
