<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Backend AJAX Element Sanitization Registry
 * Modular schema validation for builder element payloads
 */
class WpPopPop_Ajax_Element_Registry {

    /**
     * Sanitizer map: type => callable
     */
    private static $sanitizers = [];

    /**
     * Initialization flag
     */
    private static $initialized = false;

    /**
     * Bootstrap domain sanitizers
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }

        $dir = dirname(__FILE__);
        require_once $dir . '/class-ajax-element-typography.php';
        require_once $dir . '/class-ajax-element-inputs.php';
        require_once $dir . '/class-ajax-element-actions.php';
        require_once $dir . '/class-ajax-element-gamification.php';
        require_once $dir . '/class-ajax-element-media.php';

        WpPopPop_Ajax_Element_Typography::register();
        WpPopPop_Ajax_Element_Inputs::register();
        WpPopPop_Ajax_Element_Actions::register();
        WpPopPop_Ajax_Element_Gamification::register();
        WpPopPop_Ajax_Element_Media::register();

        self::$initialized = true;
    }

    /**
     * Register a type sanitizer
     */
    public static function register($type, callable $sanitizer) {
        $type = strtolower(trim($type));
        self::$sanitizers[$type] = $sanitizer;
    }

    /**
     * Check if type is registered
     */
    public static function has($type) {
        self::init();
        $type = strtolower(trim($type));
        return isset(self::$sanitizers[$type]);
    }

    /**
     * Sanitize element payload using registered domain schema
     */
    public static function sanitize($type, array $el, array $clean) {
        self::init();
        $type = strtolower(trim($type));
        if (isset(self::$sanitizers[$type]) && is_callable(self::$sanitizers[$type])) {
            return call_user_func(self::$sanitizers[$type], $el, $clean);
        }
        return $clean;
    }

    public static function sanitize_decomposed_layer_props( array $el, array $clean ) {$text_keys = array(
            'name', 'autocomplete', 'animIn', 'animOut', 'animation',
            'fontFamily', 'fontWeight', 'textAlign', 'color', 'bgColor',
            'borderStyle', 'borderColor', 'shadowColor', 'hoverBg', 'hoverColor', 'hoverBorder',
            'iconLeft', 'iconRight', 'customClass', 'cssClass', 'customId',
            'logicAction', 'logicMatch', 'defaultValue', 'paramName',
            'fieldId', 'fieldName', 'token', 'inputMask',
            'shapePreset', 'strokeColor', 'alt', 'objectFit', 'imageAction',
            'videoProvider', 'aspectRatio', 'videoUrl', 'tag', 'headingTag', 'htmlTag', 'textTransform', 'overflow',
            'closeSymbol', 'closeAction', 'closeGlyph', 'hoverBgColor', 'hoverTextColor', 'hoverBorderColor', 'hoverBg', 'hoverColor', 'hoverBorder', 'iconClass', 'progressMode', 'progressLabel', 'fillColor', 'trackColor', 'progressColor',
            'buttonIcon', 'buttonAction', 'url', 'loadingText', 'submitAction', 'confirmMessage', 'redirectUrl',
            'placeholder', 'countryCode', 'checkboxLabel', 'checkboxLayout', 'radioLayout',
            'activeColor', 'dateFormat', 'minDate', 'maxDate', 'timeFormat', 'allowedExtensions',
            'ratingShape', 'ratingColor', 'requiredMessage', 'errorMessage', 'validatorEqual', 'validatorList', 'minValue', 'maxValue', 'stepValue', 'unitSuffix'
        );
        foreach ( $text_keys as$k ) {
            if ( isset( $el[$k ] ) && ! is_array( $el[$k ] ) ) {
                $clean[$k ] = sanitize_text_field( wp_unslash( (string) $el[$k ] ) );
            }
        }

        $html_keys = array( 'content', 'options', 'titleText', 'paragraphText', 'text', 'label' );
        foreach ( $html_keys as$hk ) {
            if ( isset( $el[$hk ] ) && ! is_array( $el[$hk ] ) ) {
                $clean[$hk ] = wp_kses_post( wp_unslash( (string) $el[$hk ] ) );
            }
        }

        $num_keys = array(
            'x', 'y', 'left', 'top', 'width', 'height', 'w', 'h', 'opacity',
            'fontSize', 'lineHeight', 'letterSpacing',
            'padTop', 'padRight', 'padBottom', 'padLeft',
            'borderWidth', 'borderRadius', 'shadowX', 'shadowY', 'shadowBlur', 'shadowSpread',
            'animDuration', 'animDelay', 'iconLeftOffset', 'iconRightOffset',
            'strokeWidth', 'rotate', 'iconSize', 'cookieDays', 'progressValue', 'progressPercent', 'progressVal', 'gotoCanvas', 'goto_canvas', 'goto_screen',
            'maxLength', 'minLength', 'min', 'max', 'step', 'rows',
            'maxSelect', 'columns', 'timeStep', 'maxFileSize', 'ratingMax'
        );
        foreach ( $num_keys as$nk ) {
            if ( isset( $el[$nk ] ) && $el[$nk ] !== '' && ! is_array( $el[$nk ] ) ) {
                $clean[$nk ] = is_numeric( $el[$nk ] ) ? ( strpos( (string) $el[$nk ], '.' ) !== false ? floatval( $el[$nk ] ) : intval( $el[$nk ] ) ) : 0;
            }
        }

        $bool_keys = array(
            'required', 'transparentFill', 'targetBlank', 'linkBlank', 'nofollow',
            'autoplay', 'muted', 'loop', 'controls', 'showPercentLabel', 'striped', 'progressStriped', 'progressAnimated',
            'checkedDefault', 'multiSelect', 'multiple', 'showEyeToggle',
            'logicEnabled', 'dynamicDefault', 'readOnly', 'saveToDb', 'sizePresetApplied', 'blockDisposable'
        );
        foreach ( $bool_keys as$bk ) {
            if ( isset( $el[$bk ] ) ) {
                $clean[$bk ] = ! empty( $el[$bk ] ) && $el[$bk ] !== 'false';
            }
        }

        if ( isset( $el['inlineStyles'] ) && is_array( $el['inlineStyles'] ) ) {$clean['inlineStyles'] = array();
            foreach ( $el['inlineStyles'] as$st ) {
                if ( is_array( $st ) ) {$clean['inlineStyles'][] = array(
                        'prop'  => isset( $st['prop'] ) ? sanitize_text_field( $st['prop'] ) : '',
                        'value' => isset( $st['value'] ) ? sanitize_text_field( $st['value'] ) : '',
                    );
                }
            }
        }

        if ( isset( $el['logicRules'] ) && is_array( $el['logicRules'] ) ) {$clean['logicRules'] = array();
            foreach ( $el['logicRules'] as$rl ) {
                if ( is_array( $rl ) ) {$clean['logicRules'][] = array(
                        'field'    => isset( $rl['field'] ) ? sanitize_text_field( $rl['field'] ) : '',
                        'operator' => isset( $rl['operator'] ) ? sanitize_text_field( $rl['operator'] ) : 'is',
                        'value'    => isset( $rl['value'] ) ? sanitize_text_field( $rl['value'] ) : '',
                    );
                }
            }
        }

        foreach ( array( 'filters', 'validators' ) as $ak ) {
            if ( isset( $el[$ak ] ) && is_array( $el[$ak ] ) ) {
                $clean[$ak ] = array_values( array_map( 'sanitize_text_field', $el[$ak ] ) );
            }
        }

        return $clean;
    }

}
