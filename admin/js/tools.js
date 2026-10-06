/**
 * WpPopPop Tools: Master Coordinator
 */
(function($) {
    'use strict';

    window.WpPopPopTools = {
        init: function() {
            if (window.WpPopPopToolsSystem) {
                window.WpPopPopToolsSystem.init();
            }
            if (window.WpPopPopToolsDatabase) {
                window.WpPopPopToolsDatabase.init();
            }
            if (window.WpPopPopToolsPortability) {
                window.WpPopPopToolsPortability.init();
            }
        }
    };

    $(document).ready(function() {
        window.WpPopPopTools.init();
    });
})(jQuery);
