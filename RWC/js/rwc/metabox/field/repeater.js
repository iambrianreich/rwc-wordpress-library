/**
 * This file contains the JavaScript functionality for Repeater fields.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */
(function($) {

    $.fn.repeaterField = function(options) {

        return this.each(function() {
            var addButton = $('input.add', this );
            var template = $('.item-template', this);

            $('input, textarea,select', template).each(function() {

                $(this)
                    .data('name', $(this).prop('name'))
                    .removeProp('name')
                    .data('id', $(this).prop('id'))
                    .removeProp('id');

            });

            $(this).on('click', 'input.remove', function() {
                $(this).parent('.rwc-metabox-repeater-row').hide(500, function() {
                    $(this).remove();
                });
            });

            addButton.on('click', function() {
                var row = template.clone(true)
                    .removeClass('item-template')
                    .addClass('rwc-metabox-repeater-row')
                    .insertBefore(template);

                $('input, textarea,select', row).each(function() {


                    $(this)
                        .prop('name', $(this).data('name') + '[]')
                        .prop('id', $(this).data('id') + '[]');
                });
            });
        });
    };

    $(function() {
        $('.rwc-metabox-field-repeater').repeaterField();
    });
})(jQuery);
