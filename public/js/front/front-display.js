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
                if ($wrap.attr('data-inline') === '1') { return; }
                /* WPPOPPOP_BG_CLOSE_JS: backdrop clicks close only when "Click Background to Close" is on */
                if ($(this).hasClass('wppoppop-backdrop') && !$(this).hasClass('wppoppop-close-btn') && $wrap.attr('data-close-backdrop') !== '1') { return; }
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

                if ($wrap.attr('data-manual') === '1') return; /* opened only by button / link / JS API */
                var hasLoad = $wrap.attr('data-trigger-load') === '1';
                var hasOther = $wrap.is('[data-trigger-exit],[data-trigger-scroll],[data-trigger-idle],[data-trigger-video-end],[data-tt-event],[data-video-listen]');
                if (!hasLoad && hasOther) return;
                var loadDelay = hasLoad ? (parseFloat($wrap.attr('data-trigger-load-delay')) || 0) * 1000 : 0;
                if (loadDelay > 0) {
                    setTimeout(function() { self.show(uid); }, loadDelay);
                } else {
                    self.show(uid);
                }
            });
        },
        /* WPPOPPOP_IMPRESSION_BEACON: count one impression per popup per page view */
        trackImpression: function(uid) {
            this._impSent = this._impSent || {};
            if (!uid || this._impSent[uid]) return;
            this._impSent[uid] = true;
            var url = vars.rest_url || '';
            if (!url) {
                var l = document.querySelector('link[rel="https://api.w.org/"]');
                url = l && l.href ? l.href : '/wp-json/';
            }
            url = url.replace(/\/?$/, '/') + 'wppoppop/v1/impression';
            var body = JSON.stringify({ uid: uid });
            try {
                if (navigator.sendBeacon && navigator.sendBeacon(url, new Blob([body], { type: 'application/json' }))) return;
            } catch (e) {}
            try {
                $.ajax({ url: url, method: 'POST', contentType: 'application/json', data: body });
            } catch (e2) {}
        },

        show: function(uid) {
            var $wrap = this.getPopupWrap(uid);
            if (!$wrap.length) return;

            $wrap.data('wppoppopClosing', false);
            $wrap.children('.wppoppop-backdrop').css({ transition: '', '-webkit-transition': '', opacity: '' }); /* WPPOPPOP_CLOSE_FADE */
            if ($wrap.is('[data-wpp-noscroll]')) { $('html').addClass('wppoppop-no-scroll'); } /* WPPOPPOP_PROTECT_SCROLL */
            $wrap.stop(true, true).fadeIn(250).css('display', 'flex').attr('aria-hidden', 'false');
            this.playCanvasIn($wrap.find('.wppoppop-canvas-active').first(), $wrap.find('.wppoppop-box').first());
            try { this.initExtensionWidgets(); } catch (eW) {} /* WPPOPPOP_DATE_REINIT: library may load after bootstrap */

            this.trackImpression(uid);

            $(document).trigger('wppoppop:popup_shown', [uid]);
        },

        close: function(uid) {
            var $wrap = this.getPopupWrap(uid);
            if (!$wrap.length) return;

            if ($wrap.data('wppoppopClosing')) return;
            $wrap.data('wppoppopClosing', true);
            if (!$('[data-wpp-noscroll]').not($wrap).filter(':visible').length) { $('html').removeClass('wppoppop-no-scroll'); } /* WPPOPPOP_PROTECT_SCROLL */

            var self = this;
            var $c = $wrap.find('.wppoppop-canvas-active').first();
            var exitOn = $c.attr('data-anim-exit') !== '0';
            var outFx = exitOn ? this.fxName($c.attr('data-anim-disappearance'), 'out') : '';
            var outDur = parseInt($c.attr('data-anim-duration'), 10);
            if (isNaN(outDur) || outDur <= 0) outDur = 500;
            outDur = Math.min(outDur, 800);

            if (outFx && $c.length) {
                /* WPPOPPOP_CLOSE_FADE: the dim backdrop fades in step with the exit effect, then the wrapper hides with no extra pass */
                var $bd = $wrap.children('.wppoppop-backdrop').first();
                var bd = $bd.length ? $bd[0] : null;
                if (bd) {
                    var bdT = 'opacity ' + outDur + 'ms ease-out';
                    bd.style.webkitTransition = bdT;
                    bd.style.transition = bdT;
                    void bd.offsetWidth;
                    bd.style.opacity = '0';
                }
                this.runFx($wrap.find('.wppoppop-box').first(), outFx, outDur, 0, true);
                setTimeout(function() {
                    if (!$wrap.data('wppoppopClosing')) { return; }
                    $wrap.hide(0).attr('aria-hidden', 'true');
                    self.clearFx($wrap.find('.wppoppop-box').first());
                    if (bd) {
                        bd.style.webkitTransition = '';
                        bd.style.transition = '';
                        bd.style.opacity = '';
                    }
                }, outDur + 30);
            } else {
                $wrap.hide(0).attr('aria-hidden', 'true');
            }

            try {
                localStorage.setItem('wppoppop_dismissed_' + uid, Date.now());
            } catch(e) {}

            $(document).trigger('wppoppop:popup_closed', [uid]);
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

        triggerCelebration: function() {
            /* WPPOPPOP_CELEBRATE: an explicit per-popup effect replaces the legacy global one */
            if (window.WpPopPopFront && window.WpPopPopFront.celebrationMode && window.WpPopPopFront.celebrationMode !== 'default') { return; }
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
                    /* WPPOPPOP_DATE_LOCALE_INLINE: built-in English locale (library default is not English) */
                    opts.locale = {
                        days: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                        daysShort: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
                        daysMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
                        months: ['January','February','March','April','May','June','July','August','September','October','November','December'],
                        monthsShort: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                        today: 'Today',
                        clear: 'Clear',
                        dateFormat: 'MM/dd/yyyy',
                        timeFormat: 'hh:mm aa',
                        firstDay: 0
                    };
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


/* === WPPOPPOP_TRIGGER_REPEAT_START: "always on page load" + "re-appear every X hours" === */
(function(window, $) {
    'use strict';
    var D = window.WpPopPopFrontDisplay;
    if (!D || D._repeatWrapped) return;
    D._repeatWrapped = true;

    var timers = {};
    function key(uid) { return 'wppoppop_dismissed_' + uid; }
    function getTs(uid) { try { return parseInt(localStorage.getItem(key(uid)), 10) || 0; } catch (e) { return 0; } }
    function clr(uid) { try { localStorage.removeItem(key(uid)); } catch (e) {} }

    function waitUntil(uid, due) {
        clearTimeout(timers[uid]);
        var left = due - Date.now();
        if (left <= 0) { clr(uid); D.show(uid); return; }
        timers[uid] = setTimeout(function() { waitUntil(uid, due); }, Math.min(left, 2147000000)); /* setTimeout max ~24.8d */
    }

    var origEval = D.evaluateTriggers;
    D.evaluateTriggers = function() {
        $('.wppoppop-popup-wrap[data-uid]').each(function() {
            var $w = $(this), uid = $w.attr('data-uid');
            if (!uid || $w.attr('data-inline') === '1') { return; }
            if ($w.attr('data-trigger-always') === '1') { clr(uid); return; }
            var h = parseFloat($w.attr('data-repeat-hours')) || 0;
            if (h > 0) {
                var ts = getTs(uid);
                if (!ts) { return; }
                var due = ts + h * 3600000;
                if (Date.now() >= due) {
                    clr(uid);
                    if ($w.attr('data-trigger-load') !== '1') { D.show(uid); }
                } else {
                    waitUntil(uid, due);
                }
            }
        });
        return origEval.apply(this, arguments);
    };

    var origClose = D.close;
    D.close = function(uid) {
        var r = origClose.apply(this, arguments);
        var $w = D.getPopupWrap(uid);
        var h = parseFloat($w.attr('data-repeat-hours')) || 0;
        if (h > 0 && $w.attr('data-inline') !== '1') {
            waitUntil(uid, Date.now() + h * 3600000);
        }
        return r;
    };
})(window, jQuery);
/* === WPPOPPOP_TRIGGER_REPEAT_END === */


/* === WPPOPPOP_VIDEO_FACADE_START === */
(function (window, document) {
    'use strict';
    if (window.__wppoppopVideoFacade) { return; }
    window.__wppoppopVideoFacade = true;

    function play(node) {
        var src = node.getAttribute('data-wppoppop-embed');
        if (!src || node.getAttribute('data-wppoppop-played')) { return; }
        node.setAttribute('data-wppoppop-played', '1');
        var f = document.createElement('iframe');
        f.setAttribute('src', src);
        f.setAttribute('frameborder', '0');
        f.setAttribute('allow', node.getAttribute('data-wppoppop-allow') || 'autoplay; fullscreen; picture-in-picture');
        f.setAttribute('allowfullscreen', '');
        f.style.cssText = 'width:100%;height:100%;border:0;border-radius:inherit;display:block;background:#000;';
        while (node.firstChild) { node.removeChild(node.firstChild); }
        node.appendChild(f);
    }

    function facadeOf(t) {
        while (t && t !== document) {
            if (t.nodeType === 1 && t.className && String(t.className).indexOf('wppoppop-video-facade') !== -1) { return t; }
            t = t.parentNode;
        }
        return null;
    }

    document.addEventListener('click', function (e) {
        var n = facadeOf(e.target);
        if (n) { e.preventDefault(); play(n); }
    }, true);

    document.addEventListener('keydown', function (e) {
        var k = e.key || e.keyCode;
        if (k !== 'Enter' && k !== ' ' && k !== 13 && k !== 32) { return; }
        var n = facadeOf(e.target);
        if (n) { e.preventDefault(); play(n); }
    }, true);
})(window, document);
/* === WPPOPPOP_VIDEO_FACADE_END === */
