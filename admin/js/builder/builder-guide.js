/**
 * WpPopPop Builder Guide (guided tour). Vanilla ES5, no dependencies.
 * Steps come from window.wppoppop_guide_vars (validated server-side, PHP filter: wppoppop_guide_steps).
 * JS extension point: listen to "wppoppop:guide:steps" and push to event.detail.steps.
 * The overlay never intercepts pointer events outside its card, so layer drag / push is untouched.
 */
(function (window, document) {
    'use strict';

    var cfg = window.wppoppop_guide_vars;
    if (!cfg || !cfg.enabled) { return; }

    var i18n = cfg.i18n || {};
    var steps = [];
    var idx = 0;
    var running = false;
    var openedSettings = false;
    var openedInspector = false;
    var busy = false;
    var textFound = true;
    var root, spot, card, ui = {};
    var lastFocus = null;
    var showTimer = 0;
    var settleUntil = 0;
    var rafId = 0;

    /* Browser-side copy of "already seen": the tour never auto-starts twice here, even if the server save is lost. */
    var SEEN_KEY = 'wppoppop_guide_seen_' + (cfg.uid || 0);

    function seenLocal() {
        try { return window.localStorage.getItem(SEEN_KEY) === '1'; } catch (e) { return false; }
    }

    function markSeen() {
        try { window.localStorage.setItem(SEEN_KEY, '1'); } catch (e) { /* storage may be blocked */ }
    }

    function q(sel, ctx) { return (ctx || document).querySelector(sel); }

    function make(tag, cls, text) {
        var n = document.createElement(tag);
        if (cls) { n.className = cls; }
        if (text !== undefined && text !== null) { n.textContent = text; }
        return n;
    }

    function button(cls, label, type) {
        var b = make('button', cls, label);
        b.setAttribute('type', type || 'button');
        return b;
    }

    function fmt(str, map) {
        return String(str || '').replace(/\{(\w+)\}/g, function (m, k) {
            return Object.prototype.hasOwnProperty.call(map, k) ? map[k] : m;
        });
    }

    /* **bold** only; everything else is plain text, so no HTML is ever injected. */
    function rich(node, text) {
        var parts = String(text || '').split(/\*\*(.+?)\*\*/g);
        var i, el;
        while (node.firstChild) { node.removeChild(node.firstChild); }
        for (i = 0; i < parts.length; i++) {
            if (!parts[i]) { continue; }
            if (i % 2) {
                el = document.createElement('strong');
                el.textContent = parts[i];
                node.appendChild(el);
            } else {
                node.appendChild(document.createTextNode(parts[i]));
            }
        }
    }

    /* Saves run one at a time, in order, so a late "progress" call can never overwrite a "dismiss". */
    var queue = [];
    var sending = false;

    function pump() {
        var job, xhr, finished = false;
        if (sending || !queue.length) { return; }
        sending = true;
        job = queue.shift();
        function done(ok) {
            if (finished) { return; }
            finished = true;
            sending = false;
            if (job.cb) { job.cb(ok); }
            pump();
        }
        try {
            xhr = new XMLHttpRequest();
            xhr.open('POST', cfg.ajax_url, true);
            xhr.timeout = 10000;
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            xhr.onreadystatechange = function () {
                var res = null;
                if (xhr.readyState !== 4) { return; }
                try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }
                done(xhr.status === 200 && !!res && res.success === true);
            };
            xhr.ontimeout = function () { done(false); };
            xhr.send('action=wppoppop_guide_save&op=' + encodeURIComponent(job.op) +
                '&step=' + encodeURIComponent(job.step) + '&nonce=' + encodeURIComponent(cfg.nonce));
        } catch (e) { done(false); }
    }

    function save(op, step, cb) {
        queue.push({ op: op, step: step, cb: cb });
        pump();
    }

    function viewport() {
        return {
            w: window.innerWidth || document.documentElement.clientWidth,
            h: window.innerHeight || document.documentElement.clientHeight
        };
    }

    function isVisible(n) {
        var r, v, cs;
        if (!n || !n.getClientRects || !n.getClientRects().length) { return false; }
        r = n.getBoundingClientRect();
        v = viewport();
        if (r.width < 2 || r.height < 2) { return false; }
        if (r.right <= 0 || r.bottom <= 0 || r.left >= v.w || r.top >= v.h) { return false; }
        cs = window.getComputedStyle(n);
        return cs.visibility !== 'hidden' && cs.display !== 'none';
    }

    function findTarget(step) {
        var list = step.selector || [];
        var i, n;
        if (typeof list === 'string') { list = [list]; }
        for (i = 0; i < list.length; i++) {
            try { n = q(list[i]); } catch (e) { n = null; }
            if (isVisible(n)) { return n; }
        }
        return null;
    }

    /* ---- whitelisted "before" actions (only touch the same UI a user would click) ---- */
    function inspectorOpen() {
        var d = q('#wppoppop-inspector-drawer');
        return !!(d && d.classList.contains('is-open'));
    }

    function openInspector() {
        var row;
        if (inspectorOpen()) { return false; }
        row = q('#wppoppop-layers-list .wppoppop-layer-item .wppoppop-layer-icon') ||
              q('#wppoppop-layers-list .wppoppop-layer-item .wppoppop-layer-title');
        if (row) { row.click(); openedInspector = true; return true; }
        return false;
    }

    /* Close the Inspector only if the tour opened it, so the builder is left as the user had it. */
    function closeInspector() {
        var x;
        if (!openedInspector) { return; }
        openedInspector = false;
        x = q('#wppoppop-inspector-close');
        if (x && inspectorOpen()) { x.click(); }
    }

    function setTab(name) {
        var b = q('#wppoppop-inspector-tabs-bar .wppoppop-insp-tab[data-tab="' + name + '"]');
        if (b && b.getClientRects().length) { b.click(); return true; }
        return false;
    }

    function drawer(open) {
        var S = window.WpPopPopBuilderSettings;
        if (!S) { return false; }
        if (open && typeof S.openDrawer === 'function') { S.openDrawer(); openedSettings = true; return true; }
        if (!open && openedSettings && typeof S.closeDrawer === 'function') { S.closeDrawer(); openedSettings = false; return true; }
        return false;
    }

    function runBefore(step) {
        var b = step.before || '';
        var changed = drawer(b === 'open-settings');
        if (b === 'open-inspector') {
            changed = openInspector() || changed;
        } else if (b.indexOf('inspector-tab:') === 0) {
            changed = openInspector() || changed;
            changed = setTab(b.split(':')[1]) || changed;
        }
        return changed;
    }

    /* ---- DOM ---- */
    function build() {
        var foot, links, nav;
        root = make('div', 'wppoppop-tour');
        root.setAttribute('id', 'wppoppop-tour');
        root.appendChild(make('div', 'wppoppop-tour-dim'));
        spot = make('div', 'wppoppop-tour-spot');
        root.appendChild(spot);

        card = make('div', 'wppoppop-tour-card');
        card.setAttribute('role', 'dialog');
        card.setAttribute('aria-modal', 'false');
        card.setAttribute('aria-labelledby', 'wppoppop-tour-title');
        card.setAttribute('aria-describedby', 'wppoppop-tour-text');
        card.setAttribute('tabindex', '-1');

        ui.x = button('wppoppop-tour-x', '\u00d7');
        ui.x.setAttribute('aria-label', i18n.close || 'Close');
        ui.count = make('div', 'wppoppop-tour-count');
        ui.title = make('h3', 'wppoppop-tour-title');
        ui.title.setAttribute('id', 'wppoppop-tour-title');
        ui.text = make('p', 'wppoppop-tour-text');
        ui.text.setAttribute('id', 'wppoppop-tour-text');
        ui.text.setAttribute('aria-live', 'polite');

        foot = make('div', 'wppoppop-tour-foot');
        links = make('div', 'wppoppop-tour-links');
        ui.skip = button('wppoppop-tour-link', i18n.skip || 'Skip');
        ui.dont = button('wppoppop-tour-link', i18n.dont_show || "Don't show again");
        links.appendChild(ui.skip);
        links.appendChild(ui.dont);
        nav = make('div', 'wppoppop-tour-nav');
        ui.back = button('wppoppop-tour-btn', i18n.back || 'Back');
        ui.next = button('wppoppop-tour-btn is-primary', i18n.next || 'Next');
        nav.appendChild(ui.back);
        nav.appendChild(ui.next);
        foot.appendChild(links);
        foot.appendChild(nav);

        card.appendChild(ui.x);
        card.appendChild(ui.count);
        card.appendChild(ui.title);
        card.appendChild(ui.text);
        card.appendChild(foot);
        root.appendChild(card);
        document.body.appendChild(root);

        ui.x.addEventListener('click', function () { close('skip'); });
        ui.skip.addEventListener('click', function () { close('skip'); });
        ui.dont.addEventListener('click', function () { close('dismiss'); });
        ui.back.addEventListener('click', function () { show(idx - 1); });
        ui.next.addEventListener('click', function () {
            if (idx >= steps.length - 1) { close('complete'); } else { show(idx + 1); }
        });
        card.addEventListener('keydown', trapTab);
    }

    function trapTab(e) {
        var list, first, last, i, items;
        if (e.key !== 'Tab' && e.keyCode !== 9) { return; }
        items = card.querySelectorAll('button');
        list = [];
        for (i = 0; i < items.length; i++) {
            if (!items[i].disabled && items[i].getClientRects().length) { list.push(items[i]); }
        }
        if (!list.length) { return; }
        first = list[0];
        last = list[list.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }

    /* The "empty" text is shown while the target is missing and swapped back once it appears (slow slide-in animations). */
    function applyText(found) {
        var s = steps[idx];
        textFound = found;
        rich(ui.text, (!found && s.empty) ? s.empty : s.text);
    }

    /* ---- layout ---- */
    function layout() {
        var s = steps[idx];
        var t, r, v, cw, ch, m = 12, gap = 14, pad = 6, order, chosen = null, left, top, i, pl;
        if (!running || !s) { return; }
        v = viewport();
        cw = card.offsetWidth;
        ch = card.offsetHeight;
        t = findTarget(s);
        if (!!t !== textFound) {
            applyText(!!t);
            ch = card.offsetHeight;
        }

        if (!t) {
            root.classList.add('is-center');
            card.style.left = Math.max(m, Math.round((v.w - cw) / 2)) + 'px';
            card.style.top = Math.max(m, Math.round((v.h - ch) / 2)) + 'px';
            return;
        }
        root.classList.remove('is-center');
        r = t.getBoundingClientRect();
        spot.style.left = (r.left - pad) + 'px';
        spot.style.top = (r.top - pad) + 'px';
        spot.style.width = (r.width + pad * 2) + 'px';
        spot.style.height = (r.height + pad * 2) + 'px';

        pl = (s.placement && s.placement !== 'auto' && s.placement !== 'center') ? s.placement : '';
        order = pl ? [pl, 'bottom', 'top', 'right', 'left'] : ['bottom', 'top', 'right', 'left'];
        for (i = 0; i < order.length && !chosen; i++) {
            if (order[i] === 'bottom' && r.bottom + gap + ch + m <= v.h) { chosen = 'bottom'; }
            else if (order[i] === 'top' && r.top - gap - ch - m >= 0) { chosen = 'top'; }
            else if (order[i] === 'right' && r.right + gap + cw + m <= v.w) { chosen = 'right'; }
            else if (order[i] === 'left' && r.left - gap - cw - m >= 0) { chosen = 'left'; }
        }
        if (chosen === 'bottom') { left = r.left + r.width / 2 - cw / 2; top = r.bottom + gap; }
        else if (chosen === 'top') { left = r.left + r.width / 2 - cw / 2; top = r.top - gap - ch; }
        else if (chosen === 'right') { left = r.right + gap; top = r.top + r.height / 2 - ch / 2; }
        else if (chosen === 'left') { left = r.left - gap - cw; top = r.top + r.height / 2 - ch / 2; }
        else { left = v.w - cw - m; top = v.h - ch - m; }

        card.style.left = Math.round(Math.max(m, Math.min(left, v.w - cw - m))) + 'px';
        card.style.top = Math.round(Math.max(m, Math.min(top, v.h - ch - m))) + 'px';
    }

    function settle() {
        cancelAnimationFrame2(rafId);
        (function tick() {
            layout();
            if (running && Date.now() < settleUntil) { rafId = requestAnimationFrame2(tick); }
        })();
    }

    function requestAnimationFrame2(fn) {
        return (window.requestAnimationFrame || function (f) { return window.setTimeout(f, 16); })(fn);
    }

    function cancelAnimationFrame2(id) {
        if (!id) { return; }
        (window.cancelAnimationFrame || window.clearTimeout)(id);
    }

    function onViewportChange() {
        settleUntil = Date.now() + 120;
        settle();
    }

    /* ---- flow ---- */
    function render() {
        var s = steps[idx];
        var found = !!findTarget(s);
        ui.count.textContent = fmt(i18n.step_of, { n: idx + 1, total: steps.length });
        ui.title.textContent = s.title;
        applyText(found);
        ui.back.hidden = idx === 0;
        ui.next.textContent = (idx >= steps.length - 1) ? (i18n.finish || 'Finish') : (i18n.next || 'Next');
        settleUntil = Date.now() + 700;
        settle();
        try { ui.next.focus({ preventScroll: true }); } catch (e) { ui.next.focus(); }
    }

    function show(i) {
        var changed;
        idx = Math.max(0, Math.min(steps.length - 1, i));
        changed = runBefore(steps[idx]);
        save('progress', idx + 1);
        window.clearTimeout(showTimer);
        showTimer = window.setTimeout(render, changed ? 300 : 0);
    }

    function onKey(e) {
        if (running && (e.key === 'Escape' || e.key === 'Esc' || e.keyCode === 27)) {
            e.stopPropagation();
            e.preventDefault();
            close('skip');
        }
    }

    function collectSteps() {
        var list = (cfg.steps || []).slice(0);
        var evt, out = [], i;
        try {
            evt = document.createEvent('CustomEvent');
            evt.initCustomEvent('wppoppop:guide:steps', false, false, { steps: list });
            document.dispatchEvent(evt);
        } catch (e) { /* extension hook is optional */ }
        for (i = 0; i < list.length; i++) {
            if (list[i] && typeof list[i].title === 'string' && typeof list[i].text === 'string') { out.push(list[i]); }
        }
        return out;
    }

    function start(from) {
        if (running) { return; }
        steps = collectSteps();
        if (!steps.length) { return; }
        if (!root) { build(); }
        lastFocus = document.activeElement;
        running = true;
        markSeen();
        root.classList.add('is-open');
        document.addEventListener('keydown', onKey, true);
        window.addEventListener('resize', onViewportChange);
        window.addEventListener('scroll', onViewportChange, true);
        show(typeof from === 'number' ? from : 0);
    }

    function stop() {
        if (!running) { return; }
        running = false;
        window.clearTimeout(showTimer);
        cancelAnimationFrame2(rafId);
        document.removeEventListener('keydown', onKey, true);
        window.removeEventListener('resize', onViewportChange);
        window.removeEventListener('scroll', onViewportChange, true);
        root.classList.remove('is-open');
        drawer(false);
        closeInspector();
        if (lastFocus && typeof lastFocus.focus === 'function' && document.body.contains(lastFocus)) {
            try { lastFocus.focus(); } catch (e) { /* element may be gone */ }
        }
    }

    function setBusy(on) {
        busy = on;
        ui.dont.disabled = on;
        ui.dont.textContent = on ? (i18n.saving || 'Saving…') : (i18n.dont_show || "Don't show again");
    }

    function close(how) {
        if (how === 'dismiss') {
            if (busy) { return; }
            setBusy(true);
            /* Close when the server answers, or after 1.5 s at most; the browser-side flag already covers a lost save. */
            var closed = false;
            var finish = function (ok) {
                if (closed) { return; }
                closed = true;
                setBusy(false);
                if (!ok && window.console && window.console.warn) { window.console.warn('WpPopPop guide: the server did not confirm "Don\'t show again".'); }
                stop();
            };
            markSeen();
            save('dismiss', idx + 1, finish);
            window.setTimeout(function () { finish(true); }, 1500);
            return;
        }
        save(how === 'complete' ? 'complete' : 'progress', idx + 1);
        stop();
    }

    window.WpPopPopGuide = {
        start: start,
        stop: stop,
        isRunning: function () { return running; }
    };

    function whenReady(cb) {
        var tries = 0;
        (function poll() {
            var busy = document.getElementById('wppoppop-boot-loader') || document.getElementById('wppoppop-fouc-guard');
            if (!busy || ++tries > 40) { window.setTimeout(cb, 250); } else { window.setTimeout(poll, 150); }
        })();
    }

    function boot() {
        document.addEventListener('click', function (e) {
            var t = e.target;
            while (t && t !== document && t.id !== 'wppoppop-btn-guide') { t = t.parentNode; }
            if (!t || t === document) { return; }
            e.preventDefault();
            if (running) { stop(); } else { start(0); }
        });
        if (cfg.autostart && !seenLocal()) { whenReady(function () { start(0); }); }
    }

    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})(window, document);
