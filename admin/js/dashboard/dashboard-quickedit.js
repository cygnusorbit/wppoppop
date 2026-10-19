/**
 * WpPopPop Dashboard: Quick Edit + Active toggle
 * Cross-browser: Chrome, Safari, Firefox, Edge (ES5 only)
 */
(function (window, $) {
    'use strict';

    var QE = {
        $row: null,

        vars: function () {
            var v = window.wppoppop_vars || {};
            return {
                ajax_url: v.ajax_url || (typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : ''),
                nonce: v.nonce || $('#wppoppop_admin_nonce_field').val() || ''
            };
        },

        normStatus: function (s) {
            return (s === 'publish' || s === 'published') ? 'publish' : 'draft';
        },

        init: function () {
            var self = this;
            $(document).off('.wppoppopQE');

            $(document).on('click.wppoppopQE', '.wppoppop-quick-edit-btn', function (e) {
                e.preventDefault();
                self.open($(this).closest('tr'));
            });
            $(document).on('click.wppoppopQE', '.wppoppop-qe-btn-cancel', function (e) {
                e.preventDefault();
                self.close();
            });
            $(document).on('click.wppoppopQE', '.wppoppop-qe-btn-save', function (e) {
                e.preventDefault();
                self.save();
            });
            $(document).on('keydown.wppoppopQE', '.wppoppop-qe-active input, .wppoppop-qe-active select', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); self.save(); }
            });
            $(document).on('keydown.wppoppopQE', function (e) {
                if ((e.key === 'Escape' || e.keyCode === 27) && $('.wppoppop-qe-active').length) { self.close(); }
            });
            $(document).on('change.wppoppopQE', '.wppoppop-status-toggle-input', function () {
                self.toggle($(this));
            });
        },

        open: function ($row) {
            if (!$row || !$row.length) { return; }
            this.close();

            var uid = $row.attr('data-uid') || '';
            var title = $row.attr('data-raw-title') || $row.find('a.row-title').text();
            var status = this.normStatus($row.attr('data-status'));
            if (!uid) { return; }

            var $tpl = $('#wppoppop-inline-edit');
            if (!$tpl.length) { return; }

            var $edit = $tpl.clone().removeAttr('id').addClass('wppoppop-qe-active').attr('data-uid', uid);
            // Prevent these fields from being submitted with the GET list form.
            $edit.find('[name]').removeAttr('name');
            $edit.find('td.colspanchange').attr('colspan', $row.children('th, td').length);
            $edit.find('.wppoppop-qe-input-title').val(title);
            $edit.find('.wppoppop-qe-input-uid').val(uid);
            $edit.find('.wppoppop-qe-input-status').val(status);

            this.$row = $row;
            $row.hide().after($edit);
            $edit.find('.wppoppop-qe-input-title').trigger('focus');
        },

        close: function () {
            $('.wppoppop-qe-active').remove();
            if (this.$row) { this.$row.show(); this.$row = null; }
        },

        save: function () {
            var self = this;
            var $edit = $('.wppoppop-qe-active');
            if (!$edit.length || !this.$row) { return; }

            var uid = $edit.attr('data-uid');
            var title = $.trim($edit.find('.wppoppop-qe-input-title').val());
            var status = $edit.find('.wppoppop-qe-input-status').val();
            var $err = $edit.find('.wppoppop-qe-error-notice');
            var $btns = $edit.find('.wppoppop-qe-btn-save, .wppoppop-qe-btn-cancel');
            var $spin = $edit.find('.wppoppop-qe-spinner');

            $err.hide();
            if (!title) {
                $edit.find('.wppoppop-qe-error-message').text('Title cannot be empty.');
                $err.show();
                return;
            }

            var v = this.vars();
            $btns.prop('disabled', true);
            $spin.addClass('is-active');

            $.ajax({
                url: v.ajax_url, type: 'POST', dataType: 'json',
                data: { action: 'wppoppop_quick_edit', nonce: v.nonce, uid: uid, title: title, status: status }
            }).done(function (res) {
                if (res && res.success && res.data) {
                    var $row = self.$row;
                    self.$row = null;
                    $('.wppoppop-qe-active').remove();
                    self.applyRow($row, res.data);
                    self.applyCounts(res.data.counts);
                    $row.show();
                } else {
                    $edit.find('.wppoppop-qe-error-message').text((res && res.data && res.data.message) || 'Update failed.');
                    $err.show();
                }
            }).fail(function () {
                $edit.find('.wppoppop-qe-error-message').text('Server error while saving.');
                $err.show();
            }).always(function () {
                $btns.prop('disabled', false);
                $spin.removeClass('is-active');
            });
        },

        applyRow: function ($row, d) {
            var status = this.normStatus(d.status);
            $row.attr('data-raw-title', d.title).attr('data-status', status);
            $row.find('a.row-title').text(d.title);
            $row.find('.wppoppop-quick-edit-btn').attr('data-title', d.title).attr('data-status', status);
            $row.find('.wppoppop-action-embed').attr('data-status', status);

            var $strong = $row.find('td.column-title > strong').first();
            $strong.find('.post-state').remove();
            if (status !== 'publish') {
                $strong.append(' <span class="post-state">\u2014 Draft</span>');
            }
            $row.find('.wppoppop-status-toggle-input').prop('checked', status === 'publish');
        },

        applyCounts: function (c) {
            if (!c) { return; }
            var keys = ['all', 'active', 'inactive', 'trash'];
            var $links = $('.wppoppop-table-wrap .wppoppop-view-btn');
            for (var i = 0; i < keys.length && i < $links.length; i++) {
                $links.eq(i).find('.count').text('(' + (c[keys[i]] || 0) + ')');
            }
        },

        toggle: function ($input) {
            var self = this;
            var uid = $input.attr('data-uid');
            var want = $input.is(':checked');
            var $sw = $input.closest('.wppoppop-switch');
            var $row = $input.closest('tr');
            var v = this.vars();

            $sw.addClass('is-busy');
            $input.prop('disabled', true);

            $.ajax({
                url: v.ajax_url, type: 'POST', dataType: 'json',
                data: { action: 'wppoppop_toggle_status', nonce: v.nonce, uid: uid, active: want ? 1 : 0 }
            }).done(function (res) {
                if (res && res.success && res.data) {
                    self.applyRow($row, { title: $row.attr('data-raw-title') || $row.find('a.row-title').text(), status: res.data.status });
                    self.applyCounts(res.data.counts);
                    // If a filtered view no longer matches, remove the row.
                    var idx = $('.wppoppop-table-wrap .wppoppop-view-btn').index($('.wppoppop-table-wrap .wppoppop-view-btn.current'));
                    var nowOn = self.normStatus(res.data.status) === 'publish';
                    if ((idx === 1 && !nowOn) || (idx === 2 && nowOn)) {
                        $row.fadeOut(250, function () { $(this).remove(); });
                    }
                } else {
                    $input.prop('checked', !want);
                    window.alert((res && res.data && res.data.message) || 'Could not update status.');
                }
            }).fail(function () {
                $input.prop('checked', !want);
                window.alert('Server error while updating status.');
            }).always(function () {
                $sw.removeClass('is-busy');
                $input.prop('disabled', false);
            });
        }
    };

    window.WpPopPopDashboardQuickEdit = QE;
    $(function () { QE.init(); });
})(window, jQuery);
