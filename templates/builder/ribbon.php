<?php
/**
 * Builder Ribbon Toolbar - GreenPop Complete Element Palette Parity
 * Categorized into 5 functional groups across all 26 elements.
 */
if (!defined('ABSPATH')) exit;
?>
<div class="wppoppop-ribbon-bar" id="wppoppop-ribbon-bar">
    <!-- Category: Content & Typography -->
    <div class="wppoppop-ribbon-group" data-group="content">
        <span class="wppoppop-ribbon-group-title"><?php esc_html_e('Content', 'wppoppop'); ?></span>
        <div class="wppoppop-ribbon-items">
            <button type="button" class="wppoppop-ribbon-btn" data-type="title" title="<?php esc_attr_e('Heading / Title', 'wppoppop'); ?>">
                <span class="dashicons dashicons-heading"></span>
                <span class="btn-label"><?php esc_html_e('Title', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="paragraph" title="<?php esc_attr_e('Paragraph / Text Block', 'wppoppop'); ?>">
                <span class="dashicons dashicons-editor-paragraph"></span>
                <span class="btn-label"><?php esc_html_e('Text', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="html" title="<?php esc_attr_e('Custom HTML / Embed', 'wppoppop'); ?>">
                <span class="dashicons dashicons-editor-code"></span>
                <span class="btn-label"><?php esc_html_e('HTML', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="divider" title="<?php esc_attr_e('Horizontal / Vertical Divider', 'wppoppop'); ?>">
                <span class="dashicons dashicons-minus"></span>
                <span class="btn-label"><?php esc_html_e('Divider', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="icon" title="<?php esc_attr_e('Vector Icon Badge', 'wppoppop'); ?>">
                <span class="dashicons dashicons-star-filled"></span>
                <span class="btn-label"><?php esc_html_e('Icon', 'wppoppop'); ?></span>
            </button>
        </div>
    </div>

    <!-- Category: Form Inputs -->
    <div class="wppoppop-ribbon-group" data-group="inputs">
        <span class="wppoppop-ribbon-group-title"><?php esc_html_e('Form Inputs', 'wppoppop'); ?></span>
        <div class="wppoppop-ribbon-items">
            <button type="button" class="wppoppop-ribbon-btn" data-type="textfield" title="<?php esc_attr_e('Single-Line Text Input', 'wppoppop'); ?>">
                <span class="dashicons dashicons-forms"></span>
                <span class="btn-label"><?php esc_html_e('Text Field', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="email" title="<?php esc_attr_e('Email Address Input', 'wppoppop'); ?>">
                <span class="dashicons dashicons-email-alt"></span>
                <span class="btn-label"><?php esc_html_e('Email', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="number" title="<?php esc_attr_e('Numeric Stepper Input', 'wppoppop'); ?>">
                <span class="dashicons dashicons-calculator"></span>
                <span class="btn-label"><?php esc_html_e('Number', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="select" title="<?php esc_attr_e('Dropdown Select Menu', 'wppoppop'); ?>">
                <span class="dashicons dashicons-menu"></span>
                <span class="btn-label"><?php esc_html_e('Dropdown', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="radios" title="<?php esc_attr_e('Radio Choice Buttons', 'wppoppop'); ?>">
                <span class="dashicons dashicons-marker"></span>
                <span class="btn-label"><?php esc_html_e('Radios', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="checkboxes" title="<?php esc_attr_e('Checkboxes / Consent Toggle', 'wppoppop'); ?>">
                <span class="dashicons dashicons-yes"></span>
                <span class="btn-label"><?php esc_html_e('Checkbox', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="date" title="<?php esc_attr_e('Calendar Datepicker', 'wppoppop'); ?>">
                <span class="dashicons dashicons-calendar-alt"></span>
                <span class="btn-label"><?php esc_html_e('Date', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="file" title="<?php esc_attr_e('File Attachment Upload', 'wppoppop'); ?>">
                <span class="dashicons dashicons-upload"></span>
                <span class="btn-label"><?php esc_html_e('File', 'wppoppop'); ?></span>
            </button>
        </div>
    </div>

    <!-- Category: Media & Graphics -->
    <div class="wppoppop-ribbon-group" data-group="media">
        <span class="wppoppop-ribbon-group-title"><?php esc_html_e('Media & Shapes', 'wppoppop'); ?></span>
        <div class="wppoppop-ribbon-items">
            <button type="button" class="wppoppop-ribbon-btn" data-type="image" title="<?php esc_attr_e('WP Media Image', 'wppoppop'); ?>">
                <span class="dashicons dashicons-format-image"></span>
                <span class="btn-label"><?php esc_html_e('Image', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="video" title="<?php esc_attr_e('Video Player (YouTube/Vimeo)', 'wppoppop'); ?>">
                <span class="dashicons dashicons-video-alt3"></span>
                <span class="btn-label"><?php esc_html_e('Video', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="shape" title="<?php esc_attr_e('Vector SVG Shape (11 Presets)', 'wppoppop'); ?>">
                <span class="dashicons dashicons-art"></span>
                <span class="btn-label"><?php esc_html_e('Shape', 'wppoppop'); ?></span>
            </button>
        </div>
    </div>

    <!-- Category: Interactive & Gamification -->
    <div class="wppoppop-ribbon-group" data-group="gamification">
        <span class="wppoppop-ribbon-group-title"><?php esc_html_e('Interactive', 'wppoppop'); ?></span>
        <div class="wppoppop-ribbon-items">
            <button type="button" class="wppoppop-ribbon-btn" data-type="wheel" title="<?php esc_attr_e('Lucky Prize Wheel', 'wppoppop'); ?>">
                <span class="dashicons dashicons-update"></span>
                <span class="btn-label"><?php esc_html_e('Wheel', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="scratch" title="<?php esc_attr_e('Scratch Card Foil', 'wppoppop'); ?>">
                <span class="dashicons dashicons-hidden"></span>
                <span class="btn-label"><?php esc_html_e('Scratch', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="rating" title="<?php esc_attr_e('Star Rating Gauge', 'wppoppop'); ?>">
                <span class="dashicons dashicons-star-half"></span>
                <span class="btn-label"><?php esc_html_e('Rating', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="slider" title="<?php esc_attr_e('Range Slider Gauge', 'wppoppop'); ?>">
                <span class="dashicons dashicons-leftright"></span>
                <span class="btn-label"><?php esc_html_e('Slider', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="signature" title="<?php esc_attr_e('Digital Signature Canvas', 'wppoppop'); ?>">
                <span class="dashicons dashicons-edit"></span>
                <span class="btn-label"><?php esc_html_e('Signature', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="countdown" title="<?php esc_attr_e('Urgency Countdown Timer', 'wppoppop'); ?>">
                <span class="dashicons dashicons-clock"></span>
                <span class="btn-label"><?php esc_html_e('Timer', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="progress" title="<?php esc_attr_e('Step Progress Bar', 'wppoppop'); ?>">
                <span class="dashicons dashicons-performance"></span>
                <span class="btn-label"><?php esc_html_e('Progress', 'wppoppop'); ?></span>
            </button>
        </div>
    </div>

    <!-- Category: Actions & Navigation -->
    <div class="wppoppop-ribbon-group" data-group="actions">
        <span class="wppoppop-ribbon-group-title"><?php esc_html_e('Actions', 'wppoppop'); ?></span>
        <div class="wppoppop-ribbon-items">
            <button type="button" class="wppoppop-ribbon-btn" data-type="submit" title="<?php esc_attr_e('Form Submit Button', 'wppoppop'); ?>">
                <span class="dashicons dashicons-yes-alt"></span>
                <span class="btn-label"><?php esc_html_e('Submit', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="step" title="<?php esc_attr_e('Next Step / Canvas Button', 'wppoppop'); ?>">
                <span class="dashicons dashicons-controls-forward"></span>
                <span class="btn-label"><?php esc_html_e('Next Step', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="link" title="<?php esc_attr_e('CTA Outbound Link Button', 'wppoppop'); ?>">
                <span class="dashicons dashicons-admin-links"></span>
                <span class="btn-label"><?php esc_html_e('Link', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="pay" title="<?php esc_attr_e('Payment Checkout Button', 'wppoppop'); ?>">
                <span class="dashicons dashicons-cart"></span>
                <span class="btn-label"><?php esc_html_e('Pay', 'wppoppop'); ?></span>
            </button>
            <button type="button" class="wppoppop-ribbon-btn" data-type="close" title="<?php esc_attr_e('Dismiss / Close Trigger', 'wppoppop'); ?>">
                <span class="dashicons dashicons-no-alt"></span>
                <span class="btn-label"><?php esc_html_e('Close', 'wppoppop'); ?></span>
            </button>
        </div>
    </div>
</div>
