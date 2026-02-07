<?php
/**
 * Add agent metabox tab to property
 *
 * @param $property_metabox_tabs
 *
 * @return array
 */
function ere_agent_metabox_tab( $property_metabox_tabs ) {
	if ( is_array( $property_metabox_tabs ) ) {
		$property_metabox_tabs['agent'] = array(
			'label' => esc_html__( 'Agent Information', ERE_TEXT_DOMAIN ),
			'icon'  => 'dashicons-businessman',
		);
	}

	return $property_metabox_tabs;
}

add_filter( 'ere_property_metabox_tabs', 'ere_agent_metabox_tab', 60 );


/**
 * Add agent metaboxes fields to property
 *
 * @param $property_metabox_fields
 *
 * @return array
 */
function ere_agent_metabox_fields( $property_metabox_fields ) {
	$ere_agent_fields           = $options = array();
	$agent_pt_exist             = post_type_exists( 'agent' );
	$columns                    = 12;
	$options['my_profile_info'] = esc_html__( 'Author information.', ERE_TEXT_DOMAIN );

	if ( $agent_pt_exist ) {
		$options['agent_info'] = esc_html__( 'Agent Information. ( Select the agent below )', ERE_TEXT_DOMAIN );
		$columns               = 6;
	}

	$options['none'] = esc_html__( 'None. ( Hide information box )', ERE_TEXT_DOMAIN );

	$ere_agent_fields[] = array(
		'name'    => esc_html__( 'What to display in agent information box ?', ERE_TEXT_DOMAIN ),
		'id'      => "REAL_HOMES_agent_display_option",
		'type'    => 'radio',
		'std'     => 'none',
		'options' => $options,
		'columns' => $columns,
		'tab'     => 'agent',
	);

	if ( $agent_pt_exist ) {
		$ere_agent_fields[] = array(
			'name'     => esc_html__( 'Agents', ERE_TEXT_DOMAIN ),
			'id'       => "REAL_HOMES_agents",
			'type'     => 'select',
			'options'  => ere_get_agents_array(),
			'multiple' => true,
			'columns'  => 6,
			'tab'      => 'agent',
			'std'      => get_option( 'realhomes_default_selected_agent_backend', 'none' )
		);
	}

	return array_merge( $property_metabox_fields, $ere_agent_fields );

}

add_filter( 'ere_property_metabox_fields', 'ere_agent_metabox_fields', 60 );
