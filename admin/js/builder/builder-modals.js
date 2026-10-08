/**
 * WpPopPop Visual Builder: Dialog Modals Engine (Embed Codes & Live Sandbox Preview)
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderModals = {
        init: function() {
            this.bindEmbedModal();
            this.bindPreviewModal();
        },

        bindEmbedModal: function() {
            var self = this;
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
        },

        bindPreviewModal: function() {
            var self = this;
            $('#wppoppop-btn-preview').on('click', function(e) {
                e.preventDefault();
                var $sandbox =$('#wppoppop-preview-sandbox-root');
                $sandbox.empty();

                var $clone =$('#wppoppop-canvas-box').clone();
                $clone.removeAttr('id').css({
                    position: 'relative',
                    boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.3)',
                    pointerEvents: 'auto'
                });
                $clone.find('.ui-resizable-handle').remove();$clone.find('.wppoppop-selected').removeClass('wppoppop-selected');
                $sandbox.append($clone);

                $('#wppoppop-builder-preview-modal').css('display', 'flex').fadeIn(150);
            });

            $('#wppoppop-builder-preview-close, #wppoppop-builder-preview-modal').on('click', function(e) {
                if (e.target === this || $(this).is('#wppoppop-builder-preview-close')) {
                    $('#wppoppop-builder-preview-modal').fadeOut(150);
                }
            });
        }
    };
})(jQuery);
