<?php
/**
 * Fired during plugin activation
 *
 * @link       https://datamad.co.uk
 * @since      1.0.0
 *
 * @package    Widgets_On_Pages
 * @subpackage Widgets_On_Pages/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Widgets_On_Pages
 * @subpackage Widgets_On_Pages/includes
 * @author     Todd Halfpenny <todd@toddhalfpenny.com>
 */
class Widgets_On_Pages_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 * @param strgin $wop_plugin_version Version of our plugin.
	 */
	public static function activate( $wop_plugin_version ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-widgets-on-pages-admin.php';
		// flush_rewrite_rules();

		// First upgrade from pre v1 release check.
		// Read our CPTs, to see if they've already been created.
		$args = array( 'post_type' => 'turbo-sidebar-cpt', 'posts_per_page' => 100 );
		$loop = new WP_Query( $args );
		if ( ! $loop->have_posts() ) {
			// No CPTs, so first install / upgrade.
			// Create CPTs from exisiting WoP options.
			$options = get_option( 'wop_options_field' );
			$num_sidebars = $options['num_of_wop_sidebars'] + 1;

			// Handle the main wop sidebar.
			if ( '' != $options['wop_name_1'] ) :
				$name = $options['wop_name_1'];
			else :
				$name = '1';
			endif;
			$my_post = array(
				'post_title'    => wp_strip_all_tags( $name ),
				'post_content'  => '',
				'post_excerpt'  => '1',
				'post_status'   => 'publish',
				'post_type'     => 'turbo-sidebar-cpt',
			);
			// Insert the post into the database.
			wp_insert_post( $my_post );

			// Insert more CPTs for each legacy WoP Sidebar.
			// Note silly use of except.
			if ( $num_sidebars > 1 ) {
				for ( $sidebar = 2; $sidebar <= $num_sidebars; $sidebar++ ) {
					$option_id = 'wop_name_' . $sidebar;
					if ( '' != $options[ $option_id ] ) :
						$name = $options[ $option_id ];
					else :
						$name = $sidebar;
					endif;
					$my_post = array(
						'post_title'    => wp_strip_all_tags( $name ),
						'post_content'  => '',
						'post_excerpt'  => $sidebar,
						'post_status'   => 'publish',
						'post_type'     => 'turbo-sidebar-cpt',
					);
					// Insert the post into the database.
					wp_insert_post( $my_post );
				} // End for
			} // End $num_sidebar check
		}

		update_option( 'wop_plugin_version', $wop_plugin_version );
	}
}
