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
            $('.wppoppop-tab-btn').on('click', function() {
                var targetTab = $(this).data('tab');
                self.switchTab(targetTab);
            });
        },

        switchTab: function(tabKey) {
            $('.wppoppop-tab-btn').removeClass('active');
            $('.wppoppop-tab-btn[data-tab="' + tabKey + '"]').addClass('active');

            $('.wppoppop-tab-content').hide().removeClass('active');
            var $targetContent = $('#tab-' + tabKey);
            if ($targetContent.length) {
                $targetContent.show().addClass('active');
            }

            // Sync with URL Hash for direct linking
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '#' + tabKey);
            }
        },

        restoreActiveTab: function() {
            if (window.location.hash) {
                var hashTab = window.location.hash.replace('#', '');
                if ($('.wppoppop-tab-btn[data-tab="' + hashTab + '"]').length) {
                    this.switchTab(hashTab);
                }
            }
        }
    };

    window.WpPopPopSettings.Tabs = Tabs;
})(window, jQuery);
