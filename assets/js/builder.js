(function($) {
    'use strict';

    $(document).ready(function() {
        let layers = window.wppoppopInitialLayers || [];
        let selectedIndex = null;

        if ($.fn.draggable) {
            $('#wppoppop-layers-panel').draggable({
                handle: '#wppoppop-layers-drag-handle',
                containment: '#wppoppop-canvas-container'
            });
        }

        function showToast(msg) {
            const $t = $('#wppoppop-toast');
            $t.text(msg).fadeIn(200);
            setTimeout(() => $t.fadeOut(200), 2400);
        }

        function renderLayers() {
            const $canvas = $('#wppoppop-canvas-layers');
            const $list   = $('#wppoppop-layers-list');
            $canvas.empty();
            $list.empty();

            if (layers.length === 0) {
                $('#wppoppop-layers-hint').show();
            } else {
                $('#wppoppop-layers-hint').hide();
            }

            layers.forEach(function(l, idx) {
                if (l.visible === false) return;

                const isSelected = (selectedIndex === idx);
                const $el = $('<div></div>')
                    .addClass('wppoppop-canvas-element')
                    .toggleClass('selected', isSelected)
                    .attr('data-idx', idx)
                    .css({
                        left: (l.x || 30) + 'px',
                        top: (l.y || 30) + 'px',
                        width: (l.w || 180) + 'px',
                        height: (l.h || 42) + 'px',
                        zIndex: l.z || (idx + 1),
                        color: l.color || '#111827',
                        backgroundColor: l.bg || 'transparent',
                        fontSize: (l.fontSize || 14) + 'px',
                        fontFamily: l.fontFamily || 'inherit',
                        borderRadius: (l.radius || 3) + 'px',
                        border: (l.type === 'rectangle' ? '1px solid #ccd0d4' : 'none')
                    });

                if (l.type === 'text') {
                    $el.text(l.content || 'Headline Text');
                } else if (l.type === 'email') {
                    $el.append($('<input type="email" placeholder="Enter your email..." readonly />').css({ width:'100%', height:'100%', border:'1px solid #ccd0d4', padding:'0 10px', borderRadius: (l.radius || 3) + 'px' }));
                } else if (l.type === 'input') {
                    $el.append($('<input type="text" placeholder="Your Name" readonly />').css({ width:'100%', height:'100%', border:'1px solid #ccd0d4', padding:'0 10px', borderRadius: (l.radius || 3) + 'px' }));
                } else if (l.type === 'submit') {
                    $el.append($('<button type="button"></button>').text(l.content || 'Subscribe Now').css({ width:'100%', height:'100%', background: l.bg || '#b5295c', color: l.color || '#ffffff', border:'none', borderRadius: (l.radius || 3) + 'px', cursor:'pointer', fontWeight: 600 }));
                } else if (l.type === 'ribbon') {
                    $el.text(l.content || 'SPECIAL OFFER').css({ background: l.bg || '#e0f2fe', color: l.color || '#0284c7', fontSize: '11px', fontWeight: 700 });
                } else if (l.type === 'close') {
                    $el.text('✕').css({ fontSize: '18px', cursor: 'pointer' });
                } else if (l.type === 'divider') {
                    $el.append($('<hr style="width:100%; border:0; border-top:1px solid #ccd0d4; margin:0;" />'));
                } else {
                    $el.text(l.content || l.type.toUpperCase());
                }

                $canvas.append($el);

                const $li = $('<li></li>')
                    .toggleClass('active', isSelected)
                    .attr('data-idx', idx)
                    .append($('<span></span>').text((idx + 1) + '. ' + (l.content ? l.content.substring(0, 16) : l.type)))
                    .append(
                        $('<div class="wppoppop-layer-item-actions"></div>')
                            .append($('<span class="dashicons dashicons-admin-generic" title="Edit Properties"></span>').on('click', (e) => { e.stopPropagation(); openInspector(idx); }))
                            .append($('<span class="dashicons dashicons-trash" title="Delete"></span>').on('click', (e) => { e.stopPropagation(); deleteLayer(idx); }))
                    );

                $list.append($li);
            });

            if ($.fn.draggable) {
                $('.wppoppop-canvas-element').draggable({
                    containment: '#wppoppop-canvas-box',
                    stop: function(e, ui) {
                        const idx = $(this).data('idx');
                        layers[idx].x = Math.round(ui.position.left);
                        layers[idx].y = Math.round(ui.position.top);
                    }
                });
            }
        }

        $(document).on('click', '.wppoppop-canvas-element', function(e) {
            e.stopPropagation();
            selectedIndex = $(this).data('idx');
            renderLayers();
        });

        $(document).on('click', '#wppoppop-layers-list li', function() {
            selectedIndex = $(this).data('idx');
            renderLayers();
        });

        $('#wppoppop-canvas-container').on('click', function(e) {
            if ($(e.target).is('#wppoppop-canvas-container') || $(e.target).is('#wppoppop-canvas-box') || $(e.target).is('#wppoppop-canvas-layers')) {
                selectedIndex = null;
                renderLayers();
            }
        });

        $('#wppoppop-toolbar .wppoppop-tool-btn').on('click', function() {
            const type = $(this).data('type');
            const offset = (layers.length * 15) % 180;
            const newLayer = {
                type: type,
                content: type === 'text' ? 'Headline Layer' : (type === 'submit' ? 'Subscribe Now' : ''),
                x: 40 + offset,
                y: 40 + offset,
                w: type === 'submit' ? 180 : (type === 'text' ? 240 : 220),
                h: type === 'text' ? 36 : 42,
                z: layers.length + 1,
                color: type === 'submit' ? '#ffffff' : '#111827',
                bg: type === 'submit' ? '#b5295c' : (type === 'ribbon' ? '#e0f2fe' : 'transparent'),
                fontSize: type === 'text' ? 20 : 14,
                radius: 4,
                visible: true
            };
            layers.push(newLayer);
            selectedIndex = layers.length - 1;
            renderLayers();
        });

        function openInspector(idx) {
            selectedIndex = idx;
            const l = layers[idx];
            $('#wppoppop-inspector-title').text('Edit: ' + l.type.toUpperCase());
            $('#prop-fontfamily').val(l.fontFamily || "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif");
            $('#prop-content').val(l.content || '');
            $('#prop-color').val(l.color || '#111827');
            $('#prop-bg').val(l.bg && l.bg !== 'transparent' ? l.bg : '#ffffff');
            $('#prop-fontsize').val(l.fontSize || 14);
            $('#prop-radius').val(l.radius || 0);
            $('#wppoppop-inspector-modal').fadeIn(150);
        }

        $('#wppoppop-inspector-apply').on('click', function() {
            if (selectedIndex !== null && layers[selectedIndex]) {
                layers[selectedIndex].fontFamily = $('#prop-fontfamily').val();
                layers[selectedIndex].content = $('#prop-content').val();
                layers[selectedIndex].color = $('#prop-color').val();
                layers[selectedIndex].bg = $('#prop-bg').val();
                layers[selectedIndex].fontSize = parseInt($('#prop-fontsize').val(), 10) || 14;
                layers[selectedIndex].radius = parseInt($('#prop-radius').val(), 10) || 0;
            }
            $('#wppoppop-inspector-modal').fadeOut(150);
            renderLayers();
        });

        $('.wppoppop-inspector-close').on('click', function() {
            $('#wppoppop-inspector-modal').fadeOut(150);
        });

        function deleteLayer(idx) {
            layers.splice(idx, 1);
            selectedIndex = null;
            renderLayers();
        }

        $('#wppoppop-save-btn').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).find('.wppoppop-save-text').text('Saving...');

            $.post(window.WPPopPopBuilder.ajax_url, {
                action: 'wppoppop_save_builder_popup',
                nonce: window.WPPopPopBuilder.nonce,
                popup_id: $('#wppoppop-popup-id').val(),
                title: $('#wppoppop-popup-title').val(),
                layers: JSON.stringify(layers)
            }, function(res) {
                $btn.prop('disabled', false).find('.wppoppop-save-text').text('Save');
                if (res.success) {
                    if (res.data.popup_id) {
                        $('#wppoppop-popup-id').val(res.data.popup_id);
                    }
                    showToast(res.data.message || 'Saved successfully!');
                } else {
                    alert('Error saving popup.');
                }
            });
        });

        renderLayers();
    });
})(jQuery);
