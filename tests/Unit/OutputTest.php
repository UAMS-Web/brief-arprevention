<?php

/**
 * The Customizer CSS in lib/output.php.
 *
 * The logo arithmetic: the logo is shrunk to the configured maximum width, so its
 * effective height is `min(width, max width) / max(width / height, 1)`, and the
 * title area is padded to center it in a 60px header.
 *
 * The logo rules are built only when the attachment lookup returns a logo, so no logo
 * value is ever read unset.
 *
 * @see lib/output.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

/**
 * Runs brief_css() and returns the inline CSS it added, or null when it added none.
 *
 * @param  array<string, mixed>  $mods  Theme mods.
 * @param  array{0: string, 1: int, 2: int}|false  $logo  `wp_get_attachment_image_src()` result.
 */
function brief_run_css(array $mods = [], array|false $logo = false): ?string
{
    brief_stub_genesis($mods);
    when('absint')->alias(fn ($value): int => abs((int) $value));
    when('wp_get_attachment_image_src')->justReturn($logo);

    $css = null;
    Functions\expect('wp_add_inline_style')->zeroOrMoreTimes()->andReturnUsing(function (string $handle, string $added) use (&$css): void {
        expect($handle)->toBe('brief');
        $css = $added;
    });

    brief_css();

    return $css;
}

it('is registered on wp_enqueue_scripts', function (): void {
    expect(brief_registered_hooks('actions', 'wp_enqueue_scripts', 'brief_css'))->toBe(10);
});

it('adds no CSS for the default colors and no logo', function (): void {
    expect(brief_run_css())->toBeNull();
});

it('recolors text, links and buttons from a custom text color only', function (): void {
    $css = brief_run_css(['brief_text_color' => '#112233']);

    expect($css)->toContain('color: #112233;')
        ->toContain('background-color: #112233;')
        ->toContain('color: #112233 !important;')
        ->toContain('.nav-primary .genesis-nav-menu > .menu-highlight > a');
    expect($css)->not->toContain('box-shadow');
});

it('recolors the link highlight from a custom link color only', function (): void {
    $css = brief_run_css(['brief_link_color' => '#445566']);

    expect($css)->toContain('box-shadow: inset 0 -7px 0 0 #445566;')
        ->toContain('box-shadow: inset 0 -6px 0 0 #445566;');
    expect($css)->not->toContain('background-color');
});

it('adds both color blocks, text first, when both colors are custom', function (): void {
    $css = brief_run_css(['brief_text_color' => '#112233', 'brief_link_color' => '#445566']);

    expect(strstr((string) $css, '#445566', true))->toContain('#112233');
});

describe('the logo', function (): void {
    it('adds the logo rules after the color rules instead of replacing them', function (): void {
        $css = (string) brief_run_css(['brief_text_color' => '#112233', 'brief_logo_width' => 400], ['u', 400, 200]);

        expect($css)->toContain('color: #112233;')
            ->toContain('position: static;');
    });

    it('pads the title area to center a logo shorter than the header', function (): void {
        // 700x100 shrunk to 350 is 50px tall: (60 - 50) / 2 = 5, plus the 5px offset.
        $css = brief_run_css(logo: ['u', 700, 100]);

        expect($css)->toContain('padding-top: 10px;');
        expect($css)->not->toContain('position: static;');
        expect($css)->not->toContain('max-width');
    });

    it('does not pad a logo that fills the header', function (): void {
        // 60px tall at the 350 maximum: padding is exactly 0.
        expect(brief_run_css(logo: ['u', 60, 60]))->toBeNull();
    });

    it('pads a logo only when it is taller than one pixel', function (int $size, bool $padded): void {
        expect(str_contains((string) brief_run_css(logo: ['u', $size, $size]), 'padding-top'))->toBe($padded);
    })->with([
        'one pixel' => [1, false],
        'two pixels' => [2, true],
    ]);

    it('does not enlarge a portrait logo: a ratio under 1 is read as 1', function (): void {
        // 100x200 is a 0.5 ratio. Read as 1 the effective height is 100, so the
        // title area is padded by (60 - 100) / 2 clamped to 0, which adds no rule.
        // A ratio read as 0.5 would make it 200 and add the static header rule.
        expect(brief_run_css(logo: ['u', 100, 200]))->toBeNull();
    });

    it('does not pad a logo taller than the header', function (): void {
        // 400x200 at a 400 maximum: padding clamps at 0 rather than going negative.
        expect(brief_run_css(['brief_logo_width' => 400], ['u', 400, 200]))->not->toContain('padding-top');
    });

    it('does not divide by zero for a logo with no height', function (): void {
        // Height 0 is read as 1: ratio 350, effective height 1, so no padding.
        expect(brief_run_css(logo: ['u', 350, 0]))->toBeNull();
    });

    it('limits the title area to a custom maximum width', function (): void {
        $css = brief_run_css(['brief_logo_width' => 400], ['u', 400, 400]);

        expect($css)->toContain('max-width: 400px;');
        expect($css)->not->toContain('float: none;');
    });

    it('lets the header scroll for a logo 200px tall or more', function (int $height, bool $static): void {
        $css = (string) brief_run_css(['brief_logo_width' => 400], ['u', 400, $height]);

        expect(str_contains($css, 'position: static;'))->toBe($static);
    })->with([
        'just under' => [199, false],
        'exactly' => [200, true],
    ]);

    it('centers the logo and menu from a maximum width of 600', function (int $width, bool $centered): void {
        $css = (string) brief_run_css(['brief_logo_width' => $width], ['u', 700, 100]);

        expect(str_contains($css, 'float: none;'))->toBe($centered)
            ->and(str_contains($css, 'text-align: center;'))->toBe($centered);
    })->with([
        'just under' => [599, false],
        'exactly' => [600, true],
    ]);
});
