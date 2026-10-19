/**
 * WpPopPop Visual Builder: Embed Modal Controller ("How to Embed & Display Popup")
 * Single source of truth for snippets, tabs, copy (with Safari/HTTP fallback) and close.
 */
(function($) {
    'use strict';

    var MODAL = '#wppoppop-modal-embed';
    var PLACEHOLDER = 'YOUR_POPUP_UID';
    var ENS = '.wppoppopEmbed';

    window.WpPopPopBuilderModals = {
        _bound: false,
        _tab: 'shortcode',

        init: function() {
            if (this._bound) return;
            this._bound = true;
            this.bindEmbedModal();
        },

        getUid: function() {
            var uid = '';
            try {
                if (window.WpPopPopBuilderIO && typeof window.WpPopPopBuilderIO.getUid === 'function') {
                    uid = window.WpPopPopBuilderIO.getUid();
                }
            } catch (e) {}
            if (!uid) uid = $('#wppoppop-builder-uid').val() || '';
            return $.trim(String(uid || ''));
        },

        refresh: function() {
            var uid = this.getUid();
            var shown = uid || PLACEHOLDER;
            $('#wppoppop-embed-shortcode-val').val('[wppoppop uid="' + shown + '"]');
            $('#wppoppop-embed-onclick-val').val('<a href="#" class="wppoppop-trigger" data-wppoppop-id="' + shown + '">Open Popup</a>');
            $('#wppoppop-embed-js-val').val("window.WpPopPop && window.WpPopPop.show('" + shown + "');");
            $('.wppoppop-embed-uid').text(shown);
            $('#wppoppop-embed-unsaved').toggle(!uid);
            var st = $.trim(String($('#wppoppop-builder-status').val() || ''));
            $('#wppoppop-embed-draft').toggle(!!uid && st !== '' && st !== 'publish' && st !== 'published');
        },

        switchTab: function(tab) {
            this._tab = tab || 'shortcode';
            $('.wppoppop-embed-tab').removeClass('active')
                .filter('[data-tab="' + this._tab + '"]').addClass('active');
            $('.wppoppop-embed-tab-content').removeClass('active').hide();
            $('#embed-tab-' + this._tab).addClass('active').show();
        },

        openEmbed: function() {
            this.refresh();
            this.switchTab(this._tab);
            $(MODAL).stop(true, true).css({
                display: 'flex', alignItems: 'center', justifyContent: 'center', opacity: 0
            }).animate({ opacity: 1 }, 150);
        },

        closeEmbed: function() {
            var $m = $(MODAL);
            if (!$m.is(':visible')) return;
            $m.stop(true, true).animate({ opacity: 0 }, 120, function() {
                $m.css({ display: 'none', opacity: 1 });
            });
        },

        copyText: function(text, $btn) {
            if (!$btn.data('origHtml')) $btn.data('origHtml', $btn.html());
            var orig = $btn.data('origHtml');

            function ok() {
                $btn.addClass('copied').html('<span class="dashicons dashicons-yes"></span> Copied!');
                setTimeout(function() { $btn.removeClass('copied').html(orig); }, 1600);
            }
            function fallback() {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.cssText = 'position:fixed;top:0;left:-9999px;opacity:0;';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try { ta.setSelectionRange(0, 99999); } catch (e) {}
                var done = false;
                try { done = document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(ta);
                if (done) { ok(); } else { window.prompt('Copy to clipboard (Cmd/Ctrl+C, Enter):', text); }
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(ok, fallback);
            } else {
                fallback();
            }
        },

        bindEmbedModal: function() {
            var self = this;

            $(document).off('click' + ENS, '#wppoppop-btn-embed, #wppoppop-btn-shortcode')
                .on('click' + ENS, '#wppoppop-btn-embed, #wppoppop-btn-shortcode', function(e) {
                    e.preventDefault();
                    self.openEmbed();
                });

            $(document).off('click' + ENS, '.wppoppop-embed-tab')
                .on('click' + ENS, '.wppoppop-embed-tab', function(e) {
                    e.preventDefault();
                    self.switchTab($(this).attr('data-tab'));
                });

            $(document).off('click' + ENS, '.wppoppop-btn-copy')
                .on('click' + ENS, '.wppoppop-btn-copy', function(e) {
                    e.preventDefault();
                    var $btn = $(this);
                    var $input = $('#' + $btn.attr('data-target'));
                    if (!$input.length) return;
                    $input[0].select();
                    self.copyText($input.val(), $btn);
                });

            $(document).off('focus' + ENS, MODAL + ' .wppoppop-code-box input')
                .on('focus' + ENS, MODAL + ' .wppoppop-code-box input', function() { this.select(); });

            $(document).off('click' + ENS, MODAL + ' .wppoppop-modal-close')
                .on('click' + ENS, MODAL + ' .wppoppop-modal-close', function(e) {
                    e.preventDefault();
                    self.closeEmbed();
                });

            $(document).off('mousedown' + ENS, MODAL)
                .on('mousedown' + ENS, MODAL, function(e) {
                    if (e.target === this) self.closeEmbed();
                });

            $(document).off('keydown' + ENS)
                .on('keydown' + ENS, function(e) {
                    if (e.key === 'Escape' || e.keyCode === 27) self.closeEmbed();
                });
        }
    };

    $(function() { window.WpPopPopBuilderModals.init(); });
})(jQuery);
