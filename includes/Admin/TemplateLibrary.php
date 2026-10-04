<?php
namespace WPPopPop\Admin;

class TemplateLibrary {
    public static function get_template_layers(string $template_id): string {
        switch ($template_id) {
            case '27124': // Fashawn Style Minimalist
                $layers = [
                    [
                        'type'     => 'rectangle',
                        'content'  => '',
                        'x'        => 0,
                        'y'        => 0,
                        'w'        => 640,
                        'h'        => 400,
                        'z'        => 1,
                        'color'    => '#111827',
                        'bg'       => '#ffffff',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'ribbon',
                        'content'  => 'NEW COLLECTION 2026',
                        'x'        => 40,
                        'y'        => 40,
                        'w'        => 160,
                        'h'        => 26,
                        'z'        => 2,
                        'color'    => '#b5295c',
                        'bg'       => '#fce7f3',
                        'fontSize' => 11,
                        'radius'   => 13,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'text',
                        'content'  => 'Get 25% Off Your First Order',
                        'x'        => 40,
                        'y'        => 80,
                        'w'        => 420,
                        'h'        => 40,
                        'z'        => 3,
                        'color'    => '#111827',
                        'bg'       => 'transparent',
                        'fontSize' => 24,
                        'radius'   => 0,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'text',
                        'content'  => 'Join our VIP styling club to unlock private flash sales and curated arrivals.',
                        'x'        => 40,
                        'y'        => 130,
                        'w'        => 480,
                        'h'        => 42,
                        'z'        => 4,
                        'color'    => '#6b7280',
                        'bg'       => 'transparent',
                        'fontSize' => 14,
                        'radius'   => 0,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'input',
                        'content'  => 'Full Name',
                        'x'        => 40,
                        'y'        => 190,
                        'w'        => 340,
                        'h'        => 42,
                        'z'        => 5,
                        'color'    => '#374151',
                        'bg'       => '#ffffff',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'email',
                        'content'  => 'Email Address',
                        'x'        => 40,
                        'y'        => 245,
                        'w'        => 340,
                        'h'        => 42,
                        'z'        => 6,
                        'color'    => '#374151',
                        'bg'       => '#ffffff',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'submit',
                        'content'  => 'Claim My 25% Discount',
                        'x'        => 40,
                        'y'        => 305,
                        'w'        => 240,
                        'h'        => 44,
                        'z'        => 7,
                        'color'    => '#ffffff',
                        'bg'       => '#b5295c',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ]
                ];
                break;

            case '27082': // Purple Gradient Optin
                $layers = [
                    [
                        'type'     => 'rectangle',
                        'content'  => '',
                        'x'        => 0,
                        'y'        => 0,
                        'w'        => 640,
                        'h'        => 400,
                        'z'        => 1,
                        'color'    => '#ffffff',
                        'bg'       => '#581c87',
                        'fontSize' => 14,
                        'radius'   => 6,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'text',
                        'content'  => "Let's talk growth.",
                        'x'        => 50,
                        'y'        => 60,
                        'w'        => 380,
                        'h'        => 44,
                        'z'        => 2,
                        'color'    => '#ffffff',
                        'bg'       => 'transparent',
                        'fontSize' => 28,
                        'radius'   => 0,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'text',
                        'content'  => 'Scale your conversions with modern trigger automations.',
                        'x'        => 50,
                        'y'        => 115,
                        'w'        => 460,
                        'h'        => 34,
                        'z'        => 3,
                        'color'    => '#d8b4fe',
                        'bg'       => 'transparent',
                        'fontSize' => 15,
                        'radius'   => 0,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'email',
                        'content'  => 'Enter your business email',
                        'x'        => 50,
                        'y'        => 180,
                        'w'        => 360,
                        'h'        => 44,
                        'z'        => 4,
                        'color'    => '#111827',
                        'bg'       => '#ffffff',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'submit',
                        'content'  => 'Start Free Trial',
                        'x'        => 50,
                        'y'        => 240,
                        'w'        => 200,
                        'h'        => 44,
                        'z'        => 5,
                        'color'    => '#ffffff',
                        'bg'       => '#9333ea',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ]
                ];
                break;

            case '27063': // Sign In Compact Vector
                $layers = [
                    [
                        'type'     => 'rectangle',
                        'content'  => '',
                        'x'        => 0,
                        'y'        => 0,
                        'w'        => 640,
                        'h'        => 400,
                        'z'        => 1,
                        'color'    => '#111827',
                        'bg'       => '#f8fafc',
                        'fontSize' => 14,
                        'radius'   => 8,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'text',
                        'content'  => 'Sign In to Your Account',
                        'x'        => 60,
                        'y'        => 50,
                        'w'        => 320,
                        'h'        => 36,
                        'z'        => 2,
                        'color'    => '#0f172a',
                        'bg'       => 'transparent',
                        'fontSize' => 22,
                        'radius'   => 0,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'email',
                        'content'  => 'Email address',
                        'x'        => 60,
                        'y'        => 110,
                        'w'        => 320,
                        'h'        => 42,
                        'z'        => 3,
                        'color'    => '#111827',
                        'bg'       => '#ffffff',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'input',
                        'content'  => 'Password',
                        'x'        => 60,
                        'y'        => 168,
                        'w'        => 320,
                        'h'        => 42,
                        'z'        => 4,
                        'color'    => '#111827',
                        'bg'       => '#ffffff',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'submit',
                        'content'  => 'Sign In',
                        'x'        => 60,
                        'y'        => 230,
                        'w'        => 140,
                        'h'        => 42,
                        'z'        => 5,
                        'color'    => '#ffffff',
                        'bg'       => '#0284c7',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ]
                ];
                break;

            default:
                $layers = [
                    [
                        'type'     => 'rectangle',
                        'content'  => '',
                        'x'        => 0,
                        'y'        => 0,
                        'w'        => 640,
                        'h'        => 400,
                        'z'        => 1,
                        'color'    => '#111827',
                        'bg'       => '#ffffff',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'text',
                        'content'  => 'Special Announcement',
                        'x'        => 40,
                        'y'        => 50,
                        'w'        => 380,
                        'h'        => 36,
                        'z'        => 2,
                        'color'    => '#111827',
                        'bg'       => 'transparent',
                        'fontSize' => 24,
                        'radius'   => 0,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'email',
                        'content'  => 'Your Email Address',
                        'x'        => 40,
                        'y'        => 120,
                        'w'        => 320,
                        'h'        => 42,
                        'z'        => 3,
                        'color'    => '#111827',
                        'bg'       => '#ffffff',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ],
                    [
                        'type'     => 'submit',
                        'content'  => 'Subscribe',
                        'x'        => 40,
                        'y'        => 180,
                        'w'        => 180,
                        'h'        => 42,
                        'z'        => 4,
                        'color'    => '#ffffff',
                        'bg'       => '#b5295c',
                        'fontSize' => 14,
                        'radius'   => 4,
                        'visible'  => true
                    ]
                ];
                break;
        }

        return wp_json_encode($layers);
    }
}
