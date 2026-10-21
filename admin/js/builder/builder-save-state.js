/**
 * WpPopPop Visual Builder: Save / Update button state
 * Clean saved popup -> "Save" (blue). Any change to a saved popup -> "Update" (orange).
 * Compares a live snapshot (canvases, canvas meta, title, settings) against the last saved/loaded baseline.
 */
(function(window, $) {
    'use strict';
    if (window.__wppoppopSaveStateBound) return;
    window.__wppoppopSaveStateBound = true;

    var BTN = '#wppoppop-btn-save';
    var baseline = null, pending = null, busy = false;

    function snap() {
        try {
            var c = window.WpPopPopBuilderCore;
            if (!c || !c.state) return null;
            var o = { c: c.state.canvases, m: c.state.canvasMeta, t: $('#wppoppop-builder-title').val() || '' };
            var S = window.WpPopPopBuilderSettings;
            if (S && typeof S.getSettings === 'function') { try { o.s = S.getSettings(); } catch (e) {} }
            return JSON.stringify(o);
        } catch (e) { return null; }
    }

    function hasUid() {
        var io = window.WpPopPopBuilderIO;
        var u = (io && typeof io.getUid === 'function') ? io.getUid() : $('#wppoppop-builder-uid').val();
        return !!u;
    }

    function paint(dirty) {
        var $b = $(BTN);
        if (!$b.length || busy || $b.prop('disabled')) return;
        $b.toggleClass('is-dirty', dirty);
        var want = dirty ? 'Update' : 'Save';
        if ($.trim($b.text()) !== want) { $b.text(want); }
    }

    function check() {
        if (busy || baseline === null) return;
        var now = snap();
        if (now === null) return;
        paint(hasUid() && now !== baseline);
    }

    function rebase() { baseline = snap(); check(); }

    function isAction(settings, name) {
        return settings && typeof settings.data === 'string' && settings.data.indexOf(name) !== -1;
    }

    $(document).ajaxSend(function(e, xhr, settings) {
        if (isAction(settings, 'wppoppop_save_popup')) { busy = true; pending = snap(); }
    });

    $(document).ajaxSuccess(function(e, xhr, settings, data) {
        if (isAction(settings, 'wppoppop_save_popup')) {
            var ok = !!(data && data.success);
            if (ok) { baseline = pending; }
            // builder-io restores the button text 1500ms after a successful save
            setTimeout(function() { busy = false; $(BTN).removeClass('is-dirty'); check(); if (ok) paint(false); }, ok ? 1550 : 50);
        } else if (isAction(settings, 'wppoppop_load_popup')) {
            setTimeout(rebase, 900);
        }
    });

    $(document).ajaxError(function(e, xhr, settings) {
        if (isAction(settings, 'wppoppop_save_popup')) { setTimeout(function() { busy = false; check(); }, 50); }
    });

    $(function() {
        if (!hasUid()) { setTimeout(rebase, 1200); }
        setTimeout(function() { if (baseline === null) rebase(); }, 4000); // load-failure fallback
        setInterval(function() { if (!document.hidden) check(); }, 700);
        $(document).on('input change keyup', '#wppoppop-builder-title, #wppoppop-inspector-drawer, #wppoppop-settings-drawer', function() {
            setTimeout(check, 30);
        });
    });
})(window, jQuery);
