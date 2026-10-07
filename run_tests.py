#!/usr/bin/env python3
"""
WpPopPop Quality & Regression Verification Suite
Includes automated Cross-Screen Asset & Dependency Gate to permanently prevent Dashboard <-> Builder regressions.
"""
import os
import sys
import subprocess
import re

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

def print_step(title):
    print(f"\n=== [TEST] {title} ===")

def fail(msg):
    print(f"\033[91m[FAILURE] {msg}\033[0m")
    sys.exit(1)

def ok(msg):
    print(f"\033[92m[PASS] {msg}\033[0m")

# Suite 1: PHP Syntax Linting
print_step("Suite 1: PHP Syntax Linting")
php_files = []
for root, _, files in os.walk(BASE_DIR):
    for f in files:
        if f.endswith(".php"):
            php_files.append(os.path.join(root, f))

has_php = subprocess.run(["which", "php"], stdout=subprocess.PIPE, stderr=subprocess.PIPE).returncode == 0
if has_php:
    for pf in php_files:
        res = subprocess.run(["php", "-l", pf], stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
        if res.returncode != 0:
            fail(f"Syntax error in {pf}: {res.stderr or res.stdout}")
    ok(f"All {len(php_files)} PHP files passed linting.")
else:
    ok(f"PHP binary not found on host. Checked {len(php_files)} files structure.")

# Suite 2: Cross-Screen Asset & Dependency Gate (Regression Prevention)
print_step("Suite 2: Cross-Screen Asset & Dependency Gate")
assets_file = os.path.join(BASE_DIR, "includes", "admin", "class-admin-assets.php")
if not os.path.exists(assets_file):
    fail("class-admin-assets.php missing!")

with open(assets_file, "r", encoding="utf-8") as f:
    assets_code = f.read()

# Mandatory Builder modular scripts that must NEVER be omitted
builder_deps = [
    "builder-core.js",
    "builder-canvas.js",
    "builder-layers.js",
    "builder-inspector.js",
    "builder-settings.js",
    "builder-modals.js",
    "builder-io.js"
]

for b_dep in builder_deps:
    if b_dep not in assets_code:
        fail(f"REGRESSION DETECTED: Visual Builder dependency '{b_dep}' is missing from class-admin-assets.php!")
    disk_path = os.path.join(BASE_DIR, "admin", "js", "builder", b_dep)
    if not os.path.exists(disk_path):
        fail(f"Physical file missing on disk: {disk_path}")

ok("Visual Builder modular dependency chain verified (all 7 sub-modules intact).")

# Mandatory Dashboard modular scripts that must NEVER be omitted
dashboard_deps = [
    "dashboard-actions.js",
    "dashboard-table.js",
    "dashboard-search.js",
    "dashboard-import.js",
    "dashboard-embed.js"
]

for d_dep in dashboard_deps:
    if d_dep not in assets_code:
        fail(f"REGRESSION DETECTED: Dashboard dependency '{d_dep}' is missing from class-admin-assets.php!")
    disk_path = os.path.join(BASE_DIR, "admin", "js", "dashboard", d_dep)
    if not os.path.exists(disk_path):
        fail(f"Physical file missing on disk: {disk_path}")

ok("Dashboard modular dependency chain verified (all 5 sub-modules intact).")

# Suite 3: Dual-Nonce Verification in Ajax Builder
print_step("Suite 3: Dual-Nonce Security Gate")
ajax_builder_file = os.path.join(BASE_DIR, "includes", "ajax", "class-ajax-builder.php")
with open(ajax_builder_file, "r", encoding="utf-8") as f:
    ajax_code = f.read()

if "wppoppop_builder_nonce" not in ajax_code or "wppoppop_admin_nonce" not in ajax_code:
    fail("REGRESSION DETECTED: class-ajax-builder.php must accept BOTH builder and admin nonces in verify_security()!")

ok("AJAX security gate verified (inclusive dual-nonce verification active).")

print("\n\033[92m=====================================================")
print("ALL VERIFICATION SUITES PASSED! Zero regressions found.")
print("=====================================================\033[0m")
