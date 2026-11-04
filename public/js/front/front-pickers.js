/* WPPOPPOP_FRONT_PICKERS_V1
 * Date + Time pickers for front form elements (live + Live Preview).
 * - Date: custom calendar, used only when Air Datepicker is not bound to the field.
 * - Time: slot list (native <input type="time"> has no dropdown in Firefox/Safari).
 * Values are written in the same format the field already uses and
 * input/change events are dispatched so conditional logic and submit keep working.
 */
(function () {
    'use strict';

    var pop = null;
    var owner = null;
    var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var DOW = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

    function pad(n) { return (n < 10 ? '0' : '') + n; }

    function fire(el) {
        ['input', 'change'].forEach(function (t) {
            var e;
            try { e = new Event(t, { bubbles: true }); }
            catch (err) { e = document.createEvent('Event'); e.initEvent(t, true, true); }
            el.dispatchEvent(e);
        });
    }

    function close() {
        if (pop && pop.parentNode) { pop.parentNode.removeChild(pop); }
        pop = null;
        owner = null;
    }

    function place() {
        if (!pop || !owner) { return; }
        var r = owner.getBoundingClientRect();
        var w = pop.offsetWidth;
        var h = pop.offsetHeight;
        var vw = document.documentElement.clientWidth;
        var vh = document.documentElement.clientHeight;
        var top = r.bottom + 4;
        if (top + h > vh - 4 && r.top - h - 4 > 4) { top = r.top - h - 4; }
        top = Math.max(4, Math.min(top, vh - h - 4));
        var left = Math.max(4, Math.min(r.left, vw - w - 4));
        pop.style.top = top + 'px';
        pop.style.left = left + 'px';
    }

    function mount(el, node) {
        close();
        owner = el;
        pop = node;
        try { pop.style.fontFamily = window.getComputedStyle(el).fontFamily; } catch (e) { /* noop */ }
        pop.style.visibility = 'hidden';
        document.body.appendChild(pop);
        place();
        pop.style.visibility = '';
    }

    /* ---------- Date ---------- */
    function parseIso(s) {
        var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(s || '');
        return m ? new Date(+m[1], +m[2] - 1, +m[3]) : null;
    }

    function parseVal(v, fmt) {
        var m;
        v = (v || '').trim();
        if (fmt === 'MM/DD/YYYY') {
            m = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/.exec(v);
            return m ? new Date(+m[3], +m[1] - 1, +m[2]) : null;
        }
        if (fmt === 'DD/MM/YYYY') {
            m = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/.exec(v);
            return m ? new Date(+m[3], +m[2] - 1, +m[1]) : null;
        }
        m = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(v);
        return m ? new Date(+m[1], +m[2] - 1, +m[3]) : null;
    }

    function fmtVal(d, fmt) {
        if (fmt === 'MM/DD/YYYY') { return pad(d.getMonth() + 1) + '/' + pad(d.getDate()) + '/' + d.getFullYear(); }
        if (fmt === 'DD/MM/YYYY') { return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear(); }
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function sameDay(a, b) {
        return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
    }

    function openDate(el) {
        var fmt = el.getAttribute('data-format') || 'YYYY-MM-DD';
        var min = parseIso(el.getAttribute('data-min'));
        var max = parseIso(el.getAttribute('data-max'));
        var sel = parseVal(el.value, fmt);
        var today = new Date();
        var view = new Date((sel || today).getFullYear(), (sel || today).getMonth(), 1);

        function setVal(d) {
            el.value = d ? fmtVal(d, fmt) : '';
            fire(el);
            close();
        }

        function render() {
            var box = document.createElement('div');
            box.className = 'wppoppop-pk wppoppop-pk-cal';
            box.setAttribute('role', 'dialog');
            var html = '<div class="wppoppop-pk-head">' +
                '<button type="button" class="wppoppop-pk-nav" data-nav="-1" aria-label="Previous month">&#8249;</button>' +
                '<span class="wppoppop-pk-title">' + MONTHS[view.getMonth()] + ' ' + view.getFullYear() + '</span>' +
                '<button type="button" class="wppoppop-pk-nav" data-nav="1" aria-label="Next month">&#8250;</button></div>' +
                '<div class="wppoppop-pk-grid">';
            DOW.forEach(function (d) { html += '<span class="wppoppop-pk-dow">' + d + '</span>'; });
            var first = new Date(view.getFullYear(), view.getMonth(), 1).getDay();
            var days = new Date(view.getFullYear(), view.getMonth() + 1, 0).getDate();
            var i;
            for (i = 0; i < first; i++) { html += '<span></span>'; }
            for (i = 1; i <= days; i++) {
                var d = new Date(view.getFullYear(), view.getMonth(), i);
                var off = (min && d < min) || (max && d > max);
                html += '<button type="button" class="wppoppop-pk-day' + (sameDay(d, sel) ? ' is-sel' : '') + (sameDay(d, today) ? ' is-today' : '') +
                    '" data-day="' + i + '"' + (off ? ' disabled' : '') + '>' + i + '</button>';
            }
            html += '</div><div class="wppoppop-pk-foot"><button type="button" data-act="clear">Clear</button><button type="button" data-act="today">Today</button></div>';
            box.innerHTML = html;
            box.addEventListener('mousedown', function (e) { e.preventDefault(); });
            box.addEventListener('click', function (e) {
                var t = e.target;
                if (!t || !t.getAttribute) { return; }
                e.stopPropagation();
                var nav = t.getAttribute('data-nav');
                var day = t.getAttribute('data-day');
                var act = t.getAttribute('data-act');
                if (nav) {
                    view = new Date(view.getFullYear(), view.getMonth() + (+nav), 1);
                    mount(el, render());
                } else if (day) {
                    setVal(new Date(view.getFullYear(), view.getMonth(), +day));
                } else if (act === 'clear') {
                    setVal(null);
                } else if (act === 'today') {
                    var n = new Date();
                    var td = new Date(n.getFullYear(), n.getMonth(), n.getDate());
                    if ((!min || td >= min) && (!max || td <= max)) { setVal(td); }
                }
            });
            return box;
        }

        mount(el, render());
    }

    /* ---------- Time ---------- */
    function openTime(el) {
        var h24 = el.getAttribute('data-format') === '24h';
        var step = parseInt(el.getAttribute('step'), 10);
        step = (step > 0) ? Math.max(1, Math.round(step / 60)) : 15;
        var cur = /^(\d{2}):(\d{2})/.exec(el.value || '');
        var curMin = cur ? (+cur[1] * 60 + +cur[2]) : -1;
        var box = document.createElement('div');
        box.className = 'wppoppop-pk wppoppop-pk-time';
        box.setAttribute('role', 'listbox');
        var html = '';
        var m;
        for (m = 0; m < 1440; m += step) {
            var hh = Math.floor(m / 60);
            var mm = m % 60;
            var label = h24 ? (pad(hh) + ':' + pad(mm)) : (((hh % 12) || 12) + ':' + pad(mm) + ' ' + (hh < 12 ? 'AM' : 'PM'));
            html += '<button type="button" class="wppoppop-pk-slot' + (m === curMin ? ' is-sel' : '') + '" data-v="' + pad(hh) + ':' + pad(mm) + '">' + label + '</button>';
        }
        box.innerHTML = html;
        box.addEventListener('mousedown', function (e) { e.preventDefault(); });
        box.addEventListener('click', function (e) {
            var v = e.target && e.target.getAttribute ? e.target.getAttribute('data-v') : null;
            if (!v) { return; }
            e.stopPropagation();
            el.value = v;
            fire(el);
            close();
        });
        mount(el, box);
        var selected = box.querySelector('.is-sel');
        if (!selected && curMin < 0) { selected = box.children[Math.min(box.children.length - 1, Math.floor(540 / step))]; }
        if (selected) { box.scrollTop = Math.max(0, selected.offsetTop - 60); }
    }

    /* ---------- Wiring ---------- */
    function isDate(t) {
        return t && t.classList && t.classList.contains('wppoppop-front-date') && !t._wppAirDp && t.tagName === 'INPUT';
    }

    function isTime(t) {
        return t && t.classList && t.classList.contains('wppoppop-input-time') && t.tagName === 'INPUT';
    }

    function usable(t) {
        return !t.disabled && !t.readOnly;
    }

    function openFor(t) {
        if (owner === t) { return; }
        if (isDate(t) && usable(t)) { openDate(t); }
        else if (isTime(t) && usable(t)) { openTime(t); }
    }

    document.addEventListener('click', function (e) {
        var t = e.target;
        if (pop && pop.contains(t)) { return; }
        if (isDate(t) || isTime(t)) {
            if (owner !== t) { openFor(t); }
            return;
        }
        if (pop) { close(); }
    }, true);

    document.addEventListener('keydown', function (e) {
        var t = e.target;
        var k = e.key || e.keyCode;
        if (k === 'Escape' || k === 'Esc' || k === 27) { if (pop) { close(); } return; }
        if ((k === 'ArrowDown' || k === 'F4' || k === 40) && (isDate(t) || isTime(t))) {
            if (e.altKey || isDate(t)) { e.preventDefault(); openFor(t); }
        }
    }, true);

    window.addEventListener('scroll', function (e) {
        if (pop && !(e.target && e.target.nodeType === 1 && pop.contains(e.target))) { place(); }
    }, true);
    window.addEventListener('resize', place);
})();
