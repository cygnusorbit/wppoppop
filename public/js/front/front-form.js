/**
 * WpPopPop Frontend Runtime: Form Submission, Action Routing & Interactive Button Controller
 * Full Support for all 26 Canvas Elements, Text Field Validation, Submit Actions & Close Triggers
 */
(function($) {
    'use strict';

    window.WpPopPopFrontForm = {
        init: function($popup) {
            this.bindEvents($popup);
            this.interpolateTokens($popup);
        },

        bindEvents: function($popup) {
            var self = this;
            var uid = $popup.data('uid');

            // 1. Form Submission via Submit Button or Enter Key
            $popup.find('.wppoppop-form').off('submit.wppoppopForm').on('submit.wppoppopForm', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $submitBtn = $form.find('.wppoppop-submit-btn, button[type="submit"]').first();
                self.handleSubmit($popup, $form, $submitBtn);
            });

            // 2. Direct Click on Submit Buttons
            $popup.off('click.wppoppopSubmit', '.wppoppop-submit-btn').on('click.wppoppopSubmit', '.wppoppop-submit-btn', function(e) {
                var $btn = $(this);
                var $form = $btn.closest('.wppoppop-form');
                if ($form.length) {
                    e.preventDefault();
                    self.handleSubmit($popup, $form, $btn);
                }
            });

            // 3. CTA Link Buttons with Analytics Tracking
            $popup.off('click.wppoppopLink', '.wppoppop-link-btn').on('click.wppoppopLink', '.wppoppop-link-btn', function(e) {
                var $link = $(this);
                var href = $link.attr('href');
                var target = $link.attr('target') || '_self';

                // Record Conversion Event
                self.recordConversion(uid, 'link_click');

                if (!href || href === '#' || href === 'javascript:void(0);') {
                    e.preventDefault();
                    return;
                }

                if (target === '_blank') {
                    // Let default link navigation occur in new tab
                    return;
                }

                e.preventDefault();
                window.location.href = href;
            });

            // 4. On-Canvas Close Icons with Persistence Actions
            $popup.off('click.wppoppopCloseIcon', '.wppoppop-close-btn').on('click.wppoppopCloseIcon', '.wppoppop-close-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $btn = $(this);
                var closeAction = $btn.data('close-action') || 'close';
                self.handleCloseAction($popup, uid, closeAction);
            });

            // 5. Real-Time Dynamic Merge Token Interpolation
            $popup.find('.wppoppop-form').on('input change', 'input, select, textarea', function() {
                self.interpolateTokens($popup);
            });

            // 6. Interactive Star Rating Field Sync
            $popup.off('click.wppoppopRating', '.wppoppop-rating-star').on('click.wppoppopRating', '.wppoppop-rating-star', function(e) {
                e.stopPropagation();
                var $star = $(this);
                var ratingVal = parseInt($star.data('val'), 10) || 5;
                var $wrap = $star.closest('.wppoppop-field-rating');
                $wrap.find('input[type="hidden"]').val(ratingVal);
                $wrap.find('.wppoppop-rating-star').each(function() {
                    var v = parseInt($(this).data('val'), 10);
                    $(this).css('color', v <= ratingVal ? '#f59e0b' : '#cbd5e1');
                });
            });

            // 7. Signature Clear Button
            $popup.off('click.wppoppopSigClear', '.wppoppop-sig-clear').on('click.wppoppopSigClear', '.wppoppop-sig-clear', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $wrap = $(this).closest('.wppoppop-sig-wrap');
                var canvas = $wrap.find('.wppoppop-sig-canvas')[0];
                if (canvas) {
                    var ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    $wrap.find('input[type="hidden"]').val('');
                }
            });
        },

        handleSubmit: function($popup, $form, $submitBtn) {
            var self = this;
            var uid = $popup.data('uid');

            // Clear existing validation error tooltips
            self.clearErrorBubbles($form);

            // Validate all form fields (Email, Text Fields, Numbers, Checkboxes)
            var validation = self.validateForm($form);
            if (!validation.valid) {
                self.showErrorBubble(validation.$field, validation.message);
                return;
            }

            // Sync Digital Signature Canvas to hidden input if present
            $form.find('.wppoppop-sig-wrap').each(function() {
                var $wrap = $(this);
                var canvas = $wrap.find('.wppoppop-sig-canvas')[0];
                if (canvas) {
                    var dataUrl = canvas.toDataURL('image/png');
                    $wrap.find('input[type="hidden"]').val(dataUrl);
                }
            });

            // Submit Button loading state
            var originalBtnHtml = $submitBtn.html();
            $submitBtn.prop('disabled', true).css('opacity', '0.7');

            var formData = $form.serializeArray();
            var payload = {
                action: 'wppoppop_submit_form',
                uid: uid,
                nonce: (window.wppoppop_front_vars && window.wppoppop_front_vars.nonce) ? window.wppoppop_front_vars.nonce : '',
                fields: {}
            };

            formData.forEach(function(item) {
                payload.fields[item.name] = item.value;
            });

            var ajaxUrl = (window.wppoppop_front_vars && window.wppoppop_front_vars.ajax_url)
                ? window.wppoppop_front_vars.ajax_url
                : '/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function(resp) {
                    $submitBtn.prop('disabled', false).css('opacity', '1').html(originalBtnHtml);

                    // Track analytics & sound effects
                    self.recordConversion(uid, 'form_submit');
                    if (window.WpPopPopFront && window.WpPopPopFront.playChime) {
                        window.WpPopPopFront.playChime('win');
                    }
                    if (window.WpPopPopFront && window.WpPopPopFront.launchConfetti) {
                        window.WpPopPopFront.launchConfetti($popup);
                    }

                    // Route action based on Submit Button configuration
                    var submitAction = $submitBtn.data('action') || 'default';
                    self.handlePostSubmitAction($popup, uid, submitAction, resp);
                },
                error: function() {
                    $submitBtn.prop('disabled', false).css('opacity', '1').html(originalBtnHtml);
                    alert('Submission could not be completed. Please try again.');
                }
            });
        },

        validateForm: function($form) {
            var result = { valid: true, $field: null, message: '' };

            // 1. Validate Single-Line Text Fields
            $form.find('input[type="text"]').each(function() {
                var $input = $(this);
                if ($input.prop('required') && !$.trim($input.val())) {
                    result = { valid: false, $field: $input, message: 'Please fill out this required field.' };
                    return false;
                }
            });
            if (!result.valid) return result;

            // 2. Validate Email Fields
            $form.find('input[type="email"]').each(function() {
                var $input = $(this);
                var val = $.trim($input.val());
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if ($input.prop('required') && !val) {
                    result = { valid: false, $field: $input, message: 'Please enter your email address.' };
                    return false;
                }
                if (val && !emailRegex.test(val)) {
                    result = { valid: false, $field: $input, message: 'Please enter a valid email address.' };
                    return false;
                }
            });
            if (!result.valid) return result;

            // 3. Validate Number Fields
            $form.find('input[type="number"]').each(function() {
                var $input = $(this);
                var val = $input.val();
                if ($input.prop('required') && val === '') {
                    result = { valid: false, $field: $input, message: 'Please enter a number.' };
                    return false;
                }
            });
            if (!result.valid) return result;

            // 4. Validate Checkboxes (e.g. Terms Agreement)
            $form.find('input[type="checkbox"]').each(function() {
                var $chk = $(this);
                if ($chk.prop('required') && !$chk.is(':checked')) {
                    result = { valid: false, $field: $chk, message: 'You must agree to continue.' };
                    return false;
                }
            });

            return result;
        },

        handlePostSubmitAction: function($popup, uid, action, response) {
            var self = this;

            // Persist submitted cookie to prevent re-triggering
            self.setCookie('wppoppop_submitted_' + uid, '1', 30);

            switch (action) {
                case 'next_canvas':
                    // Advance to next canvas sequence container
                    var $activeContainer = $popup.find('.wppoppop-canvas-container:visible, .wppoppop-screen-container:visible');
                    var curIdx = parseInt($activeContainer.data('canvas-index') || $activeContainer.data('screen-index'), 10) || 1;
                    var nextIdx = curIdx + 1;
                    var $nextContainer = $popup.find('.wppoppop-canvas-container[data-canvas-index="' + nextIdx + '"], .wppoppop-screen-container[data-screen-index="' + nextIdx + '"]');

                    if ($nextContainer.length && window.WpPopPopFront && window.WpPopPopFront.switchCanvas) {
                        window.WpPopPopFront.switchCanvas($popup, nextIdx);
                    } else {
                        self.showSuccessAndClose($popup, uid);
                    }
                    break;

                case 'redirect':
                    var redirectUrl = (response && response.redirect_url) ? response.redirect_url : ($popup.data('redirect-url') || '');
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    } else {
                        self.showSuccessAndClose($popup, uid);
                    }
                    break;

                case 'default':
                default:
                    self.showSuccessAndClose($popup, uid);
                    break;
            }
        },

        showSuccessAndClose: function($popup, uid) {
            var self = this;
            var $activeBox = $popup.find('.wppoppop-canvas-container:visible, .wppoppop-screen-container:visible');

            // Render status overlay if available
            var $overlay = $popup.find('.wppoppop-stage-status-overlay');
            if ($overlay.length) {
                $overlay.addClass('active');
            } else {
                var successMsg = $('<div class="wppoppop-submit-success-banner" style="position:absolute;inset:0;background:rgba(255,255,255,0.97);display:flex;align-items:center;justify-content:center;flex-direction:column;text-align:center;padding:20px;z-index:999999;border-radius:inherit;"><h3 style="margin:0 0 8px 0;color:#0f172a;font-size:20px;font-weight:700;">Thank You!</h3><p style="margin:0;color:#64748b;font-size:14px;">Your submission has been received successfully.</p></div>');
                $activeBox.append(successMsg);
            }

            setTimeout(function() {
                self.closePopup($popup);
            }, 2500);
        },

        handleCloseAction: function($popup, uid, action) {
            var self = this;
            var freqDays = parseInt($popup.data('freq-days'), 10) || 7;

            // Stop any playing video media to prevent audio leaks
            self.stopMediaPlayback($popup);

            if (action === 'close_forever') {
                self.setCookie('wppoppop_closed_' + uid, '1', 365);
            } else if (action === 'close_period') {
                self.setCookie('wppoppop_closed_' + uid, '1', freqDays);
                try { sessionStorage.setItem('wppoppop_session_closed_' + uid, '1'); } catch(e) {}
            }

            self.closePopup($popup);
        },

        closePopup: function($popup) {
            var self = this;
            self.stopMediaPlayback($popup);

            var $container = $popup.find('.wppoppop-canvas-container:visible, .wppoppop-screen-container:visible');
            var exitAnim = $container.data('anim-disappearance') || 'fadeOut';

            if (exitAnim && exitAnim !== 'none') {
                var animClass = (exitAnim.indexOf('animate__') === 0) ? exitAnim : ('animate__' + exitAnim);
                $container.removeClass(function(i, c) { return (c.match(/(^|\s)animate__\S+/g) || []).join(' '); });
                $container.addClass('animate__animated ' + animClass);

                setTimeout(function() {
                    $popup.fadeOut(200);
                }, 350);
            } else {
                $popup.fadeOut(200);
            }
        },

        stopMediaPlayback: function($popup) {
            // 1. Pause HTML5 Video tags
            $popup.find('video').each(function() {
                try {
                    this.pause();
                    this.currentTime = 0;
                } catch(e) {}
            });

            // 2. Pause YouTube / Vimeo Iframes by detaching and reapplying src
            $popup.find('iframe').each(function() {
                try {
                    var src = $(this).attr('src');
                    if (src && (src.indexOf('youtube.com') > -1 || src.indexOf('vimeo.com') > -1)) {
                        $(this).attr('src', src);
                    }
                } catch(e) {}
            });
        },

        interpolateTokens: function($popup) {
            var tokens = {};
            $popup.find('.wppoppop-form').find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    tokens[name] = $(this).val();
                }
            });

            $popup.find('.wppoppop-front-element').each(function() {
                var $elem = $(this);
                var originalHtml = $elem.data('orig-html');
                if (!originalHtml) {
                    originalHtml = $elem.html();
                    $elem.data('orig-html', originalHtml);
                }

                var replaced = originalHtml;
                for (var key in tokens) {
                    if (tokens.hasOwnProperty(key)) {
                        var val = tokens[key] || '';
                        var re = new RegExp('\\{' + key + '\\}', 'gi');
                        replaced = replaced.replace(re, val);
                    }
                }
                $elem.html(replaced);
            });
        },

        showErrorBubble: function($field, message) {
            var $bubble = $('<div class="wppoppop-validation-bubble" style="position:absolute;background:#ef4444;color:#ffffff;font-size:11px;font-weight:700;padding:4px 8px;border-radius:4px;z-index:999999;box-shadow:0 4px 6px rgba(0,0,0,0.15);pointer-events:none;white-space:nowrap;">' + message + '<div style="position:absolute;bottom:-4px;left:12px;width:0;height:0;border-left:4px solid transparent;border-right:4px solid transparent;border-top:4px solid #ef4444;"></div></div>');
            
            var pos = $field.position();
            $bubble.css({
                top: (pos.top - 28) + 'px',
                left: pos.left + 'px'
            });

            $field.parent().css('position', 'relative').append($bubble);
            $field.css('borderColor', '#ef4444').focus();

            setTimeout(function() {
                $bubble.fadeOut(300, function() { $(this).remove(); });
                $field.css('borderColor', '');
            }, 3000);
        },

        clearErrorBubbles: function($form) {
            $form.find('.wppoppop-validation-bubble').remove();
            $form.find('input, select, textarea').css('borderColor', '');
        },

        recordConversion: function(uid, type) {
            var ajaxUrl = (window.wppoppop_front_vars && window.wppoppop_front_vars.ajax_url)
                ? window.wppoppop_front_vars.ajax_url
                : '/wp-admin/admin-ajax.php';

            $.post(ajaxUrl, {
                action: 'wppoppop_record_impression',
                uid: uid,
                is_conversion: 1,
                event_type: type || 'conversion'
            });
        },

        setCookie: function(name, value, days) {
            var expires = '';
            if (days) {
                var d = new Date();
                d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + d.toUTCString();
            }
            document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
        }
    };

    // Auto-initialize when popup becomes visible
    $(document).on('wppoppop:opened', function(e, $popup) {
        if ($popup && window.WpPopPopFrontForm) {
            window.WpPopPopFrontForm.init($popup);
        }
    });
})(jQuery);
