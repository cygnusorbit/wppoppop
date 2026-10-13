
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
    
        init: function() {
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
