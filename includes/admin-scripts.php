<?php
/**
 * Admin Scripts
**/

/* Load Admin Scripts */
add_action( 'admin_enqueue_scripts', 'fx_updater_admin_scripts' );


/**
 * Admin Scripts
 */
function fx_updater_admin_scripts( $hook_suffix ){
	global $post_type, $taxonomy;

	/* Only in settings page */
	if( 'toplevel_page_fx_updater' == $hook_suffix ){

		/* CSS */
		wp_enqueue_style( 'fx-updater-settings', FX_UPDATER_URI . 'assets/admin-settings.css', array(), FX_UPDATER_VERSION );

		/* JS */
		wp_enqueue_script( 'fx-updater-settings', FX_UPDATER_URI. 'assets/admin-settings.js', array( 'jquery' ), FX_UPDATER_VERSION );
	}

	/* Check post type */
	if( 'theme_repo' == $post_type || 'plugin_repo' == $post_type || 'group_repo' == $taxonomy ){

		/* Edit (Columns) */
		if( 'edit.php' == $hook_suffix || 'edit-tags.php' == $hook_suffix ){

			/* CSS */
			wp_enqueue_style( 'fx-updater-post-column', FX_UPDATER_URI . 'assets/post-columns.css', array(), FX_UPDATER_VERSION );
		}

		/* Post Edit */
		if( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) ){

			/* CSS */
			wp_enqueue_style( 'fx-updater-post-edit', FX_UPDATER_URI . 'assets/post-edit.css', array(), FX_UPDATER_VERSION );

			/* JS */
			wp_enqueue_media(); // need this.
			wp_enqueue_script( 'fx-updater-post-edit', FX_UPDATER_URI. 'assets/post-edit.js', array( 'jquery', 'jquery-ui-core', 'media-upload' ), FX_UPDATER_VERSION );
			wp_localize_script( 'fx-updater-post-edit', 'fx_upmb_upload',
				array(
					'title'  => __( 'Upload Theme ZIP', 'fx-updater' ),
					'button' => __( 'Insert ZIP File', 'fx-updater' ),
				)
			);
		}
	}
}
