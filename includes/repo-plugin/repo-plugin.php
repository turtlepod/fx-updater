<?php
/**
 * REPO PLUGIN
 * @since 1.0.0
**/
if ( ! defined( 'WPINC' ) ) { die; }

/* Path */
$path = trailingslashit( FX_UPDATER_PATH . 'includes/repo-plugin' );

/* Register Post Type */
require_once( $path . 'register-post-type.php' );

/* Admin */
if( is_admin() ){

	/* Updater Config */
	require_once( $path . 'meta-box-data.php' );

	/* Columns */
	require_once( $path . 'manage-columns.php' );
}
