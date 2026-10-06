(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Modal = {
        audioCtx: null,

        init: function() {
            this.bindCloseButtons();
            this.bindEscKey();
            this.initAutoLoad();
        },

        getVars: function() {
            return window.wppoppop_front_vars || {
                cookie_epoch: 1,
                ajax_url: '',
                rest_url: '',
                nonce: ''
            };
        },

        isSuppressed: function(uid) {
            var vars = this.getVars();
            var epoch = vars.cookie_epoch || 1;
            var closedCookie = this.getCookie('wppoppop_closed_' + uid);
            var submitCookie = this.getCookie('wppoppop_sub_' + uid);

            if (closedCookie && parseInt(closedCookie, 10) >= epoch) return true;
            if (submitCookie && parseInt(submitCookie, 10) >= epoch) return true;
            if (sessionStorage.getItem('wppoppop_session_' + uid)) return true;
            return false;
        },

        open: function(uid) {
            if (this.isSuppressed(uid)) return;

            var $wrap = $('#wppoppop-popup-' + uid);
            if (!$wrap.length) return;

            var $overlay = $wrap.closest('.wppoppop-overlay');
            $overlay.addClass('wppoppop-visible');
            $wrap.show();

            var anim = $wrap.data('anim-entrance') || 'fade';
            $wrap.removeClass('anim-fadeOut anim-slideUp').addClass('anim-' + anim);

            this.playChime('entrance');
        },

        close: function(uid) {
            var $wrap = $('#wppoppop-popup-' + uid);
            var $overlay = $wrap.closest('.wppoppop-overlay');

            $wrap.addClass('anim-fadeOut');
            setTimeout(function() {
                $overlay.removeClass('wppoppop-visible');
                $wrap.hide().removeClass('anim-fadeOut');
            }, 250);

            var freqMode = $wrap.data('freq-mode') || 'always';
            var days = parseInt($wrap.data('freq-days'), 10) || 7;
            var vars = this.getVars();

            if (freqMode === 'session') {
                sessionStorage.setItem('wppoppop_session_' + uid, '1');
            } else if (freqMode === 'days') {
                this.setCookie('wppoppop_closed_' + uid, vars.cookie_epoch || 1, days);
            }
        },

        bindCloseButtons: function() {
            var self = this;
            $(document).on('click', '.wppoppop-close-btn', function(e) {
                e.preventDefault();
                var uid = $(this).closest('.wppoppop-popup-wrap').data('uid');
                self.close(uid);
            });

            $(document).on('click', '.wppoppop-overlay', function(e) {
                if (e.target === this) {
                    var $wrap = $(this).find('.wppoppop-popup-wrap');
                    if ($wrap.data('close-backdrop') !== 0) {
                        self.close($wrap.data('uid'));
                    }
                }
            });
        },

        bindEscKey: function() {
            var self = this;
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    $('.wppoppop-overlay.wppoppop-visible').each(function() {
                        var $wrap = $(this).find('.wppoppop-popup-wrap');
                        if ($wrap.data('close-esc') !== 0) {
                            self.close($wrap.data('uid'));
                        }
                    });
                }
            });
        },

        initAutoLoad: function() {
            var self = this;
            $('.wppoppop-popup-wrap[data-trigger-load="1"]').each(function() {
                var uid = $(this).data('uid');
                var delay = (parseFloat($(this).data('trigger-delay')) || 0) * 1000;
                setTimeout(function() {
                    self.open(uid);
                }, delay);
            });
        },

        playChime: function(type) {
            try {
                var AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                if (!this.audioCtx) this.audioCtx = new AudioContext();

                var osc = this.audioCtx.createOscillator();
                var gain = this.audioCtx.createGain();
                osc.connect(gain);
                gain.connect(this.audioCtx.destination);

                var now = this.audioCtx.currentTime;
                if (type === 'entrance') {
                    osc.frequency.setValueAtTime(320, now);
                    osc.frequency.exponentialRampToValueAtTime(580, now + 0.18);
                    gain.gain.setValueAtTime(0.08, now);
                    gain.gain.linearRampToValueAtTime(0.001, now + 0.22);
                    osc.start(now);
                    osc.stop(now + 0.22);
                } else if (type === 'success') {
                    osc.frequency.setValueAtTime(440, now);
                    osc.frequency.exponentialRampToValueAtTime(880, now + 0.25);
                    gain.gain.setValueAtTime(0.12, now);
                    gain.gain.linearRampToValueAtTime(0.001, now + 0.3);
                    osc.start(now);
                    osc.stop(now + 0.3);
                }
            } catch(e) {}
        },

        setCookie: function(name, val, days) {
            var expires = '';
            if (days) {
                var d = new Date();
                d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + d.toUTCString();
            }
            document.cookie = name + '=' + (val || '') + expires + '; path=/; SameSite=Lax';
        },

        getCookie: function(name) {
            var nameEQ = name + '=';
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
    };

    window.WpPopPopFront.Modal = Modal;
})(window, jQuery);
