(function($) {

	$(function() {

		// Initialize tooltips
		$( document ).tooltip();

		/**
		 * Instantiate the Google Map so the map object is visible
		 * to the rest of the code.
		 */

		var map = new google.maps.Map(document.getElementById('rwc-feature-postlocation-google-map'), {
		  center: {lat: -34.397, lng: 150.644},
		  zoom: 10
		});

		/**
		 * Instantiate the Marker so it's visible to the rest of the code.
		 */
		var marker = new google.maps.Marker({
			map : map,
			draggable : true
		});

		/**
		 * Shows a modal error message.
		 *
		 * @param string msg   The error message.
		 * @param string title The dialog title.
		 *
		 * @return void
		 */
		function showError( msg, title ) {

			$('<div title="' + title + '">' + msg + '</div>').dialog({
				modal : true,
				buttons : {
					Ok : function() {
						$(this).dialog( 'close'  );
					}
				}
			});
		}

		/**
		 * Sets the project's coordinates based on a Google Maps
		 * LatLng object.
		 *
		 * @param LatLng The Google Maps LatLng object.
		 *
		 * @return void
		 */
		function setCoordinates( latLng ) {

			$('#post_latitude').val( latLng.lat() );
			$('#post_longitude').val( latLng.lng() );

			marker.setPosition( latLng );
			map.setCenter( latLng );
		}

		/**
		 * When the user drags the marker it resets the coordinates.
		 */
		google.maps.event.addListener(marker, 'dragend', function(e) {
			setCoordinates( e.latLng );
		});

		/**
		 * If the project has a set latitude and longitude, use it for the initial
		 * position.
		 */
		if( $('#post_latitude').val() != '' && $('#post_longitude').val() != '' ) {

			var location = new google.maps.LatLng(
				$('#post_latitude').val(),
				$('#post_longitude').val()
			);

			marker.setPosition( location );
			map.setCenter( location );
		}

		/**
		 * When the geolocate link is clicked, try to geocode the address
		 * specified by the user.
		 */
		$('#geocode, button.geolocate').on('click', function(e) {

			// Don't do default link behavior
			e.preventDefault();

			/**
			 * Get the address entered by the user.
			 */
			var address = $('#post_address').val();

			// If there isn't an address, don't geocode.
			if( address == '' ) {

				showError( 'Please specify an address.', 'Blank Address' );
				return;
			}

			// Do the geocode.
			(new google.maps.Geocoder()).geocode({ address : address }, function( result, status) {

				if( result.length == 0 ) {

					showError('<p>No geolocation results found for the address:</p><pre>' +
						address + '</pre><p>Refine the address and try again, or ' +
						'manually specify the latitude and longitude.</p>', 'Geocoding Failed' );

				} else {

					var location = result[0].geometry.location;
					setCoordinates( location );
				}
			} );
		});

		$('#post_latitude, #post_longitude').on('change', function() {

			var lat = $('#post_latitude').val();
			var lng = $('#post_longitude').val();

			if( lat != '' && lng != '' ) {

				try {

					var location = new google.maps.LatLng( lat, lng );
					setCoordinates( location );
				} catch(e) {
					showError('<p> ' + lat + ', ' + lng + ' are not valid GPS coordinates.',
						'Geocoding Failed' );
				}
			}
		});
	});
})(jQuery);
