/**
 * WpPopPop Dashboard: Native Table Controller
 * Handles native WordPress lifecycle actions, Quick Edit, and column visibility.
 */
(function(window, $) {
    'use strict';

    window.WpPopPopDashboard = window.WpPopPopDashboard || {};

    var Table = {
        init: function() {
            this.bindSelectAll();
            this.bindBulkActions();
            this.bindColumnToggles();
            this.bindQuickEdit();
        },

        getVars: function() {
            if (window.wppoppop_vars && window.wppoppop_vars.ajax_url) {
                return window.wppoppop_vars;
            }
            return {
                ajax_url: typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php',
                nonce: $('#wppoppop_admin_nonce_field').val() || $('#_wpnonce').val() || ''
            };
        },

        bindSelectAll: function() {
            var self = this;
            $(document).on('change', 'th.check-column input[type="checkbox"]', function() {
                var isChecked = $(this).is(':checked');
                $('#wppoppop-table-tbody .wppoppop-select-row:visible').prop('checked', isChecked);
                self.syncSelectAllCheckbox();
                self.updateBulkActionsState();
            });

            $(document).on('change', '.wppoppop-select-row', function() {
                self.syncSelectAllCheckbox();
                self.updateBulkActionsState();
            });
        },

        syncSelectAllCheckbox: function() {
            var $visible = $('#wppoppop-table-tbody .wppoppop-select-row:visible');
            var total = $visible.length;
            var checked = $visible.filter(':checked').length;
            var $master = $('th.check-column input[type="checkbox"]');

            if (total > 0 && checked === total) {
                $master.prop('checked', true).prop('indeterminate', false);
            } else if (checked > 0) {
                $master.prop('checked', false).prop('indeterminate', true);
            } else {
                $master.prop('checked', false).prop('indeterminate', false);
            }
        },

        updateBulkActionsState: function() {
            var checked = $('#wppoppop-table-tbody .wppoppop-select-row:checked').length;
            var $applyBtns = $('#doaction, #doaction2, .wppoppop-bulk-action-apply');
            if (checked > 0) {
                $applyBtns.prop('disabled', false).removeClass('disabled');
            } else {
                $applyBtns.prop('disabled', true).addClass('disabled');
            }
        },

        bindBulkActions: function() {
            var self = this;

            $(document).on('click', '#doaction, #doaction2, .wppoppop-bulk-action-apply', function(e) {
                var $btn = $(this);
                var isTop = $btn.attr('id') === 'doaction';
                var action = isTop ? $('#bulk-action-selector-top').val() : $('#bulk-action-selector-bottom').val();

                if (!action || action === '-1') {
                    alert('Please select a bulk action from the dropdown.');
                    e.preventDefault();
                    return false;
                }

                var selectedCount = $('#wppoppop-table-tbody .wppoppop-select-row:checked').length;
                if (selectedCount === 0) {
                    alert('Please select at least one campaign.');
                    e.preventDefault();
                    return false;
                }

                if (action === 'delete') {
                    if (!window.confirm('Are you sure you want to permanently delete ' + selectedCount + ' selected campaign(s)? This action cannot be undone.')) {
                        e.preventDefault();
                        return false;
                    }
                }

                var $form = $('#wppoppop-campaigns-table-form');
                if ($form.length) {
                    $form.submit();
                    return true;
                }
            });
        },

        bindQuickEdit: function() {
            var self = this;
            $(document).on('click', '.wppoppop-quick-edit-btn', function(e) {
                e.preventDefault();
                var uid = $(this).data('uid');
                var $row = $('#wppoppop-row-' + uid);
                if (!$row.length) return;

                $('.wppoppop-active-quick-edit').remove();

                var currentTitle = $(this).data('title') || $row.find('a.row-title').text().trim();
                var currentStatus = $(this).data('status') || 'publish';

                var $clone = $('#wppoppop-quick-edit-template-root tr').clone();
                $clone.addClass('wppoppop-active-quick-edit').attr('data-editing-uid', uid);
                $clone.find('.wppoppop-qe-input-title').val(currentTitle);
                $clone.find('.wppoppop-qe-input-uid').val(uid);
                $clone.find('.wppoppop-qe-input-status').val(currentStatus);

                $row.hide().after($clone);
                $clone.find('.wppoppop-qe-input-title').focus().select();
            });

            $(document).on('click', '.wppoppop-qe-btn-cancel', function(e) {
                e.preventDefault();
                var $inline = $(this).closest('.wppoppop-active-quick-edit');
                var uid = $inline.attr('data-editing-uid');
                $inline.remove();
                $('#wppoppop-row-' + uid).fadeIn(150);
            });

            $(document).on('click', '.wppoppop-qe-btn-save', function(e) {
                e.preventDefault();
                var $inline = $(this).closest('.wppoppop-active-quick-edit');
                var uid = $inline.attr('data-editing-uid');
                var $target = $('#wppoppop-row-' + uid);
                var newTitle = $inline.find('.wppoppop-qe-input-title').val().trim();
                var newStatus = $inline.find('.wppoppop-qe-input-status').val();
                var $btn = $(this);
                var $spinner = $inline.find('.wppoppop-qe-spinner');
                var $errorNotice = $inline.find('.wppoppop-qe-error-notice');
                var $errorMsg = $inline.find('.wppoppop-qe-error-message');

                if (!newTitle) {
                    $errorMsg.text('Title cannot be empty.');
                    $errorNotice.show();
                    return;
                }

                $errorNotice.hide();
                $spinner.addClass('is-active').show();
                $btn.prop('disabled', true);
                var vars = self.getVars();

                $.ajax({
                    url: vars.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_quick_edit',
                        nonce: vars.nonce,
                        uid: uid,
                        title: newTitle,
                        status: newStatus
                    },
                    success: function(res) {
                        $spinner.removeClass('is-active').hide();
                        $btn.prop('disabled', false);

                        if (res && res.success) {
                            $target.find('a.row-title').text(newTitle);
                            $target.attr('data-raw-title', newTitle).attr('data-status', newStatus);
                            $target.find('.wppoppop-quick-edit-btn').data('title', newTitle).data('status', newStatus);

                            $target.find('.post-state').remove();
                            if (newStatus === 'draft') {
                                $target.find('a.row-title').after(' <span class="post-state">— Draft</span>');
                            }

                            var $badge = $target.find('.wppoppop-badge');
                            if (newStatus === 'publish') {
                                $badge.removeClass('wppoppop-badge-draft').addClass('wppoppop-badge-success').text('Published');
                            } else {
                                $badge.removeClass('wppoppop-badge-success').addClass('wppoppop-badge-draft').text('Draft');
                            }

                            $inline.remove();
                            $target.fadeIn(150);
                        } else {
                            $errorMsg.text((res && res.data && res.data.message) ? res.data.message : 'Update failed.');
                            $errorNotice.show();
                        }
                    },
                    error: function() {
                        $spinner.removeClass('is-active').hide();
                        $btn.prop('disabled', false);
                        $errorMsg.text('Network communication error.');
                        $errorNotice.show();
                    }
                });
            });
        },

        bindColumnToggles: function() {
            var storageKey = 'wppoppop_hidden_cols';
            var savedCols = localStorage.getItem(storageKey);
            if (savedCols) {
                try {
                    var hiddenList = JSON.parse(savedCols);
                    if (Array.isArray(hiddenList)) {
                        hiddenList.forEach(function(colClass) {
                            $('.' + colClass).hide();
                            $('.wppoppop-col-toggle[data-col="' + colClass + '"]').prop('checked', false);
                        });
                    }
                } catch(e) {}
            }

            $(document).on('change', '.wppoppop-col-toggle', function() {
                var colClass = $(this).data('col');
                var isChecked = $(this).is(':checked');
                $('.' + colClass).toggle(isChecked);

                var hidden = [];
                $('.wppoppop-col-toggle:not(:checked)').each(function() {
                    hidden.push($(this).data('col'));
                });
                localStorage.setItem(storageKey, JSON.stringify(hidden));
            });
        }
    };

    window.WpPopPopDashboardTable = Table;
    window.WpPopPopDashboard.Table = Table;

    $(function() {
        Table.init();
    });
})(window, jQuery);
