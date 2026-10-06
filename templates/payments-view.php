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
