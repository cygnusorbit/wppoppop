/* Display Triggers: Inspector DOM <-> JS settings model.
 * Wraps getSettings / setSettings so the accordion is saved to and restored from
 * settings.triggers, settings.exit_intent_sensitivity and settings.scroll_depth.
 * Only loaded while the module is installed; nothing in the builder core depends on it. */
(function ($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._dispTrigWrapped) { return; }
    S._dispTrigWrapped = true;

    function clamp(v, lo, hi, def) {
        v = parseInt(v, 10);
        if (isNaN(v)) { v = def; }
        return Math.max(lo, Math.min(hi, v));
    }

    function flag(v) {
        return !!v && v !== 'false' && v !== '0';
    }

    function dimSubs() {
        $('.wppoppop-trig-sub[data-for]').each(function () {
            var on = $('#' + $(this).attr('data-for')).is(':checked');
            $(this).toggleClass('is-off', !on); /* visual hint only, never disabled */
        });
        $('#trig-load-delay, #set-exit-intent-sensitivity, #set-trigger-scroll-depth, #trig-repeat-hours')
            .prop('disabled', false).prop('readOnly', false);
    }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function () {
        var out = origGet.apply(this, arguments) || {};
        var pct = clamp($('#set-trigger-scroll-depth').val(), 1, 100, 50);
        out.triggers = $.extend(out.triggers || {}, {
            load: $('#trig-load').is(':checked'),
            load_delay: clamp($('#trig-load-delay').val(), 0, 3600, 0),
            exit: $('#trig-exit').is(':checked'),
            scroll: $('#trig-scroll').is(':checked'),
            scroll_val: pct, /* renderer reads this first */
            adblock: $('#trig-adblock').is(':checked'),
            backbutton: $('#trig-backbutton').is(':checked'),
            always: $('#trig-always').is(':checked'),
            always_embed: $('#trig-always-embed').is(':checked'),
            repeat: $('#trig-repeat').is(':checked'),
            repeat_hours: clamp($('#trig-repeat-hours').val(), 1, 720, 24)
        });
        out.scroll_depth = pct; /* legacy fallback stays identical */
        out.exit_intent_sensitivity = clamp($('#set-exit-intent-sensitivity').val(), 1, 200, 20);
        return out;
    };

    S.setSettings = function (settings) {
        var r = origSet.apply(this, arguments);
        if (settings) {
            var t = settings.triggers || {};
            if (settings.triggers) {
                $('#trig-load').prop('checked', !!t.load);
                $('#trig-exit').prop('checked', !!t.exit);
                $('#trig-scroll').prop('checked', !!t.scroll);
                $('#trig-adblock').prop('checked', !!t.adblock);
                $('#trig-backbutton').prop('checked', !!t.backbutton);
            }
            $('#trig-always').prop('checked', !!t.always && t.always !== 'false');
            $('#trig-always-embed').prop('checked', flag(t.always_embed));
            $('#trig-repeat').prop('checked', !!t.repeat && t.repeat !== 'false');
            $('#trig-repeat-hours').val(clamp(t.repeat_hours, 1, 720, 24));
            var pct = clamp(t.scroll_val || settings.scroll_depth, 1, 100, 50);
            $('#set-trigger-scroll-depth, #trig-scroll-val').val(pct);
            $('#trig-load-delay').val(clamp(t.load_delay, 0, 3600, 0));
            if (settings.exit_intent_sensitivity) {
                $('#set-exit-intent-sensitivity').val(clamp(settings.exit_intent_sensitivity, 1, 200, 20));
            }
        }
        dimSubs();
        return r;
    };

    function syncState() {
        var core = window.WpPopPopBuilderCore;
        var pct = clamp($('#set-trigger-scroll-depth').val(), 1, 100, 50);
        if (core && core.state) {
            core.state.settings = core.state.settings || {};
            core.state.settings.exit_intent_sensitivity = clamp($('#set-exit-intent-sensitivity').val(), 1, 200, 20);
            core.state.settings.scroll_depth = pct;
        }
        $('#trig-scroll-val').val(pct);
    }

    $(document)
        .off('input.wppTrig change.wppTrig', '#set-exit-intent-sensitivity, #set-trigger-scroll-depth, #trig-load-delay, #trig-repeat-hours')
        .on('input.wppTrig change.wppTrig', '#set-exit-intent-sensitivity, #set-trigger-scroll-depth, #trig-load-delay, #trig-repeat-hours', syncState)
        .off('change.wppTrigDim', '#trig-load, #trig-exit, #trig-scroll, #trig-repeat')
        .on('change.wppTrigDim', '#trig-load, #trig-exit, #trig-scroll, #trig-repeat', dimSubs)
        .off('change.wppAlwaysPair', '#trig-always, #trig-always-embed')
        .on('change.wppAlwaysPair', '#trig-always, #trig-always-embed', function () {
            if (!this.checked) { return; }
            $(this.id === 'trig-always' ? '#trig-always-embed' : '#trig-always').prop('checked', false);
        });

    $(function () { setTimeout(dimSubs, 350); });
})(jQuery);
