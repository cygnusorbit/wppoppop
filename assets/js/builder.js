(function($) {
    'use strict';

    $(document).ready(function() {
        const cfg = window.WPPopPopBuilderConfig || {
            popupId: 0,
            popupTitle: 'popup-new',
            popupStatus: 'publish',
            canvasConfig: { width: 640, height: 440, bgColor: '#ffffff', borderRadius: 8 },
            layers: []
        };

        let layers = Array.isArray(cfg.layers) ? cfg.layers : [];
        let canvasConfig = cfg.canvasConfig || { width: 640, height: 440, bgColor: '#ffffff', borderRadius: 8 };
        let selectedIndex = -1;
        let isDraggingNode = false;
        let isResizingNode = false;
        let isResizingCanvas = false;
        let dragOffset = { x: 0, y: 0 };
        let resizeNodeStart = { x: 0, y: 0, w: 0, h: 0 };
        let resizeCanvasStart = { x: 0, y: 0, w: 0, h: 0 };

        function updateCanvasSurface() {
            $('#wppoppop-canvas-boundary').css({
                width: canvasConfig.width + 'px',
                height: canvasConfig.height + 'px'
            });
            $('#wppoppop-canvas-surface').css({
                backgroundColor: canvasConfig.bgColor || '#ffffff',
                borderRadius: (canvasConfig.borderRadius || 8) + 'px'
            });
            $('#wppoppop-canvas-dim-display').html(canvasConfig.width + ' &times; ' + canvasConfig.height);
            $('#modal-canvas-w').val(canvasConfig.width);
            $('#modal-canvas-h').val(canvasConfig.height);
            $('#modal-canvas-radius').val(canvasConfig.borderRadius || 8);
        }

        function renderLayers() {
            const $surface = $('#wppoppop-canvas-surface').empty();
            const $stack = $('#wppoppop-layers-stack-list').empty();

            if (layers.length === 0) {
                $stack.append('<li class="wppoppop-layer-empty">No elements on this page. Click an icon on the toolbar to add one.</li>');
            }

            layers.forEach(function(l, idx) {
                // 1. Render on Canvas Surface
                const $node = $('<div></div>')
                    .addClass('wppoppop-layer-node')
                    .attr('data-index', idx)
                    .css({
                        left: l.x + 'px',
                        top: l.y + 'px',
                        width: l.w + 'px',
                        height: l.h + 'px',
                        zIndex: l.zIndex || (idx + 1),
                        color: l.color || '#1e293b',
                        backgroundColor: l.bgColor || 'transparent',
                        fontSize: (l.fontSize || 14) + 'px',
                        fontFamily: l.fontFamily || 'inherit',
                        borderRadius: (l.borderRadius || 0) + 'px'
                    });

                if (idx === selectedIndex) {
                    $node.addClass('selected');
                }

                // Render inner node content based on element type
                switch (l.type) {
                    case 'text':
                        $node.html(l.content || 'Heading Text');
                        break;
                    case 'input':
                    case 'email':
                        $node.append($('<input type="text" readonly />')
                            .attr('placeholder', l.placeholder || (l.type === 'email' ? 'Enter email address...' : 'Enter text...'))
                            .css({ width: '100%', height: '100%', border: 'none', background: 'transparent', padding: '0 8px', pointerEvents: 'none' }));
                        break;
                    case 'textarea':
                        $node.append($('<textarea readonly></textarea>')
                            .attr('placeholder', l.placeholder || 'Enter your message...')
                            .css({ width: '100%', height: '100%', border: 'none', background: 'transparent', padding: '6px 8px', pointerEvents: 'none', resize: 'none' }));
                        break;
                    case 'submit':
                        $node.append($('<button type="button"></button>')
                            .text(l.content || 'Submit')
                            .css({ width: '100%', height: '100%', border: 'none', background: 'transparent', color: 'inherit', fontWeight: 'bold', pointerEvents: 'none' }));
                        break;
                    case 'image':
                        $node.append($('<img />')
                            .attr('src', l.imageSrc || 'https://via.placeholder.com/300x150?text=Upload+Image')
                            .css({ width: '100%', height: '100%', objectFit: 'cover', pointerEvents: 'none' }));
                        break;
                    case 'dropdown':
                        $node.append($('<select disabled><option>' + (l.content || 'Choose option...') + '</option></select>')
                            .css({ width: '100%', height: '100%', pointerEvents: 'none' }));
                        break;
                    case 'checkbox':
                        $node.append($('<label style="display:flex;align-items:center;gap:6px;width:100%;height:100%;pointer-events:none;"><input type="checkbox" checked /> <span>' + (l.content || 'Accept terms') + '</span></label>'));
                        break;
                    case 'calendar':
                        $node.append($('<input type="date" disabled style="width:100%;height:100%;border:none;background:transparent;padding:0 8px;pointer-events:none;" />'));
                        break;
                    case 'clock':
                        $node.append($('<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1e293b;color:#fff;font-weight:bold;border-radius:4px;">14 : 59</div>'));
                        break;
                    case 'signature':
                        $node.append($('<div style="width:100%;height:100%;border:1px dashed #94a3b8;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:12px;">✍ Signature Pad</div>'));
                        break;
                    case 'rangeslider':
                        $node.append($('<input type="range" disabled style="width:100%;pointer-events:none;" />'));
                        break;
                    case 'wheel':
                        $node.append($('<div style="width:100%;height:100%;border-radius:50%;background:radial-gradient(circle, #b5295c 35%, #1e293b 85%);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:12px;">🎡 SPIN</div>'));
                        break;
                    case 'calc':
                        $node.append($('<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding:0 12px;background:#f8fafc;font-weight:bold;"><span>Total:</span><span style="color:#b5295c;">$0.00</span></div>'));
                        break;
                    case 'box':
                        break;
                }

                $node.append($('<div></div>').addClass('wppoppop-node-resizer'));
                $surface.append($node);

                // 2. Render on LAYERS Stack List
                const $item = $('<li></li>')
                    .addClass('wppoppop-layer-item')
                    .attr('data-index', idx)
                    .text((l.content || l.type).substring(0, 18));

                if (idx === selectedIndex) {
                    $item.addClass('active');
                }

                const $actions = $('<div></div>').addClass('wppoppop-layer-item-actions');
                $actions.append($('<span class="dashicons dashicons-arrow-up-alt2 wppoppop-layer-up" title="Move Up"></span>'));
                $actions.append($('<span class="dashicons dashicons-arrow-down-alt2 wppoppop-layer-down" title="Move Down"></span>'));
                $actions.append($('<span class="dashicons dashicons-trash wppoppop-layer-del" title="Delete"></span>'));
                $item.append($actions);

                $stack.prepend($item);
            });
        }

        function selectLayer(idx) {
            selectedIndex = idx;
            renderLayers();

            if (idx >= 0 && idx < layers.length) {
                const l = layers[idx];
                $('#wppoppop-properties-panel').show();
                $('#wppoppop-properties-header-title').text('PROPERTIES: ' + l.type.toUpperCase());

                $('#prop-input-content').val(l.content || '');
                $('#prop-input-placeholder').val(l.placeholder || '');
                $('#prop-input-slug').val(l.fieldName || '');
                $('#prop-input-x').val(l.x);
                $('#prop-input-y').val(l.y);
                $('#prop-input-w').val(l.w);
                $('#prop-input-h').val(l.h);
                $('#prop-input-fontsize').val(l.fontSize || 14);
                $('#prop-input-radius').val(l.borderRadius || 0);
                $('#prop-input-fontfamily').val(l.fontFamily || "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif");
                $('#prop-color-text').val(l.color || '#1e293b');
                $('#prop-color-bg').val(l.bgColor || 'transparent');
                $('#prop-input-imgurl').val(l.imageSrc || '');

                $('#group-field-placeholder').toggle(l.type === 'input' || l.type === 'email' || l.type === 'textarea');
                $('#group-field-slug').toggle(l.type === 'input' || l.type === 'email' || l.type === 'textarea' || l.type === 'dropdown');
                $('#group-field-image').toggle(l.type === 'image');
            } else {
                $('#wppoppop-properties-panel').hide();
            }
        }

        // Add Layer from Ribbon
        $('.wppoppop-tool-item').on('click', function(e) {
            e.preventDefault();
            const type = $(this).data('type');
            const newLayer = {
                type: type,
                x: Math.round(canvasConfig.width / 2) - 100,
                y: Math.round(canvasConfig.height / 2) - 25,
                w: 200,
                h: 46,
                zIndex: layers.length + 1,
                content: '',
                placeholder: '',
                fieldName: '',
                color: '#1e293b',
                bgColor: 'transparent',
                borderRadius: 4,
                fontSize: 14
            };

            if (type === 'text') {
                newLayer.content = 'Special Offer Headline';
                newLayer.fontSize = 22;
                newLayer.w = 280;
                newLayer.h = 38;
            } else if (type === 'input' || type === 'email') {
                newLayer.placeholder = type === 'email' ? 'Enter your email...' : 'Enter your name...';
                newLayer.fieldName = type === 'email' ? 'email' : 'name';
                newLayer.bgColor = '#ffffff';
                newLayer.borderRadius = 6;
                newLayer.w = 280;
            } else if (type === 'submit') {
                newLayer.content = 'Claim Discount';
                newLayer.bgColor = '#b5295c';
                newLayer.color = '#ffffff';
                newLayer.borderRadius = 6;
                newLayer.w = 280;
            } else if (type === 'image') {
                newLayer.w = 260;
                newLayer.h = 160;
                newLayer.imageSrc = 'https://via.placeholder.com/300x150?text=Upload+Image';
            } else if (type === 'box') {
                newLayer.w = 320;
                newLayer.h = 200;
                newLayer.bgColor = '#f8fafc';
            } else if (type === 'wheel') {
                newLayer.w = 240;
                newLayer.h = 240;
            }

            layers.push(newLayer);
            selectLayer(layers.length - 1);
        });

        // Layer Node Dragging on Canvas Surface
        $(document).on('mousedown', '.wppoppop-layer-node', function(e) {
            if ($(e.target).hasClass('wppoppop-node-resizer')) return;
            const idx = parseInt($(this).data('index'), 10);
            selectLayer(idx);

            isDraggingNode = true;
            const surfaceOffset = $('#wppoppop-canvas-surface').offset();
            dragOffset.x = e.pageX - surfaceOffset.left - layers[idx].x;
            dragOffset.y = e.pageY - surfaceOffset.top - layers[idx].y;
            e.preventDefault();
        });

        // Layer Node Resizing
        $(document).on('mousedown', '.wppoppop-node-resizer', function(e) {
            isResizingNode = true;
            resizeNodeStart.x = e.pageX;
            resizeNodeStart.y = e.pageY;
            resizeNodeStart.w = layers[selectedIndex].w;
            resizeNodeStart.h = layers[selectedIndex].h;
            e.stopPropagation();
            e.preventDefault();
        });

        // Canvas Boundary Resizing
        $('#wppoppop-canvas-resizer').on('mousedown', function(e) {
            isResizingCanvas = true;
            resizeCanvasStart.x = e.pageX;
            resizeCanvasStart.y = e.pageY;
            resizeCanvasStart.w = canvasConfig.width;
            resizeCanvasStart.h = canvasConfig.height;
            e.preventDefault();
        });

        $(document).on('mousemove', function(e) {
            if (isDraggingNode && selectedIndex >= 0) {
                const surfaceOffset = $('#wppoppop-canvas-surface').offset();
                let nx = Math.round(e.pageX - surfaceOffset.left - dragOffset.x);
                let ny = Math.round(e.pageY - surfaceOffset.top - dragOffset.y);

                nx = Math.max(0, Math.min(nx, canvasConfig.width - layers[selectedIndex].w));
                ny = Math.max(0, Math.min(ny, canvasConfig.height - layers[selectedIndex].h));

                layers[selectedIndex].x = nx;
                layers[selectedIndex].y = ny;
                $('#prop-input-x').val(nx);
                $('#prop-input-y').val(ny);

                $('.wppoppop-layer-node[data-index="' + selectedIndex + '"]').css({ left: nx + 'px', top: ny + 'px' });
            } else if (isResizingNode && selectedIndex >= 0) {
                const dw = e.pageX - resizeNodeStart.x;
                const dh = e.pageY - resizeNodeStart.y;
                const nw = Math.max(20, Math.round(resizeNodeStart.w + dw));
                const nh = Math.max(16, Math.round(resizeNodeStart.h + dh));

                layers[selectedIndex].w = nw;
                layers[selectedIndex].h = nh;
                $('#prop-input-w').val(nw);
                $('#prop-input-h').val(nh);

                $('.wppoppop-layer-node[data-index="' + selectedIndex + '"]').css({ width: nw + 'px', height: nh + 'px' });
            } else if (isResizingCanvas) {
                const dw = e.pageX - resizeCanvasStart.x;
                const dh = e.pageY - resizeCanvasStart.y;
                canvasConfig.width  = Math.max(200, Math.min(1400, Math.round(resizeCanvasStart.w + dw)));
                canvasConfig.height = Math.max(150, Math.min(1200, Math.round(resizeCanvasStart.h + dh)));
                updateCanvasSurface();
            }
        });

        $(document).on('mouseup', function() {
            isDraggingNode = false;
            isResizingNode = false;
            isResizingCanvas = false;
        });

        // Layer selection from LAYERS stack list
        $(document).on('click', '.wppoppop-layer-item', function(e) {
            if ($(e.target).is('span')) return;
            const idx = parseInt($(this).data('index'), 10);
            selectLayer(idx);
        });

        // Move Layer Up / Down
        $(document).on('click', '.wppoppop-layer-up', function(e) {
            e.stopPropagation();
            const idx = parseInt($(this).closest('.wppoppop-layer-item').data('index'), 10);
            if (idx < layers.length - 1) {
                const temp = layers[idx];
                layers[idx] = layers[idx + 1];
                layers[idx + 1] = temp;
                selectLayer(idx + 1);
            }
        });

        $(document).on('click', '.wppoppop-layer-down', function(e) {
            e.stopPropagation();
            const idx = parseInt($(this).closest('.wppoppop-layer-item').data('index'), 10);
            if (idx > 0) {
                const temp = layers[idx];
                layers[idx] = layers[idx - 1];
                layers[idx - 1] = temp;
                selectLayer(idx - 1);
            }
        });

        $(document).on('click', '.wppoppop-layer-del', function(e) {
            e.stopPropagation();
            const idx = parseInt($(this).closest('.wppoppop-layer-item').data('index'), 10);
            layers.splice(idx, 1);
            selectLayer(-1);
        });

        // Properties Input Bindings
        $('#prop-input-content, #prop-input-placeholder, #prop-input-slug, #prop-input-fontfamily').on('input change', function() {
            if (selectedIndex < 0) return;
            const l = layers[selectedIndex];
            l.content = $('#prop-input-content').val();
            l.placeholder = $('#prop-input-placeholder').val();
            l.fieldName = $('#prop-input-slug').val();
            l.fontFamily = $('#prop-input-fontfamily').val();
            renderLayers();
        });

        $('#prop-color-text, #prop-color-bg').on('input change', function() {
            if (selectedIndex < 0) return;
            const l = layers[selectedIndex];
            l.color = $('#prop-color-text').val();
            l.bgColor = $('#prop-color-bg').val();
            renderLayers();
        });

        $('#prop-input-x, #prop-input-y, #prop-input-w, #prop-input-h, #prop-input-fontsize, #prop-input-radius').on('input', function() {
            if (selectedIndex < 0) return;
            const l = layers[selectedIndex];
            l.x = parseInt($('#prop-input-x').val(), 10) || 0;
            l.y = parseInt($('#prop-input-y').val(), 10) || 0;
            l.w = parseInt($('#prop-input-w').val(), 10) || 20;
            l.h = parseInt($('#prop-input-h').val(), 10) || 20;
            l.fontSize = parseInt($('#prop-input-fontsize').val(), 10) || 14;
            l.borderRadius = parseInt($('#prop-input-radius').val(), 10) || 0;
            renderLayers();
        });

        $('#prop-btn-apply').on('click', function() {
            renderLayers();
            showToast('Element updated');
        });

        $('#prop-btn-close-props').on('click', function() {
            $('#wppoppop-properties-panel').hide();
            selectLayer(-1);
        });

        $('#prop-btn-delete-layer').on('click', function() {
            if (selectedIndex >= 0) {
                layers.splice(selectedIndex, 1);
                selectLayer(-1);
            }
        });

        // Media Library Frame
        $('#prop-btn-media').on('click', function(e) {
            e.preventDefault();
            if (wp && wp.media) {
                const frame = wp.media({
                    title: 'Select or Upload Popup Image',
                    button: { text: 'Use this Image' },
                    multiple: false
                });
                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    if (attachment && attachment.url && selectedIndex >= 0) {
                        layers[selectedIndex].imageSrc = attachment.url;
                        $('#prop-input-imgurl').val(attachment.url);
                        renderLayers();
                    }
                });
                frame.open();
            }
        });

        // Canvas Modal Toggle (Gear Icon)
        $('#wppoppop-btn-canvas-settings').on('click', function() {
            $('#wppoppop-canvas-modal').fadeIn(150);
        });
        $('#wppoppop-modal-canvas-close, #modal-canvas-cancel').on('click', function() {
            $('#wppoppop-canvas-modal').fadeOut(150);
        });
        $('#modal-canvas-apply').on('click', function() {
            canvasConfig.width = parseInt($('#modal-canvas-w').val(), 10) || 640;
            canvasConfig.height = parseInt($('#modal-canvas-h').val(), 10) || 440;
            canvasConfig.bgColor = $('#modal-canvas-bg').val() || '#ffffff';
            canvasConfig.borderRadius = parseInt($('#modal-canvas-radius').val(), 10) || 8;
            updateCanvasSurface();
            $('#wppoppop-canvas-modal').fadeOut(150);
        });

        // Toast Alert Helper
        function showToast(msg) {
            const $t = $('#wppoppop-toast-alert');
            $t.text(msg).fadeIn(150);
            setTimeout(() => $t.fadeOut(200), 2200);
        }

        // Save Popup Handler
        $('#wppoppop-btn-save-popup').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Saving...');

            const title = $('#wppoppop-popup-title-input').val().trim() || 'popup-' + Date.now();
            const status = $('#wppoppop-status-checkbox').is(':checked') ? 'publish' : 'draft';

            $.post(cfg.ajaxUrl, {
                action: 'wppoppop_save_builder',
                nonce: cfg.nonce,
                popup_id: cfg.popupId,
                title: title,
                status: status,
                layers: JSON.stringify(layers),
                canvas_config: JSON.stringify(canvasConfig)
            }, function(res) {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> Save');
                if (res.success && res.data) {
                    showToast(res.data.message || 'Popup Saved!');
                    if (!cfg.popupId && res.data.popup_id) {
                        cfg.popupId = res.data.popup_id;
                        if (window.history && window.history.replaceState) {
                            window.history.replaceState(null, '', res.data.edit_url);
                        }
                    }
                } else {
                    alert((res.data && res.data.message) ? res.data.message : 'Error saving popup.');
                }
            }).fail(function() {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> Save');
                alert('A network error occurred while saving.');
            });
        });

        // Initial Canvas & Layers Render
        updateCanvasSurface();
        renderLayers();
    });
})(jQuery);
