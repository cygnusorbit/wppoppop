/* WPPOPPOP_SPAWN_PLACE: place a newly added element at the nearest free spot so it never overlaps existing elements */
(function (window, document) {
    'use strict';
    if (window.WpPopPopSpawnPlace) { return; }

    var GAP = 8;
    var STEP = 5;

    function num(v, d) { var n = parseFloat(v); return isNaN(n) ? d : n; }
    function px(el, a, b, d) { return num(el[a] !== undefined && el[a] !== '' ? el[a] : el[b], d); }

    function rectOf(el) {
        return {
            l: px(el, 'left', 'x', 0),
            t: px(el, 'top', 'y', 0),
            w: px(el, 'width', 'w', 0),
            h: px(el, 'height', 'h', 0)
        };
    }

    function hits(a, b) {
        return a.l < b.l + b.w + GAP && a.l + a.w + GAP > b.l &&
               a.t < b.t + b.h + GAP && a.t + a.h + GAP > b.t;
    }

    function boxSize() {
        var box = document.getElementById('wppoppop-canvas-box');
        return {
            w: (box && box.offsetWidth) || 640,
            h: (box && box.offsetHeight) || 400
        };
    }

    function collides(r, others) {
        for (var i = 0; i < others.length; i++) {
            if (hits(r, others[i])) { return true; }
        }
        return false;
    }

    function place(newEl, elements) {
        if (!newEl) { return newEl; }
        var others = [];
        (elements || []).forEach(function (e) {
            if (!e || e === newEl || e.hidden === true || e.hidden === 1 || e.hidden === '1') { return; }
            var r = rectOf(e);
            if (r.w > 0 && r.h > 0) { others.push(r); }
        });
        if (!others.length) { return newEl; }

        var me = rectOf(newEl);
        if (!collides(me, others)) { return newEl; }

        var size = boxSize();
        var maxL = Math.max(0, size.w - me.w);
        var maxT = Math.max(0, size.h - me.h);
        var wantL = Math.min(Math.max(0, me.l), maxL);
        var wantT = Math.min(Math.max(0, me.t), maxT);

        var xs = [], ys = [], i, v;
        for (v = 0; v <= maxL; v += STEP) { xs.push(v); }
        for (v = 0; v <= maxT; v += STEP) { ys.push(v); }
        xs.push(maxL); ys.push(maxT);
        // Edge-aligned candidates give tight, tidy placements next to existing elements
        others.forEach(function (o) {
            xs.push(o.l + o.w + GAP, o.l - GAP - me.w);
            ys.push(o.t + o.h + GAP, o.t - GAP - me.h);
        });

        var best = null, bestD = Infinity;
        for (i = 0; i < xs.length; i++) {
            var cl = xs[i];
            if (cl < 0 || cl > maxL) { continue; }
            for (var j = 0; j < ys.length; j++) {
                var ct = ys[j];
                if (ct < 0 || ct > maxT) { continue; }
                var dx = cl - wantL, dy = ct - wantT;
                var d = dx * dx + dy * dy;
                if (d >= bestD) { continue; }
                if (!collides({ l: cl, t: ct, w: me.w, h: me.h }, others)) { best = { l: cl, t: ct }; bestD = d; }
            }
        }

        if (!best) {
            // Canvas is full: cascade below the lowest element so the new layer stays reachable
            var bottom = 0;
            others.forEach(function (o) { bottom = Math.max(bottom, o.t + o.h); });
            best = { l: wantL, t: bottom + GAP };
        }

        newEl.left = Math.round(best.l);
        newEl.top = Math.round(best.t);
        if (newEl.x !== undefined) { newEl.x = newEl.left; }
        if (newEl.y !== undefined) { newEl.y = newEl.top; }
        return newEl;
    }

    window.WpPopPopSpawnPlace = { place: place };
})(window, document);
