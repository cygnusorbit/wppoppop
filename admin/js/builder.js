
(function($) {
    'use strict';

    let activeElement = null;
    let zIndexCounter = 1;
    const stage = $('#wppoppop-stage');

    // Tab Switching
    $('.panel-tabs .tab-btn').on('click', function() {
        const parent = $(this).closest('.wppoppop-panel');
        parent.find('.tab-btn').removeClass('active');
        parent.find('.tab-pane').removeClass('active');
        $(this).addClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    // Targeting Dropdown
    $('#target-scope').on('change', function() {
        if ($(this).val() === 'specific') {
            $('#group-specific-ids').show();
        } else {
            $('#group-specific-ids').hide();
        }
    });

    // Canvas Sizing
    $('#stage-width').on('input', function() {
        stage.width(parseInt($(this).val(), 10) || 640);
    });
    $('#stage-height').on('input', function() {
        stage.height(parseInt($(this).val(), 10) || 400);
    });

    // Add Element
    $('.element-item').on('click', function() {
        const type = $(this).data('type');
        const elementId = 'elem_' + Date.now();
        zIndexCounter++;

        let defaultContent = 'Heading or message';
        let defaultBg = '#00a32a';
        let defaultColor = '#222222';
        let width = 200;
        let height = 40;
        let fieldName = '';

        let innerMarkup = '';
        if (type === 'text') {
            innerMarkup = '<div class="content-render" style="font-size:16px;color:#222;">Double-click or inspect to edit</div>';
            height = 30;
        } else if (type === 'input') {
            fieldName = 'email';
            innerMarkup = '<input type="email" placeholder="Enter your email..." disabled style="width:100%;height:100%;">';
            width = 220;
        } else if (type === 'number') {
            fieldName = 'qty';
            innerMarkup = '<input type="number" value="1" disabled style="width:100%;height:100%;">';
            width = 120;
        } else if (type === 'button') {
            innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;">Submit</button>';
            width = 160;
        } else if (type === 'html') {
            innerMarkup = '<div class="content-render"><strong>Custom HTML Block</strong></div>';
            height = 50;
        }

        const elem = $('<div class="canvas-element"></div>')
            .attr('id', elementId)
            .data('type', type)
            .data('field-name', fieldName)
            .data('z-index', zIndexCounter)
            .data('font-size', 16)
            .data('color', defaultColor)
            .data('bg-color', defaultBg)
            .data('custom-class', '')
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
    });

    function makeInteractive(elem) {
        elem.draggable({
            containment: '#wppoppop-stage',
            stop: function() { selectElement(elem); }
        }).resizable({
            containment: '#wppoppop-stage',
            stop: function() { selectElement(elem); }
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
        let contentVal = '';
        if (type === 'text' || type === 'html') {
            contentVal = elem.find('.content-render').text();
        } else if (type === 'button') {
            contentVal = elem.find('button').text();
        } else if (type === 'input') {
            contentVal = elem.find('input').attr('placeholder');
        }

        $('#prop-field-name').val(elem.data('field-name') || '');
        $('#prop-content').val(contentVal);
        $('#prop-font-size').val(elem.data('font-size') || 16);
        $('#prop-color').val(rgbToHex(elem.data('color') || '#222222'));
        $('#prop-bg-color').val(rgbToHex(elem.data('bg-color') || '#00a32a'));
        $('#prop-custom-class').val(elem.data('custom-class') || '');

        highlightLayerItem(elem.attr('id'));
    }

    stage.on('click', function() {
        $('.canvas-element').removeClass('selected');
        activeElement = null;
        $('#inspector-empty-state').show();
        $('#inspector-controls').hide();
        $('#wppoppop-layers-list li').removeClass('selected');
    });

    // Inspector Live Updates
    $('#prop-field-name').on('input', function() {
        if (activeElement) activeElement.data('field-name', $(this).val());
    });

    $('#prop-content').on('input', function() {
        if (!activeElement) return;
        const val = $(this).val();
        const type = activeElement.data('type');
        if (type === 'text' || type === 'html') {
            activeElement.find('.content-render').text(val);
        } else if (type === 'button') {
            activeElement.find('button').text(val);
        } else if (type === 'input') {
            activeElement.find('input').attr('placeholder', val);
        }
        refreshLayers();
    });

    $('#prop-font-size').on('input', function() {
        if (!activeElement) return;
        const size = $(this).val();
        activeElement.data('font-size', size);
        activeElement.find('.content-render, button, input').css('font-size', size + 'px');
    });

    $('#prop-color').on('input', function() {
        if (!activeElement) return;
        const color = $(this).val();
        activeElement.data('color', color);
        activeElement.find('.content-render').css('color', color);
    });

    $('#prop-bg-color').on('input', function() {
        if (!activeElement) return;
        const bg = $(this).val();
        activeElement.data('bg-color', bg);
        if (activeElement.data('type') === 'button') {
            activeElement.find('button').css('background-color', bg);
        }
    });

    $('#prop-custom-class').on('input', function() {
        if (activeElement) activeElement.data('custom-class', $(this).val());
    });

    $('#prop-duplicate-element').on('click', function() {
        if (!activeElement) return;
        const clone = activeElement.clone();
        const newId = 'elem_' + Date.now();
        zIndexCounter++;

        clone.attr('id', newId)
             .css({
                 top: parseInt(activeElement.css('top'), 10) + 15,
                 left: parseInt(activeElement.css('left'), 10) + 15,
                 'z-index': zIndexCounter
             })
             .data(activeElement.data());

        stage.append(clone);
        makeInteractive(clone);
        refreshLayers();
        selectElement(clone);
    });

    $('#prop-delete-element').on('click', function() {
        if (!activeElement) return;
        activeElement.remove();
        activeElement = null;
        $('#inspector-empty-state').show();
        $('#inspector-controls').hide();
        refreshLayers();
    });

    $('#wppoppop-btn-reset').on('click', function() {
        if (confirm('Clear all elements from canvas?')) {
            stage.empty();
            refreshLayers();
            $('#inspector-empty-state').show();
            $('#inspector-controls').hide();
        }
    });

    function refreshLayers() {
        const list = $('#wppoppop-layers-list');
        list.empty();

        const elements = stage.find('.canvas-element');
        if (elements.length === 0) {
            list.html('<li class="empty-layers">No elements on canvas.</li>');
            return;
        }

        elements.each(function() {
            const el = $(this);
            const id = el.attr('id');
            const type = el.data('type');
            const preview = el.text().trim().substring(0, 16) || type;
            const li = $('<li data-target="' + id + '"><span>[' + type + '] ' + preview + '</span></li>');
            list.prepend(li);
        });

        list.find('li').on('click', function() {
            const targetId = $(this).data('target');
            const el = $('#' + targetId);
            if (el.length) selectElement(el);
        });
    }

    function highlightLayerItem(id) {
        $('#wppoppop-layers-list li').removeClass('selected');
        $('#wppoppop-layers-list li[data-target="' + id + '"]').addClass('selected');
    }

    // Save Action
    $('#wppoppop-btn-save').on('click', function() {
        const elementsData = [];
        $('.canvas-element').each(function() {
            const el = $(this);
            elementsData.push({
                id: el.attr('id'),
                type: el.data('type'),
                field_name: el.data('field-name') || '',
                top: parseInt(el.css('top'), 10),
                left: parseInt(el.css('left'), 10),
                width: el.width(),
                height: el.height(),
                z_index: el.data('z-index') || 1,
                font_size: el.data('font-size') || 16,
                color: el.data('color') || '#222222',
                bg_color: el.data('bg-color') || '#00a32a',
                custom_class: el.data('custom-class') || '',
                content: el.data('type') === 'input' ? el.find('input').attr('placeholder') : el.text().trim()
            });
        });

        const payload = {
            meta: {
                title: $('#wppoppop-popup-title').val(),
                width: stage.width(),
                height: stage.height(),
                bg_color: '#ffffff'
            },
            triggers: {
                on_load: $('#trig-load').is(':checked'),
                on_load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                on_exit: $('#trig-exit').is(':checked'),
                on_scroll: $('#trig-scroll').is(':checked') ? (parseInt($('#trig-scroll-percent').val(), 10) || 50) : 0,
                on_idle: $('#trig-idle').is(':checked') ? (parseInt($('#trig-idle-seconds').val(), 10) || 15) : 0,
                click_selector: $('#trig-click-selector').val()
            },
            targeting: {
                scope: $('#target-scope').val(),
                specific_ids: $('#target-specific-ids').val()
            },
            conditional_logic: {
                if_field: $('#logic-if-field').val(),
                equals_val: $('#logic-equals-val').val(),
                target_layer: $('#logic-target-layer').val(),
                action: $('#logic-action').val()
            },
            math: {
                expression: $('#math-expression').val(),
                output_target: $('#math-output-target').val()
            },
            custom_css: $('#custom-css-area').val(),
            custom_js: {
                on_init: $('#custom-js-init').val(),
                on_submit: $('#custom-js-submit').val()
            },
            notifications: {
                enable_email: $('#notif-enable').is(':checked'),
                recipient: $('#notif-recipient').val(),
                subject: $('#notif-subject').val()
            },
            actions: {
                success_message: $('#act-success-msg').val(),
                redirect_url: $('#act-redirect-url').val()
            },
            elements: elementsData
        };

        $.post(wppoppop_vars.ajax_url, {
            action: 'wppoppop_save_popup',
            nonce: wppoppop_vars.nonce,
            uid: $('#wppoppop-popup-uid').val(),
            title: $('#wppoppop-popup-title').val(),
            data: JSON.stringify(payload)
        }, function(res) {
            if (res.success) {
                $('#wppoppop-popup-uid').val(res.data.uid);
                alert(res.data.message);
            } else {
                alert('Save failed: ' + res.data.message);
            }
        });
    });

    // Populate Builder on Edit
    if (wppoppop_vars.current_uid) {
        $.get(wppoppop_vars.ajax_url, {
            action: 'wppoppop_load_popup',
            nonce: wppoppop_vars.nonce,
            uid: wppoppop_vars.current_uid
        }, function(res) {
            if (res.success && res.data) {
                const row = res.data;
                $('#wppoppop-popup-title').val(row.title);
                $('#wppoppop-popup-uid').val(row.uid);

                const config = JSON.parse(row.data);
                if (config.meta) {
                    $('#stage-width').val(config.meta.width);
                    $('#stage-height').val(config.meta.height);
                    stage.width(config.meta.width).height(config.meta.height);
                }
                if (config.triggers) {
                    $('#trig-load').prop('checked', !!config.triggers.on_load);
                    $('#trig-load-delay').val(config.triggers.on_load_delay || 0);
                    $('#trig-exit').prop('checked', !!config.triggers.on_exit);
                    $('#trig-scroll').prop('checked', !!config.triggers.on_scroll);
                    $('#trig-scroll-percent').val(config.triggers.on_scroll || 50);
                    $('#trig-idle').prop('checked', !!config.triggers.on_idle);
                    $('#trig-idle-seconds').val(config.triggers.on_idle || 15);
                    $('#trig-click-selector').val(config.triggers.click_selector || '');
                }
                if (config.targeting) {
                    $('#target-scope').val(config.targeting.scope || 'everywhere').trigger('change');
                    $('#target-specific-ids').val(config.targeting.specific_ids || '');
                }
                if (config.conditional_logic) {
                    $('#logic-if-field').val(config.conditional_logic.if_field || '');
                    $('#logic-equals-val').val(config.conditional_logic.equals_val || '');
                    $('#logic-target-layer').val(config.conditional_logic.target_layer || '');
                    $('#logic-action').val(config.conditional_logic.action || 'show');
                }
                if (config.math) {
                    $('#math-expression').val(config.math.expression || '');
                    $('#math-output-target').val(config.math.output_target || '');
                }
                if (config.custom_css) {
                    $('#custom-css-area').val(config.custom_css);
                }
                if (config.custom_js) {
                    $('#custom-js-init').val(config.custom_js.on_init || '');
                    $('#custom-js-submit').val(config.custom_js.on_submit || '');
                }
                if (config.notifications) {
                    $('#notif-enable').prop('checked', !!config.notifications.enable_email);
                    $('#notif-recipient').val(config.notifications.recipient || '');
                    $('#notif-subject').val(config.notifications.subject || '');
                }
                if (config.actions) {
                    $('#act-success-msg').val(config.actions.success_message || '');
                    $('#act-redirect-url').val(config.actions.redirect_url || '');
                }

                // Render Elements
                if (Array.isArray(config.elements)) {
                    stage.empty();
                    config.elements.forEach(function(el) {
                        let innerMarkup = '';
                        if (el.type === 'text') innerMarkup = '<div class="content-render" style="font-size:' + el.font_size + 'px;color:' + el.color + ';">' + el.content + '</div>';
                        if (el.type === 'input') innerMarkup = '<input type="email" placeholder="' + el.content + '" disabled style="width:100%;height:100%;">';
                        if (el.type === 'number') innerMarkup = '<input type="number" value="1" disabled style="width:100%;height:100%;">';
                        if (el.type === 'button') innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + el.bg_color + ';color:#fff;border:none;border-radius:4px;">' + el.content + '</button>';
                        if (el.type === 'html') innerMarkup = '<div class="content-render">' + el.content + '</div>';

                        const canvasEl = $('<div class="canvas-element"></div>')
                            .attr('id', el.id)
                            .data(el)
                            .css({
                                top: el.top,
                                left: el.left,
                                width: el.width,
                                height: el.height,
                                'z-index': el.z_index
                            })
                            .html(innerMarkup);

                        stage.append(canvasEl);
                        makeInteractive(canvasEl);
                    });
                    refreshLayers();
                }
            }
        });
    }

    function rgbToHex(rgb) {
        if (!rgb || rgb.indexOf('rgb') === -1) return rgb || '#000000';
        const parts = rgb.match(/\d+/g);
        return "#" + ((1 << 24) + (parseInt(parts[0]) << 16) + (parseInt(parts[1]) << 8) + parseInt(parts[2])).toString(16).slice(1);
    }
})(jQuery);
