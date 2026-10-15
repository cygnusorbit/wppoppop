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
        'textfield', 'tel', 'dropdown', 'checkboxes', 'radios', 'file'
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
        'text': '#panel-elem-textfield',
        'textfield': '#panel-elem-textfield',
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
            var $header = $drawer.find('.wppoppop-inspector-header, .wppoppop-inspector-tabs').first();
            if ($header.length) {
                // Rename Basic to Content and hide redundant Settings tab button
                var $basicBtn = $header.find('[data-tab="basic"], [data-tab="content"]').first();
                if ($basicBtn.length) {
                    $basicBtn.text('Content').attr('id', 'insp-tab-nav-content');
                }
                $header.find('[data-tab="settings"]').hide();

                // Inject Data & Advanced tab buttons if missing from header
                if (!$header.find('[data-tab="data"]').length) {
                    $('<button type="button" class="wppoppop-insp-tab wppoppop-insp-tab-btn wppoppop-form-family-tab" id="insp-tab-nav-data" data-tab="data" style="display:none;flex:1;background:transparent;border:none;color:#94a3b8;font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:0.4px;cursor:pointer;height:100%;padding:0 2px;">Data</button>').appendTo($header);
                }
                if (!$header.find('[data-tab="advanced"]').length) {
                    $('<button type="button" class="wppoppop-insp-tab wppoppop-insp-tab-btn wppoppop-form-family-tab" id="insp-tab-nav-advanced" data-tab="advanced" style="display:none;flex:1;background:transparent;border:none;color:#94a3b8;font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:0.4px;cursor:pointer;height:100%;padding:0 2px;">Advanced</button>').appendTo($header);
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
                insp.close = function() {
                    self.forceSlideInDrawer(false);
                    if (typeof origClose === 'function') {
                        try { origClose.call(insp); } catch (err) {}
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
                        if (t === 'icon') {
                            var cls = el.iconClass || 'dashicons-star-filled';
                            var sz = parseInt(el.iconSize, 10) || 36;
                            var rot = parseInt(el.rotate, 10) || 0;
                            return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;"><span class="dashicons ' + cls + '" style="font-size:' + sz + 'px;width:' + sz + 'px;height:' + sz + 'px;transform:rotate(' + rot + 'deg);"></span></div>';
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
        if (el.bgColor && !el.transparentFill) {
            $node.css('background-color', el.bgColor);
        } else if (el.transparentFill) {
            $node.css('background-color', 'transparent');
        }

        // 4. 4-Way Padding & Borders
        var pt = parseInt(el.padTop, 10) || 0;
        var pr = parseInt(el.padRight, 10) || 0;
        var pb = parseInt(el.padBottom, 10) || 0;
        var pl = parseInt(el.padLeft, 10) || 0;
        if (pt || pr || pb || pl) {
            $node.css('padding', pt + 'px ' + pr + 'px ' + pb + 'px ' + pl + 'px');
        }
        var bw = parseInt(el.borderWidth, 10) || 0;
        var bs = el.borderStyle || (bw > 0 ? 'solid' : 'none');
        var bc = el.borderColor || '#cbd5e1';
        $node.css({
            'border-width': bw + 'px',
            'border-style': bs,
            'border-color': bc,
            'border-radius': (parseInt(el.borderRadius, 10) || 0) + 'px'
        });

        // 5. 5-Parameter Box Shadow
        var sx = parseInt(el.shadowX, 10) || 0;
        var sy = parseInt(el.shadowY, 10) || 0;
        var sBlur = parseInt(el.shadowBlur, 10) || 0;
        var sSpread = parseInt(el.shadowSpread, 10) || 0;
        var sCol = el.shadowColor || 'rgba(0,0,0,0.15)';
        if (sx || sy || sBlur || sSpread) {
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
        var hasSideIcons = isForm && ['text', 'email', 'number', 'tel', 'date', 'time', 'password'].indexOf(canonical) !== -1;
        $('#gp-form-input-icons-row').toggle(hasSideIcons);

        if (!isForm) {
            var textMedia = ['label', 'html', 'button', 'submit', 'progress'];
            var hoverMedia = ['button', 'submit'];
            $('#insp-section-typography').toggle(textMedia.indexOf(canonical) !== -1);
            $('#wppoppop-hover-state-card').toggle(hoverMedia.indexOf(canonical) !== -1);
        }

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
            var op = parseInt($('#prop-opacity').val(), 10);
            if (!isNaN(op)) {
                el.opacity = op;
                $('#prop-opacity-val').text(op + '%');
            }
            el.fontFamily    = $('#prop-font-family').val();
            el.fontSize      = parseInt($('#prop-font-size').val(), 10) || 16;
            el.fontWeight    = $('#prop-font-weight').val();
            el.lineHeight    = parseFloat($('#prop-line-height').val()) || 1.4;
            el.letterSpacing = parseFloat($('#prop-letter-spacing').val()) || 0;
            el.textAlign     = $('#prop-text-align').val();
            el.color         = $('#prop-color').val();
            el.bgColor       = $('#prop-bg-color').val();
            el.padTop        = parseInt($('#prop-pad-top').val(), 10) || 0;
            el.padRight      = parseInt($('#prop-pad-right').val(), 10) || 0;
            el.padBottom     = parseInt($('#prop-pad-bottom').val(), 10) || 0;
            el.padLeft       = parseInt($('#prop-pad-left').val(), 10) || 0;
            el.borderWidth   = parseInt($('#prop-border-width').val(), 10) || 0;
            el.borderStyle   = $('#prop-border-style').val();
            el.borderRadius  = parseInt($('#prop-border-radius').val(), 10) || 0;
            el.borderColor   = $('#prop-border-color').val();
            el.shadowX       = parseInt($('#prop-shadow-x').val(), 10) || 0;
            el.shadowY       = parseInt($('#prop-shadow-y').val(), 10) || 0;
            el.shadowBlur    = parseInt($('#prop-shadow-blur').val(), 10) || 0;
            el.shadowSpread  = parseInt($('#prop-shadow-spread').val(), 10) || 0;
            el.shadowColor   = $('#prop-shadow-color').val();
            el.hoverBg       = $('#prop-hover-bg').val();
            el.hoverColor    = $('#prop-hover-color').val();
            el.hoverBorder   = $('#prop-hover-border').val();

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

/* === WPPOPPOP_PHASE2_MEDIA_FIELD_VERIFICATION_START === */
(function(window, $) {
    'use strict';

    window.WpPopPopElementsRegistry = window.WpPopPopElementsRegistry || {};
    var Reg = window.WpPopPopElementsRegistry;

    var SVG_SHAPE_PATHS = {
        'rectangle': '<rect x="2" y="2" width="96" height="96" rx="0" />',
        'rounded':   '<rect x="2" y="2" width="96" height="96" rx="16" />',
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
        $('#prop-image-link-wrap').toggle(imgAct === 'url');

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
            var preset = el.shapePreset || 'rectangle';
            var svgInner = SVG_SHAPE_PATHS[preset] || SVG_SHAPE_PATHS['rectangle'];
            var fillCol = el.transparentFill ? 'none' : (el.bgColor || '#38bdf8');
            var stWidth = parseInt(el.strokeWidth, 10) || 0;
            var stCol   = el.strokeColor || '#0284c7';
            var rot     = parseInt(el.rotate, 10) || 0;
            $node.css({ 'background-color': 'transparent', 'transform': 'rotate(' + rot + 'deg)' });
            innerHtml = '<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="width:100%;height:100%;display:block;pointer-events:none;overflow:visible;" fill="' + fillCol + '" stroke="' + (stWidth > 0 ? stCol : 'none') + '" stroke-width="' + stWidth + '">' + svgInner + '</svg>';
        } else if (canonical === 'image') {
            var src = $.trim(el.src || el.imageUrl || '');
            var alt = $('<div>').text(el.alt || 'Image').html();
            var fit = el.objectFit || 'contain';
            if (src) {
                innerHtml = '<img src="' + src + '" alt="' + alt + '" style="width:100%;height:100%;object-fit:' + fit + ';display:block;border-radius:inherit;pointer-events:none;">';
            } else {
                innerHtml = '<div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#1e293b;color:#94a3b8;font-size:11px;border:1px dashed #475569;border-radius:inherit;pointer-events:none;"><span class="dashicons dashicons-format-image" style="font-size:24px;width:24px;height:24px;margin-bottom:4px;"></span><span>Select Image</span></div>';
            }
        } else if (canonical === 'video') {
            var prov = (el.videoProvider || 'youtube').toUpperCase();
            var ratio = el.aspectRatio || '16:9';
            var vUrl = $('<div>').text(el.videoUrl || 'No video URL set').html();
            var badges = [];
            if (el.autoplay) badges.push('Autoplay');
            if (el.muted) badges.push('Muted');
            if (el.loop) badges.push('Loop');
            innerHtml = '<div style="width:100%;height:100%;background:#0f172a;color:#f8fafc;display:flex;flex-direction:column;align-items:center;justify-content:center;border:1px solid #334155;border-radius:inherit;padding:8px;box-sizing:border-box;pointer-events:none;text-align:center;">' +
                '<span class="dashicons dashicons-video-alt3" style="font-size:28px;width:28px;height:28px;color:#f43f5e;margin-bottom:4px;"></span>' +
                '<div style="font-size:11px;font-weight:700;">' + prov + ' (' + ratio + ')</div>' +
                '<div style="font-size:10px;color:#94a3b8;max-width:90%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + vUrl + '</div>' +
                (badges.length ? '<div style="font-size:9px;color:#38bdf8;margin-top:3px;">' + badges.join(' / ') + '</div>' : '') +
                '</div>';
        } else if (canonical === 'label') {
            var tag = (el.tag || el.headingTag || 'div').toLowerCase();
            if (['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span'].indexOf(tag) === -1) tag = 'div';
            var txt = el.content !== undefined ? el.content : (el.titleText || el.paragraphText || el.text || 'Label Text');
            var tf  = el.textTransform || 'none';
            innerHtml = '<' + tag + ' class="wppoppop-stage-label-inner" style="margin:0;padding:0;width:100%;height:100%;text-transform:' + tf + ';font:inherit;color:inherit;line-height:inherit;letter-spacing:inherit;text-align:inherit;pointer-events:none;">' + txt + '</' + tag + '>';
        } else if (canonical === 'html') {
            var hTag = (el.tag === 'span') ? 'span' : 'div';
            var ovf  = el.overflow || 'hidden';
            var rawCode = el.content || '<div style="padding:8px;color:#64748b;font-size:12px;">Custom HTML Block</div>';
            innerHtml = '<' + hTag + ' style="width:100%;height:100%;overflow:' + ovf + ';pointer-events:none;">' + rawCode + '</' + hTag + '>';
        } else if (canonical === 'close') {
            var sym = el.closeSymbol || 'times';
            var cSize = parseInt(el.iconSize, 10) || 20;
            var glyphMarkup = '&times;';
            if (sym === 'text_close') {
                glyphMarkup = '<span style="font-size:' + Math.max(10, Math.round(cSize * 0.65)) + 'px;font-weight:700;letter-spacing:0.5px;">CLOSE</span>';
            } else if (sym.indexOf('dashicons-') === 0) {
                glyphMarkup = '<span class="dashicons ' + sym + '" style="font-size:' + cSize + 'px;width:' + cSize + 'px;height:' + cSize + 'px;"></span>';
            } else {
                glyphMarkup = '<span style="font-size:' + cSize + 'px;line-height:1;">&times;</span>';
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
            innerHtml = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;pointer-events:none;font:inherit;color:inherit;">' +
                icoHtml + '<span>' + $('<div>').text(bTxt).html() + '</span>' +
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
                el.imageUrl = val;
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

        $(document).off('click.p2MediaLib', '#prop-image-media-btn')
                   .on('click.p2MediaLib', '#prop-image-media-btn', function(e) {
            e.preventDefault();
            if (!window.wp || !window.wp.media) return;
            var frame = window.wp.media({
                title: 'Select or Upload Image',
                button: { text: 'Use Image' },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                if (attachment && attachment.url) {
                    $('#prop-image-url').val(attachment.url).trigger('change');
                    if (attachment.alt && !$('#prop-image-alt').val()) {
                        $('#prop-image-alt').val(attachment.alt).trigger('change');
                    }
                }
            });
            frame.open();
        });
    }

    $(function() {
        bindPhase2MediaVerification();
        setTimeout(bindPhase2MediaVerification, 120);
    });
    bindPhase2MediaVerification();
})(window, jQuery);
/* === WPPOPPOP_PHASE2_MEDIA_FIELD_VERIFICATION_END === */

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
        var offL   = parseInt(el.iconLeftOffset, 10) || 10;
        var offR   = parseInt(el.iconRightOffset, 10) || 10;
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
            '#prop-gp-required', '#prop-input-icon-left', '#prop-input-icon-left-offset',
            '#prop-input-icon-right', '#prop-input-icon-right-offset', '#prop-gp-style-css-class',
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
        if ($('#prop-gp-autocomplete').length) {
            el.autocomplete = $('#prop-gp-autocomplete').val() || 'off';
        }
        if ($('#prop-anim-in').length) {
            el.animIn       = $('#prop-anim-in').val() || 'none';
            el.animation    = el.animIn;
            el.animOut      = $('#prop-anim-out').val() || 'none';
            el.animDuration = parseInt($('#prop-anim-duration').val(), 10) || 1000;
            el.animDelay    = parseInt($('#prop-anim-delay').val(), 10) || 0;
        }
        if (isForm && $('#prop-gp-required').length) {
            el.required = $('#prop-gp-required').is(':checked');
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
            el.bgColor       = $('#prop-bg-color').val() || '#ffffff';
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
            el.iconLeft        = $('#prop-input-icon-left').val() || '';
            el.iconLeftOffset  = parseInt($('#prop-input-icon-left-offset').val(), 10) || 10;
            el.iconRight       = $('#prop-input-icon-right').val() || '';
            el.iconRightOffset = parseInt($('#prop-input-icon-right-offset').val(), 10) || 10;
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
            $('#wppoppop-gp-logic-rules-list .wppoppop-gp-repeater-item').each(function() {
                var f  = $.trim($(this).find('.gp-rule-field').val() || '');
                var op = $(this).find('.gp-rule-op').val() || 'is';
                var v  = $.trim($(this).find('.gp-rule-val').val() || '');
                if (f || op === 'not_empty') {
                    rulesArr.push({ field: f, operator: op, value: v });
                }
            });
            el.logicRules = rulesArr;
        }

        // 6. Flush Form Data & Advanced Tabs
        if (isForm) {
            el.defaultValue   = $('#prop-data-default').val() || '';
            el.dynamicDefault = $('#prop-data-dynamic').is(':checked');
            el.paramName      = $('#prop-data-param').val() || '';
            el.readOnly       = $('#prop-data-readonly').is(':checked');
            el.saveToDb       = $('#prop-data-savedb').is(':checked');
            el.fieldId        = $('#prop-adv-id').val() || el.id || '';
            el.fieldName      = el.fieldId;
            el.token          = el.fieldId;
            el.inputMask      = $('#prop-adv-mask').val() || '';

            var fArr = [];
            $('.gp-adv-filter:checked').each(function() { fArr.push($(this).val()); });
            el.filters = fArr;

            var vArr = [];
            $('.gp-adv-validator:checked').each(function() { vArr.push($(this).val()); });
            el.validators = vArr;
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

