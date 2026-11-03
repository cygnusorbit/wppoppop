/* WPPOPPOP_BTN_CANVAS_SELECT: Button > Click Action "Go to Canvas Step" > TARGET CANVAS lists the available canvases. */
(function (window, $) {
    'use strict';
    if (!$) { return; }

    var BTN = ['button', 'link', 'link_btn'];
    var SEL = '#prop-goto-canvas';

    function core() { return window.WpPopPopBuilderCore || null; }
    function cid(v) { return String(v || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim(); }

    function activeEl() {
        var c = core();
        if (!c || !c.state) { return null; }
        var id = cid(c.state.activeId);
        if (!id) { return null; }
        var list = (c.state.canvases && c.state.canvases[c.state.currentCanvas || 1]) || [];
        for (var i = 0; i < list.length; i++) {
            if (list[i] && cid(list[i].id) === id) { return list[i]; }
        }
        return null;
    }
    function isBtn(el) { return !!el && BTN.indexOf(String(el.type || '').toLowerCase()) !== -1; }

    function info() {
        var c = core(), st = (c && c.state) || {};
        var seen = {}, list = [];
        [st.canvases, st.canvasMeta].forEach(function (o) {
            if (o && typeof o === 'object') {
                Object.keys(o).forEach(function (k) {
                    var n = parseInt(k, 10);
                    if (n > 0 && !seen[n]) { seen[n] = 1; list.push(n); }
                });
            }
        });
        if (!list.length) { list = [1]; }
        list.sort(function (a, b) { return a - b; });
        return { list: list, current: parseInt(st.currentCanvas, 10) || 1, meta: st.canvasMeta || {} };
    }

    function label(n, meta) {
        var m = meta && meta[n], nm = m && (m.name || m.title || m.label);
        return 'Canvas ' + n + (nm ? ' \u2013 ' + nm : '');
    }

    function currentTarget(el) {
        return parseInt(el && (el.goto_canvas || el.goto_screen || el.target_canvas), 10) || 0;
    }

    function build(want) {
        var $s = $(SEL);
        if (!$s.length || !$s.is('select')) { return 0; }
        var i = info();
        var others = i.list.filter(function (n) { return n !== i.current; });
        var list = i.list.slice();
        var missing = want > 0 && list.indexOf(want) === -1;
        if (missing) { list.push(want); list.sort(function (a, b) { return a - b; }); }

        if (!(want > 0) || (want === i.current && others.length)) {
            var next = others.filter(function (n) { return n > i.current; })[0];
            want = next || others[0] || 0;
        }

        var sig = list.join(',') + '|' + i.current + '|' + JSON.stringify(list.map(function (n) { return label(n, i.meta); }));
        if ($s.data('wppSig') !== sig) {
            var h = '';
            list.forEach(function (n) {
                var isCur = (n === i.current);
                var txt = label(n, i.meta) + (isCur ? ' (this canvas)' : '') + (missing && n === want ? ' (missing)' : '');
                h += '<option value="' + n + '"' + (isCur ? ' disabled' : '') + '>' + $('<div>').text(txt).html() + '</option>';
            });
            if (!others.length) { h = '<option value="" disabled>Add another canvas first</option>' + h; }
            $s.html(h).data('wppSig', sig);
        }
        $s.val(want > 0 ? String(want) : '');
        return want;
    }

    function hydrate() {
        var el = activeEl();
        if (!isBtn(el)) { return; }
        var want = build(currentTarget(el));
        var a = el.buttonAction || $('#prop-button-action').val();
        if ((a === 'step' || a === 'goto_canvas') && want > 0 && currentTarget(el) !== want) {
            el.goto_canvas = want; el.goto_screen = want; el.target_canvas = want;
        }
    }

    function sched() { [60, 220, 450, 900, 1400].forEach(function (ms) { setTimeout(hydrate, ms); }); }

    $(document).off('mousedown.wppBtnCv focus.wppBtnCv', SEL).on('mousedown.wppBtnCv focus.wppBtnCv', SEL, function () {
        var el = activeEl();
        if (isBtn(el)) { build(currentTarget(el) || parseInt($(this).val(), 10) || 0); }
    });

    $(document).off('change.wppBtnCv', SEL).on('change.wppBtnCv', SEL, function () {
        var el = activeEl();
        if (!isBtn(el)) { return; }
        var v = parseInt($(this).val(), 10) || 0;
        if (v > 0) {
            el.goto_canvas = v; el.goto_screen = v; el.target_canvas = v;
            var c = core();
            if (c && typeof c.pushHistory === 'function') { c.pushHistory('Change Button Target Canvas'); }
        }
    });

    $(document).off('change.wppBtnCvAct', '#prop-button-action, #prop-logic-click-action').on('change.wppBtnCvAct', '#prop-button-action, #prop-logic-click-action', function () {
        if ($(this).val() === 'step' || $(this).val() === 'goto_canvas') { setTimeout(hydrate, 0); setTimeout(hydrate, 80); }
    });

    $(document).off('click.wppBtnCvHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-screen-tab, .wppoppop-canvas-tab, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-tool')
        .on('click.wppBtnCvHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-screen-tab, .wppoppop-canvas-tab, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-tool', sched);

    $(function () { sched(); });
})(window, window.jQuery);
