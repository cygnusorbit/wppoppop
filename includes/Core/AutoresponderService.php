<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class AutoresponderService {
    public function init(): void {
        add_action('init', [$this, 'handle_secure_download']);
    }

    public static function dispatch(int $lead_id, int $popup_id, string $email, string $name): void {
        $enabled = get_post_meta($popup_id, '_wppoppop_autoresponder_enabled', true) === '1';
        if (!$enabled) {
            return;
        }

        $subject   = get_post_meta($popup_id, '_wppoppop_autoresponder_subject', true) ?: __('Thank you for your request!', 'wppoppop');
        $body      = get_post_meta($popup_id, '_wppoppop_autoresponder_body', true);
        $asset_url = get_post_meta($popup_id, '_wppoppop_asset_file_url', true);
        $coupon    = get_post_meta($popup_id, '_wppoppop_step3_coupon', true) ?: '';

        if (empty($body)) {
            $body = "Hi {name},\n\nThank you for getting in touch! Here are your requested materials:\n{download_link}\n\nBest regards,\nThe Team";
        }

        $download_link = '';
        if (!empty($asset_url) && $lead_id > 0) {
            $token = wp_generate_password(36, false);
            $expiry_hours = (int)(get_post_meta($popup_id, '_wppoppop_token_expiry_hours', true) ?: 24);
            $expires_at = time() + ($expiry_hours * HOUR_IN_SECONDS);

            update_post_meta($lead_id, '_wppoppop_download_token', $token);
            update_post_meta($lead_id, '_wppoppop_token_expires', $expires_at);
            update_post_meta($lead_id, '_wppoppop_download_count', 0);

            $download_link = add_query_arg(['wppoppop_download' => $token], home_url('/'));
        }

        $display_name = !empty($name) ? $name : 'there';
        $email_content = str_replace(
            ['{name}', '{email}', '{popup_title}', '{download_link}', '{coupon}'],
            [$display_name, $email, get_the_title($popup_id), $download_link, $coupon],
            $body
        );

        wp_mail($email, $subject, $email_content);
    }

    public function handle_secure_download(): void {
        if (!isset($_GET['wppoppop_download'])) {
            return;
        }

        $token = sanitize_text_field(wp_unslash($_GET['wppoppop_download']));
        if (empty($token)) {
            return;
        }

        $query = new \WP_Query([
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [
                [
                    'key'     => '_wppoppop_download_token',
                    'value'   => $token,
                    'compare' => '=',
                ],
            ],
        ]);

        if (!$query->have_posts()) {
            wp_die(
                '<h1>' . esc_html__('Invalid Download Link', 'wppoppop') . '</h1><p>' . esc_html__('This download token is invalid or has already been revoked.', 'wppoppop') . '</p>',
                esc_html__('Download Error', 'wppoppop'),
                ['response' => 403]
            );
        }

        $lead       = $query->posts[0];
        $expires_at = (int) get_post_meta($lead->ID, '_wppoppop_token_expires', true);

        if ($expires_at > 0 && time() > $expires_at) {
            wp_die(
                '<h1>' . esc_html__('Download Link Expired', 'wppoppop') . '</h1><p>' . esc_html__('This download link has passed its validity window. Please request a new link.', 'wppoppop') . '</p>',
                esc_html__('Link Expired', 'wppoppop'),
                ['response' => 410]
            );
        }

        $popup_id  = absint(get_post_meta($lead->ID, '_wppoppop_lead_popup_id', true));
        $asset_url = get_post_meta($popup_id, '_wppoppop_asset_file_url', true);

        if (empty($asset_url)) {
            wp_die(__('No digital asset is assigned to this download.', 'wppoppop'));
        }

        // Increment download counter
        $count = (int) get_post_meta($lead->ID, '_wppoppop_download_count', true);
        update_post_meta($lead->ID, '_wppoppop_download_count', $count + 1);
        update_post_meta($lead->ID, '_wppoppop_download_last', current_time('mysql'));

        // If hosted locally inside wp-content/uploads, stream directly to obscure location
        $upload_dir = wp_upload_dir();
        if (str_starts_with($asset_url, $upload_dir['baseurl'])) {
            $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $asset_url);
            if (file_exists($file_path)) {
                $filename = basename($file_path);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_path));
                readfile($file_path);
                exit;
            }
        }

        // Otherwise redirect to external URL
        wp_redirect($asset_url);
        exit;
    }
}
