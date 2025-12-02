<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://geoffcordner.net
 * @since      1.0.0
 *
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/public
 */

/**
 * Public class: hide excluded categories ONLY on front-end single product pages.
 * Does NOT change saved category assignments or admin/editor behavior.
 */
class Fm_Woocommerce_Product_Category_Exclusions_Public {

	/**
	 * Undocumented variable
	 *
	 * @var [type]
	 */
	private $plugin_name;

	/**
	 * Undocumented variable
	 *
	 * @var [type]
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of the plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Utility: are we in a front-end single product context?
	 */
	private function is_single_product_frontend(): bool {
		if ( is_admin() ) {
			return false;
		}
		// Avoid interfering with AJAX/REST/cron.
		if ( ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
			|| ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() )
			|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			|| ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			return false;
		}
		return function_exists( 'is_product' ) && is_product();
	}

	/**
	 * (Optional) Print a filtered list of product categories at the end of product meta.
	 * Hook to: woocommerce_product_meta_end  (only if you deliberately want a separate block)
	 */
	public function render_filtered_categories() {
		if ( ! $this->is_single_product_frontend() ) {
			return;
		}

		global $product;
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$excluded_ids = function_exists( 'fm_wcpce_get_excluded_ids' ) ? (array) fm_wcpce_get_excluded_ids() : array();
		$keep_ids     = array_values( array_diff( (array) $product->get_category_ids(), $excluded_ids ) );

		if ( empty( $keep_ids ) ) {
			return;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'include'    => array_map( 'intval', $keep_ids ),
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		$links = array();
		foreach ( $terms as $t ) {
			$url = get_term_link( $t );
			if ( ! is_wp_error( $url ) ) {
				$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $t->name ) . '</a>';
			}
		}

		if ( empty( $links ) ) {
			return;
		}

		$label = _n( 'Category:', 'Categories:', count( $links ), 'woocommerce' );

		echo '<div class="product_meta fm-wcpce">';
		echo '<span class="posted_in fm-wcpce__categories">' . esc_html( $label ) . ' ' . implode( ', ', esc_url( $links ) ) . '</span>';
		echo '</div>';
	}

	/**
	 * Filter the default categories HTML Woo builds (wc_get_product_category_list()).
	 * Keeps Woo's output but removes excluded terms on single product pages only.
	 *
	 * NOTE: your earlier implementation used ($html, $product). Keep that signature since it worked in your stack.
	 *
	 * @param string     $html    The HTML output of the product categories list.
	 * @param WC_Product $product The WooCommerce product object.
	 * @return string
	 */
	public function filter_product_categories_html( $html, $product ) {
		if ( ! $this->is_single_product_frontend() ) {
			return $html;
		}
		if ( ! $product instanceof WC_Product ) {
			return $html;
		}

		$excluded_ids = function_exists( 'fm_wcpce_get_excluded_ids' ) ? (array) fm_wcpce_get_excluded_ids() : array();
		if ( empty( $excluded_ids ) ) {
			return $html;
		}

		$keep_ids = array_values( array_diff( (array) $product->get_category_ids(), $excluded_ids ) );
		if ( empty( $keep_ids ) ) {
			return ''; // suppress entirely if nothing remains.
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'include'    => array_map( 'intval', $keep_ids ),
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		$links = array();
		foreach ( $terms as $t ) {
			$url = get_term_link( $t );
			if ( ! is_wp_error( $url ) ) {
				$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $t->name ) . '</a>';
			}
		}
		if ( empty( $links ) ) {
			return '';
		}

		$has_posted_in = ( false !== strpos( $html, 'posted_in' ) );
		$label         = _n( 'Category:', 'Categories:', count( $links ), 'woocommerce' );

		return $has_posted_in
			? '<span class="posted_in">' . esc_html( $label ) . ' ' . implode( ', ', $links ) . '</span>'
			: implode( ', ', $links );
	}

	/**
	 * Filter the raw product category IDs returned by WooCommerce.
	 * Runs when code calls $product->get_category_ids(). Scoped to FE single only.
	 *
	 * @param int[]      $ids     Array of product category IDs to filter.
	 * @param WC_Product $product The WooCommerce product object.
	 * @return int[]
	 */
	public function filter_product_category_ids( $ids, $product ) {
		if ( ! $this->is_single_product_frontend() ) {
			return $ids;
		}
		if ( ! $product instanceof WC_Product ) {
			return $ids;
		}

		$excluded = function_exists( 'fm_wcpce_get_excluded_ids' ) ? (array) fm_wcpce_get_excluded_ids() : array();
		if ( empty( $excluded ) ) {
			return $ids;
		}

		return array_values( array_diff( (array) $ids, $excluded ) );
	}

	/**
	 * Filter get_the_terms() results for product_cat. Scoped to FE single only.
	 *
	 * @param array|WP_Error|null $terms   The terms returned by get_the_terms().
	 * @param int                 $post_id The post ID for which terms are retrieved.
	 * @param string              $taxonomy The taxonomy name.
	 * @return array|WP_Error|null
	 */
	public function filter_get_the_terms_product_cat( $terms, $post_id, $taxonomy ) {
		if ( 'product_cat' !== $taxonomy || empty( $terms ) || is_wp_error( $terms ) ) {
			return $terms;
		}
		if ( ! $this->is_single_product_frontend() ) {
			return $terms;
		}
		if ( 'product' !== get_post_type( $post_id ) ) {
			return $terms;
		}

		$excluded = function_exists( 'fm_wcpce_get_excluded_ids' ) ? (array) fm_wcpce_get_excluded_ids() : array();
		if ( empty( $excluded ) ) {
			return $terms;
		}

		$filtered = array();
		foreach ( $terms as $t ) {
			if ( ! in_array( (int) $t->term_id, $excluded, true ) ) {
				$filtered[] = $t;
			}
		}
		return $filtered;
	}

	/**
	 * Filter wp_get_object_terms() for product_cat (used by wc_get_product_category_list()).
	 * Scoped to FE single only so admin/editor & archives remain untouched.
	 *
	 * @param array|WP_Error $terms      The terms returned by wp_get_object_terms().
	 * @param int[]          $object_ids The object IDs for which terms are retrieved.
	 * @param array          $taxonomies The taxonomy names.
	 * @param array          $args       Additional arguments passed to the function.
	 * @return array|WP_Error
	 */
	public function filter_wp_get_object_terms_product_cat( $terms, $object_ids, $taxonomies, $args ) {
		if ( empty( $taxonomies ) || ! in_array( 'product_cat', (array) $taxonomies, true ) ) {
			return $terms;
		}
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return $terms;
		}
		if ( ! $this->is_single_product_frontend() ) {
			return $terms;
		}

		// If available, sanity-check we're dealing with a product.
		$first_id = is_array( $object_ids ) && ! empty( $object_ids ) ? (int) reset( $object_ids ) : 0;
		if ( $first_id && 'product' !== get_post_type( $first_id ) ) {
			return $terms;
		}

		$excluded = function_exists( 'fm_wcpce_get_excluded_ids' ) ? (array) fm_wcpce_get_excluded_ids() : array();
		if ( empty( $excluded ) ) {
			return $terms;
		}

		$filtered = array();
		foreach ( $terms as $t ) {
			$term_id = is_object( $t ) ? (int) $t->term_id : (int) $t;
			if ( ! in_array( $term_id, $excluded, true ) ) {
				$filtered[] = $t;
			}
		}
		return $filtered;
	}
}
