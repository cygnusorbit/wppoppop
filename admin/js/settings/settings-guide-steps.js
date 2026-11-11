/**
 * WpPopPop Settings > Builder > Guide Steps editor. Vanilla ES5, no dependencies.
 * Rows are plain form fields (settings[builder_guide_steps][n][...]) saved by the regular Save settings button;
 * the DOM order is the tour order, and names are re-indexed after every change.
 */
(function (window, document) {
    'use strict';

    var root = document.getElementById('wppoppop-gs-root');
    if (!root) { return; }

    var list = root.querySelector('.wppoppop-gs-list');
    var tpl = document.getElementById('wppoppop-gs-template');
    var addBtn = document.getElementById('wppoppop-gs-add');
    var resetBtn = document.getElementById('wppoppop-gs-reset');
    var countEl = document.getElementById('wppoppop-gs-count');
    var i18n = window.wppoppop_guide_steps_i18n || {};
    var max = parseInt(root.getAttribute('data-max'), 10) || 40;
    var defaults = [];

    try { defaults = JSON.parse(root.getAttribute('data-defaults') || '[]'); } catch (e) { defaults = []; }

    function rows() { return Array.prototype.slice.call(list.querySelectorAll('.wppoppop-gs-row')); }

    function field(row, key) { return row.querySelector('[name$="[' + key + ']"]'); }

    function matches(el, sel) {
        var f = el.matches || el.msMatchesSelector || el.webkitMatchesSelector;
        return f ? f.call(el, sel) : false;
    }

    function closest(el, sel) {
        while (el && el !== document) {
            if (el.nodeType === 1 && matches(el, sel)) { return el; }
            el = el.parentNode;
        }
        return null;
    }

    function syncRow(row) {
        var t = field(row, 'target');
        var custom = !!t && t.value === 'custom';
        var extra = row.querySelectorAll('.wppoppop-gs-if-custom');
        var cb = row.querySelector('.wppoppop-gs-enabled');
        var i;
        for (i = 0; i < extra.length; i++) {
            if (custom) { extra[i].removeAttribute('hidden'); } else { extra[i].setAttribute('hidden', 'hidden'); }
        }
        if (cb) { row.className = row.className.replace(/\s*\bis-off\b/, '') + (cb.checked ? '' : ' is-off'); }
    }

    function reindex() {
        var all = rows();
        var re = /(\[builder_guide_steps\]\[)[^\]]*(\])/;
        all.forEach(function (row, i) {
            var inputs = row.querySelectorAll('[name]');
            var n, up, down, num;
            for (n = 0; n < inputs.length; n++) {
                inputs[n].setAttribute('name', inputs[n].getAttribute('name').replace(re, '$1' + i + '$2'));
            }
            num = row.querySelector('.wppoppop-gs-num');
            if (num) { num.textContent = String(i + 1); }
            up = row.querySelector('[data-gs-act="up"]');
            down = row.querySelector('[data-gs-act="down"]');
            if (up) { up.disabled = i === 0; }
            if (down) { down.disabled = i === all.length - 1; }
            syncRow(row);
        });
        if (countEl) {
            countEl.textContent = (i18n.count || '{n} of {max} steps').replace('{n}', all.length).replace('{max}', max);
        }
        if (addBtn) { addBtn.disabled = all.length >= max; }
    }

    function setVal(row, key, value) {
        var el = field(row, key);
        if (el) { el.value = value === undefined || value === null ? '' : value; }
    }

    function makeRow(data) {
        var src = tpl && tpl.content ? tpl.content.querySelector('.wppoppop-gs-row') : null;
        var row, cb, del, tag;
        if (!src) { return null; }
        row = src.cloneNode(true);
        data = data || {};
        setVal(row, 'id', data.id || '');
        setVal(row, 'title', data.title);
        setVal(row, 'text', data.text);
        setVal(row, 'target', data.target || 'center');
        setVal(row, 'selector', data.selector);
        setVal(row, 'before', data.before);
        setVal(row, 'placement', data.placement || 'auto');
        cb = row.querySelector('.wppoppop-gs-enabled');
        if (cb) { cb.checked = data.enabled === undefined ? true : !!parseInt(data.enabled, 10); }
        if (data.custom !== undefined && !parseInt(data.custom, 10)) {
            row.setAttribute('data-custom', '0');
            del = row.querySelector('[data-gs-act="del"]');
            if (del && del.parentNode) { del.parentNode.removeChild(del); }
            tag = row.querySelector('.wppoppop-gs-tag');
            if (tag) { tag.textContent = i18n.builtin || 'Built-in'; }
        }
        return row;
    }

    function toggleBody(row, open) {
        var body = row.querySelector('.wppoppop-gs-body');
        var btn = row.querySelector('[data-gs-act="edit"]');
        if (!body) { return; }
        if (open) { body.removeAttribute('hidden'); } else { body.setAttribute('hidden', 'hidden'); }
        if (btn) { btn.setAttribute('aria-expanded', open ? 'true' : 'false'); }
    }

    root.addEventListener('click', function (e) {
        var btn = closest(e.target, '[data-gs-act]');
        var row, act, other, title;
        if (!btn || !root.contains(btn) || btn.disabled) { return; }
        row = closest(btn, '.wppoppop-gs-row');
        act = btn.getAttribute('data-gs-act');
        if (!row) { return; }
        e.preventDefault();
        if (act === 'edit') {
            toggleBody(row, row.querySelector('.wppoppop-gs-body').hasAttribute('hidden'));
        } else if (act === 'up' || act === 'down') {
            if (act === 'up' && row.previousElementSibling) { list.insertBefore(row, row.previousElementSibling); }
            if (act === 'down' && row.nextElementSibling) { list.insertBefore(row.nextElementSibling, row); }
            reindex();
            other = row.querySelector('[data-gs-act="' + act + '"]');
            if (!other || other.disabled) { other = row.querySelector('[data-gs-act="' + (act === 'up' ? 'down' : 'up') + '"]'); }
            if (other && !other.disabled) { other.focus(); }
        } else if (act === 'del') {
            if (window.confirm(i18n.confirm_delete || 'Delete this step?')) {
                list.removeChild(row);
                reindex();
            }
        }
    });

    if (addBtn) {
        addBtn.addEventListener('click', function (e) {
            var row, title;
            e.preventDefault();
            if (rows().length >= max) { return; }
            row = makeRow({ custom: 1, enabled: 1, target: 'center', placement: 'auto' });
            if (!row) { return; }
            list.appendChild(row);
            reindex();
            toggleBody(row, true);
            title = row.querySelector('.wppoppop-gs-title');
            if (title) { title.focus(); }
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function (e) {
            var i, row;
            e.preventDefault();
            if (!window.confirm(i18n.confirm_reset || 'Reset all guide steps to the built-in defaults? Save settings to apply.')) { return; }
            while (list.firstChild) { list.removeChild(list.firstChild); }
            for (i = 0; i < defaults.length; i++) {
                row = makeRow(defaults[i]);
                if (row) { list.appendChild(row); }
            }
            reindex();
        });
    }

    root.addEventListener('change', function (e) {
        var row = closest(e.target, '.wppoppop-gs-row');
        if (row) { syncRow(row); }
    });

    /* Enter inside a field must not submit (save) the whole settings form by accident. */
    root.addEventListener('keydown', function (e) {
        var t = e.target;
        if ((e.key === 'Enter' || e.keyCode === 13) && t && t.tagName === 'INPUT' && t.type === 'text') { e.preventDefault(); }
    });

    reindex();
})(window, document);
