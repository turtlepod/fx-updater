<?php
/**
 * GET UPDATER CODE
 * @since 1.0.0
**/
if ( ! defined( 'WPINC' ) ) { die; }

/* Path */
$path = trailingslashit( FX_UPDATER_PATH . 'includes/get-code' );

/* Admin */
if( is_admin() ){

	/* Settings */
	require_once( $path . 'settings.php' );
}



