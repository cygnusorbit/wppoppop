/* Side Tabs: front end.
 * Builds one sticky tab per popup that carries data-sidetab / data-sidetab-label (printed by WpPopPop_Side_Tabs,
 * only while the module is installed and switched on). Clicking a tab opens its popup, even if the visitor closed it before.
 * Skips: builder preview and inline popups. ES5 only (Chrome, Safari, Firefox, Edge). */
(function(window, document, $) {
    'use strict';
    if (!$ || window.__wppSideTabs) return;
    window.__wppSideTabs = true;

    var CSS = '.wppoppop-st-stack{position:fixed;top:50%;z-index:99990;display:flex;flex-direction:column;gap:8px;-webkit-transform:translateY(-50%);transform:translateY(-50%);}' +
        '.wppoppop-st-stack.wppoppop-st-left{left:0;align-items:flex-start;}' +
        '.wppoppop-st-stack.wppoppop-st-right{right:0;align-items:flex-end;}' +
        '.wppoppop-st-tab{margin:0;border:0;background:#c2185b;color:#fff;font:700 13px/1.2 inherit;font-family:inherit;padding:10px 14px;cursor:pointer;' +
        '-webkit-writing-mode:vertical-rl;-ms-writing-mode:tb-rl;writing-mode:vertical-rl;box-shadow:0 4px 12px rgba(0,0,0,.2);' +
        '-webkit-user-select:none;-moz-user-select:none;user-select:none;-webkit-appearance:none;appearance:none;transition:background .15s ease;}' +
        '.wppoppop-st-left .wppoppop-st-tab{border-top-right-radius:6px;border-bottom-right-radius:6px;}' +
        '.wppoppop-st-right .wppoppop-st-tab{border-top-left-radius:6px;border-bottom-left-radius:6px;}' +
        '.wppoppop-st-tab:hover,.wppoppop-st-tab:focus{background:#a3154d;color:#fff;outline:2px solid transparent;}' +
        '.wppoppop-st-tab:focus-visible{outline:2px solid #fff;outline-offset:-4px;}';

    function isPreview() {
        return (document.body && document.body.className.indexOf('wppoppop-preview-frame') !== -1) || /[?&]wppoppop_preview=/.test(window.location.search);
    }

    function addCss() {
        if (document.getElementById('wppoppop-st-css')) return;
        var st = document.createElement('style');
        st.id = 'wppoppop-st-css';
        st.appendChild(document.createTextNode(CSS));
        (document.head || document.getElementsByTagName('head')[0]).appendChild(st);
    }

    function stack(side) {
        var $s = $('.wppoppop-st-stack.wppoppop-st-' + side);
        if (!$s.length) {
            $s = $('<div class="wppoppop-st-stack wppoppop-st-' + side + '"></div>').appendTo(document.body);
        }
        return $s;
    }

    function openPopup(uid) {
        if (window.WpPopPop && typeof window.WpPopPop.show === 'function') {
            window.WpPopPop.show(uid);
        } else if (window.WpPopPopFrontDisplay && typeof window.WpPopPopFrontDisplay.show === 'function') {
            window.WpPopPopFrontDisplay.show(uid);
        }
    }

    function build() {
        if (isPreview()) return;
        $('.wppoppop-popup-wrap[data-sidetab]:not([data-inline="1"])').each(function() {
            var $w = $(this), uid = $w.attr('data-uid'), side = $w.attr('data-sidetab') === 'right' ? 'right' : 'left';
            var label = $w.attr('data-sidetab-label') || '';
            var made = false;
            if (!uid || !label) return;
            $('.wppoppop-st-tab').each(function() { if ($(this).attr('data-uid') === uid) { made = true; } });
            if (made) return;
            addCss();
            $('<button type="button" class="wppoppop-st-tab"></button>')
                .attr('data-uid', uid)
                .attr('aria-haspopup', 'dialog')
                .attr('title', label)
                .text(label)
                .appendTo(stack(side));
        });
    }

    $(document).on('click.wppSideTab', '.wppoppop-st-tab', function(e) {
        e.preventDefault();
        openPopup($(this).attr('data-uid'));
    });

    $(build);
    $(window).on('load', build);
})(window, document, window.jQuery);
