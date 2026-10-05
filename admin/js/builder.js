
(function($) {
    'use strict';

    let activeElement = null;
    let zIndexCounter = 1;
    const stage = $('#wppoppop-stage');

    // Tab Navigation
    $('.panel-tabs .tab-btn').on('click', function() {
        const parent = $(this).closest('.wppoppop-panel');
        parent.find('.tab-btn').removeClass('active');
        parent.find('.tab-pane').removeClass('active');
        $(this).addClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    // Canvas Resizing
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
        let defaultFontSize = 16;
        let width = 200;
        let height = 40;

        let innerMarkup = '';
        if (type === 'text') {
            innerMarkup = '<div class="content-render" style="font-size:16px;color:#222;">Double-click or inspect to edit</div>';
            height = 30;
        } else if (type === 'input') {
            innerMarkup = '<input type="email" placeholder="Enter your email..." disabled style="width:100%;height:100%;">';
            width = 220;
        } else if (type === 'button') {
            innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;">Subscribe</button>';
            width = 160;
        } else if (type === 'html') {
            innerMarkup = '<div class="content-render"><strong>Custom HTML Block</strong></div>';
            height = 50;
        }

        const elem = $('<div class="canvas-element"></div>')
            .attr('id', elementId)
            .data('type', type)
            .data('z-index', zIndexCounter)
            .data('font-size', defaultFontSize)
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

    // Layers List Handling
    function refreshLayers() {
        const list = $('#wppoppop-layers-list');
        list.empty();

        const elements = stage.find('.canvas-element');
        if (elements.length === 0) {
            list.html('<li class="empty-layers">No elements added yet.</li>');
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

    function rgbToHex(rgb) {
        if (!rgb || rgb.indexOf('rgb') === -1) return rgb || '#000000';
        const parts = rgb.match(/\d+/g);
        return "#" + ((1 << 24) + (parseInt(parts[0]) << 16) + (parseInt(parts[1]) << 8) + parseInt(parts[2])).toString(16).slice(1);
    }
})(jQuery);
