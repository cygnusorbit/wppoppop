/**
 * WpPopPop Dashboard: Import Modal Controller
 */
(function($) {
    'use strict';

    window.WpPopPopDashboardImport = {
        /* WPPOPPOP_IMPORT_V2 */
        fileName: '',

        init: function() {
            this.bindModal();
            this.bindFile();
            this.bindSubmit();
        },

        getVars: function() {
            return window.wppoppop_vars || {
                ajax_url: (typeof ajaxurl !== 'undefined') ? ajaxurl : '/wp-admin/admin-ajax.php',
                nonce: ''
            };
        },

        openModal: function() {
            $('#wppoppop-import-modal').css('display', 'flex');
        },

        closeModal: function() {
            $('#wppoppop-import-modal').css('display', 'none');
            $('#wppoppop-import-file').val('');
            $('#wppoppop-import-raw').val('');
            this.fileName = '';
        },

        bindModal: function() {
            var self = this;
            $(document).on('click', '#wppoppop-btn-open-import, #wppoppop-import-popup-btn', function(e) {
                e.preventDefault();
                self.openModal();
            });
            $(document).on('click', '#wppoppop-import-close, #wppoppop-import-cancel, .wppoppop-close-import-modal', function(e) {
                e.preventDefault();
                self.closeModal();
            });
            $(document).on('mousedown', '#wppoppop-import-modal', function(e) {
                if (e.target === this) { self.closeModal(); }
            });
            $(document).on('keydown', function(e) {
                if (e.which === 27 && $('#wppoppop-import-modal').css('display') !== 'none') { self.closeModal(); }
            });
        },

        bindFile: function() {
            var self = this;
            $(document).on('change', '#wppoppop-import-file', function() {
                var input = this;
                if (!input.files || !input.files[0]) { return; }
                var file = input.files[0];
                if (!window.FileReader) {
                    alert('Your browser cannot read files. Please paste the JSON instead.');
                    return;
                }
                self.fileName = (file.name || '').replace(/\.json$/i, '');
                var reader = new FileReader();
                reader.onload = function(evt) {
                    $('#wppoppop-import-raw').val(String(evt.target.result || ''));
                };
                reader.onerror = function() {
                    alert('Unable to read the selected file.');
                };
                reader.readAsText(file);
            });
        },

        bindSubmit: function() {
            var self = this;
            $(document).on('click', '#wppoppop-import-submit', function(e) {
                e.preventDefault();
                var $btn = $(this);
                if ($btn.prop('disabled')) { return; }

                var raw = $.trim(String($('#wppoppop-import-raw').val() || '')).replace(/^\uFEFF/, '');
                if (!raw) {
                    alert('Please choose a JSON file or paste JSON first.');
                    return;
                }

                var parsed;
                try { parsed = JSON.parse(raw); } catch (err) {
                    alert('Invalid JSON: ' + err.message);
                    return;
                }
                if (!parsed || typeof parsed !== 'object' || $.isArray(parsed)) {
                    alert('Invalid popup definition. Expected a JSON object.');
                    return;
                }

                var title = '';
                if (parsed.meta && parsed.meta.title) { title = parsed.meta.title; }
                else if (parsed.title) { title = parsed.title; }
                else if (parsed.settings && parsed.settings.title) { title = parsed.settings.title; }
                else if (self.fileName) { title = self.fileName; }

                var vars = self.getVars();
                var origHtml = $btn.html();
                $btn.prop('disabled', true).text('Importing...');

                $.ajax({
                    url: vars.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_import_popup',
                        nonce: vars.nonce,
                        title: title,
                        import_data: raw
                    },
                    success: function(res) {
                        if (res && res.success) {
                            $btn.text('Imported!');
                            setTimeout(function() { window.location.reload(); }, 400);
                        } else {
                            alert((res && res.data && res.data.message) || 'Import failed.');
                            $btn.prop('disabled', false).html(origHtml);
                        }
                    },
                    error: function() {
                        alert('Server error while importing popup.');
                        $btn.prop('disabled', false).html(origHtml);
                    }
                });
            });
        }
    };
})(jQuery);
