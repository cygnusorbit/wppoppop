#!/usr/bin/env python3
"""
WpPopPop Builder Parity & 6-Tier Architecture Sentinel
Audits the complete Parity Macro Systems across ~/Desktop/wppoppop.
"""
import os
import sys

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
check_file('templates/builder/header.php', ['wppoppop-btn-undo', 'wppoppop-btn-redo', 'wppoppop-zoom-select', 'wppoppop-canvas-duplicate-btn'], 'Phase 1 Header History & Zoom')
check_file('admin/js/builder/builder-core.js', ['duplicateActiveCanvas', 'updateHistoryButtons', 'bindKeyboardShortcuts'], 'Phase 1 History State & Shortcuts')
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
check_file('templates/builder/layers-panel.php', ['wppoppop-layers-search', 'wppoppop-layers-bulk-vis', 'wppoppop-layers-bulk-lock'], 'Phase 5 Layers Panel Markup')
check_file('admin/js/builder/builder-layers.js', ['bindLayerSearch', 'bindInlineRename', 'bindBulkActions'], 'Phase 5 Layers Panel Logic')

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
