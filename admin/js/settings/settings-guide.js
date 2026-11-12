/**
 * WpPopPop Settings: "Restart tour" button (resets the current user's guide progress).
 * Vanilla JS, no dependencies.
 */
(function (window, document) {
    'use strict';

    var cfg = window.wppoppop_guide_settings || {};

    function setStatus(msg, isError) {
        var el = document.getElementById('wppoppop-guide-restart-status');
        if (!el) { return; }
        el.textContent = msg || '';
        el.style.color = isError ? '#b32d2e' : '#008a20';
    }

    function restart(btn) {
        var xhr = new XMLHttpRequest();
        var body = 'action=wppoppop_guide_save&op=reset&nonce=' + encodeURIComponent(btn.getAttribute('data-nonce') || cfg.nonce || '');
        btn.disabled = true;
        xhr.open('POST', cfg.ajax_url || window.ajaxurl || '/wp-admin/admin-ajax.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) { return; }
            var ok = false;
            try { ok = JSON.parse(xhr.responseText).success === true; } catch (e) { ok = false; }
            btn.disabled = false;
            if (ok) {
                try { window.localStorage.removeItem('wppoppop_guide_seen_' + (btn.getAttribute('data-uid') || 0)); } catch (err) { /* storage may be blocked */ }
            }
            setStatus(ok ? (cfg.i18n && cfg.i18n.done) : (cfg.i18n && cfg.i18n.fail), !ok);
        };
        xhr.send(body);
    }

    document.addEventListener('click', function (e) {
        var t = e.target;
        while (t && t !== document && !(t.id === 'wppoppop-guide-restart')) { t = t.parentNode; }
        if (!t || t === document) { return; }
        e.preventDefault();
        setStatus('', false);
        restart(t);
    });
})(window, document);
