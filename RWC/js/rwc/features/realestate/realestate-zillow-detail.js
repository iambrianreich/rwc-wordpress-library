(function($) {

    $(function() {

        $('.property.zillow-detail').each(function() {
            var detail = $(this);
            var images = $('.images', detail);
            var imagesList = $('ul', images);
            var nav = $('.navigation', images );
            var next = $('.next', nav);
            var prev = $('.previous', nav);
            var map = $('#google-map', detail);

            var point = {
                lat : map.data('latitude'),
                lng: map.data('longitude')
            };

            var gMap = new google.maps.Map( map[0], {
                zoom : 14,
                center : point
            });

            var marker = new google.maps.Marker({
                position: point,
                map : gMap
            });

            next.on('click', function(e) {

                e.preventDefault();

                var cur = $('.images li.current');

                // If the current element isn't set, get the first even child.
                if( cur.length == 0) {
                    cur = $('.images li:nth-child(even)').first();
                    cur.addClass('current');
                } else {


                    // Set new current as two elements past the current.
                    cur.removeClass( 'current');

                    cur = ( cur.is( ':nth-child(1)'))  ?
                        cur.next() : cur.next().next();

                    cur.addClass('current');
                }

                // Stop if there isn't a current element.
                if(cur == null || cur.length == 0) return;

                // Scroll to new current.
                var left = cur.position().left;
                var offset = parseInt(imagesList.css('marginLeft'));

                imagesList.animate({
                    marginLeft : offset - left
                });
            });

            prev.on('click', function(e) {

                e.preventDefault();

                var cur = $('.images li.current');

                // If the current element isn't set, get the first even child.
                if( cur.length == 0) {
                    cur = $('.images li').first();
                    cur.addClass('current');
                } else {

                    // Set new current as two elements past the current.
                    cur.removeClass( 'current');

                    cur = cur.is(':nth-child(2)') ? cur.prev() :
                        cur.prev().prev();

                    cur.addClass('current');
                }

                // Stop if there isn't a current element.
                if(cur == null || cur.length == 0) return;

                // Scroll to new current.
                var left = cur.position().left;
                var offset = parseInt(imagesList.css('marginLeft'));
                //alert("left: " + left);
                //alert("current offset: " + offset);
                //alert(offset - left);
                imagesList.animate({
                    marginLeft : offset - left
                });
            });

            // Hide empty sections.
            $('.section', detail).each(function() {
                var content = $('.content', $(this)).html().trim();
                if( content == '') {
                    $(this).hide();
                }
            });

            // Simple accordions.
            $('.section.collapse h3').on('click', function(e) {
                $(this).parent().toggleClass('expanded');
            });

            /**
             * Sticky Sidebar. Uncomment if you decide to use it.

            $('#contact-form', detail).each(function() {
                var form = $(this);
                var origin = form.position().top;
                var offset = form.offset().top;
                var zd = $('.zillow-detail')
                var fTop = zd.offset().top + zd.outerHeight();

                var stickySidebar = function() {
                    var yScroll = $(window).scrollTop();
                    if( yScroll > offset ) {

                        var top = Math.min(
                            top = yScroll - origin + 85 + $('#wpadminbar').outerHeight(),
                            fTop - form.outerHeight() - origin
                        );

                        form.clearQueue().animate({
                            top : top
                        }, 250, 'swing');
                    } else {
                        form.clearQueue().animate({
                            top : origin
                        }, 250, 'swing');
                    }
                };

                $(window).scroll(stickySidebar);
                stickySidebar();
            });
            */
            
            $('form', detail).on('submit', function() {

                var form = $(this);
                var data = jQuery(form).serialize();

                $.ajax({
                    type:"POST",
                    url: "/wp-admin/admin-ajax.php",
                    data: data,
                    success:function(data){
                        form.html(data);
                    }
                });
                return false;
            });
        });
    });
})(jQuery);
