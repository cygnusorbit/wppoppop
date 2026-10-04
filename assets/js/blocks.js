(function(wp) {
    'use strict';

    if (!wp || !wp.blocks || !wp.element) return;

    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var TextControl = wp.components.TextControl;
    var ServerSideRender = wp.serverSideRender;

    var popupOptions = (window.WPPopPopGutenberg && window.WPPopPopGutenberg.popups) ? window.WPPopPopGutenberg.popups : [
        { value: 0, label: '-- No Popups Available --' }
    ];

    // BLOCK 1: wppoppop/popup-box
    registerBlockType('wppoppop/popup-box', {
        title: 'Popup Inline Box',
        description: 'Embed an interactive popup or lead capture form directly into content.',
        icon: 'format-gallery',
        category: 'widgets',
        attributes: {
            popupId: { type: 'number', default: 0 }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: 'Popup Selection', initialOpen: true },
                        el(SelectControl, {
                            label: 'Select Popup',
                            value: attributes.popupId,
                            options: popupOptions,
                            onChange: function(val) {
                                setAttributes({ popupId: parseInt(val, 10) });
                            }
                        })
                    )
                ),
                el('div', { key: 'preview', className: 'wppoppop-editor-block-wrap' },
                    attributes.popupId > 0
                        ? el(ServerSideRender, {
                            block: 'wppoppop/popup-box',
                            attributes: attributes
                        })
                        : el('div', {
                            style: { padding: '24px', background: '#f8fafc', border: '1px dashed #cbd5e1', textAlign: 'center', color: '#64748b' }
                        }, 'Select a popup from the block sidebar to display inline preview.')
                )
            ];
        },
        save: function() {
            return null; // Dynamic server-side rendered
        }
    });

    // BLOCK 2: wppoppop/trigger-button
    registerBlockType('wppoppop/trigger-button', {
        title: 'Popup Trigger Button',
        description: 'Place an action button that opens a designated modal on click.',
        icon: 'button',
        category: 'widgets',
        attributes: {
            popupId: { type: 'number', default: 0 },
            buttonText: { type: 'string', default: 'Claim Offer' },
            alignment: { type: 'string', default: 'center' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: 'Button Configuration', initialOpen: true },
                        el(SelectControl, {
                            label: 'Target Popup',
                            value: attributes.popupId,
                            options: popupOptions,
                            onChange: function(val) {
                                setAttributes({ popupId: parseInt(val, 10) });
                            }
                        }),
                        el(TextControl, {
                            label: 'Button Label',
                            value: attributes.buttonText,
                            onChange: function(val) {
                                setAttributes({ buttonText: val });
                            }
                        }),
                        el(SelectControl, {
                            label: 'Alignment',
                            value: attributes.alignment,
                            options: [
                                { value: 'left', label: 'Left' },
                                { value: 'center', label: 'Center' },
                                { value: 'right', label: 'Right' }
                            ],
                            onChange: function(val) {
                                setAttributes({ alignment: val });
                            }
                        })
                    )
                ),
                el('div', {
                    key: 'preview',
                    style: { textAlign: attributes.alignment, padding: '12px 0' }
                },
                    el('button', {
                        type: 'button',
                        className: 'wppoppop-btn-pink',
                        style: { cursor: 'pointer', padding: '10px 24px', fontSize: '14px', borderRadius: '4px' }
                    }, attributes.buttonText || 'Claim Offer')
                )
            ];
        },
        save: function() {
            return null; // Dynamic server-side rendered
        }
    });
})(window.wp);
