/**
 * WpPopPop Dashboard: Row Actions Sub-Module
 * Handles single-item duplication, deletion, export, embed code modal, and shortcode copying.
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
            $(document).off('click', '.wppoppop-duplicate-btn').on('click', '.wppoppop-duplicate-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                if (!uid) return;

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (window.ajaxurl || '/wp-admin/admin-ajax.php');
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';

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
                            alert((res.data && res.data.message) ? res.data.message : 'Duplication failed.');
                        }
                    },
                    error: function() {
                        alert('Error communicating with the server.');
                    }
                });
            });
        },

        bindDelete: function() {
            $(document).off('click', '.wppoppop-delete-btn').on('click', '.wppoppop-delete-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $btn =$(this);
                var uid = $btn.attr('data-uid');
                if (!uid) {
                    var $row =$btn.closest('tr.wppoppop-table-row');
                    uid = $row.attr('data-uid');
                }

                if (!uid) {
                    alert('Error: Could not identify popup UID.');
                    return;
                }

                if (!window.confirm('Are you sure you want to permanently delete this popup campaign?')) {
                    return;
                }

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (window.ajaxurl || '/wp-admin/admin-ajax.php');
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';

                var $targetRow =$btn.closest('tr.wppoppop-table-row');
                if (!$targetRow.length) {
                    $targetRow =$('#wppoppop-table-tbody .wppoppop-table-row[data-uid="' + uid + '"]');
                }

                $btn.css('opacity', '0.5');

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
                            $targetRow.fadeOut(300, function() {$(this).remove();
                                if (window.WpPopPopDashboardTable && typeof window.WpPopPopDashboardTable.reindex === 'function') {
                                    window.WpPopPopDashboardTable.reindex();
                                }
                            });
                        } else {
                            $btn.css('opacity', '1');
                            var msg = (res && res.data && res.data.message) ? res.data.message : 'Failed to delete popup.';
                            alert(msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        $btn.css('opacity', '1');
                        var errMsg = 'Communication error while deleting popup: ' + (error || status);
                        if (xhr.responseText) {
                            try {
                                var parsed = JSON.parse(xhr.responseText);
                                if (parsed && parsed.data && parsed.data.message) {
                                    errMsg = parsed.data.message;
                                }
                            } catch(err) {}
                        }
                        alert(errMsg);
                    }
                });
            });
        },

        bindExport: function() {
            $(document).off('click', '.wppoppop-export-btn').on('click', '.wppoppop-export-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                if (!uid) return;

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (window.ajaxurl || '/wp-admin/admin-ajax.php');
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';

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
            $(document).off('click', '.wppoppop-shortcode-chip').on('click', '.wppoppop-shortcode-chip', function() {
                var copyText = $(this).attr('data-copy');
                var $elem =$(this);
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

    $(document).ready(function() {
        if (window.WpPopPopDashboardActions) {
            window.WpPopPopDashboardActions.init();
        }
    });
})(jQuery);
