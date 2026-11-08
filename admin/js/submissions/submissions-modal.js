(function(window, $) {
    'use strict';
    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};

    function escHtml(v) { return $('<div>').text(v == null ? '' : String(v)).html(); }

    var Modal = {
        init: function() {
            this.bindInspector();
            this.bindDismissal();
            this.bindResend();
        },

        bindInspector: function() {
            $('.wppoppop-view-sub-btn').on('click', function() {
                var $btn = $(this);
                var id = $btn.data('id');
                var email = $btn.data('email');
                var popup = $btn.data('popup');
                var date = $btn.data('date');
                var country = $btn.data('country');
                var fields = $btn.data('fields') || {};

                $('#wppoppop-modal-id').text('#' + id);
                $('#wppoppop-submission-modal').attr('data-sub-id', id);
                $('#wppoppop-sub-resend-msg').text('').css('color', '#64748b');
                $('#wppoppop-sub-resend-target').val('all');
                (function() {
                    var v = window.wppoppop_vars || {};
                    var $readRow = $btn.closest('tr.wppoppop-sub-row');
                    if (v.ajax_url && id && $readRow.hasClass('is-unread')) {
                        $.post(v.ajax_url, { action: 'wppoppop_mark_submission_read', nonce: v.nonce, id: id }).done(function(res) {
                            if (res && res.success) {
                                $readRow.removeClass('is-unread');
                                $(document).trigger('wppoppop:subs-read-changed');
                            }
                        });
                    }
                })();
                $('#wppoppop-modal-email').text(email);
                $('#wppoppop-modal-meta').text(popup + ' • ' + date + ' • Country: ' + country);

                var $fieldsList = $('#wppoppop-modal-fields-list');
                $fieldsList.empty();

                var $utmList = $('#wppoppop-modal-utm-list');
                $utmList.empty();
                var hasUtm = false;
                var hasSig = false;

                $.each(fields, function(key, val) {
                    if (key === 'signature' && val && String(val).indexOf('data:image/') === 0) {
                        hasSig = true;
                        $('#wppoppop-modal-sig-img').attr('src', val);
                    } else if (key.indexOf('utm_') === 0) {
                        hasUtm = true;
                        $utmList.append('<span style="background:#e2e8f0;padding:2px 8px;border-radius:0px;"><strong>' + key + ':</strong> ' + val + '</span>');
                    } else {
                        var displayVal = Array.isArray(val) ? val.join(', ') : val;
                        if (key.charAt(0) === '_') { key = key.substring(1); }
                        $fieldsList.append('<div style="background:#f8fafc;padding:8px 12px;border-radius:0px;border:1px solid #e2e8f0;"><label style="display:block;font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;">' + escHtml(key) + '</label><div style="font-weight:600;font-size:13px;color:#1e293b;">' + escHtml(displayVal) + '</div></div>');
                    }
                });

                $('#wppoppop-modal-sig-wrap').toggle(hasSig);
                $('#wppoppop-modal-utm-wrap').toggle(hasUtm);

                $('#wppoppop-submission-modal').css('display', 'flex');
            });
        },

        bindDismissal: function() {
            $('#wppoppop-sub-modal-close, #wppoppop-sub-modal-done').on('click', function() {
                $('#wppoppop-submission-modal').hide();
            });

            $('#wppoppop-submission-modal').on('click', function(e) {
                if (e.target === this) {
                    $(this).hide();
                }
            });
        },

        bindResend: function() {
            $('#wppoppop-sub-modal-resend').on('click', function() {
                var v = window.wppoppop_vars || {};
                var $btn = $(this), $msg = $('#wppoppop-sub-resend-msg');
                var id = $('#wppoppop-submission-modal').attr('data-sub-id');
                if (!v.ajax_url || !id || $btn.prop('disabled')) { return; }
                $btn.prop('disabled', true);
                $msg.css('color', '#64748b').text('Sending...');
                $.post(v.ajax_url, {
                    action: 'wppoppop_resend_notification',
                    nonce: v.nonce,
                    id: id,
                    target: $('#wppoppop-sub-resend-target').val()
                }).done(function(res) {
                    var ok = !!(res && res.success);
                    $msg.css('color', ok ? '#15803d' : '#b91c1c').text((res && res.data && res.data.message) || (ok ? 'Sent.' : 'Failed.'));
                }).fail(function(xhr) {
                    var m = xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message;
                    $msg.css('color', '#b91c1c').text(m || 'Request failed.');
                }).always(function() {
                    $btn.prop('disabled', false);
                });
            });
        }
    };

    window.WpPopPopSubmissions.Modal = Modal;
})(window, jQuery);
