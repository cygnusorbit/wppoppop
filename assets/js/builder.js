(function($) {
    'use strict';

    $(document).ready(function() {
        let layers = window.lepopupInitialLayers || [];
        const $canvasBox = $('#lepopup-canvas-box');
        const $canvasLayers = $('#lepopup-canvas-layers');
        const $layersList = $('#lepopup-layers-list');
        const $layersHint = $('#lepopup-layers-hint');
        const $notification = $('#lepopup-notification');

        function showNotification(msg) {
            $notification.text(msg).addClass('is-active');
            setTimeout(function() {
                $notification.removeClass('is-active');
            }, 2500);
        }

        function renderLayers() {
            $canvasLayers.empty();
            $layersList.empty();

            if (layers.length === 0) {
                $layersHint.show();
                return;
            }
            $layersHint.hide();

            layers.forEach(function(layer, idx) {
                const $item = $('<li>', { class: 'lepopup-layer-item' });
                $item.html('<span>' + (layer.label || ('Layer #' + (idx + 1))) + '</span><span class="dashicons dashicons-trash lepopup-delete-layer" data-index="' + idx + '"></span>');
                $layersList.append($item);

                const $elem = $('<div>', {
                    class: 'lepopup-placed-element',
                    style: 'position:absolute; left:' + (layer.x || 30) + 'px; top:' + (layer.y || 30) + 'px; z-index:' + (idx + 1) + ';'
                });

                if (layer.type === 'text') {
                    $elem.html('<h3>' + (layer.content || 'Headline Layer') + '</h3>');
                } else if (layer.type === 'email' || layer.type === 'input') {
                    $elem.html('<input type="text" placeholder="' + (layer.content || 'Your placeholder...') + '" style="padding:6px 10px; border:1px solid #ccc; width:220px;" readonly />');
                } else if (layer.type === 'submit') {
                    $elem.html('<button type="button" style="background:#d82761; color:#fff; border:none; padding:8px 18px; border-radius:3px;">' + (layer.content || 'Subscribe') + '</button>');
                } else {
                    $elem.html('<div style="padding:10px; background:rgba(0,0,0,0.05); border:1px dashed #aaa;">' + (layer.label || layer.type) + '</div>');
                }

                $canvasLayers.append($elem);
            });
        }

        // Add Layer from Elements Ribbon
        $('#lepopup-toolbar .lepopup-tool-btn').on('click', function() {
            const type = $(this).data('type');
            const title = $(this).attr('title') || type;

            layers.push({
                type: type,
                label: title,
                content: type === 'text' ? 'New Heading' : (type === 'submit' ? 'Submit' : ''),
                x: 40 + (layers.length * 15),
                y: 40 + (layers.length * 15)
            });

            renderLayers();
            showNotification('Added layer: ' + title);
        });

        // Delete Layer
        $(document).on('click', '.lepopup-delete-layer', function(e) {
            e.stopPropagation();
            const index = $(this).data('index');
            layers.splice(index, 1);
            renderLayers();
        });

        // Canvas Resizing Handle
        let isResizing = false;
        $('#lepopup-resize-handle').on('mousedown', function(e) {
            isResizing = true;
            $('body').css('user-select', 'none');
            $(document).on('mousemove.lepopupResize', function(ev) {
                if (!isResizing) return;
                const offset = $canvasBox.offset();
                const newWidth = Math.max(300, ev.pageX - offset.left);
                const newHeight = Math.max(200, ev.pageY - offset.top);
                $canvasBox.css({ width: newWidth + 'px', height: newHeight + 'px' });
            });

            $(document).on('mouseup.lepopupResize', function() {
                isResizing = false;
                $('body').css('user-select', '');
                $(document).off('.lepopupResize');
            });
        });

        // Floating Layers Dragging
        let isDraggingPanel = false;
        let dragOffset = { x: 0, y: 0 };
        const $panel = $('#lepopup-layers-panel');

        $panel.find('.lepopup-layers-header').on('mousedown', function(e) {
            isDraggingPanel = true;
            const pos = $panel.position();
            dragOffset.x = e.pageX - pos.left;
            dragOffset.y = e.pageY - pos.top;

            $(document).on('mousemove.lepopupPanelDrag', function(ev) {
                if (!isDraggingPanel) return;
                $panel.css({
                    left: (ev.pageX - dragOffset.x) + 'px',
                    top: (ev.pageY - dragOffset.y) + 'px',
                    right: 'auto'
                });
            });

            $(document).on('mouseup.lepopupPanelDrag', function() {
                isDraggingPanel = false;
                $(document).off('.lepopupPanelDrag');
            });
        });

        // Save Button AJAX Handler
        $('#lepopup-save-btn').on('click', function() {
            const $btn = $(this);
            const title = $('#lepopup-popup-title').val();
            const popupId = $('#lepopup-popup-id').val();

            $btn.prop('disabled', true).find('.lepopup-save-text').text('Saving...');

            $.ajax({
                url: window.WPPopPopBuilder.ajax_url,
                method: 'POST',
                data: {
                    action: 'wppoppop_save_builder_popup',
                    nonce: window.WPPopPopBuilder.nonce,
                    popup_id: popupId,
                    title: title,
                    layers: JSON.stringify(layers)
                },
                success: function(res) {
                    if (res.success) {
                        $('#lepopup-popup-id').val(res.data.popup_id);
                        showNotification(res.data.message || 'Saved successfully!');
                    } else {
                        showNotification('Error saving: ' + (res.data.message || 'Unknown error'));
                    }
                },
                error: function() {
                    showNotification('Network error occurred while saving.');
                },
                complete: function() {
                    $btn.prop('disabled', false).find('.lepopup-save-text').text('Save');
                }
            });
        });

        renderLayers();
    });
})(jQuery);
