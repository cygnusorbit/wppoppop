(function(window, $) {
    'use strict';
    window.WpPopPopLibrary = window.WpPopPopLibrary || {};

    var Preview = {
        activeConfig: null,

        init: function() {
            this.bindCardPreview();
            this.bindModalDismissal();
        },

        bindCardPreview: function() {
            var self = this;
            $('.wppoppop-tpl-preview-btn').on('click', function() {
                var $btn = $(this);
                $('#wppoppop-lib-modal-title').text($btn.data('title'));
                $('#wppoppop-lib-modal-desc').text($btn.data('desc'));
                $('#wppoppop-lib-modal-dims').text($btn.data('width') + ' x ' + $btn.data('height') + ' px');
                $('#wppoppop-lib-modal-elements').text($btn.data('elements') + ' Layers');
                self.activeConfig = $btn.data('config');

                $('#wppoppop-lib-preview-modal').css('display', 'flex');
            });
        },

        bindModalDismissal: function() {
            var self = this;
            $('#wppoppop-lib-modal-close, #wppoppop-lib-modal-cancel').on('click', function() {
                $('#wppoppop-lib-preview-modal').hide();
                self.activeConfig = null;
            });

            $('#wppoppop-lib-preview-modal').on('click', function(e) {
                if (e.target === this) {
                    $(this).hide();
                    self.activeConfig = null;
                }
            });
        }
    };

    window.WpPopPopLibrary.Preview = Preview;
})(window, jQuery);
