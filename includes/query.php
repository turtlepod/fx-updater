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

	return $template;
}

/**
 * Theme Data Query
 * @since 1.0.0
 */
function fx_updater_theme_data( $request ){

	/* Stripslash all */
	$request = stripslashes_deep( $request );

	/* Data */
	$data = array();

	/* Theme Slug Not Set, bail */
	if( !isset( $request['id'] ) ){
		return apply_filters( 'fx_updater_theme_data', array(), $request );
	}

	/* == Query theme based on ID (slug) == */

	/* Slug */
	$slug = sanitize_title( $request['id'] );

	/* Query Args */
	$args = array(
		'name'                => $slug,
		'post_type'           => 'theme_repo',
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
	);

	/* Get Posts Data */
	$posts = get_posts( $args );
	$post_id = $posts[0]->ID;

	/* Theme Name */
	$data['name'] = get_the_title( $post_id );

	/* Theme Name */
	$data['slug'] = $slug;

	/* New Version */
	$data['version'] = get_post_meta( $post_id, 'version', true );

	/* Zip File Package */
	$data['package'] = get_post_meta( $post_id, 'download_link', true );

	return $data;
}


/**
 * Plugin Data Query
 * @since 1.0.0
 */
function fx_updater_plugin_data( $request ){
	$data = array();
	return $data;
}




















