/**
 * WpPopPop Tools: Top-bar triggers.
 * Bar buttons proxy to the real card buttons so there is a single code path.
 */
(function($) {
    'use strict';
    $(document).on('click', '[data-wpp-trigger]', function(e) {
        e.preventDefault();
        var $target = $($(this).attr('data-wpp-trigger')).first();
        if ($target.length) {
            $target.trigger('click');
        }
    });
})(jQuery);
