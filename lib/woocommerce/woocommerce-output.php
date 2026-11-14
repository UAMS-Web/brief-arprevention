<?php

declare(strict_types=1);
/**
 * Brief.
 *
 * This file adds the WooCommerce styles and the Customizer additions for WooCommerce to the Brief Theme.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://minimalgenesis.com/
 */
add_filter('woocommerce_enqueue_styles', 'brief_woocommerce_styles');
/**
 * Enqueues the theme's custom WooCommerce styles to the WooCommerce plugin.
 *
 * @param  array  $enqueue_styles  The WooCommerce styles to enqueue.
 *
 * @since 2.3.0
 *
 * @return array Modified WooCommerce styles to enqueue.
 */
function brief_woocommerce_styles($enqueue_styles)
{

    $enqueue_styles[genesis_get_theme_handle().'-woocommerce-styles'] = [
        'src' => get_stylesheet_directory_uri().'/lib/woocommerce/woocommerce-styles.css',
        'deps' => '',
        'version' => genesis_get_theme_version(),
        'media' => 'screen',
    ];

    return $enqueue_styles;

}

add_action('wp_enqueue_scripts', 'brief_woocommerce_css');
/**
 * Adds the themes's custom CSS to the WooCommerce stylesheet.
 *
 * @since 2.3.0
 *
 * @return string CSS to be outputted after the theme's custom WooCommerce stylesheet.
 */
function brief_woocommerce_css(): void
{

    // If WooCommerce isn't active, exit early.
    if (! class_exists('WooCommerce')) {
        return;
    }

    $appearance = genesis_get_config('appearance');

    $color_text = get_theme_mod('brief_text_color', $appearance['default-colors']['text']);

    $woo_css = '';

    // Equivalent: `$woo_css` is the empty string here, so `.=` and `=` give the same value.
    // @pest-mutate-ignore: ConcatEqualToEqual
    $woo_css .= ($appearance['default-colors']['text'] !== $color_text) ? sprintf(
        '

		.woocommerce-error::before,
		.woocommerce-info::before,
		.woocommerce-message::before,
		.woocommerce div.product p.price,
		.woocommerce div.product span.price,
		.woocommerce ul.products li.product .price,
		.woocommerce .widget_layered_nav ul li.chosen a::before,
		.woocommerce .widget_layered_nav_filters ul li a::before,
		.woocommerce .widget_rating_filter ul li.chosen a::before {
			color: %1$s;
		}
		
		.woocommerce a.button,
		.woocommerce a.button.alt,
		.woocommerce button.button,
		.woocommerce button.button.alt,
		.woocommerce input.button,
		.woocommerce input.button.alt,
		.woocommerce input.button[type="submit"],
		.woocommerce #respond input#submit,
		.woocommerce #respond input#submit.alt,
		.woocommerce a.button:hover,
		.woocommerce a.button:focus,
		.woocommerce a.button.alt:hover,
		.woocommerce a.button.alt:focus,
		.woocommerce button.button:hover,
		.woocommerce button.button:focus,
		.woocommerce button.button.alt:hover,
		.woocommerce button.button.alt:focus,
		.woocommerce input.button:hover,
		.woocommerce input.button:focus,
		.woocommerce input.button.alt:hover,
		.woocommerce input.button.alt:focus,
		.woocommerce input[type="submit"]:hover,
		.woocommerce input[type="submit"]:focus,
		.woocommerce span.onsale,
		.woocommerce #respond input#submit:hover,
		.woocommerce #respond input#submit:focus,
		.woocommerce #respond input#submit.alt:hover,
		.woocommerce #respond input#submit.alt:focus,
		.woocommerce.widget_price_filter .ui-slider .ui-slider-handle,
		.woocommerce.widget_price_filter .ui-slider .ui-slider-range {
			background-color: %1$s;
		}

	',
        $color_text
    ) : '';

    if ($woo_css) {
        wp_add_inline_style(genesis_get_theme_handle().'-woocommerce-styles', $woo_css);
    }

}
