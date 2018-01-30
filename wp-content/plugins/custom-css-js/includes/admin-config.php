<?php
/**
 * Custom CSS and JS
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * CustomCSSandJS_AdminConfig 
 */
class CustomCSSandJS_AdminConfig {

    var $settings_default;

    var $settings;

    /**
     * Constructor
     */
    public function __construct() {
        // Get the "default settings"
        $settings_default = apply_filters( 'ccj_settings_default', array() );

        // Get the saved settings
        $settings = get_option('ccj_settings');
        if ( $settings == false ) {
            $settings = $settings_default;
        } else {
            foreach( $settings_default as $_key => $_value ) {
                if ( ! isset($settings[$_key] ) ) {
                    $settings[$_key] = $_value;
                }
            }
        }
        $this->settings = $settings;
        $this->settings_default = $settings_default;

        //Add actions and filters
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );


        add_action( 'ccj_settings_form', array( $this, 'ccj_settings_form' ), 11 );
        add_filter( 'ccj_settings_default', array( $this, 'ccj_settings_default' ) );
        add_filter( 'ccj_settings_save', array( $this, 'ccj_settings_save' ) );
    }


    /**
     * Add submenu pages
     */
    function admin_menu() {
        $menu_slug = 'edit.php?post_type=custom-css-js';

        add_submenu_page( $menu_slug, __('Settings', 'custom-css-js'), __('Settings', 'custom-css-js'), 'manage_options', 'custom-css-js-config', array( $this, 'config_page' ) );

    }


    /**
     * Enqueue the scripts and styles
     */
    public function admin_enqueue_scripts( $hook ) {

        $screen = get_current_screen();

        // Only for custom-css-js post type
        if ( $screen->post_type != 'custom-css-js' ) 
            return false;

        if ( $hook != 'custom-css-js_page_custom-css-js-config' ) 
            return false;

        // Some handy variables
        $a = plugins_url( '/', CCJ_PLUGIN_FILE). 'assets';
        $v = CCJ_VERSION; 

        wp_enqueue_script( 'tipsy', $a . '/jquery.tipsy.js', array('jquery'), $v, false );
        wp_enqueue_style( 'tipsy', $a . '/tipsy.css', array(), $v );
    }



    /**
     * Template for the config page
     */
    function config_page() {

        if ( isset( $_POST['ccj_settings-nonce'] ) ) {
            check_admin_referer('ccj_settings', 'ccj_settings-nonce');

            $data = apply_filters( 'ccj_settings_save', array() );

            update_option( 'ccj_settings', $data );

        } else {
            $data = $this->settings;
        }

        ?>
        <div class="wrap">

        <?php $this->config_page_header('editor'); ?>

        <form action="<?php echo admin_url('edit.php'); ?>?post_type=custom-css-js&page=custom-css-js-config" id="ccj_settings" method="post">
        <table class="form-table">
                <?php do_action( 'ccj_settings_form' ); ?>

            <tr>
            <th>&nbsp;</th>
            <td>
            <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save'); ?>" />
            <?php wp_nonce_field('ccj_settings', 'ccj_settings-nonce', false); ?>
            </td>
            </tr>

        </table>
        </form>
        </div>
        <?php
    }


    /**
     * Template for config page header 
     */
    function config_page_header( $tab = 'editor' ) {
  
        $url = '?post_type=custom-css-js&page=custom-css-js-config';

        $active = array('editor' => '', 'general' => '', 'debug' => '');
        $active[$tab] = 'nav-tab-active';

        ?>
        <style type="text/css">.form-table th { width: 500px; } </style>
        <h1><?php _e('Custom CSS & JS Settings'); ?></h1>

        <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
            <a href="<?php echo $url; ?>" class="nav-tab <?php echo $active['editor']; ?>"><?php echo __('Editor Settings', 'custom-css-js'); ?></a>
        </h2>

        <?php     
    }

    /**
     * Add the default for the theme
     */
    function ccj_settings_default( $defaults ) {
        return array_merge( $defaults, array( 'ccj_htmlentities' => false) );
    }


    /**
     * Add the 'ccj_htmlentities' value to the $_POST for the Settings page
     */
    function ccj_settings_save( $data ) {
        $default['htmlentities'] = false;
        $htmlentities = isset($_POST['ccj_htmlentities']) ? true : false;

        return array_merge( $data, array('ccj_htmlentities' => $htmlentities) );
    }



    /**
     * Form for "Editor Theme" field
     */
    function ccj_settings_form() {

        // Get the setting
        $settings = get_option('ccj_settings');
        $htmlentities = (isset($settings['ccj_htmlentities']) && $settings['ccj_htmlentities']) ? true : false;


        $title = __('If you want to use an HTML entity in your code (for example '. htmlentities('&gt; or &quot;').'), but the editor keeps on changing them to its equivalent character (&gt; and &quot; for the previous example), then you might want to enable this option.', 'custom-css-js');
        $help = '<span class="dashicons dashicons-editor-help" rel="tipsy" title="'.$title.'"></span>';

        ?>
        <tr>
        <th scope="row"><label for="ccj_htmlentities"><?php _e('Keep the HTML entities, don\'t convert to its character', 'custom-css-js') ?> <?php echo $help; ?></label></th>
        <td><input type="checkbox" name="ccj_htmlentities" id = "ccj_htmlentities" value="1" <?php checked($htmlentities, true); ?> />
        </td>
        </tr>
        <?php
    }



}

return new CustomCSSandJS_AdminConfig();
