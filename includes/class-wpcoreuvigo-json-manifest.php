<?php
/**
 * The assets-specific functionality of the plugin.
 *
 * @link       info@ideit.es
 * @since      1.0.0
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/includes
 */

/**
 * The assets-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.1.5
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/includes
 * @author     IdeiT <info@ideit.es>
 */

class WpcoreuvigoJsonManifest {
	/** @var array */
	public $manifest;

	/** @var string */
	public $dist;

	/**
	 * JsonManifest constructor
	 *
	 * @param string $manifestPath Local filesystem path to JSON-encoded manifest
	 * @param string $distUri Remote URI to assets root
	 */
	public function __construct( $manifestPath, $distUri ) {
		$this->manifest = file_exists( $manifestPath ) ? json_decode( file_get_contents( $manifestPath ), true ) : [];
		$this->dist     = $distUri;
	}

	public function get( $asset ) {
		return isset( $this->manifest[ $asset ] ) ? $this->manifest[ $asset ] : $asset;
	}

	public function getUri( $asset ) {
		return "{$this->dist}/{$this->get($asset)}";
	}
}





// /**
// * Get paths for assets
// */
// class WpcoreuvigoJsonManifest {
// private $manifest;
// public function __construct( $manifest_path ) {
// if ( file_exists( $manifest_path ) ) {
// $this->manifest = json_decode( file_get_contents( $manifest_path ), true );
// } else {
// $this->manifest = [];
// }
// }
// public function get() {
// return $this->manifest;
// }
// public function getPath( $key = '', $default = null ) {
// $collection = $this->manifest;
// if ( is_null( $key ) ) {
// return $collection;
// }
// if ( isset( $collection[ $key ] ) ) {
// return $collection[ $key ];
// }
// foreach ( explode( '.', $key ) as $segment ) {
// if ( ! isset( $collection[ $segment ] ) ) {
// return $default;
// } else {
// $collection = $collection[ $segment ];
// }
// }
// return $collection;
// }
// }
