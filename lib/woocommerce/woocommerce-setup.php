<?php

declare(strict_types=1);
/**
 * Brief.
 *
 * This file adds the required WooCommerce setup functions to the Brief Theme.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://minimalgenesis.com/
 */

// Adds product gallery support.
if (class_exists('WooCommerce')) {

    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('wc-product-gallery-zoom');

}

add_filter('woocommerce_style_smallscreen_breakpoint', 'brief_woocommerce_breakpoint');
/**
 * Modifies the WooCommerce breakpoints.
 *
 * @since 2.3.0
 *
 * @return string Pixel width of the theme's breakpoint.
 */
function brief_woocommerce_breakpoint(): string
{

    $current = genesis_site_layout(false);
    $layouts = [
        'one-sidebar' => [
            'content-sidebar',
            'sidebar-content',
        ],
    ];

    if (in_array($current, $layouts['one-sidebar'], true)) {
        return '1200px';
    }

    return '860px';

}

add_filter('genesiswooc_products_per_page', 'brief_default_products_per_page');
/**
 * Sets the default products per page.
 *
 * @since 2.3.0
 *
 * @return int Number of products to show per page.
 */
function brief_default_products_per_page(): int
{

    return 8;

}

add_filter('woocommerce_pagination_args', 'brief_woocommerce_pagination');
/**
 * Updates the next and previous arrows to the default Genesis style.
 *
 * @param  array  $args  The previous and next text arguments.
 *
 * @since 2.3.0
 *
 * @return array New next and previous text arguments.
 */
function brief_woocommerce_pagination($args)
{

    $args['prev_text'] = sprintf('&laquo; %s', __('Previous Page', 'brief'));
    $args['next_text'] = sprintf('%s &raquo;', __('Next Page', 'brief'));

    return $args;

}

add_action('after_switch_theme', 'brief_woocommerce_image_dimensions_after_theme_setup', 1);
/**
 * Defines WooCommerce image sizes on theme activation.
 *
 * @since 2.3.0
 */
function brief_woocommerce_image_dimensions_after_theme_setup(): void
{

    global $pagenow;

    // Checks conditionally to see if we're activating the current theme and that WooCommerce is installed.
    if (! isset($_GET['activated']) || $pagenow !== 'themes.php' || ! class_exists('WooCommerce')) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification -- low risk, follows official snippet at https://goo.gl/nnHHQa.
        return;
    }

    brief_update_woocommerce_image_dimensions();

}

add_action('activated_plugin', 'brief_woocommerce_image_dimensions_after_woo_activation', 10, 2);
/**
 * Defines the WooCommerce image sizes on WooCommerce activation.
 *
 * @since 2.3.0
 *
 * @param  string  $plugin  The path of the plugin being activated.
 */
function brief_woocommerce_image_dimensions_after_woo_activation($plugin): void
{

    // Checks to see if WooCommerce is being activated.
    if ($plugin !== 'woocommerce/woocommerce.php') {
        return;
    }

    brief_update_woocommerce_image_dimensions();

}

/**
 * Updates WooCommerce image dimensions.
 *
 * @since 2.3.0
 */
function brief_update_woocommerce_image_dimensions(): void
{

    // Updates image size options.
    update_option('woocommerce_single_image_width', 655);    // Single product image.
    update_option('woocommerce_thumbnail_image_width', 500); // Catalog image.

    // Updates image cropping option.
    update_option('woocommerce_thumbnail_cropping', '1:1');

}

add_filter('woocommerce_get_image_size_gallery_thumbnail', 'brief_gallery_image_thumbnail');
/**
 * Filters the WooCommerce gallery image dimensions.
 *
 * @since 2.6.0
 *
 * @param  array  $size  The gallery image size and crop arguments.
 * @return array The modified gallery image size and crop arguments.
 */
function brief_gallery_image_thumbnail($size): array
{

    return [
        'width' => 180,
        'height' => 180,
        'crop' => 1,
    ];

}

// Remove description tab heading
add_filter('woocommerce_product_description_heading', '__return_null');
