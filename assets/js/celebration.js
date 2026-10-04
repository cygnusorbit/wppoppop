(function() {
    'use strict';

    const cfg = window.WPPopPopCelebrationConfig || {
        soundEnabled: true,
        confettiEnabled: true,
        openSound: 'pop',
        successSound: 'fanfare',
        soundVolume: 0.5
    };

    let audioCtx = null;
    function getAudioContext() {
        if (!audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                audioCtx = new AudioContext();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    // Web Audio Synthesizer Presets
    const SoundSynthesizer = {
        play: function(preset) {
            if (!cfg.soundEnabled) return;
            const ctx = getAudioContext();
            if (!ctx) return;

            const now = ctx.currentTime;
            const masterGain = ctx.createGain();
            masterGain.gain.setValueAtTime(cfg.soundVolume, now);
            masterGain.connect(ctx.destination);

            switch (preset) {
                case 'pop':
                    // Gentle water droplet pop
                    const oscPop = ctx.createOscillator();
                    const gainPop = ctx.createGain();
                    oscPop.type = 'sine';
                    oscPop.frequency.setValueAtTime(420, now);
                    oscPop.frequency.exponentialRampToValueAtTime(140, now + 0.08);
                    gainPop.gain.setValueAtTime(1, now);
                    gainPop.gain.linearRampToValueAtTime(0.01, now + 0.08);
                    oscPop.connect(gainPop);
                    gainPop.connect(masterGain);
                    oscPop.start(now);
                    oscPop.stop(now + 0.09);
                    break;

                case 'chime':
                    // Resonant dual bell chord
                    [523.25, 659.25, 783.99].forEach(function(freq, i) {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(freq, now + (i * 0.05));
                        gain.gain.setValueAtTime(0.6, now + (i * 0.05));
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 1.2);
                        osc.connect(gain);
                        gain.connect(masterGain);
                        osc.start(now + (i * 0.05));
                        osc.stop(now + 1.25);
                    });
                    break;

                case 'cash_register':
                    // Cash register bell ding
                    [987.77, 1318.51].forEach(function(freq, idx) {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, now + (idx * 0.08));
                        gain.gain.setValueAtTime(0.7, now + (idx * 0.08));
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
                        osc.connect(gain);
                        gain.connect(masterGain);
                        osc.start(now + (idx * 0.08));
                        osc.stop(now + 0.85);
                    });
                    break;

                case 'fanfare':
                default:
                    // Victory fanfare arpeggio (C Major chord)
                    const notes = [261.63, 329.63, 392.00, 523.25];
                    notes.forEach(function(freq, i) {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        const start = now + (i * 0.09);
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, start);
                        gain.gain.setValueAtTime(0.5, start);
                        gain.gain.exponentialRampToValueAtTime(0.001, start + 0.6);
                        osc.connect(gain);
                        gain.connect(masterGain);
                        osc.start(start);
                        osc.stop(start + 0.65);
                    });
                    break;
            }
        }
    };

    // Physics-Based 2D Canvas Confetti Cannon
    const ConfettiCannon = {
        fire: function() {
            if (!cfg.confettiEnabled) return;

            const canvas = document.createElement('canvas');
            canvas.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;pointer-events:none;z-index:9999999;';
            document.body.appendChild(canvas);

            const ctx = canvas.getContext('2d');
            let width = (canvas.width = window.innerWidth);
            let height = (canvas.height = window.innerHeight);

            const colors = ['#b5295c', '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'];
            const particles = [];
            const count = 120;

            for (let i = 0; i < count; i++) {
                particles.push({
                    x: width / 2,
                    y: height * 0.45,
                    vx: (Math.random() - 0.5) * 22,
                    vy: (Math.random() - 0.7) * 20,
                    size: Math.random() * 8 + 5,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    rotation: Math.random() * 360,
                    rotationSpeed: (Math.random() - 0.5) * 12,
                    opacity: 1
                });
            }

            let animationFrame;
            const startTime = performance.now();

            function render(currentTime) {
                const elapsed = currentTime - startTime;
                ctx.clearRect(0, 0, width, height);

                let alive = false;
                particles.forEach(function(p) {
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.48; // Gravity
                    p.vx *= 0.98; // Air drag
                    p.rotation += p.rotationSpeed;
                    p.opacity = Math.max(0, 1 - (elapsed / 3000));

                    if (p.opacity > 0 && p.y < height + 50) {
                        alive = true;
                        ctx.save();
                        ctx.translate(p.x, p.y);
                        ctx.rotate((p.rotation * Math.PI) / 180);
                        ctx.globalAlpha = p.opacity;
                        ctx.fillStyle = p.color;
                        ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
                        ctx.restore();
                    }
                });

                if (alive && elapsed < 3200) {
                    animationFrame = requestAnimationFrame(render);
                } else {
                    cancelAnimationFrame(animationFrame);
                    if (canvas.parentNode) {
                        canvas.parentNode.removeChild(canvas);
                    }
                }
            }

            animationFrame = requestAnimationFrame(render);
        }
    };

    // Expose global celebration hooks
    window.WPPopPopCelebration = {
        playOpenSound: function() {
            SoundSynthesizer.play(cfg.openSound || 'pop');
        },
        celebrateConversion: function() {
            SoundSynthesizer.play(cfg.successSound || 'fanfare');
            ConfettiCannon.fire();
        }
    };
})();
