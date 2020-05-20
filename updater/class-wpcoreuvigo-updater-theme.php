<?php
/**
 * This class is responsible for updating themes
 */

class WpcoreuvigoTheme_Updater extends WpcoreuvigoUpdater {

	/**
	 * Contains the information regarding the theme
	 * @access protected
	 */
	protected $theme;

	/**
	 * Initializes the theme updater
	 *
	 * @param array $params The configuration parameters.
	 */
	protected function initialize() {

		$this->theme    = wp_get_theme( basename( get_template_directory() ) );
		$this->slug     = sanitize_title( $this->theme->stylesheet );
		$this->version  = $this->theme->version;

		add_filter( 'site_transient_update_themes', array( $this, 'checkUpdate' ) );

	}

}
