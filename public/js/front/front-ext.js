/* WpPopPop declarative modules: frontend runtime.
 * A module's module.js calls WpPopPopExt.register('<slug>', function (uid, values) { ... });
 * It runs once for every popup on the page that carries data for that module. */
(function (w, d) {
    'use strict';
    var E = w.WpPopPopExt = w.WpPopPopExt || { data: {}, h: {} };
    var ready = false;

    E.get = function (uid, slug) {
        return (E.data[uid] && E.data[uid][slug]) || null;
    };

    function run(slug, fn) {
        for (var uid in E.data) {
            if (Object.prototype.hasOwnProperty.call(E.data, uid) && E.data[uid][slug]) {
                try { fn(uid, E.data[uid][slug]); } catch (err) { if (w.console) { w.console.error('WpPopPopExt[' + slug + ']', err); } }
            }
        }
    }

    E.register = function (slug, fn) {
        if (typeof fn !== 'function') { return; }
        (E.h[slug] = E.h[slug] || []).push(fn);
        if (ready) { run(slug, fn); }
    };

    function boot() {
        ready = true;
        for (var slug in E.h) {
            if (Object.prototype.hasOwnProperty.call(E.h, slug)) {
                for (var i = 0; i < E.h[slug].length; i++) { run(slug, E.h[slug][i]); }
            }
        }
    }

    if (d.readyState === 'loading') { d.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})(window, document);
