/* Notifications: Inspector DOM <-> JS model.
 * Wraps getSettings / setSettings so the notification cards (recipient, subject, message, attachments, conditional
 * logic) are saved to and restored from settings.notifications. Sending and sanitising stay in the core
 * (WpPopPop_Notifications). The accordion ships only the list container and the add button; the cards are built here.
 * Only loaded while the module is installed. ES5 only (Chrome, Safari, Firefox, Edge). */
/* WPPOPPOP_NOTIFICATIONS_UI: Notifications module (Inspector DOM <-> settings.notifications) */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._ntfWrapped) return;
    S._ntfWrapped = true;

    function tr(t) { return (window.wp && window.wp.i18n && window.wp.i18n.__) ? window.wp.i18n.__(t, 'wppoppop') : t; }

    var MAX = 20;
    var list = [];
    var OPS = [['is', 'is'], ['is_not', 'is not'], ['contains', 'contains'], ['not_contains', 'does not contain'], ['empty', 'is empty'], ['not_empty', 'is not empty']];

    function uid() { return 'n_' + Math.random().toString(36).slice(2, 8); }
    function esc(v) { return $('<div></div>').text(v === undefined || v === null ? '' : String(v)).html(); }
    function isTrue(v) { return v === true || v === 1 || v === '1' || v === 'true'; }

    function blank() {
        return {
            id: uid(), name: 'Notification', enable: true, send: 'after_submission',
            recipient: '', subject: 'New submission from {{form-name}}', message: '{{form-data}}',
            attachments: [], reply_to: '', from_email: '{{global-from-email}}', from_name: '{{global-from-name}}', format: 'html', /* WPPOPPOP_NTF_FORMAT */
            logic_enable: false, logic_match: 'all', rules: []
        };
    }

    function fieldTokens() {
        var out = [], seen = {};
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.canvases) {
            Object.keys(core.state.canvases).forEach(function(c) {
                (core.state.canvases[c] || []).forEach(function(el) {
                    var tok = el && (el.field_name || el.fieldName || el.fieldId);
                    if (!tok || seen[tok]) return;
                    seen[tok] = true;
                    out.push({ tok: String(tok), type: el.type || 'field' });
                });
            });
        }
        return out;
    }

    function tagOptions() {
        var h = '<option value="">&lt;/&gt; Tags</option><optgroup label="Form values">';
        $.each(fieldTokens(), function(i, f) { h += '<option value="{{' + esc(f.tok) + '}}">' + esc(f.tok) + ' | ' + esc(f.type) + '</option>'; });
        h += '<option value="{{email}}">email</option><option value="{{form-data}}">All form data</option></optgroup><optgroup label="General">' +
            '<option value="{{form-name}}">Popup name</option><option value="{{submission-id}}">Submission ID</option>' +
            '<option value="{{country}}">Visitor country</option><option value="{{site-name}}">Site name</option>' +
            '<option value="{{global-from-email}}">Global from email</option><option value="{{global-from-name}}">Global from name</option></optgroup>';
        return h;
    }

    function tag(k) { return '<button type="button" class="button button-small wppoppop-ntf-tag" data-for="' + k + '" title="Insert merge tag" aria-label="Insert merge tag" aria-haspopup="true" aria-expanded="false" style="display:inline-flex;align-items:center;justify-content:center;min-height:22px;height:22px;line-height:1;padding:0 5px;margin:0 0 0 auto;"><span class="dashicons" aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;font-size:16px;line-height:1;margin:0;padding:0;">&#xf150;</span></button>'; }

    function inputRow(label, k, n, big) {
        var ctl = big
            ? '<textarea class="widefat" rows="5" data-k="' + k + '">' + esc(n[k]) + '</textarea>'
            : '<input type="text" class="widefat" data-k="' + k + '" value="' + esc(n[k]) + '">';
        return '<div style="margin:0 0 10px;"><div class="wppoppop-ntf-lblrow" style="position:relative;display:flex;align-items:center;gap:6px;margin:0 0 4px;"><label style="font-weight:600;">' + label + '</label>' + tag(k) + '</div>' + ctl + '</div>';
    }

    function ruleHtml(r) {
        var f = '<select data-r="field"><option value="">Select field</option>';
        var found = false;
        $.each(fieldTokens(), function(i, t) { var sel = (r.field === t.tok); if (sel) found = true; f += '<option value="' + esc(t.tok) + '"' + (sel ? ' selected' : '') + '>' + esc(t.tok) + '</option>'; });
        if (r.field && !found) { f += '<option value="' + esc(r.field) + '" selected>' + esc(r.field) + '</option>'; }
        f += '</select>';
        var o = '<select data-r="operator">';
        $.each(OPS, function(i, p) { o += '<option value="' + p[0] + '"' + (r.operator === p[0] ? ' selected' : '') + '>' + p[1] + '</option>'; });
        o += '</select>';
        return '<div class="wppoppop-ntf-rule" style="display:flex;gap:4px;margin:4px 0;flex-wrap:wrap;">' + f + o +
            '<input type="text" data-r="value" value="' + esc(r.value) + '" placeholder="Value" style="flex:1;min-width:70px;">' +
            '<button type="button" class="button button-small wppoppop-ntf-rule-del" aria-label="Remove rule">&times;</button></div>';
    }

    function cardHtml(n, idx) {
        var att = '';
        $.each(n.attachments || [], function(i, a) {
            att += '<div style="display:flex;align-items:center;gap:6px;margin:3px 0;font-size:12px;"><span class="dashicons dashicons-media-default"></span><span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + esc(a.name || a.url || ('#' + a.id)) + '</span><button type="button" class="button button-small wppoppop-ntf-att-del" data-i="' + i + '">&times;</button></div>';
        });
        var rules = '';
        $.each(n.rules || [], function(i, r) { rules += ruleHtml(r); });
        return '<div class="wppoppop-ntf-card" data-id="' + esc(n.id) + '" style="border:1px solid #cbd5e1;border-radius:0px;margin-bottom:10px;background:#f8fafc;">' +
            '<div class="wppoppop-ntf-head" style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;cursor:pointer;">' +
                '<strong class="wppoppop-ntf-title" style="font-style:italic;">' + esc(n.name || 'Notification') + '</strong>' +
                '<span class="wppoppop-ntf-actions" style="display:inline-flex;align-items:center;gap:10px;">' +
                    '<label class="wppoppop-toggle-switch wppoppop-ntf-toggle" title="Enabled" style="margin:0;"><input type="checkbox" data-k="enable" aria-label="Enabled"' + (isTrue(n.enable) ? ' checked' : '') + '><span class="wppoppop-toggle-slider"></span></label>' +
                    '<button type="button" class="button-link wppoppop-ntf-del" title="Delete" aria-label="Delete notification" style="color:#b91c1c;"><span class="dashicons dashicons-trash"></span></button>' +
                '</span>' +
            '</div>' +
            '<div class="wppoppop-ntf-body" style="padding:6px 10px 10px;' + (idx === 0 && list.length === 1 ? '' : 'display:none;') + '">' +
                '<div style="margin:0 0 10px;"><label style="font-weight:600;display:block;">Name</label><input type="text" class="widefat" data-k="name" value="' + esc(n.name) + '"></div>' +
                '<div style="margin:0 0 10px;"><label style="font-weight:600;display:block;">Send</label><select class="widefat" data-k="send">' +
                    '<option value="after_submission"' + (n.send === 'after_submission' ? ' selected' : '') + '>After successful form submission</option>' +
                    '<option value="after_confirmation"' + (n.send === 'after_confirmation' ? ' selected' : '') + '>After double opt-in confirmation (needs Double Opt-In)</option></select></div>' +
                inputRow('Recipient', 'recipient', n, false) +
                inputRow('Subject', 'subject', n, false) +
                inputRow('Message', 'message', n, true) +
                '<div style="margin:0 0 10px;"><label style="font-weight:600;display:block;">Format</label><select class="widefat" data-k="format"><option value="html"' + (n.format !== 'text' ? ' selected' : '') + '>HTML</option><option value="text"' + (n.format === 'text' ? ' selected' : '') + '>Plain text</option></select><p class="description" style="margin:4px 0 0;font-size:11px;color:#64748b;">HTML sends a rich email: tags such as &lt;b&gt;, &lt;a&gt; and &lt;img&gt; are allowed in the Message. Plain text strips all tags.</p></div>' +
                '<div style="margin:0 0 10px;"><label style="font-weight:600;display:block;">Attachments</label>' + att + '<button type="button" class="button button-small wppoppop-ntf-att-add" style="display:inline-flex;align-items:center;justify-content:center;gap:4px;line-height:1;"><span class="dashicons dashicons-plus" style="display:inline-flex;align-items:center;justify-content:center;font-size:14px;width:14px;height:14px;line-height:1;margin:0;padding:0;"></span>Add file</button></div>' +
                inputRow('Reply-To', 'reply_to', n, false) +
                '<div style="margin:0 0 10px;"><label style="font-weight:600;display:block;">From</label><div style="display:flex;gap:6px;"><input type="text" class="widefat" data-k="from_email" value="' + esc(n.from_email) + '"><input type="text" class="widefat" data-k="from_name" value="' + esc(n.from_name) + '"></div></div>' +
                '<label style="display:flex;align-items:center;gap:8px;margin:0 0 6px;font-weight:600;"><input type="checkbox" data-k="logic_enable"' + (isTrue(n.logic_enable) ? ' checked' : '') + '> Enable conditional logic</label>' +
                '<div class="wppoppop-ntf-logic" style="' + (isTrue(n.logic_enable) ? '' : 'display:none;') + 'padding:6px;border:1px dashed #cbd5e1;border-radius:0px;">' +
                    '<div style="margin-bottom:4px;">Send if <select data-k="logic_match"><option value="all"' + (n.logic_match !== 'any' ? ' selected' : '') + '>all</option><option value="any"' + (n.logic_match === 'any' ? ' selected' : '') + '>any</option></select> of the rules match:</div>' +
                    rules + '<button type="button" class="button button-small wppoppop-ntf-rule-add">+ Add rule</button>' +
                '</div>' +
            '</div></div>';
    }

    function render() {
        var $l = $('#wppoppop-ntf-list');
        if (!$l.length) return;
        var openIds = {};
        $l.find('.wppoppop-ntf-card').each(function() { if ($(this).find('.wppoppop-ntf-body').is(':visible')) openIds[$(this).attr('data-id')] = true; });
        var h = '';
        $.each(list, function(i, n) { h += cardHtml(n, i); });
        if (!list.length) { h = '<p class="description" style="margin:0;">No notifications yet. Click "Add notification".</p>'; }
        $l.html(h);
        $l.find('.wppoppop-ntf-card').each(function() { if (openIds[$(this).attr('data-id')]) $(this).find('.wppoppop-ntf-body').show(); });
        $('#wppoppop-ntf-add').prop('disabled', list.length >= MAX);
    }

    function byId(id) { for (var i = 0; i < list.length; i++) { if (list[i].id === id) return list[i]; } return null; }
    function cur($el) { return byId($el.closest('.wppoppop-ntf-card').attr('data-id')); }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        out.notifications = $.map(list, function(n) { return $.extend(true, {}, n); });
        return out;
    };

    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        var src = settings && settings.notifications;
        if (src && !$.isArray(src) && typeof src === 'object') { src = $.map(src, function(v) { return v; }); }
        list = [];
        $.each($.isArray(src) ? src : [], function(i, n) {
            if (!n || typeof n !== 'object') return;
            var m = $.extend(true, blank(), n);
            m.id = n.id ? String(n.id) : uid();
            m.enable = isTrue(n.enable) || n.enable === undefined;
            m.logic_enable = isTrue(n.logic_enable);
            m.attachments = $.isArray(n.attachments) ? n.attachments : [];
            m.rules = $.isArray(n.rules) ? n.rules : [];
            list.push(m);
        });
        render();
        return r;
    };

    $(document)
        .off('.wppNtf')
        .on('click.wppNtf', '#wppoppop-ntf-add', function(e) {
            e.preventDefault();
            if (list.length >= MAX) return;
            var n = blank();
            list.push(n);
            render();
            $('#wppoppop-ntf-list .wppoppop-ntf-card[data-id="' + n.id + '"] .wppoppop-ntf-body').show();
        })
        .on('click.wppNtf', '.wppoppop-ntf-head', function(e) {
            if ($(e.target).closest('.wppoppop-ntf-del, .wppoppop-ntf-toggle').length) return;
            $(this).next('.wppoppop-ntf-body').slideToggle(120);
        })
        .on('click.wppNtf', '.wppoppop-ntf-del', function(e) {
            e.preventDefault(); e.stopPropagation();
            var n = cur($(this));
            if (!n || !window.confirm(tr('Delete this notification?'))) return;
            list = $.grep(list, function(x) { return x.id !== n.id; });
            render();
        })
        .on('input.wppNtf change.wppNtf', '#wppoppop-ntf-list [data-k]', function() {
            var n = cur($(this)); if (!n) return;
            var k = $(this).attr('data-k');
            n[k] = ($(this).attr('type') === 'checkbox') ? $(this).is(':checked') : $(this).val();
            if (k === 'name') { $(this).closest('.wppoppop-ntf-card').find('.wppoppop-ntf-title').text(n.name || 'Notification'); }
            if (k === 'logic_enable') { $(this).closest('.wppoppop-ntf-card').find('.wppoppop-ntf-logic').toggle(!!n.logic_enable); }
        })
        .on('change.wppNtf', '.wppoppop-ntf-tag-legacy', function() {
            var t = $(this).val(); if (!t) return;
            var $c = $(this).closest('.wppoppop-ntf-card');
            var $t = $c.find('[data-k="' + $(this).attr('data-for') + '"]');
            var el = $t.get(0); if (!el) return;
            var v = $t.val() || '';
            var s = (typeof el.selectionStart === 'number') ? el.selectionStart : v.length;
            var e2 = (typeof el.selectionEnd === 'number') ? el.selectionEnd : s;
            $t.val(v.slice(0, s) + t + v.slice(e2));
            try { el.focus(); el.setSelectionRange(s + t.length, s + t.length); } catch (err) {}
            $(this).val('');
            $t.trigger('input');
        })
        .on('click.wppNtf', '.wppoppop-ntf-rule-add', function(e) {
            e.preventDefault();
            var n = cur($(this)); if (!n) return;
            if (n.rules.length >= 20) return;
            n.rules.push({ field: '', operator: 'is', value: '' });
            render();
            $('#wppoppop-ntf-list .wppoppop-ntf-card[data-id="' + n.id + '"] .wppoppop-ntf-body').show();
        })
        .on('click.wppNtf', '.wppoppop-ntf-rule-del', function(e) {
            e.preventDefault();
            var n = cur($(this)); if (!n) return;
            n.rules.splice($(this).closest('.wppoppop-ntf-rule').index('.wppoppop-ntf-rule') - $(this).closest('.wppoppop-ntf-card').find('.wppoppop-ntf-rule').first().index('.wppoppop-ntf-rule'), 1);
            render();
        })
        .on('input.wppNtf change.wppNtf', '#wppoppop-ntf-list [data-r]', function() {
            var n = cur($(this)); if (!n) return;
            var $rows = $(this).closest('.wppoppop-ntf-card').find('.wppoppop-ntf-rule');
            var i = $rows.index($(this).closest('.wppoppop-ntf-rule'));
            if (i < 0 || !n.rules[i]) return;
            n.rules[i][$(this).attr('data-r')] = $(this).val();
        })
        .on('click.wppNtf', '.wppoppop-ntf-att-add', function(e) {
            e.preventDefault();
            var n = cur($(this)); if (!n) return;
            if (!window.wp || !window.wp.media) { window.alert(tr('WordPress Media is unavailable on this screen.')); return; }
            var frame = window.wp.media({ title: 'Select attachment', button: { text: 'Attach' }, multiple: false });
            frame.on('select', function() {
                var a = frame.state().get('selection').first();
                a = a ? a.toJSON() : null;
                if (a && a.id && n.attachments.length < 10) {
                    n.attachments.push({ id: a.id, name: a.filename || a.title || '', url: a.url || '' });
                    render();
                    $('#wppoppop-ntf-list .wppoppop-ntf-card[data-id="' + n.id + '"] .wppoppop-ntf-body').show();
                }
            });
            frame.open();
        })
        .on('click.wppNtf', '.wppoppop-ntf-att-del', function(e) {
            e.preventDefault();
            var n = cur($(this)); if (!n) return;
            n.attachments.splice(parseInt($(this).attr('data-i'), 10), 1);
            render();
            $('#wppoppop-ntf-list .wppoppop-ntf-card[data-id="' + n.id + '"] .wppoppop-ntf-body').show();
        })
        .on('click.wppNtf', '.wppoppop-acc-item[data-html-module="acc-notifications"] > .wppoppop-acc-header', function() { setTimeout(render, 0); });

    $(function() { setTimeout(render, 300); });
})(jQuery);


/* WPPOPPOP_NTF_TAGMENU: icon button + merge-tag menu for Notifications */
(function($) {
    'use strict';
    if (window._wppNtfTagMenu) return;
    window._wppNtfTagMenu = true;

    var $menu = null, $btn = null;

    function esc(v) { return $('<div></div>').text(v === undefined || v === null ? '' : String(v)).html(); }

    function tokens() {
        var out = [], seen = {};
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.canvases) {
            Object.keys(core.state.canvases).forEach(function(c) {
                (core.state.canvases[c] || []).forEach(function(el) {
                    var t = el && (el.field_name || el.fieldName || el.fieldId);
                    if (!t || seen[t]) return;
                    seen[t] = true;
                    out.push([String(t) + ' | ' + (el.type || 'field'), '{{' + t + '}}']);
                });
            });
        }
        return out;
    }

    function close() {
        if ($btn) { $btn.attr('aria-expanded', 'false'); }
        if ($menu) { $menu.remove(); }
        $menu = null; $btn = null;
    }

    function group(title, items) {
        var h = '<div style="padding:6px 10px 2px;font-size:11px;font-weight:700;color:#475569;">' + esc(title) + '</div>';
        $.each(items, function(i, it) {
            h += '<button type="button" class="wppoppop-ntf-tag-item" data-tag="' + esc(it[1]) + '" style="display:block;width:100%;text-align:left;border:0;background:transparent;padding:5px 10px;font-size:12px;cursor:pointer;color:#1e293b;">' + esc(it[0]) + '</button>';
        });
        return h;
    }

    function open($b) {
        close();
        $btn = $b;
        $b.attr('aria-expanded', 'true');
        var forms = tokens().concat([['email', '{{email}}'], ['All form data', '{{form-data}}']]);
        var gen = [['Popup name', '{{form-name}}'], ['Submission ID', '{{submission-id}}'], ['Visitor country', '{{country}}'], ['Site name', '{{site-name}}'], ['Global from email', '{{global-from-email}}'], ['Global from name', '{{global-from-name}}']];
        $menu = $('<div class="wppoppop-ntf-tag-menu" role="menu"></div>').css({
            position: 'absolute', top: '100%', right: 0, left: 'auto', zIndex: 50, minWidth: '190px', maxHeight: '220px',
            overflowY: 'auto', background: '#fff', border: '1px solid #cbd5e1', borderRadius: '0px',
            boxShadow: '0 4px 14px rgba(0,0,0,.15)', marginTop: '2px'
        }).html(group('Form values', forms) + group('General', gen));
        $b.closest('.wppoppop-ntf-lblrow').append($menu);
    }

    $(document)
        .off('.wppNtfMenu')
        .on('click.wppNtfMenu', '.wppoppop-ntf-tag', function(e) {
            e.preventDefault(); e.stopPropagation();
            if ($btn && $btn[0] === this && $menu) { close(); } else { open($(this)); }
        })
        .on('mouseenter.wppNtfMenu', '.wppoppop-ntf-tag-item', function() { $(this).css('background', '#f1f5f9'); })
        .on('mouseleave.wppNtfMenu', '.wppoppop-ntf-tag-item', function() { $(this).css('background', 'transparent'); })
        .on('click.wppNtfMenu', '.wppoppop-ntf-tag-item', function(e) {
            e.preventDefault(); e.stopPropagation();
            var tag = $(this).attr('data-tag');
            var $t = $btn ? $btn.closest('.wppoppop-ntf-card').find('[data-k="' + $btn.attr('data-for') + '"]') : $();
            var el = $t.get(0);
            if (el && tag) {
                var v = $t.val() || '';
                var a = (typeof el.selectionStart === 'number') ? el.selectionStart : v.length;
                var b = (typeof el.selectionEnd === 'number') ? el.selectionEnd : a;
                $t.val(v.slice(0, a) + tag + v.slice(b));
                try { el.focus(); el.setSelectionRange(a + tag.length, a + tag.length); } catch (err) {}
                $t.trigger('input');
            }
            close();
        })
        .on('click.wppNtfMenu', function(e) {
            if ($menu && !$(e.target).closest('.wppoppop-ntf-tag-menu, .wppoppop-ntf-tag').length) { close(); }
        })
        .on('keydown.wppNtfMenu', function(e) {
            if ($menu && (e.key === 'Escape' || e.keyCode === 27)) { e.stopPropagation(); close(); }
        });
})(jQuery);
