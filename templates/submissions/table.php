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
/* Label slot + envelope (first column) */
#wppoppop-submissions-table td.col-first .wppoppop-unread-dot{display:none}
#wppoppop-submissions-table .wppoppop-label-slot{display:inline-block;position:relative;width:18px;height:18px;margin-right:6px;vertical-align:middle}
#wppoppop-submissions-table .wppoppop-label-btn{display:block;position:relative;width:18px;height:18px;margin:0;padding:0;border:0;border-radius:3px;background:none;box-shadow:none;cursor:pointer;line-height:1}
#wppoppop-submissions-table .wppoppop-label-btn:focus{outline:0;box-shadow:0 0 0 1px var(--wpp-c-primary,#c1272d)}
#wppoppop-submissions-table .wppoppop-label-dots{position:absolute;top:0;right:0;bottom:0;left:0;display:-webkit-box;display:flex;flex-wrap:wrap;align-content:center;justify-content:center;overflow:hidden}
#wppoppop-submissions-table .wppoppop-label-dot{display:block;width:8px;height:8px;margin:1px;border-radius:50%}
#wppoppop-submissions-table .wppoppop-bookmark{position:absolute;left:1px;top:1px;color:#a7aaad;visibility:hidden}
#wppoppop-submissions-table .wppoppop-label-btn:hover .wppoppop-bookmark{color:#50575e}
#wppoppop-submissions-table tr:hover .wppoppop-bookmark,#wppoppop-submissions-table tr.is-menu-open .wppoppop-bookmark,#wppoppop-submissions-table .wppoppop-label-btn:focus .wppoppop-bookmark{visibility:visible}
#wppoppop-submissions-table .wppoppop-label-slot.has-labels .wppoppop-bookmark{display:none}
#wppoppop-submissions-table .wppoppop-mail{display:inline-block;width:16px;height:16px;margin-right:6px;vertical-align:middle;color:#8c8f94}
#wppoppop-submissions-table .wppoppop-mail svg{display:block}
#wppoppop-submissions-table .wppoppop-mail .m-closed{display:none}
#wppoppop-submissions-table tr.is-unread .wppoppop-mail{color:var(--wpp-c-primary,#c1272d)}
#wppoppop-submissions-table tr.is-unread .wppoppop-mail .m-closed{display:block}
#wppoppop-submissions-table tr.is-unread .wppoppop-mail .m-open{display:none}
#wppoppop-submissions-table td.col-first .row-actions{margin-left:46px}
/* Label picker menu (moved to <body> by JS) */
#wppoppop-label-menu{display:none;position:absolute;z-index:100050;min-width:170px;padding:6px;background:#fff;border:1px solid #dcdcde;border-radius:6px;box-shadow:0 6px 16px rgba(0,0,0,.18)}
#wppoppop-label-menu.is-open{display:block}
#wppoppop-label-menu .wppoppop-label-opt{display:block;width:100%;margin:0 0 6px;padding:8px 12px;border:0;border-radius:4px;box-sizing:border-box;cursor:pointer;font-size:12px;font-weight:600;line-height:1.4;text-align:left}
#wppoppop-label-menu .wppoppop-label-opt:last-child{margin-bottom:0}
#wppoppop-label-menu .wppoppop-label-opt:focus{outline:0;box-shadow:0 0 0 2px rgba(0,0,0,.35)}
#wppoppop-label-menu .wppoppop-label-opt .chk{display:none;margin-right:6px}
#wppoppop-label-menu .wppoppop-label-opt.is-on .chk{display:inline}
#wppoppop-label-menu .wppoppop-label-empty{padding:6px 8px;color:#646970;font-size:12px;max-width:200px}
#wppoppop-submissions-table .wppoppop-unread-dot{display:inline-block;width:8px;height:8px;margin:0 6px 1px 0;border-radius:50%;background:var(--wpp-c-danger,#d63638);vertical-align:middle}
#wppoppop-submissions-table tr:not(.is-unread) .wppoppop-unread-dot{display:none}
#wppoppop-submissions-table tr.is-unread .mark-unread-wrap,#wppoppop-submissions-table tr:not(.is-unread) .mark-read-wrap{display:none}
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
        $is_unread    = empty($s->is_read);
        $dot_col      = in_array('popup', $active_cols, true) ? 'popup' : reset($active_cols);
        $unread_dot   = '<span class="wppoppop-unread-dot" title="' . esc_attr__('Unread', 'wppoppop') . '" aria-label="' . esc_attr__('Unread', 'wppoppop') . '"></span>';
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
        <tr class="wppoppop-sub-row <?php echo $is_confirmed ? 'is-confirmed' : 'is-pending'; ?><?php echo $is_unread ? ' is-unread' : ''; ?>"
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
                    if ($val === '' || $val === null) {
                        $__want = preg_replace('/^fields/', '', preg_replace('/[^a-z0-9]/', '', strtolower($fk)));
                        foreach ($fields as $__fk2 => $__fv2) {
                            $__have = preg_replace('/^fields/', '', preg_replace('/[^a-z0-9]/', '', strtolower((string) $__fk2)));
                            if ($__want !== '' && $__have === $__want) { $val = $__fv2; break; }
                        }
                    }
                    // Cross-popup fallback: match by field label inside this row's own popup.
                    if (($val === '' || $val === null) && !empty($popup_field_maps[$s->popup_uid])) {
                        $__want_lbl = strtolower((string) $avail_cols[$c]);
                        $__want_key = sanitize_key($fk);
                        foreach ($popup_field_maps[$s->popup_uid] as $__mk => $__ml) {
                            if (strtolower($__ml) !== $__want_lbl && $__mk !== $__want_key) {
                                continue;
                            }
                            foreach ([$__mk, 'fields' . $__mk, 'fields_' . $__mk] as $__try2) {
                                if (isset($fields[$__try2]) && $fields[$__try2] !== '') {
                                    $val = $fields[$__try2];
                                    break 2;
                                }
                            }
                            $__nk2 = preg_replace('/[^a-z0-9]/', '', strtolower($__mk));
                            foreach ($fields as $__fk3 => $__fv3) {
                                if (preg_replace('/^fields/', '', preg_replace('/[^a-z0-9]/', '', strtolower((string) $__fk3))) === $__nk2 && $__fv3 !== '') {
                                    $val = $__fv3;
                                    break 2;
                                }
                            }
                        }
                    }
                    $val = is_array($val) ? implode(', ', $val) : $val;
                }
                $val = ($val === '' || $val === null) ? '—' : (string) $val;
                $cls = ($c === 'email' || stripos($c, 'mail') !== false) ? 'col-email' : '';
                if ($ci === 0) : ?>
                    <td class="col-first <?php echo esc_attr($cls); ?>" data-sortval="<?php echo esc_attr(strtolower($val)); ?>">
                        <?php
                        $row_label_ids = [];
                        foreach ($row_labels as $rl) { $row_label_ids[] = $rl['id']; }
                        ?>
                        <span class="wppoppop-label-slot<?php echo $row_label_ids ? ' has-labels' : ''; ?>" data-labels="<?php echo esc_attr(wp_json_encode($row_label_ids)); ?>">
                            <button type="button" class="wppoppop-label-btn" aria-haspopup="true" title="<?php esc_attr_e('Labels', 'wppoppop'); ?>" aria-label="<?php esc_attr_e('Labels', 'wppoppop'); ?>">
                                <span class="wppoppop-label-dots"><?php foreach ($row_labels as $rl) : ?><span class="wppoppop-label-dot" style="background:<?php echo esc_attr($rl['color']); ?>;" title="<?php echo esc_attr($rl['name']); ?>"></span><?php endforeach; ?></span>
                                <svg class="wppoppop-bookmark" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path d="M6 3h12v18l-6-4-6 4z" fill="currentColor"/></svg>
                            </button>
                        </span>
                        <span class="wppoppop-mail" aria-hidden="true">
                            <svg class="m-closed" viewBox="0 0 24 24" width="16" height="16" focusable="false"><rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M3 7l9 6 9-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                            <svg class="m-open" viewBox="0 0 24 24" width="16" height="16" focusable="false"><path d="M3 10l9-6 9 6v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M3 10l9 6 9-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                        </span>
                        <?php echo ($c === $dot_col) ? $unread_dot : ''; ?><a href="#" class="wppoppop-view-sub-btn" <?php echo $view_attrs; ?>><?php echo esc_html($val); ?></a>
                        <div class="row-actions">
                            <span class="view"><a href="#" class="wppoppop-view-sub-btn" <?php echo $view_attrs; ?>>View</a> | </span>
                            <span class="mark-unread-wrap"><a href="#" class="wppoppop-mark-unread-btn" data-id="<?php echo esc_attr($s->id); ?>">Mark as unread</a> | </span>
                            <span class="mark-read-wrap"><a href="#" class="wppoppop-mark-read-btn" data-id="<?php echo esc_attr($s->id); ?>">Mark as read</a> | </span>
                            <span class="anon"><a href="#" class="wppoppop-anon-sub-btn" data-id="<?php echo esc_attr($s->id); ?>" title="Anonymize PII (GDPR)">Anonymize</a> | </span>
                            <span class="trash"><a href="#" class="wppoppop-del-sub-btn" data-id="<?php echo esc_attr($s->id); ?>">Delete</a></span>
                        </div>
                    </td>
                <?php else : ?>
                    <td class="<?php echo esc_attr($cls); ?>" data-sortval="<?php echo esc_attr(strtolower($val)); ?>"><?php echo ($c === $dot_col) ? $unread_dot : ''; echo esc_html($val); ?></td>
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
<?php
$wppoppop_lbl_text = function ($hex) {
    $h = ltrim((string) $hex, '#');
    if (strlen($h) === 3) { $h = $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2]; }
    if (strlen($h) !== 6 || !ctype_xdigit($h)) { return '#ffffff'; }
    $y = (hexdec(substr($h, 0, 2)) * 299 + hexdec(substr($h, 2, 2)) * 587 + hexdec(substr($h, 4, 2)) * 114) / 1000;
    return $y > 200 ? '#1d2327' : '#ffffff';
};
?>
<div id="wppoppop-label-menu" role="menu" aria-label="<?php esc_attr_e('Labels', 'wppoppop'); ?>">
    <?php $__has_lbl = false; foreach ((array) $tbl_opts['labels'] as $__l) : if (empty($__l['id'])) { continue; } $__has_lbl = true; $__col = !empty($__l['color']) ? $__l['color'] : '#f0c330'; ?>
        <button type="button" class="wppoppop-label-opt" role="menuitemcheckbox" aria-checked="false" data-label-id="<?php echo esc_attr($__l['id']); ?>" data-color="<?php echo esc_attr($__col); ?>" data-name="<?php echo esc_attr($__l['name']); ?>" style="background:<?php echo esc_attr($__col); ?>;color:<?php echo esc_attr($wppoppop_lbl_text($__col)); ?>;"><span class="chk">&#10003;</span><?php echo esc_html($__l['name']); ?></button>
    <?php endforeach; if (!$__has_lbl) : ?>
        <div class="wppoppop-label-empty"><?php esc_html_e('No labels yet. Add some in Table options.', 'wppoppop'); ?></div>
    <?php endif; ?>
</div>
<?php wppoppop_subs_bulk_bar('bottom', $tbl_opts['labels']); ?>
</div>
