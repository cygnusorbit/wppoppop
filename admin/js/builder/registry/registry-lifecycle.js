/**
 * WpPopPop Builder Registry Module: registry-lifecycle
 * Split from builder-elements-registry.js (load order matters; see class-admin-assets.php)
 */
/* === WPPOPPOP_PHASE4_AUTONOMOUS_LIFECYCLE_START === */
(function(window, $) {
    'use strict';

    window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var Reg = window.WpPopPopElementsRegistry;

    Reg.flushElementFromDOM = function(targetEl) {
        var el = targetEl || (typeof Reg.resolveElement === 'function' ? Reg.resolveElement() : null);
        if (!el) return null;

        var rawType = String(el.type || 'label').toLowerCase().trim();
        var canonical = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[rawType]) ? Reg.ALIAS_MAP[rawType] : rawType;
        var isForm = Reg.FORM_FAMILY && Reg.FORM_FAMILY.indexOf(canonical) !== -1;

        // 1. Flush Layer Name & Geometry (X, Y, W, H)
        var $nameInp =$('#prop-layer-name, #prop-name').first();
        if ($nameInp.length &&$nameInp.val()) {
            el.name = $nameInp.val();
        }
        var $xInp =$('#prop-x, #prop-pos-left').first();
        var $yInp =$('#prop-y, #prop-pos-top').first();
        var $wInp =$('#prop-w, #prop-size-width').first();
        var $hInp =$('#prop-h, #prop-size-height').first();
        if ($xInp.length && $xInp.val() !== '') { el.x = parseInt($xInp.val(), 10) || 0; el.left = el.x; }
        if ($yInp.length && $yInp.val() !== '') { el.y = parseInt($yInp.val(), 10) || 0; el.top = el.y; }
        if ($wInp.length && $wInp.val() !== '') { el.width = Math.max(16, parseInt($wInp.val(), 10) || 160); el.w = el.width; }
        if ($hInp.length && $hInp.val() !== '') { el.height = Math.max(16, parseInt($hInp.val(), 10) || 44); el.h = el.height; }

        // 2. Flush Active Element-Specific Panel (.wppoppop-gp-bind)
        var panelSel = (Reg.PANEL_MAP && Reg.PANEL_MAP[canonical]) ? Reg.PANEL_MAP[canonical] : ('#panel-elem-' + canonical);
        $(panelSel).find('.wppoppop-gp-bind').each(function() {
            var $inp =$(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            el[prop] = $inp.is(':checkbox') ? $inp.is(':checked') :$inp.val();
        });

        // 3. Flush Shared Content Footer (Autocomplete, Animation, Required)
        if ($('#prop-gp-autocomplete').length && $('#insp-row-gp-autocomplete').is(':visible')) { /* WPPOPPOP_AC_GUARD */
            el.autocomplete = $('#prop-gp-autocomplete').val() || 'off';
        }
        if ($('#prop-anim-in').length) {
            el.animIn       = $('#prop-anim-in').val() || 'none';
            el.animation    = el.animIn;
            el.animOut      = $('#prop-anim-out').val() || 'none';
            el.animDuration = parseInt($('#prop-anim-duration').val(), 10) || 1000;
            el.animDelay    = parseInt($('#prop-anim-delay').val(), 10) || 0;
            if (window.WpPopPopBuilderAnimation) { window.WpPopPopBuilderAnimation.syncAliases(el); }
        }
        if (isForm && $('#prop-gp-required').length) {
            el.required = $('#prop-gp-required').is(':checked');
            if ($('#prop-gp-required-msg').length) { el.requiredMessage = $('#prop-gp-required-msg').val() || ''; } /* WPPOPPOP_REQ_MSG: flush */
        }

        // 4. Flush Style Tab (Media Suite + Form Input Style Suite + Inline CSS Repeater)
        if (!isForm && $('#insp-section-media-style-suite').length) {
            var op = parseInt($('#prop-opacity').val(), 10);
            if (!isNaN(op)) el.opacity = op;
            el.fontFamily    = $('#prop-font-family').val() || 'inherit';
            el.fontSize      = parseInt($('#prop-font-size').val(), 10) || 16;
            el.fontWeight    = $('#prop-font-weight').val() || '400';
            el.lineHeight    = parseFloat($('#prop-line-height').val()) || 1.4;
            el.letterSpacing = parseFloat($('#prop-letter-spacing').val()) || 0;
            el.textAlign     = $('#prop-text-align').val() || 'left';
            el.color         = $('#prop-color').val() || '#1e293b';
            if (!(canonical === 'shape' && !el.bgColor)) { el.bgColor       = $('#prop-bg-color').val() || '#ffffff'; }
            el.padTop        = parseInt($('#prop-pad-top').val(), 10) || 0;
            el.padRight      = parseInt($('#prop-pad-right').val(), 10) || 0;
            el.padBottom     = parseInt($('#prop-pad-bottom').val(), 10) || 0;
            el.padLeft       = parseInt($('#prop-pad-left').val(), 10) || 0;
            el.borderWidth   = parseInt($('#prop-border-width').val(), 10) || 0;
            el.borderStyle   = $('#prop-border-style').val() || 'none';
            el.borderRadius  = parseInt($('#prop-border-radius').val(), 10) || 0;
            el.borderColor   = $('#prop-border-color').val() || '#cbd5e1';
            el.shadowX       = parseInt($('#prop-shadow-x').val(), 10) || 0;
            el.shadowY       = parseInt($('#prop-shadow-y').val(), 10) || 0;
            el.shadowBlur    = parseInt($('#prop-shadow-blur').val(), 10) || 0;
            el.shadowSpread  = parseInt($('#prop-shadow-spread').val(), 10) || 0;
            el.shadowColor   = $('#prop-shadow-color').val() || 'rgba(0,0,0,0.15)';
            el.hoverBg       = $('#prop-hover-bg').val() || '#1d4ed8';
            el.hoverColor    = $('#prop-hover-color').val() || '#ffffff';
            el.hoverBorder   = $('#prop-hover-border').val() || '#1d4ed8';
        }

        if (isForm) {
            var $activeAlign =$('#gp-form-text-align-group .wppoppop-align-seg-btn.active');
            if ($activeAlign.length) {
                el.textAlign = $activeAlign.attr('data-align') || 'left';
            }
            el.iconLeft        = $('#prop-form-icon-left').val() || '';
            el.iconLeftOffset  = (isNaN(parseInt($('#prop-form-icon-left-offset').val(), 10)) ? 10 : parseInt($('#prop-form-icon-left-offset').val(), 10));
            el.iconRight       = $('#prop-form-icon-right').val() || '';
            el.iconRightOffset = (isNaN(parseInt($('#prop-form-icon-right-offset').val(), 10)) ? 10 : parseInt($('#prop-form-icon-right-offset').val(), 10));
            if ($('#prop-form-icon-left-color').length)  { el.iconLeftColor  = $('#prop-form-icon-left-color').val()  || '#64748b'; } /* WPPOPPOP_ICON_COLOR */
            if ($('#prop-form-icon-right-color').length) { el.iconRightColor = $('#prop-form-icon-right-color').val() || '#64748b'; }
        }

        if ($('#prop-gp-style-css-class').length) {
            el.customClass = $('#prop-gp-style-css-class').val() || '';
            el.cssClass    = el.customClass;
        }

        var stylesArr = [];
        $('#wppoppop-gp-inline-styles-list .wppoppop-gp-repeater-item').each(function() {
            var p = $.trim($(this).find('.gp-css-prop').val() || '');
            var v = $.trim($(this).find('.gp-css-val').val() || '');
            if (p || v) stylesArr.push({ prop: p, value: v });
        });
        el.inlineStyles = stylesArr;

        // 5. Flush Conditional Logic Repeater
        if ($('#prop-logic-enabled').length) {
            el.logicEnabled = $('#prop-logic-enabled').is(':checked');
            el.logicAction  = $('#prop-logic-action').val() || 'show';
            el.logicMatch   = $('#prop-logic-match').val() || 'all';
            var rulesArr = [];
            $('#wppoppop-logic-rules-list .wppoppop-logic-rule-row').each(function() {
                var f  = $.trim($(this).find('.rule-field').val() || '');
                var op = $(this).find('.rule-operator').val() || 'equal';
                var v  = $.trim($(this).find('.rule-value').val() || '');
                if (f || op === 'not_empty') {
                    rulesArr.push({ field: f, operator: op, value: v });
                }
            });
            el.logicRules = rulesArr;
        }

        // 6. Flush Form Data & Advanced Tabs
        if (isForm) {
            /* WPPOPPOP_RATING_DATA_FLUSH: correct ids; Rating uses its own Data controls */
            if (canonical === 'rating' && $('#prop-rt-data-default').length) {
                el.ratingMax      = parseInt($('#prop-rt-data-stars').val(), 10) === 10 ? 10 : 5;
                el.defaultValue   = $('#prop-rt-data-default').val() || '';
                el.dynamicDefault = $('#prop-rt-data-dynamic').is(':checked');
                el.paramName      = $.trim($('#prop-rt-data-param').val() || '');
                el.saveToDb       = $('#prop-rt-data-savedb').is(':checked');
            } else {
                if ($('#prop-data-default').length) { el.defaultValue = $('#prop-data-default').val() || ''; }
                var $dd = $('#prop-data-dynamic-default, #prop-data-dynamic').first();
                if ($dd.length) { el.dynamicDefault = $dd.is(':checked'); }
                var $dp = $('#prop-data-param-name, #prop-data-param').first();
                if ($dp.length) { el.paramName = $dp.val() || ''; }
                if ($('#prop-data-readonly').length) { el.readOnly = $('#prop-data-readonly').is(':checked'); }
                var $ds = $('#prop-data-save-db, #prop-data-savedb').first();
                if ($ds.length) { el.saveToDb = $ds.is(':checked'); }
            }
            /* WPPOPPOP_ADV_TAB: flush */
            if ($('#prop-field-name').length) {
                var advName = $.trim($('#prop-field-name').val() || '').replace(/[^A-Za-z0-9_-]/g, '');
                el.fieldName = advName; el.fieldId = advName; el.token = advName;
            }
            if ($('#prop-input-mask').length) { el.inputMask = $.trim($('#prop-input-mask').val() || ''); }
            if ($('.prop-filter-chk').length) {
                var fArr = [];
                $('.prop-filter-chk:checked').each(function() { fArr.push($(this).val()); });
                el.filters = fArr;
            }
            if ($('.prop-validator-chk').length) {
                var vArr = [];
                $('.prop-validator-chk:checked').each(function() { vArr.push($(this).val()); });
                el.validators = vArr;
                el.errorMessage = $('#prop-validator-error').val() || '';
                el.validatorEqual = $('#prop-validator-equal').val() || '';
                el.validatorList = $('#prop-validator-list').val() || '';
            }
        }

        return el;
    };

    Reg.refreshAllStageElements = function() {
        if (!window.WpPopPopBuilderCanvas || typeof window.WpPopPopBuilderCanvas.getActiveElements !== 'function') {
            return;
        }
        var list = window.WpPopPopBuilderCanvas.getActiveElements() || [];
        for (var i = 0; i < list.length; i++) {
            var item = list[i];
            if (!item || !item.type) continue;
            var rawT = String(item.type).toLowerCase().trim();
            var canT = (Reg.ALIAS_MAP && Reg.ALIAS_MAP[rawT]) ? Reg.ALIAS_MAP[rawT] : rawT;
            if (Reg.FORM_FAMILY && Reg.FORM_FAMILY.indexOf(canT) !== -1) {
                if (typeof Reg.renderFormElementStage === 'function') Reg.renderFormElementStage(item);
            } else {
                if (typeof Reg.renderMediaElementStage === 'function') Reg.renderMediaElementStage(item);
            }
        }
    };

    function bindPhase4LifecycleHooks() {
        if (window.WpPopPopBuilderCanvas && !window.WpPopPopBuilderCanvas._phase4StageHooked) {
            window.WpPopPopBuilderCanvas._phase4StageHooked = true;
            var origRenderCanvas = window.WpPopPopBuilderCanvas.renderCanvas;
            if (typeof origRenderCanvas === 'function') {
                window.WpPopPopBuilderCanvas.renderCanvas = function() {
                    var res = origRenderCanvas.apply(this, arguments);
                    Reg.refreshAllStageElements();
                    return res;
                };
            }
        }
        setTimeout(function() { Reg.refreshAllStageElements(); }, 180);
    }

    $(function() {
        bindPhase4LifecycleHooks();
        setTimeout(bindPhase4LifecycleHooks, 250);
    });
    bindPhase4LifecycleHooks();
})(window, jQuery);
/* === WPPOPPOP_PHASE4_AUTONOMOUS_LIFECYCLE_END === */
