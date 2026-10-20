/**
 * WpPopPop Builder Registry Module: registry-token
 * Auto-generates unique "<layer-name>-<6 alnum>" ID / Merge Token names.
 */
(function(window, $) {
    'use strict';
    window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var Reg = window.WpPopPopElementsRegistry;
    var ALPHA = 'abcdefghijklmnopqrstuvwxyz0123456789';
    var AUTO_RE = /^(.*)-([a-z0-9]{6})$/;

    function rand6() {
        var out = '', i, n;
        var buf = null;
        try {
            if (window.crypto && window.crypto.getRandomValues) {
                buf = new Uint8Array(6);
                window.crypto.getRandomValues(buf);
            }
        } catch (e) { buf = null; }
        for (i = 0; i < 6; i++) {
            n = buf ? buf[i] : Math.floor(Math.random() * 256);
            out += ALPHA.charAt(n % ALPHA.length);
        }
        return out;
    }

    function slug(name) {
        var s = String(name || '').toLowerCase()
            .replace(/[^a-z0-9_-]+/g, '-')
            .replace(/-{2,}/g, '-')
            .replace(/^[-_]+|[-_]+$/g, '');
        return s || 'field';
    }

    function allElements() {
        var core = window.WpPopPopBuilderCore, out = [];
        if (!core || !core.state || !core.state.canvases) return out;
        $.each(core.state.canvases, function(k, list) {
            if ($.isArray(list)) { for (var i = 0; i < list.length; i++) out.push(list[i]); }
        });
        return out;
    }

    function currentToken(el) { return String(el.fieldName || el.fieldId || el.token || ''); }

    function isTaken(tok, self) {
        var all = allElements(), low = tok.toLowerCase();
        for (var i = 0; i < all.length; i++) {
            if (all[i] === self) continue;
            if (currentToken(all[i]).toLowerCase() === low) return true;
        }
        return false;
    }

    function isFormEl(el) {
        var t = String(el.type || '').toLowerCase();
        var canon = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[t]) ? Reg.ALIAS_MAP[t] : t;
        return !Reg.FORM_FAMILY || Reg.FORM_FAMILY.indexOf(canon) !== -1;
    }


    function paintToken(tok) {
        $('#prop-field-name, #prop-data-token, #gp-data-fieldname, #prop-adv-field-id').val(tok);
        $('#prop-display-token, #gp-data-token-preview').val('{' + tok + '}');
    }

    function apply(el, tok) {
        el.fieldName = tok; el.fieldId = tok; el.token = tok; el.field_name = tok;
        if (Reg.resolveElement && Reg.resolveElement() === el) {
            paintToken(tok);
        }
    }

    Reg.generateUniqueToken = function(el) {
        var base = slug(el.name || el.label || el.type), tok, guard = 0;
        do { tok = base + '-' + rand6(); guard++; } while (isTaken(tok, el) && guard < 50);
        return tok;
    };

    /* Ensure one element has a valid, non-duplicate token. */
    function ensureCore(el, renamed) {
        if (!el || !isFormEl(el)) return;
        var cur = currentToken(el), base = slug(el.name || el.label || el.type), m;
        if (!cur) { apply(el, Reg.generateUniqueToken(el)); return; }
        m = AUTO_RE.exec(cur);
        if (isTaken(cur, el)) {
            var b = m ? m[1] : cur;
            var t, g = 0;
            do { t = b + '-' + rand6(); g++; } while (isTaken(t, el) && g < 50);
            apply(el, t);
        } else if (el.fieldName !== cur || el.fieldId !== cur || el.token !== cur) {
            apply(el, cur);
        }
    };

    Reg.ensureElementToken = function(el) { ensureCore(el, false); };

    /* Pre-save gate: every layer on every canvas. */
    Reg.ensureAllTokensUnique = function() {
        var all = allElements();
        for (var i = 0; i < all.length; i++) Reg.ensureElementToken(all[i], false);
    };

    function bind() {

        $(document).off('input.wpTok change.wpTok', '#prop-layer-name, #prop-element-name')
            .on('input.wpTok change.wpTok', '#prop-layer-name, #prop-element-name', function() {
                var el = Reg.resolveElement ? Reg.resolveElement() : null;
                if (!el) return;
                el.name = $(this).val();
                Reg.ensureElementToken(el, false);
            });

        /* Manual edits: sanitize + duplicate check on commit */
        $(document).off('change.wpTok', '#prop-field-name').on('change.wpTok', '#prop-field-name', function() {
            var el = Reg.resolveElement ? Reg.resolveElement() : null;
            if (!el) return;
            var v = $.trim($(this).val() || '').replace(/[^A-Za-z0-9_-]/g, '');
            if (!v) { el.fieldName = el.fieldId = el.token = ''; }
            else { apply(el, v); }
            Reg.ensureElementToken(el, false);
        });

        /* Inline rename in layers panel / selection: after model update */
        $(document).off('blur.wpTok keydown.wpTok', '.wppoppop-layer-rename-input')
            .on('blur.wpTok', '.wppoppop-layer-rename-input', function() {
                setTimeout(function() {
                    var el = Reg.resolveElement ? Reg.resolveElement() : null;
                    if (el) Reg.ensureElementToken(el, false);
                }, 30);
            });

        /* Pre-save: capture phase so it runs before the save handlers */
        document.addEventListener('click', function(e) {
            var t = e.target;
            if (!t || !t.closest) return;
            if (t.closest('#wppoppop-save-btn, #wppoppop-btn-save, .wppoppop-save-popup, [data-action="save"], button[id*="save"]')) {
                try {
                    if (typeof Reg.flushElementFromDOM === 'function') Reg.flushElementFromDOM();
                    Reg.ensureAllTokensUnique();
                } catch (err) { /* never block save */ }
            }
        }, true);

        /* Selecting a layer with an empty token: generate immediately */
        $(document).off('click.wpTok', '.wppoppop-canvas-element, .wppoppop-layer-item')
            .on('click.wpTok', '.wppoppop-canvas-element, .wppoppop-layer-item', function() {
                setTimeout(function() {
                    var el = Reg.resolveElement ? Reg.resolveElement() : null;
                    if (el && !currentToken(el)) Reg.ensureElementToken(el, false);
                }, 60);
            });
    }

    $(function() { bind(); });
})(window, jQuery);
