/**
 * WpPopPop Settings: Default typography control sync (WPPOPPOP_DEFAULT_TYPO)
 */
(function ($) {
    'use strict';

    var HEX = /^#([0-9a-f]{3}|[0-9a-f]{6})$/i;

    function expand(v) {
        return v.length === 4 ? '#' + v[1] + v[1] + v[2] + v[2] + v[3] + v[3] : v;
    }

    $(function () {
        var $picker = $('#setting-default-font-color-picker');
        var $text = $('#setting-default-font-color');
        if (!$picker.length || !$text.length) { return; }

        $picker.on('input change', function () {
            $text.val(String($picker.val()).toLowerCase());
        });

        $text.on('input', function () {
            var v = $.trim($text.val());
            if (v && v.charAt(0) !== '#') { v = '#' + v; }
            if (HEX.test(v)) { $picker.val(expand(v)); }
        });

        $text.on('blur', function () {
            var v = $.trim($text.val());
            if (v && v.charAt(0) !== '#') { v = '#' + v; }
            $text.val(HEX.test(v) ? v.toLowerCase() : '');
        });
    });
})(jQuery);
