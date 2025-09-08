<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://geoffcordner.net
 * @since      1.0.0
 *
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/includes
 * @author     Geoff Cordner <geoffcordner@gmail.com>
 */
class Fm_Woocommerce_Product_Category_Exclusions_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fm-woocommerce-product-category-exclusions',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
