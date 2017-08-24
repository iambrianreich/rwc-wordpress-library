(function($) {
    $(function() {

        $('.rwc-metabox-field-fundraiser-products .container .product header input[type="checkbox"]').on('click', function() {

            var parent = $('.options', $(this).closest('.product'));
            if( $(this).is(':checked')) {
                parent.addClass('enabled');
            } else {
                parent.removeClass('enabled');
            }

        });

        $('.rwc-metabox-field-fundraiser-products .container .product .options ul ul > li h5 input[type="checkbox"]').on('click', function() {
            var parent = $('.container', $(this).closest('li'));
            if( $(this).is(':checked')) {
                parent.addClass('enabled');
            } else {
                parent.removeClass('enabled');
            }
        });


        var table = $('.rwc-metabox-field-fundraiser-products .customizations table');
        var tbody = $('tbody', table);
        var template = $('.template', table);

        $('button.remove', tbody).on('click', function() {
            
            $(this).closest('tr').remove();
        });

        /*
         * When Add button is clicked, copy the template row and update index.
         */
        $('button.add', table).on('click', function() {

            // The number of rows currently in the table.
            var count = $('tbody tr', table).length;

            // Clone the template
            var newLine = template.clone();

            // Update the index
            $('input', newLine).each(function() {
                var id = $(this).prop('id');
                var newId = id.replace('index', count);

                $(this).prop('id', newId);
                $(this).prop('name', newId);

                newLine.appendTo(tbody).fadeIn(500);
            });

            $('button.remove', newLine).on('click', function() {

                $(this).closest('tr').remove();
            });
        });
    });
})(jQuery);
