<?php
/**
 * Meta Box Data
 * @since 1.0.0
**/

/* Add meta boxes on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'fx_updater_plugin_data_add_meta_boxes' );

/**
 * Register Updater Config Meta Box
 * @since 1.0.0
 */
function fx_updater_plugin_data_add_meta_boxes(){

	/* Add Meta Box */
	add_meta_box(
		'fx-updater-plugin-data',                        // ID
		_x( 'Plugin Data', 'plugins', 'fx-updater' ),     // Title 
		'fx_updater_plugin_data_meta_box',               // Callback
		array( 'plugin_repo' ),                          // post type
		'normal',                                        // Context
		'default'                                        // Priority
	);

	/* Remove WP Core Slug Meta Box */
	remove_meta_box( 'slugdiv', array( 'plugin_repo' ), 'normal' );
}


/**
 * Plugin Data Meta Box Callback
 * Slug input is copied from WP Core "Slug" Meta Box.
 * 
 * @see post_slug_meta_box() "wp-admin/includes/meta-boxes.php"
 * @since 1.0.0
 */
function fx_updater_plugin_data_meta_box( $post ){
	global $hook_suffix, $wp_version;
	$post_id = $post->ID;

	/** SLUG: This filter is documented in wp-admin/edit-tag-form.php */
	$editable_slug = apply_filters( 'editable_slug', $post->post_name, $post );

	/* Download ZIP */
	$download_link = get_post_meta( $post_id, 'download_link', true );

	/* Version */
	$version = 'post-new.php' == $hook_suffix ? '1.0.0' : get_post_meta( $post_id, 'version', true );

	/* Last Updated */
	$last_updated = fx_updater_explode_date( get_post_meta( $post_id, 'last_updated', true ) );
	$year  = date( 'Y' );
	$month = date( 'm' );
	$day   = date( 'd' );
	if( $last_updated ){
		$year  = 'post-new.php' == $hook_suffix ? $year : $last_updated['year'];
		$month = 'post-new.php' == $hook_suffix ? $month : $last_updated['month'];
		$day   = 'post-new.php' == $hook_suffix ? $day : $last_updated['day'];
	}

	/* WP Version */
	$wp_requires = 'post-new.php' == $hook_suffix ? $wp_version : get_post_meta( $post_id, 'requires', true );
	$wp_tested = 'post-new.php' == $hook_suffix ? $wp_version : get_post_meta( $post_id, 'tested', true );

	/* Changelog */
	$changelog = get_post_meta( $post_id, 'section_changelog', true );
	?>

	<div class="fx-upmb-fields">

		<div class="fx-upmb-field fx-upmb-slug">
			<div class="fx-upmb-field-label">
				<p>
					<label for="repo_slug"><?php _ex( 'Plugin Slug', 'plugins', 'fx-updater' ); ?></label>
				</p>
			</div><!-- .fx-upmb-field-label -->
			<div class="fx-upmb-field-content">
				<p>
					<input name="post_name" type="text" id="repo_slug" value="<?php echo esc_attr( $editable_slug ); ?>" />
				</p>
				<p class="description">
					<?php _ex( 'Use this as $repo_slug in updater config.', 'plugins', 'fx-updater' ); ?>
				</p>
			</div><!-- .fx-upmb-field-content -->
		</div><!-- .fx-upmb-field.fx-upmb-slug -->

		<div class="fx-upmb-field fx-upmb-home-url">
			<div class="fx-upmb-field-label">
				<p>
					<label for="repo_uri"><?php _ex( 'Repository URL', 'plugins', 'fx-updater' ); ?></label>
				</p>
			</div><!-- .fx-upmb-field-label -->
			<div class="fx-upmb-field-content">
				<p>
					<input type="text" autocomplete="off" id="repo_uri" value="<?php echo esc_url( trailingslashit( home_url() ) ); ?>" readonly="readonly"/>
				</p>
				<p class="description">
					<?php _ex( 'Use this as $repo_uri in updater config. This is your site home URL.', 'plugins', 'fx-updater' ); ?>
				</p>
			</div><!-- .fx-upmb-field-content -->
		</div><!-- .fx-upmb-field.fx-upmb-home-url -->

		<div class="fx-upmb-field fx-upmb-upload">
			<div class="fx-upmb-field-label">
				<p>
					<label for="fxu_download_link"><?php _ex( 'Plugin ZIP', 'plugins', 'fx-updater' ); ?></label>
				</p>
			</div><!-- .fx-upmb-field-label -->
			<div class="fx-upmb-field-content">
				<p >
					<input id="fxu_download_link" class="fx-upmb-upload-url" autocomplete="off" placeholder="http://" name="download_link" type="url" value="<?php echo esc_url( $download_link ); ?>">
				</p>

				<p>
					<a href="#" class="button button-primary upload-zip"><?php _ex( 'Upload', 'plugins', 'fx-updater' ); ?></a> 
					<a href="#" class="button remove-zip disabled"><?php _ex( 'Remove', 'plugins', 'fx-updater' ); ?></a>
				</p>
				<p class="description">
					<?php _ex( 'Input plugin ZIP URL or upload it.', 'plugins', 'fx-updater' ); ?>
				</p>
			</div><!-- .fx-upmb-field-content -->
		</div><!-- .fx-upmb-field.fx-upmb-upload -->

		<div class="fx-upmb-field fx-upmb-version">
			<div class="fx-upmb-field-label">
				<p>
					<label for="fxu_version"><?php _ex( 'Version', 'plugins', 'fx-updater' ); ?></label>
				</p>
			</div><!-- .fx-upmb-field-label -->
			<div class="fx-upmb-field-content">
				<p>
					<input id="fxu_version" autocomplete="off" name="version" placeholder="e.g 1.0.0" type="text" value="<?php echo fx_updater_sanitize_version( $version ); ?>"> 
					<span class="fx-upmb-desc"><?php _ex( 'Latest plugin version.', 'plugins', 'fx-updater' ); ?></span>
				</p>
			</div><!-- .fx-upmb-field-content -->
		</div><!-- .fx-upmb-field.fx-upmb-version-->

		<div class="fx-upmb-field fx-upmb-last-updated">
			<div class="fx-upmb-field-label">
				<p>
					<label for="last_updated_month"><?php _ex( 'Release Date', 'plugins', 'fx-updater' ); ?></label>
				</p>
			</div><!-- .fx-upmb-field-label -->
			<div class="fx-upmb-field-content">
				<p>
					<select id="last_updated_month" name="last_updated_month" class="fx-upmb-month">
						<option value="01" <?php selected( '01', $month ); ?>><?php _ex( '01-Jan', 'plugins', 'fx-updater' ); ?></option>
						<option value="02" <?php selected( '02', $month ); ?>><?php _ex( '02-Feb', 'plugins', 'fx-updater' ); ?></option>
						<option value="03" <?php selected( '03', $month ); ?>><?php _ex( '03-Mar', 'plugins', 'fx-updater' ); ?></option>
						<option value="04" <?php selected( '04', $month ); ?>><?php _ex( '04-Apr', 'plugins', 'fx-updater' ); ?></option>
						<option value="05" <?php selected( '05', $month ); ?>><?php _ex( '05-May', 'plugins', 'fx-updater' ); ?></option>
						<option value="06" <?php selected( '06', $month ); ?>><?php _ex( '06-Jun', 'plugins', 'fx-updater' ); ?></option>
						<option value="07" <?php selected( '07', $month ); ?>><?php _ex( '07-Jul', 'plugins', 'fx-updater' ); ?></option>
						<option value="08" <?php selected( '08', $month ); ?>><?php _ex( '08-Aug', 'plugins', 'fx-updater' ); ?></option>
						<option value="09" <?php selected( '09', $month ); ?>><?php _ex( '09-Sep', 'plugins', 'fx-updater' ); ?></option>
						<option value="10" <?php selected( '10', $month ); ?>><?php _ex( '10-Oct', 'plugins', 'fx-updater' ); ?></option>
						<option value="11" <?php selected( '11', $month ); ?>><?php _ex( '11-Nov', 'plugins', 'fx-updater' ); ?></option>
						<option value="12" <?php selected( '12', $month ); ?>><?php _ex( '12-Dec', 'plugins', 'fx-updater' ); ?></option>
					</select>
					<input autocomplete="off" name="last_updated_day" type="text" value="<?php echo esc_attr( $day ); ?>" class="fx-upmb-day" size="2">, 
					<input autocomplete="off" name="last_updated_year" type="text" value="<?php echo esc_attr( $year ); ?>" class="fx-upmb-year" size="4">
				</p>
				<p class="description"><?php _ex( 'Last updated date. (Month, Date, Year)', 'plugins', 'fx-updater' ); ?></p>
			</div><!-- .fx-upmb-field-content -->
		</div><!-- .fx-upmb-field.fx-upmb-last-updated-->

		<div class="fx-upmb-field fx-upmb-wp-version">
			<div class="fx-upmb-field-label">
				<p>
					<label for="wp_requires"><?php _ex( 'WP Version', 'plugins', 'fx-updater' ); ?></label>
				</p>
			</div><!-- .fx-upmb-field-label -->
			<div class="fx-upmb-field-content">
				<p>
					<input id="wp_requires" autocomplete="off" name="requires" type="text" value="<?php echo esc_attr( $wp_requires ); ?>"> 
					<span class="fx-upmb-desc"><?php _ex( 'Minimum/Required WordPress version.', 'plugins', 'fx-updater' ); ?></span>
				</p>
				<p>
					<input id="wp_tested" autocomplete="off" name="tested" type="text" value="<?php echo esc_attr( $wp_tested ); ?>"> 
					<span class="fx-upmb-desc"><?php _ex( 'Up to/Tested WordPress version.', 'plugins', 'fx-updater' ); ?></span>
				</p>
			</div><!-- .fx-upmb-field-content -->
		</div><!-- .fx-upmb-field.fx-upmb-wp-version-->

		<div class="fx-upmb-field fx-upmb-changelog">
			<div class="fx-upmb-field-label">
				<p>
					<label for="fxu_changelog"><?php _ex( 'Changelog', 'plugins', 'fx-updater' ); ?></label>
				</p>
			</div><!-- .fx-upmb-field-label -->
			<div class="fx-upmb-field-content">
				<div class="fx-upmb-changelog-area">
					<textarea id="fxu_changelog" name="section_changelog" rows="8" cols="30" placeholder="<?php _ex( 'Your plugin changelog here...', 'plugins', 'fx-updater' ); ?>"><?php echo esc_textarea( fx_updater_sanitize_plugin_section( $changelog ) ); ?></textarea>
				</div>
				<p class="description">
					<?php _ex( 'Add changelog using markdown or HTML.', 'plugins', 'fx-updater' ); ?>
				</p>
			</div><!-- .fx-upmb-field-content -->
		</div><!-- .fx-upmb-field.fx-upmb-changelog-->

	</div><!-- .fx-upmb-form -->

	<?php
	wp_nonce_field( "fx_updater_nonce1248", "fx_updater_plugin_data" );
}


/* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'fx_updater_plugin_data_meta_box_save_post', 10, 2 );

/**
 * Save Plugin Data
 * @since 1.0.0
 */
function fx_updater_plugin_data_meta_box_save_post( $post_id, $post ){

	/* Stripslashes Submitted Data */
	$request = stripslashes_deep( $_POST );

	/* Verify nonce */
	if ( ! isset( $request['fx_updater_plugin_data'] ) || ! wp_verify_nonce( $request['fx_updater_plugin_data'], 'fx_updater_nonce1248' ) ){
		return $post_id;
	}
	/* Do not save on autosave */
	if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	/* Check post type and user caps. */
	$post_type = get_post_type_object( $post->post_type );
	if ( 'plugin_repo' != $post->post_type || !current_user_can( $post_type->cap->edit_post, $post_id ) ){
		return $post_id;
	}

	/* == ZIP FILE == */

	/* Get (old) saved data */
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

	/* Get (old) saved data */
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

	/* === RELEASE DATE === */

	/* Get (old) saved data */
	$old_data = get_post_meta( $post_id, 'last_updated', true );

	/* Get new submitted data and sanitize it. */
	$new_date = array(
		'day'   => isset( $request['last_updated_day'] ) ? $request['last_updated_day'] : date( 'd' ),
		'month' => isset( $request['last_updated_month'] ) ? $request['last_updated_month'] : date( 'm' ),
		'year'  => isset( $request['last_updated_year'] ) ? $request['last_updated_year'] : date( 'Y' ),
	);
	$new_data = fx_updater_format_date( $new_date ); // YYYY-MM-DD

	/* New data submitted, No previous data, create it  */
	if ( $new_data && '' == $old_data ){
		add_post_meta( $post_id, 'last_updated', $new_data, true );
	}
	/* New data submitted, but it's different data than previously stored data, update it */
	elseif( $new_data && ( $new_data != $old_data ) ){
		update_post_meta( $post_id, 'last_updated', $new_data );
	}
	/* New data submitted is empty, but there's old data available, delete it. */
	elseif ( empty( $new_data ) && $old_data ){
		delete_post_meta( $post_id, 'last_updated' );
	}

	/* === WP VERSION === */

	/* WP Version: Required */

	/* Get (old) saved data */
	$old_data = get_post_meta( $post_id, 'requires', true );

	/* Get new submitted data and sanitize it. */
	$new_data = isset( $request['requires'] ) ? fx_updater_sanitize_version( $request['requires'] ) : '';

	/* New data submitted, No previous data, create it  */
	if ( $new_data && '' == $old_data ){
		add_post_meta( $post_id, 'requires', $new_data, true );
	}
	/* New data submitted, but it's different data than previously stored data, update it */
	elseif( $new_data && ( $new_data != $old_data ) ){
		update_post_meta( $post_id, 'requires', $new_data );
	}
	/* New data submitted is empty, but there's old data available, delete it. */
	elseif ( empty( $new_data ) && $old_data ){
		delete_post_meta( $post_id, 'requires' );
	}

	/* WP Version: Tested */

	/* Get (old) saved data */
	$old_data = get_post_meta( $post_id, 'tested', true );

	/* Get new submitted data and sanitize it. */
	$new_data = isset( $request['tested'] ) ? fx_updater_sanitize_version( $request['tested'] ) : '';

	/* New data submitted, No previous data, create it  */
	if ( $new_data && '' == $old_data ){
		add_post_meta( $post_id, 'tested', $new_data, true );
	}
	/* New data submitted, but it's different data than previously stored data, update it */
	elseif( $new_data && ( $new_data != $old_data ) ){
		update_post_meta( $post_id, 'tested', $new_data );
	}
	/* New data submitted is empty, but there's old data available, delete it. */
	elseif ( empty( $new_data ) && $old_data ){
		delete_post_meta( $post_id, 'tested' );
	}

	/* === CHANGELOG === */

	/* Get (old) saved data */
	$old_data = get_post_meta( $post_id, 'section_changelog', true );

	/* Get new submitted data and sanitize it. */
	$new_data = isset( $request['section_changelog'] ) ? fx_updater_sanitize_plugin_section( $request['section_changelog'] ) : '';

	/* New data submitted, No previous data, create it  */
	if ( $new_data && '' == $old_data ){
		add_post_meta( $post_id, 'section_changelog', $new_data, true );
	}
	/* New data submitted, but it's different data than previously stored data, update it */
	elseif( $new_data && ( $new_data != $old_data ) ){
		update_post_meta( $post_id, 'section_changelog', $new_data );
	}
	/* New data submitted is empty, but there's old data available, delete it. */
	elseif ( empty( $new_data ) && $old_data ){
		delete_post_meta( $post_id, 'section_changelog' );
	}


}
















