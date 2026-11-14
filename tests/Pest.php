<?php

/**
 * Pest configuration, for the Unit suite.
 */

declare(strict_types=1);
use Brain\Monkey\Container;
use Brain\Monkey\Hook\HookStorage;

use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

/*
 * Brain Monkey around every test.
 *
 * setUp() defines WordPress' hook functions and arms Patchwork; tearDown()
 * verifies every expectation the test set (a `->once()` that never happened
 * fails here) and removes every mock, so nothing leaks into the next test.
 *
 * The assertion count: Brain Monkey's expectations are Mockery expectations,
 * which PHPUnit does not count. Without adding them, a test whose only check is
 * `Functions\expect(...)->once()` reports as risky, and failOnRisky fails it.
 */
uses()
    ->beforeEach(function (): void {
        setUp();

        stubTranslationFunctions();

        brief_load_theme();
    })
    ->afterEach(function (): void {
        brief_assert_theme_load();

        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());

        tearDown();
    })
    ->in('Unit');

/**
 * The priority a callback was registered at while functions.php loaded, or null
 * when it was not registered, or registered more than once.
 *
 * @param  'actions'|'filters'  $type
 */
function brief_registered_hooks(string $type, string $hook, string $callback): ?int
{
    $priorities = $GLOBALS['brief_registered_hooks'][$type][$hook][$callback] ?? [];

    return count($priorities) === 1 ? $priorities[0] : null;
}

/**
 * The argument lists a file-scope call received while functions.php loaded.
 *
 * @return list<list<mixed>>
 */
function brief_recorded_calls(string $function): array
{
    return $GLOBALS['brief_recorded_calls'][$function] ?? [];
}

/**
 * Stand-ins for the Genesis helpers the theme calls, so a callback can run.
 *
 * `genesis_get_config()` reads the theme's real file under `config/`; the theme
 * handle and version are fixed values the tests assert against.
 *
 * @param  array<string, mixed>  $theme_mods  Values `get_theme_mod()` returns by name, else its default.
 */
function brief_stub_genesis(array $theme_mods = []): void
{
    when('get_theme_mod')->alias(
        fn (string $name, $default = false) => $theme_mods[$name] ?? $default
    );
    when('genesis_get_config')->alias(
        fn (string $name) => require __DIR__.'/../config/'.$name.'.php'
    );
    when('genesis_get_theme_handle')->justReturn('brief');
    when('genesis_get_theme_version')->justReturn('9.9.9');
}

/**
 * Loads functions.php once per process, inside the first test that runs.
 *
 * It runs from a test's `beforeEach`, so the coverage driver is recording while
 * file scope executes and a mutation of a file-scope statement is attributed to
 * the tests. What the load registered is snapshotted here, because tearDown()
 * discards it: `brief_registered_hooks()` and `brief_recorded_calls()` read the
 * snapshot back. `lib/gutenberg/init.php` loads the way WordPress loads it, by
 * running the `after_setup_theme` callback that requires it.
 */
function brief_load_theme(): void
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;

    $root = dirname(__DIR__);

    $GLOBALS['brief_recorded_calls'] = [];

    when('get_template_directory')->justReturn($GLOBALS['brief_genesis_stand_in']);
    when('get_stylesheet_directory')->justReturn($root);

    // Genesis registers these before the child theme loads; the theme removes them, so
    // a removal that does not happen (or does not match) leaves one in the snapshot.
    add_action('genesis_after_header', 'genesis_do_nav');
    add_action('genesis_after_header', 'genesis_do_subnav');
    add_action('genesis_entry_header', 'genesis_post_info', 12);
    add_action('genesis_entry_footer', 'genesis_post_meta');

    foreach (['add_theme_support', 'add_editor_style', 'add_image_size', 'unregister_sidebar', 'genesis_unregister_layout', 'genesis_register_responsive_menus'] as $recorded) {
        when($recorded)->alias(function (...$arguments) use ($recorded): void {
            $GLOBALS['brief_recorded_calls'][$recorded][] = $arguments;
        });
    }

    // What file scope reads: the real config files, with every theme mod at its default.
    when('get_theme_mod')->alias(fn (string $name, $default = false) => $default);
    when('genesis_get_config')->alias(fn (string $name) => require $root.'/config/'.$name.'.php');

    require_once $root.'/functions.php';

    genesis_child_gutenberg_support();

    $GLOBALS['brief_registered_hooks'] = [];

    $storage = new ReflectionProperty(HookStorage::class, 'storage');
    $added = $storage->getValue(Container::instance()->hookStorage())['added'];

    foreach ($added as $type => $hooks) {
        foreach ($hooks as $hook => $entries) {
            foreach ($entries as $entry) {
                $GLOBALS['brief_registered_hooks'][$type][$hook][(string) $entry[0]][] = $entry[1];
            }
        }
    }
}

/**
 * Asserts, once per process, that loading the theme registered and called what
 * tests/load-manifest.php lists.
 *
 * File scope runs once, inside whichever test runs first, and a mutation of a
 * file-scope statement is run against only the tests that executed it: that one
 * test. Checking the whole load from `afterEach` puts the assertion in whichever
 * test that is, so every file-scope mutant meets it.
 */
function brief_assert_theme_load(): void
{
    static $checked = false;

    if ($checked) {
        return;
    }

    $checked = true;

    $hooks = [];

    foreach ($GLOBALS['brief_registered_hooks'] as $type => $by_hook) {
        foreach ($by_hook as $hook => $callbacks) {
            foreach ($callbacks as $callback => $priorities) {
                foreach ($priorities as $priority) {
                    $hooks[] = "{$type} {$hook} {$callback} {$priority}";
                }
            }
        }
    }

    $expected = require __DIR__.'/load-manifest.php';

    expect($hooks)->toEqualCanonicalizing($expected['hooks'])
        ->and($GLOBALS['brief_recorded_calls'])->toBe($expected['calls']);
}
