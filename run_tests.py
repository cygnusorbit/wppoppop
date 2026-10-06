#!/usr/bin/env python3
import os
import sys
import re
import subprocess

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

GREEN = '\033[92m'
RED = '\033[91m'
YELLOW = '\033[93m'
CYAN = '\033[96m'
BOLD = '\033[1m'
RESET = '\033[0m'

passes = 0
failures = 0

def log_pass(msg):
    global passes
    passes += 1
    print(f"  {GREEN}✔ PASS:{RESET} {msg}")

def log_fail(msg):
    global failures
    failures += 1
    print(f"  {RED}✖ FAIL:{RESET} {msg}")

def log_section(title):
    print(f"\n{BOLD}{CYAN}=== {title} ==={RESET}")

# -----------------------------------------------------------------------------
# SUITE 1: PHP Syntax & Linting Verification
# -----------------------------------------------------------------------------
log_section("Suite 1: PHP Syntax Linting (php -l)")
php_files = []
for root, _, files in os.walk(BASE_DIR):
    for f in files:
        if f.endswith(".php"):
            php_files.append(os.path.join(root, f))

for php_file in sorted(php_files):
    rel_path = os.path.relpath(php_file, BASE_DIR)
    res = subprocess.run(["php", "-l", php_file], stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
    if res.returncode == 0:
        log_pass(f"Syntax valid: {rel_path}")
    else:
        err_msg = res.stderr.strip() or res.stdout.strip()
        log_fail(f"Syntax error in {rel_path}: {err_msg}")

# -----------------------------------------------------------------------------
# SUITE 2: Stylesheet @import Target Resolution
# -----------------------------------------------------------------------------
log_section("Suite 2: Stylesheet @import Integrity")
css_files = []
for root, _, files in os.walk(BASE_DIR):
    for f in files:
        if f.endswith(".css"):
            css_files.append(os.path.join(root, f))

import_regex = re.compile(r'@import\s+url\([\"\']?([^\"\')]+)[\"\']?\);')

for css_file in sorted(css_files):
    rel_css = os.path.relpath(css_file, BASE_DIR)
    with open(css_file, "r", encoding="utf-8", errors="ignore") as f:
        content = f.read()

    matches = import_regex.findall(content)
    for import_target in matches:
        target_abs = os.path.normpath(os.path.join(os.path.dirname(css_file), import_target))
        if os.path.exists(target_abs):
            log_pass(f"{rel_css} -> resolved @import: {import_target}")
        else:
            log_fail(f"{rel_css} -> missing @import target: {import_target}")

# -----------------------------------------------------------------------------
# SUITE 3: Dynamic Class Autoloader Mapping
# -----------------------------------------------------------------------------
log_section("Suite 3: Dynamic Autoloader Mapping Verification")
class_decl_regex = re.compile(r'class\s+(WpPopPop_[A-Za-z0-9_]+)')

def autoloader_expected_path(class_name):
    relative = class_name.replace("WpPopPop_", "")
    if relative.startswith("Ajax_"):
        slug = relative[5:].replace("_", "-").lower()
        return os.path.join(BASE_DIR, "includes", "ajax", f"class-ajax-{slug}.php")
    elif relative.startswith("Admin_"):
        slug = relative[6:].replace("_", "-").lower()
        return os.path.join(BASE_DIR, "includes", "admin", f"class-admin-{slug}.php")
    elif relative.startswith("Front_"):
        slug = relative[6:].replace("_", "-").lower()
        return os.path.join(BASE_DIR, "includes", "front", f"class-front-{slug}.php")
    elif relative.startswith("Addon_"):
        slug = relative[6:].replace("_", "-").lower()
        return os.path.join(BASE_DIR, "includes", "addons", f"class-addon-{slug}.php")
    elif relative.startswith("Rest_"):
        slug = relative[5:].replace("_", "-").lower()
        return os.path.join(BASE_DIR, "includes", "rest", f"class-rest-{slug}.php")
    elif relative.startswith("Widget_"):
        slug = relative[7:].replace("_", "-").lower()
        return os.path.join(BASE_DIR, "includes", "widget", f"class-widget-{slug}.php")
    else:
        slug = relative.replace("_", "-").lower()
        return os.path.join(BASE_DIR, "includes", f"class-wppoppop-{slug}.php")

for php_file in php_files:
    with open(php_file, "r", encoding="utf-8", errors="ignore") as f:
        code = f.read()
    classes = class_decl_regex.findall(code)
    for cname in classes:
        expected = autoloader_expected_path(cname)
        if os.path.exists(expected):
            log_pass(f"Autoload path exists for {cname}")
        else:
            log_fail(f"Autoload target missing for {cname} -> expected: {os.path.relpath(expected, BASE_DIR)}")

# -----------------------------------------------------------------------------
# SUITE 4: All 19 Canvas Elements Verification
# -----------------------------------------------------------------------------
log_section("Suite 4: 19 Canvas Elements Factory Integrity")
ribbon_file = os.path.join(BASE_DIR, "templates", "builder", "ribbon.php")
elements_to_verify = [
    "text", "email", "number", "select", "radios", "checkboxes",
    "rating", "date", "slider", "signature", "wheel", "scratch",
    "countdown", "progress", "file", "step_btn", "submit", "pay", "html"
]

if os.path.exists(ribbon_file):
    with open(ribbon_file, "r", encoding="utf-8") as f:
        ribbon_content = f.read()
    for el in elements_to_verify:
        if f'data-type="{el}"' in ribbon_content:
            log_pass(f"Element layer verified in ribbon: [{el}]")
        else:
            log_fail(f"Element layer missing in ribbon: [{el}]")
else:
    log_fail("templates/builder/ribbon.php not found!")

# -----------------------------------------------------------------------------
# SUITE 5: 15-Accordion Settings Drawer Verification
# -----------------------------------------------------------------------------
log_section("Suite 5: 15 Campaign Settings Accordions Integrity")
settings_drawer_file = os.path.join(BASE_DIR, "templates", "builder", "drawer-settings.php")
accordions_to_verify = [
    "1. Box & Backdrop Styling",
    "2. Display Triggers",
    "3. Conditional Logic & Math",
    "4. Sticky Side Tabs",
    "5. Payments & Checkout",
    "6. Secure Downloads",
    "7. Video Playback Listeners",
    "8. Subscriber Autoresponder",
    "9. Marketing & Webhooks",
    "10. Twilio SMS Alerts",
    "11. Targeting & Attribution",
    "12. Frequency Capping & Cookies",
    "13. WooCommerce Conversion Suite",
    "14. Custom Scoped CSS & JS",
    "15. Quiz & Lead Scoring"
]

if os.path.exists(settings_drawer_file):
    with open(settings_drawer_file, "r", encoding="utf-8") as f:
        drawer_content = f.read()
    for acc in accordions_to_verify:
        if acc in drawer_content:
            log_pass(f"Settings accordion verified: [{acc}]")
        else:
            log_fail(f"Settings accordion missing: [{acc}]")
else:
    log_fail("templates/builder/drawer-settings.php not found!")

# -----------------------------------------------------------------------------
# FINAL SUMMARY REPORT
# -----------------------------------------------------------------------------
print(f"\n{BOLD}========================================{RESET}")
print(f"{BOLD}TOTAL TESTS:{RESET} {passes + failures}")
print(f"{GREEN}{BOLD}PASSED:{RESET}      {passes}")
print(f"{RED}{BOLD}FAILED:{RESET}      {failures}")
print(f"{BOLD}========================================{RESET}")

if failures > 0:
    sys.exit(1)
else:
    print(f"\n{GREEN}{BOLD}All WpPopPop system verification tests passed successfully!{RESET}\n")
    sys.exit(0)
