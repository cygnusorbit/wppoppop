<?php
if (!defined('ABSPATH')) {
    exit;
}
$wppoppop_pp_cells = array(
    'top-left'      => array(__('Top left', 'wppoppop'), 6, 6),
    'top-center'    => array(__('Top center', 'wppoppop'), 32, 6),
    'top-right'     => array(__('Top right', 'wppoppop'), 58, 6),
    'middle-left'   => array(__('Middle left', 'wppoppop'), 6, 23),
    'center'        => array(__('Center', 'wppoppop'), 32, 23),
    'middle-right'  => array(__('Middle right', 'wppoppop'), 58, 23),
    'bottom-left'   => array(__('Bottom left', 'wppoppop'), 6, 40),
    'bottom-center' => array(__('Bottom center', 'wppoppop'), 32, 40),
    'bottom-right'  => array(__('Bottom right', 'wppoppop'), 58, 40),
);
?>
<!-- WPPOPPOP_POPUP_POSITION_UI: 21. Popup Positions -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Popup Positions', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <style>
                        .wppoppop-pp-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin:6px 0 8px}
                        .wppoppop-pp-cell{display:block;margin:0;padding:0;border:0;background:transparent;cursor:pointer;line-height:0;border-radius:8px;-webkit-appearance:none;appearance:none}
                        .wppoppop-pp-cell svg{display:block;width:100%;height:auto;overflow:visible}
                        .wppoppop-pp-cell .pp-screen{fill:#fff;stroke:#cbd5e1;stroke-width:1.5;transition:stroke .12s,fill .12s}
                        .wppoppop-pp-cell .pp-box{fill:#f8fafc;stroke:#cbd5e1;stroke-width:1.5;transition:stroke .12s,fill .12s}
                        .wppoppop-pp-cell:hover .pp-screen,.wppoppop-pp-cell:focus .pp-screen{stroke:#e5a4bb;fill:#fff7fa}
                        .wppoppop-pp-cell:hover .pp-box,.wppoppop-pp-cell:focus .pp-box{stroke:#be4a6f;fill:#fde8ef}
                        .wppoppop-pp-cell.is-selected .pp-screen{stroke:#b4234f;fill:#fff}
                        .wppoppop-pp-cell.is-selected .pp-box{stroke:#b4234f;fill:#fde8ef}
                        .wppoppop-pp-cell:focus{outline:none}
                        .wppoppop-pp-cell:focus-visible{outline:2px solid #2271b1;outline-offset:2px}
                        .wppoppop-pp-name{font-size:12px;font-weight:600;color:#334155;margin:0 0 12px;min-height:16px}
                        .wppoppop-pp-pads{display:grid;grid-template-columns:1fr 1fr;gap:8px 10px;margin:4px 0 8px}
                        .wppoppop-pp-pads label{display:block;font-size:11px;font-weight:600;color:#475569;margin:0 0 2px}
                        .wppoppop-pp-pad{display:flex;align-items:center;gap:4px}
                        .wppoppop-pp-pad input{width:100%;min-width:0}
                        .wppoppop-pp-pad span{font-size:11px;color:#64748b}
                    </style>

                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;"><?php esc_html_e('Popup position', 'wppoppop'); ?></label>
                    <div id="wppoppop-pp-grid" class="wppoppop-pp-grid" role="radiogroup" aria-label="<?php esc_attr_e('Popup position', 'wppoppop'); ?>">
                        <?php foreach ($wppoppop_pp_cells as $wppoppop_pp_slug => $wppoppop_pp_c) : ?>
                            <button type="button" class="wppoppop-pp-cell" role="radio" aria-checked="false" data-pos="<?php echo esc_attr($wppoppop_pp_slug); ?>" data-label="<?php echo esc_attr($wppoppop_pp_c[0]); ?>" title="<?php echo esc_attr($wppoppop_pp_c[0]); ?>">
                                <svg viewBox="0 0 100 70" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                    <rect class="pp-screen" x="1" y="1" width="98" height="68" rx="6" ry="6"/>
                                    <rect class="pp-box" x="<?php echo (int) $wppoppop_pp_c[1]; ?>" y="<?php echo (int) $wppoppop_pp_c[2]; ?>" width="36" height="24" rx="4" ry="4"/>
                                </svg>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <p id="wppoppop-pp-name" class="wppoppop-pp-name" aria-live="polite"></p>

                    <div style="border-top:1px solid #e2e8f0;padding-top:12px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <strong style="font-size:11px;color:#475569;text-transform:uppercase;"><?php esc_html_e('Screen edge padding', 'wppoppop'); ?></strong>
                            <button type="button" id="set-pp-edge" class="button button-secondary" style="font-size:11px;"><?php esc_html_e('Stick to edge', 'wppoppop'); ?></button>
                        </div>
                        <div class="wppoppop-pp-pads">
                            <div><label for="set-pp-top"><?php esc_html_e('Top', 'wppoppop'); ?></label><div class="wppoppop-pp-pad"><input type="number" id="set-pp-top" class="widefat" min="0" max="1000" step="1" value="0"><span>px</span></div></div>
                            <div><label for="set-pp-bottom"><?php esc_html_e('Bottom', 'wppoppop'); ?></label><div class="wppoppop-pp-pad"><input type="number" id="set-pp-bottom" class="widefat" min="0" max="1000" step="1" value="0"><span>px</span></div></div>
                            <div><label for="set-pp-left"><?php esc_html_e('Left', 'wppoppop'); ?></label><div class="wppoppop-pp-pad"><input type="number" id="set-pp-left" class="widefat" min="0" max="1000" step="1" value="0"><span>px</span></div></div>
                            <div><label for="set-pp-right"><?php esc_html_e('Right', 'wppoppop'); ?></label><div class="wppoppop-pp-pad"><input type="number" id="set-pp-right" class="widefat" min="0" max="1000" step="1" value="0"><span>px</span></div></div>
                        </div>
                        <p style="font-size:11px;color:#64748b;margin:0;"><?php esc_html_e('Distance kept between the popup and each screen edge. 0 sticks the popup to the edge; a higher value moves it away. On screens smaller than the popup it scales down to fit.', 'wppoppop'); ?></p>
                    </div>
                </div>
            </div>
