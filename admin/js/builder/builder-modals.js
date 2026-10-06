(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Modals = {
        init: function() {
            this.bindEmbedModal();
            this.bindPreviewModal();
        },

        bindEmbedModal: function() {
            $('#wppoppop-btn-embed').on('click', function(e) {
                e.preventDefault();
                var uid = (window.WpPopPopBuilder.Core && window.WpPopPopBuilder.Core.state.uid) || 'POPUP_UID';
                $('#wppoppop-embed-sc').val('[wppoppop uid="' + uid + '"]');
                $('#wppoppop-embed-locker').val('[wppoppop_locker uid="' + uid + '"]Your protected content here[/wppoppop_locker]');
                $('#wppoppop-embed-click').val('<button class="wppoppop-trigger-btn" data-target-uid="' + uid + '">Open Popup</button>');

                $('#wppoppop-builder-embed-modal').css('display', 'flex');
            });

            $('#wppoppop-builder-embed-close, #wppoppop-builder-embed-modal').on('click', function(e) {
                if (e.target === this || $(this).is('#wppoppop-builder-embed-close')) {
                    $('#wppoppop-builder-embed-modal').hide();
                }
            });
        },

        bindPreviewModal: function() {
            $('#wppoppop-btn-preview').on('click', function(e) {
                e.preventDefault();
                var $root = $('#wppoppop-preview-sandbox-root');
                $root.empty();

                var $box = $('#wppoppop-canvas-box').clone().removeAttr('id').css({
                    position: 'relative',
                    boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.5)'
                });

                $box.find('.wppoppop-canvas-item').each(function() {
                    $(this).removeClass('wppoppop-selected ui-draggable ui-resizable ui-draggable-handle');
                    $(this).find('.ui-resizable-handle').remove();
                });

                $root.append($box);
                $('#wppoppop-builder-preview-modal').css('display', 'flex');
            });

            $('#wppoppop-builder-preview-close, #wppoppop-builder-preview-modal').on('click', function(e) {
                if (e.target === this || $(this).is('#wppoppop-builder-preview-close')) {
                    $('#wppoppop-builder-preview-modal').hide();
                }
            });
        }
    };

    window.WpPopPopBuilder.Modals = Modals;
})(window, jQuery);
