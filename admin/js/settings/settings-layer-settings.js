/**
 * WpPopPop Settings: Builder sub-tabs and Element / Layer Settings editor.
 * Writes the overrides (only values that differ from the element default) into #wppoppop-ls-json,
 * which is saved with the normal settings form as settings[builder_layer_settings].
 */
(function (window, $) {
    'use strict';

    function readJson($field) {
        var data = {};
        try { data = JSON.parse($field.val() || '{}'); } catch (e) { data = {}; }
        return (data && typeof data === 'object' && !$.isArray(data)) ? data : {};
    }

    function countKeys(obj) {
        var n = 0, k;
        for (k in obj) { if (Object.prototype.hasOwnProperty.call(obj, k)) { n++; } }
        return n;
    }

    function init() {
        var $root = $('#wppoppop-ls-root');
        var $field = $('#wppoppop-ls-json');

        /* Builder sub-tabs: Builder Tools | Element / Layer Settings */
        $('.wppoppop-bld-subtabs').on('click', '.wppoppop-bld-subtab', function (e) {
            e.preventDefault();
            var target = $(this).attr('data-bld-pane');
            $('.wppoppop-bld-subtab').removeClass('is-active').attr('aria-selected', 'false');
            $(this).addClass('is-active').attr('aria-selected', 'true');
            $('.wppoppop-bld-pane').each(function () {
                if ($(this).attr('data-bld-pane') === target) { this.removeAttribute('hidden'); }
                else { this.setAttribute('hidden', 'hidden'); }
            });
        });

        if (!$root.length || !$field.length) { return; }
        var data = readJson($field);

        function persist() {
            $field.val(JSON.stringify(data));
        }

        function markElement(type) {
            var has = data[type] && countKeys(data[type]) > 0;
            $root.find('.wppoppop-ls-el-btn[data-ls-el="' + type + '"]').toggleClass('has-override', !!has);
        }

        function store(type, tab, key, value, def) {
            if (value === def) {
                if (data[type] && data[type][tab]) {
                    delete data[type][tab][key];
                    if (!countKeys(data[type][tab])) { delete data[type][tab]; }
                    if (!countKeys(data[type])) { delete data[type]; }
                }
            } else {
                data[type] = data[type] || {};
                data[type][tab] = data[type][tab] || {};
                data[type][tab][key] = value;
            }
            markElement(type);
        }

        /* Element list */
        $root.on('click', '.wppoppop-ls-el-btn', function (e) {
            e.preventDefault();
            var type = $(this).attr('data-ls-el');
            $root.find('.wppoppop-ls-el-btn').removeClass('is-active').attr('aria-selected', 'false');
            $(this).addClass('is-active').attr('aria-selected', 'true');
            $root.find('.wppoppop-ls-el').each(function () {
                if ($(this).attr('data-ls-el') === type) { this.removeAttribute('hidden'); }
                else { this.setAttribute('hidden', 'hidden'); }
            });
        });

        /* Content | Style | Logic | Data | Advanced */
        $root.on('click', '.wppoppop-ls-tab-btn', function (e) {
            e.preventDefault();
            var tab = $(this).attr('data-ls-tab');
            var $el = $(this).closest('.wppoppop-ls-el');
            $el.find('.wppoppop-ls-tab-btn').removeClass('is-active');
            $(this).addClass('is-active');
            $el.find('.wppoppop-ls-pane').each(function () {
                if ($(this).attr('data-ls-tab') === tab) { this.removeAttribute('hidden'); }
                else { this.setAttribute('hidden', 'hidden'); }
            });
        });

        /* Enable / disable toggles */
        $root.on('change', '.wppoppop-ls-chk', function () {
            var $c = $(this);
            var on = $c.is(':checked') ? 1 : 0;
            var def = parseInt($c.attr('data-default'), 10) === 1 ? 1 : 0;
            store($c.attr('data-type'), $c.attr('data-tab'), $c.attr('data-key'), on, def);
            $c.closest('.wppoppop-ls-row').toggleClass('is-off', !on);
            persist();
        });

        /* Reset one element to its defaults */
        $root.on('click', '.wppoppop-ls-reset', function (e) {
            e.preventDefault();
            var type = $(this).attr('data-ls-el');
            $root.find('.wppoppop-ls-chk[data-type="' + type + '"]').each(function () {
                var def = parseInt($(this).attr('data-default'), 10) === 1;
                $(this).prop('checked', def).closest('.wppoppop-ls-row').toggleClass('is-off', !def);
            });
            delete data[type];
            markElement(type);
            persist();
        });
    }

    $(init);
})(window, jQuery);
