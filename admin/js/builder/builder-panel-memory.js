/* WPPOPPOP_PANEL_MEMORY: keep Popup Settings / Layer Settings open after a browser refresh (per popup, per browser) */
(function (window, $) {
    'use strict';

    var KEY = 'wppoppop_builder_open_panel';
    var restored = false;

    function core() { return window.WpPopPopBuilderCore; }
    function uid() {
        var io = window.WpPopPopBuilderIO;
        try { return (io && typeof io.getUid === 'function') ? String(io.getUid() || '') : ''; } catch (e) { return ''; }
    }

    function read() {
        try {
            var raw = window.localStorage.getItem(KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) { return null; }
    }

    function write(v) {
        try {
            if (v) { window.localStorage.setItem(KEY, JSON.stringify(v)); }
            else { window.localStorage.removeItem(KEY); }
        } catch (e) {}
    }

    // Snapshot the real DOM state of both drawers
    function snapshot() {
        var c = core();
        var $insp = $('#wppoppop-inspector-drawer');
        var insp = window.WpPopPopBuilderInspector;
        var elId = (insp && insp.activeId) || (c && c.state && c.state.activeId) || '';
        if ($insp.length && $insp.hasClass('is-open') && elId) {
            return {
                uid: uid(),
                panel: 'inspector',
                canvas: (c && c.state && c.state.currentCanvas) || 1,
                el: String(elId),
                tab: $insp.find('.wppoppop-insp-tab.active').first().attr('data-tab') || ''
            };
        }
        if ($('#wppoppop-settings-drawer').hasClass('open')) {
            return { uid: uid(), panel: 'settings' };
        }
        return null;
    }

    function findElement(canvas, id) {
        var c = core();
        var list = (c && c.state && c.state.canvases && c.state.canvases[canvas]) || [];
        for (var i = 0; i < list.length; i++) {
            if (list[i] && String(list[i].id) === String(id)) { return list[i]; }
        }
        return null;
    }

    function restoreInspector(s) {
        var c = core();
        var insp = window.WpPopPopBuilderInspector;
        if (!c || !insp || typeof insp.open !== 'function') { return; }
        var canvas = parseInt(s.canvas, 10) || 1;
        if ((c.state.currentCanvas || 1) !== canvas && typeof c.switchCanvas === 'function') {
            c.switchCanvas(canvas);
        }
        if (!findElement(canvas, s.el)) { return; }

        c.state.activeId = s.el;
        $('.wppoppop-canvas-item').filter(function () {
            var raw = $(this).attr('data-id') || $(this).data('id') || '';
            return String(raw).replace(/^wppoppop-el-/, '').replace(/^el-/, '') === String(s.el);
        }).addClass('selected is-selected active');

        insp.open(s.el);

        if (s.tab) {
            setTimeout(function () {
                var $tab = $('#wppoppop-inspector-drawer .wppoppop-insp-tab[data-tab="' + s.tab + '"]').first();
                if ($tab.length && !$tab.hasClass('active')) { $tab.trigger('click'); }
            }, 350);
        }
    }

    function restore() {
        if (restored) { return; }
        restored = true;
        var s = read();
        if (!s || s.uid !== uid()) { return; }
        try {
            if (s.panel === 'settings') {
                var st = window.WpPopPopBuilderSettings;
                if (st && typeof st.openDrawer === 'function') { st.openDrawer(); }
            } else if (s.panel === 'inspector' && s.el) {
                restoreInspector(s);
            }
        } catch (e) {}
    }

    // Persist right before the page goes away (beforeunload for Chrome/Firefox/Edge, pagehide for Safari)
    function save() {
        if (!restored) { return; } // never overwrite the saved state before it was restored
        write(snapshot());
    }
    $(window).on('beforeunload.wppoppopPanelMem pagehide.wppoppopPanelMem', save);

    // Wait for the popup data (AJAX) so the canvases, elements and settings exist
    $(document).on('ajaxComplete.wppoppopPanelMem', function (e, xhr, opts) {
        if (restored || !opts || typeof opts.data !== 'string') { return; }
        if (opts.data.indexOf('wppoppop_load_popup') !== -1) {
            setTimeout(restore, 60);
        }
    });

    $(function () {
        if (!uid()) {
            setTimeout(restore, 400); // new popup: nothing to load
        } else {
            setTimeout(restore, 8000); // safety net if the load request never completes
        }
    });
})(window, jQuery);
