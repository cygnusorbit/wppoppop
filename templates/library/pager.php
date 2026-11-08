<?php
if (!defined('ABSPATH')) {
    exit;
}
$wpp_pg_pos = isset($wpp_pg_pos) ? $wpp_pg_pos : 'top';
if ($wpp_pg_pos === 'top') : ?>
<div class="wpp-pg-bar" id="wpp-pg-bar">
    <span class="wpp-pg-info" id="wpp-pg-info" aria-live="polite"></span>
    <div class="wpp-pg-views" role="group" aria-label="<?php esc_attr_e('Cards per page', 'wppoppop'); ?>">
        <span class="wpp-pg-label"><?php esc_html_e('View', 'wppoppop'); ?></span>
        <button type="button" class="wpp-pg-view" data-cols="3" aria-pressed="true">3 &times; 3</button>
        <button type="button" class="wpp-pg-view" data-cols="4" aria-pressed="false">4 &times; 4</button>
        <button type="button" class="wpp-pg-view" data-cols="5" aria-pressed="false">5 &times; 5</button>
    </div>
</div>
<?php else : ?>
<nav class="wpp-pg-nav" id="wpp-pg-nav" aria-label="<?php esc_attr_e('Library pagination', 'wppoppop'); ?>"></nav>
<?php endif; ?>
