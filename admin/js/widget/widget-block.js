(function (wp) {
    'use strict';
    if (!wp || !wp.blocks || !wp.element) { return; }

    var el = wp.element.createElement;
    var be = wp.blockEditor || wp.editor;
    var InspectorControls = be.InspectorControls;
    var comps = wp.components;
    var data = window.wppoppopBlockData || { popups: [] };
    var __ = wp.i18n.__;
    var sprintf = wp.i18n.sprintf;

    function popupOptions() {
        var opts = [{ label: __('\u2014 Choose a popup \u2014', 'wppoppop'), value: '' }];
        (data.popups || []).forEach(function (p) {
            opts.push({ label: p.title + ' (' + p.uid + ')', value: p.uid });
        });
        return opts;
    }

    var MODES = [
        { label: __('Inline Popup', 'wppoppop'), value: 'inline' },
        { label: __('Onload Popup', 'wppoppop'), value: 'onload' }
    ];

    wp.blocks.registerBlockType('wppoppop/popup', {
        title: __('WpPopPop Popup', 'wppoppop'),
        description: __('Embed a popup inline or open it on page load.', 'wppoppop'),
        icon: 'megaphone',
        category: 'widgets',
        keywords: ['popup', 'wppoppop', 'modal', 'lead'],
        attributes: {
            uid: { type: 'string', default: '' },
            mode: { type: 'string', default: 'inline' },
            delay: { type: 'integer', default: 0 }
        },

        edit: function (props) { /* WPPOPPOP_BLOCK_EDIT_V2 */
            var a = props.attributes;
            var set = props.setAttributes;
            var hasList = (data.popups || []).length > 0;

            function buildControls(prefix) {
                var c = [];
                if (hasList) {
                    c.push(el(comps.SelectControl, {
                        key: prefix + 'uid', label: __('Popup', 'wppoppop'), value: a.uid, options: popupOptions(),
                        onChange: function (v) { set({ uid: v }); }
                    }));
                } else {
                    c.push(el(comps.TextControl, {
                        key: prefix + 'uidtxt', label: __('Popup UID (no published popups found)', 'wppoppop'), value: a.uid || '',
                        onChange: function (v) { set({ uid: String(v || '').toLowerCase().replace(/[^a-z0-9_\-]/g, '') }); }
                    }));
                }
                c.push(el(comps.SelectControl, {
                    key: prefix + 'mode', label: __('Embed Type', 'wppoppop'), value: a.mode, options: MODES,
                    onChange: function (v) { set({ mode: v }); }
                }));
                if (a.mode === 'onload') {
                    c.push(el(comps.TextControl, {
                        key: prefix + 'delay', label: __('Onload Delay (seconds)', 'wppoppop'), type: 'number', min: 0,
                        value: String(a.delay || 0),
                        onChange: function (v) { set({ delay: Math.max(0, parseInt(v, 10) || 0) }); }
                    }));
                }
                return c;
            }

            var modeLabel = a.mode === 'onload' ? __('Onload Popup', 'wppoppop') : __('Inline Popup', 'wppoppop');
            var summary = a.uid
                ? (modeLabel + ' \u2192 ' + a.uid +
                   (a.mode === 'onload' ? ' ' + sprintf(
                       /* translators: %d: onload delay in seconds. */
                       __('(delay %ds)', 'wppoppop'), a.delay || 0) : ''))
                : __('Choose a popup below.', 'wppoppop');

            return el('div', { style: { border: '1px dashed #94a3b8', borderRadius: '0px', padding: '14px', background: '#f8fafc' } },
                el(InspectorControls, null,
                    el(comps.PanelBody, { title: __('WpPopPop Popup', 'wppoppop'), initialOpen: true }, buildControls('s_'))
                ),
                el('strong', null, __('WpPopPop Popup', 'wppoppop')),
                el('p', { style: { margin: '6px 0 10px', color: '#475569' } }, summary),
                el('div', null, buildControls('b_'))
            );
        },

        save: function () { return null; } /* dynamic block: PHP renders on the front end */
    });
})(window.wp);
