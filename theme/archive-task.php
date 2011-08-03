<?php get_header(); ?>
<?php get_template_part( 'header-container', 'single' ); ?>
<div class="container_12">
    <div class="grid_12">
<div class="zm-tt-container zm-tt-archive-container">
        <div class="main-container">
            <div class="grid_9 alpha">
                <?php load_template( MY_PLUGIN_DIR . 'theme/navigation.php' ); ?>   
                <div id="tt_main_target">
               <table>
                    <thead>
                        <tr>
                            <th id="title"><span>Title</span></th>
                            <th id="milestone"><span>Milestone</span></th>
                            <th id="age"><span>Age</span></th>
                            <th id="status"><span>Status</span></th>
                            <th id="priority"><span>Priority</span></th>
                            <th id="project"><span>Project</span></th>
                        </tr>
                    </thead>
                    <?php $x = 0; ?>
                    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                        <tr <?php post_class('result')?>>
                            <td><?php $x++; ?>
                                 <strong class="title"><a href="<?php the_permalink(); ?>" title="Link to project: <?php the_title(); ?>"><?php the_title(); ?></a></strong>
                                 <span class="comment-count"><?php comments_number(' '); ?></span>
                                 <div class="utility-container zm-base-hidden">
                                     <?php edit_post_link('Admin Edit', '' , ' |'); ?>
                                     by <?php the_author(); ?> on <?php the_time(get_option('date_format')); ?> |
                                 </div>
                            </td>
                            <td>
                                <div class="milestone-container"><?php print zm_base_get_the_term_list($post->ID, 'milestone'); ?></div>
                            </td>
                            <td>
                            <?php project_age(); ?>
                            </td>
                            <td>
                                <div class="status-container"><?php print zm_base_get_the_term_list($post->ID, 'status'); ?></div>
                            </td>
                            <td>
                                <div class="priority-container"><?php print zm_base_get_the_term_list( $post->ID, 'priority'); ?></div>
                            <td>
                                <div class="project-container"><?php print zm_base_get_the_term_list($post->ID, 'project'); ?></div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>

                    <?php // load_template( MY_PLUGIN_DIR . 'theme/archive-table.php' ); ?>
                </div>
            </div>
            <div class="grid_3 omega zm-tt-sidebar-container">
                <?php zm_base_list_terms( 'status' ); ?>
                <?php zm_base_list_terms( 'priority' ); ?>
                <?php zm_base_list_terms( 'project' ); ?>
                <?php zm_base_list_terms( 'phase' ); ?>
                <?php zm_base_list_terms( 'assigned' ); ?>
            </div>
            </div>
    </div>
</div>
</div>
<?php get_footer(); ?>
