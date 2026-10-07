(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var IO = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            $(document).on('click', '#wppoppop-btn-save', function(e) {
                e.preventDefault();
                self.save();
            });

            // Keyboard shortcut: Cmd/Ctrl + S
            $(document).on('keydown', function(e) {
                if ((e.metaKey || e.ctrlKey) && (e.key === 's' || e.keyCode === 83)) {
                    e.preventDefault();
                    self.save();
                }
            });

            // Prevent accidental navigation when changes are unsaved
            window.addEventListener('beforeunload', function(e) {
                var Core = window.WpPopPopBuilder.Core;
                if (Core && Core.isDirty) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes in your popup campaign.';
                }
            });
        },

        save: function(successCb, errorCb) {
            var Core = window.WpPopPopBuilder.Core;
            var Settings = window.WpPopPopBuilder.Settings;
            var $btn = $('#wppoppop-btn-save');
            var originalHtml = $btn.html();

            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin" style="margin-right:4px;"></span> Saving...');

            var settingsData = (Settings && typeof Settings.getSettings === 'function') ? Settings.getSettings() : {};
            var title = $('#wppoppop-builder-title').val() || 'Untitled Popup Campaign';

            var payload = {
                screens: Core.screens || [{ id: 1, name: 'Screen 1', width: 640, height: 400 }],
                elements: Core.elements || [],
                settings: settingsData
            };

            var postData = {
                action: 'wppoppop_save_popup',
                nonce: (window.wppoppop_vars && (window.wppoppop_vars.builder_nonce || window.wppoppop_vars.nonce)) || '',
                uid: Core.uid || (window.wppoppop_vars && window.wppoppop_vars.uid) || '',
                title: title,
                data: JSON.stringify(payload)
            };

            $.ajax({
                url: (window.wppoppop_vars && window.wppoppop_vars.ajax_url) || ajaxurl,
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html(originalHtml);
                    if (res && res.success) {
                        Core.isDirty = false;
                        var savedUid = (res.data && res.data.uid) ? res.data.uid : Core.uid;
                        if (savedUid) {
                            Core.uid = savedUid;
                            if (window.wppoppop_vars) window.wppoppop_vars.uid = savedUid;
                            var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=wppoppop-builder&uid=' + encodeURIComponent(savedUid);
                            window.history.replaceState({ path: newUrl }, '', newUrl);
                        }

                        if (typeof successCb === 'function') {
                            successCb(savedUid);
                        }
                    } else {
                        var msg = (res && res.data && res.data.message) ? res.data.message : 'Save operation failed.';
                        if (typeof errorCb === 'function') {
                            errorCb(msg);
                        } else {
                            alert('Error: ' + msg);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).html(originalHtml);
                    if (typeof errorCb === 'function') {
                        errorCb(error);
                    } else {
                        alert('AJAX Communication Failed: ' + error);
                    }
                }
            });
        }
    };

    window.WpPopPopBuilder.IO = IO;
})(window, jQuery);
