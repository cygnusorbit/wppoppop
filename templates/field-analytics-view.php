<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_subs = $wpdb->prefix . 'wppoppop_submissions';
$rows = $wpdb->get_results("SELECT fields_data FROM {$table_subs} ORDER BY id DESC LIMIT 500", ARRAY_A);

$field_summary = [];
$total_records = count($rows);

foreach ($rows as $r) {
    $fields = json_decode($r['fields_data'], true);
    if (is_array($fields)) {
        foreach ($fields as $key => $val) {
            if ($key === 'email' || $key === '_wppoppop_hp_email') continue;

            if (!isset($field_summary[$key])) {
                $field_summary[$key] = [];
            }

            if (is_array($val)) {
                foreach ($val as $item) {
                    $item_str = sanitize_text_field($item);
                    $field_summary[$key][$item_str] = ($field_summary[$key][$item_str] ?? 0) + 1;
                }
            } else {
                $val_str = sanitize_text_field($val);
                $field_summary[$key][$val_str] = ($field_summary[$key][$val_str] ?? 0) + 1;
            }
        }
    }
}
?>
<div class="wrap wppoppop-analytics-wrap">
    <h1>Field Analytics</h1>
    <p class="description">Analyze user response distributions across form elements, select menus, radio buttons, and ratings.</p>

    <?php if (empty($field_summary)) : ?>
        <div class="notice notice-info inline" style="margin-top: 15px;">
            <p>No custom field data collected yet. Once visitors submit popups with options or ratings, aggregate stats will appear here.</p>
        </div>
    <?php else : ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 20px; margin-top: 20px;">
            <?php foreach ($field_summary as $field_key => $values) : 
                $field_total = array_sum($values);
                ?>
                <div class="postbox" style="padding: 20px; border-radius: 6px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <h2 style="margin:0; font-size:15px; font-weight:700;">Field: <code><?php echo esc_html($field_key); ?></code></h2>
                        <span style="font-size:11px; background:#f0f0f1; padding:2px 8px; border-radius:10px;"><?php echo $field_total; ?> responses</span>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <?php foreach ($values as $val_label => $count) : 
                            $pct = ($field_total > 0) ? round(($count / $field_total) * 100, 1) : 0;
                            ?>
                            <div>
                                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:3px;">
                                    <span><?php echo esc_html($val_label ?: '(Empty)'); ?></span>
                                    <span><strong><?php echo $count; ?></strong> (<?php echo $pct; ?>%)</span>
                                </div>
                                <div style="background:#e2e8f0; height:8px; border-radius:4px; overflow:hidden;">
                                    <div style="background:#2271b1; width:<?php echo $pct; ?>%; height:100%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
