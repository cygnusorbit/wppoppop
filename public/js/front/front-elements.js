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
})(window, jQuery);
