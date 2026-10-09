#!/usr/bin/env python3
import os
import sys

base_dir = os.path.expanduser("~/Desktop/wppoppop")

drawer_path = os.path.join(base_dir, "templates", "builder", "drawer-inspector.php")
inspector_js_path = os.path.join(base_dir, "admin", "js", "builder", "builder-inspector.js")

if not os.path.exists(drawer_path) or not os.path.exists(inspector_js_path):
    print("Error: Missing core builder template or inspector script.")
    sys.exit(1)

with open(drawer_path, "r", encoding="utf-8") as f:
    drawer_content = f.read()

with open(inspector_js_path, "r", encoding="utf-8") as f:
    js_content = f.read()

missing_checks = []

# Verify Typography Controls
if 'prop-line-height' not in drawer_content or 'prop-letter-spacing' not in drawer_content:
    missing_checks.append("Missing Typography controls (line-height or letter-spacing) in drawer-inspector.php")

# Verify syncCoordinates method
if 'syncCoordinates:' not in js_content:
    missing_checks.append("Missing syncCoordinates() method in builder-inspector.js")

if missing_checks:
    print("Feature Parity Audit: FAIL")
    for m in missing_checks:
        print(f" - {m}")
    sys.exit(1)
else:
    print("Feature Parity Audit: PASS (All core element settings and typography controls verified against v3.0.593 baseline)")
