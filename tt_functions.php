<?php
/**
 *
 * This file should only contain procedural code that is NOT part of any Hook/Action.
 * Please place Hook/Action funcitons in the apropiate file.
 *
 */
 
/**
 * Prints the "age" of a Task from the current date to when it was posted
 */
if ( ! function_exists( 'tt_task_age' ) ) :
function tt_task_age() {
    printf( __( '<span class="meta">Age <span class="%1$s">%2$s</span></span>', 'Task' ),
        'meta-prep-author',
        sprintf( '<span class="date">%1$s</span>',
            esc_attr( human_time_diff( get_the_time('U'), current_time('timestamp') ) )
        )
    );
}
endif;

/**
 * Prints a json dataset of Tasks
 */
if ( ! function_exists( 'tt_json_feed' ) ) :
/** @todo tt_json_feed() should take the following params: taxonomies(array), count(int) */
function tt_json_feed() {
    global $wp_query, $post;
    $my_query = null;
    $tasks = array();
    
    $args = array(
       'post_type' => 'task',
       'post_status' => 'publish'
    );
        
    $my_query = new WP_Query( $args );
    
    $taxonomies = array( 'status', 
                         'priority', 
                         'project', 
                         'phase', 
                         'assigned' 
                         );
    
    while ( $my_query->have_posts() ) : $my_query->the_post();
    
        $tasks[$post->ID] = array(
            "id" => $post->ID,
            "title" => $post->post_title
            );
    
        foreach ( $taxonomies as $taxonomy ) {
            $term = wp_get_object_terms( $post->ID, $taxonomy );
            $term = ( $term ) ? $term[0]->slug : 'none' ;
            $tasks[$post->ID][$taxonomy] = $term;
        }
    
    endwhile;
    print '<script type="text/javascript">var _tasks = ' . json_encode( $tasks ) . '</script>';
}
endif;
