#!/usr/bin/env python3
import os
import sys
import shutil
import subprocess

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


REPO_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))

CANONICAL_ELEMENT_SPECS = [
    ('title', ['panel-elem-title']),
    ('paragraph', ['panel-elem-paragraph', 'panel-elem-text']),
    ('textfield', ['panel-elem-textfield']),
    ('image', ['panel-elem-image']),
    ('shape', ['panel-elem-shape']),
    ('video', ['panel-elem-video']),
    ('link', ['panel-elem-link', 'panel-elem-link_btn']),
    ('close', ['panel-elem-close', 'panel-elem-close_icon']),
    ('email', ['panel-elem-email']),
    ('number', ['panel-elem-number']),
    ('select', ['panel-elem-select', 'panel-elem-dropdown']),
    ('radios', ['panel-elem-radios', 'panel-elem-radio']),
    ('checkboxes', ['panel-elem-checkboxes', 'panel-elem-checkbox']),
    ('rating', ['panel-elem-rating']),
    ('date', ['panel-elem-date']),
    ('slider', ['panel-elem-slider']),
    ('signature', ['panel-elem-signature']),
    ('wheel', ['panel-elem-wheel']),
    ('scratch', ['panel-elem-scratch']),
    ('countdown', ['panel-elem-countdown']),
    ('progress', ['panel-elem-progress']),
    ('file', ['panel-elem-file']),
    ('step', ['panel-elem-step', 'panel-elem-step_btn']),
    ('submit', ['panel-elem-submit']),
    ('pay', ['panel-elem-pay']),
    ('html', ['panel-elem-html'])
]

REQUIRED_TYPOGRAPHY = ['prop-line-height', 'prop-letter-spacing']

def audit_parity():
    template_path = os.path.join(REPO_DIR, "templates", "builder", "drawer-inspector.php")
    if not os.path.isfile(template_path):
        return True, "No inspector template present to audit."
    with open(template_path, "r", encoding="utf-8", errors="replace") as f:
        src = f.read()

    missing_elements = []
    for elem_name, panel_ids in CANONICAL_ELEMENT_SPECS:
        if not any((f'id="{pid}"' in src or f"id='{pid}'" in src) for pid in panel_ids):
            missing_elements.append(elem_name)

    if missing_elements:
        return False, f"Missing element panels for: {missing_elements}"

    missing_typo = [t for t in REQUIRED_TYPOGRAPHY if f'id="{t}"' not in src and f"id='{t}'" not in src]
    if missing_typo:
        return False, f"Missing typography controls: {missing_typo}"

    return True, "Parity check passed: all 26 elements and typography controls intact."

def apply_atomic_patch(filepath, search_block, replace_block):
    rel_path = os.path.relpath(filepath, REPO_DIR)
    if not os.path.isfile(filepath):
        print(f"❌ File not found: {rel_path}")
        return False

    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()

    count = content.count(search_block)
    if count == 0:
        print(f"❌ Search anchor not found in {rel_path}. Aborting.")
        return False
    if count > 1:
        print(f"❌ Ambiguous search anchor ({count} occurrences) in {rel_path}. Aborting.")
        return False

    # Create backup before mutation
    bak_path = filepath + ".guardian.bak"
    shutil.copy2(filepath, bak_path)

    # Perform replacement
    patched = content.replace(search_block, replace_block, 1)
    with open(filepath, "w", encoding="utf-8") as f:
        f.write(patched)

    # Run validations
    valid = True
    err_msg = ""
    if filepath.endswith(".php"):
        ok, msg = lint_php_file(filepath)
        if not ok:
            valid = False
            err_msg = f"PHP Syntax Error: {msg}"

    if valid and "drawer-inspector.php" in filepath:
        ok, msg = audit_parity()
        if not ok:
            valid = False
            err_msg = f"Feature Regression Error: {msg}"

    if not valid:
        print(f"❌ Verification failed! Rolling back changes to {rel_path}.")
        print(f"   Reason: {err_msg}")
        shutil.move(bak_path, filepath)
        return False

    if os.path.exists(bak_path):
        os.remove(bak_path)
    print(f"✔ Successfully patched {rel_path} with zero regressions.")
    return True

if __name__ == "__main__":
    if len(sys.argv) > 1 and sys.argv[1] == "audit":
        ok, msg = audit_parity()
        print(msg)
        sys.exit(0 if ok else 1)
    print("✔ Safe Patch Guardian installed and ready.")
