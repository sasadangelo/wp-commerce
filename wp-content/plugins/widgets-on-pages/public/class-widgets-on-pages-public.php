<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://datamad.co.uk
 * @since      1.0.0
 *
 * @package    Widgets_On_Pages
 * @subpackage Widgets_On_Pages/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Widgets_On_Pages
 * @subpackage Widgets_On_Pages/public
 * @author     Todd Halfpenny <todd@toddhalfpenny.com>
 */
class Widgets_On_Pages_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param string $plugin_name       The name of the plugin.
     * @param string $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->widgets_on_template();
        add_shortcode( 'widgets_on_pages', array( $this, 'widgets_on_page' ) );
    }
    
    /**
     * Our lovely shortcode.
     *
     * @param array $atts Should contain '$id' that should match to Turbo Sidebar.
     * @since    1.0.0
     */
    public static function widgets_on_page( $atts )
    {
        extract( shortcode_atts( array(
            'id'     => '1',
            'tiny'   => '1',
            'small'  => '1',
            'medium' => '1',
            'large'  => '1',
            'wide'   => '1',
        ), $atts ) );
        $str = "<div id='" . str_replace( ' ', '_', $id ) . "' class='widgets_on_page wop_tiny" . $tiny . '  wop_small' . $small . '  wop_medium' . $medium . '  wop_large' . $large . '  wop_wide' . $wide . "'>\n\t\t\t<ul>";
        // Legacy bullshit.
        if ( is_numeric( $id ) ) {
            $id = 'wop-' . $id;
        }
        ob_start();
        
        if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( $id ) ) {
            $my_str = ob_get_contents();
        } else {
            // Ouput somethign nice to the source.
            $my_str = '<!-- ERROR NO TURBO SIDEBAR FOUND WITH ID ' . $id . '-->';
        }
        
        ob_end_clean();
        $str .= $my_str;
        $str .= '</ul></div><!-- widgets_on_page -->';
        return $str;
    }
    
    /**
     * Our lovely template tage handler.
     *
     * @param string $id Id that should match the ID of our Turbo Sidebar.
     * @since    1.0.0
     */
    public static function widgets_on_template( $id = '1' )
    {
        $arr = array(
            'id' => $id,
        );
        return Widgets_On_Pages_Public::widgets_on_page( $arr );
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Widgets_On_Pages_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Widgets_On_Pages_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $options = get_option( 'wop_options_field' );
        if ( !is_array( $options ) ) {
            $options = array();
        }
        
        if ( array_key_exists( 'enable_css', $options ) ) {
            $tmp = get_option( 'wop_options_field' );
            $enable_css = $tmp['enable_css'];
            if ( $enable_css ) {
                wp_enqueue_style(
                    $this->plugin_name,
                    plugin_dir_url( __FILE__ ) . 'css/widgets-on-pages-public.css',
                    array(),
                    $this->version,
                    'all'
                );
            }
        }
    
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
    }

}