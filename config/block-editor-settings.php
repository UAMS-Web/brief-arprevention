<?php
/**
 * Block Editor settings specific to Brief.
 *
 * @package Brief
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 * @link    https://minimalgenesis.com/
 */

$brief_link_color            = get_theme_mod( 'brief_link_color', brief_customizer_get_default_link_highlight_color() );
$brief_link_color_contrast   = brief_color_contrast( $brief_link_color );
$brief_link_color_brightness = brief_color_brightness( $brief_link_color, 35 );

return array(
	'admin-fonts-url'              => 'https://fonts.googleapis.com/css?family=Jaldi:400,700|Lora:400,400i,700,700i',
	'content-width'                => 702,
	'default-button-bg'            => $brief_link_color,
	'default-button-color'         => $brief_link_color_contrast,
	'default-button-outline-hover' => $brief_link_color_brightness,
	'editor-color-palette'         => array(
		array(
			'name'  => __( 'Highlight color', 'brief' ),
			'slug'  => 'theme-primary',
			'color' => get_theme_mod( 'brief_link_color', brief_customizer_get_default_link_highlight_color() ),
		),
		array(
			'name'  => __( 'Accent color', 'brief' ),
			'slug'  => 'theme-secondary',
			'color' => get_theme_mod( 'brief_accent_color', brief_customizer_get_default_accent_color() ),
		),
	),
	'editor-font-sizes'            => array(
		array(
			'name' => __( 'Small', 'brief' ),
			'size' => 16,
			'slug' => 'small',
		),
		array(
			'name' => __( 'Normal', 'brief' ),
			'size' => 20,
			'slug' => 'normal',
		),
		array(
			'name' => __( 'Large', 'brief' ),
			'size' => 24,
			'slug' => 'large',
		),
		array(
			'name' => __( 'Larger', 'brief' ),
			'size' => 28,
			'slug' => 'larger',
		),
	),
);
