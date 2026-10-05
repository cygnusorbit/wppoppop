
(function($) {
    'use strict';

    $(document).ready(function() {
        // Sticky Side Tabs
        $(document).on('click', '.wppoppop-sidetab, .wppoppop-open-btn', function() {
            const uid = $(this).data('target-uid');
            const target = $('#wppoppop-popup-' + uid);
            if (target.length) target.addClass('wppoppop-visible').fadeIn(200);
        });

        $('.wppoppop-overlay, .wppoppop-inline-container').each(function() {
            const popup = $(this);
            const uid = popup.data('uid');
            const config = popup.data('config') || {};
            const styling = config.styling || {};
            let displayed = false;

            function showPopup() {
                if (displayed) return;
                displayed = true;
                popup.addClass('wppoppop-visible').fadeIn(200);

                // Use REST API for Impression
                const restUrl = wppoppop_front_vars.rest_url + 'impression';
                if (window.fetch) {
                    fetch(restUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ uid: uid })
                    });
                }
            }

            function closePopup() {
                popup.removeClass('wppoppop-visible').fadeOut(200);
            }

            popup.find('.wppoppop-close-btn').on('click', closePopup);

            // Close on Backdrop Click
            if (styling.close_backdrop !== false) {
                popup.on('click', function(e) {
                    if ($(e.target).hasClass('wppoppop-overlay')) closePopup();
                });
            }

            // Close on ESC Key
            if (styling.close_esc !== false) {
                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape' && popup.hasClass('wppoppop-visible')) closePopup();
                });
            }

            // Render & Handle Fortune Wheel
            popup.find('.wppoppop-wheel-canvas').each(function() {
                const canvas = this;
                const ctx = canvas.getContext('2d');
                const slices = $(canvas).data('slices') || ['10% OFF', 'FREE SHIP', '20% OFF', 'TRY AGAIN'];
                const numSlices = slices.length;
                const arc = (2 * Math.PI) / numSlices;
                const colors = ['#f44336', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5', '#2196f3', '#009688', '#4caf50'];

                // Draw Wheel
                for (let i = 0; i < numSlices; i++) {
                    const angle = i * arc;
                    ctx.fillStyle = colors[i % colors.length];
                    ctx.beginPath();
                    ctx.arc(110, 110, 105, angle, angle + arc, false);
                    ctx.lineTo(110, 110);
                    ctx.fill();

                    // Text label
                    ctx.save();
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 12px sans-serif';
                    ctx.translate(110, 110);
                    ctx.rotate(angle + arc / 2);
                    ctx.textAlign = 'right';
                    ctx.fillText(slices[i], 95, 4);
                    ctx.restore();
                }

                // Spin Interaction
                const spinBtn = $(this).siblings('.wppoppop-wheel-spin-btn');
                spinBtn.on('click', function() {
                    spinBtn.prop('disabled', true);
                    const selectedIdx = Math.floor(Math.random() * numSlices);
                    const wonPrize = slices[selectedIdx];
                    const degrees = 1800 + (360 - (selectedIdx * (360 / numSlices)) - (180 / numSlices));

                    $(canvas).css('transform', 'rotate(' + degrees + 'deg)');

                    setTimeout(function() {
                        popup.find('input[name="prize"]').val(wonPrize);
                        // Update interactive token text
                        popup.find('.wppoppop-text-render').each(function() {
                            const raw = $(this).data('raw-template');
                            if (raw && raw.indexOf('{prize}') !== -1) {
                                $(this).text(raw.replace(/{prize}/g, wonPrize));
                            }
                        });
                        alert('Congratulations! You won: ' + wonPrize);
                    }, 4200);
                });
            });

            // Form Submission with REST Engine
            popup.find('.wppoppop-submit-trigger').on('click', function() {
                const emailInput = popup.find('.wppoppop-field-email');
                const email = emailInput.val();
                if (!email) {
                    alert('Please enter your email address.');
                    return;
                }

                const fields = {};
                popup.find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name && name !== 'email') fields[name] = $(this).val();
                });

                $.post(wppoppop_front_vars.ajax_url, {
                    action: 'wppoppop_submit_form',
                    uid: uid,
                    email: email,
                    fields: fields
                }, function(res) {
                    if (res.success) {
                        const statusOverlay = popup.find('.wppoppop-status-overlay');
                        popup.find('.wppoppop-status-message').text(res.data.message);
                        statusOverlay.fadeIn();
                        setTimeout(function() {
                            closePopup();
                            statusOverlay.hide();
                        }, 2500);
                    } else {
                        alert(res.data.message);
                    }
                });
            });

            // Trigger Display by default
            setTimeout(showPopup, 1000);
        });
    });
})(jQuery);
