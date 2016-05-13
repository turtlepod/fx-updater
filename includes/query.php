<?php
/**
 * Query Functions
**/

/* Add query variable so wordpress recognize it */
add_filter( 'query_vars', 'fx_updater_query_vars' );


/**
 * Add Query Vars
 * So WordPress could recognize it.
 * @since 1.0.0
 */
function fx_updater_query_vars( $vars ){
	$vars[] = 'fx_updater';
	return $vars;
}


/* Create JSON end point. */
add_filter( 'template_include', 'fx_updater_template_include' ) ;


/**
 * Load custom template when visiting ?fx_updater 
 * @since 1.0.0
 */
function fx_updater_template_include( $template ){

	/* Get query var */
	$fx_updater = get_query_var( 'fx_updater' );

	/* Checking theme data "?fx_updater=theme" */
	if ( 'theme' == $fx_updater ){
		$template = FX_UPDATER_PATH . 'templates/theme-data.php';
	}

	/* Checking plugin data "?fx_updater=plugin" */
	elseif( 'plugin' == $fx_updater ){
		$template = FX_UPDATER_PATH . 'templates/plugin-data.php';
	}

	/* Checking group data "?fx_updater=group" */
	elseif( 'group' == $fx_updater ){
		$template = FX_UPDATER_PATH . 'templates/group-data.php';
	}

	return $template;
}

/**
 * Theme Data Query
 * @since 1.0.0
 */
function fx_updater_theme_data(){

	/* Stripslash all */
	$request = stripslashes_deep( $_REQUEST );

	/* Data */
	$data = array();

	/* Theme Slug Not Set, bail */
	if( !isset( $request['id'] ) ){
		return apply_filters( 'fx_updater_theme_data', array(), $request );
	}

	/* == Query Theme == */

	/* Query Args */
	$args = array(
		'name'                => sanitize_title( $request['id'] ),
		'post_type'           => 'theme_repo',
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
	);

	/* Get Posts Data */
	$posts = get_posts( $args );
	if( ! isset( $posts[0] ) ){
		return apply_filters( 'fx_updater_theme_data', array(), $request );
	}

	/* Post ID */
	$post_id = $posts[0]->ID;

	/* New Version */
	$data['version'] = get_post_meta( $post_id, 'version', true );

	/* Zip File Package */
	$data['download_link'] = get_post_meta( $post_id, 'download_link', true );

	return apply_filters( 'fx_updater_theme_data', $data, $request );
}


/**
 * Plugin Data Query
 * @since 1.0.0
 */
function fx_updater_plugin_data(){

	/* Stripslash all */
	$request = stripslashes_deep( $_REQUEST );

	/* Data */
	$data = array();

	/* Plugin Slug Not Set, bail */
	if( !isset( $request['id'] ) ){
		return apply_filters( 'fx_updater_plugin_data', array(), $request );
	}

	/* == Query Plugin == */

	/* Slug */
	$slug = sanitize_title( $request['id'] );

	/* Query Args */
	$args = array(
		'name'                => $slug,
		'post_type'           => 'plugin_repo',
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
	);

	/* Get Posts Data */
	$posts = get_posts( $args );
	if( ! isset( $posts[0] ) ){
		return apply_filters( 'fx_updater_plugin_data', array(), $request );
	}

	/* Post ID */
	$post_id = $posts[0]->ID;

	/* New Version */
	$data['version'] = get_post_meta( $post_id, 'version', true );

	/* Zip File Package */
	$data['download_link'] = get_post_meta( $post_id, 'download_link', true );

	/* WP Tested */
	$data['tested'] = get_post_meta( $post_id, 'tested', true );

	/* WP Requires */
	$data['requires'] = get_post_meta( $post_id, 'requires', true );

	/* Last Updated */
	$data['last_updated'] = get_post_meta( $post_id, 'last_updated', true );

	/* Last Updated */
	$data['sections'] = array(
		'changelog' => get_post_meta( $post_id, 'section_changelog', true ),
	);

	return apply_filters( 'fx_updater_plugin_data', $data, $request );
}




















