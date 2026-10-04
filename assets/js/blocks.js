(() => {
    'use strict';

    const { registerBlockType } = window.wp.blocks;
    const { createElement: el } = window.wp.element;
    const { InspectorControls } = window.wp.blockEditor || window.wp.editor;
    const { PanelBody, SelectControl, TextControl } = window.wp.components;
    const { __ } = window.wp.i18n;

    const editorData = window.WPPopPopEditor || { popups: [] };

    // 1. Block: wppoppop/popup-box (Inline Popup Container)
    registerBlockType('wppoppop/popup-box', {
        title: __('WP Pop Pop - Inline Box', 'wppoppop'),
        icon: 'format-gallery',
        category: 'widgets',
        attributes: {
            popupId: {
                type: 'number',
                default: 0
            }
        },
        edit: (props) => {
            const { attributes, setAttributes } = props;
            const selectedPopup = editorData.popups.find(p => p.value === attributes.popupId);

            return el('div', { className: 'wppoppop-block-editor-preview' },
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Popup Configuration', 'wppoppop'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Select Popup', 'wppoppop'),
                            value: attributes.popupId,
                            options: editorData.popups,
                            onChange: (val) => setAttributes({ popupId: parseInt(val, 10) })
                        })
                    )
                ),
                el('div', {
                    style: {
                        border: '2px dashed #3b82f6',
                        borderRadius: '8px',
                        padding: '24px',
                        textAlign: 'center',
                        background: '#f8fafc',
                        color: '#1e293b'
                    }
                },
                    el('div', { style: { fontSize: '18px', fontWeight: '700', marginBottom: '8px', color: '#2563eb' } }, '🚀 WP Pop Pop - Inline Container'),
                    el('p', { style: { margin: 0, fontSize: '14px', color: '#64748b' } },
                        attributes.popupId > 0
                            ? `${__('Active Embedded Popup:', 'wppoppop')} ${selectedPopup ? selectedPopup.label : '#' + attributes.popupId}`
                            : __('Please select a popup in the block settings sidebar.', 'wppoppop')
                    )
                )
            );
        },
        save: () => null // Rendered via server-side callback
    });

    // 2. Block: wppoppop/trigger-button (Modal Launcher Button)
    registerBlockType('wppoppop/trigger-button', {
        title: __('WP Pop Pop - Trigger Button', 'wppoppop'),
        icon: 'button',
        category: 'widgets',
        attributes: {
            popupId: {
                type: 'number',
                default: 0
            },
            buttonText: {
                type: 'string',
                default: __('Open Popup', 'wppoppop')
            }
        },
        edit: (props) => {
            const { attributes, setAttributes } = props;

            return el('div', { className: 'wppoppop-block-trigger-preview' },
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Button & Popup Settings', 'wppoppop'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Select Target Popup', 'wppoppop'),
                            value: attributes.popupId,
                            options: editorData.popups,
                            onChange: (val) => setAttributes({ popupId: parseInt(val, 10) })
                        }),
                        el(TextControl, {
                            label: __('Button Text Label', 'wppoppop'),
                            value: attributes.buttonText,
                            onChange: (val) => setAttributes({ buttonText: val })
                        })
                    )
                ),
                el('div', { style: { padding: '12px 0' } },
                    el('button', {
                        type: 'button',
                        className: 'wppoppop-trigger wppoppop-btn-inline',
                        style: { pointerEvents: 'none' }
                    }, attributes.buttonText || __('Open Popup', 'wppoppop')),
                    el('small', { style: { display: 'block', marginTop: '6px', color: '#94a3b8' } },
                        attributes.popupId > 0
                            ? `(Targeting Popup #${attributes.popupId})`
                            : __('(Select target popup in sidebar)', 'wppoppop')
                    )
                )
            );
        },
        save: () => null // Rendered via server-side callback
    });
})();
