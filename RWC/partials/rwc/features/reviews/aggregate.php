<?php

/**
 * Keeps a running count of the total number of reviews in the query.
 * 
 * @var integer
 */
$totalReviews = 0;

/**
 * Keeps a running tally of the total number of points awarded by the reviews
 * returned by the query.
 * 
 * @var integer
 */
$totalPoints = 0;

/**
 * Keeps a running tally of the total number of available points available to
 * all reviews returned by the query.
 * 
 * @var integer
 */
$totalAvailablePoints = 0;

// Iterates through all of the reviews in the query.
while( $query->have_posts () ) {
	
	// Get current Review
	$query->the_post();
	
	// Get post wrapper.
	$review = $GLOBALS['review'];
	
	// Increment review count.
	$totalReviews++;
	
	// Add review's points to total
	$totalPoints += $review->get_review_rating();
	
	// Add possible points to total available.
	$totalAvailablePoints += $review->get_max_rating(); 
}
	
/**
 * In case reviews were on different scales, we need to determine the average
 * number of available points.
 * 
 * @var float
 */
$averageAvailablePoints = $totalAvailablePoints / $totalReviews;

/**
 * The average rating, determined by dividing the total number of points
 * awarded by the total available, and then multiplying it by the average
 * available points.
 * 
 * @var float $averageRating
 */
$averageRating = $totalPoints / $totalAvailablePoints * $averageAvailablePoints;

?>
<div class="rwc-reviews-aggregate" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
	Rated <span itemprop="ratingValue"><?php echo round( $averageRating ); ?></span> / <?php echo $averageAvailablePoints; ?> 
  	based on <span itemprop="reviewCount"><?php echo $totalReviews; ?></span> customer 
  	<?php echo ( $totalReviews > 1 ? 'reviews' : 'review' ); ?>
</div>