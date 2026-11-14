<?php

/**
 * The two page templates in page-templates/.
 *
 * Each runs at file scope when WordPress renders a page that uses it; the loader
 * in tests/Pest.php runs both once, and tests/load-manifest.php pins what they
 * registered, removed and called (`genesis()` and `remove_theme_support()` are
 * recorded calls). The tests here cover the callbacks the Landing template defines.
 *
 * @see page-templates/blocks.php
 * @see page-templates/landing.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

it('removes the page chrome that the templates drop', function (): void {
    expect(brief_registered_hooks('actions', 'genesis_entry_header', 'genesis_entry_header_markup_open'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_entry_header', 'genesis_entry_header_markup_close'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_entry_header', 'genesis_do_post_title'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_before_loop', 'genesis_do_breadcrumbs'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_before_header', 'genesis_skip_links'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_header', 'genesis_header_markup_open'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_header', 'genesis_do_header'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_header', 'genesis_header_markup_close'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_footer', 'genesis_footer_markup_open'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_footer', 'genesis_do_footer'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_footer', 'genesis_footer_markup_close'))->toBeNull();
});

it('forces the full-width layout, runs the Genesis loop and drops the menus', function (): void {
    expect(brief_registered_hooks('filters', 'genesis_site_layout', '__genesis_return_full_width_content'))->toBe(10)
        ->and(brief_recorded_calls('genesis'))->toBe([[], []])
        ->and(brief_recorded_calls('remove_theme_support'))->toBe([['genesis-menus']]);
});

it('adds the landing page body class and keeps the others', function (): void {
    expect(brief_landing_body_class(['page', 'logged-in']))->toBe(['page', 'logged-in', 'landing-page'])
        ->and(brief_registered_hooks('filters', 'body_class', 'brief_landing_body_class'))->toBe(10);
});

it('dequeues the skip links script', function (): void {
    Functions\expect('wp_dequeue_script')->once()->with('skip-links');

    brief_dequeue_skip_links();

    expect(brief_registered_hooks('actions', 'wp_enqueue_scripts', 'brief_dequeue_skip_links'))->toBe(10);
});
