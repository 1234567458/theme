<?php
/**
 * This file contains the button type of login modal widget
 *
 * @version 2.4.0
 **/
global $settings;
if ( 'yes' == $settings['rhea_login_avatar_replace'] && ! empty( $settings['rhea_login_modal_avatar_text'] ) ) {
	?>
    <span class="rhea_login_register_text <?php echo 'button' == $settings['login_text_button_type'] ? esc_attr( 'rhea-user-login-button' ) : '' ?>"><?php echo esc_html( $settings['rhea_login_modal_avatar_text'] ); ?></span>
	<?php
} else {
	include RHEA_ASSETS_DIR . '/icons/icon-profile.svg';
}
?>

