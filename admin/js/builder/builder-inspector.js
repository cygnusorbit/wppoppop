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
            $('#wppoppop-inspector-close').on('click', function() {
                window.WpPopPopBuilder.Core.activeId = null;
                $(document).trigger('builder:element:deselected');
            });
        },

        open: function(el) {
            this.populateScreenDropdowns();
            this.populateConditionalFields();
            this.loadElement(el);
            $('.wppoppop-builder-wrap').addClass('panel-open');
            $('#wppoppop-inspector-drawer').addClass('open');
        },

        close: function() {
            $('.wppoppop-builder-wrap').removeClass('panel-open');
            $('#wppoppop-inspector-drawer').removeClass('open');
        },

        populateScreenDropdowns: function() {
            var Core = window.WpPopPopBuilder.Core;
            var $target = $('#prop-target-screen').empty();
            var $condTarget = $('#prop-cond-target-screen').empty();
            var $condFallback = $('#prop-cond-fallback-screen').empty();

            $condFallback.append('<option value="next_screen">Proceed to Next Screen</option>');
            $condFallback.append('<option value="close">Close Popup</option>');

            Core.screens.forEach(function(sc) {
                var opt = '<option value="' + sc.id + '">' + sc.title + '</option>';
                $target.append(opt);
                $condTarget.append(opt);
                $condFallback.append(opt);
            });
        },

        populateConditionalFields: function() {
            var Core = window.WpPopPopBuilder.Core;
            var $fieldSelect = $('#prop-cond-field').empty();

            // Candidate elements used on the current active screen
            var inputTypes = ['email', 'number', 'text', 'select', 'radios', 'checkboxes', 'rating', 'slider', 'date'];
            var screenElements = Core.elements.filter(function(e) {
                return e.screen === Core.currentScreen && inputTypes.indexOf(e.type) !== -1;
            });

            if (screenElements.length === 0) {
                $fieldSelect.append('<option value="">(No input elements on Screen ' + Core.currentScreen + ')</option>');
            } else {
                screenElements.forEach(function(el) {
                    var label = el.label || el.content || el.type;
                    $fieldSelect.append('<option value="' + el.id + '">[' + el.type.toUpperCase() + '] ' + label + '</option>');
                });
            }
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

            syncLiveProperty('#prop-action-close', 'actionClose', false);
            syncLiveProperty('#prop-target-screen', 'actionTargetScreen', true);
            syncLiveProperty('#prop-action-url', 'actionUrl', false);
            syncLiveProperty('#prop-action-js', 'actionJs', false);

            $('#prop-action-blank').on('change', function() {
                if (!Core.activeId) return;
                Core.updateElement(Core.activeId, { actionBlank: $(this).is(':checked') });
            });

            // Conditional Logic Controls
            $('#prop-cond-enable').on('change', function() {
                if (!Core.activeId) return;
                var enabled = $(this).is(':checked');
                Core.updateElement(Core.activeId, { condEnable: enabled });
                $('#prop-cond-box').slideToggle(150, function() {
                    $(this).toggle(enabled);
                });
            });

            syncLiveProperty('#prop-cond-field', 'condField', false);
            syncLiveProperty('#prop-cond-operator', 'condOperator', false);
            syncLiveProperty('#prop-cond-val', 'condVal', false);
            syncLiveProperty('#prop-cond-target-screen', 'condTargetScreen', true);
            syncLiveProperty('#prop-cond-fallback-screen', 'condFallbackScreen', false);

            $('#prop-cond-operator').on('change', function() {
                var op = $(this).val();
                if (op === 'is_empty' || op === 'is_not_empty') {
                    $('#prop-cond-val-wrap').hide();
                } else {
                    $('#prop-cond-val-wrap').show();
                }
            });

            $('#prop-action-close').on('change', function() {
                var act = $(this).val();
                if (act === 'jump_screen' || act === 'next_screen') {
                    $('#prop-target-screen-wrap').show();
                } else {
                    $('#prop-target-screen-wrap').hide();
                }
                if (act === 'redirect') {
                    $('#prop-action-url-wrap').show();
                } else {
                    $('#prop-action-url-wrap').hide();
                }
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

            $(document).on('builder:screens:rendered builder:elements:updated', function() {
                if ($('#wppoppop-inspector-drawer').hasClass('open') && Core.activeId) {
                    self.populateScreenDropdowns();
                    self.populateConditionalFields();
                }
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

            $('#prop-action-close').val(el.actionClose || 'none');
            $('#prop-target-screen').val(el.actionTargetScreen || 2);
            $('#prop-action-url').val(el.actionUrl || '');
            $('#prop-action-blank').prop('checked', !!el.actionBlank);
            $('#prop-action-js').val(el.actionJs || '');

            // Load Conditional Logic State
            var condEnabled = !!el.condEnable;
            $('#prop-cond-enable').prop('checked', condEnabled);
            $('#prop-cond-box').toggle(condEnabled);

            if (el.condField) $('#prop-cond-field').val(el.condField);
            $('#prop-cond-operator').val(el.condOperator || 'equals');
            $('#prop-cond-val').val(el.condVal || '');
            if (el.condTargetScreen) $('#prop-cond-target-screen').val(el.condTargetScreen);
            if (el.condFallbackScreen) $('#prop-cond-fallback-screen').val(el.condFallbackScreen);

            var op = el.condOperator || 'equals';
            $('#prop-cond-val-wrap').toggle(op !== 'is_empty' && op !== 'is_not_empty');

            if (el.actionClose === 'jump_screen' || el.actionClose === 'next_screen') {
                $('#prop-target-screen-wrap').show();
            } else {
                $('#prop-target-screen-wrap').hide();
            }

            if (el.actionClose === 'redirect') {
                $('#prop-action-url-wrap').show();
            } else {
                $('#prop-action-url-wrap').hide();
            }
        }
    };

    window.WpPopPopBuilder.Inspector = Inspector;
})(window, jQuery);
