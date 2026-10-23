/**
 * WpPopPop Settings: Reset Defaults (current tab or all)
 */
(function (window, $) {
    'use strict';

    var Reset = {
        init: function () {
            this.$btn = $('#wppoppop-reset-btn');
            this.$menu = $('#wppoppop-reset-menu');
            if (!this.$btn.length || this.bound) { return; }
            this.bound = true;
            var self = this;

            this.$btn.on('click', function (e) { e.preventDefault(); e.stopPropagation(); self.toggle(); });
            this.$menu.on('click', 'button', function (e) {
                e.preventDefault();
                var scope = $(this).data('scope');
                self.close();
                self.run(scope);
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.wppoppop-reset-wrap').length) { self.close(); }
            }).on('keydown', function (e) {
                if (e.which === 27) { self.close(); }
            });
        },

        toggle: function () {
            var open = this.$menu.prop('hidden');
            this.$menu.prop('hidden', !open);
            this.$btn.attr('aria-expanded', open ? 'true' : 'false');
        },

        close: function () {
            this.$menu.prop('hidden', true);
            this.$btn.attr('aria-expanded', 'false');
        },

        vars: function () {
            var sv = window.wppoppop_settings_vars || {}, gv = window.wppoppop_vars || {};
            return {
                url: sv.ajax_url || gv.ajax_url || (typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : '/wp-admin/admin-ajax.php'),
                nonce: sv.nonce || gv.nonce || $('#wppoppop_settings_nonce').val()
            };
        },

        run: function (scope) {
            var $tabBtn = $('.wppoppop-tab-btn.active').first();
            var tab = scope === 'all' ? 'all' : ($tabBtn.data('tab') || 'general');
            var label = $.trim($tabBtn.clone().children().remove().end().text()) || tab;

            var msg = scope === 'all'
                ? 'Reset ALL settings to defaults?\n\nThis includes API keys, custom code and menu changes. Your popups, campaigns and leads are not affected. This cannot be undone.'
                : 'Reset the "' + label + '" tab to defaults?\n\nYour popups, campaigns and leads are not affected.';
            if (!window.confirm(msg)) { return; }

            var v = this.vars();
            var $m = $('#wppoppop-save-settings-msg');
            this.$btn.prop('disabled', true);
            $m.css('color', '#64748b').text('Resetting...');

            var self = this;
            $.ajax({
                url: v.url, type: 'POST', dataType: 'json',
                data: { action: 'wppoppop_reset_settings', nonce: v.nonce, tab: tab }
            }).done(function (res) {
                if (res && res.success) {
                    $m.css('color', '#16a34a').text('\u2713 ' + (res.data && res.data.message ? res.data.message : 'Reset.'));
                    setTimeout(function () { window.location.reload(); }, 600);
                } else {
                    self.$btn.prop('disabled', false);
                    $m.css('color', '#dc2626').text('\u2715 ' + (res && res.data && res.data.message ? res.data.message : 'Reset failed.'));
                }
            }).fail(function () {
                self.$btn.prop('disabled', false);
                $m.css('color', '#dc2626').text('\u2715 Server communication error.');
            });
        }
    };

    window.WpPopPopSettingsReset = Reset;
    $(function () { Reset.init(); });
})(window, jQuery);
