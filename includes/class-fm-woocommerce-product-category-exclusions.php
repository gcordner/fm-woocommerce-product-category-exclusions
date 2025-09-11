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

	/**
	 * The loader that's responsible for maintaining and registering all hooks for the plugin.
	 *
	 * @var Fm_Woocommerce_Product_Category_Exclusions_Loader
	 */
	protected $loader;
	/**
	 * The unique identifier of this plugin.
	 *
	 * @var string
	 */
	protected $plugin_name = 'fm-woocommerce-product-category-exclusions';

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	protected $version = FM_WOOCOMMERCE_PRODUCT_CATEGORY_EXCLUSIONS_VERSION;

	/**
	 * Constructor for the core plugin class.
	 *
	 * Initializes dependencies, sets locale, and defines admin and public hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		// Public hooks not needed yet for this step.
	}

	/**
	 * Loads the required dependencies for the plugin.
	 *
	 * Includes loader, internationalization, admin, and public classes.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-fm-woocommerce-product-category-exclusions-loader.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-fm-woocommerce-product-category-exclusions-i18n.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-fm-woocommerce-product-category-exclusions-admin.php';
		require_once plugin_dir_path( __DIR__ ) . 'public/class-fm-woocommerce-product-category-exclusions-public.php';
		$this->loader = new Fm_Woocommerce_Product_Category_Exclusions_Loader();
	}

	/**
	 * Sets the locale for internationalization.
	 *
	 * Registers the text domain for translations.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function set_locale() {
		$plugin_i18n = new Fm_Woocommerce_Product_Category_Exclusions_i18n( $this->plugin_name );

		$this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );
	}


	/**
	 * Defines the admin hooks for the plugin.
	 *
	 * Registers actions for the admin menu, settings, and save handler.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Fm_Woocommerce_Product_Category_Exclusions_Admin( $this->plugin_name, $this->version );

		// Admin menu + settings for exclusions UI.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'admin_post_fm_wcpce_save', $plugin_admin, 'handle_save' );

		// (Optional) enqueue admin assets if needed later. // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Defines the public hooks for the plugin.
	 *
	 * Registers actions for displaying filtered categories on the product page.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function define_public_hooks() {
		$plugin_public = new Fm_Woocommerce_Product_Category_Exclusions_Public(
			$this->get_plugin_name(),
			$this->get_version()
		);

		// 1) Filter the raw category IDs returned by Woo everywhere.
		$this->loader->add_filter(
			'woocommerce_product_get_category_ids',
			$plugin_public,
			'filter_product_category_ids',
			10,
			2
		);

		// 2) Filter the categories HTML built by Woo everywhere.
		$this->loader->add_filter(
			'woocommerce_product_get_categories',
			$plugin_public,
			'filter_product_categories_html',
			10,
			2
		);

		// 3) Critical for Astra: wc_get_product_category_list() uses Core term APIs.
		$this->loader->add_filter(
			'get_the_terms',
			$plugin_public,
			'filter_get_the_terms_product_cat',
			10,
			3
		);

		$this->loader->add_filter(
			'wp_get_object_terms',
			$plugin_public,
			'filter_wp_get_object_terms_product_cat',
			10,
			4
		);

		// IMPORTANT: Do NOT add an action that echoes its own categories block.
		// This prevents duplicate category sections across themes.
	}


	/**
	 * Executes the loader to register all hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Gets the unique plugin name.
	 *
	 * @since 1.0.0
	 * @return string The plugin name.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	/**
	 * Gets the loader instance responsible for registering all hooks.
	 *
	 * @since 1.0.0
	 * @return Fm_Woocommerce_Product_Category_Exclusions_Loader The loader instance.
	 */
	public function get_loader() {
		return $this->loader;
	}
	/**
	 * Gets the current version of the plugin.
	 *
	 * @since 1.0.0
	 * @return string The plugin version.
	 */
	public function get_version() {
		return $this->version;
	}
}
