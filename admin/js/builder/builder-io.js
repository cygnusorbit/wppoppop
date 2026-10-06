(function(window, $) {
    'use strict';
    window.WpPopPop = window.WpPopPop || {};

    var IO = {
        save: function() {
            var State = window.WpPopPop.State;
            var $btn = $('#wppoppop-btn-save');
            $btn.prop('disabled', true).text('Saving...');

            var title = $('#wppoppop-cfg-title').val() || 'Untitled Popup';
            var payload = {
                meta: State.config.meta,
                triggers: State.config.triggers,
                targeting: State.config.targeting,
                frequency: State.config.frequency,
                autoresponder: State.config.autoresponder,
                marketing: State.config.marketing,
                twilio: State.config.twilio,
                tabs: State.config.tabs,
                ribbon: State.config.ribbon,
                woocommerce: State.config.woocommerce,
                payments: State.config.payments,
                downloads: State.config.downloads,
                quiz: State.config.quiz,
                sound: State.config.sound,
                elements: State.elements
            };

            $.post(window.wppoppop_vars.ajax_url, {
                action: 'wppoppop_save_popup',
                nonce: window.wppoppop_vars.nonce,
                uid: State.uid,
                title: title,
                data: JSON.stringify(payload)
            }).done(function(res) {
                if (res.success) {
                    State.uid = res.data.uid;
                    alert(res.data.message || 'Saved successfully!');
                    if (window.history && window.history.replaceState) {
                        var newUrl = window.location.href.split('&uid=')[0] + '&uid=' + State.uid;
                        window.history.replaceState(null, '', newUrl);
                    }
                } else {
                    alert('Save error: ' + (res.data ? res.data.message : 'Unknown'));
                }
            }).fail(function() {
                alert('Network error while saving popup.');
            }).always(function() {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> Save Popup');
            });
        },

        load: function(uid) {
            if (!uid) return;
            $.get(window.wppoppop_vars.ajax_url, {
                action: 'wppoppop_load_popup',
                nonce: window.wppoppop_vars.nonce,
                uid: uid
            }).done(function(res) {
                if (res.success && res.data) {
                    var data = JSON.parse(res.data.data);
                    window.WpPopPop.State.elements = data.elements || [];
                    window.WpPopPop.State.config = Object.assign(window.WpPopPop.State.config, data);
                    $('#wppoppop-cfg-title').val(res.data.title);
                    $('#wppoppop-cfg-width').val(data.meta.width);
                    $('#wppoppop-cfg-height').val(data.meta.height);
                    window.WpPopPop.Canvas.refresh();
                    window.WpPopPop.Layers.renderList();
                }
            });
        }
    };

    window.WpPopPop.IO = IO;
})(window, jQuery);
