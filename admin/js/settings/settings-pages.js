/**
 * WpPopPop Settings: Pages tab (rename / reorder / hide admin menu pages)
 */
(function ($) {
    'use strict';

    var $list;

    function reindex() {
        var $items = $list.children('li');
        $items.each(function (i) {
            var $li = $(this);
            $li.find('.wpp-pg-order').val(i + 1);
            $li.find('.wpp-pg-up').prop('disabled', i === 0);
            $li.find('.wpp-pg-down').prop('disabled', i === $items.length - 1);
            $li.toggleClass('is-hidden-page', $li.find('.wpp-pg-hidden-cb').is(':checked'));
        });
    }

    function init() {
        $list = $('#wppoppop-pages-list');
        if (!$list.length) { return; }

        if ($.fn.sortable) {
            $list.sortable({
                handle: '.wpp-pg-handle',
                axis: 'y',
                cursor: 'move',
                update: reindex
            });
        }

        $list.on('click', '.wpp-pg-up', function (e) {
            e.preventDefault();
            var $li = $(this).closest('li');
            var $prev = $li.prev('li');
            if ($prev.length) { $prev.before($li); reindex(); }
        });

        $list.on('click', '.wpp-pg-down', function (e) {
            e.preventDefault();
            var $li = $(this).closest('li');
            var $next = $li.next('li');
            if ($next.length) { $next.after($li); reindex(); }
        });

        $list.on('change', '.wpp-pg-hidden-cb', reindex);

        $('#wppoppop-pages-reset').on('click', function (e) {
            e.preventDefault();
            var items = $list.children('li').get();
            items.sort(function (a, b) {
                return parseInt($(a).attr('data-idx'), 10) - parseInt($(b).attr('data-idx'), 10);
            });
            $.each(items, function (_, li) {
                var $li = $(li);
                $li.find('.wpp-pg-label').val('');
                $li.find('.wpp-pg-hidden-cb').prop('checked', false);
                $list.append($li);
            });
            reindex();
        });

        // Safety net: flush order right before the AJAX save serialises the form.
        $('#wppoppop-settings-form').on('submit', reindex);

        reindex();
    }

    $(init);
})(jQuery);
