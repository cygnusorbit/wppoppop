(function(window, $) {
    'use strict';

    $(document).ready(function() {
        if (!window.WpPopPopFront) return;

        var Triggers     = window.WpPopPopFront.Triggers;
        var Display      = window.WpPopPopFront.Display;
        var Gamification = window.WpPopPopFront.Gamification;
        var Logic        = window.WpPopPopFront.Logic;
        var Submit       = window.WpPopPopFront.Submit;

        // Initialize every active popup on the page
        $('.wppoppop-overlay, .wppoppop-ribbon-bar, .wppoppop-inline-container').each(function() {
            var $popup = $(this);
            var uid = $popup.data('uid');
            var rawConfig = $popup.attr('data-config');
            var config = {};

            try {
                config = typeof rawConfig === 'string' ? JSON.parse(rawConfig) : (rawConfig || {});
            } catch(e) {
                config = {};
            }

            // Check Frequency Capping & Cookie Rules
            if (!Display.canShow(uid, config) && !$popup.hasClass('wppoppop-inline-container')) {
                return;
            }

            // Bind Lifecycle Events
            Display.bindEvents($popup, config);

            // Initialize Gamification
            if (Gamification) Gamification.init($popup, config);

            // Initialize Dynamic Tokens & Quiz Logic
            if (Logic) Logic.init($popup, config);

            // Initialize Form Submissions & Payments
            if (Submit) Submit.init($popup, config);

            // Arm Triggers
            if (Triggers && !$popup.hasClass('wppoppop-inline-container')) {
                Triggers.init($popup[0], config, function() {
                    Display.show($popup, config);
                });
            }
        });
    });
})(window, jQuery);
