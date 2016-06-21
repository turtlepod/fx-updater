<?php
/**
 * SETTINGS PAGE
 * @since 1.0.0
**/
if ( ! defined( 'WPINC' ) ) { die; }


/* Create Settings Page */
add_action( 'admin_menu', 'fx_updater_create_settings_page' );


/**
 * Create Settings Page
 * @since 1.0.0
 */
function fx_updater_create_settings_page(){
	
	/* Add Menu Page */
	add_menu_page(
		_x( 'Get Code', 'settings', 'fx-updater' ),         // page title
		_x( 'f(x) Updater', 'settings', 'fx-updater' ),     // menu title
		'manage_fx_updaters',                               // capability
		'fx_updater',                                       // menu slug
		'fx_updater_settings_page',                         // callback function
		'dashicons-update',                                 // dashicon
		2                                                 // position
	);

	/* Add Submenu Page: Settings */
	add_submenu_page(
		'fx_updater',                                       // parent slug
		_x( 'Get Code', 'settings', 'fx-updater' ),         // page title
		_x( 'Get Code', 'settings', 'fx-updater' ),         // menu title
		'manage_fx_updaters',                               // capability
		'fx_updater'                                        // menu slug
	);

	/* Remove Sub Menu (make it hidden) */
	remove_submenu_page( 'fx_updater', 'fx_updater' );
}


/**
 * Menu Page Callback Function
 * @since 1.0.0
 */
function fx_updater_settings_page(){
?>
	<div class="wrap">

		<h1><?php _ex( 'f(x) Updater', 'settings', 'fx-updater' ); ?></h1>

	</div><!-- wrap -->
<?php
}

