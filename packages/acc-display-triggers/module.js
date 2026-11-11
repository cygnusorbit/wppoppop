/**
 * Display Triggers - frontend runtime.
 * Reads the data-trigger-* attributes the popup renderer prints and opens the popup:
 * exit intent, scroll depth, inactivity and video end.
 * Page load / always / repeat stay with the core display runtime.
 */
(function (window, $) {
    'use strict';
    var F = window.WpPopPopFront;
    var T = F && F.Triggers;
    if (!T || typeof T.register !== 'function' || T._displayTriggers) { return; }
    T._displayTriggers = true;

    function open(uid) {
        if (uid && F.Modal) { F.Modal.open(uid); }
    }

    function bindExitIntent() { /* Chrome / Edge / Firefox + Safari fallback */
        var triggered = {};
        function fire(e) {
            var y = (typeof e.clientY === 'number') ? e.clientY : 0;
            $('.wppoppop-popup-wrap[data-trigger-exit="1"]').each(function () {
                var $w = $(this);
                var uid = $w.attr('data-uid');
                var sens = parseInt($w.attr('data-exit-sens'), 10) || 20;
                if (!uid || triggered[uid] || y > sens) { return; }
                triggered[uid] = true;
                open(uid);
            });
        }
        /* Chrome / Edge / Firefox: pointer leaves the viewport */
        document.documentElement.addEventListener('mouseleave', fire, false);
        /* Safari fallback: mouseout with no related target = left the window */
        document.addEventListener('mouseout', function (e) {
            if (!e.relatedTarget && !e.toElement) { fire(e); }
        }, false);
    }

    function bindScrollDepth() {
        var triggered = {};
        $(window).on('scroll', function () {
            var scrollTop = $(window).scrollTop();
            var docHeight = $(document).height() - $(window).height();
            var scrollPct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

            $('.wppoppop-popup-wrap[data-trigger-scroll]').each(function () {
                var uid = $(this).data('uid');
                var targetPct = parseFloat($(this).data('trigger-scroll')) || 50;
                if (!triggered[uid] && scrollPct >= targetPct && F.Modal) {
                    triggered[uid] = true;
                    F.Modal.open(uid);
                }
            });
        });
    }

    function bindInactivity() {
        var idleTime = 0;
        setInterval(function () {
            idleTime += 1;
            $('.wppoppop-popup-wrap[data-trigger-idle]').each(function () {
                var uid = $(this).data('uid');
                var targetSec = parseInt($(this).data('trigger-idle'), 10) || 15;
                if (idleTime >= targetSec && !$(this).data('idleFired') && F.Modal) {
                    $(this).data('idleFired', true);
                    F.Modal.open(uid);
                }
            });
        }, 1000);

        $(document).on('mousemove keydown scroll', function () {
            idleTime = 0;
        });
    }

    function bindVideoListeners() {
        $('video').on('ended', function () {
            $('.wppoppop-popup-wrap[data-trigger-video-end="1"]').each(function () {
                open($(this).data('uid'));
            });
        });
    }

    T.register(function () {
        bindExitIntent();
        bindScrollDepth();
        bindInactivity();
        bindVideoListeners();
    });
})(window, jQuery);
