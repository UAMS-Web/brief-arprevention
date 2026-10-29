<?php
/**
 * Brief.
 *
 * This file adds the default theme settings to the Brief Theme.
 *
 * @package Brief
 * @author  Minimal Genesis
 * @license GPL-2.0-or-later
 * @link    https://minimalgenesis.com/
 */

add_filter( 'simple_social_default_styles', 'brief_social_default_styles' );
/**
 * Set Simple Social Icon defaults.
 *
 * @since 1.0.0
 *
 * @param array $defaults Social style defaults.
 * @return array Modified social style defaults.
 */
function brief_social_default_styles( $defaults ) {

	$args = genesis_get_config( 'simple-social-icons-settings' );

	return wp_parse_args( $args, $defaults );

}
