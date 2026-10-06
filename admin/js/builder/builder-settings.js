(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Settings = {
        init: function() {
            this.bindToggle();
            this.bindAccordions();
            this.bindLiveInputs();
            this.hydrate(window.wppoppop_initial_config || {});
        },

        bindToggle: function() {
            var self = this;

            // 1. Header Button Toggle Trigger
            $(document).on('click', '#wppoppop-btn-settings', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.toggle();
            });

            // 2. Explicit Close Button Trigger (Supports ID and Class)
            $(document).on('click', '#wppoppop-settings-drawer-close, .wppoppop-drawer-close-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.close();
            });

            // 3. Backdrop Scrim Click Dismissal
            $(document).on('click', '#wppoppop-settings-backdrop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.close();
            });

            // 4. Keyboard Escape Key Dismissal
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    var $drawer = $('#wppoppop-settings-drawer');
                    if ($drawer.hasClass('open') || $drawer.hasClass('active')) {
                        self.close();
                    }
                }
            });

            // 5. Click-Outside Guard Dismissal
            $(document).on('click', function(e) {
                var $drawer = $('#wppoppop-settings-drawer');
                if (($drawer.hasClass('open') || $drawer.hasClass('active')) &&
                    !$(e.target).closest('#wppoppop-settings-drawer').length &&
                    !$(e.target).closest('#wppoppop-btn-settings').length) {
                    self.close();
                }
            });
        },

        open: function() {
            $('#wppoppop-settings-drawer').addClass('open active');
            $('#wppoppop-settings-backdrop').addClass('open active').show();
            $('#wppoppop-btn-settings').addClass('active');
            $(document).trigger('builder:settings:opened');
        },

        close: function() {
            $('#wppoppop-settings-drawer').removeClass('open active');
            $('#wppoppop-settings-backdrop').removeClass('open active').hide();
            $('#wppoppop-btn-settings').removeClass('active');
            $(document).trigger('builder:settings:closed');
        },

        toggle: function() {
            var $drawer = $('#wppoppop-settings-drawer');
            if ($drawer.hasClass('open') || $drawer.hasClass('active')) {
                this.close();
            } else {
                this.open();
            }
        },

        bindAccordions: function() {
            $(document).on('click', '.wppoppop-acc-header', function(e) {
                e.preventDefault();
                var $body = $(this).next('.wppoppop-acc-body');
                $body.slideToggle(200);
            });
        },

        bindLiveInputs: function() {
            var self = this;
            var Core = window.WpPopPopBuilder.Core;

            $('#set-box-width, #set-box-height, #set-bg-mode, #set-bg-color, #set-grad-color1, #set-grad-color2, #set-grad-angle').on('input change', function() {
                self.applyLiveStyles();
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

            $('#set-custom-css').on('input change', function() {
                self.applyCustomCss($(this).val());
                if (Core) Core.isDirty = true;
            });

            $('#wppoppop-settings-drawer input, #wppoppop-settings-drawer select, #wppoppop-settings-drawer textarea').on('input change', function() {
                if (Core) Core.isDirty = true;
            });
        },

        hydrate: function(cfg) {
            var s = cfg.settings || {};

            // 1. Box & Canvas Dimensions
            $('#set-box-width').val(s.width || 640);
            $('#set-box-height').val(s.height || 400);
            $('#set-bg-mode').val(s.bgMode || 'solid');
            $('#set-bg-color').val(s.bgColor || '#ffffff');
            $('#set-grad-color1').val(s.gradColor1 || '#3b82f6');
            $('#set-grad-color2').val(s.gradColor2 || '#1d4ed8');
            $('#set-grad-angle').val(s.gradAngle || 135);

            if (s.bgMode === 'gradient') {
                $('#set-solid-wrap').hide();
                $('#set-gradient-wrap').show();
            } else {
                $('#set-solid-wrap').show();
                $('#set-gradient-wrap').hide();
            }

            // 2. Display Triggers
            var trig = s.triggers || cfg.triggers || {};
            $('#trig-load').prop('checked', trig.on_load !== false);
            $('#trig-load-delay').val(trig.on_load_delay || 0);
            $('#trig-exit').prop('checked', !!trig.on_exit);
            $('#trig-scroll').prop('checked', !!trig.on_scroll);
            $('#trig-scroll-val').val(trig.scroll_val || 50);
            $('#trig-adblock').prop('checked', !!trig.on_adblock);
            $('#trig-backbutton').prop('checked', !!trig.on_backbutton);

            // 3. Math & Logic
            var math = s.math || {};
            $('#set-math-formula').val(math.formula || '');
            $('#set-math-target').val(math.target || '');

            // 4. Side Tab
            var sideTab = s.sideTab || {};
            $('#set-sidetab-enable').prop('checked', !!sideTab.enable);
            $('#set-sidetab-label').val(sideTab.label || '');
            $('#set-sidetab-pos').val(sideTab.position || 'left');

            // 5. Payments
            var pay = s.payments || {};
            $('#set-pay-gateway').val(pay.gateway || 'stripe');
            $('#set-pay-amount').val(pay.amount || 19.99);

            // 6. Downloads
            var dl = s.downloads || {};
            $('#set-dl-enable').prop('checked', !!dl.enable);
            $('#set-dl-url').val(dl.url || '');

            // 7. Video
            var vid = s.video || {};
            $('#set-vid-enable').prop('checked', !!vid.enable);
            $('#set-vid-time').val(vid.time || 30);

            // 8. Autoresponder
            var auto = s.autoresponder || {};
            $('#set-auto-enable').prop('checked', !!auto.enable);
            $('#set-auto-subject').val(auto.subject || '');
            $('#set-auto-body').val(auto.body || '');

            // 9. Webhooks
            var hook = s.webhooks || {};
            $('#set-webhook-url').val(hook.url || '');
            $('#set-webhook-secret').val(hook.secret || '');

            // 10. SMS
            var sms = s.sms || {};
            $('#set-sms-enable').prop('checked', !!sms.enable);
            $('#set-sms-phone').val(sms.phone || '');

            // 11. Targeting
            var targ = s.targeting || {};
            $('#set-target-auth').val(targ.auth || 'all');

            // 12. Frequency
            var freq = s.frequency || {};
            $('#set-freq-mode').val(freq.mode || 'always');

            // 13. WooCommerce
            var wc = s.woocommerce || {};
            $('#set-wc-coupon').prop('checked', !!wc.coupon);
            $('#set-wc-amount').val(wc.amount || 15);

            // 14. Scoped CSS & JS
            $('#set-custom-css').val(s.customCss || cfg.custom_css || '');
            $('#set-custom-js').val(s.customJs || cfg.custom_js || '');

            // 15. Quiz Scoring
            var quiz = s.quiz || {};
            $('#set-quiz-enable').prop('checked', !!quiz.enable);
            $('#set-quiz-pass').val(quiz.passScore || 70);
            $('#set-quiz-confetti').prop('checked', quiz.confetti !== false);

            this.applyLiveStyles();
            this.applyCustomCss($('#set-custom-css').val());
        },

        applyLiveStyles: function() {
            var w = parseInt($('#set-box-width').val(), 10) || 640;
            var h = parseInt($('#set-box-height').val(), 10) || 400;
            var mode = $('#set-bg-mode').val();
            var $box = $('#wppoppop-canvas-box');

            if (window.WpPopPopBuilder && window.WpPopPopBuilder.Core && window.WpPopPopBuilder.Core.viewport !== 'mobile') {
                $box.css({ width: w + 'px', height: h + 'px' });
            }

            if (mode === 'gradient') {
                var c1 = $('#set-grad-color1').val() || '#3b82f6';
                var c2 = $('#set-grad-color2').val() || '#1d4ed8';
                var deg = $('#set-grad-angle').val() || 135;
                $box.css('background', 'linear-gradient(' + deg + 'deg, ' + c1 + ', ' + c2 + ')');
            } else {
                var solid = $('#set-bg-color').val() || '#ffffff';
                $box.css('background', solid);
            }
        },

        applyCustomCss: function(css) {
            $('#wppoppop-custom-css-preview').remove();
            if (css && css.trim()) {
                $('head').append('<style id="wppoppop-custom-css-preview">' + css + '</style>');
            }
        },

        getSettings: function() {
            return {
                width: parseInt($('#set-box-width').val(), 10) || 640,
                height: parseInt($('#set-box-height').val(), 10) || 400,
                bgMode: $('#set-bg-mode').val() || 'solid',
                bgColor: $('#set-bg-color').val() || '#ffffff',
                gradColor1: $('#set-grad-color1').val() || '#3b82f6',
                gradColor2: $('#set-grad-color2').val() || '#1d4ed8',
                gradAngle: parseInt($('#set-grad-angle').val(), 10) || 135,
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
