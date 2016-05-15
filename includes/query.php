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

	/* Version */
	$version = get_post_meta( $post_id, 'version', true );

	/* Version data available */
	if( $version ){

		$data = array(
			'version'       => fx_updater_sanitize_version( $version ),
			'download_link' => esc_url_raw( get_post_meta( $post_id, 'download_link', true ) ),
		);
	}

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

	/* Query Args */
	$args = array(
		'name'                => sanitize_title( $request['id'] ),
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

	/* Version */
	$version = get_post_meta( $post_id, 'version', true );

	/* Version data available */
	if( $version ){

		$data = array(
			'version'        => fx_updater_sanitize_version( $version ),
			'download_link'  => esc_url_raw( get_post_meta( $post_id, 'download_link', true ) ),
			'tested'         => fx_updater_sanitize_version( get_post_meta( $post_id, 'tested', true ) ),
			'requires'       => fx_updater_sanitize_version( get_post_meta( $post_id, 'requires', true ) ),
			'last_updated'   => sanitize_title_with_dashes( get_post_meta( $post_id, 'last_updated', true ) ),
			'sections'       => array(
				'changelog'  => fx_updater_section_markdown_to_html( get_post_meta( $post_id, 'section_changelog', true ) ),
			),
			'upgrade_notice' => strip_tags( get_post_meta( $post_id, 'upgrade_notice', true ) ),
		);
	}

	return apply_filters( 'fx_updater_plugin_data', $data, $request );
}



/**
 * Group Data Query
 * @since 1.0.0
 */
function fx_updater_group_data(){

	/* Stripslash all */
	$request = stripslashes_deep( $_REQUEST );

	/* Data */
	$data = array();

	/* Plugin Slug Not Set, bail */
	if( !isset( $request['id'] ) ){
		return apply_filters( 'fx_updater_group_data', array(), $request );
	}

	/* == Query Plugin == */

	/* Query Args */
	$args = array(
		'post_type'           => array( 'theme_repo', 'plugin_repo' ),
		'post_status'         => 'publish',
		'posts_per_page'      => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'group_repo',
				'field'    => 'slug',
				'terms'    => sanitize_title( $request['id'] ),
			),
		),
	);

	/* Data */
	$data = array( 'themes'  => array(), 'plugins' => array() );

	/* Get Posts Data */
	$query = new WP_Query( $args );
	if ( $query->have_posts() ){

		/* Start loop */
		while ( $query->have_posts() ){
			$query->the_post();

			/* Post ID */
			$post_id = get_the_ID();

			/* Only if Plugin File/Theme Folder is set */
			if( $repo_id = get_post_meta( $post_id, 'id', true ) ){

				/* Version */
				$version = get_post_meta( $post_id, 'version', true );

				/* Data */
				if( $version && 'theme_repo' == get_post_type() ){
					$data['themes'][$repo_id] = array(
						'version'       => fx_updater_sanitize_version( $version ),
						'download_link' => esc_url_raw( get_post_meta( $post_id, 'download_link', true ) ),
					);
				}
				if( $version && 'plugin_repo' == get_post_type() ){
					$data['plugins'][$repo_id] = array(
						'version'       => fx_updater_sanitize_version( $version ),
						'download_link' => esc_url_raw( get_post_meta( $post_id, 'download_link', true ) ),
						'tested'        => fx_updater_sanitize_version( get_post_meta( $post_id, 'tested', true ) ),
						'requires'      => fx_updater_sanitize_version( get_post_meta( $post_id, 'requires', true ) ),
						'last_updated'  => sanitize_title_with_dashes( get_post_meta( $post_id, 'last_updated', true ) ),
						'sections'      => array(
							'changelog' => fx_updater_section_markdown_to_html( get_post_meta( $post_id, 'section_changelog', true ) ),
						),
						'upgrade_notice' => strip_tags( get_post_meta( $post_id, 'upgrade_notice', true ) ),
					);
				}
			}
		}
	}
	wp_reset_postdata();
	return $data;
}

