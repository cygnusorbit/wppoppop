<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$table_name = $wpdb->prefix . 'wppoppop_items';
$items = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");
?>
<div class="wrap wppoppop-admin-page">
    <h1 class="wp-heading-inline">WpPopPop Campaigns</h1>
    <a href="<?php echo admin_url('admin.php?page=wppoppop-builder'); ?>" class="page-title-action">Create Popup</a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 240px;">Title</th>
                <th>UID / Identifier</th>
                <th>Shortcode</th>
                <th style="width: 100px;">Impressions</th>
                <th style="width: 100px;">Submissions</th>
                <th style="width: 220px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)) : ?>
                <tr><td colspan="6">No popups created yet. Launch the builder to create your first popup.</td></tr>
            <?php else : ?>
                <?php foreach ($items as $item) : ?>
                    <tr data-uid="<?php echo esc_attr($item->uid); ?>">
                        <td><strong><a href="<?php echo admin_url('admin.php?page=wppoppop-builder&uid=' . esc_attr($item->uid)); ?>"><?php echo esc_html($item->title); ?></a></strong></td>
                        <td><code><?php echo esc_html($item->uid); ?></code></td>
                        <td><code>[wppoppop uid="<?php echo esc_attr($item->uid); ?>"]</code></td>
                        <td><?php echo number_format($item->impressions); ?></td>
                        <td><?php echo number_format($item->submissions); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=wppoppop-builder&uid=' . esc_attr($item->uid)); ?>" class="button button-small">Edit</a>
                            <button type="button" class="button button-small btn-duplicate-popup" data-uid="<?php echo esc_attr($item->uid); ?>">Duplicate</button>
                            <button type="button" class="button button-small btn-export-popup" data-uid="<?php echo esc_attr($item->uid); ?>">Export</button>
                            <button type="button" class="button button-small button-link-delete btn-delete-popup" data-uid="<?php echo esc_attr($item->uid); ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('.btn-duplicate-popup').on('click', function() {
        const uid = $(this).data('uid');
        $.post(wppoppop_vars.ajax_url, {
            action: 'wppoppop_duplicate_popup',
            nonce: wppoppop_vars.nonce,
            uid: uid
        }, function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.data.message);
            }
        });
    });

    $('.btn-delete-popup').on('click', function() {
        if (!confirm('Are you sure you want to delete this popup?')) return;
        const uid = $(this).data('uid');
        $.post(wppoppop_vars.ajax_url, {
            action: 'wppoppop_delete_popup',
            nonce: wppoppop_vars.nonce,
            uid: uid
        }, function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.data.message);
            }
        });
    });

    $('.btn-export-popup').on('click', function() {
        const uid = $(this).data('uid');
        $.get(wppoppop_vars.ajax_url, {
            action: 'wppoppop_export_popup',
            nonce: wppoppop_vars.nonce,
            uid: uid
        }, function(res) {
            if (res.success) {
                const blob = new Blob([res.data.payload], {type: 'application/json'});
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = res.data.filename;
                link.click();
            } else {
                alert(res.data.message);
            }
        });
    });
});
</script>
