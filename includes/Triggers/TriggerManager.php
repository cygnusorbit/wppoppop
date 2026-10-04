<?php
namespace WPPopPop\Triggers;

use WPPopPop\Targeting\RuleEvaluator;
use WPPopPop\Core\LayerRenderer;
use WPPopPop\Core\SocialProofManager;

class TriggerManager {
    private RuleEvaluator $evaluator;
    private ?\WP_Post $active_popup = null;
    private bool $popup_resolved = false;

    public function __construct() {
        $this->evaluator = new RuleEvaluator();
    }

    public function init(): void {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'render_popup_markup']);
        add_filter('the_content', [$this, 'inject_inline_popup']);
    }

    private function resolve_active_popup(): ?\WP_Post {
        if (!$this->popup_resolved) {
            $this->active_popup = $this->evaluator->get_active_popup();
            $this->popup_resolved = true;
        }
        return $this->active_popup;
    }

    public function enqueue_assets(): void {
        $popup = $this->resolve_active_popup();
        if (!$popup) {
            return;
        }

        $exit_intent  = get_post_meta($popup->ID, '_wppoppop_exit_intent', true) !== '0';
        $scroll_depth = (int)(get_post_meta($popup->ID, '_wppoppop_scroll_depth', true) ?: 50);
        $inactivity   = (int)(get_post_meta($popup->ID, '_wppoppop_inactivity', true) ?: 10);
        $adblock      = get_post_meta($popup->ID, '_wppoppop_adblock', true) === '1';
        $optin_locker = get_post_meta($popup->ID, '_wppoppop_optin_locker', true) === '1';
        $ga_tracking  = get_post_meta($popup->ID, '_wppoppop_ga_tracking', true) === '1';
        $js_on_open   = get_post_meta($popup->ID, '_wppoppop_js_on_open', true) ?: '';
        $js_on_submit = get_post_meta($popup->ID, '_wppoppop_js_on_submit', true) ?: '';
        $js_on_close  = get_post_meta($popup->ID, '_wppoppop_js_on_close', true) ?: '';

        $freq_limit   = (int) get_post_meta($popup->ID, '_wppoppop_freq_limit', true);
        $prepopulate  = get_post_meta($popup->ID, '_wppoppop_prepopulate', true) !== '0';

        $pay_enabled  = get_post_meta($popup->ID, '_wppoppop_payment_enabled', true) === '1';
        $pay_amount   = (float)(get_post_meta($popup->ID, '_wppoppop_payment_amount', true) ?: 19.99);
        $pay_currency = (string)(get_post_meta($popup->ID, '_wppoppop_payment_currency', true) ?: 'usd');
        $display_mode = (string)(get_post_meta($popup->ID, '_wppoppop_display_mode', true) ?: 'modal');

        // Phase 26: Tab Switch, Back Button & Sound Settings
        $tab_switch   = get_post_meta($popup->ID, '_wppoppop_tab_switch_enabled', true) === '1';
        $tab_flash    = (string)(get_post_meta($popup->ID, '_wppoppop_tab_title_flash', true) ?: '');
        $back_button  = get_post_meta($popup->ID, '_wppoppop_back_button_enabled', true) === '1';
        $sound_fx     = get_post_meta($popup->ID, '_wppoppop_sound_fx_enabled', true) === '1';

        $sp_enabled  = get_post_meta($popup->ID, '_wppoppop_sp_enabled', true) === '1';
        $sp_interval = (int)(get_post_meta($popup->ID, '_wppoppop_sp_interval', true) ?: 8);
        $sp_duration = (int)(get_post_meta($popup->ID, '_wppoppop_sp_duration', true) ?: 5);
        $sp_position = (string)(get_post_meta($popup->ID, '_wppoppop_sp_position', true) ?: 'bottom-left');
        $sp_feed     = $sp_enabled ? SocialProofManager::get_activity_feed($popup->ID) : [];

        wp_enqueue_style(
            'wppoppop-frontend',
            WPPOPPOP_URL . 'assets/css/wppoppop.css',
            [],
            WPPOPPOP_VERSION
        );

        wp_enqueue_script(
            'wppoppop-triggers',
            WPPOPPOP_URL . 'assets/js/triggers.js',
            [],
            WPPOPPOP_VERSION,
            true
        );

        wp_localize_script('wppoppop-triggers', 'WPPopPopConfig', [
            'exitIntent'      => $exit_intent,
            'scrollPercent'   => $scroll_depth,
            'inactivitySec'   => $inactivity,
            'adblock'         => $adblock,
            'optinLocker'     => $optin_locker,
            'loadDelayMs'     => 0,
            'popupId'         => $popup->ID,
            'popupTitle'      => $popup->post_title,
            'displayMode'     => $display_mode,
            'restUrl'         => esc_url_raw(rest_url('wppoppop/v1/submit')),
            'paymentUrl'      => esc_url_raw(rest_url('wppoppop/v1/create-payment')),
            'impressionUrl'   => esc_url_raw(rest_url('wppoppop/v1/impression')),
            'restNonce'       => wp_create_nonce('wp_rest'),
            'gaTracking'      => $ga_tracking,
            'jsOnOpen'        => $js_on_open,
            'jsOnSubmit'      => $js_on_submit,
            'jsOnClose'       => $js_on_close,
            'freqLimit'       => $freq_limit,
            'prepopulate'     => $prepopulate,
            'paymentEnabled'  => $pay_enabled,
            'paymentAmount'   => $pay_amount,
            'paymentCurrency' => $pay_currency,
            'tabSwitch'       => $tab_switch,
            'tabTitleFlash'   => $tab_flash,
            'backButton'      => $back_button,
            'soundFx'         => $sound_fx,
            'socialProof'     => [
                'enabled'     => $sp_enabled,
                'feed'        => $sp_feed,
                'intervalSec' => $sp_interval,
                'durationSec' => $sp_duration,
                'position'    => $sp_position,
            ],
        ]);
    }

    public function inject_inline_popup(string $content): string {
        if (is_admin() || !is_singular() || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        $popup = $this->resolve_active_popup();
        if (!$popup) {
            return $content;
        }

        $mode = get_post_meta($popup->ID, '_wppoppop_inline_mode', true) ?: 'none';
        if ($mode === 'none') {
            return $content;
        }

        $inline_markup = '<div class="wppoppop-inline-container">' . 
                         LayerRenderer::render_layers($popup->ID, apply_filters('the_content', $popup->post_content)) . 
                         '</div>';

        if ($mode === 'content_start') {
            return $inline_markup . $content;
        } elseif ($mode === 'content_end') {
            return $content . $inline_markup;
        }

        return $content;
    }

    public function render_popup_markup(): void {
        $popup = $this->resolve_active_popup();
        if (!$popup) {
            return;
        }

        $display_mode = get_post_meta($popup->ID, '_wppoppop_display_mode', true) ?: 'modal';
        $position     = get_post_meta($popup->ID, '_wppoppop_position', true) ?: 'center';
        $inline_mode  = get_post_meta($popup->ID, '_wppoppop_inline_mode', true) ?: 'none';
        $optin_locker = get_post_meta($popup->ID, '_wppoppop_optin_locker', true) === '1';

        if ($inline_mode !== 'none') {
            return;
        }

        $overlay_classes = 'wppoppop-overlay wppoppop-mode-' . esc_attr($display_mode);
        if ($display_mode === 'modal') {
            $overlay_classes .= ' wppoppop-pos-' . esc_attr($position);
        }
        if ($optin_locker) {
            $overlay_classes .= ' wppoppop-locker-active';
        }
        ?>
        <div id="wppoppop-modal" class="<?php echo esc_attr($overlay_classes); ?>" aria-hidden="true">
            <div class="wppoppop-container" role="dialog" aria-modal="true">
                <?php if (!$optin_locker): ?>
                    <button type="button" class="wppoppop-close" id="wppoppop-close-btn" aria-label="Close popup">&times;</button>
                <?php endif; ?>
                <div class="wppoppop-content">
                    <?php echo LayerRenderer::render_layers($popup->ID, apply_filters('the_content', $popup->post_content)); ?>
                </div>
            </div>
        </div>

        <!-- Social Proof Toast Container -->
        <div id="wppoppop-sp-toast" class="wppoppop-sp-toast" style="display:none;" aria-live="polite">
            <div class="wppoppop-sp-icon" id="wppoppop-sp-icon">⚡</div>
            <div class="wppoppop-sp-content">
                <div class="wppoppop-sp-title" id="wppoppop-sp-title"></div>
                <div class="wppoppop-sp-meta" id="wppoppop-sp-meta"></div>
            </div>
            <button type="button" class="wppoppop-sp-close" id="wppoppop-sp-close" aria-label="Dismiss notification">&times;</button>
        </div>

        <!-- AdBlock Bait Probe -->
        <div class="adsbox pub_300x250 pub_728x90 text-ad textAd text_ad banner-ad" id="wppoppop-ad-bait" style="position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;" aria-hidden="true">&nbsp;</div>
        <?php
    }
}
