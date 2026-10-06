(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Modals = {
        init: function() {
            this.bindEmbed();
            this.bindPreview();
        },

        bindEmbed: function() {
            var self = this;
            $('#wppoppop-btn-embed').on('click', function() {
                var uid = (window.wppoppop_vars && window.wppoppop_vars.current_uid) || 'temp_preview';
                $('#wppoppop-embed-sc').val('[wppoppop uid="' + uid + '"]');
                $('#wppoppop-embed-locker').val('[wppoppop_locker uid="' + uid + '"]Your locked content here[/wppoppop_locker]');
                $('#wppoppop-embed-click').val('<button class="wppoppop-open-btn" data-target-uid="' + uid + '">Open Popup</button>');
                $('#wppoppop-builder-embed-modal').addClass('open');
            });

            $('#wppoppop-builder-embed-close').on('click', function() {
                $('#wppoppop-builder-embed-modal').removeClass('open');
            });

            // 1-Click Clipboard Copy
            $('#wppoppop-embed-sc, #wppoppop-embed-locker, #wppoppop-embed-click').on('click', function() {
                var $input = $(this);
                $input.select();
                try {
                    navigator.clipboard.writeText($input.val());
                    var originalBg = $input.css('background');
                    $input.css('background', '#dcfce7');
                    setTimeout(function() { $input.css('background', originalBg); }, 600);
                } catch(e) {
                    document.execCommand('copy');
                }
            });
        },

        bindPreview: function() {
            var self = this;
            $('#wppoppop-btn-preview').on('click', function() {
                self.compileLiveSandbox();
                $('#wppoppop-builder-preview-modal').addClass('open');
            });

            $('#wppoppop-builder-preview-close').on('click', function() {
                $('#wppoppop-builder-preview-modal').removeClass('open');
            });
        },

        compileLiveSandbox: function() {
            var Core = window.WpPopPopBuilder.Core;
            var Settings = window.WpPopPopBuilder.Settings;
            var Canvas = window.WpPopPopBuilder.Canvas;
            var set = Settings.getSettings();

            var $root = $('#wppoppop-preview-sandbox-root');
            $root.empty();

            // Background Wrapper
            var $box = $('<div></div>')
                .css({
                    position: 'relative',
                    width: set.width + 'px',
                    height: set.height + 'px',
                    borderRadius: '8px',
                    boxShadow: '0 25px 50px -12px rgba(0,0,0,0.5)',
                    overflow: 'hidden'
                });

            if (set.bgMode === 'gradient') {
                $box.css('background', 'linear-gradient(' + set.gradAngle + 'deg, ' + set.gradColor1 + ', ' + set.gradColor2 + ')');
            } else {
                $box.css('background', set.bgColor);
            }

            // Close Button
            var $closeBtn = $('<button type="button">&times;</button>')
                .css({
                    position: 'absolute',
                    top: '10px',
                    right: '10px',
                    background: 'rgba(0,0,0,0.2)',
                    border: 'none',
                    color: '#ffffff',
                    fontSize: '20px',
                    width: '28px',
                    height: '28px',
                    borderRadius: '50%',
                    cursor: 'pointer',
                    zIndex: 9999,
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center'
                })
                .on('click', function() {
                    $('#wppoppop-builder-preview-modal').removeClass('open');
                });
            $box.append($closeBtn);

            // Screen Viewports
            [1, 2, 3].forEach(function(sNum) {
                var $screen = $('<div></div>')
                    .attr('data-preview-screen', sNum)
                    .css({
                        position: 'absolute',
                        inset: 0,
                        display: (sNum === 1) ? 'block' : 'none'
                    });

                var screenEls = Core.elements.filter(function(e) { return e.screen === sNum && !e.hidden; });
                screenEls.forEach(function(el, idx) {
                    var $elNode = Canvas.buildElementNode(el, idx + 10);
                    $elNode.removeClass('active locked').css('cursor', 'default');
                    
                    // Wire Screen Jump for Next Step Buttons
                    if (el.type === 'step_btn') {
                        $elNode.find('button').on('click', function(evt) {
                            evt.preventDefault();
                            var target = (sNum < 3) ? (sNum + 1) : 1;
                            $box.find('[data-preview-screen]').hide();
                            $box.find('[data-preview-screen="' + target + '"]').fadeIn(200);
                        });
                    }
                    $screen.append($elNode);
                });

                $box.append($screen);
            });

            // Append Scoped Custom CSS if configured
            if (set.customCss) {
                $box.append('<style>' + set.customCss + '</style>');
            }

            $root.append($box);
        }
    };

    window.WpPopPopBuilder.Modals = Modals;
})(window, jQuery);
