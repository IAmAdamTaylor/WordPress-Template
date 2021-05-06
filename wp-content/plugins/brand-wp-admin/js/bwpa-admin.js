( function( $, window, document, undefined ) {
	
	'use strict';

	function update_logo_id( id ) {
		var $input = $( '#brandwpadmin_logo_id' );
		
		$input.val( id );
	}

	function show_logo_preview( src ) {
		var $image = $( '#brandwpadmin_upload_logo_preview' );
		
		$image.next( 'p' ).hide();
		$image.attr( 'src', src ).show();
	}

	function update_favicon_id( id ) {
		var $input = $( '#brandwpadmin_favicon_id' );
		
		$input.val( id );
	}

	function show_favicon_preview( src ) {
		var $image = $( '#brandwpadmin_upload_favicon_preview' );
		
		$image.attr( 'src', src );
	}

	// For uploading files
	var file_frame;

	$('.js-brandwpadmin-open-upload-frame').on( 'click', function( event ) {

		var $this = $(this),
				frameArgs = {
					title: 'Select an image to upload',
					button: {
						text: 'Use this image',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				};

		if ( $this.attr( 'data-mime-type' ) ) {
			frameArgs.library = {
				type: $this.attr( 'data-mime-type' )
			};
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media( frameArgs );

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			var attachment = file_frame.state().get('selection').first().toJSON();
			
			// Do something with attachment.id and/or attachment.url here
			if ( 'brandwpadmin_upload_logo_button' == $this.attr('id') ) {

				update_logo_id( attachment.id );
				show_logo_preview( attachment.url );

			} else if ( 'brandwpadmin_upload_favicon_button' == $this.attr('id') ) {

				update_favicon_id( attachment.id );
				show_favicon_preview( attachment.url );

			}
		});
		
		// Finally, open the modal
		file_frame.open();

    event.preventDefault();
    return false;
  } );

  $('#brandwpadmin_logo_width').on( 'change', function() {
  	var $this = $(this),
  			width = $this.val();

  	// Constrain: min <= width <= max
		width = Math.max( $this.attr('min'), width );
		width = Math.min( width, $this.attr('max') );

		// Put the constrained value back into the input
		$this.val( width );

  	$('#brandwpadmin_upload_logo_preview').css( {
  		width: width
  	} );
  } );

} )( jQuery, window, document );
