(function(window, $) {
    'use strict';

    $(document).ready(function() {
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

        // Initialize Core State Machine
        if (Core && typeof Core.init === 'function') {
            Core.init(window.wppoppop_initial_config || {});
        }

        // Initialize Canvas & Tool Palette
        if (Canvas && typeof Canvas.init === 'function') {
            Canvas.init();
        }

        // Initialize Floating Layers Panel
        if (Layers && typeof Layers.init === 'function') {
            Layers.init();
        }

        // Initialize Inspector Drawer
        if (Inspector && typeof Inspector.init === 'function') {
            Inspector.init();
        }

        // Initialize Campaign Settings Drawer
        if (Settings && typeof Settings.init === 'function') {
            Settings.init();
        }

        // Initialize Embed & Sandbox Modals
        if (Modals && typeof Modals.init === 'function') {
            Modals.init();
        }

        // Initialize Save & Persistence Pipeline
        if (IO && typeof IO.init === 'function') {
            IO.init();
        }
    });
})(window, jQuery);
