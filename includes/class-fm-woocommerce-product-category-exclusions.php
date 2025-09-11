<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://geoffcordner.net
 * @since      1.0.0
 *
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Fm_Woocommerce_Product_Category_Exclusions
 * @subpackage Fm_Woocommerce_Product_Category_Exclusions/includes
 * @author     Geoff Cordner <geoffcordner@gmail.com>
 */
class Fm_Woocommerce_Product_Category_Exclusions {

	protected $loader;
	protected $plugin_name = 'fm-woocommerce-product-category-exclusions';
	protected $version     = FM_WOOCOMMERCE_PRODUCT_CATEGORY_EXCLUSIONS_VERSION;

	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		// Public hooks not needed yet for this step.
	}

	private function load_dependencies() {
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-fm-woocommerce-product-category-exclusions-loader.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-fm-woocommerce-product-category-exclusions-i18n.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-fm-woocommerce-product-category-exclusions-admin.php';
		require_once plugin_dir_path( __DIR__ ) . 'public/class-fm-woocommerce-product-category-exclusions-public.php';
		$this->loader = new Fm_Woocommerce_Product_Category_Exclusions_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new Fm_Woocommerce_Product_Category_Exclusions_i18n( $this->plugin_name );

		$this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );
	}


	private function define_admin_hooks() {
		$plugin_admin = new Fm_Woocommerce_Product_Category_Exclusions_Admin( $this->plugin_name, $this->version );

		// Admin menu + settings for exclusions UI.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'admin_post_fm_wcpce_save', $plugin_admin, 'handle_save' );

		// (Optional) enqueue admin assets if needed later.
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	private function define_public_hooks() {
		$plugin_public = new Fm_Woocommerce_Product_Category_Exclusions_Public( $this->get_plugin_name(), $this->get_version() );

		// Print our filtered category list after the default product meta.
		// (15 so it appears right after the core meta block).
		$this->loader->add_action(
			'woocommerce_product_meta_end',
			$plugin_public,
			'render_filtered_categories',
			15
		);
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}
	public function get_loader() {
		return $this->loader;
	}
	public function get_version() {
		return $this->version;
	}
}
