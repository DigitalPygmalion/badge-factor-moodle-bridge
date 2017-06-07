<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/DigitalPygmalion/badge-factor-moodle-bridge
 * @since      1.0.0
 *
 * @package    Badge_Factor_Moodle_Bridge
 * @subpackage Badge_Factor_Moodle_Bridge/includes
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
 * @package    Badge_Factor_Moodle_Bridge
 * @subpackage Badge_Factor_Moodle_Bridge/includes
 * @author     Django Doucet <doucet.django@uqam.ca>
 */
class Badge_Factor_Moodle_Bridge {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Badge_Factor_Moodle_Bridge_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'badge-factor-moodle-bridge';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Badge_Factor_Moodle_Bridge_Loader. Orchestrates the hooks of the plugin.
	 * - Badge_Factor_Moodle_Bridge_i18n. Defines internationalization functionality.
	 * - Badge_Factor_Moodle_Bridge_Admin. Defines all hooks for the admin area.
	 * - Badge_Factor_Moodle_Bridge_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-badge-factor-moodle-bridge-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-badge-factor-moodle-bridge-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-badge-factor-moodle-bridge-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-badge-factor-moodle-bridge-public.php';

		$this->loader = new Badge_Factor_Moodle_Bridge_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Badge_Factor_Moodle_Bridge_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Badge_Factor_Moodle_Bridge_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Badge_Factor_Moodle_Bridge_Admin( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// Plugin Settings and Options page
		$this->loader->add_action( 'admin_init', $plugin_admin, 'badge_factor_moodle_bridge_settings_init', 90, 0 );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'badge_factor_moodle_bridge_add_admin_menu', 10 );
		//$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'badge_factor_moodle_bridge_settings_link' );
		
		// Get Badge list
		//$this->loader->add_action( 'wp_ajax_get_badge_list_wp2moodle', $plugin_admin, 'get_badge_list_wp2moodle' );
		//$this->loader->add_action( 'wp_ajax_nopriv_get_badge_list_wp2moodle', $plugin_admin, 'get_badge_list_wp2moodle' );
		
		
		// Remove button, we are replacing it
		$this->loader->add_action( 'init', $plugin_admin, 'remove_wp2m_tinymce_button', 20 );
		//$this->loader->add_action( 'mce_buttons', $plugin_admin, 'remove_wp2m_tinymce', 20 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Badge_Factor_Moodle_Bridge_Public( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Request test Pub
        //$this->loader->add_action( 'wp_ajax_nopriv_request_test_pub', $plugin_public, 'request_test_pub' );
		
		// Authenticate
		//$this->loader->add_filter( 'authenticate', $plugin_public, 'authenticate', 100, 3);
		
		// Authenticate
    	//$this->loader->add_action( 'wp_ajax_nopriv_sav_authenticate', $plugin_public, 'sav_authenticate' );

		// Get Badge assertion from Moodle
		$this->loader->add_action( 'wp_ajax_nopriv_get_assertion', $plugin_public, 'get_assertion' );

        // Get Badge list for wp2Moodle
		$this->loader->add_action( 'wp_ajax_get_badge_list_wp2moodle', $plugin_public, 'get_badge_list_wp2moodle' );
        $this->loader->add_action( 'wp_ajax_nopriv_get_badge_list_wp2moodle', $plugin_public, 'get_badge_list_wp2moodle' );
		
		// Get Button text options for tinymce button
		$this->loader->add_action( 'wp_ajax_get_wp2moodle_options', $plugin_public, 'get_wp2moodle_options' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_wp2moodle_options', $plugin_public, 'get_wp2moodle_options' );
		
		//
		//$this->loader->add_action('wp_head', $plugin_public, 'badge_factor_moodle_bridge_ajaxurl');
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Badge_Factor_Moodle_Bridge_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
