
jQuery(document).ready(function($) {
    'use strict';

    // Toggle Action Popover Menu
    $(document).on('click', '.wppoppop-action-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const wrap = $(this).closest('.wppoppop-action-wrap');
        const menu = wrap.find('.wppoppop-action-menu');

        $('.wppoppop-action-menu').not(menu).removeClass('show');
        $('.wppoppop-action-btn').not(this).removeClass('active');

        $(this).toggleClass('active');
        menu.toggleClass('show');
    });

    // Close Dropdowns on Click Outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.wppoppop-action-wrap').length) {
            $('.wppoppop-action-menu').removeClass('show');
            $('.wppoppop-action-btn').removeClass('active');
        }
    });

    // Close on ESC
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('.wppoppop-action-menu').removeClass('show');
            $('.wppoppop-action-btn').removeClass('active');
        }
    });

    // 1-Click Copy Shortcode
    $(document).on('click', '.popup-shortcode-badge', function(e) {
        e.preventDefault();
        const badge = $(this);
        const code = badge.data('shortcode');

        navigator.clipboard.writeText(code).then(function() {
            const origHtml = badge.html();
            badge.html('<span style="font-size:11px;font-weight:700;color:#15803d;">Copied!</span>');
            setTimeout(function() {
                badge.html(origHtml);
            }, 1400);
        });
    });

    // Action 1: Toggle Status (Activate / Deactivate)
    $(document).on('click', '.action-btn-toggle', function(e) {
        e.preventDefault();
        const btn = $(this);
        const uid = btn.data('uid');
        const badge = $('#badge-status-' + uid);

        btn.text('Updating...');

        $.post(wppoppop_dash_vars.ajax_url, {
            action: 'wppoppop_toggle_status',
            nonce: wppoppop_dash_vars.nonce,
            uid: uid
        }, function(res) {
            if (res.success) {
                btn.text(res.data.action_text);
                badge.text(res.data.status_label)
                     .removeClass('status-active status-draft')
                     .addClass(res.data.is_active ? 'status-active' : 'status-draft');
                $('.wppoppop-action-menu').removeClass('show');
            } else {
                alert(res.data ? res.data.message : 'Error updating status');
            }
        });
    });

    // Action 2: Duplicate Popup
    $(document).on('click', '.action-btn-duplicate', function(e) {
        e.preventDefault();
        const uid = $(this).data('uid');
        if (!confirm('Duplicate this popup configuration?')) return;

        $(this).text('Duplicating...');

        $.post(wppoppop_dash_vars.ajax_url, {
            action: 'wppoppop_duplicate_popup',
            nonce: wppoppop_dash_vars.nonce,
            uid: uid
        }, function(res) {
            if (res.success) {
                window.location.reload();
            } else {
                alert(res.data ? res.data.message : 'Duplication failed.');
            }
        });
    });

    // Action 3: Reset Statistics
    $(document).on('click', '.action-btn-reset-stats', function(e) {
        e.preventDefault();
        const uid = $(this).data('uid');
        if (!confirm('Are you sure you want to reset impressions and submissions counters for this popup?')) return;

        $.post(wppoppop_dash_vars.ajax_url, {
            action: 'wppoppop_reset_stats',
            nonce: wppoppop_dash_vars.nonce,
            uid: uid
        }, function(res) {
            if (res.success) {
                $('#metric-impressions-' + uid).text('0');
                $('#metric-submissions-' + uid).text('0');
                $('#metric-cr-' + uid).html('<strong>0%</strong>');
                $('.wppoppop-action-menu').removeClass('show');
                alert(res.data.message);
            } else {
                alert('Reset failed.');
            }
        });
    });

    // Action 4: Delete Popup
    $(document).on('click', '.action-btn-delete', function(e) {
        e.preventDefault();
        const uid = $(this).data('uid');
        if (!confirm('Permanently delete this popup? This cannot be undone.')) return;

        const row = $('#popup-row-' + uid);
        row.css('opacity', '0.4');

        $.post(wppoppop_dash_vars.ajax_url, {
            action: 'wppoppop_delete_popup',
            nonce: wppoppop_dash_vars.nonce,
            uid: uid
        }, function(res) {
            if (res.success) {
                row.fadeOut(300, function() { $(this).remove(); });
            } else {
                row.css('opacity', '1');
                alert('Deletion failed.');
            }
        });
    });

    // Live Instant Search Filter
    $('#wppoppop-search-input').on('keyup', function() {
        const query = $(this).val().toLowerCase().trim();
        $('#wppoppop-popups-table tbody tr').not('.no-items').each(function() {
            const title = $(this).data('title') || '';
            const uid = $(this).data('uid') || '';
            if (title.indexOf(query) !== -1 || uid.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
