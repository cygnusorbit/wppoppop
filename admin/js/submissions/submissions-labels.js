/**
 * WpPopPop Submissions: per-row label picker (bookmark -> colour menu)
 * Cross-browser: Chrome, Safari, Firefox, Edge (ES5 only)
 */
(function (window, $) {
    'use strict';

    var Labels = {
        $menu: null,
        $row: null,

        vars: function () { return window.wppoppop_vars || { ajax_url: window.ajaxurl || '', nonce: '' }; },

        rowLabels: function ($row) {
            var a = [];
            try { a = JSON.parse($row.find('.wppoppop-label-slot').attr('data-labels') || '[]'); } catch (e) { a = []; }
            return $.isArray(a) ? a : [];
        },

        init: function () {
            var self = this;
            this.$menu = $('#wppoppop-label-menu');
            if (!this.$menu.length) { return; }
            $('body').append(this.$menu);

            $(document).off('.wppLbl');
            $(document).on('click.wppLbl', '.wppoppop-label-btn', function (e) {
                e.preventDefault(); e.stopPropagation();
                self.toggle($(this));
            });
            $(document).on('click.wppLbl', '#wppoppop-label-menu .wppoppop-label-opt', function (e) {
                e.preventDefault(); e.stopPropagation();
                self.choose($(this));
            });
            $(document).on('click.wppLbl', function (e) {
                if (!$(e.target).closest('#wppoppop-label-menu').length) { self.close(); }
            });
            $(document).on('keydown.wppLbl', function (e) {
                if (e.key === 'Escape' || e.keyCode === 27) { self.close(); }
            });
            $(window).on('resize.wppLbl', function () { self.close(); });
        },

        toggle: function ($btn) {
            var $row = $btn.closest('tr');
            var same = this.$row && this.$row[0] === $row[0] && this.$menu.hasClass('is-open');
            this.close();
            if (same) { return; }

            this.$row = $row.addClass('is-menu-open');
            var ids = this.rowLabels($row);
            this.$menu.find('.wppoppop-label-opt').each(function () {
                var on = $.inArray(String($(this).attr('data-label-id')), ids) !== -1;
                $(this).toggleClass('is-on', on).attr('aria-checked', on ? 'true' : 'false');
            });

            var off = $btn.offset();
            this.$menu.css({ top: off.top + $btn.outerHeight() + 4, left: off.left }).addClass('is-open');

            // keep inside the viewport horizontally
            var right = this.$menu.offset().left + this.$menu.outerWidth();
            var maxRight = $(window).scrollLeft() + $(window).width() - 8;
            if (right > maxRight) { this.$menu.css('left', Math.max(8, off.left - (right - maxRight))); }
        },

        close: function () {
            if (this.$menu) { this.$menu.removeClass('is-open'); }
            $('#wppoppop-submissions-table tr.is-menu-open').removeClass('is-menu-open');
            this.$row = null;
        },

        choose: function ($opt) {
            var self = this;
            var $row = this.$row;
            if (!$row || !$row.length) { return; }
            var lid = String($opt.attr('data-label-id'));
            var id = $row.attr('data-id');
            var ids = this.rowLabels($row);
            var has = $.inArray(lid, ids) !== -1;
            var v = this.vars();

            $.post(v.ajax_url, {
                action: 'wppoppop_assign_sub_labels',
                nonce: v.nonce,
                ids: [id],
                label_id: lid,
                mode: has ? 'remove' : 'add'
            }).done(function (res) {
                if (res && res.success) {
                    if (has) { ids = $.grep(ids, function (x) { return x !== lid; }); }
                    else { ids.push(lid); }
                    self.paint($row, ids);
                    self.close();
                } else {
                    window.alert((res && res.data && res.data.message) || 'Could not update label.');
                }
            }).fail(function () {
                window.alert('Network error while updating label.');
            });
        },

        paint: function ($row, ids) {
            var self = this;
            var $slot = $row.find('.wppoppop-label-slot');
            var $dots = $slot.find('.wppoppop-label-dots').empty();
            $slot.attr('data-labels', JSON.stringify(ids)).toggleClass('has-labels', ids.length > 0);
            $.each(ids, function (_, lid) {
                var $o = self.$menu.find('.wppoppop-label-opt[data-label-id="' + lid + '"]');
                if (!$o.length) { return; }
                $('<span class="wppoppop-label-dot"></span>')
                    .css('background', $o.attr('data-color'))
                    .attr('title', $o.attr('data-name'))
                    .appendTo($dots);
            });
        }
    };

    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};
    window.WpPopPopSubmissions.Labels = Labels;
    $(function () { Labels.init(); });
})(window, jQuery);
