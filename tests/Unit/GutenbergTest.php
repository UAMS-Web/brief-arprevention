<?php

/**
 * The block editor support in lib/gutenberg/init.php and lib/gutenberg/inline-styles.php.
 *
 * @see lib/gutenberg/init.php
 * @see lib/gutenberg/inline-styles.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

it('registers its callbacks on their hooks', function (string $type, string $hook, string $callback, int $priority): void {
    expect(brief_registered_hooks($type, $hook, $callback))->toBe($priority);
})->with([
    'front-end styles' => ['actions', 'wp_enqueue_scripts', 'brief_enqueue_gutenberg_frontend_styles', 10],
    'editor fonts' => ['actions', 'enqueue_block_editor_assets', 'brief_block_editor_styles', 10],
    'body classes' => ['filters', 'body_class', 'brief_blocks_body_classes', 10],
    'content width' => ['actions', 'after_setup_theme', 'brief_content_width', 0],
    'front-end inline styles' => ['actions', 'wp_enqueue_scripts', 'brief_custom_gutenberg_css', 10],
    'editor inline styles' => ['actions', 'enqueue_block_editor_assets', 'brief_custom_gutenberg_admin_css', 10],
]);

it('declares editor support at file scope', function (): void {
    brief_stub_genesis();
    $appearance = brief_require_array(dirname(__DIR__, 2).'/config/appearance.php');
    $features = collect_features();

    expect($features['editor-styles'])->toBe([])
        ->and($features['align-wide'])->toBe([])
        ->and($features['responsive-embeds'])->toBe([])
        ->and($features['editor-font-sizes'])->toBe($appearance['editor-font-sizes'])
        ->and($features['editor-color-palette'])->toBe($appearance['editor-color-palette'])
        ->and(brief_recorded_calls('add_editor_style'))->toBe([['/lib/gutenberg/style-editor.css']]);
});

/**
 * The file-scope add_theme_support() calls, keyed by feature.
 *
 * @return array<string, mixed>
 */
function collect_features(): array
{
    $features = [];

    foreach (brief_recorded_calls('add_theme_support') as $call) {
        if (is_string($call[0])) {
            $features[$call[0]] = array_key_exists(1, $call) ? $call[1] : [];
        }
    }

    return $features;
}

it('enqueues the front-end block stylesheet after the theme stylesheet', function (): void {
    brief_stub_genesis();
    when('get_stylesheet_directory_uri')->justReturn('https://example.test/theme');
    Functions\expect('wp_enqueue_style')->once()->with(
        'brief-gutenberg',
        'https://example.test/theme/lib/gutenberg/front-end.css',
        ['brief'],
        '9.9.9'
    );

    brief_enqueue_gutenberg_frontend_styles();
});

it('enqueues the editor fonts', function (): void {
    brief_stub_genesis();
    Functions\expect('wp_enqueue_style')->once()->with(
        'brief-gutenberg-fonts',
        'https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i|Open+Sans:700',
        [],
        '9.9.9'
    );

    brief_block_editor_styles();
});

describe('brief_blocks_body_classes', function (): void {
    it('leaves the classes alone off a singular view', function (): void {
        when('is_singular')->justReturn(false);

        expect(brief_blocks_body_classes(['a']))->toBe(['a']);
    });

    it('adds has-no-blocks to a post without blocks', function (): void {
        when('is_singular')->justReturn(true);
        when('has_blocks')->justReturn(false);
        when('parse_blocks')->justReturn([]);

        expect(brief_blocks_body_classes(['a']))->toBe(['a', 'has-no-blocks']);
    });

    it('names the first block and its alignment', function (array $block, array $expected): void {
        when('is_singular')->justReturn(true);
        when('has_blocks')->justReturn(true);
        Functions\expect('get_post')->once()->withNoArgs()->andReturn((object) ['post_content' => '<!-- wp:x -->']);
        Functions\expect('parse_blocks')->once()->with('<!-- wp:x -->')->andReturn([$block]);

        expect(brief_blocks_body_classes(['a']))->toBe(['a', ...$expected]);
    })->with([
        'a named block' => [['blockName' => 'core/cover', 'attrs' => []], ['first-block-core-cover']],
        'an aligned block' => [['blockName' => 'core/group', 'attrs' => ['align' => 'full']], ['first-block-core-group', 'first-block-align-full']],
        'freeform content' => [['blockName' => null, 'attrs' => []], []],
        'no blocks parsed' => [[], []],
    ]);

    it('adds nothing when there is no current post', function (): void {
        when('is_singular')->justReturn(true);
        when('has_blocks')->justReturn(true);
        Functions\expect('get_post')->once()->andReturn(null);
        Functions\expect('parse_blocks')->never();

        expect(brief_blocks_body_classes(['a']))->toBe(['a']);
    });

    it('adds nothing when the post parses to no blocks at all', function (): void {
        when('is_singular')->justReturn(true);
        when('has_blocks')->justReturn(true);
        when('get_post')->justReturn((object) ['post_content' => '']);
        when('parse_blocks')->justReturn([]);

        expect(brief_blocks_body_classes(['a']))->toBe(['a']);
    });
});

it('sets the content width through a filter', function (): void {
    brief_stub_genesis();
    Functions\expect('apply_filters')->once()->with('brief_content_width', 1062)->andReturn(900);

    brief_content_width();

    expect($GLOBALS['content_width'])->toBe(900);
});

describe('inline styles', function (): void {
    it('builds one rule per editor font size', function (): void {
        when('get_theme_support')->justReturn([[
            ['slug' => 'small', 'size' => 14.4],
            ['slug' => 'big', 'size' => 30],
        ]]);

        expect(brief_inline_font_sizes())
            ->toStartWith("\t\t.site-container .has-small-font-size {")
            ->toContain('.site-container .has-small-font-size {')
            ->toContain('font-size: 14.4px;')
            ->toContain('.site-container .has-big-font-size {')
            ->toContain('font-size: 30px;');
    });

    it('builds no font size CSS without editor font size support', function (): void {
        when('get_theme_support')->justReturn(false);

        expect(brief_inline_font_sizes())->toBe('');
    });

    it('builds no font size CSS when the support holds no list of sizes', function (mixed $support): void {
        when('get_theme_support')->justReturn($support);

        expect(brief_inline_font_sizes())->toBe('');
    })->with([
        'an empty list' => [[]],
        'a string where the list belongs' => [['small']],
        'no first entry' => [['a' => []]],
    ]);

    it('skips a font size that has no text slug or no plain size', function (mixed $size): void {
        when('get_theme_support')->justReturn([[$size, ['slug' => 'kept', 'size' => 20]]]);

        expect(brief_inline_font_sizes())->toContain('.has-kept-font-size')
            ->and(substr_count(brief_inline_font_sizes(), 'font-size {'))->toBe(1);
    })->with([
        'not an array' => ['small'],
        'no slug' => [['size' => 10]],
        'a slug that is not text' => [['slug' => 7, 'size' => 10]],
        'no size' => [['slug' => 'small']],
        'a size that is not a plain value' => [['slug' => 'small', 'size' => [10]]],
    ]);

    it('builds text and background rules for each palette color', function (): void {
        brief_stub_genesis(['brief_text_color' => '#111111', 'brief_link_color' => '#222222']);

        $css = brief_inline_color_palette();

        expect($css)->toStartWith("\t\t.site-container .has-theme-primary-color,")
            ->toContain('.site-container .has-theme-primary-color,')
            ->toContain('.site-container .has-theme-primary-background-color,')
            ->toContain('.site-container .has-theme-secondary-color,')
            ->toContain('.site-container .has-theme-secondary-background-color,')
            ->and(substr_count($css, 'color: #111111;'))->toBe(2)
            ->and(substr_count($css, 'color: #222222;'))->toBe(2);
    });

    it('adds the front-end CSS after the block stylesheet', function (): void {
        brief_stub_genesis(['brief_text_color' => '#111111', 'brief_link_color' => '#222222']);
        when('get_theme_support')->justReturn(false);

        $added = [];
        when('wp_add_inline_style')->alias(function (string $handle, string $css) use (&$added): void {
            $added[] = [$handle, $css];
        });

        brief_custom_gutenberg_css();

        expect($added)->toHaveCount(1)
            ->and($added[0][0])->toBe('brief-gutenberg')
            ->and($added[0][1])->toContain('background-color: #111111;')
            ->toContain('box-shadow: inset 0 -6px 0 0 #222222;')
            ->toContain('.has-theme-primary-color,');
    });

    it('appends the font size CSS between the base rules and the palette', function (): void {
        brief_stub_genesis();
        when('get_theme_support')->justReturn([[['slug' => 'tiny', 'size' => 9]]]);

        $css = '';
        when('wp_add_inline_style')->alias(function (string $handle, string $added) use (&$css): void {
            $css = $added;
        });

        brief_custom_gutenberg_css();

        expect(strstr($css, '.has-tiny-font-size', true))->toContain('.wp-block-image figcaption a')
            ->and(strstr($css, '.has-theme-primary-color', true))->toContain('.has-tiny-font-size');
    });

    it('adds the editor CSS after the editor fonts', function (): void {
        brief_stub_genesis(['brief_text_color' => '#111111', 'brief_link_color' => '#222222']);

        $added = [];
        when('wp_add_inline_style')->alias(function (string $handle, string $css) use (&$added): void {
            $added[] = [$handle, $css];
        });

        brief_custom_gutenberg_admin_css();

        expect($added)->toHaveCount(1)
            ->and($added[0][0])->toBe('brief-gutenberg-fonts')
            ->and($added[0][1])->toContain('color: #111111 !important;')
            ->toContain('box-shadow: inset 0 -7px 0 0 #222222;')
            ->toContain('border: 2px solid #111111;');
    });
});
