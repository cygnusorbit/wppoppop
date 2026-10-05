
(function($) {
    'use strict';

    // Real-Time Web Audio Chime Synthesizer
    function playAudioTone(type) {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'open') {
                osc.frequency.setValueAtTime(440, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.08, ctx.currentTime);
                gain.gain.linearRampToValueAtTime(0.001, ctx.currentTime + 0.2);
                osc.start();
                osc.stop(ctx.currentTime + 0.2);
            } else if (type === 'success') {
                osc.frequency.setValueAtTime(523.25, ctx.currentTime);
                osc.frequency.setValueAtTime(659.25, ctx.currentTime + 0.1);
                osc.frequency.setValueAtTime(783.99, ctx.currentTime + 0.2);
                gain.gain.setValueAtTime(0.1, ctx.currentTime);
                gain.gain.linearRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            }
        } catch(e) {}
    }

    $(document).ready(function() {
        // Sticky Side Tabs
        $(document).on('click', '.wppoppop-sidetab, .wppoppop-open-btn', function() {
            const uid = $(this).data('target-uid');
            activatePopup(uid);
        });

        // 1. On-Demand Dynamic Popup Injector
        function activatePopup(uid) {
            let popup = $('#wppoppop-popup-' + uid);
            if (popup.length) {
                showModal(popup);
                return;
            }

            // Fetch markup on-demand via REST
            const fetchUrl = wppoppop_front_vars.rest_url + 'popup/' + uid;
            $.get(fetchUrl, function(res) {
                if (res && res.html) {
                    $('body').append(res.html);
                    popup = $('#wppoppop-popup-' + uid);
                    initPopupInstance(popup);
                    showModal(popup);
                }
            });
        }

        function showModal(popup) {
            popup.addClass('wppoppop-visible').fadeIn(200);
            if (popup.data('sound-fx') == '1') playAudioTone('open');
            $.post(wppoppop_front_vars.ajax_url, { action: 'wppoppop_record_impression', uid: popup.data('uid') });
        }

        // Initialize Existing or Injected Popups
        function initPopupInstance(popup) {
            const uid = popup.data('uid');

            popup.find('.wppoppop-close-btn').on('click', function() {
                popup.removeClass('wppoppop-visible').fadeOut(200);
            });

            // Range Slider Value Binding
            popup.find('.wppoppop-slider-control').on('input', function() {
                const val = $(this).val();
                const prefix = $(this).data('prefix') || '$';
                popup.find('.slider-live-value').text(prefix + val);
            });

            // Countdown Timer Widget
            popup.find('.wppoppop-countdown-widget').each(function() {
                const widget = $(this);
                const totalMins = parseInt(widget.data('mins'), 10) || 15;
                let remaining = totalMins * 60;

                const timerInterval = setInterval(function() {
                    remaining--;
                    if (remaining <= 0) {
                        clearInterval(timerInterval);
                        remaining = 0;
                    }
                    const m = Math.floor(remaining / 60);
                    const s = remaining % 60;
                    widget.find('.unit-mins').text(m < 10 ? '0' + m : m);
                    widget.find('.unit-secs').text(s < 10 ? '0' + s : s);
                }, 1000);
            });

            // Multi-Screen Sequence Transition
            popup.find('.wppoppop-next-screen-btn').on('click', function() {
                popup.find('.wppoppop-screen-container').hide();
                popup.find('.wppoppop-screen-container[data-screen-index="2"]').fadeIn(200);
                popup.find('.wppoppop-progress-fill').css('width', '100%');
                popup.find('.wppoppop-progress-label').text('100% Completed');
            });

            // Form Submission
            popup.find('.wppoppop-submit-trigger').on('click', function(e) {
                e.preventDefault();
                $('.wppoppop-error-bubble').remove();

                const emailInput = popup.find('.wppoppop-field-email');
                const email = emailInput.val();
                if (!email) {
                    const bubble = $('<div class="wppoppop-error-bubble">Please enter an email address.</div>');
                    emailInput.closest('.wppoppop-layer-item').append(bubble);
                    setTimeout(function() { bubble.fadeOut(300, function() { $(this).remove(); }); }, 3000);
                    return;
                }

                const fields = {};
                popup.find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name && name !== 'email' && name !== '_wppoppop_hp_email') fields[name] = $(this).val();
                });

                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_submit_form',
                    uid: uid,
                    email: email,
                    fields: fields
                }, function(res) {
                    if (res.success) {
                        if (popup.data('sound-fx') == '1') playAudioTone('success');
                        const statusOverlay = popup.find('.wppoppop-status-overlay');
                        popup.find('.wppoppop-status-message').text(res.data.message);
                        statusOverlay.fadeIn();
                        setTimeout(function() { popup.removeClass('wppoppop-visible').fadeOut(200); }, 2500);
                    } else {
                        alert(res.data.message);
                    }
                });
            });
        }

        // Initialize preloaded DOM popups
        $('.wppoppop-overlay').each(function() {
            initPopupInstance($(this));
        });

        // 2. Process On-Demand Manifest Triggers
        if (window.wppoppop_manifest && Array.isArray(window.wppoppop_manifest)) {
            window.wppoppop_manifest.forEach(function(item) {
                const triggers = item.triggers || {};

                if (triggers.on_load) {
                    setTimeout(function() { activatePopup(item.uid); }, (parseInt(triggers.on_load_delay, 10) || 0) * 1000);
                }
                if (triggers.on_exit) {
                    $(document).on('mouseleave', function(e) { if (e.clientY <= 0) activatePopup(item.uid); });
                }
                if (triggers.on_scroll && triggers.on_scroll > 0) {
                    $(window).on('scroll', function() {
                        const scrollPct = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
                        if (scrollPct >= triggers.on_scroll) activatePopup(item.uid);
                    });
                }
            });
        }
    });
})(jQuery);
