<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 24: PASSWORD INPUT (password) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-password" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '24. Password Input Settings', 'wppoppop' ); ?></div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:8px;">
        <div class="wppoppop-gp-col">
            <label for="prop-password-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'PLACEHOLDER', 'wppoppop' ); ?></label>
            <input type="text" id="prop-password-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="Enter password..." style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-password-minlen" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MIN LENGTH', 'wppoppop' ); ?></label>
            <input type="number" id="prop-password-minlen" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minLength" value="8" min="1" max="64" style="font-size:12px;">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-password-eye" class="wppoppop-gp-bind" data-prop="showEyeToggle" value="1" checked> <?php esc_html_e( 'Show Password Visibility Eye Icon', 'wppoppop' ); ?>
    </label>
</div>
