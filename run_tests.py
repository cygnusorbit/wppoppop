#!/usr/bin/env python3
import os
import sys
import re
import glob
import shutil
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
# PHP Runtime Auto-Discovery
# -----------------------------------------------------------------------------
def find_php_runtime():
    # 1. System PATH
    php_path = shutil.which("php")
    if php_path:
        return {"type": "host", "cmd": [php_path]}

    # 2. Common Homebrew / MAMP Paths on macOS
    candidates = [
        "/opt/homebrew/bin/php",
        "/usr/local/bin/php",
        "/opt/homebrew/opt/php/bin/php",
        "/opt/homebrew/opt/php@8.2/bin/php",
        "/opt/homebrew/opt/php@8.1/bin/php",
        "/opt/homebrew/opt/php@8.0/bin/php",
        "/opt/homebrew/opt/php@7.4/bin/php",
    ] + glob.glob("/Applications/MAMP/bin/php/php*/bin/php")

    for path in candidates:
        if os.path.isfile(path) and os.access(path, os.X_OK):
            return {"type": "host", "cmd": [path]}

    # 3. Check for Active Docker Container with PHP
    docker_bin = shutil.which("docker")
    if docker_bin:
        try:
            d_res = subprocess.run(
                [docker_bin, "ps", "--filter", "status=running", "--format", "{{.Names}}"],
                stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, timeout=3
            )
            if d_res.returncode == 0:
                containers = d_res.stdout.strip().splitlines()
                for c in containers:
                    c_check = subprocess.run(
                        [docker_bin, "exec", c, "which", "php"],
                        stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, timeout=3
                    )
                    if c_check.returncode == 0:
                        return {"type": "docker", "cmd": [docker_bin, "exec", "-i", c, "php"]}
        except Exception:
            pass

    return None

# -----------------------------------------------------------------------------
# Python-Native Standalone PHP Parser Fallback
# -----------------------------------------------------------------------------
def lint_php_python(filepath):
    try:
        with open(filepath, "r", encoding="utf-8", errors="replace") as f:
            content = f.read()
    except Exception as e:
        return False, f"Could not read file: {e}"

    if "<?php" not in content and "<?" not in content:
        return False, "Missing PHP opening tag (<?php)"

    # Check for illegal control characters (e.g. 0x0B vertical tabs)
    for idx, ch in enumerate(content):
        code = ord(ch)
        if code < 32 and ch not in ("\n", "\r", "\t"):
            line = content[:idx].count("\n") + 1
            return False, f"Illegal control character 0x{code:02X} at line {line}"

    # Bracket Balance Checker Inside PHP Blocks
    i = 0
    n = len(content)
    line = 1
    stack = []
    is_in_php = False
    in_single = False
    in_double = False
    in_line_comment = False
    in_block_comment = False

    while i < n:
        ch = content[i]
        if ch == "\n":
            line += 1
            if in_line_comment:
                in_line_comment = False
            i += 1
            continue

        if not is_in_php:
            if content[i:i+5] == "<?php":
                is_in_php = True
                i += 5
                continue
            elif content[i:i+3] == "<?=":
                is_in_php = True
                i += 3
                continue
            i += 1
            continue

        # Inside PHP block:
        if in_line_comment:
            i += 1
            continue

        if in_block_comment:
            if ch == "*" and i + 1 < n and content[i+1] == "/":
                in_block_comment = False
                i += 2
                continue
            i += 1
            continue

        if in_single:
            if ch == "\\":
                i += 2
                continue
            if ch == "'":
                in_single = False
            i += 1
            continue

        if in_double:
            if ch == "\\":
                i += 2
                continue
            if ch == '"':
                in_double = False
            i += 1
            continue

        # Exit PHP block
        if ch == "?" and i + 1 < n and content[i+1] == ">":
            is_in_php = False
            i += 2
            continue

        # Comments
        if ch == "/" and i + 1 < n:
            if content[i+1] == "/":
                in_line_comment = True
                i += 2
                continue
            elif content[i+1] == "*":
                in_block_comment = True
                i += 2
                continue
        elif ch == "#":
            in_line_comment = True
            i += 1
            continue

        # Strings
        if ch == "'":
            in_single = True
            i += 1
            continue
        elif ch == '"':
            in_double = True
            i += 1
            continue

        # Brackets
        if ch in "({[":
            stack.append((ch, line))
        elif ch in ")}]":
            if not stack:
                return False, f"Unexpected closing '{ch}' at line {line}"
            top_ch, top_line = stack.pop()
            expected = {"(": ")", "{": "}", "[": "]"}[top_ch]
            if ch != expected:
                return False, f"Mismatched bracket: opened '{top_ch}' at line {top_line}, closed with '{ch}' at line {line}"

        i += 1

    if in_single:
        return False, "Unclosed single-quote string at end of file"
    if in_double:
        return False, "Unclosed double-quote string at end of file"
    if in_block_comment:
        return False, "Unclosed block comment (/*) at end of file"
    if stack:
        unclosed, u_line = stack[-1]
        return False, f"Unclosed '{unclosed}' opened at line {u_line}"

    return True, "OK"

# -----------------------------------------------------------------------------
# SUITE 1: PHP Syntax & Linting Verification
# -----------------------------------------------------------------------------
log_section("Suite 1: PHP Syntax Linting")
php_runtime = find_php_runtime()

if php_runtime and php_runtime["type"] == "host":
    print(f"  {YELLOW}ℹ Using host PHP binary:{RESET} {php_runtime['cmd'][0]}")
elif php_runtime and php_runtime["type"] == "docker":
    print(f"  {YELLOW}ℹ Using Docker PHP runtime:{RESET} {' '.join(php_runtime['cmd'])}")
else:
    print(f"  {YELLOW}ℹ No PHP CLI found in PATH. Using embedded Python parser engine.{RESET}")

php_files = []
for root, _, files in os.walk(BASE_DIR):
    for f in files:
        if f.endswith(".php"):
            php_files.append(os.path.join(root, f))

for php_file in sorted(php_files):
    rel_path = os.path.relpath(php_file, BASE_DIR)
    if php_runtime:
        try:
            if php_runtime["type"] == "host":
                cmd = php_runtime["cmd"] + ["-l", php_file]
                res = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
            else:
                with open(php_file, "r", encoding="utf-8") as pf:
                    code_in = pf.read()
                cmd = php_runtime["cmd"] + ["-l"]
                res = subprocess.run(cmd, input=code_in, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)

            if res.returncode == 0:
                log_pass(f"Syntax valid: {rel_path}")
            else:
                err_msg = res.stderr.strip() or res.stdout.strip()
                log_fail(f"Syntax error in {rel_path}: {err_msg}")
        except Exception as e:
            # Fallback to python parser if subprocess fails
            valid, msg = lint_php_python(php_file)
            if valid:
                log_pass(f"Syntax valid (Python parser): {rel_path}")
            else:
                log_fail(f"Syntax error in {rel_path}: {msg}")
    else:
        valid, msg = lint_php_python(php_file)
        if valid:
            log_pass(f"Syntax valid (Python parser): {rel_path}")
        else:
            log_fail(f"Syntax error in {rel_path}: {msg}")

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
