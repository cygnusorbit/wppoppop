/**
 * WpPopPop Settings: Module tab. WPP_MODULES_UI
 * Drag to reorder (number = position), search, All / Active / Inactive filters,
 * Uninstall / Delete via the shared modules API.
 */
(function ($) {
    'use strict';

    var M = window.WpPopPopModules || null;

    $(function () {
        var $list = $('#wppoppop-mod-list');
        if (!$list.length) { return; }

        var $filters = $('#wpp-mod-filters');
        var $search = $('#wpp-mod-search');
        var $empty = $('#wpp-mod-empty');
        var $notice = $('#wpp-mod-notice');
        var filter = 'all';
        var query = '';

        function rows() { return $list.children('li.wpp-mod-row'); }

        function renumber() {
            rows().each(function (i) { $(this).find('.mod-num').first().text((i + 1) + '.'); });
        }

        // Locked rows have a disabled checkbox and always count as active.
        function isActive($li) {
            var $cb = $li.find('input[type=checkbox]').first();
            return $cb.prop('checked') || $cb.prop('disabled');
        }

        function counts() {
            var all = 0, on = 0;
            rows().each(function () { all++; if (isActive($(this))) { on++; } });
            var n = { all: all, active: on, inactive: all - on };
            $filters.find('button').each(function () {
                $(this).find('.count').text('(' + n[String($(this).data('filter'))] + ')');
            });
        }

        function apply() {
            var shown = 0;
            rows().each(function () {
                var $li = $(this);
                var on = isActive($li);
                var ok = filter === 'all' || (filter === 'active' ? on : !on);
                if (ok && query) {
                    ok = String($li.attr('data-search') || '').toLowerCase().indexOf(query) !== -1;
                }
                $li.prop('hidden', !ok);
                if (ok) { shown++; }
            });
            $empty.prop('hidden', shown > 0);
            if ($.fn.sortable && $list.data('ui-sortable')) {
                $list.sortable('option', 'disabled', filter !== 'all' || query !== '');
            }
        }

        function refresh() { renumber(); counts(); apply(); }

        if ($.fn.sortable) {
            $list.sortable({
                handle: '.wppoppop-mod-handle',
                axis: 'y',
                cursor: 'grabbing',
                tolerance: 'pointer',
                forcePlaceholderSize: true,
                placeholder: 'wppoppop-mod-ph',
                update: renumber
            });
        }

        $filters.on('click', 'button', function () {
            filter = String($(this).data('filter'));
            $filters.find('button').removeClass('is-active');
            $(this).addClass('is-active');
            apply();
        });

        $search.on('input', function () {
            query = String($search.val() || '').replace(/^\s+|\s+$/g, '').toLowerCase();
            apply();
        });
        // The search box sits inside the settings form: Enter must not submit it.
        $search.on('keydown', function (e) { if (e.which === 13) { e.preventDefault(); } });

        $list.on('change', 'input[type=checkbox]', function () { counts(); apply(); });

        $list.on('click', '[data-act]', function (e) {
            e.preventDefault();
            if (!M) { return; }
            var $li = $(this).closest('li.wpp-mod-row');
            var action = String($(this).data('act'));
            M.act(action, { slug: String($li.data('module')), name: String($li.data('name')) }, {
                busy: function (on) { $li.toggleClass('is-busy', on); },
                done: function (r) {
                    if (!r.ok) { M.notice($notice, 'error', r.message); return; }
                    $li.remove();
                    refresh();
                    M.notice($notice, 'success', r.data.message, { href: M.vars.extend_url, text: M.t('extend_link') });
                }
            });
        });

        // Safety net: the hidden module_order[] inputs follow DOM order at serialise time.
        $('#wppoppop-settings-form').on('submit', renumber);

        refresh();
    });
})(jQuery);
