(function($) {
    'use strict';

    var currentScreen = 1;
    var selectedElement = null;

    function getSecurityNonce() {
        if (typeof wppoppop_vars !== 'undefined' && wppoppop_vars.nonce) {
            return wppoppop_vars.nonce;
        }
        return $('#wppoppop_builder_nonce_field').val() || '';
    }

    function getAjaxUrl() {
        if (typeof wppoppop_vars !== 'undefined' && wppoppop_vars.ajax_url) {
            return wppoppop_vars.ajax_url;
        }
        return (typeof ajaxurl !== 'undefined') ? ajaxurl : '/wp-admin/admin-ajax.php';
    }

    function updateLayersList() {
        var $list = $('#wppoppop-layers-list');
        $list.empty();

        var $screenElements = $('#wppoppop-stage .wppoppop-element').filter(function() {
            return parseInt($(this).attr('data-screen'), 10) === currentScreen;
        });

        if ($screenElements.length === 0) {
            $list.append('<li class="empty-layers">No elements on current screen.</li>');
            return;
        }

        $screenElements.each(function() {
            var $el = $(this);
            var id = $el.attr('id');
            var type = $el.attr('data-type') || 'element';
            var isSel = (selectedElement && selectedElement.attr('id') === id) ? 'active' : '';

            var $li = $('<li class="layer-item ' + isSel + '" data-target="' + id + '">' +
                '<span class="dashicons dashicons-menu layer-handle"></span>' +
                '<span class="layer-label">' + type.toUpperCase() + ' (' + id + ')</span>' +
                '<span class="dashicons dashicons-trash layer-del" title="Delete"></span>' +
                '</li>');

            $list.append($li);
        });
    }

    function selectElement($el) {
        if (selectedElement) {
            selectedElement.removeClass('is-selected');
        }
        selectedElement = $el;
        if (!selectedElement || selectedElement.length === 0) {
            $('#inspector-empty-state').show();
            $('#inspector-controls').hide();
            updateLayersList();
            return;
        }

        selectedElement.addClass('is-selected');
        $('#inspector-empty-state').hide();
        $('#inspector-controls').show();

        // Populate Inspector fields
        $('#prop-field-name').val(selectedElement.attr('data-field-name') || '');
        $('#prop-content').val(selectedElement.attr('data-content') || '');
        $('#prop-goto-screen').val(selectedElement.attr('data-goto-screen') || '1');
        $('#prop-options').val(selectedElement.attr('data-options') || '');
        $('#prop-required').prop('checked', selectedElement.attr('data-required') === 'true');
        $('#prop-error-msg').val(selectedElement.attr('data-error-msg') || 'Please fill out this field.');
        $('#prop-mask').val(selectedElement.attr('data-mask') || '');
        $('#prop-font-family').val(selectedElement.attr('data-font-family') || 'Inherit');
        $('#prop-font-size').val(parseInt(selectedElement.attr('data-font-size'), 10) || 16);
        $('#prop-border-radius').val(parseInt(selectedElement.attr('data-border-radius'), 10) || 4);
        $('#prop-opacity').val(parseFloat(selectedElement.attr('data-opacity')) || 1.0);
        $('#prop-color').val(selectedElement.attr('data-color') || '#222222');
        $('#prop-bg-color').val(selectedElement.attr('data-bg-color') || '#00a32a');

        $('#prop-anim-effect').val(selectedElement.attr('data-anim-effect') || 'none');
        $('#prop-anim-delay').val(parseInt(selectedElement.attr('data-anim-delay'), 10) || 0);
        $('#prop-anim-duration').val(parseInt(selectedElement.attr('data-anim-duration'), 10) || 500);

        var type = selectedElement.attr('data-type');
        $('#group-prop-goto').toggle(type === 'nextstep');
        $('#group-prop-options').toggle(type === 'dropdown' || type === 'radio' || type === 'checkbox' || type === 'wheel');
        $('#group-prop-mask').toggle(type === 'input');

        updateLayersList();
    }

    function makeInteractive($el) {
        var isSnap = $('#chk-grid-snap').is(':checked');
        $el.draggable({
            containment: '#wppoppop-stage',
            grid: isSnap ? [10, 10] : false,
            stop: function() {
                updateLayersList();
            }
        }).resizable({
            containment: '#wppoppop-stage',
            handles: 'n, e, s, w, se',
            stop: function() {
                updateLayersList();
            }
        });

        $el.on('mousedown', function(e) {
            e.stopPropagation();
            selectElement($(this));
        });
    }

    function addElementToStage(type, props) {
        var id = 'elem_' + Date.now() + '_' + Math.floor(Math.random() * 100);
        var screen = currentScreen;
        var p = props || {};

        var top = p.top !== undefined ? p.top : 40;
        var left = p.left !== undefined ? p.left : 40;
        var width = p.width !== undefined ? p.width : 280;
        var height = p.height !== undefined ? p.height : 45;
        var content = p.content !== undefined ? p.content : (type === 'text' ? 'Heading Text' : 'Click Here');

        var $el = $('<div class="wppoppop-element" id="' + id + '"></div>');
        $el.attr({
            'data-type': type,
            'data-screen': p.screen || screen,
            'data-field-name': p.field_name || (type === 'input' ? 'email' : ''),
            'data-content': content,
            'data-goto-screen': p.goto_screen || '2',
            'data-options': p.options || 'Prize 1, Prize 2, Prize 3',
            'data-required': p.required ? 'true' : 'false',
            'data-error-msg': p.error_msg || 'Please fill out this field.',
            'data-mask': p.mask || '',
            'data-font-family': p.font_family || 'Inherit',
            'data-font-size': p.font_size || 16,
            'data-border-radius': p.border_radius || 4,
            'data-opacity': p.opacity || 1.0,
            'data-color': p.color || '#222222',
            'data-bg-color': p.bg_color || (type === 'button' ? '#00a32a' : '#ffffff'),
            'data-anim-effect': p.anim_effect || 'none',
            'data-anim-delay': p.anim_delay || 0,
            'data-anim-duration': p.anim_duration || 500
        });

        $el.css({
            top: top + 'px',
            left: left + 'px',
            width: width + 'px',
            height: height + 'px',
            fontSize: (p.font_size || 16) + 'px',
            borderRadius: (p.border_radius || 4) + 'px',
            opacity: p.opacity || 1.0,
            color: p.color || '#222222',
            backgroundColor: p.bg_color || (type === 'button' ? '#00a32a' : 'transparent')
        });

        // Content placeholder inner rendering
        var innerHtml = '<div class="element-content-box">' + content + '</div>';
        if (type === 'input') {
            innerHtml = '<input type="text" placeholder="' + content + '" disabled style="width:100%;height:100%;">';
        } else if (type === 'button' || type === 'nextstep' || type === 'pay_btn') {
            innerHtml = '<button type="button" style="width:100%;height:100%;background:inherit;color:inherit;border:none;border-radius:inherit;font-weight:700;">' + content + '</button>';
        } else if (type === 'wheel') {
            innerHtml = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#fef08a;font-weight:700;"><span class="dashicons dashicons-chart-pie"></span> Lucky Wheel</div>';
        } else if (type === 'scratch') {
            innerHtml = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#cbd5e1;font-weight:700;"><span class="dashicons dashicons-tickets-alt"></span> Scratch Foil</div>';
        }
        $el.html(innerHtml);

        $('#wppoppop-stage').append($el);
        makeInteractive($el);
        selectElement($el);
    }

    function switchScreen(screenNum) {
        currentScreen = parseInt(screenNum, 10);
        $('.btn-screen-toggle').removeClass('active');
        $('.btn-screen-toggle[data-screen="' + currentScreen + '"]').addClass('active');

        $('#wppoppop-stage .wppoppop-element').each(function() {
            var elScreen = parseInt($(this).attr('data-screen'), 10) || 1;
            if (elScreen === currentScreen) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        selectElement(null);
    }

    // Save Popup Pipeline
    function savePopup() {
        var $btn = $('#wppoppop-btn-save');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin" style="vertical-align:middle;"></span> Saving...');

        var uid = $('#wppoppop-popup-uid').val();
        var title = $('#wppoppop-popup-title').val().trim() || 'New Converting Popup';
        var status = $('#wppoppop-popup-status').val() || 'publish';

        // Gather all elements
        var elements = [];
        $('#wppoppop-stage .wppoppop-element').each(function() {
            var $el = $(this);
            elements.push({
                id: $el.attr('id'),
                type: $el.attr('data-type'),
                screen: parseInt($el.attr('data-screen'), 10) || 1,
                top: Math.round(parseFloat($el.css('top')) || 0),
                left: Math.round(parseFloat($el.css('left')) || 0),
                width: Math.round(parseFloat($el.css('width')) || $el.outerWidth()),
                height: Math.round(parseFloat($el.css('height')) || $el.outerHeight()),
                field_name: $el.attr('data-field-name') || '',
                content: $el.attr('data-content') || '',
                goto_screen: $el.attr('data-goto-screen') || '2',
                options: $el.attr('data-options') || '',
                required: $el.attr('data-required') === 'true',
                error_msg: $el.attr('data-error-msg') || '',
                mask: $el.attr('data-mask') || '',
                font_family: $el.attr('data-font-family') || 'Inherit',
                font_size: parseInt($el.attr('data-font-size'), 10) || 16,
                border_radius: parseInt($el.attr('data-border-radius'), 10) || 4,
                opacity: parseFloat($el.attr('data-opacity')) || 1.0,
                color: $el.attr('data-color') || '#222222',
                bg_color: $el.attr('data-bg-color') || '#ffffff',
                anim_effect: $el.attr('data-anim-effect') || 'none',
                anim_delay: parseInt($el.attr('data-anim-delay'), 10) || 0,
                anim_duration: parseInt($el.attr('data-anim-duration'), 10) || 500
            });
        });

        // Gather Accordion Configurations
        var config = {
            meta: {
                title: title,
                width: parseInt($('#stage-width').val(), 10) || 640,
                height: parseInt($('#stage-height').val(), 10) || 400,
                bg_color: $('#box-bg-color').val() || '#ffffff',
                border_radius: parseInt($('#box-border-radius').val(), 10) || 8,
                backdrop_blur: parseInt($('#style-backdrop-blur').val(), 10) || 5,
                position_mode: $('#style-position-mode').val() || 'modal',
                close_esc: $('#style-close-esc').is(':checked'),
                close_backdrop: $('#style-close-backdrop').is(':checked')
            },
            elements: elements,
            sounds: { enable: $('#snd-enable').is(':checked') },
            triggers: {
                load: $('#trig-load').is(':checked'),
                load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                exit: $('#trig-exit').is(':checked'),
                scroll: $('#trig-scroll').is(':checked'),
                idle: $('#trig-idle').is(':checked'),
                adblock: $('#trig-adblock').is(':checked'),
                mobile_back: $('#trig-mobile-back').is(':checked')
            },
            logic: {
                math_expression: $('#math-expression').val() || '',
                math_output_target: $('#math-output-target').val() || ''
            },
            coupons: {
                enable: $('#cpn-enable').is(':checked'),
                prefix: $('#cpn-prefix').val() || 'POP-',
                type: $('#cpn-type').val() || 'percent',
                amount: parseFloat($('#cpn-amount').val()) || 15,
                auto_apply: $('#cpn-auto-apply').is(':checked'),
                woo_cart_rule: $('#woo-cart-rule').is(':checked'),
                woo_min_cart: parseFloat($('#woo-min-cart').val()) || 50
            },
            sidetabs: {
                enable: $('#tab-enable').is(':checked'),
                text: $('#tab-text').val() || 'Special Offer',
                pos: $('#tab-pos').val() || 'left'
            },
            payments: {
                enable: $('#pay-enable').is(':checked'),
                amount: parseFloat($('#pay-amount').val()) || 10.00,
                currency: $('#pay-currency').val() || 'USD'
            },
            downloads: {
                enable: $('#dl-enable').is(':checked'),
                url: $('#dl-url').val() || ''
            },
            video: { enable: $('#vid-enable').is(':checked') },
            autoresponder: {
                enable_user_email: $('#ar-enable').is(':checked'),
                subject: $('#ar-subject').val() || 'Thank you!',
                message: $('#ar-message').val() || ''
            },
            marketing: {
                webhook_url: $('#mkt-webhook-url').val() || '',
                webhook_secret: $('#mkt-webhook-secret').val() || ''
            },
            twilio: {
                enable: $('#sms-enable').is(':checked'),
                sid: $('#sms-sid').val() || '',
                token: $('#sms-token').val() || '',
                from: $('#sms-from').val() || '',
                to: $('#sms-to').val() || ''
            },
            targeting: {
                auth_mode: $('#target-auth-mode').val() || 'all',
                roles: $('#target-roles').val() || '',
                url_param_key: $('#target-url-param-key').val() || '',
                url_param_val: $('#target-url-param-val').val() || '',
                scope: $('#target-scope').val() || 'everywhere',
                geo_mode: $('#target-geo-mode').val() || 'all'
            },
            cookies: {
                freq_mode: $('#freq-mode').val() || 'everytime',
                hide_submitted: $('#freq-hide-submitted').is(':checked')
            },
            customcode: {
                css: $('#code-custom-css').val() || '',
                js: $('#code-custom-js').val() || ''
            }
        };

        $.ajax({
            url: getAjaxUrl(),
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'wppoppop_save_popup',
                nonce: getSecurityNonce(),
                uid: uid,
                title: title,
                status: status,
                data: JSON.stringify(config)
            },
            success: function(res) {
                $btn.prop('disabled', false).html(originalText);
                if (res && res.success) {
                    var savedUid = res.data && res.data.uid ? res.data.uid : uid;
                    $('#wppoppop-popup-uid').val(savedUid);

                    if (window.history && window.history.replaceState) {
                        var newUrl = window.location.pathname + '?page=wppoppop-builder&uid=' + encodeURIComponent(savedUid);
                        window.history.replaceState(null, '', newUrl);
                    }
                    alert(res.data.message || 'Popup saved successfully!');
                } else {
                    var msg = (res && res.data && res.data.message) ? res.data.message : 'Unknown response error.';
                    alert('Error: ' + msg);
                }
            },
            error: function(xhr, statusText, err) {
                $btn.prop('disabled', false).html(originalText);
                var errDetail = (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message)
                    ? xhr.responseJSON.data.message
                    : (err || statusText || 'HTTP ' + xhr.status);
                alert('Connection failure while saving: ' + errDetail);
            }
        });
    }

    // Hydrate Existing Popup from Database
    function loadPopup(uid) {
        if (!uid) return;
        $.ajax({
            url: getAjaxUrl(),
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'wppoppop_load_popup',
                uid: uid,
                nonce: getSecurityNonce()
            },
            success: function(res) {
                if (!res || !res.success || !res.data) return;
                var item = res.data;
                $('#wppoppop-popup-title').val(item.title || 'New Converting Popup');
                $('#wppoppop-popup-uid').val(item.uid);
                $('#wppoppop-popup-status').val(item.status || 'publish');

                var data = {};
                try {
                    data = (typeof item.data === 'string') ? JSON.parse(item.data) : item.data;
                } catch(e) {
                    data = {};
                }

                // Restore Meta Stage
                if (data.meta) {
                    $('#stage-width').val(data.meta.width || 640).trigger('input');
                    $('#stage-height').val(data.meta.height || 400).trigger('input');
                    $('#box-bg-color').val(data.meta.bg_color || '#ffffff').trigger('input');
                    $('#box-border-radius').val(data.meta.border_radius || 8);
                    $('#style-backdrop-blur').val(data.meta.backdrop_blur || 5);
                    $('#style-position-mode').val(data.meta.position_mode || 'modal');
                    $('#style-close-esc').prop('checked', data.meta.close_esc !== false);
                    $('#style-close-backdrop').prop('checked', data.meta.close_backdrop !== false);
                }

                // Restore Accordion Rules
                if (data.triggers) {
                    $('#trig-load').prop('checked', !!data.triggers.load);
                    $('#trig-load-delay').val(data.triggers.load_delay || 0);
                    $('#trig-exit').prop('checked', !!data.triggers.exit);
                    $('#trig-scroll').prop('checked', !!data.triggers.scroll);
                    $('#trig-idle').prop('checked', !!data.triggers.idle);
                    $('#trig-adblock').prop('checked', !!data.triggers.adblock);
                    $('#trig-mobile-back').prop('checked', !!data.triggers.mobile_back);
                }
                if (data.logic) {
                    $('#math-expression').val(data.logic.math_expression || '');
                    $('#math-output-target').val(data.logic.math_output_target || '');
                }
                if (data.coupons) {
                    $('#cpn-enable').prop('checked', !!data.coupons.enable);
                    $('#cpn-prefix').val(data.coupons.prefix || 'POP-');
                    $('#cpn-type').val(data.coupons.type || 'percent');
                    $('#cpn-amount').val(data.coupons.amount || 15);
                    $('#cpn-auto-apply').prop('checked', data.coupons.auto_apply !== false);
                    $('#woo-cart-rule').prop('checked', !!data.coupons.woo_cart_rule);
                    $('#woo-min-cart').val(data.coupons.woo_min_cart || 50);
                }
                if (data.sidetabs) {
                    $('#tab-enable').prop('checked', !!data.sidetabs.enable);
                    $('#tab-text').val(data.sidetabs.text || 'Special Offer');
                    $('#tab-pos').val(data.sidetabs.pos || 'left');
                }
                if (data.payments) {
                    $('#pay-enable').prop('checked', !!data.payments.enable);
                    $('#pay-amount').val(data.payments.amount || 10.00);
                    $('#pay-currency').val(data.payments.currency || 'USD');
                }
                if (data.downloads) {
                    $('#dl-enable').prop('checked', !!data.downloads.enable);
                    $('#dl-url').val(data.downloads.url || '');
                }
                if (data.video) {
                    $('#vid-enable').prop('checked', !!data.video.enable);
                }
                if (data.autoresponder) {
                    $('#ar-enable').prop('checked', !!data.autoresponder.enable_user_email);
                    $('#ar-subject').val(data.autoresponder.subject || 'Thank you!');
                    $('#ar-message').val(data.autoresponder.message || '');
                }
                if (data.marketing) {
                    $('#mkt-webhook-url').val(data.marketing.webhook_url || '');
                    $('#mkt-webhook-secret').val(data.marketing.webhook_secret || '');
                }
                if (data.twilio) {
                    $('#sms-enable').prop('checked', !!data.twilio.enable);
                    $('#sms-sid').val(data.twilio.sid || '');
                    $('#sms-token').val(data.twilio.token || '');
                    $('#sms-from').val(data.twilio.from || '');
                    $('#sms-to').val(data.twilio.to || '');
                }
                if (data.targeting) {
                    $('#target-auth-mode').val(data.targeting.auth_mode || 'all');
                    $('#target-roles').val(data.targeting.roles || '');
                    $('#target-url-param-key').val(data.targeting.url_param_key || '');
                    $('#target-url-param-val').val(data.targeting.url_param_val || '');
                    $('#target-scope').val(data.targeting.scope || 'everywhere');
                    $('#target-geo-mode').val(data.targeting.geo_mode || 'all');
                }
                if (data.cookies) {
                    $('#freq-mode').val(data.cookies.freq_mode || 'everytime');
                    $('#freq-hide-submitted').prop('checked', data.cookies.hide_submitted !== false);
                }
                if (data.customcode) {
                    $('#code-custom-css').val(data.customcode.css || '');
                    $('#code-custom-js').val(data.customcode.js || '');
                }

                // Render Elements
                $('#wppoppop-stage .wppoppop-element').remove();
                if (Array.isArray(data.elements)) {
                    data.elements.forEach(function(elProps) {
                        addElementToStage(elProps.type, elProps);
                    });
                }
                switchScreen(1);
            }
        });
    }

    // Document Ready Initializer
    $(document).ready(function() {
        // Tab switching
        $('.tab-btn').on('click', function() {
            var tab = $(this).attr('data-tab');
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.tab-pane').removeClass('active');
            $('#' + tab).addClass('active');
            if (tab === 'tab-layers') updateLayersList();
        });

        // Element palette click
        $('.element-item').on('click', function() {
            var type = $(this).attr('data-type');
            addElementToStage(type);
        });

        // Stage dimensions real-time sync
        $('#stage-width').on('input change', function() {
            $('#wppoppop-stage').css('width', $(this).val() + 'px');
        });
        $('#stage-height').on('input change', function() {
            $('#wppoppop-stage').css('height', $(this).val() + 'px');
        });
        $('#box-bg-color').on('input change', function() {
            $('#wppoppop-stage').css('background-color', $(this).val());
        });

        // Screen switching
        $('.btn-screen-toggle').on('click', function() {
            switchScreen($(this).attr('data-screen'));
        });

        // Accordion collapsing
        $('.accordion-header').on('click', function() {
            $(this).closest('.accordion-item').toggleClass('active');
        });

        // Save Button Handler
        $('#wppoppop-btn-save').on('click', function(e) {
            e.preventDefault();
            savePopup();
        });

        // Live Preview Modal
        $('#wppoppop-btn-preview').on('click', function() {
            var $mount = $('#wppoppop-preview-stage-mount');
            $mount.empty();

            var $clone = $('#wppoppop-stage').clone();
            $clone.find('.ui-resizable-handle, .canvas-grid-guide').remove();
            $clone.find('.wppoppop-element').removeClass('is-selected ui-draggable ui-resizable');
            $clone.find('input, button').prop('disabled', false);

            $mount.append($clone);
            $('#wppoppop-live-preview-modal').fadeIn(150);
        });
        $('#btn-close-live-preview').on('click', function() {
            $('#wppoppop-live-preview-modal').fadeOut(150);
        });

        // Embed Code Modal
        $('#wppoppop-btn-embed').on('click', function() {
            var uid = $('#wppoppop-popup-uid').val() || 'pop_sample';
            $('#embed-code-shortcode').val('[wppoppop uid="' + uid + '"]');
            $('#embed-code-button').val('[wppoppop_button uid="' + uid + '" text="Open Popup"]');
            $('#embed-code-class').val('wppoppop-trigger-open data-popup-uid="' + uid + '"');
            $('#wppoppop-embed-modal').fadeIn(150);
        });
        $('#btn-close-embed-modal').on('click', function() {
            $('#wppoppop-embed-modal').fadeOut(150);
        });

        // Deselect when clicking stage backdrop
        $('#wppoppop-stage').on('mousedown', function(e) {
            if (e.target === this) {
                selectElement(null);
            }
        });

        // Inspector live updates
        $('#prop-content').on('input', function() {
            if (selectedElement) {
                selectedElement.attr('data-content', $(this).val());
                var type = selectedElement.attr('data-type');
                if (type === 'input') {
                    selectedElement.find('input').attr('placeholder', $(this).val());
                } else if (type === 'button' || type === 'nextstep') {
                    selectedElement.find('button').text($(this).val());
                } else {
                    selectedElement.find('.element-content-box').text($(this).val());
                }
            }
        });
        $('#prop-field-name').on('input', function() {
            if (selectedElement) selectedElement.attr('data-field-name', $(this).val());
        });
        $('#prop-font-size').on('input change', function() {
            if (selectedElement) {
                selectedElement.attr('data-font-size', $(this).val());
                selectedElement.css('font-size', $(this).val() + 'px');
            }
        });
        $('#prop-border-radius').on('input change', function() {
            if (selectedElement) {
                selectedElement.attr('data-border-radius', $(this).val());
                selectedElement.css('border-radius', $(this).val() + 'px');
            }
        });
        $('#prop-color').on('input change', function() {
            if (selectedElement) {
                selectedElement.attr('data-color', $(this).val());
                selectedElement.css('color', $(this).val());
            }
        });
        $('#prop-bg-color').on('input change', function() {
            if (selectedElement) {
                selectedElement.attr('data-bg-color', $(this).val());
                selectedElement.css('background-color', $(this).val());
            }
        });

        // Layer removal & actions
        $('#prop-delete-element').on('click', function() {
            if (selectedElement) {
                selectedElement.remove();
                selectElement(null);
            }
        });

        // Hydrate from existing UID
        var initUid = $('#wppoppop-popup-uid').val();
        if (!initUid && typeof wppoppop_vars !== 'undefined' && wppoppop_vars.current_uid) {
            initUid = wppoppop_vars.current_uid;
            $('#wppoppop-popup-uid').val(initUid);
        }
        if (initUid) {
            loadPopup(initUid);
        } else {
            // Default canvas startup items
            addElementToStage('text', { top: 40, left: 40, width: 500, height: 40, content: 'Join Our Newsletter Today' });
            addElementToStage('input', { top: 110, left: 40, width: 500, height: 45, content: 'Enter your email address...' });
            addElementToStage('button', { top: 175, left: 40, width: 500, height: 45, content: 'Subscribe Now' });
        }
    });

})(jQuery);
