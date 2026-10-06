(function(window, $) {
    'use strict';
    window.WpPopPop = window.WpPopPop || {};

    var Modals = {
        init: function() {
            this.bindEmbed();
            this.bindPreview();
        },

        bindEmbed: function() {
            $('#wppoppop-btn-embed').on('click', function() {
                var uid = window.WpPopPop.State.uid || 'POPUP_UID';
                $('#wppoppop-embed-shortcode').val('[wppoppop uid="' + uid + '"]');
                $('#wppoppop-embed-locker').val('[wppoppop_locker uid="' + uid + '"]Exclusive Premium Content Here[/wppoppop_locker]');
                $('#wppoppop-embed-trigger').val('<a href="#" class="wppoppop-open-btn" data-target-uid="' + uid + '">Open Popup</a>');
                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) || '';
                $('#wppoppop-embed-remote').val('<script src="' + ajaxUrl + '?action=wppoppop_remote_embed&uid=' + uid + '"><\/script>');
                $('#wppoppop-embed-modal').fadeIn(200);
            });

            $('#wppoppop-embed-close, #wppoppop-embed-modal').on('click', function(e) {
                if (e.target === this) $('#wppoppop-embed-modal').fadeOut(200);
            });
        },

        bindPreview: function() {
            $('#wppoppop-btn-preview').on('click', function() {
                var $stage = $('#wppoppop-preview-stage');
                $stage.empty();

                var $box = $('#wppoppop-canvas').clone().removeAttr('id').css({
                    position: 'relative',
                    boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.2)'
                });

                $box.find('.wppoppop-canvas-layer').each(function() {
                    $(this).removeClass('wppoppop-layer-selected ui-draggable ui-resizable');
                });

                $stage.append($box);
                $('#wppoppop-preview-modal').fadeIn(200);
            });

            $('#wppoppop-preview-close, #wppoppop-preview-modal').on('click', function(e) {
                if (e.target === this) $('#wppoppop-preview-modal').fadeOut(200);
            });
        }
    };

    window.WpPopPop.Modals = Modals;
})(window, jQuery);
