(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var IO = {
        init: function() {
            this.bindSave();
            this.loadInitialData();
        },

        getVars: function() {
            return window.wppoppop_vars || {
                ajax_url: ajaxurl || '',
                nonce: '',
                current_uid: ''
            };
        },

        bindSave: function() {
            var self = this;
            $('#wppoppop-btn-save').on('click', function() {
                var core = window.WpPopPopBuilder.Core;
                var $btn = $(this);
                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation:wppoppopSpin 1s infinite linear;"></span> Saving...');

                var vars = self.getVars();
                var title = $('#wppoppop-builder-title').val().trim() || 'Untitled Popup';
                var settingsData = window.WpPopPopBuilder.Settings ? window.WpPopPopBuilder.Settings.serialize() : {};

                var payload = {
                    title: title,
                    meta: settingsData,
                    elements: core.state.elements
                };

                $.post(vars.ajax_url, {
                    action: 'wppoppop_save_popup',
                    nonce: vars.nonce,
                    uid: core.state.uid,
                    title: title,
                    data: JSON.stringify(payload)
                }).done(function(res) {
                    if (res.success && res.data) {
                        core.state.uid = res.data.uid;
                        if (window.history && window.history.replaceState) {
                            var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=wppoppop-builder&uid=' + res.data.uid;
                            window.history.replaceState({ path: newUrl }, '', newUrl);
                        }
                        alert('Popup configuration saved successfully!');
                    } else {
                        alert('Save Error: ' + (res.data ? res.data.message : 'Unable to save popup.'));
                    }
                }).fail(function() {
                    alert('Network error while saving popup.');
                }).always(function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> Save Popup');
                });
            });
        },

        loadInitialData: function() {
            var core = window.WpPopPopBuilder.Core;
            if (window.wppoppop_initial_config) {
                try {
                    var parsed = typeof window.wppoppop_initial_config === 'string' ? JSON.parse(window.wppoppop_initial_config) : window.wppoppop_initial_config;
                    if (parsed.elements && Array.isArray(parsed.elements)) {
                        core.state.elements = parsed.elements;
                    }
                } catch(e) {}
            }

            core.recordHistory();
            if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
            if (window.WpPopPopBuilder.Layers) window.WpPopPopBuilder.Layers.renderList();
        }
    };

    window.WpPopPopBuilder.IO = IO;
})(window, jQuery);
