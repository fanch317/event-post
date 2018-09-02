<div class="eventpost-search-results">
    <?php if($events->have_posts()): ?>
    <span class="eventpost-search-item-count">
        <?php printf(_n('%d event found', '%d events found', $events->post_count, 'event-post'), $events->post_count); ?>
    </span>
    <hr>
    <?php while($events->have_posts()): $events->the_post(); ?>
    <article class="eventpost-search-item">
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail(); ?>
            <?php the_title(); ?>
        </a>
        <?php the_dates(); ?>
        <?php the_location(); ?>
        <?php the_excerpt(); ?>
    </article>
        <?php endwhile; ?>
    <?php else: ?>
        <?php _e('Sorry, there is no event matching your request.', 'event-post'); ?>
    <?php endif; ?>
</div>
