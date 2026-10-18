/**
 * WpPopPop Frontend Lead Capture & Form Submission Controller
 * Normalizes .wppoppop-submit-trigger & .wppoppop-submit-btn
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

            $(document).on('click', '.wppoppop-submit-btn, .wppoppop-submit-trigger', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $canvas = $btn.closest('.wppoppop-canvas-container, .wppoppop-canvas-stage, .wppoppop-box');
                var $popup  = $btn.closest('.wppoppop-popup-wrap, .wppoppop-overlay');
                var uid     = $popup.data('uid');

                self.processSubmission($canvas, $btn, uid, $popup);
            });
        },

        processSubmission: function($canvas, $btn, uid, $popup) {
            var self = this;
            var fields = {};
            var hasEmail = false;
            var emailVal = '';

            $canvas.find('input, select, textarea').each(function() {
                var $input = $(this);
                var name = $input.attr('name') || $input.data('field-name') || $input.attr('type');
                if (!name || $input.attr('type') === 'submit') return;

                var val = $input.val();
                fields[name] = val;

                if ($input.attr('type') === 'email' || name.indexOf('email') !== -1) {
                    hasEmail = true;
                    emailVal = val;
                }
            });

            if (hasEmail && emailVal) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailVal)) {
                    self.showError($canvas, 'Please enter a valid email address.');
                    return;
                }
            }

            var origText = $btn.text();
            $btn.prop('disabled', true).text('Submitting...');

            if (vars.enable_recaptcha && vars.recaptcha_site_key && typeof window.grecaptcha !== 'undefined') {
                window.grecaptcha.ready(function() {
                    window.grecaptcha.execute(vars.recaptcha_site_key, { action: 'wppoppop_submit' })
                        .then(function(token) {
                            self.dispatchAjax(fields, token, $canvas, $btn, origText, uid, $popup);
                        })
                        .catch(function() {
                            self.dispatchAjax(fields, '', $canvas, $btn, origText, uid, $popup);
                        });
                });
            } else {
                self.dispatchAjax(fields, '', $canvas, $btn, origText, uid, $popup);
            }
        },

        dispatchAjax: function(fields, recaptchaToken, $canvas, $btn, origText, uid, $popup) {
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
                        if (window.WpPopPopFrontDisplay && typeof window.WpPopPopFrontDisplay.triggerCelebration === 'function') {
                            window.WpPopPopFrontDisplay.triggerCelebration();
                        }
                        $(document).trigger('wppoppop:form_submitted', [res.data]);

                        if (res.data && res.data.redirect_url) {
                            window.location.href = res.data.redirect_url;
                        } else {
                            var successMsg = (res.data && res.data.message) ? res.data.message : 'Thank you for your submission!';
                            self.showSuccess($canvas, successMsg, $popup);
                            setTimeout(function() {
                                if (window.WpPopPopFrontDisplay) {
                                    window.WpPopPopFrontDisplay.close(uid);
                                }
                            }, 2200);
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
                $canvas.append($msgBox);
            }
            $msgBox.css('color', '#dc2626').text(msg).show();
        },

        showSuccess: function($canvas, msg, $popup) {
            var $statusOverlay = $popup.find('.wppoppop-status-overlay');
            if ($statusOverlay.length) {
                $statusOverlay.find('.wppoppop-status-message').text(msg);
                $statusOverlay.css('display', 'flex').fadeIn(200);
            } else {
                var $msgBox = $canvas.find('.wppoppop-status-bubble');
                if (!$msgBox.length) {
                    $msgBox = $('<div class="wppoppop-status-bubble" style="color:#16a34a;font-size:12px;font-weight:600;margin-top:8px;text-align:center;"></div>');
                    $canvas.append($msgBox);
                }
                $msgBox.css('color', '#16a34a').text(msg).show();
            }
        }
    };

    $(document).ready(function() {
        window.WpPopPopFrontForm.init();
    });

})(window, jQuery);

/* === WPPOPPOP_PHASE5_FRONT_RUNTIME_START === */
(function(window, $) {
    'use strict';

    function ingestDynamicUrlParameters() {
        if (typeof URLSearchParams === 'undefined') return;
        var params = new URLSearchParams(window.location.search);
        $('[data-dynamic-default="true"][data-param-name]').each(function() {
            var $field =$(this);
            var paramKey = $field.attr('data-param-name');
            if (paramKey && params.has(paramKey)) {
                var val = params.get(paramKey);
                $field.val(val).trigger('change');
            }
        });
    }

    function applyFieldFilters(val, filterCsv) {
        if (!filterCsv || typeof val !== 'string') return val;
        var filters = filterCsv.split(',');
        filters.forEach(function(f) {
            f = $.trim(f);
            if (f === 'trim') val = $.trim(val);
            else if (f === 'strip_tags') val = val.replace(/<[^>]*>?/gm, '');
            else if (f === 'lowercase') val = val.toLowerCase();
            else if (f === 'digits') val = val.replace(/[^0-9]/g, '');
            else if (f === 'alpha') val = val.replace(/[^a-zA-Z\s]/g, '');
            else if (f === 'alphanumeric') val = val.replace(/[^a-zA-Z0-9\s]/g, '');
        });
        return val;
    }

    function bindDecomposed26Runtime() {
        ingestDynamicUrlParameters();

        // 1. Tile & Image Select Interactive Choice Cards (Elements 19 & 20)
        $(document).off('click.wppoppopChoiceCard').on('click.wppoppopChoiceCard', '.wppoppop-choice-card', function(e) {
            e.preventDefault();
            var $card =$(this);
            var $grid =$card.closest('.wppoppop-interactive-choices');
            var isMulti = String($grid.attr('data-multi')) === 'true';
            var actColor = $grid.attr('data-active-color') || '#0284c7';

            if (!isMulti) {
                $grid.find('.wppoppop-choice-card').removeClass('is-selected').css({
                    borderColor: '#cbd5e1',
                    background: '#f1f5f9',
                    color: '#334155'
                });
                $card.addClass('is-selected');
            } else {
                $card.toggleClass('is-selected');
            }

            $grid.find('.wppoppop-choice-card.is-selected').each(function() {
                if ($(this).hasClass('wppoppop-tile-option')) {$(this).css({ background: actColor, color: '#ffffff', borderColor: actColor });
                } else {
                    $(this).css({ borderColor: actColor });
                }
            });

            var selectedVals = [];
            $grid.find('.wppoppop-choice-card.is-selected').each(function() {
                selectedVals.push($(this).attr('data-value'));
            });
            $grid.find('input[type="hidden"]').val(selectedVals.join(', ')).trigger('change');
        });

        // 2. Interactive Star / Heart / Thumb Rating (Element 25)
        $(document).off('click.wppoppopRatingGlyph').on('click.wppoppopRatingGlyph', '.wppoppop-rating-glyph', function(e) {
            e.preventDefault();
            var $glyph =$(this);
            var score = parseInt($glyph.attr('data-score'), 10) || 5;
            var $wrap =$glyph.closest('.wppoppop-interactive-rating');
            var actCol = $wrap.attr('data-active-color') || '#f59e0b';
            $wrap.find('.wppoppop-rating-glyph').each(function() {
                var s = parseInt($(this).attr('data-score'), 10) || 1;
                $(this).css('color', s <= score ? actCol : '#cbd5e1');
            });
            $wrap.find('input[type="hidden"]').val(score).trigger('change');
        });

        // 3. Password Eye Reveal Toggle (Element 24)
        $(document).off('click.wppoppopPwEye').on('click.wppoppopPwEye', '.wppoppop-pw-eye-toggle', function(e) {
            e.preventDefault();
            var $inp =$(this).siblings('input.wppoppop-input-password');
            if (!$inp.length) return;
            var curType = $inp.attr('type');$inp.attr('type', curType === 'password' ? 'text' : 'password');
        });

        // 4. File Upload Dropzone Filename Display (Element 23)
        $(document).off('change.wppoppopFileDrop').on('change.wppoppopFileDrop', '.wppoppop-upload-dropzone input[type="file"]', function() {
            var files = this.files;
            if (files && files.length > 0) {
                var names = [];
                for (var i = 0; i < files.length; i++) names.push(files[i].name);
                $(this).siblings('.wppoppop-upload-label-text').text(names.join(', '));
            }
        });

        // 5. Apply Advanced Tab Sanitization Filters on blur/change
        $(document).off('blur.wppoppopAdvFilters').on('blur.wppoppopAdvFilters', '[data-filters]', function() {
            var $inp =$(this);
            var filterCsv = $inp.attr('data-filters');
            if (filterCsv && $inp.val()) {
                $inp.val(applyFieldFilters($inp.val(), filterCsv));
            }
        });
    }

    $(function() {
        bindDecomposed26Runtime();
    });
})(window, jQuery);
/* === WPPOPPOP_PHASE5_FRONT_RUNTIME_END === */

/* === WPPOPPOP_PHASE4_RUNTIME_LOGIC_AND_DATA_START === */
(function($) {
    'use strict';

    function applyDynamicUrlParams() {
        if (!window.location || !window.location.search) return;
        var params = new URLSearchParams(window.location.search);
        $('[data-param-name]').each(function() {
            var $el =$(this);
            var pName = $el.attr('data-param-name');
            if (pName && params.has(pName)) {
                var val = params.get(pName);
                if ($el.is('input, textarea, select')) {$el.val(val).trigger('change');
                } else {
                    $el.find('input, textarea, select').first().val(val).trigger('change');
                }
            }
        });
    }

    function evaluateConditionalLogic($scope) {
        var $root = ($scope && $scope.length) ?$scope : $(document);$root.find('[data-logic-enabled="1"], [data-logic-enabled="true"]').each(function() {
            var $target =$(this);
            var action  = $target.attr('data-logic-action') || 'show';
            var match   = $target.attr('data-logic-match') || 'all';
            var rawRules = $target.attr('data-logic-rules') || '[]';
            var rules = [];
            try { rules = JSON.parse(rawRules); } catch (e) { rules = []; }
            if (!Array.isArray(rules) || !rules.length) return;

            var results = rules.map(function(r) {
                var fieldKey = r.field || '';
                var op       = r.operator || 'is';
                var expected = String(r.value || '').toLowerCase();
                var $inp =$root.find('[name="' + fieldKey + '"], [data-field-token="' + fieldKey + '"], #' + fieldKey).first();
                var actual = String($inp.val() || '').toLowerCase();

                if (op === 'is') return actual === expected;
                if (op === 'is_not') return actual !== expected;
                if (op === 'contains') return actual.indexOf(expected) !== -1;
                if (op === 'not_empty') return actual.trim() !== '';
                return true;
            });

            var passed = (match === 'any')
                ? results.some(function(b) { return b; })
                : results.every(function(b) { return b; });

            var shouldShow = (action === 'show') ? passed : !passed;
            $target.toggle(shouldShow);
        });
    }

    $(function() {
        applyDynamicUrlParams();
        evaluateConditionalLogic($(document));$(document).on('input change', '.wppoppop-modal input, .wppoppop-modal select, .wppoppop-modal textarea', function() {
            evaluateConditionalLogic($(this).closest('.wppoppop-modal'));
        });
    });
})(jQuery);
/* === WPPOPPOP_PHASE4_RUNTIME_LOGIC_AND_DATA_END === */

/* === WPPOPPOP_PHASE4_INTERACTIVE_WIDGET_RUNTIME_START === */
(function($) {
    'use strict';

    // 1. Password Visibility Eye Toggle on Frontend
    $(document).on('click', '.wppoppop-pw-eye-toggle', function(e) {
        e.preventDefault();
        var $inp =$(this).siblings('input');
        if ($inp.length) {
            var cur = $inp.attr('type') === 'password' ? 'text' : 'password';$inp.attr('type', cur);
        }
    });

    // 2. Interactive Tile & Image Select Choice Clicks on Frontend
    $(document).on('click', '.wppoppop-tile-option, .wppoppop-imageselect-option', function() {
        var $opt =$(this);
        var $wrap =$opt.closest('[data-field-token], .wppoppop-form-field-wrap');
        var isMulti = $wrap.attr('data-multi') === '1';
        if (!isMulti) {
            $opt.siblings().removeClass('is-selected active');
        }
        $opt.toggleClass('is-selected active');
        var vals = [];
        $wrap.find('.is-selected, .active').each(function() {
            vals.push($(this).attr('data-value') ||$(this).text().trim());
        });
        $wrap.find('input[type="hidden"]').val(vals.join(', ')).trigger('change');
    });

    // 3. Interactive Star Rating Selection on Frontend
    $(document).on('click', '.wppoppop-rating-glyph', function() {
        var $g =$(this);
        var score = parseInt($g.attr('data-score'), 10) || 5;
        var $wrap =$g.closest('.wppoppop-rating-wrap');
        var activeCol = $wrap.attr('data-active-color') || '#f59e0b';
        $wrap.find('.wppoppop-rating-glyph').each(function() {
            var s = parseInt($(this).attr('data-score'), 10) || 1;
            $(this).css('color', s <= score ? activeCol : '#cbd5e1');
        });
        $wrap.find('input[type="hidden"]').val(score).trigger('change');
    });
})(jQuery);
/* === WPPOPPOP_PHASE4_INTERACTIVE_WIDGET_RUNTIME_END === */

/* === WPPOPPOP_ADV_RUNTIME_START === */
(function($) {
    'use strict';
    if (window.__wppAdvRuntime) return;
    window.__wppAdvRuntime = true;

    function slot(c) { return c === '9' || c === 'a' || c === '*'; }
    function slotOk(m, ch) {
        if (m === '9') return /[0-9]/.test(ch);
        if (m === 'a') return /[A-Za-z]/.test(ch);
        return /[A-Za-z0-9]/.test(ch);
    }
    function applyMask(val, mask) {
        var out = '', vi = 0;
        for (var mi = 0; mi < mask.length && vi < val.length; mi++) {
            var m = mask.charAt(mi);
            if (slot(m)) {
                var found = false;
                while (vi < val.length) {
                    var ch = val.charAt(vi++);
                    if (slotOk(m, ch)) { out += ch; found = true; break; }
                }
                if (!found) break;
            } else {
                out += m;
                if (val.charAt(vi) === m) vi++;
            }
        }
        return out;
    }
    function applyFilters(val, csv, live) {
        var f = String(csv || '').split(','), v = String(val);
        if (f.indexOf('striptags') !== -1) v = v.replace(/<[^>]*>?/g, '');
        if (f.indexOf('alpha') !== -1) v = v.replace(/[^A-Za-z\s]/g, '');
        if (f.indexOf('digits') !== -1) v = v.replace(/\D/g, '');
        if (f.indexOf('lowercase') !== -1) v = v.toLowerCase();
        if (f.indexOf('uppercase') !== -1) v = v.toUpperCase();
        if (!live && f.indexOf('trim') !== -1) v = $.trim(v);
        return v;
    }

    document.addEventListener('focusin', function(e) {
        var t = e.target;
        if (!t || !t.getAttribute) return;
        var mask = t.getAttribute('data-input-mask');
        if (mask && (!t.maxLength || t.maxLength < 1 || t.maxLength > mask.length)) { t.maxLength = mask.length; }
    }, true);

    document.addEventListener('input', function(e) {
        var t = e.target;
        if (!t || !t.getAttribute || typeof t.value !== 'string') return;
        var mask = t.getAttribute('data-input-mask'), fil = t.getAttribute('data-filters');
        if (!mask && !fil) return;
        var v = t.value, n = v;
        if (fil) n = applyFilters(n, fil, true);
        if (mask) n = applyMask(n, mask);
        if (n !== v) t.value = n;
    }, true);

    document.addEventListener('focusout', function(e) {
        var t = e.target;
        if (!t || !t.getAttribute || typeof t.value !== 'string') return;
        var fil = t.getAttribute('data-filters');
        if (!fil) return;
        var n = applyFilters(t.value, fil, false);
        if (n !== t.value) t.value = n;
    }, true);

    function bubble($f, msg) {
        $('.wppoppop-error-bubble').remove();
        var $b = $('<div class="wppoppop-error-bubble"></div>').text(msg);
        $('body').append($b);
        var off = $f.offset() || { top: 0, left: 0 };
        $b.css({ position: 'absolute', top: (off.top - $b.outerHeight() - 8) + 'px', left: off.left + 'px',
            background: '#ef4444', color: '#ffffff', padding: '6px 12px', borderRadius: '4px', fontSize: '12px',
            fontWeight: '600', zIndex: 9999999, boxShadow: '0 4px 12px rgba(239,68,68,0.3)' });
        try { $f.trigger('focus'); } catch (err) {}
        setTimeout(function() { $b.fadeOut(300, function() { $b.remove(); }); }, 3500);
    }

    function validate($p) {
        var bad = null, msg = '';
        var $all = $p.find('input, textarea, select');
        $all.each(function() {
            var t = this, $f = $(t);
            if (t.type === 'hidden' || t.type === 'button' || t.type === 'submit' || !$f.is(':visible')) return;
            var csv = t.getAttribute('data-filters');
            if (csv && typeof t.value === 'string') {
                var nv = applyFilters(t.value, csv, false);
                if (nv !== t.value) t.value = nv;
            }
            var rules = String(t.getAttribute('data-validators') || '').split(',').filter(Boolean);
            var req = t.hasAttribute('required') || rules.indexOf('required') !== -1;
            var v = $f.is(':checkbox') ? ($f.is(':checked') ? '1' : '') : $f.val();
            v = Array.isArray(v) ? v.join(',') : String(v == null ? '' : v);
            var custom = t.getAttribute('data-invalid-msg');
            function fail(m) { bad = $f; msg = m; return false; }
            if (!v) {
                if (req) return fail(t.getAttribute('data-error-msg') || custom || 'This field is required.');
                return;
            }
            if (rules.indexOf('email') !== -1 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
                return fail(custom || 'Please enter a valid email address.');
            }
            if (t.type === 'number') { /* WPPOPPOP_NUMBER_RANGE */
                var nv2 = parseFloat(v), mn = t.getAttribute('min'), mx = t.getAttribute('max');
                if (isNaN(nv2)) return fail(custom || 'Please enter a valid number.');
                if (mn !== null && mn !== '' && nv2 < parseFloat(mn)) return fail(custom || ('Value must be at least ' + mn + '.'));
                if (mx !== null && mx !== '' && nv2 > parseFloat(mx)) return fail(custom || ('Value must be at most ' + mx + '.'));
            }
            if (rules.indexOf('equal') !== -1) {
                var eq = t.getAttribute('data-equal-value');
                if (eq !== null && v !== eq) return fail(custom || 'Value does not match.');
            }
            if (rules.indexOf('inarray') !== -1) {
                var list = String(t.getAttribute('data-in-list') || '').split(',').map(function(x) { return $.trim(x).toLowerCase(); }).filter(Boolean);
                if (list.length && list.indexOf(v.toLowerCase()) === -1) return fail(custom || 'Value is not in the allowed list.');
            }
            if (rules.indexOf('noduplicates') !== -1) {
                var dup = false;
                $all.each(function() {
                    if (this === t || this.type === 'hidden' || this.type === 'checkbox' || this.type === 'radio' || this.type === 'file') return;
                    if (!$(this).is(':visible')) return;
                    if (String($(this).val() || '').toLowerCase() === v.toLowerCase()) { dup = true; return false; }
                });
                if (dup) return fail(custom || 'This value is already used in another field.');
            }
        });
        return bad ? { field: bad, msg: msg } : null;
    }

    document.addEventListener('click', function(e) {
        var btn = (e.target && e.target.closest) ? e.target.closest('.wppoppop-submit-trigger') : null;
        if (!btn) return;
        var popup = btn.closest('.wppoppop-popup-wrap') || document.body;
        var res = validate($(popup));
        if (res) {
            e.preventDefault();
            e.stopImmediatePropagation();
            bubble(res.field, res.msg);
        }
    }, true);
})(jQuery);
/* === WPPOPPOP_ADV_RUNTIME_END === */

/* === WPPOPPOP_TA_COUNT_START === */
(function() {
    'use strict';
    if (window.__wppTaCount) return;
    window.__wppTaCount = true;
    document.addEventListener('input', function(e) {
        var t = e.target;
        if (!t || t.tagName !== 'TEXTAREA' || !t.parentNode) return;
        var c = t.parentNode.querySelector('.wppoppop-ta-count');
        if (!c) return;
        var max = parseInt(c.getAttribute('data-max'), 10) || 0;
        c.textContent = t.value.length + ' / ' + max;
    }, true);
})();
/* === WPPOPPOP_TA_COUNT_END === */
