<?php
/**
 * The public-facing functionality shortcodes of the plugin.
 *
 * @link       info@ideit.es
 * @since      1.0.0
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/public
 */

/**
 * The public-facing functionality shortcodes of the plugin.
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/public
 * @author     IdeiT <info@ideit.es>
 */
class Wpcoreuvigo_Public_Blocks {
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Registers all gutenberg blocks at once
	 *
	 * @return [type] [description]
	 */
	public function register_blocks() {

		if (function_exists('acf_register_block_type')) {

			// Bloque de listaxe de actualidade

			acf_register_block_type(array(
				'name'              => 'milestone-list',
				'title'             => __('Milestone list', 'wpcoreuvigo'),
				'description'       => __('A block for milestone list.', 'wpcoreuvigo'),
				'render_callback'   => array( $this, 'milestone_list_block_render_callback' ),
				'category'          => 'theme',
				'icon'              => 'admin-site-alt3',
				'keywords'          => array( 'milestone' ),
			));


		}
	}

	public function milestone_list_block_render_callback($block, $content = '', $is_preview = false, $post_id = 0) {

		$id = 'milestone-list-' . $block['id'];
		if ( !empty($block['anchor']) ) {
			$id = $block['anchor'];
		}

		$classname = 'milestone-list';
		if ( !empty($block['className']) ) {
			$classname .= ' ' . $block['className'];
		}

		$categories = get_field('milestone_category');
		$show_filter = get_field('milestone_filter_show');
		$milestone_maxitems = get_field('milestone_maxitems');
		$milestone_categories_selected = [];

		// Para los elementos
		$items = array();

		// Obtenemos hitos
		$query_args = array(
			'post_type'	     => Wpcoreuvigo_Data::UV_POST_TYPE_MILESTONE,
			// 'orderby'       => 'date',
			// 'order'         => 'DESC',
			'meta_key'       => 'milestone_date',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'posts_per_page' => $milestone_maxitems ? $milestone_maxitems : -1,
		);

		if (!empty($categories)) {
			$show_filter = false;
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => Wpcoreuvigo_Data::UV_TAXONOMY_MILESTONE_CATEGORY,
					'field'    => 'term_id',
					'terms'    => $categories,
				),
			);
		}

		if ($show_filter) {
			if (!empty($_POST['milestone_category'])){
				$milestone_categories_selected = $_POST['milestone_category'];
				write_log($milestone_categories_selected);
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => Wpcoreuvigo_Data::UV_TAXONOMY_MILESTONE_CATEGORY,
						'field'    => 'term_id',
						'terms'    => $milestone_categories_selected,
					),
				);
			}
		}

		$items = get_posts( $query_args );

		// Agrupamos en años
		$milestones_grouped = [];
		foreach ($items as $milestone) {
			$milestone_date = get_field('milestone_date', $milestone->ID);
			$year = 'empty';
			if ($milestone_date) {
				$year = date('Y', strtotime($milestone_date));
			}
			$milestones_grouped[$year][] = $milestone;
		}

		$data = array(
			'milestones'         => $items,
			'milestones_grouped' => $milestones_grouped,
			'show_filter'        => $show_filter,
			'id'                 => $id,
			'classname'          => $classname,
			'milestone_maxitems' => $milestone_maxitems,
		);

		if ($show_filter) {
			$data['categories'] = get_terms( array(
				'taxonomy' => Wpcoreuvigo_Data::UV_TAXONOMY_MILESTONE_CATEGORY,
				'hide_empty' => true,
			) );
			$data['milestone_categories_selected'] = $milestone_categories_selected;
		}

		$template = \App\locate_template('blocks/milestone-list');

		$output = '';

		if ($template) {
			$output = \App\template($template, $data);
		} else {
			$output = __('No template found', 'wpcoreuvigo');
		}

		echo $output;
	}

}
