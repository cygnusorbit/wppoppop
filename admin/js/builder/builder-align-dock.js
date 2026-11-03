/* WPPOPPOP_ALIGN_DOCK: Alignment Tool sits below the canvas, always 50px away (follows canvas resize, zoom, pan, workspace resize) */
(function (window, document, $) {
    'use strict';
    if (window.__wppAlignDockBound) { return; }
    window.__wppAlignDockBound = true;

    var GAP = 50;
    var raf = 0;

    function place() {
        raf = 0;
        var tb = document.getElementById('wppoppop-alignment-toolbar');
        var box = document.getElementById('wppoppop-canvas-box');
        var ws = tb && tb.parentNode;
        if (!tb || !box || !ws) { return; }

        var b = box.getBoundingClientRect();
        var w = ws.getBoundingClientRect();
        var top = Math.round((b.bottom - w.top + ws.scrollTop + GAP) * 100) / 100;
        var left = Math.round((b.left + b.width / 2 - w.left + ws.scrollLeft) * 100) / 100;

        if (tb.style.top !== top + 'px') { tb.style.top = top + 'px'; }
        if (tb.style.left !== left + 'px') { tb.style.left = left + 'px'; }
        if (tb.style.bottom !== 'auto') { tb.style.bottom = 'auto'; }
    }

    function schedule() {
        if (raf) { return; }
        raf = (window.requestAnimationFrame || function (f) { return setTimeout(f, 16); })(place);
    }

    function hookCore() {
        var c = window.WpPopPopBuilderCore;
        if (!c || c.__wppAlignDockHooked || typeof c.updateCanvasTransform !== 'function') { return; }
        c.__wppAlignDockHooked = true;
        var orig = c.updateCanvasTransform;
        c.updateCanvasTransform = function () {
            var r = orig.apply(this, arguments);
            schedule();
            return r;
        };
    }

    function init() {
        var tb = document.getElementById('wppoppop-alignment-toolbar');
        var box = document.getElementById('wppoppop-canvas-box');
        if (!tb || !box) { return; }
        hookCore();

        if (window.ResizeObserver) {
            var ro = new ResizeObserver(schedule);
            ro.observe(box);
            if (tb.parentNode) { ro.observe(tb.parentNode); }
        }
        // Canvas inline size / transform changes (resize handle, zoom, pan)
        if (window.MutationObserver) {
            new MutationObserver(schedule).observe(box, { attributes: true, attributeFilter: ['style', 'class'] });
            new MutationObserver(schedule).observe(tb, { attributes: true, attributeFilter: ['style'] });
        }
        window.addEventListener('resize', schedule);
        if (tb.parentNode) { tb.parentNode.addEventListener('scroll', schedule, { passive: true }); }
        $(document).on('mousemove.wppAlignDock mouseup.wppAlignDock', function () {
            if (document.body.classList.contains('wppoppop-is-panning') || $('#wppoppop-canvas-box').hasClass('is-resizing')) { schedule(); }
        });
        place();
        setTimeout(place, 300);
    }

    hookCore();
    $(init);
    $(window).on('load', function () { hookCore(); schedule(); });
    window.WpPopPopAlignDock = { place: place };
})(window, document, jQuery);
