(function($) {

	// When the Button is clicked...
	$('.bulgroz-admin-upload').click( function() {

		// Get the Text element.
		var text = $( this ).siblings('.bulgroz-admin-input');
		var img = $( this ).siblings('img.preview');

		// Show WP Media Uploader popup
		tb_show( 'Selectionner un logo', 'media-upload.php?type=image&TB_iframe=true&post_id=0', false );

		// Callback executé à la au success de tb_show ?
		window.send_to_editor = function( html ) {

			var ID = $( 'img', html ).attr( 'data-id' );
			img.attr('src', $( 'img', html ).attr( 'src' ))

			// Send this value to the Text field.
			text.attr( 'value', ID ).trigger( 'change' );
			tb_remove(); // Then close the popup window
		}

		return false;

	});

})(jQuery);