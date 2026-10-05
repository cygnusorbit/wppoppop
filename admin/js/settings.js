
(function($) {
    'use strict';

    $(document).ready(function() {
        // Tab switching
        $('.settings-nav-tabs .nav-tab').on('click', function(e) {
            e.preventDefault();
            $('.settings-nav-tabs .nav-tab').removeClass('active');
            $('.settings-tab-pane').removeClass('active');

            $(this).addClass('active');
            $('#' + $(this).data('tab')).addClass('active');
        });

        // Save Settings via AJAX
        $('#wppoppop-settings-form').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btn-save-settings');
            btn.prop('disabled', true).text('Saving...');

            const formData = $(this).serialize();

            $.post(wppoppop_settings_vars.ajax_url, {
                action: 'wppoppop_save_settings',
                nonce: wppoppop_settings_vars.nonce,
                data: formData
            }, function(res) {
                btn.prop('disabled', false).html('<span class="dashicons dashicons-yes"></span> Save Settings');
                if (res.success) {
                    alert(res.data.message);
                } else {
                    alert('Error: ' + res.data.message);
                }
            });
        });

        // Reset Cookies Action
        $('#btn-reset-cookie').on('click', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to reset all visitor cookies? Popups will reappear for all users.')) {
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true);

            $.post(wppoppop_settings_vars.ajax_url, {
                action: 'wppoppop_reset_cookies',
                nonce: wppoppop_settings_vars.nonce
            }, function(res) {
                btn.prop('disabled', false);
                if (res.success) {
                    alert(res.data.message);
                } else {
                    alert(res.data.message);
                }
            });
        });
    });
})(jQuery);
