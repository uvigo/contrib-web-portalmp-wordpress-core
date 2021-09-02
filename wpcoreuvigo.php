<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              info@ideit.es
 * @since             1.0.0
 * @package           Wpcoreuvigo
 *
 * @wordpress-plugin
 * Plugin Name:       UVigo WordPress Core
 * Plugin URI:        https://github.com/uvigo/contrib-web-portalmp-wordpress-core
 * Description:       Common funcionalities to use with other Plugins and Themes from Universidade de Vigo.
 * Version:           1.4.1
 * Author:            IdeiT
 * Author URI:        https://ideit.es
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpcoreuvigo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPCOREUVIGO_VERSION', '1.4.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpcoreuvigo-activator.php
 */
function activate_wpcoreuvigo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcoreuvigo-activator.php';
	Wpcoreuvigo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpcoreuvigo-deactivator.php
 */
function deactivate_wpcoreuvigo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcoreuvigo-deactivator.php';
	Wpcoreuvigo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpcoreuvigo' );
register_deactivation_hook( __FILE__, 'deactivate_wpcoreuvigo' );

/**
 * A clase utilizada para recuperar os arquivos de assets
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpcoreuvigo-json-manifest.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpcoreuvigo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpcoreuvigo() {
	$plugin = new Wpcoreuvigo();
	$plugin->run();
}
run_wpcoreuvigo();


/**
 * Recupera o arquivo de asset da carpeta public
 *
 * @param [type] $asset
 * @param string $env
 * @return void
 */
function wpcoreuvigo_public_asset_path( $asset ) {
	static $manifest_public;

	if ( empty( $manifest_public ) ) {
		$manifest_path   = plugin_dir_path( __FILE__ ) . 'public/dist/assets.json';
		$manifest_uri    = plugin_dir_url( __FILE__ ) . 'public/dist';
		$manifest_public = new WpcoreuvigoJsonManifest( $manifest_path, $manifest_uri );
	}

	return $manifest_public->getUri( $asset );
}

/**
 * Recupera o arquivo de asset da carpeta admin
 *
 * @param [type] $asset
 * @param string $env
 * @return void
 */
function wpcoreuvigo_admin_asset_path( $asset ) {
	static $manifest_admin;

	if ( empty( $manifest_admin ) ) {
		$manifest_path  = plugin_dir_path( __FILE__ ) . 'admin/dist/assets.json';
		$manifest_uri   = plugin_dir_url( __FILE__ ) . 'admin/dist';
		$manifest_admin = new WpcoreuvigoJsonManifest( $manifest_path, $manifest_uri );
	}

	return $manifest_admin->getUri( $asset );
}
