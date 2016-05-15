<?php
/**
 * Manage Columns
 * @since 1.0.0
**/

/* Add custom column */
add_filter( 'manage_edit-group_repo_columns', 'fx_updater_group_columns' );
add_filter( 'manage_group_repo_custom_column', 'fx_updater_group_custom_columns', 10, 3 );


/**
 * Columns
 * @since 1.0.0
 */
function fx_updater_group_columns( $columns ){
	unset( $columns['posts'] );
	$columns['items'] = _x( 'Items', 'group', 'fx-updater' );
	return $columns;
}

/**
 * Custom Columns
 * @since 1.0.0
 */
function fx_updater_group_custom_columns( $value, $column, $term_id ){
	switch( $column ) {
		case 'items' :
			$term = get_term( $term_id, 'group_repo');
			$url = add_query_arg( array( 'taxonomy' => 'group_repo', 'term' => $term->slug ), admin_url( 'edit.php' ) );
			$theme_url = add_query_arg( 'post_type', 'theme_repo', $url );
			$plugin_url = add_query_arg( 'post_type', 'plugin_repo', $url );
			?>
			<div class="updater-info">
				<p>
					<span class="dashicons dashicons-update"></span>
					<?php echo sprintf( _x( '%d Items', 'group', 'fx-updater' ), $term->count ); ?>
				</p>
				<p>
					<span class="dashicons dashicons-admin-appearance"></span>
					<a href="<?php echo esc_url( $theme_url ); ?>"><?php _ex( 'View Themes', 'group', 'fx-updater' ); ?></a>
				</p>
				<p>
					<span class="dashicons dashicons-admin-plugins"></span>
					<a href="<?php echo esc_url( $plugin_url ); ?>"><?php _ex( 'View Plugin', 'group', 'fx-updater' ); ?></a>
				</p>
			</div>
			<?php
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}
