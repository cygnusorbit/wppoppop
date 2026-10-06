(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPopLog) return;

        if (window.WpPopPopLog.Search) {
            window.WpPopPopLog.Search.init();
        }

        if (window.WpPopPopLog.Payload) {
            window.WpPopPopLog.Payload.init();
        }
    });
})(window, jQuery);
