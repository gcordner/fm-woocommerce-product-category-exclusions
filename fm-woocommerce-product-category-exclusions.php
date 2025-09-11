<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://geoffcordner.net
 * @since             1.0.0
 * @package           Fm_Woocommerce_Product_Category_Exclusions
 *
 * @wordpress-plugin
 * Plugin Name:       FM WooCommerce Product Category Exclusions
 * Plugin URI:        https://github.com/gcordner/fm-woocommerce-product-category-exclusions.git
 * Description:       Outputs a filtered list of product categories.
 * Version:           1.0.0
 * Author:            Geoff Cordner
 * Author URI:        https://geoffcordner.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fm-woocommerce-product-category-exclusions
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Version + shared option key (array of ints, autoload=no).
define( 'FM_WOOCOMMERCE_PRODUCT_CATEGORY_EXCLUSIONS_VERSION', '1.0.0' );
define( 'FM_WCPCE_OPTION', 'fm_wcpce_excluded_product_cat_ids' );

/**
 * Activation
 */
function activate_fm_woocommerce_product_category_exclusions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fm-woocommerce-product-category-exclusions-activator.php';
	Fm_Woocommerce_Product_Category_Exclusions_Activator::activate();
}
/**
 * Deactivation
 */
function deactivate_fm_woocommerce_product_category_exclusions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fm-woocommerce-product-category-exclusions-deactivator.php';
	Fm_Woocommerce_Product_Category_Exclusions_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_fm_woocommerce_product_category_exclusions' );
register_deactivation_hook( __FILE__, 'deactivate_fm_woocommerce_product_category_exclusions' );

/**
 * Core plugin class
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fm-woocommerce-product-category-exclusions.php';
require plugin_dir_path( __FILE__ ) . 'includes/fm-wcpce-functions.php';

/**
 * Run
 */
function run_fm_woocommerce_product_category_exclusions() {
	$plugin = new Fm_Woocommerce_Product_Category_Exclusions();
	$plugin->run();
}
run_fm_woocommerce_product_category_exclusions();
