/**
 * WpPopPop Visual Builder Master Bootstrap Coordinator
 * Initializes Decomposed Sub-Controllers with Canvas Navigation
 */
jQuery(document).ready(function($) {
    'use strict';

    if (window.WpPopPopBuilderCore) window.WpPopPopBuilderCore.init();
    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.init();
    if (window.WpPopPopBuilderLayers) window.WpPopPopBuilderLayers.init();
    if (window.WpPopPopBuilderInspector) window.WpPopPopBuilderInspector.init();
    if (window.WpPopPopBuilderSettings) window.WpPopPopBuilderSettings.init();
    if (window.WpPopPopBuilderModals) window.WpPopPopBuilderModals.init();
    if (window.WpPopPopBuilderIO) window.WpPopPopBuilderIO.init();
});
