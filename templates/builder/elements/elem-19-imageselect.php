<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 19: IMAGE SELECT GRID (imageselect) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-imageselect" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '19. Image Select Grid Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-imageselect-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'IMAGE CHOICES (LABEL OR LABEL::IMAGE_URL PER LINE)', 'wppoppop' ); ?></label>
        <textarea id="prop-imageselect-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" style="font-size:12px;">Starter Plan, Pro Plan, Enterprise</textarea>
    </div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:8px;">
        <div class="wppoppop-gp-col">
            <label for="prop-imageselect-cols" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'GRID COLUMNS', 'wppoppop' ); ?></label>
            <select id="prop-imageselect-cols" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="columns" style="font-size:12px;">
                <option value="2">2 Columns</option>
                <option value="3" selected>3 Columns</option>
                <option value="4">4 Columns</option>
            </select>
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-imageselect-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ACTIVE ACCENT', 'wppoppop' ); ?></label>
            <input type="color" id="prop-imageselect-color" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="activeColor" value="#c2185b" style="height:30px;padding:2px;cursor:pointer;">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-imageselect-multi" class="wppoppop-gp-bind" data-prop="multiSelect" value="1"> <?php esc_html_e( 'Allow Multiple Selections', 'wppoppop' ); ?>
    </label>
</div>
