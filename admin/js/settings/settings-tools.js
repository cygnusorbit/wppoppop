/**
 * WpPopPop Settings: Tools & Maintenance Controller
 */
(function($) {
    'use strict';

    window.WpPopPopSettingsTools = {
        init: function() {
            this.bindCookieReset();
        },

        bindCookieReset: function() {
            $('#wppoppop-reset-cookies-btn').on('click', function(e) {
                e.preventDefault();

                if (!confirm('Are you sure you want to invalidate all visitor cookies? All dismissed or submitted popups will reappear immediately.')) {
                    return;
                }

                var $btn = $(this);
                var $status = $('#wppoppop-cookie-reset-status');
                var origText = $btn.text();

                $btn.prop('disabled', true).text('Resetting...');
                $status.css('color', '#64748b').text('Invalidating cookie epoch cache...');

                var ajaxUrl = (window.wppoppop_settings_vars && window.wppoppop_settings_vars.ajax_url)
                    ? window.wppoppop_settings_vars.ajax_url
                    : ((window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));

                var nonce = (window.wppoppop_settings_vars && window.wppoppop_settings_vars.nonce)
                    ? window.wppoppop_settings_vars.nonce
                    : ((window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '');

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'wppoppop_reset_cookies',
                        nonce: nonce
                    },
                    dataType: 'json',
                    success: function(res) {
                        $btn.prop('disabled', false).text(origText);
                        if (res && res.success) {
                            var msg = (res.data && res.data.message) ? res.data.message : 'Cookie cache reset successfully!';
                            $status.css('color', '#16a34a').text('✓ ' + msg);
                            if (res.data && res.data.epoch) {
                                $('#wppoppop-current-cookie-epoch').text(res.data.epoch);
                            }
                        } else {
                            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Failed to reset cookies.';
                            $status.css('color', '#dc2626').text('✕ ' + errMsg);
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).text(origText);
                        $status.css('color', '#dc2626').text('✕ Server communication error.');
                    }
                });
            });
        }
    };
})(jQuery);
