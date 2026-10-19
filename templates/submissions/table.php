<?php
if (!defined('ABSPATH')) {
    exit;
}

$colspan = 1 + count($active_cols);

if (!function_exists('wppoppop_subs_bulk_bar')) {
    function wppoppop_subs_bulk_bar($pos, $labels) {
        ?>
        <div class="tablenav <?php echo esc_attr($pos); ?>">
            <div class="alignleft actions bulkactions">
                <select class="wppoppop-bulk-select">
                    <option value="">Bulk actions</option>
                    <option value="delete">Delete</option>
                    <option value="anonymize">Anonymize (GDPR)</option>
                    <?php foreach ((array) $labels as $l) : if (empty($l['id'])) { continue; } ?>
                        <option value="label:<?php echo esc_attr($l['id']); ?>">Add label: <?php echo esc_html($l['name']); ?></option>
                    <?php endforeach; ?>
                    <option value="unlabel">Remove all labels</option>
                </select>
                <button type="button" class="button wppoppop-bulk-apply">Apply</button>
            </div>
            <div class="tablenav-pages wppoppop-pager">
                <span class="displaying-num"></span>
                <span class="pagination-links">
                    <button type="button" class="button wppoppop-pg-first">&laquo;</button>
                    <button type="button" class="button wppoppop-pg-prev">&lsaquo;</button>
                    <span class="paging-input"><span class="wppoppop-pg-cur">1</span> of <span class="wppoppop-pg-total">1</span></span>
                    <button type="button" class="button wppoppop-pg-next">&rsaquo;</button>
                    <button type="button" class="button wppoppop-pg-last">&raquo;</button>
                </span>
            </div>
        </div>
        <?php
    }
}
?>
<style>
#wppoppop-submissions-table{border:1px solid #c3c4c7}
#wppoppop-submissions-table th{font-size:11px;font-weight:600}
#wppoppop-submissions-table th.wppoppop-sortable{cursor:pointer;color:#2271b1}
#wppoppop-submissions-table td{font-size:11px;vertical-align:middle;padding:8px 10px}
#wppoppop-submissions-table td.col-first a.wppoppop-view-sub-btn{font-weight:600;text-decoration:none}
#wppoppop-submissions-table tr.is-pending td.col-first a.wppoppop-view-sub-btn,#wppoppop-submissions-table tr.is-pending td.col-email{font-weight:600;color:#1d3fd8}
#wppoppop-submissions-table tr.is-confirmed td{color:#50575e}
#wppoppop-submissions-table tr.is-pending .wppoppop-envelope{color:#c1272d}
#wppoppop-submissions-table tr.is-confirmed .wppoppop-envelope{color:#8c8f94}
#wppoppop-submissions-table .wppoppop-envelope,#wppoppop-submissions-table .wppoppop-tag{font-size:16px;width:16px;height:16px;vertical-align:middle}
#wppoppop-submissions-table .row-actions{font-size:11px;color:#a7aaad;position:static;left:0;margin-top:2px;visibility:hidden}
#wppoppop-submissions-table tr:hover .row-actions{visibility:visible}
#wppoppop-submissions-table .row-actions a{text-decoration:none;cursor:pointer}
#wppoppop-submissions-table .row-actions .trash a{color:#b32d2e}
.wppoppop-sub-row.wppoppop-hidden{display:none}
.wppoppop-pager .pagination-links .button{min-width:26px;padding:0 6px;height:26px;line-height:24px}
.wppoppop-pager .paging-input{margin:0 6px;font-size:12px}
</style>
<div class="wppoppop-subs-list">
<?php wppoppop_subs_bulk_bar('top', $tbl_opts['labels']); ?>
<table class="wp-list-table widefat fixed striped table-view-list" id="wppoppop-submissions-table" data-per-page="<?php echo esc_attr($tbl_opts['per_page']); ?>">
    <thead>
        <tr>
            <td class="manage-column column-cb check-column" style="width:30px;"><input type="checkbox" class="wppoppop-check-all"></td>
            <?php foreach ($active_cols as $ci => $c) : ?>
                <th class="wppoppop-sortable" data-sort="<?php echo $c === 'date' ? 'date' : 'text'; ?>" <?php echo $ci === 0 ? 'style="width:190px;"' : ''; ?>><?php echo esc_html($avail_cols[$c]); ?> &#8693;</th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody id="wppoppop-submissions-body">
    <?php if (empty($submissions)) : ?>
        <tr class="wppoppop-empty-row">
            <td colspan="<?php echo (int) $colspan; ?>" style="text-align:center;padding:30px 10px;color:#64748b;">
                No leads captured yet. Published popups with form elements will log submission records here.
            </td>
        </tr>
    <?php else : foreach ($submissions as $s) :
        $fields = json_decode($s->fields_data, true);
        if (!is_array($fields)) {
            $fields = [];
        }
        $is_confirmed = ($s->status === 'confirmed');
        $country      = !empty($s->country_code) ? $s->country_code : '—';
        $ts           = strtotime($s->created_at);
        $date_label   = $ts ? date_i18n('j/n/Y g:i A', $ts) : $s->created_at;
        $popup_title  = $s->popup_title ? $s->popup_title : 'Untitled Popup';
        $row_labels   = [];
        if (!empty($label_map[$s->id]) && is_array($label_map[$s->id])) {
            foreach ($label_map[$s->id] as $lid) {
                if (isset($labels_by_id[$lid])) {
                    $row_labels[] = $labels_by_id[$lid];
                }
            }
        }
        $view_attrs = 'data-id="' . esc_attr($s->id) . '" data-email="' . esc_attr($s->email) . '" data-popup="' . esc_attr($popup_title)
            . '" data-date="' . esc_attr($s->created_at) . '" data-country="' . esc_attr($country)
            . "\" data-fields='" . esc_attr(wp_json_encode($fields)) . "'";
        ?>
        <tr class="wppoppop-sub-row <?php echo $is_confirmed ? 'is-confirmed' : 'is-pending'; ?>"
            data-id="<?php echo esc_attr($s->id); ?>"
            data-status="<?php echo $is_confirmed ? 'confirmed' : 'pending'; ?>"
            data-ts="<?php echo esc_attr((int) $ts); ?>"
            data-email="<?php echo esc_attr(strtolower($s->email)); ?>"
            data-title="<?php echo esc_attr(strtolower($popup_title)); ?>">
            <th scope="row" class="check-column"><input type="checkbox" class="wppoppop-row-check" value="<?php echo esc_attr($s->id); ?>"></th>
            <?php foreach ($active_cols as $ci => $c) :
                if ($c === 'date')         { $val = $date_label; }
                elseif ($c === 'popup')    { $val = $popup_title; }
                elseif ($c === 'email')    { $val = $s->email; }
                elseif ($c === 'country')  { $val = $country; }
                elseif ($c === 'status')   { $val = ucfirst($s->status); }
                else {
                    $fk  = substr($c, 6);
                    $val = '';
                    foreach ([$fk, strtolower($fk), 'fields' . strtolower($fk), 'fields_' . strtolower($fk)] as $__try) {
                        if (isset($fields[$__try])) {
                            $val = $fields[$__try];
                            break;
                        }
                    }
                    $val = is_array($val) ? implode(', ', $val) : $val;
                }
                $val = ($val === '' || $val === null) ? '—' : (string) $val;
                $cls = ($c === 'email' || stripos($c, 'mail') !== false) ? 'col-email' : '';
                if ($ci === 0) : ?>
                    <td class="col-first <?php echo esc_attr($cls); ?>" data-sortval="<?php echo esc_attr(strtolower($val)); ?>">
                        <?php foreach ($row_labels as $rl) : ?>
                            <span class="dashicons dashicons-tag wppoppop-tag" style="color:<?php echo esc_attr($rl['color']); ?>;" title="<?php echo esc_attr($rl['name']); ?>"></span>
                        <?php endforeach; ?>
                        <span class="dashicons <?php echo $is_confirmed ? 'dashicons-email' : 'dashicons-email-alt'; ?> wppoppop-envelope"></span>
                        <a href="#" class="wppoppop-view-sub-btn" <?php echo $view_attrs; ?>><?php echo esc_html($val); ?></a>
                        <div class="row-actions">
                            <span class="view"><a href="#" class="wppoppop-view-sub-btn" <?php echo $view_attrs; ?>>View</a> | </span>
                            <span class="anon"><a href="#" class="wppoppop-anon-sub-btn" data-id="<?php echo esc_attr($s->id); ?>" title="Anonymize PII (GDPR)">Anonymize</a> | </span>
                            <span class="trash"><a href="#" class="wppoppop-del-sub-btn" data-id="<?php echo esc_attr($s->id); ?>">Delete</a></span>
                        </div>
                    </td>
                <?php else : ?>
                    <td class="<?php echo esc_attr($cls); ?>" data-sortval="<?php echo esc_attr(strtolower($val)); ?>"><?php echo esc_html($val); ?></td>
                <?php endif;
            endforeach; ?>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="manage-column column-cb check-column"><input type="checkbox" class="wppoppop-check-all"></td>
            <?php foreach ($active_cols as $c) : ?>
                <th><?php echo esc_html($avail_cols[$c]); ?></th>
            <?php endforeach; ?>
        </tr>
    </tfoot>
</table>
<?php wppoppop_subs_bulk_bar('bottom', $tbl_opts['labels']); ?>
</div>
