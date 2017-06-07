<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://bitbucket.org/django_d/
 * @since      1.0.0
 *
 * @package    Badge_Factor_Moodle_Bridge
 * @subpackage Badge_Factor_Moodle_Bridge/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Badge_Factor_Moodle_Bridge
 * @subpackage Badge_Factor_Moodle_Bridge/includes
 * @author     Django Doucet <doucet.django@uqam.ca>
 */
class Badge_Factor_Moodle_Bridge_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'badge-factor-moodle-bridge',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
