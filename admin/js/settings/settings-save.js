/**
 * WpPopPop Settings: Asynchronous Save Controller
 */
(function($) {
    'use strict';

    window.WpPopPopSettingsSave = {
        init: function() {
            this.bindSubmit();
        },

        bindSubmit: function() {
            var self = this;
            $('#wppoppop-settings-form').on('submit', function(e) {
                e.preventDefault();

                var $form = $(this);
                var $btn = $('#wppoppop-save-settings-btn');
                var $spinner = $('#wppoppop-save-settings-spinner');
                var $msg = $('#wppoppop-save-settings-msg');
                var $alert = $('#wppoppop-settings-alert');

                $btn.prop('disabled', true);
                $spinner.addClass('is-active');
                $msg.css('color', '#64748b').text('Saving settings...');

                var ajaxUrl = (window.wppoppop_settings_vars && window.wppoppop_settings_vars.ajax_url)
                    ? window.wppoppop_settings_vars.ajax_url
                    : ((window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));

                var nonce = (window.wppoppop_settings_vars && window.wppoppop_settings_vars.nonce)
                    ? window.wppoppop_settings_vars.nonce
                    : ((window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : $('#wppoppop_settings_nonce').val());

                var formData = $form.serializeArray();
                formData.push({ name: 'action', value: 'wppoppop_save_settings' });
                formData.push({ name: 'nonce', value: nonce });

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(res) {
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');

                        if (res && res.success) {
                            var successText = (res.data && res.data.message) ? res.data.message : 'Settings saved successfully!';
                            $msg.css('color', '#16a34a').text('✓ ' + successText);
                            if ($alert.length) {
                                $alert.removeClass('notice-error').addClass('notice-success').show().find('p').text(successText);
                            }
                            setTimeout(function() {
                                $msg.fadeOut(300, function() { $(this).text('').show(); });
                            }, 3500);
                        } else {
                            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Failed to save settings.';
                            $msg.css('color', '#dc2626').text('✕ ' + errMsg);
                            if ($alert.length) {
                                $alert.removeClass('notice-success').addClass('notice-error').show().find('p').text(errMsg);
                            }
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');
                        $msg.css('color', '#dc2626').text('✕ Server communication error.');
                    }
                });
            });
        }
    };
})(jQuery);
