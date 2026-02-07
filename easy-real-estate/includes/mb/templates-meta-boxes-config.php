<?php
if ( ! function_exists( 'ere_properties_filter_meta_boxes' ) ) :
	/**
	 * Contains properties filter meta boxes declaration
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function ere_properties_filter_meta_boxes( $meta_boxes ) {

		// removed first element and got the whole remaining array with preserved keys as we do not need 'None' in agents list.
		$agents_for_pages = array_slice( ere_get_agents_array(), 1, null, true );

		// Get enabled sorting options from theme settings
		$enabled_options = get_option( 'realhomes_available_sorting_options', array( 'price-asc', 'price-desc', 'date-asc', 'date-desc' ) );

		// Sorting options with labels
		if ( function_exists( 'realhomes_sorting_options_list' ) ) {
			$sorting_option_fields = realhomes_sorting_options_list();
		} else {
			$sorting_option_fields = array(
				'price-asc'   => esc_html__( 'Price - Low to High', ERE_TEXT_DOMAIN ),
				'price-desc'  => esc_html__( 'Price - High to Low', ERE_TEXT_DOMAIN ),
				'date-asc'    => esc_html__( 'Date - Old to New', ERE_TEXT_DOMAIN ),
				'date-desc'   => esc_html__( 'Date - New to Old', ERE_TEXT_DOMAIN )
			);
		}

		// Filter enabled options to ensure they exist in the available sort options
		$valid_sort_options = array_intersect_key( $sorting_option_fields, array_flip( $enabled_options ) );
		$valid_sort_options = array_merge(
			[ 'default' => esc_html__( 'Global Default', ERE_TEXT_DOMAIN ) ],
			$valid_sort_options
		);

		$meta_boxes[] = array(
			'id'         => 'properties-list-meta-box',
			'title'      => esc_html__( 'Properties Filter Settings', ERE_TEXT_DOMAIN ),
			'post_types' => array( 'page' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'show'       => array(
				'template' => array( 'templates/properties.php' ),
			),
			'fields'     => array(
				array(
					'id'      => 'inspiry_posts_per_page',
					'name'    => esc_html__( 'Number of Properties Per Page', ERE_TEXT_DOMAIN ),
					'type'    => 'number',
					'step'    => '1',
					'min'     => 1,
					'std'     => 6,
					'columns' => 6,
				),
				array(
					'id'       => 'inspiry_properties_order',
					'name'     => esc_html__( 'Order Properties By', ERE_TEXT_DOMAIN ),
					'type'     => 'select',
					'options'  => $valid_sort_options,
					'multiple' => false,
					'std'      => 'default',
					'columns'  => 6,
				),
				array(
					'id'              => 'inspiry_properties_locations',
					'name'            => esc_html__( 'Locations', ERE_TEXT_DOMAIN ),
					'type'            => 'select',
					'options'         => ERE_Data::get_locations_slug_name( true ),
					'multiple'        => true,
					'select_all_none' => true,
					'columns'         => 6,
				),
				array(
					'id'              => 'inspiry_properties_statuses',
					'name'            => esc_html__( 'Statuses', ERE_TEXT_DOMAIN ),
					'type'            => 'select',
					'options'         => ERE_Data::get_statuses_slug_name(),
					'multiple'        => true,
					'select_all_none' => true,
					'columns'         => 6,
				),
				array(
					'id'              => 'inspiry_properties_types',
					'name'            => esc_html__( 'Types', ERE_TEXT_DOMAIN ),
					'type'            => 'select',
					'options'         => ERE_Data::get_types_slug_name(),
					'multiple'        => true,
					'select_all_none' => true,
					'columns'         => 6,
				),
				array(
					'id'              => 'inspiry_properties_features',
					'name'            => esc_html__( 'Features', ERE_TEXT_DOMAIN ),
					'type'            => 'select',
					'options'         => ERE_Data::get_features_slug_name(),
					'multiple'        => true,
					'select_all_none' => true,
					'columns'         => 6,
				),
				array(
					'id'      => 'inspiry_properties_min_beds',
					'name'    => esc_html__( 'Minimum Beds', ERE_TEXT_DOMAIN ),
					'type'    => 'number',
					'step'    => 'any',
					'min'     => 0,
					'std'     => 0,
					'columns' => 6,
				),
				array(
					'id'      => 'inspiry_properties_min_baths',
					'name'    => esc_html__( 'Minimum Baths', ERE_TEXT_DOMAIN ),
					'type'    => 'number',
					'step'    => 'any',
					'min'     => 0,
					'std'     => 0,
					'columns' => 6,
				),
				array(
					'id'      => 'inspiry_properties_min_price',
					'name'    => esc_html__( 'Minimum Price', ERE_TEXT_DOMAIN ),
					'type'    => 'number',
					'step'    => 'any',
					'min'     => 0,
					'std'     => 0,
					'columns' => 6,
				),
				array(
					'id'      => 'inspiry_properties_max_price',
					'name'    => esc_html__( 'Maximum Price', ERE_TEXT_DOMAIN ),
					'type'    => 'number',
					'step'    => 'any',
					'min'     => 0,
					'std'     => 0,
					'columns' => 6,
				),
				array(
					'name'            => esc_html__( 'Properties by Agents', ERE_TEXT_DOMAIN ),
					'id'              => 'inspiry_properties_by_agents',
					'type'            => 'select',
					'options'         => $agents_for_pages,
					'multiple'        => true,
					'select_all_none' => true,
					'columns'         => 6,
				),
				array(
					'id'        => 'ere_ajax_pagination',
					'name'      => esc_html__( 'Disable AJAX Pagination', ERE_TEXT_DOMAIN ),
					'type'      => 'switch',
					'style'     => 'square',
					'on_label'  => esc_html__( 'Yes', ERE_TEXT_DOMAIN ),
					'off_label' => esc_html__( 'No', ERE_TEXT_DOMAIN ),
					'std'       => 0,
					'columns'   => 3,
				),
				array(
					'id'        => 'inspiry_featured_properties_only',
					'name'      => esc_html__( 'Display Only Featured Properties', ERE_TEXT_DOMAIN ),
					'type'      => 'switch',
					'style'     => 'square',
					'on_label'  => esc_html__( 'Yes', ERE_TEXT_DOMAIN ),
					'off_label' => esc_html__( 'No', ERE_TEXT_DOMAIN ),
					'std'       => 0,
					'columns'   => 3,
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'ere_properties_filter_meta_boxes' );

endif;


if ( ! function_exists( 'ere_gallery_properties_filter_meta_boxes' ) ) :
	/**
	 * Contains partner's meta box declaration
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function ere_gallery_properties_filter_meta_boxes( $meta_boxes ) {

		$meta_boxes[] = array(
			'id'         => 'properties-gallery-meta-box',
			'title'      => esc_html__( 'Properties Gallery Filter Settings', ERE_TEXT_DOMAIN ),
			'post_types' => array( 'page' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'show'       => array(
				'template' => array(
					'templates/properties-gallery.php',
					'templates/2-columns-gallery.php',
					'templates/3-columns-gallery.php',
					'templates/4-columns-gallery.php',
				),
			),
			'fields'     => array(
				 array(
					'name'    => esc_html__( 'Number of Columns', ERE_TEXT_DOMAIN ),
					'id'      => 'realhomes_properties_gallery_column',
					'type'    => 'select',
					'std'     => '3',
					'options' => array(
						'2'       => esc_html__( '2', ERE_TEXT_DOMAIN ),
						'3'       => esc_html__( '3', ERE_TEXT_DOMAIN ),
						'4'       => esc_html__( '4', ERE_TEXT_DOMAIN ),
					),
					'visible' => array( 'page_template', 'templates/properties-gallery.php' ),
					'columns' => 6,
				),
				array(
					'id'   => 'inspiry_gallery_posts_per_page',
					'name' => esc_html__( 'Number of Properties Per Page', ERE_TEXT_DOMAIN ),
					'desc' => esc_html__( 'Use any integer value. To show all properties use -1 instead.', ERE_TEXT_DOMAIN ),
					'type' => 'text',
					'std'  => 6,
					'columns' => 6,
				),
				array(
					'id'              => 'inspiry_gallery_properties_locations',
					'name'            => esc_html__( 'Locations', ERE_TEXT_DOMAIN ),
					'type'            => 'select',
					'options'         => ERE_Data::get_locations_slug_name( true ),
					'multiple'        => true,
					'select_all_none' => true,
					'columns' => 6,
				),
				array(
					'id'              => 'inspiry_gallery_properties_statuses',
					'name'            => esc_html__( 'Statuses', ERE_TEXT_DOMAIN ),
					'type'            => 'select',
					'options'         => ERE_Data::get_statuses_slug_name(),
					'multiple'        => true,
					'select_all_none' => true,
					'columns' => 6,
				),
				array(
					'id'              => 'inspiry_gallery_properties_types',
					'name'            => esc_html__( 'Types', ERE_TEXT_DOMAIN ),
					'type'            => 'select',
					'options'         => ERE_Data::get_types_slug_name(),
					'multiple'        => true,
					'select_all_none' => true,
					'columns' => 6,
				),
				array(
					'id'              => 'inspiry_gallery_properties_features',
					'name'            => esc_html__( 'Features', ERE_TEXT_DOMAIN ),
					'type'            => 'select',
					'options'         => ERE_Data::get_features_slug_name(),
					'multiple'        => true,
					'select_all_none' => true,
					'columns' => 6,
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'ere_gallery_properties_filter_meta_boxes' );

endif;