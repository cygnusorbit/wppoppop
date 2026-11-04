/**
 * WpPopPop Front: Auto-Close Countdown
 * Per-canvas countdown (data-autoclose on the canvas, seconds). Restarts when the popup opens
 * or the active canvas changes, updates the badge and closes the popup at zero.
 */
(function(window, $) {
    'use strict';

    var D = window.WpPopPopFrontDisplay;
    if (!D || D._autoCloseWrapped) { return; }
    D._autoCloseWrapped = true;

    var timers = {};

    function secondsFor($canvas) {
        var n = parseInt($canvas.attr('data-autoclose'), 10);
        if (isNaN(n) || n < 0) { return 0; }
        return Math.min(n, 300);
    }

    function stop(uid) {
        var t = timers[uid];
        if (t) {
            window.clearInterval(t.id);
            delete timers[uid];
        }
    }

    function paint($canvas, left) {
        $canvas.find('.wppoppop-autoclose-num').first().text(left);
    }

    function start(uid) {
        stop(uid);
        var $wrap = D.getPopupWrap(uid);
        if (!$wrap.length || $wrap.attr('data-inline') === '1') { return; }

        var st = { canvas: null, end: 0, id: 0 };

        function arm() {
            var $c = $wrap.find('.wppoppop-canvas-active').first();
            var secs = secondsFor($c);
            st.canvas = $c.attr('data-canvas');
            st.end = secs > 0 ? new Date().getTime() + secs * 1000 : 0;
            if (secs > 0) { paint($c, secs); }
        }

        arm();
        st.id = window.setInterval(function() {
            if ($wrap.data('wppoppopClosing') || $wrap.attr('aria-hidden') === 'true') {
                stop(uid);
                return;
            }
            var $c = $wrap.find('.wppoppop-canvas-active').first();
            if ($c.attr('data-canvas') !== st.canvas) { arm(); }
            if (!st.end) { return; }
            var left = Math.ceil((st.end - new Date().getTime()) / 1000);
            if (left <= 0) {
                stop(uid);
                D.close(uid);
                return;
            }
            paint($c, left);
        }, 200);
        timers[uid] = st;
    }

    var origShow = D.show;
    D.show = function(uid) {
        var r = origShow.apply(this, arguments);
        start(uid);
        return r;
    };

    var origClose = D.close;
    D.close = function(uid) {
        stop(uid);
        return origClose.apply(this, arguments);
    };
})(window, jQuery);
