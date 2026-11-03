/**
 * WpPopPop Settings: Module tab (drag to reorder; number = position)
 */
(function ($) {
    'use strict';

    function renumber($list) {
        $list.children('li').each(function (i) {
            $(this).find('.mod-num').first().text((i + 1) + '.');
        });
    }

    $(function () {
        var $list = $('#wppoppop-mod-list');
        if (!$list.length) { return; }

        if ($.fn.sortable) {
            $list.sortable({
                handle: '.wppoppop-mod-handle',
                axis: 'y',
                cursor: 'grabbing',
                tolerance: 'pointer',
                forcePlaceholderSize: true,
                placeholder: 'wppoppop-mod-ph',
                update: function () { renumber($list); }
            });
        }

        // Safety net: the hidden module_order[] inputs follow DOM order at serialise time.
        $('#wppoppop-settings-form').on('submit', function () { renumber($list); });
    });
})(jQuery);
