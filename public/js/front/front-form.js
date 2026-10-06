(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Form = {
        init: function() {
            this.bindTokenInterpolation();
            this.bindScreenTransitions();
            this.bindSubmissions();
            this.bindStickySideTabs();
        },

        getVars: function() {
            return window.wppoppop_front_vars || {
                ajax_url: '',
                rest_url: '',
                nonce: '',
                cookie_epoch: 1
            };
        },

        bindTokenInterpolation: function() {
            $(document).on('input', '.wppoppop-popup-wrap input, .wppoppop-popup-wrap select', function() {
                var key = $(this).attr('name') || $(this).data('field-name');
                var val = $(this).val();
                if (key) {
                    $(this).closest('.wppoppop-popup-wrap').find('.wppoppop-token-' + key).text(val);
                }
            });
        },

        bindScreenTransitions: function() {
            $(document).on('click', '.wppoppop-next-step', function(e) {
                e.preventDefault();
                var targetScreen = $(this).data('goto-screen') || 2;
                var $wrap = $(this).closest('.wppoppop-popup-wrap');

                $wrap.find('.wppoppop-screen').hide();
                $wrap.find('.wppoppop-screen[data-screen="' + targetScreen + '"]').fadeIn(200);

                var $progressBar = $wrap.find('.wppoppop-progress-fill');
                if ($progressBar.length) {
                    var pct = targetScreen >= 2 ? (targetScreen * 35) + '%' : '30%';
                    $progressBar.css('width', pct);
                }
            });
        },

        bindSubmissions: function() {
            var self = this;
            $(document).on('submit', '.wppoppop-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $wrap = $form.closest('.wppoppop-popup-wrap');
                var uid = $wrap.data('uid');

                var emailVal = $form.find('input[type="email"]').val() || '';
                if (emailVal && /@(mailinator|10minutemail|guerrillamail)\.com$/i.test(emailVal)) {
                    self.showError($form.find('input[type="email"]'), 'Disposable emails not permitted.');
                    return;
                }

                var $btn = $form.find('button[type="submit"]');
                $btn.prop('disabled', true).text('Submitting...');

                var vars = self.getVars();
                var formData = $form.serializeArray();
                var payload = { uid: uid, nonce: vars.nonce, data: {} };

                $.each(formData, function(i, field) {
                    payload.data[field.name] = field.value;
                });

                var endpoint = (vars.rest_url ? vars.rest_url + 'submit' : vars.ajax_url);
                var postData = vars.rest_url ? JSON.stringify(payload) : { action: 'wppoppop_submit_form', nonce: vars.nonce, uid: uid, data: payload.data };

                $.ajax({
                    url: endpoint,
                    type: 'POST',
                    data: postData,
                    contentType: vars.rest_url ? 'application/json' : 'application/x-www-form-urlencoded; charset=UTF-8'
                }).done(function(res) {
                    if (res.success || res.status === 'success') {
                        if (window.WpPopPopFront.Modal) {
                            window.WpPopPopFront.Modal.playChime('success');
                            window.WpPopPopFront.Modal.setCookie('wppoppop_sub_' + uid, vars.cookie_epoch || 1, 30);
                        }
                        if (window.WpPopPopFront.Elements) {
                            window.WpPopPopFront.Elements.triggerConfetti();
                        }

                        // Unlock Inline Content Lockers
                        $('[data-locked-uid="' + uid + '"]').addClass('wppoppop-unlocked').siblings('.wppoppop-locker-overlay').fadeOut(250);

                        // Auto-apply WooCommerce coupon if present
                        if (res.coupon_code && typeof wc_add_to_cart_params !== 'undefined') {
                            $.post('/?wc-ajax=apply_coupon', { coupon_code: res.coupon_code });
                        }

                        // Tokenized Secure Download redirect if enabled
                        if (res.download_url) {
                            window.location.href = res.download_url;
                        }

                        $wrap.find('.wppoppop-screen').hide();
                        var $successScreen = $wrap.find('.wppoppop-screen-success');
                        if ($successScreen.length) {
                            $successScreen.fadeIn(200);
                        } else {
                            setTimeout(function() {
                                if (window.WpPopPopFront.Modal) window.WpPopPopFront.Modal.close(uid);
                            }, 1200);
                        }
                    } else {
                        alert(res.message || 'Submission error. Please check your fields.');
                        $btn.prop('disabled', false).text('Submit');
                    }
                }).fail(function() {
                    alert('Network error while processing form submission.');
                    $btn.prop('disabled', false).text('Submit');
                });
            });
        },

        showError: function($el, msg) {
            $('.wppoppop-error-bubble').remove();
            var $bubble = $('<div class="wppoppop-error-bubble">' + msg + '</div>');
            $el.parent().css('position', 'relative').append($bubble);
            setTimeout(function() { $bubble.fadeOut(200, function() { $(this).remove(); }); }, 3000);
        },

        bindStickySideTabs: function() {
            $(document).on('click', '.wppoppop-sidetab', function() {
                var uid = $(this).data('target-uid');
                if (uid && window.WpPopPopFront.Modal) {
                    window.WpPopPopFront.Modal.open(uid);
                }
            });
        }
    };

    window.WpPopPopFront.Form = Form;
})(window, jQuery);
