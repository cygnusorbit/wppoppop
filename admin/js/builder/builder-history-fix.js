/* WPPOPPOP_HISTORY_FIX: undo/redo never restores the transparent bootstrap placeholder */
(function (window, $) {
    'use strict';

    function core() { return window.WpPopPopBuilderCore; }

    // The core boots with a transparent placeholder canvasMeta (bg_transparent:true); it is never a user choice
    function fixMeta(metaMap) {
        if (!metaMap) { return; }
        for (var k in metaMap) {
            if (Object.prototype.hasOwnProperty.call(metaMap, k)) {
                var m = metaMap[k];
                if (m && m.bg_transparent === true) {
                    delete m.bg_transparent;
                    m.bg_color = '#ffffff';
                    if (!m.bg_mode) { m.bg_mode = 'solid'; }
                }
            }
        }
    }

    // Drop every history entry that pre-dates the loaded popup and start from the real state
    function resetBaseline() {
        var c = core();
        if (!c || !c.state) { return; }
        fixMeta(c.state.canvasMeta);
        c.state.history = [];
        c.state.historyIndex = -1;
        if (typeof c.pushHistory === 'function') { c.pushHistory(); }
        if (typeof c.updateHistoryButtons === 'function') { c.updateHistoryButtons(); }
    }

    function wrapRestore() {
        var c = core();
        if (!c || c._historyFixWrapped || typeof c.restoreSnapshot !== 'function') { return; }
        c._historyFixWrapped = true;
        var orig = c.restoreSnapshot;
        c.restoreSnapshot = function (snapshotStr) {
            var str = snapshotStr;
            try {
                var parsed = JSON.parse(snapshotStr);
                fixMeta(parsed && parsed.canvasMeta);
                str = JSON.stringify(parsed);
            } catch (e) {}
            var out = orig.call(this, str);
            var S = window.WpPopPopBuilderSettings;
            if (S) {
                try {
                    if (typeof S.applyActiveCanvasBackground === 'function') { S.applyActiveCanvasBackground(); }
                    if (typeof S.syncCurrentCanvasSettings === 'function') { S.syncCurrentCanvasSettings(); }
                } catch (e2) {}
            }
            if (typeof this.updateHistoryButtons === 'function') { this.updateHistoryButtons(); }
            return out;
        };
    }

    function wrapLoad() {
        var io = window.WpPopPopBuilderIO;
        if (!io || io._historyFixWrapped || typeof io.loadInitialData !== 'function') { return; }
        io._historyFixWrapped = true;
        var orig = io.loadInitialData;
        io.loadInitialData = function () {
            var hasUid = false;
            try { hasUid = !!this.getUid(); } catch (e) {}
            var out = orig.apply(this, arguments);
            if (!hasUid) { resetBaseline(); } // new popup: defaults are set synchronously
            return out;
        };
    }

    wrapRestore();
    wrapLoad();

    // Saved popup: baseline is reset right after its AJAX success handler has populated the state
    $(document).on('ajaxComplete.wppoppopHistoryFix', function (e, xhr, opts) {
        if (opts && typeof opts.data === 'string' && opts.data.indexOf('wppoppop_load_popup') !== -1) {
            setTimeout(resetBaseline, 0);
        }
    });
})(window, jQuery);
