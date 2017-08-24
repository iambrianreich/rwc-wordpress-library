<?php
?>
<?php if( $query->have_posts() ) : ?>
    <ul class="fundraiser-list">
        <?php while( $query->have_posts() ) : $query->the_post(); ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
        <?php endwhile; ?>
    </ul>
<?php else : ?>
    <p>No fundraisers are currently running.</p>
<?php endif; ?>
