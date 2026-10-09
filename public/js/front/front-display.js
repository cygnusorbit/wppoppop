/**
 * WpPopPop Frontend Display Runtime Engine
 * Manages GA Events, Render Delays, Component Celebrations, and Cookie Epoch Synchronization
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
            var startDelay = vars.render_delay || 0;

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
                    // Cookie epoch incremented by admin; clear dismissals
                    Object.keys(localStorage).forEach(function(key) {
                        if (key.indexOf('wppoppop_dismissed_') === 0 || key.indexOf('wppoppop_submitted_') === 0) {
                            localStorage.removeItem(key);
                        }
                    });
                }
                localStorage.setItem('wppoppop_cookie_epoch', vars.cookie_epoch);
            } catch (e) {
                // Ignore storage exceptions in private/incognito modes
            }
        },

        bindEvents: function() {
            var self = this;

            // Close button trigger
            $(document).on('click', '.wppoppop-close-btn, .wppoppop-backdrop', function(e) {
                e.preventDefault();
                var uid = $(this).data('uid');
                self.close(uid);
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

        evaluateTriggers: function() {
            var self = this;
            $('.wppoppop-popup-wrap').each(function() {
                var $wrap = $(this);
                var uid = $wrap.data('uid');
                
                // Frequency check: if dismissed in current epoch, bypass
                try {
                    if (localStorage.getItem('wppoppop_dismissed_' + uid)) {
                        return;
                    }
                } catch(e) {}

                self.show(uid);
            });
        },

        show: function(uid) {
            var $wrap = $('#wppoppop-wrap-' + uid);
            if (!$wrap.length) return;

            $wrap.fadeIn(250).attr('aria-hidden', 'false');

            // Dispatch Google Analytics Event if enabled
            if (vars.ga_tracking) {
                this.dispatchGA('wppoppop_impression', { popup_uid: uid });
            }
        },

        close: function(uid) {
            var $wrap = $('#wppoppop-wrap-' + uid);
            if (!$wrap.length) return;

            $wrap.fadeOut(200).attr('aria-hidden', 'true');

            try {
                localStorage.setItem('wppoppop_dismissed_' + uid, Date.now());
            } catch(e) {}

            // Dispatch Google Analytics Event if enabled
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

        /**
         * Trigger celebration effects (Confetti / Fireworks) upon conversion.
         */
        triggerCelebration: function() {
            // 1. Canvas Confetti Burst
            if (typeof window.confetti === 'function') {
                try {
                    window.confetti({
                        particleCount: 90,
                        spread: 65,
                        origin: { y: 0.6 }
                    });
                } catch (e) {}
            }

            // 2. Canvas Fireworks
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

        /**
         * Initialize component extension widgets on canvas elements.
         */
        initExtensionWidgets: function() {
            // 1. jQuery Mask
            if (typeof $.fn.mask === 'function') {
                $('input[data-mask]').each(function() {
                    var maskPattern = $(this).data('mask');
                    if (maskPattern) {
                        $(this).mask(maskPattern);
                    }
                });
            }

            // 2. Air Datepicker
            if (typeof window.AirDatepicker !== 'undefined') {
                $('.wppoppop-datepicker-input').each(function() {
                    new window.AirDatepicker(this, {
                        autoClose: true
                    });
                });
            }

            // 3. Digital Signature Pad
            if (typeof window.SignaturePad !== 'undefined') {
                $('.wppoppop-signature-canvas').each(function() {
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
