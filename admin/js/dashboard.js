(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPopDashboard) return;

        if (window.WpPopPopDashboard.Actions) {
            window.WpPopPopDashboard.Actions.init();
        }

        if (window.WpPopPopDashboard.Import) {
            window.WpPopPopDashboard.Import.init();
        }

        if (window.WpPopPopDashboard.Embed) {
            window.WpPopPopDashboard.Embed.init();
        }

        if (window.WpPopPopDashboard.Search) {
            window.WpPopPopDashboard.Search.init();
        }
    });
})(window, jQuery);
