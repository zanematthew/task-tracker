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
<div class="zm-default-archive-container" >
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
                        <?php print zm_base_get_the_term_list( array( 'post_id' => $post->ID, 'link' => 'anchor', 'taxonomy' => 'type', 'post_type' => $cpt )); ?>
                    </div>
                </td>            
                <td>
                    <strong class="title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></strong>
                    <span class="<?php print $comment_class; ?>"><a href="<?php the_permalink(); ?>#comments_target" title="<?php comments_number(); ?>"><?php comments_number(' '); ?></a></span>

                    <div class="utility-container zm-base-hidden">
                        
                        <?php if ( is_user_logged_in() && current_user_can( 'administrator' ) ) : ?>
                        <span class="mini-button-container">
                            <span class="default"><?php edit_post_link('WordPress Admin Edit', '' ); ?></span>
                        </span>
                        <?php endif; ?>

                        <?php if ( is_user_logged_in() ) : ?>
                            <span class="mini-button-container">
                                <a href="#delete" class="default_delete high" data-post_id="<?php print $post->ID; ?>" data-security="<?php print wp_create_nonce( 'tt-ajax-forms' );?>">Delete</a>
                            </span>
                        <?php endif; ?>

                        <br />Added <?php tt_task_age(); ?> ago
                    </div>
                </td>
                    <td>
                        <div class="status-container zm-base-item">
                            <?php print zm_base_get_the_term_list( array( 'post_id' => $post->ID, 'taxonomy' => 'status', 'link' => 'anchor', 'post_type' => $cpt )); ?>
                        </div>
                    </td>
                    <td>
                        <div class="priority-container zm-base-item">
                            <?php print zm_base_get_the_term_list( array( 'post_id' => $post->ID, 'taxonomy' => 'priority', 'link' => 'anchor', 'post_type' => $cpt  )); ?>
                        </div>
                    </td>
            </tr>            
        <?php endwhile; ?>
    </table>
    <?php tt_json_feed( $cpt,  $cpt_obj[$cpt]->taxonomies ); ?>
</div>
