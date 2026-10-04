(function() {
    'use strict';

    // 1. Safe Mathematical Expression Parser (No eval)
    function evaluateExpression(expr, scope) {
        let cleanExpr = expr.replace(/\{([a-zA-Z0-9_\-]+)\}/g, function(_, varName) {
            return scope[varName] !== undefined ? scope[varName] : 0;
        });

        // Tokenize and calculate basic arithmetic operations
        try {
            const sanitized = cleanExpr.replace(/[^0-9+\-*/().]/g, '');
            const compute = new Function('return ' + sanitized);
            const res = compute();
            return isFinite(res) ? res : 0;
        } catch (e) {
            return 0;
        }
    }

    function initCalculators(container) {
        const forms = (container || document).querySelectorAll('.wppoppop-form');
        forms.forEach(function(form) {
            const calcBadges = form.querySelectorAll('[data-calc-formula]');
            if (!calcBadges.length) return;

            function updateCalculations() {
                const scope = {};
                form.querySelectorAll('input, select').forEach(function(input) {
                    if (input.name) {
                        scope[input.name] = parseFloat(input.value) || 0;
                    }
                });

                calcBadges.forEach(function(badge) {
                    const formula = badge.getAttribute('data-calc-formula') || '0';
                    const precision = parseInt(badge.getAttribute('data-calc-precision') || '2', 10);
                    const prefix = badge.getAttribute('data-calc-prefix') || '$';
                    const calculated = evaluateExpression(formula, scope);
                    const output = prefix + calculated.toFixed(precision);

                    const valElem = badge.querySelector('.wppoppop-calc-val') || badge;
                    valElem.textContent = output;

                    // If dynamic total binds to form amount for checkout
                    if (badge.hasAttribute('data-bind-amount')) {
                        const amountField = form.querySelector('input[name="amount"]');
                        if (amountField) amountField.value = calculated.toFixed(precision);
                    }
                });
            }

            form.addEventListener('input', updateCalculations);
            updateCalculations();
        });
    }

    // 2. HTML5 Canvas Signature Pad
    function initSignaturePads(container) {
        const pads = (container || document).querySelectorAll('.wppoppop-signature-wrap');
        pads.forEach(function(wrap) {
            const canvas = wrap.querySelector('.wppoppop-signature-canvas');
            const hidden = wrap.querySelector('input[type="hidden"]');
            const clearBtn = wrap.querySelector('.wppoppop-sig-clear');
            if (!canvas || canvas.dataset.sigInit === '1') return;
            canvas.dataset.sigInit = '1';

            const ctx = canvas.getContext('2d');
            canvas.width = canvas.offsetWidth || 300;
            canvas.height = canvas.offsetHeight || 120;

            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#1e293b';

            let drawing = false;
            let hasDrawn = false;

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            function start(e) {
                drawing = true;
                hasDrawn = true;
                const p = getPos(e);
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
                e.preventDefault();
            }

            function draw(e) {
                if (!drawing) return;
                const p = getPos(e);
                ctx.lineTo(p.x, p.y);
                ctx.stroke();
                e.preventDefault();
            }

            function stop() {
                if (!drawing) return;
                drawing = false;
                if (hidden && hasDrawn) {
                    hidden.value = canvas.toDataURL('image/png');
                }
            }

            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', draw);
            window.addEventListener('mouseup', stop);

            canvas.addEventListener('touchstart', start, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            window.addEventListener('touchend', stop);

            if (clearBtn) {
                clearBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    if (hidden) hidden.value = '';
                    hasDrawn = false;
                });
            }
        });
    }

    // 3. Input Masking (Phone, Dates, Cards)
    function initInputMasks(container) {
        const masked = (container || document).querySelectorAll('input[data-mask]');
        masked.forEach(function(input) {
            const pattern = input.getAttribute('data-mask'); // e.g. "(999) 999-9999"

            input.addEventListener('input', function() {
                const raw = input.value.replace(/\D/g, '');
                let formatted = '';
                let rawIdx = 0;

                for (let i = 0; i < pattern.length && rawIdx < raw.length; i++) {
                    if (pattern[i] === '9') {
                        formatted += raw[rawIdx++];
                    } else {
                        formatted += pattern[i];
                    }
                }
                input.value = formatted;
            });
        });
    }

    // 4. Numeric Range Sliders
    function initRangeSliders(container) {
        const sliders = (container || document).querySelectorAll('.wppoppop-range-input');
        sliders.forEach(function(range) {
            const out = range.parentElement.querySelector('.wppoppop-slider-val');
            function update() {
                if (out) out.textContent = range.value;
            }
            range.addEventListener('input', update);
            update();
        });
    }

    function initAllInteractiveFields(root) {
        initCalculators(root);
        initSignaturePads(root);
        initInputMasks(root);
        initRangeSliders(root);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initAllInteractiveFields(document);
    });

    window.WPPopPopInteractive = {
        init: initAllInteractiveFields
    };
})();
