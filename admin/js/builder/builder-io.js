(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var IO = {
        init: function() {
            this.bindSave();
        },

        bindSave: function() {
            $('#wppoppop-btn-save').on('click', function() {
                var Core = window.WpPopPopBuilder.Core;
                var Settings = window.WpPopPopBuilder.Settings;
                var $btn = $(this);

                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="font-size:14px;width:14px;height:14px;animation:spin 1s linear infinite;"></span> Saving...');

                var title = $('#wppoppop-builder-title').val().trim() || 'Untitled Popup Campaign';
                var currentSettings = Settings.getSettings();

                var payloadConfig = {
                    elements: Core.elements,
                    screens: Core.screens,
                    settings: currentSettings,
                    triggers: currentSettings.triggers,
                    custom_css: currentSettings.customCss,
                    custom_js: currentSettings.customJs,
                    box: {
                        width: currentSettings.width,
                        height: currentSettings.height,
                        bg_mode: currentSettings.bgMode,
                        bg_color: currentSettings.bgColor
                    }
                };

                var uid = (window.wppoppop_vars && window.wppoppop_vars.current_uid) || '';
                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) || '';
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) || '';

                $.post(ajaxUrl, {
                    action: 'wppoppop_save_popup',
                    nonce: nonce,
                    uid: uid,
                    title: title,
                    data: JSON.stringify(payloadConfig)
                }).done(function(res) {
                    if (res.success) {
                        Core.isDirty = false;
                        $btn.html('<span class="dashicons dashicons-yes" style="font-size:14px;width:14px;height:14px;"></span> Saved!');
                        if (res.data && res.data.uid) {
                            window.wppoppop_vars.current_uid = res.data.uid;
                            var currentUrl = new URL(window.location.href);
                            currentUrl.searchParams.set('uid', res.data.uid);
                            window.history.replaceState({ path: currentUrl.toString() }, '', currentUrl.toString());
                        }
                    } else {
                        alert(res.data && res.data.message ? res.data.message : 'Save error');
                    }
                }).fail(function() {
                    alert('Network communication error with server.');
                }).always(function() {
                    setTimeout(function() {
                        $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved" style="font-size:14px;width:14px;height:14px;"></span> Save Popup');
                    }, 1400);
                });
            });
        }
    };

    window.WpPopPopBuilder.IO = IO;
})(window, jQuery);
