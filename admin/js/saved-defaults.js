/* WpPopPop Saved Defaults: shared "Save as Default" / "Clear" buttons (Settings, Theme, Builder, Tools). */
(function () {
    'use strict';
    var cfg = window.wppoppopDefaults;
    if (!cfg) { return; }
    var t = cfg.i18n || {};

    function closestAttr(el, attr) {
        while (el && el.nodeType === 1) {
            if (el.hasAttribute(attr)) { return el; }
            el = el.parentNode;
        }
        return null;
    }

    function send(params, done) {
        var x = new XMLHttpRequest(), body = [], k;
        params.nonce = cfg.nonce;
        for (k in params) {
            if (Object.prototype.hasOwnProperty.call(params, k)) {
                body.push(encodeURIComponent(k) + '=' + encodeURIComponent(params[k]));
            }
        }
        x.open('POST', cfg.ajax_url, true);
        x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        x.onreadystatechange = function () {
            if (x.readyState !== 4) { return; }
            var r = null;
            try { r = JSON.parse(x.responseText); } catch (e) { r = null; }
            if (r && r.success) { done(null, r.data || {}); return; }
            done((r && r.data && r.data.message) || t.error, null);
        };
        x.send(body.join('&'));
    }

    function run(btn, action, scope, extra, label) {
        if (btn.disabled) { return; }
        var orig = btn.innerHTML, params = { action: action, scope: scope }, k;
        for (k in extra) { if (Object.prototype.hasOwnProperty.call(extra, k)) { params[k] = extra[k]; } }
        btn.disabled = true;
        btn.textContent = t.saving || '...';
        send(params, function (err, data) {
            if (err) {
                btn.disabled = false;
                btn.innerHTML = orig;
                window.alert(err);
                return;
            }
            btn.textContent = label;
            if (data && data.message && data.message.indexOf('(') !== -1) { window.alert(data.message); }
            if (btn.hasAttribute('data-wpp-defaults-reload')) {
                window.setTimeout(function () { window.location.reload(); }, 600);
                return;
            }
            window.setTimeout(function () { btn.disabled = false; btn.innerHTML = orig; }, 1600);
        });
    }

    document.addEventListener('click', function (e) {
        var btn = closestAttr(e.target, 'data-wpp-defaults-save');
        if (btn) {
            e.preventDefault();
            var scope = btn.getAttribute('data-wpp-defaults-save'), extra = {};
            if (!window.confirm(t['confirm_' + scope] || '')) { return; }
            if (scope === 'popup') {
                var B = window.WpPopPopBuilderDefaults;
                if (!B || typeof B.capture !== 'function') { window.alert(t.error); return; }
                extra.data = B.capture();
            }
            run(btn, 'wppoppop_defaults_save', scope, extra, t.saved || 'OK');
            return;
        }
        btn = closestAttr(e.target, 'data-wpp-defaults-clear');
        if (btn) {
            e.preventDefault();
            if (!window.confirm(t.confirm_clear || '')) { return; }
            run(btn, 'wppoppop_defaults_clear', btn.getAttribute('data-wpp-defaults-clear'), {}, t.cleared || 'OK');
        }
    });
})();
