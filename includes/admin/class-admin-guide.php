<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Builder guided tour: global on/off flag and per-user progress (user_meta).
 * The tour auto-starts once per user (last_step === 0); afterwards it only
 * runs from the builder Help button or after "Restart tour" in Settings.
 */
class WpPopPop_Admin_Guide {
    const META_KEY = 'wppoppop_guide_state';
    const NONCE    = 'wppoppop_guide_nonce';

    public function __construct() {
        add_action('wppoppop_admin_builder_assets', [$this, 'enqueue']);
    }

    /** Global "Show guide" switch (Settings > Builder). On unless explicitly saved as off. */
    public static function enabled() {
        $s = get_option('wppoppop_settings', []);
        if (!is_array($s) || !array_key_exists('builder_guide', $s)) {
            return true;
        }
        return !empty($s['builder_guide']);
    }

    public static function default_state() {
        return ['completed' => 0, 'dismissed' => 0, 'last_step' => 0];
    }

    /** Whitelist + cast: nothing outside these three keys is ever stored. */
    public static function sanitize_state($in) {
        $in  = is_array($in) ? $in : [];
        $out = self::default_state();
        $out['completed'] = !empty($in['completed']) ? 1 : 0;
        $out['dismissed'] = !empty($in['dismissed']) ? 1 : 0;
        $out['last_step'] = isset($in['last_step']) ? max(0, min(999, (int) $in['last_step'])) : 0;
        return $out;
    }

    public static function get_state($user_id = 0) {
        $user_id = $user_id ? (int) $user_id : get_current_user_id();
        if (!$user_id) {
            return self::default_state();
        }
        return self::sanitize_state(get_user_meta($user_id, self::META_KEY, true));
    }

    /** Merge a partial state into the stored one and persist it. */
    public static function save_state($user_id, array $patch) {
        $user_id = $user_id ? (int) $user_id : get_current_user_id();
        $state   = self::sanitize_state(array_merge(self::get_state($user_id), $patch));
        if ($user_id) {
            update_user_meta($user_id, self::META_KEY, $state);
        }
        return $state;
    }

    public static function reset_state($user_id = 0) {
        $user_id = $user_id ? (int) $user_id : get_current_user_id();
        if ($user_id) {
            delete_user_meta($user_id, self::META_KEY);
        }
        return self::default_state();
    }

    /** First builder open only: enabled, never started, not completed, not dismissed. */
    public static function should_autostart($user_id = 0) {
        if (!self::enabled()) {
            return false;
        }
        $st = self::get_state($user_id);
        return !$st['completed'] && !$st['dismissed'] && 0 === $st['last_step'];
    }

    public static function nonce() {
        return wp_create_nonce(self::NONCE);
    }

    /**
     * Built-in steps. Text supports **bold** only (no HTML).
     * selector: CSS selector or list of selectors (first visible one wins); empty = centered card.
     * before:   '', open-inspector, open-settings or inspector-tab:{basic|style|logic|data|advanced}.
     * empty:    text shown instead when the target is not available (e.g. no layer to select yet).
     */
    public static function builtin_steps() {
        return [
            [
                'id' => 'welcome', 'selector' => '', 'placement' => 'center', 'before' => '',
                'title' => __('Welcome to the builder', 'wppoppop'),
                'text'  => __('A quick tour of the essentials. You can skip it at any time and relaunch it from the **?** button in the toolbar.', 'wppoppop'),
            ],
            [
                'id' => 'ribbon', 'selector' => '#wppoppop-ribbon-wrap', 'placement' => 'bottom', 'before' => '',
                'title' => __('Add elements', 'wppoppop'),
                'text'  => __('Click an element in the ribbon, or **drag it onto the canvas**, to add it to your popup.', 'wppoppop'),
            ],
            [
                'id' => 'canvas', 'selector' => '#wppoppop-canvas-box', 'placement' => 'auto', 'before' => '',
                'title' => __('Position on the canvas', 'wppoppop'),
                'text'  => __('**Drag** a layer to move it and use its handles to resize it. Layers snap to the guides, and the arrow keys nudge the selected layer.', 'wppoppop'),
            ],
            [
                'id' => 'layers', 'selector' => '#wppoppop-floating-layers-panel', 'placement' => 'auto', 'before' => '',
                'title' => __('Layers panel', 'wppoppop'),
                'text'  => __('Every element is listed here. **Drag the grip** to change the stacking order, use the icons to duplicate, lock, hide or delete, and click a row to select it.', 'wppoppop'),
            ],
            [
                'id' => 'inspector', 'selector' => '#wppoppop-inspector-drawer', 'placement' => 'left', 'before' => 'open-inspector',
                'title' => __('Layer Settings (Inspector)', 'wppoppop'),
                'text'  => __('Selecting a layer slides the **Inspector** in and pushes the layers panel aside, so nothing gets covered.', 'wppoppop'),
                'empty' => __('Add an element and select it: the **Inspector** slides in from the right and pushes the layers panel aside, so nothing gets covered.', 'wppoppop'),
            ],
            [
                'id' => 'inspector-tabs', 'selector' => '#wppoppop-inspector-tabs-bar', 'placement' => 'left', 'before' => 'inspector-tab:basic',
                'title' => __('Inspector tabs', 'wppoppop'),
                'text'  => __('**Content**, **Style** and **Logic** hold every setting of the selected layer. Form fields also get **Data** and **Advanced** tabs.', 'wppoppop'),
                'empty' => __('The Inspector has **Content**, **Style** and **Logic** tabs (plus **Data** and **Advanced** for form fields) for the selected layer.', 'wppoppop'),
            ],
            [
                'id' => 'inspector-style', 'selector' => '#wppoppop-inspector-body', 'placement' => 'left', 'before' => 'inspector-tab:style',
                'title' => __('Style and logic', 'wppoppop'),
                'text'  => __('Colors, borders, shadows and spacing live in **Style**. Show or hide rules live in **Logic**. Changes apply to the canvas instantly.', 'wppoppop'),
                'empty' => __('Colors, borders, shadows and spacing live in **Style**. Show or hide rules live in **Logic**.', 'wppoppop'),
            ],
            [
                'id' => 'triggers', 'selector' => ['[data-html-module="acc-display-triggers"] .wppoppop-acc-header', '#wppoppop-settings-drawer'], 'placement' => 'left', 'before' => 'open-settings',
                'title' => __('Display triggers', 'wppoppop'),
                'text'  => __('Open **Popup Settings** to choose when the popup appears: on page load, after a delay, on exit intent, on scroll depth and more.', 'wppoppop'),
                'empty' => __('**Popup Settings** (the gear in the toolbar) is where you choose when the popup appears: page load, delay, exit intent, scroll depth and more.', 'wppoppop'),
            ],
            [
                'id' => 'preview', 'selector' => '#wppoppop-btn-preview', 'placement' => 'bottom', 'before' => '',
                'title' => __('Preview', 'wppoppop'),
                'text'  => __('Check how the popup looks and behaves before it goes live.', 'wppoppop'),
            ],
            [
                'id' => 'publish', 'selector' => '#wppoppop-btn-save', 'placement' => 'bottom', 'before' => '',
                'title' => __('Save and publish', 'wppoppop'),
                'text'  => __('Click **Save** to store your popup. It is published together with the triggers and targeting you configured.', 'wppoppop'),
            ],
        ];
    }

    const MAX_STEPS = 40;
    const MAX_TEXT  = 400;

    /** Built-in steps, each mapped to one of the target presets below. */
    public static function default_steps() {
        $map = [
            'welcome'         => 'center',
            'ribbon'          => 'ribbon',
            'canvas'          => 'canvas',
            'layers'          => 'layers',
            'inspector'       => 'inspector',
            'inspector-tabs'  => 'inspector_tabs',
            'inspector-style' => 'inspector_body',
            'triggers'        => 'triggers',
            'preview'         => 'preview',
            'publish'         => 'save',
        ];
        $out = [];
        foreach (self::builtin_steps() as $s) {
            $s['target'] = isset($map[$s['id']]) ? $map[$s['id']] : 'center';
            $out[] = $s;
        }
        return $out;
    }

    /** Builder areas a step can point at (editor dropdown). Extend with the wppoppop_guide_targets filter. */
    public static function targets() {
        $t = [
            'center'         => ['label' => __('None (centered card)', 'wppoppop'), 'selector' => [], 'placement' => 'center', 'before' => ''],
            'ribbon'         => ['label' => __('Elements ribbon', 'wppoppop'), 'selector' => ['#wppoppop-ribbon-wrap'], 'placement' => 'bottom', 'before' => ''],
            'canvas'         => ['label' => __('Canvas', 'wppoppop'), 'selector' => ['#wppoppop-canvas-box'], 'placement' => 'auto', 'before' => ''],
            'layers'         => ['label' => __('Layers panel', 'wppoppop'), 'selector' => ['#wppoppop-floating-layers-panel'], 'placement' => 'auto', 'before' => ''],
            'inspector'      => ['label' => __('Inspector (Layer Settings)', 'wppoppop'), 'selector' => ['#wppoppop-inspector-drawer'], 'placement' => 'left', 'before' => 'open-inspector'],
            'inspector_tabs' => ['label' => __('Inspector tabs', 'wppoppop'), 'selector' => ['#wppoppop-inspector-tabs-bar'], 'placement' => 'left', 'before' => 'inspector-tab:basic'],
            'inspector_body' => ['label' => __('Inspector settings (Style tab)', 'wppoppop'), 'selector' => ['#wppoppop-inspector-body'], 'placement' => 'left', 'before' => 'inspector-tab:style'],
            'triggers'       => ['label' => __('Display triggers (Popup Settings)', 'wppoppop'), 'selector' => ['[data-html-module="acc-display-triggers"] .wppoppop-acc-header', '#wppoppop-settings-drawer'], 'placement' => 'left', 'before' => 'open-settings'],
            'preview'        => ['label' => __('Preview button', 'wppoppop'), 'selector' => ['#wppoppop-btn-preview'], 'placement' => 'bottom', 'before' => ''],
            'save'           => ['label' => __('Save / Publish button', 'wppoppop'), 'selector' => ['#wppoppop-btn-save'], 'placement' => 'bottom', 'before' => ''],
            'help'           => ['label' => __('Guide (?) button', 'wppoppop'), 'selector' => ['#wppoppop-btn-guide'], 'placement' => 'bottom', 'before' => ''],
        ];
        $t   = apply_filters('wppoppop_guide_targets', $t);
        $out = [];
        foreach ((array) $t as $key => $v) {
            $key = sanitize_key($key);
            if ('' === $key || 'custom' === $key || !is_array($v)) {
                continue;
            }
            $sels = [];
            foreach ((array) (isset($v['selector']) ? $v['selector'] : []) as $one) {
                $one = self::clean_selector($one);
                if ('' !== $one) {
                    $sels[] = $one;
                }
            }
            $out[$key] = [
                'label'     => isset($v['label']) ? sanitize_text_field($v['label']) : $key,
                'selector'  => $sels,
                'placement' => self::clean_place(isset($v['placement']) ? $v['placement'] : 'auto'),
                'before'    => self::clean_before(isset($v['before']) ? $v['before'] : ''),
            ];
        }
        if (!isset($out['center'])) {
            $out = ['center' => ['label' => __('None (centered card)', 'wppoppop'), 'selector' => [], 'placement' => 'center', 'before' => '']] + $out;
        }
        return $out;
    }

    protected static function clean_before($before) {
        $before = (string) $before;
        return preg_match('/^(open-inspector|open-settings|inspector-tab:(basic|style|logic|data|advanced))?$/', $before) ? $before : '';
    }

    protected static function clean_place($place) {
        return in_array($place, ['auto', 'top', 'bottom', 'left', 'right', 'center'], true) ? $place : 'auto';
    }

    protected static function default_row($d) {
        return [
            'id' => $d['id'], 'enabled' => 1, 'title' => $d['title'], 'text' => $d['text'],
            'target' => $d['target'], 'selector' => '', 'before' => '', 'placement' => 'auto', 'custom' => 0,
        ];
    }

    /** Rows for the editor's "Reset to defaults" button. */
    public static function editor_defaults() {
        $out = [];
        foreach (self::default_steps() as $d) {
            $out[] = self::default_row($d);
        }
        return $out;
    }

    /**
     * Whitelist the rows posted by the Settings editor (Settings > Builder > Guide Steps).
     * Built-in steps keep their id, edits that equal the built-in text are not stored (so translations
     * and plugin updates keep flowing); custom steps get a generated "c-xxxxxxxx" id.
     */
    public static function sanitize_custom_steps($in) {
        if (!is_array($in)) {
            return [];
        }
        $targets  = self::targets();
        $defaults = [];
        foreach (self::default_steps() as $d) {
            $defaults[$d['id']] = $d;
        }
        $out  = [];
        $seen = [];
        foreach ($in as $row) {
            if (!is_array($row) || count($out) >= self::MAX_STEPS) {
                continue;
            }
            $id      = isset($row['id']) ? sanitize_key($row['id']) : '';
            $builtin = isset($defaults[$id]);
            if ($builtin && isset($seen[$id])) {
                continue;
            }
            if (!$builtin && (!preg_match('/^c-[a-z0-9]{6,12}$/', $id) || isset($seen[$id]))) {
                $id = 'c-' . substr(md5(uniqid('', true)), 0, 8);
            }
            $title = isset($row['title']) ? mb_substr(sanitize_text_field($row['title']), 0, 120) : '';
            $text  = isset($row['text']) ? mb_substr(sanitize_text_field($row['text']), 0, self::MAX_TEXT) : '';
            if ($builtin) {
                if ($title === sanitize_text_field($defaults[$id]['title'])) {
                    $title = '';
                }
                if ($text === sanitize_text_field($defaults[$id]['text'])) {
                    $text = '';
                }
            } elseif ('' === $title || '' === $text) {
                continue;
            }
            $target = isset($row['target']) ? sanitize_key($row['target']) : '';
            if ('custom' !== $target && !isset($targets[$target])) {
                $target = $builtin ? $defaults[$id]['target'] : 'center';
            }
            $selector = isset($row['selector']) ? self::clean_selector($row['selector']) : '';
            if ('custom' === $target && '' === $selector) {
                $target = 'center';
            }
            $seen[$id] = true;
            $out[] = [
                'id'        => $id,
                'enabled'   => !empty($row['enabled']) ? 1 : 0,
                'title'     => $title,
                'text'      => $text,
                'target'    => $target,
                'selector'  => 'custom' === $target ? $selector : '',
                'before'    => 'custom' === $target && isset($row['before']) ? self::clean_before($row['before']) : '',
                'placement' => isset($row['placement']) ? self::clean_place($row['placement']) : 'auto',
            ];
        }
        return $out;
    }

    /** Every row (enabled or not) in saved order: built-ins resolved to their text, new built-ins appended. */
    public static function resolved_rows() {
        $defaults = [];
        foreach (self::default_steps() as $d) {
            $defaults[$d['id']] = $d;
        }
        $s     = get_option('wppoppop_settings', []);
        $saved = (is_array($s) && !empty($s['builder_guide_steps'])) ? self::sanitize_custom_steps($s['builder_guide_steps']) : [];
        $rows  = [];
        $used  = [];
        foreach ($saved as $r) {
            $custom = !isset($defaults[$r['id']]);
            if (!$custom) {
                $d = $defaults[$r['id']];
                $used[$r['id']] = true;
                $r['title'] = '' !== $r['title'] ? $r['title'] : $d['title'];
                $r['text']  = '' !== $r['text'] ? $r['text'] : $d['text'];
            }
            $r['custom'] = $custom ? 1 : 0;
            $rows[]      = $r;
        }
        foreach ($defaults as $id => $d) {
            if (!isset($used[$id])) {
                $rows[] = self::default_row($d);
            }
        }
        return $rows;
    }

    /** Defaults merged with the admin's edits (enabled rows only), as step arrays. */
    public static function build_steps() {
        $targets  = self::targets();
        $defaults = [];
        foreach (self::default_steps() as $d) {
            $defaults[$d['id']] = $d;
        }
        $steps = [];
        foreach (self::resolved_rows() as $r) {
            if (empty($r['enabled'])) {
                continue;
            }
            if ('custom' === $r['target']) {
                $sel    = '' !== $r['selector'] ? [$r['selector']] : [];
                $before = $r['before'];
                $place  = $r['placement'];
            } else {
                $t      = isset($targets[$r['target']]) ? $targets[$r['target']] : $targets['center'];
                $sel    = $t['selector'];
                $before = $t['before'];
                $place  = 'auto' === $r['placement'] ? $t['placement'] : $r['placement'];
            }
            $empty = '';
            if (isset($defaults[$r['id']]) && $r['target'] === $defaults[$r['id']]['target'] && $r['text'] === $defaults[$r['id']]['text'] && isset($defaults[$r['id']]['empty'])) {
                $empty = $defaults[$r['id']]['empty'];
            }
            $steps[] = [
                'id' => $r['id'], 'selector' => $sel, 'placement' => $place, 'before' => $before,
                'title' => $r['title'], 'text' => $r['text'], 'empty' => $empty,
            ];
        }
        return $steps;
    }

    /** Steps sent to the builder: defaults -> admin edits -> wppoppop_guide_steps filter (Pro / themes). */
    public static function steps() {
        return self::sanitize_steps(apply_filters('wppoppop_guide_steps', self::build_steps()));
    }

    /** Guide is on and has at least one enabled step. */
    public static function active() {
        return self::enabled() && (bool) self::steps();
    }

    protected static function clean_selector($sel) {
        $sel = trim((string) $sel);
        if ('' === $sel || strlen($sel) > 200 || !preg_match('/^[\w\-\s#.\[\]="\':>,~+*()^$|]+$/u', $sel)) {
            return '';
        }
        return $sel;
    }

    /** Whitelist every field so a bad filter result can never break or inject into the tour. */
    public static function sanitize_steps($steps) {
        $out     = [];
        $seen    = [];
        $places  = ['auto', 'top', 'bottom', 'left', 'right', 'center'];
        foreach ((array) $steps as $i => $s) {
            if (!is_array($s) || count($out) >= 40) {
                continue;
            }
            $title = isset($s['title']) ? sanitize_text_field($s['title']) : '';
            $text  = isset($s['text']) ? sanitize_text_field($s['text']) : '';
            if ('' === $title || '' === $text) {
                continue;
            }
            $id = isset($s['id']) ? sanitize_key($s['id']) : '';
            if ('' === $id || isset($seen[$id])) {
                $id = 'step-' . (count($out) + 1);
            }
            $seen[$id] = true;

            $sels = [];
            if (isset($s['selector'])) {
                foreach ((array) $s['selector'] as $one) {
                    $one = self::clean_selector($one);
                    if ('' !== $one) {
                        $sels[] = $one;
                    }
                }
            }
            $before = isset($s['before']) ? (string) $s['before'] : '';
            if (!preg_match('/^(open-inspector|open-settings|inspector-tab:(basic|style|logic|data|advanced))?$/', $before)) {
                $before = '';
            }
            $place = (isset($s['placement']) && in_array($s['placement'], $places, true)) ? $s['placement'] : 'auto';

            $out[] = [
                'id'        => $id,
                'selector'  => $sels,
                'placement' => $sels ? $place : 'center',
                'before'    => $before,
                'title'     => mb_substr($title, 0, 120),
                'text'      => mb_substr($text, 0, 400),
                'empty'     => isset($s['empty']) ? mb_substr(sanitize_text_field($s['empty']), 0, 400) : '',
            ];
        }
        return $out;
    }

    public static function payload() {
        return [
            'enabled'   => 1,
            'autostart' => self::should_autostart() ? 1 : 0,
            'state'     => self::get_state(),
            'nonce'     => self::nonce(),
            'ajax_url'  => admin_url('admin-ajax.php'),
            'steps'     => self::steps(),
            'i18n'      => [
                /* translators: 1: current step number, 2: total steps */
                'step_of'   => __('Step {n} of {total}', 'wppoppop'),
                'next'      => __('Next', 'wppoppop'),
                'back'      => __('Back', 'wppoppop'),
                'finish'    => __('Finish', 'wppoppop'),
                'skip'      => __('Skip', 'wppoppop'),
                'dont_show' => __("Don't show again", 'wppoppop'),
                'close'     => __('Close guide', 'wppoppop'),
            ],
        ];
    }

    /** Builder page only (fired by WpPopPop_Admin_Assets). */
    public function enqueue() {
        if (!self::enabled()) {
            return;
        }
        $ver = function ($rel) {
            $f = WPPOPPOP_PATH . $rel;
            return file_exists($f) ? (string) filemtime($f) : (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1');
        };
        wp_enqueue_style('wppoppop-builder-guide-css', WPPOPPOP_URL . 'admin/css/builder/guide.css', ['wppoppop-builder-css'], $ver('admin/css/builder/guide.css'));
        wp_enqueue_script('wppoppop-builder-guide-js', WPPOPPOP_URL . 'admin/js/builder/builder-guide.js', ['wppoppop-builder-js'], $ver('admin/js/builder/builder-guide.js'), true);
        wp_localize_script('wppoppop-builder-guide-js', 'wppoppop_guide_vars', self::payload());
    }
}
