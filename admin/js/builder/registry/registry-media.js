/**
 * WpPopPop Builder Registry Module: registry-media
 * Split from builder-elements-registry.js (load order matters; see class-admin-assets.php)
 */
/* === WPPOPPOP_PHASE2_MEDIA_FIELD_VERIFICATION_START === */
(function(window, $) {
    'use strict';

    window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var Reg = window.WpPopPopElementsRegistry;

    var SVG_SHAPE_PATHS = {
        'rectangle': '<rect x="0" y="0" width="100" height="100" rx="0" />',
        'rect':      '<rect x="0" y="0" width="100" height="100" rx="0" />',
        'square':    '<rect x="0" y="0" width="100" height="100" rx="0" />',
        'rounded':   '<rect x="0" y="0" width="100" height="100" rx="16" />',
        'circle':    '<circle cx="50" cy="50" r="48" />',
        'pill':      '<rect x="2" y="15" width="96" height="70" rx="35" />',
        'triangle':  '<polygon points="50,4 96,96 4,96" />',
        'diamond':   '<polygon points="50,2 98,50 50,98 2,50" />',
        'star':      '<polygon points="50,3 63,36 98,38 70,60 79,95 50,75 21,95 30,60 2,38 37,36" />',
        'heart':     '<path d="M50,88 C50,88 10,60 10,32 C10,16 23,6 37,6 C45,6 50,12 50,18 C50,12 55,6 63,6 C77,6 90,16 90,32 C90,60 50,88 50,88 Z" />',
        'pentagon':  '<polygon points="50,3 97,38 79,94 21,94 3,38" />',
        'hexagon':   '<polygon points="25,5 75,5 98,50 75,95 25,95 2,50" />',
        'badge':     '<polygon points="50,2 62,13 78,10 83,25 97,33 93,49 99,64 86,74 83,90 67,91 56,99 44,91 28,90 25,74 12,64 18,49 14,33 28,25 33,10 48,13" />'
    };

    Reg.syncMediaSubRows = function(el) {
        if (!el) return;
        var imgAct = $('#prop-image-action').val() || el.imageAction || 'none';
        if ($('#prop-image-link-wrap').length) { $('#prop-image-link-wrap').toggle(imgAct === 'url'); }

        var cAct = $('#prop-close-action').val() || el.closeAction || 'close';
        $('#prop-close-cookie-wrap').toggle(cAct === 'close_forever');

        var pMode = $('#prop-progress-mode').val() || el.progressMode || 'manual';
        $('#prop-progress-val-wrap').toggle(pMode === 'manual');

        var bAct = $('#prop-button-action').val() || el.buttonAction || 'url';
        $('#prop-button-url-wrap').toggle(bAct === 'url' || bAct === 'link');
        $('#prop-button-goto-wrap').toggle(bAct === 'goto_canvas' || bAct === 'step');

        var sAct = $('#prop-submit-action').val() || el.submitAction || 'message';
        $('#prop-submit-msg-wrap').toggle(sAct === 'message');
        $('#prop-submit-goto-wrap').toggle(sAct === 'goto_canvas');
        $('#prop-submit-redir-wrap').toggle(sAct === 'redirect');
    };

    Reg.renderMediaElementStage = function(el) {
        if (!el) return;
        var $node = (typeof Reg.getStageNode === 'function') ? Reg.getStageNode(el) : $();
        if (!$node || !$node.length) return;

        var rawType = String(el.type || 'label').toLowerCase().trim();
        var canonical = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[rawType]) ? Reg.ALIAS_MAP[rawType] : rawType;

        var $handles = $node.children('.ui-resizable-handle, .wppoppop-resize-handle').detach();
        var innerHtml = null;

        if (canonical === 'shape') {
            var preset = el.shapePreset || 'square';
            var svgInner = SVG_SHAPE_PATHS[preset] || SVG_SHAPE_PATHS['square'];
            var fillCol = el.shapeNoFill ? 'none' : (el.shapeFill || '#3b82f6');
            var stWidth = parseInt(el.strokeWidth, 10) || 0;
            var stCol   = el.strokeColor || '#0284c7';
            var rot     = parseInt(el.rotate, 10) || 0;
            $node.css({ 'transform': 'rotate(' + rot + 'deg)' });
            innerHtml = '<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="width:100%;height:100%;display:block;pointer-events:none;overflow:visible;" fill="' + fillCol + '" stroke="' + (stWidth > 0 ? stCol : 'none') + '" stroke-width="' + stWidth + '">' + svgInner + '</svg>';
        } else if (canonical === 'image') {
            var src = $.trim(el.src || el.imageUrl || el.imgUrl || '');
            var escAttr = function(v) {
                return String(v == null ? '' : v).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            };
            var alt = escAttr(el.alt || el.imageAlt || el.altText || '');
            var fit = el.objectFit || el.imageFit || 'cover';
            if (['cover', 'contain', 'fill', 'none', 'scale-down'].indexOf(fit) === -1) fit = 'cover';
            if (src) {
                innerHtml = '<img src="' + escAttr(src) + '" alt="' + alt + '" draggable="false" style="width:100%;height:100%;object-fit:' + fit + ';display:block;border-radius:inherit;pointer-events:none;">';
            } else {
                innerHtml = '<div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#1e293b;color:#94a3b8;font-size:11px;border:1px dashed #475569;border-radius:inherit;pointer-events:none;"><span class="dashicons dashicons-format-image" style="font-size:24px;width:24px;height:24px;margin-bottom:4px;"></span><span>Select Image</span></div>';
            }
        } else if (canonical === 'video') {
            // WPPOPPOP_VIDEO_STAGE_V2
            var vEsc = function(v) { return String(v == null ? '' : v).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); };
            var vTrue = function(v) { return v === true || v === 'true' || v === 1 || v === '1' || v === 'on'; };
            var vPick = function(a, b, c) { return el[a] !== undefined ? el[a] : (el[b] !== undefined ? el[b] : el[c]); };
            var vRaw = $.trim(el.videoUrl || el.content || el.url || el.src || '');
            var vAuto = vTrue(vPick('videoAutoplay', 'autoplay'));
            var vMute = vAuto || vTrue(vPick('videoMute', 'mute', 'muted'));
            var vLoop = vTrue(vPick('videoLoop', 'loop'));
            var vCtl  = (vPick('videoControls', 'controls') === undefined) ? true : vTrue(vPick('videoControls', 'controls'));
            var vRatio = el.aspectRatio || '16:9';
            var inPreview = !!(window.WpPopPopBuilderCanvas && window.WpPopPopBuilderCanvas.isPreview);
            var ytId = (vRaw.match(/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:watch\?(?:[^#]*&)?v=|embed\/|shorts\/|v\/))([\w-]{11})/) || [])[1];
            var vimId = (vRaw.match(/vimeo\.com\/(?:video\/|channels\/[^\/]+\/|groups\/[^\/]+\/videos\/)?(\d+)/) || [])[1];

            if (!vRaw) {
                innerHtml = '<div style="width:100%;height:100%;background:#0f172a;color:#94a3b8;display:flex;align-items:center;justify-content:center;font-size:11px;border-radius:inherit;pointer-events:none;">No video URL set</div>';
            } else if (inPreview) {
                var frameCss = 'width:100%;height:100%;border:0;border-radius:inherit;display:block;background:#000;';
                if (ytId) {
                    var ysrc = 'https://www.youtube.com/embed/' + ytId + '?playsinline=1&rel=0&autoplay=' + (vAuto ? 1 : 0) + '&mute=' + (vMute ? 1 : 0) + '&controls=' + (vCtl ? 1 : 0) + (vLoop ? '&loop=1&playlist=' + ytId : '');
                    innerHtml = '<iframe src="' + vEsc(ysrc) + '" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen style="' + frameCss + '"></iframe>';
                } else if (vimId) {
                    var vsrc = 'https://player.vimeo.com/video/' + vimId + '?playsinline=1&autoplay=' + (vAuto ? 1 : 0) + '&muted=' + (vMute ? 1 : 0) + '&loop=' + (vLoop ? 1 : 0) + '&controls=' + (vCtl ? 1 : 0);
                    innerHtml = '<iframe src="' + vEsc(vsrc) + '" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="' + frameCss + '"></iframe>';
                } else {
                    innerHtml = '<video src="' + vEsc(vRaw) + '" playsinline webkit-playsinline preload="metadata"' + (vCtl ? ' controls' : '') + (vAuto ? ' autoplay' : '') + (vMute ? ' muted' : '') + (vLoop ? ' loop' : '') + ' style="width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block;background:#000;"></video>';
                }
            } else {
                var thumb = ytId ? ('<img src="https://img.youtube.com/vi/' + ytId + '/hqdefault.jpg" alt="" draggable="false" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.8;">') : '';
                var provLabel = ytId ? 'YOUTUBE' : (vimId ? 'VIMEO' : 'MP4');
                var vb = [];
                if (vAuto) vb.push('Autoplay');
                if (vMute) vb.push('Muted');
                if (vLoop) vb.push('Loop');
                innerHtml = '<div style="width:100%;height:100%;position:relative;background:#0f172a;color:#f8fafc;overflow:hidden;border-radius:inherit;pointer-events:none;user-select:none;-webkit-user-select:none;">' + thumb +
                    '<div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:rgba(15,23,42,.35);text-align:center;padding:8px;box-sizing:border-box;">' +
                    '<span class="dashicons dashicons-controls-play" style="font-size:44px;width:44px;height:44px;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,.6);"></span>' +
                    '<div style="font-size:11px;font-weight:700;text-shadow:0 1px 3px rgba(0,0,0,.7);">' + provLabel + ' (' + vEsc(vRatio) + ')</div>' +
                    '<div style="font-size:10px;max-width:90%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-shadow:0 1px 3px rgba(0,0,0,.7);">' + vEsc(vRaw) + '</div>' +
                    (vb.length ? '<div style="font-size:9px;color:#38bdf8;margin-top:3px;">' + vb.join(' / ') + '</div>' : '') +
                    '</div></div>';
            }
        } else if (canonical === 'label') {
            var tag = (el.tag || el.headingTag || 'div').toLowerCase();
            if (['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span'].indexOf(tag) === -1) tag = 'div';
            var txt = (el.content !== undefined && el.content !== '') ? el.content : (el.titleText || el.paragraphText || el.text || 'Hello World');
            var tf  = el.textTransform || 'none';
            var jcMap = { center: 'center', right: 'flex-end' };
            var jc = jcMap[el.textAlign] || 'flex-start';
            var hScale = { h1: 2, h2: 1.5, h3: 1.17, h4: 1, h5: 0.83, h6: 0.67 };
            var tagFont = hScale[tag] ? ('font-family:inherit;font-style:inherit;font-size:' + hScale[tag] + 'em;font-weight:' + (el.fontWeight || 700) + ';') : 'font:inherit;';
            innerHtml = '<' + tag + ' class="wppoppop-stage-label-inner" style="margin:0!important;padding:0!important;box-sizing:border-box;display:flex;align-items:center;justify-content:' + jc + ';width:100%;height:100%;text-transform:' + tf + ';' + tagFont + 'color:inherit;line-height:inherit;letter-spacing:inherit;text-align:inherit;pointer-events:none;">' + txt + '</' + tag + '>';
        } else if (canonical === 'html') {
            var hTag = (el.tag === 'span') ? 'span' : 'div';
            var ovf  = el.overflow || 'hidden';
            var rawCode = el.content || '<div style="padding:8px;color:#64748b;font-size:12px;">Custom HTML Block</div>';
            innerHtml = '<' + hTag + ' style="width:100%;height:100%;overflow:' + ovf + ';pointer-events:none;">' + rawCode + '</' + hTag + '>';
        } else if (canonical === 'close') {
            var __g = (el.closeGlyph || el.content || '');
            var __glyphOK = ['\u00d7','\u2715','\u2716','\u2297','CLOSE'].indexOf(__g) !== -1;
            var sym = el.closeSymbol || (__g === 'CLOSE' ? 'text_close' : 'times'); /*WPPOPPOP_CLOSE_GLYPH_STAGE*/
            var cSize = parseInt(el.iconSize, 10) || 20;
            var glyphMarkup = '&times;';
            if (sym === 'text_close') {
                glyphMarkup = '<span style="font-size:' + Math.max(10, Math.round(cSize * 0.65)) + 'px;font-weight:700;letter-spacing:0.5px;">CLOSE</span>';
            } else if (sym.indexOf('dashicons-') === 0) {
                glyphMarkup = '<span class="dashicons ' + sym + '" style="font-size:' + cSize + 'px;width:' + cSize + 'px;height:' + cSize + 'px;"></span>';
            } else {
                glyphMarkup = '<span style="font-size:' + cSize + 'px;line-height:1;">' + (__glyphOK && __g !== 'CLOSE' ? $('<div>').text(__g).html() : '&times;') + '</span>';
            }
            innerHtml = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;pointer-events:none;">' + glyphMarkup + '</div>';
        } else if (canonical === 'icon') {
            var iCls = $.trim(el.iconClass || 'dashicons-star-filled');
            var iSize = parseInt(el.iconSize, 10) || 36;
            var iRot  = parseInt(el.rotate, 10) || 0;
            var fullCls = (iCls.indexOf('dashicons-') === 0 && iCls.indexOf('dashicons ') === -1) ? ('dashicons ' + iCls) : iCls;
            innerHtml = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;transform:rotate(' + iRot + 'deg);pointer-events:none;">' +
                '<i class="' + fullCls + '" style="font-size:' + iSize + 'px;width:' + iSize + 'px;height:' + iSize + 'px;line-height:1;display:inline-flex;align-items:center;justify-content:center;"></i>' +
                '</div>';
        } else if (canonical === 'progress') {
            var pct = Math.min(100, Math.max(0, parseInt(el.progressValue !== undefined ? el.progressValue : 65, 10)));
            var fCol = el.fillColor || '#c2185b';
            var tCol = el.trackColor || '#334155';
            var showPct = (el.showPercentLabel !== false);
            var lblText = $.trim(el.progressLabel || '');
            var stripeCss = el.striped ? 'background-image:linear-gradient(45deg,rgba(255,255,255,.18) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.18) 50%,rgba(255,255,255,.18) 75%,transparent 75%,transparent);background-size:20px 20px;' : '';
            var badgeText = lblText ? (lblText + (showPct ? ' (' + pct + '%)' : '')) : (showPct ? (pct + '%') : '');
            innerHtml = '<div style="width:100%;height:100%;background:' + tCol + ';border-radius:inherit;overflow:hidden;position:relative;display:flex;align-items:center;pointer-events:none;">' +
                '<div style="width:' + pct + '%;height:100%;background-color:' + fCol + ';' + stripeCss + 'transition:width 0.2s ease;"></div>' +
                (badgeText ? '<span style="position:absolute;left:0;right:0;text-align:center;font-size:11px;font-weight:700;color:#ffffff;text-shadow:0 1px 2px rgba(0,0,0,0.45);">' + $('<div>').text(badgeText).html() + '</span>' : '') +
                '</div>';
        } else if (canonical === 'button' || canonical === 'submit') {
            /* WPPOPPOP_SUBMIT_STAGE_ICONS */
            var sbIconHtml = '', sbPadStyle = ''; var sbJust = 'center'; /* WPPOPPOP_SUBMIT_ALIGN_STAGE */ if (canonical === 'submit') { sbJust = (el.textAlign === 'left') ? 'flex-start' : ((el.textAlign === 'right') ? 'flex-end' : 'center'); }
            if (canonical === 'submit') {
                var sbHex = function(c) { return /^#[0-9a-fA-F]{3,8}$/.test(c || ''); };
                var sbOne = function(cls, side, off, colorKey) {
                    cls = $.trim(cls || '');
                    if (!cls) return '';
                    var col = sbHex(el[colorKey]) ? el[colorKey] : (sbHex(el.fgColor) ? el.fgColor : 'currentColor');
                    var pos = 'position:absolute;' + side + ':' + off + 'px;top:50%;transform:translateY(-50%);color:' + col + ';line-height:1;pointer-events:none;font-size:14px;width:14px;height:14px;';
                    if (!/^[A-Za-z][A-Za-z0-9 _-]*$/.test(cls)) { return '<span aria-hidden="true" style="' + pos + '">' + $('<div>').text(cls).html() + '</span>'; }
                    var full = cls;
                    if (cls.indexOf('dashicons-') === 0 && cls.indexOf('dashicons ') === -1) { full = 'dashicons ' + cls; }
                    else if (cls.indexOf('fa-') === 0) { full = 'fas ' + cls; }
                    return '<i class="' + full + '" aria-hidden="true" style="' + pos + '"></i>';
                };
                var sbOffL = isNaN(parseInt(el.iconLeftOffset, 10)) ? 10 : parseInt(el.iconLeftOffset, 10);
                var sbOffR = isNaN(parseInt(el.iconRightOffset, 10)) ? 10 : parseInt(el.iconRightOffset, 10);
                var sbL = $.trim(el.iconLeft || ''), sbR = $.trim(el.iconRight || '');
                sbIconHtml = sbOne(sbL, 'left', sbOffL, 'iconLeftColor') + sbOne(sbR, 'right', sbOffR, 'iconRightColor');
                sbPadStyle = 'padding:0 ' + (sbR ? sbOffR + 22 : 12) + 'px 0 ' + (sbL ? sbOffL + 22 : 12) + 'px;';
            }
            var bTxt = el.content !== undefined ? el.content : (canonical === 'submit' ? 'Submit Form' : 'Click Here');
            var bIco = $.trim(el.buttonIcon || '');
            var icoHtml = '';
            if (bIco) {
                var icoFull = (bIco.indexOf('dashicons-') === 0 && bIco.indexOf('dashicons ') === -1) ? ('dashicons ' + bIco) : bIco;
                icoHtml = '<i class="' + icoFull + '" style="margin-right:6px;font-size:14px;"></i>';
            }
            $node.css({
                '--wppoppop-hover-bg': el.hoverBg || '#1d4ed8',
                '--wppoppop-hover-color': el.hoverColor || '#ffffff',
                '--wppoppop-hover-border': el.hoverBorder || '#1d4ed8'
            });
            innerHtml = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:' + sbJust + ';position:relative;box-sizing:border-box;' + sbPadStyle + 'pointer-events:none;' + tagFont + 'color:inherit;">' +
                icoHtml + sbIconHtml + '<span>' + $('<div>').text(bTxt).html() + '</span>' +
                '</div>';
        }

        if (innerHtml !== null) {
            $node.html(innerHtml).append($handles);
        }
        if (typeof Reg.applyStyleToStageNode === 'function') {
            Reg.applyStyleToStageNode(el);
        }
    };

    function bindPhase2MediaVerification() {
        var prevSync = Reg.syncInspectorForElement;
        if (typeof prevSync === 'function' && !Reg._phase2MediaSyncHooked) {
            Reg._phase2MediaSyncHooked = true;
            Reg.syncInspectorForElement = function(idOrEl) {
                prevSync.apply(this, arguments);
                var el = Reg.resolveElement(idOrEl);
                if (el) {
                    if (el.type === 'image' || el.type === 'img') {
                        $('#prop-image-url').val(el.src || el.imageUrl || el.imgUrl || '');
                        $('#prop-image-alt').val(el.alt || el.imageAlt || el.altText || '');
                        $('#prop-image-fit').val(el.objectFit || el.imageFit || 'cover');
                        $('#prop-image-link').val(el.linkUrl || '');
                    }
                    Reg.syncMediaSubRows(el);
                }
            };
        }

        $(document).off('input.p2MediaBind change.p2MediaBind', '[data-elem-family="media"] .wppoppop-gp-bind')
                   .on('input.p2MediaBind change.p2MediaBind', '[data-elem-family="media"] .wppoppop-gp-bind', function() {
            var el = Reg.resolveElement();
            if (!el) return;
            var $inp = $(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            var val = $inp.is(':checkbox') ? $inp.is(':checked') : $inp.val();
            el[prop] = val;

            if (prop === 'content') {
                el.label = val;
                el.titleText = val;
                el.paragraphText = val;
                el.text = val;
            } else if (prop === 'src') {
                val = $.trim(val);
                el.src = val;
                el.imageUrl = val;
                el.imgUrl = val;
                if (el.type === 'image' || el.type === 'img') { el.content = val; }
            } else if (prop === 'videoUrl') {
                val = $.trim(val);
                el.videoUrl = val;
                el.content = val;
                el.url = val;
            } else if (prop === 'videoAutoplay' || prop === 'videoMute' || prop === 'videoLoop' || prop === 'videoControls') {
                var vAlias = { videoAutoplay: 'autoplay', videoMute: 'mute', videoLoop: 'loop', videoControls: 'controls' }[prop];
                el[vAlias] = val;
                if (prop === 'videoAutoplay' && val) { el.videoMute = true; el.mute = true; $('#prop-video-mute').prop('checked', true); }
            } else if (prop === 'objectFit') {
                el.imageFit = val;
            } else if (prop === 'alt') {
                el.imageAlt = val;
                el.altText = val;
            } else if (prop === 'tag') {
                el.headingTag = val;
                el.htmlTag = val;
            } else if (prop === 'gotoCanvas') {
                el.goto_canvas = parseInt(val, 10) || 2;
            }

            Reg.syncMediaSubRows(el);
            Reg.renderMediaElementStage(el);
        });

        $(document).off('click.p2IconPreset', '.wppoppop-icon-preset')
                   .on('click.p2IconPreset', '.wppoppop-icon-preset', function(e) {
            e.preventDefault();
            var iconCls = $(this).attr('data-icon') || '';
            if (iconCls) {
                $('#prop-icon-class').val(iconCls).trigger('change');
            }
        });

        var MEDIA_BTN_SEL = '#wppoppop-btn-media-upload, #prop-image-media-btn, .wppoppop-media-lib-trigger';
        $(document).off('click.p2MediaLib', MEDIA_BTN_SEL)
                   .on('click.p2MediaLib', MEDIA_BTN_SEL, function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!window.wp || !window.wp.media) {
                var manual = window.prompt('WordPress Media is unavailable. Enter image URL:', $('#prop-image-url').val() || '');
                if (manual) { $('#prop-image-url').val($.trim(manual)).trigger('change'); }
                return;
            }
            if (!Reg._wpMediaFrame) {
                Reg._wpMediaFrame = window.wp.media({
                    title: 'Select or Upload Image',
                    button: { text: 'Use Image' },
                    multiple: false,
                    library: { type: 'image' }
                });
                Reg._wpMediaFrame.on('select', function() {
                    var att = Reg._wpMediaFrame.state().get('selection').first();
                    att = att ? att.toJSON() : null;
                    if (!att || !att.url) return;
                    $('#prop-image-url').val(att.url).trigger('change');
                    if (att.alt && !$.trim($('#prop-image-alt').val())) {
                        $('#prop-image-alt').val(att.alt).trigger('change');
                    }
                    if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                        window.WpPopPopBuilderCore.pushHistory();
                    }
                });
            }
            Reg._wpMediaFrame.open();
        });

        $(document).off('blur.p2MediaHist change.p2MediaHist', '#prop-image-url, #prop-image-alt, #prop-image-fit, #prop-image-link')
                   .on('change.p2MediaHist', '#prop-image-url, #prop-image-alt, #prop-image-fit, #prop-image-link', function() {
            if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                window.WpPopPopBuilderCore.pushHistory();
            }
        });
    }

    $(function() {
        bindPhase2MediaVerification();
        setTimeout(bindPhase2MediaVerification, 120);
    });
    bindPhase2MediaVerification();
})(window, jQuery);
/* === WPPOPPOP_PHASE2_MEDIA_FIELD_VERIFICATION_END === */
