<?php
namespace WPPopPop\Targeting;

class WooCommerceManager {
    public static function is_wc_active(): bool {
        return class_exists('WooCommerce');
    }

    public static function matches_woocommerce(\WP_Post $post): bool {
        if (!self::is_wc_active()) {
            return true;
        }

        $wc_rule = get_post_meta($post->ID, '_wppoppop_wc_rule', true) ?: 'all';
        if ($wc_rule === 'all') {
            return true;
        }

        if ($wc_rule === 'cart_abandonment') {
            if (!function_exists('is_cart') || !function_exists('is_checkout')) {
                return false;
            }
            if (!is_cart() && !is_checkout()) {
                return false;
            }
            if (!function_exists('WC') || !WC()->cart || WC()->cart->is_empty()) {
                return false;
            }
            $min_total = (float) get_post_meta($post->ID, '_wppoppop_wc_min_cart', true);
            if ($min_total > 0 && WC()->cart->get_subtotal() < $min_total) {
                return false;
            }
            return true;
        }

        if ($wc_rule === 'products_only') {
            return function_exists('is_product') && is_product();
        }

        if ($wc_rule === 'cart_checkout_only') {
            return (function_exists('is_cart') && is_cart()) || (function_exists('is_checkout') && is_checkout());
        }

        return true;
    }
}
