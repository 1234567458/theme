<?php
global $settings, $rhea_add_meta_select;

$property_id          = get_the_ID();
$property_meta_keys   = rhea_get_property_meta_keys_list();
$rhea_add_meta_select = ! empty( $settings['rhea_add_meta_select'] ) ? $settings['rhea_add_meta_select'] : array();

if ( ! empty( $rhea_add_meta_select ) ) {
	$meta_layout = empty( $settings['meta_layout'] ) ? '1' : $settings['meta_layout'];
	?>
    <div class="rh_prop_card_meta_wrap_ultra rh-ul-tooltip">
		<?php
		foreach ( $rhea_add_meta_select as $i => $meta ) {
			$meta_display = $meta['rhea_property_meta_display'] ?? null;

			if ( $meta_display && ! empty( $property_meta_keys[ $meta_display ] ) ) {
				$meta_key_data = $property_meta_keys[ $meta_display ];
				$meta_postfix  = $property_meta_keys[ $meta_display ]['postfix'] ?? '';

				rhea_ultra_meta(
					$meta['rhea_meta_repeater_label'],
					$meta_key_data['key'],
					$meta_key_data['icon'],
					$meta_postfix,
					$i + 1,
					$meta_layout
				);
			}
		}

		// Display additional fields icons
		do_action( 'rhea_property_listing_additional_fields_icons', $property_id, array( 'layout' => $meta_layout ) );
		?>
    </div>
	<?php
}