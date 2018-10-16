<?php
/**
 * Define the code related with tinymce
 *
 * @link       info@ideit.es
 * @since      1.0.0
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/admin
 */

/**
 *  Define the code related with tinymce
 *
 * @since      1.0.0
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/admin
 * @author     IdeiT <info@ideit.es>
 */
class Wpcoreuvigo_Tinymce {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Init process for registering our button
	 *
	 * @return [type] [description]
	 */
	public function tinymce_init() {

		// Abort early if the user will never see TinyMCE
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) && get_user_option( 'rich_editing' ) == 'true' ) {
			return;
		}

		// Add a callback to register our TinyMCE plugin
		add_filter( 'mce_external_plugins', array( $this, 'tinymce_register_plugin' ) );

		// Add a callback to add our button to the TinyMCE toolbar
		add_filter( 'mce_buttons_2', array( $this, 'tinymce_add_button' ) );
	}

	/**
	 * This callback registers our plug-in
	 *
	 * @param  [type] $plugin_array [description]
	 * @return [type]               [description]
	 */
	public function tinymce_register_plugin( $plugin_array ) {

		$plugin_array['content_floor_button']       = wpcoreuvigo_admin_asset_path( 'scripts/tinymce-floor.js' );
		$plugin_array['content_floor_image_button'] = wpcoreuvigo_admin_asset_path( 'scripts/tinymce-floor-image.js' );
		$plugin_array['content_block_button']       = wpcoreuvigo_admin_asset_path( 'scripts/tinymce-block.js' );

		return $plugin_array;
	}

	/**
	 * This callback adds our button to the toolbar
	 *
	 * @param  [type] $buttons [description]
	 * @return [type]          [description]
	 */
	public function tinymce_add_button( $buttons ) {

		// Add the button ID to the $button array
		$buttons[] = 'content_floor_button';
		$buttons[] = 'content_floor_image_button';
		$buttons[] = 'content_block_button';

		return $buttons;
	}

}
