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

/**
 * Ajax handler to maybe return TurboSidebat content for inclusion in header/footer
 *
 * @since 1.3.0
 */
function wop_maybe_insert_with_header() {
	$pst_id = intval( $_POST['post_id'] );

	$args = array(
				'post_type' => 'turbo-sidebar-cpt',
				'posts_per_page' => 50,
				'meta_query' => array(
	      	'relation' => 'AND',
	        array(
						'key' => '_wop_auto_insert',
						'value' => 0,
	        ),
	        array(
						'key' => '_wop_before_after',
						'value' => array( '2', '3', '4', '5' ), // Before header, afetr header, before footer.
						'compare' => 'IN',
	        ),
				),
			);
	$potential_turbo_sidebars = get_posts( $args );

	// Check if we should exclude for this post_id.
	$pst_exclude = get_post_meta( $pst_id, '_wop_exclude', true );
	if ( $pst_exclude ) {
		wp_die();
	}

	// Check if we should show for this post type.
	$valid_post_types = $potential_turbo_sidebars[0]->_wop_valid_post_types;
	if ( 'all' == $valid_post_types ) {
		$valid_post_type = true;
	} else {
		$pst_type = get_post_type( $pst_id );
		if ( $pst_type == $valid_post_types ) {
			$valid_post_type = true;
		} else {
			$valid_post_type = false;
		}
	}

	if ( $valid_post_type ) {
		$arr = array(
			'id' => $potential_turbo_sidebars[0]->post_title,
			'small' => $potential_turbo_sidebars[0]->_wop_cols_small,
			'medium' => $potential_turbo_sidebars[0]->_wop_cols_medium,
			'large' => $potential_turbo_sidebars[0]->_wop_cols_large,
			'wide' => $potential_turbo_sidebars[0]->_wop_cols_wide,
			);
		echo $potential_turbo_sidebars[0]->_wop_before_after . 'wop--part' . Widgets_On_Pages_Public::widgets_on_page( $arr );
	} else {
		wp_die();
	}
	wp_die();
}

add_action( 'wp_ajax_wop_maybe_insert_with_header', 'wop_maybe_insert_with_header' );

?>
