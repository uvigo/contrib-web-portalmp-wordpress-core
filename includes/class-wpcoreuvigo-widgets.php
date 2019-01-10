<?php
/**
 * Define Widgets
 *
 * @link       info@ideit.es
 * @since      1.0.0
 *
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/includes
 */

/**
 * Define Widgets
 *
 * @since      1.0.0
 * @package    Wpcoreuvigo
 * @subpackage Wpcoreuvigo/includes
 * @author     IdeiT <info@ideit.es>
 */
class Wpcoreuvigo_Filter_Widget extends WP_Widget {

	const DATE_FORMAT             = 'd/m/Y';
	const F_KEYWORDS_FIELD_NAME   = 'f_keywords';
	const F_CATEGORIES_FIELD_NAME = 'f_categories';
	const F_SDATE_FIELD_NAME      = 'f_sdate';
	const F_EDATE_FIELD_NAME      = 'f_edate';
	const F_TYPE_FIELD_NAME       = 'f_type';

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct(
			'wpcoreuvigo_filter_widget', // Base ID
			esc_html__( 'Wpcoreuvigo Widget Filter ', 'wpcoreuvigo' ), // Name
			[
				'description'                 => esc_html__( 'Filter posts', 'wpcoreuvigo' ),
				'classname'                   => 'widget-filter',
				'customize_selective_refresh' => true,
			]
		);
	}

	/**
	 * Retrieves a list of registered taxonomy objects.
	 *
	 * @return array
	 */
	public function list_taxonomies() {
		return get_taxonomies( [ 'public' => true ], 'objects' );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$blog_url = get_option( 'page_for_posts' );
		$blog_url = get_permalink( $blog_url );

		// Filtro Fechas
		$dates = ! empty( $instance['dates'] ) ? $instance['dates'] : false;
		if ( $dates ) {
			$now      = new DateTime();
			$end_date = $now->format( self::DATE_FORMAT );
			$now->modify( '-12 month' );
			$start_date = $now->format( self::DATE_FORMAT );

			$f_text  = get_query_var( self::F_KEYWORDS_FIELD_NAME, '' );
			$f_sdate = get_query_var( self::F_SDATE_FIELD_NAME, '' );
			$f_edate = get_query_var( self::F_EDATE_FIELD_NAME, '' );
			$f_type  = get_query_var( self::F_TYPE_FIELD_NAME, '' );
		}
		// Filtro Taxonomias
		$taxonomies = $instance['taxonomies'];
		?>
		<form action="<?php echo esc_url( $blog_url ); ?>" class="widget-filter__form" method="get">
			<div class="widget-filter__search">
				<input type="search" class="form-control" placeholder="<?php echo _x( 'Insert text...', 'Widget filter news: search input', 'wpcoreuvigo' ); ?>" value="<?php echo $f_text; ?>" name="<?php echo esc_attr( self::F_KEYWORDS_FIELD_NAME ); ?>">
				<button type="submit" class="btn" data-icon="U"><span class="sr-only"><?php esc_html_e( 'Search', 'wpcoreuvigo' ); ?></span></button>
			</div>
			<?php if ( $dates || $taxonomies ) : ?>
				<h3 class="mt-8 mb-2"><?php echo esc_html_x( 'Filter', 'Widget filter news: filter titles', 'wpcoreuvigo' ); ?></h3>
				<div class="widget-filter__title"><?php echo esc_html_x( 'Apply filters', 'Widget filter news: filter help', 'wpcoreuvigo' ); ?></div>
			<?php endif; ?>
			<?php do_action( 'wpcoreuvigo_filter_widget_before_filters' ); ?>
			<?php if ( $dates ) : ?>
				<div class="widget-filter__block">
					<div class="widget-filter__block__title" data-icon="3"><?php esc_html_e( 'Dates', 'wpcoreuvigo' ); ?></div>
					<div id="filterDates" class="widget-filter__content widget-filter__dates">
						<div class="form-group">
							<span class="widget-filter__dates__icon icon_calendar"></span>
							<label for="f_sdate"><?php echo esc_html_x( 'From:', 'Widget filter news: date', 'wpcoreuvigo' ); ?></label>
							<input type="text" class="form-control" pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}" placeholder="<?php echo $start_date; ?>" value="<?php echo $f_sdate; ?>" name="<?php echo self::F_SDATE_FIELD_NAME; ?>">
						</div>
						<div class="form-group">
							<span class="widget-filter__dates__icon icon_calendar"></span>
							<label for="f_edate"><?php echo esc_html_x( 'To:', 'Widget filter news: date', 'wpcoreuvigo' ); ?></label>
							<input type="text" class="form-control" pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}" placeholder="<?php echo $end_date; ?>" value="<?php echo $f_edate; ?>" name="<?php echo self::F_EDATE_FIELD_NAME; ?>">
						</div>
						<div class="form-group mt-2">
							<button type="submit" style="min-width: 10px;" class="btn btn-outline-primary btn-icon btn-sm">Aplicar</button>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php
			if ( $taxonomies ) {
				$wp_taxonomies = $this->list_taxonomies();
				foreach ( $wp_taxonomies as $wp_taxonomy ) {
					if ( in_array( $wp_taxonomy->name, $taxonomies ) ) {
						$terms              = get_terms(
							[
								'taxonomy'   => $wp_taxonomy->name,
								'hide_empty' => false,
							]
						);
						$query_var_tax_name = $wp_taxonomy->query_var;
						if ( 'category_name' === $query_var_tax_name ) {
							$query_var_tax_name = self::F_CATEGORIES_FIELD_NAME;
						}
						$query_var_array_tax_selected = get_query_var( $query_var_tax_name, array() );
						?>
						<div class="widget-filter__block">
							<div class="widget-filter__block__title" data-icon="3"><?php echo esc_html( $wp_taxonomy->labels->name ); ?></div>
							<div id="<?php echo esc_attr( $wp_taxonomy->name ); ?>" class="widget-filter__content widget-filter__checkbox widget-filter__taxonomy">
								<?php foreach ( $terms  as $term ) : ?>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="<?php echo esc_attr( $query_var_tax_name ); ?>[]"
											value="<?php echo esc_html( $term->slug ); ?>"
											<?php echo ( in_array( $term->slug, $query_var_array_tax_selected ) ? 'checked' : '' ); ?>
											id="<?php echo esc_attr( $term->term_id ); ?>">
										<label class="form-check-label" for="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></label>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
						<?php
					}
				}
			}
			?>
			<?php do_action( 'wpcoreuvigo_filter_widget_after_filters' ); ?>
		</form>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title      = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$dates      = ! empty( $instance['dates'] ) ? $instance['dates'] : false;
		$taxonomies = ! empty( $instance['taxonomies'] ) ? $instance['taxonomies'] : array();
		// Taxonomias asociadas a posts
		$wp_taxonomies = $this->list_taxonomies();
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'wpcoreuvigo' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<h4><?php esc_html_e( 'Configuration', 'wpcoreuvigo' ); ?></h4>
		<p>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'dates' ) ); ?>"
					value="<?php echo ( $dates ? 'true' : 'false' ); ?>" <?php echo ( $dates ? 'checked' : '' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'dates' ) ); ?>">
				<label class="form-check-input" for="<?php echo esc_attr( $this->get_field_id( 'dates' ) ); ?>"><?php esc_attr_e( 'Search by dates', 'wpcoreuvigo' ); ?></label>
			</div>
		</p>
		<h4><?php esc_html_e( 'Taxonomies used', 'wpcoreuvigo' ); ?></h4>
		<p>
		<?php
		if ( $wp_taxonomies ) {
			foreach ( $wp_taxonomies  as $taxonomy ) {
				if ( in_array( 'post', $taxonomy->object_type ) ) {
					if ( ! in_array( $taxonomy->name, array( 'post_format', 'post_tag' ) ) ) {
						$checked = in_array( $taxonomy->name, $taxonomies );
						?>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'taxonomies' ) ); ?>[]"
								value="<?php echo esc_attr( $taxonomy->name ); ?>" <?php checked( $checked, true ); ?> id="<?php echo esc_attr( $taxonomy->name ); ?>">
							<label class="form-check-label" for="<?php echo esc_attr( $taxonomy->name ); ?>">
								<?php echo esc_html( $taxonomy->labels->name ); ?>
							</label>
						</div>
						<?php
					}
				}
			}
		}
		?>
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance               = [];
		$instance['title']      = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['dates']      = ( ! empty( $new_instance['dates'] ) ) ? boolval( $new_instance['dates'] ) : false;
		$instance['taxonomies'] = ( ! empty( $new_instance['taxonomies'] ) ) ? $new_instance['taxonomies'] : array();

		return $instance;
	}

	/**
	 * Add query_vars
	 *
	 * @param [type] $vars
	 * @return array
	 */
	public static function query_vars_filter( $vars ) {
		$vars[] = self::F_KEYWORDS_FIELD_NAME;
		$vars[] = self::F_SDATE_FIELD_NAME;
		$vars[] = self::F_EDATE_FIELD_NAME;
		$vars[] = self::F_CATEGORIES_FIELD_NAME;
		$vars[] = self::F_TYPE_FIELD_NAME;

		return $vars;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $query
	 * @return void
	 */
	public static function pre_get_posts( $query ) {

		if ( is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_home() ) {
			write_log('pre_get_posts ------> WIDGET FILTER');

			$f_categories = get_query_var( self::F_CATEGORIES_FIELD_NAME );
			if ( ! empty( $f_categories ) ) {
				if ( is_array( $f_categories ) ) {
					$elements = implode( ',', $f_categories );
					set_query_var( 'category_name', $elements );
				}
			}

			$f_sdate = get_query_var( self::F_SDATE_FIELD_NAME );
			$f_edate = get_query_var( self::F_EDATE_FIELD_NAME );

			$date_query = [];

			// add meta_query elements
			if ( ! empty( $f_sdate ) ) {
				$fecha = DateTime::createFromFormat( self::DATE_FORMAT, $f_sdate );
				if ( $fecha ) {
					$date_query['after'] = array(
						'year'  => $fecha->format( 'Y' ),
						'month' => $fecha->format( 'm' ),
						'day'   => $fecha->format( 'd' ),
					);
				}
			}
			if ( ! empty( $f_edate ) ) {
				$fecha = DateTime::createFromFormat( self::DATE_FORMAT, $f_edate );
				if ( $fecha ) {
					$date_query['before'] = array(
						'year'  => $fecha->format( 'Y' ),
						'month' => $fecha->format( 'm' ),
						'day'   => $fecha->format( 'd' ),
					);
				}
			}
			if ( ! empty( $f_sdate ) || ! empty( $f_edate ) ) {
				$date_query['inclusive'] = true;
			}
			$query->set( 'date_query', array( $date_query ) );

			$f_keywords = get_query_var( self::F_KEYWORDS_FIELD_NAME );

			if ( ! empty( $f_keywords ) ) {
				$query->set( 's', $f_keywords );
			}

			$f_type = get_query_var( self::F_TYPE_FIELD_NAME );

			if ( ! empty( $f_type ) ) {
				if ( is_array( $f_type ) ) {
					$query->set( 'post_type', $f_type );
				} else {
					$query->set( 'post_type', array( $f_type ) );
				}
			}
		}
	}

}
