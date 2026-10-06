(function(window, $) {
    'use strict';
    window.WpPopPopLog = window.WpPopPopLog || {};

    var Payload = {
        currentRawPayload: '',

        init: function() {
            this.bindInspector();
            this.bindDismissal();
            this.bindCopy();
        },

        bindInspector: function() {
            var self = this;
            $('.wppoppop-view-payload-btn').on('click', function() {
                var $btn = $(this);
                var id = $btn.data('id');
                var type = $btn.data('type');
                var msg = $btn.data('msg');
                var date = $btn.data('date');
                var payload = $btn.data('payload');

                $('#wppoppop-modal-log-title').text('#' + id + ' • ' + type + ' (' + date + ')');
                $('#wppoppop-modal-log-desc').text(msg);

                var prettyJson = '';
                try {
                    var parsed = (typeof payload === 'string') ? JSON.parse(payload) : payload;
                    prettyJson = JSON.stringify(parsed, null, 2);
                } catch(e) {
                    prettyJson = String(payload);
                }

                self.currentRawPayload = prettyJson;
                $('#wppoppop-modal-payload-code').text(prettyJson);
                $('#wppoppop-payload-modal').css('display', 'flex');
            });
        },

        bindDismissal: function() {
            $('#wppoppop-payload-close, #wppoppop-payload-done').on('click', function() {
                $('#wppoppop-payload-modal').hide();
            });

            $('#wppoppop-payload-modal').on('click', function(e) {
                if (e.target === this) {
                    $(this).hide();
                }
            });
        },

        bindCopy: function() {
            var self = this;
            $('#wppoppop-payload-copy-btn').on('click', function() {
                if (navigator.clipboard && self.currentRawPayload) {
                    navigator.clipboard.writeText(self.currentRawPayload);
                    var orig = $(this).text();
                    $(this).text('Copied!');
                    var $selfBtn = $(this);
                    setTimeout(function() { $selfBtn.text(orig); }, 1500);
                }
            });
        }
    };

    window.WpPopPopLog.Payload = Payload;
})(window, jQuery);
