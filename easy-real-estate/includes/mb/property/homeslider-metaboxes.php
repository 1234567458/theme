<?php
/**
 * Add home slider metabox tab to property
 *
 * @param $property_metabox_tabs
 *
 * @return array
 */
function ere_homeslider_metabox_tab( $property_metabox_tabs ) {
	if ( is_array( $property_metabox_tabs ) ) {
		$property_metabox_tabs['home-slider'] = array(
			'label' => esc_html__( 'Homepage Slider', ERE_TEXT_DOMAIN ),
			'icon'  => 'dashicons-images-alt',
		);
	}

	return $property_metabox_tabs;
}

add_filter( 'ere_property_metabox_tabs', 'ere_homeslider_metabox_tab', 90 );


/**
 * Add home slider metaboxes fields to property
 *
 * @param $property_metabox_fields
 *
 * @return array
 */
function ere_homeslider_metabox_fields( $property_metabox_fields ) {

	$ere_homeslider_fields = array(
		array(
			'name'    => esc_html__( 'Do you want to add this property in Homepage Slider ?', ERE_TEXT_DOMAIN ),
			'desc'    => esc_html__( 'If Yes, Then you need to provide a slider image below.', ERE_TEXT_DOMAIN ),
			'id'      => "REAL_HOMES_add_in_slider",
			'type'    => 'radio',
			'std'     => 'no',
			'options' => array(
				'yes' => esc_html__( 'Yes ', ERE_TEXT_DOMAIN ),
				'no'  => esc_html__( 'No', ERE_TEXT_DOMAIN ),
			),
			'columns' => 12,
			'tab'     => 'home-slider',
		),
		array(
			'name'             => esc_html__( 'Slider Image', ERE_TEXT_DOMAIN ),
			'id'               => "REAL_HOMES_slider_image",
			'desc'             => esc_html__( 'The recommended image size is 1970px by 850px. You can use bigger or smaller image but try to keep the same height to width ratio and use the exactly same size images for all properties that will be added in slider.', ERE_TEXT_DOMAIN ),
			'type'             => 'image_advanced',
			'max_file_uploads' => 1,
			'columns'          => 12,
			'tab'              => 'home-slider',
		),
	);

	return array_merge( $property_metabox_fields, $ere_homeslider_fields );

}

add_filter( 'ere_property_metabox_fields', 'ere_homeslider_metabox_fields', 90 );
