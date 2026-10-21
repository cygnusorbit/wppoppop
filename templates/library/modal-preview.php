<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
#wppoppop-lib-preview-modal{display:none;position:fixed;inset:0;background:rgba(15,23,42,.75);z-index:99999;align-items:center;justify-content:center}
#wppoppop-lib-preview-modal .lp-dlg{background:#fff;color:#1e293b;border-radius:10px;width:960px;max-width:96vw;max-height:94vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,.5)}
#wppoppop-lib-preview-modal .lp-hd{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 18px;background:#0f172a;color:#fff}
#wppoppop-lib-preview-modal .lp-hd h3{margin:0;font-size:15px;color:#fff}
#wppoppop-lib-preview-modal .lp-x{background:transparent;border:0;color:#94a3b8;font-size:24px;cursor:pointer;line-height:1}
#wppoppop-lib-preview-modal .lp-bar{display:flex;align-items:center;gap:8px;padding:8px 18px;border-bottom:1px solid #e2e8f0;font-size:12px;color:#475569}
#wppoppop-lib-preview-modal .lp-bar .lp-sp{flex:1}
#wppoppop-lib-preview-modal .lp-bar .button.is-on{background:#2563eb;border-color:#1d4ed8;color:#fff}
#wppoppop-lib-preview-modal .lp-stage{position:relative;flex:1;min-height:460px;background:#f1f5f9;display:flex;justify-content:center;overflow:auto}
#wppoppop-lib-preview-modal .lp-frame{border:0;background:#fff;width:100%;height:560px;max-width:100%;transition:width .2s ease}
#wppoppop-lib-preview-modal .lp-frame.is-mobile{width:390px}
#wppoppop-lib-preview-modal .lp-mock-wrap{padding:24px;display:flex;align-items:flex-start;justify-content:center;width:100%}
#wppoppop-lib-preview-modal .lp-mock{position:relative;background:#fff;box-shadow:0 10px 30px rgba(0,0,0,.2);border-radius:6px;overflow:hidden;transform-origin:top center}
#wppoppop-lib-preview-modal .lp-mock>div{position:absolute;box-sizing:border-box;overflow:hidden;font:12px/1.3 sans-serif;display:flex;align-items:center;justify-content:center;text-align:center;border:1px dashed #94a3b8;background:rgba(148,163,184,.12);color:#334155}
#wppoppop-lib-preview-modal .lp-msg{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:13px;color:#64748b;background:rgba(241,245,249,.9)}
#wppoppop-lib-preview-modal .lp-ft{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 18px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;color:#64748b}
</style>
<div class="wppoppop-modal-backdrop" id="wppoppop-lib-preview-modal" role="dialog" aria-modal="true" aria-labelledby="wppoppop-lp-title">
    <div class="lp-dlg">
        <div class="lp-hd">
            <h3 id="wppoppop-lp-title"><?php esc_html_e('Template Preview', 'wppoppop'); ?></h3>
            <button type="button" class="lp-x" id="wppoppop-lp-close" aria-label="<?php esc_attr_e('Close', 'wppoppop'); ?>">&times;</button>
        </div>
        <div class="lp-bar">
            <button type="button" class="button button-small is-on" data-lp-view="desktop"><?php esc_html_e('Desktop', 'wppoppop'); ?></button>
            <button type="button" class="button button-small" data-lp-view="mobile"><?php esc_html_e('Mobile', 'wppoppop'); ?></button>
            <span class="lp-sp"></span>
            <button type="button" class="button button-small" id="wppoppop-lp-replay"><?php esc_html_e('Replay', 'wppoppop'); ?></button>
        </div>
        <div class="lp-stage" id="wppoppop-lp-stage"></div>
        <div class="lp-ft">
            <span id="wppoppop-lp-meta"></span>
            <span>
                <button type="button" class="button" id="wppoppop-lp-cancel"><?php esc_html_e('Close', 'wppoppop'); ?></button>
                <button type="button" class="button button-primary" id="wppoppop-lp-use" style="display:none;background:#00a32a;border-color:#008a20;"><?php esc_html_e('Use Template', 'wppoppop'); ?></button>
            </span>
        </div>
    </div>
</div>
