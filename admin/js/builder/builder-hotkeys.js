/**
 * WpPopPop Visual Builder: Keyboard History & Delete Hotkeys
 * Cmd/Ctrl+Z = undo 1 step, Shift+Cmd/Ctrl+Z (or Ctrl+Y) = redo 1 step, Delete/Backspace = delete selected layer.
 * Capture-phase listener so the legacy core handler never double-fires.
 */
(function(window, $) {
    'use strict';
    if (window.__wppoppopHotkeysBound) return;
    window.__wppoppopHotkeysBound = true;

    function core() { return window.WpPopPopBuilderCore; }

    function isTyping(t) {
        if (!t) return false;
        var tag = (t.tagName || '').toLowerCase();
        if (tag === 'input' || tag === 'textarea' || tag === 'select' || t.isContentEditable) return true;
        return !!(t.closest && t.closest('.note-editor, [contenteditable="true"]'));
    }

    function clean(id) {
        return String(id == null ? '' : id).replace(/^wppoppop-el-/, '').replace(/^el-/, '');
    }

    function activeList() {
        var c = core();
        if (!c || !c.state) return [];
        var cur = c.state.currentCanvas || 1;
        return (c.state.canvases && c.state.canvases[cur]) || [];
    }

    function findEl(id) {
        var cid = clean(id), list = activeList();
        for (var i = 0; i < list.length; i++) {
            if (clean(list[i].id) === cid) return list[i];
        }
        return null;
    }

    function snapshot() {
        var c = core();
        return JSON.stringify({ canvases: c.state.canvases, canvasMeta: c.state.canvasMeta });
    }

    // Record any un-pushed edits so undo steps back exactly one change
    function flushPending() {
        var c = core();
        var s = c.state;
        if (s.history.length && s.history[s.historyIndex] !== snapshot()) {
            c.pushHistory();
        }
    }

    function reselect(id) {
        if (!id) return;
        var el = findEl(id);
        var cv = window.WpPopPopBuilderCanvas;
        if (el && cv && typeof cv.selectElement === 'function') cv.selectElement(el.id);
    }

    function doUndo() {
        var c = core();
        if (!c || !c.state || typeof c.undo !== 'function') return;
        var prev = c.state.activeId;
        flushPending();
        if (c.state.historyIndex > 0) {
            c.undo();
            reselect(prev);
        }
    }

    function doRedo() {
        var c = core();
        if (!c || !c.state || typeof c.redo !== 'function') return;
        var prev = c.state.activeId;
        if (c.state.historyIndex < c.state.history.length - 1) {
            c.redo();
            reselect(prev);
        }
    }

    function deleteActive() {
        var c = core();
        if (!c || !c.state || !c.state.activeId) return false;
        var el = findEl(c.state.activeId);
        if (!el || el.locked) return false;
        var L = window.WpPopPopBuilderLayers;
        if (L && typeof L.deleteLayer === 'function') {
            L.deleteLayer(el.id);
        } else {
            var list = activeList();
            list.splice(list.indexOf(el), 1);
            if (window.WpPopPopBuilderCanvas) {
                if (typeof window.WpPopPopBuilderCanvas.deselect === 'function') window.WpPopPopBuilderCanvas.deselect();
                window.WpPopPopBuilderCanvas.renderCanvas();
            }
            if (L && typeof L.renderLayers === 'function') L.renderLayers();
            c.pushHistory();
        }
        return true;
    }

    document.addEventListener('keydown', function(e) {
        if (!document.getElementById('wppoppop-canvas-box')) return;
        if (window.WpPopPopBuilderPreview && window.WpPopPopBuilderPreview.isOpen) return;
        if (isTyping(e.target)) return;

        var mod = e.metaKey || e.ctrlKey;
        var k = String(e.key || '').toLowerCase();

        if (mod && !e.altKey && (e.code === 'KeyZ' || k === 'z')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (e.shiftKey) { doRedo(); } else { doUndo(); }
            return;
        }
        if (mod && !e.shiftKey && !e.altKey && (e.code === 'KeyY' || k === 'y')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            doRedo();
            return;
        }
        if (!mod && !e.altKey && (e.key === 'Delete' || e.key === 'Backspace' || e.keyCode === 46 || e.keyCode === 8)) {
            if (deleteActive()) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }
    }, true);

    window.WpPopPopBuilderHotkeys = { undo: doUndo, redo: doRedo, deleteActive: deleteActive };
})(window, jQuery);
