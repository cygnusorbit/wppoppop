/**
 * WpPopPop Frontend Display Runtime Engine
 * Dual-Contract Normalization: Connects All Settings to All Popup Variants
 */
(function(window, $) {
    'use strict';

    var vars = window.wppoppop_front_vars || {
        ajax_url: '',
        nonce: '',
        cookie_epoch: 1,
        render_delay: 0,
        preload_events: false,
        ga_tracking: false,
        adblock_detector: false,
        libraries: {}
    };

    window.WpPopPopFrontDisplay = {
        init: function() {
            var self = this;
            var startDelay = parseInt(vars.render_delay, 10) || 0;

            if (startDelay > 0) {
                setTimeout(function() {
                    self.bootstrap();
                }, startDelay);
            } else {
                self.bootstrap();
            }
        },

        bootstrap: function() {
            this.syncCookieEpoch();
            this.bindEvents();
            this.evaluateTriggers();
            this.initExtensionWidgets();
        },

        syncCookieEpoch: function() {
            try {
                var storedEpoch = localStorage.getItem('wppoppop_cookie_epoch');
                if (storedEpoch && parseInt(storedEpoch, 10) < parseInt(vars.cookie_epoch, 10)) {
                    Object.keys(localStorage).forEach(function(key) {
                        if (key.indexOf('wppoppop_dismissed_') === 0 || key.indexOf('wppoppop_submitted_') === 0) {
                            localStorage.removeItem(key);
                        }
                    });
                }
                localStorage.setItem('wppoppop_cookie_epoch', vars.cookie_epoch);
            } catch (e) {}
        },

        bindEvents: function() {
            var self = this;

            // Universal close triggers
            $(document).on('click', '.wppoppop-close-btn, .wppoppop-backdrop', function(e) {
                e.preventDefault();
                var $wrap = $(this).closest('.wppoppop-popup-wrap, .wppoppop-overlay');
                var uid = $(this).data('uid') || $wrap.data('uid');
                self.close(uid);
            });

            // Sequence next-canvas step triggers
            $(document).on('click', '.wppoppop-next-canvas-btn, .wppoppop-next-screen-btn', function(e) {
                e.preventDefault();
                var targetIdx = $(this).data('goto-canvas') || $(this).data('goto-screen') || $(this).data('goto') || 2;
                var $popup = $(this).closest('.wppoppop-popup-wrap, .wppoppop-overlay');

                $popup.find('.wppoppop-canvas-container, .wppoppop-canvas-stage').removeClass('wppoppop-canvas-active wppoppop-screen-active active').hide();
                var $next = $popup.find('[data-canvas="' + targetIdx + '"], [data-screen="' + targetIdx + '"], [data-canvas-index="' + targetIdx + '"]');
                if ($next.length) {
                    $next.addClass('wppoppop-canvas-active wppoppop-screen-active active').fadeIn(200);
                    self.playCanvasIn($next);
                }
            });

            // AdBlock Event Check
            if (vars.adblock_detector && window.wppoppop_adblock_detected) {
                $(document).trigger('wppoppop:adblock_active');
            }

            // Lead capture celebration trigger
            $(document).on('wppoppop:form_submitted', function(e, data) {
                self.triggerCelebration();
            });
        },

        getPopupWrap: function(uid) {
            return $('#wppoppop-popup-' + uid + ', #wppoppop-wrap-' + uid).first();
        },

        evaluateTriggers: function() {
            var self = this;
            $('.wppoppop-popup-wrap, .wppoppop-overlay[data-uid]').each(function() {
                var $wrap = $(this);
                var uid = $wrap.data('uid');
                if (!uid) return;

                try {
                    if (localStorage.getItem('wppoppop_dismissed_' + uid)) {
                        return;
                    }
                } catch(e) {}

                var hasLoad = $wrap.attr('data-trigger-load') === '1';
                var hasOther = $wrap.is('[data-trigger-exit],[data-trigger-scroll],[data-trigger-back],[data-trigger-idle]');
                if (!hasLoad && hasOther) return;
                var loadDelay = hasLoad ? (parseFloat($wrap.attr('data-trigger-load-delay')) || 0) * 1000 : 0;
                if (loadDelay > 0) {
                    setTimeout(function() { self.show(uid); }, loadDelay);
                } else {
                    self.show(uid);
                }
            });
        },

        show: function(uid) {
            var $wrap = this.getPopupWrap(uid);
            if (!$wrap.length) return;

            $wrap.data('wppoppopClosing', false);
            $wrap.stop(true, true).fadeIn(250).css('display', 'flex').attr('aria-hidden', 'false');
            this.playCanvasIn($wrap.find('.wppoppop-canvas-active').first());

            if (vars.ga_tracking) {
                this.dispatchGA('wppoppop_impression', { popup_uid: uid });
            }
        },

        close: function(uid) {
            var $wrap = this.getPopupWrap(uid);
            if (!$wrap.length) return;

            if ($wrap.data('wppoppopClosing')) return;
            $wrap.data('wppoppopClosing', true);

            var self = this;
            var $c = $wrap.find('.wppoppop-canvas-active').first();
            var outFx = this.fxName($c.attr('data-anim-disappearance'), 'out');
            var outDur = parseInt($c.attr('data-anim-duration'), 10);
            if (isNaN(outDur) || outDur <= 0) outDur = 500;
            outDur = Math.min(outDur, 800);

            if (outFx && $c.length) {
                this.runFx($c, outFx, outDur, 0, true);
                setTimeout(function() {
                    $wrap.fadeOut(150).attr('aria-hidden', 'true');
                    self.clearFx($c);
                }, outDur + 20);
            } else {
                $wrap.fadeOut(200).attr('aria-hidden', 'true');
            }

            try {
                localStorage.setItem('wppoppop_dismissed_' + uid, Date.now());
            } catch(e) {}

            if (vars.ga_tracking) {
                this.dispatchGA('wppoppop_close', { popup_uid: uid });
            }
        },

        fxName: function(effect, dir) {
            if (!effect || effect === 'none') return '';
            var e = String(effect).replace(/^animate__/, '').replace(/^wppoppop-anim-/, '').replace(/^anim-/, '').replace(/^wppoppop-fx-/, '');
            var inMap = { fade: 'fadeIn', slideDown: 'slideInDown', slideUp: 'slideInUp', slideLeft: 'slideInRight', slideRight: 'slideInLeft', zoom: 'zoomIn', flip: 'flipInX' };
            var outMap = { fade: 'fadeOut', slideDown: 'slideOutDown', slideUp: 'slideOutUp', slideLeft: 'slideOutLeft', slideRight: 'slideOutRight', zoom: 'zoomOut' };
            var map = (dir === 'out') ? outMap : inMap;
            return 'wppoppop-fx-' + (map[e] || e);
        },

        clearFx: function($n) {
            $n.each(function() {
                this.className = this.className.replace(/(^|\s)wppoppop-fx-\S+/g, '');
                this.style.animationName = '';
                this.style.webkitAnimationName = '';
                this.style.animationDuration = '';
                this.style.webkitAnimationDuration = '';
                this.style.animationDelay = '';
                this.style.webkitAnimationDelay = '';
                this.style.animationFillMode = '';
                this.style.webkitAnimationFillMode = '';
            });
        },

        runFx: function($n, fxClass, dur, del, keep) {
            var self = this;
            if (!$n.length || !fxClass) return;
            self.clearFx($n);
            void $n[0].offsetWidth;
            $n.css({
                'animation-duration': dur + 'ms', '-webkit-animation-duration': dur + 'ms',
                'animation-delay': del + 'ms', '-webkit-animation-delay': del + 'ms',
                'animation-fill-mode': 'both', '-webkit-animation-fill-mode': 'both'
            }).addClass('wppoppop-fx-run ' + fxClass);
            clearTimeout($n.data('wppoppopFxTimer'));
            if (!keep) {
                $n.data('wppoppopFxTimer', setTimeout(function() { self.clearFx($n); }, dur + del + 100));
            }
        },

        restartFx: function(node) {
            node.style.webkitAnimationName = 'none';
            node.style.animationName = 'none';
            void node.offsetWidth;
            node.style.webkitAnimationName = '';
            node.style.animationName = '';
        },

        playCanvasIn: function($c) {
            var self = this;
            if (!$c || !$c.length) return;
            var eff = this.fxName($c.attr('data-anim-appearance'), 'in');
            var dur = parseInt($c.attr('data-anim-duration'), 10);
            var del = parseInt($c.attr('data-anim-delay'), 10);
            if (isNaN(dur) || dur <= 0) dur = 1000;
            if (isNaN(del) || del < 0) del = 0;
            if (eff) this.runFx($c, eff, dur, del, false);
            $c.find('.wppoppop-fx-run').each(function() { self.restartFx(this); });
        },

        dispatchGA: function(eventName, params) {
            try {
                if (typeof window.gtag === 'function') {
                    window.gtag('event', eventName, params);
                }
                if (Array.isArray(window.dataLayer)) {
                    window.dataLayer.push($.extend({ event: eventName }, params));
                }
            } catch (err) {}
        },

        triggerCelebration: function() {
            if (typeof window.confetti === 'function') {
                try {
                    window.confetti({
                        particleCount: 90,
                        spread: 65,
                        origin: { y: 0.6 }
                    });
                } catch (e) {}
            }

            if (typeof window.Fireworks !== 'undefined') {
                try {
                    var container = document.body;
                    var fireworks = new window.Fireworks.default(container, {
                        autoresize: true,
                        opacity: 0.5,
                        acceleration: 1.05,
                        friction: 0.97,
                        gravity: 1.5,
                        particles: 50,
                        traceLength: 3,
                        traceSpeed: 10,
                        explosion: 5,
                        intensity: 30
                    });
                    fireworks.start();
                    setTimeout(function() { fireworks.stop(); }, 2500);
                } catch (e) {}
            }
        },

        initExtensionWidgets: function() {
            if (typeof $.fn.mask === 'function') {
                $('input[data-mask]').each(function() {
                    var maskPattern = $(this).data('mask');
                    if (maskPattern) $(this).mask(maskPattern);
                });
            }

            if (typeof window.AirDatepicker !== 'undefined') {
                $('.wppoppop-datepicker-input').each(function() {
                    new window.AirDatepicker(this, { autoClose: true });
                });
            }

            if (typeof window.SignaturePad !== 'undefined') {
                $('.wppoppop-signature-canvas, .wppoppop-sig-canvas').each(function() {
                    if (!this._sigPad) {
                        this._sigPad = new window.SignaturePad(this, {
                            backgroundColor: 'rgba(255, 255, 255, 0)',
                            penColor: '#0f172a'
                        });
                    }
                });
            }
        }
    };

    window.WpPopPopFront = window.WpPopPopFront || {};
    if (!window.WpPopPopFront.Modal) {
        window.WpPopPopFront.Modal = {
            isSuppressed: function(uid) {
                try { return !!localStorage.getItem('wppoppop_dismissed_' + uid); } catch (e) { return false; }
            },
            open: function(uid) {
                if (!uid || this.isSuppressed(uid)) return;
                var $w = window.WpPopPopFrontDisplay.getPopupWrap(uid);
                if (!$w.length || $w.is(':visible')) return;
                window.WpPopPopFrontDisplay.show(uid);
            },
            close: function(uid) { window.WpPopPopFrontDisplay.close(uid); }
        };
    }

    $(document).ready(function() {
        var T = window.WpPopPopFront.Triggers;
        if (T && typeof T.init === 'function' && !T._wppoppopInit) {
            var origInit = T.init;
            T.init = function() {
                if (T._wppoppopInited) return;
                T._wppoppopInited = true;
                return origInit.apply(T, arguments);
            };
            T._wppoppopInit = true;
            T.init();
        }
        window.WpPopPopFrontDisplay.init();
    });

})(window, jQuery);
