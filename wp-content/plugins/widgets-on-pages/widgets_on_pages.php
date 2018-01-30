<?php

/**
 * Widgets on Pages - FREE
 *
 * @link              https://datamad.co.uk
 * @since             1.0.0
 * @package           Widgets_On_Pages
 *
 * @wordpress-plugin
 * Plugin Name:       Widgets On Pages
 * Plugin URI:        https://datamad.co.uk/wordpress-plugins/widgets-on-pages/
 * Description:       The easiest way to Add Widgets or Sidebars to Posts and Pages using shortcodes or template tags.
 * Version:           1.4.0
 * Author:            Todd Halfpenny
 * Author URI:        http://toddhalfpenny.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       widgets-on-pages
 * Domain Path:       /languages
 */

if ( !function_exists( 'wop_fs' ) ) {
    /**
     * Freemius Stuff.
     * Create a helper function for easy SDK access.
     */
    function wop_fs()
    {
        global  $wop_fs ;
        
        if ( !isset( $wop_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wop_fs = fs_dynamic_init( array(
                'id'             => '1049',
                'slug'           => 'widgets-on-pages',
                'type'           => 'plugin',
                'public_key'     => 'pk_cc686be98cc9dc884d69bfce70cfc',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'    => 'widgets-on-pages',
                'contact' => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $wop_fs;
    }
    
    // Init Freemius.
    wop_fs();
    // Signal that SDK was initiated.
    do_action( 'wop_fs_loaded' );
    function wop_fs_custom_connect_message_on_update(
        $message,
        $user_first_name,
        $plugin_title,
        $user_login,
        $site_link,
        $freemius_link
    )
    {
        return sprintf(
            __fs( 'hey-x' ) . '<br>' . __( 'Thanks for your continuing use of %2$s. Please help us improve it even further by opting-in below.</br>By doing so, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'widgets-on-pages' ),
            $user_first_name,
            '<b>' . $plugin_title . '</b>',
            '<b>' . $user_login . '</b>',
            $site_link,
            $freemius_link
        );
    }
    
    wop_fs()->add_filter(
        'connect_message_on_update',
        'wop_fs_custom_connect_message_on_update',
        10,
        6
    );
    // If this file is called directly, abort.
    if ( !defined( 'WPINC' ) ) {
        die;
    }
    if ( !defined( 'WOP_PLUGIN_VERSION' ) ) {
        define( 'WOP_PLUGIN_VERSION', '1.4.0' );
    }
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-widgets-on-pages-activator.php
     *
     * @param strgin $wop_plugin_version Version of our plugin.
     */
    function activate_widgets_on_pages( $wop_plugin_version )
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-widgets-on-pages-activator.php';
        Widgets_On_Pages_Activator::activate( $wop_plugin_version );
    }
    
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-widgets-on-pages-deactivator.php
     */
    function deactivate_widgets_on_pages()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-widgets-on-pages-deactivator.php';
        Widgets_On_Pages_Deactivator::deactivate();
    }
    
    register_activation_hook( __FILE__, 'activate_widgets_on_pages' );
    register_deactivation_hook( __FILE__, 'deactivate_widgets_on_pages' );
    /**
     * Also check if we have updated - note activation hook not fired upon updates
     */
    function wop_plugin_check_version()
    {
        if ( WOP_PLUGIN_VERSION !== get_option( 'wop_plugin_version' ) ) {
            activate_widgets_on_pages( WOP_PLUGIN_VERSION );
        }
    }
    
    add_action( 'plugins_loaded', 'wop_plugin_check_version' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-widgets-on-pages.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_widgets_on_pages()
    {
        $plugin = new Widgets_On_Pages( WOP_PLUGIN_VERSION );
        $plugin->run();
    }
    
    run_widgets_on_pages();
} else {
    $args = array(
        'back_link' => true,
    );
    wp_die( '<h1>Activation Failed</h1><p>Sorry, looks like you already have another version of Widgets on Pages already activated.</p><p>Please deactivate any other versions before re-attempting to activate</p>', 'Plugin activation failed', $args );
}
