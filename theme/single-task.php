<?php get_header(); ?>
<?php get_template_part('header-container','index'); ?>
<div class="zm-tt-container zm-tt-single-container">
    <div class="tt-glue">
        <div class="main-container">
            <?php load_template( MY_PLUGIN_DIR . '/theme/default/navigation.php' ); ?>
            <div id="tt_main_target">
                <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                    <div <?php post_class('result')?>>
                        <h1 class="title"><?php the_title(); ?></h1>
                        <?php the_content(); ?>
                    <div class="nav-previous-container">
                    </div>
                        <div class="entry-utility" id="utility">
                            <ul class="inline">
                                <li><div class="status-container"><small>Status</small><br /> <?php print zm_base_get_the_term_list($post->ID, 'status'); ?></div></li>
                                <li><div class="priority-container"><small>Priority</small><br /><?php print zm_base_get_the_term_list( $post->ID, 'priority'); ?></div></li>
                                <li><div class="project-container"><small>Project</small><br /><?php print zm_base_get_the_term_list($post->ID, 'project'); ?></div></li>
                                <li><div class="milestone-container"><small>Milestone</small><br /><?php print zm_base_get_the_term_list($post->ID, 'milestone'); ?></div></li>
                                <li><div class="ticket-container"><?php print '<small>Ticket </small><br />#' . '<a href="'. get_permalink() .'">' . $post->ID . '</a>'; ?></div></li>
                                <li><span class="ui-icon ui-icon-arrow"><a href="#top">Return to Top</a></span></li>
                            </ul>
                        </div>
                    </div>
                    <?php load_template( MY_PLUGIN_DIR . '/theme/custom/task-update.php' ); ?>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="zm-tt-sidebar-container">
            <?php zm_base_list_terms( array('taxonomy' => 'status' ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'priority', 'link' => false ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'project', 'link' => false ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'phase', 'link' => false ) ); ?>
            <?php zm_base_list_terms( array('taxonomy' => 'assigned', 'link' => false ) ); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>

