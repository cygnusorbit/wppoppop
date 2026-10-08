/**
 * WpPopPop Visual Builder: Campaign Settings Drawer Engine
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderSettings = {
        init: function() {
            this.bindDrawer();
            this.bindAccordions();
            this.bindCanvasMetaInputs();
            this.bindLiveStyle();
        },

        openDrawer: function() {
            $('#wppoppop-settings-drawer').addClass('open');
            $('#wppoppop-settings-backdrop').addClass('open');
            this.syncCurrentCanvasSettings();
        },

        closeDrawer: function() {
            $('#wppoppop-settings-drawer').removeClass('open');
            $('#wppoppop-settings-backdrop').removeClass('open');
        },

        bindDrawer: function() {
            var self = this;
            $('#wppoppop-btn-settings').on('click', function(e) {
                e.preventDefault();
                self.openDrawer();
            });

            $('#wppoppop-settings-drawer-close, #wppoppop-settings-backdrop').on('click', function(e) {
                e.preventDefault();
                self.closeDrawer();
            });
        },

        bindAccordions: function() {
            $(document).on('click', '.wppoppop-acc-header', function(e) {
                e.preventDefault();
                var $header = $(this);
                var $body = $header.next('.wppoppop-acc-body');

                $('.wppoppop-acc-header').not($header).removeClass('active');
                $('.wppoppop-acc-body').not($body).slideUp(150);

                $header.toggleClass('active');
                $body.slideToggle(150);
            });
        },

        syncCurrentCanvasSettings: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;
            var cur = core.state.currentCanvas || 1;
            var meta = core.state.canvasMeta[cur] || { name: 'Canvas ' + cur, width: 640, height: 400 };

            $('#set-current-canvas-badge').text(meta.name || ('Canvas ' + cur));
            $('#set-canvas-name').val(meta.name || ('Canvas ' + cur));
            $('#set-canvas-width').val(meta.width || 640);
            $('#set-canvas-height').val(meta.height || 400);
            $('#quick-box-width').val(meta.width || 640);
            $('#quick-box-height').val(meta.height || 400);
        },

        bindCanvasMetaInputs: function() {
            // Live synchronizing Canvas Name
            $('#set-canvas-name').on('input change', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                var val = $(this).val();

                if (!core.state.canvasMeta[cur]) {
                    core.state.canvasMeta[cur] = { width: 640, height: 400 };
                }
                core.state.canvasMeta[cur].name = val;
                $('#set-current-canvas-badge').text(val || ('Canvas ' + cur));

                // Update the tab button text immediately
                $('.wppoppop-canvas-tab[data-canvas="' + cur + '"], .wppoppop-screen-tab[data-screen="' + cur + '"]')
                    .text(val || ('Canvas ' + cur));
            });

            // Live synchronizing Canvas Dimensions
            $('#set-canvas-width, #set-canvas-height, #quick-box-width, #quick-box-height').on('input change', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;

                var isQuick = $(this).attr('id').indexOf('quick') !== -1;
                var w = parseInt((isQuick ? $('#quick-box-width') : $('#set-canvas-width')).val(), 10) || 640;
                var h = parseInt((isQuick ? $('#quick-box-height') : $('#set-canvas-height')).val(), 10) || 400;

                if (!core.state.canvasMeta[cur]) {
                    core.state.canvasMeta[cur] = { name: 'Canvas ' + cur };
                }
                core.state.canvasMeta[cur].width = w;
                core.state.canvasMeta[cur].height = h;

                $('#set-canvas-width, #quick-box-width').val(w);
                $('#set-canvas-height, #quick-box-height').val(h);

                if (core.state.viewport === 'desktop') {
                    $('#wppoppop-canvas-box').css({ width: w + 'px', height: h + 'px' });
                }
            });
        },

        bindLiveStyle: function() {
            $('#set-bg-mode').on('change', function() {
                var mode = $(this).val();
                $('#set-solid-wrap').toggle(mode === 'solid');
                $('#set-gradient-wrap').toggle(mode === 'gradient');
                applyBackground();
            });

            $('#set-bg-color, #set-grad-color1, #set-grad-color2, #set-grad-angle').on('input change', function() {
                applyBackground();
            });

            function applyBackground() {
                var mode = $('#set-bg-mode').val();
                var $box = $('#wppoppop-canvas-box');
                if (mode === 'gradient') {
                    var c1 = $('#set-grad-color1').val() || '#3b82f6';
                    var c2 = $('#set-grad-color2').val() || '#1d4ed8';
                    var ang = $('#set-grad-angle').val() || 135;
                    $box.css({ background: 'linear-gradient(' + ang + 'deg, ' + c1 + ', ' + c2 + ')' });
                } else {
                    var bg = $('#set-bg-color').val() || '#ffffff';
                    $box.css({ background: bg });
                }
            }

            // Sync Quiz scoring inputs
            $('#set-quiz-pass-canvas, #set-quiz-pass-screen').on('input change', function() {
                var v = $(this).val();
                $('#set-quiz-pass-canvas, #set-quiz-pass-screen').val(v);
            });

            $('#set-quiz-fail-canvas, #set-quiz-fail-screen').on('input change', function() {
                var v = $(this).val();
                $('#set-quiz-fail-canvas, #set-quiz-fail-screen').val(v);
            });
        },

        getSettings: function() {
            return {
                box: {
                    width: parseInt($('#set-canvas-width').val(), 10) || 640,
                    height: parseInt($('#set-canvas-height').val(), 10) || 400,
                    bg_mode: $('#set-bg-mode').val() || 'solid',
                    bg_color: $('#set-bg-color').val() || '#ffffff',
                    grad_color1: $('#set-grad-color1').val() || '#3b82f6',
                    grad_color2: $('#set-grad-color2').val() || '#1d4ed8',
                    grad_angle: parseInt($('#set-grad-angle').val(), 10) || 135
                },
                triggers: {
                    load: $('#trig-load').is(':checked'),
                    load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                    exit: $('#trig-exit').is(':checked'),
                    scroll: $('#trig-scroll').is(':checked'),
                    scroll_val: parseInt($('#trig-scroll-val').val(), 10) || 50,
                    adblock: $('#trig-adblock').is(':checked'),
                    backbutton: $('#trig-backbutton').is(':checked')
                },
                logic_math: {
                    formula: $('#set-math-formula').val() || '',
                    target: $('#set-math-target').val() || ''
                },
                sidetab: {
                    enable: $('#set-sidetab-enable').is(':checked'),
                    label: $('#set-sidetab-label').val() || '',
                    pos: $('#set-sidetab-pos').val() || 'left'
                },
                payments: {
                    gateway: $('#set-pay-gateway').val() || 'stripe',
                    amount: parseFloat($('#set-pay-amount').val()) || 0
                },
                downloads: {
                    enable: $('#set-dl-enable').is(':checked'),
                    url: $('#set-dl-url').val() || ''
                },
                video: {
                    enable: $('#set-vid-enable').is(':checked'),
                    time: parseInt($('#set-vid-time').val(), 10) || 0
                },
                autoresponder: {
                    enable: $('#set-auto-enable').is(':checked'),
                    subject: $('#set-auto-subject').val() || '',
                    body: $('#set-auto-body').val() || ''
                },
                webhooks: {
                    url: $('#set-webhook-url').val() || '',
                    secret: $('#set-webhook-secret').val() || ''
                },
                sms: {
                    enable: $('#set-sms-enable').is(':checked'),
                    phone: $('#set-sms-phone').val() || ''
                },
                targeting: {
                    auth: $('#set-target-auth').val() || 'all'
                },
                frequency: {
                    mode: $('#set-freq-mode').val() || 'always'
                },
                woocommerce: {
                    coupon: $('#set-wc-coupon').is(':checked'),
                    amount: $('#set-wc-amount').val() || ''
                },
                custom_code: {
                    css: $('#set-custom-css').val() || '',
                    js: $('#set-custom-js').val() || ''
                },
                quiz: {
                    enable: $('#set-quiz-enable').is(':checked'),
                    pass_threshold: parseInt($('#set-quiz-pass').val(), 10) || 70,
                    pass_canvas: parseInt($('#set-quiz-pass-canvas').val(), 10) || 2,
                    pass_screen: parseInt($('#set-quiz-pass-canvas').val(), 10) || 2,
                    fail_canvas: parseInt($('#set-quiz-fail-canvas').val(), 10) || 3,
                    fail_screen: parseInt($('#set-quiz-fail-canvas').val(), 10) || 3,
                    confetti: $('#set-quiz-confetti').is(':checked')
                }
            };
        },

        setSettings: function(settings) {
            if (!settings) return;
            if (settings.box) {
                $('#set-bg-mode').val(settings.box.bg_mode || 'solid').trigger('change');
                $('#set-bg-color').val(settings.box.bg_color || '#ffffff');
                $('#set-grad-color1').val(settings.box.grad_color1 || '#3b82f6');
                $('#set-grad-color2').val(settings.box.grad_color2 || '#1d4ed8');
                $('#set-grad-angle').val(settings.box.grad_angle || 135);
            }
            if (settings.triggers) {
                $('#trig-load').prop('checked', !!settings.triggers.load);
                $('#trig-load-delay').val(settings.triggers.load_delay || '');
                $('#trig-exit').prop('checked', !!settings.triggers.exit);
                $('#trig-scroll').prop('checked', !!settings.triggers.scroll);
                $('#trig-scroll-val').val(settings.triggers.scroll_val || 50);
                $('#trig-adblock').prop('checked', !!settings.triggers.adblock);
                $('#trig-backbutton').prop('checked', !!settings.triggers.backbutton);
            }
            if (settings.quiz) {
                $('#set-quiz-enable').prop('checked', !!settings.quiz.enable);
                $('#set-quiz-pass').val(settings.quiz.pass_threshold || 70);
                var p = settings.quiz.pass_canvas || settings.quiz.pass_screen || 2;
                var f = settings.quiz.fail_canvas || settings.quiz.fail_screen || 3;
                $('#set-quiz-pass-canvas, #set-quiz-pass-screen').val(p);
                $('#set-quiz-fail-canvas, #set-quiz-fail-screen').val(f);
                $('#set-quiz-confetti').prop('checked', !!settings.quiz.confetti);
            }
        }
    };
})(jQuery);
