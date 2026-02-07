<?php
/**
 * Add basic metabox tab to property
 *
 * @param $property_metabox_tabs
 *
 * @return array
 */
function ere_basic_metabox_tab( $property_metabox_tabs ) {
	if ( is_array( $property_metabox_tabs ) ) {
		$property_metabox_tabs['details'] = array(
			'label' => esc_html__( 'Basic Information', ERE_TEXT_DOMAIN ),
			'icon' => ( is_plugin_active( 'realhomes-vacation-rentals/realhomes-vacation-rentals.php' ) && function_exists( 'inspiry_is_rvr_enabled' ) && inspiry_is_rvr_enabled() ) ? 'dashicons-palmtree' : 'dashicons-admin-home',
		);
	}

	return $property_metabox_tabs;
}

// Set 'Basic Information' tab priority low if 'Vacation Rentals' is active.
$tab_priority = ( is_plugin_active( 'realhomes-vacation-rentals/realhomes-vacation-rentals.php' ) ) ? 11 : 10;
add_filter( 'ere_property_metabox_tabs', 'ere_basic_metabox_tab', $tab_priority );

/**
 * Add basic metaboxes fields to property
 *
 * @param $property_metabox_fields
 *
 * @return array
 */
function ere_basic_metabox_fields( $property_metabox_fields ) {

	// Migration code related to additional details improvements in version 3.11.2
	$post_id = false;
	if ( isset( $_GET['post'] ) ) {
		$post_id = intval( $_GET['post'] );
	} else if ( isset( $_POST['post_ID'] ) ) {
		$post_id = intval( $_POST['post_ID'] );
	}

	if ( $post_id && $post_id > 0 ) {
		ere_additional_details_migration( $post_id ); // Migrate property additional details from old metabox key to new key.
	}

	// Display property price fields in this "Basic Information" section only if RVR is not enabled.
	if ( function_exists( 'inspiry_is_rvr_enabled' ) && ! inspiry_is_rvr_enabled() ) {
		$price_fields = array(
			array(
				'id'      => "REAL_HOMES_property_price",
				'name'    => esc_html__( 'Sale or Rent Price ( Only digits )', ERE_TEXT_DOMAIN ),
				'desc'    => esc_html__( 'Example: 12500', ERE_TEXT_DOMAIN ),
				'type'    => 'text',
				'std'     => '',
				'columns' => 6,
				'tab'     => 'details',
			),
			array(
				'id'      => "REAL_HOMES_property_old_price",
				'name'    => esc_html__( 'Old Price If Any ( Only digits )', ERE_TEXT_DOMAIN ),
				'desc'    => esc_html__( 'Example: 14500', ERE_TEXT_DOMAIN ),
				'type'    => 'text',
				'std'     => '',
				'columns' => 6,
				'tab'     => 'details',
			),
			array(
				'id'      => 'REAL_HOMES_property_price_prefix',
				'name'    => esc_html__( 'Price Prefix', ERE_TEXT_DOMAIN ),
				'desc'    => esc_html__( 'Example: From. Provide a value to override the global prefix value, or use \'None\' to disable it in case you do not want a prefix at all.', ERE_TEXT_DOMAIN ),
				'type'    => 'text',
				'std'     => '',
				'columns' => 6,
				'tab'     => 'details',
			),
			array(
				'id'      => "REAL_HOMES_property_price_postfix",
				'name'    => esc_html__( 'Price Postfix', ERE_TEXT_DOMAIN ),
				'desc'    => esc_html__( 'Example: Monthly or Per Night. Provide a value to override the global postfix value, or use \'None\' to disable it in case you do not want a postfix at all.', ERE_TEXT_DOMAIN ),
				'type'    => 'text',
				'std'     => '',
				'columns' => 6,
				'tab'     => 'details',
			),
			array(
				'type'    => 'divider',
				'columns' => 12,
				'id'      => 'price_divider',
				'tab'     => 'details',
			),
		);
	} else {
		$price_fields = array();
	}

	$ere_basic_fields = array();

	$ere_basic_fields[] = array(
		'id'      => "REAL_HOMES_property_size",
		'name'    => esc_html__( 'Area Size ( Only digits )', ERE_TEXT_DOMAIN ),
		'desc'    => esc_html__( 'Example: 2500', ERE_TEXT_DOMAIN ),
		'type'    => 'text',
		'std'     => '',
		'columns' => 6,
		'tab'     => 'details',
	);

	$ere_basic_fields[] = array(
		'id'      => "REAL_HOMES_property_size_postfix",
		'name'    => esc_html__( 'Area Size Postfix', ERE_TEXT_DOMAIN ),
		'desc'    => esc_html__( 'Example: sq ft. Provide a value to override the global postfix value, or use \'None\' to disable it in case you do not want a postfix at all.', ERE_TEXT_DOMAIN ),
		'type'    => 'text',
		'std'     => '',
		'columns' => 6,
		'tab'     => 'details',
	);

	$ere_basic_fields[] = array(
		'id'      => "REAL_HOMES_property_lot_size",
		'name'    => esc_html__( 'Lot Size ( Only digits )', ERE_TEXT_DOMAIN ),
		'desc'    => esc_html__( 'Example: 3000', ERE_TEXT_DOMAIN ),
		'type'    => 'text',
		'std'     => '',
		'columns' => 6,
		'tab'     => 'details',
	);

	$ere_basic_fields[] = array(
		'id'      => "REAL_HOMES_property_lot_size_postfix",
		'name'    => esc_html__( 'Lot Size Postfix', ERE_TEXT_DOMAIN ),
		'desc'    => esc_html__( 'Example: sq ft. Provide a value to override the global postfix value, or use \'None\' to disable it in case you do not want a postfix at all.', ERE_TEXT_DOMAIN ),
		'type'    => 'text',
		'std'     => '',
		'columns' => 6,
		'tab'     => 'details',
	);

	$ere_basic_fields[] = array(
		'id'      => "REAL_HOMES_property_bedrooms",
		'name'    => esc_html__( 'Bedrooms', ERE_TEXT_DOMAIN ),
		'desc'    => esc_html__( 'Example: 4', ERE_TEXT_DOMAIN ),
		'type'    => 'text',
		'std'     => '',
		'columns' => 6,
		'tab'     => 'details',
	);

	$ere_basic_fields[] = array(
		'id'      => "REAL_HOMES_property_bathrooms",
		'name'    => esc_html__( 'Bathrooms', ERE_TEXT_DOMAIN ),
		'desc'    => esc_html__( 'Example: 2', ERE_TEXT_DOMAIN ),
		'type'    => 'text',
		'std'     => '',
		'columns' => 6,
		'tab'     => 'details',
	);

	$ere_basic_fields[] = array(
		'id'      => "REAL_HOMES_property_garage",
		'name'    => esc_html__( 'Garages or Parking Spaces', ERE_TEXT_DOMAIN ),
		'desc'    => esc_html__( 'Example: 1', ERE_TEXT_DOMAIN ),
		'type'    => 'text',
		'std'     => '',
		'columns' => 6,
		'tab'     => 'details',
	);

	$ere_basic_fields[] = array(
		'id'         => "REAL_HOMES_property_id",
		'name'       => esc_html__( 'Property ID', ERE_TEXT_DOMAIN ),
		'desc'       => esc_html__( 'It will help you search a property directly.', ERE_TEXT_DOMAIN ),
		'type'       => 'text',
		'std'        => ( 'true' === get_option( 'inspiry_auto_property_id_check' ) ) ? get_option( 'inspiry_auto_property_id_pattern' ) : '',
		'columns'    => 6,
		'tab'        => 'details',
		'attributes' => array(
			'readonly' => ( 'true' === get_option( 'inspiry_auto_property_id_check' ) ) ? true : false,
		),
	);

	$ere_basic_fields[] = array(
		'id'      => "REAL_HOMES_property_year_built",
		'name'    => esc_html__( 'Year Built', ERE_TEXT_DOMAIN ),
		'desc'    => esc_html__( 'Example: 2017', ERE_TEXT_DOMAIN ),
		'type'    => 'text',
		'std'     => '',
		'columns' => 6,
		'tab'     => 'details',
	);

	if ( ! ( function_exists( 'inspiry_is_rvr_enabled' ) && inspiry_is_rvr_enabled() ) && post_type_exists( 'owner' ) ) {
		$owner_ids = get_posts( array(
			'post_type'        => 'owner',
			'posts_per_page'   => 500,
			'suppress_filters' => false,
			'fields'           => 'ids',
		) );

		$owners_posts = array( 0 => esc_html__( 'None', ERE_TEXT_DOMAIN ) );

		foreach ( $owner_ids as $owner_id ) {
			$owners_posts[ $owner_id ] = esc_html( get_the_title( $owner_id ) );
		}


		$ere_basic_fields[] = array(
			'id'      => "rvr_property_owner",
			'name'    => esc_html__( 'Owner', ERE_TEXT_DOMAIN ),
			'desc'    => sprintf( esc_html__( 'You can add new owner by %s clicking here%s.', ERE_TEXT_DOMAIN ), '<a style="color: #ea723d;" target="_blank" href="' . get_home_url() . '/wp-admin/post-new.php?post_type=owner">', '</a>' ),
			'type'    => 'select',
			'options' => $owners_posts,
			'std'     => '',
			'columns' => 6,
			'tab'     => 'details',
		);
	}

	$ere_basic_fields[] = array(
		'name'    => esc_html__( 'Mark this property as featured ?', ERE_TEXT_DOMAIN ),
		'id'      => "REAL_HOMES_featured",
		'type'    => 'radio',
		'std'     => '0',
		'options' => array(
			'1' => esc_html__( 'Yes', ERE_TEXT_DOMAIN ),
			'0' => esc_html__( 'No', ERE_TEXT_DOMAIN )
		),
		'columns' => 6,
		'tab'     => 'details',
	);

	$ere_basic_fields[] = array(
		'type'    => 'divider',
		'columns' => 12,
		'id'      => 'additional_details_divider',
		'tab'     => 'details',
	);

	$ere_basic_fields[] =array(
		'id'         => 'REAL_HOMES_additional_details_list',
		'name'       => esc_html__( 'Additional Details', ERE_TEXT_DOMAIN ),
		'type'       => 'text_list',
		'columns'    => 12,
		'tab'        => 'details',
		'clone'      => true,
		'sort_clone' => true,
		'add_button' => esc_html__( '+ Add More', ERE_TEXT_DOMAIN ),
		'options'    => array(
			esc_html__( 'Title', ERE_TEXT_DOMAIN ) => esc_html__( 'Title', ERE_TEXT_DOMAIN ),
			esc_html__( 'Value', ERE_TEXT_DOMAIN ) => esc_html__( 'Value', ERE_TEXT_DOMAIN ),
		)
	);

	$ere_basic_fields = array_merge( $price_fields, $ere_basic_fields );

	return array_merge( $property_metabox_fields, $ere_basic_fields );
}

add_filter( 'ere_property_metabox_fields', 'ere_basic_metabox_fields', 10 );