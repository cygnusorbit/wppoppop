/**
 * WpPopPop Extend page: lists uploads, uninstalled / deleted built-ins and locked modules;
 * upload a module.zip, install, uninstall and delete.
 */
(function ($) {
    'use strict';

    var M = window.WpPopPopModules;
    if (!M) { return; }

    $(function () {
        var items = (M.vars.modules || []).slice();
        var filter = 'all';
        var query = '';
        var selected = null;
        var maxZip = Number(M.vars.max_zip) || 0;

        var $grid = $('#wpp-ext-grid');
        var $notice = $('#wpp-ext-notice');
        var $search = $('#wpp-ext-search');
        var $filters = $('#wpp-ext-filters');
        var $drop = $('#wpp-ext-drop');
        var $file = $('#wpp-ext-file');
        var $name = $('#wpp-ext-file-name');
        var $btn = $('#wpp-ext-upload-btn');

        function isAvail(m) { return !m.locked && (m.state === 'available' || m.state === 'deleted'); }

        function inFilter(m, f) {
            switch (f) {
                case 'available': return isAvail(m);
                case 'installed': return m.state === 'installed';
                case 'builtin': return m.source === 'core';
                case 'uploaded': return m.source === 'uploaded';
                default: return true;
            }
        }

        function matches(m) {
            if (!inFilter(m, filter)) { return false; }
            if (!query) { return true; }
            return (m.name + ' ' + m.description + ' ' + m.author + ' ' + m.slug).toLowerCase().indexOf(query) !== -1;
        }

        function find(slug) {
            for (var i = 0; i < items.length; i++) { if (items[i].slug === slug) { return i; } }
            return -1;
        }

        function upsert(m) {
            var i = find(m.slug);
            if (i === -1) { items.push(m); } else { items[i] = m; }
        }

        function nameOf(slug) {
            var i = find(slug);
            return i === -1 ? slug : items[i].name;
        }

        function pill(text, cls) {
            return $('<span class="wpp-mod-pill"></span>').addClass(cls || '').text(text);
        }

        function button(label, cls) {
            return $('<button type="button" class="button"></button>').addClass(cls || '').text(label);
        }

        function card(m) {
            var $c = $('<article class="wpp-ext-item"></article>').attr('data-slug', m.slug);
            var $head = $('<div class="wpp-ext-item-head"></div>').appendTo($c);
            $('<h3 class="wpp-ext-item-name"></h3>').text(m.name).appendTo($head);
            $head.append(pill(m.source === 'core' ? M.t('builtin') : M.t('uploaded')));
            if (m.locked) {
                $head.append(pill(M.t('locked'), 'is-lock'));
            } else if (m.state === 'installed') {
                $head.append(pill(M.t('st_installed'), 'is-ok'));
            } else if (m.state === 'deleted') {
                $head.append(pill(M.t('st_deleted'), 'is-bad'));
            } else {
                $head.append(pill(M.t('st_available'), 'is-warn'));
            }
            if (m.deprecated) { $head.append(pill(M.t('deprecated'), 'is-warn')); }

            $('<p class="wpp-ext-item-desc"></p>').text(m.description || '').appendTo($c);

            var $meta = $('<p class="wpp-ext-item-meta"></p>').appendTo($c);
            if (m.version) { $('<span></span>').text(M.fmt(M.t('version'), m.version)).appendTo($meta); }
            if (m.author) { $('<span></span>').text(M.fmt(M.t('by'), m.author)).appendTo($meta); }
            if (m.requires && m.requires.length) {
                $('<span></span>').text(M.fmt(M.t('requires'), $.map(m.requires, nameOf).join(', '))).appendTo($meta);
            }

            var $act = $('<div class="wpp-ext-item-actions"></div>').appendTo($c);
            if (!m.locked) {
                if (m.state === 'installed') {
                    $('<a class="button"></a>').attr('href', M.vars.modules_url).text(M.t('manage')).appendTo($act);
                    $act.append(button(M.t('uninstall')).attr('data-act', 'uninstall'));
                } else {
                    var $inst = button(m.state === 'deleted' ? M.t('restore') : M.t('install'), 'button-primary').attr('data-act', 'install');
                    if (!m.compatible) {
                        $inst.prop('disabled', true);
                        $('<span class="wpp-ext-item-note"></span>').text(M.t('needs_version')).appendTo($act);
                    }
                    $act.prepend($inst);
                }
                if (m.state !== 'deleted') {
                    $act.append(button(M.t('delete'), 'button-link button-link-delete').attr('data-act', 'delete'));
                }
            }
            if (!$act.children().length) { $act.remove(); }
            return $c;
        }

        function counts() {
            $filters.find('button').each(function () {
                var f = $(this).data('filter');
                var n = 0;
                for (var i = 0; i < items.length; i++) { if (inFilter(items[i], f)) { n++; } }
                $(this).find('.count').text('(' + n + ')');
            });
        }

        function render() {
            $grid.empty();
            var shown = 0;
            for (var i = 0; i < items.length; i++) {
                if (matches(items[i])) { $grid.append(card(items[i])); shown++; }
            }
            if (!shown) { $('<p class="wpp-ext-empty"></p>').text(M.t(items.length ? 'no_match' : 'empty')).appendTo($grid); }
            counts();
        }

        /* filters + search */
        $filters.on('click', 'button', function () {
            filter = String($(this).data('filter'));
            $filters.find('button').removeClass('is-active');
            $(this).addClass('is-active');
            render();
        });
        $search.on('input', function () { query = String($search.val() || '').replace(/^\s+|\s+$/g, '').toLowerCase(); render(); });
        $search.on('keydown', function (e) { if (e.which === 13) { e.preventDefault(); } });

        /* card actions */
        $grid.on('click', '[data-act]', function () {
            var $c = $(this).closest('.wpp-ext-item');
            var i = find(String($c.data('slug')));
            if (i === -1) { return; }
            var m = items[i];
            var action = String($(this).data('act'));
            M.act(action, m, {
                busy: function (on) { $c.toggleClass('is-busy', on); },
                done: function (r) {
                    if (!r.ok) { M.notice($notice, 'error', r.message); render(); return; }
                    if (action === 'delete') {
                        if (m.source === 'uploaded') { items.splice(i, 1); } else { m.state = 'deleted'; }
                        M.notice($notice, 'success', r.data.message);
                    } else if (r.data.module) {
                        upsert(r.data.module);
                        M.notice($notice, 'success', r.data.message, action === 'install' ? { href: M.vars.modules_url, text: M.t('manage_link') } : null);
                    }
                    render();
                }
            });
        });

        /* upload */
        if (!$drop.length) { return; }

        function setFile(f) {
            selected = null;
            $name.text('');
            if (f) {
                if (!/\.zip$/i.test(f.name)) { M.notice($notice, 'error', M.t('not_zip')); }
                else if (maxZip && f.size > maxZip) { M.notice($notice, 'error', M.fmt(M.t('too_big'), Math.round(maxZip / 1048576) + ' MB')); }
                else { selected = f; $name.text(f.name); $notice.prop('hidden', true); }
            }
            $btn.prop('disabled', !selected);
        }

        $file.on('change', function () { setFile(this.files && this.files[0] ? this.files[0] : null); });
        $drop.on('dragenter dragover', function (e) { e.preventDefault(); $drop.addClass('is-over'); });
        $drop.on('dragleave dragend', function () { $drop.removeClass('is-over'); });
        $drop.on('drop', function (e) {
            e.preventDefault();
            $drop.removeClass('is-over');
            var dt = e.originalEvent && e.originalEvent.dataTransfer;
            if (dt && dt.files && dt.files.length) { setFile(dt.files[0]); }
        });

        function send(replace) {
            if (!selected) { M.notice($notice, 'error', M.t('pick_zip')); return; }
            var label = $btn.text();
            $btn.prop('disabled', true).text(M.t('uploading'));
            M.upload(selected, replace, function (r) {
                $btn.text(label);
                if (!r.ok && r.code === 'exists' && r.data) {
                    $btn.prop('disabled', false);
                    M.dialog({
                        title: M.t('replace_title'),
                        message: M.fmt(M.t('replace_msg'), r.data.name, r.data.old_version, r.data.new_version),
                        confirm: M.t('replace'),
                        onConfirm: function () { send(true); }
                    });
                    return;
                }
                if (!r.ok) { $btn.prop('disabled', false); M.notice($notice, 'error', r.message); return; }
                if (r.data.module) { upsert(r.data.module); }
                selected = null;
                $file.val('');
                $name.text('');
                M.notice($notice, 'success', r.data.message);
                render();
            });
        }

        $btn.on('click', function () { send(false); });
        $('#wpp-ext-bar-upload').on('click', function () {
            var card = document.getElementById('wpp-ext-upload-card');
            if (card && card.scrollIntoView) { card.scrollIntoView(); }
            $file.trigger('click');
        });

        render();
    });
})(jQuery);
