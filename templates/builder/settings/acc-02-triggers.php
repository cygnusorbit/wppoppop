<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 2. Display Triggers -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">2. Display Triggers</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <style id="wppoppop-trig-switch-css">
                        .wppoppop-trig-row{display:flex;align-items:center;justify-content:space-between;gap:10px;margin:8px 0 4px;font-size:13px;}
                        .wppoppop-sw{position:relative;display:inline-block;width:38px;height:22px;flex:0 0 38px;}
                        .wppoppop-sw input{position:absolute;opacity:0;width:100%;height:100%;margin:0;cursor:pointer;z-index:2;}
                        .wppoppop-sw .wppoppop-sw-track{position:absolute;top:0;right:0;bottom:0;left:0;background:#94a3b8;border-radius:22px;-webkit-transition:background .18s ease;transition:background .18s ease;}
                        .wppoppop-sw .wppoppop-sw-track:before{content:"";position:absolute;left:3px;top:3px;width:16px;height:16px;background:#fff;border-radius:50%;-webkit-transition:-webkit-transform .18s ease;transition:transform .18s ease;box-shadow:0 1px 2px rgba(0,0,0,.35);}
                        .wppoppop-sw input:checked + .wppoppop-sw-track{background:#2563eb;}
                        .wppoppop-sw input:checked + .wppoppop-sw-track:before{-webkit-transform:translateX(16px);transform:translateX(16px);}
                        .wppoppop-sw input:focus + .wppoppop-sw-track{box-shadow:0 0 0 2px rgba(37,99,235,.45);}
                        .wppoppop-trig-sub{margin:2px 0 8px 0;-webkit-transition:opacity .15s ease;transition:opacity .15s ease;}
                        .wppoppop-trig-sub.is-off{opacity:.5;}
                        .wppoppop-trig-sub label{font-size:10px;color:#64748b;display:block;text-transform:uppercase;}
                        .wppoppop-trig-sub input[type=number]{background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:4px 8px;border-radius:4px;font-size:12px;}
                        .wppoppop-trig-hr{border:0;border-top:1px solid #e2e8f0;margin:10px 0;}
                    </style>

                    <div class="wppoppop-trig-row"><span>On Page Load</span>
                        <label class="wppoppop-sw"><input type="checkbox" id="trig-load" value="1"><span class="wppoppop-sw-track"></span></label></div>
                    <div class="wppoppop-trig-sub" data-for="trig-load">
                        <label for="trig-load-delay">Delay (seconds)</label>
                        <input type="number" id="trig-load-delay" min="0" max="3600" step="1" value="0" placeholder="0" class="widefat">
                    </div>

                    <div class="wppoppop-trig-row"><span>Popup All the Time on Page Load <small style="opacity:.7;font-weight:400;">(All Pages)</small></span>
                        <label class="wppoppop-sw"><input type="checkbox" id="trig-always" value="1"><span class="wppoppop-sw-track"></span></label></div>
                    <div class="wppoppop-trig-sub" style="opacity:.8"><label style="text-transform:none;">Opens on every page load, even after the visitor closed it. Shows on every page of the site (subject to targeting rules).</label></div>

                    <div class="wppoppop-trig-row"><span>Popup All the Time on Page Load <small style="opacity:.7;font-weight:400;">(Embed Code Only)</small></span>
                        <label class="wppoppop-sw"><input type="checkbox" id="trig-always-embed" value="1"><span class="wppoppop-sw-track"></span></label></div>
                    <div class="wppoppop-trig-sub" style="opacity:.8"><label style="text-transform:none;">Opens on every page load, only on pages that contain this popup's shortcode or widget code. Not shown anywhere else.</label></div>

                    <div class="wppoppop-trig-row"><span>Appear Continuously Every X Hours</span>
                        <label class="wppoppop-sw"><input type="checkbox" id="trig-repeat" value="1"><span class="wppoppop-sw-track"></span></label></div>
                    <div class="wppoppop-trig-sub" data-for="trig-repeat">
                        <label for="trig-repeat-hours">Repeat every (hours)</label>
                        <input type="number" id="trig-repeat-hours" min="1" max="720" step="1" value="24" placeholder="24" class="widefat">
                    </div>

                    <hr class="wppoppop-trig-hr">

                    <div class="wppoppop-trig-row"><span>On Exit Intent</span>
                        <label class="wppoppop-sw"><input type="checkbox" id="trig-exit" value="1"><span class="wppoppop-sw-track"></span></label></div>
                    <div class="wppoppop-trig-sub" data-for="trig-exit">
                        <label for="set-exit-intent-sensitivity">Sensitivity Threshold (px top)</label>
                        <input type="number" id="set-exit-intent-sensitivity" class="widefat" min="1" max="200" step="1" value="20" placeholder="20">
                    </div>

                    <div class="wppoppop-trig-row"><span>On Scroll Depth (%)</span>
                        <label class="wppoppop-sw"><input type="checkbox" id="trig-scroll" value="1"><span class="wppoppop-sw-track"></span></label></div>
                    <div class="wppoppop-trig-sub" data-for="trig-scroll">
                        <label for="set-trigger-scroll-depth">Scroll Depth Trigger (%)</label>
                        <input type="number" id="set-trigger-scroll-depth" class="widefat" min="1" max="100" step="1" value="50" placeholder="50">
                        <input type="hidden" id="trig-scroll-val" value="50">
                    </div>

                    <div class="wppoppop-trig-row"><span>On AdBlock Detected</span>
                        <label class="wppoppop-sw"><input type="checkbox" id="trig-adblock" value="1"><span class="wppoppop-sw-track"></span></label></div>

                    <div class="wppoppop-trig-row"><span>On Mobile Back-Button</span>
                        <label class="wppoppop-sw"><input type="checkbox" id="trig-backbutton" value="1"><span class="wppoppop-sw-track"></span></label></div>
                </div>
            </div>
