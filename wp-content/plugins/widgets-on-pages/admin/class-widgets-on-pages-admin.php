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
        // Shortcode/Template Tag Custom Meta on Turbo Sidebar CTP.
        add_action( 'load-post.php', array( $this, 'wop_load_post_hook' ) );
        add_filter(
            'contextual_help',
            array( $this, 'wop_plugin_help' ),
            10,
            3
        );
        // WYSIWYG Tiny MCE.
        add_action( 'admin_head', array( $this, 'wop_add_my_tc_button' ) );
        add_action( 'wp_ajax_twd_cpt_list', array( $this, 'twd_list_ajax' ) );
        add_action( 'admin_footer', array( $this, 'twd_cpt_list' ) );
        if ( wop_fs()->is_not_paying() ) {
            // Auto Insert Turbo Sidebar PRO-feature Custom Meta.
            add_action( 'add_meta_boxes', array( $this, 'wop_add_free_custom_meta' ) );
        }
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
            // Note, this is the same as above to remove dupe link.
            array( $this, 'display_options_page' )
        );
        // Sub menu page -> Turbo Sidebar.
        $this->wop_turbo_sidebars_screen_id = add_submenu_page(
            $this->plugin_name,
            'Turbo Sidebars',
            'Turbo Sidebars',
            'edit_posts',
            'edit.php?post_type=turbo-sidebar-cpt'
        );
    }
    
    /**
     * Register our setting
     *
     * @since    1.0.0
     */
    function wop_register_settings()
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
     * Hook to add action for shortcode / template tag meta boxes
     * Note: We have this seprate function for the action as we saw this error
     * 	https://wordpress.org/support/topic/warning-call_user_func_array-expects-parameter-1-to-be-a-valid-callback-13/#post-9420083
     *
     * @since  1.3.0
     */
    public function wop_load_post_hook()
    {
        add_action( 'add_meta_boxes', array( $this, 'wop_add_edit_only_custom_meta' ) );
    }
    
    /**
     * Adds meta boxes from admin screen (Shortcode and Template Tag)
     *
     * @since  1.1.0
     */
    public function wop_add_edit_only_custom_meta()
    {
        // Shortcode & Template Tag- for info and copying.
        add_meta_box(
            'wop-cpt-shortcode-meta-box',
            __( 'Shortcode / Template Tag', 'widgets-on-pages' ),
            array( $this, 'cpt_shortcode_meta_box_markup' ),
            'turbo-sidebar-cpt',
            'side',
            'high',
            null
        );
    }
    
    /**
     * Adds meta boxes from admin screen
     *
     * @since  1.1.0
     */
    public function wop_add_custom_meta()
    {
    }
    
    /**
     * Adds meta boxes from admin screen
     *
     * @since  1.3.0
     */
    public function wop_add_free_custom_meta()
    {
        // Auto Insert.
        add_meta_box(
            'wop-cpt-autoinsert-free-meta-box',
            __( 'Auto Insert', 'widgets-on-pages' ),
            array( $this, 'cpt_autoinsert_free_meta_box_markup' ),
            'turbo-sidebar-cpt',
            'normal',
            'low',
            null
        );
    }
    
    /**
     * Shortcode metabox markup
     *
     * @param object $post Our WP post.
     * @since 1.1.0
     */
    public function cpt_shortcode_meta_box_markup( $post )
    {
        echo  __( '<h4>Shortcode</h4><p>Use this shortcode in your post/page</h4>', 'widgets-on-pages' ) ;
        $shortcode_id = '[widgets_on_pages id="' . $post->post_title . '"]';
        ?>
		<?php 
        echo  '<p id="wop-shortcode">' . $shortcode_id . '</p><button type="button" id="bq_copy_sc" value="Copy Shortcode" class="button-secondary" />Copy Shortcode</button>' ;
        echo  '<section><h3>' . esc_html__( 'Insert using the visual editor', 'widgets-on-pages' ) . '</h3>' ;
        echo  '<p>' . esc_html__( 'Use the visual editor to add Turbo Sidebars.', 'widgets-on-pages' ) ;
        if ( wop_fs()->is_not_paying() ) {
            echo  '<p><a href="' . wop_fs()->get_upgrade_url() . '">' . esc_html__( 'Upgrade Now', 'widgets-on-pages' ) . '</a>' . esc_html__( ' And you can arrange widgets in columns, too!', 'widgets-on-pages' ) ;
        }
        echo  '</section>' ;
        echo  __( '<h4>Template Tag</h4><p>Use this code to include the sidebar in your theme.</h4>', 'widgets-on-pages' ) ;
        $shortcode_id = esc_html( '<?php widgets_on_template("' . $post->post_title . '");?>' );
        echo  '<p id="wop-template-tag">' . $shortcode_id . '</p><button type="button" id="bq_copy_tt" value="Copy Shortcode" class="button-secondary" />Copy PHP</button>' ;
    }
    
    /**
     * Auto Insert PRO INFO metabox markup
     *
     * @since 1.3.0
     */
    public function cpt_autoinsert_free_meta_box_markup()
    {
        // Show our custom meta options.
        ?>
		<div class='inside'>
			<?php 
        echo  '<h4>' . esc_html__( 'Auto Insert options is a Widgets on Pages PRO feature', 'widgets-on-pages' ) . '</h4><p>' . esc_html__( 'To auto-insert widgets into your theme\'s header, before-or-after page content, or into your theme\'s footer you need ', 'widgets-on-pages' ) . '<a href="' . wop_fs()->get_upgrade_url() . '">Widgets on Pages PRO.</a></p><p><a href="' . wop_fs()->get_upgrade_url() . '">' . esc_html__( 'Upgrade now', 'widgets-on-pages' ) . '</a> ' . esc_html( 'to access these features (and more), updates and priority support', 'widgets-on-pages' ) . '</p>' ;
        echo  '<a class="button-primary" href="' . wop_fs()->get_upgrade_url() . '">' . esc_html__( 'Get PRO Features', 'widgets-on-pages' ) . '</a>' ;
        ?>
		</div>
		<hr/>
		<div class='inside'>
			<h3><?php 
        _e( 'Auto Insert', 'widgets-on-pages' );
        ?></h3>
			<p>
				<input type="radio"  disabled /> Yes<br />
				<input type="radio"  disabled/> No
			</p>
		</div>

		<div class='inside'>
			<h3><?php 
        _e( 'Position', 'widgets-on-pages' );
        ?></h3>
			<p>
				<input type="radio" disabled /> Before Header<br />
				<input type="radio" disabled /> After Header
			</p>
			<p>
				<input type="radio" disabled /> Before Content<br />
				<input type="radio" disabled /> After Content
			</p>
			<p>
				<input type="radio" disabled /> Before Footer<br />
				<input type="radio" disabled /> After Footer
			</p>
			</p>
		</div>

		<div class='inside'>
			<h3><?php 
        _e( 'Show on Posts / Pages', 'widgets-on-pages' );
        ?></h3>
			<p>
				<input type="radio" disabled /> Posts<br />
				<input type="radio" disabled /> Pages<br />
				<input type="radio" disabled /> Posts &amp; Pages
			</p>
		</div>

		<div class='inside'>
			<h3><?php 
        _e( 'Layout Options', 'widgets-on-pages' );
        ?></h3>
			<p><?php 
        _e( 'Number of widget columms per screen size', 'widgets-on-pages' );
        ?></p>
			<p><label><?php 
        _e( 'Small Screen', 'widgets-on-pages' );
        ?></label>
				<select>
				    <option value="1" selected>1</option>
				    <option value="2">2</option>
				    <option value="3">3</option>
				    <option value="4">4</option>
				</select>
			</p>
			<p><label><?php 
        _e( 'Medium Screen', 'widgets-on-pages' );
        ?></label>
				<select>
				    <option value="1">1</option>
				    <option value="2">2</option>
				    <option value="3">3</option>
				    <option value="4">4</option>
				</select>
			</p>
			<p><label><?php 
        _e( 'Large Screen', 'widgets-on-pages' );
        ?></label>
				<select>
				    <option value="1">1</option>
				    <option value="2">2</option>
				    <option value="3">3</option>
				    <option value="4">4</option>
				</select>
			</p>
			<p><label><?php 
        _e( 'Wide Screen', 'widgets-on-pages' );
        ?></label>
				<select>
				    <option value="1"></option>
				    <option value="2"></option>
				    <option value="3"></option>
				    <option value="4"></option>
				</select>
			</p>

		<?php 
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
        $opts['exclude_from_search'] = true;
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
        // Note: not using WP_Query as can cause pages to not display (e.g. Manage
        // Subscriptions link with Subscribe to Comments Reloaded - https://core.trac.wordpress.org/ticket/18408).
        $myposts = get_posts( $args );
        foreach ( $myposts as $post ) {
            setup_postdata( $post );
            
            if ( is_numeric( $post->post_name ) ) {
                $name = 'Widgets on Pages ' . $post->post_name;
                $shortcode_id = $post->post_name;
                $id = 'wop-' . $post->post_name;
            } else {
                $name = $post->post_title;
                $id = 'wop-' . $post->post_name;
                $shortcode_id = $post->post_title;
            }
            
            if ( '' != $post->post_excerpt ) {
                $id = 'wop-' . $post->post_excerpt;
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
     * @param string $hook Name of our hook.
     * @since    1.0.0
     */
    public function enqueue_scripts( $hook )
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
        // Load our JS for Turbo Sidebars admin screen.
        
        if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
            $screen = get_current_screen();
            
            if ( is_object( $screen ) && 'turbo-sidebar-cpt' == $screen->post_type ) {
                wp_enqueue_script(
                    $this->plugin_name,
                    plugin_dir_url( __FILE__ ) . 'js/wop-cpt-admin.js',
                    array( 'jquery' ),
                    $this->version,
                    true
                );
                wp_enqueue_script(
                    $this->plugin_name . '_prem',
                    plugin_dir_url( __FILE__ ) . 'js/wop-cpt-admin__premium_only.js',
                    array( 'jquery' ),
                    $this->version,
                    true
                );
            }
        
        }
    
    }
    
    /**
     * Adds a button to the TinyMCE editor.
     */
    public function wop_add_my_tc_button()
    {
        global  $typenow ;
        // Check user permissions.
        if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
            return;
        }
        // Verify the post type.
        if ( !in_array( $typenow, array( 'post', 'page' ) ) ) {
            return;
        }
        // Check if WYSIWYG is enabled.
        
        if ( get_user_option( 'rich_editing' ) == 'true' ) {
            add_filter( 'mce_external_plugins', array( $this, 'wop_add_tinymce_plugin' ) );
            add_filter( 'mce_buttons', array( $this, 'wop_register_my_tc_button' ) );
        }
    
    }
    
    /**
     * Sets up the link from our button to our JS.
     *
     * @param  array $plugin_array Exsting plugin array.
     * @return arry               Our updated plugin array.
     */
    public function wop_add_tinymce_plugin( $plugin_array )
    {
        $plugin_array['wop_tc_button'] = plugins_url( '/js/wop-tinymce-plugin.js', __FILE__ );
        return $plugin_array;
    }
    
    /**
     * Add our TinyMCE button.
     *
     * @param  array $buttons Existing array of buttons.
     * @return array          Updated array of buttons.
     */
    public static function wop_register_my_tc_button( $buttons )
    {
        array_push( $buttons, 'wop_tc_button' );
        return $buttons;
    }
    
    /**
     * Function to fetch buttons
     *
     * @since  1.1.0
     */
    public function twd_list_ajax()
    {
        // Check for nonce.
        check_ajax_referer( 'twd-nonce', 'security' );
        $list = array();
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
            
            if ( '' != get_the_excerpt( $loop->post ) ) {
                $id = 'wop-' . get_the_excerpt( $loop->post );
            }
            $list[] = array(
                'text'  => $name,
                'value' => $id,
            );
        }
        echo  wp_send_json( $list ) ;
        wp_die();
        // This is required to terminate immediately and return a proper response.
    }
    
    /**
     * Function to output button list ajax script
     *
     * @since  1.1.0
     */
    public function twd_cpt_list()
    {
        // Create nonce.
        global  $pagenow ;
        
        if ( 'admin.php' != $pagenow ) {
            $nonce = wp_create_nonce( 'twd-nonce' );
            $notPaying = false;
            if ( wop_fs()->is_not_paying() ) {
                $notPaying = true;
            }
            ?><script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					var data = {
						'action'	: 'twd_cpt_list', // wp ajax action
						'security'	: '<?php 
            echo  $nonce ;
            ?>' // nonce value created earlier
					};
					// Fire ajax.
						jQuery.post( ajaxurl, data, function( response ) {
							console.log("WOP", response);
							// If nonce fails then not authorized else settings saved.
							if( response === '-1' ){
								// Do nothing.
								console.log('error');
							} else {
								if (typeof(tinyMCE) != 'undefined') {
									if (tinyMCE.activeEditor != null) {
										tinyMCE.activeEditor.settings.cptPostsList = response;
										tinyMCE.activeEditor.settings.notPaying = <?php 
            echo  $notPaying ;
            ?>;
									}
								}
							}
						});
				});
			</script>
	<?php 
        }
    
    }

}