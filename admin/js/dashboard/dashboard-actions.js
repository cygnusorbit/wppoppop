(function(window, $) {
    'use strict';
    window.WpPopPopDashboard = window.WpPopPopDashboard || {};

    var Actions = {
        init: function() {
            this.bindCopyShortcode();
            this.bindDuplicate();
            this.bindDelete();
            this.bindExport();
        },

        getVars: function() {
            return window.wppoppop_vars || {
                ajax_url: ajaxurl || '',
                nonce: ''
            };
        },

        bindCopyShortcode: function() {
            $('.wppoppop-copy-sc-btn').on('click', function() {
                var sc = $(this).data('shortcode');
                var $btn = $(this);
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(sc);
                }
                var orig = $btn.text();
                $btn.text('Copied!');
                setTimeout(function() { $btn.text(orig); }, 1500);
            });
        },

        bindDuplicate: function() {
            var self = this;
            $('.wppoppop-duplicate-btn').on('click', function() {
                var uid = $(this).data('uid');
                var $btn = $(this);
                var vars = self.getVars();

                $btn.prop('disabled', true).text('Copying...');

                $.post(vars.ajax_url, {
                    action: 'wppoppop_duplicate_popup',
                    nonce: vars.nonce,
                    uid: uid
                }).done(function(res) {
                    if (res.success) {
                        location.reload();
                    } else {
                        alert('Duplication Error: ' + (res.data ? res.data.message : 'Unable to duplicate.'));
                        $btn.prop('disabled', false).text('Copy');
                    }
                }).fail(function() {
                    alert('Network error while duplicating popup.');
                    $btn.prop('disabled', false).text('Copy');
                });
            });
        },

        bindDelete: function() {
            var self = this;
            $('.wppoppop-delete-btn').on('click', function() {
                if (!confirm('Are you sure you want to permanently delete this popup campaign?')) {
                    return;
                }

                var uid = $(this).data('uid');
                var $row = $(this).closest('tr');
                var vars = self.getVars();

                $.post(vars.ajax_url, {
                    action: 'wppoppop_delete_popup',
                    nonce: vars.nonce,
                    uid: uid
                }).done(function(res) {
                    if (res.success) {
                        $row.fadeOut(250, function() { $(this).remove(); });
                    } else {
                        alert('Delete Error: ' + (res.data ? res.data.message : 'Unable to delete.'));
                    }
                }).fail(function() {
                    alert('Network error while deleting popup.');
                });
            });
        },

        bindExport: function() {
            var self = this;
            $('.wppoppop-export-btn').on('click', function() {
                var uid = $(this).data('uid');
                var vars = self.getVars();
                var $btn = $(this);

                $btn.prop('disabled', true).text('Exporting...');

                $.get(vars.ajax_url, {
                    action: 'wppoppop_export_popup',
                    nonce: vars.nonce,
                    uid: uid
                }).done(function(res) {
                    if (res.success && res.data) {
                        var filename = res.data.filename || 'wppoppop-export.json';
                        var payloadStr = typeof res.data.payload === 'string' ? res.data.payload : JSON.stringify(res.data.payload, null, 2);
                        var blob = new Blob([payloadStr], { type: 'application/json;charset=utf-8;' });
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.setAttribute('download', filename);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        alert('Export Error: ' + (res.data ? res.data.message : 'Unable to export.'));
                    }
                }).fail(function() {
                    alert('Network error while exporting popup.');
                }).always(function() {
                    $btn.prop('disabled', false).text('Export');
                });
            });
        }
    };

    window.WpPopPopDashboard.Actions = Actions;
})(window, jQuery);
