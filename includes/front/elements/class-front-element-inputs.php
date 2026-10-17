<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Element_Inputs {

    public static function register() {
        WpPopPop_Front_Element_Registry::register('textfield', [__CLASS__, 'render_textfield']);
        WpPopPop_Front_Element_Registry::register('email', [__CLASS__, 'render_email']);
        WpPopPop_Front_Element_Registry::register('number', [__CLASS__, 'render_number']);
        WpPopPop_Front_Element_Registry::register('select', [__CLASS__, 'render_select']);
        WpPopPop_Front_Element_Registry::register('dropdown', [__CLASS__, 'render_select']);
        WpPopPop_Front_Element_Registry::register('radios', [__CLASS__, 'render_radios']);
        WpPopPop_Front_Element_Registry::register('radio', [__CLASS__, 'render_radios']);
        WpPopPop_Front_Element_Registry::register('checkboxes', [__CLASS__, 'render_checkboxes']);
        WpPopPop_Front_Element_Registry::register('checkbox', [__CLASS__, 'render_checkboxes']);
        WpPopPop_Front_Element_Registry::register('date', [__CLASS__, 'render_date']);
        WpPopPop_Front_Element_Registry::register('file', [__CLASS__, 'render_file']);
    }

    public static function render_textfield(array $el, array $config = []) {
        $field_name = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'text_field';
        $placeholder = isset($el['placeholder']) ? esc_attr($el['placeholder']) : '';
        $default_val = isset($el['content']) ? esc_attr($el['content']) : '';
        $required = !empty($el['required']) ? 'required' : '';

        return sprintf(
            '<input type="text" name="%1$s" placeholder="%2$s" value="%3$s" %4$s class="wppoppop-front-input" style="width:100%%;height:100%%;border:inherit;border-radius:inherit;background:inherit;color:inherit;font-size:inherit;font-family:inherit;padding:0 12px;box-sizing:border-box;outline:none;">',
            esc_attr($field_name),
            $placeholder,
            $default_val,
            $required
        );
    }

    public static function render_email(array $el, array $config = []) {
        $field_name = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'email';
        $placeholder = isset($el['placeholder']) ? esc_attr($el['placeholder']) : 'Enter your email...';
        $default_val = isset($el['content']) ? esc_attr($el['content']) : '';
        $required = isset($el['required']) && !$el['required'] ? '' : 'required';

        return sprintf(
            '<input type="email" name="%1$s" placeholder="%2$s" value="%3$s" %4$s class="wppoppop-front-input" style="width:100%%;height:100%%;border:inherit;border-radius:inherit;background:inherit;color:inherit;font-size:inherit;font-family:inherit;padding:0 12px;box-sizing:border-box;outline:none;">',
            esc_attr($field_name),
            $placeholder,
            $default_val,
            $required
        );
    }

    public static function render_number(array $el, array $config = []) {
        $field_name = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'number_field';
        $min = isset($el['min']) ? floatval($el['min']) : 0;
        $max = isset($el['max']) ? floatval($el['max']) : 100;
        $step = !empty($el['step']) ? floatval($el['step']) : 1;
        $val = isset($el['content']) && $el['content'] !== '' ? esc_attr($el['content']) : $min;
        $placeholder = isset($el['placeholder']) ? esc_attr($el['placeholder']) : '';
        $required = !empty($el['required']) ? 'required' : '';

        return sprintf(
            '<input type="number" name="%1$s" min="%2$s" max="%3$s" step="%4$s" value="%5$s" placeholder="%6$s" %7$s class="wppoppop-front-input" style="width:100%%;height:100%%;border:inherit;border-radius:inherit;background:inherit;color:inherit;font-size:inherit;padding:0 12px;box-sizing:border-box;outline:none;">',
            esc_attr($field_name),
            $min,
            $max,
            $step,
            $val,
            $placeholder,
            $required
        );
    }

    public static function render_select(array $el, array $config = []) {
        $field_name = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'select_field';
        $placeholder = isset($el['placeholder']) ? esc_html($el['placeholder']) : '-- Select --';
        $default_val = isset($el['default_value']) ? trim($el['default_value']) : '';
        $required = !empty($el['required']) ? 'required' : '';
        $raw = isset($el['content']) ? $el['content'] : 'Option 1, Option 2, Option 3';
        $items = strpos($raw, "\n") !== false ? explode("\n", $raw) : explode(',', $raw);

        $html = sprintf(
            '<select name="%1$s" %2$s class="wppoppop-front-select" style="width:100%%;height:100%%;border:inherit;border-radius:inherit;background:inherit;color:inherit;font-size:inherit;padding:0 12px;box-sizing:border-box;outline:none;">',
            esc_attr($field_name),
            $required
        );

        if ($placeholder) {
            $html .= '<option value="">' . $placeholder . '</option>';
        }

        foreach ($items as $item) {
            $v = trim($item);
            if ($v !== '') {
                $sel = ($default_val === $v) ? ' selected' : '';
                $html .= '<option value="' . esc_attr($v) . '"' . $sel . '>' . esc_html($v) . '</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }

    public static function render_radios(array $el, array $config = []) {
        $field_name = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'radio_group';
        $default_val = isset($el['default_value']) ? trim($el['default_value']) : '';
        $is_stacked = (!empty($el['layout']) && $el['layout'] === 'stacked');
        $flex_dir = $is_stacked ? 'column' : 'row';
        $gap = $is_stacked ? '6px' : '14px';
        $raw = isset($el['content']) ? $el['content'] : 'Option 1, Option 2';
        $items = strpos($raw, "\n") !== false ? explode("\n", $raw) : explode(',', $raw);

        $html = sprintf(
            '<div class="wppoppop-front-radios" style="display:flex;flex-direction:%1$s;gap:%2$s;align-items:%3$s;width:100%%;height:100%%;padding:0 8px;box-sizing:border-box;">',
            $flex_dir,
            $gap,
            $is_stacked ? 'flex-start' : 'center'
        );

        foreach ($items as $idx => $item) {
            $v = trim($item);
            if ($v !== '') {
                $checked = ($default_val === $v || (!$default_val && $idx === 0)) ? ' checked' : '';
                $html .= sprintf(
                    '<label style="display:flex;align-items:center;gap:6px;cursor:pointer;margin:0;"><input type="radio" name="%1$s" value="%2$s"%3$s> <span>%4$s</span></label>',
                    esc_attr($field_name),
                    esc_attr($v),
                    $checked,
                    esc_html($v)
                );
            }
        }
        $html .= '</div>';
        return $html;
    }

    public static function render_checkboxes(array $el, array $config = []) {
        $field_name = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'checkbox_consent';
        $text = isset($el['content']) ? $el['content'] : 'I agree to the terms';
        $checked = !empty($el['checked']) ? ' checked' : '';
        $required = !empty($el['required']) ? 'required' : '';

        return sprintf(
            '<label class="wppoppop-front-checkbox" style="display:flex;align-items:center;gap:8px;cursor:pointer;width:100%%;height:100%%;padding:0 8px;box-sizing:border-box;"><input type="checkbox" name="%1$s" value="1"%2$s %3$s> <span style="line-height:1.2;">%4$s</span></label>',
            esc_attr($field_name),
            $checked,
            $required,
            wp_kses_post($text)
        );
    }

    public static function render_date(array $el, array $config = []) {
        $field_name = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'date_field';
        $format = !empty($el['dateFormat']) ? esc_attr($el['dateFormat']) : 'YYYY-MM-DD';
        $placeholder = !empty($el['placeholder']) ? esc_attr($el['placeholder']) : $format;
        $min = !empty($el['minDate']) ? esc_attr($el['minDate']) : '';
        $max = !empty($el['maxDate']) ? esc_attr($el['maxDate']) : '';
        $required = !empty($el['required']) ? 'required' : '';

        return sprintf(
            '<input type="text" name="%1$s" placeholder="%2$s" data-format="%3$s" data-min="%4$s" data-max="%5$s" %6$s class="wppoppop-front-date wppoppop-datepicker" style="width:100%%;height:100%%;border:inherit;border-radius:inherit;background:inherit;color:inherit;font-size:inherit;padding:0 12px;box-sizing:border-box;outline:none;">',
            esc_attr($field_name),
            $placeholder,
            $format,
            $min,
            $max,
            $required
        );
    }

    public static function render_file(array $el, array $config = []) {
        $field_name = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'file_upload';
        $label = !empty($el['label_text']) ? esc_html($el['label_text']) : 'Choose File';
        $exts = !empty($el['fileExts']) ? esc_attr($el['fileExts']) : '.pdf, .jpg, .png';
        $max_size = !empty($el['max_size_mb']) ? intval($el['max_size_mb']) : 10;
        $required = !empty($el['required']) ? 'required' : '';

        return sprintf(
            '<div class="wppoppop-front-file-wrap" style="position:relative;width:100%%;height:100%%;overflow:hidden;border:inherit;border-radius:inherit;background:inherit;color:inherit;"><input type="file" name="%1$s" accept="%2$s" data-max-size="%3$s" %4$s style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%%;height:100%%;z-index:2;"><div style="width:100%%;height:100%%;display:flex;align-items:center;justify-content:center;gap:6px;padding:0 10px;box-sizing:border-box;pointer-events:none;"><span>📁</span> <span>%5$s</span> <small style="opacity:0.75;font-size:10px;">(%2$s)</small></div></div>',
            esc_attr($field_name),
            $exts,
            $max_size,
            $required,
            $label
        );
    }
}
