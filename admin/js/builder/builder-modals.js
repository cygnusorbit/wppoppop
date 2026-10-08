/**
 * WpPopPop Visual Builder: Dialog Modals Engine (Embed Codes & Live Sandbox Preview)
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderModals = {
        init: function() {
            this.bindEmbedModal();
        },

        bindEmbedModal: function() {
            $('#wppoppop-btn-embed').on('click', function(e) {
                e.preventDefault();
                var uid = (window.WpPopPopBuilderIO && window.WpPopPopBuilderIO.getUid()) || $('#wppoppop-builder-uid').val() || 'pop_demo';
                $('#wppoppop-embed-sc').val('[wppoppop uid="' + uid + '"]');
                $('#wppoppop-embed-locker').val('[wppoppop_lock uid="' + uid + '"]Your protected content here...[/wppoppop_lock]');
                $('#wppoppop-embed-click').val('<button type="button" class="wppoppop-trigger-btn" data-target-uid="' + uid + '">Open Popup</button>');
                $('#wppoppop-builder-embed-modal').css('display', 'flex').fadeIn(150);
            });

            $('#wppoppop-builder-embed-close, #wppoppop-builder-embed-modal').on('click', function(e) {
                if (e.target === this || $(this).is('#wppoppop-builder-embed-close')) {
                    $('#wppoppop-builder-embed-modal').fadeOut(150);
                }
            });
        }
    };
})(jQuery);
