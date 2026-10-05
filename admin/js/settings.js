
jQuery(document).ready(function($) {
    'use strict';

    // Tab switching
    $('.settings-nav-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        $('.settings-nav-tabs .nav-tab').removeClass('active');
        $('.settings-tab-pane').removeClass('active');

        $(this).addClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    function showNotice(type, msg) {
        const noticeArea = $('#wppoppop-settings-notice-area');
        const alertClass = (type === 'success') ? 'notice-success' : 'notice-error';
        noticeArea.html('<div class="notice ' + alertClass + ' is-dismissible" style="margin: 15px 0;"><p>' + msg + '</p></div>');
        $('html, body').animate({ scrollTop: 0 }, 200);
    }

    // Save Settings via AJAX
    $('#wppoppop-settings-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = $('#btn-save-settings');
        btn.prop('disabled', true).text('Saving...');

        const ajaxUrl = (typeof wppoppop_settings_vars !== 'undefined' && wppoppop_settings_vars.ajax_url)
            ? wppoppop_settings_vars.ajax_url
            : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');

        const nonce = (typeof wppoppop_settings_vars !== 'undefined' && wppoppop_settings_vars.nonce)
            ? wppoppop_settings_vars.nonce
            : $('#wppoppop_settings_nonce_field').val();

        const formData = form.serialize();

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'wppoppop_save_settings',
                nonce: nonce,
                data: formData
            },
            success: function(res) {
                btn.prop('disabled', false).html('<span class="dashicons dashicons-yes"></span> Save Settings');
                if (res && res.success) {
                    showNotice('success', res.data.message || 'Settings saved successfully!');
                } else {
                    showNotice('error', (res && res.data && res.data.message) ? res.data.message : 'Error saving settings.');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false).html('<span class="dashicons dashicons-yes"></span> Save Settings');
                showNotice('error', 'AJAX save failed (HTTP ' + xhr.status + '): ' + (xhr.responseText || error));
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

        const ajaxUrl = (typeof wppoppop_settings_vars !== 'undefined' && wppoppop_settings_vars.ajax_url)
            ? wppoppop_settings_vars.ajax_url
            : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');

        const nonce = (typeof wppoppop_settings_vars !== 'undefined' && wppoppop_settings_vars.nonce)
            ? wppoppop_settings_vars.nonce
            : $('#wppoppop_settings_nonce_field').val();

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'wppoppop_reset_cookies',
                nonce: nonce
            },
            success: function(res) {
                btn.prop('disabled', false);
                if (res && res.success) {
                    showNotice('success', res.data.message);
                } else {
                    showNotice('error', (res && res.data) ? res.data.message : 'Error resetting cookies.');
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                showNotice('error', 'AJAX request failed (HTTP ' + xhr.status + '): ' + error);
            }
        });
    });
});
