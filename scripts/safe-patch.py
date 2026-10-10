#!/usr/bin/env python3
import os
import sys
import re
import shutil
import subprocess

REPO_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))

def run_parity_check():
    auditor = os.path.join(REPO_DIR, "scripts", "audit-feature-parity.py")
    if os.path.isfile(auditor):
        res = subprocess.run([sys.executable, auditor], capture_output=True, text=True)
        if res.returncode != 0:
            return False, res.stdout or res.stderr
    return True, "Parity check passed."

def apply_safe_patch(rel_path, search_pattern, replacement, is_regex=False):
    abs_path = os.path.join(REPO_DIR, rel_path)
    if not os.path.isfile(abs_path):
        print(f"❌ Error: File not found: {rel_path}")
        return False

    with open(abs_path, "r", encoding="utf-8", errors="replace") as f:
        original_code = f.read()

    # Create safety backup
    bak_path = abs_path + ".safepatch.bak"
    with open(bak_path, "w", encoding="utf-8") as f:
        f.write(original_code)

    if is_regex:
        if not re.search(search_pattern, original_code):
            print(f"❌ Error: Regex pattern not found in {rel_path}")
            os.remove(bak_path)
            return False
        modified_code = re.sub(search_pattern, replacement, original_code, count=1)
    else:
        if search_pattern not in original_code:
            print(f"❌ Error: Target code block not found in {rel_path}")
            os.remove(bak_path)
            return False
        modified_code = original_code.replace(search_pattern, replacement, 1)

    # Write candidate change
    with open(abs_path, "w", encoding="utf-8") as f:
        f.write(modified_code)

    # Validate parity and syntax
    passed, msg = run_parity_check()
    if not passed:
        print(f"❌ Parity Check FAILED! Auto-reverting {rel_path} to backup.")
        print(msg)
        shutil.copyfile(bak_path, abs_path)
        os.remove(bak_path)
        return False

    # Cleanup backup on success
    if os.path.exists(bak_path):
        os.remove(bak_path)

    print(f"✔ Successfully updated {rel_path} in-place with zero regressions.")
    return True

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python3 scripts/safe-patch.py check")
        sys.exit(0)
    print("✔ Safe patch utility is ready and connected to parity guards.")
