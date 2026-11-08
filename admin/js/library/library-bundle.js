/**
 * WpPopPop Library: zip export (template.json + images) and drag & drop import (.zip / .json).
 * ES5 + XHR/FormData/Blob: Chrome, Safari, Firefox, Edge.
 */
(function (window, $) {
    'use strict';

    var cfg = window.wppoppop_libzip_vars || { ajax_url: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), nonce: '', zip: false, max_mb: 60, i18n: {} };
    var t = cfg.i18n || {};
    var queue = [], busy = false, imported = 0, dragDepth = 0;

    /* ------------------------------------------------------------ zip export */
    function saveBlob(blob, name) {
        if (window.navigator && window.navigator.msSaveBlob) { window.navigator.msSaveBlob(blob, name); return; }
        var url = (window.URL || window.webkitURL).createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url; a.download = name; a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { document.body.removeChild(a); (window.URL || window.webkitURL).revokeObjectURL(url); }, 1000);
    }

    /* cb('ok' | 'fallback' | 'error') */
    function download(id, cb) {
        var x = new XMLHttpRequest();
        x.open('POST', cfg.ajax_url, true);
        x.responseType = 'blob';
        x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        x.onload = function () {
            var ct = x.getResponseHeader('Content-Type') || '';
            if (x.status === 200 && ct.indexOf('application/zip') !== -1) {
                var cd = x.getResponseHeader('Content-Disposition') || '', m = /filename="?([^";]+)"?/i.exec(cd);
                saveBlob(x.response, m ? m[1] : 'template-wppoppop-template.zip');
                var miss = parseInt(x.getResponseHeader('X-Wppoppop-Missing') || '0', 10);
                if (miss > 0) { window.alert((t.missing || '%d image(s) could not be bundled (missing or too large); their links were kept.').replace('%d', miss)); }
                cb('ok');
                return;
            }
            var fr = new FileReader();
            fr.onload = function () {
                var r = null;
                try { r = JSON.parse(String(fr.result)); } catch (e) { r = null; }
                if (r && r.data && r.data.code === 'no_zip') { cb('fallback'); return; }
                window.alert((r && r.data && r.data.message) || t.export_failed || 'Download failed.');
                cb('error');
            };
            fr.onerror = function () { window.alert(t.export_failed || 'Download failed.'); cb('error'); };
            fr.readAsText(x.response);
        };
        x.onerror = function () { window.alert(t.export_failed || 'Download failed.'); cb('error'); };
        x.send('action=wppoppop_template_export_zip&nonce=' + encodeURIComponent(cfg.nonce) + '&id=' + encodeURIComponent(id));
    }

    window.WpPopPopLibraryZip = {
        canZip: function () { return !!cfg.zip && typeof window.FileReader !== 'undefined' && typeof (new XMLHttpRequest()).responseType === 'string'; },
        download: download
    };

    /* ------------------------------------------------------------ import UI */
    function $zone() { return $('#wppoppop-lib-drop'); }
    function $log() { return $('#wppoppop-lib-drop-log'); }

    function addRow(name) {
        var $r = $('<div class="wpp-drop-row"><span class="wpp-drop-name"></span><span class="wpp-drop-state"></span></div>');
        $r.find('.wpp-drop-name').text(name);
        $log().append($r).show();
        return $r;
    }

    function setState($r, text, cls) {
        $r.removeClass('is-ok is-err is-busy').addClass(cls || '').find('.wpp-drop-state').text(text);
    }

    function validName(n) { return /\.(zip|json)$/i.test(n || ''); }

    function enqueue(files) {
        var i, f;
        for (i = 0; i < files.length; i++) {
            f = files[i];
            if (!validName(f.name)) {
                setState(addRow(f.name), t.bad_type || 'Only .zip or .json files', 'is-err');
                continue;
            }
            if (f.size > cfg.max_mb * 1048576) {
                setState(addRow(f.name), (t.too_big || 'Larger than %d MB').replace('%d', cfg.max_mb), 'is-err');
                continue;
            }
            queue.push({ file: f, row: addRow(f.name) });
        }
        next();
    }

    function finish() {
        busy = false;
        $zone().removeClass('is-busy');
        if (imported > 0) {
            window.setTimeout(function () { window.location.reload(); }, 1200);
        }
    }

    function next() {
        if (busy) { return; }
        var job = queue.shift();
        if (!job) { return; }
        busy = true;
        $zone().addClass('is-busy');
        setState(job.row, t.uploading || 'Uploading...', 'is-busy');

        var fd = new FormData(), x = new XMLHttpRequest();
        fd.append('action', 'wppoppop_template_import_file');
        fd.append('nonce', cfg.nonce);
        fd.append('file', job.file, job.file.name);
        x.open('POST', cfg.ajax_url, true);
        if (x.upload) {
            x.upload.onprogress = function (e) {
                if (e.lengthComputable && e.total > 0) { setState(job.row, Math.round(e.loaded / e.total * 100) + '%', 'is-busy'); }
            };
        }
        x.onreadystatechange = function () {
            if (x.readyState !== 4) { return; }
            var r = null;
            try { r = JSON.parse(x.responseText); } catch (e) { r = null; }
            if (r && r.success) {
                imported++;
                setState(job.row, (r.data && r.data.message) || t.done || 'Imported', 'is-ok');
            } else {
                setState(job.row, (r && r.data && r.data.message) || t.failed || 'Import failed', 'is-err');
            }
            busy = false;
            if (queue.length) { next(); } else { finish(); }
        };
        x.send(fd);
    }

    function hasFiles(e) {
        var dt = e.originalEvent && e.originalEvent.dataTransfer, i;
        if (!dt || !dt.types) { return false; }
        for (i = 0; i < dt.types.length; i++) { if (dt.types[i] === 'Files') { return true; } }
        return false;
    }

    $(function () {
        var $wrap = $('.wppoppop-library-wrap');
        if (!$wrap.length || !$zone().length) { return; }

        $(document).on('click.wppZip', '.wppoppop-lib-import-btn, #wppoppop-lib-drop-pick', function (e) {
            e.preventDefault();
            document.getElementById('wppoppop-lib-file').click();
        });
        $('#wppoppop-lib-file').on('change', function () {
            if (this.files && this.files.length) { enqueue(this.files); }
            this.value = '';
        });

        $wrap.on('dragenter.wppZip', function (e) {
            if (!hasFiles(e)) { return; }
            e.preventDefault();
            dragDepth++;
            $zone().addClass('is-dragover');
        });
        $wrap.on('dragover.wppZip', function (e) {
            if (!hasFiles(e)) { return; }
            e.preventDefault();
            try { e.originalEvent.dataTransfer.dropEffect = 'copy'; } catch (err) {}
        });
        $wrap.on('dragleave.wppZip', function (e) {
            if (!hasFiles(e)) { return; }
            dragDepth = Math.max(0, dragDepth - 1);
            if (dragDepth === 0) { $zone().removeClass('is-dragover'); }
        });
        $wrap.on('drop.wppZip', function (e) {
            if (!hasFiles(e)) { return; }
            e.preventDefault();
            dragDepth = 0;
            $zone().removeClass('is-dragover');
            var dt = e.originalEvent.dataTransfer;
            if (dt.files && dt.files.length) { enqueue(dt.files); }
        });
    });
})(window, jQuery);
