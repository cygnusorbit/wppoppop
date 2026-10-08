/**
 * WpPopPop Visual Builder: Layer Properties Inspector Engine (Restored v3.0.53 + Animate.css)
 * Full Dynamic 19-Element Contextual Settings, Style Suite & Logic Sync
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

        normalizeType: function(rawType) {
            var t = (rawType || 'text').toString().toLowerCase().trim();
            if (t === 'radio') return 'radios';
            if (t === 'checkbox') return 'checkboxes';
            if (t === 'dropdown') return 'select';
            if (t === 'step' || t === 'button') return 'step_btn';
            if (t === 'progressbar') return 'progress';
            if (t === 'upload') return 'file';
            if (t === 'payment') return 'pay';
            return t;
        },

        open: function(id) {
            if (!id && id !== 0) return;
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            var el = elements.find(function(e) { 
                return e && (e.id === id || String(e.id) === String(id) || ('el-' + e.id) === String(id)); 
            });
            if (!el) {
                return;
            }

            var type = this.normalizeType(el.type || 'text');

            // 1. Header Type Badge
            $('#insp-element-type-badge').text(type.toUpperCase());

            // 2. Populate Standard Coordinates & Bounds
            var safeIdSuffix = String(el.id || id).slice(-4);
            $('#prop-layer-name').val(el.name || (type.toUpperCase() + ' ' + safeIdSuffix));
            $('#prop-pos-top').val(el.top || 0);
            $('#prop-pos-left').val(el.left || 0);
            $('#prop-size-width').val(el.width || 200);
            $('#prop-size-height').val(el.height || 42);

            // 3. Show & Populate Contextual Element Panel (All 19 Elements)
            $('.element-panel').hide();
            var $activePanel = $('#panel-elem-' + type);
            if ($activePanel.length) {
                $activePanel.show();
            }

            switch(type) {
                case 'text':
                    $('#prop-text-content').val(el.content || '');
                    $('#prop-text-tag').val(el.htmlTag || 'p');
                    break;
                case 'email':
                    $('#prop-email-placeholder').val(el.content || 'Enter your email...');
                    $('#prop-email-fieldname').val(el.field_name || 'email');
                    $('#prop-email-required').prop('checked', el.required !== false);
                    break;
                case 'number':
                    $('#prop-number-val').val(el.content || '1');
                    $('#prop-number-min').val(el.min !== undefined ? el.min : 0);
                    $('#prop-number-max').val(el.max !== undefined ? el.max : 100);
                    $('#prop-number-step').val(el.step !== undefined ? el.step : 1);
                    $('#prop-number-fieldname').val(el.field_name || 'quantity');
                    break;
                case 'select':
                    $('#prop-select-options').val(Array.isArray(el.options) ? el.options.join(', ') : (el.content || 'Option 1, Option 2, Option 3'));
                    $('#prop-select-fieldname').val(el.field_name || 'dropdown_field');
                    break;
                case 'radios':
                    $('#prop-radios-options').val(Array.isArray(el.options) ? el.options.join(', ') : (el.content || 'Choice A, Choice B'));
                    $('#prop-radios-fieldname').val(el.field_name || 'radio_choice');
                    break;
                case 'checkboxes':
                    $('#prop-checkbox-label').val(el.content || 'I agree to the terms');
                    $('#prop-checkbox-fieldname').val(el.field_name || 'terms_agreement');
                    $('#prop-checkbox-checked').prop('checked', el.checked !== false);
                    break;
                case 'rating':
                    $('#prop-rating-val').val(el.content || 5);
                    $('#prop-rating-color').val(el.ratingColor || '#f59e0b');
                    $('#prop-rating-fieldname').val(el.field_name || 'rating');
                    break;
                case 'date':
                    $('#prop-date-fieldname').val(el.field_name || 'appointment_date');
                    $('#prop-date-required').prop('checked', !!el.required);
                    break;
                case 'slider':
                    $('#prop-slider-min').val(el.min !== undefined ? el.min : 0);
                    $('#prop-slider-max').val(el.max !== undefined ? el.max : 100);
                    $('#prop-slider-val').val(el.content || 50);
                    $('#prop-slider-fieldname').val(el.field_name || 'range_val');
                    break;
                case 'signature':
                    $('#prop-sig-color').val(el.penColor || '#0f172a');
                    $('#prop-sig-clear-label').val(el.clearLabel || 'Clear Signature');
                    $('#prop-sig-fieldname').val(el.field_name || 'digital_signature');
                    break;
                case 'wheel':
                    $('#prop-wheel-slices').val(Array.isArray(el.options) ? el.options.join(', ') : (el.content || '10% OFF, FREE SHIPPING, 25% OFF, JACKPOT'));
                    $('#prop-wheel-btn-text').val(el.btnText || 'SPIN TO WIN!');
                    $('#prop-wheel-win-msg').val(el.winMsg || 'Congratulations! You won {prize}!');
                    break;
                case 'scratch':
                    $('#prop-scratch-prize').val(el.content || 'YOU WON 25% OFF! USE CODE: WIN25');
                    $('#prop-scratch-foil').val(el.foilColor || '#94a3b8');
                    $('#prop-scratch-pct').val(el.scratchPct || 45);
                    break;
                case 'countdown':
                    $('#prop-countdown-seconds').val(el.countdownSeconds || 900);
                    $('#prop-countdown-expire').val(el.expireAction || 'none');
                    break;
                case 'progress':
                    $('#prop-progress-val').val(el.content || 65);
                    $('#prop-progress-color').val(el.progressColor || '#2563eb');
                    break;
                case 'file':
                    $('#prop-file-exts').val(el.fileExts || '.jpg, .jpeg, .png, .pdf');
                    $('#prop-file-max-mb').val(el.fileMaxMb || 5);
                    $('#prop-file-fieldname').val(el.field_name || 'attachment');
                    break;
                case 'step_btn':
                    $('#prop-step-label').val(el.content || 'Next Canvas &rarr;');
                    $('#prop-step-canvas').val(el.goto_canvas || el.goto_screen || 2);
                    break;
                case 'submit':
                    $('#prop-submit-label').val(el.content || 'Submit Form');
                    $('#prop-submit-action').val(el.submitAction || 'default');
                    break;
                case 'pay':
                    $('#prop-pay-label').val(el.content || 'Checkout Now');
                    $('#prop-pay-amount').val(el.payAmount !== undefined ? el.payAmount : 19.99);
                    $('#prop-pay-currency').val(el.payCurrency || 'USD');
                    $('#prop-pay-gateway').val(el.payGateway || 'stripe');
                    break;
                case 'html':
                    $('#prop-html-code').val(el.content || '');
                    break;
            }

            // 4. Populate Style Tab
            $('#prop-font-family').val(el.fontFamily || 'inherit');
            $('#prop-font-size').val(el.fontSize || 14);
            $('#prop-color').val(el.color || '#0f172a');
            $('#prop-font-weight').val(el.fontWeight || '400');
            $('#prop-text-align').val(el.textAlign || 'left');

            $('#prop-bg-color').val(el.bgColor || '#ffffff');
            $('#prop-border-color').val(el.borderColor || '#cbd5e1');
            $('#prop-border-radius').val(el.borderRadius !== undefined ? el.borderRadius : 4);
            $('#prop-border-width').val(el.borderWidth !== undefined ? el.borderWidth : 1);
            $('#prop-opacity').val(el.opacity !== undefined ? el.opacity : 1);

            // Animate.css Effect Value
            $('#prop-anim-effect').val(el.animEffect || 'none');
            $('#prop-box-shadow').val(el.boxShadow || 'none');

            // 5. Populate Logic Tab
            var tokenName = el.field_name || type;
            $('#prop-display-token').val('{' + tokenName + '}');
            $('#prop-action-url').val(el.actionUrl || '');
            $('#prop-action-blank').prop('checked', !!el.actionBlank);
            $('#prop-action-close').val(el.actionClose || 'none');
            $('#prop-action-js').val(el.actionJs || '');

            // Ensure Basic tab is active
            $('.wppoppop-insp-tab').removeClass('active');
            $('.wppoppop-insp-tab[data-tab="basic"]').addClass('active');
            $('.wppoppop-insp-content').hide();
            $('#insp-tab-basic').show();

            // Direct Display Activation
            $('#wppoppop-inspector-drawer')
                .addClass('open')
                .css({
                    'display': 'flex',
                    'transform': 'translateX(0)',
                    'visibility': 'visible',
                    'pointer-events': 'auto'
                });
            $('.wppoppop-main-frame').addClass('panel-open');
        },

        close: function() {
            $('#wppoppop-inspector-drawer')
                .removeClass('open')
                .css({
                    'display': 'none',
                    'transform': 'translateX(100%)',
                    'visibility': 'hidden',
                    'pointer-events': 'none'
                });
            $('.wppoppop-main-frame').removeClass('panel-open');
            $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.state.activeId = null;
            }
        },

        syncCoordinates: function(el) {
            $('#prop-pos-top').val(el.top);
            $('#prop-pos-left').val(el.left);
            $('#prop-size-width').val(el.width);
            $('#prop-size-height').val(el.height);
        },

        bindInputs: function() {
            var getActiveEl = function() {
                var activeId = window.WpPopPopBuilderCore.state.activeId;
                if (!activeId && activeId !== 0) return null;
                var elements = window.WpPopPopBuilderCanvas.getActiveElements();
                return elements.find(function(e) { return e && String(e.id) === String(activeId); });
            };

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

            $('#prop-text-content, #prop-text-tag').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-text-content').val();
                    el.htmlTag = $('#prop-text-tag').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-email-placeholder, #prop-email-fieldname, #prop-email-required').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-email-placeholder').val();
                    el.field_name = $('#prop-email-fieldname').val();
                    el.required = $('#prop-email-required').is(':checked');
                    $('#prop-display-token').val('{' + (el.field_name || 'email') + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-number-val, #prop-number-min, #prop-number-max, #prop-number-step, #prop-number-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-number-val').val();
                    el.min = parseFloat($('#prop-number-min').val());
                    el.max = parseFloat($('#prop-number-max').val());
                    el.step = parseFloat($('#prop-number-step').val());
                    el.field_name = $('#prop-number-fieldname').val();
                    $('#prop-display-token').val('{' + (el.field_name || 'quantity') + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-select-options, #prop-select-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-select-options').val();
                    el.options = el.content.split(',').map(function(s) { return s.trim(); });
                    el.field_name = $('#prop-select-fieldname').val();
                    $('#prop-display-token').val('{' + (el.field_name || 'dropdown_field') + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-radios-options, #prop-radios-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-radios-options').val();
                    el.options = el.content.split(',').map(function(s) { return s.trim(); });
                    el.field_name = $('#prop-radios-fieldname').val();
                    $('#prop-display-token').val('{' + (el.field_name || 'radio_choice') + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-checkbox-label, #prop-checkbox-fieldname, #prop-checkbox-checked').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-checkbox-label').val();
                    el.field_name = $('#prop-checkbox-fieldname').val();
                    el.checked = $('#prop-checkbox-checked').is(':checked');
                    $('#prop-display-token').val('{' + (el.field_name || 'terms_agreement') + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-rating-val, #prop-rating-color, #prop-rating-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = parseInt($('#prop-rating-val').val(), 10) || 5;
                    el.ratingColor = $('#prop-rating-color').val();
                    el.field_name = $('#prop-rating-fieldname').val();
                    $('#prop-display-token').val('{' + (el.field_name || 'rating') + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-date-fieldname, #prop-date-required').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.field_name = $('#prop-date-fieldname').val();
                    el.required = $('#prop-date-required').is(':checked');
                    $('#prop-display-token').val('{' + (el.field_name || 'appointment_date') + '}');
                }
            });

            $('#prop-slider-min, #prop-slider-max, #prop-slider-val, #prop-slider-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.min = parseInt($('#prop-slider-min').val(), 10);
                    el.max = parseInt($('#prop-slider-max').val(), 10);
                    el.content = parseInt($('#prop-slider-val').val(), 10);
                    el.field_name = $('#prop-slider-fieldname').val();
                    $('#prop-display-token').val('{' + (el.field_name || 'range_val') + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-sig-color, #prop-sig-clear-label, #prop-sig-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.penColor = $('#prop-sig-color').val();
                    el.clearLabel = $('#prop-sig-clear-label').val();
                    el.field_name = $('#prop-sig-fieldname').val();
                    $('#prop-display-token').val('{' + (el.field_name || 'digital_signature') + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-wheel-slices, #prop-wheel-btn-text, #prop-wheel-win-msg').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-wheel-slices').val();
                    el.options = el.content.split(',').map(function(s) { return s.trim(); });
                    el.btnText = $('#prop-wheel-btn-text').val();
                    el.winMsg = $('#prop-wheel-win-msg').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-scratch-prize, #prop-scratch-foil, #prop-scratch-pct').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-scratch-prize').val();
                    el.foilColor = $('#prop-scratch-foil').val();
                    el.scratchPct = parseInt($('#prop-scratch-pct').val(), 10);
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-countdown-seconds, #prop-countdown-expire').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.countdownSeconds = parseInt($('#prop-countdown-seconds').val(), 10) || 900;
                    el.expireAction = $('#prop-countdown-expire').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-progress-val, #prop-progress-color').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = parseInt($('#prop-progress-val').val(), 10) || 0;
                    el.progressColor = $('#prop-progress-color').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-file-exts, #prop-file-max-mb, #prop-file-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.fileExts = $('#prop-file-exts').val();
                    el.fileMaxMb = parseInt($('#prop-file-max-mb').val(), 10);
                    el.field_name = $('#prop-file-fieldname').val();
                    $('#prop-display-token').val('{' + (el.field_name || 'attachment') + '}');
                }
            });

            $('#prop-step-label, #prop-step-canvas').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-step-label').val();
                    var cNum = parseInt($('#prop-step-canvas').val(), 10) || 2;
                    el.goto_canvas = cNum;
                    el.goto_screen = cNum;
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-submit-label, #prop-submit-action').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-submit-label').val();
                    el.submitAction = $('#prop-submit-action').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-pay-label, #prop-pay-amount, #prop-pay-currency, #prop-pay-gateway').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-pay-label').val();
                    el.payAmount = parseFloat($('#prop-pay-amount').val()) || 0;
                    el.payCurrency = $('#prop-pay-currency').val();
                    el.payGateway = $('#prop-pay-gateway').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-html-code').on('input change', function() {
                var el = getActiveEl();
                if (el) {
                    el.content = $('#prop-html-code').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            // Style Tab Live Sync with Animate.css Class Triggering
            $('#prop-font-family, #prop-font-size, #prop-color, #prop-font-weight, #prop-text-align, #prop-bg-color, #prop-border-color, #prop-border-radius, #prop-border-width, #prop-opacity, #prop-anim-effect, #prop-box-shadow').on('input change', function() {
                var el = getActiveEl();
                if (!el) return;
                el.fontFamily = $('#prop-font-family').val();
                el.fontSize = parseInt($('#prop-font-size').val(), 10) || 14;
                el.color = $('#prop-color').val();
                el.fontWeight = $('#prop-font-weight').val();
                el.textAlign = $('#prop-text-align').val();
                el.bgColor = $('#prop-bg-color').val();
                el.borderColor = $('#prop-border-color').val();
                el.borderRadius = parseInt($('#prop-border-radius').val(), 10) || 0;
                el.borderWidth = parseInt($('#prop-border-width').val(), 10) || 0;
                el.opacity = parseFloat($('#prop-opacity').val()) || 1;
                el.animEffect = $('#prop-anim-effect').val();
                el.boxShadow = $('#prop-box-shadow').val();

                window.WpPopPopBuilderCanvas.renderCanvas();

                // Live preview the selected Animate.css animation on the canvas element
                if (el.animEffect && el.animEffect !== 'none') {
                    var $node = $('#el-' + el.id);
                    $node.removeClass(function(index, className) {
                        return (className.match(/(^|\s)animate__\S+/g) || []).join(' ');
                    });
                    $node.addClass('animate__animated animate__' + el.animEffect);
                    setTimeout(function() {
                        $node.removeClass('animate__animated animate__' + el.animEffect);
                    }, 1200);
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
})(jQuery);
