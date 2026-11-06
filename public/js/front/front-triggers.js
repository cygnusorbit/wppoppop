(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var extensions = [];

    var Triggers = {
        _ready: false,

        /* A module (Display Triggers) hooks its runtime in here. It runs once, after the click trigger is bound. */
        register: function(fn) {
            if (typeof fn !== 'function') { return; }
            if (this._ready) {
                try { fn(); } catch (e) {}
            } else {
                extensions.push(fn);
            }
        },

        init: function() {
            if (this._ready) { return; }
            this._ready = true;
            this.bindClickTriggers();
            while (extensions.length) {
                try { extensions.shift()(); } catch (e) {}
            }
        },

        bindClickTriggers: function() {
            $(document).on('click', '.wppoppop-open-btn, [data-target-uid]', function(e) {
                e.preventDefault();
                var uid = $(this).data('target-uid') || $(this).attr('data-target-uid');
                if (uid && window.WpPopPopFront.Modal) {
                    window.WpPopPopFront.Modal.open(uid);
                }
            });
        }
    };

    window.WpPopPopFront.Triggers = Triggers;
})(window, jQuery);
