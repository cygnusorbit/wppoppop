/**
 * WpPopPop Visual Builder: Layer Properties Inspector Engine
 * Two-Way Real-Time Synchronization for All 26 Canvas Elements
 */
(function(window, $) {
    'use strict';

    window.WpPopPopBuilderInspector = {
        activeId: null,

        init: function() {
            this.bindTabs();
            this.bindActions();
            this.bindInputs();
            this.populateCustomFonts();
        },

        populateCustomFonts: function() {
            var customFonts = window.wppoppop_custom_fonts || [];
            if (!Array.isArray(customFonts) || customFonts.length === 0) return;

            var $fontSelect = $('#prop-font-family');
            if (!$fontSelect.length) return;

            customFonts.forEach(function(fontName) {
                if ($fontSelect.find('option[value="' + fontName + '"]').length === 0) {
                    $fontSelect.append($('<option>', {
                        value: fontName,
                        text: fontName + ' (Custom)'
                    }));
                }
            });
        },

        bindTabs: function() {
            var self = this;
            $('.wppoppop-insp-tab').off('click.inspTab').on('click.inspTab', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                self.switchTab(tab);
            });
        },

        switchTab: function(tab) {
            $('.wppoppop-insp-tab').removeClass('active').css({
                'background': 'transparent',
                'color': '#94a3b8',
                'border-bottom-color': 'transparent'
            });

            $('.wppoppop-insp-tab[data-tab="' + tab + '"]').addClass('active').css({
                'background': '#0f172a',
                'color': '#38bdf8',
                'border-bottom-color': '#38bdf8'
            });

            $('.wppoppop-insp-tab-content').hide();
            $('#wppoppop-insp-tab-' + tab).show();
        },

        bindActions: function() {
            var self = this;

            $('#wppoppop-inspector-close').off('click.close').on('click.close', function(e) {
                e.preventDefault();
                self.close();
            });

            $('#wppoppop-inspector-del-btn').off('click.del').on('click.del', function(e) {
                e.preventDefault();
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.deleteActiveElement();
                }
            });

            $('#wppoppop-inspector-dup-btn').off('click.dup').on('click.dup', function(e) {
                e.preventDefault();
                if (self.activeId && window.WpPopPopBuilderLayers) {
                    window.WpPopPopBuilderLayers.duplicateLayer(self.activeId);
                }
            });

            $('#prop-anim-test-btn').off('click.testAnim').on('click.testAnim', function(e) {
                e.preventDefault();
                var effect = $('#prop-anim-effect').val();
                if (self.activeId && window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.playAnimation(self.activeId, effect);
                }
            });

            $('#prop-bg-transparent-btn').off('click.trans').on('click.trans', function(e) {
                e.preventDefault();
                $('#prop-bg-color-hex').val('transparent');
                self.applyToActive('bgColor', 'transparent');
            });
        },

        open: function(id) {
            this.activeId = id;
            var el = this.getActiveElement();
            if (!el) return;

            var $drawer = $('#wppoppop-inspector-drawer');
            $drawer.addClass('open').css('transform', 'translateX(0)');
            $('body').addClass('panel-open');

            // Header info
            $('#prop-layer-type-badge').text((el.type || 'element').toUpperCase());
            $('#prop-layer-name').val(el.name || (el.type || 'Element').toUpperCase());

            // Coordinates & dimensions
            $('#prop-x').val(el.left || el.x || 0);
            $('#prop-y').val(el.top || el.y || 0);
            $('#prop-w').val(el.width || el.w || 200);
            $('#prop-h').val(el.height || el.h || 40);

            // Generic content / label
            $('#prop-content').val(el.content || '');

            // Typography
            $('#prop-font-family').val(el.fontFamily || 'inherit');
            $('#prop-font-size').val(el.fontSize || 14);
            $('#prop-font-weight').val(el.fontWeight || '400');
            $('#prop-text-align').val(el.textAlign || 'left');
            $('#prop-line-height').val(el.lineHeight || el.line_height || 1.4);
            $('#prop-letter-spacing').val(el.letterSpacing || el.letter_spacing || 0);

            var color = el.color || '#0f172a';
            $('#prop-color').val(color);
            $('#prop-color-hex').val(color);

            // Background & border
            var bgColor = el.bgColor || '#ffffff';
            if (bgColor !== 'transparent') $('#prop-bg-color').val(bgColor);
            $('#prop-bg-color-hex').val(bgColor);

            $('#prop-border-style').val(el.borderStyle || 'solid');
            $('#prop-border-width').val(el.borderWidth !== undefined ? el.borderWidth : 1);
            $('#prop-border-radius').val(el.borderRadius !== undefined ? el.borderRadius : 4);

            var borderColor = el.borderColor || '#cbd5e1';
            $('#prop-border-color').val(borderColor);
            $('#prop-border-color-hex').val(borderColor);

            $('#prop-box-shadow').val(el.boxShadow || 'none');
            $('#prop-anim-effect').val(el.animEffect || 'none');

            // Logic tokens
            var tokenName = el.fieldName || el.field_name || el.name || el.type || 'field';
            $('#prop-logic-token').text('{' + String(tokenName).toLowerCase().replace(/[^a-z0-9_]/g, '_') + '}');

            // Switch to contextual panels
            this.updateContextualPanels(el);
        },

        close: function() {
            $('#wppoppop-inspector-drawer').removeClass('open').css('transform', 'translateX(100%)');
            $('body').removeClass('panel-open');
            this.activeId = null;
        },

        syncCoordinates: function(el) {
            if (!el || String(el.id) !== String(this.activeId)) return;
            $('#prop-x').val(el.left || el.x || 0);
            $('#prop-y').val(el.top || el.y || 0);
            $('#prop-w').val(el.width || el.w || 200);
            $('#prop-h').val(el.height || el.h || 40);
        },

        updateContextualPanels: function(el) {
            $('.wppoppop-panel-specific').hide();
            var t = (el.type || 'text').toString().toLowerCase().trim();

            if (t === 'title') {
                $('#panel-type-title').show();
                $('#prop-title-tag').val(el.htmlTag || 'h2');
            } else if (t === 'image') {
                $('#panel-type-image').show();
                $('#prop-image-url').val(el.imageUrl || el.content || '');
                $('#prop-image-fit').val(el.imageFit || 'cover');
            } else if (t === 'video') {
                $('#panel-type-video').show();
                $('#prop-video-url').val(el.videoUrl || el.content || '');
                $('#prop-video-autoplay').prop('checked', !!el.videoAutoplay);
            } else if (t === 'shape') {
                $('#panel-type-shape').show();
                $('#prop-shape-preset').val(el.shapePreset || 'circle');
                $('#prop-shape-stroke-width').val(el.shapeStrokeWidth || 0);
                $('#prop-shape-rotate').val(el.shapeRotate || 0);
            } else if (['email', 'textfield', 'number', 'select', 'radios', 'checkboxes', 'date', 'file'].indexOf(t) > -1) {
                $('#panel-type-form-field').show();
                $('#prop-field-name').val(el.fieldName || el.field_name || t);
                $('#prop-field-required').prop('checked', !!el.required);

                if (['select', 'radios'].indexOf(t) > -1) {
                    $('#panel-sub-choices').show();
                    $('#prop-field-choices').val(el.content || 'Option 1, Option 2, Option 3');
                } else {
                    $('#panel-sub-choices').hide();
                }
            } else if (t === 'step_btn') {
                $('#panel-type-step-btn').show();
                $('#prop-step-target').val(el.goto_canvas || el.goto_screen || 2);
            } else if (t === 'link_btn') {
                $('#panel-type-link-btn').show();
                $('#prop-link-url').val(el.linkUrl || '#');
                $('#prop-link-blank').prop('checked', !!el.linkBlank);
            } else if (t === 'wheel') {
                $('#panel-type-wheel').show();
                $('#prop-wheel-slices').val(el.slices || '10% OFF, FREE SHIP, 25% OFF, JACKPOT, 5% OFF');
                $('#prop-wheel-btn-text').val(el.btnText || 'SPIN TO WIN!');
            } else if (t === 'countdown') {
                $('#panel-type-countdown').show();
                $('#prop-countdown-seconds').val(el.countdownSeconds || 900);
            } else if (t === 'pay') {
                $('#panel-type-pay').show();
                $('#prop-pay-amount').val(el.payAmount !== undefined ? el.payAmount : 19.99);
                $('#prop-pay-currency').val(el.payCurrency || 'USD');
            }
        },

        bindInputs: function() {
            var self = this;

            // Rename layer
            $('#prop-layer-name').on('input change', function() {
                var name = $(this).val();
                self.applyToActive('name', name);
                if (window.WpPopPopBuilderLayers) {
                    window.WpPopPopBuilderLayers.renderLayers();
                }
            });

            // Coordinates & dimensions
            $('#prop-x, #prop-y, #prop-w, #prop-h').on('input change', function() {
                var left = parseInt($('#prop-x').val(), 10) || 0;
                var top  = parseInt($('#prop-y').val(), 10) || 0;
                var w    = parseInt($('#prop-w').val(), 10) || 20;
                var h    = parseInt($('#prop-h').val(), 10) || 20;

                var el = self.getActiveElement();
                if (el) {
                    el.left = el.x = left;
                    el.top = el.y = top;
                    el.width = el.w = w;
                    el.height = el.h = h;
                    $('#el-' + el.id).css({
                        left: left + 'px',
                        top: top + 'px',
                        width: w + 'px',
                        height: h + 'px'
                    });
                }
            });

            // Content
            $('#prop-content').on('input change', function() {
                self.applyToActive('content', $(this).val());
                self.rerenderStageElement();
            });

            // Typography
            $('#prop-font-family').on('change', function() {
                self.applyToActive('fontFamily', $(this).val());
                self.rerenderStageElement();
            });

            $('#prop-font-size').on('input change', function() {
                var size = parseInt($(this).val(), 10) || 14;
                self.applyToActive('fontSize', size);
                self.rerenderStageElement();
            });

            $('#prop-font-weight').on('change', function() {
                self.applyToActive('fontWeight', $(this).val());
                self.rerenderStageElement();
            });

            $('#prop-text-align').on('change', function() {
                self.applyToActive('textAlign', $(this).val());
                self.rerenderStageElement();
            });

            $('#prop-line-height').on('input change', function() {
                var lh = parseFloat($(this).val()) || 1.4;
                self.applyToActive('lineHeight', lh);
                self.applyToActive('line_height', lh);
                self.rerenderStageElement();
            });

            $('#prop-letter-spacing').on('input change', function() {
                var ls = parseFloat($(this).val()) || 0;
                self.applyToActive('letterSpacing', ls);
                self.applyToActive('letter_spacing', ls);
                self.rerenderStageElement();
            });

            // Colors
            $('#prop-color').on('input change', function() {
                var c = $(this).val();
                $('#prop-color-hex').val(c);
                self.applyToActive('color', c);
                self.rerenderStageElement();
            });
            $('#prop-color-hex').on('input change', function() {
                var c = $(this).val();
                if (/^#[0-9A-Fa-f]{6}$/.test(c)) $('#prop-color').val(c);
                self.applyToActive('color', c);
                self.rerenderStageElement();
            });

            $('#prop-bg-color').on('input change', function() {
                var c = $(this).val();
                $('#prop-bg-color-hex').val(c);
                self.applyToActive('bgColor', c);
                self.rerenderStageElement();
            });
            $('#prop-bg-color-hex').on('input change', function() {
                var c = $(this).val();
                self.applyToActive('bgColor', c);
                self.rerenderStageElement();
            });

            // Borders
            $('#prop-border-style').on('change', function() {
                self.applyToActive('borderStyle', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-border-width').on('input change', function() {
                self.applyToActive('borderWidth', parseInt($(this).val(), 10) || 0);
                self.rerenderStageElement();
            });
            $('#prop-border-radius').on('input change', function() {
                self.applyToActive('borderRadius', parseInt($(this).val(), 10) || 0);
                self.rerenderStageElement();
            });
            $('#prop-border-color').on('input change', function() {
                var c = $(this).val();
                $('#prop-border-color-hex').val(c);
                self.applyToActive('borderColor', c);
                self.rerenderStageElement();
            });
            $('#prop-border-color-hex').on('input change', function() {
                var c = $(this).val();
                self.applyToActive('borderColor', c);
                self.rerenderStageElement();
            });

            // Shadows & Animations
            $('#prop-box-shadow').on('change', function() {
                self.applyToActive('boxShadow', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-anim-effect').on('change', function() {
                self.applyToActive('animEffect', $(this).val());
            });

            // Specific contextual inputs
            $('#prop-title-tag').on('change', function() {
                self.applyToActive('htmlTag', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-image-url').on('input change', function() {
                self.applyToActive('imageUrl', $(this).val());
                self.applyToActive('content', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-image-fit').on('change', function() {
                self.applyToActive('imageFit', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-video-url').on('input change', function() {
                self.applyToActive('videoUrl', $(this).val());
                self.applyToActive('content', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-video-autoplay').on('change', function() {
                self.applyToActive('videoAutoplay', $(this).is(':checked'));
                self.rerenderStageElement();
            });
            $('#prop-shape-preset').on('change', function() {
                self.applyToActive('shapePreset', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-shape-stroke-width').on('input change', function() {
                self.applyToActive('shapeStrokeWidth', parseInt($(this).val(), 10) || 0);
                self.rerenderStageElement();
            });
            $('#prop-shape-rotate').on('input change', function() {
                self.applyToActive('shapeRotate', parseInt($(this).val(), 10) || 0);
                self.rerenderStageElement();
            });
            $('#prop-field-name').on('input change', function() {
                var fn = $(this).val();
                self.applyToActive('fieldName', fn);
                self.applyToActive('field_name', fn);
                $('#prop-logic-token').text('{' + String(fn).toLowerCase().replace(/[^a-z0-9_]/g, '_') + '}');
            });
            $('#prop-field-required').on('change', function() {
                self.applyToActive('required', $(this).is(':checked'));
            });
            $('#prop-field-choices').on('input change', function() {
                self.applyToActive('content', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-step-target').on('change', function() {
                var t = parseInt($(this).val(), 10) || 2;
                self.applyToActive('goto_canvas', t);
                self.applyToActive('goto_screen', t);
                self.rerenderStageElement();
            });
            $('#prop-link-url').on('input change', function() {
                self.applyToActive('linkUrl', $(this).val());
            });
            $('#prop-link-blank').on('change', function() {
                self.applyToActive('linkBlank', $(this).is(':checked'));
            });
            $('#prop-wheel-slices').on('input change', function() {
                self.applyToActive('slices', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-wheel-btn-text').on('input change', function() {
                self.applyToActive('btnText', $(this).val());
                self.rerenderStageElement();
            });
            $('#prop-countdown-seconds').on('input change', function() {
                self.applyToActive('countdownSeconds', parseInt($(this).val(), 10) || 900);
                self.rerenderStageElement();
            });
            $('#prop-pay-amount').on('input change', function() {
                self.applyToActive('payAmount', parseFloat($(this).val()) || 0);
                self.rerenderStageElement();
            });
            $('#prop-pay-currency').on('input change', function() {
                self.applyToActive('payCurrency', $(this).val());
                self.rerenderStageElement();
            });
        },

        applyToActive: function(key, val) {
            var el = this.getActiveElement();
            if (!el) return;
            el[key] = val;
            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        rerenderStageElement: function() {
            var el = this.getActiveElement();
            if (!el || !window.WpPopPopBuilderCanvas) return;
            var $node = $('#el-' + el.id);
            if ($node.length) {
                var newHtml = window.WpPopPopBuilderCanvas.getInnerMarkup(el);
                $node.html(newHtml);
                
                // Refresh node styles
                $node.css({
                    fontFamily: el.fontFamily && el.fontFamily !== 'inherit' ? el.fontFamily : '',
                    fontSize: el.fontSize ? el.fontSize + 'px' : '',
                    fontWeight: el.fontWeight || '',
                    textAlign: el.textAlign || '',
                    lineHeight: el.lineHeight || el.line_height || '',
                    letterSpacing: (el.letterSpacing || el.letter_spacing || 0) + 'px',
                    color: el.color || '',
                    backgroundColor: el.bgColor && el.bgColor !== 'transparent' ? el.bgColor : 'transparent',
                    borderStyle: el.borderStyle || 'solid',
                    borderWidth: (el.borderWidth !== undefined ? el.borderWidth : 1) + 'px',
                    borderColor: el.borderColor || '',
                    borderRadius: (el.borderRadius !== undefined ? el.borderRadius : 4) + 'px',
                    boxShadow: el.boxShadow && el.boxShadow !== 'none' ? el.boxShadow : 'none'
                });
            }
        },

        getActiveElement: function() {
            if (!this.activeId || !window.WpPopPopBuilderCanvas) return null;
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            return elements.find(function(e) { return String(e.id) === String(window.WpPopPopBuilderInspector.activeId); }) || null;
        }
    };

    $(document).ready(function() {
        window.WpPopPopBuilderInspector.init();
    });

})(window, jQuery);
