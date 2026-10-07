<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * WpPopPop Native Admin Campaigns List Table
 * Extends WP_List_Table to provide authentic WordPress administration list controls.
 */
class WpPopPop_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => 'popup',
            'plural'   => 'popups',
            'ajax'     => false,
        ]);
    }

    protected function get_table_classes() {
        return [
            'widefat',
            'fixed',
            'striped',
            'table-view-list',
            'posts',
            'wppoppop-campaigns-table',
        ];
    }

    public function get_columns() {
        return [
            'cb'          => '<input id="cb-select-all" type="checkbox">',
            'title'       => __('Campaign Title', 'wppoppop'),
            'shortcode'   => __('Shortcode', 'wppoppop'),
            'status'      => __('Status', 'wppoppop'),
            'impressions' => __('Views', 'wppoppop'),
            'submissions' => __('Leads', 'wppoppop'),
            'cr'          => __('CR %', 'wppoppop'),
        ];
    }

    protected function get_sortable_columns() {
        return [
            'title'       => ['title', false],
            'impressions' => ['impressions', false],
            'submissions' => ['submissions', false],
            'cr'          => ['cr', false],
        ];
    }

    public function get_bulk_actions() {
        return [
            'publish'   => __('Set Status: Published', 'wppoppop'),
            'draft'     => __('Set Status: Draft', 'wppoppop'),
            'duplicate' => __('Duplicate Selected', 'wppoppop'),
            'export'    => __('Export Selected (JSON)', 'wppoppop'),
            'delete'    => __('Delete Selected', 'wppoppop'),
        ];
    }

    protected function get_views() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $all_rows   = $wpdb->get_results("SELECT id, status, impressions, submissions FROM {$table_name}");

        $all_count     = !empty($all_rows) ? count($all_rows) : 0;
        $pub_count     = 0;
        $draft_count   = 0;
        $high_cr_count = 0;

        if (!empty($all_rows)) {
            foreach ($all_rows as $row) {
                if ($row->status === 'publish') {
                    $pub_count++;
                } else {
                    $draft_count++;
                }
                $cr = ($row->impressions > 0) ? round(($row->submissions / $row->impressions) * 100, 1) : 0;
                if ($cr >= 10.0) {
                    $high_cr_count++;
                }
            }
        }

        $current = isset($_GET['status']) ? sanitize_key($_GET['status']) : 'all';

        return [
            'all'     => sprintf(
                '<a href="%s" class="%s" data-status="all">%s <span class="count">(<span class="wppoppop-count-all">%d</span>)</span></a>',
                esc_url(remove_query_arg(['status', 'paged'])),
                $current === 'all' ? 'current wppoppop-status-pill' : 'wppoppop-status-pill',
                __('All', 'wppoppop'),
                $all_count
            ),
            'publish' => sprintf(
                '<a href="%s" class="%s" data-status="publish">%s <span class="count">(<span class="wppoppop-count-publish">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'publish', 'paged' => 1])),
                $current === 'publish' ? 'current wppoppop-status-pill' : 'wppoppop-status-pill',
                __('Published', 'wppoppop'),
                $pub_count
            ),
            'draft'   => sprintf(
                '<a href="%s" class="%s" data-status="draft">%s <span class="count">(<span class="wppoppop-count-draft">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'draft', 'paged' => 1])),
                $current === 'draft' ? 'current wppoppop-status-pill' : 'wppoppop-status-pill',
                __('Drafts', 'wppoppop'),
                $draft_count
            ),
            'high-cr' => sprintf(
                '<a href="%s" class="%s" data-status="high-cr">%s <span class="count">(<span class="wppoppop-count-high-cr">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'high-cr', 'paged' => 1])),
                $current === 'high-cr' ? 'current wppoppop-status-pill' : 'wppoppop-status-pill',
                __('High Converting (&ge;10%)', 'wppoppop'),
                $high_cr_count
            ),
        ];
    }

    protected function bulk_actions($which = '') {
        if (is_null($this->_actions)) {
            $this->_actions = $this->get_bulk_actions();
        }
        if (empty($this->_actions)) {
            return;
        }

        $select_name = 'action' . ('bottom' === $which ? '2' : '');
        $select_id   = 'wppoppop-bulk-action-selector-' . $which;
        ?>
        <label for="<?php echo esc_attr($select_id); ?>" class="screen-reader-text"><?php esc_html_e('Select bulk action', 'wppoppop'); ?></label>
        <select name="<?php echo esc_attr($select_name); ?>" id="<?php echo esc_attr($select_id); ?>" class="wppoppop-bulk-action-selector">
            <option value=""><?php esc_html_e('Bulk Actions', 'wppoppop'); ?></option>
            <?php foreach ($this->_actions as $name => $title) : ?>
                <option value="<?php echo esc_attr($name); ?>"><?php echo esc_html($title); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="button action wppoppop-bulk-action-apply" disabled><?php esc_html_e('Apply', 'wppoppop'); ?></button>
        <span class="wppoppop-selected-count-badge" style="display:none;font-size:11px;font-weight:600;padding:2px 8px;background:#f0f0f1;color:#50575e;border-radius:10px;margin-left:4px;">0 selected</span>
        <?php
    }

    protected function extra_tablenav($which) {
        if ($which === 'top') {
            ?>
            <div class="alignleft actions">
                <div class="wppoppop-column-toggle-wrapper" style="display:inline-block;position:relative;">
                    <button type="button" class="button wppoppop-column-toggle-btn" style="height:30px;line-height:28px;">
                        <span class="dashicons dashicons-columns" style="font-size:16px;width:16px;height:16px;vertical-align:text-bottom;"></span>
                        <span><?php esc_html_e('Columns', 'wppoppop'); ?></span>
                        <span class="dashicons dashicons-arrow-down-alt2" style="font-size:12px;width:12px;height:12px;vertical-align:middle;"></span>
                    </button>
                    <div class="wppoppop-column-dropdown" style="display:none;position:absolute;left:0;top:100%;margin-top:4px;background:#ffffff;border:1px solid #c3c4c7;border-radius:4px;box-shadow:0 3px 6px rgba(0,0,0,0.1);padding:10px 14px;min-width:180px;z-index:1000;text-align:left;">
                        <div style="font-size:11px;font-weight:700;color:#646970;text-transform:uppercase;margin-bottom:6px;border-bottom:1px solid #f0f0f1;padding-bottom:4px;"><?php esc_html_e('Visible Columns', 'wppoppop'); ?></div>
                        <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-shortcode" checked> <?php esc_html_e('Shortcode', 'wppoppop'); ?></label>
                        <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-status" checked> <?php esc_html_e('Status', 'wppoppop'); ?></label>
                        <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-impressions" checked> <?php esc_html_e('Views', 'wppoppop'); ?></label>
                        <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-submissions" checked> <?php esc_html_e('Leads', 'wppoppop'); ?></label>
                        <label style="display:block;font-size:12px;color:#2c3338;padding:3px 0;cursor:pointer;"><input type="checkbox" class="wppoppop-col-toggle-cb" data-col="col-cr" checked> <?php esc_html_e('Conversion Rate', 'wppoppop'); ?></label>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    protected function pagination($which) {
        if (empty($this->_pagination_args)) {
            return;
        }

        $total_items  = $this->_pagination_args['total_items'];
        $total_pages  = $this->_pagination_args['total_pages'];
        $current_page = $this->get_pagenum();
        ?>
        <div class="tablenav-pages">
            <span class="displaying-num"><span class="wppoppop-total-items-text"><?php echo number_format_i18n($total_items); ?></span> <?php esc_html_e('items', 'wppoppop'); ?></span>
            <span class="pagination-links">
                <button type="button" class="first-page button wppoppop-btn-first" title="<?php esc_attr_e('First Page', 'wppoppop'); ?>" <?php disabled($current_page <= 1); ?>>&laquo;</button>
                <button type="button" class="prev-page button wppoppop-btn-prev" title="<?php esc_attr_e('Previous Page', 'wppoppop'); ?>" <?php disabled($current_page <= 1); ?>>&lsaquo;</button>
                <span class="paging-input">
                    <span class="wppoppop-current-page-text"><?php echo esc_html($current_page); ?></span> of <span class="total-pages wppoppop-total-pages-text"><?php echo esc_html($total_pages); ?></span>
                </span>
                <button type="button" class="next-page button wppoppop-btn-next" title="<?php esc_attr_e('Next Page', 'wppoppop'); ?>" <?php disabled($current_page >= $total_pages); ?>>&rsaquo;</button>
                <button type="button" class="last-page button wppoppop-btn-last" title="<?php esc_attr_e('Last Page', 'wppoppop'); ?>" <?php disabled($current_page >= $total_pages); ?>>&raquo;</button>
            </span>
        </div>
        <?php
    }

    public function single_row($item) {
        $cr        = ($item->impressions > 0) ? round(($item->submissions / $item->impressions) * 100, 1) : 0;
        $is_draft  = ($item->status !== 'publish');
        $row_class = 'wppoppop-table-row ' . ($is_draft ? 'status-draft' : 'status-publish');

        echo sprintf(
            '<tr id="wppoppop-row-%1$s" class="%2$s" data-uid="%1$s" data-title="%3$s" data-raw-title="%4$s" data-status="%5$s" data-impressions="%6$d" data-submissions="%7$d" data-cr="%8$s">',
            esc_attr($item->uid),
            esc_attr($row_class),
            esc_attr(strtolower($item->title)),
            esc_attr($item->title),
            esc_attr($item->status),
            (int) $item->impressions,
            (int) $item->submissions,
            esc_attr($cr)
        );
        $this->single_row_columns($item);
        echo '</tr>';
    }

    protected function single_row_columns($item) {
        list($columns, $hidden, $sortable, $primary) = $this->get_column_info();

        foreach ($columns as $column_name => $column_display_name) {
            $classes = "$column_name column-$column_name col-$column_name";
            if ($primary === $column_name) {
                $classes .= ' has-row-actions column-primary';
            }
            if (in_array($column_name, $hidden, true)) {
                $classes .= ' hidden';
            }

            if ('cb' === $column_name) {
                echo '<th scope="row" class="check-column col-cb">';
                echo $this->column_cb($item);
                echo '</th>';
            } elseif (method_exists($this, '_column_' . $column_name)) {
                echo call_user_func([$this, '_column_' . $column_name], $item, $classes);
            } elseif (method_exists($this, 'column_' . $column_name)) {
                echo "<td class='$classes'>";
                echo call_user_func([$this, 'column_' . $column_name], $item);
                echo '</td>';
            } else {
                echo "<td class='$classes'>";
                echo $this->column_default($item, $column_name);
                echo '</td>';
            }
        }
    }

    public function column_cb($item) {
        return sprintf(
            '<label class="screen-reader-text" for="cb-select-%1$s">%2$s</label>' .
            '<input id="cb-select-%1$s" type="checkbox" class="wppoppop-row-checkbox" name="popup_ids[]" value="%1$s">',
            esc_attr($item->uid),
            sprintf(__('Select %s', 'wppoppop'), esc_html($item->title))
        );
    }

    public function column_title($item) {
        $is_draft = ($item->status !== 'publish');
        $edit_url = admin_url('admin.php?page=wppoppop-builder&uid=' . $item->uid);

        $actions = [
            'edit'                 => sprintf('<a href="%s">%s</a>', esc_url($edit_url), __('Edit', 'wppoppop')),
            'inline hide-if-no-js' => sprintf('<button type="button" class="button-link editinline wppoppop-quick-edit-btn" data-uid="%s">%s</button>', esc_attr($item->uid), __('Quick&nbsp;Edit', 'wppoppop')),
            'duplicate'            => sprintf('<a href="#" class="wppoppop-duplicate-btn" data-uid="%s">%s</a>', esc_attr($item->uid), __('Duplicate', 'wppoppop')),
            'export'               => sprintf('<a href="#" class="wppoppop-export-btn" data-uid="%s">%s</a>', esc_attr($item->uid), __('Export JSON', 'wppoppop')),
            'trash'                => sprintf('<a href="#" class="submitdelete wppoppop-delete-btn" data-uid="%s" style="color:#b32d2e;">%s</a>', esc_attr($item->uid), __('Delete', 'wppoppop')),
        ];

        $title_markup = sprintf(
            '<strong><a class="row-title" href="%1$s">%2$s</a><span class="post-state wppoppop-post-state" style="%3$s"> — %4$s</span></strong>',
            esc_url($edit_url),
            esc_html($item->title),
            $is_draft ? '' : 'display:none;',
            __('Draft', 'wppoppop')
        );

        return $title_markup . $this->row_actions($actions);
    }

    public function column_shortcode($item) {
        return sprintf(
            '<code class="wppoppop-shortcode-chip" data-copy="[wppoppop uid=&quot;%1$s&quot;]" title="%2$s">[wppoppop uid="%3$s..."]</code>',
            esc_attr($item->uid),
            esc_attr__('Click to copy shortcode', 'wppoppop'),
            esc_html(substr($item->uid, 0, 8))
        );
    }

    public function column_status($item) {
        $is_publish = ($item->status === 'publish');
        return sprintf(
            '<span class="wppoppop-status-badge %s">%s</span>',
            $is_publish ? 'badge-active' : 'badge-inactive',
            esc_html(ucfirst($item->status))
        );
    }

    public function column_impressions($item) {
        return number_format_i18n((int) $item->impressions);
    }

    public function column_submissions($item) {
        return number_format_i18n((int) $item->submissions);
    }

    public function column_cr($item) {
        $cr    = ($item->impressions > 0) ? round(($item->submissions / $item->impressions) * 100, 1) : 0;
        $color = ($cr > 0) ? '#007017' : '#646970';
        return sprintf('<span style="font-weight:600;color:%s;">%s%%</span>', esc_attr($color), esc_html($cr));
    }

    public function column_default($item, $column_name) {
        return isset($item->$column_name) ? esc_html($item->$column_name) : '';
    }

    public function no_items() {
        esc_html_e('No popup campaigns found. Click "Create Popup" above to launch your first campaign.', 'wppoppop');
    }

    public function prepare_items($items = null) {
        global $wpdb;
        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];

        if ($items === null) {
            $table_name = $wpdb->prefix . 'wppoppop_items';
            $items = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");
        }

        $data = is_array($items) ? $items : [];

        // 1. Search Query Filter
        if (!empty($_REQUEST['s'])) {
            $search = strtolower(trim(wp_unslash($_REQUEST['s'])));
            $data   = array_filter($data, function($item) use ($search) {
                return (strpos(strtolower($item->title), $search) !== false) ||
                       (strpos(strtolower($item->uid), $search) !== false);
            });
        }

        // 2. Status Views Filter
        if (!empty($_REQUEST['status']) && $_REQUEST['status'] !== 'all') {
            $status_filter = sanitize_key($_REQUEST['status']);
            $data = array_filter($data, function($item) use ($status_filter) {
                if ($status_filter === 'high-cr') {
                    $cr = ($item->impressions > 0) ? round(($item->submissions / $item->impressions) * 100, 1) : 0;
                    return $cr >= 10.0;
                }
                return $item->status === $status_filter;
            });
        }

        // 3. Multi-Column Sorting
        $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_key($_REQUEST['orderby']) : 'id';
        $order   = (!empty($_REQUEST['order'])) ? sanitize_key($_REQUEST['order']) : 'desc';

        usort($data, function($a, $b) use ($orderby, $order) {
            $result = 0;
            if ($orderby === 'title') {
                $result = strcasecmp($a->title, $b->title);
            } elseif ($orderby === 'impressions') {
                $result = (int) $a->impressions <=> (int) $b->impressions;
            } elseif ($orderby === 'submissions') {
                $result = (int) $a->submissions <=> (int) $b->submissions;
            } elseif ($orderby === 'cr') {
                $cr_a   = ($a->impressions > 0) ? ($a->submissions / $a->impressions) : 0;
                $cr_b   = ($b->impressions > 0) ? ($b->submissions / $b->impressions) : 0;
                $result = $cr_a <=> $cr_b;
            } else {
                $result = (int) $a->id <=> (int) $b->id;
            }
            return ($order === 'asc') ? $result : -$result;
        });

        // 4. Dynamic Screen Options Per-Page Evaluation
        $user_id        = get_current_user_id();
        $screen         = get_current_screen();
        $option_name    = $screen ? $screen->get_option('per_page', 'option') : 'wppoppop_campaigns_per_page';
        $saved_per_page = $user_id ? get_user_meta($user_id, $option_name, true) : null;

        if (!empty($saved_per_page) && (int) $saved_per_page > 0) {
            $per_page = (int) $saved_per_page;
        } else {
            $per_page = ($screen && (int) $screen->get_option('per_page', 'default') > 0) 
                ? (int) $screen->get_option('per_page', 'default') 
                : 25;
        }

        $total_items  = count($data);
        $current_page = $this->get_pagenum();

        $this->items = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page) ?: 1,
        ]);
    }

    public function display() {
        $this->display_tablenav('top');
        ?>
        <table class="wp-list-table <?php echo implode(' ', $this->get_table_classes()); ?>" id="wppoppop-dashboard-table">
            <thead>
                <tr>
                    <?php $this->print_column_headers(); ?>
                </tr>
            </thead>
            <tbody id="wppoppop-table-tbody">
                <?php $this->display_rows_or_placeholder(); ?>
            </tbody>
            <tfoot>
                <tr>
                    <?php $this->print_column_headers(false); ?>
                </tr>
            </tfoot>
        </table>
        <?php
        $this->display_tablenav('bottom');
    }
}
