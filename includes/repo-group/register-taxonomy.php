<?php
/**
 * REPO GROUP: REGISTER TAXONOMY
 * - Register Custom Taxonomy
 *
 * @since 1.0.0
**/
if ( ! defined( 'WPINC' ) ) { die; }


/* === REGISTER POST TYPE === */

/* add register post type on the 'init' hook */
add_action( 'init', 'fx_updater_group_repo_register_taxonomy' );

/**
 * Register Taxonomy
 * @since  0.1.0
 */
function fx_updater_group_repo_register_taxonomy() {

	$args = array(
		'public'			=> false,
		'show_ui'			=> true,
		'show_in_nav_menus'	=> false,
		'show_tagcloud'		=> false,
		'show_admin_column'	=> true,
		'hierarchical'		=> true,
		'query_var'			=> false,
		'capabilities'		=> array(
			'manage_terms'	=> 'manage_fx_updaters',
			'edit_terms'	=> 'manage_fx_updaters',
			'delete_terms'	=> 'manage_fx_updaters',
			'assign_terms'	=> 'edit_fx_updaters',
		),
		'rewrite' => false,
		'labels' => array(
			'name'						=> _x( 'Repo Groups', 'group', 'fx-updater' ),
			'singular_name'				=> _x( 'Repo Group', 'group', 'fx-updater' ),
			'menu_name'					=> _x( 'Groups', 'group', 'fx-updater' ),
			'name_admin_bar'			=> _x( 'Groups', 'group', 'fx-updater' ),
			'search_items'				=> _x( 'Search Groups', 'group', 'fx-updater' ),
			'popular_items'				=> _x( 'Popular Groups', 'group', 'fx-updater' ),
			'all_items'					=> _x( 'All Groups', 'group', 'fx-updater' ),
			'edit_item'					=> _x( 'Edit Group', 'group', 'fx-updater' ),
			'view_item'					=> _x( 'View Group', 'group', 'fx-updater' ),
			'update_item'				=> _x( 'Update Group', 'group', 'fx-updater' ),
			'add_new_item'				=> _x( 'Add New Group', 'group', 'fx-updater' ),
			'new_item_name'				=> _x( 'New Group Name', 'group', 'fx-updater' ),
			'separate_items_with_commas'=> _x( 'Separate groups with commas', 'group', 'fx-updater' ),
			'add_or_remove_items'		=> _x( 'Add or remove groups', 'group', 'fx-updater' ),
			'choose_from_most_used'		=> _x( 'Choose from the most used groups', 'group', 'fx-updater' ),
		)
	);

	/* Register Custom Taxonomy */
	$args = apply_filters( 'group_repo_taxonomy_args', $args );
	register_taxonomy( 'group_repo', array( 'plugin_repo', 'theme_repo' ), $args );
}



/* === ADD ADMIN MENU AS SUB MENU === */

/* Admin Menu */
add_action( 'admin_menu', 'fx_updater_group_repo_admin_menu' );

/**
 * Add admin menu as sub menu in f(x) Updater Settings.
 * @since 1.0.0
 * @link https://shellcreeper.com/how-to-add-wordpress-cpt-admin-menu-as-sub-menu/
 */
function fx_updater_group_repo_admin_menu(){

	$group_repo = get_taxonomy( 'group_repo' );

	add_submenu_page(
		'fx_updater',                       // parent slug
		$group_repo->labels->name,          // page title
		$group_repo->labels->menu_name,     // menu title
		$group_repo->cap->manage_terms,     // capability (edit_fx_updaters)
		'edit-tags.php?taxonomy=group_repo' // menu slug
	);
}

/* Parent Menu Fix */
add_filter( 'parent_file', 'fx_updater_group_repo_parent_file' );

/**
 * Fix active parent admin menu to f(x) Updater settings
 * @since 1.0.0
 * @link https://shellcreeper.com/how-to-add-wordpress-cpt-admin-menu-as-sub-menu/
 */
function fx_updater_group_repo_parent_file( $parent_file ){
	global $current_screen, $self;
	if ( in_array( $current_screen->base, array( 'edit-tags', 'term' ) ) && 'group_repo' == $current_screen->taxonomy ) {
		$parent_file = 'fx_updater';
	}
	return $parent_file;
}


