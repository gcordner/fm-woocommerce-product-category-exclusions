<?php
/**
 * Fired during plugin activation.
 *
 * @link       https://geoffcordner.net
 * @since      1.0.0
 *
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/includes
 * @author     Geoff Cordner <geoffcordner@gmail.com>
 */
class Fm_Woocommerce_Product_Category_Exclusions_Activator {
	/**
	 * Activate the plugin.
	 *
	 * @return void
	 */
	public static function activate() {
		// If missing, create with autoload = false (DB 'off').
		if ( get_option( FM_WCPCE_OPTION, null ) === null ) {
			add_option( FM_WCPCE_OPTION, array(), '', false );
		}
	}
}
