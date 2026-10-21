/**
 * WpPopPop Library: Download template JSON. ES5; Blob + msSaveBlob fallback (Chrome, Safari, Firefox, Edge).
 */
(function (window, $) {
    'use strict';

    function vars() {
        return window.wppoppop_libact_vars || window.wppoppop_lib_vars || { ajax_url: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), nonce: '' };
    }

    function saveText(name, text) {
        var blob = new Blob([text], { type: 'application/json' });
        if (window.navigator && window.navigator.msSaveBlob) { window.navigator.msSaveBlob(blob, name); return; }
        var url = (window.URL || window.webkitURL).createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url; a.download = name; a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { document.body.removeChild(a); (window.URL || window.webkitURL).revokeObjectURL(url); }, 1000);
    }

    function decorate() {
        $('.wppoppop-usertpl-use').each(function () {
            var $use = $(this);
            if ($use.siblings('.wppoppop-usertpl-dl').length) { return; }
            $('<button type="button" class="button button-small wppoppop-usertpl-dl"></button>')
                .attr({ 'data-id': $use.attr('data-id'), title: 'Download', 'aria-label': 'Download' })
                .insertAfter($use);
        });
    }

    $(function () {
        decorate();
        $(document).on('click.wppDl', '.wppoppop-usertpl-dl', function (e) {
            e.preventDefault();
            var $b = $(this), v = vars();
            if ($b.prop('disabled')) { return; }
            $b.prop('disabled', true);
            $.ajax({
                url: v.ajax_url, type: 'POST', dataType: 'json',
                data: { action: 'wppoppop_template_download', nonce: v.nonce, id: $b.attr('data-id') },
                success: function (res) {
                    if (res && res.success && res.data && res.data.payload) { saveText(res.data.filename || 'template.json', res.data.payload); }
                    else { window.alert((res && res.data && res.data.message) || 'Download failed.'); }
                },
                error: function () { window.alert('Server error while preparing the download.'); },
                complete: function () { $b.prop('disabled', false); }
            });
        });
    });
})(window, jQuery);
