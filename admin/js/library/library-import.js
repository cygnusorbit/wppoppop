/**
 * WpPopPop Popups Library: 1-Click Template Cloning Engine
 * Cross-Browser Compatibility: Safari, Firefox, Chrome, Edge
 */
(function(window, $) {
    'use strict';
    window.WpPopPopLibraryImport = window.WpPopPopLibraryImport || {};

    var Import = {
        init: function() {
            this.bindImportButtons();
        },

        getVars: function() {
            return window.wppoppop_vars || {
                ajax_url: typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php',
                nonce: ''
            };
        },

        bindImportButtons: function() {
            var self = this;

            $(document).on('click', '.wppoppop-tpl-import-btn, #wppoppop-lib-modal-import-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                if ($btn.prop('disabled')) return;

                var templateKey = $btn.attr('data-template-key') || $btn.closest('.wppoppop-template-card').data('template-key');
                var templateTitle = $btn.closest('.wppoppop-template-card').find('.wppoppop-tpl-info h3').text() || 'Starter Template';

                if (!templateKey) {
                    alert('Unable to identify template configuration.');
                    return;
                }

                var origText = $btn.html();
                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin" style="font-size:14px;width:14px;height:14px;"></span> Cloning...');

                var vars = self.getVars();

                // Construct initial canvas definitions based on starter template
                var starterConfig = self.buildTemplateConfig(templateKey, templateTitle);

                $.ajax({
                    url: vars.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_import_popup',
                        nonce: vars.nonce,
                        title: templateTitle,
                        import_data: JSON.stringify(starterConfig)
                    },
                    success: function(res) {
                        if (res && res.success) {
                            $btn.html('<span class="dashicons dashicons-yes"></span> Ready!');
                            var targetUid = (res.data && res.data.uid) ? res.data.uid : '';
                            setTimeout(function() {
                                if (targetUid) {
                                    window.location.href = '/wp-admin/admin.php?page=wppoppop-builder&uid=' + targetUid;
                                } else {
                                    window.location.href = '/wp-admin/admin.php?page=wppoppop';
                                }
                            }, 500);
                        } else {
                            alert((res && res.data && res.data.message) || 'Template import failed.');
                            $btn.prop('disabled', false).html(origText);
                        }
                    },
                    error: function() {
                        alert('Server communication error while cloning template.');
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });
        },

        buildTemplateConfig: function(key, title) {
            return {
                meta: { title: title },
                box: { width: 640, height: 400, bg_mode: 'solid', bg_color: '#ffffff' },
                canvases: {
                    1: [
                        { id: 't1', type: 'text', name: 'Headline', top: 50, left: 60, width: 520, height: 50, content: title, fontSize: 22, fontWeight: '700', textAlign: 'center' },
                        { id: 't2', type: 'email', name: 'Email Field', top: 130, left: 120, width: 400, height: 44, content: 'Enter your email address...', field_name: 'email' },
                        { id: 't3', type: 'submit', name: 'Submit Button', top: 195, left: 160, width: 320, height: 44, content: 'Get Started Now &rarr;', bgColor: '#2563eb', color: '#ffffff' }
                    ],
                    2: [
                        { id: 't4', type: 'text', name: 'Success Message', top: 120, left: 60, width: 520, height: 60, content: 'Thank you! Your submission was received.', fontSize: 18, fontWeight: '700', textAlign: 'center' }
                    ]
                },
                canvasMeta: {
                    1: { name: 'Canvas 1', width: 640, height: 400 },
                    2: { name: 'Canvas 2', width: 640, height: 400 }
                },
                settings: {
                    box: { width: 640, height: 400 },
                    triggers: { load: true, load_delay: 2 }
                }
            };
        }
    };

    window.WpPopPopLibraryImport = Import;
})(window, jQuery);
