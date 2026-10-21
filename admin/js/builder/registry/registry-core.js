/**
 * WpPopPop Builder Registry Module: registry-core
 * Split from builder-elements-registry.js (load order matters; see class-admin-assets.php)
 */
/**
 * WpPopPop Decomposed 26-Element Registry, Family Tab Router & Slide-In Coordinator
 * Safely extends WpPopPopBuilderCanvas, WpPopPopBuilderInspector, and WpPopPopBuilderLayers
 * without modifying or risking syntax errors in core files.
 */
(function(window, $) {
    'use strict';

    var MEDIA_FAMILY = [
        'shape', 'image', 'video', 'label', 'html',
        'close', 'icon', 'progress', 'button', 'submit',
        'title', 'paragraph', 'close_icon', 'link', 'link_btn', 'step_btn'
    ];

    var FORM_FAMILY = [
        'text', 'email', 'number', 'phone', 'textarea',
        'select', 'checkbox', 'radio', 'multiselect', 'imageselect',
        'tile', 'date', 'time', 'upload', 'password', 'rating',
        'textfield', 'tel', 'dropdown', 'checkboxes', 'radios', 'file', 'checkboxgroup'
    ];

    var PANEL_MAP = {
        'shape': '#panel-elem-shape',
        'image': '#panel-elem-image',
        'video': '#panel-elem-video',
        'label': '#panel-elem-label, #panel-elem-title',
        'title': '#panel-elem-title, #panel-elem-label',
        'paragraph': '#panel-elem-paragraph, #panel-elem-text',
        'html': '#panel-elem-html',
        'close': '#panel-elem-close',
        'close_icon': '#panel-elem-close',
        'icon': '#panel-elem-icon',
        'progress': '#panel-elem-progress',
        'button': '#panel-elem-button, #panel-elem-link_btn, #panel-elem-link',
        'link': '#panel-elem-link, #panel-elem-button, #panel-elem-link_btn',
        'link_btn': '#panel-elem-link_btn, #panel-elem-button',
        'step_btn': '#panel-elem-step_btn',
        'text': '#panel-elem-text',
        'textfield': '#panel-elem-text',
        'email': '#panel-elem-email',
        'number': '#panel-elem-number',
        'phone': '#panel-elem-phone',
        'tel': '#panel-elem-phone',
        'textarea': '#panel-elem-textarea',
        'select': '#panel-elem-select',
        'dropdown': '#panel-elem-select',
        'checkbox': '#panel-elem-checkbox, #panel-elem-checkboxes',
        'checkboxes': '#panel-elem-checkboxes, #panel-elem-checkbox',
        'radio': '#panel-elem-radio, #panel-elem-radios',
        'checkboxgroup': '#panel-elem-checkboxgroup',
        'radios': '#panel-elem-radios, #panel-elem-radio',
        'multiselect': '#panel-elem-multiselect',
        'imageselect': '#panel-elem-imageselect',
        'tile': '#panel-elem-tile',
        'date': '#panel-elem-date',
        'time': '#panel-elem-time',
        'upload': '#panel-elem-upload, #panel-elem-file',
        'file': '#panel-elem-file, #panel-elem-upload',
        'password': '#panel-elem-password',
        'rating': '#panel-elem-rating',
        'submit': '#panel-elem-submit'
    };

    var WpPopPopElementsRegistry = {
        MEDIA_FAMILY: MEDIA_FAMILY,
        FORM_FAMILY: FORM_FAMILY,
        _initialized: false,

        getFamily: function(rawType) {
            var t = String(rawType || '').toLowerCase().trim();
            return FORM_FAMILY.indexOf(t) !== -1 ? 'form' : 'media';
        },

        ensureFiveTabDOM: function() {
            var $drawer = $('#wppoppop-inspector-drawer');
            if (!$drawer.length) return;
            // Purge stray Data/Advanced buttons that were injected into the title header (next to the X button)
            $drawer.find('.wppoppop-inspector-header [data-tab], header [data-tab]').not('#wppoppop-inspector-tabs-bar [data-tab]').remove();
            var $header = $drawer.find('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs').first();
            if ($header.length) {
                // Rename Basic to Content and hide redundant Settings tab button
                var $basicBtn = $header.find('[data-tab="basic"], [data-tab="content"]').first();
                if ($basicBtn.length) {
                    $basicBtn.text('Content').attr('id', 'insp-tab-nav-content');
                }
                $header.find('[data-tab="settings"]').hide();

                // Inject Data & Advanced tab buttons if missing from header
                if (!$header.find('[data-tab="data"]').length) {
                    $('<button type="button" class="wppoppop-insp-tab wppoppop-insp-tab-btn wppoppop-form-family-tab" id="insp-tab-nav-data" data-tab="data" style="display:none;">Data</button>').appendTo($header);
                }
                if (!$header.find('[data-tab="advanced"]').length) {
                    $('<button type="button" class="wppoppop-insp-tab wppoppop-insp-tab-btn wppoppop-form-family-tab" id="insp-tab-nav-advanced" data-tab="advanced" style="display:none;">Advanced</button>').appendTo($header);
                }
            }
            // Ensure #insp-tab-data and #insp-tab-advanced panes sit inside .wppoppop-inspector-body
            var $body = $drawer.find('.wppoppop-inspector-body').first();
            if ($body.length) {
                var $dataPane = $('#insp-tab-data');
                var $advPane = $('#insp-tab-advanced');
                if ($dataPane.length && !$dataPane.parent().is($body)) {
                    $body.append($dataPane);
                }
                if ($advPane.length && !$advPane.parent().is($body)) {
                    $body.append($advPane);
                }
            }
        },

        resolveElement: function(arg) {
            if (arg && typeof arg === 'object' && arg.type) {
                return arg;
            }
            var rawId = (arg !== undefined && arg !== null && typeof arg !== 'object')
                ? String(arg)
                : (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? String(window.WpPopPopBuilderCore.state.activeId || '') : '');
            var cleanId = rawId.replace(/^wppoppop-el-/, '').replace(/^el-/, '');

            if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') {
                var list = window.WpPopPopBuilderCanvas.getActiveElements() || [];
                if (Array.isArray(list)) {
                    var found = list.find(function(item) {
                        return item && (String(item.id) === cleanId || String(item.id) === rawId);
                    });
                    if (found) return found;
                }
            }
            if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.canvases) {
                var cur = window.WpPopPopBuilderCore.state.currentCanvas || 0;
                var cData = window.WpPopPopBuilderCore.state.canvases[cur];
                var arr = (cData && cData.elements) ? cData.elements : cData;
                if (Array.isArray(arr)) {
                    var foundCore = arr.find(function(item) {
                        return item && (String(item.id) === cleanId || String(item.id) === rawId);
                    });
                    if (foundCore) return foundCore;
                }
            }
            // Fallback: check currently selected stage DOM node
            var $sel = $('.wppoppop-canvas-item.selected, .wppoppop-canvas-item.wppoppop-selected, .wppoppop-element.selected').first();
            if ($sel.length) {
                var domType = $sel.attr('data-type') || $sel.attr('data-element-type');
                var domId = $sel.attr('data-id') || $sel.attr('id') || cleanId;
                if (domType) {
                    return { id: domId, type: domType };
                }
            }
            return null;
        },

        forceSlideInDrawer: function(isOpen) {
            this.ensureFiveTabDOM();
            var $drawer = $('#wppoppop-inspector-drawer');
            var $ws = $('#wppoppop-builder-workspace, .wppoppop-builder-workspace').first();
            if ($drawer.length && $ws.length && !$drawer.parent().is($ws)) {
                $ws.append($drawer);
            }
            if (!$drawer.length) return;
            var dNode = $drawer[0];
            if (isOpen) {
                $drawer.removeClass('is-closed closed').addClass('is-open open');
                dNode.style.setProperty('display', 'flex', 'important');
                dNode.style.setProperty('visibility', 'visible', 'important');
                dNode.style.setProperty('transform', 'translate3d(0, 0, 0)', 'important');
                dNode.style.setProperty('-webkit-transform', 'translate3d(0, 0, 0)', 'important');
                dNode.style.setProperty('pointer-events', 'auto', 'important');
                $('.wppoppop-builder-workspace, .wppoppop-main-frame, body').addClass('panel-open inspector-open');
            } else {
                $drawer.removeClass('is-open open').addClass('is-closed');
                dNode.style.setProperty('transform', 'translate3d(100%, 0, 0)', 'important');
                dNode.style.setProperty('-webkit-transform', 'translate3d(100%, 0, 0)', 'important');
                dNode.style.setProperty('visibility', 'hidden', 'important');
                dNode.style.setProperty('pointer-events', 'none', 'important');
                $('.wppoppop-builder-workspace, .wppoppop-main-frame, body').removeClass('panel-open inspector-open');
            }

            if (window.WpPopPopBuilderLayers) {
                if (typeof window.WpPopPopBuilderLayers.pushForInspector === 'function') {
                    window.WpPopPopBuilderLayers.pushForInspector(!!isOpen);
                } else if (typeof window.WpPopPopBuilderLayers.pushLayerPanel === 'function') {
                    window.WpPopPopBuilderLayers.pushLayerPanel(!!isOpen);
                }
            }
        },

                syncMediaSubRows: function(el) {
            if (!el) return;
            var cAction = $('#prop-close-action').val() || el.closeAction || 'close';
            $('#prop-close-cookie-wrap').toggle(cAction === 'close_forever');

            var bAction = $('#prop-button-action').val() || el.buttonAction || 'url';
            $('#prop-button-url-wrap').toggle(bAction === 'url' || bAction === 'link');

            var sAction = $('#prop-submit-action').val() || el.submitAction || 'message';
            $('#prop-submit-msg-wrap').toggle(sAction === 'message');
            $('#prop-submit-redir-wrap').toggle(sAction === 'redirect');

            var pMode = $('#prop-progress-mode').val() || el.progressMode || 'manual';
            $('#prop-progress-val-wrap').toggle(pMode === 'manual');
        },

                renderFormInputWithIcons: function(el, innerText) {
            var align = el.textAlign || 'left';
            var iLeft = $.trim(el.iconLeft || '');
            var iRight = $.trim(el.iconRight || '');
            var offL = parseInt(el.iconLeftOffset, 10) || 10;
            var offR = parseInt(el.iconRightOffset, 10) || 10;
            var padL = iLeft ? (offL + 22) : 10;
            var padR = iRight ? (offR + 22) : 10;
            var leftHtml = iLeft ? '<i class="' + iLeft + '" style="position:absolute;left:' + offL + 'px;top:50%;transform:translateY(-50%);color:#64748b;font-size:14px;pointer-events:none;"></i>' : '';
            var rightHtml = iRight ? '<i class="' + iRight + '" style="position:absolute;right:' + offR + 'px;top:50%;transform:translateY(-50%);color:#64748b;font-size:14px;pointer-events:none;"></i>' : '';
            return '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;padding-left:' + padL + 'px;padding-right:' + padR + 'px;text-align:' + align + ';border:1px solid #cbd5e1;border-radius:inherit;background:#ffffff;color:#475569;font-size:12px;box-sizing:border-box;pointer-events:none;">' + leftHtml + '<span style="width:100%;text-align:' + align + ';overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + innerText + '</span>' + rightHtml + '</div>';
        },

        syncFamilyTabs: function(rawType, inspector) {
            this.ensureFiveTabDOM();
            var t = String(rawType || '').toLowerCase().trim();
            var family = this.getFamily(rawType);
            var isForm = (family === 'form');

            $('.wppoppop-form-family-tab, .wppoppop-tab-form-only, #insp-tab-nav-data, #insp-tab-nav-advanced, [data-tab="data"], [data-tab="advanced"]').each(function() {
                if (isForm) {
                    this.style.setProperty('display', 'inline-flex', 'important');
                    this.style.setProperty('align-items', 'center', 'important');
                    this.style.setProperty('justify-content', 'center', 'important');
                } else {
                    this.style.setProperty('display', 'none', 'important');
                }
            });

            // Hide legacy Settings tab button so header strictly shows 3 tabs (Media) or 5 tabs (Form)
            $('.wppoppop-inspector-header [data-tab="settings"], .wppoppop-inspector-tabs [data-tab="settings"]').each(function() {
                this.style.setProperty('display', 'none', 'important');
            });

            $('#prop-display-token').closest('div').toggle(isForm);
            // Contextual Style Tab Scoping for Media Family (Elements 1-9 & 26)
            if (!isForm) {
                var textMedia = ['label', 'title', 'paragraph', 'html', 'button', 'link', 'link_btn', 'step_btn', 'submit', 'progress'];
                var hoverMedia = ['button', 'link', 'link_btn', 'step_btn', 'submit'];
                $('#insp-section-typography').toggle(textMedia.indexOf(t) !== -1);
                $('#wppoppop-hover-state-card').toggle(hoverMedia.indexOf(t) !== -1);
            }

            var activeTab = (inspector && inspector.activeTab) ? inspector.activeTab : 'basic';
            if (!isForm && (activeTab === 'data' || activeTab === 'advanced')) {
                if (inspector && typeof inspector.switchTab === 'function') {
                    inspector.switchTab('basic');
                }
            }
        },

        hydrateElement: function(el, inspector) {
            if (!el) return;
            var t = String(el.type || 'label').toLowerCase().trim();
            this.syncFamilyTabs(t, inspector);
            if (typeof this.syncMediaSubRows === 'function') { this.syncMediaSubRows(el); }

            // Ensure both #insp-tab-settings and #insp-tab-basic show when on Content/Basic tab
            var curTab = (inspector && inspector.activeTab) ? inspector.activeTab : 'basic';
            if (curTab === 'basic' || curTab === 'content' || curTab === 'settings') {
                $('#insp-tab-settings, #insp-tab-basic').addClass('active').show();
            }

            // Show the specific panel for this element among all 26 types
            $('.element-panel, .wppoppop-panel-specific').hide();
            var targetSelector = PANEL_MAP[t] || ('#panel-elem-' + t);
            $(targetSelector).show();

            // Hydrate decomposed module inputs (.wppoppop-gp-bind)
            $('.wppoppop-gp-bind').each(function() {
                var $inp = $(this);
                var prop = $inp.data('prop');
                if (!prop) return;
                var val = el[prop];
                if ($inp.is(':checkbox')) {
                    $inp.prop('checked', !!val);
                } else if (val !== undefined) {
                    $inp.val(val);
                }
            });

            // Hydrate Form Family Data & Advanced tabs
            if (this.getFamily(t) === 'form') {
                $('#prop-data-token').val(el.fieldName || el.token || el.id || '');
                $('#prop-data-default').val(el.defaultValue || '');
                $('#prop-data-dynamic-default').prop('checked', !!el.dynamicDefault);
                $('#prop-data-param-name').val(el.paramName || '');
                $('#prop-data-param-wrap').toggle(!!el.dynamicDefault);
                $('#prop-data-readonly').prop('checked', !!el.readOnly);
                $('#prop-data-save-db').prop('checked', el.saveToDb !== false);

                $('#prop-adv-field-id').val(el.fieldId || '');
                $('#prop-adv-input-mask').val(el.inputMask || '');
                $('#prop-adv-custom-attrs').val(el.customAttrs || '');
                this.renderBadges('#wppoppop-adv-filters-list', el.filters || [], 'filter');
                this.renderBadges('#wppoppop-adv-validators-list', el.validators || [], 'validator');
            }
        },

        renderBadges: function(selector, list, kind) {
            var $wrap = $(selector);
            if (!$wrap.length) return;
            $wrap.empty();
            (Array.isArray(list) ? list : []).forEach(function(item) {
                $('<span class="wppoppop-adv-badge" />')
                    .attr('data-kind', kind)
                    .attr('data-val', item)
                    .text(item + ' ×')
                    .appendTo($wrap);
            });
        },

        installHooks: function() {
            if (this._initialized) return;
            this._initialized = true;
            var self = this;

            // 1. Wrap WpPopPopBuilderInspector methods safely
            var insp = window.WpPopPopBuilderInspector;
            if (insp) {
                window.WpPopPopInspector = insp;
                window.WpPopPopBuilder = window.WpPopPopBuilder || {};
                window.WpPopPopBuilder.Inspector = insp;

                var origOpen = insp.open;
                insp.open = function(id) {
                    self.forceSlideInDrawer(true);
                    if (typeof origOpen === 'function') {
                        try { origOpen.call(insp, id); } catch (err) {}
                    }
                    var el = self.resolveElement(id || insp.activeElementId);
                    if (el) {
                        insp.activeElementId = el.id;
                        self.hydrateElement(el, insp);
                    }
                };

                var origClose = insp.close;
                insp.close = function(force) {
                    if (force !== true && !window.__wppoppopInspCloseAllowed) { return; } /* WPPOPPOP_INSPECTOR_CLOSE_ONLY_X */
                    self.forceSlideInDrawer(false);
                    if (typeof origClose === 'function') {
                        try { origClose.call(insp, true); } catch (err) {}
                    }
                };

                var origSwitchTab = insp.switchTab;
                insp.switchTab = function(tab) {
                    var norm = (tab === 'content' || tab === 'settings') ? 'basic' : tab;
                    if (typeof origSwitchTab === 'function') {
                        try { origSwitchTab.call(insp, norm); } catch (err) {}
                    }
                    insp.activeTab = norm;
                    $('.wppoppop-insp-tab, .wppoppop-insp-tab-btn').removeClass('active').css('color', '#94a3b8');
                    $('.wppoppop-insp-tab[data-tab="' + norm + '"], .wppoppop-insp-tab-btn[data-tab="' + norm + '"]').addClass('active').css('color', '#ffffff');

                    $('.wppoppop-insp-content, .wppoppop-insp-tab-content, .wppoppop-tab-pane').removeClass('active').hide();
                    if (norm === 'basic') {
                        $('#insp-tab-settings, #insp-tab-basic').addClass('active').show();
                    } else {
                        $('#insp-tab-' + norm).addClass('active').show();
                    }
                };

                var origShowPanel = insp.showElementPanel;
                insp.showElementPanel = function(type) {
                    if (typeof origShowPanel === 'function') {
                        try { origShowPanel.call(insp, type); } catch (err) {}
                    }
                    if (type) {
                        self.syncFamilyTabs(type, insp);
                        $('.element-panel, .wppoppop-panel-specific').hide();
                        var targetSelector = PANEL_MAP[String(type).toLowerCase().trim()] || ('#panel-elem-' + type);
                        $(targetSelector).show();
                    }
                };

                var origPopulate = insp.populateFields;
                if (typeof origPopulate === 'function') {
                    insp.populateFields = function(el) {
                        var res = origPopulate.apply(insp, arguments);
                        if (el && el.type) {
                            self.hydrateElement(el, insp);
                        }
                        return res;
                    };
                }
            }

            // Delegated click handler for all 5 Inspector Tabs (Content, Style, Logic, Data, Advanced)
            $(document).off('click.wppoppopFiveTabClick').on('click.wppoppopFiveTabClick', '.wppoppop-insp-tab, .wppoppop-insp-tab-btn', function(e) {
                e.preventDefault();
                var tabName = $(this).attr('data-tab') || $(this).data('tab');
                if (tabName && window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.switchTab === 'function') {
                    window.WpPopPopBuilderInspector.switchTab(tabName);
                }
            });
            
            // 2. Wrap WpPopPopBuilderCanvas methods to support all 26 element types & auto-slide-in on add
            var canvas = window.WpPopPopBuilderCanvas;
            if (canvas) {
                var origGetInner = canvas.getInnerMarkup;
                canvas.getInnerMarkup = function(el) {
                    if (el && el.type) {
                        var t = String(el.type).toLowerCase();
                        if (t === 'label') {
                            var tag = el.tag || 'div';
                            var txt = el.content || el.label || 'Label Text';
                            var tf = el.textTransform ? 'text-transform:' + el.textTransform + ';' : '';
                            return '<' + tag + ' style="margin:0;width:100%;height:100%;display:flex;align-items:center;' + tf + '">' + txt + '</' + tag + '>';
                        }
                        if (t === 'icon') { /* WPPOPPOP_FA_REGISTRY_ICON_FIX */
                            var iCls = $.trim(el.iconClass || el.content || 'dashicons-star-filled');
                            var iSz = parseInt(el.iconSize, 10) || 36;
                            var iRot = parseInt(el.rotate, 10) || 0;
                            var iCol = el.iconColor || el.color || '#38bdf8';
                            var iIsFa = /^(fa|fas|far|fab|fal|fad|fa-solid|fa-regular|fa-brands)(\s|$)|^fa-/.test(iCls);
                            var iFull;
                            if (iIsFa) {
                                iFull = (iCls.indexOf('fa-') === 0 && iCls.indexOf(' ') === -1) ? ('fas ' + iCls) : iCls;
                            } else {
                                iFull = (iCls.indexOf('dashicons ') === 0) ? iCls : ('dashicons ' + (iCls.indexOf('dashicons-') === 0 ? iCls : 'dashicons-' + iCls));
                            }
                            return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:' + iCol + ';transform:rotate(' + iRot + 'deg);pointer-events:none;">' +
                                '<i class="' + iFull.replace(/"/g, '') + '" style="font-size:' + iSz + 'px;line-height:1;display:inline-block;"></i></div>';
                        }
                        if (t === 'button') {
                            var bTxt = el.content || el.label || 'Click Here';
                            return '<button type="button" style="width:100%;height:100%;border:none;background:inherit;color:inherit;font:inherit;cursor:pointer;">' + bTxt + '</button>';
                        }
                        if (t === 'text' || t === 'textfield' || t === 'text_field') {
                            var tfPh = el.placeholder || el.content || 'Enter text...';
                            return '<input type="text" placeholder="' + tfPh + '" readonly style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;pointer-events:none;" />';
                        }
                        if (t === 'phone' || t === 'tel') {
                            var phPrefix = el.phonePrefix ? String(el.phonePrefix) + ' ' : '';
                            var ph = el.placeholder || (phPrefix + '(555) 000-0000');
                            return '<input type="tel" placeholder="' + ph + '" readonly style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;pointer-events:none;" />';
                        }
                        if (t === 'textarea') {
                            var taPh = el.placeholder || 'Write your message...';
                            var taRows = parseInt(el.rows, 10) || 4;
                            return '<textarea rows="' + taRows + '" placeholder="' + taPh + '" readonly style="width:100%;height:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;resize:none;pointer-events:none;"></textarea>';
                        }
                        if (t === 'select' || t === 'dropdown') {
                            var selFirst = el.placeholder || 'Select an option...';
                            var selOpts = String(el.options || 'Option 1, Option 2').split(/[\n,]+/);
                            var selHtml = '<option>' + selFirst + '</option>' + selOpts.map(function(o) { return '<option>' + $.trim(o) + '</option>'; }).join('');
                            return '<select disabled style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;pointer-events:none;">' + selHtml + '</select>';
                        }
                        if (t === 'checkbox' || t === 'checkboxes') {
                            var cbOpts = String(el.options || 'I agree to the terms and conditions').split(/[\n,]+/);
                            var cbDir = (el.layout === 'inline') ? 'row' : 'column';
                            var cbChecked = el.checkedDefault ? 'checked' : '';
                            var cbItems = cbOpts.slice(0, 4).map(function(o, idx) {
                                var ck = (idx === 0 && cbChecked) ? 'checked' : '';
                                return '<label style="display:inline-flex;align-items:center;gap:6px;margin-right:8px;"><input type="checkbox" ' + ck + ' disabled /> <span>' + $.trim(o) + '</span></label>';
                            }).join('');
                            return '<div style="display:flex;flex-direction:' + cbDir + ';flex-wrap:wrap;gap:4px;justify-content:center;height:100%;font-size:12px;padding:0 6px;">' + cbItems + '</div>';
                        }
                        if (t === 'radio' || t === 'radios') {
                            var rdOpts = String(el.options || 'Choice A, Choice B').split(/[\n,]+/);
                            var rdDir = (el.layout === 'inline') ? 'row' : 'column';
                            var rds = rdOpts.slice(0, 4).map(function(o, idx) {
                                return '<label style="display:inline-flex;align-items:center;gap:6px;margin-right:10px;"><input type="radio" ' + (idx === 0 ? 'checked' : '') + ' disabled /> <span>' + $.trim(o) + '</span></label>';
                            }).join('');
                            return '<div style="display:flex;flex-direction:' + rdDir + ';flex-wrap:wrap;gap:4px;justify-content:center;height:100%;font-size:12px;padding:0 6px;">' + rds + '</div>';
                        }
                        if (t === 'multiselect') {
                            var msOpts = String(el.options || 'Marketing, Design, Engineering, Sales').split(/[\n,]+/);
                            var msHtml = msOpts.map(function(o) { return '<option>' + $.trim(o) + '</option>'; }).join('');
                            return '<select multiple disabled style="width:100%;height:100%;padding:4px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;pointer-events:none;">' + msHtml + '</select>';
                        }
                        if (t === 'imageselect') {
                            var isOpts = String(el.options || 'Starter Plan, Pro Plan, Enterprise').split(/[\n,]+/);
                            var cols = parseInt(el.columns, 10) || 3;
                            var cards = isOpts.slice(0, 4).map(function(o, idx) {
                                var parts = String(o).split('|');
                                var lbl = $.trim(parts[0] || 'Choice');
                                var imgUrl = parts[1] ? $.trim(parts[1]) : '';
                                var borderCol = idx === 0 ? '#0284c7' : '#cbd5e1';
                                var thumb = imgUrl ? '<img src="' + imgUrl + '" style="width:100%;height:32px;object-fit:cover;border-radius:3px;margin-bottom:2px;" />' : '<span class="dashicons dashicons-format-image" style="display:block;margin:0 auto 2px;color:#64748b;"></span>';
                                return '<div style="border:2px solid ' + borderCol + ';border-radius:6px;padding:4px;text-align:center;font-size:11px;font-weight:600;background:#f8fafc;">' + thumb + lbl + '</div>';
                            }).join('');
                            return '<div style="display:grid;grid-template-columns:repeat(' + cols + ',1fr);gap:6px;width:100%;height:100%;align-items:center;">' + cards + '</div>';
                        }
                        if (t === 'tile') {
                            var tlOpts = String(el.options || '$10 / mo, $25 / mo, $50 / mo').split(/[\n,]+/);
                            var tlCols = parseInt(el.columns, 10) || 3;
                            var actBg = el.activeColor || '#0284c7';
                            var tiles = tlOpts.slice(0, 4).map(function(o, i) {
                                var styleStr = i === 0 ? ('background:' + actBg + ';color:#ffffff;border:1px solid ' + actBg + ';') : 'background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;';
                                return '<div style="padding:6px;border-radius:6px;text-align:center;font-weight:700;font-size:12px;' + styleStr + '">' + $.trim(o) + '</div>';
                            }).join('');
                            return '<div style="display:grid;grid-template-columns:repeat(' + tlCols + ',1fr);gap:6px;width:100%;height:100%;align-items:center;">' + tiles + '</div>';
                        }
                        if (t === 'date') {
                            var dtFmt = el.dateFormat || 'YYYY-MM-DD';
                            return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;background:#ffffff;color:#475569;font-size:12px;box-sizing:border-box;pointer-events:none;"><span>' + dtFmt + '</span><span class="dashicons dashicons-calendar-alt" style="font-size:16px;width:16px;height:16px;"></span></div>';
                        }
                        if (t === 'time') {
                            var tmLbl = (el.timeFormat === '24h') ? '14:30 (24h)' : '02:30 PM';
                            return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;background:#ffffff;color:#475569;font-size:12px;box-sizing:border-box;pointer-events:none;"><span>' + tmLbl + '</span><span class="dashicons dashicons-clock" style="font-size:16px;width:16px;height:16px;"></span></div>';
                        }
                        if (t === 'password') {
                            var pwPh = el.placeholder || 'Enter password...';
                            var eyeHtml = (el.showEyeToggle !== false) ? '<span class="dashicons dashicons-visibility" style="font-size:16px;width:16px;height:16px;color:#64748b;"></span>' : '';
                            return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;background:#ffffff;color:#64748b;font-size:12px;box-sizing:border-box;pointer-events:none;"><span>' + pwPh + '</span>' + eyeHtml + '</div>';
                        }
                        if (t === 'upload' || t === 'file') {
                            var upLbl = el.placeholder || 'Choose file or drag here...';
                            return '<div style="width:100%;height:100%;border:1px dashed #94a3b8;border-radius:inherit;display:flex;align-items:center;justify-content:center;gap:6px;font-size:12px;color:#475569;background:#f8fafc;box-sizing:border-box;pointer-events:none;"><span class="dashicons dashicons-upload"></span><span>' + upLbl + '</span></div>';
                        }
                        if (t === 'rating') {
                            var rMax = parseInt(el.ratingMax, 10) || 5;
                            var rCol = el.ratingColor || '#f59e0b';
                            var rShape = el.ratingShape || 'star';
                            var glyph = rShape === 'heart' ? '♥' : (rShape === 'thumb' ? '👍' : '★');
                            var stars = '';
                            for (var si = 0; si < rMax; si++) {
                                var c = si < Math.ceil(rMax * 0.8) ? rCol : '#cbd5e1';
                                stars += '<span style="color:' + c + ';font-size:20px;line-height:1;">' + glyph + '</span>';
                            }
                            return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;gap:4px;pointer-events:none;">' + stars + '</div>';
                        }
                        if (t === 'checkbox') {
                            var cbOpts = String(el.options || 'I agree to the terms').split(',');
                            return '<div style="display:flex;flex-direction:column;gap:4px;justify-content:center;height:100%;font-size:12px;"><label><input type="checkbox" checked disabled /> ' + $.trim(cbOpts[0]) + '</label></div>';
                        }
                        if (t === 'radio') {
                            var rdOpts = String(el.options || 'Choice A, Choice B').split(',');
                            var rds = rdOpts.slice(0, 3).map(function(o, idx) {
                                return '<label style="margin-right:8px;"><input type="radio" ' + (idx === 0 ? 'checked' : '') + ' disabled /> ' + $.trim(o) + '</label>';
                            }).join('');
                            return '<div style="display:flex;align-items:center;height:100%;font-size:12px;">' + rds + '</div>';
                        }
                    }
                    return typeof origGetInner === 'function' ? origGetInner.call(canvas, el) : '';
                };

                var origAddElement = canvas.addElement;
                if (typeof origAddElement === 'function') {
                    canvas.addElement = function(type, x, y) {
                        var res = origAddElement.apply(canvas, arguments);
                        // Find newly added element (last element in active canvas) and slide open Inspector
                        var els = typeof canvas.getActiveElements === 'function' ? (canvas.getActiveElements() || []) : [];
                        var newest = Array.isArray(els) && els.length ? els[els.length - 1] : null;
                        if (newest && newest.id) {
                            if (typeof canvas.selectElement === 'function') {
                                canvas.selectElement(newest.id);
                            }
                            if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.renderLayers === 'function') {
                                window.WpPopPopBuilderLayers.renderLayers();
                            }
                            if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.open === 'function') {
                                window.WpPopPopBuilderInspector.open(newest.id);
                            }
                        }
                        return res;
                    };
                }

                var origSelectElement = canvas.selectElement;
                if (typeof origSelectElement === 'function') {
                    canvas.selectElement = function(id) {
                        var res = origSelectElement.apply(canvas, arguments);
                        if (id && window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.open === 'function') {
                            window.WpPopPopBuilderInspector.open(id);
                        }
                        return res;
                    };
                }
            }

            // 3. Ensure Layers Panel renders on startup
            if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.renderLayers === 'function') {
                window.WpPopPopBuilderLayers.renderLayers();
            }

            this.bindDecomposedInputs();
        },

        bindDecomposedInputs: function() {
            var self = this;
            var insp = window.WpPopPopBuilderInspector;

            // Direct family tab routing on Ribbon, Canvas Stage, and Layer Panel clicks
            $(document).off('click.wppoppopFamilyRouteRibbon').on('click.wppoppopFamilyRouteRibbon', '.wppoppop-ribbon-btn, .wppoppop-ribbon-tool', function() {
                var rType = $(this).attr('data-type') ||$(this).data('type');
                if (rType) {
                    self.forceSlideInDrawer(true);
                    self.syncFamilyTabs(rType, window.WpPopPopBuilderInspector);
                    setTimeout(function() {
                        var activeEl = self.resolveElement();
                        if (activeEl) {
                            self.hydrateElement(activeEl, window.WpPopPopBuilderInspector);
                        } else {
                            self.syncFamilyTabs(rType, window.WpPopPopBuilderInspector);
                        }
                    }, 30);
                }
            });

            $(document).off('click.wppoppopFamilyRouteStage').on('click.wppoppopFamilyRouteStage', '.wppoppop-canvas-item, .wppoppop-layer-item', function() {
                var clickedId = $(this).attr('data-id') || $(this).data('id') ||$(this).attr('id') || '';
                var clickedEl = self.resolveElement(clickedId);
                if (clickedEl && clickedEl.type) {
                    self.forceSlideInDrawer(true);
                    self.hydrateElement(clickedEl, window.WpPopPopBuilderInspector);
                }
            });

            $(document).off('.wppoppopGpBind').on('input.wppoppopGpBind change.wppoppopGpBind', '.wppoppop-gp-bind', function() {
                var el = self.resolveElement(insp ? insp.activeElementId : null);
                if (!el) return;
                var $inp = $(this);
                var prop = $inp.data('prop');
                if (!prop) return;
                el[prop] = $inp.is(':checkbox') ? $inp.is(':checked') : $inp.val();
                if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.renderCanvas === 'function') {
                    window.WpPopPopBuilderCanvas.renderCanvas();
                }
            });

            $(document).off('click.wppoppopIconPreset').on('click.wppoppopIconPreset', '.wppoppop-icon-preset', function(e) {
                e.preventDefault();
                $('#prop-icon-class').val($(this).data('icon')).trigger('change');
            });

            $(document).off('.wppoppopDataAdv').on(
                'input.wppoppopDataAdv change.wppoppopDataAdv',
                '#prop-data-token, #prop-data-default, #prop-data-dynamic-default, #prop-data-param-name, #prop-data-readonly, #prop-data-save-db, #prop-adv-field-id, #prop-adv-input-mask, #prop-adv-custom-attrs',
                function() {
                    var el = self.resolveElement(insp ? insp.activeElementId : null);
                    if (!el) return;
                    el.fieldName = $('#prop-data-token').val() || '';
                    el.token = el.fieldName;
                    el.defaultValue = $('#prop-data-default').val() || '';
                    el.dynamicDefault = $('#prop-data-dynamic-default').is(':checked');
                    el.paramName = $('#prop-data-param-name').val() || '';
                    $('#prop-data-param-wrap').toggle(!!el.dynamicDefault);
                    el.readOnly = $('#prop-data-readonly').is(':checked');
                    el.saveToDb = $('#prop-data-save-db').is(':checked');

                    el.fieldId = $('#prop-adv-field-id').val() || '';
                    el.inputMask = $('#prop-adv-input-mask').val() || '';
                    el.customAttrs = $('#prop-adv-custom-attrs').val() || '';
                }
            );

            $(document).off('click.wppoppopAdvAdd').on('click.wppoppopAdvAdd', '.wppoppop-adv-add-btn', function(e) {
                e.preventDefault();
                var el = self.resolveElement(insp ? insp.activeElementId : null);
                if (!el) return;
                var kind = $(this).data('kind');
                var val = $(this).data('val');
                if (kind === 'filter') {
                    el.filters = Array.isArray(el.filters) ? el.filters : [];
                    if (el.filters.indexOf(val) === -1) el.filters.push(val);
                    self.renderBadges('#wppoppop-adv-filters-list', el.filters, 'filter');
                } else {
                    el.validators = Array.isArray(el.validators) ? el.validators : [];
                    if (el.validators.indexOf(val) === -1) el.validators.push(val);
                    self.renderBadges('#wppoppop-adv-validators-list', el.validators, 'validator');
                }
            });

            $(document).off('click.wppoppopAdvRem').on('click.wppoppopAdvRem', '.wppoppop-adv-badge', function(e) {
                e.preventDefault();
                var el = self.resolveElement(insp ? insp.activeElementId : null);
                if (!el) return;
                var kind = $(this).attr('data-kind');
                var val = $(this).attr('data-val');
                if (kind === 'filter' && Array.isArray(el.filters)) {
                    el.filters = el.filters.filter(function(x) { return x !== val; });
                    self.renderBadges('#wppoppop-adv-filters-list', el.filters, 'filter');
                } else if (kind === 'validator' && Array.isArray(el.validators)) {
                    el.validators = el.validators.filter(function(x) { return x !== val; });
                    self.renderBadges('#wppoppop-adv-validators-list', el.validators, 'validator');
                }
            });
        }
    };

    window.WpPopPopElementsRegistry = WpPopPopElementsRegistry;
    WpPopPopElementsRegistry.PANEL_MAP = PANEL_MAP;
    WpPopPopElementsRegistry.ALIAS_MAP = {
        'textfield': 'text', 'phone': 'tel', 'dropdown': 'select', 'checkboxes': 'checkbox',
        'radios': 'radio', 'file': 'upload', 'title': 'label', 'paragraph': 'label',
        'close_icon': 'close', 'link': 'button', 'link_btn': 'button', 'step_btn': 'button'
    };

    $(function() {
        WpPopPopElementsRegistry.installHooks();
        // Secondary tick after campaign data hydration completes
        setTimeout(function() {
            WpPopPopElementsRegistry.installHooks();
            if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.renderLayers === 'function') {
                window.WpPopPopBuilderLayers.renderLayers();
            }
        }, 150);
    });

})(window, jQuery);
