<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://geoffcordner.net
 * @since      1.0.0
 *
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/admin
 * @author     Geoff Cordner <geoffcordner@gmail.com>
 */
class Fm_Woocommerce_Product_Category_Exclusions_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Add submenu under WooCommerce.
	 */
	public function add_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Category Exclusions', 'fm-woocommerce-product-category-exclusions' ),
			__( 'Category Exclusions', 'fm-woocommerce-product-category-exclusions' ),
			'manage_woocommerce',
			'fm-wcpce',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings + field.
	 *
	 * Note: With the custom save handler, we no longer register a Settings API option here.
	 * Keeping the method to preserve structure and comments.
	 */
	public function register_settings() {
		// Intentionally empty: saving is handled by handle_save() to explicitly control autoload=false.
	}

	/**
	 * Sanitize and force autoload = no on save.
	 *
	 * Note: With the custom save handler, sanitation happens inside handle_save().
	 * Keeping this method for compatibility; it is not used by the current flow.
	 */
	public function sanitize_ids( $value ) {
		$ids = array_map( 'intval', (array) $value );
		$ids = array_values( array_unique( array_filter( $ids, fn( $v ) => $v > 0 ) ) );
		return $ids;
	}

	/**
	 * Render a scrollable checklist of product_cat terms.
	 */
	public function render_categories_checklist() {
		$saved = get_option( FM_WCPCE_OPTION, array() );

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) ) {
			echo '<p class="description">' . esc_html__( 'Unable to load product categories.', 'fm-woocommerce-product-category-exclusions' ) . '</p>';
			return;
		}

		if ( empty( $terms ) ) {
			echo '<p class="description">' . esc_html__( 'No product categories found.', 'fm-woocommerce-product-category-exclusions' ) . '</p>';
			return;
		}

		echo '<div style="max-height:340px; overflow:auto; border:1px solid #ccd0d4; padding:10px; background:#fff;">';

		foreach ( $terms as $t ) {
			$id    = (int) $t->term_id;
			$name  = $t->name;
			$field = 'fm_wcpce_ids[]'; // changed name so we can handle via custom admin-post action

			printf(
				'<label style="display:block; margin:4px 0;"><input type="checkbox" name="%s" value="%d" %s /> %s</label>',
				esc_attr( $field ),
				$id,
				checked( in_array( $id, (array) $saved, true ), true, false ),
				esc_html( $name )
			);
		}

		echo '</div>';
		echo '<p class="description">' . esc_html__( 'Checked categories will be excluded from the product page output.', 'fm-woocommerce-product-category-exclusions' ) . '</p>';
	}

	/**
	 * Settings page wrapper.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'fm-woocommerce-product-category-exclusions' ) );
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WooCommerce Product Category Exclusions', 'fm-woocommerce-product-category-exclusions' ); ?></h1>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="fm_wcpce_save" />
				<?php wp_nonce_field( 'fm_wcpce_save' ); ?>

				<?php
				// Render the checklist directly (no Settings API sections/fields used for saving).
				$this->render_categories_checklist();
				submit_button( __( 'Save Exclusions', 'fm-woocommerce-product-category-exclusions' ) );
				?>
			</form>

			<hr />
			<h2><?php esc_html_e( 'Currently Excluded IDs', 'fm-woocommerce-product-category-exclusions' ); ?></h2>
			<code><?php echo esc_html( implode( ', ', array_map( 'intval', (array) get_option( FM_WCPCE_OPTION, array() ) ) ) ); ?></code>
		</div>
		<?php
	}

	/**
	 * Handle form submission.
	 *
	 * Saves the selected IDs and explicitly sets autoload = false so the DB stores 'off' (not 'auto').
	 */
	public function handle_save() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'No permission.', 'fm-woocommerce-product-category-exclusions' ) );
		}

		check_admin_referer( 'fm_wcpce_save' );

		$ids = isset( $_POST['fm_wcpce_ids'] ) ? (array) $_POST['fm_wcpce_ids'] : array();
		$ids = array_values(
			array_unique(
				array_filter(
					array_map( 'intval', $ids ),
					fn( $v ) => $v > 0
				)
			)
		);

		// Save with autoload = false so DB stores 'off' (never 'auto' per WP 6.6 semantics).
		update_option( FM_WCPCE_OPTION, $ids, false );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'fm-wcpce',
					'updated' => 1,
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	// Scaffolded placeholders if you add assets later.
	// public function enqueue_styles() {} .
	// public function enqueue_scripts() {} .
}
