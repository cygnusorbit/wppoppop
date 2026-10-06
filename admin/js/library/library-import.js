(function(window, $) {
    'use strict';
    window.WpPopPopLibrary = window.WpPopPopLibrary || {};

    var Import = {
        init: function() {
            this.bindDirectImport();
            this.bindModalImport();
        },

        getVars: function() {
            return window.wppoppop_vars || {
                ajax_url: ajaxurl || '',
                nonce: ''
            };
        },

        bindDirectImport: function() {
            var self = this;
            $('.wppoppop-tpl-import-btn').on('click', function() {
                var config = $(this).data('config');
                var title = $(this).data('title');
                self.executeImport(config, title, $(this));
            });
        },

        bindModalImport: function() {
            var self = this;
            $('#wppoppop-lib-modal-import-action').on('click', function() {
                if (window.WpPopPopLibrary.Preview && window.WpPopPopLibrary.Preview.activeConfig) {
                    var title = $('#wppoppop-lib-modal-title').text();
                    self.executeImport(window.WpPopPopLibrary.Preview.activeConfig, title, $(this));
                }
            });
        },

        executeImport: function(configData, title, $btn) {
            var vars = this.getVars();
            $btn.prop('disabled', true).text('Importing...');
            var rawJson = typeof configData === 'string' ? configData : JSON.stringify(configData);

            $.post(vars.ajax_url, {
                action: 'wppoppop_import_popup',
                nonce: vars.nonce,
                import_data: rawJson
            }).done(function(res) {
                if (res.success) {
                    window.location.href = 'admin.php?page=wppoppop-builder';
                } else {
                    alert('Import Error: ' + (res.data ? res.data.message : 'Unable to import template.'));
                    $btn.prop('disabled', false).text('Import & Edit');
                }
            }).fail(function() {
                alert('Network failure occurred during template import.');
                $btn.prop('disabled', false).text('Import & Edit');
            });
        }
    };

    window.WpPopPopLibrary.Import = Import;
})(window, jQuery);
