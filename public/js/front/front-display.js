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
            return $('#wppoppop-popup-' + uid + ', #wppoppop-wrap-' + uid + ', [data-uid="' + uid + '"]');
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

                self.show(uid);
            });
        },

        show: function(uid) {
            var $wrap = this.getPopupWrap(uid);
            if (!$wrap.length) return;

            $wrap.fadeIn(250).css('display', 'flex').attr('aria-hidden', 'false');

            if (vars.ga_tracking) {
                this.dispatchGA('wppoppop_impression', { popup_uid: uid });
            }
        },

        close: function(uid) {
            var $wrap = this.getPopupWrap(uid);
            if (!$wrap.length) return;

            $wrap.fadeOut(200).attr('aria-hidden', 'true');

            try {
                localStorage.setItem('wppoppop_dismissed_' + uid, Date.now());
            } catch(e) {}

            if (vars.ga_tracking) {
                this.dispatchGA('wppoppop_close', { popup_uid: uid });
            }
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

    $(document).ready(function() {
        window.WpPopPopFrontDisplay.init();
    });

})(window, jQuery);
