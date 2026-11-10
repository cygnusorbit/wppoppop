#!/usr/bin/env python3
# Builds languages/wppoppop.pot from the PHP and JS sources.
# Usage (from the plugin folder):  python3 scripts/make-pot.py
import os, re, sys, datetime

DOMAIN = 'wppoppop'
OUT = os.path.join('languages', DOMAIN + '.pot')
SKIP_DIRS = {'.git', '.dev', 'node_modules', 'vendor', 'scripts', 'languages'}
SKIP_PREFIX = ('assets/vendor',)

# function name -> argument roles (the domain is always the last one)
SPEC = {
    '__': ['s', 'd'], '_e': ['s', 'd'], 'esc_html__': ['s', 'd'], 'esc_html_e': ['s', 'd'],
    'esc_attr__': ['s', 'd'], 'esc_attr_e': ['s', 'd'],
    '_x': ['s', 'c', 'd'], '_ex': ['s', 'c', 'd'], 'esc_html_x': ['s', 'c', 'd'], 'esc_attr_x': ['s', 'c', 'd'],
    '_n': ['s', 'p', 'n', 'd'], '_nx': ['s', 'p', 'n', 'c', 'd'],
    '_n_noop': ['s', 'p', 'd'], '_nx_noop': ['s', 'p', 'c', 'd'],
}


def mask(src, js):
    # Returns (masked source, comments). Comments and non-code text are blanked, strings are kept.
    out, comments = [], []
    n, i, line = len(src), 0, 1
    in_php = js
    prev = ''

    def blank(t):
        return ''.join(c if c == '\n' else ' ' for c in t)

    while i < n:
        c = src[i]
        if not in_php:
            m = re.compile(r'<\?(?:php\b|=)').search(src, i)
            j = m.end() if m else n
            seg = src[i:j]
            out.append(blank(seg))
            line += seg.count('\n')
            i = j
            in_php = True
            continue
        if not js and src.startswith('?>', i):
            out.append('  ')
            i += 2
            in_php = False
            continue
        if c in '\'"' or (js and c == '`'):
            j = i + 1
            while j < n and src[j] != c:
                j += 2 if src[j] == '\\' else 1
            j = min(j + 1, n)
            seg = src[i:j]
            out.append(seg)
            line += seg.count('\n')
            i, prev = j, 'x'
            continue
        if src.startswith('/*', i):
            j = src.find('*/', i + 2)
            j = n if j < 0 else j + 2
            seg = src[i:j]
            line += seg.count('\n')
            comments.append((line, seg))
            out.append(blank(seg))
            i = j
            continue
        if src.startswith('//', i) or (not js and c == '#' and not src.startswith('#[', i)):
            j = i
            while j < n and src[j] != '\n' and not (not js and src.startswith('?>', j)):
                j += 1
            comments.append((line, src[i:j]))
            out.append(blank(src[i:j]))
            i = j
            continue
        if js and c == '/' and (prev in '' or prev in '(,=:[!&|?{};+-*%<>~^'):
            j = i + 1
            cls = False
            while j < n and src[j] != '\n':
                if src[j] == '\\':
                    j += 2
                    continue
                if src[j] == '[':
                    cls = True
                elif src[j] == ']':
                    cls = False
                elif src[j] == '/' and not cls:
                    break
                j += 1
            out.append(blank(src[i:j + 1]))
            i, prev = j + 1, 'x'
            continue
        out.append(c)
        if c == '\n':
            line += 1
        if not c.isspace():
            prev = c
        i += 1
    return ''.join(out), comments


def split_args(text, start):
    # text[start] is just after '('. Returns (args, end) or None.
    depth, i, n, cur, args = 0, start, len(text), start, []
    while i < n:
        c = text[i]
        if c in '\'"`':
            j = i + 1
            while j < n and text[j] != c:
                j += 2 if text[j] == '\\' else 1
            i = j + 1
            continue
        if c in '([{':
            depth += 1
        elif c in ')]}':
            if depth == 0:
                args.append(text[cur:i])
                return args, i
            depth -= 1
        elif c == ',' and depth == 0:
            args.append(text[cur:i])
            cur = i + 1
        i += 1
    return None


LIT = r"""(?:'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"|`(?:\\.|[^`\\])*`)"""
LIT_RE = re.compile(r'^\s*' + LIT + r'(?:\s*\.\s*' + LIT + r')*\s*$' , re.S)
PIECE_RE = re.compile(LIT, re.S)
ESC = {'n': '\n', 't': '\t', 'r': '\r', '\\': '\\', '"': '"', "'": "'", '`': '`', '$': '$'}


UNI_RE = re.compile(r'\\u\{([0-9A-Fa-f]+)\}|\\u([0-9A-Fa-f]{4})|\\x([0-9A-Fa-f]{2})')


def uni(body):
    return UNI_RE.sub(lambda m: chr(int(m.group(1) or m.group(2) or m.group(3), 16)), body)


def unquote(piece, js):
    q, body = piece[0], piece[1:-1]
    if js:
        body = uni(body)
    if q == "'":
        if js:
            return re.sub(r'\\(.)', lambda m: ESC.get(m.group(1), m.group(1)), body, flags=re.S)
        return body.replace("\\\\", "\\").replace("\\'", "'")
    if q == '`' and '${' in body:
        return None
    if q == '"' and not js and re.search(r'(?<!\\)\$[A-Za-z_{]|\{\$', body):
        return None
    return re.sub(r'\\(.)', lambda m: ESC.get(m.group(1), '\\' + m.group(1)), body, flags=re.S)


def literal(arg, js):
    if not LIT_RE.match(arg):
        return None
    parts = [unquote(p, js) for p in PIECE_RE.findall(arg)]
    return None if any(p is None for p in parts) else ''.join(parts)


def tcomment(comments, lineno):
    found = []
    for end, text in comments:
        if lineno - 1 <= end <= lineno:
            body = re.sub(r'^\s*(/\*+|//|#)', '', text)
            body = re.sub(r'\*+/\s*$', '', body)
            body = ' '.join(re.sub(r'^\s*\*+', '', l).strip() for l in body.split('\n')).strip()
            if body.lower().startswith('translators:'):
                found.append(body)
    return found


def scan(path, rel, js, entries, order):
    with open(path, encoding='utf-8', errors='replace') as f:
        src = f.read()
    text, comments = mask(src, js)
    pat = r"(?<![\w$.\\>:])(?:wp\.i18n\.)?(" + '|'.join(sorted(SPEC, key=len, reverse=True)) + r")\s*\("
    if js:
        pat = r"(?<![\w$])(?:wp\.i18n\.|i18n\.)?(" + '|'.join(['__', '_x', '_n', '_nx']) + r")\s*\("
    for m in re.finditer(pat, text):
        if re.search(r'function\s*&?\s*$', text[max(0, m.start() - 14):m.start()]):
            continue
        res = split_args(text, m.end())
        if not res:
            continue
        roles = SPEC[m.group(1)]
        args = [a.strip() for a in res[0]]
        if len(args) != len(roles):
            continue
        vals = {}
        for role, a in zip(roles, args):
            if role == 'n':
                continue
            v = literal(a, js)
            if v is None:
                break
            vals[role] = v
        else:
            if vals.get('d') != DOMAIN or not vals.get('s'):
                continue
            lineno = text.count('\n', 0, m.start()) + 1
            key = (vals.get('c', ''), vals['s'], vals.get('p', ''))
            if key not in entries:
                entries[key] = {'refs': [], 'tc': []}
                order.append(key)
            e = entries[key]
            ref = '%s:%d' % (rel, lineno)
            if ref not in e['refs']:
                e['refs'].append(ref)
            for t in tcomment(comments, lineno):
                if t not in e['tc']:
                    e['tc'].append(t)


def q(s):
    return '"' + s.replace('\\', '\\\\').replace('"', '\\"').replace('\n', '\\n').replace('\t', '\\t') + '"'


def header_fields(main):
    with open(main, encoding='utf-8') as f:
        head = f.read(8192)
    out = []
    for label, what in (('Plugin Name', 'Plugin Name'), ('Plugin URI', 'Plugin URI'), ('Description', 'Description'),
                        ('Author', 'Author'), ('Author URI', 'Author URI')):
        m = re.search(r'^[ \t/*#@]*' + re.escape(label) + r':[ \t]*(.+)$', head, re.M)
        if m:
            out.append(('%s of the plugin' % what, m.group(1).strip()))
    version = re.search(r'^[ \t/*#@]*Version:[ \t]*(\S+)', head, re.M)
    return out, (version.group(1) if version else '')


def main():
    if not os.path.isfile(DOMAIN + '.php'):
        sys.exit('Run this from the plugin folder.')
    entries, order = {}, []
    for root, dirs, files in os.walk('.'):
        rel_root = os.path.relpath(root, '.').replace(os.sep, '/')
        dirs[:] = sorted(d for d in dirs if d not in SKIP_DIRS
                         and not (rel_root == '.' and d in SKIP_DIRS)
                         and not (('%s/%s' % (rel_root, d)).lstrip('./').startswith(SKIP_PREFIX)))
        for fn in sorted(files):
            rel = os.path.normpath(os.path.join(root, fn)).replace(os.sep, '/')
            if fn.endswith('.php'):
                scan(os.path.join(root, fn), rel, False, entries, order)
            elif fn.endswith('.js') and not fn.endswith('.min.js'):
                scan(os.path.join(root, fn), rel, True, entries, order)
    fields, version = header_fields(DOMAIN + '.php')
    now = datetime.datetime.now(datetime.timezone.utc).strftime('%Y-%m-%dT%H:%M:%S+00:00')
    lines = [
        '# Copyright (C) %s cygnusorbit' % now[:4],
        '# This file is distributed under the GPL-2.0-or-later.',
        'msgid ""', 'msgstr ""',
        '"Project-Id-Version: WpPopPop %s\\n"' % version,
        '"Report-Msgid-Bugs-To: https://wordpress.org/support/plugin/%s\\n"' % DOMAIN,
        '"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n"',
        '"Language-Team: LANGUAGE <LL@li.org>\\n"',
        '"MIME-Version: 1.0\\n"',
        '"Content-Type: text/plain; charset=UTF-8\\n"',
        '"Content-Transfer-Encoding: 8bit\\n"',
        '"POT-Creation-Date: %s\\n"' % now,
        '"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n"',
        '"X-Generator: scripts/make-pot.py\\n"',
        '"X-Domain: %s\\n"' % DOMAIN, '',
    ]
    for comment, value in fields:
        lines += ['#. %s' % comment, '#: %s.php' % DOMAIN, 'msgid %s' % q(value), 'msgstr ""', '']
    for key in order:
        ctx, single, plural = key
        e = entries[key]
        for t in e['tc']:
            lines.append('#. ' + t)
        lines.append('#: ' + ' '.join(e['refs']))
        if ctx:
            lines.append('msgctxt ' + q(ctx))
        lines.append('msgid ' + q(single))
        if plural:
            lines += ['msgid_plural ' + q(plural), 'msgstr[0] ""', 'msgstr[1] ""']
        else:
            lines.append('msgstr ""')
        lines.append('')
    os.makedirs('languages', exist_ok=True)
    text = '\n'.join(lines)
    stamp = re.compile(r'^"POT-Creation-Date:[^\n]*\n', re.M)
    if os.path.isfile(OUT):
        with open(OUT, encoding='utf-8') as f:
            if stamp.sub('', f.read()) == stamp.sub('', text):
                print('%s is up to date (%d strings).' % (OUT, len(order) + len(fields)))
                return
    with open(OUT, 'w', encoding='utf-8', newline='\n') as f:
        f.write(text)
    print('%s written: %d strings.' % (OUT, len(order) + len(fields)))


main()
