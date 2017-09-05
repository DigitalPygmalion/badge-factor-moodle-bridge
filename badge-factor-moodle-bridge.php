<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/DigitalPygmalion/badge-factor-moodle-bridge
 * @since             1.0.0
 * @package           Badge_Factor_Moodle_Bridge
 *
 * @wordpress-plugin
 * Plugin Name:       BadgeFactorMoodleBridge
 * Plugin URI:        https://github.com/DigitalPygmalion/badge-factor-moodle-bridge
 * Description:       Link WP and Moodle.
 * Version:           1.0.0
 * Author:            Django Doucet (Service de l'audiovisuel UQAM)
 * Author URI:        https://audiovisuel.uqam.ca/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       badge-factor-moodle-bridge
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-badge-factor-moodle-bridge-activator.php
 */
function activate_badge_factor_moodle_bridge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-badge-factor-moodle-bridge-activator.php';
	Badge_Factor_Moodle_Bridge_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-badge-factor-moodle-bridge-deactivator.php
 */
function deactivate_badge_factor_moodle_bridge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-badge-factor-moodle-bridge-deactivator.php';
	Badge_Factor_Moodle_Bridge_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_badge_factor_moodle_bridge' );
register_deactivation_hook( __FILE__, 'deactivate_badge_factor_moodle_bridge' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-badge-factor-moodle-bridge.php';

 /**
 * Adds a custom field to link moodle course_id_number to badges post type.
 *
 * @param array $buttons Array of registered TinyMCE Buttons
 * @return array Modified array of registered TinyMCE Buttons
 */
function create_course_id_field() {
	if(function_exists("register_field_group"))
	{
		register_field_group(array (
			'id' => 'acf_moodle-bridge',
			'title' => 'Moodle Bridge',
			'fields' => array (
				array (
					'key' => 'field_59aedcdcbc971',
					'label' => 'Course ID number',
					'name' => 'course_id_number',
					'type' => 'text',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'badges',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array (
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 0,
		));
	}
}
add_action( 'init', 'create_course_id_field' );

/**
 * Add the endpoint to redirect user to his BuddyPress page.
 */
function badge_list_endpoint() {
	add_rewrite_endpoint( 'voir-mes-badges', EP_ROOT );
}
add_action( 'init', 'badge_list_endpoint' );

/**
 *  Redirect to the logged user BuddyPress page.
 */
function go_to_badge_list($query){
	
	if(isset($query)){
		if($query->is_main_query() && isset($query->query_vars["voir-mes-badges"])){
			$url = bp_core_get_userlink(bp_loggedin_user_id(), false, true);
			if($url != ""){
				wp_redirect($url);
				exit;
			}
		}
	}
}
add_action( 'pre_get_posts', 'go_to_badge_list' );

class TinyMCE_Badgefactor_Shortcode {

    /**
     * Constructor. Called when the plugin is initialised.
     */
    function __construct() {
        if ( is_admin() ) {
            add_action( 'init', array(  $this, 'badgefactor_shortcode_button' ) );
        }
    }
    /**
     * Check if the current user can edit Posts or Pages, and is using the Visual Editor
     * If so, add some filters so we can register our plugin
     */
    function badgefactor_shortcode_button() {

// Check if the logged in WordPress User can edit Posts or Pages
// If not, don't register our TinyMCE plugin

        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
            return;
        }

// Check if the logged in WordPress User has the Visual Editor enabled
// If not, don't register our TinyMCE plugin
        if ( get_user_option( 'rich_editing' ) !== 'true' ) {
            return;
        }

// Setup some filters
        add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_plugin' ) );
        add_filter( 'mce_buttons', array( &$this, 'add_tinymce_toolbar_button' ) );
		
//		remove_filter('mce_external_plugins', 'wp2m_add_plugin');
//	    remove_filter('mce_buttons', 'wp2m_register_button', 10);
		
    }

    /**
     *  Adds a TinyMCE plugin compatible JS file to the TinyMCE / Visual Editor instance
	 *	https://stackoverflow.com/questions/33454966/tinymce-dialog-with-ajax-content
	 *	includes/
	 *	wp_ajax_nopriv_get_badge_list_wp2moodle() 
     */
    function add_tinymce_plugin( $plugin_array ) {

/*		if( function_exists	) {
			function wp2m_add_plugin($plugin_array) {
			   $plugin_array['wp2m'] = plugin_dir_url(__FILE__).'wp2m.js';
			   return $plugin_array;
			}
		}*/
		
        $plugin_array['badgefactor_shortcode'] = plugin_dir_url( __FILE__ ) . 'admin/js/badge-factor-moodle-bridge-admin.js';
        return $plugin_array;


		
    }

	
    /**
     * Adds a button to the TinyMCE / Visual Editor which the user can click
     * to insert a link with a custom CSS class.
     *
     * @param array $buttons Array of registered TinyMCE Buttons
     * @return array Modified array of registered TinyMCE Buttons
     */
    function add_tinymce_toolbar_button( $buttons ) {
	
        array_push( $buttons, '|', 'badgefactor_shortcode' );
        return $buttons;
    }

}
$tinymce_badgefactor_shortcode = new TinyMCE_Badgefactor_Shortcode;

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_badge_factor_moodle_bridge() {

	$plugin = new Badge_Factor_Moodle_Bridge();
	$plugin->run();

}
run_badge_factor_moodle_bridge();
