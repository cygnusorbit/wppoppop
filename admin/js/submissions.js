(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPopSubmissions) return;

        if (window.WpPopPopSubmissions.Search) {
            window.WpPopPopSubmissions.Search.init();
        }

        if (window.WpPopPopSubmissions.Actions) {
            window.WpPopPopSubmissions.Actions.init();
        }

        if (window.WpPopPopSubmissions.Modal) {
            window.WpPopPopSubmissions.Modal.init();
        }
    });
})(window, jQuery);
