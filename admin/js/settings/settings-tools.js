(function(window, $) {
    'use strict';
    window.WpPopPopSettings = window.WpPopPopSettings || {};

    var Tools = {
        init: function() {
            this.bindCookieReset();
        },

        bindCookieReset: function() {
            $('#wppoppop-reset-cookies-btn').on('click', function() {
                if (!confirm('Reset all visitor cookies across all popups? Popups with frequency capping will reappear for returning visitors immediately.')) {
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).text('Resetting Cookies...');

                var $status = $('#wppoppop-cookie-reset-status');
                $status.empty();

                var vars = window.wppoppop_settings_vars || {
                    ajax_url: ajaxurl || '',
                    nonce: ''
                };

                $.post(vars.ajax_url, {
                    action: 'wppoppop_reset_cookies',
                    nonce: vars.nonce
                }).done(function(res) {
                    if (res.success) {
                        $status.html('<span style="color:#10b981;font-weight:600;">&#10004; ' + (res.data.message || 'Cookies reset successfully.') + '</span>');
                    } else {
                        var errMsg = (res.data && res.data.message) ? res.data.message : 'Unable to reset cookies.';
                        $status.html('<span style="color:#ef4444;font-weight:600;">&#9888; ' + errMsg + '</span>');
                    }
                }).fail(function() {
                    $status.html('<span style="color:#ef4444;font-weight:600;">&#9888; Network error during cookie reset.</span>');
                }).always(function() {
                    $btn.prop('disabled', false).text('Reset All Visitor Cookies');
                });
            });
        }
    };

    window.WpPopPopSettings.Tools = Tools;
})(window, jQuery);
