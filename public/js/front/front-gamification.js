(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Gamification = {
        audioCtx: null,

        init: function($popup, config) {
            this.initAudioUnlock();
            this.initLuckyWheels($popup);
            this.initScratchCards($popup);
            this.initCountdowns($popup);
        },

        // Helper: Retina / High-DPI Canvas Buffer Scaler
        setupHiDPICanvas: function(canvas, width, height) {
            var dpr = window.devicePixelRatio || 1;
            width = width || canvas.width || 200;
            height = height || canvas.height || 200;

            canvas.width = Math.round(width * dpr);
            canvas.height = Math.round(height * dpr);
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';

            var ctx = canvas.getContext('2d');
            ctx.scale(dpr, dpr);
            return { ctx: ctx, dpr: dpr, width: width, height: height };
        },

        // Safari & Chrome Web Audio Autoplay Gesture Unlock
        initAudioUnlock: function() {
            var self = this;
            var unlock = function() {
                try {
                    var AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext && !self.audioCtx) {
                        self.audioCtx = new AudioContext();
                    }
                    if (self.audioCtx && self.audioCtx.state === 'suspended') {
                        self.audioCtx.resume();
                    }
                } catch(e) {}

                window.removeEventListener('pointerdown', unlock);
                window.removeEventListener('touchstart', unlock);
                window.removeEventListener('click', unlock);
            };

            window.addEventListener('pointerdown', unlock, { passive: true });
            window.addEventListener('touchstart', unlock, { passive: true });
            window.addEventListener('click', unlock, { passive: true });
        },

        playChime: function(type) {
            try {
                var AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                if (!this.audioCtx) this.audioCtx = new AudioContext();

                var ctx = this.audioCtx;
                if (ctx.state === 'suspended') {
                    ctx.resume().catch(function() {});
                }

                if (type === 'open') {
                    [261.63, 329.63, 392.00, 523.25].forEach(function(freq, i) {
                        var osc = ctx.createOscillator();
                        var gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.value = freq;
                        gain.gain.setValueAtTime(0.04, ctx.currentTime + i * 0.08);
                        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + i * 0.08 + 0.3);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(ctx.currentTime + i * 0.08);
                        osc.stop(ctx.currentTime + i * 0.08 + 0.35);
                    });
                } else if (type === 'success' || type === 'win') {
                    [523.25, 659.25, 783.99, 1046.50].forEach(function(freq, i) {
                        var osc = ctx.createOscillator();
                        var gain = ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.value = freq;
                        gain.gain.setValueAtTime(0.08, ctx.currentTime + i * 0.1);
                        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + i * 0.1 + 0.4);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(ctx.currentTime + i * 0.1);
                        osc.stop(ctx.currentTime + i * 0.1 + 0.45);
                    });
                }
            } catch(e) {}
        },

        launchConfetti: function($popup) {
            var canvas = document.createElement('canvas');
            canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:99999;';
            $popup.find('.wppoppop-box').append(canvas);

            var w = canvas.offsetWidth || 640;
            var h = canvas.offsetHeight || 400;
            var hdpi = this.setupHiDPICanvas(canvas, w, h);
            var ctx = hdpi.ctx;

            var pieces = [];
            var colors = ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];

            for (var i = 0; i < 75; i++) {
                pieces.push({
                    x: w / 2,
                    y: h / 2,
                    w: Math.random() * 8 + 4,
                    h: Math.random() * 6 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    vx: (Math.random() - 0.5) * 12,
                    vy: (Math.random() - 0.7) * 14,
                    rot: Math.random() * 360,
                    vRot: (Math.random() - 0.5) * 10
                });
            }

            var frame = 0;
            function animate() {
                ctx.clearRect(0, 0, w, h);
                pieces.forEach(function(p) {
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.35;
                    p.rot += p.vRot;
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate((p.rot * Math.PI) / 180);
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    ctx.restore();
                });

                frame++;
                if (frame < 120) {
                    requestAnimationFrame(animate);
                } else {
                    canvas.remove();
                }
            }
            animate();
        },

        initLuckyWheels: function($popup) {
            var self = this;
            $popup.find('.wppoppop-wheel-wrapper').each(function() {
                var $wrap = $(this);
                var canvas = $wrap.find('.wppoppop-wheel-canvas')[0];
                if (!canvas) return;

                var w = 180;
                var h = 180;
                var hdpi = self.setupHiDPICanvas(canvas, w, h);
                var ctx = hdpi.ctx;

                var slices = JSON.parse(canvas.getAttribute('data-slices') || '["10% OFF","FREE SHIP","5% OFF","TRY AGAIN"]');
                var numSlices = slices.length;
                var arc = (2 * Math.PI) / numSlices;
                var colors = ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4'];

                function drawWheel() {
                    var r = w / 2;
                    ctx.clearRect(0, 0, w, h);
                    for (var i = 0; i < numSlices; i++) {
                        var angle = i * arc;
                        ctx.beginPath();
                        ctx.fillStyle = colors[i % colors.length];
                        ctx.moveTo(r, r);
                        ctx.arc(r, r, r - 4, angle, angle + arc);
                        ctx.lineTo(r, r);
                        ctx.fill();
                        ctx.strokeStyle = '#ffffff';
                        ctx.lineWidth = 2;
                        ctx.stroke();

                        ctx.save();
                        ctx.translate(r, r);
                        ctx.rotate(angle + arc / 2);
                        ctx.textAlign = 'right';
                        ctx.fillStyle = '#ffffff';
                        ctx.font = 'bold 11px -apple-system, sans-serif';
                        ctx.fillText(slices[i], r - 12, 4);
                        ctx.restore();
                    }
                }
                drawWheel();

                var spinning = false;
                $wrap.find('.wppoppop-wheel-spin-btn').on('click', function() {
                    if (spinning) return;
                    spinning = true;
                    var winIdx = Math.floor(Math.random() * numSlices);
                    var prize = slices[winIdx];
                    var extraSpins = 5 * 360;
                    var sliceDeg = 360 / numSlices;
                    var targetDeg = extraSpins + (360 - (winIdx * sliceDeg + sliceDeg / 2));

                    $(canvas).css({
                        transition: 'transform 4s cubic-bezier(0.15, 0.9, 0.25, 1)',
                        transform: 'rotate(' + targetDeg + 'deg)'
                    });

                    setTimeout(function() {
                        spinning = false;
                        $wrap.find('input[name="prize"]').val(prize);
                        if (window.WpPopPopFront.Logic) {
                            window.WpPopPopFront.Logic.setToken($popup, 'prize', prize);
                        }
                        self.playChime('win');
                        self.launchConfetti($popup);
                    }, 4000);
                });
            });
        },

        initScratchCards: function($popup) {
            var self = this;
            $popup.find('.wppoppop-scratch-wrapper').each(function() {
                var $wrap = $(this);
                var canvas = $wrap.find('.wppoppop-scratch-canvas')[0];
                if (!canvas) return;

                var w = $wrap.outerWidth() || 200;
                var h = $wrap.outerHeight() || 100;
                var hdpi = self.setupHiDPICanvas(canvas, w, h);
                var ctx = hdpi.ctx;

                ctx.fillStyle = '#94a3b8';
                ctx.fillRect(0, 0, w, h);
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 12px -apple-system, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('SCRATCH TO REVEAL', w / 2, h / 2 + 4);

                var isDrawing = false;
                var revealed = false;

                function scratch(x, y) {
                    ctx.globalCompositeOperation = 'destination-out';
                    ctx.beginPath();
                    ctx.arc(x, y, 16, 0, Math.PI * 2, false);
                    ctx.fill();

                    if (!revealed) {
                        checkThreshold();
                    }
                }

                function checkThreshold() {
                    try {
                        var imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        var data = imgData.data;
                        var cleared = 0;
                        for (var i = 3; i < data.length; i += 32) {
                            if (data[i] === 0) cleared++;
                        }
                        if (cleared / (data.length / 32) >= 0.40) {
                            revealed = true;
                            $(canvas).fadeOut(300);
                            self.playChime('win');
                            self.launchConfetti($popup);
                        }
                    } catch(e) {}
                }

                // Unified Pointer Events for Touch & Mouse
                $(canvas).on('pointerdown mousedown touchstart', function(e) {
                    isDrawing = true;
                    var rect = canvas.getBoundingClientRect();
                    var clientX = e.clientX || (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0].clientX);
                    var clientY = e.clientY || (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0].clientY);
                    scratch(clientX - rect.left, clientY - rect.top);
                }).on('pointermove mousemove touchmove', function(e) {
                    if (!isDrawing) return;
                    var rect = canvas.getBoundingClientRect();
                    var clientX = e.clientX || (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0].clientX);
                    var clientY = e.clientY || (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0].clientY);
                    scratch(clientX - rect.left, clientY - rect.top);
                }).on('pointerup mouseup touchend pointercancel', function() {
                    isDrawing = false;
                });
            });
        },

        initCountdowns: function($popup) {
            $popup.find('.wppoppop-countdown-timer').each(function() {
                var $timer = $(this);
                var totalSecs = parseInt($timer.data('seconds'), 10) || 900;
                var $mins = $timer.find('.cd-mins');
                var $secs = $timer.find('.cd-secs');

                var interval = setInterval(function() {
                    totalSecs--;
                    if (totalSecs <= 0) {
                        clearInterval(interval);
                        totalSecs = 0;
                    }
                    var m = Math.floor(totalSecs / 60);
                    var s = totalSecs % 60;
                    $mins.text(m < 10 ? '0' + m : m);
                    $secs.text(s < 10 ? '0' + s : s);
                }, 1000);
            });
        }
    };

    window.WpPopPopFront.Gamification = Gamification;
})(window, jQuery);


/* WPPOPPOP_CELEBRATE: selectable success celebration effects */
(function(window, $) {
    'use strict';
    var G = window.WpPopPopFront && window.WpPopPopFront.Gamification;
    if (!G || G.celebrate) return;

    var COLORS = ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];
    var raf = window.requestAnimationFrame || window.webkitRequestAnimationFrame || function(cb) { return window.setTimeout(cb, 16); };

    function runCanvas(spawn, step, ms) {
        var c = document.createElement('canvas');
        c.style.cssText = 'position:fixed;left:0;top:0;width:100%;height:100%;pointer-events:none;z-index:2147483000;';
        var dpr = window.devicePixelRatio || 1;
        var w = window.innerWidth, h = window.innerHeight;
        c.width = Math.round(w * dpr);
        c.height = Math.round(h * dpr);
        document.body.appendChild(c);
        var ctx = c.getContext('2d');
        if (!ctx) { if (c.parentNode) c.parentNode.removeChild(c); return; }
        ctx.scale(dpr, dpr);
        var parts = spawn(w, h);
        var start = new Date().getTime();
        (function frame() {
            var t = new Date().getTime() - start;
            ctx.clearRect(0, 0, w, h);
            var alive = step(ctx, parts, w, h, t, ms);
            if (t < ms && alive !== false) {
                raf(frame);
            } else if (c.parentNode) {
                c.parentNode.removeChild(c);
            }
        })();
    }

    function rnd(a, b) { return a + Math.random() * (b - a); }

    function rain() {
        runCanvas(function(w) {
            var a = [];
            for (var i = 0; i < 140; i++) {
                a.push({ x: rnd(0, w), y: rnd(-400, 0), w: rnd(5, 11), h: rnd(4, 8), vy: rnd(2, 5), vx: rnd(-1, 1),
                         rot: rnd(0, 360), vr: rnd(-8, 8), color: COLORS[i % COLORS.length] });
            }
            return a;
        }, function(ctx, parts, w, h, t, ms) {
            for (var i = 0; i < parts.length; i++) {
                var p = parts[i];
                p.x += p.vx; p.y += p.vy; p.rot += p.vr;
                if (p.y > h + 20 && t < ms - 1500) { p.y = rnd(-60, -10); p.x = rnd(0, w); }
                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate(p.rot * Math.PI / 180);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                ctx.restore();
            }
        }, 3500);
    }

    function starPath(ctx, r) {
        ctx.beginPath();
        for (var i = 0; i < 10; i++) {
            var rad = (i % 2 === 0) ? r : r / 2.4;
            var ang = (Math.PI / 5) * i - Math.PI / 2;
            if (i === 0) ctx.moveTo(Math.cos(ang) * rad, Math.sin(ang) * rad);
            else ctx.lineTo(Math.cos(ang) * rad, Math.sin(ang) * rad);
        }
        ctx.closePath();
    }

    function stars() {
        runCanvas(function(w, h) {
            var a = [];
            for (var i = 0; i < 70; i++) {
                var ang = rnd(0, Math.PI * 2), sp = rnd(3, 11);
                a.push({ x: w / 2, y: h / 2, vx: Math.cos(ang) * sp, vy: Math.sin(ang) * sp - 2, r: rnd(6, 14),
                         rot: rnd(0, 360), vr: rnd(-6, 6), color: COLORS[i % COLORS.length], life: 1 });
            }
            return a;
        }, function(ctx, parts) {
            var any = false;
            for (var i = 0; i < parts.length; i++) {
                var p = parts[i];
                p.x += p.vx; p.y += p.vy; p.vy += 0.18; p.vx *= 0.99; p.rot += p.vr; p.life -= 0.01;
                if (p.life <= 0) continue;
                any = true;
                ctx.save();
                ctx.globalAlpha = Math.max(0, p.life);
                ctx.translate(p.x, p.y);
                ctx.rotate(p.rot * Math.PI / 180);
                ctx.fillStyle = p.color;
                starPath(ctx, p.r);
                ctx.fill();
                ctx.restore();
            }
            return any;
        }, 3000);
    }

    function cannon() {
        if (typeof window.confetti !== 'function') return false;
        try {
            window.confetti({ particleCount: 70, angle: 60, spread: 60, origin: { x: 0, y: 0.8 } });
            window.confetti({ particleCount: 70, angle: 120, spread: 60, origin: { x: 1, y: 0.8 } });
            return true;
        } catch (e) { return false; }
    }

    function fireworks() {
        if (typeof window.Fireworks === 'undefined') return false;
        try {
            var Ctor = window.Fireworks.default || window.Fireworks;
            var fw = new Ctor(document.body, {
                autoresize: true, opacity: 0.5, acceleration: 1.05, friction: 0.97, gravity: 1.5,
                particles: 50, traceLength: 3, traceSpeed: 10, explosion: 5, intensity: 30
            });
            fw.start();
            window.setTimeout(function() { try { fw.stop(); } catch (e) {} }, 2500);
            return true;
        } catch (e) { return false; }
    }

    G.celebrate = function(effect, $popup) {
        effect = effect || 'default';
        var burst = function() { try { G.launchConfetti($popup); } catch (e) {} };
        switch (effect) {
            case 'none':      return;
            case 'burst':     burst(); return;
            case 'cannon':    if (!cannon()) burst(); return;
            case 'fireworks': if (!fireworks()) burst(); return;
            case 'rain':      rain(); return;
            case 'stars':     stars(); return;
            case 'both':      burst(); fireworks(); return;
            default:          burst(); return;
        }
    };
})(window, jQuery);
