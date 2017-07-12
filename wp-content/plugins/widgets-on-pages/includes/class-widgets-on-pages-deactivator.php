<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://datamad.co.uk
 * @since      1.0.0
 *
 * @package    Widgets_On_Pages
 * @subpackage Widgets_On_Pages/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Widgets_On_Pages
 * @subpackage Widgets_On_Pages/includes
 * @author     Todd Halfpenny <todd@toddhalfpenny.com>
 */
class Widgets_On_Pages_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.3
	 */
	public static function deactivate() {
		delete_option( 'wop_plugin_version' );
	}
}
