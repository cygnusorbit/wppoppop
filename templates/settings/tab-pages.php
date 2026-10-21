<?php
if (!defined('ABSPATH')) {
    exit;
}
$__pg_rows = class_exists('WpPopPop_Admin_Menu')
    ? WpPopPop_Admin_Menu::resolve_pages(WpPopPop_Admin_Menu::get_original_pages(), WpPopPop_Admin_Menu::get_prefs())
    : [];
$__pg_pin = class_exists('WpPopPop_Admin_Menu') ? WpPopPop_Admin_Menu::pinned() : [];
?>
<style>
.wppoppop-pg-list{max-width:760px;margin:12px 0;padding:0;list-style:none}
.wppoppop-pg-list li{display:flex;align-items:center;gap:10px;padding:8px 12px;margin:0 0 -1px;background:#fff;border:1px solid #dcdcde}
.wppoppop-pg-list li.ui-sortable-helper{box-shadow:0 2px 8px rgba(0,0,0,.15)}
.wppoppop-pg-list li.is-hidden-page .wpp-pg-label{opacity:.55}
.wppoppop-pg-list .wpp-pg-handle{cursor:move;color:#787c82;flex:none;user-select:none;-webkit-user-select:none}
.wppoppop-pg-list .wpp-pg-label{flex:1;min-width:0}
.wppoppop-pg-list .wpp-pg-slug{flex:none;font-size:11px;color:#787c82;min-width:130px}
.wppoppop-pg-list .wpp-pg-move{flex:none;display:flex;gap:2px}
.wppoppop-pg-list .wpp-pg-hide{flex:none;font-size:12px;white-space:nowrap}
</style>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Pages', 'wppoppop'); ?></h3>
    <p class="description"><?php esc_html_e('Rename, reorder or hide WpPopPop pages in the admin menu. The list syncs automatically with the menu. Hidden pages remain reachable by URL. Save settings, then reload to see the menu update.', 'wppoppop'); ?></p>

    <?php if (empty($__pg_rows)) : ?>
        <p><em><?php esc_html_e('No menu pages detected.', 'wppoppop'); ?></em></p>
    <?php else : ?>
    <ul id="wppoppop-pages-list" class="wppoppop-pg-list">
        <?php foreach ($__pg_rows as $__i => $__r) :
            $__is_pin = in_array($__r['slug'], $__pg_pin, true);
            $__n = 'settings[menu_pages][' . esc_attr($__r['slug']) . ']';
        ?>
        <li data-slug="<?php echo esc_attr($__r['slug']); ?>" data-idx="<?php echo (int) $__r['idx']; ?>" data-default="<?php echo esc_attr($__r['default']); ?>" class="<?php echo $__r['hidden'] ? 'is-hidden-page' : ''; ?>">
            <span class="dashicons dashicons-menu wpp-pg-handle" title="<?php esc_attr_e('Drag to reorder', 'wppoppop'); ?>"></span>
            <input type="text" class="regular-text wpp-pg-label" maxlength="60"
                   name="<?php echo $__n; ?>[label]"
                   value="<?php echo esc_attr($__r['custom'] ? $__r['label'] : ''); ?>"
                   placeholder="<?php echo esc_attr($__r['default']); ?>">
            <code class="wpp-pg-slug"><?php echo esc_html($__r['slug']); ?></code>
            <span class="wpp-pg-move">
                <button type="button" class="button button-small wpp-pg-up" aria-label="<?php esc_attr_e('Move up', 'wppoppop'); ?>">&uarr;</button>
                <button type="button" class="button button-small wpp-pg-down" aria-label="<?php esc_attr_e('Move down', 'wppoppop'); ?>">&darr;</button>
            </span>
            <label class="wpp-pg-hide">
                <input type="hidden" name="<?php echo $__n; ?>[hidden]" value="0">
                <input type="checkbox" class="wpp-pg-hidden-cb" name="<?php echo $__n; ?>[hidden]" value="1" <?php checked(!empty($__r['hidden'])); ?> <?php disabled($__is_pin); ?>>
                <?php echo $__is_pin ? esc_html__('Always visible', 'wppoppop') : esc_html__('Hide', 'wppoppop'); ?>
            </label>
            <input type="hidden" class="wpp-pg-order" name="<?php echo $__n; ?>[order]" value="<?php echo (int) ($__i + 1); ?>">
        </li>
        <?php endforeach; ?>
    </ul>
    <p><button type="button" class="button" id="wppoppop-pages-reset"><?php esc_html_e('Reset to default', 'wppoppop'); ?></button></p>
    <?php endif; ?>
</div>
