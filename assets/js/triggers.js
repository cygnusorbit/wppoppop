(() => {
    'use strict';

    const config = window.WPPopPopConfig || {
        exitIntent: true,
        scrollPercent: 50,
        inactivitySec: 10,
        adblock: false,
        optinLocker: false,
        loadDelayMs: 0,
        popupId: 0,
        popupTitle: '',
        displayMode: 'modal',
        restUrl: '',
        paymentUrl: '',
        impressionUrl: '',
        restNonce: '',
        gaTracking: false,
        jsOnOpen: '',
        jsOnSubmit: '',
        jsOnClose: '',
        freqLimit: 0,
        prepopulate: true,
        paymentEnabled: false,
        paymentAmount: 19.99,
        paymentCurrency: 'usd',
        tabSwitch: false,
        tabTitleFlash: '',
        backButton: false,
        soundFx: false
    };

    const cookieKey = `wppoppop_sub_${config.popupId}`;
    const isSubscribed = localStorage.getItem(cookieKey) === '1' || document.cookie.includes(`${cookieKey}=1`);

    const viewsKey = `wppoppop_views_${config.popupId}`;
    const currentViews = parseInt(localStorage.getItem(viewsKey) || '0', 10);
    if (config.freqLimit > 0 && currentViews >= config.freqLimit) {
        return;
    }

    let isTriggered = false;
    let impressionRecorded = false;
    let lockedRedirectUrl = null;
    let countdownInterval = null;

    const modal = document.getElementById('wppoppop-modal');
    const closeBtn = document.getElementById('wppoppop-close-btn');
    const form = document.getElementById('wppoppop-form');
    const feedback = document.getElementById('wppoppop-feedback');
    const submitBtn = document.getElementById('wppoppop-submit-btn');
    const progressBar = document.getElementById('wppoppop-progress-bar');
    const step1ChoiceInput = document.getElementById('wppoppop-step1-choice');
    const declineBtn = document.getElementById('wppoppop-choice-decline');
    const step3FinishBtn = document.getElementById('wppoppop-step3-finish');

    // Web Audio Synthesizer (Zero asset dependencies)
    const playSound = (type) => {
        if (!config.soundFx) return;
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();

            if (type === 'open') {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(440, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.12, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.22);
            } else if (type === 'success') {
                const notes = [523.25, 659.25, 783.99, 1046.50];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + (idx * 0.08));
                    gain.gain.setValueAtTime(0.15, ctx.currentTime + (idx * 0.08));
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + (idx * 0.08) + 0.25);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(ctx.currentTime + (idx * 0.08));
                    osc.stop(ctx.currentTime + (idx * 0.08) + 0.26);
                });
            }
        } catch (e) {}
    };

    // Tab-Switch (OnPageSwitch) & Inactive Tab Title Flasher
    let originalDocumentTitle = document.title;
    let titleFlashTimer = null;

    if (config.tabTitleFlash || config.tabSwitch) {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                if (config.tabTitleFlash && !isSubscribed) {
                    let showAlt = false;
                    titleFlashTimer = setInterval(() => {
                        document.title = showAlt ? config.tabTitleFlash : originalDocumentTitle;
                        showAlt = !showAlt;
                    }, 1200);
                }
            } else {
                if (titleFlashTimer) {
                    clearInterval(titleFlashTimer);
                    titleFlashTimer = null;
                    document.title = originalDocumentTitle;
                }
                if (config.tabSwitch && !isSubscribed) {
                    showModal('OnPageSwitch');
                }
            }
        });
    }

    // Browser Back-Button Interceptor
    if (config.backButton && !isSubscribed) {
        try {
            window.history.pushState({ wppoppop: true }, document.title, window.location.href);
            window.addEventListener('popstate', (e) => {
                if (!isSubscribed && !isTriggered) {
                    e.preventDefault();
                    showModal('OnBackButton');
                }
            });
        } catch (e) {}
    }

    if (config.prepopulate) {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const nameField = document.getElementById('wppoppop-name');
            const emailField = document.getElementById('wppoppop-email');
            if (nameField && urlParams.has('name')) nameField.value = urlParams.get('name');
            if (emailField && urlParams.has('email')) emailField.value = urlParams.get('email');
        } catch (e) {}
    }

    // Fortune Wheel Engine
    const wheelStage = modal ? modal.querySelector('#wppoppop-wheel-stage') : null;
    let isSpinning = false;
    let currentWheelAngle = 0;

    const initFortuneWheel = () => {
        if (!wheelStage) return;
        const canvas = wheelStage.querySelector('#wppoppop-wheel-canvas');
        const spinBtn = wheelStage.querySelector('#wppoppop-wheel-spin-btn');
        const resultBanner = wheelStage.querySelector('#wppoppop-wheel-result');
        const wonPrizeInput = modal.querySelector('#wppoppop-won-prize');
        const finalCoupon = modal.querySelector('#wppoppop-final-coupon');

        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        const radius = width / 2;

        const rawSlices = (wheelStage.getAttribute('data-slices') || '').trim().split('\n');
        const slices = rawSlices.map(line => {
            const parts = line.split('|');
            return {
                label: (parts[0] || '').trim(),
                coupon: (parts[1] || 'NONE').trim()
            };
        }).filter(s => s.label.length > 0);

        if (slices.length === 0) return;

        const sliceAngle = (2 * Math.PI) / slices.length;
        const palette = ['#3b82f6', '#1d4ed8', '#0284c7', '#0369a1', '#2563eb', '#1e40af', '#60a5fa', '#0ea5e9'];

        const drawWheel = (angleOffset = 0) => {
            ctx.clearRect(0, 0, width, height);

            for (let i = 0; i < slices.length; i++) {
                const angle = angleOffset + (i * sliceAngle);
                ctx.beginPath();
                ctx.fillStyle = palette[i % palette.length];
                ctx.moveTo(radius, radius);
                ctx.arc(radius, radius, radius - 6, angle, angle + sliceAngle);
                ctx.closePath();
                ctx.fill();
                ctx.lineWidth = 2;
                ctx.strokeStyle = '#ffffff';
                ctx.stroke();

                ctx.save();
                ctx.translate(radius, radius);
                ctx.rotate(angle + sliceAngle / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 12px -apple-system, sans-serif';
                ctx.fillText(slices[i].label, radius - 18, 4);
                ctx.restore();
            }

            ctx.beginPath();
            ctx.arc(radius, radius, radius - 4, 0, 2 * Math.PI);
            ctx.lineWidth = 4;
            ctx.strokeStyle = '#e2e8f0';
            ctx.stroke();
        };

        drawWheel(currentWheelAngle);

        if (spinBtn) {
            spinBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (isSpinning) return;
                isSpinning = true;
                spinBtn.disabled = true;

                const winningIndex = Math.floor(Math.random() * slices.length);
                const winningSlice = slices[winningIndex];

                const fullRounds = 5 + Math.floor(Math.random() * 3);
                const targetPointerAngle = (3 * Math.PI) / 2;
                const endAngle = targetPointerAngle - (winningIndex * sliceAngle) - (sliceAngle / 2);
                const totalRotation = (fullRounds * 2 * Math.PI) + endAngle;

                const startTime = performance.now();
                const spinDuration = 4500;
                const startAngle = currentWheelAngle;

                const animateSpin = (now) => {
                    const elapsed = now - startTime;
                    const progress = Math.min(elapsed / spinDuration, 1);
                    const easeOut = 1 - Math.pow(1 - progress, 3);

                    currentWheelAngle = startAngle + (totalRotation * easeOut);
                    drawWheel(currentWheelAngle);

                    if (progress < 1) {
                        requestAnimationFrame(animateSpin);
                    } else {
                        isSpinning = false;
                        if (wonPrizeInput) wonPrizeInput.value = winningSlice.coupon;
                        if (finalCoupon && winningSlice.coupon !== 'NONE') finalCoupon.textContent = winningSlice.coupon;

                        if (resultBanner) {
                            resultBanner.style.display = 'block';
                            if (winningSlice.coupon !== 'NONE') {
                                resultBanner.className = 'wppoppop-wheel-announcement wppoppop-wheel-win';
                                resultBanner.innerHTML = `🎉 <strong>${winningSlice.label}!</strong> Claim your discount code below.`;
                            } else {
                                resultBanner.className = 'wppoppop-wheel-announcement wppoppop-wheel-miss';
                                resultBanner.innerHTML = `Nice try! Better luck on the next promo.`;
                            }
                        }
                    }
                };

                requestAnimationFrame(animateSpin);
            });
        }
    };

    // Countdown Timer Engine
    const countdownBar = modal ? modal.querySelector('#wppoppop-countdown-bar') : null;
    const startCountdown = () => {
        if (!countdownBar) return;

        const cdType    = countdownBar.getAttribute('data-cd-type') || 'evergreen';
        const cdDateStr = countdownBar.getAttribute('data-cd-date');
        const cdMins    = parseInt(countdownBar.getAttribute('data-cd-mins') || '15', 10);
        const cdAction  = countdownBar.getAttribute('data-cd-action') || 'hide';
        const cdRedir   = countdownBar.getAttribute('data-cd-redir');

        let targetTime = 0;
        const cdKey = `wppoppop_cd_time_${config.popupId}`;

        if (cdType === 'evergreen') {
            const stored = localStorage.getItem(cdKey);
            if (stored) {
                targetTime = parseInt(stored, 10);
            } else {
                targetTime = Date.now() + (cdMins * 60 * 1000);
                localStorage.setItem(cdKey, targetTime.toString());
            }
        } else if (cdType === 'fixed' && cdDateStr) {
            targetTime = new Date(cdDateStr).getTime();
        }

        const elDays  = countdownBar.querySelector('#wppoppop-cd-days');
        const elHours = countdownBar.querySelector('#wppoppop-cd-hours');
        const elMins  = countdownBar.querySelector('#wppoppop-cd-mins');
        const elSecs  = countdownBar.querySelector('#wppoppop-cd-secs');

        const updateClock = () => {
            const remaining = targetTime - Date.now();
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                if (elDays) elDays.textContent = '00';
                if (elHours) elHours.textContent = '00';
                if (elMins) elMins.textContent = '00';
                if (elSecs) elSecs.textContent = '00';

                if (cdAction === 'hide') {
                    closeModal();
                } else if (cdAction === 'redirect' && cdRedir) {
                    window.location.href = cdRedir;
                } else if (cdAction === 'text') {
                    countdownBar.innerHTML = '<span class="wppoppop-cd-expired">OFFER HAS EXPIRED</span>';
                }
                return;
            }

            const totalSecs = Math.floor(remaining / 1000);
            const days  = Math.floor(totalSecs / 86400);
            const hours = Math.floor((totalSecs % 86400) / 3600);
            const mins  = Math.floor((totalSecs % 3600) / 60);
            const secs  = totalSecs % 60;

            const pad = (n) => n.toString().padStart(2, '0');
            if (elDays) elDays.textContent = pad(days);
            if (elHours) elHours.textContent = pad(hours);
            if (elMins) elMins.textContent = pad(mins);
            if (elSecs) elSecs.textContent = pad(secs);
        };

        updateClock();
        countdownInterval = setInterval(updateClock, 1000);
    };

    // Math Expressions & Dynamic Mirroring
    const layerWrap = modal ? modal.querySelector('.wppoppop-layer-wrapper') : null;
    let currentCalculatedAmount = config.paymentAmount;

    if (layerWrap) {
        const mathEnabled = layerWrap.getAttribute('data-math-enabled') === '1';
        const unitPrice = parseFloat(layerWrap.getAttribute('data-unit-price') || '25');
        const currency = layerWrap.getAttribute('data-currency') || '$';
        const condEnabled = layerWrap.getAttribute('data-cond-enabled') === '1';
        const condThreshold = parseInt(layerWrap.getAttribute('data-cond-threshold') || '3', 10);

        const qtyInput = layerWrap.querySelector('#wppoppop-calc-qty');
        const displayTotal = layerWrap.querySelector('#wppoppop-calc-display');
        const payAmountDisplay = layerWrap.querySelector('#wppoppop-pay-amount');
        const hiddenTotal = layerWrap.querySelector('#wppoppop-calculated-total');
        const condGroup = layerWrap.querySelector('#wppoppop-cond-group');

        const recalculate = () => {
            if (!mathEnabled || !qtyInput) return;
            let qty = parseInt(qtyInput.value || '1', 10);
            if (isNaN(qty) || qty < 1) qty = 1;

            const total = qty * unitPrice;
            currentCalculatedAmount = total;

            if (displayTotal) displayTotal.textContent = `${currency}${total.toFixed(2)}`;
            if (payAmountDisplay) payAmountDisplay.textContent = `${currency}${total.toFixed(2)} ${config.paymentCurrency.toUpperCase()}`;
            if (hiddenTotal) hiddenTotal.value = total.toFixed(2);

            if (condEnabled && condGroup) {
                condGroup.style.display = (qty >= condThreshold) ? 'flex' : 'none';
            }
        };

        if (qtyInput) qtyInput.addEventListener('input', recalculate);

        layerWrap.querySelectorAll('.wppoppop-step-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!qtyInput) return;
                const dir = parseInt(btn.getAttribute('data-step-dir') || '0', 10);
                let currentVal = parseInt(qtyInput.value || '1', 10);
                qtyInput.value = Math.max(1, currentVal + dir);
                recalculate();
            });
        });

        const nameInput = layerWrap.querySelector('#wppoppop-name');
        const mirrorTarget = layerWrap.querySelector('.wppoppop-live-mirror');
        if (nameInput && mirrorTarget) {
            nameInput.addEventListener('input', () => {
                const val = nameInput.value.trim();
                if (val.length > 0) {
                    mirrorTarget.textContent = `, ${val}!`;
                    mirrorTarget.style.display = 'inline';
                } else {
                    mirrorTarget.style.display = 'none';
                }
            });
        }
    }

    const executeCustomJs = (code, context = {}) => {
        if (!code || typeof code !== 'string' || !code.trim()) return;
        try {
            const handler = new Function('context', code);
            handler(context);
        } catch (err) {
            console.error('[WPPopPop] Custom JS execution error:', err);
        }
    };

    const dispatchAnalytics = (action, extraData = {}) => {
        if (!config.gaTracking) return;
        const eventPayload = {
            popup_id: config.popupId,
            popup_title: config.popupTitle || `Popup #${config.popupId}`,
            event_category: 'WPPopPop',
            ...extraData
        };
        if (typeof window.gtag === 'function') window.gtag('event', action, eventPayload);
        if (Array.isArray(window.dataLayer)) window.dataLayer.push({ event: action, ...eventPayload });
        if (typeof window.fbq === 'function') window.fbq('trackCustom', action, eventPayload);
    };

    const recordImpression = () => {
        if (impressionRecorded || !config.impressionUrl || !config.popupId) return;
        impressionRecorded = true;
        localStorage.setItem(viewsKey, (currentViews + 1).toString());
        fetch(config.impressionUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ popup_id: config.popupId })
        }).catch(() => {});
    };

    const showModal = (triggerName) => {
        if (isTriggered || !modal) return;
        isTriggered = true;
        modal.classList.add('wppoppop-visible');
        modal.setAttribute('aria-hidden', 'false');
        recordImpression();
        startCountdown();
        initFortuneWheel();
        playSound('open');
        dispatchAnalytics('popup_impression', { trigger: triggerName });
        executeCustomJs(config.jsOnOpen, { popup_id: config.popupId, trigger: triggerName });
    };

    const closeModal = () => {
        if (!modal || config.optinLocker) return;
        modal.classList.remove('wppoppop-visible');
        modal.setAttribute('aria-hidden', 'true');
        if (countdownInterval) clearInterval(countdownInterval);
        dispatchAnalytics('popup_close');
        executeCustomJs(config.jsOnClose, { popup_id: config.popupId });
    };

    if (closeBtn && !config.optinLocker) closeBtn.addEventListener('click', closeModal);
    if (modal && !config.optinLocker) {
        modal.addEventListener('click', (e) => {
            const isNonModal = modal.classList.contains('wppoppop-mode-bar_top') ||
                               modal.classList.contains('wppoppop-mode-bar_bottom') ||
                               modal.classList.contains('wppoppop-mode-slide_in');
            if (e.target === modal && !isNonModal) {
                closeModal();
            }
        });
    }

    const goToStep = (stepNumber) => {
        const steps = modal.querySelectorAll('.wppoppop-step');
        steps.forEach(s => s.classList.remove('wppoppop-step-active'));
        const targetStep = modal.querySelector(`.wppoppop-step-${stepNumber}`);
        if (targetStep) targetStep.classList.add('wppoppop-step-active');
        if (progressBar) {
            const percentages = { 1: '33%', 2: '66%', 3: '100%' };
            progressBar.style.width = percentages[stepNumber] || '100%';
        }
    };

    document.addEventListener('click', (e) => {
        const nextBtn = e.target.closest('[data-step-next]');
        if (nextBtn) {
            e.preventDefault();
            const nextStep = nextBtn.getAttribute('data-step-next');
            const choice = nextBtn.getAttribute('data-choice');
            if (step1ChoiceInput && choice) step1ChoiceInput.value = choice;
            goToStep(nextStep);
        }
    });

    if (declineBtn) {
        declineBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal();
        });
    }

    if (step3FinishBtn) {
        step3FinishBtn.addEventListener('click', () => {
            closeModal();
            if (lockedRedirectUrl) window.location.href = lockedRedirectUrl;
        });
    }

    document.addEventListener('click', (e) => {
        const lockedLink = e.target.closest('a[data-wppoppop-lock], .wppoppop-lock');
        if (lockedLink && !isSubscribed) {
            e.preventDefault();
            lockedRedirectUrl = lockedLink.getAttribute('href');
            isTriggered = false;
            showModal('LinkLocker');
            return;
        }

        const trigger = e.target.closest('.wppoppop-trigger, [data-popup-id]');
        if (trigger) {
            e.preventDefault();
            isTriggered = false;
            showModal('OnClick');
        }
    });

    if (!isSubscribed) {
        if (config.adblock) {
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    const bait = document.getElementById('wppoppop-ad-bait');
                    if (!bait || bait.offsetParent === null || bait.offsetHeight === 0 || bait.offsetLeft === 0 || window.getComputedStyle(bait).display === 'none') {
                        showModal('OnAdBlockDetected');
                    }
                }, 300);
            });
        }

        if (config.loadDelayMs > 0) setTimeout(() => showModal('OnLoad'), config.loadDelayMs);

        if (config.exitIntent) {
            const onMouseLeave = (e) => {
                if (e.clientY <= 15 && e.relatedTarget == null) {
                    showModal('OnExit');
                }
            };
            document.addEventListener('mouseleave', onMouseLeave);
        }

        if (config.scrollPercent > 0) {
            let scrollTicking = false;
            const checkScroll = () => {
                const h = document.documentElement;
                const b = document.body;
                const scrollTop = h.scrollTop || b.scrollTop;
                const scrollHeight = (h.scrollHeight || b.scrollHeight) - h.clientHeight;
                if (scrollHeight > 0 && ((scrollTop / scrollHeight) * 100) >= config.scrollPercent) {
                    showModal('OnScroll');
                    window.removeEventListener('scroll', throttledScroll);
                }
                scrollTicking = false;
            };
            const throttledScroll = () => {
                if (!scrollTicking) {
                    requestAnimationFrame(checkScroll);
                    scrollTicking = true;
                }
            };
            window.addEventListener('scroll', throttledScroll, { passive: true });
        }

        if (config.inactivitySec > 0) {
            let idleTimer;
            const resetIdleTimer = () => {
                clearTimeout(idleTimer);
                idleTimer = setTimeout(() => { showModal('OnInactivity'); }, config.inactivitySec * 1000);
            };
            ['mousemove', 'keydown', 'scroll', 'touchstart'].forEach(evt => {
                window.addEventListener(evt, resetIdleTimer, { passive: true });
            });
            resetIdleTimer();
        }
    }

    // Multipart Form Submission
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            feedback.style.display = 'none';
            feedback.className = 'wppoppop-feedback';
            submitBtn.disabled = true;
            submitBtn.classList.add('wppoppop-loading');

            const formData = new FormData(form);

            try {
                if (config.paymentEnabled) {
                    const payRes = await fetch(config.paymentUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            popup_id: config.popupId,
                            amount: currentCalculatedAmount,
                            email: formData.get('email') || ''
                        })
                    });
                    const payData = await payRes.json();
                    if (!payRes.ok || !payData.success) {
                        feedback.style.display = 'block';
                        feedback.textContent = payData.message || 'Payment processing failed.';
                        feedback.classList.add('wppoppop-error');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('wppoppop-loading');
                        return;
                    }
                    dispatchAnalytics('popup_payment_completed', { amount: currentCalculatedAmount });
                }

                const response = await fetch(config.restUrl, {
                    method: 'POST',
                    headers: {
                        'X-WP-Nonce': config.restNonce
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    form.reset();
                    localStorage.setItem(cookieKey, '1');
                    document.cookie = `${cookieKey}=1; path=/; max-age=2592000; SameSite=Lax`;

                    playSound('success');
                    dispatchAnalytics('popup_submission', { email: formData.get('email'), name: formData.get('name') });
                    executeCustomJs(config.jsOnSubmit, { popup_id: config.popupId, email: formData.get('email'), name: formData.get('name') });

                    const step3 = modal.querySelector('.wppoppop-step-3');
                    if (step3) {
                        goToStep(3);
                    } else {
                        feedback.style.display = 'block';
                        feedback.textContent = data.message || 'Thank you! Order completed.';
                        feedback.classList.add('wppoppop-success');
                        setTimeout(() => {
                            closeModal();
                            if (lockedRedirectUrl) window.location.href = lockedRedirectUrl;
                        }, 1800);
                    }
                } else {
                    feedback.style.display = 'block';
                    feedback.textContent = data.message || 'Error processing request.';
                    feedback.classList.add('wppoppop-error');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('wppoppop-loading');
                }
            } catch (err) {
                feedback.style.display = 'block';
                feedback.classList.add('wppoppop-error');
                feedback.textContent = 'A network error occurred. Please try again.';
                submitBtn.disabled = false;
                submitBtn.classList.remove('wppoppop-loading');
            }
        });
    }

    // Social Proof Toast Stream Engine
    const toastEl    = document.getElementById('wppoppop-sp-toast');
    const toastIcon  = document.getElementById('wppoppop-sp-icon');
    const toastTitle = document.getElementById('wppoppop-sp-title');
    const toastMeta  = document.getElementById('wppoppop-sp-meta');
    const toastClose = document.getElementById('wppoppop-sp-close');

    const initSocialProofStream = () => {
        const sp = config.socialProof;
        if (!sp || !sp.enabled || !sp.feed || sp.feed.length === 0 || !toastEl) {
            return;
        }

        toastEl.className = `wppoppop-sp-toast wppoppop-sp-${sp.position || 'bottom-left'}`;

        let currentIndex = 0;
        let toastTimer = null;

        const showNextToast = () => {
            const item = sp.feed[currentIndex];
            if (!item) return;

            if (toastIcon) toastIcon.textContent = item.icon || '⚡';
            if (toastTitle) toastTitle.textContent = item.title || '';
            if (toastMeta) toastMeta.textContent = item.time || '';

            toastEl.style.display = 'flex';
            requestAnimationFrame(() => {
                toastEl.classList.add('wppoppop-sp-visible');
            });

            setTimeout(() => {
                toastEl.classList.remove('wppoppop-sp-visible');
                setTimeout(() => {
                    toastEl.style.display = 'none';
                    currentIndex = (currentIndex + 1) % sp.feed.length;
                    toastTimer = setTimeout(showNextToast, (sp.intervalSec || 8) * 1000);
                }, 300);
            }, (sp.durationSec || 5) * 1000);
        };

        toastTimer = setTimeout(showNextToast, 3500);

        if (toastClose) {
            toastClose.addEventListener('click', (e) => {
                e.stopPropagation();
                toastEl.classList.remove('wppoppop-sp-visible');
                setTimeout(() => { toastEl.style.display = 'none'; }, 300);
                clearTimeout(toastTimer);
            });
        }

        toastEl.addEventListener('click', () => {
            toastEl.classList.remove('wppoppop-sp-visible');
            setTimeout(() => { toastEl.style.display = 'none'; }, 300);
            showModal('SocialProofClick');
        });
    };

    window.addEventListener('DOMContentLoaded', initSocialProofStream);
})();
