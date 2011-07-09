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

function project_submit_task() {
    unset( $_POST['action'] );
    /** @todo error checking to come */
    $type = $_POST['post_type'];
    $title = $_POST['post_title'];
    $content = $_POST['content'];
    /** 
     * We unset what we've used because everything left over is going to be used for our taxonomies
     * We do this as opposed to hard coding taxonomies, because we might not know what they are called
     */
    unset( $_POST['post_title'] );
    unset( $_POST['content'] );
    unset( $_POST['post_author'] );
    unset( $_POST['post_type'] );

    $author_ID = get_current_user_id();

    if ( current_user_can( 'administrator' ) || current_usr_can( 'editor' ) ) {
        $status = 'publish';
    } else {
        $status = 'pending';
    }

    $post = array(
        'post_title' => $title,
        'post_content' => $content,
        'post_author' => $author_ID,
        'post_type' => $type,
        'post_status' => $status
    );

    /** insert our post */
    $post_id = wp_insert_post( $post, true );

    if ( $post_id )
        base_insert_terms( $post_id );

    die();
}

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
function base(){
    load_template( MY_PLUGIN_DIR . '/theme/task-create.php' );
    die();
}
