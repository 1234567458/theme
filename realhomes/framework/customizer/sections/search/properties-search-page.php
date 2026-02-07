<?php
/**
 * Section: `Properties Search Page`
 * Panel:   `Properties Search`
 *
 * @since   2.6.3
 * @package realhomes/customizer
 */
if ( ! function_exists( 'inspiry_properties_search_page_customizer' ) ) :
	/**
	 * Properties search page section.
	 *
	 * @since  2.6.3
	 *
	 * @param object $wp_customize - Instance of WP_Customize_Manager.
	 *
	 */
	function inspiry_properties_search_page_customizer( WP_Customize_Manager $wp_customize ) {

		/**
		 * Search Page
		 */
		$wp_customize->add_section(
			'inspiry_properties_search_page', array(
				'title' => esc_html__( 'Properties Search Page', RH_TEXT_DOMAIN ),
				'panel' => 'inspiry_properties_search_panel',
			)
		);

		/* Inspiry Search Page */
		$wp_customize->add_setting(
			'inspiry_search_page', array(
				'type'              => 'option',
				'transport'         => 'postMessage',
				'sanitize_callback' => 'inspiry_sanitize_select',
			)
		);

		// Arguments for the inspiry_pages function to fetch only search related pages.
		$search_pages_args = array(
			'meta_query' => array(
				'relation' => 'or',
				array(
					'key'   => '_wp_page_template',
					'value' => 'elementor_header_footer',
				),
				array(
					'key'   => '_wp_page_template',
					'value' => 'elementor_canvas',
				),
				array(
					'key'   => '_wp_page_template',
					'value' => 'templates/properties-search.php',
				),
			),
		);

		$wp_customize->add_control(
			'inspiry_search_page', array(
				'label'       => esc_html__( 'Select Search Page', RH_TEXT_DOMAIN ),
				'description' => esc_html__( 'Ensure that you have assigned one of the search templates to the page your are looking for. Also, make sure to configure Permalinks as "Post name".', RH_TEXT_DOMAIN ),
				'type'        => 'select',
				'section'     => 'inspiry_properties_search_page',
				'choices'     => inspiry_pages( $search_pages_args ),
			)
		);

		if ( 'modern' === INSPIRY_DESIGN_VARIATION ) {

			// Search Results Page Layout
			$wp_customize->add_setting( 'inspiry_search_results_page_layout', array(
				'type'              => 'option',
				'default'           => 'list',
				'sanitize_callback' => 'inspiry_sanitize_select',
			) );
			$wp_customize->add_control( 'inspiry_search_results_page_layout', array(
				'label'   => esc_html__( 'Search Page layout', RH_TEXT_DOMAIN ),
				'type'    => 'select',
				'section' => 'inspiry_properties_search_page',
				'choices' => array(
					'list' => esc_html__( 'List', RH_TEXT_DOMAIN ),
					'grid' => esc_html__( 'Grid', RH_TEXT_DOMAIN ),
				),
			) );
		}

		/* Search Template Variation */
		$wp_customize->add_setting(
			'inspiry_search_template_variation', array(
				'type'              => 'option',
				'default'           => 'two-columns',
				'sanitize_callback' => 'inspiry_sanitize_select',
			)
		);
		$wp_customize->add_control(
			'inspiry_search_template_variation', array(
				'label'           => esc_html__( 'Search Page Variation', RH_TEXT_DOMAIN ),
				'description'     => esc_html__( 'Search page variation to display properties.', RH_TEXT_DOMAIN ),
				'type'            => 'select',
				'section'         => 'inspiry_properties_search_page',
				'choices'         => array(
					'one-column'   => esc_html__( 'One Column', RH_TEXT_DOMAIN ),
					'two-columns'  => esc_html__( 'Two Columns', RH_TEXT_DOMAIN ),
					'four-columns' => esc_html__( 'Four Columns', RH_TEXT_DOMAIN ),
				),
				'active_callback' => 'inspiry_search_template_variation',
			)
		);

		/* Number of Properties To Display on Search Results Page */
		$wp_customize->add_setting(
			'theme_properties_on_search', array(
				'type'              => 'option',
				'default'           => '4',
				'sanitize_callback' => 'inspiry_sanitize_select',
			)
		);
		$wp_customize->add_control(
			'theme_properties_on_search', array(
				'label'   => esc_html__( 'Number of properties to display on a page?', RH_TEXT_DOMAIN ),
				'type'    => 'select',
				'section' => 'inspiry_properties_search_page',
				'choices' => array(
					'1'  => 1,
					'2'  => 2,
					'3'  => 3,
					'4'  => 4,
					'5'  => 5,
					'6'  => 6,
					'7'  => 7,
					'8'  => 8,
					'9'  => 9,
					'10' => 10,
					'11' => 11,
					'12' => 12,
					'13' => 13,
					'14' => 14,
					'15' => 15,
					'16' => 16,
					'17' => 17,
					'18' => 18,
					'19' => 19,
					'20' => 20,
				),
			)
		);

		if ( 'classic' === INSPIRY_DESIGN_VARIATION ) {

			// Search Results Page Area Below Header
			$wp_customize->add_setting(
				'theme_search_module', array(
					'type'              => 'option',
					'default'           => 'properties-map',
					'sanitize_callback' => 'inspiry_sanitize_radio',
				)
			);
			$wp_customize->add_control(
				'theme_search_module', array(
					'label'           => esc_html__( 'Search Results Page Sub-Header', RH_TEXT_DOMAIN ),
					'description'     => esc_html__( 'What you want to display in area below header on properties search results page ?', RH_TEXT_DOMAIN ),
					'type'            => 'radio',
					'section'         => 'inspiry_properties_search_page',
					'choices'         => array(
						'properties-map' => esc_html__( 'Map with Properties Markers', RH_TEXT_DOMAIN ),
						'simple-banner'  => esc_html__( 'Image Banner', RH_TEXT_DOMAIN ),
					),
					'active_callback' => 'inspiry_search_results_layout'
				)
			);
		}

		if ( 'modern' === INSPIRY_DESIGN_VARIATION ) {

			// Header Variation
			$wp_customize->add_setting(
				'inspiry_search_header_variation', array(
					'type'              => 'option',
					'default'           => 'banner',
					'sanitize_callback' => 'inspiry_sanitize_radio',
				)
			);
			$wp_customize->add_control(
				'inspiry_search_header_variation', array(
					'label'       => esc_html__( 'Header Variation', RH_TEXT_DOMAIN ),
					'description' => esc_html__( 'Header variation for search results page.', RH_TEXT_DOMAIN ),
					'type'        => 'radio',
					'section'     => 'inspiry_properties_search_page',
					'choices'     => array(
						'banner' => esc_html__( 'Banner', RH_TEXT_DOMAIN ),
						'none'   => esc_html__( 'None', RH_TEXT_DOMAIN ),
					),
				)
			);

			// Ajax Search Results on Search Results Page
			$wp_customize->add_setting(
				'realhomes_ajax_search_results', array(
					'type'              => 'option',
					'default'           => 'no',
					'sanitize_callback' => 'inspiry_sanitize_radio',
				)
			);
			$wp_customize->add_control(
				'realhomes_ajax_search_results', array(
					'label'       => esc_html__( 'Enable Ajax Search Results', RH_TEXT_DOMAIN ),
					'description' => esc_html__( 'Do you want to enable ajax based search results?', RH_TEXT_DOMAIN ),
					'type'        => 'radio',
					'section'     => 'inspiry_properties_search_page',
					'choices'     => array(
						'yes' => esc_html__( 'Yes', RH_TEXT_DOMAIN ),
						'no'  => esc_html__( 'No', RH_TEXT_DOMAIN ),
					),
				)
			);

			// Map on Search Results Page
			$wp_customize->add_setting( 'inspiry_search_results_layout', array(
				'type'              => 'option',
				'default'           => 'with_map',
				'sanitize_callback' => 'inspiry_sanitize_radio',
			) );
			$wp_customize->add_control( 'inspiry_search_results_layout', array(
				'label'           => esc_html__( 'Search Results Page Map', RH_TEXT_DOMAIN ),
				'type'            => 'radio',
				'section'         => 'inspiry_properties_search_page',
				'choices'         => array(
					'with_map'    => esc_html__( 'Show', RH_TEXT_DOMAIN ),
					'without_map' => esc_html__( 'Hide', RH_TEXT_DOMAIN ),
				),
				'active_callback' => 'inspiry_search_results_layout'
			) );
		}

		if ( 'ultra' === INSPIRY_DESIGN_VARIATION ) {
			$wp_customize->add_setting( 'realhomes_search_results_page_map', array(
				'type'              => 'option',
				'default'           => 'map',
				'sanitize_callback' => 'inspiry_sanitize_radio',
			) );
			$wp_customize->add_control( 'realhomes_search_results_page_map', array(
				'label'       => esc_html__( 'Module Below Header', RH_TEXT_DOMAIN ),
				'description' => esc_html__( 'What to display in area below header on properties search results page other than half map template?', RH_TEXT_DOMAIN ),
				'type'        => 'radio',
				'section'     => 'inspiry_properties_search_page',
				'choices'     => array(
					'map'  => esc_html__( 'Map with Properties Markers', RH_TEXT_DOMAIN ),
					'none' => esc_html__( 'None', RH_TEXT_DOMAIN ),
				),
			) );
		}

		$map_type = inspiry_get_maps_type();
		if ( 'google-maps' == $map_type ) {
			// Google Map Type
			$wp_customize->add_setting( 'inspiry_search_map_type', array(
				'type'              => 'option',
				'default'           => 'global',
				'sanitize_callback' => 'inspiry_sanitize_select',
			) );
			$wp_customize->add_control( 'inspiry_search_map_type', array(
				'label'           => esc_html__( 'Map Type', RH_TEXT_DOMAIN ),
				'description'     => esc_html__( 'Choose Google Map Type', RH_TEXT_DOMAIN ),
				'type'            => 'select',
				'section'         => 'inspiry_properties_search_page',
				'choices'         => array(
					'global'    => esc_html__( 'Global Default', RH_TEXT_DOMAIN ),
					'roadmap'   => esc_html__( 'RoadMap', RH_TEXT_DOMAIN ),
					'satellite' => esc_html__( 'Satellite', RH_TEXT_DOMAIN ),
					'hybrid'    => esc_html__( 'Hybrid', RH_TEXT_DOMAIN ),
					'terrain'   => esc_html__( 'Terrain', RH_TEXT_DOMAIN ),
				),
				'active_callback' => 'inspiry_search_map_enabled',
			) );
		}

		// Number of properties to display on map
		$wp_customize->add_setting( 'inspiry_properties_on_search_map', array(
			'type'              => 'option',
			'default'           => 'all',
			'sanitize_callback' => 'inspiry_sanitize_radio',
		) );
		$wp_customize->add_control( 'inspiry_properties_on_search_map', array(
			'label'           => esc_html__( 'Number of properties to mark on map?', RH_TEXT_DOMAIN ),
			'section'         => 'inspiry_properties_search_page',
			'type'            => 'radio',
			'choices'         => array(
				'all'            => esc_html__( 'All found', RH_TEXT_DOMAIN ),
				'as_on_one_page' => esc_html__( 'As on one page', RH_TEXT_DOMAIN ),
			),
			'active_callback' => 'inspiry_search_map_enabled',
		) );

		// Stick Featured Properties on top of Search Results in default sorting
		$wp_customize->add_setting(
			'inspiry_featured_properties_on_top', array(
				'type'              => 'option',
				'default'           => 'true',
				'sanitize_callback' => 'inspiry_sanitize_radio',
			)
		);
		$wp_customize->add_control(
			'inspiry_featured_properties_on_top', array(
				'label'       => esc_html__( 'Display featured properties on top in search results?', RH_TEXT_DOMAIN ),
				'description' => esc_html__( 'This setting will be applied on sorting based on Sort by Date (Old to New and New to Old) only.', RH_TEXT_DOMAIN ),
				'type'        => 'radio',
				'section'     => 'inspiry_properties_search_page',
				'choices'     => array(
					'true'  => esc_html__( 'Yes', RH_TEXT_DOMAIN ),
					'false' => esc_html__( 'No', RH_TEXT_DOMAIN ),
				),
			)
		);

		if ( class_exists( 'ERE_Data' ) ) {
			/* Exclude Statuses */
			$wp_customize->add_setting( 'inspiry_search_exclude_status', array(
				'type'              => 'option',
				'sanitize_callback' => 'inspiry_sanitize_multiple_checkboxes',
			) );
			$wp_customize->add_control(
				new Inspiry_Multiple_Checkbox_Customize_Control(
					$wp_customize,
					'inspiry_search_exclude_status',
					array(
						'section' => 'inspiry_properties_search_page',
						'label'   => esc_html__( 'Which statuses would you like to exclude?', RH_TEXT_DOMAIN ),
						'choices' => ERE_Data::get_statuses_id_name(),
					)
				)
			);
		}

		$wp_customize->add_setting(
			'realhomes_no_results_title', array(
				'type'              => 'option',
				'default'           => esc_html__( 'Oops! No Properties Found', RH_TEXT_DOMAIN ),
				'sanitize_callback' => 'inspiry_sanitize_field',
			)
		);
		$wp_customize->add_control(
			'realhomes_no_results_title', array(
				'label'       => esc_html__( 'No Properties Found Title', RH_TEXT_DOMAIN ),
				'type'        => 'text',
				'section'     => 'inspiry_properties_search_page',
			)
		);

		$wp_customize->add_setting(
			'inspiry_search_template_no_result_text', array(
				'type'              => 'option',
				'default'           => esc_html__( 'Try modifying your search criteria or explore popular property listings below.', RH_TEXT_DOMAIN ),
				'sanitize_callback' => 'inspiry_sanitize_field',
			)
		);
		$wp_customize->add_control(
			'inspiry_search_template_no_result_text', array(
				'label'       => esc_html__( 'No Property Found Text', RH_TEXT_DOMAIN ),
				'description' => esc_html__( 'i.e No Property Found! (a, strong, em, i and br tags are allowed)', RH_TEXT_DOMAIN ),
				'type'        => 'textarea',
				'section'     => 'inspiry_properties_search_page',
			)
		);

		$wp_customize->add_setting(
			'realhomes_no_results_reset_button', array(
				'type'              => 'option',
				'default'           => true,
				'sanitize_callback' => 'inspiry_sanitize_radio',
			)
		);
		$wp_customize->add_control(
			'realhomes_no_results_reset_button', array(
				'label'       => esc_html__( 'Show reset button on no results', RH_TEXT_DOMAIN ),
				'type'        => 'radio',
				'section'     => 'inspiry_properties_search_page',
				'choices'     => array(
					true  => esc_html__( 'Yes', RH_TEXT_DOMAIN ),
					false => esc_html__( 'No', RH_TEXT_DOMAIN ),
				)
			)
		);

		$wp_customize->add_setting(
			'realhomes_no_results_taxonomies', array(
				'type'              => 'option',
				'default'           => true,
				'sanitize_callback' => 'inspiry_sanitize_radio',
			)
		);
		$wp_customize->add_control(
			'realhomes_no_results_taxonomies', array(
				'label'       => esc_html__( 'Show taxonomy terms on no results page', RH_TEXT_DOMAIN ),
				'type'        => 'radio',
				'section'     => 'inspiry_properties_search_page',
				'choices'     => array(
					true  => esc_html__( 'Yes', RH_TEXT_DOMAIN ),
					false => esc_html__( 'No', RH_TEXT_DOMAIN ),
				)
			)
		);

		$wp_customize->add_setting(
			'realhomes_no_results_taxonomies_title', array(
				'type'              => 'option',
				'default'           => esc_html__( 'You can also look into the popular searches', RH_TEXT_DOMAIN ),
				'sanitize_callback' => 'inspiry_sanitize_field',
			)
		);
		$wp_customize->add_control(
			'realhomes_no_results_taxonomies_title', array(
				'label'       => esc_html__( 'No Results Taxonomies Title', RH_TEXT_DOMAIN ),
				'type'        => 'text',
				'section'     => 'inspiry_properties_search_page',
			)
		);

		/* Separator */
		$wp_customize->add_setting( 'inspiry_search_url_separator', array( 'sanitize_callback' => 'sanitize_text_field', ) );
		$wp_customize->add_control(
			new Inspiry_Separator_Control(
				$wp_customize,
				'inspiry_search_url_separator',
				array(
					'section' => 'inspiry_properties_search_page',
				)
			)
		);
	}

	add_action( 'customize_register', 'inspiry_properties_search_page_customizer' );
endif;

if ( ! function_exists( 'inspiry_properties_search_page_defaults' ) ) :

	/**
	 * inspiry_properties_search_page_defaults.
	 *
	 * @since  2.6.3
	 */

	function inspiry_properties_search_page_defaults( WP_Customize_Manager $wp_customize ) {
		$properties_search_page_settings_ids = array(
			'inspiry_search_header_variation',
			'theme_search_module',
			'theme_properties_on_search',
			'inspiry_featured_properties_on_top',
			'inspiry_search_results_layout',
			'inspiry_search_template_variation',
		);
		inspiry_initialize_defaults( $wp_customize, $properties_search_page_settings_ids );
	}

	add_action( 'customize_save_after', 'inspiry_properties_search_page_defaults' );
endif;

if ( ! function_exists( 'inspiry_search_map_enabled' ) ) {
	/**
	 * Check if Search page map is enabled
	 *
	 * @param $control
	 *
	 * @return bool
	 */
	function inspiry_search_map_enabled( $control ) {

		if ( 'classic' === INSPIRY_DESIGN_VARIATION && ( 'properties-map' === $control->manager->get_setting( 'theme_search_module' )->value() ) ) {
			return true;
		} else if ( 'modern' === INSPIRY_DESIGN_VARIATION && ( 'with_map' === $control->manager->get_setting( 'inspiry_search_results_layout' )->value() ) ) {
			return true;
		} else if ( 'ultra' === INSPIRY_DESIGN_VARIATION && ( 'map' === $control->manager->get_setting( 'realhomes_search_results_page_map' )->value() ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_search_template_variation' ) ) {
	/**
	 * Limited the visibility of option for search page template in classic variation.
	 *
	 * @return bool
	 */
	function inspiry_search_template_variation() {

		if ( is_page_template( 'templates/properties-search.php' ) && 'classic' === INSPIRY_DESIGN_VARIATION ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_search_results_layout' ) ) {
	/**
	 * Hide the option for search half map templates.
	 *
	 * @return bool
	 */
	function inspiry_search_results_layout() {

		if ( realhomes_is_half_map_template() ) {
			return false;
		}

		return true;
	}
}