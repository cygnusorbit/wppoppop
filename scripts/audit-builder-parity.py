#!/usr/bin/env python3
"""
WpPopPop Builder Parity & 6-Tier Architecture Sentinel
Audits the complete Parity Macro Systems across ~/Desktop/wppoppop.
"""
import os
import sys

# --- Decomposed Partial Aggregator Hook ---
_orig_open_wppoppop = open
def open(file, mode='r', *args, **kwargs):
    f_obj = _orig_open_wppoppop(file, mode, *args, **kwargs)
    if 'r' in mode and isinstance(file, str):
        norm_f = file.replace('\\', '/')
        if norm_f.endswith('templates/builder/drawer-settings.php'):
            import glob as _glob, os as _os, io as _io
            base = f_obj.read()
            f_obj.close()
            s_dir = _os.path.join(_os.path.dirname(file), 'settings')
            for _af in sorted(_glob.glob(_os.path.join(s_dir, 'acc-*.php'))):
                with _orig_open_wppoppop(_af, 'r', encoding='utf-8', errors='ignore') as _sub:
                    base += '\n' + _sub.read()
            return _io.StringIO(base)
        elif norm_f.endswith('templates/builder/drawer-inspector.php'):
            import glob as _glob, os as _os, io as _io
            base = f_obj.read()
            f_obj.close()
            e_dir = _os.path.join(_os.path.dirname(file), 'elements')
            for _ef in sorted(_glob.glob(_os.path.join(e_dir, '*.php'))):
                with _orig_open_wppoppop(_ef, 'r', encoding='utf-8', errors='ignore') as _sub:
                    base += '\n' + _sub.read()
            return _io.StringIO(base)
    return f_obj


base_dir = os.path.expanduser('~/Desktop/wppoppop')
errors = []

def check_file(rel_path, required_strings, desc):
    path = os.path.join(base_dir, rel_path)
    if not os.path.isfile(path):
        errors.append(f"Missing required file: {rel_path} ({desc})")
        return
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    for s in required_strings:
        if s not in content:
            errors.append(f"In {rel_path}: Missing expected token '{s}' ({desc})")

print("🔍 Auditing Macro Parity Systems in WpPopPop...")

# Phase 1: Header Workflow & Canvas Sequence
check_file('templates/builder/header.php', ['wppoppop-zoom-select', 'wppoppop-canvas-duplicate-btn', 'wppoppop-btn-theme-toggle', 'wppoppop-snap-object'], 'Phase 1 Header Zoom, Duplicate, Theme & Snap (Undo/Redo buttons intentionally removed)')
check_file('admin/js/builder/builder-core.js', ['duplicateActiveCanvas', 'updateHistoryButtons', 'bindKeyboardShortcuts'], 'Phase 1 History State & Shortcuts')

# WPPOPPOP_NO_UNDO_BUTTONS_AUDIT: toolbar undo/redo buttons were removed on purpose
_hdr_path = os.path.join(base_dir, 'templates/builder/header.php')
if os.path.isfile(_hdr_path):
    with open(_hdr_path, 'r', encoding='utf-8') as _hf:
        _hdr = _hf.read()
    for _banned in ['wppoppop-btn-undo', 'wppoppop-btn-redo', 'Canvas:</span>']:
        if _banned in _hdr:
            errors.append("In templates/builder/header.php: Removed element '" + _banned + "' must not be present")
check_file('admin/js/builder/builder-core.js', ['undo: function', 'redo: function', "e.key === 'z'"], 'Keyboard undo/redo remains available')
check_file('admin/js/builder/builder-canvas.js', ['setZoom', 'bindZoomControls'], 'Phase 1 Viewport Zoom Engine')

# Phase 2: Inspector Advanced Styling & States
check_file('templates/builder/drawer-inspector.php', ['prop-opacity', 'prop-btn-hover-section', 'prop-custom-class', 'prop-custom-id'], 'Phase 2 Inspector Controls')
check_file('admin/js/builder/builder-inspector.js', ['prop-opacity', 'prop-btn-hover-bg', 'prop-custom-class'], 'Phase 2 Inspector State Hydration')
check_file('includes/ajax/class-ajax-builder.php', ['opacity', 'hoverBgColor', 'customClass'], 'Phase 2 Backend Whitelist')

# Phase 3: Stage Physics & Alignment Engine
check_file('templates/builder/canvas.php', ['wppoppop-alignment-toolbar', 'wppoppop-guide-v', 'wppoppop-guide-h'], 'Phase 3 Alignment Stage Markup')
check_file('admin/js/builder/builder-canvas.js', ['alignSelectedElements', 'smartSnapGuides', 'bindKeyboardNudge'], 'Phase 3 Stage Physics & Alignment')

# Phase 4: Campaign Settings & Triggers
check_file('templates/builder/drawer-settings.php', ['set-exit-intent-sensitivity', 'set-trigger-scroll-depth', 'set-canvas-autoclose'], 'Phase 4 Settings Triggers Markup')
check_file('admin/js/builder/builder-settings.js', ['set-exit-intent-sensitivity', 'set-trigger-scroll-depth', 'set-canvas-autoclose'], 'Phase 4 Settings Hydration')

# Phase 5: Floating Layers Panel Enhancements
check_file('templates/builder/layers-panel.php', ['wppoppop-floating-layers-panel', 'wppoppop-layers-list'], 'Phase 5 Layers Panel Markup')
check_file('admin/js/builder/builder-layers.js', ['bindInlineRename', 'renderLayers'], 'Phase 5 Layers Panel Logic')

# Phase 6: Lifecycle Flush & IO Persistence
check_file('admin/js/builder/builder-io.js', ['flushActiveElement', 'exportConfig'], 'Phase 6 Pre-Save DOM Flush')

if errors:
    print(f"❌ Parity Audit FAILED ({len(errors)} issues detected):")
    for err in errors:
        print(f"  • {err}")
    sys.exit(1)
else:
    print("✅ Parity Audit PASSED: Zero regressions detected.")
    print("   All 6 Macro Builder Systems are fully compliant with parity.")
    sys.exit(0)
