/**
 * WpPopPop builder element extension store.
 *
 * Companion plugins register per-element behaviour here instead of editing core scripts:
 *
 *   WpPopPopElementExt.register('type', {
 *     stage(el, ctx)       -> inner HTML of a form element on the stage (ctx: align, reqStar, defVal)
 *     mediaStage(el)       -> inner HTML of a media element on the stage
 *     stageDone($node, el) -> runs after the stage node was styled
 *     fallbackInner(el)    -> inner HTML used by getInnerMarkup()
 *     spawn(newEl)         -> default values for a freshly added layer
 *     hydrate(el, type)    -> Inspector fields <- model
 *     flushData(el)        -> flush own Data controls into the model; return true when handled
 *     syncRows(el)         -> show/hide conditional Inspector rows (called for every registered type)
 *     bindInspector(resolveEl) -> bind Inspector controls (called for every registered type)
 *   });
 */
(function (window) {
    'use strict';
    if (window.WpPopPopElementExt) { return; }

    var store = {};
    var own = Object.prototype.hasOwnProperty;

    function key(type) {
        return String(type || '').toLowerCase().replace(/^\s+|\s+$/g, '');
    }

    window.WpPopPopElementExt = {
        register: function (type, def) {
            var k = key(type);
            if (!k || !def) { return; }
            var cur = store[k] = store[k] || {};
            for (var h in def) {
                if (own.call(def, h) && typeof def[h] === 'function') { cur[h] = def[h]; }
            }
        },
        has: function (type, hook) {
            var d = store[key(type)];
            return !!(d && typeof d[hook] === 'function');
        },
        call: function (type, hook, args) {
            var d = store[key(type)];
            if (d && typeof d[hook] === 'function') { return d[hook].apply(d, args || []); }
        },
        all: function (hook, args) {
            for (var k in store) {
                if (own.call(store, k) && typeof store[k][hook] === 'function') { store[k][hook].apply(store[k], args || []); }
            }
        }
    };
})(window);
