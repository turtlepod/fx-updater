<?php
/**
 * Manage Columns
 * @since 1.0.0
**/

/* Manage Columns */
add_filter( 'manage_plugin_repo_posts_columns', 'fx_updater_plugin_columns' );
add_action( 'manage_plugin_repo_posts_custom_column', 'fx_updater_plugin_custom_columns', 10, 2 );

/**
 * Columns
 * @since 1.0.0
 */
function fx_updater_plugin_columns( $columns ){
	unset( $columns['title'] );
	unset( $columns['date'] );

	$new_columns = array(
		'cb'           => '<input type="checkbox" />',
		'title'        => _x( 'Plugins', 'plugins', 'fx-updater' ),
		'updater_info' => _x( 'Info', 'plugins', 'fx-updater' ),
	);

	return array_merge( $new_columns, $columns );
}

/**
 * Custom Columns
 * @since 1.0.0
 */
function fx_updater_plugin_custom_columns( $column, $post_id ){
	switch( $column ) {
		case 'updater_info' :
			/* Vars */
			$status = '<span class="up-status-active">' . _x( 'Active', 'plugins', 'fx-updater' ) . '</span>';
			$group = '<span class="group-status-active">' . _x( 'Active', 'plugins', 'fx-updater' ) . '</span>';
			$version = get_post_meta( $post_id, 'version', true );
			if( !$version ){
				$status = '<span class="up-status-inactive">' . _x( 'Not Active', 'plugins', 'fx-updater' ) . '</span>';
				$version = 'N/A';
			}
			$package = get_post_meta( $post_id, 'download_link', true );
			if( !$package ){
				$status = '<span class="up-status-inactive">' . _x( 'Not Active', 'plugins', 'fx-updater' ) . '</span>';
				$package = 'N/A';
			}
			else{
				$package = '<a href="' . esc_url( $package ) . '">' . _x( 'Download ZIP', 'plugins', 'fx-updater' ) . '</a>';
			}
			$slug = get_post_field( 'post_name', get_post( $post_id ) );
			$plugin_id = get_post_meta( $post_id, 'id', true );
			if( !$plugin_id ){
				$plugin_id = 'N/A';
				$status = '<span class="group-status-inactive">' . _x( 'Not Active', 'plugins', 'fx-updater' ) . '</span>';
			}
			$post_status = get_post_status( $post_id );
			if( 'publish' !== $post_status ){
				$status = '<span class="up-status-inactive">' . _x( 'Not Active', 'plugins', 'fx-updater' ) . '</span>';
			}
			?>
			<div class="updater-info">
				<p>
					<span class="dashicons dashicons-update"></span>
					<?php _ex( 'Status', 'plugins', 'fx-updater' ); ?>: <strong><?php echo $status; ?></strong>
				</p>
				<p>
					<span class="dashicons dashicons-index-card"></span>
					<?php _ex( 'Group Update', 'plugins', 'fx-updater' ); ?>: <strong><?php echo $group; ?></strong>
				</p>
				<p>
					<span class="dashicons dashicons-admin-plugins"></span>
					<?php _ex( 'Version', 'plugins', 'fx-updater' ); ?>: <strong><?php echo $version; ?></strong>
				</p>
				<p>
					<span class="dashicons dashicons-media-archive"></span>
					<?php _ex( 'Package', 'plugins', 'fx-updater' ); ?>: <strong><?php echo $package; ?></strong>
				</p>
				<p>
					<span class="dashicons dashicons-edit"></span>
					<?php _ex( 'Slug', 'plugins', 'fx-updater' ); ?>: <strong><?php echo $slug; ?></strong>
				</p>
				<p>
					<span class="dashicons dashicons-media-code"></span>
					<?php _ex( 'Plugin File', 'plugins', 'fx-updater' ); ?>: <strong><?php echo $plugin_id; ?></strong>
				</p>
			</div>
			<?php
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}


/* Disable Month Drop Down */
add_filter( 'disable_months_dropdown', 'fx_updater_plugin_disable_months_dropdown', 10, 2 );

/**
 * Disable Month Drop Down if in Theme Column
 * @since 1.0.0
 */
function fx_updater_plugin_disable_months_dropdown( $months, $post_type ){
	if( 'plugin_repo' == $post_type ){
		return true; 
	}
	return $months;
}


/* Add Repo Group Drop Down in Filter Column */
add_action( 'restrict_manage_posts', 'fx_updater_plugin_add_filter', 10, 2 );

/**
 * Add Filter using group_repo custom taxonomy
 * @since 1.0.0
 */
function fx_updater_plugin_add_filter( $post_type ){
	if( 'plugin_repo' == $post_type ){

			$request = stripslashes_deep( $_GET );
			$selected = '';
			if( isset( $request['taxonomy'], $request['term'] ) && 'group_repo' == $request['taxonomy'] ){
				$selected = $request['term'];
			}

			$dropdown_options = array(
				'show_option_all'  => get_taxonomy( 'group_repo' )->labels->all_items,
				'hide_empty'       => 0,
				'hierarchical'     => 1,
				'show_count'       => 0,
				'orderby'          => 'name',
				'selected'         => $selected,
				'taxonomy'         => 'group_repo',
				'name'             => 'term',
				'value_field'      => 'slug',
			);

			echo '<input type="hidden" name="taxonomy" value="group_repo">';
			wp_dropdown_categories( $dropdown_options );
	}
}

