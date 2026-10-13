<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Element_Inputs {

    public static function register() {
        $types = ['textfield', 'email', 'number', 'select', 'dropdown', 'radios', 'radio', 'checkboxes', 'checkbox', 'date', 'file'];
        foreach ($types as $t) {
            WpPopPop_Ajax_Element_Registry::register($t, [__CLASS__, 'sanitize_input']);
        }
    }

    public static function sanitize_input(array $el, array $clean) {
        if (isset($el['placeholder']))   $clean['placeholder']   = sanitize_text_field($el['placeholder']);
        if (isset($el['field_name']))    $clean['field_name']    = sanitize_key($el['field_name']);
        if (isset($el['fieldName']))     $clean['fieldName']     = sanitize_key($el['fieldName']);
        if (isset($el['required']))      $clean['required']      = (bool)$el['required'];
        if (isset($el['min']))           $clean['min']           = floatval($el['min']);
        if (isset($el['max']))           $clean['max']           = floatval($el['max']);
        if (isset($el['step']))          $clean['step']          = floatval($el['step']);
        if (isset($el['default_value'])) $clean['default_value'] = sanitize_text_field($el['default_value']);
        if (isset($el['layout']))        $clean['layout']        = sanitize_text_field($el['layout']);
        if (isset($el['checked']))       $clean['checked']       = (bool)$el['checked'];
        if (isset($el['dateFormat']))    $clean['dateFormat']    = sanitize_text_field($el['dateFormat']);
        if (isset($el['minDate']))       $clean['minDate']       = sanitize_text_field($el['minDate']);
        if (isset($el['maxDate']))       $clean['maxDate']       = sanitize_text_field($el['maxDate']);
        if (isset($el['fileExts']))      $clean['fileExts']      = sanitize_text_field($el['fileExts']);
        if (isset($el['max_size_mb']))   $clean['max_size_mb']   = intval($el['max_size_mb']);
        if (isset($el['label_text']))    $clean['label_text']    = sanitize_text_field($el['label_text']);
        return $clean;
    }
}
