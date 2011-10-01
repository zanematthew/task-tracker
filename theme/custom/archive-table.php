<?php
global $wp_query;

$cpt = $_POST['post_type'];
$cpt_obj = get_post_types( array( 'name' => $cpt), 'objects' );

$args = array(
  'post_type' => $cpt,
  'post_status' => 'publish'
);

$my_query = null;
$x = 0;
$my_query = new WP_Query( $args );

?>
<div class="zm-tt-archive-container" >
   <table id="archive_table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Title</th>
                <th>Status</th>
                <th>Priority</th>
            </tr>
        </thead>    
        <?php if ( $my_query->have_posts() ) while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
        <?php

$comments_count = wp_count_comments( $post->ID);

if ( $comments_count->total_comments == 1 ) 
    $comment_class = 'comment-count';

elseif ( $comments_count->total_comments > 1 ) 
    $comment_class = 'comments-count';
else 
    $comment_class = '';

$x++;
        ?>
            <tr <?php post_class('result')?>>
                <td>
                    <div class="type-container zm-base-item">
                        <?php print zm_base_get_the_term_list( array( 'post_id' => $post->ID, 'taxonomy' => 'type' )); ?>
                    </div>
                </td>            
                <td>
                    <strong class="title"><a href="<?php the_permalink(); ?>" title="Link to project: <?php the_title(); ?>"><?php the_title(); ?></a></strong>
                    <span class="<?php print $comment_class; ?>"><a href="<?php the_permalink(); ?>#comments_target" title="<?php comments_number(); ?>"><?php comments_number(' '); ?></a></span>

                    <div class="utility-container zm-base-hidden">
                        <?php edit_post_link('Edit', '' , ' |'); ?>
                        by <?php the_author(); ?> on <?php the_time(get_option('date_format')); ?> |
                        <?php if ( is_user_logged_in() ) : ?>
                        <a href="#delete" class="default_delete" data-post_id="<?php print $post->ID; ?>" data-security="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">Delete</a><?php endif; ?>
                    </div>
                </td>
                    <td>
                        <div class="status-container zm-base-item">
                            <?php print zm_base_get_the_term_list( array( 'post_id' => $post->ID, 'taxonomy' => 'status' )); ?>
                        </div>
                    </td>
                    <td>
                        <div class="priority-container zm-base-item">
                            <?php print zm_base_get_the_term_list( array( 'post_id' => $post->ID, 'taxonomy' => 'priority' )); ?>
                        </div>
                    </td>
            </tr>            
        <?php endwhile; ?>
    </table>
    <?php tt_json_feed( $cpt,  $cpt_obj[$cpt]->taxonomies ); ?>
</div>
