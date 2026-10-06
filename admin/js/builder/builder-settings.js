(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Settings = {
        init: function() {
            this.bindDrawer();
            this.bindAccordions();
            this.bindBoxStyling();
            this.bindFormInputs();
        },

        bindDrawer: function() {
            $('#wppoppop-btn-settings').on('click', function(e) {
                e.preventDefault();
                $('#wppoppop-settings-drawer').css('display', 'flex');
            });

            $('#wppoppop-settings-drawer-close').on('click', function(e) {
                e.preventDefault();
                $('#wppoppop-settings-drawer').hide();
            });
        },

        bindAccordions: function() {
            $(document).on('click', '.wppoppop-acc-header', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $body = $btn.next('.wppoppop-acc-body');
                $btn.toggleClass('active');
                $body.slideToggle(180);
            });
        },

        bindBoxStyling: function() {
            var self = this;
            var core = window.WpPopPopBuilder.Core;

            $('#set-box-width, #set-box-height').on('input change', function() {
                var w = parseInt($('#set-box-width').val(), 10) || 640;
                var h = parseInt($('#set-box-height').val(), 10) || 400;
                if (core && core.state) {
                    core.state.config.meta.width = w;
                    core.state.config.meta.height = h;
                    if (core.state.viewport === 'desktop') {
                        $('#wppoppop-canvas-box').css({ width: w + 'px', height: h + 'px' });
                    }
                }
            });

            $('#set-bg-mode').on('change', function() {
                var mode = $(this).val();
                if (core && core.state) core.state.config.meta.bgMode = mode;
                if (mode === 'gradient') {
                    $('#set-solid-wrap').hide();
                    $('#set-gradient-wrap').show();
                    self.applyGradient();
                } else {
                    $('#set-solid-wrap').show();
                    $('#set-gradient-wrap').hide();
                    var color = $('#set-bg-color').val() || '#ffffff';
                    $('#wppoppop-canvas-box').css('background', color);
                }
            });

            $('#set-bg-color').on('input change', function() {
                var col = $(this).val();
                if (core && core.state) core.state.config.meta.bgColor = col;
                $('#wppoppop-canvas-box').css('background', col);
            });

            $('#set-grad-color1, #set-grad-color2, #set-grad-angle').on('input change', function() {
                self.applyGradient();
            });
        },

        applyGradient: function() {
            var c1 = $('#set-grad-color1').val() || '#3b82f6';
            var c2 = $('#set-grad-color2').val() || '#1d4ed8';
            var deg = parseInt($('#set-grad-angle').val(), 10) || 135;
            var core = window.WpPopPopBuilder.Core;
            if (core && core.state) {
                core.state.config.meta.gradColor1 = c1;
                core.state.config.meta.gradColor2 = c2;
                core.state.config.meta.gradAngle = deg;
            }
            $('#wppoppop-canvas-box').css('background', 'linear-gradient(' + deg + 'deg, ' + c1 + ', ' + c2 + ')');
        },

        bindFormInputs: function() {
            var core = window.WpPopPopBuilder.Core;
            if (!core) return;
            var cfg = core.state.config;

            $('#trig-load, #trig-load-delay, #trig-exit, #trig-scroll, #trig-scroll-val, #trig-adblock, #trig-backbutton').on('change input', function() {
                cfg.triggers.onLoad = $('#trig-load').is(':checked');
                cfg.triggers.onLoadDelay = parseInt($('#trig-load-delay').val(), 10) || 0;
                cfg.triggers.onExit = $('#trig-exit').is(':checked');
                cfg.triggers.onScroll = $('#trig-scroll').is(':checked');
                cfg.triggers.scrollVal = parseInt($('#trig-scroll-val').val(), 10) || 50;
                cfg.triggers.onAdblock = $('#trig-adblock').is(':checked');
                cfg.triggers.onBackButton = $('#trig-backbutton').is(':checked');
            });

            $('#set-math-formula, #set-math-target').on('input', function() {
                cfg.logic.formula = $('#set-math-formula').val();
                cfg.logic.targetId = $('#set-math-target').val();
            });

            $('#set-sidetab-enable, #set-sidetab-label, #set-sidetab-pos').on('change input', function() {
                cfg.sideTab.enable = $('#set-sidetab-enable').is(':checked');
                cfg.sideTab.label = $('#set-sidetab-label').val();
                cfg.sideTab.pos = $('#set-sidetab-pos').val();
            });

            $('#set-pay-gateway, #set-pay-amount').on('change input', function() {
                cfg.payment.gateway = $('#set-pay-gateway').val();
                cfg.payment.amount = parseFloat($('#set-pay-amount').val()) || 0;
            });

            $('#set-dl-enable, #set-dl-url').on('change input', function() {
                cfg.downloads.enable = $('#set-dl-enable').is(':checked');
                cfg.downloads.url = $('#set-dl-url').val();
            });

            $('#set-vid-enable, #set-vid-time').on('change input', function() {
                cfg.video.enable = $('#set-vid-enable').is(':checked');
                cfg.video.time = parseInt($('#set-vid-time').val(), 10) || 0;
            });

            $('#set-auto-enable, #set-auto-subject, #set-auto-body').on('change input', function() {
                cfg.autoresponder.enable = $('#set-auto-enable').is(':checked');
                cfg.autoresponder.subject = $('#set-auto-subject').val();
                cfg.autoresponder.body = $('#set-auto-body').val();
            });

            $('#set-webhook-url, #set-webhook-secret').on('input', function() {
                cfg.marketing.webhookUrl = $('#set-webhook-url').val();
                cfg.marketing.webhookSecret = $('#set-webhook-secret').val();
            });

            $('#set-sms-enable, #set-sms-phone').on('change input', function() {
                cfg.twilio.enable = $('#set-sms-enable').is(':checked');
                cfg.twilio.phone = $('#set-sms-phone').val();
            });

            $('#set-target-auth').on('change', function() {
                cfg.targeting.auth = $('#set-target-auth').val();
            });

            $('#set-freq-mode').on('change', function() {
                cfg.frequency.mode = $('#set-freq-mode').val();
            });

            $('#set-wc-coupon, #set-wc-amount').on('change input', function() {
                cfg.woocommerce.enableCoupon = $('#set-wc-coupon').is(':checked');
                cfg.woocommerce.couponAmount = $('#set-wc-amount').val();
            });

            $('#set-custom-css, #set-custom-js').on('input', function() {
                cfg.customCode.css = $('#set-custom-css').val();
                cfg.customCode.js = $('#set-custom-js').val();
            });

            $('#set-quiz-enable, #set-quiz-pass, #set-quiz-confetti').on('change input', function() {
                cfg.quiz.enable = $('#set-quiz-enable').is(':checked');
                cfg.quiz.passScore = parseInt($('#set-quiz-pass').val(), 10) || 0;
                cfg.quiz.confetti = $('#set-quiz-confetti').is(':checked');
            });
        },

        populateFromConfig: function(cfg) {
            if (!cfg) return;
            if (cfg.meta) {
                $('#set-box-width').val(cfg.meta.width || 640);
                $('#set-box-height').val(cfg.meta.height || 400);
                $('#set-bg-mode').val(cfg.meta.bgMode || 'solid');
                if (cfg.meta.bgMode === 'gradient') {
                    $('#set-solid-wrap').hide();
                    $('#set-gradient-wrap').show();
                    $('#set-grad-color1').val(cfg.meta.gradColor1 || '#3b82f6');
                    $('#set-grad-color2').val(cfg.meta.gradColor2 || '#1d4ed8');
                    $('#set-grad-angle').val(cfg.meta.gradAngle || 135);
                    this.applyGradient();
                } else {
                    $('#set-solid-wrap').show();
                    $('#set-gradient-wrap').hide();
                    $('#set-bg-color').val(cfg.meta.bgColor || '#ffffff');
                    $('#wppoppop-canvas-box').css('background', cfg.meta.bgColor || '#ffffff');
                }
                if (window.WpPopPopBuilder.Core && window.WpPopPopBuilder.Core.state.viewport === 'desktop') {
                    $('#wppoppop-canvas-box').css({ width: (cfg.meta.width || 640) + 'px', height: (cfg.meta.height || 400) + 'px' });
                }
            }
            if (cfg.triggers) {
                $('#trig-load').prop('checked', !!cfg.triggers.onLoad);
                $('#trig-load-delay').val(cfg.triggers.onLoadDelay || '');
                $('#trig-exit').prop('checked', !!cfg.triggers.onExit);
                $('#trig-scroll').prop('checked', !!cfg.triggers.onScroll);
                $('#trig-scroll-val').val(cfg.triggers.scrollVal || '');
                $('#trig-adblock').prop('checked', !!cfg.triggers.onAdblock);
                $('#trig-backbutton').prop('checked', !!cfg.triggers.onBackButton);
            }
            if (cfg.logic) {
                $('#set-math-formula').val(cfg.logic.formula || '');
                $('#set-math-target').val(cfg.logic.targetId || '');
            }
            if (cfg.sideTab) {
                $('#set-sidetab-enable').prop('checked', !!cfg.sideTab.enable);
                $('#set-sidetab-label').val(cfg.sideTab.label || '');
                $('#set-sidetab-pos').val(cfg.sideTab.pos || 'left');
            }
            if (cfg.payment) {
                $('#set-pay-gateway').val(cfg.payment.gateway || 'stripe');
                $('#set-pay-amount').val(cfg.payment.amount || '');
            }
            if (cfg.downloads) {
                $('#set-dl-enable').prop('checked', !!cfg.downloads.enable);
                $('#set-dl-url').val(cfg.downloads.url || '');
            }
            if (cfg.video) {
                $('#set-vid-enable').prop('checked', !!cfg.video.enable);
                $('#set-vid-time').val(cfg.video.time || '');
            }
            if (cfg.autoresponder) {
                $('#set-auto-enable').prop('checked', !!cfg.autoresponder.enable);
                $('#set-auto-subject').val(cfg.autoresponder.subject || '');
                $('#set-auto-body').val(cfg.autoresponder.body || '');
            }
            if (cfg.marketing) {
                $('#set-webhook-url').val(cfg.marketing.webhookUrl || '');
                $('#set-webhook-secret').val(cfg.marketing.webhookSecret || '');
            }
            if (cfg.twilio) {
                $('#set-sms-enable').prop('checked', !!cfg.twilio.enable);
                $('#set-sms-phone').val(cfg.twilio.phone || '');
            }
            if (cfg.targeting) {
                $('#set-target-auth').val(cfg.targeting.auth || 'all');
            }
            if (cfg.frequency) {
                $('#set-freq-mode').val(cfg.frequency.mode || 'always');
            }
            if (cfg.woocommerce) {
                $('#set-wc-coupon').prop('checked', !!cfg.woocommerce.enableCoupon);
                $('#set-wc-amount').val(cfg.woocommerce.couponAmount || '');
            }
            if (cfg.customCode) {
                $('#set-custom-css').val(cfg.customCode.css || '');
                $('#set-custom-js').val(cfg.customCode.js || '');
            }
            if (cfg.quiz) {
                $('#set-quiz-enable').prop('checked', !!cfg.quiz.enable);
                $('#set-quiz-pass').val(cfg.quiz.passScore || '');
                $('#set-quiz-confetti').prop('checked', !!cfg.quiz.confetti);
            }
        }
    };

    window.WpPopPopBuilder.Settings = Settings;
})(window, jQuery);
