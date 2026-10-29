<?php
/**
 * Brief appearance settings.
 *
 * @package Brief
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 * @link    https://minimalgenesis.com/
 */

$brief_default_colors = [
	'text'   => '#333',
	'link'   => '#fff385',
];

$brief_text_color = get_theme_mod(
	'brief_text_color',
	$brief_default_colors['text']
);

$brief_link_color = get_theme_mod(
	'brief_link_color',
	$brief_default_colors['link']
);

return [
	'fonts-url'            => 'https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i|Open+Sans:700',
	'content-width'        => 1062,
	'text-color'           => $brief_text_color,
	'link-color'           => $brief_link_color,
	'button-color'         => $brief_text_color,
	'default-colors'       => $brief_default_colors,
	'editor-color-palette' => [
		[
			'name'  => __( 'Text color', 'brief' ),
			'slug'  => 'theme-primary',
			'color' => $brief_text_color,
		],
		[
			'name'  => __( 'Link color', 'brief' ),
			'slug'  => 'theme-secondary',
			'color' => $brief_link_color,
		],
	],
	'editor-font-sizes'    => [
		[
			'name' => __( 'Small', 'brief' ),
			'size' => 14.4,
			'slug' => 'small',
		],
		[
			'name' => __( 'Normal', 'brief' ),
			'size' => 18,
			'slug' => 'normal',
		],
		[
			'name' => __( 'Large', 'brief' ),
			'size' => 20,
			'slug' => 'large',
		],
		[
			'name' => __( 'Larger', 'brief' ),
			'size' => 24,
			'slug' => 'larger',
		],
	],
];
