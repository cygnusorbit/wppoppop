/**
 * WpPopPop Element: Title / Headline
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    window.WpPopPopElements.register('title', {
        label: 'Headline Title',
        icon: 'dashicons-heading',

        getDefaults: function() {
            return {
                width: 280,
                height: 48,
                content: 'Your Compelling Headline',
                titleText: 'Your Compelling Headline',
                headingTag: 'h2',
                textTransform: 'none',
                fontSize: 24,
                fontWeight: '700',
                color: '#0f172a',
                textAlign: 'left',
                lineHeight: '1.2',
                letterSpacing: '0px'
            };
        },

        renderStage: function(el) {
            var text = el.titleText || el.content || 'Headline Title';
            var tag = el.headingTag || el.tag || 'h2';
            var transform = el.textTransform || 'none';
            var align = el.textAlign || 'left';
            var lh = el.lineHeight || '1.2';
            var ls = el.letterSpacing || '0px';

            return '<' + tag + ' style="margin:0;padding:0;width:100%;height:100%;font-size:inherit;font-weight:inherit;' +
                'color:inherit;text-align:' + align + ';text-transform:' + transform + ';line-height:' + lh + ';' +
                'letter-spacing:' + ls + ';display:flex;align-items:center;user-select:none;pointer-events:none;box-sizing:border-box;">' +
                text +
                '</' + tag + '>';
        },

        renderSettings: function(el, $container) {
            var text = el.titleText || el.content || 'Your Compelling Headline';
            var tag = el.headingTag || el.tag || 'h2';
            var transform = el.textTransform || 'none';

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Headline Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Headline Text</label>' +
                    '<textarea id="insp-title-text" rows="2" class="wppoppop-insp-input" style="resize:vertical;">' + text + '</textarea>' +
                '</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">' +
                    '<div>' +
                        '<label>Heading Tag</label>' +
                        '<select id="insp-title-tag" class="wppoppop-insp-input">' +
                            '<option value="h1"' + (tag === 'h1' ? ' selected' : '') + '>H1 Heading</option>' +
                            '<option value="h2"' + (tag === 'h2' ? ' selected' : '') + '>H2 Heading</option>' +
                            '<option value="h3"' + (tag === 'h3' ? ' selected' : '') + '>H3 Heading</option>' +
                            '<option value="h4"' + (tag === 'h4' ? ' selected' : '') + '>H4 Heading</option>' +
                            '<option value="h5"' + (tag === 'h5' ? ' selected' : '') + '>H5 Heading</option>' +
                            '<option value="h6"' + (tag === 'h6' ? ' selected' : '') + '>H6 Heading</option>' +
                            '<option value="div"' + (tag === 'div' ? ' selected' : '') + '>DIV Tag</option>' +
                        '</select>' +
                    '</div>' +
                    '<div>' +
                        '<label>Text Transform</label>' +
                        '<select id="insp-title-transform" class="wppoppop-insp-input">' +
                            '<option value="none"' + (transform === 'none' ? ' selected' : '') + '>None</option>' +
                            '<option value="uppercase"' + (transform === 'uppercase' ? ' selected' : '') + '>Uppercase</option>' +
                            '<option value="lowercase"' + (transform === 'lowercase' ? ' selected' : '') + '>Lowercase</option>' +
                            '<option value="capitalize"' + (transform === 'capitalize' ? ' selected' : '') + '>Capitalize</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-title-text').on('input change', function() {
                el.content = $(this).val();
                el.titleText = el.content;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-title-tag').on('change', function() {
                el.headingTag = $(this).val();
                el.tag = el.headingTag;
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-title-transform').on('change', function() {
                el.textTransform = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    });
})(jQuery);
