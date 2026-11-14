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
use function Patchwork\relay;

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

        // Pest binds `$this` to the running test inside this closure, which PHPStan cannot see.
        // @phpstan-ignore method.nonObject
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());

        tearDown();
    })
    ->in('Unit');

/**
 * What loading the theme registered and called, snapshotted because tearDown() discards it.
 *
 * `hooks` is keyed by type, hook and callback; each registration is `[priority, accepted args]`.
 * `calls` holds the argument lists of the file-scope calls that `brief_load_theme()` records.
 *
 * @return array{hooks: array<string, array<string, array<string, list<array{0: int, 1: int}>>>>, calls: array<string, list<list<mixed>>>}
 */
function &brief_theme_load_state(): array
{
    static $state = ['hooks' => [], 'calls' => []];

    return $state;
}

/**
 * The hooks Brain Monkey has recorded as added, keyed by type and hook.
 *
 * Brain Monkey keeps them in a private property of no declared type; each entry is
 * `[callback, priority, accepted args]`, as its `HookStorage::pushToAdded()` writes it, with the
 * callback as a `Brain\Monkey\Name\CallbackStringForm`.
 *
 * @return array<string, array<string, list<array{0: Stringable, 1: int, 2: int}>>>
 */
function brief_added_hooks(): array
{
    $storage = new ReflectionProperty(HookStorage::class, 'storage');

    // @phpstan-ignore offsetAccess.nonOffsetAccessible, return.type (a private property of unknown type; see the shape above)
    return $storage->getValue(Container::instance()->hookStorage())['added'];
}

/**
 * The priority a callback was registered at while functions.php loaded, or null
 * when it was not registered, or registered more than once.
 *
 * @param  string  $type  `actions` or `filters`.
 */
function brief_registered_hooks(string $type, string $hook, string $callback): ?int
{
    $registrations = brief_theme_load_state()['hooks'][$type][$hook][$callback] ?? [];

    return count($registrations) === 1 ? $registrations[0][0] : null;
}

/**
 * The argument lists a file-scope call received while functions.php loaded.
 *
 * @return list<list<mixed>>
 */
function brief_recorded_calls(string $function): array
{
    return brief_theme_load_state()['calls'][$function] ?? [];
}

/**
 * Loads a PHP file that returns an array, such as a file under `config/` or the load manifest.
 *
 * @return array<mixed>
 */
function brief_require_array(string $path): array
{
    $value = require $path;

    if (! is_array($value)) {
        throw new UnexpectedValueException("{$path} did not return an array.");
    }

    return $value;
}

/**
 * WordPress's `absint()`: the absolute value of a whole number, 0 for anything that is not numeric.
 */
function brief_absint(mixed $value): int
{
    return is_numeric($value) ? abs((int) $value) : 0;
}

/**
 * Stand-ins for the Genesis helpers the theme calls, so a callback can run.
 *
 * `genesis_get_config()` reads the theme's real file under `config/`; the theme
 * handle and version are fixed values the tests assert against.
 *
 * @param  array<mixed>  $theme_mods  Values `get_theme_mod()` returns by name, else its default.
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
 * running the `after_setup_theme` callback that requires it, and the two page templates load after it.
 */
function brief_load_theme(): void
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;

    $root = dirname(__DIR__);

    $state = &brief_theme_load_state();

    when('get_template_directory')->justReturn($GLOBALS['brief_genesis_stand_in']);
    when('get_stylesheet_directory')->justReturn($root);

    // Genesis registers these before the child theme loads; the theme removes them, so
    // a removal that does not happen (or does not match) leaves one in the snapshot.
    add_action('genesis_after_header', 'genesis_do_nav');
    add_action('genesis_after_header', 'genesis_do_subnav');
    add_action('genesis_entry_header', 'genesis_post_info', 12);
    add_action('genesis_entry_footer', 'genesis_post_meta');

    // The same for what the page templates remove.
    add_action('genesis_entry_header', 'genesis_entry_header_markup_open', 5);
    add_action('genesis_entry_header', 'genesis_entry_header_markup_close', 15);
    add_action('genesis_entry_header', 'genesis_do_post_title');
    add_action('genesis_before_loop', 'genesis_do_breadcrumbs');
    add_action('genesis_before_header', 'genesis_skip_links', 5);
    add_action('genesis_header', 'genesis_header_markup_open', 5);
    add_action('genesis_header', 'genesis_do_header');
    add_action('genesis_header', 'genesis_header_markup_close', 15);
    add_action('genesis_footer', 'genesis_footer_markup_open', 5);
    add_action('genesis_footer', 'genesis_do_footer');
    add_action('genesis_footer', 'genesis_footer_markup_close', 15);

    foreach (['add_theme_support', 'add_editor_style', 'add_image_size', 'unregister_sidebar', 'genesis_unregister_layout', 'genesis_register_responsive_menus', 'remove_theme_support', 'genesis'] as $recorded) {
        when($recorded)->alias(function (...$arguments) use ($recorded, &$state): void {
            $state['calls'][$recorded][] = $arguments;
        });
    }

    // WooCommerce is active while the theme loads, so file scope takes its WooCommerce branch.
    brief_woocommerce_active(true);

    // What file scope reads: the real config files, with every theme mod at its default.
    when('get_theme_mod')->alias(fn (string $name, $default = false) => $default);
    when('genesis_get_config')->alias(fn (string $name) => require $root.'/config/'.$name.'.php');

    require_once $root.'/functions.php';

    genesis_child_gutenberg_support();

    // The page templates run at file scope when WordPress renders a page that uses one.
    require_once $root.'/page-templates/blocks.php';
    require_once $root.'/page-templates/landing.php';

    foreach (brief_added_hooks() as $type => $hooks) {
        foreach ($hooks as $hook => $entries) {
            foreach ($entries as $entry) {
                $state['hooks'][$type][$hook][(string) $entry[0]][] = [$entry[1], $entry[2]];
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

    foreach (brief_theme_load_state()['hooks'] as $type => $by_hook) {
        foreach ($by_hook as $hook => $callbacks) {
            foreach ($callbacks as $callback => $registrations) {
                foreach ($registrations as [$priority, $accepted_args]) {
                    $hooks[] = "{$type} {$hook} {$callback} {$priority} {$accepted_args}";
                }
            }
        }
    }

    $expected = brief_require_array(__DIR__.'/load-manifest.php');

    expect($hooks)->toEqualCanonicalizing($expected['hooks'])
        ->and(brief_theme_load_state()['calls'])->toBe($expected['calls']);
}

/**
 * Makes `class_exists()` answer as given for the named classes; every other class is
 * answered by the real function (Mockery and Brain Monkey call it while a test runs).
 *
 * @param  array<string, bool>  $answers
 */
function brief_class_exists(array $answers): void
{
    when('class_exists')->alias(fn (string $class) => $answers[$class] ?? relay(func_get_args()));
}

/**
 * Makes `class_exists('WooCommerce')` answer as given.
 */
function brief_woocommerce_active(bool $active): void
{
    brief_class_exists(['WooCommerce' => $active]);
}

/**
 * Makes `function_exists()` answer as given for the named functions; the real
 * function answers for every other.
 *
 * @param  array<string, bool>  $answers
 */
function brief_function_exists(array $answers): void
{
    when('function_exists')->alias(fn (string $function) => $answers[$function] ?? relay(func_get_args()));
}
