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
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/public
 * @author     Geoff Cordner <geoffcordner@gmail.com>
 */
class Fm_Woocommerce_Product_Category_Exclusions_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Print a filtered list of product categories on single product pages.
	 * Hooked to: woocommerce_product_meta_end
	 */
	public function render_filtered_categories() {
		if ( ! is_product() ) {
			return;
		}

		global $product;
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$excluded_ids = fm_wcpce_get_excluded_ids();
		$cat_ids      = array_diff( $product->get_category_ids(), $excluded_ids );

		if ( empty( $cat_ids ) ) {
			return;
		}

		// Load the category terms we’re keeping (one query).
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'include'    => array_map( 'intval', $cat_ids ),
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		$links = array();
		foreach ( $terms as $t ) {
			$url = get_term_link( $t );
			if ( is_wp_error( $url ) ) {
				continue;
			}
			$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $t->name ) . '</a>';
		}

		if ( empty( $links ) ) {
			return;
		}

		$label = _n( 'Categories (filtered):', 'Categories (filtered):', count( $links ), 'fm-woocommerce-product-category-exclusions' );

		echo '<div class="product_meta fm-wcpce">';
		echo '<span class="posted_in fm-wcpce__categories">' . esc_html( $label ) . ' ' . implode( ', ', $links ) . '</span>';
		echo '</div>';
	}

	/**
	 * Filter the raw product category IDs returned by WooCommerce.
	 *
	 * Runs whenever code calls $product->get_category_ids().
	 *
	 * @param int[]      $ids     Original IDs.
	 * @param WC_Product $product Product.
	 * @return int[]              Filtered IDs.
	 */
	public function filter_product_category_ids( $ids, $product ) {
		if ( ! $product instanceof WC_Product ) {
			return $ids;
		}

		$excluded = function_exists( 'fm_wcpce_get_excluded_ids' ) ? (array) fm_wcpce_get_excluded_ids() : array();
		if ( empty( $excluded ) ) {
			return $ids;
		}

		// Remove excluded IDs and reindex.
		return array_values( array_diff( (array) $ids, $excluded ) );
	}

	/**
	 * Filter the default categories HTML that WooCommerce builds.
	 *
	 * Runs whenever code calls $product->get_categories() or wc_get_product_category_list().
	 *
	 * @param string     $html    Original HTML.
	 * @param WC_Product $product Product.
	 * @return string             Filtered HTML (or empty string if none remain).
	 */
	public function filter_product_categories_html( $html, $product ) {
		if ( ! $product instanceof WC_Product ) {
			return $html;
		}

		// By now, $product->get_category_ids() is already filtered by our other hook.
		$keep_ids = (array) $product->get_category_ids();
		if ( empty( $keep_ids ) ) {
			return '';
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'include'    => array_map( 'intval', $keep_ids ),
				'hide_empty' => false,
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

		// If the original HTML had Woo's posted_in wrapper, keep that UX.
		$has_posted_in = ( false !== strpos( $html, 'posted_in' ) );
		$label         = _n( 'Category:', 'Categories:', count( $links ), 'woocommerce' );

		return $has_posted_in
		? '<span class="posted_in">' . esc_html( $label ) . ' ' . implode( ', ', $links ) . '</span>'
		: implode( ', ', $links );
	}

	/**
	 * Filter get_the_terms() results for product_cat so term lists exclude blocked categories.
	 *
	 * @param array|WP_Error|null $terms
	 * @param int                 $post_id
	 * @param string              $taxonomy
	 * @return array|WP_Error|null
	 */
	public function filter_get_the_terms_product_cat( $terms, $post_id, $taxonomy ) {
		if ( 'product_cat' !== $taxonomy || empty( $terms ) || is_wp_error( $terms ) ) {
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
	 * Filter wp_get_object_terms() results for product_cat (used by wc_get_product_category_list()).
	 *
	 * @param array|WP_Error $terms
	 * @param int[]          $object_ids
	 * @param array          $taxonomies
	 * @param array          $args
	 * @return array|WP_Error
	 */
	public function filter_wp_get_object_terms_product_cat( $terms, $object_ids, $taxonomies, $args ) {
		// Only apply when product_cat is being requested.
		if ( empty( $taxonomies ) || ! in_array( 'product_cat', (array) $taxonomies, true ) ) {
			return $terms;
		}
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return $terms;
		}

		// If we can, ensure we're dealing with products (not mandatory but safer).
		// $object_ids can be array; we check the first when present.
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
			// $t can be WP_Term objects already; if IDs only, cast carefully.
			$term_id = is_object( $t ) ? (int) $t->term_id : (int) $t;
			if ( ! in_array( $term_id, $excluded, true ) ) {
				$filtered[] = $t;
			}
		}
		return $filtered;
	}
}
