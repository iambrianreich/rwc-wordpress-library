/**
 * A jQuery plug-in for simple vertical tabs without loading a larger library.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 */
(function($) {

    $.fn.verticalTab = function( options ) {

        // Mix-in options
        var settings = $.extend({}, options );

        // Iterate through matches
        return this.each(function() {

            var tabs = this;

            $(this).addClass( 'rwc-vertical-tabs' );
            // On click, select.
            $('> ul li a', this).on( 'click', function() {

                // Deselect all
                $( '.selected', tabs).removeClass('selected');

                var target = $(this).attr('href');

                $(this).addClass( 'selected');
                $( target, tabs).addClass( 'selected');

                return false;
            });

            // Invoke first
            $('> ul li a', this).first().trigger( 'click' );

        });
    };

    $(function() {
        $('.vertical-tabs').verticalTab();
    });
})(jQuery);
