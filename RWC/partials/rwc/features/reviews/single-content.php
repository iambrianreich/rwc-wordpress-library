<?php

/**
 * Renders the content for a single Review post as a Schema.org Review.
 * 
 * This template requires the following variables to be in scope. The $review
 * variable must reference an instance of RWC\Features\Reviews\Post. The
 * template should also be rendered within a Loop so that WordPress template
 * functions reference the current post.
 * 
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting https://www.reich-consulting.net/
 */
?>
<div itemprop="review" itemscope itemtype="http://schema.org/Review">
	<h3 itemprop="name"><?php the_title(); ?></h3>
	<div itemprop="author"><?php echo esc_html( $review->get_author_name() ); ?></div>
	<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
      <meta itemprop="worstRating" content = "<?php echo esc_attr( $review->get_min_rating() ); ?>">
      <span itemprop="ratingValue" class="rating-<?php echo esc_attr( $review->get_review_rating() ); ?>">
      	<?php echo esc_html( $review->get_review_rating() ); ?>
      </span> /
      <span itemprop="bestRating"><?php echo esc_html( $review->get_max_rating() ); ?></span> 
      Stars
    </div>
	<div itemprop="reviewBody"><?php the_content(); ?></div>
</div>