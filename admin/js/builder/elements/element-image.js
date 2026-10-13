/**
 * WpPopPop Element: Image Element
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    window.WpPopPopElements.register('image', {
        label: 'Image',
        icon: 'dashicons-format-image',

        getDefaults: function() {
            return {
                width: 220,
                height: 160,
                content: '',
                imgUrl: '',
                altText: 'Popup Image',
                objectFit: 'cover',
                borderRadius: 4,
                borderWidth: 0,
                borderColor: '#cbd5e1'
            };
        },

        renderStage: function(el) {
            var url = el.imgUrl || el.content || '';
            var alt = el.altText || 'Popup Image';
            var fit = el.objectFit || 'cover';

            if (!url) {
                return '<div style="width:100%;height:100%;border:inherit;border-radius:inherit;background:#f1f5f9;' +
                    'display:flex;flex-direction:column;align-items:center;justify-content:center;color:#94a3b8;' +
                    'padding:10px;box-sizing:border-box;user-select:none;text-align:center;">' +
                    '<span class="dashicons dashicons-format-image" style="font-size:32px;width:32px;height:32px;margin-bottom:6px;"></span>' +
                    '<span style="font-size:11px;font-weight:600;">No Image Selected</span>' +
                    '</div>';
            }

            return '<img src="' + url + '" alt="' + alt + '" style="width:100%;height:100%;object-fit:' + fit + ';' +
                'border:inherit;border-radius:inherit;display:block;pointer-events:none;user-select:none;">';
        },

        renderSettings: function(el, $container) {
            var url = el.imgUrl || el.content || '';
            var fit = el.objectFit || 'cover';

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Image Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Image Source URL</label>' +
                    '<div style="display:flex;gap:6px;">' +
                        '<input type="url" id="insp-img-url" value="' + url + '" placeholder="https://..." class="wppoppop-insp-input" style="flex:1;">' +
                        '<button type="button" id="insp-img-select-btn" class="button button-secondary" style="white-space:nowrap;font-size:11px;">Media</button>' +
                    '</div>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Object Fit</label>' +
                        '<select id="insp-img-fit" class="wppoppop-insp-input">' +
                            '<option value="cover"' + (fit === 'cover' ? ' selected' : '') + '>Cover (Crop)</option>' +
                            '<option value="contain"' + (fit === 'contain' ? ' selected' : '') + '>Contain</option>' +
                            '<option value="fill"' + (fit === 'fill' ? ' selected' : '') + '>Fill (Stretch)</option>' +
                            '<option value="none"' + (fit === 'none' ? ' selected' : '') + '>None (Original)</option>' +
                        '</select>' +
                    '</div>' +
                    '<div>' +
                        '<label>Alt Text</label>' +
                        '<input type="text" id="insp-img-alt" value="' + (el.altText || '') + '" class="wppoppop-insp-input">' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-img-url').on('input change', function() {
                el.imgUrl = $(this).val().trim();
                el.content = el.imgUrl;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-img-fit').on('change', function() {
                el.objectFit = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-img-alt').on('change', function() {
                el.altText = $(this).val().trim();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-img-select-btn').on('click', function(e) {
                e.preventDefault();
                if (typeof wp !== 'undefined' && wp.media) {
                    var frame = wp.media({
                        title: 'Select or Upload Image',
                        button: { text: 'Use Image' },
                        multiple: false,
                        library: { type: 'image' }
                    });

                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        if (attachment && attachment.url) {
                            el.imgUrl = attachment.url;
                            el.content = attachment.url;
                            if (attachment.alt && !el.altText) {
                                el.altText = attachment.alt;
                                $('#insp-img-alt').val(el.altText);
                            }
                            $('#insp-img-url').val(attachment.url);
                            window.WpPopPopBuilderCanvas.renderCanvas();
                            window.WpPopPopBuilderCore.pushHistory();
                        }
                    });

                    frame.open();
                } else {
                    var promptUrl = prompt('Enter Image URL:', el.imgUrl || '');
                    if (promptUrl) {
                        el.imgUrl = promptUrl.trim();
                        el.content = el.imgUrl;
                        $('#insp-img-url').val(el.imgUrl);
                        window.WpPopPopBuilderCanvas.renderCanvas();
                        window.WpPopPopBuilderCore.pushHistory();
                    }
                }
            });
        }
    });
})(jQuery);
