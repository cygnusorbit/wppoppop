(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Submit = {
        init: function($popup, config) {
            var self = this;
            var uid = $popup.data('uid');

            // Form Submit Trigger
            $popup.on('click', '.wppoppop-submit-trigger', function(e) {
                e.preventDefault();
                self.processForm($popup, config);
            });

            // Instant Payment Trigger
            $popup.on('click', '.wppoppop-pay-trigger', function(e) {
                e.preventDefault();
                self.processPayment($popup, config, $(this));
            });

            // Signature Pad clearing
            $popup.find('.wppoppop-sig-clear-btn').on('click', function() {
                var canvas = $(this).siblings('.wppoppop-sig-canvas')[0];
                if (canvas) {
                    var ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    $(this).siblings('.wppoppop-sig-input').val('');
                }
            });

            this.initSignaturePad($popup);
        },

        initSignaturePad: function($popup) {
            $popup.find('.wppoppop-sig-canvas').each(function() {
                var canvas = this;
                var ctx = canvas.getContext('2d');
                var isDrawing = false;
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.strokeStyle = '#1e293b';

                $(canvas).on('mousedown touchstart', function(e) {
                    isDrawing = true;
                    var offset = $(canvas).offset();
                    var x = (e.pageX || e.originalEvent.touches[0].pageX) - offset.left;
                    var y = (e.pageY || e.originalEvent.touches[0].pageY) - offset.top;
                    ctx.beginPath();
                    ctx.moveTo(x, y);
                }).on('mousemove touchmove', function(e) {
                    if (!isDrawing) return;
                    var offset = $(canvas).offset();
                    var x = (e.pageX || e.originalEvent.touches[0].pageX) - offset.left;
                    var y = (e.pageY || e.originalEvent.touches[0].pageY) - offset.top;
                    ctx.lineTo(x, y);
                    ctx.stroke();
                }).on('mouseup touchend', function() {
                    isDrawing = false;
                    $(canvas).siblings('.wppoppop-sig-input').val(canvas.toDataURL());
                });
            });
        },

        showErrorBubble: function($field, msg) {
            $('.wppoppop-error-bubble').remove();
            var $bubble = $('<div class="wppoppop-error-bubble">' + msg + '</div>');
            $('body').append($bubble);

            var offset = $field.offset();
            $bubble.css({
                position: 'absolute',
                top: (offset.top - $bubble.outerHeight() - 8) + 'px',
                left: offset.left + 'px',
                background: '#ef4444',
                color: '#ffffff',
                padding: '6px 12px',
                borderRadius: '4px',
                fontSize: '12px',
                fontWeight: '600',
                zIndex: 9999999,
                boxShadow: '0 4px 12px rgba(239, 68, 68, 0.3)'
            }).fadeIn(200);

            setTimeout(function() { $bubble.fadeOut(300, function() { $bubble.remove(); }); }, 3500);
        },

        validate: function($popup) {
            var self = this;
            var isValid = true;
            var disposableDomains = ['mailinator.com', '10minutemail.com', 'guerrillamail.com', 'trashmail.com', 'tempmail.com', 'sharklasers.com'];

            // Anti-Spam Honeypot
            if ($popup.find('input[name="_wppoppop_hp_email"]').val()) {
                return false;
            }

            // Required field check
            $popup.find('.wppoppop-layer-item[data-required="1"]').each(function() {
                var $input = $(this).find('input, select, textarea');
                var val = $input.val();
                if (!val || (Array.isArray(val) && val.length === 0) || ($input.is(':checkbox') && !$input.is(':checked'))) {
                    self.showErrorBubble($input, $(this).data('error-msg') || 'This field is required');
                    isValid = false;
                    return false;
                }
            });

            if (!isValid) return false;

            // Email format & Disposable Domain Validation
            var $emailInput = $popup.find('.wppoppop-field-email');
            if ($emailInput.length) {
                var emailVal = $emailInput.val().trim();
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailVal)) {
                    self.showErrorBubble($emailInput, 'Please enter a valid email address.');
                    return false;
                }
                var domain = emailVal.split('@')[1] ? emailVal.split('@')[1].toLowerCase() : '';
                if (disposableDomains.indexOf(domain) !== -1) {
                    self.showErrorBubble($emailInput, 'Disposable and temporary email addresses are not allowed.');
                    return false;
                }
            }

            return true;
        },

        processForm: function($popup, config) {
            var self = this;
            if (!this.validate($popup)) return;

            var uid = $popup.data('uid');
            var $btn = $popup.find('.wppoppop-submit-trigger');
            $btn.prop('disabled', true).text('Submitting...');

            var fields = {};
            $popup.find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name && name !== '_wppoppop_hp_email') {
                    fields[name] = $(this).val();
                }
            });

            var email = fields['email'] || $popup.find('.wppoppop-field-email').val() || '';
            var restUrl = (window.wppoppop_front_vars && window.wppoppop_front_vars.rest_url) || '';
            var ajaxUrl = (window.wppoppop_front_vars && window.wppoppop_front_vars.ajax_url) || '';

            var payload = {
                uid: uid,
                email: email,
                fields: fields,
                country: (window.wppoppop_front_vars && window.wppoppop_front_vars.visitor_country) || '',
                quiz_score: window.WpPopPopFront.Logic.tokens['quiz_score'] || 0
            };

            function handleSuccess(res) {
                $btn.prop('disabled', false).text('Submitted');

                // Mark submission cookie
                window.WpPopPopFront.Display.setCookie('wppoppop_sub_' + uid, '1', 365);
                window.WpPopPopFront.Display.setCookie('wppoppop_unlocked_' + uid, '1', 365);

                // Sound & Confetti
                if (window.WpPopPopFront.Gamification) {
                    window.WpPopPopFront.Gamification.playChime('success');
                    window.WpPopPopFront.Gamification.launchConfetti($popup);
                }

                // Show status overlay
                var $status = $popup.find('.wppoppop-status-overlay');
                $status.find('.wppoppop-status-message').text((res.data && res.data.message) || res.message || 'Thank you! Submitted successfully.');
                $status.fadeIn(200);

                // Auto-apply WooCommerce coupon if active
                var woo = config.woocommerce || {};
                if (woo.auto_apply && ajaxUrl) {
                    $.post(ajaxUrl, {
                        action: 'wppoppop_apply_wc_coupon',
                        nonce: window.wppoppop_front_vars.nonce,
                        coupon_code: fields['coupon'] || (woo.prefix + '_PROMO')
                    });
                }

                // Redirect or Auto-close
                setTimeout(function() {
                    var redir = (res.data && res.data.redirect_url) || res.redirect_url;
                    if (redir) {
                        window.location.href = redir;
                    } else {
                        window.WpPopPopFront.Display.close($popup, config);
                    }
                }, 2000);
            }

            if (restUrl) {
                fetch(restUrl + 'submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                }).then(function(r) { return r.json(); }).then(handleSuccess).catch(function() {
                    $btn.prop('disabled', false).text('Submit');
                    alert('Submission error. Please check your connection.');
                });
            } else {
                $.post(ajaxUrl, Object.assign({ action: 'wppoppop_submit_form' }, payload), handleSuccess);
            }
        },

        processPayment: function($popup, config, $btn) {
            var uid = $popup.data('uid');
            var email = $popup.find('.wppoppop-field-email').val() || 'customer@example.com';
            var amt = $btn.data('amount') || '10.00';
            var cur = $btn.data('currency') || 'USD';
            var ajaxUrl = (window.wppoppop_front_vars && window.wppoppop_front_vars.ajax_url) || '';

            $btn.prop('disabled', true).text('Processing Payment...');

            $.post(ajaxUrl, {
                action: 'wppoppop_process_payment',
                uid: uid,
                email: email,
                amount: amt,
                currency: cur,
                gateway: 'Stripe'
            }).done(function(res) {
                if (res.success) {
                    alert(res.data.message || 'Payment completed!');
                    window.WpPopPopFront.Display.close($popup, config);
                }
            }).always(function() {
                $btn.prop('disabled', false).text('Pay ' + amt + ' ' + cur);
            });
        }
    };

    window.WpPopPopFront.Submit = Submit;
})(window, jQuery);
