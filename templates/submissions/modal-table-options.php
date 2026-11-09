<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
/* WPP_TO_THEME: every colour, font, size and radius comes from Theme settings (fallbacks = previous neutral look) */
#wppoppop-table-options-modal{display:none;position:fixed;top:0;right:0;bottom:0;left:0;background:var(--wpp-c-modal-backdrop,rgba(15,23,42,.7));z-index:100000;align-items:center;justify-content:center}
#wppoppop-table-options-modal.is-open{display:-webkit-flex;display:flex}
#wppoppop-table-options-modal .wto-panel{position:relative;display:-webkit-flex;display:flex;-webkit-flex-direction:column;flex-direction:column;width:640px;max-width:94vw;max-height:92vh;overflow:hidden;box-sizing:border-box;background:var(--wpp-c-modal-bg,#fff);color:var(--wpp-c-text,#1d2327);font-family:var(--wpp-font-base,inherit);font-size:var(--wpp-fs-base,13px);line-height:var(--wpp-lh-base,1.5);border-radius:var(--wpp-r-card,3px);box-shadow:var(--wpp-shadow-modal,0 20px 25px -5px rgba(0,0,0,.25))}
#wppoppop-table-options-modal .wto-head{display:-webkit-flex;display:flex;align-items:center;justify-content:space-between;gap:12px;flex:none;padding:12px 16px 12px 20px;background:var(--wpp-c-modal-head-bg,#0f172a);color:var(--wpp-c-modal-head-text,#fff)}
body.wppoppop-admin-themed #wppoppop-table-options-modal .wto-head h2{display:-webkit-flex;display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin:0;padding:0;color:var(--wpp-c-modal-head-text,#fff) !important;font-family:var(--wpp-font-heading,inherit);font-size:var(--wpp-fs-modal-title,14px);font-weight:var(--wpp-fw-heading,600);line-height:1.3}
body.wppoppop-admin-themed #wppoppop-table-options-modal .wto-head h2 .dashicons{color:var(--wpp-c-modal-head-text,#fff) !important;width:18px;height:18px;font-size:18px}
body.wppoppop-admin-themed #wppoppop-table-options-modal .wto-head h2 small{color:var(--wpp-c-modal-head-text,#fff);font-size:var(--wpp-fs-small,12px);font-weight:400;opacity:.75}
#wppoppop-table-options-modal .wto-status{font-size:var(--wpp-fs-small,12px);font-weight:400;opacity:.85}
#wppoppop-table-options-modal .wto-status.is-err{opacity:1;padding:1px 8px;border-radius:var(--wpp-r-badge,12px);background:var(--wpp-c-danger,#b32d2e);color:var(--wpp-c-danger-text,#fff)}
#wppoppop-table-options-modal .wto-side{display:-webkit-flex;display:flex;gap:6px;flex:none}
#wppoppop-table-options-modal .wto-side button{display:-webkit-inline-flex;display:inline-flex;align-items:center;justify-content:center;width:var(--wpp-h-button,34px);height:var(--wpp-h-button,34px);margin:0;padding:0;box-sizing:border-box;border:var(--wpp-bw,1px) solid transparent;border-radius:var(--wpp-r-button,3px);font-size:16px;line-height:1;cursor:pointer}
#wppoppop-table-options-modal .wto-foot{display:-webkit-flex;display:flex;justify-content:flex-end;flex:none;padding:12px 20px;border-top:var(--wpp-bw,1px) solid var(--wpp-c-border,#dcdcde)}
#wppoppop-table-options-modal .wto-foot .wto-save{display:-webkit-inline-flex;display:inline-flex;align-items:center;justify-content:center;height:var(--wpp-h-button,34px);margin:0;padding:0 20px;box-sizing:border-box;background:var(--wpp-c-primary,#c1272d);border:var(--wpp-bw,1px) solid var(--wpp-c-primary,#c1272d);color:var(--wpp-c-primary-text,#fff);font-family:inherit;font-size:var(--wpp-fs-button,13px);font-weight:var(--wpp-fw-button,600);line-height:1;border-radius:var(--wpp-r-button,3px);cursor:pointer}
#wppoppop-table-options-modal .wto-foot .wto-save:hover,#wppoppop-table-options-modal .wto-foot .wto-save:focus{background:var(--wpp-c-primary-hover,#a51f25);border-color:var(--wpp-c-primary-hover,#a51f25)}
#wppoppop-table-options-modal .wto-side .wto-close{background:transparent;color:var(--wpp-c-modal-close,#94a3b8)}
#wppoppop-table-options-modal .wto-side .wto-close:hover,#wppoppop-table-options-modal .wto-side .wto-close:focus{color:var(--wpp-c-modal-head-text,#fff);border-color:var(--wpp-c-modal-close,#94a3b8)}
#wppoppop-table-options-modal .wto-bodywrap{flex:1 1 auto;min-height:0;overflow:auto;padding:20px}
#wppoppop-table-options-modal .wto-row{display:-webkit-flex;display:flex;gap:20px;margin-bottom:20px;align-items:flex-start}
#wppoppop-table-options-modal .wto-row:last-child{margin-bottom:0}
#wppoppop-table-options-modal .wto-label{width:140px;flex:none;padding-top:8px;font-size:var(--wpp-fs-label,11px);font-weight:var(--wpp-fw-label,700);letter-spacing:var(--wpp-ls-label,.5px);text-transform:var(--wpp-tt-label,uppercase);color:var(--wpp-c-muted,#787c82)}
#wppoppop-table-options-modal .wto-body{flex:1;min-width:0}
#wppoppop-table-options-modal .wto-add-btn{display:-webkit-inline-flex;display:inline-flex;align-items:center;justify-content:center;height:var(--wpp-h-button,34px);padding:0 16px;box-sizing:border-box;background:var(--wpp-c-primary,#c1272d);border:var(--wpp-bw,1px) solid var(--wpp-c-primary,#c1272d);color:var(--wpp-c-primary-text,#fff);font-family:inherit;font-size:var(--wpp-fs-button,13px);font-weight:var(--wpp-fw-button,600);line-height:1;border-radius:var(--wpp-r-button,3px);cursor:pointer}
#wppoppop-table-options-modal .wto-add-btn:hover,#wppoppop-table-options-modal .wto-add-btn:focus{background:var(--wpp-c-primary-hover,#a51f25);border-color:var(--wpp-c-primary-hover,#a51f25)}
#wppoppop-table-options-modal #wto-labels{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 12px;margin:0 0 12px}
#wppoppop-table-options-modal #wto-labels:empty{display:none;margin:0}
#wppoppop-table-options-modal .wto-lbl{display:-webkit-flex;display:flex;align-items:center;gap:8px;min-width:0;margin:0;padding:6px 10px;box-sizing:border-box;color:#fff;font-size:var(--wpp-fs-label,11px);font-weight:600;border-radius:var(--wpp-r-button,3px)}
#wppoppop-table-options-modal .wto-lbl input.lbl-name[type="text"]{flex:1 1 auto;min-width:0;width:auto;height:24px !important;min-height:0 !important;margin:0;padding:2px 4px;background:transparent !important;border:0 !important;border-bottom:1px dashed rgba(255,255,255,.65) !important;border-radius:0 !important;box-shadow:none !important;outline:0;color:#fff !important;font-size:var(--wpp-fs-label,11px);font-weight:600}
#wppoppop-table-options-modal .wto-lbl input.lbl-name[type="text"]:focus{border-bottom:1px solid #fff !important;box-shadow:none !important}
#wppoppop-table-options-modal .wto-lbl input.lbl-name::-webkit-input-placeholder{color:rgba(255,255,255,.7)}
#wppoppop-table-options-modal .wto-lbl input.lbl-name::-moz-placeholder{color:rgba(255,255,255,.7);opacity:1}
#wppoppop-table-options-modal .wto-lbl input.lbl-name::placeholder{color:rgba(255,255,255,.7)}
#wppoppop-table-options-modal .lbl-color-wrap{position:relative;flex:0 0 auto;display:inline-block;width:20px;height:20px;cursor:pointer}
#wppoppop-table-options-modal .lbl-color-wrap .dashicons{width:20px;height:20px;font-size:18px;line-height:20px;color:#fff}
#wppoppop-table-options-modal .lbl-color-wrap input.lbl-color{position:absolute !important;left:0;top:0;width:100% !important;height:100% !important;min-height:0 !important;margin:0;padding:0 !important;border:0;opacity:0;cursor:pointer}
#wppoppop-table-options-modal .lbl-color-wrap:hover .dashicons,#wppoppop-table-options-modal .lbl-del:hover{opacity:.8}
#wppoppop-table-options-modal .lbl-del{flex:0 0 auto;width:20px;height:20px;font-size:16px;line-height:20px;color:#fff;cursor:pointer;text-decoration:none}
#wppoppop-table-options-modal .wto-cols{display:-webkit-flex;display:flex;border-top:var(--wpp-bw,1px) solid var(--wpp-c-border,#dcdcde);padding-top:10px}
#wppoppop-table-options-modal .wto-cols>div{flex:1;padding:0 8px;min-width:0}
#wppoppop-table-options-modal .wto-cols>div+div{border-left:var(--wpp-bw,1px) solid var(--wpp-c-border,#dcdcde)}
#wppoppop-table-options-modal .wto-cols h4{margin:6px 0 12px;text-align:center;font-size:var(--wpp-fs-label,11px);font-weight:var(--wpp-fw-label,700);letter-spacing:var(--wpp-ls-label,.5px);text-transform:var(--wpp-tt-label,uppercase);color:var(--wpp-c-heading,#1d2327)}
#wppoppop-table-options-modal .wppoppop-cols-list{list-style:none;margin:0;padding:0;min-height:60px;max-height:190px;overflow:auto}
#wppoppop-table-options-modal .wppoppop-cols-list li{margin:0 0 8px;padding:8px 6px;text-align:center;font-size:var(--wpp-fs-small,12px);background:var(--wpp-c-secondary-bg,#fff);color:var(--wpp-c-secondary-text,#1d2327);border:var(--wpp-bw,1px) solid var(--wpp-c-secondary-border,#c3c4c7);border-radius:var(--wpp-r-button,3px);cursor:move}
#wppoppop-table-options-modal .wppoppop-cols-list li:hover{background:var(--wpp-c-secondary-hover,#f1f5f9)}
#wppoppop-table-options-modal #wppoppop-cols-active li{font-weight:700;border-bottom:2px solid var(--wpp-c-primary,#c1272d)}
#wppoppop-table-options-modal #wppoppop-cols-inactive li{background:transparent;border:1px solid var(--wpp-c-secondary-bg,#c3c4c7);color:var(--wpp-c-muted,#787c82)}
.wto-ph{border:1px dashed var(--wpp-c-primary,#c1272d) !important;background:var(--wpp-c-row-stripe,#f6f7f7) !important;height:30px}
#wppoppop-table-options-modal .wto-help{margin:8px 0 0;font-size:var(--wpp-fs-small,12px);color:var(--wpp-c-muted,#787c82)}
#wppoppop-table-options-modal #wto-per-page{width:100%;height:var(--wpp-h-input,32px);font-size:var(--wpp-fs-input,12px)}
#wppoppop-table-options-modal input[type=text].wto-custom{width:calc(100% - 90px)}
@media (max-width:560px){#wppoppop-table-options-modal .wto-row{-webkit-flex-direction:column;flex-direction:column;gap:6px}#wppoppop-table-options-modal .wto-label{width:auto;padding-top:0}#wppoppop-table-options-modal #wto-labels{grid-template-columns:1fr}}
</style>
<div id="wppoppop-table-options-modal" data-config="<?php echo esc_attr(wp_json_encode($tbl_config_payload)); ?>">
    <div class="wto-panel" role="dialog" aria-modal="true" aria-labelledby="wto-title">
        <div class="wto-head">
            <h2 id="wto-title"><span class="dashicons dashicons-list-view"></span> <?php esc_html_e('Table options', 'wppoppop'); ?>
                <small>(<?php echo $sel_uid === '' ? esc_html__('All popups / default', 'wppoppop') : esc_html($sel_uid); ?>)</small>
            </h2>
            <div class="wto-side">
                <button type="button" class="wto-close" id="wto-close" title="<?php esc_attr_e('Close', 'wppoppop'); ?>">&#10005;</button>
            </div>
        </div>
        <div class="wto-bodywrap">

        <div class="wto-row">
            <div class="wto-label"><?php esc_html_e('Labels', 'wppoppop'); ?></div>
            <div class="wto-body">
                <div id="wto-labels"></div>
                <button type="button" class="wto-add-btn" id="wto-add-label"><?php esc_html_e('+ Add New', 'wppoppop'); ?></button>
            </div>
        </div>

        <div class="wto-row">
            <div class="wto-label"><?php esc_html_e('Column layout', 'wppoppop'); ?></div>
            <div class="wto-body">
                <div class="wto-cols">
                    <div>
                        <h4><?php esc_html_e('Active columns', 'wppoppop'); ?></h4>
                        <ul id="wppoppop-cols-active" class="wppoppop-cols-list"></ul>
                    </div>
                    <div>
                        <h4><?php esc_html_e('Inactive columns', 'wppoppop'); ?></h4>
                        <ul id="wppoppop-cols-inactive" class="wppoppop-cols-list"></ul>
                    </div>
                </div>
                <div style="margin-top:10px;">
                    <span class="wto-custom-wrap" hidden style="display:none !important;"><input type="text" class="wto-custom" id="wto-custom-key" placeholder="<?php esc_attr_e('Custom form field name (e.g. mobile_number)', 'wppoppop'); ?>"><button type="button" class="button" id="wto-add-custom"><?php esc_html_e('Add field', 'wppoppop'); ?></button></span>
                    <?php if ($sel_uid !== '') : ?>
                        
                        <p class="wto-help"><?php esc_html_e('Form fields on this popup sync live — new fields appear under Inactive columns without refreshing.', 'wppoppop'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="wto-row">
            <div class="wto-label"><?php esc_html_e('Entries per page', 'wppoppop'); ?></div>
            <div class="wto-body"><select id="wto-per-page"><option value="10">10</option><option value="20">20</option><option value="30">30</option></select></div>
        </div>
        </div>
        <div class="wto-foot">
            <button type="button" class="wto-save" id="wto-save"><?php esc_html_e('Save', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
