(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Settings = {
        init: function() {
            this.bindDrawer();
            this.bindAccordions();
            this.bindBackgroundMode();
        },

        bindDrawer: function() {
            $('#wppoppop-btn-settings').on('click', function() {
                $('#wppoppop-settings-drawer').css('display', 'flex');
            });

            $('#wppoppop-settings-drawer-close').on('click', function() {
                $('#wppoppop-settings-drawer').hide();
            });
        },

        bindAccordions: function() {
            $('.wppoppop-acc-header').on('click', function() {
                var $header = $(this);
                var $body = $header.next('.wppoppop-acc-body');

                $header.toggleClass('active');
                $body.slideToggle(180);
            });
        },

        bindBackgroundMode: function() {
            $('#set-bg-mode').on('change', function() {
                var isGrad = $(this).val() === 'gradient';
                $('#set-solid-wrap').toggle(!isGrad);
                $('#set-gradient-wrap').toggle(isGrad);
            });
        },

        serialize: function() {
            return {
                width: parseInt($('#set-box-width').val(), 10) || 640,
                height: parseInt($('#set-box-height').val(), 10) || 400,
                bg_mode: $('#set-bg-mode').val() || 'solid',
                bg_color: $('#set-bg-color').val() || '#ffffff',
                grad_color1: $('#set-grad-color1').val() || '#3b82f6',
                grad_color2: $('#set-grad-color2').val() || '#1d4ed8',
                grad_angle: parseInt($('#set-grad-angle').val(), 10) || 135,
                trig_load: $('#trig-load').is(':checked'),
                trig_load_delay: parseFloat($('#trig-load-delay').val()) || 0,
                trig_exit: $('#trig-exit').is(':checked'),
                trig_scroll: $('#trig-scroll').is(':checked'),
                trig_scroll_val: parseInt($('#trig-scroll-val').val(), 10) || 50,
                trig_adblock: $('#trig-adblock').is(':checked'),
                trig_backbutton: $('#trig-backbutton').is(':checked'),
                math_formula: $('#set-math-formula').val() || '',
                math_target: $('#set-math-target').val() || '',
                sidetab_enable: $('#set-sidetab-enable').is(':checked'),
                sidetab_label: $('#set-sidetab-label').val() || '',
                sidetab_pos: $('#set-sidetab-pos').val() || 'left',
                pay_gateway: $('#set-pay-gateway').val() || 'stripe',
                pay_amount: parseFloat($('#set-pay-amount').val()) || 0,
                dl_enable: $('#set-dl-enable').is(':checked'),
                dl_url: $('#set-dl-url').val() || '',
                vid_enable: $('#set-vid-enable').is(':checked'),
                vid_time: parseInt($('#set-vid-time').val(), 10) || 0,
                auto_enable: $('#set-auto-enable').is(':checked'),
                auto_subject: $('#set-auto-subject').val() || '',
                auto_body: $('#set-auto-body').val() || '',
                webhook_url: $('#set-webhook-url').val() || '',
                webhook_secret: $('#set-webhook-secret').val() || '',
                sms_enable: $('#set-sms-enable').is(':checked'),
                sms_phone: $('#set-sms-phone').val() || '',
                target_auth: $('#set-target-auth').val() || 'all',
                freq_mode: $('#set-freq-mode').val() || 'always',
                wc_coupon: $('#set-wc-coupon').is(':checked'),
                wc_amount: $('#set-wc-amount').val() || '',
                custom_css: $('#set-custom-css').val() || '',
                custom_js: $('#set-custom-js').val() || '',
                quiz_enable: $('#set-quiz-enable').is(':checked'),
                quiz_pass: parseInt($('#set-quiz-pass').val(), 10) || 0,
                quiz_confetti: $('#set-quiz-confetti').is(':checked')
            };
        }
    };

    window.WpPopPopBuilder.Settings = Settings;
})(window, jQuery);
