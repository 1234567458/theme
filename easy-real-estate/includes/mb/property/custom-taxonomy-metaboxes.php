<?php
if ( ! function_exists( 'ere_property_type_meta_boxes' ) ) :
	/**
	 * Property type meta boxes declaration
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function ere_property_type_meta_boxes( $meta_boxes ) {

		$meta_boxes[] = array(
			'title'      => esc_html__( 'Custom Property Type Map Icon', ERE_TEXT_DOMAIN ),
			'taxonomies' => 'property-type',
			'fields'     => array(
				array(
					'name'             => esc_html__( 'Icon Image', ERE_TEXT_DOMAIN ),
					'id'               => 'inspiry_property_type_icon',
					'type'             => 'image_advanced',
					'mime_type'        => 'image/png',
					'max_file_uploads' => 1,
				),
				array(
					'name'             => esc_html__( 'Retina Icon Image', ERE_TEXT_DOMAIN ),
					'id'               => 'inspiry_property_type_icon_retina',
					'type'             => 'image_advanced',
					'mime_type'        => 'image/png',
					'max_file_uploads' => 1,
				),
			),
		);

		return $meta_boxes;
	}

	add_filter( 'rwmb_meta_boxes', 'ere_property_type_meta_boxes' );

endif;


if ( ! function_exists( 'ere_property_feature_meta_boxes' ) ) :
	/**
	 * Property feature meta boxes declaration
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function ere_property_feature_meta_boxes( $meta_boxes ) {

		$meta_boxes[] = array(
			'title'      => 'Property Feature Icon',
			'taxonomies' => 'property-feature',
			'fields'     => array(
				array(
					'name'             => esc_html__( 'Icon Image', ERE_TEXT_DOMAIN ),
					'desc'             => esc_html__( 'Recommended image size for icon is 64px by 64px.', ERE_TEXT_DOMAIN ),
					'id'               => 'inspiry_property_feature_icon',
					'type'             => 'image_advanced',
					'mime_type'        => 'image/png',
					'max_file_uploads' => 1,
				),
			),
		);

		return $meta_boxes;
	}

	add_filter( 'rwmb_meta_boxes', 'ere_property_feature_meta_boxes' );

endif;


if ( ! function_exists( 'ere_property_status_color_meta_boxes' ) ) :
	/**
	 * Property Status colors meta boxes declaration
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function ere_property_status_color_meta_boxes( $meta_boxes ) {

		$meta_boxes[] = array(
			'title' => esc_html__( 'Property Status Tag Colors', ERE_TEXT_DOMAIN ),
			'taxonomies' => 'property-status',
			'fields'     => array(
				array(
					'name'             => esc_html__( 'Status Tag Background Color', ERE_TEXT_DOMAIN ),
					'id'               => 'inspiry_property_status_bg',
					'type'             => 'color',
					'alpha_channel'    => true,
				),
				array(
					'name'             => esc_html__( 'Status Tag Text Color', ERE_TEXT_DOMAIN ),
					'id'               => 'inspiry_property_status_text',
					'type'             => 'color',
					'alpha_channel'    => true,
				),
			),
		);

		return $meta_boxes;
	}

	add_filter( 'rwmb_meta_boxes', 'ere_property_status_color_meta_boxes' );

endif;


if ( ! function_exists( 'ere_property_taxonomy_term_color_meta_boxes' ) ) {
	/**
	 * Taxonomies term color metaboxes declarations
	 *
	 * @since 2.2.0
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function ere_property_taxonomy_term_color_meta_boxes( $meta_boxes ) {

		$meta_boxes[] = array(
			'title'      => esc_html__( 'Term Color', ERE_TEXT_DOMAIN ),
			'taxonomies' => array(
				'property-status',
				'property-city',
				'property-type'
			),
			'fields'     => array(
				array(
					'name'          => esc_html__( '&nbsp;', ERE_TEXT_DOMAIN ),
					'desc'          => esc_html__( 'This color will be applied to charts depicting property statistics categorized by taxonomy terms on the Agents and Agencies detail page.', ERE_TEXT_DOMAIN ),
					'id'            => 'ere_property_taxonomy_term_color',
					'type'          => 'color',
					'alpha_channel' => true,
				)
			)
		);

		return $meta_boxes;
	}

	add_filter( 'rwmb_meta_boxes', 'ere_property_taxonomy_term_color_meta_boxes' );

}