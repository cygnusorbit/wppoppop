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
            $(document).on('input', '.wppoppop-popup-wrap input, .wppoppop-popup-wrap select, .wppoppop-box input, .wppoppop-box select', function() {
                var key = $(this).attr('name') || $(this).data('field-name');
                var val = $(this).val();
                if (key) {
                    $(this).closest('.wppoppop-box, .wppoppop-popup-wrap').find('.wppoppop-token-' + key).text(val);
                }
            });
        },

        bindScreenTransitions: function() {
            $(document).on('click', '.wppoppop-next-canvas-btn, .wppoppop-next-screen-btn, .wppoppop-next-step', function(e) {
                e.preventDefault();
                var targetCanvas = parseInt($(this).data('goto-canvas') || $(this).data('goto-screen') || $(this).data('goto'), 10) || 2;
                var $popup = $(this).closest('.wppoppop-overlay, .wppoppop-popup-wrap');

                if (window.WpPopPopFront && window.WpPopPopFront.Display) {
                    window.WpPopPopFront.Display.switchCanvas($popup, targetCanvas);
                } else {
                    $popup.find('.wppoppop-canvas-container, .wppoppop-screen-container').hide().removeClass('wppoppop-canvas-active wppoppop-screen-active');
                    $popup.find('[data-canvas-index="' + targetCanvas + '"], [data-screen-index="' + targetCanvas + '"]').fadeIn(200).addClass('wppoppop-canvas-active wppoppop-screen-active');
                }
            });
        },

        bindSubmissions: function() {
            var self = this;
            $(document).on('submit', '.wppoppop-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $wrap = $form.closest('.wppoppop-popup-wrap, .wppoppop-overlay');
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

                        $('[data-locked-uid="' + uid + '"]').addClass('wppoppop-unlocked').siblings('.wppoppop-locker-overlay').fadeOut(250);

                        if (res.download_url) {
                            window.location.href = res.download_url;
                        }

                        $wrap.find('.wppoppop-canvas-container, .wppoppop-screen-container').hide();
                        var $successScreen = $wrap.find('.wppoppop-screen-success');
                        if ($successScreen.length) {
                            $successScreen.fadeIn(200);
                        } else {
                            setTimeout(function() {
                                if (window.WpPopPopFront.Display) window.WpPopPopFront.Display.close($wrap, {});
                                else if (window.WpPopPopFront.Modal) window.WpPopPopFront.Modal.close(uid);
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
            $(document).on('click', '.wppoppop-sidetab, .wppoppop-side-tab', function() {
                var uid = $(this).data('target-uid');
                var $target = $('#wppoppop-popup-' + uid);
                if ($target.length && window.WpPopPopFront.Display) {
                    window.WpPopPopFront.Display.show($target, {});
                } else if (uid && window.WpPopPopFront.Modal) {
                    window.WpPopPopFront.Modal.open(uid);
                }
            });
        }
    };

    window.WpPopPopFront.Form = Form;
})(window, jQuery);
