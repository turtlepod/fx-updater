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

