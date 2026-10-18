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

        var iLeft  = $.trim(el.iconLeft || (canonical === 'email' ? 'dashicons-email' : (canonical === 'date' ? 'dashicons-calendar-alt' : (canonical === 'time' ? 'dashicons-clock' : (canonical === 'password' ? 'dashicons-lock' : '')))));
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
            var minV = el.min !== undefined ? el.min : 0;
            var maxV = el.max !== undefined ? el.max : 100;
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding-left:' + padL + 'px;padding-right:10px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<span style="flex:1;text-align:' + align + ';color:#334155;font-size:12px;">' + $('<div>').text(nPh).html() + reqStar + '</span>' +
                '<span style="font-size:9px;color:#94a3b8;margin-right:6px;">[' + minV + '–' + maxV + ']</span>' +
                '<span style="display:flex;flex-direction:column;font-size:8px;line-height:1;color:#64748b;">&#9650;<br>&#9660;</span>' +
                '</div>';
        } else if (canonical === 'tel') {
            var dial = $.trim(el.countryCode || '+1');
            var tPh  = defVal || el.placeholder || el.inputMask || '(555) 000-0000';
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;overflow:hidden;box-sizing:border-box;pointer-events:none;">' +
                '<span style="background:#f1f5f9;color:#334155;font-size:11px;font-weight:700;padding:0 10px;height:100%;display:flex;align-items:center;border-right:1px solid #cbd5e1;">' + $('<div>').text(dial).html() + '</span>' +
                '<span style="flex:1;padding:0 10px;text-align:' + align + ';color:#64748b;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(tPh).html() + reqStar + '</span>' +
                rightIconHtml +
                '</div>';
        } else if (canonical === 'textarea') {
            var taPh = defVal || el.placeholder || 'Write your message...';
            var maxC = parseInt(el.maxLength, 10) || 500;
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;flex-direction:column;justify-content:space-between;padding:8px 10px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                '<div style="text-align:' + align + ';color:#64748b;font-size:12px;line-height:1.4;overflow:hidden;">' + $('<div>').text(taPh).html() + reqStar + '</div>' +
                '<div style="text-align:right;font-size:9px;color:#94a3b8;">0 / ' + maxC + '</div>' +
                '</div>';
        } else if (canonical === 'select') {
            var sOpts = parseOptionsList(el.options, ['Option 1', 'Option 2', 'Option 3']);
            var sDisp = defVal || el.placeholder || sOpts[0] || 'Select an option...';
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding:0 12px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                '<span style="flex:1;text-align:' + align + ';color:#334155;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(sDisp).html() + reqStar + '</span>' +
                '<span class="dashicons dashicons-arrow-down-alt2" style="font-size:16px;width:16px;height:16px;color:#64748b;"></span>' +
                '</div>';
        } else if (canonical === 'checkbox') {
            var cbOpts = parseOptionsList(el.options, []);
            var isChecked = !!el.checkedDefault || (defVal !== '' && defVal !== '0' && defVal !== 'false');
            var cbDir = (el.checkboxLayout === 'horizontal') ? 'row' : 'column';
            if (!cbOpts.length) {
                var cLbl = el.checkboxLabel || 'I agree to the Terms and Privacy Policy';
                innerHtml = '<div style="width:100%;height:100%;display:flex;align-items:center;gap:8px;padding:4px 6px;box-sizing:border-box;pointer-events:none;">' +
                    '<span style="width:16px;height:16px;border-radius:4px;border:1.5px solid ' + (isChecked ? '#c2185b' : '#94a3b8') + ';background:' + (isChecked ? '#c2185b' : '#ffffff') + ';color:#ffffff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;">' + (isChecked ? '&#10003;' : '') + '</span>' +
                    '<span style="font-size:12px;color:#1e293b;line-height:1.3;">' + $('<div>').text(cLbl).html() + reqStar + '</span>' +
                    '</div>';
            } else {
                var cbItemsHtml = cbOpts.map(function(opt, idx) {
                    var active = (idx === 0 && isChecked);
                    return '<span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;color:#1e293b;">' +
                        '<span style="width:14px;height:14px;border-radius:3px;border:1.5px solid ' + (active ? '#c2185b' : '#94a3b8') + ';background:' + (active ? '#c2185b' : '#ffffff') + ';color:#ffffff;display:inline-flex;align-items:center;justify-content:center;font-size:9px;">' + (active ? '&#10003;' : '') + '</span>' +
                        $('<div>').text(opt).html() + '</span>';
                }).join('');
                innerHtml = '<div style="width:100%;height:100%;display:flex;flex-direction:' + cbDir + ';flex-wrap:wrap;align-items:center;gap:8px;padding:4px 6px;box-sizing:border-box;pointer-events:none;">' + cbItemsHtml + '</div>';
            }
        } else if (canonical === 'radio') {
            var rOpts = parseOptionsList(el.options, ['Choice A', 'Choice B', 'Choice C']);
            var rDir  = (el.radioLayout === 'vertical') ? 'column' : 'row';
            var rHtml = rOpts.map(function(opt, idx) {
                var isSel = defVal ? (opt.toLowerCase() === defVal.toLowerCase()) : (idx === 0);
                return '<span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;color:#1e293b;">' +
                    '<span style="width:14px;height:14px;border-radius:50%;border:1.5px solid ' + (isSel ? '#c2185b' : '#94a3b8') + ';display:inline-flex;align-items:center;justify-content:center;background:#ffffff;">' +
                    (isSel ? '<span style="width:7px;height:7px;border-radius:50%;background:#c2185b;"></span>' : '') +
                    '</span>' + $('<div>').text(opt).html() + '</span>';
            }).join('');
            innerHtml = '<div style="width:100%;height:100%;display:flex;flex-direction:' + rDir + ';flex-wrap:wrap;align-items:center;gap:10px;padding:4px 6px;box-sizing:border-box;pointer-events:none;">' + rHtml + '</div>';
        } else if (canonical === 'multiselect') {
            var mOpts = parseOptionsList(el.options, ['Marketing', 'Design', 'Development']);
            var maxS  = parseInt(el.maxSelect, 10) || 0;
            var pills = mOpts.slice(0, 4).map(function(o, i) {
                var sel = (i === 0);
                return '<span style="padding:2px 7px;border-radius:4px;font-size:10px;font-weight:600;background:' + (sel ? '#c2185b' : '#f1f5f9') + ';color:' + (sel ? '#ffffff' : '#475569') + ';border:1px solid ' + (sel ? '#c2185b' : '#cbd5e1') + ';">' + $('<div>').text(o).html() + '</span>';
            }).join('');
            innerHtml = '<div style="width:100%;height:100%;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;padding:6px 8px;display:flex;flex-wrap:wrap;align-items:center;gap:5px;box-sizing:border-box;pointer-events:none;">' +
                pills + (maxS > 0 ? '<span style="margin-left:auto;font-size:9px;color:#94a3b8;">Max: ' + maxS + '</span>' : '') +
                '</div>';
        } else if (canonical === 'imageselect') {
            var imgOpts = parseOptionsList(el.options, ['Starter', 'Pro', 'Enterprise']);
            var iCols   = Math.min(4, Math.max(1, parseInt(el.columns, 10) || 3));
            var iAcc    = el.activeColor || '#c2185b';
            var cards   = imgOpts.slice(0, iCols).map(function(item, idx) {
                var parts = item.split('::');
                var lbl = $.trim(parts[0] || 'Choice');
                var active = (idx === 0);
                return '<div style="border:2px solid ' + (active ? iAcc : '#cbd5e1') + ';border-radius:6px;background:#ffffff;padding:4px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;">' +
                    '<span class="dashicons dashicons-format-image" style="font-size:18px;width:18px;height:18px;color:' + (active ? iAcc : '#94a3b8') + ';margin-bottom:2px;"></span>' +
                    '<span style="font-size:10px;font-weight:600;color:#1e293b;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(lbl).html() + '</span>' +
                    '</div>';
            }).join('');
            innerHtml = '<div style="width:100%;height:100%;display:grid;grid-template-columns:repeat(' + iCols + ',1fr);gap:6px;box-sizing:border-box;pointer-events:none;">' + cards + '</div>';
        } else if (canonical === 'tile') {
            var tOpts = parseOptionsList(el.options, ['$10', '$25', '$50', '$100']);
            var tCols = Math.min(4, Math.max(1, parseInt(el.columns, 10) || 4));
            var tAcc  = el.activeColor || '#c2185b';
            var tiles = tOpts.slice(0, tCols).map(function(lbl, idx) {
                var active = defVal ? (lbl.toLowerCase() === defVal.toLowerCase()) : (idx === 0);
                return '<div style="border:1.5px solid ' + (active ? tAcc : '#cbd5e1') + ';background:' + (active ? tAcc : '#ffffff') + ';color:' + (active ? '#ffffff' : '#334155') + ';border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;padding:4px;">' +
                    $('<div>').text(lbl).html() + '</div>';
            }).join('');
            innerHtml = '<div style="width:100%;height:100%;display:grid;grid-template-columns:repeat(' + tCols + ',1fr);gap:6px;box-sizing:border-box;pointer-events:none;">' + tiles + '</div>';
        } else if (canonical === 'date') {
            var dFmt = el.dateFormat || 'YYYY-MM-DD';
            var dPh  = defVal || el.placeholder || dFmt;
            innerHtml = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding-left:' + padL + 'px;padding-right:10px;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;pointer-events:none;">' +
                leftIconHtml +
                '<span style="flex:1;text-align:' + align + ';color:#334155;font-size:12px;">' + $('<div>').text(dPh).html() + reqStar + '</span>' +
                '<span style="font-size:9px;color:#94a3b8;background:#f1f5f9;padding:2px 5px;border-radius:3px;">' + dFmt + '</span>' +
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
        /* WPPOPPOP_TF_NOPAD */
        if (canonical === 'text' && $node[0]) {
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
