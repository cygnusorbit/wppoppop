/**
 * WpPopPop Tools: Maintenance sub-module (purge logs, optimize, orphan report).
 */
(function($) {
    'use strict';

    function vars() {
        return {
            url: (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (typeof ajaxurl !== 'undefined' ? ajaxurl : ''),
            nonce: (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : ''
        };
    }

    function notice(type, msg) {
        $('#wppoppop-maint-notice').removeClass('is-success is-error').addClass(type === 'success' ? 'is-success' : 'is-error').text(msg).prop('hidden', false);
    }

    window.WpPopPopToolsMaintenance = {
        init: function() {
            $(document).on('click', '[data-wpp-maint]', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var action = $btn.attr('data-wpp-maint');
                var v = vars();
                var data = { action: action, nonce: v.nonce };
                if (action === 'wppoppop_purge_logs') {
                    data.days = parseInt($('#wppoppop-purge-days').val(), 10) || 30;
                    if (!window.confirm('Delete all log entries older than ' + data.days + ' days? This cannot be undone.')) { return; }
                }
                $btn.prop('disabled', true);
                $.ajax({ url: v.url, type: 'POST', dataType: 'json', data: data })
                    .done(function(res) {
                        if (res && res.success) {
                            notice('success', res.data.message || 'Done.');
                            if (res.data.tables) {
                                $.each(res.data.tables, function(key, t) {
                                    var $row = $('#wppoppop-table-row-' + key);
                                    $row.find('.wppoppop-tbl-rows-count').text(t.rows);
                                    $row.find('.wppoppop-tbl-size-val').text(t.size);
                                });
                            }
                        } else {
                            notice('error', (res && res.data && res.data.message) ? res.data.message : 'Action failed.');
                        }
                    })
                    .fail(function() { notice('error', 'Server communication error.'); })
                    .always(function() { $btn.prop('disabled', false); });
            });
        }
    };

    $(function() { window.WpPopPopToolsMaintenance.init(); });
})(jQuery);
