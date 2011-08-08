<?php

if ( ! function_exists( 'project_age' ) ) :
function project_age() {
    printf( __( '<span class="meta">Age <span class="%1$s">%2$s</span></span>', 'project' ),
        'meta-prep-author',
        sprintf( '<span class="date">%1$s</span>',
            esc_attr( human_time_diff( get_the_time('U'), current_time('timestamp') ) )
        )
    );
}
endif;


function base_insert_terms( $post_id ) {
    /** insert terms */
    /** @todo should only do the insert if they change? */
    $taxonomies = $_POST;
    foreach( $taxonomies as $taxonomy => $term ) {
        if ( isset( $term ) ) {
            wp_set_post_terms( $post_id, $term, &$taxonomy );
        }
    }
}

function project_create_ticket_div() {
    $html = '<div id="create_ticket_dialog" class="dialog-container">';
    $html .= '<div id="create_ticket_target" style="display: none;">hi</div>';
    $html .= '</div>';
    print $html;
}

/** 
 * load our template 
 * uh, why not make it ajaxy? :D
 */
function tt_load_template( ) {
    $template = $_POST['template'];

    if ( $template == null )
        die( 'please enter a template' );
        
    load_template( MY_PLUGIN_DIR . $template );
    die();
}

/**
 * Prints an json dataset of Tasks
 */
function tt_json_feed() {
    global $wp_query, $post;
    $args = array(
       'post_type' => 'task',
       'post_status' => 'publish'
    );
    
    $my_query = null;
    $my_query = new WP_Query( $args );
    $tasks = array();
    $taxonomies = array( 'status', 'priority', 'project', 'phase', 'assigned' );
    
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
