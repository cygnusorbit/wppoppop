#!/usr/bin/env python3
import os
import re
import sys
import subprocess

def find_wp_config():
    # Check parent directories for WordPress wp-config.php
    current = os.path.abspath(os.getcwd())
    for _ in range(5):
        candidate = os.path.join(current, "wp-config.php")
        if os.path.exists(candidate):
            return candidate
        parent = os.path.dirname(current)
        if parent == current:
            break
        current = parent
    return None

def parse_wp_config(config_path):
    config = {}
    with open(config_path, "r", encoding="utf-8", errors="ignore") as f:
        content = f.read()

    defines = {
        'DB_NAME': r"define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)",
        'DB_USER': r"define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)",
        'DB_PASSWORD': r"define\s*\(\s*['\"]DB_PASSWORD['\"]\s*,\s*['\"]([^'\"]*)['\"]\s*\)",
        'DB_HOST': r"define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)",
    }
    for key, pattern in defines.items():
        match = re.search(pattern, content)
        if match:
            config[key] = match.group(1)

    prefix_match = re.search(r"\$table_prefix\s*=\s*['\"]([^'\"]+)['\"]", content)
    config['table_prefix'] = prefix_match.group(1) if prefix_match else 'wp_'
    return config

def main():
    print("=" * 60)
    print("  WpPopPop Database Diagnostic Tool")
    print("=" * 60)

    # 1. Try WP-CLI first if available
    try:
        res = subprocess.run(["wp", "db", "query", "SHOW TABLES LIKE '%wppoppop%';"], capture_output=True, text=True)
        if res.returncode == 0:
            print("[+] Connected via WP-CLI:")
            print(res.stdout)
            print("[+] Checking 'wppoppop_items' schema:")
            schema_res = subprocess.run(["wp", "db", "query", "DESCRIBE $(wp db prefix)wppoppop_items;"], capture_output=True, text=True)
            print(schema_res.stdout)
            return
    except FileNotFoundError:
        pass

    # 2. Inspect wp-config.php
    wp_config = find_wp_config()
    if not wp_config:
        print("[-] wp-config.php not found in parent folders.")
        print("    Run WP-CLI command directly in your WordPress root: wp db query 'SHOW TABLES LIKE \'%wppoppop%\';'")
        sys.exit(0)

    cfg = parse_wp_config(wp_config)
    prefix = cfg.get('table_prefix', 'wp_')
    print(f"[+] Found wp-config.php: {wp_config}")
    print(f"[+] Database Name: {cfg.get('DB_NAME')}")
    print(f"[+] Table Prefix:  {prefix}")

    tables = [
        f"{prefix}wppoppop_items",
        f"{prefix}wppoppop_submissions",
        f"{prefix}wppoppop_campaigns",
        f"{prefix}wppoppop_transactions",
        f"{prefix}wppoppop_downloads"
    ]

    host = cfg.get('DB_HOST', 'localhost').split(':')[0]
    user = cfg.get('DB_USER', 'root')
    password = cfg.get('DB_PASSWORD', '')
    dbname = cfg.get('DB_NAME', 'wordpress')

    cmd = ["mysql", f"-h{host}", f"-u{user}"]
    if password:
        cmd.append(f"-p{password}")
    cmd.extend([dbname, "-e"])

    query = f"SHOW TABLES LIKE '{prefix}wppoppop%';"
    try:
        res = subprocess.run(cmd + [query], capture_output=True, text=True)
        if res.returncode == 0:
            print("\n[+] Existing Tables:")
            print(res.stdout if res.stdout else "No tables found.")
            
            # Check schema of items table
            desc_query = f"DESCRIBE {prefix}wppoppop_items;"
            desc_res = subprocess.run(cmd + [desc_query], capture_output=True, text=True)
            if desc_res.returncode == 0:
                print(f"[+] Schema for {prefix}wppoppop_items:")
                print(desc_res.stdout)
        else:
            print("[-] MySQL Query Error:", res.stderr.strip())
    except FileNotFoundError:
        print("[-] mysql CLI client not found in PATH.")
        print(f"    Target database: {dbname}, tables: {', '.join(tables)}")

if __name__ == '__main__':
    main()
