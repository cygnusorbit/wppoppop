(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPopFront) return;

        if (window.WpPopPopFront.Modal) {
            window.WpPopPopFront.Modal.init();
        }

        if (window.WpPopPopFront.Elements) {
            window.WpPopPopFront.Elements.init();
        }

        if (window.WpPopPopFront.Form) {
            window.WpPopPopFront.Form.init();
        }

        if (window.WpPopPopFront.Triggers) {
            window.WpPopPopFront.Triggers.init();
        }
    });
})(window, jQuery);
