<?php
if (!defined('ABSPATH')) exit;

/**
 * Decomposed 26-Element Frontend Renderer Adapter
 * Renders visitor HTML + Data/Advanced/Logic attributes for newly standardized elements.
 */
class WpPopPop_Front_Decomposed_26 {

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
                return '<input type="text" id="' . $field_id . '" name="fields[' . esc_attr($field_name) . ']" data-field-name="' . esc_attr($field_name) . '" value="' . esc_attr($default_val) . '" placeholder="' . esc_attr($placeholder ?: 'Enter text...') . '" class="wppoppop-form-input wppoppop-input-text"' . $data_attrs . ' style="width:100%;height:100%;box-sizing:border-box;padding:0 12px;" />';

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
