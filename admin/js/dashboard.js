
(function($) {
    'use strict';

    $(document).ready(function() {
        // 1. Context Action Dropdown Toggle
        $(document).on('click', '.wppoppop-action-btn', function(e) {
            e.stopPropagation();
            const currentMenu = $(this).siblings('.wppoppop-action-menu');
            $('.wppoppop-action-menu').not(currentMenu).removeClass('active');
            currentMenu.toggleClass('active');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.wppoppop-action-wrap').length) {
                $('.wppoppop-action-menu').removeClass('active');
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.wppoppop-action-menu').removeClass('active');
            }
        });

        // 2. 1-Click Shortcode Badge Copy
        $(document).on('click', '.popup-shortcode-badge', function(e) {
            e.preventDefault();
            const badge = $(this);
            const code = badge.data('shortcode') || badge.find('code').text();

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(function() {
                    flashBadgeCopied(badge);
                });
            } else {
                const temp = $('<textarea>').val(code).appendTo('body').select();
                document.execCommand('copy');
                temp.remove();
                flashBadgeCopied(badge);
            }
        });

        function flashBadgeCopied(badge) {
            const icon = badge.find('.copy-icon');
            const origClass = icon.attr('class');
            icon.attr('class', 'dashicons dashicons-yes-alt copy-icon').css('color', '#10b981');
            setTimeout(function() {
                icon.attr('class', origClass).css('color', '');
            }, 2000);
        }

        // 3. Instant Live Table Search
        $('#wppoppop-search-input').on('input', function() {
            const q = $(this).val().toLowerCase().trim();
            $('#wppoppop-popups-table tbody tr').each(function() {
                const row = $(this);
                if (row.hasClass('no-items')) return;
                const uid = (row.data('uid') || '').toString().toLowerCase();
                const title = (row.data('title') || '').toString().toLowerCase();
                if (!q || uid.indexOf(q) !== -1 || title.indexOf(q) !== -1) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        });

        // 4. Toggle Status (Active / Inactive) via AJAX
        $(document).on('click', '.action-btn-toggle', function(e) {
            e.preventDefault();
            const btn = $(this);
            const uid = btn.data('uid');
            btn.closest('.wppoppop-action-menu').removeClass('active');
            btn.prop('disabled', true);

            $.post(wppoppop_dash_vars.ajax_url, {
                action: 'wppoppop_toggle_popup_status',
                nonce: wppoppop_dash_vars.nonce,
                uid: uid
            }, function(res) {
                btn.prop('disabled', false);
                if (res.success) {
                    const badge = $('#badge-status-' + uid);
                    if (res.data.status === 'publish') {
                        badge.removeClass('status-draft').addClass('status-active').text('Active');
                        btn.text('Deactivate');
                    } else {
                        badge.removeClass('status-active').addClass('status-draft').text('Inactive');
                        btn.text('Activate');
                    }
                } else {
                    alert(res.data && res.data.message ? res.data.message : 'Error changing status.');
                }
            }).fail(function() {
                btn.prop('disabled', false);
                alert('Connection failure.');
            });
        });

        // 5. Duplicate Popup via AJAX
        $(document).on('click', '.action-btn-duplicate', function(e) {
            e.preventDefault();
            const btn = $(this);
            const uid = btn.data('uid');
            btn.closest('.wppoppop-action-menu').removeClass('active');

            btn.prop('disabled', true);
            $.post(wppoppop_dash_vars.ajax_url, {
                action: 'wppoppop_duplicate_popup',
                nonce: wppoppop_dash_vars.nonce,
                uid: uid
            }, function(res) {
                if (res.success) {
                    location.reload();
                } else {
                    btn.prop('disabled', false);
                    alert(res.data && res.data.message ? res.data.message : 'Duplication failed.');
                }
            }).fail(function() {
                btn.prop('disabled', false);
                alert('Connection error.');
            });
        });

        // 6. Reset Statistics via AJAX
        $(document).on('click', '.action-btn-reset-stats', function(e) {
            e.preventDefault();
            const btn = $(this);
            const uid = btn.data('uid');
            btn.closest('.wppoppop-action-menu').removeClass('active');

            if (!confirm('Are you sure you want to reset impressions and submissions counters to 0?')) {
                return;
            }

            btn.prop('disabled', true);
            $.post(wppoppop_dash_vars.ajax_url, {
                action: 'wppoppop_reset_stats',
                nonce: wppoppop_dash_vars.nonce,
                uid: uid
            }, function(res) {
                btn.prop('disabled', false);
                if (res.success) {
                    $('#metric-impressions-' + uid).text('0');
                    $('#metric-submissions-' + uid).text('0');
                    $('#metric-cr-' + uid).html('<strong>0%</strong>');
                } else {
                    alert(res.data && res.data.message ? res.data.message : 'Reset failed.');
                }
            }).fail(function() {
                btn.prop('disabled', false);
                alert('Server error.');
            });
        });

        // 7. Delete Popup via AJAX
        $(document).on('click', '.action-btn-delete', function(e) {
            e.preventDefault();
            const btn = $(this);
            const uid = btn.data('uid');
            const row = $('#popup-row-' + uid).length ? $('#popup-row-' + uid) : btn.closest('tr');
            btn.closest('.wppoppop-action-menu').removeClass('active');

            if (!confirm('Are you sure you want to permanently delete this popup campaign?')) {
                return;
            }

            btn.prop('disabled', true);
            $.post(wppoppop_dash_vars.ajax_url, {
                action: 'wppoppop_delete_popup',
                nonce: wppoppop_dash_vars.nonce,
                uid: uid
            }, function(res) {
                if (res.success) {
                    row.fadeOut(300, function() {
                        row.remove();
                        if ($('#wppoppop-popups-table tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    btn.prop('disabled', false);
                    alert(res.data && res.data.message ? res.data.message : 'Deletion failed.');
                }
            }).fail(function() {
                btn.prop('disabled', false);
                alert('Server error.');
            });
        });
    });
})(jQuery);
