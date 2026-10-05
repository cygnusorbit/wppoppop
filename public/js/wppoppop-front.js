
(function($) {
    'use strict';

    $(document).ready(function() {
        $('.wppoppop-overlay').each(function() {
            const popup = $(this);
            const uid = popup.data('uid');
            const config = popup.data('config') || {};
            const triggers = config.triggers || {};
            const freq = config.frequency || {};
            let displayed = false;

            // 1. Frequency Capping & Cookie Rules Evaluation
            function canDisplay() {
                if (freq.hide_submitted && getCookie('wppoppop_submitted_' + uid)) {
                    return false;
                }
                if (freq.mode === 'once_session' && sessionStorage.getItem('wppoppop_seen_' + uid)) {
                    return false;
                }
                if (freq.mode === 'days' && getCookie('wppoppop_seen_' + uid)) {
                    return false;
                }
                return true;
            }

            if (!canDisplay()) return;

            // 2. Play Staggered Entrance Animations
            function triggerLayerAnimations(screenContainer) {
                screenContainer.find('.wppoppop-layer-item').each(function() {
                    const layer = $(this);
                    const anim = layer.data('anim');
                    const delay = parseInt(layer.data('anim-delay'), 10) || 0;
                    const duration = parseInt(layer.data('anim-duration'), 10) || 500;

                    if (anim && anim !== 'none') {
                        layer.css({
                            'opacity': 0,
                            'animation-delay': delay + 'ms',
                            'animation-duration': duration + 'ms'
                        });
                        setTimeout(function() {
                            layer.addClass('anim-triggered');
                        }, 20);
                    }
                });
            }

            function showPopup() {
                if (displayed) return;
                displayed = true;
                popup.addClass('wppoppop-visible').fadeIn(200);

                // Mark seen
                if (freq.mode === 'once_session') {
                    sessionStorage.setItem('wppoppop_seen_' + uid, '1');
                } else if (freq.mode === 'days') {
                    setCookie('wppoppop_seen_' + uid, '1', freq.days || 7);
                }

                triggerLayerAnimations(popup.find('.wppoppop-screen-container[data-screen-index="1"]'));

                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_record_impression',
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

            // 3. Multi-Step Transition Handler
            popup.find('.wppoppop-next-screen-btn').on('click', function() {
                const nextScreenIndex = $(this).data('goto') || 2;
                popup.find('.wppoppop-screen-container').hide();
                const nextContainer = popup.find('.wppoppop-screen-container[data-screen-index="' + nextScreenIndex + '"]');
                nextContainer.fadeIn(200);
                triggerLayerAnimations(nextContainer);
            });

            // 4. Real-Time Interactive Input Replacement
            popup.on('input', 'input', function() {
                const name = $(this).attr('name');
                const val = $(this).val();
                if (!name) return;

                popup.find('.wppoppop-text-render').each(function() {
                    const textContainer = $(this);
                    let raw = textContainer.data('raw-template');
                    if (raw && raw.indexOf('{' + name + '}') !== -1) {
                        textContainer.text(raw.replace(new RegExp('\\{' + name + '\\}', 'g'), val || '...'));
                    }
                });
            });

            // 5. Triggers
            if (triggers.on_load) {
                setTimeout(showPopup, (parseInt(triggers.on_load_delay, 10) || 0) * 1000);
            }
            if (triggers.on_exit) {
                $(document).on('mouseleave', function(e) {
                    if (e.clientY <= 0) showPopup();
                });
            }
            if (triggers.on_scroll && triggers.on_scroll > 0) {
                $(window).on('scroll', function() {
                    const scrollPercent = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
                    if (scrollPercent >= triggers.on_scroll) showPopup();
                });
            }

            // 6. Form Submission
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
                    uid: uid,
                    email: email,
                    fields: fields
                }, function(res) {
                    if (res.success) {
                        if (freq.hide_submitted) {
                            setCookie('wppoppop_submitted_' + uid, '1', 365);
                        }

                        const statusOverlay = popup.find('.wppoppop-status-overlay');
                        popup.find('.wppoppop-status-message').text(res.data.message);
                        statusOverlay.fadeIn();

                        setTimeout(function() {
                            closePopup();
                            statusOverlay.hide();
                        }, 2500);
                    } else {
                        alert(res.data.message);
                    }
                });
            });

            function setCookie(cname, cvalue, exdays) {
                const d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                document.cookie = cname + "=" + cvalue + ";expires=" + d.toUTCString() + ";path=/";
            }
            function getCookie(cname) {
                const name = cname + "=";
                const ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i].trim();
                    if (c.indexOf(name) === 0) return c.substring(name.length, c.length);
                }
                return "";
            }
        });
    });
})(jQuery);
