
(function($) {
    'use strict';

    $(document).ready(function() {
        $('.wppoppop-overlay').each(function() {
            const popup = $(this);
            const uid = popup.data('uid');
            const config = popup.data('config') || {};
            const triggers = config.triggers || {};
            let displayed = false;

            function showPopup() {
                if (displayed) return;
                displayed = true;
                popup.addClass('wppoppop-visible').fadeIn(200);

                // Record impression via AJAX
                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_record_impression',
                    nonce: wppoppop_front_vars.nonce,
                    uid: uid
                });
            }

            function closePopup() {
                popup.removeClass('wppoppop-visible').fadeOut(200);
            }

            popup.find('.wppoppop-close-btn').on('click', closePopup);
            popup.on('click', function(e) {
                if ($(e.target).hasClass('wppoppop-overlay')) closePopup();
            });

            // 1. Page Load Trigger
            if (triggers.on_load) {
                const delay = (parseInt(triggers.on_load_delay, 10) || 0) * 1000;
                setTimeout(showPopup, delay);
            }

            // 2. Exit Intent Trigger
            if (triggers.on_exit) {
                $(document).on('mouseleave', function(e) {
                    if (e.clientY <= 0) showPopup();
                });
            }

            // 3. Scroll Depth Trigger
            if (triggers.on_scroll && triggers.on_scroll > 0) {
                $(window).on('scroll', function() {
                    const scrollPercent = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
                    if (scrollPercent >= triggers.on_scroll) showPopup();
                });
            }

            // 4. Inactivity / Idle Trigger
            if (triggers.on_idle && triggers.on_idle > 0) {
                let idleTime = 0;
                const idleLimit = triggers.on_idle;
                const idleInterval = setInterval(function() {
                    idleTime++;
                    if (idleTime >= idleLimit) {
                        showPopup();
                        clearInterval(idleInterval);
                    }
                }, 1000);

                $(this).on('mousemove keypress scroll', function() {
                    idleTime = 0;
                });
            }

            // 5. Selector Click Trigger
            if (triggers.click_selector) {
                $(document).on('click', triggers.click_selector, function(e) {
                    e.preventDefault();
                    showPopup();
                });
            }

            // Submission Handling via AJAX
            popup.find('.wppoppop-submit-trigger').on('click', function() {
                const emailInput = popup.find('.wppoppop-field-email');
                const email = emailInput.val();

                if (!email) {
                    alert('Please enter your email.');
                    return;
                }

                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_submit_form',
                    nonce: wppoppop_front_vars.nonce,
                    uid: uid,
                    email: email
                }, function(res) {
                    if (res.success) {
                        const statusOverlay = popup.find('.wppoppop-status-overlay');
                        const statusMsg = popup.find('.wppoppop-status-message');
                        statusMsg.text(res.data.message);
                        statusOverlay.fadeIn();

                        setTimeout(function() {
                            closePopup();
                            statusOverlay.hide();
                            emailInput.val('');
                        }, 2500);
                    } else {
                        alert(res.data.message);
                    }
                });
            });
        });
    });
})(jQuery);
