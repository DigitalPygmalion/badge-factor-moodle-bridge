<?php
/**
 * Plugin Name: WP SAML Auth
 * Version: 0.2.1
 * Description: SAML authentication for WordPress, using SimpleSAMLphp.
 * Author: Pantheon
 * Author URI: https://pantheon.io
 * Plugin URI: https://wordpress.org/plugins/wp-saml-auth/
 * Text Domain: wp-saml-auth
 * Domain Path: /languages
 * @package Wp_Saml_Auth
 */

/**
 * Provides default options for WP SAML Auth.
 *
 * @param mixed $value
 * @param string $option_name
 */
function wpsa_filter_option( $value, $option_name ) {
    global $defaults;
    $defaults = array(
        /**
         * Path to SimpleSAMLphp autoloader.
         *
         * Follow the standard implementation by installing SimpleSAMLphp
         * alongside the plugin, and provide the path to its autoloader.
         * Alternatively, this plugin will work if it can find the
         * `SimpleSAML_Auth_Simple` class.
         *
         * @param string
         */
        'simplesamlphp_autoload' => dirname( __FILE__ ) . '~/simplesamlphp/lib/_autoload.php',
        /**
         * Authentication source to pass to SimpleSAMLphp
         *
         * This must be one of your configured identity providers in
         * SimpleSAMLphp. If the identity provider isn't configured
         * properly, the plugin will not work properly.
         *
         * @param string
         */
        'auth_source'            => 'default-sp',
        /**
         * Whether or not to automatically provision new WordPress users.
         *
         * When WordPress is presented with a SAML user without a
         * corresponding WordPress account, it can either create a new user
         * or display an error that the user needs to contact the site
         * administrator.
         *
         * @param bool
         */
        'auto_provision'         => 1,
        /**
         * Whether or not to permit logging in with username and password.
         *
         * If this feature is disabled, all authentication requests will be
         * channeled through SimpleSAMLphp.
         *
         * @param bool
         */
        'permit_wp_login'        => true,
        /**
         * Attribute by which to get a WordPress user for a SAML user.
         *
         * @param string Supported options are 'email' and 'login'.
         */
        'get_user_by'            => 'email',
        /**
         * SAML attribute which includes the user_login value for a user.
         *
         * @param string
         */
        'user_login_attribute'   => 'urn:oid:0.9.2342.19200300.100.1.1',
        /**
         * SAML attribute which includes the user_email value for a user.
         *
         * @param string
         */
        'user_email_attribute'   => 'urn:oid:0.9.2342.19200300.100.1.3',
        /**
         * SAML attribute which includes the display_name value for a user.
         *
         * @param string
         */
        'display_name_attribute' => 'urn:oid:2.16.840.1.113730.3.1.241',
        /**
         * SAML attribute which includes the first_name value for a user.
         *
         * @param string
         */
        'first_name_attribute' => 'urn:oid:2.5.4.42',
        /**
         * SAML attribute which includes the last_name value for a user.
         *
         * @param string
         */
        'last_name_attribute' => 'urn:oid:2.5.4.4',
        /**
         * Default WordPress role to grant when provisioning new users.
         *
         * @param string
         */
        'default_role'		=> get_option( 'default_role' ),
    );
//
    $value = isset( $defaults[ $option_name ] ) ? $defaults[ $option_name ] : $value;
    return $value;
}
add_filter( 'wp_saml_auth_option', 'wpsa_filter_option', 0, 2 );


/**
 * Options page for WP SAML Auth.
 *
 * @since
 */
function wp_saml_auth_add_admin_menu() {
    //add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '' )
    //add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
    add_submenu_page( 'options-general.php', 'WP SAML Auth', 'WP SAML Auth', 'manage_options', 'wp_saml_auth', 'wp_saml_auth_options_page' );
    function wp_saml_auth_options_page() {

        ?>
        <form action='options.php' method='post'>

            <h2>WP SAML Auth</h2>

            <?php
            settings_fields( 'WPSAMLAuthPluginPage' );
            do_settings_sections( 'WPSAMLAuthPluginPage' );
            submit_button();
            ?>

        </form>
        <?php
    }
}
add_action( 'admin_menu', 'wp_saml_auth_add_admin_menu' );
function wp_saml_auth_settings_init() {
    register_setting( 'WPSAMLAuthPluginPage', 'wp_saml_auth_settings' );

    add_settings_section(
        'wp_saml_auth_WPSAMLAuthPluginPage_section',
        __( '', 'wp-saml-auth' ),
        'wp_saml_auth_settings_section_callback',
        'WPSAMLAuthPluginPage'
    );

    add_settings_field(
        'wp_saml_auth_auto_provision',
        __( 'auto_provision', 'wp-saml-auth' ),
        'wp_saml_auth_auto_provision_render',
        'WPSAMLAuthPluginPage',
        'wp_saml_auth_WPSAMLAuthPluginPage_section'
    );

    add_settings_field(
        'wp_saml_auth_permit_wp_login',
        __( 'permit_wp_login', 'wp-saml-auth' ),
        'wp_saml_auth_permit_wp_login_render',
        'WPSAMLAuthPluginPage',
        'wp_saml_auth_WPSAMLAuthPluginPage_section'
    );
    add_settings_field(
        'wp_saml_auth_unauth_moodle_text',
        __( 'Anonymous button text', 'wp-saml-auth' ),
        'wp_saml_auth_unauth_moodle_text_render',
        'WPSAMLAuthPluginPage',
        'wp_saml_auth_WPSAMLAuthPluginPage_section'
    );
    add_settings_field(
        'wp_saml_auth_auth_moodle_text',
        __( 'Authenticated button text', 'wp-saml-auth' ),
        'wp_saml_auth_auth_moodle_text_render',
        'WPSAMLAuthPluginPage',
        'wp_saml_auth_WPSAMLAuthPluginPage_section'
    );

    function wp_saml_auth_auto_provision_render() {

        global $defaults;
        /*
            wpsa_filter_option('auto_provision')
            $defaults = get_option( 'wp_saml_auth_option' );
            $defaults = apply_filters(wp_saml_auth_option( null, $defaults['auto_provision'] ));

            <pre>wpsa_filter_option('', '$defaults[ $auto_provision]'): <?php print wpsa_filter_option('$defaults[ $auto_provision]'); ?></pre>
            <pre>wpsa_filter_option('$auto_provision'): <?php print wpsa_filter_option(null, $auto_provision); ?></pre>
            <pre>wpsa_filter_option(NULL, $defaults[$auto_provision]): <?php print wpsa_filter_option(NULL, $defaults[$auto_provision]); ?></pre>
            <pre>wp_saml_auth_option( '', $defaults['auto_provision'] ): <?php print wp_saml_auth_option( null, $defaults['auto_provision'] ); ?></pre>
            <pre>wp_saml_auth_option( null, $auto_provision ): <?php print wp_saml_auth_option( null, $auto_provision ); ?></pre>
            <pre>apply_filters( 'wp_saml_auth_option', null, $auto_provision ): <?php print apply_filters( 'wp_saml_auth_option', null, $auto_provision ); ?></pre>
            <pre>get_option( 'auto_provision' ): <?php print get_option( $auto_provision ); ?></pre>
            <pre>print $defaults:<?php global $defaults; print $defaults; ?></pre>
        */

        $options = get_option( 'wp_saml_auth_settings' );
//	$options['wp_saml_auth_auto_provision'] = $defaults['auto_provision'];
        ?>
        <pre>print $defaults:<?php print $defaults['auto_provision']; ?></pre>
        <input type='checkbox' name='wp_saml_auth_settings[wp_saml_auth_auto_provision]' <?php checked($options['wp_saml_auth_auto_provision'], 1); ?>' value='1'>
        <?php
        wpsa_filter_option(false, $defaults[ $auto_provision]);

    }

    function wp_saml_auth_permit_wp_login_render() {
        global $defaults;
        $options = get_option( 'wp_saml_auth_settings' );
        $options['wp_saml_auth_permit_wp_login'] = $defaults['permit_wp_login'];
        ?>
        <pre>$defaults['permit_wp_login']:<?php print $defaults['permit_wp_login']; ?></pre>
        <input type='checkbox' name='wp_saml_auth_settings[wp_saml_auth_permit_wp_login]' value='<?php echo (isset($options['wp_saml_auth_permit_wp_login'])) ? $options['wp_saml_auth_permit_wp_login'] : ''; ?>'>
        <?php
    }

    function wp_saml_auth_unauth_moodle_text_render() {
        global $defaults;
        $options = get_option( 'wp_saml_auth_settings' );
        $options['wp_saml_auth_permit_wp_login'] = $defaults['get_user_by'];
        ?>
        <pre>$defaults['get_user_by']:<?php print $defaults['get_user_by']; ?></pre>
        <input type='text' name='wp_saml_auth_settings[wp_saml_auth_unauth_moodle_text]' value='<?php echo (isset($options['wp_saml_auth_unauth_moodle_text'])) ? $options['wp_saml_auth_unauth_moodle_text'] : ''; ?>'>
        <?php
        if($options['wp_saml_auth_unauth_moodle_text'] = 'test'){
            wpsa_filter_option($options['wp_saml_auth_unauth_moodle_text'], $defaults[ $get_user_by]);
        }
    }

    function wp_saml_auth_auth_moodle_text_render() {
        global $defaults;
        $options = get_option( 'wp_saml_auth_settings' );
        ?>
        <input type='text' name='wp_saml_auth_settings[wp_saml_auth_auth_moodle_text]' value='<?php echo (isset($options['wp_saml_auth_auth_moodle_text'])) ? $options['wp_saml_auth_auth_moodle_text'] : ''; ?>'>
        <?php

    }

    function wp_saml_auth_settings_section_callback() {

        echo __( '<br><h2>Configure button links to Moodle</h2>', 'wp-saml-auth' );

    }
}
add_action( 'admin_init', 'wp_saml_auth_settings_init' );
/**
 * Initialize the WP SAML Auth plugin.
 *
 * Core logic for the plugin is in the WP_SAML_Auth class.
 */
require_once dirname( __FILE__ ) . '/inc/class-wp-saml-auth.php';
WP_SAML_Auth::get_instance();
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once dirname( __FILE__ ) . '/inc/class-wp-saml-auth-cli.php';
    WP_CLI::add_command( 'saml-auth', 'WP_SAML_Auth_CLI' );
}

/**
 * Password_Reset_Removed.
 */
class Password_Reset_Removed
{

    function __construct()
    {
        add_filter( 'show_password_fields', array( $this, 'wpsa_disable' ) );
        add_filter( 'allow_password_reset', array( $this, 'wpsa_disable' ) );
        add_filter( 'lostpassword_url', array( $this, 'custom_password_reset_url' ) );
//	add_filter( 'allow_password_reset', array( $this, 'disable_password_reset') );
    }


    function wpsa_disable()
    {
        if ( is_admin() ) {
            $userdata = wp_get_current_user();
            $user = new WP_User($userdata->ID);
            if ( !empty( $user->roles ) && is_array( $user->roles ) && $user->roles[0] == 'administrator' )
                return true;
        }
        return false;
    }

    function disable_password_reset() { return false; }

    function custom_password_reset_url() {
        $password_reset_url = 'http://www.codeaccesms.uqam.ca/';
        if(!empty($password_reset_url) ){
            return $password_reset_url;
            exit;
        } else {
            return '?action=lostpassword';
        }
    }

}

$pass_reset_removed = new Password_Reset_Removed();