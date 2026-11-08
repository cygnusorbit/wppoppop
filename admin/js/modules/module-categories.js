/**
 * WpPopPop Module Category manager (Settings > Module > Module Category).
 * Add, rename, delete and reorder (drag or arrows) the categories used by the filter pills on the Extend page.
 */
(function ($) {
    'use strict';

    var M = window.WpPopPopModules;
    if (!M) { return; }

    $(function () {
        var $open = $('#wpp-mod-cats-btn');
        var $modal = $('#wpp-cat-modal');
        if (!$open.length || !$modal.length) { return; }

        /* Lives under <body>, outside the settings form, so none of its fields can be saved or submitted with it. */
        $modal.appendTo(document.body);
        var $dlg = $modal.find('.wpp-cat-dlg');
        var $fixed = $('#wpp-cat-fixed');
        var $list = $('#wpp-cat-list');
        var $new = $('#wpp-cat-new');
        var $msg = $('#wpp-cat-msg');
        var $save = $('#wpp-cat-save');
        var MAX = Number(M.vars.cat_max) || 30;
        var NAME_MAX = Number(M.vars.cat_name_max) || 40;
        var $returnTo = null;
        var busy = false;

        function trim(v) { return String(v === null || v === undefined ? '' : v).replace(/^\s+|\s+$/g, ''); }

        function icon(name) { return $('<span class="dashicons" aria-hidden="true"></span>').addClass('dashicons-' + name); }

        function btn(cls, label, ico) {
            return $('<button type="button" class="wpp-cat-btn"></button>').addClass(cls).attr({ 'aria-label': label, title: label }).append(icon(ico));
        }

        function row(c) {
            var $r = $('<li class="wpp-cat-row"></li>').attr('data-slug', c.slug || '');
            $r.append($('<span class="wpp-cat-grip" aria-hidden="true"></span>').append(icon('menu')));
            $('<input type="text" class="wpp-cat-name" autocomplete="off">')
                .attr({ maxlength: NAME_MAX, 'aria-label': M.t('cat_name') }).val(c.name || '').appendTo($r);
            $r.append(btn('wpp-cat-up', M.t('cat_up'), 'arrow-up-alt2'));
            $r.append(btn('wpp-cat-down', M.t('cat_down'), 'arrow-down-alt2'));
            $r.append(btn('wpp-cat-del', M.t('cat_remove'), 'trash'));
            return $r;
        }

        function say(type, text) {
            if (!text) { $msg.prop('hidden', true).empty(); return; }
            $msg.removeClass('is-error is-success').addClass('is-' + type).text(text).prop('hidden', false);
        }

        function fill() {
            $fixed.empty();
            $list.empty();
            $.each(M.vars.categories || [], function (i, c) {
                if (c.locked) {
                    $('<li class="wpp-cat-row is-locked"></li>')
                        .append($('<span class="wpp-cat-grip" aria-hidden="true"></span>').append(icon('lock')))
                        .append($('<span class="wpp-cat-fixed-name"></span>').text(c.name))
                        .append($('<span class="wpp-cat-lock"></span>').text(M.t('cat_default')))
                        .appendTo($fixed);
                } else {
                    $list.append(row(c));
                }
            });
            if ($.fn.sortable) {
                if ($list.data('ui-sortable')) { $list.sortable('destroy'); }
                $list.sortable({
                    handle: '.wpp-cat-grip',
                    axis: 'y',
                    tolerance: 'pointer',
                    placeholder: 'wpp-cat-ph',
                    forcePlaceholderSize: true
                });
            }
        }

        function collect() {
            var out = [];
            $list.children('.wpp-cat-row').each(function () {
                out.push({ slug: String($(this).attr('data-slug') || ''), name: trim($(this).find('.wpp-cat-name').val()) });
            });
            return out;
        }

        function taken(name, except) {
            var low = name.toLowerCase();
            if (low === 'all modules') { return true; }
            var all = M.vars.categories || [];
            for (var i = 0; i < all.length; i++) {
                if (all[i].locked && String(all[i].name).toLowerCase() === low) { return true; }
            }
            var hit = false;
            $list.children('.wpp-cat-row').each(function () {
                var $n = $(this).find('.wpp-cat-name');
                if ($n[0] !== except && trim($n.val()).toLowerCase() === low) { hit = true; }
            });
            return hit;
        }

        function addNew() {
            var name = trim($new.val());
            if (!name) { say('error', M.t('cat_empty')); $new.trigger('focus'); return; }
            if (taken(name)) { say('error', M.fmt(M.t('cat_dup'), name)); $new.trigger('focus'); return; }
            if ($list.children().length >= MAX) { say('error', M.fmt(M.t('cat_max_msg'), MAX)); return; }
            var $r = row({ slug: '', name: name }).appendTo($list);
            $new.val('').trigger('focus');
            say('', '');
            var el = $list.closest('.wpp-cat-bd')[0];
            if (el) { el.scrollTop = el.scrollHeight; }
            $r.addClass('is-new');
        }

        function check() {
            var rows = collect();
            var seen = {};
            for (var i = 0; i < rows.length; i++) {
                var low = rows[i].name.toLowerCase();
                if (!low) { say('error', M.t('cat_empty')); $list.children().eq(i).find('.wpp-cat-name').trigger('focus'); return null; }
                if (low === 'all modules' || seen[low]) { say('error', M.fmt(M.t('cat_dup'), rows[i].name)); $list.children().eq(i).find('.wpp-cat-name').trigger('focus'); return null; }
                seen[low] = 1;
            }
            return rows;
        }

        function focusables() {
            return $dlg.find('button, input, a[href], [tabindex]:not([tabindex="-1"])').filter(':visible:not(:disabled)');
        }

        function close() {
            if (busy) { return; }
            $(document).off('keydown.wppcats');
            $modal.prop('hidden', true);
            say('', '');
            if ($returnTo && $returnTo.length) { $returnTo.trigger('focus'); }
        }

        function openModal() {
            $returnTo = $(document.activeElement);
            fill();
            say('', '');
            $new.val('');
            $modal.prop('hidden', false);
            $(document).off('keydown.wppcats').on('keydown.wppcats', function (e) {
                if (e.which === 27) { e.preventDefault(); close(); return; }
                if (e.which !== 9) { return; }
                var $f = focusables();
                if (!$f.length) { return; }
                var first = $f[0];
                var last = $f[$f.length - 1];
                if (e.shiftKey && (document.activeElement === first || !$dlg[0].contains(document.activeElement))) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && (document.activeElement === last || !$dlg[0].contains(document.activeElement))) { e.preventDefault(); first.focus(); }
            });
            var $first = $list.find('.wpp-cat-name').first();
            ($first.length ? $first : $new).trigger('focus');
        }

        function save() {
            if (busy) { return; }
            var rows = check();
            if (!rows) { return; }
            busy = true;
            $save.prop('disabled', true).text(M.t('cat_saving'));
            M.post('cats_save', { cats: JSON.stringify(rows) }, function (r) {
                busy = false;
                $save.prop('disabled', false).text(M.t('cat_save'));
                if (!r.ok) { say('error', r.message); return; }
                M.vars.categories = r.data.categories || M.vars.categories;
                close();
                $(document).trigger('wppoppop:module-cats-saved', [M.vars.categories]); /* WPPOPPOP_CATS_ON_EXTEND */
                M.notice($('#wpp-ext-notice').length ? $('#wpp-ext-notice') : $('#wpp-mod-notice'), 'success', r.data.message || M.t('cat_saved'));
            });
        }

        $open.on('click', openModal);
        $('#wpp-cat-close, #wpp-cat-cancel').on('click', close);
        $modal.on('mousedown', function (e) { if (e.target === $modal[0]) { close(); } });
        $('#wpp-cat-add').on('click', addNew);
        $new.on('keydown', function (e) { if (e.which === 13) { e.preventDefault(); e.stopPropagation(); addNew(); } });
        $save.on('click', save);

        $list.on('keydown', '.wpp-cat-name', function (e) { if (e.which === 13) { e.preventDefault(); e.stopPropagation(); $new.trigger('focus'); } });
        $list.on('input', '.wpp-cat-name', function () { say('', ''); });
        $list.on('click', '.wpp-cat-del', function () {
            var $r = $(this).closest('.wpp-cat-row');
            var $next = $r.next().length ? $r.next() : $r.prev();
            $r.remove();
            ($next.length ? $next.find('.wpp-cat-name') : $new).trigger('focus');
        });
        $list.on('click', '.wpp-cat-up, .wpp-cat-down', function () {
            var $b = $(this);
            var $r = $b.closest('.wpp-cat-row');
            if ($b.hasClass('wpp-cat-up')) { var $p = $r.prev('.wpp-cat-row'); if ($p.length) { $r.insertBefore($p); } }
            else { var $n = $r.next('.wpp-cat-row'); if ($n.length) { $r.insertAfter($n); } }
            $b.trigger('focus');
        });
    });
})(jQuery);
