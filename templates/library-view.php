<?php
if (!defined('ABSPATH')) {
    exit;
}

// Pre-configured Starter Template Catalog
$templates = [
    [
        'id'          => 'flash-sale-coupon',
        'category'    => 'ecommerce',
        'cat_label'   => 'E-Commerce',
        'title'       => 'Flash Sale 20% OFF Coupon',
        'description' => 'High-converting discount offer featuring headline, coupon tag, email capture, and urgency countdown.',
        'icon'        => 'dashicons-tag',
        'bg_preview'  => 'linear-gradient(135deg, #ec4899, #8b5cf6)',
        'config'      => [
            'meta'     => ['width' => 640, 'height' => 400, 'bg_mode' => 'solid', 'bg_color' => '#ffffff'],
            'triggers' => ['on_load' => true, 'on_load_delay' => 2, 'on_exit' => true],
            'elements' => [
                ['id' => 'el_1', 'type' => 'text', 'screen' => 1, 'left' => 40, 'top' => 40, 'width' => 560, 'height' => 40, 'z_index' => 1, 'content' => 'FLASH SALE: 20% OFF TODAY', 'color' => '#1e293b', 'font_size' => 24, 'font_family' => 'inherit'],
                ['id' => 'el_2', 'type' => 'countdown', 'screen' => 1, 'left' => 220, 'top' => 100, 'width' => 200, 'height' => 40, 'z_index' => 2, 'content' => 900],
                ['id' => 'el_3', 'type' => 'input', 'screen' => 1, 'left' => 120, 'top' => 170, 'width' => 400, 'height' => 45, 'z_index' => 3, 'content' => 'Enter your email for instant code...'],
                ['id' => 'el_4', 'type' => 'button', 'screen' => 1, 'left' => 220, 'top' => 240, 'width' => 200, 'height' => 45, 'z_index' => 4, 'content' => 'CLAIM 20% OFF', 'bg_color' => '#ec4899']
            ]
        ]
    ],
    [
        'id'          => 'lucky-prize-wheel',
        'category'    => 'gamified',
        'cat_label'   => 'Gamified',
        'title'       => 'Spin-to-Win Fortune Wheel',
        'description' => 'Interactive HTML5 spin wheel offering prizes, confetti shower, and dynamic prize coupon token capture.',
        'icon'        => 'dashicons-update',
        'bg_preview'  => 'linear-gradient(135deg, #f59e0b, #ef4444)',
        'config'      => [
            'meta'     => ['width' => 640, 'height' => 450, 'bg_mode' => 'solid', 'bg_color' => '#ffffff'],
            'triggers' => ['on_load' => true, 'on_load_delay' => 0],
            'elements' => [
                ['id' => 'el_w1', 'type' => 'wheel', 'screen' => 1, 'left' => 60, 'top' => 80, 'width' => 240, 'height' => 280, 'z_index' => 1, 'options' => ['10% OFF', 'FREE SHIP', '25% OFF', 'JACKPOT']],
                ['id' => 'el_w2', 'type' => 'text', 'screen' => 1, 'left' => 320, 'top' => 90, 'width' => 280, 'height' => 60, 'z_index' => 2, 'content' => 'Spin the Lucky Wheel to win exclusive discounts!', 'color' => '#1e293b', 'font_size' => 20],
                ['id' => 'el_w3', 'type' => 'input', 'screen' => 1, 'left' => 320, 'top' => 170, 'width' => 280, 'height' => 45, 'z_index' => 3, 'content' => 'Enter email to receive prize...'],
                ['id' => 'el_w4', 'type' => 'button', 'screen' => 1, 'left' => 320, 'top' => 230, 'width' => 280, 'height' => 45, 'z_index' => 4, 'content' => 'CLAIM MY PRIZE', 'bg_color' => '#f59e0b']
            ]
        ]
    ],
    [
        'id'          => 'scratch-win-promo',
        'category'    => 'gamified',
        'cat_label'   => 'Gamified',
        'title'       => 'Scratch & Win Mystery Card',
        'description' => 'Metallic canvas scratch foil hiding a secret discount code with instant audio success chimes.',
        'icon'        => 'dashicons-tickets-alt',
        'bg_preview'  => 'linear-gradient(135deg, #64748b, #334155)',
        'config'      => [
            'meta'     => ['width' => 640, 'height' => 400, 'bg_mode' => 'solid', 'bg_color' => '#ffffff'],
            'triggers' => ['on_exit' => true],
            'elements' => [
                ['id' => 'el_sc1', 'type' => 'text', 'screen' => 1, 'left' => 40, 'top' => 40, 'width' => 560, 'height' => 40, 'z_index' => 1, 'content' => 'Scratch the card to reveal your secret code!', 'color' => '#1e293b', 'font_size' => 20],
                ['id' => 'el_sc2', 'type' => 'scratch', 'screen' => 1, 'left' => 180, 'top' => 110, 'width' => 280, 'height' => 100, 'z_index' => 2, 'content' => 'MYSTERY50'],
                ['id' => 'el_sc3', 'type' => 'input', 'screen' => 1, 'left' => 180, 'top' => 230, 'width' => 280, 'height' => 45, 'z_index' => 3, 'content' => 'Your email...'],
                ['id' => 'el_sc4', 'type' => 'button', 'screen' => 1, 'left' => 180, 'top' => 290, 'width' => 280, 'height' => 45, 'z_index' => 4, 'content' => 'UNLOCK CODE', 'bg_color' => '#2563eb']
            ]
        ]
    ],
    [
        'id'          => 'newsletter-minimal',
        'category'    => 'lead-gen',
        'cat_label'   => 'Lead Generation',
        'title'       => 'Clean Newsletter Opt-In',
        'description' => 'Elegant, distraction-free email opt-in template tailored for blogs, SaaS companies, and content creators.',
        'icon'        => 'dashicons-email-alt',
        'bg_preview'  => 'linear-gradient(135deg, #2563eb, #1e40af)',
        'config'      => [
            'meta'     => ['width' => 580, 'height' => 360, 'bg_mode' => 'solid', 'bg_color' => '#ffffff'],
            'triggers' => ['on_scroll' => true, 'scroll_val' => 40],
            'elements' => [
                ['id' => 'el_nl1', 'type' => 'text', 'screen' => 1, 'left' => 40, 'top' => 40, 'width' => 500, 'height' => 36, 'z_index' => 1, 'content' => 'Stay Ahead with Weekly Insights', 'color' => '#1e293b', 'font_size' => 22],
                ['id' => 'el_nl2', 'type' => 'text', 'screen' => 1, 'left' => 40, 'top' => 85, 'width' => 500, 'height' => 45, 'z_index' => 2, 'content' => 'Join 25,000+ founders receiving curated growth playbooks every Tuesday.', 'color' => '#64748b', 'font_size' => 14],
                ['id' => 'el_nl3', 'type' => 'input', 'screen' => 1, 'left' => 60, 'top' => 160, 'width' => 460, 'height' => 45, 'z_index' => 3, 'content' => 'Enter your work email address...'],
                ['id' => 'el_nl4', 'type' => 'button', 'screen' => 1, 'left' => 180, 'top' => 230, 'width' => 220, 'height' => 45, 'z_index' => 4, 'content' => 'Subscribe Free', 'bg_color' => '#2563eb']
            ]
        ]
    ],
    [
        'id'          => 'survey-feedback',
        'category'    => 'feedback',
        'cat_label'   => 'Surveys & Feedback',
        'title'       => '5-Star Feedback & Rating Survey',
        'description' => 'Collect user satisfaction scores, star ratings, and optional commentary before visitors leave.',
        'icon'        => 'dashicons-star-filled',
        'bg_preview'  => 'linear-gradient(135deg, #10b981, #047857)',
        'config'      => [
            'meta'     => ['width' => 600, 'height' => 380, 'bg_mode' => 'solid', 'bg_color' => '#ffffff'],
            'triggers' => ['on_exit' => true],
            'elements' => [
                ['id' => 'el_fb1', 'type' => 'text', 'screen' => 1, 'left' => 40, 'top' => 30, 'width' => 520, 'height' => 36, 'z_index' => 1, 'content' => 'How would you rate your experience?', 'color' => '#1e293b', 'font_size' => 22],
                ['id' => 'el_fb2', 'type' => 'rating', 'screen' => 1, 'left' => 210, 'top' => 80, 'width' => 180, 'height' => 40, 'z_index' => 2],
                ['id' => 'el_fb3', 'type' => 'dropdown', 'screen' => 1, 'left' => 100, 'top' => 140, 'width' => 400, 'height' => 40, 'z_index' => 3, 'options' => ['Very Satisfied', 'Neutral', 'Needs Improvement']],
                ['id' => 'el_fb4', 'type' => 'input', 'screen' => 1, 'left' => 100, 'top' => 200, 'width' => 400, 'height' => 45, 'z_index' => 4, 'content' => 'Optional: Your email address...'],
                ['id' => 'el_fb5', 'type' => 'button', 'screen' => 1, 'left' => 200, 'top' => 270, 'width' => 200, 'height' => 45, 'z_index' => 5, 'content' => 'Submit Feedback', 'bg_color' => '#10b981']
            ]
        ]
    ],
    [
        'id'          => 'urgency-countdown',
        'category'    => 'ecommerce',
        'cat_label'   => 'E-Commerce',
        'title'       => 'Urgency Countdown Timer Banner',
        'description' => 'Drive instant purchase decisions with a live 15-minute countdown and single-use checkout code.',
        'icon'        => 'dashicons-clock',
        'bg_preview'  => 'linear-gradient(135deg, #1e293b, #0f172a)',
        'config'      => [
            'meta'     => ['width' => 640, 'height' => 380, 'bg_mode' => 'solid', 'bg_color' => '#ffffff'],
            'triggers' => ['on_load' => true, 'on_load_delay' => 1],
            'elements' => [
                ['id' => 'el_uc1', 'type' => 'text', 'screen' => 1, 'left' => 40, 'top' => 40, 'width' => 560, 'height' => 40, 'z_index' => 1, 'content' => 'Your Exclusive Offer Expires In:', 'color' => '#1e293b', 'font_size' => 22],
                ['id' => 'el_uc2', 'type' => 'countdown', 'screen' => 1, 'left' => 200, 'top' => 100, 'width' => 240, 'height' => 50, 'z_index' => 2, 'content' => 600],
                ['id' => 'el_uc3', 'type' => 'text', 'screen' => 1, 'left' => 40, 'top' => 170, 'width' => 560, 'height' => 30, 'z_index' => 3, 'content' => 'Use code RUSH25 at checkout for 25% off all orders.', 'color' => '#475569', 'font_size' => 15],
                ['id' => 'el_uc4', 'type' => 'button', 'screen' => 1, 'left' => 200, 'top' => 230, 'width' => 240, 'height' => 45, 'z_index' => 4, 'content' => 'Shop Now &rarr;', 'bg_color' => '#0284c7']
            ]
        ]
    ]
];
?>
<div class="wrap wppoppop-library-wrap" style="max-width:1200px;">
    <!-- Catalog Header Toolbar & Category Filters -->
    <?php include WPPOPPOP_PATH . 'templates/library/header.php'; ?>

    <!-- Responsive Template Cards Grid -->
    <?php include WPPOPPOP_PATH . 'templates/library/grid.php'; ?>

    <!-- Template Inspection & Preview Modal -->
    <?php include WPPOPPOP_PATH . 'templates/library/modal-preview.php'; ?>
</div>
