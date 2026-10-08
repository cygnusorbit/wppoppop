/**
 * WpPopPop Visual Builder: Layer Properties Inspector Engine
 * Supports Target Canvas Navigation, Typography Alignment, Animation Replay & Full Transparency Swatches
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderInspector = {
        init: function() {
            this.bindTabs();
            this.bindClose();
            this.bindInputs();
        },

        bindTabs: function() {
            $('.wppoppop-insp-tab').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.wppoppop-insp-tab').removeClass('active');
                $(this).addClass('active');

                $('.wppoppop-insp-content').hide();
                $('#insp-tab-' + tab).show();
            });
        },

        bindClose: function() {
            var self = this;
            $('#wppoppop-inspector-close').on('click', function(e) {
                e.preventDefault();
                self.close();
            });
        },

        open: function(id) {
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            var el = elements.find(function(e) { return e.id === id; });
            if (!el) return;

            // Update Selected Element Badge
            $('#insp-element-type-badge').text((el.type || 'ELEMENT').toUpperCase());

            // Switch Element-Specific Panel in Basic Tab
            $('#wppoppop-element-specific-settings .element-panel').hide();
            if (el.type) {
                $('#panel-elem-' + el.type).show();
            }

            // Populate Basic Tab
            $('#prop-layer-name').val(el.name || '');
            $('#prop-pos-top').val(el.top || 0);
            $('#prop-pos-left').val(el.left || 0);
            $('#prop-size-width').val(el.width || 100);
            $('#prop-size-height').val(el.height || 40);
            $('#prop-content, #prop-text-content').val(el.content || '');

            // Target Canvas / Screen Binding
            var targetCanvas = el.goto_canvas || el.goto_screen || 2;
            $('#prop-goto-canvas, #prop-goto-screen, #prop-step-canvas, input[name="prop-goto-screen"]').val(targetCanvas);
            $('#prop-goto-wrap').toggle(el.type === 'step_btn');

            // Populate Style Tab - Typography
            $('#prop-font-family').val(el.fontFamily || 'inherit');
            $('#prop-font-size').val(el.fontSize || '');
            $('#prop-font-weight').val(el.fontWeight || '400');
            $('#prop-text-align').val(el.textAlign || 'left');
            $('#prop-padding').val(el.padding !== undefined ? el.padding : 0);

            // Text Color transparency sync
            var currentColor = el.color || '#000000';
            if (currentColor === 'transparent') {
                $('#prop-text-transparent-btn').addClass('active');
            } else {
                $('#prop-text-transparent-btn').removeClass('active');
                $('#prop-color').val(currentColor);
            }

            // Background Color transparency sync
            var currentBg = el.bgColor || '#ffffff';
            if (currentBg === 'transparent') {
                $('#prop-bg-transparent-btn').addClass('active');
            } else {
                $('#prop-bg-transparent-btn').removeClass('active');
                $('#prop-bg-color').val(currentBg);
            }

            // Border Color transparency sync
            var currentBorder = el.borderColor || '#cbd5e1';
            if (currentBorder === 'transparent') {
                $('#prop-border-transparent-btn').addClass('active');
            } else {
                $('#prop-border-transparent-btn').removeClass('active');
                $('#prop-border-color').val(currentBorder);
            }

            $('#prop-border-style').val(el.borderStyle || 'solid');
            $('#prop-border-radius').val(el.borderRadius || 4);
            $('#prop-border-width').val(el.borderWidth !== undefined ? el.borderWidth : 1);
            $('#prop-opacity').val(el.opacity !== undefined ? el.opacity : 1);
            $('#prop-anim-effect').val(el.animEffect || 'none');
            $('#prop-box-shadow').val(el.boxShadow || 'none');

            // Populate Logic Tab
            $('#prop-display-token').val('{' + (el.fieldName || el.id) + '}');
            $('#prop-action-url').val(el.actionUrl || '');
            $('#prop-action-blank').prop('checked', !!el.actionBlank);
            $('#prop-action-close').val(el.actionClose || 'none');
            $('#prop-action-js').val(el.actionJs || '');

            // Open Frame Docking
            $('.wppoppop-main-frame, .wppoppop-builder-wrap').addClass('panel-open');
            $('#wppoppop-inspector-drawer').show();
        },

        close: function() {
            $('.wppoppop-main-frame, .wppoppop-builder-wrap').removeClass('panel-open');
            $('#wppoppop-inspector-drawer').hide();
        },

        syncCoordinates: function(el) {
            $('#prop-pos-top').val(el.top);
            $('#prop-pos-left').val(el.left);
            $('#prop-size-width').val(el.width);
            $('#prop-size-height').val(el.height);
        },

        bindInputs: function() {
            var self = this;
            var getActiveEl = function() {
                var activeId = window.WpPopPopBuilderCore.state.activeId;
                if (!activeId) return null;
                var elements = window.WpPopPopBuilderCanvas.getActiveElements();
                return elements.find(function(e) { return e.id === activeId; });
            };

            // Basic Tab Live Sync
            $('#prop-layer-name').on('input', function() {
                var el = getActiveEl();
                if (el) {
                    el.name = $(this).val();
                    if (window.WpPopPopBuilderLayers) window.WpPopPopBuilderLayers.renderLayers();
                }
            });

            $('#prop-pos-top, #prop-pos-left, #prop-size-width, #prop-size-height').on('input change', function() {
                var el = getActiveEl();
                if (!el) return;
                el.top = parseInt($('#prop-pos-top').val(), 10) || 0;
                el.left = parseInt($('#prop-pos-left').val(), 10) || 0;
                el.width = parseInt($('#prop-size-width').val(), 10) || 100;
                el.height = parseInt($('#prop-size-height').val(), 10) || 40;

                $('#el-' + el.id).css({
                    top: el.top + 'px',
                    left: el.left + 'px',
                    width: el.width + 'px',
                    height: el.height + 'px'
                });
            });

            $('#prop-content, #prop-text-content').on('input', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $(this).val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            // Target Canvas Binding
            $('#prop-goto-canvas, #prop-goto-screen, #prop-step-canvas').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    var val = parseInt($(this).val(), 10) || 2;
                    el.goto_canvas = val;
                    el.goto_screen = val;
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            // Checker Button: TEXT COLOR -> color: transparent;
            $('#prop-text-transparent-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el) return;
                el.color = 'transparent';
                $('#prop-text-transparent-btn').addClass('active');

                var $node = $('#el-' + el.id);
                $node.css('color', 'transparent');
                $node.find('*').css('color', 'transparent');

                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
                if (window.WpPopPopBuilderCore) {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });

            // Checker Button: BG COLOR -> background-color: transparent;
            $('#prop-bg-transparent-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el) return;
                el.bgColor = 'transparent';
                $('#prop-bg-transparent-btn').addClass('active');

                var $node = $('#el-' + el.id);
                $node.css('background-color', 'transparent');
                $node.find('button').css('background', 'transparent');

                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
                if (window.WpPopPopBuilderCore) {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });

            // Checker Button: BORDER COLOR -> border-color: transparent;
            $('#prop-border-transparent-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el) return;
                el.borderColor = 'transparent';
                $('#prop-border-transparent-btn').addClass('active');

                var $node = $('#el-' + el.id);
                $node.css('border-color', 'transparent');

                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
                if (window.WpPopPopBuilderCore) {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });

            // Animation Play / Replay button
            $('#prop-anim-replay-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (el && window.WpPopPopBuilderCanvas) {
                    var effect = $('#prop-anim-effect').val() || el.animEffect;
                    window.WpPopPopBuilderCanvas.playAnimation(el.id, effect);
                }
            });

            // Style Tab Live Sync
            $('#prop-font-family, #prop-font-size, #prop-font-weight, #prop-text-align, #prop-padding, #prop-color, #prop-bg-color, #prop-border-color, #prop-border-style, #prop-border-radius, #prop-border-width, #prop-opacity, #prop-anim-effect, #prop-box-shadow').on('input change', function() {
                var el = getActiveEl();
                if (!el) return;
                el.fontFamily = $('#prop-font-family').val();
                el.fontSize = $('#prop-font-size').val();
                el.fontWeight = $('#prop-font-weight').val();
                el.textAlign = $('#prop-text-align').val();
                el.padding = parseInt($('#prop-padding').val(), 10) || 0;

                // Color inputs reset handlers
                if ($(this).attr('id') === 'prop-color') {
                    el.color = $('#prop-color').val();
                    $('#prop-text-transparent-btn').removeClass('active');
                } else if ($(this).attr('id') === 'prop-bg-color') {
                    el.bgColor = $('#prop-bg-color').val();
                    $('#prop-bg-transparent-btn').removeClass('active');
                } else if ($(this).attr('id') === 'prop-border-color') {
                    el.borderColor = $('#prop-border-color').val();
                    $('#prop-border-transparent-btn').removeClass('active');
                }

                el.borderStyle = $('#prop-border-style').val();
                el.borderRadius = $('#prop-border-radius').val();
                el.borderWidth = $('#prop-border-width').val();
                el.opacity = $('#prop-opacity').val();
                
                var prevAnim = el.animEffect;
                el.animEffect = $('#prop-anim-effect').val();
                el.boxShadow = $('#prop-box-shadow').val();

                window.WpPopPopBuilderCanvas.renderCanvas();

                // If animation changed, trigger preview
                if ($(this).attr('id') === 'prop-anim-effect' && el.animEffect !== 'none' && el.animEffect !== prevAnim) {
                    window.WpPopPopBuilderCanvas.playAnimation(el.id, el.animEffect);
                }
            });

            // Logic Tab Live Sync
            $('#prop-action-url, #prop-action-blank, #prop-action-close, #prop-action-js').on('input change', function() {
                var el = getActiveEl();
                if (!el) return;
                el.actionUrl = $('#prop-action-url').val();
                el.actionBlank = $('#prop-action-blank').is(':checked');
                el.actionClose = $('#prop-action-close').val();
                el.actionJs = $('#prop-action-js').val();
            });
        }
    };

    // Backward-compatible alias
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};
    window.WpPopPopBuilder.Inspector = window.WpPopPopBuilderInspector;
})(jQuery);
