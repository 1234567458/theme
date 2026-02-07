<?php
/**
 * Add floorplans metabox tab to property
 *
 * @param $property_metabox_tabs
 *
 * @return array
 */
function ere_floorplans_metabox_tab( $property_metabox_tabs ) {
	if ( is_array( $property_metabox_tabs ) ) {
		$property_metabox_tabs['floor-plans'] = array(
			'label' => esc_html__( 'Floor Plans', ERE_TEXT_DOMAIN ),
			'icon'  => 'dashicons-layout',
		);
	}

	return $property_metabox_tabs;
}

add_filter( 'ere_property_metabox_tabs', 'ere_floorplans_metabox_tab', 40 );


/**
 * Add floorplans metaboxes fields to property
 *
 * @param $property_metabox_fields
 *
 * @return array
 */
function ere_floorplans_metabox_fields( $property_metabox_fields ) {

	$ere_floorplans_fields = array(
		array(
			'id'         => 'inspiry_floor_plans',
			'type'       => 'group',
			'columns'    => 12,
			'clone'      => true,
			'sort_clone' => true,
			'tab'        => 'floor-plans',
			'add_button' => esc_html__( '+ Add More', ERE_TEXT_DOMAIN ),
			'fields'     => array(
				array(
					'name' => esc_html__( 'Floor Name', ERE_TEXT_DOMAIN ),
					'id'   => 'inspiry_floor_plan_name',
					'desc' => esc_html__( 'Example: Ground Floor', ERE_TEXT_DOMAIN ),
					'type' => 'text',
					'columns' => 6,
				),
				array(
					'name' => esc_html__( 'Description', ERE_TEXT_DOMAIN ),
					'id'   => 'inspiry_floor_plan_descr',
					'type' => 'textarea',
					'columns' => 6,
				),
				array(
					'name'    => esc_html__( 'Floor Price ( Only digits )', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_floor_plan_price',
					'desc'    => esc_html__( 'Example: 4000', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
				),
				array(
					'name'    => esc_html__( 'Price Postfix', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_floor_plan_price_postfix',
					'desc'    => esc_html__( 'Example: Per Month or Per Night', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
				),
				array(
					'name'    => esc_html__( 'Floor Size ( Only digits )', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_floor_plan_size',
					'desc'    => esc_html__( 'Example: 2500', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
				),
				array(
					'name'    => esc_html__( 'Size Postfix', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_floor_plan_size_postfix',
					'desc'    => esc_html__( 'Example: sq ft', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
				),
				array(
					'name'    => esc_html__( 'Bedrooms', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_floor_plan_bedrooms',
					'desc'    => esc_html__( 'Example: 4', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
				),
				array(
					'name'    => esc_html__( 'Bathrooms', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_floor_plan_bathrooms',
					'desc'    => esc_html__( 'Example: 2', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
				),

				array(
					'name'             => esc_html__( 'Floor Plan Image', ERE_TEXT_DOMAIN ),
					'id'               => 'inspiry_floor_plan_image',
					'desc'             => esc_html__( 'The recommended minimum width is 770px and height is flexible.', ERE_TEXT_DOMAIN ),
					'type'             => 'file_input',
					'max_file_uploads' => 1,
					'columns' => 12,
				),
			),
		),
	);

	return array_merge( $property_metabox_fields, $ere_floorplans_fields );

}

add_filter( 'ere_property_metabox_fields', 'ere_floorplans_metabox_fields', 40 );
