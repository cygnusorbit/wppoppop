/**
 * WpPopPop Dashboard: Embed & Shortcode Modal Engine
 * Cross-Browser Compatibility: Safari, Firefox, Chrome, Edge
 */
(function(window, $) {
    'use strict';
    window.WpPopPopDashboardEmbed = window.WpPopPopDashboardEmbed || {};

    var Embed = {
        init: function() {
            this.bindTriggers();
            this.bindCopyButtons();
            this.bindClose();
        },

        bindTriggers: function() {
            var self = this;
            $(document).off('click.wppoppopDashEmbed', '.btn-embed-popup, .wppoppop-action-embed')
                .on('click.wppoppopDashEmbed', '.btn-embed-popup, .wppoppop-action-embed', function(e) {
                    e.preventDefault();
                    var $t = $(this);
                    var $row = $t.closest('tr');
                    var uid = $.trim(String($t.attr('data-uid') || $row.attr('data-uid') || $row.find('input[name="bulk_ids[]"]').val() || ''));
                    if (!uid) return;
                    var status = String($t.attr('data-status') || $row.attr('data-status') || 'publish');
                    self.open(uid, status);
                });
        },

        open: function(uid, status) {
            $('#wppoppop-dash-sc-field').val('[wppoppop uid="' + uid + '"]');
            $('#wppoppop-dash-btnsc-field').val('[wppoppop uid="' + uid + '" mode="button" text="Open Popup"]');
            $('#wppoppop-dash-btn-field').val('<a href="#" class="wppoppop-trigger" data-wppoppop-id="' + uid + '">Open Popup</a>');
            $('#wppoppop-dash-js-field').val("window.WpPopPop && window.WpPopPop.show('" + uid + "');");
            $('#wppoppop-dash-embed-uid').text(uid);
            $('#wppoppop-dash-embed-draft').toggle(status !== 'publish' && status !== 'published');
            $('#wppoppop-dash-embed-modal').stop(true, true).css({
                display: 'flex', alignItems: 'center', justifyContent: 'center', opacity: 0
            }).animate({ opacity: 1 }, 150);
        },

        close: function() {
            var $m = $('#wppoppop-dash-embed-modal');
            if (!$m.is(':visible')) return;
            $m.stop(true, true).animate({ opacity: 0 }, 120, function() {
                $m.css({ display: 'none', opacity: 1 });
            });
        },

        copyText: function(text, $btn) {
            var origHtml = $btn.html();

            function triggerSuccess() {
                $btn.html('<span class="dashicons dashicons-yes" style="font-size:12px;width:12px;height:12px;"></span> Copied!');
                setTimeout(function() {
                    $btn.html(origHtml);
                }, 1500);
            }

            // Universal Cross-Browser Fallback Function (Safari / Non-HTTPS safe)
            function execCommandFallback(str) {
                var textArea = document.createElement('textarea');
                textArea.value = str;
                textArea.style.position = 'fixed';
                textArea.style.top = '0';
                textArea.style.left = '-9999px';
                textArea.style.opacity = '0';
                textArea.setAttribute('readonly', '');
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                textArea.setSelectionRange(0, 99999);

                var successful = false;
                try {
                    successful = document.execCommand('copy');
                } catch (err) {
                    successful = false;
                }
                document.body.removeChild(textArea);
                if (successful) {
                    triggerSuccess();
                } else {
                    window.prompt('Copy to clipboard (Cmd/Ctrl+C, Enter):', str);
                }
            }

            // Modern W3C Clipboard API with Synchronous Fallback
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    triggerSuccess();
                }).catch(function() {
                    execCommandFallback(text);
                });
            } else {
                execCommandFallback(text);
            }
        },

        bindCopyButtons: function() {
            var self = this;

            // 1. Direct table row chip click
            $(document).off('click.wppoppopDashEmbed', '.wppoppop-shortcode-chip').on('click.wppoppopDashEmbed', '.wppoppop-shortcode-chip', function(e) {
                e.preventDefault();
                var text = $(this).text().trim();
                self.copyText(text, $(this));
            });

            // 2. Modal copy button clicks
            $(document).off('click.wppoppopDashEmbed', '.wppoppop-btn-copy-input').on('click.wppoppopDashEmbed', '.wppoppop-btn-copy-input', function(e) {
                e.preventDefault();
                var targetId = $(this).data('target');
                var text = $('#' + targetId).val();
                if (text) {
                    self.copyText(text, $(this));
                }
            });
        },

        bindClose: function() {
            var self = this;
            $(document).off('click.wppoppopDashEmbed', '#wppoppop-dash-embed-close')
                .on('click.wppoppopDashEmbed', '#wppoppop-dash-embed-close', function(e) {
                    e.preventDefault();
                    self.close();
                });
            $(document).off('mousedown.wppoppopDashEmbed', '#wppoppop-dash-embed-modal')
                .on('mousedown.wppoppopDashEmbed', '#wppoppop-dash-embed-modal', function(e) {
                    if (e.target === this) self.close();
                });
            $(document).off('keydown.wppoppopDashEmbed')
                .on('keydown.wppoppopDashEmbed', function(e) {
                    if (e.key === 'Escape' || e.keyCode === 27) self.close();
                });
            $(document).off('focus.wppoppopDashEmbed', '#wppoppop-dash-embed-modal input[readonly]')
                .on('focus.wppoppopDashEmbed', '#wppoppop-dash-embed-modal input[readonly]', function() { this.select(); });
        }
    };

    window.WpPopPopDashboardEmbed = Embed;
})(window, jQuery);
