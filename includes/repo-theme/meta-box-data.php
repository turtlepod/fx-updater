<?php
/**
 * Meta Box Data
 * @since 1.0.0
**/

/* Add meta boxes on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'fx_updater_theme_data_add_meta_boxes' );

/**
 * Register Updater Config Meta Box
 * @since 1.0.0
 */
function fx_updater_theme_data_add_meta_boxes(){

	/* Add Meta Box */
	add_meta_box(
		'fx-updater-theme-data',                        // ID
		_x( 'Theme Data', 'themes', 'fx-updater' ),     // Title 
		'fx_updater_theme_data_meta_box',               // Callback
		array( 'theme_repo' ),                          // post type
		'normal',                                       // Context
		'default'                                       // Priority
	);

	/* Remove WP Core Slug Meta Box */
	remove_meta_box( 'slugdiv', array( 'theme_repo' ), 'normal' );
}


/**
 * Theme Data Meta Box Callback
 * Slug input is copied from WP Core "Slug" Meta Box.
 * 
 * @see post_slug_meta_box() "wp-admin/includes/meta-boxes.php"
 * @since 1.0.0
 */
function fx_updater_theme_data_meta_box( $post ){
	global $hook_suffix, $wp_version;
	$post_id = $post->ID;

	/** SLUG: This filter is documented in wp-admin/edit-tag-form.php */
	$editable_slug = apply_filters( 'editable_slug', $post->post_name, $post );

	/* Download ZIP */
	$download_link = get_post_meta( $post_id, 'download_link', true );

	/* Version */
	$version = 'post-new.php' == $hook_suffix ? '1.0.0' : get_post_meta( $post_id, 'version', true );
	?>

	<div class="fx-upmb-fields">

		<div class="fx-upmb-field fx-upmb-slug">
			<p>
				<label for="repo_slug"><?php _ex( 'Theme Slug', 'themes', 'fx-updater' ); ?></label>
			</p>
			<p>
				<input name="post_name" type="text" id="repo_slug" value="<?php echo esc_attr( $editable_slug ); ?>" />
			</p>
			<p class="description">
				<?php _ex( 'Use this as <code>$repo_slug</code> in updater config.', 'themes', 'fx-updater' ); ?>
			</p>
		</div><!-- .fx-upmb-field.fx-upmb-slug -->

		<div class="fx-upmb-field fx-upmb-home-url">
			<p>
				<label for="repo_uri"><?php _ex( 'Repository URL', 'themes', 'fx-updater' ); ?></label>
			</p>
			<p>
				<input type="text" autocomplete="off" id="repo_uri" value="<?php echo esc_url( trailingslashit( home_url() ) ); ?>" readonly="readonly"/>
			</p>
			<p class="description">
				<?php _ex( 'Use this as <code>$repo_uri</code> in updater config. This is your site home URL.', 'themes', 'fx-updater' ); ?>
			</p>
		</div><!-- .fx-upmb-field.fx-upmb-home-url -->

		<div class="fx-upmb-field fx-upmb-upload">
			<p>
				<label for="fxu_download_link"><?php _e( 'Theme ZIP', 'fx-updater' ); ?></label>
			</p>

			<p >
				<input id="fxu_download_link" class="fx-upmb-upload-url" autocomplete="off" placeholder="http://" name="download_link" type="url" value="<?php echo esc_url( $download_link ); ?>">
			</p>

			<p>
				<a href="#" class="button button-primary upload-zip"><?php _e( 'Upload', 'fx-updater' ); ?></a> 
				<a href="#" class="button remove-zip disabled"><?php _e( 'Remove', 'fx-updater' ); ?></a>
			</p>
			<p class="description">
				<?php _ex( 'Input URL to theme ZIP file or upload it.', 'themes', 'fx-updater' ); ?>
			</p>
		</div><!-- .fx-upmb-field.fx-upmb-upload -->

		<div class="fx-upmb-field fx-upmb-version">
			<p>
				<label for="fxu_version"><?php _e( 'Version', 'fx-updater' ); ?></label>
			</p>
			<p>
				<input id="fxu_version" autocomplete="off" name="version" placeholder="e.g 1.0.0" type="text" value="<?php echo fx_updater_sanitize_version( $version ); ?>"> 
			</p>
			<p class="description">
				<?php _ex( 'Latest theme version.', 'themes', 'fx-updater' ); ?>
			</p>
		</div><!-- .fx-upmb-field.fx-upmb-version-->

	</div><!-- .fx-upmb-form -->

	<?php
	wp_nonce_field( "fx_updater_nonce7894", "fx_updater_theme_data" );
}


/* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'fx_updater_theme_data_meta_box_save_post', 10, 2 );

/**
 * Save Theme Data
 * @since 1.0.0
 */
function fx_updater_theme_data_meta_box_save_post( $post_id, $post ){

	/* Stripslashes Submitted Data */
	$request = stripslashes_deep( $_POST );

	/* Verify nonce */
	if ( ! isset( $request['fx_updater_theme_data'] ) || ! wp_verify_nonce( $request['fx_updater_theme_data'], 'fx_updater_nonce7894' ) ){
		return $post_id;
	}
	/* Do not save on autosave */
	if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	/* Check post type and user caps. */
	$post_type = get_post_type_object( $post->post_type );
	if ( 'theme_repo' != $post->post_type || !current_user_can( $post_type->cap->edit_post, $post_id ) ){
		return $post_id;
	}

	/* == ZIP FILE == */

	/* Get (old) saved page builder data */
	$old_data = get_post_meta( $post_id, 'download_link', true );

	/* Get new submitted data and sanitize it. */
	$new_data = isset( $request['download_link'] ) ? esc_url( $request['download_link'] ) : '';

	/* New data submitted, No previous data, create it  */
	if ( $new_data && '' == $old_data ){
		add_post_meta( $post_id, 'download_link', $new_data, true );
	}
	/* New data submitted, but it's different data than previously stored data, update it */
	elseif( $new_data && ( $new_data != $old_data ) ){
		update_post_meta( $post_id, 'download_link', $new_data );
	}
	/* New data submitted is empty, but there's old data available, delete it. */
	elseif ( empty( $new_data ) && $old_data ){
		delete_post_meta( $post_id, 'download_link' );
	}

	/* == VERSION == */

	/* Get (old) saved page builder data */
	$old_data = get_post_meta( $post_id, 'version', true );

	/* Get new submitted data and sanitize it. */
	$new_data = isset( $request['version'] ) ? fx_updater_sanitize_version( $request['version'] ) : '';

	/* New data submitted, No previous data, create it  */
	if ( $new_data && '' == $old_data ){
		add_post_meta( $post_id, 'version', $new_data, true );
	}
	/* New data submitted, but it's different data than previously stored data, update it */
	elseif( $new_data && ( $new_data != $old_data ) ){
		update_post_meta( $post_id, 'version', $new_data );
	}
	/* New data submitted is empty, but there's old data available, delete it. */
	elseif ( empty( $new_data ) && $old_data ){
		delete_post_meta( $post_id, 'version' );
	}
}

