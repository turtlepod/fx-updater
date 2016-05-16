<?php
/**
 * Plugin Name: f(x) Updater
 * Plugin URI: http://genbumedia.com/plugins/fx-updater/
 * Description: Your own update server for WordPress themes and plugins.
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: fx-updater
 * Domain Path: /languages/
 *
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
**/

/* Do not access this file directly */
if ( ! defined( 'WPINC' ) ) { die; }

/* Constants
------------------------------------------ */

/* Set plugin version constant. */
define( 'FX_UPDATER_VERSION', '1.0.0' );

/* Set constant path to the plugin directory. */
define( 'FX_UPDATER_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );

/* Set the constant path to the plugin directory URI. */
define( 'FX_UPDATER_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


/* Includes
------------------------------------------ */

/* Load Utility Functions */
require_once( FX_UPDATER_PATH . 'includes/functions.php' );

/* Load Settings Functions  */
require_once( FX_UPDATER_PATH . 'includes/admin-scripts.php' );

/* Load Query Functions */
require_once( FX_UPDATER_PATH . 'includes/query.php' );

/* Load Settings Functions  */
require_once( FX_UPDATER_PATH . 'includes/get-code/settings.php' );

/* Load Group Repo Functions */
require_once( FX_UPDATER_PATH . 'includes/repo-group/repo-group.php' );

/* Load Plugin Repo Functions  */
require_once( FX_UPDATER_PATH . 'includes/repo-plugin/repo-plugin.php' );

/* Load Theme Repo Functions */
require_once( FX_UPDATER_PATH . 'includes/repo-theme/repo-theme.php' );


/* Plugins Loaded
------------------------------------------ */

/* Load plugins file */
add_action( 'plugins_loaded', 'fx_base_plugins_loaded' );

/**
 * Load plugins file
 * @since 0.1.0
 */
function fx_base_plugins_loaded(){

	/* Load Text Domain (Language Translation) */
	load_plugin_textdomain( 'fx-updater', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* Plugin Action Link */
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'fx_updater_plugin_action_links' );
}

/**
 * Add Action Link For Support
 * @since 1.0.0
 */
function fx_updater_plugin_action_links( $links ){

	/* Get current user info */
	if( function_exists( 'wp_get_current_user' ) ){
		$current_user = wp_get_current_user();
	}
	else{
		global $current_user;
		get_currentuserinfo();
	}

	/* Build support url */
	$support_url = add_query_arg(
		array(
			'about'      => urlencode( 'f(x) Updater (v.' . FX_UPDATER_VERSION . ')' ),
			'sp_name'    => urlencode( $current_user->display_name ),
			'sp_email'   => urlencode( $current_user->user_email ),
			'sp_website' => urlencode( home_url() ),
		),
		'http://genbumedia.com/contact/'
	);

	/* Add support link */
	$links[] = '<a target="_blank" href="' . esc_url( $support_url ) . '">' . __( 'Get Support', 'fx-base' ) . '</a>';

	return $links;
}


/* Activation and Uninstall
------------------------------------------ */

/* Register activation hook. */
register_activation_hook( __FILE__, 'fx_updater_plugin_activation' );


/**
 * Runs only when the plugin is activated.
 * @since 1.0.0
 */
function fx_updater_plugin_activation() {

	/* Get the administrator role. */
	$role = get_role( 'administrator' );

	/* If the administrator role exists, add required capabilities for the plugin. */
	if ( !empty( $role ) ) {
		$role->add_cap( 'manage_fx_updaters' );
		$role->add_cap( 'create_fx_updaters' );
		$role->add_cap( 'edit_fx_updaters' );
	}

	/* Temporary Data (5sec) to Add Activation Notice */
	set_transient( 'fx_updater_activation_notice', '1', 5 );

	/* uninstall plugin */
	register_uninstall_hook( __FILE__, 'fx_updater_plugin_uninstall' );
}


/**
 * Uninstall plugin
 * @since 0.1.0
 */
function fx_updater_plugin_uninstall(){

	/* Get the administrator role. */
	$role = get_role( 'administrator' );

	/* If the administrator role exists, remove added capabilities for the plugin. */
	if ( !empty( $role ) ) {
		$role->add_cap( 'manage_fx_updaters' );
		$role->add_cap( 'create_fx_updaters' );
		$role->add_cap( 'edit_fx_updaters' );
	}
}


/* Activation Notice
------------------------------------------ */

/* Add admin notice */
add_action( 'admin_notices', 'fx_updater_plugin_activation_notice' );

/**
 * Admin Notice on Activation.
 * @since 1.0.0
 */
function fx_updater_plugin_activation_notice(){
	$transient = get_transient( 'fx_updater_activation_notice' );
	if( $transient ){
		?>
		<div class="notice notice-info is-dismissible">
			<p><?php _e( 'Thank you for using our plugin :)', 'fx-updater' ); ?></p>
		</div>
		<?php
		delete_transient( 'fx_updater_activation_notice' );
	}
}


