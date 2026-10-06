(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPopLibrary) return;

        if (window.WpPopPopLibrary.Filter) {
            window.WpPopPopLibrary.Filter.init();
        }

        if (window.WpPopPopLibrary.Preview) {
            window.WpPopPopLibrary.Preview.init();
        }

        if (window.WpPopPopLibrary.Import) {
            window.WpPopPopLibrary.Import.init();
        }
    });
})(window, jQuery);
