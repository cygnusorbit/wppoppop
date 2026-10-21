/**
 * WpPopPop Dashboard: rearrange overview boxes (pointer drag + keyboard), saved per user.
 * Plain JS, no dependencies. Chrome / Safari / Firefox / Edge.
 */
(function (window, document) {
    'use strict';

    var cfg = window.wppoppop_dash_layout || {};
    var grid = document.getElementById('wppoppop-dash-grid');
    if (!grid || !cfg.ajax_url) { return; }

    var PREFIX = 'wppoppop-box-';
    var lastSaved = '';
    var toastEl = null, toastTimer = null, keyTimer = null, drag = null;

    function boxes() {
        return Array.prototype.filter.call(grid.children, function (n) {
            return n.classList && n.classList.contains('wppoppop-box') && n.id && n.id.indexOf(PREFIX) === 0;
        });
    }

    /* DOM -> model */
    function order() {
        return boxes().map(function (b) { return b.id.slice(PREFIX.length); });
    }

    function title(b) {
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
        var o = order();
        var key = o.join(',');
        if (key === lastSaved) { return; }
        var parts = ['action=wppoppop_save_dash_layout', 'nonce=' + encodeURIComponent(cfg.nonce || '')];
        o.forEach(function (k) { parts.push('order%5B%5D=' + encodeURIComponent(k)); });
        var xhr = new XMLHttpRequest();
        xhr.open('POST', cfg.ajax_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) { return; }
            var res = null;
            try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }
            if (xhr.status === 200 && res && res.success) { lastSaved = key; toast('Layout saved'); }
            else { toast('Could not save the layout', true); }
        };
        xhr.send(parts.join('&'));
    }

    function hit(x, y, skip) {
        var list = boxes();
        for (var i = 0; i < list.length; i++) {
            if (list[i] === skip) { continue; }
            var r = list[i].getBoundingClientRect();
            if (x >= r.left && x <= r.right && y >= r.top && y <= r.bottom) { return list[i]; }
        }
        return null;
    }

    function place(box, target) {
        var list = boxes();
        if (list.indexOf(target) > list.indexOf(box)) { grid.insertBefore(box, target.nextSibling); }
        else { grid.insertBefore(box, target); }
    }

    /* pointer drag */
    grid.addEventListener('pointerdown', function (e) {
        var g = e.target && e.target.closest ? e.target.closest('.wppoppop-box-grip') : null;
        if (!g) { return; }
        if (typeof e.button === 'number' && e.button !== 0) { return; }
        e.preventDefault();
        drag = { box: g.parentNode, grip: g, id: e.pointerId };
        try { g.setPointerCapture(e.pointerId); } catch (err) {}
        drag.box.classList.add('is-dragging');
        document.body.classList.add('wppoppop-dash-dragging');
    });

    grid.addEventListener('pointermove', function (e) {
        if (!drag || e.pointerId !== drag.id) { return; }
        e.preventDefault();
        if (e.clientY < 70) { window.scrollBy(0, -18); }
        else if (e.clientY > window.innerHeight - 70) { window.scrollBy(0, 18); }
        var t = hit(e.clientX, e.clientY, drag.box);
        if (t) { place(drag.box, t); }
    });

    function finish(e) {
        if (!drag || (e && e.pointerId !== drag.id)) { return; }
        try { drag.grip.releasePointerCapture(drag.id); } catch (err) {}
        drag.box.classList.remove('is-dragging');
        document.body.classList.remove('wppoppop-dash-dragging');
        drag = null;
        save();
    }
    grid.addEventListener('pointerup', finish);
    grid.addEventListener('pointercancel', finish);

    /* keyboard */
    grid.addEventListener('keydown', function (e) {
        var g = e.target && e.target.closest ? e.target.closest('.wppoppop-box-grip') : null;
        if (!g) { return; }
        var k = e.key || '';
        var back = (k === 'ArrowLeft' || k === 'ArrowUp' || k === 'Left' || k === 'Up');
        var fwd = (k === 'ArrowRight' || k === 'ArrowDown' || k === 'Right' || k === 'Down');
        if (!back && !fwd) { return; }
        e.preventDefault();
        var box = g.parentNode, list = boxes(), i = list.indexOf(box);
        if (back && i > 0) { grid.insertBefore(box, list[i - 1]); }
        else if (fwd && i < list.length - 1) { grid.insertBefore(box, list[i + 1].nextSibling); }
        else { return; }
        g.focus();
        window.clearTimeout(keyTimer);
        keyTimer = window.setTimeout(save, 400);
    });

    decorate();
    lastSaved = order().join(',');
})(window, document);
