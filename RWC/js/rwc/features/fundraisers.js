(function($) {

    $(function() {

        var fields = $( '.summary .variations select');
        var checkoutItems = $('.summary .quantity, .summary .button[type="submit"]');
        function variations_have_values() {

            var hasValue = true;
            fields.each(function() {
                console.log(hasValue);
                hasValue = hasValue & ( $(this).val() != '' );
            });

            if( hasValue == false ) {
                checkoutItems.hide();
            } else {
                checkoutItems.show();
            }
        }

        fields.on('change', variations_have_values);

    });
})(jQuery);
