<?php
/**
 * Header Modal
 *
 * Header modal for login in the header.
 *
 * @package realhomes_elementor_addon
 */

global $settings;
$current_user = wp_get_current_user();
?>
<div class="rhea_modal">
	<?php if ( is_user_logged_in() ) { ?>
        <div class="rhea_modal__corner"></div>
        <div class="rhea_modal__wrap">

			<?php
			// User Information Section
			if ( 'yes' == $settings['show_login_modal_avatar'] || 'yes' == $settings['show_login_modal_user_name'] ) {
				$user_avatar       = '';
				$current_user_meta = get_user_meta( $current_user->ID );
				$profile_image_id  = ! empty( $current_user_meta['profile_image_id'][0] ) ? intval( $current_user_meta['profile_image_id'][0] ) : 0;
				$has_valid_image   = $profile_image_id && wp_get_attachment_image_src( $profile_image_id, [ 40, 40 ] );

				if ( $has_valid_image ) {
					$user_avatar = wp_get_attachment_image( $profile_image_id, [ 40, 40 ], false, [ 'class' => 'rh_modal_profile_img' ] );
				} else {
					$default_avatar  = get_option( 'avatar_default', 'gravatar_default' );
					$avatar_fallback = ( $default_avatar !== 'gravatar_default' && $default_avatar !== 'blank' ) ? $default_avatar : 'gravatar_default';
					$user_avatar     = get_avatar( $current_user->user_email, 150, $avatar_fallback, $current_user->display_name, [ 'class' => 'user-icon' ] );
				}
				?>
                <div class="rhea_user">
					<?php
					if ( 'yes' == $settings['show_login_modal_avatar'] ) {
						?>
                        <div class="rhea_user__avatar">
                            <a href="<?php echo esc_url( realhomes_get_dashboard_page_url( 'profile' ) ); ?>"><?php echo $user_avatar; ?></a>
                        </div>
						<?php
					}
					?>

					<?php
					if ( 'yes' == $settings['show_login_modal_user_name'] ) {
						?>
                        <div class="rhea_user__details">
                            <p class="rhea_user__msg">
								<?php echo ! empty( $settings['rhea_login_welcome_label'] ) ? esc_html( $settings['rhea_login_welcome_label'] ) : esc_html__( 'Welcome', RHEA_TEXT_DOMAIN ); ?>
                            </p>
                            <h3 class="rhea_user__name"><a href="<?php echo esc_url( realhomes_get_dashboard_page_url( 'profile' ) ); ?>"><?php echo esc_html( $current_user->display_name ); ?></a></h3>
                        </div>
						<?php
					}
					?>
                </div>
				<?php
			}
			?>

            <!-- Dashboard Links -->
            <div class="rhea_modal__dashboard">
		        <?php
		        $dashboard_links = [
			        [
				        'key'       => 'show_login_modal_analytics',
				        'url'       => realhomes_get_dashboard_page_url( 'analytics' ),
				        'condition' => 'show' === get_option( 'realhomes_dashboard_analytics_module', 'show' ) && inspiry_is_property_analytics_enabled() && realhomes_get_current_user_role_option( 'property_analytics' ),
				        'icon'      => 'analytics-icon.svg',
				        'icon_path' => RHEA_ASSETS_DIR . '/icons/',
				        'label'     => $settings['rhea_analytics_label'] ?? esc_html__( 'Analytics', RHEA_TEXT_DOMAIN )
			        ],
			        [
				        'key'       => 'show_login_modal_properties_crm',
				        'url'       => realhomes_get_dashboard_page_url( 'properties-crm' ),
				        'condition' => 'show' === get_option( 'realhomes_dashboard_crm_module', 'show' ) && class_exists( 'Realhomes_Crm' ) && realhomes_get_current_user_role_option( 'manage_crm' ),
				        'icon'      => 'properties-crm.svg',
				        'icon_path' => RHEA_ASSETS_DIR . 'icons/',
				        'label'     => $settings['rhea_login_realhomes_crm_label'] ?? esc_html__( 'Properties CRM', RHEA_TEXT_DOMAIN )
			        ],
			        [
				        'key'       => 'show_login_modal_properties',
				        'url'       => realhomes_get_dashboard_page_url( 'properties' ),
				        'condition' => realhomes_dashboard_module_enabled( 'inspiry_properties_module_display' ),
				        'icon'      => 'icon-dash-my-properties.svg',
				        'icon_path' => RHEA_ASSETS_DIR . 'icons/',
				        'label'     => $settings['rhea_login_my_properties_label'] ?? esc_html__( 'My Properties', RHEA_TEXT_DOMAIN )
			        ],
			        [
				        'key'       => 'show_login_modal_favorites',
				        'url'       => realhomes_get_dashboard_page_url( 'favorites' ),
				        'condition' => realhomes_dashboard_module_enabled( 'inspiry_favorites_module_display' ),
				        'icon'      => 'icon-dash-favorite.svg',
				        'icon_path' => RHEA_ASSETS_DIR . 'icons/',
				        'label'     => $settings['rhea_login_favorites_label'] ?? esc_html__( 'Favorites', RHEA_TEXT_DOMAIN )
			        ],
			        [
				        'key'       => 'show_login_modal_saved_search',
				        'url'       => realhomes_get_dashboard_page_url( 'saved-searches' ),
				        'condition' => function_exists( 'inspiry_is_save_search_enabled' ) && inspiry_is_save_search_enabled(),
				        'icon'      => 'icon-dash-alert.svg',
				        'icon_path' => RHEA_ASSETS_DIR . '/icons/',
				        'label'     => get_option( 'realhomes_saved_searches_label', esc_html__( 'Saved Searches', RHEA_TEXT_DOMAIN ) )
			        ],
			        [
				        'key'       => 'show_login_modal_profile',
				        'url'       => realhomes_get_dashboard_page_url( 'profile' ),
				        'condition' => realhomes_dashboard_module_enabled( 'inspiry_profile_module_display' ),
				        'icon'      => 'icon-dash-profile.svg',
				        'icon_path' => RHEA_ASSETS_DIR . 'icons/',
				        'label'     => $settings['rhea_login_profile_label'] ?? esc_html__( 'Profile', RHEA_TEXT_DOMAIN )
			        ],
			        [
				        'key'       => 'show_login_modal_membership',
				        'url'       => realhomes_get_dashboard_page_url( 'membership' ),
				        'condition' => function_exists( 'IMS_Helper_Functions' ) && IMS_Helper_Functions()::is_memberships(),
				        'icon'      => 'icon-membership.svg',
				        'icon_path' => RHEA_ASSETS_DIR . 'icons/',
				        'label'     => $settings['rhea_login_membership_label'] ?? esc_html__( 'Membership', RHEA_TEXT_DOMAIN )
			        ]
		        ];

		        foreach ( $dashboard_links as $link ) {
			        if ( ! empty( $settings[ $link['key'] ] ) && 'yes' == $settings[ $link['key'] ] && ! empty( $link['url'] ) && $link['condition'] ) {
				        ?>
                        <a href="<?php echo esc_url( $link['url'] ); ?>" class="rhea_modal__dash_link">
					        <?php
					        if ( ! empty( $link['icon_path'] ) ) {
                                rhea_safe_include_svg( $link['icon'], $link['icon_path'] );
					        } else {
						        echo '<i class="fas ' . esc_attr( $link['icon'] ) . '"></i>';
					        }
					        ?>
                            <span><?php echo esc_html( $link['label'] ); ?></span>
                        </a>
				        <?php
			        }
		        }

		        // Additional Custom Links
		        if ( ! empty( $settings['rhea_login_add_more_repeater'] ) ) {
			        foreach ( $settings['rhea_login_add_more_repeater'] as $item ) {
				        if ( ! empty( $item['rhea_page_url']['url'] ) ) {
					        ?>
                            <a href="<?php echo esc_url( $item['rhea_page_url']['url'] ); ?>" class="rhea_modal__dash_link rhea_login_extended_link elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
						        <?php
						        if ( ! empty( $item['rhea_link_icon'] ) ) {
							        \Elementor\Icons_Manager::render_icon( $item['rhea_link_icon'], [ 'aria-hidden' => 'true' ] );
						        }
						        ?>
                                <span class="rhea_login_extended"><?php echo esc_html( $item['rhea_link_text'] ); ?></span>
                            </a>
					        <?php
				        }
			        }
		        }
		        ?>

                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="rhea_modal__dash_link">
			        <?php rhea_safe_include_svg( 'icon-dash-logout.svg', RHEA_ASSETS_DIR . '/icons/' ); ?>
                    <span class="rhea_logout_text"><?php echo $settings['rhea_log_out_label'] ?? esc_html__( 'Log Out', RHEA_TEXT_DOMAIN ); ?></span>
                </a>
            </div>

        </div>
		<?php
	}
	?>
</div>
