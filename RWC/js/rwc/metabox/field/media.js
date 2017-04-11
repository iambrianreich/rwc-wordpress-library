(function($) {

    $.fn.mediaField = function(options) {

        return this.each(function() {

            /**
             * Updates the hidden media field.
             *
             * @param jQuery The jQuery element with the field.
             * @param Object The JSON object containing the images.
             *
             * @return void
             */
            function updateHidden( field, value) {

                field.val( JSON.stringify(value));
                field.data('images', value);
            }

            /**
             * Converts a string value for the field into a JSON compatible
             * object.
             *
             * @param string value The JSON string value to parse.
             *
             * @return Object Returns the JSON object.
             */
            function valueToObject(value) {
                return ( value == '') ? [] : JSON.parse(value);
            };

            var field = $(this);
            var hidden = $('input[type="hidden"]', field);
            var btn = $('.add-new-image-btn', this);

            // Initialize the JSON object in the hidden field.
            updateHidden( hidden, valueToObject(hidden.val()));

            // Load the images into the DOM.
            var images = hidden.data('images');
            $.each(images, function(i, image) {

                if( image != null ) {
                    $('<div class="img-container" data-id="' + image.id +
                        '"><img src="' + image.thumbnail +
                        '"/><span class="remove">X</span></div>').insertBefore(btn);
                }
            });

            /**
             * When Remove is clicked, remove the image from both the field
             * and the DOM.
             */
            $(this).on('click', '.img-container .remove', function() {

                var id = $(this).parent().data('id');

                $('[data-id="' + id + '"]', field).hide(500, function() {

                    // Remove item from DOM
                    $(this).remove();

                    // Remove from hidden field value.
                    var images = hidden.data('images');
                    var newImages = [];
                    $.each(images, function(i, image) {
                        if(image.id != id) newImages.push(image);
                    });
                    updateHidden(hidden, newImages);
                });
            });

            /**
             * When the Add button is clicked, open the media picker.
             */
            btn.on('click', function() {

                'use strict';

                var btn = $(this);
                var file_frame, image_data;

                // If the media picker is already loaded, open it.
                if ( undefined !== file_frame ) {

                    file_frame.open();
                    return;
                }

                // Load the media picker if it's not loaded.
                file_frame = wp.media.frames.file_frame = wp.media({
                    frame:    'post',
                    state:    'insert',
                    multiple: true
                });

                /*
                 * When the user clicks insert on the media screen, add the
                 * selected images to the field.
                 */
                file_frame.on( 'insert', function() {

                    //Get media attachment details from the frame state
                    var attachment = file_frame.state().get('selection').toJSON();
                    var value = field.val();
                    var images = hidden.data('images');

                    $.each(attachment, function(index, image) {

                        // Add the image to the JSON object
                        images.push({
                            id : image.id,
                            url : image.url,
                            thumbnail : image.sizes.square.url
                        });

                        // Add the image to the DOM
                        $('<div class="img-container" data-id="' + image.id +
                            '"><img src="' + image.sizes.square.url +
                            '"/><span class="remove">X</span></div>').insertBefore(btn);
                    });

                    // Update the field.
                    updateHidden(hidden, images);

                /*
                  // Send the attachment URL to our custom image input field.
                  imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

                  // Send the attachment id to our hidden input
                  imgIdInput.val( attachment.id );

                  // Hide the add image link
                  addImgLink.addClass( 'hidden' );

                  // Unhide the remove image link
                  delImgLink.removeClass( 'hidden' );
                  */


                });

                file_frame.open();
            });

            var container = $('.image-container', $(this));
            container.sortable({

                /**
                 * Update the JSON list on sort.
                 */
                update : function(e, ui) {

                    images = hidden.data( 'images' );

                    var list = [];

                    $('.img-container:has("img")', field).each(function(index, value) {

                        var current = $(this);
                        var currentId  = current.data('id');

                        function isMatch(element) {
                            return element.id == currentId;
                        }
                        list.push( images.find(isMatch) );
                    });
                    console.log(list);
                    updateHidden(hidden, list);
                },

                /**
                 * Prevent sorting of the add new image button.
                 */
                stop : function(e, ui ) {
                    if($(ui.item).hasClass('add-new-image-btn')) {
                        $(this).sortable( 'cancel');
                    }
                }
            });

            $('.image-container', $(this) ).disableSelection();

        }); // each
    }; // fn

    $(function() {
        $('.rwc-metabox-field-media').mediaField();
    });
})(jQuery);
