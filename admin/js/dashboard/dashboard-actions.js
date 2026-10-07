/**
 * WpPopPop Dashboard: Row Actions Sub-Module
 * Handles single-item duplication, deletion, export, and shortcode copying.
 */
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
            if (window.wppoppop_vars && window.wppoppop_vars.ajax_url) {
                return window.wppoppop_vars;
            }
            if (window.wppoppopAdmin && window.wppoppopAdmin.ajax_url) {
                return window.wppoppopAdmin;
            }
            return {
                ajax_url: typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php',
                nonce: $('#wppoppop_admin_nonce_field').val() || $('#_wpnonce').val() || ''
            };
        },

        bindCopyShortcode: function() {
            $(document).off('click', '.wppoppop-copy-sc-btn, .wppoppop-shortcode-chip')
                       .on('click', '.wppoppop-copy-sc-btn, .wppoppop-shortcode-chip', function(e) {
                e.preventDefault();
                var sc = $(this).attr('data-copy') || $(this).attr('data-shortcode') || $(this).text().trim();
                var $btn = $(this);
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(sc).then(function() {
                        var orig = $btn.text();
                        $btn.text('Copied!');
                        setTimeout(function() { $btn.text(orig); }, 1500);
                    });
                }
            });
        },

        bindDuplicate: function() {
            var self = this;
            $(document).off('click', '.wppoppop-duplicate-btn')
                       .on('click', '.wppoppop-duplicate-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var uid = $btn.attr('data-uid') || $btn.data('uid') || $btn.closest('tr').attr('data-uid');
                var vars = self.getVars();

                if (!uid) return;
                $btn.prop('disabled', true).text('Copying...');

                $.post(vars.ajax_url, {
                    action: 'wppoppop_duplicate_popup',
                    nonce: vars.nonce || vars.admin_nonce || vars.builder_nonce,
                    uid: uid
                }).done(function(res) {
                    if (res && res.success) {
                        window.location.reload();
                    } else {
                        alert('Duplication Error: ' + (res && res.data ? res.data.message : 'Unable to duplicate campaign.'));
                        $btn.prop('disabled', false).text('Duplicate');
                    }
                }).fail(function() {
                    alert('Network error while duplicating campaign.');
                    $btn.prop('disabled', false).text('Duplicate');
                });
            });
        },

        bindDelete: function() {
            var self = this;

            // Delegated click handler on document to guarantee clicks are always captured
            $(document).off('click', '.wppoppop-delete-btn')
                       .on('click', '.wppoppop-delete-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $btn = $(this);
                var uid = $btn.attr('data-uid') || $btn.data('uid');
                var $parentRow = $btn.closest('tr');

                if (!uid && $parentRow.length) {
                    uid = $parentRow.attr('data-uid') || $parentRow.data('uid');
                }

                if (!uid) {
                    alert('Error: Unable to identify campaign UID for deletion.');
                    return;
                }

                var $row = $('#wppoppop-row-' + uid);
                if (!$row.length) {
                    $row = $parentRow.length ? $parentRow : $('#wppoppop-table-tbody tr[data-uid="' + uid + '"]');
                }

                var title = $row.attr('data-raw-title') || $row.find('a.row-title').text().trim() || 'this campaign';

                if (!window.confirm('Are you sure you want to permanently delete "' + title + '"? This action cannot be undone.')) {
                    return;
                }

                var vars = self.getVars();
                var nonce = vars.nonce || vars.admin_nonce || vars.builder_nonce;

                $.ajax({
                    url: vars.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_delete_popup',
                        nonce: nonce,
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success) {
                            $row.fadeOut(250, function() {
                                $(this).remove();

                                var remaining = $('#wppoppop-table-tbody .wppoppop-table-row').length;
                                $('.wppoppop-total-items-text, #wppoppop-total-items').text(remaining);

                                if (window.WpPopPopDashboardTable) {
                                    if (typeof window.WpPopPopDashboardTable.reindex === 'function') {
                                        window.WpPopPopDashboardTable.reindex();
                                    }
                                    if (typeof window.WpPopPopDashboardTable.recalculatePillCounts === 'function') {
                                        window.WpPopPopDashboardTable.recalculatePillCounts();
                                    }
                                    if (typeof window.WpPopPopDashboardTable.syncSelectAllCheckbox === 'function') {
                                        window.WpPopPopDashboardTable.syncSelectAllCheckbox();
                                    }
                                    if (typeof window.WpPopPopDashboardTable.updateBulkActionsState === 'function') {
                                        window.WpPopPopDashboardTable.updateBulkActionsState();
                                    }
                                }

                                if (remaining === 0) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Server refused deletion request.';
                            alert('Delete Error: ' + errMsg);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Network or authorization error while deleting campaign: ' + (error || status));
                    }
                });
            });
        },

        bindExport: function() {
            var self = this;
            $(document).off('click', '.wppoppop-export-btn')
                       .on('click', '.wppoppop-export-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid') || $(this).data('uid') || $(this).closest('tr').attr('data-uid');
                var vars = self.getVars();
                var $btn = $(this);

                if (!uid) return;
                $btn.prop('disabled', true).text('Exporting...');

                $.get(vars.ajax_url, {
                    action: 'wppoppop_export_popup',
                    nonce: vars.nonce || vars.admin_nonce || vars.builder_nonce,
                    uid: uid
                }).done(function(res) {
                    if (res && res.success && res.data) {
                        var filename = res.data.filename || ('popup-' + uid + '.json');
                        var payloadStr = typeof res.data.payload === 'string' ? res.data.payload : JSON.stringify(res.data.payload, null, 2);
                        var blob = new Blob([payloadStr], { type: 'application/json;charset=utf-8;' });
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.setAttribute('download', filename);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        alert('Export Error: ' + (res && res.data ? res.data.message : 'Unable to export campaign.'));
                    }
                }).fail(function() {
                    alert('Network error while exporting popup.');
                }).always(function() {
                    $btn.prop('disabled', false).text('Export JSON');
                });
            });
        }
    };

    // Expose both namespaces so coordinator resolves unconditionally
    window.WpPopPopDashboardActions = Actions;
    window.WpPopPopDashboard.Actions = Actions;

    $(function() {
        Actions.init();
    });
})(window, jQuery);
