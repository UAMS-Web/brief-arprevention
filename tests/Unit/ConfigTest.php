<?php

/**
 * The configuration files under config/, each pinned to its exact contents.
 *
 * Genesis reads some of them by name (`child-theme-settings`, `menus`, `html5` and
 * the others without a `genesis_get_config()` caller in the theme); every value
 * here is what Genesis or the theme receives.
 *
 * `config/block-editor-settings.php` is gone: it called two Customizer helpers the
 * theme never defined, so it could not load, and nothing referenced it. It was
 * pinned as failing first, in the commit before the one that deleted it.
 *
 * @see config/
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

/**
 * Loads a config file with the given theme mods.
 *
 * @param  array<string, mixed>  $mods
 */
function brief_config(string $name, array $mods = []): mixed
{
    brief_stub_genesis($mods);

    return require dirname(__DIR__, 2).'/config/'.$name.'.php';
}

it('lists the accessibility features', function (): void {
    expect(brief_config('accessibility'))->toBe(['404-page', 'drop-down-menu', 'headings', 'search-form', 'skip-links']);
});

it('lists the HTML5 markup features', function (): void {
    expect(brief_config('html5'))->toBe(['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);
});

it('lets the custom logo be any height and width', function (): void {
    expect(brief_config('custom-logo'))->toBe(['flex-height' => true, 'flex-width' => true]);
});

it('names the two menus', function (): void {
    expect(brief_config('menus'))->toBe(['primary' => 'Header Menu', 'secondary' => 'Footer Menu']);
});

it('gives posts and pages the singular image size', function (): void {
    expect(brief_config('post-type-supports'))->toBe([
        'post' => ['genesis-singular-images'],
        'page' => ['genesis-singular-images'],
    ]);
});

it('configures the responsive menus', function (): void {
    expect(brief_config('responsive-menus'))->toBe([
        'script' => ['mainMenu' => '', 'menuClasses' => ['others' => ['.nav-primary']]],
        'extras' => ['media_query_width' => '960px'],
    ]);
});

it('sets the Simple Social Icons defaults', function (): void {
    expect(brief_config('simple-social-icons-settings'))->toBe([
        'alignment' => 'aligncenter',
        'background_color' => '#ffffff',
        'background_color_hover' => '#ffffff',
        'border_radius' => 0,
        'border_width' => 0,
        'icon_color' => '#333333',
        'icon_color_hover' => '#666666',
        'size' => 40,
    ]);
});

it('declares the theme supports', function (): void {
    expect(brief_config('theme-supports'))->toBe([
        'genesis-custom-logo' => ['height' => 120, 'width' => 700, 'flex-height' => true, 'flex-width' => true],
        'html5' => ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form'],
        'genesis-accessibility' => ['drop-down-menu', 'headings', 'search-form', 'skip-links'],
        'genesis-after-entry-widget-area' => '',
        'genesis-footer-widgets' => 1,
        'genesis-menus' => ['primary' => 'Header Menu', 'secondary' => 'Footer Menu'],
    ]);
});

it('sets the default Genesis settings', function (): void {
    if (! defined('GENESIS_SETTINGS_FIELD')) {
        define('GENESIS_SETTINGS_FIELD', 'genesis-settings');
    }

    expect(brief_config('child-theme-settings'))->toBe([
        'genesis-settings' => [
            'blog_cat_num' => 4,
            'breadcrumb_home' => 0,
            'breadcrumb_front_page' => 0,
            'breadcrumb_posts_page' => 0,
            'breadcrumb_single' => 0,
            'breadcrumb_page' => 0,
            'breadcrumb_archive' => 0,
            'breadcrumb_404' => 0,
            'breadcrumb_attachment' => 0,
            'content_archive' => 'full',
            'content_archive_limit' => 220,
            'content_archive_thumbnail' => 0,
            'entry_meta_after_content' => '[post_categories before=""] [post_tags before=""]',
            'entry_meta_before_content' => '[post_date]',
            'image_size' => 'medium',
            'image_alignment' => 'alignleft',
            'posts_nav' => 'numeric',
            'site_layout' => 'full-width-content',
        ],
        'posts_per_page' => 4,
    ]);
});

it('sets the Genesis settings for the starter content', function (): void {
    expect(brief_config('child-theme-settings-genesis'))->toBe([
        'blog_cat_num' => 6,
        'breadcrumb_home' => 0,
        'breadcrumb_front_page' => 0,
        'breadcrumb_posts_page' => 0,
        'breadcrumb_single' => 0,
        'breadcrumb_page' => 0,
        'breadcrumb_archive' => 0,
        'breadcrumb_404' => 0,
        'breadcrumb_attachment' => 0,
        'content_archive' => 'excerpts',
        'content_archive_limit' => 0,
        'content_archive_thumbnail' => 0,
        'image_size' => 'large',
        'image_alignment' => 'none',
        'posts_nav' => 'numeric',
        'site_layout' => 'full-width-content',
    ]);
});

describe('appearance', function (): void {
    it('uses the default colors when no theme mod is set', function (): void {
        $appearance = brief_config('appearance');

        expect($appearance)->toBe([
            'fonts-url' => 'https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i|Open+Sans:700',
            'content-width' => 1062,
            'text-color' => '#333',
            'link-color' => '#fff385',
            'button-color' => '#333',
            'default-colors' => ['text' => '#333', 'link' => '#fff385'],
            'editor-color-palette' => [
                ['name' => 'Text color', 'slug' => 'theme-primary', 'color' => '#333'],
                ['name' => 'Link color', 'slug' => 'theme-secondary', 'color' => '#fff385'],
            ],
            'editor-font-sizes' => [
                ['name' => 'Small', 'size' => 14.4, 'slug' => 'small'],
                ['name' => 'Normal', 'size' => 18, 'slug' => 'normal'],
                ['name' => 'Large', 'size' => 20, 'slug' => 'large'],
                ['name' => 'Larger', 'size' => 24, 'slug' => 'larger'],
            ],
        ]);
    });

    it('carries the customized colors into the palette and the buttons, and keeps the defaults', function (): void {
        $appearance = brief_config('appearance', ['brief_text_color' => '#111111', 'brief_link_color' => '#222222']);

        expect($appearance['text-color'])->toBe('#111111')
            ->and($appearance['button-color'])->toBe('#111111')
            ->and($appearance['link-color'])->toBe('#222222')
            ->and($appearance['default-colors'])->toBe(['text' => '#333', 'link' => '#fff385'])
            ->and(array_column($appearance['editor-color-palette'], 'color'))->toBe(['#111111', '#222222']);
    });

    it('asks for each theme mod by name with its default', function (): void {
        Functions\expect('get_theme_mod')->once()->with('brief_text_color', '#333')->andReturn('#333');
        Functions\expect('get_theme_mod')->once()->with('brief_link_color', '#fff385')->andReturn('#fff385');

        require dirname(__DIR__, 2).'/config/appearance.php';
    });
});
