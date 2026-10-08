(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Display = {
        canShow: function(uid, config) {
            var freq = config.frequency || {};
            var epoch = (window.wppoppop_front_vars && window.wppoppop_front_vars.cookie_epoch) || '1';

            if (this.getCookie('wppoppop_epoch_' + uid) !== epoch) {
                this.eraseCookie('wppoppop_closed_' + uid);
                this.eraseCookie('wppoppop_sub_' + uid);
                this.setCookie('wppoppop_epoch_' + uid, epoch, 365);
            }

            if (freq.hide_on_submit && this.getCookie('wppoppop_sub_' + uid)) {
                return false;
            }

            if (freq.mode === 'session') {
                if (sessionStorage.getItem('wppoppop_session_' + uid)) return false;
            } else if (freq.mode === 'days') {
                if (this.getCookie('wppoppop_closed_' + uid)) return false;
            }

            return true;
        },

        show: function($popup, config) {
            var uid = $popup.data('uid');
            $popup.fadeIn(300);
            this.recordImpression(uid);

            if (config.sound && config.sound.enable && window.WpPopPopFront.Gamification) {
                window.WpPopPopFront.Gamification.playChime('open');
            }

            if (config.custom_js) {
                try { (new Function(config.custom_js))(); } catch(e) {}
            }
        },

        close: function($popup, config) {
            var uid = $popup.data('uid');
            var freq = config.frequency || {};

            $popup.fadeOut(250);

            if (freq.mode === 'session') {
                sessionStorage.setItem('wppoppop_session_' + uid, '1');
            } else if (freq.mode === 'days') {
                var days = parseInt(freq.days, 10) || 7;
                this.setCookie('wppoppop_closed_' + uid, '1', days);
            }
        },

        bindEvents: function($popup, config) {
            var self = this;
            var uid = $popup.data('uid');

            $popup.find('.wppoppop-close-btn').on('click', function() {
                self.close($popup, config);
            });

            $popup.on('click', function(e) {
                if (e.target === this) {
                    self.close($popup, config);
                }
            });

            $(document).on('keydown.wppoppop_esc_' + uid, function(e) {
                if (e.key === 'Escape' && $popup.is(':visible')) {
                    self.close($popup, config);
                }
            });

            $('.wppoppop-side-tab[data-target-uid="' + uid + '"]').on('click', function() {
                self.show($popup, config);
            });

            // Multi-Canvas Step Switcher (Dual compatible button classes and targets)
            $popup.on('click', '.wppoppop-next-canvas-btn, .wppoppop-next-screen-btn, .wppoppop-next-step', function(e) {
                e.preventDefault();
                var targetCanvas = parseInt($(this).data('goto-canvas') || $(this).data('goto-screen') || $(this).data('goto'), 10) || 2;
                self.switchCanvas($popup, targetCanvas);
            });
        },

        switchCanvas: function($popup, canvasNum) {
            canvasNum = parseInt(canvasNum, 10) || 1;

            // Hide all canvas containers
            $popup.find('.wppoppop-canvas-container, .wppoppop-screen-container')
                .hide()
                .removeClass('wppoppop-canvas-active wppoppop-screen-active');

            // Select active canvas container
            var $target = $popup.find('.wppoppop-canvas-container[data-canvas-index="' + canvasNum + '"], .wppoppop-screen-container[data-screen-index="' + canvasNum + '"]');
            if ($target.length) {
                $target.fadeIn(200).addClass('wppoppop-canvas-active wppoppop-screen-active');
            } else {
                $popup.find('.wppoppop-canvas-container, .wppoppop-screen-container')
                    .first()
                    .fadeIn(200)
                    .addClass('wppoppop-canvas-active wppoppop-screen-active');
            }

            // Update Progress Bar if present
            var totalCanvases = $popup.find('.wppoppop-canvas-container, .wppoppop-screen-container').length || 1;
            var pct = Math.min(100, Math.round((canvasNum / totalCanvases) * 100));
            $popup.find('.wppoppop-progress-fill').css('width', pct + '%');
        },

        switchScreen: function($popup, screenNum) {
            this.switchCanvas($popup, screenNum);
        },

        recordImpression: function(uid) {
            var restUrl = (window.wppoppop_front_vars && window.wppoppop_front_vars.rest_url) || '';
            var ajaxUrl = (window.wppoppop_front_vars && window.wppoppop_front_vars.ajax_url) || '';

            if (restUrl) {
                fetch(restUrl + 'impression', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ uid: uid })
                }).catch(function() {});
            } else if (ajaxUrl) {
                $.post(ajaxUrl, { action: 'wppoppop_record_impression', uid: uid });
            }

            if (window.gtag) gtag('event', 'wppoppop_impression', { popup_uid: uid });
            if (window.dataLayer) window.dataLayer.push({ event: 'wppoppop_impression', popup_uid: uid });
        },

        setCookie: function(name, value, days) {
            var expires = '';
            if (days) {
                var d = new Date();
                d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + d.toUTCString();
            }
            document.cookie = name + '=' + (value || '') + expires + '; path=/; SameSite=Lax';
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
        },

        eraseCookie: function(name) {
            document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }
    };

    window.WpPopPopFront.Display = Display;
})(window, jQuery);
