(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Triggers = {
        init: function(popupEl, config, showCallback) {
            var triggers = config.triggers || {};
            var uid = $(popupEl).data('uid');
            var hasTriggered = false;

            function triggerOnce() {
                if (!hasTriggered) {
                    hasTriggered = true;
                    showCallback();
                }
            }

            // 1. Page Load with Delay
            if (triggers.on_load) {
                var delay = (parseInt(triggers.on_load_delay, 10) || 0) * 1000;
                setTimeout(triggerOnce, delay);
            }

            // 2. Desktop Cursor Exit Intent
            if (triggers.on_exit) {
                $(document).one('mouseleave.wppoppop_' + uid, function(e) {
                    if (e.clientY <= 10) {
                        triggerOnce();
                    }
                });
            }

            // 3. Mobile Back-Button Exit Interceptor (HTML5 History API)
            if (triggers.on_backbutton) {
                try {
                    window.history.pushState({ wppoppop_intercept: uid }, '');
                    $(window).one('popstate.wppoppop_' + uid, function(e) {
                        triggerOnce();
                    });
                } catch(err) {}
            }

            // 4. Scroll Depth Percentage
            if (triggers.on_scroll) {
                var targetPct = parseInt(triggers.scroll_val, 10) || 50;
                $(window).on('scroll.wppoppop_' + uid, function() {
                    var sTop = $(window).scrollTop();
                    var docH = $(document).height() - $(window).height();
                    if (docH > 0) {
                        var curPct = (sTop / docH) * 100;
                        if (curPct >= targetPct) {
                            $(window).off('scroll.wppoppop_' + uid);
                            triggerOnce();
                        }
                    }
                });
            }

            // 5. Idle Inactivity Timeout
            if (triggers.on_idle) {
                var idleSecs = (parseInt(triggers.idle_val, 10) || 30) * 1000;
                var idleTimer = null;
                function resetIdle() {
                    clearTimeout(idleTimer);
                    idleTimer = setTimeout(triggerOnce, idleSecs);
                }
                $(document).on('mousemove.wppoppop_' + uid + ' keydown.wppoppop_' + uid + ' scroll.wppoppop_' + uid, resetIdle);
                resetIdle();
            }

            // 6. AdBlock Detector
            if (triggers.on_adblock) {
                var testAd = document.createElement('div');
                testAd.innerHTML = '&nbsp;';
                testAd.className = 'adsbox pub_300x250 pub_300x250m pub_728x90 text-ad textAd text_ad text_ads';
                testAd.style.cssText = 'position:absolute;top:-999px;left:-999px;width:1px;height:1px;';
                document.body.appendChild(testAd);
                setTimeout(function() {
                    if (testAd.offsetHeight === 0 || testAd.clientHeight === 0 || window.getComputedStyle(testAd).display === 'none') {
                        triggerOnce();
                    }
                    testAd.remove();
                }, 100);
            }

            // 7. HTML5 & YouTube Video Event Listeners
            var videoCfg = config.video || {};
            if (videoCfg.enable) {
                $('video').on('ended', triggerOnce);
                if (videoCfg.on_time && videoCfg.timestamp) {
                    $('video').on('timeupdate', function() {
                        if (this.currentTime >= parseFloat(videoCfg.timestamp)) {
                            triggerOnce();
                        }
                    });
                }
            }

            // Manual Click Trigger (.wppoppop-open-btn[data-target-uid="UID"])
            $(document).on('click', '.wppoppop-open-btn[data-target-uid="' + uid + '"]', function(e) {
                e.preventDefault();
                showCallback();
            });
        }
    };

    window.WpPopPopFront.Triggers = Triggers;
})(window, jQuery);
