/**
 * WpPopPop Dashboard: Row Actions Sub-Module
 * Handles single-item duplication, deletion, export, and shortcode copying.
 */
(function($) {
    'use strict';

    window.WpPopPopDashboardActions = {
        init: function() {
            this.bindDuplicate();
            this.bindDelete();
            this.bindExport();
            this.bindCopyShortcode();
        },

        bindDuplicate: function() {
            $(document).on('click', '.wppoppop-duplicate-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var uid = $btn.attr('data-uid') || $btn.closest('tr').attr('data-uid');
                if (!uid) return;

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                var nonce = (window.wppoppop_vars && (window.wppoppop_vars.nonce || window.wppoppop_vars.admin_nonce || window.wppoppop_vars.builder_nonce)) || '';

                $btn.prop('disabled', true);

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_duplicate_popup',
                        nonce: nonce,
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success) {
                            window.location.reload();
                        } else {
                            $btn.prop('disabled', false);
                            alert((res.data && res.data.message) ? res.data.message : 'Duplication failed.');
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false);
                        alert('Error communicating with the server.');
                    }
                });
            });
        },

        bindDelete: function() {
            $(document).on('click', '.wppoppop-delete-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $btn = $(this);
                var uid = $btn.attr('data-uid') || $btn.data('uid');

                if (!uid) {
                    var $row = $btn.closest('tr.wppoppop-table-row');
                    if ($row.length) {
                        uid = $row.attr('data-uid') || $row.data('uid');
                    }
                }

                if (!uid) {
                    alert('Could not determine popup identifier to delete.');
                    return;
                }

                var title = $btn.closest('tr').find('a.row-title').text().trim() || 'this popup campaign';
                if (!window.confirm('Are you sure you want to permanently delete "' + title + '"?\n\nThis action cannot be undone.')) {
                    return;
                }

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (window.ajaxurl || '/wp-admin/admin-ajax.php');
                var nonce = (window.wppoppop_vars && (window.wppoppop_vars.nonce || window.wppoppop_vars.admin_nonce || window.wppoppop_vars.builder_nonce)) || '';

                var $targetRow = $('tr.wppoppop-table-row[data-uid="' + uid + '"]');
                if (!$targetRow.length) {
                    $targetRow = $('#wppoppop-row-' + uid);
                }
                $targetRow.css('opacity', '0.35');

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_delete_popup',
                        nonce: nonce,
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success) {
                            $targetRow.fadeOut(250, function() {
                                $(this).remove();
                                if (window.WpPopPopDashboardTable && typeof window.WpPopPopDashboardTable.reindex === 'function') {
                                    window.WpPopPopDashboardTable.reindex();
                                }
                            });
                        } else {
                            $targetRow.css('opacity', '1');
                            var msg = (res && res.data && res.data.message) ? res.data.message : 'Failed to delete popup.';
                            alert(msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        $targetRow.css('opacity', '1');
                        alert('Communication error with WordPress server: ' + error);
                    }
                });
            });
        },

        bindExport: function() {
            $(document).on('click', '.wppoppop-export-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var uid = $btn.attr('data-uid') || $btn.closest('tr').attr('data-uid');
                if (!uid) return;

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                var nonce = (window.wppoppop_vars && (window.wppoppop_vars.nonce || window.wppoppop_vars.admin_nonce || window.wppoppop_vars.builder_nonce)) || '';

                $.ajax({
                    url: ajaxUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_export_popup',
                        nonce: nonce,
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success && res.data) {
                            var blob = new Blob([res.data.payload], { type: 'application/json;charset=utf-8;' });
                            var link = document.createElement('a');
                            link.href = URL.createObjectURL(blob);
                            link.download = res.data.filename || ('popup-' + uid + '.json');
                            link.click();
                        } else {
                            alert('Export failed.');
                        }
                    }
                });
            });
        },

        bindCopyShortcode: function() {
            $(document).on('click', '.wppoppop-shortcode-chip', function() {
                var copyText = $(this).attr('data-copy');
                var $elem = $(this);
                var origText = $elem.text();

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(copyText).then(function() {
                        $elem.text('Copied!');
                        setTimeout(function() {
                            $elem.text(origText);
                        }, 1200);
                    });
                }
            });
        }
    };
})(jQuery);
