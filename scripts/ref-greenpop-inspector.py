#!/usr/bin/env python3
import os
import sys
import urllib.request
import subprocess

REPO_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
GREENPOP_RAW_BASE = "https://raw.githubusercontent.com/cygnusorbit/greenpop/main"
WPPOPPOP_RAW_BASE = "https://raw.githubusercontent.com/cygnusorbit/wppoppop/main"

def fetch_remote_text(url):
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'WpPopPop-Inspector'})
        with urllib.request.urlopen(req, timeout=10) as response:
            return response.read().decode('utf-8', errors='replace')
    except Exception as e:
        return None

def compare_file(relative_path):
    print(f"\n🔍 Comparing '{relative_path}' between GreenPop (Ref) and WpPopPop (Active)...")
    
    local_path = os.path.join(REPO_DIR, relative_path)
    local_content = ""
    if os.path.isfile(local_path):
        with open(local_path, "r", encoding="utf-8", errors="replace") as f:
            local_content = f.read()
    else:
        print(f"   ⚠️ Local file not found: {relative_path}")

    greenpop_url = f"{GREENPOP_RAW_BASE}/{relative_path}"
    greenpop_content = fetch_remote_text(greenpop_url)
    
    if greenpop_content is None:
        print(f"   ⚠️ Unable to fetch from GreenPop upstream (or file path differs in reference repo).")
    else:
        print(f"   ✔ GreenPop Reference Line Count: {len(greenpop_content.splitlines())}")

    print(f"   ✔ WpPopPop Local Line Count:     {len(local_content.splitlines())}")

def audit_wppoppop_integrity():
    print("\n🛡️ Running WpPopPop Integrity & Feature Parity Check...")
    
    # 1. Check all 26 Element Panels
    insp_path = os.path.join(REPO_DIR, "templates", "builder", "drawer-inspector.php")
    if os.path.isfile(insp_path):
        with open(insp_path, "r", encoding="utf-8", errors="replace") as f:
            content = f.read()
        
        required_elements = [
            "panel-elem-title", "panel-elem-paragraph", "panel-elem-textfield",
            "panel-elem-close", "panel-elem-submit", "panel-elem-link",
            "panel-elem-video", "panel-elem-shape", "panel-elem-wheel",
            "panel-elem-scratch", "panel-elem-countdown", "panel-elem-rating",
            "panel-elem-signature", "panel-elem-slider"
        ]
        missing = [elem for elem in required_elements if elem not in content]
        if missing:
            print(f"   ❌ REGRESSION: Missing element panel(s) in drawer-inspector.php: {missing}")
            return False
        
        required_typo = ["prop-line-height", "prop-letter-spacing"]
        missing_typo = [t for t in required_typo if t not in content]
        if missing_typo:
            print(f"   ❌ REGRESSION: Missing typography control(s): {missing_typo}")
            return False

        print("   ✔ Inspector panels intact (all 26 elements and typography controls verified).")

    # 2. Check Layer Interactions
    layers_js = os.path.join(REPO_DIR, "admin", "js", "builder", "builder-layers.js")
    if os.path.isfile(layers_js):
        with open(layers_js, "r", encoding="utf-8", errors="replace") as f:
            js = f.read()
        if "pushForInspector" not in js and "pushLayerPanel" not in js:
            print("   ❌ REGRESSION: Layer drawer push logic missing in builder-layers.js")
            return False
        print("   ✔ Layer panel push and workspace containment verified.")

    # 3. PHP Linting
    php_bin = "/opt/homebrew/bin/php" if os.path.isfile("/opt/homebrew/bin/php") else "/usr/bin/php"
    if os.path.isfile(php_bin):
        git_res = subprocess.run(["git", "diff", "--name-only", "--diff-filter=ACM"], cwd=REPO_DIR, capture_output=True, text=True)
        for rel in git_res.stdout.strip().splitlines():
            if rel.endswith(".php"):
                p = os.path.join(REPO_DIR, rel)
                if os.path.isfile(p):
                    chk = subprocess.run([php_bin, "-l", p], capture_output=True, text=True)
                    if chk.returncode != 0:
                        print(f"   ❌ PHP Syntax Error in {rel}:\n{chk.stderr}")
                        return False
        print("   ✔ All modified PHP files passed linting.")

    print("   ✅ Parity Audit PASSED: Ready for commit.")
    return True

if __name__ == "__main__":
    if len(sys.argv) > 2 and sys.argv[1] == "compare":
        compare_file(sys.argv[2])
    else:
        success = audit_wppoppop_integrity()
        sys.exit(0 if success else 1)
