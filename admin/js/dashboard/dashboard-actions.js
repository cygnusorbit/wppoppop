/**
 * WpPopPop Dashboard: Actions Engine (Duplicate, Delete, Export)
 * Cross-Browser Compatibility: Safari, Firefox, Chrome, Edge
 */
(function(window, $) {
    'use strict';
    window.WpPopPopDashboardActions = window.WpPopPopDashboardActions || {};

    var Actions = {
        init: function() {
            this.bindDuplicate();
            this.bindDelete();
            this.bindExport();
        },

        getVars: function() {
            return window.wppoppop_vars || {
                ajax_url: typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php',
                nonce: ''
            };
        },

        bindDuplicate: function() {
            var self = this;
            $(document).on('click', '.btn-duplicate-popup, .wppoppop-action-duplicate', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var uid = $btn.data('uid') || $btn.closest('tr').find('input[name="popup_id[]"]').val();
                if (!uid) return;

                if (!window.confirm('Duplicate this popup campaign?')) return;

                var vars = self.getVars();
                $btn.prop('disabled', true);

                $.ajax({
                    url: vars.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_duplicate_popup',
                        nonce: vars.nonce,
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success) {
                            window.location.reload();
                        } else {
                            alert((res && res.data && res.data.message) || 'Duplication failed.');
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Server error while duplicating popup.');
                        $btn.prop('disabled', false);
                    }
                });
            });
        },

        bindDelete: function() {
            var self = this;
            $(document).on('click', '.btn-delete-popup, .wppoppop-action-delete', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var uid = $btn.data('uid') || $btn.closest('tr').find('input[name="popup_id[]"]').val();
                if (!uid) return;

                if (!window.confirm('Are you sure you want to delete this popup? This cannot be undone.')) return;

                var vars = self.getVars();
                $btn.prop('disabled', true);

                $.ajax({
                    url: vars.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_delete_popup',
                        nonce: vars.nonce,
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success) {
                            $btn.closest('tr').fadeOut(200, function() {
                                $(this).remove();
                            });
                        } else {
                            alert((res && res.data && res.data.message) || 'Deletion failed.');
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Server error while deleting popup.');
                        $btn.prop('disabled', false);
                    }
                });
            });
        },

        bindExport: function() {
            var self = this;
            $(document).on('click', '.btn-export-popup, .wppoppop-action-export', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var uid = $btn.data('uid') || $btn.closest('tr').find('input[name="popup_id[]"]').val();
                if (!uid) return;

                var vars = self.getVars();

                $.ajax({
                    url: vars.ajax_url,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_export_popup',
                        nonce: vars.nonce,
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success && res.data) {
                            var filename = res.data.filename || ('popup-' + uid + '.json');
                            var payloadStr = typeof res.data.payload === 'string' ? res.data.payload : JSON.stringify(res.data.payload, null, 2);

                            // Safari-Safe Cross-Browser Blob File Download
                            var blob = new Blob([payloadStr], { type: 'application/json;charset=utf-8' });
                            var blobUrl = window.URL.createObjectURL(blob);
                            var downloadLink = document.createElement('a');

                            downloadLink.href = blobUrl;
                            downloadLink.download = filename;
                            downloadLink.style.display = 'none';
                            document.body.appendChild(downloadLink);

                            downloadLink.click();

                            // Delayed revocation to ensure Safari completes stream write
                            setTimeout(function() {
                                document.body.removeChild(downloadLink);
                                window.URL.revokeObjectURL(blobUrl);
                            }, 1500);
                        } else {
                            alert((res && res.data && res.data.message) || 'Export failed.');
                        }
                    },
                    error: function() {
                        alert('Server error while exporting popup.');
                    }
                });
            });
        }
    };

    window.WpPopPopDashboardActions = Actions;
})(window, jQuery);
