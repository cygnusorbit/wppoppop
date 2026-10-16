
import os, re, glob

print("==================================================================")
print("  WpPopPop v3.0.647 — 100% Pure Autonomous Decomposed Audit")
print("==================================================================")

# 1. Verify Plugin Version
version = "Unknown"
if os.path.exists("wppoppop.php"):
    with open("wppoppop.php", "r", encoding="utf-8", errors="ignore") as f:
        m = re.search(r"Version:\s*([0-9.]+)", f.read())
        if m:
            version = m.group(1)
print(f"Detected Plugin Version: {version}\n")

# 2. Check Decomposed Module Counts
checks = [
    ("Element Content Partials (elem-*.php)", "templates/builder/elements/elem-*.php", 26),
    ("Inspector Tab Partials (tab-*.php)", "templates/builder/elements/tab-*.php", 7),
    ("Campaign Settings Accordions (acc-*.php)", "templates/builder/settings/acc-*.php", 15),
    ("Builder JS Element Modules", "admin/js/builder/elements/*.js", 5),
    ("Frontend PHP Element Modules", "includes/front/elements/*.php", 5),
    ("Backend AJAX Element Modules", "includes/ajax/elements/*.php", 5),
    ("Frontend JS Runtime Modules", "public/js/front/*.js", 4),
    ("Frontend CSS Domain Modules", "public/css/front/*.css", 5),
    ("Builder CSS Domain Modules", "admin/css/builder/*.css", 6),
]

passed_counts = 0
for label, pattern, min_expected in checks:
    files = sorted(glob.glob(pattern))
    count = len(files)
    status = "✅ PASS" if count >= min_expected else f"❌ GAP (Found {count}, Expected >= {min_expected})"
    if count >= min_expected:
        passed_counts += 1
    print(f"  {label:42s}: {count:2d} files  [{status}]")

# 3. Inspect Coordinator Bottlenecks & Line Counts
print("\n--- Coordinator Thin-Shell & Delegation Audit ---")
coordinators = [
    "templates/builder/drawer-inspector.php",
    "templates/builder/drawer-settings.php",
    "admin/js/builder/builder-inspector.js",
    "admin/js/builder/builder-elements-registry.js",
    "admin/js/builder/builder-canvas.js",
    "admin/js/builder/builder-io.js",
    "includes/ajax/class-ajax-builder.php",
]
for path in coordinators:
    if os.path.exists(path):
        with open(path, "r", encoding="utf-8", errors="ignore") as f:
            content = f.read()
            lines = len(content.splitlines())
        print(f"  {path:45s}: {lines:4d} lines")

# 4. Specific Architectural Purity Gates
print("\n--- Autonomous Decomposition Purity Gates ---")
gates = []

# Gate A: Campaign Settings Accordions Decomposed
acc_files = len(glob.glob("templates/builder/settings/acc-*.php"))
gates.append(("Gate 1: Campaign Settings Accordions (acc-*.php)", acc_files >= 15))

# Gate B: Inspector 7 Tab Partials Decomposed
tab_files = len(glob.glob("templates/builder/elements/tab-*.php"))
gates.append(("Gate 2: Inspector 5-Tab Modules (tab-*.php)", tab_files >= 5))

# Gate C: Canvas Stage Registry Delegation
canvas_delegated = False
if os.path.exists("admin/js/builder/builder-canvas.js"):
    with open("admin/js/builder/builder-canvas.js", "r", encoding="utf-8", errors="ignore") as f:
        c_txt = f.read()
        canvas_delegated = ("WpPopPopElementsRegistry" in c_txt or "WpPopPopElements" in c_txt)
gates.append(("Gate 3: Canvas Stage Registry Delegation", canvas_delegated))

# Gate D: Pre-Save Schema Flush Delegation in builder-io.js
io_delegated = False
if os.path.exists("admin/js/builder/builder-io.js"):
    with open("admin/js/builder/builder-io.js", "r", encoding="utf-8", errors="ignore") as f:
        io_txt = f.read()
        io_delegated = ("flushElementFromDOM" in io_txt or "WpPopPopElementsRegistry" in io_txt)
gates.append(("Gate 4: Pre-Save Schema Flush (builder-io.js)", io_delegated))

# Gate E: Backend AJAX Registry Sanitization Delegation
ajax_delegated = False
if os.path.exists("includes/ajax/class-ajax-builder.php"):
    with open("includes/ajax/class-ajax-builder.php", "r", encoding="utf-8", errors="ignore") as f:
        aj_txt = f.read()
        ajax_delegated = "WpPopPop_Ajax_Element_Registry" in aj_txt
gates.append(("Gate 5: Backend AJAX Schema Registry Delegation", ajax_delegated))

all_gates_pass = True
for g_label, g_ok in gates:
    print(f"  {g_label:48s}: {'✅ YES' if g_ok else '❌ NO (Hybrid Bottleneck Remains)'}")
    if not g_ok:
        all_gates_pass = False

print("\n==================================================================")
if all_gates_pass and passed_counts == len(checks):
    print("🎉 VERDICT: 100% PURE AUTONOMOUS DECOMPOSED MODULAR ARCHITECTURE!")
else:
    print("⚠️ VERDICT: HYBRID BOTTLENECKS DETECTED — SEE FAILED GATES ABOVE.")
print("==================================================================")
