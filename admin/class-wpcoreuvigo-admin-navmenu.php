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
class Wpcoreuvigo_Admin_Navmenu {

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
	 * Undocumented variable
	 *
	 * @var array
	 */
	private $icons = [
		'icon_search',
		'icon_profile',
	];

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
			'icon' => array(
				'type'  => 'select',
				'label' => __( 'Icon item', 'wpcoreuvigo' ),
				'value' => 'parent',
			),
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $walker
	 * @return void
	 */
	public function wp_edit_nav_menu_walker( $walker ) {
		$walker = 'Menu_Item_Custom_Fields_Walker';
		if ( ! class_exists( $walker ) ) {
			// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcoreuvigo-i18n.php';
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
				} elseif ( 'select' === $field['type'] ) {
					printf(
						'<label for="%1$s">%2$s<br><select id="%1$s" class="iconselectmenu %1$s" name="%3$s"><option value="">%4$s</option>',
						esc_attr( $id ),
						esc_html( $label ),
						esc_attr( $name ),
						esc_html__( 'No icon', 'wpcoreuvigo' )
					);

					foreach ( $this->icons as $icon ) {
						printf(
							'<option value="%1$s" class="asdf-%1$s" %2$s>%1$s</option>',
							esc_attr( $icon ),
							selected( $icon, $value, false )
						);
					}
					printf( '</select></label>' );
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
}
