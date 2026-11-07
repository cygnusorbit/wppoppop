/* Side Tabs: Inspector DOM <-> JS model.
 * Wraps getSettings / setSettings so the tab is saved to and restored from settings.sidetab ({ enable, label, pos }).
 * Sanitising on save and the front-end attributes stay in the core (WpPopPop_Side_Tabs); the tab is built by module.js.
 * Only loaded while the module is installed. ES5 only (Chrome, Safari, Firefox, Edge). */
/* WPPOPPOP_SIDE_TABS_UI: Side Tabs module (Inspector DOM <-> settings.sidetab) */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._sideTabsWrapped) return;
    S._sideTabsWrapped = true;

    function isTrue(v) { return v === true || v === 1 || v === '1' || v === 'true' || v === 'on'; }
    function pos(v) { return v === 'right' ? 'right' : 'left'; }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        out.sidetab = {
            enable: $('#set-sidetab-enable').is(':checked'),
            label: $('#set-sidetab-label').val() || '',
            pos: pos($('#set-sidetab-pos').val())
        };
        return out;
    };

    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        var t = (settings && settings.sidetab && typeof settings.sidetab === 'object') ? settings.sidetab : {};
        $('#set-sidetab-enable').prop('checked', isTrue(t.enable));
        $('#set-sidetab-label').val(t.label || '');
        $('#set-sidetab-pos').val(pos(t.pos));
        return r;
    };
})(jQuery);
