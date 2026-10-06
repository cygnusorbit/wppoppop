<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wppoppop-submissions-wrap" style="max-width:1200px;">
    <!-- Top Action & Search Header -->
    <?php include WPPOPPOP_PATH . 'templates/submissions/header.php'; ?>

    <!-- Submissions List Table -->
    <?php include WPPOPPOP_PATH . 'templates/submissions/table.php'; ?>

    <!-- Lead Submission Detail & Print Receipt Modal -->
    <?php include WPPOPPOP_PATH . 'templates/submissions/modal-editor.php'; ?>
</div>

<script>
(function($) {
    'use strict';
    $(document).ready(function() {
        var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) || '';
        var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) || ajaxurl;

        // 1. Live Keyword Search
        $('#wppoppop-subs-search').on('input', function() {
            var term = $(this).val().toLowerCase();
            $('.wppoppop-sub-row').each(function() {
                var email = $(this).data('email') || '';
                var title = $(this).data('title') || '';
                if (email.indexOf(term) !== -1 || title.indexOf(term) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // 2. CSV Export Trigger
        $('#wppoppop-btn-export-csv').on('click', function() {
            window.location.href = ajaxUrl + '?action=wppoppop_export_submissions_csv&nonce=' + nonce;
        });

        // 3. View/Edit Modal Inspector
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

        // 4. Modal Dismissal
        $('#wppoppop-sub-modal-close, #wppoppop-sub-modal-done').on('click', function() {
            $('#wppoppop-submission-modal').hide();
        });

        // 5. Print Receipt Action
        $('#wppoppop-sub-modal-print').on('click', function() {
            window.print();
        });

        // 6. GDPR Anonymize Action
        $('.wppoppop-anon-sub-btn').on('click', function() {
            if (!confirm('Anonymize this lead record for GDPR compliance?')) return;
            var id = $(this).data('id');
            var $row = $(this).closest('tr');
            $.post(ajaxUrl, { action: 'wppoppop_anonymize_submission', nonce: nonce, id: id }, function(res) {
                if (res.success) {
                    $row.find('td:nth-child(2) strong').text('anonymized_' + id + '@privacy.local');
                    alert('Lead PII scrubbed successfully.');
                }
            });
        });

        // 7. Delete Submission Action
        $('.wppoppop-del-sub-btn').on('click', function() {
            if (!confirm('Permanently delete this submission record?')) return;
            var id = $(this).data('id');
            var $row = $(this).closest('tr');
            $.post(ajaxUrl, { action: 'wppoppop_delete_submission', nonce: nonce, id: id }, function(res) {
                if (res.success) {
                    $row.fadeOut(200, function() { $(this).remove(); });
                }
            });
        });
    });
})(jQuery);
</script>
