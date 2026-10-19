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
            this.playCanvasIn($wrap.find('.wppoppop-canvas-active').first(), $wrap.find('.wppoppop-box').first());

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
            var exitOn = $c.attr('data-anim-exit') !== '0';
            var outFx = exitOn ? this.fxName($c.attr('data-anim-disappearance'), 'out') : '';
            var outDur = parseInt($c.attr('data-anim-duration'), 10);
            if (isNaN(outDur) || outDur <= 0) outDur = 500;
            outDur = Math.min(outDur, 800);

            if (outFx && $c.length) {
                this.runFx($wrap.find('.wppoppop-box').first(), outFx, outDur, 0, true);
                setTimeout(function() {
                    $wrap.fadeOut(150).attr('aria-hidden', 'true');
                    self.clearFx($wrap.find('.wppoppop-box').first());
                }, outDur + 20);
            } else {
                $wrap[exitOn ? 'fadeOut' : 'hide'](exitOn ? 200 : 0).attr('aria-hidden', 'true');
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

        playCanvasIn: function($c, $target) {
            var self = this;
            if (!$c || !$c.length) return;
            var eff = this.fxName($c.attr('data-anim-appearance'), 'in');
            var dur = parseInt($c.attr('data-anim-duration'), 10);
            var del = parseInt($c.attr('data-anim-delay'), 10);
            if (isNaN(dur) || dur <= 0) dur = 1000;
            if (isNaN(del) || del < 0) del = 0;
            if (eff) this.runFx(($target && $target.length) ? $target : $c, eff, dur, del, false);
            var offset = eff ? (del + dur) : 0;
            $c.find('.wppoppop-layer-item.wppoppop-fx-run').each(function() {
                var own = this.getAttribute('data-fx-delay');
                if (own === null) {
                    own = parseInt(this.style.animationDelay || this.style.webkitAnimationDelay, 10) || 0;
                    this.setAttribute('data-fx-delay', own);
                }
                var d = (parseInt(own, 10) || 0) + offset;
                this.style.webkitAnimationDelay = d + 'ms';
                this.style.animationDelay = d + 'ms';
                self.restartFx(this);
            });
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
                $('.wppoppop-datepicker-input, .wppoppop-datepicker, .wppoppop-front-date').each(function() {
                    if (this._wppAirDp) return; /* WPPOPPOP_DATE_FIX: avoid double-init */
                    var $el = $(this);
                    var opts = { autoClose: true };
                    var fmt = $el.data('format') || $el.attr('data-format');
                    if (fmt) {
                        opts.dateFormat = String(fmt)
                            .replace('YYYY', 'yyyy').replace('MM', 'MM').replace('DD', 'dd');
                    }
                    var minD = $el.data('min') || $el.attr('data-min');
                    var maxD = $el.data('max') || $el.attr('data-max');
                    if (minD) { var md = new Date(minD); if (!isNaN(md)) opts.minDate = md; }
                    if (maxD) { var xd = new Date(maxD); if (!isNaN(xd)) opts.maxDate = xd; }
                    this._wppAirDp = new window.AirDatepicker(this, opts);
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

    // ---- Public JavaScript API + universal click triggers ----
    (function() {
        var D = window.WpPopPopFrontDisplay;

        function norm(uid) { return String(uid == null ? '' : uid).replace(/^\s+|\s+$/g, ''); }
        function wrap(uid) { return D.getPopupWrap(uid); }
        function clearDismissed(uid) { try { localStorage.removeItem('wppoppop_dismissed_' + uid); } catch (e) {} }

        window.WpPopPop = {
            show: function(uid) {
                uid = norm(uid);
                if (!uid) return false;
                var $w = wrap(uid);
                if (!$w.length) {
                    if (window.console && console.warn) console.warn('[WpPopPop] Popup not found on this page: ' + uid);
                    return false;
                }
                clearDismissed(uid);
                if ($w.is(':visible')) return true;
                D.show(uid);
                return true;
            },
            hide: function(uid) {
                uid = norm(uid);
                var $w = wrap(uid);
                if (!uid || !$w.length || !$w.is(':visible')) return false;
                D.close(uid);
                return true;
            },
            isOpen: function(uid) {
                var $w = wrap(norm(uid));
                return !!($w.length && $w.is(':visible'));
            },
            toggle: function(uid) {
                return this.isOpen(uid) ? this.hide(uid) : this.show(uid);
            },
            list: function() {
                var out = [];
                $('.wppoppop-popup-wrap[data-uid]').each(function() { out.push($(this).attr('data-uid')); });
                return out;
            }
        };

        $(document).off('click.wppoppopApi').on('click.wppoppopApi',
            '.wppoppop-trigger, .wppoppop-trigger-btn, .wppoppop-open-btn, [data-wppoppop-id], [data-target-uid], a[href^="#wppoppop-"]',
            function(e) {
                var $t = $(this);
                var uid = $t.attr('data-wppoppop-id') || $t.attr('data-target-uid') || '';
                if (!uid) {
                    var h = $t.attr('href') || '';
                    if (h.indexOf('#wppoppop-') === 0) uid = h.slice(10);
                }
                if (!uid) return;
                e.preventDefault();
                window.WpPopPop.show(uid);
            });
    })();

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


/* === WPPOPPOP_CLOSE_DISMISS_START === */
(function(window, document) {
    'use strict';
    if (window.__wppCloseDismissBound) return;
    window.__wppCloseDismissBound = true;

    function setCookie(name, val, days) {
        var d = new Date(); d.setTime(d.getTime() + days * 86400000);
        document.cookie = name + '=' + encodeURIComponent(val) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
    }
    document.addEventListener('click', function(e) {
        var t = e.target;
        if (!t || !t.closest) return;
        var btn = t.closest('.wppoppop-close-trigger[data-close-action]');
        if (!btn) return;
        var action = btn.getAttribute('data-close-action') || 'close';
        var wrap = btn.closest('.wppoppop-popup-wrap');
        var uid = wrap ? wrap.getAttribute('data-uid') : '';
        if (!uid) return;
        var vars = window.wppoppop_front_vars || {};
        try {
            if (action === 'close_period') {
                window.sessionStorage.setItem('wppoppop_session_' + uid, '1');
            } else if (action === 'close_forever') {
                var days = parseInt(btn.getAttribute('data-cookie-days'), 10);
                if (!days || days < 1) days = 30;
                setCookie('wppoppop_closed_' + uid, vars.cookie_epoch || 1, days);
            }
        } catch (err) {}
        /* existing handlers then perform the actual close animation */
    }, true);
})(window, document);
/* === WPPOPPOP_CLOSE_DISMISS_END === */


/* === WPPOPPOP_PROGRESS_RUNTIME_START === */
(function(window, document) {
    'use strict';
    if (window.__wppProgressBound) return;
    window.__wppProgressBound = true;

    function visible(el) {
        return !!(el.offsetWidth || el.offsetHeight || (el.getClientRects && el.getClientRects().length));
    }
    function formPercent(scope) {
        var nodes = scope.querySelectorAll('input, select, textarea');
        var groups = {}, order = [], uid = 0;
        for (var i = 0; i < nodes.length; i++) {
            var f = nodes[i], type = (f.type || '').toLowerCase();
            if (f.disabled) continue;
            if (['hidden', 'submit', 'button', 'reset', 'image', 'range'].indexOf(type) !== -1) continue;
            if (!visible(f)) continue;
            var key = ((type === 'radio' || type === 'checkbox') && f.name) ? ('g:' + f.name) : ('u:' + (uid++));
            if (!groups[key]) { groups[key] = false; order.push(key); }
            var done = false;
            if (type === 'radio' || type === 'checkbox') done = f.checked;
            else if (type === 'file') done = !!(f.files && f.files.length);
            else done = String(f.value || '').replace(/^\s+|\s+$/g, '') !== '';
            if (done) groups[key] = true;
        }
        if (!order.length) return 0;
        var ok = 0;
        for (var k = 0; k < order.length; k++) { if (groups[order[k]]) ok++; }
        return Math.round(ok / order.length * 100);
    }
    function stepPercent(popup) {
        var cs = popup.querySelectorAll('.wppoppop-canvas-container');
        if (!cs.length) return 0;
        var idx = 0;
        for (var i = 0; i < cs.length; i++) {
            if (window.getComputedStyle(cs[i]).display !== 'none') { idx = i; break; }
        }
        return Math.round((idx + 1) / cs.length * 100);
    }
    function update() {
        var bars = document.querySelectorAll('[data-progress-mode="form"], [data-progress-mode="steps"]');
        for (var i = 0; i < bars.length; i++) {
            var bar = bars[i];
            var mode = bar.getAttribute('data-progress-mode');
            var popup = bar.closest('.wppoppop-popup-wrap') || document;
            var scope = bar.closest('.wppoppop-canvas-container') || popup;
            var pct = (mode === 'steps') ? stepPercent(popup) : formPercent(scope);
            var fill = bar.querySelector('.wppoppop-progress-fill');
            if (fill) fill.style.width = pct + '%';
            var badge = bar.querySelector('.wppoppop-progress-badge');
            if (badge) {
                var lbl = bar.getAttribute('data-progress-label') || '';
                var show = bar.getAttribute('data-progress-showpct') !== '0';
                badge.textContent = lbl ? (lbl + (show ? ' (' + pct + '%)' : '')) : (show ? (pct + '%') : '');
            }
            bar.setAttribute('aria-valuenow', pct);
            bar.setAttribute('role', 'progressbar');
        }
    }
    function later() { setTimeout(update, 0); setTimeout(update, 80); setTimeout(update, 400); }
    ['input', 'change', 'keyup', 'click'].forEach(function(ev) { document.addEventListener(ev, later, true); });
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', update); else update();
    setInterval(update, 1000);   /* covers popups opening later and canvas fade transitions */
})(window, document);
/* === WPPOPPOP_PROGRESS_RUNTIME_END === */


/* === WPPOPPOP_BUTTON_CLOSE_START === */
(function(window, document) {
    'use strict';
    if (window.__wppBtnCloseBound) return;
    window.__wppBtnCloseBound = true;
    document.addEventListener('click', function(e) {
        var t = e.target;
        if (!t || !t.closest) return;
        var btn = t.closest('.wppoppop-front-btn.wppoppop-close-trigger');
        if (!btn) return;
        e.preventDefault();
        var wrap = btn.closest('.wppoppop-popup-wrap');
        var uid = wrap ? wrap.getAttribute('data-uid') : '';
        if (window.WpPopPopFrontDisplay && typeof window.WpPopPopFrontDisplay.close === 'function' && uid) {
            window.WpPopPopFrontDisplay.close(uid);
        } else if (wrap) {
            wrap.style.display = 'none';
        }
    }, false);
})(window, document);
/* === WPPOPPOP_BUTTON_CLOSE_END === */
