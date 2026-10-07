(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Modals = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            // Live Preview Click: Auto-save & Redirect to Homepage
            $(document).on('click', '#wppoppop-btn-preview', function(e) {
                e.preventDefault();
                self.launchLivePreview();
            });

            // Embed Dialog Trigger
            $(document).on('click', '#wppoppop-btn-embed', function(e) {
                e.preventDefault();
                self.openEmbedModal();
            });

            // Modal Dismissal
            $(document).on('click', '.wppoppop-modal-close, #wppoppop-modal-backdrop', function(e) {
                e.preventDefault();
                self.closeAll();
            });

            // 1-Click Clipboard Copy
            $(document).on('click', '.wppoppop-copy-btn', function(e) {
                e.preventDefault();
                var targetId = $(this).data('target');
                var $input = $(targetId);
                if ($input.length) {
                    $input.select();
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText($input.val()).then(function() {
                            self.showCopiedFeedback($(e.target));
                        });
                    } else {
                        document.execCommand('copy');
                        self.showCopiedFeedback($(e.target));
                    }
                }
            });
        },

        launchLivePreview: function() {
            var Core = window.WpPopPopBuilder.Core;
            var IO = window.WpPopPopBuilder.IO;
            var $btn = $('#wppoppop-btn-preview');

            var homeUrl = (window.wppoppop_vars && window.wppoppop_vars.home_url) || (window.location.origin + '/');
            var uid = (Core && Core.uid) || (window.wppoppop_vars && window.wppoppop_vars.uid);

            var origHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin" style="margin-right:4px;"></span> Launching...');

            function executeRedirect(targetUid) {
                var separator = homeUrl.indexOf('?') !== -1 ? '&' : '?';
                var targetUrl = homeUrl + separator + 'wppoppop_preview=' + encodeURIComponent(targetUid) + '&t=' + Date.now();
                window.location.href = targetUrl;
            }

            // Save first to ensure the live homepage receives current modifications
            if (IO && typeof IO.save === 'function') {
                IO.save(function(savedUid) {
                    var finalUid = savedUid || uid;
                    executeRedirect(finalUid);
                }, function(err) {
                    if (uid) {
                        executeRedirect(uid);
                    } else {
                        $btn.prop('disabled', false).html(origHtml);
                        alert('Could not prepare preview. Please save the popup first.');
                    }
                });
            } else if (uid) {
                executeRedirect(uid);
            } else {
                $btn.prop('disabled', false).html(origHtml);
                alert('No active popup ID found to preview.');
            }
        },

        openEmbedModal: function() {
            var Core = window.WpPopPopBuilder.Core;
            var uid = (Core && Core.uid) || (window.wppoppop_vars && window.wppoppop_vars.uid) || 'popup_demo';
            
            $('#embed-shortcode-standard').val('[wppoppop uid="' + uid + '"]');
            $('#embed-shortcode-locker').val('[wppoppop_locker uid="' + uid + '"]Your protected content here...[/wppoppop_locker]');
            $('#embed-html-button').val('<button type="button" class="wppoppop-trigger-btn" data-wppoppop-trigger="' + uid + '">Open Offer</button>');
            $('#embed-remote-js').val('<script src="' + ((window.wppoppop_vars && window.wppoppop_vars.site_url) || '') + 'wp-content/plugins/wppoppop/front/js/embed.js" data-wppoppop-embed="' + uid + '"><\/script>');

            $('#wppoppop-builder-embed-modal').show();
            $('#wppoppop-modal-backdrop').show();
        },

        showCopiedFeedback: function($btn) {
            var origText = $btn.text();
            $btn.text('Copied!');
            setTimeout(function() {
                $btn.text(origText);
            }, 1800);
        },

        closeAll: function() {
            $('.wppoppop-modal-dialog').hide();
            $('#wppoppop-modal-backdrop').hide();
        }
    };

    window.WpPopPopBuilder.Modals = Modals;
})(window, jQuery);
