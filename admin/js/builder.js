
jQuery(document).ready(function($) {
    'use strict';

    let activeElement = null;
    let currentScreen = 1;
    let zIndexCounter = 10;
    let pageCount = 2;
    const stage = $('#wppoppop-stage');

    // 1. Drawer Controls
    $('#wppoppop-btn-settings').on('click', function(e) {
        e.preventDefault();
        $('#wppoppop-inspector-drawer').removeClass('open');
        $('#wppoppop-settings-drawer').addClass('open');
        $('#wppoppop-drawer-backdrop').fadeIn(150);
    });

    $('.btn-close-drawer, #wppoppop-drawer-backdrop').on('click', function() {
        $('#wppoppop-settings-drawer').removeClass('open');
        $('#wppoppop-inspector-drawer').removeClass('open');
        $('#wppoppop-drawer-backdrop').fadeOut(150);
    });

    // Accordions
    $(document).on('click', '.accordion-header', function() {
        const item = $(this).closest('.accordion-item');
        item.toggleClass('active');
        item.find('.accordion-body').slideToggle(150);
    });

    // 2. Stage Dimensions & Styling Live Synchronization
    function updateStageStyles() {
        const w = parseInt($('#stage-width').val(), 10) || 620;
        const h = parseInt($('#stage-height').val(), 10) || 380;
        stage.css({ width: w + 'px', height: h + 'px' });

        const radius = parseInt($('#box-border-radius').val(), 10) || 4;
        stage.css('border-radius', radius + 'px');

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
            stage.css({
                'background-color': bgColor,
                'background-image': 'none'
            });
        }
    }
    $('#stage-width, #stage-height, #box-border-radius, #box-bg-color, #box-bg-image').on('input change', updateStageStyles);

    // 3. Screens / Pages Tab Strip
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

    // 4. Element Factory Creation Helper
    function createNewElement(type, posX, posY) {
        zIndexCounter += 5;
        const elementId = 'elem_' + Date.now();

        let w = 180, h = 40, content = 'New ' + type, fieldName = '';
        let opts = ['10% OFF', 'FREE SHIPPING', '25% OFF', '$5 REWARD'];
        let bgColor = '#0284c7', color = '#ffffff', fontSize = 16, radius = 4;

        if (type === 'box') { w = 240; h = 120; bgColor = '#f8fafc'; color = '#334155'; content = ''; }
        else if (type === 'text') { w = 220; h = 32; bgColor = 'transparent'; color = '#1e293b'; content = 'Headline Text'; fontSize = 20; }
        else if (type === 'image') { w = 160; h = 120; bgColor = '#e2e8f0'; content = 'https://via.placeholder.com/160x120'; }
        else if (type === 'video') { w = 260; h = 150; bgColor = '#0f172a'; color = '#38bdf8'; content = 'Video Player'; }
        else if (type === 'html') { w = 200; h = 60; bgColor = 'transparent'; color = '#0f172a'; content = '<p>Custom HTML snippet</p>'; }
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
        else if (type === 'list') { w = 180; h = 60; bgColor = 'transparent'; color = '#1e293b'; content = '• Feature 1\n• Feature 2'; }
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
            layer_name: type.toUpperCase(),
            field_name: fieldName,
            content: content,
            options: opts,
            font_family: 'Inherit',
            font_size: fontSize,
            border_radius: radius,
            color: color,
            bg_color: bgColor,
            opacity: 1.0,
            required: 0,
            z_index: zIndexCounter,
            locked: 0
        };

        const leftPos = (posX !== undefined) ? posX : 60;
        const topPos  = (posY !== undefined) ? posY : 60;

        const elem = $('<div class="canvas-element"></div>')
            .attr('id', elementId)
            .data(initialData)
            .css({
                top: topPos,
                left: leftPos,
                width: w,
                height: h,
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
    }

    // Ribbon Click Handler
    $('.ribbon-btn').on('click', function() {
        createNewElement($(this).data('type'));
    });

    // Ribbon Drag & Drop onto Stage
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
        if (type === 'wheel') return '<div style="width:100%;height:100%;border-radius:50%;border:2px dashed #475569;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;">Lucky Wheel</div>';
        if (type === 'scratch') return '<div style="width:100%;height:100%;background:linear-gradient(135deg,#94a3b8,#cbd5e1);display:flex;align-items:center;justify-content:center;font-weight:700;color:#1e293b;">Scratch Card</div>';
        if (type === 'countdown') return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;letter-spacing:2px;">' + (c || '15:00') + '</div>';
        if (type === 'progress') return '<div style="background:#22c55e;width:50%;height:100%;"></div>';
        if (type === 'signature') return '<div style="border:1px dashed #94a3b8;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:11px;">Sign Here</div>';
        if (type === 'rating') return '<div style="font-size:18px;letter-spacing:3px;">&#9733;&#9733;&#9733;&#9733;&#9733;</div>';
        if (type === 'close') return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:' + (d.font_size || 22) + 'px;cursor:pointer;">&times;</div>';
        if (type === 'button' || type === 'nextstep' || type === 'pay_btn') return '<button type="button" style="width:100%;height:100%;background:transparent;border:none;color:inherit;font-weight:700;cursor:pointer;font-size:' + (d.font_size || 14) + 'px;">' + c + '</button>';
        if (type === 'input') return '<input type="text" placeholder="' + c + '" disabled style="width:100%;height:100%;background:transparent;border:none;padding:0 8px;color:inherit;">';
        if (type === 'image') return '<img src="' + (c.startsWith('http') ? c : 'https://via.placeholder.com/160x120') + '" style="width:100%;height:100%;object-fit:cover;display:block;">';
        if (type === 'divider') return '<div style="width:100%;height:100%;"></div>';
        return '<div class="content-render" style="width:100%;height:100%;display:flex;align-items:center;font-size:' + (d.font_size || 16) + 'px;">' + c + '</div>';
    }

    function makeInteractive(elem) {
        elem.draggable({
            containment: '#wppoppop-stage',
            cancel: '.locked',
            stop: function() { selectElement(elem); }
        }).resizable({
            containment: '#wppoppop-stage',
            cancel: '.locked',
            stop: function() { selectElement(elem); }
        });

        elem.on('click', function(e) {
            e.stopPropagation();
            selectElement(elem);
        });
    }

    // 5. Element Selection & Inspector Drawer
    function selectElement(elem) {
        $('.canvas-element').removeClass('selected');
        elem.addClass('selected');
        activeElement = elem;

        const d = elem.data();
        $('#prop-layer-name').val(d.layer_name || d.type.toUpperCase());
        $('#prop-field-name').val(d.field_name || '');
        $('#prop-content').val(d.content || '');
        $('#prop-options').val(Array.isArray(d.options) ? d.options.join(', ') : (d.options || ''));
        $('#prop-font-family').val(d.font_family || 'Inherit');
        $('#prop-font-size').val(d.font_size || 16);
        $('#prop-border-radius').val(d.border_radius || 4);
        $('#prop-color').val(rgbToHex(elem.css('color')) || '#ffffff');
        $('#prop-bg-color').val(rgbToHex(elem.css('background-color')) || '#000000');
        $('#prop-opacity').val(d.opacity !== undefined ? d.opacity : 1.0);
        $('#prop-required').prop('checked', d.required == 1);

        if (['wheel', 'dropdown', 'radio', 'checkbox'].indexOf(d.type) !== -1) {
            $('#group-prop-options').show();
        } else {
            $('#group-prop-options').hide();
        }

        $('#wppoppop-settings-drawer').removeClass('open');
        $('#wppoppop-inspector-drawer').addClass('open');
        $('#wppoppop-drawer-backdrop').fadeIn(150);

        highlightLayer(elem.attr('id'));
    }

    function deselectElement() {
        $('.canvas-element').removeClass('selected');
        activeElement = null;
        $('#wppoppop-layers-list li').removeClass('selected');
    }
    stage.on('click', deselectElement);

    // Inspector Live Bindings
    $('#prop-layer-name').on('input', function() {
        if (activeElement) {
            activeElement.data('layer_name', $(this).val());
            refreshLayers();
        }
    });

    $('#prop-field-name').on('input', function() {
        if (activeElement) activeElement.data('field_name', $(this).val());
    });

    $('#prop-content').on('input', function() {
        if (!activeElement) return;
        const val = $(this).val();
        activeElement.data('content', val);
        activeElement.html(renderElementMarkup(activeElement.data('type'), activeElement.data()));
    });

    $('#prop-options').on('input', function() {
        if (activeElement) activeElement.data('options', $(this).val().split(',').map(s => s.trim()));
    });

    $('#prop-font-family').on('change', function() {
        if (activeElement) {
            const font = $(this).val();
            activeElement.css('font-family', font === 'Inherit' ? 'inherit' : font);
            activeElement.data('font_family', font);
        }
    });

    $('#prop-font-size').on('input', function() {
        if (activeElement) {
            const sz = parseInt($(this).val(), 10);
            activeElement.css('font-size', sz + 'px');
            activeElement.data('font_size', sz);
            activeElement.html(renderElementMarkup(activeElement.data('type'), activeElement.data()));
        }
    });

    $('#prop-border-radius').on('input', function() {
        if (activeElement) {
            const r = parseInt($(this).val(), 10);
            activeElement.css('border-radius', r + 'px');
            activeElement.data('border_radius', r);
        }
    });

    $('#prop-color').on('input', function() {
        if (activeElement) {
            const c = $(this).val();
            activeElement.css('color', c);
            activeElement.data('color', c);
        }
    });

    $('#prop-bg-color').on('input', function() {
        if (activeElement) {
            const bg = $(this).val();
            activeElement.css('background-color', bg);
            activeElement.data('bg_color', bg);
        }
    });

    $('#prop-opacity').on('input', function() {
        if (activeElement) {
            const op = parseFloat($(this).val());
            activeElement.css('opacity', op);
            activeElement.data('opacity', op);
        }
    });

    $('#prop-required').on('change', function() {
        if (activeElement) activeElement.data('required', $(this).is(':checked') ? 1 : 0);
    });

    $('#prop-duplicate-element').on('click', function() {
        if (!activeElement) return;
        zIndexCounter += 5;
        const clone = activeElement.clone();
        const cloneId = 'elem_' + Date.now();
        clone.attr('id', cloneId)
            .css({
                top: parseInt(activeElement.css('top'), 10) + 15,
                left: parseInt(activeElement.css('left'), 10) + 15,
                'z-index': zIndexCounter
            })
            .data(activeElement.data());
        clone.data('id', cloneId);
        clone.data('z_index', zIndexCounter);
        stage.append(clone);
        makeInteractive(clone);
        refreshLayers();
        selectElement(clone);
    });

    $('#prop-delete-element').on('click', function() {
        if (!activeElement) return;
        activeElement.remove();
        deselectElement();
        $('#wppoppop-inspector-drawer').removeClass('open');
        $('#wppoppop-drawer-backdrop').fadeOut(150);
        refreshLayers();
    });

    // 6. Floating Layers Management with Native HTML5 + jQuery UI Sortable Drag & Drop
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
                // Top item in list has highest z-index
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

        // Sort descending by z-index so highest appears on top
        elems.sort(function(a, b) {
            const zA = parseInt($(a).css('z-index'), 10) || parseInt($(a).data('z_index'), 10) || 1;
            const zB = parseInt($(b).css('z-index'), 10) || parseInt($(b).data('z_index'), 10) || 1;
            return zB - zA;
        });

        elems.forEach(function(domEl) {
            const el = $(domEl);
            const id = el.attr('id');
            const d = el.data();
            const name = d.layer_name || d.content || d.type.toUpperCase();
            const isLocked = d.locked == 1;
            const isHidden = el.is(':hidden');
            const isSelected = activeElement && activeElement.attr('id') === id;

            const li = $('<li data-target="' + id + '" class="layer-item' + (isSelected ? ' selected' : '') + '" draggable="true"></li>');
            li.html(
                '<span class="layer-drag-grip" title="Drag to reorder layer"><span class="dashicons dashicons-menu"></span></span>' +
                '<span class="layer-title" title="[' + d.type + '] ' + name + '">[' + d.type + '] ' + name + '</span>' +
                '<div class="layer-actions">' +
                    '<button type="button" class="btn-layer-up" title="Move Layer Up (Bring Forward)">&uarr;</button>' +
                    '<button type="button" class="btn-layer-down" title="Move Layer Down (Send Backward)">&darr;</button>' +
                    '<button type="button" class="btn-layer-lock ' + (isLocked ? 'active-action' : '') + '" title="Lock Dragging"><span class="dashicons ' + (isLocked ? 'dashicons-lock' : 'dashicons-unlock') + '"></span></button>' +
                    '<button type="button" class="btn-layer-eye ' + (isHidden ? 'active-action' : '') + '" title="Toggle Visibility"><span class="dashicons ' + (isHidden ? 'dashicons-hidden' : 'dashicons-visibility') + '"></span></button>' +
                '</div>'
            );
            list.append(li);
        });

        // Initialize jQuery UI sortable if loaded
        if ($.fn.sortable) {
            if (list.hasClass('ui-sortable')) {
                list.sortable('destroy');
            }
            list.sortable({
                items: 'li[data-target]',
                handle: '.layer-drag-grip, .layer-title',
                axis: 'y',
                cursor: 'grabbing',
                placeholder: 'layer-sortable-placeholder',
                forcePlaceholderSize: true,
                opacity: 0.85,
                start: function(e, ui) {
                    ui.placeholder.height(ui.item.outerHeight());
                },
                update: function() {
                    recomputeZIndicesFromList();
                }
            });
        }
    }

    // HTML5 Drag and Drop Reordering Handlers (Universal Fallback & Precision)
    $('#wppoppop-layers-list').on('dragstart', 'li.layer-item', function(e) {
        draggedItem = this;
        $(this).addClass('is-dragging');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        e.originalEvent.dataTransfer.setData('text/html', this.outerHTML);
    });

    $('#wppoppop-layers-list').on('dragover', 'li.layer-item', function(e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'move';
        if (this === draggedItem) return;

        const rect = this.getBoundingClientRect();
        const midY = rect.top + (rect.height / 2);
        if (e.originalEvent.clientY < midY) {
            $(this).addClass('drag-over-top').removeClass('drag-over-bottom');
        } else {
            $(this).addClass('drag-over-bottom').removeClass('drag-over-top');
        }
    });

    $('#wppoppop-layers-list').on('dragleave', 'li.layer-item', function() {
        $(this).removeClass('drag-over-top drag-over-bottom');
    });

    $('#wppoppop-layers-list').on('drop', 'li.layer-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!draggedItem || this === draggedItem) return;

        const isBefore = $(this).hasClass('drag-over-top');
        $(this).removeClass('drag-over-top drag-over-bottom');

        if (isBefore) {
            $(draggedItem).insertBefore(this);
        } else {
            $(draggedItem).insertAfter(this);
        }

        recomputeZIndicesFromList();
        const targetId = $(draggedItem).data('target');
        const targetEl = $('#' + targetId);
        if (targetEl.length) selectElement(targetEl);
    });

    $('#wppoppop-layers-list').on('dragend', 'li.layer-item', function() {
        $(this).removeClass('is-dragging');
        $('#wppoppop-layers-list li').removeClass('drag-over-top drag-over-bottom');
        draggedItem = null;
    });

    function highlightLayer(id) {
        $('#wppoppop-layers-list li').removeClass('selected');
        $('#wppoppop-layers-list li[data-target="' + id + '"]').addClass('selected');
    }

    // Layer Title Click to Select
    $('#wppoppop-layers-list').on('click', '.layer-title', function(e) {
        e.stopPropagation();
        const target = $('#' + $(this).closest('li').data('target'));
        if (target.length) selectElement(target);
    });

    // Move Layer UP Button (Higher z-index / Forward)
    $('#wppoppop-layers-list').on('click', '.btn-layer-up', function(e) {
        e.stopPropagation();
        const li = $(this).closest('li');
        const prevLi = li.prev('li[data-target]');
        if (prevLi.length) {
            li.insertBefore(prevLi);
            recomputeZIndicesFromList();
            const target = $('#' + li.data('target'));
            if (target.length) selectElement(target);
        }
    });

    // Move Layer DOWN Button (Lower z-index / Backward)
    $('#wppoppop-layers-list').on('click', '.btn-layer-down', function(e) {
        e.stopPropagation();
        const li = $(this).closest('li');
        const nextLi = li.next('li[data-target]');
        if (nextLi.length) {
            li.insertAfter(nextLi);
            recomputeZIndicesFromList();
            const target = $('#' + li.data('target'));
            if (target.length) selectElement(target);
        }
    });

    // Toggle Eye Visibility
    $('#wppoppop-layers-list').on('click', '.btn-layer-eye', function(e) {
        e.stopPropagation();
        const li = $(this).closest('li');
        const target = $('#' + li.data('target'));
        target.toggle();
        const isHidden = target.is(':hidden');
        $(this).toggleClass('active-action', isHidden);
        $(this).find('.dashicons').toggleClass('dashicons-visibility', !isHidden).toggleClass('dashicons-hidden', isHidden);
    });

    // Toggle Drag Lock
    $('#wppoppop-layers-list').on('click', '.btn-layer-lock', function(e) {
        e.stopPropagation();
        const li = $(this).closest('li');
        const target = $('#' + li.data('target'));
        const locked = target.data('locked') == 1 ? 0 : 1;
        target.data('locked', locked);
        target.toggleClass('locked', locked === 1);
        $(this).toggleClass('active-action', locked === 1);
        $(this).find('.dashicons').toggleClass('dashicons-lock', locked === 1).toggleClass('dashicons-unlock', locked === 0);
    });

    // 7. Embed Snippets Modal
    $('#wppoppop-btn-embed').on('click', function() {
        const uid = $('#wppoppop-popup-uid').val() || 'pop_sample';
        $('#embed-code-shortcode').val('[wppoppop uid="' + uid + '"]');
        $('#embed-code-button').val('[wppoppop_button uid="' + uid + '"]Click to Open[/wppoppop_button]');
        $('#embed-code-class').val('<a href="#" class="wppoppop-trigger-' + uid + '">Open Popup</a>');
        $('#wppoppop-embed-modal').fadeIn(150);
    });

    // 8. Live Interactive Preview Modal
    $('#wppoppop-btn-preview').on('click', function() {
        const mount = $('#wppoppop-preview-stage-mount').empty();
        const pStage = $('<div class="preview-stage-box"></div>').css({
            width: stage.width(),
            height: stage.height(),
            background: stage.css('background-color'),
            'background-image': stage.css('background-image'),
            'background-size': stage.css('background-size'),
            'background-position': stage.css('background-position'),
            'border-radius': stage.css('border-radius'),
            position: 'relative',
            boxShadow: '0 20px 40px rgba(0,0,0,0.5)'
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
        $('#wppoppop-live-preview-modal').fadeIn(150);
    });

    // Modals Close
    $('.btn-close-modal, .wppoppop-modal-backdrop').on('click', function(e) {
        if (e.target === this || $(this).hasClass('btn-close-modal')) {
            $('.wppoppop-modal-backdrop').fadeOut(150);
        }
    });

    // 9. Full Payload Serialization & Persistence
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
                layer_name: d.layer_name || d.type.toUpperCase(),
                field_name: d.field_name || '',
                content: d.content || '',
                options: d.options || [],
                font_family: d.font_family || 'Inherit',
                font_size: d.font_size || 16,
                border_radius: d.border_radius || 4,
                color: d.color || '#ffffff',
                bg_color: d.bg_color || '#000000',
                opacity: d.opacity !== undefined ? d.opacity : 1.0,
                required: d.required || 0,
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
                position_mode: $('#style-position-mode').val(),
                backdrop_blur: parseInt($('#style-backdrop-blur').val(), 10) || 5,
                border_radius: parseInt($('#box-border-radius').val(), 10) || 4,
                bg_color: $('#box-bg-color').val() || '#ffffff',
                bg_image: $('#box-bg-image').val() || '',
                close_esc: $('#style-close-esc').is(':checked'),
                close_backdrop: $('#style-close-backdrop').is(':checked')
            },
            triggers: {
                on_load: $('#trig-load').is(':checked'),
                on_load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                on_exit: $('#trig-exit').is(':checked'),
                on_scroll: $('#trig-scroll').is(':checked'),
                on_idle: $('#trig-idle').is(':checked'),
                on_mobile_back: $('#trig-mobile-back').is(':checked'),
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

    // 10. Load Existing Campaign or Initialize Starter Template
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
                        $('#style-position-mode').val(conf.styling.position_mode || 'modal');
                        $('#style-backdrop-blur').val(conf.styling.backdrop_blur || 5);
                        $('#box-border-radius').val(conf.styling.border_radius || 4);
                        $('#box-bg-color').val(conf.styling.bg_color || '#ffffff');
                        $('#box-bg-image').val(conf.styling.bg_image || '');
                        $('#style-close-esc').prop('checked', conf.styling.close_esc !== false);
                        $('#style-close-backdrop').prop('checked', conf.styling.close_backdrop !== false);
                    }
                    if (conf.triggers) {
                        $('#trig-load').prop('checked', !!conf.triggers.on_load);
                        $('#trig-load-delay').val(conf.triggers.on_load_delay || 0);
                        $('#trig-exit').prop('checked', !!conf.triggers.on_exit);
                        $('#trig-scroll').prop('checked', !!conf.triggers.on_scroll);
                        $('#trig-idle').prop('checked', !!conf.triggers.on_idle);
                        $('#trig-mobile-back').prop('checked', !!conf.triggers.on_mobile_back);
                    }
                    updateStageStyles();

                    if (Array.isArray(conf.elements) && conf.elements.length > 0) {
                        conf.elements.forEach(function(d) {
                            const elem = $('<div class="canvas-element"></div>')
                                .attr('id', d.id)
                                .data(d)
                                .css({
                                    top: d.top,
                                    left: d.left,
                                    width: d.width,
                                    height: d.height,
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
                        return;
                    }
                }
                initStarterElements();
            }).fail(initStarterElements);
        } else {
            initStarterElements();
        }
    }

    // Default Starter Canvas
    function initStarterElements() {
        if (stage.find('.canvas-element').length > 0) return;
        updateStageStyles();

        const starters = [
            { id: 'el_title', type: 'text', screen: 1, top: 40, left: 50, width: 520, height: 40, content: 'License Agreement', font_size: 24, color: '#1e293b', bg_color: 'transparent', z_index: 10 },
            { id: 'el_desc', type: 'text', screen: 1, top: 90, left: 50, width: 520, height: 120, content: 'Please read our terms and conditions before proceeding. By clicking Agree, you accept our standard end-user license agreement and privacy policy.', font_size: 15, color: '#475569', bg_color: 'transparent', z_index: 11 },
            { id: 'el_agree', type: 'nextstep', screen: 1, top: 250, left: 140, width: 140, height: 44, content: 'AGREE', font_size: 15, color: '#ffffff', bg_color: '#00a32a', border_radius: 6, z_index: 12 },
            { id: 'el_decline', type: 'close', screen: 1, top: 250, left: 320, width: 140, height: 44, content: 'DECLINE', font_size: 15, color: '#ffffff', bg_color: '#dc2626', border_radius: 6, z_index: 13 },
            { id: 'el_x', type: 'close', screen: 1, top: 15, left: 580, width: 28, height: 28, content: '&times;', font_size: 22, color: '#64748b', bg_color: 'transparent', z_index: 14 }
        ];

        starters.forEach(function(d) {
            d.layer_name = d.content.substring(0, 16);
            const elem = $('<div class="canvas-element"></div>')
                .attr('id', d.id)
                .data(d)
                .css({
                    top: d.top,
                    left: d.left,
                    width: d.width,
                    height: d.height,
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
    }

    function rgbToHex(rgb) {
        if (!rgb || rgb.indexOf('rgb') === -1) return rgb || '#000000';
        const parts = rgb.match(/\d+/g);
        if (!parts || parts.length < 3) return '#000000';
        return "#" + ((1 << 24) + (parseInt(parts[0]) << 16) + (parseInt(parts[1]) << 8) + parseInt(parts[2])).toString(16).slice(1);
    }

    loadPopupData();
});
