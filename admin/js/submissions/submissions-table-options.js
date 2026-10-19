(function(window, $) {
    'use strict';
    window.WpPopPopSubmissions = window.WpPopPopSubmissions || {};

    var COLORS = ['#f0c330', '#c9001a', '#2271b1', '#00a32a', '#8e44ad', '#e67e22'];

    var Opts = {
        cfg: null,

        vars: function() { return window.wppoppop_vars || {}; },

        init: function() {
            var $m = $('#wppoppop-table-options-modal');
            if (!$m.length) return;
            var raw = $m.attr('data-config');
            try { this.cfg = JSON.parse(raw); } catch (e) { return; }
            if (!this.cfg || typeof this.cfg !== 'object') return;
            if (!$.isArray(this.cfg.labels)) this.cfg.labels = [];
            if (!$.isArray(this.cfg.active)) this.cfg.active = [];

            this.bind($m);
        },

        bind: function($m) {
            var self = this;

            $('#wppoppop-subs-popup-filter').on('change', function() {
                var base = $(this).attr('data-base') || window.location.href;
                var v = $(this).val();
                window.location.href = base + (v ? '&popup_uid=' + encodeURIComponent(v) : '');
            });

            $(document).on('click', '#wppoppop-table-options-btn', function(e) {
                e.preventDefault();
                self.open($m);
            });
            $('#wto-close').on('click', function() { $m.removeClass('is-open'); });
            $m.on('mousedown', function(e) { if (e.target === this) $m.removeClass('is-open'); });
            $(document).on('keydown', function(e) { if (e.which === 27) $m.removeClass('is-open'); });

            $('#wto-add-label').on('click', function() {
                var id = 'l' + Date.now().toString(36) + Math.floor(Math.random() * 1000).toString(36);
                var color = COLORS[$('#wto-labels .wto-lbl').length % COLORS.length];
                self.addLabelRow({ id: id, name: 'New label', color: color });
            });
            $('#wto-labels').on('click', '.lbl-del', function(e) {
                e.preventDefault();
                $(this).closest('.wto-lbl').remove();
            });
            $('#wto-labels').on('input change', '.lbl-color', function() {
                $(this).closest('.wto-lbl').css('background', $(this).val());
            });

            $('#wto-add-custom').on('click', function() { self.addCustom(); });
            $('#wto-custom-key').on('keydown', function(e) {
                if (e.which === 13) { e.preventDefault(); self.addCustom(); }
            });

            $('#wto-save').on('click', function() { self.save(); });
        },

        open: function($m) {
            this.render();
            $m.addClass('is-open');
        },

        render: function() {
            var self = this;
            var cfg = this.cfg;

            $('#wto-labels').empty();
            $.each(cfg.labels, function(_, l) { self.addLabelRow(l); });

            var $act = $('#wppoppop-cols-active').empty();
            var $ina = $('#wppoppop-cols-inactive').empty();
            var used = {};
            $.each(cfg.active, function(_, id) {
                if (cfg.available[id] !== undefined) {
                    used[id] = true;
                    $act.append(self.colItem(id, cfg.available[id]));
                }
            });
            $.each(cfg.available, function(id, name) {
                if (!used[id]) $ina.append(self.colItem(id, name));
            });

            if ($.fn.sortable) {
                var $both = $('#wppoppop-cols-active, #wppoppop-cols-inactive');
                if ($both.data('ui-sortable')) $both.sortable('destroy');
                $both.sortable({
                    connectWith: '.wppoppop-cols-list',
                    items: '> li',
                    placeholder: 'wto-ph',
                    tolerance: 'pointer'
                });
            }
            $('#wto-per-page').val(cfg.per_page || 20);
        },

        colItem: function(id, name) {
            return $('<li>').attr('data-col', id).text(name);
        },

        addLabelRow: function(l) {
            var color = /^#[0-9a-fA-F]{6}$/.test(l.color || '') ? l.color : '#f0c330';
            var $row = $('<div class="wto-lbl">').attr('data-id', l.id).css('background', color);
            $row.append($('<input type="text" class="lbl-name" maxlength="40">').val(l.name || ''));
            $row.append($('<input type="color" class="lbl-color">').val(color));
            $row.append($('<a href="#" class="lbl-del dashicons dashicons-trash" title="Delete"></a>'));
            $('#wto-labels').append($row);
        },

        addCustom: function() {
            var $in = $('#wto-custom-key');
            var key = $.trim($in.val()).replace(/[^A-Za-z0-9_\-\. ]/g, '').substring(0, 64);
            if (!key) return;
            var id = 'field:' + key;
            var exists = false;
            $('#wppoppop-cols-active li, #wppoppop-cols-inactive li').each(function() {
                if ($(this).attr('data-col') === id) exists = true;
            });
            if (!exists) {
                var name = key.replace(/[_\-]/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); });
                this.cfg.available[id] = name;
                $('#wppoppop-cols-active').append(this.colItem(id, name));
            }
            $in.val('');
        },

        // Pre-save flush: DOM -> model
        flush: function() {
            var labels = [];
            $('#wto-labels .wto-lbl').each(function() {
                var name = $.trim($(this).find('.lbl-name').val());
                if (!name) return;
                labels.push({
                    id: $(this).attr('data-id'),
                    name: name,
                    color: $(this).find('.lbl-color').val()
                });
            });
            var active = $('#wppoppop-cols-active li').map(function() { return $(this).attr('data-col'); }).get();
            var per = parseInt($('#wto-per-page').val(), 10);
            if (isNaN(per)) per = 20;
            return { labels: labels, columns: active, per_page: Math.max(5, Math.min(200, per)) };
        },

        save: function() {
            var model = this.flush();
            var v = this.vars();
            var $btn = $('#wto-save').prop('disabled', true);
            $.post(v.ajax_url, {
                action: 'wppoppop_save_table_options',
                nonce: v.nonce,
                popup_uid: this.cfg.popup_uid || '',
                config: JSON.stringify(model)
            }).done(function(res) {
                if (res && res.success) {
                    window.location.reload();
                } else {
                    alert('Save failed: ' + (res && res.data && res.data.message ? res.data.message : 'Unknown error'));
                    $btn.prop('disabled', false);
                }
            }).fail(function() {
                alert('Network error while saving table options.');
                $btn.prop('disabled', false);
            });
        }
    };

    window.WpPopPopSubmissions.TableOptions = Opts;
    $(document).ready(function() { Opts.init(); });
})(window, jQuery);
