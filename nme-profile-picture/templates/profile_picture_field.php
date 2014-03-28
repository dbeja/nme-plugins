<script>
var frame;
jQuery( function($) {
	$( '#nme_choose_profile_picture' ).click( function(e) {
		e.preventDefault();
		
		// if was already opened...
		if( frame ) {
			frame.open();
			return;
		}
		
		// open wp media uploader
		var frame = wp.media({
			title: '<?php _e( 'Choose Profile Picture', 'nme-profile-picture' ); ?>',
			library: { type: 'image' },
			button: {
				text: '<?php _e( 'Choose', 'nme-profile-picture' ); ?>'
			}
		});
		
		frame.on( 'select', function() {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			$( '#nme_profile_picture_preview' ).html( '<img src="' + attachment.url + '"/>' );
			$( '#nme_profile_picture_url' ).val( attachment.url );
		});
		
		frame.open();
	});
	
	// reset profile picture
	$( '#nme_reset_profile_picture' ).click( function(e) {
		e.preventDefault();
		
		$( '#nme_profile_picture_url' ).val('');
		$( '#nme_profile_picture_preview' ).html('');
	});
});
</script>

<h3><?php _e( 'Profile Picture', 'nme-profile-picture' ); ?></h3>
<a id="nme_choose_profile_picture" class="button button-secondary" href="#"><?php _e( 'Choose Profile Picture...', 'nme-profile-picture' ); ?></a>
<a id="nme_reset_profile_picture" class="button button-secondary" href="#"><?php _e( 'Reset Profile Picture', 'nme-profile-picture' ); ?></a>
<input name="nme_profile_picture_url" id="nme_profile_picture_url" type="hidden" value="<?php echo $current_profile_picture; ?>" />
<div id="nme_profile_picture_preview">
	<?php if( !empty( $current_profile_picture ) ): ?>
		<img src="<?php echo $current_profile_picture; ?>" />
	<?php endif; ?>
</div>