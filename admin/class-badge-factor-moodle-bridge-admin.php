<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/DigitalPygmalion/badge-factor-moodle-bridge
 * @since      1.0.0
 *
 * @package    Badge_Factor_Moodle_Bridge
 * @subpackage Badge_Factor_Moodle_Bridge/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Badge_Factor_Moodle_Bridge
 * @subpackage Badge_Factor_Moodle_Bridge/admin
 * @author     Django Doucet <doucet.django@uqam.ca>
 */
class Badge_Factor_Moodle_Bridge_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Badge_Factor_Moodle_Bridge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Badge_Factor_Moodle_Bridge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

/*		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/badge-factor-moodle-bridge-admin.js', array( __CLASS__, 'editor_js'), $this->version, false );
		wp_localize_script( $this->plugin_name, 'admin_url', array('ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		
		$bridge_options = get_option( 'badge_factor_moodle_bridge_settings' );
		wp_localize_script( $this->plugin_name, 'admin_url', array('moodle_bridge_settings' => $bridge_options ) );*/

	}

	/**
     * Remove wp2Moodle tinymce button.
     *
     * @since    1.0.0
     */
	public function remove_wp2m_tinymce_button(){
	//	remove_action ( 'init', 'wp2m_register_addbutton', 10);
		remove_filter('mce_external_plugins', 'wp2m_add_plugin', 10);
	//	remove_filter('mce_buttons', 'wp2m_register_button', 10);
	}

	/**
     * Badge Factor Moodle Bridge Options.
     *
     * @since    1.0.0
     */
	public function badge_factor_moodle_bridge_add_admin_menu() { 
		
		$minimum_role = badgeos_get_manager_capability();
		
		//Hack to add badge_factor_moodle_bridge_options_page under badgefactor Menu
		add_menu_page('badge_factor_moodle_bridge', __('Moodle Bridge Options', 'badgefactor_moodle_bridge'), $minimum_role, 'badge_factor_moodle_bridge',  'badge_factor_moodle_bridge_options_page');
		remove_menu_page('badge_factor_moodle_bridge'); 
		
		add_submenu_page('badgeos_badgeos', __('Badge Factor Moodle Bridge', 'badgefactor'), __('Moodle Bridge Options', 'badge_factor_moodle_bridge'), badgeos_get_manager_capability(), 'badge_factor_moodle_bridge', 'badge_factor_moodle_bridge_options_page');
		
		function badge_factor_moodle_bridge_options_page() { 
		
			?>
			<form action='options.php' method='post'>
		
				<h2>Badge Factor Moodle Bridge</h2>
		
				<?php
				settings_fields( 'BadgeFactorMoodleBridgePluginPage' );
				do_settings_sections( 'BadgeFactorMoodleBridgePluginPage' );
				submit_button();
				?>
		
			</form>
			<?php

		}
	
	}
	
	/**
     * Badge Factor Moodle Bridge Options Page Settings.
     *
     * @since    1.0.0
     */
	public function badge_factor_moodle_bridge_settings_init() { 
		register_setting( 'BadgeFactorMoodleBridgePluginPage', 'badge_factor_moodle_bridge_settings' );

		add_settings_section(
			'badge_factor_moodle_bridge_BadgeFactorMoodleBridgePluginPage_section', 
			__( '', 'badge-factor-moodle-bridge' ), 
			'badge_factor_moodle_bridge_settings_section_callback', 
			'BadgeFactorMoodleBridgePluginPage'
		);
		
        add_settings_field(
            'badge_factor_moodle_bridge_unauth_moodle_text',
            __( 'Anonymous button text', 'badge-factor-moodle-bridge' ),
            'badge_factor_moodle_bridge_unauth_moodle_text_render',
            'BadgeFactorMoodleBridgePluginPage',
            'badge_factor_moodle_bridge_BadgeFactorMoodleBridgePluginPage_section'
        );

        add_settings_field(
            'badge_factor_moodle_bridge_auth_moodle_text',
            __( 'Authenticated button text', 'badge-factor-moodle-bridge' ),
            'badge_factor_moodle_bridge_auth_moodle_text_render',
            'BadgeFactorMoodleBridgePluginPage',
            'badge_factor_moodle_bridge_BadgeFactorMoodleBridgePluginPage_section'
        );
	
		function badge_factor_moodle_bridge_moodle_url_render() { 
	
			$options = get_option( 'badge_factor_moodle_bridge_settings' );
			?>
			<input type='text' name='badge_factor_moodle_bridge_settings[badge_factor_moodle_bridge_moodle_url]' value='<?php echo (isset($options['badge_factor_moodle_bridge_moodle_url'])) ? $options['badge_factor_moodle_bridge_moodle_url'] : ''; ?>'>
			<?php
		
		}
	
		function badge_factor_moodle_bridge_shared_api_key_render() { 
		
			$options = get_option( 'badge_factor_moodle_bridge_settings' );
			?>
			<input type='text' name='badge_factor_moodle_bridge_settings[badge_factor_moodle_bridge_shared_api_key]' value='<?php echo (isset($options['badge_factor_moodle_bridge_shared_api_key'])) ? $options['badge_factor_moodle_bridge_shared_api_key'] : ''; ?>'>
			<?php
		
		}

        function badge_factor_moodle_bridge_unauth_moodle_text_render() {

            $options = get_option( 'badge_factor_moodle_bridge_settings' );
            ?>
            <input type='text' name='badge_factor_moodle_bridge_settings[badge_factor_moodle_bridge_unauth_moodle_text]' value='<?php echo (isset($options['badge_factor_moodle_bridge_unauth_moodle_text'])) ? $options['badge_factor_moodle_bridge_unauth_moodle_text'] : ''; ?>'>
            <?php

        }

        function badge_factor_moodle_bridge_auth_moodle_text_render() {

            $options = get_option( 'badge_factor_moodle_bridge_settings' );
            ?>
            <input type='text' name='badge_factor_moodle_bridge_settings[badge_factor_moodle_bridge_auth_moodle_text]' value='<?php echo (isset($options['badge_factor_moodle_bridge_auth_moodle_text'])) ? $options['badge_factor_moodle_bridge_auth_moodle_text'] : ''; ?>'>
            <?php

        }
		
		function badge_factor_moodle_bridge_settings_section_callback() { 
		
			echo __( 'First, configure connection with Moodle via <a href="admin.php?page=wp2moodle/wp2m_settings_page.php">wp2Moodle</a> plugin', 'badge-factor-moodle-bridge' );
            echo __( '<br><h2>Configure button links to Moodle</h2>', 'badge-factor-moodle-bridge' );
		
		}
		
		//REMOVE SECTION GET FROM @wp2Moodle
		add_settings_section(
			'badge_factor_moodle_bridge_BadgeFactorMoodleBridgePluginPage_section_del', 
			__( '', 'badge-factor-moodle-bridge' ), 
			'badge_factor_moodle_bridge_settings_section_callback_del', 
			'BadgeFactorMoodleBridgePluginPage'
		);
		//REMOVE FIELD/SETTING GET FROM @wp2Moodle
		add_settings_field( 
			'badge_factor_moodle_bridge_moodle_url', 
			__( 'TODO REMOVE - Moodle URL', 'badge-factor-moodle-bridge' ),
			'badge_factor_moodle_bridge_moodle_url_render', 
			'BadgeFactorMoodleBridgePluginPage', 
			'badge_factor_moodle_bridge_BadgeFactorMoodleBridgePluginPage_section_del' 
		);

        //REMOVE FIELD/SETTING GET FROM @wp2Moodle
		add_settings_field( 
			'badge_factor_moodle_bridge_shared_api_key', 
			__( 'TODO REMOVE - Shared API Key', 'badge-factor-moodle-bridge' ),
			'badge_factor_moodle_bridge_shared_api_key_render', 
			'BadgeFactorMoodleBridgePluginPage', 
			'badge_factor_moodle_bridge_BadgeFactorMoodleBridgePluginPage_section_del' 
		);
		
		function badge_factor_moodle_bridge_settings_section_callback_del() { 
		
            echo __( '<br><h2>REMOVE</h2>', 'badge-factor-moodle-bridge' );
		
		}
	}
	
}
