(function() {
    'use strict';

    function initCountdowns() {
        const timers = document.querySelectorAll('.wppoppop-countdown-wrap');
        timers.forEach(function(el) {
            if (el.dataset.countdownActive === '1') return;
            el.dataset.countdownActive = '1';

            const mode       = el.getAttribute('data-timer-mode') || 'evergreen';
            const fixedDate  = el.getAttribute('data-target-date') || '';
            const minutes    = parseInt(el.getAttribute('data-duration-min') || '15', 10);
            const timerId    = el.getAttribute('data-timer-id') || 'default';
            const onExpiry   = el.getAttribute('data-on-expiry') || 'none'; // close, redirect, none
            const redirectUrl = el.getAttribute('data-redirect-url') || '';

            let targetTs = 0;

            if (mode === 'fixed' && fixedDate) {
                targetTs = new Date(fixedDate).getTime();
            } else {
                // Evergreen timer stored in visitor's localStorage
                const storageKey = 'wppoppop_timer_' + timerId;
                const savedTs    = localStorage.getItem(storageKey);
                const now        = Date.now();

                if (savedTs && parseInt(savedTs, 10) > now) {
                    targetTs = parseInt(savedTs, 10);
                } else {
                    targetTs = now + (minutes * 60 * 1000);
                    localStorage.setItem(storageKey, targetTs.toString());
                }
            }

            const daysEl  = el.querySelector('.wppoppop-cd-days');
            const hoursEl = el.querySelector('.wppoppop-cd-hours');
            const minsEl  = el.querySelector('.wppoppop-cd-mins');
            const secsEl  = el.querySelector('.wppoppop-cd-secs');

            function pad(n) {
                return n < 10 ? '0' + n : n;
            }

            function update() {
                const now = Date.now();
                const diff = targetTs - now;

                if (diff <= 0) {
                    if (daysEl) daysEl.textContent = '00';
                    if (hoursEl) hoursEl.textContent = '00';
                    if (minsEl) minsEl.textContent = '00';
                    if (secsEl) secsEl.textContent = '00';
                    clearInterval(interval);

                    if (onExpiry === 'close') {
                        const modal = el.closest('.wppoppop-overlay');
                        if (modal) modal.style.display = 'none';
                    } else if (onExpiry === 'redirect' && redirectUrl) {
                        window.location.href = redirectUrl;
                    }
                    return;
                }

                if (diff < 60000) {
                    el.classList.add('urgent');
                }

                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);

                if (daysEl) daysEl.textContent = pad(d);
                if (hoursEl) hoursEl.textContent = pad(h);
                if (minsEl) minsEl.textContent = pad(m);
                if (secsEl) secsEl.textContent = pad(s);
            }

            update();
            const interval = setInterval(update, 1000);
        });
    }

    document.addEventListener('DOMContentLoaded', initCountdowns);
    window.WPPopPopCountdown = { init: initCountdowns };
})();
