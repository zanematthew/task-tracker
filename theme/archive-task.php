<?php get_header(); ?>
<?php get_template_part( 'header-container', 'single' ); ?>
    <div class="zm-tt-container zm-tt-archive-container">
        <div class="tt-glue">
            <div class="main-container">
                <?php load_template( MY_PLUGIN_DIR . 'theme/navigation.php' ); ?>   
                <div class="tt-filter-container">
                    <ul class="inline">
                        <li><a href="#filter-task" id="filter_handle" tt_template="navigation-filter.php">Filter</a></li>                
                    </ul>
                </div>
                <div id="tt_filter_target"></div>                
                <div id="tt_main_target">
                    <div class="tt_loading"></div>
                    <div class="sample" tt_template="theme/archive-table.php"></div>
                    <div id="no_results">nothing here move on..</div>                        
                </div>
<?php
/*
global $wp_query;
$args = array(
  'post_type' => 'task',
  'post_status' => 'publish'
);

$my_query = null;
$my_query = new WP_Query( $args );
*/
?>
<div class="zm-tt-archive-container" >
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
        <?php // if ( $my_query->have_posts() ) while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
        <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
            <tr <?php post_class('result')?>>
                <td><?php $x++; ?>
                     <strong class="title"><a href="<?php the_permalink(); ?>" title="Link to project: <?php the_title(); ?>">#<?php the_ID(); ?> <?php the_title(); ?></a></strong>
                     <span class="comment-count"><?php comments_number(' '); ?></span>
                     <div class="utility-container zm-base-hidden">
                         <?php edit_post_link('Admin Edit', '' , ' |'); ?>
                         by <?php the_author(); ?> on <?php the_time(get_option('date_format')); ?> |
                     </div>
                </td>
                <td>
                    <div class="milestone-container zm-base-item">
                        <?php print zm_base_get_the_term_list( array( 'link' => true, 'post_id' => $post->ID, 'taxonomy' => 'phase' )); ?>
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
            </tr>
        <?php endwhile; ?>
    </table>
    <div id="no_results">nothing here move on..</div>
    <?php tt_json_feed( 'task',  array( 'status', 'priority', 'project', 'phase', 'assigned' ) ); ?>
</div>


            </div>
            <div class="zm-tt-sidebar-container">
                <?php zm_base_list_terms( array('taxonomy' => 'status', 'link' => 'javascript://' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'priority', 'link' => 'anchor' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'project' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'phase' ) ); ?>
                <?php zm_base_list_terms( array('taxonomy' => 'assigned' ) ); ?>
            </div>
        </div>
    </div>
<?php tt_json_feed( 'task',  array( 'status', 'priority', 'project', 'phase', 'assigned' ) ); ?>
<?php get_footer(); ?>
