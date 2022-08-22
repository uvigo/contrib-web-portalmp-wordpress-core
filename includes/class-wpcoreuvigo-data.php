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
class Wpcoreuvigo_Data
{

	// TAX
	const TAXONOMY_SPECTATOR_NAME  = 'spectator';
	const TAXONOMY_UNIVERSE_NAME   = 'universe';
	const TAXONOMY_GEOGRAPHIC_NAME = 'geographic';

	// CPT Document
	const UV_DOCUMENT_POST_TYPE          = 'uvigo-document';
	const UV_TAXONOMY_DOCUMENT_TYPE_NAME = 'uvigo-tax-document';

	// CPT Actas
	const UV_ACT_POST_TYPE          = 'uvigo-act';
	const UV_TAXONOMY_ACT_TYPE_NAME = 'uvigo-tax-act';

	// CPT Formulario
	const UV_FORM_POST_TYPE          = 'uvigo-form';
	const UV_TAXONOMY_FORM_TYPE_NAME = 'uvigo-tax-form';

	// CPT Fitos Históricos
	const UV_POST_TYPE_MILESTONE         = 'uvigo-milestone';
	const UV_TAXONOMY_MILESTONE_CATEGORY = 'uvigo-tax-milestone';

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Cambiamos el nombre de los artículos de WordPress 'post'
	 */
	public function change_post_type_labels( $labels ) {

		$labels->name          = _x( 'News', 'post_type_labels', 'wpcoreuvigo' );
		$labels->singular_name = _x( 'New', 'post_type_labels', 'wpcoreuvigo' );

		return $labels;
	}

	/**
	 * Register spectator taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_spectator_taxonomy()
	{

		$use_spectator_taxonomy = true;
		$use_spectator_taxonomy = apply_filters('wpcoreuvigo_use_spectator_taxonomy_post_types', $use_spectator_taxonomy);

		if (!$use_spectator_taxonomy) {
			return false;
		}

		if (!taxonomy_exists(self::TAXONOMY_SPECTATOR_NAME)) {
			$labels = array(
				'name'              => _x('Spectators', 'taxonomy general name', 'wpcoreuvigo'),
				'singular_name'     => _x('Spectator', 'taxonomy singular name', 'wpcoreuvigo'),
				'search_items'      => __('Search Spectator', 'wpcoreuvigo'),
				'all_items'         => __('All Spectators', 'wpcoreuvigo'),
				'parent_item'       => __('Parent Spectator', 'wpcoreuvigo'),
				'parent_item_colon' => __('Parent Spectator:', 'wpcoreuvigo'),
				'edit_item'         => __('Edit Spectator', 'wpcoreuvigo'),
				'update_item'       => __('Update Spectator', 'wpcoreuvigo'),
				'add_new_item'      => __('Add New Spectator', 'wpcoreuvigo'),
				'new_item_name'     => __('New Spectator Name', 'wpcoreuvigo'),
				'menu_name'         => __('Spectator', 'wpcoreuvigo'),
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
				'rewrite'            => array('slug' => 'publico'),
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
		$spectators_cpts = apply_filters('wpcoreuvigo_register_spectator_taxonomy_post_types', $spectators_cpts);

		foreach ($spectators_cpts as $cpt) {
			register_taxonomy_for_object_type(self::TAXONOMY_SPECTATOR_NAME, $cpt);
		}
	}

	/**
	 * Register universe taxonomy
	 *
	 * @since    1.0.0
	 */
	public function register_universe_taxonomy()
	{

		$use_universe_taxonomy = true;
		$use_universe_taxonomy = apply_filters('wpcoreuvigo_use_universe_taxonomy_post_types', $use_universe_taxonomy);

		if (!$use_universe_taxonomy) {
			return false;
		}

		if (!taxonomy_exists(self::TAXONOMY_UNIVERSE_NAME)) {
			$labels = array(
				'name'              => _x('Universes', 'taxonomy general name', 'wpcoreuvigo'),
				'singular_name'     => _x('Universe', 'taxonomy singular name', 'wpcoreuvigo'),
				'search_items'      => __('Search Universe', 'wpcoreuvigo'),
				'all_items'         => __('All Universe', 'wpcoreuvigo'),
				'parent_item'       => __('Parent Universe', 'wpcoreuvigo'),
				'parent_item_colon' => __('Parent Universe:', 'wpcoreuvigo'),
				'edit_item'         => __('Edit Universe', 'wpcoreuvigo'),
				'update_item'       => __('Update Universe', 'wpcoreuvigo'),
				'add_new_item'      => __('Add New Universe', 'wpcoreuvigo'),
				'new_item_name'     => __('New Universe Name', 'wpcoreuvigo'),
				'menu_name'         => __('Universe', 'wpcoreuvigo'),
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
				'rewrite'            => array('slug' => 'universo'),
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
		$universe_cpts = apply_filters('wpcoreuvigo_register_universe_taxonomy_post_types', $universe_cpts);

		foreach ($universe_cpts as $cpt) {
			register_taxonomy_for_object_type(self::TAXONOMY_UNIVERSE_NAME, $cpt);
		}
	}

	/**
	 * Register geographic taxonomy
	 *
	 * @since    1.0.0
	 */
	public function register_geographic_taxonomy()
	{

		$use_geographic_taxonomy = true;
		$use_geographic_taxonomy = apply_filters('wpcoreuvigo_use_geographic_taxonomy_post_types', $use_geographic_taxonomy);

		if (!$use_geographic_taxonomy) {
			return false;
		}

		if (!taxonomy_exists(self::TAXONOMY_GEOGRAPHIC_NAME)) {
			$labels = array(
				'name'              => _x('Geographic', 'taxonomy general name', 'wpcoreuvigo'),
				'singular_name'     => _x('Geographic', 'taxonomy singular name', 'wpcoreuvigo'),
				'search_items'      => __('Search Geographic', 'wpcoreuvigo'),
				'all_items'         => __('All Geographic', 'wpcoreuvigo'),
				'parent_item'       => __('Parent Geographic', 'wpcoreuvigo'),
				'parent_item_colon' => __('Parent Geographic:', 'wpcoreuvigo'),
				'edit_item'         => __('Edit Geographic', 'wpcoreuvigo'),
				'update_item'       => __('Update Geographic', 'wpcoreuvigo'),
				'add_new_item'      => __('Add New Geographic', 'wpcoreuvigo'),
				'new_item_name'     => __('New Geographic Name', 'wpcoreuvigo'),
				'menu_name'         => __('Geographic', 'wpcoreuvigo'),
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
				'rewrite'            => array('slug' => 'xeografico'),
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
		$geographic_cpts = apply_filters('wpcoreuvigo_register_geographic_taxonomy_post_types', $geographic_cpts);

		foreach ($geographic_cpts as $cpt) {
			register_taxonomy_for_object_type(self::TAXONOMY_GEOGRAPHIC_NAME, $cpt);
		}
	}

	/**
	 * Register taxonomies terms
	 *
	 * @since    1.0.0
	 */
	public function register_taxonomies_terms()
	{

		// TAXONOMY_UNIVERSE_NAME
		if (taxonomy_exists(self::TAXONOMY_UNIVERSE_NAME)) {
			if (term_exists('Institucional', self::TAXONOMY_UNIVERSE_NAME) === null) {
				wp_insert_term('Institucional', self::TAXONOMY_UNIVERSE_NAME);
				wp_insert_term('Estudar', self::TAXONOMY_UNIVERSE_NAME);
				wp_insert_term('Investigar', self::TAXONOMY_UNIVERSE_NAME);
				wp_insert_term('No Campus', self::TAXONOMY_UNIVERSE_NAME);
				wp_insert_term('Ven a UVigo', self::TAXONOMY_UNIVERSE_NAME);
			}
		}

		// TAXONOMY_SPECTATOR_NAME
		if (taxonomy_exists(self::TAXONOMY_SPECTATOR_NAME)) {
			if (term_exists('Estudantes', self::TAXONOMY_SPECTATOR_NAME) === null) {
				wp_insert_term('Estudantes', self::TAXONOMY_SPECTATOR_NAME);
				wp_insert_term('PAS', self::TAXONOMY_SPECTATOR_NAME);
				wp_insert_term('PDI', self::TAXONOMY_SPECTATOR_NAME);
				wp_insert_term('Comunidade', self::TAXONOMY_SPECTATOR_NAME);
				wp_insert_term('Contratistas', self::TAXONOMY_SPECTATOR_NAME);
				wp_insert_term('Entidades Colaboradoras', self::TAXONOMY_SPECTATOR_NAME);
				wp_insert_term('Público externo', self::TAXONOMY_SPECTATOR_NAME);
				wp_insert_term('Medios', self::TAXONOMY_SPECTATOR_NAME);
			}
			// NOTA:  Creado a partir de la fuente de cursos : Futuro alumnado | Visitantes | Internacional | Alumni
			$spectators = array(
				'Estudantes'              => array('1', 'TXN.PO.ESTUDANTES'),
				'Futuro alumnado'         => array('2', 'TXN.PO.FUTURO_ALUMNADO'),
				'Internacional'           => array('3', 'TXN.PO.INTERNACIONAL'),
				'Alumni'                  => array('4', 'TXN.PO.ALUMNI'),
				'PAS'                     => array('5', 'TXN.PO.PAS'),
				'PDI'                     => array('6', 'TXN.PO.PDI'),
				'Contratistas'            => array('9', 'TXN.PO.CONTRATRISTAS'),
				'Entidades Colaboradoras' => array('10', 'TXN.PO.ENT_COLABORADORAS'),
				'Visitantes'              => array('11', 'TXN.PO.VISITANTES'),
				'Medios'                  => array('12', 'TXN.PO.MEDIOS'),
				'Comunidade'              => array('', ''),
				'Público externo'         => array('', ''),
			);

			foreach ($spectators as $d => $v) {
				$term = term_exists($d, self::TAXONOMY_SPECTATOR_NAME);
				if (!isset($term)) {
					$result  = wp_insert_term($d, self::TAXONOMY_SPECTATOR_NAME);
					$term_id = $result['term_id'];

					add_term_meta($term_id, 'uvigo_spectator_id', $v[0]);
					add_term_meta($term_id, 'uvigo_spectator_code', $v[1]);
				}
			}
		}

		// TAXONOMY_GEOGRAPHIC_NAME
		if (taxonomy_exists(self::TAXONOMY_GEOGRAPHIC_NAME)) {
			$geographics = array(
				'Ourense'    => array('1', 'TXN.AX.OU'),
				'Pontevedra' => array('2', 'TXN.AX.PO'),
				'Vigo'       => array('3', 'TXN.AX.VI'),
				'Outros'     => array('', ''),
			);

			foreach ($geographics as $d => $v) {
				$term = term_exists($d, self::TAXONOMY_GEOGRAPHIC_NAME);
				if (!isset($term)) {
					$result  = wp_insert_term($d, self::TAXONOMY_GEOGRAPHIC_NAME);
					$term_id = $result['term_id'];

					add_term_meta($term_id, 'uvigo_geographic_id', $v[0]);
					add_term_meta($term_id, 'uvigo_geographic_code', $v[1]);
				}
			}
		}
	}

	/**
	 * New CPT Documents
	 */

	/**
	 * Register the custom post type document
	 *
	 * @since    1.0.0
	 */
	public function register_document_post_type()
	{
		$labels = array(
			'name'               => _x('Documents', 'post type general name', 'wpcoreuvigo'),
			'singular_name'      => _x('Document', 'post type singular name', 'wpcoreuvigo'),
			'menu_name'          => _x('Documents', 'admin menu', 'wpcoreuvigo'),
			'name_admin_bar'     => _x('Documents', 'add new on admin bar', 'wpcoreuvigo'),
			'add_new'            => _x('Add new', 'Document', 'wpcoreuvigo'),
			'add_new_item'       => __('Add new document', 'wpcoreuvigo'),
			'new_item'           => __('New document', 'wpcoreuvigo'),
			'edit_item'          => __('Edit document', 'wpcoreuvigo'),
			'view_item'          => __('View document', 'wpcoreuvigo'),
			'all_items'          => __('All documents', 'wpcoreuvigo'),
			'search_items'       => __('Search documents', 'wpcoreuvigo'),
			'parent_item_colon'  => __('Parent document:', 'wpcoreuvigo'),
			'not_found'          => __('Documents not found.', 'wpcoreuvigo'),
			'not_found_in_trash' => __('Documents not found in trash.', 'wpcoreuvigo'),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __('Documents', 'wpcoreuvigo'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'documentos', 'with_front' => false, 'pages' => false, 'feeds' => false ),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array('title', 'excerpt', 'custom-fields', 'author'),
			'show_in_rest'       => true,
		);
		register_post_type(self::UV_DOCUMENT_POST_TYPE, $args);
	}


	/**
	 * Modifica la url de los tipos de contenido "Documento" para incluir la taxonomía de tipos.
	 *
	 * @param mixed $post_link
	 * @param mixed $post
	 * @param mixed $leavename
	 * @param mixed $sample
	 * @return mixed
	 */
	public function document_post_type_link( $post_link, $post, $leavename, $sample ) {

		if ( get_post_type( $post ) == self::UV_DOCUMENT_POST_TYPE ) {

			$terms_path = array();

			$term_id = get_field('uvigo_document_taxonomy', $post->ID);

			if ($term_id) {
				$term = get_term( $term_id, self::UV_TAXONOMY_DOCUMENT_TYPE_NAME );

				$terms_path[] = $term->slug;

				if ($term->parent) {
					$term = get_term( $term->parent, self::UV_TAXONOMY_DOCUMENT_TYPE_NAME );
					if ($term) {
						$terms_path[] = $term->slug;
					}
				}

				$terms_path = array_reverse( $terms_path );
			}

			if ( !empty( $terms_path ) ) {
				return str_replace( 'documentos/', 'documentos/' . implode('/', $terms_path) . '/', $post_link );
			}
		}

		return $post_link;
	}

	/**
	 * Genera las reglas de redirección para los documentos que tienen en la url los tipos de documentos
	 * -> Solamente para 2 niveles de jerarquía de categorías
	 *
	 * @return void
	 */
	public function document_rewrite_rules() {

		// Cuando el tipo de documento no tiene jerarquía
		add_rewrite_rule(
			'documentos/([^/]+)/([^/]+)/?$',
			'index.php?post_type=uvigo-document&uvigo-document=$matches[2]',
			'top'
		);

		// Cuando el tipo de documento es jerárquico (2 niveles)
		add_rewrite_rule(
			'documentos/[^/]+/[^/]+/([^/]+)/?$',
			'index.php?post_type=uvigo-document&uvigo-document=$matches[1]',
			'top'
		);
	}

	public function document_download_file() {

		if ( is_admin() ) {
			return;
		}

		if ( is_singular( self::UV_DOCUMENT_POST_TYPE ) ) {

			$post_id = get_the_ID();

			$origin_type = get_field( 'uvigo_document_origin_type', $post_id );

			if ($origin_type === 'file') {
				$file = get_field( 'uvigo_document_file', $post_id );

				if (!$file) {
					return;
				}

				$file_src = get_attached_file( $file['ID'] );

				if ( file_exists( $file_src ) ) {

					$filename = basename($file_src);
					$filetype = wp_check_filetype($file_src);

					header('Pragma: public'); // required
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Cache-Control: private', false); // required for certain browsers
					header('Content-Description: File Transfer');
					header('Content-Type: ' . $filetype['type']);
					header('Content-Disposition: inline; filename="' . $filename . '";');
					// header('Content-Transfer-Encoding: binary');
					header('Content-Length: ' . filesize($file_src));
					ob_clean();
					flush();
					readfile($file_src);

					wp_die();

				} else {
					status_header(404);
					global $wp_query;
					$wp_query->set_404();
					// wp_die();
				}
			}

			if ($origin_type === 'url') {
				$url = get_field( 'uvigo_document_url', $post_id );
				wp_redirect( $url );
				exit();
			}
		}
	}


	/**
	 * Register Document Type taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_document_type_taxonomy()
	{
		if (!taxonomy_exists(self::UV_TAXONOMY_DOCUMENT_TYPE_NAME)) {
			$labels = array(
				'name'              => _x('Documents Types', 'taxonomy general name', 'wpcoreuvigo'),
				'singular_name'     => _x('Document Type', 'taxonomy singular name', 'wpcoreuvigo'),
				'search_items'      => __('Search Document Type', 'wpcoreuvigo'),
				'all_items'         => __('All Documents Types', 'wpcoreuvigo'),
				'parent_item'       => __('Parent Document Type', 'wpcoreuvigo'),
				'parent_item_colon' => __('Parent Document Type:', 'wpcoreuvigo'),
				'edit_item'         => __('Edit Document Type', 'wpcoreuvigo'),
				'update_item'       => __('Update Document Type', 'wpcoreuvigo'),
				'add_new_item'      => __('Add New Document Type', 'wpcoreuvigo'),
				'new_item_name'     => __('New Document Type Name', 'wpcoreuvigo'),
				'menu_name'         => __('Document Type', 'wpcoreuvigo'),
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
				'rewrite'            => array( 'slug' => 'document-type', 'with_front' => false ),
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

	/**
	 * Register the custom post type act
	 *
	 * @since    1.0.0
	 */
	public function register_act_post_type()
	{
		$labels = array(
			'name'               => _x('Acts', 'post type general name', 'wpcoreuvigo'),
			'singular_name'      => _x('Act', 'post type singular name', 'wpcoreuvigo'),
			'menu_name'          => _x('Acts', 'admin menu', 'wpcoreuvigo'),
			'name_admin_bar'     => _x('Acts', 'add new on admin bar', 'wpcoreuvigo'),
			'add_new'            => _x('Add new', 'Act', 'wpcoreuvigo'),
			'add_new_item'       => __('Add new act', 'wpcoreuvigo'),
			'new_item'           => __('New act', 'wpcoreuvigo'),
			'edit_item'          => __('Edit act', 'wpcoreuvigo'),
			'view_item'          => __('View act', 'wpcoreuvigo'),
			'all_items'          => __('All acts', 'wpcoreuvigo'),
			'search_items'       => __('Search acts', 'wpcoreuvigo'),
			'parent_item_colon'  => __('Parent act:', 'wpcoreuvigo'),
			'not_found'          => __('Acts not found.', 'wpcoreuvigo'),
			'not_found_in_trash' => __('Acts not found in trash.', 'wpcoreuvigo'),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __('Acts', 'wpcoreuvigo'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'actas', 'with_front' => false ),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array('title', 'excerpt', 'custom-fields', 'author'),
			'show_in_rest'       => true,
		);
		register_post_type(self::UV_ACT_POST_TYPE, $args);
	}

	/**
	 * Register Act Type taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_act_type_taxonomy()
	{
		if (!taxonomy_exists(self::UV_TAXONOMY_ACT_TYPE_NAME)) {
			$labels = array(
				'name'              => _x('Acts Types', 'taxonomy general name', 'wpcoreuvigo'),
				'singular_name'     => _x('Act Type', 'taxonomy singular name', 'wpcoreuvigo'),
				'search_items'      => __('Search Act Type', 'wpcoreuvigo'),
				'all_items'         => __('All Acts Types', 'wpcoreuvigo'),
				'parent_item'       => __('Parent Act Type', 'wpcoreuvigo'),
				'parent_item_colon' => __('Parent Act Type:', 'wpcoreuvigo'),
				'edit_item'         => __('Edit Act Type', 'wpcoreuvigo'),
				'update_item'       => __('Update Act Type', 'wpcoreuvigo'),
				'add_new_item'      => __('Add New Act Type', 'wpcoreuvigo'),
				'new_item_name'     => __('New Act Type Name', 'wpcoreuvigo'),
				'menu_name'         => __('Act Type', 'wpcoreuvigo'),
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
				'rewrite'            => array('slug' => 'act-type'),
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

	/**
	 * Register the custom post type form
	 *
	 * @since    1.0.0
	 */
	public function register_form_post_type()
	{
		$labels = array(
			'name'               => _x('Forms', 'post type general name', 'wpcoreuvigo'),
			'singular_name'      => _x('Form', 'post type singular name', 'wpcoreuvigo'),
			'menu_name'          => _x('Forms', 'admin menu', 'wpcoreuvigo'),
			'name_admin_bar'     => _x('Forms', 'add new on admin bar', 'wpcoreuvigo'),
			'add_new'            => _x('Add new', 'Form', 'wpcoreuvigo'),
			'add_new_item'       => __('Add new form', 'wpcoreuvigo'),
			'new_item'           => __('New form', 'wpcoreuvigo'),
			'edit_item'          => __('Edit form', 'wpcoreuvigo'),
			'view_item'          => __('View form', 'wpcoreuvigo'),
			'all_items'          => __('All forms', 'wpcoreuvigo'),
			'search_items'       => __('Search forms', 'wpcoreuvigo'),
			'parent_item_colon'  => __('Parent form:', 'wpcoreuvigo'),
			'not_found'          => __('Forms not found.', 'wpcoreuvigo'),
			'not_found_in_trash' => __('Forms not found in trash.', 'wpcoreuvigo'),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __('Forms', 'wpcoreuvigo'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array('slug' => 'formularios', 'with_front' => false),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array('title', 'excerpt', 'custom-fields', 'page-attributes', 'author'),
			'show_in_rest'       => true,
		);
		register_post_type(self::UV_FORM_POST_TYPE, $args);
	}

	/**
	 * Register Form Type taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_form_type_taxonomy()
	{
		if (!taxonomy_exists(self::UV_TAXONOMY_FORM_TYPE_NAME)) {
			$labels = array(
				'name'              => _x('Forms Types', 'taxonomy general name', 'wpcoreuvigo'),
				'singular_name'     => _x('Form Type', 'taxonomy singular name', 'wpcoreuvigo'),
				'search_items'      => __('Search Form Type', 'wpcoreuvigo'),
				'all_items'         => __('All Forms Types', 'wpcoreuvigo'),
				'parent_item'       => __('Parent Form Type', 'wpcoreuvigo'),
				'parent_item_colon' => __('Parent Form Type:', 'wpcoreuvigo'),
				'edit_item'         => __('Edit Form Type', 'wpcoreuvigo'),
				'update_item'       => __('Update Form Type', 'wpcoreuvigo'),
				'add_new_item'      => __('Add New Form Type', 'wpcoreuvigo'),
				'new_item_name'     => __('New Form Type Name', 'wpcoreuvigo'),
				'menu_name'         => __('Form Type', 'wpcoreuvigo'),
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
				'rewrite'            => array('slug' => 'form-type'),
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
	 * Register the custom post type document
	 *
	 * @since    1.0.0
	 */
	public function register_milestone_post_type()
	{
		$labels = array(
			'name'               => _x('Milestones', 'post type general name', 'wpcoreuvigo'),
			'singular_name'      => _x('Milestone', 'post type singular name', 'wpcoreuvigo'),
			'menu_name'          => _x('Milestones', 'admin menu', 'wpcoreuvigo'),
			'name_admin_bar'     => _x('Milestones', 'add new on admin bar', 'wpcoreuvigo'),
			'add_new'            => _x('Add new', 'Milestone', 'wpcoreuvigo'),
			'add_new_item'       => __('Add new milestone', 'wpcoreuvigo'),
			'new_item'           => __('New milestone', 'wpcoreuvigo'),
			'edit_item'          => __('Edit milestone', 'wpcoreuvigo'),
			'view_item'          => __('View milestone', 'wpcoreuvigo'),
			'all_items'          => __('All milestones', 'wpcoreuvigo'),
			'search_items'       => __('Search milestones', 'wpcoreuvigo'),
			'parent_item_colon'  => __('Parent milestone:', 'wpcoreuvigo'),
			'not_found'          => __('Milestones not found.', 'wpcoreuvigo'),
			'not_found_in_trash' => __('Milestones not found in trash.', 'wpcoreuvigo'),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __('Milestones', 'wpcoreuvigo'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array('slug' => 'fitos', 'with_front' => false),
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-admin-site-alt3',
			'show_in_rest'       => true,
			'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'author'),
		);

		register_post_type(self::UV_POST_TYPE_MILESTONE, $args);
	}

	/**
	 * Register Form Type taxonomy.
	 *
	 * @since    1.0.0
	 */
	public function register_milestone_category_taxonomy()
	{
		if (!taxonomy_exists(self::UV_TAXONOMY_MILESTONE_CATEGORY)) {
			$labels = array(
				'name'              => _x('Milestone category', 'taxonomy general name', 'wpcoreuvigo'),
				'singular_name'     => _x('Milestone category', 'taxonomy singular name', 'wpcoreuvigo'),
				'search_items'      => __('Search milestone category', 'wpcoreuvigo'),
				'all_items'         => __('All milestone category', 'wpcoreuvigo'),
				'parent_item'       => __('Parent milestone category', 'wpcoreuvigo'),
				'parent_item_colon' => __('Parent milestone category:', 'wpcoreuvigo'),
				'edit_item'         => __('Edit milestone category', 'wpcoreuvigo'),
				'update_item'       => __('Update milestone category', 'wpcoreuvigo'),
				'add_new_item'      => __('Add New milestone category', 'wpcoreuvigo'),
				'new_item_name'     => __('New milestone category Name', 'wpcoreuvigo'),
				'menu_name'         => __('Milestone category', 'wpcoreuvigo'),
			);

			$args = array(
				'hierarchical'       => true,
				'labels'             => $labels,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_admin_column'  => true,
				'show_in_quick_edit' => true,
				'meta_box_cb'        => false,
				'show_in_rest'       => true,
				'query_var'          => 'milestone-category',
				'rewrite'            => array('slug' => 'milestone-category'),
			);

			$ob = register_taxonomy(
				self::UV_TAXONOMY_MILESTONE_CATEGORY,
				array(
					self::UV_POST_TYPE_MILESTONE,
				),
				$args
			);
		}
	}

	/**
	 * ACF
	 *
	 * Add a Act, Document, Forms field group
	 *
	 * @return void
	 */
	function wpcoreuvigo_acf_add_local_field_groups()
	{
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
					'default_value' => array(),
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
				array(
					'key' => 'field_5d8507b8ea6c1',
					'label' => 'Url',
					'name' => 'uvigo_form_url',
					'type' => 'url',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => ''
					),
					'wpml_cf_preferences' => 0,
					'default_value' => '',
					'placeholder' => ''
				)
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

		//Tipo Taxonomia Formulario
		acf_add_local_field_group(array(
			array(
				'key' => 'group_5d8503137dbe1',
				'title' => 'Taxonomia Tipos de Formularios',
				'fields' => array(
					array(
						'key' => 'field_5d85035589ea2',
						'label' => 'Orden',
						'name' => 'uvigo_tax_form_order',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => ''
						),
						'wpml_cf_preferences' => 0,
						'default_value' => 0,
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => 0,
						'max' => 100,
						'step' => ''
					)
				),
				'location' => array(
					array(
						array(
							'param' => 'taxonomy',
							'operator' => '==',
							'value' => 'uvigo-tax-form'
						)
					)
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => ''
			)
		));

		// Campos para mensajes durante el desarrollo
		acf_add_local_field_group(array(
			'key' => 'group_5c5dcb2fb102d',
			'title' => 'Desarrollo',
			'fields' => array(
				array(
					'key' => 'field_5c5dcb40373b1',
					'label' => 'Revisión',
					'name' => 'dev_revision',
					'type' => 'wysiwyg',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'wpml_cf_preferences' => 0,
					'default_value' => '',
					'tabs' => 'all',
					'toolbar' => 'basic',
					'media_upload' => 0,
					'delay' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'page',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'side',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
		));
	}
}
