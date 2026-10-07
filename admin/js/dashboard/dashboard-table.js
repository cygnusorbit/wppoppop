/**
 * WpPopPop Dashboard: Native Table Controller
 * Handles Quick Edit, Row Actions (Delete, Duplicate, Export), Column Toggles, Bulk Operations, and Status Filtering.
 */
(function($) {
    'use strict';

    window.WpPopPopDashboardTable = {
        storageKey: 'wppoppop_dash_col_visibility',

        init: function() {
            this.bindSelectAll();
            this.bindBulkActions();
            this.bindColumnToggles();
            this.bindQuickEdit();
            this.bindRowActions();
            this.bindShortcodeCopy();
            this.restoreColumnToggles();
        },

        getAjaxUrl: function() {
            if (window.wppoppop_vars && window.wppoppop_vars.ajax_url) {
                return window.wppoppop_vars.ajax_url;
            }
            if (window.wppoppopAdmin && window.wppoppopAdmin.ajax_url) {
                return window.wppoppopAdmin.ajax_url;
            }
            if (typeof ajaxurl !== 'undefined') {
                return ajaxurl;
            }
            return '/wp-admin/admin-ajax.php';
        },

        getNonce: function() {
            if (window.wppoppop_vars && window.wppoppop_vars.nonce) {
                return window.wppoppop_vars.nonce;
            }
            if (window.wppoppopAdmin && window.wppoppopAdmin.nonce) {
                return window.wppoppopAdmin.nonce;
            }
            if (window.wppoppopAdmin && window.wppoppopAdmin.admin_nonce) {
                return window.wppoppopAdmin.admin_nonce;
            }
            var formNonce = $('#wppoppop_admin_nonce_field').val() || $('#_wpnonce').val();
            return formNonce || '';
        },

        recalculatePillCounts: function() {
            var $rows = $('#wppoppop-table-tbody .wppoppop-table-row');
            var all = $rows.length;
            var pub = 0;
            var draft = 0;
            var highCr = 0;

            $rows.each(function() {
                var status = $(this).attr('data-status');
                var cr = parseFloat($(this).attr('data-cr')) || 0;
                if (status === 'publish') {
                    pub++;
                } else {
                    draft++;
                }
                if (cr >= 10.0) {
                    highCr++;
                }
            });

            $('.wppoppop-count-all').text(all);
            $('.wppoppop-count-publish').text(pub);
            $('.wppoppop-count-draft').text(draft);
            $('.wppoppop-count-high-cr').text(highCr);
        },

        bindQuickEdit: function() {
            var self = this;

            // Trigger Quick Edit opening
            $(document).on('click', '.wppoppop-quick-edit-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                var $targetRow = $('#wppoppop-row-' + uid);
                if (!$targetRow.length) return;

                // Close any open quick edit row
                $('.wppoppop-active-quick-edit').each(function() {
                    var prevUid = $(this).attr('data-editing-uid');
                    $('#wppoppop-row-' + prevUid).show();
                    $(this).remove();
                });

                var currentTitle = $targetRow.attr('data-raw-title') || $targetRow.find('a.row-title').text().trim();
                var currentStatus = $targetRow.attr('data-status') || 'publish';

                var $clone = $('#wppoppop-quick-edit-template-root tr').clone();
                $clone.addClass('wppoppop-active-quick-edit').attr('data-editing-uid', uid);
                $clone.find('.wppoppop-qe-input-title').val(currentTitle);
                $clone.find('.wppoppop-qe-input-uid').val(uid);
                $clone.find('.wppoppop-qe-input-status').val(currentStatus);

                $targetRow.hide();
                $targetRow.after($clone);
                $clone.find('.wppoppop-qe-input-title').focus().select();
            });

            // Cancel Quick Edit
            $(document).on('click', '.wppoppop-qe-btn-cancel', function(e) {
                e.preventDefault();
                var $inlineRow = $(this).closest('.wppoppop-active-quick-edit');
                var uid = $inlineRow.attr('data-editing-uid');
                $inlineRow.remove();
                $('#wppoppop-row-' + uid).fadeIn(200);
            });

            // Save / Update Quick Edit
            $(document).on('click', '.wppoppop-qe-btn-save', function(e) {
                e.preventDefault();
                var $inlineRow = $(this).closest('.wppoppop-active-quick-edit');
                var uid = $inlineRow.attr('data-editing-uid');
                var $targetRow = $('#wppoppop-row-' + uid);
                var newTitle = $inlineRow.find('.wppoppop-qe-input-title').val().trim();
                var newStatus = $inlineRow.find('.wppoppop-qe-input-status').val();

                var $spinner = $inlineRow.find('.wppoppop-qe-spinner');
                var $errorNotice = $inlineRow.find('.wppoppop-qe-error-notice');
                var $errorMsg = $inlineRow.find('.wppoppop-qe-error-message');
                var $btn = $(this);

                if (!newTitle) {
                    $errorMsg.text('Campaign title cannot be empty.');
                    $errorNotice.show();
                    return;
                }

                $errorNotice.hide();
                $spinner.addClass('is-active').show();
                $btn.prop('disabled', true);

                $.ajax({
                    url: self.getAjaxUrl(),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_quick_edit',
                        nonce: self.getNonce(),
                        uid: uid,
                        title: newTitle,
                        status: newStatus
                    },
                    success: function(res) {
                        $spinner.removeClass('is-active').hide();
                        $btn.prop('disabled', false);

                        if (res && res.success && res.data) {
                            var data = res.data;
                            $targetRow.attr('data-title', data.title.toLowerCase());
                            $targetRow.attr('data-raw-title', data.title);
                            $targetRow.attr('data-status', data.status);
                            $targetRow.find('a.row-title').text(data.title);

                            var $postState = $targetRow.find('.wppoppop-post-state');
                            var $badge = $targetRow.find('.wppoppop-status-badge');

                            if (data.status === 'publish') {
                                $targetRow.removeClass('status-draft').addClass('status-publish');
                                $postState.hide();
                                $badge.removeClass('badge-inactive').addClass('badge-active').text('Publish');
                            } else {
                                $targetRow.removeClass('status-publish').addClass('status-draft');
                                $postState.show();
                                $badge.removeClass('badge-active').addClass('badge-inactive').text('Draft');
                            }

                            $inlineRow.remove();
                            $targetRow.show().addClass('wppoppop-row-updated');
                            setTimeout(function() {
                                $targetRow.removeClass('wppoppop-row-updated');
                            }, 1500);

                            self.recalculatePillCounts();
                        } else {
                            $errorMsg.text((res && res.data && res.data.message) ? res.data.message : 'Quick edit failed.');
                            $errorNotice.show();
                        }
                    },
                    error: function() {
                        $spinner.removeClass('is-active').hide();
                        $btn.prop('disabled', false);
                        $errorMsg.text('Communication error processing Quick Edit.');
                        $errorNotice.show();
                    }
                });
            });
        },

        bindRowActions: function() {
            var self = this;

            // 1. Single-Row Delete Action Link
            $(document).on('click', '.wppoppop-delete-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                var $targetRow = $('#wppoppop-row-' + uid);
                var title = $targetRow.attr('data-raw-title') || $targetRow.find('a.row-title').text().trim() || 'this campaign';

                if (!window.confirm('Are you sure you want to permanently delete "' + title + '"? This cannot be undone.')) {
                    return;
                }

                $.ajax({
                    url: self.getAjaxUrl(),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_delete_popup',
                        nonce: self.getNonce(),
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success) {
                            $targetRow.fadeOut(250, function() {
                                $(this).remove();
                                self.recalculatePillCounts();
                                self.syncSelectAllCheckbox();
                                self.updateBulkActionsState();

                                var remaining = $('#wppoppop-table-tbody .wppoppop-table-row').length;
                                $('.wppoppop-total-items-text').text(remaining);
                                if (remaining === 0) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            alert((res && res.data && res.data.message) ? res.data.message : 'Failed to delete campaign.');
                        }
                    },
                    error: function() {
                        alert('Communication error while attempting to delete campaign.');
                    }
                });
            });

            // 2. Single-Row Duplicate Action Link
            $(document).on('click', '.wppoppop-duplicate-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                if (!uid) return;

                $.ajax({
                    url: self.getAjaxUrl(),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_duplicate_popup',
                        nonce: self.getNonce(),
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success) {
                            window.location.reload();
                        } else {
                            alert((res && res.data && res.data.message) ? res.data.message : 'Duplication failed.');
                        }
                    },
                    error: function() {
                        alert('Communication error while duplicating campaign.');
                    }
                });
            });

            // 3. Single-Row Export Action Link
            $(document).on('click', '.wppoppop-export-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                if (!uid) return;

                $.ajax({
                    url: self.getAjaxUrl(),
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_export_popup',
                        nonce: self.getNonce(),
                        uid: uid
                    },
                    success: function(res) {
                        if (res && res.success && res.data) {
                            var payload = (typeof res.data.payload === 'string') ? JSON.parse(res.data.payload) : res.data.payload;
                            var filename = res.data.filename || ('popup-' + uid + '.json');
                            var blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json;charset=utf-8;' });
                            var downloadUrl = URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = downloadUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            setTimeout(function() {
                                document.body.removeChild(a);
                                URL.revokeObjectURL(downloadUrl);
                            }, 100);
                        } else {
                            alert((res && res.data && res.data.message) ? res.data.message : 'Export failed.');
                        }
                    },
                    error: function() {
                        alert('Communication error exporting popup configuration.');
                    }
                });
            });
        },

        bindShortcodeCopy: function() {
            $(document).on('click', '.wppoppop-shortcode-chip', function(e) {
                e.preventDefault();
                var code = $(this).attr('data-copy') || $(this).text().trim();
                var $chip = $(this);
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(code).then(function() {
                        var orig = $chip.text();
                        $chip.text('Copied!');
                        setTimeout(function() {
                            $chip.text(orig);
                        }, 1400);
                    });
                }
            });
        },

        bindSelectAll: function() {
            var self = this;

            $('#cb-select-all, #cb-select-all-2').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('#cb-select-all, #cb-select-all-2').prop('checked', isChecked);
                $('#wppoppop-table-tbody .wppoppop-table-row:visible').find('.wppoppop-row-checkbox').prop('checked', isChecked);
                self.updateBulkActionsState();
            });

            $(document).on('change', '.wppoppop-row-checkbox', function() {
                self.syncSelectAllCheckbox();
                self.updateBulkActionsState();
            });
        },

        syncSelectAllCheckbox: function() {
            var $visibleCheckboxes = $('#wppoppop-table-tbody .wppoppop-table-row:visible').find('.wppoppop-row-checkbox');
            if ($visibleCheckboxes.length === 0) {
                $('#cb-select-all, #cb-select-all-2').prop('checked', false).prop('indeterminate', false);
                return;
            }

            var checkedCount = $visibleCheckboxes.filter(':checked').length;
            var allChecked = checkedCount === $visibleCheckboxes.length;
            var someChecked = checkedCount > 0 && !allChecked;

            $('#cb-select-all, #cb-select-all-2').prop('checked', allChecked).prop('indeterminate', someChecked);
        },

        updateBulkActionsState: function() {
            var selectedUids = this.getSelectedUids();
            var $badge = $('.wppoppop-selected-count-badge');
            var $applyBtn = $('.wppoppop-bulk-action-apply');

            if (selectedUids.length > 0) {
                $badge.show().text(selectedUids.length + ' selected');
                $applyBtn.prop('disabled', false);
            } else {
                $badge.hide();
                $applyBtn.prop('disabled', true);
            }
        },

        getSelectedUids: function() {
            var uids = [];
            $('.wppoppop-row-checkbox:checked').each(function() {
                var val = $(this).val();
                if (val) {
                    uids.push(val);
                }
            });
            return uids;
        },

        bindBulkActions: function() {
            var self = this;

            $('.wppoppop-bulk-action-selector').on('change', function() {
                var val = $(this).val();
                $('.wppoppop-bulk-action-selector').val(val);
            });

            $(document).on('click', '.wppoppop-bulk-action-apply', function(e) {
                e.preventDefault();
                var action = $(this).siblings('.wppoppop-bulk-action-selector').val();
                var uids = self.getSelectedUids();

                if (!action || uids.length === 0) {
                    return;
                }

                var $btn = $(this);
                var origText = $btn.text();

                if (action === 'publish' || action === 'draft') {
                    $btn.prop('disabled', true).text('Updating...');
                    $.ajax({
                        url: self.getAjaxUrl(),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wppoppop_bulk_status',
                            nonce: self.getNonce(),
                            status: action,
                            uids: uids
                        },
                        success: function(res) {
                            if (res && res.success) {
                                $.each(uids, function(i, uid) {
                                    var $row = $('#wppoppop-table-tbody .wppoppop-table-row[data-uid="' + uid + '"]');
                                    var $badge = $row.find('.wppoppop-status-badge');
                                    var $postState = $row.find('.wppoppop-post-state');

                                    $row.attr('data-status', action);
                                    if (action === 'publish') {
                                        $row.removeClass('status-draft').addClass('status-publish');
                                        $postState.hide();
                                        $badge.removeClass('badge-inactive').addClass('badge-active').text('Publish');
                                    } else {
                                        $row.removeClass('status-publish').addClass('status-draft');
                                        $postState.show();
                                        $badge.removeClass('badge-active').addClass('badge-inactive').text('Draft');
                                    }
                                });
                                $('.wppoppop-row-checkbox, #cb-select-all, #cb-select-all-2').prop('checked', false);
                                self.updateBulkActionsState();
                                self.recalculatePillCounts();
                            } else {
                                alert((res.data && res.data.message) ? res.data.message : 'Status update failed.');
                            }
                            $btn.prop('disabled', false).text(origText);
                        },
                        error: function() {
                            alert('Communication error updating status.');
                            $btn.prop('disabled', false).text(origText);
                        }
                    });
                } else if (action === 'duplicate') {
                    $btn.prop('disabled', true).text('Duplicating...');
                    $.ajax({
                        url: self.getAjaxUrl(),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wppoppop_bulk_duplicate',
                            nonce: self.getNonce(),
                            uids: uids
                        },
                        success: function(res) {
                            if (res && res.success) {
                                window.location.reload();
                            } else {
                                alert((res.data && res.data.message) ? res.data.message : 'Duplication failed.');
                                $btn.prop('disabled', false).text(origText);
                            }
                        },
                        error: function() {
                            alert('Communication error during duplication.');
                            $btn.prop('disabled', false).text(origText);
                        }
                    });
                } else if (action === 'export') {
                    $btn.prop('disabled', true).text('Exporting...');
                    $.ajax({
                        url: self.getAjaxUrl(),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wppoppop_bulk_export_selected',
                            nonce: self.getNonce(),
                            uids: uids
                        },
                        success: function(res) {
                            if (res && res.success && res.data) {
                                var payload = res.data.payload;
                                var filename = res.data.filename || ('wppoppop-selected-export-' + new Date().toISOString().slice(0, 10) + '.json');
                                var blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json;charset=utf-8;' });
                                var downloadUrl = URL.createObjectURL(blob);
                                var a = document.createElement('a');
                                a.style.display = 'none';
                                a.href = downloadUrl;
                                a.download = filename;
                                document.body.appendChild(a);
                                a.click();
                                setTimeout(function() {
                                    document.body.removeChild(a);
                                    URL.revokeObjectURL(downloadUrl);
                                }, 100);

                                $('.wppoppop-row-checkbox, #cb-select-all, #cb-select-all-2').prop('checked', false);
                                self.updateBulkActionsState();
                            } else {
                                alert((res.data && res.data.message) ? res.data.message : 'Selective export failed.');
                            }
                            $btn.prop('disabled', false).text(origText);
                        },
                        error: function() {
                            alert('Communication error generating export.');
                            $btn.prop('disabled', false).text(origText);
                        }
                    });
                } else if (action === 'delete') {
                    if (!window.confirm('Are you sure you want to permanently delete the ' + uids.length + ' selected popup campaign(s)? This cannot be undone.')) {
                        return;
                    }

                    $btn.prop('disabled', true).text('Deleting...');
                    $.ajax({
                        url: self.getAjaxUrl(),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wppoppop_bulk_delete',
                            nonce: self.getNonce(),
                            uids: uids
                        },
                        success: function(response) {
                            if (response && response.success) {
                                $.each(uids, function(i, uid) {
                                    $('#wppoppop-table-tbody .wppoppop-table-row[data-uid="' + uid + '"]').remove();
                                });
                                $('.wppoppop-row-checkbox, #cb-select-all, #cb-select-all-2').prop('checked', false);
                                self.updateBulkActionsState();
                                self.recalculatePillCounts();
                            } else {
                                alert((response.data && response.data.message) ? response.data.message : 'Bulk deletion failed.');
                            }
                            $btn.prop('disabled', false).text(origText);
                        },
                        error: function() {
                            alert('Communication error while processing bulk deletion.');
                            $btn.prop('disabled', false).text(origText);
                        }
                    });
                }
            });
        },

        bindColumnToggles: function() {
            var self = this;

            $(document).on('click', '.wppoppop-column-toggle-btn', function(e) {
                e.stopPropagation();
                var $menu = $(this).siblings('.wppoppop-column-dropdown');
                $menu.toggle();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.wppoppop-column-toggle-wrapper').length) {
                    $('.wppoppop-column-dropdown').hide();
                }
            });

            $('.wppoppop-col-toggle-cb').on('change', function() {
                var colClass = $(this).attr('data-col');
                var isChecked = $(this).is(':checked');
                $('.' + colClass).toggle(isChecked);
                self.saveColumnPreferences();
            });
        },

        saveColumnPreferences: function() {
            var prefs = {};
            $('.wppoppop-col-toggle-cb').each(function() {
                var col = $(this).attr('data-col');
                prefs[col] = $(this).is(':checked');
            });
            try {
                localStorage.setItem(this.storageKey, JSON.stringify(prefs));
            } catch (e) {}
        },

        restoreColumnToggles: function() {
            var saved = null;
            try {
                saved = JSON.parse(localStorage.getItem(this.storageKey));
            } catch (e) {}

            if (saved && typeof saved === 'object') {
                $('.wppoppop-col-toggle-cb').each(function() {
                    var col = $(this).attr('data-col');
                    if (saved[col] !== undefined) {
                        var isChecked = Boolean(saved[col]);
                        $(this).prop('checked', isChecked);
                        $('.' + col).toggle(isChecked);
                    }
                });
            }
        }
    };

    $(function() {
        window.WpPopPopDashboardTable.init();
    });
})(jQuery);
