<?php
/**
 * Plugin Name: RealHomes Elementor Addon
 * Plugin URI: http://themeforest.net/item/real-homes-wordpress-real-estate-theme/5373914
 * Description: Provides RealHomes based Elementor widgets.
 * Author: InspiryThemes
 * Author URI: https://inspirythemes.com
 * Text Domain: realhomes-elementor-addon
 * Domain Path: /languages
 * Version: 2.4.5
 * Tested up to: 6.8.3
 * Requires at least: 6.0
 * Requires PHP: 8.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Currently plugin Text Domain.
 */
define( 'RHEA_TEXT_DOMAIN', 'realhomes-elementor-addon' );

if ( ! class_exists( 'RealHomes_Elementor_Addon' ) ) {
	final class RealHomes_Elementor_Addon {
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
		 * @var RealHomes_Elementor_Addon
		 */
		protected static $_instance;

		/**
		 * Admin notice message.
		 *
		 * @var string
		 */
		public string $message;

		public function __construct( $message ) {
			$this->plugin_name = $this->get_plugin_details( 'Name' );
			$this->version     = $this->get_plugin_details( 'Version' );

			// Show notice and stop further execution of the plugin.
			if ( ! empty( $message ) ) {
				$this->message = $message;

				add_action( 'admin_notices', array( $this, 'realhomes_activation_notice' ) );

				return;
			}

			// RealHomes Elementor Addon Depends on Easy Real Estate plugin
			if ( class_exists( 'Easy_Real_Estate' ) ) {
				$this->disable_textdomain_autoregistration();
				$this->define_constants();
				$this->includes();
				$this->init_hooks();
				do_action( 'realhomes_elementor_addon_loaded' );
			}

			register_activation_hook( __FILE__, array( $this, 'disable_global_schemes' ) );
		}

		/**
		 * Displays theme activation notice on the admin screen.
		 *
		 * @return void
		 */
		public function realhomes_activation_notice() {
			printf( '<div class="notice notice-warning is-dismissible"><p><strong>%s</strong> %s</p></div>', $this->plugin_name, esc_html( $this->message ) );
		}

		public function disable_global_schemes() {
			/**
			 * Disable global schemes on plugin activation.
			 */
			update_option( 'elementor_disable_typography_schemes', 'yes' );
			update_option( 'elementor_disable_color_schemes', 'yes' );
			$get_elementor_container_width = get_option( 'elementor_container_width' );
			if ( empty( $get_elementor_container_width ) ) {
				update_option( 'elementor_container_width', 1240 );
			}
		}

		public static function instance( $message ) {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self( $message );
			}

			return self::$_instance;
		}

		protected function define_constants() {
			if ( ! defined( 'RHEA_VERSION' ) ) {
				define( 'RHEA_VERSION', $this->version );
			}

			// Full path and filename
			if ( ! defined( 'RHEA_PLUGIN_FILE' ) ) {
				define( 'RHEA_PLUGIN_FILE', __FILE__ );
			}

			// Plugin directory path
			if ( ! defined( 'RHEA_PLUGIN_DIR' ) ) {
				define( 'RHEA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin directory URL
			if ( ! defined( 'RHEA_PLUGIN_URL' ) ) {
				define( 'RHEA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin file path relative to plugins directory
			if ( ! defined( 'RHEA_PLUGIN_BASENAME' ) ) {
				define( 'RHEA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			}

			// Plugin Assets Director
			if ( ! defined( 'RHEA_ASSETS_DIR' ) ) {
				define( 'RHEA_ASSETS_DIR', RHEA_PLUGIN_DIR . 'assets/' );
			}
		}

		public function includes() {
			$this->include_functions();
		}

		/**
		 * Functions
		 */
		public function include_functions() {
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/basic.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/rhea-search.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/inquiry-form-handler.php' );

			include_once( RHEA_PLUGIN_DIR . 'includes/traits/search-form.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/traits/rhea-properties-partials.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/traits/rhea-properties-action-buttons.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/traits/rhea-single-property-sections-title-v2.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/traits/rhea-gdpr-controls.php' );

			include_once( RHEA_PLUGIN_DIR . 'includes/functions/class-rhea-elementor-color-scheme.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/class-rhea-elementor-header-footer.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/class-rhea-elementor-search-form.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/class-rhea-elementor-single-property.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/class-rhea-elementor-single-agent.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/class-rhea-elementor-single-agency.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/class-rhea-elementor-archive.php' );
			include_once( RHEA_PLUGIN_DIR . 'includes/functions/class-rhea-generate-icon.php' );

			if ( 'classic' !== INSPIRY_DESIGN_VARIATION && 'enable' === get_option( 'realhomes_mega_menu', 'disable' ) ) {
				include_once( RHEA_PLUGIN_DIR . 'includes/functions/class-rhea-elementor-mega-menu.php' );
			}

			if ( class_exists( 'ERE_Subscription_API' ) && ERE_Subscription_API::status() ) {
				include_once( RHEA_PLUGIN_DIR . 'includes/functions/plugin-update.php' );   // plugin update functions
			}
		}

		/**
		 * Initialize hooks.
		 */
		public function init_hooks() {
			add_action( 'init', array( $this, 'init' ) );

			// initialize elementor widgets
			add_action( 'plugins_loaded', [ $this, 'initialize_elementor_stuff' ] );

			// Adding action links to admin plugins list page
			add_filter( 'plugin_action_links_' . RHEA_PLUGIN_BASENAME, [ $this, 'plugin_action_links' ] );
		}

		/**
		 * Initialize plugin.
		 */
		public function init() {
			do_action( 'before_rhea_init' );    // before init action

			$this->load_plugin_textdomain();    // load text domain for translation.

			do_action( 'rhea_init' );   // done with init
		}

		/**
		 * Disable text-domain autoload for the plugin.
		 *
		 * @since 2.4.3
		 */
		private function disable_textdomain_autoregistration() {
			if ( isset( $GLOBALS['wp_textdomain_registry'] ) ) {
				try {
					$registry   = $GLOBALS['wp_textdomain_registry'];
					$reflection = new ReflectionClass( $registry );
					if ( $reflection->hasProperty( 'custom_paths' ) ) {
						$prop = $reflection->getProperty( 'custom_paths' );
						$prop->setAccessible( true );
						$custom_paths = $prop->getValue( $registry );
						if ( isset( $custom_paths[ RHEA_TEXT_DOMAIN ] ) ) {
							unset( $custom_paths[ RHEA_TEXT_DOMAIN ] );
							$prop->setValue( $registry, $custom_paths );
						}
						$prop->setAccessible( false );
					}
				} catch ( Exception $e ) {
					// Optional: log or fail silently
					error_log( 'Text-domain registry reflection error: ' . $e->getMessage() );
				}
			}
		}

		/**
		 * Get plugin details safely
		 *
		 * @since 2.3.6
		 *
		 * @param string $key Key to fetch plugin detail
		 *
		 * @return string|mixed
		 */
		public function get_plugin_details( $key = 'Version' ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			// Prevent early translation call by setting $translate to false.
			$plugin_data = get_plugin_data( __FILE__, false, false );

			return $plugin_data[ $key ];
		}

		/**
		 * Load text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( RHEA_TEXT_DOMAIN, false, dirname( RHEA_PLUGIN_BASENAME ) . '/languages' );
		}

		/**
		 * Initialize elementor stuff
		 */
		public function initialize_elementor_stuff() {
			include_once( RHEA_PLUGIN_DIR . 'elementor/class-rhea-elementor-extension.php' );
		}

		/**
		 * Cloning is forbidden.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden!', RHEA_TEXT_DOMAIN ), RHEA_VERSION );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing is forbidden!', RHEA_TEXT_DOMAIN ), RHEA_VERSION );
		}

		/**
		 * Plugin action links.
		 *
		 * Adds action links to the plugin list table
		 *
		 * Fired by `plugin_action_links` filter.
		 *
		 * @since 2.2.2
		 *
		 * @param array $links An array of plugin action links.
		 *
		 * @return array An array of plugin action links.
		 */
		public function plugin_action_links( $links ) {
			$documentation_link = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://realhomes.io/documentation/realhomes-elementor-widgets/', esc_html__( 'Documentation', RHEA_TEXT_DOMAIN ) );

			array_unshift( $links, $documentation_link );

			return $links;
		}

	}
}

/**
 * Returns the main instance of RealHomes_Elementor_Addon to prevent the need to use globals.
 *
 * @return RealHomes_Elementor_Addon
 */
function rhea_init() {
	$notice               = '';
	$theme_name           = 'RealHomes';
	$theme                = wp_get_theme();
	$current_active_theme = $theme->get( 'Name' );

	if ( ! empty( $theme->get_template() ) && 'realhomes' === $theme->get_template() ) {
		$current_active_theme = $theme_name;
	}

	// Check if the activated theme is not RealHomes or RealHomes Child.
	if ( $current_active_theme !== $theme_name ) {
		$notice = esc_html__( 'plugin requires the RealHomes theme to be activated in order to function properly. Please activate the RealHomes theme to ensure optimal performance and avoid any inconsistencies.', RHEA_TEXT_DOMAIN );
	}

	return RealHomes_Elementor_Addon::instance( $notice );
}

// Get RealHomes Elementor Addon Running.
rhea_init();