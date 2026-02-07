<?php
/**
 * Property Head for single property.
 *
 * @since      2.1.0
 */
global $settings;
global $post_id
?>
<div class="rh-ultra-thumb-info-box">
    <div class="page-head-inner">
        <div class="rh-ultra-property-tags rh-property-title">
			<?php
			// Property Status
			if ( function_exists( 'ere_get_property_tags' ) ) {
				ere_get_property_tags( $post_id, 'rh-ultra-status rh-ultra-property-tag' );
				ere_get_property_tags( $post_id, 'rh-ultra-type rh-ultra-property-tag', 'property-type' );
			}

			$is_featured = get_post_meta( $post_id, 'REAL_HOMES_featured', true );
			// Property Featured Tag
			if ( $is_featured ) {
				?>
                <span class="rh_ultra_featured rh-ultra-property-tag">
                    <?php
                    if ( ! empty( $settings['ere_property_featured_label'] ) ) {
	                    echo esc_html( $settings['ere_property_featured_label'] );
                    } else {
	                    esc_html_e( 'Featured', RHEA_TEXT_DOMAIN );
                    }
                    ?>
                </span>
				<?php
			}
			// Property Label Tag
			inspiry_display_property_label( $post_id, 'rh-ultra-label rh-ultra-property-tag' );
			?>
        </div>
        <div class="rh-ultra-property-title-price">
            <h1><?php echo get_the_title($post_id); ?></h1>
			<?php
			// Property Price
			if ( function_exists( 'ere_property_price' ) ) {
				?>
                <div class="rh-ultra-property-tag-wrapper">
					<?php
					$price_classes = '';
					$amount        = floatval( get_post_meta( $post_id, 'REAL_HOMES_property_price', true ) );
					if ( empty( $amount ) || is_nan( $amount ) ) {
						$price_classes = ' price-on-call ';
					}

                    if ( realhomes_is_dual_price_enabled( $post_id ) ) {
                        $price_classes .= ' dual-price';
                    }
					?>
                    <span class="rh-ultra-price <?php echo esc_attr( $price_classes ) ?>"><?php ere_property_price( $post_id, true ); ?></span>
                </div>
				<?php
			}
			?>
        </div>
		<?php
		$address_display  = get_option( 'inspiry_display_property_address', 'true' );
		$property_address = get_post_meta( $post_id, 'REAL_HOMES_property_address', true );
		// Property Address
		if ( 'true' === $address_display && ! empty( $property_address ) ) {
			?>
            <p class="rh-ultra-property-address">
                <span class="rh-ultra-address-pin"><?php rhea_safe_include_svg( 'icons/pin-line.svg' ); ?></span>
				<?php echo esc_html( $property_address ); ?>
            </p>
			<?php
		}
		?>
    </div>
</div>