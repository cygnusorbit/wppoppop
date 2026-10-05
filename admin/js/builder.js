
(function($) {
    'use strict';

    let activeElement = null;
    let currentScreen = 1;
    let zIndexCounter = 1;
    const stage = $('#wppoppop-stage');

    // Multi-Screen Tab Switcher
    $('.btn-screen-toggle').on('click', function() {
        $('.btn-screen-toggle').removeClass('active');
        $(this).addClass('active');
        currentScreen = parseInt($(this).data('screen'), 10);

        $('.canvas-element').each(function() {
            const elScreen = $(this).data('screen') || 1;
            if (elScreen === currentScreen) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        deselectElement();
        refreshLayers();
    });

    // Panel Tab Navigation
    $('.panel-tabs .tab-btn').on('click', function() {
        const parent = $(this).closest('.wppoppop-panel');
        parent.find('.tab-btn').removeClass('active');
        parent.find('.tab-pane').removeClass('active');
        $(this).addClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    $('#freq-mode').on('change', function() {
        if ($(this).val() === 'days') {
            $('#group-freq-days').show();
        } else {
            $('#group-freq-days').hide();
        }
    });

    $('#stage-width').on('input', function() {
        stage.width(parseInt($(this).val(), 10) || 640);
    });
    $('#stage-height').on('input', function() {
        stage.height(parseInt($(this).val(), 10) || 400);
    });

    // Add Element to Current Screen
    $('.element-item').on('click', function() {
        const type = $(this).data('type');
        zIndexCounter++;
        const elementId = 'elem_' + Date.now();

        let defaultContent = 'Heading text';
        let defaultBg = '#00a32a';
        let width = 200;
        let height = 40;
        let fieldName = '';

        if (type === 'text') height = 30;
        if (type === 'input') { fieldName = 'email'; width = 220; defaultContent = 'Enter email...'; }
        if (type === 'number') { fieldName = 'qty'; width = 120; defaultContent = '1'; }
        if (type === 'button') { width = 160; defaultContent = 'Submit'; }
        if (type === 'nextstep') { width = 160; defaultContent = 'Next Step &rarr;'; defaultBg = '#2271b1'; }
        if (type === 'html') { height = 50; defaultContent = '<strong>Custom HTML Block</strong>'; }

        let innerMarkup = '';
        if (type === 'text') innerMarkup = '<div class="content-render" style="font-size:16px;color:#222;">' + defaultContent + '</div>';
        if (type === 'input') innerMarkup = '<input type="email" placeholder="' + defaultContent + '" disabled style="width:100%;height:100%;">';
        if (type === 'number') innerMarkup = '<input type="number" value="' + defaultContent + '" disabled style="width:100%;height:100%;">';
        if (type === 'button' || type === 'nextstep') innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;font-weight:600;">' + defaultContent + '</button>';
        if (type === 'html') innerMarkup = '<div class="content-render">' + defaultContent + '</div>';

        const elem = $('<div class="canvas-element"></div>')
            .attr('id', elementId)
            .data('type', type)
            .data('screen', currentScreen)
            .data('field-name', fieldName)
            .data('anim', 'fade')
            .data('anim-delay', 0)
            .data('anim-duration', 500)
            .data('z-index', zIndexCounter)
            .data('font-size', 16)
            .data('color', '#222222')
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
        $('#anim-empty-state').hide();
        $('#anim-controls').show();

        const type = elem.data('type');
        let contentVal = '';
        if (type === 'text' || type === 'html') contentVal = elem.find('.content-render').text();
        if (type === 'button' || type === 'nextstep') contentVal = elem.find('button').text();
        if (type === 'input') contentVal = elem.find('input').attr('placeholder');
        if (type === 'number') contentVal = elem.find('input').val();

        $('#prop-field-name').val(elem.data('field-name') || '');
        $('#prop-content').val(contentVal);
        $('#prop-font-size').val(elem.data('font-size') || 16);
        $('#prop-color').val(rgbToHex(elem.data('color') || '#222222'));
        $('#prop-bg-color').val(rgbToHex(elem.data('bg-color') || '#00a32a'));

        $('#prop-anim-effect').val(elem.data('anim') || 'fade');
        $('#prop-anim-delay').val(elem.data('anim-delay') || 0);
        $('#prop-anim-duration').val(elem.data('anim-duration') || 500);

        highlightLayerItem(elem.attr('id'));
    }

    function deselectElement() {
        $('.canvas-element').removeClass('selected');
        activeElement = null;
        $('#inspector-empty-state').show();
        $('#inspector-controls').hide();
        $('#anim-empty-state').show();
        $('#anim-controls').hide();
    }

    stage.on('click', deselectElement);

    // Inspector Live Updates
    $('#prop-field-name').on('input', function() {
        if (activeElement) activeElement.data('field-name', $(this).val());
    });

    $('#prop-content').on('input', function() {
        if (!activeElement) return;
        const val = $(this).val();
        const type = activeElement.data('type');
        if (type === 'text' || type === 'html') activeElement.find('.content-render').text(val);
        if (type === 'button' || type === 'nextstep') activeElement.find('button').text(val);
        if (type === 'input') activeElement.find('input').attr('placeholder', val);
        refreshLayers();
    });

    $('#prop-anim-effect').on('change', function() {
        if (activeElement) activeElement.data('anim', $(this).val());
    });
    $('#prop-anim-delay').on('input', function() {
        if (activeElement) activeElement.data('anim-delay', parseInt($(this).val(), 10) || 0);
    });
    $('#prop-anim-duration').on('input', function() {
        if (activeElement) activeElement.data('anim-duration', parseInt($(this).val(), 10) || 500);
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
        deselectElement();
        refreshLayers();
    });

    function refreshLayers() {
        const list = $('#wppoppop-layers-list');
        list.empty();

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
                screen: el.data('screen') || 1,
                type: el.data('type'),
                field_name: el.data('field-name') || '',
                anim: el.data('anim') || 'none',
                anim_delay: el.data('anim-delay') || 0,
                anim_duration: el.data('anim-duration') || 500,
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
            frequency: {
                mode: $('#freq-mode').val(),
                days: parseInt($('#freq-days-count').val(), 10) || 7,
                hide_submitted: $('#freq-hide-submitted').is(':checked')
            },
            triggers: {
                on_load: $('#trig-load').is(':checked'),
                on_load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                on_exit: $('#trig-exit').is(':checked'),
                on_scroll: $('#trig-scroll').is(':checked') ? 50 : 0,
                on_idle: $('#trig-idle').is(':checked') ? 15 : 0
            },
            targeting: {
                devices: $('#target-devices').val(),
                scope: $('#target-scope').val(),
                specific_ids: $('#target-specific-ids').val()
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

    // Populate on edit
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
                if (config.frequency) {
                    $('#freq-mode').val(config.frequency.mode || 'everytime').trigger('change');
                    $('#freq-days-count').val(config.frequency.days || 7);
                    $('#freq-hide-submitted').prop('checked', !!config.frequency.hide_submitted);
                }
                if (config.targeting) {
                    $('#target-devices').val(config.targeting.devices || 'all');
                    $('#target-scope').val(config.targeting.scope || 'everywhere');
                    $('#target-specific-ids').val(config.targeting.specific_ids || '');
                }

                if (Array.isArray(config.elements)) {
                    stage.empty();
                    config.elements.forEach(function(el) {
                        let innerMarkup = '';
                        if (el.type === 'text') innerMarkup = '<div class="content-render" style="font-size:' + el.font_size + 'px;color:' + el.color + ';">' + el.content + '</div>';
                        if (el.type === 'input') innerMarkup = '<input type="email" placeholder="' + el.content + '" disabled style="width:100%;height:100%;">';
                        if (el.type === 'number') innerMarkup = '<input type="number" value="' + el.content + '" disabled style="width:100%;height:100%;">';
                        if (el.type === 'button' || el.type === 'nextstep') innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + el.bg_color + ';color:#fff;border:none;border-radius:4px;font-weight:600;">' + el.content + '</button>';
                        if (el.type === 'html') innerMarkup = '<div class="content-render">' + el.content + '</div>';

                        const canvasEl = $('<div class="canvas-element"></div>')
                            .attr('id', el.id)
                            .data(el)
                            .css({
                                top: el.top,
                                left: el.left,
                                width: el.width,
                                height: el.height,
                                'z-index': el.z_index,
                                display: (el.screen || 1) === currentScreen ? 'block' : 'none'
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
