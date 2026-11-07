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
        var DEF = 'all-modules';
        var cats = (M.vars.categories || []).slice();
        var cat = DEF;
        var query = '';
        var selected = null;
        var maxZip = Number(M.vars.max_zip) || 0;

        var $grid = $('#wpp-ext-grid');
        var $notice = $('#wpp-ext-notice');
        var $search = $('#wpp-ext-search');
        var $filters = $('#wpp-ext-filters');
        var $cats = $('#wpp-ext-cats');
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

        function textMatch(m) {
            if (!query) { return true; }
            return (m.name + ' ' + m.description + ' ' + m.author + ' ' + m.slug).toLowerCase().indexOf(query) !== -1;
        }

        /* "All Modules" (the default category) shows everything, like "All Templates" in the Library. */
        function inCat(m, c) { return c === DEF || (m.category || DEF) === c; }

        function matches(m) { return inFilter(m, filter) && inCat(m, cat) && textMatch(m); }

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

        function catName(slug) {
            for (var i = 0; i < cats.length; i++) { if (cats[i].slug === slug) { return cats[i].name; } }
            return cats.length ? cats[0].name : '';
        }

        function isImg(u) {
            if (!u || u.length > 500 || /[\s<>"'\\]/.test(u)) { return false; }
            if (/^https?:\/\/\S+$/i.test(u) || /^\/\/[\w.\-]+(?::\d+)?(?:\/\S*)?$/.test(u)) { return true; }
            return u.indexOf(':') === -1 && !/(?:^|\/)\.\.(?:\/|$)/.test(u.replace(/[?#].*$/, ''));
        }

        function imgSrc(u, m) {
            return (u && !/^(?:https?:\/\/|\/)/i.test(u) && m.image_base) ? m.image_base + u : u;
        }

        function icon(name, label, cls) {
            return $('<button type="button" class="button wpp-ext-icon-btn"></button>').addClass(cls || '')
                .attr({ title: label, 'aria-label': label })
                .append($('<span class="dashicons" aria-hidden="true"></span>').addClass('dashicons-' + name));
        }

        function fillThumb($t, url) {
            $t.empty().removeClass('is-empty');
            var $ph = function () { $t.empty().addClass('is-empty').append('<span class="dashicons dashicons-admin-plugins"></span>'); };
            if (!url) { $ph(); return $t; }
            $('<img alt="" width="40" height="40" decoding="async">').on('error', $ph).attr('src', url).appendTo($t);
            return $t;
        }

        function thumb(url) {
            return fillThumb($('<span class="wpp-ext-item-img" aria-hidden="true"></span>'), url);
        }

        function customBtn(b) {
            var $a = $('<a class="button"></a>').attr('href', b.url);
            if (b.new_tab) { $a.attr({ target: '_blank', rel: 'noopener noreferrer' }); }
            if (b.icon) {
                return $a.addClass('wpp-ext-icon-btn').attr({ title: b.label, 'aria-label': b.label })
                    .append($('<span class="dashicons" aria-hidden="true"></span>').addClass('dashicons-' + b.icon));
            }
            return $a.toggleClass('button-primary', !!b.primary).text(b.label);
        }

        function card(m) {
            var $c = $('<article class="wpp-ext-item"></article>').attr('data-slug', m.slug);
            var $top = $('<div class="wpp-ext-item-top"></div>').appendTo($c);
            $top.append(thumb(m.image));
            var $head = $('<div class="wpp-ext-item-head"></div>').appendTo($top);
            $('<h3 class="wpp-ext-item-name"></h3>').text(m.name).appendTo($head);
            if (m.show_source !== false) {
                $head.append(pill(m.source === 'core' ? M.t('builtin') : M.t('uploaded')));
            }
            if (m.show_state !== false) {
                if (m.locked) {
                    $head.append(pill(M.t('locked'), 'is-lock'));
                } else if (m.state === 'installed') {
                    $head.append(pill(M.t('st_installed'), 'is-ok'));
                } else if (m.state === 'deleted') {
                    $head.append(pill(M.t('st_deleted'), 'is-bad'));
                } else {
                    $head.append(pill(M.t('st_available'), 'is-warn'));
                }
            }
            if (m.deprecated) { $head.append(pill(M.t('deprecated'), 'is-warn')); }
            $.each(m.pills || [], function (i, p) {
                var o = (p && typeof p === 'object') ? p : { text: p };
                $head.append(pill(o.text, o.style === 'primary' ? 'is-custom is-primary' : 'is-custom'));
            });

            $('<p class="wpp-ext-item-desc"></p>').text(m.description || '').appendTo($c);
            if (m.links && m.links.length) {
                var $ln = $('<p class="wpp-ext-item-links"></p>').appendTo($c);
                $.each(m.links, function (i, l) {
                    var $l = $('<a></a>').attr('href', l.url).text(l.label).appendTo($ln);
                    if (l.new_tab) { $l.attr({ target: '_blank', rel: 'noopener noreferrer' }); }
                });
            }

            var $meta = $('<p class="wpp-ext-item-meta"></p>').appendTo($c);
            if (m.version) { $('<span></span>').text(M.fmt(M.t('version'), m.version)).appendTo($meta); }
            if (m.author) { $('<span></span>').text(M.fmt(M.t('by'), m.author)).appendTo($meta); }
            if (m.requires && m.requires.length) {
                $('<span></span>').text(M.fmt(M.t('requires'), $.map(m.requires, nameOf).join(', '))).appendTo($meta);
            }

            $('<span></span>').text(M.t('cat_label') + ': ' + catName(m.category)).appendTo($meta);

            var $act = $('<div class="wpp-ext-item-actions"></div>').appendTo($c);
            var $end = $('<span class="wpp-ext-act-end"></span>');
            if (!m.locked) {
                if (m.state === 'installed') {
                    if (m.can_manage !== false) {
                        $('<a class="button wpp-ext-icon-btn"></a>').attr({ href: M.vars.modules_url, title: M.t('manage'), 'aria-label': M.t('manage') })
                            .append('<span class="dashicons dashicons-admin-generic" aria-hidden="true"></span>').appendTo($act);
                    }
                    if (m.can_uninstall !== false) {
                        $act.append(icon('remove', M.t('uninstall')).attr('data-act', 'uninstall'));
                    }
                } else {
                    var $inst = button(m.state === 'deleted' ? M.t('restore') : M.t('install'), 'button-primary').attr('data-act', 'install');
                    if (!m.compatible) {
                        $inst.prop('disabled', true);
                        $('<span class="wpp-ext-item-note"></span>').text(M.t('needs_version')).appendTo($act);
                    }
                    $act.prepend($inst);
                }
            }
            $.each(m.buttons || [], function (i, b) { $act.append(customBtn(b)); });
            if (m.can_edit !== false) {
                $end.append(icon('edit', M.t('edit')).attr('data-edit', '1'));
            }
            if (!m.locked && m.state !== 'deleted' && m.can_delete !== false) {
                $end.append(icon('trash', M.t('delete'), 'wpp-ext-del').attr('data-act', 'delete'));
            }
            $act.append($end);
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

        function renderCats() {
            var known = false;
            $.each(cats, function (i, c) { if (c.slug === cat) { known = true; } });
            if (!known) { cat = DEF; }
            $cats.empty();
            if (!cats.length) { return; }
            $.each(cats, function (i, c) {
                var n = 0;
                for (var k = 0; k < items.length; k++) {
                    if (inFilter(items[k], filter) && textMatch(items[k]) && inCat(items[k], c.slug)) { n++; }
                }
                var $p = $('<button type="button" class="wpp-cat-pill"></button>')
                    .attr({ 'data-cat': c.slug, 'aria-pressed': c.slug === cat ? 'true' : 'false' })
                    .toggleClass('is-active', c.slug === cat);
                $('<span class="label"></span>').text(c.name).appendTo($p);
                $('<span class="count"></span>').text('(' + n + ')').appendTo($p);
                $cats.append($p);
            });
            $('<a class="wpp-cat-manage"></a>').attr('href', M.vars.modules_url).text(M.t('cat_manage')).appendTo($cats);
        }

        function render() {
            $grid.empty();
            var shown = 0;
            for (var i = 0; i < items.length; i++) {
                if (matches(items[i])) { $grid.append(card(items[i])); shown++; }
            }
            if (!shown) { $('<p class="wpp-ext-empty"></p>').text(M.t(items.length ? 'no_match' : 'empty')).appendTo($grid); }
            counts();
            renderCats();
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

        /* category pills + Edit (category -> module.json) */
        $cats.on('click', '.wpp-cat-pill', function () {
            cat = String($(this).attr('data-cat'));
            render();
        });

        function editDialog(m) {
            var $prev = $(document.activeElement);
            var id = 'wpp-ext-edit';
            var $back = $('<div class="wpp-mod-backdrop"></div>');
            var $dlg = $('<div class="wpp-mod-modal wpp-ext-edit" role="dialog" aria-modal="true" tabindex="-1"></div>')
                .attr('aria-labelledby', id + '-t').appendTo($back);
            $('<h3 class="wpp-mod-modal-title"></h3>').attr('id', id + '-t').text(M.fmt(M.t('edit_title'), m.name)).appendTo($dlg);
            var $row = $('<div class="wpp-ext-edit-row"></div>').appendTo($dlg);
            $('<label></label>').attr('for', id + '-cat').text(M.t('cat_label')).appendTo($row);
            var $sel = $('<select></select>').attr('id', id + '-cat').appendTo($row);
            $.each(cats, function (i, c) { $('<option></option>').val(c.slug).text(c.name).appendTo($sel); });
            $sel.val(m.category || DEF);
            var $field = $('<div class="wpp-ext-edit-field"></div>').appendTo($dlg);
            $('<label></label>').attr('for', id + '-img').text(M.t('edit_image')).appendTo($field);
            var $imgRow = $('<div class="wpp-ext-edit-img"></div>').appendTo($field);
            var $pv = thumb(m.image).appendTo($imgRow);
            var $img = $('<input type="url">').attr({ id: id + '-img', placeholder: 'images/icon.png', maxlength: 500, autocomplete: 'off', spellcheck: 'false' })
                .val(m.image_raw || '').appendTo($imgRow);
            var $err = $('<p class="wpp-ext-edit-err" role="alert" hidden></p>').text(M.t('edit_img_bad')).appendTo($dlg);
            var pvTimer = 0;
            $img.on('input change', function () {
                $err.prop('hidden', true);
                clearTimeout(pvTimer);
                pvTimer = setTimeout(function () {
                    var u = String($img.val() || '').replace(/^\s+|\s+$/g, '');
                    fillThumb($pv, isImg(u) ? imgSrc(u, m) : '');
                }, 350);
            });
            $('<p class="wpp-mod-modal-msg wpp-ext-edit-note"></p>').text(M.t('edit_note')).appendTo($dlg);
            var $act = $('<div class="wpp-mod-modal-actions"></div>').appendTo($dlg);
            var $cancel = $('<button type="button" class="button"></button>').text(M.t('cancel')).appendTo($act);
            var $ok = $('<button type="button" class="button button-primary"></button>').text(M.t('edit_save')).appendTo($act);
            var busy = false;

            function close() {
                if (busy) { return; }
                $(document).off('keydown.wppext');
                $back.remove();
                if ($prev.length) { $prev.trigger('focus'); }
            }

            function save() {
                if (busy) { return; }
                var url = String($img.val() || '').replace(/^\s+|\s+$/g, '');
                if (url !== '' && !isImg(url)) { $err.prop('hidden', false); $img.trigger('focus'); return; }
                busy = true;
                $ok.prop('disabled', true).text(M.t('edit_saving'));
                M.post('set_category', { slug: m.slug, category: $sel.val(), image: url }, function (r) {
                    busy = false;
                    close();
                    if (!r.ok) { M.notice($notice, 'error', r.message); return; }
                    if (r.data.module) { upsert(r.data.module); }
                    M.notice($notice, r.data.json === false ? 'info' : 'success', r.data.message);
                    render();
                });
            }

            $img.on('keydown', function (e) { if (e.which === 13) { e.preventDefault(); save(); } });
            $cancel.on('click', close);
            $ok.on('click', save);
            $back.on('mousedown', function (e) { if (e.target === $back[0]) { close(); } });
            $(document).off('keydown.wppext').on('keydown.wppext', function (e) {
                if (e.which === 27) { e.preventDefault(); close(); return; }
                if (e.which !== 9) { return; }
                var $f = $dlg.find('select, input, button').filter(':visible:not(:disabled)');
                if (!$f.length) { return; }
                var first = $f[0];
                var last = $f[$f.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            });
            $back.appendTo(document.body);
            $sel.trigger('focus');
        }

        $grid.on('click', '[data-edit]', function () {
            var i = find(String($(this).closest('.wpp-ext-item').data('slug')));
            if (i !== -1) { editDialog(items[i]); }
        });

        /* upload */
        if (!$drop.length) { render(); return; }

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

        function compareBody(d) {
            var $w = $('<div class="wpp-ext-cmp"></div>');
            function col(label, m, cls) {
                var $c = $('<div class="wpp-ext-cmp-col"></div>').addClass(cls).appendTo($w);
                $('<span class="wpp-ext-cmp-tag"></span>').text(label).appendTo($c);
                var $t = $('<div class="wpp-ext-cmp-top"></div>').appendTo($c);
                $t.append(thumb(m.image));
                $('<strong class="wpp-ext-cmp-name"></strong>').text(m.name).appendTo($t);
                var $p = $('<p class="wpp-ext-cmp-line"></p>').appendTo($c);
                $('<span class="wpp-mod-pill"></span>').text(M.fmt(M.t('version'), m.version)).appendTo($p);
                $p.append(' ');
                $('<span class="wpp-mod-pill"></span>').text(m.source === 'core' ? M.t('builtin') : M.t('uploaded')).appendTo($p);
                if (m.author) { $('<p class="wpp-ext-cmp-line"></p>').text(M.fmt(M.t('by'), m.author)).appendTo($c); }
                if (m.description) { $('<p class="wpp-ext-cmp-line"></p>').text(m.description).appendTo($c); }
            }
            col(M.t('cmp_current'), d.old, '');
            $('<span class="wpp-ext-cmp-arrow dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>').appendTo($w);
            col(M.t('cmp_new'), d['new'], 'is-new');
            return $w;
        }

        function send(replace) {
            if (!selected) { M.notice($notice, 'error', M.t('pick_zip')); return; }
            var label = $btn.text();
            $btn.prop('disabled', true).text(M.t('uploading'));
            M.upload(selected, replace, function (r) {
                $btn.text(label);
                if (!r.ok && r.code === 'exists' && r.data) {
                    $btn.prop('disabled', false);
                    var d = r.data;
                    var both = !!(d.old && d['new']);
                    M.dialog({
                        title: M.t('replace_title'),
                        message: both ? M.t(d.old.source === 'core' ? 'cmp_msg_core' : 'cmp_msg') : M.fmt(M.t('replace_msg'), d.name, d.old_version, d.new_version),
                        body: both ? compareBody(d) : null,
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
