<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Ultra Properties Main Slider
 *
 * Displays the properties image and details as slider into the page.
 *
 */
class RHEA_Ultra_Main_Properties_Slider extends \Elementor\Widget_Base {

	public function get_name() {
		return 'rhea-ultra-main-properties-slider';
	}

	public function get_title() {
		return esc_html__( 'RH: Main Properties Slider', RHEA_TEXT_DOMAIN );
	}

	public function get_icon() {
		return 'eicon-post-slider rh-ultra-widget';
	}

	public function get_categories() {
		return [ 'ultra-real-homes' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'slider_section',
			[
				'label' => esc_html__( 'Slider', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'slider_height',
			[
				'label'     => esc_html__( 'Slider Height', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea-properties-slider-two-slide' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_full_screen' => 'no',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'exclude'   => [ 'custom' ],
				'default'   => 'full',
				'separator' => 'after',
			]
		);

		$this->add_control(
			'number_of_properties',
			[
				'label'       => esc_html__( 'Number of Properties', RHEA_TEXT_DOMAIN ),
				'description' => wp_kses_post(
					'<span style="color: red;">' . esc_html__( 'Note:', RHEA_TEXT_DOMAIN ) . '</span> ' .
					esc_html__( 'This control is only active when no property is selected in the field below', RHEA_TEXT_DOMAIN )
				),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 6
			]
		);

		$this->add_control(
			'custom_status_heading',
			[
				'label'     => esc_html__( 'Select Properties', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$slider_properties_args = array(
			'post_type'        => 'property',
			'suppress_filters' => false,
			'numberposts'      => 100,
			'meta_query'       => array(
				array(
					'key'     => 'REAL_HOMES_add_in_slider',
					'value'   => 'yes',
					'compare' => 'LIKE',
				),
			),
		);

		$slider_properties      = get_posts( $slider_properties_args );
		$slider_properties_list = array( '' => esc_html__( 'None', RHEA_TEXT_DOMAIN ) );
		if ( $slider_properties ) {
			foreach ( $slider_properties as $slider_property ) {
				$slider_properties_list[ $slider_property->ID ] = esc_html( $slider_property->post_title );
			}
		}

		$slider_properties_defaults = array();
		if ( ! empty( $slider_properties_list ) ) {
			foreach ( $slider_properties_list as $key => $value ) {
				$slider_properties_defaults[] = array(
					'property_id'    => esc_html( $key ),
					'property_title' => '',
					'property_image' => array(),
				);

				break;
			}
		}

		$slider_properties_repeater = new \Elementor\Repeater();
		$slider_properties_repeater->add_control(
			'property_id',
			[
				'label'       => esc_html__( 'Select Property', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Property slide will not be displayed if no Slider Image is selected" ', RHEA_TEXT_DOMAIN ),

				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => $slider_properties_list,
			]
		);

		$slider_properties_repeater->add_control(
			'property_title',
			[
				'label'       => esc_html__( 'Title', RHEA_TEXT_DOMAIN ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXT,
			]
		);

		$slider_properties_repeater->add_control(
			'property_image',
			[
				'label' => esc_html__( 'Choose Image', RHEA_TEXT_DOMAIN ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			]
		);
		$slider_properties_repeater->add_control(
			'bg_slide_position',
			[
				'label'   => esc_html__( 'Background Position', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => array(
					'center center' => esc_html__( 'Center Center', RHEA_TEXT_DOMAIN ),
					'center left'   => esc_html__( 'Center Left', RHEA_TEXT_DOMAIN ),
					'center right'  => esc_html__( 'Center Right', RHEA_TEXT_DOMAIN ),
					'top center'    => esc_html__( 'Top Center', RHEA_TEXT_DOMAIN ),
					'top left'      => esc_html__( 'Top Left', RHEA_TEXT_DOMAIN ),
					'top right'     => esc_html__( 'Top Right', RHEA_TEXT_DOMAIN ),
					'bottom center' => esc_html__( 'Bottom Center', RHEA_TEXT_DOMAIN ),
					'bottom left'   => esc_html__( 'Bottom Left', RHEA_TEXT_DOMAIN ),
					'bottom right'  => esc_html__( 'Bottom Right', RHEA_TEXT_DOMAIN ),
				),
			]
		);

		$this->add_control(
			'properties_list',
			[
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $slider_properties_repeater->get_controls(),
				'show_label'  => false,
				'default'     => $slider_properties_defaults,
				'title_field' => '{{{ property_title }}}'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'slider_control_options',
			[
				'label' => esc_html__( 'Slider Options', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slider_animation_styles',
			[
				'label'   => esc_html__( 'Animation Style', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => array(
					'slide' => esc_html__( 'Slide', RHEA_TEXT_DOMAIN ),
					'fade'  => esc_html__( 'Fade', RHEA_TEXT_DOMAIN )
				)
			]
		);

		$this->add_control(
			'slider_animation_delay',
			[
				'label'   => esc_html__( 'Animation Delay', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 7000
			]
		);

		$this->add_control(
			'slider_animation_speed',
			[
				'label'   => esc_html__( 'Animation Speed', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 1500
			]
		);

		$this->add_control(
			'slider_keyboard_nav',
			[
				'label'        => esc_html__( 'Keyboard Nav', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => true,
				'default'      => true
			]
		);

		$this->add_control(
			'slider_direction_nav',
			[
				'label'        => esc_html__( 'Direction Nav', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => true,
				'default'      => true
			]
		);

		$this->add_control(
			'slider_pause_on_hover',
			[
				'label'        => esc_html__( 'Pause on hover', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => true,
				'default'      => true
			]
		);

		$this->add_control(
			'slider_reverse',
			[
				'label'        => esc_html__( 'Reverse', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => true,
				'default'      => false
			]
		);

		$this->add_control(
			'slider_direction',
			[
				'label'   => esc_html__( 'Direction', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => esc_html__( 'Horizontal', RHEA_TEXT_DOMAIN ),
					'vertical'   => esc_html__( 'Vertical', RHEA_TEXT_DOMAIN )
				),
				'condition' => [
					'slider_animation_styles' => 'slide'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'property_meta_heading',
			[
				'label' => esc_html__( 'Property Meta', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_ratings_with_title',
			[
				'label'        => esc_html__( 'Show Ratings', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'Hide', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_added_date',
			[
				'label'        => esc_html__( 'Show Added Date', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'Hide', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'show_ratings_with_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_added_date_label',
			[
				'label'     => esc_html__( 'Added Label Text', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Added:', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'show_added_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_property_meta',
			[
				'label'        => esc_html__( 'Show Meta Info', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'Hide', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$get_meta = array(
			'bedrooms'   => esc_html__( 'Bedrooms', RHEA_TEXT_DOMAIN ),
			'bathrooms'  => esc_html__( 'Bathrooms', RHEA_TEXT_DOMAIN ),
			'area'       => esc_html__( 'Area', RHEA_TEXT_DOMAIN ),
			'garage'     => esc_html__( 'Garages/Parking', RHEA_TEXT_DOMAIN ),
			'year-built' => esc_html__( 'Year Built', RHEA_TEXT_DOMAIN ),
			'lot-size'   => esc_html__( 'Lot Size', RHEA_TEXT_DOMAIN ),
		);

		$meta_defaults = array(
			array(
				'rhea_property_meta_display' => 'bedrooms',
				'rhea_meta_repeater_label'   => esc_html__( 'Bedrooms', RHEA_TEXT_DOMAIN ),
			),
			array(
				'rhea_property_meta_display' => 'bathrooms',
				'rhea_meta_repeater_label'   => esc_html__( 'Bathrooms', RHEA_TEXT_DOMAIN ),
			),
			array(
				'rhea_property_meta_display' => 'area',
				'rhea_meta_repeater_label'   => esc_html__( 'Area', RHEA_TEXT_DOMAIN ),
			),
		);

		if ( rhea_is_rvr_enabled() ) {
			$get_meta['guests']   = esc_html__( 'Guests Capacity', RHEA_TEXT_DOMAIN );
			$get_meta['min-stay'] = esc_html__( 'Min Stay', RHEA_TEXT_DOMAIN );

			$meta_defaults[] = array(
				'rhea_property_meta_display' => 'guests',
				'rhea_meta_repeater_label'   => esc_html__( 'Guests', RHEA_TEXT_DOMAIN ),
			);
		}

		$meta_repeater = new \Elementor\Repeater();
		$meta_repeater->add_control(
			'rhea_property_meta_display',
			[
				'label'   => esc_html__( 'Select Meta', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $get_meta,
			]
		);

		$meta_repeater->add_control(
			'rhea_meta_repeater_label',
			[
				'label'   => esc_html__( 'Meta Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Add Label', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'rhea_add_meta_select',
			[
				'label'       => esc_html__( 'Add Meta', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $meta_repeater->get_controls(),
				'default'     => $meta_defaults,
				'title_field' => ' {{{ rhea_meta_repeater_label }}}',
				'condition'   => [
					'show_property_meta' => 'yes',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ere_properties_labels',
			[
				'label' => esc_html__( 'Property Labels', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ere_property_featured_label',
			[
				'label'   => esc_html__( 'Featured', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Featured', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'ere_property_build_label',
			[
				'label'   => esc_html__( 'Build', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( ' Build ', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'spaces_section',
			[
				'label' => esc_html__( 'Spaces', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'slider_min_height',
			[
				'label'     => esc_html__( 'Slider Min Height', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-thumb' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_vertical_position',
			[
				'label'     => esc_html__( 'Content Area Vertical Position', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-detail' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_area_padding',
			[
				'label'      => esc_html__( 'Content Box Padding', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea-ultra-main-slider-detail' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border-radius-value',
			[
				'label'      => esc_html__( 'Content Box Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea-ultra-main-slider-detail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'property-title-margin',
			[
				'label'      => esc_html__( 'Property Title Margin', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea-ultra-main-slider-detail h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'rating-stars-size',
			[
				'label'     => esc_html__( 'Rating Stars Size', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .rvr_card_info_wrap .rh-ultra-rvr-rating .rating-stars i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating-stars-margin',
			[
				'label'      => esc_html__( 'Rating Stars Margin', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rvr_card_info_wrap .rh-ultra-rvr-rating .rating-stars' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'added-label-margin',
			[
				'label'      => esc_html__( 'Added Label Margin', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .rvr_card_info_wrap .added-date .added-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'colors_section',
			[
				'label' => esc_html__( 'Colors', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'slider_nav_button_bg_color',
			[
				'label'     => esc_html__( 'Slider Nav Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-nav a' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'slider_nav_button_bg_hover_color',
			[
				'label'     => esc_html__( 'Slider Nav Background Hover', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-nav a:hover' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'slider_nav_button_color',
			[
				'label'     => esc_html__( 'Slider Nav Icon', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-nav a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'slider_nav_button_hover_color',
			[
				'label'     => esc_html__( 'Slider Nav Icon Hover', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-nav a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_wrapper_background',
			[
				'label'     => esc_html__( 'Content Wrapper Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-detail' => 'background-color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'property_featured_tag_bg',
			[
				'label'     => esc_html__( 'Featured Tag Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-slider-featured-tag' => 'background: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'property_featured_tag_color',
			[
				'label'     => esc_html__( 'Featured Tag Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-slider-featured-tag' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'property_title_color',
			[
				'label'     => esc_html__( 'Title', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-detail h3 a' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'property_title_hover_color',
			[
				'label'     => esc_html__( 'Title Hover', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-detail h3 a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'property_build_color',
			[
				'label'     => esc_html__( 'Year Build', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-main-slider-detail h3 span' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'rating_stars_color',
			[
				'label'     => esc_html__( 'Rating Stars', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvr_card_info_wrap .rh-ultra-rvr-rating .rating-stars i' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'rating_bars_color',
			[
				'label'     => esc_html__( 'Rating Count Bars', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvr_card_info_wrap .inspiry_stars_avg_rating .inspiry_rating_percentage .inspiry_rating_line .inspiry_rating_line_inner' => 'background-color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'added_label_color',
			[
				'label'     => esc_html__( 'Added Label', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvr_card_info_wrap .added-date .added-title' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'added_date_color',
			[
				'label'     => esc_html__( 'Added Date', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rvr_card_info_wrap .added-date' => 'color: {{VALUE}}',
				],
				'separator' => 'after'
			]
		);

		$this->add_control(
			'property_meta_icon_color',
			[
				'label'     => esc_html__( 'Meta Icon Primary', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .rh-ultra-dark' => 'fill: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'property_meta_icon_color_secondary',
			[
				'label'     => esc_html__( 'Meta Icon Secondary', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .rh-ultra-light' => 'fill: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'property_meta_title_color',
			[
				'label'     => esc_html__( 'Meta Title', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .rhea-meta-icons-labels' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'property_meta_value_color',
			[
				'label'     => esc_html__( 'Meta Value', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_meta_box .figure' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'property_meta_value_label_color',
			[
				'label'     => esc_html__( 'Area Unit', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_meta_box .label' => 'color: {{VALUE}};',
				],
				'separator' => 'after'
			]
		);

		$this->add_control(
			'property_status_color',
			[
				'label'     => esc_html__( 'Status', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-slider-property-status' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'property_price_color',
			[
				'label'     => esc_html__( 'Price', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ere-price-display' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'property_price_pre_fix_color',
			[
				'label'     => esc_html__( 'Price Pre-fix', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ere-price-prefix' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'property_price_post_fix_color',
			[
				'label'     => esc_html__( 'Price Post-fix', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ere-price-postfix' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'property_typography_section',
			[
				'label' => esc_html__( 'Typography', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_title_typography',
				'label'    => esc_html__( 'Title', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea-ultra-main-slider-detail h3'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_year_typography',
				'label'    => esc_html__( 'Year Build', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea-ultra-main-slider-detail h3 span'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_added_on_typography',
				'label'    => esc_html__( 'Added Date Label', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rvr_card_info_wrap .added-date .added-title'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_added_date_typography',
				'label'    => esc_html__( 'Date', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rvr_card_info_wrap .added-date:not(.added-title)'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_meta_title_typography',
				'label'    => esc_html__( 'Meta Title', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea-meta-icons-labels'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_meta_value_typography',
				'label'    => esc_html__( 'Meta Value', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea_ultra_meta_box .figure'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_area_postfix_typography',
				'label'    => esc_html__( 'Area Unit', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea_ultra_meta_box .label'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_status_typography',
				'label'    => esc_html__( 'Status', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea-ultra-slider-price .rhea-ultra-slider-property-status'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_price_typography',
				'label'    => esc_html__( 'Price', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea-ultra-slider-price .ere-price-display'
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_price_prefix_typography',
				'label'    => esc_html__( 'Price Pre-fix', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea-ultra-slider-price .ere-price-prefix'
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'property_price_postfix_typography',
				'label'    => esc_html__( 'Price Post-fix', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea-ultra-slider-price .ere-price-postfix'
			]
		);

		$this->end_controls_section();
	}

	public function rhea_ultra_main_slider( $settings, $widget_id, $slider_properties_query, $slider_properties_ids = [], $slider_properties = [] ) {
		// Ensure global variables are available.
		if ( ! isset( $settings ) || ! isset( $widget_id ) || ! isset( $slider_properties_query ) ) {
			return;
		}
		?>
        <div id="rhea-ultra-main-slider-wrapper-<?php echo esc_attr( $widget_id ); ?>" class="rhea-ultra-main-slider-wrapper">
            <div id="rhea-ultra-main-slider-<?php echo esc_attr( $widget_id ); ?>" class="rhea-ultra-main-slider flexslider loading">
                <ul class="slides">
					<?php
					if ( $slider_properties_query->have_posts() ) {
						while ( $slider_properties_query->have_posts() ) {
							$slider_properties_query->the_post();
							$property_id       = get_the_ID();
							$property_title    = get_the_title();
							$slider_image_id   = get_post_meta( $property_id, 'REAL_HOMES_slider_image', true );
							$property_address  = get_post_meta( $property_id, 'REAL_HOMES_property_address', true );
							$is_featured       = get_post_meta( $property_id, 'REAL_HOMES_featured', true );
							$bg_slide_position = 'center';

							if ( ! empty( $slider_properties_ids ) && in_array( $property_id, array_keys( $slider_properties_ids ) ) ) {
								$_property = $settings['properties_list'][ $slider_properties_ids[ $property_id ] ];

								if ( ! empty( $_property['property_image']['id'] ) ) {
									$slider_image_id = $_property['property_image']['id'];
									$slider_image_id = rhea_get_wpml_translated_image_id( $slider_image_id );
								}

								if ( ! empty( $_property['property_title'] ) ) {
									$property_title = $_property['property_title'];
								}

								if ( ! empty( $_property['bg_slide_position'] ) ) {
									$bg_slide_position = $_property['bg_slide_position'];
								}
							}

							$image_url = \Elementor\Group_Control_Image_Size::get_attachment_image_src( $slider_image_id, 'thumbnail', $settings );

							// For default properties that are in the slider
							if ( empty( $slider_properties_ids ) && empty( $image_url ) && $slider_image_id ) {
								$image_data = wp_get_attachment_image_src( $slider_image_id, 'full' );
								$image_url  = $image_data ? $image_data[0] : '';
							}

							// Fallback to Featured Image if no slider image is set
							if ( empty( $image_url ) && has_post_thumbnail( $property_id ) ) {
								$image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $property_id ), 'full' );
								$image_url  = $image_data ? $image_data[0] : '';
							}

							?>
                            <li>
                                <a class="rhea-ultra-main-slider-thumb" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( $property_title ); ?>" style="background-position: <?php echo esc_attr( $bg_slide_position ); ?>; background-image: url('<?php echo esc_url( $image_url ) ?>');"></a>
                                <div class="rhea-ultra-main-detail-wrapper">
                                    <div class="rhea-ultra-main-slider-detail">
										<?php if ( ! empty( $is_featured ) ) : ?>
                                            <span class="rhea-ultra-slider-featured-tag">
												<?php echo esc_html( $settings['ere_property_featured_label'] ?? __( 'Featured', RHEA_TEXT_DOMAIN ) ); ?>
											</span>
										<?php endif; ?>

                                        <div class="ultra-slider-property-detail">
                                            <h3 class="rhea-ultra-main-slider-title">
                                                <a href="<?php the_permalink(); ?>"><?php echo esc_html( $property_title ); ?></a>
												<?php
												$property_year_built = get_post_meta( get_the_ID(), 'REAL_HOMES_property_year_built', true );
												if ( ! empty( $property_year_built ) ) {
													$build = esc_html( $settings['ere_property_build_label'] ?? __( ' Build ', RHEA_TEXT_DOMAIN ) );
													echo '<span class="rhea-ultra-slider-year-build">' . esc_html( $build . $property_year_built ) . '</span>';
												}
												?>
                                            </h3>

                                            <div class="rvr_card_info_wrap">
												<?php
												if ( ! empty( $settings['show_ratings_with_title'] ) && 'yes' === $settings['show_ratings_with_title'] && 'true' === get_option( 'inspiry_property_ratings', 'false' ) ) {
													echo '<div class="rh-ultra-rvr-rating">';
													inspiry_rating_average( [ 'rating_string' => false ] );
													echo '</div>';
												}
												if ( ! empty( $settings['show_added_date'] ) && $settings['show_added_date'] === 'yes' ) {
													$added_title = esc_html( $settings['show_added_date_label'] ?? __( 'Added:', RHEA_TEXT_DOMAIN ) );
													echo '<p class="added-date"><span class="added-title">' . esc_html( $added_title ) . '</span> ' . get_the_date() . '</p>';
												}
												?>
                                            </div>
                                        </div>

										<?php
										if ( 'yes' === $settings['show_property_meta'] ) {
											rhea_get_template_part( 'assets/partials/ultra/grid-card-meta' );
										}
										?>

                                        <div class="rhea-ultra-slider-price">
											<?php
											$statuses = get_the_terms( $property_id, 'property-status' );
											if ( ! empty( $statuses ) && ! is_wp_error( $statuses ) ) {
												echo '<span class="rhea-ultra-slider-property-status">' . esc_html( $statuses[0]->name ) . '</span>';
											}
											if ( function_exists( 'ere_get_property_price' ) ) {
                                                $dual_price_class = realhomes_is_dual_price_enabled( $property_id ) ? 'dual-price' : '';
												echo '<p class="rhea-ultra-main-slider-property-price ' . esc_attr( $dual_price_class ) . '">' . ere_get_property_price( get_the_ID(), false, true ) . '</p>';
											}
											?>
                                        </div>
                                    </div>
                                </div>
                            </li>
							<?php
						}
						wp_reset_postdata();
					}
					?>
                </ul>
            </div>

            <div id="rhea-ultra-main-slider-nav-<?php echo esc_attr( $widget_id ); ?>" class="rhea-ultra-main-slider-nav">
                <a href="#" class="flex-prev nav-buttons" aria-label="Previous Slide"><i class="fas fa-caret-left"></i></a>
                <a href="#" class="flex-next nav-buttons" aria-label="Next Slide"><i class="fas fa-caret-right"></i></a>
            </div>
        </div>

		<?php
		$animation_style = $settings['slider_animation_styles'] ?? 'fade';
		$animation_delay = isset( $settings['slider_animation_delay'] ) ? (int)$settings['slider_animation_delay'] : 7000;
		$animation_speed = isset( $settings['slider_animation_speed'] ) ? (int)$settings['slider_animation_speed'] : 1500;
		$keyboard_nav    = isset( $settings['slider_keyboard_nav'] ) ? (bool)$settings['slider_keyboard_nav'] : true;
		$direction_nav   = isset( $settings['slider_direction_nav'] ) ? (bool)$settings['slider_direction_nav'] : true;
		$hover_pause     = isset( $settings['slider_pause_on_hover'] ) ? (bool)$settings['slider_pause_on_hover'] : true;
		$reverse         = isset( $settings['slider_reverse'] ) ? (bool)$settings['slider_reverse'] : false;
		$direction       = $settings['slider_direction'] ?? 'horizontal';
		?>
        <script type="application/javascript">
            ( function ( $ ) {
                'use strict';
                $( document ).ready( function () {
                    if ( $().flexslider ) {
                        $( '#rhea-ultra-main-slider-<?php echo esc_attr( $this->get_id() ); ?>' ).flexslider( {
                            animation          : <?php echo json_encode( $animation_style ); ?>,
                            slideshowSpeed     : <?php echo json_encode( $animation_delay ); ?>,
                            animationSpeed     : <?php echo json_encode( $animation_speed ); ?>,
                            slideshow          : true,
                            controlNav         : false,
                            keyboardNav        : <?php echo json_encode( $keyboard_nav ); ?>,
                            directionNav       : <?php echo json_encode( $direction_nav ); ?>,
                            pauseOnHover       : <?php echo json_encode( $hover_pause ); ?>,
                            reverse            : <?php echo json_encode( $reverse ); ?>,
                            direction          : <?php echo json_encode( $direction ); ?>,
                            customDirectionNav : $( '#rhea-ultra-main-slider-nav-<?php echo esc_attr( $widget_id ); ?> .nav-buttons' ),
                            start              : function ( slider ) {
                                slider.removeClass( 'loading' );
                            }
                        } );
                    }
                } );
            } )( jQuery );
        </script>
		<?php
	}

	protected function render() {
		global $settings;
		global $widget_id;
		$settings  = $this->get_settings_for_display();
		$widget_id = $this->get_id();


		// Collect the selected properties IDs.
		$slider_properties_ids = array();
		if ( ! empty( $settings['properties_list'] ) && is_array( $settings['properties_list'] ) ) {
			foreach ( $settings['properties_list'] as $key => $value ) {
				if ( isset( $value['property_id'] ) && ! empty( $value['property_id'] ) ) {
					$slider_properties_ids[ $value['property_id'] ] = $key;
				}
			}
		}

		if ( ! empty( $slider_properties_ids ) ) {
			$slider_properties_query = new WP_Query( array(
				'post_type'      => 'property',
				'posts_per_page' => -1,
				'post__in'       => array_keys( $slider_properties_ids ),
				"orderby"        => "post__in"
			) );
		} else {
			$slider_properties_query = new WP_Query( array(
				'post_type'        => 'property',
				'posts_per_page'   => ! empty( $settings['number_of_properties'] ) ? intval( $settings['number_of_properties'] ) : 6,
				'suppress_filters' => false,
				'meta_query'       => array(
					array(
						'key'     => 'REAL_HOMES_add_in_slider',
						'value'   => 'yes',
						'compare' => 'LIKE',
					),
				),
			) );
		}
		$this->rhea_ultra_main_slider( $settings, $widget_id, $slider_properties_query, $slider_properties_ids );
	}
}
