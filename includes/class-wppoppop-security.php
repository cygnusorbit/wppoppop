<?php
/**
 * WpPopPop Security & AJAX Permission Controller
 * Autonomous Decomposed Modular Architecture
 *
 * @package WpPopPop
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WpPopPop_Security {

    /**
     * Singleton instance.
     * @var WpPopPop_Security|null
     */
    protected static $instance = null;

    /**
     * Get instance.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_security_nonce' ], 1 );
    }

    /**
     * Localize administrative security nonce & intercept admin AJAX dispatches.
     */
    public function enqueue_admin_security_nonce() {
        $nonce = wp_create_nonce( 'wppoppop_nonce' );

        // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- handle without a file, it only carries an inline script
        wp_register_script( 'wppoppop-security-bridge', false, [ 'jquery' ], null, false );
        wp_enqueue_script( 'wppoppop-security-bridge' );

        $script = '
            window.wppoppop_nonce = ' . wp_json_encode( $nonce ) . ';
            window.wppoppopSecurity = { nonce: ' . wp_json_encode( $nonce ) . ' };
            if ( typeof jQuery !== "undefined" ) {
                jQuery( document ).ajaxSend( function( event, xhr, settings ) {
                    if ( settings.url && settings.url.indexOf( "admin-ajax.php" ) !== -1 ) {
                        if ( typeof settings.data === "string" && settings.data.indexOf( "action=wppoppop_" ) !== -1 ) {
                            if ( settings.data.indexOf( "nonce=" ) === -1 && settings.data.indexOf( "_ajax_nonce=" ) === -1 && settings.data.indexOf( "security=" ) === -1 ) {
                                var separator = settings.data.length ? "&" : "";
                                settings.data += separator + "nonce=" + encodeURIComponent( window.wppoppop_nonce );
                            }
                        }
                    }
                } );
            }
        ';
        wp_add_inline_script( 'wppoppop-security-bridge', $script, 'before' );
    }

    /**
     * Resolve the visitor IP. Proxy headers are opt-in via the wppoppop_client_ip_headers filter.
     */
    public static function get_client_ip() {
        $headers = (array) apply_filters( 'wppoppop_client_ip_headers', [ 'REMOTE_ADDR' ] );
        foreach ( $headers as $h ) {
            if ( empty( $_SERVER[ $h ] ) ) {
                continue;
            }
            $raw  = sanitize_text_field( wp_unslash( $_SERVER[ $h ] ) );
            $list = array_map( 'trim', explode( ',', $raw ) );
            foreach ( $list as $candidate ) {
                if ( filter_var( $candidate, FILTER_VALIDATE_IP ) ) {
                    return $candidate;
                }
            }
        }
        return '';
    }

    /**
     * Hourly per-IP submission throttle. True = limit exceeded.
     */
    public static function is_rate_limited( $ip, $max_per_hour ) {
        $max_per_hour = (int) $max_per_hour;
        if ( $max_per_hour <= 0 ) {
            return false;
        }
        $key   = 'wppoppop_sub_rl_' . md5( (string) $ip );
        $count = (int) get_transient( $key );
        if ( $count >= $max_per_hour ) {
            return true;
        }
        set_transient( $key, $count + 1, HOUR_IN_SECONDS );
        return false;
    }

    /**
     * Server-side reCAPTCHA verification (v2 / v3). Unconfigured secret key cannot be verified, so it passes.
     */
    public static function verify_recaptcha( $token, $ip = '' ) {
        $secret = (string) wppoppop_get_setting( 'recaptcha_secret_key', '' );
        if ( $secret === '' ) {
            return true;
        }
        if ( empty( $token ) ) {
            return false;
        }
        $res = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [
            'timeout' => 10,
            'body'    => [
                'secret'   => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ],
        ] );
        if ( is_wp_error( $res ) || (int) wp_remote_retrieve_response_code( $res ) !== 200 ) {
            return false;
        }
        $body = json_decode( wp_remote_retrieve_body( $res ), true );
        if ( ! is_array( $body ) || empty( $body['success'] ) ) {
            return false;
        }
        if ( isset( $body['score'] ) ) {
            $threshold = (float) wppoppop_get_setting( 'recaptcha_threshold', 0.4 );
            return (float) $body['score'] >= $threshold;
        }
        return true;
    }

    /**
     * True when the email domain (or a parent domain) is a known disposable provider.
     */
    public static function is_disposable_email( $email ) {
        $at = strrpos( (string) $email, '@' );
        if ( $at === false ) {
            return false;
        }
        $domain  = strtolower( trim( substr( $email, $at + 1 ) ) );
        $domains = (array) apply_filters( 'wppoppop_disposable_domains', [
            'mailinator.com', '10minutemail.com', 'guerrillamail.com', 'trashmail.com',
            'tempmail.com', 'sharklasers.com', 'yopmail.com', 'getnada.com', 'throwawaymail.com',
            'temp-mail.org', 'dispostable.com', 'maildrop.cc', 'fakeinbox.com',
        ] );
        foreach ( $domains as $d ) {
            $d = strtolower( (string) $d );
            if ( $d !== '' && ( $domain === $d || substr( $domain, -strlen( $d ) - 1 ) === '.' . $d ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Authoritative backend permission and nonce verifier for administrative actions.
     *
     * @param string $cap Required capability.
     * @return bool True on success, terminates on failure.
     */
    public static function verify_admin( $cap = 'manage_options' ) {
        if ( ! current_user_can( $cap ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized permission.', 'wppoppop' ) ], 403 );
            wp_die();
        }

        $verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );

        if ( ! $verified ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Invalid or expired security token.', 'wppoppop' ) ], 403 );
            wp_die();
        }

        return true;
    }
}

if ( ! function_exists( 'wppoppop_get_client_ip' ) ) {
    function wppoppop_get_client_ip() {
        return WpPopPop_Security::get_client_ip();
    }
}

// Bootstrap
WpPopPop_Security::get_instance();
