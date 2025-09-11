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
/**
 * Define the internationalization functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fm_Woocommerce_Product_Category_Exclusions_i18n {

	/**
	 * Text domain.
	 *
	 * @var string
	 */
	private $domain;

	/**
	 * Inject the text domain.
	 *
	 * @param string $domain
	 */
	public function __construct( $domain ) {
		$this->domain = $domain;
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
