/**
 * WpPopPop Builder - Dedicated Standalone Animation Controller
 * Autonomous engine for keyframe execution, reflow triggers, alias normalization, and preview orchestration.
 */
(function(window, $) {
    'use strict';

    var WpPopPopBuilderAnimation = {
        init: function() {
            this.bindPermanentEvents();
        },

        getAnimationClass: function(effect) {
            if (!effect || effect === 'none') return '';
            var e = String(effect).trim();
            if (e.indexOf('anim-') === 0 || e.indexOf('wppoppop-anim-') === 0 || e.indexOf('animate__') === 0) {
                return e;
            }
            return 'anim-' + e;
        },

        resolveFxName: function(effect) {
            if (!effect || effect === 'none') return '';
            var e = String(effect).trim().replace(/^animate__/, '').replace(/^wppoppop-anim-/, '').replace(/^anim-/, '');
            var map = {
                fade: 'fadeIn', slideDown: 'slideInDown', slideUp: 'slideInUp',
                slideLeft: 'slideInRight', slideRight: 'slideInLeft',
                zoom: 'zoomIn', flip: 'flipInX', bounceInLeft: 'bounceInLeft'
            };
            return 'wppoppop-fx-' + (map[e] || e);
        },

        syncAliases: function(el) {
            if (!el) return el;
            var eff = el.animIn || el.animation || 'none';
            el.anim_appearance = eff; el.animEffect = eff; el.anim_effect = eff;
            el.anim_disappearance = el.animOut || 'none';
            if (el.animDuration !== undefined) el.anim_duration = el.animDuration;
            if (el.animDelay !== undefined) el.anim_delay = el.animDelay;
            return el;
        },

        normalizeAliases: function(el) {
            if (!el || typeof el !== 'object') return { effect: 'none', duration: 1000, delay: 0, disappearance: 'none' };
            var eff = el.animIn || el.anim_appearance || el.animation || el.animEffect || el.anim_effect || 'none';
            var dur = parseInt(el.animDuration || el.anim_duration || el.duration || 1000, 10);
            var del = parseInt(el.animDelay || el.anim_delay || el.delay || 0, 10);
            var dis = el.anim_disappearance || el.disappearance || 'none';
            el.anim_appearance = eff;
            el.animation = eff;
            el.animEffect = eff;
            el.anim_effect = eff;
            el.anim_duration = dur;
            el.anim_delay = del;
            el.anim_disappearance = dis;
            return { effect: eff, duration: dur, delay: del, disappearance: dis };
        },

        playElement: function(target, effect, duration, delay) {
            var $node = null;
            if (typeof target === 'string' || typeof target === 'number') {
                $node = $('#el-' + target);
                if (!$node.length) $node = $('[data-id="' + target + '"]');
                if (!$node.length) $node = $('#wppoppop-el-' + target);
            } else if (target instanceof $) {
                $node = target;
            } else if (target && target.nodeType) {
                $node = $(target);
            }

            if (!$node || !$node.length) return;

            var id = $node.data('id') || ($node.attr('id') || '').replace(/^el-|^wppoppop-el-/, '');
            var activeEl = null;
            if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
                var c = window.WpPopPopBuilderCore.state.currentCanvas || 1;
                var list = (window.WpPopPopBuilderCore.state.canvases && window.WpPopPopBuilderCore.state.canvases[c]) || [];
                for (var i = 0; i < list.length; i++) {
                    if (String(list[i].id) === String(id)) {
                        activeEl = list[i];
                        break;
                    }
                }
            }

            if (!effect && activeEl) {
                var norm = this.normalizeAliases(activeEl);
                effect = norm.effect;
                if (typeof duration === 'undefined') duration = norm.duration;
                if (typeof delay === 'undefined') delay = norm.delay;
            }

            effect = effect || 'none';
            if (effect === 'none' || !effect) return;

            duration = typeof duration !== 'undefined' ? parseInt(duration, 10) : 1000;
            delay = typeof delay !== 'undefined' ? parseInt(delay, 10) : 0;
            if (isNaN(duration) || duration <= 0) duration = 1000;
            if (isNaN(delay) || delay < 0) delay = 0;

            var baseClass = this.getAnimationClass(effect);
            var cleanClass = baseClass.replace(/^anim-|^wppoppop-anim-|^animate__/, '');

            var classAttr = $node.attr('class') || '';
            var filteredClasses = classAttr.split(/\s+/).filter(function(cls) {
                return !cls.match(/^anim-/) && !cls.match(/^wppoppop-anim-/) && !cls.match(/^animate__/) && !cls.match(/^wppoppop-fx-/);
            }).join(' ');
            $node.attr('class', filteredClasses);

            if ($node[0]) {
                void $node[0].offsetWidth;
            }

            $node.css({
                'animation-duration': duration + 'ms',
                '-webkit-animation-duration': duration + 'ms',
                'animation-delay': delay + 'ms',
                '-webkit-animation-delay': delay + 'ms',
                'animation-fill-mode': 'both',
                '-webkit-animation-fill-mode': 'both'
            });

            $node.addClass('wppoppop-fx-run ' + WpPopPopBuilderAnimation.resolveFxName(effect));
            $node.css({'animation-name': WpPopPopBuilderAnimation.resolveFxName(effect), '-webkit-animation-name': WpPopPopBuilderAnimation.resolveFxName(effect)});

            var totalTime = duration + delay + 80;
            clearTimeout($node.data('wppoppop_anim_timer'));
            var timer = setTimeout(function() {
                var currentClasses = ($node.attr('class') || '').split(/\s+/).filter(function(cls) {
                    return !cls.match(/^anim-/) && !cls.match(/^wppoppop-anim-/) && !cls.match(/^animate__/) && !cls.match(/^wppoppop-fx-/);
                }).join(' ');
                $node.attr('class', currentClasses);
                $node.css({
                    'animation-duration': '',
                    '-webkit-animation-duration': '',
                    'animation-delay': '',
                    '-webkit-animation-delay': '',
                    'animation-fill-mode': '',
                    '-webkit-animation-fill-mode': '', 'animation-name': '', '-webkit-animation-name': ''
                });
            }, totalTime);
            $node.data('wppoppop_anim_timer', timer);
        },

        playCanvas: function(effect, duration, delay) {
            var $box = $('#wppoppop-canvas-box');
            if (!$box.length) return;

            if (!effect && window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
                var c = window.WpPopPopBuilderCore.state.currentCanvas || 1;
                var meta = (window.WpPopPopBuilderCore.state.canvasMeta && window.WpPopPopBuilderCore.state.canvasMeta[c]) || {};
                effect = meta.anim_appearance || meta.animation || 'none';
                if (typeof duration === 'undefined') duration = meta.anim_duration;
                if (typeof delay === 'undefined') delay = meta.anim_delay;
            }

            effect = effect || 'none';
            if (effect === 'none' || !effect) return;

            duration = typeof duration !== 'undefined' ? parseInt(duration, 10) : 800;
            delay = typeof delay !== 'undefined' ? parseInt(delay, 10) : 0;
            if (isNaN(duration) || duration <= 0) duration = 800;
            if (isNaN(delay) || delay < 0) delay = 0;

            var baseClass = this.getAnimationClass(effect);
            var cleanClass = baseClass.replace(/^anim-|^wppoppop-anim-|^animate__/, '');

            var classAttr = $box.attr('class') || '';
            var filteredClasses = classAttr.split(/\s+/).filter(function(cls) {
                return !cls.match(/^anim-/) && !cls.match(/^wppoppop-anim-/) && !cls.match(/^animate__/) && !cls.match(/^wppoppop-fx-/);
            }).join(' ');
            $box.attr('class', filteredClasses);

            if ($box[0]) {
                void $box[0].offsetWidth;
            }

            $box.css({
                'animation-duration': duration + 'ms',
                '-webkit-animation-duration': duration + 'ms',
                'animation-delay': delay + 'ms',
                '-webkit-animation-delay': delay + 'ms',
                'animation-fill-mode': 'both',
                '-webkit-animation-fill-mode': 'both'
            });

            $box.addClass('wppoppop-fx-run ' + WpPopPopBuilderAnimation.resolveFxName(effect));
            $box.css({'animation-name': WpPopPopBuilderAnimation.resolveFxName(effect), '-webkit-animation-name': WpPopPopBuilderAnimation.resolveFxName(effect)});

            var totalTime = duration + delay + 80;
            clearTimeout($box.data('wppoppop_anim_timer'));
            var timer = setTimeout(function() {
                var cur = ($box.attr('class') || '').split(/\s+/).filter(function(cls) {
                    return !cls.match(/^anim-/) && !cls.match(/^wppoppop-anim-/) && !cls.match(/^animate__/) && !cls.match(/^wppoppop-fx-/);
                }).join(' ');
                $box.attr('class', cur);
                $box.css({
                    'animation-duration': '',
                    '-webkit-animation-duration': '',
                    'animation-delay': '',
                    '-webkit-animation-delay': '',
                    'animation-fill-mode': '',
                    '-webkit-animation-fill-mode': '', 'animation-name': '', '-webkit-animation-name': ''
                });
            }, totalTime);
            $box.data('wppoppop_anim_timer', timer);
        },

        playAllElements: function() {
            var self = this;
            if (!window.WpPopPopBuilderCore || !window.WpPopPopBuilderCore.state) return;
            var c = window.WpPopPopBuilderCore.state.currentCanvas || 1;
            var elements = (window.WpPopPopBuilderCore.state.canvases && window.WpPopPopBuilderCore.state.canvases[c]) || [];
            elements.forEach(function(el) {
                var norm = self.normalizeAliases(el);
                if (norm.effect && norm.effect !== 'none') {
                    var cm = (window.WpPopPopBuilderCore.state.canvasMeta && window.WpPopPopBuilderCore.state.canvasMeta[c]) || {};
                    var off = 0;
                    if (cm.anim_appearance && cm.anim_appearance !== 'none') {
                        off = (parseInt(cm.anim_delay, 10) || 0) + (parseInt(cm.anim_duration, 10) || 1000);
                    }
                    self.playElement(el.id, norm.effect, norm.duration, norm.delay + off);
                }
            });
        },

        replayPreview: function() {
            var nowTs = Date.now();
            if (this._lastReplay && nowTs - this._lastReplay < 400) return;
            this._lastReplay = nowTs;
            this.playCanvas();
            this.playAllElements();
        },

        bindPermanentEvents: function() {
            var self = this;

            // Element Inspector Play Button
            $(document).off('click.wppoppop_anim_el_play', '#wppoppop-btn-play-elem-anim, #prop-anim-play-btn, #prop-anim-play, .wppoppop-anim-play-btn')
                       .on('click.wppoppop_anim_el_play', '#wppoppop-btn-play-elem-anim, #prop-anim-play-btn, #prop-anim-play, .wppoppop-anim-play-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var activeId = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.activeId) ||
                               ($('#insp-active-element-id').val()) ||
                               ($('.wppoppop-canvas-item.is-selected').data('id'));
                var effect = $('#prop-anim-in').val() || $('#prop-anim-appearance').val();
                var duration = $('#prop-anim-duration').val();
                var delay = $('#prop-anim-delay').val();
                if (activeId) {
                    self.playElement(activeId, effect, duration, delay);
                }
            });

            // Element Inspector Dropdown Change preview
            $(document).off('change.wppoppop_anim_el_select', '#prop-anim-in, #prop-anim-appearance')
                       .on('change.wppoppop_anim_el_select', '#prop-anim-in, #prop-anim-appearance', function(e) {
                var activeId = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.activeId) ||
                               ($('#insp-active-element-id').val()) ||
                               ($('.wppoppop-canvas-item.is-selected').data('id'));
                var effect = $(this).val();
                var duration = $('#prop-anim-duration').val();
                var delay = $('#prop-anim-delay').val();
                if (activeId && effect && effect !== 'none') {
                    self.playElement(activeId, effect, duration, delay);
                }
            });

            // Canvas Settings Play Button
            $(document).off('click.wppoppop_anim_canvas_play', '#set-canvas-anim-play, #set-canvas-anim-test-btn')
                       .on('click.wppoppop_anim_canvas_play', '#set-canvas-anim-play, #set-canvas-anim-test-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var effect = $('#set-canvas-anim-appearance').val() || $('#set-anim-appearance').val();
                var duration = $('#set-canvas-anim-duration').val() || $('#set-anim-duration').val();
                var delay = $('#set-canvas-anim-delay').val() || $('#set-anim-delay').val();
                self.playCanvas(effect, duration, delay);
            });

            // Canvas Settings Dropdown Change preview
            $(document).off('change.wppoppop_anim_canvas_select', '#set-canvas-anim-appearance, #set-anim-appearance')
                       .on('change.wppoppop_anim_canvas_select', '#set-canvas-anim-appearance, #set-anim-appearance', function(e) {
                var effect = $(this).val();
                var duration = $('#set-canvas-anim-duration').val() || $('#set-anim-duration').val();
                var delay = $('#set-canvas-anim-delay').val() || $('#set-anim-delay').val();
                if (effect && effect !== 'none') {
                    self.playCanvas(effect, duration, delay);
                }
            });

            // Preview Mode Replay & Trigger
            $(document).off('click.wppoppop_anim_replay', '#wppoppop-preview-replay-btn')
                       .on('click.wppoppop_anim_replay', '#wppoppop-preview-replay-btn', function(e) {
                e.preventDefault();
                self.replayPreview();
            });

            $(document).off('click.wppoppop_anim_enter_prev', '#wppoppop-btn-preview')
                       .on('click.wppoppop_anim_enter_prev', '#wppoppop-btn-preview', function(e) {
                setTimeout(function() {
                    self.replayPreview();
                }, 100);
            });
        }
    };


    /* UNIFIED_FX_LIST: one list + one engine for canvas and layers */
    var FX_IN = {
        'Attention': ['bounce','flash','pulse','rubberBand','shakeX','shakeY','headShake','swing','tada','wobble','jello','heartBeat','flip'],
        'Back': ['backInDown','backInLeft','backInRight','backInUp'],
        'Bouncing': ['bounceIn','bounceInDown','bounceInLeft','bounceInRight','bounceInUp'],
        'Fading': ['fadeIn','fadeInDown','fadeInDownBig','fadeInLeft','fadeInLeftBig','fadeInRight','fadeInRightBig','fadeInUp','fadeInUpBig','fadeInTopLeft','fadeInTopRight','fadeInBottomLeft','fadeInBottomRight'],
        'Flippers': ['flipInX','flipInY'],
        'Lightspeed': ['lightSpeedInRight','lightSpeedInLeft'],
        'Rotating': ['rotateIn','rotateInDownLeft','rotateInDownRight','rotateInUpLeft','rotateInUpRight'],
        'Specials': ['jackInTheBox','rollIn'],
        'Zooming': ['zoomIn','zoomInDown','zoomInLeft','zoomInRight','zoomInUp'],
        'Sliding': ['slideInDown','slideInLeft','slideInRight','slideInUp']
    };
    var FX_OUT = {
        'Back': ['backOutDown','backOutLeft','backOutRight','backOutUp'],
        'Bouncing': ['bounceOut','bounceOutDown','bounceOutLeft','bounceOutRight','bounceOutUp'],
        'Fading': ['fadeOut','fadeOutDown','fadeOutDownBig','fadeOutLeft','fadeOutLeftBig','fadeOutRight','fadeOutRightBig','fadeOutUp','fadeOutUpBig','fadeOutTopLeft','fadeOutTopRight','fadeOutBottomLeft','fadeOutBottomRight'],
        'Flippers': ['flipOutX','flipOutY'],
        'Lightspeed': ['lightSpeedOutRight','lightSpeedOutLeft'],
        'Rotating': ['rotateOut','rotateOutDownLeft','rotateOutDownRight','rotateOutUpLeft','rotateOutUpRight'],
        'Specials': ['hinge','rollOut'],
        'Zooming': ['zoomOut','zoomOutDown','zoomOutLeft','zoomOutRight','zoomOutUp'],
        'Sliding': ['slideOutDown','slideOutLeft','slideOutRight','slideOutUp']
    };
    var SEL_IN  = '#set-canvas-anim-appearance, #set-anim-appearance, #prop-anim-in';
    var SEL_OUT = '#set-canvas-anim-disappearance, #set-anim-disappearance, #prop-anim-out';

    function fillSelect($sel, groups) {
        $sel.each(function() {
            var $s = $(this);
            if ($s.data('fxFilled')) return;
            var cur = $s.val();
            var legacy = { fade: 'fadeIn', slideDown: 'slideInDown', slideUp: 'slideInUp', slideLeft: 'slideInRight', slideRight: 'slideInLeft', zoom: 'zoomIn' };
            if (cur && legacy[cur] && groups === FX_IN) cur = legacy[cur];
            if (cur === 'fade' && groups === FX_OUT) cur = 'fadeOut';
            var known = false;
            var $frag = $('<div></div>');
            $frag.append($('<option></option>').attr('value', 'none').text('None'));
            Object.keys(groups).forEach(function(g) {
                var $og = $('<optgroup></optgroup>').attr('label', g);
                groups[g].forEach(function(n) {
                    if (n === cur) known = true;
                    $og.append($('<option></option>').attr('value', n).text(n));
                });
                $frag.append($og);
            });
            $s.empty().append($frag.children());
            if (cur && cur !== 'none' && !known) {
                $s.append($('<option></option>').attr('value', cur).text(cur));
            }
            $s.val(cur || 'none');
            $s.data('fxFilled', true);
        });
    }

    WpPopPopBuilderAnimation.populateSelects = function() {
        fillSelect($(SEL_IN), FX_IN);
        fillSelect($(SEL_OUT), FX_OUT);
    };

    WpPopPopBuilderAnimation.playCanvas = function(effect, duration, delay) {
        var $box = $('#wppoppop-canvas-box');
        if (!$box.length) return;
        var core = window.WpPopPopBuilderCore;
        var meta = {};
        if (core && core.state) {
            var c = core.state.currentCanvas || 1;
            meta = (core.state.canvasMeta && core.state.canvasMeta[c]) || {};
        }
        if (!effect) effect = meta.anim_appearance;
        if (typeof duration === 'undefined' || duration === '') duration = meta.anim_duration;
        if (typeof delay === 'undefined' || delay === '') delay = meta.anim_delay;
        if (!effect || effect === 'none') return;
        return this.playElement($box, effect, duration, delay);
    };

    $(function() {
        WpPopPopBuilderAnimation.populateSelects();
        setTimeout(WpPopPopBuilderAnimation.populateSelects, 400);
        setTimeout(WpPopPopBuilderAnimation.populateSelects, 1200);
    });

    window.WpPopPopBuilderAnimation = WpPopPopBuilderAnimation;

    $(document).ready(function() {
        WpPopPopBuilderAnimation.init();
    });

})(window, jQuery);
