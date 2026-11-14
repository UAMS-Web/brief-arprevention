<?php

/**
 * The WooCommerce styles in lib/woocommerce/woocommerce-output.php.
 *
 * Pinned before any change: `brief_woocommerce_css()` reads
 * `$appearance['default-colors']['accent']` and a `$color_accent` that nothing
 * assigns, so with WooCommerce active it raises two warnings on every request and
 * its accent block can never run (`null !== null` is false).
 *
 * @see lib/woocommerce/woocommerce-output.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

/**
 * Runs brief_woocommerce_css() and returns the warnings it raised and the CSS it added.
 *
 * @param  array<string, mixed>  $mods
 * @return array{warnings: list<string>, css: ?string}
 */
function brief_run_woocommerce_css(array $mods = []): array
{
    brief_stub_genesis($mods);
    brief_woocommerce_active(true);

    $css = null;
    when('wp_add_inline_style')->alias(function (string $handle, string $added) use (&$css): void {
        expect($handle)->toBe('brief-woocommerce-styles');
        $css = $added;
    });

    $warnings = [];
    set_error_handler(function (int $level, string $message) use (&$warnings): bool {
        $warnings[] = $message;

        return true;
    }, E_WARNING);

    try {
        brief_woocommerce_css();
    } finally {
        restore_error_handler();
    }

    return ['warnings' => $warnings, 'css' => $css];
}

it('registers its hooks', function (string $type, string $hook, string $callback): void {
    expect(brief_registered_hooks($type, $hook, $callback))->toBe(10);
})->with([
    ['filters', 'woocommerce_enqueue_styles', 'brief_woocommerce_styles'],
    ['actions', 'wp_enqueue_scripts', 'brief_woocommerce_css'],
]);

it('adds the theme stylesheet to the WooCommerce styles', function (): void {
    brief_stub_genesis();
    when('get_stylesheet_directory_uri')->justReturn('https://example.test/theme');

    expect(brief_woocommerce_styles(['woocommerce-general' => ['src' => 'a']]))->toBe([
        'woocommerce-general' => ['src' => 'a'],
        'brief-woocommerce-styles' => [
            'src' => 'https://example.test/theme/lib/woocommerce/woocommerce-styles.css',
            'deps' => '',
            'version' => '9.9.9',
            'media' => 'screen',
        ],
    ]);
});

it('adds nothing and reads nothing when WooCommerce is not active', function (): void {
    brief_stub_genesis();
    brief_woocommerce_active(false);
    Functions\expect('wp_add_inline_style')->never();

    brief_woocommerce_css();
});

it('adds no CSS for the default text color', function (): void {
    $result = brief_run_woocommerce_css();

    expect($result['css'])->toBeNull();
});

it('recolors the WooCommerce text and buttons from a custom text color', function (): void {
    $result = brief_run_woocommerce_css(['brief_text_color' => '#112233']);

    expect($result['css'])->toContain('color: #112233;')
        ->toContain('background-color: #112233;')
        ->toContain('.woocommerce-message::before')
        ->not->toContain('border-top-color');
});

it('raises no warning', function (): void {
    expect(brief_run_woocommerce_css()['warnings'])->toBe([])
        ->and(brief_run_woocommerce_css(['brief_text_color' => '#112233'])['warnings'])->toBe([]);
});
