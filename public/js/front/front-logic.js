(function(window, $) {
    'use strict';
    window.WpPopPopFront = window.WpPopPopFront || {};

    var Logic = {
        tokens: {},

        init: function($popup, config) {
            this.initUrlTokens();
            this.initUserTokens();
            this.bindInputs($popup, config);
            this.evaluateAll($popup, config);
        },

        initUrlTokens: function() {
            var params = new URLSearchParams(window.location.search);
            params.forEach(function(val, key) {
                Logic.tokens['query:' + key] = val;
            });
        },

        initUserTokens: function() {
            var u = (window.wppoppop_front_vars && window.wppoppop_front_vars.current_user) || {};
            if (u.logged_in) {
                Logic.tokens['user_email'] = u.email;
                Logic.tokens['user_name']  = u.name;
                Logic.tokens['user_login'] = u.login;
            }
        },

        setToken: function($popup, key, val) {
            this.tokens[key] = val;
            this.renderTokens($popup);
        },

        bindInputs: function($popup, config) {
            var self = this;

            // Form inputs binding
            $popup.find('input, select, textarea').on('input change', function() {
                var name = $(this).attr('name');
                if (name) {
                    self.tokens[name] = $(this).val();
                }
                self.evaluateAll($popup, config);
            });

            // Star Rating binding
            $popup.find('.wppoppop-field-rating').on('click', function(e) {
                var stars = 5;
                $(this).find('input[name="rating"]').val(stars);
                self.tokens['rating'] = stars;
                self.evaluateAll($popup, config);
            });
        },

        evaluateAll: function($popup, config) {
            this.renderTokens($popup);
            this.evaluateQuizScoring($popup, config);
        },

        renderTokens: function($popup) {
            var self = this;
            $popup.find('.wppoppop-text-render').each(function() {
                var raw = $(this).data('raw-template') || $(this).text();
                var text = raw;
                Object.keys(self.tokens).forEach(function(key) {
                    var regex = new RegExp('{' + key + '}', 'g');
                    text = text.replace(regex, self.tokens[key]);
                });
                $(this).text(text);
            });
        },

        evaluateQuizScoring: function($popup, config) {
            var quiz = config.quiz || {};
            if (!quiz.enable) return;

            var score = 0;
            // Accumulate radio points
            $popup.find('input[type="radio"]:checked').each(function() {
                var val = $(this).val();
                if (val && val.indexOf(':') !== -1) {
                    score += parseInt(val.split(':')[1], 10) || 0;
                }
            });

            this.setToken($popup, 'quiz_score', score);

            // Hook next screen routing to pass/fail threshold
            var passThreshold = parseInt(quiz.pass_score, 10) || 40;
            var passScreen = parseInt(quiz.pass_screen, 10) || 2;
            var failScreen = parseInt(quiz.fail_screen, 10) || 3;

            $popup.find('.wppoppop-next-screen-btn').attr('data-goto', (score >= passThreshold) ? passScreen : failScreen);
        }
    };

    window.WpPopPopFront.Logic = Logic;
})(window, jQuery);
