<?php

/**
 * The hooks and callbacks in functions.php.
 *
 * File scope runs once, in tests/bootstrap.php, so what it registered is read
 * back from the snapshot taken there. The one statement that is a no-op under
 * test is the responsive-menus call guarded by `function_exists()`: Genesis is
 * not loaded, the guard is false, and the call is unreachable here.
 *
 * @see functions.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

it('registers each callback on its hook at its priority', function (string $type, string $hook, string $callback, int $priority): void {
    expect(brief_registered_hooks($type, $hook, $callback))->toBe($priority);
})->with([
    'textdomain' => ['actions', 'after_setup_theme', 'brief_localization_setup', 10],
    'gutenberg support' => ['actions', 'after_setup_theme', 'genesis_child_gutenberg_support', 10],
    'theme supports' => ['actions', 'after_setup_theme', 'brief_theme_support', 9],
    'post type supports' => ['actions', 'after_setup_theme', 'brief_post_type_support', 9],
    'styles' => ['actions', 'wp_enqueue_scripts', 'brief_enqueue_scripts_styles', 10],
    'primary navigation' => ['actions', 'genesis_header', 'genesis_do_nav', 12],
    'secondary navigation' => ['actions', 'genesis_footer', 'genesis_do_subnav', 5],
    'entry info' => ['actions', 'genesis_entry_header', 'genesis_post_info', 9],
    'entry meta' => ['actions', 'genesis_entry_header', 'genesis_post_meta', 14],
    'editor menus' => ['actions', 'admin_menu', 'hide_menu', 10],
    'secondary menu depth' => ['filters', 'wp_nav_menu_args', 'brief_secondary_menu_args', 10],
    'edit link' => ['filters', 'genesis_edit_post_link', '__return_false', 10],
    'read more' => ['filters', 'get_the_content_more_link', 'brief_read_more_link', 10],
    'author box gravatar' => ['filters', 'genesis_author_box_gravatar_size', 'brief_author_box_gravatar', 10],
    'comment gravatar' => ['filters', 'genesis_comment_list_args', 'brief_comments_gravatar', 10],
    'form one button' => ['filters', 'gform_submit_button_1', '__return_false', 10],
    'form two button' => ['filters', 'gform_submit_button_2', '__return_false', 10],
]);

it('adds the image sizes and drops the sidebars and layouts it does not use', function (): void {
    expect(brief_recorded_calls('add_image_size'))->toBe([
        ['sidebar-featured', 75, 75, true],
        ['genesis-singular-images', 702, 526, true],
    ])
        ->and(brief_recorded_calls('unregister_sidebar'))->toBe([['header-right'], ['sidebar-alt']])
        ->and(brief_recorded_calls('genesis_unregister_layout'))->toBe([
            ['content-sidebar-sidebar'],
            ['sidebar-content-sidebar'],
            ['sidebar-sidebar-content'],
        ]);
});

it('loads the child theme text domain from its languages folder', function (): void {
    brief_stub_genesis();
    when('get_stylesheet_directory')->justReturn('/theme');
    Functions\expect('load_child_theme_textdomain')->once()->with('brief', '/theme/languages');

    brief_localization_setup();
});

it('enqueues the fonts and dashicons, and the AMP sheet only under AMP', function (bool $amp): void {
    brief_stub_genesis();
    when('get_stylesheet_directory_uri')->justReturn('https://example.test/theme');
    when('genesis_is_amp')->justReturn($amp);

    $styles = [];
    when('wp_enqueue_style')->alias(function (...$arguments) use (&$styles): void {
        $styles[] = $arguments;
    });

    brief_enqueue_scripts_styles();

    $expected = [
        ['brief-fonts', 'https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i|Open+Sans:700', [], '9.9.9'],
        ['dashicons'],
    ];

    if ($amp) {
        $expected[] = ['brief-amp', 'https://example.test/theme/lib/amp/amp.css', ['brief'], '9.9.9'];
    }

    expect($styles)->toBe($expected);
})->with([
    'not AMP' => [false],
    'AMP' => [true],
]);

it('adds every theme support from config and every post type support from config', function (): void {
    brief_stub_genesis();

    $features = [];
    when('add_theme_support')->alias(function (string $feature, $args) use (&$features): void {
        $features[$feature] = $args;
    });
    $post_types = [];
    when('add_post_type_support')->alias(function (string $post_type, $args) use (&$post_types): void {
        $post_types[$post_type] = $args;
    });

    brief_theme_support();
    brief_post_type_support();

    expect($features)->toBe(require dirname(__DIR__, 2).'/config/theme-supports.php')
        ->and($post_types)->toBe(require dirname(__DIR__, 2).'/config/post-type-supports.php');
});

it('limits only the secondary menu to one level', function (): void {
    expect(brief_secondary_menu_args(['theme_location' => 'secondary', 'depth' => 0]))
        ->toBe(['theme_location' => 'secondary', 'depth' => 1])
        ->and(brief_secondary_menu_args(['theme_location' => 'primary', 'depth' => 0]))
        ->toBe(['theme_location' => 'primary', 'depth' => 0]);
});

it('links the read more text to the permalink', function (): void {
    when('get_permalink')->justReturn('https://example.test/post/');

    expect(brief_read_more_link())
        ->toBe('... <a class="more-link" href="https://example.test/post/">Continue Reading</a>');
});

it('sets the gravatar sizes', function (): void {
    expect(brief_author_box_gravatar())->toBe(90)
        ->and(brief_comments_gravatar(['avatar_size' => 48, 'style' => 'ol']))
        ->toBe(['avatar_size' => 60, 'style' => 'ol']);
});

describe('hide_menu', function (): void {
    it('does nothing for a user who is not an editor', function (): void {
        when('wp_get_current_user')->justReturn((object) ['roles' => ['author']]);
        Functions\expect('get_role')->never();
        Functions\expect('remove_submenu_page')->never();

        hide_menu();
    });

    it('reads a single role given as a string', function (): void {
        when('wp_get_current_user')->justReturn((object) ['roles' => 'editor']);
        when('current_user_can')->justReturn(true);
        when('remove_submenu_page')->justReturn();

        global $submenu;
        $submenu = ['themes.php' => [6 => 'Customize']];

        hide_menu();

        expect($submenu['themes.php'])->toBe([]);
    });

    it('hides the menu pages from an editor and grants the capability when missing', function (bool $has_capability): void {
        when('wp_get_current_user')->justReturn((object) ['roles' => ['editor']]);
        when('current_user_can')->justReturn($has_capability);

        $role = Mockery::mock();
        if ($has_capability) {
            Functions\expect('get_role')->never();
        } else {
            $role->shouldReceive('add_cap')->once()->with('edit_theme_options');
            Functions\expect('get_role')->once()->with('editor')->andReturn($role);
        }

        $removed = [];
        when('remove_submenu_page')->alias(function (string $menu, string $submenu) use (&$removed): void {
            $removed[] = [$menu, $submenu];
        });

        global $submenu;
        $submenu = ['themes.php' => [5 => 'Themes', 6 => 'Customize', 7 => 'Widgets']];

        hide_menu();

        expect($removed)->toBe([
            ['themes.php', 'themes.php'],
            ['themes.php', 'widgets.php'],
            ['themes.php', 'customize.php'],
        ])->and($submenu['themes.php'])->toBe([5 => 'Themes', 7 => 'Widgets']);
    })->with([
        'without the capability' => [false],
        'with the capability' => [true],
    ]);
});
