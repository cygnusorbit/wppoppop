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

            // Trigger initial canvas entrance animation (Animate.css)
            var $firstCanvas = $popup.find('.wppoppop-canvas-container, .wppoppop-screen-container').first();
            if ($firstCanvas.length) {
                this.triggerCanvasAnimation($firstCanvas);
            }

            if (config.custom_js) {
                try { (new Function(config.custom_js))(); } catch(e) {}
            }
        },

        triggerCanvasAnimation: function($canvas) {
            var appearance = ($canvas.data('anim-appearance') || 'fadeIn').toString().trim();
            var duration = parseInt($canvas.data('anim-duration'), 10) || 1000;
            var delay = parseInt($canvas.data('anim-delay'), 10) || 0;

            if (appearance === 'none') {
                $canvas.show().addClass('wppoppop-canvas-active wppoppop-screen-active');
                return;
            }

            // Support both standard Animate.css names (fadeIn, slideInDown) and legacy prefixes (fade, slideDown)
            var animClass = appearance;
            if (animClass.indexOf('animate__') !== 0) {
                if (animClass === 'fade') animClass = 'fadeIn';
                if (animClass === 'slideDown') animClass = 'slideInDown';
                if (animClass === 'slideUp') animClass = 'slideInUp';
                if (animClass === 'slideLeft') animClass = 'slideInLeft';
                if (animClass === 'slideRight') animClass = 'slideInRight';
                animClass = 'animate__' + animClass;
            }

            $canvas.css({
                '--animate-duration': (duration / 1000) + 's',
                '--animate-delay': (delay / 1000) + 's'
            });

            $canvas.show().addClass('wppoppop-canvas-active wppoppop-screen-active animate__animated ' + animClass);
        },

        close: function($popup, config) {
            var self = this;
            var uid = $popup.data('uid');
            var freq = config.frequency || {};

            var $activeCanvas = $popup.find('.wppoppop-canvas-container.wppoppop-canvas-active, .wppoppop-screen-container.wppoppop-screen-active');
            var disappearance = ($activeCanvas.data('anim-disappearance') || 'fadeOut').toString().trim();

            if (disappearance !== 'none') {
                var exitClass = disappearance;
                if (exitClass.indexOf('animate__') !== 0) {
                    if (exitClass === 'fade') exitClass = 'fadeOut';
                    if (exitClass === 'slideDown') exitClass = 'slideOutDown';
                    if (exitClass === 'slideUp') exitClass = 'slideOutUp';
                    if (exitClass === 'slideLeft') exitClass = 'slideOutLeft';
                    if (exitClass === 'slideRight') exitClass = 'slideOutRight';
                    exitClass = 'animate__' + exitClass;
                }

                $activeCanvas.addClass('animate__animated ' + exitClass);
                setTimeout(function() {
                    $popup.fadeOut(200);
                }, 400);
            } else {
                $popup.fadeOut(250);
            }

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

            $popup.on('click', '.wppoppop-next-canvas-btn, .wppoppop-next-screen-btn, .wppoppop-next-step', function(e) {
                e.preventDefault();
                var targetCanvas = parseInt($(this).data('goto-canvas') || $(this).data('goto-screen') || $(this).data('goto'), 10) || 2;
                self.switchCanvas($popup, targetCanvas);
            });
        },

        switchCanvas: function($popup, canvasNum) {
        var $containers = $popup.find('.wppoppop-canvas-container, .wppoppop-screen-container');
        var $target = $popup.find('.wppoppop-canvas-container[data-canvas-index="' + canvasNum + '"], .wppoppop-screen-container[data-screen-index="' + canvasNum + '"]');
        if (!$target.length) return this;

        var $box = $popup.find('.wppoppop-modal-box');
        var $current = $containers.filter('.is-active');

        // Play disappearance animation on outgoing canvas if defined
        var curDis = $current.data('anim-disappearance');
        if (curDis && curDis !== 'none') {
            $current.addClass('animate__animated animate__' + curDis);
        }

        $containers.removeClass('is-active').hide();
        $target.addClass('is-active').css('display', 'block');

        // Dynamic Dimension Adaptation
        var targetW = parseInt($target.data('width'), 10);
        var targetH = parseInt($target.data('height'), 10);
        if (targetW && targetH) {
            $box.css({ width: targetW + 'px', height: targetH + 'px' });
        }

        // Dynamic Background & Shadow Adaptation
        var bgColor = $target.data('bg-color') || $target.attr('data-bg-color');
        var bgMode = $target.data('bg-mode') || $target.attr('data-bg-mode') || 'solid';
        var grad1 = $target.data('grad-color1') || $target.attr('data-grad-color1') || '#3b82f6';
        var grad2 = $target.data('grad-color2') || $target.attr('data-grad-color2') || '#1d4ed8';
        var gradAngle = $target.data('grad-angle') || $target.attr('data-grad-angle') || 135;

        if (bgColor === 'transparent') {
            $box.addClass('wppoppop-canvas-transparent');
            $box.css({ background: 'transparent', 'box-shadow': 'none' });
        } else {
            $box.removeClass('wppoppop-canvas-transparent');
            if (bgMode === 'gradient') {
                $box.css({
                    background: 'linear-gradient(' + gradAngle + 'deg, ' + grad1 + ', ' + grad2 + ')',
                    'box-shadow': ''
                });
            } else {
                $box.css({
                    background: bgColor || '#ffffff',
                    'box-shadow': ''
                });
            }
        }

        // Play entrance animation on incoming canvas
        var appAnim = $target.data('anim-appearance');
        if (appAnim && appAnim !== 'none') {
            $target.addClass('animate__animated animate__' + appAnim);
        }

        return this;
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
