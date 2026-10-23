(function(window, $) {
    'use strict';
    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};

    var Actions = {
        init: function() {
            this.bindExportCsv();
            this.bindAnonymize();
            this.bindDelete();
            this.bindReadToggle();
        },

        getVars: function() {
            return window.wppoppop_vars || {
                ajax_url: ajaxurl || '',
                nonce: ''
            };
        },

        bindExportCsv: function() {
            var self = this;
            $('#wppoppop-btn-export-csv').on('click', function() {
                var vars = self.getVars();
                var EM = window.WpPopPopSubmissions && window.WpPopPopSubmissions.ExportModal;
                if (EM && EM.open()) { return; }
                window.location.href = vars.ajax_url + '?action=wppoppop_export_submissions_csv&nonce=' + vars.nonce;
            });
        },

        bindAnonymize: function() {
            var self = this;
            $('.wppoppop-anon-sub-btn').on('click', function() {
                if (!confirm('Anonymize this lead record for GDPR compliance?')) {
                    return;
                }

                var id = $(this).data('id');
                var $row = $(this).closest('tr');
                var vars = self.getVars();

                $.post(vars.ajax_url, {
                    action: 'wppoppop_anonymize_submission',
                    nonce: vars.nonce,
                    id: id
                }).done(function(res) {
                    if (res.success) {
                        $row.find('td:nth-child(2) strong').text('anonymized_' + id + '@privacy.local');
                        alert(res.data.message || 'Lead PII scrubbed successfully.');
                    } else {
                        alert('Anonymization Error: ' + (res.data ? res.data.message : 'Action failed.'));
                    }
                }).fail(function() {
                    alert('Network error while anonymizing lead.');
                });
            });
        },

        bindReadToggle: function() {
            var self = this;
            $(document).off('click.wppSubRead').on('click.wppSubRead', '.wppoppop-mark-unread-btn, .wppoppop-mark-read-btn', function(e) {
                e.preventDefault();
                var $a = $(this);
                var makeRead = $a.hasClass('wppoppop-mark-read-btn');
                var id = $a.data('id');
                var $row = $a.closest('tr');
                var vars = self.getVars();
                if ($a.data('busy')) { return; }
                $a.data('busy', true);

                $.post(vars.ajax_url, {
                    action: makeRead ? 'wppoppop_mark_submission_read' : 'wppoppop_mark_submission_unread',
                    nonce: vars.nonce,
                    id: id
                }).done(function(res) {
                    if (res && res.success) {
                        $row.toggleClass('is-unread', !makeRead);
                        var L = window.WpPopPopSubmissions && window.WpPopPopSubmissions.List;
                        if (L && typeof L.render === 'function') { L.render(); }
                    } else {
                        alert((res && res.data && res.data.message) || 'Could not update read state.');
                    }
                }).fail(function() {
                    alert('Network error while updating read state.');
                }).always(function() { $a.data('busy', false); });
            });
        },

        bindDelete: function() {
            var self = this;
            $('.wppoppop-del-sub-btn').on('click', function() {
                if (!confirm('Permanently delete this submission record?')) {
                    return;
                }

                var id = $(this).data('id');
                var $row = $(this).closest('tr');
                var vars = self.getVars();

                $.post(vars.ajax_url, {
                    action: 'wppoppop_delete_submission',
                    nonce: vars.nonce,
                    id: id
                }).done(function(res) {
                    if (res.success) {
                        $row.fadeOut(200, function() { $(this).remove(); });
                    } else {
                        alert('Deletion Error: ' + (res.data ? res.data.message : 'Action failed.'));
                    }
                }).fail(function() {
                    alert('Network error while deleting submission.');
                });
            });
        }
    };

    window.WpPopPopSubmissions.Actions = Actions;
})(window, jQuery);


/* === WPPOPPOP_EXPORT_MODAL_START: choose CSV columns for the currently filtered popup === */
(function(window, $) {
    'use strict';
    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};

    var uid = '';

    function vars() { return window.wppoppop_vars || { ajax_url: window.ajaxurl || '', nonce: '' }; }
    function key() { return 'wppoppop_export_cols_' + (uid || '_all'); }
    function esc(t) { return $('<div>').text(t == null ? '' : String(t)).html(); }
    function saved() {
        try { var r = window.localStorage.getItem(key()); return r ? JSON.parse(r) : null; } catch (e) { return null; }
    }
    function store(ids) { try { window.localStorage.setItem(key(), JSON.stringify(ids)); } catch (e) {} }

    function render(cols) {
        var sel = saved();
        var html = '', group = '';
        $.each(cols, function(_, c) {
            if (c.group !== group) {
                group = c.group;
                html += '<div class="wex-group">' + (group === 'field' ? 'Form fields' : 'Standard columns') + '</div>';
            }
            var on = sel ? ($.inArray(c.id, sel) !== -1) : (c.group !== 'core'); /* WPPOPPOP_EXPORT_STD_OFF: Standard columns unticked by default */
            html += '<label class="wex-item"><input type="checkbox" class="wex-col" value="' + esc(c.id) + '"' + (on ? ' checked' : '') + '>' + esc(c.label) + '</label>';
        });
        $('#wex-body').html(html || '<div class="wex-msg">No columns available.</div>');
    }

    var Modal = {
        open: function() {
            var $m = $('#wppoppop-export-modal');
            if (!$m.length) { return false; }
            /* WPPOPPOP_EXPORT_URL_UID: the popup comes from ?popup_uid= in the page URL (dropdown is only a fallback) */
            var $f = $('#wppoppop-subs-popup-filter');
            var um = /[?&]popup_uid=([^&#]*)/.exec(window.location.search || '');
            uid = '';
            if (um && um[1]) {
                try { uid = decodeURIComponent(um[1].replace(/\+/g, ' ')); } catch (e) { uid = um[1]; }
            } else if ($f.length) {
                uid = $f.val() || '';
            }
            uid = $.trim(uid);
            var uname = '';
            if (uid) {
                $f.find('option').each(function() { if (this.value === uid) { uname = $.trim($(this).text()); return false; } });
            }
            $('#wex-sub').text(uid ? ('Popup: ' + (uname || uid) + (uname && uname !== uid ? '  (' + uid + ')' : '')) : 'All popups');
            $('#wex-body').html('<div class="wex-msg">Loading columns...</div>');
            $m.addClass('is-open');
            var v = vars();
            $.post(v.ajax_url, { action: 'wppoppop_export_columns', nonce: v.nonce, popup_uid: uid }).done(function(res) {
                if (res && res.success && res.data && res.data.columns) { render(res.data.columns); }
                else { $('#wex-body').html('<div class="wex-msg">Could not load columns.</div>'); }
            }).fail(function() {
                $('#wex-body').html('<div class="wex-msg">Network error while loading columns.</div>');
            });
            return true;
        },
        close: function() { $('#wppoppop-export-modal').removeClass('is-open'); },
        go: function() {
            var ids = [];
            $('#wex-body .wex-col:checked').each(function() { ids.push($(this).val()); });
            if (!ids.length) { window.alert('Select at least one column.'); return; }
            store(ids);
            var v = vars();
            window.location.href = v.ajax_url + '?action=wppoppop_export_submissions&nonce=' + encodeURIComponent(v.nonce)
                + '&popup_uid=' + encodeURIComponent(uid) + '&columns=' + encodeURIComponent(JSON.stringify(ids));
            Modal.close();
        }
    };

    $(document)
        .off('click.wppExport')
        .on('click.wppExport', '#wex-cancel', function() { Modal.close(); })
        .on('click.wppExport', '#wex-go', function() { Modal.go(); })
        .on('click.wppExport', '#wex-all', function() { $('#wex-body .wex-col').prop('checked', true); })
        .on('click.wppExport', '#wex-none', function() { $('#wex-body .wex-col').prop('checked', false); })
        .on('mousedown.wppExport', '#wppoppop-export-modal', function(e) { if (e.target === this) { Modal.close(); } })
        .on('keydown.wppExport', function(e) { if (e.which === 27 && $('#wppoppop-export-modal').hasClass('is-open')) { Modal.close(); } });

    window.WpPopPopSubmissions.ExportModal = Modal;
})(window, jQuery);
/* === WPPOPPOP_EXPORT_MODAL_END === */
