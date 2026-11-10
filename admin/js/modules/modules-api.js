/**
 * WpPopPop modules: shared client API (Module tab + Extend page).
 * post / upload talk to the wppoppop_module_* AJAX endpoints; act() runs the
 * install / uninstall / delete flows incl. confirm dialogs, "Remove data" and the in-use warning.
 */
(function (window, $) {
    'use strict';

    var V = window.wppoppop_modules_vars || {};
    var T = V.i18n || {};
    var seq = 0;

    function t(key) {
        return Object.prototype.hasOwnProperty.call(T, key) ? T[key] : key;
    }

    function fmt(str) {
        var args = Array.prototype.slice.call(arguments, 1);
        var i = 0;
        return String(str).replace(/%(?:(\d+)\$)?s/g, function (m, n) {
            var idx = n ? parseInt(n, 10) - 1 : i++;
            return idx < args.length ? String(args[idx]) : '';
        });
    }

    function parse(res) {
        if (res && res.success) {
            return { ok: true, data: res.data || {} };
        }
        var d = (res && res.data) || {};
        return { ok: false, code: d.code || '', message: d.message || t('error_generic'), data: d.data || null };
    }

    function onFail(xhr, done) {
        var body = xhr && xhr.responseJSON;
        done(body ? parse(body) : { ok: false, code: 'http', message: t('error_generic'), data: null });
    }

    function post(action, data, done) {
        $.ajax({
            url: V.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: $.extend({ action: 'wppoppop_module_' + action, nonce: V.nonce }, data || {})
        }).done(function (res) { done(parse(res)); })
          .fail(function (xhr) { onFail(xhr, done); });
    }

    function upload(file, replace, done) {
        var fd = new window.FormData();
        fd.append('action', 'wppoppop_module_upload');
        fd.append('nonce', V.nonce);
        fd.append('module_zip', file, file.name);
        if (replace) { fd.append('replace', '1'); }
        $.ajax({ url: V.ajax_url, type: 'POST', data: fd, processData: false, contentType: false, dataType: 'json' })
            .done(function (res) { done(parse(res)); })
            .fail(function (xhr) { onFail(xhr, done); });
    }

    function notice($el, type, message, link) {
        if (!$el || !$el.length) { return; }
        $el.removeClass('is-success is-error is-info').addClass('is-' + type).empty();
        $('<span></span>').text(message).appendTo($el);
        if (link && link.href) {
            $el.append(' ');
            $('<a></a>').attr('href', link.href).text(link.text).appendTo($el);
        }
        $el.prop('hidden', false);
    }

    function dialog(o) {
        var $prev = $(document.activeElement);
        var id = 'wpp-mod-dlg-' + (++seq);
        var $back = $('<div class="wpp-mod-backdrop"></div>');
        var $dlg = $('<div class="wpp-mod-modal" role="dialog" aria-modal="true" tabindex="-1"></div>')
            .attr('aria-labelledby', id + '-t').appendTo($back);
        $('<h3 class="wpp-mod-modal-title"></h3>').attr('id', id + '-t').text(o.title).appendTo($dlg);
        $('<p class="wpp-mod-modal-msg"></p>').text(o.message).appendTo($dlg);
        if (o.body) {
            $dlg.addClass('is-wide');
            $(o.body).appendTo($dlg);
        }
        var $cb = null;
        if (o.checkbox) {
            var $lab = $('<label class="wpp-mod-modal-check"></label>').appendTo($dlg);
            $cb = $('<input type="checkbox">').appendTo($lab);
            $('<span></span>').text(o.checkbox).appendTo($lab);
        }
        var $act = $('<div class="wpp-mod-modal-actions"></div>').appendTo($dlg);
        var $cancel = $('<button type="button" class="button"></button>').text(t('cancel')).appendTo($act);
        var $ok = $('<button type="button" class="button button-primary"></button>').text(o.confirm).appendTo($act);
        if (o.danger) { $ok.addClass('wpp-mod-danger'); }

        function close() {
            $(document).off('keydown.wppmod' + id);
            $back.remove();
            if ($prev.length) { $prev.trigger('focus'); }
        }
        $cancel.on('click', close);
        $back.on('mousedown', function (e) { if (e.target === $back[0]) { close(); } });
        $ok.on('click', function () {
            var checked = $cb ? $cb.prop('checked') : false;
            close();
            if (o.onConfirm) { o.onConfirm(checked); }
        });
        $(document).on('keydown.wppmod' + id, function (e) {
            if (e.which === 27) {
                e.preventDefault();
                close();
            } else if (e.which === 9) {
                var $f = $dlg.find('input, button').filter(':visible');
                if (!$f.length) { return; }
                var first = $f[0], last = $f[$f.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); $(last).trigger('focus'); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); $(first).trigger('focus'); }
            }
        });
        $back.appendTo(document.body);
        (o.danger ? $cancel : $ok).trigger('focus');
    }

    /**
     * Run a lifecycle action on a module summary {slug, name, source}.
     * hooks.busy(bool) toggles the caller's busy state; hooks.done(result) receives {ok, data|message}.
     */
    function act(action, mod, hooks) {
        hooks = hooks || {};
        var busy = hooks.busy || function () {};
        var done = hooks.done || function () {};
        var name = mod.name || mod.slug;

        function send(removeData, force) {
            busy(true);
            post(action, { slug: mod.slug, remove_data: removeData ? 1 : 0, force: force ? 1 : 0 }, function (r) {
                busy(false);
                if (!r.ok && r.code === 'in_use' && !force) {
                    dialog({
                        title: t('in_use_title'),
                        message: r.message + ' ' + t('in_use_msg'),
                        confirm: t('continue_anyway'),
                        danger: true,
                        onConfirm: function () { send(removeData, true); }
                    });
                    return;
                }
                done(r);
            });
        }

        if (action === 'install') {
            busy(true);
            post('install', { slug: mod.slug }, function (r) { busy(false); done(r); });
            return;
        }
        var isDelete = action === 'delete';
        dialog({
            title: isDelete ? t('delete_title') : t('uninstall_title'),
            message: isDelete
                ? fmt(mod.source === 'uploaded' ? t('delete_msg_uploaded') : t('delete_msg_core'), name)
                : fmt(t('uninstall_msg'), name),
            checkbox: t('remove_data'),
            confirm: isDelete ? t('delete') : t('uninstall'),
            danger: true,
            onConfirm: function (removeData) { send(removeData, false); }
        });
    }

    window.WpPopPopModules = { vars: V, t: t, fmt: fmt, post: post, upload: upload, dialog: dialog, notice: notice, act: act };
})(window, jQuery);
