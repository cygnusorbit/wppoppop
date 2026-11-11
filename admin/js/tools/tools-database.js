/**
 * WpPopPop Tools: Database Maintenance Sub-Module
 * Manages asynchronous table integrity verification and metric counter flushes.
 */
(function($) {
    'use strict';

    window.WpPopPopToolsDatabase = {
        init: function() {
            this.bindRepair();
            this.bindReset();
        },

        bindRepair: function() {
            var self = this;
            $(document).on('click', '#wppoppop-repair-tables-btn', function(e) {
                e.preventDefault();
                var $btn =$(this);
                var origHtml = $btn.html();
                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';

                $btn.prop('disabled', true).html('Verifying Schema...');

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_repair_tables',
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response && response.success) {
                            var data = response.data;
                            if (data.tables) {
                                $.each(data.tables, function(key, tbl) {
                                    var $row =$('#wppoppop-table-row-' + key);
                                    if ($row.length) {
                                        var $pill =$row.find('.wppoppop-tbl-status-pill');
                                        var $rows =$row.find('.wppoppop-tbl-rows-count');
                                        var $size =$row.find('.wppoppop-tbl-size-val');

                                        $pill.toggleClass('is-ok', !!tbl.exists).toggleClass('is-bad', !tbl.exists).text(tbl.status);

                                        $rows.text(tbl.rows);$size.text(tbl.size);
                                    }
                                });
                            }

                            self.showNotice('success', data.message || wp.i18n.__('Database schema tables verified successfully!', 'wppoppop'));
                            $btn.prop('disabled', false).html('Schema Optimal!');
                            setTimeout(function() {
                                $btn.html(origHtml);
                            }, 2500);
                        } else {
                            var form = document.getElementById('wppoppop-repair-tables-form');
                            if (form) {
                                form.submit();
                            } else {
                                self.showNotice('error', (response.data && response.data.message) ? response.data.message : wp.i18n.__('Database repair failed.', 'wppoppop'));
                                $btn.prop('disabled', false).html(origHtml);
                            }
                        }
                    },
                    error: function() {
                        var form = document.getElementById('wppoppop-repair-tables-form');
                        if (form) {
                            form.submit();
                        } else {
                            self.showNotice('error', wp.i18n.__('Network error while attempting database verification.', 'wppoppop'));
                            $btn.prop('disabled', false).html(origHtml);
                        }
                    }
                });
            });
        },

        bindReset: function() {
            var self = this;
            $(document).on('click', '#wppoppop-reset-counters-btn', function(e) {
                e.preventDefault();

                var confirmed = window.WpPopPopTypedConfirm(
                    'WARNING: Are you sure you want to reset all popup impressions, captured leads numbers, and conversion counts across all campaigns to zero?\n\nThis action cannot be undone.'
                );

                if (!confirmed) {
                    return;
                }

                var $btn =$(this);
                var origHtml = $btn.html();
                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';

                $btn.prop('disabled', true).html('Resetting Counters...');

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_reset_counters',
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response && response.success) {
                            self.showNotice('success', response.data.message || wp.i18n.__('All campaign counters have been reset to zero.', 'wppoppop'));
                            $btn.prop('disabled', false).html('Counters Reset!');
                            setTimeout(function() {
                                $btn.html(origHtml);
                            }, 2500);
                        } else {
                            var form = document.getElementById('wppoppop-reset-counters-form');
                            if (form) {
                                form.submit();
                            } else {
                                self.showNotice('error', (response.data && response.data.message) ? response.data.message : wp.i18n.__('Counter reset failed.', 'wppoppop'));
                                $btn.prop('disabled', false).html(origHtml);
                            }
                        }
                    },
                    error: function() {
                        var form = document.getElementById('wppoppop-reset-counters-form');
                        if (form) {
                            form.submit();
                        } else {
                            self.showNotice('error', wp.i18n.__('Network error while attempting to reset counters.', 'wppoppop'));
                            $btn.prop('disabled', false).html(origHtml);
                        }
                    }
                });
            });
        },

        showNotice: function(type, message) {
            var $notice = $('#wppoppop-db-status-notice');
            if (!$notice.length) {
                return;
            }

            var isSuccess = (type === 'success');
            $notice.css({
                display: 'block',
                background: isSuccess ? '#f0fdf4' : '#fef2f2',
                border: '1px solid ' + (isSuccess ? '#bbf7d0' : '#fecaca'),
                color: isSuccess ? '#166534' : '#991b1b'
            }).html((isSuccess ? '&#10003; ' : '&#9888; ') + message);

            setTimeout(function() {
                $notice.fadeOut(400);
            }, 6000);
        }
    };
})(jQuery);
