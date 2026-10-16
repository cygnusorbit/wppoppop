<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 2. Display Triggers -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">2. Display Triggers</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="trig-load" value="1"> On Page Load</label>
                    <input type="number" id="trig-load-delay" placeholder="Delay (seconds)" class="widefat" style="margin:4px 0 8px 0;">
                    <label><input type="checkbox" id="trig-exit" value="1"> On Exit Intent</label>
                    <div style="margin:4px 0 8px 20px;">
                        <label style="font-size:10px;color:#64748b;display:block;">Sensitivity Threshold (px top):</label>
                        <input type="number" id="set-exit-intent-sensitivity" class="widefat" min="5" max="100" step="5" value="20" placeholder="20" style="background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:4px 8px;border-radius:4px;font-size:12px;">
                    </div><br>
                    <label><input type="checkbox" id="trig-scroll" value="1"> On Scroll Depth (%)</label>
                    <div style="margin:4px 0 8px 20px;">
                        <label style="font-size:10px;color:#64748b;display:block;">Scroll Depth Trigger (%):</label>
                        <input type="number" id="set-trigger-scroll-depth" class="widefat" min="1" max="100" step="5" value="50" placeholder="50" style="background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:4px 8px;border-radius:4px;font-size:12px;">
                    </div>
                    <input type="number" id="trig-scroll-val" placeholder="Scroll % (e.g. 50)" class="widefat" style="margin:4px 0 8px 0;">
                    <label><input type="checkbox" id="trig-adblock" value="1"> On AdBlock Detected</label><br>
                    <label><input type="checkbox" id="trig-backbutton" value="1"> On Mobile Back-Button</label>
                </div>
            </div>
