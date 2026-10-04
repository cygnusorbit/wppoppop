<?php
namespace WPPopPop\Triggers;

use WPPopPop\Core\LayerRenderer;
use WPPopPop\Targeting\TargetingManager;

class TriggerManager {
    public function init(): void {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'render_modal_markup']);
        add_filter('the_content', [$this, 'inject_inline_popups']);
    }

    public function enqueue_assets(): void {
        $events = ['onload', 'onscroll', 'onexit', 'oninactivity'];
        $active_event_popups = [];

        foreach ($events as $event) {
            $ids = TargetingManager::get_active_popups_for_event($event);
            if (!empty($ids)) {
                $active_event_popups[$event] = $ids[0];
            }
        }

        $fallback_id = 0;
        if (empty($active_event_popups)) {
            $latest = get_posts([
                'post_type'      => 'wppoppop',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'orderby'        => 'date',
                'order'          => 'DESC'
            ]);
            if (!empty($latest)) {
                $fallback_id = $latest[0]->ID;
                $active_event_popups['onload'] = $fallback_id;
            }
        }

        if (empty($active_event_popups) && !$fallback_id) {
            return;
        }

        wp_enqueue_style('wppoppop-frontend', WPPOPPOP_URL . 'assets/css/wppoppop.css', [], WPPOPPOP_VERSION);
        wp_enqueue_script('wppoppop-triggers', WPPOPPOP_URL . 'assets/js/triggers.js', [], WPPOPPOP_VERSION, true);

        $primary_id = $active_event_popups['onload'] ?? ($fallback_id ?: reset($active_event_popups));

        wp_localize_script('wppoppop-triggers', 'WPPopPopConfig', [
            'popupId'       => $primary_id,
            'eventMap'      => $active_event_popups,
            'exitIntent'    => isset($active_event_popups['onexit']),
            'scrollPercent' => isset($active_event_popups['onscroll']) ? 40 : 0,
            'inactivitySec' => isset($active_event_popups['oninactivity']) ? 15 : 0,
            'isLocker'        => (bool) get_post_meta($primary_id, '_wppoppop_is_locker', true),
            'unlockDuration'  => (int) (get_post_meta($primary_id, '_wppoppop_unlock_duration', true) ?: 30),
            'adblockDetector' => (bool) \WPPopPop\Admin\SettingsManager::get('adblock_detector', 0),
            'restUrl'       => rest_url('wppoppop/v1/submit'),
            'impressionUrl' => rest_url('wppoppop/v1/impression'),
            'restNonce'     => wp_create_nonce('wp_rest'),
        ]);
    }

    public function render_modal_markup(): void {
        $events = ['onload', 'onscroll', 'onexit', 'oninactivity'];
        $rendered = [];

        foreach ($events as $e) {
            $ids = TargetingManager::get_active_popups_for_event($e);
            foreach ($ids as $id) {
                if (isset($rendered[$id])) continue;
                $rendered[$id] = true;
                ?>
                <div id="wppoppop-modal-<?php echo esc_attr($id); ?>" class="wppoppop-overlay wppoppop-modal-element" data-popup-id="<?php echo esc_attr($id); ?>" aria-hidden="true" style="display:none;">
                    <div class="wppoppop-container" role="dialog" aria-modal="true">
                        <button type="button" class="wppoppop-close wppoppop-modal-close" aria-label="Close">&times;</button>
                        <div class="wppoppop-content">
                            <?php echo LayerRenderer::render_layers($id, ''); ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
    }

    public function inject_inline_popups(string $content): string {
        if (!is_singular()) {
            return $content;
        }

        $start_ids = TargetingManager::get_active_popups_for_event('contentstart');
        $end_ids   = TargetingManager::get_active_popups_for_event('contentend');

        $before = '';
        foreach ($start_ids as $id) {
            $before .= '<div class="wppoppop-inline-wrap wppoppop-inline-start" data-popup-id="' . esc_attr($id) . '">' . LayerRenderer::render_layers($id) . '</div>';
        }

        $after = '';
        foreach ($end_ids as $id) {
            $after .= '<div class="wppoppop-inline-wrap wppoppop-inline-end" data-popup-id="' . esc_attr($id) . '">' . LayerRenderer::render_layers($id) . '</div>';
        }

        return $before . $content . $after;
    }
}
