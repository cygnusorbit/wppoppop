/**
 * WpPopPop Visual Builder: Campaign Settings Drawer Engine
 * Dedicated Per-Canvas Settings (Name, Size, Color, Animation, Logic Tab) & Bullets Selector
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderSettings = {
        init: function() {
            this.bindDrawer();
            this.bindAccordions();
            this.bindCanvasSubtabs();
            this.bindCanvasBullets();
            this.bindCanvasMetaInputs();
            this.bindCanvasLogicInputs();
            this.bindLiveStyle();
        },

        openDrawer: function() {
            $('#wppoppop-settings-drawer').addClass('open');
            $('#wppoppop-settings-backdrop').addClass('open');
            this.renderCanvasBullets();
            this.syncCurrentCanvasSettings();
        },

        closeDrawer: function() {
            $('#wppoppop-settings-drawer').removeClass('open');
            $('#wppoppop-settings-backdrop').removeClass('open');
        },

        bindDrawer: function() {
            var self = this;
            $('#wppoppop-btn-settings').on('click', function(e) {
                e.preventDefault();
                self.openDrawer();
            });

            $('#wppoppop-settings-drawer-close, #wppoppop-settings-backdrop').on('click', function(e) {
                e.preventDefault();
                self.closeDrawer();
            });
        },

        bindAccordions: function() {
            $(document).on('click', '.wppoppop-acc-header', function(e) {
                e.preventDefault();
                var $header = $(this);
                var $body = $header.next('.wppoppop-acc-body');

                $('.wppoppop-acc-header').not($header).removeClass('active');
                $('.wppoppop-acc-body').not($body).slideUp(150);

                $header.toggleClass('active');
                $body.slideToggle(150);
            });
        },

        bindCanvasSubtabs: function() {
            $('.wppoppop-csubtab').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.wppoppop-csubtab').removeClass('active');
                $(this).addClass('active');

                $('.wppoppop-csubcontent').hide();
                $('#csub-tab-' + tab).show();
            });
        },

        renderCanvasBullets: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;

            var $container = $('#wppoppop-canvas-bullets-container');
            $container.empty();

            var canvasKeys = Object.keys(core.state.canvases).map(Number).sort(function(a, b) { return a - b; });
            if (canvasKeys.length === 0) canvasKeys = [1, 2];

            canvasKeys.forEach(function(cNum) {
                var meta = core.state.canvasMeta[cNum] || { name: 'Canvas ' + cNum };
                var name = meta.name || ('Canvas ' + cNum);
                var isActive = (cNum === core.state.currentCanvas);

                var $btn = $('<button type="button" class="wppoppop-canvas-bullet"></button>')
                    .attr('data-canvas', cNum)
                    .toggleClass('active', isActive)
                    .html('<span class="wppoppop-bullet-dot"></span> ' + name);

                $container.append($btn);
            });

            var $addBullet = $('<button type="button" id="wppoppop-settings-add-canvas-bullet" class="wppoppop-bullet-add-btn" title="Add Canvas">+</button>');
            $container.append($addBullet);
        },

        bindCanvasBullets: function() {
            var self = this;

            $(document).on('click', '.wppoppop-canvas-bullet', function(e) {
                e.preventDefault();
                var cNum = parseInt($(this).data('canvas'), 10) || 1;
                var core = window.WpPopPopBuilderCore;
                if (core) {
                    core.switchCanvas(cNum);
                }
                self.syncCurrentCanvasSettings();
            });

            $(document).on('click', '#wppoppop-settings-add-canvas-bullet', function(e) {
                e.preventDefault();
                var core = window.WpPopPopBuilderCore;
                if (core) {
                    core.addNewCanvas();
                }
                self.renderCanvasBullets();
                self.syncCurrentCanvasSettings();
            });
        },

        syncCurrentCanvasSettings: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;

            var cur = core.state.currentCanvas || 1;
            var meta = core.state.canvasMeta[cur] || {
                name: 'Canvas ' + cur,
                width: 640,
                height: 400,
                bg_mode: 'solid',
                bg_color: '#ffffff',
                grad_color1: '#3b82f6',
                grad_color2: '#1d4ed8',
                grad_angle: 135,
                anim_appearance: 'fade',
                anim_duration: 1000,
                anim_delay: 0,
                anim_disappearance: 'fade',
                logic_enabled: false,
                logic_field: '',
                logic_operator: 'equals',
                logic_value: '',
                logic_action: 'show'
            };

            // 1. Update Title Badge & Bullets
            $('#set-current-canvas-badge').text(meta.name || ('Canvas ' + cur));
            $('.wppoppop-canvas-bullet').removeClass('active');
            $('.wppoppop-canvas-bullet[data-canvas="' + cur + '"]').addClass('active');

            // 2. Sync General & Dimensions
            $('#set-canvas-name').val(meta.name || ('Canvas ' + cur));
            $('#set-canvas-width').val(meta.width || 640);
            $('#set-canvas-height').val(meta.height || 400);

            // 3. Sync Canvas-Specific Background Color
            var bgMode = meta.bg_mode || 'solid';
            $('#set-canvas-bg-mode').val(bgMode);
            $('#set-canvas-solid-wrap').toggle(bgMode === 'solid');
            $('#set-canvas-gradient-wrap').toggle(bgMode === 'gradient');
            $('#set-canvas-bg-color').val(meta.bg_color || '#ffffff');
            $('#set-canvas-grad-color1').val(meta.grad_color1 || '#3b82f6');
            $('#set-canvas-grad-color2').val(meta.grad_color2 || '#1d4ed8');
            $('#set-canvas-grad-angle').val(meta.grad_angle || 135);

            // 4. Sync Canvas Animation Settings (Matching Screenshot)
            var animApp = meta.anim_appearance || 'fade';
            var animDur = meta.anim_duration !== undefined ? meta.anim_duration : 1000;
            var animDel = meta.anim_delay !== undefined ? meta.anim_delay : 0;
            var animDis = meta.anim_disappearance || 'fade';

            $('#set-canvas-anim-appearance').val(animApp);
            $('#set-canvas-anim-duration').val(animDur);
            $('#set-canvas-anim-delay').val(animDel);
            $('#set-canvas-anim-disappearance').val(animDis);

            // 5. Sync Canvas Logic Tab & Conditional Logic
            this.checkConditionalLogicEligibility();
            this.populateLogicFieldOptions();

            $('#set-canvas-logic-enable').prop('checked', !!meta.logic_enabled);
            $('#set-canvas-logic-rules-panel').toggle(!!meta.logic_enabled && !$('#set-canvas-logic-enable').prop('disabled'));
            if (meta.logic_field) $('#set-canvas-logic-field').val(meta.logic_field);
            if (meta.logic_operator) $('#set-canvas-logic-operator').val(meta.logic_operator);
            $('#set-canvas-logic-val').val(meta.logic_value || '');
            if (meta.logic_action) $('#set-canvas-logic-action').val(meta.logic_action);
        },

        checkConditionalLogicEligibility: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;

            var cur = core.state.currentCanvas || 1;
            var elements = (core.state.canvases && core.state.canvases[cur]) ? core.state.canvases[cur] : [];
            var count = elements.length;

            $('#set-canvas-element-count-badge').text(count + (count === 1 ? ' Element' : ' Elements'));

            var $toggle = $('#set-canvas-logic-enable');
            var $notice = $('#set-canvas-logic-disabled-notice');
            var $rulesPanel = $('#set-canvas-logic-rules-panel');

            if (count === 0) {
                $toggle.prop('disabled', true).prop('checked', false);
                $notice.show();
                $rulesPanel.hide();
                if (core.state.canvasMeta[cur]) {
                    core.state.canvasMeta[cur].logic_enabled = false;
                }
            } else {
                $toggle.prop('disabled', false);
                $notice.hide();
            }
        },

        populateLogicFieldOptions: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;

            var $select = $('#set-canvas-logic-field');
            $select.empty();

            var addedTokens = [];
            var cur = core.state.currentCanvas || 1;

            Object.keys(core.state.canvases).forEach(function(cNum) {
                var els = core.state.canvases[cNum] || [];
                els.forEach(function(el) {
                    var token = el.field_name || el.type;
                    if (addedTokens.indexOf(token) === -1) {
                        addedTokens.push(token);
                        var optLabel = (el.name || el.type.toUpperCase()) + ' (Canvas ' + cNum + ' &bull; {' + token + '})';
                        $select.append($('<option></option>').val(token).html(optLabel));
                    }
                });
            });

            if (addedTokens.length === 0) {
                $select.append('<option value="">No form fields found</option>');
            }
        },

        bindCanvasMetaInputs: function() {
            var self = this;
            var getMeta = function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return null;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) {
                    core.state.canvasMeta[cur] = { name: 'Canvas ' + cur, width: 640, height: 400 };
                }
                return core.state.canvasMeta[cur];
            };

            // Canvas Name Sync
            $('#set-canvas-name').on('input change', function() {
                var meta = getMeta();
                if (!meta) return;
                var val = $(this).val();
                meta.name = val;

                $('#set-current-canvas-badge').text(val || 'Canvas');
                var cur = window.WpPopPopBuilderCore.state.currentCanvas;
                $('.wppoppop-canvas-tab[data-canvas="' + cur + '"]').text(val || ('Canvas ' + cur));
                self.renderCanvasBullets();
            });

            // Canvas Width / Height Sync
            $('#set-canvas-width, #set-canvas-height').on('input change', function() {
                var meta = getMeta();
                if (!meta) return;
                var w = parseInt($('#set-canvas-width').val(), 10) || 640;
                var h = parseInt($('#set-canvas-height').val(), 10) || 400;

                meta.width = w;
                meta.height = h;

                $('#quick-box-width').val(w);
                $('#quick-box-height').val(h);

                if (window.WpPopPopBuilderCore.state.viewport === 'desktop') {
                    $('#wppoppop-canvas-box').css({ width: w + 'px', height: h + 'px' });
                }
            });

            // Canvas Background Mode & Colors Sync
            $('#set-canvas-bg-mode').on('change', function() {
                var meta = getMeta();
                if (!meta) return;
                var mode = $(this).val();
                meta.bg_mode = mode;

                $('#set-canvas-solid-wrap').toggle(mode === 'solid');
                $('#set-canvas-gradient-wrap').toggle(mode === 'gradient');
                self.applyActiveCanvasBackground();
            });

            $('#set-canvas-bg-color, #set-canvas-grad-color1, #set-canvas-grad-color2, #set-canvas-grad-angle').on('input change', function() {
                var meta = getMeta();
                if (!meta) return;
                meta.bg_color = $('#set-canvas-bg-color').val();
                meta.grad_color1 = $('#set-canvas-grad-color1').val();
                meta.grad_color2 = $('#set-canvas-grad-color2').val();
                meta.grad_angle = parseInt($('#set-canvas-grad-angle').val(), 10) || 135;
                self.applyActiveCanvasBackground();
            });

            // Canvas Animation Setting Sync (Appearance, Duration, Delay, Disappearance)
            $('#set-canvas-anim-appearance, #set-canvas-anim-duration, #set-canvas-anim-delay, #set-canvas-anim-disappearance').on('input change', function() {
                var meta = getMeta();
                if (!meta) return;
                meta.anim_appearance = $('#set-canvas-anim-appearance').val();
                meta.anim_duration = parseInt($('#set-canvas-anim-duration').val(), 10) || 1000;
                meta.anim_delay = parseInt($('#set-canvas-anim-delay').val(), 10) || 0;
                meta.anim_disappearance = $('#set-canvas-anim-disappearance').val();
            });
        },

        applyActiveCanvasBackground: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;
            var cur = core.state.currentCanvas || 1;
            var meta = core.state.canvasMeta[cur] || {};
            var $box = $('#wppoppop-canvas-box');

            var mode = meta.bg_mode || 'solid';
            if (mode === 'gradient') {
                var c1 = meta.grad_color1 || '#3b82f6';
                var c2 = meta.grad_color2 || '#1d4ed8';
                var ang = meta.grad_angle || 135;
                $box.css({ background: 'linear-gradient(' + ang + 'deg, ' + c1 + ', ' + c2 + ')' });
            } else {
                var bg = meta.bg_color || '#ffffff';
                $box.css({ background: bg });
            }
        },

        bindCanvasLogicInputs: function() {
            var getMeta = function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return null;
                var cur = core.state.currentCanvas || 1;
                return core.state.canvasMeta[cur];
            };

            $('#set-canvas-logic-enable').on('change', function() {
                var meta = getMeta();
                if (!meta) return;
                var isEnabled = $(this).is(':checked');
                meta.logic_enabled = isEnabled;
                $('#set-canvas-logic-rules-panel').slideToggle(150, isEnabled);
            });

            $('#set-canvas-logic-field, #set-canvas-logic-operator, #set-canvas-logic-val, #set-canvas-logic-action').on('input change', function() {
                var meta = getMeta();
                if (!meta) return;
                meta.logic_field = $('#set-canvas-logic-field').val();
                meta.logic_operator = $('#set-canvas-logic-operator').val();
                meta.logic_value = $('#set-canvas-logic-val').val();
                meta.logic_action = $('#set-canvas-logic-action').val();
            });
        },

        bindLiveStyle: function() {},

        getSettings: function() {
            return {
                box: {
                    width: parseInt($('#set-canvas-width').val(), 10) || 640,
                    height: parseInt($('#set-canvas-height').val(), 10) || 400
                },
                triggers: {
                    load: $('#trig-load').is(':checked'),
                    load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                    exit: $('#trig-exit').is(':checked'),
                    scroll: $('#trig-scroll').is(':checked'),
                    scroll_val: parseInt($('#trig-scroll-val').val(), 10) || 50,
                    adblock: $('#trig-adblock').is(':checked'),
                    backbutton: $('#trig-backbutton').is(':checked')
                },
                logic_math: {
                    formula: $('#set-math-formula').val() || '',
                    target: $('#set-math-target').val() || ''
                },
                sidetab: {
                    enable: $('#set-sidetab-enable').is(':checked'),
                    label: $('#set-sidetab-label').val() || '',
                    pos: $('#set-sidetab-pos').val() || 'left'
                },
                payments: {
                    gateway: $('#set-pay-gateway').val() || 'stripe',
                    amount: parseFloat($('#set-pay-amount').val()) || 0
                },
                downloads: {
                    enable: $('#set-dl-enable').is(':checked'),
                    url: $('#set-dl-url').val() || ''
                },
                video: {
                    enable: $('#set-vid-enable').is(':checked'),
                    time: parseInt($('#set-vid-time').val(), 10) || 0
                },
                autoresponder: {
                    enable: $('#set-auto-enable').is(':checked'),
                    subject: $('#set-auto-subject').val() || '',
                    body: $('#set-auto-body').val() || ''
                },
                webhooks: {
                    url: $('#set-webhook-url').val() || '',
                    secret: $('#set-webhook-secret').val() || ''
                },
                sms: {
                    enable: $('#set-sms-enable').is(':checked'),
                    phone: $('#set-sms-phone').val() || ''
                },
                targeting: {
                    auth: $('#set-target-auth').val() || 'all'
                },
                frequency: {
                    mode: $('#set-freq-mode').val() || 'always'
                },
                woocommerce: {
                    coupon: $('#set-wc-coupon').is(':checked'),
                    amount: $('#set-wc-amount').val() || ''
                },
                custom_code: {
                    css: $('#set-custom-css').val() || '',
                    js: $('#set-custom-js').val() || ''
                },
                quiz: {
                    enable: $('#set-quiz-enable').is(':checked'),
                    pass_threshold: parseInt($('#set-quiz-pass').val(), 10) || 70,
                    pass_canvas: parseInt($('#set-quiz-pass-canvas').val(), 10) || 2,
                    pass_screen: parseInt($('#set-quiz-pass-canvas').val(), 10) || 2,
                    fail_canvas: parseInt($('#set-quiz-fail-canvas').val(), 10) || 3,
                    fail_screen: parseInt($('#set-quiz-fail-canvas').val(), 10) || 3,
                    confetti: $('#set-quiz-confetti').is(':checked')
                }
            };
        },

        setSettings: function(settings) {
            if (!settings) return;
            if (settings.triggers) {
                $('#trig-load').prop('checked', !!settings.triggers.load);
                $('#trig-load-delay').val(settings.triggers.load_delay || '');
                $('#trig-exit').prop('checked', !!settings.triggers.exit);
                $('#trig-scroll').prop('checked', !!settings.triggers.scroll);
                $('#trig-scroll-val').val(settings.triggers.scroll_val || 50);
                $('#trig-adblock').prop('checked', !!settings.triggers.adblock);
                $('#trig-backbutton').prop('checked', !!settings.triggers.backbutton);
            }
            if (settings.quiz) {
                $('#set-quiz-enable').prop('checked', !!settings.quiz.enable);
                $('#set-quiz-pass').val(settings.quiz.pass_threshold || 70);
                var p = settings.quiz.pass_canvas || settings.quiz.pass_screen || 2;
                var f = settings.quiz.fail_canvas || settings.quiz.fail_screen || 3;
                $('#set-quiz-pass-canvas, #set-quiz-pass-screen').val(p);
                $('#set-quiz-fail-canvas, #set-quiz-fail-screen').val(f);
                $('#set-quiz-confetti').prop('checked', !!settings.quiz.confetti);
            }
        }
    };
})(jQuery);
