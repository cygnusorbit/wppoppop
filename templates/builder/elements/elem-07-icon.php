<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 07: FONT AWESOME / VECTOR ICON (icon) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-icon" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">7. Font Awesome / Vector Icon</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ICON CLASS (FONT AWESOME OR DASHICONS)</label>
    <div style="display:flex;gap:6px;align-items:stretch;margin-bottom:8px;"><input type="text" id="prop-icon-class" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconClass" value="dashicons-star-filled" placeholder="e.g. fas fa-star or dashicons-star-filled" style="font-size:12px;flex:1;min-width:0;"><button type="button" class="button wppoppop-fa-pick-btn" data-target="#prop-icon-class" title="Select icon" aria-label="Select icon" style="flex:0 0 auto;width:34px;padding:0;display:flex;align-items:center;justify-content:center;"><i class="fas fa-icons" style="pointer-events:none;"></i></button></div>
    <div style="display:flex;gap:5px;flex-wrap:wrap;margin-bottom:10px;">
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-star-filled">★ Star</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-yes-alt">✔ Check</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-heart">♥ Heart</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-bell">🔔 Bell</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-awards">🏆 Award</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-megaphone">📣 Promo</button>
    </div>
    <div id="wppoppop-icon-picker-grid" style="display:grid;grid-template-columns:repeat(6,1fr);gap:6px;margin-bottom:10px;">
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-star-filled" title="Star"><span class="dashicons dashicons-star-filled" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-yes-alt" title="Check"><span class="dashicons dashicons-yes-alt" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-heart" title="Heart"><span class="dashicons dashicons-heart" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-bell" title="Bell"><span class="dashicons dashicons-bell" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-awards" title="Award"><span class="dashicons dashicons-awards" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-megaphone" title="Megaphone"><span class="dashicons dashicons-megaphone" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-location" title="Location"><span class="dashicons dashicons-location" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-cart" title="Cart"><span class="dashicons dashicons-cart" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-tag" title="Tag"><span class="dashicons dashicons-tag" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-lock" title="Lock"><span class="dashicons dashicons-lock" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-thumbs-up" title="Thumbs Up"><span class="dashicons dashicons-thumbs-up" style="pointer-events:none;"></span></button>
        <button type="button" class="wppoppop-icon-preset wppoppop-icon-grid-btn" data-icon="dashicons-warning" title="Warning"><span class="dashicons dashicons-warning" style="pointer-events:none;"></span></button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ICON SIZE (PX)</label>
            <input type="number" id="prop-icon-size" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconSize" min="12" max="200" value="36" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ROTATION (DEG)</label>
            <input type="number" id="prop-icon-rotate" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="rotate" min="0" max="360" value="0" style="font-size:12px;">
        </div>
    </div>
</div>

<?php
$wppoppop_fa_picker_07 = __DIR__ . '/tab-style-icon-picker.php';
if ( file_exists( $wppoppop_fa_picker_07 ) ) { include_once $wppoppop_fa_picker_07; }
?>
