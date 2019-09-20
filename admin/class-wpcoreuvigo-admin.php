<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       info@ideit.es
 * @since      1.0.0
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/admin
 * @author     IdeiT <info@ideit.es>
 */
class Wpcoreuvigo_Admin {

	const TAXONOMY_SPECTATOR_NAME  = 'spectator';
	const TAXONOMY_UNIVERSE_NAME   = 'universe';
	const TAXONOMY_GEOGRAPHIC_NAME = 'geographic';

	const UPDATEAPI_URL = 'https://ideit.software/wpapi/packages/';

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
	 * Used to set new fields in menu.
	 *
	 * @var array $menu_fields store fields
	 */
	private $menu_fields = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		/*
		 * Code example to create custom fields in menus
		 * 	'field-01' => array(
		 * 		'type'  => 'text', or 'checkbox'
		 * 		'label' => __( 'Custom Field', 'wpcoreuvigo' ),
		 * 		'value' => '',
		 * 	)
		 */
		$this->menu_fields = array(
			'openchild' => array(
				'type'  => 'checkbox',
				'label' => __( 'Link to first child item', 'wpcoreuvigo' ),
				'value' => 'parent',
			),
		);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpcoreuvigo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpcoreuvigo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name . '-admin', wpcoreuvigo_admin_asset_path( 'styles/main.css' ), false, null );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpcoreuvigo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpcoreuvigo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Non cargamos este script agora porque só ten os plugins de TinyMCE e estes cargan con aqruivos JS independientes.
		wp_enqueue_script( $this->plugin_name . '-admin', wpcoreuvigo_admin_asset_path( 'scripts/main.js' ), array( 'jquery' ), null, true );

	}

	/**
	 * Add field to Page Attributes for store page sidebar used
	 *
	 * @param [type] $post
	 * @return void
	 */
	public function add_page_attributes( $post ) {
		if ( 'page' === $post->post_type ) {
			$uvigo_page_redirect_child = get_post_meta( $post->ID, 'uvigo_page_redirect_child', true );
			if ( empty( $uvigo_page_redirect_child ) ) {
				$uvigo_page_redirect_child = 'none';
			}
			?>
			<p class="post-attributes-label-wrapper">
				<label class="post-attributes-label"><?php esc_html_e( 'Behaviour', 'wpcoreuvigo' ); ?></label>
				<br>
				<label for="uvigo_page_redirect_child">
					<input type="checkbox" name="uvigo_page_redirect_child" id="uvigo_page_redirect_child"<?php checked( $uvigo_page_redirect_child, 'redirect_child' ); ?> value="redirect_child"> <?php esc_html_e( 'Redirect to first child', 'wpcoreuvigo' ); ?>
				</label>
			</p>
			<?php
		}
	}

	/**
	 * Save attributes in Pages for redirect to first child
	 *
	 * @param [type] $post_id
	 * @return void
	 */
	public function save_page_attributes( $post_id ) {

		if ( 'page' != get_post_type( $post_id ) ) {
			return;
		}

		if ( array_key_exists( 'uvigo_page_redirect_child', $_POST ) ) {
			update_post_meta( $post_id, 'uvigo_page_redirect_child', $_POST['uvigo_page_redirect_child'] );
		} else {
			delete_post_meta( $post_id, 'uvigo_page_redirect_child' );
		}
	}

	/**
	 * Walker for edit menu
	 *
	 * @param [type] $walker
	 * @return void
	 */
	public function wp_edit_nav_menu_walker( $walker ) {
		$walker = 'Menu_Item_Custom_Fields_Walker';
		if ( ! class_exists( $walker ) ) {
			require_once dirname( __FILE__ ) . '/class-wpcoreuvigo-menu-edit-walker.php';
		}

		return $walker;
	}

	/**
	 * Print field
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public function wp_nav_menu_item_custom_fields( $id, $item, $depth, $args ) {
		foreach ( $this->menu_fields as $_key => $field ) {
			$label = $field['label'];
			$key   = sprintf( 'menu-item-%s', $_key );
			$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
			$name  = sprintf( '%s[%s]', $key, $item->ID );
			$value = get_post_meta( $item->ID, $key, true );
			$class = sprintf( 'field-%s', $_key );
			?>
				<p class="description description-wide <?php echo esc_attr( $class ); ?>">
				<?php
				if ( 'checkbox' === $field['type'] ) {
					$val = $field['value'];
					printf(
						'<label for="%1$s"><input type="checkbox" id="%1$s" class="%1$s" name="%3$s" value="%4$s" %5$s /> %2$s</label>',
						esc_attr( $id ),
						esc_html( $label ),
						esc_attr( $name ),
						esc_attr( $val ),
						checked( $value, $val, false )
					);
				} else {
					printf(
						'<label for="%1$s">%2$s<br /><input type="text" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" /></label>',
						esc_attr( $id ),
						esc_html( $label ),
						esc_attr( $name ),
						esc_attr( $value )
					);
				}
				?>
				</p>
			<?php
		}
	}

	/**
	 * Save custom field value
	 *
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public function wp_update_nav_menu_item( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );
		foreach ( $this->menu_fields as $_key => $field ) {
			$key = sprintf( 'menu-item-%s', $_key );
			// Sanitize
			if ( ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
				$value = sanitize_text_field( $_POST[ $key ][ $menu_item_db_id ] );
			} else {
				$value = null;
			}
			// Update
			if ( ! is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $key, $value );
			} else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}

	/**
	 * Add our fields to the screen options toggle
	 *
	 * @param array $columns Menu item columns
	 * @return array
	 */
	public function manage_nav_menus_columns( $columns ) {
		$new_columns = array();
		foreach ( $this->menu_fields as $_key => $field ) {
			$new_columns[ $_key ] = $field['label'];
		}
		$columns = array_merge( $columns, $new_columns );

		return $columns;
	}

	/**
	 * Check for plugin update info from API Url
	 *
	 * @param [type] $checked_data
	 * @return void
	 */
	public function check_for_plugin_update( $checked_data ) {
		global $wp_version;

		$plugin_slug = $this->plugin_name;

		// Comment out these two lines during testing.
		if ( empty( $checked_data->checked ) ) {
			return $checked_data;
		}

		$key_plugin = $plugin_slug . '/' . $plugin_slug . '.php';

		$args = array(
			'slug'    => $plugin_slug,
			'version' => array_key_exists( $key_plugin, $checked_data->checked ) ? $checked_data->checked[ $key_plugin ] : $this->version,
		);

		$request_string = array(
			'body'       => array(
				'action'  => 'basic_check',
				'request' => serialize( $args ),
				'api-key' => md5( get_bloginfo( 'url' ) ),
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
		);

		// Start checking for an update
		$raw_response = wp_remote_post( self::UPDATEAPI_URL, $request_string );

		if ( ! is_wp_error( $raw_response ) && ( 200 == $raw_response['response']['code'] ) ) {
			$response = unserialize( $raw_response['body'] );
		}

		if ( is_object( $response ) && ! empty( $response ) ) { // Feed the update data into WP updater
			$checked_data->response[ $plugin_slug . '/' . $plugin_slug . '.php' ] = $response;
		}

		return $checked_data;
	}

	/**
	 * Get plugin_information from API Url
	 *
	 * @param [type] $result
	 * @param [type] $action
	 * @param [type] $args
	 * @return void
	 */
	public function plugin_api_call( $result, $action, $args ) {
		global $wp_version;

		$plugin_slug = $this->plugin_name;

		if ( ! isset( $args->slug ) || ( $args->slug !== $plugin_slug ) ) {
			return false;
		}

		// Get the current version
		$plugin_info     = get_site_transient( 'update_plugins' );
		$current_version = $plugin_info->checked[ $plugin_slug . '/' . $plugin_slug . '.php' ];
		$args->version   = $current_version;

		$request_string = array(
			'body'       => array(
				'action'  => $action,
				'request' => serialize( $args ),
				'api-key' => md5( get_bloginfo( 'url' ) ),
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
		);

		$request = wp_remote_post( self::UPDATEAPI_URL, $request_string );

		if ( is_wp_error( $request ) ) {
			$res = new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>' ), $request->get_error_message() );
		} else {
			$res = unserialize( $request['body'] );

			if ( false === $res ) {
				$res = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred' ), $request['body'] );
			}
		}

		return $res;
	}


	/**
	 * Register spectator taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_spectator_taxonomy() {

		$use_spectator_taxonomy = true;
		$use_spectator_taxonomy = apply_filters( 'wpcoreuvigo_use_spectator_taxonomy_post_types', $use_spectator_taxonomy );

		if ( ! $use_spectator_taxonomy ) {
			return false;
		}

		if ( ! taxonomy_exists( self::TAXONOMY_SPECTATOR_NAME ) ) {
			$labels = array(
				'name'              => _x( 'Spectators', 'taxonomy general name', 'wpcoreuvigo' ),
				'singular_name'     => _x( 'Spectator', 'taxonomy singular name', 'wpcoreuvigo' ),
				'search_items'      => __( 'Search Spectator', 'wpcoreuvigo' ),
				'all_items'         => __( 'All Spectators', 'wpcoreuvigo' ),
				'parent_item'       => __( 'Parent Spectator', 'wpcoreuvigo' ),
				'parent_item_colon' => __( 'Parent Spectator:', 'wpcoreuvigo' ),
				'edit_item'         => __( 'Edit Spectator', 'wpcoreuvigo' ),
				'update_item'       => __( 'Update Spectator', 'wpcoreuvigo' ),
				'add_new_item'      => __( 'Add New Spectator', 'wpcoreuvigo' ),
				'new_item_name'     => __( 'New Spectator Name', 'wpcoreuvigo' ),
				'menu_name'         => __( 'Spectator', 'wpcoreuvigo' ),
			);

			$args = array(
				'hierarchical'       => true,
				'labels'             => $labels,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_admin_column'  => false,
				'show_in_quick_edit' => false,
				'query_var'          => 'taxonomy-spectator',
				'rewrite'            => array( 'slug' => 'publico' ),
			);

			$ob = register_taxonomy(
				self::TAXONOMY_SPECTATOR_NAME,
				array(),
				$args
			);
		}

		// Register post-types
		$spectators_cpts = array(
			'post',
		);
		$spectators_cpts = apply_filters( 'wpcoreuvigo_register_spectator_taxonomy_post_types', $spectators_cpts );

		foreach ( $spectators_cpts as $cpt ) {
			register_taxonomy_for_object_type( self::TAXONOMY_SPECTATOR_NAME, $cpt );
		}
	}

	/**
	 * Register universe taxonomy
	 *
	 * @since    1.0.0
	 */
	public function register_universe_taxonomy() {

		$use_universe_taxonomy = true;
		$use_universe_taxonomy = apply_filters( 'wpcoreuvigo_use_universe_taxonomy_post_types', $use_universe_taxonomy );

		if ( ! $use_universe_taxonomy ) {
			return false;
		}

		if ( ! taxonomy_exists( self::TAXONOMY_UNIVERSE_NAME ) ) {
			$labels = array(
				'name'              => _x( 'Universes', 'taxonomy general name', 'wpcoreuvigo' ),
				'singular_name'     => _x( 'Universe', 'taxonomy singular name', 'wpcoreuvigo' ),
				'search_items'      => __( 'Search Universe', 'wpcoreuvigo' ),
				'all_items'         => __( 'All Universe', 'wpcoreuvigo' ),
				'parent_item'       => __( 'Parent Universe', 'wpcoreuvigo' ),
				'parent_item_colon' => __( 'Parent Universe:', 'wpcoreuvigo' ),
				'edit_item'         => __( 'Edit Universe', 'wpcoreuvigo' ),
				'update_item'       => __( 'Update Universe', 'wpcoreuvigo' ),
				'add_new_item'      => __( 'Add New Universe', 'wpcoreuvigo' ),
				'new_item_name'     => __( 'New Universe Name', 'wpcoreuvigo' ),
				'menu_name'         => __( 'Universe', 'wpcoreuvigo' ),
			);

			$args = array(
				'hierarchical'       => true,
				'labels'             => $labels,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_admin_column'  => false,
				'show_in_quick_edit' => false,
				'query_var'          => 'taxonomy-universe',
				'rewrite'            => array( 'slug' => 'universo' ),
			);

			$ob = register_taxonomy(
				self::TAXONOMY_UNIVERSE_NAME,
				array(),
				$args
			);
		}

		// Register post-types
		$universe_cpts = array(
			'post',
		);
		$universe_cpts = apply_filters( 'wpcoreuvigo_register_universe_taxonomy_post_types', $universe_cpts );

		foreach ( $universe_cpts as $cpt ) {
			register_taxonomy_for_object_type( self::TAXONOMY_UNIVERSE_NAME, $cpt );
		}
	}

	/**
	 * Register geographic taxonomy
	 *
	 * @since    1.0.0
	 */
	public function register_geographic_taxonomy() {

		$use_geographic_taxonomy = true;
		$use_geographic_taxonomy = apply_filters( 'wpcoreuvigo_use_geographic_taxonomy_post_types', $use_geographic_taxonomy );

		if ( ! $use_geographic_taxonomy ) {
			return false;
		}

		if ( ! taxonomy_exists( self::TAXONOMY_GEOGRAPHIC_NAME ) ) {
			$labels = array(
				'name'              => _x( 'Geographic', 'taxonomy general name', 'wpcoreuvigo' ),
				'singular_name'     => _x( 'Geographic', 'taxonomy singular name', 'wpcoreuvigo' ),
				'search_items'      => __( 'Search Geographic', 'wpcoreuvigo' ),
				'all_items'         => __( 'All Geographic', 'wpcoreuvigo' ),
				'parent_item'       => __( 'Parent Geographic', 'wpcoreuvigo' ),
				'parent_item_colon' => __( 'Parent Geographic:', 'wpcoreuvigo' ),
				'edit_item'         => __( 'Edit Geographic', 'wpcoreuvigo' ),
				'update_item'       => __( 'Update Geographic', 'wpcoreuvigo' ),
				'add_new_item'      => __( 'Add New Geographic', 'wpcoreuvigo' ),
				'new_item_name'     => __( 'New Geographic Name', 'wpcoreuvigo' ),
				'menu_name'         => __( 'Geographic', 'wpcoreuvigo' ),
			);

			$args = array(
				'hierarchical'       => true,
				'labels'             => $labels,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_admin_column'  => false,
				'show_in_quick_edit' => false,
				'query_var'          => 'taxonomy-geographic',
				'rewrite'            => array( 'slug' => 'xeografico' ),
			);

			$ob = register_taxonomy(
				self::TAXONOMY_GEOGRAPHIC_NAME,
				array(),
				$args
			);
		}

		// Register post-types
		$geographic_cpts = array(
			'post',
		);
		$geographic_cpts = apply_filters( 'wpcoreuvigo_register_geographic_taxonomy_post_types', $geographic_cpts );

		foreach ( $geographic_cpts as $cpt ) {
			register_taxonomy_for_object_type( self::TAXONOMY_GEOGRAPHIC_NAME, $cpt );
		}
	}

	/**
	 * Register taxonomies terms
	 *
	 * @since    1.0.0
	 */
	public function register_taxonomies_terms() {

		// TAXONOMY_UNIVERSE_NAME
		if ( taxonomy_exists( self::TAXONOMY_UNIVERSE_NAME ) ) {
			if ( term_exists( 'Institucional', self::TAXONOMY_UNIVERSE_NAME ) === null ) {
				wp_insert_term( 'Institucional', self::TAXONOMY_UNIVERSE_NAME );
				wp_insert_term( 'Estudar', self::TAXONOMY_UNIVERSE_NAME );
				wp_insert_term( 'Investigar', self::TAXONOMY_UNIVERSE_NAME );
				wp_insert_term( 'No Campus', self::TAXONOMY_UNIVERSE_NAME );
				wp_insert_term( 'Ven a UVigo', self::TAXONOMY_UNIVERSE_NAME );
			}
		}

		// TAXONOMY_SPECTATOR_NAME
		if ( taxonomy_exists( self::TAXONOMY_SPECTATOR_NAME ) ) {
			if ( term_exists( 'Estudantes', self::TAXONOMY_SPECTATOR_NAME ) === null ) {
				wp_insert_term( 'Estudantes', self::TAXONOMY_SPECTATOR_NAME );
				wp_insert_term( 'PAS', self::TAXONOMY_SPECTATOR_NAME );
				wp_insert_term( 'PDI', self::TAXONOMY_SPECTATOR_NAME );
				wp_insert_term( 'Comunidade', self::TAXONOMY_SPECTATOR_NAME );
				wp_insert_term( 'Contratistas', self::TAXONOMY_SPECTATOR_NAME );
				wp_insert_term( 'Entidades Colaboradoras', self::TAXONOMY_SPECTATOR_NAME );
				wp_insert_term( 'Público externo', self::TAXONOMY_SPECTATOR_NAME );
				wp_insert_term( 'Medios', self::TAXONOMY_SPECTATOR_NAME );
			}
			// NOTA:  Creado a partir de la fuente de cursos : Futuro alumnado | Visitantes | Internacional | Alumni
			$spectators = array(
				'Estudantes'              => array( '1', 'TXN.PO.ESTUDANTES' ),
				'Futuro alumnado'         => array( '2', 'TXN.PO.FUTURO_ALUMNADO' ),
				'Internacional'           => array( '3', 'TXN.PO.INTERNACIONAL' ),
				'Alumni'                  => array( '4', 'TXN.PO.ALUMNI' ),
				'PAS'                     => array( '5', 'TXN.PO.PAS' ),
				'PDI'                     => array( '6', 'TXN.PO.PDI' ),
				'Contratistas'            => array( '9', 'TXN.PO.CONTRATRISTAS' ),
				'Entidades Colaboradoras' => array( '10', 'TXN.PO.ENT_COLABORADORAS' ),
				'Visitantes'              => array( '11', 'TXN.PO.VISITANTES' ),
				'Medios'                  => array( '12', 'TXN.PO.MEDIOS' ),
				'Comunidade'              => array( '', '' ),
				'Público externo'         => array( '', '' ),
			);

			foreach ( $spectators as $d => $v ) {
				$term = term_exists( $d, self::TAXONOMY_SPECTATOR_NAME );
				if ( ! isset( $term ) ) {
					$result  = wp_insert_term( $d, self::TAXONOMY_SPECTATOR_NAME );
					$term_id = $result['term_id'];

					add_term_meta( $term_id, 'uvigo_spectator_id', $v[0] );
					add_term_meta( $term_id, 'uvigo_spectator_code', $v[1] );
				}
			}
		}

		// TAXONOMY_GEOGRAPHIC_NAME
		if ( taxonomy_exists( self::TAXONOMY_GEOGRAPHIC_NAME ) ) {
			$geographics = array(
				'Ourense'    => array( '1', 'TXN.AX.OU' ),
				'Pontevedra' => array( '2', 'TXN.AX.PO' ),
				'Vigo'       => array( '3', 'TXN.AX.VI' ),
				'Outros'     => array( '', '' ),
			);

			foreach ( $geographics as $d => $v ) {
				$term = term_exists( $d, self::TAXONOMY_GEOGRAPHIC_NAME );
				if ( ! isset( $term ) ) {
					$result  = wp_insert_term( $d, self::TAXONOMY_GEOGRAPHIC_NAME );
					$term_id = $result['term_id'];

					add_term_meta( $term_id, 'uvigo_geographic_id', $v[0] );
					add_term_meta( $term_id, 'uvigo_geographic_code', $v[1] );
				}
			}
		}
	}

	/**
	 * Create Form of meta_fields of : spectator_taxonomy
	 *
	 * @return void
	 */
	public function spectator_taxonomy_add_new_meta_field_on_create() {
		?>
		<div class="form-field">
			<label for="term_meta[uvigo_spectator_id]"><?php esc_html_e( 'Id', 'wpcoreuvigo' ); ?></label>
			<input type="text" name="term_meta[uvigo_spectator_id]" id="term_meta[uvigo_spectator_id]" value="">
		</div>

		<div class="form-field">
			<label for="term_meta[uvigo_spectator_code]"><?php esc_html_e( 'Internal code', 'wpcoreuvigo' ); ?></label>
			<input type="text" name="term_meta[uvigo_spectator_code]" id="term_meta[uvigo_spectator_code]" value="">
		</div>
		<?php
	}

	/**
	 * Edit Form of meta_fields of : spectator_taxonomy
	 *
	 * @param [Term] $term WordPress object
	 * @return void
	 */
	public function spectator_taxonomy_add_new_meta_field_on_edit( $term ) {
		$t_id      = $term->term_id;
		$term_meta = get_term_meta( $t_id, false );
		?>

		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[uvigo_spectator_id]"><?php esc_html_e( 'Id', 'wpcoreuvigo' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[uvigo_spectator_id]" id="term_meta[uvigo_spectator_id]" value="<?php echo esc_attr( $term_meta['uvigo_spectator_id'][0] ) ? esc_attr( $term_meta['uvigo_spectator_id'][0] ) : ''; ?>">
			</td>
		</tr>

		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[uvigo_spectator_code]"><?php esc_html_e( 'Internal code', 'wpcoreuvigo' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[uvigo_spectator_code]" id="term_meta[uvigo_spectator_code]" value="<?php echo esc_attr( $term_meta['uvigo_spectator_code'][0] ) ? esc_attr( $term_meta['uvigo_spectator_code'][0] ) : ''; ?>">
			</td>
		</tr>

		<?php
	}

	/**
	 * Save meta terms on spectator taxonomy
	 *
	 * @param [type] $term_id
	 * @return void
	 */
	public function save_spectator_taxonomy_custom_meta( $term_id ) {
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id      = $term_id;
			$term_meta = get_term_meta( $t_id, false );
			$cat_keys  = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset( $_POST['term_meta'][ $key ] ) ) {
					update_term_meta( $term_id, $key, $_POST['term_meta'][ $key ] );
				}
			}
		}
	}

	/**
	 * Create Form of meta_fields of : geographic_taxonomy
	 *
	 * @return void
	 */
	public function geographic_taxonomy_add_new_meta_field_on_create() {
		?>
		<div class="form-field">
			<label for="term_meta[uvigo_geographic_id]"><?php esc_html_e( 'Id', 'wpcoreuvigo' ); ?></label>
			<input type="text" name="term_meta[uvigo_geographic_id]" id="term_meta[uvigo_geographic_id]" value="">
		</div>

		<div class="form-field">
			<label for="term_meta[uvigo_geographic_code]"><?php esc_html_e( 'Internal code', 'wpcoreuvigo' ); ?></label>
			<input type="text" name="term_meta[uvigo_geographic_code]" id="term_meta[uvigo_geographic_code]" value="">
		</div>

		<?php
	}

	/**
	 * Edit Form of meta_fields of : geographic_taxonomy
	 *
	 * @param [Term] $term WordPress object
	 * @return void
	 */
	public function geographic_taxonomy_add_new_meta_field_on_edit( $term ) {
		$t_id      = $term->term_id;
		$term_meta = get_term_meta( $t_id, false );
		?>

			<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[uvigo_geographic_id]"><?php esc_html_e( 'Id', 'wpcoreuvigo' ); ?></label></th>
				<td>
					<input type="text" name="term_meta[uvigo_geographic_id]" id="term_meta[uvigo_geographic_id]" value="<?php echo esc_attr( $term_meta['uvigo_geographic_id'][0] ) ? esc_attr( $term_meta['uvigo_geographic_id'][0] ) : ''; ?>">
				</td>
			</tr>

			<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[uvigo_geographic_code]"><?php esc_html_e( 'Internal code', 'wpcoreuvigo' ); ?></label></th>
				<td>
					<input type="text" name="term_meta[uvigo_geographic_code]" id="term_meta[uvigo_geographic_code]" value="<?php echo esc_attr( $term_meta['uvigo_geographic_code'][0] ) ? esc_attr( $term_meta['uvigo_geographic_code'][0] ) : ''; ?>">
				</td>
			</tr>

		<?php
	}

	/**
	 * Save meta terms on geographic taxonomy
	 *
	 * @param [type] $term_id
	 * @return void
	 */
	public function save_geographic_taxonomy_custom_meta( $term_id ) {
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id      = $term_id;
			$term_meta = get_term_meta( $t_id, false );
			$cat_keys  = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset( $_POST['term_meta'][ $key ] ) ) {
					update_term_meta( $term_id, $key, $_POST['term_meta'][ $key ] );
				}
			}
		}
	}

	/**
	 * Whether custom ordering is enabled.
	 *
	 * @param bool $custom Whether custom ordering is enabled.
	 * @return bool
	 */
	public function custom_menu_order( $custom ) {
		return true;
	}

	/**
	 * Order menu
	 *
	 * @param [array] $menu Menú elments
	 * @return array
	 */
	public function menu_order( $menu ) {
		// Change upload position on menu.
		$index_upload = array_search( 'upload.php', $menu, true );
		$index_pages  = array_search( 'edit.php?post_type=page', $menu, true );
		array_splice( $menu, $index_upload, 1 );
		array_splice( $menu, $index_pages, 0, 'upload.php' );

		return $menu;
	}

	/**
	 * Initialize widgets
	 *
	 * @return void
	 */
	public function widgets_init() {
		register_widget( 'Wpcoreuvigo_Filter_Widget' );
	}

	/**
	 * New CPT Documents
	 */

	const UV_DOCUMENT_POST_TYPE          = 'uvigo-document';
	const UV_TAXONOMY_DOCUMENT_TYPE_NAME = 'uvigo-tax-document';

	/**
	 * Register the custom post type document
	 *
	 * @since    1.0.0
	 */
	public function register_document_post_type() {
		$labels = array(
			'name'               => _x( 'Documents', 'post type general name', 'wpcoreuvigo' ),
			'singular_name'      => _x( 'Document', 'post type singular name', 'wpcoreuvigo' ),
			'menu_name'          => _x( 'Documents', 'admin menu', 'wpcoreuvigo' ),
			'name_admin_bar'     => _x( 'Documents', 'add new on admin bar', 'wpcoreuvigo' ),
			'add_new'            => _x( 'Add new', 'Document', 'wpcoreuvigo' ),
			'add_new_item'       => __( 'Add new document', 'wpcoreuvigo' ),
			'new_item'           => __( 'New document', 'wpcoreuvigo' ),
			'edit_item'          => __( 'Edit document', 'wpcoreuvigo' ),
			'view_item'          => __( 'View document', 'wpcoreuvigo' ),
			'all_items'          => __( 'All documents', 'wpcoreuvigo' ),
			'search_items'       => __( 'Search documents', 'wpcoreuvigo' ),
			'parent_item_colon'  => __( 'Parent document:', 'wpcoreuvigo' ),
			'not_found'          => __( 'Documents not found.', 'wpcoreuvigo' ),
			'not_found_in_trash' => __( 'Documents not found in trash.', 'wpcoreuvigo' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Documents', 'wpcoreuvigo' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'documentos' ),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array( 'title', 'editor', 'excerpt', 'custom-fields', 'author' ),
		);
		register_post_type( self::UV_DOCUMENT_POST_TYPE, $args );
	}

	/**
	 * Register Document Type taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_document_type_taxonomy() {
		if ( ! taxonomy_exists( self::UV_TAXONOMY_DOCUMENT_TYPE_NAME ) ) {
			$labels = array(
				'name'              => _x( 'Documents Types', 'taxonomy general name', 'wpcoreuvigo' ),
				'singular_name'     => _x( 'Document Type', 'taxonomy singular name', 'wpcoreuvigo' ),
				'search_items'      => __( 'Search Document Type', 'wpcoreuvigo' ),
				'all_items'         => __( 'All Documents Types', 'wpcoreuvigo' ),
				'parent_item'       => __( 'Parent Document Type', 'wpcoreuvigo' ),
				'parent_item_colon' => __( 'Parent Document Type:', 'wpcoreuvigo' ),
				'edit_item'         => __( 'Edit Document Type', 'wpcoreuvigo' ),
				'update_item'       => __( 'Update Document Type', 'wpcoreuvigo' ),
				'add_new_item'      => __( 'Add New Document Type', 'wpcoreuvigo' ),
				'new_item_name'     => __( 'New Document Type Name', 'wpcoreuvigo' ),
				'menu_name'         => __( 'Document Type', 'wpcoreuvigo' ),
			);

			$args = array(
				'hierarchical'       => true,
				'labels'             => $labels,
				'public'             => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => false,
				'show_admin_column'  => true,
				'show_in_quick_edit' => false,
				'meta_box_cb'        => false,
				'query_var'          => 'taxonomy-document-type',
				'rewrite'            => array( 'slug' => 'document-type' ),
			);

			$ob = register_taxonomy(
				self::UV_TAXONOMY_DOCUMENT_TYPE_NAME,
				array(
					self::UV_DOCUMENT_POST_TYPE,
				),
				$args
			);
		}
	}

	/**
	 * New CPT Actas
	 */

	const UV_ACT_POST_TYPE          = 'uvigo-act';
	const UV_TAXONOMY_ACT_TYPE_NAME = 'uvigo-tax-act';

	/**
	 * Register the custom post type act
	 *
	 * @since    1.0.0
	 */
	public function register_act_post_type() {
		$labels = array(
			'name'               => _x( 'Acts', 'post type general name', 'wpcoreuvigo' ),
			'singular_name'      => _x( 'Act', 'post type singular name', 'wpcoreuvigo' ),
			'menu_name'          => _x( 'Acts', 'admin menu', 'wpcoreuvigo' ),
			'name_admin_bar'     => _x( 'Acts', 'add new on admin bar', 'wpcoreuvigo' ),
			'add_new'            => _x( 'Add new', 'Act', 'wpcoreuvigo' ),
			'add_new_item'       => __( 'Add new act', 'wpcoreuvigo' ),
			'new_item'           => __( 'New act', 'wpcoreuvigo' ),
			'edit_item'          => __( 'Edit act', 'wpcoreuvigo' ),
			'view_item'          => __( 'View act', 'wpcoreuvigo' ),
			'all_items'          => __( 'All acts', 'wpcoreuvigo' ),
			'search_items'       => __( 'Search acts', 'wpcoreuvigo' ),
			'parent_item_colon'  => __( 'Parent act:', 'wpcoreuvigo' ),
			'not_found'          => __( 'Acts not found.', 'wpcoreuvigo' ),
			'not_found_in_trash' => __( 'Acts not found in trash.', 'wpcoreuvigo' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Acts', 'wpcoreuvigo' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'actas' ),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array( 'title', 'editor', 'excerpt', 'custom-fields', 'author' ),
		);
		register_post_type( self::UV_ACT_POST_TYPE, $args );
	}

	/**
	 * Register Act Type taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_act_type_taxonomy() {
		if ( ! taxonomy_exists( self::UV_TAXONOMY_ACT_TYPE_NAME ) ) {
			$labels = array(
				'name'              => _x( 'Acts Types', 'taxonomy general name', 'wpcoreuvigo' ),
				'singular_name'     => _x( 'Act Type', 'taxonomy singular name', 'wpcoreuvigo' ),
				'search_items'      => __( 'Search Act Type', 'wpcoreuvigo' ),
				'all_items'         => __( 'All Acts Types', 'wpcoreuvigo' ),
				'parent_item'       => __( 'Parent Act Type', 'wpcoreuvigo' ),
				'parent_item_colon' => __( 'Parent Act Type:', 'wpcoreuvigo' ),
				'edit_item'         => __( 'Edit Act Type', 'wpcoreuvigo' ),
				'update_item'       => __( 'Update Act Type', 'wpcoreuvigo' ),
				'add_new_item'      => __( 'Add New Act Type', 'wpcoreuvigo' ),
				'new_item_name'     => __( 'New Act Type Name', 'wpcoreuvigo' ),
				'menu_name'         => __( 'Act Type', 'wpcoreuvigo' ),
			);

			$args = array(
				'hierarchical'       => true,
				'labels'             => $labels,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_admin_column'  => true,
				'show_in_quick_edit' => false,
				'meta_box_cb'        => false,
				'query_var'          => 'taxonomy-act-type',
				'rewrite'            => array( 'slug' => 'act-type' ),
			);

			$ob = register_taxonomy(
				self::UV_TAXONOMY_ACT_TYPE_NAME,
				array(
					self::UV_ACT_POST_TYPE,
				),
				$args
			);
		}
	}

	/**
	 * New CPT FORMULARIO
	 */
	const UV_FORM_POST_TYPE          = 'uvigo-form';
	const UV_TAXONOMY_FORM_TYPE_NAME = 'uvigo-tax-form';

	/**
	 * Register the custom post type form
	 *
	 * @since    1.0.0
	 */
	public function register_form_post_type() {
		$labels = array(
			'name'               => _x( 'Forms', 'post type general name', 'wpcoreuvigo' ),
			'singular_name'      => _x( 'Form', 'post type singular name', 'wpcoreuvigo' ),
			'menu_name'          => _x( 'Forms', 'admin menu', 'wpcoreuvigo' ),
			'name_admin_bar'     => _x( 'Forms', 'add new on admin bar', 'wpcoreuvigo' ),
			'add_new'            => _x( 'Add new', 'Form', 'wpcoreuvigo' ),
			'add_new_item'       => __( 'Add new form', 'wpcoreuvigo' ),
			'new_item'           => __( 'New form', 'wpcoreuvigo' ),
			'edit_item'          => __( 'Edit form', 'wpcoreuvigo' ),
			'view_item'          => __( 'View form', 'wpcoreuvigo' ),
			'all_items'          => __( 'All forms', 'wpcoreuvigo' ),
			'search_items'       => __( 'Search forms', 'wpcoreuvigo' ),
			'parent_item_colon'  => __( 'Parent form:', 'wpcoreuvigo' ),
			'not_found'          => __( 'Forms not found.', 'wpcoreuvigo' ),
			'not_found_in_trash' => __( 'Forms not found in trash.', 'wpcoreuvigo' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Forms', 'wpcoreuvigo' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'formularios' ),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array( 'title', 'editor', 'excerpt', 'custom-fields', 'author' ),
		);
		register_post_type( self::UV_FORM_POST_TYPE, $args );
	}

	/**
	 * Register Form Type taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_form_type_taxonomy() {
		if ( ! taxonomy_exists( self::UV_TAXONOMY_FORM_TYPE_NAME ) ) {
			$labels = array(
				'name'              => _x( 'Forms Types', 'taxonomy general name', 'wpcoreuvigo' ),
				'singular_name'     => _x( 'Form Type', 'taxonomy singular name', 'wpcoreuvigo' ),
				'search_items'      => __( 'Search Form Type', 'wpcoreuvigo' ),
				'all_items'         => __( 'All Forms Types', 'wpcoreuvigo' ),
				'parent_item'       => __( 'Parent Form Type', 'wpcoreuvigo' ),
				'parent_item_colon' => __( 'Parent Form Type:', 'wpcoreuvigo' ),
				'edit_item'         => __( 'Edit Form Type', 'wpcoreuvigo' ),
				'update_item'       => __( 'Update Form Type', 'wpcoreuvigo' ),
				'add_new_item'      => __( 'Add New Form Type', 'wpcoreuvigo' ),
				'new_item_name'     => __( 'New Form Type Name', 'wpcoreuvigo' ),
				'menu_name'         => __( 'Form Type', 'wpcoreuvigo' ),
			);

			$args = array(
				'hierarchical'       => true,
				'labels'             => $labels,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_admin_column'  => true,
				'show_in_quick_edit' => false,
				'meta_box_cb'        => false,
				'query_var'          => 'taxonomy-form-type',
				'rewrite'            => array( 'slug' => 'form-type' ),
			);

			$ob = register_taxonomy(
				self::UV_TAXONOMY_FORM_TYPE_NAME,
				array(
					self::UV_FORM_POST_TYPE,
				),
				$args
			);
		}
	}

	/**
	 * Add field to Posts for set video url
	 *
	 * @param [type] $content
	 * @param [type] $post_id
	 * @return void
	 */
	public function add_featured_video_url( $content, $post_id ) {

		// Only featured video in post type
		if ( 'post' !== get_post_type( $post_id ) ) {
			return $content;
		}

		$field_id    = 'uvigo_featured_video_url';
		$field_value = esc_attr( get_post_meta( $post_id, $field_id, true ) );
		$field_text  = esc_html__( 'Video url:', 'wpcoreuvigo' );

		$field_label = sprintf(
			'<p><label for="%1$s">%3$s</label><input type="text" name="%1$s" id="%1$s" value="%2$s" class="widefat"></p>',
			$field_id,
			$field_value,
			$field_text
		);

		return $content .= $field_label;
	}

	/**
	 * Save video url in Posts
	 *
	 * @param [type] $post_ID
	 * @param [type] $post
	 * @param [type] $update
	 * @return void
	 */
	public function save_featured_video_url( $post_id, $post, $update ) {

		// Only featured video in post type
		if ( 'post' !== get_post_type( $post_id ) ) {
			return;
		}

		$field_id = 'uvigo_featured_video_url';

		if ( isset( $_POST[ $field_id ] ) ) {
			$field_value = $_POST[ $field_id ];

			$youtube_pattern = '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#';
			$vimeo_pattern   = '#^https?://(.+\.)?vimeo\.com/.*#';

			if ( preg_match( $youtube_pattern, $field_value ) || preg_match( $vimeo_pattern, $field_value ) ) {
				$field_value = esc_url( $field_value );
				update_post_meta( $post_id, $field_id, $field_value );
			}
		}
	}

	/**
	 * Add field to Posts to set Thumbnail visibility
	 *
	 * @param [type] $content
	 * @param [type] $post_id
	 * @return void
	 */
	public function add_hide_thumbnail( $content, $post_id ) {

		// Only featured video in post type
		if ( ! in_array( get_post_type( $post_id ), [ 'post', 'page' ], true ) ) {
			return $content;
		}

		$field_id    = 'uvigo_hide_thumbnail_in_single';
		$field_value = esc_attr( get_post_meta( $post_id, $field_id, true ) );
		$field_text  = esc_html__( 'Hide image in single view', 'wpcoreuvigo' );

		$field_label = sprintf(
			'<p><label for="%1$s"><input type="checkbox" name="%1$s" id="%1$s" value="1" %4$s>%3$s</label></p>',
			$field_id,
			$field_value,
			$field_text,
			checked( $field_value, 1, false )
		);

		return $content .= $field_label;
	}

	/**
	 * Save Thumbnail visibility in Posts
	 *
	 * @param [type] $post_ID
	 * @param [type] $post
	 * @param [type] $update
	 * @return void
	 */
	public function save_hide_thumbnail( $post_id, $post, $update ) {

		// Only featured video in post type
		if ( ! in_array( get_post_type( $post_id ), [ 'post', 'page' ], true ) ) {
			return;
		}

		$field_id = 'uvigo_hide_thumbnail_in_single';

		if ( isset( $_POST[ $field_id ] ) ) {
			$field_value = $_POST[ $field_id ];
			if ( $field_value ) {
				update_post_meta( $post_id, $field_id, $field_value );
			} else {
				delete_post_meta( $post_id, $field_id );
			}
		} else {
			delete_post_meta( $post_id, $field_id );
		}
	}

	/**
	 * Restringir edición de taxonomia en documentos y actas
	 *
	 * Field name : uvigo_act_taxonomy
	 * Field name : uvigo_document_taxonomy
	 *
	 * Modify a field taxonomy before it is rendered
	 */
	function prepare_field_before_render_uvigo_taxonomy( $field ) {
		if( $field['value'] ) {
			//OJO: Si se deshabilita, no se envia en el form y da un error de validación despues
			//$field['disabled'] = true;

			$field['add_term'] = false;
		}
		return $field;
	}

	/**
	 * Visualiza o no el botón de añadir documentos en Actas
	 *
	 * @return void
	 */
	function check_ACF_add_files_permissions_button() {

		$post = get_post();
		if ( $post ) {
			$post_type = get_post_type( $post );
			if ( $post_type == Wpcoreuvigo_Admin::UV_ACT_POST_TYPE ) {
				$post_id = $post->ID;
				//$terms = get_the_terms( $post->ID, Wpcoreuvigo_Admin::UV_TAXONOMY_ACT_TYPE_NAME );
				$taxonomy = get_field('uvigo_act_taxonomy', $post_id, false);
				$date = get_field('uvigo_act_date', $post_id, false);

				// Si no hay taxonomía seleccionada, no permite añadir actas.
				if ( empty($taxonomy) || empty($date) ) {
					// Bloqueo por JavaScript
					?><script type="text/javascript">
					jQuery('div[data-name="uvigo_act_documents"].acf-field-repeater .acf-actions a[data-event="add-row"]').remove();
					jQuery('div[data-name="uvigo_act_documents"].acf-field-repeater .acf-actions a[data-event="remove-row"]').remove();
					jQuery('div[data-name="uvigo_act_documents"].acf-field-repeater .acf-label').append( "<p>Necesario gardar antes de subir documento.</p>" )
					</script><?php
				}
			}
			if ( $post_type == Wpcoreuvigo_Admin::UV_DOCUMENT_POST_TYPE ) {
				$post_id = $post->ID;
				$taxonomy = get_field('uvigo_document_taxonomy', $post_id, false);
				// Si no hay taxonomía seleccionada, no permite añadir documentos.
				if ( empty($taxonomy) ) {
					// Bloqueo por JavaScript
					?><script type="text/javascript">
					jQuery('div[data-name="uvigo_document_file"] .acf-file-uploader a[data-name="add"]').remove();
					jQuery('div[data-name="uvigo_document_file"] .acf-file-uploader .hide-if-value p' ).html('Necesario gardar antes de subir documento.');
					</script><?php
				}/*
				else {
					// Deshabilitar taxonomia por JavaScript ( en desarrollo .. )
					?><script type="text/javascript">
					jQuery('div[data-name="uvigo_document_taxonomy"] select').select2("readonly", true);
					</script><?php
				}*/
			}
			if ( $post_type == Wpcoreuvigo_Admin::UV_FORM_POST_TYPE ) {
				$post_id = $post->ID;
				$taxonomy = get_field('uvigo_form_taxonomy', $post_id, false);
				// Si no hay taxonomía seleccionada, no permite añadir formularios.
				if ( empty($taxonomy) ) {
					// Bloqueo por JavaScript
					?><script type="text/javascript">
					jQuery('div[data-name="uvigo_form_document_doc"] .acf-file-uploader a[data-name="add"]').remove();
					jQuery('div[data-name="uvigo_form_document_doc"] .acf-file-uploader .hide-if-value p' ).html('Necesario gardar antes de subir formulario.');

					jQuery('div[data-name="uvigo_form_document_pdf"] .acf-file-uploader a[data-name="add"]').remove();
					jQuery('div[data-name="uvigo_form_document_pdf"] .acf-file-uploader .hide-if-value p' ).html('Necesario gardar antes de subir formulario.');

					jQuery('div[data-name="uvigo_form_document_odt"] .acf-file-uploader a[data-name="add"]').remove();
					jQuery('div[data-name="uvigo_form_document_odt"] .acf-file-uploader .hide-if-value p' ).html('Necesario gardar antes de subir formulario.');
					</script><?php
				}
			}
		}
	}

	/**
	 * Change Upload Directory for Custom Post-Type
	 *
	 * This will change the upload directory for a custom post-type. Attachments will
	 * now be uploaded to other directory.
	 */
	function custom_upload_directory( $args ) {
		$post_type = '';
		$post_id = '';
		if ( isset( $_REQUEST['post'] ) ) {
			$post_id = $_REQUEST['post'];
			$post_type = get_post_type( $post_id );
		} elseif ( isset( $_REQUEST['post_id'] ) ) {
			$post_id = $_REQUEST['post_id'];
			$post_type = get_post_type( $post_id );
		}
		if ( $post_id ){
			return $this->custom_upload_directory_by_post_type($args, $post_type, $post_id);
		} else {
			return $args;
		}
	}

	function handle_upload_prefilter( $file )
	{
		add_filter( 'upload_dir', array( $this, 'custom_upload_directory' ) );
		return $file;
	}

	function handle_upload( $fileinfo )
	{
		remove_filter( 'upload_dir', array( $this, 'custom_upload_directory') );
		return $fileinfo;
	}

	/**
	 * Determine Directory by Custom Post-Type
	 *
	 * @param [type] $args
	 * @param [type] $post_type
	 * @return void
	 */
	function custom_upload_directory_by_post_type( $args, $post_type, $post_id ) {

		if ( $post_type ) {
			switch ( $post_type ) {

				case self::UV_ACT_POST_TYPE:
					$new = array_merge( $args, array() );

					$label_post_type = 'actas';

					$term_id = get_field( 'uvigo_act_taxonomy', $post_id, false );
					$date    = get_field( 'uvigo_act_date', $post_id, false );
					if ( ! empty( $term_id ) && ! empty( $date ) ) {
						$taxonomy_slugs_dir = get_term_parents_list(
							$term_id,
							self::UV_TAXONOMY_ACT_TYPE_NAME,
							array(
								'format'    => 'slug',
								'separator' => '/',
								'link'      => false,
								'inclusive' => true,
							)
						);

						$date  = new DateTime( $date );
						$year  = $date->format( 'Y' );
						$month = $date->format( 'm' );

						$taxonomy_slugs_dir = substr( $taxonomy_slugs_dir, 0, -1 );

						$dir    = '/' . $taxonomy_slugs_dir . '/' . $year . '/' . $month;
						$subdir = '/' . $label_post_type . $dir;
					} else {
						$subdir = '/' . $label_post_type . $new['subdir'];
					}

					$new['path']   = str_replace( $new['subdir'], $subdir, $new['path'] );
					$new['url']    = str_replace( $new['subdir'], $subdir, $new['url'] );
					$new['subdir'] = str_replace( $new['subdir'], $subdir, $new['subdir'] );

					$args = $new;

					break;

				case self::UV_DOCUMENT_POST_TYPE:
					$new = array_merge( $args, array() );

					$label_post_type = 'documentos';

					$term_id = get_field( 'uvigo_document_taxonomy', $post_id, false );
					if ( ! empty( $term_id ) ) {
						$taxonomy_slugs_dir = get_term_parents_list(
							$term_id,
							self::UV_TAXONOMY_DOCUMENT_TYPE_NAME,
							array(
								'format'    => 'slug',
								'separator' => '/',
								'link'      => false,
								'inclusive' => true,
							)
						);

						$taxonomy_slugs_dir = substr( $taxonomy_slugs_dir, 0, -1 );

						$subdir = '/' . $label_post_type . '/' . $taxonomy_slugs_dir;
					} else {
						$subdir = '/' . $label_post_type . $new['subdir'];
					}

					$new['path']   = str_replace( $new['subdir'], $subdir, $new['path'] );
					$new['url']    = str_replace( $new['subdir'], $subdir, $new['url'] );
					$new['subdir'] = str_replace( $new['subdir'], $subdir, $new['subdir'] );

					$args = $new;
					break;

				case self::UV_FORM_POST_TYPE:
					$new = array_merge( $args, array() );

					$label_post_type = 'formularios';

					$term_id = get_field( 'uvigo_form_taxonomy', $post_id, false );
					if ( ! empty( $term_id ) ) {
						$taxonomy_slugs_dir = get_term_parents_list(
							$term_id,
							self::UV_TAXONOMY_FORM_TYPE_NAME,
							array(
								'format'    => 'slug',
								'separator' => '/',
								'link'      => false,
								'inclusive' => true,
							)
						);

						$taxonomy_slugs_dir = substr( $taxonomy_slugs_dir, 0, -1 );
						$form_slug_dir = get_post_field('post_name', $post_id);

						$subdir = '/' . $label_post_type . '/' . $taxonomy_slugs_dir . '/' . $form_slug_dir;
					} else {
						$subdir = '/' . $label_post_type . $new['subdir'];
					}

					$new['path']   = str_replace( $new['subdir'], $subdir, $new['path'] );
					$new['url']    = str_replace( $new['subdir'], $subdir, $new['url'] );
					$new['subdir'] = str_replace( $new['subdir'], $subdir, $new['subdir'] );

					$args = $new;
					break;

				default:
					// Do nothing
					break;
			}

			// error_log("custom_upload_directory");
			// error_log(print_r($args,true));
		}

		return $args;
	}

	/**
	 * Restrinxe a edición da taxonomia de Tipo Documento
	 *
	 * @return void
	 */
	function restrict_update_taxonomy_document_type( $term_id, $taxonomy ){
		if ($taxonomy == Wpcoreuvigo_Admin::UV_TAXONOMY_DOCUMENT_TYPE_NAME){
			$term = get_term( $term_id, $taxonomy );
			if ( $term->count  > 0 ){
				wp_die(
					'<h1>' . __( 'Non se pode modificar o tipo de documento.' ) . '</h1>' .
					'<p>' . __( 'Sentímolo, pero non se pode modificar o tipo de documento, para evitar problemas co acceso os ficheiros en disco.' ) . '</p>',
					403
				);
			}
		}
	}

	/**
	 * Engade páxina de Utilidades
	 *
	 * @return void
	 */
	public function add_management_uvigo_tools_page() {
		add_management_page(
			'Uvigo Tools',
			'Uvigo Tools',
			'install_plugins',
			'uvigo_tools',
			array( $this, 'uvigo_tools' ),
			''
		);
	}

	/**
	 * Páxina de Uvigo Tools
	 *
	 * Visualización de operaciones e utilidades.
	 *
	 * @return void
	 */
	public function uvigo_tools() {
		?>
		<form method="post">
			<?php wp_nonce_field( 'uvigo_tools_management' ); ?>
			<h3>Operación para reubicar documentos</h3>
			<p> Esta operación movera os documentos de uploads/dir -> uploads/documents/dir </p>
			<button style="background-color:green;" type="submit" name="execute_test_move_documents" value="execute">
				<span>Test : Testear Mover documentos</span>
			</button>
			<button style="background-color:red;" type="submit" name="execute_move_documents" value="execute">
				<span>Executar : Mover documentos</span>
			</button>
		</form>
		<?php
		if ( isset( $_POST[ 'execute_test_move_documents' ] ) && check_admin_referer( 'uvigo_tools_management' ) ) {
			echo '<h3>RESULTADO TEST</h3>';
			$this->uvigo_documents_tools( false );
		}
		if ( isset( $_POST[ 'execute_move_documents' ] ) && check_admin_referer( 'uvigo_tools_management' ) ) {
			echo '<h3>RESULTADO EXECUCION</h3>';
			$this->uvigo_documents_tools( true );
		}
	}

	/**
	 * TOOLS : Move Documents de updloads -> uploads/documents
	 *
	 * @param boolean $execute : really move or only test
	 * @return void
	 */
	private function uvigo_documents_tools( $execute = false ) {
		$documents = get_posts(
			array(
				'post_type' => self::UV_DOCUMENT_POST_TYPE,
				'orderby'   => 'id',
				'order'     => 'ASC',
				'posts_per_page' => -1,
			)
		);

		$i = 0;

		foreach ( $documents as $document ) {
			echo '</br>';
			$document_post_id = $document->ID;

			$field = get_field( 'uvigo_document_file', $document_post_id, false );

			// Recuperamos ID del attachment:
			echo '</br>['.$i.'] DOCUMENT ID ' . $document_post_id;
			echo '</br>Field: ';
			echo print_r( $field, true );
			if ( $field ) {
				$att = get_post( $field );
				if ( $att ) {
					$attachment_id = $att->ID;
					echo '<div style="margin-left:20px">ATT ID : ' . $attachment_id . ' Title ' . $att->post_title . '</div>';
					$fullsize_path = get_attached_file( $attachment_id ); // Full path
					echo '<div style="margin-left:40px">ATT fullsize : ' . $fullsize_path . '</div>';

					// Uploads Dir para Documento : Determinar cual sería la ruta para almacenar el documento
					$date = mysql2date( 'Y/m', $document->post_date );

					$uploads = _wp_upload_dir( $date );
					$uploads = $this->custom_upload_directory_by_post_type( $uploads, self::UV_DOCUMENT_POST_TYPE, $document_post_id );

					echo '<div style="margin-left:40px">MOVE TO : </div>';
					foreach ( $uploads as $key => $value) {
						echo '<div style="margin-left:50px">['.$key. '] = ' . print_r( $value, true ) . '</div>';
					}
					// Recuperamos nombre del fichero
					$name = basename( $fullsize_path );
					$new_fullsize_path = $uploads['path'] . "/$name";

					// Validamos si el fichero ya está en la ruta esperada
					if ( $fullsize_path !== $new_fullsize_path ) {
						// Aseguramos que no exista conflicto por nombre
						$filename = wp_unique_filename( $uploads['path'], $name );
						$new_fullsize_path = $uploads['path'] . "/$filename";
						echo '<div style="margin-left:50px">NEW PATH : ' . $new_fullsize_path . '</div>';
						echo '</br>';

						echo '<div style="margin-left:20px">FROM PATH : ' . $fullsize_path . '</div>';
						echo '<div style="margin-left:20px">  TO PATH : ' . $new_fullsize_path . '</div>';

						if ( $execute ) {
							echo '<div style="margin-left:20px">MOVENDO .. </div>';

							// Creamos directorio en disco si no existe
							if ( ! is_dir( $uploads['path'] ) ) {
								echo '<div style="margin-left:20px">Creando directorio .. </div>';
								$mkdirok = mkdir( $uploads['path'], 0777, true );
								echo '<div style="margin-left:20px">CREADO DIR : ' . $mkdirok . ' </div>';
							}

							// Movemos Fichero en disco
							$ok = rename( $fullsize_path, $new_fullsize_path );
							echo '<div style="margin-left:20px">MOVED : ' . $ok . ' </div>';
							if ( $ok === true ) {
								// Actualizamos información del post.
								$updated = update_attached_file( $attachment_id, $new_fullsize_path );
								echo '<div style="margin-left:20px">UPDATED : ' . $updated . ' </div>';
							}
						}
					} else {
						echo '<div style="margin-left:20px">Ya está en la ruta esperada.</div>';
					}
				}
			}
			$i++;
		}
		echo 'FIN';
	}

	/**
	 * Visualización de columnas en ACTAS : Fecha
	 *
	 * @param [type] $columns
	 * @return void
	 */
	function manage_uvigo_act_columns( $columns ) {
		$start = array_slice( $columns, 0, 2 );
		return array_merge(
			$start,
			array(
				'uvigo_act_date' => 'Data',
			),
			$columns
		);
	}

	/**
	 * Visualización contenido de columnas en ACTAS
	 *
	 * @param [type] $column_name
	 * @param [type] $post_id
	 * @return void
	 */
	function manage_uvigo_act_custom_column( $column_name, $post_id ) {
		if ( $column_name == 'uvigo_act_date' ) {
			$date = get_field('uvigo_act_date', $post_id, false);
			$date = new DateTime( $date );
			echo date_i18n( get_option( 'date_format' ), $date->getTimestamp() );
		}
	}

	/**
	 * Visualización de columnas en DOCUMENTOS
	 *
	 * @param [type] $columns
	 * @return void
	 */
	function manage_uvigo_document_columns( $columns ) {
		$start = array_slice( $columns, 0, 4 );
		return array_merge(
			$start,
			array(
				'uvigo_document_taxonomy_hierarchy' => 'Xerarquía',
			),
			$columns
		);
	}

	/**
	 * Visualización contenido de columnas en DOCUMENTOS
	 *
	 * @param [type] $column_name
	 * @param [type] $post_id
	 * @return void
	 */
	function manage_uvigo_document_custom_column( $column_name, $post_id ) {
		if ( $column_name == 'uvigo_document_taxonomy_hierarchy' ) {
			$term_id = get_field( 'uvigo_document_taxonomy', $post_id, false );
			echo get_term_parents_list( $term_id, Wpcoreuvigo_Admin::UV_TAXONOMY_DOCUMENT_TYPE_NAME, array( 'inclusive' => true ) );
		}
	}

	/**
	 * Filtro por taxonomia en Actas
	 *
	 * @param [type] $post_type
	 * @param [type] $which
	 * @return void
	 */
	function manage_posts_table_filtering_uvigo_act( $post_type, $which ) {

		global $wpdb;

		if ( $post_type == Wpcoreuvigo_Admin::UV_ACT_POST_TYPE ) {

			$taxonomy_slug = Wpcoreuvigo_Admin::UV_TAXONOMY_ACT_TYPE_NAME;
			$taxonomy = get_taxonomy( $taxonomy_slug );
			$selected = '';
			$request_attr = 'taxonomy-act-type'; //this will show up in the url

			if ( isset( $_REQUEST[ $request_attr ] ) ) {
				$selected = $_REQUEST[ $request_attr ]; //in case the current page is already filtered
			}

			wp_dropdown_categories(array(
				'show_option_all' =>  __("Ver todas as {$taxonomy->label}"),
				'taxonomy'        =>  $taxonomy_slug,
				'name'            =>  $request_attr,
				'value_field'     =>  'slug',
				'orderby'         =>  'name',
				'order'           =>  'DESC',
				'selected'        =>  $selected,
				'hierarchical'    =>  false,
				'depth'           =>  0,
				'show_count'      =>  false, // Show number of post in parent term
				'hide_empty'      =>  false, // Don't show posts w/o terms
			));
		}
	}

	/**
	 * Filtro por taxonomia en Documentos
	 *
	 * @param [type] $post_type
	 * @param [type] $which
	 * @return void
	 */
	function manage_posts_table_filtering_uvigo_document( $post_type, $which ) {

		global $wpdb;

		if ( $post_type == Wpcoreuvigo_Admin::UV_DOCUMENT_POST_TYPE ) {

			$taxonomy_slug = Wpcoreuvigo_Admin::UV_TAXONOMY_DOCUMENT_TYPE_NAME;
			$taxonomy = get_taxonomy( $taxonomy_slug );
			$selected = '';
			$request_attr = 'taxonomy-document-type'; //this will show up in the url

			if ( isset( $_REQUEST[ $request_attr ] ) ) {
				$selected = $_REQUEST[ $request_attr ]; //in case the current page is already filtered
			}

			wp_dropdown_categories(array(
				'show_option_all' =>  __("Ver todas as {$taxonomy->label}"),
				'taxonomy'        =>  $taxonomy_slug,
				'name'            =>  $request_attr,
				'value_field'     =>  'slug',
				'orderby'         =>  'name',
				'order'           =>  'DESC',
				'selected'        =>  $selected,
				'hierarchical'    =>  true,
				'depth'           =>  0,
				'show_count'      =>  false, // Show number of post in parent term
				'hide_empty'      =>  false, // Don't show posts w/o terms
			));
		}
	}

	/**
	 * Asigna un Alias al tipo de fichero
	 *
	 * @param [type] $file_type
	 * @return void
	 */
	function wpcoreuvigo_acf_file_subtype_alias( $file_type ) {
		$file_type_alias = $file_type;
		switch ( $file_type ) {
			case 'msword':
				$file_type_alias = 'doc';
				break;
			case 'vnd.oasis.opendocument.text':
				$file_type_alias = 'odt';
				break;
			default:
				$file_type_alias = $file_type;
				break;
		}
		return $file_type_alias;
	}

	/**
	 * ACF
	 *
	 * Add a Act, Document, Forms field group
	 *
	 * @return void
	 */
	function wpcoreuvigo_acf_add_local_field_groups() {
		// ACTAS
		acf_add_local_field_group(array(
			'key' => 'group_5c6aa1928cca5',
			'title' => 'Acta',
			'fields' => array(
				array(
					'key' => 'field_5c6d5bf24af0a',
					'label' => 'Taxonomía',
					'name' => 'uvigo_act_taxonomy',
					'type' => 'taxonomy',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'taxonomy' => 'uvigo-tax-act',
					'field_type' => 'select',
					'allow_null' => 1,
					'add_term' => 1,
					'save_terms' => 1,
					'load_terms' => 0,
					'return_format' => 'object',
					'multiple' => 0,
				),
				array(
					'key' => 'field_5c6aa19c140b0',
					'label' => 'Data',
					'name' => 'uvigo_act_date',
					'type' => 'date_picker',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'display_format' => 'd/m/Y',
					'return_format' => 'd/m/Y',
					'first_day' => 1,
				),
				array(
					'key' => 'field_5c6aa206140b1',
					'label' => 'Documentos',
					'name' => 'uvigo_act_documents',
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'collapsed' => '',
					'min' => 0,
					'max' => 0,
					'layout' => 'row',
					'button_label' => '',
					'sub_fields' => array(
						array(
							'key' => 'field_5c6aa242140b2',
							'label' => 'Titulo',
							'name' => 'uvigo_act_document_title',
							'type' => 'text',
							'instructions' => '',
							'required' => 1,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
						array(
							'key' => 'field_5c6aa254140b3',
							'label' => 'Documento',
							'name' => 'uvigo_act_document_file',
							'type' => 'file',
							'instructions' => '',
							'required' => 1,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'wpml_cf_preferences' => 0,
							'return_format' => 'array',
							'library' => 'all',
							'min_size' => '',
							'max_size' => '',
							'mime_types' => '',
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'uvigo-act',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
		));

		// Documentos
		acf_add_local_field_group(array(
			'key' => 'group_5b8e42f3be05a',
			'title' => 'Documentos',
			'fields' => array(
				array(
					'key' => 'field_5c6ffaf011690',
					'label' => 'Taxonomia',
					'name' => 'uvigo_document_taxonomy',
					'type' => 'taxonomy',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'taxonomy' => 'uvigo-tax-document',
					'field_type' => 'select',
					'allow_null' => 0,
					'add_term' => 1,
					'save_terms' => 1,
					'load_terms' => 1,
					'return_format' => 'object',
					'multiple' => 0,
				),
				array(
					'key' => 'field_5b8e3e4639624',
					'label' => 'Origen de datos',
					'name' => 'uvigo_document_origin_type',
					'type' => 'select',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						'file' => 'Document',
						'url' => 'Url',
					),
					'default_value' => array(
					),
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'ajax' => 0,
					'placeholder' => '',
					'return_format' => 'value',
				),
				array(
					'key' => 'field_5b8e3eb639625',
					'label' => 'Documento',
					'name' => 'uvigo_document_file',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_5b8e3e4639624',
								'operator' => '==',
								'value' => 'file',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'library' => 'all',
					'min_size' => '',
					'max_size' => '',
					'mime_types' => '',
				),
				array(
					'key' => 'field_5b8e3ef539626',
					'label' => 'Url',
					'name' => 'uvigo_document_url',
					'type' => 'url',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_5b8e3e4639624',
								'operator' => '==',
								'value' => 'url',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'uvigo-document',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'seamless',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
		));

		// Formularios
		acf_add_local_field_group(array(
			'key' => 'group_5d15d1458734e',
			'title' => 'Formularios',
			'fields' => array(
				array(
					'key' => 'field_5d15d14aa8747',
					'label' => 'Taxonomía',
					'name' => 'uvigo_form_taxonomy',
					'type' => 'taxonomy',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'taxonomy' => 'uvigo-tax-form',
					'field_type' => 'select',
					'allow_null' => 1,
					'add_term' => 1,
					'save_terms' => 1,
					'load_terms' => 0,
					'return_format' => 'object',
					'wpml_cf_preferences' => 0,
					'multiple' => 0,
				),
				array(
					'key' => 'field_5d15d1e6a8748',
					'label' => 'Doc',
					'name' => 'uvigo_form_document_doc',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'library' => 'all',
					'min_size' => '',
					'max_size' => '',
					'mime_types' => '',
					'wpml_cf_preferences' => 0,
				),
				array(
					'key' => 'field_5d15d210a8749',
					'label' => 'Pdf',
					'name' => 'uvigo_form_document_pdf',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'library' => 'all',
					'min_size' => '',
					'max_size' => '',
					'mime_types' => '',
					'wpml_cf_preferences' => 0,
				),
				array(
					'key' => 'field_5d15d222a874a',
					'label' => 'Odt',
					'name' => 'uvigo_form_document_odt',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'library' => 'all',
					'min_size' => '',
					'max_size' => '',
					'mime_types' => '',
					'wpml_cf_preferences' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'uvigo-form',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
		));
	}

}
