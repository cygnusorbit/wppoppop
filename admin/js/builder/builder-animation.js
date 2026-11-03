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

/* === WPPOPPOP_SHAPE_INTERACTION_ENGINE_START === */
(function() {
    'use strict';

    // 1. Toggle-Slide Click Handler & Sync
    document.addEventListener('click', function(e) {
        var toggle = e.target.closest('.wppoppop-toggle-switch, .wppoppop-switch, .wppoppop-toggle-slide');
        if (!toggle) return;

        var checkbox = toggle.querySelector('input[type="checkbox"]');
        if (!checkbox) return;

        // If click was directly on the input, allow natural browser state change
        if (e.target !== checkbox) {
            checkbox.checked = !checkbox.checked;
            var changeEvt = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvt);
        }

        if (checkbox.checked) {
            toggle.classList.add('is-active', 'active');
        } else {
            toggle.classList.remove('is-active', 'active');
        }
    }, true);

    // 2. Shape Background Color Field Suppression on Layer Selection
    function updateShapeInspectorView() {
        var inspector = document.querySelector('.wppoppop-inspector, #wppoppop-inspector, .wppoppop-layer-settings');
        if (!inspector) return;

        var activeLayer = document.querySelector('.wppoppop-layer.is-selected, .wppoppop-layer.active, [data-layer-id].active');
        var layerType = activeLayer ? (activeLayer.getAttribute('data-type') || activeLayer.getAttribute('data-element-type') || '') : '';

        if (!layerType) {
            var typeInp = inspector.querySelector('input[name="type"], input[data-prop="type"]');
            if (typeInp) layerType = typeInp.value;
        }

        if (layerType === 'shape') {
            inspector.setAttribute('data-element', 'shape');
            inspector.setAttribute('data-element-type', 'shape');

            // Hide background color controls dynamically
            var bgControls = inspector.querySelectorAll(
                '.wppoppop-control-background, .wppoppop-control-bg-color, [data-prop="background"], [data-prop="background_color"], [data-prop="backgroundColor"]'
            );
            bgControls.forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
            });

            // Ensure Stroke default shows 0 if empty
            var strokeInp = inspector.querySelector('input[name="stroke_width"], input[data-prop="stroke_width"], input[data-prop="stroke"]');
            if (strokeInp && (strokeInp.value === '' || strokeInp.value === undefined)) {
                strokeInp.value = '0';
            }

            // Ensure Border & Corner Radius default show 0 if empty
            var borderInp = inspector.querySelector('input[name="border_width"], input[data-prop="border_width"], input[data-prop="borderRadius"]');
            if (borderInp && (borderInp.value === '' || borderInp.value === undefined)) {
                borderInp.value = '0';
            }
        } else {
            if (inspector.getAttribute('data-element') === 'shape') {
                inspector.removeAttribute('data-element');
                inspector.removeAttribute('data-element-type');
            }
        }
    }

    document.addEventListener('click', function() {
        setTimeout(updateShapeInspectorView, 50);
    });
    window.addEventListener('load', function() {
        setTimeout(updateShapeInspectorView, 100);
    });
})();
/* === WPPOPPOP_SHAPE_INTERACTION_ENGINE_END === */

/* === WPPOPPOP_SHAPE_DEFAULTS_ENFORCER_START === */
(function() {
    'use strict';

    var ZERO_PROPS = [
        'stroke', 'stroke_width', 'strokeWidth', 'stroke-width',
        'border_width', 'borderWidth', 'border-width',
        'border_radius', 'borderRadius', 'border-radius', 'corner_radius', 'cornerRadius', 'radius',
        'shadow_x', 'shadow_y', 'shadow_blur', 'shadow_spread',
        'shadowX', 'shadowY', 'shadowBlur', 'shadowSpread',
        'box_shadow_x', 'box_shadow_y', 'box_shadow_blur', 'box_shadow_spread'
    ];

    function sanitizeShapeModel(element) {
        if (!element) return;
        var type = element.type || element.element_type || element.elementType || '';
        if (type !== 'shape') return;

        // Force stroke to 0 if undefined, null, empty string, or non-explicit
        if (element.stroke_width === undefined || element.stroke_width === null || element.stroke_width === '' || element.stroke_width === 1 || element.stroke_width === '1' || element.stroke_width === '1px') {
            element.stroke_width = 0;
        }
        if (element.stroke === undefined || element.stroke === null || element.stroke === '' || element.stroke === 1 || element.stroke === '1') {
            element.stroke = 0;
        }

        // Force border and corner radius to 0
        if (element.border_width === undefined || element.border_width === null || element.border_width === '') {
            element.border_width = 0;
        }
        if (element.border_radius === undefined || element.border_radius === null || element.border_radius === '') {
            element.border_radius = 0;
        }
        if (element.borderRadius === undefined || element.borderRadius === null || element.borderRadius === '') {
            element.borderRadius = 0;
        }

        // Force all box shadow fields to 0
        ['shadow_x', 'shadow_y', 'shadow_blur', 'shadow_spread'].forEach(function(sp) {
            if (element[sp] === undefined || element[sp] === null || element[sp] === '') {
                element[sp] = 0;
            }
        });
    }

    function syncInspectorInputs() {
        var inspector = document.querySelector('.wppoppop-inspector, #wppoppop-inspector, .wppoppop-sidebar-settings, .wppoppop-layer-settings');
        if (!inspector) return;

        var activeLayer = document.querySelector('.wppoppop-layer.is-selected, .wppoppop-layer.active, [data-layer-id].active, .wppoppop-layer-item.active');
        var layerType = activeLayer ? (activeLayer.getAttribute('data-type') || activeLayer.getAttribute('data-element-type') || activeLayer.getAttribute('data-element') || '') : '';

        if (!layerType) {
            var typeInp = inspector.querySelector('input[name="type"], input[data-prop="type"], [data-setting="type"]');
            if (typeInp) layerType = typeInp.value;
        }

        if (layerType === 'shape') {
            inspector.setAttribute('data-element', 'shape');
            inspector.setAttribute('data-element-type', 'shape');

            // Hide background color controls
            var bgControls = inspector.querySelectorAll(
                '.wppoppop-control-background, .wppoppop-control-bg-color, [data-prop="background"], [data-prop="background_color"], [data-prop="backgroundColor"], .control-background-color'
            );
            bgControls.forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
            });

            // Enforce "0" on empty or uninitialized target fields in inspector DOM
            var inputs = inspector.querySelectorAll('input, select');
            inputs.forEach(function(inp) {
                var prop = (inp.getAttribute('data-prop') || inp.getAttribute('data-setting') || inp.getAttribute('data-key') || inp.name || inp.id || '').toLowerCase();
                for (var i = 0; i < ZERO_PROPS.length; i++) {
                    var zp = ZERO_PROPS[i].toLowerCase();
                    if (prop === zp || prop.endsWith('_' + zp) || prop.endsWith('-' + zp)) {
                        if (inp.value === '' || inp.value === undefined || inp.value === null || inp.value === '1' && (prop.indexOf('stroke') !== -1 || prop.indexOf('border') !== -1)) {
                            // If it defaulted to 1 or empty on a brand new shape, reset to 0
                            if (!activeLayer.getAttribute('data-has-custom-style')) {
                                inp.value = '0';
                                var evt = new Event('input', { bubbles: true });
                                inp.dispatchEvent(evt);
                            }
                        }
                        break;
                    }
                }
            });
        }
    }

    // Intercept builder element addition
    if (typeof window.WpPopPop !== 'undefined' && window.WpPopPop.registerElement) {
        var origReg = window.WpPopPop.registerElement;
        window.WpPopPop.registerElement = function(type, config) {
            if (type === 'shape' && config && config.defaults) {
                config.defaults.stroke = 0;
                config.defaults.stroke_width = 0;
                config.defaults.strokeWidth = 0;
                config.defaults.border_width = 0;
                config.defaults.border_radius = 0;
                config.defaults.borderRadius = 0;
                config.defaults.shadow_x = 0;
                config.defaults.shadow_y = 0;
                config.defaults.shadow_blur = 0;
                config.defaults.shadow_spread = 0;
            }
            return origReg.apply(this, arguments);
        };
    }

    // Bind DOM events
    document.addEventListener('click', function() {
        setTimeout(syncInspectorInputs, 40);
    }, true);

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(syncInspectorInputs, 80);
    });

    window.addEventListener('load', function() {
        setTimeout(syncInspectorInputs, 150);
    });
})();
/* === WPPOPPOP_SHAPE_DEFAULTS_ENFORCER_END === */

/* === WPPOPPOP_SHAPE_STROKE_ZERO_ENGINE_START === */
(function() {
    'use strict';

    function enforceShapeStrokeZero() {
        var inspector = document.querySelector('.wppoppop-inspector, #wppoppop-inspector, .wppoppop-layer-settings');
        if (!inspector) return;

        var activeLayer = document.querySelector('.wppoppop-layer.is-selected, .wppoppop-layer.active, [data-layer-id].active, .wppoppop-layer-item.active');
        var layerType = activeLayer ? (activeLayer.getAttribute('data-type') || activeLayer.getAttribute('data-element-type') || activeLayer.getAttribute('data-element') || '') : '';

        if (!layerType) {
            var typeInp = inspector.querySelector('input[name="type"], input[data-prop="type"], [data-setting="type"]');
            if (typeInp) layerType = typeInp.value;
        }

        if (layerType === 'shape') {
            // Find STROKE WIDTH (PX) inputs in inspector
            var strokeInputs = inspector.querySelectorAll(
                'input[name="stroke_width"], input[name="strokeWidth"], input[name="stroke"], ' +
                'input[data-prop="stroke_width"], input[data-prop="strokeWidth"], input[data-prop="stroke"], ' +
                'input[data-setting="stroke_width"], input[data-key="stroke_width"], #stroke_width'
            );

            strokeInputs.forEach(function(inp) {
                // If input is empty, undefined, or defaulted to 1 on initial load, lock it to 0
                if (inp.value === '' || inp.value === undefined || inp.value === null || (inp.value === '1' && !activeLayer.hasAttribute('data-stroke-user-set'))) {
                    inp.value = '0';
                    inp.setAttribute('value', '0');
                    var evt = new Event('input', { bubbles: true });
                    inp.dispatchEvent(evt);
                }
            });

            // Mark when user explicitly changes stroke
            strokeInputs.forEach(function(inp) {
                if (!inp._strokeTrackerAttached) {
                    inp._strokeTrackerAttached = true;
                    inp.addEventListener('change', function() {
                        if (activeLayer) activeLayer.setAttribute('data-stroke-user-set', 'true');
                    });
                }
            });
        }
    }

    // Intercept builder element registration to force default 0
    if (typeof window.WpPopPop !== 'undefined' && window.WpPopPop.registerElement) {
        var origRegister = window.WpPopPop.registerElement;
        window.WpPopPop.registerElement = function(type, config) {
            if (type === 'shape' && config && config.defaults) {
                config.defaults.stroke_width = 0;
                config.defaults.strokeWidth = 0;
                config.defaults.stroke = 0;
            }
            return origRegister.apply(this, arguments);
        };
    }

    document.addEventListener('click', function() {
        setTimeout(enforceShapeStrokeZero, 40);
    }, true);

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(enforceShapeStrokeZero, 80);
    });

    window.addEventListener('load', function() {
        setTimeout(enforceShapeStrokeZero, 120);
    });
})();
/* === WPPOPPOP_SHAPE_STROKE_ZERO_ENGINE_END === */
