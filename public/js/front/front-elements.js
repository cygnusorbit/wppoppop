(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Elements = {
        init: function() {
            this.initCountdowns();
            this.initSliders();
            this.initRatings();
            this.initWheels();
            this.initScratchCards();
            this.initSignatures();
            this.initTokens();
        },

        initTokens: function() {
            var map = {
                "url": window.location.href,
                "page-title": document.title,
                "user-agent": navigator.userAgent
            };
            $(".wppoppop-const").each(function() {
                var k = $(this).attr("data-token");
                if (map.hasOwnProperty(k) && map[k]) { $(this).text(map[k]); }
            });
        },

        initCountdowns: function() {
            $('.wppoppop-countdown-num').each(function() {
                var $num = $(this);
                var sec = parseInt($num.data('seconds'), 10) || 600;

                var timer = setInterval(function() {
                    if (sec <= 0) {
                        clearInterval(timer);
                        $num.text('00:00');
                        return;
                    }
                    sec--;
                    var m = Math.floor(sec / 60);
                    var s = sec % 60;
                    $num.text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
                }, 1000);
            });
        },

        initSliders: function() {
            $('.wppoppop-range-slider').on('input', function() {
                var val = $(this).val();
                var targetKey = $(this).data('bind');
                var $label = $(this).siblings('.wppoppop-range-val');
                if ($label.length) $label.text(val);

                if (targetKey) {
                    $(this).closest('.wppoppop-popup-wrap').find('[data-token="' + targetKey + '"]').text(val);
                }
            });
        },

        initRatings: function() {
            $('.wppoppop-rating-star').on('click', function() {
                var score = $(this).data('score');
                var $parent = $(this).closest('.wppoppop-rating-stars');
                $parent.find('.wppoppop-rating-star').each(function() {
                    $(this).toggleClass('active', $(this).data('score') <= score);
                });
                $parent.siblings('input[type="hidden"]').val(score);
            });
        },

        initWheels: function() {
            $('.wppoppop-wheel-btn').on('click', function() {
                var $btn = $(this);
                if ($btn.data('spun')) return;
                $btn.data('spun', true).prop('disabled', true);

                var $canvas = $btn.siblings('canvas');
                var slices = $btn.data('slices') || ['10% OFF', 'FREE SHIP', '25% OFF', 'JACKPOT'];
                var winningIdx = Math.floor(Math.random() * slices.length);
                var degrees = 1800 + (winningIdx * (360 / slices.length));

                $canvas.css({
                    transition: 'transform 3.5s cubic-bezier(0.17, 0.67, 0.12, 0.99)',
                    transform: 'rotate(' + degrees + 'deg)'
                });

                setTimeout(function() {
                    var prize = slices[winningIdx];
                    $btn.closest('.wppoppop-popup-wrap').find('[data-token="prize"]').text(prize);
                    $btn.closest('.wppoppop-popup-wrap').find('input[name="prize"]').val(prize);
                    if (window.WpPopPopFront.Elements) {
                        window.WpPopPopFront.Elements.triggerConfetti();
                    }
                }, 3600);
            });
        },

        initScratchCards: function() {
            $('.wppoppop-scratch-canvas').each(function() {
                var canvas = this;
                var ctx = canvas.getContext('2d');
                var w = canvas.width;
                var h = canvas.height;

                ctx.fillStyle = '#94a3b8';
                ctx.fillRect(0, 0, w, h);

                var isDrawing = false;
                var clearedPixels = 0;
                var totalPixels = w * h;

                function scratch(e) {
                    if (!isDrawing) return;
                    var rect = canvas.getBoundingClientRect();
                    var x = (e.clientX || (e.touches && e.touches[0].clientX)) - rect.left;
                    var y = (e.clientY || (e.touches && e.touches[0].clientY)) - rect.top;

                    ctx.globalCompositeOperation = 'destination-out';
                    ctx.beginPath();
                    ctx.arc(x, y, 16, 0, Math.PI * 2);
                    ctx.fill();

                    clearedPixels += 1;
                    if (clearedPixels > 60 && !$(canvas).data('revealed')) {
                        $(canvas).data('revealed', true);
                        if (window.WpPopPopFront.Elements) {
                            window.WpPopPopFront.Elements.triggerConfetti();
                        }
                    }
                }

                $(canvas).on('mousedown touchstart', function() { isDrawing = true; });
                $(canvas).on('mouseup touchend', function() { isDrawing = false; });
                $(canvas).on('mousemove touchmove', scratch);
            });
        },

        initSignatures: function() {
            $('.wppoppop-signature-canvas').each(function() {
                var canvas = this;
                var ctx = canvas.getContext('2d');
                var isDrawing = false;

                function draw(e) {
                    if (!isDrawing) return;
                    var rect = canvas.getBoundingClientRect();
                    var x = (e.clientX || (e.touches && e.touches[0].clientX)) - rect.left;
                    var y = (e.clientY || (e.touches && e.touches[0].clientY)) - rect.top;

                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.strokeStyle = '#1e293b';
                    ctx.lineTo(x, y);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.moveTo(x, y);
                }

                $(canvas).on('mousedown touchstart', function(e) {
                    isDrawing = true;
                    ctx.beginPath();
                    draw(e);
                });
                $(canvas).on('mouseup touchend', function() {
                    isDrawing = false;
                    ctx.beginPath();
                    var dataUrl = canvas.toDataURL('image/png');
                    $(canvas).siblings('input[type="hidden"]').val(dataUrl);
                });
                $(canvas).on('mousemove touchmove', draw);
            });
        },

        triggerConfetti: function() {
            var canvas = document.createElement('canvas');
            canvas.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:9999999;';
            document.body.appendChild(canvas);

            var ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            var particles = [];
            var colors = ['#ec4899', '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'];
            for (var i = 0; i < 75; i++) {
                particles.push({
                    x: canvas.width / 2,
                    y: canvas.height / 2,
                    vx: (Math.random() - 0.5) * 12,
                    vy: (Math.random() - 0.8) * 12,
                    size: Math.random() * 6 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)]
                });
            }

            var frame = 0;
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for (var j = 0; j < particles.length; j++) {
                    var p = particles[j];
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.25;
                    ctx.fillStyle = p.color;
                    ctx.fillRect(p.x, p.y, p.size, p.size);
                }
                frame++;
                if (frame < 80) {
                    requestAnimationFrame(animate);
                } else {
                    canvas.remove();
                }
            }
            animate();
        }
    };

    window.WpPopPopFront.Elements = Elements;
    $(function() { Elements.initTokens(); });
})(window, jQuery);

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
