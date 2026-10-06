(function(window, $) {
    'use strict';
    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};

    var Modal = {
        init: function() {
            this.bindInspector();
            this.bindDismissal();
            this.bindPrintReceipt();
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
                $('#wppoppop-modal-email').text(email);
                $('#wppoppop-modal-meta').text(popup + ' • ' + date + ' • Country: ' + country);

                var $fieldsList = $('#wppoppop-modal-fields-list');
                $fieldsList.empty();

                var $utmList = $('#wppoppop-modal-utm-list');
                $utmList.empty();
                var hasUtm = false;
                var hasSig = false;

                $.each(fields, function(key, val) {
                    if (key === 'signature' && val) {
                        hasSig = true;
                        $('#wppoppop-modal-sig-img').attr('src', val);
                    } else if (key.indexOf('utm_') === 0) {
                        hasUtm = true;
                        $utmList.append('<span style="background:#e2e8f0;padding:2px 8px;border-radius:4px;"><strong>' + key + ':</strong> ' + val + '</span>');
                    } else {
                        var displayVal = Array.isArray(val) ? val.join(', ') : val;
                        $fieldsList.append('<div style="background:#f8fafc;padding:8px 12px;border-radius:6px;border:1px solid #e2e8f0;"><label style="display:block;font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;">' + key + '</label><div style="font-weight:600;font-size:13px;color:#1e293b;">' + displayVal + '</div></div>');
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

        bindPrintReceipt: function() {
            $('#wppoppop-sub-modal-print').on('click', function() {
                window.print();
            });
        }
    };

    window.WpPopPopSubmissions.Modal = Modal;
})(window, jQuery);
