/**
 * WpPopPop Tools: System Diagnostics Sub-Module
 * Manages clipboard copying of diagnostic health reports and downloadable logs.
 */
(function($) {
    'use strict';

    window.WpPopPopToolsSystem = {
        init: function() {
            this.bindCopyReport();
            this.bindDownloadReport();
        },

        bindCopyReport: function() {
            $(document).on('click', '#wppoppop-copy-system-info-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var origHtml = $btn.html();
                var reportText = $('#wppoppop-system-report-text').val() || '';

                if (!reportText) {
                    alert(wp.i18n.__('System report data is currently unavailable.', 'wppoppop'));
                    return;
                }

                function showSuccess() {
                    $btn.html('Report Copied!');
                    setTimeout(function() {
                        $btn.html(origHtml);
                    }, 2500);
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(reportText).then(showSuccess).catch(function() {
                        fallbackCopy();
                    });
                } else {
                    fallbackCopy();
                }

                function fallbackCopy() {
                    var $textarea = $('#wppoppop-system-report-text');
                    $textarea.css({ display: 'block', position: 'fixed', left: '-9999px', top: '0' });
                    $textarea.focus();
                    $textarea.select();
                    try {
                        var successful = document.execCommand('copy');
                        if (successful) {
                            showSuccess();
                        } else {
                            alert(wp.i18n.__('Unable to copy to clipboard. Please manually copy the report text.', 'wppoppop'));
                        }
                    } catch (err) {
                        alert(wp.i18n.__('Clipboard copy is not supported in this browser. Please use the Download option.', 'wppoppop'));
                    }
                    $textarea.css('display', 'none');
                }
            });
        },

        bindDownloadReport: function() {
            $(document).on('click', '#wppoppop-download-system-info-btn', function(e) {
                e.preventDefault();
                var reportText = $('#wppoppop-system-report-text').val() || '';
                if (!reportText) {
                    alert(wp.i18n.__('System report data is currently unavailable.', 'wppoppop'));
                    return;
                }

                var filename = 'wppoppop-system-report-' + new Date().toISOString().slice(0, 10) + '.txt';
                var blob = new Blob([reportText], { type: 'text/plain;charset=utf-8;' });
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
            });
        }
    };
})(jQuery);
