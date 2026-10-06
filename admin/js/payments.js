(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPopPayments) return;

        if (window.WpPopPopPayments.Search) {
            window.WpPopPopPayments.Search.init();
        }

        if (window.WpPopPopPayments.Modal) {
            window.WpPopPopPayments.Modal.init();
        }

        if (window.WpPopPopPayments.Export) {
            window.WpPopPopPayments.Export.init();
        }
    });
})(window, jQuery);
