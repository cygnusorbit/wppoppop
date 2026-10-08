/**
 * WpPopPop Visual Builder: Layer Properties Inspector Engine
 * Full 21-Element Contextual Settings, Native WordPress Media Uploader & Style Suite
 */
(function($) {
    'use strict';

    var wpMediaFrame = null;

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

            var type = el.type || 'text';

            // 1. Header Type Badge
            $('#insp-element-type-badge').text(type.toUpperCase());

            // 2. Switch Contextual Element Panel in Basic Tab (All 21 Elements)
            $('#wppoppop-element-specific-settings .element-panel').hide();
            $('#panel-elem-' + type).show();

            // 3. Populate Standard Dimensions & Coordinates
            $('#prop-layer-name').val(el.name || '');
            $('#prop-pos-top').val(el.top || 0);
            $('#prop-pos-left').val(el.left || 0);
            $('#prop-size-width').val(el.width || 100);
            $('#prop-size-height').val(el.height || 40);

            // 4. Contextual Element Data Population
            switch (type) {
                case 'text':
                    $('#prop-text-content').val(el.content || '');
                    $('#prop-text-tag').val(el.htmlTag || 'p');
                    break;

                case 'image':
                    var imgUrl = el.imageUrl || el.content || '';
                    var imgAlt = el.imageAlt || el.alt || '';
                    var imgFit = el.imageFit || 'cover';
                    $('#prop-image-url').val(imgUrl);
                    $('#prop-image-alt').val(imgAlt);
                    $('#prop-image-fit').val(imgFit);

                    if (imgUrl) {
                        $('#prop-image-preview').attr('src', imgUrl).show();
                        $('#prop-image-placeholder').hide();
                    } else {
                        $('#prop-image-preview').attr('src', '').hide();
                        $('#prop-image-placeholder').show();
                    }
                    break;

                case 'shape':
                    $('#prop-shape-preset').val(el.shapePreset || 'circle');
                    $('#prop-shape-fill').val(el.shapeFill || '#3b82f6');
                    $('#prop-shape-stroke').val(el.shapeStroke || '#1d4ed8');
                    $('#prop-shape-stroke-width').val(el.shapeStrokeWidth !== undefined ? el.shapeStrokeWidth : 0);
                    $('#prop-shape-rotate').val(el.shapeRotate !== undefined ? el.shapeRotate : 0);
                    break;

                case 'email':
                    $('#prop-email-placeholder').val(el.content || 'Enter your email...');
                    $('#prop-email-fieldname').val(el.fieldName || el.field_name || 'email');
                    $('#prop-email-required').prop('checked', el.required !== false);
                    break;

                case 'number':
                    $('#prop-number-val').val(el.content || '1');
                    $('#prop-number-min').val(el.min !== undefined ? el.min : 0);
                    $('#prop-number-max').val(el.max !== undefined ? el.max : 100);
                    $('#prop-number-step').val(el.step !== undefined ? el.step : 1);
                    $('#prop-number-fieldname').val(el.fieldName || el.field_name || 'quantity');
                    break;

                case 'select':
                    $('#prop-select-options').val(Array.isArray(el.options) ? el.options.join(', ') : (el.content || 'Option 1, Option 2, Option 3'));
                    $('#prop-select-fieldname').val(el.fieldName || el.field_name || 'dropdown_field');
                    break;

                case 'radios':
                    $('#prop-radios-options').val(Array.isArray(el.options) ? el.options.join(', ') : (el.content || 'Choice A, Choice B, Choice C'));
                    $('#prop-radios-fieldname').val(el.fieldName || el.field_name || 'radio_choice');
                    break;

                case 'checkboxes':
                    $('#prop-checkbox-label').val(el.content || 'I agree to the terms and conditions');
                    $('#prop-checkbox-fieldname').val(el.fieldName || el.field_name || 'terms_agreement');
                    $('#prop-checkbox-checked').prop('checked', !!el.checked);
                    break;

                case 'rating':
                    $('#prop-rating-val').val(el.content || 5);
                    $('#prop-rating-color').val(el.ratingColor || '#f59e0b');
                    $('#prop-rating-fieldname').val(el.fieldName || el.field_name || 'rating');
                    break;

                case 'date':
                    $('#prop-date-fieldname').val(el.fieldName || el.field_name || 'appointment_date');
                    $('#prop-date-required').prop('checked', !!el.required);
                    break;

                case 'slider':
                    $('#prop-slider-min').val(el.min !== undefined ? el.min : 0);
                    $('#prop-slider-max').val(el.max !== undefined ? el.max : 100);
                    $('#prop-slider-val').val(el.content || 50);
                    $('#prop-slider-fieldname').val(el.fieldName || el.field_name || 'range_val');
                    break;

                case 'signature':
                    $('#prop-sig-color').val(el.penColor || '#0f172a');
                    $('#prop-sig-clear-label').val(el.clearLabel || 'Clear Signature');
                    $('#prop-sig-fieldname').val(el.fieldName || el.field_name || 'digital_signature');
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
                    $('#prop-file-fieldname').val(el.fieldName || el.field_name || 'attachment');
                    break;

                case 'step_btn':
                    var targetCanvas = el.goto_canvas || el.goto_screen || 2;
                    $('#prop-step-label').val(el.content || 'Next Canvas &rarr;');
                    $('#prop-step-canvas').val(targetCanvas);
                    break;

                case 'submit':
                    $('#prop-submit-label').val(el.content || 'Submit Form');
                    $('#prop-submit-action').val(el.submitAction || 'default');
                    break;

                case 'pay':
                    $('#prop-pay-label').val(el.content || 'Checkout Now');
                    $('#prop-pay-amount').val(el.payAmount || 19.99);
                    $('#prop-pay-currency').val(el.payCurrency || 'USD');
                    $('#prop-pay-gateway').val(el.payGateway || 'stripe');
                    break;

                case 'html':
                    $('#prop-html-code').val(el.content || '');
                    break;
            }

            // 5. Populate Style Tab - Typography
            $('#prop-font-family').val(el.fontFamily || 'inherit');
            $('#prop-font-size').val(el.fontSize || 14);
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
            $('#prop-border-radius').val(el.borderRadius !== undefined ? el.borderRadius : 4);
            $('#prop-border-width').val(el.borderWidth !== undefined ? el.borderWidth : 1);
            $('#prop-opacity').val(el.opacity !== undefined ? el.opacity : 1);
            $('#prop-anim-effect').val(el.animEffect || 'none');
            $('#prop-box-shadow').val(el.boxShadow || 'none');

            // 6. Populate Logic Tab
            var tokenName = el.fieldName || el.field_name || type;
            $('#prop-display-token').val('{' + tokenName + '}');
            $('#prop-action-url').val(el.actionUrl || '');
            $('#prop-action-blank').prop('checked', !!el.actionBlank);
            $('#prop-action-close').val(el.actionClose || 'none');
            $('#prop-action-js').val(el.actionJs || '');

            // Open Inspector Frame
            $('.wppoppop-main-frame, .wppoppop-builder-wrap').addClass('panel-open');
            $('#wppoppop-inspector-drawer').show();
        },

        close: function() {
            $('.wppoppop-main-frame, .wppoppop-builder-wrap').removeClass('panel-open');
            $('#wppoppop-inspector-drawer').hide();
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
            var self = this;
            var getActiveEl = function() {
                var activeId = window.WpPopPopBuilderCore ? window.WpPopPopBuilderCore.state.activeId : null;
                if (!activeId) return null;
                var elements = window.WpPopPopBuilderCanvas.getActiveElements();
                return elements.find(function(e) { return e.id === activeId; });
            };

            // Standard Coordinates & Bounds Sync
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

            // Native WordPress Media Library Uploader Button
            $('#prop-image-upload-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el) return;

                if (typeof wp === 'undefined' || !wp.media) {
                    alert('WordPress Media Library is not available. Please verify wp_enqueue_media() is active or enter an Image URL manually.');
                    return;
                }

                if (wpMediaFrame) {
                    wpMediaFrame.open();
                    return;
                }

                wpMediaFrame = wp.media({
                    title: 'Select or Upload Element Image',
                    button: { text: 'Use this Image' },
                    library: { type: 'image' },
                    multiple: false
                });

                wpMediaFrame.on('select', function() {
                    var attachment = wpMediaFrame.state().get('selection').first().toJSON();
                    if (!attachment || !attachment.url) return;

                    var activeEl = getActiveEl();
                    if (!activeEl) return;

                    var url = attachment.url;
                    var alt = attachment.alt || attachment.title || '';

                    activeEl.imageUrl = url;
                    activeEl.content = url;
                    activeEl.imageAlt = alt;

                    $('#prop-image-url').val(url);
                    $('#prop-image-alt').val(alt);
                    $('#prop-image-preview').attr('src', url).show();
                    $('#prop-image-placeholder').hide();

                    if (window.WpPopPopBuilderCanvas) {
                        window.WpPopPopBuilderCanvas.renderCanvas();
                    }
                    if (window.WpPopPopBuilderCore) {
                        window.WpPopPopBuilderCore.pushHistory();
                    }
                });

                wpMediaFrame.open();
            });

            // Remove Image Button
            $('#prop-image-remove-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el) return;

                el.imageUrl = '';
                el.content = '';
                el.imageAlt = '';

                $('#prop-image-url').val('');
                $('#prop-image-alt').val('');
                $('#prop-image-preview').attr('src', '').hide();
                $('#prop-image-placeholder').show();

                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
                if (window.WpPopPopBuilderCore) {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });

            // Image URL, Alt & Object Fit Live Sync
            $('#prop-image-url').on('input change', function() {
                var el = getActiveEl();
                if (!el || el.type !== 'image') return;
                var url = $(this).val().trim();
                el.imageUrl = url;
                el.content = url;

                if (url) {
                    $('#prop-image-preview').attr('src', url).show();
                    $('#prop-image-placeholder').hide();
                } else {
                    $('#prop-image-preview').attr('src', '').hide();
                    $('#prop-image-placeholder').show();
                }

                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-image-alt').on('input change', function() {
                var el = getActiveEl();
                if (!el || el.type !== 'image') return;
                el.imageAlt = $(this).val();
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-image-fit').on('change', function() {
                var el = getActiveEl();
                if (!el || el.type !== 'image') return;
                el.imageFit = $(this).val();
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            // Shape Live Sync
            $('#prop-shape-preset, #prop-shape-fill, #prop-shape-stroke, #prop-shape-stroke-width, #prop-shape-rotate').on('input change', function() {
                var el = getActiveEl();
                if (!el || el.type !== 'shape') return;
                el.shapePreset = $('#prop-shape-preset').val();
                el.shapeFill = $('#prop-shape-fill').val();
                el.shapeStroke = $('#prop-shape-stroke').val();
                el.shapeStrokeWidth = parseInt($('#prop-shape-stroke-width').val(), 10) || 0;
                el.shapeRotate = parseInt($('#prop-shape-rotate').val(), 10) || 0;
                window.WpPopPopBuilderCanvas.renderCanvas();
            });

            // Text Live Sync
            $('#prop-text-content, #prop-text-tag').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'text') {
                    el.content = $('#prop-text-content').val();
                    el.htmlTag = $('#prop-text-tag').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            // Form Elements Live Sync
            $('#prop-email-placeholder, #prop-email-fieldname, #prop-email-required').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'email') {
                    el.content = $('#prop-email-placeholder').val();
                    el.fieldName = $('#prop-email-fieldname').val();
                    el.required = $('#prop-email-required').is(':checked');
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-number-val, #prop-number-min, #prop-number-max, #prop-number-step, #prop-number-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'number') {
                    el.content = $('#prop-number-val').val();
                    el.min = parseFloat($('#prop-number-min').val());
                    el.max = parseFloat($('#prop-number-max').val());
                    el.step = parseFloat($('#prop-number-step').val());
                    el.fieldName = $('#prop-number-fieldname').val();
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-select-options, #prop-select-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'select') {
                    el.content = $('#prop-select-options').val();
                    el.options = el.content.split(',').map(function(s) { return s.trim(); });
                    el.fieldName = $('#prop-select-fieldname').val();
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-radios-options, #prop-radios-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'radios') {
                    el.content = $('#prop-radios-options').val();
                    el.options = el.content.split(',').map(function(s) { return s.trim(); });
                    el.fieldName = $('#prop-radios-fieldname').val();
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-checkbox-label, #prop-checkbox-fieldname, #prop-checkbox-checked').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'checkboxes') {
                    el.content = $('#prop-checkbox-label').val();
                    el.fieldName = $('#prop-checkbox-fieldname').val();
                    el.checked = $('#prop-checkbox-checked').is(':checked');
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-rating-val, #prop-rating-color, #prop-rating-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'rating') {
                    el.content = parseInt($('#prop-rating-val').val(), 10) || 5;
                    el.ratingColor = $('#prop-rating-color').val();
                    el.fieldName = $('#prop-rating-fieldname').val();
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-date-fieldname, #prop-date-required').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'date') {
                    el.fieldName = $('#prop-date-fieldname').val();
                    el.required = $('#prop-date-required').is(':checked');
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                }
            });

            $('#prop-slider-min, #prop-slider-max, #prop-slider-val, #prop-slider-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'slider') {
                    el.min = parseInt($('#prop-slider-min').val(), 10);
                    el.max = parseInt($('#prop-slider-max').val(), 10);
                    el.content = parseInt($('#prop-slider-val').val(), 10);
                    el.fieldName = $('#prop-slider-fieldname').val();
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-sig-color, #prop-sig-clear-label, #prop-sig-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'signature') {
                    el.penColor = $('#prop-sig-color').val();
                    el.clearLabel = $('#prop-sig-clear-label').val();
                    el.fieldName = $('#prop-sig-fieldname').val();
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-wheel-slices, #prop-wheel-btn-text, #prop-wheel-win-msg').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'wheel') {
                    el.content = $('#prop-wheel-slices').val();
                    el.options = el.content.split(',').map(function(s) { return s.trim(); });
                    el.btnText = $('#prop-wheel-btn-text').val();
                    el.winMsg = $('#prop-wheel-win-msg').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-scratch-prize, #prop-scratch-foil, #prop-scratch-pct').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'scratch') {
                    el.content = $('#prop-scratch-prize').val();
                    el.foilColor = $('#prop-scratch-foil').val();
                    el.scratchPct = parseInt($('#prop-scratch-pct').val(), 10);
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-countdown-seconds, #prop-countdown-expire').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'countdown') {
                    el.countdownSeconds = parseInt($('#prop-countdown-seconds').val(), 10) || 900;
                    el.expireAction = $('#prop-countdown-expire').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-progress-val, #prop-progress-color').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'progress') {
                    el.content = parseInt($('#prop-progress-val').val(), 10) || 0;
                    el.progressColor = $('#prop-progress-color').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-file-exts, #prop-file-max-mb, #prop-file-fieldname').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'file') {
                    el.fileExts = $('#prop-file-exts').val();
                    el.fileMaxMb = parseInt($('#prop-file-max-mb').val(), 10);
                    el.fieldName = $('#prop-file-fieldname').val();
                    $('#prop-display-token').val('{' + el.fieldName + '}');
                }
            });

            $('#prop-step-label, #prop-step-canvas').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'step_btn') {
                    el.content = $('#prop-step-label').val();
                    var cNum = parseInt($('#prop-step-canvas').val(), 10) || 2;
                    el.goto_canvas = cNum;
                    el.goto_screen = cNum;
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-submit-label, #prop-submit-action').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'submit') {
                    el.content = $('#prop-submit-label').val();
                    el.submitAction = $('#prop-submit-action').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-pay-label, #prop-pay-amount, #prop-pay-currency, #prop-pay-gateway').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'pay') {
                    el.content = $('#prop-pay-label').val();
                    el.payAmount = parseFloat($('#prop-pay-amount').val()) || 0;
                    el.payCurrency = $('#prop-pay-currency').val();
                    el.payGateway = $('#prop-pay-gateway').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-html-code').on('input change', function() {
                var el = getActiveEl();
                if (el && el.type === 'html') {
                    el.content = $('#prop-html-code').val();
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            // Checker Buttons: Transparent Fills & Borders
            $('#prop-text-transparent-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el) return;
                el.color = 'transparent';
                $('#prop-text-transparent-btn').addClass('active');

                var $node = $('#el-' + el.id);
                $node.css('color', 'transparent');
                $node.find('*').css('color', 'transparent');

                if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                if (window.WpPopPopBuilderCore) window.WpPopPopBuilderCore.pushHistory();
            });

            $('#prop-bg-transparent-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el) return;
                el.bgColor = 'transparent';
                $('#prop-bg-transparent-btn').addClass('active');

                var $node = $('#el-' + el.id);
                $node.css('background-color', 'transparent');
                $node.find('button').css('background', 'transparent');

                if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                if (window.WpPopPopBuilderCore) window.WpPopPopBuilderCore.pushHistory();
            });

            $('#prop-border-transparent-btn').off('click').on('click', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el) return;
                el.borderColor = 'transparent';
                $('#prop-border-transparent-btn').addClass('active');

                var $node = $('#el-' + el.id);
                $node.css('border-color', 'transparent');

                if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                if (window.WpPopPopBuilderCore) window.WpPopPopBuilderCore.pushHistory();
            });

            // Live Animation Replay Preview
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
                el.fontSize = parseInt($('#prop-font-size').val(), 10) || 14;
                el.fontWeight = $('#prop-font-weight').val();
                el.textAlign = $('#prop-text-align').val();
                el.padding = parseInt($('#prop-padding').val(), 10) || 0;

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
                el.borderRadius = parseInt($('#prop-border-radius').val(), 10) || 0;
                el.borderWidth = parseInt($('#prop-border-width').val(), 10) || 0;
                el.opacity = parseFloat($('#prop-opacity').val()) || 1;

                var prevAnim = el.animEffect;
                el.animEffect = $('#prop-anim-effect').val();
                el.boxShadow = $('#prop-box-shadow').val();

                window.WpPopPopBuilderCanvas.renderCanvas();

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
