
jQuery(document).ready(function($) {
    'use strict';

    let activeElement = null;
    let currentScreen = 1;
    let zIndexCounter = 10;
    let pageCount = 2;
    const stage = $('#wppoppop-stage');
    const builderWrap = $('#wppoppop-builder-wrap');

    // 1. History Buffer (Undo / Redo)
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
                top: parseInt(el.css('top'), 10) || 0,
                left: parseInt(el.css('left'), 10) || 0,
                width: el.outerWidth(),
                height: el.outerHeight(),
                data: JSON.parse(JSON.stringify(el.data()))
            });
        });

        if (historyIndex < historyStack.length - 1) {
            historyStack.splice(historyIndex + 1);
        }
        historyStack.push(state);
        if (historyStack.length > 30) historyStack.shift();
        historyIndex = historyStack.length - 1;
    }

    function applyHistoryState(state) {
        stage.find('.canvas-element').remove();
        state.forEach(function(item) {
            const el = $('<div class="canvas-element"></div>')
                .attr('id', item.id)
                .data(item.data)
                .css({
                    top: item.top + 'px',
                    left: item.left + 'px',
                    width: item.width + 'px',
                    height: item.height + 'px',
                    'z-index': item.data.z_index || 10,
                    'border-radius': (item.data.border_radius || 4) + 'px',
                    opacity: item.data.opacity !== undefined ? item.data.opacity : 1,
                    'background-color': item.data.bg_color || '#000000',
                    color: item.data.color || '#ffffff',
                    display: (item.screen || 1) === currentScreen ? 'block' : 'none'
                })
                .html(renderElementMarkup(item.type, item.data));

            stage.append(el);
            makeInteractive(el);
        });
        deselectElement();
        refreshLayers();
    }

    $('#btn-undo').on('click', function() {
        if (historyIndex > 0) {
            historyIndex--;
            applyHistoryState(historyStack[historyIndex]);
        }
    });

    $('#btn-redo').on('click', function() {
        if (historyIndex < historyStack.length - 1) {
            historyIndex++;
            applyHistoryState(historyStack[historyIndex]);
        }
    });

    // Keyboard Shortcuts (Undo/Redo & Esc)
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
            e.preventDefault();
            $('#btn-undo').trigger('click');
        } else if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.shiftKey && e.key === 'Z'))) {
            e.preventDefault();
            $('#btn-redo').trigger('click');
        } else if (e.key === 'Escape') {
            closeInspectorPanel();
            $('#wppoppop-settings-drawer').removeClass('open');
            $('.wppoppop-modal-backdrop').fadeOut(150);
        }
    });

    // Alignment Buttons
    $('.btn-align').on('click', function() {
        if (!activeElement) return;
        const align = $(this).data('align');
        const sW = stage.width();
        const sH = stage.height();
        const eW = activeElement.outerWidth();
        const eH = activeElement.outerHeight();

        if (align === 'left') activeElement.css('left', '0px');
        if (align === 'center-h') activeElement.css('left', Math.round((sW - eW) / 2) + 'px');
        if (align === 'right') activeElement.css('left', (sW - eW) + 'px');
        if (align === 'top') activeElement.css('top', '0px');
        if (align === 'center-v') activeElement.css('top', Math.round((sH - eH) / 2) + 'px');
        if (align === 'bottom') activeElement.css('top', (sH - eH) + 'px');

        $('#prop-pos-top').val(parseInt(activeElement.css('top'), 10));
        $('#prop-pos-left').val(parseInt(activeElement.css('left'), 10));
        recordState();
    });

    // 2. Settings Drawer & Accordion Handlers
    $('#wppoppop-btn-settings').on('click', function(e) {
        e.preventDefault();
        $('#wppoppop-settings-drawer').toggleClass('open');
    });

    $('#btn-close-settings').on('click', function() {
        $('#wppoppop-settings-drawer').removeClass('open');
    });

    $(document).on('click', '.accordion-header', function() {
        const item = $(this).closest('.accordion-item');
        item.toggleClass('active');
        item.find('.accordion-body').slideToggle(150);
    });

    // 3. Inspector Tabs & Frame Pushing
    $('.inspector-tab-btn').on('click', function() {
        $('.inspector-tab-btn').removeClass('active');
        $('.inspector-tab-pane').removeClass('active');
        $(this).addClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    $('#btn-close-inspector').on('click', function(e) {
        e.preventDefault();
        closeInspectorPanel();
    });

    function openInspectorPanel() {
        builderWrap.addClass('panel-open');
    }

    function closeInspectorPanel() {
        builderWrap.removeClass('panel-open');
        deselectElement();
    }

    // 4. Stage Box Dimensions & Styling
    function updateStageStyles() {
        const w = parseInt($('#stage-width').val(), 10) || 620;
        const h = parseInt($('#stage-height').val(), 10) || 380;
        stage.css({ width: w + 'px', height: h + 'px' });

        const radius = parseInt($('#box-border-radius').val(), 10) || 4;
        stage.css('border-radius', radius + 'px');

        const shadow = $('#box-shadow').val() || 'deep';
        if (shadow === 'subtle') stage.css('box-shadow', '0 10px 25px rgba(0,0,0,0.15)');
        else if (shadow === 'glow') stage.css('box-shadow', '0 0 35px rgba(56,189,248,0.4)');
        else if (shadow === 'none') stage.css('box-shadow', 'none');
        else stage.css('box-shadow', '0 25px 50px -12px rgba(0, 0, 0, 0.7)');

        const fillType = $('#box-fill-type').val();
        if (fillType === 'gradient') {
            $('#group-box-gradient-controls').show();
            $('#group-box-solid-color').hide();
            const angle = $('#box-grad-angle').val() || 135;
            const c1 = $('#box-grad-c1').val() || '#1e293b';
            const c2 = $('#box-grad-c2').val() || '#0f172a';
            stage.css({ 'background': 'linear-gradient(' + angle + 'deg, ' + c1 + ', ' + c2 + ')' });
        } else {
            $('#group-box-gradient-controls').hide();
            $('#group-box-solid-color').show();
            const bgColor = $('#box-bg-color').val() || '#ffffff';
            const bgImg = $('#box-bg-image').val();
            if (bgImg) {
                stage.css({
                    'background-color': bgColor,
                    'background-image': 'url(' + bgImg + ')',
                    'background-size': 'cover',
                    'background-position': 'center'
                });
            } else {
                stage.css({ 'background-color': bgColor, 'background-image': 'none' });
            }
        }
    }
    $('#stage-width, #stage-height, #box-border-radius, #box-shadow, #box-fill-type, #box-bg-color, #box-grad-angle, #box-grad-c1, #box-grad-c2, #box-bg-image').on('input change', updateStageStyles);

    // 1-Click Theme Palettes
    $('.btn-theme-preset').on('click', function() {
        const theme = $(this).data('theme');
        $('#box-fill-type').val('gradient');
        if (theme === 'midnight') {
            $('#box-grad-angle').val(135); $('#box-grad-c1').val('#1e293b'); $('#box-grad-c2').val('#0f172a');
        } else if (theme === 'emerald') {
            $('#box-grad-angle').val(135); $('#box-grad-c1').val('#064e3b'); $('#box-grad-c2').val('#022c22');
        } else if (theme === 'sunset') {
            $('#box-grad-angle').val(135); $('#box-grad-c1').val('#881337'); $('#box-grad-c2').val('#4c0519');
        } else if (theme === 'neon') {
            $('#box-grad-angle').val(135); $('#box-grad-c1').val('#18181b'); $('#box-grad-c2').val('#09090b');
        } else if (theme === 'clean') {
            $('#box-grad-angle').val(135); $('#box-grad-c1').val('#ffffff'); $('#box-grad-c2').val('#f1f5f9');
        }
        updateStageStyles();
        recordState();
    });

    // 5. Screen Navigation
    $('.pages-tabs-list').on('click', '.btn-page-tab', function() {
        if ($(this).hasClass('btn-add-page')) {
            pageCount++;
            const newTab = $('<button type="button" class="btn-page-tab" data-screen="' + pageCount + '">Page ' + pageCount + '</button>');
            $('.btn-add-page').before(newTab);
            newTab.trigger('click');
            return;
        }

        $('.btn-page-tab').removeClass('active');
        $(this).addClass('active');
        currentScreen = parseInt($(this).data('screen'), 10);

        $('.canvas-element').each(function() {
            const scr = $(this).data('screen') || 1;
            $(this).toggle(scr === currentScreen);
        });

        deselectElement();
        refreshLayers();
    });

    // 6. Complete Element Factory
    function createNewElement(type, posX, posY) {
        zIndexCounter += 5;
        const elementId = 'elem_' + Date.now();

        let w = 180, h = 40, content = 'New ' + type, fieldName = '';
        let opts = ['10% OFF', 'FREE SHIPPING', '25% OFF', '$5 REWARD'];
        let bgColor = '#0284c7', color = '#ffffff', fontSize = 16, radius = 4;
        let imgUrl = 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?w=620';
        let videoUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

        if (type === 'box') { w = 240; h = 120; bgColor = '#f8fafc'; color = '#334155'; content = ''; }
        else if (type === 'text') { w = 220; h = 32; bgColor = 'transparent'; color = '#1e293b'; content = 'Headline Text'; fontSize = 20; }
        else if (type === 'image') { w = 400; h = 240; bgColor = 'transparent'; content = ''; }
        else if (type === 'video') { w = 280; h = 160; bgColor = '#0f172a'; color = '#38bdf8'; content = 'Video Player'; }
        else if (type === 'html') { w = 200; h = 60; bgColor = 'transparent'; color = '#0f172a'; content = '<p>Custom HTML code</p>'; }
        else if (type === 'close') { w = 32; h = 32; bgColor = 'transparent'; color = '#64748b'; content = '&times;'; fontSize = 24; }
        else if (type === 'nextstep') { w = 140; h = 38; bgColor = '#2563eb'; content = 'Next Step &rarr;'; }
        else if (type === 'divider') { w = 260; h = 2; bgColor = '#cbd5e1'; content = ''; }
        else if (type === 'link') { w = 120; h = 30; bgColor = 'transparent'; color = '#2563eb'; content = 'Learn More'; }
        else if (type === 'textarea') { w = 220; h = 60; bgColor = '#ffffff'; color = '#1e293b'; content = 'Enter message...'; fieldName = 'message'; }
        else if (type === 'input') { w = 220; h = 38; bgColor = '#ffffff'; color = '#1e293b'; content = 'Enter email...'; fieldName = 'email'; }
        else if (type === 'number') { w = 120; h = 38; bgColor = '#ffffff'; color = '#1e293b'; content = '1'; fieldName = 'quantity'; }
        else if (type === 'slider') { w = 200; h = 32; bgColor = '#e2e8f0'; content = '50'; fieldName = 'range'; }
        else if (type === 'dropdown') { w = 200; h = 38; bgColor = '#ffffff'; color = '#1e293b'; content = 'Select Option'; fieldName = 'dropdown'; }
        else if (type === 'checkbox') { w = 180; h = 30; bgColor = 'transparent'; color = '#1e293b'; content = 'Agree to terms'; fieldName = 'agree'; }
        else if (type === 'radio') { w = 180; h = 30; bgColor = 'transparent'; color = '#1e293b'; content = 'Option A'; fieldName = 'choice'; }
        else if (type === 'list') { w = 180; h = 60; bgColor = 'transparent'; color = '#1e293b'; content = '• Item 1\n• Item 2'; }
        else if (type === 'gallery') { w = 220; h = 70; bgColor = '#cbd5e1'; color = '#475569'; content = 'Gallery (3 items)'; }
        else if (type === 'coupon') { w = 140; h = 36; bgColor = '#fef08a'; color = '#854d0e'; content = 'SAVE20'; radius = 6; }
        else if (type === 'date') { w = 180; h = 38; bgColor = '#ffffff'; color = '#1e293b'; content = 'Select Date'; fieldName = 'date'; }
        else if (type === 'countdown') { w = 200; h = 42; bgColor = '#0f172a'; color = '#38bdf8'; content = '15 : 00'; }
        else if (type === 'file') { w = 200; h = 38; bgColor = '#f8fafc'; color = '#475569'; content = 'Choose File...'; fieldName = 'file'; }
        else if (type === 'rating') { w = 140; h = 30; bgColor = 'transparent'; color = '#f59e0b'; content = '&#9733;&#9733;&#9733;&#9733;&#9733;'; }
        else if (type === 'button') { w = 160; h = 40; bgColor = '#00a32a'; color = '#ffffff'; content = 'SUBMIT'; }
        else if (type === 'wheel') { w = 180; h = 180; bgColor = '#f8fafc'; color = '#0f172a'; content = 'Lucky Wheel'; fieldName = 'prize'; }
        else if (type === 'scratch') { w = 200; h = 100; bgColor = '#94a3b8'; color = '#0f172a'; content = 'Scratch Card'; fieldName = 'scratch_prize'; }
        else if (type === 'progress') { w = 220; h = 18; bgColor = '#e2e8f0'; content = '50'; }
        else if (type === 'signature') { w = 220; h = 80; bgColor = '#f8fafc'; color = '#64748b'; content = 'Sign Here'; fieldName = 'signature'; }
        else if (type === 'pay_btn') { w = 160; h = 42; bgColor = '#2563eb'; color = '#ffffff'; content = 'Pay $10.00'; }

        const initialData = {
            id: elementId,
            type: type,
            screen: currentScreen,
            layer_name: type === 'image' ? 'Image' : type.toUpperCase(),
            field_name: fieldName,
            content: content,
            options: opts,
            image_url: imgUrl,
            video_url: videoUrl,
            img_size: 'cover',
            img_pos_h: 'center',
            img_pos_v: 'center',
            img_repeat: 'no-repeat',
            slider_min: 0,
            slider_max: 100,
            slider_step: 1,
            countdown_minutes: 15,
            url: '',
            target_blank: 0,
            close_action: 'none',
            onclick: '',
            anim_appear: 'fade',
            anim_duration: 1000,
            anim_delay: 0,
            anim_disappear: 'fade',
            calc_formula: '',
            calc_target: '',
            goto_screen: 'none',
            font_family: 'Inherit',
            font_size: fontSize,
            border_radius: radius,
            color: color,
            bg_color: bgColor,
            opacity: 1.0,
            required: 0,
            error_msg: 'Please complete this field.',
            z_index: zIndexCounter,
            locked: 0
        };

        const leftPos = (posX !== undefined) ? posX : 60;
        const topPos  = (posY !== undefined) ? posY : 60;

        const elem = $('<div class="canvas-element"></div>')
            .attr('id', elementId)
            .data(initialData)
            .css({
                top: topPos + 'px',
                left: leftPos + 'px',
                width: w + 'px',
                height: h + 'px',
                'z-index': zIndexCounter,
                'border-radius': radius + 'px',
                'opacity': 1,
                'background-color': bgColor,
                color: color
            })
            .html(renderElementMarkup(type, initialData));

        stage.append(elem);
        makeInteractive(elem);
        refreshLayers();
        selectElement(elem);
        recordState();
    }

    $('.ribbon-btn').on('click', function() {
        createNewElement($(this).data('type'));
    });

    $('.ribbon-btn').attr('draggable', 'true').on('dragstart', function(e) {
        e.originalEvent.dataTransfer.setData('text/plain', $(this).data('type'));
        e.originalEvent.dataTransfer.effectAllowed = 'copy';
    });

    stage.on('dragover', function(e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'copy';
        $(this).addClass('stage-drop-hover');
    }).on('dragleave', function() {
        $(this).removeClass('stage-drop-hover');
    }).on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('stage-drop-hover');
        const type = e.originalEvent.dataTransfer.getData('text/plain');
        if (type) {
            const offset = stage.offset();
            const posX = Math.max(10, Math.round(e.originalEvent.pageX - offset.left - 50));
            const posY = Math.max(10, Math.round(e.originalEvent.pageY - offset.top - 20));
            createNewElement(type, posX, posY);
        }
    });

    function renderElementMarkup(type, d) {
        const c = d.content || '';
        if (type === 'image') {
            const url = d.image_url || 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?w=620';
            return '<div class="img-render-box" style="width:100%;height:100%;background-image:url(' + url + ');background-size:' + (d.img_size || 'cover') + ';background-position:' + (d.img_pos_h || 'center') + ' ' + (d.img_pos_v || 'center') + ';background-repeat:' + (d.img_repeat || 'no-repeat') + ';border-radius:' + (d.border_radius || 4) + 'px;"></div>';
        }
        if (type === 'video') return '<div style="width:100%;height:100%;background:#0f172a;color:#38bdf8;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;"><span class="dashicons dashicons-video-alt3" style="margin-right:4px;"></span> ' + (d.video_url ? 'Video Feed' : 'Video Player') + '</div>';
        if (type === 'wheel') return '<div style="width:100%;height:100%;border-radius:50%;border:2px dashed #475569;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;background:#f8fafc;color:#0f172a;">Lucky Wheel</div>';
        if (type === 'scratch') return '<div style="width:100%;height:100%;background:linear-gradient(135deg,#94a3b8,#cbd5e1);display:flex;align-items:center;justify-content:center;font-weight:700;color:#1e293b;border-radius:' + (d.border_radius || 4) + 'px;">' + (d.field_name || 'Scratch Card') + '</div>';
        if (type === 'countdown') return '<div style="width:100%;height:100%;background:#0f172a;color:#38bdf8;display:flex;align-items:center;justify-content:center;font-weight:700;letter-spacing:2px;border-radius:' + (d.border_radius || 4) + 'px;">' + (d.countdown_minutes ? d.countdown_minutes + ' : 00' : (c || '15:00')) + '</div>';
        if (type === 'progress') return '<div style="background:#e2e8f0;width:100%;height:100%;border-radius:10px;overflow:hidden;"><div style="background:#22c55e;width:' + (parseInt(c, 10) || 50) + '%;height:100%;"></div></div>';
        if (type === 'signature') return '<div style="border:1px dashed #94a3b8;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:11px;color:#64748b;background:#f8fafc;">Sign Here</div>';
        if (type === 'rating') return '<div style="font-size:18px;letter-spacing:3px;color:#f59e0b;">&#9733;&#9733;&#9733;&#9733;&#9733;</div>';
        if (type === 'close') return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:' + (d.font_size || 22) + 'px;cursor:pointer;">&times;</div>';
        if (type === 'button' || type === 'nextstep' || type === 'pay_btn') return '<button type="button" style="width:100%;height:100%;background:transparent;border:none;color:inherit;font-weight:700;cursor:pointer;font-size:' + (d.font_size || 14) + 'px;">' + (c || 'SUBMIT') + '</button>';
        if (type === 'input') return '<input type="text" placeholder="' + c + '" disabled style="width:100%;height:100%;background:transparent;border:none;padding:0 8px;color:inherit;">';
        if (type === 'slider') return '<div style="font-size:11px;padding:4px;"><input type="range" min="' + (d.slider_min||0) + '" max="' + (d.slider_max||100) + '" value="' + (c||50) + '" disabled style="width:100%;"></div>';
        if (type === 'divider') return '<div style="width:100%;height:100%;background:#cbd5e1;"></div>';
        return '<div class="content-render" style="width:100%;height:100%;display:flex;align-items:center;font-size:' + (d.font_size || 16) + 'px;">' + c + '</div>';
    }

    function makeInteractive(elem) {
        elem.draggable({
            containment: '#wppoppop-stage',
            cancel: '.locked',
            grid: $('#chk-grid-snap').is(':checked') ? [10, 10] : false,
            drag: function() {
                if (activeElement && activeElement.is(elem)) {
                    $('#prop-pos-top').val(parseInt(elem.css('top'), 10) || 0);
                    $('#prop-pos-left').val(parseInt(elem.css('left'), 10) || 0);
                }
            },
            stop: function() { selectElement(elem); recordState(); }
        }).resizable({
            containment: '#wppoppop-stage',
            cancel: '.locked',
            grid: $('#chk-grid-snap').is(':checked') ? [10, 10] : false,
            resize: function() {
                if (activeElement && activeElement.is(elem)) {
                    $('#prop-size-width').val(elem.outerWidth() || 0);
                    $('#prop-size-height').val(elem.outerHeight() || 0);
                }
            },
            stop: function() { selectElement(elem); recordState(); }
        });

        elem.on('click', function(e) {
            e.stopPropagation();
            selectElement(elem);
        });
    }

    // 7. Inspector Selection & Dynamic Binding
    function selectElement(elem) {
        $('.canvas-element').removeClass('selected');
        elem.addClass('selected');
        activeElement = elem;

        const d = elem.data();
        $('#prop-layer-name').val(d.layer_name || (d.type === 'image' ? 'Image' : d.type.toUpperCase()));
        $('#prop-pos-top').val(parseInt(elem.css('top'), 10) || 0);
        $('#prop-pos-left').val(parseInt(elem.css('left'), 10) || 0);
        $('#prop-size-width').val(elem.outerWidth() || 0);
        $('#prop-size-height').val(elem.outerHeight() || 0);

        const isImg = d.type === 'image';
        const isVid = d.type === 'video';
        const isSlider = d.type === 'slider';
        const isCountdown = d.type === 'countdown';

        $('#group-prop-image').toggle(isImg);
        $('#group-prop-video').toggle(isVid);
        $('#group-prop-slider').toggle(isSlider);
        $('#group-prop-countdown').toggle(isCountdown);
        $('#group-prop-content').toggle(!isImg && ['divider', 'box'].indexOf(d.type) === -1);
        $('#group-prop-field-name').toggle(['input', 'textarea', 'dropdown', 'radio', 'checkbox', 'date', 'number', 'wheel', 'signature', 'file', 'slider'].indexOf(d.type) !== -1);
        $('#group-prop-options').toggle(['wheel', 'dropdown', 'radio', 'checkbox'].indexOf(d.type) !== -1);

        if (isImg) {
            $('#prop-image-url').val(d.image_url || '');
            setActiveSeg('#group-img-size', d.img_size || 'cover');
            setActiveSeg('#group-img-pos-h', d.img_pos_h || 'center');
            setActiveSeg('#group-img-pos-v', d.img_pos_v || 'center');
            setActiveSeg('#group-img-repeat', d.img_repeat || 'no-repeat');
        }

        if (isVid) $('#prop-video-url').val(d.video_url || '');
        if (isSlider) {
            $('#prop-slider-min').val(d.slider_min !== undefined ? d.slider_min : 0);
            $('#prop-slider-max').val(d.slider_max !== undefined ? d.slider_max : 100);
            $('#prop-slider-step').val(d.slider_step !== undefined ? d.slider_step : 1);
        }
        if (isCountdown) $('#prop-countdown-minutes').val(d.countdown_minutes || 15);

        $('#prop-content').val(d.content || '');
        $('#prop-field-name').val(d.field_name || '');
        $('#prop-options').val(Array.isArray(d.options) ? d.options.join(', ') : (d.options || ''));
        $('#prop-url').val(d.url || '');
        $('#prop-target-blank').prop('checked', d.target_blank == 1);
        setActiveSeg('#group-prop-close-action', d.close_action || 'none');
        $('#prop-onclick').val(d.onclick || '');

        $('#prop-anim-appear').val(d.anim_appear || 'fade');
        $('#prop-anim-duration').val(d.anim_duration || 1000);
        $('#prop-anim-delay').val(d.anim_delay || 0);
        $('#prop-anim-disappear').val(d.anim_disappear || 'fade');

        $('#prop-calc-formula').val(d.calc_formula || '');
        $('#prop-calc-target').val(d.calc_target || '');
        $('#prop-goto-screen').val(d.goto_screen || 'none');

        $('#prop-font-family').val(d.font_family || 'Inherit');
        $('#prop-font-size').val(d.font_size || 16);
        $('#prop-border-radius').val(d.border_radius || 4);
        $('#prop-color').val(rgbToHex(elem.css('color')) || '#ffffff');
        $('#prop-bg-color').val(rgbToHex(elem.css('background-color')) || '#000000');
        $('#prop-opacity').val(d.opacity !== undefined ? d.opacity : 1.0);
        $('#prop-required').prop('checked', d.required == 1);
        $('#prop-error-msg').val(d.error_msg || 'Please complete this field.');

        openInspectorPanel();
        highlightLayer(elem.attr('id'));
    }

    function setActiveSeg(containerSelector, val) {
        $(containerSelector).find('.btn-seg').removeClass('active');
        $(containerSelector).find('.btn-seg[data-val="' + val + '"]').addClass('active');
    }

    function deselectElement() {
        $('.canvas-element').removeClass('selected');
        activeElement = null;
        $('#wppoppop-layers-list li').removeClass('selected');
    }

    stage.on('click', function(e) {
        if ($(e.target).is('#wppoppop-stage')) {
            closeInspectorPanel();
        }
    });

    // 8. Two-Way Coords & Dimension Inputs
    $('#prop-pos-top').on('input change', function() {
        if (activeElement) {
            activeElement.css('top', parseInt($(this).val(), 10) + 'px');
            recordState();
        }
    });

    $('#prop-pos-left').on('input change', function() {
        if (activeElement) {
            activeElement.css('left', parseInt($(this).val(), 10) + 'px');
            recordState();
        }
    });

    $('#prop-size-width').on('input change', function() {
        if (activeElement) {
            activeElement.css('width', parseInt($(this).val(), 10) + 'px');
            recordState();
        }
    });

    $('#prop-size-height').on('input change', function() {
        if (activeElement) {
            activeElement.css('height', parseInt($(this).val(), 10) + 'px');
            recordState();
        }
    });

    $('#prop-layer-name').on('input', function() {
        if (activeElement) {
            activeElement.data('layer_name', $(this).val());
            refreshLayers();
        }
    });

    $('#prop-content').on('input', function() {
        if (!activeElement) return;
        const val = $(this).val();
        activeElement.data('content', val);
        activeElement.html(renderElementMarkup(activeElement.data('type'), activeElement.data()));
    });

    $('#prop-field-name').on('input', function() {
        if (activeElement) activeElement.data('field_name', $(this).val());
    });

    $('#prop-options').on('input', function() {
        if (activeElement) activeElement.data('options', $(this).val().split(',').map(s => s.trim()));
    });

    $('#prop-video-url').on('input', function() {
        if (activeElement) {
            activeElement.data('video_url', $(this).val());
            activeElement.html(renderElementMarkup('video', activeElement.data()));
        }
    });

    $('#prop-slider-min, #prop-slider-max, #prop-slider-step').on('input', function() {
        if (activeElement) {
            activeElement.data('slider_min', parseInt($('#prop-slider-min').val(), 10) || 0);
            activeElement.data('slider_max', parseInt($('#prop-slider-max').val(), 10) || 100);
            activeElement.data('slider_step', parseInt($('#prop-slider-step').val(), 10) || 1);
            activeElement.html(renderElementMarkup('slider', activeElement.data()));
        }
    });

    $('#prop-countdown-minutes').on('input', function() {
        if (activeElement) {
            activeElement.data('countdown_minutes', parseInt($(this).val(), 10) || 15);
            activeElement.html(renderElementMarkup('countdown', activeElement.data()));
        }
    });

    $('#prop-url').on('input', function() { if (activeElement) activeElement.data('url', $(this).val()); });
    $('#prop-target-blank').on('change', function() { if (activeElement) activeElement.data('target_blank', $(this).is(':checked') ? 1 : 0); });
    $('#prop-onclick').on('input', function() { if (activeElement) activeElement.data('onclick', $(this).val()); });

    $('#prop-anim-appear').on('change', function() { if (activeElement) activeElement.data('anim_appear', $(this).val()); });
    $('#prop-anim-duration').on('input', function() { if (activeElement) activeElement.data('anim_duration', parseInt($(this).val(), 10)); });
    $('#prop-anim-delay').on('input', function() { if (activeElement) activeElement.data('anim_delay', parseInt($(this).val(), 10)); });
    $('#prop-anim-disappear').on('change', function() { if (activeElement) activeElement.data('anim_disappear', $(this).val()); });

    $('#prop-calc-formula').on('input', function() { if (activeElement) activeElement.data('calc_formula', $(this).val()); });
    $('#prop-calc-target').on('input', function() { if (activeElement) activeElement.data('calc_target', $(this).val()); });
    $('#prop-goto-screen').on('change', function() { if (activeElement) activeElement.data('goto_screen', $(this).val()); });

    // WordPress Media Library Modal Integration
    $('#btn-prop-media-picker').on('click', function(e) {
        e.preventDefault();
        if (typeof wp !== 'undefined' && wp.media) {
            const mediaFrame = wp.media({
                title: 'Select Image Asset',
                multiple: false,
                library: { type: 'image' }
            });
            mediaFrame.on('select', function() {
                const attachment = mediaFrame.state().get('selection').first().toJSON();
                $('#prop-image-url').val(attachment.url).trigger('input');
            });
            mediaFrame.open();
        } else {
            const promptUrl = prompt('Enter Image URL:', $('#prop-image-url').val() || 'https://');
            if (promptUrl) $('#prop-image-url').val(promptUrl).trigger('input');
        }
    });

    $('#prop-image-url').on('input', function() {
        if (!activeElement) return;
        const url = $(this).val();
        activeElement.data('image_url', url);
        activeElement.find('.img-render-box').css('background-image', 'url(' + url + ')');
    });

    $('.btn-group-segmented').on('click', '.btn-seg', function(e) {
        e.preventDefault();
        const parent = $(this).closest('.btn-group-segmented');
        parent.find('.btn-seg').removeClass('active');
        $(this).addClass('active');
        const val = $(this).data('val');
        const parentId = parent.attr('id');

        if (!activeElement) return;

        if (parentId === 'group-img-size') {
            activeElement.data('img_size', val);
            activeElement.find('.img-render-box').css('background-size', val);
        } else if (parentId === 'group-img-pos-h') {
            activeElement.data('img_pos_h', val);
            const v = activeElement.data('img_pos_v') || 'center';
            activeElement.find('.img-render-box').css('background-position', val + ' ' + v);
        } else if (parentId === 'group-img-pos-v') {
            activeElement.data('img_pos_v', val);
            const h = activeElement.data('img_pos_h') || 'center';
            activeElement.find('.img-render-box').css('background-position', h + ' ' + val);
        } else if (parentId === 'group-img-repeat') {
            activeElement.data('img_repeat', val);
            activeElement.find('.img-render-box').css('background-repeat', val);
        } else if (parentId === 'group-prop-close-action') {
            activeElement.data('close_action', val);
        }
    });

    // Style Properties
    $('#prop-font-family').on('change', function() {
        if (activeElement) {
            const font = $(this).val();
            activeElement.css('font-family', font === 'Inherit' ? 'inherit' : font).data('font_family', font);
        }
    });
    $('#prop-font-size').on('input', function() {
        if (activeElement) {
            const sz = parseInt($(this).val(), 10);
            activeElement.css('font-size', sz + 'px').data('font_size', sz);
            activeElement.html(renderElementMarkup(activeElement.data('type'), activeElement.data()));
        }
    });
    $('#prop-border-radius').on('input', function() {
        if (activeElement) {
            const r = parseInt($(this).val(), 10);
            activeElement.css('border-radius', r + 'px').data('border_radius', r);
            activeElement.find('.img-render-box').css('border-radius', r + 'px');
        }
    });
    $('#prop-color').on('input', function() {
        if (activeElement) activeElement.css('color', $(this).val()).data('color', $(this).val());
    });
    $('#prop-bg-color').on('input', function() {
        if (activeElement) activeElement.css('background-color', $(this).val()).data('bg_color', $(this).val());
    });
    $('#prop-opacity').on('input', function() {
        if (activeElement) activeElement.css('opacity', parseFloat($(this).val())).data('opacity', parseFloat($(this).val()));
    });
    $('#prop-required').on('change', function() {
        if (activeElement) activeElement.data('required', $(this).is(':checked') ? 1 : 0);
    });
    $('#prop-error-msg').on('input', function() {
        if (activeElement) activeElement.data('error_msg', $(this).val());
    });

    $('#prop-duplicate-element').on('click', function() {
        if (!activeElement) return;
        zIndexCounter += 5;
        const clone = activeElement.clone();
        const cloneId = 'elem_' + Date.now();
        clone.attr('id', cloneId)
            .css({
                top: (parseInt(activeElement.css('top'), 10) + 15) + 'px',
                left: (parseInt(activeElement.css('left'), 10) + 15) + 'px',
                'z-index': zIndexCounter
            })
            .data(JSON.parse(JSON.stringify(activeElement.data())));
        clone.data('id', cloneId);
        clone.data('z_index', zIndexCounter);
        stage.append(clone);
        makeInteractive(clone);
        refreshLayers();
        selectElement(clone);
        recordState();
    });

    $('#prop-delete-element').on('click', function() {
        if (!activeElement) return;
        activeElement.remove();
        closeInspectorPanel();
        refreshLayers();
        recordState();
    });

    // 9. Floating Layers Drag & Drop
    $('#wppoppop-floating-layers').draggable({
        handle: '.layers-header',
        containment: '.wppoppop-canvas-viewport'
    });

    function recomputeZIndicesFromList() {
        const items = $('#wppoppop-layers-list li[data-target]');
        const total = items.length;
        items.each(function(index) {
            const targetId = $(this).data('target');
            const el = $('#' + targetId);
            if (el.length) {
                const calculatedZ = (total - index) * 10;
                el.css('z-index', calculatedZ).data('z_index', calculatedZ);
            }
        });
    }

    let draggedItem = null;

    function refreshLayers() {
        const list = $('#wppoppop-layers-list').empty();
        let elems = stage.find('.canvas-element').filter(function() {
            return ($(this).data('screen') || 1) === currentScreen;
        }).toArray();

        if (elems.length === 0) {
            list.html('<li style="color:#64748b;justify-content:center;">No layers on this screen.</li>');
            return;
        }

        elems.sort(function(a, b) {
            const zA = parseInt($(a).css('z-index'), 10) || parseInt($(a).data('z_index'), 10) || 1;
            const zB = parseInt($(b).css('z-index'), 10) || parseInt($(b).data('z_index'), 10) || 1;
            return zB - zA;
        });

        elems.forEach(function(domEl) {
            const el = $(domEl);
            const id = el.attr('id');
            const d = el.data();
            const name = d.layer_name || (d.type === 'image' ? 'Image' : d.type.toUpperCase());
            const isLocked = d.locked == 1;
            const isHidden = el.is(':hidden');
            const isSelected = activeElement && activeElement.attr('id') === id;

            const li = $('<li data-target="' + id + '" class="layer-item' + (isSelected ? ' selected' : '') + '" draggable="true"></li>');
            li.html(
                '<span class="layer-drag-grip" title="Drag to reorder depth"><span class="dashicons dashicons-menu"></span></span>' +
                '<span class="layer-title" title="' + name + '">' + name + '</span>' +
                '<div class="layer-actions">' +
                    '<button type="button" class="btn-layer-lock ' + (isLocked ? 'active-action' : '') + '" title="Lock Dragging"><span class="dashicons ' + (isLocked ? 'dashicons-lock' : 'dashicons-unlock') + '"></span></button>' +
                    '<button type="button" class="btn-layer-eye ' + (isHidden ? 'active-action' : '') + '" title="Toggle Visibility"><span class="dashicons ' + (isHidden ? 'dashicons-hidden' : 'dashicons-visibility') + '"></span></button>' +
                '</div>'
            );
            list.append(li);
        });

        if ($.fn.sortable) {
            if (list.hasClass('ui-sortable')) list.sortable('destroy');
            list.sortable({
                items: 'li[data-target]',
                handle: '.layer-drag-grip, .layer-title',
                axis: 'y',
                cursor: 'grabbing',
                placeholder: 'layer-sortable-placeholder',
                forcePlaceholderSize: true,
                opacity: 0.85,
                update: function() {
                    recomputeZIndicesFromList();
                    recordState();
                }
            });
        }
    }

    $('#wppoppop-layers-list').on('dragstart', 'li.layer-item', function(e) {
        draggedItem = this;
        $(this).addClass('is-dragging');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
    }).on('dragover', 'li.layer-item', function(e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'move';
        if (this === draggedItem) return;
        const rect = this.getBoundingClientRect();
        if (e.originalEvent.clientY < rect.top + (rect.height / 2)) {
            $(this).addClass('drag-over-top').removeClass('drag-over-bottom');
        } else {
            $(this).addClass('drag-over-bottom').removeClass('drag-over-top');
        }
    }).on('dragleave', 'li.layer-item', function() {
        $(this).removeClass('drag-over-top drag-over-bottom');
    }).on('drop', 'li.layer-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!draggedItem || this === draggedItem) return;
        const isBefore = $(this).hasClass('drag-over-top');
        $(this).removeClass('drag-over-top drag-over-bottom');
        if (isBefore) $(draggedItem).insertBefore(this);
        else $(draggedItem).insertAfter(this);
        recomputeZIndicesFromList();
        const targetId = $(draggedItem).data('target');
        const targetEl = $('#' + targetId);
        if (targetEl.length) selectElement(targetEl);
        recordState();
    }).on('dragend', 'li.layer-item', function() {
        $(this).removeClass('is-dragging');
        $('#wppoppop-layers-list li').removeClass('drag-over-top drag-over-bottom');
        draggedItem = null;
    });

    function highlightLayer(id) {
        $('#wppoppop-layers-list li').removeClass('selected');
        $('#wppoppop-layers-list li[data-target="' + id + '"]').addClass('selected');
    }

    $('#wppoppop-layers-list').on('click', '.layer-title', function(e) {
        e.stopPropagation();
        const target = $('#' + $(this).closest('li').data('target'));
        if (target.length) selectElement(target);
    });

    $('#wppoppop-layers-list').on('click', '.btn-layer-eye', function(e) {
        e.stopPropagation();
        const li = $(this).closest('li');
        const target = $('#' + li.data('target'));
        target.toggle();
        const isHidden = target.is(':hidden');
        $(this).toggleClass('active-action', isHidden);
        $(this).find('.dashicons').toggleClass('dashicons-visibility', !isHidden).toggleClass('dashicons-hidden', isHidden);
        recordState();
    });

    $('#wppoppop-layers-list').on('click', '.btn-layer-lock', function(e) {
        e.stopPropagation();
        const li = $(this).closest('li');
        const target = $('#' + li.data('target'));
        const locked = target.data('locked') == 1 ? 0 : 1;
        target.data('locked', locked).toggleClass('locked', locked === 1);
        $(this).toggleClass('active-action', locked === 1);
        $(this).find('.dashicons').toggleClass('dashicons-lock', locked === 1).toggleClass('dashicons-unlock', locked === 0);
        recordState();
    });

    // 10. Modals (Embed & Preview) & Webhook/Twilio Tests
    $('#wppoppop-btn-embed').on('click', function(e) {
        e.preventDefault();
        const uid = $('#wppoppop-popup-uid').val() || 'pop_sample';
        $('#embed-code-shortcode').val('[wppoppop uid="' + uid + '"]');
        $('#embed-code-button').val('[wppoppop_button uid="' + uid + '"]Click to Open[/wppoppop_button]');
        $('#embed-code-class').val('<a href="#" class="wppoppop-trigger-' + uid + '">Open Popup</a>');
        $('#wppoppop-embed-modal').css('display', 'flex').hide().fadeIn(150);
    });

    $('.btn-copy-snippet').on('click', function() {
        const targetId = $(this).data('target');
        const input = $('#' + targetId);
        input.select();
        navigator.clipboard.writeText(input.val()).then(() => {
            const btn = $(this);
            btn.text('Copied!');
            setTimeout(() => btn.text('Copy'), 1500);
        });
    });

    $('#wppoppop-btn-preview').on('click', function(e) {
        e.preventDefault();
        const mount = $('#wppoppop-preview-stage-mount').empty();
        const pStage = $('<div class="preview-stage-box"></div>').css({
            width: stage.width() + 'px',
            height: stage.height() + 'px',
            background: stage.css('background'),
            'border-radius': stage.css('border-radius'),
            'box-shadow': stage.css('box-shadow'),
            position: 'relative',
            overflow: 'hidden'
        });

        stage.find('.canvas-element').filter(function() {
            return ($(this).data('screen') || 1) === currentScreen;
        }).each(function() {
            const el = $(this);
            const clone = $('<div class="preview-element"></div>').css({
                position: 'absolute',
                top: el.css('top'),
                left: el.css('left'),
                width: el.css('width'),
                height: el.css('height'),
                'border-radius': el.css('border-radius'),
                opacity: el.css('opacity'),
                'z-index': el.css('z-index'),
                background: el.css('background-color'),
                color: el.css('color')
            }).html(el.html());
            pStage.append(clone);
        });

        mount.append(pStage);
        $('#wppoppop-live-preview-modal').css('display', 'flex').hide().fadeIn(150);
    });

    $('.btn-close-modal, .wppoppop-modal-backdrop').on('click', function(e) {
        if (e.target === this || $(this).hasClass('btn-close-modal')) {
            $('.wppoppop-modal-backdrop').fadeOut(150);
        }
    });

    $('#btn-test-webhook').on('click', function() {
        const url = $('#mkt-webhook-url').val();
        if (!url) { alert('Please enter a Webhook URL.'); return; }
        const btn = $(this).text('Pinging...').prop('disabled', true);
        const ajaxUrl = (typeof wppoppop_vars !== 'undefined' && wppoppop_vars.ajax_url) ? wppoppop_vars.ajax_url : ajaxurl;
        const nonce = $('#wppoppop_builder_nonce_field').val() || (typeof wppoppop_vars !== 'undefined' ? wppoppop_vars.nonce : '');
        $.post(ajaxUrl, {
            action: 'wppoppop_test_webhook',
            nonce: nonce,
            url: url,
            secret: $('#mkt-webhook-secret').val()
        }).always(function() {
            btn.text('Test Webhook Ping').prop('disabled', false);
            alert('Webhook ping dispatched.');
        });
    });

    $('#btn-test-sms').on('click', function() {
        const sid = $('#sms-sid').val();
        const to = $('#sms-to').val();
        if (!sid || !to) { alert('Please configure Twilio Account SID and recipient phone.'); return; }
        const btn = $(this).text('Sending...').prop('disabled', true);
        const ajaxUrl = (typeof wppoppop_vars !== 'undefined' && wppoppop_vars.ajax_url) ? wppoppop_vars.ajax_url : ajaxurl;
        const nonce = $('#wppoppop_builder_nonce_field').val() || (typeof wppoppop_vars !== 'undefined' ? wppoppop_vars.nonce : '');
        $.post(ajaxUrl, {
            action: 'wppoppop_test_sms',
            nonce: nonce,
            sid: sid,
            token: $('#sms-token').val(),
            to: to
        }).always(function() {
            btn.text('Test SMS Ping').prop('disabled', false);
            alert('Test SMS dispatched.');
        });
    });

    // 11. Serialization & Persistence
    $('#wppoppop-btn-save').on('click', function(e) {
        e.preventDefault();
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Saving...');

        const elements = [];
        $('.canvas-element').each(function() {
            const el = $(this);
            const d = el.data();
            elements.push({
                id: el.attr('id'),
                type: d.type,
                screen: d.screen || 1,
                layer_name: d.layer_name || (d.type === 'image' ? 'Image' : d.type.toUpperCase()),
                field_name: d.field_name || '',
                content: d.content || '',
                options: d.options || [],
                image_url: d.image_url || '',
                video_url: d.video_url || '',
                img_size: d.img_size || 'cover',
                img_pos_h: d.img_pos_h || 'center',
                img_pos_v: d.img_pos_v || 'center',
                img_repeat: d.img_repeat || 'no-repeat',
                slider_min: d.slider_min !== undefined ? d.slider_min : 0,
                slider_max: d.slider_max !== undefined ? d.slider_max : 100,
                slider_step: d.slider_step !== undefined ? d.slider_step : 1,
                countdown_minutes: d.countdown_minutes || 15,
                url: d.url || '',
                target_blank: d.target_blank || 0,
                close_action: d.close_action || 'none',
                onclick: d.onclick || '',
                anim_appear: d.anim_appear || 'fade',
                anim_duration: d.anim_duration || 1000,
                anim_delay: d.anim_delay || 0,
                anim_disappear: d.anim_disappear || 'fade',
                calc_formula: d.calc_formula || '',
                calc_target: d.calc_target || '',
                goto_screen: d.goto_screen || 'none',
                font_family: d.font_family || 'Inherit',
                font_size: d.font_size || 16,
                border_radius: d.border_radius || 4,
                color: d.color || '#ffffff',
                bg_color: d.bg_color || '#000000',
                opacity: d.opacity !== undefined ? d.opacity : 1.0,
                required: d.required || 0,
                error_msg: d.error_msg || '',
                top: parseInt(el.css('top'), 10) || 0,
                left: parseInt(el.css('left'), 10) || 0,
                width: el.outerWidth(),
                height: el.outerHeight(),
                z_index: parseInt(el.css('z-index'), 10) || d.z_index || 1,
                locked: d.locked || 0
            });
        });

        const payload = {
            meta: {
                title: $('#wppoppop-popup-title').val() || 'yes-no-3',
                width: parseInt($('#stage-width').val(), 10) || 620,
                height: parseInt($('#stage-height').val(), 10) || 380,
                status: $('#wppoppop-popup-status').val() || 'publish'
            },
            styling: {
                fill_type: $('#box-fill-type').val() || 'solid',
                bg_color: $('#box-bg-color').val() || '#ffffff',
                grad_angle: parseInt($('#box-grad-angle').val(), 10) || 135,
                grad_c1: $('#box-grad-c1').val() || '#1e293b',
                grad_c2: $('#box-grad-c2').val() || '#0f172a',
                bg_image: $('#box-bg-image').val() || '',
                border_radius: parseInt($('#box-border-radius').val(), 10) || 4,
                box_shadow: $('#box-shadow').val() || 'deep',
                position_mode: $('#style-position-mode').val() || 'modal',
                backdrop_blur: parseInt($('#style-backdrop-blur').val(), 10) || 5,
                close_esc: $('#style-close-esc').is(':checked'),
                close_backdrop: $('#style-close-backdrop').is(':checked')
            },
            triggers: {
                on_load: $('#trig-load').is(':checked'),
                on_load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                on_exit: $('#trig-exit').is(':checked'),
                on_mobile_back: $('#trig-mobile-back').is(':checked'),
                on_scroll: $('#trig-scroll').is(':checked'),
                on_idle: $('#trig-idle').is(':checked'),
                on_selector: $('#trig-selector').val() || '',
                on_adblock: $('#trig-adblock').length ? $('#trig-adblock').is(':checked') : false
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
                auto_apply: $('#cpn-auto-apply').is(':checked')
            },
            sidetabs: {
                enable: $('#tab-enable').is(':checked'),
                text: $('#tab-text').val() || 'Special Offer',
                pos: $('#tab-pos').val() || 'right'
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
                to: $('#sms-to').val() || ''
            },
            targeting: {
                auth_mode: $('#target-auth-mode').val() || 'all',
                scope: $('#target-scope').val() || 'everywhere',
                geo_mode: $('#target-geo-mode').val() || 'all'
            },
            cookies: {
                freq_mode: $('#freq-mode').val() || 'everytime',
                hide_submitted: $('#freq-hide-submitted').is(':checked')
            },
            customcode: {
                custom_css: $('#code-custom-css').val() || '',
                custom_js: $('#code-custom-js').val() || ''
            },
            elements: elements
        };

        const nonce = $('#wppoppop_builder_nonce_field').val() || (typeof wppoppop_vars !== 'undefined' ? wppoppop_vars.nonce : '');
        const ajaxUrl = (typeof wppoppop_vars !== 'undefined' && wppoppop_vars.ajax_url) ? wppoppop_vars.ajax_url : ajaxurl;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'wppoppop_save_popup',
                nonce: nonce,
                uid: $('#wppoppop-popup-uid').val(),
                title: $('#wppoppop-popup-title').val(),
                data: JSON.stringify(payload)
            },
            success: function(res) {
                btn.prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> Save');
                if (res.success) {
                    $('#wppoppop-popup-uid').val(res.data.uid);
                    if (window.history && window.history.replaceState) {
                        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=wppoppop-builder&uid=' + res.data.uid;
                        window.history.replaceState({ path: newUrl }, '', newUrl);
                    }
                    alert(res.data.message || 'Popup configuration saved successfully!');
                } else {
                    alert('Save failed: ' + (res.data ? res.data.message : 'Unknown rejection'));
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> Save');
                alert('Save failed (HTTP ' + xhr.status + ')');
            }
        });
    });

    // 12. Load Saved Popup Configuration
    function loadPopupData() {
        const uid = $('#wppoppop-popup-uid').val();
        const ajaxUrl = (typeof wppoppop_vars !== 'undefined' && wppoppop_vars.ajax_url) ? wppoppop_vars.ajax_url : ajaxurl;
        const nonce = $('#wppoppop_builder_nonce_field').val() || (typeof wppoppop_vars !== 'undefined' ? wppoppop_vars.nonce : '');

        if (uid) {
            $.get(ajaxUrl, {
                action: 'wppoppop_load_popup',
                nonce: nonce,
                uid: uid
            }, function(res) {
                if (res.success && res.data) {
                    $('#wppoppop-popup-title').val(res.data.title);
                    let conf = {};
                    try { conf = JSON.parse(res.data.data); } catch(e) {}

                    if (conf.meta) {
                        if (conf.meta.width) $('#stage-width').val(conf.meta.width);
                        if (conf.meta.height) $('#stage-height').val(conf.meta.height);
                    }
                    if (conf.styling) {
                        $('#box-fill-type').val(conf.styling.fill_type || 'solid');
                        $('#box-bg-color').val(conf.styling.bg_color || '#ffffff');
                        $('#box-grad-angle').val(conf.styling.grad_angle || 135);
                        $('#box-grad-c1').val(conf.styling.grad_c1 || '#1e293b');
                        $('#box-grad-c2').val(conf.styling.grad_c2 || '#0f172a');
                        $('#box-bg-image').val(conf.styling.bg_image || '');
                        $('#box-border-radius').val(conf.styling.border_radius || 4);
                        $('#box-shadow').val(conf.styling.box_shadow || 'deep');
                        $('#style-position-mode').val(conf.styling.position_mode || 'modal');
                        $('#style-backdrop-blur').val(conf.styling.backdrop_blur || 5);
                        $('#style-close-esc').prop('checked', conf.styling.close_esc !== false);
                        $('#style-close-backdrop').prop('checked', conf.styling.close_backdrop !== false);
                    }
                    if (conf.triggers) {
                        $('#trig-load').prop('checked', !!conf.triggers.on_load);
                        $('#trig-load-delay').val(conf.triggers.on_load_delay || 0);
                        $('#trig-exit').prop('checked', !!conf.triggers.on_exit);
                        $('#trig-mobile-back').prop('checked', conf.triggers.on_mobile_back !== false);
                        $('#trig-scroll').prop('checked', !!conf.triggers.on_scroll);
                        $('#trig-idle').prop('checked', !!conf.triggers.on_idle);
                        $('#trig-selector').val(conf.triggers.on_selector || '');
                    }
                    if (conf.marketing) {
                        $('#mkt-webhook-url').val(conf.marketing.webhook_url || '');
                        $('#mkt-webhook-secret').val(conf.marketing.webhook_secret || '');
                    }
                    if (conf.twilio) {
                        $('#sms-sid').val(conf.twilio.sid || '');
                        $('#sms-token').val(conf.twilio.token || '');
                        $('#sms-to').val(conf.twilio.to || '');
                    }
                    updateStageStyles();

                    if (Array.isArray(conf.elements) && conf.elements.length > 0) {
                        conf.elements.forEach(function(d) {
                            const elem = $('<div class="canvas-element"></div>')
                                .attr('id', d.id)
                                .data(d)
                                .css({
                                    top: d.top + 'px',
                                    left: d.left + 'px',
                                    width: d.width + 'px',
                                    height: d.height + 'px',
                                    'z-index': d.z_index || 1,
                                    'border-radius': (d.border_radius || 4) + 'px',
                                    opacity: d.opacity !== undefined ? d.opacity : 1.0,
                                    'background-color': d.bg_color || '#000000',
                                    color: d.color || '#ffffff',
                                    display: (d.screen || 1) === currentScreen ? 'block' : 'none'
                                })
                                .html(renderElementMarkup(d.type, d));

                            stage.append(elem);
                            makeInteractive(elem);
                        });
                        refreshLayers();
                        recordState();
                        return;
                    }
                }
                initStarterElements();
            }).fail(initStarterElements);
        } else {
            initStarterElements();
        }
    }

    function initStarterElements() {
        if (stage.find('.canvas-element').length > 0) return;
        updateStageStyles();

        const starters = [
            { id: 'el_img_main', type: 'image', screen: 1, top: 0, left: 0, width: 620, height: 380, layer_name: 'Image', image_url: 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?w=620', img_size: 'cover', img_pos_h: 'center', img_pos_v: 'center', img_repeat: 'no-repeat', z_index: 5 },
            { id: 'el_box_card', type: 'box', screen: 1, top: 30, left: 30, width: 560, height: 320, layer_name: 'Rectangle / Square', bg_color: '#ffffff', border_radius: 8, opacity: 0.95, z_index: 8 },
            { id: 'el_title', type: 'text', screen: 1, top: 50, left: 60, width: 500, height: 36, content: 'License Agreement', layer_name: 'Header', font_size: 22, color: '#1e293b', bg_color: 'transparent', z_index: 10 },
            { id: 'el_desc', type: 'text', screen: 1, top: 95, left: 60, width: 500, height: 110, content: 'Please read our terms and conditions before proceeding. By clicking Agree, you accept our standard end-user license agreement and privacy policy.', layer_name: 'Description', font_size: 14, color: '#475569', bg_color: 'transparent', z_index: 11 },
            { id: 'el_agree', type: 'nextstep', screen: 1, top: 230, left: 140, width: 140, height: 42, content: 'AGREE', layer_name: 'AGREE', font_size: 15, color: '#ffffff', bg_color: '#00a32a', border_radius: 6, z_index: 12 },
            { id: 'el_decline', type: 'close', screen: 1, top: 230, left: 320, width: 140, height: 42, content: 'DECLINE', layer_name: 'DECLINE', font_size: 15, color: '#ffffff', bg_color: '#dc2626', border_radius: 6, z_index: 13 }
        ];

        starters.forEach(function(d) {
            const elem = $('<div class="canvas-element"></div>')
                .attr('id', d.id)
                .data(d)
                .css({
                    top: d.top + 'px',
                    left: d.left + 'px',
                    width: d.width + 'px',
                    height: d.height + 'px',
                    'z-index': d.z_index,
                    'border-radius': (d.border_radius || 4) + 'px',
                    'background-color': d.bg_color,
                    color: d.color
                })
                .html(renderElementMarkup(d.type, d));

            stage.append(elem);
            makeInteractive(elem);
        });

        refreshLayers();
        recordState();
    }

    function rgbToHex(rgb) {
        if (!rgb || rgb.indexOf('rgb') === -1) return rgb || '#000000';
        const parts = rgb.match(/\d+/g);
        if (!parts || parts.length < 3) return '#000000';
        return "#" + ((1 << 24) + (parseInt(parts[0]) << 16) + (parseInt(parts[1]) << 8) + parseInt(parts[2])).toString(16).slice(1);
    }

    loadPopupData();
});
