/* WPP_PROMO: dismiss memory (with optional "reappear after N days") + slider behaviour.
   Loaded in <head>: closed items are hidden by a generated rule before the page paints, so nothing flashes. */
(function () {
    'use strict';

    var PREFIX = 'wppoppop_promo_dismissed_';
    var DAY = 86400000;

    function storage() {
        try { return window.localStorage; } catch (e) { return null; }
    }

    function each(list, fn) {
        Array.prototype.forEach.call(list, fn);
    }

    function read(ls, key) {
        var raw = null;
        try { raw = ls.getItem(key); } catch (e) { return null; }
        if (!raw) { return null; }
        try {
            var v = JSON.parse(raw);
            if (v && typeof v === 'object' && typeof v.t === 'number') { return v; }
        } catch (e2) { /* legacy value */ }
        return { t: Date.now(), d: 0 };
    }

    /* Ids that are still dismissed; expired ones are removed so they show again. */
    function dismissedIds(ls) {
        var ids = [];
        if (!ls) { return ids; }
        var keys = [];
        try {
            for (var i = 0; i < ls.length; i++) {
                var k = ls.key(i);
                if (k && k.indexOf(PREFIX) === 0) { keys.push(k); }
            }
        } catch (e) { return ids; }
        var volatile = (window.wppoppopPromo && window.wppoppopPromo.volatile) || [];
        keys.forEach(function (k) {
            if (volatile.indexOf(k.slice(PREFIX.length)) !== -1) {
                try { ls.removeItem(k); } catch (e0) { /* ignore */ }
                return;
            }
            var v = read(ls, k);
            if (!v) { return; }
            if (v.d > 0 && Date.now() - v.t > v.d * DAY) {
                try { ls.removeItem(k); } catch (e) { /* ignore */ }
                return;
            }
            ids.push(k.slice(PREFIX.length));
        });
        return ids;
    }

    function hideBeforePaint(ids) {
        if (!ids.length) { return; }
        var css = ids.map(function (id) {
            return '[data-wpp-promo-id="' + id.replace(/[^a-zA-Z0-9_\-]/g, '') + '"]{display:none!important}';
        }).join('');
        var style = document.createElement('style');
        style.setAttribute('data-wpp-promo-hidden', '1');
        style.appendChild(document.createTextNode(css));
        (document.head || document.documentElement).appendChild(style);
    }

    var ls = storage();
    hideBeforePaint(dismissedIds(ls));

    function initDismiss() {
        each(document.querySelectorAll('[data-wpp-promo-id]'), function (el) {
            var btn = el.querySelector('[data-wpp-promo-dismiss]');
            if (!btn) { return; }
            btn.addEventListener('click', function () {
                el.style.display = 'none';
                if (el.getAttribute('data-wpp-promo-remember') === '0') { return; } // comes back on the next refresh
                if (!ls) { return; }
                var days = parseInt(el.getAttribute('data-wpp-promo-days'), 10) || 0;
                try {
                    ls.setItem(PREFIX + el.getAttribute('data-wpp-promo-id'), JSON.stringify({ t: Date.now(), d: days }));
                } catch (e) { /* storage full or blocked */ }
            });
        });
    }

    function initSlider(root) {
        var slides = root.querySelectorAll('.wpp-promo-slide');
        var dots = root.querySelectorAll('.wpp-promo-dot');
        var count = slides.length;
        if (count < 2) { return; }

        var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var ms = parseInt(root.getAttribute('data-interval'), 10) || 6000;
        var index = 0;
        var timer = null;

        function show(n) {
            index = (n + count) % count;
            each(slides, function (s, i) {
                var on = i === index;
                if (on) { s.classList.add('is-active'); } else { s.classList.remove('is-active'); }
                s.setAttribute('aria-hidden', on ? 'false' : 'true');
            });
            each(dots, function (d, i) {
                if (i === index) { d.classList.add('is-active'); } else { d.classList.remove('is-active'); }
            });
        }

        function stop() {
            if (timer) { window.clearInterval(timer); timer = null; }
        }

        function start() {
            stop();
            if (reduced) { return; }
            timer = window.setInterval(function () { show(index + 1); }, ms);
        }

        var prev = root.querySelector('.wpp-promo-prev');
        var next = root.querySelector('.wpp-promo-next');
        if (prev) { prev.addEventListener('click', function () { show(index - 1); start(); }); }
        if (next) { next.addEventListener('click', function () { show(index + 1); start(); }); }
        each(dots, function (d, i) {
            d.addEventListener('click', function () { show(i); start(); });
        });

        root.addEventListener('mouseenter', stop);
        root.addEventListener('mouseleave', start);
        root.addEventListener('focusin', stop);
        root.addEventListener('focusout', start);
        start();
    }

    function ready() {
        initDismiss();
        each(document.querySelectorAll('.wpp-promo-slider'), initSlider);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ready);
    } else {
        ready();
    }
})();
