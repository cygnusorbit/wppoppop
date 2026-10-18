/**
 * WpPopPop Front Module: front-tokens
 * Live {{date}} {{time}} {{url}} {{page-title}} {{user-agent}} {{wp-user-*}} resolution.
 */
(function(window, document) {
    'use strict';

    var DAYS = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    function pad(n) { return (n < 10 ? '0' : '') + n; }

    // Minimal PHP date() format -> string, using the browser (system) clock
    function fmt(format, d) {
        var out = '', i, c;
        for (i = 0; i < format.length; i++) {
            c = format.charAt(i);
            switch (c) {
                case '\\': i++; out += format.charAt(i) || ''; break;
                case 'd': out += pad(d.getDate()); break;
                case 'j': out += d.getDate(); break;
                case 'D': out += DAYS[d.getDay()].substr(0, 3); break;
                case 'l': out += DAYS[d.getDay()]; break;
                case 'N': out += (d.getDay() || 7); break;
                case 'w': out += d.getDay(); break;
                case 'm': out += pad(d.getMonth() + 1); break;
                case 'n': out += (d.getMonth() + 1); break;
                case 'M': out += MONTHS[d.getMonth()].substr(0, 3); break;
                case 'F': out += MONTHS[d.getMonth()]; break;
                case 'Y': out += d.getFullYear(); break;
                case 'y': out += String(d.getFullYear()).slice(-2); break;
                case 'a': out += (d.getHours() < 12 ? 'am' : 'pm'); break;
                case 'A': out += (d.getHours() < 12 ? 'AM' : 'PM'); break;
                case 'g': out += (d.getHours() % 12 || 12); break;
                case 'G': out += d.getHours(); break;
                case 'h': out += pad(d.getHours() % 12 || 12); break;
                case 'H': out += pad(d.getHours()); break;
                case 'i': out += pad(d.getMinutes()); break;
                case 's': out += pad(d.getSeconds()); break;
                default: out += c;
            }
        }
        return out;
    }

    function userVars() {
        var v = window.wppoppop_front_vars;
        return (v && v.current_user) ? v.current_user : {};
    }

    var TOKEN_NAMES = 'record-id|ip|user-agent|date|time|wp-user-login|wp-user-email|url|page-title';
    var TOKEN_SPLIT = new RegExp('(\\{\\{(?:' + TOKEN_NAMES + ')\\}\\})', 'g');
    var TOKEN_ONE = new RegExp('^\\{\\{(' + TOKEN_NAMES + ')\\}\\}$');
    var SKIP = /^(SCRIPT|STYLE|TEXTAREA|NOSCRIPT|INPUT|SELECT|OPTION)$/;

    function wpFormats() {
        var v = window.wppoppop_front_vars || {};
        return { date: v.date_format || 'F j, Y', time: v.time_format || 'g:i a' };
    }

    // Fallback: wrap any raw {{token}} text left in the popup markup
    function scanText() {
        var roots = document.querySelectorAll('.wppoppop-popup-wrap');
        var f = wpFormats();
        for (var r = 0; r < roots.length; r++) {
            var root = roots[r];
            if ((root.textContent || '').indexOf('{{') === -1) continue;
            var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
            var found = [];
            while (walker.nextNode()) {
                var t = walker.currentNode;
                if (t.nodeValue.indexOf('{{') !== -1 && t.parentNode && !SKIP.test(t.parentNode.nodeName)) found.push(t);
            }
            for (var i = 0; i < found.length; i++) {
                var node = found[i];
                var parts = node.nodeValue.split(TOKEN_SPLIT);
                if (parts.length < 2) continue;
                var frag = document.createDocumentFragment();
                for (var p = 0; p < parts.length; p++) {
                    var m = parts[p].match(TOKEN_ONE);
                    if (m) {
                        var span = document.createElement('span');
                        span.className = 'wppoppop-const wppoppop-const-' + m[1];
                        span.setAttribute('data-token', m[1]);
                        if (m[1] === 'date') span.setAttribute('data-format', f.date);
                        if (m[1] === 'time') span.setAttribute('data-format', f.time);
                        frag.appendChild(span);
                    } else if (parts[p] !== '') {
                        frag.appendChild(document.createTextNode(parts[p]));
                    }
                }
                node.parentNode.replaceChild(frag, node);
            }
        }
    }

    function refresh() {
        scanText();
        var nodes = document.querySelectorAll('.wppoppop-const');
        if (!nodes.length) return;
        var now = new Date();
        var u = userVars();
        for (var i = 0; i < nodes.length; i++) {
            var n = nodes[i];
            var k = n.getAttribute('data-token');
            var val = null;
            if (k === 'date') val = fmt(n.getAttribute('data-format') || wpFormats().date, now);
            else if (k === 'time') val = fmt(n.getAttribute('data-format') || wpFormats().time, now);
            else if (k === 'url') val = window.location.href;
            else if (k === 'page-title') val = document.title;
            else if (k === 'user-agent') val = navigator.userAgent;
            else if (k === 'wp-user-login' && u.login) val = u.login;
            else if (k === 'wp-user-email' && u.email) val = u.email;
            if (val !== null && n.textContent !== val) n.textContent = val;
        }
    }

    function start() {
        refresh();
        if (!window.__wppoppopTokenTimer) {
            window.__wppoppopTokenTimer = window.setInterval(refresh, 1000);
        }
    }

    window.WpPopPopFrontTokens = { refresh: refresh, format: fmt };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();
    window.addEventListener('load', refresh);
})(window, document);
