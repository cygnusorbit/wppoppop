<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
#wppoppop-table-options-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:100000;align-items:center;justify-content:center}
#wppoppop-table-options-modal.is-open{display:flex}
#wppoppop-table-options-modal .wto-panel{position:relative;background:#efefef;width:640px;max-width:94vw;max-height:92vh;overflow:auto;padding:28px 30px 24px;box-shadow:0 20px 40px rgba(0,0,0,.35)}
#wppoppop-table-options-modal h2{color:#c1272d;font-weight:400;font-size:20px;margin:0 0 26px;display:flex;align-items:center;gap:10px}
#wppoppop-table-options-modal .wto-row{display:flex;gap:20px;margin-bottom:22px;align-items:flex-start}
#wppoppop-table-options-modal .wto-label{width:140px;flex:none;font-size:12px;color:#333;padding-top:8px}
#wppoppop-table-options-modal .wto-body{flex:1;min-width:0}
.wto-add-btn{background:#7dc32d;color:#fff;border:0;padding:10px 24px;font-size:12px;font-weight:600;cursor:pointer;border-radius:2px}
/* WPP_TO_V2: labels grid, transparent fields, colour icon, per-page select */
#wppoppop-table-options-modal #wto-labels{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 12px;margin:0 0 12px}
#wppoppop-table-options-modal #wto-labels:empty{display:none;margin:0}
#wppoppop-table-options-modal .wto-lbl{display:flex;align-items:center;gap:8px;min-width:0;margin:0;padding:6px 10px;box-sizing:border-box;color:#fff;font-size:11px;font-weight:600;border-radius:2px}
#wppoppop-table-options-modal .wto-lbl input.lbl-name[type="text"]{flex:1 1 auto;min-width:0;width:auto;height:24px !important;min-height:0 !important;margin:0;padding:2px 4px;background:transparent !important;border:0 !important;border-bottom:1px dashed rgba(255,255,255,.65) !important;border-radius:0 !important;box-shadow:none !important;outline:0;color:#fff !important;font-size:11px;font-weight:600}
#wppoppop-table-options-modal .wto-lbl input.lbl-name[type="text"]:focus{border-bottom:1px solid #fff !important;box-shadow:none !important}
#wppoppop-table-options-modal .wto-lbl input.lbl-name::placeholder{color:rgba(255,255,255,.7)}
#wppoppop-table-options-modal .lbl-color-wrap{position:relative;flex:0 0 auto;display:inline-block;width:20px;height:20px;cursor:pointer}
#wppoppop-table-options-modal .lbl-color-wrap .dashicons{width:20px;height:20px;font-size:18px;line-height:20px;color:#fff}
#wppoppop-table-options-modal .lbl-color-wrap input.lbl-color{position:absolute !important;left:0;top:0;width:100% !important;height:100% !important;min-height:0 !important;margin:0;padding:0 !important;border:0;opacity:0;cursor:pointer}
#wppoppop-table-options-modal .lbl-color-wrap:hover .dashicons,#wppoppop-table-options-modal .lbl-del:hover{opacity:.8}
#wppoppop-table-options-modal .lbl-del{flex:0 0 auto;width:20px;height:20px;font-size:16px;line-height:20px;color:#fff;cursor:pointer;text-decoration:none}
@media (max-width:480px){#wppoppop-table-options-modal #wto-labels{grid-template-columns:1fr}}
.wto-cols{display:flex;border-top:1px solid #ccc;padding-top:10px}
.wto-cols>div{flex:1;padding:0 8px}
.wto-cols>div+div{border-left:1px solid #ccc}
.wto-cols h4{text-align:center;font-size:11px;margin:6px 0 12px}
.wppoppop-cols-list{list-style:none;margin:0;padding:0;min-height:60px;max-height:190px;overflow:auto}
.wppoppop-cols-list li{background:linear-gradient(#fff,#ececec);border:1px solid #d0d0d0;text-align:center;padding:8px 6px;margin:0 0 8px;font-size:11px;cursor:move}
#wppoppop-cols-active li{font-weight:700;border-bottom:2px solid #7dc32d}
#wppoppop-cols-inactive li{color:#777}
.wto-ph{border:1px dashed #c1272d !important;background:#fafafa !important;height:30px}
.wto-side{position:absolute;top:60px;right:-44px;display:flex;flex-direction:column;gap:2px}
.wto-side button{width:44px;height:44px;border:0;color:#fff;font-size:18px;cursor:pointer}
.wto-save{background:#7dc32d}.wto-close{background:#d63638}
#wto-per-page{width:100%;height:36px;font-size:13px}
#wppoppop-table-options-modal input[type=text].wto-custom{width:calc(100% - 90px)}
@media (max-width:760px){.wto-side{right:0;top:-44px;flex-direction:row}}
</style>
<div id="wppoppop-table-options-modal" data-config="<?php echo esc_attr(wp_json_encode($tbl_config_payload)); ?>">
    <div class="wto-panel">
        <div class="wto-side">
            <button type="button" class="wto-save" id="wto-save" title="Save">&#10003;</button>
            <button type="button" class="wto-close" id="wto-close" title="Close">&#10005;</button>
        </div>
        <h2><span class="dashicons dashicons-list-view"></span> Table options
            <small style="font-size:11px;color:#666;">(<?php echo $sel_uid === '' ? 'All popups / default' : esc_html($sel_uid); ?>)</small>
        </h2>

        <div class="wto-row">
            <div class="wto-label">Labels</div>
            <div class="wto-body">
                <div id="wto-labels"></div>
                <button type="button" class="wto-add-btn" id="wto-add-label">+ Add New</button>
            </div>
        </div>

        <div class="wto-row">
            <div class="wto-label">Column layout</div>
            <div class="wto-body">
                <div class="wto-cols">
                    <div>
                        <h4>Active columns</h4>
                        <ul id="wppoppop-cols-active" class="wppoppop-cols-list"></ul>
                    </div>
                    <div>
                        <h4>Inactive columns</h4>
                        <ul id="wppoppop-cols-inactive" class="wppoppop-cols-list"></ul>
                    </div>
                </div>
                <div style="margin-top:10px;">
                    <span class="wto-custom-wrap" hidden style="display:none !important;"><input type="text" class="wto-custom" id="wto-custom-key" placeholder="Custom form field name (e.g. mobile_number)"><button type="button" class="button" id="wto-add-custom">Add field</button></span>
                    <?php if ($sel_uid !== '') : ?>
                        
                        <p style="margin:8px 0 0;font-size:11px;color:#666;">Form fields on this popup sync live &mdash; new fields appear under Inactive columns without refreshing.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="wto-row">
            <div class="wto-label">Entries per page</div>
            <div class="wto-body"><select id="wto-per-page"><option value="10">10</option><option value="20">20</option><option value="30">30</option></select></div>
        </div>
    </div>
</div>
