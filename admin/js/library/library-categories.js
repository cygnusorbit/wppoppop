/**
 * WpPopPop Library: category manager (add / edit / remove / reposition).
 * jQuery UI Sortable + up/down buttons. ES5 for Chrome, Safari, Firefox, Edge.
 */
(function (window, $) {
    'use strict';

    var DEFAULTS = [
        { slug: 'lead-gen', label: 'Lead Generation' },
        { slug: 'ecommerce', label: 'E-Commerce & Coupons' },
        { slug: 'gamified', label: 'Gamified & Interactive' },
        { slug: 'feedback', label: 'Surveys & Feedback' },
        { slug: 'announcement', label: 'Announcements' },
        { slug: 'custom', label: 'My Templates' }
    ];

    function vars() {
        return window.wppoppop_libcat_vars || { ajax_url: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), nonce: '' };
    }
    function $m() { return $('#wppoppop-lib-cats-modal'); }
    function $list() { return $('#wppoppop-lc-list'); }

    function msg(text, ok) {
        $('#wppoppop-lc-msg').css('color', ok ? '#008a20' : '#b32d2e').text(text).show();
    }

    function row(c) {
        var locked = c.slug === 'custom';
        var $r = $('<li class="lc-row"></li>').attr('data-slug', c.slug);
        $r.append('<span class="lc-grip" title="Drag to reorder" aria-hidden="true">&#8801;</span>');
        $r.append($('<input type="text" maxlength="40" class="lc-label">').val(c.label));
        $r.append('<button type="button" class="lc-btn lc-up" title="Move up" aria-label="Move up">&#9650;</button>');
        $r.append('<button type="button" class="lc-btn lc-down" title="Move down" aria-label="Move down">&#9660;</button>');
        if (locked) {
            $r.append('<span class="lc-lock" title="Holds your saved templates">protected</span>');
        } else {
            $r.append('<button type="button" class="lc-btn lc-del" title="Remove" aria-label="Remove">&times;</button>');
        }
        return $r;
    }

    function render(cats) {
        var $l = $list().empty(), i;
        for (i = 0; i < cats.length; i++) { $l.append(row(cats[i])); }
        if ($l.data('ui-sortable') || $l.hasClass('ui-sortable')) { $l.sortable('destroy'); }
        if ($.fn.sortable) {
            $l.sortable({ handle: '.lc-grip', axis: 'y', tolerance: 'pointer', placeholder: 'lc-ph', forcePlaceholderSize: true });
        }
    }

    function collect() {
        var out = [];
        $list().children('.lc-row').each(function () {
            var label = $.trim($(this).find('.lc-label').val());
            if (label) { out.push({ slug: $(this).attr('data-slug') || '', label: label }); }
        });
        return out;
    }

    function currentFromPills() {
        var out = [];
        $('.wppoppop-library-categories .wppoppop-cat-btn').each(function () {
            var slug = $(this).attr('data-cat');
            if (slug && slug !== 'all') { out.push({ slug: slug, label: $.trim($(this).text()) }); }
        });
        return out;
    }

    function open() {
        $('#wppoppop-lc-msg').hide().text('');
        $('#wppoppop-lc-new').val('');
        render(currentFromPills());
        $m().css('display', 'flex');
        $('#wppoppop-lc-new').focus();
    }
    function close() { $m().hide(); }

    function addNew() {
        var label = $.trim($('#wppoppop-lc-new').val());
        if (!label) { return; }
        if ($list().children('.lc-row').length >= 30) { msg('Maximum 30 categories.', false); return; }
        var $r = row({ slug: '', label: label });
        $list().append($r);
        $('#wppoppop-lc-new').val('').focus();
        $('#wppoppop-lc-msg').hide();
    }

    function save() {
        var cats = collect(), v = vars(), $b = $('#wppoppop-lc-save');
        $b.prop('disabled', true);
        $.ajax({
            url: v.ajax_url, type: 'POST', dataType: 'json',
            data: { action: 'wppoppop_library_cats_save', nonce: v.nonce, cats: JSON.stringify(cats) },
            success: function (res) {
                if (res && res.success) {
                    msg('Saved. Reloading...', true);
                    window.location.reload();
                } else {
                    msg((res && res.data && res.data.message) || 'Save failed.', false);
                    $b.prop('disabled', false);
                }
            },
            error: function () { msg('Server error while saving.', false); $b.prop('disabled', false); }
        });
    }

    $(function () {
        $(document)
            .on('click.wppLc', '#wppoppop-lib-cats-btn', function (e) { e.preventDefault(); open(); })
            .on('click.wppLc', '#wppoppop-lc-close, #wppoppop-lc-cancel', function (e) { e.preventDefault(); close(); })
            .on('click.wppLc', '#wppoppop-lib-cats-modal', function (e) { if (e.target === this) { close(); } })
            .on('click.wppLc', '#wppoppop-lc-add', function (e) { e.preventDefault(); addNew(); })
            .on('keydown.wppLc', '#wppoppop-lc-new', function (e) { if (e.keyCode === 13) { e.preventDefault(); addNew(); } })
            .on('click.wppLc', '#wppoppop-lc-save', function (e) { e.preventDefault(); save(); })
            .on('click.wppLc', '#wppoppop-lc-reset', function (e) {
                e.preventDefault();
                if (window.confirm('Reset categories to the defaults? Your changes in this dialog will be replaced.')) { render(DEFAULTS); }
            })
            .on('click.wppLc', '#wppoppop-lib-cats-modal .lc-up', function (e) {
                e.preventDefault();
                var $r = $(this).closest('.lc-row'), $p = $r.prev('.lc-row');
                if ($p.length) { $r.insertBefore($p); }
            })
            .on('click.wppLc', '#wppoppop-lib-cats-modal .lc-down', function (e) {
                e.preventDefault();
                var $r = $(this).closest('.lc-row'), $n = $r.next('.lc-row');
                if ($n.length) { $r.insertAfter($n); }
            })
            .on('click.wppLc', '#wppoppop-lib-cats-modal .lc-del', function (e) {
                e.preventDefault();
                $(this).closest('.lc-row').remove();
            })
            .on('keydown.wppLc', function (e) { if (e.keyCode === 27 && $m().is(':visible')) { close(); } });
    });
})(window, jQuery);
