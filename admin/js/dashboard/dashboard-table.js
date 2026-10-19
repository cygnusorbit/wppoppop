/**
 * WpPopPop Dashboard: Table Management & Selection Engine
 * Cross-Browser Compatibility: Safari, Firefox, Chrome, Edge
 */
(function(window, $) {
    'use strict';
    window.WpPopPopDashboardTable = window.WpPopPopDashboardTable || {};

    var Table = {
        init: function() {
            this.bindSelectAll();
            this.bindBulkActions();
            this.bindRowHighlights();
        },

        bindSelectAll: function() {
            $(document).on('change', '#cb-select-all-1, #cb-select-all-2', function() {
                var isChecked = $(this).is(':checked');
                $('input[name="bulk_ids[]"], #cb-select-all-1, #cb-select-all-2').prop('checked', isChecked);
                $('table.wp-list-table tbody tr').toggleClass('selected', isChecked);
            });

            $(document).on('change', 'input[name="bulk_ids[]"]', function() {
                $(this).closest('tr').toggleClass('selected', $(this).is(':checked'));
                var total = $('input[name="bulk_ids[]"]').length;
                var checked = $('input[name="bulk_ids[]"]:checked').length;
                $('#cb-select-all-1, #cb-select-all-2').prop('checked', total > 0 && total === checked);
            });
        },

        bindBulkActions: function() {
            var vars = window.wppoppop_vars || {};
            $(document).on('submit', '#wppoppop-campaigns-table-form', function(e) {
                var action = $('#bulk-action-selector-top').val();
                if (!action || action === '-1') { action = $('#bulk-action-selector-bottom').val(); }
                if (action === '-1') return;

                var selectedUids = [];
                $('input[name="bulk_ids[]"]:checked').each(function() {
                    selectedUids.push($(this).val());
                });

                if (selectedUids.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one popup to perform this action.');
                    return;
                }

                if (action === 'delete') {
                    if (!window.confirm('Delete ' + selectedUids.length + ' selected popup campaign(s)?')) {
                        e.preventDefault();
                        return;
                    }
                }
            });
        },

        bindRowHighlights: function() {
            $(document).on('mouseenter', 'table.wp-list-table tbody tr', function() {
                $(this).addClass('hovered');
            }).on('mouseleave', 'table.wp-list-table tbody tr', function() {
                $(this).removeClass('hovered');
            });
        }
    };

    window.WpPopPopDashboardTable = Table;
})(window, jQuery);
