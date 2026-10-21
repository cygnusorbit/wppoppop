/**
 * Theme > General: upload / replace / remove the top bar logo (WordPress media library).
 * Plain JS. Chrome / Safari / Firefox / Edge.
 */
(function (window, document) {
    'use strict';
    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); } else { document.addEventListener('DOMContentLoaded', fn); }
    }
    ready(function () {
        var wrap = document.querySelector('.wpp-theme-media');
        if (!wrap) { return; }
        var input = wrap.querySelector('input[type="hidden"]');
        var img = wrap.querySelector('img');
        var pick = wrap.querySelector('.wpp-theme-media-pick');
        var rm = wrap.querySelector('.wpp-theme-media-remove');
        var def = wrap.getAttribute('data-default') || '';
        var frame = null;

        /* DOM -> model: the hidden input is submitted with the form */
        function setLogo(id, url) {
            input.value = id ? String(id) : '0';
            img.src = url || def;
            pick.textContent = id ? 'Replace logo' : 'Upload logo';
            rm.style.display = id ? '' : 'none';
        }

        pick.addEventListener('click', function (e) {
            e.preventDefault();
            if (!window.wp || !window.wp.media) { window.alert('WordPress Media is unavailable on this screen.'); return; }
            if (!frame) {
                frame = window.wp.media({ title: 'Select top bar logo', button: { text: 'Use as logo' }, multiple: false, library: { type: 'image' } });
                frame.on('select', function () {
                    var a = frame.state().get('selection').first();
                    a = a ? a.toJSON() : null;
                    if (a && a.id) { setLogo(a.id, a.url); }
                });
            }
            frame.open();
        });
        rm.addEventListener('click', function (e) { e.preventDefault(); setLogo(0, ''); });
    });
})(window, document);
