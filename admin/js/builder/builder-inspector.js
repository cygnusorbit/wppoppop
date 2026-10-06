(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Inspector = {
        init: function() {
            this.bindTabs();
            this.bindClose();
            this.bindInputs();
            this.bindEvents();
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

        bindClose: function() {
            var self = this;
            $('#wppoppop-inspector-close').on('click', function() {
                window.WpPopPopBuilder.Core.activeId = null;
                $(document).trigger('builder:element:deselected');
            });
        },

        open: function(el) {
            this.loadElement(el);
            $('.wppoppop-builder-wrap').addClass('panel-open');
            $('#wppoppop-inspector-drawer').addClass('open');
        },

        close: function() {
            $('.wppoppop-builder-wrap').removeClass('panel-open');
            $('#wppoppop-inspector-drawer').removeClass('open');
        },

        bindInputs: function() {
            var Core = window.WpPopPopBuilder.Core;

            function syncLiveProperty(inputSelector, propKey, isNum) {
                $(inputSelector).on('input change keyup', function() {
                    if (!Core.activeId) return;
                    var val = $(this).val();
                    if (isNum) val = parseFloat(val) || 0;
                    var updateObj = {};
                    updateObj[propKey] = val;
                    Core.updateElement(Core.activeId, updateObj);
                });
            }

            syncLiveProperty('#prop-layer-name', 'label', false);
            syncLiveProperty('#prop-pos-top', 'top', true);
            syncLiveProperty('#prop-pos-left', 'left', true);
            syncLiveProperty('#prop-size-width', 'width', true);
            syncLiveProperty('#prop-size-height', 'height', true);
            syncLiveProperty('#prop-content', 'content', false);

            syncLiveProperty('#prop-font-family', 'fontFamily', false);
            syncLiveProperty('#prop-font-size', 'fontSize', true);
            syncLiveProperty('#prop-border-radius', 'borderRadius', true);
            syncLiveProperty('#prop-color', 'color', false);
            syncLiveProperty('#prop-bg-color', 'bgColor', false);
            syncLiveProperty('#prop-opacity', 'opacity', true);
            syncLiveProperty('#prop-anim-effect', 'animEffect', false);

            syncLiveProperty('#prop-action-url', 'actionUrl', false);
            syncLiveProperty('#prop-action-close', 'actionClose', false);
            syncLiveProperty('#prop-action-js', 'actionJs', false);

            $('#prop-action-blank').on('change', function() {
                if (!Core.activeId) return;
                Core.updateElement(Core.activeId, { actionBlank: $(this).is(':checked') });
            });
        },

        bindEvents: function() {
            var self = this;
            var Core = window.WpPopPopBuilder.Core;

            $(document).on('builder:element:moving', function(e, data) {
                if (Core.activeId === data.id) {
                    $('#prop-pos-top').val(data.top);
                    $('#prop-pos-left').val(data.left);
                }
            });

            $(document).on('builder:element:resizing', function(e, data) {
                if (Core.activeId === data.id) {
                    $('#prop-size-width').val(data.width);
                    $('#prop-size-height').val(data.height);
                }
            });

            $(document).on('builder:element:selected', function(e, id) {
                var el = Core.getElementById(id);
                if (el) {
                    self.open(el);
                }
            });

            $(document).on('builder:element:deselected', function() {
                self.close();
            });
        },

        loadElement: function(el) {
            $('#prop-layer-name').val(el.label || '');
            $('#prop-pos-top').val(el.top || 0);
            $('#prop-pos-left').val(el.left || 0);
            $('#prop-size-width').val(el.width || 200);
            $('#prop-size-height').val(el.height || 40);
            $('#prop-content').val(el.content || '');
            $('#prop-font-family').val(el.fontFamily || 'inherit');
            $('#prop-font-size').val(el.fontSize || 14);
            $('#prop-border-radius').val(el.borderRadius || 0);
            $('#prop-color').val(el.color || '#1e293b');
            $('#prop-bg-color').val(el.bgColor || 'transparent');
            $('#prop-opacity').val(el.opacity !== undefined ? el.opacity : 1);
            $('#prop-anim-effect').val(el.animEffect || 'none');
            $('#prop-action-url').val(el.actionUrl || '');
            $('#prop-action-blank').prop('checked', !!el.actionBlank);
            $('#prop-action-close').val(el.actionClose || 'none');
            $('#prop-action-js').val(el.actionJs || '');
        }
    };

    window.WpPopPopBuilder.Inspector = Inspector;
})(window, jQuery);
