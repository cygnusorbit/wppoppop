// Client-side Screen Width Check
    if (cfg.minScreenWidth && window.innerWidth < cfg.minScreenWidth) {
        return; // Suppress popup on smaller screens
    }

// 1. Browser Back-Button Interceptor (HTML5 History Trap)
    if (cfg.backButtonTrap) {
        window.history.pushState({ wppoppopTrap: true }, document.title, window.location.href);
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.wppoppopTrap) {
                showModal('OnBackButton');
                window.history.pushState(null, document.title, window.location.href);
            }
        });
    }

    // 2. Inactive Tab-Switch Trigger & Title Flasher (OnPageSwitch)
    if (cfg.tabSwitchTrigger) {
        let originalDocTitle = document.title;
        let titleFlasherInterval = null;
        let isTabFlasherActive = false;

        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                if (!titleFlasherInterval) {
                    isTabFlasherActive = true;
                    titleFlasherInterval = setInterval(function() {
                        document.title = (document.title === originalDocTitle)
                            ? (cfg.tabSwitchTitle || '⚠️ Wait! Don\'t miss out!')
                            : originalDocTitle;
                    }, 1200);
                }
            } else if (document.visibilityState === 'visible') {
                if (titleFlasherInterval) {
                    clearInterval(titleFlasherInterval);
                    titleFlasherInterval = null;
                    document.title = originalDocTitle;
                }
                if (isTabFlasherActive) {
                    isTabFlasherActive = false;
                    showModal('OnPageSwitch');
                }
            }
        });
    }

    // 3. Protected Link Locker Click Interceptor
    let activeLockedDestination = null;
    document.addEventListener('click', function(e) {
        const lockTrigger = e.target.closest('[data-wppoppop-lock="1"], .wppoppop-lock-trigger');
        if (!lockTrigger) return;

        e.preventDefault();
        const popupId = lockTrigger.getAttribute('data-popup-id') || cfg.popupId;
        const destUrl = lockTrigger.getAttribute('data-dest-url') || lockTrigger.getAttribute('href');
        const destTarget = lockTrigger.getAttribute('data-dest-target') || lockTrigger.getAttribute('target') || '_self';

        if (destUrl && destUrl !== '#') {
            activeLockedDestination = { url: destUrl, target: destTarget };
        }

        const targetModal = document.getElementById('wppoppop-modal-' + popupId) || document.getElementById('wppoppop-modal');
        if (targetModal) {
            targetModal.style.display = 'flex';
            targetModal.setAttribute('aria-hidden', 'false');
            if (window.WPPopPopCelebration) {
                window.WPPopPopCelebration.playOpenSound();
            }
        }
    });

    // Execute post-submission Link Locker unlock navigation
    function executeLinkLockerUnlock() {
        if (activeLockedDestination && activeLockedDestination.url) {
            setTimeout(function() {
                if (activeLockedDestination.target === '_blank') {
                    window.open(activeLockedDestination.url, '_blank');
                } else {
                    window.location.href = activeLockedDestination.url;
                }
                activeLockedDestination = null;
            }, 1200);
        }
    }

// Sticky Floating Tab Launcher Handlers
    document.addEventListener('click', function(e) {
        const tabBtn = e.target.closest('.wppoppop-tab-launcher');
        if (!tabBtn) return;

        e.preventDefault();
        const targetId = tabBtn.getAttribute('data-popup-id');
        const targetModal = document.getElementById('wppoppop-modal-' + targetId) || document.getElementById('wppoppop-modal');

        if (targetModal) {
            targetModal.style.display = 'flex';
            targetModal.setAttribute('aria-hidden', 'false');
            if (window.WPPopPopCelebration) {
                window.WPPopPopCelebration.playOpenSound();
            }
        }
    });

    // Reveal 'after_close' tab launchers upon modal dismissal
    function revealClosedTabLaunchers() {
        document.querySelectorAll('.wppoppop-tab-launcher.wppoppop-tab-after-close').forEach(function(tab) {
            tab.classList.add('wppoppop-tab-visible');
        });
    }

(() => {
    'use strict';
    const cfg = window.WPPopPopConfig || {};
    const modal = document.getElementById('wppoppop-modal');
    const closeBtn = document.getElementById('wppoppop-close-btn');

    if (!modal) return;

    let impressionSent = false;
    function sendImpression() {
        if (impressionSent || !cfg.impressionUrl || !cfg.popupId) return;
        impressionSent = true;
        fetch(cfg.impressionUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.restNonce },
            body: JSON.stringify({ popup_id: cfg.popupId })
        }).catch(() => {});
    }

    function showModal() {
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        sendImpression();
        if (window.WPPopPopCelebration) window.WPPopPopCelebration.playOpenSound();
    }

    function closeModal() {
        modal.style.display = 'none';
        revealClosedTabLaunchers();
        modal.setAttribute('aria-hidden', 'true');
    }

    if (closeBtn) closeBtn.addEventListener('click', function() { if (!isLockerActive) closeModal(); });
// Full-Page Locker Evaluation
    const isLockerActive = (cfg.isLocker && !localStorage.getItem('wppoppop_unlocked_' + cfg.popupId));
    if (isLockerActive && modal) {
        modal.classList.add('wppoppop-is-locker');
        document.body.classList.add('wppoppop-page-locked');
        showModal();
    }

    // Function to unlock all lockers on current page
    function unlockContent(popupId) {
        const durationDays = cfg.unlockDuration || 30;
        const expiry = Date.now() + (durationDays * 86400000);

        localStorage.setItem('wppoppop_unlocked_' + popupId, expiry.toString());
        document.cookie = 'wppoppop_unlocked_' + popupId + '=1; path=/; max-age=' + (durationDays * 86400) + '; SameSite=Lax';

        // Unlock page-level lock
        document.body.classList.remove('wppoppop-page-locked');
        if (modal) modal.classList.remove('wppoppop-is-locker');

        // Unlock inline gated lockers dynamically
        document.querySelectorAll('.wppoppop-inline-locker[data-locker-id="' + popupId + '"]').forEach(function(locker) {
            const revealed = locker.querySelector('.wppoppop-revealed-content');
            if (revealed) {
                locker.innerHTML = revealed.innerHTML;
                locker.classList.add('wppoppop-unlocked-block');
            }
        });
    }
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Auto triggers
    if (cfg.exitIntent) {
        document.addEventListener('mouseleave', (e) => {
            if (e.clientY <= 0) showModal();
        }, { once: true });
    }

    if (cfg.scrollPercent) {
        window.addEventListener('scroll', () => {
            const pct = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            if (pct >= cfg.scrollPercent) showModal();
        }, { passive: true });
    }

    // Intercept checkout forms with monetary values
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.wppoppop-form');
        if (!form) return;

        const amountInput = form.querySelector('input[name="amount"], [data-amount]');
        const amount = amountInput ? parseFloat(amountInput.value || amountInput.dataset.amount) : 0;
        if (amount <= 0) return; // Standard lead submission handled by existing flow

        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing Payment...';
        }

        const email = (form.querySelector('input[name="email"]') || {}).value || '';
        const name  = (form.querySelector('input[name="name"]') || {}).value || '';

        try {
            // 1. Initialize Payment Intent
            const initRes = await fetch('/wp-json/wppoppop/v1/create-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.restNonce },
                body: JSON.stringify({
                    popup_id: cfg.popupId,
                    amount: amount,
                    email: email,
                    name: name,
                    gateway: 'stripe'
                })
            });

            const initData = await initRes.json();
            if (!initRes.ok || !initData.success) {
                alert(initData.message || 'Payment initiation failed.');
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Submit'; }
                return;
            }

            // 2. Settle and Confirm Payment
            const confirmRes = await fetch('/wp-json/wppoppop/v1/confirm-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.restNonce },
                body: JSON.stringify({
                    lead_id: initData.lead_id,
                    transaction_id: initData.transaction_id
                })
            });

            const confirmData = await confirmRes.json();
            if (confirmRes.ok && confirmData.success) {
                form.innerHTML = '<div style="color:#10b981;padding:20px;text-align:center;font-weight:600;">' +
                    '<h3>Payment Received!</h3><p>Transaction ID: ' + initData.transaction_id + '</p></div>';
                if (window.WPPopPopCelebration) window.WPPopPopCelebration.celebrateConversion();
                setTimeout(() => { if (typeof closeModal === 'function') closeModal(); }, 2500);
            } else {
                alert(confirmData.message || 'Payment confirmation failed.');
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Submit'; }
            }
        } catch (err) {
            alert('A network error occurred while processing payment.');
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Submit'; }
        }
    }, true);

    // AdBlock Detection Routine
    if (cfg.adblockDetector) {
        window.addEventListener('load', function() {
            var bait = document.createElement('div');
            bait.className = 'pub_300x250 pub_300x250m pub_728x90 text-ad textAd text_ad text_ads text-ads text-ad-links';
            bait.style.cssText = 'width: 1px !important; height: 1px !important; position: absolute !important; left: -10000px !important; top: -1000px !important;';
            document.body.appendChild(bait);

            setTimeout(function() {
                var isBlocked = !bait || bait.offsetParent === null || bait.offsetHeight === 0 || bait.offsetLeft === 0 ||
                    window.getComputedStyle(bait).getPropertyValue('display') === 'none' ||
                    window.getComputedStyle(bait).getPropertyValue('visibility') === 'hidden';

                if (isBlocked) {
                    showModal();
                }
                if (bait && bait.parentNode) {
                    bait.parentNode.removeChild(bait);
                }
            }, 300);
        });
    }

    // WooCommerce Cart Abandonment & Dynamic Coupon Engine
    const wooCfg = window.WPPopPopWooConfig || {};
    if (wooCfg.active && wooCfg.abandonment && wooCfg.abandonPopupId) {
        document.addEventListener('mouseleave', function(e) {
            if (e.clientY <= 0) {
                const targetModal = document.getElementById('wppoppop-modal-' + wooCfg.abandonPopupId);
                if (targetModal && targetModal.style.display !== 'flex') {
                    targetModal.style.display = 'flex';
                    targetModal.setAttribute('aria-hidden', 'false');
                    if (window.WPPopPopCelebration) window.WPPopPopCelebration.playOpenSound();
                }
            }
        }, { once: true });
    }

    // Auto-Apply Coupon Code Button Handler
    document.addEventListener('click', async function(e) {
        const btn = e.target.closest('[data-apply-coupon]');
        if (!btn || !wooCfg.applyCouponUrl) return;

        e.preventDefault();
        const coupon = btn.getAttribute('data-apply-coupon');
        btn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = 'Applying...';

        try {
            const res = await fetch(wooCfg.applyCouponUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ coupon_code: coupon })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                btn.textContent = 'Coupon Applied!';
                if (window.WPPopPopCelebration) window.WPPopPopCelebration.celebrateConversion();
                setTimeout(function() {
                    window.location.href = data.checkout_url || wooCfg.checkoutUrl;
                }, 1200);
            } else {
                alert(data.message || 'Could not apply coupon.');
                btn.disabled = false;
                btn.textContent = originalText;
            }
        } catch (err) {
            alert('A network error occurred.');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });

    document.querySelectorAll('.wppoppop-trigger').forEach(btn => btn.addEventListener('click', showModal));

    // Handle Form Submissions via REST API
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.wppoppop-form');
        if (!form) return;
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        const payload = {
            popup_id: cfg.popupId,
            name: (form.querySelector('input[name="name"]') || {}).value || '',
            email: (form.querySelector('input[name="email"]') || {}).value || ''
        };

        try {
            const res = await fetch(cfg.restUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.restNonce },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (res.ok && data.success) {
                form.innerHTML = '<div style="color:#10b981;padding:15px;font-weight:600;">' + (data.message || 'Subscribed successfully!') + '</div>';
                if (window.WPPopPopCelebration) window.WPPopPopCelebration.celebrateConversion();
                unlockContent(cfg.popupId);
                executeLinkLockerUnlock();
                setTimeout(closeModal, 2000);
            } else {
                alert(data.message || 'Submission failed.');
                if (submitBtn) submitBtn.disabled = false;
            }
        } catch (err) {
            alert('A network error occurred.');
            if (submitBtn) submitBtn.disabled = false;
        }
    });
})();
