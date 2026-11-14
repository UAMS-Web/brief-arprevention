<?php

declare(strict_types=1);
/**
 * Gutenberg theme support.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://minimalgenesis.com/
 */
add_action('wp_enqueue_scripts', 'brief_enqueue_gutenberg_frontend_styles');
/**
 * Enqueues Gutenberg front-end styles.
 *
 * @since 2.7.0
 */
function brief_enqueue_gutenberg_frontend_styles(): void
{

    wp_enqueue_style(
        genesis_get_theme_handle().'-gutenberg',
        get_stylesheet_directory_uri().'/lib/gutenberg/front-end.css',
        [genesis_get_theme_handle()],
        genesis_get_theme_version()
    );

}

add_action('enqueue_block_editor_assets', 'brief_block_editor_styles');
/**
 * Enqueues Gutenberg admin editor fonts and styles.
 *
 * @since 2.7.0
 */
function brief_block_editor_styles(): void
{

    $appearance = genesis_get_config('appearance');

    wp_enqueue_style(
        genesis_get_theme_handle().'-gutenberg-fonts',
        $appearance['fonts-url'],
        [],
        genesis_get_theme_version()
    );

}

add_filter('body_class', 'brief_blocks_body_classes');
/**
 * Adds body classes to help with block styling.
 *
 * - `has-no-blocks` if content contains no blocks.
 * - `first-block-[block-name]` to allow changes based on the first block (such as removing padding above a Cover block).
 * - `first-block-align-[alignment]` to allow styling adjustment if the first block is wide or full-width.
 *
 * @since 2.8.0
 *
 * @param  array<int, string>  $classes  The original classes.
 * @return array<int, string> The modified classes.
 */
function brief_blocks_body_classes($classes)
{

    // Equivalent under test: whether `has_blocks()` and `parse_blocks()` exist is a
    // property of the process, and once any test's stub defines them
    // no test can undefine them, so the `function_exists()` operands
    // cannot be varied independently of test order.
    // @pest-mutate-ignore: BooleanOrToBooleanAnd
    if (! is_singular() || ! function_exists('has_blocks') || ! function_exists('parse_blocks')) {
        return $classes;
    }

    if (! has_blocks()) {
        $classes[] = 'has-no-blocks';

        return $classes;
    }

    $post_object = get_post();

    if ($post_object === null) {
        return $classes;
    }

    // Equivalent: `parse_blocks()` always returns an array, so the cast changes nothing,
    // and `isset()` on the null a missing cast would leave is false just as it is on `[]`.
    // @pest-mutate-ignore: RemoveArrayCast
    $blocks = (array) parse_blocks($post_object->post_content);

    if (isset($blocks[0]['blockName'])) {
        $classes[] = 'first-block-'.str_replace('/', '-', $blocks[0]['blockName']);
    }

    if (is_string($blocks[0]['attrs']['align'] ?? null)) {
        $classes[] = 'first-block-align-'.$blocks[0]['attrs']['align'];
    }

    return $classes;

}

// Add support for editor styles.
add_theme_support('editor-styles');

// Enqueue editor styles.
add_editor_style('/lib/gutenberg/style-editor.css');

// Adds support for block alignments.
add_theme_support('align-wide');

// Make media embeds responsive.
add_theme_support('responsive-embeds');

$brief_appearance = genesis_get_config('appearance');

// Adds support for editor font sizes.
add_theme_support(
    'editor-font-sizes',
    $brief_appearance['editor-font-sizes']
);

// Adds support for editor color palette.
add_theme_support(
    'editor-color-palette',
    $brief_appearance['editor-color-palette']
);

require_once get_stylesheet_directory().'/lib/gutenberg/inline-styles.php';

add_action('after_setup_theme', 'brief_content_width', 0);
/**
 * Set content width to match the “wide” Gutenberg block width.
 */
function brief_content_width(): void
{

    $appearance = genesis_get_config('appearance');

    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- See https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/924
    $GLOBALS['content_width'] = apply_filters('brief_content_width', $appearance['content-width']);

}
