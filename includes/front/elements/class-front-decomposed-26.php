<?php
if (!defined('ABSPATH')) exit;

/**
 * Decomposed 26-Element Frontend Renderer Adapter
 * Renders visitor HTML + Data/Advanced/Logic attributes for newly standardized elements.
 */
class WpPopPop_Front_Decomposed_26 {

    public static function resolve_wp_tokens(string $content): string {
        if (strpos($content, '{{') === false) return $content;
        $user = function_exists('wp_get_current_user') ? wp_get_current_user() : null;
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
        $page_url = isset($_SERVER['REQUEST_URI']) ? home_url(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']))) : home_url('/');
        $values = array(
            'record-id'     => '',
            'ip'            => $ip,
            'user-agent'    => $ua,
            'date'          => function_exists('wp_date') ? wp_date(get_option('date_format')) : date_i18n(get_option('date_format')),
            'time'          => function_exists('wp_date') ? wp_date(get_option('time_format')) : date_i18n(get_option('time_format')),
            'wp-user-login' => ($user && $user->exists()) ? $user->user_login : '',
            'wp-user-email' => ($user && $user->exists()) ? $user->user_email : '',
            'url'           => $page_url,
            'page-title'    => wp_strip_all_tags(get_the_title()),
        );
        $map = array();
        foreach ($values as $slug => $val) {
            $fmt_attr = '';
            if ($slug === 'date') { $fmt_attr = ' data-format="' . esc_attr(get_option('date_format')) . '"'; }
            if ($slug === 'time') { $fmt_attr = ' data-format="' . esc_attr(get_option('time_format')) . '"'; }
            $map['{{' . $slug . '}}'] = '<span class="wppoppop-const wppoppop-const-' . $slug . '" data-token="' . esc_attr($slug) . '"' . $fmt_attr . '>' . esc_html($val) . '</span>';
        }
        return strtr($content, $map);
    }

    public static function build_data_attrs(array $el): string {
        $attrs = '';
        if (!empty($el['dynamicDefault']) && !empty($el['paramName'])) {
            $attrs .= ' data-dynamic-default="true" data-param-name="' . esc_attr($el['paramName']) . '"';
        }
        if (!empty($el['readOnly'])) {
            $attrs .= ' readonly="readonly" data-readonly="true"';
        }
        if (isset($el['saveToDb']) && !$el['saveToDb']) {
            $attrs .= ' data-save-db="false"';
        }
        if (!empty($el['inputMask'])) {
            $attrs .= ' data-input-mask="' . esc_attr($el['inputMask']) . '"';
        }
        if (!empty($el['filters']) && is_array($el['filters'])) {
            $attrs .= ' data-filters="' . esc_attr(implode(',', $el['filters'])) . '"';
        }
        if (!empty($el['validators']) && is_array($el['validators'])) {
            $attrs .= ' data-validators="' . esc_attr(implode(',', $el['validators'])) . '"';
        }
        if (isset($el['errorMessage']) && $el['errorMessage'] !== '') {
            $attrs .= ' data-invalid-msg="' . esc_attr($el['errorMessage']) . '"';
        }
        if (isset($el['validatorEqual']) && $el['validatorEqual'] !== '') {
            $attrs .= ' data-equal-value="' . esc_attr($el['validatorEqual']) . '"';
        }
        if (!empty($el['validatorList'])) {
            $attrs .= ' data-in-list="' . esc_attr($el['validatorList']) . '"';
        }
        if (!empty($el['required'])) {
            $attrs .= ' required="required" aria-required="true"';
        }
        if (!empty($el['required']) && !empty($el['requiredMessage'])) {
            $attrs .= ' data-error-msg="' . esc_attr($el['requiredMessage']) . '"';
        }
        if (!empty($el['blockDisposable']) && $el['blockDisposable'] !== 'false' && $el['blockDisposable'] !== '0') {
            $attrs .= ' data-block-disposable="1"';
        }
        if (!empty($el['autocomplete'])) {
            $attrs .= ' autocomplete="' . esc_attr($el['autocomplete']) . '"';
        }
        if (!empty($el['maxLength']) && intval($el['maxLength']) > 0) {
            $attrs .= ' maxlength="' . intval($el['maxLength']) . '"';
        }
        if (!empty($el['minLength']) && intval($el['minLength']) > 0) {
            $attrs .= ' minlength="' . intval($el['minLength']) . '"';
        }
        return $attrs;
    }

    /* WPPOPPOP_ICON_COLOR: applies the per-layer left/right icon colour to every rendered input icon */
    public static function render_element(string $type, array $el): ?string {
        $html = self::render_element_raw($type, $el);
        if ($html === null || $html === '') { return $html; }
        foreach (array('left' => 'iconLeftColor', 'right' => 'iconRightColor') as $side => $key) {
            $c = isset($el[$key]) ? trim((string) $el[$key]) : '';
            if (($c === '' || !preg_match('/^#[0-9a-fA-F]{3,8}$/', $c)) && !empty($el['fgColor'])) { $c = trim((string) $el['fgColor']); } /* WPPOPPOP_FG_COLOR */
            if ($c === '' || !preg_match('/^#[0-9a-fA-F]{3,8}$/', $c)) { continue; }
            $html = preg_replace_callback('/<i\b[^>]*style="position:absolute;' . $side . ':[^"]*"[^>]*>/', function ($m) use ($c) {
                $count = 0;
                $out = preg_replace('/color:#[0-9a-fA-F]{3,8}/', 'color:' . $c, $m[0], 1, $count);
                if ($count === 0) { $out = str_replace('position:absolute;', 'position:absolute;color:' . $c . ';', $m[0]); }
                return $out;
            }, $html);
        }
        return $html;
    }

    /* WPPOPPOP_LABEL_TYPO_FRONT */
    private static function label_typo(array $el): string {
        $al = isset($el['textAlign']) ? strtolower((string) $el['textAlign']) : 'left';
        if (!in_array($al, array('left', 'center', 'right', 'justify'), true)) { $al = 'left'; }
        $j = ($al === 'center') ? 'center' : (($al === 'right') ? 'flex-end' : 'flex-start');
        $o = 'justify-content:' . $j . ';text-align:' . $al . ';';
        if (!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) { $o .= 'font-weight:' . $el['fontWeight'] . ';'; }
        if (!empty($el['lineHeight']) && is_numeric($el['lineHeight'])) { $o .= 'line-height:' . floatval($el['lineHeight']) . ';'; }
        if (isset($el['letterSpacing']) && is_numeric($el['letterSpacing']) && floatval($el['letterSpacing']) != 0) { $o .= 'letter-spacing:' . floatval($el['letterSpacing']) . 'px;'; }
        return $o;
    }

    public static function render_element_raw(string $type, array $el): ?string {
        $t = strtolower(trim($type));
        $field_name = !empty($el['fieldName']) ? sanitize_key($el['fieldName']) : (!empty($el['token']) ? sanitize_key($el['token']) : $t);
        $field_id = !empty($el['fieldId']) ? sanitize_html_class($el['fieldId']) : 'wppoppop-field-' . esc_attr($el['id'] ?? uniqid());
        $default_val = isset($el['defaultValue']) ? (string) $el['defaultValue'] : '';
        $placeholder = isset($el['placeholder']) ? (string) $el['placeholder'] : '';
        $data_attrs = self::build_data_attrs($el);

        switch ($t) {
            case 'label':
                $allowed_tags = array('div', 'p', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6');
                $tag = (!empty($el['tag']) && in_array(strtolower($el['tag']), $allowed_tags, true)) ? strtolower($el['tag']) : 'div';
                $tf = !empty($el['textTransform']) ? 'text-transform:' . esc_attr($el['textTransform']) . ';' : '';
                $content = isset($el['content']) ? wp_kses_post($el['content']) : '';
                $content = self::resolve_wp_tokens($content);
                $label_url = !empty($el['url']) ? esc_url($el['url']) : '';
                if ($label_url !== '') {
                    $blank_on = !empty($el['targetBlank']) && $el['targetBlank'] !== 'false';
                    $content = '<a class="wppoppop-label-link" href="' . $label_url . '"' . ($blank_on ? ' target="_blank" rel="noopener noreferrer"' : '') . ' style="color:inherit;text-decoration:inherit;display:block;width:100%;">' . $content . '</a>';
                }
                return '<' . $tag . ' class="wppoppop-front-label" style="margin:0;padding:0;box-sizing:border-box;width:100%;height:100%;display:flex;align-items:center;' . self::label_typo($el) . $tf . '">' . $content . '</' . $tag . '>';

            case 'button':
                $btn_label = isset($el['content']) && $el['content'] !== '' ? esc_html($el['content']) : 'Click Here';
                $action = !empty($el['buttonAction']) ? esc_attr($el['buttonAction']) : 'link';
                $url = !empty($el['linkUrl']) ? esc_url($el['linkUrl']) : (!empty($el['url']) ? esc_url($el['url']) : '#');
                $step = !empty($el['goto_canvas']) ? intval($el['goto_canvas']) : 2;
                $blank = !empty($el['linkBlank']) ? ' target="_blank" rel="noopener noreferrer"' : '';
                if ($action === 'step') {
                    return '<button type="button" class="wppoppop-front-btn wppoppop-step-trigger" data-goto-canvas="' . $step . '" data-goto-screen="' . $step . '" style="width:100%;height:100%;cursor:pointer;">' . $btn_label . '</button>';
                }
                if ($action === 'close') {
                    return '<button type="button" class="wppoppop-front-btn wppoppop-close-trigger" data-close-action="close" style="width:100%;height:100%;cursor:pointer;">' . $btn_label . '</button>';
                }
                return '<a href="' . $url . '" class="wppoppop-front-btn wppoppop-link-btn"' . $blank . ' style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;text-decoration:none;color:inherit;">' . $btn_label . '</a>';

            case 'text':
            case 'email':
                /* WPPOPPOP_EMAIL_MIRROR */ /* WPPOPPOP_TF_FRONT: typeable input + icons + alignment + unique name */
                $tf_val   = $default_val; /* WPPOPPOP_TF_VALUE: never seed from layer content */
                $tf_name  = ($t === 'email') ? $field_name : (($field_name === 'text') ? ('text_' . substr(sanitize_key((string) (isset($el['id']) ? $el['id'] : uniqid())), -6)) : $field_name);
                $tf_nm_attr = ($t === 'email' && $tf_name === 'email') ? 'email' : 'fields[' . esc_attr($tf_name) . ']';
                $tf_align = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $tf_icons = array('left' => array('cls' => '', 'off' => 10), 'right' => array('cls' => '', 'off' => 10));
                foreach (array('iconLeft' => 'left', 'iconRight' => 'right') as $tf_key => $tf_side) {
                    $tf_raw = isset($el[$tf_key]) ? trim((string) $el[$tf_key]) : '';
                    if ($tf_raw === '') { continue; }
                    $tf_tokens = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', $tf_raw))));
                    if (empty($tf_tokens)) { continue; }
                    if (strpos($tf_tokens[0], 'fa-') === 0) { array_unshift($tf_tokens, 'fas'); }
                    elseif (strpos($tf_tokens[0], 'dashicons-') === 0) { array_unshift($tf_tokens, 'dashicons'); }
                    $tf_off_key = $tf_key . 'Offset';
                    $tf_icons[$tf_side]['cls'] = implode(' ', $tf_tokens);
                    $tf_icons[$tf_side]['off'] = (isset($el[$tf_off_key]) && $el[$tf_off_key] !== '') ? max(0, intval($el[$tf_off_key])) : 10;
                }
                static $tf_fa_css = false;
                $tf_css = '';
                if (!$tf_fa_css && ($tf_icons['left']['cls'] !== '' || $tf_icons['right']['cls'] !== '')) {
                    $tf_fa_css = true;
                    $tf_css = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />';
                }
                $tf_pl = ($tf_icons['left']['cls'] !== '') ? ($tf_icons['left']['off'] + 22) : 12;
                $tf_pr = ($tf_icons['right']['cls'] !== '') ? ($tf_icons['right']['off'] + 22) : 12;
                $tf_ico = function ($side) use ($tf_icons) {
                    if ($tf_icons[$side]['cls'] === '') { return ''; }
                    return '<i class="' . esc_attr($tf_icons[$side]['cls']) . '" aria-hidden="true" style="position:absolute;' . $side . ':' . intval($tf_icons[$side]['off']) . 'px;top:50%;transform:translateY(-50%);color:#64748b;font-size:14px;line-height:1;pointer-events:none;"></i>';
                };
                return $tf_css . '<div class="wppoppop-tf-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                    . $tf_ico('left')
                    . '<input type="' . ($t === 'email' ? 'email' : 'text') . '" id="' . $field_id . '" name="' . $tf_nm_attr . '" data-field-name="' . esc_attr($tf_name) . '" value="' . esc_attr($tf_val) . '" placeholder="' . esc_attr($placeholder ?: ($t === 'email' ? 'Enter your email...' : 'Enter text...')) . '" class="wppoppop-form-input wppoppop-input-text' . ($t === 'email' ? ' wppoppop-field-email' : '') . '"' . $data_attrs
                    . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 ' . $tf_pr . 'px 0 ' . $tf_pl . 'px;text-align:' . esc_attr($tf_align) . ';pointer-events:auto;-webkit-user-select:text;user-select:text;" />'
                    . $tf_ico('right')
                    . '</div>';

            case 'number':
                /* WPPOPPOP_NUMBER_FRONT: typeable number input + icons + alignment + bounds + unit suffix */
                $nm_align = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $nm_icons = array('left' => array('cls' => '', 'off' => 10), 'right' => array('cls' => '', 'off' => 10));
                foreach (array('iconLeft' => 'left', 'iconRight' => 'right') as $nm_key => $nm_side) {
                    $nm_raw = isset($el[$nm_key]) ? trim((string) $el[$nm_key]) : '';
                    if ($nm_raw === '') { continue; }
                    $nm_tokens = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', $nm_raw))));
                    if (empty($nm_tokens)) { continue; }
                    if (strpos($nm_tokens[0], 'fa-') === 0) { array_unshift($nm_tokens, 'fas'); }
                    elseif (strpos($nm_tokens[0], 'dashicons-') === 0) { array_unshift($nm_tokens, 'dashicons'); }
                    $nm_off_key = $nm_key . 'Offset';
                    $nm_icons[$nm_side]['cls'] = implode(' ', $nm_tokens);
                    $nm_icons[$nm_side]['off'] = (isset($el[$nm_off_key]) && $el[$nm_off_key] !== '') ? max(0, intval($el[$nm_off_key])) : 10;
                }
                static $nm_fa_css = false;
                $nm_css = '';
                if (!$nm_fa_css && ($nm_icons['left']['cls'] !== '' || $nm_icons['right']['cls'] !== '')) {
                    $nm_fa_css = true;
                    $nm_css = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />';
                }
                $nm_unit = isset($el['unitSuffix']) ? trim((string) $el['unitSuffix']) : '';
                $nm_pl = ($nm_icons['left']['cls'] !== '') ? ($nm_icons['left']['off'] + 22) : 12;
                $nm_pr = ($nm_icons['right']['cls'] !== '') ? ($nm_icons['right']['off'] + 22) : 12;
                if ($nm_unit !== '') { $nm_pr += 30 + (strlen($nm_unit) * 8); }
                $nm_ico = function ($side) use ($nm_icons) {
                    if ($nm_icons[$side]['cls'] === '') { return ''; }
                    return '<i class="' . esc_attr($nm_icons[$side]['cls']) . '" aria-hidden="true" style="position:absolute;' . $side . ':' . intval($nm_icons[$side]['off']) . 'px;top:50%;transform:translateY(-50%);color:#64748b;font-size:14px;line-height:1;pointer-events:none;"></i>';
                };
                $nm_b = array();
                foreach (array('min' => array('minValue', 'min'), 'max' => array('maxValue', 'max'), 'step' => array('stepValue', 'step')) as $nm_attr => $nm_srcs) {
                    foreach ($nm_srcs as $nm_s) {
                        if (isset($el[$nm_s]) && $el[$nm_s] !== '' && is_numeric($el[$nm_s])) { $nm_b[$nm_attr] = $el[$nm_s]; break; }
                    }
                }
                if (!isset($nm_b['step']) || floatval($nm_b['step']) <= 0) { $nm_b['step'] = 1; }
                $nm_bounds = '';
                foreach ($nm_b as $nm_k => $nm_v) { $nm_bounds .= ' ' . $nm_k . '="' . esc_attr($nm_v) . '"'; }
                $nm_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength)="[^"]*"/', '', $data_attrs);
                $nm_unit_html = ($nm_unit !== '') ? '<span class="wppoppop-num-unit" style="position:absolute;right:26px;top:50%;transform:translateY(-50%);color:#64748b;font-size:12px;pointer-events:none;">' . esc_html($nm_unit) . '</span>' : '';
                return $nm_css . '<div class="wppoppop-tf-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                    . $nm_ico('left')
                    . '<input type="number" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($default_val) . '" placeholder="' . esc_attr($placeholder !== '' ? $placeholder : 'Enter number...') . '"' . $nm_bounds . ' class="wppoppop-form-input wppoppop-input-number"' . $nm_data
                    . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 ' . $nm_pr . 'px 0 ' . $nm_pl . 'px;text-align:' . esc_attr($nm_align) . ';pointer-events:auto;-webkit-user-select:text;user-select:text;" />'
                    . $nm_ico('right') . $nm_unit_html
                    . '</div>';

            case 'phone':
            case 'tel':
                /* WPPOPPOP_PHONE_FRONT: typeable tel input + dial code + icons + alignment + format mask */
                $ph_align = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $ph_icons = array('left' => array('cls' => '', 'off' => 10), 'right' => array('cls' => '', 'off' => 10));
                foreach (array('iconLeft' => 'left', 'iconRight' => 'right') as $ph_key => $ph_side) {
                    $ph_raw = isset($el[$ph_key]) ? trim((string) $el[$ph_key]) : '';
                    if ($ph_raw === '') { continue; }
                    $ph_tokens = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', $ph_raw))));
                    if (empty($ph_tokens)) { continue; }
                    if (strpos($ph_tokens[0], 'fa-') === 0) { array_unshift($ph_tokens, 'fas'); }
                    elseif (strpos($ph_tokens[0], 'dashicons-') === 0) { array_unshift($ph_tokens, 'dashicons'); }
                    $ph_off_key = $ph_key . 'Offset';
                    $ph_icons[$ph_side]['cls'] = implode(' ', $ph_tokens);
                    $ph_icons[$ph_side]['off'] = (isset($el[$ph_off_key]) && $el[$ph_off_key] !== '') ? max(0, intval($el[$ph_off_key])) : 10;
                }
                static $ph_fa_css = false;
                $ph_css = '';
                if (!$ph_fa_css && ($ph_icons['left']['cls'] !== '' || $ph_icons['right']['cls'] !== '')) {
                    $ph_fa_css = true;
                    $ph_css = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />';
                }
                $ph_pl = (($ph_icons['left']['cls'] !== '') ? ($ph_icons['left']['off'] + 22) : 12);
                $ph_pr = ($ph_icons['right']['cls'] !== '') ? ($ph_icons['right']['off'] + 22) : 12;
                $ph_ico = function ($side, $shift) use ($ph_icons) {
                    if ($ph_icons[$side]['cls'] === '') { return ''; }
                    $pos = ($side === 'left') ? ($ph_icons[$side]['off'] + $shift) : $ph_icons[$side]['off'];
                    return '<i class="' . esc_attr($ph_icons[$side]['cls']) . '" aria-hidden="true" style="position:absolute;' . $side . ':' . intval($pos) . 'px;top:50%;transform:translateY(-50%);color:#64748b;font-size:14px;line-height:1;pointer-events:none;"></i>';
                };
                $ph_data = $data_attrs;
                $ph_hint = ($placeholder !== '') ? $placeholder : '(555) 000-0000';
                return $ph_css . '<div class="wppoppop-tf-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                    . $ph_ico('left', 0)
                    . '<input type="tel" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($default_val) . '" placeholder="' . esc_attr($ph_hint) . '" class="wppoppop-form-input wppoppop-input-phone"' . $ph_data
                    . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 ' . $ph_pr . 'px 0 ' . $ph_pl . 'px;text-align:' . esc_attr($ph_align) . ';pointer-events:auto;-webkit-user-select:text;user-select:text;" />'
                    . $ph_ico('right', 0)
                    . '</div>';

            case 'select':
            case 'dropdown':
                /* WPPOPPOP_SELECT_FRONT: real select, first option placeholder, default selection */
                $sl_raw = isset($el['optionsList']) ? (string) $el['optionsList'] : (isset($el['options']) ? (is_array($el['options']) ? implode("\n", $el['options']) : (string) $el['options']) : '');
                $sl_items = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $sl_raw)), 'strlen'));
                if (count($sl_items) === 1 && strpos($sl_items[0], ',') !== false) { $sl_items = array_values(array_filter(array_map('trim', explode(',', $sl_items[0])), 'strlen')); }
                if (empty($sl_items)) { $sl_items = array('Option 1', 'Option 2', 'Option 3'); }
                $sl_first = isset($el['firstOption']) ? trim((string) $el['firstOption']) : '';
                if ($sl_first === '' && $placeholder !== '') { $sl_first = $placeholder; }
                $sl_align = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $sl_opts = '<option value=""' . ($default_val === '' ? ' selected' : '') . '>' . esc_html($sl_first !== '' ? $sl_first : 'Select an option...') . '</option>';
                foreach ($sl_items as $sl_o) {
                    $sl_opts .= '<option value="' . esc_attr($sl_o) . '"' . (($default_val !== '' && $default_val === $sl_o) ? ' selected' : '') . '>' . esc_html($sl_o) . '</option>';
                }
                $sl_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|readonly="readonly")(="[^"]*")?/', '', $data_attrs);
                return '<div class="wppoppop-tf-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                    . '<select id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" class="wppoppop-form-input wppoppop-input-select"' . $sl_data
                    . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 12px;text-align:' . esc_attr($sl_align) . ';pointer-events:auto;background:#ffffff;cursor:pointer;">' . $sl_opts . '</select>'
                    . '</div>';

            case 'checkbox':
            case 'checkboxes':
                /* WPPOPPOP_CHECKBOX_FRONT_V2: value yes, layout, alignment, typography, unchecked by default */
                $cb_raw = isset($el['optionsList']) ? (string) $el['optionsList'] : (isset($el['checkboxLabel']) ? (string) $el['checkboxLabel'] : '');
                $cb_items = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $cb_raw)), 'strlen'));
                if (empty($cb_items)) { $cb_items = array('I agree to the Terms & Privacy Policy'); }
                $cb_lay = strtolower((string) (isset($el['layout']) ? $el['layout'] : (isset($el['checkboxLayout']) ? $el['checkboxLayout'] : 'vertical')));
                $cb_row = in_array($cb_lay, array('horizontal', 'inline', 'row'), true);
                $cb_al = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $cb_j = ($cb_al === 'center') ? 'center' : (($cb_al === 'right') ? 'flex-end' : 'flex-start');
                $cb_terms = isset($el['termsUrl']) ? esc_url(trim((string) $el['termsUrl'])) : '';
                $cb_checked = !empty($el['checkedDefault']) && $el['checkedDefault'] !== 'false' && $el['checkedDefault'] !== '0';
                $cb_multi = count($cb_items) > 1;
                $cb_ty = '';
                $cb_ty .= 'font-size:' . ((!empty($el['fontSize']) && is_numeric($el['fontSize'])) ? intval($el['fontSize']) : 12) . 'px;';
                if (!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) { $cb_ty .= 'font-weight:' . $el['fontWeight'] . ';'; }
                if (!empty($el['lineHeight']) && is_numeric($el['lineHeight'])) { $cb_ty .= 'line-height:' . floatval($el['lineHeight']) . ';'; }
                if (isset($el['letterSpacing']) && is_numeric($el['letterSpacing']) && floatval($el['letterSpacing']) != 0) { $cb_ty .= 'letter-spacing:' . floatval($el['letterSpacing']) . 'px;'; }
                if (!empty($el['color']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['color'])) { $cb_ty .= 'color:' . $el['color'] . ';'; }
                if (!empty($el['fontFamily']) && $el['fontFamily'] !== 'inherit' && preg_match('/^[A-Za-z0-9 ,\'"-]+$/', (string) $el['fontFamily'])) { $cb_ty .= 'font-family:' . esc_attr($el['fontFamily']) . ';'; }
                $cb_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|autocomplete|readonly="readonly")(="[^"]*")?/', '', $data_attrs);
                $cb_html = '';
                foreach ($cb_items as $cb_i => $cb_label) {
                    $cb_id = $field_id . '-' . $cb_i;
                    $cb_text = esc_html($cb_label);
                    if ($cb_terms !== '' && $cb_i === 0) {
                        $cb_text .= ' <a href="' . $cb_terms . '" target="_blank" rel="noopener noreferrer" style="color:#0284c7;text-decoration:underline;">(view)</a>';
                    }
                    $cb_name = $cb_multi ? ('fields[' . esc_attr($field_name) . '][]') : ('fields[' . esc_attr($field_name) . ']');
                    $cb_val  = $cb_multi ? $cb_label : 'yes';
                    $cb_req  = ($cb_multi && $cb_i > 0) ? preg_replace('/\srequired="required"|\saria-required="true"/', '', $cb_data) : $cb_data;
                    $cb_html .= '<label for="' . esc_attr($cb_id) . '" style="display:inline-flex;align-items:center;gap:8px;font-size:inherit;color:inherit;cursor:pointer;line-height:inherit;">'
                        . '<input type="checkbox" id="' . esc_attr($cb_id) . '" name="' . $cb_name . '" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($cb_val) . '" class="wppoppop-form-input wppoppop-input-checkbox"' . ($cb_checked && $cb_i === 0 ? ' checked' : '') . $cb_req
                        . ' style="width:16px;height:16px;margin:0;flex-shrink:0;cursor:pointer;pointer-events:auto;" />'
                        . '<span>' . $cb_text . '</span></label>';
                }
                return '<div class="wppoppop-checkbox-group" style="width:100%;height:100%;display:flex;flex-direction:' . ($cb_row ? 'row' : 'column') . ';flex-wrap:wrap;align-items:' . ($cb_row ? 'center' : $cb_j) . ';justify-content:' . ($cb_row ? $cb_j : 'center') . ';gap:8px;padding:0 6px;box-sizing:border-box;pointer-events:auto;' . $cb_ty . '">' . $cb_html . '</div>';

            case 'radio':
            case 'radios':
                /* WPPOPPOP_RADIO_FRONT: real radio group, layout, unselected by default, required on the group */
                $rd_raw = isset($el['optionsList']) ? (string) $el['optionsList'] : '';
                $rd_items = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $rd_raw)), 'strlen'));
                if (empty($rd_items)) { $rd_items = array('Choice A', 'Choice B', 'Choice C'); }
                $rd_lay = strtolower((string) (isset($el['layout']) ? $el['layout'] : (isset($el['radioLayout']) ? $el['radioLayout'] : 'vertical')));
                $rd_row = in_array($rd_lay, array('horizontal', 'inline', 'row'), true);
                $rd_al = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $rd_j = ($rd_al === 'center') ? 'center' : (($rd_al === 'right') ? 'flex-end' : 'flex-start');
                $rd_ty = 'font-size:' . ((!empty($el['fontSize']) && is_numeric($el['fontSize'])) ? intval($el['fontSize']) : 12) . 'px;';
                if (!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) { $rd_ty .= 'font-weight:' . $el['fontWeight'] . ';'; }
                if (!empty($el['lineHeight']) && is_numeric($el['lineHeight'])) { $rd_ty .= 'line-height:' . floatval($el['lineHeight']) . ';'; }
                if (isset($el['letterSpacing']) && is_numeric($el['letterSpacing']) && floatval($el['letterSpacing']) != 0) { $rd_ty .= 'letter-spacing:' . floatval($el['letterSpacing']) . 'px;'; }
                if (!empty($el['color']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['color'])) { $rd_ty .= 'color:' . $el['color'] . ';'; }
                if (!empty($el['fontFamily']) && $el['fontFamily'] !== 'inherit' && preg_match('/^[A-Za-z0-9 ,\'"-]+$/', (string) $el['fontFamily'])) { $rd_ty .= 'font-family:' . esc_attr($el['fontFamily']) . ';'; }
                $rd_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|autocomplete|readonly="readonly")(="[^"]*")?/', '', $data_attrs);
                $rd_html = '';
                foreach ($rd_items as $rd_i => $rd_item_raw) { /* WPPOPPOP_RADIO_LV_FRONT */
                    $rd_pp = explode('|', (string) $rd_item_raw, 2);
                    $rd_label = trim($rd_pp[0]);
                    $rd_value = (isset($rd_pp[1]) && trim($rd_pp[1]) !== '') ? trim($rd_pp[1]) : $rd_label;
                    if ($rd_label === '') { $rd_label = $rd_value; }
                    $rd_id = $field_id . '-' . $rd_i;
                    $rd_attr = ($rd_i === 0) ? $rd_data : preg_replace('/\srequired="required"|\saria-required="true"|\sdata-error-msg="[^"]*"/', '', $rd_data);
                    $rd_html .= '<label for="' . esc_attr($rd_id) . '" style="display:inline-flex;align-items:center;gap:8px;font-size:inherit;color:inherit;cursor:pointer;line-height:inherit;">'
                        . '<input type="radio" id="' . esc_attr($rd_id) . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($rd_value) . '" class="wppoppop-form-input wppoppop-input-radio"' . (($default_val !== '' && $default_val === $rd_label) ? ' checked' : '') . $rd_attr
                        . ' style="width:16px;height:16px;margin:0;flex-shrink:0;cursor:pointer;pointer-events:auto;" />'
                        . '<span>' . esc_html($rd_label) . '</span></label>';
                }
                return '<div class="wppoppop-radio-group" role="radiogroup" style="width:100%;height:100%;display:flex;flex-direction:' . ($rd_row ? 'row' : 'column') . ';flex-wrap:wrap;align-items:' . ($rd_row ? 'center' : $rd_j) . ';justify-content:' . ($rd_row ? $rd_j : 'center') . ';gap:8px;padding:0 6px;box-sizing:border-box;pointer-events:auto;' . $rd_ty . '">' . $rd_html . '</div>';

            case 'checkboxgroup':
                /* WPPOPPOP_CBGROUP_FRONT: Label|Value checkbox group, array submit, layout, typography, any-checked required */
                $cg_raw = isset($el['optionsList']) ? (string) $el['optionsList'] : '';
                $cg_items = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $cg_raw)), 'strlen'));
                if (empty($cg_items)) { $cg_items = array('Choice A', 'Choice B', 'Choice C'); }
                $cg_lay = strtolower((string) (isset($el['layout']) ? $el['layout'] : 'vertical'));
                $cg_row = in_array($cg_lay, array('horizontal', 'inline', 'row'), true);
                $cg_al = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $cg_j = ($cg_al === 'center') ? 'center' : (($cg_al === 'right') ? 'flex-end' : 'flex-start');
                $cg_ty = 'font-size:' . ((!empty($el['fontSize']) && is_numeric($el['fontSize'])) ? intval($el['fontSize']) : 12) . 'px;';
                if (!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) { $cg_ty .= 'font-weight:' . $el['fontWeight'] . ';'; }
                if (!empty($el['lineHeight']) && is_numeric($el['lineHeight'])) { $cg_ty .= 'line-height:' . floatval($el['lineHeight']) . ';'; }
                if (isset($el['letterSpacing']) && is_numeric($el['letterSpacing']) && floatval($el['letterSpacing']) != 0) { $cg_ty .= 'letter-spacing:' . floatval($el['letterSpacing']) . 'px;'; }
                if (!empty($el['color']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['color'])) { $cg_ty .= 'color:' . $el['color'] . ';'; }
                if (!empty($el['fontFamily']) && $el['fontFamily'] !== 'inherit' && preg_match('/^[A-Za-z0-9 ,\'"-]+$/', (string) $el['fontFamily'])) { $cg_ty .= 'font-family:' . esc_attr($el['fontFamily']) . ';'; }
                $cg_defs = array_filter(array_map('trim', explode(',', $default_val)), 'strlen');
                $cg_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|autocomplete|readonly="readonly")(="[^"]*")?/', '', $data_attrs);
                $cg_html = '';
                foreach ($cg_items as $cg_i => $cg_item_raw) {
                    $cg_pp = explode('|', (string) $cg_item_raw, 2);
                    $cg_label = trim($cg_pp[0]);
                    $cg_value = (isset($cg_pp[1]) && trim($cg_pp[1]) !== '') ? trim($cg_pp[1]) : $cg_label;
                    if ($cg_label === '') { $cg_label = $cg_value; }
                    $cg_id = $field_id . '-' . $cg_i;
                    $cg_attr = ($cg_i === 0) ? $cg_data : preg_replace('/\srequired="required"|\saria-required="true"|\sdata-error-msg="[^"]*"/', '', $cg_data);
                    $cg_on = (in_array($cg_value, $cg_defs, true) || in_array($cg_label, $cg_defs, true));
                    $cg_html .= '<label for="' . esc_attr($cg_id) . '" style="display:inline-flex;align-items:center;gap:8px;font-size:inherit;color:inherit;cursor:pointer;line-height:inherit;">'
                        . '<input type="checkbox" id="' . esc_attr($cg_id) . '" name="fields[' . esc_attr($field_name) . '][]" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($cg_value) . '" class="wppoppop-form-input wppoppop-input-checkbox"' . ($cg_on ? ' checked' : '') . $cg_attr
                        . ' style="width:16px;height:16px;margin:0;flex-shrink:0;cursor:pointer;pointer-events:auto;" />'
                        . '<span>' . esc_html($cg_label) . '</span></label>';
                }
                return '<div class="wppoppop-checkbox-group wppoppop-checkboxgroup" role="group" style="width:100%;height:100%;display:flex;flex-direction:' . ($cg_row ? 'row' : 'column') . ';flex-wrap:wrap;align-items:' . ($cg_row ? 'center' : $cg_j) . ';justify-content:' . ($cg_row ? $cg_j : 'center') . ';gap:8px;padding:0 6px;box-sizing:border-box;pointer-events:auto;' . $cg_ty . '">' . $cg_html . '</div>';

            case 'textarea':
                /* WPPOPPOP_TEXTAREA_FRONT: alignment + rows min-height + live counter */
                $ta_rows  = !empty($el['rows']) ? max(2, min(15, intval($el['rows']))) : 4;
                $ta_align = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $ta_max   = (!empty($el['maxLength']) && intval($el['maxLength']) > 0) ? intval($el['maxLength']) : 0;
                $ta_minh  = $ta_rows * 20 + 16;
                $ta_cnt   = $ta_max > 0 ? '<div class="wppoppop-ta-count" data-max="' . $ta_max . '" style="position:absolute;right:10px;bottom:6px;font-size:10px;color:#94a3b8;pointer-events:none;">0 / ' . $ta_max . '</div>' : '';
                return '<div class="wppoppop-tf-wrap" style="position:relative;width:100%;height:100%;min-height:' . $ta_minh . 'px;pointer-events:auto;">'
                    . '<textarea id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" rows="' . $ta_rows . '" placeholder="' . esc_attr($placeholder !== '' ? $placeholder : 'Write your message...') . '" class="wppoppop-form-input wppoppop-input-textarea"' . $data_attrs
                    . ' style="width:100%;height:100%;min-height:' . $ta_minh . 'px;box-sizing:border-box;padding:8px 12px;resize:none;text-align:' . esc_attr($ta_align) . ';pointer-events:auto;-webkit-user-select:text;user-select:text;">' . esc_textarea($default_val) . '</textarea>'
                    . $ta_cnt . '</div>';

            case 'multiselect':
                /* WPPOPPOP_MULTISELECT_FRONT_V2: chip UI that mirrors the builder stage */
                $ms_raw = isset($el['optionsList']) ? (string) $el['optionsList'] : (isset($el['options']) ? (is_array($el['options']) ? implode("\n", $el['options']) : (string) $el['options']) : '');
                $ms_items = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $ms_raw)), 'strlen'));
                if (count($ms_items) === 1 && strpos($ms_items[0], ',') !== false) { $ms_items = array_values(array_filter(array_map('trim', explode(',', $ms_items[0])), 'strlen')); }
                if (empty($ms_items)) { $ms_items = array('Marketing', 'Design', 'Development'); }
                $ms_max = !empty($el['maxSelections']) ? max(1, intval($el['maxSelections'])) : (!empty($el['maxSelect']) ? max(1, intval($el['maxSelect'])) : 0);
                $ms_fs = (!empty($el['fontSize']) && is_numeric($el['fontSize'])) ? intval($el['fontSize']) : 10;
                $ms_defaults = array_filter(array_map('trim', explode(',', $default_val)), 'strlen');
                $ms_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|autocomplete|readonly="readonly")(="[^"]*")?/', '', $data_attrs);
                $ms_chips = '';
                $ms_opts = '';
                foreach ($ms_items as $ms_i => $ms_o) {
                    $ms_on = in_array($ms_o, $ms_defaults, true);
                    $ms_chips .= '<button type="button" class="wppoppop-ms-chip" data-idx="' . $ms_i . '" aria-pressed="' . ($ms_on ? 'true' : 'false') . '" style="padding:2px 7px;border-radius:4px;font-size:' . $ms_fs . 'px;font-weight:600;line-height:1.4;cursor:pointer;margin:0;font-family:inherit;background:' . ($ms_on ? '#c2185b' : '#f1f5f9') . ';color:' . ($ms_on ? '#ffffff' : '#475569') . ';border:1px solid ' . ($ms_on ? '#c2185b' : '#cbd5e1') . ';">' . esc_html($ms_o) . '</button>';
                    $ms_opts .= '<option value="' . esc_attr($ms_o) . '"' . ($ms_on ? ' selected' : '') . '>' . esc_html($ms_o) . '</option>';
                }
                $ms_label = ($ms_max > 0) ? '<span class="wppoppop-ms-max" style="margin-left:auto;font-size:9px;color:#94a3b8;">Max: ' . $ms_max . '</span>' : '';
                return '<div class="wppoppop-ms-wrap" style="position:relative;width:100%;height:100%;background:#ffffff;border:1px solid #cbd5e1;border-radius:6px;padding:6px 8px;display:flex;flex-wrap:wrap;align-items:center;align-content:flex-start;gap:5px;box-sizing:border-box;overflow:auto;pointer-events:auto;">'
                    . $ms_chips . $ms_label
                    . '<select id="' . $field_id . '" name="fields[' . esc_attr($field_name) . '][]" data-field-name="' . esc_attr($field_name) . '"' . ($ms_max > 0 ? ' data-max-select="' . $ms_max . '"' : '') . ' multiple="multiple" tabindex="-1" aria-hidden="true" class="wppoppop-form-input wppoppop-input-multiselect"' . $ms_data
                    . ' style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;pointer-events:none;border:0;margin:0;">' . $ms_opts . '</select>'
                    . '</div>';

            case 'imageselect':
                /* WPPOPPOP_IMAGESELECT_FRONT: panel keys, Label|URL parsing, active border, clickable cards, required-safe value */
                $is_raw = isset($el['optionsList']) ? (string) $el['optionsList'] : (isset($el['options']) ? (is_array($el['options']) ? implode("\n", $el['options']) : (string) $el['options']) : '');
                $is_lines = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $is_raw)), 'strlen'));
                if (count($is_lines) === 1 && strpos($is_lines[0], ',') !== false && !preg_match('#https?:|//|\||::#i', $is_lines[0])) {
                    $is_lines = array_values(array_filter(array_map('trim', explode(',', $is_lines[0])), 'strlen'));
                }
                if (empty($is_lines)) { $is_lines = array('Style A', 'Style B', 'Style C'); }
                $is_items = array();
                foreach ($is_lines as $is_ln) {
                    $is_lbl = $is_ln; $is_img = '';
                    if (strpos($is_ln, '::') !== false) { $is_p = explode('::', $is_ln, 2); $is_lbl = trim($is_p[0]); $is_img = trim($is_p[1]); }
                    elseif (strpos($is_ln, '|') !== false) { $is_p = explode('|', $is_ln, 2); $is_lbl = trim($is_p[0]); $is_img = trim($is_p[1]); }
                    elseif (preg_match('#^(.*?),\s*((?:https?:)?//\S+|/\S+)$#i', $is_ln, $is_m)) { $is_lbl = trim($is_m[1]); $is_img = trim($is_m[2]); }
                    $is_items[] = array($is_lbl, $is_img);
                }
                $is_cols = !empty($el['gridCols']) ? intval($el['gridCols']) : (!empty($el['columns']) ? intval($el['columns']) : 3);
                $is_cols = max(1, min(4, $is_cols));
                $is_acc = (!empty($el['activeColor']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['activeColor'])) ? $el['activeColor'] : '#38bdf8';
                $is_multi = !empty($el['multiSelect']) && $el['multiSelect'] !== 'false' && $el['multiSelect'] !== '0';
                $is_defs = array_values(array_filter(array_map('trim', explode(',', $default_val)), 'strlen'));
                if (!$is_multi && count($is_defs) > 1) { $is_defs = array($is_defs[0]); }
                $is_ty = 'font-size:' . ((!empty($el['fontSize']) && is_numeric($el['fontSize'])) ? intval($el['fontSize']) : 12) . 'px;';
                $is_ty .= 'font-weight:' . ((!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) ? $el['fontWeight'] : '600') . ';';
                if (!empty($el['lineHeight']) && is_numeric($el['lineHeight'])) { $is_ty .= 'line-height:' . floatval($el['lineHeight']) . ';'; }
                if (isset($el['letterSpacing']) && is_numeric($el['letterSpacing']) && floatval($el['letterSpacing']) != 0) { $is_ty .= 'letter-spacing:' . floatval($el['letterSpacing']) . 'px;'; }
                $is_ty .= 'color:' . ((!empty($el['color']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['color'])) ? $el['color'] : '#1e293b') . ';';
                $is_cards = '';
                $is_vals = array();
                foreach ($is_items as $is_it) {
                    $is_on = in_array($is_it[0], $is_defs, true);
                    if ($is_on) { $is_vals[] = $is_it[0]; }
                    $is_url = ($is_it[1] !== '') ? esc_url($is_it[1]) : '';
                    $is_thumb = ($is_url !== '') ? '<img src="' . $is_url . '" alt="' . esc_attr($is_it[0]) . '" style="width:100%;flex:1 1 auto;min-height:0;object-fit:cover;border-radius:3px;display:block;" />' : '';
                    $is_cards .= '<div class="wppoppop-imgsel-card" role="button" tabindex="0" data-value="' . esc_attr($is_it[0]) . '" aria-pressed="' . ($is_on ? 'true' : 'false') . '" style="min-width:0;min-height:0;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;padding:4px;border:2px solid ' . ($is_on ? $is_acc : '#cbd5e1') . ';border-radius:6px;background:#ffffff;text-align:center;cursor:pointer;">'
                        . $is_thumb . '<span style="max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;' . $is_ty . '">' . esc_html($is_it[0]) . '</span></div>';
                }
                $is_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|autocomplete|readonly="readonly")(="[^"]*")?/', '', $data_attrs);
                return '<div class="wppoppop-imgsel-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                    . '<div class="wppoppop-imgsel-grid" data-multi="' . ($is_multi ? '1' : '0') . '" data-active-color="' . esc_attr($is_acc) . '" style="display:grid;grid-template-columns:repeat(' . $is_cols . ',minmax(0,1fr));grid-auto-rows:minmax(0,1fr);gap:6px;width:100%;height:100%;box-sizing:border-box;">' . $is_cards . '</div>'
                    . '<input type="text" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr(implode(', ', $is_vals)) . '" readonly tabindex="-1" aria-hidden="true"' . $is_data . ' style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;pointer-events:none;border:0;margin:0;padding:0;" />'
                    . '</div>';

            case 'tile':
                /* WPPOPPOP_TILE_FRONT: panel keys, own click class, per-tile border/typography/hover, required-safe value */
                $tl_hex = function ($v, $d) { return (is_string($v) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $v)) ? $v : $d; };
                $tl_raw = isset($el['optionsList']) ? (string) $el['optionsList'] : (isset($el['options']) ? (is_array($el['options']) ? implode("\n", $el['options']) : (string) $el['options']) : '');
                $tl_items = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $tl_raw)), 'strlen'));
                if (count($tl_items) === 1 && strpos($tl_items[0], ',') !== false) { $tl_items = array_values(array_filter(array_map('trim', explode(',', $tl_items[0])), 'strlen')); }
                if (empty($tl_items)) { $tl_items = array('$10 / mo', '$25 / mo', '$50 / mo'); }
                $tl_cols = !empty($el['gridCols']) ? intval($el['gridCols']) : (!empty($el['columns']) ? intval($el['columns']) : 3);
                $tl_cols = max(1, min(4, $tl_cols));
                $tl_acc = $tl_hex(isset($el['activeColor']) ? $el['activeColor'] : '', '#38bdf8');
                $tl_multi = !empty($el['multiSelect']) && $el['multiSelect'] !== 'false' && $el['multiSelect'] !== '0';
                $tl_defs = array_values(array_filter(array_map('trim', explode(',', $default_val)), 'strlen'));
                if (!$tl_multi && count($tl_defs) > 1) { $tl_defs = array($tl_defs[0]); }
                $tl_al = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'center';
                $tl_j = ($tl_al === 'left') ? 'flex-start' : (($tl_al === 'right') ? 'flex-end' : 'center');
                $tl_fg = $tl_hex(isset($el['color']) ? $el['color'] : '', '#334155');
                $tl_bg = (!empty($el['transparentFill']) || (isset($el['bgColor']) && $el['bgColor'] === 'transparent')) ? 'transparent' : $tl_hex(isset($el['bgColor']) ? $el['bgColor'] : '', '#f1f5f9'); /* WPPOPPOP_TILE_BG */
                $tl_ty = 'font-size:' . ((!empty($el['fontSize']) && is_numeric($el['fontSize'])) ? intval($el['fontSize']) : 12) . 'px;';
                $tl_ty .= 'font-weight:' . ((!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) ? $el['fontWeight'] : '700') . ';';
                if (!empty($el['lineHeight']) && is_numeric($el['lineHeight'])) { $tl_ty .= 'line-height:' . floatval($el['lineHeight']) . ';'; }
                if (isset($el['letterSpacing']) && is_numeric($el['letterSpacing']) && floatval($el['letterSpacing']) != 0) { $tl_ty .= 'letter-spacing:' . floatval($el['letterSpacing']) . 'px;'; }
                $tl_bw = (isset($el['borderWidth']) && is_numeric($el['borderWidth']) && intval($el['borderWidth']) > 0) ? intval($el['borderWidth']) : 1;
                $tl_bs = (!empty($el['borderStyle']) && in_array($el['borderStyle'], array('solid', 'dashed', 'dotted', 'double', 'none'), true)) ? $el['borderStyle'] : 'solid';
                $tl_bc = $tl_hex(isset($el['borderColor']) ? $el['borderColor'] : '', '#cbd5e1');
                $tl_rad = (isset($el['borderRadius']) && is_numeric($el['borderRadius'])) ? max(0, intval($el['borderRadius'])) : 6;
                $tl_h = array(
                    'background-color' => $tl_hex(isset($el['hoverBgColor']) ? $el['hoverBgColor'] : (isset($el['hoverBg']) ? $el['hoverBg'] : ''), ''),
                    'color' => $tl_hex(isset($el['hoverTextColor']) ? $el['hoverTextColor'] : (isset($el['hoverColor']) ? $el['hoverColor'] : ''), ''),
                    'border-color' => $tl_hex(isset($el['hoverBorderColor']) ? $el['hoverBorderColor'] : (isset($el['hoverBorder']) ? $el['hoverBorder'] : ''), ''),
                );
                $tl_rules = '';
                foreach ($tl_h as $tl_prop => $tl_val) { if ($tl_val !== '') { $tl_rules .= $tl_prop . ':' . $tl_val . ' !important;'; } }
                $tl_style = ($tl_rules !== '') ? '<style>#' . $field_id . '-grid .wppoppop-tl-card[aria-pressed="false"]:hover{' . $tl_rules . '}</style>' : '';
                $tl_cards = '';
                $tl_vals = array();
                foreach ($tl_items as $tl_lbl) {
                    $tl_on = in_array($tl_lbl, $tl_defs, true);
                    if ($tl_on) { $tl_vals[] = $tl_lbl; }
                    $tl_border = ($tl_bs === 'none') ? 'none' : ($tl_bw . 'px ' . $tl_bs . ' ' . ($tl_on ? $tl_acc : $tl_bc));
                    $tl_cards .= '<div class="wppoppop-tl-card" role="button" tabindex="0" data-value="' . esc_attr($tl_lbl) . '" data-bg="' . esc_attr($tl_bg) . '" data-fg="' . esc_attr($tl_fg) . '" data-bc="' . esc_attr($tl_bc) . '" aria-pressed="' . ($tl_on ? 'true' : 'false') . '" style="min-width:0;box-sizing:border-box;display:flex;align-items:center;justify-content:' . $tl_j . ';padding:6px;border-radius:' . $tl_rad . 'px;border:' . $tl_border . ';background:' . ($tl_on ? $tl_acc : $tl_bg) . ';color:' . ($tl_on ? '#ffffff' : $tl_fg) . ';cursor:pointer;transition:background-color .15s ease,color .15s ease,border-color .15s ease;-webkit-user-select:none;user-select:none;' . $tl_ty . '">'
                        . '<span style="max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . esc_html($tl_lbl) . '</span></div>';
                }
                $tl_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|autocomplete|readonly="readonly")(="[^"]*")?/', '', $data_attrs);
                return $tl_style . '<div class="wppoppop-tl-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                    . '<div id="' . $field_id . '-grid" class="wppoppop-tl-grid" data-multi="' . ($tl_multi ? '1' : '0') . '" data-active-color="' . esc_attr($tl_acc) . '" style="display:grid;grid-template-columns:repeat(' . $tl_cols . ',minmax(0,1fr));grid-auto-rows:minmax(0,1fr);gap:6px;width:100%;height:100%;box-sizing:border-box;">' . $tl_cards . '</div>'
                    . '<input type="text" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr(implode(', ', $tl_vals)) . '" readonly tabindex="-1" aria-hidden="true"' . $tl_data . ' style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;pointer-events:none;border:0;margin:0;padding:0;" />'
                    . '</div>';

            case 'date':
                /* WPPOPPOP_DATE_FRONT: placeholder, format, min/max, icons, alignment, default, required */
                $dt_fmt = (!empty($el['dateFormat']) && in_array($el['dateFormat'], array('YYYY-MM-DD', 'MM/DD/YYYY', 'DD/MM/YYYY'), true)) ? $el['dateFormat'] : 'YYYY-MM-DD';
                $dt_ph = ($placeholder !== '') ? $placeholder : $dt_fmt;
                $dt_align = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $dt_min = (!empty($el['minDate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $el['minDate'])) ? $el['minDate'] : '';
                $dt_max = (!empty($el['maxDate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $el['maxDate'])) ? $el['maxDate'] : '';
                $dt_icons = array('left' => array('cls' => 'fas fa-calendar-alt', 'off' => 10), 'right' => array('cls' => '', 'off' => 10));
                $dt_needs_dash = false;
                foreach (array('iconLeft' => 'left', 'iconRight' => 'right') as $dt_key => $dt_side) {
                    $dt_raw = isset($el[$dt_key]) ? trim((string) $el[$dt_key]) : '';
                    if ($dt_raw !== '') {
                        $dt_tokens = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', $dt_raw))));
                        if (!empty($dt_tokens)) {
                            if (strpos($dt_tokens[0], 'fa-') === 0) { array_unshift($dt_tokens, 'fas'); }
                            elseif (strpos($dt_tokens[0], 'dashicons-') === 0) { array_unshift($dt_tokens, 'dashicons'); $dt_needs_dash = true; }
                            $dt_icons[$dt_side]['cls'] = implode(' ', $dt_tokens);
                        }
                    }
                    $dt_ok = $dt_key . 'Offset';
                    if (isset($el[$dt_ok]) && $el[$dt_ok] !== '') { $dt_icons[$dt_side]['off'] = max(0, intval($el[$dt_ok])); }
                }
                static $dt_css_done = false;
                $dt_css = '';
                if (!$dt_css_done) {
                    $dt_css_done = true;
                    $dt_css = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />';
                    if ($dt_needs_dash) { $dt_css .= '<link rel="stylesheet" href="' . esc_url(includes_url('css/dashicons.min.css')) . '" />'; }
                }
                $dt_pl = ($dt_icons['left']['cls'] !== '') ? ($dt_icons['left']['off'] + 22) : 12;
                $dt_pr = ($dt_icons['right']['cls'] !== '') ? ($dt_icons['right']['off'] + 22) : 12;
                $dt_ico = function ($side) use ($dt_icons) {
                    if ($dt_icons[$side]['cls'] === '') { return ''; }
                    return '<i class="' . esc_attr($dt_icons[$side]['cls']) . '" aria-hidden="true" style="position:absolute;' . $side . ':' . intval($dt_icons[$side]['off']) . 'px;top:50%;transform:translateY(-50%);color:#64748b;font-size:14px;line-height:1;pointer-events:none;"></i>';
                };
                $dt_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|readonly="readonly"|data-readonly="true")(="[^"]*")?/', '', $data_attrs);
                return $dt_css . '<div class="wppoppop-tf-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                    . $dt_ico('left')
                    . '<input type="text" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($default_val) . '" placeholder="' . esc_attr($dt_ph) . '" autocomplete="off"'
                    . ' data-format="' . esc_attr($dt_fmt) . '" data-min="' . esc_attr($dt_min) . '" data-max="' . esc_attr($dt_max) . '"'
                    . ' class="wppoppop-form-input wppoppop-front-date wppoppop-datepicker"' . $dt_data
                    . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 ' . $dt_pr . 'px 0 ' . $dt_pl . 'px;text-align:' . esc_attr($dt_align) . ';pointer-events:auto;-webkit-user-select:text;user-select:text;background:#ffffff;border:1px solid #cbd5e1;border-radius:inherit;font-size:12px;color:#334155;" />'
                    . $dt_ico('right')
                    . '</div>';

            case 'time':
                /* WPPOPPOP_TIME_FRONT: icons (default clock), alignment, interval, 12h/24h hint, default, required */
                $tm_fmt = (isset($el['timeFormat']) && $el['timeFormat'] === '24h') ? '24h' : '12h';
                $tm_step_min = !empty($el['timeStep']) ? intval($el['timeStep']) : (!empty($el['minuteStep']) ? intval($el['minuteStep']) : 15);
                $tm_step = max(1, $tm_step_min) * 60;
                $tm_align = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $tm_icons = array('left' => array('cls' => 'fas fa-clock', 'off' => 10), 'right' => array('cls' => '', 'off' => 10));
                $tm_needs_dash = false;
                foreach (array('iconLeft' => 'left', 'iconRight' => 'right') as $tm_key => $tm_side) {
                    $tm_raw = isset($el[$tm_key]) ? trim((string) $el[$tm_key]) : '';
                    if ($tm_raw !== '') {
                        $tm_tokens = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', $tm_raw))));
                        if (!empty($tm_tokens)) {
                            if (strpos($tm_tokens[0], 'fa-') === 0) { array_unshift($tm_tokens, 'fas'); }
                            elseif (strpos($tm_tokens[0], 'dashicons-') === 0) { array_unshift($tm_tokens, 'dashicons'); $tm_needs_dash = true; }
                            $tm_icons[$tm_side]['cls'] = implode(' ', $tm_tokens);
                        }
                    }
                    $tm_ok = $tm_key . 'Offset';
                    if (isset($el[$tm_ok]) && $el[$tm_ok] !== '') { $tm_icons[$tm_side]['off'] = max(0, intval($el[$tm_ok])); }
                }
                static $tm_css_done = false;
                $tm_css = '';
                if (!$tm_css_done) {
                    $tm_css_done = true;
                    $tm_css = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />';
                    if ($tm_needs_dash) { $tm_css .= '<link rel="stylesheet" href="' . esc_url(includes_url('css/dashicons.min.css')) . '" />'; }
                }
                $tm_pl = ($tm_icons['left']['cls'] !== '') ? ($tm_icons['left']['off'] + 22) : 12;
                $tm_pr = ($tm_icons['right']['cls'] !== '') ? ($tm_icons['right']['off'] + 22) : 12;
                $tm_ico = function ($side) use ($tm_icons) {
                    if ($tm_icons[$side]['cls'] === '') { return ''; }
                    return '<i class="' . esc_attr($tm_icons[$side]['cls']) . '" aria-hidden="true" style="position:absolute;' . $side . ':' . intval($tm_icons[$side]['off']) . 'px;top:50%;transform:translateY(-50%);color:#64748b;font-size:14px;line-height:1;pointer-events:none;z-index:2;"></i>';
                };
                $tm_val = preg_match('/^\d{2}:\d{2}$/', $default_val) ? $default_val : '';
                $tm_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|readonly="readonly"|data-readonly="true")(="[^"]*")?/', '', $data_attrs);
                return $tm_css . '<div class="wppoppop-tf-wrap wppoppop-time-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                    . $tm_ico('left')
                    . '<input type="time" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" step="' . $tm_step . '" value="' . esc_attr($tm_val) . '" title="' . esc_attr($placeholder) . '" data-format="' . esc_attr($tm_fmt) . '" lang="' . ($tm_fmt === '24h' ? 'en-GB' : 'en-US') . '" class="wppoppop-form-input wppoppop-input-time"' . $tm_data
                    . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 ' . $tm_pr . 'px 0 ' . $tm_pl . 'px;text-align:' . esc_attr($tm_align) . ';pointer-events:auto;" />'
                    . $tm_ico('right')
                    . '</div>';

            case 'upload':
            case 'file':
                /* WPPOPPOP_UPLOAD_FRONT_V2: icons+offsets, normalized accept, multi, max size, alignment, file list container */
                $up_raw = !empty($el['allowedExtensions']) ? (string) $el['allowedExtensions'] : (!empty($el['allowedExts']) ? (string) $el['allowedExts'] : '.jpg,.png,.pdf');
                $up_list = array();
                foreach (preg_split('/[\s,;]+/', $up_raw) as $up_x) {
                    $up_x = strtolower(trim($up_x));
                    if ($up_x === '') { continue; }
                    $up_x = '.' . ltrim($up_x, '.');
                    if (preg_match('/^\.[a-z0-9]{1,10}$/', $up_x)) { $up_list[] = $up_x; }
                }
                if (empty($up_list)) { $up_list = array('.jpg', '.png', '.pdf'); }
                $exts = esc_attr(implode(',', array_values(array_unique($up_list))));
                $up_multi = !empty($el['multiUpload']) && $el['multiUpload'] !== 'false' && $el['multiUpload'] !== '0';
                $multi_attr = $up_multi ? ' multiple="multiple"' : '';
                $up_max = !empty($el['maxFileSize']) ? max(1, intval($el['maxFileSize'])) : 5;
                $up_maxfiles = ($up_multi && !empty($el['maxFiles'])) ? min(20, max(1, intval($el['maxFiles']))) : 0;
                $up_errmsg = (isset($el['uploadErrorMessage']) && trim((string) $el['uploadErrorMessage']) !== '') ? (string) $el['uploadErrorMessage'] : 'File too large. Maximum size is {max}MB.'; /* WPPOPPOP_UPLOAD_LIMITS */
                $up_al = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'center';
                $up_j = ($up_al === 'left') ? 'flex-start' : (($up_al === 'right') ? 'flex-end' : 'center');
                $up_icons = array('left' => array('cls' => 'fas fa-cloud-arrow-up', 'off' => 10), 'right' => array('cls' => '', 'off' => 10));
                $up_needs_dash = false;
                foreach (array('iconLeft' => 'left', 'iconRight' => 'right') as $up_key => $up_side) {
                    $up_r = isset($el[$up_key]) ? trim((string) $el[$up_key]) : '';
                    if ($up_r !== '') {
                        $up_tk = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', $up_r))));
                        if (!empty($up_tk)) {
                            if (strpos($up_tk[0], 'fa-') === 0) { array_unshift($up_tk, 'fas'); }
                            elseif (strpos($up_tk[0], 'dashicons-') === 0) { array_unshift($up_tk, 'dashicons'); $up_needs_dash = true; }
                            $up_icons[$up_side]['cls'] = implode(' ', $up_tk);
                        }
                    }
                    $up_ok = $up_key . 'Offset';
                    if (isset($el[$up_ok]) && $el[$up_ok] !== '') { $up_icons[$up_side]['off'] = max(0, intval($el[$up_ok])); }
                }
                static $up_css_done = false;
                $up_css = '';
                if (!$up_css_done) {
                    $up_css_done = true;
                    $up_css = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />';
                    if ($up_needs_dash) { $up_css .= '<link rel="stylesheet" href="' . esc_url(includes_url('css/dashicons.min.css')) . '" />'; }
                }
                $up_pl = ($up_icons['left']['cls'] !== '') ? ($up_icons['left']['off'] + 22) : 10;
                $up_pr = ($up_icons['right']['cls'] !== '') ? ($up_icons['right']['off'] + 22) : 10;
                $up_ico = function ($side) use ($up_icons) {
                    if ($up_icons[$side]['cls'] === '') { return ''; }
                    return '<i class="' . esc_attr($up_icons[$side]['cls']) . '" aria-hidden="true" style="position:absolute;' . $side . ':' . intval($up_icons[$side]['off']) . 'px;top:50%;transform:translateY(-50%);color:#64748b;font-size:14px;line-height:1;pointer-events:none;z-index:2;"></i>';
                };
                $prompt = $placeholder ?: 'Choose file or drag here...';
                $up_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|autocomplete|readonly="readonly"|data-readonly="true")(="[^"]*")?/', '', $data_attrs);
                return $up_css . '<div class="wppoppop-upload-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                     . '<label class="wppoppop-upload-dropzone" style="position:relative;width:100%;height:100%;border:1px dashed #94a3b8;border-radius:inherit;display:flex;align-items:center;justify-content:' . $up_j . ';gap:6px;font-size:12px;color:#475569;background:#f8fafc;cursor:pointer;box-sizing:border-box;padding:0 ' . $up_pr . 'px 0 ' . $up_pl . 'px;">'
                     . $up_ico('left')
                     . '<span class="wppoppop-upload-label-text" data-default-label="' . esc_attr($prompt) . '" style="max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . esc_html($prompt) . '</span>'
                     . '<input type="file" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']' . ($up_multi ? '[]' : '') . '" data-field-name="' . esc_attr($field_name) . '" accept="' . $exts . '" data-max-size="' . $up_max . '" data-max-files="' . $up_maxfiles . '" data-size-msg="' . esc_attr($up_errmsg) . '"' . $multi_attr . $up_data . ' style="display:none;" />'
                     . $up_ico('right')
                     . '</label>'
                     . '<div class="wppoppop-upload-list" aria-live="polite" style="position:absolute;left:0;top:100%;margin-top:4px;width:100%;z-index:5;display:flex;flex-direction:column;gap:4px;"></div>'
                     . '</div>';

            case 'password':
                /* WPPOPPOP_PASSWORD_FRONT_V3: mimics the WordPress login password field (wp-pwd / wp-hide-pw / dashicons) */
                $pw_eye_on = !isset($el['showEyeToggle']) || (!empty($el['showEyeToggle']) && $el['showEyeToggle'] !== 'false' && $el['showEyeToggle'] !== '0');
                $pw_al = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'left';
                $pw_icons = array('left' => array('cls' => '', 'off' => 10), 'right' => array('cls' => '', 'off' => 10));
                foreach (array('iconLeft' => 'left', 'iconRight' => 'right') as $pw_key => $pw_side) {
                    $pw_r = isset($el[$pw_key]) ? trim((string) $el[$pw_key]) : '';
                    if ($pw_r !== '') {
                        $pw_tk = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', $pw_r))));
                        if (!empty($pw_tk)) {
                            if (strpos($pw_tk[0], 'fa-') === 0) { array_unshift($pw_tk, 'fas'); }
                            elseif (strpos($pw_tk[0], 'dashicons-') === 0) { array_unshift($pw_tk, 'dashicons'); }
                            $pw_icons[$pw_side]['cls'] = implode(' ', $pw_tk);
                        }
                    }
                    $pw_ok = $pw_key . 'Offset';
                    if (isset($el[$pw_ok]) && $el[$pw_ok] !== '') { $pw_icons[$pw_side]['off'] = max(0, intval($el[$pw_ok])); }
                }
                static $pw_css_done = false;
                $pw_css = '';
                if (!$pw_css_done) {
                    $pw_css_done = true;
                    $pw_css = '<link rel="stylesheet" href="' . esc_url(includes_url('css/dashicons.min.css')) . '" />'
                        . '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />'
                        . '<style>.wppoppop-pw-toggle{position:absolute;right:0;top:0;width:40px;height:100%;min-width:40px;margin:0;padding:0;border:0;background:transparent;box-shadow:none;color:#2271b1;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:3;pointer-events:auto;-webkit-appearance:none;appearance:none;}.wppoppop-pw-toggle:hover,.wppoppop-pw-toggle:focus{color:#135e96;outline:none;}.wppoppop-pw-toggle .dashicons{font-size:20px;width:20px;height:20px;line-height:20px;}</style>';
                }
                $pw_rpos = $pw_icons['right']['off'] + ($pw_eye_on ? 40 : 0);
                $pw_pl = ($pw_icons['left']['cls'] !== '') ? ($pw_icons['left']['off'] + 22) : 12;
                $pw_pr = max($pw_eye_on ? 44 : 12, ($pw_icons['right']['cls'] !== '') ? ($pw_rpos + 22) : 0);
                $pw_ico = function ($side, $pos) use ($pw_icons) {
                    if ($pw_icons[$side]['cls'] === '') { return ''; }
                    return '<i class="' . esc_attr($pw_icons[$side]['cls']) . '" aria-hidden="true" style="position:absolute;' . $side . ':' . intval($pos) . 'px;top:50%;transform:translateY(-50%);color:#64748b;font-size:14px;line-height:1;pointer-events:none;z-index:2;"></i>';
                };
                $pw_ac = !empty($el['autocomplete']) ? $el['autocomplete'] : 'current-password';
                $pw_data = preg_replace('/\s(data-input-mask|data-filters|autocomplete)(="[^"]*")?/', '', $data_attrs);
                $eye_btn = $pw_eye_on ? '<button type="button" class="wppoppop-pw-toggle wp-hide-pw" aria-label="Show password" aria-pressed="false"><span class="dashicons dashicons-visibility" aria-hidden="true"></span></button>' : '';
                return $pw_css . '<div class="wp-pwd wppoppop-password-wrap wppoppop-tf-wrap" style="position:relative;width:100%;height:100%;pointer-events:auto;">'
                     . $pw_ico('left', $pw_icons['left']['off'])
                     . '<input type="password" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($default_val) . '" placeholder="' . esc_attr($placeholder ?: 'Enter password...') . '" autocomplete="' . esc_attr($pw_ac) . '" spellcheck="false" autocapitalize="off" class="wppoppop-form-input wppoppop-input-password"' . $pw_data . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 ' . $pw_pr . 'px 0 ' . $pw_pl . 'px;text-align:' . esc_attr($pw_al) . ';pointer-events:auto;" />'
                     . $pw_ico('right', $pw_rpos)
                     . $eye_btn . '</div>';

            case 'rating':
                /* WPPOPPOP_RATING_FRONT_V2: inactive colour, default from Data tab, a11y, matches builder stage */
                $rmax = ((!empty($el['ratingMax']) && intval($el['ratingMax']) === 10) || (empty($el['ratingMax']) && !empty($el['maxStars']) && intval($el['maxStars']) === 10)) ? 10 : 5; /* WPPOPPOP_RATING_MAXSTARS */
                $rcol = (!empty($el['ratingColor']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['ratingColor'])) ? $el['ratingColor'] : '#f59e0b';
                $roff = (!empty($el['ratingInactiveColor']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['ratingInactiveColor'])) ? $el['ratingInactiveColor'] : '#cbd5e1';
                $rshape = !empty($el['ratingShape']) ? $el['ratingShape'] : 'star';
                $glyph = ($rshape === 'heart') ? '♥' : (($rshape === 'thumb') ? '👍' : '★');
                $init_score = ($default_val !== '' && is_numeric($default_val)) ? max(0, min($rmax, intval($default_val))) : 0;
                $rfs = ($rmax === 10) ? 15 : 20;
                $rt_al = (isset($el['textAlign']) && in_array($el['textAlign'], array('left', 'center', 'right'), true)) ? $el['textAlign'] : 'center';
                $rt_j = ($rt_al === 'left') ? 'flex-start' : (($rt_al === 'right') ? 'flex-end' : 'center');
                $stars_html = '';
                for ($i = 1; $i <= $rmax; $i++) {
                    $col = ($i <= $init_score) ? $rcol : $roff;
                    $stars_html .= '<span class="wppoppop-rating-glyph" role="radio" tabindex="0" aria-label="' . $i . ' of ' . $rmax . '" data-score="' . $i . '" data-active-color="' . esc_attr($rcol) . '" style="color:' . esc_attr($col) . ';font-size:' . $rfs . 'px;cursor:pointer;line-height:1;user-select:none;-webkit-user-select:none;">' . $glyph . '</span>';
                }
                $rt_data = preg_replace('/\s(data-input-mask|data-filters|maxlength|minlength|autocomplete|readonly="readonly")(="[^"]*")?/', '', $data_attrs);
                return '<div class="wppoppop-interactive-rating" role="radiogroup" data-active-color="' . esc_attr($rcol) . '" data-inactive-color="' . esc_attr($roff) . '" style="width:100%;height:100%;display:flex;align-items:center;justify-content:' . $rt_j . ';gap:4px;pointer-events:auto;">'
                     . '<input type="hidden" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . ($init_score > 0 ? $init_score : '') . '"' . $rt_data . ' />'
                     . $stars_html . '</div>';
        }

        return null;
    }
}
