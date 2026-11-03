/* WPPOPPOP_DATA_TAB_SYNC: Data tab helpers (dynamic default row visibility) */
(function (window, $) {
    'use strict';
    if (!$ || window.__wppDataTabSync) { return; }
    window.__wppDataTabSync = true;

    function sync() {
        $('#prop-dynamic-param-row').toggle($('#prop-dynamic-default').is(':checked'));
    }

    $(document)
        .off('change.wppDataTab click.wppDataTab', '#prop-dynamic-default')
        .on('change.wppDataTab click.wppDataTab', '#prop-dynamic-default', function () {
            setTimeout(sync, 0);
        });

    $(sync);
})(window, window.jQuery);
