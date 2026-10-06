(function(window, $) {
    'use strict';
    window.WpPopPop = window.WpPopPop || {};

    var State = {
        uid: (window.wppoppop_vars && window.wppoppop_vars.current_uid) || '',
        activeScreen: 1,
        activeViewport: 'desktop',
        selectedId: null,
        elements: [],
        config: {
            meta: { width: 640, height: 400, bg_mode: 'solid', bg_color: '#ffffff', grad_1: '#1e293b', grad_2: '#0f172a', grad_angle: 135, blur: 0 },
            triggers: { on_load: true, on_load_delay: 0, on_exit: false, on_scroll: false, scroll_val: 50, on_idle: false, idle_val: 30, on_adblock: false, on_backbutton: false },
            targeting: { devices: 'all', auth_mode: 'all', geo_mode: 'all', geo_countries: '' },
            frequency: { mode: 'always', days: 7, hide_on_submit: true },
            autoresponder: { enable_user_email: false, subject: '', message: '' },
            marketing: { webhook_url: '', webhook_secret: '', mc_enable: false, mc_key: '', mc_list: '' },
            twilio: { enable: false, sid: '', token: '', from: '', to: '' },
            tabs: { enable: false, text: 'Special Offer', position: 'left' },
            ribbon: { enable: false, position: 'top' },
            woocommerce: { auto_coupon: false, amount: 10, prefix: 'POP', auto_apply: false },
            payments: { currency: 'USD', gateway: 'Stripe' },
            downloads: { enable: false, file_url: '' },
            quiz: { enable: false, pass_score: 40, pass_screen: 2, fail_screen: 3, confetti: true },
            sound: { enable: false },
            custom_css: '',
            custom_js: ''
        }
    };

    var History = {
        undoStack: [],
        redoStack: [],
        pushState: function() {
            var snapshot = JSON.stringify({ elements: State.elements, config: State.config });
            if (this.undoStack.length > 25) this.undoStack.shift();
            this.undoStack.push(snapshot);
            this.redoStack = [];
        },
        undo: function() {
            if (this.undoStack.length === 0) return;
            var current = JSON.stringify({ elements: State.elements, config: State.config });
            this.redoStack.push(current);
            var prev = JSON.parse(this.undoStack.pop());
            State.elements = prev.elements;
            State.config = prev.config;
            window.WpPopPop.Canvas.refresh();
            window.WpPopPop.Layers.renderList();
        },
        redo: function() {
            if (this.redoStack.length === 0) return;
            var current = JSON.stringify({ elements: State.elements, config: State.config });
            this.undoStack.push(current);
            var next = JSON.parse(this.redoStack.pop());
            State.elements = next.elements;
            State.config = next.config;
            window.WpPopPop.Canvas.refresh();
            window.WpPopPop.Layers.renderList();
        }
    };

    function initCoreEvents() {
        // Multi-Screen Tabs
        $(document).on('click', '.wppoppop-screen-tab', function() {
            $('.wppoppop-screen-tab').removeClass('active');
            $(this).addClass('active');
            State.activeScreen = parseInt($(this).data('screen'), 10) || 1;
            window.WpPopPop.Canvas.refresh();
            window.WpPopPop.Layers.renderList();
            if (window.WpPopPop.Inspector) window.WpPopPop.Inspector.close();
        });

        // Viewport Switcher
        $('#wppoppop-viewport-desktop').on('click', function() {
            $('.wppoppop-viewport-switch button').removeClass('active');
            $(this).addClass('active');
            State.activeViewport = 'desktop';
            $('#wppoppop-canvas').css('width', State.config.meta.width + 'px');
        });

        $('#wppoppop-viewport-mobile').on('click', function() {
            $('.wppoppop-viewport-switch button').removeClass('active');
            $(this).addClass('active');
            State.activeViewport = 'mobile';
            $('#wppoppop-canvas').css('width', '360px');
        });

        // Undo / Redo
        $('#wppoppop-btn-undo').on('click', function() { History.undo(); });
        $('#wppoppop-btn-redo').on('click', function() { History.redo(); });

        $(document).on('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'z') {
                if (e.shiftKey) History.redo();
                else History.undo();
                e.preventDefault();
            } else if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'y') {
                History.redo();
                e.preventDefault();
            }
        });
    }

    window.WpPopPop.State = State;
    window.WpPopPop.History = History;
    window.WpPopPop.initCore = initCoreEvents;
})(window, jQuery);
