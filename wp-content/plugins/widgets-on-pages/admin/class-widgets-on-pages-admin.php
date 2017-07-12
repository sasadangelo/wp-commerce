<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Widgets_On_Pages
 * @subpackage Widgets_On_Pages/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Widgets_On_Pages
 * @subpackage Widgets_On_Pages/admin
 * @author     Todd Halfpenny <todd@toddhalfpenny.com>
 */
class Widgets_On_Pages_Admin
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
     * @param string $plugin_name 	The name of this plugin.
     * @param string $version				The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        // TODO - Move these filters and hooks out of the constructor.
        add_filter(
            'plugin_action_links' . plugin_basename( __FILE__ ),
            array( $this, 'wop_plugin_action_links' ),
            10,
            2
        );
        add_filter(
            'plugin_row_meta',
            array( $this, 'wop_register_plugins_links' ),
            10,
            2
        );
        add_action( 'admin_menu', array( $this, 'wop_add_options_page' ) );
        add_action( 'admin_init', array( $this, 'wop_register_settings' ) );
        add_action( 'widgets_init', array( $this, 'wop_register_sidebar' ) );
        add_action( 'admin_menu', array( $this, 'wop_remove_hidden_meta' ) );
        add_action( 'add_meta_boxes', array( $this, 'wop_add_custom_meta' ) );
        add_filter(
            'contextual_help',
            array( $this, 'wop_plugin_help' ),
            10,
            3
        );
    }
    
    /**
     * Sets "Settings" link on listing in Plugins screen.
     *
     * @since    1.0.0
     * @param array $links Array of links from plugins admin screen.
     */
    public function wop_plugin_action_links( $links )
    {
        return array_merge( array(
            'settings' => '<a href="' . admin_url( '/options-general.php?page=widgets-on-pages' ) . '">' . __( 'Settings', 'widgets-on-pages' ) . '</a>',
        ), $links );
    }
    
    /**
     * Adds extra links under plugin description in listing on Plugins screen.
     *
     * @since    1.0.0
     * @param array  $links Array of links from plugins admin screen.
     * @param string $file The plugin file name being referenced.
     */
    public function wop_register_plugins_links( $links, $file )
    {
        
        if ( strpos( $file, $this->plugin_name ) !== false ) {
            $new_links = array(
                'donate' => '<a href="https://datamad.co.uk/donate.php" target="_blank">Donate</a>',
                'doc'    => '<a href="https://datamad.co.uk/widgets-on-pages" target="_blank">Documentation</a>',
            );
            $links = array_merge( $links, $new_links );
        }
        
        return $links;
    }
    
    /**
     * Adds Admin Menu item.
     *
     * @since    1.0.0
     */
    public function wop_add_options_page()
    {
        // Top level menu -> Directs to Turbo Sidebar listsing.
        add_menu_page(
            __( 'Widgets on Pages Settings', 'widgets-on-pages' ),
            __( 'Widgets on Pages', 'widgets-on-pages' ),
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_options_page' ),
            'dashicons-feedback'
        );
        // Sub menu page -> Settings. Note this appears as 1st option to remove
        // duplicate entry.
        $this->wop_option_screen_id = add_submenu_page(
            $this->plugin_name,
            'Widgets on Pages Settings',
            'Settings',
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_options_page' )
        );
        // Sub menu page -> Turbo Sidebar.
        $this->wop_turbo_sidebars_screen_id = add_submenu_page(
            $this->plugin_name,
            'Turbo Sidebars',
            'Turbo Sidebars',
            'manage_options',
            'edit.php?post_type=turbo-sidebar-cpt'
        );
    }
    
    /**
     * Register our setting
     *
     * @since    1.0.0
     */
    public function wop_register_settings()
    {
        register_setting( 'wop_options', 'wop_options_field' );
    }
    
    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_options_page()
    {
        include_once 'partials/widgets-on-pages-admin-display.php';
    }
    
    /**
     * Render the options page for plugin
     *
     * @param string	$text The old help.
     * @param string	$screen_id Unique string id of the screen.
     * @param WP_Screen $screen Current WP_Screen instance.
     * @since  1.0.0
     */
    public function wop_plugin_help( $text, $screen_id, $screen )
    {
        
        if ( $screen_id == $this->wop_option_screen_id ) {
            $text = '<h5>Need help with the Widgets on Pages plugin?</h5>';
            $text .= '<p>Check out the documentation and support forums for help with this plugin.</p>';
            $text .= '<a href="http://wordpress.org/extend/plugins/widgets-on-pages/">Documentation</a><br /><a href="https://wordpress.org/support/plugin/widgets-on-pages/">Support forums</a>';
        }
        
        return $text;
    }
    
    /**
     * Removes meta boxes from admin screen
     *
     * @since  1.1.0
     */
    public function wop_remove_hidden_meta()
    {
        remove_meta_box( 'postexcerpt', 'turbo-sidebar-cpt', 'normal' );
    }
    
    /**
     * Adds meta boxes from admin screen
     *
     * @since  1.1.0
     */
    public function wop_add_custom_meta()
    {
        add_meta_box(
            'wop-cpt-shortcode-meta-box',
            'Shortcode',
            array( $this, 'cpt_shortcode_meta_box_markup' ),
            'turbo-sidebar-cpt',
            'side',
            'high',
            null
        );
    }
    
    /**
     * Shortcode metabox markup
     *
     * @param object $object Our WP post.
     * @since 1.1.0
     */
    public function cpt_shortcode_meta_box_markup( $object )
    {
        echo  __( '<p>Use this shortcode in your post/page</p>', 'widgets-on-pages' ) ;
        $shortcode_id = '[widgets_on_pages id="' . $object->post_title . '"]';
        echo  '<p id="wop-shortcode">' . $shortcode_id . '</p>' ;
    }
    
    /**
     * Creates a new Turbo Sidebars custom post type
     *
     * @since 	1.0.0
     * @uses 	register_post_type()
     */
    public static function wop_cpt_turbo_sidebars()
    {
        $cap_type = 'post';
        $plural = 'Turbo Sidebars';
        $single = 'Turbo Sidebar';
        $cpt_name = 'turbo-sidebar-cpt';
        $opts['can_export'] = true;
        $opts['capability_type'] = $cap_type;
        $opts['description'] = '';
        $opts['exclude_from_search'] = false;
        $opts['has_archive'] = false;
        $opts['hierarchical'] = false;
        $opts['map_meta_cap'] = true;
        $opts['menu_icon'] = 'dashicons-welcome-widgets-menus';
        $opts['menu_position'] = 60;
        $opts['public'] = false;
        $opts['publicly_querable'] = false;
        $opts['query_var'] = true;
        $opts['register_meta_box_cb'] = '';
        $opts['rewrite'] = false;
        $opts['show_in_admin_bar'] = false;
        $opts['show_in_menu'] = 'admin.php?page=widgets-on-pages';
        // $opts['show_in_menu']							= true;
        $opts['show_in_nav_menu'] = false;
        $opts['show_ui'] = true;
        $opts['supports'] = array( 'title', 'excerpt' );
        $opts['taxonomies'] = array();
        $opts['capabilities']['delete_others_posts'] = "delete_others_{$cap_type}s";
        $opts['capabilities']['delete_post'] = "delete_{$cap_type}";
        $opts['capabilities']['delete_posts'] = "delete_{$cap_type}s";
        $opts['capabilities']['delete_private_posts'] = "delete_private_{$cap_type}s";
        $opts['capabilities']['delete_published_posts'] = "delete_published_{$cap_type}s";
        $opts['capabilities']['edit_others_posts'] = "edit_others_{$cap_type}s";
        $opts['capabilities']['edit_post'] = "edit_{$cap_type}";
        $opts['capabilities']['edit_posts'] = "edit_{$cap_type}s";
        $opts['capabilities']['edit_private_posts'] = "edit_private_{$cap_type}s";
        $opts['capabilities']['edit_published_posts'] = "edit_published_{$cap_type}s";
        $opts['capabilities']['publish_posts'] = "publish_{$cap_type}s";
        $opts['capabilities']['read_post'] = "read_{$cap_type}";
        $opts['capabilities']['read_private_posts'] = "read_private_{$cap_type}s";
        $opts['labels']['add_new'] = esc_html__( "Add New {$single}", 'now-widgets-on-pages' );
        $opts['labels']['add_new_item'] = esc_html__( "Add New {$single}", 'widgets-on-pages' );
        $opts['labels']['all_items'] = esc_html__( $plural, 'widgets-on-pages' );
        $opts['labels']['edit_item'] = esc_html__( "Edit {$single}", 'widgets-on-pages' );
        $opts['labels']['menu_name'] = esc_html__( $plural, 'widgets-on-pages' );
        $opts['labels']['name'] = esc_html__( $plural, 'widgets-on-pages' );
        $opts['labels']['name_admin_bar'] = esc_html__( $single, 'widgets-on-pages' );
        $opts['labels']['new_item'] = esc_html__( "New {$single}", 'widgets-on-pages' );
        $opts['labels']['not_found'] = esc_html__( "No {$plural} Found", 'widgets-on-pages' );
        $opts['labels']['not_found_in_trash'] = esc_html__( "No {$plural} Found in Trash", 'widgets-on-pages' );
        $opts['labels']['parent_item_colon'] = esc_html__( "Parent {$plural} :", 'widgets-on-pages' );
        $opts['labels']['search_items'] = esc_html__( "Search {$plural}", 'widgets-on-pages' );
        $opts['labels']['singular_name'] = esc_html__( $single, 'widgets-on-pages' );
        $opts['labels']['view_item'] = esc_html__( "View {$single}", 'widgets-on-pages' );
        $opts['rewrite']['ep_mask'] = EP_PERMALINK;
        $opts['rewrite']['feeds'] = false;
        $opts['rewrite']['pages'] = true;
        $opts['rewrite']['slug'] = esc_html__( strtolower( $plural ), 'widgets-on-pages' );
        $opts['rewrite']['with_front'] = false;
        $opts = apply_filters( 'turbo-sidebars-cpt-options', $opts );
        register_post_type( strtolower( $cpt_name ), $opts );
    }
    
    /**
     * Register the sidebars, based upon our Turbo Sidebars.
     *
     * @since    1.0.0
     */
    public function wop_register_sidebar()
    {
        // Register my sidebars.
        $args = array(
            'post_type'      => 'turbo-sidebar-cpt',
            'posts_per_page' => 100,
        );
        $loop = new WP_Query( $args );
        while ( $loop->have_posts() ) {
            $loop->the_post();
            
            if ( is_numeric( $loop->post->post_name ) ) {
                $name = 'Widgets on Pages ' . $loop->post->post_name;
                $shortcode_id = $loop->post->post_name;
                $id = 'wop-' . $loop->post->post_name;
            } else {
                $name = $loop->post->post_title;
                $id = 'wop-' . $loop->post->post_name;
                $shortcode_id = $loop->post->post_title;
            }
            
            if ( '' != $loop->post->post_excerpt ) {
                $id = 'wop-' . $loop->post->post_excerpt;
            }
            $desc = 'Widgets on Pages sidebar. Use shortcode';
            register_sidebar( array(
                'name'          => $name,
                'id'            => $id,
                'description'   => __( $desc, 'widgets-on-pages' ) . ' [widgets_on_pages id="' . $shortcode_id . '"]',
                'class'         => 'turbo-sidebar',
                'before_widget' => '<li id="%1$s" class="widget %2$s">',
                'after_widget'  => '</li>',
                'before_title'  => '<h2 class="widgettitle">',
                'after_title'   => '</h2>',
            ) );
        }
    }
    
    /**
     * Register the stylesheets for the admin area.
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
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/widgets-on-pages-admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
    }

}