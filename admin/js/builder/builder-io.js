(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var IO = {
        init: function() {
            this.bindSave();
            this.loadInitial();
        },

        bindSave: function() {
            var self = this;
            $('#wppoppop-btn-save').on('click', function(e) {
                e.preventDefault();
                self.save();
            });
        },

        save: function() {
            var core = window.WpPopPopBuilder.Core;
            if (!core) return;

            var $btn = $('#wppoppop-btn-save');
            var originalHtml = $btn.html();
            $btn.prop('disabled', true).text('Saving...');

            var title = $('#wppoppop-builder-title').val() || 'Untitled Popup Campaign';
            var payload = {
                meta: core.state.config.meta,
                triggers: core.state.config.triggers,
                logic: core.state.config.logic,
                sideTab: core.state.config.sideTab,
                payment: core.state.config.payment,
                downloads: core.state.config.downloads,
                video: core.state.config.video,
                autoresponder: core.state.config.autoresponder,
                marketing: core.state.config.marketing,
                twilio: core.state.config.twilio,
                targeting: core.state.config.targeting,
                frequency: core.state.config.frequency,
                woocommerce: core.state.config.woocommerce,
                customCode: core.state.config.customCode,
                quiz: core.state.config.quiz,
                elements: core.state.elements
            };

            var postData = {
                action: 'wppoppop_save_popup',
                nonce: (window.wppoppop_vars && window.wppoppop_vars.nonce) || '',
                uid: core.state.uid || '',
                title: title,
                data: JSON.stringify(payload)
            };

            $.post(window.wppoppop_vars.ajax_url, postData)
                .done(function(res) {
                    if (res && res.success) {
                        core.state.uid = res.data.uid;
                        if (window.history && window.history.replaceState) {
                            var currentUrl = window.location.href.split('&uid=')[0];
                            window.history.replaceState(null, '', currentUrl + '&uid=' + res.data.uid);
                        }
                        alert(res.data.message || 'Popup configuration saved successfully!');
                    } else {
                        alert('Save Error: ' + (res && res.data ? res.data.message : 'Unknown error'));
                    }
                })
                .fail(function(xhr) {
                    alert('Save failed: Network or server error (' + xhr.status + ')');
                })
                .always(function() {
                    $btn.prop('disabled', false).html(originalHtml);
                });
        },

        loadInitial: function() {
            var core = window.WpPopPopBuilder.Core;
            if (!core) return;

            var urlParams = new URLSearchParams(window.location.search);
            var uid = urlParams.get('uid') || (window.wppoppop_vars && window.wppoppop_vars.current_uid);

            if (!uid) {
                if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
                if (window.WpPopPopBuilder.Layers) window.WpPopPopBuilder.Layers.renderList();
                return;
            }

            core.state.uid = uid;

            $.get(window.wppoppop_vars.ajax_url, {
                action: 'wppoppop_load_popup',
                nonce: (window.wppoppop_vars && window.wppoppop_vars.nonce) || '',
                uid: uid
            }).done(function(res) {
                if (res && res.success && res.data) {
                    var row = res.data;
                    if (row.title) {
                        $('#wppoppop-builder-title').val(row.title);
                    }
                    if (row.data) {
                        var parsed = {};
                        try {
                            parsed = JSON.parse(row.data);
                        } catch(e) {
                            parsed = {};
                        }

                        core.state.elements = parsed.elements || [];
                        core.state.config = Object.assign(core.state.config, parsed);

                        if (window.WpPopPopBuilder.Settings) {
                            window.WpPopPopBuilder.Settings.populateFromConfig(core.state.config);
                        }

                        if (window.WpPopPopBuilder.Canvas) {
                            window.WpPopPopBuilder.Canvas.renderElements();
                        }

                        if (window.WpPopPopBuilder.Layers) {
                            window.WpPopPopBuilder.Layers.renderList();
                        }
                    }
                }
            });
        }
    };

    window.WpPopPopBuilder.IO = IO;
})(window, jQuery);
