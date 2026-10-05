
jQuery(document).ready(function($) {
    'use strict';

    let activeElement = null;
    let currentScreen = 1;
    let currentViewport = 'desktop';
    let zIndexCounter = 1;
    const stage = $('#wppoppop-stage');
    const features = (typeof wppoppop_vars !== 'undefined' && wppoppop_vars.features) ? wppoppop_vars.features : {};

    // Standard Icon Library Catalog
    const standardIcons = [
        'dashicons-star-filled', 'dashicons-heart', 'dashicons-cart', 'dashicons-email',
        'dashicons-bell', 'dashicons-yes', 'dashicons-dismiss', 'dashicons-lock',
        'dashicons-unlock', 'dashicons-arrow-right-alt', 'dashicons-tag', 'dashicons-awards',
        'dashicons-calendar-alt', 'dashicons-clock', 'dashicons-megaphone', 'dashicons-thumbs-up',
        'dashicons-download', 'dashicons-smartphone', 'dashicons-laptop', 'dashicons-shield'
    ];

    // Responsive Mobile Viewport Mode Switcher
    $('.btn-viewport-toggle').on('click', function() {
        $('.btn-viewport-toggle').removeClass('active');
        $(this).addClass('active');
        currentViewport = $(this).data('viewport');

        if (currentViewport === 'mobile') {
            stage.css({ width: '360px', height: '520px' });
            $('#stage-width').val(360);
            $('#stage-height').val(520);
        } else {
            stage.css({ width: '640px', height: '400px' });
            $('#stage-width').val(640);
            $('#stage-height').val(400);
        }
    });

    // Icon Picker Modal Handlers
    $('#btn-open-icon-picker').on('click', function(e) {
        e.preventDefault();
        renderIconGrid('');
        $('#wppoppop-icon-picker-modal').fadeIn(150);
    });

    $('.btn-close-icon-picker, #wppoppop-icon-picker-modal').on('click', function(e) {
        if (e.target === this || $(this).hasClass('btn-close-icon-picker')) {
            $('#wppoppop-icon-picker-modal').fadeOut(150);
        }
    });

    $('#icon-search-input').on('input', function() {
        renderIconGrid($(this).val().toLowerCase());
    });

    function renderIconGrid(filter) {
        const grid = $('#icon-picker-grid').empty();
        standardIcons.filter(ic => ic.includes(filter)).forEach(function(icon) {
            const cell = $('<div class="icon-picker-cell" data-icon="' + icon + '"><span class="dashicons ' + icon + '"></span></div>');
            cell.on('click', function() {
                const chosen = $(this).data('icon');
                $('#prop-icon-class').val(chosen);
                if (activeElement) {
                    activeElement.data('icon-class', chosen);
                    updateElementDisplay(activeElement);
                }
                $('#wppoppop-icon-picker-modal').fadeOut(150);
            });
            grid.append(cell);
        });
    }

    $('#btn-clear-icon').on('click', function() {
        $('#prop-icon-class').val('');
        if (activeElement) {
            activeElement.data('icon-class', '');
            updateElementDisplay(activeElement);
        }
    });

    function updateElementDisplay(elem) {
        const icon = elem.data('icon-class');
        const content = elem.data('content') || elem.text().trim();
        const type = elem.data('type');

        if (type === 'button' || type === 'nextstep') {
            const iconHtml = icon ? '<span class="dashicons ' + icon + '" style="vertical-align:middle;margin-right:6px;"></span>' : '';
            elem.find('button').html(iconHtml + content);
        } else if (type === 'text') {
            const iconHtml = icon ? '<span class="dashicons ' + icon + '" style="vertical-align:middle;margin-right:6px;"></span>' : '';
            elem.find('.content-render').html(iconHtml + content);
        }
    }

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

    // Left Tabs
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

    // Box Decoration Handlers
    function updateStageDecoration() {
        const radius = $('#box-border-radius').val() || 8;
        const shadowPreset = $('#box-shadow-preset').val();
        const bgType = $('#box-bg-type').val();

        stage.css('border-radius', radius + 'px');

        if (shadowPreset === 'none') stage.css('box-shadow', 'none');
        if (shadowPreset === 'subtle') stage.css('box-shadow', '0 8px 30px rgba(0, 0, 0, 0.15)');
        if (shadowPreset === 'deep') stage.css('box-shadow', '0 20px 50px rgba(0, 0, 0, 0.35)');
        if (shadowPreset === 'glow') stage.css('box-shadow', '0 0 35px rgba(34, 113, 177, 0.4)');

        if (bgType === 'solid') {
            $('#group-box-solid-bg').show();
            $('#group-box-gradient').hide();
            stage.css('background', $('#box-bg-color').val() || '#ffffff');
        } else {
            $('#group-box-solid-bg').hide();
            $('#group-box-gradient').show();
            const c1 = $('#box-grad-c1').val() || '#1e293b';
            const c2 = $('#box-grad-c2').val() || '#0f172a';
            const angle = $('#box-grad-angle').val() || 135;
            stage.css('background', 'linear-gradient(' + angle + 'deg, ' + c1 + ', ' + c2 + ')');
        }
    }

    $('#box-border-radius, #box-shadow-preset, #box-bg-type, #box-bg-color, #box-grad-c1, #box-grad-c2, #box-grad-angle').on('input change', updateStageDecoration);

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
        let options = [];

        let innerMarkup = '';
        if (type === 'countdown') {
            width = 240;
            height = 55;
            innerMarkup = '<div style="background:#0f172a;color:#fff;border-radius:4px;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;">15:00 Evergreen Timer</div>';
        } else if (type === 'progress') {
            width = 300;
            height = 24;
            innerMarkup = '<div style="background:#2271b1;color:#fff;border-radius:12px;height:100%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;">50% Completed</div>';
        } else if (type === 'slider') {
            fieldName = 'budget';
            width = 220;
            height = 45;
            innerMarkup = '<div style="font-size:12px;font-weight:600;">Slider: $50 <input type="range" disabled style="width:100%;"></div>';
        } else if (type === 'signature') {
            fieldName = 'signature';
            width = 240;
            height = 100;
            innerMarkup = '<div style="background:#f8fafc;border:1px dashed #94a3b8;width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:12px;">Sign Here</div>';
        } else if (type === 'wheel') {
            fieldName = 'prize';
            width = 220;
            height = 220;
            options = ['10% OFF', 'FREE SHIP', '20% OFF', '5% OFF'];
            innerMarkup = '<div style="background:#f6f7f7;border:2px dashed #999;border-radius:50%;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;">Fortune Wheel</div>';
        } else if (type === 'input') {
            fieldName = 'email';
            width = 220;
            innerMarkup = '<input type="email" placeholder="Enter email..." disabled style="width:100%;height:100%;">';
        } else if (type === 'dropdown') {
            fieldName = 'choice';
            width = 220;
            options = ['Option 1', 'Option 2', 'Option 3'];
            innerMarkup = '<select disabled style="width:100%;height:100%;"><option>Option 1</option><option>Option 2</option></select>';
        } else if (type === 'radio') {
            fieldName = 'radio_choice';
            width = 240;
            options = ['Option 1', 'Option 2'];
            innerMarkup = '<div style="display:flex;gap:10px;font-size:12px;"><label><input type="radio" checked disabled> Opt 1</label></div>';
        } else if (type === 'checkbox') {
            fieldName = 'terms';
            width = 240;
            options = ['Agree to terms'];
            innerMarkup = '<div style="display:flex;gap:10px;font-size:12px;"><label><input type="checkbox" checked disabled> Agree</label></div>';
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
        } else if (type === 'paybutton') {
            width = 160;
            defaultContent = 'Pay Now';
            defaultBg = '#0284c7';
            innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;font-weight:600;">' + defaultContent + '</button>';
        } else if (type === 'button') {
            width = 160;
            innerMarkup = '<button type="button" style="width:100%;height:100%;background:' + defaultBg + ';color:#fff;border:none;border-radius:4px;font-weight:600;">Submit</button>';
        } else {
            innerMarkup = '<div class="content-render">[' + type + ']</div>';
        }

        const elem = $('<div class="canvas-element"></div>')
            .attr('id', elementId)
            .data('type', type)
            .data('screen', currentScreen)
            .data('field-name', fieldName)
            .data('options', options)
            .data('content', defaultContent)
            .data('timer-mins', 15)
            .data('progress-pct', 50)
            .data('slider-min', 0)
            .data('slider-max', 100)
            .data('slider-val', 50)
            .data('slider-prefix', '$')
            .data('font-family', 'Inherit')
            .data('anim', 'fade')
            .data('anim-exit', 'fadeOut')
            .data('anim-loop', 'none')
            .data('anim-delay', 0)
            .data('anim-duration', 500)
            .data('border-radius', 4)
            .data('opacity', 1.0)
            .data('padding', 0)
            .data('hover-color', '#ffffff')
            .data('hover-bg', '#0284c7')
            .data('icon-class', '')
            .data('locked', 0)
            .data('hidden', 0)
            .data('z-index', zIndexCounter)
            .data('font-size', 16)
            .data('color', '#222222')
            .data('bg-color', defaultBg)
            .css({
                top: 40,
                left: 40,
                width: width,
                height: height,
                'z-index': zIndexCounter,
                'border-radius': '4px'
            })
            .html(innerMarkup);

        stage.append(elem);
        makeInteractive(elem);
        refreshLayers();
        selectElement(elem);
        recordState();
    });

    function makeInteractive(elem) {
        if (elem.data('locked') == 1) return;

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
        $('.canvas-element').not('.layer-locked').draggable('option', 'grid', isSnap ? [10, 10] : false);
        $('.canvas-element').not('.layer-locked').resizable('option', 'grid', isSnap ? [10, 10] : false);
    });

    function selectElement(elem) {
        $('.canvas-element').removeClass('selected');
        elem.addClass('selected');
        activeElement = elem;

        $('#inspector-empty-state').hide();
        $('#inspector-controls').show();

        const type = elem.data('type');
        $('#prop-field-name').val(elem.data('field-name') || '');
        $('#prop-content').val(elem.data('content') || elem.text().trim());

        $('#prop-icon-class').val(elem.data('icon-class') || '');
        $('#prop-border-radius').val(elem.data('border-radius') || 0);
        $('#prop-opacity').val(elem.data('opacity') || 1.0);
        $('#prop-padding').val(elem.data('padding') || 0);

        $('#prop-hover-color').val(rgbToHex(elem.data('hover-color') || '#ffffff'));
        $('#prop-hover-bg').val(rgbToHex(elem.data('hover-bg') || '#0284c7'));

        $('#prop-anim-effect').val(elem.data('anim') || 'fade');
        $('#prop-anim-exit').val(elem.data('anim-exit') || 'none');
        $('#prop-anim-loop').val(elem.data('anim-loop') || 'none');
        $('#prop-anim-delay').val(elem.data('anim-delay') || 0);
        $('#prop-anim-duration').val(elem.data('anim-duration') || 500);

        $('#group-prop-slider').toggle(type === 'slider');
        $('#group-prop-countdown').toggle(type === 'countdown');

        if (type === 'slider') {
            $('#prop-slider-min').val(elem.data('slider-min') || 0);
            $('#prop-slider-max').val(elem.data('slider-max') || 100);
            $('#prop-slider-val').val(elem.data('slider-val') || 50);
            $('#prop-slider-prefix').val(elem.data('slider-prefix') || '$');
        }
        if (type === 'countdown') {
            $('#prop-countdown-mins').val(elem.data('timer-mins') || 15);
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
    $('#prop-border-radius').on('input', function() {
        if (!activeElement) return;
        const val = $(this).val();
        activeElement.data('border-radius', val).css('border-radius', val + 'px');
        activeElement.find('button, input, select').css('border-radius', val + 'px');
    });
    $('#prop-opacity').on('input', function() {
        if (!activeElement) return;
        const val = $(this).val();
        activeElement.data('opacity', val).css('opacity', val);
    });
    $('#prop-padding').on('input', function() {
        if (!activeElement) return;
        const val = $(this).val();
        activeElement.data('padding', val).css('padding', val + 'px');
    });

    $('#prop-hover-color').on('input', function() { if (activeElement) activeElement.data('hover-color', $(this).val()); });
    $('#prop-hover-bg').on('input', function() { if (activeElement) activeElement.data('hover-bg', $(this).val()); });

    $('#prop-anim-effect').on('change', function() { if (activeElement) activeElement.data('anim', $(this).val()); });
    $('#prop-anim-exit').on('change', function() { if (activeElement) activeElement.data('anim-exit', $(this).val()); });
    $('#prop-anim-loop').on('change', function() { if (activeElement) activeElement.data('anim-loop', $(this).val()); });
    $('#prop-anim-delay').on('input', function() { if (activeElement) activeElement.data('anim-delay', parseInt($(this).val(), 10) || 0); });
    $('#prop-anim-duration').on('input', function() { if (activeElement) activeElement.data('anim-duration', parseInt($(this).val(), 10) || 500); });

    $('#prop-slider-min').on('input', function() { if (activeElement) activeElement.data('slider-min', $(this).val()); });
    $('#prop-slider-max').on('input', function() { if (activeElement) activeElement.data('slider-max', $(this).val()); });
    $('#prop-slider-val').on('input', function() { if (activeElement) activeElement.data('slider-val', $(this).val()); });
    $('#prop-slider-prefix').on('input', function() { if (activeElement) activeElement.data('slider-prefix', $(this).val()); });
    $('#prop-countdown-mins').on('input', function() { if (activeElement) activeElement.data('timer-mins', $(this).val()); });
    $('#prop-field-name').on('input', function() { if (activeElement) activeElement.data('field-name', $(this).val()); });
    $('#prop-options').on('input', function() {
        if (!activeElement) return;
        activeElement.data('options', $(this).val().split(',').map(s => s.trim()).filter(Boolean));
    });

    $('#prop-content').on('input', function() {
        if (!activeElement) return;
        const val = $(this).val();
        activeElement.data('content', val);
        updateElementDisplay(activeElement);
        refreshLayers();
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

    // Layer Hierarchy Management
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
            const label = el.data('field-name') || el.data('content') || el.text().trim().substring(0, 14) || type;
            const isLocked = el.data('locked') == 1;
            const isHidden = el.data('hidden') == 1;

            const li = $('<li data-target="' + id + '"></li>');
            const titleSpan = $('<span class="layer-title-text">[' + type + '] ' + label + '</span>');
            const actions = $('<div class="layer-actions"></div>');

            const btnVis = $('<button type="button" class="layer-action-btn btn-layer-vis" title="Toggle Visibility"><span class="dashicons ' + (isHidden ? 'dashicons-hidden' : 'dashicons-visibility') + '"></span></button>');
            const btnLock = $('<button type="button" class="layer-action-btn btn-layer-lock" title="Lock/Unlock Drag"><span class="dashicons ' + (isLocked ? 'dashicons-lock' : 'dashicons-unlock') + '"></span></button>');
            const btnUp = $('<button type="button" class="layer-action-btn btn-layer-up" title="Bring Forward"><span class="dashicons dashicons-arrow-up-alt2"></span></button>');
            const btnDown = $('<button type="button" class="layer-action-btn btn-layer-down" title="Send Backward"><span class="dashicons dashicons-arrow-down-alt2"></span></button>');

            actions.append(btnVis, btnLock, btnUp, btnDown);
            li.append(titleSpan, actions);
            list.prepend(li);
        });

        list.find('.layer-title-text').on('click', function() {
            const targetId = $(this).closest('li').data('target');
            const el = $('#' + targetId);
            if (el.length) selectElement(el);
        });

        list.find('.btn-layer-vis').on('click', function(e) {
            e.stopPropagation();
            const targetId = $(this).closest('li').data('target');
            const el = $('#' + targetId);
            const isHidden = el.data('hidden') == 1;
            el.data('hidden', isHidden ? 0 : 1);
            el.toggleClass('layer-hidden', !isHidden);
            refreshLayers();
        });

        list.find('.btn-layer-lock').on('click', function(e) {
            e.stopPropagation();
            const targetId = $(this).closest('li').data('target');
            const el = $('#' + targetId);
            const isLocked = el.data('locked') == 1;
            el.data('locked', isLocked ? 0 : 1);
            el.toggleClass('layer-locked', !isLocked);

            if (!isLocked) {
                if (el.hasClass('ui-draggable')) el.draggable('destroy');
                if (el.hasClass('ui-resizable')) el.resizable('destroy');
            } else {
                makeInteractive(el);
            }
            refreshLayers();
        });

        list.find('.btn-layer-up').on('click', function(e) {
            e.stopPropagation();
            const targetId = $(this).closest('li').data('target');
            const el = $('#' + targetId);
            let z = parseInt(el.css('z-index'), 10) || 1;
            z += 2;
            el.css('z-index', z).data('z-index', z);
            refreshLayers();
            recordState();
        });

        list.find('.btn-layer-down').on('click', function(e) {
            e.stopPropagation();
            const targetId = $(this).closest('li').data('target');
            const el = $('#' + targetId);
            let z = parseInt(el.css('z-index'), 10) || 1;
            z = Math.max(1, z - 2);
            el.css('z-index', z).data('z-index', z);
            refreshLayers();
            recordState();
        });
    }

    function highlightLayerItem(id) {
        $('#wppoppop-layers-list li').removeClass('selected');
        $('#wppoppop-layers-list li[data-target="' + id + '"]').addClass('selected');
    }

    // Live In-Builder Sandbox Preview Compiler
    $('#wppoppop-btn-preview').on('click', function(e) {
        e.preventDefault();
        const modal = $('#wppoppop-live-preview-modal');
        const mount = $('#wppoppop-preview-stage-mount').empty();

        const previewBox = $('<div class="wppoppop-box"></div>').css({
            width: stage.width(),
            height: stage.height(),
            'border-radius': stage.css('border-radius'),
            'box-shadow': stage.css('box-shadow'),
            'background': stage.css('background'),
            position: 'relative',
            overflow: 'hidden'
        });

        const elementsClone = stage.clone();
        elementsClone.find('.canvas-element').each(function() {
            $(this).removeClass('selected ui-draggable-handle ui-resizable')
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

    // Master Save Action
    $('#wppoppop-btn-save').on('click', function(e) {
        e.preventDefault();
        const saveBtn = $(this);
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
                timer_mins: el.data('timer-mins') || 15,
                progress_pct: el.data('progress-pct') || 50,
                slider_min: el.data('slider-min') || 0,
                slider_max: el.data('slider-max') || 100,
                slider_val: el.data('slider-val') || 50,
                slider_prefix: el.data('slider-prefix') || '$',
                font_family: el.data('font-family') || 'Inherit',
                anim: el.data('anim') || 'none',
                anim_exit: el.data('anim-exit') || 'none',
                anim_loop: el.data('anim-loop') || 'none',
                anim_delay: el.data('anim-delay') || 0,
                anim_duration: el.data('anim-duration') || 500,
                border_radius: el.data('border-radius') || 0,
                opacity: el.data('opacity') || 1.0,
                padding: el.data('padding') || 0,
                hover_color: el.data('hover-color') || '#ffffff',
                hover_bg: el.data('hover-bg') || '#0284c7',
                icon_class: el.data('icon-class') || '',
                locked: el.data('locked') || 0,
                top: parseInt(el.css('top'), 10) || 0,
                left: parseInt(el.css('left'), 10) || 0,
                width: el.outerWidth() || el.width(),
                height: el.outerHeight() || el.height(),
                z_index: el.data('z-index') || 1,
                font_size: el.data('font-size') || 16,
                color: el.data('color') || '#222222',
                bg_color: el.data('bg-color') || '#00a32a',
                content: el.data('content') || ((el.data('type') === 'input' || el.data('type') === 'date') 
                    ? el.find('input').attr('placeholder') 
                    : (el.find('.content-render').length ? el.find('.content-render').html() : el.text().trim()))
            });
        });

        const payload = {
            meta: {
                title: $('#wppoppop-popup-title').val() || 'Untitled Popup',
                width: stage.width(),
                height: stage.height(),
                bg_color: '#ffffff'
            },
            box_styling: {
                radius: $('#box-border-radius').val() || 8,
                shadow_preset: $('#box-shadow-preset').val() || 'subtle',
                bg_type: $('#box-bg-type').val() || 'solid',
                solid_color: $('#box-bg-color').val() || '#ffffff',
                grad_c1: $('#box-grad-c1').val() || '#1e293b',
                grad_c2: $('#box-grad-c2').val() || '#0f172a',
                grad_angle: $('#box-grad-angle').val() || 135
            },
            styling: {
                backdrop_blur: parseInt($('#style-backdrop-blur').val(), 10) || 0,
                close_esc: $('#style-close-esc').is(':checked'),
                close_backdrop: $('#style-close-backdrop').is(':checked')
            },
            sound_fx: {
                enable: $('#sound-enable').is(':checked') ? 1 : 0
            },
            triggers: {
                on_load: $('#trig-load').is(':checked'),
                on_load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                on_exit: $('#trig-exit').is(':checked'),
                on_scroll: $('#trig-scroll').is(':checked') ? 50 : 0,
                on_idle: $('#trig-idle').is(':checked') ? 15 : 0,
                click_selector: $('#trig-click-selector').val() || '',
                on_adblock: $('#trig-adblock').length ? $('#trig-adblock').is(':checked') : false
            },
            conditional_logic: {
                if_field: $('#logic-if-field').val() || '',
                equals_val: $('#logic-equals-val').val() || '',
                target_layer: $('#logic-target-layer').val() || '',
                action: $('#logic-action').val() || 'show'
            },
            math: {
                expression: $('#math-expression').length ? $('#math-expression').val() : '',
                output_target: $('#math-output-target').length ? $('#math-output-target').val() : ''
            },
            sidetab: {
                enable: $('#sidetab-enable').is(':checked'),
                label: $('#sidetab-label').val() || 'Special Offer',
                position: $('#sidetab-position').val() || 'right',
                bg_color: $('#sidetab-bg').val() || '#2271b1'
            },
            payment: {
                enable: $('#pay-enable').is(':checked'),
                amount: parseFloat($('#pay-amount').val()) || 19.99,
                currency: $('#pay-currency').val() || 'USD',
                gateway: $('#pay-gateway').val() || 'Stripe'
            },
            downloads: {
                enable: $('#dl-enable').is(':checked'),
                file_url: $('#dl-file-url').val() || '',
                expiry_hours: parseInt($('#dl-expiry-hours').val(), 10) || 24
            },
            video: {
                enable: $('#video-enable').is(':checked'),
                mode: $('#video-trigger-mode').val() || 'ended'
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
            integrations: {
                enable_webhook: $('#int-enable-webhook').is(':checked'),
                webhook_url: $('#int-webhook-url').val() || ''
            },
            sms: {
                enable_sms: $('#sms-enable').is(':checked'),
                twilio_sid: $('#sms-twilio-sid').val() || '',
                twilio_token: $('#sms-twilio-token').val() || '',
                from_phone: $('#sms-from-phone').val() || '',
                to_phone: $('#sms-to-phone').val() || ''
            },
            targeting: {
                scope: $('#target-scope').val() || 'everywhere',
                category_slugs: $('#target-cat-slugs').val() || '',
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
                    alert(res.data.message || 'Popup saved successfully!');
                } else {
                    alert('Save failed: ' + (res.data ? res.data.message : 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                saveBtn.prop('disabled', false).text('Save Popup');
                alert('Save failed: ' + error);
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

                if (config.box_styling) {
                    $('#box-border-radius').val(config.box_styling.radius || 8);
                    $('#box-shadow-preset').val(config.box_styling.shadow_preset || 'subtle');
                    $('#box-bg-type').val(config.box_styling.bg_type || 'solid');
                    $('#box-bg-color').val(config.box_styling.solid_color || '#ffffff');
                    $('#box-grad-c1').val(config.box_styling.grad_c1 || '#1e293b');
                    $('#box-grad-c2').val(config.box_styling.grad_c2 || '#0f172a');
                    $('#box-grad-angle').val(config.box_styling.grad_angle || 135);
                    updateStageDecoration();
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
