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
                var uid = $(this).attr('data-uid');
                if (!uid) return;

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
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
            $(document).on('click', '.wppoppop-delete-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                if (!uid || !window.confirm('Are you sure you want to permanently delete this popup campaign?')) {
                    return;
                }

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';

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
                            var $row =$('#wppoppop-table-tbody .wppoppop-table-row[data-uid="' + uid + '"]');
                            $row.fadeOut(300, function() {$(this).remove();
                                if (window.WpPopPopDashboardTable) {
                                    window.WpPopPopDashboardTable.reindex();
                                }
                            });
                        } else {
                            alert((res.data && res.data.message) ? res.data.message : 'Failed to delete popup.');
                        }
                    },
                    error: function() {
                        alert('Error communicating with the server.');
                    }
                });
            });
        },

        bindExport: function() {
            $(document).on('click', '.wppoppop-export-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                if (!uid) return;

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
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
            $(document).on('click', '.wppoppop-shortcode-chip', function() {
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
})(jQuery);
