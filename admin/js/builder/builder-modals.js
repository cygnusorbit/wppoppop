(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Modals = {
        init: function() {
            this.bindEmbed();
            this.bindPreview();
        },

        bindEmbed: function() {
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

            var initialScreen = Core.screens[0];
            var $box = $('<div></div>')
                .css({
                    position: 'relative',
                    width: (initialScreen.width || set.width) + 'px',
                    height: (initialScreen.height || set.height) + 'px',
                    borderRadius: '8px',
                    boxShadow: '0 25px 50px -12px rgba(0,0,0,0.5)',
                    overflow: 'hidden',
                    transition: 'width 0.25s ease, height 0.25s ease'
                });

            if (set.bgMode === 'gradient') {
                $box.css('background', 'linear-gradient(' + set.gradAngle + 'deg, ' + set.gradColor1 + ', ' + set.gradColor2 + ')');
            } else {
                $box.css('background', set.bgColor);
            }

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

            // Dynamically Render All Screens into Sandbox
            Core.screens.forEach(function(sc, idx) {
                var $screen = $('<div></div>')
                    .attr('data-preview-screen', sc.id)
                    .css({
                        position: 'absolute',
                        inset: 0,
                        display: (idx === 0) ? 'block' : 'none'
                    });

                var screenEls = Core.elements.filter(function(e) { return e.screen === sc.id && !e.hidden; });
                screenEls.forEach(function(el, elIdx) {
                    var $elNode = Canvas.buildElementNode(el, elIdx + 10);
                    $elNode.removeClass('active locked').css('cursor', 'default');

                    // Button Action Routing & Conditional Branching
                    if (el.type === 'step_btn' || el.type === 'submit' || el.type === 'pay') {
                        $elNode.find('button').on('click', function(evt) {
                            evt.preventDefault();
                            var targetScreenId = null;

                            // 1. Evaluate Conditional Logic
                            if (el.condVal && el.condTargetScreen) {
                                var enteredVal = $box.find('input, select, textarea').first().val();
                                if (enteredVal && enteredVal.toLowerCase().trim() === el.condVal.toLowerCase().trim()) {
                                    targetScreenId = el.condTargetScreen;
                                }
                            }

                            // 2. Default Navigation Flow
                            if (!targetScreenId) {
                                if (el.actionClose === 'jump_screen' && el.actionTargetScreen) {
                                    targetScreenId = el.actionTargetScreen;
                                } else if (el.actionClose === 'next_screen') {
                                    var currentIdx = Core.screens.findIndex(function(s) { return s.id === sc.id; });
                                    var nextSc = Core.screens[currentIdx + 1] || Core.screens[0];
                                    targetScreenId = nextSc.id;
                                } else if (el.actionClose === 'close') {
                                    $('#wppoppop-builder-preview-modal').removeClass('open');
                                    return;
                                }
                            }

                            if (targetScreenId) {
                                var targetScObj = Core.screens.find(function(s) { return s.id === targetScreenId; });
                                if (targetScObj) {
                                    $box.css({ width: targetScObj.width + 'px', height: targetScObj.height + 'px' });
                                }
                                $box.find('[data-preview-screen]').hide();
                                $box.find('[data-preview-screen="' + targetScreenId + '"]').fadeIn(200);
                            }
                        });
                    }

                    $screen.append($elNode);
                });

                $box.append($screen);
            });

            if (set.customCss) {
                $box.append('<style>' + set.customCss + '</style>');
            }

            $root.append($box);
        }
    };

    window.WpPopPopBuilder.Modals = Modals;
})(window, jQuery);
