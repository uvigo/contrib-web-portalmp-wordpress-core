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
class Wpcoreuvigo_Public_Shortcodes {
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
	 * Registers all shortcodes at once
	 *
	 * @return [type] [description]
	 */
	public function register_shortcodes() {
		add_shortcode( 'uvigo_floor', array( $this, 'uvigo_floor_block' ) );
		add_shortcode( 'uvigo_floor_image', array( $this, 'uvigo_floor_image_block' ) );
		add_shortcode( 'uvigo_bloque', array( $this, 'uvigo_content_block' ) );
	}

	/**
	 * Shortcode "uvigo_floor"
	 *
	 * @param  [type] $atts    [description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function uvigo_floor_block( $atts, $content = null ) {
		// style
		$defaults['style'] = '';
		// layout
		$defaults['layout'] = '';
		// image
		$defaults['image'] = '';
		$defaults['image_id'] = '';
		// icon
		$defaults['icon'] = '';
		// classname
		$defaults['classname'] = '';
		// xs
		$defaults['xs'] = '12';
		// sm
		$defaults['sm'] = '12';
		// md
		$defaults['md'] = '12';
		// lg
		$defaults['lg'] = '8';

		// contenido
		$defaults['c_title']     = '';
		$defaults['c_linkurl']   = '';
		$defaults['c_linktitle'] = 'Ir';

		$args_shortcode = shortcode_atts( $defaults, $atts, 'uvigo_floor' );

		$in_layout   = ( strpos( $args_shortcode['layout'], 'floor__layout--columns' ) !== false ) ? 1 : 0;
		$image_right = ( strpos( $args_shortcode['layout'], 'floor__layout--imageright' ) !== false ) ? 1 : 0;

		$col_xs = intval( $args_shortcode['xs'] );
		$col_sm = intval( $args_shortcode['sm'] );
		$col_md = intval( $args_shortcode['md'] );

		$offset_md = '';
		if ( $col_md < 12 ) {
			$offset_md = 'col-md-offset-' . ( 12 - $col_md ) / 2;
		}
		$offset_lg = '';

		$col_lg = intval( $args_shortcode['lg'] );
		if ( $col_lg < 12 ) {
			$offset_lg = 'col-lg-offset-' . ( 12 - $col_lg ) / 2;
		}

		$output  = '';
		$output .= '<div class="floor ' . $args_shortcode['style'] . ' ' . $args_shortcode['layout'] . ' ' . $args_shortcode['classname'] . '"';
		$output .= '>';

		if ($in_layout) {

			// Image
			// TODO: Use $args_shortcode['image_id']
			$image = '';
			if ( ! empty( $args_shortcode['image'] ) ) {
				$image .= '<div class="floor__iconimage">';
				$image .= '<img src="' . $args_shortcode['image'] . '" alt="">';
				$image .= '</div>';
			}
			// Icon
			$icon = '';
			if ( isset( $args_shortcode['icon'] ) && $args_shortcode['icon'] ) {
				$icon .= '<div class="floor__icon uvigo-iconfont ' . $args_shortcode['icon'] . '"></div>';
			}
			// Content
			$text_content = '<h2 class="floor_element_title">' . $args_shortcode['c_title'] . '</h2>';
			$text_content .= '<div class="floor_element_text">' . apply_filters( 'the_content', do_shortcode( $content ) ) . '</div>';
			if ( isset( $args_shortcode['c_linkurl'] ) && $args_shortcode['c_linkurl'] ) {
				$text_content .= '<p><a class="btn floor_element_link" href="' . $args_shortcode['c_linkurl'] . '">' . $args_shortcode['c_linktitle'] . '</a></p>';
			}

			$output .= '<div class="floor__text container">';
			$output .= '<div class="row">';
			$output .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 ' . ( $image_right ? 'order-lg-last' : '' ) . '">';

			$output .= $image;

			$output .= '</div>';
			$output .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">';

			$output .= $icon;
			$output .= $text_content;

			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';

		} else {

			// Init container
			$output .= '<div class="floor__text container">';
			$output .= '<div class="row">';
			// $output .= '<div class="col-11 col-xs-' . $col_xs . ' col-sm-' . $col_sm . ' col-md-' . $col_md . ' col-lg-' . $col_lg . '">';
			$output .= '<div class="col-11 col-sm-12 col-md-10 col-lg-8">';

			// Icon
			if ( isset( $args_shortcode['icon'] ) && $args_shortcode['icon'] ) {
				$output .= '<div class="floor__icon uvigo-iconfont ' . $args_shortcode['icon'] . '"></div>';
			}

			// Image
			// TODO: Use $args_shortcode['image_id']
			if ( ! empty( $args_shortcode['image'] ) ) {
				$output .= '<div class="floor__iconimage">';
				$output .= '<img src="' . $args_shortcode['image'] . '" width="200" alt="">';
				$output .= '</div>';
			}

			// Content
			$output .= '<h2 class="floor_element_title">' . $args_shortcode['c_title'] . '</h2>';
			$output .= '<div class="floor_element_text">' . apply_filters( 'the_content', do_shortcode( $content ) ) . '</div>';
			if ( isset( $args_shortcode['c_linkurl'] ) && $args_shortcode['c_linkurl'] ) {
				$output .= '<p><a class="btn floor_element_link" href="' . $args_shortcode['c_linkurl'] . '">' . $args_shortcode['c_linktitle'] . '</a></p>';
			}

			// End container
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Shortcode "uvigo_floor_image"
	 *
	 * @param  [type] $atts    [description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function uvigo_floor_image_block( $atts, $content = null ) {
		// icon
		$defaults['icon'] = '';
		// image
		$defaults['image'] = '';
		$defaults['image_id'] = '';
		// classname
		$defaults['classname'] = '';
		// xs
		$defaults['xs'] = '12';
		// sm
		$defaults['sm'] = '12';
		// md
		$defaults['md'] = '8';
		// lg
		$defaults['lg'] = '8';

		// contenido
		$defaults['c_title']     = '';
		$defaults['c_subtitle']  = '';
		$defaults['c_linkurl']   = '';
		$defaults['c_linktitle'] = 'Ir';

		$args_shortcode = shortcode_atts( $defaults, $atts, 'uvigo_floor_image' );

		$is_fixedheight = true;

		$col_xs = intval( $args_shortcode['xs'] );
		$col_sm = intval( $args_shortcode['sm'] );
		$col_md = intval( $args_shortcode['md'] );

		$offset_md = '';
		if ( $col_md < 12 ) {
			$offset_md = 'col-md-offset-' . ( 12 - $col_md ) / 2;
		}
		$offset_lg = '';

		$col_lg = intval( $args_shortcode['lg'] );
		if ( $col_lg < 12 ) {
			$offset_lg = 'col-lg-offset-' . ( 12 - $col_lg ) / 2;
		}

		$output = '';
		$output .= '<div class="floor floor-header ' . $args_shortcode['classname'] . '"';

		if ( $is_fixedheight ) {
			$output .= ' data-width="1200" data-height="400"';
		}

		$output .= '>';

		// Image
		// TODO: Use $args_shortcode['image_id']
		$output .= '<div class="floor__image"';
		if ( ! empty( $args_shortcode['image'] ) ) {
			$output .= ' style="background-image: url(\'' . $args_shortcode['image'] . '\');"';
		}
		$output .= '>';
		$output .= '<div class="floor__image__text">';
		$output .= '<div class="container">';
		$output .= '<div class="row">';
		$output .= '<div class="col-12">';

		if ( isset( $args_shortcode['icon'] ) ) {
			$output .= '<div class="floor__icon uvigo-iconfont uvigo-iconfont-5x ' . $args_shortcode['icon'] . '"></div>';
		}

		$output .= '<h1>' . $args_shortcode['c_title'] . '</h1>';
		$output .= '<h2>' . $args_shortcode['c_subtitle'] . '</h2>';

		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		// Init Content
		$output .= '<div class="floor__text">';
		$output .= '<div class="container">';
		$output .= '<div class="row">';
		$output .= '<div class="col-11 col-sm-12 col-md-8">';

		// Content
		$output .= do_shortcode( $content );

		if ( isset( $args_shortcode['c_linkurl'] ) && $args_shortcode['c_linkurl'] ) {
			$output .= '<p><a class="btn floor_element_link" href="' . $args_shortcode['c_linkurl'] . '">' . $args_shortcode['c_linktitle'] . '</a></p>';
		}

		// End Content
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Undocumented function
	 *
	 * @param [array] $atts Atributos
	 * @param [string] $content Contidos
	 * @return [string] Html output
	 */
	public function uvigo_content_block( $atts, $content = null ) {

		// estilo
		$defaults['estilo'] = '';
		// color
		$defaults['color'] = '';
		// classname
		$defaults['classname'] = '';

		$args_shortcode = shortcode_atts( $defaults, $atts, 'uvigo_bloque' );

		$output = '';

		$output = '<div class="custom-block ' . $args_shortcode['color'] . ' ' . $args_shortcode['estilo'] . ' ' . $args_shortcode['classname'] . '">';
		if ( 'table-of-content' === $args_shortcode['estilo'] ) {
			$output .= '<p class="title-index">' . __( 'Index', 'wpcoreuvigo' ) . '</p>';
		}
		$output .= do_shortcode( $content );
		$output .= '</div>';

		return $output;
	}

}