(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPopSettings) return;

        if (window.WpPopPopSettings.Tabs) {
            window.WpPopPopSettings.Tabs.init();
        }

        if (window.WpPopPopSettings.Save) {
            window.WpPopPopSettings.Save.init();
        }

        if (window.WpPopPopSettings.Tools) {
            window.WpPopPopSettings.Tools.init();
        }
    });
})(window, jQuery);
