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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FM_WOOCOMMERCE_PRODUCT_CATEGORY_EXCLUSIONS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fm-woocommerce-product-category-exclusions-activator.php
 */
function activate_fm_woocommerce_product_category_exclusions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fm-woocommerce-product-category-exclusions-activator.php';
	Fm_Woocommerce_Product_Category_Exclusions_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fm-woocommerce-product-category-exclusions-deactivator.php
 */
function deactivate_fm_woocommerce_product_category_exclusions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fm-woocommerce-product-category-exclusions-deactivator.php';
	Fm_Woocommerce_Product_Category_Exclusions_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_fm_woocommerce_product_category_exclusions' );
register_deactivation_hook( __FILE__, 'deactivate_fm_woocommerce_product_category_exclusions' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fm-woocommerce-product-category-exclusions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fm_woocommerce_product_category_exclusions() {

	$plugin = new Fm_Woocommerce_Product_Category_Exclusions();
	$plugin->run();

}
run_fm_woocommerce_product_category_exclusions();
