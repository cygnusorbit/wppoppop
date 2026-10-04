<?php
namespace WPPopPop\Integrations;

use WPPopPop\Admin\SettingsManager;

class WooCommerceManager {
    public static function is_woo_active(): bool {
        return class_exists('WooCommerce');
    }

    public static function init(): void {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'localize_cart_telemetry'], 20);
        add_filter('wppoppop_layer_content', [__CLASS__, 'replace_ecommerce_tags'], 10, 2);
    }

    public static function register_routes(): void {
        register_rest_route('wppoppop/v1', '/apply-coupon', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'handle_apply_coupon'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function localize_cart_telemetry(): void {
        if (!self::is_woo_active() || is_admin()) {
            return;
        }

        $cart = WC()->cart;
        $cart_total = $cart ? (float) $cart->get_total('edit') : 0.0;
        $cart_count = $cart ? $cart->get_cart_contents_count() : 0;
        $threshold  = (float) SettingsManager::get('woo_cart_threshold', 0);
        $enabled    = (bool) SettingsManager::get('woo_abandonment_enabled', 1);

        $product_title = '';
        $product_price = '';
        if (is_singular('product')) {
            global $post;
            $product = wc_get_product($post->ID);
            if ($product) {
                $product_title = $product->get_name();
                $product_price = html_entity_decode(strip_tags(wc_price($product->get_price())));
            }
        }

        wp_localize_script('wppoppop-triggers', 'WPPopPopWooConfig', [
            'active'          => true,
            'cartCount'       => $cart_count,
            'cartTotal'       => $cart_total,
            'isCart'          => is_cart(),
            'isCheckout'      => is_checkout(),
            'abandonment'     => $enabled && ($cart_count > 0) && ($cart_total >= $threshold),
            'abandonPopupId'  => absint(SettingsManager::get('woo_abandonment_popup', 0)),
            'productTitle'    => $product_title,
            'productPrice'    => $product_price,
            'applyCouponUrl'  => rest_url('wppoppop/v1/apply-coupon'),
            'checkoutUrl'     => wc_get_checkout_url(),
        ]);
    }

    public static function handle_apply_coupon(\WP_REST_Request $request): \WP_REST_Response {
        if (!self::is_woo_active()) {
            return new \WP_REST_Response(['success' => false, 'message' => 'WooCommerce is not active.'], 400);
        }

        $params = $request->get_json_params() ?: $request->get_params();
        $coupon = sanitize_text_field($params['coupon_code'] ?? '');

        if (empty($coupon)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Coupon code is required.'], 400);
        }

        if (!WC()->cart->has_discount($coupon)) {
            $applied = WC()->cart->apply_coupon($coupon);
            if (!$applied) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Invalid or expired coupon code.', 'wppoppop'),
                ], 400);
            }
        }

        return new \WP_REST_Response([
            'success'      => true,
            'message'      => sprintf(__('Coupon "%s" applied to cart!', 'wppoppop'), esc_html($coupon)),
            'checkout_url' => wc_get_checkout_url(),
        ], 200);
    }

    public static function replace_ecommerce_tags(string $content, int $popup_id = 0): string {
        if (!self::is_woo_active()) {
            return $content;
        }

        $cart = WC()->cart;
        $cart_total = $cart ? wc_price($cart->get_total('edit')) : '$0.00';
        $cart_count = $cart ? (string) $cart->get_cart_contents_count() : '0';

        $product_title = '';
        $product_price = '';
        if (is_singular('product')) {
            global $post;
            $product = wc_get_product($post->ID);
            if ($product) {
                $product_title = $product->get_name();
                $product_price = wc_price($product->get_price());
            }
        }

        $tags = [
            '{cart_total}'    => strip_tags($cart_total),
            '{cart_count}'    => $cart_count,
            '{product_name}'  => $product_title ?: __('Featured Product', 'wppoppop'),
            '{product_price}' => strip_tags($product_price) ?: '$0.00',
        ];

        return str_replace(array_keys($tags), array_values($tags), $content);
    }
}
