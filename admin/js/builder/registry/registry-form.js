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

    function formatIconTag(cls, posStyle) {
        var c = $.trim(cls || '');
        if (c && !/^[A-Za-z][A-Za-z0-9 _-]*$/.test(c)) {
            return '<span aria-hidden="true" style="' + posStyle + 'color:#64748b;font-size:14px;line-height:1;pointer-events:none;">' + $('<div>').text(c).html() + '</span>';
        }
        if (!c) return '';
        var full = (c.indexOf('dashicons-') === 0 && c.indexOf('dashicons ') === -1) ? ('dashicons ' + c) : c;
        return '<i class="' + full + '" style="' + posStyle + 'color:#64748b;font-size:14px;width:14px;height:14px;line-height:1;display:inline-flex;align-items:center;justify-content:center;pointer-events:none;"></i>';
    }

    Reg.renderFormElementStage = function(el) {
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

        var iLeft  = $.trim(el.iconLeft || ((canonical === 'date' ? 'dashicons-calendar-alt' : (canonical === 'time' ? 'dashicons-clock' : (canonical === 'password' ? 'dashicons-lock' : '')))));
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
            var rOpts = parseOptionsList(el.optionsList || el.options, ['Choice A', 'Choice B', 'Choice C']);
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
                var isSel = defVal ? (opt.toLowerCase() === defVal.toLowerCase()) : false;
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
            var tStep = parseInt(el.timeStep, 10) || 15;
            var tmPh  = defVal || el.placeholder || (tFmt === '24h' ? '14:00' : '02:00 PM');
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding-left:' + padL + 'px;padding-right:10px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<span style="flex:1;text-align:' + align + ';color:#334155;font-size:12px;">' + $('<div>').text(tmPh).html() + reqStar + '</span>' +
                '<span style="font-size:9px;color:#94a3b8;background:#f1f5f9;padding:2px 5px;border-radius:3px;">' + tFmt + ' (' + tStep + 'm)</span>' +
                '</div>';
        } else if (canonical === 'upload') {
            var uPh   = el.placeholder || 'Choose file or drag here...';
            var uExts = el.allowedExtensions || '.jpg,.png,.pdf';
            var uMax  = parseInt(el.maxFileSize, 10) || 5;
            innerHtml = '<div style="width:100%;height:100%;border:1.5px dashed #94a3b8;border-radius:6px;background:#f8fafc;display:flex;align-items:center;justify-content:center;gap:8px;padding:6px 10px;box-sizing:border-box;pointer-events:none;">' +
                '<span class="dashicons dashicons-cloud-upload" style="font-size:20px;width:20px;height:20px;color:#0284c7;"></span>' +
                '<div style="display:flex;flex-direction:column;line-height:1.2;">' +
                    '<span style="font-size:11px;font-weight:600;color:#334155;">' + $('<div>').text(uPh).html() + reqStar + '</span>' +
                    '<span style="font-size:9px;color:#64748b;">' + $('<div>').text(uExts).html() + ' (Max ' + uMax + 'MB)</span>' +
                '</div>' +
                '</div>';
        } else if (canonical === 'password') {
            var pwPh = el.placeholder || '••••••••';
            var showEye = (el.showEyeToggle !== false);
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding-left:' + padL + 'px;padding-right:10px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<span style="flex:1;text-align:' + align + ';color:#64748b;font-size:12px;">' + $('<div>').text(pwPh).html() + reqStar + '</span>' +
                (showEye ? '<span class="dashicons dashicons-visibility" style="font-size:16px;width:16px;height:16px;color:#64748b;"></span>' : '') +
                '</div>';
        } else if (canonical === 'rating') {
            var rMax   = parseInt(el.ratingMax, 10) === 10 ? 10 : 5;
            var rShape = el.ratingShape || 'star';
            var rCol   = el.ratingColor || '#f59e0b';
            var rScore = Math.min(rMax, Math.max(0, parseInt(defVal, 10) || 4));
            var glyph  = rShape === 'heart' ? '&#9829;' : (rShape === 'thumb' ? '&#128077;' : '&#9733;');
            var justify = align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start');
            var starsHtml = '';
            for (var s = 1; s <= rMax; s++) {
                var c = (s <= rScore) ? rCol : '#cbd5e1';
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
