<?php
/**
 * Agent Search Feature
 *
 * @since   4.0.0
 * @package realhomes/functions
 */

if ( ! function_exists( 'realhomes_agent_locations_options' ) ) {
	/**
	 * Agent hierarchical location options
	 *
	 * @since    4.0.0
	 * @return void
	 */
	function realhomes_agent_locations_options() {
		if ( ! class_exists( 'ERE_Data' ) ) {
			return;
		}

		$hierarchical_terms_array = array();
		$searched_terms           = null;
		$excluded_terms           = null;

		$hierarchical_terms_array = ERE_Data::get_agent_hierarchical_locations();

		if ( isset( $_GET['agent-locations'] ) ) {
			$searched_terms = $_GET['agent-locations'];
		} else if ( ! empty( $_GET['agent-locations'] ) ) {
			$searched_terms = $_GET['agent-locations'];
		}

		realhomes_generate_options( $hierarchical_terms_array, $searched_terms, '', $excluded_terms );

	}
}

if ( ! function_exists( 'realhomes_filter_agents' ) ) {
	/**
	 * Filter Agents based on Search Parameters for Ajax Call
	 *
	 * @since 4.0.0
	 * @return string JSON
	 *
	 */
	function realhomes_filter_agents() {

		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'agent_search_nonce' ) ) {
			wp_send_json_error(
				array(
					'status'  => false,
					'message' => esc_html__( 'Security check failed!', RH_TEXT_DOMAIN )
				)
			);
			wp_die();
		}

		$paged = 1;
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		}

		$number_of_agents = intval( get_option( 'theme_number_posts_agent' ) );
		if ( ! $number_of_agents ) {
			$number_of_agents = 6;
		}

		$search_args = array(
			'post_type'      => 'agent',
			'posts_per_page' => $number_of_agents,
			'paged'          => $paged,
		);

		/* Initialize Taxonomy Query Array */
		$tax_query = array();

		/* Initialize Meta Query Array */
		$meta_query = array();

		/* Keyword Search */
		if ( ! empty( $_POST['name'] ) ) {
			$search_args['s'] = $_POST['name'];
		}

		/* Meta Search Filter */
		$meta_query = apply_filters( 'realhomes_agent_meta_search', $meta_query );

		/* If more than one meta query elements exist then specify the relation */
		$meta_count = count( $meta_query );
		if ( $meta_count > 1 ) {
			$meta_query['relation'] = 'AND';
		}

		/* If meta query has some values then add it to search query */
		if ( $meta_count > 0 ) {
			$search_args['meta_query'] = $meta_query;
		}

		/* Taxonomy Search Filter */
		$tax_query = apply_filters( 'realhomes_agent_taxonomy_search', $tax_query );

		/* If more than one taxonomies exist then specify the relation */
		$tax_count = count( $tax_query );
		if ( $tax_count > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		/* If taxonomy query has some values then add it to search query */
		if ( $tax_count > 0 ) {
			$search_args['tax_query'] = $tax_query;
		}

		/* Sort Agents by Number of Properties */
		$search_args = apply_filters( 'realhomes_filter_agents_by_properties', $search_args );

		$agent_search_query  = new WP_Query( $search_args );
		$agent_search_layout = 'assets/modern/partials/agent/card';

		ob_start();
		if ( $agent_search_query->have_posts() ) {

			while ( $agent_search_query->have_posts() ) {

				$agent_search_query->the_post();

				get_template_part( $agent_search_layout );
				$search_results = ob_get_contents();

			}

			wp_send_json_success(
				array(
					'search_results' => $search_results,
					'status'         => ob_end_clean(),
					'max_pages'      => $agent_search_query->max_num_pages,
					'total_agents'   => $agent_search_query->found_posts,
				)
			);

			wp_reset_postdata();

		} else {

			$search_results = '<div class="rh_agent_card__wrap no-results">';
			$search_results .= '<p><strong>' . esc_html__( 'No Results Found!', RH_TEXT_DOMAIN ) . '</strong></p>';
			$search_results .= '</div>';

			wp_send_json_success(
				array(
					'search_results' => $search_results,
					'status'         => ob_end_clean(),
				)
			);

		}

		die;

	}

	add_action( 'wp_ajax_nopriv_realhomes_filter_agents', 'realhomes_filter_agents' );
	add_action( 'wp_ajax_realhomes_filter_agents', 'realhomes_filter_agents' );
}


if ( ! function_exists( 'realhomes_agents_search_args' ) ) {
	/**
	 * Filter Agents based on Search Parameters
	 *
	 * @since 4.0.0
	 * @return string JSON
	 *
	 */
	function realhomes_agents_search_args() {

		$paged = 1;
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		}

		$number_of_agencies = intval( get_option( 'theme_number_posts_agent', 3 ) );
		if ( ! $number_of_agencies ) {
			$number_of_agencies = 6;
		}

		$search_args = array(
			'post_type'      => 'agent',
			'posts_per_page' => $number_of_agencies,
			'paged'          => $paged,
		);

		/* Initialize Taxonomy Query Array */
		$tax_query = array();

		/* Initialize Meta Query Array */
		$meta_query = array();

		/* Keyword Search */
		if ( ! empty( $_GET['agent-txt'] ) ) {
			$search_args['s'] = $_GET['agent-txt'];
		}

		/* Meta Search Filter */
		$meta_query = apply_filters( 'realhomes_agent_meta_search', $meta_query );

		/* If more than one meta query elements exist then specify the relation */
		$meta_count = count( $meta_query );
		if ( $meta_count > 1 ) {
			$meta_query['relation'] = 'AND';
		}

		/* If meta query has some values then add it to search query */
		if ( $meta_count > 0 ) {
			$search_args['meta_query'] = $meta_query;
		}

		/* Taxonomy Search Filter */
		$tax_query = apply_filters( 'realhomes_agent_taxonomy_search', $tax_query );

		/* If more than one taxonomies exist then specify the relation */
		$tax_count = count( $tax_query );
		if ( $tax_count > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		/* If taxonomy query has some values then add it to search query */
		if ( $tax_count > 0 ) {
			$search_args['tax_query'] = $tax_query;
		}

		/* Sort Agents by Number of Properties */
		$search_args = apply_filters( 'realhomes_filter_agents_by_properties', $search_args );

		return $search_args;

	}

	add_filter( 'realhomes_agents_search_filter', 'realhomes_agents_search_args' );
}

if ( ! function_exists( 'realhomes_agent_location_search' ) ) {
	/**
	 * Add agent location related search arguments to taxonomy query of Agent Search
	 *
	 * @since 4.0.0
	 *
	 * @param $tax_query
	 *
	 * @return array
	 *
	 */
	function realhomes_agent_location_search( $tax_query ) {

		if ( ( ! empty( $_GET['agent-locations'] ) ) && ( $_GET['agent-locations'] != inspiry_any_value() ) ) {
			$tax_query[] = array(
				'taxonomy' => 'agent-location',
				'field'    => 'slug',
				'terms'    => $_GET['agent-locations'],
			);
		}

		if ( ( ! empty( $_POST['agentlocations'] ) ) && ( $_POST['agentlocations'] != inspiry_any_value() ) ) {
			$tax_query[] = array(
				'taxonomy' => 'agent-location',
				'field'    => 'slug',
				'terms'    => $_POST['agentlocations'],
			);
		}

		return $tax_query;

	}

	add_filter( 'realhomes_agent_taxonomy_search', 'realhomes_agent_location_search' );
}

if ( ! function_exists( 'realhomes_filter_agents_by_properties' ) ) {
	/**
	 * Display Agents based on the number of properties selected - Agent Search
	 *
	 * Optimized for large databases (30k+ properties)
	 *
	 * @since 4.0.0
	 *
	 * @param array $search_args
	 *
	 * @return array
	 */
	function realhomes_filter_agents_by_properties( $search_args ) {
		global $wpdb;

		$target_properties = null;

		// Check POST first (Ajax)
		if ( ! empty( $_POST['properties'] ) ) {
			$target_properties = absint( $_POST['properties'] );
		} // Fallback to GET (normal page load / URL parameter)
		else if ( ! empty( $_GET['number-of-properties'] ) ) {
			$target_properties = absint( $_GET['number-of-properties'] );
		}

		// No selection, return original query
		if ( empty( $target_properties ) ) {
			return $search_args;
		}

		// Direct optimized SQL query to count properties per agent
		$query = $wpdb->prepare(
			"
			SELECT meta_value AS agent_id, COUNT(post_id) AS total
			FROM {$wpdb->postmeta}
			WHERE meta_key = %s
			GROUP BY meta_value
			HAVING total >= %d
			",
			'REAL_HOMES_agents',
			$target_properties
		);

		$results = $wpdb->get_results( $query );

		if ( empty( $results ) ) {
			$search_args['post__in'] = array( 0 ); // No agents found

			return $search_args;
		}

		// Extract agent IDs
		$found_agents = array_map(
			function( $row ) {
				return (int)$row->agent_id;
			},
			$results
		);

		$search_args['post__in'] = $found_agents;

		return $search_args;
	}

	add_filter( 'realhomes_filter_agents_by_properties', 'realhomes_filter_agents_by_properties' );
}

if ( ! function_exists( 'realhomes_verified_agents' ) ) {
	/**
	 * Display Verified Agents - Agent Search
	 *
	 * @since 4.0.0
	 *
	 * @param $tax_query
	 *
	 * @return array
	 *
	 */
	function realhomes_verified_agents( $meta_query ) {

		$is_verified = ( ! empty( $_GET['verified-agents'] ) && 'yes' === $_GET['verified-agents'] )
			|| ( ! empty( $_POST['verifiedAgents'] ) && 'yes' === $_POST['verifiedAgents'] );

		if ( $is_verified ) {
			$meta_query[] = array(
				'key'     => 'ere_agent_verification_status',
				'value'   => '1',
				'compare' => '=',
			);
		}

		return $meta_query;

	}

	add_filter( 'realhomes_agent_meta_search', 'realhomes_verified_agents' );
}