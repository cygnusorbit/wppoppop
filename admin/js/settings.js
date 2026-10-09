/**
 * WpPopPop Settings Master Coordinator
 * Initializes Decomposed Sub-Controllers on DOM Ready
 */
jQuery(document).ready(function($) {
    'use strict';

    if (window.WpPopPopSettingsTabs) {
        window.WpPopPopSettingsTabs.init();
    }
    if (window.WpPopPopSettingsSave) {
        window.WpPopPopSettingsSave.init();
    }
    if (window.WpPopPopSettingsTools) {
        window.WpPopPopSettingsTools.init();
    }
});
