/**
 * WpPopPop Tools: Portability Sub-Module
 * Handles instant client-side bulk JSON backups & schema validation.
 */
(function($) {
    'use strict';

    window.WpPopPopToolsPortability = {
        init: function() {
            this.bindExport();
            this.bindImport();
        },

        bindExport: function() {
            $(document).on('click', '#wppoppop-bulk-export-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var origText = $btn.html();
                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';

                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation:rotation 2s infinite linear;font-size:16px;width:16px;height:16px;"></span> Generating Backup...');

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'wppoppop_bulk_export',
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response && response.success && response.data) {
                            var payload = response.data.payload;
                            var filename = response.data.filename || ('wppoppop-bulk-backup-' + new Date().toISOString().slice(0, 10) + '.json');
                            var jsonString = JSON.stringify(payload, null, 2);

                            var blob = new Blob([jsonString], { type: 'application/json;charset=utf-8;' });
                            var downloadUrl = URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = downloadUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();

                            setTimeout(function() {
                                document.body.removeChild(a);
                                URL.revokeObjectURL(downloadUrl);
                            }, 100);

                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-yes" style="font-size:16px;width:16px;height:16px;color:#10b981;"></span> Download Started!');
                            setTimeout(function() {
                                $btn.html(origText);
                            }, 2500);
                        } else {
                            var $form = $('#wppoppop-bulk-export-form');
                            if ($form.length) {
                                $form.off('submit').submit();
                            } else {
                                alert((response.data && response.data.message) ? response.data.message : 'Backup generation failed.');
                                $btn.prop('disabled', false).html(origText);
                            }
                        }
                    },
                    error: function() {
                        var $form = $('#wppoppop-bulk-export-form');
                        if ($form.length) {
                            $form.off('submit').submit();
                        } else {
                            alert('Server communication error. Please try again.');
                            $btn.prop('disabled', false).html(origText);
                        }
                    }
                });
            });
        },

        bindImport: function() {
            var self = this;
            $(document).on('submit', '#wppoppop-bulk-import-form', function(e) {
                var fileInput = document.getElementById('wppoppop-bulk-import-file');
                if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                    return; // Let browser HTML5 validation catch missing file
                }

                var file = fileInput.files[0];
                if (file.type && file.type !== 'application/json' && !file.name.endsWith('.json')) {
                    alert('Please select a valid JSON file (*.json).');
                    e.preventDefault();
                    return;
                }

                // If FileReader is supported, validate and upload via AJAX for instant feedback
                if (window.FileReader && window.FormData) {
                    e.preventDefault();
                    var $btn = $('#wppoppop-bulk-import-btn');
                    var origText = $btn.html();
                    var $status = $('#wppoppop-import-status-text');

                    $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="animation:rotation 2s infinite linear;font-size:16px;width:16px;height:16px;"></span> Validating...');
                    $status.show().text('Reading backup archive...');

                    var reader = new FileReader();
                    reader.onload = function(evt) {
                        try {
                            var parsed = JSON.parse(evt.target.result);
                            if (!parsed || (typeof parsed !== 'object') || (!parsed.items && !parsed.campaigns)) {
                                alert('Invalid backup file. The selected JSON does not contain valid WpPopPop campaigns or items.');
                                $btn.prop('disabled', false).html(origText);
                                $status.hide();
                                return;
                            }

                            $btn.html('<span class="dashicons dashicons-update" style="animation:rotation 2s infinite linear;font-size:16px;width:16px;height:16px;"></span> Restoring...');
                            $status.text('Importing popups and A/B tests...');

                            var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : ajaxurl;
                            var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : '';

                            var formData = new FormData();
                            formData.append('action', 'wppoppop_bulk_import');
                            formData.append('nonce', nonce);
                            formData.append('import_data', evt.target.result);

                            $.ajax({
                                url: ajaxUrl,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                success: function(response) {
                                    if (response && response.success) {
                                        $btn.html('<span class="dashicons dashicons-yes" style="font-size:16px;width:16px;height:16px;color:#10b981;"></span> Restored!');
                                        $status.css('color', '#10b981').text(response.data.message || 'Restoration complete!');
                                        setTimeout(function() {
                                            window.location.reload();
                                        }, 1400);
                                    } else {
                                        var errMsg = (response.data && response.data.message) ? response.data.message : 'Import failed.';
                                        alert('Import Error: ' + errMsg);
                                        $btn.prop('disabled', false).html(origText);
                                        $status.hide();
                                    }
                                },
                                error: function() {
                                    // Fallback to native submission on AJAX failure
                                    var form = document.getElementById('wppoppop-bulk-import-form');
                                    if (form) {
                                        form.submit();
                                    } else {
                                        alert('Network error while processing import.');
                                        $btn.prop('disabled', false).html(origText);
                                        $status.hide();
                                    }
                                }
                            });

                        } catch (err) {
                            alert('JSON Syntax Error: The file contents could not be parsed as valid JSON.');
                            $btn.prop('disabled', false).html(origText);
                            $status.hide();
                        }
                    };

                    reader.onerror = function() {
                        alert('Could not read the selected file.');
                        $btn.prop('disabled', false).html(origText);
                        $status.hide();
                    };

                    reader.readAsText(file);
                }
            });
        }
    };
})(jQuery);
