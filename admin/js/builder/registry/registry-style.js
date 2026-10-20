/**
 * WpPopPop Builder Registry Module: registry-style
 * Split from builder-elements-registry.js (load order matters; see class-admin-assets.php)
 */
/* === WPPOPPOP_PHASE1_CORE_TAB_VERIFICATION_START === */
(function(window, $) {
    'use strict';

    window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var Reg = window.WpPopPopElementsRegistry;

    Reg.getStageNode = function(el) {
        if (!el || !el.id) return $();
        var cleanId = String(el.id).replace(/^wppoppop-el-/, '').replace(/^el-/, '');
        return $('.wppoppop-canvas-item').filter(function() {
            var dId = $(this).attr('data-id') || $(this).data('id') || $(this).attr('id') || '';
            dId = String(dId).replace(/^wppoppop-el-/, '').replace(/^el-/, '');
            return dId === cleanId;
        });
    };

    Reg.applyStyleToStageNode = function(el) {
        if (!el) return;
        var $node = Reg.getStageNode(el);
        if (!$node.length) return;

        // 1. Geometry (X, Y, W, H)
        if (el.x !== undefined) $node.css('left', parseInt(el.x, 10) + 'px');
        if (el.y !== undefined) $node.css('top', parseInt(el.y, 10) + 'px');
        if (el.width !== undefined) $node.css('width', parseInt(el.width, 10) + 'px');
        if (el.height !== undefined) $node.css('height', parseInt(el.height, 10) + 'px');

        // 2. Opacity (normalized 0..1 for CSS)
        var op = el.opacity !== undefined ? parseFloat(el.opacity) : 100;
        var cssOp = (op > 1) ? (op / 100) : op;
        $node.css('opacity', cssOp);

        // 3. Typography & Colors
        if (el.fontFamily) $node.css('font-family', el.fontFamily);
        if (el.fontSize) $node.css('font-size', parseInt(el.fontSize, 10) + 'px');
        if (el.fontWeight) $node.css('font-weight', el.fontWeight);
        if (el.lineHeight) $node.css('line-height', el.lineHeight);
        if (el.letterSpacing !== undefined) $node.css('letter-spacing', parseFloat(el.letterSpacing) + 'px');
        if (el.textAlign) $node.css('text-align', el.textAlign);
        if (el.color) $node.css('color', el.color);
        /* WPPOPPOP_FG_COLOR: foreground colour for icons & accents */
        if (/^#[0-9a-fA-F]{3,8}$/.test(el.fgColor || '')) { $node[0].style.setProperty('--wppoppop-fg', el.fgColor); $node.attr('data-wpp-fg', '1'); }
        else { $node[0].style.removeProperty('--wppoppop-fg'); $node.removeAttr('data-wpp-fg'); }
        if (el.bgColor && !el.transparentFill) {
            $node.css('background-color', el.bgColor);
        } else if (el.transparentFill) {
            $node.css('background-color', 'transparent');
        }

        // 4. 4-Way Padding & Borders
        var pk = function(a, b) { return (el[a] !== undefined && el[a] !== '' && el[a] !== null) ? el[a] : el[b]; };
        var pt = parseInt(pk('paddingTop', 'padTop'), 10) || 0;
        var pr = parseInt(pk('paddingRight', 'padRight'), 10) || 0;
        var pb = parseInt(pk('paddingBottom', 'padBottom'), 10) || 0;
        var pl = parseInt(pk('paddingLeft', 'padLeft'), 10) || 0;
        var padSet = ['paddingTop','paddingRight','paddingBottom','paddingLeft','padTop','padRight','padBottom','padLeft'].some(function(k) { return el[k] !== undefined && el[k] !== ''; });
        if (padSet) {
            $node.css('padding', pt + 'px ' + pr + 'px ' + pb + 'px ' + pl + 'px');
        }
        var bOff = (el.borderEnabled === false || el.borderEnabled === 'false' || el.borderEnabled === 0);
        var bw = bOff ? 0 : (parseInt(el.borderWidth, 10) || 0);
        var bs = bOff ? 'none' : (el.borderStyle || (bw > 0 ? 'solid' : 'none'));
        var bc = el.borderColor || '#cbd5e1';
        $node.css({
            'border-width': bw + 'px',
            'border-style': bs,
            'border-color': bc,
            'border-radius': (bOff ? 0 : (parseInt(el.borderRadius, 10) || 0)) + 'px'
        });

        // 5. 5-Parameter Box Shadow
        var sx = parseInt(el.shadowX, 10) || 0;
        var sy = parseInt(el.shadowY, 10) || 0;
        var sBlur = parseInt(el.shadowBlur, 10) || 0;
        var sSpread = parseInt(el.shadowSpread, 10) || 0;
        var sCol = el.shadowColor || 'rgba(0,0,0,0.15)';
        var sOff = (el.shadowEnabled === false || el.shadowEnabled === 'false' || el.shadowEnabled === 0);
        if (!sOff && (sx || sy || sBlur || sSpread)) {
            $node.css('box-shadow', sx + 'px ' + sy + 'px ' + sBlur + 'px ' + sSpread + 'px ' + sCol);
        } else {
            $node.css('box-shadow', 'none');
        }

        // 6. Inline CSS Styles Repeater
        if (Array.isArray(el.inlineStyles)) {
            el.inlineStyles.forEach(function(pair) {
                if (pair && pair.prop && pair.value) {
                    $node.css(pair.prop, pair.value);
                }
            });
        }
    };

    Reg.hydrateCoreAndStyleFields = function(el) {
        if (!el) return;
        var rawType = String(el.type || 'label').toLowerCase().trim();
        var canonical = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[rawType]) ? Reg.ALIAS_MAP[rawType] : rawType;
        var isForm = Reg.FORM_FAMILY && Reg.FORM_FAMILY.indexOf(canonical) !== -1;

        // Hydrate Layer Name & Geometry (X, Y, W, H)
        $('#prop-layer-name, #prop-name').val(el.name || canonical.toUpperCase());
        $('#prop-x, #prop-pos-left').val(parseInt(el.x !== undefined ? el.x : el.left, 10) || 0);
        $('#prop-y, #prop-pos-top').val(parseInt(el.y !== undefined ? el.y : el.top, 10) || 0);
        $('#prop-w, #prop-size-width').val(parseInt(el.width !== undefined ? el.width : el.w, 10) || 160);
        $('#prop-h, #prop-size-height').val(parseInt(el.height !== undefined ? el.height : el.h, 10) || 44);

        // Toggle Media Style Suite vs Form Style Suite
        $('#insp-section-media-style-suite').toggle(!isForm);
        $('#insp-section-form-input-style').show(); // Custom CSS Class & CSS Styles repeater available for all elements
        $('#gp-form-global-style-notice, #gp-form-text-align-row').toggle(isForm);
        var hasSideIcons = isForm && ['text', 'email', 'number', 'tel', 'date', 'time', 'password', 'upload', 'file'].indexOf(canonical) !== -1;
        $('#gp-form-input-icons-row').toggle(hasSideIcons);
        $('#wppoppop-form-icons-card').toggle(canonical !== 'textarea' && canonical !== 'select'); /* WPPOPPOP_TEXTAREA_STYLE */
        /* WPPOPPOP_TF_STYLE: Text Field hides Opacity, Background, Typography, Custom CSS */
        var tfHide = '#insp-section-opacity, #insp-section-bg-color, #insp-section-typography, #insp-section-padding, #insp-section-border, #insp-section-shadow, #insp-section-custom-css, ' +
            '#prop-form-custom-class, #prop-gp-style-css-class, #wppoppop-btn-add-inline-style, #wppoppop-gp-inline-styles-list';
        var $tfNodes = $(tfHide).map(function() {
            var $c = $(this).closest('.wppoppop-insp-card, .wppoppop-insp-section');
            return ($c.length && !$c.is('#wppoppop-form-icons-card')) ? $c[0] : this;
        });
        if (canonical === 'text' || canonical === 'textfield' || canonical === 'email' || canonical === 'number' || canonical === 'tel' || canonical === 'textarea' || canonical === 'select') {
            $tfNodes.attr('data-tf-hidden', '1').hide();
        } else {
            $tfNodes.filter('[data-tf-hidden]').removeAttr('data-tf-hidden').show();
        }

        if (!isForm) {
            var textMedia = ['label', 'html', 'button', 'submit', 'progress'];
            var hoverMedia = ['button', 'submit'];
            $('#insp-section-typography').toggle(textMedia.indexOf(canonical) !== -1);
            $('#wppoppop-hover-state-card').toggle(hoverMedia.indexOf(canonical) !== -1);
        }

        /* WPPOPPOP_DATE_STYLE: keep Date's hidden sections hidden through this secondary hydration pass */
        $('#insp-tab-style').toggleClass('wppoppop-date-style', canonical === 'date' || canonical === 'time' || rawType === 'time'); /* WPPOPPOP_TIME_STYLE */

        // Hydrate Media Style fields
        var opVal = el.opacity !== undefined ? parseFloat(el.opacity) : 100;
        if (opVal > 0 && opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-font-family').val(el.fontFamily || 'inherit');
        $('#prop-font-size').val(parseInt(el.fontSize, 10) || 16);
        $('#prop-font-weight').val(el.fontWeight || '400');
        $('#prop-line-height').val(el.lineHeight !== undefined ? el.lineHeight : 1.4);
        $('#prop-letter-spacing').val(el.letterSpacing !== undefined ? el.letterSpacing : 0);
        $('#prop-text-align').val(el.textAlign || 'left');
        $('#prop-color').val(el.color || '#1e293b');
        $('#prop-bg-color').val(el.bgColor || '#ffffff');
        $('#prop-pad-top').val(parseInt(el.padTop, 10) || 0);
        $('#prop-pad-right').val(parseInt(el.padRight, 10) || 0);
        $('#prop-pad-bottom').val(parseInt(el.padBottom, 10) || 0);
        $('#prop-pad-left').val(parseInt(el.padLeft, 10) || 0);
        $('#prop-border-width').val(parseInt(el.borderWidth, 10) || 0);
        $('#prop-border-style').val(el.borderStyle || 'none');
        $('#prop-border-radius').val(parseInt(el.borderRadius, 10) || 0);
        $('#prop-border-color').val(el.borderColor || '#cbd5e1');
        $('#prop-shadow-x').val(parseInt(el.shadowX, 10) || 0);
        $('#prop-shadow-y').val(parseInt(el.shadowY, 10) || 0);
        $('#prop-shadow-blur').val(parseInt(el.shadowBlur, 10) || 0);
        $('#prop-shadow-spread').val(parseInt(el.shadowSpread, 10) || 0);
        $('#prop-shadow-color').val(el.shadowColor || 'rgba(0,0,0,0.15)');
        $('#prop-hover-bg').val(el.hoverBg || '#1d4ed8');
        $('#prop-hover-color').val(el.hoverColor || '#ffffff');
        $('#prop-hover-border').val(el.hoverBorder || '#1d4ed8');

        // Hydrate Data Tab dynamic param row visibility
        $('#insp-row-data-param').toggle(!!el.dynamicDefault);
    };

    function bindPhase1VerifiedEvents() {
        // Hook hydrateCoreAndStyleFields into syncInspectorForElement
        var origSync = Reg.syncInspectorForElement;
        if (typeof origSync === 'function' && !Reg._phase1SyncHooked) {
            Reg._phase1SyncHooked = true;
            Reg.syncInspectorForElement = function(idOrEl) {
                origSync.apply(this, arguments);
                var el = Reg.resolveElement(idOrEl);
                if (el) Reg.hydrateCoreAndStyleFields(el);
            };
        }

        // 1. Layer Name two-way sync with Floating Layers Panel
        $(document).off('input.p1LayerName change.p1LayerName', '#prop-layer-name, #prop-name')
                   .on('input.p1LayerName change.p1LayerName', '#prop-layer-name, #prop-name', function() {
            var el = Reg.resolveElement();
            if (!el) return;
            el.name = $(this).val();
            var cleanId = String(el.id).replace(/^wppoppop-el-/, '').replace(/^el-/, '');
            $('#layer-item-' + cleanId + ' .wppoppop-layer-title, .wppoppop-layer-item[data-id="' + cleanId + '"] .wppoppop-layer-title').text(el.name);
        });

        // 2. Coordinates & Bounds (X, Y, W, H) two-way sync with Stage Node
        $(document).off('input.p1Bounds change.p1Bounds', '#prop-x, #prop-pos-left, #prop-y, #prop-pos-top, #prop-w, #prop-size-width, #prop-h, #prop-size-height')
                   .on('input.p1Bounds change.p1Bounds', '#prop-x, #prop-pos-left, #prop-y, #prop-pos-top, #prop-w, #prop-size-width, #prop-h, #prop-size-height', function() {
            var el = Reg.resolveElement();
            if (!el) return;
            var id = this.id;
            var v = parseInt($(this).val(), 10) || 0;
            if (id === 'prop-x' || id === 'prop-pos-left') { el.x = v; el.left = v; }
            if (id === 'prop-y' || id === 'prop-pos-top') { el.y = v; el.top = v; }
            if (id === 'prop-w' || id === 'prop-size-width') { el.width = Math.max(16, v); el.w = el.width; }
            if (id === 'prop-h' || id === 'prop-size-height') { el.height = Math.max(16, v); el.h = el.height; }
            Reg.applyStyleToStageNode(el);
        });

        // 3. Animation Play Button on Content Tab
        $(document).off('click.p1PlayAnim', '#wppoppop-btn-play-elem-anim')
                   .on('click.p1PlayAnim', '#wppoppop-btn-play-elem-anim', function(e) {
            return; /* handled by WpPopPopBuilderAnimation */
            e.preventDefault();
            var el = Reg.resolveElement();
            if (!el) return;
            var anim = $('#prop-anim-in').val() || el.animIn || 'fadeIn';
            var dur  = parseInt($('#prop-anim-duration').val(), 10) || 1000;
            var $node = Reg.getStageNode(el);
            if ($node.length) {
                $node.css({ 'animation': 'none', '-webkit-animation': 'none' });
                void $node[0].offsetWidth;
                $node.css({
                    'animation': anim + ' ' + dur + 'ms both',
                    '-webkit-animation': anim + ' ' + dur + 'ms both'
                });
            }
        });

        // 4. Media Style Suite two-way binding
        var styleSelectors = [
            '#prop-opacity', '#prop-font-family', '#prop-font-size', '#prop-font-weight',
            '#prop-line-height', '#prop-letter-spacing', '#prop-text-align',
            '#prop-color', '#prop-bg-color', '#prop-pad-top', '#prop-pad-right',
            '#prop-pad-bottom', '#prop-pad-left', '#prop-border-width', '#prop-border-style',
            '#prop-border-radius', '#prop-border-color', '#prop-shadow-x', '#prop-shadow-y',
            '#prop-shadow-blur', '#prop-shadow-spread', '#prop-shadow-color',
            '#prop-hover-bg', '#prop-hover-color', '#prop-hover-border'
        ].join(', ');

        $(document).off('input.p1MediaStyle change.p1MediaStyle', styleSelectors)
                   .on('input.p1MediaStyle change.p1MediaStyle', styleSelectors, function() {
            var el = Reg.resolveElement();
            if (!el) return;
            var tid = this.id;
            var MAP = {
                'prop-font-family': ['fontFamily', 'str'], 'prop-font-size': ['fontSize', 'int'],
                'prop-font-weight': ['fontWeight', 'str'], 'prop-line-height': ['lineHeight', 'float'],
                'prop-letter-spacing': ['letterSpacing', 'float'], 'prop-text-align': ['textAlign', 'str'],
                'prop-color': ['color', 'str'],
                'prop-border-width': ['borderWidth', 'int'], 'prop-border-style': ['borderStyle', 'str'],
                'prop-border-color': ['borderColor', 'str'], 'prop-border-radius': ['borderRadius', 'int'],
                'prop-shadow-x': ['shadowX', 'int'], 'prop-shadow-y': ['shadowY', 'int'],
                'prop-shadow-blur': ['shadowBlur', 'int'], 'prop-shadow-spread': ['shadowSpread', 'int'],
                'prop-shadow-color': ['shadowColor', 'str'],
                'prop-hover-bg': ['hoverBg', 'str'], 'prop-hover-color': ['hoverColor', 'str'], 'prop-hover-border': ['hoverBorder', 'str']
            };
            if (tid === 'prop-opacity') {
                var op = parseInt($('#prop-opacity').val(), 10);
                if (!isNaN(op)) { el.opacity = op; $('#prop-opacity-val').text(op + '%'); }
            } else if (tid === 'prop-bg-color') {
                el.bgColor = $('#prop-bg-color').val();
                el.transparentFill = false;
                $('#prop-bg-transparent').prop('checked', false);
            } else if (tid === 'prop-bg-transparent') {
                el.transparentFill = $('#prop-bg-transparent').is(':checked');
                if (!el.transparentFill && (!el.bgColor || el.bgColor === 'transparent')) { el.bgColor = $('#prop-bg-color').val(); }
            } else if (/^prop-pad-(top|right|bottom|left)$/.test(tid)) {
                var side = tid.replace('prop-pad-', '');
                var Side = side.charAt(0).toUpperCase() + side.slice(1);
                var pv = Math.max(0, parseInt($(this).val(), 10) || 0);
                el['padding' + Side] = pv; el['pad' + Side] = pv; el['padding_' + side] = pv;
            } else if (MAP[tid]) {
                var spec = MAP[tid], raw = $(this).val(), out = raw;
                if (spec[1] === 'int') { out = parseInt(raw, 10); if (isNaN(out)) out = 0; }
                else if (spec[1] === 'float') { out = parseFloat(raw); if (isNaN(out)) out = 0; }
                el[spec[0]] = out;
            }

            Reg.applyStyleToStageNode(el);
        });

        // 5. Data Tab Dynamic URL Parameter Toggle
        $(document).off('change.p1DataDyn', '#prop-data-dynamic')
                   .on('change.p1DataDyn', '#prop-data-dynamic', function() {
            var isDyn = $(this).is(':checked');
            $('#insp-row-data-param').toggle(isDyn);
        });

        // 6. Inline CSS Styles & Logic Rule Repeater live input sync
        $(document).off('input.p1Repeaters change.p1Repeaters', '#wppoppop-gp-inline-styles-list input, #wppoppop-gp-logic-rules-list input, #wppoppop-gp-logic-rules-list select')
                   .on('input.p1Repeaters change.p1Repeaters', '#wppoppop-gp-inline-styles-list input, #wppoppop-gp-logic-rules-list input, #wppoppop-gp-logic-rules-list select', function() {
            var el = Reg.resolveElement();
            if (!el) return;
            var stylesArr = [];
            $('#wppoppop-gp-inline-styles-list .wppoppop-gp-repeater-item').each(function() {
                var p = $.trim($(this).find('.gp-css-prop').val() || '');
                var v = $.trim($(this).find('.gp-css-val').val() || '');
                if (p || v) stylesArr.push({ prop: p, value: v });
            });
            el.inlineStyles = stylesArr;

            var rulesArr = [];
            $('#wppoppop-gp-logic-rules-list .wppoppop-gp-repeater-item').each(function() {
                var f  = $.trim($(this).find('.gp-rule-field').val() || '');
                var op = $(this).find('.gp-rule-op').val() || 'is';
                var v  = $.trim($(this).find('.gp-rule-val').val() || '');
                rulesArr.push({ field: f, operator: op, value: v });
            });
            el.logicRules = rulesArr;
            Reg.applyStyleToStageNode(el);
        });
    }

    $(function() {
        bindPhase1VerifiedEvents();
        setTimeout(bindPhase1VerifiedEvents, 120);
    });
    bindPhase1VerifiedEvents();
})(window, jQuery);
/* === WPPOPPOP_PHASE1_CORE_TAB_VERIFICATION_END === */

/* === WPPOPPOP_FG_COLOR_BIND_START === */
(function(window, $) {
    'use strict';
    var Reg = window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    if (!document.getElementById('wppoppop-fg-color-css')) {
        var st = document.createElement('style');
        st.id = 'wppoppop-fg-color-css';
        st.appendChild(document.createTextNode(
            '.wppoppop-canvas-item[data-wpp-fg] .dashicons:not([style*="color"]),' +
            '.wppoppop-canvas-item[data-wpp-fg] i:not([style*="color"]){color:var(--wppoppop-fg);}' +
            '.wppoppop-canvas-item[data-wpp-fg] input[type="checkbox"],.wppoppop-canvas-item[data-wpp-fg] input[type="radio"]{accent-color:var(--wppoppop-fg);}'
        ));
        (document.head || document.documentElement).appendChild(st);
    }
    var ok = function(c) { return /^#[0-9a-fA-F]{3,8}$/.test(c || ''); };
    function rerender(el) {
        var raw = String(el.type || '').toLowerCase();
        var can = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[raw]) ? Reg.ALIAS_MAP[raw] : raw;
        var isForm = Reg.FORM_FAMILY && Reg.FORM_FAMILY.indexOf(can) !== -1;
        if (isForm && typeof Reg.renderFormElementStage === 'function') { Reg.renderFormElementStage(el); }
        else if (typeof Reg.renderMediaElementStage === 'function') { Reg.renderMediaElementStage(el); }
        else if (typeof Reg.applyStyleToStageNode === 'function') { Reg.applyStyleToStageNode(el); }
    }
    $(document).off('input.wppFg change.wppFg', '#prop-fg-color').on('input.wppFg change.wppFg', '#prop-fg-color', function(e) {
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return;
        el.fgColor = $(this).val();
        rerender(el);
        if (e.type === 'change' && window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') { window.WpPopPopBuilderCore.pushHistory(); }
    });
    $(document).off('click.wppFgReset', '#prop-fg-color-reset').on('click.wppFgReset', '#prop-fg-color-reset', function(e) {
        e.preventDefault();
        var el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (!el) return;
        el.fgColor = '';
        $('#prop-fg-color').val('#64748b');
        rerender(el);
        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') { window.WpPopPopBuilderCore.pushHistory(); }
    });
    function hydrate(el) {
        if (!el) el = (typeof Reg.resolveElement === 'function') ? Reg.resolveElement() : null;
        if (el) { $('#prop-fg-color').val(ok(el.fgColor) ? el.fgColor : '#64748b'); }
    }
    if (typeof Reg.hydrateCoreAndStyleFields === 'function' && !Reg._fgHooked) {
        Reg._fgHooked = true;
        var orig = Reg.hydrateCoreAndStyleFields;
        Reg.hydrateCoreAndStyleFields = function(el) { var r = orig.apply(this, arguments); hydrate(el); return r; };
    }
    $(document).off('click.wppFgHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]')
        .on('click.wppFgHydrate', '.wppoppop-canvas-item, .wppoppop-layer-item, #wppoppop-inspector-drawer [data-tab]', function() {
            [60, 250, 600].forEach(function(ms) { setTimeout(function() { hydrate(); }, ms); });
        });
})(window, jQuery);
/* === WPPOPPOP_FG_COLOR_BIND_END === */
