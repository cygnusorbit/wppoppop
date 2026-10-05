
jQuery(document).ready(function($) {
    'use strict';

    let activeElement = null;
    let currentScreen = 1;
    let zIndexCounter = 1;
    const stage = $('#wppoppop-stage');

    // Vertical Accordion Toggle
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

    // History Buffer
    const historyStack = [];
    let historyIndex = -1;

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

    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
            e.preventDefault();
            $('#btn-undo').trigger('click');
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
            e.preventDefault();
            $('#btn-redo').trigger('click');
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

    // Left Panel Tabs
    $('.panel-tabs .tab-btn').on('click', function() {
        const parent = $(this).closest('.wppoppop-panel');
        parent.find('.tab-btn, .tab-pane').removeClass('active');
        $(this).addClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    // Multi-Screen Tabs
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

    $('#target-geo-mode').on('change', function() {
        $('#group-geo-countries').toggle($(this).val() !== 'all');
    });

    $('#freq-mode').on('change', function() {
        $('#group-freq-days').toggle($(this).val() === 'days');
    });

    $('#stage-width').on('input', function() {
        stage.width(parseInt($(this).val(), 10) || 640);
    });
    $('#stage-height').on('input', function() {
        stage.height(parseInt($(this).val(), 10) || 400);
    });

    // Add Element
    $('.element-item').on('click', function() {
        const type = $(this).data('type');
        zIndexCounter++;
        const elementId = 'elem_' + Date.now();

        let defaultContent = 'Heading text';
        let defaultBg = '#00a32a';
        let width = 200;
        let height = 40;
        let fieldName = '';
        let options = ['10% OFF', 'FREE SHIP', '20% OFF', '5% OFF'];

        let innerMarkup = '';
        if (type === 'wheel') {
            fieldName = 'prize';
            width = 220;
            height = 220;
            innerMarkup = '<div style="background:#f6f7f7;border:2px dashed #999;border-radius:50%;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;">Fortune Wheel</div>';
        } else if (type === 'text') {
            height = 30;
            innerMarkup = '<div class="content-render" style="font-size:16px;color:#222;">' + defaultContent + '</div>';
        } else if (type === 'input') {
            fieldName = 'email';
            width = 220;
            innerMarkup = '<input type="email" placeholder="Enter email..." disabled style="width:100%;height:100%;">';
        } else if (type === 'dropdown') {
            fieldName = 'choice';
            width = 220;
            innerMarkup = '<select disabled style="width:100%;height:100%;"><option>Option 1</option><option>Option 2</option></select>';
        } else if (type === 'radio') {
            fieldName = 'radio_choice';
            width = 240;
            innerMarkup = '<div style="display:flex;gap:10px;font-size:12px;"><label><input type="radio" checked disabled> Opt 1</label></div>';
        } else if (type === 'checkbox') {
            fieldName = 'terms';
            width = 240;
            innerMarkup = '<div style="display:flex;gap:10px;font-size:12px;"><label><input type="checkbox" checked disabled> Checkbox</label></div>';
        } else if (type === 'rating') {
            fieldName = 'rating';
            width = 160;
            height = 35;
            innerMarkup = '<div style="color:#f0ad4e;font-size:22px;letter-spacing:4px;">&#9733;&#9733;&#9733;&#9733;&#9733;</div>';
        } else if (type === 'date') {
            fieldName = 'date';
            width = 180;
            innerMarkup = '<input type="date" disabled style="width:100%;height:100%;">';
        } else if (type === 'number') {
            fieldName = 'qty';
            width = 120;
            innerMarkup = '<input type="number" value="1" disabled style="width:100%;height:100%;">';
        } else if (type === 'button') {
            width = 160;
            innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;font-weight:600;">Submit</button>';
        } else if (type === 'nextstep') {
            width = 160;
            defaultContent = 'Next Step &rarr;';
            defaultBg = '#2271b1';
            innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;font-weight:600;">' + defaultContent + '</button>';
        } else {
            innerMarkup = '<div class="content-render">[' + type + ']</div>';
        }

        const elem = $('<div class="canvas-element"></div>')
            .attr('id', elementId)
            .data('type', type)
            .data('screen', currentScreen)
            .data('field-name', fieldName)
            .data('options', options)
            .data('font-family', 'Inherit')
            .data('anim', 'fade')
            .data('anim-delay', 0)
            .data('anim-duration', 500)
            .data('z-index', zIndexCounter)
            .data('font-size', 16)
            .data('color', '#222222')
            .data('bg-color', defaultBg)
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

    $('#chk-grid-snap').on('change', function() {
        const isSnap = $(this).is(':checked');
        $('.canvas-element').draggable('option', 'grid', isSnap ? [10, 10] : false);
        $('.canvas-element').resizable('option', 'grid', isSnap ? [10, 10] : false);
    });

    function selectElement(elem) {
        $('.canvas-element').removeClass('selected');
        elem.addClass('selected');
        activeElement = elem;

        $('#inspector-empty-state').hide();
        $('#inspector-controls').show();

        const inspectorItem = $('.accordion-item[data-accordion="inspector"]');
        if (!inspectorItem.hasClass('active')) {
            inspectorItem.addClass('active');
            inspectorItem.find('.accordion-body').slideDown(150);
        }

        const type = elem.data('type');
        let contentVal = '';
        if (type === 'text' || type === 'html') contentVal = elem.find('.content-render').text();
        if (type === 'button' || type === 'nextstep') contentVal = elem.find('button').text();
        if (type === 'input') contentVal = elem.find('input').attr('placeholder');

        $('#prop-field-name').val(elem.data('field-name') || '');
        $('#prop-content').val(contentVal);
        $('#prop-font-family').val(elem.data('font-family') || 'Inherit');
        $('#prop-font-size').val(elem.data('font-size') || 16);
        $('#prop-color').val(rgbToHex(elem.data('color') || '#222222'));
        $('#prop-bg-color').val(rgbToHex(elem.data('bg-color') || '#00a32a'));

        if (type === 'wheel' || type === 'dropdown' || type === 'radio' || type === 'checkbox') {
            $('#group-prop-options').show();
            const opts = elem.data('options') || [];
            $('#prop-options').val(Array.isArray(opts) ? opts.join(', ') : opts);
        } else {
            $('#group-prop-options').hide();
        }

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
    }

    stage.on('click', deselectElement);

    $('#prop-field-name').on('input', function() {
        if (activeElement) activeElement.data('field-name', $(this).val());
    });

    $('#prop-font-family').on('change', function() {
        if (activeElement) {
            const font = $(this).val();
            activeElement.data('font-family', font);
            activeElement.css('font-family', font === 'Inherit' ? 'inherit' : font);
        }
    });

    $('#prop-options').on('input', function() {
        if (!activeElement) return;
        const opts = $(this).val().split(',').map(s => s.trim()).filter(Boolean);
        activeElement.data('options', opts);
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
            const li = $('<li data-target="' + el.attr('id') + '"><span>[' + el.data('type') + '] ' + (el.data('field-name') || el.text().trim().substring(0, 14)) + '</span></li>');
            list.prepend(li);
        });

        list.find('li').on('click', function() {
            const el = $('#' + $(this).data('target'));
            if (el.length) selectElement(el);
        });
    }

    function highlightLayerItem(id) {
        $('#wppoppop-layers-list li').removeClass('selected');
        $('#wppoppop-layers-list li[data-target="' + id + '"]').addClass('selected');
    }

    // Save Action
    $('#wppoppop-btn-save').on('click', function(e) {
        e.preventDefault();
        const saveBtn = $(this);

        if (typeof wppoppop_vars === 'undefined' || !wppoppop_vars.ajax_url) {
            alert('Configuration error: wppoppop_vars is not defined. Please refresh the page.');
            return;
        }

        saveBtn.prop('disabled', true).text('Saving...');

        const elementsData = [];
        $('.canvas-element').each(function() {
            const el = $(this);
            elementsData.push({
                id: el.attr('id'),
                screen: el.data('screen') || 1,
                type: el.data('type'),
                field_name: el.data('field-name') || '',
                options: el.data('options') || [],
                font_family: el.data('font-family') || 'Inherit',
                anim: el.data('anim') || 'none',
                anim_delay: el.data('anim-delay') || 0,
                anim_duration: el.data('anim-duration') || 500,
                top: parseInt(el.css('top'), 10) || 0,
                left: parseInt(el.css('left'), 10) || 0,
                width: el.outerWidth() || el.width(),
                height: el.outerHeight() || el.height(),
                z_index: el.data('z-index') || 1,
                font_size: el.data('font-size') || 16,
                color: el.data('color') || '#222222',
                bg_color: el.data('bg-color') || '#00a32a',
                content: (el.data('type') === 'input' || el.data('type') === 'date') 
                    ? el.find('input').attr('placeholder') 
                    : (el.find('.content-render').length ? el.find('.content-render').html() : el.text().trim())
            });
        });

        const payload = {
            meta: {
                title: $('#wppoppop-popup-title').val() || 'Untitled Popup',
                width: stage.width(),
                height: stage.height(),
                bg_color: '#ffffff'
            },
            styling: {
                backdrop_blur: parseInt($('#style-backdrop-blur').val(), 10) || 0,
                close_esc: $('#style-close-esc').is(':checked'),
                close_backdrop: $('#style-close-backdrop').is(':checked')
            },
            triggers: {
                on_load: $('#trig-load').is(':checked'),
                on_load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                on_exit: $('#trig-exit').is(':checked'),
                on_scroll: $('#trig-scroll').is(':checked') ? 50 : 0,
                on_idle: $('#trig-idle').is(':checked') ? 15 : 0,
                click_selector: $('#trig-click-selector').val() || ''
            },
            autoresponder: {
                enable_user_email: $('#ar-enable').is(':checked'),
                subject: $('#ar-subject').val() || '',
                message: $('#ar-message').val() || ''
            },
            mailchimp: {
                enable: $('#mc-enable').is(':checked'),
                api_key: $('#mc-api-key').val() || '',
                list_id: $('#mc-list-id').val() || ''
            },
            activecampaign: {
                enable: $('#ac-enable').is(':checked'),
                api_url: $('#ac-api-url').val() || '',
                api_key: $('#ac-api-key').val() || ''
            },
            targeting: {
                geo_mode: $('#target-geo-mode').val() || 'all',
                geo_countries: $('#target-geo-countries').val() || '',
                devices: $('#target-devices').val() || 'all'
            },
            frequency: {
                mode: $('#freq-mode').val() || 'everytime',
                days: parseInt($('#freq-days-count').val(), 10) || 7,
                hide_submitted: $('#freq-hide-submitted').is(':checked')
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
                    if (window.history && window.history.replaceState) {
                        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=wppoppop-builder&uid=' + res.data.uid;
                        window.history.replaceState({path: newUrl}, '', newUrl);
                    }
                    alert(res.data.message || 'Popup saved successfully!');
                } else {
                    alert('Save failed: ' + (res.data ? res.data.message : 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                saveBtn.prop('disabled', false).text('Save Popup');
                alert('Save failed (HTTP ' + xhr.status + '): ' + (xhr.responseText || error));
            }
        });
    });

    // Populate on edit
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

                if (config.meta) {
                    $('#stage-width').val(config.meta.width);
                    $('#stage-height').val(config.meta.height);
                    stage.width(config.meta.width).height(config.meta.height);
                }
                if (config.styling) {
                    $('#style-backdrop-blur').val(config.styling.backdrop_blur || 0);
                    $('#style-close-esc').prop('checked', config.styling.close_esc !== false);
                    $('#style-close-backdrop').prop('checked', config.styling.close_backdrop !== false);
                }
                if (config.triggers) {
                    $('#trig-load').prop('checked', !!config.triggers.on_load);
                    $('#trig-load-delay').val(config.triggers.on_load_delay || 0);
                    $('#trig-exit').prop('checked', !!config.triggers.on_exit);
                    $('#trig-scroll').prop('checked', !!config.triggers.on_scroll);
                    $('#trig-idle').prop('checked', !!config.triggers.on_idle);
                    $('#trig-click-selector').val(config.triggers.click_selector || '');
                }
                if (config.autoresponder) {
                    $('#ar-enable').prop('checked', !!config.autoresponder.enable_user_email);
                    $('#ar-subject').val(config.autoresponder.subject || '');
                    $('#ar-message').val(config.autoresponder.message || '');
                }
                if (config.mailchimp) {
                    $('#mc-enable').prop('checked', !!config.mailchimp.enable);
                    $('#mc-api-key').val(config.mailchimp.api_key || '');
                    $('#mc-list-id').val(config.mailchimp.list_id || '');
                }
                if (config.activecampaign) {
                    $('#ac-enable').prop('checked', !!config.activecampaign.enable);
                    $('#ac-api-url').val(config.activecampaign.api_url || '');
                    $('#ac-api-key').val(config.activecampaign.api_key || '');
                }
                if (config.targeting) {
                    $('#target-geo-mode').val(config.targeting.geo_mode || 'all').trigger('change');
                    $('#target-geo-countries').val(config.targeting.geo_countries || '');
                    $('#target-devices').val(config.targeting.devices || 'all');
                }
                if (config.frequency) {
                    $('#freq-mode').val(config.frequency.mode || 'everytime').trigger('change');
                    $('#freq-days-count').val(config.frequency.days || 7);
                    $('#freq-hide-submitted').prop('checked', !!config.frequency.hide_submitted);
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

    function rgbToHex(rgb) {
        if (!rgb || rgb.indexOf('rgb') === -1) return rgb || '#000000';
        const parts = rgb.match(/\d+/g);
        return "#" + ((1 << 24) + (parseInt(parts[0]) << 16) + (parseInt(parts[1]) << 8) + parseInt(parts[2])).toString(16).slice(1);
    }
});
