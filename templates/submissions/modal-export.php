<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style id="wppoppop-export-modal-css">
#wppoppop-export-modal{display:none;position:fixed;top:0;right:0;bottom:0;left:0;z-index:100050;background:rgba(15,23,42,.6);align-items:center;justify-content:center;}
#wppoppop-export-modal.is-open{display:-webkit-flex;display:flex;}
#wppoppop-export-modal .wex-box{background:#fff;width:460px;max-width:94vw;max-height:86vh;border-radius:10px;box-shadow:0 20px 50px rgba(0,0,0,.35);display:-webkit-flex;display:flex;-webkit-flex-direction:column;flex-direction:column;overflow:hidden;}
#wppoppop-export-modal .wex-head{padding:16px 20px;border-bottom:1px solid #e2e8f0;}
#wppoppop-export-modal .wex-head h2{margin:0;font-size:16px;}
#wppoppop-export-modal .wex-sub{margin:4px 0 0;color:#64748b;font-size:12px;}
#wppoppop-export-modal .wex-tools{padding:8px 20px;border-bottom:1px solid #f1f5f9;display:-webkit-flex;display:flex;gap:8px;}
#wppoppop-export-modal .wex-body{padding:8px 20px;overflow:auto;-webkit-flex:1 1 auto;flex:1 1 auto;min-height:120px;}
#wppoppop-export-modal .wex-group{margin:10px 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;color:#64748b;}
#wppoppop-export-modal .wex-item{display:block;padding:4px 0;font-size:13px;cursor:pointer;}
#wppoppop-export-modal .wex-item input{margin-right:8px;}
#wppoppop-export-modal .wex-foot{padding:12px 20px;border-top:1px solid #e2e8f0;display:-webkit-flex;display:flex;justify-content:flex-end;gap:8px;}
#wppoppop-export-modal .wex-msg{color:#64748b;font-size:13px;padding:14px 0;}
</style>
<div id="wppoppop-export-modal" role="dialog" aria-modal="true" aria-labelledby="wex-title">
    <div class="wex-box">
        <div class="wex-head">
            <h2 id="wex-title"><?php echo esc_html__('Export to CSV', 'wppoppop'); ?></h2>
            <p class="wex-sub" id="wex-sub"></p>
        </div>
        <div class="wex-tools">
            <button type="button" class="button button-small" id="wex-all"><?php echo esc_html__('Select all', 'wppoppop'); ?></button>
            <button type="button" class="button button-small" id="wex-none"><?php echo esc_html__('Select none', 'wppoppop'); ?></button>
        </div>
        <div class="wex-body" id="wex-body"></div>
        <div class="wex-foot">
            <button type="button" class="button" id="wex-cancel"><?php echo esc_html__('Cancel', 'wppoppop'); ?></button>
            <button type="button" class="button button-primary" id="wex-go"><?php echo esc_html__('Export', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
