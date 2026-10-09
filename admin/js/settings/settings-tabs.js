/**
 * WpPopPop Settings: Tabs Navigation Controller
 */
(function(window, $) {
    'use strict';
    window.WpPopPopSettings = window.WpPopPopSettings || {};

    var Tabs = {
        init: function() {
            this.bindEvents();
            this.restoreActiveTab();
        },

        bindEvents: function() {
            var self = this;
            $('.wppoppop-tab-btn').on('click', function(e) {
                e.preventDefault();
                var targetTab = $(this).data('tab');
                self.switchTab(targetTab);
            });
        },

        switchTab: function(tabKey) {
            if (!tabKey) return;
            tabKey = String(tabKey).replace(/^#/, '').replace(/^tab-/, '').replace(/^wppoppop-tab-/, '');

            // Deactivate all tab buttons and containers
            $('.wppoppop-tab-btn').removeClass('active');
            $('.wppoppop-tab-content').removeClass('active').hide();

            // Highlight active button
            $('.wppoppop-tab-btn[data-tab="' + tabKey + '"]').addClass('active');

            // Activate target container matching either #tab-* or data-tab-content
            var $target = $('#tab-' + tabKey + ', #wppoppop-tab-' + tabKey + ', [data-tab-content="' + tabKey + '"]');
            if ($target.length) {
                $target.addClass('active').show();
            }

            // Sync with URL Hash for direct linking
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '#' + tabKey);
            }
        },

        restoreActiveTab: function() {
            if (window.location.hash) {
                var hashTab = window.location.hash.replace('#', '').replace(/^tab-/, '');
                if ($('.wppoppop-tab-btn[data-tab="' + hashTab + '"]').length) {
                    this.switchTab(hashTab);
                    return;
                }
            }
            // Default to general tab if none selected
            this.switchTab('general');
        }
    };

    window.WpPopPopSettings.Tabs = Tabs;
    window.WpPopPopSettingsTabs = Tabs;
})(window, jQuery);
