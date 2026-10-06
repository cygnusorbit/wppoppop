(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Settings = {
        activeSettingsScreen: 1,

        init: function() {
            this.bindToggle();
            this.bindAccordions();
            this.bindSubtabs();
            this.bindLiveInputs();
            this.bindBulletNav();
            this.bindElementsListener();
            this.hydrate(window.wppoppop_initial_config || {});
        },

        bindToggle: function() {
            var self = this;

            $(document).on('click', '#wppoppop-btn-settings', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.toggle();
            });

            $(document).on('click', '#wppoppop-settings-drawer-close, .wppoppop-drawer-close-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.close();
            });

            $(document).on('click', '#wppoppop-settings-backdrop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.close();
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    if ($('#wppoppop-settings-drawer').hasClass('open')) {
                        self.close();
                    }
                }
            });
        },

        open: function() {
            this.renderScreenBullets();
            $('#wppoppop-settings-drawer').addClass('open');
            $('#wppoppop-settings-backdrop').addClass('open');
            $('#wppoppop-btn-settings').addClass('active');
            $(document).trigger('builder:settings:opened');
        },

        close: function() {
            $('#wppoppop-settings-drawer').removeClass('open');
            $('#wppoppop-settings-backdrop').removeClass('open');
            $('#wppoppop-btn-settings').removeClass('active');
            $(document).trigger('builder:settings:closed');
        },

        toggle: function() {
            if ($('#wppoppop-settings-drawer').hasClass('open')) {
                this.close();
            } else {
                this.open();
            }
        },

        bindAccordions: function() {
            $(document).on('click', '.wppoppop-acc-header', function(e) {
                e.preventDefault();
                var $header = $(this);
                var $body = $header.next('.wppoppop-acc-body');
                $header.toggleClass('active');
                $body.slideToggle(200);
            });
        },

        bindSubtabs: function() {
            var self = this;
            $(document).on('click', '.wppoppop-screen-subtab', function(e) {
                e.preventDefault();
                var subtab = $(this).data('subtab');

                $('.wppoppop-screen-subtab').removeClass('active').css({ background: 'transparent', color: '#94a3b8' });
                $(this).addClass('active').css({ background: '#2563eb', color: '#ffffff' });

                if (subtab === 'logic') {
                    $('#wppoppop-subtab-pane-canvas').hide();
                    $('#wppoppop-subtab-pane-logic').show();
                    self.checkScreenEmptyState(self.activeSettingsScreen);
                    self.populateScreenLogicFields(self.activeSettingsScreen);
                    self.populateScreenTargetDropdowns();
                } else {
                    $('#wppoppop-subtab-pane-canvas').show();
                    $('#wppoppop-subtab-pane-logic').hide();
                }
            });
        },

        bindBulletNav: function() {
            var self = this;

            $(document).on('click', '.wppoppop-screen-bullet', function(e) {
                e.preventDefault();
                var sId = parseInt($(this).data('screen'), 10);
                self.setActiveScreen(sId);
            });

            $(document).on('builder:screens:rendered builder:screen:change', function() {
                self.renderScreenBullets();
            });
        },

        bindElementsListener: function() {
            var self = this;
            $(document).on('builder:elements:updated', function() {
                self.checkScreenEmptyState(self.activeSettingsScreen);
                self.populateScreenLogicFields(self.activeSettingsScreen);
            });
        },

        renderScreenBullets: function() {
            var Core = window.WpPopPopBuilder.Core;
            if (!Core || !Array.isArray(Core.screens)) return;

            var $container = $('#wppoppop-screen-settings-bullets');
            $container.empty();

            var self = this;
            if (!this.activeSettingsScreen) {
                this.activeSettingsScreen = Core.currentScreen;
            }

            Core.screens.forEach(function(sc) {
                var isActive = (sc.id === self.activeSettingsScreen);
                var $btn = $('<button type="button" class="wppoppop-screen-bullet' + (isActive ? ' active' : '') + '" data-screen="' + sc.id + '">' +
                    '<span class="bullet-dot"></span> <span class="bullet-title">' + sc.title + '</span>' +
                '</button>');
                $container.append($btn);
            });

            var activeSc = Core.screens.find(function(s) { return s.id === self.activeSettingsScreen; }) || Core.screens[0];
            if (activeSc) {
                this.loadScreenData(activeSc);
            }
        },

        setActiveScreen: function(sId) {
            var Core = window.WpPopPopBuilder.Core;
            this.activeSettingsScreen = sId;

            $('.wppoppop-screen-bullet').removeClass('active');
            $('.wppoppop-screen-bullet[data-screen="' + sId + '"]').addClass('active');

            var sc = Core.screens.find(function(s) { return s.id === sId; });
            if (sc) {
                this.loadScreenData(sc);
                if (Core.currentScreen !== sId) {
                    Core.setScreen(sId);
                }
            }
        },

        checkScreenEmptyState: function(sId) {
            var Core = window.WpPopPopBuilder.Core;
            if (!Core || !Array.isArray(Core.elements)) return false;

            var screenElements = Core.elements.filter(function(e) {
                return e.screen === sId;
            });

            var isEmpty = (screenElements.length === 0);

            if (isEmpty) {
                $('#set-screen-cond-enable')
                    .prop('checked', false)
                    .prop('disabled', true);
                $('#wppoppop-logic-toggle-label').addClass('disabled');
                $('#set-screen-cond-box').hide();
                $('#set-screen-empty-notice').show();

                var sc = Core.screens.find(function(s) { return s.id === sId; });
                if (sc && sc.logic) {
                    sc.logic.enable = false;
                }
            } else {
                $('#set-screen-empty-notice').hide();
                $('#set-screen-cond-enable').prop('disabled', false);
                $('#wppoppop-logic-toggle-label').removeClass('disabled');
            }

            return isEmpty;
        },

        loadScreenData: function(sc) {
            var self = this;
            $('#set-screen-title').val(sc.title || ('Screen ' + sc.id));

            $('#set-box-width').val(sc.width || 640);
            $('#set-box-height').val(sc.height || 400);
            $('#set-bg-mode').val(sc.bgMode || 'solid');

            var bgColor = sc.bgColor || '#ffffff';
            var gradColor1 = sc.gradColor1 || '#3b82f6';
            var gradColor2 = sc.gradColor2 || '#1d4ed8';

            $('#set-bg-color').val(bgColor);
            $('.wppoppop-color-swatch-input[data-target="#set-bg-color"]').val(bgColor.indexOf('#') === 0 ? bgColor : '#ffffff');

            $('#set-grad-color1').val(gradColor1);
            $('.wppoppop-color-swatch-input[data-target="#set-grad-color1"]').val(gradColor1.indexOf('#') === 0 ? gradColor1 : '#3b82f6');

            $('#set-grad-color2').val(gradColor2);
            $('.wppoppop-color-swatch-input[data-target="#set-grad-color2"]').val(gradColor2.indexOf('#') === 0 ? gradColor2 : '#1d4ed8');

            $('#set-grad-angle').val(sc.gradAngle || 135);

            if (sc.bgMode === 'gradient') {
                $('#set-solid-wrap').hide();
                $('#set-gradient-wrap').show();
            } else {
                $('#set-solid-wrap').show();
                $('#set-gradient-wrap').hide();
            }

            // Load Animate.style values with fallback for legacy classes
            var animInVal = sc.animIn || 'animate__fadeIn';
            if (animInVal === 'fade') animInVal = 'animate__fadeIn';
            if (animInVal === 'slideDown') animInVal = 'animate__slideInDown';
            if (animInVal === 'bounceIn') animInVal = 'animate__bounceIn';
            if (animInVal === 'zoomIn') animInVal = 'animate__zoomIn';
            if (animInVal === 'flipIn') animInVal = 'animate__flipInX';

            var animOutVal = sc.animOut || 'animate__fadeOut';
            if (animOutVal === 'fade') animOutVal = 'animate__fadeOut';
            if (animOutVal === 'slideUp') animOutVal = 'animate__slideOutUp';
            if (animOutVal === 'zoomOut') animOutVal = 'animate__zoomOut';
            if (animOutVal === 'flipOut') animOutVal = 'animate__flipOutX';

            $('#set-screen-anim-in').val(animInVal);
            $('#set-screen-anim-duration').val(sc.animInDuration !== undefined ? sc.animInDuration : 1000);
            $('#set-screen-anim-delay').val(sc.animInDelay !== undefined ? sc.animInDelay : 0);
            $('#set-screen-anim-out').val(animOutVal);

            var isEmpty = this.checkScreenEmptyState(sc.id);

            var log = sc.logic || {};
            var isCondEnabled = !isEmpty && !!log.enable;

            $('#set-screen-cond-enable').prop('checked', isCondEnabled);
            $('#set-screen-cond-box').toggle(isCondEnabled);

            this.populateScreenLogicFields(sc.id);
            if (log.field) $('#set-screen-cond-field').val(log.field);
            $('#set-screen-cond-operator').val(log.operator || 'equals');
            $('#set-screen-cond-val').val(log.val || '');

            this.populateScreenTargetDropdowns();
            if (log.targetScreen) $('#set-screen-cond-target').val(log.targetScreen);
            if (log.fallback) $('#set-screen-cond-fallback').val(log.fallback);

            var op = log.operator || 'equals';
            $('#set-screen-cond-val-wrap').toggle(op !== 'is_empty' && op !== 'is_not_empty');
        },

        populateScreenLogicFields: function(screenId) {
            var Core = window.WpPopPopBuilder.Core;
            if (!Core) return;

            var $fieldSelect = $('#set-screen-cond-field').empty();
            var inputTypes = ['email', 'number', 'text', 'select', 'radios', 'checkboxes', 'rating', 'slider', 'date'];

            var screenElements = Core.elements.filter(function(e) {
                return e.screen === screenId && inputTypes.indexOf(e.type) !== -1;
            });

            if (screenElements.length === 0) {
                $fieldSelect.append('<option value="">(No form elements found on Screen ' + screenId + ')</option>');
            } else {
                screenElements.forEach(function(el) {
                    var label = el.label || el.content || el.type;
                    $fieldSelect.append('<option value="' + el.id + '">[' + el.type.toUpperCase() + '] ' + label + '</option>');
                });
            }
        },

        populateScreenTargetDropdowns: function() {
            var Core = window.WpPopPopBuilder.Core;
            if (!Core || !Array.isArray(Core.screens)) return;

            var $target = $('#set-screen-cond-target').empty();
            var $fallback = $('#set-screen-cond-fallback').empty();

            $fallback.append('<option value="next_screen">Proceed to Next Screen</option>');
            $fallback.append('<option value="close">Close Popup</option>');

            Core.screens.forEach(function(sc) {
                var opt = '<option value="' + sc.id + '">' + sc.title + '</option>';
                $target.append(opt);
                $fallback.append(opt);
            });
        },

        bindLiveInputs: function() {
            var self = this;
            var Core = window.WpPopPopBuilder.Core;

            $(document).on('input change', '.wppoppop-color-swatch-input', function() {
                var targetSelector = $(this).data('target');
                var hexVal = $(this).val();
                if (targetSelector && $(targetSelector).length) {
                    $(targetSelector).val(hexVal).trigger('input');
                }
            });

            $(document).on('input change', '.wppoppop-color-hex-input', function() {
                var hexVal = $(this).val().trim();
                var inputId = $(this).attr('id');
                var $swatch = $('.wppoppop-color-swatch-input[data-target="#' + inputId + '"]');

                if (!$swatch.length) {
                    $swatch = $(this).closest('.wppoppop-color-picker-row').find('.wppoppop-color-swatch-input');
                }

                if (/^#([0-9A-Fa-f]{3}){1,2}$/.test(hexVal)) {
                    if (hexVal.length === 4) {
                        hexVal = '#' + hexVal[1] + hexVal[1] + hexVal[2] + hexVal[2] + hexVal[3] + hexVal[3];
                    }
                    $swatch.val(hexVal);
                }
            });

            $('#set-screen-title').on('input change', function() {
                var newTitle = $(this).val().trim() || ('Screen ' + self.activeSettingsScreen);
                if (Core) {
                    Core.renameScreen(self.activeSettingsScreen, newTitle);
                }
            });

            $('#set-box-width, #set-box-height, #set-bg-mode, #set-bg-color, #set-grad-color1, #set-grad-color2, #set-grad-angle').on('input change', function() {
                self.saveCurrentScreenData();
                self.applyLiveStyles();
                if (Core) Core.isDirty = true;
            });

            // Live Animate.style Preview Listeners
            $('#set-screen-anim-in, #set-screen-anim-duration, #set-screen-anim-delay, #set-screen-anim-out').on('input change', function() {
                self.saveCurrentScreenData();
                self.previewAnimation();
                if (Core) Core.isDirty = true;
            });

            $('#set-bg-mode').on('change', function() {
                var mode = $(this).val();
                if (mode === 'gradient') {
                    $('#set-solid-wrap').hide();
                    $('#set-gradient-wrap').show();
                } else {
                    $('#set-solid-wrap').show();
                    $('#set-gradient-wrap').hide();
                }
            });

            $('#set-screen-cond-enable').on('change', function() {
                var isEnabled = $(this).is(':checked');
                if (self.checkScreenEmptyState(self.activeSettingsScreen)) {
                    $(this).prop('checked', false);
                    return;
                }
                $('#set-screen-cond-box').slideToggle(150, function() {
                    $(this).toggle(isEnabled);
                });
                self.saveCurrentScreenData();
                if (Core) Core.isDirty = true;
            });

            $('#set-screen-cond-operator').on('change', function() {
                var op = $(this).val();
                $('#set-screen-cond-val-wrap').toggle(op !== 'is_empty' && op !== 'is_not_empty');
                self.saveCurrentScreenData();
                if (Core) Core.isDirty = true;
            });

            $('#set-screen-cond-field, #set-screen-cond-val, #set-screen-cond-target, #set-screen-cond-fallback').on('input change', function() {
                self.saveCurrentScreenData();
                if (Core) Core.isDirty = true;
            });

            $('#set-custom-css').on('input change', function() {
                self.applyCustomCss($(this).val());
                if (Core) Core.isDirty = true;
            });

            $('#wppoppop-settings-drawer input, #wppoppop-settings-drawer select, #wppoppop-settings-drawer textarea').on('input change', function() {
                if (Core) Core.isDirty = true;
            });
        },

        previewAnimation: function() {
            var animIn = $('#set-screen-anim-in').val();
            var duration = parseInt($('#set-screen-anim-duration').val(), 10) || 1000;
            var delay = parseInt($('#set-screen-anim-delay').val(), 10) || 0;
            var $box = $('#wppoppop-canvas-box');

            // Strip existing animate.css classes and properties
            $box.removeClass(function(i, c) {
                return (c.match(/(^|\s)animate__\S+/g) || []).join(' ');
            }).css({
                '--animate-duration': (duration / 1000) + 's',
                'animation-delay': (delay / 1000) + 's'
            });

            if (!animIn || animIn === 'none') {
                return;
            }

            setTimeout(function() {
                $box.addClass('animate__animated ' + animIn);
            }, 30);
        },

        saveCurrentScreenData: function() {
            var Core = window.WpPopPopBuilder.Core;
            if (!Core || !Array.isArray(Core.screens)) return;

            var self = this;
            var sc = Core.screens.find(function(s) { return s.id === self.activeSettingsScreen; }) || Core.getCurrentScreenObj();
            if (sc) {
                sc.width = parseInt($('#set-box-width').val(), 10) || 640;
                sc.height = parseInt($('#set-box-height').val(), 10) || 400;
                sc.bgMode = $('#set-bg-mode').val() || 'solid';
                sc.bgColor = $('#set-bg-color').val() || '#ffffff';
                sc.gradColor1 = $('#set-grad-color1').val() || '#3b82f6';
                sc.gradColor2 = $('#set-grad-color2').val() || '#1d4ed8';
                sc.gradAngle = parseInt($('#set-grad-angle').val(), 10) || 135;

                sc.animIn = $('#set-screen-anim-in').val() || 'animate__fadeIn';
                sc.animInDuration = parseInt($('#set-screen-anim-duration').val(), 10) || 1000;
                sc.animInDelay = parseInt($('#set-screen-anim-delay').val(), 10) || 0;
                sc.animOut = $('#set-screen-anim-out').val() || 'animate__fadeOut';

                sc.logic = {
                    enable: $('#set-screen-cond-enable').is(':checked') && !self.checkScreenEmptyState(sc.id),
                    field: $('#set-screen-cond-field').val() || '',
                    operator: $('#set-screen-cond-operator').val() || 'equals',
                    val: $('#set-screen-cond-val').val() || '',
                    targetScreen: parseInt($('#set-screen-cond-target').val(), 10) || 2,
                    fallback: $('#set-screen-cond-fallback').val() || 'next_screen'
                };
            }
        },

        applyLiveStyles: function() {
            var Core = window.WpPopPopBuilder.Core;
            if (!Core) return;

            var sc = Core.getCurrentScreenObj();
            if (!sc) return;

            var $box = $('#wppoppop-canvas-box');

            if (Core.viewport !== 'mobile') {
                $box.css({ width: (sc.width || 640) + 'px', height: (sc.height || 400) + 'px' });
            }

            if (sc.bgMode === 'gradient') {
                $box.css('background', 'linear-gradient(' + (sc.gradAngle || 135) + 'deg, ' + (sc.gradColor1 || '#3b82f6') + ', ' + (sc.gradColor2 || '#1d4ed8') + ')');
            } else {
                $box.css('background', sc.bgColor || '#ffffff');
            }
        },

        applyCustomCss: function(css) {
            $('#wppoppop-custom-css-preview').remove();
            if (css && css.trim()) {
                $('head').append('<style id="wppoppop-custom-css-preview">' + css + '</style>');
            }
        },

        hydrate: function(cfg) {
            var s = cfg.settings || {};

            var trig = s.triggers || cfg.triggers || {};
            $('#trig-load').prop('checked', trig.on_load !== false);
            $('#trig-load-delay').val(trig.on_load_delay || 0);
            $('#trig-exit').prop('checked', !!trig.on_exit);
            $('#trig-scroll').prop('checked', !!trig.on_scroll);
            $('#trig-scroll-val').val(trig.scroll_val || 50);
            $('#trig-adblock').prop('checked', !!trig.on_adblock);
            $('#trig-backbutton').prop('checked', !!trig.on_backbutton);

            var math = s.math || {};
            $('#set-math-formula').val(math.formula || '');
            $('#set-math-target').val(math.target || '');

            var sideTab = s.sideTab || {};
            $('#set-sidetab-enable').prop('checked', !!sideTab.enable);
            $('#set-sidetab-label').val(sideTab.label || '');
            $('#set-sidetab-pos').val(sideTab.position || 'left');

            var pay = s.payments || {};
            $('#set-pay-gateway').val(pay.gateway || 'stripe');
            $('#set-pay-amount').val(pay.amount || 19.99);

            var dl = s.downloads || {};
            $('#set-dl-enable').prop('checked', !!dl.enable);
            $('#set-dl-url').val(dl.url || '');

            var vid = s.video || {};
            $('#set-vid-enable').prop('checked', !!vid.enable);
            $('#set-vid-time').val(vid.time || 30);

            var auto = s.autoresponder || {};
            $('#set-auto-enable').prop('checked', !!auto.enable);
            $('#set-auto-subject').val(auto.subject || '');
            $('#set-auto-body').val(auto.body || '');

            var hook = s.webhooks || {};
            $('#set-webhook-url').val(hook.url || '');
            $('#set-webhook-secret').val(hook.secret || '');

            var sms = s.sms || {};
            $('#set-sms-enable').prop('checked', !!sms.enable);
            $('#set-sms-phone').val(sms.phone || '');

            var targ = s.targeting || {};
            $('#set-target-auth').val(targ.auth || 'all');

            var freq = s.frequency || {};
            $('#set-freq-mode').val(freq.mode || 'always');

            var wc = s.woocommerce || {};
            $('#set-wc-coupon').prop('checked', !!wc.coupon);
            $('#set-wc-amount').val(wc.amount || 15);

            $('#set-custom-css').val(s.customCss || cfg.custom_css || '');
            $('#set-custom-js').val(s.customJs || cfg.custom_js || '');

            var quiz = s.quiz || {};
            $('#set-quiz-enable').prop('checked', !!quiz.enable);
            $('#set-quiz-pass').val(quiz.passScore || 70);
            $('#set-quiz-confetti').prop('checked', quiz.confetti !== false);

            this.renderScreenBullets();
            this.applyLiveStyles();
            this.applyCustomCss($('#set-custom-css').val());
        },

        getSettings: function() {
            var Core = window.WpPopPopBuilder.Core;
            var currentSc = Core ? Core.getCurrentScreenObj() : null;

            return {
                width: currentSc ? currentSc.width : (parseInt($('#set-box-width').val(), 10) || 640),
                height: currentSc ? currentSc.height : (parseInt($('#set-box-height').val(), 10) || 400),
                bgMode: currentSc ? currentSc.bgMode : ($('#set-bg-mode').val() || 'solid'),
                bgColor: currentSc ? currentSc.bgColor : ($('#set-bg-color').val() || '#ffffff'),
                gradColor1: currentSc ? currentSc.gradColor1 : ($('#set-grad-color1').val() || '#3b82f6'),
                gradColor2: currentSc ? currentSc.gradColor2 : ($('#set-grad-color2').val() || '#1d4ed8'),
                gradAngle: currentSc ? currentSc.gradAngle : (parseInt($('#set-grad-angle').val(), 10) || 135),
                triggers: {
                    on_load: $('#trig-load').is(':checked'),
                    on_load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                    on_exit: $('#trig-exit').is(':checked'),
                    on_scroll: $('#trig-scroll').is(':checked'),
                    scroll_val: parseInt($('#trig-scroll-val').val(), 10) || 50,
                    on_adblock: $('#trig-adblock').is(':checked'),
                    on_backbutton: $('#trig-backbutton').is(':checked')
                },
                math: {
                    formula: $('#set-math-formula').val() || '',
                    target: $('#set-math-target').val() || ''
                },
                sideTab: {
                    enable: $('#set-sidetab-enable').is(':checked'),
                    label: $('#set-sidetab-label').val() || '',
                    position: $('#set-sidetab-pos').val() || 'left'
                },
                payments: {
                    gateway: $('#set-pay-gateway').val() || 'stripe',
                    amount: parseFloat($('#set-pay-amount').val()) || 19.99
                },
                downloads: {
                    enable: $('#set-dl-enable').is(':checked'),
                    url: $('#set-dl-url').val() || ''
                },
                video: {
                    enable: $('#set-vid-enable').is(':checked'),
                    time: parseInt($('#set-vid-time').val(), 10) || 30
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
                    amount: parseFloat($('#set-wc-amount').val()) || 15
                },
                customCss: $('#set-custom-css').val() || '',
                customJs: $('#set-custom-js').val() || '',
                quiz: {
                    enable: $('#set-quiz-enable').is(':checked'),
                    passScore: parseInt($('#set-quiz-pass').val(), 10) || 70,
                    confetti: $('#set-quiz-confetti').is(':checked')
                }
            };
        }
    };

    window.WpPopPopBuilder.Settings = Settings;
})(window, jQuery);
