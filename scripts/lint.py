#!/usr/bin/env python3
import os
import sys
import glob
import shutil
import subprocess

def find_php_binary():
    # 1. Standard PATH
    p = shutil.which("php")
    if p:
        return {"type": "host", "bin": p}

    # 2. Standard macOS Homebrew / MAMP paths
    candidates = [
        "/opt/homebrew/bin/php",
        "/usr/local/bin/php",
        "/opt/homebrew/opt/php/bin/php",
        "/opt/homebrew/opt/php@8.3/bin/php",
        "/opt/homebrew/opt/php@8.2/bin/php",
        "/opt/homebrew/opt/php@8.1/bin/php",
        "/opt/homebrew/opt/php@8.0/bin/php",
        "/opt/homebrew/opt/php@7.4/bin/php"
    ] + glob.glob("/Applications/MAMP/bin/php/php*/bin/php")

    for c in candidates:
        if os.path.isfile(c) and os.access(c, os.X_OK):
            return {"type": "host", "bin": c}

    # 3. Running Docker container check
    d_bin = shutil.which("docker")
    if d_bin:
        try:
            ps_res = subprocess.run([d_bin, "ps", "--filter", "status=running", "--format", "{{.Names}}"], capture_output=True, text=True, timeout=2)
            if ps_res.returncode == 0:
                for container in ps_res.stdout.strip().splitlines():
                    if container:
                        chk = subprocess.run([d_bin, "exec", container, "which", "php"], capture_output=True, text=True, timeout=2)
                        if chk.returncode == 0:
                            return {"type": "docker", "bin": d_bin, "container": container}
        except Exception:
            pass

    return None

def fallback_lint(filepath):
    try:
        with open(filepath, "r", encoding="utf-8", errors="replace") as f:
            code = f.read()
    except Exception as e:
        return False, f"Could not read file: {e}"

    if "<?php" not in code and "<?" not in code:
        return False, "Missing PHP opening tag (<?php)"

    stack = []
    line = 1
    in_single = in_double = in_line = in_block = in_php = False
    i = 0
    n = len(code)

    while i < n:
        ch = code[i]
        if ch == "\n":
            line += 1
            in_line = False
            i += 1
            continue

        if not in_php:
            if code[i:i+5] == "<?php" or code[i:i+3] == "<?=":
                in_php = True
                i += 5 if code[i:i+5] == "<?php" else 3
                continue
            i += 1
            continue

        if in_line:
            i += 1
            continue

        if in_block:
            if ch == "*" and i + 1 < n and code[i+1] == "/":
                in_block = False
                i += 2
                continue
            i += 1
            continue

        if in_single:
            if ch == "\\" and i + 1 < n:
                i += 2
                continue
            if ch == "'":
                in_single = False
            i += 1
            continue

        if in_double:
            if ch == "\\" and i + 1 < n:
                i += 2
                continue
            if ch == '"':
                in_double = False
            i += 1
            continue

        if ch == "?" and i + 1 < n and code[i+1] == ">":
            in_php = False
            i += 2
            continue

        if ch == "/" and i + 1 < n:
            if code[i+1] == "/":
                in_line = True
                i += 2
                continue
            elif code[i+1] == "*":
                in_block = True
                i += 2
                continue
        elif ch == "#":
            in_line = True
            i += 1
            continue

        if ch == "'":
            in_single = True
        elif ch == '"':
            in_double = True
        elif ch in "({[":
            stack.append((ch, line))
        elif ch in ")}]":
            if not stack:
                return False, f"Unexpected closing '{ch}' at line {line}"
            top, top_line = stack.pop()
            pairs = {"(": ")", "{": "}", "[": "]"}
            if pairs[top] != ch:
                return False, f"Mismatched bracket: opened '{top}' at line {top_line}, closed with '{ch}' at line {line}"
        i += 1

    if in_single:
        return False, "Unclosed single-quote string"
    if in_double:
        return False, "Unclosed double-quote string"
    if in_block:
        return False, "Unclosed block comment (/*)"
    if stack:
        top, top_line = stack[-1]
        return False, f"Unclosed '{top}' opened at line {top_line}"

    return True, "No syntax errors detected"

def run_lint(target):
    files = []
    if os.path.isfile(target):
        files.append(target)
    elif os.path.isdir(target):
        for root, _, filenames in os.walk(target):
            for fn in filenames:
                if fn.endswith(".php"):
                    files.append(os.path.join(root, fn))
    else:
        print(f"Error: Target '{target}' not found.")
        sys.exit(1)

    runtime = find_php_binary()
    failed = False

    for f in sorted(files):
        rel = os.path.relpath(f)
        if runtime and runtime["type"] == "host":
            res = subprocess.run([runtime["bin"], "-l", f], capture_output=True, text=True)
            if res.returncode == 0:
                print(f"No syntax errors detected in {rel}")
            else:
                print(f"Errors in {rel}:\n{res.stderr.strip() or res.stdout.strip()}")
                failed = True
        elif runtime and runtime["type"] == "docker":
            with open(f, "r", encoding="utf-8", errors="replace") as pf:
                code_in = pf.read()
            cmd = [runtime["bin"], "exec", "-i", runtime["container"], "sh", "-c", "cat > /tmp/_wpp_lint.php && php -l /tmp/_wpp_lint.php; RET=$?; rm -f /tmp/_wpp_lint.php; exit $RET"]
            res = subprocess.run(cmd, input=code_in, capture_output=True, text=True)
            if res.returncode == 0:
                print(f"No syntax errors detected in {rel}")
            else:
                print(f"Errors in {rel}:\n{res.stderr.strip() or res.stdout.strip()}")
                failed = True
        else:
            ok, msg = fallback_lint(f)
            if ok:
                print(f"No syntax errors detected in {rel} (Python AST)")
            else:
                print(f"Syntax error in {rel}: {msg}")
                failed = True

    if failed:
        sys.exit(1)

if __name__ == "__main__":
    target = sys.argv[1] if len(sys.argv) > 1 else "."
    run_lint(target)
