
jQuery(document).ready(function($) {
    'use strict';

    let activeElement = null;
    let currentScreen = 1;
    let zIndexCounter = 1;
    const stage = $('#wppoppop-stage');
    const historyStack = [];
    let historyIndex = -1;

    // Accordion Toggle
    $('.wppoppop-sidebar-right').on('click', '.accordion-header', function(e) {
        e.preventDefault();
        const item = $(this).closest('.accordion-item');
        const body = item.find('.accordion-body');

        if (item.hasClass('active')) {
            item.removeClass('active');
            body.slideUp(150);
        } else {
            item.addClass('active');
            body.slideDown(150);
        }
    });

    // History Tracking
    function recordState() {
        const state = [];
        $('.canvas-element').each(function() {
            const el = $(this);
            state.push({
                id: el.attr('id'),
                type: el.data('type'),
                screen: el.data('screen') || 1,
                top: parseInt(el.css('top'), 10),
                left: parseInt(el.css('left'), 10),
                width: el.width(),
                height: el.height(),
                content: el.data('type') === 'input' ? el.find('input').attr('placeholder') : el.text().trim(),
                data: JSON.parse(JSON.stringify(el.data()))
            });
        });

        if (historyIndex < historyStack.length - 1) {
            historyStack.splice(historyIndex + 1);
        }
        historyStack.push(state);
        if (historyStack.length > 25) historyStack.shift();
        historyIndex = historyStack.length - 1;
    }

    function applyState(state) {
        stage.empty();
        state.forEach(function(item) {
            const canvasEl = $('<div class="canvas-element"></div>')
                .attr('id', item.id)
                .data(item.data)
                .css({
                    top: item.top,
                    left: item.left,
                    width: item.width,
                    height: item.height,
                    'z-index': item.data.z_index || 1,
                    display: (item.screen || 1) === currentScreen ? 'block' : 'none'
                })
                .html('<div class="content-render">[' + item.type + '] ' + (item.data.field_name || item.content || '') + '</div>');

            stage.append(canvasEl);
            makeInteractive(canvasEl);
        });
        deselectElement();
        refreshLayers();
    }

    $('#btn-undo').on('click', function() {
        if (historyIndex > 0) {
            historyIndex--;
            applyState(historyStack[historyIndex]);
        }
    });

    $('#btn-redo').on('click', function() {
        if (historyIndex < historyStack.length - 1) {
            historyIndex++;
            applyState(historyStack[historyIndex]);
        }
    });

    // Alignment Toolbar
    $('.btn-align').on('click', function() {
        if (!activeElement) return;
        const alignType = $(this).data('align');
        const stageW = stage.width();
        const stageH = stage.height();
        const elW = activeElement.outerWidth();
        const elH = activeElement.outerHeight();

        if (alignType === 'left') activeElement.css('left', 0);
        if (alignType === 'center-h') activeElement.css('left', Math.round((stageW - elW) / 2));
        if (alignType === 'right') activeElement.css('left', stageW - elW);
        if (alignType === 'top') activeElement.css('top', 0);
        if (alignType === 'center-v') activeElement.css('top', Math.round((stageH - elH) / 2));
        if (alignType === 'bottom') activeElement.css('top', stageH - elH);
        recordState();
    });

    // Viewport Toggle (Desktop / Mobile)
    $('.btn-viewport-toggle').on('click', function() {
        $('.btn-viewport-toggle').removeClass('active');
        $(this).addClass('active');
        const vp = $(this).data('viewport');
        if (vp === 'mobile') {
            stage.css({ width: '360px', height: '520px' });
            $('#stage-width').val(360);
            $('#stage-height').val(520);
        } else {
            stage.css({ width: '640px', height: '400px' });
            $('#stage-width').val(640);
            $('#stage-height').val(400);
        }
    });

    // Panel Tabs Switcher
    $('.panel-tabs .tab-btn').on('click', function() {
        const parent = $(this).closest('.wppoppop-panel');
        parent.find('.tab-btn, .tab-pane').removeClass('active');
        $(this).addClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    // Screen Switcher
    $('.btn-screen-toggle').on('click', function() {
        $('.btn-screen-toggle').removeClass('active');
        $(this).addClass('active');
        currentScreen = parseInt($(this).data('screen'), 10);

        $('.canvas-element').each(function() {
            const elScreen = $(this).data('screen') || 1;
            $(this).toggle(elScreen === currentScreen);
        });
        deselectElement();
        refreshLayers();
    });

    $('#stage-width').on('input', function() {
        stage.width(parseInt($(this).val(), 10) || 640);
    });
    $('#stage-height').on('input', function() {
        stage.height(parseInt($(this).val(), 10) || 400);
    });

    // Palette: Element Insertion Engine (19 Types)
    $('.element-item').on('click', function() {
        const type = $(this).data('type');
        zIndexCounter++;
        const elementId = 'elem_' + Date.now();

        let defaultContent = 'Text block';
        let defaultBg = '#00a32a';
        let width = 200;
        let height = 40;
        let fieldName = '';
        let options = [];
        let innerMarkup = '';

        if (type === 'countdown') {
            width = 240; height = 55;
            innerMarkup = '<div style="background:#0f172a;color:#fff;border-radius:4px;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;">15:00 Evergreen Timer</div>';
        } else if (type === 'progress') {
            width = 300; height = 24;
            innerMarkup = '<div style="background:#2271b1;color:#fff;border-radius:12px;height:100%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;">50% Completed</div>';
        } else if (type === 'wheel') {
            fieldName = 'prize'; width = 220; height = 220;
            options = ['10% OFF', 'FREE SHIP', '20% OFF', '5% OFF'];
            innerMarkup = '<div style="background:#f6f7f7;border:2px dashed #999;border-radius:50%;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;">Fortune Wheel</div>';
        } else if (type === 'scratch') {
            fieldName = 'scratch_prize'; width = 220; height = 120;
            innerMarkup = '<div style="background:#94a3b8;color:#fff;border-radius:6px;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;">Scratch Card Surface</div>';
        } else if (type === 'slider') {
            fieldName = 'budget'; width = 220; height = 45;
            innerMarkup = '<div style="font-size:12px;font-weight:600;">Slider: $50 <input type="range" disabled style="width:100%;"></div>';
        } else if (type === 'signature') {
            fieldName = 'signature'; width = 240; height = 100;
            innerMarkup = '<div style="background:#f8fafc;border:1px dashed #94a3b8;width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:12px;">Sign Here</div>';
        } else if (type === 'file') {
            fieldName = 'attachment'; width = 220; height = 40;
            innerMarkup = '<input type="file" disabled style="width:100%;height:100%;font-size:12px;">';
        } else if (type === 'input') {
            fieldName = 'email'; width = 220;
            innerMarkup = '<input type="email" placeholder="Enter email..." disabled style="width:100%;height:100%;">';
        } else if (type === 'dropdown') {
            fieldName = 'choice'; width = 220;
            options = ['Option 1', 'Option 2', 'Option 3'];
            innerMarkup = '<select disabled style="width:100%;height:100%;"><option>Option 1</option><option>Option 2</option></select>';
        } else if (type === 'radio') {
            fieldName = 'radio_choice'; width = 240;
            options = ['Option 1', 'Option 2'];
            innerMarkup = '<div style="display:flex;gap:10px;font-size:12px;"><label><input type="radio" checked disabled> Opt 1</label></div>';
        } else if (type === 'checkbox') {
            fieldName = 'terms'; width = 240;
            options = ['Agree to terms'];
            innerMarkup = '<div style="display:flex;gap:10px;font-size:12px;"><label><input type="checkbox" checked disabled> Agree</label></div>';
        } else if (type === 'rating') {
            fieldName = 'rating'; width = 160; height = 35;
            innerMarkup = '<div style="color:#f0ad4e;font-size:22px;letter-spacing:4px;">&#9733;&#9733;&#9733;&#9733;&#9733;</div>';
        } else if (type === 'date') {
            fieldName = 'date'; width = 180;
            innerMarkup = '<input type="date" disabled style="width:100%;height:100%;">';
        } else if (type === 'number') {
            fieldName = 'qty'; width = 120;
            innerMarkup = '<input type="number" value="1" disabled style="width:100%;height:100%;">';
        } else if (type === 'pay_btn') {
            width = 160; defaultContent = 'Pay Now'; defaultBg = '#0284c7';
            innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;font-weight:600;">' + defaultContent + '</button>';
        } else if (type === 'nextstep') {
            width = 160; defaultContent = 'Next Step &rarr;'; defaultBg = '#2271b1';
            innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;font-weight:600;">' + defaultContent + '</button>';
        } else if (type === 'button') {
            width = 160;
            innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;font-weight:600;">Submit</button>';
        } else if (type === 'html') {
            width = 240; height = 60;
            innerMarkup = '<div style="border:1px dashed #aaa;padding:6px;font-family:monospace;font-size:11px;">Custom HTML</div>';
        } else {
            innerMarkup = '<div class="content-render">' + defaultContent + '</div>';
        }

        const elem = $('<div class="canvas-element"></div>')
            .attr('id', elementId)
            .data('type', type)
            .data('screen', currentScreen)
            .data('field-name', fieldName)
            .data('options', options)
            .data('goto-screen', currentScreen < 3 ? currentScreen + 1 : 1)
            .data('required', 0)
            .data('error-msg', 'Please fill out this field.')
            .data('font-family', 'Inherit')
            .data('font-size', 16)
            .data('border-radius', 4)
            .data('opacity', 1.0)
            .data('color', '#222222')
            .data('bg-color', defaultBg)
            .data('anim-effect', 'none')
            .data('anim-delay', 0)
            .data('anim-duration', 500)
            .data('z-index', zIndexCounter)
            .css({
                top: 40,
                left: 40,
                width: width,
                height: height,
                'z-index': zIndexCounter
            })
            .html(innerMarkup);

        stage.append(elem);
        makeInteractive(elem);
        refreshLayers();
        selectElement(elem);
        recordState();
    });

    function makeInteractive(elem) {
        elem.draggable({
            containment: '#wppoppop-stage',
            grid: $('#chk-grid-snap').is(':checked') ? [10, 10] : false,
            stop: function() { selectElement(elem); recordState(); }
        }).resizable({
            containment: '#wppoppop-stage',
            grid: $('#chk-grid-snap').is(':checked') ? [10, 10] : false,
            stop: function() { selectElement(elem); recordState(); }
        });

        elem.on('click', function(e) {
            e.stopPropagation();
            selectElement(elem);
        });
    }

    function selectElement(elem) {
        $('.canvas-element').removeClass('selected');
        elem.addClass('selected');
        activeElement = elem;

        $('#inspector-empty-state').hide();
        $('#inspector-controls').show();

        const type = elem.data('type');
        $('#prop-field-name').val(elem.data('field-name') || '');
        $('#prop-content').val(elem.text().trim());
        $('#prop-required').prop('checked', elem.data('required') == 1);
        $('#prop-error-msg').val(elem.data('error-msg') || 'Please fill out this field.');
        $('#prop-font-family').val(elem.data('font-family') || 'Inherit');
        $('#prop-font-size').val(elem.data('font-size') || 16);
        $('#prop-border-radius').val(elem.data('border-radius') || 4);
        $('#prop-opacity').val(elem.data('opacity') || 1.0);
        $('#prop-color').val(elem.data('color') || '#222222');
        $('#prop-bg-color').val(elem.data('bg-color') || '#00a32a');

        $('#prop-anim-effect').val(elem.data('anim-effect') || 'none');
        $('#prop-anim-delay').val(elem.data('anim-delay') || 0);
        $('#prop-anim-duration').val(elem.data('anim-duration') || 500);

        $('#group-prop-goto').toggle(type === 'nextstep');
        if (type === 'nextstep') {
            $('#prop-goto-screen').val(elem.data('goto-screen') || 2);
        }

        if (type === 'wheel' || type === 'dropdown' || type === 'radio' || type === 'checkbox') {
            $('#group-prop-options').show();
            const opts = elem.data('options') || [];
            $('#prop-options').val(Array.isArray(opts) ? opts.join(', ') : opts);
        } else {
            $('#group-prop-options').hide();
        }

        highlightLayerItem(elem.attr('id'));
    }

    function deselectElement() {
        $('.canvas-element').removeClass('selected');
        activeElement = null;
        $('#inspector-empty-state').show();
        $('#inspector-controls').hide();
    }

    stage.on('click', deselectElement);

    // Inspector Live Updates
    $('#prop-field-name').on('input', function() { if (activeElement) activeElement.data('field-name', $(this).val()); });
    $('#prop-goto-screen').on('change', function() { if (activeElement) activeElement.data('goto-screen', $(this).val()); });
    $('#prop-options').on('input', function() {
        if (!activeElement) return;
        activeElement.data('options', $(this).val().split(',').map(s => s.trim()).filter(Boolean));
    });
    $('#prop-required').on('change', function() { if (activeElement) activeElement.data('required', $(this).is(':checked') ? 1 : 0); });
    $('#prop-error-msg').on('input', function() { if (activeElement) activeElement.data('error-msg', $(this).val()); });
    $('#prop-font-family').on('change', function() { if (activeElement) activeElement.data('font-family', $(this).val()).css('font-family', $(this).val()); });
    $('#prop-font-size').on('input', function() { if (activeElement) activeElement.data('font-size', $(this).val()).css('font-size', $(this).val() + 'px'); });
    $('#prop-border-radius').on('input', function() { if (activeElement) activeElement.data('border-radius', $(this).val()).css('border-radius', $(this).val() + 'px'); });
    $('#prop-opacity').on('input', function() { if (activeElement) activeElement.data('opacity', $(this).val()).css('opacity', $(this).val()); });
    $('#prop-color').on('input', function() { if (activeElement) activeElement.data('color', $(this).val()).css('color', $(this).val()); });
    $('#prop-bg-color').on('input', function() {
        if (!activeElement) return;
        activeElement.data('bg-color', $(this).val());
        activeElement.find('button').css('background-color', $(this).val());
    });
    $('#prop-content').on('input', function() {
        if (!activeElement) return;
        const txt = $(this).val();
        if (activeElement.find('.content-render').length) {
            activeElement.find('.content-render').text(txt);
        } else if (activeElement.find('button').length) {
            activeElement.find('button').text(txt);
        }
        refreshLayers();
    });

    $('#prop-anim-effect').on('change', function() { if (activeElement) activeElement.data('anim-effect', $(this).val()); });
    $('#prop-anim-delay').on('input', function() { if (activeElement) activeElement.data('anim-delay', $(this).val()); });
    $('#prop-anim-duration').on('input', function() { if (activeElement) activeElement.data('anim-duration', $(this).val()); });

    $('#prop-duplicate-element').on('click', function() {
        if (!activeElement) return;
        const clone = activeElement.clone();
        zIndexCounter++;
        clone.attr('id', 'elem_' + Date.now())
             .css({ top: parseInt(activeElement.css('top'), 10) + 15, left: parseInt(activeElement.css('left'), 10) + 15, 'z-index': zIndexCounter })
             .data(activeElement.data());

        stage.append(clone);
        makeInteractive(clone);
        refreshLayers();
        selectElement(clone);
        recordState();
    });

    $('#prop-delete-element').on('click', function() {
        if (!activeElement) return;
        activeElement.remove();
        deselectElement();
        refreshLayers();
        recordState();
    });

    // Layers List
    function refreshLayers() {
        const list = $('#wppoppop-layers-list').empty();
        const elements = stage.find('.canvas-element').filter(function() {
            return ($(this).data('screen') || 1) === currentScreen;
        });

        if (elements.length === 0) {
            list.html('<li class="empty-layers">No elements on current screen.</li>');
            return;
        }

        elements.each(function() {
            const el = $(this);
            const id = el.attr('id');
            const type = el.data('type');
            const label = el.data('field-name') || el.text().trim().substring(0, 16) || type;

            const li = $('<li data-target="' + id + '"></li>');
            li.html('<span class="layer-title-text">[' + type + '] ' + label + '</span>');
            list.prepend(li);
        });

        list.find('.layer-title-text').on('click', function() {
            const targetId = $(this).closest('li').data('target');
            const el = $('#' + targetId);
            if (el.length) selectElement(el);
        });
    }

    function highlightLayerItem(id) {
        $('#wppoppop-layers-list li').removeClass('selected');
        $('#wppoppop-layers-list li[data-target="' + id + '"]').addClass('selected');
    }

    // Embed Code Modal
    $('#wppoppop-btn-embed').on('click', function(e) {
        e.preventDefault();
        const uid = $('#wppoppop-popup-uid').val() || 'pop_sample';
        $('#embed-code-shortcode').val('[wppoppop id="' + uid + '"]');
        $('#embed-code-button').val('[wppoppop_button id="' + uid + '"]Click Here[/wppoppop_button]');
        $('#embed-code-class').val('wppoppop-trigger-' + uid);
        $('#wppoppop-embed-modal').fadeIn(200);
    });

    $('#btn-close-embed-modal, #wppoppop-embed-modal').on('click', function(e) {
        if (e.target === this || e.target.id === 'btn-close-embed-modal') {
            $('#wppoppop-embed-modal').fadeOut(150);
        }
    });

    // Live In-Builder Preview Modal
    $('#wppoppop-btn-preview').on('click', function(e) {
        e.preventDefault();
        const modal = $('#wppoppop-live-preview-modal');
        const mount = $('#wppoppop-preview-stage-mount').empty();

        const previewBox = $('<div class="wppoppop-box"></div>').css({
            width: stage.width(),
            height: stage.height(),
            'border-radius': $('#box-border-radius').val() + 'px',
            'background': $('#box-bg-color').val() || '#ffffff',
            position: 'relative',
            overflow: 'hidden'
        });

        const elementsClone = stage.clone();
        elementsClone.find('.canvas-element').each(function() {
            $(this).removeClass('selected ui-draggable ui-draggable-handle ui-resizable')
                   .find('.ui-resizable-handle').remove();
        });

        previewBox.html(elementsClone.html());
        mount.append(previewBox);
        modal.fadeIn(200);
    });

    $('#btn-close-live-preview, #wppoppop-live-preview-modal').on('click', function(e) {
        if (e.target === this || e.target.id === 'btn-close-live-preview') {
            $('#wppoppop-live-preview-modal').fadeOut(150);
        }
    });

    // Diagnostic Pings (Webhook & Twilio)
    $('#btn-test-webhook').on('click', function() {
        const url = $('#mkt-webhook-url').val();
        if (!url) { alert('Please enter a Webhook URL.'); return; }
        const btn = $(this).text('Pinging...').prop('disabled', true);
        $.post(wppoppop_vars.ajax_url, {
            action: 'wppoppop_test_webhook',
            nonce: wppoppop_vars.nonce,
            url: url
        }).always(function() {
            btn.text('Ping Webhook').prop('disabled', false);
            alert('Webhook test request dispatched.');
        });
    });

    $('#btn-test-sms').on('click', function() {
        const sid = $('#sms-sid').val();
        const to = $('#sms-to').val();
        if (!sid || !to) { alert('Please configure Twilio SID and recipient mobile number.'); return; }
        const btn = $(this).text('Sending...').prop('disabled', true);
        $.post(wppoppop_vars.ajax_url, {
            action: 'wppoppop_test_sms',
            nonce: wppoppop_vars.nonce,
            sid: sid,
            to: to
        }).always(function() {
            btn.text('Test SMS Dispatch').prop('disabled', false);
            alert('Test SMS request dispatched.');
        });
    });

    // Save Popup Payload
    $('#wppoppop-btn-save').on('click', function(e) {
        e.preventDefault();
        const saveBtn = $(this).prop('disabled', true).text('Saving...');

        const elementsData = [];
        $('.canvas-element').each(function() {
            const el = $(this);
            elementsData.push({
                id: el.attr('id'),
                screen: el.data('screen') || 1,
                type: el.data('type'),
                field_name: el.data('field-name') || '',
                options: el.data('options') || [],
                goto_screen: el.data('goto-screen') || 1,
                required: el.data('required') || 0,
                error_msg: el.data('error-msg') || '',
                font_family: el.data('font-family') || 'Inherit',
                font_size: el.data('font-size') || 16,
                border_radius: el.data('border-radius') || 4,
                opacity: el.data('opacity') || 1.0,
                color: el.data('color') || '#222222',
                bg_color: el.data('bg-color') || '#00a32a',
                anim_effect: el.data('anim-effect') || 'none',
                anim_delay: el.data('anim-delay') || 0,
                anim_duration: el.data('anim-duration') || 500,
                z_index: el.data('z-index') || 1,
                top: parseInt(el.css('top'), 10) || 0,
                left: parseInt(el.css('left'), 10) || 0,
                width: el.outerWidth() || el.width(),
                height: el.outerHeight() || el.height(),
                content: el.data('type') === 'input' ? el.find('input').attr('placeholder') : el.text().trim()
            });
        });

        const payload = {
            meta: {
                title: $('#wppoppop-popup-title').val() || 'Untitled Popup',
                width: stage.width(),
                height: stage.height()
            },
            box_styling: {
                radius: $('#box-border-radius').val() || 8,
                bg_color: $('#box-bg-color').val() || '#ffffff',
                position_mode: $('#style-position-mode').val() || 'modal',
                backdrop_blur: parseInt($('#style-backdrop-blur').val(), 10) || 5,
                close_esc: $('#style-close-esc').is(':checked'),
                close_backdrop: $('#style-close-backdrop').is(':checked')
            },
            sounds: {
                enable: $('#snd-enable').is(':checked')
            },
            triggers: {
                on_load: $('#trig-load').is(':checked'),
                on_load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                on_exit: $('#trig-exit').is(':checked'),
                on_scroll: $('#trig-scroll').is(':checked'),
                on_idle: $('#trig-idle').is(':checked'),
                on_adblock: $('#trig-adblock').length ? $('#trig-adblock').is(':checked') : false,
                on_mobile_back: $('#trig-mobile-back').is(':checked')
            },
            logic_math: {
                expression: $('#math-expression').val() || '',
                output_target: $('#math-output-target').val() || ''
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
            video: {
                enable: $('#vid-enable').is(':checked')
            },
            autoresponder: {
                enable: $('#ar-enable').is(':checked'),
                subject: $('#ar-subject').val() || '',
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
                param_key: $('#target-url-param-key').val() || '',
                param_val: $('#target-url-param-val').val() || '',
                scope: $('#target-scope').val() || 'everywhere',
                geo_mode: $('#target-geo-mode').val() || 'all'
            },
            frequency: {
                mode: $('#freq-mode').val() || 'everytime',
                hide_submitted: $('#freq-hide-submitted').is(':checked')
            },
            custom_code: {
                css: $('#code-custom-css').val() || '',
                js: $('#code-custom-js').val() || ''
            },
            elements: elementsData
        };

        $.ajax({
            url: wppoppop_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'wppoppop_save_popup',
                nonce: wppoppop_vars.nonce,
                uid: $('#wppoppop-popup-uid').val(),
                title: $('#wppoppop-popup-title').val(),
                data: JSON.stringify(payload)
            },
            success: function(res) {
                saveBtn.prop('disabled', false).text('Save Popup');
                if (res.success) {
                    $('#wppoppop-popup-uid').val(res.data.uid);
                    alert(res.data.message || 'Popup configuration saved successfully!');
                } else {
                    alert('Save error: ' + (res.data ? res.data.message : 'Unknown rejection'));
                }
            },
            error: function(xhr, status, error) {
                saveBtn.prop('disabled', false).text('Save Popup');
                alert('Save failed: ' + error);
            }
        });
    });

    // Populate on Initial Load
    if (typeof wppoppop_vars !== 'undefined' && wppoppop_vars.current_uid) {
        $.get(wppoppop_vars.ajax_url, {
            action: 'wppoppop_load_popup',
            nonce: wppoppop_vars.nonce,
            uid: wppoppop_vars.current_uid
        }, function(res) {
            if (res.success && res.data) {
                const config = JSON.parse(res.data.data);
                $('#wppoppop-popup-title').val(res.data.title);
                $('#wppoppop-popup-uid').val(res.data.uid);

                if (config.box_styling) {
                    $('#box-border-radius').val(config.box_styling.radius || 8);
                    $('#box-bg-color').val(config.box_styling.bg_color || '#ffffff');
                    $('#style-position-mode').val(config.box_styling.position_mode || 'modal');
                    $('#style-backdrop-blur').val(config.box_styling.backdrop_blur || 5);
                    $('#style-close-esc').prop('checked', !!config.box_styling.close_esc);
                    $('#style-close-backdrop').prop('checked', !!config.box_styling.close_backdrop);
                }

                if (config.coupons) {
                    $('#cpn-enable').prop('checked', !!config.coupons.enable);
                    $('#cpn-prefix').val(config.coupons.prefix || 'POP-');
                    $('#cpn-type').val(config.coupons.type || 'percent');
                    $('#cpn-amount').val(config.coupons.amount || 15);
                    $('#cpn-auto-apply').prop('checked', !!config.coupons.auto_apply);
                    $('#woo-cart-rule').prop('checked', !!config.coupons.woo_cart_rule);
                    $('#woo-min-cart').val(config.coupons.woo_min_cart || 50);
                }

                if (config.custom_code) {
                    $('#code-custom-css').val(config.custom_code.css || '');
                    $('#code-custom-js').val(config.custom_code.js || '');
                }

                if (Array.isArray(config.elements)) {
                    applyState(config.elements.map(function(el) {
                        return {
                            id: el.id,
                            type: el.type,
                            screen: el.screen || 1,
                            top: el.top,
                            left: el.left,
                            width: el.width,
                            height: el.height,
                            content: el.content,
                            data: el
                        };
                    }));
                }
            }
        });
    }
});
