<?php
namespace WPPopPop\Core;

use WPPopPop\Admin\SettingsManager;

class CelebrationManager {
    public static function init(): void {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_celebration_assets']);
    }

    public static function enqueue_celebration_assets(): void {
        $sound_enabled    = (bool) SettingsManager::get('sound_enabled', 1);
        $confetti_enabled = (bool) SettingsManager::get('confetti_enabled', 1);

        if (!$sound_enabled && !$confetti_enabled) {
            return;
        }

        wp_enqueue_script(
            'wppoppop-celebration',
            WPPOPPOP_URL . 'assets/js/celebration.js',
            [],
            WPPOPPOP_VERSION,
            true
        );

        wp_localize_script('wppoppop-celebration', 'WPPopPopCelebrationConfig', [
            'soundEnabled'    => $sound_enabled,
            'confettiEnabled' => $confetti_enabled,
            'openSound'       => SettingsManager::get('sound_open_preset', 'pop'),
            'successSound'    => SettingsManager::get('sound_success_preset', 'fanfare'),
            'soundVolume'     => (float) (SettingsManager::get('sound_volume', 50) / 100),
        ]);
    }
}
