/**
 * WpPopPop Dashboard: rearrange overview boxes like the WordPress dashboard.
 * Boxes stack inside columns; drag a box (it follows the pointer) and drop it on the dotted area, in any column.
 * Pointer drag + keyboard (arrow keys), saved per user. WPP_DASH_COLS. Plain JS: Chrome / Safari / Firefox / Edge.
 */
(function (window, document) {
    'use strict';

    var cfg = window.wppoppop_dash_layout || {};
    var grid = document.getElementById('wppoppop-dash-grid');
    if (!grid || !cfg.ajax_url) { return; }

    var PREFIX = 'wppoppop-box-';
    var lastSaved = '';
    var toastEl = null, toastTimer = null, keyTimer = null, drag = null;

    function isCol(n) { return !!(n.classList && n.classList.contains('wppoppop-dash-col')); }
    function isBox(n) { return !!(n.classList && n.classList.contains('wppoppop-box') && n.id && n.id.indexOf(PREFIX) === 0); }

    function visible(b) {
        return !!(b.offsetWidth || b.offsetHeight || (b.getClientRects && b.getClientRects().length));
    }

    function cols() { return Array.prototype.filter.call(grid.children, isCol); }
    function boxesIn(col) { return Array.prototype.filter.call(col.children, isBox); }
    function boxes() {
        var all = [];
        cols().forEach(function (c) { all = all.concat(boxesIn(c)); });
        return all;
    }

    /* DOM -> model: one list of box keys per column */
    function layout() {
        return cols().map(function (c) {
            return boxesIn(c).map(function (b) { return b.id.slice(PREFIX.length); });
        });
    }
    function signature(l) { return l.map(function (c) { return c.join(','); }).join('|'); }

    function title(b) {
        var custom = b.getAttribute('data-box-title');
        if (custom) { return custom; }
        var h = b.querySelector('h2');
        return h ? (h.textContent || '').replace(/\s+/g, ' ').trim() : b.id;
    }

    function decorate() {
        boxes().forEach(function (b) {
            if (b.querySelector('.wppoppop-box-grip')) { return; }
            var g = document.createElement('button');
            g.type = 'button';
            g.className = 'wppoppop-box-grip';
            g.title = 'Drag to rearrange (or use the arrow keys)';
            g.setAttribute('aria-label', 'Rearrange box: ' + title(b));
            g.innerHTML = '<span class="dashicons dashicons-move" aria-hidden="true"></span>';
            b.appendChild(g);
        });
    }

    function toast(msg, isError) {
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.className = 'wppoppop-dash-toast';
            toastEl.setAttribute('role', 'status');
            document.body.appendChild(toastEl);
        }
        toastEl.textContent = msg;
        toastEl.className = 'wppoppop-dash-toast is-on' + (isError ? ' is-error' : '');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(function () { toastEl.className = 'wppoppop-dash-toast' + (isError ? ' is-error' : ''); }, 1600);
    }

    /* model -> backend (whitelisted server-side) */
    function save() {
        var l = layout();
        var key = signature(l);
        if (key === lastSaved) { return; }
        var parts = ['action=wppoppop_save_dash_layout', 'nonce=' + encodeURIComponent(cfg.nonce || '')];
        l.forEach(function (c, i) {
            c.forEach(function (k) { parts.push('cols%5B' + i + '%5D%5B%5D=' + encodeURIComponent(k)); });
        });
        var xhr = new XMLHttpRequest();
        xhr.open('POST', cfg.ajax_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) { return; }
            var res = null;
            try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }
            if (xhr.status === 200 && res && res.success) { lastSaved = key; toast(wp.i18n.__('Layout saved', 'wppoppop')); }
            else { toast(wp.i18n.__('Could not save the layout', 'wppoppop'), true); }
        };
        xhr.send(parts.join('&'));
    }

    /* The visible column closest to the pointer (columns can be empty or stacked on small screens). */
    function nearestCol(x, y) {
        var best = null, bd = Infinity;
        cols().forEach(function (c) {
            if (!visible(c)) { return; }
            var r = c.getBoundingClientRect();
            var dx = Math.max(r.left - x, 0, x - r.right);
            var dy = Math.max(r.top - y, 0, y - r.bottom);
            var d = dx * dx + dy * dy;
            if (d < bd) { bd = d; best = c; }
        });
        return best;
    }

    function flowNext(ph) {
        var n = ph.nextSibling;
        while (n && (n === drag.box || n.nodeType !== 1 || !isBox(n) || !visible(n))) { n = n.nextSibling; }
        return n;
    }

    /* Moves the dotted area to the spot in `col` that matches the pointer height (boxes stack, heights adapt). */
    function placeAt(col, y) {
        var ref = null, list = boxesIn(col);
        for (var i = 0; i < list.length; i++) {
            var b = list[i];
            if (b === drag.box || !visible(b)) { continue; }
            var r = b.getBoundingClientRect();
            if (y < r.top + r.height / 2) { ref = b; break; }
        }
        if (drag.ph.parentNode === col && flowNext(drag.ph) === ref) { return; }
        col.insertBefore(drag.ph, ref);
    }

    function markEmpty() {
        cols().forEach(function (c) {
            var has = c === drag.ph.parentNode || boxesIn(c).some(function (b) { return b !== drag.box && visible(b); });
            if (has) { c.classList.remove('is-empty'); } else { c.classList.add('is-empty'); }
        });
    }

    /* pointer drag: the box follows the pointer, a dotted area marks where it will drop */
    function lift(e) {
        var box = drag.box, r = box.getBoundingClientRect();
        var ph = document.createElement('div');
        ph.className = 'wppoppop-box-placeholder';
        ph.style.height = r.height + 'px';
        box.parentNode.insertBefore(ph, box);
        drag.ph = ph;
        drag.saved = box.getAttribute('style');
        drag.dx = e.clientX - r.left;
        drag.dy = e.clientY - r.top;
        box.style.boxSizing = 'border-box';
        box.style.width = r.width + 'px';
        box.style.height = r.height + 'px';
        box.style.margin = '0';
        box.style.position = 'fixed';
        box.style.left = r.left + 'px';
        box.style.top = r.top + 'px';
        box.style.zIndex = '100001';
        box.classList.add('is-dragging');
        document.body.classList.add('wppoppop-dash-dragging');
        markEmpty();
    }

    function follow(e) {
        drag.box.style.left = (e.clientX - drag.dx) + 'px';
        drag.box.style.top = (e.clientY - drag.dy) + 'px';
    }

    grid.addEventListener('pointerdown', function (e) {
        var g = e.target && e.target.closest ? e.target.closest('.wppoppop-box-grip') : null;
        if (!g || !isBox(g.parentNode)) { return; }
        if (typeof e.button === 'number' && e.button !== 0) { return; }
        e.preventDefault();
        drag = { box: g.parentNode, grip: g, id: e.pointerId };
        try { g.setPointerCapture(e.pointerId); } catch (err) {}
        lift(e);
    });

    grid.addEventListener('pointermove', function (e) {
        if (!drag || e.pointerId !== drag.id) { return; }
        e.preventDefault();
        follow(e);
        if (e.clientY < 70) { window.scrollBy(0, -18); }
        else if (e.clientY > window.innerHeight - 70) { window.scrollBy(0, 18); }
        var c = nearestCol(e.clientX, e.clientY);
        if (c) { placeAt(c, e.clientY); markEmpty(); }
    });

    function finish(e) {
        if (!drag || (e && e.pointerId !== drag.id)) { return; }
        var d = drag;
        drag = null;
        try { d.grip.releasePointerCapture(d.id); } catch (err) {}
        if (d.saved === null) { d.box.removeAttribute('style'); } else { d.box.setAttribute('style', d.saved); }
        d.box.classList.remove('is-dragging');
        if (d.ph.parentNode) {
            d.ph.parentNode.insertBefore(d.box, d.ph);
            d.ph.parentNode.removeChild(d.ph);
        }
        cols().forEach(function (c) { c.classList.remove('is-empty'); });
        document.body.classList.remove('wppoppop-dash-dragging');
        save();
    }
    grid.addEventListener('pointerup', finish);
    grid.addEventListener('pointercancel', finish);

    /* keyboard: up/down = within the column, left/right = to the neighbouring column */
    grid.addEventListener('keydown', function (e) {
        var g = e.target && e.target.closest ? e.target.closest('.wppoppop-box-grip') : null;
        if (!g || !isBox(g.parentNode)) { return; }
        var k = e.key || '';
        var up = (k === 'ArrowUp' || k === 'Up');
        var down = (k === 'ArrowDown' || k === 'Down');
        var left = (k === 'ArrowLeft' || k === 'Left');
        var right = (k === 'ArrowRight' || k === 'Right');
        if (!up && !down && !left && !right) { return; }
        e.preventDefault();
        var box = g.parentNode, col = box.parentNode;
        if (up || down) {
            var list = boxesIn(col).filter(visible), i = list.indexOf(box);
            if (up && i > 0) { col.insertBefore(box, list[i - 1]); }
            else if (down && i > -1 && i < list.length - 1) { col.insertBefore(box, list[i + 1].nextSibling); }
            else { return; }
        } else {
            var vis = cols().filter(visible), ci = vis.indexOf(col);
            var to = vis[ci + (right ? 1 : -1)];
            if (ci < 0 || !to) { return; }
            var mine = boxesIn(col).filter(visible), at = Math.max(0, mine.indexOf(box));
            var theirs = boxesIn(to).filter(visible);
            to.insertBefore(box, theirs[at] || null);
        }
        g.focus();
        window.clearTimeout(keyTimer);
        keyTimer = window.setTimeout(save, 400);
    });

    decorate();
    lastSaved = signature(layout());
})(window, document);
