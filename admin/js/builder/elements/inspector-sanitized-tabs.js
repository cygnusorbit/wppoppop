/**
 * WpPopPop v3.0.637+ Decomposed Sanitized Layer Inspector Engine
 * Aligns all 26 Elements with wppoppop's 3-Tab (Media/Submit) & 5-Tab (Form) Architecture.
 * Eliminates duplicate fields, scopes Style/Logic controls per element, and fine-tunes UI.
 */
(function($) {
    'use strict';

    var MEDIA_TYPES = ['shape', 'image', 'video', 'label', 'html', 'close', 'icon', 'progress', 'button', 'submit'];
    var FORM_TYPES  = ['text', 'email', 'number', 'phone', 'textarea', 'select', 'checkbox', 'radio', 'multiselect', 'imageselect', 'tile', 'date', 'time', 'upload', 'password', 'rating'];

    var TYPE_ALIASES = {
        'title': 'label',
        'paragraph': 'label',
        'text_label': 'label',
        'close_icon': 'close',
        'link': 'button',
        'link_btn': 'button',
        'step': 'button',
        'step_btn': 'button',
        'textfield': 'text',
        'tel': 'phone',
        'dropdown': 'select',
        'checkboxes': 'checkbox',
        'radios': 'radio',
        'file': 'upload'
    };

    var TYPOGRAPHY_CAPABLE = [
        'label', 'button', 'submit', 'text', 'email', 'number', 'phone',
        'textarea', 'select', 'checkbox', 'radio', 'multiselect', 'tile',
        'date', 'time', 'upload', 'password'
    ];

    var INPUT_ICON_CAPABLE = ['text', 'email', 'number', 'phone', 'select', 'date', 'time', 'password'];
    var HOVER_CAPABLE      = ['button', 'submit', 'close', 'icon', 'tile', 'imageselect'];
    var CLICK_LOGIC_CAPABLE = ['button', 'submit', 'image', 'icon', 'close', 'shape'];

    var ELEMENT_LABELS = {
        shape: '1. Rectangle / Shape',
        image: '2. Image',
        video: '3. Video Embed',
        label: '4. Label / Text',
        html: '5. HTML Content',
        close: '6. Close Icon',
        icon: '7. Font Awesome Icon',
        progress: '8. Progress Bar',
        button: '9. Button / Link',
        text: '10. Text Input',
        email: '11. Email Address',
        number: '12. Number Input',
        phone: '13. Phone / Tel',
        textarea: '14. Textarea',
        select: '15. Select Dropdown',
        checkbox: '16. Checkbox',
        radio: '17. Radio Button',
        multiselect: '18. Multiselect',
        imageselect: '19. Image Select',
        tile: '20. Tile Select',
        date: '21. Date Picker',
        time: '22. Time Picker',
        upload: '23. File Upload',
        password: '24. Password',
        rating: '25. Star Rating',
        submit: '26. Submit Button'
    };

    window.WpPopPopSanitizedInspector = {
        canonicalType: function(rawType) {
            var t = String(rawType || 'label').toLowerCase().trim();
            return TYPE_ALIASES[t] || t;
        },

        isFormElement: function(rawType) {
            var c = this.canonicalType(rawType);
            return FORM_TYPES.indexOf(c) !== -1;
        },

        init: function() {
            this.injectSanitizedContainers();
            this.bindSanitizedEvents();
        },

        injectSanitizedContainers: function() {
            // 1. Hide legacy duplicate .element-panel containers visually while keeping DOM nodes for parity scripts
            $('#wppoppop-element-specific-settings').addClass('wppoppop-legacy-hidden-panels').css('display', 'none');

            // 2. Ensure clean mounted container in Content tab
            if (!$('#wppoppop-gp-sanitized-content').length) {
                var $geomHr = $('#insp-tab-basic hr').first();
                if ($geomHr.length) {
                    $geomHr.after('<div id="wppoppop-gp-sanitized-content" class="gp-sanitized-zone"></div>');
                } else {
                    $('#insp-tab-basic').append('<div id="wppoppop-gp-sanitized-content" class="gp-sanitized-zone"></div>');
                }
            }
            // Remove any older duplicate dynamic content zones if present
            $('#wppoppop-gp-content-module, #wppoppop-elem-isolated-content').not('#wppoppop-gp-sanitized-content').hide();

            // 3. Enhance Style Tab with Input Notice, Input Icons, Hover State Switcher, 5-Slider Shadow & Rotation
            if (!$('#wppoppop-gp-style-enhancements').length) {
                var styleExtraHtml = '' +
                    '<div id="wppoppop-gp-style-enhancements">' +
                        '<div id="gp-style-form-notice" class="gp-info-banner" style="display:none;">' +
                            '<span class="dashicons dashicons-info-outline"></span>' +
                            '<span>Global input baseline styles inherit from <strong>Popup Settings &raquo; Style</strong>. Override individual layer metrics below.</span>' +
                        '</div>' +
                        '<!-- Normal vs Hover State Sub-Toggle for Interactive Layers -->' +
                        '<div id="gp-style-hover-card" class="gp-card-section" style="display:none;">' +
                            '<div class="gp-card-header">' +
                                '<span>INTERACTION STATE (NORMAL / HOVER)</span>' +
                                '<div class="gp-segmented-mini" id="gp-state-mode-switch">' +
                                    '<button type="button" class="gp-seg-btn active" data-state="normal">Normal</button>' +
                                    '<button type="button" class="gp-seg-btn" data-state="hover">Hover</button>' +
                                '</div>' +
                            '</div>' +
                            '<div id="gp-hover-fields-panel" style="display:none;margin-top:8px;">' +
                                '<div class="gp-grid-3">' +
                                    '<div><label>HOVER BG</label><input type="color" id="gp-prop-hover-bg" value="#1d4ed8"></div>' +
                                    '<div><label>HOVER TEXT</label><input type="color" id="gp-prop-hover-color" value="#ffffff"></div>' +
                                    '<div><label>HOVER BORDER</label><input type="color" id="gp-prop-hover-border" value="#1e40af"></div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<!-- Input Icons (Left / Right) -->' +
                        '<div id="gp-style-input-icons-card" class="gp-card-section" style="display:none;">' +
                            '<div class="gp-card-header"><span>INPUT ICONS (FONT AWESOME)</span></div>' +
                            '<div class="gp-grid-2" style="margin-bottom:6px;">' +
                                '<div>' +
                                    '<label>LEFT ICON CLASS</label>' +
                                    '<input type="text" id="gp-prop-icon-left" placeholder="fas fa-envelope">' +
                                '</div>' +
                                '<div>' +
                                    '<label>RIGHT ICON CLASS</label>' +
                                    '<input type="text" id="gp-prop-icon-right" placeholder="fas fa-check">' +
                                '</div>' +
                            '</div>' +
                            '<div class="gp-grid-2">' +
                                '<div>' +
                                    '<label>ICON OFFSET (PX)</label>' +
                                    '<input type="number" id="gp-prop-icon-offset" value="10" min="0" max="40">' +
                                '</div>' +
                                '<div>' +
                                    '<label>ICON COLOR</label>' +
                                    '<input type="color" id="gp-prop-icon-color" value="#64748b">' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<!-- Rotation & 5-Parameter Box Shadow Visual Builder -->' +
                        '<div id="gp-style-transform-shadow-card" class="gp-card-section">' +
                            '<div class="gp-card-header"><span>TRANSFORM &amp; SHADOW BUILDER</span></div>' +
                            '<div class="gp-grid-2" style="margin-bottom:8px;">' +
                                '<div>' +
                                    '<label>ROTATION ANGLE (0°–360°)</label>' +
                                    '<input type="number" id="gp-prop-rotation" min="0" max="360" value="0">' +
                                '</div>' +
                                '<div>' +
                                    '<label>SHADOW PRESET</label>' +
                                    '<select id="gp-prop-shadow-mode">' +
                                        '<option value="none">No Shadow</option>' +
                                        '<option value="custom">Custom Shadow</option>' +
                                        '<option value="inset">Inset Shadow</option>' +
                                    '</select>' +
                                '</div>' +
                            '</div>' +
                            '<div id="gp-shadow-sliders-wrap" style="display:none;">' +
                                '<div class="gp-grid-4" style="margin-bottom:6px;">' +
                                    '<div><label>X (PX)</label><input type="number" id="gp-sh-x" value="0"></div>' +
                                    '<div><label>Y (PX)</label><input type="number" id="gp-sh-y" value="4"></div>' +
                                    '<div><label>BLUR</label><input type="number" id="gp-sh-blur" value="12" min="0"></div>' +
                                    '<div><label>SPREAD</label><input type="number" id="gp-sh-spread" value="0"></div>' +
                                '</div>' +
                                '<div><label>SHADOW COLOR</label><input type="color" id="gp-sh-color" value="#0f172a"></div>' +
                            '</div>' +
                        '</div>' +
                        '<!-- Custom CSS Class & Inline Style -->' +
                        '<div id="gp-style-custom-css-card" class="gp-card-section">' +
                            '<div class="gp-card-header"><span>CUSTOM CSS SCOPING</span></div>' +
                            '<div style="margin-bottom:6px;">' +
                                '<label>CUSTOM CSS CLASS</label>' +
                                '<input type="text" id="gp-prop-css-class" placeholder="e.g. my-custom-layer">' +
                            '</div>' +
                            '<div>' +
                                '<label>INLINE CSS RULES</label>' +
                                '<input type="text" id="gp-prop-css-inline" placeholder="e.g. letter-spacing:1px; backdrop-filter:blur(4px);">' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                $('#insp-tab-style').append(styleExtraHtml);
            }

            // 4. Sanitize Logic Tab: Separate Conditional Visibility Builder from Click Action Routing
            if (!$('#wppoppop-gp-conditional-logic-card').length) {
                var condHtml = '' +
                    '<div id="wppoppop-gp-conditional-logic-card" class="gp-card-section">' +
                        '<div class="gp-card-header">' +
                            '<span>CONDITIONAL VISIBILITY LOGIC</span>' +
                            '<label class="gp-switch"><input type="checkbox" id="gp-logic-enabled"><span class="gp-slider"></span></label>' +
                        '</div>' +
                        '<div id="gp-logic-builder-body" style="display:none;margin-top:8px;">' +
                            '<div class="gp-grid-2" style="margin-bottom:8px;">' +
                                '<div>' +
                                    '<label>ACTION</label>' +
                                    '<select id="gp-logic-action">' +
                                        '<option value="show">Show this layer</option>' +
                                        '<option value="hide">Hide this layer</option>' +
                                    '</select>' +
                                '</div>' +
                                '<div>' +
                                    '<label>OPERATOR</label>' +
                                    '<select id="gp-logic-match">' +
                                        '<option value="all">If ALL rules match</option>' +
                                        '<option value="any">If ANY rule matches</option>' +
                                    '</select>' +
                                '</div>' +
                            '</div>' +
                            '<div id="gp-logic-rules-list" style="margin-bottom:8px;"></div>' +
                            '<button type="button" id="gp-logic-add-rule" class="gp-btn-outline">+ Add Condition Rule</button>' +
                        '</div>' +
                    '</div>';
                $('#insp-tab-logic').prepend(condHtml);
            }

            // 5. Ensure Data & Advanced Tab Containers exist & are cleanly structured
            if (!$('#insp-tab-data').length) {
                $('#wppoppop-inspector-drawer > div').last().append('<div class="wppoppop-insp-content" id="insp-tab-data" style="display:none;"></div>');
            }
            if (!$('#insp-tab-advanced').length) {
                $('#wppoppop-inspector-drawer > div').last().append('<div class="wppoppop-insp-content" id="insp-tab-advanced" style="display:none;"></div>');
            }

            this.renderDataTabTemplate();
            this.renderAdvancedTabTemplate();
        },

        renderDataTabTemplate: function() {
            var html = '' +
                '<div class="gp-card-section">' +
                    '<div class="gp-card-header"><span>FIELD IDENTIFIER &amp; MERGE TOKEN</span></div>' +
                    '<div style="margin-bottom:8px;">' +
                        '<label>FIELD NAME (SLUG)</label>' +
                        '<input type="text" id="gp-data-fieldname" placeholder="email">' +
                    '</div>' +
                    '<div>' +
                        '<label>LIVE MERGE TAG (COPY TO LABELS/EMAILS)</label>' +
                        '<input type="text" id="gp-data-token-preview" readonly class="gp-mono-readonly" value="{email}">' +
                    '</div>' +
                '</div>' +
                '<div class="gp-card-section">' +
                    '<div class="gp-card-header"><span>DEFAULT VALUE &amp; URL BINDING</span></div>' +
                    '<div style="margin-bottom:8px;">' +
                        '<label>STATIC DEFAULT VALUE</label>' +
                        '<input type="text" id="gp-data-default-val" placeholder="Initial field value...">' +
                    '</div>' +
                    '<div class="gp-toggle-row">' +
                        '<span>Dynamic URL Parameter Population</span>' +
                        '<label class="gp-switch"><input type="checkbox" id="gp-data-dynamic-enable"><span class="gp-slider"></span></label>' +
                    '</div>' +
                    '<div id="gp-data-param-wrap" style="display:none;margin-top:6px;">' +
                        '<label>URL QUERY PARAMETER KEY</label>' +
                        '<input type="text" id="gp-data-param-key" placeholder="e.g. utm_source or user_email">' +
                    '</div>' +
                '</div>' +
                '<div class="gp-card-section">' +
                    '<div class="gp-card-header"><span>PERSISTENCE &amp; ACCESS LOCKS</span></div>' +
                    '<div class="gp-toggle-row" style="margin-bottom:8px;">' +
                        '<span>Read-Only (Lock Visitor Editing)</span>' +
                        '<label class="gp-switch"><input type="checkbox" id="gp-data-readonly"><span class="gp-slider"></span></label>' +
                    '</div>' +
                    '<div class="gp-toggle-row">' +
                        '<span>Save Submission Value to Database</span>' +
                        '<label class="gp-switch"><input type="checkbox" id="gp-data-save-db" checked><span class="gp-slider"></span></label>' +
                    '</div>' +
                '</div>';
            $('#insp-tab-data').html(html);
        },

        renderAdvancedTabTemplate: function() {
            var html = '' +
                '<div class="gp-card-section">' +
                    '<div class="gp-card-header"><span>FIELD ID &amp; MANDATORY RULE</span></div>' +
                    '<div class="gp-grid-2" style="margin-bottom:8px;">' +
                        '<div>' +
                            '<label>CUSTOM FIELD ID</label>' +
                            '<input type="text" id="gp-adv-custom-id" placeholder="field_11">' +
                        '</div>' +
                        '<div>' +
                            '<label>INPUT MASK</label>' +
                            '<input type="text" id="gp-adv-input-mask" placeholder="(999) 999-9999">' +
                        '</div>' +
                    '</div>' +
                    '<div class="gp-toggle-row">' +
                        '<span>Required Field (Block Empty Submit)</span>' +
                        '<label class="gp-switch"><input type="checkbox" id="gp-adv-required"><span class="gp-slider"></span></label>' +
                    '</div>' +
                '</div>' +
                '<div class="gp-card-section">' +
                    '<div class="gp-card-header"><span>SANITIZATION FILTERS (wppoppop)</span></div>' +
                    '<p class="gp-help-note">Click to toggle pre-validation input sanitizers:</p>' +
                    '<div class="gp-pill-group" id="gp-adv-filters-group">' +
                        '<button type="button" class="gp-pill-btn" data-filter="trim">+ Trim</button>' +
                        '<button type="button" class="gp-pill-btn" data-filter="strip_tags">+ Strip Tags</button>' +
                        '<button type="button" class="gp-pill-btn" data-filter="alpha">+ Alpha</button>' +
                        '<button type="button" class="gp-pill-btn" data-filter="alphanumeric">+ Alphanumeric</button>' +
                        '<button type="button" class="gp-pill-btn" data-filter="digits">+ Digits</button>' +
                        '<button type="button" class="gp-pill-btn" data-filter="regex">+ Regex</button>' +
                    '</div>' +
                '</div>' +
                '<div class="gp-card-section">' +
                    '<div class="gp-card-header"><span>VALIDATION RULES (wppoppop)</span></div>' +
                    '<p class="gp-help-note">Click to toggle server &amp; client validators:</p>' +
                    '<div class="gp-pill-group" id="gp-adv-validators-group">' +
                        '<button type="button" class="gp-pill-btn" data-validator="email">+ Email RFC</button>' +
                        '<button type="button" class="gp-pill-btn" data-validator="equal">+ Equal</button>' +
                        '<button type="button" class="gp-pill-btn" data-validator="equal_field">+ Equal To Field</button>' +
                        '<button type="button" class="gp-pill-btn" data-validator="in_array">+ In Array</button>' +
                        '<button type="button" class="gp-pill-btn" data-validator="no_duplicates">+ Prevent Duplicates</button>' +
                    '</div>' +
                    '<div id="gp-adv-validator-param-wrap" style="margin-top:8px;">' +
                        '<label>VALIDATOR PARAMETER / REGEX PATTERN</label>' +
                        '<input type="text" id="gp-adv-validator-pattern" placeholder="Optional regex or comma list for Equal/In Array">' +
                    '</div>' +
                '</div>';
            $('#insp-tab-advanced').html(html);
        },

        syncTabsForElement: function(el) {
            if (!el) return;
            var cType = this.canonicalType(el.type);
            var isForm = this.isFormElement(cType);

            // 1. Update Header Badge with Canonical Label
            $('#insp-element-type-badge').text(ELEMENT_LABELS[cType] || cType.toUpperCase());

            // 2. Ensure 3 Tabs for Media/Submit and 5 Tabs for Form Family
            var $tabBar = $('.wppoppop-insp-tab').first().parent();
            if (!$tabBar.find('.wppoppop-insp-tab[data-tab="data"]').length) {
                $tabBar.append('<button type="button" class="wppoppop-insp-tab" data-tab="data">Data</button>');
            }
            if (!$tabBar.find('.wppoppop-insp-tab[data-tab="advanced"]').length) {
                $tabBar.append('<button type="button" class="wppoppop-insp-tab" data-tab="advanced">Adv</button>');
            }
            $tabBar.find('.wppoppop-insp-tab[data-tab="basic"]').text('Content');
            $tabBar.find('.wppoppop-insp-tab[data-tab="style"]').text('Style');
            $tabBar.find('.wppoppop-insp-tab[data-tab="logic"]').text('Logic');
            $tabBar.find('.wppoppop-insp-tab[data-tab="data"]').text('Data').toggle(isForm);
            $tabBar.find('.wppoppop-insp-tab[data-tab="advanced"]').text('Adv').toggle(isForm);

            // 3. Render Element-Specific Sanitized Content Module
            this.renderElementContentModule(el, cType);

            // 4. Contextualize Style Tab (Remove duplicate/irrelevant controls)
            this.contextualizeStyleTab(el, cType, isForm);

            // 5. Contextualize Logic Tab (Separate Click Actions from Conditional Visibility)
            this.contextualizeLogicTab(el, cType, isForm);

            // 6. Populate Data & Advanced Tabs if Form Element
            if (isForm) {
                this.populateDataTab(el, cType);
                this.populateAdvancedTab(el, cType);
            }
        },

        renderElementContentModule: function(el, cType) {
            var html = '<div class="gp-card-section"><div class="gp-card-header"><span>' + (ELEMENT_LABELS[cType] || cType.toUpperCase()) + ' SETTINGS</span></div>';

            switch (cType) {
                case 'shape':
                    var preset = el.shapePreset || 'rectangle';
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>VECTOR SHAPE PRESET</label>' +
                            '<select id="gp-cnt-shape-preset">' +
                                '<option value="rectangle" ' + (preset === 'rectangle' ? 'selected' : '') + '>Rectangle / Box</option>' +
                                '<option value="square" ' + (preset === 'square' ? 'selected' : '') + '>Square</option>' +
                                '<option value="circle" ' + (preset === 'circle' ? 'selected' : '') + '>Circle / Oval</option>' +
                                '<option value="pill" ' + (preset === 'pill' ? 'selected' : '') + '>Rounded Pill</option>' +
                                '<option value="triangle" ' + (preset === 'triangle' ? 'selected' : '') + '>Triangle</option>' +
                                '<option value="star" ' + (preset === 'star' ? 'selected' : '') + '>5-Point Star</option>' +
                                '<option value="badge" ' + (preset === 'badge' ? 'selected' : '') + '>Promo Seal / Badge</option>' +
                                '<option value="divider" ' + (preset === 'divider' ? 'selected' : '') + '>Horizontal Divider Line</option>' +
                            '</select>' +
                        '</div>';
                    break;

                case 'image':
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>IMAGE SOURCE URL</label>' +
                            '<div style="display:flex;gap:6px;">' +
                                '<input type="url" id="gp-cnt-image-url" value="' + this.esc(el.imageUrl || el.content || '') + '" placeholder="https://...">' +
                                '<button type="button" id="gp-cnt-media-btn" class="gp-btn-accent" style="white-space:nowrap;padding:0 10px;">Media</button>' +
                            '</div>' +
                        '</div>' +
                        '<div class="gp-grid-2">' +
                            '<div><label>ALT TEXT</label><input type="text" id="gp-cnt-image-alt" value="' + this.esc(el.altText || '') + '" placeholder="Describe image"></div>' +
                            '<div><label>OBJECT FIT</label>' +
                                '<select id="gp-cnt-image-fit">' +
                                    '<option value="cover" ' + ((el.objectFit || 'cover') === 'cover' ? 'selected' : '') + '>Cover</option>' +
                                    '<option value="contain" ' + (el.objectFit === 'contain' ? 'selected' : '') + '>Contain</option>' +
                                    '<option value="fill" ' + (el.objectFit === 'fill' ? 'selected' : '') + '>Stretch Fill</option>' +
                                '</select>' +
                            '</div>' +
                        '</div>';
                    break;

                case 'video':
                    var vProv = el.videoProvider || 'youtube';
                    html += '' +
                        '<div class="gp-grid-2" style="margin-bottom:8px;">' +
                            '<div><label>PROVIDER</label>' +
                                '<select id="gp-cnt-video-provider">' +
                                    '<option value="youtube" ' + (vProv === 'youtube' ? 'selected' : '') + '>YouTube</option>' +
                                    '<option value="vimeo" ' + (vProv === 'vimeo' ? 'selected' : '') + '>Vimeo</option>' +
                                    '<option value="mp4" ' + (vProv === 'mp4' ? 'selected' : '') + '>Direct MP4</option>' +
                                '</select>' +
                            '</div>' +
                            '<div><label>ASPECT RATIO</label>' +
                                '<select id="gp-cnt-video-ratio">' +
                                    '<option value="16:9" ' + ((el.aspectRatio || '16:9') === '16:9' ? 'selected' : '') + '>16:9 Widescreen</option>' +
                                    '<option value="4:3" ' + (el.aspectRatio === '4:3' ? 'selected' : '') + '>4:3 Standard</option>' +
                                    '<option value="1:1" ' + (el.aspectRatio === '1:1' ? 'selected' : '') + '>1:1 Square</option>' +
                                '</select>' +
                            '</div>' +
                        '</div>' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>VIDEO URL OR EMBED ID</label>' +
                            '<input type="url" id="gp-cnt-video-url" value="' + this.esc(el.videoUrl || el.content || '') + '" placeholder="https://www.youtube.com/watch?v=...">' +
                        '</div>' +
                        '<div class="gp-grid-2">' +
                            '<label class="gp-check-inline"><input type="checkbox" id="gp-cnt-video-autoplay" ' + (el.autoplay ? 'checked' : '') + '> Autoplay</label>' +
                            '<label class="gp-check-inline"><input type="checkbox" id="gp-cnt-video-mute" ' + (el.mute ? 'checked' : '') + '> Muted</label>' +
                            '<label class="gp-check-inline"><input type="checkbox" id="gp-cnt-video-loop" ' + (el.loop ? 'checked' : '') + '> Loop</label>' +
                            '<label class="gp-check-inline"><input type="checkbox" id="gp-cnt-video-controls" ' + (el.controls !== false ? 'checked' : '') + '> Controls</label>' +
                        '</div>';
                    break;

                case 'label':
                    var tag = el.htmlTag || 'p';
                    var tTrans = el.textTransform || 'none';
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>TEXT / HEADLINE CONTENT</label>' +
                            '<textarea id="gp-cnt-label-text" rows="3">' + this.esc(el.content || '') + '</textarea>' +
                        '</div>' +
                        '<div class="gp-grid-2">' +
                            '<div><label>SEMANTIC HTML TAG</label>' +
                                '<select id="gp-cnt-label-tag">' +
                                    '<option value="h1" ' + (tag === 'h1' ? 'selected' : '') + '>H1 Heading</option>' +
                                    '<option value="h2" ' + (tag === 'h2' ? 'selected' : '') + '>H2 Heading</option>' +
                                    '<option value="h3" ' + (tag === 'h3' ? 'selected' : '') + '>H3 Heading</option>' +
                                    '<option value="h4" ' + (tag === 'h4' ? 'selected' : '') + '>H4 Heading</option>' +
                                    '<option value="p" ' + (tag === 'p' ? 'selected' : '') + '>Paragraph (&lt;p&gt;)</option>' +
                                    '<option value="div" ' + (tag === 'div' ? 'selected' : '') + '>Block (&lt;div&gt;)</option>' +
                                    '<option value="span" ' + (tag === 'span' ? 'selected' : '') + '>Inline (&lt;span&gt;)</option>' +
                                '</select>' +
                            '</div>' +
                            '<div><label>TEXT TRANSFORM</label>' +
                                '<select id="gp-cnt-label-transform">' +
                                    '<option value="none" ' + (tTrans === 'none' ? 'selected' : '') + '>Normal</option>' +
                                    '<option value="uppercase" ' + (tTrans === 'uppercase' ? 'selected' : '') + '>UPPERCASE</option>' +
                                    '<option value="lowercase" ' + (tTrans === 'lowercase' ? 'selected' : '') + '>lowercase</option>' +
                                    '<option value="capitalize" ' + (tTrans === 'capitalize' ? 'selected' : '') + '>Capitalize</option>' +
                                '</select>' +
                            '</div>' +
                        '</div>';
                    break;

                case 'html':
                    html += '' +
                        '<div style="margin-bottom:6px;">' +
                            '<label>RAW HTML / SVG / EMBED MARKUP</label>' +
                            '<textarea id="gp-cnt-html-code" rows="5" class="gp-mono-code">' + this.esc(el.content || '') + '</textarea>' +
                        '</div>';
                    break;

                case 'close':
                    var cGlyph = el.closeGlyph || '×';
                    html += '' +
                        '<div class="gp-grid-2" style="margin-bottom:6px;">' +
                            '<div><label>ICON GLYPH</label>' +
                                '<select id="gp-cnt-close-glyph">' +
                                    '<option value="×" ' + (cGlyph === '×' ? 'selected' : '') + '>Standard Cross (×)</option>' +
                                    '<option value="✕" ' + (cGlyph === '✕' ? 'selected' : '') + '>Crisp X (✕)</option>' +
                                    '<option value="✖" ' + (cGlyph === '✖' ? 'selected' : '') + '>Bold X (✖)</option>' +
                                '</select>' +
                            '</div>' +
                            '<div><label>TOOLTIP TITLE</label>' +
                                '<input type="text" id="gp-cnt-close-title" value="' + this.esc(el.tooltip || 'Close') + '">' +
                            '</div>' +
                        '</div>';
                    break;

                case 'icon':
                    html += '' +
                        '<div class="gp-grid-2" style="margin-bottom:6px;">' +
                            '<div><label>FONT AWESOME CLASS</label>' +
                                '<input type="text" id="gp-cnt-icon-class" value="' + this.esc(el.iconClass || el.content || 'fas fa-star') + '" placeholder="fas fa-star">' +
                            '</div>' +
                            '<div><label>ICON SIZE (PX)</label>' +
                                '<input type="number" id="gp-cnt-icon-size" value="' + (el.fontSize || 32) + '" min="12" max="160">' +
                            '</div>' +
                        '</div>';
                    break;

                case 'progress':
                    html += '' +
                        '<div class="gp-grid-2" style="margin-bottom:8px;">' +
                            '<div><label>FILL PERCENT (0–100%)</label>' +
                                '<input type="number" id="gp-cnt-prog-pct" min="0" max="100" value="' + (parseInt(el.content, 10) || 65) + '">' +
                            '</div>' +
                            '<div><label>BAR FILL COLOR</label>' +
                                '<input type="color" id="gp-cnt-prog-color" value="' + (el.progressColor || '#2563eb') + '">' +
                            '</div>' +
                        '</div>' +
                        '<div class="gp-grid-2">' +
                            '<label class="gp-check-inline"><input type="checkbox" id="gp-cnt-prog-label" ' + (el.showLabel !== false ? 'checked' : '') + '> Show % Text</label>' +
                            '<label class="gp-check-inline"><input type="checkbox" id="gp-cnt-prog-striped" ' + (el.striped ? 'checked' : '') + '> Striped Animation</label>' +
                        '</div>';
                    break;

                case 'button':
                case 'submit':
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>BUTTON LABEL TEXT</label>' +
                            '<input type="text" id="gp-cnt-btn-label" value="' + this.esc(el.content || (cType === 'submit' ? 'Submit Form' : 'Click Here')) + '">' +
                        '</div>' +
                        '<div class="gp-grid-2">' +
                            '<div><label>BUTTON ICON CLASS</label>' +
                                '<input type="text" id="gp-cnt-btn-icon" value="' + this.esc(el.btnIcon || '') + '" placeholder="fas fa-arrow-right">' +
                            '</div>' +
                            '<div><label>' + (cType === 'submit' ? 'LOADING TEXT' : 'SUBTEXT') + '</label>' +
                                '<input type="text" id="gp-cnt-btn-sub" value="' + this.esc(el.loadingText || el.subtext || '') + '" placeholder="Please wait...">' +
                            '</div>' +
                        '</div>';
                    break;

                case 'text':
                case 'email':
                case 'password':
                    var defAuto = cType === 'email' ? 'email' : (cType === 'password' ? 'current-password' : 'on');
                    var curAuto = el.autocomplete || defAuto;
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>PLACEHOLDER TEXT</label>' +
                            '<input type="text" id="gp-cnt-input-placeholder" value="' + this.esc(el.placeholder || el.content || '') + '" placeholder="Please enter ' + cType + '...">' +
                        '</div>' +
                        '<div>' +
                            '<label>AUTOCOMPLETE ATTRIBUTE</label>' +
                            '<select id="gp-cnt-input-autocomplete">' +
                                '<option value="on" ' + (curAuto === 'on' ? 'selected' : '') + '>Enabled (on)</option>' +
                                '<option value="off" ' + (curAuto === 'off' ? 'selected' : '') + '>Disabled (off)</option>' +
                                '<option value="email" ' + (curAuto === 'email' ? 'selected' : '') + '>Email Address (email)</option>' +
                                '<option value="name" ' + (curAuto === 'name' ? 'selected' : '') + '>Full Name (name)</option>' +
                                '<option value="given-name" ' + (curAuto === 'given-name' ? 'selected' : '') + '>First Name (given-name)</option>' +
                                '<option value="family-name" ' + (curAuto === 'family-name' ? 'selected' : '') + '>Last Name (family-name)</option>' +
                                '<option value="username" ' + (curAuto === 'username' ? 'selected' : '') + '>Username (username)</option>' +
                                '<option value="current-password" ' + (curAuto === 'current-password' ? 'selected' : '') + '>Password (current-password)</option>' +
                            '</select>' +
                        '</div>';
                    break;

                case 'number':
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>PLACEHOLDER TEXT</label>' +
                            '<input type="text" id="gp-cnt-input-placeholder" value="' + this.esc(el.placeholder || el.content || 'Enter amount...') + '">' +
                        '</div>' +
                        '<div class="gp-grid-3">' +
                            '<div><label>MIN</label><input type="number" id="gp-cnt-num-min" value="' + (el.min !== undefined ? el.min : 0) + '"></div>' +
                            '<div><label>MAX</label><input type="number" id="gp-cnt-num-max" value="' + (el.max !== undefined ? el.max : 100) + '"></div>' +
                            '<div><label>STEP</label><input type="number" id="gp-cnt-num-step" value="' + (el.step !== undefined ? el.step : 1) + '"></div>' +
                        '</div>';
                    break;

                case 'phone':
                    html += '' +
                        '<div class="gp-grid-2" style="margin-bottom:8px;">' +
                            '<div><label>DIAL PREFIX</label>' +
                                '<input type="text" id="gp-cnt-phone-prefix" value="' + this.esc(el.dialPrefix || '+1') + '" placeholder="+1">' +
                            '</div>' +
                            '<div><label>AUTOCOMPLETE</label>' +
                                '<select id="gp-cnt-input-autocomplete">' +
                                    '<option value="tel" selected>Telephone (tel)</option>' +
                                    '<option value="off">Disabled (off)</option>' +
                                '</select>' +
                            '</div>' +
                        '</div>' +
                        '<div>' +
                            '<label>PLACEHOLDER TEXT</label>' +
                            '<input type="text" id="gp-cnt-input-placeholder" value="' + this.esc(el.placeholder || el.content || '(555) 019-2834') + '">' +
                        '</div>';
                    break;

                case 'textarea':
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>PLACEHOLDER TEXT</label>' +
                            '<input type="text" id="gp-cnt-input-placeholder" value="' + this.esc(el.placeholder || el.content || 'Write your message...') + '">' +
                        '</div>' +
                        '<div><label>VISIBLE ROWS</label>' +
                            '<input type="number" id="gp-cnt-textarea-rows" min="2" max="15" value="' + (el.rows || 4) + '">' +
                        '</div>';
                    break;

                case 'select':
                case 'radio':
                case 'checkbox':
                case 'multiselect':
                    var optVal = Array.isArray(el.options) ? el.options.join('\n') : (el.content || 'Option 1\nOption 2\nOption 3');
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>CHOICES / OPTIONS (ONE PER LINE OR COMMA SEPARATED)</label>' +
                            '<textarea id="gp-cnt-choice-options" rows="3">' + this.esc(optVal) + '</textarea>' +
                        '</div>';
                    if (cType === 'checkbox' || cType === 'radio') {
                        html += '' +
                            '<div><label>LAYOUT ORIENTATION</label>' +
                                '<select id="gp-cnt-choice-layout">' +
                                    '<option value="inline" ' + ((el.choiceLayout || 'inline') === 'inline' ? 'selected' : '') + '>Horizontal Inline</option>' +
                                    '<option value="stacked" ' + (el.choiceLayout === 'stacked' ? 'selected' : '') + '>Vertical Stacked</option>' +
                                '</select>' +
                            '</div>';
                    }
                    break;

                case 'imageselect':
                case 'tile':
                    var tileVal = Array.isArray(el.options) ? el.options.join('\n') : (el.content || 'Option A\nOption B\nOption C');
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>' + (cType === 'imageselect' ? 'IMAGE CHOICES (LABEL|IMAGE_URL PER LINE)' : 'TILE CHOICES (LABEL|ICON_CLASS PER LINE)') + '</label>' +
                            '<textarea id="gp-cnt-choice-options" rows="3">' + this.esc(tileVal) + '</textarea>' +
                        '</div>' +
                        '<div class="gp-grid-2">' +
                            '<div><label>GRID COLUMNS</label>' +
                                '<input type="number" id="gp-cnt-grid-cols" min="1" max="4" value="' + (el.gridCols || 2) + '">' +
                            '</div>' +
                            '<div style="display:flex;align-items:flex-end;padding-bottom:4px;">' +
                                '<label class="gp-check-inline"><input type="checkbox" id="gp-cnt-multi-toggle" ' + (el.multiSelect ? 'checked' : '') + '> Multi-Select</label>' +
                            '</div>' +
                        '</div>';
                    break;

                case 'date':
                case 'time':
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>PLACEHOLDER TEXT</label>' +
                            '<input type="text" id="gp-cnt-input-placeholder" value="' + this.esc(el.placeholder || el.content || (cType === 'date' ? 'YYYY-MM-DD' : 'HH:MM')) + '">' +
                        '</div>' +
                        '<div><label>FORMAT</label>' +
                            '<input type="text" id="gp-cnt-datetime-fmt" value="' + this.esc(el.dateFormat || (cType === 'date' ? 'Y-m-d' : 'H:i')) + '">' +
                        '</div>';
                    break;

                case 'upload':
                    html += '' +
                        '<div style="margin-bottom:8px;">' +
                            '<label>DROPZONE / BUTTON LABEL</label>' +
                            '<input type="text" id="gp-cnt-upload-label" value="' + this.esc(el.content || 'Choose File or Drop Here') + '">' +
                        '</div>' +
                        '<div class="gp-grid-2">' +
                            '<div><label>ALLOWED EXTENSIONS</label>' +
                                '<input type="text" id="gp-cnt-upload-exts" value="' + this.esc(el.fileExts || '.jpg,.png,.pdf') + '">' +
                            '</div>' +
                            '<div><label>MAX SIZE (MB)</label>' +
                                '<input type="number" id="gp-cnt-upload-max" min="1" max="100" value="' + (el.fileMaxMb || 5) + '">' +
                            '</div>' +
                        '</div>';
                    break;

                case 'rating':
                    html += '' +
                        '<div class="gp-grid-2">' +
                            '<div><label>MAX STARS (3–10)</label>' +
                                '<input type="number" id="gp-cnt-rating-max" min="3" max="10" value="' + (parseInt(el.content, 10) || 5) + '">' +
                            '</div>' +
                            '<div><label>ACTIVE STAR COLOR</label>' +
                                '<input type="color" id="gp-cnt-rating-color" value="' + (el.ratingColor || '#f59e0b') + '">' +
                            '</div>' +
                        '</div>';
                    break;
            }

            html += '</div>';

            // Entrance & Timing Animation Section (Consolidated in Content Tab)
            var curAnim = el.animEffect || 'none';
            html += '' +
                '<div class="gp-card-section">' +
                    '<div class="gp-card-header"><span>APPEARANCE ANIMATION (wppoppop)</span></div>' +
                    '<div style="margin-bottom:8px;">' +
                        '<label>ENTRANCE EFFECT</label>' +
                        '<select id="gp-cnt-anim-effect">' +
                            '<option value="none" ' + (curAnim === 'none' ? 'selected' : '') + '>None</option>' +
                            '<option value="fade" ' + (curAnim === 'fade' ? 'selected' : '') + '>Fade In</option>' +
                            '<option value="slideDown" ' + (curAnim === 'slideDown' ? 'selected' : '') + '>Slide Down</option>' +
                            '<option value="slideUp" ' + (curAnim === 'slideUp' ? 'selected' : '') + '>Slide Up</option>' +
                            '<option value="bounceIn" ' + (curAnim === 'bounceIn' ? 'selected' : '') + '>Bounce In</option>' +
                            '<option value="zoomIn" ' + (curAnim === 'zoomIn' ? 'selected' : '') + '>Zoom In</option>' +
                            '<option value="pulse" ' + (curAnim === 'pulse' ? 'selected' : '') + '>Pulse</option>' +
                        '</select>' +
                    '</div>' +
                    '<div class="gp-grid-2">' +
                        '<div><label>DURATION (MS)</label><input type="number" id="gp-cnt-anim-duration" step="50" value="' + (el.animDuration || 600) + '"></div>' +
                        '<div><label>START DELAY (MS)</label><input type="number" id="gp-cnt-anim-delay" step="50" value="' + (el.animDelay || 0) + '"></div>' +
                    '</div>' +
                '</div>';

            $('#wppoppop-gp-sanitized-content').html(html);
        },

        contextualizeStyleTab: function(el, cType, isForm) {
            // 1. Show/Hide Typography Card based on whether element renders text
            var hasTypo = TYPOGRAPHY_CAPABLE.indexOf(cType) !== -1;
            $('#prop-font-family').closest('div').toggle(hasTypo);

            // 2. Show/Hide Form Global Input Notice
            $('#gp-style-form-notice').toggle(isForm);

            // 3. Show/Hide Input Icons Card (Only single-line form fields)
            var hasInputIcons = INPUT_ICON_CAPABLE.indexOf(cType) !== -1;
            $('#gp-style-input-icons-card').toggle(hasInputIcons);
            if (hasInputIcons) {
                $('#gp-prop-icon-left').val(el.iconLeft || '');
                $('#gp-prop-icon-right').val(el.iconRight || '');
                $('#gp-prop-icon-offset').val(el.iconOffset !== undefined ? el.iconOffset : 10);
                $('#gp-prop-icon-color').val(el.iconColor || '#64748b');
            }

            // 4. Show/Hide Hover State Card (Buttons, Submit, Close, Icon, Tile, ImageSelect)
            var hasHover = HOVER_CAPABLE.indexOf(cType) !== -1;
            $('#gp-style-hover-card').toggle(hasHover);
            if (hasHover) {
                $('#gp-prop-hover-bg').val(el.hoverBgColor || '#1d4ed8');
                $('#gp-prop-hover-color').val(el.hoverColor || '#ffffff');
                $('#gp-prop-hover-border').val(el.hoverBorderColor || '#1e40af');
            }

            // 5. Hide duplicate Entrance Animation select inside Style Tab (since it lives cleanly in Content Tab)
            $('#prop-anim-effect').prev('label').hide();
            $('#prop-anim-effect').hide();

            // 6. Populate Transform Rotation & Shadow Builder
            $('#gp-prop-rotation').val(el.rotation || 0);
            var shMode = (el.boxShadow && el.boxShadow !== 'none') ? (el.boxShadow.indexOf('inset') !== -1 ? 'inset' : 'custom') : 'none';
            $('#gp-prop-shadow-mode').val(shMode);
            $('#gp-shadow-sliders-wrap').toggle(shMode !== 'none');
            $('#gp-sh-x').val(el.shadowX !== undefined ? el.shadowX : 0);
            $('#gp-sh-y').val(el.shadowY !== undefined ? el.shadowY : 4);
            $('#gp-sh-blur').val(el.shadowBlur !== undefined ? el.shadowBlur : 12);
            $('#gp-sh-spread').val(el.shadowSpread !== undefined ? el.shadowSpread : 0);
            $('#gp-sh-color').val(el.shadowColor || '#0f172a');

            // 7. Populate Custom CSS Class & Inline Style
            $('#gp-prop-css-class').val(el.customClass || '');
            $('#gp-prop-css-inline').val(el.customCss || '');
        },

        contextualizeLogicTab: function(el, cType, isForm) {
            // 1. Hide duplicate Merge Tag preview in Logic Tab for Media elements, and for Form elements it lives in Data Tab
            $('#prop-display-token').closest('div').hide();

            // 2. Show Click Redirect / Close Behavior ONLY on clickable CTA/Media elements (NOT form inputs)
            var hasClickLogic = CLICK_LOGIC_CAPABLE.indexOf(cType) !== -1;
            $('#prop-action-url').closest('div').toggle(hasClickLogic);
            $('#prop-action-close').closest('div').toggle(hasClickLogic);
            $('#prop-action-js').closest('div').toggle(hasClickLogic);

            // For Submit or Button, also allow Canvas Step target selection
            if (cType === 'button' || cType === 'submit') {
                if (!$('#gp-logic-step-routing').length) {
                    $('#prop-action-url').closest('div').before(
                        '<div id="gp-logic-step-routing" class="gp-card-section">' +
                            '<div class="gp-card-header"><span>STEP &amp; SUBMISSION ROUTING</span></div>' +
                            '<div class="gp-grid-2">' +
                                '<div><label>CLICK / SUBMIT ACTION</label>' +
                                    '<select id="gp-logic-cta-mode">' +
                                        '<option value="default">Default / Save &amp; Close</option>' +
                                        '<option value="next_canvas">Advance to Target Canvas</option>' +
                                        '<option value="redirect">Redirect to URL</option>' +
                                        '<option value="close">Close Popup</option>' +
                                    '</select>' +
                                '</div>' +
                                '<div><label>TARGET CANVAS #</label>' +
                                    '<input type="number" id="gp-logic-target-canvas" min="1" max="20" value="2">' +
                                '</div>' +
                            '</div>' +
                        '</div>'
                    );
                }
                $('#gp-logic-step-routing').show();
                $('#gp-logic-cta-mode').val(el.submitAction || (el.type === 'step_btn' ? 'next_canvas' : 'default'));
                $('#gp-logic-target-canvas').val(el.goto_canvas || el.goto_screen || 2);
            } else {
                $('#gp-logic-step-routing').hide();
            }

            // 3. Populate Conditional Visibility Builder (Available to ALL elements)
            var logicEnabled = !!el.logicEnabled;
            $('#gp-logic-enabled').prop('checked', logicEnabled);
            $('#gp-logic-builder-body').toggle(logicEnabled);
            $('#gp-logic-action').val(el.logicAction || 'show');
            $('#gp-logic-match').val(el.logicMatch || 'all');
            this.renderLogicRulesList(el);
        },

        renderLogicRulesList: function(el) {
            var rules = Array.isArray(el.logicRules) ? el.logicRules : [];
            var $list = $('#gp-logic-rules-list');
            $list.empty();
            rules.forEach(function(r, idx) {
                var row = '' +
                    '<div class="gp-logic-rule-row" data-idx="' + idx + '" style="display:grid;grid-template-columns:1fr 90px 1fr 24px;gap:4px;margin-bottom:6px;align-items:center;">' +
                        '<input type="text" class="gp-rule-field" placeholder="{field}" value="' + (r.field || '') + '">' +
                        '<select class="gp-rule-op">' +
                            '<option value="is" ' + (r.op === 'is' ? 'selected' : '') + '>Equals</option>' +
                            '<option value="not" ' + (r.op === 'not' ? 'selected' : '') + '>Not Equal</option>' +
                            '<option value="contains" ' + (r.op === 'contains' ? 'selected' : '') + '>Contains</option>' +
                            '<option value="filled" ' + (r.op === 'filled' ? 'selected' : '') + '>Is Filled</option>' +
                        '</select>' +
                        '<input type="text" class="gp-rule-val" placeholder="Value..." value="' + (r.val || '') + '">' +
                        '<button type="button" class="gp-rule-del" style="background:#fee2e2;color:#b91c1c;border:none;border-radius:4px;height:26px;cursor:pointer;">&times;</button>' +
                    '</div>';
                $list.append(row);
            });
        },

        populateDataTab: function(el, cType) {
            var fName = el.field_name || cType;
            $('#gp-data-fieldname').val(fName);
            $('#gp-data-token-preview').val('{' + fName + '}');
            $('#gp-data-default-val').val(el.defaultValue || '');
            var dynOn = !!el.dynamicDefault;
            $('#gp-data-dynamic-enable').prop('checked', dynOn);
            $('#gp-data-param-wrap').toggle(dynOn);
            $('#gp-data-param-key').val(el.dynamicParam || '');
            $('#gp-data-readonly').prop('checked', !!el.readOnly);
            $('#gp-data-save-db').prop('checked', el.saveToDb !== false);
        },

        populateAdvancedTab: function(el, cType) {
            $('#gp-adv-custom-id').val(el.customId || ('field_' + (el.id || '').substr(-4)));
            $('#gp-adv-input-mask').val(el.inputMask || '');
            $('#gp-adv-required').prop('checked', !!el.required);
            $('#gp-adv-validator-pattern').val(el.validatorPattern || '');

            var filters = Array.isArray(el.filters) ? el.filters : (cType === 'email' || cType === 'text' ? ['trim'] : []);
            $('#gp-adv-filters-group .gp-pill-btn').each(function() {
                var f = $(this).data('filter');
                $(this).toggleClass('active', filters.indexOf(f) !== -1);
            });

            var validators = Array.isArray(el.validators) ? el.validators : (cType === 'email' ? ['email'] : []);
            $('#gp-adv-validators-group .gp-pill-btn').each(function() {
                var v = $(this).data('validator');
                $(this).toggleClass('active', validators.indexOf(v) !== -1);
            });
        },

        bindSanitizedEvents: function() {
            var self = this;
            var getActiveEl = function() {
                if (!window.WpPopPopBuilderCore || !window.WpPopPopBuilderCore.state.activeId) return null;
                var activeId = window.WpPopPopBuilderCore.state.activeId;
                var elements = window.WpPopPopBuilderCanvas.getActiveElements();
                return elements.find(function(e) { return e.id === activeId; });
            };

            // Content Tab Dynamic Module Inputs
            $(document).on('input change', '#wppoppop-gp-sanitized-content input, #wppoppop-gp-sanitized-content select, #wppoppop-gp-sanitized-content textarea', function() {
                var el = getActiveEl();
                if (!el) return;
                var cType = self.canonicalType(el.type);

                switch (cType) {
                    case 'shape':
                        el.shapePreset = $('#gp-cnt-shape-preset').val();
                        break;
                    case 'image':
                        el.imageUrl = $('#gp-cnt-image-url').val();
                        el.content = el.imageUrl;
                        el.altText = $('#gp-cnt-image-alt').val();
                        el.objectFit = $('#gp-cnt-image-fit').val();
                        break;
                    case 'video':
                        el.videoProvider = $('#gp-cnt-video-provider').val();
                        el.aspectRatio = $('#gp-cnt-video-ratio').val();
                        el.videoUrl = $('#gp-cnt-video-url').val();
                        el.content = el.videoUrl;
                        el.autoplay = $('#gp-cnt-video-autoplay').is(':checked');
                        el.mute = $('#gp-cnt-video-mute').is(':checked');
                        el.loop = $('#gp-cnt-video-loop').is(':checked');
                        el.controls = $('#gp-cnt-video-controls').is(':checked');
                        break;
                    case 'label':
                        el.content = $('#gp-cnt-label-text').val();
                        el.htmlTag = $('#gp-cnt-label-tag').val();
                        el.textTransform = $('#gp-cnt-label-transform').val();
                        break;
                    case 'html':
                        el.content = $('#gp-cnt-html-code').val();
                        break;
                    case 'close':
                        el.closeGlyph = $('#gp-cnt-close-glyph').val();
                        el.content = el.closeGlyph;
                        el.tooltip = $('#gp-cnt-close-title').val();
                        break;
                    case 'icon':
                        el.iconClass = $('#gp-cnt-icon-class').val();
                        el.content = el.iconClass;
                        el.fontSize = parseInt($('#gp-cnt-icon-size').val(), 10) || 32;
                        $('#prop-font-size').val(el.fontSize);
                        break;
                    case 'progress':
                        el.content = parseInt($('#gp-cnt-prog-pct').val(), 10) || 0;
                        el.progressColor = $('#gp-cnt-prog-color').val();
                        el.showLabel = $('#gp-cnt-prog-label').is(':checked');
                        el.striped = $('#gp-cnt-prog-striped').is(':checked');
                        break;
                    case 'button':
                    case 'submit':
                        el.content = $('#gp-cnt-btn-label').val();
                        el.btnIcon = $('#gp-cnt-btn-icon').val();
                        el.loadingText = $('#gp-cnt-btn-sub').val();
                        el.subtext = el.loadingText;
                        break;
                    case 'text':
                    case 'email':
                    case 'password':
                        el.placeholder = $('#gp-cnt-input-placeholder').val();
                        el.content = el.placeholder;
                        el.autocomplete = $('#gp-cnt-input-autocomplete').val();
                        break;
                    case 'number':
                        el.placeholder = $('#gp-cnt-input-placeholder').val();
                        el.content = el.placeholder;
                        el.min = parseFloat($('#gp-cnt-num-min').val()) || 0;
                        el.max = parseFloat($('#gp-cnt-num-max').val()) || 100;
                        el.step = parseFloat($('#gp-cnt-num-step').val()) || 1;
                        break;
                    case 'phone':
                        el.dialPrefix = $('#gp-cnt-phone-prefix').val();
                        el.placeholder = $('#gp-cnt-input-placeholder').val();
                        el.content = el.placeholder;
                        el.autocomplete = $('#gp-cnt-input-autocomplete').val();
                        break;
                    case 'textarea':
                        el.placeholder = $('#gp-cnt-input-placeholder').val();
                        el.content = el.placeholder;
                        el.rows = parseInt($('#gp-cnt-textarea-rows').val(), 10) || 4;
                        break;
                    case 'select':
                    case 'radio':
                    case 'checkbox':
                    case 'multiselect':
                        var rawOpts = $('#gp-cnt-choice-options').val() || '';
                        el.options = rawOpts.split(/\r?\n|,/).map(function(s) { return s.trim(); }).filter(Boolean);
                        el.content = el.options.join(', ');
                        if ($('#gp-cnt-choice-layout').length) {
                            el.choiceLayout = $('#gp-cnt-choice-layout').val();
                        }
                        break;
                    case 'imageselect':
                    case 'tile':
                        var rawTiles = $('#gp-cnt-choice-options').val() || '';
                        el.options = rawTiles.split(/\r?\n/).map(function(s) { return s.trim(); }).filter(Boolean);
                        el.content = el.options.join(', ');
                        el.gridCols = parseInt($('#gp-cnt-grid-cols').val(), 10) || 2;
                        el.multiSelect = $('#gp-cnt-multi-toggle').is(':checked');
                        break;
                    case 'date':
                    case 'time':
                        el.placeholder = $('#gp-cnt-input-placeholder').val();
                        el.content = el.placeholder;
                        el.dateFormat = $('#gp-cnt-datetime-fmt').val();
                        break;
                    case 'upload':
                        el.content = $('#gp-cnt-upload-label').val();
                        el.fileExts = $('#gp-cnt-upload-exts').val();
                        el.fileMaxMb = parseInt($('#gp-cnt-upload-max').val(), 10) || 5;
                        break;
                    case 'rating':
                        el.content = parseInt($('#gp-cnt-rating-max').val(), 10) || 5;
                        el.ratingColor = $('#gp-cnt-rating-color').val();
                        break;
                }

                // Sync consolidated Animation settings
                if ($('#gp-cnt-anim-effect').length) {
                    el.animEffect = $('#gp-cnt-anim-effect').val() || 'none';
                    el.animDuration = parseInt($('#gp-cnt-anim-duration').val(), 10) || 600;
                    el.animDelay = parseInt($('#gp-cnt-anim-delay').val(), 10) || 0;
                    $('#prop-anim-effect').val(el.animEffect);
                }

                window.WpPopPopBuilderCanvas.renderCanvas();
            });

            // WP Media Library Picker for Image Element
            $(document).on('click', '#gp-cnt-media-btn', function(e) {
                e.preventDefault();
                if (typeof wp !== 'undefined' && wp.media) {
                    var frame = wp.media({ title: 'Select Layer Image', multiple: false, library: { type: 'image' } });
                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        if (attachment && attachment.url) {
                            $('#gp-cnt-image-url').val(attachment.url).trigger('change');
                        }
                    });
                    frame.open();
                }
            });

            // Style Tab: Normal / Hover State Toggle
            $(document).on('click', '#gp-state-mode-switch .gp-seg-btn', function(e) {
                e.preventDefault();
                var st = $(this).data('state');
                $('#gp-state-mode-switch .gp-seg-btn').removeClass('active');
                $(this).addClass('active');
                $('#gp-hover-fields-panel').toggle(st === 'hover');
            });

            // Style Tab: Enhanced Controls Sync
            $(document).on('input change', '#wppoppop-gp-style-enhancements input, #wppoppop-gp-style-enhancements select', function() {
                var el = getActiveEl();
                if (!el) return;

                el.hoverBgColor = $('#gp-prop-hover-bg').val();
                el.hoverColor = $('#gp-prop-hover-color').val();
                el.hoverBorderColor = $('#gp-prop-hover-border').val();

                el.iconLeft = $('#gp-prop-icon-left').val();
                el.iconRight = $('#gp-prop-icon-right').val();
                el.iconOffset = parseInt($('#gp-prop-icon-offset').val(), 10) || 10;
                el.iconColor = $('#gp-prop-icon-color').val();

                el.rotation = parseInt($('#gp-prop-rotation').val(), 10) || 0;

                var shMode = $('#gp-prop-shadow-mode').val();
                $('#gp-shadow-sliders-wrap').toggle(shMode !== 'none');
                el.shadowX = parseInt($('#gp-sh-x').val(), 10) || 0;
                el.shadowY = parseInt($('#gp-sh-y').val(), 10) || 0;
                el.shadowBlur = parseInt($('#gp-sh-blur').val(), 10) || 0;
                el.shadowSpread = parseInt($('#gp-sh-spread').val(), 10) || 0;
                el.shadowColor = $('#gp-sh-color').val() || '#0f172a';

                if (shMode === 'none') {
                    el.boxShadow = 'none';
                } else {
                    var insetPrefix = shMode === 'inset' ? 'inset ' : '';
                    el.boxShadow = insetPrefix + el.shadowX + 'px ' + el.shadowY + 'px ' + el.shadowBlur + 'px ' + el.shadowSpread + 'px ' + el.shadowColor;
                }

                el.customClass = $('#gp-prop-css-class').val();
                el.customCss = $('#gp-prop-css-inline').val();

                window.WpPopPopBuilderCanvas.renderCanvas();
            });

            // Logic Tab: Step Routing & Conditional Visibility
            $(document).on('input change', '#gp-logic-cta-mode, #gp-logic-target-canvas, #gp-logic-enabled, #gp-logic-action, #gp-logic-match', function() {
                var el = getActiveEl();
                if (!el) return;
                if ($('#gp-logic-cta-mode').length) {
                    el.submitAction = $('#gp-logic-cta-mode').val();
                    el.goto_canvas = parseInt($('#gp-logic-target-canvas').val(), 10) || 2;
                    el.goto_screen = el.goto_canvas;
                }
                el.logicEnabled = $('#gp-logic-enabled').is(':checked');
                $('#gp-logic-builder-body').toggle(el.logicEnabled);
                el.logicAction = $('#gp-logic-action').val();
                el.logicMatch = $('#gp-logic-match').val();
            });

            $(document).on('click', '#gp-logic-add-rule', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el) return;
                if (!Array.isArray(el.logicRules)) el.logicRules = [];
                el.logicRules.push({ field: '', op: 'is', val: '' });
                self.renderLogicRulesList(el);
            });

            $(document).on('click', '.gp-rule-del', function(e) {
                e.preventDefault();
                var el = getActiveEl();
                if (!el || !Array.isArray(el.logicRules)) return;
                var idx = parseInt($(this).closest('.gp-logic-rule-row').data('idx'), 10);
                el.logicRules.splice(idx, 1);
                self.renderLogicRulesList(el);
            });

            $(document).on('input change', '.gp-logic-rule-row input, .gp-logic-rule-row select', function() {
                var el = getActiveEl();
                if (!el) return;
                var rules = [];
                $('#gp-logic-rules-list .gp-logic-rule-row').each(function() {
                    rules.push({
                        field: $(this).find('.gp-rule-field').val(),
                        op: $(this).find('.gp-rule-op').val(),
                        val: $(this).find('.gp-rule-val').val()
                    });
                });
                el.logicRules = rules;
            });

            // Data Tab Events (Form Elements)
            $(document).on('input change', '#insp-tab-data input', function() {
                var el = getActiveEl();
                if (!el) return;
                el.field_name = $('#gp-data-fieldname').val() || self.canonicalType(el.type);
                $('#gp-data-token-preview, #prop-display-token').val('{' + el.field_name + '}');
                el.defaultValue = $('#gp-data-default-val').val();
                el.dynamicDefault = $('#gp-data-dynamic-enable').is(':checked');
                $('#gp-data-param-wrap').toggle(el.dynamicDefault);
                el.dynamicParam = $('#gp-data-param-key').val();
                el.readOnly = $('#gp-data-readonly').is(':checked');
                el.saveToDb = $('#gp-data-save-db').is(':checked');
            });

            // Advanced Tab Events (Form Elements)
            $(document).on('input change', '#insp-tab-advanced input', function() {
                var el = getActiveEl();
                if (!el) return;
                el.customId = $('#gp-adv-custom-id').val();
                el.inputMask = $('#gp-adv-input-mask').val();
                el.required = $('#gp-adv-required').is(':checked');
                el.validatorPattern = $('#gp-adv-validator-pattern').val();
            });

            $(document).on('click', '#gp-adv-filters-group .gp-pill-btn', function(e) {
                e.preventDefault();
                $(this).toggleClass('active');
                var el = getActiveEl();
                if (!el) return;
                var activeFilters = [];
                $('#gp-adv-filters-group .gp-pill-btn.active').each(function() {
                    activeFilters.push($(this).data('filter'));
                });
                el.filters = activeFilters;
            });

            $(document).on('click', '#gp-adv-validators-group .gp-pill-btn', function(e) {
                e.preventDefault();
                $(this).toggleClass('active');
                var el = getActiveEl();
                if (!el) return;
                var activeValidators = [];
                $('#gp-adv-validators-group .gp-pill-btn.active').each(function() {
                    activeValidators.push($(this).data('validator'));
                });
                el.validators = activeValidators;
            });
        },

        esc: function(str) {
            return String(str === undefined || str === null ? '' : str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }
    };

    $(document).ready(function() {
        window.WpPopPopSanitizedInspector.init();
    });
})(jQuery);
