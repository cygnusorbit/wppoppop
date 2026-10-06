(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Inspector = {
        init: function() {
            this.bindClose();
            this.bindTabs();
            this.bindInputs();
        },

        open: function(el) {
            $('.wppoppop-builder-wrap').addClass('panel-open');
            this.populate(el);
        },

        close: function() {
            $('.wppoppop-builder-wrap').removeClass('panel-open');
            $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
            if (window.WpPopPopBuilder.Core) {
                window.WpPopPopBuilder.Core.state.activeId = null;
            }
            if (window.WpPopPopBuilder.Layers) {
                $('.wppoppop-layer-item').removeClass('active');
            }
        },

        populate: function(el) {
            if (!el) return;
            // Basic Tab
            $('#prop-layer-name').val(el.name || (el.type.toUpperCase() + ' Layer'));
            $('#prop-pos-top').val(Math.round(el.top || 0));
            $('#prop-pos-left').val(Math.round(el.left || 0));
            $('#prop-size-width').val(Math.round(el.width || 200));
            $('#prop-size-height').val(Math.round(el.height || 40));
            $('#prop-content').val(el.content || '');

            // Style Tab
            $('#prop-font-family').val(el.fontFamily || 'inherit');
            $('#prop-font-size').val(el.fontSize || 14);
            $('#prop-color').val(el.color || '#1e293b');
            $('#prop-bg-color').val(el.bgColor || '#ffffff');
            $('#prop-border-radius').val(el.borderRadius || 0);
            $('#prop-opacity').val(el.opacity !== undefined ? el.opacity : 1);
            $('#prop-anim-effect').val(el.animEffect || 'none');

            // Logic Tab
            $('#prop-action-url').val(el.actionUrl || '');
            $('#prop-action-blank').prop('checked', !!el.actionBlank);
            $('#prop-action-close').val(el.actionClose || 'none');
            $('#prop-action-js').val(el.actionJs || '');
        },

        syncCoords: function(el) {
            if (!el) return;
            $('#prop-pos-top').val(Math.round(el.top || 0));
            $('#prop-pos-left').val(Math.round(el.left || 0));
            $('#prop-size-width').val(Math.round(el.width || 0));
            $('#prop-size-height').val(Math.round(el.height || 0));
        },

        bindClose: function() {
            var self = this;
            $('#wppoppop-inspector-close').on('click', function(e) {
                e.preventDefault();
                self.close();
            });
        },

        bindTabs: function() {
            $('.wppoppop-insp-tab').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.wppoppop-insp-tab').removeClass('active').css({ color: '#94a3b8', background: 'transparent' });
                $(this).addClass('active').css({ color: '#ffffff', background: '#1e293b' });

                $('.wppoppop-insp-content').hide();
                $('#insp-tab-' + tab).show();
            });
        },

        bindInputs: function() {
            var self = this;
            function getActive() {
                var core = window.WpPopPopBuilder.Core;
                if (!core || !core.state.activeId) return null;
                return core.getElementById(core.state.activeId);
            }

            $('#prop-layer-name').on('input', function() {
                var el = getActive();
                if (el) {
                    el.name = $(this).val();
                    if (window.WpPopPopBuilder.Layers) window.WpPopPopBuilder.Layers.renderList();
                }
            });

            $('#prop-pos-top, #prop-pos-left, #prop-size-width, #prop-size-height').on('input change', function() {
                var el = getActive();
                if (el) {
                    el.top = parseInt($('#prop-pos-top').val(), 10) || 0;
                    el.left = parseInt($('#prop-pos-left').val(), 10) || 0;
                    el.width = parseInt($('#prop-size-width').val(), 10) || 40;
                    el.height = parseInt($('#prop-size-height').val(), 10) || 20;

                    var $c = $('#canvas-' + el.id);
                    $c.css({
                        top: el.top + 'px',
                        left: el.left + 'px',
                        width: el.width + 'px',
                        height: el.height + 'px'
                    });
                }
            });

            $('#prop-content').on('input', function() {
                var el = getActive();
                if (el) {
                    el.content = $(this).val();
                    var $c = $('#canvas-' + el.id);
                    if (window.WpPopPopBuilder.Canvas) {
                        $c.html(window.WpPopPopBuilder.Canvas.getPreviewMarkup(el));
                    }
                }
            });

            $('#prop-font-family, #prop-font-size, #prop-color, #prop-bg-color, #prop-border-radius, #prop-opacity').on('input change', function() {
                var el = getActive();
                if (el) {
                    el.fontFamily = $('#prop-font-family').val();
                    el.fontSize = parseInt($('#prop-font-size').val(), 10) || 14;
                    el.color = $('#prop-color').val();
                    el.bgColor = $('#prop-bg-color').val();
                    el.borderRadius = parseInt($('#prop-border-radius').val(), 10) || 0;
                    el.opacity = parseFloat($('#prop-opacity').val()) || 1;

                    var $c = $('#canvas-' + el.id);
                    $c.css({
                        fontFamily: el.fontFamily,
                        fontSize: el.fontSize + 'px',
                        color: el.color,
                        background: el.bgColor,
                        borderRadius: el.borderRadius + 'px',
                        opacity: el.opacity
                    });
                }
            });

            $('#prop-anim-effect').on('change', function() {
                var el = getActive();
                if (el) {
                    el.animEffect = $(this).val();
                }
            });

            $('#prop-action-url, #prop-action-blank, #prop-action-close, #prop-action-js').on('input change', function() {
                var el = getActive();
                if (el) {
                    el.actionUrl = $('#prop-action-url').val();
                    el.actionBlank = $('#prop-action-blank').is(':checked');
                    el.actionClose = $('#prop-action-close').val();
                    el.actionJs = $('#prop-action-js').val();
                }
            });
        }
    };

    window.WpPopPopBuilder.Inspector = Inspector;
})(window, jQuery);
