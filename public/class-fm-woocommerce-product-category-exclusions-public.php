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
}
