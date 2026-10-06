(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPop) return;

        window.WpPopPop.initCore();
        window.WpPopPop.Canvas.init();
        window.WpPopPop.Layers.init();
        window.WpPopPop.Inspector.init();
        window.WpPopPop.Settings.init();
        window.WpPopPop.Modals.init();

        $('#wppoppop-btn-save').on('click', function() {
            window.WpPopPop.IO.save();
        });

        // Load existing record if UID present in query params
        var urlParams = new URLSearchParams(window.location.search);
        var uid = urlParams.get('uid') || (window.wppoppop_vars && window.wppoppop_vars.current_uid);
        if (uid) {
            window.WpPopPop.State.uid = uid;
            window.WpPopPop.IO.load(uid);
        }
    });
})(window, jQuery);
