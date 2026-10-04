<?php
namespace WPPopPop\API;

use WPPopPop\Targeting\PopupPostType;
use WPPopPop\Core\LayerRenderer;

class RemoteEmbedController {
    public function init(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
        add_action('init', [$this, 'handle_standalone_embed_script']);
        add_filter('rest_pre_serve_request', [$this, 'handle_cors'], 10, 4);
    }

    public function handle_cors(bool $served, \WP_HTTP_Response $result, \WP_REST_Request $request, \WP_REST_Server $server): bool {
        $route = $request->get_route();
        if (str_starts_with($route, '/wppoppop/v1/')) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-WP-Nonce');
        }
        return $served;
    }

    public function register_routes(): void {
        register_rest_route('wppoppop/v1', '/remote-data/(?P<id>\d+)', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_remote_popup_data'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function get_remote_popup_data(\WP_REST_Request $request): \WP_REST_Response {
        $popup_id = absint($request->get_param('id'));
        $popup = get_post($popup_id);

        if (!$popup || $popup->post_type !== PopupPostType::POST_TYPE || $popup->post_status !== 'publish') {
            return new \WP_REST_Response(['error' => 'Popup not found or inactive'], 404);
        }

        $exit_intent  = get_post_meta($popup->ID, '_wppoppop_exit_intent', true) !== '0';
        $scroll_depth = (int)(get_post_meta($popup->ID, '_wppoppop_scroll_depth', true) ?: 50);
        $inactivity   = (int)(get_post_meta($popup->ID, '_wppoppop_inactivity', true) ?: 10);
        $position     = get_post_meta($popup->ID, '_wppoppop_position', true) ?: 'center';
        $optin_locker = get_post_meta($popup->ID, '_wppoppop_optin_locker', true) === '1';

        $html_content = LayerRenderer::render_layers($popup->ID, apply_filters('the_content', $popup->post_content));

        return new \WP_REST_Response([
            'id'            => $popup->ID,
            'title'         => $popup->post_title,
            'html'          => $html_content,
            'position'      => $position,
            'optinLocker'   => $optin_locker,
            'exitIntent'    => $exit_intent,
            'scrollPercent' => $scroll_depth,
            'inactivitySec' => $inactivity,
            'cssUrl'        => esc_url_raw(WPPOPPOP_URL . 'assets/css/wppoppop.css'),
            'submitUrl'     => esc_url_raw(rest_url('wppoppop/v1/submit')),
            'impressionUrl' => esc_url_raw(rest_url('wppoppop/v1/impression')),
        ], 200);
    }

    public function handle_standalone_embed_script(): void {
        if (!isset($_GET['wppoppop_embed'])) {
            return;
        }

        $popup_id = absint($_GET['wppoppop_embed']);
        if (!$popup_id) {
            exit;
        }

        header('Content-Type: application/javascript; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Cache-Control: public, max-age=300');

        $remote_api_endpoint = esc_url_raw(rest_url("wppoppop/v1/remote-data/{$popup_id}"));
        ?>
(function() {
    'use strict';

    var endpoint = <?php echo wp_json_encode($remote_api_endpoint); ?>;

    fetch(endpoint)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data || !data.html) return;

            // 1. Inject Stylesheet if not already present
            if (!document.getElementById('wppoppop-remote-css')) {
                var link = document.createElement('link');
                link.id = 'wppoppop-remote-css';
                link.rel = 'stylesheet';
                link.href = data.cssUrl;
                document.head.appendChild(link);
            }

            // 2. Build Container & Overlay
            var overlay = document.createElement('div');
            overlay.id = 'wppoppop-modal';
            overlay.className = 'wppoppop-overlay wppoppop-pos-' + data.position;
            if (data.optinLocker) overlay.className += ' wppoppop-locker-active';
            overlay.setAttribute('aria-hidden', 'true');

            var container = document.createElement('div');
            container.className = 'wppoppop-container';
            container.setAttribute('role', 'dialog');
            container.setAttribute('aria-modal', 'true');

            if (!data.optinLocker) {
                var closeBtn = document.createElement('button');
                closeBtn.type = 'button';
                closeBtn.className = 'wppoppop-close';
                closeBtn.innerHTML = '&times;';
                closeBtn.setAttribute('aria-label', 'Close');
                closeBtn.onclick = function() { closeModal(); };
                container.appendChild(closeBtn);
            }

            var contentWrap = document.createElement('div');
            contentWrap.className = 'wppoppop-content';
            contentWrap.innerHTML = data.html;
            container.appendChild(contentWrap);
            overlay.appendChild(container);
            document.body.appendChild(overlay);

            var isTriggered = false;
            var impressionSent = false;
            var cookieKey = 'wppoppop_sub_' + data.id;

            if (localStorage.getItem(cookieKey) === '1' || document.cookie.indexOf(cookieKey + '=1') !== -1) {
                return;
            }

            function recordImpression() {
                if (impressionSent || !data.impressionUrl) return;
                impressionSent = true;
                fetch(data.impressionUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ popup_id: data.id })
                }).catch(function() {});
            }

            function showModal(triggerName) {
                if (isTriggered) return;
                isTriggered = true;
                overlay.classList.add('wppoppop-visible');
                overlay.setAttribute('aria-hidden', 'false');
                recordImpression();
            }

            function closeModal() {
                if (data.optinLocker) return;
                overlay.classList.remove('wppoppop-visible');
                overlay.setAttribute('aria-hidden', 'true');
            }

            if (!data.optinLocker) {
                overlay.onclick = function(e) {
                    if (e.target === overlay) closeModal();
                };
            }

            // Triggers
            if (data.exitIntent) {
                document.addEventListener('mouseleave', function(e) {
                    if (e.clientY <= 15 && e.relatedTarget == null) {
                        showModal('OnExit');
                    }
                });
            }

            if (data.scrollPercent > 0) {
                window.addEventListener('scroll', function() {
                    var h = document.documentElement;
                    var b = document.body;
                    var st = h.scrollTop || b.scrollTop;
                    var sh = (h.scrollHeight || b.scrollHeight) - h.clientHeight;
                    if (sh > 0 && ((st / sh) * 100) >= data.scrollPercent) {
                        showModal('OnScroll');
                    }
                }, { passive: true });
            }

            if (data.inactivitySec > 0) {
                var timer;
                var resetTimer = function() {
                    clearTimeout(timer);
                    timer = setTimeout(function() { showModal('OnInactivity'); }, data.inactivitySec * 1000);
                };
                ['mousemove', 'keydown', 'scroll', 'touchstart'].forEach(function(evt) {
                    window.addEventListener(evt, resetTimer, { passive: true });
                });
                resetTimer();
            }

            // Remote Form Submission
            var form = overlay.querySelector('#wppoppop-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var feedback = overlay.querySelector('#wppoppop-feedback');
                    var submitBtn = overlay.querySelector('#wppoppop-submit-btn');

                    if (feedback) feedback.style.display = 'none';
                    if (submitBtn) submitBtn.disabled = true;

                    var payload = {
                        popup_id: data.id,
                        name: (overlay.querySelector('#wppoppop-name') || {}).value || '',
                        email: (overlay.querySelector('#wppoppop-email') || {}).value || '',
                        gdpr: (overlay.querySelector('#wppoppop-gdpr') || {}).checked || false
                    };

                    fetch(data.submitUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.success) {
                            localStorage.setItem(cookieKey, '1');
                            document.cookie = cookieKey + '=1; path=/; max-age=2592000; SameSite=Lax';

                            var step3 = overlay.querySelector('.wppoppop-step-3');
                            if (step3) {
                                var steps = overlay.querySelectorAll('.wppoppop-step');
                                steps.forEach(function(s) { s.classList.remove('wppoppop-step-active'); });
                                step3.classList.add('wppoppop-step-active');
                                var bar = overlay.querySelector('#wppoppop-progress-bar');
                                if (bar) bar.style.width = '100%';
                            } else {
                                if (feedback) {
                                    feedback.className = 'wppoppop-feedback wppoppop-success';
                                    feedback.textContent = res.message || 'Subscribed!';
                                    feedback.style.display = 'block';
                                }
                                setTimeout(closeModal, 1800);
                            }
                        } else {
                            if (feedback) {
                                feedback.className = 'wppoppop-feedback wppoppop-error';
                                feedback.textContent = res.message || 'Submission error.';
                                feedback.style.display = 'block';
                            }
                            if (submitBtn) submitBtn.disabled = false;
                        }
                    })
                    .catch(function() {
                        if (feedback) {
                            feedback.className = 'wppoppop-feedback wppoppop-error';
                            feedback.textContent = 'Connection error.';
                            feedback.style.display = 'block';
                        }
                        if (submitBtn) submitBtn.disabled = false;
                    });
                });
            }
        })
        .catch(function(err) {
            console.error('[WPPopPop Remote] Initialization failed', err);
        });
})();
        <?php
        exit;
    }
}
