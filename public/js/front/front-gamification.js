(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Gamification = {
        init: function($popup, config) {
            this.initLuckyWheels($popup);
            this.initScratchCards($popup);
            this.initCountdowns($popup);
        },

        // Web Audio Synthesizer Chimes
        playChime: function(type) {
            try {
                var AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                var ctx = new AudioContext();

                if (type === 'open') {
                    // Rising major chord
                    [261.63, 329.63, 392.00, 523.25].forEach(function(freq, i) {
                        var osc = ctx.createOscillator();
                        var gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.value = freq;
                        gain.gain.setValueAtTime(0.05, ctx.currentTime + i * 0.08);
                        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + i * 0.08 + 0.3);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(ctx.currentTime + i * 0.08);
                        osc.stop(ctx.currentTime + i * 0.08 + 0.35);
                    });
                } else if (type === 'success' || type === 'win') {
                    // Victory Fanfare
                    [523.25, 659.25, 783.99, 1046.50].forEach(function(freq, i) {
                        var osc = ctx.createOscillator();
                        var gain = ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.value = freq;
                        gain.gain.setValueAtTime(0.1, ctx.currentTime + i * 0.1);
                        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + i * 0.1 + 0.4);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(ctx.currentTime + i * 0.1);
                        osc.stop(ctx.currentTime + i * 0.1 + 0.45);
                    });
                }
            } catch(e) {}
        },

        // HTML5 Particle Confetti Celebration Physics
        launchConfetti: function($popup) {
            var canvas = document.createElement('canvas');
            canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:99999;';
            $popup.find('.wppoppop-box').append(canvas);

            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            var ctx = canvas.getContext('2d');
            var pieces = [];
            var colors = ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];

            for (var i = 0; i < 70; i++) {
                pieces.push({
                    x: canvas.width / 2,
                    y: canvas.height / 2,
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
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                pieces.forEach(function(p) {
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.35; // gravity
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

        // Lucky Prize Wheel
        initLuckyWheels: function($popup) {
            var self = this;
            $popup.find('.wppoppop-wheel-wrapper').each(function() {
                var $wrap = $(this);
                var canvas = $wrap.find('.wppoppop-wheel-canvas')[0];
                if (!canvas) return;
                var ctx = canvas.getContext('2d');
                var slices = JSON.parse(canvas.getAttribute('data-slices') || '["10% OFF","FREE SHIP","5% OFF","TRY AGAIN"]');
                var numSlices = slices.length;
                var arc = (2 * Math.PI) / numSlices;
                var colors = ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4'];

                function drawWheel() {
                    var r = canvas.width / 2;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    for (var i = 0; i < numSlices; i++) {
                        var angle = i * arc;
                        ctx.beginPath();
                        ctx.fillStyle = colors[i % colors.length];
                        ctx.moveTo(r, r);
                        ctx.arc(r, r, r - 5, angle, angle + arc);
                        ctx.lineTo(r, r);
                        ctx.fill();
                        ctx.stroke();

                        ctx.save();
                        ctx.translate(r, r);
                        ctx.rotate(angle + arc / 2);
                        ctx.textAlign = 'right';
                        ctx.fillStyle = '#ffffff';
                        ctx.font = 'bold 12px sans-serif';
                        ctx.fillText(slices[i], r - 15, 5);
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

        // Scratch Card HTML5 Canvas Foil
        initScratchCards: function($popup) {
            var self = this;
            $popup.find('.wppoppop-scratch-wrapper').each(function() {
                var $wrap = $(this);
                var canvas = $wrap.find('.wppoppop-scratch-canvas')[0];
                if (!canvas) return;
                var ctx = canvas.getContext('2d');
                var w = canvas.width;
                var h = canvas.height;

                // Draw metallic foil
                ctx.fillStyle = '#94a3b8';
                ctx.fillRect(0, 0, w, h);
                ctx.fillStyle = '#475569';
                ctx.font = 'bold 13px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('SCRATCH TO REVEAL', w / 2, h / 2 + 5);

                var isDrawing = false;
                var revealed = false;

                function scratch(x, y) {
                    ctx.globalCompositeOperation = 'destination-out';
                    ctx.beginPath();
                    ctx.arc(x, y, 14, 0, Math.PI * 2, false);
                    ctx.fill();

                    if (!revealed) {
                        checkThreshold();
                    }
                }

                function checkThreshold() {
                    try {
                        var imgData = ctx.getImageData(0, 0, w, h);
                        var totalPixels = imgData.data.length / 4;
                        var cleared = 0;
                        for (var i = 3; i < imgData.data.length; i += 16) {
                            if (imgData.data[i] === 0) cleared++;
                        }
                        if (cleared / (totalPixels / 4) >= 0.45) {
                            revealed = true;
                            $(canvas).fadeOut(300);
                            self.playChime('win');
                            self.launchConfetti($popup);
                        }
                    } catch(e) {}
                }

                $(canvas).on('mousedown touchstart', function(e) {
                    isDrawing = true;
                    var offset = $(canvas).offset();
                    var x = (e.pageX || e.originalEvent.touches[0].pageX) - offset.left;
                    var y = (e.pageY || e.originalEvent.touches[0].pageY) - offset.top;
                    scratch(x, y);
                }).on('mousemove touchmove', function(e) {
                    if (!isDrawing) return;
                    var offset = $(canvas).offset();
                    var x = (e.pageX || e.originalEvent.touches[0].pageX) - offset.left;
                    var y = (e.pageY || e.originalEvent.touches[0].pageY) - offset.top;
                    scratch(x, y);
                }).on('mouseup touchend', function() {
                    isDrawing = false;
                });
            });
        },

        // Countdown Timer Engine
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
