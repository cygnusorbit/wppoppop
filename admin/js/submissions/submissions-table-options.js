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
            $('#wto-close').on('click', function() { self.close(); });
            $m.on('mousedown', function(e) { if (e.target === this) self.close(); });
            $(document).on('keydown', function(e) { if (e.which === 27 && $m.hasClass('is-open')) self.close(); });

            // ---- Autosave triggers ----------------------------------------------------
            $m.on('input change', '.lbl-name, .lbl-color, #wto-per-page', function() { self.queueSave(); });
            $m.on('click', '.lbl-del, #wto-add-label, #wto-add-custom', function() {
                setTimeout(function() { self.queueSave(); }, 0);   // after the row was added/removed
            });
            $('#wto-custom-key').on('keydown', function(e) {
                if (e.which === 13) { setTimeout(function() { self.queueSave(); }, 0); }
            });

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

            $('#wto-save').attr('title', 'Done').on('click', function() { self.close(); });

            $('#wto-reset-cols').on('click', function() {
                var pf = $.isArray(self.cfg.popup_fields) ? self.cfg.popup_fields : [];
                if (!pf.length) { alert('This popup has no saved form fields yet.'); return; }
                var keep = self.flush();               // preserve unsaved label / per-page edits
                self.cfg.labels = keep.labels;
                self.cfg.per_page = keep.per_page;
                self.cfg.active = ['date'].concat(pf.slice(0, 12));
                self.render();
                self.queueSave();
            });
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
                    tolerance: 'pointer',
                    update: function() { self.queueSave(); }
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

        dirty: false,
        timer: null,
        inflight: false,
        again: false,
        waiters: [],

        status: function(msg, ok) {
            var $s = $('#wto-autosave-status');
            if (!$s.length) {
                $s = $('<span id="wto-autosave-status" style="margin-left:12px;font-size:11px;font-weight:400;"></span>');
                $('#wppoppop-table-options-modal h2').append($s);
            }
            $s.css('color', ok === false ? '#b32d2e' : '#2e7d32').text(msg);
        },

        queueSave: function() {
            var self = this;
            this.dirty = true;
            this.status('Saving\u2026', true);
            clearTimeout(this.timer);
            this.timer = setTimeout(function() { self.timer = null; self.saveNow(); }, 500);
        },

        // Pre-save flush (DOM -> model) then POST. Serialised: never two requests at once.
        saveNow: function(cb) {
            var self = this;
            if (typeof cb === 'function') this.waiters.push(cb);
            if (this.timer) { clearTimeout(this.timer); this.timer = null; }
            if (this.inflight) { this.again = true; return; }

            this.inflight = true;
            this.again = false;
            var model = this.flush();
            var v = this.vars();
            var ok = false;

            $.post(v.ajax_url, {
                action: 'wppoppop_save_table_options',
                nonce: v.nonce,
                popup_uid: this.cfg.popup_uid || '',
                config: JSON.stringify(model)
            }).done(function(res) {
                if (res && res.success) {
                    ok = true;
                    self.status('\u2713 Saved', true);
                } else {
                    self.status('Save failed: ' + ((res && res.data && res.data.message) ? res.data.message : 'unknown error'), false);
                }
            }).fail(function(xhr) {
                var hint = (xhr && xhr.status) ? ' (HTTP ' + xhr.status + ')' : '';
                self.status('Save failed' + hint + ' \u2013 check connection', false);
            }).always(function() {
                self.inflight = false;
                if (self.again) { self.saveNow(); return; }
                if (ok) { self.dirty = false; self.reloadNeeded = true; }
                var w = self.waiters.splice(0);
                $.each(w, function(_, fn) { fn(ok); });
            });
        },

        close: function() {
            var self = this;
            var $m = $('#wppoppop-table-options-modal');
            var done = function(ok) {
                $m.removeClass('is-open');
                if (ok && self.reloadNeeded) {
                    self.reloadNeeded = false;
                    window.location.reload();
                }
            };
            if (this.dirty || this.timer || this.inflight) {
                this.saveNow(done);       // wait for the latest state to be stored, then refresh once
            } else {
                done(true);
            }
        }
    };

    window.WpPopPopSubmissions.TableOptions = Opts;
    $(document).ready(function() { Opts.init(); });
})(window, jQuery);
