<?php

/**
 * This file renders a Fundraising Report.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 */

$aggregatePrice = 10;
$aggregate = [];

// Ensure that the Fundraiser is setup.
if( ! isset( $fundraiser ) )
{
	throw new Exception('Template variable "fundraiser" not set.' );
}

// Ensure that we have the path to the library's CSS directory.
if( ! isset( $cssDirectory ) )
{
	throw new Exception( 'Template variable "library" not set.' );
}

/**
 * Accepts items in a fundraiser and generates and stores aggregate information.
 *
 * @param WC_Order_Item_Product                $item           The item to aggregate.
 * @param \RWC\Features\Fundraisers\Fundraiser $fundraiser     The fundraiser.
 * @param float                                $aggregatePrice The current total price of the fundraiser.
 * @param array                                $aggregate      Aggregate info about the items.
 *
 * @return void
 */
function aggregate_item( $item, $fundraiser, & $aggregatePrice, & $aggregate )
{

	// The unique id of the product
	$productId = $item->get_product_id();

	// The product descriptor
	$product = $item->get_product();

	// Item metadata
	$meta = $item->get_meta_data();

	// Get the product details specific to the fundraiser
	$fundraisingProduct = $fundraiser->get_enabled_product( $product );

	// Add to the aggregate price.
	$aggregatePrice += $item->get_total();

	// Add the item to the aggregate data aray.
	if( ! isset( $aggregate[ $productId ] ) ) {
		$aggregate[ $productId ] = array(
			'product_id' => $productId,
			'name' => $item->get_name(),
			'options' => array()
		);
	}


	// Iterate through options
	foreach ( $fundraisingProduct->get_options() as $option ) {
		$option->set_option( 'product', $product );
		$key[] = $option->get_field_label() . '=' .
			$meta[0]->value[ $option->get_field_name() ];
	}

	// The key is built off the combination of metadata fields.
	$key = implode( '|', $key );

	// Create keyed option count.
	if( ! isset( $aggregate[ $productId ][ 'options' ][ $key ])) {
		$aggregate[ $productId ][ 'options' ][ $key ] = 0;
	}

	// Add keyed option count.
	$aggregate[ $productId ][ 'options' ][ $key ] += $item->get_quantity();
}

?><!DOCTYPE html>
<html>
  <head>
	<link rel="stylesheet" href="<?php echo $cssDirectory; ?>/css/rwc/features/fundraisers/fundraising-report.css" />
	<title>Fundraiser Report for <?php bloginfo( 'sitename' ); ?></title>
  </head>
  <body class="small-fonts">
	<header>
	  <div class="title">
		<?php echo apply_filters  ( 'rwc_fundraiser_report_name', '<h1>' . get_bloginfo( 'name' ) . '</h1>' ); ?>
	  </div>
	  <div class="fundraiser-info">
		<h2>Fundraising Report</h2>
		<div class="contact-info">
		  <div class="business-name"><?php echo esc_html( $fundraiser->get_business_name() ); ?></div>
		  <div class="customer-name"><?php echo esc_html( $fundraiser->get_customer_name() ); ?></div>
		  <div class="address-1"><?php echo esc_html( $fundraiser->get_address1() ); ?></div>
		  <div class="address-2"><?php echo esc_html( $fundraiser->get_address2() ); ?></div>
		  <div class="city"><?php echo esc_html( $fundraiser->get_city() ); ?></div>
		  <div class="state"><?php echo esc_html( $fundraiser->get_state() ); ?></div>
		  <div class="zipcode"><?php echo esc_html( $fundraiser->get_zipcode() ); ?></div>
		</div>
	  </div>
	</header>
	<?php foreach( $orderIds as $orderId ) : ?>
		<?php $order = wc_get_order( $orderId ); ?>
		<section class="order">
			<h3 class="order-no">Order #<?php echo esc_html( $order->get_order_number() ); ?></h3>
			<address>
				<div class="billing-info">
				  <div class="first-name"><?php echo esc_html( $order->get_billing_first_name() ); ?></div>
				  <div class="last-name"><?php echo esc_html( $order->get_billing_last_name() ); ?></div>
				  <div class="company"><?php echo esc_html( $order->get_billing_company() ); ?></div>
				  <div class="address-1"><?php echo esc_html( $order->get_billing_address_1() ); ?></div>
				  <div class="address-2"><?php echo esc_html( $order->get_billing_address_2() ); ?></div>
				  <div class="city"><?php echo esc_html( $order->get_billing_city() ); ?></div>
				  <div class="state"><?php echo esc_html( $order->get_billing_state() ); ?></div>
				  <div class="zipcode"><?php echo esc_html( $order->get_billing_postcode() ); ?></div>
				</div>
			</address>
			<table>
			  <thead>
				<tr>
				  <th class="details">Details</th>
				  <th class="price">Price</th>
				  <th class="quantity">Quantity</th>
				  <th class="total">Total</th>
				</tr>
			  </thead>
			  <tbody>
				  <tr class="order">
					  <?php $items = $order->get_items(); ?>
					  <?php foreach( $items as $item ) : ?>
						  <?php aggregate_item( $item, $fundraiser, $aggregatePrice, $aggregate ); ?>
						  <?php $product = $item->get_product(); ?>
						  <td class="details">
							  <?php $meta = $item->get_meta_data(); ?>
							  <div class="name"><?php echo esc_html( $item->get_name() ); ?></div>
								  <?php $fundraisingProduct = $fundraiser->get_enabled_product( $product ); ?>
								  <?php foreach ( $fundraisingProduct->get_options() as $option ) : ?>
									  <dl class="attribute">
										  <?php $option->set_option( 'product', $product ); ?>
										  <dt><?php echo esc_html( $option->get_field_label() ); ?></dt>
										  <dd><?php echo esc_html( $meta[0]->value[ $option->get_field_name() ] ); ?></dd>
									  </dl>
								  <?php endforeach; ?>
								  <?php foreach ( $fundraisingProduct->get_customizations() as $customization ) : ?>
									  <?php $value =$meta[0]->value[ $customization->get_field_name() ]; ?>
									  <?php if( ! empty( $value ) ) : ?>
										  <dl class="attribute">
											  <?php $customization->set_option( 'product', $product ); ?>
											  <dt><?php echo esc_html( $customization->get_friendly_name() ); ?></dt>
											  <dd><?php echo esc_html( $value ); ?></dd>
										  </dl>
									  <?php endif; ?>
								  <?php endforeach; ?>
						  <td class="price"><?php echo wc_price ( $item->get_total() / $item->get_quantity() ); ?></td>
						  <td class="quantity"><?php echo esc_html( $item->get_quantity() ); ?></td>
						  <td class="total"><?php echo wc_price( $item->get_total() ) ; ?></td>
					  <?php endforeach; ?>
				  </tr>
			  </tbody>
		  </table>
		</section>
	<?php endforeach; ?>

	<footer>
		<h2>Order Aggregate Information</h2>

		<h3 class="total-cost">Fundraiser Total: <span class="total"><?php echo wc_price( $aggregatePrice ); ?></h3>

		<h3>Item Quantities</h3>

		<ul>
			<?php foreach( $aggregate as $product ) : ?>
				<?php foreach( $product[ 'options' ] as $k => $value ) : ?>
					<?php $options = explode( '|', $k ); ?>
					<?php $quantity = $value; ?>
					<li>
						<h3>
							<span class="name"><?php echo esc_html( $product[ 'name' ] ); ?></span>
							<span class="id"><?php echo esc_html( $product[ 'product_id' ] ); ?></span>
						</h3>
						<ul>
								<?php foreach( $options as $option ) : ?>
									<?php list($name, $value ) = explode( '=', $option ); ?>
									<li>
										<span class="option"><?php echo esc_html( $name ); ?></span>
										<span class="value"><?php echo $value; ?></span>
									</li>
								<?php endforeach; ?>

						</ul>
						<span class="quantity"><?php echo esc_html( $quantity ); ?></span>
					</li>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</ul>
	</footer>
  </body>
</html>
