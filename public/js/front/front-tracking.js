/**
 * WpPopPop Frontend: tracking gates, global frequency cap, admin debug badge.
 * Wraps WpPopPopFrontDisplay; requires front-display.js (trackImpression).
 */
(function (window, $) {
    'use strict';
    var D = window.WpPopPopFrontDisplay;
    var v = window.wppoppop_front_vars || {};
    var t = v.tracking || {};
    if (!D) { return; }

    function cookie(name) {
        var m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
        return m ? decodeURIComponent(m[1]) : '';
    }

    function consentOk() {
        if (t.respect_dnt && (navigator.doNotTrack === '1' || window.doNotTrack === '1' || navigator.msDoNotTrack === '1')) { return false; }
        if (t.consent_cookie && !cookie(t.consent_cookie)) { return false; }
        return true;
    }

    var origTrack = D.trackImpression;
    D.trackImpression = function (uid) {
        if (!t.enabled) { return; }
        if (t.exclude_admins && t.is_admin) { return; }
        if (!consentOk()) { return; }
        if (t.once_session) {
            try {
                var k = 'wppoppop_imp_' + uid;
                if (window.sessionStorage.getItem(k)) { return; }
                window.sessionStorage.setItem(k, '1');
            } catch (e) {}
        }
        if (typeof origTrack === 'function') { return origTrack.apply(this, arguments); }
    };

    var shown = 0;
    var origShow = D.show;
    D.show = function (uid) {
        var $w = D.getPopupWrap ? D.getPopupWrap(uid) : $('#wppoppop-popup-' + uid);
        if ($w.attr('data-inline') === '1') { return origShow.apply(this, arguments); }
        var now = Date.now();
        if (t.max_per_page > 0 && shown >= t.max_per_page) { return; }
        if (t.min_gap > 0) {
            try {
                var last = parseInt(window.localStorage.getItem('wppoppop_last_shown') || '0', 10);
                if (last && now - last < t.min_gap * 1000) { return; }
            } catch (e) {}
        }
        shown++;
        try { window.localStorage.setItem('wppoppop_last_shown', String(now)); } catch (e2) {}
        return origShow.apply(this, arguments);
    };

    if (t.debug && t.is_admin) {
        $(function () {
            var ab = [];
            document.cookie.split('; ').forEach(function (c) {
                if (c.indexOf('wppoppop_ab_') === 0) { ab.push(c); }
            });
            var txt = 'WpPopPop debug | tracking: ' + (t.enabled ? 'on' : 'off') +
                ' | consent: ' + (consentOk() ? 'ok' : 'blocked') +
                ' | A/B: ' + (ab.length ? ab.join(', ') : 'none');
            $('<div/>').text(txt).css({
                position: 'fixed', left: '8px', bottom: '8px', zIndex: 2147483647,
                background: '#111827', color: '#fff', font: '12px/1.4 sans-serif',
                padding: '6px 10px', borderRadius: '6px', maxWidth: '90vw'
            }).appendTo(document.body);
        });
    }
})(window, jQuery);
