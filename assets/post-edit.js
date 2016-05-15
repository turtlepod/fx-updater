jQuery(document).ready(function($){

	/* the vars */
	var file_frame;

	/* Click the upload button */
	$( document.body ).on( 'click', '.upload-zip', function(e){
		e.preventDefault();

		/* Open the frame if already loaded. */
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		var this_button = $( this );

		/* If media frame doesn't exist, create it with some options. */
		file_frame = wp.media.frames.file_frame = wp.media({
			className: 'media-frame fx-media-frame',
			frame: 'select',
			title: fx_upmb_upload.title,
			library: { type: 'application/zip' },
			button: { text:  fx_upmb_upload.button },
			multiple: false,
		});

		/* insert */
		file_frame.on( 'select', function(){

			/* Insert */
			var this_attachment = file_frame.state().get('selection').first().toJSON();
			this_button.parents( '.fx-upmb-upload' ).find( '.fx-upmb-upload-url' ).val( this_attachment.url );

			/* Enable remove button */
			this_button.siblings( '.remove-zip' ).removeClass( 'disabled' );

		});

		// Now that everything has been set, let's open up the frame.
		file_frame.open();
	});

	/* === Disabled Button === */
	$( '.remove-zip' ).each( function(i){
		var url_input = $( this ).parents( '.fx-upmb-upload' ).find( '.fx-upmb-upload-url' ).val();
		if( '' == url_input ){
			$( this ).addClass( 'disabled' );
		}
		else{
			$( this ).removeClass( 'disabled' );
		}
	});
	$( ".fx-upmb-upload-url" ).change( function(){
		var url_input =  $( this ).val();
		var remove_zip =  $( this ).parents( '.fx-upmb-upload' ).find( '.remove-zip' );
		if( '' == url_input ){
			remove_zip.addClass( 'disabled' );
		}
		else{
			remove_zip.removeClass( 'disabled' );
		}
	});

	/* === Remove File === */
	$( document.body ).on( 'click', '.remove-zip', function(e){
		e.preventDefault();

		/* Remove url input */
		$( this ).parents( '.fx-upmb-upload' ).find( '.fx-upmb-upload-url' ).val('');

		/* Disabled */
		$( this ).addClass( 'disabled' );
	});

});
