/**
 * WpPopPop Library: user templates (Use / Delete). ES5, cross-browser.
 */
(function (window, $) {
    'use strict';

    function vars() {
        return window.wppoppop_lib_vars || window.wppoppop_vars || { ajax_url: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), nonce: '' };
    }

    function decorate() {
        $('.wppoppop-usertpl-use').each(function () {
            var $use = $(this);
            if ($use.closest('.wppoppop-tpl-card').attr('data-preset') === '1') { return; }
            if ($use.siblings('.wppoppop-usertpl-del').length) { return; }
            $('<button type="button" class="button button-small wppoppop-usertpl-del" style="color:#b32d2e;">Delete</button>')
                .attr('data-id', $use.attr('data-id'))
                .insertAfter($use);
        });
    }

    $(function () {
        decorate();

        $(document).on('click', '.wppoppop-usertpl-use', function (e) {
            e.preventDefault();
            var $b = $(this), orig = $b.text(), v = vars();
            if ($b.prop('disabled')) { return; }
            $b.prop('disabled', true).text('Creating...');
            $.ajax({
                url: v.ajax_url, type: 'POST', dataType: 'json',
                data: { action: 'wppoppop_template_use', nonce: v.nonce, id: $b.attr('data-id') },
                success: function (res) {
                    if (res && res.success && res.data && res.data.redirect) {
                        window.location.href = res.data.redirect;
                    } else {
                        window.alert((res && res.data && res.data.message) || 'Could not use template.');
                        $b.prop('disabled', false).text(orig);
                    }
                },
                error: function () { window.alert('Server error.'); $b.prop('disabled', false).text(orig); }
            });
        });

        $(document).on('click', '.wppoppop-usertpl-del', function (e) {
            e.preventDefault();
            if (!window.confirm('Delete this template? Popups already created from it are not affected.')) { return; }
            var $b = $(this), v = vars();
            $.ajax({
                url: v.ajax_url, type: 'POST', dataType: 'json',
                data: { action: 'wppoppop_template_delete', nonce: v.nonce, id: $b.attr('data-id') },
                success: function (res) {
                    if (res && res.success) { $b.closest('.wppoppop-tpl-card').remove(); }
                    else { window.alert((res && res.data && res.data.message) || 'Delete failed.'); }
                }
            });
        });
    });
})(window, jQuery);
