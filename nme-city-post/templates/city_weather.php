<div class="nme_city_weather">
<?php if( !empty( $city ) ): ?>
	<?php echo $city->name; ?> (<?php echo !empty( $temperature ) ? $temperature : __('--'); ?>º<?php echo strtoupper($units); ?>)
<?php endif; ?>
</div>