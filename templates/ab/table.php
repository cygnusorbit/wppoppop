<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$items_table = $wpdb->prefix . 'wppoppop_items';
$all_popups_raw = $wpdb->get_results("SELECT uid, title, impressions, submissions FROM {$items_table}");
$popups_by_uid = [];
foreach ($all_popups_raw as $p) {
    $popups_by_uid[$p->uid] = $p;
}
$ab_total = is_array($campaigns) ? count($campaigns) : 0;
$ab_cols  = '
    <th scope="col" class="manage-column column-num">#</th>
    <th scope="col" class="manage-column column-title">Campaign</th>
    <th scope="col" class="manage-column column-variations">Variations &amp; Performance</th>
    <th scope="col" class="manage-column column-status">Status</th>
    <th scope="col" class="manage-column column-shortcode">Shortcode</th>';
?>
<div class="wppoppop-table-wrap wpp-ab-table-wrap">
    <div class="tablenav top">
        <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo esc_html(sprintf(_n('%s item', '%s items', $ab_total, 'wppoppop'), number_format_i18n($ab_total))); ?></span></div>
        <br class="clear">
    </div>
    <table class="wp-list-table widefat fixed striped table-view-list" id="wppoppop-ab-table">
        <thead><tr><?php echo $ab_cols; ?></tr></thead>
        <tbody>
            <?php if (empty($campaigns)) : ?>
                <tr class="no-items">
                    <td class="colspanchange" colspan="5">
                        <div class="wpp-ab-empty">
                            <span class="dashicons dashicons-randomize"></span>
                            <?php esc_html_e('No A/B testing campaigns found. Click "New A/B Campaign" in the top bar to launch your first split-test experiment.', 'wppoppop'); ?>
                        </div>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($campaigns as $idx => $c) :
                    $uids      = json_decode($c->popup_uids, true) ?: [];
                    $shortcode = '[wppoppop_ab uid="' . $c->uid . '"]';
                    $status    = strtolower((string) $c->status);
                    $is_off    = in_array($status, ['draft', 'paused', 'inactive', 'trash'], true);
                    ?>
                    <tr class="wppoppop-ab-row" data-uid="<?php echo esc_attr($c->uid); ?>">
                        <td class="column-num"><?php echo (int) ($idx + 1); ?></td>
                        <td class="column-title has-row-actions">
                            <strong class="wpp-ab-title"><?php echo esc_html($c->title); ?></strong>
                            <div class="wpp-ab-meta"><code><?php echo esc_html($c->uid); ?></code></div>
                            <div class="wpp-ab-meta"><?php esc_html_e('Created:', 'wppoppop'); ?> <?php echo esc_html($c->created_at); ?></div>
                            <div class="row-actions">
                                <span class="delete"><button type="button" class="button-link wppoppop-del-ab-btn" data-uid="<?php echo esc_attr($c->uid); ?>"><?php esc_html_e('Delete', 'wppoppop'); ?></button></span>
                            </div>
                        </td>
                        <td class="column-variations">
                            <?php if (empty($uids)) : ?>
                                <span class="wpp-ab-none"><?php esc_html_e('No variations attached.', 'wppoppop'); ?></span>
                            <?php else : ?>
                                <div class="wpp-ab-vars">
                                    <?php foreach ($uids as $var_idx => $v_uid) :
                                        $pop     = isset($popups_by_uid[$v_uid]) ? $popups_by_uid[$v_uid] : null;
                                        $v_title = $pop ? $pop->title : 'Unknown (' . $v_uid . ')';
                                        $v_impr  = $pop ? intval($pop->impressions) : 0;
                                        $v_subs  = $pop ? intval($pop->submissions) : 0;
                                        $v_cr    = $v_impr > 0 ? round(($v_subs / $v_impr) * 100, 1) : 0;
                                        $letter  = chr(65 + ($var_idx % 26));
                                        ?>
                                        <div class="wpp-ab-var">
                                            <div class="head">
                                                <span class="letter">Variant <?php echo esc_html($letter); ?></span>
                                                <span class="cr"><?php echo esc_html($v_cr); ?>% CR</span>
                                            </div>
                                            <div class="name" title="<?php echo esc_attr($v_title); ?>"><?php echo esc_html($v_title); ?></div>
                                            <div class="stats">Impr: <strong><?php echo esc_html(number_format($v_impr)); ?></strong> &bull; Leads: <strong><?php echo esc_html(number_format($v_subs)); ?></strong></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="column-status">
                            <span class="wpp-badge <?php echo $is_off ? 'is-off' : 'is-on'; ?>"><?php echo esc_html(ucfirst((string) $c->status)); ?></span>
                        </td>
                        <td class="column-shortcode">
                            <code class="wppoppop-shortcode-chip wppoppop-copy-ab-sc-btn" data-shortcode="<?php echo esc_attr($shortcode); ?>" title="<?php esc_attr_e('Click to copy shortcode', 'wppoppop'); ?>"><?php echo esc_html($shortcode); ?></code>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot><tr><?php echo $ab_cols; ?></tr></tfoot>
    </table>
</div>
