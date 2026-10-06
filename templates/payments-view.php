<?php
if (!defined('ABSPATH')) {
    exit;
}

// 1. Calculate Financial Metrics
$total_revenue   = 0.0;
$completed_count = 0;
$stripe_count    = 0;
$paypal_count    = 0;

foreach ($transactions as $t) {
    if ($t->status === 'completed') {
        $total_revenue += floatval($t->amount);
        $completed_count++;
    }
    if (strtolower($t->gateway) === 'stripe') {
        $stripe_count++;
    } elseif (strtolower($t->gateway) === 'paypal') {
        $paypal_count++;
    }
}

$avg_order_val = $completed_count > 0 ? ($total_revenue / $completed_count) : 0.0;
?>
<div class="wrap wppoppop-payments-wrap" style="max-width:1200px;">
    <!-- Top Action & Search Header -->
    <?php include WPPOPPOP_PATH . 'templates/payments/header.php'; ?>

    <!-- KPI Totals Summary Cards -->
    <?php include WPPOPPOP_PATH . 'templates/payments/kpi-summary.php'; ?>

    <!-- Transactions List Table -->
    <?php include WPPOPPOP_PATH . 'templates/payments/table.php'; ?>

    <!-- Transaction Receipt Modal Dialog -->
    <?php include WPPOPPOP_PATH . 'templates/payments/modal-receipt.php'; ?>
</div>

<script>
(function($) {
    'use strict';
    $(document).ready(function() {
        // 1. Live Keyword Search
        $('#wppoppop-payments-search').on('input', function() {
            var term = $(this).val().toLowerCase();
            $('.wppoppop-tx-row').each(function() {
                var email = $(this).data('email') || '';
                var txid  = $(this).data('txid') || '';
                if (email.indexOf(term) !== -1 || txid.indexOf(term) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // 2. Receipt Modal Inspector
        $('.wppoppop-view-receipt-btn').on('click', function() {
            var $btn = $(this);
            $('#wppoppop-rcpt-txid').text($btn.data('txid'));
            $('#wppoppop-rcpt-email').text($btn.data('email'));
            $('#wppoppop-rcpt-popup').text($btn.data('popup'));
            $('#wppoppop-rcpt-date').text($btn.data('date'));
            $('#wppoppop-rcpt-gateway').text($btn.data('gateway'));
            $('#wppoppop-rcpt-status').text($btn.data('status'));
            var formatted = '$' + $btn.data('amount') + ' ' + $btn.data('currency');
            $('#wppoppop-rcpt-amount').text(formatted);
            $('#wppoppop-rcpt-total').text(formatted);

            $('#wppoppop-receipt-modal').css('display', 'flex');
        });

        // 3. Modal Dismissal
        $('#wppoppop-receipt-close, #wppoppop-rcpt-done').on('click', function() {
            $('#wppoppop-receipt-modal').hide();
        });

        // 4. Print Receipt
        $('#wppoppop-rcpt-print').on('click', function() {
            window.print();
        });

        // 5. Export Sales CSV
        $('#wppoppop-btn-export-sales-csv').on('click', function() {
            var csv = ['Transaction ID,Customer Email,Popup Campaign,Amount,Currency,Gateway,Status,Date'];
            $('.wppoppop-tx-row:visible').each(function() {
                var cols = [
                    $(this).find('td:nth-child(2)').text().trim(),
                    $(this).find('td:nth-child(3)').text().trim(),
                    $(this).find('td:nth-child(4) strong').text().trim(),
                    $(this).find('td:nth-child(5)').text().replace('$', '').trim(),
                    'USD',
                    $(this).find('td:nth-child(6)').text().trim(),
                    $(this).find('td:nth-child(7)').text().trim(),
                    $(this).find('td:nth-child(8)').text().trim()
                ];
                csv.push('"' + cols.join('","') + '"');
            });

            var blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', 'wppoppop-sales-export.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
})(jQuery);
</script>
