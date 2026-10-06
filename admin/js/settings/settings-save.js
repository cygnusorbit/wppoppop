(function(window, $) {
    'use strict';
    window.WpPopPopSettings = window.WpPopPopSettings || {};

    var Save = {
        init: function() {
            this.bindForm();
        },

        getVars: function() {
            return window.wppoppop_settings_vars || {
                ajax_url: ajaxurl || '',
                nonce: ''
            };
        },

        bindForm: function() {
            var self = this;
            $('#wppoppop-settings-form').on('submit', function(e) {
                e.preventDefault();

                var $btn = $('#wppoppop-settings-save-btn');
                $btn.prop('disabled', true).text('Saving Changes...');

                var $notice = $('#wppoppop-settings-notice');
                $notice.hide().removeClass('notice-success notice-error');

                var vars = self.getVars();
                var formData = $(this).serialize();

                $.post(vars.ajax_url, {
                    action: 'wppoppop_save_settings',
                    nonce: vars.nonce,
                    data: formData
                }).done(function(res) {
                    if (res.success) {
                        $notice.addClass('notice-success').html('<p>' + (res.data.message || 'Settings saved successfully!') + '</p>').fadeIn(200);
                    } else {
                        var errMsg = (res.data && res.data.message) ? res.data.message : 'An error occurred while saving settings.';
                        $notice.addClass('notice-error').html('<p>' + errMsg + '</p>').fadeIn(200);
                    }
                }).fail(function() {
                    $notice.addClass('notice-error').html('<p>Network error while saving settings.</p>').fadeIn(200);
                }).always(function() {
                    $btn.prop('disabled', false).text('Save Changes');
                    $('html, body').animate({ scrollTop: 0 }, 200);
                });
            });
        }
    };

    window.WpPopPopSettings.Save = Save;
})(window, jQuery);
