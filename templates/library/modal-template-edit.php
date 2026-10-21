<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
#wppoppop-te-modal{display:none;position:fixed;inset:0;background:rgba(15,23,42,.7);z-index:99999;align-items:center;justify-content:center;padding:16px;box-sizing:border-box}
#wppoppop-te-modal *{box-sizing:border-box}
#wppoppop-te-modal .te-dlg{background:#fff;color:#1e293b;border-radius:12px;width:640px;max-width:100%;max-height:94vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,.55)}
#wppoppop-te-modal .te-hd{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;background:#0f172a}
#wppoppop-te-modal .te-hd h3,body #wppoppop-te-modal .te-hd #wppoppop-te-title{margin:0 !important;padding:0 !important;font-size:18px !important;font-weight:600 !important;line-height:1.3 !important;color:#fff !important;text-shadow:none !important}
#wppoppop-te-modal .te-x{background:transparent !important;border:0 !important;box-shadow:none !important;color:#e2e8f0 !important;font-size:22px;cursor:pointer;line-height:1;padding:0 2px}
#wppoppop-te-modal .te-bd{padding:20px 22px 18px;overflow:auto;flex:1}
#wppoppop-te-modal .te-grid{display:grid;grid-template-columns:minmax(0,1.9fr) minmax(0,1fr);grid-template-areas:"img btns" "name cat" "desc desc" "used size";gap:14px 18px}
#wppoppop-te-modal .te-a-img{grid-area:img}
#wppoppop-te-modal .te-a-btns{grid-area:btns;display:flex;flex-direction:column;gap:8px;align-self:start;padding-top:20px}
#wppoppop-te-modal .te-a-name{grid-area:name}
#wppoppop-te-modal .te-a-cat{grid-area:cat}
#wppoppop-te-modal .te-a-desc{grid-area:desc}
#wppoppop-te-modal .te-a-size{grid-area:size;align-self:start}
#wppoppop-te-modal .te-a-used{grid-area:used;display:flex;flex-direction:column;min-height:0}
#wppoppop-te-modal .te-l{display:block;margin:0 0 6px;font-size:11px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:#334155}
#wppoppop-te-modal .te-prev{width:100%;height:150px;border:2px dashed #94a3b8;border-radius:8px;background:#eaf0fb center/cover no-repeat;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:12px;text-align:center;padding:8px}
#wppoppop-te-modal .te-prev.has-img{border-style:solid;border-color:#cbd5e1;color:transparent}
#wppoppop-te-modal .te-btn{display:block;width:100%;height:32px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;text-align:center;line-height:30px;padding:0 10px;margin:0;-webkit-appearance:none;appearance:none}
#wppoppop-te-modal .te-blue{background:#0a6bb5 !important;border:1px solid #085a99 !important;color:#fff !important}
#wppoppop-te-modal .te-ghost{background:#fff !important;border:1px solid #94a3b8 !important;color:#1e293b !important}
#wppoppop-te-modal .te-in{display:block;width:100%;height:38px;min-height:0;margin:0;padding:0 12px;border:1px solid #94a3b8;border-radius:6px;background:#fff;color:#1e293b;font-size:13px;line-height:1.2;box-shadow:none}
#wppoppop-te-modal textarea.te-in{height:96px;padding:10px 12px;resize:vertical;line-height:1.4}
#wppoppop-te-modal .te-in:focus{outline:none;border-color:#f97316;box-shadow:0 0 0 2px rgba(249,115,22,.2)}
#wppoppop-te-modal .te-ro{display:flex;align-items:center;height:38px;padding:0 12px;border:1px solid #94a3b8;border-radius:6px;background:#f8fafc;font-size:12px;font-weight:600;color:#334155}
#wppoppop-te-modal .te-box{flex:1;min-height:104px;overflow:auto;padding:10px 12px;border:1px solid #94a3b8;border-radius:6px;background:#f8fafc;font-size:12px;font-weight:600;line-height:1.9;color:#334155}
#wppoppop-te-modal .te-msg{display:none;margin-top:12px;font-size:12px}
#wppoppop-te-modal .te-ft{display:flex;justify-content:flex-end;gap:10px;padding:14px 22px;border-top:1px solid #cbd5e1}
#wppoppop-te-modal .te-ft .te-btn{width:auto;min-width:104px;height:36px;line-height:34px}
#wppoppop-te-modal .te-orange{background:#f97316 !important;border:1px solid #ea580c !important;color:#fff !important}
#wppoppop-te-modal .te-orange:hover{background:#ea580c !important}
#wppoppop-te-modal .te-btn:disabled{opacity:.6;cursor:not-allowed}
@media (max-width:560px){
#wppoppop-te-modal .te-grid{grid-template-columns:1fr;grid-template-areas:"img" "btns" "name" "cat" "desc" "size" "used"}
#wppoppop-te-modal .te-a-btns{flex-direction:row;padding-top:0}
}
</style>
<div class="wppoppop-modal-backdrop" id="wppoppop-te-modal" role="dialog" aria-modal="true" aria-labelledby="wppoppop-te-title">
    <div class="te-dlg">
        <div class="te-hd">
            <h3 id="wppoppop-te-title"><?php esc_html_e('Edit Template', 'wppoppop'); ?></h3>
            <button type="button" class="te-x" id="wppoppop-te-close" aria-label="<?php esc_attr_e('Close', 'wppoppop'); ?>">&times;</button>
        </div>
        <div class="te-bd">
            <div class="te-grid">
                <div class="te-a-img">
                    <span class="te-l"><?php esc_html_e('Popup Image', 'wppoppop'); ?></span>
                    <div class="te-prev" id="wppoppop-te-prev" role="button" tabindex="0"><?php esc_html_e('Click to upload image', 'wppoppop'); ?></div>
                    <input type="hidden" id="wppoppop-te-image" value="">
                </div>
                <div class="te-a-btns">
                    <button type="button" class="te-btn te-blue" id="wppoppop-te-upload"><?php esc_html_e('Upload Image', 'wppoppop'); ?></button>
                    <button type="button" class="te-btn te-ghost" id="wppoppop-te-remove"><?php esc_html_e('Remove', 'wppoppop'); ?></button>
                </div>
                <div class="te-a-name">
                    <label class="te-l" for="wppoppop-te-name"><?php esc_html_e('Popup Name', 'wppoppop'); ?></label>
                    <input type="text" class="te-in" id="wppoppop-te-name" maxlength="120">
                </div>
                <div class="te-a-cat">
                    <label class="te-l" for="wppoppop-te-category"><?php esc_html_e('Category', 'wppoppop'); ?></label>
                    <select class="te-in" id="wppoppop-te-category"></select>
                </div>
                <div class="te-a-desc">
                    <label class="te-l" for="wppoppop-te-desc"><?php esc_html_e('Description', 'wppoppop'); ?></label>
                    <textarea class="te-in" id="wppoppop-te-desc" maxlength="400"></textarea>
                </div>
                <div class="te-a-size">
                    <span class="te-l"><?php esc_html_e('Canvas Size', 'wppoppop'); ?></span>
                    <div class="te-ro" id="wppoppop-te-size"></div>
                </div>
                <div class="te-a-used">
                    <span class="te-l"><?php esc_html_e('Used Element(s)', 'wppoppop'); ?> <span id="wppoppop-te-total"></span></span>
                    <div class="te-box" id="wppoppop-te-elements"></div>
                </div>
            </div>
            <div class="te-msg" id="wppoppop-te-msg"></div>
        </div>
        <div class="te-ft">
            <button type="button" class="te-btn te-ghost" id="wppoppop-te-cancel"><?php esc_html_e('Cancel', 'wppoppop'); ?></button>
            <button type="button" class="te-btn te-orange" id="wppoppop-te-update"><?php esc_html_e('Update', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
