<?php
/**
 * Our template tags
 *
 * @link       https://datamad.co.uk
 * @since      1.0.0
 *
 * @package    Widgets_On_Pages
 * @subpackage Widgets_On_Pages/includes
 */

if ( ! function_exists( 'widgets_on_template' ) ) {
	/**
	 * Template tag for breadcrumbs.
	 *
	 * @param string $id  What to show before the breadcrumb.
	 *
	 * @return void
	 */
	function widgets_on_template( $id = '' ) {
		echo Widgets_On_Pages_Public::widgets_on_template( $id );
	}
}
?>
