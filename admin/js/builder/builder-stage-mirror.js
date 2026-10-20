/**
 * WpPopPop Builder: Stage Mirror
 * Edit mode shows the SAME markup + inline styles as live preview by asking the real frontend
 * renderer (PHP) for each layer and mirroring it onto the stage node. Geometry stays editable.
 * Cross-browser: Chrome, Safari, Firefox, Edge (no optional chaining, no arrow functions).
 */
(function(window, $) {
    'use strict';
    if (window.__wppoppopStageMirror) return;
    window.__wppoppopStageMirror = true;

    var cache = {}, timer = null, busy = false, again = false, fails = 0;
    var KEEP = ['position', 'left', 'top', 'width', 'height', 'zIndex', 'display'];
    var SIG_SKIP = ['top', 'left', 'x', 'y', 'zIndex'];

    function vars() { return window.wppoppop_vars || {}; }
    function ajaxUrl() {
        var v = vars();
        return v.ajax_url || (typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : '/wp-admin/admin-ajax.php');
    }
    function nonce() { var v = vars(); return v.nonce || $('#wppoppop-builder-nonce').val() || ''; }
    function canvas() { return window.WpPopPopBuilderCanvas; }
    function elements() {
        var C = canvas();
        var l = (C && typeof C.getActiveElements === 'function') ? C.getActiveElements() : [];
        return Array.isArray(l) ? l : [];
    }
    function blocked() {
        var C = canvas();
        return fails >= 3 || window.WPPOPPOP_STAGE_MIRROR === false || !!(C && C.isPreview);
    }
    function sigOf(el) {
        var c = {}, k;
        for (k in el) {
            if (Object.prototype.hasOwnProperty.call(el, k) && SIG_SKIP.indexOf(k) === -1) { c[k] = el[k]; }
        }
        try { return JSON.stringify(c); } catch (e) { return String(Math.random()); }
    }

    function apply(el, html, sig, force) {
        var node = document.getElementById('el-' + el.id);
        if (!node) return;
        if (!force && node.getAttribute('data-wpp-sig') === sig) return;

        var doc;
        try { doc = new DOMParser().parseFromString('<body>' + html + '</body>', 'text/html'); } catch (e) { return; }
        var scripts = doc.querySelectorAll('script');
        for (var s = 0; s < scripts.length; s++) { scripts[s].parentNode.removeChild(scripts[s]); }

        var wrap = doc.body.querySelector('.wppoppop-layer-item') || doc.body.firstElementChild;
        if (!wrap) return;
        var extras = '', i, kids = doc.body.children;
        for (i = 0; i < kids.length; i++) { if (kids[i] !== wrap) { extras += kids[i].outerHTML; } }
        var inner = extras + wrap.innerHTML;

        var keep = {};
        for (i = 0; i < KEEP.length; i++) { keep[KEEP[i]] = node.style[KEEP[i]]; }
        var $handles = $(node).children('.ui-resizable-handle, .wppoppop-resize-handle').detach();

        node.style.cssText = wrap.getAttribute('style') || '';
        for (i = 0; i < KEEP.length; i++) { if (keep[KEEP[i]]) { node.style[KEEP[i]] = keep[KEEP[i]]; } }

        node.innerHTML = inner;
        if ((wrap.getAttribute('class') || '').indexOf('wppoppop-el-shape') !== -1) { node.style.borderRadius = '0'; }
        $(node).append($handles);

        var cls = (wrap.getAttribute('class') || '').split(/\s+/);
        for (i = 0; i < cls.length; i++) {
            if (/^wppoppop-el-/.test(cls[i])) { node.classList.add(cls[i]); }
        }
        node.setAttribute('data-wpp-sig', sig);
    }

    function syncOne(el, force) {
        if (blocked() || !el || el.id === undefined || el.hidden) return;
        var sig = sigOf(el), hit = cache[el.id];
        if (hit && hit.sig === sig) { apply(el, hit.html, sig, force); return; }
        schedule();
    }

    function schedule(ms) {
        if (blocked()) return;
        if (timer) clearTimeout(timer);
        timer = setTimeout(flush, ms || 90);
    }

    function flush() {
        timer = null;
        if (blocked()) return;
        if (busy) { again = true; return; }
        var send = [], sigs = {};
        elements().forEach(function(el) {
            if (!el || el.id === undefined || el.hidden) return;
            var s = sigOf(el), hit = cache[el.id];
            if (hit && hit.sig === s) { apply(el, hit.html, s, false); return; }
            send.push(el); sigs[el.id] = s;
        });
        if (!send.length) return;
        busy = true;
        $.ajax({
            url: ajaxUrl(), type: 'POST', dataType: 'json',
            data: { action: 'wppoppop_render_stage', nonce: nonce(), elements: JSON.stringify(send) }
        }).done(function(res) {
            if (res && res.success && res.data && res.data.html) {
                fails = 0;
                if (Object.keys(cache).length > 400) { cache = {}; }
                var map = res.data.html;
                elements().forEach(function(el) {
                    if (!el || map[el.id] === undefined) return;
                    cache[el.id] = { sig: sigs[el.id], html: map[el.id] };
                    if (sigOf(el) === sigs[el.id]) { apply(el, map[el.id], sigs[el.id], true); }
                });
            } else { fails++; }
        }).fail(function() { fails++; }).always(function() {
            busy = false;
            if (again) { again = false; schedule(30); }
        });
    }

    function wrapFn(obj, name, afterEl) {
        var f = obj && obj[name];
        if (typeof f !== 'function') return false;
        if (f.__wppMirror) return true;
        var w = function() {
            var r = f.apply(this, arguments);
            try { afterEl.apply(this, arguments); } catch (e) {}
            return r;
        };
        w.__wppMirror = true;
        obj[name] = w;
        return true;
    }

    function afterStage(el) {
        if (!el || typeof el !== 'object' || el.id === undefined) return;
        var n = document.getElementById('el-' + el.id);
        if (n) n.removeAttribute('data-wpp-sig');
        syncOne(el, true);
    }

    function hook() {
        var R = window.WpPopPopElementsRegistry;
        var C = canvas();
        var ok = true;
        if (R) {
            ok = wrapFn(R, 'renderMediaElementStage', afterStage) && ok;
            ok = wrapFn(R, 'renderFormElementStage', afterStage) && ok;
            ok = wrapFn(R, 'applyStyleToStageNode', afterStage) && ok;
        } else { ok = false; }
        if (C) {
            ok = wrapFn(C, 'renderCanvas', function() {
                elements().forEach(function(el) { syncOne(el, true); });
                schedule();
            }) && ok;
        } else { ok = false; }
        return ok;
    }

    $(document).off('input.wppMirror change.wppMirror')
        .on('input.wppMirror change.wppMirror',
            '#wppoppop-inspector-drawer input, #wppoppop-inspector-drawer select, #wppoppop-inspector-drawer textarea',
            function() { setTimeout(function() { schedule(); }, 30); });

    $(function() {
        var tries = 0;
        var t = setInterval(function() {
            tries++;
            if (hook() && tries > 4) { /* keep re-checking slowly below */ }
            if (tries > 60) { clearInterval(t); }
        }, 250);
        setInterval(hook, 2000);
        setTimeout(function() { schedule(10); }, 400);
    });
})(window, jQuery);
