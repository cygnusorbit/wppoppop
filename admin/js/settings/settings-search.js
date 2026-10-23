/**
 * WpPopPop Settings: live search across all tabs (dash-bar field)
 */
(function (window, $) {
    'use strict';

    var Search = {
        timer: null,
        prevTab: null,
        active: false,

        init: function () {
            this.$in = $('#wppoppop-settings-search');
            if (!this.$in.length || this.bound) { return; }
            this.bound = true;
            this.$clear = $('#wppoppop-settings-search-clear');
            this.$count = $('#wppoppop-settings-search-count');
            this.bind();
        },

        norm: function (s) {
            return String(s || '').toLowerCase().replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
        },

        currentTab: function () {
            return $('.wppoppop-tab-btn.active').first().data('tab') || 'general';
        },

        goTab: function (key) {
            var T = window.WpPopPopSettingsTabs || (window.WpPopPopSettings && window.WpPopPopSettings.Tabs);
            if (T && T.switchTab) { T.switchTab(key); }
        },

        bind: function () {
            var self = this;
            this.$in.on('input keyup change', function (e) {
                if (e.type === 'keyup' && (e.which === 13 || e.which === 27)) { return; }
                clearTimeout(self.timer);
                self.timer = setTimeout(function () { self.run(); }, 110);
            });
            this.$in.on('keydown', function (e) {
                if (e.which === 13) { e.preventDefault(); self.jumpToFirst(); }
                else if (e.which === 27) { e.preventDefault(); self.clear(); }
            });
            this.$clear.on('click', function (e) { e.preventDefault(); self.clear(); self.$in.trigger('focus'); });
            $(document).on('keydown', function (e) {
                if (e.which !== 191 || e.ctrlKey || e.metaKey || e.altKey) { return; }
                var t = e.target, tag = (t && t.tagName) ? t.tagName.toLowerCase() : '';
                if (tag === 'input' || tag === 'textarea' || tag === 'select' || (t && t.isContentEditable)) { return; }
                e.preventDefault();
                self.$in.trigger('focus').select();
            });
        },

        haystack: function ($item) {
            var parts = [$item.text()];
            $item.find('input, select, textarea, button').each(function () {
                var $f = $(this);
                parts.push($f.attr('name') || '', $f.attr('placeholder') || '', $f.attr('title') || '', $f.attr('aria-label') || '');
            });
            var $h = $item.closest('table').prev('h3');
            if ($h.length) { parts.push($h.text()); }
            return this.norm(parts.join(' '));
        },

        run: function () {
            var self = this;
            var q = this.norm(this.$in.val());
            if (q === '') { this.reset(); return; }

            var terms = q.split(' ');
            if (!this.active) { this.prevTab = this.currentTab(); this.active = true; }
            this.$clear.prop('hidden', false);

            var total = 0, firstTab = null, perTab = {};

            $('.wppoppop-tab-content').each(function () {
                var $tab = $(this), key = $tab.attr('data-tab-content'), hits = 0, items = [];

                $tab.find('tr, li').each(function () {
                    var $i = $(this);
                    if ($i.parent().closest('tr, li', $tab[0]).length) { return; }
                    items.push($i);
                });

                if (items.length) {
                    $.each(items, function (_, $i) {
                        var hay = self.haystack($i), ok = true, n;
                        for (n = 0; n < terms.length; n++) {
                            if (hay.indexOf(terms[n]) === -1) { ok = false; break; }
                        }
                        $i.toggleClass('wpp-s-hide', !ok).toggleClass('wpp-s-hit', ok);
                        if (ok) { hits++; }
                    });
                } else {
                    var th = self.norm($tab.text()), allOk = true, m;
                    for (m = 0; m < terms.length; m++) {
                        if (th.indexOf(terms[m]) === -1) { allOk = false; break; }
                    }
                    hits = allOk ? 1 : 0;
                }

                $tab.find('table.form-table').each(function () {
                    var $t = $(this), vis = $t.find('tr').not('.wpp-s-hide').length > 0;
                    $t.toggleClass('wpp-s-hide', !vis);
                    $t.prev('h3').toggleClass('wpp-s-hide', !vis);
                });

                perTab[key] = hits;
                total += hits;
                if (hits && firstTab === null) { firstTab = key; }
            });

            $('.wppoppop-tab-btn').each(function () {
                var $b = $(this), key = $b.data('tab'), n = perTab[key] || 0;
                $b.find('.wpp-s-badge').remove();
                $b.toggleClass('wpp-s-dim', n === 0);
                if (n > 0) { $b.append('<span class="wpp-s-badge">' + n + '</span>'); }
            });

            this.$count.toggleClass('is-empty', total === 0)
                .text(total === 0 ? 'No matches' : total + (total === 1 ? ' result' : ' results'));

            if (firstTab !== null && !perTab[this.currentTab()]) { this.goTab(firstTab); }
            this.firstTab = firstTab;
        },

        jumpToFirst: function () {
            if (!this.active) { return; }
            var $hit = $('.wppoppop-tab-content.active .wpp-s-hit').first();
            if (!$hit.length && this.firstTab) {
                this.goTab(this.firstTab);
                $hit = $('.wppoppop-tab-content.active .wpp-s-hit').first();
            }
            if ($hit.length && $hit[0].scrollIntoView) {
                try { $hit[0].scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                catch (e) { $hit[0].scrollIntoView(); }
            }
        },

        reset: function () {
            $('.wppoppop-settings-wrap .wpp-s-hide').removeClass('wpp-s-hide');
            $('.wppoppop-settings-wrap .wpp-s-hit').removeClass('wpp-s-hit');
            $('.wppoppop-tab-btn').removeClass('wpp-s-dim').find('.wpp-s-badge').remove();
            this.$count.removeClass('is-empty').text('');
            this.$clear.prop('hidden', true);
            if (this.active && this.prevTab && this.prevTab !== this.currentTab()) { this.goTab(this.prevTab); }
            this.active = false;
            this.prevTab = null;
        },

        clear: function () {
            this.$in.val('');
            this.reset();
        }
    };

    window.WpPopPopSettingsSearch = Search;
    $(function () { Search.init(); });
})(window, jQuery);
