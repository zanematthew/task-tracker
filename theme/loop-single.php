<div class="zm-tt-single-container">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
    <div class="nav-previous-container">
    <ul>
        <li class="previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></li>
        <li class="next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></li>
    </ul>
    </div>
    <div <?php post_class('result')?>>
        <h1 class="title"><?php the_title(); ?></h1>
        <?php the_content(); ?>
    <div class="nav-previous-container">
    <ul class="inline">
        <li class="previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></li>
        <li class="next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></li>
    </ul>
    </div>
        <div class="entry-utility" id="utility">
        <ul class="inline">
            <li><div class="status-container"><small>Status</small><br /> <?php print zm_base_get_the_term_list($post->ID, 'status'); ?></div></li>
            <li><div class="priority-container"><small>Priority</small><br /><?php print zm_base_get_the_term_list( $post->ID, 'priority'); ?></div></li>
            <li><div class="category-container"><small>Category</small><br /><?php the_category(); ?></div></li>
            <li><div class="project-container"><small>Project</small><br /><?php print zm_base_get_the_term_list($post->ID, 'project'); ?></div></li>
            <li><div class="milestone-container"><small>Milestone</small><br /><?php print zm_base_get_the_term_list($post->ID, 'milestone'); ?></div></li>
            <li><div class="ticket-container"><?php print '<small>Ticket </small><br />#' . '<a href="'. get_permalink() .'">' . $post->ID . '</a>'; ?></div></li>
            <li><span class="ui-icon ui-icon-arrow"><a href="#top">Return to Top</a></span></li>
        </ul>
        </div>
    </div>
<?php load_template( MY_PLUGIN_DIR . '/theme/task-update.php' ); ?>

<?php comments_template( '', true ); ?>
<?php endwhile; ?>
</div>
