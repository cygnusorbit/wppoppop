<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WpPopPop_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('Campaign', 'wppoppop'),
            'plural'   => __('Campaigns', 'wppoppop'),
            'ajax'     => false,
        ]);
    }

    public function get_columns() {
        return [
            'cb'          => '<input type="checkbox" />',
            'title'       => __('Campaign', 'wppoppop'),
            'status'      => __('Status', 'wppoppop'),
            'impressions' => __('Impressions', 'wppoppop'),
            'submissions' => __('Submissions', 'wppoppop'),
            'conversion'  => __('Conv. Rate', 'wppoppop'),
            'created_at'  => __('Created', 'wppoppop'),
        ];
    }

    protected function get_sortable_columns() {
        return [
            'title'       => ['title', false],
            'status'      => ['status', false],
            'impressions' => ['impressions', false],
            'submissions' => ['submissions', false],
            'conversion'  => ['conversion', false],
            'created_at'  => ['created_at', true],
        ];
    }

    public function get_bulk_actions() {
        $actions = [];
        $status = isset($_REQUEST['status']) ? sanitize_key($_REQUEST['status']) : 'all';

        if ($status === 'trash') {
            if (current_user_can('edit_wppoppop_campaigns')) {
                $actions['untrash'] = __('Restore', 'wppoppop');
            }
            if (current_user_can('purge_wppoppop_campaigns')) {
                $actions['delete'] = __('Delete Permanently', 'wppoppop');
            }
            return $actions;
        }

        if (current_user_can('delete_wppoppop_campaigns')) {
            $actions['trash'] = __('Move to Trash', 'wppoppop');
        }
        if (current_user_can('publish_wppoppop_campaigns')) {
            $actions['publish'] = __('Set Status: Published', 'wppoppop');
            $actions['draft']   = __('Set Status: Draft', 'wppoppop');
        }
        if (current_user_can('edit_wppoppop_campaigns')) {
            $actions['duplicate'] = __('Duplicate Selected', 'wppoppop');
            $actions['export']    = __('Export Selected (JSON)', 'wppoppop');
        }

        return $actions;
    }

    protected function get_views() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $all_rows   = $wpdb->get_results("SELECT id, status, impressions, submissions FROM {$table_name}");

        $all_count     = 0;
        $pub_count     = 0;
        $draft_count   = 0;
        $high_cr_count = 0;
        $trash_count   = 0;

        if (!empty($all_rows)) {
            foreach ($all_rows as $row) {
                if ($row->status === 'trash') {
                    $trash_count++;
                    continue;
                }

                $all_count++;
                if ($row->status === 'publish' || $row->status === 'published') {
                    $pub_count++;
                } else {
                    $draft_count++;
                }

                $imp = intval($row->impressions);
                $sub = intval($row->submissions);
                $cr  = ($imp > 0) ? round(($sub / $imp) * 100, 1) : 0;
                if ($cr >= 10.0) {
                    $high_cr_count++;
                }
            }
        }

        $current = isset($_GET['status']) ? sanitize_key($_GET['status']) : 'all';

        $views = [
            'all'     => sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-all">%d</span>)</span></a>',
                esc_url(remove_query_arg(['status', 'paged'])),
                $current === 'all' ? 'current' : '',
                __('All', 'wppoppop'),
                $all_count
            ),
            'publish' => sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-publish">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'publish', 'paged' => 1])),
                $current === 'publish' ? 'current' : '',
                __('Published', 'wppoppop'),
                $pub_count
            ),
            'draft'   => sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-draft">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'draft', 'paged' => 1])),
                $current === 'draft' ? 'current' : '',
                __('Drafts', 'wppoppop'),
                $draft_count
            ),
            'high-cr' => sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-high-cr">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'high-cr', 'paged' => 1])),
                $current === 'high-cr' ? 'current' : '',
                __('High Converting (&ge;10%)', 'wppoppop'),
                $high_cr_count
            ),
        ];

        if ($trash_count > 0 || $current === 'trash') {
            $views['trash'] = sprintf(
                '<a href="%s" class="%s">%s <span class="count">(<span class="wppoppop-count-trash">%d</span>)</span></a>',
                esc_url(add_query_arg(['status' => 'trash', 'paged' => 1])),
                $current === 'trash' ? 'current' : '',
                __('Trash', 'wppoppop'),
                $trash_count
            );
        }

        return $views;
    }

    protected function extra_tablenav($which) {
        $status = isset($_REQUEST['status']) ? sanitize_key($_REQUEST['status']) : 'all';
        if ($which === 'top' && $status === 'trash' && current_user_can('purge_wppoppop_campaigns')) {
            $empty_trash_url = wp_nonce_url(
                admin_url('admin.php?page=wppoppop&action=empty_trash'),
                'wppoppop_empty_trash'
            );
            $empty_prompt = esc_attr(wp_json_encode(__('Are you sure you want to permanently empty the trash? This cannot be undone.', 'wppoppop')));
            ?>
            <div class="alignleft actions">
                <a href="<?php echo esc_url($empty_trash_url); ?>" class="button button-secondary apply" onclick="return confirm(<?php echo $empty_prompt; ?>);">
                    <?php esc_html_e('Empty Trash', 'wppoppop'); ?>
                </a>
            </div>
            <?php
        }
    }

    protected function column_cb($item) {
        $uid = is_array($item) ? $item['uid'] : $item->uid;
        return sprintf(
            '<input type="checkbox" name="bulk_ids[]" value="%s" class="wppoppop-select-row" />',
            esc_attr($uid)
        );
    }

    public function column_title($item) {
        $uid      = is_array($item) ? $item['uid'] : $item->uid;
        $title    = is_array($item) ? $item['title'] : $item->title;
        $status   = is_array($item) ? $item['status'] : $item->status;
        $is_trash = ($status === 'trash');

        $can_edit  = current_user_can('edit_wppoppop_campaign', $uid);
        $can_trash = current_user_can('delete_wppoppop_campaign', $uid);
        $can_purge = current_user_can('purge_wppoppop_campaign', $uid);

        $edit_url = admin_url('admin.php?page=wppoppop-builder&uid=' . esc_attr($uid));

        if ($is_trash) {
            $actions = [];
            if ($can_edit) {
                $untrash_url = wp_nonce_url(
                    admin_url('admin.php?page=wppoppop&action=untrash&uid=' . rawurlencode($uid)),
                    'wppoppop_untrash_' . $uid
                );
                $actions['untrash'] = sprintf('<a href="%s">%s</a>', esc_url($untrash_url), __('Restore', 'wppoppop'));
            }

            if ($can_purge) {
                $delete_url = wp_nonce_url(
                    admin_url('admin.php?page=wppoppop&action=delete&uid=' . rawurlencode($uid)),
                    'wppoppop_delete_' . $uid
                );
                $confirm_prompt = sprintf(__('Are you sure you want to permanently delete "%s"? This action cannot be undone.', 'wppoppop'), $title);
                $actions['delete'] = sprintf(
                    '<a href="%s" class="submitdelete" onclick="return confirm(%s);">%s</a>',
                    esc_url($delete_url),
                    esc_attr(wp_json_encode($confirm_prompt)),
                    __('Delete Permanently', 'wppoppop')
                );
            }

            return sprintf('<strong>%s</strong>%s', esc_html($title), $this->row_actions($actions));
        }

        $actions = [];

        if ($can_edit) {
            $actions['edit'] = sprintf('<a href="%s">%s</a>', esc_url($edit_url), __('Edit', 'wppoppop'));
            $actions['inline hide-if-no-js'] = sprintf(
                '<button type="button" class="button-link editinline wppoppop-quick-edit-btn" data-uid="%s" data-title="%s" data-status="%s">%s</button>',
                esc_attr($uid),
                esc_attr($title),
                esc_attr($status),
                __('Quick Edit', 'wppoppop')
            );
            $actions['duplicate'] = sprintf(
                '<a href="javascript:void(0);" role="button" class="wppoppop-duplicate-btn" data-uid="%s">%s</a>',
                esc_attr($uid),
                __('Duplicate', 'wppoppop')
            );
            $actions['export'] = sprintf(
                '<a href="javascript:void(0);" role="button" class="wppoppop-export-btn" data-uid="%s">%s</a>',
                esc_attr($uid),
                __('Export JSON', 'wppoppop')
            );
        }

        if ($can_trash) {
            $trash_url = wp_nonce_url(
                admin_url('admin.php?page=wppoppop&action=trash&uid=' . rawurlencode($uid)),
                'wppoppop_trash_' . $uid
            );
            $actions['trash'] = sprintf('<a href="%s" class="submitdelete">%s</a>', esc_url($trash_url), __('Trash', 'wppoppop'));
        }

        $post_state = ($status === 'draft') ? ' <span class="post-state">— ' . esc_html__('Draft', 'wppoppop') . '</span>' : '';

        $title_link = $can_edit
            ? sprintf('<a class="row-title" href="%s">%s</a>', esc_url($edit_url), esc_html($title))
            : esc_html($title);

        return sprintf('<strong>%s%s</strong>%s', $title_link, $post_state, $this->row_actions($actions));
    }

    public function column_status($item) {
        $status = is_array($item) ? $item['status'] : $item->status;
        if ($status === 'trash') {
            return '<span class="wppoppop-badge" style="background:#fce8e6;color:#c92a2a;font-weight:600;padding:2px 8px;border-radius:3px;">' . esc_html__('Trash', 'wppoppop') . '</span>';
        }

        $is_published = ($status === 'publish' || $status === 'published');
        $badge_class  = $is_published ? 'wppoppop-badge-success' : 'wppoppop-badge-draft';
        $label        = $is_published ? __('Published', 'wppoppop') : __('Draft', 'wppoppop');

        return sprintf('<span class="wppoppop-badge %s">%s</span>', esc_attr($badge_class), esc_html($label));
    }

    public function column_impressions($item) {
        $val = is_array($item) ? $item['impressions'] : $item->impressions;
        return number_format_i18n(intval($val));
    }

    public function column_submissions($item) {
        $val = is_array($item) ? $item['submissions'] : $item->submissions;
        return number_format_i18n(intval($val));
    }

    public function column_conversion($item) {
        $imp  = intval(is_array($item) ? $item['impressions'] : $item->impressions);
        $sub  = intval(is_array($item) ? $item['submissions'] : $item->submissions);
        $rate = ($imp > 0) ? round(($sub / $imp) * 100, 1) : 0;
        return $rate . '%';
    }

    public function column_created_at($item) {
        $val = is_array($item) ? $item['created_at'] : $item->created_at;
        return esc_html(date_i18n(get_option('date_format'), strtotime($val)));
    }

    public function column_default($item, $column_name) {
        if (is_array($item)) {
            return isset($item[$column_name]) ? esc_html($item[$column_name]) : '';
        }
        return isset($item->$column_name) ? esc_html($item->$column_name) : '';
    }

    public function single_row($item) {
        $uid    = is_array($item) ? $item['uid'] : $item->uid;
        $title  = is_array($item) ? $item['title'] : $item->title;
        $status = is_array($item) ? $item['status'] : $item->status;

        echo '<tr id="wppoppop-row-' . esc_attr($uid) . '" class="wppoppop-table-row" data-uid="' . esc_attr($uid) . '" data-raw-title="' . esc_attr($title) . '" data-status="' . esc_attr($status) . '">';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    /**
     * Header-safe redirect mechanism to handle actions whether executed
     * before admin-header.php or inside template rendering.
     *
     * @param string $url The destination URL.
     */
    protected function safe_redirect($url) {
        if (!headers_sent()) {
            wp_safe_redirect($url);
            exit;
        }

        echo '<script type="text/javascript">window.location.replace(' . wp_json_encode($url) . ');</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . esc_url($url) . '"></noscript>';
        exit;
    }

    public function process_actions() {
        global $wpdb;
        $items_table = $wpdb->prefix . 'wppoppop_items';
        $subs_table  = $wpdb->prefix . 'wppoppop_submissions';

        // 1. Move to Trash
        if (isset($_GET['action']) && $_GET['action'] === 'trash' && !empty($_GET['uid'])) {
            $uid = sanitize_text_field(wp_unslash($_GET['uid']));
            check_admin_referer('wppoppop_trash_' . $uid);

            if (!current_user_can('delete_wppoppop_campaign', $uid)) {
                wp_die(__('You do not have permission to trash this campaign.', 'wppoppop'));
            }

            $wpdb->update($items_table, ['status' => 'trash'], ['uid' => $uid]);

            $redirect = remove_query_arg(['action', 'uid', '_wpnonce']);
            $redirect = add_query_arg(['trashed' => 1, 'trashed_uid' => $uid], $redirect);
            $this->safe_redirect($redirect);
        }

        // 2. Restore from Trash
        if (isset($_GET['action']) && $_GET['action'] === 'untrash' && !empty($_GET['uid'])) {
            $uid = sanitize_text_field(wp_unslash($_GET['uid']));
            check_admin_referer('wppoppop_untrash_' . $uid);

            if (!current_user_can('edit_wppoppop_campaign', $uid)) {
                wp_die(__('You do not have permission to restore this campaign.', 'wppoppop'));
            }

            $wpdb->update($items_table, ['status' => 'draft'], ['uid' => $uid]);

            $redirect = remove_query_arg(['action', 'uid', '_wpnonce']);
            $redirect = add_query_arg('untrashed', 1, $redirect);
            $this->safe_redirect($redirect);
        }

        // 3. Permanent Purge
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['uid'])) {
            $uid = sanitize_text_field(wp_unslash($_GET['uid']));
            check_admin_referer('wppoppop_delete_' . $uid);

            if (!current_user_can('purge_wppoppop_campaign', $uid)) {
                wp_die(__('You do not have permission to permanently delete this campaign.', 'wppoppop'));
            }

            $wpdb->delete($items_table, ['uid' => $uid]);
            if ($wpdb->get_var("SHOW TABLES LIKE '{$subs_table}'") === $subs_table) {
                $wpdb->delete($subs_table, ['popup_uid' => $uid]);
            }

            $redirect = remove_query_arg(['action', 'uid', '_wpnonce']);
            $redirect = add_query_arg('deleted', 1, $redirect);
            $this->safe_redirect($redirect);
        }

        // 4. Empty Trash
        if (isset($_GET['action']) && $_GET['action'] === 'empty_trash') {
            check_admin_referer('wppoppop_empty_trash');

            if (!current_user_can('purge_wppoppop_campaigns')) {
                wp_die(__('You do not have permission to empty the trash.', 'wppoppop'));
            }

            $trashed_uids = $wpdb->get_col("SELECT uid FROM {$items_table} WHERE status = 'trash'");
            if (!empty($trashed_uids)) {
                $wpdb->query("DELETE FROM {$items_table} WHERE status = 'trash'");
                if ($wpdb->get_var("SHOW TABLES LIKE '{$subs_table}'") === $subs_table) {
                    $placeholders = implode(',', array_fill(0, count($trashed_uids), '%s'));
                    $wpdb->query($wpdb->prepare("DELETE FROM {$subs_table} WHERE popup_uid IN ($placeholders)", $trashed_uids));
                }
            }

            $redirect = remove_query_arg(['action', '_wpnonce']);
            $redirect = add_query_arg('deleted', count($trashed_uids), $redirect);
            $this->safe_redirect($redirect);
        }

        // 5. Bulk Actions Handler
        $action = $this->current_action();
        if ($action && in_array($action, ['trash', 'untrash', 'delete', 'publish', 'draft'], true)) {
            $nonce = !empty($_REQUEST['_wpnonce']) ? sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])) : '';
            $valid = wp_verify_nonce($nonce, 'bulk-' . $this->_args['plural']) || 
                     wp_verify_nonce($nonce, 'bulk-popups') || 
                     wp_verify_nonce($nonce, 'wppoppop_admin_nonce');

            if (!$valid) {
                wp_die(__('Security verification failed. Please try again.', 'wppoppop'));
            }

            $uids = [];
            if (!empty($_REQUEST['bulk_ids']) && is_array($_REQUEST['bulk_ids'])) {
                $uids = array_map('sanitize_text_field', wp_unslash($_REQUEST['bulk_ids']));
            }

            if (!empty($uids)) {
                $count = count($uids);
                $placeholders = implode(',', array_fill(0, count($uids), '%s'));

                if ($action === 'trash') {
                    if (!current_user_can('delete_wppoppop_campaigns')) {
                        wp_die(__('You do not have permission to move campaigns to the trash.', 'wppoppop'));
                    }
                    $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET status = 'trash' WHERE uid IN ($placeholders)", $uids));
                    $arg = ['trashed' => $count];
                } elseif ($action === 'untrash') {
                    if (!current_user_can('edit_wppoppop_campaigns')) {
                        wp_die(__('You do not have permission to restore campaigns.', 'wppoppop'));
                    }
                    $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET status = 'draft' WHERE uid IN ($placeholders)", $uids));
                    $arg = ['untrashed' => $count];
                } elseif ($action === 'delete') {
                    if (!current_user_can('purge_wppoppop_campaigns')) {
                        wp_die(__('You do not have permission to permanently delete campaigns.', 'wppoppop'));
                    }
                    $wpdb->query($wpdb->prepare("DELETE FROM {$items_table} WHERE uid IN ($placeholders)", $uids));
                    if ($wpdb->get_var("SHOW TABLES LIKE '{$subs_table}'") === $subs_table) {
                        $wpdb->query($wpdb->prepare("DELETE FROM {$subs_table} WHERE popup_uid IN ($placeholders)", $uids));
                    }
                    $arg = ['deleted' => $count];
                } elseif ($action === 'publish' || $action === 'draft') {
                    if (!current_user_can('publish_wppoppop_campaigns')) {
                        wp_die(__('You do not have permission to modify campaign statuses.', 'wppoppop'));
                    }
                    $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET status = %s WHERE uid IN ($placeholders)", array_merge([$action], $uids)));
                    $arg = ['updated' => $count];
                }

                $redirect = remove_query_arg(['action', 'action2', 'bulk_ids', '_wpnonce']);
                $redirect = add_query_arg($arg, $redirect);
                $this->safe_redirect($redirect);
            }
        }
    }

    public function prepare_items() {
        $this->process_actions();

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];

        $status = isset($_GET['status']) ? sanitize_key($_GET['status']) : 'all';
        $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

        $query  = "SELECT * FROM {$table_name} WHERE 1=1";
        $params = [];

        if ($status === 'trash') {
            $query .= " AND status = 'trash'";
        } elseif ($status === 'published' || $status === 'publish') {
            $query .= " AND status IN ('publish', 'published')";
        } elseif ($status === 'draft') {
            $query .= " AND status = 'draft'";
        } else {
            $query .= " AND status != 'trash'";
        }

        if (!empty($search)) {
            $query .= " AND (title LIKE %s OR uid LIKE %s)";
            $wildcard = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $orderby = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'created_at';
        $order   = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

        $allowed_orderby = ['title', 'status', 'impressions', 'submissions', 'created_at'];
        if (!in_array($orderby, $allowed_orderby, true)) {
            $orderby = 'created_at';
        }

        $query .= " ORDER BY {$orderby} {$order}";

        if (!empty($params)) {
            $results = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        } else {
            $results = $wpdb->get_results($query, ARRAY_A);
        }

        $data = $results ?: [];

        if ($status === 'high-cr') {
            $data = array_filter($data, function($item) {
                $imp = intval($item['impressions']);
                $sub = intval($item['submissions']);
                $cr  = ($imp > 0) ? round(($sub / $imp) * 100, 1) : 0;
                return $cr >= 10.0;
            });
        }

        $total_items   = count($data);
        $user_per_page = (int) get_user_meta(get_current_user_id(), 'wppoppop_campaigns_per_page', true);
        $per_page      = ($user_per_page > 0) ? $user_per_page : 25;
        $current_page  = $this->get_pagenum();

        $this->items = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page) ?: 1,
        ]);
    }
}
