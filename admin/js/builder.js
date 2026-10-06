(function(window, $) {
    'use strict';

    $(document).ready(function() {
        // Tag active builder body and remove conflicting WordPress admin footer
        $('body').addClass('wppoppop-builder-active');
        $('#wpfooter').remove();

        if (!window.WpPopPopBuilder) {
            console.error('WpPopPopBuilder subsystem not initialized.');
            return;
        }

        var Core      = window.WpPopPopBuilder.Core;
        var Canvas    = window.WpPopPopBuilder.Canvas;
        var Layers    = window.WpPopPopBuilder.Layers;
        var Inspector = window.WpPopPopBuilder.Inspector;
        var Settings  = window.WpPopPopBuilder.Settings;
        var Modals    = window.WpPopPopBuilder.Modals;
        var IO        = window.WpPopPopBuilder.IO;

        if (Core && typeof Core.init === 'function') {
            Core.init(window.wppoppop_initial_config || {});
        }
        if (Canvas && typeof Canvas.init === 'function') {
            Canvas.init();
        }
        if (Layers && typeof Layers.init === 'function') {
            Layers.init();
        }
        if (Inspector && typeof Inspector.init === 'function') {
            Inspector.init();
        }
        if (Settings && typeof Settings.init === 'function') {
            Settings.init();
        }
        if (Modals && typeof Modals.init === 'function') {
            Modals.init();
        }
        if (IO && typeof IO.init === 'function') {
            IO.init();
        }
    });
})(window, jQuery);
