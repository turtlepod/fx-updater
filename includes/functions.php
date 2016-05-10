<?php
/**
 * Functions
 * @since 1.0.0
**/

/**
 * Sanitize Version
 * @since 0.1.0
 */
function fx_updater_sanitize_version( $input ){
	$output = sanitize_text_field( $input );
	$output = str_replace( ' ', '', $output );
	return trim( esc_attr( $output ) );
}



