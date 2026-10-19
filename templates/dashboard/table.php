<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
require_once WPPOPPOP_PATH . 'includes/admin/class-wppoppop-list-table.php';

$list_table = new WpPopPop_List_Table();
$list_table->prepare_items();
?>
<style id="wppoppop-screenshot-table">
.wppoppop-table-wrap table.wp-list-table { table-layout: fixed; width: 100%; border: 1px solid #c3c4c7; }
.wppoppop-table-wrap table.wp-list-table .check-column { width: 2.2em; }
.wppoppop-table-wrap table.wp-list-table th.column-title,
.wppoppop-table-wrap table.wp-list-table td.column-title { width: 26%; }
.wppoppop-table-wrap table.wp-list-table th.column-shortcode,
.wppoppop-table-wrap table.wp-list-table td.column-shortcode { width: 30%; }
.wppoppop-table-wrap table.wp-list-table th.column-entries,
.wppoppop-table-wrap table.wp-list-table td.column-entries { width: 16%; }
.wppoppop-table-wrap table.wp-list-table th.column-active,
.wppoppop-table-wrap table.wp-list-table td.column-active { width: 9%; }
.wppoppop-table-wrap table.wp-list-table th.column-modified,
.wppoppop-table-wrap table.wp-list-table td.column-modified { width: 15%; }
.wppoppop-table-wrap table.wp-list-table td { vertical-align: middle; }
.wppoppop-table-wrap table.wp-list-table td.column-title a.row-title { font-size: 14px; font-weight: 600; }
.wppoppop-table-wrap table.wp-list-table td.column-shortcode code.wppoppop-shortcode-chip {
    display: block; max-width: 100%; box-sizing: border-box; padding: 8px 12px; margin: 0;
    background: #e9e9e9; border: 0; border-radius: 4px; color: #50575e;
    font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 11px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer; user-select: all;
    -webkit-user-select: all;
}
.wppoppop-table-wrap table.wp-list-table td.column-entries a { text-decoration: none; }
.wppoppop-table-wrap .row-actions { left: -9999em; position: relative; }
.wppoppop-table-wrap tr:hover .row-actions,
.wppoppop-table-wrap tr:focus-within .row-actions { left: 0; }

/* Status filter buttons */
.wppoppop-table-wrap ul.subsubsub {
    float: none; display: flex; flex-wrap: wrap; gap: 8px;
    margin: 16px 0 18px; padding: 0; font-size: 0; line-height: 1; white-space: normal;
}
.wppoppop-table-wrap ul.subsubsub li { display: block; margin: 0; padding: 0; font-size: 0; }
.wppoppop-table-wrap a.wppoppop-view-btn {
    position: relative; display: inline-block; padding: 11px 16px; font-size: 14px; line-height: 1;
    color: #3c434a; text-decoration: none; background: #f6f7f7;
    border: 1px solid #dcdcde; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,.08);
}
.wppoppop-table-wrap a.wppoppop-view-btn:hover { background: #fff; border-color: #c3c4c7; color: #1d2327; }
.wppoppop-table-wrap a.wppoppop-view-btn .count { color: #d63638; font-size: 12px; font-weight: 400; }
.wppoppop-table-wrap a.wppoppop-view-btn.current {
    background: #c0281c; border-color: #a62016; color: #fff; font-weight: 700;
}
.wppoppop-table-wrap a.wppoppop-view-btn.current .count { color: #fff; font-weight: 700; }
.wppoppop-table-wrap a.wppoppop-view-btn.current::after {
    content: ""; position: absolute; left: 14px; bottom: -9px; width: 0; height: 0;
    border-left: 9px solid transparent; border-right: 9px solid transparent; border-top: 9px solid #c0281c;
}
.wppoppop-table-wrap .tablenav.top { clear: both; }
</style>
<div class="wppoppop-table-wrap" style="width:100%;margin-top:16px;">
    <?php if (!empty($_GET['trashed'])) : 
        $trashed_count = intval($_GET['trashed']);
        $trashed_uid   = !empty($_GET['trashed_uid']) ? sanitize_text_field($_GET['trashed_uid']) : '';
        $undo_url      = '';
        if ($trashed_count === 1 && $trashed_uid) {
            $undo_url = wp_nonce_url(
                admin_url('admin.php?page=wppoppop&action=untrash&uid=' . rawurlencode($trashed_uid)),
                'wppoppop_untrash_' . $trashed_uid
            );
        }
    ?>
        <div class="notice notice-success is-dismissible" style="margin: 12px 0 16px 0;">
            <p>
                <?php 
                printf(
                    _n('%d campaign moved to the Trash.', '%d campaigns moved to the Trash.', $trashed_count, 'wppoppop'),
                    number_format_i18n($trashed_count)
                ); 
                if ($undo_url) : ?>
                    <a href="<?php echo esc_url($undo_url); ?>"><?php esc_html_e('Undo', 'wppoppop'); ?></a>
                <?php endif; ?>
            </p>
        </div>
    <?php elseif (!empty($_GET['untrashed'])) : 
        $untrashed_count = intval($_GET['untrashed']);
    ?>
        <div class="notice notice-success is-dismissible" style="margin: 12px 0 16px 0;">
            <p>
                <?php 
                printf(
                    _n('%d campaign restored from the Trash.', '%d campaigns restored from the Trash.', $untrashed_count, 'wppoppop'),
                    number_format_i18n($untrashed_count)
                ); 
                ?>
            </p>
        </div>
    <?php elseif (!empty($_GET['deleted'])) : 
        $deleted_count = intval($_GET['deleted']);
    ?>
        <div class="notice notice-success is-dismissible" style="margin: 12px 0 16px 0;">
            <p>
                <?php 
                printf(
                    _n('%d campaign permanently deleted.', '%d campaigns permanently deleted.', $deleted_count, 'wppoppop'),
                    number_format_i18n($deleted_count)
                ); 
                ?>
            </p>
        </div>
    <?php elseif (!empty($_GET['updated'])) : 
        $updated_count = intval($_GET['updated']);
    ?>
        <div class="notice notice-success is-dismissible" style="margin: 12px 0 16px 0;">
            <p>
                <?php 
                printf(
                    _n('%d campaign status updated.', '%d campaigns status updated.', $updated_count, 'wppoppop'),
                    number_format_i18n($updated_count)
                ); 
                ?>
            </p>
        </div>
    <?php endif; ?>

    <?php wp_nonce_field('wppoppop_admin_nonce', 'wppoppop_admin_nonce_field'); ?>
    <script>
        window.wppoppop_vars = window.wppoppop_vars || {};
        window.wppoppop_vars.ajax_url = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
        window.wppoppop_vars.nonce    = <?php echo wp_json_encode(wp_create_nonce('wppoppop_admin_nonce')); ?>;
    </script>

    <form id="wppoppop-campaigns-table-form" method="get">
        <input type="hidden" name="page" value="wppoppop" />
        <?php if (!empty($_GET['status'])) : ?>
            <input type="hidden" name="status" value="<?php echo esc_attr(sanitize_key($_GET['status'])); ?>" />
        <?php endif; ?>
        <?php wp_nonce_field('bulk-popups'); ?>
        <?php
        $list_table->views();
        $list_table->display();
        ?>
    </form>

    <!-- WordPress Standard Quick Edit Template -->
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
