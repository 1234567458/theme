<?php
if ( ! function_exists( 'ere_agent_meta_boxes' ) ) :
	/**
	 * Contains agent's meta box declaration
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function ere_agent_meta_boxes( $meta_boxes ) {

		$meta_boxes[] = array(
			'id'         => 'agent-meta-box',
			'title'      => esc_html__( 'Agent Details', ERE_TEXT_DOMAIN ),
			'post_types' => array( 'agent' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'tabs'       => array(
				'agent-basic'   => array(
					'label' => esc_html__( 'Basic', ERE_TEXT_DOMAIN ),
					'icon'  => 'dashicons-admin-home',
				),
				'agent-contact' => array(
					'label' => esc_html__( 'Contact', ERE_TEXT_DOMAIN ),
					'icon'  => 'dashicons-phone',
				),
				'agent-social'  => array(
					'label' => esc_html__( 'Social', ERE_TEXT_DOMAIN ),
					'icon'  => 'dashicons-networking',
				),
			),
			'tab_style'  => 'left',
			'fields'     => array(
				array(
					'name'    => esc_html__( 'Select Agency If Any', ERE_TEXT_DOMAIN ),
					'id'      => 'REAL_HOMES_agency',
					'type'    => 'select',
					'options' => ere_get_agency_array(),
					'columns' => 6,
					'tab'     => 'agent-basic',
				),
				array(
					'name'    => esc_html__( 'Mark as Verified', ERE_TEXT_DOMAIN ),
					'id'      => 'ere_agent_verification_status',
					'type'    => 'radio',
					'std'     => '0',
					'options' => array(
						'1' => esc_html__( 'Yes', ERE_TEXT_DOMAIN ),
						'0' => esc_html__( 'No', ERE_TEXT_DOMAIN ),
					),
					'columns' => 6,
					'tab'     => 'agent-basic',
				),
				array(
					'name'    => esc_html__( 'License Number', ERE_TEXT_DOMAIN ),
					'id'      => 'REAL_HOMES_license_number',
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-basic',
				),
				array(
					'name'    => esc_html__( 'Shortcode to Replace Default Agent Form', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_custom_agent_contact_form",
					'desc'    => esc_html__( "Default agent form can be replaced with custom form using contact form 7 or WPForms plugin shortcode.", ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-basic',
				),
				array(
					'name'    => esc_html__( 'Mobile Number', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_mobile_number",
					'desc'    => esc_html__( 'Enter the mobile number in international format, starting with + and the country code. Example: +14155552671', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-contact',
				),
				array(
					'name'    => esc_html__( 'Office Number', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_office_number",
					'desc'    => esc_html__( 'Enter the office number in international format, starting with + and the country code. Example: +14155552671', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-contact',
				),
				array(
					'name'    => esc_html__( 'WhatsApp Number', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_whatsapp_number",
					'desc'    => esc_html__( 'Enter the WhatsApp number in international format, starting with + and the country code. Example: +14155552671', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-contact',
				),
				array(
					'name'    => esc_html__( 'Fax Number', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_fax_number",
					'desc'    => esc_html__( 'Enter the fax number in international format, starting with + and the country code. Example: +14155552671', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-contact',
				),
				array(
					'name'    => esc_html__( 'Website', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_website",
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-contact',
				),
				array(
					'name'    => esc_html__( 'Email Address', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_agent_email",
					'desc'    => esc_html__( 'Messages from default agent form on property single will be sent to this address.', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-contact',
				),
				array(
					'name'    => esc_html__( 'Address', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_address",
					'type'    => 'textarea',
					'columns' => 12,
					'tab'     => 'agent-contact',
				),
				array(
					'name'    => esc_html__( 'Facebook URL', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_facebook_url",
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-social',
				),
				array(
					'name'    => esc_html__( 'Twitter URL', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_twitter_url",
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-social',
				),
				array(
					'name'    => esc_html__( 'LinkedIn URL', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_linked_in_url",
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-social',
				),
				array(
					'name'    => esc_html__( 'Instagram URL', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_instagram_url',
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-social',
				),
				array(
					'name'    => esc_html__( 'Tiktok URL', ERE_TEXT_DOMAIN ),
					'id'      => 'realhomes_tiktok_url',
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-social',
				),
				array(
					'name'    => esc_html__( 'Pinterest URL', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_pinterest_url',
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-social',
				),
				array(
					'name'    => esc_html__( 'Youtube URL', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_youtube_url',
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agent-social',
				),
			),
		);

		return apply_filters( 'ere_agent_meta_boxes', $meta_boxes );

	}

	add_filter( 'rwmb_meta_boxes', 'ere_agent_meta_boxes' );

endif;


if ( ! function_exists( 'ere_agency_meta_boxes' ) ) :
	/**
	 * Contains agency's meta box declaration
	 *
	 * @param $meta_boxes
	 *
	 * @return array
	 */
	function ere_agency_meta_boxes( $meta_boxes ) {

		$meta_boxes[] = array(
			'id'         => 'agency-meta-box',
			'title'      => esc_html__( 'Provide Related Information', ERE_TEXT_DOMAIN ),
			'post_types' => array( 'agency' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'tabs'       => array(
				'agency-basic'   => array(
					'label' => esc_html__( 'Basic', ERE_TEXT_DOMAIN ),
					'icon'  => 'dashicons-admin-home',
				),
				'agency-contact' => array(
					'label' => esc_html__( 'Contact', ERE_TEXT_DOMAIN ),
					'icon'  => 'dashicons-phone',
				),
				'agency-social'  => array(
					'label' => esc_html__( 'Social', ERE_TEXT_DOMAIN ),
					'icon'  => 'dashicons-networking',
				),
			),
			'tab_style'  => 'left',
			'fields'     => array(
				array(
					'name'    => esc_html__( 'Shortcode to Replace Default Agency Form', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_custom_agency_contact_form",
					'desc'    => esc_html__( "Default agency form can be replaced with custom form using contact form 7 or WPForms plugin shortcode.", ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-basic',
				),
				array(
					'name'    => esc_html__( 'Mark as Verified', ERE_TEXT_DOMAIN ),
					'id'      => 'ere_agency_verification_status',
					'type'    => 'radio',
					'std'     => '0',
					'options' => array(
						'1' => esc_html__( 'Yes', ERE_TEXT_DOMAIN ),
						'0' => esc_html__( 'No', ERE_TEXT_DOMAIN ),
					),
					'columns' => 6,
					'tab'     => 'agency-basic',
				),
				array(
					'name'    => esc_html__( 'Mobile Number', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_mobile_number",
					'desc'    => esc_html__( 'Enter the mobile number in international format, starting with + and the country code. Example: +14155552671', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-contact',
				),
				array(
					'name'    => esc_html__( 'Office Number', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_office_number",
					'desc'    => esc_html__( 'Enter the office number in international format, starting with + and the country code. Example: +14155552671', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-contact',
				),
				array(
					'name'    => esc_html__( 'WhatsApp Number', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_whatsapp_number",
					'desc'    => esc_html__( 'Enter the WhatsApp number in international format, starting with + and the country code. Example: +14155552671', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-contact',
				),
				array(
					'name'    => esc_html__( 'Fax Number', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_fax_number",
					'desc'    => esc_html__( 'Enter the fax number in international format, starting with + and the country code. Example: +14155552671', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-contact',
				),
				array(
					'name'    => esc_html__( 'Website', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_website",
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-contact',
				),
				array(
					'name'    => esc_html__( 'Email Address', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_agency_email",
					'desc'    => esc_html__( 'Messages from default agency form will be sent to this address.', ERE_TEXT_DOMAIN ),
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-contact',
				),
				array(
					'name'    => esc_html__( 'Address', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_address",
					'type'    => 'textarea',
					'columns' => 12,
					'tab'     => 'agency-contact',
				),

				array(
					'name'    => esc_html__( 'Facebook URL', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_facebook_url",
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-social',
				),
				array(
					'name'    => esc_html__( 'Twitter URL', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_twitter_url",
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-social',
				),
				array(
					'name'    => esc_html__( 'LinkedIn URL', ERE_TEXT_DOMAIN ),
					'id'      => "REAL_HOMES_linked_in_url",
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-social',
				),
				array(
					'name'    => esc_html__( 'Instagram URL', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_instagram_url',
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-social',
				),
				array(
					'name'    => esc_html__( 'Pinterest URL', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_pinterest_url',
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-social',
				),
				array(
					'name'    => esc_html__( 'Youtube URL', ERE_TEXT_DOMAIN ),
					'id'      => 'inspiry_youtube_url',
					'type'    => 'text',
					'columns' => 6,
					'tab'     => 'agency-social',
				),
			),
		);

		return apply_filters( 'ere_agency_meta_boxes', $meta_boxes );

	}

	add_filter( 'rwmb_meta_boxes', 'ere_agency_meta_boxes' );

endif;