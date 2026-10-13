<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Element_Typography {

    public static function register() {
        WpPopPop_Ajax_Element_Registry::register('title', [__CLASS__, 'sanitize_title']);
        WpPopPop_Ajax_Element_Registry::register('paragraph', [__CLASS__, 'sanitize_paragraph']);
        WpPopPop_Ajax_Element_Registry::register('text', [__CLASS__, 'sanitize_paragraph']);
    }

    public static function sanitize_title(array $el, array $clean) {
        if (isset($el['titleText']))     $clean['titleText']     = sanitize_text_field($el['titleText']);
        if (isset($el['headingTag']))    $clean['headingTag']    = sanitize_key($el['headingTag']);
        if (isset($el['titleTag']))      $clean['titleTag']      = sanitize_key($el['titleTag']);
        if (isset($el['tag']))           $clean['tag']           = sanitize_key($el['tag']);
        if (isset($el['textTransform'])) $clean['textTransform'] = sanitize_text_field($el['textTransform']);
        if (isset($el['lineHeight']))    $clean['lineHeight']    = sanitize_text_field($el['lineHeight']);
        if (isset($el['letterSpacing'])) $clean['letterSpacing'] = sanitize_text_field($el['letterSpacing']);
        return $clean;
    }

    public static function sanitize_paragraph(array $el, array $clean) {
        if (isset($el['tag']))           $clean['tag']           = sanitize_key($el['tag']);
        if (isset($el['lineHeight']))    $clean['lineHeight']    = sanitize_text_field($el['lineHeight']);
        if (isset($el['letterSpacing'])) $clean['letterSpacing'] = sanitize_text_field($el['letterSpacing']);
        return $clean;
    }
}
