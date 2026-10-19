/**
 * WpPopPop Dashboard: Bulk "Export Selected (JSON)"
 * Cross-browser: Chrome, Safari, Firefox, Edge (ES5 only)
 */
(function (window, $) {
    'use strict';

    var Bulk = {
        init: function () {
            var self = this;
            $(document).off('submit.wppoppopBulk', '#wppoppop-campaigns-table-form')
                .on('submit.wppoppopBulk', '#wppoppop-campaigns-table-form', function (e) {
                    var $f = $(this);
                    var action = $f.find('#bulk-action-selector-top').val();
                    if (!action || action === '-1') { action = $f.find('#bulk-action-selector-bottom').val(); }
                    if (action !== 'export') { return; }

                    var uids = [];
                    $f.find('input[name="bulk_ids[]"]:checked').each(function () { uids.push($(this).val()); });
                    if (!uids.length) { return; } // table module shows the alert

                    e.preventDefault();
                    self.run(uids);
                });
        },

        run: function (uids) {
            var v = window.wppoppop_vars || {};
            $.ajax({
                url: v.ajax_url || window.ajaxurl, type: 'POST', dataType: 'json',
                data: { action: 'wppoppop_export_selected', nonce: v.nonce || '', uids: uids }
            }).done(function (res) {
                if (res && res.success && res.data) {
                    var str = typeof res.data.payload === 'string' ? res.data.payload : JSON.stringify(res.data.payload, null, 2);
                    var blob = new Blob([str], { type: 'application/json;charset=utf-8' });
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url; a.download = res.data.filename || 'wppoppop-selected.json'; a.style.display = 'none';
                    document.body.appendChild(a); a.click();
                    setTimeout(function () { document.body.removeChild(a); window.URL.revokeObjectURL(url); }, 1500);
                } else {
                    window.alert((res && res.data && res.data.message) || 'Export failed.');
                }
            }).fail(function () { window.alert('Server error while exporting.'); });
        }
    };

    window.WpPopPopDashboardBulk = Bulk;
    $(function () { Bulk.init(); });
})(window, jQuery);
