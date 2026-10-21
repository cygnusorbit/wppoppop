/**
 * WpPopPop Builder Registry Module: registry-form
 * Split from builder-elements-registry.js (load order matters; see class-admin-assets.php)
 */
/* === WPPOPPOP_PHASE3_FORM_FIELD_VERIFICATION_START === */
(function(window, $) {
    'use strict';

    window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var Reg = window.WpPopPopElementsRegistry;

    function parseOptionsList(raw, fallbackList) {
        if (Array.isArray(raw) && raw.length) return raw;
        var str = $.trim(String(raw !== undefined && raw !== null ? raw : ''));
        if (!str) return fallbackList || [];
        var parts = str.indexOf('\n') !== -1 ? str.split(/\r?\n/) : str.split(',');
        var out = [];
        for (var i = 0; i < parts.length; i++) {
            var item = $.trim(parts[i]);
            if (item) out.push(item);
        }
        return out.length ? out : (fallbackList || []);
    }

    /* WPPOPPOP_ICON_COLOR */
    var _iconEl = null;
    function formatIconTagBase(cls, posStyle, color) {
        var c = $.trim(cls || '');
        if (c && !/^[A-Za-z][A-Za-z0-9 _-]*$/.test(c)) {
            return '<span aria-hidden="true" style="' + posStyle + 'color:' + color + ';font-size:14px;line-height:1;pointer-events:none;">' + $('<div>').text(c).html() + '</span>';
        }
        if (!c) return '';
        var full = (c.indexOf('dashicons-') === 0 && c.indexOf('dashicons ') === -1) ? ('dashicons ' + c) : c;
        return '<i class="' + full + '" style="' + posStyle + 'color:' + color + ';font-size:14px;width:14px;height:14px;line-height:1;display:inline-flex;align-items:center;justify-content:center;pointer-events:none;"></i>';
    }
    function formatIconTag(cls, posStyle) {
        var key = (String(posStyle).indexOf('right:') !== -1) ? 'iconRightColor' : 'iconLeftColor';
        var c = _iconEl && _iconEl[key];
        if (!c || !/^#[0-9a-fA-F]{3,8}$/.test(c)) { c = (_iconEl && /^#[0-9a-fA-F]{3,8}$/.test(_iconEl.fgColor || '')) ? _iconEl.fgColor : '#64748b'; } /* WPPOPPOP_FG_COLOR */
        return formatIconTagBase(cls, posStyle, c);
    }

    Reg.renderFormElementStage = function(el) {
        _iconEl = el || null;
        if (!el) return;
        var $node = (typeof Reg.getStageNode === 'function') ? Reg.getStageNode(el) : $();
        if (!$node || !$node.length) return;

        var rawType = String(el.type || 'text').toLowerCase().trim();
        var canonical = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[rawType]) ? Reg.ALIAS_MAP[rawType] : rawType;
        if (!Reg.FORM_FAMILY || Reg.FORM_FAMILY.indexOf(canonical) === -1) return;

        var $handles = $node.children('.ui-resizable-handle, .wppoppop-resize-handle').detach();

        var align   = el.textAlign || 'left';
        var defVal  = $.trim(el.defaultValue || '');
        var reqStar = el.required ? ' <span style="color:#f43f5e;font-weight:700;">*</span>' : '';
        var roBadge = el.readOnly ? '<span style="font-size:9px;background:#e2e8f0;color:#475569;padding:1px 4px;border-radius:3px;margin-left:6px;flex-shrink:0;">RO</span>' : '';

        var iLeft  = $.trim(el.iconLeft || ((canonical === 'date' ? 'dashicons-calendar-alt' : (canonical === 'time' ? 'dashicons-clock' : (canonical === 'password' ? '' : (canonical === 'upload' ? 'dashicons-cloud-upload' : ''))))));
        var iRight = $.trim(el.iconRight || '');
        var offL   = (isNaN(parseInt(el.iconLeftOffset, 10)) ? 10 : parseInt(el.iconLeftOffset, 10));
        var offR   = (isNaN(parseInt(el.iconRightOffset, 10)) ? 10 : parseInt(el.iconRightOffset, 10));
        var padL   = iLeft ? (offL + 22) : 12;
        var padR   = iRight ? (offR + 22) : 12;

        var leftIconHtml  = formatIconTag(iLeft, 'position:absolute;left:' + offL + 'px;top:50%;transform:translateY(-50%);');
        var rightIconHtml = formatIconTag(iRight, 'position:absolute;right:' + offR + 'px;top:50%;transform:translateY(-50%);');

        var innerHtml = '';

        if (canonical === 'text' || canonical === 'email') {
            var ph = defVal || el.placeholder || (canonical === 'email' ? 'Enter your email address...' : 'Enter text...');
            var textCol = defVal ? '#0f172a' : '#64748b';
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;padding-left:' + padL + 'px;padding-right:' + padR + 'px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<span style="width:100%;text-align:' + align + ';color:' + textCol + ';font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(ph).html() + reqStar + '</span>' +
                roBadge + rightIconHtml +
                '</div>';
        } else if (canonical === 'number') {
            var nPh = defVal || el.placeholder || '0';
            var minV = (el.minValue !== undefined && el.minValue !== '') ? el.minValue : (el.min !== undefined ? el.min : 0);
            var maxV = (el.maxValue !== undefined && el.maxValue !== '') ? el.maxValue : (el.max !== undefined ? el.max : 100);
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding-left:' + padL + 'px;padding-right:10px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<span style="flex:1;text-align:' + align + ';color:#334155;font-size:12px;">' + $('<div>').text(nPh).html() + reqStar + '</span>' +
                (el.unitSuffix ? '<span style="font-size:11px;color:#64748b;margin-right:6px;">' + $('<div>').text(el.unitSuffix).html() + '</span>' : '') + '<span style="font-size:9px;color:#94a3b8;margin-right:6px;">[' + minV + '–' + maxV + ']</span>' +
                '<span style="display:flex;flex-direction:column;font-size:8px;line-height:1;color:#64748b;">&#9650;<br>&#9660;</span>' +
                '</div>';
        } else if (canonical === 'tel') {
            var tPh  = defVal || el.placeholder || el.inputMask || '(555) 000-0000';
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;overflow:hidden;box-sizing:border-box;pointer-events:none;">' +
                /* WPPOPPOP_TEL_ICONS */ leftIconHtml + '<span style="flex:1;padding:0 ' + (iRight ? padR : 12) + 'px 0 ' + padL + 'px;text-align:' + align + ';color:#64748b;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(tPh).html() + reqStar + '</span>' +
                rightIconHtml +
                '</div>';
        } else if (canonical === 'textarea') {
            var taPh = defVal || el.placeholder || 'Write your message...';
            var maxC = parseInt(el.maxLength, 10) || 0;
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;flex-direction:column;justify-content:space-between;padding:8px 10px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                '<div style="text-align:' + align + ';color:#64748b;font-size:12px;line-height:1.4;overflow:hidden;">' + $('<div>').text(taPh).html() + reqStar + '</div>' +
                (maxC > 0 ? '<div style="text-align:right;font-size:9px;color:#94a3b8;">0 / ' + maxC + '</div>' : '') +
                '</div>';
        } else if (canonical === 'select') {
            var sOpts = parseOptionsList(el.optionsList || el.options, ['Option 1', 'Option 2', 'Option 3']);
            var sDisp = defVal || el.firstOption || el.placeholder || 'Select an option...';
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding:0 12px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                '<span style="flex:1;text-align:' + align + ';color:#334155;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(sDisp).html() + reqStar + '</span>' +
                '<span class="dashicons dashicons-arrow-down-alt2" style="font-size:16px;width:16px;height:16px;color:#64748b;"></span>' +
                '</div>';
        } else if (canonical === 'checkbox' || canonical === 'checkboxes') {
            /* WPPOPPOP_CHECKBOX_STAGE_V2 */
            var cbOpts = parseOptionsList(el.optionsList || el.options, []);
            if (!cbOpts.length) { cbOpts = [el.checkboxLabel || 'I agree to the Terms and Privacy Policy']; }
            var isChecked = (el.checkedDefault === true || el.checkedDefault === 1 || el.checkedDefault === '1' || el.checkedDefault === 'true');
            var cbLay = String(el.layout || el.checkboxLayout || 'vertical').toLowerCase();
            var cbRow = (cbLay === 'horizontal' || cbLay === 'inline' || cbLay === 'row');
            var cbJ = align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start');
            var cbTxt = 'font-size:' + (parseInt(el.fontSize, 10) || 12) + 'px;' +
                'font-weight:' + (el.fontWeight || '400') + ';' +
                'line-height:' + (parseFloat(el.lineHeight) || 1.3) + ';' +
                'letter-spacing:' + (parseFloat(el.letterSpacing) || 0) + 'px;' +
                'color:' + (el.color || '#1e293b') + ';' +
                ((el.fontFamily && el.fontFamily !== 'inherit') ? 'font-family:' + String(el.fontFamily).replace(/["<>]/g, '') + ';' : '');
            var cbItemsHtml = cbOpts.map(function(opt, idx) {
                var active = (idx === 0 && isChecked);
                return '<span style="display:inline-flex;align-items:center;gap:8px;' + cbTxt + '">' +
                    '<span style="width:16px;height:16px;border-radius:3px;border:1.5px solid ' + (active ? '#c2185b' : '#94a3b8') + ';background:' + (active ? '#c2185b' : '#ffffff') + ';color:#ffffff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;line-height:1;flex-shrink:0;">' + (active ? '&#10003;' : '') + '</span>' +
                    '<span>' + $('<div>').text(opt).html() + (idx === 0 && el.termsUrl ? ' <u style="color:#0284c7;">(view)</u>' : '') + (idx === 0 ? reqStar : '') + '</span></span>';
            }).join('');
            innerHtml = '<div style="width:100%;height:100%;display:flex;flex-direction:' + (cbRow ? 'row' : 'column') + ';flex-wrap:wrap;align-items:' + (cbRow ? 'center' : cbJ) + ';justify-content:' + (cbRow ? cbJ : 'center') + ';gap:8px;padding:0 6px;box-sizing:border-box;pointer-events:none;">' + cbItemsHtml + '</div>';
        } else if (canonical === 'radio' || canonical === 'radios') {
            /* WPPOPPOP_RADIO_STAGE_V2 */
            var rPairs = parseOptionsList(el.optionsList || el.options, ['Choice A', 'Choice B', 'Choice C']).map(function(o) { /* WPPOPPOP_RADIO_LV */
                var s = String(o), i = s.indexOf('|');
                return i === -1 ? { l: $.trim(s), v: '' } : { l: $.trim(s.slice(0, i)), v: $.trim(s.slice(i + 1)) };
            });
            var rOpts = rPairs.map(function(x) { return x.l || x.v; });
            var rLay = String(el.layout || el.radioLayout || 'vertical').toLowerCase();
            var rRow = (rLay === 'horizontal' || rLay === 'inline' || rLay === 'row');
            var rJ = align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start');
            var rTxt = 'font-size:' + (parseInt(el.fontSize, 10) || 12) + 'px;' +
                'font-weight:' + (el.fontWeight || '400') + ';' +
                'line-height:' + (parseFloat(el.lineHeight) || 1.3) + ';' +
                'letter-spacing:' + (parseFloat(el.letterSpacing) || 0) + 'px;' +
                'color:' + (el.color || '#1e293b') + ';' +
                ((el.fontFamily && el.fontFamily !== 'inherit') ? 'font-family:' + String(el.fontFamily).replace(/["<>]/g, '') + ';' : '');
            var rHtml = rOpts.map(function(opt, idx) {
                var isSel = defVal ? (opt.toLowerCase() === defVal.toLowerCase() || (!!rPairs[idx].v && rPairs[idx].v.toLowerCase() === defVal.toLowerCase())) : false;
                return '<span style="display:inline-flex;align-items:center;gap:8px;' + rTxt + '">' +
                    '<span style="width:16px;height:16px;border-radius:50%;border:1.5px solid ' + (isSel ? '#c2185b' : '#94a3b8') + ';display:inline-flex;align-items:center;justify-content:center;background:#ffffff;flex-shrink:0;">' +
                    (isSel ? '<span style="width:8px;height:8px;border-radius:50%;background:#c2185b;"></span>' : '') +
                    '</span><span>' + $('<div>').text(opt).html() + (idx === 0 ? reqStar : '') + '</span></span>';
            }).join('');
            innerHtml = '<div style="width:100%;height:100%;display:flex;flex-direction:' + (rRow ? 'row' : 'column') + ';flex-wrap:wrap;align-items:' + (rRow ? 'center' : rJ) + ';justify-content:' + (rRow ? rJ : 'center') + ';gap:8px;padding:0 6px;box-sizing:border-box;pointer-events:none;">' + rHtml + '</div>';
        } else if (canonical === 'multiselect') {
            var mOpts = parseOptionsList(el.optionsList || el.options, ['Marketing', 'Design', 'Development']);
            var maxS  = parseInt(el.maxSelections || el.maxSelect, 10) || 0;
            var pills = mOpts.slice(0, 4).map(function(o, i) {
                var sel = (i === 0);
                return '<span style="padding:2px 7px;border-radius:4px;font-size:' + (parseInt(el.fontSize, 10) || 10) + 'px;font-weight:600;background:' + (sel ? '#c2185b' : '#f1f5f9') + ';color:' + (sel ? '#ffffff' : '#475569') + ';border:1px solid ' + (sel ? '#c2185b' : '#cbd5e1') + ';">' + $('<div>').text(o).html() + '</span>';
            }).join('');
            innerHtml = '<div style="width:100%;height:100%;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;padding:6px 8px;display:flex;flex-wrap:wrap;align-items:center;gap:5px;box-sizing:border-box;pointer-events:none;">' +
                pills + (maxS > 0 ? '<span style="margin-left:auto;font-size:9px;color:#94a3b8;">Max: ' + maxS + '</span>' : '') +
                '</div>';
        } else if (canonical === 'imageselect') {
            /* WPPOPPOP_IMAGESELECT_STAGE_V2 */
            var imgRaw = el.optionsList || el.options || '';
            if (Array.isArray(imgRaw)) { imgRaw = imgRaw.join('\n'); }
            var imgLines = $.trim(String(imgRaw)).split(/\r?\n/).map(function(x) { return $.trim(x); }).filter(Boolean);
            if (imgLines.length === 1 && imgLines[0].indexOf(',') !== -1 && !/https?:|\/\/|\||::/.test(imgLines[0])) {
                imgLines = imgLines[0].split(',').map(function(x) { return $.trim(x); }).filter(Boolean);
            }
            if (!imgLines.length) { imgLines = ['Style A', 'Style B', 'Style C']; }
            var iCols = Math.min(4, Math.max(1, parseInt(el.gridCols || el.columns, 10) || 3));
            var iAcc  = el.activeColor || '#38bdf8';
            var iTxt  = 'font-size:' + (parseInt(el.fontSize, 10) || 12) + 'px;font-weight:' + (el.fontWeight || '600') + ';color:' + (el.color || '#1e293b') + ';';
            var parseImg = function(line) {
                var m, p, lbl = line, img = '';
                if (line.indexOf('::') !== -1) { p = line.split('::'); lbl = $.trim(p.shift()); img = $.trim(p.join('::')); }
                else if (line.indexOf('|') !== -1) { p = line.split('|'); lbl = $.trim(p.shift()); img = $.trim(p.join('|')); }
                else if ((m = line.match(/^(.*?),\s*((?:https?:)?\/\/\S+|\/\S+)$/i))) { lbl = $.trim(m[1]); img = m[2]; }
                return { l: lbl, i: img };
            };
            var cards = imgLines.slice(0, 8).map(function(line, idx) {
                var it = parseImg(line);
                var active = (idx === 0);
                var thumb = (it.i && /^((https?:)?\/\/|\/)/i.test(it.i))
                    ? '<img src="' + String(it.i).replace(/"/g, '%22').replace(/</g, '%3C') + '" alt="" style="width:100%;flex:1 1 auto;min-height:0;object-fit:cover;border-radius:3px;display:block;" />'
                    : '<span class="dashicons dashicons-format-image" style="font-size:18px;width:18px;height:18px;color:' + (active ? iAcc : '#94a3b8') + ';"></span>';
                return '<div style="min-width:0;min-height:0;box-sizing:border-box;border:2px solid ' + (active ? iAcc : '#cbd5e1') + ';border-radius:6px;background:#ffffff;padding:4px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;text-align:center;">' + thumb +
                    '<span style="max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;' + iTxt + '">' + $('<div>').text(it.l || 'Choice').html() + (idx === 0 ? reqStar : '') + '</span></div>';
            }).join('');
            innerHtml = '<div style="width:100%;height:100%;display:grid;grid-template-columns:repeat(' + iCols + ',minmax(0,1fr));grid-auto-rows:minmax(0,1fr);gap:6px;box-sizing:border-box;pointer-events:none;">' + cards + '</div>';
        } else if (canonical === 'tile') {
            /* WPPOPPOP_TILE_STAGE_V2 */
            var tRaw = el.optionsList || el.options || '';
            if (Array.isArray(tRaw)) { tRaw = tRaw.join('\n'); }
            var tLines = $.trim(String(tRaw)).split(/\r?\n/).map(function(x) { return $.trim(x); }).filter(Boolean);
            if (tLines.length === 1 && tLines[0].indexOf(',') !== -1) { tLines = tLines[0].split(',').map(function(x) { return $.trim(x); }).filter(Boolean); }
            if (!tLines.length) { tLines = ['$10 / mo', '$25 / mo', '$50 / mo']; }
            var tCols = Math.min(4, Math.max(1, parseInt(el.gridCols || el.columns, 10) || 3));
            var tAcc  = (el.activeColor && /^#[0-9a-fA-F]{3,8}$/.test(el.activeColor)) ? el.activeColor : '#38bdf8';
            var tBw   = (parseInt(el.borderWidth, 10) > 0) ? parseInt(el.borderWidth, 10) : 1;
            var tBs   = (['solid', 'dashed', 'dotted', 'double', 'none'].indexOf(el.borderStyle) !== -1) ? el.borderStyle : 'solid';
            var tBc   = (el.borderColor && /^#[0-9a-fA-F]{3,8}$/.test(el.borderColor)) ? el.borderColor : '#cbd5e1';
            var tRad  = (el.borderRadius !== undefined && el.borderRadius !== '' && !isNaN(parseInt(el.borderRadius, 10))) ? parseInt(el.borderRadius, 10) : 6;
            var tJ    = align === 'right' ? 'flex-end' : (align === 'left' ? 'flex-start' : 'center');
            var tFg   = (el.color && /^#[0-9a-fA-F]{3,8}$/.test(el.color)) ? el.color : '#334155';
            var tBg   = (el.transparentFill || el.bgColor === 'transparent') ? 'transparent' : ((el.bgColor && /^#[0-9a-fA-F]{3,8}$/.test(el.bgColor)) ? el.bgColor : '#f1f5f9'); /* WPPOPPOP_TILE_BG_STAGE */
            var tTxt  = 'font-size:' + (parseInt(el.fontSize, 10) || 12) + 'px;font-weight:' + (el.fontWeight || '700') + ';line-height:' + (parseFloat(el.lineHeight) || 1.3) + ';letter-spacing:' + (parseFloat(el.letterSpacing) || 0) + 'px;';
            var tTiles = tLines.slice(0, 8).map(function(lbl, idx) {
                var on = (idx === 0);
                var bd = (tBs === 'none') ? 'none' : (tBw + 'px ' + tBs + ' ' + (on ? tAcc : tBc));
                return '<div style="min-width:0;box-sizing:border-box;display:flex;align-items:center;justify-content:' + tJ + ';padding:6px;border-radius:' + tRad + 'px;border:' + bd + ';background:' + (on ? tAcc : tBg) + ';color:' + (on ? '#ffffff' : tFg) + ';' + tTxt + '">' +
                    '<span style="max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(lbl).html() + (idx === 0 ? reqStar : '') + '</span></div>';
            }).join('');
            innerHtml = '<div style="width:100%;height:100%;display:grid;grid-template-columns:repeat(' + tCols + ',minmax(0,1fr));grid-auto-rows:minmax(0,1fr);gap:6px;box-sizing:border-box;pointer-events:none;">' + tTiles + '</div>';
        } else if (canonical === 'date') {
            var dFmt = el.dateFormat || 'YYYY-MM-DD';
            var dPh  = defVal || el.placeholder || dFmt;
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding-left:' + padL + 'px;padding-right:10px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<span style="flex:1;text-align:' + align + ';color:#334155;font-size:12px;">' + $('<div>').text(dPh).html() + reqStar + '</span>' +
                '<span style="font-size:9px;color:#94a3b8;background:#f1f5f9;padding:2px 5px;border-radius:3px;">' + dFmt + '</span>' + rightIconHtml +
                '</div>';
        } else if (canonical === 'time') {
            var tFmt  = el.timeFormat || '12h';
            var tStep = parseInt(el.timeStep, 10) || parseInt(el.minuteStep, 10) || 15;
            var tmPh  = defVal || el.placeholder || (tFmt === '24h' ? '14:00' : '02:00 PM');
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding-left:' + padL + 'px;padding-right:10px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<span style="flex:1;text-align:' + align + ';color:#334155;font-size:12px;">' + $('<div>').text(tmPh).html() + reqStar + '</span>' +
                '<span style="font-size:9px;color:#94a3b8;background:#f1f5f9;padding:2px 5px;border-radius:3px;">' + tFmt + ' (' + tStep + 'm)</span>' + rightIconHtml +
                '</div>';
        } else if (canonical === 'upload') {
            /* WPPOPPOP_UPLOAD_STAGE_V2: icons + offsets + default text */
            var uPh   = el.placeholder || 'Choose file or drag here...';
            var uExts = el.allowedExtensions || el.allowedExts || '.jpg,.png,.pdf';
            var uMax  = parseInt(el.maxFileSize, 10) || 5;
            var uAl   = el.textAlign || 'center';
            var uJ    = uAl === 'left' ? 'flex-start' : (uAl === 'right' ? 'flex-end' : 'center');
            innerHtml = '<div style="position:relative;width:100%;height:100%;border:1.5px dashed #94a3b8;border-radius:6px;background:#f8fafc;display:flex;align-items:center;justify-content:' + uJ + ';gap:8px;padding:6px ' + padR + 'px 6px ' + padL + 'px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<div style="display:flex;flex-direction:column;line-height:1.2;min-width:0;text-align:' + uAl + ';">' +
                    '<span style="font-size:11px;font-weight:600;color:#334155;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(uPh).html() + reqStar + '</span>' +
                    '<span style="font-size:9px;color:#64748b;">' + $('<div>').text(uExts).html() + ' (Max ' + uMax + 'MB)</span>' +
                '</div>' +
                rightIconHtml +
                '</div>';
        } else if (canonical === 'password') {
            /* WPPOPPOP_PASSWORD_STAGE_V3: WordPress-login style, eye = 40px dashicon button */
            var pwPh = el.placeholder || 'Enter password...';
            var showEye = (el.showEyeToggle !== false && el.showEyeToggle !== 'false' && el.showEyeToggle !== 0 && el.showEyeToggle !== '0');
            var pwRightPos = offR + (showEye ? 40 : 0);
            var pwRightIcon = formatIconTag(iRight, 'position:absolute;right:' + pwRightPos + 'px;top:50%;transform:translateY(-50%);');
            var pwPadR = Math.max(showEye ? 44 : 12, iRight ? (pwRightPos + 22) : 0);
            innerHtml = '<div class="wppoppop-pw-stage" style="position:relative;width:100%;height:100%;display:flex;align-items:center;padding-left:' + padL + 'px;padding-right:' + pwPadR + 'px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<span class="wppoppop-pw-prev-text" data-ph="' + $('<div>').text(pwPh).html().replace(/"/g, '&quot;') + '" style="flex:1;text-align:' + align + ';color:#64748b;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(pwPh).html() + reqStar + '</span>' +
                pwRightIcon +
                (showEye ? '<span class="wppoppop-pw-prev-eye dashicons dashicons-visibility" aria-label="Show password" style="position:absolute;right:0;top:0;width:40px;height:100%;display:flex;align-items:center;justify-content:center;font-size:20px;line-height:20px;color:#2271b1;cursor:pointer;pointer-events:auto;"></span>' : '') +
                '</div>';
        } else if (canonical === 'rating') {
            /* WPPOPPOP_RATING_STAGE_V2: ratingMax/ratingShape/ratingColor/ratingInactiveColor, default from Data tab */
            var rMax   = parseInt(el.ratingMax, 10) === 10 ? 10 : 5;
            var rShape = el.ratingShape || 'star';
            var rCol   = (/^#[0-9a-fA-F]{3,8}$/.test(el.ratingColor || '')) ? el.ratingColor : '#f59e0b';
            var rOff   = (/^#[0-9a-fA-F]{3,8}$/.test(el.ratingInactiveColor || '')) ? el.ratingInactiveColor : '#cbd5e1';
            var rScore = Math.min(rMax, Math.max(0, parseInt(defVal, 10) || 0));
            var glyph  = rShape === 'heart' ? '&#9829;' : (rShape === 'thumb' ? '&#128077;' : '&#9733;');
            var justify = align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start');
            var starsHtml = '';
            for (var s = 1; s <= rMax; s++) {
                var c = (s <= rScore) ? rCol : rOff;
                starsHtml += '<span style="color:' + c + ';font-size:' + (rMax === 10 ? '15px' : '20px') + ';line-height:1;">' + glyph + '</span>';
            }
            innerHtml = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:' + justify + ';gap:4px;padding:0 6px;box-sizing:border-box;pointer-events:none;">' + starsHtml + '</div>';
        }

        if (innerHtml) {
            $node.html(innerHtml).append($handles);
        }
        if (typeof Reg.applyStyleToStageNode === 'function') {
            Reg.applyStyleToStageNode(el);
        }
        if (canonical === 'tile' && $node[0]) { $node[0].style.setProperty('background-color', 'transparent', 'important'); } /* WPPOPPOP_TILE_BG_BOX */
        /* WPPOPPOP_TF_NOPAD */
        if ((canonical === 'text' || canonical === 'email' || canonical === 'number' || canonical === 'tel' || canonical === 'textarea' || canonical === 'select' || canonical === 'checkbox' || canonical === 'radio' || canonical === 'radios' || canonical === 'multiselect' || canonical === 'tile') && $node[0]) {
            $node[0].style.setProperty('padding', '0', 'important');
        }
    };

    function bindPhase3FormVerification() {
        // 1. Live 2-way binding for all 16 Form Family (.wppoppop-gp-bind) controls
        $(document).off('input.p3FormBind change.p3FormBind', '[data-elem-family="form"] .wppoppop-gp-bind')
                   .on('input.p3FormBind change.p3FormBind', '[data-elem-family="form"] .wppoppop-gp-bind', function() {
            var el = Reg.resolveElement();
            if (!el) return;
            var $inp =$(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            var val = $inp.is(':checkbox') ? $inp.is(':checked') :$inp.val();
            el[prop] = val;
            Reg.renderFormElementStage(el);
        });

        // 2. Re-render Form Element Stage when Style Icons/Alignment, Required, Data Default/ReadOnly, or Advanced Mask change
        var crossTabSelectors = [
            '#prop-gp-required', '#prop-form-icon-left', '#prop-form-icon-left-offset',
            '#prop-form-icon-right', '#prop-form-icon-right-offset', '#prop-gp-style-css-class',
            '#prop-data-default', '#prop-data-readonly', '#prop-adv-id', '#prop-adv-mask'
        ].join(', ');

        $(document).off('input.p3CrossTab change.p3CrossTab', crossTabSelectors)
                   .on('input.p3CrossTab change.p3CrossTab', crossTabSelectors, function() {
            var el = Reg.resolveElement();
            if (!el) return;
            Reg.renderFormElementStage(el);
        });

        $(document).off('click.p3AlignSeg', '#gp-form-text-align-group .wppoppop-align-seg-btn')
                   .on('click.p3AlignSeg', '#gp-form-text-align-group .wppoppop-align-seg-btn', function() {
            var el = Reg.resolveElement();
            if (!el) return;
            el.textAlign = $(this).attr('data-align') || 'left';
            Reg.renderFormElementStage(el);
        });
    }

    $(function() {
        bindPhase3FormVerification();
        setTimeout(bindPhase3FormVerification, 140);
    });
    bindPhase3FormVerification();
})(window, jQuery);
/* === WPPOPPOP_PHASE3_FORM_FIELD_VERIFICATION_END === */


/* === WPPOPPOP_TILE_HOVER_BIND_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var MAP = { 'prop-elem-hover-bg': 'hoverBgColor', 'prop-elem-hover-color': 'hoverTextColor', 'prop-elem-hover-border': 'hoverBorderColor' };
    $(document).off('input.tlHover change.tlHover', '#prop-elem-hover-bg, #prop-elem-hover-color, #prop-elem-hover-border')
        .on('input.tlHover change.tlHover', '#prop-elem-hover-bg, #prop-elem-hover-color, #prop-elem-hover-border', function(e) {
            var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
            if (!el || String(el.type || '').toLowerCase() !== 'tile') { return; }
            var k = MAP[this.id];
            if (k) { el[k] = $(this).val(); }
            if (e.type === 'change' && window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                window.WpPopPopBuilderCore.pushHistory();
            }
        });
})(window, jQuery);
/* === WPPOPPOP_TILE_HOVER_BIND_END === */


/* === WPPOPPOP_TILE_BG_BIND_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    $(document).off('input.tlBg change.tlBg', '#prop-bg-color, #prop-bg-transparent')
        .on('input.tlBg change.tlBg', '#prop-bg-color, #prop-bg-transparent', function() {
            setTimeout(function() {
                var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
                if (el && String(el.type || '').toLowerCase() === 'tile' && typeof Reg.renderFormElementStage === 'function') {
                    Reg.renderFormElementStage(el);
                }
            }, 0);
        });
})(window, jQuery);
/* === WPPOPPOP_TILE_BG_BIND_END === */


/* === WPPOPPOP_DATE_PREVIEW_PICKER_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var down = null;
    /* WPPOPPOP_DATE_PREVIEW_ONLY */
    function inPreviewMode() {
        var C = window.WpPopPopBuilderCanvas, P = window.WpPopPopBuilderPreview;
        return !!((C && C.isPreview) || (P && P.isOpen));
    }

    function pad(n) { return (n < 10 ? '0' : '') + n; }

    function fmtDate(iso, fmt) {
        var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso || '');
        if (!m) return '';
        if (fmt === 'MM/DD/YYYY') return m[2] + '/' + m[3] + '/' + m[1];
        if (fmt === 'DD/MM/YYYY') return m[3] + '/' + m[2] + '/' + m[1];
        return iso;
    }

    function closeNative() { $('#wppoppop-date-preview-native').remove(); }

    function openPicker($item, el) {
        closeNative();
        var r = $item[0].getBoundingClientRect();
        var inp = document.createElement('input');
        inp.type = 'date';
        inp.id = 'wppoppop-date-preview-native';
        inp.setAttribute('lang', 'en'); /* WPPOPPOP_DATE_NATIVE_LANG */
        if (el.minDate) inp.min = String(el.minDate);
        if (el.maxDate) inp.max = String(el.maxDate);
        inp.style.cssText = 'position:fixed;left:' + r.left + 'px;top:' + r.top + 'px;width:' +
            Math.max(r.width, 140) + 'px;height:' + Math.max(r.height, 28) +
            'px;opacity:0.01;z-index:2147483000;border:0;padding:0;margin:0;';
        document.body.appendChild(inp);

        var $span = $item.find('span[style*="flex:1"]').first();
        $(inp).on('change', function() {
            if (this.value) {
                $span.text(fmtDate(this.value, el.dateFormat || 'YYYY-MM-DD')).css('color', '#0f172a');
            }
            setTimeout(closeNative, 50);
        }).on('blur', function() { setTimeout(closeNative, 200); });

        try {
            inp.focus();
            if (typeof inp.showPicker === 'function') { inp.showPicker(); }   /* Chrome, Edge, Safari 16+, Firefox 101+ */
            else { inp.click(); }
        } catch (err) { try { inp.click(); } catch (e2) {} }
    }

    $(document).off('mousedown.wppDatePrev touchstart.wppDatePrev', '.wppoppop-canvas-item')
        .on('mousedown.wppDatePrev touchstart.wppDatePrev', '.wppoppop-canvas-item', function(e) {
            if (!inPreviewMode()) { down = null; return; }
            var t = (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0]) || e;
            down = { x: t.clientX, y: t.clientY, node: this };
        });

    $(document).off('mouseup.wppDatePrev touchend.wppDatePrev')
        .on('mouseup.wppDatePrev touchend.wppDatePrev', function(e) {
            if (!down) return;
            if (!inPreviewMode()) { down = null; closeNative(); return; }
            var d = down; down = null;
            var t = (e.originalEvent && e.originalEvent.changedTouches && e.originalEvent.changedTouches[0]) || e;
            if (Math.abs(t.clientX - d.x) > 4 || Math.abs(t.clientY - d.y) > 4) return;   /* drag, not click */
            var $item = $(d.node);
            if (!$item.length || !$(e.target).closest($item).length) return;
            setTimeout(function() {      /* wait for selection to update */
                var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
                if (!el) return;
                var raw = String(el.type || '').toLowerCase();
                var canon = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[raw]) ? Reg.ALIAS_MAP[raw] : raw;
                if (canon !== 'date') return;
                var id = $item.data('id');
                if (id && el.id && String(id) !== String(el.id)) return;
                openPicker($item, el);
            }, 0);
        });

    $(document).off('mousedown.wppDatePrevClose').on('mousedown.wppDatePrevClose', function(e) {
        if (e.target && e.target.id !== 'wppoppop-date-preview-native') { closeNative(); }
    });
})(window, jQuery);
/* === WPPOPPOP_DATE_PREVIEW_PICKER_END === */

/* === WPPOPPOP_DATE_HYDRATE_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    function hydrate() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return;
        var raw = String(el.type || '').toLowerCase();
        var canon = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[raw]) ? Reg.ALIAS_MAP[raw] : raw;
        if (canon !== 'date') return;
        $('#prop-date-placeholder').val(el.placeholder || '');
        $('#prop-date-format').val(el.dateFormat || 'YYYY-MM-DD');
        $('#prop-date-min').val(el.minDate || '');
        $('#prop-date-max').val(el.maxDate || '');
    }
    $(document).off('click.wppDateHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]')
        .on('click.wppDateHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]', function() {
            [30, 150, 400].forEach(function(ms) { setTimeout(hydrate, ms); });
        });
})(window, jQuery);
/* === WPPOPPOP_DATE_HYDRATE_END === */

/* === WPPOPPOP_TIME_HYDRATE_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    function hydrateTime() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return;
        var raw = String(el.type || '').toLowerCase();
        var canon = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[raw]) ? Reg.ALIAS_MAP[raw] : raw;
        if (canon !== 'time') return;
        $('#prop-time-placeholder').val(el.placeholder || '');
        $('#prop-time-format').val(el.timeFormat === '24h' ? '24h' : '12h');
        $('#prop-time-step').val(String(parseInt(el.timeStep, 10) || parseInt(el.minuteStep, 10) || 15));
    }
    $(document).off('click.wppTimeHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]')
        .on('click.wppTimeHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]', function() {
            [30, 150, 400].forEach(function(ms) { setTimeout(hydrateTime, ms); });
        });
})(window, jQuery);
/* === WPPOPPOP_TIME_HYDRATE_END === */

/* === WPPOPPOP_UPLOAD_HYDRATE_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    /* WPPOPPOP_UPLOAD_STYLE: Upload hides Opacity, Background, Padding, Box Shadow, Custom CSS & Inline Styles (+ icons card) */
    if (!document.getElementById('wppoppop-upload-style-css')) {
        var st = document.createElement('style');
        st.id = 'wppoppop-upload-style-css';
        st.appendChild(document.createTextNode(
            '#insp-tab-style.wppoppop-up-style #insp-section-opacity,' +
            '#insp-tab-style.wppoppop-up-style #insp-section-bg-color,' +
            '#insp-tab-style.wppoppop-up-style #insp-section-padding,' +
            '#insp-tab-style.wppoppop-up-style #insp-section-shadow,' +
            '#insp-tab-style.wppoppop-up-style #insp-section-custom-css { display: none !important; }' +
            '#insp-tab-style.wppoppop-up-style #insp-section-typography,' +
            '#insp-tab-style.wppoppop-up-style #insp-section-border,' +
            '#insp-tab-style.wppoppop-up-style #wppoppop-form-icons-card { display: block !important; }'
        ));
        (document.head || document.documentElement).appendChild(st);
    }
    function isUpload() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return null;
        var raw = String(el.type || '').toLowerCase();
        var canon = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[raw]) ? Reg.ALIAS_MAP[raw] : raw;
        return (canon === 'upload' || canon === 'file' || raw === 'upload' || raw === 'file') ? el : false;
    }
    function hydrateUpload() {
        var el = isUpload();
        $('#insp-tab-style').toggleClass('wppoppop-up-style', !!el);
        if (!el) return;
        $('#prop-upload-label').val(el.placeholder || '');
        $('#prop-upload-exts').val(el.allowedExtensions || el.allowedExts || '');
        $('#prop-upload-maxsize').val(parseInt(el.maxFileSize, 10) || 10);
        $('#prop-upload-multiple').prop('checked', el.multiUpload === true || el.multiUpload === 1 || el.multiUpload === '1' || el.multiUpload === 'true');
        $('#prop-upload-maxfiles').val(parseInt(el.maxFiles, 10) || '');
        $('#prop-upload-errmsg').val(el.uploadErrorMessage || '');
        $('#prop-upload-maxfiles-row').toggle($('#prop-upload-multiple').is(':checked'));
    }
    $(document).off('click.wppUploadHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool')
        .on('click.wppUploadHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool', function() {
            [30, 150, 400, 800].forEach(function(ms) { setTimeout(hydrateUpload, ms); });
        });
    /* Re-assert after the registry sync pass so other toggles cannot clear the class */
    if (typeof Reg.hydrateCoreAndStyleFields === 'function' && !Reg._uploadStyleHooked) {
        Reg._uploadStyleHooked = true;
        var origHy = Reg.hydrateCoreAndStyleFields;
        Reg.hydrateCoreAndStyleFields = function(el) {
            var r = origHy.apply(this, arguments);
            var t = String((el && el.type) || '').toLowerCase();
            $('#insp-tab-style').toggleClass('wppoppop-up-style', t === 'upload' || t === 'file');
            return r;
        };
    }
})(window, jQuery);
/* === WPPOPPOP_UPLOAD_HYDRATE_END === */

/* === WPPOPPOP_UPLOAD_PREVIEW_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var store = {};
    var down = null;

    function inPreviewMode() {
        var C = window.WpPopPopBuilderCanvas, P = window.WpPopPopBuilderPreview;
        return !!((C && C.isPreview) || (P && P.isOpen));
    }
    var lastErr = '';
    function sizeMsg(el, f, maxMb) {
        var t = $.trim(String(el.uploadErrorMessage || '')) || 'File too large. Maximum size is {max}MB.';
        return t.replace(/\{max\}/g, maxMb).replace(/\{file\}/g, f.name);
    }
    function fmtSize(b) {
        if (b >= 1048576) return (b / 1048576).toFixed(1) + ' MB';
        if (b >= 1024) return Math.round(b / 1024) + ' KB';
        return b + ' B';
    }
    function allowedList(el) {
        var raw = String(el.allowedExtensions || el.allowedExts || '.jpg,.png,.pdf');
        return raw.split(/[\s,;]+/).map(function(x) { x = $.trim(x).toLowerCase(); return x ? '.' + x.replace(/^\./, '') : ''; }).filter(Boolean);
    }
    function render($item, id) {
        $item.children('.wppoppop-up-prev-list').remove();
        var files = store[id] || [];
        if (!files.length && !lastErr) return;
        var $list = $('<div class="wppoppop-up-prev-list"></div>').css({
            position: 'absolute', left: 0, top: '100%', marginTop: '4px', width: '100%', zIndex: 60,
            display: 'flex', flexDirection: 'column', gap: '4px', pointerEvents: 'auto'
        });
        if (lastErr) {
            $('<div class="wppoppop-up-prev-err"></div>').text(lastErr).css({ color: '#b91c1c', background: '#fef2f2', border: '1px solid #fecaca', borderRadius: '6px', padding: '3px 6px', fontSize: '11px', boxSizing: 'border-box' }).appendTo($list);
            lastErr = '';
        }
        files.forEach(function(f, i) {
            var $row = $('<div></div>').css({
                display: 'flex', alignItems: 'center', gap: '6px', background: '#ffffff', border: '1px solid #cbd5e1',
                borderRadius: '6px', padding: '3px 6px', fontSize: '11px', color: '#334155', boxSizing: 'border-box'
            });
            $('<span></span>').text(f.name).css({ flex: '1 1 auto', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }).appendTo($row);
            $('<span></span>').text(fmtSize(f.size)).css({ fontSize: '9px', color: '#94a3b8', flexShrink: 0 }).appendTo($row);
            $('<button type="button" class="wppoppop-up-prev-remove" aria-label="Remove file"></button>').text('\u00d7').attr('data-idx', i).css({
                border: 'none', background: '#fee2e2', color: '#b91c1c', borderRadius: '50%', width: '16px', height: '16px',
                lineHeight: '14px', padding: 0, cursor: 'pointer', fontSize: '13px', flexShrink: 0
            }).appendTo($row);
            $list.append($row);
        });
        $item.append($list);
    }
    function openPicker($item, el) {
        $('#wppoppop-up-prev-native').remove();
        var inp = document.createElement('input');
        inp.type = 'file';
        inp.id = 'wppoppop-up-prev-native';
        inp.accept = allowedList(el).join(',');
        var multi = (el.multiUpload === true || el.multiUpload === 1 || el.multiUpload === '1' || el.multiUpload === 'true');
        if (multi) inp.multiple = true;
        inp.style.cssText = 'position:fixed;left:-9999px;top:0;opacity:0;';
        document.body.appendChild(inp);
        var id = String(el.id);
        $(inp).on('change', function() {
            var maxMb = parseInt(el.maxFileSize, 10) || 0;
            var allowed = allowedList(el);
            var picked = Array.prototype.slice.call(inp.files || []);
            var ok = [], err = '';
            picked.forEach(function(f) {
                var nm = String(f.name).toLowerCase();
                var ext = nm.lastIndexOf('.') > -1 ? nm.slice(nm.lastIndexOf('.')) : '';
                if (maxMb && f.size > maxMb * 1048576) { err = err || sizeMsg(el, f, maxMb); return; }
                if (allowed.length && allowed.indexOf(ext) === -1) { err = err || ('File type not allowed: ' + f.name); return; }
                ok.push(f);
            });
            var combined = multi ? (store[id] || []).concat(ok) : ok.slice(0, 1);
            var maxF = multi ? (parseInt(el.maxFiles, 10) || 0) : 1;
            if (multi && maxF && combined.length > maxF) {
                err = err || ('You can upload up to ' + maxF + ' files.');
                combined = combined.slice(0, maxF);
            }
            store[id] = combined;
            lastErr = err;
            render($item, id);
            $(inp).remove();
        });
        try { inp.click(); } catch (e) {}
    }

    $(document).off('mousedown.wppUpPrev touchstart.wppUpPrev', '.wppoppop-canvas-item')
        .on('mousedown.wppUpPrev touchstart.wppUpPrev', '.wppoppop-canvas-item', function(e) {
            if (!inPreviewMode() || $(e.target).closest('.wppoppop-up-prev-remove').length) { down = null; return; }
            var t = (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0]) || e;
            down = { x: t.clientX, y: t.clientY, node: this };
        });
    $(document).off('mouseup.wppUpPrev touchend.wppUpPrev')
        .on('mouseup.wppUpPrev touchend.wppUpPrev', function(e) {
            if (!down) return;
            if (!inPreviewMode()) { down = null; return; }
            var d = down; down = null;
            var t = (e.originalEvent && e.originalEvent.changedTouches && e.originalEvent.changedTouches[0]) || e;
            if (Math.abs(t.clientX - d.x) > 4 || Math.abs(t.clientY - d.y) > 4) return;
            var $item = $(d.node);
            if ($(e.target).closest('.wppoppop-up-prev-list').length) return;
            var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement($item.attr('data-id') || $item.data('id')) : null;
            if (!el) return;
            var raw = String(el.type || '').toLowerCase();
            var canon = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[raw]) ? Reg.ALIAS_MAP[raw] : raw;
            if (canon !== 'upload') return;
            openPicker($item, el);
        });
    $(document).off('click.wppUpPrevRemove', '.wppoppop-up-prev-remove')
        .on('click.wppUpPrevRemove', '.wppoppop-up-prev-remove', function(e) {
            e.preventDefault(); e.stopPropagation();
            var $item = $(this).closest('.wppoppop-canvas-item');
            var id = String($item.attr('data-id') || $item.data('id') || '');
            var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement(id) : null;
            var key = el && el.id ? String(el.id) : id;
            var idx = parseInt($(this).attr('data-idx'), 10);
            if (store[key]) { store[key].splice(idx, 1); }
            render($item, key);
        });
})(window, jQuery);
/* === WPPOPPOP_UPLOAD_PREVIEW_END === */

/* === WPPOPPOP_UPLOAD_TOGGLE_START === */
(function($) {
    'use strict';
    $(document).off('change.wppUpMulti', '#prop-upload-multiple').on('change.wppUpMulti', '#prop-upload-multiple', function() {
        $('#prop-upload-maxfiles-row').toggle($(this).is(':checked'));
    });
})(jQuery);
/* === WPPOPPOP_UPLOAD_TOGGLE_END === */

/* === WPPOPPOP_PASSWORD_HYDRATE_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    /* WPPOPPOP_PASSWORD_STYLE: hides Opacity, Background, Padding, Box Shadow, Custom CSS & Inline Styles */
    if (!document.getElementById('wppoppop-password-style-css')) {
        var st = document.createElement('style');
        st.id = 'wppoppop-password-style-css';
        st.appendChild(document.createTextNode(
            '#insp-tab-style.wppoppop-pw-style #insp-section-opacity,' +
            '#insp-tab-style.wppoppop-pw-style #insp-section-bg-color,' +
            '#insp-tab-style.wppoppop-pw-style #insp-section-padding,' +
            '#insp-tab-style.wppoppop-pw-style #insp-section-shadow,' +
            '#insp-tab-style.wppoppop-pw-style #insp-section-custom-css { display: none !important; }' +
            '#insp-tab-style.wppoppop-pw-style #insp-section-typography,' +
            '#insp-tab-style.wppoppop-pw-style #insp-section-border,' +
            '#insp-tab-style.wppoppop-pw-style #wppoppop-form-icons-card { display: block !important; }'
        ));
        (document.head || document.documentElement).appendChild(st);
    }
    function pwEl() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return null;
        return String(el.type || '').toLowerCase() === 'password' ? el : false;
    }
    function hydratePw() {
        var el = pwEl();
        $('#insp-tab-style').toggleClass('wppoppop-pw-style', !!el);
        if (!el) return;
        $('#prop-password-placeholder').val(el.placeholder || '');
        $('#prop-password-minlength').val(parseInt(el.minLength, 10) || '');
        $('#prop-password-eye').prop('checked', !(el.showEyeToggle === false || el.showEyeToggle === 'false' || el.showEyeToggle === 0 || el.showEyeToggle === '0'));
        $('#prop-password-autocomplete').val(el.autocomplete || 'current-password');
    }
    $(document).off('click.wppPwHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool')
        .on('click.wppPwHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool', function() {
            [30, 150, 400, 800].forEach(function(ms) { setTimeout(hydratePw, ms); });
        });
    if (typeof Reg.hydrateCoreAndStyleFields === 'function' && !Reg._pwStyleHooked) {
        Reg._pwStyleHooked = true;
        var origHy = Reg.hydrateCoreAndStyleFields;
        Reg.hydrateCoreAndStyleFields = function(el) {
            var r = origHy.apply(this, arguments);
            var isPw = String((el && el.type) || '').toLowerCase() === 'password';
            $('#insp-tab-style').toggleClass('wppoppop-pw-style', isPw);
            if (isPw) { setTimeout(hydratePw, 0); }
            return r;
        };
    }
})(window, jQuery);
/* === WPPOPPOP_PASSWORD_HYDRATE_END === */

/* === WPPOPPOP_PASSWORD_PREVIEW_START === */
(function(window, $) {
    'use strict';
    function inPreviewMode() {
        var C = window.WpPopPopBuilderCanvas, P = window.WpPopPopBuilderPreview;
        return !!((C && C.isPreview) || (P && P.isOpen));
    }
    function ensureInput($stage) {
        var $in = $stage.find('.wppoppop-pw-prev-input');
        if ($in.length) return $in;
        var $txt = $stage.find('.wppoppop-pw-prev-text');
        if (!$txt.length) return $();
        var $n = $('<input type="password" class="wppoppop-pw-prev-input" autocomplete="off" spellcheck="false" autocapitalize="off">')
            .attr('placeholder', $txt.attr('data-ph') || '')
            .css({ flex: '1 1 auto', minWidth: 0, width: '100%', border: 0, outline: 0, background: 'transparent', fontSize: '12px', color: '#0f172a', textAlign: $txt.css('text-align'), pointerEvents: 'auto', padding: 0, margin: 0, height: '100%', boxShadow: 'none' });
        $n.on('mousedown mouseup click touchstart', function(e) { e.stopPropagation(); });
        $txt.replaceWith($n);
        return $n;
    }
    function toggle($stage) {
        var $in = ensureInput($stage), $eye = $stage.find('.wppoppop-pw-prev-eye');
        if (!$in.length) return;
        var show = $in.attr('type') === 'password';
        $in.attr('type', show ? 'text' : 'password');
        $eye.toggleClass('dashicons-visibility', !show).toggleClass('dashicons-hidden', show).attr('aria-label', show ? 'Hide password' : 'Show password');
        try { $in[0].focus(); var l = $in.val().length; $in[0].setSelectionRange(l, l); } catch (e) {}
    }
    document.addEventListener('mousedown', function(e) {
        if (!inPreviewMode() || !e.target || !e.target.closest) return;
        var eye = e.target.closest('.wppoppop-pw-prev-eye');
        var stage = e.target.closest('.wppoppop-canvas-item');
        var $stage = stage ? $(stage).find('.wppoppop-pw-stage').first() : $();
        if (!$stage.length) return;
        if (eye) {
            e.preventDefault(); e.stopPropagation();
            toggle($stage);
        } else if (!$(e.target).closest('.wppoppop-pw-prev-input').length) {
            var $in = ensureInput($stage);
            setTimeout(function() { try { $in[0].focus(); } catch (er) {} }, 0);
        }
    }, true);
    document.addEventListener('click', function(e) {
        if (inPreviewMode() && e.target && e.target.closest && e.target.closest('.wppoppop-pw-prev-eye')) { e.preventDefault(); e.stopPropagation(); }
    }, true);
})(window, jQuery);
/* === WPPOPPOP_PASSWORD_PREVIEW_END === */

/* === WPPOPPOP_ICON_COLOR_BIND_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var SEL = '#prop-form-icon-left-color, #prop-form-icon-right-color';
    $(document).off('input.wppIconColor change.wppIconColor', SEL).on('input.wppIconColor change.wppIconColor', SEL, function(e) {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return;
        var v = $(this).val();
        if (this.id === 'prop-form-icon-left-color') { el.iconLeftColor = v; } else { el.iconRightColor = v; }
        if (typeof Reg.renderFormElementStage === 'function') { Reg.renderFormElementStage(el); }
        if (e.type === 'change' && window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
            window.WpPopPopBuilderCore.pushHistory();
        }
    });
    $(document).off('click.wppIconColorReset', '.wppoppop-icon-color-reset').on('click.wppIconColorReset', '.wppoppop-icon-color-reset', function(e) {
        e.preventDefault();
        $($(this).attr('data-target')).val('#64748b').trigger('input').trigger('change');
    });
    function hydrateIconColors() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return;
        var ok = function(c) { return /^#[0-9a-fA-F]{3,8}$/.test(c || ''); };
        $('#prop-form-icon-left-color').val(ok(el.iconLeftColor) ? el.iconLeftColor : '#64748b');
        $('#prop-form-icon-right-color').val(ok(el.iconRightColor) ? el.iconRightColor : '#64748b');
    }
    $(document).off('click.wppIconColorHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]')
        .on('click.wppIconColorHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]', function() {
            [30, 150, 400].forEach(function(ms) { setTimeout(hydrateIconColors, ms); });
        });
})(window, jQuery);
/* === WPPOPPOP_ICON_COLOR_BIND_END === */

/* === WPPOPPOP_RATING_HYDRATE_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    /* WPPOPPOP_RATING_STYLE: Rating hides every generic Style card */
    if (!document.getElementById('wppoppop-rating-style-css')) {
        var st = document.createElement('style');
        st.id = 'wppoppop-rating-style-css';
        st.appendChild(document.createTextNode(
            '#insp-tab-style.wppoppop-rt-style #insp-section-opacity,' +
            '#insp-tab-style.wppoppop-rt-style #insp-section-bg-color,' +
            '#insp-tab-style.wppoppop-rt-style #insp-section-padding,' +
            '#insp-tab-style.wppoppop-rt-style #insp-section-shadow,' +
            '#insp-tab-style.wppoppop-rt-style #insp-section-custom-css,' +
            '#insp-tab-style.wppoppop-rt-style #wppoppop-form-icons-card,' +
            '#insp-tab-style.wppoppop-rt-style #insp-section-typography,' +
            '#insp-tab-style.wppoppop-rt-style #insp-section-border { display: none !important; }'
        ));
        (document.head || document.documentElement).appendChild(st);
    }
    function rtEl() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return null;
        return String(el.type || '').toLowerCase() === 'rating' ? el : false;
    }
    function hydrateRt() {
        var el = rtEl();
        $('#insp-tab-style').toggleClass('wppoppop-rt-style', !!el);
        if (!el) return;
        var ok = function(c) { return /^#[0-9a-fA-F]{3,8}$/.test(c || ''); };
        $('#prop-rating-scale').val(parseInt(el.ratingMax, 10) === 10 ? '10' : '5');
        $('#prop-rating-glyph').val(el.ratingShape || 'star');
        $('#prop-rating-active-color').val(ok(el.ratingColor) ? el.ratingColor : '#f59e0b');
        $('#prop-rating-inactive-color').val(ok(el.ratingInactiveColor) ? el.ratingInactiveColor : '#cbd5e1');
    }
    $(document).off('click.wppRtHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool')
        .on('click.wppRtHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool', function() {
            [30, 150, 400, 800].forEach(function(ms) { setTimeout(hydrateRt, ms); });
        });
    if (typeof Reg.hydrateCoreAndStyleFields === 'function' && !Reg._rtStyleHooked) {
        Reg._rtStyleHooked = true;
        var origHy = Reg.hydrateCoreAndStyleFields;
        Reg.hydrateCoreAndStyleFields = function(el) {
            var r = origHy.apply(this, arguments);
            var isRt = String((el && el.type) || '').toLowerCase() === 'rating';
            $('#insp-tab-style').toggleClass('wppoppop-rt-style', isRt);
            if (isRt) { setTimeout(hydrateRt, 0); }
            return r;
        };
    }
})(window, jQuery);
/* === WPPOPPOP_RATING_HYDRATE_END === */

/* === WPPOPPOP_RATING_DATA_TAB_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var ACC = '#a3195b';
    if (!document.getElementById('wppoppop-rating-data-css')) {
        var st = document.createElement('style');
        st.id = 'wppoppop-rating-data-css';
        st.appendChild(document.createTextNode(
            '#insp-tab-data.wppoppop-rt-data > *:not(#insp-rating-data){display:none !important;}' +
            '#insp-rating-data{display:none;padding:14px;}' +
            '#insp-tab-data.wppoppop-rt-data > #insp-rating-data{display:block !important;}' +
            '#insp-rating-data .rt-lbl{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:#e2e8f0;text-transform:uppercase;margin:0 0 6px;}' +
            '#insp-rating-data .rt-help{display:inline-flex;align-items:center;justify-content:center;width:14px;height:14px;border-radius:50%;background:#e2e8f0;color:#0f172a;font-size:10px;font-weight:800;line-height:1;cursor:help;flex-shrink:0;}' +
            '#insp-rating-data .rt-field{width:100%;height:36px;background:#090d16;border:1px solid #1e293b;color:#f8fafc;border-radius:6px;padding:0 10px;font-size:12px;box-sizing:border-box;}' +
            '#insp-rating-data .rt-row{margin-bottom:16px;}' +
            '#insp-rating-data .rt-sw{position:relative;display:inline-block;width:46px;height:26px;flex-shrink:0;}' +
            '#insp-rating-data .rt-sw input{position:absolute;opacity:0;width:0;height:0;}' +
            '#insp-rating-data .rt-sl{position:absolute;inset:0;background:#475569;border-radius:26px;cursor:pointer;transition:background .15s ease;}' +
            '#insp-rating-data .rt-sl::before{content:"";position:absolute;left:3px;top:3px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform .15s ease;}' +
            '#insp-rating-data .rt-sw input:checked + .rt-sl{background:' + ACC + ';}' +
            '#insp-rating-data .rt-sw input:checked + .rt-sl::before{transform:translateX(20px);}' +
            '#insp-rating-data .rt-sw input:focus + .rt-sl{box-shadow:0 0 0 2px rgba(163,25,91,.45);}'
        ));
        (document.head || document.documentElement).appendChild(st);
    }
    function help(t) { return '<span class="rt-help" title="' + t + '" aria-label="' + t + '">?</span>'; }
    function card() {
        return '<div id="insp-rating-data">' +
            '<div class="rt-row"><label class="rt-lbl" for="prop-rt-data-stars">Number of Stars ' + help('How many stars or icons the rating scale shows.') + '</label>' +
                '<select id="prop-rt-data-stars" class="rt-field"><option value="5">5 Stars</option><option value="10">10 Stars</option></select></div>' +
            '<div class="rt-row"><label class="rt-lbl" for="prop-rt-data-default">Default Rating ' + help('Pre-selected rating when the form loads. Choose No rating to leave it empty.') + '</label>' +
                '<select id="prop-rt-data-default" class="rt-field"></select></div>' +
            '<div class="rt-row"><label class="rt-lbl" for="prop-rt-data-dynamic" style="justify-content:space-between;"><span style="display:inline-flex;align-items:center;gap:6px;">Dynamic Default Value ' + help('Fill the rating from a URL query parameter, e.g. ?rating=4.') + '</span>' +
                '<span class="rt-sw"><input type="checkbox" id="prop-rt-data-dynamic"><span class="rt-sl"></span></span></label></div>' +
            '<div class="rt-row" id="prop-rt-data-param-row" style="display:none;"><label class="rt-lbl" for="prop-rt-data-param">Parameter Name ' + help('The URL parameter to read, e.g. rating for ?rating=4.') + '</label>' +
                '<input type="text" id="prop-rt-data-param" class="rt-field" placeholder="e.g. rating"></div>' +
            '<div class="rt-row"><label class="rt-lbl" for="prop-rt-data-savedb" style="justify-content:space-between;"><span style="display:inline-flex;align-items:center;gap:6px;">Save to Database ' + help('Store the submitted rating with the lead record.') + '</span>' +
                '<span class="rt-sw"><input type="checkbox" id="prop-rt-data-savedb" checked><span class="rt-sl"></span></span></label></div>' +
            '</div>';
    }
    function ensureCard() {
        var $tab = $('#insp-tab-data');
        if (!$tab.length) return false;
        if (!$('#insp-rating-data').length) { $tab.append(card()); }
        return true;
    }
    function rtEl() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return null;
        return String(el.type || '').toLowerCase() === 'rating' ? el : false;
    }
    function fillDefaults(max, cur) {
        var h = '<option value="">No rating</option>';
        for (var i = 1; i <= max; i++) { h += '<option value="' + i + '">' + i + (i === 1 ? ' Star' : ' Stars') + '</option>'; }
        var $d = $('#prop-rt-data-default').html(h);
        var n = parseInt(cur, 10);
        $d.val((n >= 1 && n <= max) ? String(n) : '');
    }
    function hydrate() {
        var el = rtEl();
        var ready = ensureCard();
        $('#insp-tab-data').toggleClass('wppoppop-rt-data', !!el);
        if (!el || !ready) return;
        var max = parseInt(el.ratingMax, 10) === 10 ? 10 : 5;
        $('#prop-rt-data-stars').val(String(max));
        fillDefaults(max, el.defaultValue);
        var dyn = (el.dynamicDefault === true || el.dynamicDefault === 1 || el.dynamicDefault === '1' || el.dynamicDefault === 'true');
        $('#prop-rt-data-dynamic').prop('checked', dyn);
        $('#prop-rt-data-param').val(el.paramName || '');
        $('#prop-rt-data-param-row').toggle(dyn);
        $('#prop-rt-data-savedb').prop('checked', !(el.saveToDb === false || el.saveToDb === 'false' || el.saveToDb === 0 || el.saveToDb === '0'));
    }
    function commit(e) {
        if (e && e.type === 'change' && window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
            window.WpPopPopBuilderCore.pushHistory();
        }
    }
    function render(el) { if (typeof Reg.renderFormElementStage === 'function') { Reg.renderFormElementStage(el); } }

    $(document).off('input.wppRtData change.wppRtData', '#prop-rt-data-stars').on('input.wppRtData change.wppRtData', '#prop-rt-data-stars', function(e) {
        var el = rtEl(); if (!el) return;
        var max = parseInt($(this).val(), 10) === 10 ? 10 : 5;
        el.ratingMax = max;
        var cur = parseInt(el.defaultValue, 10);
        if (cur > max) { el.defaultValue = String(max); }
        fillDefaults(max, el.defaultValue);
        $('#prop-rating-scale').val(String(max));
        render(el); commit(e);
    });
    $(document).off('input.wppRtData change.wppRtData', '#prop-rt-data-default').on('input.wppRtData change.wppRtData', '#prop-rt-data-default', function(e) {
        var el = rtEl(); if (!el) return;
        el.defaultValue = $(this).val() || '';
        render(el); commit(e);
    });
    $(document).off('change.wppRtData', '#prop-rt-data-dynamic').on('change.wppRtData', '#prop-rt-data-dynamic', function(e) {
        var el = rtEl(); if (!el) return;
        el.dynamicDefault = $(this).is(':checked');
        $('#prop-rt-data-param-row').toggle(el.dynamicDefault);
        commit(e);
    });
    $(document).off('input.wppRtData change.wppRtData', '#prop-rt-data-param').on('input.wppRtData change.wppRtData', '#prop-rt-data-param', function(e) {
        var el = rtEl(); if (!el) return;
        el.paramName = $.trim($(this).val() || '');
        commit(e);
    });
    $(document).off('change.wppRtData', '#prop-rt-data-savedb').on('change.wppRtData', '#prop-rt-data-savedb', function(e) {
        var el = rtEl(); if (!el) return;
        el.saveToDb = $(this).is(':checked');
        commit(e);
    });
    /* keep Content-tab "Scale Max" and this tab in sync */
    $(document).off('change.wppRtDataSync', '#prop-rating-scale').on('change.wppRtDataSync', '#prop-rating-scale', function() { setTimeout(hydrate, 0); });

    $(document).off('click.wppRtDataHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool')
        .on('click.wppRtDataHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool', function() {
            [30, 150, 400, 800].forEach(function(ms) { setTimeout(hydrate, ms); });
        });
    if (typeof Reg.hydrateCoreAndStyleFields === 'function' && !Reg._rtDataHooked) {
        Reg._rtDataHooked = true;
        var orig = Reg.hydrateCoreAndStyleFields;
        Reg.hydrateCoreAndStyleFields = function(el) {
            var r = orig.apply(this, arguments);
            setTimeout(hydrate, 0);
            return r;
        };
    }
    $(function() { ensureCard(); });
})(window, jQuery);
/* === WPPOPPOP_RATING_DATA_TAB_END === */

/* === WPPOPPOP_SUBMIT_HYDRATE_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    /* WPPOPPOP_SUBMIT_STYLE: hides Opacity, Background, Padding, Box Shadow, Border & Corner Radius */
    if (!document.getElementById('wppoppop-submit-style-css')) {
        var st = document.createElement('style');
        st.id = 'wppoppop-submit-style-css';
        st.appendChild(document.createTextNode(
            '#insp-tab-style.wppoppop-sb-style #insp-section-opacity,' +
            '#insp-tab-style.wppoppop-sb-style #insp-section-padding,' +
            '#insp-tab-style.wppoppop-sb-style #insp-section-border { display: none !important; }' +
            '#insp-tab-style.wppoppop-sb-style #insp-section-bg-color { display: block !important; }'
        ));
        (document.head || document.documentElement).appendChild(st);
    }
    function isSubmitType(t) { t = String(t || '').toLowerCase(); return t === 'submit' || t === 'submit_btn'; }
    function sbEl() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return null;
        return isSubmitType(el.type) ? el : false;
    }
    function toggleRows(action) {
        $('#prop-submit-canvas-wrap').toggle(action === 'next_canvas');
        $('#prop-submit-redirect-wrap').toggle(action === 'redirect');
        $('#prop-submit-confirm-wrap').toggle(action === 'default');
    }
    function hydrateSb() {
        var el = sbEl();
        $('#insp-tab-style').toggleClass('wppoppop-sb-style', !!el);
        if (!el) return;
        var act = (['default', 'next_canvas', 'redirect'].indexOf(el.submitAction) !== -1) ? el.submitAction : 'default';
        $('#prop-submit-label').val(el.content || '');
        $('#prop-submit-loading').val(el.loadingText || '');
        $('#prop-submit-action').val(act);
        $('#prop-submit-canvas').val(parseInt(el.goto_canvas, 10) || '');
        $('#prop-submit-redirect').val(el.redirectUrl || '');
        $('#prop-submit-confirm').val(el.confirmMessage || '');
        toggleRows(act);
    }
    $(document).off('change.wppSbAct input.wppSbAct', '#prop-submit-action').on('change.wppSbAct input.wppSbAct', '#prop-submit-action', function() {
        toggleRows($(this).val());
    });
    $(document).off('click.wppSbHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool')
        .on('click.wppSbHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool', function() {
            [30, 150, 400, 800].forEach(function(ms) { setTimeout(hydrateSb, ms); });
        });
    if (typeof Reg.hydrateCoreAndStyleFields === 'function' && !Reg._sbStyleHooked) {
        Reg._sbStyleHooked = true;
        var orig = Reg.hydrateCoreAndStyleFields;
        Reg.hydrateCoreAndStyleFields = function(el) {
            var r = orig.apply(this, arguments);
            var is = !!(el && isSubmitType(el.type));
            $('#insp-tab-style').toggleClass('wppoppop-sb-style', is);
            if (is) { setTimeout(hydrateSb, 0); }
            return r;
        };
    }
})(window, jQuery);
/* === WPPOPPOP_SUBMIT_HYDRATE_END === */

/* === WPPOPPOP_SUBMIT_DEFAULTS_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var BG = '#2563eb', FG = '#ffffff';
    function isSubmit(t) { t = String(t || '').toLowerCase(); return t === 'submit' || t === 'submit_btn'; }
    function hex(c) { return /^#[0-9a-fA-F]{3,8}$/.test(c || ''); }

    /* Default look: blue background, white text (old fallbacks #fff / #1e293b count as unset) */
    function ensureDefaults(el) {
        if (!el || !isSubmit(el.type)) return;
        var bg = String(el.bgColor || '').toLowerCase();
        if (!(el.bgCustom === true || el.bgCustom === 1 || el.bgCustom === '1' || el.bgCustom === 'true') && (!hex(bg) || bg === '#ffffff' || bg === '#fff' || el.transparentFill)) { /* WPPOPPOP_SUBMIT_BG_CUSTOM */
            el.bgColor = BG; el.transparentFill = false;
        }
        var fg = String(el.color || '').toLowerCase();
        if (!hex(fg) || fg === '#1e293b') { el.color = FG; }
    }
    if (typeof Reg.renderMediaElementStage === 'function' && !Reg._sbDefStage) {
        Reg._sbDefStage = true;
        var oStage = Reg.renderMediaElementStage;
        Reg.renderMediaElementStage = function(el) { ensureDefaults(el); return oStage.apply(this, arguments); };
    }
    if (typeof Reg.hydrateCoreAndStyleFields === 'function' && !Reg._sbDefHydrate) {
        Reg._sbDefHydrate = true;
        var oHy = Reg.hydrateCoreAndStyleFields;
        Reg.hydrateCoreAndStyleFields = function(el) {
            ensureDefaults(el);
            var r = oHy.apply(this, arguments);
            if (el && isSubmit(el.type)) { $('#prop-bg-color').val(el.bgColor); $('#prop-color').val(el.color); }
            return r;
        };
    }

    /* Dynamic "Next canvas" list */
    function info() {
        var C = window.WpPopPopBuilderCore, st = (C && C.state) || {};
        var seen = {}, list = [];
        [st.canvases, st.canvasMeta].forEach(function(o) {
            if (o && typeof o === 'object') {
                Object.keys(o).forEach(function(k) {
                    var n = parseInt(k, 10);
                    if (n > 0 && !seen[n]) { seen[n] = 1; list.push(n); }
                });
            }
        });
        if (!list.length) list = [1];
        list.sort(function(a, b) { return a - b; });
        return { list: list, current: parseInt(st.currentCanvas, 10) || 1, meta: st.canvasMeta || {} };
    }
    function label(n, meta) {
        var m = meta && meta[n], nm = m && (m.name || m.title || m.label);
        return 'Canvas ' + n + (nm ? ' \u2013 ' + nm : '');
    }
    function build(sel) {
        var $s = $('#prop-submit-canvas');
        if (!$s.length || !$s.is('select')) return;
        var i = info(), want = parseInt(sel, 10) || 0;
        var list = i.list.slice();
        var missing = want > 0 && list.indexOf(want) === -1;
        if (missing) list.push(want);
        var sig = list.join(',') + '|' + i.current + '|' + JSON.stringify(list.map(function(n) { return label(n, i.meta); }));
        if ($s.data('sig') !== sig) {
            var h = '<option value="">Select a canvas...</option>';
            list.forEach(function(n) {
                var isCur = (n === i.current);
                var txt = label(n, i.meta) + (isCur ? ' (this canvas)' : '') + ((missing && n === want) ? ' (missing)' : '');
                h += '<option value="' + n + '"' + (isCur && n !== want ? ' disabled' : '') + '>' + $('<div>').text(txt).html() + '</option>';
            });
            $s.html(h).data('sig', sig);
        }
        $s.val(want > 0 ? String(want) : '');
    }
    function sbEl() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        return (el && isSubmit(el.type)) ? el : null;
    }
    function hydrateCanvas() {
        var el = sbEl();
        if (!el) return;
        var want = parseInt(el.goto_canvas, 10) || 0;
        if (!want) {
            var i = info(), nxt = i.current + 1;
            if (i.list.indexOf(nxt) !== -1) { want = nxt; el.goto_canvas = nxt; el.goto_screen = nxt; }
        }
        build(want);
    }
    $(document).off('mousedown.wppSbCv focus.wppSbCv', '#prop-submit-canvas').on('mousedown.wppSbCv focus.wppSbCv', '#prop-submit-canvas', function() {
        var el = sbEl();
        if (el) { build($(this).val() || el.goto_canvas); }
    });
    $(document).off('change.wppSbCv', '#prop-submit-canvas').on('change.wppSbCv', '#prop-submit-canvas', function(e) {
        var el = sbEl(); if (!el) return;
        var v = parseInt($(this).val(), 10) || 0;
        el.goto_canvas = v; el.goto_screen = v;
        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') { window.WpPopPopBuilderCore.pushHistory(); }
    });
    $(document).off('click.wppSbCvHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool')
        .on('click.wppSbCvHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab], .wppoppop-ribbon-btn, .wppoppop-ribbon-tool', function() {
            [60, 200, 450, 900].forEach(function(ms) { setTimeout(hydrateCanvas, ms); });
        });
})(window, jQuery);
/* === WPPOPPOP_SUBMIT_DEFAULTS_END === */

/* === WPPOPPOP_SUBMIT_BG_BIND_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    function sbEl() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        var t = el ? String(el.type || '').toLowerCase() : '';
        return (t === 'submit' || t === 'submit_btn') ? el : null;
    }
    $(document).off('input.wppSbBg change.wppSbBg', '#prop-bg-color, #prop-bg-transparent')
        .on('input.wppSbBg change.wppSbBg', '#prop-bg-color, #prop-bg-transparent', function(e) {
            var el = sbEl();
            if (!el) return;
            el.bgCustom = true;
            el.bgColor = $('#prop-bg-color').val() || el.bgColor;
            el.transparentFill = $('#prop-bg-transparent').is(':checked');
            if (typeof Reg.renderMediaElementStage === 'function') { Reg.renderMediaElementStage(el); }
            if (e.type === 'change' && window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                window.WpPopPopBuilderCore.pushHistory();
            }
        });
})(window, jQuery);
/* === WPPOPPOP_SUBMIT_BG_BIND_END === */

/* === WPPOPPOP_SUBMIT_ICONS_SHADOW_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    if (!document.getElementById('wppoppop-submit-icons-css')) {
        var st = document.createElement('style');
        st.id = 'wppoppop-submit-icons-css';
        st.appendChild(document.createTextNode(
            '#insp-tab-style.wppoppop-sb-style #wppoppop-form-style-module{display:block!important;}' +
            '#insp-tab-style.wppoppop-sb-style #wppoppop-form-style-module > div:not(#wppoppop-form-icons-card){display:none!important;}' +
            '#insp-tab-style.wppoppop-sb-style #wppoppop-form-icons-card,' +
            '#insp-tab-style.wppoppop-sb-style #insp-section-shadow{display:block!important;}'
        ));
        (document.head || document.documentElement).appendChild(st);
    }
    function sbEl() {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        var t = el ? String(el.type || '').toLowerCase() : '';
        return (t === 'submit' || t === 'submit_btn') ? el : null;
    }
    function fixShadow() {
        var el = sbEl(); if (!el) return;
        $('#prop-shadow-x').val(parseInt(el.shadowX, 10) || 0);
        $('#prop-shadow-y').val(parseInt(el.shadowY, 10) || 0);
        $('#prop-shadow-blur').val(parseInt(el.shadowBlur, 10) || 0);
        $('#prop-shadow-spread').val(parseInt(el.shadowSpread, 10) || 0);
        $('#prop-shadow-color').val(/^#[0-9a-fA-F]{6}$/.test(el.shadowColor || '') ? el.shadowColor : '#000000');
    }
    $(document).off('click.wppSbShadow', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]')
        .on('click.wppSbShadow', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]', function() {
            [60, 250, 600, 1000].forEach(function(ms) { setTimeout(fixShadow, ms); });
        });
    var SEL = '#prop-form-icon-left, #prop-form-icon-left-offset, #prop-form-icon-right, #prop-form-icon-right-offset, #prop-form-icon-left-color, #prop-form-icon-right-color';
    $(document).off('input.wppSbIc change.wppSbIc', SEL).on('input.wppSbIc change.wppSbIc', SEL, function() {
        var el = sbEl(); if (!el) return;
        setTimeout(function() {
            var n = function(id) { var v = parseInt($(id).val(), 10); return isNaN(v) ? 10 : v; };
            el.iconLeft = $('#prop-form-icon-left').val() || '';
            el.iconRight = $('#prop-form-icon-right').val() || '';
            el.iconLeftOffset = n('#prop-form-icon-left-offset');
            el.iconRightOffset = n('#prop-form-icon-right-offset');
            if ($('#prop-form-icon-left-color').length) { el.iconLeftColor = $('#prop-form-icon-left-color').val(); }
            if ($('#prop-form-icon-right-color').length) { el.iconRightColor = $('#prop-form-icon-right-color').val(); }
            if (typeof Reg.renderMediaElementStage === 'function') { Reg.renderMediaElementStage(el); }
        }, 0);
    });
})(window, jQuery);
/* === WPPOPPOP_SUBMIT_ICONS_SHADOW_END === */
