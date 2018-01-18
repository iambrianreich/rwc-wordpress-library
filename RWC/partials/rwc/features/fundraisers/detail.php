<?php

/**
 * Fundraiser detail view.
 *
 * This view template will render the detailed view for a Fundraiser. It will
 * display the fundraiser title and content, contact details, and a block
 * containing all of the products associated with the fundraiser which will
 * allow the customer to purchase via the fundraiser.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting https://www.reich-consulting.net/
 */
?><section class="rwc-fundraiser" id="rwc-fundraiser-<?php the_ID(); ?>">
    <div class="content">
        <header>
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
        </header>
        <section class="fundraiser-products">
            <h2>Fundraiser Merchandise</h2>
            <?php $products = $fundraiser->get_products_query(); ?>
            <?php if( $products->have_posts() ) : ?>
                <ul class="products">
					<?php while( $products->have_posts() ) : $products->the_post(); ?>
                    
                        <?php wc_get_template_part( 'content', 'product' ); ?>
					<?php endwhile; ?>
				</ul>
            <?php else : ?>
                <p>No merchandise has been assigned to this fundraiser.</p>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </section>
    </div>
</section>
