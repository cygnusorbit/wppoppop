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

            case 'phone':
            case 'tel':
                $prefix = !empty($el['phonePrefix']) ? trim($el['phonePrefix']) . ' ' : '';
                $ph = $placeholder ?: ($prefix . '(555) 000-0000');
                return '<input type="tel" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($default_val) . '" placeholder="' . esc_attr($ph) . '" class="wppoppop-form-input wppoppop-input-phone"' . $data_attrs . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 12px;" />';

            case 'textarea':
                $rows = !empty($el['rows']) ? max(2, intval($el['rows'])) : 4;
                return '<textarea id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" rows="' . $rows . '" placeholder="' . esc_attr($placeholder ?: 'Write your message...') . '" class="wppoppop-form-input wppoppop-input-textarea"' . $data_attrs . ' style="width:100%;height:100%;box-sizing:border-box;padding:8px 12px;resize:none;">' . esc_textarea($default_val) . '</textarea>';

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
