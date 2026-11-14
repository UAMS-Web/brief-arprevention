<?php

/**
 * What loading the theme registers and calls at file scope.
 *
 * The comparison itself lives in `brief_assert_theme_load()` (tests/Pest.php),
 * which runs after the first test of every process so the mutation runner
 * meets it. This test runs it explicitly, so the check is visible in the suite
 * and still runs when another test is not first.
 *
 * @see tests/load-manifest.php
 */

declare(strict_types=1);

it('registers and calls exactly what the manifest lists', function (): void {
    expect(brief_registered_hooks('actions', 'genesis_header', 'genesis_do_nav'))->toBe(12);

    brief_assert_theme_load();
});

it('removes the Genesis defaults it moves', function (): void {
    expect(brief_registered_hooks('actions', 'genesis_after_header', 'genesis_do_nav'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_after_header', 'genesis_do_subnav'))->toBeNull()
        ->and(brief_registered_hooks('actions', 'genesis_entry_header', 'genesis_post_info'))->toBe(9)
        ->and(brief_registered_hooks('actions', 'genesis_entry_footer', 'genesis_post_meta'))->toBeNull();
});
