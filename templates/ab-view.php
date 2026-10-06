<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wppoppop-ab-wrap" style="max-width:1200px;">
    <!-- Top Action & Notice Header -->
    <?php include WPPOPPOP_PATH . 'templates/ab/header.php'; ?>

    <!-- Experiments List Table -->
    <?php include WPPOPPOP_PATH . 'templates/ab/table.php'; ?>

    <!-- Create Experiment Modal Dialog -->
    <?php include WPPOPPOP_PATH . 'templates/ab/modal-create.php'; ?>
</div>

<script>
(function($) {
    'use strict';
    $(document).ready(function() {
        var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) || '';
        var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) || ajaxurl;

        // 1. Open/Close Modal
        $('#wppoppop-btn-open-create-ab').on('click', function() {
            $('#wppoppop-ab-title-input').val('');
            $('.wppoppop-ab-popup-checkbox').prop('checked', false);
            $('#wppoppop-create-ab-modal').css('display', 'flex');
        });

        $('#wppoppop-create-ab-close, #wppoppop-create-ab-cancel').on('click', function() {
            $('#wppoppop-create-ab-modal').hide();
        });

        // 2. Submit Experiment
        $('#wppoppop-create-ab-submit').on('click', function() {
            var title = $('#wppoppop-ab-title-input').val().trim();
            var selectedUids = [];
            $('.wppoppop-ab-popup-checkbox:checked').each(function() {
                selectedUids.push($(this).val());
            });

            if (!title) {
                alert('Please enter a campaign title.');
                return;
            }

            if (selectedUids.length < 2) {
                alert('Please select at least 2 popups for split-testing.');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).text('Launching...');

            $.post(ajaxUrl, {
                action: 'wppoppop_save_campaign',
                nonce: nonce,
                title: title,
                popup_uids: selectedUids
            }).done(function(res) {
                if (res.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (res.data ? res.data.message : 'Unable to create campaign.'));
                    $btn.prop('disabled', false).text('Launch Campaign');
                }
            }).fail(function() {
                alert('Network error while saving campaign.');
                $btn.prop('disabled', false).text('Launch Campaign');
            });
        });

        // 3. Copy Shortcode Chip
        $('.wppoppop-copy-ab-sc-btn').on('click', function() {
            var sc = $(this).data('shortcode');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(sc);
            }
            var orig = $(this).text();
            $(this).text('Copied!');
            var $self = $(this);
            setTimeout(function() { $self.text(orig); }, 1500);
        });

        // 4. Delete Campaign Action
        $('.wppoppop-del-ab-btn').on('click', function() {
            if (!confirm('Are you sure you want to delete this A/B testing campaign?')) return;
            var uid = $(this).data('uid');
            var $row = $(this).closest('tr');

            $.post(ajaxUrl, {
                action: 'wppoppop_delete_campaign',
                nonce: nonce,
                uid: uid
            }).done(function(res) {
                if (res.success) {
                    $row.fadeOut(200, function() { $(this).remove(); });
                }
            });
        });
    });
})(jQuery);
</script>
