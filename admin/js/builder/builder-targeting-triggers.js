/**
 * WpPopPop Builder: Targeting Triggers module (Popup Settings > Targeting Triggers)
 * Data lifecycle: inspector DOM <-> JS model <-> getSettings()/setSettings() <-> save payload.
 * The model (not the DOM) is the source of truth: ids missing from a (limited) list are never dropped.
 * Cross-browser: ES5 only (Chrome, Safari, Firefox, Edge).
 */
(function ($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._ttWrapped) { return; }
    S._ttWrapped = true;

    var DRAWER = '#wppoppop-settings-drawer';
    var ROOT = '#wppoppop-tt-item';
    var EVENTS = ['onload', 'onscroll', 'onexit', 'oninactivity'];
    var MODES = ['every-time', 'once-only', 'once-per-period'];
    var AREAS = ['sitewide', 'homepage', 'posts', 'pages', 'url'];
    var LISTS = ['cats', 'tags', 'posts', 'pages'];
    var NUM = { period_hours: [1, 8760, 24], start_delay: [0, 3600, 0], close_delay: [0, 3600, 0], scroll_offset: [0, 100000, 600], idle_delay: [1, 3600, 15] };
    var FLAGS = ['cats_all', 'tags_all', 'posts_all', 'pages_all', 'cats_archive', 'tags_archive'];
    var FLAG_DEF = { cats_all: true, tags_all: true, posts_all: true, pages_all: true, cats_archive: false, tags_archive: false };
    var model = blank();

    function blank() {
        return {
            enabled: true, event: '', mode: 'every-time', period_hours: 24, start_delay: 0, close_delay: 0,
            scroll_offset: 600, scroll_unit: 'px', idle_delay: 15, activity: 'always', from: '', to: '',
            roles: ['all'], area: 'sitewide',
            cats_all: true, cats: [], cats_archive: false, tags_all: true, tags: [], tags_archive: false,
            posts_all: true, posts: [], pages_all: true, pages: [], url_keywords: ''
        };
    }
    function pick(v, list, def) { return $.inArray(v, list) !== -1 ? v : def; }
    function bool(v) { return v === true || v === 1 || v === '1' || v === 'true'; }
    function num(v, min, max, def) {
        var n = parseFloat(v);
        if (isNaN(n)) { return def; }
        return Math.max(min, Math.min(max, Math.round(n)));
    }
    function ids(v) {
        var out = [], seen = {}, i, n;
        if (Array.isArray(v)) {
            for (i = 0; i < v.length; i++) {
                n = parseInt(v[i], 10);
                if (n > 0 && !seen[n]) { seen[n] = 1; out.push(n); }
            }
        }
        return out;
    }
    function roles(v) {
        var out = [], i, r;
        if (Array.isArray(v)) {
            for (i = 0; i < v.length; i++) {
                r = String(v[i]);
                if (/^[A-Za-z0-9_\-]+$/.test(r) && $.inArray(r, out) === -1) { out.push(r); }
            }
        }
        return (!out.length || $.inArray('all', out) !== -1) ? ['all'] : out;
    }
    function date(v) { return (typeof v === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(v)) ? v : ''; }
    function has(o, k) { return Object.prototype.hasOwnProperty.call(o, k); }

    function normalize(src) {
        var d = blank(), m = blank(), k, i;
        if (!src || typeof src !== 'object') { return m; }
        m.enabled = has(src, 'enabled') ? bool(src.enabled) : d.enabled;
        m.event = pick(src.event, EVENTS, '');
        m.mode = pick(src.mode, MODES, 'every-time');
        m.scroll_unit = pick(src.scroll_unit, ['px', '%'], 'px');
        m.activity = pick(src.activity, ['always', 'period'], 'always');
        m.area = pick(src.area, AREAS, 'sitewide');
        for (k in NUM) { if (has(NUM, k)) { m[k] = num(src[k], NUM[k][0], NUM[k][1], NUM[k][2]); } }
        if (m.scroll_unit === '%') { m.scroll_offset = Math.min(100, m.scroll_offset); }
        for (i = 0; i < FLAGS.length; i++) { m[FLAGS[i]] = has(src, FLAGS[i]) ? bool(src[FLAGS[i]]) : FLAG_DEF[FLAGS[i]]; }
        for (i = 0; i < LISTS.length; i++) { m[LISTS[i]] = ids(src[LISTS[i]]); }
        m.roles = roles(src.roles);
        m.from = date(src.from);
        m.to = date(src.to);
        if (m.from && m.to && m.from > m.to) { k = m.from; m.from = m.to; m.to = k; }
        m.url_keywords = typeof src.url_keywords === 'string' ? src.url_keywords.substring(0, 20000) : '';
        return m;
    }

    function clone(m) { return $.extend(true, {}, m); }

    function paint() {
        var $root = $(ROOT);
        if (!$root.length) { return; }

        $root.find('[data-tt-group]').each(function () {
            var on = String(model[$(this).attr('data-tt-group')]) === $(this).attr('data-tt-val');
            $(this).toggleClass('is-on', on).attr('aria-checked', on ? 'true' : 'false');
        });

        $root.find('[data-tt-field]').each(function () {
            if (this === document.activeElement) { return; }
            $(this).val(model[$(this).attr('data-tt-field')]);
        });

        $root.find('[data-tt-item]').each(function () {
            var key = $(this).attr('data-tt-item'), val = this.value, on;
            on = key === 'roles' ? $.inArray(val, model.roles) !== -1 : $.inArray(parseInt(val, 10), model[key]) !== -1;
            this.checked = on;
        });

        $root.find('[data-tt-flag]').each(function () { this.checked = !!model[$(this).attr('data-tt-flag')]; });

        $('#set-tt-enabled').prop('checked', model.enabled);
        $('#wppoppop-tt-fs').prop('disabled', !model.enabled);
        $root.toggleClass('is-off', !model.enabled);

        $root.find('[data-tt-when]').each(function () {
            var p = $(this).attr('data-tt-when').split(':');
            $(this).toggle(String(model[p[0]]) === p[1]);
        });
        $root.find('[data-tt-has]').each(function () {
            $(this).toggle(String(model[$(this).attr('data-tt-has')]) !== '');
        });
    }

    function changed() { $(DRAWER).trigger('change'); }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function () {
        var out = origGet.apply(this, arguments) || {};
        out.targeting_triggers = clone(model);
        return out;
    };

    S.setSettings = function (settings) {
        var r = origSet.apply(this, arguments);
        model = normalize(settings && settings.targeting_triggers);
        paint();
        return r;
    };

    function bind() {
        $(DRAWER)
            .off('.wppTT')
            .on('click.wppTT', ROOT + ' [data-tt-group]', function (e) {
                var g = $(this).attr('data-tt-group'), v = $(this).attr('data-tt-val');
                e.preventDefault();
                if ($.inArray(g, FLAGS) !== -1) { model[g] = bool(v); } else { model[g] = v; }
                if (g === 'scroll_unit' && v === '%') { model.scroll_offset = Math.min(100, model.scroll_offset); }
                model = normalize(model);
                paint();
                changed();
            })
            .on('input.wppTT change.wppTT', ROOT + ' [data-tt-field]', function (e) {
                var k = $(this).attr('data-tt-field'), v = $(this).val();
                if (NUM[k]) {
                    model[k] = num(v, NUM[k][0], (k === 'scroll_offset' && model.scroll_unit === '%') ? 100 : NUM[k][1], NUM[k][2]);
                } else if (k === 'from' || k === 'to') {
                    model[k] = date(v);
                } else if (k === 'url_keywords') {
                    model[k] = String(v).substring(0, 20000);
                }
                if (e.type === 'change') {
                    if (model.from && model.to && model.from > model.to) { v = model.from; model.from = model.to; model.to = v; }
                    $(this).val(model[k]);
                    paint();
                }
            })
            .on('change.wppTT', ROOT + ' [data-tt-item]', function () {
                var key = $(this).attr('data-tt-item'), raw = this.value, on = this.checked, cur, n;
                if (key === 'roles') {
                    if (raw === 'all') {
                        model.roles = ['all'];
                    } else {
                        cur = $.grep(model.roles, function (x) { return x !== 'all' && x !== raw; });
                        if (on) { cur.push(raw); }
                        model.roles = cur.length ? cur : ['all'];
                    }
                } else {
                    n = parseInt(raw, 10);
                    cur = $.grep(model[key], function (x) { return x !== n; });
                    if (on) { cur.push(n); }
                    model[key] = cur;
                }
                paint();
            })
            .on('change.wppTT', ROOT + ' [data-tt-flag]', function () {
                model[$(this).attr('data-tt-flag')] = this.checked;
            })
            .on('change.wppTT', '#set-tt-enabled', function () {
                model.enabled = this.checked;
                paint();
            })
            .on('input.wppTT', ROOT + ' [data-tt-filter]', function () {
                var q = String($(this).val()).replace(/^\s+|\s+$/g, '').toLowerCase(), key = $(this).attr('data-tt-filter');
                $(ROOT + ' [data-tt-list="' + key + '"] .tt-chk').each(function () {
                    $(this).toggle(!q || $(this).text().toLowerCase().indexOf(q) !== -1);
                });
            });
    }

    if ($(DRAWER).length) { bind(); }
    $(function () { bind(); paint(); });
})(jQuery);
