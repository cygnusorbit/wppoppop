
(function($) {
    'use strict';

    $(document).ready(function() {
        // Filter tabs
        $('.lib-filter-btn').on('click', function() {
            $('.lib-filter-btn').removeClass('active');
            $(this).addClass('active');

            const cat = $(this).data('cat');
            if (cat === 'all') {
                $('.lib-card').fadeIn(150);
            } else {
                $('.lib-card').each(function() {
                    $(this).toggle($(this).data('cat') === cat);
                });
            }
        });

        // 1-Click Import Handler
        $('.btn-import-tpl').on('click', function(e) {
            e.preventDefault();
            const btn = $(this);
            const templateKey = btn.data('tpl');

            btn.prop('disabled', true).text('Importing...');

            $.post(wppoppop_lib_vars.ajax_url, {
                action: 'wppoppop_import_library_template',
                nonce: wppoppop_lib_vars.nonce,
                template_key: templateKey
            }, function(res) {
                if (res.success && res.data.redirect_url) {
                    window.location.href = res.data.redirect_url;
                } else {
                    btn.prop('disabled', false).text('Import & Edit');
                    alert('Import failed: ' + (res.data ? res.data.message : 'Unknown error'));
                }
            });
        });
    });
})(jQuery);
