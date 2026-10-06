(function(window, $) {
    'use strict';
    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};

    var Actions = {
        init: function() {
            this.bindExportCsv();
            this.bindAnonymize();
            this.bindDelete();
        },

        getVars: function() {
            return window.wppoppop_vars || {
                ajax_url: ajaxurl || '',
                nonce: ''
            };
        },

        bindExportCsv: function() {
            var self = this;
            $('#wppoppop-btn-export-csv').on('click', function() {
                var vars = self.getVars();
                window.location.href = vars.ajax_url + '?action=wppoppop_export_submissions_csv&nonce=' + vars.nonce;
            });
        },

        bindAnonymize: function() {
            var self = this;
            $('.wppoppop-anon-sub-btn').on('click', function() {
                if (!confirm('Anonymize this lead record for GDPR compliance?')) {
                    return;
                }

                var id = $(this).data('id');
                var $row = $(this).closest('tr');
                var vars = self.getVars();

                $.post(vars.ajax_url, {
                    action: 'wppoppop_anonymize_submission',
                    nonce: vars.nonce,
                    id: id
                }).done(function(res) {
                    if (res.success) {
                        $row.find('td:nth-child(2) strong').text('anonymized_' + id + '@privacy.local');
                        alert(res.data.message || 'Lead PII scrubbed successfully.');
                    } else {
                        alert('Anonymization Error: ' + (res.data ? res.data.message : 'Action failed.'));
                    }
                }).fail(function() {
                    alert('Network error while anonymizing lead.');
                });
            });
        },

        bindDelete: function() {
            var self = this;
            $('.wppoppop-del-sub-btn').on('click', function() {
                if (!confirm('Permanently delete this submission record?')) {
                    return;
                }

                var id = $(this).data('id');
                var $row = $(this).closest('tr');
                var vars = self.getVars();

                $.post(vars.ajax_url, {
                    action: 'wppoppop_delete_submission',
                    nonce: vars.nonce,
                    id: id
                }).done(function(res) {
                    if (res.success) {
                        $row.fadeOut(200, function() { $(this).remove(); });
                    } else {
                        alert('Deletion Error: ' + (res.data ? res.data.message : 'Action failed.'));
                    }
                }).fail(function() {
                    alert('Network error while deleting submission.');
                });
            });
        }
    };

    window.WpPopPopSubmissions.Actions = Actions;
})(window, jQuery);
