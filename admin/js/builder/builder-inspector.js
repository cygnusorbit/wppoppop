(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Inspector = {
        activeElement: null,

        init: function() {
            this.bindClose();
            this.bindTabs();
            this.bindInputs();
        },

        open: function(el) {
            this.activeElement = el;
            $('.wppoppop-builder-wrap').addClass('panel-open');
            $('#wppoppop-inspector-drawer').show();

            $('#prop-layer-name').val(el.name || '');
            $('#prop-pos-top').val(el.top);
            $('#prop-pos-left').val(el.left);
            $('#prop-size-width').val(el.width);
            $('#prop-size-height').val(el.height);
            $('#prop-content').val(el.content);

            $('#prop-font-family').val(el.fontFamily || 'inherit');
            $('#prop-font-size').val(el.fontSize || 14);
            $('#prop-color').val(el.color || '#1e293b');
            $('#prop-bg-color').val(el.bgColor || '#ffffff');
            $('#prop-border-radius').val(el.borderRadius || 0);
            $('#prop-opacity').val(el.opacity || 1);
            $('#prop-anim-effect').val(el.animEffect || 'none');

            $('#prop-action-url').val(el.actionUrl || '');
            $('#prop-action-blank').prop('checked', !!el.actionBlank);
            $('#prop-action-close').val(el.actionClose || 'none');
            $('#prop-action-js').val(el.actionJs || '');
        },

        close: function() {
            this.activeElement = null;
            $('.wppoppop-builder-wrap').removeClass('panel-open');
            $('#wppoppop-inspector-drawer').hide();
        },

        bindClose: function() {
            var self = this;
            $('#wppoppop-inspector-close').on('click', function() {
                self.close();
            });
        },

        bindTabs: function() {
            $('.wppoppop-insp-tab').on('click', function() {
                var tab = $(this).data('tab');
                $('.wppoppop-insp-tab').removeClass('active');
                $(this).addClass('active');

                $('.wppoppop-insp-content').hide();
                $('#insp-tab-' + tab).show();
            });
        },

        syncCoords: function(el) {
            if (this.activeElement && this.activeElement.id === el.id) {
                $('#prop-pos-top').val(el.top);
                $('#prop-pos-left').val(el.left);
                $('#prop-size-width').val(el.width);
                $('#prop-size-height').val(el.height);
            }
        },

        bindInputs: function() {
            var self = this;
            var core = window.WpPopPopBuilder.Core;

            $('#prop-layer-name').on('input', function() {
                if (self.activeElement) {
                    self.activeElement.name = $(this).val();
                    if (window.WpPopPopBuilder.Layers) window.WpPopPopBuilder.Layers.renderList();
                }
            });

            $('#prop-pos-top, #prop-pos-left, #prop-size-width, #prop-size-height').on('input', function() {
                if (self.activeElement) {
                    self.activeElement.top = parseInt($('#prop-pos-top').val(), 10) || 0;
                    self.activeElement.left = parseInt($('#prop-pos-left').val(), 10) || 0;
                    self.activeElement.width = parseInt($('#prop-size-width').val(), 10) || 50;
                    self.activeElement.height = parseInt($('#prop-size-height').val(), 10) || 20;

                    if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
                }
            });

            $('#prop-content').on('input', function() {
                if (self.activeElement) {
                    self.activeElement.content = $(this).val();
                    if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
                }
            });

            $('#prop-font-family, #prop-font-size, #prop-color, #prop-bg-color, #prop-border-radius, #prop-opacity, #prop-anim-effect').on('input change', function() {
                if (self.activeElement) {
                    self.activeElement.fontFamily = $('#prop-font-family').val();
                    self.activeElement.fontSize = parseInt($('#prop-font-size').val(), 10) || 14;
                    self.activeElement.color = $('#prop-color').val();
                    self.activeElement.bgColor = $('#prop-bg-color').val();
                    self.activeElement.borderRadius = parseInt($('#prop-border-radius').val(), 10) || 0;
                    self.activeElement.opacity = parseFloat($('#prop-opacity').val()) || 1;
                    self.activeElement.animEffect = $('#prop-anim-effect').val();

                    if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
                }
            });

            $('#prop-action-url, #prop-action-blank, #prop-action-close, #prop-action-js').on('input change', function() {
                if (self.activeElement) {
                    self.activeElement.actionUrl = $('#prop-action-url').val();
                    self.activeElement.actionBlank = $('#prop-action-blank').is(':checked');
                    self.activeElement.actionClose = $('#prop-action-close').val();
                    self.activeElement.actionJs = $('#prop-action-js').val();
                }
            });
        }
    };

    window.WpPopPopBuilder.Inspector = Inspector;
})(window, jQuery);
