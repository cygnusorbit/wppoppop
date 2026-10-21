<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 16B: CHECKBOX CHOICES GROUP (checkboxgroup) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-checkboxgroup" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">16. Checkbox Choices (Label + Value)</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">CHECKBOX CHOICES (LABEL + VALUE)</label>
    <textarea id="prop-checkboxgroup-options" rows="4" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="optionsList" tabindex="-1" aria-hidden="true" style="display:none;"></textarea>
    <!-- WPPOPPOP_CHECKBOX_REPEATER -->
    <style>
    #wppoppop-checkboxgroup-rows .wppoppop-checkboxgroup-row{display:flex;align-items:center;gap:3px;background:#090d16;border:1px solid #1e293b;border-radius:5px;padding:3px;margin-bottom:4px;}
    #wppoppop-checkboxgroup-rows .wppoppop-checkboxgroup-row.is-dragging{opacity:.55;border-color:#38bdf8;box-shadow:0 2px 8px rgba(0,0,0,.45);}
    #wppoppop-checkboxgroup-rows .wppoppop-checkboxgroup-grip{flex:0 0 14px;width:14px;height:22px;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:13px;line-height:1;cursor:grab;touch-action:none;-webkit-user-select:none;-moz-user-select:none;user-select:none;}
    #wppoppop-checkboxgroup-rows .wppoppop-checkboxgroup-grip:hover{color:#38bdf8;}
    #wppoppop-checkboxgroup-rows.is-sorting,#wppoppop-checkboxgroup-rows.is-sorting *{cursor:grabbing!important;-webkit-user-select:none;-moz-user-select:none;user-select:none;}
    #wppoppop-checkboxgroup-rows .wppoppop-checkboxgroup-row input[type="text"]{flex:1 1 0;min-width:0;height:22px;min-height:0;padding:0 6px;font-size:11px;line-height:20px;}
    #wppoppop-checkboxgroup-rows .wppoppop-checkboxgroup-btn,#wppoppop-checkboxgroup-add{min-height:0!important;height:20px;min-width:20px;padding:0 5px;font-size:11px;font-weight:700;line-height:18px;}
    #wppoppop-checkboxgroup-rows .wppoppop-checkboxgroup-btn{flex:0 0 20px;width:20px;padding:0;}
    #wppoppop-checkboxgroup-add{margin-bottom:10px;padding:0 8px;}
    </style>
    <div style="display:flex;gap:3px;margin-bottom:3px;padding:0 3px;">
        <span style="flex:0 0 14px;"></span>
        <span style="flex:1 1 0;font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">Label</span>
        <span style="flex:1 1 0;font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;">Value <em style="font-weight:400;text-transform:none;">(optional)</em></span>
        <span style="flex:0 0 43px;"></span>
    </div>
    <div id="wppoppop-checkboxgroup-rows" style="margin-bottom:4px;"></div>
    <button type="button" id="wppoppop-checkboxgroup-add" class="button button-small">+ Add choice</button>
    <script>
    (function () {
        'use strict';
        if (window.__wppCheckboxRepeater2) { return; }
        window.__wppCheckboxRepeater2 = true;
        window.__wppCheckboxRepeater = true;
        var last = null, drag = null;
        function ta()  { return document.getElementById('prop-checkboxgroup-options'); }
        function box() { return document.getElementById('wppoppop-checkboxgroup-rows'); }
        function trim(v) { return String(v == null ? '' : v).replace(/^\s+|\s+$/g, ''); }
        function clean(v) { return trim(String(v == null ? '' : v).replace(/[\r\n|]+/g, ' ')); }
        function fire(el) {
            var ev;
            try { ev = new Event('input', { bubbles: true }); } catch (err) { ev = document.createEvent('Event'); ev.initEvent('input', true, true); }
            el.dispatchEvent(ev);
        }
        function parse(line) {
            var i = line.indexOf('|');
            if (i === -1) { return { l: trim(line), v: '' }; }
            return { l: trim(line.slice(0, i)), v: trim(line.slice(i + 1)) };
        }
        function mkInput(ph, val, role) {
            var i = document.createElement('input');
            i.type = 'text';
            i.className = 'widefat';
            i.placeholder = ph;
            i.value = val || '';
            i.setAttribute('data-role', role);
            return i;
        }
        function mkBtn(txt, title, act) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'button button-small wppoppop-checkboxgroup-btn';
            b.title = title;
            b.setAttribute('aria-label', title);
            b.setAttribute('data-act', act);
            b.appendChild(document.createTextNode(txt));
            return b;
        }
        function mkRow(l, v) {
            var row = document.createElement('div');
            row.className = 'wppoppop-checkboxgroup-row';
            var g = document.createElement('span');
            g.className = 'wppoppop-checkboxgroup-grip';
            g.title = 'Drag to reorder';
            g.setAttribute('aria-label', 'Drag to reorder');
            g.appendChild(document.createTextNode('\u22EE\u22EE'));
            g.style.fontSize = '10px';
            g.style.letterSpacing = '-3px';
            row.appendChild(g);
            row.appendChild(mkInput('Label', l, 'label'));
            row.appendChild(mkInput('Value', v, 'value'));
            row.appendChild(mkBtn('\u29C9', 'Copy this choice', 'copy'));
            row.appendChild(mkBtn('\u00D7', 'Delete this choice', 'del'));
            return row;
        }
        function serialize() {
            var t = ta(), c = box();
            if (!t || !c) { return; }
            var rows = c.querySelectorAll('.wppoppop-checkboxgroup-row'), out = [], i;
            for (i = 0; i < rows.length; i++) {
                var l = clean(rows[i].querySelector('[data-role="label"]').value);
                var v = clean(rows[i].querySelector('[data-role="value"]').value);
                if (!l && !v) { continue; }
                if (!l) { l = v; }
                out.push((v && v !== l) ? (l + ' | ' + v) : l);
            }
            var joined = out.join('\n');
            last = joined;
            if (t.value !== joined) { t.value = joined; fire(t); }
        }
        function rebuild(force) {
            var t = ta(), c = box();
            if (!t || !c || drag) { return; }
            var raw = String(t.value || '');
            if (!force && last !== null && raw === last) { return; }
            var lines = raw.split(/\r?\n/).map(trim).filter(Boolean);
            if (lines.length === 1 && lines[0].indexOf(',') !== -1 && lines[0].indexOf('|') === -1) {
                lines = lines[0].split(',').map(trim).filter(Boolean);
            }
            c.innerHTML = '';
            lines.forEach(function (ln) { var p = parse(ln); c.appendChild(mkRow(p.l, p.v)); });
            if (!lines.length) { c.appendChild(mkRow('', '')); }
            last = raw;
        }
        window.__wppCheckboxRebuild = rebuild;

        /* ---------- mouse / touch / pen drag to reorder (handle only) ---------- */
        function moveTo(y) {
            var c = box();
            if (!c || !drag) { return; }
            var rows = c.querySelectorAll('.wppoppop-checkboxgroup-row'), i, ref = null;
            for (i = 0; i < rows.length; i++) {
                if (rows[i] === drag.row) { continue; }
                var r = rows[i].getBoundingClientRect();
                if (y < r.top + r.height / 2) { ref = rows[i]; break; }
            }
            if (ref) { if (drag.row.nextSibling !== ref) { c.insertBefore(drag.row, ref); } }
            else if (c.lastChild !== drag.row) { c.appendChild(drag.row); }
        }
        function endDrag() {
            if (!drag) { return; }
            var d = drag; drag = null;
            d.row.classList.remove('is-dragging');
            var c = box(); if (c) { c.classList.remove('is-sorting'); }
            try { if (d.grip.releasePointerCapture) { d.grip.releasePointerCapture(d.id); } } catch (er) {}
            serialize();
        }
        document.addEventListener('pointerdown', function (e) {
            var g = (e.target && e.target.closest) ? e.target.closest('#wppoppop-checkboxgroup-rows .wppoppop-checkboxgroup-grip') : null;
            if (!g || (e.button !== undefined && e.button !== 0)) { return; }
            var row = g.closest('.wppoppop-checkboxgroup-row');
            if (!row) { return; }
            e.preventDefault();
            e.stopPropagation();
            drag = { row: row, grip: g, id: e.pointerId };
            row.classList.add('is-dragging');
            box().classList.add('is-sorting');
            try { g.setPointerCapture(e.pointerId); } catch (er) {}
        }, true);
        document.addEventListener('pointermove', function (e) {
            if (!drag || e.pointerId !== drag.id) { return; }
            e.preventDefault();
            moveTo(e.clientY);
        }, true);
        document.addEventListener('pointerup', function (e) { if (drag && e.pointerId === drag.id) { endDrag(); } }, true);
        document.addEventListener('pointercancel', function (e) { if (drag && e.pointerId === drag.id) { endDrag(); } }, true);
        document.addEventListener('keydown', function (e) { if (drag && e.key === 'Escape') { endDrag(); } }, true);

        /* ---------- typing ---------- */
        document.addEventListener('input', function (e) {
            var t = e.target;
            if (t && t.closest && t.closest('#wppoppop-checkboxgroup-rows')) { serialize(); }
        }, true);

        /* ---------- add / copy / delete ---------- */
        document.addEventListener('click', function (e) {
            var tg = e.target;
            var addTop = (tg && tg.closest) ? tg.closest('#wppoppop-checkboxgroup-add') : null;
            if (addTop) {
                e.preventDefault();
                var c0 = box();
                if (c0) {
                    var r0 = mkRow('', '');
                    c0.appendChild(r0);
                    var f0 = r0.querySelector('input'); if (f0) { f0.focus(); }
                }
                return;
            }
            var b = (tg && tg.closest) ? tg.closest('#wppoppop-checkboxgroup-rows [data-act]') : null;
            if (b) {
                e.preventDefault();
                var row = b.closest('.wppoppop-checkboxgroup-row'), c = box();
                if (!row || !c) { return; }
                var act = b.getAttribute('data-act');
                if (act === 'copy') {
                    var cl = mkRow(row.querySelector('[data-role="label"]').value, row.querySelector('[data-role="value"]').value);
                    c.insertBefore(cl, row.nextSibling);
                } else if (act === 'del') {
                    if (c.querySelectorAll('.wppoppop-checkboxgroup-row').length > 1) { c.removeChild(row); }
                    else {
                        var ins = row.querySelectorAll('input'), k;
                        for (k = 0; k < ins.length; k++) { ins[k].value = ''; }
                    }
                }
                serialize();
                return;
            }
            if (tg && tg.closest && tg.closest('.wppoppop-canvas-item, .wppoppop-layer-item')) {
                [60, 250, 600].forEach(function (ms) { setTimeout(function () { rebuild(false); }, ms); });
            }
        }, true);

        function watch() {
            var p = document.getElementById('panel-elem-checkboxgroup');
            if (!p || p.__wppCheckboxWatch) { return; }
            p.__wppCheckboxWatch = true;
            if (window.MutationObserver) {
                new MutationObserver(function () { rebuild(false); }).observe(p, { attributes: true, attributeFilter: ['style', 'class'] });
            }
            p.addEventListener('mousedown', function () { rebuild(false); }, true);
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { watch(); rebuild(false); });
        } else { watch(); rebuild(false); }
    })();
    </script>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">LAYOUT ORIENTATION</label>
    <select id="prop-checkboxgroup-layout" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="layout" style="font-size:12px;">
        <option value="vertical">Vertical (Stacked)</option>
        <option value="horizontal">Horizontal (Inline Row)</option>
    </select>
</div>
