/**
 * WpPopPop Builder: Style section slide-toggles (Border & Corner Radius, Box Shadow)
 * Lifecycle: Inspector DOM -> JS model (el.borderEnabled / el.shadowEnabled) -> stage -> AJAX whitelist -> front renderer.
 */
(function(window, $) {
    'use strict';
    // The registry global is replaced by registry-core.js after this file loads, so always read it lazily.
    function R() { return window.WpPopPopElementsRegistry || {}; }
    var MAP = {
        'prop-border-enabled': { prop: 'borderEnabled', sec: '#insp-section-border' },
        'prop-shadow-enabled': { prop: 'shadowEnabled', sec: '#insp-section-shadow' }
    };

    function isOn(v) { return !(v === false || v === 'false' || v === 0 || v === '0'); }
    function getEl() { var Reg = R(); return (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null; }

    function paint(id, on) {
        var m = MAP[id];
        $('#' + id).prop('checked', on);
        $(m.sec).toggleClass('wppoppop-sec-off', !on);
    }

    function hydrate(el) {
        el = el || getEl();
        if (!el) return;
        Object.keys(MAP).forEach(function(id) { paint(id, isOn(el[MAP[id].prop])); });
    }

    function rerender(el) {
        var Reg = R();
        var raw = String(el.type || '').toLowerCase();
        var can = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[raw]) ? Reg.ALIAS_MAP[raw] : raw;
        var isForm = Reg.FORM_FAMILY && Reg.FORM_FAMILY.indexOf(can) !== -1;
        if (isForm && typeof Reg.renderFormElementStage === 'function') { Reg.renderFormElementStage(el); }
        else if (typeof Reg.renderMediaElementStage === 'function') { Reg.renderMediaElementStage(el); }
        else if (typeof Reg.applyStyleToStageNode === 'function') { Reg.applyStyleToStageNode(el); }
        if (typeof Reg.applyStyleToStageNode === 'function') { Reg.applyStyleToStageNode(el); }
    }

    function bind() {
        $(document).off('change.wppoppopStyleToggle', '#prop-border-enabled, #prop-shadow-enabled')
            .on('change.wppoppopStyleToggle', '#prop-border-enabled, #prop-shadow-enabled', function() {
                var el = getEl();
                if (!el) return;
                var on = $(this).is(':checked');
                el[MAP[this.id].prop] = on;
                paint(this.id, on);
                rerender(el);
                if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });

        $(document).off('click.wppoppopStyleToggleHyd', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]')
            .on('click.wppoppopStyleToggleHyd', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]', function() {
                [60, 250, 600].forEach(function(ms) { setTimeout(function() { hydrate(); }, ms); });
            });

        var Reg = R();
        if (typeof Reg.hydrateCoreAndStyleFields === 'function' && !Reg._styleToggleHooked) {
            Reg._styleToggleHooked = true;
            var orig = Reg.hydrateCoreAndStyleFields;
            Reg.hydrateCoreAndStyleFields = function(el) { var r = orig.apply(this, arguments); hydrate(el); return r; };
        }
    }

    // Flip the Shape style layout before the inspector re-renders so hidden cards never flash on selection.
    function presetShapeStyle(e) {
        var node = e.target && e.target.closest ? e.target.closest('.wppoppop-canvas-item, .wppoppop-layer-item') : null;
        var id = node && node.getAttribute('data-id');
        if (!id) return;
        var core = window.WpPopPopBuilderCore;
        var list = (core && core.state && core.state.canvases && core.state.canvases[core.state.currentCanvas || 1]) || [];
        for (var i = 0; i < list.length; i++) {
            if (list[i] && list[i].id === id) {
                $('#insp-tab-style').toggleClass('wppoppop-shape-style', String(list[i].type || '').toLowerCase() === 'shape');
                return;
            }
        }
    }
    document.addEventListener('mousedown', presetShapeStyle, true);
    document.addEventListener('click', presetShapeStyle, true);

    $(function() { bind(); setTimeout(bind, 150); });
    bind();
})(window, jQuery);
