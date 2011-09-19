<?php get_header(); ?>
<?php get_template_part('header-container','index'); ?>
    <div class="zm-tt-container zm-tt-archive-container">
        <div class="tt-glue">
            <div class="main-container">
                <?php load_template( MY_PLUGIN_DIR . 'theme/default/navigation.php' ); ?>   
                <div class="tt-filter-container">
                    <ul class="inline">
                        <li><a href="#filter-task" id="filter_handle" tt_template="default/navigation-filter.php" data-post_type="<?php global $wp_query; print $wp_query->posts[0]->post_type; ?>">Filter</a></li>                        
                    </ul>
                </div>
                <div id="tt_filter_target"></div>                
                <div class="zm-tt-archive-container" >
                   <table id="archive_table">
                        <thead>
                            <tr>
                                <th id="title"><span>Title</span></th>                
                                <th id="age"><span>Age</span></th>
                                <th id="status"><span>Status</span></th>
                                <th id="priority"><span>Priority</span></th>
                                <th id="project"><span>Project</span></th>                
                                <th id="project"><span>Type</span></th>                                            
                            </tr>
                        </thead>
                        <?php $x = 0; ?>
                        <?php // if ( $my_query->have_posts() ) while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
                        
                        <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                            <?php $x++; ?>
                            <tr <?php post_class('result')?>>
                                <td>
                                     <strong class="title"><a href="<?php the_permalink(); ?>" title="Link to project: <?php the_title(); ?>">#<?php the_ID(); ?> <?php the_title(); ?></a></strong>
                                     <span class="comment-count"><?php comments_number(' '); ?></span>
                                     <div class="utility-container zm-base-hidden">
                                         <?php edit_post_link('Admin Edit', '' , ' |'); ?>
                                         by <?php the_author(); ?> on <?php the_time(get_option('date_format')); ?> |
                                         <a href="#delete" class="default_delete" data-post_id="<?php print $post->ID; ?>" data-security="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">Delete</a>
                                     </div>
                                </td>
                                <td>
                                    <?php tt_task_age(); ?>
                                </td>
                                <td>
                                    <div class="status-container">
                                        <?php print zm_base_get_the_term_list( array( 'link' => true, 'post_id' => $post->ID, 'taxonomy' => 'status' ) ); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="priority-container zm-base-item">
                                        <?php print zm_base_get_the_term_list( array( 'link' => true, 'post_id' => $post->ID, 'taxonomy' => 'priority' ) ); ?>
                                    </div>
                                <td>
                                    <div class="project-container zm-base-item">
                                        <?php print zm_base_get_the_term_list( array( 'link' => true, 'post_id' => $post->ID, 'taxonomy' => 'project' ) ); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="type-container">
                                        <?php print zm_base_get_the_term_list( array( 'link' => true, 'post_id' => $post->ID, 'taxonomy' => 'type' ) ); ?>
                                    </div>
                                </td>                            
                            </tr>
                        <?php endwhile; ?>
                    </table>
                    <?php tt_json_feed( 'task',  array( 'status', 'priority', 'project', 'phase', 'assigned' ) ); ?>
                </div>
            </div>
            <div class="zm-tt-sidebar-container">
                <?php zm_base_list_terms( array('taxonomy' => 'status', ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'priority' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'project' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'phase' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'assigned' ) ); ?>
            </div>
        </div>
    </div>
<?php tt_json_feed( 'task',  array( 'status', 'priority', 'project', 'phase', 'assigned' ) ); ?>
<?php get_footer(); ?>
