<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       info@ideit.es
 * @since      1.0.0
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/public
 * @author     IdeiT <info@ideit.es>
 */
class Wpcoreuvigo_Public {

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
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name . '-public', wpcoreuvigo_public_asset_path( 'styles/main.css' ), false, null );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name . '-public', wpcoreuvigo_public_asset_path( 'scripts/main.js' ), array( 'jquery' ), null, true );
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $atts
	 * @param [type] $item
	 * @param [type] $args
	 * @param [type] $depth
	 * @return void
	 */
	public function nav_menu_link_attributes( $atts, $item, $args, $depth ) {
		$value = get_post_meta( $item->ID, 'menu-item-openchild', true );
		if ( $value ) {
			$items  = wp_get_nav_menu_items( $args->menu->term_id );
			$childs = $this->get_nav_menu_item_children( $item->ID, $items, false );
			if ( count( $childs ) > 0 ) {
				$atts['href'] = $childs[0]->url;
			}
		}

		return $atts;
	}

	/**
	* Returns all child nav_menu_items under a specific parent
	*
	* @param int the parent nav_menu_item ID
	* @param array nav_menu_items
	* @param bool gives all children or direct children only
	* @return array returns filtered array of nav_menu_items
	*/
	public function get_nav_menu_item_children( $parent_id, $nav_menu_items, $depth = true ) {
		$nav_menu_item_list = array();
		foreach ( (array) $nav_menu_items as $nav_menu_item ) {
			if ( intval( $nav_menu_item->menu_item_parent ) === intval( $parent_id ) ) {
				$nav_menu_item_list[] = $nav_menu_item;
				if ( $depth ) {
					$children = $this->get_nav_menu_item_children( $nav_menu_item->ID, $nav_menu_items );
					if ( $children ) {
						$nav_menu_item_list = array_merge( $nav_menu_item_list, $children );
					}
				}
			}
		}

		return $nav_menu_item_list;
	}

	/**
	 * Check if page has to redirect to first child and doit.
	 *
	 * @return void
	 */
	public function page_to_first_child_template_redirect() {

		if ( is_admin() ) {
			return;
		}

		if ( is_page() && ! is_user_logged_in() ) {
			$page_id     = get_queried_object_id();
			$do_redirect = get_post_meta( $page_id, 'uvigo_page_redirect_child', true );
			if ( $do_redirect ) {
				$pages = get_children([
					'posts_per_page' => 1,
					'order'          => 'ASC',
					'post_parent'    => $page_id,
					'post_type'      => 'page',
				]);

				if ( $pages ) {
					$url = get_permalink( current( $pages )->ID );
					if ( wp_safe_redirect( $url, 301 ) ) {
						exit();
					}
				}
			}
		}
	}

	/**
	 * Filter to hide or show thumbnail in 'has_post_thumbnail'
	 *
	 * @param [type] $has_thumbnail
	 * @param [type] $post
	 * @param [type] $thumbnail_id
	 * @return void
	 */
	public function hide_post_thumbnail( $has_thumbnail, $post, $thumbnail_id ) {
		if ( is_single() || is_page() ) {
			if ( $has_thumbnail ) {
				$post = get_post( $post );
				if ( ! $post ) {
					return $has_thumbnail;
				}
				$uvigo_hide_thumbnail_in_single = (bool) get_post_meta( $post->ID, 'uvigo_hide_thumbnail_in_single', true );
				return ! $uvigo_hide_thumbnail_in_single;
			}
		}

		return $has_thumbnail;
	}

}
