/**
 * WpPopPop Frontend: Unified Canvas Group Scaling (live preview + real render)
 * The whole popup (canvas + every layer) scales as ONE group whenever the screen is
 * smaller than the canvas, keeping a minimum 20px gap left/right (and top/bottom).
 * Layout is never reflowed: only a uniform transform is applied to the fit stage.
 * Cross-browser: Chrome, Safari, Firefox, Edge (no arrow functions / optional chaining).
 */
(function (window, document) {
    'use strict';
    if (window.WpPopPopFrontScale) { return; }

    var PAD = 20;
    var MIN_SCALE = 0.05;
    var queued = false;

    function num(v) {
        v = parseFloat(v);
        return isNaN(v) ? 0 : v;
    }

    function activeCanvas(wrap) {
        return wrap.querySelector('.wppoppop-canvas-container.active, .wppoppop-canvas-container.wppoppop-canvas-active') ||
            wrap.querySelector('.wppoppop-canvas-container');
    }

    function canvasSize(canvas) {
        var w = num(canvas.getAttribute('data-width')) || canvas.offsetWidth;
        var h = num(canvas.getAttribute('data-height')) || canvas.offsetHeight;
        return { w: w, h: h };
    }

    function setTransform(el, value) {
        el.style.webkitTransform = value;
        el.style.transform = value;
    }

    function setOrigin(el, value) {
        el.style.webkitTransformOrigin = value;
        el.style.transformOrigin = value;
    }

    /* WPPOPPOP_POPUP_POSITION_FIT: position-aware gap + anchor for the Popup Positions module */
    var POS_MAP = {
        'top-left': ['top', 'left'], 'top-center': ['top', 'center'], 'top-right': ['top', 'right'],
        'middle-left': ['middle', 'left'], 'center': ['middle', 'center'], 'middle-right': ['middle', 'right'],
        'bottom-left': ['bottom', 'left'], 'bottom-center': ['bottom', 'center'], 'bottom-right': ['bottom', 'right']
    };

    function posParts(wrap) {
        return POS_MAP[wrap.getAttribute('data-wpp-pos') || 'center'] || POS_MAP.center;
    }

    function axisGap(a, b, anchor, startName, endName) {
        if (anchor === startName) { return a + Math.max(b, PAD); }
        if (anchor === endName) { return b + Math.max(a, PAD); }
        return Math.max(a + b, PAD * 2);
    }

    // Space the overlay reserves around the popup (its own padding), never less than the legacy 20px gap.
    function overlayGap(wrap) {
        var cs = null, pT = 0, pR = 0, pB = 0, pL = 0;
        try { cs = window.getComputedStyle(wrap); } catch (e) { cs = null; }
        if (cs) { pT = num(cs.paddingTop); pR = num(cs.paddingRight); pB = num(cs.paddingBottom); pL = num(cs.paddingLeft); }
        var parts = posParts(wrap);
        return {
            w: axisGap(pL, pR, parts[1], 'left', 'right'),
            h: axisGap(pT, pB, parts[0], 'top', 'bottom')
        };
    }

    function anchorOrigin(wrap) {
        var parts = posParts(wrap);
        var x = parts[1] === 'left' ? '0%' : (parts[1] === 'right' ? '100%' : '50%');
        var y = parts[0] === 'top' ? '0%' : (parts[0] === 'bottom' ? '100%' : '50%');
        return x + ' ' + y;
    }

    function resetInline(wrap, stage) {
        setTransform(stage, '');
        setOrigin(stage, '');
        stage.style.position = '';
        stage.style.left = '';
        stage.style.top = '';
        stage.style.width = '';
        stage.style.height = '';
        wrap.style.removeProperty('width');
        wrap.style.removeProperty('height');
    }

    /* WPPOPPOP_INLINE_COLUMN_FIT: real width the theme gives an inline embed (post/page column) */
    function contentWidth(el) {
        var w = el.clientWidth, cs = null;
        try { cs = window.getComputedStyle(el); } catch (e) { cs = null; }
        if (cs) { w -= num(cs.paddingLeft) + num(cs.paddingRight); }
        return w;
    }

    function inlineAvailWidth(wrap, stage) {
        // Measure with the stage out of flow and our own footprint cleared, so the theme's column width wins.
        var prevPos = stage.style.position;
        var prevW = wrap.style.getPropertyValue('width'), prevWP = wrap.style.getPropertyPriority('width');
        var prevH = wrap.style.getPropertyValue('height'), prevHP = wrap.style.getPropertyPriority('height');
        wrap.style.removeProperty('width');
        wrap.style.removeProperty('height');
        stage.style.position = 'absolute';
        var own = wrap.getBoundingClientRect ? wrap.getBoundingClientRect().width : wrap.clientWidth;
        stage.style.position = prevPos;
        if (prevW) { wrap.style.setProperty('width', prevW, prevWP); }
        if (prevH) { wrap.style.setProperty('height', prevH, prevHP); }

        var parent = wrap.parentNode;
        while (parent && parent.nodeType === 1 && !parent.clientWidth && parent !== document.body) { parent = parent.parentNode; }
        var par = (parent && parent.nodeType === 1) ? contentWidth(parent) : 0;

        var avail = own > 1 ? own : par;
        if (par > 1 && par < avail) { avail = par; }
        return avail > 1 ? Math.floor(avail) : (document.documentElement.clientWidth || window.innerWidth);
    }

    function fit(wrap) {
        var stage = wrap.querySelector('.wppoppop-fit-stage');
        if (!stage) { return; }
        var canvas = activeCanvas(wrap);
        if (!canvas) { return; }
        var size = canvasSize(canvas);
        if (!size.w || !size.h) { return; }

        var inline = wrap.getAttribute('data-inline') === '1';
        var docEl = document.documentElement;
        var availW, availH, pad, parent, gapW, gapH;

        if (inline) {
            availW = inlineAvailWidth(wrap, stage);
            availH = Infinity;
            pad = 0;
            gapW = 0;
            gapH = 0;
        } else {
            availW = wrap.clientWidth || docEl.clientWidth || window.innerWidth;
            availH = wrap.clientHeight || window.innerHeight || docEl.clientHeight;
            pad = PAD;
            var gap = overlayGap(wrap);
            gapW = gap.w;
            gapH = gap.h;
        }

        var scale = Math.min(1, (availW - gapW) / size.w, (availH - gapH) / size.h);
        if (!isFinite(scale) || scale > 1) { scale = 1; }
        if (scale < MIN_SCALE) { scale = MIN_SCALE; }
        scale = Math.round(scale * 10000) / 10000;

        var key = scale + '|' + size.w + 'x' + size.h + '|' + (inline ? 'i' : 'o');
        if (wrap.getAttribute('data-wpp-fit-key') === key) { return; }
        wrap.setAttribute('data-wpp-fit-key', key);

        if (scale >= 1) {
            if (inline) { resetInline(wrap, stage); } else { setTransform(stage, ''); }
            wrap.removeAttribute('data-wpp-scale');
            return;
        }

        wrap.setAttribute('data-wpp-scale', String(scale));
        if (inline) {
            // Reserve only the scaled footprint in the page flow; the stage scales from its top-left corner.
            wrap.style.setProperty('width', (Math.floor(size.w * scale * 100) / 100) + 'px', 'important');
            wrap.style.setProperty('height', (Math.floor(size.h * scale * 100) / 100) + 'px', 'important');
            stage.style.position = 'absolute';
            stage.style.left = '0';
            stage.style.top = '0';
            stage.style.width = size.w + 'px';
            stage.style.height = size.h + 'px';
            setOrigin(stage, '0 0');
        }
        // Popup overlay: scale from the anchored corner / edge so a positioned popup stays where it was placed.
        if (!inline) { setOrigin(stage, anchorOrigin(wrap)); }
        setTransform(stage, 'scale(' + scale + ')');
    }

    function fitAll() {
        var list = document.querySelectorAll('.wppoppop-popup-wrap, .wppoppop-overlay[data-uid]');
        for (var i = 0; i < list.length; i++) { fit(list[i]); }
    }

    function refresh() {
        if (queued) { return; }
        queued = true;
        var raf = window.requestAnimationFrame || function (cb) { return window.setTimeout(cb, 16); };
        raf(function () {
            queued = false;
            fitAll();
        });
    }

    function observe() {
        if (!window.MutationObserver) { return; }
        var stages = document.querySelectorAll('.wppoppop-fit-stage');
        var mo = new window.MutationObserver(refresh);
        for (var i = 0; i < stages.length; i++) {
            // Canvas switching toggles the "active" class; re-fit to the newly visible canvas.
            mo.observe(stages[i], { attributes: true, attributeFilter: ['class'], subtree: true });
        }
    }

    function observeColumns() {
        if (!window.ResizeObserver) { return; }
        var wraps = document.querySelectorAll('.wppoppop-popup-wrap[data-inline="1"]');
        if (!wraps.length) { return; }
        var ro = new window.ResizeObserver(refresh);
        for (var i = 0; i < wraps.length; i++) {
            if (wraps[i].parentNode && wraps[i].parentNode.nodeType === 1) { ro.observe(wraps[i].parentNode); }
        }
    }

    function hookDisplay() {
        var D = window.WpPopPopFrontDisplay;
        if (!D || D.__wppFitHooked || typeof D.show !== 'function') { return; }
        D.__wppFitHooked = true;
        var origShow = D.show;
        D.show = function () {
            fitAll();
            var r = origShow.apply(this, arguments);
            refresh();
            return r;
        };
    }

    window.WpPopPopFrontScale = { refresh: refresh, fitAll: fitAll, fit: fit };

    hookDisplay();
    window.addEventListener('resize', refresh, false);
    window.addEventListener('orientationchange', refresh, false);
    window.addEventListener('load', function () { fitAll(); observe(); observeColumns(); }, false);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { fitAll(); observe(); observeColumns(); }, false);
    } else {
        fitAll();
        observe();
        observeColumns();
    }
})(window, document);
