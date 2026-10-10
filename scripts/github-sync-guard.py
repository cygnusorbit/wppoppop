#!/usr/bin/env python3
import os
import sys
import subprocess
import urllib.request

REPO_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
REMOTE_URL = "https://github.com/cygnusorbit/wppoppop.git"
RAW_BASE_URL = "https://raw.githubusercontent.com/cygnusorbit/wppoppop/main/"

def ensure_remote_origin():
    """Ensure git remote origin points to the authoritative repository."""
    res = subprocess.run(["git", "remote", "get-url", "origin"], cwd=REPO_DIR, capture_output=True, text=True)
    if res.returncode != 0 or res.stdout.strip() != REMOTE_URL:
        subprocess.run(["git", "remote", "remove", "origin"], cwd=REPO_DIR, capture_output=True)
        add_res = subprocess.run(["git", "remote", "add", "origin", REMOTE_URL], cwd=REPO_DIR, capture_output=True, text=True)
        if add_res.returncode == 0:
            print(f"✔ Configured git remote origin to {REMOTE_URL}")
        else:
            print(f"⚠ Note: Unable to set remote origin: {add_res.stderr.strip()}")
    else:
        print(f"✔ Remote origin verified: {REMOTE_URL}")

def fetch_upstream():
    """Fetch latest refs from GitHub without overwriting uncommitted work."""
    print("⏳ Fetching latest upstream tree from https://github.com/cygnusorbit/wppoppop ...")
    res = subprocess.run(["git", "fetch", "origin", "main"], cwd=REPO_DIR, capture_output=True, text=True)
    if res.returncode == 0:
        print("✔ Successfully fetched origin/main.")
        return True
    else:
        print(f"⚠ Warning: Could not fetch from origin (offline or auth required). Proceeding with cached refs.")
        return False

def get_remote_file_content(rel_path):
    """Fetch authoritative file content from git tree origin/main or GitHub raw."""
    # Try git cat-file first
    git_show = subprocess.run(["git", "show", f"origin/main:{rel_path}"], cwd=REPO_DIR, capture_output=True, text=True)
    if git_show.returncode == 0:
        return git_show.stdout

    # Fallback to direct HTTPS request
    try:
        url = RAW_BASE_URL + rel_path
        with urllib.request.urlopen(url, timeout=5) as response:
            return response.read().decode("utf-8", errors="replace")
    except Exception as e:
        print(f"❌ Error fetching {rel_path} from GitHub: {e}")
        return None

def compare_with_upstream(rel_path):
    """Compare local file against GitHub upstream version to detect omissions."""
    local_path = os.path.join(REPO_DIR, rel_path)
    if not os.path.isfile(local_path):
        print(f"❌ Local file does not exist: {rel_path}")
        return False

    with open(local_path, "r", encoding="utf-8", errors="replace") as f:
        local_content = f.read()

    remote_content = get_remote_file_content(rel_path)
    if remote_content is None:
        print(f"⚠ Warning: Could not retrieve upstream version for {rel_path}.")
        return False

    print(f"📊 Upstream Reference Check for: {rel_path}")
    print(f"   Local Line Count: {len(local_content.splitlines())}")
    print(f"   Remote Line Count: {len(remote_content.splitlines())}")
    return True

if __name__ == "__main__":
    ensure_remote_origin()
    if len(sys.argv) > 1 and sys.argv[1] == "fetch":
        fetch_upstream()
    elif len(sys.argv) > 2 and sys.argv[1] == "check":
        fetch_upstream()
        compare_with_upstream(sys.argv[2])
    else:
        print("Usage:")
        print("  python3 scripts/github-sync-guard.py fetch")
        print("  python3 scripts/github-sync-guard.py check <relative-file-path>")
