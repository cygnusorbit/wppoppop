/**
 * WpPopPop Visual Builder: Layer Settings Inspector Controller
 * Hydrates 26 Elements, Vector Shapes, Spacing/Padding, Typography & Swatches
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
            var self = this;
            $(document).on('click', '.wppoppop-insp-tab', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                self.switchTab(tab);
            });
        },

        switchTab: function(tab) {
            $('.wppoppop-insp-tab').removeClass('active').css({
                borderBottomColor: 'transparent',
                color: '#94a3b8'
            });
            $('.wppoppop-insp-tab[data-tab="' + tab + '"]').addClass('active').css({
                borderBottomColor: '#38bdf8',
                color: '#38bdf8'
            });

            $('.wppoppop-tab-pane').hide();
            $('#insp-tab-' + tab).show();
        },

        bindClose: function() {
            var self = this;
            $('#wppoppop-inspector-close').on('click', function(e) {
                e.preventDefault();
                self.close();
            });
        },

        open: function(id) {
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state.canvases) return;
            var cur = core.state.currentCanvas || 1;
            var elements = core.state.canvases[cur] || [];
            var el = elements.find(function(item) { return String(item.id) === String(id); });
            if (!el) return;

            // Coordinates & Layer Name
            $('#prop-name').val(el.name || (el.type ? el.type.toUpperCase() : 'Layer'));
            $('#prop-left').val(el.left || 0);
            $('#prop-top').val(el.top || 0);
            $('#prop-width').val(el.width || 180);
            $('#prop-height').val(el.height || 42);

            // Hide all sub-panels
            $('.wppoppop-elem-panel').hide();

            // Element-specific hydration
            if (el.type === 'shape') {
                $('#panel-elem-shape').show();
                $('#prop-shape-preset').val(el.shapePreset || 'circle');

                // Shape Fill & Transparency Swatch
                var fill = el.shapeFill || '#3b82f6';
                if (fill === 'transparent') {
                    $('#prop-shape-fill-transparent-btn').addClass('active');
                    $('#prop-shape-fill').val('#ffffff');
                } else {
                    $('#prop-shape-fill-transparent-btn').removeClass('active');
                    $('#prop-shape-fill').val(fill);
                }

                // Shape Stroke & Transparency Swatch
                var stroke = el.shapeStroke !== undefined ? el.shapeStroke : '#1d4ed8';
                if (stroke === 'transparent') {
                    $('#prop-shape-stroke-transparent-btn').addClass('active');
                    $('#prop-shape-stroke').val('#ffffff');
                } else {
                    $('#prop-shape-stroke-transparent-btn').removeClass('active');
                    $('#prop-shape-stroke').val(stroke);
                }

                $('#prop-shape-stroke-width').val(el.shapeStrokeWidth !== undefined ? el.shapeStrokeWidth : 2);
                $('#prop-shape-rotate').val(el.shapeRotate || 0);
            } else if (el.type === 'title') {
                $('#panel-elem-title').show();
                $('#prop-title-text').val(el.content || 'Headline');
            } else if (el.type === 'paragraph') {
                $('#panel-elem-paragraph').show();
                $('#prop-paragraph-text').val(el.content || 'Body copy...');
            } else if (el.type === 'textfield') {
                $('#panel-elem-textfield').show();
                $('#prop-textfield-placeholder').val(el.content || 'Enter text...');
            } else {
                $('#panel-elem-general').show();
                $('#prop-content').val(el.content || '');
            }

            // Typography & Spacing
            $('#prop-font-size').val(el.fontSize || 14);
            $('#prop-font-weight').val(el.fontWeight || '400');
            $('#prop-line-height').val(el.lineHeight || 1.4);
            $('#prop-letter-spacing').val(el.letterSpacing || 0);
            $('#prop-text-align').val(el.textAlign || 'left');
            // Discrete 4-way padding hydration
            var pTop = el.paddingTop !== undefined ? el.paddingTop : (el.padding_top !== undefined ? el.padding_top : (el.padding !== undefined ? el.padding : 0));
            var pRight = el.paddingRight !== undefined ? el.paddingRight : (el.padding_right !== undefined ? el.padding_right : (el.padding !== undefined ? el.padding : 0));
            var pBottom = el.paddingBottom !== undefined ? el.paddingBottom : (el.padding_bottom !== undefined ? el.padding_bottom : (el.padding !== undefined ? el.padding : 0));
            var pLeft = el.paddingLeft !== undefined ? el.paddingLeft : (el.padding_left !== undefined ? el.padding_left : (el.padding !== undefined ? el.padding : 0));
            $('#prop-padding-top').val(pTop);
            $('#prop-padding-right').val(pRight);
            $('#prop-padding-bottom').val(pBottom);
            $('#prop-padding-left').val(pLeft);
            $('#prop-padding').val(pTop);

            // Content tab animation hydration
            var animEff = el.anim_appearance || el.animation || el.animEffect || el.anim_effect || 'none';
            var animDur = el.anim_duration !== undefined ? el.anim_duration : (el.duration !== undefined ? el.duration : 1000);
            var animDel = el.anim_delay !== undefined ? el.anim_delay : (el.delay !== undefined ? el.delay : 0);
            var animDis = el.anim_disappearance || el.exitAnimation || el.animExit || 'none';
            $('#prop-animation, #prop-anim-appearance, #prop-anim-effect').val(animEff);
            $('#prop-anim-duration').val(animDur);
            $('#prop-anim-delay').val(animDel);
            $('#prop-anim-disappearance').val(animDis);
            var padTop = el.paddingTop !== undefined ? el.paddingTop : (el.padding !== undefined ? el.padding : 0);
            var padRight = el.paddingRight !== undefined ? el.paddingRight : (el.padding !== undefined ? el.padding : 0);
            var padBottom = el.paddingBottom !== undefined ? el.paddingBottom : (el.padding !== undefined ? el.padding : 0);
            var padLeft = el.paddingLeft !== undefined ? el.paddingLeft : (el.padding !== undefined ? el.padding : 0);
            $('#prop-padding-top').val(padTop);
            $('#prop-padding-right').val(padRight);
            $('#prop-padding-bottom').val(padBottom);
            $('#prop-padding-left').val(padLeft);

            // Animation
            $('#prop-animation').val(el.animation || 'none');

            // Text Color Swatch
            var color = el.color || '#ffffff';
            if (color === 'transparent') {
                $('#prop-text-transparent-btn').addClass('active');
                $('#prop-color').val('#ffffff');
            } else {
                $('#prop-text-transparent-btn').removeClass('active');
                $('#prop-color').val(color);
            }

            // BG Color Swatch
            var bgColor = el.bgColor || '#2563eb';
            if (bgColor === 'transparent') {
                $('#prop-bg-transparent-btn').addClass('active');
                $('#prop-bg-color').val('#ffffff');
            } else {
                $('#prop-bg-transparent-btn').removeClass('active');
                $('#prop-bg-color').val(bgColor);
            }

            // Target Canvas Navigation
            $('#prop-goto-canvas').val(el.goto_canvas || el.goto_screen || 2);

            $('body').addClass('panel-open');
            $('#wppoppop-inspector-drawer').addClass('open').css('transform', 'translateX(0)');
        },

        close: function() {
            $('body').removeClass('panel-open');
            $('#wppoppop-inspector-drawer').removeClass('open').css('transform', 'translateX(100%)');
        },

        getActiveElement: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state.activeId) return null;
            var cur = core.state.currentCanvas || 1;
            var elements = core.state.canvases[cur] || [];
            return elements.find(function(item) { return String(item.id) === String(core.state.activeId); });
        },

        syncToCanvas: function() {
            if (window.WpPopPopBuilderCanvas) {
                window.WpPopPopBuilderCanvas.renderCanvas();
            }
            if (window.WpPopPopBuilderLayers) {
                window.WpPopPopBuilderLayers.renderLayers();
            }
            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        syncCoordinates: function(el) {
            $('#prop-left').val(Math.round(el.left || 0));
            $('#prop-top').val(Math.round(el.top || 0));
            $('#prop-width').val(Math.round(el.width || 0));
            $('#prop-height').val(Math.round(el.height || 0));
        },

        bindInputs: function() {
            var self = this;

            // Shape Presets & Controls
            $(document).on('change', '#prop-shape-preset', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.shapePreset = $(this).val();
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-shape-fill', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.shapeFill = $(this).val();
                $('#prop-shape-fill-transparent-btn').removeClass('active');
                self.syncToCanvas();
            });

            $(document).on('click', '#prop-shape-fill-transparent-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;
                el.shapeFill = 'transparent';
                $(this).addClass('active');
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-shape-stroke', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.shapeStroke = $(this).val();
                $('#prop-shape-stroke-transparent-btn').removeClass('active');
                self.syncToCanvas();
            });

            $(document).on('click', '#prop-shape-stroke-transparent-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;
                el.shapeStroke = 'transparent';
                $(this).addClass('active');
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-shape-stroke-width', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.shapeStrokeWidth = parseInt($(this).val(), 10) || 0;
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-shape-rotate', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.shapeRotate = parseInt($(this).val(), 10) || 0;
                self.syncToCanvas();
            });

            // Content & Text
            $(document).on('input', '#prop-content, #prop-title-text, #prop-paragraph-text, #prop-textfield-placeholder', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.content = $(this).val();
                self.syncToCanvas();
            });

            // Layer Name
            $(document).on('input', '#prop-name', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.name = $(this).val();
                if (window.WpPopPopBuilderLayers) window.WpPopPopBuilderLayers.renderLayers();
            });

            // Dimensions and Coordinates
            $(document).on('input change', '#prop-left, #prop-top, #prop-width, #prop-height', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.left = parseInt($('#prop-left').val(), 10) || 0;
                el.top = parseInt($('#prop-top').val(), 10) || 0;
                el.width = parseInt($('#prop-width').val(), 10) || 50;
                el.height = parseInt($('#prop-height').val(), 10) || 30;
                self.syncToCanvas();
            });

            // Typography & Spacing Inputs
            $(document).on('input change', '#prop-font-size', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.fontSize = parseInt($(this).val(), 10) || 14;
                self.syncToCanvas();
            });

            $(document).on('change', '#prop-font-weight', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.fontWeight = $(this).val();
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-line-height', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.lineHeight = parseFloat($(this).val()) || 1.4;
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-letter-spacing', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.letterSpacing = parseFloat($(this).val()) || 0;
                self.syncToCanvas();
            });

            $(document).on('change', '#prop-text-align', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.textAlign = $(this).val();
                self.syncToCanvas();
            });

            // Element Padding Input
            $(document).on('input change', '#prop-padding', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.padding = Math.max(0, parseInt($(this).val(), 10) || 0);
                self.syncToCanvas();
            });

            // Animation Selector & Play Button
            
            $('#prop-padding-top, #prop-padding-right, #prop-padding-bottom, #prop-padding-left').off('input.livePad change.livePad').on('input.livePad change.livePad', function() {
                var el = (typeof self.getActiveElement === 'function') ? self.getActiveElement() : ((typeof getActiveEl === 'function') ? getActiveEl() : null);
                if (!el && window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state.activeId) {
                    var cur = window.WpPopPopBuilderCore.state.currentCanvas || 1;
                    var list = window.WpPopPopBuilderCore.state.canvases[cur] || [];
                    el = list.find(function(item) { return String(item.id) === String(window.WpPopPopBuilderCore.state.activeId); });
                }
                if (!el) return;

                var dir = $(this).attr('data-dir') || $(this).attr('id').replace('prop-padding-', '');
                var val = Math.max(0, parseInt($(this).val(), 10) || 0);

                if (dir === 'top') { el.paddingTop = val; el.padding_top = val; }
                if (dir === 'right') { el.paddingRight = val; el.padding_right = val; }
                if (dir === 'bottom') { el.paddingBottom = val; el.padding_bottom = val; }
                if (dir === 'left') { el.paddingLeft = val; el.padding_left = val; }
                el.padding = el.paddingTop || 0;
                $('#prop-padding').val(el.padding);

                var $target = $('#el-' + el.id);
                if ($target.length) {
                    var isControl = ['step_btn', 'submit', 'pay', 'link_btn', 'textfield', 'email', 'number', 'date', 'select'].indexOf(el.type) !== -1;
                    var $inner = $target.find('input, button, select, textarea, a, .wppoppop-btn').first();
                    if (isControl && $inner.length) {
                        $target.css({ 'padding': '0px', 'box-sizing': 'border-box' });
                        $inner.css('padding-' + dir, val + 'px').css('box-sizing', 'border-box');
                    } else {
                        $target.css('padding-' + dir, val + 'px').css('box-sizing', 'border-box');
                    }
                }
                if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });

            $(document).on('change', '#prop-animation', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.animation = $(this).val();
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.playElementAnimation(el.id, el.animation);
                }
                self.syncToCanvas();
            });

            $(document).on('change', '#prop-anim-disappearance', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.anim_disappearance = $(this).val();
                el.exitAnimation = el.anim_disappearance;
                if (window.WpPopPopBuilderCore) window.WpPopPopBuilderCore.pushHistory();
            });

            $(document).on('click', '#prop-anim-play-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;
                var anim = $('#prop-animation').val() || el.animation || 'fade';
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.playElementAnimation(el.id, anim);
                }
            });

            // Colors
            $(document).on('input change', '#prop-color', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.color = $(this).val();
                $('#prop-text-transparent-btn').removeClass('active');
                self.syncToCanvas();
            });

            $(document).on('click', '#prop-text-transparent-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;
                el.color = 'transparent';
                $(this).addClass('active');
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-bg-color', function() {
                var el = self.getActiveElement();
                if (!el) return;
                el.bgColor = $(this).val();
                $('#prop-bg-transparent-btn').removeClass('active');
                self.syncToCanvas();
            });

            $(document).on('click', '#prop-bg-transparent-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;
                el.bgColor = 'transparent';
                $(this).addClass('active');
                self.syncToCanvas();
            });

            // Target Canvas Navigation
            $(document).on('change', '#prop-goto-canvas', function() {
                var el = self.getActiveElement();
                if (!el) return;
                var target = parseInt($(this).val(), 10) || 2;
                el.goto_canvas = target;
                el.goto_screen = target;
                self.syncToCanvas();
            });
        }
    };
})(jQuery);
