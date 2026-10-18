/**
 * WpPopPop Element: Text Field Input
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    window.WpPopPopElements.register('textfield', {
        label: 'Text Field',
        icon: 'dashicons-forms',

        getDefaults: function() {
            return {
                width: 200,
                height: 42,
                content: '',
                placeholder: 'Enter your text...',
                field_name: 'text_field',
                required: false,
                borderRadius: 4,
                borderWidth: 1,
                borderColor: '#cbd5e1',
                bgColor: '#ffffff',
                color: '#0f172a',
                fontSize: 13,
                textAlign: 'left'
            };
        },

        renderStage: function(el) {
            var val = el.content || '';
            var ph = el.placeholder || 'Enter your text...';
            var align = el.textAlign || 'left';

            return '<input type="text" value="' + val + '" placeholder="' + ph + '" readonly ' +
                'style="width:100%;height:100%;padding:0 12px;border:inherit;border-radius:inherit;' +
                'text-align:' + align + ';background:inherit;color:inherit;font-size:inherit;' +
                'box-sizing:border-box;outline:none;-webkit-appearance:none;appearance:none;pointer-events:none;">';
        },

        renderSettings: function(el, $container) {
            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Text Field Settings</h4>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Default Value</label>' +
                        '<input type="text" id="insp-tf-default" value="' + (el.content || '') + '" class="wppoppop-insp-input">' +
                    '</div>' +
                    '<div>' +
                        '<label>Placeholder</label>' +
                        '<input type="text" id="insp-tf-placeholder" value="' + (el.placeholder || '') + '" class="wppoppop-insp-input">' +
                    '</div>' +
                '</div>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Field Identifier (Name)</label>' +
                    '<input type="text" id="insp-tf-fieldname" value="' + (el.field_name || 'text_field') + '" class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="display:flex;align-items:center;gap:6px;margin-top:6px;">' +
                    '<input type="checkbox" id="insp-tf-required"' + (el.required ? ' checked' : '') + '>' +
                    '<label for="insp-tf-required" style="margin:0;cursor:pointer;">Mandatory Field (Required)</label>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-tf-default').on('input change', function() {
                el.content = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-tf-placeholder').on('input change', function() {
                el.placeholder = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-tf-fieldname').on('change', function() {
                el.field_name = $(this).val().trim().replace(/[^a-zA-Z0-9_-]/g, '') || 'text_field';
                $(this).val(el.field_name);
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-tf-required').on('change', function() {
                el.required = $(this).is(':checked');
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    });
})(jQuery);
