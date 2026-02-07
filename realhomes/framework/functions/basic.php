<?php
/**
 * This file contain theme's basic functions
 *
 * @package realhomes/functions
 */

if ( ! function_exists( 'realhomes_widget_no_items' ) ) {
	/**
	 * Display no items found message for all types of widgets.
	 *
	 * @param $message
	 *
	 * @return void
	 */
	function realhomes_widget_no_items( $message = '' ) {
		if ( empty( $message ) ) {
			$message = esc_html__( 'No Posts Found!', RH_TEXT_DOMAIN );
		}
		echo '<div class="rh-widget-no-items-found">' . esc_html( $message ) . '</div>';
	}
}

if ( ! function_exists( 'inspiry_logo_img' ) ) {
	/**
	 * Display logo image
	 *
	 * @since 3.7.1
	 *
	 * @param string $logo_url        Logo img url.
	 * @param string $retina_logo_url Retina logo image url.
	 *
	 * @return void
	 */
	function inspiry_logo_img( $logo_url, $retina_logo_url ) {

		global $is_IE;

		if ( ! empty( $logo_url ) && ! empty( $retina_logo_url ) && ! $is_IE ) {
			echo '<img alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" src="' . esc_url( $logo_url ) . '" srcset="' . esc_url( $logo_url ) . ', ' . esc_url( $retina_logo_url ) . ' 2x">';
		} else if ( ! empty( $retina_logo_url ) ) {
			echo '<img alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" src="' . esc_url( $retina_logo_url ) . '">';
		} else {
			echo '<img alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" src="' . esc_url( $logo_url ) . '">';
		}
	}
}

if ( ! function_exists( 'inspiry_post_nav' ) ) {

	/**
	 * Return link to previous and next entry.
	 *
	 * @param bool $same_category - True if from same category.
	 */
	function inspiry_post_nav( $same_category = false ) {

		if ( ( 'true' === get_option( 'inspiry_property_prev_next_link' ) && is_singular( 'property' ) )
			|| ( 'true' === get_option( 'inspiry_post_prev_next_link' ) && is_singular( 'post' ) )
		) {

			$entries['prev'] = get_previous_post( $same_category );
			$entries['next'] = get_next_post( $same_category );

			$output = '';

			foreach ( $entries as $key => $entry ) {
				if ( empty( $entry ) ) {
					continue;
				}

				$the_title = get_the_title( $entry->ID );
				$link      = get_permalink( $entry->ID );
				$image     = has_post_thumbnail( $entry );

				$entry_title = $entry_img = '';
				$class       = ( $image ) ? 'with-image' : 'without-image';
				$icon        = ( 'prev' == $key ) ? 'angle-left' : 'angle-right';

				?>
                <a class="inspiry-post-nav inspiry-post-<?php echo esc_attr( $key ) . ' ' . esc_attr( $class ); ?>" href="<?php echo esc_url( $link ); ?>">
                    <span class="label"><i class="fas fa-<?php echo esc_attr( $icon ); ?>"></i></span>
                    <span class="entry-info-wrap">
                        <span class="entry-info">
                            <?php
                            if ( 'prev' == $key ) {
	                            ?>
                                <span class="entry-title"><?php echo esc_html( $the_title ); ?></span>
                                <?php
	                            if ( $image ) {
		                            ?>
                                    <span class="entry-image">
                                        <?php echo get_the_post_thumbnail( $entry, 'thumbnail' ); ?>
                                    </span>
		                            <?php
	                            } else {
		                            ?>
                                    <span class="entry-image">
                                        <img src="<?php echo esc_url( get_inspiry_image_placeholder_url( 'thumbnail' ) ); ?>" alt="<?php echo esc_attr( $the_title ); ?>">
                                    </span>
		                            <?php
	                            }
                            } else {
	                            if ( $image ) {
		                            ?>
                                    <span class="entry-image">
                                        <?php echo get_the_post_thumbnail( $entry, 'thumbnail' ); ?>
                                    </span>
		                            <?php
	                            } else {
		                            ?>
                                    <span class="entry-image">
                                        <img src="<?php echo esc_url( get_inspiry_image_placeholder_url( 'thumbnail' ) ); ?>" alt="<?php echo esc_attr( $the_title ); ?>">
                                    </span>
		                            <?php
	                            }
	                            ?>
                                <span class="entry-title"><?php echo esc_html( $the_title ); ?></span>
	                            <?php
                            }
                            ?>
                        </span>
                    </span>
                </a>
				<?php
			}
		}
	}
}

if ( ! function_exists( 'list_gallery_images' ) ) {
	/**
	 * Get list of Gallery Images - use in gallery post format
	 *
	 * @param string $size
	 */
	function list_gallery_images( $size = 'post-featured-image' ) {
		global $post;

		if ( ! function_exists( 'rwmb_meta' ) ) {
			return;
		}
		$gallery_images = rwmb_meta( 'REAL_HOMES_gallery', 'type=plupload_image&size=' . $size, get_the_ID() );

		if ( ! empty( $gallery_images ) ) {
			foreach ( $gallery_images as $gallery_image ) {
				$caption = ( ! empty( $gallery_image['caption'] ) ) ? $gallery_image['caption'] : $gallery_image['alt'];
				echo '<li><a href="' . $gallery_image['full_url'] . '" title="' . $caption . '" data-fancybox="list-gallery" data-elementor-open-lightbox="no">';
				echo '<img src="' . $gallery_image['url'] . '" alt="' . $gallery_image['title'] . '" />';
				echo '</a></li>';
			}
		} else if ( ! empty( get_the_post_thumbnail( get_the_ID() ) ) ) {
			echo '<li><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" data-elementor-open-lightbox="no">';
			the_post_thumbnail( $size );
			echo '</a></li>';
		} else {
			inspiry_image_placeholder( $size );
		}
	}
}

if ( ! function_exists( 'framework_excerpt' ) ) {
	/**
	 * Output custom excerpt of required length
	 *
	 * @param int    $len  number of words
	 * @param string $trim string to appear after excerpt
	 */
	function framework_excerpt( $len = 15, $trim = "&hellip;" ) {
		echo get_framework_excerpt( $len, $trim );
	}
}

if ( ! function_exists( 'get_framework_excerpt' ) ) {
	/**
	 * Returns custom excerpt of required length
	 *
	 * @param int    $len  number of words
	 * @param string $trim string after excerpt
	 *
	 * @return array|string
	 */
	function get_framework_excerpt( $len = 15, $trim = "&hellip;" ) {
		$limit     = $len + 1;
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

if ( ! function_exists( 'comment_custom_excerpt' ) ) {
	/**
	 * Output comment's excerpt of required length from given contents
	 *
	 * @param int    $len             number of words
	 * @param string $comment_content comment contents
	 * @param string $trim            text after excerpt
	 */
	function comment_custom_excerpt( $len = 15, $comment_content = "", $trim = "&hellip;" ) {
		$limit     = $len + 1;
		$excerpt   = explode( ' ', $comment_content, $limit );
		$num_words = count( $excerpt );
		if ( $num_words >= $len ) {
			array_pop( $excerpt );
		} else {
			$trim = "";
		}
		$excerpt = implode( " ", $excerpt ) . "$trim";
		echo wp_kses( $excerpt, wp_kses_allowed_html( 'post' ) );
	}
}

if ( ! function_exists( 'get_framework_custom_excerpt' ) ) {
	/**
	 * Return excerpt of required length from given contents
	 *
	 * @param string $contents contents to extract excerpt
	 * @param int    $len      number of words
	 * @param string $trim     string to appear after excerpt
	 *
	 * @return array|string
	 */
	function get_framework_custom_excerpt( $contents = "", $len = 15, $trim = "&hellip;" ) {
		$limit     = $len + 1;
		$excerpt   = explode( ' ', $contents, $limit );
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

if ( ! function_exists( 'admin_js' ) ) {
	/**
	 * Register and load admin javascript
	 *
	 * @param string $hook - Page name.
	 */
	function admin_js( $hook ) {
		if ( $hook == 'post.php' || $hook == 'post-new.php' ) {
			wp_register_script( 'admin-script', get_theme_file_uri( 'common/js/admin.js' ), array( 'jquery' ), INSPIRY_THEME_VERSION );
			wp_enqueue_script( 'admin-script' );
		}
	}

	add_action( 'admin_enqueue_scripts', 'admin_js', 10, 1 );
}

/**
 * Disable Post Format UI in WordPress 3.6 and Keep the Old One Working
 */
add_filter( 'enable_post_format_ui', '__return_false' );

if ( ! function_exists( 'remove_category_list_rel' ) ) {
	/**
	 * Remove rel attribute from the category list
	 *
	 * @param $output
	 *
	 * @return mixed
	 */
	function remove_category_list_rel( $output ) {
		$output = str_replace( ' rel="tag"', '', $output );
		$output = str_replace( ' rel="category"', '', $output );
		$output = str_replace( ' rel="category tag"', '', $output );

		return $output;
	}

	add_filter( 'wp_list_categories', 'remove_category_list_rel' );
	add_filter( 'the_category', 'remove_category_list_rel' );
}

if ( ! function_exists( 'addhttp' ) ) {
	/**
	 * Add http:// in url if not exists
	 *
	 * @param $url
	 *
	 * @return string
	 */
	function addhttp( $url ) {
		if ( ! preg_match( "~^(?:f|ht)tps?://~i", $url ) ) {
			$url = "http://" . $url;
		}

		return $url;
	}
}

if ( ! function_exists( 'custom_login_logo_url' ) ) {
	/**
	 * WordPress login page logo URL
	 *
	 * @return string|void
	 */
	function custom_login_logo_url() {
		return home_url();
	}

	add_filter( 'login_headerurl', 'custom_login_logo_url' );
}

if ( ! function_exists( 'custom_login_logo_title' ) ) {
	/**
	 * WordPress login page logo url title
	 *
	 * @return string|void
	 */
	function custom_login_logo_title() {
		return get_bloginfo( 'name' );
	}

	add_filter( 'login_headertext', 'custom_login_logo_title' );
}

if ( ! function_exists( 'custom_login_style' ) ) {
	/**
	 * WordPress login page custom styles.
	 *
	 * Adds custom CSS to the WordPress login page to modify its appearance.
	 *
	 * @since 4.3.9
	 */
	function custom_login_styles() {

		if ( 'enable' !== get_option( 'realhomes_wp_login_page_styles', 'enable' ) ) {
			return '';
		}

		$page_styles = '';

		// General Text Color
		$text_color = get_option( 'realhomes_wp_login_page_text_color' );
		if ( ! empty( $text_color ) ) {
			$page_styles .= 'body.login { color: ' . $text_color . '; }';
		}

		// Link Color
		$link_color = get_option( 'realhomes_wp_login_page_link_color' );
		if ( ! empty( $link_color ) ) {
			$page_styles .= 'body.login #backtoblog a, body.login #nav a { color: ' . $link_color . '; transition: all 0.25s linear; }';
		}

		// Link Hover Color
		$link_hover_color = get_option( 'realhomes_wp_login_page_link_hover_color' );
		if ( ! empty( $link_hover_color ) ) {
			$page_styles .= 'body.login #backtoblog a:hover, body.login #nav a:hover { color: ' . $link_hover_color . '; }';
		}

		$page_styles .= 'body.login {';
		$page_styles .= 'background-color: ' . get_option( 'realhomes_wp_login_page_background_color', '#f0f0f1' ) . ';';

		$page_bg_image = get_option( 'realhomes_wp_login_page_background_image' );
		if ( ! empty( $page_bg_image ) ) {
			$page_styles .= 'background-image: url(' . esc_url( $page_bg_image ) . ');';
			$page_styles .= 'background-size:' . get_option( 'realhomes_wp_login_page_background_image_size', 'auto' ) . ';';
			$page_styles .= 'background-repeat:' . get_option( 'realhomes_wp_login_page_background_image_repeat', 'no-repeat' ) . ';';
			$page_styles .= 'background-position-x:' . get_option( 'realhomes_wp_login_page_background_image_position_x', 'center' ) . ';';
			$page_styles .= 'background-position-y:' . get_option( 'realhomes_wp_login_page_background_image_position_y', 'center' ) . ';';

			// Background Behaviour
			$bg_attachment = get_option( 'realhomes_wp_login_page_background_attachment' );
			if ( ! empty( $bg_attachment ) ) {
				$page_styles .= 'background-attachment: scroll;';
			} else {
				$page_styles .= 'background-attachment: fixed;';
			}
		}

		$page_styles .= '}';

		// Overlay Color
		$overlay_color = get_option( 'realhomes_wp_login_page_background_overlay_color', '#3582c4' );
		if ( ! empty( $overlay_color ) ) {
			$page_styles .= '#login, .language-switcher { position: relative; z-index: 2; }';
			$page_styles .= 'body.login::before {
                        content: "";
                        position: fixed;
                        top: 0;
                        left: 0;
                        z-index: 1;
                        width: 100%;
                        height: 100%;
                        background-color: ' . $overlay_color . ';
                        }';

			$opacity = get_option( 'realhomes_wp_login_page_background_opacity', '5' );
			if ( ! empty( $opacity ) ) {
				$page_styles .= 'body.login::before{ opacity: calc(' . $opacity . '/100); }';
			}
		}

		if ( 'hide' === get_option( 'realhomes_wp_login_page_logo_display', 'show' ) ) {
			$page_styles .= 'body.login #login .wp-login-logo { display: none; }';
		}

		$page_styles .= 'body.login #login .wp-login-logo a {';

		if ( 'image' === get_option( 'realhomes_wp_login_page_logo_type', 'image' ) ) {
			$logo = get_option( 'realhomes_wp_login_page_logo' );

			$page_styles .= 'background-repeat: no-repeat; background-position: center;';
			if ( ! empty( $logo ) ) {
				$page_styles .= 'background-image: url(' . esc_url( $logo ) . ');';

				$logo_width = get_option( 'realhomes_wp_login_page_logo_width' );
				if ( ! empty( $logo_width ) ) {
					$page_styles .= 'background-size:' . $logo_width . ';';
					$page_styles .= 'width:' . $logo_width . ';';
				}

				$logo_height = get_option( 'realhomes_wp_login_page_logo_height' );
				if ( ! empty( $logo_height ) ) {
					$page_styles .= 'height:' . $logo_height . ';';
				}

			} else {
				$page_styles .= 'background-image: url(' . get_template_directory_uri() . '/assets/ultra/images/logo.png' . ');';
				$page_styles .= 'background-size:119px;';
				$page_styles .= 'width:119px;';
				$page_styles .= 'height:62px;';
			}

		} else {
			$page_styles .= 'background: transparent;
              height: auto;
              width: auto;
              text-indent: 0;
              font-size: 36px;
              font-weight: bold;
            ';
			$page_styles .= 'color: ' . get_option( 'realhomes_wp_login_page_site_title_color', '#3c434a' ) . ';';
		}

		$logo_top_space = get_option( 'realhomes_wp_login_page_logo_top_space' );
		if ( ! empty( $logo_top_space ) ) {
			$page_styles .= 'margin-top:' . $logo_top_space . ';';
		}

		$logo_bottom_space = get_option( 'realhomes_wp_login_page_logo_bottom_space' );
		if ( ! empty( $logo_bottom_space ) ) {
			$page_styles .= 'margin-bottom:' . $logo_bottom_space . ';';
		}

		$page_styles .= '}';

		// Form Controls
		$page_styles .= 'body.login form {';
		$page_styles .= 'background-color: ' . get_option( 'realhomes_wp_login_page_form_bg_color', '#fff' ) . ';';

		$form_border_color = get_option( 'realhomes_wp_login_page_form_border_color' );
		if ( ! empty( $form_border_color ) ) {
			$page_styles .= 'border-color: ' . $form_border_color . ';';
		}

		$form_border_width = get_option( 'realhomes_wp_login_page_form_border_width' );
		if ( ! empty( $form_border_width ) ) {
			$page_styles .= 'border-width: ' . $form_border_width . ';';
		}

		$form_border_radius = get_option( 'realhomes_wp_login_page_form_border_radius' );
		if ( ! empty( $form_border_radius ) ) {
			$page_styles .= 'border-radius: ' . $form_border_radius . ';';
		}

		$page_styles .= '}';

		$page_styles .= 'body.login label {';
		$page_styles .= 'color: ' . get_option( 'realhomes_wp_login_page_form_label_color', '#3c434af' ) . ';';
		$page_styles .= '}';

		// Input Fields
		$font_size = get_option( 'realhomes_wp_login_page_input_font_size', '24px' );
		if ( ! empty( $font_size ) ) {
			$page_styles .= 'body.login form .input, body.login form input[type=checkbox], body.login input[type=password], body.login input[type=text] {';
			$page_styles .= 'font-size: ' . $font_size . ';';
			$page_styles .= '}';
		}

		$page_styles .= 'body.login form .input, body.login form input[type=checkbox], body.login input[type=text] {';
		$page_styles .= 'background-color: ' . get_option( 'realhomes_wp_login_page_input_bg_color', '#fff' ) . ';';
		$page_styles .= 'border-color: ' . get_option( 'realhomes_wp_login_page_input_border_color', '#ccc' ) . ';';
		$page_styles .= '}';

		$page_styles .= 'body.login form .input, .dashicons-visibility:before, .dashicons-hidden:before {';
		$page_styles .= 'color: ' . get_option( 'realhomes_wp_login_page_input_text_color', '#000' ) . ';';
		$page_styles .= '}';

		$page_styles .= 'body.login form .input, body.login input[type=password], body.login input[type=text] {';

		$input_border_width = get_option( 'realhomes_wp_login_page_input_border_width' );
		if ( ! empty( $input_border_width ) ) {
			$page_styles .= 'border-width:' . $input_border_width . ';';
		}

		$input_border_radius = get_option( 'realhomes_wp_login_page_input_border_radius' );
		if ( ! empty( $input_border_radius ) ) {
			$page_styles .= 'border-radius:' . $input_border_radius . ';';
		}

		$page_styles .= '}';

		// Button Styles
		$page_styles .= 'body.login #wp-submit, .login .language-switcher .button {';
		$page_styles .= 'color: ' . get_option( 'realhomes_wp_login_page_button_text_color', '#fff' ) . ';';
		$page_styles .= 'background-color: ' . get_option( 'realhomes_wp_login_page_button_bg_color', '#0073aa' ) . ';';
		$page_styles .= 'border-color: ' . get_option( 'realhomes_wp_login_page_button_border_color', '#0073aa' ) . ';';
		$page_styles .= 'transition: all 0.25s linear;';

		$button_border_width = get_option( 'realhomes_wp_login_page_button_border_width' );
		if ( ! empty( $button_border_width ) ) {
			$page_styles .= 'border-width:' . $button_border_width . ';';
		}

		$button_border_radius = get_option( 'realhomes_wp_login_page_button_border_radius' );
		if ( ! empty( $button_border_radius ) ) {
			$page_styles .= 'border-radius:' . $button_border_radius . ';';
		}

		$page_styles .= '}';

		$page_styles .= 'body.login #wp-submit {';
		$button_padding = get_option( 'realhomes_wp_login_page_button_padding' );
		if ( ! empty( $button_padding ) ) {
			$page_styles .= 'padding:' . $button_padding . ';';
		}
		$page_styles .= '}';

		// Button Hover Styles
		$page_styles .= 'body.login #wp-submit:hover, .login .language-switcher .button:hover {';
		$page_styles .= 'color: ' . get_option( 'realhomes_wp_login_page_button_hover_text_color', '#fff' ) . ';';
		$page_styles .= 'background-color: ' . get_option( 'realhomes_wp_login_page_button_hover_bg_color', '#005177' ) . ';';
		$page_styles .= 'border-color: ' . get_option( 'realhomes_wp_login_page_button_hover_border_color', '#005177' ) . ';';
		$page_styles .= '}';
		?>
        <style><?php echo $page_styles; ?></style>
		<?php
	}

	add_action( 'login_enqueue_scripts', 'custom_login_styles' );
}

if ( ! function_exists( 'alert' ) ) {
	/**
	 * Alert function to display messages on member pages
	 *
	 * @param string $heading
	 * @param string $message
	 */
	function alert( $heading = '', $message = '' ) {
		echo '<div class="inspiry-message">';
		echo '<strong>' . $heading . '</strong> <span>' . $message . '</span>';
		echo '</div>';
	}
}

if ( ! function_exists( 'display_notice' ) ) {
	/**
	 * display_notice function to display messages on member pages
	 *
	 * @param array $notices
	 *
	 * @return bool|mixed
	 */
	function display_notice( $notices = array() ) {

		if ( ! is_array( $notices ) || empty( $notices ) ) {
			return false;
		}

		echo '<div class="inspiry-message">';
		foreach ( $notices as $notice ) {
			echo '<strong>' . esc_html( $notice['heading'] ) . '</strong> ';
			echo '<span>';
			echo ( ! empty( $notice['message'] ) ) ? esc_html( $notice['message'] ) : esc_html__( 'No more properties are available.', RH_TEXT_DOMAIN );
			echo '</span><br>';
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'modify_user_contact_methods' ) ) {
	/**
	 * Add Additional Contact Info to User Profile Page
	 *
	 * @param $user_contactmethods
	 *
	 * @return mixed
	 */
	function modify_user_contact_methods( $user_contactmethods ) {
		$user_contactmethods['mobile_number']   = esc_html__( 'Mobile Number', RH_TEXT_DOMAIN );
		$user_contactmethods['office_number']   = esc_html__( 'Office Number', RH_TEXT_DOMAIN );
		$user_contactmethods['fax_number']      = esc_html__( 'Fax Number', RH_TEXT_DOMAIN );
		$user_contactmethods['whatsapp_number'] = esc_html__( 'WhatsApp Number', RH_TEXT_DOMAIN );
		$user_contactmethods['facebook_url']    = esc_html__( 'Facebook URL', RH_TEXT_DOMAIN );
		$user_contactmethods['twitter_url']     = esc_html__( 'Twitter URL', RH_TEXT_DOMAIN );
		$user_contactmethods['linkedin_url']    = esc_html__( 'LinkedIn URL', RH_TEXT_DOMAIN );
		$user_contactmethods['instagram_url']   = esc_html__( 'Instagram URL', RH_TEXT_DOMAIN );
		$user_contactmethods['pinterest_url']   = esc_html__( 'Pinterest URL', RH_TEXT_DOMAIN );
		$user_contactmethods['youtube_url']     = esc_html__( 'Youtube URL', RH_TEXT_DOMAIN );

		return $user_contactmethods;
	}

	add_filter( 'user_contactmethods', 'modify_user_contact_methods' );
}

if ( ! function_exists( 'get_icon_for_extension' ) ) {
	/**
	 * Fontawsome icon based on file extension
	 *
	 * @param $ext
	 *
	 * @return string
	 */
	function get_icon_for_extension( $ext ) {
		switch ( $ext ) {
			/* PDF */
			case 'pdf':
				return '<i class="far fa-file-pdf"></i>';

			/* Images */
			case 'jpg':
			case 'png':
			case 'gif':
			case 'bmp':
			case 'jpeg':
			case 'tiff':
			case 'tif':
				return '<i class="far fa-file-image"></i>';

			/* Text */
			case 'txt':
			case 'log':
			case 'tex':
				return '<i class="far fa-file-alt"></i>';

			/* Documents */
			case 'doc':
			case 'odt':
			case 'msg':
			case 'docx':
			case 'rtf':
			case 'wps':
			case 'wpd':
			case 'pages':
				return '<i class="far fa-file-word"></i>';

			/* Spread Sheets */
			case 'csv':
			case 'xlsx':
			case 'xls':
			case 'xml':
			case 'xlr':
				return '<i class="far fa-file-excel"></i>';

			/* Zip */
			case 'zip':
			case 'rar':
			case '7z':
			case 'zipx':
			case 'tar.gz':
			case 'gz':
			case 'pkg':
				return '<i class="far fa-file-archive"></i>';

			/* Audio */
			case 'mp3':
			case 'wav':
			case 'm4a':
			case 'aif':
			case 'wma':
			case 'ra':
			case 'mpa':
			case 'iff':
			case 'm3u':
				return '<i class="far fa-file-audio"></i>';

			/* Video */
			case 'avi':
			case 'flv':
			case 'm4v':
			case 'mov':
			case 'mp4':
			case 'mpg':
			case 'rm':
			case 'swf':
			case 'wmv':
				return '<i class="far fa-file-video"></i>';

			/* Others */
			default:
				return '<i class="far fa-file"></i>';
		}
	}
}

if ( ! function_exists( 'realhomes_ultra_get_icon_for_extension' ) ) {
	/**
	 * SVG icons based on file extension
	 *
	 * @param $ext
	 *
	 * @return string
	 */
	function realhomes_ultra_get_icon_for_extension( $ext ) {
		switch ( $ext ) {
			/* PDF */
			case 'pdf':
				return 'pdf.svg';

			/* Images */
			case 'jpg':
			case 'png':
			case 'gif':
			case 'bmp':
			case 'jpeg':
			case 'tiff':
			case 'tif':
				return '<i class="far fa-file-image"></i>';

			/* Text */
			case 'txt':
			case 'log':
			case 'tex':
				return '<i class="far fa-file-alt"></i>';

			/* Documents */
			case 'doc':
			case 'odt':
			case 'msg':
			case 'docx':
			case 'rtf':
			case 'wps':
			case 'wpd':
			case 'pages':
				return '<i class="far fa-file-word"></i>';

			/* Spread Sheets */
			case 'csv':
			case 'xlsx':
			case 'xls':
			case 'xml':
			case 'xlr':
				return '<i class="far fa-file-excel"></i>';

			/* Zip */
			case 'zip':
			case 'rar':
			case '7z':
			case 'zipx':
			case 'tar.gz':
			case 'gz':
			case 'pkg':
				return '<i class="far fa-file-archive"></i>';

			/* Audio */
			case 'mp3':
			case 'wav':
			case 'm4a':
			case 'aif':
			case 'wma':
			case 'ra':
			case 'mpa':
			case 'iff':
			case 'm3u':
				return '<i class="far fa-file-audio"></i>';

			/* Video */
			case 'avi':
			case 'flv':
			case 'm4v':
			case 'mov':
			case 'mp4':
			case 'mpg':
			case 'rm':
			case 'swf':
			case 'wmv':
				return '<i class="far fa-file-video"></i>';

			/* Others */
			default:
				return '<i class="far fa-file"></i>';
		}
	}
}

if ( ! function_exists( 'inspiry_image_placeholder' ) ) {
	/**
	 * Output image place holder for given size
	 *
	 * @param string $image_size - Image size.
	 */
	function inspiry_image_placeholder( $image_size ) {
		echo get_inspiry_image_placeholder( $image_size );
	}
}

if ( ! function_exists( 'get_inspiry_image_placeholder' ) ) {
	/**
	 * Returns image place holder for given size
	 *
	 * @param string $image_size - Image size.
	 *
	 * @return string - Image HTML
	 */
	function get_inspiry_image_placeholder( $image_size ) {

		if ( empty( $image_size ) ) {
			return '';
		}

		// Get custom placeholder image if configured.
		$placeholder_custom_image_url = get_option( 'inspiry_properties_placeholder_image' );
		if ( ! empty( $placeholder_custom_image_url ) ) {
			$placeholder_custom_image_id = attachment_url_to_postid( $placeholder_custom_image_url );
			if ( ! empty( $placeholder_custom_image_id ) ) {
				return wp_get_attachment_image( $placeholder_custom_image_id, $image_size, false, '' );
			}

			// Otherwise get default placeholder
		} else {
			$placeholder_image_url = inspiry_get_raw_placeholder_url( $image_size );
			if ( ! empty( $placeholder_image_url ) ) {
				return sprintf( '<img src="%s" alt="%s">', esc_url( $placeholder_image_url ), the_title_attribute( 'echo=0' ));
			}
		}

		return '';
	}
}

if ( ! function_exists( 'get_inspiry_image_placeholder_url' ) ) {
	/**
	 * Returns the URL of placeholder image.
	 *
	 * @since 3.1.0
	 *
	 * @param string $image_size - Image size.
	 *
	 * @return string|boolean - URL of the placeholder OR `false` on failure.
	 */
	function get_inspiry_image_placeholder_url( $image_size ) {

		// Get custom placeholder image if configured.
		$placeholder_custom_image_url = get_option( 'inspiry_properties_placeholder_image' );
		if ( ! empty( $placeholder_custom_image_url ) ) {
			$placeholder_custom_image_id = attachment_url_to_postid( $placeholder_custom_image_url );
			if ( ! empty( $placeholder_custom_image_id ) ) {
				return wp_get_attachment_image_url( $placeholder_custom_image_id, $image_size, false );
			}
		}

		return inspiry_get_raw_placeholder_url( $image_size );
	}
}

if ( ! function_exists( 'inspiry_get_raw_placeholder_url' ) ) {
	/**
	 * Get the URL of a local placeholder image for the given size.
	 *
	 * @since 3.10.2
	 *
	 * @param string $image_size Image size slug (without file extension).
	 * @return string|false Placeholder image URL, or false if not found.
	 */
	function inspiry_get_raw_placeholder_url( $image_size ) {
		if ( empty( $image_size ) ) {
			return false;
		}

		$relative_path = '/common/images/placeholders/' . sanitize_file_name( $image_size ) . '.jpg';

		return file_exists( get_template_directory() . $relative_path ) ? get_template_directory_uri() . $relative_path : false;
	}
}

if ( ! function_exists( 'inspiry_log' ) ) {
	/**
	 * Function to help in debugging
	 *
	 * @param $message
	 */
	function inspiry_log( $message ) {
		if ( WP_DEBUG === true ) {
			if ( is_array( $message ) || is_object( $message ) ) {
				error_log( print_r( $message, true ) );
			} else {
				error_log( $message );
			}
		}
	}
}

if ( ! function_exists( 'inspiry_get_maps_type' ) ) {
	/**
	 * Returns the type currently available for use.
	 */
	function inspiry_get_maps_type() {

		$map_service         = get_option( 'ere_theme_map_type', 'open-street-map' );
		$google_maps_api_key = get_option( 'inspiry_google_maps_api_key', false );
		$mapbox_api_key      = get_option( 'ere_mapbox_api_key', false );

		if ( $map_service === 'googlemaps' && ! empty( $google_maps_api_key ) ) {

			return 'google-maps';    // For Google Maps

		} else if ( $map_service === 'mapbox' && ! empty( $mapbox_api_key ) ) {

			return 'mapbox';    // For Mapbox https://docs.mapbox.com/

		}

		return 'open-street-map';    // For OpenStreetMap https://www.openstreetmap.org/
	}
}

if ( ! function_exists( 'inspiry_is_map_needed' ) ) {
	/**
	 * Check if google map script is needed or not
	 *
	 * @return bool
	 */
	function inspiry_is_map_needed() {

		if ( is_page_template( 'templates/contact.php' ) && ( get_post_meta( get_the_ID(), 'theme_show_contact_map', true ) == '1' ) ) {
			return true;
		} else if ( is_page_template( array( 'templates/dashboard.php' ) ) ) {
			return true;
		} else if ( is_singular( 'property' ) && ( get_option( 'theme_display_google_map' ) == 'true' ) ) {
			return true;
		} else if ( is_page_template( 'templates/home.php' ) ) {
			return true;
		} else if ( is_page_template( 'templates/properties-search.php' ) ) {
			$theme_search_module = get_option( 'theme_search_module', 'properties-map' );
			if ( 'classic' === INSPIRY_DESIGN_VARIATION && ( 'properties-map' == $theme_search_module ) ) {
				return true;
			} else if ( 'classic' === INSPIRY_DESIGN_VARIATION && ( 'simple-banner' == $theme_search_module ) ) {
				return false;
			} else {
				return true;
			}
		} else if ( is_page_template( array( 'templates/properties.php' ) )
			|| is_tax( 'property-city' )
			|| is_tax( 'property-status' )
			|| is_tax( 'property-type' )
			|| is_tax( 'property-feature' )
			|| is_post_type_archive( 'property' )
		) {
			// Theme Listing Page Module
			$theme_listing_module = get_option( 'theme_listing_module' );
			// Only for demo purpose only
			if ( isset( $_GET['module'] ) ) {
				$theme_listing_module = $_GET['module'];
			}

			if ( 'classic' !== INSPIRY_DESIGN_VARIATION && realhomes_is_half_map_template( 'property' ) ) {
				$theme_listing_module = 'properties-map';
			}

			if ( $theme_listing_module == 'properties-map' ) {
				return true;
			}
		} else if ( class_exists( 'RealHomes_Elementor_Addon' ) ) {
			if (
				'classic' !== INSPIRY_DESIGN_VARIATION
				&& 'google-maps' === inspiry_get_maps_type()
				&& inspiry_is_rvr_enabled()
				&& realhomes_is_location_type_geolocation()
			) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_map_needed_for_address' ) ) {

	function inspiry_map_needed_for_address() {
		$inspiry_address_lightbox_enable = get_option( 'inspiry_address_lightbox_enable', 'enable' );

		if ( 'enable' === $inspiry_address_lightbox_enable ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_hex_to_rgba' ) ) {

	/**
	 * Convert hexdec color string to rgb(a) string
	 *
	 * @since 2.6.2
	 *
	 * @param string $color      - Color string in rgb.
	 * @param float  $opacity    - Opacity of the color.
	 * @param bool   $value_only - Returns only rgb values.
	 *
	 * @return string
	 */
	function inspiry_hex_to_rgba( $color, $opacity = false, $value_only = false ) {

		$default = '';

		// Return default if no color provided
		if ( empty( $color ) ) {
			return $default;
		}

		// Sanitize $color if "#" is provided
		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		// Check if color has 6 or 3 characters and get values
		if ( strlen( $color ) == 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} else if ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		// Convert hexadec to rgb
		$rgb = array_map( 'hexdec', $hex );

		// Check if opacity is set(rgba or rgb)
		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
		} else if ( $value_only ) {
			$output = implode( ",", $rgb );
		} else {
			$output = 'rgb(' . implode( ",", $rgb ) . ')';
		}

		// Return rgb(a) color string
		return $output;
	}
}

if ( ! function_exists( 'inspiry_hex_darken' ) ) {

	/**
	 * Function: Returns the hex color darken to percentage.
	 *
	 * @since 3.5.0
	 *
	 * @param string $hex     - hex color.
	 * @param int    $percent - percentage in number without % symbol.
	 *
	 * @return string
	 */

	function inspiry_hex_darken( $hex, $percent = 0 ) {
		$color = '';
		if ( ! empty( $hex ) ) {
			preg_match( '/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $primary_colors );
			str_replace( '%', '', $percent );
			$color = "#";
			for ( $i = 1; $i <= 3; $i++ ) {
				$primary_colors[ $i ] = hexdec( $primary_colors[ $i ] );
				$primary_colors[ $i ] = round( $primary_colors[ $i ] * ( 100 - ( $percent * 2 ) ) / 100 );
				$color                .= str_pad( dechex( $primary_colors[ $i ] ), 2, '0', STR_PAD_LEFT );
			}
		}

		return $color;
	}
}

if ( ! function_exists( 'inspiry_author_properties_count' ) ) {
	/**
	 * Function: Returns the number listed properties of an author.
	 *
	 * @since 3.3.2
	 *
	 * @param int $author_id - Author ID for properties.
	 *
	 * @return integer
	 */
	function inspiry_author_properties_count( $author_id ) {

		if ( empty( $author_id ) ) {
			return 0;
		}

		$properties_args = array(
			'post_type'      => 'property',
			'posts_per_page' => -1,
			'author'         => $author_id,
			'meta_query'     => array(
				array(
					'key'     => 'REAL_HOMES_agent_display_option',
					'value'   => 'my_profile_info',
					'compare' => '=',
				),
			),
		);

		$properties = new WP_Query( $properties_args );
		if ( $properties->have_posts() ) {
			return $properties->found_posts;
		}

		return 0;
	}
}

if ( ! function_exists( 'inspiry_filter_excerpt_more' ) ) {

	/**
	 * Filter the more text of excerpt.
	 *
	 * @since  3.0.0
	 *
	 * @param string $more - More string of the excerpt.
	 *
	 * @return string
	 */
	function new_excerpt_more( $more ) {
		return '...';
	}

	add_filter( 'excerpt_more', 'new_excerpt_more' );
}

if ( ! function_exists( 'inspiry_backend_safe_string' ) ) {
	/**
	 * Create a lower case version of a string without spaces so we can use that string for database settings
	 *
	 * @param string $string to convert
	 *
	 * @return string the converted string
	 */
	function inspiry_backend_safe_string( $string, $replace = "_", $check_spaces = false ) {
		$string = strtolower( $string );

		$trans = array(
			'&\#\d+?;'       => '',
			'&\S+?;'         => '',
			'\s+'            => $replace,
			'ä'              => 'ae',
			'ö'              => 'oe',
			'ü'              => 'ue',
			'Ä'              => 'Ae',
			'Ö'              => 'Oe',
			'Ü'              => 'Ue',
			'ß'              => 'ss',
			'[^a-z0-9\-\._]' => '',
			$replace . '+'   => $replace,
			$replace . '$'   => $replace,
			'^' . $replace   => $replace,
			'\.+$'           => ''
		);

		$trans = apply_filters( 'inspiry_safe_string_trans', $trans, $string, $replace );

		$string = strip_tags( $string );

		foreach ( $trans as $key => $val ) {
			$string = preg_replace( "#" . $key . "#i", $val, $string );
		}

		if ( $check_spaces ) {
			if ( str_replace( '_', '', $string ) == '' ) {
				return;
			}
		}

		return stripslashes( $string );
	}
}

if ( ! function_exists( 'inspiry_hex2rgb' ) ) {
	/***
	 * Converts Hexadecimal color code to RGB
	 *
	 * @param     $colour
	 * @param int $opacity
	 *
	 * @return bool|string
	 */
	function inspiry_hex2rgb( $colour, $opacity = 1 ) {
		if ( isset( $colour[0] ) && $colour[0] == '#' ) {
			$colour = substr( $colour, 1 );
		}
		if ( strlen( $colour ) == 6 ) {
			list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
		} else if ( strlen( $colour ) == 3 ) {
			list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
		} else {
			return false;
		}
		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );

		return "rgba({$r},{$g},{$b},{$opacity})";
	}
}

if ( ! function_exists( 'inspiry_get_exploded_heading' ) ) {
	/**
	 * Returned exploded title into title and sub-title.
	 * (Modern design specific)
	 *
	 * @param $page_title
	 *
	 */
	function inspiry_get_exploded_heading( $page_title ) {

		$explode_title = get_option( 'inspiry_explode_listing_title', 'yes' );

		if ( 'yes' == $explode_title ) {
			$page_title = explode( ' ', $page_title, 2 );

			if ( ! empty( $page_title ) && ( 1 < count( $page_title ) ) ) {
				?>
                <span class="sub"><?php echo esc_html( $page_title[0] ); ?></span><span class="title"><?php echo esc_html( $page_title[1] ); ?></span>
				<?php
			} else {
				?><span class="title"><?php echo esc_html( $page_title[0] ); ?></span><?php
			}

		} else {
			?><span class="title"><?php echo esc_html( $page_title ); ?></span><?php
		}
	}
}

if ( ! function_exists( 'inspiry_is_gdpr_enabled' ) ) {
	/**
	 * Check if GDPR is enabled on forms
	 * @return bool
	 */
	function inspiry_is_gdpr_enabled() {

		$inspiry_gdpr = intval( get_option( 'inspiry_gdpr', '0' ) );

		if ( $inspiry_gdpr ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_gdpr_agreement_content' ) ) {
	/**
	 * Return GDPR field label text
	 *
	 * @param string $type
	 *
	 * @return mixed|void
	 */
	function inspiry_gdpr_agreement_content( $type = 'text' ) {

		if ( 'label' == $type ) {
			$gdpr_agreement_content = get_option( 'inspiry_gdpr_label', esc_html__( 'GDPR Agreement', RH_TEXT_DOMAIN ) );
		} else if ( 'validation-message' == $type ) {
			$gdpr_agreement_content = get_option( 'inspiry_gdpr_validation_text', esc_html__( '* Please accept GDPR agreement', RH_TEXT_DOMAIN ) );
		} else {
			$gdpr_agreement_content = get_option( 'inspiry_gdpr_text', esc_html__( 'I consent to having this website store my submitted information so they can respond to my inquiry.', RH_TEXT_DOMAIN ) );
		}

		return $gdpr_agreement_content;
	}
}

if ( ! function_exists( 'inspiry_is_rvr_enabled' ) ) {
	/**
	 * Check if Realhomes Vacation Rentals plugin is activated and enabled
	 *
	 * @return bool
	 */
	function inspiry_is_rvr_enabled(): bool {
		if ( class_exists( 'Realhomes_Vacation_Rentals' ) ) {
			$rvr_settings = get_option( 'rvr_settings' );

			return isset( $rvr_settings['rvr_activation'] ) && $rvr_settings['rvr_activation'];
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_is_paypal_payments_enabled' ) ) { // TODO: move all such enable check functions to an independent file.
	/**
	 * Check if RealHomes PayPal Payments plugin is activated and enabled
	 *
	 * @since 4.3.0
	 * @return bool
	 */
	function realhomes_is_paypal_payments_enabled() {
		$rpp_settings = get_option( 'rpp_settings' );

		if ( ! empty( $rpp_settings['enable_paypal'] ) && class_exists( 'Realhomes_Paypal_Payments' ) ) {
			if ( ! empty( $rpp_settings['payment_amount'] ) &&
				! empty( $rpp_settings['currency_code'] ) &&
				! empty( $rpp_settings['client_id'] ) &&
				! empty( $rpp_settings['secret_id'] ) ) {

				return true;
			}

			return false;
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_is_stripe_payments_enabled' ) ) { // TODO: move all such enable check functions to an independent file.
	/**
	 * Check if RealHomes Stripe Payments plugin is activated and enabled
	 *
	 * @since 4.3.0
	 * @return bool
	 */
	function realhomes_is_stripe_payments_enabled() {
		$isp_settings = get_option( 'isp_settings' );

		if ( ! empty( $isp_settings['enable_stripe'] ) && class_exists( 'Inspiry_Stripe_Payments' ) ) {
			if ( ! empty( $isp_settings['amount'] ) &&
				! empty( $isp_settings['currency_code'] ) &&
				! empty( $isp_settings['publishable_key'] ) &&
				! empty( $isp_settings['secret_key'] )
			) {
				return true;
			}

			return false;
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_is_rhwpa_payments_enabled' ) ) { // TODO: move all such enable check functions to an independent file.
	/**
	 * Check if RealHomes WooCommerce Payments Addon plugin is activated and enabled
	 *
	 * @since 4.3.0
	 * @return bool
	 */
	function realhomes_is_rhwpa_payments_enabled() {
		$rhwpa_settings = get_option( 'rhwpa_property_payment_settings' );

		if ( class_exists( 'WooCommerce' ) && class_exists( 'Realhomes_WC_Payments_Addon' ) && ! empty( $rhwpa_settings['enable_wc_payments'] ) && ! empty( $rhwpa_settings['amount'] ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_show_rvr_availability_calendar' ) ) {
	/**
	 * Checks for allowed property statuses to show availability calendar.
	 *
	 * @since 3.13.0
	 *
	 * @return bool
	 */
	function inspiry_show_rvr_availability_calendar() {

		// Get property statuses to show availability calendar.
		$property_statuses = ( array )get_option( 'inspiry_statuses_to_show_availability_calendar', array() );
		$property_statuses = array_filter( $property_statuses );
		if ( ! empty( $property_statuses ) ) {

			// Get current property statuses
			$current_statuses = get_the_terms( get_the_ID(), 'property-status' );
			if ( ! empty( $current_statuses ) && ! is_wp_error( $current_statuses ) ) {
				$show = false;
				foreach ( $current_statuses as $current_status ) {
					// Stop if current status exists in allowed statuses.
					if ( in_array( $current_status->term_id, $property_statuses ) ) {
						$show = true;
						break;
					}
				}

				return $show;
			}
		}

		return true;
	}
}

if ( ! function_exists( 'inspiry_language_switcher' ) ) {
	/**
	 * Display language list of selected WPML languages.
	 *
	 * @since 3.6.1
	 */
	function inspiry_language_switcher() {
		echo inspiry_get_language_switcher();
	}
}

if ( ! function_exists( 'inspiry_get_language_switcher' ) ) {
	/**
	 * Retrieve language list of selected WPML languages.
	 *
	 * @since 3.6.1
	 */
	function inspiry_get_language_switcher() {

		if ( function_exists( 'wpml_get_active_languages_filter' ) ) {

			$inspiry_language_switcher = get_option( 'theme_wpml_lang_switcher', 'true' );

			if ( 'true' === $inspiry_language_switcher ) {

				$languages = wpml_get_active_languages_filter( null, null );

				if ( ! empty( $languages ) ) {

					$switcher_language_display = get_option( 'theme_switcher_language_display', 'language_name_and_flag' );
					$active_language           = '';
					$languages_list            = '';

					foreach ( $languages as $language ) {
						$code             = $language['code'];
						$native_name      = $language['native_name'];
						$language_code    = $language['language_code'];
						$country_flag_url = $language['country_flag_url'];

						$language_name_in_current_language = get_option( 'theme_switcher_language_name_in_current_language', 'native_name' );
						if ( 'translated_name' === $language_name_in_current_language ) {
							$native_name = $language['translated_name'];
						}

						if ( ! $language['active'] ) {
							$languages_list .= '<li class="inspiry-language ' . esc_attr( $code ) . '">';
							$languages_list .= '<a class="inspiry-language-link" href="' . esc_url( $language['url'] ) . '">';

							if ( 'language_flag_only' === $switcher_language_display ) {
								$languages_list .= '<img src="' . esc_url( $country_flag_url ) . '" alt="' . esc_attr( $language_code ) . '" />';
							} else if ( 'language_name_only' == $switcher_language_display ) {
								$languages_list .= '<span class="inspiry-language-native">' . esc_html( $native_name ) . '</span>';

							} else {
								$languages_list .= '<img src="' . esc_url( $country_flag_url ) . '" alt="' . esc_attr( $language_code ) . '" />';
								$languages_list .= '<span class="inspiry-language-native">' . esc_html( $native_name ) . '</span>';
							}
							$languages_list .= '</a>';
							$languages_list .= '</li>';
						} else {
							$active_language .= '<li class="inspiry-language ' . esc_attr( $code ) . ' current" >';
							if ( 'language_flag_only' === $switcher_language_display ) {
								$active_language .= '<img class="inspiry-no-language-name" src="' . esc_url( $country_flag_url ) . '" alt="' . esc_attr( $language_code ) . '" />';
							} else if ( 'language_name_only' == $switcher_language_display ) {
								$active_language .= '<span class="inspiry-language-native inspiry-no-language-flag">' . esc_html( $native_name ) . '</span>';
							} else {
								$active_language .= '<img src="' . esc_url( $country_flag_url ) . '" alt="' . esc_attr( $language_code ) . '" />';
								$active_language .= '<span class="inspiry-language-native">' . esc_html( $native_name ) . '</span>';
							}
						}
					}

					$html = '<div class="inspiry-language-switcher"><ul>';
					$html .= $active_language;

					if ( ! empty( $languages_list ) ) {
						$html .= '<ul class="rh_languages_available">' . $languages_list . '</ul>';
					}

					$html .= '</li></ul></div>';

					return $html;
				}
			}
		}
	}
}

if ( ! function_exists( 'inspiry_property_qr_code' ) ) {
	/**
	 * Display QR code image generated with google chart API.
	 */
	function inspiry_property_qr_code() {
		$qr_code_url = sprintf( 'https://qrcode.tec-it.com/API/QRCode?size=small&data=%s',  urlencode( get_the_permalink() ) );
		printf( '<img class="only-for-print inspiry-qr-code" src="%s" alt="%s">', esc_url( $qr_code_url ), the_title_attribute( 'echo=0' ) );
	}
}

if ( ! function_exists( 'realhomes_featured_label' ) ) {
	/**
	 * Display Featured label text.
	 *
	 * @since 3.21.0
	 */
	function realhomes_featured_label() {

		$featured_label = get_option( 'realhomes_featured_label' );

		if ( empty( $featured_label ) ) {
			$featured_label = esc_html__( 'Featured', RH_TEXT_DOMAIN );
		}

		echo esc_html( $featured_label );

	}
}

if ( ! function_exists( 'inspiry_property_detail_page_link_text' ) ) {
	/**
	 * Display property detail page link text.
	 */
	function inspiry_property_detail_page_link_text() {
		echo get_option( 'inspiry_property_detail_page_link_text', esc_html__( 'View Property', RH_TEXT_DOMAIN ) );
	}
}

if ( ! function_exists( 'inspiry_embed_code_allowed_html' ) ) {
	/**
	 * @return array Array of allowed tags for embed code.
	 */
	function inspiry_embed_code_allowed_html() {
		$embed_code_allowed_html = wp_kses_allowed_html( 'post' );

		// iframe
		$embed_code_allowed_html['iframe'] = array(
			'src'             => array(),
			'height'          => array(),
			'width'           => array(),
			'frameborder'     => array(),
			'allowfullscreen' => array(),
			'allowvr'         => array(),
		);

		return apply_filters( 'inspiry_embed_code_allowed_html', $embed_code_allowed_html );
	}
}

if ( ! function_exists( 'inspiry_allowed_html' ) ) {
	/**
	 * @return array Array of allowed html tags.
	 */
	function inspiry_allowed_html() {

		$allowed_html = array(
			'a'      => array(
				'href'   => array(),
				'title'  => array(),
				'alt'    => array(),
				'target' => array(),
			),
			'b'      => array(),
			'br'     => array(),
			'div'    => array(
				'class' => array(),
				'id'    => array(),
			),
			'em'     => array(),
			'strong' => array(),
		);

		return apply_filters( 'inspiry_allowed_html', $allowed_html );
	}
}

if ( ! function_exists( 'inspiry_redirect' ) ) {
	/**
	 * Redirect to a page even header is sent already
	 *
	 * @param $url
	 */
	function inspiry_redirect( $url ) {
		$string = '<script type="text/javascript">';
		$string .= 'window.location = "' . esc_url( $url ) . '"';
		$string .= '</script>';

		echo wp_kses( $string, array(
			'script' => array(
				'type' => array()
			)
		) );
	}
}

if ( ! function_exists( 'inspiry_admin_body_classes' ) ) {
	/**
	 * Add classes to the body tag on admin side
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	function inspiry_admin_body_classes( $classes ) {

		$classes .= ' design_' . INSPIRY_DESIGN_VARIATION; // design variation

		return $classes;
	}

	add_filter( 'admin_body_class', 'inspiry_admin_body_classes' );
}

if ( ! function_exists( 'inspiry_body_classes' ) ) {
	/**
	 * Add classes to the body tag on frontend side
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	function inspiry_body_classes( $classes ) {

		// Adds current design variation class in body
		$classes[] = 'design_' . INSPIRY_DESIGN_VARIATION;

		// Body class to change theme elements border radius.
		if ( 'classic' !== INSPIRY_DESIGN_VARIATION && ( 'enable' === get_option( 'realhomes_round_corners', 'disable' ) ) ) {
			$classes[] = 'realhomes-round-corners';
		}

		if ( 'classic' !== INSPIRY_DESIGN_VARIATION && 'enable' === get_option( 'realhomes_mega_menu', 'disable' ) ) {
			$classes[] = 'rh-mega-menu-enabled';
		} else {
			$classes[] = 'rh-mega-menu-disabled';
		}

		// Classes for single property
		if ( is_singular( 'property' ) ) {
			if ( 'fullwidth' === get_option( 'inspiry_property_single_template', 'sidebar' ) ) {
				$classes[] = 'property-template-templates';
				$classes[] = 'property-template-property-full-width-layout';
				$classes   = array_diff( $classes, array( 'property-template-default' ) );
			}

			if ( 'modern' === INSPIRY_DESIGN_VARIATION ) {
				// Adding single property layout class.
				$classes[] = 'single-property-variation-' . get_option( 'realhomes_single_property_variation', 'default' );

				// Adding property content layout class.
				$single_property_content_layout = get_option( 'realhomes_single_property_content_layout', 'default' );
				$classes[]                      = 'property-content-layout-' . $single_property_content_layout;
				if ( 'horizontal-tabs' === $single_property_content_layout ) {
					$classes[] = 'property-content-section-' . get_option( 'realhomes_single_property_section_style', 'default' );
				}
			}

			if ( realhomes_is_custom_print_setting() ) {
				$classes[] = 'single-property-custom-print-styles';
			}
		}

		return $classes;
	}

	add_filter( 'body_class', 'inspiry_body_classes' );
}

if ( ! function_exists( 'inspiry_add_property_feed_data' ) ) {
	/**
	 * Adds Property important information to its feed
	 * Reference: https://wordpress.org/support/article/wordpress-feeds/
	 */
	function inspiry_add_property_feed_data() {

		if ( get_post_type() == 'property' ) {

			$property_id = get_the_ID();
			$meta_keys   = array(
				'REAL_HOMES_property_id',
				'REAL_HOMES_property_price',
				'REAL_HOMES_property_size',
				'REAL_HOMES_property_bedrooms',
				'REAL_HOMES_property_bathrooms',
				'REAL_HOMES_property_garage',
				'REAL_HOMES_property_year_built',
				'REAL_HOMES_property_location',
				'REAL_HOMES_property_address',
			);

			// add meta information to the feed
			echo "<meta>";
			foreach ( $meta_keys as $meta_key ) {
				if ( $meta_value = get_post_meta( $property_id, $meta_key, true ) ) {
					echo "<{$meta_key}>{$meta_value}</{$meta_key}>\n";
				}
			}
			echo "</meta>";


			$taxonomies = array(
				'property-city'    => array( 'locations', 'location' ),
				'property-status'  => array( 'statuses', 'status' ),
				'property-type'    => array( 'types', 'type' ),
				'property-feature' => array( 'features', 'feature' ),
			);

			// add taxonomies to the feed
			foreach ( $taxonomies as $taxonomy => $label ) {
				$terms = get_the_terms( $property_id, $taxonomy );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					echo "<{$label[0]}>";
					foreach ( $terms as $key => $term ) {
						echo "<{$label[1]}>{$term->name}</{$label[1]}>\n";
					}
					echo "</{$label[0]}>";
				}
			}
		}
	}

	add_action( 'rss2_item', 'inspiry_add_property_feed_data' );
}

if ( ! function_exists( 'inspiry_get_property_attachments' ) ) {
	/**
	 * Retrieves the property attachments.
	 *
	 * @since 3.12.2
	 *
	 * @param int $property_id
	 *
	 * @return array|mixed
	 */
	function inspiry_get_property_attachments( $property_id = 0 ) {

		if ( empty( $property_id ) ) {
			$property_id = get_the_ID();
		}

		$attachments = get_post_meta( $property_id, 'REAL_HOMES_attachments', false );
		if ( is_array( $attachments ) ) {
			$attachments = array_filter( $attachments );
			$attachments = array_unique( $attachments );
		}

		return $attachments;
	}
}

if ( ! function_exists( 'inspiry_walkscore' ) ) {
	/**
	 * Displays the property WalkScore.
	 *
	 * @since 3.10
	 *
	 * @param int $post_id Post ID
	 */
	function inspiry_walkscore( $post_id = 0 ) {
		$api_key = get_option( 'inspiry_walkscore_api_key' );
		if ( empty( $api_key ) ) {
			echo '<p class="ws-api-key-error">' . esc_html__( 'Walkscore API key is missing', RH_TEXT_DOMAIN ) . '</p>';

			return;
		}

		$id = get_the_ID();
		if ( ! empty( $post_id ) ) {
			$id = $post_id;
		}

		$property_address = get_post_meta( $id, 'REAL_HOMES_property_address', true );
		if ( empty( $property_address ) ) {
			return;
		}

		echo '<div id="ws-walkscore-tile"></div>';
		$data = "var ws_wsid    = '" . esc_html( $api_key ) . "';
                 var ws_address = '" . esc_html( $property_address ) . "';
                 var ws_format  = 'wide';
                 var ws_width   = '550';
                 var ws_width   = '100%';
                 var ws_height  = '350';";
		wp_enqueue_script( 'inspiry-walkscore', 'https://www.walkscore.com/tile/show-walkscore-tile.php', array(), null, true );
		wp_add_inline_script( 'inspiry-walkscore', $data, 'before' );
	}
}

if ( ! function_exists( 'inspiry_yelp_query_api' ) ) {
	/**
	 * Makes a request to the Yelp API based on a search term and location.
	 *
	 * @since 3.10
	 *
	 * @param string $key      Yelp Fusion API Key.
	 * @param string $term     The search term.
	 * @param string $location The location within which to search.
	 *
	 * @return bool|array Associative array containing the response body.
	 *
	 */
	function inspiry_yelp_query_api( $key, $term, $location ) {

		$url = add_query_arg(
			array(
				'term'     => $term,
				'location' => $location,
				'limit'    => intval( get_option( 'inspiry_yelp_search_limit', '3' ) ),
			),
			'https://api.yelp.com/v3/businesses/search'
		);

		$args = array(
			'user-agent' => '',
			'headers'    => array(
				'authorization' => 'Bearer ' . $key,
			),
		);

		$response = wp_safe_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		if ( ! empty( $response['body'] ) && is_ssl() ) {
			$response['body'] = str_replace( 'http:', 'https:', $response['body'] );
		}

		return json_decode( $response['body'] );
	}
}

if ( ! function_exists( 'inspiry_yelp_nearby_places' ) ) {
	/**
	 * Displays the Yelp Nearby Places.
	 *
	 * @since 3.10
	 */
	function inspiry_yelp_nearby_places() {
		echo inspiry_get_yelp_nearby_places();
	}
}

if ( ! function_exists( 'inspiry_get_yelp_nearby_places' ) ) {
	/**
	 * Returns the Yelp Nearby Places markup.
	 *
	 * @since 3.10
	 *
	 * @param int $post_id Post ID
	 *
	 * @return bool|mixed
	 *
	 */
	function inspiry_get_yelp_nearby_places( $post_id = 0 ) {

		$yelp_api_key = get_option( 'inspiry_yelp_api_key' );
		if ( empty( $yelp_api_key ) ) {
			printf( '<div class="yelp-error">%s</div>', esc_html__( 'Yelp API key is missing', RH_TEXT_DOMAIN ) );

			return false;
		}

		$id = get_the_ID();
		if ( ! empty( $post_id ) ) {
			$id = $post_id;
		}

		$property_location = get_post_meta( $id, 'REAL_HOMES_property_location', true );
		if ( empty( $property_location ) ) {
			return false;
		}

		$distance            = false;
		$current_lat         = '';
		$current_lng         = '';
		$property_location   = explode( ',', $property_location );
		$property_location   = $property_location[0] . ',' . $property_location[1];
		$yelp_terms          = get_option( 'inspiry_yelp_terms', array( 'education', 'realestate', 'health' ) );
		$yelp_distance_unit  = get_option( 'inspiry_yelp_distance_unit', 'mi' );
		$yelp_dist_unit_text = esc_html__( 'mi', RH_TEXT_DOMAIN );
		$yelp_dist_unit      = 1.1515;

		if ( 'km' === $yelp_distance_unit ) {
			$yelp_dist_unit_text = esc_html__( 'km', RH_TEXT_DOMAIN );
			$yelp_dist_unit      = 1.609344;
		}

		$output = '<div class="yelp-nearby-places">';

		foreach ( $yelp_terms as $yelp_term ) {
			$term = inspiry_get_yelp_term( $yelp_term );
			if ( empty( $term ) ) {
				// Skip
				continue;
			}

			$response = inspiry_yelp_query_api( $yelp_api_key, $yelp_term, $property_location );

			// Check Yelp API response for an error
			if ( isset( $response->error ) ) {

				$error = '';
				if ( ! empty( $response->error->code ) ) {
					$error .= $response->error->code . ': ';
				}
				if ( ! empty( $response->error->description ) ) {
					$error .= $response->error->description;
				}
				$output .= '<div class="yelp-error">' . esc_html( $error ) . '</div>';

			} else {

				if ( isset( $response->businesses ) ) {
					$businesses = $response->businesses;
				} else {
					$businesses = array( $response );
				}

				if ( ! count( $businesses ) ) {
					// Skip
					continue;
				}

				$output .= '<div class="yelp-places-group">';
				$output .= sprintf( '<h4 class="yelp-places-group-title"><i class="%s"></i><span>%s</span></h4>', esc_attr( $term['icon'] ), esc_html( $term['name'] ) );
				$output .= '<ul class="yelp-places-list">';
				foreach ( $businesses as $business ) {
					$output .= '<li class="yelp-places-list-item">';
					$output .= '<div class="content-left-side">';
					if ( isset( $business->name ) ) {
						$output .= '<span class="yelp-place-title">' . esc_html( $business->name ) . '</span>';
					}
					if ( isset( $response->region->center ) ) {
						$distance    = true;
						$current_lat = $response->region->center->latitude;
						$current_lng = $response->region->center->longitude;
					}
					if ( $distance && isset( $business->coordinates ) ) {
						$location_lat      = $business->coordinates->latitude;
						$location_lng      = $business->coordinates->longitude;
						$d_location_lat    = deg2rad( $location_lat );
						$d_current_lat     = deg2rad( $current_lat );
						$theta             = $current_lng - $location_lng;
						$theta             = deg2rad( $theta );
						$dist              = sin( $d_current_lat ) * sin( $d_location_lat ) + cos( $d_current_lat ) * cos( $d_location_lat ) * cos( $theta );
						$dist              = acos( $dist );
						$dist              = rad2deg( $dist );
						$location_distance = round( ( $dist * 60 * $yelp_dist_unit ), 2 );
						$output            .= sprintf( ' <span class="yelp-place-distance">%s %s</span>', esc_html( $location_distance ), esc_html( $yelp_dist_unit_text ) );
					}
					$output .= '</div>';
					$output .= '<div class="content-right-side">';
					if ( isset( $business->review_count ) ) {
						$output .= '<span class="yelp-place-review">';
						$output .= sprintf( '<span class="yelp-place-review-count">%s</span> <span class="yelp-place-review-text">%s</span>', esc_html( $business->review_count ), esc_html__( 'Reviews', RH_TEXT_DOMAIN ) );
						$output .= '</span>';
					}
					if ( isset( $business->rating ) ) {
						$output .= '<span class="yelp-place-rating rating-' . esc_attr( str_replace( '.', '-', $business->rating ) ) . '"></span>';
					}
					$output .= '</div>';
					$output .= '</li>';
				}
				$output .= '</ul>';
				$output .= '</div>';
			}
		}

		$output .= '<p class="yelp-logo">' . esc_html__( 'powered by', RH_TEXT_DOMAIN ) . '<img src="' . get_template_directory_uri() . '/common/images/yelp-logo.png" alt="yelp"></p>';
		$output .= '</div>';

		return $output;
	}
}

if ( ! function_exists( 'inspiry_get_yelp_term' ) ) {
	/**
	 * Returns the term from preset terms if exists.
	 *
	 * @since 3.10
	 *
	 * @param $term
	 *
	 * @return bool|mixed
	 *
	 */
	function inspiry_get_yelp_term( $term ) {
		$terms = array(
			'active'             => array(
				'name' => esc_html__( 'Active Life', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-bicycle'
			),
			'arts'               => array(
				'name' => esc_html__( 'Arts & Entertainment', RH_TEXT_DOMAIN ),
				'icon' => 'far fa-image'
			),
			'auto'               => array(
				'name' => esc_html__( 'Automotive', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-car'
			),
			'beautysvc'          => array(
				'name' => esc_html__( 'Beauty & Spas', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-spa'
			),
			'education'          => array(
				'name' => esc_html__( 'Education', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-graduation-cap'
			),
			'eventservices'      => array(
				'name' => esc_html__( 'Event Planning & Services', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-birthday-cake'
			),
			'financialservices'  => array(
				'name' => esc_html__( 'Financial Services', RH_TEXT_DOMAIN ),
				'icon' => 'far fa-money-bill-alt'
			),
			'food'               => array(
				'name' => esc_html__( 'Food', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-shopping-basket'
			),
			'health'             => array(
				'name' => esc_html__( 'Health & Medical', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-medkit'
			),
			'homeservices'       => array(
				'name' => esc_html__( 'Home Services ', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-wrench'
			),
			'hotelstravel'       => array(
				'name' => esc_html__( 'Hotels & Travel', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-bed'
			),
			'localflavor'        => array(
				'name' => esc_html__( 'Local Flavor', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-coffee'
			),
			'localservices'      => array(
				'name' => esc_html__( 'Local Services', RH_TEXT_DOMAIN ),
				'icon' => 'far fa-dot-circle'
			),
			'massmedia'          => array(
				'name' => esc_html__( 'Mass Media', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-tv'
			),
			'nightlife'          => array(
				'name' => esc_html__( 'Nightlife', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-glass-martini'
			),
			'pets'               => array(
				'name' => esc_html__( 'Pets', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-paw'
			),
			'professional'       => array(
				'name' => esc_html__( 'Professional Services', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-suitcase'
			),
			'publicservicesgovt' => array(
				'name' => esc_html__( 'Public Services & Government', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-university'
			),
			'realestate'         => array(
				'name' => esc_html__( 'Real Estate', RH_TEXT_DOMAIN ),
				'icon' => 'far fa-building'
			),
			'religiousorgs'      => array(
				'name' => esc_html__( 'Religious Organizations', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-universal-access'
			),
			'restaurants'        => array(
				'name' => esc_html__( 'Restaurants', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-utensils'
			),
			'shopping'           => array(
				'name' => esc_html__( 'Shopping', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-shopping-bag'
			),
			'transport'          => array(
				'name' => esc_html__( 'Transportation', RH_TEXT_DOMAIN ),
				'icon' => 'fas fa-bus'
			)
		);

		if ( isset( $terms[ $term ] ) ) {
			return $terms[ $term ];
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_agent_custom_form' ) ) {
	/**
	 * Displays the agent custom contact form.
	 *
	 * @since 3.10
	 *
	 * @param string $agent_id
	 *
	 * @param bool   $container
	 *
	 *
	 */
	function inspiry_agent_custom_form( $agent_id = '', $container = true ) {
		$form = inspiry_get_agent_custom_form( $agent_id );
		if ( $form ) {
			if ( $container ) {
				echo '<div class="agent-custom-contact-form" id="agent-form-id' . $agent_id . '">' . do_shortcode( $form ) . '</div>';
			} else {
				echo do_shortcode( $form );
			}
		}
	}
}

if ( ! function_exists( 'inspiry_get_agent_custom_form' ) ) {
	/**
	 * Returns the agent custom contact form shortcode if exists.
	 *
	 * @since 3.10
	 *
	 * @param string $agent_id
	 *
	 * @return bool|mixed
	 *
	 */
	function inspiry_get_agent_custom_form( $agent_id = '' ) {

		if ( empty( $agent_id ) ) {
			$agent_id = get_the_ID();
		}

		$metabox_form = get_post_meta( $agent_id, 'REAL_HOMES_custom_agent_contact_form', true );
		if ( ! empty( $metabox_form ) ) {
			return $metabox_form;
		}

		$customizer_form = get_option( 'theme_custom_agent_contact_form' );
		if ( ! empty( $customizer_form ) ) {
			return $customizer_form;
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_is_property_analytics_enabled' ) ) {
	/**
	 * Check property analytics feature is enabled or disabled.
	 */
	function inspiry_is_property_analytics_enabled() {
		return 'enabled' === get_option( 'inspiry_property_analytics_status', 'disabled' );
	}
}

if ( ! function_exists( 'inspiry_agency_custom_form' ) ) {
	/**
	 * Displays the agency custom contact form.
	 *
	 * @since 3.10
	 *
	 * @param string $agency_id
	 *
	 * @param bool   $container
	 *
	 */
	function inspiry_agency_custom_form( $agency_id = '', $container = true ) {
		$form = inspiry_get_agency_custom_form( $agency_id );
		if ( $form ) {
			if ( $container ) {
				echo '<div class="agent-custom-contact-form agency-custom-contact-form">' . do_shortcode( $form ) . '</div>';
			} else {
				echo do_shortcode( $form );
			}
		}
	}
}

if ( ! function_exists( 'inspiry_get_agency_custom_form' ) ) {
	/**
	 * Returns the agency custom contact form shortcode if exists.
	 *
	 * @since 3.10
	 *
	 * @param string $agency_id
	 *
	 * @return bool|mixed
	 */
	function inspiry_get_agency_custom_form( $agency_id = '' ) {

		if ( empty( $agency_id ) ) {
			$agency_id = get_the_ID();
		}

		$metabox_form = get_post_meta( $agency_id, 'REAL_HOMES_custom_agency_contact_form', true );
		if ( ! empty( $metabox_form ) ) {
			return $metabox_form;
		}

		$customizer_form = get_option( 'theme_custom_agency_contact_form' );
		if ( ! empty( $customizer_form ) ) {
			return $customizer_form;
		}

		return false;
	}
}

if ( ! function_exists( 'inspiry_get_property_single_template' ) ) {
	/**
	 * Sets the user selected template for property detail page using customizer setting.
	 *
	 * @param string $original_template
	 *
	 * @return string
	 */
	function inspiry_get_property_single_template( $original_template ) {

		if ( ! is_singular( 'property' ) ) {
			return $original_template;
		}

		if ( 'fullwidth' === get_option( 'inspiry_property_single_template', 'sidebar' ) ) {
			$global_property_template_override = get_post_meta( get_the_ID(), 'inspiry_global_property_template_override', true );
			$property_template                 = get_post_meta( get_the_ID(), '_wp_page_template', true );

			if ( ( '1' !== $global_property_template_override ) && ( 'default' === $property_template || empty( $property_template ) ) ) {
				$template = 'templates/property-full-width-layout.php';
				$located  = locate_template( $template );
				if ( $located && ! empty( $located ) ) {
					return $located;
				}
			}
		}

		return $original_template;
	}

	add_filter( 'single_template', 'inspiry_get_property_single_template' );
}

if ( ! function_exists( 'inspiry_term_description' ) ) {
	/**
	 * Displays the term description.
	 *
	 * @since 3.10.2
	 *
	 * @param int $term Optional. Term ID. Will use global term ID by default.
	 *
	 */
	function inspiry_term_description( $term = 0 ) {
		if ( 'show' === get_option( 'inspiry_term_description', 'hide' ) ) {
			$description = term_description( $term );
			if ( ! empty( $description ) ) {
				$wrapper = '<div class="inspiry-term-description">%s</div>';
				printf( $wrapper, $description );
			}
		}
	}
}

if ( ! function_exists( 'inspiry_pages' ) ) {
	/**
	 * Return an array of pages as ID & Title pair each.
	 *
	 * @since  3.10.2
	 *
	 * @param array $args Custom query arguments.
	 *
	 * @return array       List of pages as an array.
	 *
	 */
	function inspiry_pages( $args = array() ) {
		$default_args  = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
		);
		$args          = wp_parse_args( $args, $default_args );
		$inspiry_pages = array( 0 => esc_html__( 'None', RH_TEXT_DOMAIN ) );
		$raw_pages     = get_posts( $args );
		if ( 0 < count( $raw_pages ) ) {
			foreach ( $raw_pages as $single_page ) {
				$inspiry_pages[ $single_page->ID ] = $single_page->post_title;
			}
		}

		return $inspiry_pages;
	}
}

if ( ! function_exists( 'realhomes_similar_properties_filters' ) ) {
	/**
	 * Prints the filters markup for similar properties on single property page.
	 *
	 * @since  3.13
	 *
	 * @param string $design
	 * @param string $filter_wrapper_id
	 *
	 * @return mixed
	 *
	 */
	function realhomes_similar_properties_filters( $design = 'modern', $filter_wrapper_id = 'similar-properties-filters-wrapper' ) {

		// Return if filters are disabled from customizer setting.
		if ( 'disable' === get_option( 'inspiry_similar_properties_frontend_filters', 'disable' ) ) {
			return false;
		}

		// Default values
		$similar_properties_default_filters = array(
			'property-feature' => esc_html__( 'Property Features', RH_TEXT_DOMAIN ),
			'property-type'    => esc_html__( 'Property Type', RH_TEXT_DOMAIN ),
			'property-city'    => esc_html__( 'Property Location', RH_TEXT_DOMAIN ),
			'property-status'  => esc_html__( 'Property Status', RH_TEXT_DOMAIN ),
			'property-agent'   => esc_html__( 'Property Agent', RH_TEXT_DOMAIN ),
		);

		// Get the selected filters from customizer settings.
		$similar_properties_filters = get_option( 'inspiry_similar_properties_filters', array_keys( $similar_properties_default_filters ) );
		if ( ! empty( $similar_properties_filters ) && is_array( $similar_properties_filters ) ) {

			$property_id                  = get_the_ID();
			$number_of_similar_properties = get_option( 'theme_number_of_similar_properties', '2' );
			$similar_properties_args      = array(
				'post_type'           => 'property',
				'posts_per_page'      => intval( $number_of_similar_properties ),
				'post__not_in'        => array( $property_id ),
				'post_parent__not_in' => array( $property_id ),
				// to avoid child posts from appearing in similar properties.
			);

			if ( 'true' === get_option( 'realhomes_hide_filter_with_zero_properties', 'false' ) ) {
				foreach ( $similar_properties_filters as $index => $taxonomy ) {
					/* Property Taxonomies array */
					if ( 'property-agent' !== $taxonomy ) {
						$property_terms = get_the_terms( $property_id, $taxonomy );
						$terms_array    = array();
						if ( ! empty( $property_terms ) && is_array( $property_terms ) ) {
							foreach ( $property_terms as $property_term ) {
								$terms_array[] = $property_term->term_id;
							}
						}
						$similar_properties_args['tax_query'] = array(
							array(
								'taxonomy'         => $taxonomy,
								'field'            => 'id',
								'terms'            => $terms_array,
								'include_children' => false
							)
						);
					}

					if ( 'property-agent' === $taxonomy ) {
						$similar_properties_args['tax_query'] = array();
						$property_agents                      = get_post_meta( $property_id, 'REAL_HOMES_agents' );
						if ( ! empty( $property_agents ) ) {
							$similar_properties_args['meta_query'] = array(
								array(
									'key'     => 'REAL_HOMES_agents',
									'value'   => $property_agents,
									'type'    => 'NUMERIC',
									'compare' => 'IN',
								),
							);
						}
					}

					$similar_properties_query = new WP_Query( $similar_properties_args );

					// Eliminiting the filter with zero properties
					if ( $similar_properties_query->found_posts <= 0 ) {
						unset( $similar_properties_filters[ $index ] );
					}
				}
			}

			$btn_class         = 'rh-btn rh-btn-primary';
			$btn_class_current = 'rh-btn rh-btn-secondary';
			if ( 'classic' === $design ) {
				$btn_class         = 'real-btn';
				$btn_class_current = 'real-btn';
			}

			if ( empty( $filter_wrapper_id ) ) {
				$filter_wrapper_id = 'similar-properties-filters-wrapper';
			}

			$filter_wrapper_class = 'similar-properties-filters-wrapper';

			$output = sprintf( '<div id="%1$s" class="%2$s" data-similar-properties-request-nonce="%3$s">', $filter_wrapper_id, $filter_wrapper_class, wp_create_nonce( 'similar_property_nonce' ) );
			$output .= sprintf( '<a class="%s current" href="#recommended" data-similar-properties-filter="recommended">%s</a>', esc_attr( $btn_class_current ), esc_html__( 'Recommended', RH_TEXT_DOMAIN ) );

			foreach ( $similar_properties_filters as $similar_properties_filter ) {
				$output .= sprintf( '<a class="%1$s" href="#%2$s" data-similar-properties-filter="%2$s">%3$s</a>', esc_attr( $btn_class ), $similar_properties_filter, $similar_properties_default_filters[ $similar_properties_filter ] );
			}

			$output .= '</div>';

			// Print the filters markup
			echo $output;
		}
	}
}

if ( ! function_exists( 'realhomes_filter_similar_properties' ) ) {
	/**
	 * Returns the similar properties markup.
	 *
	 * @since  3.13
	 */
	function realhomes_filter_similar_properties() {
		// Verify nonce for security
		if ( ! check_ajax_referer( 'similar_property_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Security check failed! Please refresh and try again.', RH_TEXT_DOMAIN ),
				)
			);
		}

		// Validate property_filter
		$similar_properties_default_filters = array(
			'property-feature',
			'property-type',
			'property-city',
			'property-status',
			'property-agent',
		);

		// Get the selected filters from customizer settings.
		$similar_properties_filters = get_option( 'inspiry_similar_properties_filters', array_keys( $similar_properties_default_filters ) );

		$property_filter = sanitize_text_field( $_POST['property_filter'] ?? '' );
		if ( ! in_array( $property_filter, $similar_properties_default_filters, true ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Invalid property filter provided.', RH_TEXT_DOMAIN ),
			) );
			wp_die();
		}

		// Validate property_id
		$property_id = intval( $_POST['property_id'] ?? 0 );
		if ( $property_id <= 0 ) {
			wp_send_json_error( array(
				'success' => false,
				'message' => esc_html__( 'Invalid property ID provided.', RH_TEXT_DOMAIN ),
			) );
			wp_die();
		}

		// Validate properties_per_page
		$properties_per_page = intval( $_POST['properties_per_page'] ?? 2 );
		if ( $properties_per_page <= 0 ) {
			$properties_per_page = 2; // Default to 2 if invalid
		}

		// Prepare query arguments
		$similar_properties_args = array(
			'post_type'           => 'property',
			'posts_per_page'      => $properties_per_page,
			'post__not_in'        => array( $property_id ),
			'post_parent__not_in' => array( $property_id ),
			'post_status'         => array( 'publish' ),
		);

		// Handle sorting
		$similar_properties_sorty_by = get_option( 'inspiry_similar_properties_sorty_by' );
		if ( $similar_properties_sorty_by === 'low-to-high' ) {
			$similar_properties_args['orderby']  = 'meta_value_num';
			$similar_properties_args['meta_key'] = 'REAL_HOMES_property_price';
			$similar_properties_args['order']    = 'ASC';
		} else if ( $similar_properties_sorty_by === 'high-to-low' ) {
			$similar_properties_args['orderby']  = 'meta_value_num';
			$similar_properties_args['meta_key'] = 'REAL_HOMES_property_price';
			$similar_properties_args['order']    = 'DESC';
		} else if ( $similar_properties_sorty_by === 'random' ) {
			$similar_properties_args['orderby'] = 'rand';
		}

		// Filter by taxonomy or agent
		if ( 'property-agent' === $property_filter ) {
			$property_agents                       = get_post_meta( $property_id, 'REAL_HOMES_agents' );
			$similar_properties_args['meta_query'] = array(
				array(
					'key'     => 'REAL_HOMES_agents',
					'value'   => $property_agents ? $property_agents : 0,
					'compare' => 'IN',
				),
			);
		} else {
			$terms_array    = array();
			$property_terms = get_the_terms( $property_id, $property_filter );
			if ( ! empty( $property_terms ) && is_array( $property_terms ) ) {
				foreach ( $property_terms as $property_term ) {
					$terms_array[] = $property_term->term_id;
				}
			}

			$similar_properties_args['tax_query'] = array(
				array(
					'taxonomy'         => $property_filter,
					'terms'            => $terms_array,
					'include_children' => false
				)
			);
		}

		// Query similar properties
		$similar_properties_query = new WP_Query( $similar_properties_args );
		if ( $similar_properties_query->have_posts() ) {
			ob_start();
			while ( $similar_properties_query->have_posts() ) {
				$similar_properties_query->the_post();
				$property_card_variation = get_option( 'inspiry_property_card_variation', '1' );
				$design                  = sanitize_text_field( $_POST['design'] ?? 'classic' );

				if ( 'classic' === $design ) {
					get_template_part( 'assets/classic/partials/property/single/similar-property-card' );
				} else if ( 'modern' === $design ) {
					get_template_part( 'assets/modern/partials/properties/grid-card-' . $property_card_variation );
				} else {
					get_template_part( 'assets/ultra/partials/properties/grid-card-' . $property_card_variation );
				}
			}
			wp_reset_postdata();
			wp_send_json_success( ob_get_clean() );
		} else {
			wp_send_json_error( array(
				'success' => false,
				'message' => esc_html__( 'No property found for this criteria.', RH_TEXT_DOMAIN ),
			) );
		}

		wp_die();
	}

	add_action( 'wp_ajax_realhomes_filter_similar_properties', 'realhomes_filter_similar_properties' );
	add_action( 'wp_ajax_nopriv_realhomes_filter_similar_properties', 'realhomes_filter_similar_properties' );
}

if ( ! function_exists( 'inspiry_lightbox_map_theme_essentials' ) ) {
	/**
	 * Get default values for map icons, marker type and color
	 */
	function inspiry_lightbox_map_theme_essentials() {

		$map_type = inspiry_get_maps_type();
		if ( 'google-maps' === $map_type ) {
			wp_register_script(
				'lightbox-google-map',
				get_theme_file_uri( 'common/js/lightbox-google-map.js' ),
				array(
					'jquery',
					'google-map-api'
				),
				INSPIRY_THEME_VERSION,
				true
			);

		} else if ( 'mapbox' === $map_type ) {
			wp_register_script(
				'leaflet-js',
				'https://unpkg.com/leaflet@1.3.4/dist/leaflet.js',
				array(),
				'1.3.4',
				true
			);

			wp_register_script(
				'mapbox-script-2-9-2',
				'https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js',
				array(),
				'2.9.2',
				true
			);

			wp_register_script(
				'lightbox-mapbox',
				get_theme_file_uri( 'common/js/lightbox-mapbox.js' ),
				array(
					'jquery',
					'leaflet-js',
					'mapbox-script-2-9-2'
				),
				INSPIRY_THEME_VERSION,
				true
			);

			wp_register_style(
				'mapbox-style-2-9-2',
				'https://api.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css',
				array(),
				'2.9.2'
			);

		} else {
			wp_register_script(
				'lightbox-open-street-map',
				get_theme_file_uri( 'common/js/lightbox-open-street-map.js' ),
				array(
					'jquery',
					'leaflet'
				),
				INSPIRY_THEME_VERSION,
				true
			);
		}

		$property_map_data = array();

		if ( $map_type === 'mapbox' ) {
			$property_map_data['mapboxAPI']   = get_option( 'ere_mapbox_api_key' );
			$property_map_data['mapboxStyle'] = get_option( 'ere_mapbox_style' );
		}

		// Property map icon based on Property Type
		$type_terms = get_the_terms( get_the_ID(), 'property-type' );
		if ( $type_terms && ! is_wp_error( $type_terms ) ) {
			foreach ( $type_terms as $type_term ) {
				$icon_id = get_term_meta( $type_term->term_id, 'inspiry_property_type_icon', true );
				if ( ! empty ( $icon_id ) ) {
					$icon_url = wp_get_attachment_url( $icon_id );
					if ( $icon_url ) {
						$property_map_data['icon'] = esc_url( $icon_url );

						// Retina icon
						$retina_icon_id = get_term_meta( $type_term->term_id, 'inspiry_property_type_icon_retina', true );
						if ( ! empty ( $retina_icon_id ) ) {
							$retina_icon_url = wp_get_attachment_url( $retina_icon_id );
							if ( $retina_icon_url ) {
								$property_map_data['retinaIcon'] = esc_url( $retina_icon_url );
							}
						}
						break;
					}
				}
			}
		}

		// Set default icons if above code fails to sets any
		if ( ! isset( $property_map_data['icon'] ) ) {
			$property_map_data['icon']       = INSPIRY_DIR_URI . '/images/map/single-family-home-map-icon.png';           // default icon
			$property_map_data['retinaIcon'] = INSPIRY_DIR_URI . '/images/map/single-family-home-map-icon@2x.png';  // default retina icon
		}

		// Set Google Map Type & Zoom Level
		$property_map_data['marker_type']  = get_option( 'inspiry_property_map_marker_type', 'pin' );
		$property_map_data['marker_color'] = get_option( 'inspiry_property_map_marker_color', '#ea723d' );

		$property_map_data['styles'] = '';
		$styles_json                 = get_option( 'inspiry_google_maps_styles' );
		if ( ! empty( $styles_json ) ) {
			$property_map_data['styles'] = stripslashes( $styles_json );
		}

		$property_map_data['type'] = get_option( 'inspiry_property_map_type', 'roadmap' );

		$map_type = inspiry_get_maps_type();

		if ( 'google-maps' == $map_type ) {
			wp_localize_script( 'lightbox-google-map', 'propertyMapData', $property_map_data );
			wp_enqueue_script( 'lightbox-google-map' );

		} else if ( 'mapbox' === $map_type ) {
			wp_enqueue_style( 'mapbox-style-2-9-2' );
			wp_localize_script( 'lightbox-mapbox', 'propertyMapData', $property_map_data );
			wp_enqueue_script( 'lightbox-mapbox' );

		} else {
			wp_localize_script( 'lightbox-open-street-map', 'propertyMapData', $property_map_data );
			wp_enqueue_script( 'lightbox-open-street-map' );
		}
	}
}

add_action( 'realhomes_enqueue_map_lightbox_essentials', 'inspiry_lightbox_map_theme_essentials' );

if ( ! function_exists( 'realhomes_enqueue_filter_properties_js' ) ) {
	/**
	 * Enqueue the JavaScript file required for the filter properties sidebar widget
	 *
	 * @since 4.1.0
	 */
	function realhomes_enqueue_filter_properties_js() {

		// registering widget related script
		wp_register_script(
			'filter-properties-script',
			get_theme_file_uri( 'common/js/ajax-properties-filter-widget.js' ),
			array( 'jquery', 'jquery-ui-slider' ),
			INSPIRY_THEME_VERSION,
			true
		);

		// adding necessary elements to localized array
		$localized_ajax_filter_array = array(
			'mapService'      => esc_html__( get_option( 'ere_theme_map_type', 'openstreetmaps' ) ),
			'designVariation' => INSPIRY_DESIGN_VARIATION,
			'filterStrings'   => array(
				'clearAll' => esc_html__( 'Clear All', RH_TEXT_DOMAIN ),
				'selected' => esc_html__( 'Selected', RH_TEXT_DOMAIN )
			)
		);

		// adding localized array to script call
		wp_localize_script( 'filter-properties-script', 'localizedFilters', $localized_ajax_filter_array );

		// enqueuing filters property widget script
		wp_enqueue_script( 'filter-properties-script' );

		// enqueuing jquery ui styles
		wp_enqueue_style(
			'jquery-ui-base-css',
			'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css',
			array(),
			'1.13.2'
		);

	}

	add_action( 'realhomes_after_filter_properties_widget', 'realhomes_enqueue_filter_properties_js' );
}

if ( ! function_exists( 'rh_stylish_meta' ) ) {
	function rh_stylish_meta( $label, $post_meta_key, $icon, $postfix = '' ) {
		$property_id = get_the_ID();
		$post_meta   = get_post_meta( $property_id, $post_meta_key, true );

		if ( isset( $post_meta ) && ! empty( $post_meta ) ) {
			?>
            <div class="rh_prop_card__meta">
				<?php
				if ( $label ) {
					?>
                    <span class="rh_meta_titles"><?php echo esc_html( $label ); ?></span>
					<?php
				}
				?>
                <div class="rh_meta_icon_wrapper">
					<?php realhomes_property_meta_icon( $post_meta_key, '/icons/' . $icon . '.svg' ); ?>
                    <span class="figure"><?php echo esc_html( $post_meta ); ?></span>
					<?php
					if ( ! empty( $postfix ) ) {
						if ( 'REAL_HOMES_property_size_postfix' === $postfix ) {
							$get_postfix = realhomes_get_area_unit( $property_id );
						} else if ( 'REAL_HOMES_property_lot_size_postfix' === $postfix ) {
							$get_postfix = realhomes_get_lot_unit( $property_id );
						} else {
							$get_postfix = get_post_meta( $property_id, $postfix, true );
						}

						if ( ! empty( $get_postfix ) ) {
							?>
                            <span class="label"><?php echo esc_html( $get_postfix ); ?></span>
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

if ( ! function_exists( 'rh_stylish_meta_smart' ) ) {
	function rh_stylish_meta_smart( $label, $post_meta_key, $icon, $postfix = '' ) {
		$post_meta   = get_post_meta( get_the_ID(), $post_meta_key, true );
		$get_postfix = get_post_meta( get_the_ID(), $postfix, true );
		if ( isset( $post_meta ) && ! empty( $post_meta ) ) {
			?>
            <div class="rh_prop_card__meta">
				<?php
				if ( $label ) {
					?>
                    <span class="rh_meta_titles"><?php echo esc_html( $label ); ?></span>
					<?php
				}
				?>
                <div class="rh_meta_icon_wrapper">
                    <span data-tooltip="<?php echo esc_html( $label ) ?>">
					<?php
					if ( $icon ) {
						include INSPIRY_THEME_DIR . '/icons/' . $icon . '.svg';
					}
					?>
                    </span>
                    <span class="rh_meta_smart_box">
                        <span class="figure"><?php echo esc_html( $post_meta ); ?></span>
						<?php if ( isset( $postfix ) && ! empty( $postfix ) && ! empty( $get_postfix ) ) { ?>
                            <span class="label"><?php echo esc_html( get_post_meta( get_the_ID(), $postfix, true ) ); ?></span>
						<?php } ?>
                    </span>
                </div>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'rh_lightbox_data_attributes' ) ) {

	function rh_lightbox_data_attributes( $widget_id, $post_id, $classes = '' ) {

		if ( inspiry_map_needed_for_address() ) {

			$REAL_HOMES_property_map = get_post_meta( $post_id, 'REAL_HOMES_property_map', true );
			$property_location       = get_post_meta( $post_id, 'REAL_HOMES_property_location', true );

			if ( ! empty( get_the_post_thumbnail() ) ) {
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
                class="rhea_trigger_map rhea_facnybox_trigger-<?php echo esc_attr( $widget_id . ' ' . $classes ); ?>"data-rhea-map-source="rhea-map-source-<?php echo esc_attr( $widget_id ); ?>"data-rhea-map-location="<?php echo esc_attr( $property_location ); ?>"data-rhea-map-title="<?php echo esc_attr( get_the_title() ); ?>"data-rhea-map-price="<?php echo esc_attr( ere_get_property_price() ); ?>"data-rhea-map-thumb="<?php echo esc_attr( $current_property_data ) ?>"
				<?php
			}
		}
	}
}

if ( ! function_exists( 'rh_rvr_rating_average' ) ) {
	/**
	 * Display rating average based on approved comments with rating
	 */
	function rh_rvr_rating_average() {

		$args = array(
			'post_id' => get_the_ID(),
			'status'  => 'approve',
		);

		$comments = get_comments( $args );
		$ratings  = array();
		$count    = 0;

		foreach ( $comments as $comment ) {

			$rating = get_comment_meta( $comment->comment_ID, 'inspiry_rating', true );

			if ( ! empty( $rating ) ) {
				$ratings[] = absint( $rating );
				$count++;
			}
		}


		$allowed_html = array(
			'span' => array(
				'class' => array(),
			),
			'i'    => array(
				'class' => array(),
			),
		);

		if ( 0 !== count( $ratings ) ) {

			$values_count = ( array_count_values( $ratings ) );


			$avg = round( array_sum( $ratings ) / count( $ratings ), 2 );
			?>
            <div class="rh_rvr_ratings">
                <div class="rh_stars_avg_rating" title="<?php echo esc_attr( $avg ) . ' / ' . esc_html__( '5 based on', RH_TEXT_DOMAIN ) . ' ' . esc_html( $count ) . ' ' . esc_html__( 'reviews', RH_TEXT_DOMAIN ); ?>">
					<?php echo wp_kses( rhea_rating_stars( $avg ), $allowed_html ); ?>

                    <div class="rh_wrapper_rating_info">
						<?php
						$i = 5;
						while ( $i > 0 ) {
							?>
                            <p class="rh_rating_percentage">
                                <span class="rh_rating_sorting_label">
                                    <?php
                                    printf( _nx( '%s Star', '%s Stars', $i, 'Rating Stars', RH_TEXT_DOMAIN ), number_format_i18n( $i ) );
                                    ?>
                                </span>
								<?php
								if ( isset( $values_count[ $i ] ) && ! empty( $values_count[ $i ] ) ) {
									$stars = round( ( $values_count[ $i ] / ( count( $ratings ) ) ) * 100 );
								} else {
									$stars = 0;
								}
								?>

                                <span class="rh_rating_line">
                                    <span class="rh_rating_line_inner" style="width: <?php echo esc_attr( $stars ); ?>%"></span>
                                </span>

                                <span class="rh_rating_text">
                                    <span class="rh_rating_text_inner">
                                        <?php echo esc_html( $stars ) . '%' ?>
                                    </span>
                                </span>
                            </p>
							<?php

							$i--;
						}
						?>
                    </div>
                </div>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'realhomes_property_agent_contact_methods' ) ) {
	/**
	 * Displays the whatsApp and call now buttons.
	 *
	 * @since 3.14.0
	 *
	 * @param string $contact_number
	 * @param string $whatsapp_number
	 * @param string $btn_class
	 *
	 */
	function realhomes_property_agent_contact_methods( $contact_number = '', $whatsapp_number = '', $btn_class = '' ) {

		if ( ! empty( $contact_number ) || ! empty( $whatsapp_number ) ) {

			if ( empty( $whatsapp_number ) ) {
				$whatsapp_number = $contact_number;
			}

			$args = array(
				'phone' => esc_html( preg_replace('/\D/', '', $whatsapp_number ) )
			);

			$property_id    = get_the_ID();
			$property_title = get_the_title( $property_id );
			if ( ! empty( $property_title ) ) {
				$args['text'] = sprintf( esc_html__( "Hello, I'm interested in [%s] %s", RH_TEXT_DOMAIN ), esc_html( $property_title ), esc_url( get_the_permalink( $property_id ) ) );
			}
			?>
            <a class="btn-whatsapp-chat <?php echo esc_attr( $btn_class ); ?>" href="<?php echo esc_url( add_query_arg( $args, 'https://api.whatsapp.com/send' ) ); ?>" target="_blank">
				<?php inspiry_safe_include_svg( '/images/icon-whatsapp.svg', '/common/' ); ?>
                <span class="btn-text"><?php echo esc_html( realhomes_get_agent_whatsapp_button_label() ); ?></span>
            </a>
			<?php
			if ( ! empty( $contact_number ) ) :
				?>
                <a class="btn-call-now <?php echo esc_attr( $btn_class ); ?>" href="tel:<?php echo esc_attr( $contact_number ); ?>">
					<?php inspiry_safe_include_svg( '/images/icon-phone.svg', '/common/' ); ?>
                    <span class="btn-text"><?php echo esc_html( realhomes_get_agent_callnow_button_label() ); ?></span>
                </a>
			<?php
			endif;
		}
	}
}

if ( ! function_exists( 'realhomes_property_agent_sticky_bar' ) ) {
	/**
	 * Displays property agent contact methods sticky bar on mobile.
	 *
	 * @since 3.14.0
	 */
	function realhomes_property_agent_sticky_bar() {

		// Return if the page is not a single property page.
		if ( ! is_singular( 'property' ) ) {
			return false;
		}

		$property_id          = get_the_ID();
		$display_agent_info   = get_option( 'theme_display_agent_info', 'true' );
		$agent_display_option = get_post_meta( $property_id, 'REAL_HOMES_agent_display_option', true );

		// Display content if the agent related options are enabled.
		if ( ( 'true' === $display_agent_info ) && ( 'none' != $agent_display_option ) ) {

			// Collect agent information.
			$agent_args = array( 'agent_id' => '' );

			if ( 'my_profile_info' == $agent_display_option ) {
				$agent_args['display_author']      = true;
				$agent_args['author_id']           = get_the_author_meta( 'ID' );
				$agent_args['author_display_name'] = get_the_author_meta( 'display_name' );
				$agent_args['profile_image_id']    = intval( get_the_author_meta( 'profile_image_id' ) );
				$agent_args['agent_mobile']        = get_the_author_meta( 'mobile_number' );
				$agent_args['agent_whatsapp']      = get_the_author_meta( 'mobile_whatsapp' );
			} else {
				$property_agents = get_post_meta( $property_id, 'REAL_HOMES_agents' );

				// Remove invalid ids.
				$property_agents = array_filter( $property_agents, function ( $v ) {
					return ( $v > 0 );
				} );

				// Remove duplicated ids.
				$property_agents = array_unique( $property_agents );

				if ( ! empty( $property_agents ) ) {
					foreach ( $property_agents as $agent ) {
						$agent_args['agent_id']         = intval( $agent );
						$agent_args['agent_title_text'] = esc_html( get_the_title( $agent_args['agent_id'] ) );
						$agent_args['agent_mobile']     = get_post_meta( $agent_args['agent_id'], 'REAL_HOMES_mobile_number', true );
						$agent_args['agent_whatsapp']   = get_post_meta( $agent_args['agent_id'], 'REAL_HOMES_whatsapp_number', true );

						break;
					}
				}
			}

			$agent_id = $agent_args['agent_id'];
			?>
            <div id="property-agent-contact-methods-wrapper" class="property-agent-contact-methods-wrapper">
                <div class="property-agent-details">
					<?php
					if ( isset( $agent_args['display_author'] ) && ( $agent_args['display_author'] ) ) {
						if ( isset( $agent_args['profile_image_id'] ) && ( 0 < $agent_args['profile_image_id'] ) ) {
							echo wp_get_attachment_image( $agent_args['profile_image_id'], 'agent-image' );
						} else if ( isset( $agent_args['agent_email'] ) ) {
							echo get_avatar( $agent_args['agent_email'], '210' );
						}
					} else {
						if ( isset( $agent_id ) && has_post_thumbnail( $agent_id ) ) {
							?>
                            <a class="agent-thumb" href="<?php echo esc_url( get_permalink( $agent_id ) ); ?>">
								<?php
								echo get_the_post_thumbnail( $agent_id, 'agent-image' );
								if ( 0 < intval( $agent_id ) ) {
									realhomes_verification_badge( 'agent', $agent_id );
								}
								?>
                            </a>
							<?php
						}
					}

					if ( isset( $agent_args['agent_title_text'] ) && ! empty( $agent_args['agent_title_text'] ) ) : ?>
                        <h4 class="property-agent-name">
                            <a href="<?php echo esc_url( get_permalink( $agent_id ) ); ?>">
								<?php echo esc_html( $agent_args['agent_title_text'] ); ?>
                            </a>
                        </h4>
					<?php
					endif;
					?>
                </div><!-- .property-agent-details -->

                <div class="property-agent-contact-methods">
					<?php
					$agent_contact_form = get_option( 'inspiry_property_agent_form', 'true' );
					if ( ! empty( $agent_id ) && 'true' === $agent_contact_form ) :
						?>
                        <a id="scroll-to-property-agent-form" class="agent-contact-method-btn agent-contact-method-mail-btn" href="#<?php echo esc_attr( 'agent-form-id' . $agent_id ); ?>">
							<?php inspiry_safe_include_svg( '/images/icon-mail.svg', '/common/' ); ?>
                        </a>
					<?php
					endif;

					$contact_number  = '';
					$whatsapp_number = '';
					if ( isset( $agent_args['agent_mobile'] ) ) {
						$contact_number = $agent_args['agent_mobile'];
					}
					if ( isset( $agent_args['agent_whatsapp'] ) ) {
						$whatsapp_number = $agent_args['agent_whatsapp'];
					}

					if ( ! empty( $contact_number ) || ! empty( $whatsapp_number ) ) :

						if ( empty( $whatsapp_number ) ) {
							$whatsapp_number = $contact_number;
						}

						$args = array(
							'phone' => esc_html( preg_replace('/\D/', '', $whatsapp_number ) )
						);

						$property_title = get_the_title( $property_id );
						if ( ! empty( $property_title ) ) {
							$args['text'] = sprintf( esc_html__( "Hello, I'm interested in [%s] %s", RH_TEXT_DOMAIN ), esc_html( $property_title ), esc_url( get_the_permalink( $property_id ) ) );
						}
						?>
                        <a class="agent-contact-method-btn agent-contact-method-whatsapp-btn" href="<?php echo esc_url( add_query_arg( $args, 'https://api.whatsapp.com/send' ) ); ?>" target="_blank">
							<?php inspiry_safe_include_svg( '/images/icon-whatsapp.svg', '/common/' ); ?>
                        </a>
						<?php
						if ( ! empty( $contact_number ) ) :
							?>
                            <a class="agent-contact-method-btn agent-contact-method-call-btn" href="tel:<?php echo esc_attr( $contact_number ); ?>">
								<?php inspiry_safe_include_svg( '/images/icon-phone.svg', '/common/' ); ?>
                            </a>
						<?php
						endif;
					endif;
					?>
                </div><!-- .property-agent-contact-methods -->
            </div>
			<?php
		}
	}

	add_action( 'wp_footer', 'realhomes_property_agent_sticky_bar', 10 );
}

if ( ! function_exists( 'realhomes_get_agent_default_message' ) ) {
	/**
	 * Returns Agent form's default message.
	 */
	function realhomes_get_agent_default_message() {
		return get_option( 'realhomes_agent_form_default_message', esc_html__( 'Hello, I am interested in [%s]', RH_TEXT_DOMAIN ) );
	}
}

if ( ! function_exists( 'rh_ultra_meta' ) ) {
	function rh_ultra_meta( $label, $post_meta_key, $icon, $postfix = '' ) {
		$property_id = get_the_ID();
		$post_meta   = get_post_meta( $property_id, $post_meta_key, true );

		if ( ! empty( $post_meta ) ) {
			?>
            <div class="rh-ultra-prop-card-meta">
                <div class="rh-ultra-meta-icon-wrapper">
                    <span class="rh-ultra-meta-icon" data-tooltip="<?php echo esc_html( $label ) ?>">
					<?php realhomes_property_meta_icon( $post_meta_key, '/ultra/icons/' . $icon . '.svg', '/assets/' ); ?>
                    </span>
                    <span class="rh-ultra-meta-box">
                        <span class="figure"><?php echo esc_html( $post_meta ); ?></span>
                            <?php
                            if ( ! empty( $postfix ) ) {
	                            if ( 'REAL_HOMES_property_size_postfix' === $postfix ) {
		                            $get_postfix = realhomes_get_area_unit( $property_id );
	                            } else if ( 'REAL_HOMES_property_lot_size_postfix' === $postfix ) {
		                            $get_postfix = realhomes_get_lot_unit( $property_id );
	                            } else {
		                            $get_postfix = get_post_meta( $property_id, $postfix, true );
	                            }

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

if ( ! function_exists( 'realhomes_get_agent_whatsapp_button_label' ) ) {
	/**
	 * Returns Agent WhatsApp Button Label
	 */
	function realhomes_get_agent_whatsapp_button_label() {
		return get_option( 'realhomes_agent_whatsapp_button_label', esc_html__( 'WhatsApp', RH_TEXT_DOMAIN ) );
	}
}

if ( ! function_exists( 'realhomes_get_agent_callnow_button_label' ) ) {
	/**
	 * Returns Agent WhatsApp Button Label
	 */
	function realhomes_get_agent_callnow_button_label() {
		return get_option( 'realhomes_agent_callnow_button_label', esc_html__( 'Call Now', RH_TEXT_DOMAIN ) );
	}
}

if ( ! function_exists( 'realhomes_agent_verification_badge' ) ) {
	/**
	 * @since 3.20.0
	 *
	 * @param int $agent_id
	 *
	 * @return void
	 *
	 * @deprecated
	 *
	 * This function checks if agent verification is enabled globally.
	 * And in case of enabled, it will check if the current agent is
	 * verified based on the given agent id.
	 *
	 */

	// TODO: This function is deprecated and will be removed in one of the next updates
	function realhomes_agent_verification_badge( int $agent_id = 0 ) {

		/* getting the global setting */
		$verification_status = get_option( 'realhomes_agents_verification_status', 'show' );

		if ( 'show' === $verification_status ) {

			if ( 0 < intval( $agent_id ) ) {

				// getting agent verification meta status
				$agent_verification = get_post_meta( $agent_id, 'ere_agent_verification_status', true );

				if ( $agent_verification ) {
					?>
                    <span class="rh_agent_verification__icon">
                        <?php inspiry_safe_include_svg( '/icons/verified-check.svg', '/common/images' ); ?>
                    </span>
					<?php
				}

			}

		}
	}
}

if ( ! function_exists( 'realhomes_verification_badge' ) ) {
	/**
	 * Displays verification badge for supported post types like agent, agency, owner, etc.
	 *
	 * Refactored to use cleaner and maintainable structure for post type mappings.
	 *
	 * @since 4.2.0
	 *
	 * @param string $post_type Post type (e.g., 'agent', 'agency', 'owner').
	 * @param int    $post_id   Post ID to check verification status for.
	 *
	 * @return void
	 */
	function realhomes_verification_badge( $post_type = 'agent', $post_id = 0 ) {

		if ( $post_id > 0 ) {

			$post_type_keys = array(
				'agent'     => array(
					'option' => 'realhomes_agents_verification_status',
					'meta'   => 'ere_agent_verification_status',
				),
				'agency'    => array(
					'option' => 'realhomes_agencies_verification_status',
					'meta'   => 'ere_agency_verification_status',
				),
				'owner'     => array(
					'option' => 'ere_owners_post_type_verification_status',
					'meta'   => 'rvr_owner_verification_status',
				)
			);

			$option_key = isset( $post_type_keys[ $post_type ] ) ? $post_type_keys[ $post_type ]['option'] : 'realhomes_post_type_verification_status';
			$meta_key   = isset( $post_type_keys[ $post_type ] ) ? $post_type_keys[ $post_type ]['meta']   : 'ere_post_type_verification_status';

			$verification_status = get_option( $option_key, 'show' );

			if ( 'show' === $verification_status ) {

				$entity_verified = get_post_meta( $post_id, $meta_key, true );

				if ( $entity_verified ) {
					?>
                    <span class="rh_main_verification__icon" aria-label="<?php esc_attr_e( 'Verified', 'framework' ); ?>">
						<?php inspiry_safe_include_svg( '/icons/verified-check.svg', '/common/images' ); ?>
					</span>
					<?php
				}
			}
		}
	}
}

/**
 * Displays verification badge for a user based on role and meta.
 *
 * @since 4.4.3
 *
 * @param int $user_id User ID.
 *
 * @return void
 */
if ( ! function_exists( 'realhomes_user_verification_badge' ) ) {
	function realhomes_user_verification_badge( $user_id = 0 ) {

		if ( $user_id <= 0 ) {
			return;
		}

		$role                 = realhomes_get_user_role( $user_id );
		$verification_enabled = realhomes_get_current_user_role_option( 'user_verification', $role, false );

		if ( $verification_enabled ) {
			$is_verified = get_user_meta( $user_id, 'realhomes_user_verification_status', true );

			if ( $is_verified ) {
				?>
                <span class="rh_main_verification__icon" aria-label="<?php esc_attr_e( 'Verified user', RH_TEXT_DOMAIN ); ?>">
					<?php inspiry_safe_include_svg( '/icons/verified-check.svg', '/common/images' ); ?>
				</span>
				<?php
			}
		}
	}
}


if ( ! function_exists( 'realhomes_print_loader_markup' ) ) {
	/**
	 * Prints the CSS loader html.
	 *
	 * @since 3.21.0
	 *
	 * @param string $class
	 *
	 * @return void
	 */
	function realhomes_print_loader_markup( $class = '' ) {
		$container_class = 'rh-loader';

		if ( ! empty( $class ) ) {
			$container_class .= " $class";
		}

		printf( '<div class="%s"><div></div><div></div><div></div><div></div></div>', esc_attr( $container_class ) );
	}
}

if ( ! function_exists( 'realhomes_display_schedule_a_tour' ) ) {
	/**
	 * Display 'Schedule A Tour' Section/Related Functions
	 *
	 * @since 4.0.0
	 *
	 * @return boolean
	 */
	function realhomes_display_schedule_a_tour() {

		if ( is_singular( 'property' ) ) {

			$schedule_meta_setting   = get_post_meta( get_the_ID(), 'ere_property_schedule_tour', true );
			$customizer_setting      = get_option( 'realhomes_display_schedule_a_tour', 'false' );
			$customizer_display_type = get_option( 'realhomes_display_schedule_a_tour_for', 'new_properties' );

			// if section display setting is not set in individual property as in existing properties case
			if ( empty( $schedule_meta_setting ) ) {

				// if customizer is set to be viewed only for new properties
				if ( $customizer_display_type == 'new_properties' ) {
					return false;
				} else {
					return true;
				}

			}

			if ( $schedule_meta_setting === 'show' ) {
				return true;
			}

			if ( $schedule_meta_setting === 'global' && $customizer_setting === 'true' ) {
				return true;
			}

		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_contents_area_loop' ) ) {
	/**
	 * Content Area Loop
	 *
	 * @since 4.0.0
	 *
	 */
	function realhomes_contents_area_loop() {

		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				?>
                <div class="rh_content rh_content_above_footer  <?php if ( get_the_content() ) {
					echo esc_attr( 'rh_page__content' );
				} ?>">
					<?php the_content(); ?>
                </div><!-- /.rh-content -->
				<?php

			}
		}
	}
}

if ( ! function_exists( 'realhomes_content_area_display' ) ) {
	/**
	 * Display content area as per position
	 *
	 * @since 4.0.0
	 *
	 */

	function realhomes_content_area_display() {

		$get_content_position = get_post_meta( get_queried_object_id(), 'REAL_HOMES_content_area_above_footer', true );
		if ( $get_content_position !== '1' ) {
			add_action( 'realhomes_content_area_at_top', 'realhomes_contents_area_loop' );
		} else {
			add_action( 'realhomes_content_area_at_bottom', 'realhomes_contents_area_loop' );
		}
	}

	add_action( 'wp_head', 'realhomes_content_area_display' );
}

if ( ! function_exists( 'realhomes_print_no_result' ) ) {
	/**
	 * Prints a no results message with customizable text and HTML classes.
	 *
	 * @since 4.0.0
	 *
	 * @param string $custom_message Custom no results text.
	 * @param array  $args           Additional arguments for container and heading classes.
	 */
	function realhomes_print_no_result( $custom_message = '', $args = array() ) {
		$message = esc_html__( 'No Results Found!', RH_TEXT_DOMAIN );

		if ( ! empty( $custom_message ) ) {
			$message = $custom_message;

		} else {
			$custom_text = get_option( 'realhomes_no_result_found_text', $message );

			if ( ! empty( $custom_text ) ) {
				$message = $custom_text;
			}
		}

		$defaults = array(
			'container_class' => '',
			'heading_class'   => 'no-results',
			'search_args'     => array()
		);

		$args = wp_parse_args( $args, $defaults );

		if ( 'classic' !== INSPIRY_DESIGN_VARIATION && is_page_template( 'templates/properties-search.php' ) ) {
			$search_args      = $args['search_args'];
			$no_results_title = get_option( 'realhomes_no_results_title', esc_html__( 'Oops! No Properties Found', RH_TEXT_DOMAIN ) );
			$no_results_text  = get_option( 'inspiry_search_template_no_result_text' );
			?>
            <div class="<?php echo esc_attr( $args['container_class'] ); ?> rh_alert-wrapper rh-alert-wrapper no-results-found">
                <div class="rh-no-results__content">
                    <img src="<?php echo INSPIRY_COMMON_URI; ?>/images/no-results.png" alt="<?php esc_attr_e( 'No properties found', RH_TEXT_DOMAIN ); ?>" class="rh-no-results__image">
					<?php
					if ( ! empty( $no_results_title ) ) {
						?>
                        <h3><?php echo esc_html( $no_results_title ); ?></h3>
						<?php
					}

					if ( ! empty( $no_results_text ) ) {
						?>
                        <p><?php echo esc_html( $no_results_text ); ?></p>
						<?php
					}
					?>

                    <div class="action-buttons">
						<?php
						if ( is_array( $search_args ) && ! empty( $search_args ) ) {
							get_template_part( 'assets/' . INSPIRY_DESIGN_VARIATION . '/partials/properties/save-alert-button', '', array( 'search_args' => $search_args ) );
						}

						if ( get_option( 'realhomes_no_results_reset_button', true ) ) {
							?>
                            <a href="<?php echo get_permalink( get_option( 'realhomes_search_page' ) ); ?>" class="rh-btn rh-btn--primary"><?php esc_html_e( 'Reset Search', RH_TEXT_DOMAIN ); ?></a>
							<?php
						}
						?>
                    </div>

                    <!-- Quick Popular Searches -->
					<?php
					if ( get_option( 'realhomes_no_results_taxonomies', true ) ) {
						$popular_searches_title = get_option( 'realhomes_no_results_taxonomies_title', 'You can also look into the popular searches' );
						?>
                        <div class="rh-popular-searches">
							<?php
							if ( ! empty( $popular_searches_title ) ) {
								?>
                                <p><strong><?php echo esc_html( $popular_searches_title ); ?></strong></p>
								<?php
							}
							?>

                            <ul>
								<?php
								// Array of taxonomies and their labels.
								$taxonomies = array( 'property-status', 'property-type', 'property-city', 'property-features' );

								foreach ( $taxonomies as $taxonomy ) {
									$top_terms = realhomes_get_top_terms_from_taxonomy( $taxonomy, 1 );

									if ( $top_terms ) {
										foreach ( $top_terms as $term ) {
                                            if ( ! is_wp_error( get_term_link( $term ) ) ) {
	                                            echo '<li><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
                                            }
										}
									}
								}
								?>
                            </ul>
                        </div>
						<?php
					}
					?>
                </div>
            </div>
			<?php
		} else {
			?>
            <div class="<?php echo esc_attr( $args['container_class'] ); ?>">
                <h4 class="<?php echo esc_attr( $args['heading_class'] ); ?>"><?php echo esc_html( $message ); ?></h4>
            </div>
			<?php
		}
	}
}

if ( ! function_exists( 'realhomes_custom_search_form_available' ) ) {
	/**
	 * Check if search form is available to be displayed
	 *
	 * @since 4.0.0
	 */
	function realhomes_custom_search_form_available() {

		$search_form_locations = get_option( 'search_form_locations' );

		if ( ! empty( $search_form_locations ) ) {

			$page_templates = array(
				'home'           => 'templates/home.php',
				'search'         => 'templates/properties-search.php',
				'gallery'        => 'templates/properties-gallery.php',
				'agents'         => 'templates/agents-list.php',
				'agencies'       => 'templates/agencies-list.php',
				'compare'        => 'templates/compare-properties.php',
				'contact'        => 'templates/contact.php',
				'login-register' => 'templates/login-register.php',
				'user-list'      => 'templates/users-lists.php',
			);

			$template_locations = array(
				'properties' => is_page_template( 'templates/properties.php' )
					|| is_tax( array( 'property-city', 'property-status', 'property-type', 'property-feature' ) )
					|| is_post_type_archive( 'property' ),

				'property' => is_singular( 'property' ),
				'agent'    => is_singular( 'agent' ),
				'agency'   => is_singular( 'agency' ),
				'blog'     => ( 'post' === get_post_type() ),
				'post'     => is_singular( 'post' ),
				'404'      => is_404(),
			);

			// Merge page template checks
			if ( is_page() && get_page_template_slug() ) {
				foreach ( $page_templates as $key => $template ) {
					$template_locations[ $key ] = is_page_template( $template );
				}
			}

			// Check search form locations
			foreach ( $template_locations as $location => $template ) {
				if ( in_array( $location, $search_form_locations, true ) && $template ) {
					return false;
				}
			}
		}

		return true;
	}
}

if ( ! function_exists( 'realhomes_single_property_print_sections' ) ) {
	/**
	 * Returns the single property sections array for print settings.
	 *
	 * @since 4.0.1
	 *
	 * @return array
	 */
	function realhomes_single_property_print_sections() {

		$property_sections = array(
			'content'            => esc_html__( 'Content', RH_TEXT_DOMAIN ),
			'additional-details' => esc_html__( 'Additional Details', RH_TEXT_DOMAIN ),
			'common-note'        => esc_html__( 'Common Note', RH_TEXT_DOMAIN ),
			'features'           => esc_html__( 'Features', RH_TEXT_DOMAIN ),
			'floor-plans'        => esc_html__( 'Floor Plans', RH_TEXT_DOMAIN ),
			'map'                => esc_html__( 'Map', RH_TEXT_DOMAIN ),
			'walkscore'          => esc_html__( 'WalkScore', RH_TEXT_DOMAIN ),
			'energy-performance' => esc_html__( 'Energy Performance', RH_TEXT_DOMAIN ),
			'agent'              => esc_html__( 'Agents', RH_TEXT_DOMAIN ),
		);

		if ( inspiry_is_rvr_enabled() ) {
			$property_sections = array_merge( $property_sections, array(
				'rvr/location-surroundings' => esc_html__( 'Property Surroundings', RH_TEXT_DOMAIN ),
				'rvr/optional-services'     => esc_html__( 'Services', RH_TEXT_DOMAIN ),
				'rvr/property-policies'     => esc_html__( 'Property Rules', RH_TEXT_DOMAIN ),
				'rvr/guests-accommodation'  => esc_html__( 'Guests Accommodation', RH_TEXT_DOMAIN ),
				'rvr/price-details'         => esc_html__( 'Price Details', RH_TEXT_DOMAIN ),
				'rvr/seasonal-prices'       => esc_html__( 'Seasonal Prices', RH_TEXT_DOMAIN ),
			) );
		}

		return $property_sections;
	}
}

if ( ! function_exists( 'realhomes_is_custom_print_setting' ) ) {
	/**
	 * Determines whether the custom print styles option is selected.
	 *
	 * @since 4.0.1
	 *
	 * @return bool
	 */
	function realhomes_is_custom_print_setting() {

		if ( 'custom' === get_option( 'realhomes_single_property_print_setting', 'default' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_printable_section' ) ) {
	/**
	 * Adds a CSS class to the section if it is enabled for the property print,
	 * when the custom print styles option is selected.
	 *
	 * @since 4.0.1
	 *
	 * @param $section
	 * @param $echo
	 *
	 * @return void|string Void if `$echo` argument is true, otherwise print related css class if `$echo` argument is false.
	 */
	function realhomes_printable_section( $section, $echo = true ) {

		if ( ! realhomes_is_custom_print_setting() ) {
			return '';
		}

		$section_class      = 'print-disabled';
		$printable_sections = get_option( 'realhomes_single_property_printable_sections', array_keys( realhomes_single_property_print_sections() ) );

		if ( ! empty( $printable_sections ) && is_array( $printable_sections ) ) {
			if ( in_array( $section, $printable_sections ) ) {
				$section_class = 'print-enabled';
			}
		}

		if ( $echo ) {
			echo $section_class;
		} else {
			return $section_class;
		}
	}
}

if ( ! function_exists( 'realhomes_apply_wp_date_format' ) ) {
	/**
	 * Apply WordPress Global Date Format
	 *
	 * @since 4.1.1
	 *
	 * @param $date
	 *
	 * @return false|string
	 */
	function realhomes_apply_wp_date_format( $date ) {

		/* Getting RVR Date Format Settings */
		$rvr_settings           = get_option( 'rvr_settings' );
		$rvr_date_format_method = $rvr_settings['rvr_date_format_method'];
		$rvr_custom_date_format = $rvr_settings['rvr_date_format_custom'];

		// WordPress Date Format
		if ( 'wordpress' === $rvr_date_format_method ) {
			return date( get_option( 'date_format' ), strtotime( $date ) );
		}

		// Custom Date Format from RVR Settings
		if ( 'custom' === $rvr_date_format_method ) {

			/* Validating the custom date format to PHP Date Format */
			$rvr_date_year  = str_replace( 'YYYY', 'Y', $rvr_custom_date_format );
			$rvr_date_day   = str_replace( 'DD', 'd', $rvr_date_year );
			$rvr_date_final = str_replace( 'MM', 'm', $rvr_date_day );

			return date( $rvr_date_final, strtotime( $date ) );
		}

		return $date;

	}
}

if ( ! function_exists( 'realhomes_is_half_map_template' ) ) {
	/**
	 * Checks properties and properties search templates for half map layout.
	 * *
	 * @since 4.2.0
	 *
	 * @param string $context 'property|search|both'
	 *
	 * @return bool
	 */
	function realhomes_is_half_map_template( $context = 'both' ) {

		$templates         = array();
		$property_template = 'templates/properties.php';
		$search_template   = 'templates/properties-search.php';

		if ( 'property' === $context ) {
			$templates[] = $property_template;
		} else if ( 'search' === $context ) {
			$templates[] = $search_template;
		} else {
			$templates[] = $property_template;
			$templates[] = $search_template;
		}

		if ( is_page_template( $templates ) && ( '1' === get_post_meta( get_the_ID(), 'realhomes_property_half_map', true ) ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_is_header_search_form_configured' ) ) {
	/**
	 * This function checks if search form is configured to show in header by customizer or/and page/post meta options
	 *
	 * @since 4.2.0
	 *
	 * @return boolean
	 */
	function realhomes_is_header_search_form_configured() {

		$current_page_id               = get_queried_object_id();
		$inspiry_show_search_in_header = boolval( get_option( 'inspiry_show_search_in_header', 1 ) );
		$current_page_search_form      = boolval( get_post_meta( $current_page_id, 'REAL_HOMES_hide_advance_search', true ) );
		$theme_search_fields           = get_option( 'theme_search_fields' );
		$ultra_custom_search_form      = get_option( 'realhomes_custom_search_form', 'default' );
		$ultra_hide_search_pages       = get_option( 'search_form_locations' );
		$form_status                   = false;

		if ( $inspiry_show_search_in_header && ! $current_page_search_form ) {
			$form_status = true;
		}

		if ( INSPIRY_DESIGN_VARIATION === 'ultra' ) {

			if ( $ultra_custom_search_form !== 'default' ) {
				if (
					is_array( $ultra_hide_search_pages )
					&& ! in_array( 'properties', $ultra_hide_search_pages )
					&& is_page_template( array( 'templates/properties.php' ) )
					&& $form_status
				) {
					return true;
				}
			}

			return false;

		} else {

			if (
				! empty( $theme_search_fields )
				&& is_array( $theme_search_fields )
				&& $form_status
			) {

				return true;

			}
		}

		return false;
	}
}


if ( ! function_exists( 'realhomes_get_current_user_role_option' ) ) {
	/**
	 * Retrieves the value of a specific user role option.
	 *
	 * This function fetches a role-based setting (e.g., permissions) from 'ere_user_role_options'.
	 * - If `$option` is `'label'`, it returns the human-readable label for the given role.
	 * - If `$role` is not provided, it uses the current user's primary role (if logged in).
	 * - If `$login_required` is true, function exits early for non-logged-in users.
	 *
	 * @since 4.3.0
	 *
	 * @param string       $option         Option key to fetch (e.g., manage_profile, property_submit, etc.).
	 * @param string|false $role           Specific user role; if false, uses current logged-in user's role.
	 * @param bool         $login_required Whether login is required. If false, role must be provided manually.
	 *
	 * @return string|bool Returns the option value (usually boolean), label string, or false if not found.
	 */
	function realhomes_get_current_user_role_option( $option = 'label', $role = false, $login_required = true ) {

		if ( $login_required && ! is_user_logged_in() ) {
			return false;
		}

		// Return label directly if requested and role is given
		if ( $option === 'label' && ! empty( $role ) ) {
			$roles_settings = get_option( 'ere_user_role_options' );
			if ( isset( $roles_settings[ $role ]['label'] ) ) {
				return $roles_settings[ $role ]['label'];
			}
			return ucfirst( $role );
		}

		$current_role = $role;

		// Detect current user role only if login is required and no role is provided
		if ( $login_required && false === $role ) {
			global $current_user;

			if ( ! is_object( $current_user ) ) {
				$current_user = wp_get_current_user();
			}

			$roles = (array) $current_user->roles;
			$current_role = reset( $roles );
		}

		// Do not continue without a role
		if ( empty( $current_role ) ) {
			return false;
		}

		$roles_settings = get_option( 'ere_user_role_options' );

		// Skip admin bypass if login is not required
		if ( $login_required && current_user_can( 'manage_options' ) ) {
			return true;
		}

		if ( isset( $roles_settings[ $current_role ][ $option ] ) ) {
			return filter_var( $roles_settings[ $current_role ][ $option ], FILTER_VALIDATE_BOOLEAN );
		}

		return false;
	}
}



if ( ! function_exists( 'realhomes_get_user_role' ) ) {
	/**
	 * Return user role of the given user ID or current
	 *
	 * @since 4.3.0
	 *
	 * @param int $user_id
	 *
	 * @return boolean
	 */
	function realhomes_get_user_role( $user_id = 0 ) {

		if ( 0 < intval( $user_id ) ) {
			$user = get_user_by( 'id', $user_id );
		} else {
			$user = wp_get_current_user();
		}

		if ( ! is_wp_error( $user ) && $user->ID ) {
			$current_role = $user->roles;

			// Just to be on the safe side if array is displaced
			return reset( $current_role );
		}

		return $user;
	}
}

if ( ! function_exists( 'realhomes_get_agent_properties_stats_by_taxonomy' ) ) {
	/**
	 * Returns an array with properties stats by taxonomy for given agent ID/s
	 *
	 * @since 4.3.0
	 *
	 * @param array  $agent_ids     Array of Post IDs of the provided post type
	 * @param string $taxonomy_name Name of the taxonomy you want to generate chart for
	 *
	 * @return array|bool
	 */
	function realhomes_get_agent_properties_stats_by_taxonomy( $agent_ids = 0, $taxonomy_name = 'property-city' ) {

		if ( $agent_ids && ! empty( $taxonomy_name ) ) {

			$property_stats = array();
			$tax_terms      = get_terms( array( 'taxonomy' => $taxonomy_name ) );

			$properties = get_posts(
				array(
					'post_type'      => 'property',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => 'REAL_HOMES_agents',
							'value'   => $agent_ids,
							'compare' => 'IN'
						),
					)
				)
			);

			if ( ! is_wp_error( $tax_terms ) && 0 < count( $tax_terms ) && 0 < count( $properties ) ) {
				foreach ( $tax_terms as $term ) {
					// Fetching properties by term and agent
					$term_properties = get_posts(
						array(
							'post_type'      => 'property',
							'posts_per_page' => -1,
							'post_status'    => 'publish',
							'meta_query'     => array(
								'relation' => 'AND',
								array(
									'key'     => 'REAL_HOMES_agents',
									'value'   => $agent_ids,
									'compare' => 'IN'
								),
							),
							'tax_query'      => array(
								array(
									'taxonomy'         => $taxonomy_name,
									'field'            => 'slug',
									'terms'            => $term->slug,
									'include_children' => false
								)
							)
						)
					);

					if ( 0 < count( $term_properties ) ) {
						$property_stats['terms'][ $term->term_id ]['slug']  = $term->slug;
						$property_stats['terms'][ $term->term_id ]['name']  = $term->name;
						$property_stats['terms'][ $term->term_id ]['count'] = count( $term_properties );
					}
				}
			}

			return $property_stats;
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_generate_properties_stats_chart' ) ) {
	/**
	 * Generate property stats doughnut chart html for the agent/agency by given taxonomy.
	 *
	 * @since 4.3.0
	 *
	 * @param array $details       {
	 *                             Details for the function.
	 *
	 * @type int    $post_id       Post ID of the provided post type.
	 * @type string $taxonomy_name Name of the taxonomy you want to generate chart for. Default is 'property-city'.
	 * @type string $post_type     Post-type to generate stats chart for (agent|agency). Default is 'agent'.
	 * @type int    $break_point   The number of items before grouping others. Default is 3.
	 *                             }
	 */
	function realhomes_generate_properties_stats_chart( $details = array() ) {
		// Set default values
		$defaults = array(
			'post_id'       => 0,
			'taxonomy_name' => 'property-city',
			'post_type'     => 'agent',
			'break_point'   => 3,
			'colors'        => []
		);

		// Merge provided details with defaults
		$details = wp_parse_args( $details, $defaults );

		// Extract variables from the $details array
		$post_id       = $details['post_id'];
		$taxonomy_name = $details['taxonomy_name'];
		$post_type     = $details['post_type'];
		$break_point   = $details['break_point'];
		$custom_colors = $details['colors'];

		if ( empty( $post_id ) || empty( $taxonomy_name ) || empty( $post_type ) ) {
			return;
		}

		$agents_ids = $info_stats = $chart_stats = array();

		if ( $post_type === 'agency' ) {

			$agency_agents = get_posts( array(
					'post_type'      => 'agent',
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'     => 'REAL_HOMES_agency',
							'value'   => $post_id,
							'compare' => '='
						)
					)
				)
			);

			// Return at this point if no agents or error
			if ( is_wp_error( $agency_agents ) || 1 > count( $agency_agents ) ) {
				?>
                <div class="stats-wrap no-stats">
                    <p><?php esc_html_e( 'No Stats Available!', RH_TEXT_DOMAIN ); ?></p>
                </div>
				<?php
				return;
			}

			foreach ( $agency_agents as $agent ) {
				$agents_ids[] = $agent->ID;
			}
		} else {
			// Default case as agent
			$agents_ids = array( $post_id );
		}

		// Getting terms in array with properties count of current agent/s
		$property_stats = realhomes_get_agent_properties_stats_by_taxonomy( $agents_ids, $taxonomy_name );

		// Sorting stats by count
		if ( ! empty( $property_stats['terms'] ) && is_array( $property_stats['terms'] ) ) {
			usort( $property_stats['terms'], function ( $a, $b ) {
				return $b['count'] <=> $a['count']; // Compare count values in descending order
			} );

			// Separate the first some terms and group the rest into "Others"
			$stat_top_terms       = array_slice( $property_stats['terms'], 0, $break_point );
			$stat_remaining_terms = array_slice( $property_stats['terms'], $break_point );

			// Combining remaining terms into "Others"
			if ( ! empty( $stat_remaining_terms ) ) {
				$others_count     = array_sum( array_column( $stat_remaining_terms, 'count' ) );
				$stat_top_terms[] = array(
					'slug'  => 'others',
					'name'  => esc_html__( 'Others', RH_TEXT_DOMAIN ),
					'count' => $others_count
				);
			}

			// Update the array with the modified terms
			$property_stats['terms'] = $stat_top_terms;

			if ( 0 < count( $property_stats ) ) {

				// Getting total agent properties
				$total_props           = array_sum( array_column( $property_stats['terms'], 'count' ) );
				$default_color_counter = $props_counter = 0;
				$default_colors        = array(
					'#ffb3c2',
					'#a1d0f6',
					'#ffe6ae',
					'#aae0e0',
					'#bdc9ff',
					'#e5bada',
					'#e5daba',
					'#7f86ac',
					'#ff4271',
					'#89ff42'
				);

				if ( is_array( $custom_colors ) && count( $custom_colors ) ) {
					foreach ( $custom_colors as $index => $color ) {
						if ( isset( $default_colors[ $index ] ) ) {
							$default_colors[ $index ] = $color;
						}
					}
				}

				if ( isset( $property_stats['terms'] ) ) {
					foreach ( $property_stats['terms'] as $term_id => $term ) {

						$props_counter += $term['count'];
						$percentage    = ( $term['count'] / $total_props ) * 100;

						// Getting assigned color of the term
						$term_color = get_term_meta( $term_id, 'ere_property_taxonomy_term_color', true );

						// Assigning a random color if no color is assigned to this taxonomy term
						if ( empty( $term_color ) ) {
							$term_color = $default_colors[ $default_color_counter++ ];
						}

						$info_stats[ $term['slug'] ] = array(
							'term_id'   => $term_id,
							'term_name' => $term['name'],
							'count'     => $term['count'],
							'percent'   => $percentage,
							'color'     => $term_color
						);

						$chart_stats['labels'][] = $term['name'];
						$chart_stats['values'][] = round( $percentage );
						$chart_stats['colors'][] = $term_color;
					}
				}

				// If there are Unassigned properties, those will fall into 'Others' Category
				if ( $props_counter < $total_props ) {
					$others_props      = $total_props - $props_counter;
					$others_percentage = ( $others_props / $total_props ) * 100;
					$other_color       = '#dddddd';

					$info_stats['others'] = array(
						'term_name' => esc_html__( 'Others', RH_TEXT_DOMAIN ),
						'count'     => $others_props,
						'percent'   => $others_percentage,
						'color'     => $other_color
					);

					$chart_stats['labels'][] = esc_html__( 'Others', RH_TEXT_DOMAIN );
					$chart_stats['values'][] = round( $others_percentage );
					$chart_stats['colors'][] = $other_color;
				}
			}
		}

		if ( 0 < count( $info_stats ) && 0 < count( $chart_stats ) ) {
			?>
            <div class="stats-wrap">
                <div class="chart-wrapper">
                    <canvas class="tax-stats-chart" data-chart-stats='<?php echo json_encode( $chart_stats ); ?>' width="110px" height="110px"></canvas>
                </div>
                <ul class="stats-<?php echo esc_attr( $taxonomy_name ); ?>">
					<?php
					foreach ( $info_stats as $stat ) {
						?>
                        <li>
                            <i <?php echo ! empty( $stat['color'] ) ? 'style="background-color: ' . esc_attr( $stat['color'] ) . '"' : ''; ?>></i>
                            <strong><?php echo esc_html( round( $stat['percent'] ) ); ?>%</strong> <?php echo esc_html( $stat['term_name'] ); ?>
                        </li>
						<?php
					}
					?>
                </ul>
            </div>
			<?php
		} else {
			?>
            <div class="stats-wrap no-stats">
                <p><?php esc_html_e( 'No Stats Available!', RH_TEXT_DOMAIN ); ?></p>
            </div>
			<?php
		}

	}
}

if ( ! function_exists( 'realhomes_report_property_modal' ) ) {
	/**
	 * Generate report property model in the footer if required
	 *
	 * @since 4.3.0
	 */
	function realhomes_report_property_modal() {
		get_template_part( 'common/partials/report-property-modal' );
	}
}

if ( ! function_exists( 'realhomes_get_property_field_value' ) ) {
	/**
	 * Retrieves the given property meta value if not empty, otherwise global value.
	 *
	 * This function fetches a meta value for a specific property. If the meta value is empty,
	 * it retrieves a global option value instead. If neither is found, it returns a default value.
	 *
	 * @since 4.3.2
	 *
	 * @param int    $property_id   The ID of the property.
	 * @param string $meta_key      The meta key to retrieve the value for.
	 * @param string $option_name   The name of the global option to retrieve if the meta value is empty.
	 * @param mixed  $default_value The default value to return if neither meta nor option value is found. Default is an empty string.
	 *
	 * @return mixed The property meta value, global option value, or default value.
	 */
	function realhomes_get_property_field_value( $property_id, $meta_key, $option_name, $default_value = '' ) {
		$field_value = get_post_meta( $property_id, $meta_key, true );
		if ( ! empty( $field_value ) ) {

			if ( in_array( $field_value, array( 'Null', 'null', 'None', 'none', 'Empty', 'empty' ) ) ) {
				return false;
			}

			return $field_value;
		}

		$field_value = get_option( $option_name, $default_value );
		if ( ! empty( $field_value ) ) {
			return $field_value;
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_get_area_unit' ) ) {
	/**
	 * Retrieves the area size unit for the given property if not empty, otherwise global value.
	 *
	 * This function fetches the area size unit for a specific property from its meta data.
	 * If the meta value is empty, it retrieves the global area size unit option.
	 *
	 * @since 4.3.2
	 *
	 * @param int $property_id The ID of the property.
	 *
	 * @return string The area size unit for the property or global area size unit.
	 */
	function realhomes_get_area_unit( $property_id ) {
		return realhomes_get_property_field_value( $property_id, 'REAL_HOMES_property_size_postfix', 'ere_area_unit', esc_html__( 'sq ft', RH_TEXT_DOMAIN ) );
	}
}

if ( ! function_exists( 'realhomes_get_lot_unit' ) ) {
	/**
	 * Retrieves the lot size unit for the given property if not empty, otherwise global value.
	 *
	 * This function fetches the lot size unit for a specific property from its meta data.
	 * If the meta value is empty, it retrieves the global lot size unit option.
	 *
	 * @since 4.3.2
	 *
	 * @param int $property_id The ID of the property.
	 *
	 * @return string The lot size unit for the property or global lot size unit.
	 */
	function realhomes_get_lot_unit( $property_id ) {
		return realhomes_get_property_field_value( $property_id, 'REAL_HOMES_property_lot_size_postfix', 'ere_lot_unit', esc_html__( 'sq ft', RH_TEXT_DOMAIN ) );
	}
}

if ( ! function_exists( 'realhomes_get_price_prefix' ) ) {
	/**
	 * Retrieves the price prefix for the given property if not empty, otherwise global value.
	 *
	 * This function fetches the price prefix for a specific property from its meta data.
	 * If the meta value is empty, it retrieves the global price prefix option.
	 *
	 * @since 4.3.2
	 *
	 * @param int $property_id The ID of the property.
	 *
	 * @return string The price prefix for the property or global price prefix.
	 */
	function realhomes_get_price_prefix( $property_id ) {
		return realhomes_get_property_field_value( $property_id, 'REAL_HOMES_property_price_prefix', 'ere_price_prefix' );
	}
}

if ( ! function_exists( 'realhomes_get_price_postfix' ) ) {
	/**
	 * Retrieves the price postfix for the given property if not empty, otherwise global value.
	 *
	 * This function fetches the price postfix for a specific property from its meta data.
	 * If the meta value is empty, it retrieves the global price postfix option.
	 *
	 * @since 4.3.2
	 *
	 * @param int $property_id The ID of the property.
	 *
	 * @return string The price postfix for the property or global price postfix.
	 */
	function realhomes_get_price_postfix( $property_id ) {
		return realhomes_get_property_field_value( $property_id, 'REAL_HOMES_property_price_postfix', 'ere_price_postfix' );
	}
}

if ( ! function_exists( 'realhomes_get_post_author_meta' ) ) {
	/**
	 * Function to get the post author's meta using the post ID and author meta key.
	 *
	 * @since 4.3.2
	 *
	 * @param $post_id
	 * @param $meta_key
	 *
	 * @return false|string
	 */
	function realhomes_get_post_author_meta( $post_id, $meta_key = 'user_email' ) {
		// Get the post object
		$post = get_post( $post_id );

		// Check if the post object is valid
		if ( $post ) {
			// Get the author ID
			$author_id = $post->post_author;

			// Get the author's meta info
			return get_the_author_meta( $meta_key, $author_id );
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_are_emails' ) ) {
	/**
	 * Function to validate multiple comma-separated emails
	 *
	 * @since 4.3.3
	 *
	 * @param $emails
	 *
	 * @return boolean
	 */
	function realhomes_are_emails( $emails ) {
		// Split the string by commas, trim each email, and filter out any empty strings
		$email_array = array_map( 'trim', explode( ',', $emails ) );

		// Check each email using is_email()
		foreach ( $email_array as $email ) {
			if ( ! is_email( $email ) ) {
				return false; // If any email is invalid, return false
			}
		}

		return true; // All emails are valid
	}
}

if ( ! function_exists( 'realhomes_generate_sort_control_option' ) ) {
	/**
	 * Generates the sorting dropdown control.
	 *
	 * @param array $args {
	 *     Optional. Arguments to customize the sorting dropdown.
	 *
	 *     @type string  $menu_id           HTML ID for the select dropdown. Default 'sort-properties'.
	 *     @type string  $container_classes CSS classes for the wrapping div. Default 'rh-hide-before-ready'.
	 *     @type string  $select_classes    CSS classes for the select dropdown. Default 'rh-ultra-select-dropdown rh-ultra-select-light show-tick'.
	 *     @type bool    $show_title        Whether to show the menu title. Default true.
	 *     @type string  $menu_title        Title label for the dropdown. Default 'Sort By'.
	 *     @type string  $selected_item     Currently selected sorting option. Default false (use site default).
	 * }
	 */
	function realhomes_generate_sort_control_option( $args = array() ) {
		// Set default values
		$args = wp_parse_args( $args, array(
			'menu_id'           => 'sort-properties',
			'container_classes' => 'rh-hide-before-ready',
			'select_classes'    => 'rh-ultra-select-dropdown rh-ultra-select-light show-tick',
			'show_title'        => true,
			'menu_title'        => esc_html__( 'Sort By:', RH_TEXT_DOMAIN ),
			'selected_item'     => false,
		));

		// Get enabled sorting options from theme settings
		$enabled_options = get_option( 'realhomes_available_sorting_options', array( 'price-asc', 'price-desc', 'date-asc', 'date-desc' ) );

		// Sorting options with labels
		$sorting_option_fields = realhomes_sorting_options_list();

		// Ensure selected_item is a valid option, otherwise fallback to default
		$sort_by = in_array( $args['selected_item'], array_keys( $sorting_option_fields ), true ) ? $args['selected_item'] : inspiry_get_properties_sort_by_value();

		// Filter enabled options to ensure they exist in the available sort options
		$valid_sort_options = array_intersect_key( $sorting_option_fields, array_flip( $enabled_options ) );
		?>
        <div class="rh_sort_controls <?php echo esc_attr( $args['container_classes'] ); ?>">
			<?php
            if ( $args['show_title'] ) {
                ?>
                <label for="<?php echo esc_attr( $args['menu_id'] ); ?>"><?php echo esc_html( $args['menu_title'] ); ?></label>
                <?php
            }
            ?>
            <select name="<?php echo esc_attr( $args['menu_id'] ); ?>" id="<?php echo esc_attr( $args['menu_id'] ); ?>" class="inspiry_select_picker_trigger <?php echo esc_attr( $args['select_classes'] ); ?>">
                <option value="default" <?php selected( $sort_by, 'default' ); ?>><?php esc_html_e( 'Default Order', RH_TEXT_DOMAIN ); ?></option>
                <option value="title-asc" <?php selected( $sort_by, 'title-asc' ); ?>><?php esc_html_e( 'Property Title A to Z', RH_TEXT_DOMAIN ); ?></option>
                <option value="title-desc" <?php selected( $sort_by, 'title-desc' ); ?>><?php esc_html_e( 'Property Title Z to A', RH_TEXT_DOMAIN ); ?></option>
				<?php
                foreach ( $valid_sort_options as $key => $label ) {
                    ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $sort_by, $key ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php
                }
                ?>
            </select>
        </div>

		<?php
	}
}

if ( ! function_exists( 'realhomes_sorting_options_list' ) ) {
	/**
	 * Retrieves an array of sorting options for properties.
	 *
	 * This function returns a list of sorting options with their corresponding labels.
	 * If specific options need to be excluded, they can be provided as an array via the `$skip` parameter.
	 *
	 * @param array $skip Optional. An array of sorting keys to exclude from the returned list.
	 * @return array Filtered sorting options.
	 */
	function realhomes_sorting_options_list( $skip = array() ) {
		$sorting_options = array(
			'price-asc'   => esc_html__( 'Price - Low to High', RH_TEXT_DOMAIN ),
			'price-desc'  => esc_html__( 'Price - High to Low', RH_TEXT_DOMAIN ),
			'date-asc'    => esc_html__( 'Date - Old to New', RH_TEXT_DOMAIN ),
			'date-desc'   => esc_html__( 'Date - New to Old', RH_TEXT_DOMAIN ),
			'bed-asc'     => esc_html__( 'Beds - Fewest to More', RH_TEXT_DOMAIN ),
			'bed-desc'    => esc_html__( 'Beds - More to Fewer', RH_TEXT_DOMAIN ),
			'bath-asc'    => esc_html__( 'Baths - Fewest to More', RH_TEXT_DOMAIN ),
			'bath-desc'   => esc_html__( 'Baths - More to Fewer', RH_TEXT_DOMAIN ),
			'area-asc'    => esc_html__( 'Area - Small to Large', RH_TEXT_DOMAIN ),
			'area-desc'   => esc_html__( 'Area - Large to Small', RH_TEXT_DOMAIN ),
			'lot-asc'     => esc_html__( 'Lot Size - Small to Large', RH_TEXT_DOMAIN ),
			'lot-desc'    => esc_html__( 'Lot Size - Large to Small', RH_TEXT_DOMAIN ),
			'garage-asc'  => esc_html__( 'Garages - Fewest to More', RH_TEXT_DOMAIN ),
			'garage-desc' => esc_html__( 'Garages - More to Fewer', RH_TEXT_DOMAIN ),
			'year-asc'    => esc_html__( 'Year Built - Old to New', RH_TEXT_DOMAIN ),
			'year-desc'   => esc_html__( 'Year Built - New to Old', RH_TEXT_DOMAIN ),
			'views-asc'    => esc_html__( 'Views - Fewer to More', RH_TEXT_DOMAIN ),
			'views-desc'   => esc_html__( 'Views - More to Fewer', RH_TEXT_DOMAIN ),
		);

		// Remove skipped options if provided
		if ( ! empty( $skip ) && is_array( $skip ) ) {
			$sorting_options = array_diff_key( $sorting_options, array_flip( $skip ) );
		}

		return $sorting_options;
	}
}

if ( ! function_exists( 'realhomes_password_protected_template' ) ) {
	/**
	 * Redirects password-protected pages to a central password template.
	 *
	 * @since 4.3.8
	 *
	 * @param string $template The path to the template file.
	 *
	 * @return string Modified template path for password-protected pages.
	 */
	function realhomes_password_protected_template( $template ) {
		if ( post_password_required() ) {
			$password_protect_template = get_template_directory() . '/templates/password-protect.php';

			if ( file_exists( $password_protect_template ) ) {
				return $password_protect_template;
			}
		}

		return $template;
	}


	add_filter( 'template_include', 'realhomes_password_protected_template', 99 );
}

if ( ! function_exists( 'realhomes_remove_protected_prefix' ) ) {
	/**
	 * Removes the "Protected:" prefix from password-protected post titles.
	 *
	 * @since 4.3.8
	 *
	 * @param string $format The default format with "Protected:".
	 *
	 * @return string Modified format without "Protected:".
	 */
	function realhomes_remove_protected_prefix( $format ) {
		return '%s'; // Removes "Protected:" while keeping the actual title
	}

	add_filter( 'protected_title_format', 'realhomes_remove_protected_prefix' );
}

if ( ! function_exists( 'realhomes_get_top_terms_from_taxonomy' ) ) {
	/**
	 * Get the top terms from a given taxonomy based on the number of associated properties.
	 *
	 * @since 4.3.8
	 *
	 * @param string $taxonomy The taxonomy to retrieve the terms from.
	 * @param int    $number   The number of terms to retrieve. Default is 1.
	 *
	 * @return array|false Array of top terms objects, or false if no terms are found.
	 */
	function realhomes_get_top_terms_from_taxonomy( $taxonomy, $number = 1 ) {
		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => $number,
			'hide_empty' => true,
		) );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			return $terms;
		}

		return false;
	}
}

if ( ! function_exists( 'realhomes_render_user_menu' ) ) {
	/**
	 * Renders the user menu for different locations.
	 *
	 * @param string $context Menu context (dashboard, modern, ultra).
	 *
	 * @since 4.3.8
	 */
	function realhomes_render_user_menu( $context = 'dashboard' ) {
		$is_user_logged_in     = is_user_logged_in();
		$favorites_after_login = get_option( 'inspiry_login_on_fav', 'no' );
		$favorites_url         = realhomes_get_dashboard_page_url( 'favorites' );
		$searches_after_login  = get_option( 'realhomes_saved_searches_required_login', 'no' );
		$saved_searches_url    = realhomes_get_dashboard_page_url( 'saved-searches' );
		$current_module        = isset( $_GET['module'] ) ? sanitize_text_field( $_GET['module'] ) : '';

		// Common menu items
		$menu_items = [
			'analytics'      => [
				'icon'      => 'analytics-icon.svg',
				'path'      => 'common/images/icons/',
				'condition' => 'show' === get_option( 'realhomes_dashboard_analytics_module', 'show' ) && inspiry_is_property_analytics_enabled() && realhomes_get_current_user_role_option( 'property_analytics' ),
			],
			'properties-crm' => [
				'icon'      => 'properties-crm.svg',
				'path'      => 'common/images/icons/',
				'condition' => 'show' === get_option( 'realhomes_dashboard_crm_module', 'show' ) && class_exists( 'Realhomes_Crm' ) && realhomes_get_current_user_role_option( 'manage_crm' ),
			],
			'properties'     => [
				'icon'      => 'icon-dash-my-properties.svg',
				'path'      => 'common/images/',
				'condition' => realhomes_dashboard_module_enabled( 'inspiry_properties_module_display' ) && realhomes_get_current_user_role_option( 'manage_listings' ) && inspiry_no_membership_disable_stuff(),
			],
			'favorites'      => [
				'icon'      => 'icon-dash-favorite.svg',
				'path'      => 'common/images/',
				'condition' => realhomes_dashboard_module_enabled( 'inspiry_favorites_module_display' ) && ( ( $is_user_logged_in && realhomes_get_current_user_role_option( 'manage_favorites' ) ) || 'no' === $favorites_after_login ),
			],
			'saved-searches' => [
				'icon'      => 'icon-dash-alert.svg',
				'path'      => 'common/images/',
				'condition' => inspiry_is_save_search_enabled() && ( ( $is_user_logged_in && realhomes_get_current_user_role_option( 'manage_searches' ) ) || 'no' === $searches_after_login ),
			],
			'membership'     => [
				'icon'      => 'icon-membership.svg',
				'path'      => 'common/images/',
				'condition' => function_exists( 'IMS_Helper_Functions' ) && IMS_Helper_Functions()::is_memberships() && realhomes_get_current_user_role_option( 'memberships' ),
			],
			'profile'        => [
				'icon'      => 'icon-dash-profile.svg',
				'path'      => 'common/images/',
				'condition' => realhomes_dashboard_module_enabled( 'inspiry_profile_module_display' ) && realhomes_get_current_user_role_option( 'manage_profile' ),
			],
			'logout'         => [
				'icon'      => 'icon-dash-logout.svg',
				'path'      => 'common/images/',
				'condition' => $is_user_logged_in,
				'url'       => wp_logout_url( home_url() ),
			]
		];

		// Start menu wrapper
		if ( 'dashboard' === $context ) {
			echo '<div class="dashboard-menu">';
		} else {
			if ( ! $is_user_logged_in && 'no' !== $favorites_after_login && 'no' !== $searches_after_login ) {
				return;
			}

			$wrapper_class = ( 'modern' === $context ) ? 'rh_modal' : 'rh-ultra-modal';
            ?>
            <div class="<?php echo esc_attr( $wrapper_class ); ?>">
            <div class="rh_modal__corner"></div><div class="rh_modal__wrap">
			<?php
			// User Avatar and Info for Modern & Ultra
			if ( $is_user_logged_in ) {
				$current_user = wp_get_current_user();
				?>
                <div class="rh_user">
                    <div class="rh_user__avatar">
						<?php
						$gravatar = RealHomes_Cache::get_cached_avatar();
						if ( $gravatar ) {
							echo wp_kses_post( $gravatar );

						} else {
							if ( 'modern' === $context ) {
								inspiry_safe_include_svg( 'images/icon-profile.svg', '/common/' );
							} else {
								inspiry_safe_include_svg( '/icons/user-dash/icon-profile.svg' );
							}
						}
						?>
                    </div>
                    <div class="rh_user__details">
                        <p class="rh_user__msg"><?php esc_html_e( 'Welcome', RH_TEXT_DOMAIN ); ?></p>
                        <h3 class="rh_user__name"><?php esc_html_e( $current_user->display_name ); ?></h3>
                    </div>
                </div>
				<?php
			}
		}

        $user_menu_classes = 'rh_modal__dashboard';
        $menu_item_classes = 'rh_modal__dash_link';

        if ( 'ultra' === $context ) {
            $user_menu_classes = 'rh_modal__dashboard rh-trigger-nav';
        }
        ?>
        <div class="<?php echo esc_attr( $user_menu_classes ); ?>">
            <?php
            foreach ( $menu_items as $key => $item ) {
                if ( ! empty( $item['condition'] ) && ! empty( realhomes_get_dashboard_page_url( $key ) ) ) {
	                $current_menu_item = ( $current_module === $key ) ? ' current' : '';
                    $url          = ! empty( $item['url'] ) ? $item['url'] : realhomes_get_dashboard_page_url( $key );
                    ?>
                    <a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $menu_item_classes ) . esc_attr( $current_menu_item ); ?>">
                        <?php inspiry_safe_include_svg( $item['icon'], $item['path'] ); ?>
                        <span><?php echo realhomes_dashboard_menu_item_label( $key ) ?></span>
                    </a>
                    <?php
                }
            }
            ?>
        </div>
        <?php

		// End wrappers
		if ( 'dashboard' === $context ) {
            ?></div><?php
		} else {
            ?></div></div><?php
		}
	}
}

if ( ! function_exists( 'realhomes_tgm_error_page_redirect' ) ) {
	/**
	 * Redirects TGMPA Install Plugins page to Plugins page if no plugin actions are required.
	 *
	 * Prevents users from accessing an error page when there are no plugins to install, activate, or update.
	 *
	 * @since 4.3.9
	 */
	function realhomes_tgm_error_page_redirect() {
		if ( isset( $_GET['page'] ) && 'tgmpa-install-plugins' === $_GET['page'] ) {
			if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
				return;
			}

			$tgmpa_instance = TGM_Plugin_Activation::get_instance();
			$plugins        = $tgmpa_instance->plugins;
			$actions_needed = false;

			foreach ( $plugins as $plugin ) {
				if ( $tgmpa_instance->is_plugin_installed( $plugin['slug'] ) ) {
					if ( ! $tgmpa_instance->is_plugin_active( $plugin['slug'] ) || $tgmpa_instance->does_plugin_have_update( $plugin['slug'] ) ) {
						$actions_needed = true;
						break;
					}
				} else {
					$actions_needed = true;
					break;
				}
			}

			// Redirect to Plugins page if no actions are needed.
			if ( ! $actions_needed ) {
				wp_safe_redirect( admin_url( 'plugins.php' ) );
				exit;
			}
		}
	}

	add_action( 'admin_menu', 'realhomes_tgm_error_page_redirect', 999 );
}

if ( ! function_exists( 'realhomes_get_options' ) ) {
	/**
	 * This function is extension to default get_options() additionally
	 * set default values to it. It retrieves multiple options which are
	 * loaded as necessary first in order to use a single database query at most.
	 *
	 * This function accepts simple keys as well as [key, default value] pairs in single array unit
	 *
	 * @since 4.4.0
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	function realhomes_get_options( $options ) {

		// Flattening the options array for any inner array elements set for default values. Need it for options caches
		$cacheable_options = realhomes_flatten_options_array( $options );

		// Primes specific options into the cache with a single database query
		wp_prime_option_caches( $cacheable_options );

		$result = array();
		foreach ( $options as $option ) {
			if ( is_array( $option ) && ! empty( $option[0] ) && ! empty( $option[1] ) ) {
				$result[ $option[0] ] = get_option( $option[0], $option[1] );
			} else {
				$result[ $option ] = get_option( $option );
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'realhomes_flatten_options_array' ) ) {
	/**
	 * This function flattens the array by making any inner array elements
	 * as simple array keys by getting their first value as main array key element
	 *
	 * @since 4.4.0
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	function realhomes_flatten_options_array( $array ) {
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				// Replace the sub-array with the first element of the sub-array
				$array[ $key ] = $value[0];
			}
		}

		return $array;
	}
}

if ( ! function_exists( 'realhomes_send_custom_email' ) ) {
	/**
	 * Send a formatted email using the RealHomes email template system.
	 *
	 * @since 4.4.0
	 *
	 * @param array $args              {
	 *                                 Required. Email arguments.
	 *
	 * @type string $to                Recipient email address.
	 * @type string $subject           Email subject.
	 * @type string $message           Raw message content before templating.
	 * @type string $request_slug      Email template slug (e.g., 'user_registration_form').
	 * @type array  $additional_fields Optional. Template placeholders and their values.
	 * @type string $from_email        Optional. Sender email address.
	 * @type string $from_name         Optional. Sender name.
	 *                                 }
	 *
	 * @return bool True if email sent successfully, false otherwise.
	 */
	function realhomes_send_custom_email( $args ) {

		$defaults = array(
			'to'                => '',
			'subject'           => '',
			'request_slug'      => '',
			'message'           => array(),
			'additional_fields' => array(),
			'from_email'        => get_bloginfo( 'admin_email' ),
			'from_name'         => get_bloginfo( 'name' ),
		);

		$args              = wp_parse_args( $args, $defaults );
		$email_templates   = ere_get_email_templates();
		$message_body      = array();
		$additional_fields = ! empty( $args['additional_fields'] ) && is_array( $args['additional_fields'] ) && 0 < count( $args['additional_fields'] ) ? $args['additional_fields'] : false;

		if ( empty( $args['to'] ) || empty( $args['subject'] ) || empty( $args['message'] ) || empty( $args['request_slug'] ) ) {
			return false;
		}

		$message_body[] = array(
			'name'  => '',
			'value' => $args['message'],
		);

		if ( $additional_fields ) {
			foreach ( $additional_fields as $key => $field ) {
				$message_body[] = array(
					'name'  => ucwords( str_replace( '_', ' ', $key ) ),
					'value' => $field,
				);
			}
		}

		$formatted_message = ere_apply_email_template( $message_body, $args['request_slug'], $email_templates['field'], $email_templates['email'] );
		$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $args['from_name'] . ' <' . $args['from_email'] . '>', );

		return wp_mail( $args['to'], $args['subject'], $formatted_message, $headers );
	}
}

if ( ! function_exists( 'realhomes_get_current_visitor_ip' ) ) {
	/**
	 * Get the visitor's IP address.
	 *
	 * @since 4.4.1
	 *
	 * @return string IP address or fallback value.
	 */
	function realhomes_get_current_visitor_ip() {
		foreach ( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$ip_list = explode( ',', $_SERVER[ $key ] );
				return sanitize_text_field( trim( $ip_list[0] ) );
			}
		}

		return '0.0.0.0';
	}
}


if ( ! function_exists( 'realhomes_display_property_views_counter' ) ) {
	/**
	 * Displays the property views counter if enabled via theme option.
	 *
	 * @since 4.4.2
	 *
	 * @param int|null $property_id Property ID. Defaults to current post ID if not provided.
     *
     * @return null
	 */
	function realhomes_display_property_views_counter( $property_id = null ) {
		if ( 'show' === get_option( 'realhomes_listing_views_counter', 'hide' ) ) {

			if ( null === $property_id ) {
				$property_id = get_the_ID();
			}

            if ( ! function_exists( 'ere_get_properties_by_time_period' ) ) {
                return;
            }

			$current_property_views = ere_get_properties_by_time_period( [ 'post_id' => $property_id ] );
			?>
            <span class="property-views" title="<?php esc_attr_e( 'Property Views', RH_TEXT_DOMAIN ); ?>">
				<?php
				inspiry_safe_include_svg( 'views-eye.svg', '/common/images/icons/' );

				$views = 0;
				if ( isset( $current_property_views->TotalViews ) && is_numeric( $current_property_views->TotalViews ) ) {
					$views = intval( $current_property_views->TotalViews );
				}

				echo esc_html( $views );
				?>
			</span>
			<?php
		}
	}
}
