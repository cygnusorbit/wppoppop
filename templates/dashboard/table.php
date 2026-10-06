<?php
if (!defined('ABSPATH')) {
    exit;
}

$all_count     = !empty($popups) ? count($popups) : 0;
$pub_count     = 0;
$draft_count   = 0;
$high_cr_count = 0;

if (!empty($popups)) {
    foreach ($popups as $p) {
        if ($p->status === 'publish') {
            $pub_count++;
        } else {
            $draft_count++;
        }
        $cr_val = ($p->impressions > 0) ? round(($p->submissions / $p->impressions) * 100, 1) : 0;
        if ($cr_val >= 10.0) {
            $high_cr_count++;
        }
    }
}
?>
<div class="wppoppop-table-wrap" style="width:100%;margin-top:16px;">
    <!-- WordPress Standard Subsubsub Filter Views -->
    <ul class="subsubsub" style="margin-bottom:8px;">
        <li class="all">
            <a href="#" class="wppoppop-status-pill current" data-status="all">
                All <span class="count">(<span class="wppoppop-count-all"><?php echo $all_count; ?></span>)</span>
            </a> |
        </li>
        <li class="publish">
            <a href="#" class="wppoppop-status-pill" data-status="publish">
                Published <span class="count">(<span class="wppoppop-count-publish"><?php echo $pub_count; ?></span>)</span>
            </a> |
        </li>
        <li class="draft">
            <a href="#" class="wppoppop-status-pill" data-status="draft">
                Drafts <span class="count">(<span class="wppoppop-count-draft"><?php echo $draft_count; ?></span>)</span>
            </a> |
        </li>
        <li class="high-cr">
            <a href="#" class="wppoppop-status-pill" data-status="high-cr">
                High Converting (&ge;10%) <span class="count">(<span class="wppoppop-count-high-cr"><?php echo $high_cr_count; ?></span>)</span>
            </a>
        </li>
    </ul>

    <!-- WordPress Standard Search Box -->
    <p class="search-box">
        <label class="screen-reader-text" for="wppoppop-search-input">Search Campaigns:</label>
        <input type="search" id="wppoppop-search-input" placeholder="Search campaigns..." style="height:30px;">
    </p>

    <!-- Top Tablenav Bar -->
    <div class="tablenav top" style="clear:both;">
        <div class="alignleft actions bulkactions">
            <label for="wppoppop-bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
            <select name="action" id="wppoppop-bulk-action-selector-top" class="wppoppop-bulk-action-selector">
                <option value="">Bulk Actions</option>
                <option value="publish">Set Status: Published</option>
                <option value="draft">Set Status: Draft</option>
                <option value="duplicate">Duplicate Selected</option>
                <option value="export">Export Selected (JSON)</option>
                <option value="delete">Delete Selected</option>
            </select>
            <button type="button" class="button action wppoppop-bulk-action-apply" disabled>Apply</button>
            <span class="wppoppop-selected-count-badge" style="display:none;font-size:11px;font-weight:600;padding:2px 8px;background:#f0f0f1;color:#50575e;border-radius:10px;margin-left:4px;">0 selected</span>
        </div>

        <div class="alignleft actions">
            <label for="wppoppop-per-page-select" style="font-size:12px;color:#646970;margin-right:2px;">Per Page:</label>
            <select id="wppoppop-per-page-select" style="height:30px;font-size:12px;">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all">All</option>
            </select>

            <!-- Column Visibility Toggle Dropdown -->
            <div class="wppoppop-column-toggle-wrapper" style="display:inline-block;position:relative;margin-left:6px;">
                <button type="button" class="button wppoppop-column-toggle-btn" style="height:30px;line-height:28px;">
                    <span class="dashicons dashicons-columns" style="font-size:16px;width:16px;height:16px;vertical-align:text-bottom;"></span>
                    <span>Columns</span>
                    <span class="dashicons dashicons-arrow-down-alt2" style="font-size:12px;width:12px;height:12px;vertical-align:middle;"></span>
                </button>
                <div class="wppoppop-column-dropdown" style="display:none;position:absolute;left:0;top:100%;margin-top:4px;background:#ffffff;border:1px solid #c3c4c7;border-radius:4px;box-shadow:0 3px 6px rgba(0,0,0,0.1);padding:10px 14px;min-width:180px;z-index:1000;text-align:left;">
                    <div style="font-size:11px;font-weight:700;color:#646970;text-transform:uppercase;margin-bottom:6px;border-bottom:1px solid #f0f0f1;padding-bottom:4px;">Visible Columns</div>
                    <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-shortcode" checked> Shortcode</label>
                    <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-status" checked> Status</label>
                    <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-impressions" checked> Views</label>
                    <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-submissions" checked> Leads</label>
                    <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-cr" checked> Conversion Rate</label>
                    <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-actions" checked> Actions</label>
                </div>
            </div>
        </div>

        <div class="tablenav-pages">
            <span class="displaying-num"><span class="wppoppop-total-items-text">0</span> items</span>
            <span class="pagination-links">
                <button type="button" class="first-page button wppoppop-btn-first" title="First Page">&laquo;</button>
                <button type="button" class="prev-page button wppoppop-btn-prev" title="Previous Page">&lsaquo;</button>
                <span class="paging-input">
                    <span class="wppoppop-current-page-text">1</span> of <span class="total-pages wppoppop-total-pages-text">1</span>
                </span>
                <button type="button" class="next-page button wppoppop-btn-next" title="Next Page">&rsaquo;</button>
                <button type="button" class="last-page button wppoppop-btn-last" title="Last Page">&raquo;</button>
            </span>
        </div>
    </div>

    <!-- Native WordPress List Table -->
    <table class="wp-list-table widefat fixed striped table-view-list posts wppoppop-campaigns-table" id="wppoppop-dashboard-table">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column col-cb">
                    <label class="screen-reader-text" for="cb-select-all">Select All</label>
                    <input id="cb-select-all" type="checkbox">
                </td>
                <th scope="col" class="manage-column column-title column-primary sortable asc wppoppop-sortable-th col-title" data-sort="title">
                    <a href="#">
                        <span>Campaign Title</span>
                        <span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span>
                    </a>
                </th>
                <th scope="col" class="manage-column column-shortcode col-shortcode" style="width:190px;">Shortcode</th>
                <th scope="col" class="manage-column column-status col-status" style="width:110px;">Status</th>
                <th scope="col" class="manage-column column-impressions sortable desc wppoppop-sortable-th col-impressions num" data-sort="impressions" style="width:100px;text-align:right;">
                    <a href="#">
                        <span>Views</span>
                        <span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span>
                    </a>
                </th>
                <th scope="col" class="manage-column column-submissions sortable desc wppoppop-sortable-th col-submissions num" data-sort="submissions" style="width:100px;text-align:right;">
                    <a href="#">
                        <span>Leads</span>
                        <span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span>
                    </a>
                </th>
                <th scope="col" class="manage-column column-cr sortable desc wppoppop-sortable-th col-cr num" data-sort="cr" style="width:90px;text-align:right;">
                    <a href="#">
                        <span>CR %</span>
                        <span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span>
                    </a>
                </th>
                <th scope="col" class="manage-column column-actions col-actions" style="width:210px;text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody id="wppoppop-table-tbody">
            <?php if (!empty($popups)) : ?>
                <?php foreach ($popups as $p) :
                    $cr = ($p->impressions > 0) ? round(($p->submissions / $p->impressions) * 100, 1) : 0;
                    $is_draft = ($p->status !== 'publish');
                ?>
                <tr class="wppoppop-table-row <?php echo $is_draft ? 'status-draft' : 'status-publish'; ?>" 
                    id="wppoppop-row-<?php echo esc_attr($p->uid); ?>"
                    data-uid="<?php echo esc_attr($p->uid); ?>" 
                    data-title="<?php echo esc_attr(strtolower($p->title)); ?>"
                    data-raw-title="<?php echo esc_attr($p->title); ?>"
                    data-status="<?php echo esc_attr($p->status); ?>"
                    data-impressions="<?php echo esc_attr((int)$p->impressions); ?>"
                    data-submissions="<?php echo esc_attr((int)$p->submissions); ?>"
                    data-cr="<?php echo esc_attr($cr); ?>">
                    <th scope="row" class="check-column col-cb">
                        <label class="screen-reader-text" for="cb-select-<?php echo esc_attr($p->uid); ?>">Select <?php echo esc_html($p->title); ?></label>
                        <input id="cb-select-<?php echo esc_attr($p->uid); ?>" type="checkbox" class="wppoppop-row-checkbox" value="<?php echo esc_attr($p->uid); ?>">
                    </th>
                    <td class="column-title column-primary col-title page-title">
                        <strong>
                            <a class="row-title" href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . $p->uid)); ?>">
                                <?php echo esc_html($p->title); ?>
                            </a>
                            <span class="post-state wppoppop-post-state" style="<?php echo $is_draft ? '' : 'display:none;'; ?>"> — Draft</span>
                        </strong>
                        <div class="row-actions">
                            <span class="edit"><a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . $p->uid)); ?>">Edit</a> | </span>
                            <span class="inline hide-if-no-js"><button type="button" class="button-link editinline wppoppop-quick-edit-btn" data-uid="<?php echo esc_attr($p->uid); ?>">Quick&nbsp;Edit</button> | </span>
                            <span class="duplicate"><a href="#" class="wppoppop-duplicate-btn" data-uid="<?php echo esc_attr($p->uid); ?>">Duplicate</a> | </span>
                            <span class="export"><a href="#" class="wppoppop-export-btn" data-uid="<?php echo esc_attr($p->uid); ?>">Export JSON</a> | </span>
                            <span class="trash"><a href="#" class="submitdelete wppoppop-delete-btn" data-uid="<?php echo esc_attr($p->uid); ?>">Delete</a></span>
                        </div>
                    </td>
                    <td class="column-shortcode col-shortcode">
                        <code class="wppoppop-shortcode-chip" data-copy="[wppoppop uid=&quot;<?php echo esc_attr($p->uid); ?>&quot;]" title="Click to copy shortcode">
                            [wppoppop uid="<?php echo esc_html(substr($p->uid, 0, 8)); ?>..."]
                        </code>
                    </td>
                    <td class="column-status col-status">
                        <span class="wppoppop-status-badge <?php echo ($p->status === 'publish') ? 'badge-active' : 'badge-inactive'; ?>">
                            <?php echo esc_html(ucfirst($p->status)); ?>
                        </span>
                    </td>
                    <td class="column-impressions col-impressions num" style="text-align:right;">
                        <?php echo number_format_i18n($p->impressions); ?>
                    </td>
                    <td class="column-submissions col-submissions num" style="text-align:right;">
                        <?php echo number_format_i18n($p->submissions); ?>
                    </td>
                    <td class="column-cr col-cr num" style="text-align:right;font-weight:600;color:<?php echo ($cr > 0) ? '#007017' : '#646970'; ?>;">
                        <?php echo esc_html($cr); ?>%
                    </td>
                    <td class="column-actions col-actions" style="text-align:right;">
                        <div class="wppoppop-row-action-buttons">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . $p->uid)); ?>" class="button button-small" title="Edit Popup">Edit</a>
                            <button type="button" class="button button-small wppoppop-embed-btn" data-uid="<?php echo esc_attr($p->uid); ?>" data-title="<?php echo esc_attr($p->title); ?>" title="Get Embed Code">Embed</button>
                            <button type="button" class="button button-small wppoppop-duplicate-btn" data-uid="<?php echo esc_attr($p->uid); ?>" title="Duplicate Campaign">Copy</button>
                            <button type="button" class="button button-small wppoppop-export-btn" data-uid="<?php echo esc_attr($p->uid); ?>" title="Export JSON">Export</button>
                            <button type="button" class="button button-small wppoppop-delete-btn" data-uid="<?php echo esc_attr($p->uid); ?>" style="color:#b32d2e;" title="Delete Campaign">&times;</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="no-items">
                    <td colspan="8" class="colspanchange" style="text-align:center;padding:32px 10px;">
                        No popup campaigns found. Click <strong>Create Popup</strong> above to launch your first campaign.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="manage-column column-cb check-column col-cb">
                    <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                    <input id="cb-select-all-2" type="checkbox">
                </td>
                <th scope="col" class="manage-column column-title column-primary sortable asc wppoppop-sortable-th col-title" data-sort="title">
                    <a href="#">
                        <span>Campaign Title</span>
                        <span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span>
                    </a>
                </th>
                <th scope="col" class="manage-column column-shortcode col-shortcode">Shortcode</th>
                <th scope="col" class="manage-column column-status col-status">Status</th>
                <th scope="col" class="manage-column column-impressions sortable desc wppoppop-sortable-th col-impressions num" data-sort="impressions" style="text-align:right;">
                    <a href="#">
                        <span>Views</span>
                        <span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span>
                    </a>
                </th>
                <th scope="col" class="manage-column column-submissions sortable desc wppoppop-sortable-th col-submissions num" data-sort="submissions" style="text-align:right;">
                    <a href="#">
                        <span>Leads</span>
                        <span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span>
                    </a>
                </th>
                <th scope="col" class="manage-column column-cr sortable desc wppoppop-sortable-th col-cr num" data-sort="cr" style="text-align:right;">
                    <a href="#">
                        <span>CR %</span>
                        <span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span>
                    </a>
                </th>
                <th scope="col" class="manage-column column-actions col-actions" style="text-align:right;">Actions</th>
            </tr>
        </tfoot>
    </table>

    <!-- Bottom Tablenav Bar -->
    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <label for="wppoppop-bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
            <select name="action2" id="wppoppop-bulk-action-selector-bottom" class="wppoppop-bulk-action-selector">
                <option value="">Bulk Actions</option>
                <option value="publish">Set Status: Published</option>
                <option value="draft">Set Status: Draft</option>
                <option value="duplicate">Duplicate Selected</option>
                <option value="export">Export Selected (JSON)</option>
                <option value="delete">Delete Selected</option>
            </select>
            <button type="button" class="button action wppoppop-bulk-action-apply" disabled>Apply</button>
        </div>

        <div class="tablenav-pages">
            <span class="displaying-num"><span class="wppoppop-total-items-text">0</span> items</span>
            <span class="pagination-links">
                <button type="button" class="first-page button wppoppop-btn-first" title="First Page">&laquo;</button>
                <button type="button" class="prev-page button wppoppop-btn-prev" title="Previous Page">&lsaquo;</button>
                <span class="paging-input">
                    <span class="wppoppop-current-page-text">1</span> of <span class="total-pages wppoppop-total-pages-text">1</span>
                </span>
                <button type="button" class="next-page button wppoppop-btn-next" title="Next Page">&rsaquo;</button>
                <button type="button" class="last-page button wppoppop-btn-last" title="Last Page">&raquo;</button>
            </span>
        </div>
    </div>

    <!-- WordPress Standard Quick Edit Template -->
    <table style="display:none;">
        <tbody id="wppoppop-quick-edit-template-root">
            <tr id="wppoppop-inline-edit" class="inline-edit-row inline-edit-row-post quick-edit-row quick-edit-row-post">
                <td colspan="8" class="colspanchange">
                    <fieldset class="inline-edit-col-left">
                        <legend class="inline-edit-legend">Quick Edit</legend>
                        <div class="inline-edit-col">
                            <label>
                                <span class="title">Title</span>
                                <span class="input-text-wrap">
                                    <input type="text" name="popup_title" class="ptitle wppoppop-qe-input-title" value="">
                                </span>
                            </label>
                            <label>
                                <span class="title">UID</span>
                                <span class="input-text-wrap">
                                    <input type="text" name="popup_uid" class="puid wppoppop-qe-input-uid" value="" readonly style="background:#f0f0f1;color:#646970;font-family:monospace;">
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="inline-edit-col-right">
                        <legend class="inline-edit-legend">Status</legend>
                        <div class="inline-edit-col">
                            <label class="inline-edit-status">
                                <span class="title">Status</span>
                                <select name="popup_status" class="wppoppop-qe-input-status">
                                    <option value="publish">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </label>
                        </div>
                    </fieldset>

                    <div class="submit inline-edit-save">
                        <button type="button" class="button cancel alignleft wppoppop-qe-btn-cancel">Cancel</button>
                        <button type="button" class="button button-primary save alignright wppoppop-qe-btn-save">Update</button>
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
