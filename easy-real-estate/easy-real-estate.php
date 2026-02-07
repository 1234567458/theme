<?php
/**
 * Plugin Name: Easy Real Estate
 * Plugin URI: http://themeforest.net/item/real-homes-wordpress-real-estate-theme/5373914
 * Description: Provides real estate core functionality for the RealHomes theme.
 * Version: 2.3.5
 * Tested up to: 6.8.3
 * Requires at least: 6.0
 * Requires PHP: 8.3
 * Author: InspiryThemes
 * Author URI: https://themeforest.net/user/inspirythemes/portfolio?order_by=sales
 * Text Domain: easy-real-estate
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Currently plugin Text Domain.
 */
define( 'ERE_TEXT_DOMAIN', 'easy-real-estate' );

if ( ! class_exists( 'Easy_Real_Estate' ) ) :

	final class Easy_Real_Estate {

		/**
		 * Plugin's current version
		 *
		 * @var string
		 */
		public $version;

		/**
		 * Plugin Name
		 *
		 * @var string
		 */
		public $plugin_name;

		/**
		 * Plugin's singleton instance.
		 *
		 * @var Easy_Real_Estate
		 */
		protected static $_instance;

		/**
		 * Constructor function.
		 */
		public function __construct() {

			$this->plugin_name = 'easy-real-estate';

			$this->version = $this->get_plugin_version();

			$this->define_constants();

			$this->initialize_custom_post_types();

			$this->includes();

			$this->initialize_meta_boxes();

			$this->initialize_admin_menu();

			$this->init_hooks();

			add_action( 'admin_notices', array( $this, 'google_map_api_notice' ) );

			do_action( 'ere_loaded' );  // Easy Real Estate plugin loaded action hook.
		}

		/**
		 * Provides singleton instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Defines constants.
		 */
		protected function define_constants() {

			if ( ! defined( 'ERE_VERSION' ) ) {
				define( 'ERE_VERSION', $this->version );
			}

			// Full path and filename.
			if ( ! defined( 'ERE_PLUGIN_FILE' ) ) {
				define( 'ERE_PLUGIN_FILE', __FILE__ );
			}

			// Plugin directory path.
			if ( ! defined( 'ERE_PLUGIN_DIR' ) ) {
				define( 'ERE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Hook to add RHPE directory path
			do_action( 'rhpe_dir_const' );


			// Plugin directory URL.
			if ( ! defined( 'ERE_PLUGIN_URL' ) ) {
				define( 'ERE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}


			// Plugin file path relative to plugins directory.
			if ( ! defined( 'ERE_PLUGIN_BASENAME' ) ) {
				define( 'ERE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			}

			// Default Design Variation
			if ( ! defined( 'REALHOMES_DESIGN_VARIATION' ) ) {

				// Define the latest default design variation of Realhomes theme.
				define( 'REALHOMES_DESIGN_VARIATION', 'ultra' );

				/**
				 * Verify whether the user has saved their preference for the Classic, Modern or Ultra design; if not,
				 * set the latest design as the default.
				 */
				if ( ! in_array( get_option( 'inspiry_design_variation' ), array( 'classic', 'modern', 'ultra' ) ) ) {
					update_option( 'inspiry_design_variation', REALHOMES_DESIGN_VARIATION );
				}
			}

			// RealHomes selected design variation.
			if ( ! defined( 'INSPIRY_DESIGN_VARIATION' ) ) {
				define( 'INSPIRY_DESIGN_VARIATION', get_option( 'inspiry_design_variation', REALHOMES_DESIGN_VARIATION ) );
			}

			// Adding action links to admin plugins list page
			add_filter( 'plugin_action_links_' . ERE_PLUGIN_BASENAME, [ $this, 'plugin_action_links' ] );

		}

		/**
		 * Includes files required on admin and on frontend.
		 */
		public function includes() {
			$this->include_functions();
			$this->include_shortcodes();
			$this->include_widgets();
			$this->include_social_login();
		}

		/**
		 * Get plugin version safely
		 *
		 * @since 2.2.6
		 *
		 * @param string $key Key to fetch plugin detail
		 *
		 * @return string|mixed
		 */
		public function get_plugin_version() {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			// Prevent early translation call by setting $translate to false.
			$plugin_data = get_plugin_data( __FILE__, false, false );

			return $plugin_data['Version'];
		}


        /* Displays a warning notice on the admin screen if the Google Maps API key is missing.
		 *
		 * This function checks whether the selected map type is Google Maps and whether a valid API key is provided.
		 * If the API key is missing, a dismissible warning notice is displayed to the admin, prompting them to provide a valid key.
		 *
		 * @since 2.3.0
         *
		 * @return void
		 */
		public function google_map_api_notice() {
            
			// Check if the selected map type is 'googlemaps' and if the Google Maps API key is missing
			if ( 'googlemaps' === get_option( 'ere_theme_map_type' ) && empty( get_option( 'inspiry_google_maps_api_key' ) ) ) {

				// Display an admin warning notice about the missing API key
				?>
                <div class="notice notice-warning is-dismissible">
                    <p>
						<?php
						printf(
							esc_html__( '%1$sGoogle Maps API Key%2$s is missing in %1$sEasy Real Estate > Maps > Google Maps%2$s. Please provide a valid key to ensure Google Maps load correctly on the site.', ERE_TEXT_DOMAIN ),
							'<strong>',
							'</strong>'
						);
						?>
                    </p>
                </div>
				<?php
			}
		}

		/**
		 * Includes social login feature related files.
		 */
		public function include_social_login() {
			require_once ERE_PLUGIN_DIR . 'includes/social-login/autoload.php';  // Social login feature.
		}

		/**
		 * Functions
		 */
		public function include_functions() {

			require_once ERE_PLUGIN_DIR . 'includes/functions/data.php';  // data functions.
			require_once ERE_PLUGIN_DIR . 'includes/functions/basic.php';  // basic functions.
			require_once ERE_PLUGIN_DIR . 'includes/functions/price.php';   // price functions.
			require_once ERE_PLUGIN_DIR . 'includes/functions/real-estate.php';   // real estate functions.
            require_once ERE_PLUGIN_DIR . 'includes/functions/agents.php';   // agents functions.
            require_once ERE_PLUGIN_DIR . 'includes/functions/agencies.php';   // agencies functions.
            require_once ERE_PLUGIN_DIR . 'includes/functions/gdpr.php';   // gdpr functions.
			require_once ERE_PLUGIN_DIR . 'includes/functions/google-recaptcha.php';   // google recaptcha functions.
			require_once ERE_PLUGIN_DIR . 'includes/functions/form-handlers.php';   // form handlers.
			require_once ERE_PLUGIN_DIR . 'includes/functions/settings-request-handler.php'; // Settings handlers
			require_once ERE_PLUGIN_DIR . 'includes/functions/members.php';   // members functions.
			require_once ERE_PLUGIN_DIR . 'includes/functions/property-submit.php';   // members functions.
			require_once ERE_PLUGIN_DIR . 'includes/functions/agent-agency-submit.php';

			// Require property analytics feature related files if it's enabled.
			if ( inspiry_is_property_analytics_enabled() ) {
				require_once ERE_PLUGIN_DIR . 'includes/property-analytics/class-property-analytics.php';   // property analytics model.
				require_once ERE_PLUGIN_DIR . 'includes/property-analytics/class-property-analytics-view.php';   // property analytics view.
				require_once ERE_PLUGIN_DIR . 'includes/property-analytics/analytics-functions.php';   // property analytics view.
			}

			// Subscription API and Plugin Update functions.
			require_once ERE_PLUGIN_DIR . 'includes/functions/purchase-api.php';
			require_once ERE_PLUGIN_DIR . 'includes/functions/subscription-api.php';
			if ( ERE_Subscription_API::status() ) {
				require_once ERE_PLUGIN_DIR . 'includes/functions/plugin-update.php';   // plugin update functions.
			}

		}

		/**
		 * Shortcodes
		 */
		public function include_shortcodes() {
			include_once ERE_PLUGIN_DIR . 'includes/shortcodes/columns.php';
			include_once ERE_PLUGIN_DIR . 'includes/shortcodes/elements.php';
			include_once ERE_PLUGIN_DIR . 'includes/shortcodes/vc-map.php';
		}

		/**
		 * Widgets
		 */
		public function include_widgets() {

            include_once ERE_PLUGIN_DIR . 'includes/widgets/agent-properties-widget.php';
            include_once ERE_PLUGIN_DIR . 'includes/widgets/agents-list-widget.php';
            include_once ERE_PLUGIN_DIR . 'includes/widgets/agent-featured-properties-widget.php';
			include_once ERE_PLUGIN_DIR . 'includes/widgets/featured-properties-widget.php';
			include_once ERE_PLUGIN_DIR . 'includes/widgets/properties-widget.php';
			include_once ERE_PLUGIN_DIR . 'includes/widgets/property-types-widget.php';
			include_once ERE_PLUGIN_DIR . 'includes/widgets/property-taxonomy-terms-widget.php';
			include_once ERE_PLUGIN_DIR . 'includes/widgets/advance-search-widget.php';
			include_once ERE_PLUGIN_DIR . 'includes/widgets/contact-form-widget.php';
			include_once ERE_PLUGIN_DIR . 'includes/widgets/owner-widget.php';
			include_once ERE_PLUGIN_DIR . 'includes/widgets/owners-widget.php';

            if ( 'ultra' === INSPIRY_DESIGN_VARIATION ) {
				include_once ERE_PLUGIN_DIR . 'includes/widgets/ultra-mortgage-calculator-widget.php';
			} else {
				include_once ERE_PLUGIN_DIR . 'includes/widgets/mortgage-calculator-widget.php';
			}

            include_once ERE_PLUGIN_DIR . 'includes/widgets/rh-contact-information-widget.php';

            if ( 'classic' !== INSPIRY_DESIGN_VARIATION ) {
				include_once ERE_PLUGIN_DIR . 'includes/widgets/property-filters.php';
			}
		}

		/**
		 * Admin menu.
		 */
		public function initialize_admin_menu() {
			require_once ERE_PLUGIN_DIR . 'includes/admin-menu/class-ere-admin-menu.php';
		}

		/**
		 * Custom Post Types
		 */
		public function initialize_custom_post_types() {

            // Always include property post type as it is necessary one
			include_once ERE_PLUGIN_DIR . 'includes/custom-post-types/property.php';

			$post_types = [
				'ere_agent_post_type_status'     => 'agent.php',
				'ere_agency_post_type_status'    => 'agency.php',
				'ere_owner_post_type_status'     => 'owner.php',
				'ere_partner_post_type_status'   => 'partners.php',
				'ere_slides_post_type_status'    => 'slide.php',
			];

			foreach ( $post_types as $option => $file ) {
				if ( 'enable' === get_option( $option, 'enable' ) ) {
					include_once ERE_PLUGIN_DIR . "includes/custom-post-types/{$file}";
				}
			}
		}

		/**
		 * Meta boxes
		 */
		public function initialize_meta_boxes() {
			include_once ERE_PLUGIN_DIR . 'includes/mb/class-ere-meta-boxes.php';
			include_once ERE_PLUGIN_DIR . 'includes/mb/property-additional-fields.php';
		}

		/**
		 * Initialize hooks.
		 */
		public function init_hooks() {
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );  // plugin's admin styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) ); // plugin's admin scrips.
			add_action( 'realhomes_dashboard_analytics_after', array( $this, 'enqueue_dashboard_analytics_scripts' ) ); // plugin's admin scrips.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) ); // plugin's scripts.
		}

		/**
		 * Load text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( ERE_TEXT_DOMAIN, false, dirname( ERE_PLUGIN_BASENAME ) . '/languages' );
		}

		/**
		 * Enqueue admin styles
		 */
		public function enqueue_admin_styles() {
			wp_enqueue_style( 'easy-real-estate-admin', ERE_PLUGIN_URL . 'css/ere-admin.css', array(), $this->version, 'all' );
		}

		/**
		 * Enqueue Admin JavaScript
		 */
		public function enqueue_admin_scripts() {
			wp_enqueue_script(
				'easy-real-estate-admin',
				ERE_PLUGIN_URL . 'js/ere-admin.js',
				array(
					'jquery',
					'jquery-ui-sortable',
				),
				$this->version
			);

			$ere_social_links_strings = array(
				'title'       => esc_html__( 'Title', ERE_TEXT_DOMAIN ),
				'profileURL'  => esc_html__( 'Profile URL', ERE_TEXT_DOMAIN ),
				'iconClass'   => esc_html__( 'Icon Class', ERE_TEXT_DOMAIN ),
				'iconExample' => esc_html__( 'Example: fas fa-flicker', ERE_TEXT_DOMAIN ),
				'iconLink'    => esc_html__( 'Get icon!', ERE_TEXT_DOMAIN ),
			);
			wp_localize_script( 'easy-real-estate-admin', 'ereSocialLinksL10n', $ere_social_links_strings );

			$ere_price_number_format_Data = array(
				'local' => get_option( 'ere_price_number_format_language', 'en-US' ),
			);
			wp_localize_script( 'easy-real-estate-admin', 'erePriceNumberFormatData', $ere_price_number_format_Data );
		}

		/**
		 * Enqueue JavaScript
		 */
		public function enqueue_scripts() {

			// ERE frontend script.
			wp_register_script( 'jquery-validate', ERE_PLUGIN_URL . 'js/jquery.validate.min.js', array(
				'jquery',
				'jquery-form'
			), $this->version, true );
			wp_register_script( 'ere-frontend', ERE_PLUGIN_URL . 'js/ere-frontend.js', array( 'jquery-validate' ), $this->version, true );
			wp_localize_script( 'ere-frontend', 'ere_social_login_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_script( 'ere-frontend' );

			// Chart.js library for the graph.
			$should_enqueue_chart = false;
			wp_register_script( 'chart.js', ERE_PLUGIN_URL . 'includes/property-analytics/js/chart.min.js', array( 'jquery' ), $this->version, true );

			if ( is_singular( 'property' ) && inspiry_is_property_analytics_enabled() ) {
				$should_enqueue_chart = true;

				// Custom script to handle Property Views Ajax request and Graph display.
				wp_register_script( 'ere-property-analytics', ERE_PLUGIN_URL . 'includes/property-analytics/js/property-analytics.js', array( 'jquery' ), $this->version, true );

				$border_color     = '';
				$background_color = '';
				if ( 'classic' === INSPIRY_DESIGN_VARIATION ) {
					$border_color = '#ec894d';
				} else {
					$realhomes_color_scheme = get_option( 'realhomes_color_scheme', 'default' );
					$primary                = '';
					$light                  = '';

					if ( 'custom' === $realhomes_color_scheme ) {
						$primary = get_option( 'theme_core_mod_color_green' );
						$light   = get_option( 'realhomes_color_primary_light' );

					} else {
						$get_color_scheme = function_exists( 'realhomes_get_color_scheme' ) ? realhomes_get_color_scheme( $realhomes_color_scheme ) : array();
						if ( ! empty( $get_color_scheme['primary'] ) ) {
							$primary = $get_color_scheme['primary'];
						}
						if ( ! empty( $get_color_scheme['primary_light'] ) ) {
							$light = $get_color_scheme['primary_light'];
						}
					}

					if ( 'classic' !== INSPIRY_DESIGN_VARIATION ) {
						$border_color = $primary;
					}

					if ( 'ultra' === INSPIRY_DESIGN_VARIATION ) {
						$background_color = $light;
					}
				}

				$current_user_id = is_user_logged_in() ? get_current_user_id() : 0;

				// Localizing property views ajax request data information.
				wp_localize_script(
					'ere-property-analytics',
					'property_analytics',
					array(
						'is_user_logged_in' => is_user_logged_in(),
						'current_user_id'   => $current_user_id,
						'ajax_url'          => admin_url( 'admin-ajax.php' ),
						'ajax_nonce'        => wp_create_nonce( 'ere-property-analytics' ),
						'property_id'       => get_the_ID(),
						'border_color'      => $border_color,
						'background_color'  => $background_color,
						'data_label'        => esc_html__( 'Property Views', ERE_TEXT_DOMAIN ),
						'chart_type'        => get_option( 'inspiry_property_analytics_chart_type', 'line' ),
					)
				);

				wp_enqueue_script( 'ere-property-analytics' );
			}

			if (
				( is_singular( 'agent' ) && 'show' == get_option( 'realhomes_agent_single_stats_charts', 'show' ) )
				|| ( is_singular( 'agency' ) && 'show' == get_option( 'realhomes_agency_single_stats_charts', 'show' ) )
			) {
				$should_enqueue_chart = true;
			}
            
			// Enqueue Chart.js if needed
			if ( $should_enqueue_chart ) {
				wp_enqueue_script( 'chart.js' );
			}
		}

        public function enqueue_dashboard_analytics_scripts() {
	        wp_enqueue_script( 'chart.js' );

	        // Localize strings for use in JS
	        wp_localize_script( 'chart.js', 'dashboardAnalyticsStrings', array(
		        'viewsString' => esc_html__( 'Views', ERE_TEXT_DOMAIN )
	        ));
        }

		/**
		 * Tabs
		 */
		public function tabs() {

			$tabs = array(
				'price'              => esc_html__( 'Price Format', ERE_TEXT_DOMAIN ),
				'post_types'         => esc_html__( 'Post Types', ERE_TEXT_DOMAIN ),
				'map'                => esc_html__( 'Maps', ERE_TEXT_DOMAIN ),
				'captcha'            => esc_html__( 'reCAPTCHA', ERE_TEXT_DOMAIN ),
				'social'             => esc_html__( 'Social', ERE_TEXT_DOMAIN ),
				'gdpr'               => esc_html__( 'GDPR', ERE_TEXT_DOMAIN ),
				'property'           => esc_html__( 'Property', ERE_TEXT_DOMAIN ),
				'users'              => esc_html__( 'Users', ERE_TEXT_DOMAIN ),
				'property-analytics' => esc_html__( 'Property Analytics', ERE_TEXT_DOMAIN ),
				'webhooks'           => esc_html__( 'Webhooks', ERE_TEXT_DOMAIN ),
			);

			// Filter to add the New Settings tabs
			$tabs = apply_filters( 'ere_settings_tabs', $tabs );

			return $tabs;
		}

		/**
		 * Generates tabs navigation
		 */
		public function tabs_nav( $current_tab ) {

			$tabs = $this->tabs();
			?>
            <div class="nav-tab-wrapper">
				<?php
				if ( ! empty( $tabs ) && is_array( $tabs ) ) {
					foreach ( $tabs as $slug => $title ) {
						if ( file_exists( ERE_PLUGIN_DIR . 'includes/settings/' . $slug . '.php' ) ) {
							$active_tab_class = ( $current_tab === $slug ) ? 'nav-tab-active' : '';
							$admin_url        = ( $current_tab === $slug ) ? '#' : admin_url( 'admin.php?page=ere-settings&tab=' . $slug );
							echo '<a class="nav-tab ' . $active_tab_class . '" href="' . esc_url_raw( $admin_url ) . '" data-tab="' . esc_attr( $slug ) . '">' . esc_html( $title ) . '</a>';
						}
					}
				}
				?>
            </div>
			<?php
		}

		/**
		 * Settings page callback
		 */
		public function settings_page() {
			require_once ERE_PLUGIN_DIR . 'includes/settings/settings.php';
		}

		/**
		 * Retrieves an option value based on an option name.
		 *
		 * @param string $option_name
		 * @param bool   $default
		 * @param string $type
		 *
		 * @return mixed|string
		 */
		public function get_option( $option_name, $default = false, $type = 'text' ) {

			if ( isset( $_POST[ $option_name ] ) ) {
				$value = $_POST[ $option_name ];

				switch ( $type ) {
					case 'textarea':
						$value = wp_kses( $value, array(
							'a'      => array(
								'class'  => array(),
								'href'   => array(),
								'target' => array(),
								'title'  => array(),
							),
							'br'     => array(),
							'em'     => array(),
							'strong' => array(),
						) );
						break;

					case 'array':
						$value = rest_sanitize_array( $value );
						break;

					default:
						$value = sanitize_text_field( $value );
				}

				return $value;
			}

			return get_option( $option_name, $default );
		}

		/**
		 * Sanitize additional social networks array.
		 */
		public function sanitize_social_networks( $social_networks ) {

			// Initialize the new array that will hold the sanitize values.
			$sanitized_social_networks = array();

			foreach ( $social_networks as $index => $social_network ) {
				foreach ( $social_network as $key => $value ) {
					$sanitized_social_networks[ $index ][ $key ] = sanitize_text_field( $value );
				}
			}

			return $sanitized_social_networks;
		}

		/**
		 * Add notice when settings are saved.
		 */
		public function notice() {
			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'inspiry_ere_settings' ) ) {
				?>
                <div id="setting-error-ere_settings_updated" class="updated notice is-dismissible">
                    <p><strong><?php esc_html_e( 'Settings saved successfully!', ERE_TEXT_DOMAIN ); ?></strong></p>
                </div>
				<?php
			}
		}

		/**
		 * Cloning is forbidden.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden!', ERE_TEXT_DOMAIN ), ERE_VERSION );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing is forbidden!', ERE_TEXT_DOMAIN ), ERE_VERSION );
		}

		/**
		 * Plugin action links.
		 *
		 * Adds action links to the plugin list table
		 *
		 * Fired by `plugin_action_links` filter.
		 *
		 * @since 2.2.0
		 *
		 * @param array $links An array of plugin action links.
		 *
		 * @return array An array of plugin action links.
		 */
		public function plugin_action_links( $links ) {
			$settings_link      = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=ere-settings' ), esc_html__( 'Settings', ERE_TEXT_DOMAIN ) );
			$documentation_link = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://realhomes.io/documentation/price-format-settings/', esc_html__( 'Documentation', ERE_TEXT_DOMAIN ) );

			array_unshift( $links, $settings_link, $documentation_link );

			return $links;
		}

	}

endif; // End if class_exists check.


// run on ERE activation
function ere_plugin_activated() {
	add_option( 'ere_plugin_activated', true );
}

register_activation_hook( __FILE__, 'ere_plugin_activated' );

/**
 * Check plugins conflict with ERE
 */
function ere_check_plugins_conflict() {

	// List of conflicted plugins.
	$conflicted_plugins = array(
		'easy-property-listings/easy-property-listings.php',
		'essential-real-estate/essential-real-estate.php',
		'real-estate-listing-realtyna-wpl/WPL.php',
		'wp-listings/plugin.php',
		'wp-property/wp-property.php',
	);

	// Get all installed plugins.
	$all_plugins = get_plugins();
	if ( ! empty( $all_plugins ) ) {

		// Current collection of conflicted plugins.
		$current_conflicted_plugins = array();

		foreach ( $all_plugins as $file => $plugin ) {
			if ( in_array( $file, $conflicted_plugins ) ) {
				$current_conflicted_plugins[] = $plugin['Name'];
			}
		}

		if ( ! empty( $current_conflicted_plugins ) ) :
			?>
            <div class="notice notice-warning is-dismissible">
                <p>
					<?php
					printf(
						esc_html__( '%sEasy Real Estate%s detected the following plugins that may create conflicts with RealHomes Theme functionality. Please delete these plugins to run your site smoothly.', ERE_TEXT_DOMAIN ),
						'<strong>', '</strong>' );
					?>
                </p>
                <pre><?php
					foreach ( $current_conflicted_plugins as $conflicted_plugin ) {
						echo '- ' . esc_html( $conflicted_plugin ) . '<br />';
					}
					?></pre>
            </div>
		<?php
		endif;
	}
}

// hook plugins conflict check function to admin_notices
function ere_notify_conflicts() {
	if ( is_admin() && get_option( 'ere_plugin_activated', false ) ) {
		add_action( 'admin_notices', 'ere_check_plugins_conflict' );
		delete_option( 'ere_plugin_activated' );
	}
}

add_action( 'admin_init', 'ere_notify_conflicts' );

/**
 * Main instance of Easy_Real_Estate.
 *
 * Returns the main instance of Easy_Real_Estate to prevent the need to use globals.
 *
 * @return Easy_Real_Estate
 */
function ERR() {
	return Easy_Real_Estate::instance();
}

// Get ERR Running.
ERR();
