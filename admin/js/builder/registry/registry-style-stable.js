/**
 * WpPopPop Builder: Stable Style-tab layout (no blinking on selection)
 * Lifecycle: Selection -> layout preset (synchronous, before the inspector re-renders) -> stylesheet-driven section visibility.
 * Sections of the Style tab are shown/hidden by one stylesheet keyed on #insp-tab-style[data-wpp-style-layout],
 * so the late jQuery show()/hide()/toggle() passes of the inspector can never flash a section on or off.
 * Visibility only: DOM, JS model, pre-save flush, AJAX whitelist and front renderer are untouched.
 */
(function(window, $) {
    'use strict';

    var ATTR = 'data-wpp-style-layout';
    var STYLE_ID = 'wppoppop-style-stable-layout';
    var PANE = '#insp-tab-style';

    var SEL = {
        formModule:  '#wppoppop-form-style-module',
        alignCard:   '#wppoppop-form-style-module > .wppoppop-insp-card:not(#wppoppop-form-icons-card)',
        iconsCard:   '#wppoppop-form-icons-card',
        textAlign:   '#insp-section-text-align',
        submitAlign: '#insp-section-submit-text-align',
        opacity:     '#insp-section-opacity',
        bg:          '#insp-section-bg-color',
        typo:        '#insp-section-typography',
        padding:     '#insp-section-padding',
        border:      '#insp-section-border',
        shadow:      '#insp-section-shadow',
        hover:       '#prop-btn-hover-section',
        css:         '#insp-section-custom-css'
    };
    var KEYS = Object.keys(SEL);

    var FORM_BASE = 'formModule alignCard iconsCard';
    var FORM_CHOICE = 'formModule alignCard';

    // Settled Style-tab layout per element type (sections listed here are visible, all others hidden).
    var LAYOUT = {
        shape:         'padding border shadow',
        image:         'opacity padding border shadow',
        label:         'textAlign bg typo padding border shadow',
        html:          'textAlign bg padding border shadow',
        video:         'padding border shadow',
        close_icon:    'bg border hover',
        icon:          'opacity bg border',
        progress:      'opacity bg typo padding border shadow css',
        button:        'bg typo padding border shadow hover',
        submit:        'formModule iconsCard submitAlign bg typo shadow hover css',
        text:          FORM_BASE,
        email:         FORM_BASE,
        number:        FORM_BASE,
        phone:         FORM_BASE,
        select:        FORM_BASE,
        textarea:      FORM_BASE,
        date:          FORM_BASE + ' typo border',
        time:          FORM_BASE + ' typo border',
        upload:        FORM_BASE + ' typo border',
        password:      FORM_BASE + ' typo border',
        checkboxes:    FORM_CHOICE + ' bg typo shadow',
        radios:        FORM_CHOICE + ' typo',
        checkboxgroup: FORM_CHOICE + ' typo',
        multiselect:   FORM_CHOICE + ' typo',
        imageselect:   FORM_CHOICE + ' typo',
        tile:          FORM_CHOICE + ' bg typo hover',
        rating:        FORM_CHOICE
    };

    var ALIAS = {
        rect: 'shape', rectangle: 'shape', square: 'shape',
        title: 'label', paragraph: 'label',
        close: 'close_icon',
        link: 'button', link_btn: 'button', step: 'button', step_btn: 'button',
        submit_btn: 'submit',
        textfield: 'text', tel: 'phone', dropdown: 'select', file: 'upload',
        checkbox: 'checkboxes', radio: 'radios'
    };

    function layoutKey(type) {
        var t = String(type || '').toLowerCase().trim();
        if (LAYOUT.hasOwnProperty(t)) return t;
        if (ALIAS.hasOwnProperty(t) && LAYOUT.hasOwnProperty(ALIAS[t])) return ALIAS[t];
        return '';
    }

    function buildCss() {
        var rules = [];
        Object.keys(LAYOUT).forEach(function(type) {
            var on = LAYOUT[type].split(' ');
            var shown = [], hidden = [];
            KEYS.forEach(function(k) {
                var sel = 'html ' + PANE + '[' + ATTR + '="' + type + '"] ' + SEL[k];
                (on.indexOf(k) !== -1 ? shown : hidden).push(sel);
            });
            if (hidden.length) rules.push(hidden.join(',\n') + ' { display: none !important; }');
            if (shown.length) rules.push(shown.join(',\n') + ' { display: block !important; }');
        });
        return rules.join('\n');
    }

    function ensureStyle() {
        var s = document.getElementById(STYLE_ID);
        if (s) return;
        s = document.createElement('style');
        s.id = STYLE_ID;
        s.appendChild(document.createTextNode(buildCss()));
        (document.head || document.documentElement).appendChild(s);
    }

    function cleanId(raw) {
        return String(raw || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').replace(/^layer-item-/, '').trim();
    }

    function typeById(id) {
        var core = window.WpPopPopBuilderCore;
        var clean = cleanId(id);
        if (!clean || !core || !core.state || !core.state.canvases) return '';
        var canvases = core.state.canvases;
        var order = [core.state.currentCanvas || 1];
        Object.keys(canvases).forEach(function(k) { if (String(k) !== String(order[0])) order.push(k); });
        for (var c = 0; c < order.length; c++) {
            var list = canvases[order[c]] || [];
            for (var i = 0; i < list.length; i++) {
                if (list[i] && cleanId(list[i].id) === clean) return list[i].type;
            }
        }
        return '';
    }

    function applyType(type) {
        var pane = document.getElementById('insp-tab-style');
        if (!pane) return;
        var key = layoutKey(type);
        if (key) {
            if (pane.getAttribute(ATTR) !== key) pane.setAttribute(ATTR, key);
        } else if (pane.hasAttribute(ATTR)) {
            pane.removeAttribute(ATTR);
        }
    }

    function applyId(id) {
        if (!id) return;
        var type = typeById(id);
        if (type) applyType(type);
    }

    // Runs in the capture phase, before any selection handler re-renders the inspector. Never blocks the event.
    function onPointer(e) {
        var t = e.target;
        if (!t || !t.closest) return;
        var node = t.closest('.wppoppop-canvas-item, .wppoppop-layer-item');
        if (!node) return;
        applyId(node.getAttribute('data-id') || node.getAttribute('data-layer-id') || node.id);
    }
    document.addEventListener('mousedown', onPointer, true);
    document.addEventListener('touchstart', onPointer, true);
    document.addEventListener('click', onPointer, true);

    function wrap(obj, name, flag) {
        if (!obj || typeof obj[name] !== 'function' || obj[flag]) return;
        var orig = obj[name];
        obj[name] = function(idOrObj) {
            applyId(idOrObj && typeof idOrObj === 'object' ? idOrObj.id : idOrObj);
            return orig.apply(this, arguments);
        };
        obj[flag] = true;
        obj[name].__wppStableOrig = orig;
    }

    function hook() {
        wrap(window.WpPopPopBuilderCanvas, 'selectElement', '__wppStableSelectHooked');
        if (typeof window.selectLayerEverywhere === 'function' && !window.selectLayerEverywhere.__wppStableHooked) {
            var origSel = window.selectLayerEverywhere;
            window.selectLayerEverywhere = function(idOrObj) {
                applyId(idOrObj && typeof idOrObj === 'object' ? idOrObj.id : idOrObj);
                return origSel.apply(this, arguments);
            };
            window.selectLayerEverywhere.__wppStableHooked = true;
        }
    }

    // Safety net for selection made without a pointer event (hotkeys, add element, undo/redo).
    var lastActive = null;
    function syncActive() {
        var core = window.WpPopPopBuilderCore;
        var active = core && core.state ? core.state.activeId : null;
        var type = active ? typeById(active) : '';
        var key = type ? layoutKey(type) : '';
        var pane = document.getElementById('insp-tab-style');
        var current = pane ? pane.getAttribute(ATTR) : null;
        if (active !== lastActive || (key && current !== key)) {
            lastActive = active;
            if (type) applyType(type);
        }
    }

    ensureStyle();
    hook();
    $(function() {
        ensureStyle();
        hook();
        syncActive();
        setTimeout(hook, 150);
        setTimeout(hook, 600);
        setTimeout(hook, 1500);
        setInterval(syncActive, 250);
    });
})(window, jQuery);
