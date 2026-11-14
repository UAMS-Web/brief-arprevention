<?php

declare(strict_types=1);
/**
 * Adds front-end inline styles for the custom Gutenberg color palette.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://minimalgenesis.com/
 */
add_action('wp_enqueue_scripts', 'brief_custom_gutenberg_css');
/**
 * Outputs front-end inline styles based on colors declared in config/appearance.php.
 *
 * @since 2.9.0
 */
function brief_custom_gutenberg_css()
{

    $appearance = genesis_get_config('appearance');

    $css = <<<CSS

.site-container .wp-block-button .wp-block-button__link {
	background-color: {$appearance['text-color']};
}

.site-container .wp-block-button.is-style-outline .wp-block-button__link {
	color: {$appearance['text-color']};
}

.site-container .wp-block-button.is-style-outline .wp-block-button__link:focus,
.site-container .wp-block-button.is-style-outline .wp-block-button__link:hover {
	color: {$appearance['text-color']};
}

.site-container .wp-block-image figcaption a {
	box-shadow: inset 0 -6px 0 0 {$appearance['link-color']};
}
CSS;

    $css .= brief_inline_font_sizes();
    $css .= brief_inline_color_palette();

    wp_add_inline_style(genesis_get_theme_handle().'-gutenberg', $css);

}

add_action('enqueue_block_editor_assets', 'brief_custom_gutenberg_admin_css');
/**
 * Outputs back-end inline styles based on colors declared in config/appearance.php.
 *
 * Note this will appear before the style-editor.css injected by JavaScript,
 * so overrides will need to have higher specificity.
 *
 * @since 2.9.0
 */
function brief_custom_gutenberg_admin_css()
{

    $appearance = genesis_get_config('appearance');

    $css = <<<CSS
.editor-styles-wrapper {
    color: {$appearance['text-color']} !important;
}

.editor-post-title__block .editor-post-title__input {
    color: {$appearance['text-color']} !important;
}
	
.block-editor__container .editor-block-list__block a {
	box-shadow: inset 0 -7px 0 0 {$appearance['link-color']};
	color: {$appearance['text-color']};
	text-decoration: none;
}

.editor-styles-wrapper .editor-rich-text .button,
.editor-styles-wrapper .wp-block-button .wp-block-button__link:not(.has-background) {
	background-color: {$appearance['text-color']};
}

.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link {
	border: 2px solid {$appearance['text-color']};
	color: {$appearance['text-color']};
}
CSS;

    wp_add_inline_style(genesis_get_theme_handle().'-gutenberg-fonts', $css);

}

/**
 * Generate CSS for editor font sizes from the provided theme support.
 *
 * @since 2.9.0
 *
 * @return string The CSS for editor font sizes if theme support was declared.
 */
function brief_inline_font_sizes()
{

    $css = '';
    $editor_font_sizes = get_theme_support('editor-font-sizes');

    if (! $editor_font_sizes) {
        return '';
    }

    foreach ($editor_font_sizes[0] as $font_size) {
        $css .= <<<CSS
		.site-container .has-{$font_size['slug']}-font-size {
			font-size: {$font_size['size']}px;
		}
CSS;
    }

    return $css;

}

/**
 * Generate CSS for editor colors based on theme color palette support.
 *
 * @since 2.9.0
 *
 * @return string The editor colors CSS if `editor-color-palette` theme support was declared.
 */
function brief_inline_color_palette()
{

    $css = '';
    $appearance = genesis_get_config('appearance');
    $editor_color_palette = $appearance['editor-color-palette'];

    foreach ($editor_color_palette as $color_info) {
        $css .= <<<CSS
		.site-container .has-{$color_info['slug']}-color,
		.site-container .wp-block-button .wp-block-button__link.has-{$color_info['slug']}-color,
		.site-container .wp-block-button.is-style-outline .wp-block-button__link.has-{$color_info['slug']}-color {
			color: {$color_info['color']};
		}

		.site-container .has-{$color_info['slug']}-background-color,
		.site-container .wp-block-button .wp-block-button__link.has-{$color_info['slug']}-background-color,
		.site-container .wp-block-pullquote.is-style-solid-color.has-{$color_info['slug']}-background-color {
			background-color: {$color_info['color']};
		}
CSS;
    }

    return $css;

}
