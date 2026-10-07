/**
 * WpPopPop Dashboard: Table Controller
 * Handles client-side pagination, Select All management, and bulk lifecycle actions.
 */
(function($) {
    'use strict';

    window.WpPopPopDashboardTable = {
        currentPage: 1,
        perPage: 25,
        totalItems: 0,
        activeSearchQuery: '',

        init: function() {
            this.bindPerPerPage();
            this.bindPaginationNav();
            this.bindSelectAll();
            this.bindBulkActions();
            this.render();
        },

        getMatchingRows: function() {
            var self = this;
            var $rows =$('#wppoppop-table-tbody .wppoppop-table-row');
            if (!self.activeSearchQuery) {
                return $rows;
            }
            return $rows.filter(function() {
                var title = $(this).attr('data-title') || '';
                var uid = $(this).attr('data-uid') || '';
                return title.indexOf(self.activeSearchQuery) !== -1 || uid.indexOf(self.activeSearchQuery) !== -1;
            });
        },

        filter: function(query) {
            this.activeSearchQuery = (query || '').toLowerCase().trim();
            this.currentPage = 1;
            this.render();
        },

        reindex: function() {
            this.render();
        },

        render: function() {
            var self = this;
            var $allRows =$('#wppoppop-table-tbody .wppoppop-table-row');
            var $matchingRows = self.getMatchingRows();

            self.totalItems = $matchingRows.length;
            $allRows.hide();

            if (self.totalItems === 0) {
                $('#wppoppop-page-start').text('0');
                $('#wppoppop-page-end').text('0');
                $('#wppoppop-total-items').text('0');
                $('#wppoppop-page-numbers').empty();
                $('#wppoppop-btn-first, #wppoppop-btn-prev, #wppoppop-btn-next, #wppoppop-btn-last').prop('disabled', true);
                if ($('#wppoppop-table-tbody .wppoppop-no-items-row').length === 0) {
                    $('#wppoppop-table-tbody').append(
                        '<tr class="wppoppop-no-items-row"><td colspan="7" style="text-align:center;padding:48px 20px;color:#64748b;">No popups match the filter.</td></tr>'
                    );
                }
                $('#wppoppop-table-tbody .wppoppop-no-items-row').show();
                return;
            } else {
                $('#wppoppop-table-tbody .wppoppop-no-items-row').hide();
            }

            var perPageVal = self.perPage === 'all' ? self.totalItems : parseInt(self.perPage, 10);
            var totalPages = Math.ceil(self.totalItems / perPageVal) || 1;

            if (self.currentPage > totalPages) {
                self.currentPage = totalPages;
            }
            if (self.currentPage < 1) {
                self.currentPage = 1;
            }

            var startIndex = (self.currentPage - 1) * perPageVal;
            var endIndex = Math.min(startIndex + perPageVal, self.totalItems);

            $matchingRows.slice(startIndex, endIndex).show();

            $('#wppoppop-page-start').text(startIndex + 1);
            $('#wppoppop-page-end').text(endIndex);
            $('#wppoppop-total-items').text(self.totalItems);

            self.renderPageButtons(totalPages);
            self.syncSelectAllCheckbox();
        },

        renderPageButtons: function(totalPages) {
            var self = this;
            var $container =$('#wppoppop-page-numbers');
            $container.empty();

            $('#wppoppop-btn-first, #wppoppop-btn-prev').prop('disabled', self.currentPage <= 1);
            $('#wppoppop-btn-next, #wppoppop-btn-last').prop('disabled', self.currentPage >= totalPages);

            if (self.perPage === 'all' || totalPages <= 1) {
                return;
            }

            var startPage = Math.max(1, self.currentPage - 2);
            var endPage = Math.min(totalPages, self.currentPage + 2);

            for (var p = startPage; p <= endPage; p++) {
                var $btn =$('<button type="button" class="button wppoppop-page-num-btn"></button>')
                    .text(p)
                    .attr('data-page', p);

                if (p === self.currentPage) {
                    $btn.addClass('button-primary').css({ 'background': '#0284c7', 'border-color': '#0284c7' });
                }
                $container.append($btn);
            }
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

            $('#wppoppop-btn-first').on('click', function() {
                self.currentPage = 1;
                self.render();
            });

            $('#wppoppop-btn-prev').on('click', function() {
                if (self.currentPage > 1) {
                    self.currentPage--;
                    self.render();
                }
            });

            $('#wppoppop-btn-next').on('click', function() {
                var perPageVal = self.perPage === 'all' ? self.totalItems : parseInt(self.perPage, 10);
                var totalPages = Math.ceil(self.totalItems / perPageVal);
                if (self.currentPage < totalPages) {
                    self.currentPage++;
                    self.render();
                }
            });

            $('#wppoppop-btn-last').on('click', function() {
                var perPageVal = self.perPage === 'all' ? self.totalItems : parseInt(self.perPage, 10);
                self.currentPage = Math.ceil(self.totalItems / perPageVal);
                self.render();
            });

            $(document).on('click', '.wppoppop-page-num-btn', function() {
                self.currentPage = parseInt($(this).attr('data-page'), 10);
                self.render();
            });
        },

        bindSelectAll: function() {
            var self = this;

            $('#cb-select-all, #cb-select-all-2').on('change', function() {
                var isChecked = $(this).is(':checked');$('#cb-select-all, #cb-select-all-2').prop('checked', isChecked);
                $('#wppoppop-table-tbody .wppoppop-table-row:visible').find('.wppoppop-row-checkbox').prop('checked', isChecked);
                self.updateBulkActionsState();
            });

            $(document).on('change', '.wppoppop-row-checkbox', function() {
                self.syncSelectAllCheckbox();
                self.updateBulkActionsState();
            });
        },

        syncSelectAllCheckbox: function() {
            var $visibleCheckboxes =$('#wppoppop-table-tbody .wppoppop-table-row:visible').find('.wppoppop-row-checkbox');
            if ($visibleCheckboxes.length === 0) {$('#cb-select-all, #cb-select-all-2').prop('checked', false).prop('indeterminate', false);
                return;
            }

            var checkedCount = $visibleCheckboxes.filter(':checked').length;
            var allChecked = checkedCount === $visibleCheckboxes.length;
            var someChecked = checkedCount > 0 && !allChecked;

            $('#cb-select-all, #cb-select-all-2').prop('checked', allChecked).prop('indeterminate', someChecked);
        },

        updateBulkActionsState: function() {
            var selectedUids = this.getSelectedUids();
            var $badge =$('#wppoppop-selected-count-badge');
            var $applyBtn =$('#wppoppop-bulk-action-apply');

            if (selectedUids.length > 0) {
                $badge.show().text(selectedUids.length + ' selected');$applyBtn.prop('disabled', false);
            } else {
                $badge.hide();$applyBtn.prop('disabled', true);
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
            $('#wppoppop-bulk-action-apply').on('click', function(e) {
                e.preventDefault();
                var action = $('#wppoppop-bulk-action-selector').val();
                var uids = self.getSelectedUids();

                if (!action || uids.length === 0) {
                    return;
                }

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';
                var $btn =$(this);
                var origText = $btn.text();

                // 1. Bulk Status Update (Publish / Draft)
                if (action === 'publish' || action === 'draft') {
                    $btn.prop('disabled', true).text('Updating...');$.ajax({
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
                                    var $row =$('#wppoppop-table-tbody .wppoppop-table-row[data-uid="' + uid + '"]');
                                    var $badge =$row.find('.wppoppop-badge');
                                    if (action === 'publish') {
                                        $badge.css({ 'background': '#dcfce7', 'color': '#15803d' }).text('Publish');
                                    } else {
                                        $badge.css({ 'background': '#f1f5f9', 'color': '#64748b' }).text('Draft');
                                    }
                                });
                                $('.wppoppop-row-checkbox, #cb-select-all, #cb-select-all-2').prop('checked', false);
                                self.updateBulkActionsState();
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
                }

                // 2. Bulk Duplication
                else if (action === 'duplicate') {
                    $btn.prop('disabled', true).text('Duplicating...');$.ajax({
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
                }

                // 3. Selective JSON Export
                else if (action === 'export') {
                    $btn.prop('disabled', true).text('Exporting...');$.ajax({
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
                                var jsonString = JSON.stringify(payload, null, 2);

                                var blob = new Blob([jsonString], { type: 'application/json;charset=utf-8;' });
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
                }

                // 4. Bulk Delete
                else if (action === 'delete') {
                    if (!window.confirm('Are you sure you want to permanently delete the ' + uids.length + ' selected popup campaign(s)? This cannot be undone.')) {
                        return;
                    }

                    $btn.prop('disabled', true).text('Deleting...');$.ajax({
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
                                self.render();
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
        }
    };
})(jQuery);
