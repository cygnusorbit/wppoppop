#!/usr/bin/env python3
"""Layer-settings audit. Usage: python3 scripts/audit-layer-fields.py <REF_DIR>
Read-only. Writes scripts/layer-audit-report.md"""
import os, re, sys, glob
BASE = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
REF = os.path.abspath(sys.argv[1]) if len(sys.argv) > 1 else None
if not REF or not os.path.isdir(REF):
    sys.exit("Pass the reference plugin directory as argument 1")
def read(p):
    with open(p, "r", encoding="utf-8", errors="ignore") as f: return f.read()

meta_file = None
for p in glob.glob(os.path.join(REF, "*.php")):
    if "element_properties_meta = array(" in read(p):
        meta_file = p; break
if not meta_file: sys.exit("meta file not found in reference root")

SKIP = {"settings", "page", "page-confirmation"}
layer_re = re.compile(r"^\t{3}'([a-z\-]+)' => array\($")
tab_re   = re.compile(r"^\t{4}'([a-z\-]+)' => array\('type' => 'tab'.*?'label' => esc_html__\('([^']+)'")
field_re = re.compile(r"^\t{5}'([a-z0-9\-]+)' => array\((.*)$")
type_re  = re.compile(r"'type' => '([a-z0-9\-]+)'")
data, layer, tab, in_meta = {}, None, None, False
for line in read(meta_file).splitlines():
    if "element_properties_meta = array(" in line: in_meta = True; continue
    if not in_meta: continue
    if line.startswith("\t\t);") : break
    m = layer_re.match(line)
    if m: layer = m.group(1); tab = None; data[layer] = {}; continue
    if layer is None: continue
    m = tab_re.match(line)
    if m: tab = m.group(2); data[layer][tab] = []; continue
    m = field_re.match(line)
    if m and tab:
        t = type_re.search(m.group(2))
        data[layer][tab].append((m.group(1), t.group(1) if t else "-"))

tpl = ""
for root, _, files in os.walk(os.path.join(BASE, "templates", "builder")):
    for fn in files:
        if fn.endswith(".php"): tpl += read(os.path.join(root, fn))
js = "".join(read(p) for p in glob.glob(os.path.join(BASE, "admin", "js", "builder", "**", "*.js"), recursive=True))
ajax = "".join(read(p) for p in glob.glob(os.path.join(BASE, "includes", "ajax", "**", "*.php"), recursive=True))
def variants(k): return {k, k.replace("-", "_"), k.replace("-", "")}
def has(blob, k): return any(v in blob for v in variants(k))

out = ["# Layer settings audit", ""]
total = gaps = 0
seen = {}
for layer, tabs in sorted(data.items()):
    if layer in SKIP: continue
    out += ["## Layer: " + layer, "", "| Tab | Field | Control | DOM | JS | AJAX |", "|---|---|---|---|---|---|"]
    for t, fields in tabs.items():
        for k, ctl in fields:
            if k in ("logic", "css", "style-message"): pass
            if k not in seen:
                seen[k] = (has(tpl, "prop-" + k) or has(tpl, k), has(js, k), has(ajax, k))
            d, j, a = seen[k]
            total += 1
            if not (d and j and a): gaps += 1
            f = lambda b: "yes" if b else "MISSING"
            out.append("| %s | %s | %s | %s | %s | %s |" % (t, k, ctl, f(d), f(j), f(a)))
    out.append("")
out.append("Fields scanned: %d, with a gap: %d" % (total, gaps))
dest = os.path.join(BASE, "scripts", "layer-audit-report.md")
open(dest, "w", encoding="utf-8").write("\n".join(out))
print("Wrote", dest, "-", total, "fields,", gaps, "with gaps;", len(data), "layer types parsed")
