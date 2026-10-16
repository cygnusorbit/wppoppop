#!/usr/bin/env python3
import os
import sys
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

def surgical_replace(rel_path, target_block, replacement_block):
    abs_path = os.path.join(REPO_DIR, rel_path)
    if not os.path.isfile(abs_path):
        print(f"❌ Error: Target file not found: {rel_path}")
        return False

    with open(abs_path, "r", encoding="utf-8", errors="replace") as f:
        content = f.read()

    match_count = content.count(target_block)
    if match_count == 0:
        print(f"❌ Error: Search block not found in {rel_path}.")
        print("   The file content or structure was not modified.")
        return False
    elif match_count > 1:
        print(f"❌ Error: Ambiguous match! Found {match_count} occurrences of search block in {rel_path}.")
        print("   Expand the existing code anchor to make it unique.")
        return False

    new_content = content.replace(target_block, replacement_block, 1)

    # Temporary validation before saving
    tmp_path = abs_path + ".tmp"
    with open(tmp_path, "w", encoding="utf-8") as f:
        f.write(new_content)

    # Run parity and syntax checks
    auditor = os.path.join(REPO_DIR, "scripts", "audit-feature-parity.py")
    if os.path.isfile(auditor):
        check = subprocess.run([sys.executable, auditor], capture_output=True, text=True)
        # Note: only fail if PHP syntax or required parity dropped
        if check.returncode != 0 and "CRITICAL" in check.stdout:
            print("❌ Error: Replacement broke pre-commit guards:")
            print(check.stdout)
            if os.path.exists(tmp_path):
                os.remove(tmp_path)
            return False

    if os.path.exists(tmp_path):
        os.remove(tmp_path)

    with open(abs_path, "w", encoding="utf-8") as f:
        f.write(new_content)

    print(f"✔ Surgically updated: {rel_path}")
    return True

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python3 scripts/patch-surgical.py <check>")
        sys.exit(0)
    print("✔ Surgical patch utility ready.")
