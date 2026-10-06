(function(window, $) {
    'use strict';
    window.WpPopPop = window.WpPopPop || {};

    var Settings = {
        init: function() {
            this.bindDrawer();
            this.bindAccordions();
            this.bindStylingSync();
            this.bindDiagnostics();
        },

        bindDrawer: function() {
            $('#wppoppop-btn-settings').on('click', function() {
                $('#wppoppop-settings-drawer, #wppoppop-settings-drawer-backdrop').addClass('open');
            });

            $('#wppoppop-settings-drawer-close, #wppoppop-settings-drawer-backdrop').on('click', function() {
                $('#wppoppop-settings-drawer, #wppoppop-settings-drawer-backdrop').removeClass('open');
            });
        },

        bindAccordions: function() {
            $(document).on('click', '.wppoppop-accordion-header', function() {
                var $body = $(this).next('.wppoppop-accordion-body');
                $body.slideToggle(200);
            });
        },

        bindStylingSync: function() {
            var cfg = window.WpPopPop.State.config;

            $('#wppoppop-cfg-width, #wppoppop-cfg-height').on('input', function() {
                cfg.meta.width = parseInt($('#wppoppop-cfg-width').val(), 10) || 640;
                cfg.meta.height = parseInt($('#wppoppop-cfg-height').val(), 10) || 400;
                if (window.WpPopPop.State.activeViewport === 'desktop') {
                    $('#wppoppop-canvas').css({ width: cfg.meta.width + 'px', height: cfg.meta.height + 'px' });
                }
            });

            $('#wppoppop-cfg-bg-mode').on('change', function() {
                var mode = $(this).val();
                cfg.meta.bg_mode = mode;
                if (mode === 'gradient') {
                    $('#wppoppop-cfg-solid-wrap').hide();
                    $('#wppoppop-cfg-gradient-wrap').show();
                    $('#wppoppop-canvas').css('background', 'linear-gradient(' + cfg.meta.grad_angle + 'deg, ' + cfg.meta.grad_1 + ', ' + cfg.meta.grad_2 + ')');
                } else {
                    $('#wppoppop-cfg-solid-wrap').show();
                    $('#wppoppop-cfg-gradient-wrap').hide();
                    $('#wppoppop-canvas').css('background', cfg.meta.bg_color);
                }
            });

            $('#wppoppop-cfg-bg-color').on('input', function() {
                cfg.meta.bg_color = $(this).val();
                $('#wppoppop-canvas').css('background', cfg.meta.bg_color);
            });

            $('#wppoppop-cfg-grad-1, #wppoppop-cfg-grad-2, #wppoppop-cfg-grad-angle').on('input', function() {
                cfg.meta.grad_1 = $('#wppoppop-cfg-grad-1').val();
                cfg.meta.grad_2 = $('#wppoppop-cfg-grad-2').val();
                cfg.meta.grad_angle = parseInt($('#wppoppop-cfg-grad-angle').val(), 10) || 135;
                $('#wppoppop-canvas').css('background', 'linear-gradient(' + cfg.meta.grad_angle + 'deg, ' + cfg.meta.grad_1 + ', ' + cfg.meta.grad_2 + ')');
            });
        },

        bindDiagnostics: function() {
            $('#wppoppop-wh-test').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).text('Testing Ping...');
                $.ajax({
                    url: (window.wppoppop_vars ? window.wppoppop_vars.ajax_url.replace('admin-ajax.php', '') : '') + 'rest_route=/wppoppop/v1/diagnostic/webhook',
                    method: 'POST',
                    headers: { 'X-WP-Nonce': (window.wppoppop_vars && window.wppoppop_vars.nonce) || '' },
                    data: { url: $('#wppoppop-wh-url').val(), secret: $('#wppoppop-wh-secret').val() },
                    complete: function() {
                        $btn.prop('disabled', false).text('Test Webhook Ping');
                    },
                    success: function(res) {
                        alert(res.message || 'Webhook ping responded successfully!');
                    },
                    error: function(err) {
                        alert('Ping failed: ' + (err.responseJSON ? err.responseJSON.message : 'HTTP Error'));
                    }
                });
            });

            $('#wppoppop-sms-test').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).text('Testing SMS...');
                $.ajax({
                    url: (window.wppoppop_vars ? window.wppoppop_vars.ajax_url.replace('admin-ajax.php', '') : '') + 'rest_route=/wppoppop/v1/diagnostic/sms',
                    method: 'POST',
                    headers: { 'X-WP-Nonce': (window.wppoppop_vars && window.wppoppop_vars.nonce) || '' },
                    data: {
                        to: $('#wppoppop-sms-to').val(),
                        account_sid: $('#wppoppop-sms-sid').val(),
                        auth_token: $('#wppoppop-sms-token').val(),
                        from_number: $('#wppoppop-sms-from').val()
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Test SMS Dispatch');
                    },
                    success: function(res) {
                        alert(res.message || 'SMS test sent!');
                    },
                    error: function(err) {
                        alert('SMS failed: ' + (err.responseJSON ? err.responseJSON.message : 'Error'));
                    }
                });
            });
        }
    };

    window.WpPopPop.Settings = Settings;
})(window, jQuery);
