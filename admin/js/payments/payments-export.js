(function(window, $) {
    'use strict';
    window.WpPopPopPayments = window.WpPopPopPayments || {};

    var Export = {
        init: function() {
            this.bindExport();
        },

        bindExport: function() {
            $('#wppoppop-btn-export-sales-csv').on('click', function() {
                var csv = ['Transaction ID,Customer Email,Popup Campaign,Amount,Currency,Gateway,Status,Date'];
                
                $('.wppoppop-tx-row:visible').each(function() {
                    var cols = [
                        $(this).find('td:nth-child(2)').text().trim(),
                        $(this).find('td:nth-child(3)').text().trim(),
                        $(this).find('td:nth-child(4) strong').text().trim(),
                        $(this).find('td:nth-child(5)').text().replace('$', '').trim(),
                        'USD',
                        $(this).find('td:nth-child(6)').text().trim(),
                        $(this).find('td:nth-child(7)').text().trim(),
                        $(this).find('td:nth-child(8)').text().trim()
                    ];
                    csv.push('"' + cols.join('","') + '"');
                });

                var blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'wppoppop-sales-export.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }
    };

    window.WpPopPopPayments.Export = Export;
})(window, jQuery);
