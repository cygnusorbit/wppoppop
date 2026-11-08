<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Layer Settings catalogue: every Inspector setting (Content / Style / Logic / Data / Advanced)
 * per element type, with its default visibility and an enable / disable override.
 *
 * Storage: wppoppop_settings['builder_layer_settings'][type][tab][key] = 0|1
 * Only values that differ from the element's default are stored.
 */
class WpPopPop_Layer_Settings {

    const OPTION_KEY = 'builder_layer_settings';

    public static function tabs() {
        return array(
            'basic'    => __('Content', 'wppoppop'),
            'style'    => __('Style', 'wppoppop'),
            'logic'    => __('Logic', 'wppoppop'),
            'data'     => __('Data', 'wppoppop'),
            'advanced' => __('Advanced', 'wppoppop'),
        );
    }

    public static function elements() {
        return array(
            'shape'       => __('Rectangle / Shape', 'wppoppop'),
            'image'       => __('Image', 'wppoppop'),
            'video'       => __('Video Embed', 'wppoppop'),
            'label'       => __('Label / Text', 'wppoppop'),
            'html'        => __('HTML Content', 'wppoppop'),
            'close'       => __('Close Icon', 'wppoppop'),
            'icon'        => __('Font Awesome Icon', 'wppoppop'),
            'progress'    => __('Progress Bar', 'wppoppop'),
            'button'      => __('Button / Link', 'wppoppop'),
            'text'        => __('Text Input', 'wppoppop'),
            'email'       => __('Email Address', 'wppoppop'),
            'number'      => __('Number Input', 'wppoppop'),
            'phone'       => __('Phone / Tel', 'wppoppop'),
            'textarea'    => __('Textarea', 'wppoppop'),
            'select'      => __('Select Dropdown', 'wppoppop'),
            'checkbox'    => __('Agreement Checkbox', 'wppoppop'),
            'checkboxgroup' => __('Checkbox', 'wppoppop'),
            'radio'       => __('Radio Button', 'wppoppop'),
            'multiselect' => __('Multiselect', 'wppoppop'),
            'imageselect' => __('Image Select', 'wppoppop'),
            'tile'        => __('Tile Select', 'wppoppop'),
            'date'        => __('Date Picker', 'wppoppop'),
            'time'        => __('Time Picker', 'wppoppop'),
            'upload'      => __('File Upload', 'wppoppop'),
            'password'    => __('Password', 'wppoppop'),
            'rating'      => __('Star Rating', 'wppoppop'),
            'submit'      => __('Submit Button', 'wppoppop'),
        );
    }

    public static function form_types() {
        return array('text', 'email', 'number', 'phone', 'textarea', 'select', 'checkbox', 'checkboxgroup', 'radio', 'multiselect', 'imageselect', 'tile', 'date', 'time', 'upload', 'password', 'rating');
    }

    /** Element types that keep the stock Style layout of the Inspector (mirrors the builder's settled layout). */
    private static function style_layout() {
        $form_base   = 'formModule alignCard iconsCard';
        $form_choice = 'formModule alignCard';
        return array(
            'shape'         => 'padding shadow',
            'image'         => 'opacity padding border shadow',
            'video'         => 'padding border shadow',
            'label'         => 'textAlign bg typo padding border shadow',
            'html'          => 'textAlign bg padding border shadow',
            'close'         => 'bg border hover',
            'icon'          => 'opacity bg border',
            'progress'      => 'opacity bg typo padding border shadow css',
            'button'        => 'bg typo padding border shadow hover',
            'submit'        => 'formModule iconsCard submitAlign bg typo shadow hover css',
            'text'          => $form_base,
            'email'         => $form_base,
            'number'        => $form_base,
            'phone'         => $form_base,
            'select'        => $form_base,
            'textarea'      => $form_base,
            'date'          => $form_base . ' typo border',
            'time'          => $form_base . ' typo border',
            'upload'        => $form_base . ' typo border',
            'password'      => $form_base . ' typo border',
            'checkbox'      => $form_choice . ' bg typo shadow',
            'radio'         => $form_choice . ' typo',
            'checkboxgroup' => $form_choice . ' typo',
            'multiselect'   => $form_choice . ' typo',
            'imageselect'   => $form_choice . ' typo',
            'tile'          => $form_choice . ' bg typo hover',
            'rating'        => $form_choice,
        );
    }

    private static function style_native($key) {
        $out = array();
        foreach (self::style_layout() as $type => $list) {
            if (in_array($key, explode(' ', $list), true)) {
                $out[] = $type;
            }
        }
        return $out;
    }

    private static function entry($key, $label, $sel, $native, $display = 'block', $rows = array(), $per_type = false) {
        return array(
            'key'      => $key,
            'label'    => $label,
            'sel'      => $sel,
            'native'   => $native,
            'd'        => $display,
            'rows'     => $rows,
            'per_type' => $per_type,
        );
    }

    /** tab => list of entries (an entry may carry child rows). */
    public static function catalogue() {
        static $cat = null;
        if ($cat !== null) {
            return $cat;
        }
        $all  = array_keys(self::elements());
        $form = self::form_types();
        $form_no_rating = array_values(array_diff($form, array('rating')));

        $cat = array();

        $cat['basic'] = array(
            self::entry('layer_name', __('Layer Name', 'wppoppop'), '#insp-section-layer-name', $all),
            self::entry('element_settings', '', '#panel-elem-{type}', $all, 'block', array(), true),
            self::entry('animation', __('Animation (Appearance / Disappearance / Duration / Start Delay)', 'wppoppop'), '#insp-row-gp-animation', $all),
            self::entry('required', __('Required Switch', 'wppoppop'), '#insp-row-gp-required', $form, 'flex'),
            self::entry('required_msg', __('Required Error Message', 'wppoppop'), '#insp-row-gp-required-msg', $form),
            self::entry('autocomplete', __('Autocomplete Attribute', 'wppoppop'), '#insp-row-gp-autocomplete', array()),
            self::entry('coords', __('Coordinates & Bounds (X / Y / Width / Height)', 'wppoppop'), '#insp-section-coords, #insp-section-coords-grid', $all),
        );

        $cat['style'] = array(
            self::entry('opacity', __('Opacity & Visibility', 'wppoppop'), '#insp-section-opacity', self::style_native('opacity')),
            self::entry('bg', __('Background Color', 'wppoppop'), '#insp-section-bg-color', self::style_native('bg')),
            self::entry('typo', __('Typography & Spacing', 'wppoppop'), '#insp-section-typography', self::style_native('typo')),
            self::entry('padding', __('Padding (Top / Right / Bottom / Left PX)', 'wppoppop'), '#insp-section-padding', self::style_native('padding')),
            self::entry('border', __('Border & Corner Radius', 'wppoppop'), '#insp-section-border', self::style_native('border')),
            self::entry('shadow', __('Box Shadow', 'wppoppop'), '#insp-section-shadow', self::style_native('shadow')),
            self::entry('hover', __('Hover State', 'wppoppop'), '#prop-btn-hover-section', self::style_native('hover')),
            self::entry('css', __('Custom CSS Class & Inline Styles', 'wppoppop'), '#insp-section-custom-css', self::style_native('css')),
            self::entry('textAlign', __('Text Alignment', 'wppoppop'), '#insp-section-text-align', self::style_native('textAlign')),
            self::entry('submitAlign', __('Submit Text Alignment', 'wppoppop'), '#insp-section-submit-text-align', self::style_native('submitAlign')),
            self::entry('formModule', __('Input Styling Module', 'wppoppop'), '#wppoppop-form-style-module', self::style_native('formModule'), 'block', array(
                self::entry('alignCard', __('Input Text Alignment', 'wppoppop'), '#wppoppop-form-style-module > .wppoppop-insp-card:not(#wppoppop-form-icons-card)', self::style_native('alignCard')),
                self::entry('iconsCard', __('Input Icons & Offsets', 'wppoppop'), '#wppoppop-form-icons-card', self::style_native('iconsCard')),
            )),
        );

        $cat['logic'] = array(
            self::entry('conditional_logic', __('Conditional Logic', 'wppoppop'), '#insp-section-conditional-logic', $all),
            self::entry('action_routing', __('Action & Click Routing', 'wppoppop'), '#insp-section-action-routing', $all, 'block', array(
                self::entry('click_action', __('Click Action', 'wppoppop'), '#insp-row-logic-click-action', $all),
                self::entry('link_url', __('Destination URL', 'wppoppop'), '#prop-logic-link-url-row', $all),
                self::entry('goto_canvas', __('Target Canvas #', 'wppoppop'), '#prop-logic-goto-canvas-row', $all),
                self::entry('custom_js', __('Custom JS Callback (OnClick)', 'wppoppop'), '#insp-row-logic-custom-js', $all),
            )),
        );

        $cat['data'] = array(
            self::entry('rating_data', __('Rating Data', 'wppoppop'), '#insp-rating-data', array('rating')),
            self::entry('data_binding', __('Data & Value Binding', 'wppoppop'), '#insp-section-form-data', $form_no_rating, 'block', array(
                self::entry('default_value', __('Default Value', 'wppoppop'), '#insp-row-data-default', $form_no_rating),
                self::entry('dynamic_default', __('Dynamic Default (URL / Cookie)', 'wppoppop'), '#insp-row-data-dynamic', $form_no_rating, 'flex'),
                self::entry('dynamic_param', __('URL Parameter / Cookie Name', 'wppoppop'), '#prop-dynamic-param-row', $form_no_rating),
                self::entry('read_only', __('Read Only Field', 'wppoppop'), '#insp-row-data-readonly', $form_no_rating, 'flex'),
                self::entry('save_to_db', __('Save to Database', 'wppoppop'), '#insp-row-data-save', $form_no_rating, 'flex'),
            )),
        );

        $cat['advanced'] = array(
            self::entry('field_identifier', __('Field Identifier', 'wppoppop'), '#insp-section-form-advanced', $form, 'block', array(
                self::entry('field_name', __('ID / Merge Token Name', 'wppoppop'), '#insp-row-adv-field-name', $form),
                self::entry('input_mask', __('Input Mask', 'wppoppop'), '#insp-row-adv-input-mask', array()),
            )),
            self::entry('filters', __('Sanitization Filters', 'wppoppop'), '#insp-section-form-filters', $form),
            self::entry('validators', __('Validation Rules', 'wppoppop'), '#insp-section-form-validators', $form, 'block', array(
                self::entry('validator_rules', __('Validator Checklist', 'wppoppop'), '#insp-row-val-rules', $form),
                self::entry('validator_equal', __('Equal Match Value', 'wppoppop'), '#wppoppop-val-extra-equal', $form),
                self::entry('validator_inarray', __('In Array Allowed Values', 'wppoppop'), '#wppoppop-val-extra-inarray', $form),
                self::entry('validator_error', __('Custom Validation Error Message', 'wppoppop'), '#insp-row-val-error', $form),
            )),
        );

        return $cat;
    }

    /** Flatten entries and their child rows (children carry a parent key). */
    public static function flat($entries) {
        $out = array();
        foreach ($entries as $e) {
            $e['parent'] = '';
            $out[] = $e;
            foreach ($e['rows'] as $r) {
                $r['parent'] = $e['key'];
                $out[] = $r;
            }
        }
        return $out;
    }

    /** Label of an entry for one element type. */
    public static function entry_label($entry, $type) {
        if (!empty($entry['per_type'])) {
            $els = self::elements();
            return sprintf(
                /* translators: %s: element name, e.g. Text Input */
                __('%s Settings', 'wppoppop'),
                isset($els[$type]) ? $els[$type] : $type
            );
        }
        return $entry['label'];
    }

    public static function default_on($type, $entry) {
        return in_array($type, $entry['native'], true);
    }

    public static function get_overrides() {
        $s = get_option('wppoppop_settings', array());
        $raw = (is_array($s) && isset($s[self::OPTION_KEY])) ? $s[self::OPTION_KEY] : array();
        return self::sanitize($raw);
    }

    /** Accepts an array or a JSON string; returns only values that differ from the defaults. */
    public static function sanitize($raw) {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : array();
        }
        if (!is_array($raw)) {
            return array();
        }
        $out = array();
        $cat = self::catalogue();
        foreach (array_keys(self::elements()) as $type) {
            if (empty($raw[$type]) || !is_array($raw[$type])) {
                continue;
            }
            foreach ($cat as $tab => $entries) {
                if (empty($raw[$type][$tab]) || !is_array($raw[$type][$tab])) {
                    continue;
                }
                foreach (self::flat($entries) as $e) {
                    if (!array_key_exists($e['key'], $raw[$type][$tab])) {
                        continue;
                    }
                    $v   = filter_var($raw[$type][$tab][$e['key']], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    $def = self::default_on($type, $e) ? 1 : 0;
                    if ($v !== $def) {
                        $out[$type][$tab][$e['key']] = $v;
                    }
                }
            }
        }
        return $out;
    }

    /** Effective state of a setting for an element type. */
    public static function is_on($type, $tab, $entry, $overrides) {
        if (isset($overrides[$type][$tab]) && array_key_exists($entry['key'], $overrides[$type][$tab])) {
            return !empty($overrides[$type][$tab][$entry['key']]);
        }
        return self::default_on($type, $entry);
    }

    /** Data passed to the builder (wppoppop_vars.layer_settings). */
    public static function builder_payload() {
        $ov   = self::get_overrides();
        $cat  = self::catalogue();
        $sel  = array();
        $tabs = array();
        foreach ($cat as $tab => $entries) {
            foreach (self::flat($entries) as $e) {
                $sel[$tab][$e['key']] = array('s' => $e['sel'], 'd' => $e['d']);
            }
        }
        foreach (array_keys(self::elements()) as $type) {
            foreach ($cat as $tab => $entries) {
                $on = false;
                foreach ($entries as $e) {
                    if (self::is_on($type, $tab, $e, $ov)) {
                        $on = true;
                        break;
                    }
                }
                $tabs[$type][$tab] = $on;
            }
        }
        return array(
            'overrides' => empty($ov) ? new stdClass() : $ov,
            'selectors' => $sel,
            'tabs'      => $tabs,
        );
    }
}
