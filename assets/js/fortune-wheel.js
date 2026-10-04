(function() {
    'use strict';

    function initFortuneWheel() {
        const containers = document.querySelectorAll('.wppoppop-wheel-stage');
        containers.forEach(function(wrap) {
            if (wrap.dataset.wheelInitialized === '1') return;
            wrap.dataset.wheelInitialized = '1';

            const canvas   = wrap.querySelector('.wppoppop-wheel-canvas');
            const hubBtn   = wrap.querySelector('.wppoppop-wheel-hub');
            const resultBox = wrap.parentElement.querySelector('.wppoppop-wheel-result-banner');
            const hiddenPrize = wrap.parentElement.querySelector('input[name="fields[won_prize]"]');
            const popupId = wrap.getAttribute('data-popup-id') || '0';

            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const width  = (canvas.width = 300);
            const height = (canvas.height = 300);
            const radius = width / 2;

            let slices = [];
            try {
                slices = JSON.parse(wrap.getAttribute('data-slices') || '[]');
            } catch (e) {
                slices = [];
            }
            if (slices.length === 0) return;

            const sliceAngle = (2 * Math.PI) / slices.length;
            let currentAngle = 0;
            let isSpinning = false;
            let lastTickIndex = -1;

            // Audio Tick Synthesizer
            function playTick() {
                try {
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (!AudioCtx) return;
                    const ctxA = new AudioCtx();
                    const osc = ctxA.createOscillator();
                    const gain = ctxA.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(800, ctxA.currentTime);
                    gain.gain.setValueAtTime(0.08, ctxA.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctxA.currentTime + 0.04);
                    osc.connect(gain);
                    gain.connect(ctxA.destination);
                    osc.start();
                    osc.stop(ctxA.currentTime + 0.045);
                } catch (e) {}
            }

            function drawWheel(angleOffset) {
                ctx.clearRect(0, 0, width, height);

                for (let i = 0; i < slices.length; i++) {
                    const angle = angleOffset + (i * sliceAngle);

                    // Wedge
                    ctx.beginPath();
                    ctx.fillStyle = slices[i].color || '#b5295c';
                    ctx.moveTo(radius, radius);
                    ctx.arc(radius, radius, radius - 4, angle, angle + sliceAngle);
                    ctx.closePath();
                    ctx.fill();

                    // Edge border
                    ctx.lineWidth = 1.5;
                    ctx.strokeStyle = '#ffffff';
                    ctx.stroke();

                    // Text Label
                    ctx.save();
                    ctx.translate(radius, radius);
                    ctx.rotate(angle + sliceAngle / 2);
                    ctx.textAlign = 'right';
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                    ctx.fillText(slices[i].label, radius - 20, 4);
                    ctx.restore();
                }

                // Outer perimeter stroke
                ctx.beginPath();
                ctx.arc(radius, radius, radius - 2, 0, 2 * Math.PI);
                ctx.lineWidth = 4;
                ctx.strokeStyle = '#f1f5f9';
                ctx.stroke();
            }

            drawWheel(currentAngle);

            // Spin Trigger Execution
            function startSpin() {
                if (isSpinning) return;

                // Session duplication check
                const cookieKey = 'wppoppop_spun_' + popupId;
                if (localStorage.getItem(cookieKey) === '1') {
                    if (resultBox) {
                        resultBox.className = 'wppoppop-wheel-result-banner miss';
                        resultBox.textContent = 'You have already spun the wheel for this visit!';
                        resultBox.style.display = 'block';
                    }
                    return;
                }

                isSpinning = true;
                if (hubBtn) hubBtn.classList.add('spinning');

                // Determine winning index by probability weights
                let totalWeight = 0;
                slices.forEach(s => totalWeight += (s.weight || 10));
                let randomVal = Math.random() * totalWeight;
                let wonIndex = 0;
                for (let i = 0; i < slices.length; i++) {
                    if (randomVal <= slices[i].weight) {
                        wonIndex = i;
                        break;
                    }
                    randomVal -= slices[i].weight;
                }

                const wonSlice = slices[wonIndex];
                const fullSpins = 5 + Math.floor(Math.random() * 3);
                const pointerAngle = (3 * Math.PI) / 2; // Pointer is top (270 deg)
                const targetAngle = pointerAngle - (wonIndex * sliceAngle) - (sliceAngle / 2);
                const totalRotation = (fullSpins * 2 * Math.PI) + targetAngle;

                const duration = 4800;
                const startTime = performance.now();
                const startAngle = currentAngle;

                function animate(now) {
                    const elapsed = now - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const easeOut = 1 - Math.pow(1 - progress, 3.5); // Ease-out cubic deceleration

                    currentAngle = startAngle + (totalRotation * easeOut);
                    drawWheel(currentAngle);

                    // Track audio clicks on wedge transitions
                    const currentWedge = Math.floor(((currentAngle % (2 * Math.PI)) / (2 * Math.PI)) * slices.length);
                    if (currentWedge !== lastTickIndex) {
                        playTick();
                        lastTickIndex = currentWedge;
                    }

                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    } else {
                        isSpinning = false;
                        localStorage.setItem(cookieKey, '1');
                        document.cookie = cookieKey + '=1; path=/; max-age=604800; SameSite=Lax';

                        if (hiddenPrize) {
                            hiddenPrize.value = wonSlice.label + ' (' + wonSlice.coupon + ')';
                        }

                        if (resultBox) {
                            resultBox.style.display = 'block';
                            if (wonSlice.coupon !== 'NONE') {
                                resultBox.className = 'wppoppop-wheel-result-banner won';
                                resultBox.innerHTML = '🎉 <strong>' + wonSlice.label + '!</strong> Your coupon code: <code>' + wonSlice.coupon + '</code>';
                                if (window.WPPopPopCelebration) {
                                    window.WPPopPopCelebration.celebrateConversion();
                                }
                            } else {
                                resultBox.className = 'wppoppop-wheel-result-banner miss';
                                resultBox.innerHTML = 'Nice try! Subscribe below for our consolation promo.';
                            }
                        }
                    }
                }

                requestAnimationFrame(animate);
            }

            if (hubBtn) hubBtn.addEventListener('click', startSpin);
        });
    }

    document.addEventListener('DOMContentLoaded', initFortuneWheel);
    window.WPPopPopWheel = { init: initFortuneWheel };
})();
