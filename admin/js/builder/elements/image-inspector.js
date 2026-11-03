/**
 * WpPopPop Builder: Image inspector module (Image Source & Fitting)
 * Lifecycle: Inspector DOM -> JS model (live) -> pre-save flush (gp-bind) -> AJAX whitelist -> front renderer.
 * Handles: click-link target (_self/_blank), default Contain fit, and auto-fitting the frame height to the image
 * proportion (based on the element width) whenever an image source is applied.
 * Cross-browser: Chrome, Safari, Firefox, Edge (ES5 only).
 */
(function (window, $) {
    'use strict';
    if (!$ || window.WpPopPopImageInspector) { return; }

    var MIN = 16;
    var token = 0;

    function cid(r) { return String(r || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim(); }

    function activeEl() {
        var c = window.WpPopPopBuilderCore;
        if (!c || !c.state || !c.state.activeId) { return null; }
        var id = cid(c.state.activeId);
        var list = (c.state.canvases && c.state.canvases[c.state.currentCanvas || 1]) || [];
        for (var i = 0; i < list.length; i++) {
            if (list[i] && cid(list[i].id) === id) { return list[i]; }
        }
        return null;
    }

    function isImage(el) { var t = el && String(el.type || '').toLowerCase(); return t === 'image' || t === 'img'; }
    function intOr(v, d) { v = parseInt(v, 10); return isNaN(v) ? d : v; }
    function truthy(v) { return v === true || v === 1 || v === '1' || v === 'true'; }

    function rerender() {
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
            window.WpPopPopBuilderCanvas.renderCanvas();
        }
    }

    function targetOf(el) {
        if (el.linkTarget === '_blank' || el.linkTarget === '_self') { return el.linkTarget; }
        return (truthy(el.targetBlank) || truthy(el.linkBlank)) ? '_blank' : '_self';
    }

    function setTarget(el, t) {
        el.linkTarget = t === '_blank' ? '_blank' : '_self';
        el.targetBlank = el.linkTarget === '_blank';
        el.linkBlank = el.targetBlank;
    }

    function sync() {
        var el = activeEl();
        if (!isImage(el)) { return; }
        $('#prop-image-fit').val(el.objectFit || el.imageFit || 'contain');
        $('#prop-image-link-target').val(targetOf(el));
        $('#prop-size-width, #prop-width').val(intOr(el.width, 120));
        $('#prop-size-height, #prop-height').val(intOr(el.height, 150));
    }

    var timer = null;
    function sched() {
        if (timer) { return; }
        timer = window.setTimeout(function () { timer = null; sync(); }, 60);
        window.setTimeout(sync, 250);
        window.setTimeout(sync, 600);
    }

    // Frame height follows the natural image proportion at the current element width.
    function fitFrame(el, src) {
        if (!src) { return; }
        var my = ++token;
        var probe = new window.Image();
        probe.onload = function () {
            if (my !== token) { return; }
            var cur = activeEl();
            if (!cur || cur !== el || $.trim(el.src || el.imageUrl || el.imgUrl || '') !== src) { return; }
            var nw = probe.naturalWidth || probe.width, nh = probe.naturalHeight || probe.height;
            if (!nw || !nh) { return; }
            var w = Math.max(MIN, intOr(el.width, 120));
            var h = Math.max(MIN, Math.round(w * nh / nw));
            el.width = w;
            el.height = h;
            el.h = h;
            el.sizePresetApplied = true;
            $('#prop-size-height, #prop-height').val(h);
            $('#prop-size-width, #prop-width').val(w);
            rerender();
            if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                window.WpPopPopBuilderCore.pushHistory('Fit Image Frame');
            }
        };
        probe.src = src;
    }

    $(document).off('.wppImageInsp');

    $(document).on('change.wppImageInsp', '#prop-image-link-target', function () {
        var el = activeEl(); if (!isImage(el)) { return; }
        setTarget(el, $(this).val());
    });

    // Applies to typed/pasted URLs and to the WP Media picker (which triggers change on the field).
    $(document).on('change.wppImageInsp', '#prop-image-url', function () {
        var el = activeEl(); if (!isImage(el)) { return; }
        var src = $.trim($(this).val());
        window.setTimeout(function () { fitFrame(el, src); }, 0);
    });

    window.addEventListener('click', function (e) {
        var t = e.target;
        if (t && t.closest && t.closest('.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-insp-tab, #wppoppop-inspector-drawer')) { sched(); }
    }, true);

    window.WpPopPopImageInspector = { sync: sync, fitFrame: fitFrame };
    $(function () { sched(); });
})(window, window.jQuery);
