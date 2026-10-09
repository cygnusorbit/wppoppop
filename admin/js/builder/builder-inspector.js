/**
 * WpPopPop Builder: Layer Properties Inspector Controller
 * Includes dynamic custom font population & typography synchronization
 */
(function(window, $) {
    'use strict';
    window.WpPopPopBuilderInspector = {
        activeId: null,

        init: function() {
            this.bindTabs();
            this.bindInputs();
            this.bindClose();
            this.populateCustomFonts();
        },

        populateCustomFonts: function() {
            var customFonts = window.wppoppop_custom_fonts || [];
            if (!Array.isArray(customFonts) || customFonts.length === 0) {
                return;
            }

            var $fontSelect = $('#prop-font-family');
            if (!$fontSelect.length) return;

            customFonts.forEach(function(fontName) {
                if ($fontSelect.find('option[value="' + fontName + '"]').length === 0) {
                    $fontSelect.append($('<option>', {
                        value: fontName,
                        text: fontName + ' (Custom)'
                    }));
                }
            });
        },

        bindTabs: function() {
            $('.wppoppop-inspector-tab-btn').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.wppoppop-inspector-tab-btn').removeClass('active');
                $(this).addClass('active');
                $('.wppoppop-inspector-tab-content').removeClass('active').hide();
                $('#wppoppop-inspector-tab-' + tab).addClass('active').show();
            });
        },

        bindClose: function() {
            $('#wppoppop-inspector-close').on('click', function(e) {
                e.preventDefault();
                window.WpPopPopBuilderInspector.close();
            });
        },

        open: function(id) {
            this.activeId = id;
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state) return;

            var el = null;
            var currentCanvas = core.state.currentCanvas || 1;
            var elements = core.state.canvases ? (core.state.canvases[currentCanvas] || []) : [];

            for (var i = 0; i < elements.length; i++) {
                if (elements[i].id === id) {
                    el = elements[i];
                    break;
                }
            }
            if (!el) return;

            $('#wppoppop-inspector-drawer').addClass('open');
            $('body').addClass('wppoppop-inspector-open');

            // Synchronize dimensions
            $('#prop-x').val(el.x || 0);
            $('#prop-y').val(el.y || 0);
            $('#prop-w').val(el.w || 200);
            $('#prop-h').val(el.h || 40);

            // Synchronize typography
            if (el.fontFamily) $('#prop-font-family').val(el.fontFamily);
            if (el.fontSize) $('#prop-font-size').val(el.fontSize);
            if (el.fontWeight) $('#prop-font-weight').val(el.fontWeight);
            if (el.textAlign) $('#prop-text-align').val(el.textAlign);
            if (el.lineHeight) $('#prop-line-height').val(el.lineHeight);
            if (el.letterSpacing) $('#prop-letter-spacing').val(el.letterSpacing);
            if (el.color) {
                $('#prop-color').val(el.color);
                $('#prop-color-hex').val(el.color);
            }
        },

        close: function() {
            $('#wppoppop-inspector-drawer').removeClass('open');
            $('body').removeClass('wppoppop-inspector-open');
            this.activeId = null;
        },

        bindInputs: function() {
            var self = this;

            // Delegated typography updates
            $('#prop-font-family, #prop-font-size, #prop-font-weight, #prop-text-align, #prop-line-height, #prop-letter-spacing, #prop-color').on('input change', function() {
                if (!self.activeId) return;
                self.applyTypographyToActive();
            });

            $('#prop-color-hex').on('input change', function() {
                var hex = $(this).val();
                if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                    $('#prop-color').val(hex);
                    self.applyTypographyToActive();
                }
            });
        },

        applyTypographyToActive: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core || !this.activeId) return;

            var currentCanvas = core.state.currentCanvas || 1;
            var elements = core.state.canvases ? (core.state.canvases[currentCanvas] || []) : [];
            var el = null;

            for (var i = 0; i < elements.length; i++) {
                if (elements[i].id === this.activeId) {
                    el = elements[i];
                    break;
                }
            }
            if (!el) return;

            el.fontFamily    = $('#prop-font-family').val();
            el.fontSize      = parseInt($('#prop-font-size').val(), 10) || el.fontSize;
            el.fontWeight    = $('#prop-font-weight').val();
            el.textAlign     = $('#prop-text-align').val();
            el.lineHeight    = parseFloat($('#prop-line-height').val()) || el.lineHeight;
            el.letterSpacing = parseFloat($('#prop-letter-spacing').val()) || el.letterSpacing;
            el.color         = $('#prop-color').val();

            var $node = $('#el-' + el.id);
            if ($node.length) {
                $node.css({
                    'font-family': el.fontFamily,
                    'font-size': el.fontSize + 'px',
                    'font-weight': el.fontWeight,
                    'text-align': el.textAlign,
                    'line-height': el.lineHeight,
                    'letter-spacing': el.letterSpacing + 'px',
                    'color': el.color
                });
            }
        }
    };

    $(document).ready(function() {
        window.WpPopPopBuilderInspector.init();
    });

})(window, jQuery);
