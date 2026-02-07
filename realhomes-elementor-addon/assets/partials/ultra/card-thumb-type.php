<?php
/**
 * This file contains the card thumbnail layout
 *
 * @version 2.4.0
 *
 * Partial for
 * * elementor/widgets/properties-widget/card-(1,2,3,4,5).php
 */
global $settings;
$get_post_meta_images = get_post_meta( get_the_ID(), 'REAL_HOMES_property_images', false );

if ( 'gallery' === $settings['property_cards_thumb_type'] && ! empty( $get_post_meta_images ) ) {
	?>
    <div class="rhea-properties-gallery-card-wrapper">
        <div class="rhea-properties-gallery-card">
			<?php
			foreach ( $get_post_meta_images as $image ) {
				$image_source = wp_get_attachment_image_src( $image, $settings['ere_property_grid_thumb_sizes'] );
				?>
                <div class="rhea-gallery-card-image">
                    <img data-lazy="<?php echo esc_url( $image_source[0] ) ?>" width="<?php echo esc_attr( $image_source[1] ) ?>" height="<?php echo esc_attr( $image_source[2] ) ?>" alt="<?php echo esc_attr( get_post_meta( $image, '_wp_attachment_image_alt', true ) ); ?>">
                </div>
				<?php
			}
			?>
        </div>
    </div>
	<?php
} else {
	rhea_get_template_part( 'assets/partials/ultra/thumbnail' );
}
?>