<?php

declare(strict_types=1);
/**
 * Brief.
 *
 * This file adds the Customizer additions to the Brief Theme.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://minimalgenesis.com/
 */
add_action('customize_register', 'brief_customizer_register');
/**
 * Registers settings and controls with the Customizer.
 *
 * @since 2.2.3
 *
 * @param  WP_Customize_Manager  $wp_customize  Customizer object.
 */
function brief_customizer_register($wp_customize)
{

    $appearance = genesis_get_config('appearance');

    $wp_customize->add_setting(
        'brief_text_color',
        [
            'default' => $appearance['default-colors']['text'],
            'sanitize_callback' => 'sanitize_hex_color',
        ]
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'brief_text_color',
            [
                'description' => __('Change the default color for body, heading, button background, and other text.', 'brief'),
                'label' => __('Text Color', 'brief'),
                'section' => 'colors',
                'settings' => 'brief_text_color',
            ]
        )
    );

    $wp_customize->add_setting(
        'brief_link_color',
        [
            'default' => $appearance['default-colors']['link'],
            'sanitize_callback' => 'sanitize_hex_color',
        ]
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'brief_link_color',
            [
                'description' => __('Change the default link highlight color.', 'brief'),
                'label' => __('Link Color', 'brief'),
                'section' => 'colors',
                'settings' => 'brief_link_color',
            ]
        )
    );

    $wp_customize->add_setting(
        'brief_logo_width',
        [
            'default' => 350,
            'sanitize_callback' => 'absint',
            'validate_callback' => 'brief_validate_logo_width',
        ]
    );

    // Add a control for the logo size.
    $wp_customize->add_control(
        'brief_logo_width',
        [
            'label' => __('Logo Width', 'brief'),
            'description' => __('The maximum width of the logo in pixels.', 'brief'),
            'priority' => 9,
            'section' => 'title_tagline',
            'settings' => 'brief_logo_width',
            'type' => 'number',
            'input_attrs' => [
                'min' => 100,
            ],

        ]
    );

}

/**
 * Displays a message if the entered width is not numeric or greater than 100.
 *
 * @param  object  $validity  The validity status.
 * @param  int  $width  The width entered by the user.
 * @return int The new width.
 */
function brief_validate_logo_width($validity, $width)
{

    if (empty($width) || ! is_numeric($width)) {
        $validity->add('required', __('You must supply a valid number.', 'brief'));
    } elseif ($width < 100) {
        $validity->add('logo_too_small', __('The logo width cannot be less than 100.', 'brief'));
    }

    return $validity;

}
