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
	$url        = add_query_arg( 'page', 'fx_updater', admin_url( 'admin.php' ) );
	$theme_url  = add_query_arg( 'view', 'single_theme', $url );
	$plugin_url = add_query_arg( 'view', 'single_plugin', $url );
	$code_url   = add_query_arg( 'view', 'get_code', $url );
?>
	<div class="wrap">

		<h1><?php _ex( 'f(x) Updater', 'settings', 'fx-updater' ); ?></h1>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a class="nav-tab <?php fx_updater_tab_class(); ?>" href="<?php echo esc_url( $url ); ?>">Updater Plugin</a>
			<a class="nav-tab  <?php fx_updater_tab_class( 'single_theme' ); ?>" href="<?php echo esc_url( $theme_url ); ?>">Single Theme</a>
			<a class="nav-tab  <?php fx_updater_tab_class( 'single_plugin' ); ?>" href="<?php echo esc_url( $plugin_url ); ?>">Single Plugin</a>
			<a class="nav-tab  <?php fx_updater_tab_class( 'get_code' ); ?>" href="<?php echo esc_url( $code_url ); ?>">Get Code</a>
		</h2>

		<form method="post" action="<?php echo esc_url( $url ); ?>">
			<?php submit_button(); ?>
		</form>

	</div><!-- wrap -->
<?php
}

/**
 * Active Tab Class
 * @since 1.0.0
 */
function fx_updater_tab_class( $page = "" ){
	$class = "";
	if( isset( $_GET['view'] ) && !empty( $_GET['view'] ) ){
		if( $page == $_GET['view'] ){
			$class = "nav-tab-active";
		}
	}
	else{
		if( '' == $page ){
			$class = "nav-tab-active";
		}
	}
	echo $class;
}














