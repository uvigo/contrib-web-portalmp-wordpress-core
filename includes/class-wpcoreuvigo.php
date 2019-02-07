<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       info@ideit.es
 * @since      1.0.0
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/includes
 * @author     IdeiT <info@ideit.es>
 */
class Wpcoreuvigo {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wpcoreuvigo_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WPCOREUVIGO_VERSION' ) ) {
			$this->version = WPCOREUVIGO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wpcoreuvigo';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Undocumented function
	 *
	 * @return boolean
	 */
	public static function is_active_uvigo_feedsreader() {
		return class_exists( 'Uvigo_Feedsreader' );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wpcoreuvigo_Loader. Orchestrates the hooks of the plugin.
	 * - Wpcoreuvigo_i18n. Defines internationalization functionality.
	 * - Wpcoreuvigo_Admin. Defines all hooks for the admin area.
	 * - Wpcoreuvigo_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcoreuvigo-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcoreuvigo-i18n.php';

		/**
		 * Widgets
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcoreuvigo-widgets.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpcoreuvigo-admin.php';

		/**
		 * The class responsible tinymce
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpcoreuvigo-tinymce.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpcoreuvigo-public.php';

		/**
		 * The class responsible for defining shortcodes of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpcoreuvigo-public-shortcodes.php';

		$this->loader = new Wpcoreuvigo_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wpcoreuvigo_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Wpcoreuvigo_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wpcoreuvigo_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_admin, 'register_spectator_taxonomy', 10 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_universe_taxonomy', 10 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_geographic_taxonomy', 10 );

		$this->loader->add_action( 'init', $plugin_admin, 'register_document_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_document_type_taxonomy', 10 );

		$this->loader->add_action( 'wp_loaded', $plugin_admin, 'register_taxonomies_terms', 10 );

		$this->loader->add_filter( 'custom_menu_order', $plugin_admin, 'custom_menu_order', 10 );
		$this->loader->add_filter( 'menu_order', $plugin_admin, 'menu_order', 10 );

		// Featured Video
		$this->loader->add_filter( 'admin_post_thumbnail_html', $plugin_admin, 'add_featured_video_url', 10, 2 );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_featured_video_url', 10, 3 );

		// New fields in menus
		$this->loader->add_filter( 'wp_edit_nav_menu_walker', $plugin_admin, 'wp_edit_nav_menu_walker', 99 );
		$this->loader->add_action( 'wp_nav_menu_item_custom_fields', $plugin_admin, 'wp_nav_menu_item_custom_fields', 10, 4 );
		$this->loader->add_action( 'wp_update_nav_menu_item', $plugin_admin, 'wp_update_nav_menu_item', 10, 3 );
		$this->loader->add_filter( 'manage_nav-menus_columns', $plugin_admin, 'manage_nav_menus_columns', 99 );

		// Add page field to redirect to first child
		$this->loader->add_action( 'page_attributes_misc_attributes', $plugin_admin, 'add_page_attributes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_page_attributes' );

		$plugin_tinymce = new Wpcoreuvigo_Tinymce( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_tinymce, 'tinymce_init', 10 );

		// Widgets
		$this->loader->add_action( 'widgets_init', $plugin_admin, 'widgets_init' );
		$this->loader->add_filter( 'query_vars', 'Wpcoreuvigo_Filter_Widget', 'query_vars_filter' );
		$this->loader->add_action( 'pre_get_posts', 'Wpcoreuvigo_Filter_Widget', 'pre_get_posts', 100 );
		add_shortcode( 'wpcoreuvigo_actualfilter', array( 'Wpcoreuvigo_Filter_Widget', 'actualfilter_shortcode' ) );

		// Check plugin update
		$this->loader->add_filter( 'pre_set_site_transient_update_plugins', $plugin_admin, 'check_for_plugin_update' );
		// Retrive plugin information
		$this->loader->add_filter( 'plugins_api', $plugin_admin, 'plugin_api_call', 10, 3 );

		/*
		// TEMP: Enable update check on every request. Normally you don't need this! This is for testing only!
		// NOTE: The
		//	if (empty($checked_data->checked))
		//		return $checked_data;
		// lines will need to be commented in the check_for_plugin_update function as well.
		*/
		// set_site_transient( 'update_plugins', null );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wpcoreuvigo_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Redirect page to first child
		$this->loader->add_action( 'template_redirect', $plugin_public, 'page_to_first_child_template_redirect' );

		// Change link menu item to redirect to first child
		$this->loader->add_filter( 'nav_menu_link_attributes', $plugin_public, 'nav_menu_link_attributes', 10, 4 );

		$plugin_shortcodes = new Wpcoreuvigo_Public_Shortcodes( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_shortcodes, 'register_shortcodes' );

		// if ( self::is_active_uvigo_feedsreader() ) {
		// Feeds readers shortcodes
		// }
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wpcoreuvigo_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
