<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
#wppoppop-lib-cats-modal{display:none;position:fixed;inset:0;background:rgba(15,23,42,.7);z-index:99999;align-items:center;justify-content:center}
#wppoppop-lib-cats-modal .lc-dlg{background:#fff;color:#1e293b;border-radius:10px;width:480px;max-width:95vw;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,.5)}
#wppoppop-lib-cats-modal .lc-hd{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;background:#0f172a;color:#fff}
#wppoppop-lib-cats-modal .lc-hd h3{margin:0;font-size:15px;color:#fff}
#wppoppop-lib-cats-modal .lc-x{background:transparent;border:0;color:#94a3b8;font-size:24px;cursor:pointer;line-height:1}
#wppoppop-lib-cats-modal .lc-bd{padding:14px 18px;overflow:auto;flex:1}
#wppoppop-lib-cats-modal .lc-note{font-size:12px;color:#64748b;margin:0 0 10px}
#wppoppop-lib-cats-modal .lc-list{list-style:none;margin:0;padding:0}
#wppoppop-lib-cats-modal .lc-row{display:flex;align-items:center;gap:6px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:6px 8px;margin-bottom:6px}
#wppoppop-lib-cats-modal .lc-row.ui-sortable-helper{box-shadow:0 6px 16px rgba(0,0,0,.18);background:#fff}
#wppoppop-lib-cats-modal .lc-ph{height:36px;border:1px dashed #2563eb;border-radius:6px;margin-bottom:6px;background:#eff6ff}
#wppoppop-lib-cats-modal .lc-grip{cursor:grab;color:#94a3b8;font-size:18px;line-height:1;padding:0 4px;user-select:none;-webkit-user-select:none}
#wppoppop-lib-cats-modal .lc-row input[type=text]{flex:1;min-width:0}
#wppoppop-lib-cats-modal .lc-btn{border:1px solid #cbd5e1;background:#fff;border-radius:4px;width:26px;height:26px;line-height:1;cursor:pointer;color:#475569;padding:0}
#wppoppop-lib-cats-modal .lc-btn:hover{background:#f1f5f9}
#wppoppop-lib-cats-modal .lc-del{color:#b32d2e}
#wppoppop-lib-cats-modal .lc-lock{font-size:10px;color:#64748b;white-space:nowrap}
#wppoppop-lib-cats-modal .lc-add{display:flex;gap:6px;margin-top:10px}
#wppoppop-lib-cats-modal .lc-add input{flex:1}
#wppoppop-lib-cats-modal .lc-msg{display:none;margin-top:8px;font-size:12px}
#wppoppop-lib-cats-modal .lc-ft{display:flex;justify-content:space-between;gap:8px;padding:12px 18px;border-top:1px solid #e2e8f0}
</style>
<div class="wppoppop-modal-backdrop" id="wppoppop-lib-cats-modal" role="dialog" aria-modal="true" aria-labelledby="wppoppop-lc-title">
    <div class="lc-dlg">
        <div class="lc-hd">
            <h3 id="wppoppop-lc-title"><?php esc_html_e('Manage Categories', 'wppoppop'); ?></h3>
            <button type="button" class="lc-x" id="wppoppop-lc-close" aria-label="<?php esc_attr_e('Close', 'wppoppop'); ?>">&times;</button>
        </div>
        <div class="lc-bd">
            <p class="lc-note"><?php esc_html_e('Drag to reorder. These are the filter pills shown above the template grid.', 'wppoppop'); ?></p>
            <ul class="lc-list" id="wppoppop-lc-list"></ul>
            <div class="lc-add">
                <input type="text" id="wppoppop-lc-new" maxlength="40" placeholder="<?php esc_attr_e('New category name...', 'wppoppop'); ?>">
                <button type="button" class="button" id="wppoppop-lc-add"><?php esc_html_e('Add', 'wppoppop'); ?></button>
            </div>
            <div class="lc-msg" id="wppoppop-lc-msg"></div>
        </div>
        <div class="lc-ft">
            <button type="button" class="button" id="wppoppop-lc-reset"><?php esc_html_e('Reset to defaults', 'wppoppop'); ?></button>
            <span>
                <button type="button" class="button" id="wppoppop-lc-cancel"><?php esc_html_e('Cancel', 'wppoppop'); ?></button>
                <button type="button" class="button button-primary" id="wppoppop-lc-save"><?php esc_html_e('Save Categories', 'wppoppop'); ?></button>
            </span>
        </div>
    </div>
</div>
