(function($) {
    'use strict';

    $(document).ready(function() {
        const matrix = window.wppoppopTargetingMatrix || {
            onload: { active: [], passive: [] },
            onscroll: { active: [], passive: [] },
            onexit: { active: [], passive: [] },
            oninactivity: { active: [], passive: [] },
            contentstart: { active: [], passive: [] },
            contentend: { active: [], passive: [] }
        };
        const allPopups = window.wppoppopAvailablePopups || [];
        let currentEvent = 'onload';

        function renderTargetGrid() {
            const currentData = matrix[currentEvent] || { active: [], passive: [] };
            const $activeGrid = $('#wppoppop-active-grid').empty();
            const $passiveGrid = $('#wppoppop-passive-grid').empty();

            const assigned = new Set([...currentData.active, ...currentData.passive]);
            allPopups.forEach(p => {
                if (!assigned.has(p.id)) {
                    currentData.passive.push(p.id);
                }
            });

            if (currentData.active.length > 0) {
                $('#wppoppop-active-zone .wppoppop-zone-empty').hide();
                currentData.active.forEach(id => {
                    const popup = allPopups.find(p => p.id === id);
                    if (popup) $activeGrid.append(createCard(popup));
                });
            } else {
                $('#wppoppop-active-zone .wppoppop-zone-empty').show();
            }

            if (currentData.passive.length > 0) {
                $('#wppoppop-passive-zone .wppoppop-zone-empty').hide();
                currentData.passive.forEach(id => {
                    const popup = allPopups.find(p => p.id === id);
                    if (popup) $passiveGrid.append(createCard(popup));
                });
            } else {
                $('#wppoppop-passive-zone .wppoppop-zone-empty').show();
            }

            bindDragEvents();
        }

        function createCard(p) {
            return $('<div></div>')
                .addClass('wppoppop-target-card')
                .attr('draggable', 'true')
                .attr('data-id', p.id)
                .append('<span class="dashicons dashicons-menu wppoppop-target-handle"></span>')
                .append($('<span></span>').addClass('wppoppop-target-title').text(p.title + ' (#' + p.id + ')'));
        }

        function bindDragEvents() {
            $('.wppoppop-target-card').on('dragstart', function(e) {
                e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
                $(this).css('opacity', '0.5');
            }).on('dragend', function() {
                $(this).css('opacity', '1');
            });

            $('.wppoppop-target-dropzone').off('dragover dragleave drop').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('drag-over');
            }).on('dragleave', function() {
                $(this).removeClass('drag-over');
            }).on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');
                const id = parseInt(e.originalEvent.dataTransfer.getData('text/plain'), 10);
                const targetZone = $(this).data('zone');

                if (!id) return;

                const cur = matrix[currentEvent];
                cur.active = cur.active.filter(x => x !== id);
                cur.passive = cur.passive.filter(x => x !== id);

                if (targetZone === 'active') {
                    cur.active.push(id);
                } else {
                    cur.passive.push(id);
                }

                renderTargetGrid();
                saveMatrix();
            });
        }

        function saveMatrix() {
            const cur = matrix[currentEvent];
            $.post(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_save_targeting_matrix',
                nonce: window.WPPopPopAdmin.nonce,
                event: currentEvent,
                active: cur.active,
                passive: cur.passive
            });
        }

        $('#wppoppop-targeting-pills .wppoppop-pill-btn').on('click', function() {
            $('#wppoppop-targeting-pills .wppoppop-pill-btn').removeClass('active').find('.wppoppop-pill-dot').html('&#9675;');
            $(this).addClass('active').find('.wppoppop-pill-dot').html('&#9673;');
            currentEvent = $(this).data('event');
            renderTargetGrid();
        });

        // A/B Campaign Modal Handlers
        $('.wppoppop-open-campaign-modal').on('click', function(e) {
            e.preventDefault();
            $('#wppoppop-campaign-modal').fadeIn(150);
        });

        $('#wppoppop-campaign-modal-close, #wppoppop-campaign-cancel').on('click', function() {
            $('#wppoppop-campaign-modal').fadeOut(150);
        });

        $('#wppoppop-campaign-form').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#wppoppop-campaign-submit-btn');
            $btn.prop('disabled', true).text('Saving...');

            const selectedPopups = [];
            $(this).find('input[name="popups[]"]:checked').each(function() {
                selectedPopups.push($(this).val());
            });

            $.post(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_save_campaign',
                nonce: window.WPPopPopAdmin.nonce,
                title: $('#camp-title').val(),
                slug: $('#camp-slug').val(),
                popups: selectedPopups
            }, function(res) {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.data.message || 'Error saving campaign.');
                    $btn.prop('disabled', false).text('Save Campaign');
                }
            });
        });

        $('.wppoppop-btn-del-campaign').on('click', function() {
            if (!confirm('Are you sure you want to delete this campaign?')) {
                return;
            }
            const id = $(this).data('id');
            $.post(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_delete_campaign',
                nonce: window.WPPopPopAdmin.nonce,
                campaign_id: id
            }, function(res) {
                if (res.success) {
                    $('tr[data-campaign-id="' + id + '"]').fadeOut(200, function() { $(this).remove(); });
                } else {
                    alert('Error deleting campaign.');
                }
            });
        });

        // Settings Tab Switcher (General vs Advanced)
        $('#wppoppop-main-settings-tabs .wppoppop-tab-link').on('click', function(e) {
            e.preventDefault();
            $('#wppoppop-main-settings-tabs .wppoppop-tab-link').removeClass('active');
            $(this).addClass('active');

            const tab = $(this).data('tab');
            $('.wppoppop-settings-tab-pane').hide();
            $('#pane-' + tab).show();
        });

        // Master Settings Submission
        $('#wppoppop-master-settings-form').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#wppoppop-master-save-btn');
            $btn.prop('disabled', true).text('Saving...');

            $.post(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_save_all_settings',
                nonce: window.WPPopPopAdmin.nonce,
                settings: $(this).serializeArray().reduce(function(acc, cur) {
                    const match = cur.name.match(/settings\[(.*?)\]/);
                    if (match) acc[match[1]] = cur.value;
                    return acc;
                }, {})
            }, function(res) {
                $btn.prop('disabled', false).html('&#10003; Save Settings');
                alert(res.success ? (res.data.message || 'Settings saved!') : 'Error saving settings.');
            });
        });

        // Transactions Selection & Deletion
        $('#wppoppop-select-all-tx').on('change', function() {
            $('.wppoppop-tx-chk').prop('checked', this.checked);
        });

        $('#wppoppop-delete-tx-btn').on('click', function() {
            const selected = [];
            $('.wppoppop-tx-chk:checked').each(function() {
                selected.push($(this).val());
            });

            if (selected.length === 0) {
                alert('Please select at least one transaction to delete.');
                return;
            }

            if (!confirm('Are you sure you want to delete the selected transactions?')) {
                return;
            }

            $.post(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_bulk_delete_leads',
                nonce: window.WPPopPopAdmin.nonce,
                lead_ids: selected
            }, function(res) {
                if (res.success) {
                    selected.forEach(function(id) {
                        $('tr[data-tx-id="' + id + '"]').fadeOut(200, function() { $(this).remove(); });
                    });
                } else {
                    alert('Error deleting transactions.');
                }
            });
        });

        // Lead Detail Modal Inspector
        $('.wppoppop-open-lead-detail').on('click', function(e) {
            e.preventDefault();
            const leadId = $(this).data('lead-id');
            const $body = $('#wppoppop-lead-modal-body');
            $('#wppoppop-lead-modal').fadeIn(150);

            $body.html('<p style="color:#64748b;">Loading record #' + leadId + '...</p>');

            $.get(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_get_lead_details',
                nonce: window.WPPopPopAdmin.nonce,
                lead_id: leadId
            }, function(res) {
                if (res.success && res.data) {
                    const d = res.data;
                    let html = '<table class="wppoppop-form-table" style="font-size:13px;">';
                    html += '<tr><th style="width:140px;">Lead ID:</th><td>#' + d.id + '</td></tr>';
                    html += '<tr><th>Email:</th><td><strong>' + d.email + '</strong></td></tr>';
                    html += '<tr><th>Name:</th><td>' + (d.name || '—') + '</td></tr>';
                    html += '<tr><th>Source Popup:</th><td>' + d.popup_title + '</td></tr>';
                    html += '<tr><th>Status:</th><td><span class="wppoppop-badge wppoppop-badge-active">' + d.status + '</span></td></tr>';
                    html += '<tr><th>Amount:</th><td>$' + d.amount + '</td></tr>';
                    html += '<tr><th>Timestamp:</th><td>' + d.created + '</td></tr>';
                    html += '</table>';
                    $body.html(html);
                } else {
                    $body.html('<p style="color:#dc2626;">Error retrieving lead details.</p>');
                }
            });
        });

        $('#wppoppop-lead-modal-close, #wppoppop-lead-modal-dismiss').on('click', function() {
            $('#wppoppop-lead-modal').fadeOut(150);
        });

        function showAdminToast(msg) {
            const $t = $('#wppoppop-admin-toast');
            $t.text(msg).fadeIn(150);
            setTimeout(() => $t.fadeOut(200), 2200);
        }

        // Kebab Dropdown Toggle
        $(document).on('click', '.wppoppop-kebab-trigger', function(e) {
            e.stopPropagation();
            $('.wppoppop-dropdown-menu').not($(this).siblings('.wppoppop-dropdown-menu')).hide();
            $(this).siblings('.wppoppop-dropdown-menu').toggle();
        });

        $(document).on('click', function() {
            $('.wppoppop-dropdown-menu').hide();
        });

        // Copy Shortcode on Click
        $(document).on('click', '.wppoppop-icon-code', function() {
            const shortcode = $(this).attr('data-shortcode');
            if (navigator.clipboard && shortcode) {
                navigator.clipboard.writeText(shortcode).then(function() {
                    showAdminToast('Copied: ' + shortcode);
                });
            }
        });

        // Live Preview Modal
        $(document).on('click', '.wppoppop-icon-eye', function() {
            const popupId = $(this).data('id');
            $('#wppoppop-preview-content').html('<div style="color:#64748b;font-size:14px;">Loading sandbox preview...</div>');
            $('#wppoppop-preview-modal').fadeIn(150);

            $.get(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_get_preview',
                nonce: window.WPPopPopAdmin.nonce,
                popup_id: popupId
            }, function(res) {
                if (res.success && res.data) {
                    $('#wppoppop-preview-title').text('Preview: ' + res.data.title + ' (#' + popupId + ')');
                    $('#wppoppop-preview-content').html(res.data.html);
                } else {
                    $('#wppoppop-preview-content').html('<div style="color:#dc2626;">Error rendering preview.</div>');
                }
            });
        });

        $('#wppoppop-preview-modal-close').on('click', function() {
            $('#wppoppop-preview-modal').fadeOut(150);
        });

        // Toggle Status (ACTIVE / INACTIVE)
        $(document).on('click', '.wppoppop-action-toggle-status', function(e) {
            e.preventDefault();
            const popupId = $(this).data('id');
            const $row = $('tr[data-popup-id="' + popupId + '"]');

            $.post(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_toggle_status',
                nonce: window.WPPopPopAdmin.nonce,
                popup_id: popupId
            }, function(res) {
                if (res.success) {
                    $row.find('.wppoppop-badge')
                        .text(res.data.status)
                        .removeClass('wppoppop-badge-active wppoppop-badge-inactive')
                        .addClass(res.data.badge_class);
                    showAdminToast('Status changed to ' + res.data.status);
                }
            });
        });

        // Duplicate Popup
        $(document).on('click', '.wppoppop-action-duplicate', function(e) {
            e.preventDefault();
            const popupId = $(this).data('id');

            $.post(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_duplicate_popup',
                nonce: window.WPPopPopAdmin.nonce,
                popup_id: popupId
            }, function(res) {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.data.message || 'Error duplicating popup.');
                }
            });
        });

        // Delete Popup
        $(document).on('click', '.wppoppop-action-delete', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to trash this popup?')) {
                return;
            }
            const popupId = $(this).data('id');
            const $row = $('tr[data-popup-id="' + popupId + '"]');

            $.post(window.WPPopPopAdmin.ajax_url, {
                action: 'wppoppop_delete_popup',
                nonce: window.WPPopPopAdmin.nonce,
                popup_id: popupId
            }, function(res) {
                if (res.success) {
                    $row.fadeOut(200, function() { $(this).remove(); });
                    showAdminToast('Popup moved to trash');
                }
            });
        });

        // Import Popup Modal & JSON Upload
        $('#wppoppop-import-btn').on('click', function(e) {
            e.preventDefault();
            $('#wppoppop-import-modal').fadeIn(150);
        });

        $('#wppoppop-import-modal-close, #wppoppop-import-cancel').on('click', function() {
            $('#wppoppop-import-modal').fadeOut(150);
        });

        $('#wppoppop-import-form').on('submit', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('wppoppop-import-file');
            if (!fileInput.files.length) {
                alert('Please choose a .json popup configuration file.');
                return;
            }

            const $btn = $('#wppoppop-import-submit-btn');
            $btn.prop('disabled', true).text('Importing...');

            const formData = new FormData();
            formData.append('action', 'wppoppop_import_popup');
            formData.append('nonce', window.WPPopPopAdmin.nonce);
            formData.append('popup_file', fileInput.files[0]);

            $.ajax({
                url: window.WPPopPopAdmin.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success && res.data.redirect) {
                        window.location.href = res.data.redirect;
                    } else {
                        alert(res.data.message || 'Import failed.');
                        $btn.prop('disabled', false).text('Upload & Import');
                    }
                },
                error: function() {
                    alert('Network error during file upload.');
                    $btn.prop('disabled', false).text('Upload & Import');
                }
            });
        });

        if ($('#wppoppop-targeting-pills').length > 0) {
            renderTargetGrid();
        }
    });
})(jQuery);
