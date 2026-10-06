(function(window, $) {
    'use strict';
    window.WpPopPop = window.WpPopPop || {};

    var Inspector = {
        init: function() {
            this.bindTabs();
            this.bindInputs();
        },

        open: function(el) {
            $('.wppoppop-builder-wrapper').addClass('panel-open');
            this.populate(el);
        },

        close: function() {
            $('.wppoppop-builder-wrapper').removeClass('panel-open');
            $('.wppoppop-canvas-layer').removeClass('wppoppop-layer-selected');
            window.WpPopPop.State.selectedId = null;
        },

        populate: function(el) {
            // Basic Tab
            $('#wppoppop-prop-name').val(el.name);
            $('#wppoppop-prop-top').val(el.top);
            $('#wppoppop-prop-left').val(el.left);
            $('#wppoppop-prop-width').val(el.width);
            $('#wppoppop-prop-height').val(el.height);
            $('#wppoppop-prop-content').val(el.content || '');
            $('#wppoppop-prop-goto').val(el.goto_screen || 2);
            $('#wppoppop-prop-click-act').val(el.click_action || 'none');

            // Style Tab
            $('#wppoppop-prop-font').val(el.font_family || 'inherit');
            $('#wppoppop-prop-fontsize').val(el.font_size || 16);
            $('#wppoppop-prop-color').val(el.color || '#1e293b');
            $('#wppoppop-prop-bgcolor').val(el.bg_color || '#2271b1');
            $('#wppoppop-prop-radius').val(el.border_radius || 4);
            $('#wppoppop-prop-anim').val(el.anim || 'none');

            // Logic Tab
            $('#wppoppop-prop-req').prop('checked', !!el.required);
            $('#wppoppop-prop-fieldname').val(el.field_name || '');
            $('#wppoppop-prop-options').val(Array.isArray(el.options) ? el.options.join('\n') : '');
        },

        syncCoords: function(el) {
            $('#wppoppop-prop-top').val(el.top);
            $('#wppoppop-prop-left').val(el.left);
        },

        syncSize: function(el) {
            $('#wppoppop-prop-width').val(el.width);
            $('#wppoppop-prop-height').val(el.height);
        },

        bindTabs: function() {
            var self = this;
            $('.wppoppop-prop-tab').on('click', function() {
                var tab = $(this).data('tab');
                $('.wppoppop-prop-tab').removeClass('active');
                $(this).addClass('active');
                $('.wppoppop-prop-section').hide();
                $('.wppoppop-prop-section[data-section="' + tab + '"]').show();
            });

            $('#wppoppop-property-close').on('click', function() {
                self.close();
            });
        },

        bindInputs: function() {
            var self = this;
            function getActiveEl() {
                var id = window.WpPopPop.State.selectedId;
                return window.WpPopPop.State.elements.find(function(item) { return item.id === id; });
            }

            $('#wppoppop-prop-name').on('input', function() {
                var el = getActiveEl();
                if (el) {
                    el.name = $(this).val();
                    window.WpPopPop.Layers.renderList();
                }
            });

            $('#wppoppop-prop-top, #wppoppop-prop-left').on('input', function() {
                var el = getActiveEl();
                if (el) {
                    el.top = parseInt($('#wppoppop-prop-top').val(), 10) || 0;
                    el.left = parseInt($('#wppoppop-prop-left').val(), 10) || 0;
                    $('#' + el.id).css({ top: el.top + 'px', left: el.left + 'px' });
                }
            });

            $('#wppoppop-prop-width, #wppoppop-prop-height').on('input', function() {
                var el = getActiveEl();
                if (el) {
                    el.width = parseInt($('#wppoppop-prop-width').val(), 10) || 100;
                    el.height = parseInt($('#wppoppop-prop-height').val(), 10) || 30;
                    $('#' + el.id).css({ width: el.width + 'px', height: el.height + 'px' });
                }
            });

            $('#wppoppop-prop-content, #wppoppop-prop-font, #wppoppop-prop-fontsize, #wppoppop-prop-color, #wppoppop-prop-bgcolor, #wppoppop-prop-radius').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#wppoppop-prop-content').val();
                    el.font_family = $('#wppoppop-prop-font').val();
                    el.font_size = parseInt($('#wppoppop-prop-fontsize').val(), 10) || 16;
                    el.color = $('#wppoppop-prop-color').val();
                    el.bg_color = $('#wppoppop-prop-bgcolor').val();
                    el.border_radius = parseInt($('#wppoppop-prop-radius').val(), 10) || 0;
                    $('#' + el.id).html(window.WpPopPop.Canvas.getElementHtml(el));
                }
            });

            $('#wppoppop-prop-goto, #wppoppop-prop-click-act, #wppoppop-prop-anim, #wppoppop-prop-req, #wppoppop-prop-fieldname, #wppoppop-prop-options').on('change input', function() {
                var el = getActiveEl();
                if (el) {
                    el.goto_screen = parseInt($('#wppoppop-prop-goto').val(), 10) || 2;
                    el.click_action = $('#wppoppop-prop-click-act').val();
                    el.anim = $('#wppoppop-prop-anim').val();
                    el.required = $('#wppoppop-prop-req').is(':checked');
                    el.field_name = $('#wppoppop-prop-fieldname').val();
                    el.options = $('#wppoppop-prop-options').val().split('\n').filter(Boolean);
                }
            });
        }
    };

    window.WpPopPop.Inspector = Inspector;
})(window, jQuery);
