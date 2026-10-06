(function(window, $) {
    'use strict';
    window.WpPopPopLibrary = window.WpPopPopLibrary || {};

    var Filter = {
        init: function() {
            this.bindCategoryPills();
            this.bindLiveSearch();
        },

        bindCategoryPills: function() {
            $('.wppoppop-cat-btn').on('click', function() {
                $('.wppoppop-cat-btn').removeClass('active').css({ background: '', color: '', borderColor: '' });
                $(this).addClass('active').css({ background: '#2563eb', color: '#fff', borderColor: '#1d4ed8' });

                var cat = $(this).data('cat');
                if (cat === 'all') {
                    $('.wppoppop-tpl-card').show();
                } else {
                    $('.wppoppop-tpl-card').each(function() {
                        $(this).toggle($(this).data('category') === cat);
                    });
                }
            });
        },

        bindLiveSearch: function() {
            $('#wppoppop-lib-search').on('input', function() {
                var term = $(this).val().toLowerCase().trim();
                $('.wppoppop-tpl-card').each(function() {
                    var title = $(this).data('title') || '';
                    var desc  = $(this).data('desc') || '';
                    if (title.indexOf(term) !== -1 || desc.indexOf(term) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        }
    };

    window.WpPopPopLibrary.Filter = Filter;
})(window, jQuery);
