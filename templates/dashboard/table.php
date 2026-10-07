<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
require_once WPPOPPOP_PATH . 'includes/admin/class-wppoppop-list-table.php';

$list_table = new WpPopPop_List_Table();
$list_table->prepare_items(isset($popups) ? $popups : null);
?>
<div class="wppoppop-table-wrap" style="width:100%;margin-top:16px;">
    <?php wp_nonce_field('wppoppop_admin_nonce', 'wppoppop_admin_nonce_field'); ?>
    <script>
        window.wppoppop_vars = window.wppoppop_vars || {};
        window.wppoppop_vars.ajax_url = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
        window.wppoppop_vars.nonce    = <?php echo wp_json_encode(wp_create_nonce('wppoppop_admin_nonce')); ?>;
    </script>

    <form id="wppoppop-campaigns-table-form" method="get">
        <input type="hidden" name="page" value="wppoppop" />
        <?php
        $list_table->views();
        $list_table->search_box(__('Search Campaigns', 'wppoppop'), 'wppoppop-search-input');
        $list_table->display();
        ?>
    </form>

    <!-- WordPress Standard Quick Edit Template (Aligned to 7 Core Columns) -->
    <table style="display:none;">
        <tbody id="wppoppop-quick-edit-template-root">
            <tr id="wppoppop-inline-edit" class="inline-edit-row inline-edit-row-post quick-edit-row quick-edit-row-post">
                <td colspan="7" class="colspanchange">
                    <fieldset class="inline-edit-col-left">
                        <legend class="inline-edit-legend"><?php esc_html_e('Quick Edit', 'wppoppop'); ?></legend>
                        <div class="inline-edit-col">
                            <label>
                                <span class="title"><?php esc_html_e('Title', 'wppoppop'); ?></span>
                                <span class="input-text-wrap">
                                    <input type="text" name="popup_title" class="ptitle wppoppop-qe-input-title" value="">
                                </span>
                            </label>
                            <label>
                                <span class="title"><?php esc_html_e('UID', 'wppoppop'); ?></span>
                                <span class="input-text-wrap">
                                    <input type="text" name="popup_uid" class="puid wppoppop-qe-input-uid" value="" readonly style="background:#f0f0f1;color:#646970;font-family:monospace;">
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="inline-edit-col-right">
                        <legend class="inline-edit-legend"><?php esc_html_e('Status', 'wppoppop'); ?></legend>
                        <div class="inline-edit-col">
                            <label class="inline-edit-status">
                                <span class="title"><?php esc_html_e('Status', 'wppoppop'); ?></span>
                                <select name="popup_status" class="wppoppop-qe-input-status">
                                    <option value="publish"><?php esc_html_e('Published', 'wppoppop'); ?></option>
                                    <option value="draft"><?php esc_html_e('Draft', 'wppoppop'); ?></option>
                                </select>
                            </label>
                        </div>
                    </fieldset>

                    <div class="submit inline-edit-save">
                        <button type="button" class="button cancel alignleft wppoppop-qe-btn-cancel"><?php esc_html_e('Cancel', 'wppoppop'); ?></button>
                        <button type="button" class="button button-primary save alignright wppoppop-qe-btn-save"><?php esc_html_e('Update', 'wppoppop'); ?></button>
                        <span class="spinner wppoppop-qe-spinner alignright" style="float:right;margin-top:4px;"></span>
                        <div class="notice notice-error notice-alt wppoppop-qe-error-notice" style="display:none;clear:both;margin-top:8px;">
                            <p class="wppoppop-qe-error-message" style="margin:0;"></p>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
