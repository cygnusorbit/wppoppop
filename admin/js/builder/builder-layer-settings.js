/**
 * WpPopPop Builder: Layer Settings (per-element enable / disable of Inspector settings).
 * Source: Settings > Builder > Element / Layer Settings (wppoppop_vars.layer_settings).
 * Visibility only: the Inspector DOM, JS model, pre-save flush, AJAX whitelist and front renderer are untouched,
 * so a disabled setting keeps whatever value the layer already stores.
 * Sections are shown / hidden by one stylesheet keyed on the drawer's data-wpp-ls-type attribute, and the
 * tab buttons are re-asserted after the Inspector hydrates, so nothing flashes on selection.
 */
(function (window, $) {
    'use strict';

    var cfg = window.wppoppop_vars && window.wppoppop_vars.layer_settings;
    if (!cfg || !cfg.overrides || !cfg.selectors || !cfg.tabs) { return; }

    var overrides = cfg.overrides;
    var hasOverrides = false;
    var k;
    for (k in overrides) { if (Object.prototype.hasOwnProperty.call(overrides, k)) { hasOverrides = true; break; } }
    if (!hasOverrides) { return; }

    var ATTR = 'data-wpp-ls-type';
    var STYLE_ID = 'wppoppop-layer-settings-css';
    var DRAWER = '#wppoppop-inspector-drawer';
    var TAB_KEYS = ['basic', 'style', 'logic', 'data', 'advanced'];
    var FORM = ['text', 'email', 'number', 'phone', 'textarea', 'select', 'checkbox', 'checkboxgroup', 'radio', 'multiselect', 'imageselect', 'tile', 'date', 'time', 'upload', 'password', 'rating'];
    var ALIAS = {
        rect: 'shape', rectangle: 'shape', square: 'shape',
        title: 'label', paragraph: 'label',
        close_icon: 'close',
        link: 'button', link_btn: 'button', step: 'button', step_btn: 'button',
        submit_btn: 'submit',
        textfield: 'text', spinner: 'number', tel: 'phone', dropdown: 'select', file: 'upload',
        checkboxes: 'checkbox', radios: 'radio'
    };

    function canonical(raw) {
        var t = String(raw || '').toLowerCase().replace(/^\s+|\s+$/g, '');
        if (ALIAS.hasOwnProperty(t)) { t = ALIAS[t]; }
        return cfg.tabs.hasOwnProperty(t) ? t : '';
    }

    function buildCss() {
        var rules = [];
        Object.keys(overrides).forEach(function (type) {
            var tabs = overrides[type] || {};
            Object.keys(tabs).forEach(function (tab) {
                var keys = tabs[tab] || {};
                Object.keys(keys).forEach(function (key) {
                    var meta = cfg.selectors[tab] && cfg.selectors[tab][key];
                    if (!meta || !meta.s) { return; }
                    var decl = 'display:' + (parseInt(keys[key], 10) === 1 ? (meta.d || 'block') : 'none') + ' !important;';
                    var list = String(meta.s).replace(/\{type\}/g, type).split(',').map(function (p) {
                        return 'html body ' + DRAWER + '[' + ATTR + '="' + type + '"] ' + p.replace(/^\s+|\s+$/g, '');
                    });
                    rules.push(list.join(',\n') + '{' + decl + '}');
                });
            });
        });
        return rules.join('\n');
    }

    function ensureStyle() {
        if (document.getElementById(STYLE_ID)) { return; }
        var s = document.createElement('style');
        s.id = STYLE_ID;
        s.appendChild(document.createTextNode(buildCss()));
        (document.head || document.documentElement).appendChild(s);
    }

    function cleanId(raw) {
        return String(raw || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').replace(/^layer-item-/, '').replace(/^\s+|\s+$/g, '');
    }

    function typeById(id) {
        var core = window.WpPopPopBuilderCore;
        var clean = cleanId(id);
        if (!clean || !core || !core.state || !core.state.canvases) { return ''; }
        var canvases = core.state.canvases;
        var order = [core.state.currentCanvas || 1];
        Object.keys(canvases).forEach(function (c) { if (String(c) !== String(order[0])) { order.push(c); } });
        for (var c = 0; c < order.length; c++) {
            var list = canvases[order[c]] || [];
            for (var i = 0; i < list.length; i++) {
                if (list[i] && cleanId(list[i].id) === clean) { return list[i].type; }
            }
        }
        return '';
    }

    function drawer() { return document.getElementById('wppoppop-inspector-drawer'); }

    function applyType(rawType) {
        var type = canonical(rawType);
        var d = drawer();
        if (!d) { return ''; }
        if (type) {
            if (d.getAttribute(ATTR) !== type) { d.setAttribute(ATTR, type); }
        } else if (d.hasAttribute(ATTR)) {
            d.removeAttribute(ATTR);
        }
        return type;
    }

    function currentType() {
        var d = drawer();
        return d ? (d.getAttribute(ATTR) || '') : '';
    }

    // Tab buttons: hide a tab whose settings are all disabled; show Data / Advanced on a non-form element once enabled.
    function syncTabs(type) {
        var state = type ? cfg.tabs[type] : null;
        if (!state) { return; }
        var isForm = FORM.indexOf(type) !== -1;
        var $bar = $('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs');
        var activeHidden = false;
        var activeTab = $bar.find('.wppoppop-insp-tab.active').attr('data-tab') || 'basic';

        TAB_KEYS.forEach(function (tab) {
            var on = state[tab] !== false;
            var nativeOn = (tab === 'data' || tab === 'advanced') ? isForm : true;
            $bar.find('[data-tab="' + tab + '"]').each(function () {
                var el = this;
                if (on !== nativeOn) {
                    if (!el.hasAttribute('data-wpp-ls-set')) {
                        el.setAttribute('data-wpp-ls-set', '1');
                        el.setAttribute('data-wpp-ls-prev', el.style.getPropertyValue('display') + '|' + el.style.getPropertyPriority('display'));
                    }
                    el.style.setProperty('display', on ? 'inline-block' : 'none', 'important');
                } else if (el.hasAttribute('data-wpp-ls-set')) {
                    var prev = (el.getAttribute('data-wpp-ls-prev') || '|').split('|');
                    el.removeAttribute('data-wpp-ls-set');
                    el.removeAttribute('data-wpp-ls-prev');
                    if (prev[0]) { el.style.setProperty('display', prev[0], prev[1] || ''); }
                    else { el.style.removeProperty('display'); }
                }
            });
            if (tab === activeTab && !on) { activeHidden = true; }
        });

        if (activeHidden) {
            for (var i = 0; i < TAB_KEYS.length; i++) {
                if (state[TAB_KEYS[i]] !== false) {
                    var insp = window.WpPopPopBuilderInspector;
                    if (insp && typeof insp.switchTab === 'function') { insp.switchTab(TAB_KEYS[i]); }
                    else { $bar.find('[data-tab="' + TAB_KEYS[i] + '"]').first().trigger('click'); }
                    break;
                }
            }
        }
    }

    function applyId(id) {
        if (!id) { return; }
        var type = typeById(id);
        if (type) { applyType(type); }
    }

    function settle() {
        syncTabs(currentType());
        setTimeout(function () { syncTabs(currentType()); }, 0);
        setTimeout(function () { syncTabs(currentType()); }, 120);
        setTimeout(function () { syncTabs(currentType()); }, 400);
    }

    // Capture phase: runs before the selection handlers re-render the Inspector. Never blocks the event.
    function onPointer(e) {
        var t = e.target;
        if (!t || !t.closest) { return; }
        var node = t.closest('.wppoppop-canvas-item, .wppoppop-layer-item');
        if (!node) { return; }
        applyId(node.getAttribute('data-id') || node.getAttribute('data-layer-id') || node.id);
    }
    document.addEventListener('mousedown', onPointer, true);
    document.addEventListener('touchstart', onPointer, true);
    document.addEventListener('click', onPointer, true);

    function wrap(obj, name, flag) {
        if (!obj || typeof obj[name] !== 'function' || obj[flag]) { return; }
        var orig = obj[name];
        obj[name] = function (idOrObj) {
            applyId(idOrObj && typeof idOrObj === 'object' ? idOrObj.id : idOrObj);
            var res = orig.apply(this, arguments);
            settle();
            return res;
        };
        obj[flag] = true;
    }

    function hook() {
        wrap(window.WpPopPopBuilderCanvas, 'selectElement', '__wppLsSelectHooked');
        wrap(window.WpPopPopBuilderInspector, 'open', '__wppLsOpenHooked');
        if (typeof window.selectLayerEverywhere === 'function' && !window.selectLayerEverywhere.__wppLsHooked) {
            var origSel = window.selectLayerEverywhere;
            window.selectLayerEverywhere = function (idOrObj) {
                applyId(idOrObj && typeof idOrObj === 'object' ? idOrObj.id : idOrObj);
                var res = origSel.apply(this, arguments);
                settle();
                return res;
            };
            window.selectLayerEverywhere.__wppLsHooked = true;
        }
    }

    // Safety net for selection made without a pointer event (hotkeys, add element, undo / redo).
    var lastActive = null;
    function syncActive() {
        var core = window.WpPopPopBuilderCore;
        var active = core && core.state ? core.state.activeId : null;
        if (!active) { return; }
        var type = typeById(active);
        if (!type) { return; }
        var changed = active !== lastActive || canonical(type) !== currentType();
        lastActive = active;
        if (changed) { applyType(type); }
        syncTabs(currentType());
    }

    ensureStyle();
    hook();
    $(function () {
        ensureStyle();
        hook();
        syncActive();
        setTimeout(hook, 150);
        setTimeout(hook, 600);
        setTimeout(hook, 1500);
        setInterval(syncActive, 250);
    });
})(window, jQuery);
