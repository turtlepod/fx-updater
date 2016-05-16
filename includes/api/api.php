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

	/* Path */
	$path = trailingslashit( FX_UPDATER_PATH . 'includes/api/templates' );

	/* Plugins */
	if ( 'query_plugins' == $fx_updater ){
		$template = $path . 'query_plugins.php';
	}
	elseif ( 'list_plugins' == $fx_updater ){
		$template = $path . 'list_plugins.php';
	}
	elseif( 'plugin_information' == $fx_updater ){
		$template = $path . 'plugin_information.php';
	}

	/* Themes */
	elseif( 'query_themes' == $fx_updater ){
		$template = $path . 'query_themes.php';
	}

	return $template;
}


/**
 * Query Plugins
 * @since 1.0.0
 */
function fx_updater_query_plugins(){

	/* Stripslash all */
	$request = stripslashes_deep( $_REQUEST );

	/* Var */
	$group = isset( $request['group'] ) ? $request['group'] : false;
	$plugin = isset( $request['plugin'] ) ? $request['plugin'] : false;
	$plugins = isset( $request['plugins'] ) ? $request['plugins'] : array();
	$data = array();

	/* Query Type */
	$query_type = $group ? 'group' : ( $plugin ? 'plugin' : false );
	if( !$query_type ){ return $data; }

	/* Query Args */
	$args = array(
		'post_type'   => 'plugin_repo',
		'post_status' => 'publish',
	);
	if( 'plugin' == $query_type ){
		$args['posts_per_page'] = 1;
		$args['meta_key'] = 'id';
		$args['meta_value'] = esc_attr( $plugin );
	}
	elseif( 'group' == $query_type ){
		$args['posts_per_page'] = -1;
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'group_repo',
				'field'    => 'slug',
				'terms'    => sanitize_title( $group ),
			),
		);
	}

	/* WP Query */
	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			/* Get Plugin Data */
			$post_id  = get_the_ID();
			$id       = get_post_meta( $post_id, 'id', true );
			$version  = get_post_meta( $post_id, 'version', true );
			$package  = get_post_meta( $post_id, 'download_link', true );
			$tested   = get_post_meta( $post_id, 'tested', true );
			$notice   = get_post_meta( $post_id, 'upgrade_notice', true );
			if( $version ){
				/* Version compare */
				if( isset( $plugins[$id]['Version'] ) && version_compare( $plugins[$id]['Version'], $version, "<" ) ){
					$data[$id] = array(
						'slug'         => dirname( $id ),
						'plugin'       => $id,
						'new_version'  => $version,
					);
					if( $package ){
						$data[$id]['package'] = $package;
					}
					if( $tested ){
						$data[$id]['tested'] = $tested;
					}
					if( $notice ){
						$data[$id]['upgrade_notice'] = $notice;
					}
				}
				else{
					$data[$id] = array();
				}
			}
		}
	}
	wp_reset_postdata();

	return apply_filters( 'fx_updater_query_plugins', $data, $request );
}

/**
 * Query Themes
 * @since 1.0.0
 */
function fx_updater_query_themes(){

	/* Stripslash all */
	$request = stripslashes_deep( $_REQUEST );

	/* Var */
	$group = isset( $request['group'] ) ? $request['group'] : false;
	$theme = isset( $request['theme'] ) ? $request['theme'] : false;
	$themes = isset( $request['themes'] ) ? $request['themes'] : array();
	$data = array();

	/* Query Type */
	$query_type = $group ? 'group' : ( $theme ? 'theme' : false );
	if( !$query_type ){ return $data; }

	/* Query Args */
	$args = array(
		'post_type'   => 'theme_repo',
		'post_status' => 'publish',
	);
	if( 'theme' == $query_type ){
		$args['posts_per_page'] = 1;
		$args['meta_key'] = 'id';
		$args['meta_value'] = esc_attr( $theme );
	}
	elseif( 'group' == $query_type ){
		$args['posts_per_page'] = -1;
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'group_repo',
				'field'    => 'slug',
				'terms'    => sanitize_title( $group ),
			),
		);
	}

	/* WP Query */
	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			/* Get Theme Data */
			$post_id  = get_the_ID();
			$id       = get_post_meta( $post_id, 'id', true );
			$version  = get_post_meta( $post_id, 'version', true );
			$package  = get_post_meta( $post_id, 'download_link', true );
			if( $version ){
				/* Version compare */
				if( isset( $themes[$id]['Version'] ) && version_compare( $themes[$id]['Version'], $version, "<" ) ){
					$data[$id] = array(
						'theme'       => $id,
						'new_version' => $version,
						'url'         => $themes[$id]['ThemeURI'],
					);
					if( $package ){
						$data[$id]['package'] = $package;
					}
				}
				else{
					$data[$id] = array();
				}
			}
		}
	}
	wp_reset_postdata();
	return apply_filters( 'fx_updater_query_themes', $data, $request );
}


/**
 * List plugins in a group.
 * @since 1.0.0
 */
function fx_updater_list_plugins(){

	/* Stripslash all */
	$request = stripslashes_deep( $_REQUEST );

	/* Var */
	$data = array();
	$group = isset( $request['group'] ) ? $request['group'] : false;
	if( !$group ) return $data;

	/* Query Args */
	$args = array(
		'post_type'      => 'plugin_repo',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'tax_query'      => array(
			array(
				'taxonomy' => 'group_repo',
				'field'    => 'slug',
				'terms'    => sanitize_title( $group ),
			),
		),
	);

	/* WP Query */
	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			/* Get Plugin Data */
			$post_id  = get_the_ID();
			$id       = get_post_meta( $post_id, 'id', true );
			$version  = get_post_meta( $post_id, 'version', true );
			if( $id && $version ){
				$slug = dirname( $id );
				$data[$slug] = $id;
			}
		}
	}
	wp_reset_postdata();
	return apply_filters( 'fx_updater_list_plugins', $data, $request );
}

/**
 * Plugin Information
 * @since 1.0.0
 */
function fx_updater_plugin_information(){

	/* Stripslash all */
	$request = stripslashes_deep( $_REQUEST );
	$plugin = isset( $request['plugin'] ) ? $request['plugin'] : false;

	/* Data */
	$data = array();

	/* Bail early */
	if( !$plugin ){ return $data; }

	/* Query Args */
	$args = array(
		'post_type'           => 'plugin_repo',
		'posts_per_page'      => 1,
		'meta_key'            => 'id',
		'meta_value'          => esc_attr( $plugin ),
	);


	/* Query */
	$the_query = new WP_Query( $args );

	/* Start Loop */
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			/* Get Plugin Data */
			$post_id    = get_the_ID();
			$id         = get_post_meta( $post_id, 'id', true );
			$version    = get_post_meta( $post_id, 'version', true );
			$updated    = get_post_meta( $post_id, 'last_updated', true );
			$download   = get_post_meta( $post_id, 'download_link', true );
			$requires   = get_post_meta( $post_id, 'requires', true );
			$tested     = get_post_meta( $post_id, 'tested', true );
			$changelog  = get_post_meta( $post_id, 'section_changelog', true );
			/* Data */
			$data = array(
				'name'     => get_the_title( $post_id ),
				'slug'     => dirname( $id ),
				'external' => true,
				'sections' => array(
					'changelog' => fx_updater_section_markdown_to_html( $changelog ),
				),
			);
			if( $version ){
				$data['version'] = $version;
			}
			if( $updated ){
				$data['last_updated'] = $updated;
			}
			if( $download ){
				$data['download_link'] = $download;
			}
			if( $requires ){
				$data['requires'] = $requires;
			}
			if( $tested ){
				$data['tested'] = $tested;
			}
		}
	}
	wp_reset_postdata();
	return apply_filters( 'fx_updater_plugin_information', $data, $request );
}

