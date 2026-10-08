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
