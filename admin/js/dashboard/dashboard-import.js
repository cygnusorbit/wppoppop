(function(window, $) {
    'use strict';
    window.WpPopPopDashboard = window.WpPopPopDashboard || {};

    var Import = {
        init: function() {
            this.bindModal();
            this.bindFileReader();
            this.bindSubmit();
        },

        bindModal: function() {
            $('#wppoppop-btn-open-import').on('click', function() {
                $('#wppoppop-import-file').val('');
                $('#wppoppop-import-raw').val('');
                $('#wppoppop-import-modal').css('display', 'flex');
            });

            $('#wppoppop-import-close, #wppoppop-import-cancel').on('click', function() {
                $('#wppoppop-import-modal').hide();
            });
        },

        bindFileReader: function() {
            $('#wppoppop-import-file').on('change', function(e) {
                var file = e.target.files[0];
                if (!file) return;

                var reader = new FileReader();
                reader.onload = function(evt) {
                    $('#wppoppop-import-raw').val(evt.target.result);
                };
                reader.readAsText(file);
            });
        },

        bindSubmit: function() {
            $('#wppoppop-import-submit').on('click', function() {
                var raw = $('#wppoppop-import-raw').val().trim();
                if (!raw) {
                    alert('Please select a JSON file or paste valid JSON payload.');
                    return;
                }

                try {
                    JSON.parse(raw);
                } catch(e) {
                    alert('Invalid JSON structure. Please check the code.');
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).text('Importing...');

                var vars = window.wppoppop_vars || { ajax_url: ajaxurl || '', nonce: '' };

                $.post(vars.ajax_url, {
                    action: 'wppoppop_import_popup',
                    nonce: vars.nonce,
                    import_data: raw
                }).done(function(res) {
                    if (res.success) {
                        location.reload();
                    } else {
                        alert('Import Error: ' + (res.data ? res.data.message : 'Unable to import popup.'));
                        $btn.prop('disabled', false).text('Import Popup');
                    }
                }).fail(function() {
                    alert('Network error occurred during popup import.');
                    $btn.prop('disabled', false).text('Import Popup');
                });
            });
        }
    };

    window.WpPopPopDashboard.Import = Import;
})(window, jQuery);
