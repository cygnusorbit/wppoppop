/**
 * WpPopPop Dashboard: Embed Snippet Modal Controller
 */
(function($) {
    'use strict';

    window.WpPopPopDashboardEmbed = {
        init: function() {
            this.bindModal();
        },

        bindModal: function() {
            $(document).on('click', '.wppoppop-embed-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                var title = $(this).attr('data-title');

                $('#wppoppop-embed-popup-title').text(title);
                $('#wppoppop-embed-shortcode-val').val('[wppoppop uid="' + uid + '"]');
                $('#wppoppop-embed-locker-val').val('[wppoppop_lock uid="' + uid + '"]Hidden Premium Content[/wppoppop_lock]');
                $('#wppoppop-embed-btn-val').val('<button class="wppoppop-open-btn" data-target-uid="' + uid + '">Open Popup</button>');

                $('#wppoppop-dash-embed-modal').fadeIn(200);
            });

            $(document).on('click', '.wppoppop-close-embed-modal', function() {$('#wppoppop-dash-embed-modal').fadeOut(200);
            });
        }
    };
})(jQuery);
