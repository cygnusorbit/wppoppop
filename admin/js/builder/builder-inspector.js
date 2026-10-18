
    function resolveActiveElement() {
        if (!window.WpPopPopBuilderCore || !window.WpPopPopBuilderCore.state) return null;
        var rawId = window.WpPopPopBuilderCore.state.activeId;
        if (!rawId) return null;
        var cleanId = String(rawId).replace(/^el-/, '');
        var cur = window.WpPopPopBuilderCore.state.currentCanvas || 1;
        var elements = (window.WpPopPopBuilderCore.state.canvases && window.WpPopPopBuilderCore.state.canvases[cur]) ? window.WpPopPopBuilderCore.state.canvases[cur] : [];
        return elements.find(function(it) {
            return String(it.id).replace(/^el-/, '') === cleanId;
        });
    }

/**
 * WpPopPop Visual Builder: Layer Settings Inspector Controller
 * Hydrates 26 Elements, Vector Shapes, Spacing/Padding, Typography & Swatches
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderInspector = {
/* --- Per-Layer Instance Settings Parity --- */
    getInstanceElement: function() {
        var core = window.WpPopPopBuilderCore;
        if (!core || !core.state || !core.state.canvases) return null;
        var cur = core.state.currentCanvas || 1;
        var elements = core.state.canvases[cur] || [];
        var activeId = core.state.activeId;
        return elements.find(function(e) { return String(e.id) === String(activeId); });
    },

        bindAdvancedStylingInputs: function() {
        var self = this;

        // Opacity Live Slider
        $(document).off('input.wppoppop_op change.wppoppop_op', '#prop-opacity').on('input.wppoppop_op change.wppoppop_op', '#prop-opacity', function(e) {
            var val = parseInt($(this).val(), 10);
            $('#prop-opacity-val').text(val + '%');
            var el = (typeof self.getActiveEl === 'function' ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null));
            if (el) {
                el.opacity = val;
                $('#wppoppop-el-' + el.id).css('opacity', val / 100);
                if (e.type === 'change' && window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            }
        });

        // Custom CSS Class & ID
        $(document).off('input.wppoppop_css_id change.wppoppop_css_id', '#prop-custom-class, #prop-custom-id').on('input.wppoppop_css_id change.wppoppop_css_id', '#prop-custom-class, #prop-custom-id', function() {
            var el = (typeof self.getActiveEl === 'function' ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null));
            if (el) {
                el.customClass = $('#prop-custom-class').val().trim();
                el.customId = $('#prop-custom-id').val().trim();
            }
        });

        // Button Hover State Colors
        $(document).off('input.wppoppop_btn_hvr change.wppoppop_btn_hvr', '#prop-btn-hover-bg, #prop-btn-hover-color, #prop-btn-hover-border').on('input.wppoppop_btn_hvr change.wppoppop_btn_hvr', '#prop-btn-hover-bg, #prop-btn-hover-color, #prop-btn-hover-border', function(e) {
            var el = (typeof self.getActiveEl === 'function' ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null));
            if (el) {
                el.hoverBgColor = $('#prop-btn-hover-bg').val();
                el.hoverTextColor = $('#prop-btn-hover-color').val();
                el.hoverBorderColor = $('#prop-btn-hover-border').val();
                var $node = $('#wppoppop-el-' + el.id);
                $node.css({
                    '--wppoppop-hover-bg': el.hoverBgColor,
                    '--wppoppop-hover-color': el.hoverTextColor,
                    '--wppoppop-hover-border': el.hoverBorderColor
                });
                if (e.type === 'change' && window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            }
        });
    },

    bindAnimationPlayControls: function() {
        var self = this;
        $(document).off('click.wppoppop_anim_play').on('click.wppoppop_anim_play', '#wppoppop-btn-play-elem-anim, #prop-anim-play-btn, #prop-anim-replay-btn, #prop-anim-test-btn, .wppoppop-anim-play-btn', function(e) {
            e.preventDefault();
            var core = window.WpPopPopBuilderCore;
            var activeId = (core && core.state) ? core.state.activeId : null;
            if (!activeId && window.WpPopPopBuilderCanvas) {
                activeId = window.WpPopPopBuilderCanvas.activeId;
            }
            if (!activeId) {
                var $sel = $('.wppoppop-canvas-item.wppoppop-selected, .wppoppop-selected');
                if ($sel.length) {
                    activeId = $sel.data('id') || $sel.attr('data-id') || ($sel.attr('id') || '').replace(/^(wppoppop-el-|el-)/, '');
                }
            }
            if (!activeId) return;

            var activeEl = null;
            if (core && core.state && core.state.canvases) {
                var cur = core.state.currentCanvas || 1;
                var list = core.state.canvases[cur] || [];
                activeEl = list.find(function(el) { return el.id === activeId; });
            }

            var effect = $('#prop-anim-in').val() || $('#prop-anim-appearance').val() || $('#prop-anim-effect').val();
            if (!effect || effect === 'none') {
                if (activeEl) {
                    effect = activeEl.anim_appearance || activeEl.animEffect || activeEl.animation || activeEl.anim_effect;
                }
            }
            if (!effect || effect === 'none') {
                return;
            }

            var duration = parseInt($('#prop-anim-duration').val(), 10) || 1000;
            var delay = parseInt($('#prop-anim-delay').val(), 10) || 0;

            if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.playAnimation === 'function') {
                window.WpPopPopBuilderCanvas.playAnimation(activeId, effect, duration, delay);
            }
        });

        $(document).off('change.wppoppop_anim_sel').on('change.wppoppop_anim_sel', '#prop-anim-in, #prop-anim-appearance, #prop-anim-effect', function() {
            var val = $(this).val();
            if (val && val !== 'none') {
                var core = window.WpPopPopBuilderCore;
                var activeId = (core && core.state) ? core.state.activeId : null;
                if (activeId && window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.playAnimation === 'function') {
                    window.WpPopPopBuilderCanvas.playAnimation(activeId, val);
                }
            }
        });
    },

    bindInspectorCloseEvents: function() {
        var self = this;
        $(document).off('click.wppoppop_insp_close').on('click.wppoppop_insp_close', '#wppoppop-inspector-close, .wppoppop-inspector-close, .wppoppop-drawer-close, #insp-close-btn', function(e) {
            e.preventDefault();
            self.close();
        });
        $(document).off('click.wppoppop_ws_deselect').on('click.wppoppop_ws_deselect', '#wppoppop-canvas-workspace, .wppoppop-builder-workspace', function(e) {
            if ($(e.target).is('#wppoppop-canvas-workspace, .wppoppop-builder-workspace, #wppoppop-canvas-scroll-container')) {
                self.close();
            }
        });
    },

    bindLayerNameInputEvents: function() {
        var self = this;
        $(document).off('input.wppoppop_lname change.wppoppop_lname', '#prop-layer-name, #prop-element-name')
                   .on('input.wppoppop_lname change.wppoppop_lname', '#prop-layer-name, #prop-element-name', function(e) {
            var newName = $(this).val();
            $('#prop-layer-name, #prop-element-name').not(this).val(newName);

            var core = window.WpPopPopBuilderCore;
            var aId = self.activeId || (core && core.state ? core.state.activeId : null);
            if (!aId && window.WpPopPopBuilderCanvas) aId = window.WpPopPopBuilderCanvas.activeId;
            if (!aId) return;

            var cleanId = String(aId).replace(/^(wppoppop-el-|el-)/, '');
            var cur = (core && core.state) ? (core.state.currentCanvas || 1) : 1;
            var list = (core && core.state && core.state.canvases) ? (core.state.canvases[cur] || []) : [];

            var targetEl = null;
            for (var i = 0; i < list.length; i++) {
                if (String(list[i].id).replace(/^(wppoppop-el-|el-)/, '') === cleanId) {
                    targetEl = list[i];
                    break;
                }
            }

            if (targetEl) {
                targetEl.name = newName;
                targetEl.label = newName;

                // Live-update floating layers panel list item title
                var $panel = $('#wppoppop-floating-layers-panel, #wppoppop-layers-list');
                var $item = $panel.find('[data-id="' + cleanId + '"], [data-element-id="' + cleanId + '"], [data-id="' + aId + '"]');
                if ($item.length) {
                    $item.find('.wppoppop-layer-title').text(newName || targetEl.type);
                }
            }

            if (e.type === 'change' && core && typeof core.pushHistory === 'function') {
                core.pushHistory();
            }
        });
    },

    
        normalizeType: function(type) {
            if (!type) return 'text';
            var t = String(type).toLowerCase();
            var map = {
                'paragraph': 'text',
                'select': 'dropdown',
                'dropdown': 'dropdown',
                'radio': 'radios',
                'radios': 'radios',
                'checkbox': 'checkboxes',
                'checkboxes': 'checkboxes',
                'step': 'step_btn',
                'step_btn': 'step_btn',
                'link': 'link_btn',
                'link_btn': 'link_btn',
                'close_icon': 'close',
                'close': 'close',
                'lucky_wheel': 'wheel',
                'wheel': 'wheel',
                'scratch_card': 'scratch',
                'scratch': 'scratch',
                'custom_html': 'html',
                'html': 'html'
            };
            return map[t] || t;
        },
    
        
    // === v3.0.637 LAYER SETTINGS SANITIZER & CONTEXTUAL SCOPER ===
    
    // === PHASE 4: PRE-SAVE DOM FLUSH FOR SANITIZED 5-TAB LAYER SETTINGS ===
    flushActiveElement: function() { if (typeof this.flushSanitizedLayerSettings === 'function') { this.flushSanitizedLayerSettings(); } },
    flushSanitizedLayerSettings: function() {
        var el = null;
        if (typeof this.getActiveEl === 'function') {
            el = this.getActiveEl();
        } else if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.getActiveElement === 'function') {
            el = window.WpPopPopBuilderCore.getActiveElement();
        }
        if (!el) return;

        // 1. Flush visible .wppoppop-gp-bind inputs inside active element panel
        jQuery('.element-panel:visible .wppoppop-gp-bind, .wppoppop-panel-specific:visible .wppoppop-gp-bind').each(function() {
            var $inp = jQuery(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            el[prop] = $inp.is(':checkbox') ? $inp.is(':checked') :$inp.val();
        });

        // 2. Flush Basic / Content shared controls
        if (jQuery('#prop-gp-autocomplete').length) {
            el.autocomplete = jQuery('#prop-gp-autocomplete').val() || 'off';
        }
        if (jQuery('#prop-anim-in').length) {
            el.animIn       = jQuery('#prop-anim-in').val() || 'none';
            el.animation    = el.animIn;
            el.animOut      = jQuery('#prop-anim-out').val() || 'none';
            el.animDuration = parseInt(jQuery('#prop-anim-duration').val(), 10) || 1000;
            el.animDelay    = parseInt(jQuery('#prop-anim-delay').val(), 10) || 0;
            if (window.WpPopPopBuilderAnimation) { window.WpPopPopBuilderAnimation.syncAliases(el); }
        }
        if (jQuery('#prop-gp-required').length && jQuery('#insp-row-gp-required').is(':visible')) {
            el.required = jQuery('#prop-gp-required').is(':checked');
        }

        // 3. Flush Form Input Style Module (Icons, Alignment, Custom CSS Class, Inline Styles Repeater)
        if (jQuery('#insp-section-form-input-style').length && jQuery('#insp-section-form-input-style').is(':visible')) {
            el.iconLeft        = jQuery('#prop-input-icon-left').val() || '';
            el.iconLeftOffset  = parseInt(jQuery('#prop-input-icon-left-offset').val(), 10) || 10;
            el.iconRight       = jQuery('#prop-input-icon-right').val() || '';
            el.iconRightOffset = parseInt(jQuery('#prop-input-icon-right-offset').val(), 10) || 10;
            el.customClass     = jQuery('#prop-gp-style-css-class').val() || '';
            el.cssClass        = el.customClass;

            var stylesArr = [];
            jQuery('#wppoppop-gp-inline-styles-list .wppoppop-gp-repeater-item').each(function() {
                var p = jQuery.trim(jQuery(this).find('.gp-css-prop').val() || '');
                var v = jQuery.trim(jQuery(this).find('.gp-css-val').val() || '');
                if (p !== '' || v !== '') {
                    stylesArr.push({ prop: p, value: v });
                }
            });
            el.inlineStyles = stylesArr;
        }

        // 4. Flush Conditional Logic Repeater
        if (jQuery('#insp-section-gp-conditional-logic').length) {
            el.logicEnabled = jQuery('#prop-logic-enabled').is(':checked');
            el.logicAction  = jQuery('#prop-logic-action').val() || 'show';
            el.logicMatch   = jQuery('#prop-logic-match').val() || 'all';
            var rulesArr = [];
            jQuery('#wppoppop-gp-logic-rules-list .wppoppop-gp-repeater-item').each(function() {
                var f  = jQuery.trim(jQuery(this).find('.gp-rule-field').val() || '');
                var op = jQuery(this).find('.gp-rule-op').val() || 'is';
                var v  = jQuery.trim(jQuery(this).find('.gp-rule-val').val() || '');
                if (f !== '' || op === 'not_empty') {
                    rulesArr.push({ field: f, operator: op, value: v });
                }
            });
            el.logicRules = rulesArr;
        }

        // 5. Flush Data & Advanced Tabs (if present in DOM)
        if (jQuery('#prop-data-default').length) {
            el.defaultValue   = jQuery('#prop-data-default').val();
        }
        if (jQuery('#prop-data-dynamic').length) {
            el.dynamicDefault = jQuery('#prop-data-dynamic').is(':checked');
        }
        if (jQuery('#prop-data-param').length) {
            el.paramName      = jQuery('#prop-data-param').val();
        }
        if (jQuery('#prop-data-readonly').length) {
            el.readOnly       = jQuery('#prop-data-readonly').is(':checked');
        }
        if (jQuery('#prop-data-savedb').length) {
            el.saveToDb       = jQuery('#prop-data-savedb').is(':checked');
        }
        if (jQuery('#prop-adv-id').length && jQuery('#prop-adv-id').val()) {
            el.fieldId        = jQuery('#prop-adv-id').val();
        }
    },

    sanitizeLayerInspectorForElement: function(el) {
        if (!el) return;
        var rawType = String(el.type || 'label').toLowerCase();
        var aliasMap = {
            'title': 'label', 'paragraph': 'label', 'heading': 'label',
            'textfield': 'text', 'phone': 'tel', 'dropdown': 'select',
            'radios': 'radio', 'checkboxes': 'checkbox', 'file': 'upload',
            'close_icon': 'close', 'link': 'button', 'link_btn': 'button',
            'step': 'button', 'step_btn': 'button', 'spinner': 'number'
        };
        var canonical = aliasMap[rawType] || rawType;

        var mediaFamily = ['shape', 'image', 'video', 'label', 'html', 'close', 'icon', 'progress', 'button', 'submit'];
        var formFamily  = ['text', 'email', 'number', 'tel', 'textarea', 'select', 'checkbox', 'radio', 'multiselect', 'imageselect', 'tile', 'date', 'time', 'upload', 'password', 'rating'];
        var isForm = formFamily.indexOf(canonical) !== -1;

        // 1. Tab Bar Visibility: 5 Tabs for Form (10-25), 3 Tabs for Media (1-9) & Submit (26)
        var $dataTabBtn = jQuery('.wppoppop-inspector-tabs [data-tab="tab-data"], .wppoppop-insp-tab[data-tab="tab-data"], .wppoppop-insp-tab[data-tab="data"]');
        var $advTabBtn  = jQuery('.wppoppop-inspector-tabs [data-tab="tab-advanced"], .wppoppop-insp-tab[data-tab="tab-advanced"], .wppoppop-insp-tab[data-tab="advanced"]');
        if (isForm) {
            $dataTabBtn.show().css('display', 'inline-flex');
            $advTabBtn.show().css('display', 'inline-flex');
        } else {
            $dataTabBtn.hide();
            $advTabBtn.hide();
            var activeTab = jQuery('.wppoppop-inspector-tabs .active, .wppoppop-insp-tab.active').attr('data-tab') || '';
            if (activeTab.indexOf('data') !== -1 || activeTab.indexOf('advanced') !== -1) {
                jQuery('.wppoppop-inspector-tabs [data-tab="tab-basic"], .wppoppop-insp-tab[data-tab="tab-basic"], .wppoppop-insp-tab[data-tab="basic"]').first().trigger('click');
            }
        }

        // 2. Content/Basic Tab Sanitization:
        //    Hide generic #prop-content if dedicated modular panel handles content or if element is non-text form/media
        var usesGenericContent = (canonical === 'label');
        jQuery('#prop-content').closest('.wppoppop-prop-row, .wppoppop-insp-card').toggle(usesGenericContent);

        //    Show Autocomplete only on single-line text-like inputs
        var hasAutocomplete = ['text', 'email', 'tel', 'password', 'number'].indexOf(canonical) !== -1;
        jQuery('#insp-row-gp-autocomplete').toggle(hasAutocomplete);

        //    Show canonical Required switch only for Form elements (10-25)
        jQuery('#insp-row-gp-required').toggle(isForm);

        //    Deduplicate any redundant Required or Token inputs inside #tab-basic or #tab-logic
        jQuery('#insp-section-token').hide();
        jQuery('#wppoppop-element-specific-settings .element-panel').find('input[id*="required"], input[id*="token"]').each(function() {
            if (this.id !== 'prop-gp-required') {
                jQuery(this).closest('.wppoppop-prop-row, .wppoppop-form-group').hide();
            }
        });

        // 3. Style Tab Sanitization:
        //    - Form Family (10-25): Show Form Input Style card; hide Media Typography/Hover/Box cards
        //      (except imageselect, tile, rating, upload which also allow custom color/box tweaks)
        var textBearingMedia = ['label', 'button', 'submit', 'progress'];
        var hoverCapableMedia = ['button', 'submit', 'close', 'icon'];
        var iconSlotFormTypes = ['text', 'email', 'number', 'tel', 'date', 'time', 'password'];

        if (isForm) {
            jQuery('#insp-section-form-input-style').show();
            jQuery('#gp-form-input-icons-row').toggle(iconSlotFormTypes.indexOf(canonical) !== -1);
            jQuery('#insp-section-typography, #wppoppop-hover-state-card').hide();
        } else {
            jQuery('#insp-section-form-input-style').hide();
            jQuery('#insp-section-typography').toggle(textBearingMedia.indexOf(canonical) !== -1);
            jQuery('#wppoppop-hover-state-card').toggle(hoverCapableMedia.indexOf(canonical) !== -1);
        }

        // 4. Logic Tab Sanitization:
        //    - Conditional Logic Builder is available for all 26 elements
        //    - Click/Action Redirect URL only for actionable Media elements (button, submit, image, icon)
        var actionableTypes = ['button', 'submit', 'image', 'icon'];
        jQuery('#insp-section-gp-conditional-logic').show();
        jQuery('#prop-action-url, #prop-action-target, #prop-onclick-action').closest('.wppoppop-prop-row, .wppoppop-insp-card').toggle(actionableTypes.indexOf(canonical) !== -1);

        // 5. Hydrate canonical controls from el model
        jQuery('#prop-gp-autocomplete').val(el.autocomplete || (canonical === 'email' ? 'email' : (canonical === 'tel' ? 'tel' : 'off')));
        jQuery('#prop-gp-required').prop('checked', !!el.required);
        jQuery('#prop-anim-in').val(el.animIn || el.anim_appearance || el.animation || 'none');
        jQuery('#prop-anim-out').val(el.animOut || el.anim_disappearance || 'none');
        jQuery('#prop-anim-duration').val(el.animDuration !== undefined ? el.animDuration : 1000);
        jQuery('#prop-anim-delay').val(el.animDelay !== undefined ? el.animDelay : 0);

        jQuery('#prop-input-icon-left').val(el.iconLeft || (canonical === 'email' ? 'fas fa-envelope' : ''));
        jQuery('#prop-input-icon-left-offset').val(el.iconLeftOffset !== undefined ? el.iconLeftOffset : 10);
        jQuery('#prop-input-icon-right').val(el.iconRight || '');
        jQuery('#prop-input-icon-right-offset').val(el.iconRightOffset !== undefined ? el.iconRightOffset : 10);
        jQuery('#prop-gp-style-css-class').val(el.customClass || el.cssClass || '');

        var currentAlign = el.textAlign || 'left';
        jQuery('#gp-form-text-align-group .wppoppop-align-seg-btn').removeClass('active');
        jQuery('#gp-form-text-align-group .wppoppop-align-seg-btn[data-align="' + currentAlign + '"]').addClass('active');

        jQuery('#prop-logic-enabled').prop('checked', !!el.logicEnabled);
        jQuery('#wppoppop-gp-logic-rules-wrap').toggle(!!el.logicEnabled);
        jQuery('#prop-logic-action').val(el.logicAction || 'show');
        jQuery('#prop-logic-match').val(el.logicMatch || 'all');

        // Hydrate Inline CSS Styles Repeater
        var $stylesList = jQuery('#wppoppop-gp-inline-styles-list').empty();
        if (Array.isArray(el.inlineStyles)) {
            el.inlineStyles.forEach(function(item) {
                if (!item) return;
                var pVal = jQuery('<div>').text(item.prop || '').html();
                var vVal = jQuery('<div>').text(item.value || '').html();
                $stylesList.append(
                    '<div class="wppoppop-gp-repeater-item">' +
                    '<input type="text" class="wppoppop-input gp-css-prop" placeholder="property" value="' + pVal + '">' +
                    '<input type="text" class="wppoppop-input gp-css-val" placeholder="value" value="' + vVal + '">' +
                    '<button type="button" class="wppoppop-gp-btn-remove" title="Remove">&times;</button>' +
                    '</div>'
                );
            });
        }

        // Hydrate Conditional Logic Rules Repeater
        var $rulesList = jQuery('#wppoppop-gp-logic-rules-list').empty();
        if (Array.isArray(el.logicRules)) {
            el.logicRules.forEach(function(rule) {
                if (!rule) return;
                var fVal = jQuery('<div>').text(rule.field || '').html();
                var opVal = rule.operator || 'is';
                var rVal = jQuery('<div>').text(rule.value || '').html();
                $rulesList.append(
                    '<div class="wppoppop-gp-repeater-item">' +
                    '<input type="text" class="wppoppop-input gp-rule-field" placeholder="Field token" value="' + fVal + '">' +
                    '<select class="wppoppop-select gp-rule-op" style="width:90px;">' +
                        '<option value="is"' + (opVal === 'is' ? ' selected' : '') + '>is</option>' +
                        '<option value="is_not"' + (opVal === 'is_not' ? ' selected' : '') + '>is not</option>' +
                        '<option value="contains"' + (opVal === 'contains' ? ' selected' : '') + '>contains</option>' +
                        '<option value="not_empty"' + (opVal === 'not_empty' ? ' selected' : '') + '>not empty</option>' +
                    '</select>' +
                    '<input type="text" class="wppoppop-input gp-rule-val" placeholder="Value" value="' + rVal + '">' +
                    '<button type="button" class="wppoppop-gp-btn-remove" title="Remove rule">&times;</button>' +
                    '</div>'
                );
            });
        }

    },

    bindSanitizedwppoppopControls: function() {
        var self = this;
        if (self._gpSanitizedBound) return;
        self._gpSanitizedBound = true;

        var getEl = function() {
            if (typeof self.getActiveEl === 'function') return self.getActiveEl();
            if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.getActiveElement === 'function') {
                return window.WpPopPopBuilderCore.getActiveElement();
            }
            return null;
        };

        var syncStage = function() {
            if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                window.WpPopPopBuilderCanvas.renderCanvas();
            }
        };

        // Autocomplete, Animation & Required bindings
        jQuery(document).on('change input', '#prop-gp-autocomplete, #prop-anim-in, #prop-anim-out, #prop-anim-duration, #prop-anim-delay, #prop-gp-required', function() {
            var el = getEl();
            if (!el) return;
            el.autocomplete = jQuery('#prop-gp-autocomplete').val();
            el.animIn       = jQuery('#prop-anim-in').val();
            el.animation    = el.animIn;
            el.animOut      = jQuery('#prop-anim-out').val();
            el.animDuration = parseInt(jQuery('#prop-anim-duration').val(), 10) || 1000;
            el.animDelay    = parseInt(jQuery('#prop-anim-delay').val(), 10) || 0;
            if (window.WpPopPopBuilderAnimation) { window.WpPopPopBuilderAnimation.syncAliases(el); }
            el.required     = jQuery('#prop-gp-required').is(':checked');
            jQuery('#prop-required').prop('checked', el.required);
            syncStage();
        });

        // Form Style Text Alignment Segmented Buttons
        jQuery(document).on('click', '#gp-form-text-align-group .wppoppop-align-seg-btn', function(e) {
            e.preventDefault();
            var align = jQuery(this).attr('data-align') || 'left';
            jQuery('#gp-form-text-align-group .wppoppop-align-seg-btn').removeClass('active');
            jQuery(this).addClass('active');
            var el = getEl();
            if (!el) return;
            el.textAlign = align;
            jQuery('#prop-text-align').val(align);
            syncStage();
        });

        // Input Icons & Custom CSS Class
        jQuery(document).on('change input', '#prop-input-icon-left, #prop-input-icon-left-offset, #prop-input-icon-right, #prop-input-icon-right-offset, #prop-gp-style-css-class', function() {
            var el = getEl();
            if (!el) return;
            el.iconLeft        = jQuery('#prop-input-icon-left').val();
            el.iconLeftOffset  = parseInt(jQuery('#prop-input-icon-left-offset').val(), 10) || 10;
            el.iconRight       = jQuery('#prop-input-icon-right').val();
            el.iconRightOffset = parseInt(jQuery('#prop-input-icon-right-offset').val(), 10) || 10;
            el.customClass     = jQuery('#prop-gp-style-css-class').val();
            el.cssClass        = el.customClass;
            jQuery('#prop-custom-class').val(el.customClass);
            syncStage();
        });

        // Add Inline CSS Style Repeater Row
        jQuery(document).on('click', '#wppoppop-btn-add-css-style', function(e) {
            e.preventDefault();
            var el = getEl();
            if (!el) return;
            el.inlineStyles = Array.isArray(el.inlineStyles) ? el.inlineStyles : [];
            el.inlineStyles.push({ prop: '', value: '' });
            var rowHtml = '<div class="wppoppop-gp-repeater-item">' +
                '<input type="text" class="wppoppop-input gp-css-prop" placeholder="property (e.g. border-bottom)">' +
                '<input type="text" class="wppoppop-input gp-css-val" placeholder="value (e.g. 2px solid #c2185b)">' +
                '<button type="button" class="wppoppop-gp-btn-remove" title="Remove">&times;</button>' +
                '</div>';
            jQuery('#wppoppop-gp-inline-styles-list').append(rowHtml);
        });

        // Conditional Logic Toggle & Add Rule Repeater
        jQuery(document).on('change', '#prop-logic-enabled, #prop-logic-action, #prop-logic-match', function() {
            var el = getEl();
            if (!el) return;
            el.logicEnabled = jQuery('#prop-logic-enabled').is(':checked');
            el.logicAction  = jQuery('#prop-logic-action').val();
            el.logicMatch   = jQuery('#prop-logic-match').val();
            jQuery('#wppoppop-gp-logic-rules-wrap').toggle(el.logicEnabled);
        });

        jQuery(document).on('click', '#wppoppop-btn-add-logic-rule', function(e) {
            e.preventDefault();
            var el = getEl();
            if (!el) return;
            el.logicRules = Array.isArray(el.logicRules) ? el.logicRules : [];
            el.logicRules.push({ field: '', operator: 'is', value: '' });
            var ruleHtml = '<div class="wppoppop-gp-repeater-item">' +
                '<input type="text" class="wppoppop-input gp-rule-field" placeholder="Field token">' +
                '<select class="wppoppop-select gp-rule-op" style="width:90px;"><option value="is">is</option><option value="is_not">is not</option><option value="contains">contains</option><option value="not_empty">not empty</option></select>' +
                '<input type="text" class="wppoppop-input gp-rule-val" placeholder="Value">' +
                '<button type="button" class="wppoppop-gp-btn-remove" title="Remove rule">&times;</button>' +
                '</div>';
            jQuery('#wppoppop-gp-logic-rules-list').append(ruleHtml);
        });

        jQuery(document).on('click', '.wppoppop-gp-btn-remove', function(e) {
            e.preventDefault();
            jQuery(this).closest('.wppoppop-gp-repeater-item').remove();
        });
    },

    init: function() {
        if (typeof this.bindSanitizedwppoppopControls === "function") { this.bindSanitizedwppoppopControls(); }
            this.bindTabs();
            this.bindClose();
            this.bindInputs();
            this.bindLayerNameInputEvents();
            this.bindInspectorCloseEvents();
            /* animation preview handled by WpPopPopBuilderAnimation */
        },

        bindTabs: function() {
            var self = this;
            $(document).on('click', '.wppoppop-insp-tab', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                self.switchTab(tab);
            });
        },

        switchTab: function(tab) {
        $('.wppoppop-insp-tab').removeClass('active').css({
            'border-bottom': '2px solid transparent',
            'color': '#94a3b8'
        });
        $('.wppoppop-insp-tab[data-tab="' + tab + '"]').addClass('active').css({
            'border-bottom': '2px solid #38bdf8',
            'color': '#38bdf8'
        });

        $('.wppoppop-tab-pane, .wppoppop-insp-content').hide();
        $('#insp-tab-' + tab).show();
    },
    _orig_switchTab: function(tab) {
            $('.wppoppop-insp-tab').removeClass('active').css({
                borderBottomColor: 'transparent',
                color: '#94a3b8'
            });
            $('.wppoppop-insp-tab[data-tab="' + tab + '"]').addClass('active').css({
                borderBottomColor: '#38bdf8',
                color: '#38bdf8'
            });

            $('.wppoppop-tab-pane').hide();
            $('#insp-tab-' + tab).show();

                if (tab === 'style') {
                    var curEl = (typeof self.getActiveEl === 'function' ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null));
                    if (!curEl && window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
                        var aId = window.WpPopPopBuilderCore.state.activeId;
                        var curC = window.WpPopPopBuilderCore.state.currentCanvas || 1;
                        var cList = window.WpPopPopBuilderCore.state.canvases ? window.WpPopPopBuilderCore.state.canvases[curC] : [];
                        curEl = (cList || []).find(function(it) { return it.id === aId; });
                    }
                    if (curEl) {
                        var cType = (curEl.type || 'text').toLowerCase();
                        if (['shape', 'image', 'video', 'signature', 'html'].indexOf(cType) !== -1) {
                            $('#insp-typography-section').hide();
                        } else {
                            $('#insp-typography-section').show();
                        }
                    }
                }

        },

        bindClose: function() {
            var self = this;
            $('#wppoppop-inspector-close').on('click', function(e) {
                e.preventDefault();
                self.close();
            });
        },

        updateContextualVisibility: function(type) {
        var rawType = (type || '').toLowerCase();
        this.updateContextualVisibility(rawType);
        var textTypes = ['title', 'paragraph', 'text', 'textfield', 'email', 'number', 'select', 'dropdown', 'date', 'submit', 'submit_btn', 'step', 'step_btn', 'link', 'link_btn', 'pay', 'pay_btn', 'countdown'];
        var buttonTypes = ['submit', 'submit_btn', 'step', 'step_btn', 'link', 'link_btn', 'pay', 'pay_btn', 'close', 'close_icon'];
        var formTypes = ['textfield', 'email', 'number', 'select', 'dropdown', 'radios', 'radio', 'checkboxes', 'checkbox', 'date', 'file'];

        // Typography Section
        if (textTypes.indexOf(rawType) !== -1) {
            $('#insp-section-typography, .insp-section-typography').show();
        } else {
            $('#insp-section-typography, .insp-section-typography').hide();
        }

        // Button States Switcher Section
        if (buttonTypes.indexOf(rawType) !== -1) {
            $('#insp-section-button-states').show();
        } else {
            $('#insp-section-button-states').hide();
            $('#wppoppop-hover-props-panel').hide();
            $('#wppoppop-btn-state-toggle .wppoppop-segment-btn[data-state="normal"]').addClass('active');
            $('#wppoppop-btn-state-toggle .wppoppop-segment-btn[data-state="hover"]').removeClass('active');
        }

        // Form Tokens & Token pills
        if (formTypes.indexOf(rawType) !== -1 || rawType === 'paragraph' || rawType === 'title') {
            $('#insp-section-tokens, .wppoppop-token-pills').show();
        } else {
            $('#insp-section-tokens, .wppoppop-token-pills').hide();
        }
    },

    bindButtonStateTabs: function() {
        var self = this;
        $('#wppoppop-btn-state-toggle').off('click.wppoppop_btn_state', '.wppoppop-segment-btn').on('click.wppoppop_btn_state', '.wppoppop-segment-btn', function(e) {
            e.preventDefault();
            var state = $(this).data('state');
            $('#wppoppop-btn-state-toggle .wppoppop-segment-btn').removeClass('active');
            $(this).addClass('active');

            if (state === 'hover') {
                $('#wppoppop-hover-props-panel').slideDown(140);
                $('#insp-section-appearance').slideUp(140);
            } else {
                $('#wppoppop-hover-props-panel').slideUp(140);
                $('#insp-section-appearance').slideDown(140);
            }
        });
    },

    bindBoxShadowBuilder: function() {
        var self = this;
        var updateShadow = function() {
            var cur = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) ? (window.WpPopPopBuilderCore.state.currentCanvas || 1) : 1;
            var elements = window.WpPopPopBuilderCore.state.canvases[cur] || [];
            var el = elements.find(function(item) { return item.id === self.activeId; });
            if (!el) return;

            var x = parseInt($('#prop-shadow-x').val(), 10) || 0;
            var y = parseInt($('#prop-shadow-y').val(), 10) || 0;
            var blur = parseInt($('#prop-shadow-blur').val(), 10) || 0;
            var spread = parseInt($('#prop-shadow-spread').val(), 10) || 0;
            var color = $('#prop-shadow-color').val() || 'transparent';

            el.shadowX = x;
            el.shadowY = y;
            el.shadowBlur = blur;
            el.shadowSpread = spread;
            el.shadowColor = color;

            var shadowStr = (x === 0 && y === 0 && blur === 0 && spread === 0) ? 'none' : (x + 'px ' + y + 'px ' + blur + 'px ' + spread + 'px ' + color);
            el.boxShadow = shadowStr;

            var $node = $('#' + el.id + ', #el-' + el.id);
            $node.css('box-shadow', shadowStr);
        };

        $('#prop-shadow-x, #prop-shadow-y, #prop-shadow-blur, #prop-shadow-spread, #prop-shadow-color')
            .off('input.wppoppop_shadow change.wppoppop_shadow')
            .on('input.wppoppop_shadow change.wppoppop_shadow', updateShadow);
    },

    hydrateStyleLogicFields: function(el) {
        if (!el) return;
        var op = (el.opacity !== undefined && el.opacity !== null) ? parseFloat(el.opacity) : 1;
        var opPercent = Math.round(op <= 1 ? op * 100 : op);
        $('#prop-opacity').val(opPercent);
        $('#prop-opacity-val').text(opPercent + '%');

        // Button hover visibility & values
        var btnTypes = ['submit', 'submit_btn', 'step', 'step_btn', 'link', 'link_btn', 'pay', 'pay_btn', 'close', 'close_icon'];
        if (btnTypes.indexOf((el.type || '').toLowerCase()) !== -1) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBg || el.btnHoverBg || '#2563eb');
            $('#prop-btn-hover-color').val(el.hoverColor || el.btnHoverColor || '#ffffff');
            $('#prop-btn-hover-border').val(el.hoverBorder || el.btnHoverBorder || '#1d4ed8');
        } else {
            $('#prop-btn-hover-section').hide();
        }

        // Custom CSS identifiers
        $('#prop-custom-class').val(el.custom_css_class || el.customClass || '');
        $('#prop-custom-id').val(el.custom_css_id || el.customId || '');

        // Navigation
        $('#prop-goto-canvas').val(el.goto_canvas || el.goto_screen || el.target_canvas || 2);

        // Display Token
        var tokenName = el.fieldName || el.field_name || el.name || el.type || 'field';
        $('#prop-display-token').val('{' + tokenName + '}');
    },

    open: function(id) {
            if (window.WpPopPopElementsRegistry) {
                var _regEl = window.WpPopPopElementsRegistry.resolveElement(id);
                if (_regEl) {
                    window.WpPopPopElementsRegistry.hydrateElement(_regEl, this);
                }
            }
        var self = this;
        if (!id) return;
        self.activeId = id;

        // 1. Structural containment & Slide-in UI activation
        var $drawer = $('#wppoppop-inspector-drawer');
        var $ws = $('.wppoppop-builder-workspace, #wppoppop-builder-workspace').first();
        if ($drawer.length && $ws.length && $drawer.parent()[0] !== $ws[0]) {
            $drawer.appendTo($ws);
        }

        $drawer.css('display', 'flex').removeClass('is-closed').addClass('is-open');
        $('body, .wppoppop-builder-workspace, .wppoppop-main-frame, .wppoppop-builder-wrap').addClass('panel-open inspector-open');

        if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.pushForInspector === 'function') {
            window.WpPopPopBuilderLayers.pushForInspector(true);
        }

        // 2. Safe defensive element resolution
        var elements = [];
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') {
            elements = window.WpPopPopBuilderCanvas.getActiveElements() || [];
        }
        if ((!elements || !elements.length) && window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.canvases) {
            var cur = window.WpPopPopBuilderCore.state.currentCanvas || 1;
            elements = window.WpPopPopBuilderCore.state.canvases[cur] || [];
        }

        var el = null;
        if (elements && elements.length) {
            for (var i = 0; i < elements.length; i++) {
                if (String(elements[i].id) === String(id)) {
                    el = elements[i];
                    break;
                }
            }
        }
        if (!el && window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.canvases) {
            var curC = window.WpPopPopBuilderCore.state.currentCanvas || 1;
            var list = window.WpPopPopBuilderCore.state.canvases[curC] || [];
            for (var j = 0; j < list.length; j++) {
                if (String(list[j].id) === String(id)) {
                    el = list[j];
                    break;
                }
            }
        }

        if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }


        // 3. Mark element as selected on stage
        $('.wppoppop-element, .wppoppop-canvas-item').removeClass('wppoppop-selected ui-selected');
        $('#wppoppop-el-' + id + ', #el-' + id + ', [data-id="' + id + '"]').addClass('wppoppop-selected');
        if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
            window.WpPopPopBuilderCore.state.activeId = id;
        }

        // 4. Populate Layer Name
        $('#prop-layer-name, #prop-element-name').val(el.name || el.label || el.type || '');

        // 5. Canonical Type Normalization & Contextual Panels
        var canonicalType = (el.type || 'text').toLowerCase();
        if (canonicalType === 'select') canonicalType = 'dropdown';
        if (canonicalType === 'radios') canonicalType = 'radio';
        if (canonicalType === 'checkboxes') canonicalType = 'checkbox';
        if (canonicalType === 'step_btn') canonicalType = 'step';
        if (canonicalType === 'link_btn') canonicalType = 'link';
        if (canonicalType === 'close_icon') canonicalType = 'close';
        if (canonicalType === 'text') canonicalType = 'paragraph';

        // Contextual Typography Visibility (Hide for non-text types)
        var nonTextTypes = ['shape', 'image', 'video', 'signature', 'html'];
        if (nonTextTypes.indexOf(canonicalType) !== -1 || nonTextTypes.indexOf((el.type || '').toLowerCase()) !== -1) {
            $('#insp-typography-section').hide();
        } else {
            $('#insp-typography-section').show();
        }

        // Contextual Form Token Visibility
        var inputElements = ['textfield', 'email', 'number', 'dropdown', 'radio', 'checkbox', 'rating', 'date', 'slider', 'file', 'signature'];
        if (inputElements.indexOf(canonicalType) === -1) {
            $('#prop-display-token').closest('.wppoppop-inspector-section, div').hide();
        } else {
            $('#prop-display-token').closest('.wppoppop-inspector-section, div').show();
        }

        // Show matching contextual panel in Basic Tab
        $('.element-panel').hide();
        var $activePanel = $('#panel-elem-' + canonicalType);
        if (!$activePanel.length && canonicalType === 'paragraph') $activePanel = $('#panel-elem-text');
        if (!$activePanel.length && canonicalType === 'dropdown') $activePanel = $('#panel-elem-select');
        if (!$activePanel.length && canonicalType === 'radio') $activePanel = $('#panel-elem-radios');
        if (!$activePanel.length && canonicalType === 'checkbox') $activePanel = $('#panel-elem-checkboxes');
        if (!$activePanel.length && canonicalType === 'step') $activePanel = $('#panel-elem-step_btn');
        if (!$activePanel.length && canonicalType === 'link') $activePanel = $('#panel-elem-link_btn');
        if (!$activePanel.length && canonicalType === 'close') $activePanel = $('#panel-elem-close_icon');
        if ($activePanel.length) {
            $activePanel.show();
        }

        // 6. Element-specific values population
        if (canonicalType === 'title') {
            $('#prop-title-content, #prop-title-text').val(el.content || el.text || '');
            $('#prop-title-tag').val(el.htmlTag || el.tag || 'h2');
        } else if (canonicalType === 'paragraph') {
            $('#prop-paragraph-content, #prop-text-content').val(el.content || el.text || '');
        } else if (canonicalType === 'textfield') {
            $('#prop-textfield-placeholder').val(el.placeholder || el.content || '');
            $('#prop-textfield-fieldname').val(el.fieldName || el.field_name || 'text_field');
            $('#prop-textfield-required').prop('checked', !!el.required);
        } else if (canonicalType === 'dropdown') {
            $('#prop-dropdown-options, #prop-select-options').val(Array.isArray(el.options) ? el.options.join(', ') : (el.content || ''));
            $('#prop-dropdown-fieldname, #prop-select-fieldname').val(el.fieldName || el.field_name || 'select_field');
        } else if (canonicalType === 'step') {
            $('#prop-step-label').val(el.content || el.text || 'Next');
            $('#prop-step-canvas').val(el.goto_canvas || el.goto_screen || 2);
        } else if (canonicalType === 'link') {
            $('#prop-link-label').val(el.content || el.text || 'Learn More');
            $('#prop-link-url').val(el.url || el.href || '');
        } else if (canonicalType === 'close') {
            $('#prop-close-label').val(el.content || el.text || '×');
            $('#prop-close-action').val(el.action || el.close_action || 'close');
        } else if (canonicalType === 'image') {
            $('#prop-image-url').val(el.url || el.src || '');
            $('#prop-image-alt').val(el.alt || '');
        } else if (canonicalType === 'video') {
            $('#prop-video-url').val(el.videoUrl || el.content || el.url || el.src || '');
            $('#prop-video-autoplay').prop('checked', !!(el.videoAutoplay || el.autoplay));
            $('#prop-video-mute').prop('checked', !!(el.videoMute || el.mute));
            $('#prop-video-loop').prop('checked', !!(el.videoLoop || el.loop));
            $('#prop-video-provider').val(el.videoProvider || 'youtube');
            $('#prop-video-aspect').val(el.aspectRatio || '16:9');
        }

        // 7. General Appearance, Coordinates & Dimensions
        $('#prop-left').val(parseInt(el.left, 10) || 0);
        $('#prop-top').val(parseInt(el.top, 10) || 0);
        $('#prop-width, #prop-size-width').val(parseInt(el.width, 10) || 100);
        $('#prop-height, #prop-size-height').val(parseInt(el.height, 10) || 40);

        // Opacity
        var elOpacity = (el.opacity !== undefined && el.opacity !== null && el.opacity !== '') ? parseInt(el.opacity, 10) : 100;
        $('#prop-opacity').val(elOpacity);
        $('#prop-opacity-val').text(elOpacity + '%');

        // Custom CSS Class & ID
        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        // Button Hover States
        var isButtonType = ['submit', 'link', 'step', 'pay'].indexOf(canonicalType) !== -1;
        if (isButtonType) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '#2563eb');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '#ffffff');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '#1d4ed8');
        } else {
            $('#prop-btn-hover-section').hide();
        }

        // Typography
        if ($('#prop-font-family').length && el.fontFamily) $('#prop-font-family').val(el.fontFamily);
        if ($('#prop-font-size').length && el.fontSize) $('#prop-font-size').val(parseInt(el.fontSize, 10) || 16);
        if ($('#prop-font-weight').length && el.fontWeight) $('#prop-font-weight').val(el.fontWeight);
        if ($('#prop-text-align').length && el.textAlign) $('#prop-text-align').val(el.textAlign);
        if ($('#prop-line-height').length && el.lineHeight) $('#prop-line-height').val(el.lineHeight);
        if ($('#prop-letter-spacing').length && el.letterSpacing) $('#prop-letter-spacing').val(el.letterSpacing);

        // Colors
        if ($('#prop-color').length && el.color) $('#prop-color').val(el.color);
        if ($('#prop-bg-color').length && el.bgColor) $('#prop-bg-color').val(el.bgColor);

        // Animation (canonical keys: animIn / animOut / animDuration / animDelay)
        if ($('#prop-anim-in').length) {
            $('#prop-anim-in').val(el.animIn || el.anim_appearance || el.animation || 'none');
            $('#prop-anim-out').val(el.animOut || el.anim_disappearance || 'none');
            $('#prop-anim-duration').val(el.animDuration !== undefined && el.animDuration !== '' ? el.animDuration : 1000);
            $('#prop-anim-delay').val(el.animDelay !== undefined && el.animDelay !== '' ? el.animDelay : 0);
        }
    },

    close: function() {
        var $drawer = $('#wppoppop-inspector-drawer');
        $drawer.removeClass('is-open').addClass('is-closed');
        $('body, .wppoppop-builder-workspace, .wppoppop-main-frame, .wppoppop-builder-wrap').removeClass('panel-open inspector-open');
        setTimeout(function() {
            if (!$drawer.hasClass('is-open')) {
                $drawer.hide();
            }
        }, 280);
        $('.wppoppop-element, .wppoppop-canvas-item').removeClass('wppoppop-selected ui-selected');
        if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
            window.WpPopPopBuilderCore.state.activeId = null;
        }
        if (window.WpPopPopBuilderCanvas) {
            window.WpPopPopBuilderCanvas.activeId = null;
        }
        if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.pushForInspector === 'function') {
            window.WpPopPopBuilderLayers.pushForInspector(false);
        }
    },

        getActiveElement: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state.activeId) return null;
            var cur = core.state.currentCanvas || 1;
            var elements = core.state.canvases[cur] || [];
            return elements.find(function(item) { return String(item.id) === String(core.state.activeId); });
        },

        syncToCanvas: function() {
            if (window.WpPopPopBuilderCanvas) {
                window.WpPopPopBuilderCanvas.renderCanvas();
            }
            if (window.WpPopPopBuilderLayers) {
                window.WpPopPopBuilderLayers.renderLayers();
            }
            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        
        showElementSpecificSettings: function(el) {
            var rawType = (el && el.type) ? String(el.type).toLowerCase() : 'text';
            var normType = this.normalizeType ? this.normalizeType(rawType) : rawType;
            
            // Hide all specific panels
            var panels = document.querySelectorAll('.element-panel, .wppoppop-panel-specific');
            panels.forEach(function(p) { p.style.display = 'none'; });

            // Activate matching panel
            var panel = document.getElementById('panel-elem-' + normType) ||
                        document.getElementById('panel-elem-' + rawType) ||
                        document.getElementById('panel-elem-' + (normType === 'text' ? 'paragraph' : normType));
            if (panel) {
                panel.style.display = 'block';
            }

            // Contextual isolation: Show/Hide Typography in Style tab based on element type
            var textTypes = ['text', 'paragraph', 'title', 'email', 'textfield', 'number', 'dropdown', 'select', 'radios', 'checkboxes', 'step_btn', 'link_btn', 'submit', 'pay', 'countdown'];
            var isTextual = textTypes.indexOf(normType) !== -1 || textTypes.indexOf(rawType) !== -1;
            var typoSection = document.getElementById('wppoppop-insp-typography-group');
            if (typoSection) {
                typoSection.style.display = isTextual ? 'block' : 'none';
            }
        },
    
        syncCoordinates: function(el) {
            $('#prop-left').val(Math.round(el.left || 0));
            $('#prop-top').val(Math.round(el.top || 0));
            $('#prop-width').val(Math.round(el.width || 0));
            $('#prop-height').val(Math.round(el.height || 0));
        },

        bindStyleLogicSync: function() {
        var self = this;
        var getActiveEl = function() {
            var activeId = window.WpPopPopBuilderCore ? window.WpPopPopBuilderCore.state.activeId : null;
            if (!activeId) return null;
            var elements = window.WpPopPopBuilderCanvas ? window.WpPopPopBuilderCanvas.getActiveElements() : [];
            return elements.find(function(e) { return e.id === activeId; });
        };

        // Opacity live stage update
        $('#prop-opacity').off('input.wppoppop_op change.wppoppop_op').on('input.wppoppop_op change.wppoppop_op', function() {
            var el = getActiveEl();
            if (!el) return;
            var val = parseInt($(this).val(), 10);
            $('#prop-opacity-val').text(val + '%');
            var opFloat = (val / 100).toFixed(2);
            el.opacity = parseFloat(opFloat);
            $('#el-' + el.id + ', #' + el.id).css('opacity', opFloat);
        });

        // Hover live model update
        $('#prop-btn-hover-bg, #prop-btn-hover-color, #prop-btn-hover-border').off('input.wppoppop_hover change.wppoppop_hover')
            .on('input.wppoppop_hover change.wppoppop_hover', function() {
                var el = getActiveEl();
                if (!el) return;
                el.hoverBg = $('#prop-btn-hover-bg').val();
                el.btnHoverBg = el.hoverBg;
                el.hoverColor = $('#prop-btn-hover-color').val();
                el.btnHoverColor = el.hoverColor;
                el.hoverBorder = $('#prop-btn-hover-border').val();
                el.btnHoverBorder = el.hoverBorder;
            });

        // Custom CSS identifiers update
        $('#prop-custom-class, #prop-custom-id').off('input.wppoppop_css change.wppoppop_css')
            .on('input.wppoppop_css change.wppoppop_css', function() {
                var el = getActiveEl();
                if (!el) return;
                el.custom_css_class = $('#prop-custom-class').val().trim();
                el.customClass = el.custom_css_class;
                el.custom_css_id = $('#prop-custom-id').val().trim();
                el.customId = el.custom_css_id;
            });

        // Target Canvas update
        $('#prop-goto-canvas').off('change.wppoppop_canvas').on('change.wppoppop_canvas', function() {
            var el = getActiveEl();
            if (!el) return;
            var cVal = parseInt($(this).val(), 10) || 1;
            el.goto_canvas = cVal;
            el.goto_screen = cVal;
            el.target_canvas = cVal;
        });

        // Click to copy display token
        $('#prop-display-token').off('click.wppoppop_copy_token').on('click.wppoppop_copy_token', function() {
            $(this).select();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText($(this).val());
            } else {
                document.execCommand('copy');
            }
        });
    },

    bindInputs: function() {
        var self = this;
        var getActiveEl = function() {
            var core = window.WpPopPopBuilderCore;
            var activeId = core && core.state ? core.state.activeId : null;
            if (!activeId) return null;
            var elements = (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') ? window.WpPopPopBuilderCanvas.getActiveElements() : [];
            return elements.find(function(e) { return e.id === activeId; }) || null;
        };

        // HTML / Embed Code Live Sync
        $(document).off('input.wppoppop_html change.wppoppop_html', '#prop-html-code')
            .on('input.wppoppop_html change.wppoppop_html', '#prop-html-code', function() {
                var el = getActiveEl();
                if (!el) return;
                var val = $(this).val();
                el.content = val;
                el.htmlCode = val;
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

        // Divider Live Sync
        $(document).off('change.wppoppop_div input.wppoppop_div', '#prop-divider-style, #prop-divider-weight, #prop-divider-color, #prop-divider-width-pct, #prop-divider-align')
            .on('change.wppoppop_div input.wppoppop_div', '#prop-divider-style, #prop-divider-weight, #prop-divider-color, #prop-divider-width-pct, #prop-divider-align', function() {
                var el = getActiveEl();
                if (!el) return;
                el.dividerStyle = $('#prop-divider-style').val();
                el.dividerWeight = parseInt($('#prop-divider-weight').val(), 10) || 2;
                el.dividerColor = $('#prop-divider-color').val();
                el.dividerWidthPct = parseInt($('#prop-divider-width-pct').val(), 10) || 100;
                el.dividerAlign = $('#prop-divider-align').val();
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

        // Icon Preset & Class Live Sync
        $(document).off('change.wppoppop_icon_preset', '#prop-icon-preset').on('change.wppoppop_icon_preset', '#prop-icon-preset', function() {
            var val = $(this).val();
            if (val !== 'custom') {
                $('#prop-icon-class').val(val).trigger('input');
            }
        });

        $(document).off('input.wppoppop_icon change.wppoppop_icon', '#prop-icon-class, #prop-icon-size, #prop-icon-color')
            .on('input.wppoppop_icon change.wppoppop_icon', '#prop-icon-class, #prop-icon-size, #prop-icon-color', function() {
                var el = getActiveEl();
                if (!el) return;
                var iClass = $('#prop-icon-class').val().trim() || 'fas fa-star';
                var iSize = parseInt($('#prop-icon-size').val(), 10) || 36;
                var iColor = $('#prop-icon-color').val() || '#38bdf8';

                el.iconClass = iClass;
                el.content = iClass;
                el.iconSize = iSize;
                el.iconColor = iColor;

                $('#prop-icon-live-preview').attr('class', iClass).css({
                    color: iColor,
                    fontSize: iSize + 'px'
                });

                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

        this.bindStyleLogicSync();
        this.bindButtonStateTabs();
        this.bindBoxShadowBuilder();

        /* Title Element Event Listeners */
        $('#prop-title-text').off('input.wppoppopTitle change.wppoppopTitle').on('input.wppoppopTitle change.wppoppopTitle', function() {
            var el = (typeof self.getActiveEl === 'function') ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null);
            if (!el) return;
            var val = $(this).val();
            el.titleText = val;
            el.text = val;
            el.content = val;

            var $node = $('#el-' + el.id + ', #canvas-elem-' + el.id);
            if ($node.length) {
                var $inner = $node.find('.wppoppop-title-inner');
                if ($inner.length) {
                    $inner.text(val || 'Headline Title');
                } else {
                    $node.find('h1, h2, h3, h4, h5, h6, div').first().text(val || 'Headline Title');
                }
            }
        });

        $('#prop-title-tag').off('change.wppoppopTitleTag').on('change.wppoppopTitleTag', function() {
            var el = (typeof self.getActiveEl === 'function') ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null);
            if (!el) return;
            var newTag = $(this).val();
            el.headingTag = newTag;
            el.titleTag = newTag;
            el.tag = newTag;

            if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                window.WpPopPopBuilderCanvas.renderCanvas();
            }
            if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                window.WpPopPopBuilderCore.pushHistory('Change Heading Tag');
            }
        });

        $('#prop-title-transform').off('change.wppoppopTitleTrans').on('change.wppoppopTitleTrans', function() {
            var el = (typeof self.getActiveEl === 'function') ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null);
            if (!el) return;
            var transVal = $(this).val();
            el.textTransform = transVal;
            el.text_transform = transVal;

            var $node = $('#el-' + el.id + ', #canvas-elem-' + el.id);
            if ($node.length) {
                $node.find('.wppoppop-title-inner, h1, h2, h3, h4, h5, h6, div').css('text-transform', transVal);
            }
            if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                window.WpPopPopBuilderCore.pushHistory('Change Text Transform');
            }
        });


        /* Phase 2: Bind Opacity & Hover Listeners */
        $('#prop-opacity').off('input.wppoppopOpacity').on('input.wppoppopOpacity', function() {
            var val = parseInt($(this).val(), 10);
            $('#prop-opacity-val').text(val + '%');
            var el = (typeof self.getActiveEl === 'function') ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null);
            if (el) {
                el.opacity = val / 100;
                var $node = $('#canvas-elem-' + el.id);
                if ($node.length) $node.css('opacity', el.opacity);
            }
        });

        $('#prop-custom-class').off('input.wppoppopCustomClass change.wppoppopCustomClass').on('input.wppoppopCustomClass change.wppoppopCustomClass', function() {
            var val = $(this).val().trim();
            var el = (typeof self.getActiveEl === 'function') ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null);
            if (el) {
                el.customClass = val;
                el.custom_class = val;
            }
        });

        $('#prop-custom-id').off('input.wppoppopCustomId change.wppoppopCustomId').on('input.wppoppopCustomId change.wppoppopCustomId', function() {
            var val = $(this).val().trim();
            var el = (typeof self.getActiveEl === 'function') ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null);
            if (el) {
                el.customId = val;
                el.custom_id = val;
            }
        });

        $('#prop-btn-hover-bg, #prop-btn-hover-color, #prop-btn-hover-border').off('input.wppoppopHover change.wppoppopHover').on('input.wppoppopHover change.wppoppopHover', function() {
            var el = (typeof self.getActiveEl === 'function') ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null);
            if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }

            el.hoverBgColor = $('#prop-btn-hover-bg').val().trim();
            el.hoverTextColor = $('#prop-btn-hover-color').val().trim();
            el.hoverBorderColor = $('#prop-btn-hover-border').val().trim();
            
            var $node = $('#canvas-elem-' + el.id);
            if ($node.length) {
                $node.attr('data-has-hover', 'true');
                if (el.hoverBgColor) $node[0].style.setProperty('--wppoppop-hover-bg', el.hoverBgColor);
                if (el.hoverTextColor) $node[0].style.setProperty('--wppoppop-hover-color', el.hoverTextColor);
                if (el.hoverBorderColor) $node[0].style.setProperty('--wppoppop-hover-border', el.hoverBorderColor);
            }
        });


            // --- 26-ELEMENT TWO-WAY LIVE EVENT BINDINGS ---
            $('#prop-title-text, #prop-paragraph-text, #prop-text-content').off('input.wppoppop_sync').on('input.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    var val = $(this).val();
                    el.text = val;
                    el.content = val;
                    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-tf-placeholder, #prop-tf-name, #prop-tf-required').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.placeholder = $('#prop-tf-placeholder').val();
                    el.fieldName = $('#prop-tf-name').val();
                    el.required = $('#prop-tf-required').is(':checked');
                }
            });

            $('#prop-email-placeholder, #prop-email-name, #prop-email-required').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.placeholder = $('#prop-email-placeholder').val();
                    el.fieldName = $('#prop-email-name').val();
                    el.required = $('#prop-email-required').is(':checked');
                }
            });

            $('#prop-num-placeholder, #prop-num-min, #prop-num-max, #prop-num-step, #prop-num-name, #prop-num-required').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.placeholder = $('#prop-num-placeholder').val();
                    el.min = parseFloat($('#prop-num-min').val()) || 0;
                    el.max = parseFloat($('#prop-num-max').val()) || 100;
                    el.step = parseFloat($('#prop-num-step').val()) || 1;
                    el.fieldName = $('#prop-num-name').val();
                    el.required = $('#prop-num-required').is(':checked');
                }
            });

            $('#prop-select-options, #prop-select-name, #prop-select-required').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.options = $('#prop-select-options').val().split('\n').filter(Boolean);
                    el.fieldName = $('#prop-select-name').val();
                    el.required = $('#prop-select-required').is(':checked');
                }
            });

            $('#prop-radio-options, #prop-radio-name').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.options = $('#prop-radio-options').val().split('\n').filter(Boolean);
                    el.fieldName = $('#prop-radio-name').val();
                }
            });

            $('#prop-cb-label, #prop-cb-name, #prop-cb-checked, #prop-cb-required').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.label = $('#prop-cb-label').val();
                    el.checkboxLabel = el.label;
                    el.fieldName = $('#prop-cb-name').val();
                    el.checked = $('#prop-cb-checked').is(':checked');
                    el.required = $('#prop-cb-required').is(':checked');
                }
            });

            $('#prop-date-placeholder, #prop-date-name, #prop-date-required').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.placeholder = $('#prop-date-placeholder').val();
                    el.fieldName = $('#prop-date-name').val();
                    el.required = $('#prop-date-required').is(':checked');
                }
            });

            $('#prop-file-extensions, #prop-file-max-size, #prop-file-name, #prop-file-required').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.allowedExtensions = $('#prop-file-extensions').val();
                    el.maxSize = parseInt($('#prop-file-max-size').val(), 10) || 5;
                    el.fieldName = $('#prop-file-name').val();
                    el.required = $('#prop-file-required').is(':checked');
                }
            });

            $('#prop-step-label, #prop-step-canvas').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.label = $('#prop-step-label').val();
                    el.targetCanvas = parseInt($('#prop-step-canvas').val(), 10) || 2;
                    el.goto_canvas = el.targetCanvas;
                    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-pay-label, #prop-pay-amount, #prop-pay-currency, #prop-pay-gateway').off('input.wppoppop_sync change.wppoppop_sync').on('input.wppoppop_sync change.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.label = $('#prop-pay-label').val();
                    el.amount = $('#prop-pay-amount').val();
                    el.currency = $('#prop-pay-currency').val();
                    el.gateway = $('#prop-pay-gateway').val();
                    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $('#prop-html-code').off('input.wppoppop_sync').on('input.wppoppop_sync', function() {
                var el = resolveEl();
                if (el) {
                    el.htmlCode = $(this).val();
                    el.code = el.htmlCode;
                    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            var self = this;
            var resolveEl = function() {
                return (typeof self.getActiveEl === 'function') ? self.getActiveEl() : ((typeof self.getActiveElement === 'function') ? self.getActiveElement() : null);
            };

            // Dynamic Heading Tag Synchronization
            $('#prop-title-tag').off('change.wppoppop_stage').on('change.wppoppop_stage', function() {
                var el = resolveEl();
                if (el) {
                    el.titleTag = $(this).val();
                    el.htmlTag = $(this).val();
                    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                    if (window.WpPopPopBuilderCore) window.WpPopPopBuilderCore.pushHistory();
                }
            });

            // Image Object-Fit & Alt Synchronization
            $('#prop-image-fit, #prop-image-alt').off('input.wppoppop_stage change.wppoppop_stage').on('input.wppoppop_stage change.wppoppop_stage', function() {
                var el = resolveEl();
                if (el) {
                    el.imageFit = $('#prop-image-fit').val();
                    el.objectFit = el.imageFit;
                    el.imageAlt = $('#prop-image-alt').val();
                    el.alt = el.imageAlt;
                    el.altText = el.imageAlt;
                    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            // Video URL & Controls Synchronization
            $('#prop-video-url, #prop-video-autoplay, #prop-video-mute, #prop-video-controls').off('input.wppoppop_stage change.wppoppop_stage').on('input.wppoppop_stage change.wppoppop_stage', function() {
                var el = resolveEl();
                if (el) {
                    el.videoUrl = $('#prop-video-url').val();
                    el.videoAutoplay = $('#prop-video-autoplay').is(':checked');
                    el.videoMute = $('#prop-video-mute').is(':checked');
                    el.videoControls = $('#prop-video-controls').is(':checked');
                    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            // Shape Rotation Matrix Synchronization
            $('#prop-shape-rotate').off('input.wppoppop_stage change.wppoppop_stage').on('input.wppoppop_stage change.wppoppop_stage', function() {
                var el = resolveEl();
                if (el) {
                    var deg = parseInt($(this).val(), 10) || 0;
                    el.shapeRotate = deg;
                    el.rotate = deg;
                    var $node = $('#el-' + el.id);
                    if ($node.length) {
                        $node.css('transform', 'rotate(' + deg + 'deg)');
                    }
                }
            });

            // Progress Bar Percentage Slider Synchronization
            $('#prop-progress-val').off('input.wppoppop_stage change.wppoppop_stage').on('input.wppoppop_stage change.wppoppop_stage', function() {
                var el = resolveEl();
                if (el) {
                    var p = parseInt($(this).val(), 10) || 0;
                    el.progressVal = p;
                    $('#prop-progress-val-num').text(p);
                    var $node = $('#el-' + el.id);
                    if ($node.length) {
                        $node.find('.wppoppop-progress-bar').css('width', p + '%');
                        $node.find('span').text(p + '%');
                    }
                }
            });

            // Slider Bounds Synchronization
            $('#prop-slider-min, #prop-slider-max, #prop-slider-step, #prop-slider-default').off('input.wppoppop_stage change.wppoppop_stage').on('input.wppoppop_stage change.wppoppop_stage', function() {
                var el = resolveEl();
                if (el) {
                    el.sliderMin = parseFloat($('#prop-slider-min').val()) || 0;
                    el.sliderMax = parseFloat($('#prop-slider-max').val()) || 100;
                    el.sliderStep = parseFloat($('#prop-slider-step').val()) || 1;
                    el.sliderDefault = parseFloat($('#prop-slider-default').val()) || 0;
                    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            // Rating Stars & Color Synchronization
            $('#prop-rating-color, #prop-rating-max').off('input.wppoppop_stage change.wppoppop_stage').on('input.wppoppop_stage change.wppoppop_stage', function() {
                var el = resolveEl();
                if (el) {
                    el.ratingColor = $('#prop-rating-color').val();
                    el.ratingMax = parseInt($('#prop-rating-max').val(), 10) || 5;
                    if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

        var self = this;
        // Close Pop Element Live Bindings
        $('#prop-close-action').off('change.wppoppopClose').on('change.wppoppopClose', function() {
            var val = $(this).val();
            var activeEl = self.getActiveElement();
        this.hydrateStyleLogicFields(activeEl);
            if (activeEl) {
                activeEl.closeAction = val;
                activeEl.close_action = val;
                if (val === 'redirect') {
                    $('#prop-close-redirect-wrap').slideDown(150);
                } else {
                    $('#prop-close-redirect-wrap').slideUp(150);
                }
            }
        });

        $('#prop-close-icon-style').off('change.wppoppopClose').on('change.wppoppopClose', function() {
            var val = $(this).val();
            var activeEl = self.getActiveElement();
            if (activeEl) {
                activeEl.closeIconStyle = val;
                activeEl.close_icon_style = val;
                if (val === 'custom' || val === 'text') {
                    $('#prop-close-text-wrap').slideDown(150);
                    if (!activeEl.closeText) {
                        activeEl.closeText = 'CLOSE';
                        $('#prop-close-text').val('CLOSE');
                    }
                    activeEl.content = activeEl.closeText;
                } else {
                    $('#prop-close-text-wrap').slideUp(150);
                    activeEl.content = (val === 'dashicon') ? 'dashicons-no-alt' : '×';
                }
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            }
        });

        $('#prop-close-text').off('input.wppoppopClose change.wppoppopClose').on('input.wppoppopClose change.wppoppopClose', function() {
            var val = $(this).val();
            var activeEl = self.getActiveElement();
            if (activeEl) {
                activeEl.closeText = val;
                activeEl.close_text = val;
                activeEl.content = val;
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            }
        });

        $('#prop-close-redirect-url').off('input.wppoppopClose change.wppoppopClose').on('input.wppoppopClose change.wppoppopClose', function() {
            var val = $(this).val();
            var activeEl = self.getActiveElement();
            if (activeEl) {
                activeEl.closeRedirectUrl = val;
                activeEl.close_redirect_url = val;
            }
        });
        var self = this;

        // Canonical Aliased Inputs Synchronization
        $('#prop-dropdown-options, #prop-select-options, #prop-select-fieldname, #prop-dropdown-fieldname').off('input change').on('input change', function() {
            var el = (typeof self.getActiveEl === 'function' ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null));
            if (el && (el.type === 'dropdown' || el.type === 'select')) {
                var val = $('#prop-dropdown-options').length ? $('#prop-dropdown-options').val() : $('#prop-select-options').val();
                el.content = val;
                el.options = val ? val.split(',').map(function(s){ return s.trim(); }) : [];
                el.field_name = $('#prop-dropdown-fieldname').val() || $('#prop-select-fieldname').val() || 'dropdown_field';
                if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
            }
        });

        $('#prop-step-label, #prop-step-canvas').off('input change').on('input change', function() {
            var el = (typeof self.getActiveEl === 'function' ? self.getActiveEl() : (typeof self.getActiveElement === 'function' ? self.getActiveElement() : null));
            if (el && (el.type === 'step' || el.type === 'step_btn')) {
                el.content = $('#prop-step-label').val();
                var cNum = parseInt($('#prop-step-canvas').val(), 10) || 2;
                el.goto_canvas = cNum;
                el.goto_screen = cNum;
                if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
            }
        });

        this.bindAdvancedStylingInputs();
            var self = this;

            // Shape Presets & Controls
            $(document).on('change', '#prop-shape-preset', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.shapePreset = $(this).val();
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-shape-fill', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.shapeFill = $(this).val();
                $('#prop-shape-fill-transparent-btn').removeClass('active');
                self.syncToCanvas();
            });

            $(document).on('click', '#prop-shape-fill-transparent-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.shapeFill = 'transparent';
                $(this).addClass('active');
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-shape-stroke', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.shapeStroke = $(this).val();
                $('#prop-shape-stroke-transparent-btn').removeClass('active');
                self.syncToCanvas();
            });

            $(document).on('click', '#prop-shape-stroke-transparent-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.shapeStroke = 'transparent';
                $(this).addClass('active');
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-shape-stroke-width', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.shapeStrokeWidth = parseInt($(this).val(), 10) || 0;
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-shape-rotate', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.shapeRotate = parseInt($(this).val(), 10) || 0;
                self.syncToCanvas();
            });

            // Content & Text
            $(document).on('input', '#prop-content, #prop-title-text, #prop-paragraph-text, #prop-textfield-placeholder', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.content = $(this).val();
                self.syncToCanvas();
            });

            // Layer Name
            $(document).on('input', '#prop-name', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.name = $(this).val();
                if (window.WpPopPopBuilderLayers) window.WpPopPopBuilderLayers.renderLayers();
            });

            // Dimensions and Coordinates
            $(document).on('input change', '#prop-left, #prop-top, #prop-width, #prop-height', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.left = parseInt($('#prop-left').val(), 10) || 0;
                el.top = parseInt($('#prop-top').val(), 10) || 0;
                el.width = parseInt($('#prop-width').val(), 10) || 50;
                el.height = parseInt($('#prop-height').val(), 10) || 30;
                self.syncToCanvas();
            });

            // Typography & Spacing Inputs
            $(document).on('input change', '#prop-font-size', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.fontSize = parseInt($(this).val(), 10) || 14;
                self.syncToCanvas();
            });

            $(document).on('change', '#prop-font-weight', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.fontWeight = $(this).val();
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-line-height', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.lineHeight = parseFloat($(this).val()) || 1.4;
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-letter-spacing', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.letterSpacing = parseFloat($(this).val()) || 0;
                self.syncToCanvas();
            });

            $(document).on('change', '#prop-text-align', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.textAlign = $(this).val();
                self.syncToCanvas();
            });

            // Element Padding Input
            $(document).on('input change', '#prop-padding', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.padding = Math.max(0, parseInt($(this).val(), 10) || 0);
                self.syncToCanvas();
            });

            // Animation Selector & Play Button
            
            $('#prop-padding-top, #prop-padding-right, #prop-padding-bottom, #prop-padding-left').off('input.livePad change.livePad').on('input.livePad change.livePad', function() {
                var el = (typeof self.getActiveElement === 'function') ? self.getActiveElement() : ((typeof getActiveEl === 'function') ? getActiveEl() : null);
                if (!el && window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state.activeId) {
                    var cur = window.WpPopPopBuilderCore.state.currentCanvas || 1;
                    var list = window.WpPopPopBuilderCore.state.canvases[cur] || [];
                    el = list.find(function(item) { return String(item.id) === String(window.WpPopPopBuilderCore.state.activeId); });
                }
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }


                var dir = $(this).attr('data-dir') || $(this).attr('id').replace('prop-padding-', '');
                var val = Math.max(0, parseInt($(this).val(), 10) || 0);

                if (dir === 'top') { el.paddingTop = val; el.padding_top = val; }
                if (dir === 'right') { el.paddingRight = val; el.padding_right = val; }
                if (dir === 'bottom') { el.paddingBottom = val; el.padding_bottom = val; }
                if (dir === 'left') { el.paddingLeft = val; el.padding_left = val; }
                el.padding = el.paddingTop || 0;
                $('#prop-padding').val(el.padding);

                var $target = $('#el-' + el.id);
                if ($target.length) {
                    var isControl = ['step_btn', 'submit', 'pay', 'link_btn', 'textfield', 'email', 'number', 'date', 'select'].indexOf(el.type) !== -1;
                    var $inner = $target.find('input, button, select, textarea, a, .wppoppop-btn').first();
                    if (isControl && $inner.length) {
                        $target.css({ 'padding': '0px', 'box-sizing': 'border-box' });
                        $inner.css('padding-' + dir, val + 'px').css('box-sizing', 'border-box');
                    } else {
                        $target.css('padding-' + dir, val + 'px').css('box-sizing', 'border-box');
                    }
                }
                if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });

            $(document).on('change', '#prop-animation', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.animation = $(this).val();
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.playElementAnimation(el.id, el.animation);
                }
                self.syncToCanvas();
            });

            $(document).on('change', '#prop-anim-disappearance', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.anim_disappearance = $(this).val();
                el.exitAnimation = el.anim_disappearance;
                if (window.WpPopPopBuilderCore) window.WpPopPopBuilderCore.pushHistory();
            });

            $(document).on('click', '#prop-anim-play-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                var anim = $('#prop-animation').val() || el.animation || 'fade';
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.playElementAnimation(el.id, anim);
                }
            });

            // Colors
            $(document).on('input change', '#prop-color', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.color = $(this).val();
                $('#prop-text-transparent-btn').removeClass('active');
                self.syncToCanvas();
            });

            $(document).on('click', '#prop-text-transparent-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.color = 'transparent';
                $(this).addClass('active');
                self.syncToCanvas();
            });

            $(document).on('input change', '#prop-bg-color', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.bgColor = $(this).val();
                $('#prop-bg-transparent-btn').removeClass('active');
                self.syncToCanvas();
            });

            $(document).on('click', '#prop-bg-transparent-btn', function(e) {
                e.preventDefault();
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                el.bgColor = 'transparent';
                $(this).addClass('active');
                self.syncToCanvas();
            });

            // Target Canvas Navigation
            $(document).on('change', '#prop-goto-canvas', function() {
                var el = self.getActiveElement();
                if (!el) return;

        /* Title Element Hydration */
        if ((el.type || '').toLowerCase() === 'title') {
            var rawTitle = el.titleText || el.text || el.content || 'Headline Title';
            var rawTag   = el.headingTag || el.titleTag || el.tag || 'h2';
            var rawTrans = el.textTransform || el.text_transform || 'none';
            $('#prop-title-text').val(rawTitle);
            $('#prop-title-tag').val(rawTag);
            $('#prop-title-transform').val(rawTrans);
        }


        /* Phase 2: Hydrate Opacity & Hover */
        var opVal = (typeof el.opacity !== 'undefined') ? parseFloat(el.opacity) : 1;
        if (opVal <= 1) opVal = Math.round(opVal * 100);
        $('#prop-opacity').val(opVal);
        $('#prop-opacity-val').text(opVal + '%');

        $('#prop-custom-class').val(el.customClass || el.custom_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_id || '');

        var btnTypes = ['submit', 'link', 'step', 'pay', 'submit_btn', 'link_btn', 'step_btn', 'pay_btn', 'button'];
        var isButton = btnTypes.indexOf((el.type || '').toLowerCase()) >= 0;
        if (isButton) {
            $('#prop-btn-hover-section').show();
            $('#prop-btn-hover-bg').val(el.hoverBgColor || el.hover_bg_color || '');
            $('#prop-btn-hover-color').val(el.hoverTextColor || el.hover_text_color || '');
            $('#prop-btn-hover-border').val(el.hoverBorderColor || el.hover_border_color || '');
        } else {
            $('#prop-btn-hover-section').hide();
        }

                var target = parseInt($(this).val(), 10) || 2;
                el.goto_canvas = target;
                el.goto_screen = target;
                self.syncToCanvas();
            });
        }
    };
})(jQuery);

/* === WPPOPPOP_SELF_CONTAINED_5TAB_ENGINE_START === */
(function(window, $) {
    'use strict';

    var MEDIA_FAMILY_3_TABS = [
        'shape', 'rectangle', 'square',
        'image', 'video',
        'label', 'title', 'paragraph',
        'html', 'close', 'close_icon',
        'icon', 'fontawesome', 'progress',
        'button', 'link', 'link_btn', 'step_btn',
        'submit'
    ];

    var FORM_FAMILY_5_TABS = [
        'text', 'textfield', 'text_field',
        'email', 'number', 'phone', 'tel',
        'textarea', 'select', 'dropdown',
        'checkbox', 'checkboxes', 'radio', 'radios',
        'multiselect', 'imageselect', 'tile',
        'date', 'time', 'upload', 'file',
        'password', 'rating'
    ];

    function getCanonicalFamily(rawType) {
        var t = String(rawType || '').toLowerCase().trim();
        if (FORM_FAMILY_5_TABS.indexOf(t) !== -1) {
            return 'form';
        }
        return 'media';
    }

    function resolveActiveElement(idOrEl) {
        if (idOrEl && typeof idOrEl === 'object' && idOrEl.type) {
            return idOrEl;
        }
        var insp = window.WpPopPopBuilderInspector;
        var rawId = (idOrEl !== undefined && idOrEl !== null && typeof idOrEl !== 'object')
            ? String(idOrEl)
            : (insp && insp.activeElementId ? String(insp.activeElementId) : (
                window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? String(window.WpPopPopBuilderCore.state.activeId || '') : ''
            ));
        var cleanId = rawId.replace(/^wppoppop-el-/, '').replace(/^el-/, '');

        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') {
            var els = window.WpPopPopBuilderCanvas.getActiveElements() || [];
            if (Array.isArray(els)) {
                var found = els.find(function(e) {
                    return e && (String(e.id) === cleanId || String(e.id) === rawId);
                });
                if (found) return found;
            }
        }
        var $sel =$('.wppoppop-canvas-item.selected, .wppoppop-canvas-item.wppoppop-selected, .wppoppop-element.selected').first();
        if ($sel.length) {
            return {
                id: $sel.attr('data-id') || cleanId,
                type: $sel.attr('data-type') ||$sel.attr('data-element-type') || ''
            };
        }
        return null;
    }

    function ensureFiveTabHeaderDOM() {
        var $logicBtn =$('#wppoppop-inspector-drawer [data-tab="logic"]').first();
        if (!$logicBtn.length) return;
        var $bar =$logicBtn.parent();
        var cls = ($logicBtn.attr('class') || 'wppoppop-insp-tab').replace(/\bactive\b/g, '').trim();

        if (!$bar.find('[data-tab="data"]').length) {
            $('<button type="button" id="insp-tab-nav-data" data-tab="data">DATA</button>')
                .attr('class', cls + ' wppoppop-form-family-tab')
                .insertAfter($logicBtn);
        }
        if (!$bar.find('[data-tab="advanced"]').length) {
            $('<button type="button" id="insp-tab-nav-advanced" data-tab="advanced">ADVANCED</button>')
                .attr('class', cls + ' wppoppop-form-family-tab')
                .insertAfter($bar.find('[data-tab="data"]'));
        }
    }

    function renderBadgePills(selector, list, kind) {
        var $box =$(selector);
        if (!$box.length) return;
        $box.empty();
        (Array.isArray(list) ? list : []).forEach(function(item) {
            $('<span class="wppoppop-adv-badge" />')
                .attr('data-kind', kind)
                .attr('data-val', item)
                .css({
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '4px',
                    padding: '3px 9px',
                    fontSize: '11px',
                    fontWeight: '700',
                    color: '#fff',
                    background: '#c2185b',
                    borderRadius: '12px',
                    cursor: 'pointer'
                })
                .text(item + ' ×')
                .appendTo($box);
        });
    }

    function applyFamilyRouting(elOrType) {
        ensureFiveTabHeaderDOM();
        var el = (elOrType && typeof elOrType === 'object') ? elOrType : resolveActiveElement(elOrType);
        var type = (el && el.type) ? String(el.type).toLowerCase().trim() : (typeof elOrType === 'string' ? elOrType.toLowerCase().trim() : '');
        if (!type) {
            var layerTitle = String($('#prop-layer-name').val() || '').toLowerCase().trim();
            var m = layerTitle.match(/^([a-z_]+)/);
            if (m) type = m[1];
        }

        var family = getCanonicalFamily(type);
        var isForm = (family === 'form');

        // 1. Toggle 3-Tab (Media) vs 5-Tab (Form) in Header
        $('#wppoppop-inspector-drawer [data-tab="data"], #wppoppop-inspector-drawer [data-tab="advanced"]').each(function() {
            if (isForm) {
                this.style.setProperty('display', 'inline-flex', 'important');
                this.style.setProperty('align-items', 'center', 'important');
                this.style.setProperty('justify-content', 'center', 'important');
                this.style.setProperty('flex', '1 1 0', 'important');
                this.style.setProperty('min-width', '0', 'important');
            } else {
                this.style.setProperty('display', 'none', 'important');
            }
        });

        // 2. Ensure Element 10 (`text` / `textfield`) shows its decomposed Form Input panel
        if (type === 'text' || type === 'textfield' || type === 'text_field') {
            $('#panel-elem-paragraph-legacy, #panel-elem-paragraph, #panel-elem-title').hide();
            $('#panel-elem-text, #panel-elem-textfield').first().show();
        }

        // 3. Auto-fallback to Content tab if switching from Form (on Data/Advanced) to Media
        if (!isForm) {
            var $act =$('#wppoppop-inspector-drawer [data-tab].active');
            var curTab = $act.attr('data-tab') || '';
            if (curTab === 'data' || curTab === 'advanced') {
                $('#wppoppop-inspector-drawer [data-tab="content"], #wppoppop-inspector-drawer [data-tab="basic"]').first().trigger('click');
            }
        }

        // 4. Hydrate Form Family Data & Advanced tabs
        if (el && isForm) {
            if (!Array.isArray(el.filters)) el.filters = ['trim'];
            if (!Array.isArray(el.validators)) {
                el.validators = (type === 'email') ? ['email'] : [];
                if (el.required && el.validators.indexOf('required') === -1) el.validators.push('required');
            }
            $('#prop-data-default').val(el.defaultValue || '');
            $('#prop-data-dynamic-default').prop('checked', !!el.dynamicDefault);
            $('#prop-data-param-name').val(el.paramName || '');
            $('#prop-data-param-wrap').toggle(!!el.dynamicDefault);
            $('#prop-data-readonly').prop('checked', !!el.readOnly);
            $('#prop-data-save-db').prop('checked', el.saveToDb !== false);

            $('#prop-adv-field-id').val(el.fieldId || el.fieldName || el.id || '');
            $('#prop-adv-input-mask').val(el.inputMask || '');
            renderBadgePills('#wppoppop-adv-filters-list', el.filters, 'filter');
            renderBadgePills('#wppoppop-adv-validators-list', el.validators, 'validator');
        }
    }

    function hookInspectorLifecycle() {
        var insp = window.WpPopPopBuilderInspector;
        if (!insp || insp._pureDecomposedHooked) return;
        insp._pureDecomposedHooked = true;

        var origOpen = insp.open;
        insp.open = function(id) {
            var res = typeof origOpen === 'function' ? origOpen.apply(this, arguments) : undefined;
            applyFamilyRouting(id);
            setTimeout(function() { applyFamilyRouting(id); }, 20);
            return res;
        };

        var origShowPanel = insp.showElementPanel;
        if (typeof origShowPanel === 'function') {
            insp.showElementPanel = function(type) {
                var res = origShowPanel.apply(this, arguments);
                applyFamilyRouting(type);
                return res;
            };
        }

        var origSwitchTab = insp.switchTab;
        insp.switchTab = function(tab) {
            var t = String(tab || 'content').toLowerCase();
            if (typeof origSwitchTab === 'function') {
                origSwitchTab.apply(this, arguments);
            }
            if (t === 'data' || t === 'advanced') {
                $('#wppoppop-inspector-drawer [data-tab]').removeClass('active');
                $('#wppoppop-inspector-drawer [data-tab="' + t + '"]').addClass('active');
                $('#wppoppop-inspector-drawer .wppoppop-insp-content, #wppoppop-inspector-drawer .wppoppop-insp-tab-content, #wppoppop-inspector-drawer .wppoppop-tab-pane, #insp-tab-basic, #insp-tab-content, #insp-tab-settings, #insp-tab-style, #insp-tab-logic, #insp-tab-data, #insp-tab-advanced').removeClass('active').hide();
                $('#insp-tab-' + t).addClass('active').show();
            } else {
                $('#insp-tab-data, #insp-tab-advanced').removeClass('active').hide();
            }
        };

        $(document).off('click.wppoppopPureTab').on('click.wppoppopPureTab', '#wppoppop-inspector-drawer [data-tab]', function(e) {
            var tabKey = $(this).attr('data-tab');
            if (tabKey === 'data' || tabKey === 'advanced') {
                e.preventDefault();
                e.stopPropagation();
                insp.switchTab(tabKey);
            } else {
                $('#insp-tab-data, #insp-tab-advanced').removeClass('active').hide();
            }
        });

        $(document).off('click.wppoppopPureSelect').on('click.wppoppopPureSelect', '.wppoppop-ribbon-btn, .wppoppop-ribbon-tool, .wppoppop-canvas-item, .wppoppop-layer-item', function() {
            var t = $(this).attr('data-type') ||$(this).data('type') || '';
            applyFamilyRouting(t);
            setTimeout(function() { applyFamilyRouting(t); }, 30);
        });
    }

    $(function() {
        hookInspectorLifecycle();
        setTimeout(hookInspectorLifecycle, 100);
    });
    hookInspectorLifecycle();
})(window, jQuery);
/* === WPPOPPOP_SELF_CONTAINED_5TAB_ENGINE_END === */



/* === WPPOPPOP_PHASE3_FORM_CORE_MODULES_START === */
(function(window, $) {
    'use strict';

        var FORM_CORE_PANEL_MAP = {
        'text': '#panel-elem-text',
        'textfield': '#panel-elem-text',
        'text_field': '#panel-elem-text',
        'email': '#panel-elem-email',
        'number': '#panel-elem-number',
        'phone': '#panel-elem-phone',
        'tel': '#panel-elem-phone',
        'textarea': '#panel-elem-textarea',
        'select': '#panel-elem-select',
        'dropdown': '#panel-elem-select',
        'checkbox': '#panel-elem-checkbox',
        'checkboxes': '#panel-elem-checkbox',
        'radio': '#panel-elem-radio',
        'radios': '#panel-elem-radio',
        'multiselect': '#panel-elem-multiselect',
        'imageselect': '#panel-elem-imageselect',
        'tile': '#panel-elem-tile',
        'date': '#panel-elem-date',
        'time': '#panel-elem-time',
        'upload': '#panel-elem-upload',
        'file': '#panel-elem-upload',
        'password': '#panel-elem-password',
        'rating': '#panel-elem-rating'
    };

    function getActiveFormElement(idOrObj) {
        if (idOrObj && typeof idOrObj === 'object' && idOrObj.type) return idOrObj;
        var insp = window.WpPopPopBuilderInspector;
        var rawId = (idOrObj !== undefined && idOrObj !== null && typeof idOrObj !== 'object')
            ? String(idOrObj)
            : (insp && insp.activeElementId ? String(insp.activeElementId) : (
                window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? String(window.WpPopPopBuilderCore.state.activeId || '') : ''
            ));
        var cleanId = rawId.replace(/^wppoppop-el-/, '').replace(/^el-/, '');
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') {
            var els = window.WpPopPopBuilderCanvas.getActiveElements() || [];
            if (Array.isArray(els)) {
                var found = els.find(function(e) {
                    return e && (String(e.id) === cleanId || String(e.id) === rawId);
                });
                if (found) return found;
            }
        }
        return null;
    }

    function hydrateFormCoreModule(elOrType) {
        var el = (elOrType && typeof elOrType === 'object') ? elOrType : getActiveFormElement(elOrType);
        var type = (el && el.type) ? String(el.type).toLowerCase().trim() : (typeof elOrType === 'string' ? elOrType.toLowerCase().trim() : '');
        if (!type) return;

        var targetPanel = FORM_CORE_PANEL_MAP[type];
        if (!targetPanel) return;

        // Hide all legacy inline panels and display only the standalone decomposed module
        $('[id$="-legacy"]').hide();
        $('.element-panel, .wppoppop-panel-specific').hide();$(targetPanel).show();

        // Ensure Typography & Input Icons are visible in Style Tab for Form Inputs, while hiding Button Hover
        $('#prop-line-height, #prop-letter-spacing').closest('.wppoppop-insp-row, .wppoppop-field-row, div').show();
        $('#prop-btn-hover-section, #prop-btn-hover-bg').closest('.wppoppop-insp-card, div').hide();

        if (!el) return;

        // Hydrate `.wppoppop-gp-bind` inputs inside the active standalone Form Core panel
        $(targetPanel).find('.wppoppop-gp-bind').each(function() {
            var $inp =$(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            var val = el[prop];
            if (val === undefined) {
                if (prop === 'placeholder') val = el.content || '';
                if (prop === 'fieldName') val = el.token || el.fieldId || type;
                if (prop === 'options') val = Array.isArray(el.options) ? el.options.join(', ') : (el.options || 'Option 1, Option 2');
                if (prop === 'min') val = el.minValue !== undefined ? el.minValue : 0;
                if (prop === 'max') val = el.maxValue !== undefined ? el.maxValue : 100;
                if (prop === 'step') val = el.stepValue !== undefined ? el.stepValue : 1;
            } else if (prop === 'options' && Array.isArray(val)) {
                val = val.join(', ');
            }
            if ($inp.is(':checkbox')) {$inp.prop('checked', !!val);
            } else if (val !== undefined && val !== null) {
                $inp.val(val);
            }
        });
    }

    function bindFormCoreModuleEvents() {
        $(document).off('.wppoppopFormCoreModules').on(
            'input.wppoppopFormCoreModules change.wppoppopFormCoreModules',
            '[data-elem-family="form"] .wppoppop-gp-bind',
            function() {
                var el = getActiveFormElement();
                if (!el) return;
                var $inp =$(this);
                var prop = $inp.data('prop');
                if (!prop) return;
                var val = $inp.is(':checkbox') ? $inp.is(':checked') :$inp.val();
                el[prop] = val;

                // Keep aliases & 5-Tab Data/Advanced properties synchronized
                if (prop === 'placeholder') {
                    el.content = val;
                } else if (prop === 'fieldName') {
                    el.token = val;
                    el.fieldId = val;
                    $('#prop-adv-field-id, #prop-data-token').val(val);
                } else if (prop === 'inputMask') {
                    $('#prop-adv-input-mask').val(val);
                } else if (prop === 'min') {
                    el.minValue = val;
                } else if (prop === 'max') {
                    el.maxValue = val;
                } else if (prop === 'step') {
                    el.stepValue = val;
                } else if (prop === 'required') {
                    el.validators = Array.isArray(el.validators) ? el.validators : [];
                    if (val && el.validators.indexOf('required') === -1) {
                        el.validators.push('required');
                    } else if (!val) {
                        el.validators = el.validators.filter(function(x) { return x !== 'required'; });
                    }
                    if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.syncFamilyTabs === 'function') {
                        window.WpPopPopBuilderInspector.syncFamilyTabs(el);
                    }
                }

                if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            }
        );

        var insp = window.WpPopPopBuilderInspector;
        if (insp && !insp._phase3FormCoreHooked) {
            insp._phase3FormCoreHooked = true;
            var origOpen = insp.open;
            insp.open = function(id) {
                var res = typeof origOpen === 'function' ? origOpen.apply(this, arguments) : undefined;
                hydrateFormCoreModule(id);
                setTimeout(function() { hydrateFormCoreModule(id); }, 25);
                return res;
            };
            var origShow = insp.showElementPanel;
            if (typeof origShow === 'function') {
                insp.showElementPanel = function(type) {
                    var res = origShow.apply(this, arguments);
                    hydrateFormCoreModule(type);
                    return res;
                };
            }
        }

        $(document).off('click.wppoppopFormCoreSelect').on(
            'click.wppoppopFormCoreSelect',
            '.wppoppop-ribbon-btn, .wppoppop-ribbon-tool, .wppoppop-canvas-item, .wppoppop-layer-item',
            function() {
                var t = $(this).attr('data-type') ||$(this).data('type') || '';
                setTimeout(function() { hydrateFormCoreModule(t); }, 30);
            }
        );
    }

    $(function() {
        bindFormCoreModuleEvents();
        setTimeout(bindFormCoreModuleEvents, 120);
    });
    bindFormCoreModuleEvents();
})(window, jQuery);
/* === WPPOPPOP_PHASE3_FORM_CORE_MODULES_END === */

/* === WPPOPPOP_PHASE3_FORM_DECOMPOSED_MODULES_START === */
(function(window, $) {
    'use strict';

    var FORM_PANEL_MAP = {
        'text': '#panel-elem-text',
        'textfield': '#panel-elem-text',
        'text_field': '#panel-elem-text',
        'email': '#panel-elem-email',
        'number': '#panel-elem-number',
        'spinner': '#panel-elem-number',
        'phone': '#panel-elem-phone',
        'tel': '#panel-elem-phone',
        'textarea': '#panel-elem-textarea',
        'select': '#panel-elem-select',
        'dropdown': '#panel-elem-select',
        'checkbox': '#panel-elem-checkbox',
        'checkboxes': '#panel-elem-checkbox',
        'radio': '#panel-elem-radio',
        'radios': '#panel-elem-radio',
        'multiselect': '#panel-elem-multiselect',
        'imageselect': '#panel-elem-imageselect',
        'tile': '#panel-elem-tile',
        'date': '#panel-elem-date',
        'time': '#panel-elem-time',
        'upload': '#panel-elem-upload',
        'file': '#panel-elem-upload',
        'password': '#panel-elem-password',
        'rating': '#panel-elem-rating'
    };

    function getActiveFormStageElement(idOrObj) {
        if (idOrObj && typeof idOrObj === 'object' && idOrObj.type) return idOrObj;
        var insp = window.WpPopPopBuilderInspector;
        var rawId = (idOrObj !== undefined && idOrObj !== null && typeof idOrObj !== 'object')
            ? String(idOrObj)
            : (insp && insp.activeElementId ? String(insp.activeElementId) : (
                window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? String(window.WpPopPopBuilderCore.state.activeId || '') : ''
            ));
        var cleanId = rawId.replace(/^wppoppop-el-/, '').replace(/^el-/, '');
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') {
            var els = window.WpPopPopBuilderCanvas.getActiveElements() || [];
            if (Array.isArray(els)) {
                var found = els.find(function(e) {
                    return e && (String(e.id) === cleanId || String(e.id) === rawId);
                });
                if (found) return found;
            }
        }
        return null;
    }

    function hydrateFormModule(elOrType) {
        var el = (elOrType && typeof elOrType === 'object') ? elOrType : getActiveFormStageElement(elOrType);
        var type = (el && el.type) ? String(el.type).toLowerCase().trim() : (typeof elOrType === 'string' ? elOrType.toLowerCase().trim() : '');
        if (!type) return;

        var targetPanel = FORM_PANEL_MAP[type];
        if (!targetPanel) return;

        $('[id$="-legacy"]').hide();
        $('.element-panel, .wppoppop-panel-specific').hide();$(targetPanel).show();

        if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.sanitizeLayerInspectorForElement === 'function' && el) {
            window.WpPopPopBuilderInspector.sanitizeLayerInspectorForElement(el);
        }

        if (!el) return;

        $(targetPanel).find('.wppoppop-gp-bind').each(function() {
            var $inp =$(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            var val = el[prop];
            if ($inp.is(':checkbox')) {$inp.prop('checked', !!val);
            } else if (val !== undefined && val !== null) {
                $inp.val(val);
            }
        });
    }

    function bindFormModuleEvents() {
        $(document).off('.wppoppopFormModules').on('input.wppoppopFormModules change.wppoppopFormModules', '[data-elem-family="form"] .wppoppop-gp-bind', function() {
            var el = getActiveFormStageElement();
            if (!el) return;
            var $inp =$(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            var val = $inp.is(':checkbox') ? $inp.is(':checked') :$inp.val();
            el[prop] = val;

            if (prop === 'fieldName') {
                el.token = val;
                $('#prop-field-name, #prop-adv-id').val(val);
            }

            if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                window.WpPopPopBuilderCanvas.renderCanvas();
            }
        });

        var insp = window.WpPopPopBuilderInspector;
        if (insp && !insp._phase3FormHooked) {
            insp._phase3FormHooked = true;
            var origOpen = insp.open;
            insp.open = function(id) {
                var res = typeof origOpen === 'function' ? origOpen.apply(this, arguments) : undefined;
                hydrateFormModule(id);
                setTimeout(function() { hydrateFormModule(id); }, 25);
                return res;
            };
        }

        $(document).off('click.wppoppopFormSelect').on('click.wppoppopFormSelect', '.wppoppop-ribbon-btn, .wppoppop-ribbon-tool, .wppoppop-canvas-item, .wppoppop-layer-item', function() {
            var t = $(this).attr('data-type') ||$(this).data('type') || '';
            setTimeout(function() { hydrateFormModule(t); }, 30);
        });
    }

    $(function() {
        bindFormModuleEvents();
        setTimeout(bindFormModuleEvents, 120);
    });
    bindFormModuleEvents();
})(window, jQuery);
/* === WPPOPPOP_PHASE3_FORM_DECOMPOSED_MODULES_END === */




/* === WPPOPPOP_COORDS_ANIM_SANITY_PATCH_START === */
(function($){
    'use strict';
    var GUARD = '__wppoppop_coords_anim_sanity_v1';

    function enforceCoordsAnimVisibility(tab) {
        var $drawer = $('#wppoppop-inspector-drawer');
        if (!$drawer.length) return;
        var activeTab = (typeof tab === 'string') ? tab : ($drawer.find('.wppoppop-insp-tab.active').data('tab') || '');
        activeTab = String(activeTab || '').toLowerCase();

        var isBasic = (activeTab === 'basic' || activeTab === 'content');

        // Coordinates header -> grid detection (defensive)
        var $coordsHeader = $drawer.find('div').filter(function() {
            var t = $(this).text() || '';
            return (typeof t === 'string' && t.trim().toUpperCase().indexOf('COORDINATES') === 0);
        }).first();

        var $coordsGrid = $coordsHeader.length ? $coordsHeader.next('div').first() : $drawer.find('#prop-left').closest('div').parent();

        if ($coordsHeader.length) {
            if (isBasic) { $coordsHeader.show(); } else { $coordsHeader.hide(); }
        }
        if ($coordsGrid.length) {
            if (isBasic) { $coordsGrid.show(); } else { $coordsGrid.hide(); }
        }

        // Animation wrapper
        var $animWrap = $drawer.find('#wppoppop-element-anim-wrap');
        if ($animWrap.length) {
            if (isBasic) { $animWrap.show(); } else { $animWrap.hide(); }
        }

        // Also ensure input fields inside these sections cannot be accidentally visible
        var coordInputs = ['#prop-left','#prop-top','#prop-width','#prop-height'];
        coordInputs.forEach(function(sel){
            var $i = $drawer.find(sel);
            if ($i.length) {
                if (isBasic) { $i.closest('div').show(); } else { $i.closest('div').hide(); }
            }
        });
    }

    function attachSanityPatches() {
        if (!window.WpPopPopBuilderInspector) return;
        var insp = window.WpPopPopBuilderInspector;
        if (insp[GUARD]) return;
        insp[GUARD] = true;

        // Patch switchTab if present
        if (typeof insp.switchTab === 'function') {
            var origSwitch = insp.switchTab;
            insp.switchTab = function(tab) {
                var res = origSwitch.apply(this, arguments);
                try { enforceCoordsAnimVisibility(tab); } catch (e) { /* safe fail */ }
                return res;
            };
        }

        // Patch open to run enforcement after opening
        if (typeof insp.open === 'function') {
            var origOpen = insp.open;
            insp.open = function() {
                var res = origOpen.apply(this, arguments);
                try { setTimeout(function(){ enforceCoordsAnimVisibility($('.wppoppop-insp-tab.active').data('tab')); }, 20); } catch(e) {}
                return res;
            };
        }

        // Global tab-button click fallback
        $(document).on('click.wppoppop_coords_sanity', '.wppoppop-insp-tab', function(){
            var tab = $(this).data('tab') || '';
            try { setTimeout(function(){ enforceCoordsAnimVisibility(tab); }, 8); } catch(e) {}
        });

        // Run once immediately (in case inspector already open)
        $(function(){
            try { enforceCoordsAnimVisibility($('.wppoppop-insp-tab.active').data('tab')); } catch(e) {}
        });
    }

    // Defer attaching until DOM ready and inspector exists
    $(function(){
        attachSanityPatches();
        // try again shortly in case inspector is loaded after this script
        setTimeout(attachSanityPatches, 120);
    });

})(jQuery);
/* === WPPOPPOP_COORDS_ANIM_SANITY_PATCH_END === */



/* === WPPOPPOP_INSPECTOR_LAYOUT_FIX_START === */

(function($){
    'use strict';

    var GUARD = '__wppoppop_layout_fix_v1';

    function setTabRolesAndPanels() {
        var $drawer = $('#wppoppop-inspector-drawer');
        if (!$drawer.length) return;

        // Buttons -> role=tab, container -> role=tablist
        var $tabs = $drawer.find('.wppoppop-insp-tab');
        $tabs.attr('role', 'tab');
        $tabs.each(function(i){
            var $b = $(this);
            var tabKey = $b.data('tab') || ('tab-' + i);
            $b.attr('id', 'wppoppop-insp-tab-btn-' + tabKey);
            $b.attr('aria-controls', 'insp-tab-' + tabKey);
            $b.attr('aria-selected', $b.hasClass('active') ? 'true' : 'false');
            $b.attr('tabindex', $b.hasClass('active') ? '0' : '-1');
        });

        // Panes -> role=tabpanel and link them
        $drawer.find('.wppoppop-tab-pane').each(function(){
            var $p = $(this);
            var id = $p.attr('id') || '';
            if (!id) return;
            $p.attr('role', 'tabpanel');
            var tabKey = id.replace(/^insp-tab-/, '');
            $p.attr('id', id);
            var $btn = $drawer.find('.wppoppop-insp-tab[data-tab="' + tabKey + '"]').first();
            if ($btn.length) {
                $p.attr('aria-labelledby', $btn.attr('id'));
            }
            // Toggle active class and aria-hidden
            var isActive = $btn.length && $btn.hasClass('active');
            if (isActive) {
                $p.addClass('active').attr('aria-hidden', 'false');
            } else {
                $p.removeClass('active').attr('aria-hidden', 'true');
            }
        });
    }

    function enforceInspectorSizing() {
        var $drawer = $('#wppoppop-inspector-drawer');
        if (!$drawer.length) return;
        var headerHeight = $drawer.find('[style*="height:48px"], .wppoppop-insp-header').outerHeight() || 48;
        var topOffset = parseInt($drawer.css('top')) || 52;
        var avail = window.innerHeight - (topOffset + 8); // small gap
        $drawer.css('max-height', Math.max(200, avail) + 'px');
        // ensure panes have proper min-height logic to make flex's min-height:0 work cross-browser
        $drawer.find('.wppoppop-tab-pane, .wppoppop-insp-content').css({ 'min-height': 0, 'max-height': (avail - headerHeight) + 'px' });
    }

    function enforceCoordsAnimationVisibilityOnTab(tabKey) {
        // defensive: treat "basic" and "content" as same
        var key = (tabKey || $('.wppoppop-insp-tab.active').data('tab') || '').toString().toLowerCase();
        var isBasic = (key === 'basic' || key === 'content');
        var $drawer = $('#wppoppop-inspector-drawer');
        if (!$drawer.length) return;

        // animation wrapper
        var $anim = $drawer.find('#wppoppop-element-anim-wrap');
        if ($anim.length) {
            if (isBasic) $anim.show(); else $anim.hide();
        }

        // coordinates inputs - ensure visible only on basic
        var coords = ['#prop-left','#prop-top','#prop-width','#prop-height'];
        coords.forEach(function(sel){
            var $i = $drawer.find(sel);
            if ($i.length) {
                if (isBasic) {
                    $i.closest('div').show();
                } else {
                    $i.closest('div').hide();
                }
            }
        });

        // update aria-selected
        $drawer.find('.wppoppop-insp-tab').each(function(){
            var $b = $(this);
            var sel = ($b.data('tab') || '').toString().toLowerCase();
            $b.attr('aria-selected', (sel === key) ? 'true' : 'false');
            $b.attr('tabindex', (sel === key) ? '0' : '-1');
        });

        // update panes active/aria-hidden
        $drawer.find('.wppoppop-tab-pane').each(function(){
            var $p = $(this);
            var pid = $p.attr('id') || '';
            var pkey = pid.replace(/^insp-tab-/, '');
            if (pkey === key) {
                $p.addClass('active').attr('aria-hidden','false').show();
            } else {
                $p.removeClass('active').attr('aria-hidden','true').hide();
            }
        });
    }

    function attachLayoutHooks() {
        if (!window.WpPopPopBuilderInspector) return;
        var insp = window.WpPopPopBuilderInspector;
        if (insp && insp.__wpoppop_layout_fix_attached) return;
        if (insp) insp.__wpoppop_layout_fix_attached = true;

        // ensure roles/initial states
        setTabRolesAndPanels();
        enforceInspectorSizing();
        enforceCoordsAnimationVisibilityOnTab();

        // patch switchTab if exists
        if (typeof insp.switchTab === 'function') {
            var orig = insp.switchTab;
            insp.switchTab = function(tab) {
                var res = orig.apply(this, arguments);
                try { setTimeout(function(){ setTabRolesAndPanels(); enforceCoordsAnimationVisibilityOnTab(tab); enforceInspectorSizing(); }, 10); } catch(e){}
                return res;
            };
        }

        // patch open to recompute sizing & roles after open
        if (typeof insp.open === 'function') {
            var origOpen = insp.open;
            insp.open = function() {
                var res = origOpen.apply(this, arguments);
                try { setTimeout(function(){ setTabRolesAndPanels(); enforceCoordsAnimationVisibilityOnTab(); enforceInspectorSizing(); }, 20); } catch(e){}
                return res;
            };
        }

        // fallback binding for clicks on tabs
        $(document).off('click.wppoppop_inspector_layout').on('click.wppoppop_inspector_layout', '#wppoppop-inspector-drawer .wppoppop-insp-tab', function(e){
            var tab = $(this).data('tab') || '';
            setTimeout(function(){ setTabRolesAndPanels(); enforceCoordsAnimationVisibilityOnTab(tab); enforceInspectorSizing(); }, 8);
        });

        // window resize
        $(window).on('resize.wppoppop_inspector_layout', function(){ enforceInspectorSizing(); });

        // initial enforcement shortly after load (IDEMPOTENT)
        setTimeout(function(){ setTabRolesAndPanels(); enforceCoordsAnimationVisibilityOnTab(); enforceInspectorSizing(); }, 120);
    }

    // run when DOM ready; re-run shortly in case inspector loads late
    $(function(){
        attachLayoutHooks();
        setTimeout(attachLayoutHooks, 200);
    });

})(jQuery);
/* === WPPOPPOP_INSPECTOR_LAYOUT_FIX_END === */




/* === WPPOPPOP_LAYER_PANEL_SYNC_START === */

(function($){
    'use strict';

    // Helper: highlight layer item(s) in layer panels for a given element id
    function highlightLayerPanelItem(elementId) {
        if (!elementId) return;
        var selectors = [
            '#wppoppop-floating-layers-panel .wppoppop-layer-item',
            '#wppoppop-layers-list .wppoppop-layer-item',
            '.wppoppop-layer-item' // fallback
        ];
        selectors.forEach(function(sel){
            var $panel = $(sel).closest('div').first();
            if (!$panel.length) return;
        });

        // Remove active class from all known layer items, then add to matching
        $('.wppoppop-layer-item').removeClass('is-active wppoppop-layer-active inspector-layer-selected');
        $('[data-id="' + elementId + '"], [data-element-id="' + elementId + '"], [data-id="el-' + elementId + '"]').each(function(){
            $(this).addClass('is-active wppoppop-layer-active inspector-layer-selected');
            // ensure it is visible / scrolled into view inside layers pane(s)
            var $parent = $(this).closest('#wppoppop-layers-list, #wppoppop-floating-layers-panel, .wppoppop-layers-wrap');
            if ($parent.length) {
                try { $(this)[0].scrollIntoView({ block: 'nearest', behavior: 'auto' }); } catch(e){}
            }
        });
    }

    // Ensure inspector UI reflects the model values for the selected element
    function syncInspectorToElement(elementId) {
        if (!elementId || !window.WpPopPopBuilderInspector) return;
        var insp = window.WpPopPopBuilderInspector;
        // Resolve element object using existing helper if available
        var el = null;
        if (typeof insp.getActiveElement === 'function') {
            el = insp.getActiveElement();
            // getActiveElement may return null if state.activeId hasn't been updated yet, attempt fallback
            if (!el && window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
                var cur = window.WpPopPopBuilderCore.state.currentCanvas || 1;
                var list = window.WpPopPopBuilderCore.state.canvases ? (window.WpPopPopBuilderCore.state.canvases[cur] || []) : [];
                for (var i = 0; i < list.length; i++) {
                    if (String(list[i].id) === String(elementId) || String(list[i].id) === 'el-' + String(elementId)) {
                        el = list[i];
                        break;
                    }
                }
            }
        } else if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
            var cur2 = window.WpPopPopBuilderCore.state.currentCanvas || 1;
            var list2 = window.WpPopPopBuilderCore.state.canvases ? (window.WpPopPopBuilderCore.state.canvases[cur2] || []) : [];
            for (var j = 0; j < list2.length; j++) {
                if (String(list2[j].id) === String(elementId) || String(list2[j].id) === 'el-' + String(elementId)) {
                    el = list2[j]; break;
                }
            }
        }

        if (!el) return;

        // Use existing inspector methods if they exist to hydrate fields
        try {
            if (typeof insp.syncCoordinates === 'function') insp.syncCoordinates(el);
        } catch(e) {}

        try {
            if (typeof insp.hydrateStyleLogicFields === 'function') insp.hydrateStyleLogicFields(el);
        } catch(e) {}

        try {
            if (typeof insp.showElementSpecificSettings === 'function') insp.showElementSpecificSettings(el);
        } catch(e) {}

        // Additional hydration to ensure inputs mirrored (defensive)
        try {
            if ($('#prop-left').length) $('#prop-left').val(parseInt(el.left, 10) || 0);
            if ($('#prop-top').length) $('#prop-top').val(parseInt(el.top, 10) || 0);
            if ($('#prop-width').length) $('#prop-width').val(parseInt(el.width, 10) || 100);
            if ($('#prop-height').length) $('#prop-height').val(parseInt(el.height, 10) || 40);
            if ($('#prop-layer-name').length) $('#prop-layer-name').val(el.name || el.label || '');
            if ($('#prop-content').length) $('#prop-content').val(el.content || el.text || '');
        } catch(e) {}

        // ensure the inspector pushes history/state if needed
        try {
            if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                // do not create a history entry here, just ensure inspector state is consistent (no-op)
            }
        } catch(e){}
    }

    // Monkey-patch Inspector.open to also sync layer-panel highlight and inspector values
    function patchInspectorOpen() {
        if (!window.WpPopPopBuilderInspector) return;
        var insp = window.WpPopPopBuilderInspector;
        if (insp.__wppoppop_layer_panel_patch_applied) return;
        insp.__wppoppop_layer_panel_patch_applied = true;

        if (typeof insp.open === 'function') {
            var origOpen = insp.open;
            insp.open = function(id) {
                var res = origOpen.apply(this, arguments);
                try {
                    // element id normalization
                    var cleanId = String(id || (this.activeId || '')).replace(/^el-/, '').replace(/^wppoppop-el-/, '');
                    // highlight layer item(s)
                    setTimeout(function(){
                        highlightLayerPanelItem(cleanId);
                        syncInspectorToElement(cleanId);
                    }, 10);
                } catch(e){}
                return res;
            };
        }
    }

    // Ensure clicking a layer item selects it, highlights panel, and hydrates inspector
    function bindLayerPanelClicks() {
        $(document).off('click.wppoppop_layer_panel').on('click.wppoppop_layer_panel', '.wppoppop-layer-item', function(e){
            e.preventDefault();
            var $el = $(this);
            var id = $el.attr('data-id') || $el.attr('data-element-id') || $el.data('id') || $el.data('element-id') || '';
            if (!id) return;
            // normalize
            id = String(id).replace(/^el-/, '').replace(/^wppoppop-el-/, '');
            // highlight in panels
            highlightLayerPanelItem(id);
            // open inspector for this element if inspector exists
            if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.open === 'function') {
                try { window.WpPopPopBuilderInspector.open(id); } catch(e){}
            }
            // also ensure inspector input values sync after a short delay
            setTimeout(function(){ syncInspectorToElement(id); }, 20);
        });
    }

    $(function(){
        // attach patch and bindings on DOM ready and re-run shortly after to handle late loads
        try { patchInspectorOpen(); } catch(e){}
        try { bindLayerPanelClicks(); } catch(e){}
        setTimeout(function(){ try { patchInspectorOpen(); bindLayerPanelClicks(); } catch(e){} }, 120);
    });

})(jQuery);
/* === WPPOPPOP_LAYER_PANEL_SYNC_END === */

/* === WPPOPPOP_WORKSPACE_MEDIA_SELECT_AND_CHECKER_GUARD_START === */
(function(window, $) {
    'use strict';

    function extractCleanElementId($node) {
        if (!$node || !$node.length) return '';
        var raw = $node.attr('data-id') || $node.data('id') || $node.attr('data-element-id') || $node.attr('id') || '';
        return String(raw).replace(/^wppoppop-el-/, '').replace(/^el-/, '').replace(/^layer-/, '');
    }

    function selectAndInspectLayer(cleanId) {
        if (!cleanId) return;

        // 1. Update core state activeId
        if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
            window.WpPopPopBuilderCore.state.activeId = cleanId;
        }

        // 2. Highlight on Workspace Canvas (.wppoppop-canvas-item)
        $('.wppoppop-canvas-item').removeClass('selected is-selected active');
        var $stageItem = $('.wppoppop-canvas-item').filter(function() {
            return extractCleanElementId($(this)) === cleanId;
        });
        $stageItem.addClass('selected is-selected active');

        // 3. Highlight in Floating Layers Panel (.wppoppop-layer-item)
        $('.wppoppop-layer-item').removeClass('active is-active selected');
        var $layerRow = $('.wppoppop-layer-item').filter(function() {
            return extractCleanElementId($(this)) === cleanId;
        });
        $layerRow.addClass('active is-active selected');

        // 4. Allow jQuery UI draggable on button/media elements on Workspace
        $stageItem.each(function() {
            var $el = $(this);
            if ($el.data('ui-draggable') || $el.data('draggable')) {
                try { $el.draggable('option', 'cancel', '.ui-resizable-handle'); } catch (err) {}
            }
        });

        // 5. Close Campaign Settings drawer if open so drawers don't collide
        if (window.WpPopPopBuilderSettings && typeof window.WpPopPopBuilderSettings.closeDrawer === 'function') {
            window.WpPopPopBuilderSettings.closeDrawer();
        }
        $('#wppoppop-settings-drawer').removeClass('open is-open');
        $('#wppoppop-settings-backdrop').fadeOut(150);

        // 6. Slide in Layer Settings Inspector Drawer & Push Floating Layers Panel to right: 376px
        var $inspDrawer = $('#wppoppop-inspector-drawer');
        $inspDrawer.removeClass('is-closed').addClass('is-open open').css('display', 'flex');
        $('body, .wppoppop-builder-workspace, .wppoppop-main-frame').addClass('panel-open inspector-open');

        if (window.WpPopPopBuilderInspector) {
            window.WpPopPopBuilderInspector.activeElementId = cleanId;
            if (typeof window.WpPopPopBuilderInspector.open === 'function') {
                window.WpPopPopBuilderInspector.open(cleanId);
            }
        }
        if (window.WpPopPopElementsRegistry && typeof window.WpPopPopElementsRegistry.syncInspectorForElement === 'function') {
            window.WpPopPopElementsRegistry.syncInspectorForElement(cleanId);
        }
        if (window.WpPopPopBuilderLayers) {
            if (typeof window.WpPopPopBuilderLayers.pushForInspector === 'function') {
                window.WpPopPopBuilderLayers.pushForInspector(true);
            } else if (typeof window.WpPopPopBuilderLayers.pushLayerPanel === 'function') {
                window.WpPopPopBuilderLayers.pushLayerPanel(true);
            } else {
                $('#wppoppop-floating-layers-panel').css('right', '376px');
            }
        }
    }

    function bindWorkspaceMediaSelectionAndCheckerGuard() {
        // A. Direct Selection of ANY Media or Form Element on the Workspace Stage
        $(document).off('mousedown.gpStageSelect click.gpStageSelect', '.wppoppop-canvas-item')
                   .on('mousedown.gpStageSelect click.gpStageSelect', '.wppoppop-canvas-item', function(e) {
            if ($(e.target).hasClass('ui-resizable-handle') || $(e.target).closest('.ui-resizable-handle').length) {
                return;
            }
            e.stopPropagation();
            var cleanId = extractCleanElementId($(this));
            if (cleanId) {
                selectAndInspectLayer(cleanId);
            }
        });

        // B. Direct Selection from the Floating Layers Panel
        $(document).off('click.gpLayerRowSelect', '.wppoppop-layer-item')
                   .on('click.gpLayerRowSelect', '.wppoppop-layer-item', function(e) {
            if ($(e.target).closest('.wppoppop-layer-actions, .wppoppop-layer-btn, button, input').length) {
                return;
            }
            e.stopPropagation();
            var cleanId = extractCleanElementId($(this));
            if (cleanId) {
                selectAndInspectLayer(cleanId);
            }
        });

        // C. Stop clicks inside Campaign Settings Drawer (#wppoppop-settings-drawer)
        //    or Inspector Drawer (#wppoppop-inspector-drawer) from bubbling to the checker area
        $(document).off('mousedown.gpDrawerStop click.gpDrawerStop', '#wppoppop-settings-drawer, #wppoppop-inspector-drawer, #wppoppop-floating-layers-panel')
                   .on('mousedown.gpDrawerStop click.gpDrawerStop', '#wppoppop-settings-drawer, #wppoppop-inspector-drawer, #wppoppop-floating-layers-panel', function(e) {
            e.stopPropagation();
        });

        // D. Clicking on the Checker Area (or empty canvas background) closes Campaign Settings
        //    only if Campaign Settings was explicitly dismissed via its close button OR if clicking
        //    on the checker area when no settings input is being edited
        $(document).off('click.gpCheckerAreaClick', '.wppoppop-builder-workspace, #wppoppop-builder-workspace, #wppoppop-canvas-scroll-container, #wppoppop-canvas-box')
                   .on('click.gpCheckerAreaClick', '.wppoppop-builder-workspace, #wppoppop-builder-workspace, #wppoppop-canvas-scroll-container, #wppoppop-canvas-box', function(e) {
            var $t = $(e.target);
            // Ignore clicks on stage elements, resize handles, layers panel, or drawers
            if ($t.closest('.wppoppop-canvas-item, .ui-resizable-handle, #wppoppop-canvas-corner-handle, #wppoppop-floating-layers-panel, #wppoppop-inspector-drawer, #wppoppop-settings-drawer').length) {
                return;
            }

            // Deselect active stage element and slide out Layer Settings Inspector
            $('.wppoppop-canvas-item').removeClass('selected is-selected active');
            $('.wppoppop-layer-item').removeClass('active is-active selected');
            $('#wppoppop-inspector-drawer').removeClass('is-open open').addClass('is-closed');
            $('body, .wppoppop-builder-workspace, .wppoppop-main-frame').removeClass('panel-open inspector-open');

            if (window.WpPopPopBuilderLayers) {
                if (typeof window.WpPopPopBuilderLayers.pushForInspector === 'function') {
                    window.WpPopPopBuilderLayers.pushForInspector(false);
                } else {
                    $('#wppoppop-floating-layers-panel').css('right', '16px');
                }
            }
        });
    }

    $(function() {
        bindWorkspaceMediaSelectionAndCheckerGuard();
        setTimeout(bindWorkspaceMediaSelectionAndCheckerGuard, 150);
    });
    bindWorkspaceMediaSelectionAndCheckerGuard();
})(window, jQuery);
/* === WPPOPPOP_WORKSPACE_MEDIA_SELECT_AND_CHECKER_GUARD_END === */

/* === WPPOPPOP_UNIFIED_SELECTION_AND_POPULATION_SUITE_START === */
(function(window, $) {
    'use strict';

    var FORM_TYPES = [
        'text', 'textfield', 'email', 'number', 'spinner', 'phone', 'tel',
        'textarea', 'select', 'dropdown', 'checkbox', 'checkboxes',
        'radio', 'radios', 'multiselect', 'imageselect', 'tile',
        'date', 'time', 'upload', 'file', 'password', 'rating'
    ];

    var TEXT_BEARING_TYPES = [
        'label', 'title', 'paragraph', 'button', 'link', 'link_btn',
        'step', 'step_btn', 'submit', 'submit_btn', 'progress',
        'text', 'textfield', 'email', 'number', 'spinner', 'phone', 'tel', 'textarea'
    ];

    function normalizeId(raw) {
        if (raw === undefined || raw === null) return '';
        return String(raw)
            .replace(/^wppoppop-layer-/, '')
            .replace(/^layer-/, '')
            .replace(/^wppoppop-el-/, '')
            .replace(/^el-/, '')
            .trim();
    }

    function resolveElement(idOrObj) {
        if (idOrObj && typeof idOrObj === 'object' && idOrObj.type) return idOrObj;
        var rawId = (idOrObj !== undefined && idOrObj !== null && typeof idOrObj !== 'object')
            ? String(idOrObj)
            : (window.WpPopPopBuilderInspector && (window.WpPopPopBuilderInspector.activeElementId || window.WpPopPopBuilderInspector.activeId)
                ? String(window.WpPopPopBuilderInspector.activeElementId || window.WpPopPopBuilderInspector.activeId)
                : (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? String(window.WpPopPopBuilderCore.state.activeId || '') : ''));
        var cleanId = normalizeId(rawId);
        if (!cleanId) return null;

        // 1. Check canvas elements list
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') {
            var list = window.WpPopPopBuilderCanvas.getActiveElements() || [];
            for (var i = 0; i < list.length; i++) {
                if (list[i] && (String(list[i].id) === rawId || normalizeId(list[i].id) === cleanId)) {
                    return list[i];
                }
            }
        }

        // 2. Fallback to core state canvases
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.canvases) {
            var cur = core.state.currentCanvas || 1;
            var cData = core.state.canvases[cur];
            var arr = Array.isArray(cData) ? cData : (cData && Array.isArray(cData.elements) ? cData.elements : []);
            for (var j = 0; j < arr.length; j++) {
                if (arr[j] && (String(arr[j].id) === rawId || normalizeId(arr[j].id) === cleanId)) {
                    return arr[j];
                }
            }
        }
        return null;
    }

    function executeSwitchTab(tabName) {
        var target = String(tabName || 'basic').toLowerCase().trim();
        if (target === 'content') target = 'basic';

        var $tabsBar = $('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs');
        $tabsBar.find('.wppoppop-insp-tab, [data-tab]').removeClass('active');
        $tabsBar.find('[data-tab="' + target + '"]').addClass('active');

        var allTabs = ['basic', 'style', 'logic', 'data', 'advanced'];
        for (var i = 0; i < allTabs.length; i++) {
            var pane = document.getElementById('insp-tab-' + allTabs[i]);
            if (pane) {
                if (allTabs[i] === target) {
                    $(pane).addClass('active');
                    pane.style.setProperty('display', 'block', 'important');
                } else {
                    $(pane).removeClass('active');
                    pane.style.setProperty('display', 'none', 'important');
                }
            }
        }
    }

    function populateInspectorTabs(el) {
        if (!el) return;
        var type = String(el.type || '').toLowerCase().trim();
        var isForm = FORM_TYPES.indexOf(type) !== -1;
        var isText = TEXT_BEARING_TYPES.indexOf(type) !== -1;
        var isButton = ['button', 'link', 'link_btn', 'step', 'step_btn', 'submit', 'submit_btn'].indexOf(type) !== -1;

        // Toggle Data & Advanced tabs visibility
        var $tabsBar = $('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs');
        $tabsBar.find('[data-tab="data"], [data-tab="advanced"]').each(function() {
            this.style.setProperty('display', isForm ? 'inline-block' : 'none', 'important');
        });

        // Current active tab check
        var curTab = $tabsBar.find('.wppoppop-insp-tab.active').attr('data-tab') || 'basic';
        if (!isForm && (curTab === 'data' || curTab === 'advanced')) {
            executeSwitchTab('basic');
        } else {
            executeSwitchTab(curTab);
        }

        // ==========================================
        // 1. POPULATE STYLE TAB
        // ==========================================
        var op = (el.opacity !== undefined && el.opacity !== null && el.opacity !== '') ? parseFloat(el.opacity) : 100;
        if (op <= 1 && op > 0) op = Math.round(op * 100);
        $('#prop-opacity').val(op);
        $('#prop-opacity-val').text(op + '%');

        $('#prop-bg-color').val(el.bgColor || '#3b82f6');
        $('#prop-bg-transparent').prop('checked', !!el.transparentFill || el.bgColor === 'transparent' || (type === 'shape' && !el.bgColor));

        $('#insp-section-typography').toggle(isText);
        if (isText) {
            $('#prop-font-size').val(el.fontSize || 16);
            $('#prop-font-weight').val(el.fontWeight || 400);
            $('#prop-line-height').val(el.lineHeight || 1.4);
            $('#prop-letter-spacing').val(el.letterSpacing || 0);
            $('#prop-color').val(el.color || '#ffffff');
        }

        $('#prop-pad-top').val(el.paddingTop || 0);
        $('#prop-pad-right').val(el.paddingRight || 0);
        $('#prop-pad-bottom').val(el.paddingBottom || 0);
        $('#prop-pad-left').val(el.paddingLeft || 0);

        $('#prop-border-width').val(el.borderWidth || 0);
        $('#prop-border-style').val(el.borderStyle || 'solid');
        $('#prop-border-color').val(el.borderColor || '#334155');
        $('#prop-border-radius').val(el.borderRadius || 4);

        $('#prop-shadow-x').val(el.shadowX || 0);
        $('#prop-shadow-y').val(el.shadowY || 4);
        $('#prop-shadow-blur').val(el.shadowBlur || 12);
        $('#prop-shadow-spread').val(el.shadowSpread || 0);
        $('#prop-shadow-color').val(el.shadowColor || '#000000');

        $('#prop-btn-hover-section').toggle(isButton);
        if (isButton) {
            $('#prop-elem-hover-bg').val(el.hoverBgColor || '#0284c7');
            $('#prop-elem-hover-color').val(el.hoverTextColor || '#ffffff');
            $('#prop-elem-hover-border').val(el.hoverBorderColor || '#0284c7');
        }

        $('#wppoppop-form-style-module').toggle(isForm);
        if (isForm) {
            var align = el.textAlign || 'left';
            $('#wppoppop-form-align-group .wppoppop-form-align-btn')
                .removeClass('active button-primary')
                .filter('[data-align="' + align + '"]')
                .addClass('active button-primary');
            $('#prop-form-icon-left').val(el.iconLeft || '');
            $('#prop-form-icon-left-offset').val(el.iconLeftOffset || 10);
            $('#prop-form-icon-right').val(el.iconRight || '');
            $('#prop-form-icon-right-offset').val(el.iconRightOffset || 10);
            $('#prop-form-custom-class').val(el.customClass || '');
        }

        // ==========================================
        // 2. POPULATE DATA TAB
        // ==========================================
        $('#prop-default-value').val(el.defaultValue || '');
        $('#prop-dynamic-default').prop('checked', !!el.dynamicDefault);
        $('#prop-dynamic-param-row').toggle(!!el.dynamicDefault);
        $('#prop-dynamic-param').val(el.paramName || el.dynamicParam || '');
        $('#prop-readonly').prop('checked', !!el.readOnly);
        $('#prop-save-to-db').prop('checked', el.saveToDb !== false);

        // ==========================================
        // 3. POPULATE ADVANCED TAB
        // ==========================================
        $('#prop-field-name').val(el.fieldName || el.fieldId || '');
        $('#prop-input-mask').val(el.inputMask || '');
        $('#prop-validator-error').val(el.errorMessage || '');

        var filters = Array.isArray(el.filters) ? el.filters : [];
        $('.prop-filter-chk').each(function() {
            $(this).prop('checked', filters.indexOf($(this).val()) !== -1);
        });

        var validators = Array.isArray(el.validators) ? el.validators : [];
        if (el.required && validators.indexOf('required') === -1) {
            validators.push('required');
        }
        $('.prop-validator-chk').each(function() {
            $(this).prop('checked', validators.indexOf($(this).val()) !== -1);
        });
    }

    // ==========================================
    // GLOBAL AUTHORITATIVE SELECTION DISPATCHER
    // ==========================================
    window.selectLayerEverywhere = function(idOrObj) {
        var el = resolveElement(idOrObj);
        if (!el) return;
        var actualId = el.id;

        // 1. Highlight layer row in Layers Panel
        $('#wppoppop-layers-list .wppoppop-layer-item, #wppoppop-layers-list > li')
            .removeClass('active selected is-selected');
        $('#wppoppop-layers-list .wppoppop-layer-item[data-id="' + actualId + '"], #wppoppop-layers-list > [data-id="' + actualId + '"]')
            .addClass('active selected is-selected');

        // 2. Select on Canvas stage
        if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
            window.WpPopPopBuilderCore.state.activeId = actualId;
        }
        if (window.WpPopPopBuilderCanvas) {
            window.WpPopPopBuilderCanvas.selectedId = actualId;
            if (typeof window.WpPopPopBuilderCanvas.selectElement === 'function') {
                try { window.WpPopPopBuilderCanvas.selectElement(actualId); } catch(e) {}
            }
        }
        $('.wppoppop-canvas-item, .wppoppop-element').removeClass('wppoppop-selected selected is-selected');
        $('#wppoppop-el-' + actualId + ', #el-' + actualId + ', [data-id="' + actualId + '"]')
            .addClass('wppoppop-selected selected is-selected');

        // 3. Open Layer Settings Inspector
        if (window.WpPopPopBuilderInspector) {
            window.WpPopPopBuilderInspector.activeElementId = actualId;
            window.WpPopPopBuilderInspector.activeId = actualId;
            if (typeof window.WpPopPopBuilderInspector.open === 'function') {
                try { window.WpPopPopBuilderInspector.open(actualId); } catch(e) {}
            }
        }

        // 4. Force slide in and push layers panel
        var $drawer = $('#wppoppop-inspector-drawer');
        $drawer.css('display', 'flex').removeClass('is-closed').addClass('is-open open');
        $('body, .wppoppop-builder-workspace, .wppoppop-main-frame').addClass('panel-open inspector-open');

        if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.pushForInspector === 'function') {
            window.WpPopPopBuilderLayers.pushForInspector(true);
        }

        // 5. Populate and hydrate all tabs
        populateInspectorTabs(el);
    };

    function bindSuiteEvents() {
        // Direct Tab Clicking
        $(document)
            .off('click.wppoppopSuiteTabs')
            .on('click.wppoppopSuiteTabs', '#wppoppop-inspector-drawer .wppoppop-insp-tab, #wppoppop-inspector-tabs-bar [data-tab], .wppoppop-inspector-tabs [data-tab]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var tab = $(this).attr('data-tab') || $(this).data('tab') || 'basic';
                executeSwitchTab(tab);
            });

        // Layer Panel 1-Click Selection
        $(document)
            .off('click.wppoppopSuiteLayerSelect')
            .on('click.wppoppopSuiteLayerSelect', '#wppoppop-floating-layers-panel .wppoppop-layer-item, #wppoppop-layers-list .wppoppop-layer-item, #wppoppop-layers-list > li', function(e) {
                if ($(e.target).closest('.wppoppop-layer-action-btn, .wppoppop-layer-actions, .wppoppop-layer-vis, .wppoppop-layer-lock, .wppoppop-layer-del, .wppoppop-layer-dup, button, input').length) {
                    return;
                }
                e.preventDefault();
                e.stopPropagation();
                var rawId = $(this).attr('data-id') || $(this).data('id') || $(this).attr('id') || '';
                window.selectLayerEverywhere(rawId);
                        try {
                            if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.openLayerInspector === 'function') {
                                window.WpPopPopBuilderLayers.openLayerInspector(rawId);
                            }
                        } catch (errOpen) {}
            });

        // Two-way synchronization on Style, Data & Advanced inputs
        $(document)
            .off('input.wppoppopSuiteInputs change.wppoppopSuiteInputs')
            .on('input.wppoppopSuiteInputs change.wppoppopSuiteInputs', '#insp-tab-style input, #insp-tab-style select, #insp-tab-data input, #insp-tab-advanced input', function() {
                var el = resolveElement();
                if (!el) return;
                var $inp = $(this);
                var prop = $inp.data('prop') || $inp.data('data-prop') || $inp.data('adv-prop');
                var val = $inp.is(':checkbox') ? $inp.is(':checked') : $inp.val();

                if ($inp.attr('id') === 'prop-opacity') {
                    $('#prop-opacity-val').text(val + '%');
                    el.opacity = val;
                } else if ($inp.attr('id') === 'prop-bg-transparent') {
                    el.transparentFill = val;
                    if (val) el.bgColor = 'transparent';
                } else if (prop) {
                    el[prop] = val;
                }

                if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

        // Wrap Inspector controller methods
        var insp = window.WpPopPopBuilderInspector;
        if (insp) {
            insp.switchTab = executeSwitchTab;
            var origOpen = insp.open;
            insp.open = function(id) {
                var res = typeof origOpen === 'function' ? origOpen.apply(this, arguments) : undefined;
                var el = resolveElement(id);
                if (el) populateInspectorTabs(el);
                return res;
            };
        }
    }

    $(function() {
        bindSuiteEvents();
        setTimeout(bindSuiteEvents, 150);
    });
    bindSuiteEvents();

})(window, jQuery);
/* === WPPOPPOP_UNIFIED_SELECTION_AND_POPULATION_SUITE_END === */

/* === WPPOPPOP_MASTER_LAYER_INSPECTOR_AND_LAYER_ENGINE_START === */
(function(window, $) {
    'use strict';

    var FALLBACK_STYLE_HTML = "";
    var FALLBACK_LOGIC_HTML = "";
    var FALLBACK_DATA_HTML = "";
    var FALLBACK_ADV_HTML = "";

    var FORM_TYPES = [
        'text', 'textfield', 'email', 'number', 'spinner', 'phone', 'tel',
        'textarea', 'select', 'dropdown', 'checkbox', 'checkboxes',
        'radio', 'radios', 'multiselect', 'imageselect', 'tile',
        'date', 'time', 'upload', 'file', 'password', 'rating'
    ];

    var MEDIA_PANEL_MAP = {
        'shape': '#panel-elem-shape',
        'rect': '#panel-elem-shape',
        'rectangle': '#panel-elem-shape',
        'square': '#panel-elem-shape',
        'image': '#panel-elem-image',
        'video': '#panel-elem-video',
        'label': '#panel-elem-label',
        'title': '#panel-elem-label',
        'paragraph': '#panel-elem-label',
        'html': '#panel-elem-html',
        'close': '#panel-elem-close',
        'close_icon': '#panel-elem-close',
        'icon': '#panel-elem-icon',
        'fontawesome': '#panel-elem-icon',
        'progress': '#panel-elem-progress',
        'button': '#panel-elem-button',
        'link': '#panel-elem-button',
        'link_btn': '#panel-elem-button',
        'step': '#panel-elem-button',
        'step_btn': '#panel-elem-button',
        'submit': '#panel-elem-submit',
        'submit_btn': '#panel-elem-submit',
        'text': '#panel-elem-text',
        'textfield': '#panel-elem-text',
        'email': '#panel-elem-email',
        'number': '#panel-elem-number',
        'spinner': '#panel-elem-number',
        'phone': '#panel-elem-phone',
        'tel': '#panel-elem-phone',
        'textarea': '#panel-elem-textarea',
        'select': '#panel-elem-select',
        'dropdown': '#panel-elem-select',
        'checkbox': '#panel-elem-checkbox',
        'checkboxes': '#panel-elem-checkbox',
        'radio': '#panel-elem-radio',
        'radios': '#panel-elem-radio',
        'multiselect': '#panel-elem-multiselect',
        'imageselect': '#panel-elem-imageselect',
        'tile': '#panel-elem-tile',
        'date': '#panel-elem-date',
        'time': '#panel-elem-time',
        'upload': '#panel-elem-upload',
        'file': '#panel-elem-upload',
        'password': '#panel-elem-password',
        'rating': '#panel-elem-rating'
    };

    function normalizeId(raw) {
        if (raw === undefined || raw === null) return '';
        return String(raw)
            .replace(/^wppoppop-layer-item-/, '')
            .replace(/^wppoppop-layer-/, '')
            .replace(/^layer-/, '')
            .replace(/^wppoppop-el-/, '')
            .replace(/^el-/, '')
            .trim();
    }

    function extractAllCanvasElements() {
        var results = [];
        // 1. Try WpPopPopBuilderCanvas.getActiveElements()
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') {
            try {
                var activeList = window.WpPopPopBuilderCanvas.getActiveElements();
                if (Array.isArray(activeList)) {
                    results = results.concat(activeList);
                } else if (activeList && typeof activeList === 'object') {
                    Object.keys(activeList).forEach(function(k) {
                        if (activeList[k] && typeof activeList[k] === 'object') results.push(activeList[k]);
                    });
                }
            } catch (e) {}
        }

        // 2. Try WpPopPopBuilderCore.state.canvases across current and all canvas keys
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.canvases) {
            var canvases = core.state.canvases;
            var curKey = core.state.currentCanvas || 1;
            var keysToCheck = [curKey].concat(Object.keys(canvases));
            keysToCheck.forEach(function(ck) {
                var cData = canvases[ck];
                if (!cData) return;
                var pool = Array.isArray(cData) ? cData : (cData.elements ? cData.elements : cData);
                if (Array.isArray(pool)) {
                    results = results.concat(pool);
                } else if (pool && typeof pool === 'object') {
                    Object.keys(pool).forEach(function(ek) {
                        if (pool[ek] && typeof pool[ek] === 'object' && (pool[ek].id || pool[ek].type)) {
                            if (!pool[ek].id) pool[ek].id = ek;
                            results.push(pool[ek]);
                        }
                    });
                }
            });
        }
        return results;
    }

    function resolveElement(idOrObj) {
        if (idOrObj && typeof idOrObj === 'object' && (idOrObj.id || idOrObj.type)) return idOrObj;
        var insp = window.WpPopPopBuilderInspector;
        var rawId = (idOrObj !== undefined && idOrObj !== null && typeof idOrObj !== 'object')
            ? String(idOrObj)
            : (insp && (insp.activeElementId || insp.activeId)
                ? String(insp.activeElementId || insp.activeId)
                : (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? String(window.WpPopPopBuilderCore.state.activeId || '') : ''));
        var cleanId = normalizeId(rawId);
        if (!cleanId) return null;

        var all = extractAllCanvasElements();
        for (var i = 0; i < all.length; i++) {
            var item = all[i];
            if (!item) continue;
            if (String(item.id) === rawId || normalizeId(item.id) === cleanId) {
                return item;
            }
        }

        // DOM Fallback so resolveElement NEVER returns null when a layer or stage node exists
        var $stageNode = $('#wppoppop-el-' + cleanId + ', #el-' + cleanId + ', .wppoppop-canvas-item[data-id="' + rawId + '"], .wppoppop-canvas-item[data-id="' + cleanId + '"]').first();
        var $layerNode = $('.wppoppop-layer-item[data-id="' + rawId + '"], .wppoppop-layer-item[data-id="' + cleanId + '"]').first();
        if ($stageNode.length || $layerNode.length) {
            var inferredType = ($stageNode.attr('data-type') || $layerNode.attr('data-type') || 'label').toLowerCase();
            var fallbackObj = {
                id: $stageNode.attr('data-id') || $layerNode.attr('data-id') || cleanId,
                type: inferredType,
                name: $layerNode.find('.wppoppop-layer-title, .wppoppop-layer-name').text().trim() || inferredType.toUpperCase(),
                left: parseInt($stageNode.css('left'), 10) || 20,
                top: parseInt($stageNode.css('top'), 10) || 20,
                width: $stageNode.outerWidth() || 180,
                height: $stageNode.outerHeight() || 44
            };
            return fallbackObj;
        }
        return null;
    }

    // Guarantee all 5 tab panes exist inside the scroll body and are NEVER empty
    function ensureFiveTabsDom() {
        var $drawer = $('#wppoppop-inspector-drawer');
        if (!$drawer.length) return;

        // Ensure #wppoppop-inspector-drawer is strictly inside .wppoppop-builder-workspace and 360px wide
        var $ws = $('.wppoppop-builder-workspace, #wppoppop-builder-workspace').first();
        if ($ws.length && $drawer.parent()[0] !== $ws[0]) {
            $ws.append($drawer);
        }
        $drawer.css({
            position: 'absolute',
            top: '0px',
            right: '0px',
            bottom: '0px',
            left: 'auto',
            width: '360px',
            maxWidth: '360px',
            height: '100%'
        });

        var $body = $drawer.find('#wppoppop-inspector-body, .wppoppop-inspector-body').first();
        if (!$body.length) {
            $body = $('<div id="wppoppop-inspector-body" class="wppoppop-inspector-body" style="flex:1 1 auto;overflow-y:auto;overflow-x:hidden;padding:16px;box-sizing:border-box;width:100%;"></div>');
            $drawer.append($body);
        } else {
            $body.attr('id', 'wppoppop-inspector-body').addClass('wppoppop-inspector-body');
        }

        // Deduplicate any duplicate tab panes and move canonical panes inside #wppoppop-inspector-body
        var paneSpecs = [
            { id: 'insp-tab-basic', fallback: null },
            { id: 'insp-tab-style', fallback: FALLBACK_STYLE_HTML },
            { id: 'insp-tab-logic', fallback: FALLBACK_LOGIC_HTML },
            { id: 'insp-tab-data', fallback: FALLBACK_DATA_HTML },
            { id: 'insp-tab-advanced', fallback: FALLBACK_ADV_HTML }
        ];

        for (var i = 0; i < paneSpecs.length; i++) {
            var spec = paneSpecs[i];
            var $panes = $('[id="' + spec.id + '"]');
            if ($panes.length > 1) {
                $panes.slice(1).remove();
            }
            var $pane = $('#' + spec.id);
            if (!$pane.length) {
                $pane = $('<div id="' + spec.id + '" class="wppoppop-tab-pane wppoppop-insp-content" style="display:none;"></div>');
                $body.append($pane);
            } else if ($pane.parent()[0] !== $body[0]) {
                $body.append($pane);
            }

            // If Style, Logic, Data, or Advanced pane is empty, inject authoritative markup
            if (spec.fallback && $pane.children().length === 0) {
                $pane.html(spec.fallback);
            }
        }

        // Keep Coordinates & Bounds and Animation strictly inside #insp-tab-basic
        var $basic = $('#insp-tab-basic');
        $('#insp-section-coords, #insp-section-animation, #insp-section-gp-basic-shared').each(function() {
            if ($basic.length && !$.contains($basic[0], this)) {
                $basic.append(this);
            }
        });

        // Ensure all 5 tab buttons exist in the tab bar
        var $tabsBar = $('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs').first();
        if ($tabsBar.length) {
            if (!$tabsBar.find('[data-tab="data"]').length) {
                $tabsBar.append('<button type="button" class="wppoppop-insp-tab wppoppop-form-only-tab" data-tab="data" style="display:none;">Data</button>');
            }
            if (!$tabsBar.find('[data-tab="advanced"]').length) {
                $tabsBar.append('<button type="button" class="wppoppop-insp-tab wppoppop-form-only-tab" data-tab="advanced" style="display:none;">Advanced</button>');
            }
        }
    }

    function executeSwitchTab(tabName) {
        ensureFiveTabsDom();
        var target = String(tabName || 'basic').toLowerCase().trim();
        if (target === 'content') target = 'basic';

        var $tabsBar = $('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs');
        $tabsBar.find('.wppoppop-insp-tab, [data-tab]').removeClass('active');
        $tabsBar.find('[data-tab="' + target + '"]').addClass('active');

        var allTabs = ['basic', 'style', 'logic', 'data', 'advanced'];
        for (var i = 0; i < allTabs.length; i++) {
            var pane = document.getElementById('insp-tab-' + allTabs[i]);
            if (pane) {
                if (allTabs[i] === target) {
                    $(pane).addClass('active');
                    pane.style.setProperty('display', 'block', 'important');
                    // Ensure direct section cards inside active tab are visible unless explicitly scoped off
                    $(pane).children('.wppoppop-insp-section').not('#wppoppop-form-style-module, #prop-btn-hover-section').show();
                } else {
                    $(pane).removeClass('active');
                    pane.style.setProperty('display', 'none', 'important');
                }
            }
        }
    }

    function renderInlineStylesRepeater(el) {
        var $list = $('#wppoppop-gp-inline-styles-list');
        if (!$list.length) return;
        $list.empty();
        var styles = Array.isArray(el.inlineStyles) ? el.inlineStyles : [];
        for (var i = 0; i < styles.length; i++) {
            var r = styles[i] || {};
            var $row = $(
                '<div class="wppoppop-inline-style-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:6px;align-items:center;">' +
                    '<input type="text" class="wppoppop-input wppoppop-style-prop" placeholder="property (e.g. border-color)" style="font-size:11px;" />' +
                    '<input type="text" class="wppoppop-input wppoppop-style-val" placeholder="value (e.g. #38bdf8)" style="font-size:11px;" />' +
                    '<button type="button" class="button button-small wppoppop-remove-inline-style" style="color:#f87171;">&times;</button>' +
                '</div>'
            );
            $row.find('.wppoppop-style-prop').val(r.prop || '');
            $row.find('.wppoppop-style-val').val(r.val || '');
            $list.append($row);
        }
    }

    function renderLogicRulesList(el) {
        var $list = $('#wppoppop-logic-rules-list');
        if (!$list.length) return;
        $list.empty();
        var rules = Array.isArray(el.logicRules) ? el.logicRules : [];
        var allEls = extractAllCanvasElements();
        var cleanSelf = normalizeId(el.id);

        if (!rules.length) {
            $list.append('<div style="font-size:11px; color:#64748b; font-style:italic;">No rules yet. Click "+ Add rule" to add a condition.</div>');
            return;
        }

        for (var i = 0; i < rules.length; i++) {
            var r = rules[i] || {};
            var fieldOpts = '';
            for (var j = 0; j < allEls.length; j++) {
                var f = allEls[j];
                if (!f || normalizeId(f.id) === cleanSelf) continue;
                var fId = normalizeId(f.id);
                var fName = f.fieldName || f.name || f.label || f.placeholder || (String(f.type).toUpperCase() + ' #' + fId);
                var sel = (fId === normalizeId(r.field)) ? ' selected' : '';
                fieldOpts += '<option value="' + fId + '"' + sel + '>' + $('<div>').text(fName).html() + '</option>';
            }

            var ops = [
                { k: 'equal', l: 'Equals (==)' },
                { k: 'not_equal', l: 'Not Equal (!=)' },
                { k: 'contains', l: 'Contains' },
                { k: 'not_contains', l: 'Does Not Contain' },
                { k: 'greater', l: 'Greater (>)' },
                { k: 'less', l: 'Less (<)' },
                { k: 'empty', l: 'Is Empty' },
                { k: 'not_empty', l: 'Is Not Empty' }
            ];
            var opHtml = ops.map(function(o) {
                return '<option value="' + o.k + '"' + (o.k === (r.operator || 'equal') ? ' selected' : '') + '>' + o.l + '</option>';
            }).join('');

            var $row = $(
                '<div class="wppoppop-logic-rule-row" style="background:#090d16; border:1px solid #1e293b; border-radius:6px; padding:8px; display:grid; grid-template-columns:1fr 1fr; gap:6px;">' +
                    '<select class="widefat wppoppop-input rule-field" style="font-size:11px; grid-column:span 2;"><option value="">-- Select Field --</option>' + fieldOpts + '</select>' +
                    '<select class="widefat wppoppop-input rule-operator" style="font-size:11px;">' + opHtml + '</select>' +
                    '<div style="display:flex; gap:4px;">' +
                        '<input type="text" class="widefat wppoppop-input rule-value" placeholder="Value..." style="flex:1; font-size:11px;" />' +
                        '<button type="button" class="button button-small btn-remove-logic-rule" style="color:#ef4444; padding:0 6px;">&times;</button>' +
                    '</div>' +
                '</div>'
            );
            $row.find('.rule-value').val(r.value || '');
            $list.append($row);
        }
    }

    function hydrateAllFiveTabs(el) {
        ensureFiveTabsDom();
        if (!el) return;

        var type = String(el.type || 'label').toLowerCase().trim();
        var isForm = FORM_TYPES.indexOf(type) !== -1;
        var isButton = ['button', 'link', 'link_btn', 'step', 'step_btn', 'submit', 'submit_btn'].indexOf(type) !== -1;

        // 1. 3-Tab (Media/Action) vs 5-Tab (Form) Routing
        var $tabsBar = $('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs');
        $tabsBar.find('[data-tab="data"], [data-tab="advanced"]').each(function() {
            this.style.setProperty('display', isForm ? 'inline-block' : 'none', 'important');
        });

        var curTab = $tabsBar.find('.wppoppop-insp-tab.active').attr('data-tab') || 'basic';
        if (!isForm && (curTab === 'data' || curTab === 'advanced')) {
            curTab = 'basic';
        }
        executeSwitchTab(curTab);

        // 2. CONTENT TAB (#insp-tab-basic)
        $('#prop-layer-name, #prop-element-name').val(el.name || el.label || type.toUpperCase());
        $('#prop-pos-top, #prop-top').val(parseInt(el.top, 10) || 0);
        $('#prop-pos-left, #prop-left').val(parseInt(el.left, 10) || 0);
        $('#prop-size-width, #prop-width').val(parseInt(el.width, 10) || 160);
        $('#prop-size-height, #prop-height').val(parseInt(el.height, 10) || 44);
        $('#prop-required').prop('checked', !!el.required);

        var targetPanel = MEDIA_PANEL_MAP[type];
        if (targetPanel && $(targetPanel).length) {
            $('[id$="-legacy"]').hide();
            $('.element-panel, .wppoppop-panel-specific').hide();
            $(targetPanel).show();
            $(targetPanel).find('.wppoppop-gp-bind').each(function() {
                var $inp = $(this);
                var p = $inp.data('prop');
                if (!p) return;
                var v = el[p];
                if (v === undefined) {
                    if (p === 'content') v = el.label || el.titleText || el.text || '';
                    if (p === 'placeholder') v = el.label || '';
                    if (p === 'optionsList') v = el.options || el.selectOptions || '';
                }
                if ($inp.is(':checkbox')) $inp.prop('checked', !!v);
                else if (v !== undefined && v !== null) $inp.val(v);
            });
        }

        if (type === 'shape') {
            $('#prop-shape-fill').val(el.shapeFill || '#3b82f6');
            $('#prop-shape-transparent').prop('checked', !!el.shapeNoFill);
        }

        // 3. STYLE TAB (#insp-tab-style)
        $('#wppoppop-form-style-module').toggle(isForm);
        $('#prop-btn-hover-section').toggle(isButton);
        $('#insp-section-opacity, #insp-section-bg-color, #insp-section-typography, #insp-section-padding, #insp-section-border, #insp-section-shadow, #insp-section-custom-css').show();
        $('#insp-section-typography').toggle(['shape', 'rect', 'rectangle', 'square'].indexOf(type) === -1);

        var op = (el.opacity !== undefined && el.opacity !== null && el.opacity !== '') ? parseFloat(el.opacity) : 100;
        if (op <= 1 && op > 0) op = Math.round(op * 100);
        $('#prop-opacity').val(op);
        $('#prop-opacity-val').text(op + '%');

        $('#prop-bg-color').val((el.bgColor && el.bgColor !== 'transparent') ? el.bgColor : '#3b82f6');
        $('#prop-bg-transparent').prop('checked', !!el.transparentFill || el.bgColor === 'transparent' || (type === 'shape' && !el.bgColor));
        $('#prop-font-size').val(parseInt(el.fontSize, 10) || 16);
        $('#prop-font-weight').val(el.fontWeight || '400');
        $('#prop-line-height').val(el.lineHeight || 1.4);
        $('#prop-letter-spacing').val(el.letterSpacing || 0);
        $('#prop-color').val(el.color || '#ffffff');

        $('#prop-pad-top').val(parseInt(el.paddingTop, 10) || 0);
        $('#prop-pad-right').val(parseInt(el.paddingRight, 10) || 0);
        $('#prop-pad-bottom').val(parseInt(el.paddingBottom, 10) || 0);
        $('#prop-pad-left').val(parseInt(el.paddingLeft, 10) || 0);

        $('#prop-border-width').val(parseInt(el.borderWidth, 10) || 0);
        $('#prop-border-style').val(el.borderStyle || 'solid');
        $('#prop-border-color').val(el.borderColor || '#334155');
        $('#prop-border-radius').val(parseInt(el.borderRadius, 10) || 4);

        $('#prop-shadow-x').val(parseInt(el.shadowX, 10) || 0);
        $('#prop-shadow-y').val(parseInt(el.shadowY, 10) || 4);
        $('#prop-shadow-blur').val(parseInt(el.shadowBlur, 10) || 12);
        $('#prop-shadow-spread').val(parseInt(el.shadowSpread, 10) || 0);
        $('#prop-shadow-color').val(el.shadowColor || '#000000');

        $('#prop-elem-hover-bg').val(el.hoverBgColor || '#0284c7');
        $('#prop-elem-hover-color').val(el.hoverTextColor || '#ffffff');
        $('#prop-elem-hover-border').val(el.hoverBorderColor || '#0284c7');

        var align = el.textAlign || 'left';
        $('#wppoppop-form-align-group .wppoppop-form-align-btn')
            .removeClass('active button-primary')
            .removeClass('active is-active button-primary');
        $('#prop-form-icon-left').val(el.iconLeft || '');
        $('#prop-form-icon-left-offset').val(el.iconLeftOffset !== undefined ? el.iconLeftOffset : 10);
        $('#prop-form-icon-right').val(el.iconRight || '');
        $('#prop-form-icon-right-offset').val(el.iconRightOffset !== undefined ? el.iconRightOffset : 10);
        $('#prop-form-custom-class').val(el.customClass || '');
        renderInlineStylesRepeater(el);

        // 4. LOGIC TAB (#insp-tab-logic)
        $('#prop-logic-enabled').prop('checked', !!el.logicEnabled);
        $('#wppoppop-logic-body').toggle(!!el.logicEnabled);
        $('#prop-logic-action').val(el.logicAction || 'show');
        $('#prop-logic-match').val(el.logicMatch || 'all');
        renderLogicRulesList(el);
        $('#prop-logic-click-action').val(el.buttonAction || 'link');
        $('#prop-logic-link-url').val(el.linkUrl || el.url || '');
        $('#prop-logic-goto-canvas').val(el.goto_canvas || 2);
        $('#prop-logic-custom-js').val(el.customJs || '');

        // 5. DATA TAB (#insp-tab-data)
        $('#prop-default-value').val(el.defaultValue || '');
        $('#prop-dynamic-default').prop('checked', !!el.dynamicDefault);
        $('#prop-dynamic-param').val(el.paramName || el.dynamicParam || '');
        $('#prop-readonly').prop('checked', !!el.readOnly);
        $('#prop-save-to-db').prop('checked', el.saveToDb !== false);

        // 6. ADVANCED TAB (#insp-tab-advanced)
        $('#prop-field-name').val(el.fieldName || el.fieldId || '');
        $('#prop-input-mask').val(el.inputMask || '');
        $('#prop-validator-error').val(el.errorMessage || '');

        var filters = Array.isArray(el.filters) ? el.filters : [];
        $('.prop-filter-chk').each(function() {
            $(this).prop('checked', filters.indexOf($(this).val()) !== -1);
        });

        var validators = Array.isArray(el.validators) ? el.validators : [];
        if (el.required && validators.indexOf('required') === -1) validators.push('required');
        $('.prop-validator-chk').each(function() {
            $(this).prop('checked', validators.indexOf($(this).val()) !== -1);
        });
    }

    // =========================================================================
    // AUTHORITATIVE LAYER SELECTION DISPATCHER (CALLED BY CAPTURE & BUBBLE)
    // =========================================================================
    var _selectingGuard = false;
    window.selectLayerEverywhere = function(idOrObj) {
        if (_selectingGuard) return;
        var el = resolveElement(idOrObj);
        if (!el) return;

        _selectingGuard = true;
        try {
            var actualId = String(el.id);
            var cleanId = normalizeId(actualId);

            // 1. Update Core & Canvas state
            if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
                window.WpPopPopBuilderCore.state.activeId = actualId;
            }
            if (window.WpPopPopBuilderCanvas) {
                window.WpPopPopBuilderCanvas.selectedId = actualId;
            }

            // 2. Highlight in Floating Layers Panel
            $('#wppoppop-layers-list .wppoppop-layer-item, #wppoppop-layers-list > *').removeClass('active selected is-selected');
            $('#wppoppop-layers-list [data-id="' + actualId + '"], #wppoppop-layers-list [data-id="' + cleanId + '"], #wppoppop-layers-list [data-id="el-' + cleanId + '"], #wppoppop-layers-list [data-id="wppoppop-el-' + cleanId + '"]')
                .addClass('active selected is-selected');

            // 3. Highlight on Canvas Stage
            $('.wppoppop-canvas-item, .wppoppop-element').removeClass('wppoppop-selected selected is-selected');
            $('#wppoppop-el-' + cleanId + ', #el-' + cleanId + ', .wppoppop-canvas-item[data-id="' + actualId + '"], .wppoppop-canvas-item[data-id="' + cleanId + '"]')
                .addClass('wppoppop-selected selected is-selected');

            // 4. Open Inspector Drawer & Push Layers Panel to right: 376px
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.activeElementId = actualId;
                window.WpPopPopBuilderInspector.activeId = actualId;
            }
            var $drawer = $('#wppoppop-inspector-drawer');
            $drawer.css('display', 'flex').removeClass('is-closed').addClass('is-open open');
            $('body, .wppoppop-builder-workspace, .wppoppop-main-frame').addClass('panel-open inspector-open');

            if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.pushForInspector === 'function') {
                window.WpPopPopBuilderLayers.pushForInspector(true);
            } else {
                $('#wppoppop-floating-layers-panel').css('right', '376px');
            }

            // 5. Hydrate all 5 Tabs
            hydrateAllFiveTabs(el);
        } finally {
            _selectingGuard = false;
        }
    };

    function bindMasterHooks() {
        ensureFiveTabsDom();

        // Hook WpPopPopBuilderInspector methods
        var insp = window.WpPopPopBuilderInspector;
        if (insp) {
            insp.switchTab = executeSwitchTab;
            insp.sanitizeLayerInspectorForElement = hydrateAllFiveTabs;
            if (!insp._masterOpenWrapped) {
                insp._masterOpenWrapped = true;
                var origOpen = insp.open;
                insp.open = function(id) {
                    var res;
                    try {
                        if (typeof origOpen === 'function') res = origOpen.apply(this, arguments);
                    } catch (e) {}
                    var el = resolveElement(id);
                    if (el) hydrateAllFiveTabs(el);
                    return res;
                };
            }
        }

        // 1. CAPTURE-PHASE CLICK LISTENER ON WINDOW (Cannot be blocked by e.stopPropagation()!)
        if (!window._wppoppopMasterCaptureBound) {
            window._wppoppopMasterCaptureBound = true;
            window.addEventListener('click', function(e) {
                var target = e.target;
                if (!target || !target.closest) return;

                // A. Click on Layer Settings Tab Button (Content | Style | Logic | Data | Advanced)
                var tabBtn = target.closest('#wppoppop-inspector-drawer .wppoppop-insp-tab, #wppoppop-inspector-tabs-bar [data-tab], .wppoppop-inspector-tabs [data-tab]');
                if (tabBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    var tabName = tabBtn.getAttribute('data-tab') || 'basic';
                    executeSwitchTab(tabName);
                    return;
                }

                // B. Click on Layer Item inside Floating Layers Panel
                var layerRow = target.closest('#wppoppop-floating-layers-panel .wppoppop-layer-item, #wppoppop-layers-list .wppoppop-layer-item, #wppoppop-layers-list > li, #wppoppop-layers-list > div');
                if (layerRow) {
                    // Allow action buttons (lock, hide, duplicate, delete) or rename input to work normally
                    if (target.closest('.wppoppop-layer-action-btn, .wppoppop-layer-actions, .wppoppop-layer-btn-lock, .wppoppop-layer-btn-vis, .wppoppop-layer-btn-dup, .wppoppop-layer-btn-del, button, input')) {
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    var rawId = layerRow.getAttribute('data-id') || layerRow.getAttribute('data-layer-id') || layerRow.getAttribute('data-el-id') || layerRow.id || '';
                    if (rawId) {
                        window.selectLayerEverywhere(rawId);
                        try {
                            if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.openLayerInspector === 'function') {
                                window.WpPopPopBuilderLayers.openLayerInspector(rawId);
                            }
                        } catch (errOpen) {}
                    }
                    return;
                }
            }, true); // <-- TRUE = Capture Phase!
        }

        // 2. Two-way live binding for Style, Logic, Data & Advanced inputs
        $(document)
            .off('input.wppoppopMasterSync change.wppoppopMasterSync')
            .on('input.wppoppopMasterSync change.wppoppopMasterSync', '#insp-tab-style input, #insp-tab-style select, #insp-tab-logic input, #insp-tab-logic select, #insp-tab-data input, #insp-tab-advanced input', function() {
                var el = resolveElement();
                if (!el) return;
                var $inp = $(this);
                var prop = $inp.data('prop') || $inp.data('data-prop') || $inp.data('adv-prop') || $inp.data('logic-prop');
                var val = $inp.is(':checkbox') ? $inp.is(':checked') : $inp.val();

                if (this.id === 'prop-opacity') {
                    $('#prop-opacity-val').text(val + '%');
                    el.opacity = val;
                } else if (this.id === 'prop-bg-transparent') {
                    el.transparentFill = val;
                    if (val) el.bgColor = 'transparent';
                } else if (this.id === 'prop-logic-enabled') {
                    el.logicEnabled = val;
                    $('#wppoppop-logic-body').toggle(!!val);
                } else if (prop) {
                    el[prop] = val;
                }

                // Live apply CSS to stage node
                var cleanId = normalizeId(el.id);
                var $node = $('#wppoppop-el-' + cleanId + ', #el-' + cleanId + ', .wppoppop-canvas-item[data-id="' + el.id + '"]').first();
                if ($node.length) {
                    if (el.opacity !== undefined) $node.css('opacity', (parseFloat(el.opacity) > 1 ? parseFloat(el.opacity) / 100 : el.opacity));
                    if (el.bgColor) $node.css('background-color', el.transparentFill ? 'transparent' : el.bgColor);
                    if (el.color) $node.css('color', el.color);
                    if (el.fontSize) $node.css('font-size', el.fontSize + 'px');
                    if (el.fontWeight) $node.css('font-weight', el.fontWeight);
                    if (el.lineHeight) $node.css('line-height', el.lineHeight);
                    if (el.letterSpacing !== undefined) $node.css('letter-spacing', el.letterSpacing + 'px');
                    if (el.borderRadius !== undefined) $node.css('border-radius', el.borderRadius + 'px');
                    if (el.borderWidth !== undefined) $node.css('border', el.borderWidth + 'px ' + (el.borderStyle || 'solid') + ' ' + (el.borderColor || '#334155'));
                }
            });

        // 3. Repeater buttons (+ Add a style, + Add rule, Form Align)
        $(document)
            .off('click.wppoppopMasterRepeaters')
            .on('click.wppoppopMasterRepeaters', '#wppoppop-btn-add-inline-style, #btn-add-logic-rule, #wppoppop-form-align-group .wppoppop-form-align-btn', function(e) {
                e.preventDefault();
                var el = resolveElement();
                if (!el) return;
                if (this.id === 'wppoppop-btn-add-inline-style') {
                    if (!Array.isArray(el.inlineStyles)) el.inlineStyles = [];
                    el.inlineStyles.push({ prop: '', val: '' });
                    renderInlineStylesRepeater(el);
                } else if (this.id === 'btn-add-logic-rule') {
                    if (!Array.isArray(el.logicRules)) el.logicRules = [];
                    el.logicRules.push({ field: '', operator: 'equal', value: '' });
                    renderLogicRulesList(el);
                } else if ($(this).hasClass('wppoppop-form-align-btn')) {
                    el.textAlign = $(this).attr('data-align') || 'left';
                    $('#wppoppop-form-align-group .wppoppop-form-align-btn').removeClass('active button-primary');
                    $(this).addClass('active button-primary');
                }
            });
    }

    $(function() {
        bindMasterHooks();
        setTimeout(bindMasterHooks, 150);
        setTimeout(bindMasterHooks, 450);
    });
    bindMasterHooks();

})(window, jQuery);
/* === WPPOPPOP_MASTER_LAYER_INSPECTOR_AND_LAYER_ENGINE_END === */

/* === WPPOPPOP_INSPECTOR_ICON_ONLY_ACTIVE_ON_CLICK_START === */
(function(window, $) {
    'use strict';

    // Reset all inspector icons/presets to neutral (unclicked) when switching layers
    function resetInspectorIconsOnOpen() {
        $('#wppoppop-inspector-drawer')
            .find('.wppoppop-icon-preset, .wppoppop-form-icon-preset, .wppoppop-icon-grid-btn, .wppoppop-form-align-btn, .wppoppop-insp-icon-btn')
            .removeClass('active is-active button-primary');
        $('#wppoppop-inspector-header-icon').css('color', '#94a3b8');
    }

    function bindIconActiveOnlyOnClick() {
        // 1. Capture-phase listener so clicking any icon inside #wppoppop-inspector-drawer
        //    exclusively activates ONLY the clicked icon.
        if (!window._wppoppopIconClickOnlyBound) {
            window._wppoppopIconClickOnlyBound = true;

            window.addEventListener('click', function(e) {
                var t = e.target;
                if (!t || !t.closest) return;

                var drawer = t.closest('#wppoppop-inspector-drawer');
                if (!drawer) return;

                // A. Element 7 Icon Presets & Icon Picker Grid (#panel-elem-icon)
                var iconPresetBtn = t.closest('.wppoppop-icon-preset, .wppoppop-icon-grid-btn');
                if (iconPresetBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    var iconCls = iconPresetBtn.getAttribute('data-icon') || '';
                    var $allIconBtns =$(drawer).find('.wppoppop-icon-preset, .wppoppop-icon-grid-btn');
                    $allIconBtns.removeClass('active is-active');$(drawer).find('[data-icon="' + iconCls + '"]').addClass('active is-active');
                    $('#prop-icon-class').val(iconCls).trigger('input').trigger('change');
                    return;
                }

                // B. Form Input Icon Presets (Left / Right Input Icons in Style Tab)
                var formIconBtn = t.closest('.wppoppop-form-icon-preset');
                if (formIconBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    var targetSel = formIconBtn.getAttribute('data-target') || '#prop-form-icon-left';
                    var glyph = formIconBtn.getAttribute('data-glyph') || '';

                    // Scope active state strictly to buttons sharing the same data-target (left vs right)
                    var $groupBtns =$(drawer).find('.wppoppop-form-icon-preset[data-target="' + targetSel + '"]');
                    $groupBtns.removeClass('active is-active');

                    // Only mark active if a non-empty icon glyph was clicked (Clear resets all to inactive)
                    if (glyph !== '') {
                        $(formIconBtn).addClass('active is-active');
                    }
                    $(targetSel).val(glyph).trigger('input').trigger('change');
                    return;
                }

                // C. Form Text Alignment Buttons (Left | Center | Right)
                var alignBtn = t.closest('.wppoppop-form-align-btn');
                if (alignBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    var alignVal = alignBtn.getAttribute('data-align') || 'left';
                    $('#wppoppop-form-align-group .wppoppop-form-align-btn').removeClass('active is-active button-primary');
                    $(alignBtn).addClass('active is-active');
                    if (window.WpPopPopBuilderInspector) {
                        var el = window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function'
                            ? (window.WpPopPopBuilderCanvas.getActiveElements() || []).find(function(x) {
                                return x && String(x.id) === String(window.WpPopPopBuilderInspector.activeElementId);
                            })
                            : null;
                        if (el) {
                            el.textAlign = alignVal;
                            if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                                window.WpPopPopBuilderCanvas.renderCanvas();
                            }
                        }
                    }
                    return;
                }

                // D. Titlebar Gear Icon (#wppoppop-inspector-header-icon)
                var headerIcon = t.closest('#wppoppop-inspector-header-icon, .wppoppop-insp-icon-btn');
                if (headerIcon) {
                    var $hi = $(headerIcon);$hi.toggleClass('active is-active');
                    $hi.css('color',$hi.hasClass('active') ? '#38bdf8' : '#94a3b8');
                    return;
                }
            }, true);
        }

        // 2. When the user types manually into #prop-icon-class, #prop-form-icon-left, or #prop-form-icon-right,
        //    clear preset button active states unless an exact preset was clicked
        $(document)
            .off('keydown.wppoppopManualIconInput')
            .on('keydown.wppoppopManualIconInput', '#prop-icon-class, #prop-form-icon-left, #prop-form-icon-right', function() {
                if (this.id === 'prop-icon-class') {
                    $('#panel-elem-icon .wppoppop-icon-preset').removeClass('active is-active');
                } else {
                    $('.wppoppop-form-icon-preset[data-target="#' + this.id + '"]').removeClass('active is-active');
                }
            });
    }

    // Hook into selectLayerEverywhere so opening a layer resets unclicked icons to neutral
    var origSelectEverywhere = window.selectLayerEverywhere;
    if (typeof origSelectEverywhere === 'function' && !origSelectEverywhere._iconResetWrapped) {
        var wrappedSelect = function(idOrObj) {
            var res = origSelectEverywhere.apply(this, arguments);
            resetInspectorIconsOnOpen();
            return res;
        };
        wrappedSelect._iconResetWrapped = true;
        window.selectLayerEverywhere = wrappedSelect;
    }

    $(function() {
        resetInspectorIconsOnOpen();
        bindIconActiveOnlyOnClick();
        setTimeout(bindIconActiveOnlyOnClick, 150);
    });
    bindIconActiveOnlyOnClick();

})(window, jQuery);

/* === WPPOPPOP_VIDEO_SIZE_PRESET_START === */
/* Video layers: default COORDINATES & BOUNDS preset WIDTH 320px x HEIGHT 240px.
   Applied on creation, plus an "Apply preset" button in the Content tab. */
(function(window, $) {
    'use strict';

    var PRESET_W = 320;
    var PRESET_H = 240;
    var TYPE = 'video';

    function cleanId(raw) {
        return String(raw || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim();
    }

    function getList() {
        var core = window.WpPopPopBuilderCore;
        if (!core || !core.state || !core.state.canvases) return null;
        var cur = core.state.currentCanvas || 1;
        return core.state.canvases[cur] || null;
    }

    function getActiveEl() {
        var core = window.WpPopPopBuilderCore;
        var list = getList();
        if (!core || !list || !core.state.activeId) return null;
        var id = cleanId(core.state.activeId);
        for (var i = 0; i < list.length; i++) {
            if (list[i] && cleanId(list[i].id) === id) return list[i];
        }
        return null;
    }

    function isVideo(el) {
        return !!el && String(el.type || '').toLowerCase().trim() === TYPE;
    }

    function applyPreset(el) {
        el.width = PRESET_W;
        el.height = PRESET_H;
        $('#prop-width, #prop-size-width').val(PRESET_W);
        $('#prop-height, #prop-size-height').val(PRESET_H);
        var id = cleanId(el.id);
        var $node = $('#canvas-elem-' + id + ', #wppoppop-el-' + id + ', #el-' + id + ', .wppoppop-canvas-item[data-id="' + id + '"]').first();
        if ($node.length) {
            $node.css({ width: PRESET_W + 'px', height: PRESET_H + 'px' });
        }
    }

    function renderAndRecord(label) {
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
            window.WpPopPopBuilderCanvas.renderCanvas();
        }
        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
            window.WpPopPopBuilderCore.pushHistory(label);
        }
    }

    // Wrap addElement so every new Video layer starts at the preset size
    function wrapAddElement() {
        var canvas = window.WpPopPopBuilderCanvas;
        if (!canvas || typeof canvas.addElement !== 'function' || canvas.__wppoppopVideoPresetWrapped) return;
        canvas.__wppoppopVideoPresetWrapped = true;
        var orig = canvas.addElement;
        canvas.addElement = function(type) {
            var before = getList() ? getList().length : 0;
            var res = orig.apply(this, arguments);
            if (String(type || '').toLowerCase().trim() === TYPE) {
                var list = getList() || [];
                var created = (res && typeof res === 'object' && res.type) ? res : null;
                if (!created && list.length > before) created = list[list.length - 1];
                if (!created) {
                    for (var i = list.length - 1; i >= 0; i--) {
                        if (isVideo(list[i])) { created = list[i]; break; }
                    }
                }
                if (isVideo(created)) {
                    applyPreset(created);
                    renderAndRecord('Video Size Preset');
                }
            }
            return res;
        };
    }

    // Content tab button inside COORDINATES & BOUNDS
    function ensurePresetButton() {
        var $coords = $('#insp-section-coords');
        if (!$coords.length || $('#wppoppop-video-preset-btn').length) return;
        $coords.append(
            '<div id="wppoppop-video-preset-wrap" style="display:none; margin-top:8px;">' +
                '<button type="button" id="wppoppop-video-preset-btn" class="button button-small" ' +
                'style="width:100%; font-size:11px; font-weight:700;">Apply Video Preset (320 x 240)</button>' +
            '</div>'
        );
    }

    function refreshPresetButton() {
        ensurePresetButton();
        $('#wppoppop-video-preset-wrap').toggle(isVideo(getActiveEl()));
    }

    function bindPresetButton() {
        $(document).off('click.wppoppopVideoPreset').on('click.wppoppopVideoPreset', '#wppoppop-video-preset-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var el = getActiveEl();
            if (!isVideo(el)) return;
            applyPreset(el);
            renderAndRecord('Apply Video Size Preset');
        });
    }

    function hookInspector() {
        var insp = window.WpPopPopBuilderInspector;
        if (!insp || insp.__wppoppopVideoPresetHooked) return;
        insp.__wppoppopVideoPresetHooked = true;
        if (typeof insp.open === 'function') {
            var origOpen = insp.open;
            insp.open = function() {
                var res = origOpen.apply(this, arguments);
                setTimeout(refreshPresetButton, 20);
                return res;
            };
        }
    }

    $(function() {
        wrapAddElement();
        bindPresetButton();
        hookInspector();
        refreshPresetButton();
        setTimeout(wrapAddElement, 150);
        setTimeout(hookInspector, 150);
        setInterval(refreshPresetButton, 500);
    });
})(window, jQuery);
/* === WPPOPPOP_VIDEO_SIZE_PRESET_END === */

/* === WPPOPPOP_FA_ICON_CONTROLS_START === */
/* Font Awesome / Vector Icon (types: icon, fontawesome).
   Content tab: colour picker (el.iconColor) + COORDINATES preset 40 x 40. */
(function(window, $) {
    'use strict';

    var TYPES = ['icon', 'fontawesome'];
    var PRESET = 40;
    var DEFAULT_COLOR = '#38bdf8';

    function cleanId(raw) {
        return String(raw || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim();
    }

    function getList() {
        var core = window.WpPopPopBuilderCore;
        if (!core || !core.state || !core.state.canvases) return null;
        return core.state.canvases[core.state.currentCanvas || 1] || null;
    }

    function getActiveEl() {
        var core = window.WpPopPopBuilderCore;
        var list = getList();
        if (!core || !list || !core.state.activeId) return null;
        var id = cleanId(core.state.activeId);
        for (var i = 0; i < list.length; i++) {
            if (list[i] && cleanId(list[i].id) === id) return list[i];
        }
        return null;
    }

    function isIcon(el) {
        return !!el && TYPES.indexOf(String(el.type || '').toLowerCase().trim()) !== -1;
    }

    function stageNode(el) {
        var id = cleanId(el.id);
        return $('#canvas-elem-' + id + ', #wppoppop-el-' + id + ', #el-' + id + ', .wppoppop-canvas-item[data-id="' + id + '"]').first();
    }

    function applyColorToStage(el) {
        var $node = stageNode(el);
        if (!$node.length) return;
        var $glyph = $node.find('i, .wppoppop-icon, .fa, [class*="fa-"]').first();
        ($glyph.length ? $glyph : $node).css('color', el.iconColor);
    }

    function applySizeToStage(el) {
        var $node = stageNode(el);
        if ($node.length) $node.css({ width: PRESET + 'px', height: PRESET + 'px' });
    }

    function render(label) {
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
            window.WpPopPopBuilderCanvas.renderCanvas();
        }
        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
            window.WpPopPopBuilderCore.pushHistory(label);
        }
    }

    // Colour picker row inside the Icon panel (reuses #prop-icon-color if the drawer already has it)
    function ensureColorPicker() {
        if ($('#prop-icon-color').length) return;
        var $panel = $('#panel-elem-icon');
        if (!$panel.length || $('#wppoppop-fa-color-row').length) return;
        $panel.append(
            '<div id="wppoppop-fa-color-row" style="display:none; margin-top:8px;">' +
                '<label for="wppoppop-fa-color" style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">ICON COLOR</label>' +
                '<input type="color" id="wppoppop-fa-color" value="' + DEFAULT_COLOR + '" style="width:100%; height:32px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />' +
            '</div>'
        );
    }

    function colorInput() {
        return $('#prop-icon-color').length ? $('#prop-icon-color') : $('#wppoppop-fa-color');
    }

    function refreshUi() {
        ensureColorPicker();
        var el = getActiveEl();
        var show = isIcon(el);
        $('#wppoppop-fa-color-row').toggle(show && !$('#prop-icon-color').length);
        $('#wppoppop-fa-size-wrap').toggle(show);
        if (show) {
            colorInput().val(el.iconColor || DEFAULT_COLOR);
        }
    }

    function ensureSizeButton() {
        var $coords = $('#insp-section-coords');
        if (!$coords.length || $('#wppoppop-fa-size-wrap').length) return;
        $coords.append(
            '<div id="wppoppop-fa-size-wrap" style="display:none; margin-top:8px;">' +
                '<button type="button" id="wppoppop-fa-size-btn" class="button button-small" style="width:100%; font-size:11px; font-weight:700;">Apply Icon Preset (40 x 40)</button>' +
            '</div>'
        );
    }

    function bindControls() {
        $(document)
            .off('input.wppoppopFaColor change.wppoppopFaColor', '#prop-icon-color, #wppoppop-fa-color')
            .on('input.wppoppopFaColor change.wppoppopFaColor', '#prop-icon-color, #wppoppop-fa-color', function(e) {
                var el = getActiveEl();
                if (!isIcon(el)) return;
                el.iconColor = $(this).val();
                el.color = el.iconColor;
                applyColorToStage(el);
                if (e.type === 'change') render('Change Icon Color');
            });

        $(document)
            .off('click.wppoppopFaSize')
            .on('click.wppoppopFaSize', '#wppoppop-fa-size-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var el = getActiveEl();
                if (!isIcon(el)) return;
                el.width = PRESET;
                el.height = PRESET;
                $('#prop-width, #prop-size-width').val(PRESET);
                $('#prop-height, #prop-size-height').val(PRESET);
                applySizeToStage(el);
                render('Apply Icon Size Preset');
            });
    }

    function wrapAddElement() {
        var canvas = window.WpPopPopBuilderCanvas;
        if (!canvas || typeof canvas.addElement !== 'function' || canvas.__wppoppopIconPresetWrapped) return;
        canvas.__wppoppopIconPresetWrapped = true;
        var orig = canvas.addElement;
        canvas.addElement = function(type) {
            var before = getList() ? getList().length : 0;
            var res = orig.apply(this, arguments);
            if (TYPES.indexOf(String(type || '').toLowerCase().trim()) !== -1) {
                var list = getList() || [];
                var created = (res && typeof res === 'object' && res.type) ? res : null;
                if (!created && list.length > before) created = list[list.length - 1];
                if (isIcon(created)) {
                    created.width = PRESET;
                    created.height = PRESET;
                    if (!created.iconColor) created.iconColor = DEFAULT_COLOR;
                    applySizeToStage(created);
                    render('New Icon Preset');
                }
            }
            return res;
        };
    }

    function hookInspector() {
        var insp = window.WpPopPopBuilderInspector;
        if (!insp || insp.__wppoppopFaHooked) return;
        insp.__wppoppopFaHooked = true;
        if (typeof insp.open === 'function') {
            var origOpen = insp.open;
            insp.open = function() {
                var res = origOpen.apply(this, arguments);
                setTimeout(refreshUi, 20);
                return res;
            };
        }
    }

    $(function() {
        ensureColorPicker();
        ensureSizeButton();
        bindControls();
        wrapAddElement();
        hookInspector();
        refreshUi();
        setTimeout(wrapAddElement, 150);
        setTimeout(hookInspector, 150);
        setInterval(function() { ensureSizeButton(); refreshUi(); }, 500);
    });
})(window, jQuery);
/* === WPPOPPOP_FA_ICON_CONTROLS_END === */

/* === WPPOPPOP_MEDIA_STYLE_TRIM_START === */
/* WPPOPPOP_MEDIA_STYLE_TRIM_V2: per-type Style-tab section trim.
   Hides via a stylesheet keyed on <html data-wppoppop-trim-type="...">, so jQuery .show()
   calls elsewhere cannot re-display hidden sections. Visibility only; DOM and data untouched. */
(function(window, $) {
    'use strict';

    var HIDE_MAP = {
        'image':      ['insp-section-typography', 'insp-section-custom-css', 'insp-section-bg-color'],
        'shape':      ['insp-section-typography', 'insp-section-custom-css'],
        'rect':       ['insp-section-typography', 'insp-section-custom-css'],
        'rectangle':  ['insp-section-typography', 'insp-section-custom-css'],
        'square':     ['insp-section-typography', 'insp-section-custom-css'],
        'video':      ['insp-section-opacity', 'insp-section-bg-color', 'insp-section-custom-css'],
        'label':      ['insp-section-opacity', 'insp-section-custom-css'],
        'html':       ['insp-section-opacity', 'insp-section-custom-css'],
        'close':      ['insp-section-opacity', 'insp-section-typography', 'insp-section-padding', 'insp-section-shadow', 'insp-section-custom-css'],
        'close_icon': ['insp-section-opacity', 'insp-section-typography', 'insp-section-padding', 'insp-section-shadow', 'insp-section-custom-css'],
        'icon':       ['insp-section-typography', 'insp-section-padding', 'insp-section-shadow', 'insp-section-custom-css'],
        'fontawesome':['insp-section-typography', 'insp-section-padding', 'insp-section-shadow', 'insp-section-custom-css']
    };

    var STYLE_ID = 'wppoppop-style-trim-v2';
    var ATTR = 'data-wppoppop-trim-type';

    function buildCss() {
        var rules = [];
        Object.keys(HIDE_MAP).forEach(function(type) {
            HIDE_MAP[type].forEach(function(id) {
                rules.push('html[' + ATTR + '="' + type + '"] #' + id + ' { display: none !important; }');
            });
        });
        return rules.join('\n');
    }

    function ensureStyle() {
        var s = document.getElementById(STYLE_ID);
        if (!s) {
            s = document.createElement('style');
            s.id = STYLE_ID;
            (document.head || document.documentElement).appendChild(s);
        }
        var css = buildCss();
        if (s.textContent !== css) s.textContent = css;
    }

    function cleanId(raw) {
        return String(raw || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim();
    }

    function getActiveType() {
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.activeId) {
            var activeId = cleanId(core.state.activeId);
            var cur = core.state.currentCanvas || 1;
            var list = (core.state.canvases && core.state.canvases[cur]) || [];
            for (var i = 0; i < list.length; i++) {
                if (list[i] && cleanId(list[i].id) === activeId) {
                    return String(list[i].type || '').toLowerCase().trim();
                }
            }
        }
        var $sel = $('.wppoppop-canvas-item.wppoppop-selected, .wppoppop-canvas-item.selected').first();
        return String($sel.attr('data-type') || '').toLowerCase().trim();
    }

    function applyTrim() {
        ensureStyle();
        var type = getActiveType();
        var root = document.documentElement;
        if (HIDE_MAP.hasOwnProperty(type)) {
            if (root.getAttribute(ATTR) !== type) root.setAttribute(ATTR, type);
        } else if (root.hasAttribute(ATTR)) {
            root.removeAttribute(ATTR);
        }
    }

    function scheduleTrim() {
        applyTrim();
        setTimeout(applyTrim, 0);
        setTimeout(applyTrim, 60);
        setTimeout(applyTrim, 200);
    }

    function hookInspector() {
        var insp = window.WpPopPopBuilderInspector;
        if (!insp || insp.__wppoppopMediaTrimV2Hooked) return;
        insp.__wppoppopMediaTrimV2Hooked = true;

        if (typeof insp.open === 'function') {
            var origOpen = insp.open;
            insp.open = function() {
                var res = origOpen.apply(this, arguments);
                scheduleTrim();
                return res;
            };
        }
        if (typeof insp.switchTab === 'function') {
            var origSwitch = insp.switchTab;
            insp.switchTab = function() {
                var res = origSwitch.apply(this, arguments);
                scheduleTrim();
                return res;
            };
        }
    }

    // Capture phase: runs even when other handlers call stopPropagation()
    window.addEventListener('click', function(e) {
        var t = e.target;
        if (!t || !t.closest) return;
        if (t.closest('.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-insp-tab, #wppoppop-inspector-drawer')) {
            scheduleTrim();
        }
    }, true);

    $(function() {
        ensureStyle();
        hookInspector();
        scheduleTrim();
        setTimeout(hookInspector, 150);
        setTimeout(hookInspector, 450);
        setInterval(applyTrim, 400); // covers selection changes made without a click event
    });
})(window, jQuery);
/* === WPPOPPOP_MEDIA_STYLE_TRIM_END === */

/* === WPPOPPOP_LABEL_TEXT_ALIGN_START === */
/* Label & Html layers: TEXT ALIGNMENT section in Style tab (left / center / right).
   Writes to the element model (el.textAlign) and the stage node. */
(function(window, $) {
    'use strict';

    var SECTION_ID = 'insp-section-text-align';
    var ALIGNS = ['left', 'center', 'right'];
    var TARGET_TYPES = ['label', 'html'];
    function isTargetEl(el) {
        return !!el && TARGET_TYPES.indexOf(String(el.type || '').toLowerCase().trim()) !== -1;
    }

    function cleanId(raw) {
        return String(raw || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim();
    }

    function getActiveEl() {
        var core = window.WpPopPopBuilderCore;
        if (!core || !core.state) return null;
        var activeId = cleanId(core.state.activeId);
        if (!activeId) return null;
        var cur = core.state.currentCanvas || 1;
        var list = (core.state.canvases && core.state.canvases[cur]) || [];
        for (var i = 0; i < list.length; i++) {
            if (list[i] && cleanId(list[i].id) === activeId) return list[i];
        }
        return null;
    }

    function ensureSection() {
        var $existing = $('#' + SECTION_ID);
        if ($existing.length) return $existing;
        var pane = document.getElementById('insp-tab-style');
        if (!pane) return $();

        var btns = ALIGNS.map(function(a) {
            var label = a.charAt(0).toUpperCase() + a.slice(1);
            return '<button type="button" class="button wppoppop-label-align-btn" data-align="' + a +
                   '" style="font-size:11px; font-weight:700;">' + label + '</button>';
        }).join('');

        var $s = $(
            '<div class="wppoppop-insp-section" id="' + SECTION_ID + '" style="display:none; background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">' +
                '<div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">TEXT ALIGNMENT</div>' +
                '<div id="wppoppop-label-align-group" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;">' + btns + '</div>' +
            '</div>'
        );

        var anchor = document.getElementById('insp-section-opacity');
        if (anchor) { $(anchor).before($s); } else { $(pane).prepend($s); }
        return $s;
    }

    function paintButtons(align) {
        $('#wppoppop-label-align-group .wppoppop-label-align-btn').each(function() {
            var on = $(this).attr('data-align') === align;
            $(this).toggleClass('active button-primary', on);
        });
    }

    function applyAlignToStage(el) {
        var id = cleanId(el.id);
        var $node = $('#wppoppop-el-' + id + ', #el-' + id + ', #canvas-elem-' + id + ', .wppoppop-canvas-item[data-id="' + id + '"]').first();
        if ($node.length) {
            $node.css('text-align', el.textAlign);
            $node.find('.wppoppop-label-inner, .wppoppop-title-inner, p, h1, h2, h3, h4, h5, h6, div').first().css('text-align', el.textAlign);
        }
    }

    function hydrate() {
        var el = getActiveEl();
        var $s = ensureSection();
        if (!$s.length) return;
        var isLabel = isTargetEl(el);
        if (!isLabel) {
            $s.hide();
            return;
        }
        var align = ALIGNS.indexOf(el.textAlign) !== -1 ? el.textAlign : 'left';
        paintButtons(align);
        $s.show();
    }

    function scheduleHydrate() {
        hydrate();
        setTimeout(hydrate, 0);
        setTimeout(hydrate, 60);
        setTimeout(hydrate, 200);
    }

    function bindAlignButtons() {
        $(document)
            .off('click.wppoppopLabelAlign')
            .on('click.wppoppopLabelAlign', '#wppoppop-label-align-group .wppoppop-label-align-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var el = getActiveEl();
                if (!isTargetEl(el)) return;
                var align = $(this).attr('data-align');
                if (ALIGNS.indexOf(align) === -1) return;

                el.textAlign = align;
                el.text_align = align;
                paintButtons(align);
                applyAlignToStage(el);

                if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
                if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                    window.WpPopPopBuilderCore.pushHistory('Change Text Alignment');
                }
            });
    }

    function hookInspector() {
        var insp = window.WpPopPopBuilderInspector;
        if (!insp || insp.__wppoppopLabelAlignHooked) return;
        insp.__wppoppopLabelAlignHooked = true;

        if (typeof insp.open === 'function') {
            var origOpen = insp.open;
            insp.open = function() {
                var res = origOpen.apply(this, arguments);
                scheduleHydrate();
                return res;
            };
        }
        if (typeof insp.switchTab === 'function') {
            var origSwitch = insp.switchTab;
            insp.switchTab = function() {
                var res = origSwitch.apply(this, arguments);
                scheduleHydrate();
                return res;
            };
        }
    }

    // Capture phase: runs even when other handlers call stopPropagation()
    window.addEventListener('click', function(e) {
        var t = e.target;
        if (!t || !t.closest) return;
        if (t.closest('.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-insp-tab, #wppoppop-inspector-drawer')) {
            scheduleHydrate();
        }
    }, true);

    $(function() {
        ensureSection();
        bindAlignButtons();
        hookInspector();
        scheduleHydrate();
        setTimeout(hookInspector, 150);
        setTimeout(hookInspector, 450);
    });
})(window, jQuery);
/* === WPPOPPOP_LABEL_TEXT_ALIGN_END === */

/* === WPPOPPOP_CLOSE_GLYPH_HOVER_START === */
/* Close layers: ICON GLYPH hydration/save + BUTTON HOVER STATE visibility & binding.
   Writes el.content / el.closeGlyph and el.hoverBgColor / hoverTextColor / hoverBorderColor. */
(function(window, $) {
    'use strict';

    var CLOSE_TYPES = ['close', 'close_icon'];
    var GLYPHS = ['×', '✕', '✖', '⊗', 'CLOSE'];
    var DEFAULT_GLYPH = '×';

    function cleanId(raw) {
        return String(raw || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim();
    }

    function getActiveEl() {
        var core = window.WpPopPopBuilderCore;
        if (!core || !core.state) return null;
        var activeId = cleanId(core.state.activeId);
        if (!activeId) return null;
        var cur = core.state.currentCanvas || 1;
        var list = (core.state.canvases && core.state.canvases[cur]) || [];
        for (var i = 0; i < list.length; i++) {
            if (list[i] && cleanId(list[i].id) === activeId) return list[i];
        }
        return null;
    }

    function isClose(el) {
        return !!el && CLOSE_TYPES.indexOf(String(el.type || '').toLowerCase().trim()) !== -1;
    }

    function stageNode(el) {
        var id = cleanId(el.id);
        return $('#canvas-elem-' + id + ', #wppoppop-el-' + id + ', #el-' + id + ', .wppoppop-canvas-item[data-id="' + id + '"]').first();
    }

    function resolveGlyph(el) {
        if (GLYPHS.indexOf(el.content) !== -1) return el.content;
        if (GLYPHS.indexOf(el.closeGlyph) !== -1) return el.closeGlyph;
        return DEFAULT_GLYPH;
    }

    function hydrateGlyph(el) {
        var $sel = $('#prop-close-glyph');
        if (!$sel.length) return;
        $sel.val(resolveGlyph(el));
    }

    function hydrateHover(el) {
        $('#prop-btn-hover-section').show();
        $('#prop-elem-hover-bg').val(el.hoverBgColor || el.hoverBg || '#0284c7');
        $('#prop-elem-hover-color').val(el.hoverTextColor || el.hoverColor || '#ffffff');
        $('#prop-elem-hover-border').val(el.hoverBorderColor || '#0284c7');
    }

    function applyHoverToStage(el) {
        var $node = stageNode(el);
        if (!$node.length) return;
        $node.attr('data-has-hover', 'true');
        var node = $node[0];
        if (el.hoverBgColor) node.style.setProperty('--wppoppop-hover-bg', el.hoverBgColor);
        if (el.hoverTextColor) node.style.setProperty('--wppoppop-hover-color', el.hoverTextColor);
        if (el.hoverBorderColor) node.style.setProperty('--wppoppop-hover-border', el.hoverBorderColor);
    }

    function render() {
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
            window.WpPopPopBuilderCanvas.renderCanvas();
        }
    }

    function pushHistory(label) {
        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
            window.WpPopPopBuilderCore.pushHistory(label);
        }
    }

    function hydrate() {
        var el = getActiveEl();
        if (!isClose(el)) return;
        hydrateGlyph(el);
        hydrateHover(el);
    }

    function scheduleHydrate() {
        hydrate();
        setTimeout(hydrate, 0);
        setTimeout(hydrate, 60);
        setTimeout(hydrate, 200);
    }

    function bindControls() {
        $(document)
            .off('change.wppoppopCloseGlyph input.wppoppopCloseGlyph')
            .on('change.wppoppopCloseGlyph input.wppoppopCloseGlyph', '#prop-close-glyph', function() {
                var el = getActiveEl();
                if (!isClose(el)) return;
                var val = $(this).val();
                if (GLYPHS.indexOf(val) === -1) return;
                el.content = val;
                el.closeGlyph = val;
                el.glyph_style = el.glyph_style || 'standard';
                render();
                if (this.getAttribute('data-committed') !== '1' && $(this).is('select')) {
                    pushHistory('Change Close Icon Glyph');
                }
            });

        $(document)
            .off('input.wppoppopCloseHover change.wppoppopCloseHover')
            .on('input.wppoppopCloseHover change.wppoppopCloseHover', '#prop-elem-hover-bg, #prop-elem-hover-color, #prop-elem-hover-border', function(e) {
                var el = getActiveEl();
                if (!isClose(el)) return;
                el.hoverBgColor = $('#prop-elem-hover-bg').val();
                el.hoverBg = el.hoverBgColor;
                el.hoverTextColor = $('#prop-elem-hover-color').val();
                el.hoverColor = el.hoverTextColor;
                el.hoverBorderColor = $('#prop-elem-hover-border').val();
                applyHoverToStage(el);
                if (e.type === 'change') pushHistory('Change Close Hover State');
            });
    }

    function hookInspector() {
        var insp = window.WpPopPopBuilderInspector;
        if (!insp || insp.__wppoppopCloseHooked) return;
        insp.__wppoppopCloseHooked = true;

        if (typeof insp.open === 'function') {
            var origOpen = insp.open;
            insp.open = function() {
                var res = origOpen.apply(this, arguments);
                scheduleHydrate();
                return res;
            };
        }
        if (typeof insp.switchTab === 'function') {
            var origSwitch = insp.switchTab;
            insp.switchTab = function() {
                var res = origSwitch.apply(this, arguments);
                scheduleHydrate();
                return res;
            };
        }
    }

    // Capture phase: runs even when other handlers call stopPropagation()
    window.addEventListener('click', function(e) {
        var t = e.target;
        if (!t || !t.closest) return;
        if (t.closest('.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-insp-tab, #wppoppop-inspector-drawer')) {
            scheduleHydrate();
        }
    }, true);

    $(function() {
        bindControls();
        hookInspector();
        scheduleHydrate();
        setTimeout(hookInspector, 150);
        setTimeout(hookInspector, 450);
    });
})(window, jQuery);
/* === WPPOPPOP_CLOSE_GLYPH_HOVER_END === */
/* === WPPOPPOP_INSPECTOR_ICON_ONLY_ACTIVE_ON_CLICK_END === */


/* === WPPOPPOP_CLOSE_PRESET_HOVER_FIX_START === */
(function(window, $) {
    'use strict';
    function cid(r) { return String(r || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim(); }
    function activeEl() {
        var core = window.WpPopPopBuilderCore;
        if (!core || !core.state) return null;
        var id = cid(core.state.activeId); if (!id) return null;
        var l = (core.state.canvases && core.state.canvases[core.state.currentCanvas || 1]) || [];
        for (var i = 0; i < l.length; i++) { if (l[i] && cid(l[i].id) === id) return l[i]; }
        return null;
    }
    function isClose(el) { return !!el && ['close', 'close_icon'].indexOf(String(el.type || '').toLowerCase()) !== -1; }
    function stage(el) {
        var id = cid(el.id);
        return $('#canvas-elem-' + id + ', #wppoppop-el-' + id + ', #el-' + id + ', .wppoppop-canvas-item[data-id="' + el.id + '"]').first();
    }
    function applyHover(el) {
        var $n = stage(el); if (!$n.length) return;
        var bg = el.hoverBgColor || el.hoverBg, fg = el.hoverTextColor || el.hoverColor, bd = el.hoverBorderColor || el.hoverBorder;
        if (!bg && !fg && !bd) return;
        $n.attr('data-has-hover', 'true');
        if (bg) $n[0].style.setProperty('--wppoppop-hover-bg', bg);
        if (fg) $n[0].style.setProperty('--wppoppop-hover-color', fg);
        if (bd) $n[0].style.setProperty('--wppoppop-hover-border', bd);
    }
    /* Phase: 35x35 preset (only for untouched / legacy 36x36 close layers) */
    function applyPreset(el) {
        if (el.sizePresetApplied) return false;
        var w = parseInt(el.width, 10) || 0, h = parseInt(el.height, 10) || 0;
        if (w !== 35 || h !== 35) { el.width = 35; el.height = 35; }
        el.sizePresetApplied = true;
        return true;
    }
    function syncDims(el) {
        $('#prop-width, #prop-size-width').val(parseInt(el.width, 10) || 35);
        $('#prop-height, #prop-size-height').val(parseInt(el.height, 10) || 35);
    }
    function showHoverUI() {
        $('#prop-btn-hover-section').css('display', 'block');
    }
    function run() {
        var el = activeEl(); if (!isClose(el)) return;
        if (applyPreset(el)) {
            if (window.WpPopPopBuilderCanvas && window.WpPopPopBuilderCanvas.renderCanvas) window.WpPopPopBuilderCanvas.renderCanvas();
        }
        syncDims(el);
        $('#prop-close-glyph').val(el.closeGlyph || el.content || '\u00d7');
        showHoverUI();
        $('#prop-elem-hover-bg').val(el.hoverBgColor || el.hoverBg || '#0284c7');
        $('#prop-elem-hover-color').val(el.hoverTextColor || el.hoverColor || '#ffffff');
        $('#prop-elem-hover-border').val(el.hoverBorderColor || el.hoverBorder || '#0284c7');
        applyHover(el);
    }
    function schedule() { [0, 60, 200, 350, 700, 1200].forEach(function(ms) { setTimeout(run, ms); }); }

    function wrapRender() {
        var cv = window.WpPopPopBuilderCanvas;
        if (!cv || cv.__closeHoverWrapped || typeof cv.renderCanvas !== 'function') return;
        cv.__closeHoverWrapped = true;
        var orig = cv.renderCanvas;
        cv.renderCanvas = function() {
            var r = orig.apply(this, arguments);
            try {
                var core = window.WpPopPopBuilderCore;
                var l = (core && core.state && core.state.canvases && core.state.canvases[core.state.currentCanvas || 1]) || [];
                l.forEach(function(e) { if (isClose(e)) applyHover(e); });
            } catch (e) {}
            return r;
        };
    }
    /* Resize inputs -> model sync stays handled by existing #prop-width/#prop-height binding. */
    $(function() { wrapRender(); setTimeout(wrapRender, 150); setTimeout(wrapRender, 450); schedule(); });
    window.addEventListener('click', function(e) {
        var t = e.target;
        if (t && t.closest && t.closest('.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-insp-tab, #wppoppop-inspector-drawer')) schedule();
    }, true);
})(window, jQuery);
/* === WPPOPPOP_CLOSE_PRESET_HOVER_FIX_END === */

/* === WPPOPPOP_CLOSE_HOVER_LIVE_START === */
(function(window, $) {
    'use strict';
    var MAP = { hoverBgColor: ['hoverBgColor','hoverBg'], hoverTextColor: ['hoverTextColor','hoverColor'], hoverBorderColor: ['hoverBorderColor','hoverBorder'] };
    function cid(r) { return String(r || '').replace(/^wppoppop-el-/, '').replace(/^el-/, '').trim(); }
    function list() {
        var c = window.WpPopPopBuilderCore;
        return (c && c.state && c.state.canvases && c.state.canvases[c.state.currentCanvas || 1]) || [];
    }
    function byId(id) {
        id = cid(id);
        var l = list();
        for (var i = 0; i < l.length; i++) { if (l[i] && cid(l[i].id) === id) return l[i]; }
        return null;
    }
    function isClose(el) { return !!el && ['close', 'close_icon'].indexOf(String(el.type || '').toLowerCase()) !== -1; }
    function activeEl() { var c = window.WpPopPopBuilderCore; return (c && c.state) ? byId(c.state.activeId) : null; }

    /* Inspector colour inputs (any duplicate) -> model of the active Close layer */
    $(document).off('input.wppCloseHv change.wppCloseHv')
      .on('input.wppCloseHv change.wppCloseHv',
          '[data-prop="hoverBgColor"], [data-prop="hoverTextColor"], [data-prop="hoverBorderColor"]',
          function(e) {
        var el = activeEl(); if (!isClose(el)) return;
        var prop = $(this).attr('data-prop'), val = $(this).val();
        (MAP[prop] || []).forEach(function(k) { el[k] = val; });
        $('[data-prop="' + prop + '"]').not(this).val(val);
        if (e.type === 'change' && window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.pushHistory) {
            window.WpPopPopBuilderCore.pushHistory('Change Close Hover State');
        }
    });

    /* Live hover on stage nodes (no dependency on data-type / CSS variables) */
    $(document).off('mouseenter.wppCloseHv mouseleave.wppCloseHv', '.wppoppop-canvas-item')
      .on('mouseenter.wppCloseHv', '.wppoppop-canvas-item', function() {
        var $n = $(this);
        var el = byId($n.attr('data-id') || $n.attr('id'));
        if (!isClose(el)) return;
        var bg = el.hoverBgColor || el.hoverBg, fg = el.hoverTextColor || el.hoverColor, bd = el.hoverBorderColor || el.hoverBorder;
        $n.data('wppHvSave', { bg: this.style.backgroundColor, color: this.style.color, bd: this.style.borderColor });
        if (bg) this.style.setProperty('background-color', bg, 'important');
        if (fg) this.style.setProperty('color', fg, 'important');
        if (bd) this.style.setProperty('border-color', bd, 'important');
    }).on('mouseleave.wppCloseHv', '.wppoppop-canvas-item', function() {
        var sv = $(this).data('wppHvSave'); if (!sv) return;
        this.style.removeProperty('background-color'); this.style.removeProperty('color'); this.style.removeProperty('border-color');
        if (sv.bg) this.style.backgroundColor = sv.bg;
        if (sv.color) this.style.color = sv.color;
        if (sv.bd) this.style.borderColor = sv.bd;
        $(this).removeData('wppHvSave');
    });
})(window, jQuery);
/* === WPPOPPOP_CLOSE_HOVER_LIVE_END === */

/* === WPPOPPOP_CLOSE_GLYPH_COLOR_START === */
(function(window,$){
    'use strict';
    function cid(r){return String(r||'').replace(/^wppoppop-el-/,'').replace(/^el-/,'').trim();}
    function activeEl(){
        var c=window.WpPopPopBuilderCore; if(!c||!c.state)return null;
        var id=cid(c.state.activeId); if(!id)return null;
        var l=(c.state.canvases&&c.state.canvases[c.state.currentCanvas||1])||[];
        for(var i=0;i<l.length;i++){ if(l[i]&&cid(l[i].id)===id) return l[i]; }
        return null;
    }
    function run(){
        var el=activeEl();
        if(!el||['close','close_icon'].indexOf(String(el.type||'').toLowerCase())===-1) return;
        var v=/^#[0-9a-f]{6}$/i.test(el.color||'')?el.color:'#64748b';
        $('#prop-close-glyph-color').val(v);
    }
    function sched(){[0,60,200,350,700,1200].forEach(function(ms){setTimeout(run,ms);});}
    window.addEventListener('click',function(e){
        var t=e.target;
        if(t&&t.closest&&t.closest('.wppoppop-canvas-item, .wppoppop-layer-item, .wppoppop-insp-tab, #wppoppop-inspector-drawer')) sched();
    },true);
    $(function(){sched();});
})(window,jQuery);
/* === WPPOPPOP_CLOSE_GLYPH_COLOR_END === */
