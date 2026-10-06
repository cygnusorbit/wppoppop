(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Modals = {
        init: function() {
            this.bindEmbed();
            this.bindPreview();
        },

        bindEmbed: function() {
            $('#wppoppop-btn-embed').on('click', function() {
                var uid = window.WpPopPopBuilder.Core.state.uid || 'pop_sample';
                $('#wppoppop-embed-sc').val('[wppoppop uid="' + uid + '"]');
                $('#wppoppop-embed-locker').val('[wppoppop_locker uid="' + uid + '"]Exclusive Locked Content Here[/wppoppop_locker]');
                $('#wppoppop-embed-click').val('<a href="#" class="wppoppop-open-btn" data-target-uid="' + uid + '">Open Popup</a>');
                $('#wppoppop-builder-embed-modal').css('display', 'flex');
            });

            $('#wppoppop-builder-embed-close, #wppoppop-builder-embed-modal').on('click', function(e) {
                if (e.target === this) $('#wppoppop-builder-embed-modal').hide();
            });
        },

        bindPreview: function() {
            $('#wppoppop-btn-preview').on('click', function() {
                var core = window.WpPopPopBuilder.Core;
                var $root = $('#wppoppop-preview-sandbox-root');
                $root.empty();

                var $previewBox = $('#wppoppop-canvas-box').clone();
                $previewBox.find('.ui-resizable-handle').remove();
                $previewBox.find('.wppoppop-canvas-item').removeClass('wppoppop-selected');
                $root.append($previewBox);

                $('#wppoppop-builder-preview-modal').css('display', 'flex');
            });

            $('#wppoppop-builder-preview-close, #wppoppop-builder-preview-modal').on('click', function(e) {
                if (e.target === this) $('#wppoppop-builder-preview-modal').hide();
            });
        }
    };

    window.WpPopPopBuilder.Modals = Modals;
})(window, jQuery);
