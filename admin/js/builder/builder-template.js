/**
 * WpPopPop Builder: Save As Template
 * Inspector DOM -> JS model -> pre-save flush -> AJAX (server whitelist) -> Library
 * ES5 / cross-browser (Chrome, Safari, Firefox, Edge).
 */
(function (window, $) {
    'use strict';

    var T = {
        frame: null,

        vars: function () {
            return window.wppoppop_vars || { ajax_url: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), nonce: $('#wppoppop-builder-nonce').val() || '' };
        },

        flush: function () {
            try {
                if (window.WpPopPopElementsRegistry && typeof window.WpPopPopElementsRegistry.flushElementFromDOM === 'function') { window.WpPopPopElementsRegistry.flushElementFromDOM(); }
                var insp = window.WpPopPopBuilderInspector;
                if (insp && typeof insp.flushSanitizedLayerSettings === 'function') { insp.flushSanitizedLayerSettings(); }
                if (window.WpPopPopBuilderIO && typeof window.WpPopPopBuilderIO.flushActiveElement === 'function') { window.WpPopPopBuilderIO.flushActiveElement(); }
            } catch (e) {}
        },

        collect: function () {
            this.flush();
            var core = window.WpPopPopBuilderCore;
            var st = (core && core.state) ? core.state : {};
            var settings = window.WpPopPopBuilderSettings ? window.WpPopPopBuilderSettings.getSettings() : {};
            var canv = st.canvases || { 1: [] };
            var meta = st.canvasMeta || {};
            /* WPPOPPOP_TPL_ANIM_SYNC: same live-field sync the Save button performs */
            var cur = st.currentCanvas || 1;
            if (!st.canvasMeta) { st.canvasMeta = {}; }
            if (!st.canvasMeta[cur]) { st.canvasMeta[cur] = {}; }
            meta = st.canvasMeta;
            var cm = meta[cur];
            var vApp = $('#set-canvas-anim-appearance, #set-anim-appearance').val();
            if (vApp) { cm.anim_appearance = vApp; }
            var vDur = $('#set-canvas-anim-duration, #set-anim-duration').val();
            if (vDur !== undefined && vDur !== '' && !isNaN(vDur)) { cm.anim_duration = parseInt(vDur, 10); }
            var vDel = $('#set-canvas-anim-delay, #set-anim-delay').val();
            if (vDel !== undefined && vDel !== '' && !isNaN(vDel)) { cm.anim_delay = parseInt(vDel, 10); }
            var vDis = $('#set-canvas-anim-disappearance, #set-anim-disappearance').val();
            if (vDis) { cm.anim_disappearance = vDis; }
            return {
                title: $('#wppoppop-builder-title').val() || 'Untitled Popup',
                canvases: canv,
                screens: canv,
                canvasMeta: meta,
                canvas_meta: meta,
                settings: settings
            };
        },

        stats: function (payload) {
            var counts = {}, total = 0, k, i, list, t;
            for (k in payload.canvases) {
                if (!Object.prototype.hasOwnProperty.call(payload.canvases, k)) { continue; }
                list = payload.canvases[k] || [];
                for (i = 0; i < list.length; i++) {
                    t = (list[i] && list[i].type) ? String(list[i].type) : 'element';
                    counts[t] = (counts[t] || 0) + 1;
                    total++;
                }
            }
            var cur = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.currentCanvas) || 1;
            var m = payload.canvasMeta[cur] || payload.canvasMeta[1] || {};
            return { counts: counts, total: total, width: parseInt(m.width, 10) || 640, height: parseInt(m.height, 10) || 400 };
        },

        esc: function (s) { return $('<div>').text(String(s)).html(); },

        open: function () {
            var payload = this.collect();
            var s = this.stats(payload), html = '', t;
            for (t in s.counts) {
                if (Object.prototype.hasOwnProperty.call(s.counts, t)) {
                    html += '<div>' + this.esc(t) + ' x ' + s.counts[t] + '</div>';
                }
            }
            $('#wppoppop-tpl-elements').html(html || '<em>No elements</em>');
            $('#wppoppop-tpl-total').text('(' + s.total + ')');
            $('#wppoppop-tpl-size').text(s.width + ' \u00d7 ' + s.height + ' px');
            $('#wppoppop-tpl-name').val(payload.title);
            $('#wppoppop-tpl-desc').val('');
            this.setImage('');
            if ($('#wppoppop-tpl-category option[value="custom"]').length) { $('#wppoppop-tpl-category').val('custom'); }
            $('#wppoppop-tpl-msg').hide().text('');
            $('#wppoppop-tpl-submit').prop('disabled', false);
            $('#wppoppop-modal-save-template').css('display', 'flex');
            $('#wppoppop-tpl-name').focus();
        },

        close: function () { $('#wppoppop-modal-save-template').hide(); },

        setImage: function (url) {
            $('#wppoppop-tpl-image').val(url || '');
            $('#wppoppop-tpl-prev').toggleClass('has-img', !!url);
            $('#wppoppop-tpl-prev').css('background-image', url ? 'url("' + url.replace(/"/g, '%22') + '")' : 'none');
        },

        pickImage: function () {
            var self = this;
            if (!window.wp || !window.wp.media) { var u = window.prompt('Image URL'); if (u) { self.setImage(u); } return; }
            if (!this.frame) {
                this.frame = window.wp.media({ title: 'Popup Image', button: { text: 'Use image' }, library: { type: 'image' }, multiple: false });
                this.frame.on('select', function () {
                    var a = self.frame.state().get('selection').first().toJSON();
                    self.setImage(a.url || '');
                });
            }
            this.frame.open();
        },

        msg: function (text, ok) {
            $('#wppoppop-tpl-msg').css('color', ok ? '#008a20' : '#b32d2e').text(text).show();
        },

        submit: function () {
            var self = this, v = this.vars();
            var name = $.trim($('#wppoppop-tpl-name').val());
            if (!name) { this.msg('Please enter a popup name.', false); return; }
            var $b = $('#wppoppop-tpl-submit').prop('disabled', true);
            $.ajax({
                url: v.ajax_url, type: 'POST', dataType: 'json',
                data: {
                    action: 'wppoppop_template_save',
                    nonce: v.nonce,
                    name: name,
                    description: $('#wppoppop-tpl-desc').val(),
                    image: $('#wppoppop-tpl-image').val(),
                    category: $('#wppoppop-tpl-category').val() || 'custom',
                    data: JSON.stringify(this.collect())
                },
                success: function (res) {
                    if (res && res.success) {
                        self.msg('Saved to Popups Library.', true);
                        setTimeout(function () { self.close(); }, 900);
                    } else {
                        self.msg((res && res.data && res.data.message) || 'Save failed.', false);
                        $b.prop('disabled', false);
                    }
                },
                error: function () { self.msg('Server error while saving template.', false); $b.prop('disabled', false); }
            });
        },

        init: function () {
            var self = this;
            $(document)
                .off('click.wppoppopTpl')
                .on('click.wppoppopTpl', '#wppoppop-btn-save-template', function (e) { e.preventDefault(); self.open(); })
                .on('click.wppoppopTpl', '#wppoppop-tpl-close, #wppoppop-tpl-cancel', function (e) { e.preventDefault(); self.close(); })
                .on('click.wppoppopTpl', '#wppoppop-tpl-upload, #wppoppop-tpl-prev', function (e) { e.preventDefault(); self.pickImage(); })
                .on('click.wppoppopTpl', '#wppoppop-tpl-remove-img', function (e) { e.preventDefault(); self.setImage(''); })
                .on('click.wppoppopTpl', '#wppoppop-tpl-submit', function (e) { e.preventDefault(); self.submit(); })
                .on('click.wppoppopTpl', '#wppoppop-modal-save-template', function (e) { if (e.target === this) { self.close(); } })
                .on('keydown.wppoppopTpl', function (e) { if (e.keyCode === 27) { self.close(); } });
        }
    };

    window.WpPopPopBuilderTemplate = T;
    $(function () { T.init(); });
})(window, jQuery);
