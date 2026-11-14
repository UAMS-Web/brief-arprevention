<?php

declare(strict_types=1);
/**
 * Brief.
 *
 * This file adds the required CSS to the front end to the Brief Theme.
 *
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 *
 * @link    https://minimalgenesis.com/
 */
add_action('wp_enqueue_scripts', 'brief_css');
/**
 * Checks the settings for the link color, and accent color.
 * If any of these value are set the appropriate CSS is output.
 *
 * @since 2.2.3
 */
function brief_css(): void
{

    $appearance = genesis_get_config('appearance');

    $color_text = get_theme_mod('brief_text_color', $appearance['default-colors']['text']);
    $color_link = get_theme_mod('brief_link_color', $appearance['default-colors']['link']);
    $logo = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');

    if ($logo) {
        $logo_height = absint($logo[2]);
        $logo_max_width = get_theme_mod('brief_logo_width', 350);
        $logo_width = absint($logo[1]);
        $logo_ratio = $logo_width / max($logo_height, 1);
        $logo_effective_height = min($logo_width, $logo_max_width) / max($logo_ratio, 1);
        $logo_padding = max(0, (60 - $logo_effective_height) / 2);
    }

    $css = '';

    // Equivalent: `$css` is the empty string here, so `.=` and `=` give the same value.
    // @pest-mutate-ignore: ConcatEqualToEqual
    $css .= ($appearance['default-colors']['text'] !== $color_text) ? sprintf(
        '

		body,
		.entry-title a,
		.widget-title a,
		.genesis-nav-menu a,
		.gs-faq__question,
		input,
		mark,
		.menu-toggle,
		select,
		.site-title a,
		.sub-menu-toggle,
		textarea {
			color: %1$s;
		}
		
		a,
		.archive-pagination li a,
		.genesis-nav-menu a:focus,
		.genesis-nav-menu a:hover,
		.genesis-nav-menu .current-menu-item > a,
		.genesis-nav-menu .sub-menu .current-menu-item > a:focus,
		.genesis-nav-menu .sub-menu .current-menu-item > a:hover,
		.menu-toggle:focus,
		.menu-toggle:hover,
		.sub-menu-toggle:focus,
		.sub-menu-toggle:hover {
			color: %1$s;
		}
		
		a.button,
		.archive-pagination li a:focus,
		.archive-pagination li a:hover,
		.archive-pagination li.active a,
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.site-container div.wpforms-container-full .wpforms-form input[type="submit"],
		.site-container div.wpforms-container-full .wpforms-form button[type="submit"],
		.site-container .nf-form-content input[type=button],
		.site-container .nf-form-content input[type=submit],
		.button {
			background-color: %1$s;
		}
		
		a.button:focus,
		a.button:hover,
		button:focus,
		button:hover,
		input[type="button"]:focus,
		input[type="button"]:hover,
		input[type="reset"]:focus,
		input[type="reset"]:hover,
		input[type="submit"]:focus,
		input[type="submit"]:hover,
		.site-container div.wpforms-container-full .wpforms-form input[type="submit"]:focus,
		.site-container div.wpforms-container-full .wpforms-form input[type="submit"]:hover,
		.site-container div.wpforms-container-full .wpforms-form button[type="submit"]:focus,
		.site-container div.wpforms-container-full .wpforms-form button[type="submit"]:hover,
		.site-container .nf-form-content input[type=button]:focus,
		.site-container .nf-form-content input[type=button]:hover,
		.site-container .nf-form-content input[type=submit]:focus,
		.site-container .nf-form-content input[type=submit]:hover,
		.button:focus,
		.button:hover {
			background-color: %1$s;
		}
		
		.nf-form-content .list-select-wrap .nf-field-element > div,
		.nf-form-content input:not([type=button]),
		.nf-form-content textarea {
			color: %1$s !important;
		}
		
		@media only screen and (min-width: 960px) {
			.nav-primary .genesis-nav-menu > .menu-highlight > a {
				background-color: %1$s;
			}
		}
		
		',
        $color_text
    ) : '';

    $css .= ($appearance['default-colors']['link'] !== $color_link) ? sprintf(
        '

		.content a,
		.genesis-nav-menu a:focus,
		.genesis-nav-menu a:hover,
		.genesis-nav-menu .current-menu-item > a,
		.genesis-nav-menu .sub-menu .current-menu-item > a:focus,
		.genesis-nav-menu .sub-menu .current-menu-item > a:hover,
		.nav-secondary a:focus,
		.nav-secondary a:hover,
		.nav-secondary .current-menu-item > a,
		.widget a {
			box-shadow: inset 0 -7px 0 0 %1$s;
		}
		
		.entry-title a {
			box-shadow: none;
		}
		
		.nav-secondary a {
			box-shadow: none;
		}

		.entry-meta a,
		.gallery-caption a,
		.site-footer a,
		.wp-caption-text a {
			box-shadow: inset 0 -6px 0 0 %1$s;
		}
		
		.nav-secondary a {
			box-shadow: none;
		}
		
		.genesis-nav-menu .sub-menu .current-menu-item > a {
			box-shadow: inset 0 -6px 0 0 %1$s;
		}
		
		',
        $color_link
    ) : '';

    $css .= (has_custom_logo() && ($logo_effective_height >= 200)) ?
        '
		.site-header {
			position: static;
		}
		'
    : '';

    $css .= (has_custom_logo() && ($logo_max_width !== 350)) ? sprintf(
        '
		.wp-custom-logo .site-container .title-area {
			max-width: %spx;
		}
		',
        $logo_max_width
    ) : '';

    // Place menu below logo and center logo once it gets big.
    $css .= (has_custom_logo() && ($logo_max_width >= 600)) ?
        '
		.wp-custom-logo .title-area,
		.wp-custom-logo .menu-toggle,
		.wp-custom-logo .nav-primary {
			float: none;
		}

		.wp-custom-logo .title-area {
			margin: 0 auto;
			text-align: center;
		}

		@media only screen and (min-width: 960px) {
			.wp-custom-logo .nav-primary {
				text-align: center;
			}

			.wp-custom-logo .nav-primary .sub-menu {
				text-align: left;
			}
		}
		'
    : '';

    $css .= (has_custom_logo() && $logo_padding && ($logo_effective_height > 1)) ? sprintf(
        '
		.wp-custom-logo .title-area {
			padding-top: %spx;
		}
		',
        $logo_padding + 5
    ) : '';

    if ($css) {
        wp_add_inline_style(genesis_get_theme_handle(), $css);
    }

}
