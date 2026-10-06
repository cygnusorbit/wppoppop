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
