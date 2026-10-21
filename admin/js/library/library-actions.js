/**
 * WpPopPop Library: icon action buttons + template info editor. ES5 for Chrome, Safari, Firefox, Edge.
 */
(function (window, $) {
    'use strict';

    function vars() {
        return window.wppoppop_libact_vars || window.wppoppop_lib_vars || { ajax_url: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), nonce: '' };
    }
    function label($b, text) { $b.attr({ title: text, 'aria-label': text }); }

    function decorate() {
        $('.wppoppop-tpl-preview-btn').each(function () { label($(this), 'Preview'); });
        $('.wppoppop-tpl-import-btn').each(function () { label($(this), 'Import'); });
        $('.wppoppop-usertpl-use').each(function () {
            var $use = $(this), id = $use.attr('data-id');
            label($use, 'Import');
            if ($use.closest('.wppoppop-tpl-card').attr('data-preset') === '1') { return; }
            if (!$use.siblings('.wppoppop-usertpl-edit').length) {
                $('<button type="button" class="button button-small wppoppop-usertpl-edit"></button>').attr('data-id', id).insertAfter($use);
            }
            if (!$use.siblings('.wppoppop-usertpl-del').length) {
                $('<button type="button" class="button button-small wppoppop-usertpl-del"></button>').attr('data-id', id).insertAfter($use.siblings('.wppoppop-usertpl-edit').first());
            }
            label($use.siblings('.wppoppop-usertpl-edit'), 'Edit');
            label($use.siblings('.wppoppop-usertpl-del'), 'Delete');
        });
    }

    var frame = null, curId = '';
    function $m() { return $('#wppoppop-te-modal'); }
    function msg(t, ok) { $('#wppoppop-te-msg').css('color', ok ? '#008a20' : '#b32d2e').text(t).show(); }

    function setImage(url) {
        $('#wppoppop-te-image').val(url || '');
        $('#wppoppop-te-prev').toggleClass('has-img', !!url)
            .css('background-image', url ? 'url("' + String(url).replace(/"/g, '%22') + '")' : 'none');
    }

    function pickImage() {
        if (!window.wp || !window.wp.media) { var u = window.prompt('Image URL'); if (u) { setImage(u); } return; }
        if (!frame) {
            frame = window.wp.media({ title: 'Popup Image', button: { text: 'Use image' }, library: { type: 'image' }, multiple: false });
            frame.on('select', function () { setImage(frame.state().get('selection').first().toJSON().url || ''); });
        }
        frame.open();
    }

    function fillCategories(sel) {
        var $s = $('#wppoppop-te-category').empty(), found = false;
        $('.wppoppop-library-categories .wppoppop-cat-btn').each(function () {
            var slug = $(this).attr('data-cat');
            if (!slug || slug === 'all') { return; }
            $s.append($('<option></option>').val(slug).text($.trim($(this).text())));
            if (slug === sel) { found = true; }
        });
        $s.val(found ? sel : 'custom');
    }

    function openEdit(id) {
        var v = vars();
        curId = id;
        $('#wppoppop-te-msg').hide();
        $.ajax({
            url: v.ajax_url, type: 'POST', dataType: 'json',
            data: { action: 'wppoppop_template_get', nonce: v.nonce, id: id },
            success: function (res) {
                if (!(res && res.success && res.data)) { window.alert((res && res.data && res.data.message) || 'Could not load template.'); return; }
                var d = res.data, html = '', k;
                $('#wppoppop-te-name').val(d.title);
                $('#wppoppop-te-desc').val(d.description);
                setImage(d.image);
                fillCategories(d.category);
                $('#wppoppop-te-size').text(d.width + ' \u00d7 ' + d.height + ' px');
                $('#wppoppop-te-total').text('(' + d.total + ')');
                for (k in d.counts) {
                    if (Object.prototype.hasOwnProperty.call(d.counts, k)) { html += '<div>' + $('<div>').text(k).html() + ' x ' + parseInt(d.counts[k], 10) + '</div>'; }
                }
                $('#wppoppop-te-elements').html(html || '<em>No elements</em>');
                $('#wppoppop-te-update').prop('disabled', false);
                $m().css('display', 'flex');
                $('#wppoppop-te-name').focus();
            },
            error: function () { window.alert('Server error.'); }
        });
    }

    function closeEdit() { $m().hide(); }

    function update() {
        var v = vars(), name = $.trim($('#wppoppop-te-name').val());
        if (!name) { msg('Please enter a popup name.', false); return; }
        var $b = $('#wppoppop-te-update').prop('disabled', true);
        $.ajax({
            url: v.ajax_url, type: 'POST', dataType: 'json',
            data: {
                action: 'wppoppop_template_update', nonce: v.nonce, id: curId, name: name,
                description: $('#wppoppop-te-desc').val(), image: $('#wppoppop-te-image').val(),
                category: $('#wppoppop-te-category').val() || 'custom'
            },
            success: function (res) {
                if (res && res.success) { msg('Updated.', true); window.location.reload(); }
                else { msg((res && res.data && res.data.message) || 'Update failed.', false); $b.prop('disabled', false); }
            },
            error: function () { msg('Server error while updating.', false); $b.prop('disabled', false); }
        });
    }

    $(function () {
        decorate();
        $(document)
            .on('click.wppAct', '.wppoppop-usertpl-edit', function (e) { e.preventDefault(); openEdit($(this).attr('data-id')); })
            .on('click.wppAct', '#wppoppop-te-close, #wppoppop-te-cancel', function (e) { e.preventDefault(); closeEdit(); })
            .on('click.wppAct', '#wppoppop-te-modal', function (e) { if (e.target === this) { closeEdit(); } })
            .on('click.wppAct', '#wppoppop-te-upload, #wppoppop-te-prev', function (e) { e.preventDefault(); pickImage(); })
            .on('click.wppAct', '#wppoppop-te-remove', function (e) { e.preventDefault(); setImage(''); })
            .on('click.wppAct', '#wppoppop-te-update', function (e) { e.preventDefault(); update(); })
            .on('keydown.wppAct', function (e) { if (e.keyCode === 27 && $m().is(':visible')) { closeEdit(); } });
    });
})(window, jQuery);
