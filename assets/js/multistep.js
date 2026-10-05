(function() {
    'use strict';

    function initMultiStepFunnels() {
        const funnels = document.querySelectorAll('.wppoppop-multistep-container');
        funnels.forEach(function(funnel) {
            if (funnel.dataset.multistepInit === '1') return;
            funnel.dataset.multistepInit = '1';

            const panes = funnel.querySelectorAll('.wppoppop-step-pane');
            const fillBar = funnel.querySelector('.wppoppop-progress-fill');
            const stepText = funnel.querySelector('.wppoppop-step-text');
            const stepPercent = funnel.querySelector('.wppoppop-step-percent');
            const hiddenChoice = funnel.querySelector('.wppoppop-hidden-choice');
            const form = funnel.querySelector('.wppoppop-form');

            const totalSteps = panes.length;
            let currentStep = 1;

            function goToStep(step) {
                if (step < 1 || step > totalSteps) return;
                currentStep = step;

                panes.forEach(function(p) {
                    const s = parseInt(p.getAttribute('data-step'), 10);
                    if (s === currentStep) {
                        p.classList.add('wppoppop-step-active');
                    } else {
                        p.classList.remove('wppoppop-step-active');
                    }
                });

                const pct = Math.round((currentStep / totalSteps) * 100);
                if (fillBar) fillBar.style.width = pct + '%';
                if (stepText) stepText.textContent = 'Step ' + currentStep + ' of ' + totalSteps;
                if (stepPercent) stepPercent.textContent = pct + '%';
            }

            // Choice card selection in Step 1
            funnel.querySelectorAll('.wppoppop-choice-card').forEach(function(card) {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const choice = card.getAttribute('data-choice');
                    const nextStep = parseInt(card.getAttribute('data-next-step') || '2', 10);

                    if (hiddenChoice) hiddenChoice.value = choice;
                    goToStep(nextStep);
                });
            });

            // Back button navigation
            funnel.querySelectorAll('.wppoppop-prev-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = parseInt(btn.getAttribute('data-step-target') || '1', 10);
                    goToStep(target);
                });
            });

            // Intercept form submission to advance to Step 3 (Reward)
            if (form) {
                form.addEventListener('submit', function() {
                    const onResponseCheck = setInterval(function() {
                        const feedback = form.querySelector('.wppoppop-feedback');
                        if (feedback && feedback.classList.contains('wppoppop-success')) {
                            clearInterval(onResponseCheck);
                            goToStep(3);
                            if (window.WPPopPopCelebration) {
                                window.WPPopPopCelebration.celebrateConversion();
                            }
                        }
                    }, 200);
                    setTimeout(function() { clearInterval(onResponseCheck); }, 6000);
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initMultiStepFunnels);
    window.WPPopPopMultiStep = { init: initMultiStepFunnels };
})();
