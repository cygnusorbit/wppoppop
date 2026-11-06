/* WpPopPop declarative modules: Inspector DOM <-> JS settings model.
 * Wraps getSettings / setSettings so every [data-ext-module] accordion is saved to and
 * restored from settings.ext_<id>, exactly like the built-in modules. */
(function ($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._extWrapped) { return; }
    S._extWrapped = true;

    function readField($el) {
        var t = $el.attr('data-ext-type');
        if (t === 'toggle') { return $el.is(':checked'); }
        if (t === 'number') {
            var n = parseFloat($el.val());
            return isNaN(n) ? parseFloat($el.attr('data-ext-default')) || 0 : n;
        }
        return $el.val();
    }

    function writeField($el, v) {
        var t = $el.attr('data-ext-type');
        if (t === 'toggle') { $el.prop('checked', v === true || v === 1 || v === '1' || v === 'true'); } else { $el.val(v === null || v === undefined ? '' : v); }
    }

    function defaultOf($el) {
        var t = $el.attr('data-ext-type'), d = $el.attr('data-ext-default');
        if (t === 'toggle') { return d === '1'; }
        if (t === 'number') { return parseFloat(d) || 0; }
        return d === undefined ? '' : d;
    }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function () {
        var out = origGet.apply(this, arguments) || {};
        $('[data-ext-module]').each(function () {
            var obj = {}, $fields = $(this).find('[data-ext-field]');
            if (!$fields.length) { return; }
            $fields.each(function () { obj[$(this).attr('data-ext-field')] = readField($(this)); });
            out['ext_' + $(this).attr('data-ext-module').replace(/^ext_/, '')] = obj;
        });
        return out;
    };

    S.setSettings = function (settings) {
        var r = origSet.apply(this, arguments);
        $('[data-ext-module]').each(function () {
            var id = $(this).attr('data-ext-module').replace(/^ext_/, '');
            var src = (settings && settings['ext_' + id] && typeof settings['ext_' + id] === 'object') ? settings['ext_' + id] : {};
            $(this).find('[data-ext-field]').each(function () {
                var k = $(this).attr('data-ext-field');
                writeField($(this), Object.prototype.hasOwnProperty.call(src, k) ? src[k] : defaultOf($(this)));
            });
        });
        return r;
    };
})(jQuery);
