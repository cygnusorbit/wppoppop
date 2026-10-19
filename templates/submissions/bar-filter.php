<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="cell subs-filter">
    <select id="wppoppop-subs-popup-filter" aria-label="<?php echo esc_attr__('Filter by popup', 'wppoppop'); ?>" data-base="<?php echo esc_url(admin_url('admin.php?page=wppoppop-submissions')); ?>">
        <option value=""><?php echo esc_html__('All popups', 'wppoppop'); ?></option>
        <?php foreach ((array) $all_popups as $__p) : ?>
            <option value="<?php echo esc_attr($__p->uid); ?>" <?php selected($sel_uid, $__p->uid); ?>><?php echo esc_html($__p->title ? $__p->title : $__p->uid); ?></option>
        <?php endforeach; ?>
    </select>
</div>
