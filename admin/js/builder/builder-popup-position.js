/**
 * WpPopPop Builder: Popup Positions module (Popup Settings > Popup Positions)
 * Data lifecycle: inspector DOM <-> JS model <-> getSettings()/setSettings() <-> save payload.
 * The model (not the DOM) is the source of truth, so a switched-off module never wipes saved values.
 * Cross-browser: ES5 only (Chrome, Safari, Firefox, Edge).
 */
(function ($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._popupPositionWrapped) { return; }
    S._popupPositionWrapped = true;

    var POSITIONS = ['top-left', 'top-center', 'top-right', 'middle-left', 'center', 'middle-right', 'bottom-left', 'bottom-center', 'bottom-right'];
    var SIDES = ['top', 'right', 'bottom', 'left'];
    var MAX_PAD = 1000;
    var DEFAULT_PAD = 50; // WPPOPPOP_PP_DEFAULT_PAD
    var model = blank();

    function blank() { return { position: 'center', top: DEFAULT_PAD, right: DEFAULT_PAD, bottom: DEFAULT_PAD, left: DEFAULT_PAD }; }
    function normPos(v) { return $.inArray(v, POSITIONS) !== -1 ? v : 'center'; }
    function normPad(v) {
        v = parseInt(v, 10);
        if (isNaN(v)) { v = DEFAULT_PAD; }
        if (v < 0) { v = 0; }
        return Math.min(MAX_PAD, v);
    }

    function selectedLabel() {
        return $('#wppoppop-pp-grid .wppoppop-pp-cell[data-pos="' + model.position + '"]').attr('data-label') || '';
    }

    function paint() {
        $('#wppoppop-pp-grid .wppoppop-pp-cell').each(function () {
            var on = $(this).attr('data-pos') === model.position;
            $(this).toggleClass('is-selected', on).attr('aria-checked', on ? 'true' : 'false');
        });
        $('#wppoppop-pp-name').text(selectedLabel());
        $.each(SIDES, function (i, s) { $('#set-pp-' + s).val(model[s]); });
    }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function () {
        var out = origGet.apply(this, arguments) || {};
        out.popup_position = $.extend({}, model);
        return out;
    };

    S.setSettings = function (settings) {
        var r = origSet.apply(this, arguments);
        var src = (settings && settings.popup_position && typeof settings.popup_position === 'object') ? settings.popup_position : {};
        model = blank();
        model.position = normPos(src.position);
        $.each(SIDES, function (i, s) { model[s] = normPad(src[s]); });
        paint();
        return r;
    };

    $(document)
        .off('.wppPos')
        .on('click.wppPos', '#wppoppop-pp-grid .wppoppop-pp-cell', function (e) {
            e.preventDefault();
            model.position = normPos($(this).attr('data-pos'));
            paint();
            $('#wppoppop-settings-drawer').trigger('change');
        })
        .on('mouseenter.wppPos focus.wppPos', '#wppoppop-pp-grid .wppoppop-pp-cell', function () {
            $('#wppoppop-pp-name').text($(this).attr('data-label') || '');
        })
        .on('mouseleave.wppPos blur.wppPos', '#wppoppop-pp-grid .wppoppop-pp-cell', function () {
            $('#wppoppop-pp-name').text(selectedLabel());
        })
        .on('input.wppPos change.wppPos', '#set-pp-top, #set-pp-right, #set-pp-bottom, #set-pp-left', function (e) {
            var side = this.id.replace('set-pp-', '');
            model[side] = normPad($(this).val());
            if (e.type === 'change') { $(this).val(model[side]); }
        })
        .on('click.wppPos', '#set-pp-edge', function (e) {
            e.preventDefault();
            $.each(SIDES, function (i, s) { model[s] = 0; });
            paint();
            $('#wppoppop-settings-drawer').trigger('change');
        });

    $(function () { paint(); });
})(jQuery);
