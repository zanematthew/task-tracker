<?php
global $wp_query;
$args = array(
  'post_type' => $_POST['post_type'],
  'post_status' => $_POST['post_status']
);

$my_query = null;
$my_query = new WP_Query( $args );
?>
<div class="zm-tt-archive-container" >
   <table id="archive_table">
        <thead>
            <tr>
                <th id="title"><span>Title</span></th>
                <th id="milestone"><span>Phase</span></th>
                <th id="assigned"><span>Assigned</span></th>
                <th id="status"><span>Status</span></th>
                <th id="priority"><span>Priority</span></th>
                <th id="project"><span>Project</span></th>
            </tr>
        </thead>
        <?php $x = 0; ?>
        <?php if ( $my_query->have_posts() ) while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
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
                    <div class="milestone-container zm-base-item">
                        <?php print zm_base_get_the_term_list( array( 'link' => false, 'post_id' => $post->ID, 'taxonomy' => 'phase' )); ?>
                    </div>
                </td>
                <td>
                    <div class="milestone-container zm-base-item">
                        <?php print zm_base_get_the_term_list( array( 'link' => false, 'post_id' => $post->ID, 'taxonomy' => 'assigned' )); ?>
                    </div>
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
    <?php tt_json_feed( 'task',  array( 'status', 'priority', 'project', 'phase', 'assigned' ) ); ?>
</div>