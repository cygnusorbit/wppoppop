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

    public static function render_element(string $type, array $el): ?string {
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
                return '<' . $tag . ' class="wppoppop-front-label" style="margin:0;width:100%;height:100%;display:flex;align-items:center;' . $tf . '">' . $content . '</' . $tag . '>';

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
                if (!empty($el['fontSize']) && is_numeric($el['fontSize'])) { $cb_ty .= 'font-size:' . intval($el['fontSize']) . 'px;'; }
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
                $raw_opts = isset($el['options']) ? (is_array($el['options']) ? implode(',', $el['options']) : (string) $el['options']) : 'Option 1, Option 2';
                $items = preg_split('/[\r\n,]+/', $raw_opts);
                $max_sel = !empty($el['maxSelect']) ? intval($el['maxSelect']) : 10;
                $opts_html = '';
                foreach ($items as $item) {
                    $item = trim($item);
                    if ($item === '') continue;
                    $opts_html .= '<option value="' . esc_attr($item) . '">' . esc_html($item) . '</option>';
                }
                return '<select id="' . $field_id . '" name="fields[' . esc_attr($field_name) . '][]" data-field-name="' . esc_attr($field_name) . '" data-max-select="' . $max_sel . '" multiple="multiple" class="wppoppop-form-input wppoppop-input-multiselect"' . $data_attrs . ' style="width:100%;height:100%;box-sizing:border-box;padding:4px;">' . $opts_html . '</select>';

            case 'imageselect':
                $raw_opts = isset($el['options']) ? (is_array($el['options']) ? implode(',', $el['options']) : (string) $el['options']) : 'Starter Plan, Pro Plan, Enterprise';
                $items = preg_split('/[\r\n,]+/', $raw_opts);
                $cols = !empty($el['columns']) ? max(1, min(4, intval($el['columns']))) : 3;
                $multi = !empty($el['multiSelect']) ? 'true' : 'false';
                $cards = '';
                foreach ($items as $idx => $item) {
                    $item = trim($item);
                    if ($item === '') continue;
                    $parts = explode('|', $item);
                    $lbl = trim($parts[0]);
                    $img = isset($parts[1]) ? trim($parts[1]) : '';
                    $active_cls = ($idx === 0 && $default_val === '') || ($default_val === $lbl) ? ' is-selected' : '';
                    $thumb = $img ? '<img src="' . esc_url($img) . '" alt="' . esc_attr($lbl) . '" style="width:100%;height:40px;object-fit:cover;border-radius:4px;margin-bottom:4px;" />' : '';
                    $cards .= '<div class="wppoppop-choice-card' . $active_cls . '" data-value="' . esc_attr($lbl) . '" style="border:2px solid #cbd5e1;border-radius:6px;padding:6px;text-align:center;cursor:pointer;font-size:12px;font-weight:600;background:#f8fafc;">' . $thumb . '<span>' . esc_html($lbl) . '</span></div>';
                }
                $init_val = $default_val !== '' ? $default_val : (isset($items[0]) ? trim(explode('|', $items[0])[0]) : '');
                return '<div class="wppoppop-interactive-choices wppoppop-imageselect-grid" data-multi="' . $multi . '" style="display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:6px;width:100%;height:100%;align-items:center;">'
                     . '<input type="hidden" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($init_val) . '"' . $data_attrs . ' />'
                     . $cards . '</div>';

            case 'tile':
                $raw_opts = isset($el['options']) ? (is_array($el['options']) ? implode(',', $el['options']) : (string) $el['options']) : '$10 / mo, $25 / mo, $50 / mo';
                $items = preg_split('/[\r\n,]+/', $raw_opts);
                $cols = !empty($el['columns']) ? max(1, min(4, intval($el['columns']))) : 3;
                $act_color = !empty($el['activeColor']) ? esc_attr($el['activeColor']) : '#0284c7';
                $multi = !empty($el['multiSelect']) ? 'true' : 'false';
                $tiles = '';
                foreach ($items as $idx => $item) {
                    $lbl = trim($item);
                    if ($lbl === '') continue;
                    $active_cls = ($idx === 0 && $default_val === '') || ($default_val === $lbl) ? ' is-selected' : '';
                    $tiles .= '<div class="wppoppop-choice-card wppoppop-tile-option' . $active_cls . '" data-value="' . esc_attr($lbl) . '" data-active-color="' . $act_color . '" style="padding:8px;border-radius:6px;text-align:center;cursor:pointer;font-weight:700;font-size:12px;border:1px solid #cbd5e1;background:#f1f5f9;color:#334155;">' . esc_html($lbl) . '</div>';
                }
                $init_val = $default_val !== '' ? $default_val : (isset($items[0]) ? trim($items[0]) : '');
                return '<div class="wppoppop-interactive-choices wppoppop-tile-grid" data-multi="' . $multi . '" data-active-color="' . $act_color . '" style="display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:6px;width:100%;height:100%;align-items:center;">'
                     . '<input type="hidden" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($init_val) . '"' . $data_attrs . ' />'
                     . $tiles . '</div>';

            case 'time':
                $step_sec = (!empty($el['timeStep']) ? max(1, intval($el['timeStep'])) : 15) * 60;
                return '<input type="time" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" step="' . $step_sec . '" value="' . esc_attr($default_val) . '" class="wppoppop-form-input wppoppop-input-time"' . $data_attrs . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 12px;" />';

            case 'upload':
            case 'file':
                $exts = !empty($el['allowedExtensions']) ? esc_attr($el['allowedExtensions']) : '.jpg,.png,.pdf';
                $multi_attr = !empty($el['multiUpload']) ? ' multiple="multiple"' : '';
                $prompt =$placeholder ?: 'Choose file or drag here...';
                return '<label class="wppoppop-upload-dropzone" style="width:100%;height:100%;border:1px dashed #94a3b8;border-radius:inherit;display:flex;align-items:center;justify-content:center;gap:6px;font-size:12px;color:#475569;background:#f8fafc;cursor:pointer;box-sizing:border-box;padding:0 10px;">'
                     . '<span class="wppoppop-upload-label-text">' . esc_html($prompt) . '</span>'
                     . '<input type="file" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" accept="' . $exts . '"' . $multi_attr .$data_attrs . ' style="display:none;" />'
                     . '</label>';

            case 'password':
                $show_eye = !isset($el['showEyeToggle']) || !empty($el['showEyeToggle']);
                $eye_btn =$show_eye ? '<button type="button" class="wppoppop-pw-eye-toggle" aria-label="Toggle Password Visibility" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:transparent;border:none;cursor:pointer;color:#64748b;font-size:14px;">👁</button>' : '';
                return '<div class="wppoppop-password-wrap" style="position:relative;width:100%;height:100%;">'
                     . '<input type="password" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($default_val) . '" placeholder="' . esc_attr($placeholder ?: 'Enter password...') . '" class="wppoppop-form-input wppoppop-input-password"' . $data_attrs . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 34px 0 12px;" />'
                     . $eye_btn . '</div>';

            case 'rating':
                $rmax = (!empty($el['ratingMax']) && intval($el['ratingMax']) === 10) ? 10 : 5;
                $rcol = !empty($el['ratingColor']) ? esc_attr($el['ratingColor']) : '#f59e0b';
                $rshape = !empty($el['ratingShape']) ? $el['ratingShape'] : 'star';$glyph = ($rshape === 'heart') ? '♥' : (($rshape === 'thumb') ? '👍' : '★');
                $init_score = $default_val !== '' ? intval($default_val) : 5;
                $stars_html = '';
                for ($i = 1; $i <= $rmax; $i++) {
                    $col = ($i <= $init_score) ?$rcol : '#cbd5e1';
                    $stars_html .= '<span class="wppoppop-rating-glyph" data-score="' . $i . '" data-active-color="' . $rcol . '" style="color:' . $col . ';font-size:20px;cursor:pointer;line-height:1;user-select:none;">' . $glyph . '</span>';
                }
                return '<div class="wppoppop-interactive-rating" data-active-color="' . $rcol . '" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;gap:4px;">'
                     . '<input type="hidden" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . $init_score . '"' . $data_attrs . ' />'
                     . $stars_html . '</div>';
        }

        return null;
    }
}
