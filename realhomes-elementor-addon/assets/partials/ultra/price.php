<?php
$property_id = get_the_ID();
?>
<div class="rh_prop_card__priceLabel_ultra">
	<p class="rh_prop_card__price_ultra <?php echo realhomes_is_dual_price_enabled( $property_id ) ? 'dual-price' : ''; ?>">
		<?php
		if (function_exists('ere_property_price')) {
			ere_property_price( $property_id, true, true );
		}
		?>
	</p>
</div>