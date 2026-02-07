<?php
/**
 * Agency Search Feature
 *
 * @since   4.0.0
 * @package realhomes/functions
 */

if ( ! function_exists( 'realhomes_agency_locations_options' ) ) {
	/**
	 * Agency hierarchical location options
	 *
	 * @since    4.0.0
	 * @return void
	 */
	function realhomes_agency_locations_options() {
		if ( ! class_exists( 'ERE_Data' ) ) {
			return;
		}

		$hierarchical_terms_array = array();
		$searched_terms           = null;
		$excluded_terms           = null;

		$hierarchical_terms_array = ERE_Data::get_agency_hierarchical_locations();

		if ( isset( $_GET['agency-locations'] ) ) {
			$searched_terms = $_GET['agency-locations'];
		} else if ( ! empty( $_GET['agency-locations'] ) ) {
			$searched_terms = $_GET['agency-locations'];
		}

		realhomes_generate_options( $hierarchical_terms_array, $searched_terms, '', $excluded_terms );

	}
}

if ( ! function_exists( 'realhomes_filter_agency' ) ) {
	/**
	 * Filter Agents based on Search Parameters for Ajax Call
	 *
	 * @since 4.0.0
	 * @return string JSON
	 *
	 */
	function realhomes_filter_agency() {

		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'agency_search_nonce' ) ) {
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

		$number_of_agencies = intval( get_option( 'inspiry_number_posts_agency' ) );
		if ( ! $number_of_agencies ) {
			$number_of_agencies = 3;
		}

		$search_args = array(
			'post_type'      => 'agency',
			'posts_per_page' => $number_of_agencies,
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
		$meta_query = apply_filters( 'realhomes_agency_meta_search', $meta_query );

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
		$tax_query = apply_filters( 'realhomes_agency_taxonomy_search', $tax_query );

		/* If more than one taxonomies exist then specify the relation */
		$tax_count = count( $tax_query );
		if ( $tax_count > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		/* If taxonomy query has some values then add it to search query */
		if ( $tax_count > 0 ) {
			$search_args['tax_query'] = $tax_query;
		}

		$search_args = apply_filters( 'realhomes_filter_agencies_by_agents', $search_args );

		$agency_search_query  = new WP_Query( $search_args );
		$agency_search_layout = 'assets/modern/partials/agency/card';

		ob_start();
		if ( $agency_search_query->have_posts() ) {

			while ( $agency_search_query->have_posts() ) {

				$agency_search_query->the_post();

				get_template_part( $agency_search_layout );
				$search_results = ob_get_contents();

			}

			wp_send_json_success(
				array(
					'search_results' => $search_results,
					'status'         => ob_end_clean(),
					'max_pages'      => $agency_search_query->max_num_pages,
					'total_agencies' => $agency_search_query->found_posts
				)
			);

			wp_reset_postdata();

		} else {

			$search_results = '<div class="rh_agency_card__wrap my-2 no-results">';
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

	add_action( 'wp_ajax_nopriv_realhomes_filter_agency', 'realhomes_filter_agency' );
	add_action( 'wp_ajax_realhomes_filter_agency', 'realhomes_filter_agency' );
}

if ( ! function_exists( 'realhomes_agency_search_args' ) ) {
	/**
	 * Filter Agents based on Search Parameters for Ajax Call
	 *
	 * @since 4.0.0
	 * @return string JSON
	 *
	 */
	function realhomes_agency_search_args() {

		$paged = 1;
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		}

		$number_of_agencies = intval( get_option( 'inspiry_number_posts_agency', 3 ) );
		if ( ! $number_of_agencies ) {
			$number_of_agencies = 6;
		}

		$search_args = array(
			'post_type'      => 'agency',
			'posts_per_page' => $number_of_agencies,
			'paged'          => $paged,
		);

		/* Initialize Taxonomy Query Array */
		$tax_query = array();

		/* Initialize Meta Query Array */
		$meta_query = array();

		/* Meta Search Filter */
		$meta_query = apply_filters( 'realhomes_agency_meta_search', $meta_query );

		/* If more than one meta query elements exist then specify the relation */
		$meta_count = count( $meta_query );
		if ( $meta_count > 1 ) {
			$meta_query['relation'] = 'AND';
		}

		/* If meta query has some values then add it to search query */
		if ( $meta_count > 0 ) {
			$search_args['meta_query'] = $meta_query;
		}

		/* Keyword Search */
		if ( ! empty( $_GET['agency-txt'] ) ) {
			$search_args['s'] = $_GET['agency-txt'];
		}

		/* Taxonomy Search Filter */
		$tax_query = apply_filters( 'realhomes_agency_taxonomy_search', $tax_query );

		/* If more than one taxonomies exist then specify the relation */
		$tax_count = count( $tax_query );
		if ( $tax_count > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		/* If taxonomy query has some values then add it to search query */
		if ( $tax_count > 0 ) {
			$search_args['tax_query'] = $tax_query;
		}

		$search_args = apply_filters( 'realhomes_filter_agencies_by_agents', $search_args );

		return $search_args;

	}

	add_filter( 'realhomes_agencies_search_filter', 'realhomes_agency_search_args' );
}

if ( ! function_exists( 'realhomes_agency_location_search' ) ) {
	/**
	 * Add agency location related search arguments to taxonomy query of Agent Search
	 *
	 * @since 4.0.0
	 *
	 * @param $tax_query
	 *
	 * @return array
	 *
	 */
	function realhomes_agency_location_search( $tax_query ) {

		if ( ( ! empty( $_GET['agency-locations'] ) ) && ( $_GET['agency-locations'] != inspiry_any_value() ) ) {
			$tax_query[] = array(
				'taxonomy' => 'agency-location',
				'field'    => 'slug',
				'terms'    => $_GET['agency-locations'],
			);
		}

		if ( ( ! empty( $_POST['agencylocations'] ) ) && ( $_POST['agencylocations'] != inspiry_any_value() ) ) {
			$tax_query[] = array(
				'taxonomy' => 'agency-location',
				'field'    => 'slug',
				'terms'    => $_POST['agencylocations'],
			);
		}

		return $tax_query;

	}

	add_filter( 'realhomes_agency_taxonomy_search', 'realhomes_agency_location_search' );
}


if ( ! function_exists( 'realhomes_agencies_by_properties' ) ) {
	/**
	 * Display Agencies based on the number of properties selected - Agency Search
	 *
	 * @since 4.3.2
	 *
	 * @param $search_args
	 *
	 * @return array
	 *
	 */
	function realhomes_agencies_by_properties( $search_args ) {

		if ( empty( $_POST['properties'] ) ) {
			return $search_args;
		}

		$target_prop_count   = $_POST['properties'];
		$agencies_with_posts = array();

		$agency_args  = array(
			'post_type'      => 'agency',
			'posts_per_page' => 1500, // limiting number for better performance
		);
		$agency_posts = get_posts( $agency_args );

		if ( ! is_wp_error( $agency_posts ) && 0 < count( $agency_posts ) ) {
			foreach ( $agency_posts as $agency ) {
				$agents_args = array(
					'post_type'      => 'agent',
					'posts_per_page' => 1500, // limiting number for better performance
					'meta_query'     => array(
						array(
							'key'     => 'REAL_HOMES_agency',
							'value'   => $agency->ID,
							'compare' => '=',
						)
					)
				);

				$agent_posts = get_posts( $agents_args );

				if ( ! is_wp_error( $agent_posts ) && 0 < count( $agent_posts ) ) {
					foreach ( $agent_posts as $agent ) {

						$properties_args = array(
							'post_type'      => 'property',
							'posts_per_page' => 1500, // limiting number for better performance
							'meta_query'     => array(
								array(
									'key'     => 'REAL_HOMES_agents',
									'value'   => intval( $agent->ID ),
									'compare' => '=',
								)
							)
						);

						$properties                                       = get_posts( $properties_args );
						$agencies_with_posts[ $agency->ID ][ $agent->ID ] = count( $properties );
					}
				}
			}
		}

		// Filter agencies based on the total agent count
		$found_agencies = array_filter( $agencies_with_posts, function ( $agents ) use ( $target_prop_count ) {
			return array_sum( $agents ) == $target_prop_count;
		}, ARRAY_FILTER_USE_BOTH );

		$found_agencies          = array_keys( $found_agencies );
		$search_args['post__in'] = $found_agencies;

		return $search_args;

	}

	add_filter( 'realhomes_filter_agencies_by_properties', 'realhomes_agencies_by_properties' );
}

if ( ! function_exists( 'realhomes_filter_agencies_by_agents' ) ) {
	/**
	 * Filter agencies by the selected number of agents.
	 *
	 * @since 4.4.4
	 *
	 * @param array $search_args Existing search arguments.
	 *
	 * @return array Modified search arguments.
	 */
	function realhomes_filter_agencies_by_agents( $search_args ) {

		$number_of_agents = null;

		if ( ! empty( $_GET['agents'] ) && ( $_GET['agents'] != inspiry_any_value() ) ) {
			$number_of_agents = absint( $_GET['agents'] );
		}

		if ( ! empty( $_POST['numberofagents'] ) ) {
			$number_of_agents = absint( $_POST['numberofagents'] );
		}

		// If no specific number of agents is selected, return original args
		if ( empty( $number_of_agents ) ) {
			return $search_args;
		}

		// Fetch all agents with agency assigned with their agency ID
		$agent_posts = get_posts( array(
			'post_type'      => 'agent',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => 'REAL_HOMES_agency',
					'compare' => 'EXISTS',
				),
			),
		) );

		$agency_counts = array();

		// Count agents per agency
		foreach ( $agent_posts as $agent_id ) {
			$agency_id = get_post_meta( $agent_id, 'REAL_HOMES_agency', true );
			if ( $agency_id ) {
				if ( ! isset( $agency_counts[ $agency_id ] ) ) {
					$agency_counts[ $agency_id ] = 0;
				}
				$agency_counts[ $agency_id ]++;
			}
		}

		// Filter agencies that match the required agent count
		$agencies_with_agents = array();
		foreach ( $agency_counts as $agency_id => $count ) {
			if ( $count >= $number_of_agents ) {
				$agencies_with_agents[] = $agency_id;
			}
		}

		// If no agencies found, ensure no results by setting post__in to an empty array
		if ( ! empty( $agencies_with_agents ) ) {
			$search_args['post__in'] = $agencies_with_agents;
		} else {
			$search_args['post__in'] = array( 0 );
		}

		return $search_args;
	}

	add_filter( 'realhomes_filter_agencies_by_agents', 'realhomes_filter_agencies_by_agents' );
}

if ( ! function_exists( 'realhomes_verified_agencies' ) ) {
	/**
	 * Display Verified Agencies - Agency Search
	 *
	 * @since 4.4.4
	 *
	 * @param $meta_query
	 *
	 * @return array
	 *
	 */
	function realhomes_verified_agencies( $meta_query ) {


		$is_verified = ( ! empty( $_GET['verified-agencies'] ) && 'yes' === $_GET['verified-agencies'] )
			|| ( ! empty( $_POST['verifiedAgencies'] ) && 'yes' === $_POST['verifiedAgencies'] );

		if ( $is_verified ) {
			$meta_query[] = array(
				'key'     => 'ere_agency_verification_status',
				'value'   => '1',
				'compare' => '=',
			);
		}

		return $meta_query;

	}

	add_filter( 'realhomes_agency_meta_search', 'realhomes_verified_agencies' );
}