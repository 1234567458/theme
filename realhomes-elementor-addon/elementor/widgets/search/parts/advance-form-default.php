<?php
/**
 * Default template for search form advance fields
 *
 * @since      2.1.2
 */
global $settings;
global $the_widget_id;
?>
    <div class="rhea_collapsed_search_fields  rhea_advance_fields_<?php echo esc_attr( $settings['rhea_default_advance_state'] ) ?>" id="collapsed_wrapper_<?php echo esc_attr( $the_widget_id ); ?>">
        <div class="rhea_collapsed_search_fields_inner <?php echo 'yes' === $settings['hide_collapsed_labels'] ? esc_attr( 'rhea-hide-search-labels' ) : ''; ?>" id="collapsed-<?php echo esc_attr( $the_widget_id ); ?>"></div>
		<?php
		if ( 'yes' !== $settings['show_features_dropdown'] ) {
			rhea_get_template_part( 'elementor/widgets/search/fields/property-features' );
		}
		?>
    </div>
<?php
rhea_get_template_part( 'elementor/widgets/search/fields/search-button-bottom' );
