(function (window, $) {
    'use strict';

    var V = window.wppoppop_stats_vars || {};
    var MAX = parseInt(V.max_series, 10) || 8;

    function post(action, data) {
        data = data || {};
        data.action = action;
        data.nonce = V.nonce;
        return $.post(V.ajax_url, data);
    }

    function toast(msg, bad) {
        var $n = $('<div class="notice is-dismissible ' + (bad ? 'notice-error' : 'notice-success') + '" style="margin:8px 0;"><p></p></div>');
        $n.find('p').text(msg);
        $('.wppoppop-stats-header').first().before($n);
        setTimeout(function () { $n.fadeOut(200, function () { $n.remove(); }); }, 2200);
    }

    /* ---- label colors: add / edit (on change) / delete ---- */
    var timers = {};
    $(document).on('input change', '.wpp-lbl-input', function () {
        var $w = $(this).closest('.wpp-lbl'), uid = $w.data('uid'), color = this.value;
        clearTimeout(timers[uid]);
        timers[uid] = setTimeout(function () {
            post('wppoppop_stats_label_save', { uid: uid, color: color }).done(function (r) {
                if (r && r.success) {
                    $w.removeClass('is-empty').addClass('has-label').find('.wpp-lbl-del').prop('hidden', false);
                    $('.wpp-name-dot[data-uid="' + uid + '"]').css({ background: color, display: 'inline-block' });
                } else { toast((r && r.data && r.data.message) || 'Error', true); }
            }).fail(function (xhr) { toast('Request failed' + (xhr && xhr.status ? ' (HTTP ' + xhr.status + ')' : ''), true); });
        }, 250);
    });

    $(document).on('click', '.wpp-lbl-del', function () {
        var $w = $(this).closest('.wpp-lbl'), uid = $w.data('uid');
        post('wppoppop_stats_label_delete', { uid: uid }).done(function (r) {
            if (r && r.success) {
                $w.removeClass('has-label').addClass('is-empty').find('.wpp-lbl-input').val('#94a3b8');
                $w.find('.wpp-lbl-del').prop('hidden', true);
                $('.wpp-name-dot[data-uid="' + uid + '"]').css('display', 'none');
            } else { toast((r && r.data && r.data.message) || 'Error', true); }
        });
    });

    /* ---- column visibility (live + saved) ---- */
    $(document).on('change', '.wpp-col-cb', function () {
        var cols = [];
        $('.wpp-col-cb').each(function () {
            var on = this.checked || this.value === 'popup';
            $('.wpp-col-' + this.value).toggleClass('wpp-col-off', !on);
            if (on) { cols.push(this.value); }
        });
        post('wppoppop_stats_prefs_save', { columns: cols });
    });

    /* ---- per page ---- */
    $(document).on('change', '.wpp-per', function () { this.form.submit(); });

    /* ---- chart popup selection ---- */
    $(document).on('change', '.wpp-chart-cb', function () {
        var n = $('.wpp-chart-cb:checked').length;
        if (n > MAX) { this.checked = false; $('.wpp-pick-msg').text(V.max_msg || ('Max ' + MAX)); }
    });
    $(document).on('click', '#wpp-chart-apply', function () {
        var uids = [];
        $('.wpp-chart-cb:checked').each(function () { uids.push(this.value); });
        var $b = $(this).prop('disabled', true);
        post('wppoppop_stats_prefs_save', { chart_set: 1, chart_uids: uids }).always(function () {
            $b.prop('disabled', false);
            window.location.reload();
        });
    });

    /* ---- close dropdown panels on outside click / Esc ---- */
    $(document).on('click', function (e) {
        $('details.wpp-pick[open]').each(function () {
            if (!$.contains(this, e.target)) { this.removeAttribute('open'); }
        });
    });
    $(document).on('keydown', function (e) {
        if (e.which === 27) { $('details.wpp-pick[open]').removeAttr('open'); }
    });
})(window, jQuery);
