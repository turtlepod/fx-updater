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

/**
 * Return array of date/month/year.
 * false if not valid.
 * @param $input string.
 * @since 0.1.0
 */
function fx_updater_explode_date( $input ){
	if( !$input ){
		return false;
	}
	$output = array();
	$input = sanitize_title_with_dashes( $input );
	$data = explode( '-', $input );
	if( !isset( $data[0] ) || !isset( $data[1] ) || !isset( $data[2] ) ){
		return false;
	}
	$output['year']  = $data[0];
	$output['month'] = $data[1];
	$output['day']   = $data[2];
	if( !checkdate( $output['month'], $output['day'], $output['year'] ) ){
		return false;
	}
	return $output;
}


/**
 * Format date to text string "YYYY-MM-DD" from array of year, month, date.
 * always return a value. if not valid will return current date.
 * @param $args array of year, month, and day (as key)
 * @since 0.1.0
 */
function fx_updater_format_date( $args ){
	/* current date */
	$default = array(
		'year'  => date( 'Y' ),
		'month' => date( 'm' ),
		'day'   => date( 'd' ),
	);
	$date = wp_parse_args( $args, $default );
	$date = array_map( 'esc_attr', $date );

	if( !checkdate( $date['month'], $date['day'], $date['year'] ) ){
		$year  = $default['year'];
		$month = $default['month'];
		$day   = $default['day'];
	}
	else{
		$year  = $date['year'];
		$month = $date['month'];
		$day   = $date['day'];
	}
	return sanitize_title_with_dashes( "{$year}-{$month}-{$day}" );
}


/**
 * Sanitize Section
 * @since 0.1.0
 */
function fx_updater_sanitize_plugin_section( $input ){

	/* allowed tags */
	$plugins_allowedtags = array(
		'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ),
		'abbr' => array( 'title' => array() ), 'acronym' => array( 'title' => array() ),
		'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
		'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
		'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
		'img' => array( 'src' => array(), 'class' => array(), 'alt' => array() )
	);

	$output = wp_kses( $input, $plugins_allowedtags );
	return $output;
}
