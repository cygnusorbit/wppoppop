(function(window, $) {
    'use strict';

    var Overview = {
        closeAll: function() {
            $('.wppoppop-dd').removeClass('is-open');
            $('.wppoppop-dash-bar [data-dd]').attr('aria-expanded', 'false');
        },

        init: function() {
            var self = this;
            if (!$('.wppoppop-dash-bar').length) return;

            $(document).on('click', '.wppoppop-dash-bar [data-dd]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $btn = $(this);
                var $dd = $('#' + $btn.attr('data-dd'));
                var open = $dd.hasClass('is-open');
                self.closeAll();
                if (!open) {
                    $dd.addClass('is-open');
                    $btn.attr('aria-expanded', 'true');
                    $dd.find('a').first().trigger('focus');
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.wppoppop-dd').length) self.closeAll();
            });

            $(document).on('keydown', function(e) {
                if (e.which === 27) self.closeAll();
            });
        }
    };

    window.WpPopPopOverview = Overview;
    $(document).ready(function() { Overview.init(); });
})(window, jQuery);
