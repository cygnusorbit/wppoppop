(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPopBuilder) return;

        if (window.WpPopPopBuilder.Core) {
            window.WpPopPopBuilder.Core.init();
        }

        if (window.WpPopPopBuilder.Canvas) {
            window.WpPopPopBuilder.Canvas.init();
        }

        if (window.WpPopPopBuilder.Layers) {
            window.WpPopPopBuilder.Layers.init();
        }

        if (window.WpPopPopBuilder.Inspector) {
            window.WpPopPopBuilder.Inspector.init();
        }

        if (window.WpPopPopBuilder.Settings) {
            window.WpPopPopBuilder.Settings.init();
        }

        if (window.WpPopPopBuilder.Modals) {
            window.WpPopPopBuilder.Modals.init();
        }

        if (window.WpPopPopBuilder.IO) {
            window.WpPopPopBuilder.IO.init();
        }
    });
})(window, jQuery);
