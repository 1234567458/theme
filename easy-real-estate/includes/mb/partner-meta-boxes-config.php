<?php
if ( ! function_exists( 'ere_partner_meta_boxes' ) ) :
	/**
	 * Contains partner's meta box declaration
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function ere_partner_meta_boxes( $meta_boxes ) {

		$meta_boxes[] = array(
			'id'         => 'partners-meta-box',
			'title'      => esc_html__( 'Partner Information', ERE_TEXT_DOMAIN ),
			'post_types' => array( 'partners' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name' => esc_html__( 'Website URL', ERE_TEXT_DOMAIN ),
					'id'   => "REAL_HOMES_partner_url",
					'desc' => esc_html__( 'Provide Website URL', ERE_TEXT_DOMAIN ),
					'type' => 'text',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'ere_partner_meta_boxes' );

endif;