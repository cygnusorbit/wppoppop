/* WPPOPPOP_RIBBON_TOOLTIP: small bubble below a ribbon element on hover/focus. Text = the (type) in the title, Title Case */
(function (window, document) {
    'use strict';
    if (window.__wppRibbonTip) { return; }
    window.__wppRibbonTip = true;

    var SEL = '.wppoppop-ribbon-tool, #wppoppop-alignment-toolbar .wppoppop-align-btn';
    var tip = null, current = null;

    function titleCase(s) {
        return String(s).replace(/[_-]+/g, ' ').replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '')
            .toLowerCase().replace(/(^|\s)([a-z0-9])/g, function (m, a, b) { return a + b.toUpperCase(); });
    }

    function labelFor(btn) {
        var raw = btn.getAttribute('data-wpp-title') || btn.getAttribute('title') || '';
        var m = raw.match(/\(([^()]+)\)\s*$/);
        if (m) { return titleCase(m[1]); }
        return titleCase(btn.getAttribute('data-type') || raw);
    }

    function prime(btn) {
        var t = btn.getAttribute('title');
        if (t) {
            btn.setAttribute('data-wpp-title', t);
            btn.setAttribute('aria-label', labelFor(btn));
            btn.removeAttribute('title');
        }
    }

    function ensureTip() {
        if (tip) { return tip; }
        tip = document.createElement('div');
        tip.className = 'wppoppop-ribbon-tip';
        tip.setAttribute('role', 'tooltip');
        document.body.appendChild(tip);
        return tip;
    }

    function show(btn) {
        prime(btn);
        current = btn;
        var t = ensureTip();
        t.textContent = labelFor(btn);
        t.className = 'wppoppop-ribbon-tip is-on';
        var r = btn.getBoundingClientRect();
        var w = t.offsetWidth, vw = document.documentElement.clientWidth;
        var left = r.left + r.width / 2 - w / 2;
        left = Math.max(6, Math.min(left, vw - w - 6));
        t.style.left = Math.round(left) + 'px';
        t.style.top = Math.round(r.bottom + 8) + 'px';
        t.style.setProperty('--wpp-tip-x', Math.round(r.left + r.width / 2 - left) + 'px');
    }

    function hide() {
        current = null;
        if (tip) { tip.className = 'wppoppop-ribbon-tip'; }
    }

    function target(e) {
        var n = e.target;
        return (n && n.closest) ? n.closest(SEL) : null;
    }

    document.addEventListener('mouseover', function (e) {
        var b = target(e);
        if (b && b !== current) { show(b); }
    }, true);
    document.addEventListener('mouseout', function (e) {
        var b = target(e);
        if (b && !(e.relatedTarget && b.contains(e.relatedTarget))) { hide(); }
    }, true);
    document.addEventListener('focusin', function (e) { var b = target(e); if (b) { show(b); } }, true);
    document.addEventListener('focusout', function (e) { if (target(e)) { hide(); } }, true);
    document.addEventListener('mousedown', hide, true);
    document.addEventListener('dragstart', hide, true);
    window.addEventListener('scroll', hide, true);
    window.addEventListener('resize', hide);

    function init() {
        var list = document.querySelectorAll(SEL);
        for (var i = 0; i < list.length; i++) { prime(list[i]); }
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
})(window, document);
