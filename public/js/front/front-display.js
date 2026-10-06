(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Display = {
        init: function() {
            var self = this;
            $('.wppoppop-modal-overlay').each(function() {
                self.setupPopup($(this));
            });
        },

        setupPopup: function($popup) {
            var self = this;
            var config = $popup.data('popup-config') || {};
            var triggers = config.triggers || {};

            // 1. Close Button & Backdrop Dismissal
            $popup.find('.wppoppop-modal-close, .wppoppop-modal-backdrop').on('click', function(e) {
                e.preventDefault();
                self.closePopup($popup);
            });

            // 2. Button Action & Multi-Screen Conditional Logic Routing
            $popup.on('click', '.wppoppop-btn-action', function(e) {
                e.preventDefault();
                var $btnEl = $(this).closest('.wppoppop-element');
                var $currentScreen = $btnEl.closest('.wppoppop-screen-viewport');
                var currentScreenId = parseInt($currentScreen.data('screen-id'), 10) || 1;

                self.handleElementAction($popup, $btnEl, currentScreenId, config);
            });

            // 3. Triggers Engine (Page Load, Exit Intent, Scroll)
            if (triggers.on_load) {
                var delay = parseInt(triggers.on_load_delay, 10) * 1000 || 0;
                setTimeout(function() { self.openPopup($popup); }, delay);
            }

            if (triggers.on_exit) {
                var exitFired = false;
                $(document).on('mouseleave', function(e) {
                    if (e.clientY <= 20 && !exitFired) {
                        exitFired = true;
                        self.openPopup($popup);
                    }
                });
            }

            if (triggers.on_scroll) {
                var scrollVal = parseInt(triggers.scroll_val, 10) || 50;
                var scrollFired = false;
                $(window).on('scroll', function() {
                    var sTop = $(window).scrollTop();
                    var docH = $(document).height() - $(window).height();
                    var pct = (sTop / docH) * 100;
                    if (pct >= scrollVal && !scrollFired) {
                        scrollFired = true;
                        self.openPopup($popup);
                    }
                });
            }
        },

        openPopup: function($popup) {
            $popup.fadeIn(200);
            $('body').addClass('wppoppop-lock-scroll');
        },

        closePopup: function($popup) {
            var config = $popup.data('popup-config') || {};
            var screens = config.screens || [];
            var $box = $popup.find('.wppoppop-popup-box');
            var $activeScreen = $popup.find('.wppoppop-screen-viewport:visible');
            var currentScreenId = parseInt($activeScreen.data('screen-id'), 10) || 1;

            var currentScObj = screens.find(function(s) { return s.id === currentScreenId; }) || screens[0] || {};
            var animOut = currentScObj.animOut || 'animate__fadeOut';

            if (animOut && animOut !== 'none') {
                $box.removeClass(function(i, c) { return (c.match(/(^|\s)animate__\S+/g) || []).join(' '); });
                $box.addClass('animate__animated ' + animOut);

                var dur = (parseInt(currentScObj.animInDuration, 10) || 600) / 1000;
                $box.css('--animate-duration', dur + 's');

                setTimeout(function() {
                    $popup.fadeOut(200);
                    $('body').removeClass('wppoppop-lock-scroll');
                }, dur * 1000);
            } else {
                $popup.fadeOut(200);
                $('body').removeClass('wppoppop-lock-scroll');
            }
        },

        handleElementAction: function($popup, $btnEl, currentScreenId, config) {
            var self = this;
            var screens = config.screens || [];
            var currentScObj = screens.find(function(s) { return s.id === currentScreenId; }) || {};
            var targetScreenId = null;

            // 1. Evaluate Screen-Level Conditional Logic from Accordion 1
            if (currentScObj.logic && currentScObj.logic.enable && currentScObj.logic.field) {
                targetScreenId = self.evaluateConditionalLogic($popup, currentScObj.logic, currentScreenId);
            }

            // 2. Default Element Action if Conditional Logic was not met
            if (!targetScreenId) {
                var action = $btnEl.data('action') || 'none';
                if (action === 'jump_screen') {
                    targetScreenId = parseInt($btnEl.data('target-screen'), 10);
                } else if (action === 'next_screen') {
                    var curIdx = screens.findIndex(function(s) { return s.id === currentScreenId; });
                    var nextSc = screens[curIdx + 1] || screens[0];
                    targetScreenId = nextSc.id;
                } else if (action === 'close') {
                    self.closePopup($popup);
                    return;
                } else if (action === 'redirect') {
                    var url = $btnEl.data('url');
                    if (url) {
                        window.location.href = url;
                        return;
                    }
                }
            }

            // 3. Perform Animated Transition to Target Screen
            if (targetScreenId && targetScreenId !== currentScreenId) {
                self.switchScreen($popup, currentScreenId, targetScreenId, config);
            }
        },

        evaluateConditionalLogic: function($popup, logic, currentScreenId) {
            var $fieldEl = $popup.find('#wppoppop-el-' + logic.field);
            if (!$fieldEl.length) return null;

            var val = '';
            var $input = $fieldEl.find('.wppoppop-input, input, select');

            if ($input.is(':checkbox')) {
                val = $input.is(':checked') ? ($input.val() || '1') : '';
            } else if ($input.is(':radio')) {
                val = $fieldEl.find('input:checked').val() || '';
            } else {
                val = ($input.val() || '').trim();
            }

            var op = logic.operator || 'equals';
            var matchVal = (logic.val || '').trim();
            var isMatch = false;

            if (op === 'equals') isMatch = (val.toLowerCase() === matchVal.toLowerCase());
            else if (op === 'not_equals') isMatch = (val.toLowerCase() !== matchVal.toLowerCase());
            else if (op === 'contains') isMatch = (val.toLowerCase().indexOf(matchVal.toLowerCase()) !== -1);
            else if (op === 'greater_than') isMatch = (parseFloat(val) > parseFloat(matchVal));
            else if (op === 'less_than') isMatch = (parseFloat(val) < parseFloat(matchVal));
            else if (op === 'is_empty') isMatch = (!val || val === '');
            else if (op === 'is_not_empty') isMatch = (val && val !== '');

            if (isMatch) {
                return parseInt(logic.targetScreen, 10);
            } else if (logic.fallback) {
                if (logic.fallback === 'close') {
                    this.closePopup($popup);
                    return null;
                }
                return parseInt(logic.fallback, 10) || null;
            }

            return null;
        },

        switchScreen: function($popup, fromId, toId, config) {
            var screens = config.screens || [];
            var fromSc = screens.find(function(s) { return s.id === fromId; }) || {};
            var toSc = screens.find(function(s) { return s.id === toId; });
            if (!toSc) return;

            var $box = $popup.find('.wppoppop-popup-box');
            var $fromScreen = $popup.find('.wppoppop-screen-viewport[data-screen-id="' + fromId + '"]');
            var $toScreen = $popup.find('.wppoppop-screen-viewport[data-screen-id="' + toId + '"]');

            // 1. Play Exit Animation on Current Screen
            var animOut = fromSc.animOut || 'animate__fadeOut';
            var exitDuration = (parseInt(fromSc.animInDuration, 10) || 500) / 1000;

            if (animOut && animOut !== 'none') {
                $box.removeClass(function(i, c) { return (c.match(/(^|\s)animate__\S+/g) || []).join(' '); });
                $box.css('--animate-duration', exitDuration + 's');
                $box.addClass('animate__animated ' + animOut);
            }

            setTimeout(function() {
                // 2. Hide Old Screen, Display New Screen Viewport
                $fromScreen.hide();
                $toScreen.show();

                // 3. Morph Box Dimensions & Background
                $box.css({
                    width: (toSc.width || 640) + 'px',
                    height: (toSc.height || 400) + 'px'
                });

                if (toSc.bgMode === 'gradient') {
                    var deg = toSc.gradAngle || 135;
                    var c1 = toSc.gradColor1 || '#3b82f6';
                    var c2 = toSc.gradColor2 || '#1d4ed8';
                    $box.css('background', 'linear-gradient(' + deg + 'deg, ' + c1 + ', ' + c2 + ')');
                } else {
                    $box.css('background', toSc.bgColor || '#ffffff');
                }

                // 4. Play Entrance Animation for Destination Screen
                var animIn = toSc.animIn || 'animate__fadeIn';
                var enterDuration = (parseInt(toSc.animInDuration, 10) || 800) / 1000;
                var enterDelay = (parseInt(toSc.animInDelay, 10) || 0) / 1000;

                $box.removeClass(function(i, c) { return (c.match(/(^|\s)animate__\S+/g) || []).join(' '); });
                $box.css({
                    '--animate-duration': enterDuration + 's',
                    'animation-delay': enterDelay + 's'
                });

                if (animIn && animIn !== 'none') {
                    $box.addClass('animate__animated ' + animIn);
                }
            }, (animOut !== 'none') ? (exitDuration * 1000 * 0.8) : 50);
        }
    };

    $(document).ready(function() {
        Display.init();
    });

    window.WpPopPopFront.Display = Display;
})(window, jQuery);
