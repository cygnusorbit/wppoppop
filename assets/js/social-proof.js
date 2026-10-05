(function() {
    'use strict';

    const cfg = window.WPPopPopSocialProofConfig || {
        feedUrl: '/wp-json/wppoppop/v1/social-proof-feed',
        interval: 10,
        duration: 5,
        position: 'bottom-left'
    };

    let items = [];
    let currentIndex = 0;
    let timer = null;
    let isHovered = false;

    async function loadSocialProofFeed() {
        try {
            const res = await fetch(cfg.feedUrl);
            const data = await res.json();
            if (res.ok && data.success && Array.isArray(data.items) && data.items.length > 0) {
                items = data.items;
                startRotation();
            }
        } catch (e) {}
    }

    function createToastElement(item) {
        const toast = document.createElement('div');
        toast.className = 'wppoppop-sp-toast';
        if (item.popup_id) {
            toast.setAttribute('data-popup-id', item.popup_id);
        }

        toast.innerHTML = `
            <div class="wppoppop-sp-avatar">${item.avatar || '👤'}</div>
            <div class="wppoppop-sp-body">
                <div class="wppoppop-sp-title">${item.name}</div>
                <div class="wppoppop-sp-desc">${item.action}</div>
                <div class="wppoppop-sp-meta">${item.time_ago} &bull; <span style="color:#059669;font-weight:600;">Verified</span></div>
            </div>
            <button type="button" class="wppoppop-sp-close" aria-label="Dismiss">&times;</button>
        `;

        toast.querySelector('.wppoppop-sp-close').addEventListener('click', function(e) {
            e.stopPropagation();
            hideToast(toast);
        });

        toast.addEventListener('mouseenter', () => { isHovered = true; });
        toast.addEventListener('mouseleave', () => { isHovered = false; });

        // Clicking the social proof notification launches the designated popup
        toast.addEventListener('click', function() {
            const pid = toast.getAttribute('data-popup-id');
            if (pid) {
                const modal = document.getElementById('wppoppop-modal-' + pid) || document.getElementById('wppoppop-modal');
                if (modal) {
                    modal.style.display = 'flex';
                    modal.setAttribute('aria-hidden', 'false');
                    if (window.WPPopPopCelebration) {
                        window.WPPopPopCelebration.playOpenSound();
                    }
                }
            }
        });

        return toast;
    }

    function showNextToast() {
        if (isHovered || items.length === 0) return;

        const container = document.getElementById('wppoppop-social-proof-container');
        if (!container) return;

        container.innerHTML = '';
        const item = items[currentIndex];
        currentIndex = (currentIndex + 1) % items.length;

        const toast = createToastElement(item);
        container.appendChild(toast);

        // Trigger smooth slide-in
        requestAnimationFrame(() => {
            toast.classList.add('wppoppop-sp-active');
        });

        // Hide after configured duration
        setTimeout(() => {
            if (!isHovered) {
                hideToast(toast);
            } else {
                const hoverCheck = setInterval(() => {
                    if (!isHovered) {
                        clearInterval(hoverCheck);
                        hideToast(toast);
                    }
                }, 1000);
            }
        }, cfg.duration * 1000);
    }

    function hideToast(toast) {
        if (!toast) return;
        toast.classList.remove('wppoppop-sp-active');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 400);
    }

    function startRotation() {
        setTimeout(showNextToast, 2500); // Initial delay
        timer = setInterval(showNextToast, (cfg.duration + cfg.interval) * 1000);
    }

    document.addEventListener('DOMContentLoaded', loadSocialProofFeed);
})();
