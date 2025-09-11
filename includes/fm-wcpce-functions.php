<?php
/**
 * Functions for FM WooCommerce Product Category Exclusions
 *
 * @package Fm_Woocommerce_Product_Category_Exclusions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fm_wcpce_get_excluded_ids' ) ) {
	/**
	 * Get excluded product_cat IDs (normalized array of ints).
	 *
	 * @return int[]
	 */
	function fm_wcpce_get_excluded_ids() {
		$ids = array_map( 'intval', (array) get_option( FM_WCPCE_OPTION, array() ) );

		/**
		 * Filter the excluded IDs before use.
		 *
		 * @param int[] $ids Excluded term IDs.
		 */
		return apply_filters( 'fm_wcpce_excluded_ids', $ids );
	}
}
