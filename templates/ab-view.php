<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$campaigns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wppoppop_campaigns ORDER BY id DESC");
$popups    = $wpdb->get_results("SELECT uid, title, impressions, submissions FROM {$wpdb->prefix}wppoppop_items ORDER BY id DESC");
?>
<div class="wrap wppoppop-admin-page">
    <h1 class="wp-heading-inline">A/B Testing Campaigns</h1>
    <p>Create split-testing campaigns to serve random popup variations and evaluate which yields higher conversion rates.</p>

    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <!-- Left: Create Campaign Box -->
        <div class="postbox" style="flex: 1; padding: 20px;">
            <h2>Create A/B Campaign</h2>
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: 600; display: block; margin-bottom: 5px;">Campaign Name:</label>
                <input type="text" id="ab-campaign-title" class="widefat" placeholder="e.g. Summer Sale Split Test">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: 600; display: block; margin-bottom: 5px;">Select Variations to Rotate (Choose 2 or more):</label>
                <?php if (empty($popups)) : ?>
                    <p>No popups found. Create popups in the builder first.</p>
                <?php else : ?>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px; background: #fff;">
                        <?php foreach ($popups as $p) : ?>
                            <label style="display: block; margin-bottom: 6px;">
                                <input type="checkbox" class="ab-popup-checkbox" value="<?php echo esc_attr($p->uid); ?>">
                                <?php echo esc_html($p->title); ?> (<code><?php echo esc_html($p->uid); ?></code>)
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" id="btn-save-ab" class="button button-primary">Launch Campaign</button>
        </div>

        <!-- Right: Active Campaigns List -->
        <div class="postbox" style="flex: 2; padding: 20px;">
            <h2>Active Experiments</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Shortcode</th>
                        <th>Variations</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($campaigns)) : ?>
                        <tr><td colspan="4">No A/B campaigns active.</td></tr>
                    <?php else : ?>
                        <?php foreach ($campaigns as $camp) : 
                            $uids = json_decode($camp->popup_uids, true);
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html($camp->title); ?></strong></td>
                                <td><code>[wppoppop_ab uid="<?php echo esc_attr($camp->uid); ?>"]</code></td>
                                <td>
                                    <?php 
                                    foreach ($uids as $var_uid) {
                                        $var_popup = $wpdb->get_row($wpdb->prepare("SELECT title, impressions, submissions FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $var_uid));
                                        if ($var_popup) {
                                            $rate = $var_popup->impressions > 0 ? round(($var_popup->submissions / $var_popup->impressions) * 100, 1) : 0;
                                            echo "<div style='font-size:12px; margin-bottom:4px;'>&bull; <strong>" . esc_html($var_popup->title) . "</strong>: " . $var_popup->submissions . " subs / " . $var_popup->impressions . " views (" . $rate . "%)</div>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button type="button" class="button button-small button-link-delete btn-delete-camp" data-uid="<?php echo esc_attr($camp->uid); ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#btn-save-ab').on('click', function() {
        const title = $('#ab-campaign-title').val().trim();
        const selected = [];
        $('.ab-popup-checkbox:checked').each(function() {
            selected.push($(this).val());
        });

        if (!title) {
            alert('Please specify a campaign name.');
            return;
        }

        if (selected.length < 2) {
            alert('Please select at least 2 popups for the split test.');
            return;
        }

        $.post(wppoppop_vars.ajax_url, {
            action: 'wppoppop_save_campaign',
            nonce: wppoppop_vars.nonce,
            title: title,
            popup_uids: selected
        }, function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.data.message);
            }
        });
    });

    $('.btn-delete-camp').on('click', function() {
        if (!confirm('Are you sure you want to delete this A/B campaign?')) return;
        const uid = $(this).data('uid');
        $.post(wppoppop_vars.ajax_url, {
            action: 'wppoppop_delete_campaign',
            nonce: wppoppop_vars.nonce,
            uid: uid
        }, function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.data.message);
            }
        });
    });
});
</script>
