<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-breakdown-section" style="margin-bottom:24px;">
    <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 14px 0;">Selection & Option Distributions</h3>
    
    <?php if (empty($choice_fields)) : ?>
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:24px;text-align:center;color:#64748b;">
            No multiple choice, dropdown, or checkbox form submissions recorded yet.
        </div>
    <?php else : ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(360px, 1fr));gap:16px;">
            <?php foreach ($choice_fields as $field_name => $data) : 
                $field_total = $data['total'];
                $options     = $data['options'];
                arsort($options);
                ?>
                <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <strong style="font-size:14px;color:#1e293b;text-transform:capitalize;"><?php echo esc_html(str_replace('_', ' ', $field_name)); ?></strong>
                        <span style="font-size:11px;font-weight:600;color:#64748b;background:#f1f5f9;padding:2px 8px;border-radius:4px;">
                            <?php echo number_format($field_total); ?> answers
                        </span>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <?php foreach ($options as $opt_label => $votes) : 
                            $pct = $field_total > 0 ? round(($votes / $field_total) * 100, 1) : 0;
                            ?>
                            <div>
                                <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
                                    <span style="font-weight:600;color:#334155;"><?php echo esc_html($opt_label); ?></span>
                                    <span style="color:#64748b;"><?php echo number_format($votes); ?> (<?php echo esc_html($pct); ?>%)</span>
                                </div>
                                <div style="width:100%;height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                                    <div style="width:<?php echo esc_attr($pct); ?>%;height:100%;background:#2563eb;border-radius:4px;transition:width 0.4s ease;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
