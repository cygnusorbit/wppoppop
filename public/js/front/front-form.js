/**
 * WpPopPop Frontend Lead Capture & Form Submission Controller
 * Handles token extraction, reCAPTCHA resolution, and server dispatch
 */
(function(window, $) {
    'use strict';

    var vars = window.wppoppop_front_vars || {
        ajax_url: '',
        nonce: '',
        enable_recaptcha: false,
        recaptcha_site_key: ''
    };

    window.WpPopPopFrontForm = {
        init: function() {
            this.bindSubmissions();
        },

        bindSubmissions: function() {
            var self = this;

            $(document).on('click', '.wppoppop-submit-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $canvas = $btn.closest('.wppoppop-canvas-stage');
                var $popup = $btn.closest('.wppoppop-popup-wrap');
                var uid = $popup.data('uid');

                self.processSubmission($canvas, $btn, uid);
            });
        },

        processSubmission: function($canvas, $btn, uid) {
            var self = this;
            var fields = {};
            var hasEmail = false;
            var emailVal = '';

            // Extract input values from the active canvas
            $canvas.find('input, select, textarea').each(function() {
                var $input = $(this);
                var name = $input.attr('name') || $input.data('field-name') || $input.attr('type');
                if (!name || $input.attr('type') === 'submit') return;

                var val = $input.val();
                fields[name] = val;

                if ($input.attr('type') === 'email' || name.indexOf('email') !== false) {
                    hasEmail = true;
                    emailVal = val;
                }
            });

            // Basic email validation
            if (hasEmail && emailVal) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailVal)) {
                    self.showError($canvas, 'Please enter a valid email address.');
                    return;
                }
            }

            var origText = $btn.text();
            $btn.prop('disabled', true).text('Submitting...');

            // If reCAPTCHA is active, execute and obtain token before AJAX dispatch
            if (vars.enable_recaptcha && vars.recaptcha_site_key && typeof window.grecaptcha !== 'undefined') {
                window.grecaptcha.ready(function() {
                    window.grecaptcha.execute(vars.recaptcha_site_key, { action: 'wppoppop_submit' })
                        .then(function(token) {
                            self.dispatchAjax(fields, token, $canvas, $btn, origText, uid);
                        })
                        .catch(function() {
                            // If token resolution fails, fall back to direct dispatch
                            self.dispatchAjax(fields, '', $canvas, $btn, origText, uid);
                        });
                });
            } else {
                self.dispatchAjax(fields, '', $canvas, $btn, origText, uid);
            }
        },

        dispatchAjax: function(fields, recaptchaToken, $canvas, $btn, origText, uid) {
            var self = this;
            var payload = {
                action: 'wppoppop_submit_form',
                nonce: vars.nonce,
                popup_uid: uid,
                fields: fields,
                recaptcha_token: recaptchaToken
            };

            $.ajax({
                url: vars.ajax_url,
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).text(origText);

                    if (res && res.success) {
                        // Trigger celebration if loaded
                        if (window.WpPopPopFrontDisplay && typeof window.WpPopPopFrontDisplay.triggerCelebration === 'function') {
                            window.WpPopPopFrontDisplay.triggerCelebration();
                        }
                        $(document).trigger('wppoppop:form_submitted', [res.data]);

                        // Redirect or display completion message
                        if (res.data && res.data.redirect_url) {
                            window.location.href = res.data.redirect_url;
                        } else {
                            var successMsg = (res.data && res.data.message) ? res.data.message : 'Thank you for your submission!';
                            self.showSuccess($canvas, successMsg);
                            setTimeout(function() {
                                if (window.WpPopPopFrontDisplay) {
                                    window.WpPopPopFrontDisplay.close(uid);
                                }
                            }, 2000);
                        }
                    } else {
                        var errMsg = (res && res.data && res.data.message) ? res.data.message : 'Submission failed. Please try again.';
                        self.showError($canvas, errMsg);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text(origText);
                    self.showError($canvas, 'Network error. Please try again.');
                }
            });
        },

        showError: function($canvas, msg) {
            var $msgBox = $canvas.find('.wppoppop-status-bubble');
            if (!$msgBox.length) {
                $msgBox = $('<div class="wppoppop-status-bubble" style="color:#dc2626;font-size:12px;font-weight:600;margin-top:8px;text-align:center;"></div>');
                $canvas.find('.wppoppop-canvas-content').append($msgBox);
            }
            $msgBox.css('color', '#dc2626').text(msg).show();
        },

        showSuccess: function($canvas, msg) {
            var $msgBox = $canvas.find('.wppoppop-status-bubble');
            if (!$msgBox.length) {
                $msgBox = $('<div class="wppoppop-status-bubble" style="color:#16a34a;font-size:12px;font-weight:600;margin-top:8px;text-align:center;"></div>');
                $canvas.find('.wppoppop-canvas-content').append($msgBox);
            }
            $msgBox.css('color', '#16a34a').text(msg).show();
        }
    };

    $(document).ready(function() {
        window.WpPopPopFrontForm.init();
    });

})(window, jQuery);
