<?php

/**
 * This template renders attribute selection fields for Fundraiser Products.
 *
 * The following view variables must be in scope when the template is rendered.
 * The $fundraiser variable should be set to a Fundraiser instance which
 * specifies the Fundraiser that is currently selected by the user. The $product
 * variable is set to the WC_Product that is currently being viewed.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features\Fundraiser
 * @subpackage Templates
 */

$fundraiserProduct = $fundraiser->get_enabled_product( $product ); ?>
<?php if( $fundraiserProduct == null) : ?>
    <p class="message product-not-in-fundraiser">
        The product <?php the_title() ?> has not been marked for sale in this
        fundraiser. Purchase features have been disabled.
    </p>
<?php else : ?>
    <?php $attributes = $fundraiserProduct->get_options(); ?>
    <?php $customizations = $fundraiserProduct->get_customizations(); ?>
    <table class="variations" cellspacing="0">
        <tbody>
            <?php foreach ( $attributes as $attribute ) : ?>
                <tr>
                    <td class="label">
                        <label for="<?php echo  $attribute->get_field_name(); ?>">
                            <?php echo $attribute->get_field_label(); ?>
                        </label>
                    </td>
                    <td class="value">
                        <select id="<?php echo $attribute->get_field_name(); ?>"
                            name="<?php echo $attribute->get_field_name(); ?>">
                            <option value="">Select an Option...</option>
                            <?php foreach( $attribute->get_enabled_attribute_options()  as $option ) : ?>
                                <option
                                    value="<?php echo $option->get_value(); ?>"
                                    <?php if( $option->is_default() ) echo 'selected="selected"'; ?> ><?php echo $option->get_friendly_name(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php endforeach;?>
            <?php foreach( $customizations as $customization ) : ?>
                <tr>
                    <td class="label">
                        <label for="<?php echo $customization->get_field_name(); ?>">
                            <?php echo $customization->get_friendly_name(); ?>
                        </label>
                    </td>
                    <td class="value">
                        <input
                            type="text"
                            id="<?php echo $customization->get_field_name(); ?>"
                            name="<?php echo $customization->get_field_name(); ?>" />
                        <div class="description">
                            <?php echo $customization->get_friendly_description(); ?>
                            <?php if( $customization->get_price_differential() != 0 ) : ?>
                                <span class="price_differential">(<?php echo $customization->get_friendly_price_differential(); ?>)</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
