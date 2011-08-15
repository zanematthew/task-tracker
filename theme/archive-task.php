<?php get_header(); ?>
<?php get_template_part( 'header-container', 'single' ); ?>
        <div class="zm-tt-container zm-tt-archive-container">
<div class="tt-glue">
            <div class="main-container">
                    <?php load_template( MY_PLUGIN_DIR . 'theme/navigation.php' ); ?>   
                    <div id="tt_main_target">
                       <table id="archive_table">
                            <thead>
                                <tr>
                                    <th id="title"><span>Title</span></th>
                                    <th id="milestone"><span>Phase</span></th>
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
                                         #<?php the_ID(); ?>                                    
                                         <strong class="title">
                                         <a href="<?php the_permalink(); ?>" title="Link to project: <?php the_title(); ?>"><?php the_title(); ?></a></strong>
                                         <span class="comment-count"><?php comments_number(' '); ?></span>
                                         <div class="utility-container zm-base-hidden">
                                             <?php edit_post_link('Admin Edit', '' , ' |'); ?>
                                             by <?php the_author(); ?> on <?php the_time(get_option('date_format')); ?> |
                                         </div>
                                    </td>
                                    <td>
                                        <div class="milestone-container zm-base-item">
                                            <?php print zm_base_get_the_term_list( array( 'link' => false, 'post_id' => $post->ID, 'taxonomy' => 'phase' )); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php tt_task_age(); ?>
                                    </td>
                                    <td>
                                        <div class="status-container">
                                            <?php print zm_base_get_the_term_list( array( 'link' => false, 'post_id' => $post->ID, 'taxonomy' => 'status' ) ); ?>                                            
                                        </div>
                                    </td>
                                    <td>
                                        <div class="priority-container zm-base-item">
                                            <?php print zm_base_get_the_term_list( array( 'link' => false, 'post_id' => $post->ID, 'taxonomy' => 'priority' ) ); ?>
                                        </div>
                                    <td>
                                        <div class="project-container zm-base-item">
                                            <?php print zm_base_get_the_term_list( array( 'link' => false, 'post_id' => $post->ID, 'taxonomy' => 'project' ) ); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                        <?php // load_template( MY_PLUGIN_DIR . 'theme/archive-table.php' ); ?>
<div id="no_results">nothing here move on..</div>                        
                </div>
            </div>

                <div class="zm-tt-sidebar-container">
                    <?php zm_base_list_terms( array('taxonomy' => 'status', 'link' => false ) ); ?>
                    <?php zm_base_list_terms( array('taxonomy' => 'priority', 'link' => false ) ); ?>
                    <?php zm_base_list_terms( array('taxonomy' => 'project', 'link' => false ) ); ?>
                    <?php zm_base_list_terms( array('taxonomy' => 'phase', 'link' => false ) ); ?>
                    <?php zm_base_list_terms( array('taxonomy' => 'assigned', 'link' => false ) ); ?>
                </div>
        </div>
</div>
<?php tt_json_feed(); ?>
<?php get_footer(); ?>
