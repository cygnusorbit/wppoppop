<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wppoppop-submissions-wrap" style="max-width:100%;">
    <?php include WPPOPPOP_PATH . 'templates/submissions/context.php'; ?>
    <!-- Top Action & Search Header -->
    <?php include WPPOPPOP_PATH . 'templates/submissions/header.php'; ?>

    <!-- Submissions List Table -->
    <?php include WPPOPPOP_PATH . 'templates/submissions/table.php'; ?>

    <?php include WPPOPPOP_PATH . 'templates/submissions/modal-table-options.php'; ?>
    <!-- Lead Submission Detail & Print Receipt Modal -->
    <?php include WPPOPPOP_PATH . 'templates/submissions/modal-editor.php'; ?>
</div>
