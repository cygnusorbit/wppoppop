
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
        $(document).off('click.wppoppop_anim_play').on('click.wppoppop_anim_play', '#prop-anim-play-btn, #prop-anim-replay-btn, #prop-anim-test-btn, .wppoppop-anim-play-btn', function(e) {
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

            var effect = $('#prop-anim-appearance').val() || $('#prop-animation').val() || $('#prop-anim-effect').val();
            if (!effect || effect === 'none') {
                if (activeEl) {
                    effect = activeEl.anim_appearance || activeEl.animEffect || activeEl.animation || activeEl.anim_effect;
                }
            }
            if (!effect || effect === 'none') {
                effect = 'bounce';
            }

            var duration = parseInt($('#prop-anim-duration').val(), 10) || 1000;
            var delay = parseInt($('#prop-anim-delay').val(), 10) || 0;

            if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.playAnimation === 'function') {
                window.WpPopPopBuilderCanvas.playAnimation(activeId, effect, duration, delay);
            }
        });

        $(document).off('change.wppoppop_anim_sel').on('change.wppoppop_anim_sel', '#prop-anim-appearance, #prop-animation, #prop-anim-effect', function() {
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
        jQuery('#prop-anim-in').val(el.animIn || el.animation || 'none');
        jQuery('#prop-anim-out').val(el.animOut || 'none');
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
            this.bindAnimationPlayControls();
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
            $('#prop-video-url').val(el.url || el.src || '');
            $('#prop-video-autoplay').prop('checked', !!el.autoplay);
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

        // Animation
        if ($('#prop-anim-appearance').length) {
            $('#prop-anim-appearance').val(el.anim_appearance || el.animation || el.animEffect || 'none');
        }
        if ($('#prop-anim-duration').length) {
            $('#prop-anim-duration').val(el.anim_duration || el.duration || 1000);
        }
        if ($('#prop-anim-delay').length) {
            $('#prop-anim-delay').val(el.anim_delay || el.delay || 0);
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
                    el.imageAlt = $('#prop-image-alt').val();
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
                this.style.setProperty('flex', '1', 'important');
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

/* === WPPOPPOP_PHASE2_MEDIA_DECOMPOSED_MODULES_START === */
(function(window, $) {
    'use strict';

    var MEDIA_PANEL_MAP = {
        'shape': '#panel-elem-shape',
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
        'submit_btn': '#panel-elem-submit'
    };

    var TEXT_BEARING_MEDIA = ['label', 'title', 'paragraph', 'html', 'button', 'link', 'link_btn', 'step_btn', 'submit', 'submit_btn', 'progress', 'close', 'close_icon'];
    var HOVER_BUTTON_MEDIA = ['button', 'link', 'link_btn', 'step_btn', 'submit', 'submit_btn'];

    function getActiveStageElement(idOrObj) {
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

    function hydrateMediaModule(elOrType) {
        var el = (elOrType && typeof elOrType === 'object') ? elOrType : getActiveStageElement(elOrType);
        var type = (el && el.type) ? String(el.type).toLowerCase().trim() : (typeof elOrType === 'string' ? elOrType.toLowerCase().trim() : '');
        if (!type) return;

        // If this is one of the 10 Media Family elements, hide legacy panels & show its standalone decomposed module
        var targetPanel = MEDIA_PANEL_MAP[type];
        if (targetPanel) {
            $('[id$="-legacy"]').hide();
            $('.element-panel, .wppoppop-panel-specific').hide();$(targetPanel).show();

            // Contextual Style Tab Isolation for Media Elements
            var hasText = TEXT_BEARING_MEDIA.indexOf(type) !== -1;
            var isBtn = HOVER_BUTTON_MEDIA.indexOf(type) !== -1;
            $('#prop-line-height, #prop-letter-spacing').closest('.wppoppop-insp-row, .wppoppop-field-row, div').toggle(hasText);
            $('#prop-btn-hover-section, #prop-btn-hover-bg').closest('.wppoppop-insp-card, div').toggle(isBtn);
        }

        if (!el || !targetPanel) return;

                if (type === 'image') {
            var hydratedUrl = el.imageUrl || el.src || el.url || el.content || '';
            var hydratedFit = el.objectFit || el.imageFit || 'cover';
            var hydratedAlt = el.imageAlt || el.alt || '';
            $('#prop-image-url').val(hydratedUrl);
            $('#prop-image-fit').val(hydratedFit);
            $('#prop-image-alt').val(hydratedAlt);
            if (hydratedUrl && hydratedUrl.indexOf('http') === 0) {
                $('#prop-image-preview').attr('src', hydratedUrl).show();
                $('#prop-image-placeholder').hide();
            } else {
                $('#prop-image-preview').attr('src', '').hide();
                $('#prop-image-placeholder').show();
            }
        }

        // Hydrate `.wppoppop-gp-bind` inputs inside the active standalone Media panel
        $(targetPanel).find('.wppoppop-gp-bind').each(function() {
            var $inp =$(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            var val = el[prop];
            if (val === undefined) {
                if (prop === 'content') val = el.label || el.titleText || el.paragraphText || el.text || '';
                if (prop === 'tag') val = el.headingTag || el.htmlTag || 'div';
                if (prop === 'src') val = el.imageUrl || el.url || '';
                if (prop === 'linkUrl') val = el.url || '';
            }
            if ($inp.is(':checkbox')) {$inp.prop('checked', !!val);
            } else if (val !== undefined && val !== null) {
                $inp.val(val);
            }
        });
    }

    function bindMediaModuleEvents() {
        // Two-way live binding for all standalone Media `.wppoppop-gp-bind` controls
        $(document).off('.wppoppopMediaModules').on('input.wppoppopMediaModules change.wppoppopMediaModules', '[data-elem-family="media"] .wppoppop-gp-bind', function() {
            var el = getActiveStageElement();
            if (!el) return;
            var $inp =$(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            var val = $inp.is(':checkbox') ? $inp.is(':checked') :$inp.val();
            el[prop] = val;

            // Sync aliases for backward/forward compatibility across all 6 tiers
            if (prop === 'content') {
                el.label = val;
                el.titleText = val;
                el.paragraphText = val;
                el.text = val;
            } else if (prop === 'tag') {
                el.headingTag = val;
                el.htmlTag = val;
            } else if (prop === 'src') {
                el.imageUrl = val;
                el.url = val;
            } else if (prop === 'linkUrl') {
                el.url = val;
            } else if (prop === 'transparentFill') {
                if (val) el.bgColor = 'transparent';
            }

            if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                window.WpPopPopBuilderCanvas.renderCanvas();
            }
        });

        // Native WordPress Media Library Picker for Element 2 (Image)
        var wppoppopMediaFrame = null;
        $(document).off('click.wppoppopWpMedia').on(
            'click.wppoppopWpMedia',
            '#prop-image-upload-btn, #wppoppop-btn-media-upload, .wppoppop-media-upload-trigger',
            function(e) {
                e.preventDefault();
                e.stopPropagation();

                var applySelectedImage = function(url, altText) {
                    var el = getActiveStageElement();
                    $('#prop-image-url').val(url);
                    if (altText !== undefined && altText !== '') {
                        $('#prop-image-alt').val(altText);
                    }
                    if (url) {
                        $('#prop-image-preview').attr('src', url).show();
                        $('#prop-image-placeholder').hide();
                    } else {
                        $('#prop-image-preview').attr('src', '').hide();
                        $('#prop-image-placeholder').show();
                    }
                    if (el) {
                        el.imageUrl = url;
                        el.src = url;
                        el.url = url;
                        el.content = url;
                        if (altText !== undefined && altText !== '') {
                            el.imageAlt = altText;
                            el.alt = altText;
                        }
                        el.objectFit = $('#prop-image-fit').val() || el.objectFit || 'cover';
                        el.imageFit = el.objectFit;
                    }
                    if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                        window.WpPopPopBuilderCanvas.renderCanvas();
                    }
                };

                if (typeof window.wp !== 'undefined' && window.wp && typeof window.wp.media === 'function') {
                    if (wppoppopMediaFrame) {
                        wppoppopMediaFrame.open();
                        return;
                    }
                    wppoppopMediaFrame = window.wp.media({
                        title: 'Select or Upload Image',
                        button: { text: 'Use this Image' },
                        library: { type: 'image' },
                        multiple: false
                    });
                    wppoppopMediaFrame.on('select', function() {
                        var state = wppoppopMediaFrame.state();
                        if (!state) return;
                        var selection = state.get('selection');
                        if (!selection || !selection.first()) return;
                        var attachment = selection.first().toJSON();
                        if (attachment && attachment.url) {
                            applySelectedImage(attachment.url, attachment.alt || attachment.title || '');
                        }
                    });
                    wppoppopMediaFrame.open();
                } else {
                    var fallbackUrl = window.prompt('Enter Image URL:', $('#prop-image-url').val() || 'https://');
                    if (fallbackUrl !== null) {
                        applySelectedImage($.trim(fallbackUrl), '');
                    }
                }
            }
        );

        // Remove Image Button Handler (#prop-image-remove-btn)
        $(document).off('click.wppoppopWpMediaRemove').on(
            'click.wppoppopWpMediaRemove',
            '#prop-image-remove-btn, .wppoppop-media-remove-trigger',
            function(e) {
                e.preventDefault();
                e.stopPropagation();
                var el = getActiveStageElement();
                $('#prop-image-url').val('');
                $('#prop-image-alt').val('');
                $('#prop-image-preview').attr('src', '').hide();
                $('#prop-image-placeholder').show();
                if (el) {
                    el.imageUrl = '';
                    el.src = '';
                    el.url = '';
                    el.content = '';
                }
                if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            }
        );

        // Live sync manual typing in #prop-image-url, #prop-image-fit, #prop-image-alt
        $(document).off('input.wppoppopImgUrl change.wppoppopImgUrl').on(
            'input.wppoppopImgUrl change.wppoppopImgUrl',
            '#prop-image-url, #prop-image-fit, #prop-image-alt',
            function() {
                var el = getActiveStageElement();
                var url = $.trim($('#prop-image-url').val() || '');
                var fit = $('#prop-image-fit').val() || 'cover';
                var alt = $('#prop-image-alt').val() || '';
                if (url) {
                    $('#prop-image-preview').attr('src', url).show();
                    $('#prop-image-placeholder').hide();
                } else {
                    $('#prop-image-preview').attr('src', '').hide();
                    $('#prop-image-placeholder').show();
                }
                if (el) {
                    el.imageUrl = url;
                    el.src = url;
                    el.url = url;
                    el.content = url;
                    el.objectFit = fit;
                    el.imageFit = fit;
                    el.imageAlt = alt;
                    el.alt = alt;
                }
                if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            }
        );

        // 1-Click Icon Preset Selector for Element 7 (Font Awesome / Icon)
        $(document).off('click.wppoppopMediaIconPreset').on('click.wppoppopMediaIconPreset', '#panel-elem-icon .wppoppop-icon-preset', function(e) {
            e.preventDefault();
            var iconCls = $(this).data('icon');
            if (iconCls) {
                $('#prop-icon-class').val(iconCls).trigger('change');
            }
        });

        // Hook into Inspector open / showElementPanel / stage clicks
        var insp = window.WpPopPopBuilderInspector;
        if (insp && !insp._phase2MediaHooked) {
            insp._phase2MediaHooked = true;
            var origOpen = insp.open;
            insp.open = function(id) {
                var res = typeof origOpen === 'function' ? origOpen.apply(this, arguments) : undefined;
                hydrateMediaModule(id);
                setTimeout(function() { hydrateMediaModule(id); }, 25);
                return res;
            };
            var origShow = insp.showElementPanel;
            if (typeof origShow === 'function') {
                insp.showElementPanel = function(type) {
                    var res = origShow.apply(this, arguments);
                    hydrateMediaModule(type);
                    return res;
                };
            }
        }

        $(document).off('click.wppoppopMediaSelect').on('click.wppoppopMediaSelect', '.wppoppop-ribbon-btn, .wppoppop-ribbon-tool, .wppoppop-canvas-item, .wppoppop-layer-item', function() {
            var t = $(this).attr('data-type') ||$(this).data('type') || '';
            setTimeout(function() { hydrateMediaModule(t); }, 30);
        });
    }

    $(function() {
        bindMediaModuleEvents();
        setTimeout(bindMediaModuleEvents, 120);
    });
    bindMediaModuleEvents();
})(window, jQuery);
/* === WPPOPPOP_PHASE2_MEDIA_DECOMPOSED_MODULES_END === */

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
__WPPOPPOP_INSPECTOR_LAYOUT_FIX_JS_V1__
(function($){
    'use strict';

    var GUARD =  + JS_GUARD + r;

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
__WPPOPPOP_LAYER_PANEL_SYNC_V1__
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

