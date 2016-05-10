<?php
/**
 * REPO THEME: REGISTER POST TYPE
 * - Register Post Type
 * - Add Admin Menu as Settings Sub Menu
 *
 * @since 1.0.0
**/
if ( ! defined( 'WPINC' ) ) { die; }


/* === REGISTER POST TYPE === */

/* add register post type on the 'init' hook */
add_action( 'init', 'fx_updater_theme_repo_register_post_type' );


/**
 * Register Post Type
 * @since  0.1.0
 */
function fx_updater_theme_repo_register_post_type() {

	/* === THEMES REPO === */

	$theme_args = array(
		'description'           => '',
		'public'                => false,
		'publicly_queryable'    => true,
		'show_in_nav_menus'     => false,
		'show_in_admin_bar'     => false,
		'exclude_from_search'   => true,
		'show_ui'               => true,
		'show_in_menu'          => false,
		'menu_position'         => null,
		'menu_icon'             => 'dashicons-update',
		'can_export'            => true,
		'delete_with_user'      => false,
		'hierarchical'          => false,
		'has_archive'           => false, 
		'query_var'             => true,
		'rewrite'               => false,
		'capability_type'       => 'theme_repo',
		'map_meta_cap'          => true,
		'capabilities'          => array(
			'edit_post'                 => 'edit_fx_updater',    // don't assign these to roles
			'read_post'                 => 'read_fx_updater',    // don't assign these to roles
			'delete_post'               => 'delete_fx_updater',  // don't assign these to roles
			'create_posts'              => 'create_fx_updaters', // primitive meta caps
			'edit_posts'                => 'edit_fx_updaters',   // primitive caps outside map_meta_cap()
			'edit_others_posts'         => 'manage_fx_updaters', // primitive caps outside map_meta_cap()
			'publish_posts'             => 'manage_fx_updaters', // primitive caps outside map_meta_cap()
			'read_private_posts'        => 'read',
			'read'                      => 'read',
			'delete_posts'              => 'manage_fx_updaters', // primitive caps inside map_meta_cap()
			'delete_private_posts'      => 'manage_fx_updaters', // primitive caps inside map_meta_cap()
			'delete_published_posts'    => 'manage_fx_updaters', // primitive caps inside map_meta_cap()
			'delete_others_posts'       => 'manage_fx_updaters', // primitive caps inside map_meta_cap()
			'edit_private_posts'        => 'edit_fx_updaters',   // primitive caps inside map_meta_cap()
			'edit_published_posts'      => 'edit_fx_updaters'    // primitive caps inside map_meta_cap()
		),
		'supports'              => array( 'title' ),
		'labels'                => array(
			'name'                      => _x( 'Themes Updater', 'post-type', 'fx-updater' ),
			'singular_name'             => _x( 'Theme Repo', 'post-type', 'fx-updater' ),
			'add_new'                   => _x( 'Add New', 'post-type', 'fx-updater' ),
			'add_new_item'              => _x( 'Add New Theme Repo', 'post-type', 'fx-updater' ),
			'edit_item'                 => _x( 'Edit Theme Repo', 'post-type', 'fx-updater' ),
			'new_item'                  => _x( 'New Theme Repo', 'post-type', 'fx-updater' ),
			'all_items'                 => _x( 'All Themes', 'post-type', 'fx-updater' ),
			'view_item'                 => _x( 'View Theme Repo', 'post-type', 'fx-updater' ),
			'search_items'              => _x( 'Search Theme Repo', 'post-type', 'fx-updater' ),
			'not_found'                 => _x( 'No Theme Repo found', 'post-type', 'fx-updater' ),
			'not_found_in_trash'        => _x( 'No Theme Repo found in Trash', 'post-type', 'fx-updater' ), 
			'menu_name'                 => _x( 'Themes', 'post-type', 'fx-updater' ),
		),
	);

	/* REGISTER "theme_repo" POST TYPE */
	register_post_type( 'theme_repo', $theme_args );
}


/* === ADD ADMIN MENU AS SUB MENU === */

/* Admin Menu */
add_action( 'admin_menu', 'fx_updater_theme_repo_admin_menu' );

/**
 * Add admin menu,
 * Submenu in fx updater settings.
 * @since 0.1.0
 */
function fx_updater_theme_repo_admin_menu(){

	/* Add Submenu Page: Plugin Repo */
	$plugin_repo = get_post_type_object( 'theme_repo' );
	add_submenu_page(
		'fx_updater',                       // parent slug
		$plugin_repo->labels->name,         // page title
		$plugin_repo->labels->menu_name,    // menu title
		$plugin_repo->cap->edit_posts,      // capability (edit_fx_updaters)
		'edit.php?post_type=theme_repo'     // menu slug
	);

}

/* Parent Menu Fix */
add_filter( 'parent_file', 'fx_updater_theme_repo_parent_file' );

/**
 * Fix Parent Admin Menu to point to f(x) Updater settings
 * @since 0.1.0
 */
function fx_updater_theme_repo_parent_file( $parent_file ){
	global $current_screen, $self;
	if ( in_array( $current_screen->base, array( 'post', 'edit' ) ) && 'theme_repo' == $current_screen->post_type ) {
		$parent_file = 'fx_updater';
	}
	return $parent_file;
}










