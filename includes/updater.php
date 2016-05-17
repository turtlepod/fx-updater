<?php
/* Load Class. */
$fxupdater_updater = new fxUpdater_Updater();

/**
 * Updater Class
**/
class fxUpdater_Updater{

	/**
	 * Class Constructor
	 */
	public function __construct() {

		/* Updater Config */
		$this->config = array(
			'server'  => 'http://genbumedia.com/',
			'type'    => 'plugin',
			'id'      => 'fx-updater/fx-updater.php',
			'api'     => '1.0.0',
			'post'    => array(),
		);

		/* Admin Init */
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		/* Fix Install Folder */
		add_filter( 'upgrader_post_install', array( $this, 'fix_install_folder' ), 11, 3 );
	}

	/**
	 * Admin Init.
	 * Some functions only available in admin.
	 */
	public function admin_init(){

		/* Add theme update data */
		if( 'plugin' !== $this->config['type'] ){
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'add_theme_update_data' ), 10, 2 );
		}

		/* Add plugin update data */
		if( 'theme' !== $this->config['type'] ){
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'add_plugin_update_data' ), 10, 2 );
		}

		/* Plugin Information */
		if( 'theme' !== $this->config['type'] ){
			add_filter( 'plugins_api_result', array( $this, 'plugin_info' ), 10, 3 );
		}
	}

	/**
	 * Add theme update data if available
	 */
	public function add_theme_update_data( $value, $transient ){
		if( isset( $value->response ) ){
			$update_data = $this->get_data( 'query_themes' );
			foreach( $update_data as $theme => $data ){
				if( isset( $data['new_version'], $data['theme'], $data['url'] ) ){
					$value->response[$theme] = (array)$data;
				}
				else{
					unset( $value->response[$theme] );
				}
			}
		}
		return $value;
	}

	/**
	 * Add plugin update data if available
	 */
	public function add_plugin_update_data( $value, $transient ){
		if( isset( $value->response ) ){
			$update_data = $this->get_data( 'query_plugins' );
			foreach( $update_data as $plugin => $data ){
				if( isset( $data['new_version'], $data['slug'], $data['plugin'] ) ){
					$value->response[$plugin] = (object)$data;
				}
				else{
					unset( $value->response[$plugin] );
				}
			}
		}
		return $value;
	}

	/**
	 * Plugin Information
	 */
	public function plugin_info( $res, $action, $args ){

		/* Get list plugin */
		if( 'group' == $this->config['type'] ){
			$list_plugins = $this->get_data( 'list_plugins' );
		}
		else{
			$slug = dirname( $this->config['id'] );
			$list_plugins = array(
				$slug => $this->config['id'],
			);
		}

		/* If in our list, add our data. */
		if( 'plugin_information' == $action && isset( $args->slug ) && array_key_exists( $args->slug, $list_plugins ) ){

			$info = $this->get_data( 'plugin_information', $list_plugins[$args->slug] );

			if( isset( $info['name'], $info['slug'], $info['external'], $info['sections'] ) ){
				$res = (object)$info;
			}
		}
		return $res;
	}

	/**
	 * Get update data from server
	 */
	public function get_data( $action, $plugin = '' ){

		/* Get WP Version */
		global $wp_version;

		/* Remote Options */
		$body = $this->config['post'];
		if( 'query_plugins' == $action ){
			$body['plugins'] = get_plugins();
		}
		elseif( 'query_themes' == $action ){
			$themes = array();
			$get_themes = wp_get_themes();
			foreach( $get_themes as $theme ){
				$stylesheet = $theme->get_stylesheet();
				$themes[$stylesheet] = array(
					'Name' => $theme->get( 'Name' ),
					'ThemeURI' => $theme->get( 'ThemeURI' ),
					'Description' => $theme->get( 'Description' ),
					'Author' => $theme->get( 'Author' ),
					'AuthorURI' => $theme->get( 'AuthorURI' ),
					'Version' => $theme->get( 'Version' ),
					'Template' => $theme->get( 'Template' ),
					'Status' => $theme->get( 'Status' ),
					'Tags' => $theme->get( 'Tags' ),
					'TextDomain' => $theme->get( 'TextDomain' ),
					'DomainPath' => $theme->get( 'DomainPath' ),
				);
			}
			$body['themes'] = $themes;
		}
		elseif( 'plugin_information' == $action ){
			$body['plugin'] =  $plugin;
		}
		$options = array(
			'timeout'    => 20,
			'body'       => $body,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
		); 

		/* Remote URL */
		$url_args = array(
			'fx_updater'          => $action,
			$this->config['type'] => $this->config['id'],
		);
		$server = set_url_scheme( $this->config['server'], 'http' );
		$url = $http_url = add_query_arg( $url_args, $server );
		if ( $ssl = wp_http_supports( array( 'ssl' ) ) ){
			$url = set_url_scheme( $url, 'https' );
		}

		/* Try HTTPS */
		$raw_response = wp_remote_post( esc_url_raw( $url ), $options );

		/* Fail, try HTTP */
		if ( is_wp_error( $raw_response ) ) {
			$raw_response = wp_remote_post( esc_url_raw( $http_url ), $options );
		}

		/* Still fail, bail. */
		if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
			return array();
		}

		/* return array */
		$data = json_decode( trim( wp_remote_retrieve_body( $raw_response ) ), true );
		return is_array( $data ) ? $data : array();
	}

	/**
	 * Fix Install Folder
	 */
	public function fix_install_folder( $true, $hook_extra, $result ){
		if ( isset( $hook_extra['plugin'] ) ){
			global $wp_filesystem;
			$proper_destination = trailingslashit( $result['local_destination'] ) . dirname( $hook_extra['plugin'] );
			$wp_filesystem->move( $result['destination'], $proper_destination );
			$result['destination'] = $proper_destination;
			$result['destination_name'] = dirname( $hook_extra['plugin'] );
			global $hook_suffix;
			if( 'update.php' == $hook_suffix && isset( $_GET['action'], $_GET['plugin'] ) && 'upgrade-plugin' == $_GET['action'] && $hook_extra['plugin'] == $_GET['plugin'] ){
				activate_plugin( $hook_extra['plugin'] );
			}
		}
		elseif( isset( $hook_extra['theme'] ) ){
			global $wp_filesystem;
			$proper_destination = trailingslashit( $result['local_destination'] ) . $hook_extra['theme'];
			$wp_filesystem->move( $result['destination'], $proper_destination );
			if( get_option( 'theme_switched' ) == $hook_extra['theme'] && $result['destination_name'] == get_stylesheet() ){
				wp_clean_themes_cache();
				switch_theme( $hook_extra['theme'] );
			}
			$result['destination'] = $proper_destination;
			$result['destination_name'] = $hook_extra['theme'];
		}
		return $true;
	}

}
