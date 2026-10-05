<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$table_subs  = $wpdb->prefix . 'wppoppop_submissions';
$table_items = $wpdb->prefix . 'wppoppop_items';

$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$where_sql = '';
if (!empty($search_query)) {
    $where_sql = $wpdb->prepare("WHERE s.email LIKE %s OR i.title LIKE %s", '%' . $wpdb->esc_like($search_query) . '%', '%' . $wpdb->esc_like($search_query) . '%');
}

$submissions = $wpdb->get_results("SELECT s.*, i.title as popup_title 
    FROM {$table_subs} s LEFT JOIN {$table_items} i ON s.popup_uid = i.uid {$where_sql} ORDER BY s.id DESC LIMIT 100");

$total_subs      = $wpdb->get_var("SELECT COUNT(*) FROM {$table_subs}");
$total_confirmed = $wpdb->get_var("SELECT COUNT(*) FROM {$table_subs} WHERE status = 'confirmed'");
$total_views     = $wpdb->get_var("SELECT SUM(impressions) FROM {$table_items}");
$global_rate     = ($total_views > 0) ? round(($total_confirmed / $total_views) * 100, 2) : 0;
?>
<div class="wrap wppoppop-admin-page">
    <h1 class="wp-heading-inline">Submissions & Lead Editor</h1>
    <a href="<?php echo admin_url('admin-ajax.php?action=wppoppop_export_submissions_csv&nonce=' . wp_create_nonce('wppoppop_builder_nonce')); ?>" class="page-title-action">Export to CSV</a>
    <hr class="wp-header-end">

    <div style="display: flex; gap: 20px; margin: 20px 0;">
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Total Impressions</div>
            <div style="font-size: 32px; font-weight: 700; color: #2271b1; margin-top: 5px;"><?php echo number_format((int)$total_views); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Captured Leads</div>
            <div style="font-size: 32px; font-weight: 700; color: #8c8f94; margin-top: 5px;"><?php echo number_format((int)$total_subs); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Confirmed Leads</div>
            <div style="font-size: 32px; font-weight: 700; color: #00a32a; margin-top: 5px;"><?php echo number_format((int)$total_confirmed); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Conversion Rate</div>
            <div style="font-size: 32px; font-weight: 700; color: #d63638; margin-top: 5px;"><?php echo $global_rate; ?>%</div>
        </div>
    </div>

    <!-- Search Form -->
    <form method="get" style="margin-bottom: 15px; display: flex; gap: 8px;">
        <input type="hidden" name="page" value="wppoppop-submissions">
        <input type="search" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="Search lead by email..." style="width: 320px;">
        <button type="submit" class="button">Search Leads</button>
    </form>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 180px;">Popup</th>
                <th>Lead Email</th>
                <th style="width: 70px;">Country</th>
                <th style="width: 90px;">Status</th>
                <th>Submitted Data</th>
                <th style="width: 140px;">Date</th>
                <th style="width: 200px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($submissions)) : ?>
                <tr><td colspan="8">No lead records found.</td></tr>
            <?php else : ?>
                <?php foreach ($submissions as $sub) : 
                    $fields = json_decode($sub->fields_data, true);
                    ?>
                    <tr id="sub-row-<?php echo esc_attr($sub->id); ?>">
                        <td>#<?php echo esc_html($sub->id); ?></td>
                        <td><strong><?php echo esc_html($sub->popup_title ?: 'Deleted'); ?></strong></td>
                        <td class="sub-email-cell"><a href="mailto:<?php echo esc_attr($sub->email); ?>"><?php echo esc_html($sub->email); ?></a></td>
                        <td><code><?php echo esc_html($sub->country_code ?: 'GL'); ?></code></td>
                        <td><span style="font-size:11px;font-weight:700;color:#00a32a;"><?php echo ucfirst(esc_html($sub->status)); ?></span></td>
                        <td>
                            <?php 
                            if (!empty($fields)) {
                                foreach ($fields as $k => $v) {
                                    if ($k === 'signature') {
                                        echo "<span style='color:#0284c7;font-weight:700;'>[Signature]</span> ";
                                    } else {
                                        $v_str = is_array($v) ? implode(', ', $v) : $v;
                                        echo "<code>" . esc_html($k) . "</code>: " . esc_html($v_str) . " ";
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html($sub->created_at); ?></td>
                        <td>
                            <button type="button" class="button button-small btn-view-lead" data-id="<?php echo esc_attr($sub->id); ?>">View/Edit</button>
                            <button type="button" class="button button-small btn-gdpr-anonymize" data-id="<?php echo esc_attr($sub->id); ?>">Anonymize</button>
                            <button type="button" class="button button-small button-link-delete btn-gdpr-delete" data-id="<?php echo esc_attr($sub->id); ?>">Purge</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Lead Detail & Editor Modal -->
    <div id="wppoppop-lead-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:100000; align-items:center; justify-content:center;">
        <div style="background:#fff; width:600px; max-width:92vw; max-height:90vh; overflow-y:auto; border-radius:6px; padding:25px; box-shadow:0 10px 25px rgba(0,0,0,0.3);">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd; padding-bottom:12px; margin-bottom:15px;">
                <h2 style="margin:0; font-size:18px;">Lead Submission #<span id="lead-modal-id"></span></h2>
                <div>
                    <button type="button" class="button button-secondary" id="btn-print-lead"><span class="dashicons dashicons-printer" style="vertical-align:middle;"></span> Print</button>
                    <button type="button" class="button button-link" id="btn-close-lead-modal" style="font-size:20px; line-height:1;">&times;</button>
                </div>
            </div>

            <div id="lead-printable-area">
                <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-weight:700;display:block;">Email Address:</label>
                    <input type="email" id="edit-lead-email" class="widefat" style="margin-top:4px;">
                </div>

                <div id="edit-lead-fields-container" style="margin-top:15px;"></div>
                <div id="edit-lead-signature-wrap" style="margin-top:15px; display:none;">
                    <label style="font-weight:700;display:block;">Digital Signature:</label>
                    <img id="edit-lead-signature-img" src="" alt="Digital Signature" style="border:1px solid #ddd; background:#f9fafb; margin-top:5px; max-width:100%; height:auto;">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid #ddd; padding-top:15px;">
                <button type="button" class="button button-primary" id="btn-save-lead-edit">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentLeadData = null;

    $('.btn-view-lead').on('click', function() {
        const id = $(this).data('id');
        $.get(wppoppop_vars ? wppoppop_vars.ajax_url : ajaxurl, {
            action: 'wppoppop_get_submission_detail',
            nonce: '<?php echo wp_create_nonce('wppoppop_builder_nonce'); ?>',
            id: id
        }, function(res) {
            if (res.success) {
                currentLeadData = res.data;
                $('#lead-modal-id').text(currentLeadData.id);
                $('#edit-lead-email').val(currentLeadData.email);

                const container = $('#edit-lead-fields-container').empty();
                $('#edit-lead-signature-wrap').hide();

                for (const [key, val] of Object.entries(currentLeadData.fields)) {
                    if (key === 'signature') {
                        $('#edit-lead-signature-img').attr('src', val);
                        $('#edit-lead-signature-wrap').show();
                    } else {
                        const fieldVal = Array.isArray(val) ? val.join(', ') : val;
                        container.append(
                            '<div style="margin-bottom:10px;">' +
                            '<label style="font-weight:600;font-size:12px;display:block;">Field [' + key + ']:</label>' +
                            '<input type="text" class="widefat edit-field-item" data-key="' + key + '" value="' + fieldVal + '">' +
                            '</div>'
                        );
                    }
                }

                $('#wppoppop-lead-modal').css('display', 'flex');
            }
        });
    });

    $('#btn-close-lead-modal').on('click', function() {
        $('#wppoppop-lead-modal').hide();
    });

    $('#btn-print-lead').on('click', function() {
        const printContent = document.getElementById('lead-printable-area').innerHTML;
        const win = window.open('', '', 'height=500, width=650');
        win.document.write('<html><head><title>Print Lead #' + currentLeadData.id + '</title></head><body style="font-family:sans-serif;padding:30px;">');
        win.document.write('<h2>WpPopPop Lead Receipt #' + currentLeadData.id + '</h2>');
        win.document.write(printContent);
        win.document.write('</body></html>');
        win.document.close();
        win.print();
    });

    $('#btn-save-lead-edit').on('click', function() {
        const updatedFields = {};
        $('.edit-field-item').each(function() {
            updatedFields[$(this).data('key')] = $(this).val();
        });
        if (currentLeadData.fields.signature) {
            updatedFields['signature'] = currentLeadData.fields.signature;
        }

        $.post(wppoppop_vars ? wppoppop_vars.ajax_url : ajaxurl, {
            action: 'wppoppop_update_submission',
            nonce: '<?php echo wp_create_nonce('wppoppop_builder_nonce'); ?>',
            id: currentLeadData.id,
            email: $('#edit-lead-email').val(),
            fields: updatedFields
        }, function(res) {
            if (res.success) {
                alert(res.data.message);
                location.reload();
            } else {
                alert('Update error: ' + res.data.message);
            }
        });
    });

    $('.btn-gdpr-anonymize').on('click', function() {
        if (!confirm('Anonymize lead PII under GDPR?')) return;
        const subId = $(this).data('id');
        $.post(wppoppop_vars ? wppoppop_vars.ajax_url : ajaxurl, {
            action: 'wppoppop_anonymize_submission',
            nonce: '<?php echo wp_create_nonce('wppoppop_builder_nonce'); ?>',
            id: subId
        }, function(res) {
            location.reload();
        });
    });

    $('.btn-gdpr-delete').on('click', function() {
        if (!confirm('Permanently delete lead?')) return;
        const subId = $(this).data('id');
        $.post(wppoppop_vars ? wppoppop_vars.ajax_url : ajaxurl, {
            action: 'wppoppop_delete_submission',
            nonce: '<?php echo wp_create_nonce('wppoppop_builder_nonce'); ?>',
            id: subId
        }, function(res) {
            location.reload();
        });
    });
});
</script>
