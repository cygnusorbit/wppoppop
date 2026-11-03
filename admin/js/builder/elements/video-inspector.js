/**
 * WpPopPop Builder: Video inspector module (Video Embed & Playback)
 * Lifecycle: Inspector DOM -> JS model (live) -> pre-save flush (gp-bind) -> AJAX whitelist -> front renderer.
 * Handles: video thumbnail (WP Media picker, URL, clear), Width x Height inputs, click-to-play facade in preview.
 * Cross-browser: Chrome, Safari, Firefox, Edge (ES5 only).
 */
(function (window, $) {
    'use strict';
    if (!$ || window.WpPopPopVideoInspector) { return; }

    var DEF_W = 200, DEF_H = 150, MIN = 16;
    var frame = null;

    function cid(r) { return String(r || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim(); }

    function activeEl() {
        var c = window.WpPopPopBuilderCore;
        if (!c || !c.state || !c.state.activeId) { return null; }
        var id = cid(c.state.activeId);
        var list = (c.state.canvases && c.state.canvases[c.state.currentCanvas || 1]) || [];
        for (var i = 0; i < list.length; i++) {
            if (list[i] && cid(list[i].id) === id) { return list[i]; }
        }
        return null;
    }

    function isVideo(el) { return !!el && String(el.type || '').toLowerCase() === 'video'; }
    function intOr(v, d) { v = parseInt(v, 10); return isNaN(v) ? d : v; }

    function rerender() {
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
            window.WpPopPopBuilderCanvas.renderCanvas();
        }
    }

    function history(label) {
        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
            window.WpPopPopBuilderCore.pushHistory(label);
        }
    }

    function paintPreview(url) {
        var $box = $('#prop-video-thumb-preview');
        var $clr = $('#prop-video-thumb-clear');
        if (url) {
            $box.addClass('has-thumb').css('background-image', 'url("' + String(url).replace(/"/g, '%22') + '")');
            $box.find('.wppoppop-video-thumb-hint').hide();
            $clr.show();
        } else {
            $box.removeClass('has-thumb').css('background-image', 'none');
            $box.find('.wppoppop-video-thumb-hint').show();
            $clr.hide();
        }
    }

    function sync() {
        var el = activeEl();
        if (!isVideo(el)) { return; }
        var t = $.trim(el.videoThumb || '');
        $('#prop-video-thumb').val(t);
        paintPreview(t);
        var w = intOr(el.width, DEF_W), h = intOr(el.height, DEF_H);
        // #prop-size-* / #prop-width|height are shared with the pre-save flush and the drawer bounds.
        $('#prop-size-width, #prop-width').val(w);
        $('#prop-size-height, #prop-height').val(h);
        if (document.activeElement !== $('#prop-video-width')[0]) { $('#prop-video-width').val(w); }
        if (document.activeElement !== $('#prop-video-height')[0]) { $('#prop-video-height').val(h); }
    }

    var timer = null;
    function sched() {
        if (timer) { return; }
        timer = window.setTimeout(function () { timer = null; sync(); }, 60);
        window.setTimeout(sync, 250);
        window.setTimeout(sync, 600);
    }

    function applyThumb(url) {
        var el = activeEl();
        if (!isVideo(el)) { return; }
        url = $.trim(url || '');
        el.videoThumb = url;
        $('#prop-video-thumb').val(url);
        paintPreview(url);
        rerender();
        history('Video Thumbnail');
    }

    function openMedia() {
        if (!window.wp || !window.wp.media) {
            var u = window.prompt('Thumbnail image URL:', $('#prop-video-thumb').val() || '');
            if (u !== null) { applyThumb(u); }
            return;
        }
        if (!frame) {
            frame = window.wp.media({
                title: 'Select Video Thumbnail',
                button: { text: 'Use as Thumbnail' },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function () {
                var att = frame.state().get('selection').first();
                att = att && att.toJSON ? att.toJSON() : null;
                if (!att) { return; }
                var url = att.url;
                if (att.sizes && att.sizes.large && att.sizes.large.url) { url = att.sizes.large.url; }
                applyThumb(url);
            });
        }
        frame.open();
    }

    function swapFacade(node) {
        var src = node.getAttribute('data-wppoppop-embed');
        if (!src || node.getAttribute('data-wppoppop-played')) { return; }
        node.setAttribute('data-wppoppop-played', '1');
        var f = document.createElement('iframe');
        f.setAttribute('src', src);
        f.setAttribute('frameborder', '0');
        f.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture; fullscreen');
        f.setAttribute('allowfullscreen', '');
        f.style.cssText = 'width:100%;height:100%;border:0;border-radius:inherit;display:block;background:#000;';
        while (node.firstChild) { node.removeChild(node.firstChild); }
        node.appendChild(f);
    }

    $(document).off('.wppVideoInsp');

    $(document).on('click.wppVideoInsp', '#prop-video-thumb-btn, #prop-video-thumb-preview', function (e) {
        e.preventDefault();
        if (!isVideo(activeEl())) { return; }
        openMedia();
    });

    $(document).on('click.wppVideoInsp', '#prop-video-thumb-clear', function (e) {
        e.preventDefault();
        e.stopPropagation();
        applyThumb('');
    });

    $(document).on('input.wppVideoInsp', '#prop-video-thumb', function () {
        var el = activeEl();
        if (!isVideo(el)) { return; }
        el.videoThumb = $.trim($(this).val());
        paintPreview(el.videoThumb);
    });

    $(document).on('change.wppVideoInsp', '#prop-video-thumb', function () {
        applyThumb($(this).val());
    });

    // Width x Height: keep the panel inputs, the shared flush inputs and the drawer bounds in step.
    $(document).on('input.wppVideoInsp change.wppVideoInsp', '#prop-video-width, #prop-video-height', function () {
        var el = activeEl();
        if (!isVideo(el)) { return; }
        var isW = this.id === 'prop-video-width';
        var raw = $.trim($(this).val());
        if (raw === '') { return; }
        var v = Math.max(MIN, intOr(raw, isW ? DEF_W : DEF_H));
        if (isW) { el.width = v; el.w = v; } else { el.height = v; el.h = v; }
        $(isW ? '#prop-size-width, #prop-width' : '#prop-size-height, #prop-height').val(v);
        rerender();
    });
    $(document).on('input.wppVideoInsp change.wppVideoInsp', '#prop-width, #prop-height', function () {
        var el = activeEl();
        if (!isVideo(el)) { return; }
        var isW = this.id === 'prop-width';
        var v = intOr($(this).val(), 0);
        if (v) { $(isW ? '#prop-video-width' : '#prop-video-height').val(v); }
    });

    // Preview-mode facade (thumbnail + play button) swaps in the player on click.
    $(document).on('click.wppVideoInsp', '.wppoppop-video-facade', function () { swapFacade(this); });

    window.addEventListener('click', function (e) {
        var t = e.target;
        if (t && t.closest && t.closest('.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-insp-tab, #wppoppop-inspector-drawer')) { sched(); }
    }, true);
    // Canvas drag / resize ends: refresh Width x Height from the model.
    window.addEventListener('mouseup', function () { if (isVideo(activeEl())) { sched(); } }, true);

    window.WpPopPopVideoInspector = { sync: sync, openMedia: openMedia };
    $(function () { sched(); });
})(window, window.jQuery);
