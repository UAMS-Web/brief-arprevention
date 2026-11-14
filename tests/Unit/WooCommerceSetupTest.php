<?php

/**
 * The WooCommerce setup in lib/woocommerce/woocommerce-setup.php.
 *
 * `class_exists()` is redefinable (patchwork.json), so each test says whether
 * WooCommerce is active instead of depending on which test ran first.
 *
 * @see lib/woocommerce/woocommerce-setup.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

/**
 * Captures update_option() calls.
 *
 * @return array<int, array{0: string, 1: mixed}>
 */
function brief_capture_options(): array
{
    $options = [];
    when('update_option')->alias(function (string $name, $value) use (&$options): void {
        $options[] = [$name, $value];
    });

    return $options;
}

it('turns on the product gallery features at load when WooCommerce is active', function (): void {
    $features = array_column(array_filter(
        brief_recorded_calls('add_theme_support'),
        fn (array $call): bool => str_starts_with($call[0], 'wc-')
    ), 0);

    expect($features)->toBe(['wc-product-gallery-lightbox', 'wc-product-gallery-slider', 'wc-product-gallery-zoom']);
});

it('uses the one-sidebar breakpoint only for a layout with one sidebar', function (string $layout, string $expected): void {
    Functions\expect('genesis_site_layout')->once()->with(false)->andReturn($layout);

    expect(brief_woocommerce_breakpoint())->toBe($expected);
})->with([
    'content then sidebar' => ['content-sidebar', '1200px'],
    'sidebar then content' => ['sidebar-content', '1200px'],
    'full width' => ['full-width-content', '860px'],
    'two sidebars' => ['content-sidebar-sidebar', '860px'],
    'not a string' => ['', '860px'],
]);

it('shows eight products per page', function (): void {
    expect(brief_default_products_per_page())->toBe(8);
});

it('uses the Genesis pagination arrows and keeps the other arguments', function (): void {
    expect(brief_woocommerce_pagination(['end_size' => 3, 'prev_text' => 'x']))->toBe([
        'end_size' => 3,
        'prev_text' => '&laquo; Previous Page',
        'next_text' => 'Next Page &raquo;',
    ]);
});

it('sizes the gallery thumbnail 180 by 180 and cropped', function (): void {
    expect(brief_gallery_image_thumbnail())->toBe(['width' => 180, 'height' => 180, 'crop' => 1]);
});

it('sets the WooCommerce image sizes and cropping', function (): void {
    $options = [];
    when('update_option')->alias(function (string $name, $value) use (&$options): void {
        $options[] = [$name, $value];
    });

    brief_update_woocommerce_image_dimensions();

    expect($options)->toBe([
        ['woocommerce_single_image_width', 655],
        ['woocommerce_thumbnail_image_width', 500],
        ['woocommerce_thumbnail_cropping', '1:1'],
    ]);
});

describe('image sizes on theme activation', function (): void {
    afterEach(function (): void {
        unset($_GET['activated']);
        unset($GLOBALS['pagenow']);
    });

    it('updates them when this theme was just activated on the themes screen with WooCommerce active', function (): void {
        brief_woocommerce_active(true);
        $_GET['activated'] = 'true';
        $GLOBALS['pagenow'] = 'themes.php';
        Functions\expect('update_option')->times(3);

        brief_woocommerce_image_dimensions_after_theme_setup();
    });

    it('does nothing unless every condition holds', function (bool $active, ?string $activated, string $page): void {
        brief_woocommerce_active($active);
        if ($activated !== null) {
            $_GET['activated'] = $activated;
        }

        $GLOBALS['pagenow'] = $page;
        Functions\expect('update_option')->never();

        brief_woocommerce_image_dimensions_after_theme_setup();
    })->with([
        'WooCommerce inactive' => [false, 'true', 'themes.php'],
        'not just activated' => [true, null, 'themes.php'],
        'another screen' => [true, 'true', 'plugins.php'],
    ]);
});

it('updates the image sizes when WooCommerce itself is activated', function (): void {
    Functions\expect('update_option')->times(3);

    brief_woocommerce_image_dimensions_after_woo_activation('woocommerce/woocommerce.php');
});

it('leaves the image sizes alone when another plugin is activated', function (): void {
    Functions\expect('update_option')->never();

    brief_woocommerce_image_dimensions_after_woo_activation('akismet/akismet.php');
});

it('registers its hooks', function (string $type, string $hook, string $callback, int $priority): void {
    expect(brief_registered_hooks($type, $hook, $callback))->toBe($priority);
})->with([
    ['filters', 'woocommerce_style_smallscreen_breakpoint', 'brief_woocommerce_breakpoint', 10],
    ['filters', 'genesiswooc_products_per_page', 'brief_default_products_per_page', 10],
    ['filters', 'woocommerce_pagination_args', 'brief_woocommerce_pagination', 10],
    ['actions', 'after_switch_theme', 'brief_woocommerce_image_dimensions_after_theme_setup', 1],
    ['actions', 'activated_plugin', 'brief_woocommerce_image_dimensions_after_woo_activation', 10],
    ['filters', 'woocommerce_get_image_size_gallery_thumbnail', 'brief_gallery_image_thumbnail', 10],
    ['filters', 'woocommerce_product_description_heading', '__return_null', 10],
]);
