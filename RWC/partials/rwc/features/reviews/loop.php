<ul class="rwc-reviews-list">
	<?php while( $query->have_posts () ) : $query->the_post(); ?>
		<li class="review">
		<?php
			$review = $GLOBALS[ 'review' ];
			include('single-content.php'); 
		?></li>	
	<?php endwhile; ?>
</ul>
