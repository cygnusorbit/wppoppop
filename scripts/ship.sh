#!/usr/bin/env bash
# Usage: scripts/ship.sh "commit message"
set -euo pipefail
cd "$(dirname "$0")/.."
MSG="${1:-Update}"

[ -d .git ] || git init
git remote get-url origin >/dev/null 2>&1 || git remote add origin https://github.com/cygnusorbit/wppoppop.git
git remote set-url origin https://github.com/cygnusorbit/wppoppop.git   # no token in the URL
git fetch origin --tags --quiet || true

NEXT=$(python3 - <<'PY'
import re, subprocess
s = open('wppoppop.php', encoding='utf-8').read()
m = re.search(r"define\(\s*['\"]WPPOPPOP_VERSION['\"]\s*,\s*['\"](\d+)\.(\d+)\.(\d+)", s)
a, b, c = map(int, m.groups())
def exists(ref):
    return subprocess.run(['git', 'rev-parse', '-q', '--verify', ref], capture_output=True).returncode == 0
def taken(v):
    t = 'working-stable-v' + v
    return exists('refs/tags/' + t) or exists('refs/heads/backup/' + t) or exists('refs/remotes/origin/backup/' + t)
c += 1
while taken('%d.%d.%d' % (a, b, c)):
    c += 1
print('%d.%d.%d' % (a, b, c))
PY
)

TAG="working-stable-v${NEXT}"
python3 scripts/update_version.py "$NEXT"
git add -A
git commit -m "${MSG} (v${NEXT})"
git tag -a "$TAG" -m "Backup ${NEXT}"
git branch "backup/${TAG}"
git push -u origin main --force-with-lease
git push origin "$TAG" "backup/${TAG}"
echo "Shipped v${NEXT}  (tag + backup branch pushed)"
