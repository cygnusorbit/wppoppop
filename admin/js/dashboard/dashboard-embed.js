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
            $(document).on('click', '.btn-embed-popup, .wppoppop-action-embed', function(e) {
                e.preventDefault();
                var uid = $(this).data('uid') || $(this).closest('tr').find('input[name="popup_id[]"]').val();
                if (!uid) return;

                var scStandard = '[wppoppop uid="' + uid + '"]';
                var scLocker   = '[wppoppop_lock uid="' + uid + '"]Your protected content here...[/wppoppop_lock]';
                var clickBtn   = '<button type="button" class="wppoppop-trigger-btn" data-target-uid="' + uid + '">Open Popup</button>';

                $('#wppoppop-modal-embed-sc').val(scStandard);
                $('#wppoppop-modal-embed-locker').val(scLocker);
                $('#wppoppop-modal-embed-click').val(clickBtn);

                $('#wppoppop-dashboard-embed-modal').css('display', 'flex').fadeIn(150);
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
            $(document).on('click', '.wppoppop-shortcode-chip', function(e) {
                e.preventDefault();
                var text = $(this).text().trim();
                self.copyText(text, $(this));
            });

            // 2. Modal copy button clicks
            $(document).on('click', '.wppoppop-btn-copy-input', function(e) {
                e.preventDefault();
                var targetId = $(this).data('target');
                var text = $('#' + targetId).val();
                if (text) {
                    self.copyText(text, $(this));
                }
            });
        },

        bindClose: function() {
            $(document).on('click', '#wppoppop-dashboard-embed-close, #wppoppop-dashboard-embed-modal', function(e) {
                if (e.target === this || $(this).is('#wppoppop-dashboard-embed-close')) {
                    $('#wppoppop-dashboard-embed-modal').fadeOut(150);
                }
            });
        }
    };

    window.WpPopPopDashboardEmbed = Embed;
})(window, jQuery);
