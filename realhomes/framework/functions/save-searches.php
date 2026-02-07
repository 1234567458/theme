<?php
/**
 * Functions: Saved Searches.
 *
 * @since   3.13
 * @package rh/functions
 */

if ( ! function_exists( 'inspiry_is_save_search_enabled' ) ) {
	/**
	 * Check if Save Search feature is enabled.
	 */
	function inspiry_is_save_search_enabled() {

		if (
			'yes' === get_option( 'realhomes_saved_searches_enabled', 'yes' )
			&& (
				realhomes_get_current_user_role_option('manage_searches')
				|| ( ! is_user_logged_in() && 'no' === get_option( 'realhomes_saved_searches_required_login', 'yes' ) )
			)
		) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_save_search' ) ) {
	/**
	 * Save search for the alert to the user meta.
	 *
	 * @since  1.13
	 */
	function inspiry_save_search() {

		if (
			'yes' === get_option( 'realhomes_saved_searches_enabled', 'yes' )
			&& (
				realhomes_get_current_user_role_option('manage_searches')
				|| ( ! is_user_logged_in() && 'no' === get_option( 'realhomes_saved_searches_required_login', 'yes' ) )
			)
		) {

			$nonce = $_POST['nonce'];

			if ( ! wp_verify_nonce( $nonce, 'inspiry_save_search' ) ) {
				echo wp_json_encode(
					array(
						'success' => false,
						'message' => esc_html__( 'Security verification failed, please refresh the page and try again.', RH_TEXT_DOMAIN ),
					)
				);
				wp_die();
			}

			global $wpdb, $current_user;

			$user_id     = is_user_logged_in() ? $current_user->ID : 0; // Assign user ID if logged in, else store as guest (0)
			$search_args = $_POST['search_args'];
			$search_url  = $_POST['search_url'];
			$table_name  = $wpdb->prefix . 'realhomes_saved_searches';

			// Insert into database
			$wpdb->insert(
				$table_name,
				array(
					'user_id'              => $user_id,
					'search_wp_query_args' => $search_args,
					'search_query_str'     => $search_url,
					'time'                 => current_time( 'mysql' ),
				),
				array(
					'%d',
					'%s',
					'%s',
					'%s',
				)
			);

			// Get the inserted row ID
			$saved_search_id = $wpdb->insert_id;

			echo wp_json_encode(
				array(
					'success' => true,
					'message' => esc_html__( 'Search is Saved!', RH_TEXT_DOMAIN ),
					'search_id' => $saved_search_id, // Return saved search ID
				)
			);
			wp_die();
		} else {
			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Invalid Request!', RH_TEXT_DOMAIN ),
				)
			);
		}
		die;
	}

	add_action( 'wp_ajax_nopriv_inspiry_save_search', 'inspiry_save_search' );
	add_action( 'wp_ajax_inspiry_save_search', 'inspiry_save_search' );
}


if ( ! function_exists( 'inspiry_prepare_save_search_table' ) ) {
	/**
	 * Create or update the saved searches database table
	 * Only runs when:
	 * 1. Saved searches are enabled (default: yes), AND
	 * 2. The table doesn't already exist
	 */
	function inspiry_prepare_save_search_table() {
		// Check if saved searches are enabled (default to 'yes')
		if ( 'yes' !== get_option( 'realhomes_saved_searches_enabled', 'yes' ) ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'realhomes_saved_searches';

		// Check if table exists using prepared statement
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name ) {
			// Table exists - just modify columns if needed
			$wpdb->query(
				"ALTER TABLE $table_name
                MODIFY COLUMN search_query_str LONGTEXT NOT NULL"
			);

			return;
		}

		// Create table if it doesn't exist
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id mediumint(9) NOT NULL,
            search_wp_query_args longtext NOT NULL,
            search_query_str longtext NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Log errors if any
		if ( ! empty( $wpdb->last_error ) ) {
			error_log( 'RealHomes Saved Searches Table Error: ' . $wpdb->last_error );
		}
	}

	// Run on init hook for both frontend and backend
	add_action( 'init', 'inspiry_prepare_save_search_table' );
}


if ( ! function_exists( 'inspiry_tax_terms_string' ) ) {
	/**
	 * Return taxonomy terms as a single string.
	 *
	 * @param array  $slugs slugs of taxonomy.
	 * @param string $taxonomy_name Taxonomy name.
	 * @return string
	 */
	function inspiry_tax_terms_string( $slugs, $taxonomy_name ) {
		$terms_array = array();
		if ( is_array( $slugs ) && ! empty( $slugs ) ) {
			foreach ( $slugs as $slug ) {
				$term_obj      = get_term_by( 'slug', $slug, $taxonomy_name );
				$terms_array[] = $term_obj->name;
			}

			$result = join( ', ', $terms_array );
			return $result;
		} elseif ( ! empty( $slugs ) ) {
			$term_obj = get_term_by( 'slug', $slugs, $taxonomy_name );
			return $term_obj->name;
		}
		return '';
	}
}

if ( ! function_exists( 'inspiry_delete_saved_search_item' ) ) {
	/**
	 * Save search for the alert to the user meta.
	 *
	 * @since  1.13
	 * @return void
	 */
	function inspiry_delete_saved_search_item() {

		if ( inspiry_is_save_search_enabled() ) {
			global $wpdb, $current_user;

			if ( isset( $_POST['user_id'] ) && ! empty( $_POST['user_id'] ) ) {
				$user_id = intval( $_POST['user_id'] );
			} else {
				$user_id = $current_user->ID;
			}

			$search_item_id = intval( $_POST['search_item_id'] );

			$table_name        = $wpdb->prefix . 'realhomes_saved_searches';
			$saved_search_item = $wpdb->get_row( 'SELECT * FROM ' . $table_name . ' WHERE id = ' . $search_item_id );

			if ( $user_id != $saved_search_item->user_id ) {
				echo wp_json_encode(
					array(
						'success' => false,
						'message' => esc_html__( 'Permissions Denied!', RH_TEXT_DOMAIN ),
					)
				);
				wp_die();
			} else {
				$wpdb->delete( $table_name, array( 'id' => $search_item_id ), array( '%d' ) );
				echo wp_json_encode(
					array(
						'success' => true,
						'message' => esc_html__( 'Search item is deleted successfully!', RH_TEXT_DOMAIN ),
					)
				);
				wp_die();
			}
		}
	}

	add_action( 'wp_ajax_nopriv_inspiry_delete_saved_search_item', 'inspiry_delete_saved_search_item' );
	add_action( 'wp_ajax_inspiry_delete_saved_search_item', 'inspiry_delete_saved_search_item' );
}

/**
 * Cron Job - Send new listing email matching saved searches.
 */
if ( ! function_exists( 'inspiry_init_searches_notification' ) ) {
	/**
	 * Initialze the cron job to notify saved searches.
	 */
	function inspiry_init_searches_notification() {
		$notify_duration = get_option( 'realhomes_search_emails_frequency', 'daily' );
		if ( inspiry_is_save_search_enabled() && ! wp_next_scheduled( 'realhomes_notify_searches' ) || wp_get_schedule( 'realhomes_notify_searches' ) !== $notify_duration ) {
			wp_clear_scheduled_hook( 'realhomes_notify_searches' );
			wp_schedule_event( time(), $notify_duration, 'realhomes_notify_searches' );
		}
	}
	inspiry_init_searches_notification();
}

if ( ! function_exists( 'inspiry_check_new_listing' ) ) {
	/**
	 * Check if new listing is published.
	 */
	function inspiry_check_new_listing() {

		$query_period = inspiry_get_query_period();

		$args = array(
			'post_type'     => 'property',
			'post_status'   => 'publish',
			'post_per_page' => -1,
			'date_query'    => $query_period,
		);

		$properties = new WP_QUERY( $args );

		if ( $properties->have_posts() ) {
			inspiry_send_new_listing_email();
		}
	}
	add_action( 'realhomes_notify_searches', 'inspiry_check_new_listing' );
}

if ( ! function_exists( 'inspiry_get_query_period' ) ) {
	/**
	 * Return formatted query date based on the notify duration.
	 */
	function inspiry_get_query_period() {

		$notify_duration = get_option( 'realhomes_search_emails_frequency', 'daily' );

		if ( 'weekly' === $notify_duration ) {
			$query_period = array(
				array(
					'year' => gmdate( 'Y' ),
					'week' => gmdate( 'W' ),
				),
			);
		} else {
			$current_date = getdate();
			$query_period = array(
				array(
					'year'  => $current_date['year'],
					'month' => $current_date['mon'],
					'day'   => $current_date['mday'],
				),
			);
		}
		return $query_period;
	}
}

if ( ! function_exists( 'inspiry_send_new_listing_email' ) ) {
	/**
	 * Send email notifications about new listing matching user saved searches.
	 */
	function inspiry_send_new_listing_email() {
		global $wpdb;
		$table_name     = $wpdb->prefix . 'realhomes_saved_searches';
		$saved_searches = $wpdb->get_results( 'SELECT * FROM ' . $table_name, OBJECT );

		// Build email subject.
		$default_subject = esc_html__( 'Check Out Latest Properties Matching Your Saved Search Criteria', RH_TEXT_DOMAIN );
		$subject         = get_option( 'realhomes_saved_search_email_subject', $default_subject );
		$subject         = empty( $subject ) ? $default_subject : $subject;

		if ( 0 !== count( $saved_searches ) ) {

			foreach ( $saved_searches as $saved_search ) {

				$args         = unserialize( base64_decode( $saved_search->search_wp_query_args ) );
				$mail_content = inspiry_prepare_mail_template( $args, $saved_search->search_query_str );
				$user_info    = get_userdata( $saved_search->user_id );
				$user_email   = $user_info->user_email;

				if ( ! empty( $user_email ) && ! empty( $mail_content ) ) {

					$headers   = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$headers   = apply_filters( 'realhomes_saved_search_mail_header', $headers );
					$subject   = $subject . ' - ' . get_bloginfo( 'name' );

					$email_body = array();
					$email_body = ere_email_template( $mail_content );

					wp_mail( $user_email, $subject, $email_body, $headers );
				}
			}
		}
	}
}

if ( ! function_exists( 'inspiry_prepare_mail_template' ) ) {
	/**
	 * Prepare the template with properties data to send as email notification.
	 *
	 * @param array  $args WP_QUERY arguments.
	 * @param string $url_query Search query of URL.
	 */
	function inspiry_prepare_mail_template( $args, $url_query ) {

		$query_period       = inspiry_get_query_period();
		$args['date_query'] = $query_period;

		$properties   = new WP_QUERY( $args );
		$email_markup = '';

		if ( $properties->have_posts() ) {
			$number_of_properties = $properties->post_count;
			if ( 0 === $number_of_properties % 2 ) {
				$layout = 'even';
			} else {
				$layout = 'odd';
			}

			$email_markup .= '<div class="properties-wrap" style="text-align:center; margin-top: 20px; margin-right: -15px;">';

			$counter = 0;
			while ( $properties->have_posts() ) {
				$properties->the_post();

				if ( 'even' === $layout && ( $counter + 1 === $number_of_properties || $counter + 2 === $number_of_properties ) ) {
					$border_bottom = 'none';
				} elseif ( 'odd' === $layout && $counter + 1 === $number_of_properties ) {
					$border_bottom = 'none';
				} else {
					$border_bottom = '1px solid #dddddd';
				}

				$counter++;

				$email_markup .= '<div style="
					border-bottom: ' . $border_bottom . '; 
					margin-bottom: 15px;
					padding-bottom: 5px; 
					float: left;
					width: 47%;
					overflow: hidden;
					margin-right: 15px;
				">';

				if ( ! empty( get_the_post_thumbnail() ) ) {
					$image_id         = get_post_thumbnail_id();
					$image_attributes = wp_get_attachment_image_src( $image_id, 'property-thumb-image' );
					$image_url        = $image_attributes[0];
				} else {
					$image_url = get_inspiry_image_placeholder_url( 'property-thumb-image' );
				}

				$email_markup .= '<a href="' . get_the_permalink() . '" target="_blank"><img src="' . $image_url . '" width="100%"></a><br>';
				$email_markup .= '<a href="' . get_the_permalink() . '" target="_blank" style="
					text-decoration: none;
					font-size: 15px;
				">' . get_the_title() . '</a>';
				$email_markup .= '<p style="margin:-10px 0 0;color:#1ea69a;font-size:13px;">' . ere_get_property_price() . '</p><br><br>';
				$email_markup .= '</div>';
			}
			$email_markup .= '</div>';
		} else {
			return '';
		}

		// Search results page url.
		$search_page_url    = inspiry_get_search_page_url();
		$search_results_url = $search_page_url . '?' . $url_query;

		// Email template header and footer text.
		$default_header_text = esc_html__( 'Following new properties are listed matching your search criteria. You can check the [search results here].', RH_TEXT_DOMAIN );
		$default_footer_text = esc_html__( 'To stop getting such emails, Simply remove related saved search from your account.', RH_TEXT_DOMAIN );

		$header_text = get_option( 'realhomes_saved_search_email_header', $default_header_text );
		$footer_text = get_option( 'realhomes_saved_search_email_footer', $default_footer_text );

		$header_text = empty( $header_text ) ? $default_header_text : $header_text;
		$footer_text = empty( $footer_text ) ? $default_footer_text : $footer_text;

		$header_text = str_replace( '[', '%1$s', $header_text );
		$header_text = str_replace( ']', '%2$s', $header_text );
		$header_text = sprintf( $header_text, '<a href="' . esc_url( $search_results_url ) . '">', '</a>' );

		$mail_content   = array();
		$mail_content[] = array(
			'name'  => '',
			'value' => $header_text,
		);

		$mail_content[] = array(
			'name'  => '',
			'value' => $email_markup,
		);

		$mail_content[] = array(
			'name'  => '',
			'value' => $footer_text,
		);

		return $mail_content;
	}
}


if ( ! function_exists( 'realhomes_fetch_saved_searches' ) ) {
	/**
	 * Fetch saved searches for logged-in and guest users.
	 *
	 * @since 4.3.8
	 */
	function realhomes_fetch_saved_searches() {
		// Verify the nonce
		if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( $_POST['_ajax_nonce'], 'saved_searches_nonce' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Nonce verification failed.', RH_TEXT_DOMAIN ) ) );
		}

		global $wpdb;

		$table_name     = $wpdb->prefix . 'realhomes_saved_searches';
		$saved_searches = [];

		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();

			// Fetch searches from DB for logged-in users
			$saved_searches = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$table_name} WHERE user_id = %d ORDER BY id DESC", $user_id ),
				OBJECT
			);
		} else if ( isset( $_POST['saved_searches'] ) ) {
			$stored_saved_searches = $_POST['saved_searches'];
			$saved_search_ids      = array();

			if ( is_array( $stored_saved_searches ) ) {
				foreach ( $stored_saved_searches as $saved_search ) {
					$saved_search_ids[] = $saved_search['search_id'];
				}
			}

			// Remove null values (in case some objects don't have search_id)
			$saved_search_ids = array_filter( $saved_search_ids );

			if ( ! empty( $saved_search_ids ) && is_array( $saved_search_ids ) ) {
				$placeholders = implode( ',', array_fill( 0, count( $saved_search_ids ), '%d' ) );

				// Prepare and execute the query properly
				$query = $wpdb->prepare(
					"SELECT * FROM {$table_name} WHERE id IN ($placeholders) ORDER BY id DESC",
					...$saved_search_ids
				);

				$saved_searches = $wpdb->get_results( $query, OBJECT );
			} else {
				$saved_searches = [];
			}
		}

		ob_start();

		if ( ! empty( $saved_searches ) ) {
			realhomes_dashboard_notice(
				[
					esc_html__( 'Welcome to Saved Searches!', RH_TEXT_DOMAIN ),
					esc_html__( 'You will get email updates about latest properties that will match your saved search criteria.', RH_TEXT_DOMAIN )
				],
				'info',
				true
			);
			?>
            <div id="saved-searches" class="saved-searches">
                <div class="dashboard-posts-list">
                    <div class="dashboard-posts-list-body">
						<?php
						foreach ( $saved_searches as $search_data ) {
							$args = [
								'search_data' => $search_data,
								'separator'   => '<span>|</span>',
							];
							get_template_part( 'common/dashboard/saved-search-item', '', $args );
						}
						?>
                    </div>
                </div>
            </div>
			<?php
		} else {
			realhomes_dashboard_no_items(
				esc_html__( 'No saved searches!', RH_TEXT_DOMAIN ),
				esc_html__( 'You have not saved any search.', RH_TEXT_DOMAIN ),
				'no-searches.svg'
			);
		}

		wp_send_json_success( array( 'html' => ob_get_clean() ) );
	}

	add_action( 'wp_ajax_realhomes_fetch_saved_searches', 'realhomes_fetch_saved_searches' );
	add_action( 'wp_ajax_nopriv_realhomes_fetch_saved_searches', 'realhomes_fetch_saved_searches' );
}


if ( ! function_exists( 'realhomes_saved_searches_migration' ) ) {
	/**
	 * Migrate saved searches from local storage to the server.
	 *
	 * @modified 4.3.8
	 */
	function realhomes_saved_searches_migration() {
		// Verify nonce for security.
		if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( $_POST['_ajax_nonce'], 'realhomes_saved_search_migration_nonce' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Security check failed!', RH_TEXT_DOMAIN ) ) );
		}

		// Ensure user login and data integrity.
		if ( isset( $_POST['saved_searches'] ) && is_array( $_POST['saved_searches'] ) && is_user_logged_in() ) {

			// Ensure the required table exists.
			inspiry_prepare_save_search_table();

			global $wpdb, $current_user;
			$user_id    = $current_user->ID;
			$table_name = $wpdb->prefix . 'realhomes_saved_searches';
			$saved_ids  = array();

			foreach ( $_POST['saved_searches'] as $saved_search ) {
				$wp_query_args = $saved_search['wp_query_args'];
				$query_str     = $saved_search['query_str'];
				$current_date  = $saved_search['time'];

				// Insert into the database.
				$wpdb->insert(
					$table_name,
					array(
						'user_id'              => $user_id,
						'search_wp_query_args' => $wp_query_args,
						'search_query_str'     => $query_str,
						'time'                 => $current_date,
					),
					array( '%d', '%s', '%s', '%s' )
				);

				// Get the inserted ID.
				$saved_ids[] = $wpdb->insert_id;
			}

			wp_send_json_success(
				array(
					'message'   => esc_html__( 'Saved searches migrated successfully!', RH_TEXT_DOMAIN ),
					'saved_ids' => $saved_ids, // Return the IDs of the migrated searches
				)
			);
		} else {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid request!', RH_TEXT_DOMAIN ) ) );
		}

		wp_die();
	}

	add_action( 'wp_ajax_realhomes_saved_searches_migration', 'realhomes_saved_searches_migration' );
}


if ( ! function_exists( 'realhomes_get_saved_search_migration_nonce' ) ) {
	/**
	 * Generate nonce for saved search migration.
	 * This function is called via AJAX and only allows logged-in users to receive a nonce.
	 *
	 * @since 4.3.8
	 */
	function realhomes_get_saved_search_migration_nonce() {
		if ( is_user_logged_in() ) {
			echo wp_create_nonce( 'realhomes_saved_search_migration_nonce' );
		} else {
			echo 'false';
		}
		wp_die();
	}

	add_action( 'wp_ajax_realhomes_get_saved_search_migration_nonce', 'realhomes_get_saved_search_migration_nonce' );
}