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
 */
function fx_updater_create_settings_page(){
	
	/* Add Menu Page */
	add_menu_page(
		_x( 'Updater', 'settings', 'fx-updater' ),          // page title
		_x( 'f(x) Updater', 'settings', 'fx-updater' ),     // menu title
		'manage_fx_updaters',                               // capability
		'fx_updater',                                       // menu slug
		'fx_updater_updater_page',                         // callback function
		'dashicons-update',                                 // dashicon
		100                                                 // position
	);

	/* Add Submenu Page: Settings */
	add_submenu_page(
		'fx_updater',                                       // parent slug
		_x( 'Updater', 'settings', 'fx-updater' ),          // page title
		_x( 'Updater', 'settings', 'fx-updater' ),          // menu title
		'manage_fx_updaters',                               // capability
		'fx_updater'                                        // menu slug
	);

}


/**
 * Menu Page Callback Function
 */
function fx_updater_updater_page(){
?>
	<div class="wrap">

		<h1><?php _ex( 'f(x) Updater', 'settings', 'fx-updater' ); ?></h1>

		<form method="post" action="options.php">
			<?php settings_fields( 'fx_updater' ); ?>
			<?php do_settings_sections( 'fx_updater' ); ?>
			<?php submit_button(); ?>
		</form>

	</div><!-- wrap -->
<?php
}
