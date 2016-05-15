<?php
/**
 * SETTINGS PAGE
 * @since 1.0.0
**/
if ( ! defined( 'WPINC' ) ) { die; }


/* Create Settings Page */
add_action( 'admin_menu', 'fx_updater_create_settings_page' );


/**
 * Create Settings Page
 * @since 1.0.0
 */
function fx_updater_create_settings_page(){
	
	/* Add Menu Page */
	add_menu_page(
		_x( 'Updater', 'settings', 'fx-updater' ),          // page title
		_x( 'f(x) Updater', 'settings', 'fx-updater' ),     // menu title
		'manage_fx_updaters',                               // capability
		'fx_updater',                                       // menu slug
		'fx_updater_settings_page',                         // callback function
		'dashicons-update',                                 // dashicon
		100                                                 // position
	);

	/* Add Submenu Page: Settings */
	add_submenu_page(
		'fx_updater',                                       // parent slug
		_x( 'Updater', 'settings', 'fx-updater' ),          // page title
		_x( 'Updater', 'settings', 'fx-updater' ),          // menu title
		'manage_fx_updaters',                               // capability
		'fx_updater'                                        // menu slug
	);
}


/**
 * Menu Page Callback Function
 * @since 1.0.0
 */
function fx_updater_settings_page(){
?>
	<div class="wrap">

		<h1><?php _ex( 'f(x) Updater', 'settings', 'fx-updater' ); ?></h1>

		<?php fx_updater_settings_tab(); ?>
		<?php fx_updater_settings_form(); ?>
		<?php fx_updater_settings_update_code(); ?>

	</div><!-- wrap -->
<?php
}


/**
 * Settings Tab
 * @since 1.0.0
 */
function fx_updater_settings_tab(){

	/* Request */
	$request = stripslashes_deep( $_REQUEST );
	$view = isset( $request['view'] ) ? $request['view'] : '';

	/* Active CSS Class */
	$group_class = empty( $view ) ? "nav-tab nav-tab-active" : "nav-tab";
	$theme_class = ( 'single_theme' == $view ) ? "nav-tab nav-tab-active" : "nav-tab";
	$plugin_class = ( 'single_plugin' == $view ) ? "nav-tab nav-tab-active" : "nav-tab";

	/* Tab Link */
	$url = add_query_arg( 'page', 'fx_updater', admin_url( 'admin.php' ) );
	$theme_url = add_query_arg( 'view', 'single_theme', $url );
	$plugin_url = add_query_arg( 'view', 'single_plugin', $url );
?>
	<h2 class="nav-tab-wrapper wp-clearfix">

		<a class="<?php echo( $group_class ); ?>" href="<?php echo esc_url( $url ); ?>"><span class="dashicons dashicons-index-card"></span> <?php _ex( 'Group Updater', 'settings', 'fx-updater' ); ?></a>

		<a class="<?php echo( $theme_class ); ?>" href="<?php echo esc_url( $theme_url ); ?>"><span class="dashicons dashicons-admin-appearance"></span> <?php _ex( 'Theme Updater', 'settings', 'fx-updater' ); ?></a>

		<a class="<?php echo( $plugin_class ); ?>" href="<?php echo esc_url( $plugin_url ); ?>"><span class="dashicons dashicons-admin-plugins"></span> <?php _ex( 'Plugin Updater', 'settings', 'fx-updater' ); ?></a>

	</h2>
<?php
}


/* Admin Notice */
add_action( 'admin_notices', 'fx_updater_settings_notice' );

/**
 * Error Notice of Invalid Request.
 * @since 1.0.0
 */
function fx_updater_settings_notice(){
	global $hook_suffix;
	if( 'toplevel_page_fx_updater' !== $hook_suffix ){
		return;
	}
	$request = stripslashes_deep( $_REQUEST );
	$code_req = isset( $request['fxup_get_code'] ) ? $request['fxup_get_code'] : '';
	$notice = array();
	if( isset( $code_req['prefix'] ) && empty( $code_req['prefix'] ) ){
		$notice[] = _x( 'code prefix empty', 'settings', 'fx-updater' );
	}
	if( empty( $code_req['domain'] ) ){
		$notice[] = _x( 'text domain empty', 'settings', 'fx-updater' );
	}
	if( empty( $code_req['id'] ) ){
		$notice[] = _x( 'item not selected', 'settings', 'fx-updater' );
	}
	$message = '<strong>' . _x( "Error: ", 'settings', 'fx-updater' ) . '</strong>' . implode( ", ", $notice ) . ". " . _x( 'Please try again.', 'settings', 'fx-updater' );
	if( $code_req && $notice ){
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo $message; ?></p>
		</div>
		<?php
	}
}


/**
 * Form
 * @since 1.0.0
 */
function fx_updater_settings_form(){

	/* Request */
	$request = stripslashes_deep( $_REQUEST );
	$view = isset( $request['view'] ) ? $request['view'] : '';
	$selected_id = isset( $request['fxup_get_code']['id'] ) ? $request['fxup_get_code']['id'] : "";
	$selected_id = fx_updater_settings_sanitize_id( $selected_id );
	$code_req = isset( $request['fxup_get_code'] ) ? $request['fxup_get_code'] : '';
	$default = array( 'prefix' => '', 'domain' => '', 'id' => '', 'type' => '' );
	$code_req = wp_parse_args( $code_req, $default );
	extract( $code_req );
	$prefix = fx_updater_settings_sanitize_prefix( $prefix );
	$domain = fx_updater_settings_sanitize_domain( $domain );
	$id = fx_updater_settings_sanitize_id( $id );
	$type = fx_updater_settings_sanitize_type( $type );

	/* Form Action URL */
	$action_url = add_query_arg( 'page', 'fx_updater', admin_url( 'admin.php' ) );
	$action_url = 'single_theme' == $view ? add_query_arg( 'view', 'single_theme', $action_url ) : $action_url;
	$action_url = 'single_plugin' == $view ? add_query_arg( 'view', 'single_plugin', $action_url ) : $action_url;
	?>
	<form class="fx-updater-settings-form" method="post" action="<?php echo esc_url( $action_url ); ?>">

		<div class="field fxup-code-prefix">
			<input id="fxu_code_prefix" type="text" value="<?php echo $prefix; ?>" name="fxup_get_code[prefix]">
			<label for="fxu_code_prefix"><?php _ex( 'Code Prefix', 'settings', 'fx-updater' );?></label>
		</div><!-- .fxup-code-prefix -->

		<div class="field fxup-text-domain">
			<input id="fxu_text_domain" type="text" value="<?php echo $domain; ?>" name="fxup_get_code[domain]">
			<label for="fxu_text_domain"><?php _ex( 'Text Domain', 'settings', 'fx-updater' );?></label>
		</div><!-- .fxup-text-domain -->

		<div class="field fxup-select-item">

			<?php /* === GROUP UPDATER === */ ?>
			<?php if( empty( $view ) ){ ?>

				<?php
				$args = array(
					'show_option_none'  => _x( '== Select Group ==', 'settings', 'fx-updater' ),
					'option_none_value' => '',
					'name'              => 'fxup_get_code[id]',
					'id'                => 'fxu_select_group',
					'selected'          => $selected_id,
					'taxonomy'          => 'group_repo',
					'value_field'       => 'slug',
				);
				wp_dropdown_categories( $args ); ?>
				<label for="fxu_select_group"><?php _ex( 'Group Repo', 'settings', 'fx-updater' );?></label>
				<input type="hidden" name="fxup_get_code[type]" value="group_repo">

			<?php /* === THEME UPDATER === */ ?>
			<?php } elseif( 'single_theme' == $view ){ ?>

				<?php
				$posts = get_posts( array( 'post_type' => 'theme_repo', 'posts_per_page' => -1 ) );
				if( !empty( $posts ) ){
					?>
					<select class="postform" id="fxu_select_theme" name="fxup_get_code[id]">
						<option value="" <?php selected( $selected_id, '' ); ?>><?php _ex( '== Select Theme ==', 'settings', 'fx-updater' );?></option>
						<?php foreach( $posts as $post ){ ?>
							<option value="<?php echo esc_attr( $post->post_name ); ?>" <?php selected( $selected_id, $post->post_name ); ?>><?php echo $post->post_title; ?></option>
						<?php } ?>
					</select>
					<?php
				}
				?>
				<label for="fxu_select_theme"><?php _ex( 'Theme Repo', 'settings', 'fx-updater' );?></label>
				<input type="hidden" name="fxup_get_code[type]" value="theme_repo">

			<?php /* === PLUGIN UPDATER === */ ?>
			<?php } elseif( 'single_plugin' == $view ){ ?>

				<?php
				$posts = get_posts( array( 'post_type' => 'plugin_repo', 'posts_per_page' => -1 ) );
				if( !empty( $posts ) ){
					?>
					<select class="postform" id="fxu_select_plugin" name="fxup_get_code[id]">
						<option value="" <?php selected( $selected_id, '' ); ?>><?php _ex( '== Select Plugin ==', 'settings', 'fx-updater' );?></option>
						<?php foreach( $posts as $post ){ ?>
							<option value="<?php echo esc_attr( $post->post_name ); ?>" <?php selected( $selected_id, $post->post_name ); ?>><?php echo $post->post_title; ?></option>
						<?php } ?>
					</select>
					<?php
				}
				?>
				<label for="fxu_select_plugin"><?php _ex( 'Plugin Repo', 'settings', 'fx-updater' );?></label>
				<input type="hidden" name="fxup_get_code[type]" value="theme_repo">

			<?php } ?>

		</div><!-- .fxup-select-item -->

		<div class="field fxup-submit">
			<input id="fxu_get_code" type="submit" value="<?php _ex( 'Get Code', 'settings', 'fx-updater' );?>" class="button button-primary">
			<?php wp_nonce_field( "fx_updater_get_code_nonce_7845", "fx_updater_get_code" ); ?>
		</div><!-- .fxup-submit -->

	</form>
	<?php
}


/**
 * Update Code
 * @since 1.0.0
 */
function fx_updater_settings_update_code(){

	/* Check Request */
	$request = stripslashes_deep( $_REQUEST );
	$view = isset( $request['view'] ) ? $request['view'] : '';
	$code_req = isset( $request['fxup_get_code'] ) ? $request['fxup_get_code'] : '';
	$default = array( 'prefix' => '', 'domain' => '', 'id' => '', 'type' => '' );
	$code_req = wp_parse_args( $code_req, $default );
	extract( $code_req );
	$prefix = fx_updater_settings_sanitize_prefix( $prefix );
	$domain = fx_updater_settings_sanitize_domain( $domain );
	$id = fx_updater_settings_sanitize_id( $id );
	$type = fx_updater_settings_sanitize_type( $type );
	if( !$prefix || !$domain || !$id || !$type || ! isset( $request['fx_updater_get_code'] ) || ! wp_verify_nonce( $request['fx_updater_get_code'], 'fx_updater_get_code_nonce_7845' ) ){
		return false;
	}
	/* Template path */
	$path = trailingslashit( FX_UPDATER_PATH . 'includes' );
	$path = trailingslashit( $path . 'code-template' );
	$path_code = $path . $type . '-code' . '.txt';
	$path_class = $path . $type . '-class' . '.txt';
	?>

	<?php if( file_exists( $path_code ) ){
		$text = file_get_contents( $path_code, true);
		$text = fx_updater_settings_replace_code( $text, $prefix, $domain, $id );
		?>
		<div class="fxup-box">
			<div class="title">
				<?php _ex( 'PHP Code', 'settings', 'fx-updater' ); ?>
			</div><!-- .title -->
			<div class="inner">
				<?php if( empty( $view ) ){ ?>
					<p><?php _ex( 'Add this code in your main plugin file.', 'settings', 'fx-updater' ); ?></p>
				<?php } elseif( 'single_theme' == $view ){ ?>
					<p><?php _ex( 'Add this code in your theme functions.php.', 'settings', 'fx-updater' ); ?></p>
				<?php } elseif( 'single_plugin' == $view ){ ?>
					<p><?php _ex( 'Add this code in you main plugin file.', 'settings', 'fx-updater' ); ?></p>
				<?php } ?>
				<textarea class="pre" readonly="readonly"><?php echo esc_textarea( $text ); ?></textarea>
			</div><!-- .inner -->
		</div><!-- .fxup-box -->
	<?php } ?>

	<?php if( file_exists( $path_class ) ){
		$text = file_get_contents( $path_class, true);
		$text = fx_updater_settings_replace_code( $text, $prefix, $domain, $id );
		?>
		<div class="fxup-box">
			<div class="title">
				<?php _ex( 'Updater Class', 'settings', 'fx-updater' ); ?>
			</div><!-- .title -->
			<div class="inner">
				<?php if( empty( $view ) ){ ?>
					<p><?php _ex( 'Create "includes/updater.php" in your plugin and add this code.', 'settings', 'fx-updater' ); ?></p>
				<?php } elseif( 'single_theme' == $view ){ ?>
					<p><?php _ex( 'Create "includes/updater.php" in your theme and add this code.', 'settings', 'fx-updater' ); ?></p>
				<?php } elseif( 'single_plugin' == $view ){ ?>
					<p><?php _ex( 'Create "includes/updater.php" in your plugin and add this code.', 'settings', 'fx-updater' ); ?></p>
				<?php } ?>
				<textarea class="pre" readonly="readonly"><?php echo esc_textarea( $text ); ?></textarea>
			</div><!-- .inner -->
		</div><!-- .fxup-box -->
	<?php } ?>
<?php
}

/**
 * Replace String in Code
 * @since 1.0.0
 */
function fx_updater_settings_replace_code( $text, $prefix, $domain, $id ){

	/* Repo URI */
	$text = str_replace( 'http://dev.play/', esc_url( set_url_scheme( trailingslashit( home_url() ), 'http' ) ), $text );

	/* Prefix */
	$text = str_replace( 'prefix', esc_attr( $prefix ), $text );

	/* Text Domain */
	$text = str_replace( 'domain', esc_attr( $domain ), $text );

	/* ID */
	$text = str_replace( 'repo-slug', sanitize_title( $id ), $text );

	return $text;
}


/**
 * Sanitize Prefix
 * @since 1.0.0
 */
function fx_updater_settings_sanitize_prefix( $input ){
	$input = trim( $input );
	$input = str_replace( ' ', '_', $input );
	$input = str_replace( '-', '_', $input );
	$input = sanitize_text_field( esc_attr( $input ) );
	return $input;
}

/**
 * Sanitize Text Domain
 * @since 1.0.0
 */
function fx_updater_settings_sanitize_domain( $input ){
	$input = trim( $input );
	$input = str_replace( ' ', '-', $input );
	$input = sanitize_text_field( esc_attr( $input ) );
	return $input;
}

/**
 * Sanitize Item ID
 * @since 1.0.0
 */
function fx_updater_settings_sanitize_id( $input ){
	$input = sanitize_title( $input );
	$input = sanitize_text_field( $input );
	return $input;
}
/**
 * Sanitize Type
 * @since 1.0.0
 */
function fx_updater_settings_sanitize_type( $input ){
	$valid = array( 'group_repo', 'theme_repo', 'plugin_repo' );
	if( in_array( $input, $valid ) ){
		return $input;
	}
	return '';
}


/* Admin Scripts */
add_action( 'admin_enqueue_scripts', 'fx_updater_settings_scripts' );


/**
 * Load scripts
 * @since 1.0.0
 */
function fx_updater_settings_scripts( $hook_suffix ){

	/* Only in settings page */
	if( 'toplevel_page_fx_updater' == $hook_suffix ){

		/* CSS */
		wp_enqueue_style( 'fx-updater-settings', FX_UPDATER_URI . 'assets/admin-settings.css', array(), FX_UPDATER_VERSION );

		/* JS */
		wp_enqueue_script( 'fx-updater-settings', FX_UPDATER_URI. 'assets/admin-settings.js', array( 'jquery' ), FX_UPDATER_VERSION );
	}
}