<?php
/**
 * Ultra Search Form Widget
 *
 * @modified  2.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class RHEA_ultra_Search_Form_Widget extends \Elementor\Widget_Base {
	private $is_rvr_enabled;

	public function __construct( array $data = [], ?array $args = null ) {
		parent::__construct( $data, $args );
		$this->is_rvr_enabled = rhea_is_rvr_enabled();
	}

	public function get_name() {
		return 'rhea-ultra-search-form-widget';
	}

	public function get_title() {
		return esc_html__( 'RH: Search Form', RHEA_TEXT_DOMAIN );
	}

	public function get_icon() {
		return 'eicon-search rh-ultra-widget';
	}

	public function get_categories() {
		return [ 'ultra-real-homes' ];
	}

	use RHEASearchFormSettings;
	use RHEASearchFormRadiusSettings;

	public function get_script_depends() {

		wp_register_script(
			'rvr-ultra-search-form',
			RHEA_PLUGIN_URL . 'elementor/js/search-form.js',
			[ 'elementor-frontend' ],
			RHEA_VERSION,
			true
		);

		return [
			'jquery-ui-slider',
			'rvr-ultra-search-form'
		];
	}

	public function get_style_depends() {
		wp_register_style( 'rvr-ultra-search-form', RHEA_PLUGIN_URL . '/elementor/css/rvr-search-form.css', array(), RHEA_VERSION );

		return [
			'rvr-ultra-search-form'
		];
	}

	public function search_template_options() {
		if ( function_exists( 'inspiry_pages' ) ) {
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

			return inspiry_pages( $search_pages_args );
		}

		return false;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'rhea_search_basic_settings',
			[
				'label' => esc_html__( 'Basic Settings', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'rhea_select_search_template',
			[
				'label'       => esc_html__( 'Select Search Template', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'If no search template is selected, "RealHomes > Customize Settings > Property Search > Properties Search Page" settings will be applied by default   ', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $this->search_template_options(),
			]
		);


		$this->add_control(
			'rhea_top_field_count',
			[
				'label'              => esc_html__( 'Top Fields To Display', RHEA_TEXT_DOMAIN ),
				'description'        => esc_html__( 'Select number of fields to display in top bar' ),
				'type'               => \Elementor\Controls_Manager::SELECT,
				'default'            => '3',
				'frontend_available' => true,
				'options'            => array(
					'1'  => esc_html__( 'One', RHEA_TEXT_DOMAIN ),
					'2'  => esc_html__( 'Two', RHEA_TEXT_DOMAIN ),
					'3'  => esc_html__( 'Three', RHEA_TEXT_DOMAIN ),
					'4'  => esc_html__( 'Four', RHEA_TEXT_DOMAIN ),
					'5'  => esc_html__( 'Five', RHEA_TEXT_DOMAIN ),
					'6'  => esc_html__( 'Six', RHEA_TEXT_DOMAIN ),
					'7'  => esc_html__( 'Seven', RHEA_TEXT_DOMAIN ),
					'8'  => esc_html__( 'Eight', RHEA_TEXT_DOMAIN ),
					'9'  => esc_html__( 'Nine', RHEA_TEXT_DOMAIN ),
					'10' => esc_html__( 'Ten', RHEA_TEXT_DOMAIN ),
					'11' => esc_html__( 'Eleven', RHEA_TEXT_DOMAIN ),
					'12' => esc_html__( 'Twelve', RHEA_TEXT_DOMAIN ),
				),
			]
		);

		$this->add_control(
			'rhea_default_advance_state',
			[
				'label'   => esc_html__( 'Advance Fields Default State', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'collapsed',
				'options' => array(
					'collapsed' => esc_html__( 'Collapsed', RHEA_TEXT_DOMAIN ),
					'open'      => esc_html__( 'Open', RHEA_TEXT_DOMAIN ),
				),
			]
		);

		$this->add_control(
			'show_labels',
			[
				'label'        => esc_html__( 'Show Labels', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'show_advance_fields',
			[
				'label'        => esc_html__( 'Show Advance Fields Button', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_search_buttons_at_top',
			[
				'label'        => esc_html__( 'Show Search Buttons At Top', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'advance_search_button_label',
			[
				'label'   => esc_html__( 'Advance Fields Button Text', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Advance Search', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'search_button_label',
			[
				'label'   => esc_html__( 'Search Button Text', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Search', RHEA_TEXT_DOMAIN ),
			]
		);


		$this->add_control(
			'show_advance_features',
			[
				'label'        => esc_html__( 'Show More Features ', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'advance_features_styles',
			[
				'label'   => esc_html__( 'More Features Styles', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'select',
				'options' => array(
					'checkbox' => esc_html__( 'Checkbox', RHEA_TEXT_DOMAIN ),
					'select'   => esc_html__( 'Select', RHEA_TEXT_DOMAIN ),
				),
			]
		);

		$this->add_control(
			'advance_features_text',
			[
				'label'   => esc_html__( 'More Features Text', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Looking for certain features', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_responsive_control(
			'advance_features_text_alignment',
			[
				'label'       => esc_html__( 'More Features Button Alignment', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'default'     => 'flex-start',
				'label_block' => true,
				'options'     => array(
					'flex-start' => esc_html__( 'Flex Start', RHEA_TEXT_DOMAIN ),
					'flex-end'   => esc_html__( 'Flex End', RHEA_TEXT_DOMAIN ),
					'center'     => esc_html__( 'Center', RHEA_TEXT_DOMAIN ),
				),
				'selectors'   => [
					'{{WRAPPER}} .rhea_open_more_features_outer' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_fields_separator',
			[
				'label'        => esc_html__( 'Show Fields Separator', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_search_fields_sorting',
			[
				'label' => esc_html__( 'Search Fields Sorting', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'rhea_search_select_sort',
			[
				'label' => esc_html__( 'Control Name', RHEA_TEXT_DOMAIN ),
				'type'  => 'rhea-select-unit-control',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_search_typo_section',
			[
				'label' => esc_html__( 'Typography', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'fields_typography',
				'label'    => esc_html__( 'Search Fields', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' =>
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker > .dropdown-toggle, 
					{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location > .dropdown-toggle,
                    {{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__option input[type="text"]',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'fields_dropdown_typography',
				'label'    => esc_html__( 'Search Fields Dropdown', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu li a .text,
				               {{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu li a .text',
			]
		);

		$this->add_responsive_control(
			'rhea_select_deselect_icon_size',
			[
				'label'     => esc_html__( 'Drop Down Select/Deselect All Icons Size', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_select_bs_buttons svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'fields_search_button_typography',
				'label'    => esc_html__( 'Search Button', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_search_form_button',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'fields_advance_search_button_typography',
				'label'    => esc_html__( 'Advance Search Button', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_advanced_expander',
			]
		);


		$this->add_responsive_control(
			'rhea_search_expander_icon_size',
			[
				'label'     => esc_html__( 'Advance Fields Button Icon Size (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_advanced_expander svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'more_features_button_typography',
				'label'    => esc_html__( 'More Feature Button', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea_open_more_features',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'more_features_fields_typography',
				'label'    => esc_html__( 'More Feature Checkbox Fields', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea-more-options-wrapper label',
			]
		);

		$this->add_responsive_control(
			'more_features_fields_checkbox_size',
			[
				'label'     => esc_html__( 'Feature Checkbox Size (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea-more-options-wrapper label:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'more_features_checkbox_typography',
				'label'    => esc_html__( 'More Feature Checkbox Checked', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea-more-options-wrapper label:before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'price_slider_text_typo',
				'label'    => esc_html__( 'Price Slider Text', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea_price_slider_wrapper > .rhea_price_label',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'price_slider_form_to_typo',
				'label'    => esc_html__( 'Price Slider From/To Text', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea_price_slider_wrapper > .rhea_price_range',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'price_slider_price_typo',
				'label'    => esc_html__( 'Price Slider Min Max Range ', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .rhea_price_slider_wrapper .rhea_price_display',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'ajax_ph_input_typo',
				'label'    => esc_html__( 'Ajax based location field Input ', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .bs-searchbox .form-control',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'ajax_no_result_typo',
				'label'    => esc_html__( 'Ajax no result text', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .bootstrap-select .no-results',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'ajax_drop_down_title_typo',
				'label'     => esc_html__( 'Keyword Search DropDown Title', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector'  => '{{WRAPPER}} .rhea_ultra_search_form_wrapper .sfoi_ajax_title',
				'condition' => [
					'enable_ajax_search' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'ajax_drop_down_status_typo',
				'label'     => esc_html__( 'Keyword Search DropDown Status', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector'  => '{{WRAPPER}} .rhea_ultra_search_form_wrapper .sfoi_ajax_status',
				'condition' => [
					'enable_ajax_search' => 'yes',
				],
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'rhea_search_form_sizes',
			[
				'label' => esc_html__( 'Basic Styles', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'rhea_select_position',
			[
				'label'     => esc_html__( 'Position', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'relative' => esc_html__( 'Relative', RHEA_TEXT_DOMAIN ),
					'absolute' => esc_html__( 'Absolute', RHEA_TEXT_DOMAIN ),
				),
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper' => 'position: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'position_top_bottom',
			array(
				'label'   => esc_html__( 'Vertical Position', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'top'    => array(
						'title' => esc_html__( 'Top', RHEA_TEXT_DOMAIN ),
						'icon'  => 'eicon-v-align-top',
					),
					'bottom' => array(
						'title' => esc_html__( 'Bottom', RHEA_TEXT_DOMAIN ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'toggle'  => true,
			)
		);

		$this->add_responsive_control(
			'position-from-top',
			[
				'label'      => esc_html__( 'Position From Top', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'position_top_bottom' => 'top',
				],
			]
		);

		$this->add_responsive_control(
			'position-from-bottom',
			[
				'label'      => esc_html__( 'Position From Bottom', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'position_top_bottom' => 'bottom',
				],
			]
		);

		$this->add_responsive_control(
			'position_left_right',
			array(
				'label'   => esc_html__( 'Horizontal Position', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'left'  => array(
						'title' => esc_html__( 'Left', RHEA_TEXT_DOMAIN ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', RHEA_TEXT_DOMAIN ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'toggle'  => true,
			)
		);

		$this->add_responsive_control(
			'position-from-left',
			[
				'label'      => esc_html__( 'Position From Left', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'position_left_right' => 'left',
				],
			]
		);

		$this->add_responsive_control(
			'position-from-right',
			[
				'label'      => esc_html__( 'Position From Right', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper' => 'right: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'position_left_right' => 'right',
				],
			]
		);

		$this->add_responsive_control(
			'form-max-width',
			[
				'label'      => esc_html__( 'Search Form Max Width', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_search_outer_padding',
			[
				'label'      => esc_html__( 'Search Form Outer Padding', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_search_wrapper_padding',
			[
				'label'      => esc_html__( 'Search Form Wrapper Padding', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea-ultra-search-form-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_fields_height',
			[
				'label'     => esc_html__( 'Fields Heights (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 30,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_advanced_expander'                      => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_search_form_button'                     => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker > .dropdown-toggle'                => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location > .dropdown-toggle'       => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__option input[type="text"]' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'rhea_advance_top_fields_width',
			[
				'label'          => esc_html__( 'Top Bar Fields Width (%)', RHEA_TEXT_DOMAIN ),
				'description'    => esc_html__( 'Width can be set individually for default fields from Content tab', RHEA_TEXT_DOMAIN ),
				'type'           => \Elementor\Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'range'          => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors'      => [
					'{{WRAPPER}} .rhea_top_search_box .rhea_prop_search__option' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_advance_fields_width',
			[
				'label'          => esc_html__( 'Collapsed Fields Width (%)', RHEA_TEXT_DOMAIN ),
				'description'    => esc_html__( 'Width can be set individually for default fields from Content tab. (Note: These changes will not effect on Min Max Price Slider. For that go to Content > Min MAx Price)', RHEA_TEXT_DOMAIN ),
				'type'           => \Elementor\Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'range'          => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'tablet_default' => [
					'size' => 33.333,
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors'      => [
//					'{{WRAPPER}} .rhea_collapsed_search_fields_inner .rhea_prop_search__option' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rhea_collapsed_search_fields_inner .rhea_prop_search__option:not(.rhea_price_slider_field)' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_search_buttons_wrapper_margin',
			[
				'label'      => esc_html__( 'Search Buttons Wrapper Margin', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_search_button_wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'rhea_search_buttons_wrapper_padding',
			[
				'label'      => esc_html__( 'Search Buttons Wrapper Padding', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_search_button_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'search_buttons_alignment',
			[
				'label'       => esc_html__( 'Search Button Alignments', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'default'     => 'flex-end',
				'label_block' => true,
				'options'     => array(
					'flex-end'      => esc_html__( 'Flex End', RHEA_TEXT_DOMAIN ),
					'flex-start'    => esc_html__( 'Flex Start', RHEA_TEXT_DOMAIN ),
					'center'        => esc_html__( 'Center', RHEA_TEXT_DOMAIN ),
					'space-around'  => esc_html__( 'Space around', RHEA_TEXT_DOMAIN ),
					'space-between' => esc_html__( 'Space Between', RHEA_TEXT_DOMAIN ),
				),
				'selectors'   => [
					'{{WRAPPER}} .rhea_search_button_wrapper' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_expander_button_padding',
			[
				'label'      => esc_html__( 'Advance Button Padding (px)', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_advanced_expander' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_search_button_padding',
			[
				'label'      => esc_html__( 'Search Button Padding (px)', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_search_form_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'rhea_dropdown_padding',
			[
				'label'      => esc_html__( 'Search Form Dropdown List Padding', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu li a'          => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'rhea_select_deselect_box_height',
			[
				'label'     => esc_html__( 'Drop Down Select/Deselect Box Height', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu .btn-block button'          => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu .btn-block button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_select_deselect_box_border',
			[
				'label'      => esc_html__( 'Drop Down Select/Deselect Box Border', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu .btn-block'          => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu .btn-block' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_items_in',
			[
				'label'   => esc_html__( 'Drop Drown List Height', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 3,
				'max'     => 15,
				'step'    => .5,
				'default' => 5.5,
			]

		);

		$this->add_responsive_control(
			'rhea_horizontal_fields_spacings',
			[
				'label'     => esc_html__( 'Fields Horizontal Spacing (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__option'   => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_search_button_wrapper' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_top_search_fields'     => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rhea_collapsed_search_fields_inner'                         => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_search_button_gap',
			[
				'label'     => esc_html__( 'Search Buttons Gap (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_search_button_wrapper' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_labels_margin_bottom',
			[
				'label'     => esc_html__( 'Labels Margin Bottom (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea_fields_labels' => 'margin-bottom: {{SIZE}}{{UNIT}};'

				],
			]
		);

		$this->add_responsive_control(
			'rhea_vertical_fields_spacings',
			[
				'label'     => esc_html__( 'Fields Margin Bottom (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__option'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_search_button_wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_horizontal_features_spacings',
			[
				'label'     => esc_html__( 'More Features Horizontal Spacing (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea-more-options-wrapper .rhea-option-bar' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rhea-more-options-wrapper'                  => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_vertical_features_spacings',
			[
				'label'     => esc_html__( 'More Features Margin Bottom (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea-more-options-wrapper .rhea-option-bar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_search_form_colors',
			[
				'label' => esc_html__( 'Colors', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]

		);

		$this->add_control(
			'rhea_search_form_bg',
			[
				'label'     => esc_html__( 'Search Form Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-search-form-fields' => 'background: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'rhea_search_button_bg',
			[
				'label'     => esc_html__( 'Search Button Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_search_form_button' => 'background: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'rhea_search_button_bg_hover',
			[
				'label'     => esc_html__( 'Search Button Background Hover', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_search_form_button:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_search_button_text',
			[
				'label'     => esc_html__( 'Search Button Text', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_search_form_button span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_search_button_text_hover',
			[
				'label'     => esc_html__( 'Search Button Text Hover', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_search_form_button:hover span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'rhea_advance_search_button_bg',
			[
				'label'     => esc_html__( 'Advance Search Button Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_advanced_expander' => 'background: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'rhea_advance_search_button_bg_hover',
			[
				'label'     => esc_html__( 'Advance Search Button Background Hover', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_advanced_expander:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_advance_search_button_icon',
			[
				'label'     => esc_html__( 'Advance Search Button Icon', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_advanced_expander .search-ultra-plus'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_advanced_expander .search-ultra-plus svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_search_button_icon_hover',
			[
				'label'     => esc_html__( 'Advance Search Button Icon Hover', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_advanced_expander:hover .search-ultra-plus'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_advanced_expander:hover .search-ultra-plus svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_fields_border_color',
			[
				'label'     => esc_html__( 'Fields Border Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker > .dropdown-toggle'                => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location > .dropdown-toggle'       => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__option input[type="text"]' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rhea_price_slider_wrapper'                                                   => 'border-color: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'rhea_text_fields_focused_color',
			[
				'label'     => esc_html__( 'Fields Focused Border/Icon Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea-text-field-wrapper:focus-within .feather-tag'   => 'stroke: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_mod_text_field:focus-within label .icon-search' => 'stroke: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__option input[type="text"]:focus'   => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_fields_text_color',
			[
				'label'     => esc_html__( 'Fields Text Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker > .dropdown-toggle'                                                        => 'color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location > .dropdown-toggle'                                               => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__option input[type="text"]'                                         => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper ::placeholder'                                                                        => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper ::-ms-input-placeholder'                                                              => 'color: {{VALUE}}',
					'{{WRAPPER}} .bs-searchbox .form-control'                                                                                          => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper ::-webkit-input-placeholder'                                                          => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__option .rhea_price_slider_wrapper .rhea_price_label'               => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_price_slider_wrapper .rhea_price_range'                                                                         => 'color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker > 
					.dropdown-toggle .caret, .bootstrap-select.rhea_multi_select_picker_location > .dropdown-toggle .caret'        => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker.dropup > 
					.dropdown-toggle .caret, .bootstrap-select.rhea_multi_select_picker_location.dropup > .dropdown-toggle .caret' => 'border-bottom-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_fields_label_color',
			[
				'label'     => esc_html__( 'Fields Label Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_fields_labels' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_price_from_to_colors',
			[
				'label'     => esc_html__( 'Price Slider From/To color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_price_slider_wrapper .rhea_price_range' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);
		$this->add_control(
			'rhea_price_range_colors',
			[
				'label'     => esc_html__( 'Price Slider Range Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_price_slider_wrapper .rhea_price_display' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_control(
			'rhea_price_bg_color',
			[
				'label'     => esc_html__( 'Price Slider UI Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .ui-widget.ui-widget-content' => 'background: {{VALUE}}; border-color: {{VALUE}};',
				],
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_control(
			'rhea_price_slider_color',
			[
				'label'     => esc_html__( 'Price Slider UI Range', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_price_slider_wrapper .ui-slider .ui-slider-range' => 'background: {{VALUE}};',
				],
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_control(
			'rhea_price_darg_color',
			[
				'label'     => esc_html__( 'Price Slider UI Drag Button', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .ui-widget-content .ui-state-default' => 'background: {{VALUE}};',
				],
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);


		$this->add_control(
			'rhea_dropdown_bg_color',
			[
				'label'     => esc_html__( 'Dropdown Background Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu'          => 'background: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu' => 'background: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select .no-results'                                      => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_caret_bg_color',
			[
				'label'     => esc_html__( 'Dropdown Caret Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__selectwrap .bs-caret' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_text_color',
			[
				'label'     => esc_html__( 'Dropdown Text Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu li a'          => 'color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu li a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select .no-results'                                           => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_text_selected_color',
			[
				'label'     => esc_html__( 'Dropdown Text Select/Hover Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu li.selected a'          => 'color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu li.selected a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu li:hover a'             => 'color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu li:hover a'    => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_bg_selected_color',
			[
				'label'     => esc_html__( 'Dropdown Background Select/Hover Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu li.selected'          => 'background: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu li.selected' => 'background: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu li:hover'             => 'background: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu li:hover'    => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_select_deselect_border_color',
			[
				'label'     => esc_html__( 'Dropdown Buttons and Ajax placeholder Border', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu .btn-block'          => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu .btn-block' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .bs-searchbox .form-control'                                                   => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_select_deselect_color',
			[
				'label'     => esc_html__( 'Dropdown Buttons Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_select_bs_buttons.rhea_bs_select svg'         => 'fill: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_select_bs_buttons.rhea_bs_deselect .rhea_des' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_select_deselect_bg_color',
			[
				'label'     => esc_html__( 'Dropdown Buttons Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu .btn-block button'          => 'background: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu .btn-block button' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_select_deselect_hover_color',
			[
				'label'     => esc_html__( 'Dropdown Buttons Hover Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .actions-btn:hover .rhea_select_bs_buttons.rhea_bs_select svg'         => 'fill: {{VALUE}}',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .actions-btn:hover .rhea_select_bs_buttons.rhea_bs_deselect .rhea_des' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_select_deselect_hover_bg_color',
			[
				'label'     => esc_html__( 'Dropdown Buttons Hover Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker .dropdown-menu .btn-block button:hover'          => 'background: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location .dropdown-menu .btn-block button:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_search_form_bg_btn_features',
			[
				'label'     => esc_html__( 'More Features Button Background Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_open_more_features' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'rhea_search_form_bg_btn_hover_features',
			[
				'label'     => esc_html__( 'More Features Button Open/Hover Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_open_more_features:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_search_form_btn_features',
			[
				'label'     => esc_html__( 'More Features Button Text Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_open_more_features'             => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_open_more_features span:before' => 'background: {{VALUE}}',
					'{{WRAPPER}} .rhea_open_more_features span:after'  => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'rhea_search_form_btn_hover_features',
			[
				'label'     => esc_html__( 'More Features Button Hover Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_open_more_features:hover'             => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea_open_more_features:hover span:before' => 'background: {{VALUE}}',
					'{{WRAPPER}} .rhea_open_more_features:hover span:after'  => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_search_form_features_box',
			[
				'label'     => esc_html__( 'Features Checkbox Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-more-options-mode-container .rhea-more-options-wrapper label:before'               => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .rhea-more-options-mode-container .rhea-more-options-wrapper input:checked+label:before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_search_form_features_bg_box',
			[
				'label'     => esc_html__( 'Features Checkbox Background', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-more-options-mode-container .rhea-more-options-wrapper input:checked+label:before' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_search_form_features_color',
			[
				'label'     => esc_html__( 'Features Text Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-more-options-mode-container .rhea-more-options-wrapper label' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'rhea_search_form_features_border_color',
			[
				'label'     => esc_html__( 'Features Tabs Border Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-features-styles-2 .rhea-option-bar label' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'advance_features_styles' => 'select',
				]
			]
		);
		$this->add_control(
			'rhea_search_form_features_bg_color',
			[
				'label'     => esc_html__( 'Features Tabs Background Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-features-styles-2 .rhea-option-bar label' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'advance_features_styles' => 'select',
				]
			]
		);

		$this->add_control(
			'rhea_search_form_features_active',
			[
				'label'     => esc_html__( 'Features Tabs Active/Hover Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-features-styles-2 .rhea-option-bar input:checked+label' => 'background-color: {{VALUE}}; border-color: {{VALUE}}',
					'{{WRAPPER}} .rhea-features-styles-2 .rhea-option-bar label:hover'         => 'background-color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'condition' => [
					'advance_features_styles' => 'select',
				]
			]
		);

		$this->add_control(
			'rhea_search_form_features_text_color',
			[
				'label'     => esc_html__( 'Features Tabs Active Text Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-features-styles-2 .rhea-option-bar input:checked+label' => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea-features-styles-2 .rhea-option-bar label:hover'         => 'color: {{VALUE}}',
				],
				'condition' => [
					'advance_features_styles' => 'select',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_dropdown_scroll_section',
			[
				'label' => esc_html__( 'Dropdown List Scroll Styles', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'rhea_dropdown_scroll_size',
			[
				'label'      => esc_html__( 'Scroll Width (px)', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker.open .dropdown-menu ::-webkit-scrollbar'          => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location.open .dropdown-menu ::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rhea-properties-data-list::-webkit-scrollbar'                                               => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_scroll_thumb',
			[
				'label'     => esc_html__( 'Scroll Thumb Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker.open .dropdown-menu ::-webkit-scrollbar-thumb'          => 'background-color: {{VALUE}}; outline-color: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location.open .dropdown-menu ::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}}; outline-color: {{VALUE}}',
					'{{WRAPPER}} .rhea-properties-data-list::-webkit-scrollbar-thumb'                                               => 'background-color: {{VALUE}}; outline-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rhea_dropdown_scroll_track',
			[
				'label'     => esc_html__( 'Scroll Track Box Shadow', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker.open .dropdown-menu ::-webkit-scrollbar-track'          => 'box-shadow: {{VALUE}}; -webkit-box-shadow: {{VALUE}}',
					'{{WRAPPER}} .bootstrap-select.rhea_multi_select_picker_location.open .dropdown-menu ::-webkit-scrollbar-track' => 'box-shadow: {{VALUE}}; -webkit-box-shadow: {{VALUE}}',
					'{{WRAPPER}} .rhea-properties-data-list::-webkit-scrollbar-track'                                               => 'box-shadow: {{VALUE}}; -webkit-box-shadow: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'top_tabs_settings',
			[
				'label' => esc_html__( 'Top Tabs Layout Styles', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_responsive_control(
			'top_tabs_wrapper_padding',
			[
				'label'      => esc_html__( 'Taps Wrapper Padding', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea-search-top-tabs-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'top_tabs_inner_padding',
			[
				'label'      => esc_html__( 'Tab Inner Padding', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea-mod-tabs-list .rhea-mod-tab-name'     => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .rhea-ultra-tabs-list .rhea-ultra-tab-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space_between_tabs',
			[
				'label'     => esc_html__( 'Tabs Horizontal Space (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-tabs-list' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_space_between_tabs',
			[
				'label'     => esc_html__( 'Tabs Vertical Space (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-tabs-list' => 'row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'tabs_typography',
				'label'    => esc_html__( 'Tabs Typography', RHEA_TEXT_DOMAIN ),
				'global'   => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' =>
					'{{WRAPPER}} .rhea-ultra-tabs-list .rhea-ultra-tab-name',
			]
		);


		$this->add_control(
			'tabs_bg_color',
			[
				'label'     => esc_html__( 'Tabs Background Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-tabs-list .rhea-ultra-tab-name' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tabs_bg_hover_color',
			[
				'label'     => esc_html__( 'Tabs Background Hover/Active Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-tabs-list input[type=radio]:hover~*'   => 'background: {{VALUE}}',
					'{{WRAPPER}} .rhea-ultra-tabs-list input[type=radio]:checked~*' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tabs_text_color',
			[
				'label'     => esc_html__( 'Tabs Text Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-tabs-list .rhea-ultra-tab-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tabs_text_hover_color',
			[
				'label'     => esc_html__( 'Tabs Text Hover/Active Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-tabs-list input[type=radio]:hover~*'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .rhea-ultra-tabs-list input[type=radio]:checked~*' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tabs_border_heading',
			[
				'label' => esc_html__( 'Tabs Border Settings', RHEA_TEXT_DOMAIN ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs(
			'search_tabs_border'
		);

		$this->start_controls_tab(
			'tab_styles_normal',
			[
				'label' => esc_html__( 'Normal', RHEA_TEXT_DOMAIN ),
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'tabs-border',
				'label'    => esc_html__( 'Tabs Border', RHEA_TEXT_DOMAIN ),
				'selector' => '{{WRAPPER}} .rhea-ultra-tabs-list .rhea-ultra-tab-name',
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_styles_hover',
			[
				'label' => esc_html__( 'Hover', RHEA_TEXT_DOMAIN ),
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'tabs-border-hover',
				'label'    => esc_html__( 'Tabs Border Hover', RHEA_TEXT_DOMAIN ),
				'selector' => '{{WRAPPER}} .rhea-ultra-tabs-list input[type=radio]:hover~*,{{WRAPPER}} .rhea-ultra-tabs-list input[type=radio]:checked~*',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'search_form_radiuses',
			[
				'label' => esc_html__( 'Radiuses', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'search_form_border_radius',
			[
				'label'      => esc_html__( 'Search Form Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea-ultra-search-form-inner'                                  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea-ultra-search-form-fields' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'status-tabs-border-radius',
			[
				'label'      => esc_html__( 'Status Tabs Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea-ultra-tabs-list .rhea-ultra-tab-name' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'dropdown-arrow-border-radius',
			[
				'label'      => esc_html__( 'Dropdown Arrow Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_prop_search__selectwrap .bs-caret' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'dropdown-border-radius',
			[
				'label'      => esc_html__( 'DropDown Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .bootstrap-select .dropdown-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'advance-search-border-radius',
			[
				'label'      => esc_html__( 'Advance search Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_search_button_wrapper .rhea_advanced_expander' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'search-border-radius',
			[
				'label'      => esc_html__( 'Search Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea_search_button_wrapper .rhea_search_form_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'features-button-border-radius',
			[
				'label'      => esc_html__( 'Features Button Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea-more-options-mode-container .rhea_open_more_features' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'features-border-radius',
			[
				'label'      => esc_html__( 'Features Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .rhea-features-styles-2 .rhea-option-bar label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ranges-bar-border-radius',
			[
				'label'      => esc_html__( 'Range Bar Edges Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .ui-widget.ui-widget-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'ranges-button-border-radius',
			[
				'label'      => esc_html__( 'Range Button Radius Button', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .ui-widget-content .ui-state-default' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'calendar-wrapper-border-radius',
			[
				'label'      => esc_html__( 'Calendar Wrapper Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .daterangepicker' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'calendar-dates-border-radius',
			[
				'label'      => esc_html__( 'Calendar Dates Border Radius', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_ultra_search_form_wrapper .daterangepicker .calendar-table td' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'rhea_property_location_section',
			[
				'label' => esc_html__( 'Property Locations', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'location_radius_search',
			[
				'label'        => esc_html__( 'Enable Radius Location Search (Google Maps Only)', RHEA_TEXT_DOMAIN ),
				'description'  => esc_html__( 'To enable Radius Search, go to "Dashboard > Easy Real Estate > Maps" tab.', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'rhea_select_locations',
			[
				'label'       => esc_html__( 'Number of Location Select Boxes', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'In case of 1 location box, all locations will be listed in that select box. In case of 2 or more, Each select box will list parent locations of a level that matches its number and all the remaining children locations will be listed in last select box.', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'default'     => '1',
				'label_block' => true,
				'options'     => [
					'1' => esc_html__( '1', RHEA_TEXT_DOMAIN ),
					'2' => esc_html__( '2', RHEA_TEXT_DOMAIN ),
					'3' => esc_html__( '3', RHEA_TEXT_DOMAIN ),
					'4' => esc_html__( '4', RHEA_TEXT_DOMAIN ),
				],
				'condition'   => [
					'location_radius_search' => '',
				]
			]
		);

		$this->add_control(
			'rhea_location_title_1',
			[
				'label'      => esc_html__( 'Main Location Label', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => esc_html__( 'Main Location', RHEA_TEXT_DOMAIN ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '1'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '2'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '3'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);
		$this->add_control(
			'rhea_location_ph_1',
			[
				'label'      => esc_html__( 'Main Location Placeholder', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => esc_html__( 'All Main Locations', RHEA_TEXT_DOMAIN ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '1'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '2'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '3'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);

		$this->add_control(
			'location_count_placeholder',
			[
				'label'       => esc_html__( 'Location Count Placeholder', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Placeholder text when more than 2 values are being selected', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Locations Selected', RHEA_TEXT_DOMAIN ),
				'condition'   => [
					'rhea_select_locations' => '1'
				]
			]
		);

		$this->add_control(
			'rhea_location_title_2',
			[
				'label'      => esc_html__( 'Child Location ', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => esc_html__( 'Child Location', RHEA_TEXT_DOMAIN ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '2'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '3'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				]
			]
		);

		$this->add_control(
			'rhea_location_ph_2',
			[
				'label'      => esc_html__( 'Main Child Placeholder', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => esc_html__( 'All Child Locations', RHEA_TEXT_DOMAIN ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '2'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '3'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);

		$this->add_control(
			'rhea_location_title_3',
			[
				'label'      => esc_html__( 'Grand Child Location', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => esc_html__( 'Grand Child Location', RHEA_TEXT_DOMAIN ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '3'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);
		$this->add_control(
			'rhea_location_ph_3',
			[
				'label'      => esc_html__( 'Grand Child Placeholder', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => esc_html__( 'All Grand Child Locations', RHEA_TEXT_DOMAIN ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '3'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);
		$this->add_control(
			'rhea_location_title_4',
			[
				'label'      => esc_html__( 'Great Grand Child Location', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => esc_html__( 'Great Grand Child Location', RHEA_TEXT_DOMAIN ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);

		$this->add_control(
			'rhea_location_ph_4',
			[
				'label'      => esc_html__( 'Great Grand Child Placeholder', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::TEXT,
				'default'    => esc_html__( 'All Great Grand Child Locations', RHEA_TEXT_DOMAIN ),
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);

		$this->add_control(
			'set_multiple_location',
			[
				'label'        => esc_html__( 'Enable Multi Select ?', RHEA_TEXT_DOMAIN ),
				'description'  => esc_html__( 'This will enable multi select if Number of Location Select Boxes is equal to 1.', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
				'conditions'   => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '1'
						],
						[
							'name'     => 'location_radius_search',
							'operator' => '==',
							'value'    => ''
						],
					]
				],
			]
		);

		$this->add_control(
			'set_ajax_location',
			[
				'label'        => esc_html__( 'Enable Ajax Based Locations ?', RHEA_TEXT_DOMAIN ),
				'description'  => esc_html__( 'This will enable ajax based locations if Number of Location Select Boxes is equal to 1.', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
				'conditions'   => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '1'
						],
						[
							'name'     => 'location_radius_search',
							'operator' => '==',
							'value'    => ''
						],
					]
				],
			]
		);

		$this->add_control(
			'rhea_ajax_input_placeholder',
			[
				'label'     => esc_html__( 'Live Search Placeholder', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Enter location name', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'set_ajax_location' => 'yes',
				],
			]
		);

		$this->add_control(
			'rhea_no_result_matched',
			[
				'label'     => esc_html__( 'No Result Text', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'No Location Matched', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'set_ajax_location' => 'yes'
				]
			]
		);

		$this->add_control(
			'hide_empty_location',
			[
				'label'        => esc_html__( 'Hide Empty Locations ?', RHEA_TEXT_DOMAIN ),
				'description'  => esc_html__( 'Optimize Locations by hiding the ones with zero property.', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'location_radius_search' => '',
				]
			]
		);
		$this->add_control(
			'sort_location_alphabetically',
			[
				'label'        => esc_html__( 'Sort Locations Alphabetically ?', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'set_ajax_location'     => 'yes',
					'rhea_select_locations' => '1',
				],
			]
		);

		$this->add_responsive_control(
			'property_locations_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_prop_locations_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'location_field_separator_color',
			[
				'label'       => esc_html__( 'Location 1 Separator Color', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}} .rhea-ultra-field-separator.location-separator_0 .rhea_prop_search__selectwrap:after'          => 'background: {{VALUE}}',
					'{{WRAPPER}} .rh_geolocation_field_wrapper .geolocation-address-field-inner .rhea-text-field-wrapper:after' => 'background: {{VALUE}}'
				],

			]
		);
		$this->add_responsive_control(
			'location_field_separator_color_2',
			[
				'label'       => esc_html__( 'Location 2 Separator Color', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}} .rhea-ultra-field-separator.location-separator_1 .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '2'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '3'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);
		$this->add_responsive_control(
			'location_field_separator_color_3',
			[
				'label'       => esc_html__( 'Location 3 Separator Color', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}} .rhea-ultra-field-separator.location-separator_2 .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '3'
						],
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);
		$this->add_responsive_control(
			'location_field_separator_color_4',
			[
				'label'       => esc_html__( 'Location 4 Separator Color', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}} .rhea-ultra-field-separator.location-separator_3 .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'rhea_select_locations',
							'operator' => '==',
							'value'    => '4'
						],
					]
				],
			]
		);

		$this->SF_fields_icons( 'enable_location_icon', 'location_icon', 'rhea_prop_locations_field' );

		$this->end_controls_section();

		$this->SF_radius_fields();

		if ( $this->is_rvr_enabled ) {
			$this->start_controls_section(
				'rhea_property_check_in_out_section',
				[
					'label' => esc_html__( 'Check In/Out', RHEA_TEXT_DOMAIN ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'show_single_checkin_checkout_field',
				[
					'label'        => esc_html__( 'Show Single Field', RHEA_TEXT_DOMAIN ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
					'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
					'return_value' => 'yes',
					'default'      => 'no',
				]
			);

			$this->add_control(
				'check_in_label',
				[
					'label'   => esc_html__( 'Check In Label', RHEA_TEXT_DOMAIN ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Check In', RHEA_TEXT_DOMAIN ),
				]
			);

			$this->add_control(
				'check_in_placeholder',
				[
					'label'   => esc_html__( 'Check In Placeholder', RHEA_TEXT_DOMAIN ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Check In', RHEA_TEXT_DOMAIN ),
				]
			);

			$this->add_control(
				'check_out_label',
				[
					'label'      => esc_html__( 'Check Out Label', RHEA_TEXT_DOMAIN ),
					'type'       => \Elementor\Controls_Manager::TEXT,
					'default'    => esc_html__( 'Check Out', RHEA_TEXT_DOMAIN ),
					'conditions' => [
						'terms' => [
							[
								'name'     => 'show_single_checkin_checkout_field',
								'operator' => '!==',
								'value'    => 'yes'
							],
						]
					],
				]
			);

			$this->add_control(
				'check_out_placeholder',
				[
					'label'      => esc_html__( 'Check Out Placeholder', RHEA_TEXT_DOMAIN ),
					'type'       => \Elementor\Controls_Manager::TEXT,
					'default'    => esc_html__( 'Check Out', RHEA_TEXT_DOMAIN ),
					'conditions' => [
						'terms' => [
							[
								'name'     => 'show_single_checkin_checkout_field',
								'operator' => '!==',
								'value'    => 'yes'
							],
						]
					],
				]
			);

			$this->add_responsive_control(
				'check_in_field_size',
				[
					'label'       => esc_html__( 'Check In Field Size (%)', RHEA_TEXT_DOMAIN ),
					'description' => esc_html__( 'Size will be implemented on both fields. Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
					'type'        => \Elementor\Controls_Manager::SLIDER,
					'size_units'  => [ '%', 'px' ],
					'range'       => [
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
					],
					'selectors'   => [
						'{{WRAPPER}} .rhea_mod_text_field.rvr_check_in' => 'width: {{SIZE}}{{UNIT}} !important;'
					],
				]
			);

			$this->add_responsive_control(
				'check_out_field_size',
				[
					'label'       => esc_html__( 'Check Out Field Size (%)', RHEA_TEXT_DOMAIN ),
					'description' => esc_html__( 'Size will be implemented on both fields. Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
					'type'        => \Elementor\Controls_Manager::SLIDER,
					'size_units'  => [ '%', 'px' ],
					'range'       => [
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
					],
					'selectors'   => [
						'{{WRAPPER}} .rhea_mod_text_field.rvr_check_out' => 'width: {{SIZE}}{{UNIT}} !important;'
					],
					'conditions'  => [
						'terms' => [
							[
								'name'     => 'show_single_checkin_checkout_field',
								'operator' => '!==',
								'value'    => 'yes'
							],
						]
					],
				]
			);

			$this->add_responsive_control(
				'calender-position',
				[
					'label'      => esc_html__( 'Calender Horizontal Position', RHEA_TEXT_DOMAIN ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ '%', 'px' ],
					'range'      => [
						'%'  => [
							'min' => -100,
							'max' => 100,
						],
						'px' => [
							'min' => -1000,
							'max' => 1000,
						],
					],
					'selectors'  => [
						'{{WRAPPER}} .rhea_ultra_search_form_wrapper .daterangepicker' => 'margin-left: {{SIZE}}{{UNIT}} !important;'
					],
				]
			);

			$this->add_responsive_control(
				'check_in_separator_color',
				[
					'label'     => esc_html__( 'Check In Field Separator Color', RHEA_TEXT_DOMAIN ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rhea-ultra-field-separator.rvr_check_in .rhea-text-field-wrapper:after' => 'background: {{VALUE}}',
					]
				]
			);
			$this->add_responsive_control(
				'check_out_separator_color',
				[
					'label'      => esc_html__( 'Check Out Field Separator Color', RHEA_TEXT_DOMAIN ),
					'type'       => \Elementor\Controls_Manager::COLOR,
					'selectors'  => [
						'{{WRAPPER}} .rhea-ultra-field-separator.rvr_check_out .rhea-text-field-wrapper:after' => 'background: {{VALUE}}',
					],
					'conditions' => [
						'terms' => [
							[
								'name'     => 'show_single_checkin_checkout_field',
								'operator' => '!==',
								'value'    => 'yes'
							],
						]
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'rhea_property_guests_section',
				[
					'label' => esc_html__( 'Guests', RHEA_TEXT_DOMAIN ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'guests_label',
				[
					'label'   => esc_html__( 'Guests Label', RHEA_TEXT_DOMAIN ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Guests', RHEA_TEXT_DOMAIN ),
				]
			);

			$this->add_control(
				'max_guests',
				[
					'label'   => esc_html__( 'Max Guests', RHEA_TEXT_DOMAIN ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'min'     => 1,
					'max'     => 100,
					'step'    => 1,
					'default' => 10,
				]
			);

			$this->add_control(
				'guests_placeholder',
				[
					'label'   => esc_html__( 'Guests Placeholder', RHEA_TEXT_DOMAIN ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Guests', RHEA_TEXT_DOMAIN ),
				]
			);

			$this->add_responsive_control(
				'guests_field_size',
				[
					'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
					'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
					'type'        => \Elementor\Controls_Manager::SLIDER,
					'size_units'  => [ '%', 'px' ],
					'range'       => [
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
					],
					'selectors'   => [
						'{{WRAPPER}} .rhea_rvr_guests_field' => 'width: {{SIZE}}{{UNIT}} !important;',
					],
				]
			);

			$this->add_responsive_control(
				'guests_separator_color',
				[
					'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .rhea-ultra-field-separator.rhea_rvr_guests_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
					]
				]
			);

			$this->end_controls_section();
		}

		$this->start_controls_section(
			'rhea_property_status_section',
			[
				'label' => esc_html__( 'Property Status', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'property_status_label',
			[
				'label'   => esc_html__( 'Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Property Status', RHEA_TEXT_DOMAIN ),
			]
		);


		$this->add_control(
			'property_status_placeholder',
			[
				'label'   => esc_html__( 'Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'All Status', RHEA_TEXT_DOMAIN ),
			]
		);

		$rhea_all_statuses = get_terms( array( 'taxonomy' => 'property-status', 'hide_empty' => false, ) );
		$status_options    = array();
		$status_default    = array();
		if ( ! empty( $rhea_all_statuses ) && ! is_wp_error( $rhea_all_statuses ) ) {
			foreach ( $rhea_all_statuses as $status ) {
				$status_options[ $status->term_id ] = $status->name;
				$status_default[ $status->slug ]    = $status->name;
			}
		}

		$rhea_all_types = get_terms( array( 'taxonomy' => 'property-type', 'hide_empty' => false, ) );
		$types_default  = array();
		$exclude_types  = array();
		if ( ! empty( $rhea_all_types ) && ! is_wp_error( $rhea_all_types ) ) {
			foreach ( $rhea_all_types as $types ) {
				$types_default[ $types->slug ]    = $types->name;
				$exclude_types[ $types->term_id ] = $types->name;

			}
		}


		$this->add_control(
			'status_count_placeholder',
			[
				'label'       => esc_html__( 'Count Placeholder', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Placeholder text when more than 2 values are being selected', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Status Selected', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'show_status_in_tabs',
			[
				'label'        => esc_html__( 'Show Property Status In Tabs', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'status_tabs_display_location',
			[
				'label'       => esc_html__( 'Tabs Display Location', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => false,
				'label_block' => true,
				'options'     => array(
					'top'    => esc_html__( 'At Top', RHEA_TEXT_DOMAIN ),
					'fields' => esc_html__( 'In Fields', RHEA_TEXT_DOMAIN ),
				),
				'condition'   => [
					'show_status_in_tabs' => 'yes',
				],
				'default'     => 'top',
			]
		);

		$this->add_control(
			'show_all_tab_status',
			[
				'label'        => esc_html__( 'Show All Statuses Tab', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'show_status_in_tabs' => 'yes',
				],
			]
		);


		$this->add_control(
			'default_status_select',
			[
				'label'       => esc_html__( 'Default Selected Status', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => false,
				'label_block' => true,
				'options'     => $status_default,
			]
		);

		$this->add_control(
			'status_in_tabs',
			[
				'label'       => esc_html__( 'Select Status To Show In Tabs', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $status_default,
				'condition'   => [
					'show_status_in_tabs' => 'yes',
				],
			]
		);

		$this->add_control(
			'rhea_select_exclude_status',
			[
				'label'       => esc_html__( 'Exclude Status', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $status_options,
				'condition'   => [
					'show_status_in_tabs' => '',
				],
			]
		);

		$this->add_responsive_control(
			'property_status_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_status_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'status_separator_color',
			[
				'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_status_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'rhea_property_type_section',
			[
				'label' => esc_html__( 'Property Type', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'property_types_label',
			[
				'label'   => esc_html__( 'Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Property Types', RHEA_TEXT_DOMAIN ),
			]
		);


		$this->add_control(
			'property_types_placeholder',
			[
				'label'   => esc_html__( 'Placeholder / All Tab', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'All Types', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'types_count_placeholder',
			[
				'label'       => esc_html__( 'Count Placeholder', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Placeholder text when more than 2 values are being selected', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Types Selected', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'show_types_in_tabs',
			[
				'label'        => esc_html__( 'Show Property Types In Tabs', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'show_all_tab',
			[
				'label'        => esc_html__( 'Show All Types Tab', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'show_types_in_tabs' => 'yes',
				],
			]
		);


		$this->add_control(
			'types_in_tabs',
			[
				'label'       => esc_html__( 'Select Types To Show In Tabs', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $types_default,
				'condition'   => [
					'show_types_in_tabs' => 'yes',
				],
			]
		);

		$this->add_control(
			'default_types_select',
			[
				'label'       => esc_html__( 'Default Selected Types', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => false,
				'label_block' => true,
				'options'     => $types_default,
			]
		);

		$this->add_control(
			'select_excluded_types',
			[
				'label'       => esc_html__( 'Exclude Selected Types', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $exclude_types,
				'condition'   => [
					'show_types_in_tabs' => '',
				],
			]
		);

		$this->add_control(
			'show_types_select_all',
			[
				'label'        => esc_html__( 'Show Select/Deselect All?', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'set_multiple_types',
			[
				'label'        => esc_html__( 'Enable Multi Select ?', RHEA_TEXT_DOMAIN ),
				'description'  => esc_html__( 'This will enable multi select.', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_responsive_control(
			'property_types_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_types_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'type_separator_color',
			[
				'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_types_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_property_min_beds_section',
			[
				'label' => esc_html__( 'Bedrooms', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


		$this->add_control(
			'min_bed_label',
			[
				'label'   => esc_html__( 'Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Bedroom', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'min_bed_placeholder',
			[
				'label'   => esc_html__( 'Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'All Beds', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'min_bed_drop_down_value',
			[
				'label'       => esc_html__( 'Values In Drop Down', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Only provide comma separated numbers. Do not add decimal points, dashes and spaces.', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( '1,2,3,4,5,6,7,8,9,10', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_responsive_control(
			'min_bed_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_min_beds_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'beds_separator_color',
			[
				'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_min_beds_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'rhea_property_min_baths_section',
			[
				'label' => esc_html__( 'Bathrooms', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'min_bath_label',
			[
				'label'   => esc_html__( 'Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Bathroom', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'min_bath_placeholder',
			[
				'label'   => esc_html__( 'Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'All Baths', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'min_bath_drop_down_value',
			[
				'label'       => esc_html__( 'Values In Drop Down', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Only provide comma separated numbers. Do not add decimal points, dashes and spaces.', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( '1,2,3,4,5,6,7,8,9,10', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_responsive_control(
			'min_bath_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_min_baths_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'baths_separator_color',
			[
				'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_min_baths_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_property_min_max_price_section',
			[
				'label' => esc_html__( 'Min Max Price', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


		$this->add_control(
			'show_select_fields',
			[
				'label'        => esc_html__( 'Switch To Select Fields', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'slider_field_label',
			[
				'label'     => esc_html__( 'Field Label', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Price', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_control(
			'slider_range_label',
			[
				'label'     => esc_html__( 'Price Range Label', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Price Range', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_control(
			'slider_range_from',
			[
				'label'     => esc_html__( 'Price From Label', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'From', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);
		$this->add_control(
			'slider_range_to',
			[
				'label'     => esc_html__( 'Price To Label', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'To', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_control(
			'slider_min_price',
			[
				'label'     => esc_html__( 'Minimum Price', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_control(
			'slider_max_price',
			[
				'label'     => esc_html__( 'Maximum Price', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_responsive_control(
			'slider_price_field_size',
			[
				'label'           => esc_html__( 'Price Slider Width (%)', RHEA_TEXT_DOMAIN ),
				'type'            => \Elementor\Controls_Manager::SLIDER,
				'condition'       => [
					'show_select_fields' => '',
				],
				'size_units'      => [ '%', 'px' ],
				'range'           => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'desktop_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors'       => [
					'{{WRAPPER}} .rhea_price_slider_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'slider_price_field_height',
			[
				'label'     => esc_html__( 'Price Slider Height (px)', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'condition' => [
					'show_select_fields' => '',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .rhea_price_slider_wrapper' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'rhea_price_slider_padding',
			[
				'label'      => esc_html__( 'Price Slider Padding', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_price_slider_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_responsive_control(
			'price_range_slider_step',
			[
				'label'     => esc_html__( 'Slider Range Step', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 100,
				'condition' => [
					'show_select_fields' => '',
				]
			]
		);

		$this->add_control(
			'price_range_on_top',
			[
				'label'        => esc_html__( 'Display Price Range On Top?', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'show_select_fields' => '',
				],
			]
		);
		$this->add_responsive_control(
			'rhea_price_range_padding',
			[
				'label'      => esc_html__( 'Price Range Text Padding', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .rhea_price_range' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'show_select_fields' => '',
				],
			]
		);

		$this->add_control(
			'min_price_label',
			[
				'label'     => esc_html__( 'Min Price Label', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Min Price', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'show_select_fields' => 'yes',
				],
			]
		);

		$this->add_control(
			'min_price_placeholder',
			[
				'label'     => esc_html__( 'Min Price Placeholder', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Min Price', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'show_select_fields' => 'yes',
				],
			]
		);


		$this->add_responsive_control(
			'min_price_field_size',
			[
				'label'       => esc_html__( 'Min Price Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'condition'   => [
					'show_select_fields' => 'yes',
				],
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_min_price_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'max_price_label',
			[
				'label'     => esc_html__( 'Max Price Label', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Max Price', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'show_select_fields' => 'yes',
				],
			]
		);

		$this->add_control(
			'max_price_placeholder',
			[
				'label'     => esc_html__( 'Max Price Placeholder', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Max Price', RHEA_TEXT_DOMAIN ),
				'condition' => [
					'show_select_fields' => 'yes',
				],
			]
		);


		$this->add_responsive_control(
			'max_price_field_size',
			[
				'label'       => esc_html__( 'Max Price Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'condition'   => [
					'show_select_fields' => 'yes',
				],
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_max_price_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'min_price_drop_down_value',
			[
				'label'       => esc_html__( 'Min Price List', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Only provide comma separated numbers. Do not add decimal points, dashes, spaces and currency signs.', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( '1000,5000,10000,50000,100000,200000,300000,400000,500000,600000,700000,800000,900000,1000000,1500000,2000000,2500000,5000000', RHEA_TEXT_DOMAIN ),
				'condition'   => [
					'show_select_fields' => 'yes',
				],
			]
		);

		$this->add_control(
			'max_price_drop_down_value',
			[
				'label'       => esc_html__( 'Max Price List', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Only provide comma separated numbers. Do not add decimal points, dashes, spaces and currency signs.', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( '5000,10000,50000,100000,200000,300000,400000,500000,600000,700000,800000,900000,1000000,1500000,2000000,2500000,5000000,10000000', RHEA_TEXT_DOMAIN ),
				'condition'   => [
					'show_select_fields' => 'yes',
				],
			]
		);

		$this->add_control(
			'min_rent_price_drop_down_value',
			[
				'label'       => esc_html__( 'Min Price List For Rent', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Only provide comma separated numbers. Do not add decimal points, dashes, spaces and currency signs.', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( '500,1000,2000,3000,4000,5000,7500,10000,15000,20000,25000,30000,40000,50000,75000,100000', RHEA_TEXT_DOMAIN ),
				'condition'   => [
					'show_select_fields' => 'yes',
				],
			]
		);

		$this->add_control(
			'max_rent_price_drop_down_value',
			[
				'label'       => esc_html__( 'Max Price List For Rent', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Only provide comma separated numbers. Do not add decimal points, dashes, spaces and currency signs.', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( '1000,2000,3000,4000,5000,7500,10000,15000,20000,25000,30000,40000,50000,75000,100000,150000', RHEA_TEXT_DOMAIN ),
				'condition'   => [
					'show_select_fields' => 'yes',
				],
			]
		);

		$status_list = array();
		if ( class_exists( 'ERE_Data' ) ) {
			$status_list = ERE_Data::get_statuses_slug_name();
		};

		$this->add_control(
			'rhea_select_status_for_rent',
			[
				'label'              => esc_html__( 'Status That Represents Rent', RHEA_TEXT_DOMAIN ),
				'description'        => esc_html__( 'Visitor expects smaller values for rent prices. So provide the list of minimum and maximum rent prices below. The rent prices will be displayed based on rent status selected here.', RHEA_TEXT_DOMAIN ),
				'type'               => \Elementor\Controls_Manager::SELECT2,
				'default'            => 'for-rent',
				'label_block'        => true,
				'frontend_available' => true,
				'options'            => $status_list,
				'condition'          => [
					'show_select_fields' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'min_price_separator_color',
			[
				'label'     => esc_html__( 'Min Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_min_price_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'max_price_separator_color',
			[
				'label'     => esc_html__( 'Max Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_max_price_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
			]
		);


		$this->end_controls_section();
		$this->start_controls_section(
			'rhea_property_garages_section',
			[
				'label' => esc_html__( 'Garages', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


		$this->add_control(
			'garages_label',
			[
				'label'   => esc_html__( 'Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Garages', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'garages_placeholder',
			[
				'label'   => esc_html__( 'Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'All Garages', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'garages_drop_down_value',
			[
				'label'       => esc_html__( 'Values In Drop Down', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Only provide comma separated numbers. Do not add decimal points, dashes and spaces.', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( '1,2,3,4,5,6,7,8,9,10', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_responsive_control(
			'garages_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_garages_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'garages_separator_color',
			[
				'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_garages_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_property_agent_section',
			[
				'label' => esc_html__( 'Agents', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


		$this->add_control(
			'agent_label',
			[
				'label'   => esc_html__( 'Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Agent', RHEA_TEXT_DOMAIN ),
			]
		);
		$this->add_control(
			'agent_placeholder',
			[
				'label'   => esc_html__( 'Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'All Agents', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'agent_count_placeholder',
			[
				'label'       => esc_html__( 'Count Placeholder', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Placeholder text when more than 2 values are being selected', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Agents Selected', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'show_select_all',
			[
				'label'        => esc_html__( 'Show Select/Deselect All?', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'set_multiple_agents',
			[
				'label'        => esc_html__( 'Enable Multi Select ?', RHEA_TEXT_DOMAIN ),
				'description'  => esc_html__( 'This will enable multi select.', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);


		$this->add_responsive_control(
			'agent_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_agent_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		$this->add_responsive_control(
			'agents_separator_color',
			[
				'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_agent_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_property_agency_section',
			[
				'label' => esc_html__( 'Agencies', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'agency_label',
			[
				'label'   => esc_html__( 'Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Agency', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'agency_placeholder',
			[
				'label'   => esc_html__( 'Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'All Agencies', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'agency_count_placeholder',
			[
				'label'       => esc_html__( 'Count Placeholder', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Placeholder text when more than 2 values are being selected', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Agencies Selected', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'show_select_all_for_agency',
			[
				'label'        => esc_html__( 'Show Select/Deselect All?', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'set_multiple_agencies',
			[
				'label'        => esc_html__( 'Enable Multi Select?', RHEA_TEXT_DOMAIN ),
				'description'  => esc_html__( 'This will enable multi select.', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_responsive_control(
			'agency_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_agency_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'agency_separator_color',
			[
				'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_agency_field .rhea_prop_search__selectwrap:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'rhea_property_min_area_section',
			[
				'label' => esc_html__( 'Min Max Area', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'min_area_label',
			[
				'label'   => esc_html__( 'Min Area Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Min Area', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'min_area_placeholder',
			[
				'label'   => esc_html__( 'Min Area Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Min Area', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_responsive_control(
			'min_area_field_size',
			[
				'label'       => esc_html__( 'Min Area Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_min_area_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'max_area_label',
			[
				'label'   => esc_html__( 'Max Area Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Max Area', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'max_area_placeholder',
			[
				'label'   => esc_html__( 'Max Area Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Max Area', RHEA_TEXT_DOMAIN ),
			]
		);


		$this->add_responsive_control(
			'max_area_field_size',
			[
				'label'       => esc_html__( 'Max Area Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_max_area_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);


		$this->add_control(
			'area_units_placeholder',
			[
				'label'   => esc_html__( 'Label Units', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '(sq ft)', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'area_units_title_attr',
			[
				'label'   => esc_html__( 'Title Attribute', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Only provide digits!', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_responsive_control(
			'min_area_separator_color',
			[
				'label'     => esc_html__( 'Min Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_min_area_field .rhea-text-field-wrapper:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'max_area_separator_color',
			[
				'label'     => esc_html__( 'Max Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_max_area_field .rhea-text-field-wrapper:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_property_min_lot_size_section',
			[
				'label' => esc_html__( 'Min Max Lot Size', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'min_lot_size_label',
			[
				'label'   => esc_html__( 'Min Lot Size Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Min Lot Size', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'min_lot_size_placeholder',
			[
				'label'   => esc_html__( 'Min Lot Size Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Min Lot Size', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_responsive_control(
			'min_lot_size_field_size',
			[
				'label'       => esc_html__( 'Min Lot Size Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_min_lot_size_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'max_lot_size_label',
			[
				'label'   => esc_html__( 'Max Lot Size Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Max Lot Size', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'max_lot_size_placeholder',
			[
				'label'   => esc_html__( 'Max Lot Size Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Max Lot Size', RHEA_TEXT_DOMAIN ),
			]
		);


		$this->add_responsive_control(
			'max_lot_size_field_size',
			[
				'label'       => esc_html__( 'Max Lot Size Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_max_lot_size_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'lot_size_units_placeholder',
			[
				'label'   => esc_html__( 'Label Units', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( '(sq ft)', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'lot_size_units_title_attr',
			[
				'label'   => esc_html__( 'Title Attribute', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Only provide digits!', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_responsive_control(
			'min_lot_separator_color',
			[
				'label'     => esc_html__( 'Min Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_min_lot_size_field .rhea-text-field-wrapper:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'max_lot_separator_color',
			[
				'label'     => esc_html__( 'Max Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_max_lot_size_field .rhea-text-field-wrapper:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_property_keyword_section',
			[
				'label' => esc_html__( 'Keyword', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'keyword_label',
			[
				'label'   => esc_html__( 'Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Keyword', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_control(
			'keyword_placeholder',
			[
				'label'   => esc_html__( 'Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Keyword', RHEA_TEXT_DOMAIN ),
			]
		);
		$this->add_responsive_control(
			'keyword_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_keyword_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		$this->add_responsive_control(
			'keyword_separator_color',
			[
				'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_keyword_field .rhea-text-field-wrapper:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_keyword_icon',
			[
				'label'        => esc_html__( 'Show Icon', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_responsive_control(
			'keyword_field_icon_size',
			[
				'label'      => esc_html__( 'Icon Size (%)', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rhea-text-field-wrapper .rhea-icon-search' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'  => [
					'show_keyword_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'keyword_field_icon_color',
			[
				'label'     => esc_html__( 'Field Icon Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-icon-search .icon-search' => 'stroke: {{VALUE}}',
				],
				'condition' => [
					'show_keyword_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'keyword_field_icon_focus_color',
			[
				'label'     => esc_html__( 'Field Icon Focus Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_keyword_field:focus-within label .icon-search' => 'stroke: {{VALUE}}',
				],
				'condition' => [
					'show_keyword_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'enable_ajax_search',
			[
				'label'        => esc_html__( 'Enable Live Search?', RHEA_TEXT_DOMAIN ),
				'description'  => esc_html__( 'Dropdown list of properties matched with keywords', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_responsive_control(
			'keyword_field_dropdown_size',
			[
				'label'      => esc_html__( 'Dropdown Size', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 200,
					],
					'px' => [
						'min' => 0,
						'max' => 1500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rhea-properties-data-list' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'  => [
					'enable_ajax_search' => 'yes',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_property_property_id_section',
			[
				'label' => esc_html__( 'Property ID', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


		$this->add_control(
			'property_id_label',
			[
				'label'   => esc_html__( 'Label', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Property ID', RHEA_TEXT_DOMAIN ),
			]
		);


		$this->add_control(
			'property_id_placeholder',
			[
				'label'   => esc_html__( 'Placeholder', RHEA_TEXT_DOMAIN ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Property ID', RHEA_TEXT_DOMAIN ),
			]
		);

		$this->add_responsive_control(
			'property_id_field_size',
			[
				'label'       => esc_html__( 'Field Size (%)', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Width can be set globally for Collapsed/Advance fields from "Style > Collapsed Fields Width"', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ '%', 'px' ],
				'range'       => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .rhea_property_id_field' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'prop_id_separator_color',
			[
				'label'     => esc_html__( 'Field Separator Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-ultra-field-separator.rhea_property_id_field .rhea-text-field-wrapper:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_tag_icon',
			[
				'label'        => esc_html__( 'Show Icon', RHEA_TEXT_DOMAIN ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', RHEA_TEXT_DOMAIN ),
				'label_off'    => esc_html__( 'No', RHEA_TEXT_DOMAIN ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_responsive_control(
			'id_field_icon_size',
			[
				'label'      => esc_html__( 'Icon Size (%)', RHEA_TEXT_DOMAIN ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rhea-text-field-wrapper .feather-tag' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'  => [
					'show_keyword_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'id_field_icon_color',
			[
				'label'     => esc_html__( 'Field Icon Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea-text-field-wrapper .feather-tag' => 'stroke: {{VALUE}}',
				],
				'condition' => [
					'show_keyword_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'id_field_icon_focus_color',
			[
				'label'     => esc_html__( 'Field Icon Focus Color', RHEA_TEXT_DOMAIN ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rhea_property_id_field:focus-within label .feather-tag' => 'stroke: {{VALUE}}',
				],
				'condition' => [
					'show_keyword_icon' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rhea_property_additional_fields',
			[
				'label' => esc_html__( 'Additional Fields', RHEA_TEXT_DOMAIN ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'rhea-important-note-control',
			[
				'label' => esc_html__( 'Note: You can add additional fields from Admin > Easy Real Estate > New Fields Builder ', RHEA_TEXT_DOMAIN ),
				'type'  => 'rhea-important-note',
			]
		);

		$this->add_control(
			'rhea_select_addition_placeholder',
			[
				'label'       => esc_html__( 'Select to display in placeholder', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'This is the first default item in select drop down list.', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'default'     => 'name',
				'label_block' => true,
				'options'     => array(
					'name' => esc_html__( 'Field Name', RHEA_TEXT_DOMAIN ),
					'any'  => esc_html__( 'Any Text', RHEA_TEXT_DOMAIN ),
				),
			]
		);


		$this->add_control(
			'additional_field_any_text',
			[
				'label'       => esc_html__( 'Any Text PlaceHolder', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Placeholder will appear if labels are enabled from Basic Settings > Show Labels ', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Any', RHEA_TEXT_DOMAIN ),
			]
		);


		$this->add_control(
			'prefix_placeholder',
			[
				'label'       => esc_html__( 'Placeholder Prefix', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Prefix for Select fields', RHEA_TEXT_DOMAIN ),
				'default'     => esc_html__( 'All', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'condition'   => [
					'rhea_select_addition_placeholder' => 'name',
				],
			]
		);
		$this->add_control(
			'postfix_placeholder',
			[
				'label'       => esc_html__( 'Placeholder Postfix', RHEA_TEXT_DOMAIN ),
				'description' => esc_html__( 'Postfix for Select fields', RHEA_TEXT_DOMAIN ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'condition'   => [
					'rhea_select_addition_placeholder' => 'name',
				],
			]
		);

		$this->end_controls_section();
	}

	public function query_parameter_locations() {
		$parameter_locations = array();

		if ( function_exists( 'inspiry_get_location_select_names' ) ) {
			$location_names = inspiry_get_location_select_names();
			if ( 0 < count( $location_names ) ) {
				foreach ( $location_names as $location ) {
					if ( isset( $_GET[ $location ] ) ) {
						$parameter_locations[ $location ] = $_GET[ $location ];
					}
				}
			}
		}

		return $parameter_locations;
	}

	protected function render() {
		// ERE_Data class is needed for operations below
		if ( ! class_exists( 'ERE_Data' ) ) {
			return;
		}

		global $settings, $the_widget_id, $search_fields_to_display;

		$sidebar_widget_id   = '';
		$settings            = $this->get_settings_for_display();
		$currency_sign_space = get_option( 'ere_currency_sign_space', 'none' );

		if ( ! empty( $settings['rhea_search_select_sort'] ) ) {
			$search_fields_to_display = $settings['rhea_search_select_sort'];

			if ( is_string( $search_fields_to_display ) ) {
				$search_fields_to_display = explode( ',', $search_fields_to_display );
			}
		}

		$rhea_top_field_count = $settings['rhea_top_field_count'];
		$sidebar_widget_id    = apply_filters( 'rh_sidebar_widget_id', $sidebar_widget_id );
		$the_widget_id        = $this->get_id() . $sidebar_widget_id;
		$slider_range_step    = ! empty( $settings['price_range_slider_step'] ) ? $settings['price_range_slider_step'] : 100;
		?>
        <div class="rhea_ultra_search_form_wrapper rhea-search-form-1" id="rhea-<?php echo esc_attr( $the_widget_id ); ?>">
            <form class="rhea_search_form advance-search-form" action="<?php echo esc_url( rhea_get_search_page_url( $settings['rhea_select_search_template'] ) ); ?>" method="get">
				<?php
				$tabs_enable_class   = '';
				$show_status_in_tabs = $settings['show_status_in_tabs'];
				if ( ( 'yes' === $show_status_in_tabs && 'top' === $settings['status_tabs_display_location'] ) ||
					'yes' === $settings['show_types_in_tabs'] ) {
					rhea_get_template_part( 'elementor/widgets/search/fields/top-tabs-ultra' );
					$tabs_enable_class = ' rhea-form-tabs-enable ';
				}
				?>
                <div class="rhea-ultra-search-form-fields <?php echo 'yes' === $settings['show_search_buttons_at_top'] ? esc_attr( 'rhea-ultra-search-buttons-top' ) : '' ?>">
                    <div class="rhea-ultra-search-form-inner">
                        <div class="rhea_top_search_fields">
                            <div class="rhea_top_search_box <?php echo esc_attr( 'rhea_top_fields_count_' . $rhea_top_field_count ) ?>" id="top-<?php echo esc_attr( $the_widget_id ); ?>">
								<?php
								if ( 'yes' === $settings['set_ajax_location'] ) {
									rhea_get_template_part( 'elementor/widgets/search/fields/location-ajax' );
								} else {
									rhea_get_template_part( 'elementor/widgets/search/fields/location' );
								}

								if ( $this->is_rvr_enabled ) {
									rhea_get_template_part( 'elementor/widgets/search/fields/rvr/check-in-out' );
									rhea_get_template_part( 'elementor/widgets/search/fields/rvr/guest' );
								}

								if ( 'yes' !== $show_status_in_tabs ) {
									rhea_get_template_part( 'elementor/widgets/search/fields/status' );
								} else if ( 'fields' === $settings['status_tabs_display_location'] ) {
									rhea_get_template_part( 'elementor/widgets/search/fields/status-tabs' );
								}

								if ( 'yes' !== $settings['show_types_in_tabs'] ) {
									rhea_get_template_part( 'elementor/widgets/search/fields/type' );
								}

								if ( 'yes' == $settings['show_select_fields'] ) {
									rhea_get_template_part( 'elementor/widgets/search/fields/min-max-price' );
								} else {
									rhea_get_template_part( 'elementor/widgets/search/fields/price-slider' );
								}

								rhea_get_template_part( 'elementor/widgets/search/fields/min-beds' );
								rhea_get_template_part( 'elementor/widgets/search/fields/min-baths' );
								rhea_get_template_part( 'elementor/widgets/search/fields/min-garages' );
								rhea_get_template_part( 'elementor/widgets/search/fields/agent' );
								rhea_get_template_part( 'elementor/widgets/search/fields/agency' );
								rhea_get_template_part( 'elementor/widgets/search/fields/min-max-area' );
								rhea_get_template_part( 'elementor/widgets/search/fields/min-max-lot-size' );
								rhea_get_template_part( 'elementor/widgets/search/fields/ultra-keyword-search' );
								rhea_get_template_part( 'elementor/widgets/search/fields/ultra-property-id' );

								$additional_fields = rhea_get_additional_search_fields();
								if ( ! empty( $additional_fields ) ) {
									foreach ( $additional_fields as $additional_field ) {
										$field_name = $additional_field['field_name'];
										$field_key  = $additional_field['field_key'];
										$is_multiselect = ! empty( $additional_field['multi_select'] ) && 'yes' === $additional_field['multi_select'] ? true : false;

										if ( isset( $additional_field['field_options'] ) ) {
											$options = $additional_field['field_options'];

											if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
												$options = explode( ',', apply_filters( 'wpml_translate_single_string', implode( ',', $options ), 'Additional Fields', $field_name . ' Value', ICL_LANGUAGE_CODE ) );
												$options = array_filter( array_map( 'trim', $options ) );
												$options = array_combine( $options, $options );
											}
										}

										if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
											$field_name = apply_filters( 'wpml_translate_single_string', $field_name, 'Additional Fields', $field_name . ' Label', ICL_LANGUAGE_CODE );
										}

										if ( in_array( $field_key, $search_fields_to_display ) ) {
											$field_position = intval( array_search( $field_key, $search_fields_to_display ) ) + 1;

											if ( 'name' === $settings['rhea_select_addition_placeholder'] ) {
												$set_placeholder = $field_name;
											} else {
												$set_placeholder = $settings['additional_field_any_text'];
											}

											$field_id = $field_key . $the_widget_id;
											if ( in_array( $additional_field['field_type'], array( 'text', 'textarea' ) ) ) {
												?>
                                                <div class="rhea_prop_search__option rhea_mod_text_field" data-key-position="<?php echo esc_attr( $field_position ); ?>" style="order: <?php echo esc_attr( $field_position ); ?>">
													<?php
													if ( 'yes' === $settings['show_labels'] ) {
														?>
                                                        <label class="rhea_fields_labels" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field_name ); ?></label>
														<?php
													}
													?>
                                                    <span class="rhea-text-field-wrapper">
                                                        <input type="text" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo isset( $_GET[ $field_key ] ) ? esc_attr( $_GET[ $field_key ] ) : ''; ?>" placeholder="<?php echo esc_attr( $set_placeholder ); ?>" />
                                                    </span>
                                                </div>
												<?php
											} else {
												?>
                                                <div class="rhea_prop_search__option rhea_prop_search__select" data-key-position="<?php echo esc_attr( $field_position ); ?>" style="order: <?php echo esc_attr( $field_position ); ?>">
													<?php
													if ( 'yes' === $settings['show_labels'] ) {
														?>
                                                        <label class="rhea_fields_labels" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field_name ); ?></label>
														<?php
													}
													?>
                                                    <span class="rhea_prop_search__selectwrap">
                                                        <select title="<?php echo esc_html( $field_name ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_key . '[]' ); ?>" class="rhea_multi_select_picker selectpicker show-tick"
                                                            <?php echo $is_multiselect ? 'multiple' : ''; ?>>
                                                            <?php
                                                            // Display default select option
                                                            if ( ! $is_multiselect ) {
	                                                            $selected = empty( $_GET[ $field_key ] ) ? 'selected="selected"' : '';
	                                                            echo '<option value="' . rhea_any_value() . '" ' . $selected . '>' . esc_html( $settings['prefix_placeholder'] . ' ' . $set_placeholder . ' ' . $settings['postfix_placeholder'] ) . '</option>';
                                                            }
                                                            // Display all available select options
                                                            if ( ! empty( $options ) && is_array( $options ) ) {
	                                                            foreach ( $options as $option ) {
		                                                            $selected = ( ! empty( $_GET[ $field_key ] ) && in_array( $option, $_GET[ $field_key ] ) ) ? 'selected="selected"' : '';
		                                                            echo '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $option ) . '</option>';
	                                                            }
                                                            }
                                                            ?>
                                                        </select>
                                                    </span>
                                                </div>
												<?php
											}
										}
									}
								}

								if ( function_exists( 'inspiry_get_maps_type' ) && 'google-maps' === inspiry_get_maps_type() ) {
									rhea_get_template_part( 'elementor/widgets/search/fields/rvr/geolocation-slider' );
								}
								?>
                            </div>
							<?php
							if ( 'yes' === $settings['show_search_buttons_at_top'] ) {
								rhea_get_template_part( 'elementor/widgets/search/fields/search-buttons-ultra' );
							}
							?>
                        </div>
                        <div class="rhea_collapsed_search_fields  rhea_advance_fields_<?php echo esc_attr( $settings['rhea_default_advance_state'] ) ?>" id="collapsed_wrapper_<?php echo esc_attr( $the_widget_id ); ?>">
                            <div class="rhea_collapsed_search_fields_inner" id="collapsed-<?php echo esc_attr( $the_widget_id ); ?>"></div>
                        </div>
						<?php rhea_get_template_part( 'elementor/widgets/search/fields/search-buttons-ultra' ); ?>
                    </div>
					<?php rhea_get_template_part( 'elementor/widgets/search/fields/property-features' ); ?>
                </div>
            </form>
        </div>
		<?php
		if ( function_exists( 'realhomes_currency_switcher_enabled' ) && realhomes_currency_switcher_enabled() ) {
			$get_currencies_data  = realhomes_get_currencies_data();
			$get_current_currency = realhomes_get_current_currency();
			$get_position         = ( $get_currencies_data[ $get_current_currency ]['position'] );
			$get_separator        = $get_currencies_data[ $get_current_currency ]['thousands_sep'];
			$get_symbol           = html_entity_decode( $get_currencies_data[ $get_current_currency ]['symbol'] );
		} else {
			$get_position  = get_option( 'theme_currency_position', 'before' );
			$get_separator = get_option( 'theme_thousands_sep', ',' );
			$get_symbol    = get_option( 'theme_currency_sign', '$' );
		}

		if ( 'before' === $currency_sign_space ) {
			$get_symbol = ' ' . $get_symbol;
		} else if ( 'after' === $currency_sign_space ) {
			$get_symbol = $get_symbol . ' ';
		}

		// check hide empty value based on setting
		$hide_empty = false;
		if ( 'yes' === $settings['hide_empty_location'] ) {
			$hide_empty = true;
		}

		// get hierarchical_locations
		$hierarchical_locations    = ERE_Data::get_hierarchical_locations( $hide_empty );
		$location_select_ids       = rhea_get_location_select_ids( $the_widget_id );
		$select_count              = rhea_get_locations_number( $settings['rhea_select_locations'], 'no' );
		$locations_place_holders   = rhea_location_placeholder(
			$settings['rhea_location_ph_1'],
			$settings['rhea_location_ph_2'],
			$settings['rhea_location_ph_3'],
			$settings['rhea_location_ph_4']
		);
		$query_parameter_locations = $this->query_parameter_locations();
		$any_value                 = rhea_any_value();

		$multi_select = '';
		if ( '1' == $settings['rhea_select_locations'] && 'yes' == $settings['set_multiple_location'] ) {
			$multi_select = 'multiple';
		}

		// Setting Minimum Price Slider Value (Queried/Searched)
		$rhea_searched_price_min = '';
		if ( isset( $_GET['min-price'] ) ) {
			$rhea_searched_price_min = $_GET['min-price'];
		}

		$rhea_searched_price_max = '';
		if ( isset( $_GET['max-price'] ) ) {
			$rhea_searched_price_max = $_GET['max-price'];
		}

		// Establishing the Minimum Price Slider Value from
		// Widget Settings with a fallback to rhea_get_min_max_price
		$rhea_price_min_value = '';
		if ( ! empty( $settings['slider_min_price'] ) ) {
			$rhea_price_min_value = (int)$settings['slider_min_price'];
		} else {
			$rhea_price_min_value = rhea_get_min_max_price( 'min' );
		}

		$rhea_price_max_value = '';
		if ( ! empty( $settings['slider_max_price'] ) ) {
			$rhea_price_max_value = (int)$settings['slider_max_price'];
		} else {
			$rhea_price_max_value = rhea_get_min_max_price( 'max' );
		}

		// set selectors for Status tabs and default status fields
		if ( 'yes' === $show_status_in_tabs ) {
			$status_id = '.rhea-status-tabs-' . esc_attr( $the_widget_id ) . ' input';
		} else {
			$status_id = '#select-status-' . esc_attr( $the_widget_id );
		}

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
            <script type="application/javascript">
                rheaSearchFields( "#rhea-<?php echo esc_attr( $the_widget_id ); ?>",<?php echo intval( $rhea_top_field_count );?>, "#top-<?php echo esc_attr( $the_widget_id ); ?>", "#collapsed-<?php echo esc_attr( $the_widget_id ); ?>" );
                rheaSearchStatusChange( "#rhea-<?php echo esc_attr( $the_widget_id ); ?> .price-for-others", "#rhea-<?php echo esc_attr( $the_widget_id ); ?> .price-for-rent", "<?php echo esc_attr( $status_id ); ?>", "<?php echo esc_attr( $settings['rhea_select_status_for_rent'] )?>" );
                rheaPropertySlider(
                    "#rhea_slider_<?php echo esc_attr( $the_widget_id )?> .rhea_price_slider",
					<?php echo preg_replace( '/[^0-9]/', '', ( rhea_get_plain_property_price( $rhea_price_min_value ) ) );?>,
					<?php echo preg_replace( '/[^0-9]/', '', ( rhea_get_plain_property_price( $rhea_price_max_value ) ) );?>,
                    "<?php echo $get_position;?>",
                    "<?php echo $get_separator; ?>",
                    "<?php echo $get_symbol; ?>",
                    "<?php echo $rhea_searched_price_min; ?>",
                    "<?php echo $rhea_searched_price_max; ?>",
                    <?php echo $slider_range_step; ?>
                );
                rheaSearchAdvanceState( ".advance_button_<?php echo esc_attr( $the_widget_id ); ?>", "#collapsed_wrapper_<?php echo esc_attr( $the_widget_id ); ?>" );
                rheaSearchAdvanceState( "#advance_bottom_button_<?php echo esc_attr( $the_widget_id ); ?>", "#collapsed_wrapper_<?php echo esc_attr( $the_widget_id ); ?>" );
                rheaFeaturesState( "#rhea_features_<?php echo esc_attr( $the_widget_id );?> .rhea_open_more_features", "#rhea_features_<?php echo esc_attr( $the_widget_id );?> .rhea-more-options-wrapper" );
                rheaLocationsHandler(<?php echo json_encode( $hierarchical_locations )?>,
					<?php echo json_encode( $locations_place_holders )?>,
					<?php echo json_encode( $location_select_ids )?>,
					<?php echo json_encode( $query_parameter_locations )?>,
					<?php echo json_encode( $select_count )?>,
					<?php echo json_encode( $any_value )?>,
					<?php echo json_encode( $multi_select )?>
                );
                rheaSelectPicker( "<?php echo "#rhea-" . $the_widget_id; ?> .rhea_multi_select_picker" );
                rheaSelectPicker( "<?php echo "#rhea-" . $the_widget_id; ?> .rhea_multi_select_picker_location" );
                minMaxPriceValidation( "#select-min-price-<?php echo esc_attr( $the_widget_id ); ?>", "#select-max-price-<?php echo esc_attr( $the_widget_id ); ?>" );
                minMaxRentPriceValidation( "#select-min-price-for-rent-<?php echo esc_attr( $the_widget_id ); ?>", "#select-max-price-for-rent-<?php echo esc_attr( $the_widget_id ); ?>" );
                minMaxAreaValidation( "#min-area-<?php echo esc_attr( $the_widget_id ); ?>", "#max-area-<?php echo esc_attr( $the_widget_id ); ?>" );
                minMaxAreaValidation( "#min-lot-size-<?php echo esc_attr( $the_widget_id ); ?>", "#max-lot-size-<?php echo esc_attr( $the_widget_id ); ?>" );

                jQuery( "<?php echo "#rhea-" . $the_widget_id; ?> .rhea_multi_select_picker_location" )
                .on( 'change', function () {
                    jQuery( "<?php echo "#rhea-" . $the_widget_id; ?> .rhea_multi_select_picker_location" )
                    .selectpicker( 'refresh' );

                } );

                rheaAjaxSelect( ".rhea_location_ajax_parent_<?php echo esc_attr( $the_widget_id )?>",
                    "#rhea_ajax_location_<?php echo $the_widget_id?>",
                    '<?php echo admin_url( 'admin-ajax.php' )?>',
                    '<?php echo $settings['hide_empty_location']?>',
                    '<?php echo $settings['sort_location_alphabetically']?>'
                );
				<?php if ( 'yes' === $settings['enable_ajax_search'] ) { ?>
                searchFormAjaxKeywords( "#keyword-search<?php echo esc_attr( $the_widget_id ); ?>", "<?php echo admin_url( 'admin-ajax.php' ) ?>" );
				<?php } ?>
                jQuery( "#rhea-<?php echo esc_attr( $the_widget_id ); ?>" ).fadeIn();
            </script>
			<?php
		} else {
			?>
            <script type="application/javascript">
                jQuery( document ).bind( "ready", function () {
                    rheaSearchFields( "#rhea-<?php echo esc_attr( $the_widget_id ); ?>",<?php echo intval( $rhea_top_field_count );?>, "#top-<?php echo esc_attr( $the_widget_id ); ?>", "#collapsed-<?php echo esc_attr( $the_widget_id ); ?>" );
                    rheaSearchStatusChange( "#rhea-<?php echo esc_attr( $the_widget_id ); ?> .price-for-others", "#rhea-<?php echo esc_attr( $the_widget_id ); ?> .price-for-rent", "<?php echo esc_attr( $status_id ); ?>", "<?php echo esc_attr( $settings['rhea_select_status_for_rent'] )?>" );
                    rheaPropertySlider(
                        "#rhea_slider_<?php echo esc_attr( $the_widget_id )?> .rhea_price_slider",
						<?php echo preg_replace( '/[^0-9]/', '', ( rhea_get_plain_property_price( $rhea_price_min_value ) ) );?>,
						<?php echo preg_replace( '/[^0-9]/', '', ( rhea_get_plain_property_price( $rhea_price_max_value ) ) );?>,
                        "<?php echo $get_position; ?>",
                        "<?php echo $get_separator; ?>",
                        "<?php echo $get_symbol; ?>",
                        "<?php echo $rhea_searched_price_min; ?>",
                        "<?php echo $rhea_searched_price_max; ?>",
                        <?php echo $slider_range_step; ?>
                    );
                    rheaSearchAdvanceState( ".advance_button_<?php echo esc_attr( $the_widget_id ); ?>", "#collapsed_wrapper_<?php echo esc_attr( $the_widget_id ); ?>" );
                    rheaSearchAdvanceState( "#advance_bottom_button_<?php echo esc_attr( $the_widget_id ); ?>", "#collapsed_wrapper_<?php echo esc_attr( $the_widget_id ); ?>" );
                    rheaFeaturesState( "#rhea_features_<?php echo esc_attr( $the_widget_id );?> .rhea_open_more_features", "#rhea_features_<?php echo esc_attr( $the_widget_id );?> .rhea-more-options-wrapper" );
                    rheaLocationsHandler(<?php echo json_encode( $hierarchical_locations )?>,
						<?php echo json_encode( $locations_place_holders )?>,
						<?php echo json_encode( $location_select_ids )?>,
						<?php echo json_encode( $query_parameter_locations )?>,
						<?php echo json_encode( $select_count )?>,
						<?php echo json_encode( $any_value )?>,
						<?php echo json_encode( $multi_select )?>
                    );
                    rheaSelectPicker( "<?php echo "#rhea-" . $the_widget_id; ?> select.rhea_multi_select_picker" );
                    rheaSelectPicker( "<?php echo "#rhea-" . $the_widget_id; ?> select.rhea_multi_select_picker_location" );
                    minMaxPriceValidation( "#select-min-price-<?php echo esc_attr( $the_widget_id ); ?>", "#select-max-price-<?php echo esc_attr( $the_widget_id ); ?>" );
                    minMaxRentPriceValidation( "#select-min-price-for-rent-<?php echo esc_attr( $the_widget_id ); ?>", "#select-max-price-for-rent-<?php echo esc_attr( $the_widget_id ); ?>" );
                    minMaxAreaValidation( "#min-area-<?php echo esc_attr( $the_widget_id ); ?>", "#max-area-<?php echo esc_attr( $the_widget_id ); ?>" );
                    minMaxAreaValidation( "#min-lot-size-<?php echo esc_attr( $the_widget_id ); ?>", "#max-lot-size-<?php echo esc_attr( $the_widget_id ); ?>" );

                    jQuery( "<?php echo "#rhea-" . $the_widget_id; ?> .rhea_multi_select_picker_location" )
                    .on( 'change', function () {
                        setTimeout( function () {
                            jQuery( "<?php echo "#rhea-" . $the_widget_id; ?> .rhea_multi_select_picker_location" )
                            .selectpicker( 'refresh' );
                        }, 500 );
                    } );

                    rheaAjaxSelect( ".rhea_location_ajax_parent_<?php echo esc_attr( $the_widget_id )?>",
                        "#rhea_ajax_location_<?php echo $the_widget_id?>",
                        '<?php echo admin_url( 'admin-ajax.php' )?>',
                        '<?php echo $settings['hide_empty_location'] == 'yes' ? $settings['hide_empty_location'] : 'no' ?>',
                        '<?php echo $settings['sort_location_alphabetically'] == 'yes' ? $settings['sort_location_alphabetically'] : 'no' ?>'
                    );
					<?php
					if ( 'yes' === $settings['enable_ajax_search'] ) {
					?>
                    searchFormAjaxKeywords( "#keyword-search<?php echo esc_attr( $the_widget_id ); ?>", "<?php echo admin_url( 'admin-ajax.php' ) ?>" );
					<?php
					}
					?>
                    jQuery( "#rhea-<?php echo esc_attr( $the_widget_id ); ?>" ).fadeIn();
					<?php
					if ( wp_is_mobile() ) {
					?>
                    window.onpageshow = function ( event ) {
                        if ( event.persisted ) {
                            window.location.reload();
                        }
                    };
					<?php
					}
					?>
                } );
            </script>
			<?php
		}
	}
}
