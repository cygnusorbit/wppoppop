/* WPPOPPOP_NUDGE: move the selected element 1px (Shift = 10px) from the Alignment Tool arrows or the keyboard arrow keys */
(function (window, document, $) {
    'use strict';
    if (window.__wppNudgeBound) { return; }
    window.__wppNudgeBound = true;

    var timer = null;

    function core() { return window.WpPopPopBuilderCore; }
    function cid(r) { return String(r == null ? '' : r).replace(/^#/, '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').replace(/^\s+|\s+$/g, ''); }
    function num(v) { var n = parseFloat(v); return isNaN(n) ? 0 : n; }

    function activeElement() {
        var c = core();
        if (!c || !c.state || !c.state.activeId) { return null; }
        var list = (c.state.canvases && c.state.canvases[c.state.currentCanvas || 1]) || [];
        var k = cid(c.state.activeId);
        for (var i = 0; i < list.length; i++) {
            if (list[i] && cid(list[i].id) === k) { return list[i]; }
        }
        return null;
    }

    function nudge(dx, dy) {
        var el = activeElement();
        if (!el || el.locked) { return false; }
        var left = Math.round(num(el.left !== undefined ? el.left : el.x)) + dx;
        var top = Math.round(num(el.top !== undefined ? el.top : el.y)) + dy;
        el.left = left;
        el.top = top;
        if (el.x !== undefined) { el.x = left; }
        if (el.y !== undefined) { el.y = top; }

        var id = cid(el.id);
        var node = document.getElementById('el-' + id) || document.getElementById('wppoppop-el-' + id);
        if (node) { node.style.left = left + 'px'; node.style.top = top + 'px'; }

        $('#prop-left, #prop-pos-left, #prop-x').val(left);
        $('#prop-top, #prop-pos-top, #prop-y').val(top);
        var I = window.WpPopPopBuilderInspector;
        if (I && typeof I.syncCoordinates === 'function') { try { I.syncCoordinates(el); } catch (e) {} }

        // One undo step per burst of nudges
        clearTimeout(timer);
        timer = setTimeout(function () {
            var c = core();
            if (c && typeof c.pushHistory === 'function') { c.pushHistory('Nudge'); }
        }, 300);
        return true;
    }

    function isTyping(t) {
        if (!t) { return false; }
        var tag = (t.tagName || '').toLowerCase();
        if (tag === 'input' || tag === 'textarea' || tag === 'select' || t.isContentEditable) { return true; }
        return !!(t.closest && t.closest('.note-editor, [contenteditable="true"], [role="slider"]'));
    }

    var KEYS = {
        ArrowLeft: [-1, 0], ArrowRight: [1, 0], ArrowUp: [0, -1], ArrowDown: [0, 1],
        Left: [-1, 0], Right: [1, 0], Up: [0, -1], Down: [0, 1]
    };
    var CODES = { 37: [-1, 0], 39: [1, 0], 38: [0, -1], 40: [0, 1] };

    // Capture phase + stopImmediatePropagation: replaces the older legacy nudge handlers
    document.addEventListener('keydown', function (e) {
        if (!document.getElementById('wppoppop-canvas-box')) { return; }
        if (window.WpPopPopBuilderPreview && window.WpPopPopBuilderPreview.isOpen) { return; }
        if (e.metaKey || e.ctrlKey || e.altKey) { return; }
        var d = KEYS[e.key] || CODES[e.keyCode];
        if (!d || isTyping(e.target)) { return; }
        if (!activeElement()) { return; }
        var step = e.shiftKey ? 10 : 1;
        e.preventDefault();
        e.stopImmediatePropagation();
        nudge(d[0] * step, d[1] * step);
    }, true);

    // Toolbar arrows
    // Bound on the toolbar itself: the toolbar stops click bubbling to document.
    $(function () { $('#wppoppop-alignment-toolbar').off('click.wppNudge').on('click.wppNudge', '.wppoppop-nudge-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var d = String($(this).attr('data-nudge') || '');
        var step = e.shiftKey ? 10 : 1;
        var map = { left: [-1, 0], right: [1, 0], up: [0, -1], down: [0, 1] };
        if (map[d]) { nudge(map[d][0] * step, map[d][1] * step); }
    }); });

    window.WpPopPopNudge = { move: nudge };
})(window, document, jQuery);
