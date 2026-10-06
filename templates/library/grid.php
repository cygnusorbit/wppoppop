<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-library-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:20px;">
    <?php foreach ($templates as $tpl) : 
        $json_str = wp_json_encode($tpl['config']);
        ?>
        <div class="wppoppop-tpl-card" 
             data-id="<?php echo esc_attr($tpl['id']); ?>" 
             data-category="<?php echo esc_attr($tpl['category']); ?>" 
             data-title="<?php echo esc_attr(strtolower($tpl['title'])); ?>" 
             data-desc="<?php echo esc_attr(strtolower($tpl['description'])); ?>"
             style="background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;flex-direction:column;transition:transform 0.2s ease, box-shadow 0.2s ease;">
            
            <!-- Card Visual Banner / Preview Thumbnail -->
            <div style="height:150px;background:<?php echo esc_attr($tpl['bg_preview']); ?>;display:flex;align-items:center;justify-content:center;position:relative;padding:16px;">
                <span class="dashicons <?php echo esc_attr($tpl['icon']); ?>" style="font-size:48px;width:48px;height:48px;color:#ffffff;opacity:0.9;"></span>
                <span style="position:absolute;top:10px;left:10px;background:rgba(0,0,0,0.4);backdrop-filter:blur(2px);color:#ffffff;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                    <?php echo esc_html($tpl['cat_label']); ?>
                </span>
            </div>

            <!-- Card Body -->
            <div style="padding:16px;flex:1;display:flex;flex-direction:column;">
                <h3 style="margin:0 0 6px 0;font-size:15px;font-weight:700;color:#1e293b;"><?php echo esc_html($tpl['title']); ?></h3>
                <p style="margin:0 0 14px 0;font-size:12px;color:#64748b;line-height:1.4;flex:1;"><?php echo esc_html($tpl['description']); ?></p>
                
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid #f1f5f9;margin-top:auto;">
                    <span style="font-size:11px;color:#94a3b8;font-weight:600;">
                        <?php echo count($tpl['config']['elements']); ?> Elements &bull; <?php echo esc_html($tpl['config']['meta']['width']); ?>x<?php echo esc_html($tpl['config']['meta']['height']); ?>px
                    </span>
                    <div style="display:flex;gap:6px;">
                        <button type="button" class="button button-small wppoppop-tpl-preview-btn" 
                                data-id="<?php echo esc_attr($tpl['id']); ?>"
                                data-title="<?php echo esc_attr($tpl['title']); ?>"
                                data-desc="<?php echo esc_attr($tpl['description']); ?>"
                                data-elements="<?php echo count($tpl['config']['elements']); ?>"
                                data-width="<?php echo esc_attr($tpl['config']['meta']['width']); ?>"
                                data-height="<?php echo esc_attr($tpl['config']['meta']['height']); ?>"
                                data-config='<?php echo esc_attr($json_str); ?>'>
                            Preview
                        </button>
                        <button type="button" class="button button-small button-primary wppoppop-tpl-import-btn" 
                                data-id="<?php echo esc_attr($tpl['id']); ?>" 
                                data-title="<?php echo esc_attr($tpl['title']); ?>" 
                                data-config='<?php echo esc_attr($json_str); ?>'
                                style="background:#c2185b;border-color:#ad1457;">
                            Import & Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
