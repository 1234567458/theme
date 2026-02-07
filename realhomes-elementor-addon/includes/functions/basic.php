<?php
/**
 * Contains Basic Functions for RealHomes Elementor Addon plugin.
 */

/**
 * Get template part for RHEA plugin.
 *
 * @access public
 *
 * @param mixed  $slug Template slug.
 * @param string $name Template name (default: '').
 */
function rhea_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Get slug-name.php.
	if ( ! $template && $name && file_exists( RHEA_PLUGIN_DIR . "/{$slug}-{$name}.php" ) ) {
		$template = RHEA_PLUGIN_DIR . "/{$slug}-{$name}.php";
	}

	// Get slug.php.
	if ( ! $template && file_exists( RHEA_PLUGIN_DIR . "/{$slug}.php" ) ) {
		$template = RHEA_PLUGIN_DIR . "/{$slug}.php";
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'rhea_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

if ( ! function_exists( 'rhea_allowed_html' ) ) {
	/**
	 * Returns array of allowed tags to be used in wp_kses function
	 *
	 * @modified 2.1.1
	 *
	 * @return array
	 */
	function rhea_allowed_html() {

		$allowed_html = [
			'a'      => [ 'href' => [], 'title' => [], 'alt' => [], 'target' => [] ],
			'b'      => [],
			'br'     => [],
			'em'     => [],
			'strong' => [],
			'div'    => [ 'class' => [], 'id' => [] ],
			'i'      => [ 'aria-hidden' => [], 'class' => [] ],
			'svg'    => array_fill_keys(
				[ 'xmlns', 'xmlns:xlink', 'width', 'height', 'viewBox', 'fill', 'stroke', 'stroke-width', 'stroke-linecap',
                'stroke-linejoin', 'stroke-miterlimit', 'role', 'aria-hidden', 'aria-labelledby', 'aria-describedby', 'id',
                'class', 'style', 'focusable', 'preserveAspectRatio' ], []
			),
			'path'   => array_fill_keys(
				[ 'd', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit',
                'opacity', 'fill-rule', 'clip-rule', 'style' ], []
			)
		];

		return apply_filters( 'rhea_allowed_html', $allowed_html );
	}
}

if ( ! function_exists( 'rhea_list_gallery_images' ) ) {
	/**
	 * Get list of Gallery Images - use in gallery post format
	 *
	 * @param string $size
	 */
	function rhea_list_gallery_images( $size = 'post-featured-image' ) {

		$gallery_images = rwmb_meta( 'REAL_HOMES_gallery', 'type=plupload_image&size=' . $size, get_the_ID() );

		if ( ! empty( $gallery_images ) ) {
			foreach ( $gallery_images as $gallery_image ) {
				$caption = ( ! empty( $gallery_image['caption'] ) ) ? $gallery_image['caption'] : $gallery_image['alt'];
				echo '<li><a href="' . esc_url( $gallery_image['full_url'] ) . '" title="' . esc_attr( $caption ) . '" data-fancybox="thumbnail-' . get_the_ID() . '">';
				echo '<img src="' . esc_url( $gallery_image['url'] ) . '" alt="' . esc_attr( $gallery_image['title'] ) . '" />';
				echo '</a></li>';
			}
		} else if ( has_post_thumbnail( get_the_ID() ) ) {
			echo '<li><a href="' . get_permalink() . '" title="' . get_the_title() . '" >';
			the_post_thumbnail( $size );
			echo '</a></li>';
		}
	}
}

if ( ! function_exists( 'rhea_framework_excerpt' ) ) {
	/**
	 * Output custom excerpt of required length
	 *
	 * @param int    $len  number of words
	 * @param string $trim string to appear after excerpt
	 */
	function rhea_framework_excerpt( $len = 15, $trim = "&hellip;" ) {
		echo rhea_get_framework_excerpt( $len, $trim );
	}
}

if ( ! function_exists( 'rhea_get_framework_excerpt' ) ) {
	/**
	 * Returns custom excerpt of required length
	 *
	 * @param int    $len  number of words
	 * @param string $trim string after excerpt
	 *
	 * @return string
	 */
	function rhea_get_framework_excerpt( $len = 15, $trim = "&hellip;" ) {
		$limit     = (int)$len + 1;
		$excerpt   = explode( ' ', get_the_excerpt(), $limit );
		$num_words = count( $excerpt );
		if ( $num_words >= $len ) {
			array_pop( $excerpt );
		} else {
			$trim = "";
		}
		$excerpt = implode( " ", $excerpt ) . "$trim";

		return $excerpt;
	}
}

if ( ! function_exists( 'rhea_get_framework_excerpt_by_id' ) ) {
	/**
	 * Returns custom excerpt of required length
	 *
	 * @param int    $id   post ID
	 * @param int    $len  number of words
	 * @param string $trim string after excerpt
	 *
	 * @return string
	 */
	function rhea_get_framework_excerpt_by_id( $id, $len = 15, $trim = '&hellip;' ) {
		$content = get_post_field( 'post_excerpt', $id );

		if ( empty( $content ) ) {
			$content = get_post_field( 'post_content', $id );

			if ( strpos( $content, '<!--more-->' ) !== false ) {
				$content = explode( '<!--more-->', $content )[0];
			}

			$content = wp_strip_all_tags( strip_shortcodes( $content ) );
		}

		$content = trim( $content );

		if ( empty( $content ) ) {
			return '';
		}

		$words = preg_split( '/\s+/', $content, $len + 1, PREG_SPLIT_NO_EMPTY );

		if ( count( $words ) > $len ) {
			array_pop( $words );
		}

		return implode( ' ', $words ) . ( count( $words ) >= $len ? $trim : '' );
	}


}


if ( ! function_exists( 'RHEA_ajax_pagination' ) ) {
	/**
	 * Function for Widgets AJAX pagination
	 *
	 * @param string $pages
	 * @param string $current_query
	 * @param string $container_class
	 * @param bool   $show_navigation_links Optional. Whether to show First/Prev/Next/Last links. Default true.
	 */
	function RHEA_ajax_pagination( $pages = '', $current_query = '', $container_class = '', $show_navigation_links = true ) {

		if ( empty( $current_query ) ) {
			global $wp_query;
			$current_query = $wp_query;
		}

		$paged = 1;
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} else if ( get_query_var( 'page' ) ) { // if static front page
			$paged = get_query_var( 'page' );
		}

		$prev          = $paged - 1;
		$next          = $paged + 1;
		$range         = 3; // Change it to show more links
		$pages_to_show = ( $range * 2 ) + 1;

		if ( $pages == '' ) {
			$pages = $current_query->max_num_pages;
			if ( ! $pages ) {
				$pages = 1;
			}
		}

		if ( empty( $container_class ) ) {
			$container_class = 'rhea_pagination_wrapper';
		}

		if ( 1 != $pages ) {
			printf( '<div class="%s rhea-listing-pagination-wrap">', esc_attr( $container_class ) );
			echo "<div class='pagination rhea-pagination-clean'>";

			if ( $show_navigation_links ) {
				if ( ( $paged > 2 ) && ( $paged > $range + 1 ) && ( $pages_to_show < $pages ) ) {
					echo "<a href='" . esc_url( get_pagenum_link( 1 ) ) . "' class='real-btn real-btn-jump real-btn-first' data-page='1'>" . esc_html__( 'First', RHEA_TEXT_DOMAIN ) . "</a> ";
				}

				if ( ( $paged > 1 ) && ( $pages_to_show < $pages ) ) {
					echo "<a rel='prev' href='" . esc_url( get_pagenum_link( $prev ) ) . "' class='real-btn real-btn-jump real-btn-prev' data-page='" . esc_attr( $prev ) . "'>" . esc_html__( 'Prev', RHEA_TEXT_DOMAIN ) . "</a>";
				}
			}

			$min_page_number = $paged - $range - 1;
			$max_page_number = $paged + $range + 1;

			for ( $i = 1; $i <= $pages; $i++ ) {
				if ( ( ( $i > $min_page_number ) && ( $i < $max_page_number ) ) || ( $pages <= $pages_to_show ) ) {
					$rel_tag = '';
					if ( $paged > $i ) {
						$rel_tag = 'rel="prev"';
					} else if ( $paged < $i ) {
						$rel_tag = 'rel="next"';
					}
					$current_class = 'real-btn';
					$current_class .= ( $paged == $i ) ? ' current' : '';
					echo "<a " . $rel_tag . " href='" . esc_url( get_pagenum_link( $i ) ) . "' class='" . esc_attr( $current_class ) . "' data-page='" . esc_attr( $i ) . "'>" . esc_html( $i ) . "</a>";
				}
			}

			if ( $show_navigation_links ) {
				if ( ( $paged < $pages ) && ( $pages_to_show < $pages ) ) {
					echo "<a rel='next' href='" . esc_url( get_pagenum_link( $next ) ) . "' class='real-btn real-btn-jump real-btn-next' data-page='" . esc_attr( $next ) . "'>" . esc_html__( 'Next', RHEA_TEXT_DOMAIN ) . "</a>";
				}

				if ( ( $paged < $pages - 1 ) && ( $paged + $range - 1 < $pages ) && ( $pages_to_show < $pages ) ) {
					echo "<a href='" . esc_url( get_pagenum_link( $pages ) ) . "' class='real-btn real-btn-jump real-btn-last' data-page='" . esc_attr( $pages ) . "'>" . esc_html__( 'Last', RHEA_TEXT_DOMAIN ) . "</a> ";
				}
			}

			echo "</div>";
			echo "</div>";
		}
	}
}


if ( ! function_exists( 'RHEA_ultra_ajax_pagination' ) ) {
	/**
	 * Function for Widgets AJAX pagination
	 *
	 * @param string $pages
	 */
	function RHEA_ultra_ajax_pagination( $pages = '' ) {

		global $wp_query;

		$paged = 1;
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} else if ( get_query_var( 'page' ) ) { // if is static front page
			$paged = get_query_var( 'page' );
		}

		$prev          = $paged - 1;
		$next          = $paged + 1;
		$range         = 3;                             // change it to show more links
		$pages_to_show = ( $range * 2 ) + 1;

		if ( $pages == '' ) {
			$pages = $wp_query->max_num_pages;
			if ( ! $pages ) {
				$pages = 1;
			}
		}

		if ( 1 != $pages ) {
			echo "<div class='rhea_ultra_pagination_wrapper rhea_pagination_wrapper'>";
			echo "<div class='pagination rhea-pagination-clean'>";

			if ( ( $paged > 2 ) && ( $paged > $range + 1 ) && ( $pages_to_show < $pages ) ) {
				echo "<a href='" . get_pagenum_link( 1 ) . "' class='rhea-ultra-btn real-btn'><i class='fas fa-caret-left'></i><i class='fas fa-caret-left'></i></a> "; // First Page
			}

			if ( ( $paged > 1 ) && ( $pages_to_show < $pages ) ) {
				echo "<a rel='prev' href='" . get_pagenum_link( $prev ) . "' class='rhea-ultra-btn real-btn'><i class='fas fa-caret-left'></i></a> "; // Previous Page
			}

			$min_page_number = $paged - $range - 1;
			$max_page_number = $paged + $range + 1;

			?>
            <div class="rhea_ultra_pagination_counter">
                <div class="rhea_ultra_pagination_counter_inner">
					<?php
					for ( $i = 1; $i <= $pages; $i++ ) {
						if ( ( ( $i > $min_page_number ) && ( $i < $max_page_number ) ) || ( $pages <= $pages_to_show ) ) {
							$rel_tag = '';
							if ( $paged > $i ) {
								$rel_tag = 'rel="prev"';
							} else if ( $paged < $i ) {
								$rel_tag = 'rel="next"';
							}
							$current_class = 'real-btn';
							$current_class .= ( $paged == $i ) ? ' current' : '';
							echo "<a " . $rel_tag . " href='" . get_pagenum_link( $i ) . "' class='" . $current_class . "' >" . $i . "</a> ";
						}
					}
					?>
                </div>
            </div>
			<?php

			if ( ( $paged < $pages ) && ( $pages_to_show < $pages ) ) {
				echo "<a rel='next' href='" . get_pagenum_link( $next ) . "' class='rhea-ultra-btn real-btn'><i class='fas fa-caret-right'></i> </a> "; // Next Page
			}

			if ( ( $paged < $pages - 1 ) && ( $paged + $range - 1 < $pages ) && ( $pages_to_show < $pages ) ) {
				echo "<a href='" . get_pagenum_link( $pages ) . "' class='rhea-ultra-btn real-btn'><i class='fas fa-caret-right'></i><i class='fas fa-caret-right'></i></a> "; // Last Page
			}

			echo "</div>";
			echo "</div>";
		}
	}
}

if ( ! function_exists( 'rhea_property_price' ) ) {
	/**
	 * Output property price
	 */
	function rhea_property_price() {
		echo rhea_get_property_price();
	}
}

if ( ! function_exists( 'rhea_get_property_price' ) ) {
	/**
	 * Returns property price in configured format
	 *
	 * @return mixed|string
	 */
	function rhea_get_property_price() {

		// get property price
		$price_digits = doubleval( get_post_meta( get_the_ID(), 'REAL_HOMES_property_price', true ) );

		if ( $price_digits ) {
			// get price prefix and postfix
			$price_pre_fix  = get_post_meta( get_the_ID(), 'REAL_HOMES_property_price_prefix', true );
			$price_post_fix = get_post_meta( get_the_ID(), 'REAL_HOMES_property_price_postfix', true );

			// if wp-currencies plugin installed and current currency cookie is set
			if ( class_exists( 'WP_Currencies' ) && isset( $_COOKIE["current_currency"] ) ) {
				$current_currency = $_COOKIE["current_currency"];
				if ( currency_exists( $current_currency ) ) {    // validate current currency
					$base_currency             = ere_get_base_currency();
					$converted_price           = convert_currency( $price_digits, $base_currency, $current_currency );
					$formatted_converted_price = format_currency( $converted_price, $current_currency );
					$formatted_converted_price = apply_filters( 'inspiry_property_converted_price', $formatted_converted_price, $price_digits );

					return $price_pre_fix . ' ' . $formatted_converted_price . ' ' . $price_post_fix;
				}
			}

			// otherwise go with default approach.
			$currency            = ere_get_currency_sign();
			$decimals            = intval( get_option( 'theme_decimals', '0' ) );
			$decimal_point       = get_option( 'theme_dec_point', '.' );
			$thousands_separator = get_option( 'theme_thousands_sep', ',' );
			$currency_position   = get_option( 'theme_currency_position', 'before' );
			$formatted_price     = number_format( $price_digits, $decimals, $decimal_point, $thousands_separator );
			$formatted_price     = apply_filters( 'inspiry_property_price', $formatted_price, $price_digits );

			if ( 'after' === $currency_position ) {
				return $price_pre_fix . ' ' . $formatted_price . $currency . ' <span>' . $price_post_fix . '</span>';
			} else {
				return $price_pre_fix . ' ' . $currency . $formatted_price . ' <span>' . $price_post_fix . '</span>';
			}

		} else {
			return ere_no_price_text();
		}
	}
}

if ( ! function_exists( 'rhea_display_property_label' ) ) {
	/**
	 * Display property label
	 *
	 * @param int    $post_id
	 * @param string $class
	 */
	function rhea_display_property_label( $post_id, $class = 'rhea-property-label' ) {

		$label_text = get_post_meta( $post_id, 'inspiry_property_label', true );
		$color      = get_post_meta( $post_id, 'inspiry_property_label_color', true );
		if ( ! empty ( $label_text ) ) {
			?>
            <span style="background: <?php echo esc_attr( $color ); ?>; border-color: <?php echo esc_attr( $color ); ?>" class='<?php echo esc_attr( $class ) ?>'><?php echo esc_html( $label_text ); ?></span>
			<?php

		}
	}
}

if ( ! function_exists( 'rhea_get_maps_type' ) ) {
	/**
	 * Returns the type currently available for use.
	 */
	function rhea_get_maps_type() {
		$google_maps_api_key = get_option( 'inspiry_google_maps_api_key', false );

		if ( ! empty( $google_maps_api_key ) ) {
			return 'google-maps';    // For Google Maps
		}

		return 'open-street-map';    // For OpenStreetMap https://www.openstreetmap.org/
	}
}

if ( ! function_exists( 'rhea_switch_currency_plain' ) ) {
	/**
	 * Convert and format given amount from base currency to current currency.
	 *
	 * @since  1.0.0
	 *
	 * @param string $amount Amount in digits to change currency for.
	 *
	 * @return string
	 */
	function rhea_switch_currency_plain( $amount ) {

		if ( function_exists( 'realhomes_currency_switcher_enabled' ) && realhomes_currency_switcher_enabled() ) {
			$base_currency    = realhomes_get_base_currency();
			$current_currency = realhomes_get_current_currency();
			$converted_amount = realhomes_convert_currency( $amount, $base_currency, $current_currency );

			return apply_filters( 'realhomes_switch_currency', $converted_amount );
		}
	}
}

if ( ! function_exists( 'rhea_get_location_options' ) ) {
	/**
	 * Return Property Locations as Options List in Json format
	 */
	function rhea_get_location_options() {

		$number     = 15;// Number of locations that will be returned per Ajax request
		$page       = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
		$offset     = $number * ( $page - 1 ); // Offset of locations list for the current Ajax request
		$sort_alpha = isset( $_GET['sortplpha'] ) && 'yes' === sanitize_text_field( $_GET['sortplpha'] );
		$hide_empty = isset( $_GET['hideemptyfields'] ) && 'yes' === sanitize_text_field( $_GET['hideemptyfields'] );
		$search     = isset( $_GET['query'] ) ? sanitize_text_field( $_GET['query'] ) : '';

		// Prepare a query to fetch property locations from database
		$term_args = array(
			'taxonomy'   => 'property-city',
			'number'     => $number,
			'offset'     => $offset,
			'hide_empty' => $hide_empty,
			'orderby'    => $sort_alpha ? 'name' : 'count',
			'order'      => $sort_alpha ? 'asc' : 'desc',
		);

		// If there is a search parameter
		if ( $search ) {
			$term_args['name__like'] = $search;
		}

		$term_args = apply_filters( 'rhea_location_terms_query_args', $term_args );

		$locations = get_terms( $term_args );

		// Build an array of locations info form their objects
		$options = array();
		if ( ! empty( $locations ) && ! is_wp_error( $locations ) ) {
			foreach ( $locations as $location ) {
				$options[] = array( $location->slug, $location->name );
			}
		}

		$options = apply_filters( 'rhea_location_options_output', $options, $locations );

		wp_send_json( $options ); // Return locations list in Json format
	}

	add_action( 'wp_ajax_rhea_get_location_options', 'rhea_get_location_options' );
	add_action( 'wp_ajax_nopriv_rhea_get_location_options', 'rhea_get_location_options' );
}


if ( ! function_exists( 'rhea_searched_ajax_locations' ) ) {
	/**
	 * Display Property Ajax Searched Locations Select Options
	 */
	function rhea_searched_ajax_locations() {

		$searched_locations = '';
		if ( isset( $_GET['location'] ) ) {
			$searched_locations = $_GET['location'];
		}

		if ( is_array( $searched_locations ) && ! empty( $_GET['location'] ) ) {

			foreach ( $searched_locations as $location ) {
				$searched_terms = get_term_by( 'slug', $location, 'property-city' );
				?>
                <option value="<?php echo esc_attr( $searched_terms->slug ) ?>" selected="selected"><?php echo esc_html( $searched_terms->name ) ?></option>
				<?php
			}
		} else if ( ! empty( $searched_terms ) ) {
			$searched_terms = get_term_by( 'slug', $searched_locations, 'property-city' );
			?>
            <option value="<?php echo esc_attr( $searched_terms->slug ) ?>" selected="selected"><?php echo esc_html( $searched_terms->name ) ?></option>
			<?php
		}

	}
}

if ( ! function_exists( 'rhea_rating_stars' ) ) {
	/**
	 * Display rated stars based on given number of rating
	 *
	 * @param int $rating - Average rating.
	 *
	 * @return string
	 */
	function rhea_rating_stars( $rating ) {

		$output = '';

		if ( ! empty( $rating ) ) {

			$whole    = floor( $rating );
			$fraction = $rating - $whole;

			$round = round( $fraction, 2 );

			$output = '<span class="rating-stars">';

			for ( $count = 1; $count <= $whole; $count++ ) {
				$output .= '<i class="fas fa-star rated"></i>'; //3
			}
			$half = 0;
			if ( $round <= .24 ) {
				$half = 0;
			} else if ( $round >= .25 && $round <= .74 ) {
				$half   = 1;
				$output .= '<i class="fas fa-star-half-alt"></i>';
			} else if ( $round >= .75 ) {
				$half   = 1;
				$output .= '<i class="fas fa-star rated"></i>';
			}

			$unrated = 5 - ( $whole + $half );
			for ( $count = 1; $count <= $unrated; $count++ ) {
				$output .= '<i class="far fa-star"></i>';
			}

			$output .= '</span>';
		}

		return $output;
	}
}

if ( ! function_exists( 'rhea_stylish_meta' ) ) {
	function rhea_stylish_meta( $label, $post_meta_key, $icon, $postfix = '', $index = '1', $layout = '1' ) {
		$property_id = get_the_ID();
		$post_meta   = get_post_meta( $property_id, $post_meta_key, true );

		// Return early if no metadata is found
		if ( empty( $post_meta ) ) {
			return '';
		}

		// Generate label markup if label exists
		$label_markup = $label ? sprintf( '<span class="rhea_meta_titles">%s</span>', esc_html( $label ) ) : '';

		// Generate meta markup
		$meta_markup = sprintf( '<span class="figure">%s</span>', esc_html( $post_meta ) );

		// Generate postfix markup if postfix exists
		$postfix_markup = '';
		if ( ! empty( $postfix ) ) {
			$get_postfix = get_post_meta( $property_id, $postfix, true );
			if ( ! empty( $get_postfix ) ) {
				$postfix_markup = sprintf( '<span class="label">%s</span>', esc_html( $get_postfix ) );
			}
		}
		?>
        <div class="rh_prop_card__meta" style="<?php echo ! empty( $index ) ? ( 'order: ' . esc_attr( $index ) ) : '' ?>">
			<?php if ( '2' === $layout ) : ?>
                <div class="rhea_meta_icon_wrapper">
					<?php rhea_property_meta_icon( $post_meta_key, $icon ); ?>
                </div>
				<?= $label_markup ?>
				<?= $meta_markup ?>
				<?= $postfix_markup ?><?php else : ?>
				<?= $label_markup ?>
                <div class="rhea_meta_icon_wrapper">
					<?php rhea_property_meta_icon( $post_meta_key, $icon ); ?>
					<?= $meta_markup ?>
					<?= $postfix_markup ?>
                </div>
			<?php endif; ?>
        </div>
		<?php
	}
}

if ( ! function_exists( 'rhea_stylish_meta_smart' ) ) {
	function rhea_stylish_meta_smart( $label, $post_meta_key, $icon, $postfix = '', $index = '1' ) {
		$property_id = get_the_ID();
		$post_meta   = get_post_meta( $property_id, $post_meta_key, true );

		if ( isset( $post_meta ) && ! empty( $post_meta ) ) {
			?>
            <div class="rh_prop_card__meta" style="<?php echo ! empty( $index ) ? ( 'order: ' . esc_attr( $index ) ) : '' ?>">
				<?php
				if ( $label ) {
					?>
                    <span class="rhea_meta_titles"><?php echo esc_html( $label ); ?></span>
					<?php
				}
				?>
                <div class="rhea_meta_icon_wrapper">
                    <span data-tooltip="<?php echo esc_html( $label ) ?>">
					<?php rhea_property_meta_icon( $post_meta_key, $icon ); ?>
                    </span>
                    <span class="rhea_meta_smart_box">
                    <span class="figure"><?php echo esc_html( $post_meta ); ?></span>
	                    <?php
	                    if ( ! empty( $postfix ) ) {
		                    $get_postfix = get_post_meta( $property_id, $postfix, true );
		                    if ( ! empty( $get_postfix ) ) {
			                    ?>
                                <span class="label"><?php echo esc_html( $get_postfix ); ?></span>
			                    <?php
		                    }
	                    }
	                    ?>
                    </span>
                </div>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'rhea_ultra_meta' ) ) {
	function rhea_ultra_meta( $label, $post_meta_key, $icon, $postfix = '', $index = '1', $layout = '1' ) {
		$property_id = get_the_ID();
		$post_meta   = get_post_meta( $property_id, $post_meta_key, true );

		// Return early if no metadata is found
		if ( empty( $post_meta ) ) {
			return '';
		}

		// Generate label markup if label exists
		$label_markup = $label ? sprintf( '<span class="rhea-meta-labels">%s</span>', esc_html( $label ) ) : '';

		// Generate meta markup
		$meta_markup = sprintf( '<span class="figure">%s</span>', esc_html( $post_meta ) );

		// Generate postfix markup if postfix exists
		$postfix_markup = '';
		if ( ! empty( $postfix ) ) {
			$get_postfix = get_post_meta( $property_id, $postfix, true );
			if ( ! empty( $get_postfix ) ) {
				$postfix_markup = sprintf( '<span class="label">%s</span>', esc_html( $get_postfix ) );
			}
		}
		?>
        <div class="rhea_ultra_prop_card__meta" style="<?php echo ! empty( $index ) ? ( 'order: ' . esc_attr( $index ) ) : ''; ?>">
            <div class="rhea_ultra_meta_icon_wrapper">
                <span class="rhea_ultra_meta_icon" title="<?php echo esc_attr( $label ); ?>"><?php rhea_property_meta_icon( $post_meta_key, $icon ); ?></span>
                <div class="rhea_ultra_meta_box">
					<?php
					if ( '2' === $layout ) {
						echo $label_markup;
					}

					printf( '%s %s', $meta_markup, $postfix_markup );
					?>
                </div>
            </div>
        </div>
		<?php
	}
}

if ( ! function_exists( 'rhea_property_meta_icon' ) ) {
	/**
	 * Displays property meta icon based on field name.
	 *
	 * @since 2.3.0
	 *
	 * @param string $field_name      The name of the property field associated with the icon.
	 * @param string $icon_name       The name of the SVG icon to be displayed.
	 * @param bool   $floorplans_meta display floor plan meta icons
	 *
	 * @return void
	 */
	function rhea_property_meta_icon( $field_name, $icon_name, $floorplans_meta = false ) {
		if ( function_exists( 'realhomes_property_meta_icon_custom' ) && ! realhomes_property_meta_icon_custom( $field_name, $floorplans_meta ) ) {
			rhea_safe_include_svg( '/icons/' . $icon_name . '.svg' );
		}
	}
}

if ( ! function_exists( 'rhea_send_message_to_agent' ) ) {
	/**
	 * Handler for agent's contact form
	 */
	function rhea_send_message_to_agent() {
		if ( isset( $_POST['email'] ) ):
			/*
			 *  Verify Nonce
			 */
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'agent_message_nonce' ) ) {
				echo json_encode( array(
					'success' => false,
					'message' => ' <span class="rhea_error_log"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Security verification failed, please refresh the page and try again.', RHEA_TEXT_DOMAIN ) . '</span>',
				) );
				die;
			}

			/* Verify Google reCAPTCHA */
			ere_verify_google_recaptcha();

			/* Sanitize and Validate Target email address that is coming from agent form */
			$to_email = sanitize_email( $_POST['target'] );
			$to_email = is_email( $to_email );
			if ( ! $to_email ) {
				echo json_encode( array(
					'success' => false,
					'message' => ' <span class="rhea_error_log"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Target Email address is not properly configured!', RHEA_TEXT_DOMAIN ) . '</span>',
				) );
				die;
			}

			/* Sanitize and Validate contact form input data */
			$from_name  = sanitize_text_field( $_POST['name'] );
			$from_phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
			$message    = stripslashes( $_POST['message'] );

			/*
			 * From email
			 */
			$from_email = sanitize_email( $_POST['email'] );
			$from_email = is_email( $from_email );
			if ( ! $from_email ) {
				echo json_encode( array(
					'success' => false,
					'message' => ' <span class="rhea_error_log"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Provided Email address is invalid!', RHEA_TEXT_DOMAIN ) . ' </span>',
				) );
				die;
			}

			/*
			 * Email Subject
			 */
			$is_agency_form = ( isset( $_POST['form_of'] ) && 'agency' == $_POST['form_of'] );
			$form_of        = $is_agency_form ? esc_html__( 'using agency contact form at', RHEA_TEXT_DOMAIN ) : esc_html__( 'using agent contact form at', RHEA_TEXT_DOMAIN );
			$email_subject  = esc_html__( 'New message sent by', RHEA_TEXT_DOMAIN ) . ' ' . $from_name . ' ' . $form_of . ' ' . get_bloginfo( 'name' );

			/*
			 * Email body
			 */
			$email_body = array();

			if ( isset( $_POST['property_title'] ) ) {
				$property_title = sanitize_text_field( $_POST['property_title'] );
				if ( ! empty( $property_title ) ) {
					$email_body[] = array(
						'name'  => esc_html__( 'Property Title', RHEA_TEXT_DOMAIN ),
						'value' => $property_title,
					);
				}
			}

			if ( isset( $_POST['property_permalink'] ) ) {
				$property_permalink = esc_url( $_POST['property_permalink'] );
				if ( ! empty( $property_permalink ) ) {
					$email_body[] = array(
						'name'  => esc_html__( 'Property URL', RHEA_TEXT_DOMAIN ),
						'value' => $property_permalink,
					);
				}
			}

			$email_body[] = array(
				'name'  => esc_html__( 'Name', RHEA_TEXT_DOMAIN ),
				'value' => $from_name,
			);

			$email_body[] = array(
				'name'  => esc_html__( 'Email', RHEA_TEXT_DOMAIN ),
				'value' => $from_email,
			);

			if ( ! empty( $from_phone ) ) {
				$email_body[] = array(
					'name'  => esc_html__( 'Contact Number', RHEA_TEXT_DOMAIN ),
					'value' => $from_phone,
				);
			}

			$email_body[] = array(
				'name'  => esc_html__( 'Message', RHEA_TEXT_DOMAIN ),
				'value' => $message,
			);

			if ( '1' == get_option( 'inspiry_gdpr_in_email', '0' ) ) {
				$GDPR_agreement = $_POST['gdpr'];
				if ( ! empty( $GDPR_agreement ) ) {
					$email_body[] = array(
						'name'  => esc_html__( 'GDPR Agreement', RHEA_TEXT_DOMAIN ),
						'value' => $GDPR_agreement,
					);
				}
			}

			$email_body = ere_email_template( $email_body, 'agent_contact_form' );

			/*
			 * Email Headers ( Reply To and Content Type )
			 */
			$headers   = array();
			$headers[] = "Reply-To: $from_name <$from_email>";
			$headers[] = "Content-Type: text/html; charset=UTF-8";
			$headers   = apply_filters( "inspiry_agent_mail_header", $headers );    // just in case if you want to modify the header in child theme

			/* Send copy of message to admin if configured */
			$theme_send_message_copy = get_option( 'theme_send_message_copy', 'false' );
			if ( $theme_send_message_copy == 'true' ) {
				$cc_email = get_option( 'theme_message_copy_email' );
				$cc_email = explode( ',', $cc_email );
				if ( ! empty( $cc_email ) ) {
					foreach ( $cc_email as $ind_email ) {
						$ind_email = sanitize_email( $ind_email );
						$ind_email = is_email( $ind_email );
						if ( $ind_email ) {
							$headers[] = "Cc: $ind_email";
						}
					}
				}
			}

			if ( wp_mail( $to_email, $email_subject, $email_body, $headers ) ) {

				if ( '1' === get_option( 'ere_agency_form_webhook_integration', '0' ) && $is_agency_form ) {
					ere_forms_safe_webhook_post( $_POST, 'agency_contact_form' );
				} else if ( '1' === get_option( 'ere_agent_form_webhook_integration', '0' ) ) {
					ere_forms_safe_webhook_post( $_POST, 'agent_contact_form' );
				}

				echo json_encode( array(
					'success' => true,
					'message' => ' <span class="rhea_success_log"><i class="far fa-check-circle"></i> ' . esc_html__( 'Message Sent Successfully!', RHEA_TEXT_DOMAIN ) . '</span>',
				) );
			} else {
				echo json_encode( array(
						'success' => false,
						'message' => ' <span class="rhea_error_log"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Server Error: WordPress mail function failed!', RHEA_TEXT_DOMAIN ) . '</span>',
					)
				);
			}

		else:
			echo json_encode( array(
					'success' => false,
					'message' => ' <span class="rhea_error_log"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Invalid Request !', RHEA_TEXT_DOMAIN ) . '</span>',
				)
			);
		endif;

		do_action( 'inspiry_after_agent_form_submit' );

		die;
	}

	add_action( 'wp_ajax_nopriv_rhea_send_message_to_agent', 'rhea_send_message_to_agent' );
	add_action( 'wp_ajax_rhea_send_message_to_agent', 'rhea_send_message_to_agent' );
}

if ( ! function_exists( 'rhea_schedule_tour_form_mail' ) ) {
	/**
	 * Handler for schedule form email.
	 */
	function rhea_schedule_tour_form_mail() {

		if ( isset( $_POST['email'] ) ):

			// Verify Nonce.
			$nonce = $_POST['nonce'];
			if ( ! wp_verify_nonce( $nonce, 'schedule_tour_form_nonce' ) ) {
				echo json_encode( array(
					'success' => false,
					'message' => ' <label class="error"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Security verification failed, please refresh the page and try again.', RHEA_TEXT_DOMAIN ) . '</label>',
				) );
				die;
			}

			// Sanitize and Validate target email address that is coming from the form.
			$to_email = sanitize_email( $_POST['target'] );
			$to_email = is_email( $to_email );
			if ( ! $to_email ) {
				echo json_encode( array(
					'success' => false,
					'message' => ' <label class="error"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Target Email address is not properly configured!', RHEA_TEXT_DOMAIN ) . '</label>',
				) );
				die;
			}

			// Sanitize and validate form input data.
			$from_name    = sanitize_text_field( $_POST['name'] );
			$phone_number = sanitize_text_field( $_POST['phone_number'] );
			$date         = sanitize_text_field( $_POST['date'] );
			$message      = stripslashes( $_POST['message'] );

			// From email.
			$from_email = sanitize_email( $_POST['email'] );
			$from_email = is_email( $from_email );
			if ( ! $from_email ) {
				echo json_encode( array(
					'success' => false,
					'message' => ' <label class="error"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Provided Email address is invalid!', RHEA_TEXT_DOMAIN ) . ' </label>',
				) );
				die;
			}

			// Email Subject.
			$email_subject = esc_html__( 'New message sent by', RHEA_TEXT_DOMAIN ) . ' ' . $from_name . ' ' . esc_html__( 'using schedule tour form at', RHEA_TEXT_DOMAIN ) . ' ' . get_bloginfo( 'name' );

			// Email Body.
			$email_body = array();

			$email_body[] = array(
				'name'  => esc_html__( 'Name', RHEA_TEXT_DOMAIN ),
				'value' => $from_name,
			);

			$email_body[] = array(
				'name'  => esc_html__( 'Email', RHEA_TEXT_DOMAIN ),
				'value' => $from_email,
			);

			if ( ! empty( $phone_number ) ) {
				$email_body[] = array(
					'name'  => esc_html__( 'Phone Number', RHEA_TEXT_DOMAIN ),
					'value' => $phone_number,
				);
			}

			if ( ! empty( $date ) ) {
				$timestamp    = strtotime( $date );
				$email_body[] = array(
					'name'  => esc_html__( 'Desired Date & Time', RHEA_TEXT_DOMAIN ),
					'value' => date_i18n( get_option( 'date_format' ), $timestamp ) . ' ' . date_i18n( get_option( 'time_format' ), $timestamp ),
				);
			}

			$email_body[] = array(
				'name'  => esc_html__( 'Message', RHEA_TEXT_DOMAIN ),
				'value' => $message,
			);

			// Apply default emil template.
			$email_body = ere_email_template( $email_body, 'schedule_tour_form' );

			// Email Headers ( Reply To and Content Type ).
			$headers   = array();
			$headers[] = "Reply-To: $from_name <$from_email>";
			$headers[] = "Content-Type: text/html; charset=UTF-8";
			$headers   = apply_filters( "inspiry_schedule_tour_form_mail_header", $headers ); // just in case if you want to modify the header in child theme

			if ( wp_mail( $to_email, $email_subject, $email_body, $headers ) ) {
				echo json_encode( array(
					'success' => true,
					'message' => ' <label class="success"><i class="far fa-check-circle"></i> ' . esc_html__( 'Message Sent Successfully!', RHEA_TEXT_DOMAIN ) . '</label>',
				) );
			} else {
				echo json_encode( array(
						'success' => false,
						'message' => ' <label class="error"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Server Error: WordPress mail function failed!', RHEA_TEXT_DOMAIN ) . '</label>',
					)
				);
			}

		else:
			echo json_encode( array(
					'success' => false,
					'message' => ' <label class="error"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Invalid Request !', RHEA_TEXT_DOMAIN ) . '</label>',
				)
			);
		endif;

		die;
	}

	add_action( 'wp_ajax_nopriv_rhea_schedule_tour_form_mail', 'rhea_schedule_tour_form_mail' );
	add_action( 'wp_ajax_rhea_schedule_tour_form_mail', 'rhea_schedule_tour_form_mail' );
}

if ( ! function_exists( 'rhea_safe_include_svg' ) ) {
	/**
	 * Safely includes or returns an SVG file from the specified path.
	 *
	 * @since 0.7.2
	 *
	 * @param string $file The SVG file name.
	 * @param string $path (Optional) The directory path; defaults to RHEA_ASSETS_DIR.
	 * @param bool   $echo (Optional) Whether to echo or return the SVG content. Default: true (echo).
	 *
	 * @return string|null SVG content if $echo is false, otherwise it outputs the SVG.
	 */
	function rhea_safe_include_svg( $file, $path = RHEA_ASSETS_DIR, $echo = true ) {
		$file_path = trailingslashit( $path ) . ltrim( $file, '/' );

		if ( file_exists( $file_path ) ) {
			$svg = file_get_contents( $file_path );

			// Ensure it's valid SVG before outputting.
			if ( strpos( $svg, '<svg' ) !== false ) {
				if ( $echo ) {
					echo $svg;
					return null;
				}
				return $svg;
			}
		}

		return null; // Return null if file doesn't exist or is invalid.
	}
}


if ( ! function_exists( 'rhea_lightbox_data_attributes' ) ) {

	function rhea_lightbox_data_attributes( $widget_id, $post_id, $classes = '' ) {

		$REAL_HOMES_property_map = get_post_meta( $post_id, 'REAL_HOMES_property_map', true );
		$property_location       = get_post_meta( $post_id, 'REAL_HOMES_property_location', true );

		if ( has_post_thumbnail() ) {
			$image_id         = get_post_thumbnail_id();
			$image_attributes = wp_get_attachment_image_src( $image_id, 'property-thumb-image' );
			if ( ! empty( $image_attributes[0] ) ) {
				$current_property_data = $image_attributes[0];
			}
		} else {
			$current_property_data = get_inspiry_image_placeholder_url( 'property-thumb-image' );
		}

		if ( ! empty( $property_location ) && $REAL_HOMES_property_map !== '1' ) {
			?>
            class="rhea_trigger_map rhea_facnybox_trigger-<?php echo esc_attr( $widget_id . ' ' . $classes ); ?>" data-rhea-map-source="rhea-map-source-<?php echo esc_attr( $widget_id ); ?>" data-rhea-map-location="<?php echo esc_attr( $property_location ); ?>" data-rhea-map-title="<?php echo esc_attr( get_the_title() ); ?>" data-rhea-map-price="<?php echo esc_attr( ere_get_property_price() ); ?>" data-rhea-map-thumb="<?php echo esc_attr( $current_property_data ) ?>"
			<?php
		}
	}
}

if ( ! function_exists( 'rhea_get_available_menus' ) ) {
	/**
	 * Get Available Menus
	 *
	 * @since 0.9.7
	 *
	 * @return array
	 */
	function rhea_get_available_menus() {
		$available_menues = wp_get_nav_menus();
		$options          = array();
		if ( ! empty( $available_menues ) ) {
			foreach ( $available_menues as $menu ) {
				$options[ $menu->slug ] = $menu->name;
			}
		}

		return $options;
	}
}

/**
 * Process additional fields for property elementor widgets
 * This method is generating HTML and returns nothing.
 *
 * @since 0.9.9
 *
 * @param int    $property_id
 * @param string $type
 * @param string $variation
 *
 * @return void
 */
function rhea_property_widgets_additional_fields( int $property_id, string $type = 'all', string $variation = 'modern' ) {
	/**
	 * Add property additional fields to the property listing cards
	 */
	$additional_fields = rhea_get_additional_fields( $type );

	if ( ! empty( $additional_fields ) ) {
		foreach ( $additional_fields as $field ) {
			$single_value = true;

			if ( 'checkbox_list' == $field['field_type'] ) {
				$single_value = false;
			}

			$value = get_post_meta( $property_id, $field['field_key'], $single_value );
			if ( ! empty( $value ) ) {

				if ( is_array( $value ) ) {
					$value = implode( ', ', $value );
				}

				if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
					$field['field_name'] = apply_filters( 'wpml_translate_single_string', $field['field_name'], 'Additional Fields', $field['field_name'] . ' Label', ICL_LANGUAGE_CODE );
				}

				if ( $variation == 'classic' ) {
					?>
                    <div class="rhea_meta_wrapper additional-field-wrap">
                        <div class="rhea_meta_wrapper_inner">
							<?php
							if ( ! empty ( $field['field_icon'] ) ) {
								?>
                                <i class="<?php echo esc_attr( $field['field_icon'] ); ?>" aria-hidden="true"></i>
								<?php
							}
							?>
                            <span class="figure">
                                <span class="figure"><?php echo esc_html( $value ); ?></span>
                                <?php
                                if ( $field['field_name'] ) {
	                                ?>
                                    <span class="rhea_meta_titles"><?php echo esc_html( $field['field_name'] ); ?></span>
	                                <?php
                                }
                                ?>
                            </span>
                        </div>
                    </div>
					<?php
				} else {
					?>
                    <div class="rh_prop_card__meta additional-field">
						<?php
						if ( $field['field_name'] ) {
							?>
                            <span class="rhea_meta_titles"><?php echo esc_html( $field['field_name'] ); ?></span>
							<?php
						}
						?>
                        <div class="rhea_meta_icon_wrapper">
							<?php
							if ( ! empty ( $field['field_icon'] ) ) {
								?>
                                <i class="<?php echo esc_attr( $field['field_icon'] ); ?>" aria-hidden="true"></i>
								<?php
							}
							?>
                            <span class="figure"><?php echo esc_html( $value ); ?></span>
                        </div>
                    </div>
					<?php
				}
			}
		}
	}
}

/**
 * Return a valid list of property additional fields
 *
 * @since 0.9.9
 *
 * @param string $location
 *
 * @return array $build_fields
 */
function rhea_get_additional_fields( string $location = 'all' ): array {

	$additional_fields = get_option( 'inspiry_property_additional_fields' );
	$build_fields      = array();

	if ( ! empty( $additional_fields['inspiry_additional_fields_list'] ) ) {
		foreach ( $additional_fields['inspiry_additional_fields_list'] as $field ) {

			// Ensure all required values of a field are available then add it to the fields list
			if ( ( $location == 'all' || ( ! empty( $field['field_display'] ) && in_array( $location, $field['field_display'] ) ) ) && ! empty( $field['field_type'] ) && ! empty( $field['field_name'] ) ) {

				// Prepare select field options list
				if ( in_array( $field['field_type'], array( 'select', 'checkbox_list', 'radio' ) ) ) {
					if ( empty( $field['field_options'] ) ) {
						$field['field_type'] = 'text';
					} else {
						$options                = explode( ',', $field['field_options'] );
						$options                = array_filter( array_map( 'trim', $options ) );
						$field['field_options'] = array_combine( $options, $options );
					}
				}

				// Set the field icon and unique key
				$field['field_icon'] = empty( $field['field_icon'] ) ? '' : $field['field_icon'];
				$field['field_key']  = 'inspiry_' . strtolower( preg_replace( '/\s+/', '_', $field['field_name'] ) );

				// Add final field to the fields list
				$build_fields[] = $field;
			}
		}
	}

	// Return additional fields array
	return $build_fields;
}

if ( ! function_exists( 'rhea_get_sample_property_id' ) ) {
	/**
	 * Return Sample Property ID to be used for Single Property Elementor Design from Editor Side
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	function rhea_get_sample_property_id() {
		if ( is_singular( 'property' ) ) {
			return 0;
		}
		$sample_id = get_option( 'realhomes_sample_property_id' );
		if ( ! empty( $sample_id ) ) {
			return (int)$sample_id;
		} else {
			$first_post = get_posts( [
				'post_type'      => 'property',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'ASC',
				'fields'         => 'ids'
			] )[0] ?? null;

			return (int)$first_post;
		}
	}
}

if ( ! function_exists( 'rhea_get_sample_agent_id' ) ) {
	/**
	 * Return Sample Agent ID to be used for Single Agent Elementor Design from Editor Side
	 *
	 * @since 2.3.0
	 *
	 * @return int
	 */
	function rhea_get_sample_agent_id() {
		return (int)get_option( 'realhomes_sample_agent_id', '' );
	}
}

if ( ! function_exists( 'rhea_is_preview_mode' ) ) {
	/**
	 * Check if screen is in Elementor preview mode
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	function rhea_is_preview_mode() {

		if ( class_exists( '\Elementor\Plugin' ) ) {

			// TODO: This return type is generating error on 404 and archives (pages with no id assigned) need to discuss and improve this one
			if ( 0 < get_the_ID() ) {
				return \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor();
			}

			return false;
		}

		return false;
	}
}

if ( ! function_exists( 'rhea_print_no_result' ) ) {
	/**
	 * Print HTML when no results found
	 *
	 * @since 2.1.0
	 *
	 * @param string $custom_no_result_text
	 */
	function rhea_print_no_result( $custom_no_result_text = '' ) {

		$no_result = esc_html__( 'No Information Added! Please Edit Property To Add Information.', RHEA_TEXT_DOMAIN );
		if ( ! empty( $custom_no_result_text ) ) {
			$no_result = esc_html( $custom_no_result_text );
		}
		?>
        <p class="rhea-no-results"><i class="fas fa-exclamation-triangle"></i> <?php echo esc_html( $no_result ); ?></p>
		<?php

	}
}

if ( ! function_exists( 'rhea_print_no_result_for_editor' ) ) {
	/**
	 * Print HTML when no results found in Elementor Editor
	 *
	 * @since 2.1.0
	 *
	 * @param string $custom_no_result_text
	 */
	function rhea_print_no_result_for_editor( $custom_no_result_text = '' ) {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
            <div class="rhea-section-editor-class">
				<?php rhea_print_no_result( $custom_no_result_text ); ?>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'rhea_is_rvr_enabled' ) ) {
	/**
	 * Check if Realhomes Vacation Rentals plugin is activated and enabled
	 *
	 * @since      2.2.0
	 *
	 * @return bool
	 */
	function rhea_is_rvr_enabled() {
		$rvr_settings = get_option( 'rvr_settings' );
		$rvr_enabled  = isset( $rvr_settings['rvr_activation'] ) ? $rvr_settings['rvr_activation'] : false;

		if ( $rvr_enabled && class_exists( 'Realhomes_Vacation_Rentals' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'rhea_prepare_map_data' ) ) {
	/**
	 * Prepares data for displaying properties on maps.
	 *
	 * This function queries and prepares property data for display on maps. It retrieves property information
	 * such as title, classes, property type, price, URL, location, thumbnail, and map icon.
	 *
	 * @since 2.3.0
	 *
	 * @param array $properties_args The arguments for the properties query.
	 * @param bool  $ajax_request    Whether the function is called during an AJAX request.
	 *
	 * @return array|null If $ajax_request is true, returns the array of properties data; otherwise, localizes the data for use in JavaScript.
	 */
	function rhea_prepare_map_data( $properties_args, $ajax_request = false ) {
		$map_properties_query = new WP_Query( $properties_args );

		// Initialize an array to store map data.
		$properties_map_data = array();

		if ( $map_properties_query->have_posts() ) {
			while ( $map_properties_query->have_posts() ) {
				$map_properties_query->the_post();

				$property_id                    = get_the_ID();
				$current_property_data          = array();
				$current_property_data['title'] = get_the_title();

				// Retrieve and add property classes based on taxonomies.
				$get_post_taxonomies = get_post_taxonomies( $property_id );
				foreach ( $get_post_taxonomies as $taxonomy ) {
					$get_the_terms = get_the_terms( $property_id, $taxonomy );
					if ( is_array( $get_the_terms ) || is_object( $get_the_terms ) ) {
						foreach ( $get_the_terms as $term ) {
							$current_property_data['classes'][] = 'rhea-' . $term->slug;
						}
					}
				}

				// Retrieve property type.
				if ( function_exists( 'ere_get_property_types' ) ) {
					$current_property_data['propertyType'] = ere_get_property_types( $property_id );
				} else {
					$current_property_data['propertyType'] = null;
				}

				// Retrieve property price.
				if ( function_exists( 'ere_get_property_price' ) ) {
					$current_property_data['price'] = ere_get_property_price();
				} else {
					$current_property_data['price'] = null;
				}

				// Retrieve property URL.
				$current_property_data['url'] = get_permalink();

				// Retrieve and parse property location.
				$property_location = get_post_meta( $property_id, 'REAL_HOMES_property_location', true );
				if ( ! empty( $property_location ) ) {
					$lat_lng                      = explode( ',', $property_location );
					$current_property_data['lat'] = $lat_lng[0];
					$current_property_data['lng'] = $lat_lng[1];
				}

				// Retrieve and set property thumbnail.
				if ( has_post_thumbnail() ) {
					$image_id         = get_post_thumbnail_id();
					$image_attributes = wp_get_attachment_image_src( $image_id, 'property-thumb-image' );
					if ( ! empty( $image_attributes[0] ) ) {
						$current_property_data['thumb'] = $image_attributes[0];
					}
				}

				// Retrieve and set property map icon based on property type.
				$type_terms = get_the_terms( $property_id, 'property-type' );
				if ( $type_terms && ! is_wp_error( $type_terms ) ) {
					foreach ( $type_terms as $type_term ) {
						$icon_id = get_term_meta( $type_term->term_id, 'inspiry_property_type_icon', true );
						if ( ! empty( $icon_id ) ) {
							$icon_url = wp_get_attachment_url( $icon_id );
							if ( $icon_url ) {
								$current_property_data['icon'] = esc_url( $icon_url );

								// Retrieve and set retina icon.
								$retina_icon_id = get_term_meta( $type_term->term_id, 'inspiry_property_type_icon_retina', true );
								if ( ! empty( $retina_icon_id ) ) {
									$retina_icon_url = wp_get_attachment_url( $retina_icon_id );
									if ( $retina_icon_url ) {
										$current_property_data['retinaIcon'] = esc_url( $retina_icon_url );
									}
								}
								break;
							}
						}
					}
				}

				if ( ! isset( $current_property_data['icon'] ) ) {
					$current_property_data['icon']       = INSPIRY_DIR_URI . '/images/map/single-family-home-map-icon.png';     // default icon
					$current_property_data['retinaIcon'] = INSPIRY_DIR_URI . '/images/map/single-family-home-map-icon@2x.png';  // default retina icon
				}

				$properties_map_data[] = $current_property_data;
			}

			wp_reset_postdata();

			if ( $ajax_request ) {
				return $properties_map_data;
			}

			// Localize the map data for use in JavaScript.
			$localize_map_data = array(
				'rheaPropertiesData' => json_encode( $properties_map_data ),
				'rheaPropertiesArgs' => json_encode( $properties_args ),
			);

			wp_localize_script( 'rhea-properties-google-map-js', 'propertiesMapNewData', $localize_map_data );
			wp_localize_script( 'rhea-open-street-map', 'propertiesMapNewData', $localize_map_data );
			wp_localize_script( 'rhea-mapbox-script', 'propertiesMapNewData', $localize_map_data );
		}
	}
}

if ( ! function_exists( 'rhea_ajax_prepare_map_data' ) ) {
	/**
	 * Prepares data for displaying properties on maps for AJAX requests.
	 *
	 * This function is an AJAX handler that prepares property data for display on maps during AJAX requests.
	 * It retrieves and processes the properties arguments sent via POST, calls rhea_prepare_map_data function
	 * to get the map data, and sends a JSON response using wp_send_json_success.
	 *
	 * @since 2.3.0
	 */
	function rhea_ajax_prepare_map_data() {
		$properties_args = array();

        if ( isset( $_POST['filterValues'] ) ) {
	        $properties_args = rhea_prepare_filter_widget_args( $_POST['filterValues'] );
        } else if ( ! empty( $_POST['properties_args'] ) ) {
			$properties_args = $_POST['properties_args'];

			// Check if paged parameter is set and assign it to properties_args if available.
			if ( ! empty( $_POST['paged'] ) ) {
				$properties_args['paged'] = $_POST['paged'];
			}
		}

		// Call the rhea_prepare_map_data function with $ajax_request set to true.
		$properties_map_data = rhea_prepare_map_data( $properties_args, true );

		// Send a JSON success response with the prepared map data.
		wp_send_json_success( $properties_map_data );
	}

	// Add the AJAX action hooks.
	add_action( 'wp_ajax_rhea_map_properties_data', 'rhea_ajax_prepare_map_data' );
	add_action( 'wp_ajax_nopriv_rhea_map_properties_data', 'rhea_ajax_prepare_map_data' );
}

if ( ! function_exists( 'rhea_get_additional_search_fields' ) ) {
	/**
	 * Retrieve additional search fields.
	 *
	 * @since 2.3.0
	 *
	 * @return array An array of additional search fields.
	 */
	function rhea_get_additional_search_fields() {
		$fields            = array();
		$additional_fields = get_option( 'inspiry_property_additional_fields' );

		// If there are no additional fields configured, return empty array
		if ( empty( $additional_fields['inspiry_additional_fields_list'] ) ) {
			return $fields;
		}

		foreach ( $additional_fields['inspiry_additional_fields_list'] as $field ) {
			if ( ! empty( $field['field_display'] ) && in_array( 'search', $field['field_display'] ) && ! empty( $field['field_type'] ) && ! empty( $field['field_name'] ) ) {

				// Prepare select field options list
				if ( in_array( $field['field_type'], array( 'select', 'checkbox_list', 'radio' ) ) ) {
					if ( empty( $field['field_options'] ) ) {
						$field['field_type'] = 'text';
					} else {
						$options                = explode( ',', $field['field_options'] );
						$options                = array_filter( array_map( 'trim', $options ) );
						$field['field_options'] = array_combine( $options, $options );
					}
				}

				// Set the field unique key
				$field['field_key'] = 'inspiry_' . strtolower( preg_replace( '/\s+/', '_', $field['field_name'] ) );

				// Add the processed field to the fields array
				$fields[] = $field;
			}
		}

		return $fields;
	}
}

if ( ! function_exists( 'rhea_search_form_fields' ) ) {
	/**
	 * Retrieves an array of search form fields.
	 *
	 * @since 2.3.0
	 *
	 * @param bool $modern_variation Whether to include modern variation of search fields. Default is false.
	 *
	 * @return array An associative array of search form fields.
	 */
	function rhea_search_form_fields( $modern_variation = false ) {
		$search_fields = array(
			'location'         => esc_html__( 'Property Location', RHEA_TEXT_DOMAIN ),
			'status'           => esc_html__( 'Property Status', RHEA_TEXT_DOMAIN ),
			'type'             => esc_html__( 'Property Type', RHEA_TEXT_DOMAIN ),
			'min-max-price'    => esc_html__( 'Min and Max Price', RHEA_TEXT_DOMAIN ),
			'min-beds'         => esc_html__( 'Min Beds', RHEA_TEXT_DOMAIN ),
			'min-baths'        => esc_html__( 'Min Baths', RHEA_TEXT_DOMAIN ),
			'min-garages'      => esc_html__( 'Min Garages', RHEA_TEXT_DOMAIN ),
			'agency'           => esc_html__( 'Agency', RHEA_TEXT_DOMAIN ),
			'agent'            => esc_html__( 'Agent', RHEA_TEXT_DOMAIN ),
			'min-max-area'     => esc_html__( 'Min and Max Area', RHEA_TEXT_DOMAIN ),
			'min-max-lot-size' => esc_html__( 'Min and Max Lot Size', RHEA_TEXT_DOMAIN ),
			'keyword-search'   => esc_html__( 'Keyword Search', RHEA_TEXT_DOMAIN ),
			'property-id'      => esc_html__( 'Property ID', RHEA_TEXT_DOMAIN ),
		);

		if ( $modern_variation ) {
			$search_fields['property-features-dropdown'] = esc_html__( 'Features', RHEA_TEXT_DOMAIN );
		}

		if ( rhea_is_rvr_enabled() ) {
			$search_fields['check-in-out'] = esc_html__( 'Check In/Out', RHEA_TEXT_DOMAIN );
			$search_fields['guests']       = esc_html__( 'Guests', RHEA_TEXT_DOMAIN );
		}

		$additional_fields = rhea_get_additional_search_fields();
		if ( ! empty( $additional_fields ) ) {
			foreach ( $additional_fields as $field ) {
				$field_name = $field['field_name'];

				if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
					$field_name = apply_filters( 'wpml_translate_single_string', $field_name, 'Additional Fields', $field_name . ' Label', ICL_LANGUAGE_CODE );
				}

				$search_fields[ $field['field_key'] ] = $field_name;
			}
		}

		if ( function_exists( 'inspiry_get_maps_type' ) && 'google-maps' === inspiry_get_maps_type() ) {
			$search_fields['radius-search'] = esc_html__( 'Radius Search Slider', RHEA_TEXT_DOMAIN );
		}

		return apply_filters( 'rhea_sort_search_fields', $search_fields );
	}
}

if ( ! function_exists( 'rhea_get_wpml_translated_image_id' ) ) {
	/**
	 * Get WPML translated image ID
	 *
	 * @since 2.3.1
	 *
	 * @param int $id attachment ID
	 *
	 * @return int Translated Image ID
	 */
	function rhea_get_wpml_translated_image_id( $id ) {
		$current_language = apply_filters( 'wpml_current_language', null );

		return apply_filters( 'wpml_object_id', $id, 'attachment', false, $current_language );
	}
}

if ( ! function_exists( 'rhea_wpml_is_active' ) ) {
	/**
	 * Check if WPML is active and not admin
	 *
	 * @since 2.3.1
	 */
	function rhea_wpml_is_active(): bool {
		return ( class_exists( 'SitePress' ) && ! is_admin() );
	}
}

if ( ! function_exists( 'rhea_bulk_settings_check' ) ) {
	/**
	 * This function checks many keys against single single yes/no option with or/and argument types
	 * It is made to minimize the code in case many options need to be checked against similar value
	 *
	 * @since 2.3.2
	 *
	 * @param array  $settings elementor widget settings array
	 * @param array  $keys     array of keys for $settings variable
	 * @param string $values   value all keys are being tested against
	 * @param string $type     options: (and, or)
	 *
	 */
	function rhea_bulk_settings_check( $settings, $keys, $value, $type = 'and' ) {
		if ( ! is_array( $settings ) || ! is_array( $keys ) ) {
			return false;
		}

		$result = false;

		foreach ( $keys as $key ) {
			if ( ! empty( $settings[ $key ] ) && $settings[ $key ] === $value ) {
				$result = true;

				if ( $type === 'or' ) {
					break;
				}
			} else {
				$result = false;
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'rhea_display_widget_card_agent' ) ) {
	/**
	 * Displays the agent card in the widget.
	 *
	 * This function outputs the agent's profile image, title, and optionally a verification badge
	 * based on the settings and arguments passed.
	 *
	 * @since 2.4.0
	 *
	 * @param array $args     Arguments for the agent, including agent ID, email, title, and image.
	 * @param array $settings Settings for the display, such as showing the verification badge.
	 */
	function rhea_display_widget_card_agent( $args, $settings ) {
		$display_verification_badge = $settings['show_verification_badge'];
		$verification_status        = get_post_meta( $args['agent_id'], 'ere_agent_verification_status', true );

		if ( ! empty( $args['display_author'] ) ) {
			if ( isset( $args['profile_image_id'] ) && ( 0 < $args['profile_image_id'] ) ) {
				?>
                <a class="agent-image" href="<?php echo esc_url( get_author_posts_url( $args['agent_id'] ) ); ?>">
					<?php echo wp_get_attachment_image( $args['profile_image_id'], 'agent-image' ); ?>
                </a>
				<?php
			} else if ( isset( $args['agent_email'] ) ) {
				?>
                <a class="agent-image" href="<?php echo esc_url( get_author_posts_url( $args['agent_id'] ) ); ?>">
					<?php echo get_avatar( $args['agent_email'], '210' ); ?>
                </a>
				<?php
			}
		} else {
			if ( isset( $args['agent_id'] ) && has_post_thumbnail( $args['agent_id'] ) ) {
				?>
                <a class="agent-image" href="<?php echo esc_url( get_permalink( $args['agent_id'] ) ); ?>">
					<?php echo get_the_post_thumbnail( $args['agent_id'], 'agent-image' ); ?>
                </a>
				<?php
			}
		}

		if ( ! empty( $args['agent_title_text'] ) ) {

			if ( ! empty( $args['display_author'] ) ) {
				?>
                <a class="agent-title" href="<?php echo esc_url( get_author_posts_url( $args['agent_id'] ) ); ?>">
					<?php echo esc_html( $args['agent_title_text'] ); ?>
                </a>
				<?php
			} else {
				if ( ! empty( $args['agent_id'] ) ) {
					?>
                    <a class="agent-title" href="<?php echo esc_url( get_permalink( $args['agent_id'] ) ); ?>">
						<?php echo esc_html( $args['agent_title_text'] ); ?>
                    </a>
					<?php
					if ( 'yes' === $display_verification_badge && $verification_status ) {
						?>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="verification-badge" d="M12.3756 0.274902L15.9526 3.17894L20.5353 3.65479L21.0112 8.23756L23.9152 11.8145L21.0112 15.3915L20.5353 19.9743L15.9526 20.4502L12.3756 23.3542L8.7986 20.4502L4.21582 19.9743L3.73998 15.3915L0.835938 11.8145L3.73998 8.23756L4.21582 3.65479L8.7986 3.17894L12.3756 0.274902Z" fill="#43BBED" />
                            <path class="verified-sign" d="M8.04834 11.8965L10.8778 14.699L16.7031 8.9292" stroke="white" stroke-width="1.2147" stroke-linecap="round" />
                        </svg>
						<?php
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'rhea_widget_card_agent' ) ) {
	/**
	 * Outputs the agent card for a property in the widget.
	 *
	 * This function determines the display option for the agent associated with a property,
	 * and calls the appropriate method to display the agent card.
	 *
	 * @since 2.4.0
	 *
	 * @param int   $property_id The ID of the property.
	 * @param array $args        Optional. Additional arguments for customization, including settings for displaying the agent.
	 */
	function rhea_widget_card_agent( $property_id, $args = array() ) {

		$agent_display_option = get_post_meta( $property_id, 'REAL_HOMES_agent_display_option', true );
		if ( 'none' === $agent_display_option ) {
			return false;
		}

		$settings = $args['settings'];

		if ( 'my_profile_info' === $agent_display_option ) {
			$profile_args                     = array();
			$profile_args['agent_id']         = $profile_args['display_author'] = true;
			$profile_args['author_id']        = get_the_author_meta( 'ID' );
			$profile_args['agent_title_text'] = get_the_author_meta( 'display_name' );
			$profile_args['profile_image_id'] = intval( get_the_author_meta( 'profile_image_id' ) );

			rhea_display_widget_card_agent( $profile_args, $settings );

		} else {
			$property_agent = get_post_meta( $property_id, 'REAL_HOMES_agents', true );

			if ( ! empty( $property_agent ) ) {
				$agent_args                     = array();
				$agent_args['agent_id']         = intval( $property_agent );
				$agent_args['agent_title_text'] = get_the_title( $agent_args['agent_id'] );

				rhea_display_widget_card_agent( $agent_args, $settings );
			}
		}
	}
}

if ( ! function_exists( 'rhea_properties_widget_v14_card' ) ) {
	/**
	 * Displays a property card for the widget V14.
	 *
	 * This function generates the HTML markup for an individual property card in a property listing widget V14.
	 * It outputs the property thumbnail, title, address, meta information (like bed, bath, etc.), and agent info
	 * with support for additional features such as featured properties and slider integration.
	 *
	 * @since 2.4.0
	 *
	 * @param int   $property_id               The ID of the property post.
	 * @param array $args                      {
	 *                                         Optional arguments.
	 *
	 * @type string $is_rvr                    Indicates if the property is a vacation rental (default: 'false').
	 * @type string $slide                     Indicates if the card is displayed in a slider (default: 'false').
	 * @type array  $settings                  {
	 *                                         Additional settings.
	 * @type string $thumbnail_size            The size of the property image thumbnail.
	 * @type string $show_address              Whether to show the property address (default: 'yes').
	 * @type string $show_agent                Whether to show the property agent (default: 'yes').
	 * @type string $property_added_date_label Custom label for the property added date.
	 * @type string $show_old_price            Whether to display the old price (default: 'no').
	 *                                         }
	 *                                         }
	 */
	function rhea_properties_widget_v14_card( $property_id, $args = array() ) {
		global $settings, $widget_id;

		$defaults = array(
			'is_rvr'   => 'false',
			'slide'    => 'false',
			'layout'   => 'carousel',
			'settings' => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$settings       = $args['settings'];
		$layout         = $args['layout'];
		$property_image = ! empty( $settings['thumbnail_size'] ) ? $settings['thumbnail_size'] : 'modern-property-child-slider';
		$swiper_slide   = ( 'true' === $args['slide'] ) ? 'swiper-slide' : '';
		$property_link  = get_the_permalink( $property_id );
		?>
        <div class="rh-property-card-v14-wrapper <?php echo esc_attr( $swiper_slide ); ?>">
            <article class="rh-property-card-v14">
                <figure class="rh-property-thumb-wrapper rhea-trigger-animate">
					<?php rhea_render_properties_tags( $property_id, $settings, 'rhea-animate-item top deanimate-top-sm-devices' ); ?>
					<?php
					if ( in_array( $layout, array( 'carousel', 'grid' ) ) ) {
						?>
                        <a href="<?php echo esc_url( $property_link ); ?>">
							<?php
							if ( has_post_thumbnail( $property_id ) ) {
								the_post_thumbnail( $property_image );
							} else {
								inspiry_image_placeholder( $property_image );
							}
							?>
                        </a>
						<?php
					} else {
						if ( has_post_thumbnail( $property_id ) ) {
							$property_thumbnail = get_the_post_thumbnail_url( $property_id, $property_image );
						} else {
							$property_thumbnail = inspiry_get_raw_placeholder_url( $property_image );
						}

						printf( '<a href="%s" style="background-image: url(%s);"></a>', esc_url( $property_link ), $property_thumbnail );
					}

                    if ( ! empty( $settings['show_views_count'] ) && 'yes' === $settings['show_views_count'] && 'enabled' === get_option( 'inspiry_property_analytics_status', 'disabled' ) ) {
	                    ?>
                        <span class="property-views">
                            <?php
                            rhea_safe_include_svg( '/icons/views-eye.svg' );
                            echo esc_html( rhea_get_total_property_views( $property_id ) );
                            ?>
                        </span>
	                    <?php
                    }
					?>
                </figure>
                <div class="rh-property-card-v14-inner">
                    <div class="rh-property-content-wrapper">
                        <header class="rh-property-card-v14-header">
                            <h3 class="property-title">
                                <a href="<?php echo esc_url( $property_link ); ?>"><?php the_title(); ?></a>
                            </h3>

							<?php
							if ( 'list' === $layout ) {
								$show_status = ( 'yes' == $settings['show_property_status'] );
								$show_type   = ( 'yes' == $settings['show_property_type'] );

								if ( $show_status || $show_type ) {
									?>
                                    <div class="property-type-and-status-tags">
										<?php
										if ( $show_status ) {
											$property_status = get_the_terms( $property_id, 'property-status' );
											if ( ! empty( $property_status ) && ! is_wp_error( $property_status ) ) {
												?>
                                                <a href="<?php echo get_term_link( $property_status[0]->term_id ); ?>"><?php echo esc_html( $property_status[0]->name ); ?></a>
												<?php
											}
										}

										if ( $show_type ) {
											$property_type = get_the_terms( $property_id, "property-type" );
											if ( ! empty( $property_type ) && ! is_wp_error( $property_type ) ) {
												?>
                                                <a href="<?php echo get_term_link( $property_type[0]->term_id ); ?>"><?php echo esc_html( $property_type[0]->name ); ?></a>
												<?php
											}
										}
										?>
                                    </div>
									<?php
								}
							}
							?>
                        </header>

                        <div class="rh-property-address-wrapper">
							<?php
							$is_address = ! empty( $settings['show_address'] ) && 'yes' == $settings['show_address'];
							if ( $is_address ) {
								?>
                                <svg class="pin-icon" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.3455 9.15338C10.6798 8.33298 10.8621 7.69489 10.8621 7.20873C10.8621 4.53485 8.67437 2.34713 6.00049 2.34713C3.29622 2.34713 1.13888 4.53485 1.13888 7.20873C1.13888 7.69489 1.29081 8.33298 1.62504 9.15338C1.95928 9.94339 2.41505 10.7942 2.96198 11.6449C3.99508 13.3161 5.21048 14.9265 5.9701 15.8989C6.76011 14.9265 7.97552 13.3161 9.00861 11.6449C9.55554 10.7942 10.0113 9.94339 10.3455 9.15338ZM6.69934 16.5673C6.33472 17.0231 5.63587 17.0231 5.27125 16.5673C3.72161 14.5923 0.16656 9.88262 0.16656 7.20873C0.16656 3.98792 2.77967 1.37481 6.00049 1.37481C9.2213 1.37481 11.8344 3.98792 11.8344 7.20873C11.8344 9.88262 8.27937 14.5923 6.69934 16.5673Z" />
                                    <circle cx="6.12143" cy="6.81479" r="2.12143" />
                                </svg>
								<?php
							}
							?>
                            <div class="rh-property-address-inner">
								<?php
								if ( $is_address ) {
									$property_address = get_post_meta( $property_id, 'REAL_HOMES_property_address', true );

									if ( ! empty( $property_address ) ) {
										?>
                                        <address class="rh-property-address">
											<?php echo esc_html( $property_address ); ?>
                                        </address>
										<?php
									}
								}
								?>
                                <p class="rh-property-added-date">
									<?php
									if ( ! empty( $settings['property_added_date_label'] ) ) {
										echo '<span class="added-title">' . esc_html( $settings['property_added_date_label'] ) . '</span> ';
									}

									echo get_the_date();
									?>
                                </p>
                            </div>
                        </div>

						<?php rhea_get_template_part( 'assets/partials/ultra/grid-card-meta' ); ?>

                        <div class="rh-property-more-info">
							<?php
							if ( ! empty( $settings['show_agent'] ) && 'yes' === $settings['show_agent'] ) {
								?>
                                <div class="rh-property-agent">
									<?php
									if ( true === $args['is_rvr'] ) {
										$property_owner = get_post_meta( $property_id, 'rvr_property_owner', true );
										if ( ! empty( $property_owner ) ) {
											if ( has_post_thumbnail( $property_owner ) ) {
												?>
                                                <span class="agent-image">
                                                    <?php
                                                    echo get_the_post_thumbnail( $property_owner, 'agent-image' );
                                                    realhomes_verification_badge( 'owner', $property_owner );
                                                    ?>
                                                </span>
												<?php
											}
											?>
                                            <span class="agent-title owner-title"><?php echo get_the_title( $property_owner ); ?></span>
											<?php
										}
									} else {
										rhea_widget_card_agent( $property_id, $args );
									}
									?>
                                </div>
								<?php
							}

							if ( function_exists( 'ere_property_price' ) ) {
								?>
                                <div class="rh-property-price-group">
                                    <?php
                                    $show_old_price = ( ! empty( $settings['show_old_price'] ) && 'yes' === $settings['show_old_price'] );
                                    echo ere_get_property_price( $property_id, $show_old_price, true );
                                    ?>
                                </div>
								<?php
							}
							?>
                        </div>
                    </div>
                    <footer class="rh-property-actions">
						<?php
						rhea_render_favourite_button( $property_id, $settings, '/common/images/icons/ultra-favourite.svg', 'ultra' );
						rhea_render_compare_button( $property_id, $settings );
						rhea_render_media_count_button( $property_id, $widget_id, $settings, true );
						?>
                    </footer>
                </div>
            </article>
        </div>
		<?php
	}
}

if ( ! function_exists( 'rhea_properties_widget_v14_properties_filter' ) ) {
	/**
	 * Handles AJAX requests to filter and display properties.
	 *
	 * This function processes AJAX requests for filtering properties based on taxonomy and term values.
	 * It retrieves properties from the `property` post type using specific query parameters (e.g., taxonomy term, ordering).
	 * If properties are found, it returns the HTML markup for those properties; otherwise, it shows a 'no results' message.
	 *
	 * @since 2.4.0
	 *
	 * @return void
	 */
	function rhea_properties_widget_v14_properties_filter() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'filter_properties_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce' );
			wp_die();
		}

		$settings       = $_POST['settings'];
		$posts_per_page = ! empty( $settings ) ? $settings['posts_per_page'] : 6;

		if ( isset( $_POST['taxonomy'] ) && isset( $_POST['term'] ) ) {
			$taxonomy = sanitize_text_field( $_POST['taxonomy'] );
			$term     = sanitize_text_field( $_POST['term'] );

			$properties_args = array(
				'post_type'      => 'property',
				'post_status'    => 'publish',
				'posts_per_page' => $posts_per_page,
				'order'          => $settings['order'],
				'tax_query'      => [
					[
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $term,
					],
				],
			);

			if ( ! empty( $settings['skip_sticky_properties'] ) && ( $settings['skip_sticky_properties'] !== 'yes' ) ) {
				$properties_args['meta_key'] = 'REAL_HOMES_sticky';
			}

			// Sorting
			if ( 'price' === $settings['orderby'] ) {
				$properties_args['orderby']  = 'meta_value_num';
				$properties_args['meta_key'] = 'REAL_HOMES_property_price';
			} else {
				// for date, title, menu_order and rand
				$properties_args['orderby'] = $settings['orderby'];
			}

			$meta_query = array();
			if ( ! empty( $settings['show_only_featured'] ) && ( 'yes' === $settings['show_only_featured'] ) ) {
				$meta_query[] = array(
					'key'     => 'REAL_HOMES_featured',
					'value'   => 1,
					'compare' => '=',
					'type'    => 'NUMERIC',
				);
			}

			if ( ! empty( $settings['single_agent_agency_properties'] ) && ! empty( $settings['select_agent'] ) ) {
				$select_agent = $settings['select_agent'];
				if ( 'yes' === $settings['single_agent_agency_properties'] || ! empty( $select_agent ) ) {
					$agent_id = get_the_ID();
					if ( ! empty( $select_agent ) ) {
						$agent_id = $select_agent;
					}
					$meta_query[] = array(
						'key'     => 'REAL_HOMES_agents',
						'value'   => $agent_id,
						'compare' => '=',
					);
				}
			}

			$properties_args['meta_query'] = $meta_query;

			$properties = new WP_Query( $properties_args );

			if ( $properties->have_posts() ) {
				ob_start();

				while ( $properties->have_posts() ) {
					$properties->the_post();

					$property_id      = get_the_ID();
					$args             = array();
					$args['slide']    = 'true';
					$args['settings'] = $settings;

					rhea_properties_widget_v14_card( $property_id, $args );
				}

				wp_reset_postdata();

				$data = ob_get_clean();
				wp_send_json_success( array(
					'props'      => $data,
					'foundProps' => $properties->found_posts
				) );
			} else {
				realhomes_print_no_result();
			}
		}

		wp_die();
	}

	add_action( 'wp_ajax_rhea_properties_widget_v14_properties_filter', 'rhea_properties_widget_v14_properties_filter' );
	add_action( 'wp_ajax_nopriv_rhea_properties_widget_v14_properties_filter', 'rhea_properties_widget_v14_properties_filter' );
}

if ( ! function_exists( 'rhea_render_favourite_button' ) ) {
	/**
	 * Generate Markup For Favourite Buttons
	 *
	 * @since 2.4.0
	 *
	 * @param int    $property_id      Property ID.
	 * @param array  $settings         Controls Settings.
	 * @param string $icon_path        Icon Custom Path.
	 * @param string $design_variation Design Variation.
	 */
	function rhea_render_favourite_button( $property_id, $settings, $icon_path = '', $design_variation = '' ) {

		$fav_button = get_option( 'theme_enable_fav_button' );

		if ( 'true' === $fav_button && 'yes' === $settings['ere_enable_fav_properties'] ) {

			$require_login               = get_option( 'inspiry_login_on_fav', 'no' );
			$add_to_fav_property_label   = get_option( 'inspiry_add_to_fav_property_label' );
			$added_to_fav_property_label = get_option( 'inspiry_added_to_fav_property_label' );

			if ( ! empty( $settings['ere_property_fav_label'] ) ) {
				$add_label = $settings['ere_property_fav_label'];
			} else if ( $add_to_fav_property_label ) {
				$add_label = $add_to_fav_property_label;
			} else {
				$add_label = esc_html__( 'Add to favorites', RHEA_TEXT_DOMAIN );
			}

			if ( ! empty( $settings['ere_property_fav_added_label'] ) ) {
				$added_label = $settings['ere_property_fav_added_label'];
			} else if ( $added_to_fav_property_label ) {
				$added_label = $added_to_fav_property_label;
			} else {
				$added_label = esc_html__( 'Added to favorites', RHEA_TEXT_DOMAIN );
			}

			$tooltip_class            = '';
			$added_tooltip_attributes = ' data-tooltip="' . esc_attr( $added_label ) . '" ';
			$add_tooltip_attributes   = ' data-tooltip="' . esc_attr( $add_label ) . '" ';
			if ( 'ultra' === $design_variation ) {
				$tooltip_class            = ' rh-ui-tooltip ';
				$added_tooltip_attributes = ' title="' . esc_attr( $added_label ) . '" ';
				$add_tooltip_attributes   = ' title="' . esc_attr( $add_label ) . '" ';
			}

			$button_label      = ! empty( $settings['favourite_button_label'] ) ? $settings['favourite_button_label'] : '';
			$is_user_logged_in = is_user_logged_in();

			if ( ( $is_user_logged_in && 'yes' === $require_login ) || ( 'yes' !== $require_login ) ) {

				$user_status = 'user_not_logged_in';
				if ( $is_user_logged_in ) {
					$user_status = 'user_logged_in';
				}
				?>
                <div class="favorite-btn-wrap favorite-btn-<?php echo esc_attr( $property_id ); ?>">
					<?php
					if ( is_added_to_favorite( $property_id ) ) {
						?>
                        <span class="favorite-placeholder highlight__red <?php echo esc_attr( $user_status . $tooltip_class ); ?>" data-propertyid="<?php echo esc_attr( $property_id ); ?>" <?php echo $added_tooltip_attributes ?>>
                            <?php
                            inspiry_include_favorite_svg_icon( $icon_path );
                            echo esc_html( $button_label );
                            ?>
                        </span>
                        <a href="#" class="favorite add-to-favorite hide <?php echo esc_attr( $user_status . $tooltip_class ); ?>" data-propertyid="<?php echo esc_attr( $property_id ); ?>" <?php echo $add_tooltip_attributes ?>>
							<?php
							inspiry_include_favorite_svg_icon( $icon_path );
							echo esc_html( $button_label );
							?>
                        </a>
						<?php
					} else {
						?>
                        <span class="favorite-placeholder highlight__red hide <?php echo esc_attr( $user_status . $tooltip_class ); ?>" data-propertyid="<?php echo esc_attr( $property_id ); ?>" <?php echo $added_tooltip_attributes ?>>
                            <?php
                            inspiry_include_favorite_svg_icon( $icon_path );
                            echo esc_html( $button_label );
                            ?>
                        </span>
                        <a href="#" class="favorite add-to-favorite <?php echo esc_attr( $user_status . $tooltip_class ); ?>" data-propertyid="<?php echo esc_attr( $property_id ); ?>" <?php echo $add_tooltip_attributes ?>>
							<?php
							inspiry_include_favorite_svg_icon( $icon_path );
							echo esc_html( $button_label );
							?>
                        </a>
						<?php
					}
					?>
                </div>
				<?php
			} else {
				?>
                <a href="#" class="<?php echo esc_attr( $tooltip_class ) ?> favorite add-to-favorite require-login" data-login="<?php echo esc_url( inspiry_get_login_register_url() ); ?>" <?php echo $add_tooltip_attributes ?>>
					<?php
					inspiry_include_favorite_svg_icon( $icon_path );
					echo esc_html( $button_label );
					?>
                </a>
				<?php
			}
		}
	}
}

if ( ! function_exists( 'rhea_render_single_property_favourite_button' ) ) {
	/**
	 * Generate Markup For Single Property Favourite Buttons
	 *
	 * @since 2.4.0
	 *
	 * @param int    $property_id      Property ID.
	 * @param array  $settings         Controls Settings.
	 * @param string $icon_path        Icon Custom Path.
	 * @param string $design_variation Design Variation.
	 */
	function rhea_render_single_property_favourite_button( $property_id, $settings, $icon_path = '', $design_variation = '' ) {

		$fav_button = get_option( 'theme_enable_fav_button' );

		if ( 'true' === $fav_button && 'yes' === $settings['show_favourite_button'] ) {

			$require_login               = get_option( 'inspiry_login_on_fav', 'no' );
			$add_to_fav_property_label   = get_option( 'inspiry_add_to_fav_property_label' );
			$added_to_fav_property_label = get_option( 'inspiry_added_to_fav_property_label' );

			if ( ! empty( $settings['favourites_label_add'] ) ) {
				$add_label = $settings['favourites_label_add'];
			} else if ( $add_to_fav_property_label ) {
				$add_label = $add_to_fav_property_label;
			} else {
				$add_label = esc_html__( 'Add to favorites', RHEA_TEXT_DOMAIN );
			}

			if ( ! empty( $settings['favourites_label_added'] ) ) {
				$added_label = $settings['favourites_label_added'];
			} else if ( $added_to_fav_property_label ) {
				$added_label = $added_to_fav_property_label;
			} else {
				$added_label = esc_html__( 'Added to favorites', RHEA_TEXT_DOMAIN );
			}

			$tooltip_class            = '';
			$added_tooltip_attributes = ' data-tooltip="' . esc_attr( $added_label ) . '" ';
			$add_tooltip_attributes   = ' data-tooltip="' . esc_attr( $add_label ) . '" ';
			if ( 'ultra' === $design_variation ) {
				$tooltip_class            = ' rh-ui-tooltip ';
				$added_tooltip_attributes = ' title="' . esc_attr( $added_label ) . '" ';
				$add_tooltip_attributes   = ' title="' . esc_attr( $add_label ) . '" ';
			}

			$is_user_logged_in = is_user_logged_in();

			if ( ( $is_user_logged_in && 'yes' === $require_login ) || ( 'yes' !== $require_login ) ) {

				$user_status = 'user_not_logged_in';
				if ( $is_user_logged_in ) {
					$user_status = 'user_logged_in';
				}
				?>
                <div class="favorite-btn-wrap favorite-btn-<?php echo esc_attr( $property_id ); ?>">
					<?php
					if ( is_added_to_favorite( $property_id ) ) {
						?>
                        <span class="favorite-placeholder highlight__red <?php echo esc_attr( $user_status . $tooltip_class ); ?>" data-propertyid="<?php echo esc_attr( $property_id ); ?>" <?php echo $added_tooltip_attributes ?>>
                            <?php
                            inspiry_include_favorite_svg_icon( $icon_path );
                            ?>
                        </span><a href="#" class="favorite add-to-favorite hide <?php echo esc_attr( $user_status . $tooltip_class ); ?>" data-propertyid="<?php echo esc_attr( $property_id ); ?>" <?php echo $add_tooltip_attributes ?>>
							<?php
							inspiry_include_favorite_svg_icon( $icon_path );
							?>
                        </a>
						<?php
					} else {
						?>
                        <span class="favorite-placeholder highlight__red hide <?php echo esc_attr( $user_status . $tooltip_class ); ?>" data-propertyid="<?php echo esc_attr( $property_id ); ?>" <?php echo $added_tooltip_attributes ?>>
                            <?php
                            inspiry_include_favorite_svg_icon( $icon_path );
                            ?>
                        </span><a href="#" class="favorite add-to-favorite <?php echo esc_attr( $user_status . $tooltip_class ); ?>" data-propertyid="<?php echo esc_attr( $property_id ); ?>" <?php echo $add_tooltip_attributes ?>>
							<?php
							inspiry_include_favorite_svg_icon( $icon_path );
							?>
                        </a>
						<?php
					}
					?>
                </div>
				<?php
			} else {
				?>
                <a href="#" class="<?php echo esc_attr( $tooltip_class ) ?> favorite add-to-favorite require-login" data-login="<?php echo esc_url( inspiry_get_login_register_url() ); ?>" <?php echo $add_tooltip_attributes ?>>
					<?php
					inspiry_include_favorite_svg_icon( $icon_path );
					?>
                </a>
				<?php
			}
		}
	}
}

if ( ! function_exists( 'rhea_render_compare_button' ) ) {
	/**
	 * Render Compare Buttons
	 *
	 * @since 2.4.0
	 *
	 * @param int    $property_id Property ID.
	 * @param array  $settings    Controls Settings.
	 * @param string $class       Container Class Selector.
	 */
	function rhea_render_compare_button( $property_id, $settings, $class = '' ) {
		$compare_properties_module = get_option( 'theme_compare_properties_module' );
		$inspiry_compare_page      = get_option( 'inspiry_compare_page' );
		if ( 'enable' === $compare_properties_module && $inspiry_compare_page && 'yes' == $settings['ere_enable_compare_properties'] ) {
			$property_img_url = get_the_post_thumbnail_url( $property_id, 'property-thumb-image' );
			if ( empty( $property_img_url ) ) {
				$property_img_url = get_inspiry_image_placeholder_url( 'property-thumb-image' );
			}

			$button_label = ( array_key_exists( 'compare_button_label', $settings ) && ! empty( $settings['compare_button_label'] ) ) ? $settings['compare_button_label'] : '';
			?>
            <div class="add-to-compare-span rhea_compare_icons rhea_svg_fav_icons compare-btn-<?php echo esc_attr( $property_id . ' ' . $class ); ?>" data-property-id="<?php echo esc_attr( $property_id ); ?>" data-property-title="<?php echo esc_attr( get_the_title( $property_id ) ); ?>" data-property-url="<?php echo esc_url( get_the_permalink( $property_id ) ); ?>" data-property-image="<?php echo esc_url( $property_img_url ); ?>">
                <span class="compare-placeholder highlight hide rh-ui-tooltip" title="<?php echo esc_attr( $settings['ere_property_compare_added_label'] ); ?>">
                    <?php
                    if ( ! RealHomes_Render_Icons::realhomes_custom_icons( 'realhomes_compare_button_icon' ) ) {
	                    rhea_safe_include_svg( 'icons/ultra-compare.svg' );
                    }
                    echo esc_html( $button_label );
                    ?>
                </span>
                <a class="rh_trigger_compare rhea_add_to_compare rh-ui-tooltip" title="<?php echo esc_attr( $settings['ere_property_compare_label'] ); ?>" href="<?php echo esc_url( get_the_permalink( $property_id ) ); ?>">
					<?php
					if ( ! RealHomes_Render_Icons::realhomes_custom_icons( 'realhomes_compare_button_icon' ) ) {
						rhea_safe_include_svg( 'icons/ultra-compare.svg' );
					}
					echo esc_html( $button_label );
					?>
                </a>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'rhea_render_single_property_compare_button' ) ) {
	/**
	 * Render Compare Buttons For Single Property
	 *
	 * @since 2.4.0
	 *
	 * @param int    $property_id Property ID.
	 * @param array  $settings    Controls Settings.
	 * @param string $class       Container Class Selector.
	 */
	function rhea_render_single_property_compare_button( $property_id, $settings, $class = '' ) {
		$compare_properties_module = get_option( 'theme_compare_properties_module' );
		$inspiry_compare_page      = get_option( 'inspiry_compare_page' );
		if ( 'enable' === $compare_properties_module && $inspiry_compare_page && 'yes' == $settings['ere_enable_compare_properties'] ) {
			$property_img_url = get_the_post_thumbnail_url( $property_id, 'property-thumb-image' );
			if ( empty( $property_img_url ) ) {
				$property_img_url = get_inspiry_image_placeholder_url( 'property-thumb-image' );
			}

			?>
            <div class="add-to-compare-span rhea_compare_icons rhea_svg_fav_icons compare-btn-<?php echo esc_attr( $property_id . ' ' . $class ); ?>" data-property-id="<?php echo esc_attr( $property_id ); ?>" data-property-title="<?php echo esc_attr( get_the_title( $property_id ) ); ?>" data-property-url="<?php echo esc_url( get_the_permalink( $property_id ) ); ?>" data-property-image="<?php echo esc_url( $property_img_url ); ?>">
                <span class="compare-placeholder highlight hide rh-ui-tooltip" title="<?php echo esc_attr( $settings['compare_label'] ); ?>">
                    <?php
                    if ( ! RealHomes_Render_Icons::realhomes_custom_icons( 'realhomes_compare_button_icon' ) ) {
	                    rhea_safe_include_svg( 'icons/ultra-compare.svg' );
                    }
                    ?>
                </span>
                <a class="rh_trigger_compare rhea_add_to_compare rh-ui-tooltip" title="<?php echo esc_attr( $settings['compare_label_added'] ); ?>" href="<?php echo esc_url( get_the_permalink( $property_id ) ); ?>">
					<?php
					if ( ! RealHomes_Render_Icons::realhomes_custom_icons( 'realhomes_compare_button_icon' ) ) {
						rhea_safe_include_svg( 'icons/ultra-compare.svg' );
					}
					?>
                </a>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'rhea_render_media_count_button' ) ) {
	/**
	 * Generate Markup for Media Count Buttons
	 *
	 * @since 2.4.0
	 *
	 * @param int    $property_id Property ID.
	 * @param string $widget_id   Widget ID.
	 * @param array  $settings    Settings.
	 * @param bool   $tooltip     Enable Tooltip.
	 * @param string $class       Container Class Selector.
	 */
	function rhea_render_media_count_button( $property_id, $widget_id, $settings, $tooltip = false, $class = '' ) {

		$get_post_meta_image = get_post_meta( $property_id, 'REAL_HOMES_property_images', false );
		$count_images        = ! empty( $get_post_meta_image ) ? count( $get_post_meta_image ) : '';
		$tooltip_class       = $tooltip ? 'rh-ui-tooltip' : '';
		?>
        <div class="rhea_ultra_media_count <?php echo esc_attr( $class ) ?>">
			<?php
			if ( $count_images > 0 ) {
				if ( array_key_exists( 'images_button_label', $settings ) && ! empty( $settings['images_button_label'] ) ) {
					$title = esc_attr( $count_images . ' ' . $settings['images_button_label'] );
					$label = esc_html( $settings['images_button_label'] );
				} else {
					$title = esc_attr( $count_images . ' ' . __( 'Images', RHEA_TEXT_DOMAIN ) );
					$label = '<span>' . esc_html( $count_images ) . '</span>';
				}
				?>
                <div class="rhea_media rhea_media_image <?php echo esc_attr( $tooltip_class ) ?>" title="<?php echo $title; ?>" data-fancybox-trigger="gallery-<?php echo esc_attr( $widget_id ) . '-' . esc_attr( $property_id ); ?>" data-this-id="<?php echo esc_attr( $property_id ); ?>">
					<?php
					if ( ! RealHomes_Render_Icons::realhomes_custom_icons( 'realhomes_image_button_icon' ) ) {
						include RHEA_ASSETS_DIR . 'icons/photos.svg';
					}
					echo wp_kses( $label, array( 'span' => array() ) );
					?>
                </div>
				<?php
			}

			$inspiry_video_group = get_post_meta( $property_id, 'inspiry_video_group', true );
			$count_videos        = ! empty( $inspiry_video_group ) ? count( $inspiry_video_group ) : '';

			if ( $count_videos > 0 ) {
				if ( array_key_exists( 'videos_button_label', $settings ) && ! empty( $settings['videos_button_label'] ) ) {
					$title = esc_attr( $count_videos . ' ' . $settings['videos_button_label'] );
					$label = esc_html( $settings['videos_button_label'] );
				} else {
					$title = esc_attr( $count_videos . ' ' . __( 'Videos', RHEA_TEXT_DOMAIN ) );
					$label = '<span>' . esc_html( $count_videos ) . '</span>';
				}
				?>
                <div class="rhea_media rhea_media_video <?php echo esc_attr( $tooltip_class ) ?>" title="<?php echo esc_attr( $title ) ?>" data-fancybox-trigger="video-<?php echo esc_attr( $widget_id ) . '-' . esc_attr( $property_id ); ?>" data-this-id="<?php echo esc_attr( $property_id ); ?>">
					<?php
					if ( ! RealHomes_Render_Icons::realhomes_custom_icons( 'realhomes_video_button_icon' ) ) {
						include RHEA_ASSETS_DIR . 'icons/video.svg';
					}
					echo wp_kses( $label, array( 'span' => array() ) );
					?>
                </div>
				<?php
			}
			?>
        </div>
        <div class="rhea_property_images_load" style="display: none">
			<?php
			if ( ! empty( $get_post_meta_image ) ) {
				foreach ( $get_post_meta_image as $item ) {
					$images_src = wp_get_attachment_image_src( $item, 'post-featured-image' );

					if ( ! empty( $images_src ) ) {
						?>
                        <span style="display: none;" data-fancybox="gallery-<?php echo esc_attr( $widget_id ) . '-' . esc_attr( $property_id ); ?>" data-src="<?php echo esc_url( $images_src[0] ); ?>" data-thumb="<?php echo esc_url( $images_src[0] ); ?>"></span>
						<?php
					}
				}
			}

			if ( ! empty( $inspiry_video_group ) ) {
				foreach ( $inspiry_video_group as $video ) {
					?>
                    <span style="display: none;" data-fancybox="video-<?php echo esc_attr( $widget_id ) . '-' . esc_attr( $property_id ); ?>" data-src="<?php echo esc_url( $video['inspiry_video_group_url'] ); ?>"></span>
					<?php
				}
			}
			?>
        </div>
		<?php
	}
}

if ( ! function_exists( 'rhea_render_properties_tags' ) ) {
	/**
	 * Renders property tags
	 *
	 * @since 2.4.0
	 *
	 * @param int    $property_id Property ID.
	 * @param array  $settings    Controls Settings.
	 * @param string $class       Container CSS Class.
	 */
	function rhea_render_properties_tags( $property_id, $settings, $class = '' ) {
		$is_featured       = get_post_meta( $property_id, 'REAL_HOMES_featured', true );
		$label_text        = get_post_meta( $property_id, 'inspiry_property_label', true );
		$show_featured_tag = $settings['ere_show_featured_tag'];
		$show_label_tags   = $settings['ere_show_label_tags'];

		if ( 'yes' === $show_featured_tag || 'yes' === $show_label_tags ) {
			?>
            <div class="rhea_tags_wrapper <?php echo esc_attr( $class ) ?>">
				<?php
				if ( 'yes' === $show_featured_tag && '1' === $is_featured ) {
					?>
                    <div class="rhea-tags rhea_featured">
						<?php rhea_safe_include_svg( 'icons/featured.svg' ); ?>
                        <span class="rhea_tags_tooltip"><span class="rhea_tags_tooltip_inner"><?php echo esc_html( $settings['ere_property_featured_label'] ); ?></span></span>
                    </div>
					<?php
				}

				if ( 'yes' === $show_label_tags && ! empty( $label_text ) ) {
					?>
                    <div class="rhea-tags rhea_hot <?php echo ! empty( $label_text_bg ) ? esc_attr( 'rhea_default_label' ) : ''; ?>">
						<?php rhea_safe_include_svg( 'icons/hot-icon.svg' ); ?>
                        <span class="rhea_tags_tooltip"><span class="rhea_tags_tooltip_inner"><?php echo esc_html( $label_text ); ?></span></span>
                    </div>
					<?php
				}
				?>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'rhea_render_single_property_meta_v2' ) ) {
	/**
	 * Renders the metadata for a single property item.
	 *
	 * This function outputs the HTML structure for a property metadata item,
	 * including its icon, label, value, and an optional postfix. It supports
	 * customization of the meta key and icon, including support for Elementor icons.
	 *
	 * @since 2.4.0
	 *
	 * @param string $label          The label for the metadata item (e.g., "Bedrooms").
	 * @param string $value          The value for the metadata item (e.g., "3").
	 * @param string $postfix        Optional. A postfix to append to the value (e.g., "sqft"). Default is an empty string.
	 * @param string $post_meta_key  Optional. The meta key associated with the item. Used for rendering a default icon. Default is an empty string.
	 * @param mixed  $icon           Optional. A custom icon class, markup, or data array (e.g., for Elementor icons). Default is an empty string.
	 * @param bool   $is_custom_icon Optional. Indicates if the icon should be rendered as a custom Elementor icon. Default is false.
	 */
	function rhea_render_single_property_meta_v2( $label, $value, $postfix = '', $post_meta_key = '', $icon = null, $is_custom_icon = false ) {
		?>
        <div class="meta-item">
            <div class="meta-item-icon-wrapper">
				<?php
				if ( $is_custom_icon ) {
					\Elementor\Icons_Manager::render_icon( $icon );
				} else if ( ! empty( $post_meta_key ) && ! empty( $icon ) ) {
					rhea_property_meta_icon( $post_meta_key, $icon );
				} else if ( ! empty( $icon ) ) {
					echo $icon;
				}
				?>
            </div>
            <div class="meta-item-content">
				<?php
				if ( ! empty( $label ) ) {
					?>
                    <span class="meta-item-label"><?php echo esc_html( $label ); ?></span>
					<?php
				}
				?>
                <div class="meta-item-value-wrapper">
                    <span class="meta-item-value"><?php echo esc_html( $value ); ?></span>
					<?php
					if ( ! empty( $postfix ) ) {
						?>
                        <span class="meta-item-value-postfix"><?php echo esc_html( $postfix ); ?></span>
						<?php
					}
					?>
                </div>
            </div>
        </div>
		<?php
	}
}

if ( ! function_exists( 'rhea_get_property_meta_keys_list' ) ) {
	/**
	 * Returns an associative array of meta slugs mapped to their corresponding meta keys, icons, and postfix options.
	 *
	 * This function provides a structured list of property meta keys along with their associated details, such as icons and optional postfixes.
	 *
	 * @since 2.4.0
	 *
	 * @return array Associative array of meta slugs with meta key, icon, and postfix details.
	 */
	function rhea_get_property_meta_keys_list() {
		$property_meta_keys_list = array(
			'bedrooms'    => array(
				'key'  => 'REAL_HOMES_property_bedrooms',
				'icon' => 'ultra-bedrooms',
			),
			'bathrooms'   => array(
				'key'  => 'REAL_HOMES_property_bathrooms',
				'icon' => 'ultra-bathrooms',
			),
			'area'        => array(
				'key'     => 'REAL_HOMES_property_size',
				'icon'    => 'ultra-area',
				'postfix' => 'REAL_HOMES_property_size_postfix'
			),
			'garage'      => array(
				'key'  => 'REAL_HOMES_property_garage',
				'icon' => 'ultra-garagers',
			),
			'year-built'  => array(
				'key'  => 'REAL_HOMES_property_year_built',
				'icon' => 'ultra-calender',
			),
			'lot-size'    => array(
				'key'     => 'REAL_HOMES_property_lot_size',
				'icon'    => 'ultra-lot-size',
				'postfix' => 'REAL_HOMES_property_lot_size_postfix'
			),
			'property-id' => array(
				'key'  => 'REAL_HOMES_property_id',
				'icon' => 'id-card',
			),
		);

		if ( rhea_is_rvr_enabled() ) {
			$property_meta_keys_list['guests'] = array(
				'key'  => 'rvr_guests_capacity',
				'icon' => 'guests-icons',
			);

			$property_meta_keys_list['min-stay'] = array(
				'key'  => 'rvr_min_stay',
				'icon' => 'icon-min-stay',
			);
		}

		return $property_meta_keys_list;
	}
}

if ( ! function_exists( 'rhea_get_elementor_widget_name_by_id' ) ) {
	/**
	 * Get Elementor widget name by widget ID.
	 *
	 * This function retrieves the widget name (defined in the `get_name()` method of an Elementor widget)
	 * by its unique widget ID within a specific Elementor post/page.
	 *
	 * @since 2.4.0
	 *
	 * @param string $widget_id The unique ID of the Elementor widget.
	 * @param int    $post_id   The ID of the post/page where the Elementor widget is used.
	 *
	 * @return string|false The widget name if found, or false if not found.
	 */
	function rhea_get_elementor_widget_name_by_id( $widget_id, $post_id ) {
		// Get the Elementor document for the current post
		$document = \Elementor\Plugin::$instance->documents->get( $post_id );

		if ( ! $document ) {
			return false;
		}

		// Get all widgets in the post
		$widgets_data = $document->get_elements_data();

		// Search recursively through the widgets
		return rhea_search_widget_name_by_id( $widgets_data, $widget_id );
	}
}


if ( ! function_exists( 'rhea_search_widget_name_by_id' ) ) {
	/**
	 * Recursively search for the widget name by its unique ID.
	 *
	 * This function traverses through Elementor widget data to find a widget
	 * by its ID and returns its widget name.
	 *
	 * @since 2.4.0
	 *
	 * @param array  $elements  The array of Elementor widget data.
	 * @param string $widget_id The unique ID of the widget to search for.
	 *
	 * @return string|false The widget name if found, or false if not found.
	 */
	function rhea_search_widget_name_by_id( $elements, $widget_id ) {
		foreach ( $elements as $element ) {
			// Check if the current element's ID matches the given widget ID
			if ( isset( $element['id'] ) && $element['id'] === $widget_id ) {
				return $element['widgetType']; // Return the widget name
			}

			// If the element has child elements, search recursively
			if ( ! empty( $element['elements'] ) ) {
				$result = rhea_search_widget_name_by_id( $element['elements'], $widget_id );
				if ( $result ) {
					return $result;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_get_available_image_sizes' ) ) {
	/**
	 * Get all available image sizes including custom ones.
	 *
	 * @since 2.3.9
	 *
	 * @return array List of image sizes with their dimensions.
	 */
	function realhomes_get_available_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = [];
		$default_sizes = [ 'thumbnail', 'medium', 'medium_large', 'large', 'full' ];

		// Add default WordPress image sizes
		foreach ( $default_sizes as $size ) {
			$width  = get_option( "{$size}_size_w" );
			$height = get_option( "{$size}_size_h" );
			$sizes[ $size ] = ucfirst( str_replace( '_', ' ', $size ) ) . " ({$width}x{$height})";
		}

		// Add additional custom image sizes
		if ( ! empty( $_wp_additional_image_sizes ) ) {
			foreach ( $_wp_additional_image_sizes as $size => $details ) {
				$width  = isset( $details['width'] ) ? $details['width'] : 'auto';
				$height = isset( $details['height'] ) ? $details['height'] : 'auto';
				$sizes[ $size ] = ucfirst( str_replace( '_', ' ', $size ) ) . " ({$width}x{$height})";
			}
		}

		return $sizes;
	}
}


if ( ! function_exists( 'rhea_sort_properties_filter' ) ) {
	/**
	 * Adjusts the properties query based on sorting criteria.
	 *
	 * This function modifies the properties query using the 'sortby' parameter
	 * from the URL or a provided value. It allows sorting by title, price, date,
	 * bedrooms, bathrooms, area, lot size, garage, and year built in ascending or
	 * descending order. When sorting by price, properties without a price value
	 * are moved to the end of the list.
	 *
	 * @since 2.3.9
	 *
	 * @param array  $properties_args The existing arguments for the properties query.
	 * @param string $sort_by         (Optional) Sorting criteria. If empty, it defaults to the 'sortby' URL parameter.
	 *
	 * @return array The modified properties query arguments.
	 */
	function rhea_sort_properties_filter( $properties_args, $sort_by = '' ) {
		// Use the 'sortby' URL parameter if not explicitly provided.
		$sort_by = $sort_by ?: ( isset( $_GET['sortby'] ) ? sanitize_text_field( $_GET['sortby'] ) : '' );

		// Define sorting options.
		$sort_options = [
			'title-asc'    => ['orderby' => 'title', 'order' => 'ASC'],
			'title-desc'   => ['orderby' => 'title', 'order' => 'DESC'],
			'price-asc'    => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_price', 'order' => 'ASC'],
			'price-desc'   => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_price', 'order' => 'DESC'],
			'date-asc'     => ['orderby' => 'date', 'order' => 'ASC'],
			'date-desc'    => ['orderby' => 'date', 'order' => 'DESC'],
			'bed-asc'      => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_bedrooms', 'order' => 'ASC'],
			'bed-desc'     => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_bedrooms', 'order' => 'DESC'],
			'bath-asc'     => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_bathrooms', 'order' => 'ASC'],
			'bath-desc'    => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_bathrooms', 'order' => 'DESC'],
			'area-asc'     => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_size', 'order' => 'ASC'],
			'area-desc'    => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_size', 'order' => 'DESC'],
			'lot-asc'      => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_lot_size', 'order' => 'ASC'],
			'lot-desc'     => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_lot_size', 'order' => 'DESC'],
			'garage-asc'   => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_garage', 'order' => 'ASC'],
			'garage-desc'  => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_garage', 'order' => 'DESC'],
			'year-asc'     => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_year_built', 'order' => 'ASC'],
			'year-desc'    => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_year_built', 'order' => 'DESC'],
			'views-asc'     => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_year_built', 'order' => 'ASC'],
			'views-desc'    => ['orderby' => 'meta_value_num', 'meta_key' => 'REAL_HOMES_property_year_built', 'order' => 'DESC'],
		];

		// Handle date sorting when 'REAL_HOMES_featured' meta key is used.
		if ( in_array( $sort_by, ['date-asc', 'date-desc'], true ) && isset( $properties_args['meta_key'] ) && $properties_args['meta_key'] === 'REAL_HOMES_featured' ) {
			$sort_options[ $sort_by ] = [
				'orderby' => ['meta_value_num' => 'DESC', 'date' => ( $sort_by === 'date-asc' ? 'ASC' : 'DESC' )],
			];
		}

		// Apply sorting if a valid option is provided.
		if ( isset( $sort_options[ $sort_by ] ) ) {
			$properties_args = array_merge( $properties_args, $sort_options[ $sort_by ] );
		}

		return $properties_args;
	}
}

if ( ! function_exists( 'rhea_process_filter_widget_post_types' ) ) {
	/**
	 * Process post types listings for filter properties widget
	 *
	 * @since  2.4.0
	 *
	 * @param array $args arguments to manage lists
	 *                    - post_type           (post, page, {custom post type name} etc.)
	 *                    - display_type        (thumbnail, radio)
	 *                    - view_limit          The number of items after which the 'view more' will be displayed
	 *                    - wrapper_classes     Post type specific classes for filter section
	 *                    - section_title       Section display title
	 *                    - display_title       Display title tag to show selection above posts listings
	 *                    - target_id           JS target ID which will be used to target this section
	 */
	function rhea_process_filter_widget_post_types( $args = array() ) {

		$post_type          = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
		$display_type       = isset( $args['display_type'] ) ? $args['display_type'] : 'radio';
		$view_limit         = isset( $args['view_limit'] ) ? $args['view_limit'] : 6;
		$wrapper_classes    = isset( $args['wrapper_classes'] ) ? $args['wrapper_classes'] : 'post-options';
		$section_title      = isset( $args['section_title'] ) ? $args['section_title'] : esc_html__( 'Posts', 'easy-real-estate' );
		$section_title_icon = isset( $args['section_title_icon'] ) ? $args['section_title_icon'] : '';
		$display_tag_title  = isset( $args['display_title'] ) ? $args['display_title'] : esc_html__( 'Post', 'easy-real-estate' );
		$js_target_id       = isset( $args['target_id'] ) ? $args['target_id'] : 'posts';
		$section_view_state = isset( $args['section_default_view_state'] ) ? $args['section_default_view_state'] : 'yes';

		// to ensure that view limit is not set under 1
		if ( 1 > intval( $view_limit ) ) {
			$view_limit = 6;
		}

		// fetching the posts
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => -1
			)
		);

		if ( ! is_wp_error( $posts ) && is_array( $posts ) ) {

			// if sitepress WPML is enabled
			if ( class_exists( 'SitePress' ) ) {
				$current_posts = array();

				// Filter IDs to get content in current language only
				foreach ( $posts as $post ) {
					if ( ! in_array( $post->ID, $current_posts ) ) {
						if ( apply_filters( 'wpml_object_id', $post->ID, $post_type, false ) !== null ) {
							$current_posts[] = apply_filters( 'wpml_object_id', $post->ID, $post_type, false );
						}
					}
				}

				if ( 0 < count( $current_posts ) ) {
					$posts = get_posts(
						array(
							'post_type'      => $post_type,
							'posts_per_page' => -1,
							'post__in'       => $current_posts
						)
					);
				} else {
					$posts = array();
				}
			}

			// Counter variable to honor view limit of the checkboxes
			$counter = 1;

			// if there are posts available
			if ( 0 < count( $posts ) ) {
				?>
                <div class="filter-wrapper <?php echo esc_attr( $wrapper_classes ); ?>">
                    <h4 class="<?php echo 'yes' !== $section_view_state ? 'collapsed' : ''; ?>">
                        <?php
                        echo esc_html( $section_title );
                        echo ! empty( $section_title_icon ) ? '<span class="section-control-icon">' . wp_kses( $section_title_icon, rhea_allowed_html() ) . '</span>' : '';
                        ?>
                    </h4>
                    <div class="filter-section posts-list display-type-<?php echo esc_attr( $display_type ); ?>" data-meta-name="<?php echo esc_attr( $js_target_id ); ?>" data-display-title="<?php echo esc_attr( $display_tag_title ); ?>" <?php echo 'yes' !== $section_view_state ? 'style="display: none;"' : ''; ?>>
                        <div class="items-visible">
							<?php
							foreach ( $posts as $post ) {
								// foreach will break after generating checkboxes under the selected limit
								if ( $counter <= $view_limit ) {
									$post_id = $post->ID;
									?>
                                    <div class="pt-item <?php echo esc_attr( $display_type ); ?>" data-post-id="<?php echo esc_attr( $post_id ) . '|' . get_the_title( $post_id ); ?>">
										<?php
										if ( $display_type === 'thumbnail' ) {
											if ( has_post_thumbnail( $post_id ) ) {
												?>
                                                <figure><?php echo get_the_post_thumbnail( $post_id, 'thumbnail' ); ?></figure>
												<?php
											}
											?>
                                            <div class="item-content">
                                                <h5 class="pt-title"><?php echo get_the_title( $post_id ); ?></h5>
                                            </div>
											<?php
										} else {
											?>
                                            <span><?php echo get_the_title( $post_id ); ?><i>&#10003;</i></span>
											<?php
										}
										?>
                                    </div>
									<?php
								} else {
									break;
								}
								$counter++;
							}
							?>
                        </div>
						<?php
						// if we still have items after the limit
						if ( $view_limit < count( $posts ) ) {

							// slicing the array to skip already generated checkboxes
							$more_posts = array_slice( $posts, $view_limit );
							if ( 0 < count( $more_posts ) ) {
								?>
                                <div class="items-view-more">
									<?php
									foreach ( $more_posts as $post ) {
										$post_id = $post->ID;
										?>
                                        <div class="pt-item <?php echo esc_attr( $display_type ); ?>" data-post-id="<?php echo esc_attr( $post_id ) . '|' . get_the_title( $post_id ); ?>">
											<?php
											if ( $display_type === 'thumbnail' ) {
												if ( has_post_thumbnail( $post_id ) ) {
													?>
                                                    <figure><?php echo get_the_post_thumbnail( $post_id, 'thumbnail' ); ?></figure>
													<?php
												}
												?>
                                                <div class="item-content">
                                                    <h5 class="pt-title"><?php echo get_the_title( $post_id ); ?></h5>
                                                </div>
												<?php
											} else {
												?>
                                                <span><?php echo get_the_title( $post_id ); ?><i>&#10003;</i></span>
												<?php
											}
											?>
                                        </div>
										<?php
									}
									?>
                                </div>

                                <a class="view-more"><?php esc_html_e( 'View More', RHEA_TEXT_DOMAIN ); ?></a>
                                <a class="view-less"><?php esc_html_e( 'View Less', RHEA_TEXT_DOMAIN ); ?></a>
								<?php
							}
						}
						?>
                    </div>
                </div>
				<?php
			}

		}
	}
}

if ( ! function_exists( 'rhea_get_elementor_icon_html' ) ) {
	/**
	 * Retrieves an Elementor icon as a string instead of direct output.
	 *
	 * This function allows storing Elementor icons in variables for later use in templates.
	 * It ensures that `\Elementor\Icons_Manager::render_icon()` does not break layouts by directly echoing output.
	 *
	 * @since 2.4.0
	 *
	 * @param array $icon_settings Icon settings array from Elementor.
	 * @return string HTML string of the icon or empty string if no icon is set.
	 */
	function rhea_get_elementor_icon_html( $icon_settings ) {
		if ( ! is_array( $icon_settings ) || empty( $icon_settings ) ) {
			return ''; // Return empty string if no valid icon settings are provided.
		}

		ob_start(); // Start output buffering

		// Handle SVG icons
		if ( isset( $icon_settings['value'] ) && is_array( $icon_settings['value'] ) && ! empty( $icon_settings['value']['url'] ) ) {
			\Elementor\Icons_Manager::render_icon( $icon_settings, [ 'aria-hidden' => 'true' ] );
		}
		// Handle FontAwesome and other icon libraries
		else if ( ! empty( $icon_settings['library'] ) && ! empty( $icon_settings['value'] ) ) {
			?>
            <i class="<?php echo esc_attr( $icon_settings['library'] . ' ' . $icon_settings['value'] ); ?>"></i>
			<?php
		}

		return ob_get_clean(); // Capture buffered output and return it as a string.
	}
}


if ( ! function_exists( 'rhea_prepare_filter_widget_args' ) ) {
	/**
	 * Prepares query arguments for filtering properties based on user-selected filters.
	 *
	 * This function processes filter values, creates tax and meta queries, and ensures
	 * only valid filters are included in the final search arguments.
	 *
	 * @since 2.4.0
	 *
	 * @param array $filter_values An associative array of filter parameters.
	 *
	 * @return array The prepared arguments for WP_Query.
	 */
	function rhea_prepare_filter_widget_args( $filter_values ) {
		if ( ! is_array( $filter_values ) || empty( $filter_values ) ) {
			return [];
		}

		$search_args = array( 'post_type' => 'property' );

		/* Initialize Taxonomy Query Array */
		$tax_query = array();

		/* Initialize Meta Query Array */
		$meta_query = array();

		/* Confirming if necessary information is set */
		if ( isset( $_POST['filterValues'] ) ) {
			$search_values        = $_POST['filterValues'];
			$property_features    = ! empty( $search_values['features'] ) ? $search_values['features'] : '';
			$property_city        = ! empty( $search_values['locations'] ) ? $search_values['locations'] : '';
			$property_statuses    = ! empty( $search_values['statuses'] ) ? $search_values['statuses'] : '';
			$property_types       = ! empty( $search_values['types'] ) ? $search_values['types'] : '';
			$price                = ! empty( $search_values['price'] ) ? $search_values['price'] : '';
			$area                 = ! empty( $search_values['area'] ) ? $search_values['area'] : '';
			$min_beds             = ! empty( $search_values['bedrooms'] ) ? $search_values['bedrooms'] : '';
			$min_baths            = ! empty( $search_values['bathrooms'] ) ? $search_values['bathrooms'] : '';
			$garages              = ! empty( $search_values['garages'] ) ? $search_values['garages'] : '';
			$property_id          = ! empty( $search_values['propertyID'] ) ? $search_values['propertyID'] : '';
			$agents               = ! empty( $search_values['agent'] ) ? $search_values['agent'] : '';
			$agencies             = ! empty( $search_values['agencies'] ) ? $search_values['agencies'] : '';
			$number_of_properties = ! empty( $search_values['filter_posts_count'] ) ? $search_values['filter_posts_count'] : 6;

			$search_args['posts_per_page'] = $number_of_properties;

			// Property Type query filter
			if ( 0 < $property_types ) {
				$tax_query[] = array(
					'taxonomy' => 'property-type',
					'field'    => 'slug',
					'terms'    => $property_types,
				);
			}

			// Property City (location) query filter
			if ( 0 < $property_city ) {
				$tax_query[] = array(
					'taxonomy' => 'property-city',
					'field'    => 'slug',
					'terms'    => $property_city,
				);
			}

			// Property Status query filter
			if ( 0 < $property_statuses ) {
				$tax_query[] = array(
					'taxonomy' => 'property-status',
					'field'    => 'slug',
					'terms'    => $property_statuses,
				);
			}

			// Property Features query filter
			if ( 0 < $property_features ) {
				$tax_query[] = array(
					'taxonomy' => 'property-feature',
					'field'    => 'slug',
					'terms'    => $property_features
				);
			}

			// Minimum Bedrooms
			if ( ! empty( $min_beds ) ) {
				$min_beds = intval( $min_beds ) - 0.1;
				if ( $min_beds > 0 ) {
					$meta_query[] = array(
						'key'     => 'REAL_HOMES_property_bedrooms',
						'value'   => $min_beds,
						'compare' => '>'
					);
				}
			}

			// Minimum Bathrooms
			if ( ! empty( $min_baths ) ) {
				$min_baths = intval( $min_baths ) - 0.1;
				if ( $min_baths > 0 ) {
					$meta_query[] = array(
						'key'     => 'REAL_HOMES_property_bathrooms',
						'value'   => $min_baths,
						'compare' => '>'
					);
				}
			}

			// Minimum Garages
			if ( ! empty( $garages ) ) {
				$garages = intval( $garages ) - 0.1;
				if ( $garages > 0 ) {
					$meta_query[] = array(
						'key'     => 'REAL_HOMES_property_garage',
						'value'   => $garages,
						'compare' => '>'
					);
				}
			}

			// Property ID
			if ( ! empty( $property_id ) ) {
				if ( $property_id > 0 ) {
					$meta_query[] = array(
						'key'     => 'REAL_HOMES_property_id',
						'value'   => $property_id,
						'compare' => '='
					);
				}
			}

			// Property Agents
			if ( ! empty( $agents ) ) {
				$agents_array = array();
				if ( is_array( $agents ) ) {
					foreach ( $agents as $agent_item ) {
						$agent_item_array = explode( '|', $agent_item );
						$agents_array[]   = $agent_item_array[0];
					}
				}

				$meta_query[] = array(
					'key'     => 'REAL_HOMES_agents',
					'value'   => $agents_array,
					'compare' => 'IN'
				);
			}

			// Property Agencies
			if ( ! empty( $agencies ) ) {
				$agency_array = array();
				if ( is_array( $agencies ) ) {
					foreach ( $agencies as $agency_item ) {
						$agency_item_array = explode( '|', $agency_item );
						$agency_array[]    = $agency_item_array[0];
					}
				}

				$agencies_query = new WP_Query(
					array(
						'post_type'      => 'agent',
						'posts_per_page' => -1,
						'meta_query'     => array(
							array(
								'key'     => 'REAL_HOMES_agency',
								'value'   => $agency_array,
								'type'    => 'NUMERIC',
								'compare' => 'IN',
							),
						),
					),
				);

				if ( $agencies_query->have_posts() ) {
					$agents_in_agency = wp_list_pluck( $agencies_query->posts, 'ID' );
				} else {
					$agents_in_agency = [ -1 ];
				}

				$meta_query[] = array(
					'key'     => 'REAL_HOMES_agents',
					'value'   => $agents_in_agency,
					'compare' => 'IN',
				);

			}


			// Prices Range Meta
			if ( ! empty( $price ) ) {
				$price_array = explode( ' - ', $price );
				$min_price   = doubleval( $price_array[0] );
				$max_price   = doubleval( $price_array[1] );
				if ( $min_price >= 0 && $max_price > $min_price ) {
					$meta_query[] = array(
						'key'     => 'REAL_HOMES_property_price',
						'value'   => array( $min_price, $max_price ),
						'type'    => 'NUMERIC',
						'compare' => 'BETWEEN'
					);
				}
			}

			// Areas Range Meta
			if ( ! empty( $area ) ) {
				$area_array = explode( ' - ', $area );
				$min_area   = doubleval( $area_array[0] );
				$max_area   = doubleval( $area_array[1] );
				if ( $min_area >= 0 && $max_area > $min_area ) {
					$meta_query[] = array(
						'key'     => 'REAL_HOMES_property_size',
						'value'   => array( $min_area, $max_area ),
						'type'    => 'NUMERIC',
						'compare' => 'BETWEEN'
					);
				}
			}

			// Additional fields query filter
			$additional_fields = get_option( 'inspiry_property_additional_fields' );
            if ( ! empty( $additional_fields['inspiry_additional_fields_list'] ) ) {
	            $additional_fields = $additional_fields['inspiry_additional_fields_list'];

	            if ( is_array( $additional_fields ) && 0 < count( $additional_fields ) ) {
		            foreach ( $additional_fields as $field ) {
			            $field_name = $field['field_name'];
			            $field_type = $field['field_type'];
			            $field_slug = 'inspiry_' . strtolower( str_replace( ' ', '_', $field_name ) );

			            if ( ! empty( $search_values[ $field_slug ] ) ) {

				            if ( $field_type === 'checkbox_list' ) {
					            $meta_query[] = array(
						            'key'     => $field_slug,
						            'value'   => $search_values[ $field_slug ],
						            'compare' => 'IN',
					            );
				            } else if ( $field_type === 'textarea' ) {
					            $meta_query[] = array(
						            'key'     => $field_slug,
						            'value'   => $_POST[ $field_slug ],
						            'compare' => 'LIKE'
					            );
				            } else if ( $field_type === 'select' ) {
					            $select_compare = ( ! empty( $field['multi_select'] ) && $field['multi_select'] === 'yes' ) ? 'IN' : '=';
					            $meta_query[]   = array(
						            'key'     => $field_slug,
						            'value'   => $search_values[ $field_slug ],
						            'compare' => $select_compare
					            );
				            } else {
					            $meta_query[] = array(
						            'key'     => $field_slug,
						            'value'   => $search_values[ $field_slug ],
						            'compare' => '='
					            );
				            }
			            }
		            }
	            }
            }

			/* Managing the page number */
			if ( ! empty( $search_values['page'] ) ) {
				$search_args['paged'] = $search_values['page'];
			}

		} // isset fieldValues

		/* Taxonomy Search Filter */
		$tax_query = apply_filters( 'rhea_ajax_filters_widget_taxonomy', $tax_query );

		/* Meta Search Filter */
		$meta_query = apply_filters( 'rhea_ajax_filters_widget_meta', $meta_query );

		/* If more than one taxonomy exists then specify the relation */
		$tax_count = count( $tax_query );
		if ( $tax_count > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		/* If more than one meta query elements exist then specify the relation */
		$meta_count = count( $meta_query );
		if ( $meta_count > 1 ) {
			$meta_query['relation'] = 'AND';
		}

		/* If taxonomy query has some values then add it to search query */
		if ( $tax_count > 0 ) {
			$search_args['tax_query'] = $tax_query;
		}

		/* If meta query has some values then add it to search query */
		if ( $meta_count > 0 ) {
			$search_args['meta_query'] = $meta_query;
		}

		if ( ! empty( $search_values['properties_sort_by'] ) ) {
			$search_args = rhea_sort_properties_filter( $search_args, $search_values['properties_sort_by'] );
		}

		return $search_args;
	}
}


if ( ! function_exists( 'rhea_generate_sort_dropdown_options' ) ) {
	/**
	 * Generates the sort dropdown options markup for RealHomes Elementor Addon.
	 *
	 * @since 2.4.2
	 *
	 * @param array $args {
	 *     Optional. Arguments to customize the dropdown.
	 *
	 *     @type string   $selected        Currently selected option. Default 'default'.
	 *     @type string   $id              ID attribute for the <select> tag. Default 'sort-properties'.
	 *     @type string   $name            Name attribute for the <select> tag. Default 'sort-properties'.
	 *     @type string   $class           CSS class(es) for the <select> tag.
	 *                                     Default 'inspiry_select_picker_trigger rh-ultra-select-dropdown show-tick'.
	 *     @type string[] $disabled_fields Array of option values to disable. Default empty array.
	 * }
	 */
	function rhea_generate_sort_dropdown_options( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'selected'        => 'default',
			'id'              => 'sort-properties',
			'name'            => 'sort-properties',
			'class'           => 'inspiry_select_picker_trigger rh-ultra-select-dropdown show-tick',
			'disabled_fields' => array(),
		) );

		$disabled_fields = is_array( $args['disabled_fields'] ) ? $args['disabled_fields'] : array();

		$sort_options = array(
			'default'     => esc_html__( 'Default Order', RHEA_TEXT_DOMAIN ),
			'title-asc'   => esc_html__( 'Property Title A to Z', RHEA_TEXT_DOMAIN ),
			'title-desc'  => esc_html__( 'Property Title Z to A', RHEA_TEXT_DOMAIN ),
			'price-asc'   => esc_html__( 'Price Low to High', RHEA_TEXT_DOMAIN ),
			'price-desc'  => esc_html__( 'Price High to Low', RHEA_TEXT_DOMAIN ),
			'date-asc'    => esc_html__( 'Date Old to New', RHEA_TEXT_DOMAIN ),
			'date-desc'   => esc_html__( 'Date New to Old', RHEA_TEXT_DOMAIN ),
			'bed-asc'     => esc_html__( 'Bedrooms Fewest to Most', RHEA_TEXT_DOMAIN ),
			'bed-desc'    => esc_html__( 'Bedrooms Most to Fewest', RHEA_TEXT_DOMAIN ),
			'bath-asc'    => esc_html__( 'Bathrooms Fewest to Most', RHEA_TEXT_DOMAIN ),
			'bath-desc'   => esc_html__( 'Bathrooms Most to Fewest', RHEA_TEXT_DOMAIN ),
			'area-asc'    => esc_html__( 'Area Smallest to Largest', RHEA_TEXT_DOMAIN ),
			'area-desc'   => esc_html__( 'Area Largest to Smallest', RHEA_TEXT_DOMAIN ),
			'lot-asc'     => esc_html__( 'Lot Size Smallest to Largest', RHEA_TEXT_DOMAIN ),
			'lot-desc'    => esc_html__( 'Lot Size Largest to Smallest', RHEA_TEXT_DOMAIN ),
			'garage-asc'  => esc_html__( 'Garages Fewest to Most', RHEA_TEXT_DOMAIN ),
			'garage-desc' => esc_html__( 'Garages Most to Fewest', RHEA_TEXT_DOMAIN ),
			'year-asc'    => esc_html__( 'Year Built Oldest to Newest', RHEA_TEXT_DOMAIN ),
			'year-desc'   => esc_html__( 'Year Built Newest to Oldest', RHEA_TEXT_DOMAIN ),
			'views-asc'   => esc_html__( 'Views - Fewer to More', RHEA_TEXT_DOMAIN ),
			'views-desc'  => esc_html__( 'Views - More to Fewer', RHEA_TEXT_DOMAIN ),
		);

		echo '<select name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( $args['class'] ) . '">';

		foreach ( $sort_options as $value => $label ) {
			$selected = selected( $args['selected'], $value, false );
			$disabled = in_array( $value, $disabled_fields, true ) ? 'disabled' : '';
			printf(
				'<option value="%1$s" %2$s %3$s>%4$s</option>',
				esc_attr( $value ),
				$selected,
				$disabled,
				esc_html( $label )
			);
		}

		echo '</select>';
	}
}