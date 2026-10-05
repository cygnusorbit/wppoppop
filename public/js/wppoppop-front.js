
(function($) {
    'use strict';

    $(document).ready(function() {
        // Sticky Side Tabs Click Action
        $(document).on('click', '.wppoppop-sidetab, .wppoppop-open-btn', function() {
            const uid = $(this).data('target-uid');
            const targetPopup = $('#wppoppop-popup-' + uid);
            if (targetPopup.length) {
                targetPopup.addClass('wppoppop-visible').fadeIn(200);
            }
        });

        // Video Events Listener
        $('video').each(function() {
            const videoElem = this;
            $('.wppoppop-overlay').each(function() {
                const popup = $(this);
                const config = popup.data('config') || {};
                const videoCfg = config.video || {};

                if (videoCfg.enable) {
                    if (videoCfg.mode === 'ended') {
                        videoElem.addEventListener('ended', function() {
                            popup.addClass('wppoppop-visible').fadeIn(200);
                        });
                    } else if (videoCfg.mode === 'play') {
                        videoElem.addEventListener('play', function() {
                            popup.addClass('wppoppop-visible').fadeIn(200);
                        });
                    }
                }
            });
        });

        // Modal Handlers
        $('.wppoppop-overlay').each(function() {
            const popup = $(this);
            const uid = popup.data('uid');
            const config = popup.data('config') || {};
            const triggers = config.triggers || {};
            const paymentCfg = config.payment || {};
            let displayed = false;

            function showPopup() {
                if (displayed) return;
                displayed = true;
                popup.addClass('wppoppop-visible').fadeIn(200);

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

            // Display Triggers
            if (triggers.on_load) {
                setTimeout(showPopup, (parseInt(triggers.on_load_delay, 10) || 0) * 1000);
            }
            if (triggers.on_exit) {
                $(document).on('mouseleave', function(e) {
                    if (e.clientY <= 0) showPopup();
                });
            }

            // Payment Submission Trigger
            popup.find('.wppoppop-pay-trigger').on('click', function() {
                const email = popup.find('.wppoppop-field-email').val();
                if (!email) {
                    alert('Please enter your email to proceed to checkout.');
                    return;
                }

                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_process_payment',
                    nonce: wppoppop_front_vars.nonce,
                    uid: uid,
                    email: email,
                    amount: paymentCfg.amount || 19.99,
                    currency: paymentCfg.currency || 'USD',
                    gateway: paymentCfg.gateway || 'Stripe'
                }, function(res) {
                    if (res.success) {
                        const statusOverlay = popup.find('.wppoppop-status-overlay');
                        popup.find('.wppoppop-status-message').html(res.data.message + '<br><small>TX: ' + res.data.transaction_id + '</small>');
                        statusOverlay.fadeIn();
                        setTimeout(function() {
                            closePopup();
                            statusOverlay.hide();
                        }, 3000);
                    } else {
                        alert(res.data.message);
                    }
                });
            });

            // Standard Form Submission
            popup.find('.wppoppop-submit-trigger').on('click', function() {
                const emailInput = popup.find('.wppoppop-field-email');
                const email = emailInput.val();

                if (!email) {
                    alert('Please enter your email address.');
                    return;
                }

                const fields = {};
                popup.find('input').each(function() {
                    const name = $(this).attr('name');
                    if (name && name !== 'email') fields[name] = $(this).val();
                });

                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_submit_form',
                    nonce: wppoppop_front_vars.nonce,
                    uid: uid,
                    email: email,
                    fields: fields
                }, function(res) {
                    if (res.success) {
                        const statusOverlay = popup.find('.wppoppop-status-overlay');
                        let messageHtml = res.data.message;

                        if (res.data.download_url) {
                            messageHtml += '<br><a href="' + res.data.download_url + '" class="button button-primary" style="margin-top:10px;display:inline-block;">Download File Now</a>';
                        }

                        // Unlock content locker immediately on page
                        const locker = $('.wppoppop-content-locker[data-locker-uid="' + uid + '"]');
                        if (locker.length) {
                            locker.find('.wppoppop-locked-content').css({ 'filter': 'none', 'pointer-events': 'auto' });
                            locker.find('.wppoppop-locker-overlay').fadeOut();
                        }

                        popup.find('.wppoppop-status-message').html(messageHtml);
                        statusOverlay.fadeIn();

                        setTimeout(function() {
                            if (res.data.redirect_url) {
                                window.location.href = res.data.redirect_url;
                            } else if (!res.data.download_url) {
                                closePopup();
                                statusOverlay.hide();
                            }
                        }, 2500);
                    } else {
                        alert(res.data.message);
                    }
                });
            });
        });
    });
})(jQuery);
