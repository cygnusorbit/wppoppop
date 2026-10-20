(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Triggers = {
        init: function() {
            this.bindClickTriggers();
            this.bindExitIntent();
            this.bindMobileBack();
            this.bindScrollDepth();
            this.bindInactivity();
            this.bindVideoListeners();
            this.checkAdBlock();
        },

        bindClickTriggers: function() {
            $(document).on('click', '.wppoppop-open-btn, [data-target-uid]', function(e) {
                e.preventDefault();
                var uid = $(this).data('target-uid') || $(this).attr('data-target-uid');
                if (uid && window.WpPopPopFront.Modal) {
                    window.WpPopPopFront.Modal.open(uid);
                }
            });
        },

        bindExitIntent: function() { /* WPPOPPOP_EXIT_XBROWSER */
            var triggered = {};
            function fire(e) {
                var y = (typeof e.clientY === 'number') ? e.clientY : 0;
                $('.wppoppop-popup-wrap[data-trigger-exit="1"]').each(function() {
                    var $w = $(this);
                    var uid = $w.attr('data-uid');
                    var sens = parseInt($w.attr('data-exit-sens'), 10) || 20;
                    if (!uid || triggered[uid] || y > sens) { return; }
                    triggered[uid] = true;
                    if (window.WpPopPopFront.Modal) { window.WpPopPopFront.Modal.open(uid); }
                });
            }
            /* Chrome / Edge / Firefox: pointer leaves the viewport */
            document.documentElement.addEventListener('mouseleave', fire, false);
            /* Safari fallback: mouseout with no related target = left the window */
            document.addEventListener('mouseout', function(e) {
                if (!e.relatedTarget && !e.toElement) { fire(e); }
            }, false);
        },

        bindMobileBack: function() {
            if (window.history && window.history.pushState && $('.wppoppop-popup-wrap[data-trigger-back="1"]').length) {
                window.history.pushState({ wppoppop: true }, '');
                $(window).on('popstate', function() {
                    $('.wppoppop-popup-wrap[data-trigger-back="1"]').each(function() {
                        var uid = $(this).data('uid');
                        if (window.WpPopPopFront.Modal && !window.WpPopPopFront.Modal.isSuppressed(uid)) {
                            window.WpPopPopFront.Modal.open(uid);
                        }
                    });
                });
            }
        },

        bindScrollDepth: function() {
            var triggered = {};
            $(window).on('scroll', function() {
                var scrollTop = $(window).scrollTop();
                var docHeight = $(document).height() - $(window).height();
                var scrollPct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

                $('.wppoppop-popup-wrap[data-trigger-scroll]').each(function() {
                    var uid = $(this).data('uid');
                    var targetPct = parseFloat($(this).data('trigger-scroll')) || 50;
                    if (!triggered[uid] && scrollPct >= targetPct && window.WpPopPopFront.Modal) {
                        triggered[uid] = true;
                        window.WpPopPopFront.Modal.open(uid);
                    }
                });
            });
        },

        bindInactivity: function() {
            var idleTime = 0;
            var interval = setInterval(function() {
                idleTime += 1;
                $('.wppoppop-popup-wrap[data-trigger-idle]').each(function() {
                    var uid = $(this).data('uid');
                    var targetSec = parseInt($(this).data('trigger-idle'), 10) || 15;
                    if (idleTime >= targetSec && !$(this).data('idleFired') && window.WpPopPopFront.Modal) {
                        $(this).data('idleFired', true);
                        window.WpPopPopFront.Modal.open(uid);
                    }
                });
            }, 1000);

            $(document).on('mousemove keydown scroll', function() {
                idleTime = 0;
            });
        },

        bindVideoListeners: function() {
            $('video').on('ended', function() {
                $('.wppoppop-popup-wrap[data-trigger-video-end="1"]').each(function() {
                    var uid = $(this).data('uid');
                    if (window.WpPopPopFront.Modal) {
                        window.WpPopPopFront.Modal.open(uid);
                    }
                });
            });
        },

        checkAdBlock: function() {
            var bait = document.createElement('div');
            bait.className = 'pub_300x250 pub_300x250m pub_728x90 text-ad ad-text';
            bait.style.cssText = 'width: 1px !important; height: 1px !important; position: absolute !important; left: -10000px !important;';
            document.body.appendChild(bait);

            setTimeout(function() {
                var isBlocked = (bait.offsetParent === null || bait.offsetHeight === 0 || bait.offsetLeft === 0);
                bait.remove();
                if (isBlocked) {
                    $('.wppoppop-popup-wrap[data-trigger-adblock="1"]').each(function() {
                        var uid = $(this).data('uid');
                        if (window.WpPopPopFront.Modal) {
                            window.WpPopPopFront.Modal.open(uid);
                        }
                    });
                }
            }, 200);
        }
    };

    window.WpPopPopFront.Triggers = Triggers;
})(window, jQuery);
