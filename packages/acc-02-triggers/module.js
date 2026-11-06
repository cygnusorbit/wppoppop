/**
 * Display Triggers - frontend runtime.
 * Reads the data-trigger-* attributes the popup renderer prints and opens the popup:
 * exit intent, mobile back button, scroll depth, inactivity, video end and AdBlock.
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

    function bindMobileBack() {
        if (window.history && window.history.pushState && $('.wppoppop-popup-wrap[data-trigger-back="1"]').length) {
            window.history.pushState({ wppoppop: true }, '');
            $(window).on('popstate', function () {
                $('.wppoppop-popup-wrap[data-trigger-back="1"]').each(function () {
                    var uid = $(this).data('uid');
                    if (F.Modal && !F.Modal.isSuppressed(uid)) {
                        F.Modal.open(uid);
                    }
                });
            });
        }
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

    function checkAdBlock() {
        var bait = document.createElement('div');
        bait.className = 'pub_300x250 pub_300x250m pub_728x90 text-ad ad-text';
        bait.style.cssText = 'width: 1px !important; height: 1px !important; position: absolute !important; left: -10000px !important;';
        document.body.appendChild(bait);

        setTimeout(function () {
            var isBlocked = (bait.offsetParent === null || bait.offsetHeight === 0 || bait.offsetLeft === 0);
            bait.remove();
            if (isBlocked) {
                $('.wppoppop-popup-wrap[data-trigger-adblock="1"]').each(function () {
                    open($(this).data('uid'));
                });
            }
        }, 200);
    }

    T.register(function () {
        bindExitIntent();
        bindMobileBack();
        bindScrollDepth();
        bindInactivity();
        bindVideoListeners();
        checkAdBlock();
    });
})(window, jQuery);
