<?php
/**
 * REPO THEME
 * @since 1.0.0
**/
if ( ! defined( 'WPINC' ) ) { die; }

/* Path */
$path = trailingslashit( FX_UPDATER_PATH . 'includes/repo-theme' );

/* Register Post Type */
require_once( $path . 'register-post-type.php' );

