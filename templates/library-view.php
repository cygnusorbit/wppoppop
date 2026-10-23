<?php
if (!defined('ABSPATH')) {
    exit;
}

// Built-in starter templates removed: only saved (My Templates) and premade (library-presets) cards are listed.
$templates = []; /* WPPOPPOP_NO_HARDCODED */

// User-saved templates (Builder > Save As Template)
$wppoppop_user_tpls = get_option('wppoppop_user_templates', []);
$wppoppop_user_cards = [];
if (is_array($wppoppop_user_tpls)) {
    foreach (array_reverse($wppoppop_user_tpls) as $ut) {
        if (!is_array($ut) || empty($ut['id'])) { continue; }
        $wppoppop_user_cards[] = [
            'id'          => $ut['id'],
            'category'    => (isset($ut['category']) && $ut['category'] !== '') ? $ut['category'] : 'custom',
            'cat_label'   => __('My Templates', 'wppoppop'),
            'title'       => isset($ut['title']) ? $ut['title'] : '',
            'description' => isset($ut['description']) ? $ut['description'] : '',
            'icon'        => 'dashicons-saved',
            'bg_preview'  => 'linear-gradient(135deg, #00a32a, #0f172a)',
            'image'       => isset($ut['image']) ? $ut['image'] : '',
            'user'        => true,
            'config'      => [
                'meta'     => ['width' => isset($ut['width']) ? (int) $ut['width'] : 640, 'height' => isset($ut['height']) ? (int) $ut['height'] : 400, 'bg_mode' => 'solid', 'bg_color' => '#ffffff'],
                'triggers' => new stdClass(),
                'elements' => isset($ut['elements']) && is_array($ut['elements']) ? $ut['elements'] : [],
            ],
        ];
    }
}

// Premade templates (library-presets/*.json)
if (!class_exists('WpPopPop_Library_Presets')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-library-presets.php'; }
$wppoppop_preset_cards = [];
foreach (WpPopPop_Library_Presets::all() as $wppoppop_pt) {
    $wppoppop_preset_cards[] = [
        'id'          => $wppoppop_pt['id'],
        'category'    => $wppoppop_pt['category'],
        'cat_label'   => __('Premade', 'wppoppop'),
        'title'       => $wppoppop_pt['title'],
        'description' => $wppoppop_pt['description'],
        'icon'        => 'dashicons-layout',
        'bg_preview'  => 'linear-gradient(135deg, #2563eb, #0f172a)',
        'image'       => $wppoppop_pt['image'],
        'preset'      => true,
        'config'      => [
            'meta'     => ['width' => $wppoppop_pt['width'], 'height' => $wppoppop_pt['height'], 'bg_mode' => 'solid', 'bg_color' => '#ffffff'],
            'triggers' => new stdClass(),
            'elements' => $wppoppop_pt['elements'],
        ],
    ];
}
$templates = array_merge($wppoppop_user_cards, $wppoppop_preset_cards, $templates);

// Keep card badges in sync with renamed categories
if (!class_exists('WpPopPop_Ajax_Templates')) { require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-templates.php'; }
$wppoppop_cat_map = [];
foreach (WpPopPop_Ajax_Templates::get_categories() as $wppoppop_c) { $wppoppop_cat_map[$wppoppop_c['slug']] = $wppoppop_c['label']; }
foreach ($templates as $wppoppop_i => $wppoppop_t) {
    /* WPPOPPOP_TPL_CAT_FALLBACK: deleted category -> My Templates */
    if (!empty($wppoppop_t['user']) && !isset($wppoppop_cat_map[$wppoppop_t['category']])) { $templates[$wppoppop_i]['category'] = 'custom'; $wppoppop_t['category'] = 'custom'; }
    if (isset($wppoppop_cat_map[$wppoppop_t['category']])) { $templates[$wppoppop_i]['cat_label'] = $wppoppop_cat_map[$wppoppop_t['category']]; }
}
?>
<div class="wrap wppoppop-dash wppoppop-library-wrap">
    <!-- Catalog Header Toolbar & Category Filters -->
    <?php include WPPOPPOP_PATH . 'templates/library/header.php'; ?>

    <!-- Responsive Template Cards Grid -->
    <?php include WPPOPPOP_PATH . 'templates/library/grid.php'; ?>

    <!-- Template Inspection & Preview Modal -->
    <?php include WPPOPPOP_PATH . 'templates/library/modal-preview.php'; ?>

    <!-- Category Manager (gear icon) -->
    <?php include WPPOPPOP_PATH . 'templates/library/modal-categories.php'; ?>

    <!-- Template info editor (Edit icon) -->
    <?php include WPPOPPOP_PATH . 'templates/library/modal-template-edit.php'; ?>
</div>
