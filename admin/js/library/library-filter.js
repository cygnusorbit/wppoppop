/**
 * WpPopPop Popups Library: Filter & Search Engine
 * Cross-Browser Compatibility: Safari, Firefox, Chrome, Edge
 */
(function(window, $) {
    'use strict';
    window.WpPopPopLibraryFilter = window.WpPopPopLibraryFilter || {};

    var Filter = {
        activeCategory: 'all',
        activeSearch: '',

        init: function() {
            this.bindCategoryPills();
            this.bindSearchInput();
        },

        bindCategoryPills: function() {
            var self = this;
            $(document).on('click', '.wppoppop-cat-btn', function(e) {
                e.preventDefault();
                $('.wppoppop-cat-btn').removeClass('active');
                $(this).addClass('active');

                self.activeCategory = $(this).data('category') || 'all';
                self.applyFilters();
            });
        },

        bindSearchInput: function() {
            var self = this;
            // Cross-browser input event with keyup fallback
            $(document).on('input keyup', '#wppoppop-lib-search', function() {
                self.activeSearch = $(this).val().toLowerCase().trim();
                self.applyFilters();
            });
        },

        applyFilters: function() {
            var self = this;
            var visibleCount = 0;

            $('.wppoppop-template-card').each(function() {
                var $card = $(this);
                var category = ($card.data('category') || '').toString().toLowerCase();
                var title = ($card.find('.wppoppop-tpl-info h3').text() || '').toLowerCase();
                var desc = ($card.find('.wppoppop-tpl-info p').text() || '').toLowerCase();

                var categoryMatch = (self.activeCategory === 'all') || (category === self.activeCategory);
                var searchMatch = !self.activeSearch || (title.indexOf(self.activeSearch) !== -1) || (desc.indexOf(self.activeSearch) !== -1);

                if (categoryMatch && searchMatch) {
                    $card.show();
                    visibleCount++;
                } else {
                    $card.hide();
                }
            });

            // Toggle empty notice
            if (visibleCount === 0) {
                if ($('#wppoppop-lib-empty-notice').length === 0) {
                    $('.wppoppop-library-grid').append('<div id="wppoppop-lib-empty-notice" class="wppoppop-library-empty">No popup starter templates found matching your criteria.</div>');
                } else {
                    $('#wppoppop-lib-empty-notice').show();
                }
            } else {
                $('#wppoppop-lib-empty-notice').hide();
            }
        }
    };

    window.WpPopPopLibraryFilter = Filter;
})(window, jQuery);
