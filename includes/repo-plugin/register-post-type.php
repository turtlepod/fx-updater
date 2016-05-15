<?php
/**
 * REPO PLUGIN: REGISTER POST TYPE
 * - Register Post Type
 * - Add Admin Menu as Settings Sub Menu
 * - Edit Post: Title Placeholder
 * - Edit Post: Updated Message
 *
 * @since 1.0.0
**/
if ( ! defined( 'WPINC' ) ) { die; }


/* === REGISTER POST TYPE === */

/* add register post type on the 'init' hook */
add_action( 'init', 'fx_updater_plugin_repo_register_post_type' );

/**
 * Register Post Type
 * @since  0.1.0
 */
function fx_updater_plugin_repo_register_post_type() {

	/* === PLUGINS REPO === */

	$args = array(
		'description'           => '',
		'public'                => false,
		'publicly_queryable'    => false,
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
		'query_var'             => false,
		'rewrite'               => false,
		'capability_type'       => 'plugin_repo',
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
			'name'                      => _x( 'Plugins Updater', 'plugins', 'fx-updater' ),
			'singular_name'             => _x( 'Plugin Repo', 'plugins', 'fx-updater' ),
			'add_new'                   => _x( 'Add New', 'plugins', 'fx-updater' ),
			'add_new_item'              => _x( 'Add New Plugin Repo', 'plugins', 'fx-updater' ),
			'edit_item'                 => _x( 'Edit Plugin Repo', 'plugins', 'fx-updater' ),
			'new_item'                  => _x( 'New Plugin Repo', 'plugins', 'fx-updater' ),
			'all_items'                 => _x( 'All Plugins', 'plugins', 'fx-updater' ),
			'view_item'                 => _x( 'View Plugin', 'plugins', 'fx-updater' ),
			'search_items'              => _x( 'Search Plugin', 'plugins', 'fx-updater' ),
			'not_found'                 => _x( 'No Plugin found', 'plugins', 'fx-updater' ),
			'not_found_in_trash'        => _x( 'No Plugin found in Trash', 'plugins', 'fx-updater' ), 
			'menu_name'                 => _x( 'Plugins', 'plugins', 'fx-updater' ),
		),
	);

	/* REGISTER "plugin_repo" POST TYPE */
	register_post_type( 'plugin_repo', $args );
}


/* === ADD ADMIN MENU AS SUB MENU === */

/* Admin Menu */
add_action( 'admin_menu', 'fx_updater_plugin_repo_admin_menu' );

/**
 * Add admin menu as sub menu in f(x) Updater Settings.
 * @since 1.0.0
 * @link https://shellcreeper.com/how-to-add-wordpress-cpt-admin-menu-as-sub-menu/
 */
function fx_updater_plugin_repo_admin_menu(){

	/* Add Submenu Page: Plugin Repo */
	$plugin_repo = get_post_type_object( 'plugin_repo' );
	add_submenu_page(
		'fx_updater',                       // parent slug
		$plugin_repo->labels->name,         // page title
		$plugin_repo->labels->menu_name,    // menu title
		$plugin_repo->cap->edit_posts,      // capability (edit_fx_updaters)
		'edit.php?post_type=plugin_repo'    // menu slug
	);

}

/* Parent Menu Fix */
add_filter( 'parent_file', 'fx_updater_plugin_repo_parent_file' );

/**
 * Fix active parent admin menu to f(x) Updater settings
 * @since 1.0.0
 * @link https://shellcreeper.com/how-to-add-wordpress-cpt-admin-menu-as-sub-menu/
 */
function fx_updater_plugin_repo_parent_file( $parent_file ){
	global $current_screen, $self;
	if ( in_array( $current_screen->base, array( 'post', 'edit' ) ) && 'plugin_repo' == $current_screen->post_type ) {
		$parent_file = 'fx_updater';
	}
	return $parent_file;
}


/* === EDIT POST: TITLE PLACEHOLDER === */

/* Title Placeholder */
add_filter( 'enter_title_here', 'fx_updater_plugin_edit_title_placeholder', 10, 2 );

/**
 * Change "Enter title here" to "Plugin Name"
 * @since 0.1.0
 */
function fx_updater_plugin_edit_title_placeholder( $placeholder, $post ){
	if( 'plugin_repo' == get_post_type( $post ) ){
		$placeholder = _x( 'Plugin Name', 'plugins', 'fx-updater' );
	}
	return $placeholder;
}


/* === EDIT POST: UPDATED MESSAGE === */

/* Updated message */
add_filter( 'post_updated_messages', 'fx_updater_plugin_updated_message' );

/**
 * Custom Updated Message
 * @since 0.1.0
 */
function fx_updater_plugin_updated_message( $messages ){
	global $post, $post_ID;

	$messages['plugin_repo'] = array(
		 0 => '', // Unused. Messages start at index 1.
		 1 => _x( 'Plugin updated.', 'plugins', 'fx-updater' ),
		 2 => _x( 'Plugin field updated.', 'plugins', 'fx-updater' ),
		 3 => _x( 'Plugin field deleted.', 'plugins', 'fx-updater' ),
		 4 => _x( 'Plugin updated.', 'plugins', 'fx-updater' ),
		/* translators: %s: date and time of the revision */
		 5 => isset($_GET['revision']) ? sprintf( _x( 'Plugin restored to revision from %s', 'plugins', 'fx-updater' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		 6 => _x( 'Plugin published.', 'plugins', 'fx-updater' ),
		 7 => _x( 'Plugin saved.', 'plugins', 'fx-updater' ),
		 8 => _x( 'Plugin submitted.', 'plugins', 'fx-updater' ),
		 9 => sprintf( _x( 'Plugin scheduled for: <strong>%1$s</strong>.', 'plugins', 'fx-updater' ),
			/* translators: Publish box date format, see http://php.net/date */
			date_i18n( _x( 'M j, Y @ H:i', 'plugins', 'fx-updater' ), strtotime( $post->post_date ) ) ),
		10 => _x( 'Plugin draft updated.', 'plugins', 'fx-updater' ),
	);

	return $messages;
}

