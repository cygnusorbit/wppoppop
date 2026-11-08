<?php
if (!defined('ABSPATH')) {
    exit;
}
$wpp_sd_status = WpPopPop_Saved_Defaults::status();
$wpp_sd_rows   = [
    'settings' => [
        'title' => __('Settings defaults', 'wppoppop'),
        'desc'  => __('Used by "Reset Defaults" on the Settings page and for fresh installs. Emails, keys/secrets and custom code are never saved.', 'wppoppop'),
        'save'  => true,
    ],
    'theme'    => [
        'title' => __('Theme defaults', 'wppoppop'),
        'desc'  => __('Used by Revert/Reset on the Theme page and for fresh installs. The top-bar logo is not saved.', 'wppoppop'),
        'save'  => true,
    ],
    'popup'    => [
        'title' => __('New popup defaults', 'wppoppop'),
        'desc'  => __('Canvas size, background, animation and popup settings applied to every new popup (layers are not included). Save them from the builder: Settings drawer, "Save as default for new popups".', 'wppoppop'),
        'save'  => false,
    ],
];
?>
<div class="wppoppop-card-box wpp-tools-card" id="wppoppop-saved-defaults">
    <div class="wpp-tools-card-head">
        <span class="dashicons dashicons-star-filled"></span>
        <h3><?php esc_html_e('Saved Defaults', 'wppoppop'); ?></h3>
    </div>
    <p class="wpp-tools-help"><?php esc_html_e('Turn your current setup into the real defaults. They are stored on this site and in the plugin "defaults" folder, so they travel with the plugin.', 'wppoppop'); ?></p>

    <div class="wpp-tools-grid wpp-tools-grid-3">
        <?php foreach ($wpp_sd_rows as $wpp_sd_scope => $wpp_sd_row) :
            $wpp_sd_st = $wpp_sd_status[$wpp_sd_scope]; ?>
            <div class="wpp-tools-panel">
                <h4><?php echo esc_html($wpp_sd_row['title']); ?></h4>
                <p class="wpp-tools-help"><?php echo esc_html($wpp_sd_row['desc']); ?></p>
                <p class="wpp-tools-help"><strong><?php esc_html_e('Status:', 'wppoppop'); ?></strong>
                    <?php if ($wpp_sd_st['saved']) :
                        $wpp_sd_when = WpPopPop_Saved_Defaults::format_time($wpp_sd_st['time']);
                        echo esc_html(sprintf(
                            /* translators: 1: date/time, 2: source (site or plugin file) */
                            __('Saved %1$s (%2$s)', 'wppoppop'),
                            $wpp_sd_when !== '' ? $wpp_sd_when : __('earlier', 'wppoppop'),
                            $wpp_sd_st['source'] === 'file' ? __('plugin file', 'wppoppop') : __('this site', 'wppoppop')
                        ));
                    else :
                        esc_html_e('Built-in defaults', 'wppoppop');
                    endif; ?>
                </p>
                <div class="wpp-tools-row">
                    <?php if ($wpp_sd_row['save']) : ?>
                        <button type="button" class="button" data-wpp-defaults-save="<?php echo esc_attr($wpp_sd_scope); ?>" data-wpp-defaults-reload="1"><?php esc_html_e('Save current as default', 'wppoppop'); ?></button>
                    <?php endif; ?>
                    <button type="button" class="button" data-wpp-defaults-clear="<?php echo esc_attr($wpp_sd_scope); ?>" data-wpp-defaults-reload="1" <?php disabled(!$wpp_sd_st['saved']); ?>><?php esc_html_e('Clear', 'wppoppop'); ?></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!WpPopPop_Saved_Defaults::file_writable()) : ?>
        <p class="wpp-tools-help" style="margin-top:12px;"><?php esc_html_e('The plugin "defaults" folder is not writable: defaults are stored on this site only.', 'wppoppop'); ?></p>
    <?php endif; ?>
</div>
