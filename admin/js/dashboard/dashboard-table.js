/**
 * WpPopPop Dashboard: Native Table Controller
 * Handles WordPress-style pagination, Select All, column toggles, dual Tablenav controls, sorting, filters, and Quick Edit.
 */
(function($) {
    'use strict';

    window.WpPopPopDashboardTable = {
        currentPage: 1,
        perPage: 25,
        totalItems: 0,
        activeSearchQuery: '',
        activeStatusFilter: 'all',
        sortColumn: 'title',
        sortDirection: 'asc',
        storageKey: 'wppoppop_dash_col_visibility',

        init: function() {
            this.bindPerPerPage();
            this.bindPaginationNav();
            this.bindSelectAll();
            this.bindBulkActions();
            this.bindColumnToggles();
            this.bindSorting();
            this.bindStatusFilters();
            this.bindQuickEdit();
            this.restoreColumnToggles();
            this.sortRows(this.sortColumn, this.sortDirection);
            this.render();
        },

        getMatchingRows: function() {
            var self = this;
            var $rows = $('#wppoppop-table-tbody .wppoppop-table-row');

            return $rows.filter(function() {
                var $row = $(this);
                var title = $row.attr('data-title') || '';
                var uid = $row.attr('data-uid') || '';
                var status = $row.attr('data-status') || '';
                var cr = parseFloat($row.attr('data-cr')) || 0;

                if (self.activeStatusFilter === 'publish' && status !== 'publish') {
                    return false;
                }
                if (self.activeStatusFilter === 'draft' && status !== 'draft') {
                    return false;
                }
                if (self.activeStatusFilter === 'high-cr' && cr < 10.0) {
                    return false;
                }

                if (self.activeSearchQuery) {
                    if (title.indexOf(self.activeSearchQuery) === -1 && uid.indexOf(self.activeSearchQuery) === -1) {
                        return false;
                    }
                }

                return true;
            });
        },

        filter: function(query) {
            this.activeSearchQuery = (query || '').toLowerCase().trim();
            this.currentPage = 1;
            this.render();
        },

        reindex: function() {
            this.recalculatePillCounts();
            this.render();
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

        sortRows: function(column, direction) {
            var $tbody = $('#wppoppop-table-tbody');
            var $rows = $tbody.find('.wppoppop-table-row').get();

            $rows.sort(function(a, b) {
                var valA, valB;
                if (column === 'title') {
                    valA = $(a).attr('data-title') || '';
                    valB = $(b).attr('data-title') || '';
                    return direction === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                } else if (column === 'impressions') {
                    valA = parseInt($(a).attr('data-impressions'), 10) || 0;
                    valB = parseInt($(b).attr('data-impressions'), 10) || 0;
                    return direction === 'asc' ? valA - valB : valB - valA;
                } else if (column === 'submissions') {
                    valA = parseInt($(a).attr('data-submissions'), 10) || 0;
                    valB = parseInt($(b).attr('data-submissions'), 10) || 0;
                    return direction === 'asc' ? valA - valB : valB - valA;
                } else if (column === 'cr') {
                    valA = parseFloat($(a).attr('data-cr')) || 0;
                    valB = parseFloat($(b).attr('data-cr')) || 0;
                    return direction === 'asc' ? valA - valB : valB - valA;
                }
                return 0;
            });

            $.each($rows, function(idx, row) {
                $tbody.append(row);
            });
        },

        bindSorting: function() {
            var self = this;

            $(document).on('click', '.wppoppop-sortable-th a', function(e) {
                e.preventDefault();
                var $th = $(this).closest('.wppoppop-sortable-th');
                var targetCol = $th.attr('data-sort');
                if (!targetCol) return;

                if (self.sortColumn === targetCol) {
                    self.sortDirection = (self.sortDirection === 'asc') ? 'desc' : 'asc';
                } else {
                    self.sortColumn = targetCol;
                    self.sortDirection = (targetCol === 'title') ? 'asc' : 'desc';
                }

                $('.wppoppop-sortable-th').each(function() {
                    var col = $(this).attr('data-sort');
                    $(this).removeClass('asc desc sorted');

                    if (col === self.sortColumn) {
                        $(this).addClass('sorted ' + self.sortDirection);
                    }
                });

                self.sortRows(self.sortColumn, self.sortDirection);
                self.render();
            });
        },

        bindStatusFilters: function() {
            var self = this;

            $(document).on('click', '.wppoppop-status-pill', function(e) {
                e.preventDefault();
                var status = $(this).attr('data-status');
                if (!status) return;

                $('.wppoppop-status-pill').removeClass('current');
                $(this).addClass('current');

                self.activeStatusFilter = status;
                self.currentPage = 1;
                self.render();
            });
        },

        bindQuickEdit: function() {
            var self = this;

            $(document).on('click', '.wppoppop-quick-edit-btn', function(e) {
                e.preventDefault();
                var uid = $(this).attr('data-uid');
                var $targetRow = $('#wppoppop-row-' + uid);
                if (!$targetRow.length) return;

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

            $(document).on('click', '.wppoppop-qe-btn-cancel', function(e) {
                e.preventDefault();
                var $inlineRow = $(this).closest('.wppoppop-active-quick-edit');
                var uid = $inlineRow.attr('data-editing-uid');
                $inlineRow.remove();
                $('#wppoppop-row-' + uid).fadeIn(200);
            });

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

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_quick_edit',
                        nonce: nonce,
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
                            self.render();
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

        render: function() {
            var self = this;
            var $allRows = $('#wppoppop-table-tbody .wppoppop-table-row');
            var $matchingRows = self.getMatchingRows();

            self.totalItems = $matchingRows.length;
            $allRows.hide();

            var perPageVal = self.perPage === 'all' ? self.totalItems : parseInt(self.perPage, 10);
            var totalPages = Math.ceil(self.totalItems / perPageVal) || 1;

            if (self.totalItems === 0) {
                $('.wppoppop-total-items-text').text('0');
                $('.wppoppop-current-page-text').text('0');
                $('.wppoppop-total-pages-text').text('0');
                $('.wppoppop-btn-first, .wppoppop-btn-prev, .wppoppop-btn-next, .wppoppop-btn-last').prop('disabled', true);
                $('#wppoppop-table-tbody .no-items').show();
                return;
            }

            $('#wppoppop-table-tbody .no-items').hide();

            if (self.currentPage > totalPages) {
                self.currentPage = totalPages;
            }
            if (self.currentPage < 1) {
                self.currentPage = 1;
            }

            var startIndex = (self.currentPage - 1) * perPageVal;
            var endIndex = Math.min(startIndex + perPageVal, self.totalItems);

            $matchingRows.slice(startIndex, endIndex).show();

            $('.wppoppop-total-items-text').text(self.totalItems);
            $('.wppoppop-current-page-text').text(self.currentPage);
            $('.wppoppop-total-pages-text').text(totalPages);

            $('.wppoppop-btn-first, .wppoppop-btn-prev').prop('disabled', self.currentPage <= 1);
            $('.wppoppop-btn-next, .wppoppop-btn-last').prop('disabled', self.currentPage >= totalPages);

            self.syncSelectAllCheckbox();
        },

        bindPerPerPage: function() {
            var self = this;
            $('#wppoppop-per-page-select').on('change', function() {
                self.perPage = $(this).val();
                self.currentPage = 1;
                self.render();
            });
        },

        bindPaginationNav: function() {
            var self = this;

            $(document).on('click', '.wppoppop-btn-first', function() {
                self.currentPage = 1;
                self.render();
            });

            $(document).on('click', '.wppoppop-btn-prev', function() {
                if (self.currentPage > 1) {
                    self.currentPage--;
                    self.render();
                }
            });

            $(document).on('click', '.wppoppop-btn-next', function() {
                var perPageVal = self.perPage === 'all' ? self.totalItems : parseInt(self.perPage, 10);
                var totalPages = Math.ceil(self.totalItems / perPageVal);
                if (self.currentPage < totalPages) {
                    self.currentPage++;
                    self.render();
                }
            });

            $(document).on('click', '.wppoppop-btn-last', function() {
                var perPageVal = self.perPage === 'all' ? self.totalItems : parseInt(self.perPage, 10);
                self.currentPage = Math.ceil(self.totalItems / perPageVal);
                self.render();
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

            // Keep top and bottom bulk action selectors synchronized
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

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';
                var $btn = $(this);
                var origText = $btn.text();

                if (action === 'publish' || action === 'draft') {
                    $btn.prop('disabled', true).text('Updating...');
                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wppoppop_bulk_status',
                            nonce: nonce,
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
                                self.reindex();
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
                        url: ajaxUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wppoppop_bulk_duplicate',
                            nonce: nonce,
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
                        url: ajaxUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wppoppop_bulk_export_selected',
                            nonce: nonce,
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
                        url: ajaxUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wppoppop_bulk_delete',
                            nonce: nonce,
                            uids: uids
                        },
                        success: function(response) {
                            if (response && response.success) {
                                $.each(uids, function(i, uid) {
                                    $('#wppoppop-table-tbody .wppoppop-table-row[data-uid="' + uid + '"]').remove();
                                });
                                $('.wppoppop-row-checkbox, #cb-select-all, #cb-select-all-2').prop('checked', false);
                                self.updateBulkActionsState();
                                self.reindex();
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
})(jQuery);
