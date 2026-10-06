(function(window, $) {
    'use strict';
    window.WpPopPopDashboard = window.WpPopPopDashboard || {};

    var Embed = {
        init: function() {
            this.bindModal();
            this.bindAutoSelect();
        },

        open: function(uid) {
            $('#wppoppop-dash-sc-field').val('[wppoppop uid="' + uid + '"]');
            $('#wppoppop-dash-locker-field').val('[wppoppop_locker uid="' + uid + '"]Exclusive Locked Content Here[/wppoppop_locker]');
            $('#wppoppop-dash-btn-field').val('<a href="#" class="wppoppop-open-btn" data-target-uid="' + uid + '">Open Popup</a>');
            $('#wppoppop-dash-embed-modal').css('display', 'flex');
        },

        bindModal: function() {
            $('#wppoppop-dash-embed-close').on('click', function() {
                $('#wppoppop-dash-embed-modal').hide();
            });

            $('#wppoppop-dash-embed-modal').on('click', function(e) {
                if (e.target === this) {
                    $(this).hide();
                }
            });
        },

        bindAutoSelect: function() {
            $('#wppoppop-dash-sc-field, #wppoppop-dash-locker-field, #wppoppop-dash-btn-field').on('click', function() {
                $(this).select();
            });
        }
    };

    window.WpPopPopDashboard.Embed = Embed;
})(window, jQuery);
