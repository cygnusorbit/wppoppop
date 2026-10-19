(function(window, $) {
    'use strict';
    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};

    var PER_PAGE = parseInt($('#wppoppop-submissions-table').attr('data-per-page'), 10) || 20;

    var List = {
        filter: 'all',
        term: '',
        page: 1,

        getVars: function() {
            return window.wppoppop_vars || {};
        },

        rows: function() {
            return $('#wppoppop-submissions-body .wppoppop-sub-row');
        },

        matched: function() {
            var self = this;
            return this.rows().filter(function() {
                var $r = $(this);
                if (self.filter === 'unread') { if (!$r.hasClass('is-unread')) return false; }
                else if (self.filter !== 'all' && $r.attr('data-status') !== self.filter) return false;
                if (self.term) {
                    var hay = ($r.attr('data-email') || '') + ' ' + ($r.attr('data-title') || '') + ' ' + $r.text().toLowerCase();
                    if (hay.indexOf(self.term) === -1) return false;
                }
                return true;
            });
        },

        render: function() {
            var $all = this.rows();
            var $m = this.matched();
            var total = $m.length;
            var pages = Math.max(1, Math.ceil(total / PER_PAGE));
            if (this.page > pages) this.page = pages;
            if (this.page < 1) this.page = 1;

            $all.addClass('wppoppop-hidden');
            $m.slice((this.page - 1) * PER_PAGE, this.page * PER_PAGE).removeClass('wppoppop-hidden');

            $('.wppoppop-pg-cur').text(this.page);
            $('.wppoppop-pg-total').text(pages);
            $('.wppoppop-pager .displaying-num').text(total + (total === 1 ? ' item' : ' items'));
            $('.wppoppop-pg-first, .wppoppop-pg-prev').prop('disabled', this.page <= 1);
            $('.wppoppop-pg-next, .wppoppop-pg-last').prop('disabled', this.page >= pages);

            $('[data-count="all"]').text($all.length);
            $('[data-count="pending"]').text($all.filter('[data-status="pending"]').length);
            $('[data-count="confirmed"]').text($all.filter('[data-status="confirmed"]').length);
            $('[data-count="unread"]').text($all.filter('.is-unread').length);
            $('.wppoppop-check-all').prop('checked', false);
        },

        counts: function() {
            var $all = this.rows();
            $('[data-count="all"]').text($all.length);
            $('[data-count="pending"]').text($all.filter('[data-status="pending"]').length);
            $('[data-count="confirmed"]').text($all.filter('[data-status="confirmed"]').length);
            $('[data-count="unread"]').text($all.filter('.is-unread').length);
        },

        selectedIds: function() {
            return $('.wppoppop-row-check:checked').map(function() { return $(this).val(); }).get();
        },

        bulk: function(action) {
            var self = this;
            var ids = this.selectedIds();
            if (!action || !ids.length) return;
            if (action.indexOf('label:') === 0 || action === 'unlabel') {
                var v0 = this.getVars();
                $.post(v0.ajax_url, {
                    action: 'wppoppop_assign_sub_labels',
                    nonce: v0.nonce,
                    ids: ids,
                    label_id: action.indexOf('label:') === 0 ? action.substr(6) : '',
                    mode: action === 'unlabel' ? 'clear' : 'add'
                }).done(function(res) {
                    if (res && res.success) { window.location.reload(); }
                    else { alert('Label update failed.'); }
                }).fail(function() { alert('Network error while updating labels.'); });
                return;
            }
            var vars = this.getVars();
            var ajaxAction = action === 'delete' ? 'wppoppop_delete_submission' : 'wppoppop_anonymize_submission';
            var msg = action === 'delete' ? 'Delete ' + ids.length + ' submission(s)? This cannot be undone.' : 'Anonymize PII for ' + ids.length + ' submission(s)?';
            if (!window.confirm(msg)) return;

            var done = 0;
            (function next() {
                if (!ids.length) {
                    if (action === 'anonymize') { window.location.reload(); return; }
                    self.render();
                    return;
                }
                var id = ids.shift();
                $.post(vars.ajax_url, { action: ajaxAction, nonce: vars.nonce, id: id }).done(function(res) {
                    if (res && res.success && action === 'delete') {
                        $('.wppoppop-sub-row[data-id="' + id + '"]').remove();
                    }
                }).always(function() { done++; next(); });
            })();
        },

        sortBy: function($th) {
            var idx = $th.index();
            var type = $th.data('sort');
            var dir = $th.data('dir') === 'asc' ? 'desc' : 'asc';
            $th.data('dir', dir);
            var $body = $('#wppoppop-submissions-body');
            var arr = this.rows().get();
            arr.sort(function(a, b) {
                var av, bv;
                if (type === 'date') {
                    av = parseInt($(a).attr('data-ts'), 10) || 0;
                    bv = parseInt($(b).attr('data-ts'), 10) || 0;
                } else {
                    av = ($(a).children().eq(idx).attr('data-sortval') || '');
                    bv = ($(b).children().eq(idx).attr('data-sortval') || '');
                }
                if (av < bv) return dir === 'asc' ? -1 : 1;
                if (av > bv) return dir === 'asc' ? 1 : -1;
                return 0;
            });
            $.each(arr, function(_, r) { $body.append(r); });
            this.render();
        },

        init: function() {
            var self = this;
            if (!$('#wppoppop-submissions-table').length) return;

            $('#wppoppop-subs-tabs').on('click', 'button', function() {
                $('#wppoppop-subs-tabs button').removeClass('active');
                $(this).addClass('active');
                self.filter = $(this).data('filter');
                self.page = 1;
                self.render();
            });

            $(document).on('wppoppop:subs-search', function(e, term) {
                self.term = term || '';
                self.page = 1;
                self.render();
            });

            $(document).on('click', '.wppoppop-pg-first', function() { self.page = 1; self.render(); });
            $(document).on('click', '.wppoppop-pg-prev',  function() { self.page--; self.render(); });
            $(document).on('click', '.wppoppop-pg-next',  function() { self.page++; self.render(); });
            $(document).on('click', '.wppoppop-pg-last',  function() { self.page = 999999; self.render(); });

            $(document).on('change', '.wppoppop-check-all', function() {
                var on = $(this).is(':checked');
                $('.wppoppop-check-all').prop('checked', on);
                $('.wppoppop-sub-row:not(.wppoppop-hidden) .wppoppop-row-check').prop('checked', on);
            });

            $(document).on('click', '.wppoppop-bulk-apply', function() {
                var action = $(this).siblings('.wppoppop-bulk-select').val();
                self.bulk(action);
            });

            $('#wppoppop-submissions-table thead').on('click', 'th.wppoppop-sortable', function() {
                self.sortBy($(this));
            });

            // Refresh counts after single-row delete handled by the Actions module.
            $(document).ajaxComplete(function(e, xhr, settings) {
                if (settings && settings.data && String(settings.data).indexOf('wppoppop_delete_submission') !== -1) {
                    setTimeout(function() { self.render(); }, 450);
                }
            });

            $(document).on('wppoppop:subs-read-changed', function() { self.counts(); });

            this.render();
        }
    };

    window.WpPopPopSubmissions.List = List;
    $(document).ready(function() { List.init(); });
})(window, jQuery);
