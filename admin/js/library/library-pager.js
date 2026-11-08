/**
 * WpPopPop Library: pagination + 3x3 / 4x4 / 5x5 view (columns x rows).
 * Works on top of the existing search / category filter: it only pages the cards the filter leaves visible.
 * ES5 + jQuery: Chrome, Safari, Firefox, Edge.
 */
(function (window, $) {
    'use strict';

    var KEY = 'wppoppop_lib_view';
    var HIDE = 'wpp-pg-off';
    var cols = 3, page = 1, lastSig = '', timer = 0;

    function load() {
        try {
            var v = parseInt(window.localStorage.getItem(KEY), 10);
            if (v === 3 || v === 4 || v === 5) { cols = v; }
        } catch (e) {}
    }

    function save() {
        try { window.localStorage.setItem(KEY, String(cols)); } catch (e) {}
    }

    function $cards() { return $('.wppoppop-library-grid .wppoppop-tpl-card'); }

    /* cards the search / category filter keeps visible (the filter hides with an inline display:none) */
    function visible() {
        return $cards().filter(function () { return this.style.display !== 'none'; });
    }

    function t(k, d) {
        var c = window.wppoppop_libpg_vars;
        return (c && c.i18n && c.i18n[k]) || d;
    }

    function reset() { $cards().removeClass(HIDE); }

    function pageList(pages) {
        var out = [], i, lo = Math.max(2, page - 1), hi = Math.min(pages - 1, page + 1);
        out.push(1);
        if (lo > 2) { out.push(0); }
        for (i = lo; i <= hi; i++) { out.push(i); }
        if (hi < pages - 1) { out.push(0); }
        if (pages > 1) { out.push(pages); }
        return out;
    }

    function renderNav(pages) {
        var $n = $('#wpp-pg-nav'), list, i, h = '';
        if (!$n.length) { return; }
        if (pages <= 1) { $n.empty().hide(); return; }
        h += '<button type="button" class="wpp-pg-btn" data-page="' + (page - 1) + '"' + (page <= 1 ? ' disabled' : '') + ' aria-label="' + t('prev', 'Previous page') + '">&lsaquo;</button>';
        list = pageList(pages);
        for (i = 0; i < list.length; i++) {
            if (list[i] === 0) { h += '<span class="wpp-pg-gap">&hellip;</span>'; continue; }
            h += '<button type="button" class="wpp-pg-btn' + (list[i] === page ? ' is-current' : '') + '" data-page="' + list[i] + '"' + (list[i] === page ? ' aria-current="page"' : '') + '>' + list[i] + '</button>';
        }
        h += '<button type="button" class="wpp-pg-btn" data-page="' + (page + 1) + '"' + (page >= pages ? ' disabled' : '') + ' aria-label="' + t('next', 'Next page') + '">&rsaquo;</button>';
        $n.html(h).show();
    }

    function refresh(fromFilter) {
        var $grid = $('.wppoppop-library-grid'), per = cols * cols, $v, total, pages, from, to, sig;
        if (!$grid.length) { return; }
        $grid.attr('data-cols', cols);
        reset();
        $v = visible();
        total = $v.length;
        sig = $v.map(function () { return $(this).attr('data-id'); }).get().join(',');
        if (fromFilter && sig !== lastSig) { page = 1; }
        lastSig = sig;
        pages = Math.max(1, Math.ceil(total / per));
        if (page > pages) { page = pages; }
        if (page < 1) { page = 1; }
        from = (page - 1) * per;
        to = from + per;
        $v.each(function (i) { if (i < from || i >= to) { $(this).addClass(HIDE); } });
        $('.wpp-pg-view').each(function () {
            var on = parseInt($(this).attr('data-cols'), 10) === cols;
            $(this).toggleClass('is-active', on).attr('aria-pressed', on ? 'true' : 'false');
        });
        $('#wpp-pg-info').text(total ? (t('showing', 'Showing %1$s-%2$s of %3$s').replace('%1$s', from + 1).replace('%2$s', Math.min(to, total)).replace('%3$s', total)) : '');
        renderNav(pages);
    }

    function later() {
        window.clearTimeout(timer);
        timer = window.setTimeout(function () { refresh(false); }, 0);
    }

    window.WpPopPopLibPager = { reset: reset, refresh: refresh };

    $(function () {
        var grid = document.querySelector('.wppoppop-library-grid');
        if (!grid) { return; }
        load();
        $(document).on('click.wppPg', '.wpp-pg-view', function (e) {
            e.preventDefault();
            cols = parseInt($(this).attr('data-cols'), 10) || 3;
            page = 1;
            save();
            refresh(false);
        });
        $(document).on('click.wppPg', '.wpp-pg-btn', function (e) {
            e.preventDefault();
            if (this.disabled) { return; }
            page = parseInt($(this).attr('data-page'), 10) || 1;
            refresh(false);
            if (grid.scrollIntoView) { grid.scrollIntoView(true); }
        });
        if (window.MutationObserver) {
            /* a card was deleted / the empty notice toggled: re-page without leaving the current page */
            new window.MutationObserver(later).observe(grid, { childList: true });
        }
        refresh(true);
    });
})(window, jQuery);
