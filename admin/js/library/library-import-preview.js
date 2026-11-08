/**
 * WpPopPop Library: preview a template (.zip / .json) in a modal before importing it.
 * Called by library-bundle.js with the chosen / dropped files; calls back with the files the user confirmed.
 * ES5 + jQuery + XHR/FormData: Chrome, Safari, Firefox, Edge.
 */
(function (window, $) {
    'use strict';

    var cfg = window.wppoppop_libprev_vars || { ajax_url: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), nonce: '', i18n: {} };
    var t = cfg.i18n || {};
    var S = null; // open dialog state

    function tx(k, d) { return t[k] || d; }

    function fmtSize(b) {
        if (b >= 1048576) { return (Math.round(b / 104857.6) / 10) + ' MB'; }
        return Math.max(1, Math.round(b / 1024)) + ' KB';
    }

    function makeItem(file) {
        var $li = $('<li class="wpp-ip-item is-loading"></li>');
        var $th = $('<div class="wpp-ip-thumb"><span class="dashicons dashicons-format-image"></span></div>').appendTo($li);
        var $body = $('<div class="wpp-ip-body"></div>').appendTo($li);
        var $title = $('<strong class="wpp-ip-title"></strong>').text(file.name).appendTo($body);
        var $desc = $('<p class="wpp-ip-desc"></p>').appendTo($body);
        var $meta = $('<div class="wpp-ip-meta"></div>').appendTo($body);
        var $state = $('<span class="wpp-ip-state"></span>').text(tx('loading', 'Reading template...')).appendTo($body);
        var $rm = $('<button type="button" class="wpp-ip-remove"></button>').attr({ 'aria-label': tx('remove', 'Remove'), title: tx('remove', 'Remove') }).html('&times;').appendTo($li);
        return { file: file, ok: false, done: false, $li: $li, $th: $th, $title: $title, $desc: $desc, $meta: $meta, $state: $state, $rm: $rm, xhr: null };
    }

    function pill(text) { return $('<span class="wpp-ip-pill"></span>').text(text); }

    function fill(it, d) {
        it.ok = true;
        it.$title.text(d.title || it.file.name);
        it.$desc.text(d.description || '');
        it.$meta.empty();
        if (d.category) { it.$meta.append(pill(d.category)); }
        if (d.elements) { it.$meta.append(pill(d.elements + ' ' + tx('elements', 'elements'))); }
        if (d.images) { it.$meta.append(pill(d.images + ' ' + tx('images', 'images'))); }
        it.$meta.append(pill(it.file.name + ' \u00b7 ' + fmtSize(it.file.size)));
        it.$state.text('').hide();
        if (d.thumb) {
            var img = new window.Image();
            img.alt = '';
            img.onerror = function () { it.$th.find('img').remove(); it.$th.find('.dashicons').show(); };
            img.src = d.thumb;
            it.$th.find('.dashicons').hide();
            it.$th.append(img);
        }
    }

    function fail(it, msg) {
        it.ok = false;
        it.$li.addClass('is-err');
        it.$state.text(msg).show();
    }

    function sync() {
        var pending = 0, ok = 0, i;
        for (i = 0; i < S.items.length; i++) {
            if (!S.items[i].done) { pending++; } else if (S.items[i].ok) { ok++; }
        }
        S.$go.prop('disabled', pending > 0 || ok === 0).text(ok > 1 ? tx('import_n', 'Import %d templates').replace('%d', ok) : tx('import', 'Import'));
        if (!S.items.length) { closeDlg(); }
    }

    function pump() {
        var it = null, i, fd, x;
        if (!S || S.busy) { return; }
        for (i = 0; i < S.items.length; i++) {
            if (!S.items[i].done && !S.items[i].xhr) { it = S.items[i]; break; }
        }
        if (!it) { sync(); return; }
        S.busy = true;
        fd = new FormData();
        fd.append('action', 'wppoppop_template_preview_file');
        fd.append('nonce', cfg.nonce);
        fd.append('file', it.file, it.file.name);
        x = new XMLHttpRequest();
        it.xhr = x;
        x.open('POST', cfg.ajax_url, true);
        x.onreadystatechange = function () {
            var r = null;
            if (x.readyState !== 4) { return; }
            it.xhr = null;
            if (S) { S.busy = false; }
            if (it.removed) { pump(); return; }
            try { r = JSON.parse(x.responseText); } catch (e) { r = null; }
            it.done = true;
            it.$li.removeClass('is-loading');
            if (r && r.success && r.data) { fill(it, r.data); }
            else { fail(it, (r && r.data && r.data.message) || tx('failed', 'This file could not be read.')); }
            if (S) { sync(); pump(); }
        };
        x.send(fd);
    }

    function addFiles(files) {
        var i, it;
        for (i = 0; i < files.length; i++) {
            it = makeItem(files[i]);
            S.items.push(it);
            S.$list.append(it.$li);
            (function (item) {
                item.$rm.on('click', function () {
                    item.removed = true;
                    item.done = true;
                    if (item.xhr) { try { item.xhr.abort(); } catch (e) {} item.xhr = null; S.busy = false; }
                    S.items.splice($.inArray(item, S.items), 1);
                    item.$li.remove();
                    sync();
                    pump();
                });
            })(it);
        }
        sync();
        pump();
    }

    function closeDlg() {
        var i, s = S;
        if (!s) { return; }
        S = null;
        for (i = 0; i < s.items.length; i++) { if (s.items[i].xhr) { try { s.items[i].xhr.abort(); } catch (e) {} } }
        $(document).off('keydown.wppIp');
        s.$back.remove();
        if (s.$prev && s.$prev.length) { s.$prev.trigger('focus'); }
    }

    function open(files, cb) {
        if (S) { S.cb = cb || S.cb; addFiles(files); return; }
        var $back = $('<div class="wpp-ip-backdrop"></div>');
        var $dlg = $('<div class="wpp-ip-modal" role="dialog" aria-modal="true" tabindex="-1" aria-labelledby="wpp-ip-title"></div>').appendTo($back);
        var $act;
        S = { items: [], busy: false, cb: cb, $back: $back, $prev: $(document.activeElement) };
        $('<h3 class="wpp-ip-heading" id="wpp-ip-title"></h3>').text(tx('title', 'Preview template')).appendTo($dlg);
        $('<p class="wpp-ip-note"></p>').text(tx('note', 'Check the template below, then click Import to add it to My Templates.')).appendTo($dlg);
        S.$list = $('<ul class="wpp-ip-list"></ul>').appendTo($dlg);
        $act = $('<div class="wpp-ip-actions"></div>').appendTo($dlg);
        $('<button type="button" class="button"></button>').text(tx('cancel', 'Cancel')).on('click', closeDlg).appendTo($act);
        S.$go = $('<button type="button" class="button button-primary" disabled></button>').text(tx('import', 'Import')).appendTo($act);
        S.$go.on('click', function () {
            var list = [], i, cbk = S.cb;
            if (S.$go.prop('disabled')) { return; }
            for (i = 0; i < S.items.length; i++) { if (S.items[i].ok) { list.push(S.items[i].file); } }
            closeDlg();
            if (cbk && list.length) { cbk(list); }
        });
        $back.on('mousedown', function (e) { if (e.target === $back[0]) { closeDlg(); } });
        $(document).on('keydown.wppIp', function (e) {
            if (e.which === 27) { e.preventDefault(); closeDlg(); }
            else if (e.which === 9 && S) {
                var $f = S.$back.find('button:not(:disabled)').filter(':visible');
                if (!$f.length) { return; }
                var first = $f[0], last = $f[$f.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); $(last).trigger('focus'); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); $(first).trigger('focus'); }
            }
        });
        $back.appendTo(document.body);
        $dlg.trigger('focus');
        addFiles(files);
    }

    window.WpPopPopLibImportPreview = { open: open };
})(window, jQuery);
