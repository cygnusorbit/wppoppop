
(function($) {
    'use strict';

    $(document).ready(function() {
        $('.wppoppop-overlay').each(function() {
            const popup = $(this);
            const uid = popup.data('uid');
            const config = popup.data('config') || {};
            const triggers = config.triggers || {};
            const customJs = config.custom_js || {};
            const logic = config.conditional_logic || {};
            const math = config.math || {};
            let displayed = false;

            function showPopup() {
                if (displayed) return;
                displayed = true;
                popup.addClass('wppoppop-visible').fadeIn(200);

                // Run Custom On Init JS Handler
                if (customJs.on_init) {
                    try {
                        const initFunc = new Function('popup', '$', customJs.on_init);
                        initFunc(popup, $);
                    } catch (e) {
                        console.error('WpPopPop on_init error:', e);
                    }
                }

                // Record Impression
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

            // 1. Triggers
            if (triggers.on_load) {
                const delay = (parseInt(triggers.on_load_delay, 10) || 0) * 1000;
                setTimeout(showPopup, delay);
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

            if (triggers.click_selector) {
                $(document).on('click', triggers.click_selector, function(e) {
                    e.preventDefault();
                    showPopup();
                });
            }

            // 2. Real-Time Conditional Logic & Math Expression Evaluator
            function evaluateDynamicState() {
                const fieldValues = {};
                popup.find('input').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        fieldValues[name] = $(this).val();
                    }
                });

                // Conditional Logic
                if (logic.if_field && logic.target_layer) {
                    const currentVal = fieldValues[logic.if_field];
                    const targetEl = $('#' + logic.target_layer);
                    if (currentVal == logic.equals_val) {
                        logic.action === 'show' ? targetEl.show() : targetEl.hide();
                    } else {
                        logic.action === 'show' ? targetEl.hide() : targetEl.show();
                    }
                }

                // Math Expression
                if (math.expression && math.output_target) {
                    let expr = math.expression;
                    for (const [key, val] of Object.entries(fieldValues)) {
                        const num = parseFloat(val) || 0;
                        expr = expr.replace(new RegExp('\\{' + key + '\\}', 'g'), num);
                    }
                    try {
                        const computed = Function('"use strict"; return (' + expr + ')')();
                        $('#' + math.output_target).find('.wppoppop-text-render').text(computed);
                    } catch (e) {
                        // Incomplete expression, silently pass
                    }
                }
            }

            popup.on('input change', 'input', evaluateDynamicState);

            // 3. Form Submission Handling
            popup.find('.wppoppop-submit-trigger').on('click', function() {
                const emailInput = popup.find('.wppoppop-field-email');
                const email = emailInput.val();

                if (!email) {
                    alert('Please enter your email address.');
                    return;
                }

                const additionalFields = {};
                popup.find('input').each(function() {
                    const name = $(this).attr('name');
                    if (name && name !== 'email') {
                        additionalFields[name] = $(this).val();
                    }
                });

                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_submit_form',
                    nonce: wppoppop_front_vars.nonce,
                    uid: uid,
                    email: email,
                    fields: additionalFields
                }, function(res) {
                    if (res.success) {
                        // Execute Custom On Submit JS Handler
                        if (customJs.on_submit) {
                            try {
                                const submitFunc = new Function('popup', '$', 'response', customJs.on_submit);
                                submitFunc(popup, $, res.data);
                            } catch (e) {
                                console.error('WpPopPop on_submit error:', e);
                            }
                        }

                        const statusOverlay = popup.find('.wppoppop-status-overlay');
                        const statusMsg = popup.find('.wppoppop-status-message');
                        statusMsg.text(res.data.message);
                        statusOverlay.fadeIn();

                        setTimeout(function() {
                            if (res.data.redirect_url) {
                                window.location.href = res.data.redirect_url;
                            } else {
                                closePopup();
                                statusOverlay.hide();
                                emailInput.val('');
                            }
                        }, 2000);
                    } else {
                        alert(res.data.message);
                    }
                });
            });
        });
    });
})(jQuery);
