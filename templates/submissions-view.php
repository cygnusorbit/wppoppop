<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wppoppop-dashboard-wrap wppoppop-submissions-wrap" style="width:100%;padding-right:30px;max-width:none;box-sizing:border-box;">
    <?php include WPPOPPOP_PATH . 'templates/submissions/context.php'; ?>
    <!-- Top Action & Search Header -->
    <?php include WPPOPPOP_PATH . 'templates/submissions/header.php'; ?>

    <!-- Submissions List Table -->
    <?php include WPPOPPOP_PATH . 'templates/submissions/table.php'; ?>

    <?php include WPPOPPOP_PATH . 'templates/submissions/modal-table-options.php'; ?>
    <?php include WPPOPPOP_PATH . 'templates/submissions/modal-export.php'; ?>
    <!-- Lead Submission Detail & Print Receipt Modal -->
    <?php include WPPOPPOP_PATH . 'templates/submissions/modal-editor.php'; ?>
</div>
