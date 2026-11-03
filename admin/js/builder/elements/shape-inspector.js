/**
 * WpPopPop Builder: Shape / Vector Geometry inspector module
 * Lifecycle: Inspector DOM -> JS model (live) -> pre-save flush (gp-bind + #prop-size-*) -> AJAX whitelist -> front renderer.
 * Handles: Width/Height, Fill + Stroke color with transparent toggles, Rotation slider and Reset (stroke width + rotation = 0).
 * Cross-browser: Chrome, Safari, Firefox, Edge (ES5 only).
 */
(function (window, $) {
    'use strict';
    if (!$ || window.WpPopPopShapeInspector) { return; }

    var HEX = /^#[0-9a-f]{6}$/i;
    var DEF_FILL = '#3b82f6';
    var DEF_STROKE = '#0284c7';

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

    function isShape(el) { return !!el && String(el.type || '').toLowerCase() === 'shape'; }
    function truthy(v) { return v === true || v === 1 || v === '1' || v === 'true'; }
    function intOr(v, d) { v = parseInt(v, 10); return isNaN(v) ? d : v; }

    function rerender(label) {
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
            window.WpPopPopBuilderCanvas.renderCanvas();
        }
        if (label && window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
            window.WpPopPopBuilderCore.pushHistory(label);
        }
    }

    function paintRot(deg) {
        $('#prop-shape-rotate-val').text(deg + '\u00B0');
    }

    function paintClear() {
        $('.wppoppop-shape-color').each(function () {
            var on = $('#' + $(this).attr('data-wpp-clear-for')).is(':checked');
            $(this).toggleClass('is-clear', on);
        });
    }

    function sync() {
        var el = activeEl();
        if (!isShape(el)) { return; }

        $('#prop-size-width, #prop-width').val(intOr(el.width !== undefined ? el.width : el.w, 120));
        $('#prop-size-height, #prop-height').val(intOr(el.height !== undefined ? el.height : el.h, 120));

        var fill = String(el.shapeFill || '');
        $('#prop-shape-fill').val(HEX.test(fill) ? fill : DEF_FILL);
        $('#prop-shape-transparent').prop('checked', truthy(el.shapeNoFill));

        var stroke = String(el.strokeColor || el.shapeStroke || '');
        var strokeClear = stroke.toLowerCase() === 'transparent';
        $('#prop-shape-stroke-color').val(HEX.test(stroke) ? stroke : DEF_STROKE);
        $('#prop-shape-stroke-transparent').prop('checked', strokeClear);

        $('#prop-shape-stroke-width').val(Math.max(0, intOr(el.strokeWidth !== undefined ? el.strokeWidth : el.shapeStrokeWidth, 0)));

        var rot = intOr(el.rotate !== undefined && el.rotate !== '' ? el.rotate : el.shapeRotate, 0);
        $('#prop-shape-rotate').val(rot);
        paintRot(rot);
        paintClear();
    }

    var timer = null;
    function sched() {
        if (timer) { return; }
        timer = window.setTimeout(function () {
            timer = null;
            sync();
        }, 60);
        window.setTimeout(sync, 250);
        window.setTimeout(sync, 600);
    }

    $(document).off('.wppShapeInsp');

    $(document).on('input.wppShapeInsp change.wppShapeInsp', '#prop-shape-rotate', function () {
        var el = activeEl(); if (!isShape(el)) { return; }
        var deg = Math.min(360, Math.max(0, intOr($(this).val(), 0)));
        el.rotate = deg;
        el.shapeRotate = deg;
        paintRot(deg);
    });

    $(document).on('input.wppShapeInsp change.wppShapeInsp', '#prop-shape-fill', function () {
        var el = activeEl(); if (!isShape(el)) { return; }
        el.shapeFill = $(this).val();
        if ($('#prop-shape-transparent').is(':checked')) {
            $('#prop-shape-transparent').prop('checked', false);
            el.shapeNoFill = false;
            rerender();
        }
        paintClear();
    });

    $(document).on('change.wppShapeInsp', '#prop-shape-transparent', function () {
        var el = activeEl(); if (!isShape(el)) { return; }
        el.shapeNoFill = $(this).is(':checked');
        paintClear();
        rerender('Shape Fill Transparency');
    });

    $(document).on('input.wppShapeInsp change.wppShapeInsp', '#prop-shape-stroke-color', function () {
        var el = activeEl(); if (!isShape(el)) { return; }
        el.strokeColor = $(this).val();
        el.shapeStroke = el.strokeColor;
        $('#prop-shape-stroke-transparent').prop('checked', false);
        paintClear();
        rerender();
    });

    $(document).on('change.wppShapeInsp', '#prop-shape-stroke-transparent', function () {
        var el = activeEl(); if (!isShape(el)) { return; }
        var clear = $(this).is(':checked');
        el.strokeColor = clear ? 'transparent' : ($('#prop-shape-stroke-color').val() || DEF_STROKE);
        el.shapeStroke = el.strokeColor;
        paintClear();
        rerender('Shape Stroke Transparency');
    });

    $(document).on('input.wppShapeInsp change.wppShapeInsp', '#prop-shape-stroke-width', function () {
        var el = activeEl(); if (!isShape(el)) { return; }
        var w = Math.max(0, intOr($(this).val(), 0));
        el.strokeWidth = w;
        el.shapeStrokeWidth = w;
        el.strokeNone = false;
    });

    $(document).on('click.wppShapeInsp', '#prop-shape-reset', function (e) {
        e.preventDefault();
        var el = activeEl(); if (!isShape(el)) { return; }
        el.strokeWidth = 0; el.shapeStrokeWidth = 0; el.strokeNone = false;
        el.rotate = 0; el.shapeRotate = 0;
        $('#prop-shape-stroke-width').val(0);
        $('#prop-shape-rotate').val(0);
        paintRot(0);
        rerender('Reset Shape');
    });

    // Keep the Width/Height pair in the shape panel and the Coordinates & Bounds pair identical.
    $(document).on('input.wppShapeInsp change.wppShapeInsp', '#prop-size-width, #prop-size-height', function () {
        var map = { 'prop-size-width': '#prop-width', 'prop-size-height': '#prop-height' };
        $(map[this.id]).val($(this).val());
    });
    $(document).on('input.wppShapeInsp change.wppShapeInsp', '#prop-width, #prop-height', function () {
        var map = { 'prop-width': '#prop-size-width', 'prop-height': '#prop-size-height' };
        $(map[this.id]).val($(this).val());
    });

    // Selection, tab switches and drag/resize on the stage re-hydrate the panel.
    window.addEventListener('click', function (e) {
        var t = e.target;
        if (t && t.closest && t.closest('.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-insp-tab, #wppoppop-inspector-drawer')) { sched(); }
    }, true);
    document.addEventListener('mouseup', function () { if (isShape(activeEl())) { sched(); } }, true);
    document.addEventListener('touchend', function () { if (isShape(activeEl())) { sched(); } }, true);
    document.addEventListener('keyup', function (e) {
        var k = e.keyCode;
        if (k >= 37 && k <= 40 && isShape(activeEl())) { sched(); }
    }, true);

    window.WpPopPopShapeInspector = { sync: sync };
    $(function () { sched(); });
})(window, window.jQuery);
