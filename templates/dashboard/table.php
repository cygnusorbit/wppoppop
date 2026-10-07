<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-table-container" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-top:20px;">
    <!-- Table Controls Bar -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <!-- Left Controls: Search & Bulk Actions -->
        <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;">
            <div style="position:relative;">
                <span class="dashicons dashicons-search" style="position:absolute;left:8px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;width:16px;height:16px;"></span>
                <input type="search" id="wppoppop-search-input" placeholder="Search campaigns by name or UID..." style="padding-left:28px;height:34px;border-radius:6px;border:1px solid #cbd5e1;min-width:260px;font-size:13px;">
            </div>

            <div class="wppoppop-bulk-actions-wrap" style="display:flex;align-items:center;gap:6px;">
                <select id="wppoppop-bulk-action-selector" style="height:34px;border-radius:6px;border:1px solid #cbd5e1;font-size:13px;">
                    <option value="">Bulk Actions</option>
                    <option value="publish">Set Status: Published</option>
                    <option value="draft">Set Status: Draft</option>
                    <option value="duplicate">Duplicate Selected</option>
                    <option value="export">Export Selected (JSON)</option>
                    <option value="delete">Delete Selected</option>
                </select>
                <button type="button" id="wppoppop-bulk-action-apply" class="button" disabled style="height:34px;line-height:32px;">Apply</button>
                <span id="wppoppop-selected-count-badge" style="display:none;font-size:11px;font-weight:600;padding:3px 8px;background:#f1f5f9;color:#475569;border-radius:12px;">0 selected</span>
            </div>
        </div>

        <!-- Right Controls: Items Per Page Selector (Column Toggle Removed) -->
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="display:flex;align-items:center;gap:6px;">
                <label for="wppoppop-per-page-select" style="font-size:12px;color:#64748b;font-weight:500;">Per Page:</label>
                <select id="wppoppop-per-page-select" style="height:34px;border-radius:6px;border:1px solid #cbd5e1;font-size:12px;">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Campaigns List Table (Action Column Removed, Row Actions Kept Under Title) -->
    <table class="wp-list-table widefat fixed striped posts wppoppop-campaigns-table" id="wppoppop-dashboard-table" style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;box-shadow:none;">
        <thead>
            <tr style="background:#f8fafc;">
                <td id="cb" class="manage-column column-cb check-column col-cb" style="width:38px;padding:10px 12px;">
                    <input id="cb-select-all" type="checkbox" title="Select All">
                </td>
                <th class="col-title" style="font-weight:600;padding:10px 14px;color:#334155;">Campaign Title</th>
                <th class="col-shortcode" style="font-weight:600;width:220px;padding:10px 14px;color:#334155;">Shortcode</th>
                <th class="col-status" style="font-weight:600;width:110px;padding:10px 14px;color:#334155;">Status</th>
                <th class="col-impressions" style="font-weight:600;width:110px;text-align:right;padding:10px 14px;color:#334155;">Views</th>
                <th class="col-submissions" style="font-weight:600;width:110px;text-align:right;padding:10px 14px;color:#334155;">Leads</th>
                <th class="col-cr" style="font-weight:600;width:100px;text-align:right;padding:10px 14px;color:#334155;">CR %</th>
            </tr>
        </thead>
        <tbody id="wppoppop-table-tbody">
            <?php if (!empty($popups)) : ?>
                <?php foreach ($popups as $p) :
                    $cr = ($p->impressions > 0) ? round(($p->submissions / $p->impressions) * 100, 1) : 0;
                ?>
                <tr class="wppoppop-table-row" data-uid="<?php echo esc_attr($p->uid); ?>" data-title="<?php echo esc_attr(strtolower($p->title)); ?>">
                    <th scope="row" class="check-column col-cb" style="padding:12px;">
                        <input type="checkbox" class="wppoppop-row-checkbox" value="<?php echo esc_attr($p->uid); ?>">
                    </th>
                    <td class="col-title" style="padding:12px 14px;">
                        <strong>
                            <a class="row-title" href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . $p->uid)); ?>" style="color:#0f172a;text-decoration:none;font-weight:600;">
                                <?php echo esc_html($p->title); ?>
                            </a>
                        </strong>
                        <div class="row-actions" style="margin-top:4px;font-size:12px;">
                            <span class="edit"><a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . $p->uid)); ?>">Edit</a> | </span>
                            <span class="embed"><a href="#" class="wppoppop-embed-btn" data-uid="<?php echo esc_attr($p->uid); ?>" data-title="<?php echo esc_attr($p->title); ?>">Get Embed Code</a> | </span>
                            <span class="duplicate"><a href="#" class="wppoppop-duplicate-btn" data-uid="<?php echo esc_attr($p->uid); ?>">Duplicate</a> | </span>
                            <span class="export"><a href="#" class="wppoppop-export-btn" data-uid="<?php echo esc_attr($p->uid); ?>">Export JSON</a> | </span>
                            <span class="trash"><a href="#" class="wppoppop-delete-btn" data-uid="<?php echo esc_attr($p->uid); ?>" style="color:#b91c1c;">Delete</a></span>
                        </div>
                    </td>
                    <td class="col-shortcode" style="padding:12px 14px;">
                        <code class="wppoppop-shortcode-chip" data-copy="[wppoppop uid=&quot;<?php echo esc_attr($p->uid); ?>&quot;]" title="Click to copy shortcode" style="cursor:pointer;background:#f1f5f9;padding:4px 8px;border-radius:4px;border:1px solid #cbd5e1;font-size:11px;display:inline-block;">
                            [wppoppop uid="<?php echo esc_html(substr($p->uid, 0, 8)); ?>..."]
                        </code>
                    </td>
                    <td class="col-status" style="padding:12px 14px;">
                        <span class="wppoppop-badge" style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:<?php echo ($p->status === 'publish') ? '#dcfce7;color:#15803d;' : '#f1f5f9;color:#64748b;'; ?>">
                            <?php echo esc_html(ucfirst($p->status)); ?>
                        </span>
                    </td>
                    <td class="col-impressions" style="text-align:right;font-weight:500;padding:12px 14px;color:#334155;">
                        <?php echo number_format_i18n($p->impressions); ?>
                    </td>
                    <td class="col-submissions" style="text-align:right;font-weight:500;padding:12px 14px;color:#334155;">
                        <?php echo number_format_i18n($p->submissions); ?>
                    </td>
                    <td class="col-cr" style="text-align:right;font-weight:600;padding:12px 14px;color:<?php echo ($cr > 0) ? '#059669;' : '#64748b;'; ?>">
                        <?php echo esc_html($cr); ?>%
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="wppoppop-no-items-row">
                    <td colspan="7" style="text-align:center;padding:48px 20px;color:#64748b;">
                        <span class="dashicons dashicons-format-aside" style="font-size:36px;width:36px;height:36px;color:#94a3b8;margin-bottom:10px;"></span>
                        <h3 style="margin:0 0 8px 0;font-size:16px;color:#1e293b;">No popups created yet</h3>
                        <p style="margin:0 0 16px 0;font-size:13px;">Create your first high-converting popup campaign to start capturing leads.</p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder')); ?>" class="button button-primary">Create Your First Popup</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background:#f8fafc;">
                <td class="manage-column column-cb check-column col-cb" style="width:38px;padding:10px 12px;">
                    <input id="cb-select-all-2" type="checkbox" title="Select All">
                </td>
                <th class="col-title" style="font-weight:600;padding:10px 14px;color:#334155;">Campaign Title</th>
                <th class="col-shortcode" style="font-weight:600;padding:10px 14px;color:#334155;">Shortcode</th>
                <th class="col-status" style="font-weight:600;padding:10px 14px;color:#334155;">Status</th>
                <th class="col-impressions" style="font-weight:600;text-align:right;padding:10px 14px;color:#334155;">Views</th>
                <th class="col-submissions" style="font-weight:600;text-align:right;padding:10px 14px;color:#334155;">Leads</th>
                <th class="col-cr" style="font-weight:600;text-align:right;padding:10px 14px;color:#334155;">CR %</th>
            </tr>
        </tfoot>
    </table>

    <!-- Pagination & Summary Footer -->
    <div class="wppoppop-pagination-wrap" id="wppoppop-pagination-bar" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:16px 4px 4px;margin-top:10px;border-top:1px solid #f1f5f9;">
        <div class="wppoppop-pagination-info" style="font-size:13px;color:#64748b;">
            Showing <span id="wppoppop-page-start" style="font-weight:600;color:#1e293b;">0</span> to <span id="wppoppop-page-end" style="font-weight:600;color:#1e293b;">0</span> of <span id="wppoppop-total-items" style="font-weight:600;color:#1e293b;">0</span> campaigns
        </div>
        <div class="wppoppop-pagination-nav" style="display:flex;align-items:center;gap:4px;">
            <button type="button" class="button wppoppop-page-btn" id="wppoppop-btn-first" title="First Page">&laquo;</button>
            <button type="button" class="button wppoppop-page-btn" id="wppoppop-btn-prev" title="Previous Page">&lsaquo;</button>
            <div id="wppoppop-page-numbers" style="display:flex;align-items:center;gap:4px;"></div>
            <button type="button" class="button wppoppop-page-btn" id="wppoppop-btn-next" title="Next Page">&rsaquo;</button>
            <button type="button" class="button wppoppop-page-btn" id="wppoppop-btn-last" title="Last Page">&raquo;</button>
        </div>
    </div>
</div>
