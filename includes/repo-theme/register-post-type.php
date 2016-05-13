<?php
/**
 * REPO THEME: REGISTER POST TYPE
 * - Register Post Type
 * - Add Admin Menu as Settings Sub Menu
 * - Edit Post: Title Placeholder
 * - Edit Post: Updated Message
 * - Admin Scripts
 *
 * @since 1.0.0
**/
if ( ! defined( 'WPINC' ) ) { die; }


/* === REGISTER POST TYPE === */

/* add register post type on the 'init' hook */
add_action( 'init', 'fx_updater_theme_register_post_type' );


/**
 * Register Post Type
 * @since  0.1.0
 */
function fx_updater_theme_register_post_type() {

	/* === THEMES REPO === */

	$theme_args = array(
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
			'name'                      => _x( 'Themes Repository', 'themes', 'fx-updater' ),
			'singular_name'             => _x( 'Theme', 'themes', 'fx-updater' ),
			'add_new'                   => _x( 'Add New', 'themes', 'fx-updater' ),
			'add_new_item'              => _x( 'Add New Theme Repo', 'themes', 'fx-updater' ),
			'edit_item'                 => _x( 'Edit Theme', 'themes', 'fx-updater' ),
			'new_item'                  => _x( 'New Theme', 'themes', 'fx-updater' ),
			'all_items'                 => _x( 'All Themes', 'themes', 'fx-updater' ),
			'view_item'                 => _x( 'View Theme', 'themes', 'fx-updater' ),
			'search_items'              => _x( 'Search Theme', 'themes', 'fx-updater' ),
			'not_found'                 => _x( 'No Theme Found', 'themes', 'fx-updater' ),
			'not_found_in_trash'        => _x( 'No Theme Found in Trash', 'themes', 'fx-updater' ), 
			'menu_name'                 => _x( 'Themes', 'themes', 'fx-updater' ),
		),
	);

	/* Register "theme_repo" post type */
	register_post_type( 'theme_repo', $theme_args );
}


/* === ADD ADMIN MENU AS SUB MENU === */

/* Admin Menu */
add_action( 'admin_menu', 'fx_updater_theme_admin_menu' );

/**
 * Add admin menu,
 * Submenu in fx updater settings.
 * @since 0.1.0
 */
function fx_updater_theme_admin_menu(){

	$cpt_obj = get_post_type_object( 'theme_repo' );
	add_submenu_page(
		'fx_updater',                     // parent slug
		$cpt_obj->labels->name,           // page title
		$cpt_obj->labels->menu_name,      // menu title
		$cpt_obj->cap->edit_posts,        // capability
		'edit.php?post_type=theme_repo'   // menu slug
	);
}

/* Parent Menu Fix */
add_filter( 'parent_file', 'fx_updater_theme_parent_file' );

/**
 * Fix Parent Admin Menu to point to f(x) Updater settings
 * @since 0.1.0
 */
function fx_updater_theme_parent_file( $parent_file ){
	global $current_screen, $self;
	if ( in_array( $current_screen->base, array( 'post', 'edit' ) ) && 'theme_repo' == $current_screen->post_type ) {
		$parent_file = 'fx_updater';
	}
	return $parent_file;
}


/* === EDIT POST: TITLE PLACEHOLDER === */

/* Title Placeholder */
add_filter( 'enter_title_here', 'fx_updater_theme_edit_title_placeholder', 10, 2 );

/**
 * Change "Enter title here" to "Plugin Name"
 * @since 0.1.0
 */
function fx_updater_theme_edit_title_placeholder( $placeholder, $post ){
	if( 'theme_repo' == get_post_type( $post ) ){
		$placeholder = _x( 'Theme Name', 'themes', 'fx-updater' );
	}
	return $placeholder;
}


/* === EDIT POST: UPDATED MESSAGE === */

/* Updated message */
add_filter( 'post_updated_messages', 'fx_updater_theme_updated_message' );

/**
 * Custom Updated Message
 * @since 0.1.0
 */
function fx_updater_theme_updated_message( $messages ){
	global $post, $post_ID;

	$messages['theme_repo'] = array(
		 0 => '', // Unused. Messages start at index 1.
		 1 => _x( 'Theme updated.', 'themes', 'fx-updater' ),
		 2 => _x( 'Theme field updated.', 'themes', 'fx-updater' ),
		 3 => _x( 'Theme field deleted.', 'themes', 'fx-updater' ),
		 4 => _x( 'Theme updated.', 'themes', 'fx-updater' ),
		/* translators: %s: date and time of the revision */
		 5 => isset($_GET['revision']) ? sprintf( _x( 'Theme restored to revision from %s', 'themes', 'fx-updater' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		 6 => _x( 'Theme published.', 'themes', 'fx-updater' ),
		 7 => _x( 'Theme saved.', 'themes', 'fx-updater' ),
		 8 => _x( 'Theme submitted.', 'themes', 'fx-updater' ),
		 9 => sprintf( _x( 'Theme scheduled for: <strong>%1$s</strong>.', 'themes', 'fx-updater' ),
			/* translators: Publish box date format, see http://php.net/date */
			date_i18n( _x( 'M j, Y @ H:i', 'themes', 'fx-updater' ), strtotime( $post->post_date ) ) ),
		10 => _x( 'Theme draft updated.', 'themes', 'fx-updater' ),
	);

	return $messages;
}

/* === ADMIN SCRIPTS === */


/* Load Admin Scripts */
add_action( 'admin_enqueue_scripts', 'fx_updater_theme_admin_scripts' );


/**
 * Admin Scripts
 */
function fx_updater_theme_admin_scripts( $hook ){
	global $post_type;

	/* Check post type */
	if( 'theme_repo' == $post_type ){

		/* Edit (Columns) */
		if( 'edit.php' == $hook ){

			/* CSS */
			wp_enqueue_style( 'fx-updater-theme-admin-column', FX_UPDATER_URI . 'assets/admin-theme-columns.css', array(), FX_UPDATER_VERSION );

			/* JS */
			wp_enqueue_script( 'fx-updater-theme-admin-column', FX_UPDATER_URI. 'assets/admin-theme-columns.js', array( 'jquery' ), FX_UPDATER_VERSION );
		}

		/* Post Edit */
		if( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ){

			/* CSS */
			wp_enqueue_style( 'fx-updater-theme-admin-edit', FX_UPDATER_URI . 'assets/admin-theme-edit.css', array(), FX_UPDATER_VERSION );

			/* JS */
			wp_enqueue_media(); // need this.
			wp_enqueue_script( 'fx-updater-theme-admin-edit', FX_UPDATER_URI. 'assets/admin-theme-edit.js', array( 'jquery', 'jquery-ui-core', 'media-upload' ), FX_UPDATER_VERSION );
			wp_localize_script( 'fx-updater-theme-admin-edit', 'fx_upmb_upload',
				array(
					'title'  => _x( 'Upload Theme ZIP', 'themes', 'fx-updater' ),
					'button' => _x( 'Insert ZIP File', 'themes', 'fx-updater' ),
				)
			);
		}
	}
}


