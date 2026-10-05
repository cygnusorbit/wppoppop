
(function($) {
    'use strict';

    $(document).ready(function() {
        const disposableDomains = [
            'mailinator.com', '10minutemail.com', 'tempmail.com', 'guerrillamail.com',
            'sharklasers.com', 'throwawaymail.com', 'yopmail.com', 'trashmail.com',
            'fakeinbox.com', 'dispostable.com', 'getnada.com', 'burnermail.io'
        ];

        // Synthesize Audio Chimes
        const AudioChimes = {
            ctx: null,
            init: function() {
                if (!this.ctx && (window.AudioContext || window.webkitAudioContext)) {
                    this.ctx = new (window.AudioContext || window.webkitAudioContext)();
                }
            },
            playChime: function(type) {
                this.init();
                if (!this.ctx) return;
                try {
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    const now = this.ctx.currentTime;

                    if (type === 'entrance') {
                        osc.frequency.setValueAtTime(440, now);
                        osc.frequency.exponentialRampToValueAtTime(880, now + 0.25);
                        gain.gain.setValueAtTime(0.12, now);
                        gain.gain.linearRampToValueAtTime(0, now + 0.25);
                        osc.start(now);
                        osc.stop(now + 0.25);
                    } else if (type === 'win') {
                        osc.frequency.setValueAtTime(523.25, now);
                        osc.frequency.exponentialRampToValueAtTime(1046.5, now + 0.4);
                        gain.gain.setValueAtTime(0.15, now);
                        gain.gain.linearRampToValueAtTime(0, now + 0.4);
                        osc.start(now);
                        osc.stop(now + 0.4);
                    }
                } catch (e) {}
            }
        };

        // Extract UTM parameters
        const urlParams = new URLSearchParams(window.location.search);
        const utmData = {};
        ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(p) {
            if (urlParams.has(p)) utmData[p] = urlParams.get(p);
        });

        // Initialize Popups & Ribbons
        $('.wppoppop-overlay, .wppoppop-ribbon, .wppoppop-inline-container').each(function() {
            const popup = $(this);
            const uid = popup.data('uid');
            const config = popup.data('config') || {};
            const triggers = config.triggers || {};
            const sounds = config.sounds || {};
            let displayed = false;

            // Interactive Dynamic Tag Replacement ({user_name}, {query:utm})
            function updateDynamicTokens(couponCode) {
                popup.find('.wppoppop-text-render').each(function() {
                    const textElem = $(this);
                    let content = textElem.data('raw-template') || textElem.text();

                    if (wppoppop_front_vars.current_user && wppoppop_front_vars.current_user.logged_in) {
                        content = content.replace(/{user_name}/g, wppoppop_front_vars.current_user.name)
                                         .replace(/{user_email}/g, wppoppop_front_vars.current_user.email);
                    } else {
                        content = content.replace(/{user_name}/g, 'Friend')
                                         .replace(/{user_email}/g, '');
                    }

                    if (couponCode) {
                        content = content.replace(/{coupon_code}/g, couponCode);
                    }

                    urlParams.forEach(function(val, key) {
                        content = content.replace(new RegExp('{query:' + key + '}', 'g'), val);
                    });

                    textElem.html(content);
                });
            }
            updateDynamicTokens('');

            // Auto-fill logged-in email
            if (wppoppop_front_vars.current_user && wppoppop_front_vars.current_user.logged_in) {
                const em = popup.find('.wppoppop-field-email');
                if (em.length && !em.val()) em.val(wppoppop_front_vars.current_user.email);
            }

            // Multi-Screen Navigation
            popup.on('click', '.wppoppop-next-screen-btn', function(e) {
                e.preventDefault();
                const targetIdx = parseInt($(this).data('goto'), 10) || 2;
                const targetScreen = popup.find('.wppoppop-screen-container[data-screen-index="' + targetIdx + '"]');
                if (targetScreen.length) {
                    popup.find('.wppoppop-screen-container').hide().removeClass('wppoppop-screen-active');
                    targetScreen.fadeIn(200).addClass('wppoppop-screen-active');
                }
            });

            // Display Function
            function showPopup() {
                if (displayed) return;
                displayed = true;
                popup.addClass('wppoppop-visible').fadeIn(200);

                if (sounds.enable !== false) AudioChimes.playChime('entrance');

                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_record_impression',
                    uid: uid
                });
            }

            function closePopup() {
                popup.removeClass('wppoppop-visible').fadeOut(200);
            }

            popup.find('.wppoppop-close-btn').on('click', closePopup);

            // Display Triggers
            if (triggers.on_load) {
                setTimeout(showPopup, (parseInt(triggers.on_load_delay, 10) || 0) * 1000);
            }
            if (triggers.on_exit) {
                $(document).on('mouseleave', function(e) {
                    if (e.clientY <= 0) showPopup();
                });
            }

            // Side Tab Trigger Click
            $(document).on('click', '.wppoppop-side-tab[data-target-uid="' + uid + '"], .wppoppop-open-btn[data-target-uid="' + uid + '"]', function(e) {
                e.preventDefault();
                showPopup();
            });

            // Form Submission with Validation & Disposable Shield
            popup.find('.wppoppop-submit-trigger').on('click', function(e) {
                e.preventDefault();
                $('.wppoppop-error-bubble').remove();

                const emailInput = popup.find('.wppoppop-field-email');
                const emailVal = (emailInput.val() || '').trim();

                if (!emailVal) {
                    showErrorBubble(emailInput.closest('.wppoppop-layer-item'), 'Email address is required');
                    return;
                }

                const domain = emailVal.split('@')[1] ? emailVal.split('@')[1].toLowerCase() : '';
                if (disposableDomains.indexOf(domain) !== -1) {
                    showErrorBubble(emailInput.closest('.wppoppop-layer-item'), 'Temporary emails are not accepted');
                    return;
                }

                const fields = {};
                popup.find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name && name !== '_wppoppop_hp_email') {
                        fields[name] = $(this).val();
                    }
                });

                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_submit_form',
                    uid: uid,
                    email: emailVal,
                    country: wppoppop_front_vars.visitor_country || '',
                    utm_data: utmData,
                    fields: fields
                }, function(res) {
                    if (res.success) {
                        if (sounds.enable !== false) AudioChimes.playChime('win');

                        if (res.data.coupon_code) {
                            updateDynamicTokens(res.data.coupon_code);
                        }

                        const statusOverlay = popup.find('.wppoppop-status-overlay');
                        popup.find('.wppoppop-status-message').text(res.data.message);
                        statusOverlay.fadeIn();

                        setTimeout(function() {
                            if (res.data.download_url) window.location.href = res.data.download_url;
                            if (res.data.redirect_url) window.location.href = res.data.redirect_url;
                            if (!res.data.download_url && !res.data.redirect_url) {
                                closePopup();
                                statusOverlay.hide();
                            }
                        }, 2500);
                    } else {
                        showErrorBubble(emailInput.closest('.wppoppop-layer-item'), res.data.message);
                    }
                });
            });

            function showErrorBubble(layer, text) {
                const bubble = $('<div class="wppoppop-error-bubble">' + text + '</div>');
                layer.append(bubble);
                setTimeout(function() { bubble.fadeOut(300, function() { $(this).remove(); }); }, 3500);
            }
        });
    });
})(jQuery);
