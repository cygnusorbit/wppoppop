
cat << 'EOF' > update_version.py
import re
import sys
from pathlib import Path

target_file = Path.home() / "Desktop" / "wppoppop" / "wppoppop.php"
if not target_file.exists():
    target_file = Path("wppoppop.php")

new_version = sys.argv[1] if len(sys.argv) > 1 else "1.2.0"
content = target_file.read_text(encoding="utf-8")

# Update the header version and the WPPOPPOP_VERSION constant
content = re.sub(r'(Version:\s+)[0-9.]+', rf'\g<1>{new_version}', content)
content = re.sub(
    r"(define\(\s*['\"]WPPOPPOP_VERSION['\"],\s*['\"])[^'\"]+(['\"]\);)",
    rf"\g<1>{new_version}\2",
    content
)

target_file.write_text(content, encoding="utf-8")
print(f"WP Pop Pop version updated to {new_version} in {target_file}")
EOF
