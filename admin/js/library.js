/**
 * WpPopPop Popups Library: Master Bootstrap Coordinator
 * Cross-Browser Compatibility: Safari, Firefox, Chrome, Edge
 */
jQuery(document).ready(function($) {
    'use strict';

    if (window.WpPopPopLibraryFilter) window.WpPopPopLibraryFilter.init();
    if (window.WpPopPopLibraryPreview) window.WpPopPopLibraryPreview.init();
    if (window.WpPopPopLibraryImport) window.WpPopPopLibraryImport.init();
});
