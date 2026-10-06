/**
 * WpPopPop Dashboard: Import Modal Controller
 */
(function($) {
    'use strict';

    window.WpPopPopDashboardImport = {
        init: function() {
            this.bindModal();
        },

        bindModal: function() {
            $(document).on('click', '#wppoppop-import-popup-btn', function(e) {
                e.preventDefault();
                $('#wppoppop-import-modal').fadeIn(200);
            });

            $(document).on('click', '.wppoppop-close-import-modal', function() {$('#wppoppop-import-modal').fadeOut(200);
            });
        }
    };
})(jQuery);
