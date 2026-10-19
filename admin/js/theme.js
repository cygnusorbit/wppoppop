(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); } else { document.addEventListener('DOMContentLoaded', fn); }
    }
    function $all(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }
    function isHex6(v) { return /^#[0-9a-f]{6}$/i.test(v); }

    ready(function () {
        var form = document.getElementById('wpp-theme-form');
        if (!form) { return; }
        var main = form.querySelector('.wpp-theme-main');

        /* ---- Model: DOM inputs -> live CSS variables on <body> ---- */
        function applyVar(input) {
            var v = input.getAttribute('data-wpp-var');
            if (v) { document.body.style.setProperty(v, input.value); }
        }
        $all('[data-wpp-var]', form).forEach(function (input) {
            input.addEventListener('input', function () {
                applyVar(input);
                var picker = input.parentNode.querySelector('.wpp-theme-picker');
                if (picker && isHex6(input.value)) { picker.value = input.value; }
            });
            input.addEventListener('change', function () { applyVar(input); });
        });

        /* ---- Color pickers -> text field ---- */
        $all('.wpp-theme-picker', form).forEach(function (picker) {
            picker.addEventListener('input', function () {
                var text = picker.parentNode.querySelector('input[type="text"]');
                text.value = picker.value;
                applyVar(text);
            });
        });

        /* ---- Per-field reset ---- */
        $all('.wpp-theme-field-reset', form).forEach(function (btn) {
            btn.addEventListener('click', function () {
                var text = btn.parentNode.querySelector('input[type="text"]');
                text.value = text.getAttribute('data-default') || '';
                text.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });

        /* ---- Tabs ---- */
        var tabs = $all('.wpp-theme-tab', form), panels = $all('.wpp-theme-panel', form);
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var id = tab.getAttribute('data-tab');
                tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
                panels.forEach(function (p) { p.classList.toggle('is-active', p.getAttribute('data-panel') === id); });
            });
        });

        /* ---- Search ---- */
        var search = document.getElementById('wpp-theme-search');
        if (search) {
            search.addEventListener('input', function () {
                var q = search.value.toLowerCase().trim();
                main.classList.toggle('is-searching', q !== '');
                $all('.wpp-theme-field', form).forEach(function (f) {
                    var hit = q === '' || (f.getAttribute('data-label') || '').indexOf(q) !== -1;
                    f.classList.toggle('is-hidden', !hit);
                });
                panels.forEach(function (p) {
                    var any = p.querySelectorAll('.wpp-theme-field:not(.is-hidden)').length;
                    if (q !== '') { p.style.display = any ? 'block' : 'none'; } else { p.style.display = ''; }
                });
            });
        }

        /* ---- Revert unsaved ---- */
        var revert = document.getElementById('wpp-theme-reset-form');
        if (revert) { revert.addEventListener('click', function () { window.location.reload(); }); }

        /* ---- Export ---- */
        var exportBtn = document.getElementById('wpp-theme-export'), json = document.getElementById('wpp-theme-json');
        if (exportBtn && json) {
            exportBtn.addEventListener('click', function () {
                var out = {};
                $all('[name^="wpp_theme["]', form).forEach(function (el) {
                    var key = el.name.replace(/^wpp_theme\[|\]$/g, '');
                    out[key] = el.type === 'checkbox' ? (el.checked ? 1 : 0) : el.value;
                });
                json.value = JSON.stringify(out, null, 2);
            });
        }

        /* ---- Import / Reset buttons set the action; Pre-save flush ---- */
        $all('[data-action]', form).forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (btn.getAttribute('data-action') === 'reset' && !window.confirm('Reset the whole theme to defaults?')) {
                    e.preventDefault();
                    return;
                }
                document.getElementById('wpp-theme-action').value = btn.getAttribute('data-action');
            });
        });
        form.addEventListener('submit', function () {
            var action = document.getElementById('wpp-theme-action').value;
            if (action === 'save') {
                $all('.wpp-theme-picker', form).forEach(function (picker) {
                    var text = picker.parentNode.querySelector('input[type="text"]');
                    if (text && text.value === '' ) { text.value = picker.value; }
                });
            }
        });
    });
})();
