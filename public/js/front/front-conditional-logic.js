/* WPPOPPOP_COND_LOGIC_V2: frontend conditional-logic runtime */
(function (window, document, $) {
    'use strict';
    if (window.__wppLogicV2) { return; }
    window.__wppLogicV2 = true;

    function norm(v) {
        return String(v == null ? '' : v)
            .replace(/^wppoppop-layer-item-/, '').replace(/^wppoppop-layer-/, '')
            .replace(/^layer-/, '').replace(/^wppoppop-el-/, '').replace(/^el-/, '')
            .replace(/^\s+|\s+$/g, '');
    }

    function findLayer(scope, id) {
        var list = scope.querySelectorAll('[data-layer-id]');
        for (var i = 0; i < list.length; i++) {
            if (list[i].getAttribute('data-layer-id') === id) { return list[i]; }
        }
        return null;
    }

    function readValues(wrap) {
        var vals = [];
        if (!wrap) { return vals; }
        var ctrls = wrap.querySelectorAll('input, select, textarea');
        for (var i = 0; i < ctrls.length; i++) {
            var c = ctrls[i], t = String(c.type || '').toLowerCase();
            if (t === 'button' || t === 'submit' || t === 'reset' || t === 'file') { continue; }
            if (t === 'checkbox' || t === 'radio') {
                if (c.checked) { vals.push(c.value === '' ? 'on' : c.value); }
                continue;
            }
            if (c.tagName === 'SELECT' && c.multiple) {
                for (var o = 0; o < c.options.length; o++) {
                    if (c.options[o].selected) { vals.push(c.options[o].value); }
                }
                continue;
            }
            if (c.value === '' || c.value == null) { continue; }
            if (t === 'hidden' && String(c.value).indexOf(',') !== -1) {
                var parts = String(c.value).split(/\s*,\s*/);
                for (var p = 0; p < parts.length; p++) { if (parts[p] !== '') { vals.push(parts[p]); } }
            } else {
                vals.push(String(c.value));
            }
        }
        return vals;
    }

    function check(op, vals, expected) {
        var joined = vals.join(', ').toLowerCase();
        var e = String(expected == null ? '' : expected).toLowerCase().replace(/^\s+|\s+$/g, '');
        var lower = [];
        for (var i = 0; i < vals.length; i++) { lower.push(String(vals[i]).toLowerCase().replace(/^\s+|\s+$/g, '')); }
        var isEqual = lower.indexOf(e) !== -1 || joined.replace(/^\s+|\s+$/g, '') === e;
        var blank = joined.replace(/^\s+|\s+$/g, '') === '';
        switch (op) {
            case 'equal': case 'equals': case 'is':
                return isEqual;
            case 'not_equal': case 'is_not':
                return !isEqual;
            case 'contains':
                return e === '' ? !blank : joined.indexOf(e) !== -1;
            case 'not_contains':
                return e === '' ? blank : joined.indexOf(e) === -1;
            case 'greater': case 'less':
                if (blank) { return false; }
                var n = parseFloat(joined), m = parseFloat(e);
                if (!isNaN(n) && !isNaN(m)) { return op === 'greater' ? n > m : n < m; }
                return op === 'greater' ? joined > e : joined < e;
            case 'empty':
                return blank;
            case 'not_empty':
                return !blank;
        }
        return true;
    }

    function setHidden(target, hide) {
        var was = target.getAttribute('data-logic-hidden') === '1';
        if (hide === was) { return; }
        var ctrls = target.querySelectorAll('input, select, textarea');
        var i;
        if (hide) {
            target.setAttribute('data-logic-hidden', '1');
            target.style.setProperty('display', 'none', 'important');
            for (i = 0; i < ctrls.length; i++) {
                if (!ctrls[i].disabled) { ctrls[i].disabled = true; ctrls[i].setAttribute('data-logic-disabled', '1'); }
            }
        } else {
            target.removeAttribute('data-logic-hidden');
            target.style.removeProperty('display');
            for (i = 0; i < ctrls.length; i++) {
                if (ctrls[i].getAttribute('data-logic-disabled') === '1') {
                    ctrls[i].disabled = false;
                    ctrls[i].removeAttribute('data-logic-disabled');
                }
            }
        }
    }

    function evaluate() {
        var targets = document.querySelectorAll('[data-logic-enabled="1"]');
        for (var i = 0; i < targets.length; i++) {
            var t = targets[i], rules = [];
            try { rules = JSON.parse(t.getAttribute('data-logic-rules') || '[]'); } catch (e) { rules = []; }
            if (!rules || !rules.length) { continue; }
            var scope = t.closest ? (t.closest('.wppoppop-canvas-container') || document) : document;
            var results = [];
            for (var r = 0; r < rules.length; r++) {
                var rule = rules[r] || {};
                var src = findLayer(scope, norm(rule.field));
                results.push(check(String(rule.operator || 'equal'), readValues(src), rule.value));
            }
            var passed = (t.getAttribute('data-logic-match') === 'any')
                ? results.some(function (b) { return b; })
                : results.every(function (b) { return b; });
            var show = (t.getAttribute('data-logic-action') === 'hide') ? !passed : passed;
            setHidden(t, !show);
        }
    }

    var timer = null;
    function schedule() {
        if (timer) { clearTimeout(timer); }
        timer = setTimeout(function () { timer = null; evaluate(); }, 0);
    }

    document.addEventListener('input', schedule, true);
    document.addEventListener('change', schedule, true);
    document.addEventListener('click', schedule, true);
    if ($) { $(document).on('input change', 'input, select, textarea', schedule); }

    function boot() {
        evaluate();
        setTimeout(evaluate, 400);
        setTimeout(evaluate, 1500);
        if (window.MutationObserver && document.body) {
            new MutationObserver(function (muts) {
                for (var i = 0; i < muts.length; i++) {
                    if (muts[i].addedNodes && muts[i].addedNodes.length) { schedule(); return; }
                }
            }).observe(document.body, { childList: true, subtree: true });
        }
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
    window.addEventListener('load', evaluate);

    window.WpPopPopFrontConditionalLogic = { evaluate: evaluate };
})(window, document, window.jQuery);
