/**
 * WpPopPop Popups Library: Template Inspection Modal Engine
 * Cross-Browser Compatibility: Safari, Firefox, Chrome, Edge
 */
(function(window, $) {
    'use strict';
    window.WpPopPopLibraryPreview = window.WpPopPopLibraryPreview || {};

    var Preview = {
        init: function() {
            this.bindTriggers();
            this.bindClose();
        },

        bindTriggers: function() {
            $(document).on('click', '.wppoppop-tpl-preview-btn', function(e) {
                e.preventDefault();
                var $card = $(this).closest('.wppoppop-template-card');
                var title = $card.find('.wppoppop-tpl-info h3').text() || 'Starter Template';
                var desc = $card.find('.wppoppop-tpl-info p').text() || '';
                var category = $card.find('.wppoppop-tpl-badge').text() || 'Standard';
                var elementsCount = $card.find('.wppoppop-tpl-count').text() || '4 Elements';
                var templateKey = $card.data('template-key') || '';

                $('#wppoppop-modal-tpl-title').text(title);
                $('#wppoppop-modal-tpl-desc').text(desc);
                $('#wppoppop-modal-spec-category').text(category);
                $('#wppoppop-modal-spec-elements').text(elementsCount);
                $('#wppoppop-modal-spec-canvas').text('640 × 400 px');

                $('#wppoppop-lib-modal-import-btn').attr('data-template-key', templateKey);

                $('#wppoppop-lib-preview-modal').css('display', 'flex').fadeIn(150);
            });
        },

        bindClose: function() {
            $(document).on('click', '.wppoppop-lib-modal-close, #wppoppop-lib-preview-modal', function(e) {
                if (e.target === this || $(this).hasClass('wppoppop-lib-modal-close')) {
                    $('#wppoppop-lib-preview-modal').fadeOut(150);
                }
            });

            // Cross-browser ESC key listener
            $(document).on('keydown', function(e) {
                if ((e.key === 'Escape' || e.keyCode === 27) && $('#wppoppop-lib-preview-modal').is(':visible')) {
                    $('#wppoppop-lib-preview-modal').fadeOut(150);
                }
            });
        }
    };

    window.WpPopPopLibraryPreview = Preview;
})(window, jQuery);
