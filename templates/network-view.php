<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables are local to the method that includes this file
if (!defined('ABSPATH')) {
    exit;
}
$net_logo_custom = class_exists('WpPopPop_Admin_Theme') ? WpPopPop_Admin_Theme::logo_url() : '';
$net_logo        = ($net_logo_custom !== '') ? $net_logo_custom : WPPOPPOP_URL . 'admin/img/logo-temp.svg';
$net_pro_active  = defined('WPPOPPOP_PRO_VERSION');
$net_more        = max(0, $net_total - count($net_sites));
?>
<div class="wrap wppoppop-dash wppoppop-theme-wrap wppoppop-network-wrap">
    <h1 class="screen-reader-text"><?php esc_html_e('WpPopPop Network Settings', 'wppoppop'); ?></h1>
    <div class="wppoppop-dash-bar" role="navigation" aria-label="WpPopPop">
        <div class="cell logo"><img src="<?php echo esc_url($net_logo); ?>" alt="WpPopPop"<?php echo ($net_logo_custom !== '') ? ' style="width:auto;max-width:140px;height:28px;object-fit:contain;"' : ''; ?>></div>
        <div class="cell title">
            <span class="dashicons dashicons-networking"></span>
            <span><?php esc_html_e('Network Settings', 'wppoppop'); ?></span>
        </div>
    </div>

    <?php if ($net_status === 'saved') : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Network settings saved.', 'wppoppop'); ?></p></div>
    <?php endif; ?>

    <div class="wpp-theme-layout">
        <div class="wpp-theme-main">
            <form method="post" action="<?php echo esc_url(network_admin_url('edit.php?action=' . WpPopPop_Admin_Network::ACTION)); ?>">
                <?php wp_nonce_field(WpPopPop_Admin_Network::NONCE, 'wpp_network_nonce'); ?>
                <section class="wpp-theme-panel is-active">
                    <h2><?php esc_html_e('Defaults for new sites', 'wppoppop'); ?></h2>
                    <p class="description"><?php esc_html_e('These values are copied into every site created from now on. Existing sites keep their own settings.', 'wppoppop'); ?></p>
                    <div class="wpp-theme-fields">
                        <div class="wpp-theme-field">
                            <label for="wpp-net-sender-name"><?php esc_html_e('Sender name', 'wppoppop'); ?></label>
                            <input type="text" id="wpp-net-sender-name" name="wpp_network[sender_name]" value="<?php echo esc_attr($net_values['sender_name']); ?>" placeholder="WpPopPop">
                        </div>
                        <div class="wpp-theme-field">
                            <label for="wpp-net-sender-email"><?php esc_html_e('Sender email', 'wppoppop'); ?></label>
                            <input type="text" id="wpp-net-sender-email" name="wpp_network[sender_email]" value="<?php echo esc_attr($net_values['sender_email']); ?>" placeholder="<?php echo esc_attr(get_site_option('admin_email')); ?>">
                            <p class="description"><?php esc_html_e('Leave empty to use each site\'s admin email.', 'wppoppop'); ?></p>
                        </div>
                        <div class="wpp-theme-field is-wide is-bool">
                            <label for="wpp-net-clean"><?php esc_html_e('Remove data when the plugin is deleted (new sites)', 'wppoppop'); ?></label>
                            <input type="checkbox" id="wpp-net-clean" name="wpp_network[clean_on_uninstall]" value="1" <?php checked(!empty($net_values['clean_on_uninstall'])); ?>>
                        </div>
                        <p class="description wpp-theme-field is-wide"><?php esc_html_e('When on, deleting the plugin also drops the tables and settings of those sites, and removes these network settings.', 'wppoppop'); ?></p>
                    </div>
                    <p><button type="submit" class="button button-primary"><?php esc_html_e('Save network settings', 'wppoppop'); ?></button></p>
                </section>
            </form>

            <section class="wpp-theme-panel is-active">
                <h2><?php esc_html_e('Sites', 'wppoppop'); ?></h2>
                <table class="widefat striped wppoppop-network-sites">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e('Site', 'wppoppop'); ?></th>
                            <th scope="col"><?php esc_html_e('Remove data on delete', 'wppoppop'); ?></th>
                            <th scope="col"><?php esc_html_e('Settings', 'wppoppop'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($net_sites as $net_row) : ?>
                        <tr>
                            <td><?php echo esc_html($net_row['name'] !== '' ? $net_row['name'] : '#' . $net_row['id']); ?> <span class="description">#<?php echo (int) $net_row['id']; ?></span></td>
                            <td>
                                <?php if (!$net_row['ready']) : ?>
                                    <span class="wppoppop-network-chip">&mdash;</span>
                                <?php elseif ($net_row['clean']) : ?>
                                    <span class="wppoppop-network-chip is-on"><?php esc_html_e('Yes', 'wppoppop'); ?></span>
                                <?php else : ?>
                                    <span class="wppoppop-network-chip"><?php esc_html_e('No', 'wppoppop'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><a href="<?php echo esc_url($net_row['url']); ?>"><?php esc_html_e('Open site settings', 'wppoppop'); ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($net_more > 0) : ?>
                    <p class="description">
                        <?php
                        /* translators: %d: number of sites that are not listed */
                        echo esc_html(sprintf(_n('And %d more site.', 'And %d more sites.', $net_more, 'wppoppop'), $net_more));
                        ?>
                    </p>
                <?php endif; ?>
            </section>
        </div>

        <aside class="wpp-theme-side">
            <div class="wppoppop-box wpp-theme-box">
                <h3><?php esc_html_e('Network status', 'wppoppop'); ?></h3>
                <ul class="wppoppop-network-status">
                    <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Network activated', 'wppoppop'); ?></li>
                    <li>
                        <span class="dashicons <?php echo $net_pro_active ? 'dashicons-yes-alt' : 'dashicons-minus'; ?>"></span>
                        <?php echo $net_pro_active ? esc_html__('WpPopPop Pro is active', 'wppoppop') : esc_html__('WpPopPop Pro is not active', 'wppoppop'); ?>
                    </li>
                    <li><span class="dashicons dashicons-admin-multisite"></span>
                        <?php
                        /* translators: %d: number of sites in the network */
                        echo esc_html(sprintf(_n('%d site in the network', '%d sites in the network', $net_total, 'wppoppop'), $net_total));
                        ?>
                    </li>
                </ul>
                <p class="description"><?php esc_html_e('Popups, submissions and per-site settings are managed inside each site, under WpPopPop > Settings.', 'wppoppop'); ?></p>
            </div>
        </aside>
    </div>
</div>
