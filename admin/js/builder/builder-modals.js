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
                .attr('id', 'wppoppop-sandbox-box')
                .css({
                    position: 'relative',
                    width: (initialScreen.width || set.width || 640) + 'px',
                    height: (initialScreen.height || set.height || 400) + 'px',
                    borderRadius: '8px',
                    boxShadow: '0 25px 50px -12px rgba(0,0,0,0.5)',
                    overflow: 'hidden',
                    transition: 'width 0.25s ease, height 0.25s ease, background 0.25s ease'
                });

            if (initialScreen.bgMode === 'gradient') {
                $box.css('background', 'linear-gradient(' + (initialScreen.gradAngle || 135) + 'deg, ' + (initialScreen.gradColor1 || '#3b82f6') + ', ' + (initialScreen.gradColor2 || '#1d4ed8') + ')');
            } else {
                $box.css('background', initialScreen.bgColor || '#ffffff');
            }

            // Apply Initial Screen Entrance Animation
            var initAnim = initialScreen.animIn || 'fade';
            var initDur = (initialScreen.animInDuration !== undefined ? initialScreen.animInDuration : 1000) / 1000;
            var initDelay = (initialScreen.animInDelay !== undefined ? initialScreen.animInDelay : 0) / 1000;

            var animClassMap = {
                fade: 'wppoppopFadeIn',
                slideDown: 'wppoppopSlideDown',
                bounceIn: 'wppoppopBounceIn',
                zoomIn: 'wppoppopZoomIn',
                flipIn: 'wppoppopFlipIn'
            };

            if (initAnim !== 'none' && animClassMap[initAnim]) {
                $box.css('animation', animClassMap[initAnim] + ' ' + initDur + 's ease-out ' + initDelay + 's forwards');
            }

            var $closeBtn = $('<button type="button">&times;</button>')
                .css({
                    position: 'absolute',
                    top: '10px',
                    right: '10px',
                    background: 'rgba(0,0,0,0.25)',
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
                    justifyContent: 'center',
                    lineHeight: '1'
                })
                .on('click', function() {
                    $('#wppoppop-builder-preview-modal').removeClass('open');
                });
            $box.append($closeBtn);

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

                    if (el.type === 'email' || el.type === 'number' || el.type === 'text') {
                        $elNode.html('<input type="' + (el.type === 'email' ? 'email' : (el.type === 'number' ? 'number' : 'text')) + '" class="wppoppop-preview-input" data-el-id="' + el.id + '" placeholder="' + (el.content || el.label || '') + '" style="width:100%;height:100%;padding:0 10px;box-sizing:border-box;border:1px solid #cbd5e1;border-radius:inherit;font-size:inherit;background:#ffffff;color:#1e293b;">');
                    } else if (el.type === 'select') {
                        $elNode.html('<select class="wppoppop-preview-input" data-el-id="' + el.id + '" style="width:100%;height:100%;padding:0 8px;box-sizing:border-box;border:1px solid #cbd5e1;border-radius:inherit;font-size:inherit;background:#ffffff;color:#1e293b;"><option value="">Select an option...</option><option value="VIP">VIP</option><option value="Standard">Standard</option><option value="Yes">Yes</option><option value="No">No</option></select>');
                    } else if (el.type === 'radios') {
                        $elNode.html('<div style="display:flex;align-items:center;gap:12px;width:100%;height:100%;padding:0 8px;box-sizing:border-box;"><label style="display:flex;align-items:center;gap:4px;font-size:inherit;color:inherit;cursor:pointer;"><input type="radio" name="rad_' + el.id + '" value="Choice A" class="wppoppop-preview-input" data-el-id="' + el.id + '" checked> Choice A</label><label style="display:flex;align-items:center;gap:4px;font-size:inherit;color:inherit;cursor:pointer;"><input type="radio" name="rad_' + el.id + '" value="Choice B" class="wppoppop-preview-input" data-el-id="' + el.id + '"> Choice B</label></div>');
                    } else if (el.type === 'checkboxes') {
                        $elNode.html('<label style="display:flex;align-items:center;gap:8px;width:100%;height:100%;padding:0 8px;box-sizing:border-box;cursor:pointer;"><input type="checkbox" class="wppoppop-preview-input" data-el-id="' + el.id + '" value="1" checked><span style="font-size:inherit;color:inherit;">' + (el.content || el.label || 'Accept terms') + '</span></label>');
                    }

                    if (el.type === 'step_btn' || el.type === 'submit' || el.type === 'pay') {
                        $elNode.find('button').on('click', function(evt) {
                            evt.preventDefault();
                            var targetScreenId = null;

                            var scObj = Core.screens.find(function(s) { return s.id === sc.id; });
                            if (scObj && scObj.logic && scObj.logic.enable && scObj.logic.field) {
                                var $fieldInput = $box.find('.wppoppop-preview-input[data-el-id="' + scObj.logic.field + '"]');
                                var fieldValue = '';

                                if ($fieldInput.is(':checkbox')) {
                                    fieldValue = $fieldInput.is(':checked') ? ($fieldInput.val() || '1') : '';
                                } else if ($fieldInput.is(':radio')) {
                                    fieldValue = $box.find('.wppoppop-preview-input[data-el-id="' + scObj.logic.field + '"]:checked').val() || '';
                                } else {
                                    fieldValue = ($fieldInput.val() || '').trim();
                                }

                                var condOp = scObj.logic.operator || 'equals';
                                var matchVal = (scObj.logic.val || '').trim();
                                var isMatch = false;

                                if (condOp === 'equals') isMatch = (fieldValue.toLowerCase() === matchVal.toLowerCase());
                                else if (condOp === 'not_equals') isMatch = (fieldValue.toLowerCase() !== matchVal.toLowerCase());
                                else if (condOp === 'contains') isMatch = (fieldValue.toLowerCase().indexOf(matchVal.toLowerCase()) !== -1);
                                else if (condOp === 'greater_than') isMatch = (parseFloat(fieldValue) > parseFloat(matchVal));
                                else if (condOp === 'less_than') isMatch = (parseFloat(fieldValue) < parseFloat(matchVal));
                                else if (condOp === 'is_empty') isMatch = (!fieldValue || fieldValue === '');
                                else if (condOp === 'is_not_empty') isMatch = (fieldValue && fieldValue !== '');

                                if (isMatch) {
                                    targetScreenId = parseInt(scObj.logic.targetScreen, 10);
                                } else if (scObj.logic.fallback) {
                                    if (scObj.logic.fallback === 'close') {
                                        $('#wppoppop-builder-preview-modal').removeClass('open');
                                        return;
                                    } else if (scObj.logic.fallback === 'next_screen') {
                                        var curIdx = Core.screens.findIndex(function(s) { return s.id === sc.id; });
                                        var nextSc = Core.screens[curIdx + 1] || Core.screens[0];
                                        targetScreenId = nextSc.id;
                                    } else {
                                        targetScreenId = parseInt(scObj.logic.fallback, 10);
                                    }
                                }
                            }

                            if (!targetScreenId) {
                                if (el.actionClose === 'jump_screen' && el.actionTargetScreen) {
                                    targetScreenId = parseInt(el.actionTargetScreen, 10);
                                } else if (el.actionClose === 'next_screen') {
                                    var currentIdx = Core.screens.findIndex(function(s) { return s.id === sc.id; });
                                    var nextSc = Core.screens[currentIdx + 1] || Core.screens[0];
                                    targetScreenId = nextSc.id;
                                } else if (el.actionClose === 'close') {
                                    $('#wppoppop-builder-preview-modal').removeClass('open');
                                    return;
                                } else if (el.actionClose === 'redirect' && el.actionUrl) {
                                    if (el.actionBlank) {
                                        window.open(el.actionUrl, '_blank');
                                    } else {
                                        window.location.href = el.actionUrl;
                                    }
                                    return;
                                }
                            }

                            if (targetScreenId) {
                                var targetScObj = Core.screens.find(function(s) { return s.id === targetScreenId; });
                                if (targetScObj) {
                                    $box.css({ width: targetScObj.width + 'px', height: targetScObj.height + 'px' });
                                    if (targetScObj.bgMode === 'gradient') {
                                        $box.css('background', 'linear-gradient(' + (targetScObj.gradAngle || 135) + 'deg, ' + (targetScObj.gradColor1 || '#3b82f6') + ', ' + (targetScObj.gradColor2 || '#1d4ed8') + ')');
                                    } else {
                                        $box.css('background', targetScObj.bgColor || '#ffffff');
                                    }

                                    // Apply Destination Screen Entrance Animation
                                    var nextAnim = targetScObj.animIn || 'fade';
                                    var nextDur = (targetScObj.animInDuration !== undefined ? targetScObj.animInDuration : 1000) / 1000;
                                    var nextDelay = (targetScObj.animInDelay !== undefined ? targetScObj.animInDelay : 0) / 1000;

                                    if (nextAnim !== 'none' && animClassMap[nextAnim]) {
                                        $box.css('animation', 'none');
                                        setTimeout(function() {
                                            $box.css('animation', animClassMap[nextAnim] + ' ' + nextDur + 's ease-out ' + nextDelay + 's forwards');
                                        }, 10);
                                    }
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
